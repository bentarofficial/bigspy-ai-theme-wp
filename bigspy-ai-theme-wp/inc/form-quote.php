<?php
/**
 * Form price quote (G-58): a limited formula language, line-by-line quote and the
 * snapshot stored with the entry. assets/js/blocks.js (bsNfQuote) mirrors these rules
 * for the live preview; the server always computes again from the saved block.
 *
 * Formula: numbers, + - * / ( ), comparisons (> < >= <= == !=), min(), max(),
 * round(x[, digits]), ceil(), floor(), if(cond, a, b) and references:
 *   {f_kg}        a field: number value, or the summed prices of the picked options
 *   {f_speed=Fast} 1 when that option is picked, else 0
 *   {l_wash}      the amount of another line
 * No eval, no PHP/JS, no network: anything else is a syntax error.
 *
 * Rounding policy 1: each line is rounded half away from zero to the currency's
 * decimals; the total is the sum of rounded lines.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

const BST_QUOTE_POLICY = 1;
const BST_QUOTE_LIMIT  = 1e12;

/** Pricing settings of a form, normalized. */
function bst_quote_settings( $p ) {
	$p     = is_array( $p ) ? $p : array();
	$lines = array();
	$seen  = array();
	foreach ( (array) ( $p['lines'] ?? array() ) as $l ) {
		if ( ! is_array( $l ) ) {
			continue;
		}
		$id = strtolower( (string) ( $l['id'] ?? '' ) );
		if ( ! preg_match( '/^l_[a-z0-9_-]{1,40}$/', $id ) || isset( $seen[ $id ] ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$lines[]     = array(
			'id'    => $id,
			'label' => (string) ( $l['label'] ?? '' ),
			'expr'  => substr( (string) ( $l['expr'] ?? '' ), 0, 500 ),
		);
	}
	return array(
		'enabled'  => ! empty( $p['enabled'] ) && $lines,
		'symbol'   => substr( (string) ( $p['symbol'] ?? '₫' ), 0, 8 ),
		'decimals' => max( 0, min( 3, (int) ( $p['decimals'] ?? 0 ) ) ),
		'note'     => (string) ( $p['note'] ?? '' ),
		'lines'    => $lines,
	);
}

/** Option => price of a select / radio / checkbox field ("Option = 18000" per line). */
function bst_quote_option_prices( $f ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) ( $f['prices'] ?? '' ) ) as $row ) {
		$pos = strrpos( $row, '=' );
		if ( false === $pos ) {
			continue;
		}
		$opt = trim( substr( $row, 0, $pos ) );
		$num = bst_quote_number( substr( $row, $pos + 1 ) );
		if ( '' !== $opt && null !== $num ) {
			$out[ $opt ] = $num;
		}
	}
	return $out;
}

/** "1,5" / "1.5" / "1.234,5" / "1,234.5" -> float, or null. */
function bst_quote_number( $v ) {
	$v = str_replace( array( ' ', "\u{00a0}" ), '', trim( (string) $v ) );
	if ( '' === $v ) {
		return null;
	}
	$c = strrpos( $v, ',' );
	$d = strrpos( $v, '.' );
	if ( false !== $c && false !== $d ) {
		$v = $c > $d ? str_replace( ',', '.', str_replace( '.', '', $v ) ) : str_replace( ',', '', $v );
	} elseif ( false !== $c ) {
		$v = str_replace( ',', '.', $v );
	}
	return preg_match( '/^-?\d+(\.\d+)?$/', $v ) ? (float) $v : null;
}

/** Tokens of a formula, or null on a character outside the language. */
function bst_quote_tokens( $s ) {
	if ( ! preg_match_all( '/\s*(?:(\d+(?:\.\d+)?)|\{([a-z0-9_-]+)(?:=([^}]*))?\}|([a-z]+)|(>=|<=|==|!=|[-+*\/(),<>]))/A', $s, $m, PREG_SET_ORDER ) ) {
		return '' === trim( $s ) ? array() : null;
	}
	$len = 0;
	$out = array();
	foreach ( $m as $t ) {
		$len += strlen( $t[0] );
		if ( isset( $t[5] ) && '' !== $t[5] ) {
			$out[] = array( 'op', $t[5] );
		} elseif ( isset( $t[4] ) && '' !== $t[4] ) {
			$out[] = array( 'fn', $t[4] );
		} elseif ( isset( $t[2] ) && '' !== $t[2] ) {
			$out[] = array( 'ref', $t[2], isset( $t[3] ) ? trim( $t[3] ) : null );
		} else {
			$out[] = array( 'num', (float) $t[1] );
		}
	}
	return '' === trim( substr( $s, $len ) ) ? $out : null;
}

