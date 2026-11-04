<?php
/**
 * Built-in form pricing (G-58): a quote computed from the form's fields.
 *
 * The block attribute "pricing" holds { enabled, currency, decimals, lines[],
 * minTotal, roundStep, note }. Each line: { id, label, kind, when?, ... }:
 *   fixed   amount
 *   qty     field × unit                     (kg, m², hours, people…)
 *   area    field × field2 × unit            (width × height)
 *   option  price per choice of field (× qtyField when set)
 *   tier    field quantity priced by tiers [ {from, unit} ], mode all|step
 *   percent pct of target lines (all earlier lines when empty); negative = discount
 *   formula restricted expression: numbers, {fieldId}, {lineId}, + - * / ( ),
 *           comparisons, min max round ceil floor if
 * Money is kept as integers in the currency's smallest unit. The browser shows a
 * preview with the same rules (assets/js/blocks.js); the server always computes
 * again from the saved block and never trusts a total sent by the browser.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Line kinds. */
function bst_pricing_kinds() {
	return array( 'fixed', 'qty', 'area', 'option', 'tier', 'percent', 'formula' );
}

/** Clean pricing settings; null when pricing is off or has no lines. */
function bst_pricing_settings( $p ) {
	if ( ! is_array( $p ) || empty( $p['enabled'] ) || empty( $p['lines'] ) || ! is_array( $p['lines'] ) ) {
		return null;
	}
	$lines = array();
	$seen  = array();
	foreach ( $p['lines'] as $l ) {
		if ( ! is_array( $l ) || ! in_array( $l['kind'] ?? '', bst_pricing_kinds(), true ) ) {
			continue;
		}
		$id = sanitize_key( (string) ( $l['id'] ?? '' ) );
		if ( '' === $id || isset( $seen[ $id ] ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$l['id']     = $id;
		$l['label']  = (string) ( $l['label'] ?? '' );
		$l['when']   = bst_form_when_clean( $l['when'] ?? null );
		$lines[]     = $l;
	}
	if ( ! $lines ) {
		return null;
	}
	$decimals = (int) ( $p['decimals'] ?? 0 );
	$cur      = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', (string) ( $p['currency'] ?? 'VND' ) ), 0, 3 ) );
	return array(
		'currency'  => $cur ? $cur : 'VND',
		'decimals'  => max( 0, min( 3, $decimals ) ),
		'lines'     => $lines,
		'minTotal'  => max( 0, (float) ( $p['minTotal'] ?? 0 ) ),
		'roundStep' => max( 0, (float) ( $p['roundStep'] ?? 0 ) ),
		'note'      => (string) ( $p['note'] ?? '' ),
	);
}

/** Revision of the price rules: changes whenever the saved rules change. */
function bst_pricing_revision( $p ) {
	return substr( md5( (string) wp_json_encode( $p ) ), 0, 12 );
}

/** Number typed by the visitor ("1,5" and "1.5" both work); null when not a number. */
function bst_pricing_num( $v ) {
	$v = str_replace( array( ' ', "\u{00a0}" ), '', trim( (string) $v ) );
	if ( preg_match( '/^-?\d+,\d+$/', $v ) ) {
		$v = str_replace( ',', '.', $v );
	}
	return is_numeric( $v ) ? (float) $v : null;
}

/** Amount (major units) to integer minor units, rounding half away from zero. */
function bst_pricing_minor( $amount, $decimals ) {
	$f = 10 ** $decimals;
	$x = $amount * $f;
	return (int) ( $x < 0 ? -floor( -$x + 0.5 + 1e-9 ) : floor( $x + 0.5 + 1e-9 ) );
}

/**
 * Evaluate a restricted expression. Returns float, or null on any error.
 * Grammar: expr := cmp ; cmp := sum ((>|<|>=|<=|==|!=) sum)? ;
 * sum := term ((+|-) term)* ; term := unary ((*|/) unary)* ;
 * unary := -unary | atom ; atom := number | {id} | name(args) | (expr)
 *
 * @param string   $src  Expression.
 * @param callable $vars id => float|null.
 */
function bst_pricing_eval( $src, $vars ) {
	if ( ! preg_match_all( '/\s*(\d+(?:\.\d+)?|\{[a-z0-9_-]+\}|[a-z]+|>=|<=|==|!=|[-+*\/(),<>])/A', (string) $src, $m ) || implode( '', $m[0] ) !== (string) $src ) {
		return null;
	}
	$t   = $m[1];
	$pos = 0;
	$fail = false;
	$peek = function () use ( &$t, &$pos ) {
		return $t[ $pos ] ?? null;
	};
	$expr = null;
	$atom = function () use ( &$t, &$pos, &$fail, &$expr, $peek, $vars ) {
		$tok = $t[ $pos++ ] ?? null;
		if ( null === $tok ) {
			$fail = true;
			return 0;
		}
		if ( is_numeric( $tok ) ) {
			return (float) $tok;
		}
		if ( '{' === $tok[0] ) {
			$v = $vars( substr( $tok, 1, -1 ) );
			if ( null === $v ) {
				$fail = true;
				return 0;
			}
			return (float) $v;
		}
		if ( '(' === $tok ) {
			$v = $expr();
			if ( ')' !== ( $t[ $pos++ ] ?? null ) ) {
				$fail = true;
			}
			return $v;
		}
		if ( preg_match( '/^[a-z]+$/', $tok ) && '(' === $peek() ) {
			++$pos;
			$args = array();
			if ( ')' !== $peek() ) {
				do {
					$args[] = $expr();
				} while ( ',' === $peek() && ++$pos );
			}
			if ( ')' !== ( $t[ $pos++ ] ?? null ) ) {
				$fail = true;
				return 0;
			}
			$n = count( $args );
			switch ( $tok ) {
				case 'min':
					return $n ? min( $args ) : ( $fail = true ) && 0;
				case 'max':
					return $n ? max( $args ) : ( $fail = true ) && 0;
				case 'round':
					return 1 === $n ? round( $args[0] ) : ( 2 === $n && $args[1] > 0 ? round( $args[0] / $args[1] ) * $args[1] : ( ( $fail = true ) && 0 ) );
				case 'ceil':
					return 1 === $n ? ceil( $args[0] ) : ( 2 === $n && $args[1] > 0 ? ceil( $args[0] / $args[1] ) * $args[1] : ( ( $fail = true ) && 0 ) );
				case 'floor':
					return 1 === $n ? floor( $args[0] ) : ( 2 === $n && $args[1] > 0 ? floor( $args[0] / $args[1] ) * $args[1] : ( ( $fail = true ) && 0 ) );
				case 'if':
					return 3 === $n ? ( $args[0] ? $args[1] : $args[2] ) : ( ( $fail = true ) && 0 );
			}
		}
		$fail = true;
		return 0;
	};
	$unary = function () use ( &$unary, &$pos, $peek, $atom ) {
		if ( '-' === $peek() ) {
			++$pos;
			return -$unary();
		}
		return $atom();
	};
	$term = function () use ( &$pos, &$fail, $peek, $unary ) {
		$v = $unary();
		while ( in_array( $peek(), array( '*', '/' ), true ) ) {
			$op = $peek();
			++$pos;
			$r = $unary();
			if ( '/' === $op && 0.0 === (float) $r ) {
				$fail = true;
				return 0;
			}
			$v = '*' === $op ? $v * $r : $v / $r;
		}
		return $v;
	};
	$sum = function () use ( &$pos, $peek, $term ) {
		$v = $term();
		while ( in_array( $peek(), array( '+', '-' ), true ) ) {
			$op = $peek();
			++$pos;
			$v = '+' === $op ? $v + $term() : $v - $term();
		}
		return $v;
	};
	$expr = function () use ( &$pos, $peek, $sum ) {
		$v = $sum();
		$op = $peek();
		if ( in_array( $op, array( '>', '<', '>=', '<=', '==', '!=' ), true ) ) {
			++$pos;
			$r = $sum();
			switch ( $op ) {
				case '>':
					return $v > $r ? 1.0 : 0.0;
				case '<':
					return $v < $r ? 1.0 : 0.0;
				case '>=':
					return $v >= $r ? 1.0 : 0.0;
				case '<=':
					return $v <= $r ? 1.0 : 0.0;
				case '==':
					return abs( $v - $r ) < 1e-9 ? 1.0 : 0.0;
				default:
					return abs( $v - $r ) >= 1e-9 ? 1.0 : 0.0;
			}
		}
		return $v;
	};
	$v = $expr();
	if ( $fail || $pos !== count( $t ) || ! is_finite( $v ) || abs( $v ) > 1e12 ) {
		return null;
	}
	return (float) $v;
}

/**
 * Compute the quote.
 *
 * @param array $p      Pricing settings from bst_pricing_settings().
 * @param array $fields Normalized fields.
 * @param array $values name => value (hidden fields already '').
 * @return array { status: ok|incomplete|confirm, lines: [ {id,label,amount} ], total, currency, decimals }
 *         Amounts are integers in minor units.
 */
function bst_pricing_quote( $p, $fields, $values ) {
	$by_id = array();
	foreach ( $fields as $f ) {
		$by_id[ $f['id'] ] = $f;
	}
	$shown  = bst_form_visibility( $fields, $values );
	$dec    = $p['decimals'];
	$raw    = function ( $fid ) use ( $by_id, $values, $shown ) {
		if ( ! isset( $by_id[ $fid ] ) || empty( $shown[ $fid ] ) ) {
			return '';
		}
		return (string) ( $values[ sanitize_key( $by_id[ $fid ]['name'] ) ] ?? '' );
	};
	$status = 'ok';
	$need   = function ( $fid ) use ( $raw, &$status ) {
		$n = bst_pricing_num( $raw( $fid ) );
		if ( null === $n ) {
			$status = 'confirm' === $status ? 'confirm' : 'incomplete';
		}
		return $n;
	};
	$amounts = array();
	$out     = array();
	foreach ( $p['lines'] as $l ) {
		if ( $l['when'] ) {
			$f = array( 'id' => '__line', 'name' => '__line', 'when' => $l['when'] );
			$v = bst_form_visibility( array_merge( $fields, array( $f ) ), $values );
			if ( empty( $v['__line'] ) ) {
				continue;
			}
		}
		$amount = null;
		$unit   = (float) ( $l['unit'] ?? 0 );
		switch ( $l['kind'] ) {
			case 'fixed':
				$amount = (float) ( $l['amount'] ?? 0 );
				break;
			case 'qty':
				$q      = $need( (string) ( $l['field'] ?? '' ) );
				$amount = null === $q ? null : $q * $unit;
				break;
			case 'area':
				$a      = $need( (string) ( $l['field'] ?? '' ) );
				$b      = $need( (string) ( $l['field2'] ?? '' ) );
				$amount = null === $a || null === $b ? null : max( $a * $b, (float) ( $l['minQty'] ?? 0 ) ) * $unit;
				break;
			case 'option':
				$picked = $raw( (string) ( $l['field'] ?? '' ) );
				if ( '' === $picked ) {
					$status = 'confirm' === $status ? 'confirm' : 'incomplete';
					break;
				}
				$prices = (array) ( $l['prices'] ?? array() );
				$amount = 0.0;
				foreach ( explode( ', ', $picked ) as $choice ) {
					if ( ! isset( $prices[ $choice ] ) || '' === $prices[ $choice ] || null === bst_pricing_num( $prices[ $choice ] ) ) {
						$status = 'confirm';
						continue;
					}
					$amount += bst_pricing_num( $prices[ $choice ] );
				}
				if ( ! empty( $l['qtyField'] ) ) {
					$q      = $need( (string) $l['qtyField'] );
					$amount = null === $q ? null : $amount * $q;
				}
				break;
			case 'tier':
				$q     = $need( (string) ( $l['field'] ?? '' ) );
				$tiers = array_values( array_filter( (array) ( $l['tiers'] ?? array() ), 'is_array' ) );
				usort( $tiers, function ( $x, $y ) {
					return (float) ( $x['from'] ?? 0 ) <=> (float) ( $y['from'] ?? 0 );
				} );
				if ( null === $q ) {
					break;
				}
				if ( ! $tiers || $q < (float) ( $tiers[0]['from'] ?? 0 ) ) {
					$status = 'confirm';
					break;
				}
				if ( 'step' === ( $l['mode'] ?? 'all' ) ) {
					$amount = 0.0;
					foreach ( $tiers as $i => $tr ) {
						$from = (float) ( $tr['from'] ?? 0 );
						$to   = isset( $tiers[ $i + 1 ] ) ? (float) $tiers[ $i + 1 ]['from'] : INF;
						if ( $q > $from ) {
							$amount += ( min( $q, $to ) - $from ) * (float) ( $tr['unit'] ?? 0 );
						}
					}
				} else {
					$u = 0.0;
					foreach ( $tiers as $tr ) {
						if ( $q >= (float) ( $tr['from'] ?? 0 ) ) {
							$u = (float) ( $tr['unit'] ?? 0 );
						}
					}
					$amount = $q * $u;
				}
				break;
			case 'percent':
				$targets = array_filter( array_map( 'sanitize_key', (array) ( $l['targets'] ?? array() ) ) );
				$base    = 0;
				foreach ( $amounts as $lid => $minor ) {
					if ( ! $targets || in_array( $lid, $targets, true ) ) {
						$base += $minor;
					}
				}
				$amount = $base / ( 10 ** $dec ) * (float) ( $l['pct'] ?? 0 ) / 100;
				break;
			case 'formula':
				$missing = false;
				$amount  = bst_pricing_eval(
					trim( (string) ( $l['expr'] ?? '' ) ),
					function ( $id ) use ( $by_id, $raw, $amounts, $dec, &$missing ) {
						if ( isset( $amounts[ $id ] ) ) {
							return $amounts[ $id ] / ( 10 ** $dec );
						}
						if ( isset( $by_id[ $id ] ) ) {
							$n = bst_pricing_num( $raw( $id ) );
							if ( null === $n ) {
								$missing = true;
							}
							return $n;
						}
						return 0.0; // A line that did not apply counts as 0.
					}
				);
				if ( null === $amount ) {
					$status = $missing ? ( 'confirm' === $status ? 'confirm' : 'incomplete' ) : 'confirm';
				}
				break;
		}
		if ( null === $amount ) {
			continue;
		}
		$minor            = bst_pricing_minor( $amount, $dec );
		$amounts[ $l['id'] ] = $minor;
		$out[]            = array( 'id' => $l['id'], 'label' => $l['label'], 'amount' => $minor );
	}
	$total = array_sum( $amounts );
	if ( $p['roundStep'] > 0 ) {
		$step  = max( 1, bst_pricing_minor( $p['roundStep'], $dec ) );
		$total = (int) ( round( $total / $step ) * $step );
	}
	// A negative total means wrong input or rules: never show it as a price.
	if ( $total < 0 ) {
		$total  = 0;
		$status = 'confirm';
	}
	$min = bst_pricing_minor( $p['minTotal'], $dec );
	if ( $total < $min ) {
		$total = $min;
	}
	return array(
		'status'   => $status,
		'lines'    => $out,
		'total'    => $total,
		'currency' => $p['currency'],
		'decimals' => $dec,
	);
}

/** Format minor units for display and mail: 1.234.567 ₫ / 12.50 USD. */
function bst_pricing_format( $minor, $currency, $decimals ) {
	$v = $minor / ( 10 ** $decimals );
	if ( 'VND' === $currency ) {
		return number_format( $v, $decimals, ',', '.' ) . ' ₫';
	}
	return number_format( $v, $decimals, '.', ',' ) . ' ' . $currency;
}

/** Mail text of a quote: lines, total and status. */
function bst_pricing_text( $q, $html = false ) {
	$rows = array();
	foreach ( $q['lines'] as $l ) {
		$rows[] = ( '' !== $l['label'] ? $l['label'] : $l['id'] ) . ': ' . bst_pricing_format( $l['amount'], $q['currency'], $q['decimals'] );
	}
	if ( $html ) {
		$rows = array_map( 'esc_html', $rows );
	}
	return implode( $html ? '<br>' : "\n", $rows );
}

/** Total text: amount, or the reason there is none. */
function bst_pricing_total_text( $q ) {
	if ( 'incomplete' === $q['status'] ) {
		return __( 'Not enough information to price', 'bigspy-ai-theme-wp' );
	}
	$t = bst_pricing_format( $q['total'], $q['currency'], $q['decimals'] );
	return 'confirm' === $q['status'] ? $t . ' (' . __( 'price to be confirmed', 'bigspy-ai-theme-wp' ) . ')' : $t;
}

/** Expression problems for the editor check: syntax or unknown ids. Empty string when fine. */
function bst_pricing_check_expr( $expr, $known ) {
	$unknown = array();
	$ok      = bst_pricing_eval(
		$expr,
		function ( $id ) use ( $known, &$unknown ) {
			if ( ! in_array( $id, $known, true ) ) {
				$unknown[] = $id;
			}
			return 1.37; // Unlikely to make a divisor 0 by chance.
		}
	);
	if ( $unknown ) {
		return 'unknown:' . implode( ',', $unknown );
	}
	return null === $ok ? 'syntax' : '';
}
