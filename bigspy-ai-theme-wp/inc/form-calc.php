<?php
/**
 * Built-in form price calculator (G-58).
 *
 * - Field type `calc`: a read-only price line with a limited formula.
 *   Formulas reference fields by stable ID: {f_kg} * 18000.
 * - Choice fields can carry prices (`optionPrices`: option => number).
 * - The browser shows a live preview; on submit the server recomputes every
 *   line from the saved block. Totals sent by the browser are never read.
 *
 * Formula grammar (no eval, no PHP/JS, no network):
 *   numbers, {field_id}, + - * / ( ), comparisons > < >= <= == !=, && ||,
 *   min(a,b,...) max(...) round(x[,d]) ceil(x) floor(x) abs(x) if(cond,a,b).
 * The JS twin is in assets/js/blocks.js; both pass tools/tests/form-calc-fixtures.json.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Largest absolute value a line may reach. */
function bst_calc_limit() {
	return 1e12;
}

/** Functions allowed in formulas => array( min args, max args ). */
function bst_calc_functions() {
	return array(
		'min'   => array( 1, 20 ),
		'max'   => array( 1, 20 ),
		'round' => array( 1, 2 ),
		'ceil'  => array( 1, 1 ),
		'floor' => array( 1, 1 ),
		'abs'   => array( 1, 1 ),
		'if'    => array( 3, 3 ),
	);
}

/** Split a formula into tokens. Returns array of array( type, value ), or a string error. */
function bst_calc_tokens( $src ) {
	$src = (string) $src;
	if ( strlen( $src ) > 500 ) {
		return 'too_long';
	}
	$out = array();
	$i   = 0;
	$len = strlen( $src );
	while ( $i < $len ) {
		$rest = substr( $src, $i );
		if ( preg_match( '/^\s+/', $rest, $m ) ) {
			$i += strlen( $m[0] );
			continue;
		}
		if ( preg_match( '/^\d+(?:\.\d+)?/', $rest, $m ) ) {
			$out[] = array( 'n', (float) $m[0] );
		} elseif ( preg_match( '/^\{([a-z][a-z0-9_]{1,31})\}/', $rest, $m ) ) {
			$out[] = array( 'r', $m[1] );
		} elseif ( preg_match( '/^[a-z]+/', $rest, $m ) ) {
			$out[] = array( 'f', $m[0] );
		} elseif ( preg_match( '/^(>=|<=|==|!=|&&|\|\||[-+*\/(),<>])/', $rest, $m ) ) {
			$out[] = array( 'o', $m[0] );
		} else {
			return 'bad_char';
		}
		$i += strlen( $m[0] );
	}
	return $out;
}

/**
 * Parse a formula. Returns array( 'ast' => node ) or array( 'error' => code ).
 * Nodes: array('n', v) array('r', id) array('neg', x) array('b', op, a, b) array('f', name, args).
 */
function bst_calc_parse( $src ) {
	$t = bst_calc_tokens( $src );
	if ( is_string( $t ) ) {
		return array( 'error' => $t );
	}
	if ( ! $t ) {
		return array( 'error' => 'empty' );
	}
	$p = array( 't' => $t, 'i' => 0, 'depth' => 0 );
	try {
		$ast = bst_calc_parse_level( $p, 0 );
		if ( $p['i'] < count( $t ) ) {
			return array( 'error' => 'syntax' );
		}
		return array( 'ast' => $ast );
	} catch ( Exception $e ) {
		return array( 'error' => $e->getMessage() );
	}
}

/** Binary operator levels, loosest first. */
function bst_calc_levels() {
	return array( array( '||' ), array( '&&' ), array( '==', '!=', '>', '<', '>=', '<=' ), array( '+', '-' ), array( '*', '/' ) );
}

