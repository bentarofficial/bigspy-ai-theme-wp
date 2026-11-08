<?php
/**
 * Price calculation for the built-in Form (G-57 field IDs, G-58 calculator).
 *
 * Fields keep a stable "id"; formulas reference {id}, so renaming a label, a mail tag
 * or moving a field never changes a price. Choice options may carry a price
 * ("Label | 18000"). A "calc" field shows a read-only total from a limited formula:
 * numbers, {field}, + - * /, parentheses, comparisons, min, max, round, ceil, floor
 * and if(condition, then, else). No eval, no code, no outside data.
 *
 * The browser shows a preview with assets/js/form-calc.js (same grammar). On submit
 * the server computes again from the saved block; totals sent by the browser are ignored.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Stable ID of a field: its saved id, else one derived from the mail tag (old forms, never stored). */
function bst_native_field_id( $f ) {
	$id = (string) ( $f['id'] ?? '' );
	return preg_match( '/^[a-z][a-z0-9_]{1,31}$/', $id ) ? $id : 'n_' . str_replace( '-', '_', sanitize_key( (string) ( $f['name'] ?? '' ) ) );
}

/** Option labels and prices of a choice field: array( label => price|null ). */
function bst_native_option_prices( $f ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) ( $f['options'] ?? '' ) ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $parts[0] ) {
			continue;
		}
		$price = isset( $parts[1] ) ? bst_calc_number( $parts[1] ) : null;
		$out[ $parts[0] ] = $price;
	}
	return $out;
}

/** A number typed by a person: "1,5" and "1.5" are the same; "18.000" with three digits is eighteen thousand. */
function bst_calc_number( $v ) {
	$v = str_replace( array( ' ', "\u{00a0}" ), '', trim( (string) $v ) );
	if ( '' === $v ) {
		return null;
	}
	if ( preg_match( '/^-?\d{1,3}([.,]\d{3})+$/', $v ) && ! preg_match( '/^-?0[.,]/', $v ) ) {
		$v = str_replace( array( '.', ',' ), '', $v );
	} else {
		$v = str_replace( ',', '.', $v );
	}
	return is_numeric( $v ) ? (float) $v : null;
}

/** Decimals used for money in a currency (VND and JPY have none). */
function bst_calc_decimals( $currency ) {
	return in_array( strtoupper( (string) $currency ), array( 'VND', 'JPY', 'KRW' ), true ) ? 0 : 2;
}

/** Money text: 89.800 ₫ / 12.50 USD. */
function bst_calc_format( $amount, $currency ) {
	$currency = strtoupper( (string) $currency );
	$dec      = bst_calc_decimals( $currency );
	if ( 'VND' === $currency ) {
		return number_format( $amount, 0, ',', '.' ) . ' ₫';
	}
	return number_format( $amount, $dec, '.', ',' ) . ( $currency ? ' ' . $currency : '' );
}

/** Round half away from zero to $dec places, the same way in PHP and JS (integers in minor units). */
function bst_calc_round( $v, $dec ) {
	$m = pow( 10, $dec );
	$s = $v < 0 ? -1 : 1;
	return $s * floor( abs( $v ) * $m + 0.5 + 1e-9 ) / $m;
}

/**
 * Tokens of a formula, or WP_Error.
 *
 * @return array|WP_Error List of array( type, value ).
 */