/** Parse a formula to a tree. Throws on a syntax error. */
function bst_quote_parse( $s ) {
	$t = bst_quote_tokens( (string) $s );
	if ( null === $t || ! $t ) {
		throw new Exception( 'syntax' );
	}
	$i     = 0;
	$depth = 0;
	$peek  = function () use ( &$t, &$i ) {
		return $t[ $i ] ?? array( 'end', '' );
	};
	$eat   = function ( $v ) use ( &$t, &$i ) {
		if ( ( $t[ $i ][1] ?? null ) !== $v || 'op' !== ( $t[ $i ][0] ?? '' ) ) {
			throw new Exception( 'syntax' );
		}
		$i++;
	};
	$cmp   = null;
	$add   = null;
	$mul   = null;
	$unary = null;
	$cmp   = function () use ( &$add, $peek, &$i ) {
		$a = $add();
		$p = $peek();
		if ( 'op' === $p[0] && in_array( $p[1], array( '>', '<', '>=', '<=', '==', '!=' ), true ) ) {
			$i++;
			return array( 'bin', $p[1], $a, $add() );
		}
		return $a;
	};
	$add   = function () use ( &$mul, $peek, &$i ) {
		$a = $mul();
		while ( 'op' === $peek()[0] && in_array( $peek()[1], array( '+', '-' ), true ) ) {
			$o = $peek()[1];
			$i++;
			$a = array( 'bin', $o, $a, $mul() );
		}
		return $a;
	};
	$mul   = function () use ( &$unary, $peek, &$i ) {
		$a = $unary();
		while ( 'op' === $peek()[0] && in_array( $peek()[1], array( '*', '/' ), true ) ) {
			$o = $peek()[1];
			$i++;
			$a = array( 'bin', $o, $a, $unary() );
		}
		return $a;
	};
	$unary = function () use ( &$unary, &$cmp, $peek, $eat, &$i, &$depth ) {
		if ( ++$depth > 40 ) {
			throw new Exception( 'syntax' );
		}
		$p = $peek();
		$i++;
		if ( 'op' === $p[0] && '-' === $p[1] ) {
			$r = array( 'neg', $unary() );
		} elseif ( 'num' === $p[0] ) {
			$r = array( 'num', $p[1] );
		} elseif ( 'ref' === $p[0] ) {
			$r = array( 'ref', $p[1], $p[2] );
		} elseif ( 'op' === $p[0] && '(' === $p[1] ) {
			$r = $cmp();
			$eat( ')' );
		} elseif ( 'fn' === $p[0] && in_array( $p[1], array( 'min', 'max', 'round', 'ceil', 'floor', 'if' ), true ) ) {
			$eat( '(' );
			$args = array( $cmp() );
			while ( 'op' === $peek()[0] && ',' === $peek()[1] ) {
				$i++;
				$args[] = $cmp();
			}
			$eat( ')' );
			$n = count( $args );
			if ( ( 'if' === $p[1] && 3 !== $n ) || ( in_array( $p[1], array( 'ceil', 'floor' ), true ) && 1 !== $n ) || ( 'round' === $p[1] && $n > 2 ) ) {
				throw new Exception( 'syntax' );
			}
			$r = array( 'fn', $p[1], $args );
		} else {
			throw new Exception( 'syntax' );
		}
		$depth--;
		return $r;
	};
	$tree = $cmp();
	if ( 'end' !== $peek()[0] ) {
		throw new Exception( 'syntax' );
	}
	return $tree;
}