function bst_calc_parse_level( &$p, $level ) {
	$levels = bst_calc_levels();
	if ( $level >= count( $levels ) ) {
		return bst_calc_parse_unary( $p );
	}
	$left = bst_calc_parse_level( $p, $level + 1 );
	while ( isset( $p['t'][ $p['i'] ] ) && 'o' === $p['t'][ $p['i'] ][0] && in_array( $p['t'][ $p['i'] ][1], $levels[ $level ], true ) ) {
		$op = $p['t'][ $p['i'] ][1];
		++$p['i'];
		$left = array( 'b', $op, $left, bst_calc_parse_level( $p, $level + 1 ) );
	}
	return $left;
}

function bst_calc_parse_unary( &$p ) {
	if ( ++$p['depth'] > 40 ) {
		throw new Exception( 'too_deep' );
	}
	$tok = $p['t'][ $p['i'] ] ?? null;
	if ( ! $tok ) {
		throw new Exception( 'syntax' );
	}
	++$p['i'];
	if ( 'o' === $tok[0] && '-' === $tok[1] ) {
		$node = array( 'neg', bst_calc_parse_unary( $p ) );
	} elseif ( 'o' === $tok[0] && '+' === $tok[1] ) {
		$node = bst_calc_parse_unary( $p );
	} elseif ( 'n' === $tok[0] ) {
		$node = array( 'n', $tok[1] );
	} elseif ( 'r' === $tok[0] ) {
		$node = array( 'r', $tok[1] );
	} elseif ( 'o' === $tok[0] && '(' === $tok[1] ) {
		$node = bst_calc_parse_level( $p, 0 );
		bst_calc_expect( $p, ')' );
	} elseif ( 'f' === $tok[0] ) {
		$fns = bst_calc_functions();
		if ( ! isset( $fns[ $tok[1] ] ) ) {
			throw new Exception( 'unknown_function' );
		}
		bst_calc_expect( $p, '(' );
		$args = array( bst_calc_parse_level( $p, 0 ) );
		while ( isset( $p['t'][ $p['i'] ] ) && array( 'o', ',' ) === $p['t'][ $p['i'] ] ) {
			++$p['i'];
			$args[] = bst_calc_parse_level( $p, 0 );
		}
		bst_calc_expect( $p, ')' );
		if ( count( $args ) < $fns[ $tok[1] ][0] || count( $args ) > $fns[ $tok[1] ][1] ) {
			throw new Exception( 'arg_count' );
		}
		$node = array( 'f', $tok[1], $args );
	} else {
		throw new Exception( 'syntax' );
	}
	--$p['depth'];
	return $node;
}

function bst_calc_expect( &$p, $op ) {
	if ( array( 'o', $op ) !== ( $p['t'][ $p['i'] ] ?? null ) ) {
		throw new Exception( 'syntax' );
	}
	++$p['i'];
}

/** Field IDs a formula references. */
function bst_calc_refs( $src ) {
	preg_match_all( '/\{([a-z][a-z0-9_]{1,31})\}/', (string) $src, $m );
	return array_values( array_unique( $m[1] ) );
}

/**
 * Evaluate an AST. $ref( id ) returns a float, or null when that input is
 * still missing. Throws Exception('incomplete'|'div_zero'|'overflow').
 */