function bst_calc_tokens( $src ) {
	$src = (string) $src;
	if ( strlen( $src ) > 2000 ) {
		return new WP_Error( 'calc', 'Formula too long.' );
	}
	$out = array();
	$i   = 0;
	$len = strlen( $src );
	while ( $i < $len ) {
		$c = $src[ $i ];
		if ( ctype_space( $c ) ) {
			$i++;
			continue;
		}
		if ( preg_match( '/\G\d+(\.\d+)?/', $src, $m, 0, $i ) ) {
			$out[] = array( 'num', (float) $m[0] );
			$i    += strlen( $m[0] );
		} elseif ( preg_match( '/\G\{([a-z][a-z0-9_]{1,31})\}/', $src, $m, 0, $i ) ) {
			$out[] = array( 'ref', $m[1] );
			$i    += strlen( $m[0] );
		} elseif ( preg_match( '/\G(min|max|round|ceil|floor|if)\b/', $src, $m, 0, $i ) ) {
			$out[] = array( 'fn', $m[1] );
			$i    += strlen( $m[0] );
		} elseif ( preg_match( '/\G(<=|>=|==|!=|<|>|[-+*\/(),])/', $src, $m, 0, $i ) ) {
			$out[] = array( 'op', $m[1] );
			$i    += strlen( $m[0] );
		} else {
			return new WP_Error( 'calc', sprintf( 'Unexpected "%s" at position %d.', substr( $src, $i, 10 ), $i + 1 ) );
		}
	}
	if ( count( $out ) > 400 ) {
		return new WP_Error( 'calc', 'Formula too long.' );
	}
	return $out;
}

/** Parse tokens into a tree (arrays), or WP_Error. */
function bst_calc_parse( $src ) {
	$tokens = bst_calc_tokens( $src );
	if ( is_wp_error( $tokens ) ) {
		return $tokens;
	}
	if ( ! $tokens ) {
		return new WP_Error( 'calc', 'Formula is empty.' );
	}
	$pos   = 0;
	$depth = 0;
	$peek  = function () use ( &$tokens, &$pos ) {
		return $tokens[ $pos ] ?? array( 'end', '' );
	};
	$is    = function ( $value ) use ( $peek ) {
		$t = $peek();
		return 'op' === $t[0] && $t[1] === $value;
	};
	$expect = function ( $value ) use ( $is, &$pos ) {
		if ( ! $is( $value ) ) {
			throw new Exception( sprintf( 'Expected "%s".', $value ) );
		}
		$pos++;
	};
	$cmp     = null;
	$sum     = null;
	$product = null;
	$unary   = null;
	$primary = function () use ( $peek, $is, $expect, &$pos, &$cmp, &$depth ) {
		$t = $peek();
		if ( 'num' === $t[0] || 'ref' === $t[0] ) {
			$pos++;
			return $t;
		}
		if ( 'fn' === $t[0] ) {
			$pos++;
			$expect( '(' );
			$args = array( $cmp() );
			while ( $is( ',' ) ) {
				$pos++;
				$args[] = $cmp();
			}
			$expect( ')' );
			$want = array( 'min' => array( 1, 20 ), 'max' => array( 1, 20 ), 'round' => array( 1, 2 ), 'ceil' => array( 1, 1 ), 'floor' => array( 1, 1 ), 'if' => array( 3, 3 ) )[ $t[1] ];
			if ( count( $args ) < $want[0] || count( $args ) > $want[1] ) {
				throw new Exception( sprintf( '%s() takes %d to %d values.', $t[1], $want[0], $want[1] ) );
			}
			return array( 'fn', $t[1], $args );
		}
		if ( $is( '(' ) ) {
			if ( ++$depth > 30 ) {
				throw new Exception( 'Too many parentheses.' );
			}
			$pos++;
			$e = $cmp();
			$expect( ')' );
			$depth--;
			return $e;
		}
		throw new Exception( 'end' === $t[0] ? 'Formula ends too early.' : sprintf( 'Unexpected "%s".', $t[1] ) );
	};
	$unary = function () use ( $is, &$pos, &$unary, $primary ) {
		if ( $is( '-' ) ) {
			$pos++;
			return array( 'neg', $unary() );
		}
		return $primary();
	};
	$product = function () use ( $peek, &$pos, $unary ) {
		$left = $unary();
		while ( in_array( $peek(), array( array( 'op', '*' ), array( 'op', '/' ) ), true ) ) {
			$op   = $peek()[1];
			$pos++;
			$left = array( 'bin', $op, $left, $unary() );
		}
		return $left;
	};
	$sum = function () use ( $peek, &$pos, $product ) {
		$left = $product();
		while ( in_array( $peek(), array( array( 'op', '+' ), array( 'op', '-' ) ), true ) ) {
			$op   = $peek()[1];
			$pos++;
			$left = array( 'bin', $op, $left, $product() );
		}
		return $left;
	};
	$cmp = function () use ( $peek, &$pos, $sum ) {
		$left = $sum();
		$t    = $peek();
		if ( 'op' === $t[0] && in_array( $t[1], array( '<', '<=', '>', '>=', '==', '!=' ), true ) ) {
			$pos++;
			$left = array( 'bin', $t[1], $left, $sum() );
		}
		return $left;
	};
	try {
		$tree = $cmp();
		if ( $pos < count( $tokens ) ) {
			throw new Exception( sprintf( 'Unexpected "%s".', $tokens[ $pos ][1] ) );
		}
	} catch ( Exception $e ) {
		return new WP_Error( 'calc', $e->getMessage() );
	}
	return $tree;
}