/** References of a tree: [ [id, option|null], ... ]. */
function bst_quote_refs( $n ) {
	switch ( $n[0] ) {
		case 'ref':
			return array( array( $n[1], $n[2] ) );
		case 'neg':
			return bst_quote_refs( $n[1] );
		case 'bin':
			return array_merge( bst_quote_refs( $n[2] ), bst_quote_refs( $n[3] ) );
		case 'fn':
			return array_merge( array(), ...array_map( 'bst_quote_refs', $n[2] ) );
	}
	return array();
}

/** Round half away from zero to d decimals, without float drift (1.005 -> 1.01). */
function bst_quote_round( $x, $d ) {
	$m = 10 ** $d;
	$v = abs( $x ) * $m;
	$r = floor( $v + 0.5 + 1e-9 * max( 1, $v ) );
	return ( $x < 0 ? -$r : $r ) / $m;
}

/** Evaluate a tree. $ref( id, option ) returns the number. Throws on a math error. */
function bst_quote_eval( $n, $ref ) {
	switch ( $n[0] ) {
		case 'num':
			return $n[1];
		case 'ref':
			return $ref( $n[1], $n[2] );
		case 'neg':
			return -bst_quote_eval( $n[1], $ref );
		case 'bin':
			$a = bst_quote_eval( $n[2], $ref );
			$b = bst_quote_eval( $n[3], $ref );
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
						throw new Exception( 'div0' );
					}
					$r = $a / $b;
					break;
				default:
					$e = abs( $a - $b ) < 1e-9;
					$r = (float) array( '>' => $a > $b && ! $e, '<' => $a < $b && ! $e, '>=' => $a > $b || $e, '<=' => $a < $b || $e, '==' => $e, '!=' => ! $e )[ $n[1] ];
			}
			break;
		case 'fn':
			if ( 'if' === $n[1] ) {
				return 0.0 !== (float) bst_quote_eval( $n[2][0], $ref ) ? bst_quote_eval( $n[2][1], $ref ) : bst_quote_eval( $n[2][2], $ref );
			}
			$v = array();
			foreach ( $n[2] as $arg ) {
				$v[] = bst_quote_eval( $arg, $ref );
			}
			switch ( $n[1] ) {
				case 'min':
					$r = min( $v );
					break;
				case 'max':
					$r = max( $v );
					break;
				case 'ceil':
					$r = ceil( $v[0] - 1e-9 );
					break;
				case 'floor':
					$r = floor( $v[0] + 1e-9 );
					break;
				default:
					$r = bst_quote_round( $v[0], max( -6, min( 6, (int) ( $v[1] ?? 0 ) ) ) );
			}
			break;
		default:
			throw new Exception( 'syntax' );
	}
	if ( ! is_finite( $r ) || abs( $r ) > BST_QUOTE_LIMIT ) {
		throw new Exception( 'range' );
	}
	return $r;
}

/**
 * Problems of the pricing setup (for the editor and to switch the quote off):
 * [ 'line' => id, 'code' => syntax|missing_ref|bad_option|cycle, 'ref' => ... ].
 */
function bst_quote_validate( $pricing, $fields ) {
	$p      = bst_quote_settings( $pricing );
	$fields = array_column( bst_form_schema_normalize( $fields ), null, 'id' );
	$lines  = array_column( $p['lines'], null, 'id' );
	$errors = array();
	$graph  = array();
	foreach ( $p['lines'] as $l ) {
		$graph[ $l['id'] ] = array();
		try {
			$refs = bst_quote_refs( bst_quote_parse( $l['expr'] ) );
		} catch ( Exception $e ) {
			$errors[] = array( 'line' => $l['id'], 'code' => 'syntax', 'ref' => '' );
			continue;
		}
		foreach ( $refs as $r ) {
			if ( isset( $lines[ $r[0] ] ) && null === $r[1] ) {
				$graph[ $l['id'] ][] = $r[0];
			} elseif ( ! isset( $fields[ $r[0] ] ) ) {
				$errors[] = array( 'line' => $l['id'], 'code' => 'missing_ref', 'ref' => $r[0] );
			} elseif ( null !== $r[1] && ! in_array( $r[1], bst_native_field_options( $fields[ $r[0] ] ), true ) ) {
				$errors[] = array( 'line' => $l['id'], 'code' => 'bad_option', 'ref' => $r[0] . '=' . $r[1] );
			}
		}
	}
	foreach ( bst_form_schema_cycles( array_map( function ( $id ) use ( $graph ) {
		return array( 'id' => $id, 'show' => array( 'rules' => array_map( function ( $to ) {
			return array( 'field' => $to );
		}, $graph[ $id ] ) ) );
	}, array_keys( $graph ) ) ) as $id ) {
		$errors[] = array( 'line' => $id, 'code' => 'cycle', 'ref' => $id );
	}
	foreach ( $graph as $id => $to ) {
		if ( in_array( $id, $to, true ) ) {
			$errors[] = array( 'line' => $id, 'code' => 'cycle', 'ref' => $id );
		}
	}
	return $errors;
}