function bst_calc_eval( $n, $ref ) {
	switch ( $n[0] ) {
		case 'n':
			return $n[1];
		case 'r':
			$v = $ref( $n[1] );
			if ( null === $v ) {
				throw new Exception( 'incomplete' );
			}
			return $v;
		case 'neg':
			return -bst_calc_eval( $n[1], $ref );
		case 'f':
			if ( 'if' === $n[1] ) {
				return bst_calc_eval( $n[2][0], $ref ) ? bst_calc_eval( $n[2][1], $ref ) : bst_calc_eval( $n[2][2], $ref );
			}
			$a = array();
			foreach ( $n[2] as $x ) {
				$a[] = bst_calc_eval( $x, $ref );
			}
			switch ( $n[1] ) {
				case 'min':
					return min( $a );
				case 'max':
					return max( $a );
				case 'ceil':
					return ceil( $a[0] - 1e-9 );
				case 'floor':
					return floor( $a[0] + 1e-9 );
				case 'abs':
					return abs( $a[0] );
				default:
					return bst_calc_round( $a[0], isset( $a[1] ) ? (int) $a[1] : 0 );
			}
	}
	$a = bst_calc_eval( $n[2], $ref );
	if ( '&&' === $n[1] && ! $a ) {
		return 0.0;
	}
	if ( '||' === $n[1] && $a ) {
		return 1.0;
	}
	$b = bst_calc_eval( $n[3], $ref );
	switch ( $n[1] ) {
		case '+':
			$r = $a + $b;
			break;
		case '-':
			$r = $a - $b;
			break;
		case '*':
			$r = $a * $b;
			break;
		case '/':
			if ( 0.0 === (float) $b ) {
				throw new Exception( 'div_zero' );
			}
			$r = $a / $b;
			break;
		case '&&':
		case '||':
			return $b ? 1.0 : 0.0;
		default:
			$cmp = array(
				'==' => abs( $a - $b ) < 1e-9,
				'!=' => abs( $a - $b ) >= 1e-9,
				'>'  => $a > $b + 1e-9,
				'<'  => $a < $b - 1e-9,
				'>=' => $a >= $b - 1e-9,
				'<=' => $a <= $b + 1e-9,
			);
			return $cmp[ $n[1] ] ? 1.0 : 0.0;
	}
	if ( ! is_finite( $r ) || abs( $r ) > bst_calc_limit() ) {
		throw new Exception( 'overflow' );
	}
	return $r;
}

/** Round half away from zero to $d decimals (0–4); the same rule as the JS twin. */
function bst_calc_round( $x, $d = 0 ) {
	$d = max( 0, min( 4, (int) $d ) );
	$f = pow( 10, $d );
	$v = abs( $x ) * $f;
	$r = floor( $v + 0.5 + 1e-7 );
	return ( $x < 0 ? -$r : $r ) / $f;
}

/** Number typed by a visitor: "1,5" and "1.5" both mean 1.5. Returns float or null. */
function bst_calc_number( $v ) {
	$v = str_replace( ' ', '', trim( (string) $v ) );
	if ( preg_match( '/^-?\d+,\d+$/', $v ) ) {
		$v = str_replace( ',', '.', $v );
	}
	return '' !== $v && is_numeric( $v ) ? (float) $v : null;
}

/** Prices of a choice field: option => float. */
function bst_calc_option_prices( $f ) {
	$out = array();
	foreach ( (array) ( $f['optionPrices'] ?? array() ) as $o => $p ) {
		$n = bst_calc_number( $p );
		if ( null !== $n ) {
			$out[ (string) $o ] = $n;
		}
	}
	return $out;
}

/** Currency settings of a form. */
function bst_calc_currency( $s ) {
	$code = strtoupper( preg_replace( '/[^A-Za-z]/', '', (string) ( $s['priceCurrency'] ?? 'VND' ) ) );
	$code = $code ? substr( $code, 0, 3 ) : 'VND';
	$dec  = isset( $s['priceDecimals'] ) && '' !== $s['priceDecimals'] ? (int) $s['priceDecimals'] : ( in_array( $code, array( 'VND', 'JPY', 'KRW' ), true ) ? 0 : 2 );
	return array( 'code' => $code, 'decimals' => max( 0, min( 4, $dec ) ) );
}

/** Money as text: 1.234.567 ₫ / 1,234.50 USD. */
function bst_calc_money( $v, $cur ) {
	if ( 'VND' === $cur['code'] ) {
		return number_format( $v, $cur['decimals'], ',', '.' ) . ' ₫';
	}
	return number_format( $v, $cur['decimals'], '.', ',' ) . ' ' . $cur['code'];
}