/** Field IDs a parsed formula reads. */
function bst_calc_refs( $tree ) {
	if ( 'ref' === $tree[0] ) {
		return array( $tree[1] );
	}
	$kids = 'fn' === $tree[0] ? $tree[2] : ( 'neg' === $tree[0] ? array( $tree[1] ) : ( 'bin' === $tree[0] ? array( $tree[2], $tree[3] ) : array() ) );
	$out  = array();
	foreach ( $kids as $kid ) {
		$out = array_merge( $out, bst_calc_refs( $kid ) );
	}
	return array_values( array_unique( $out ) );
}

/** Value of a parsed formula. Throws on division by zero or numbers out of range. */
function bst_calc_eval( $tree, $vars ) {
	switch ( $tree[0] ) {
		case 'num':
			return $tree[1];
		case 'ref':
			return $vars[ $tree[1] ];
		case 'neg':
			return -bst_calc_eval( $tree[1], $vars );
		case 'fn':
			if ( 'if' === $tree[1] ) {
				return bst_calc_eval( $tree[2][0], $vars ) ? bst_calc_eval( $tree[2][1], $vars ) : bst_calc_eval( $tree[2][2], $vars );
			}
			$a = array();
			foreach ( $tree[2] as $arg ) {
				$a[] = bst_calc_eval( $arg, $vars );
			}
			switch ( $tree[1] ) {
				case 'min':
					return min( $a );
				case 'max':
					return max( $a );
				case 'ceil':
					return ceil( $a[0] - 1e-9 );
				case 'floor':
					return floor( $a[0] + 1e-9 );
				default:
					return bst_calc_round( $a[0], max( 0, min( 6, (int) ( $a[1] ?? 0 ) ) ) );
			}
		default:
			$l = bst_calc_eval( $tree[2], $vars );
			$r = bst_calc_eval( $tree[3], $vars );
			switch ( $tree[1] ) {
				case '+':
					$v = $l + $r;
					break;
				case '-':
					$v = $l - $r;
					break;
				case '*':
					$v = $l * $r;
					break;
				case '/':
					if ( abs( $r ) < 1e-12 ) {
						throw new Exception( 'division' );
					}
					$v = $l / $r;
					break;
				case '<':
					return $l < $r - 1e-9 ? 1 : 0;
				case '<=':
					return $l <= $r + 1e-9 ? 1 : 0;
				case '>':
					return $l > $r + 1e-9 ? 1 : 0;
				case '>=':
					return $l >= $r - 1e-9 ? 1 : 0;
				case '==':
					return abs( $l - $r ) < 1e-9 ? 1 : 0;
				default:
					return abs( $l - $r ) >= 1e-9 ? 1 : 0;
			}
			if ( ! is_finite( $v ) || abs( $v ) > 1e12 ) {
				throw new Exception( 'range' );
			}
			return $v;
	}
}