/** Revision of the pricing rules: changes when any price, formula or option price changes. */
function bst_quote_revision( $pricing, $fields ) {
	$prices = array();
	foreach ( bst_form_schema_normalize( $fields ) as $f ) {
		$prices[ $f['id'] ] = bst_quote_option_prices( $f );
	}
	return substr( md5( wp_json_encode( array( BST_QUOTE_POLICY, bst_quote_settings( $pricing ), $prices ) ) ), 0, 12 );
}

/**
 * Quote for these field values (keyed by field name).
 * status: off | ok | incomplete (a required field it needs is empty) | error (needs a manual price).
 */
function bst_quote_compute( $pricing, $fields, $values ) {
	$p      = bst_quote_settings( $pricing );
	$rev    = bst_quote_revision( $pricing, $fields );
	$base   = array( 'status' => 'off', 'lines' => array(), 'total' => 0, 'symbol' => $p['symbol'], 'decimals' => $p['decimals'], 'rev' => $rev, 'policy' => BST_QUOTE_POLICY );
	if ( ! $p['enabled'] ) {
		return $base;
	}
	if ( bst_quote_validate( $pricing, $fields ) ) {
		return array_merge( $base, array( 'status' => 'error' ) );
	}
	$fields = array_column( bst_form_schema_normalize( $fields ), null, 'id' );
	$state  = bst_form_schema_state( $fields, $values );
	$trees  = array();
	foreach ( $p['lines'] as $l ) {
		$trees[ $l['id'] ] = array( 'label' => $l['label'], 'tree' => bst_quote_parse( $l['expr'] ) );
	}
	$done       = array();
	$incomplete = false;
	$line       = null;
	$ref        = function ( $id, $opt ) use ( &$line, &$done, &$incomplete, $trees, $fields, $state, $values, $p ) {
		if ( isset( $trees[ $id ] ) && null === $opt ) {
			return $line( $id );
		}
		$f = $fields[ $id ];
		if ( ! $state[ $id ]['visible'] ) {
			return 0.0;
		}
		$v = (string) ( $values[ sanitize_key( $f['name'] ) ] ?? '' );
		if ( '' === $v ) {
			$incomplete = $incomplete || $state[ $id ]['required'];
			return 0.0;
		}
		$picked = in_array( $f['type'] ?? '', array( 'select', 'radio', 'checkbox' ), true ) ? array_map( 'trim', explode( ', ', $v ) ) : array( $v );
		if ( null !== $opt ) {
			return in_array( $opt, $picked, true ) ? 1.0 : 0.0;
		}
		$prices = bst_quote_option_prices( $f );
		if ( $prices ) {
			$sum = 0.0;
			foreach ( $picked as $o ) {
				$sum += $prices[ $o ] ?? 0.0;
			}
			return $sum;
		}
		$n = bst_quote_number( $v );
		if ( null === $n || $n < 0 ) {
			throw new Exception( 'input' );
		}
		return $n;
	};
	$line       = function ( $id ) use ( &$ref, &$done, $trees, $p ) {
		if ( ! isset( $done[ $id ] ) ) {
			$done[ $id ] = bst_quote_round( bst_quote_eval( $trees[ $id ]['tree'], $ref ), $p['decimals'] );
		}
		return $done[ $id ];
	};
	$out   = array();
	$total = 0.0;
	try {
		foreach ( $trees as $id => $t ) {
			$a      = $line( $id );
			$total += $a;
			$out[]  = array( 'id' => $id, 'label' => $t['label'], 'amount' => $a );
		}
	} catch ( Exception $e ) {
		return array_merge( $base, array( 'status' => 'error' ) );
	}
	$total = bst_quote_round( $total, $p['decimals'] );
	// Nothing chargeable picked yet: never show a free total.
	if ( $incomplete || ! array_filter( array_column( $out, 'amount' ) ) ) {
		return array_merge( $base, array( 'status' => 'incomplete' ) );
	}
	if ( $total < 0 || abs( $total ) > BST_QUOTE_LIMIT ) {
		return array_merge( $base, array( 'status' => 'error' ) );
	}
	return array_merge( $base, array( 'status' => 'ok', 'lines' => $out, 'total' => $total ) );
}