/**
 * Compute every calc line of a form for these values (keyed by field name)
 * and the visibility map from bst_form_schema_visibility().
 * Returns array( 'lines' => array( id => array( state, value ) ), 'total' => id|'' ).
 * state: ok | incomplete | error.
 */
function bst_calc_quote( $fields, $values, $seen, $cur ) {
	$by_id = array();
	foreach ( $fields as $f ) {
		$by_id[ $f['id'] ] = $f;
	}
	$lines = array();
	$total = '';
	$order = bst_form_schema_order( $fields );
	if ( null === $order ) {
		$order = array();
	}
	$ref = function ( $id ) use ( &$lines, $by_id, $values, $seen ) {
		if ( ! isset( $by_id[ $id ] ) ) {
			throw new Exception( 'missing_ref' );
		}
		$f = $by_id[ $id ];
		if ( empty( $seen[ $id ] ) ) {
			return 0.0; // Hidden fields charge nothing.
		}
		$type = $f['type'] ?? 'text';
		$v    = (string) ( $values[ sanitize_key( $f['name'] ) ] ?? '' );
		if ( 'calc' === $type ) {
			$line = $lines[ $id ] ?? array( 'error', 0 );
			if ( 'ok' !== $line[0] ) {
				throw new Exception( $line[0] );
			}
			return $line[1];
		}
		if ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ) {
			if ( '' === $v ) {
				if ( 'checkbox' === $type ) {
					return 0.0;
				}
				return null;
			}
			$prices = bst_calc_option_prices( $f );
			$sum    = 0.0;
			foreach ( explode( ', ', $v ) as $o ) {
				if ( ! isset( $prices[ $o ] ) ) {
					throw new Exception( 'unpriced' ); // An option without a price needs confirmation.
				}
				$sum += $prices[ $o ];
			}
			return $sum;
		}
		if ( 'acceptance' === $type ) {
			return '' === $v ? 0.0 : 1.0;
		}
		if ( 'number' === $type ) {
			return '' === $v ? null : bst_calc_number( $v );
		}
		return 0.0;
	};
	foreach ( $order as $id ) {
		$f = $by_id[ $id ];
		if ( 'calc' !== ( $f['type'] ?? '' ) ) {
			continue;
		}
		if ( ! empty( $f['isTotal'] ) && '' === $total ) {
			$total = $id;
		}
		if ( empty( $seen[ $id ] ) ) {
			$lines[ $id ] = array( 'ok', 0.0 );
			continue;
		}
		$ast = bst_calc_parse( $f['formula'] ?? '' );
		if ( isset( $ast['error'] ) ) {
			$lines[ $id ] = array( 'error', 0 );
			continue;
		}
		try {
			$v            = bst_calc_round( bst_calc_eval( $ast['ast'], $ref ), $cur['decimals'] );
			$lines[ $id ] = ! empty( $f['isTotal'] ) && $v < 0 ? array( 'error', 0 ) : array( 'ok', $v );
		} catch ( Exception $e ) {
			$lines[ $id ] = array( 'incomplete' === $e->getMessage() ? 'incomplete' : 'error', 0 );
		}
	}
	return array( 'lines' => $lines, 'total' => $total );
}

/** Check every formula of a form. Returns array( id => error code ). */
function bst_calc_check( $fields ) {
	$ids = wp_list_pluck( $fields, 'id' );
	$out = array();
	foreach ( $fields as $f ) {
		if ( 'calc' !== ( $f['type'] ?? '' ) ) {
			continue;
		}
		$p = bst_calc_parse( $f['formula'] ?? '' );
		if ( isset( $p['error'] ) ) {
			$out[ $f['id'] ] = $p['error'];
		} elseif ( array_diff( bst_calc_refs( $f['formula'] ), $ids ) ) {
			$out[ $f['id'] ] = 'missing_ref';
		}
	}
	return $out;
}