/**
 * Check every calc formula of a form: syntax, unknown or unusable fields, loops.
 *
 * @return array{order: string[], trees: array, errors: array} errors keyed by field ID.
 */
function bst_calc_plan( $fields ) {
	$by     = array();
	$errors = array();
	$trees  = array();
	foreach ( $fields as $f ) {
		$by[ bst_native_field_id( $f ) ] = $f;
	}
	$usable = array( 'number', 'select', 'radio', 'checkbox', 'acceptance', 'calc' );
	foreach ( $by as $id => $f ) {
		if ( 'calc' !== ( $f['type'] ?? '' ) ) {
			continue;
		}
		$tree = bst_calc_parse( $f['formula'] ?? '' );
		if ( is_wp_error( $tree ) ) {
			$errors[ $id ] = $tree->get_error_message();
			continue;
		}
		foreach ( bst_calc_refs( $tree ) as $ref ) {
			if ( ! isset( $by[ $ref ] ) ) {
				$errors[ $id ] = sprintf( 'Field {%s} does not exist (deleted?). Pick it again.', $ref );
			} elseif ( ! in_array( $by[ $ref ]['type'] ?? 'text', $usable, true ) ) {
				$errors[ $id ] = sprintf( 'Field "%s" is not a number or choice field.', $by[ $ref ]['label'] ?? $ref );
			}
		}
		if ( ! isset( $errors[ $id ] ) ) {
			$trees[ $id ] = $tree;
		}
	}
	// Order calc fields so each runs after the calc fields it reads; loops are errors.
	$order = array();
	$state = array();
	$visit = function ( $id ) use ( &$visit, &$order, &$state, &$trees, &$errors, $by ) {
		if ( 'done' === ( $state[ $id ] ?? '' ) ) {
			return true;
		}
		if ( 'busy' === ( $state[ $id ] ?? '' ) ) {
			return false;
		}
		$state[ $id ] = 'busy';
		foreach ( bst_calc_refs( $trees[ $id ] ) as $ref ) {
			if ( 'calc' === ( $by[ $ref ]['type'] ?? '' ) && ( ! isset( $trees[ $ref ] ) || ! $visit( $ref ) ) ) {
				$errors[ $id ] = isset( $trees[ $ref ] ) || ! isset( $errors[ $ref ] ) ? 'This formula uses itself through other totals (a loop).' : sprintf( 'It uses "%s", which has an error.', $by[ $ref ]['label'] ?? $ref );
				$state[ $id ] = 'done';
				return false;
			}
		}
		$state[ $id ] = 'done';
		$order[]      = $id;
		return true;
	};
	foreach ( array_keys( $trees ) as $id ) {
		$visit( $id );
	}
	$order = array_values( array_filter( $order, function ( $id ) use ( $errors ) { return ! isset( $errors[ $id ] ); } ) );
	return array( 'order' => $order, 'trees' => $trees, 'errors' => $errors );
}

/**
 * Totals of every calc field for these submitted values (keyed by mail tag).
 *
 * @return array id => array( status: ok|incomplete|confirm, amount: float|null, text: string )
 */