/** 1234567 -> "1.234.567 ₫". */
function bst_quote_money( $x, $q ) {
	return number_format( (float) $x, (int) $q['decimals'], ',', '.' ) . ( '' !== $q['symbol'] ? ' ' . $q['symbol'] : '' );
}

/** Plain-text quote for mail tags and entries. */
function bst_quote_text( $q, $html = false ) {
	if ( 'ok' !== $q['status'] ) {
		return 'off' === $q['status'] ? '' : __( 'Price to be confirmed', 'bigspy-ai-theme-wp' );
	}
	$rows = array();
	foreach ( $q['lines'] as $l ) {
		if ( 0.0 !== (float) $l['amount'] ) {
			$rows[] = $html ? '<tr><td>' . esc_html( $l['label'] ) . '</td><td style="text-align:right">' . esc_html( bst_quote_money( $l['amount'], $q ) ) . '</td></tr>' : $l['label'] . ': ' . bst_quote_money( $l['amount'], $q );
		}
	}
	$total = __( 'Estimated total', 'bigspy-ai-theme-wp' );
	if ( $html ) {
		return '<table>' . implode( '', $rows ) . '<tr><th style="text-align:left">' . esc_html( $total ) . '</th><th style="text-align:right">' . esc_html( bst_quote_money( $q['total'], $q ) ) . '</th></tr></table>';
	}
	$rows[] = $total . ': ' . bst_quote_money( $q['total'], $q );
	return implode( "\n", $rows );
}

/** Quote of the submission being handled, for the mail tags. */
function bst_quote_current( $set = null ) {
	static $q = null;
	if ( null !== $set ) {
		$q = $set;
	}
	return $q;
}

/** Front-end box: filled by blocks.js; without script it says the price comes later. */
function bst_quote_markup( $s ) {
	$p = bst_quote_settings( $s['pricing'] ?? array() );
	if ( ! $p['enabled'] ) {
		return '';
	}
	$cfg = array(
		'lines'    => $p['lines'],
		'symbol'   => $p['symbol'],
		'decimals' => $p['decimals'],
		'prices'   => (object) array_filter( array_map( 'bst_quote_option_prices', array_column( $s['fields'], null, 'id' ) ) ),
		'types'    => array_column( $s['fields'], 'type', 'id' ),
		'ok'       => ! bst_quote_validate( $s['pricing'], $s['fields'] ),
	);
	wp_enqueue_script( 'bst-form-quote', BST_URI . '/assets/js/form-quote.js', array(), bst_asset_version( 'assets/js/form-quote.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	return '<div class="bs-nf-quote" data-bs-quote="' . esc_attr( wp_json_encode( $cfg ) ) . '" data-rev="' . esc_attr( bst_quote_revision( $s['pricing'], $s['fields'] ) ) . '"'
		. ' data-msg-incomplete="' . esc_attr__( 'Choose more details to see the price.', 'bigspy-ai-theme-wp' ) . '"'
		. ' data-msg-error="' . esc_attr__( 'Price to be confirmed', 'bigspy-ai-theme-wp' ) . '"'
		. ' data-msg-total="' . esc_attr__( 'Estimated total', 'bigspy-ai-theme-wp' ) . '">'
		. '<div class="bs-nf-quote__body" aria-live="polite" aria-atomic="true"><p class="bs-nf-quote__status">' . esc_html__( 'The price is calculated when you send the form.', 'bigspy-ai-theme-wp' ) . '</p></div>'
		. ( '' !== $p['note'] ? '<p class="bs-nf-quote__note">' . esc_html( $p['note'] ) . '</p>' : '' )
		. '</div>';
}