function bst_calc_run( $fields, $values ) {
	$plan = bst_calc_plan( $fields );
	$vars = array();
	$miss = array();
	$by   = array();
	foreach ( $fields as $f ) {
		$id        = bst_native_field_id( $f );
		$by[ $id ] = $f;
		$v         = (string) ( $values[ sanitize_key( $f['name'] ) ] ?? '' );
		$type      = $f['type'] ?? 'text';
		if ( 'number' === $type ) {
			$n           = bst_calc_number( $v );
			$vars[ $id ] = null === $n ? 0.0 : $n;
			$miss[ $id ] = null === $n && ! empty( $f['required'] );
		} elseif ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ) {
			$prices = bst_native_option_prices( $f );
			$picked = '' === $v ? array() : explode( ', ', $v );
			$sum    = 0.0;
			foreach ( $picked as $p ) {
				$sum += (float) ( $prices[ $p ] ?? 0 );
			}
			$vars[ $id ] = $sum;
			$miss[ $id ] = ! $picked && ! empty( $f['required'] );
		} elseif ( 'acceptance' === $type ) {
			$vars[ $id ] = '' === $v ? 0.0 : 1.0;
			$miss[ $id ] = false;
		}
	}
	$out = array();
	foreach ( $plan['errors'] as $id => $msg ) {
		$out[ $id ] = array( 'status' => 'confirm', 'amount' => null, 'error' => $msg );
	}
	foreach ( $plan['order'] as $id ) {
		$f       = $by[ $id ];
		$missing = false;
		foreach ( bst_calc_refs( $plan['trees'][ $id ] ) as $ref ) {
			$missing = $missing || ! empty( $miss[ $ref ] );
		}
		$dec = bst_calc_decimals( $f['currency'] ?? 'VND' );
		if ( $missing ) {
			$out[ $id ]  = array( 'status' => 'incomplete', 'amount' => null );
			$miss[ $id ] = true;
			$vars[ $id ] = 0.0;
			continue;
		}
		try {
			$v = bst_calc_round( bst_calc_eval( $plan['trees'][ $id ], $vars ), $dec );
		} catch ( Exception $e ) {
			$v = null;
		}
		if ( null === $v || $v < 0 ) {
			$out[ $id ]  = array( 'status' => 'confirm', 'amount' => null );
			$miss[ $id ] = true;
			$vars[ $id ] = 0.0;
			continue;
		}
		$out[ $id ]  = array( 'status' => 'ok', 'amount' => $v );
		$vars[ $id ] = $v;
	}
	foreach ( $out as $id => &$r ) {
		$cur       = $by[ $id ]['currency'] ?? 'VND';
		$r['text'] = 'ok' === $r['status'] ? bst_calc_format( $r['amount'], $cur ) : ( 'incomplete' === $r['status'] ? bst_calc_msg( 'incomplete' ) : bst_calc_msg( 'confirm' ) );
	}
	return $out;
}

/** Visitor messages for a total that cannot be shown. */
function bst_calc_msg( $status ) {
	return 'incomplete' === $status ? __( 'Choose more details to see the price', 'bigspy-ai-theme-wp' ) : __( 'Price to be confirmed', 'bigspy-ai-theme-wp' );
}

/** Settings the browser preview needs (no mail settings). */
function bst_calc_client_config( $fields ) {
	$plan = bst_calc_plan( $fields );
	$cfg  = array( 'fields' => array(), 'calcs' => array(), 'msg' => array( 'incomplete' => bst_calc_msg( 'incomplete' ), 'confirm' => bst_calc_msg( 'confirm' ) ) );
	foreach ( $fields as $f ) {
		$id   = bst_native_field_id( $f );
		$type = $f['type'] ?? 'text';
		if ( 'calc' === $type ) {
			$cfg['calcs'][] = array( 'id' => $id, 'name' => sanitize_key( $f['name'] ), 'formula' => isset( $plan['errors'][ $id ] ) ? '' : (string) $f['formula'], 'currency' => strtoupper( (string) ( $f['currency'] ?? 'VND' ) ) );
			continue;
		}
		if ( in_array( $type, array( 'number', 'select', 'radio', 'checkbox', 'acceptance' ), true ) ) {
			$cfg['fields'][ $id ] = array( 'name' => sanitize_key( $f['name'] ), 'type' => $type, 'required' => ! empty( $f['required'] ), 'prices' => in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ? (object) bst_native_option_prices( $f ) : null );
		}
	}
	return $cfg;
}
