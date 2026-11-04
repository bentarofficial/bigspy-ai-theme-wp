<?php
/**
 * G-70 eval: score one AI Designer result against its case (no WordPress).
 *
 * Usage: php tools/eval/score.php <case.json> <result.json> [<out.html>]
 * Prints a JSON score. When <out.html> is given, writes the page rendered with
 * the real block templates (lib/render.php) for the 375px browser check, unless
 * the result already carries rendered_html from a live site.
 *
 * Server rules are not copied: bst_aid_editable_percent, bst_aid_block_rule_error,
 * bst_aid_text_has_layout and bst_aid_core_blocks are read from
 * ai-designer/includes/tools-design.php; Form rules come from inc/form-schema.php
 * and inc/form-pricing.php.
 */

require_once __DIR__ . '/lib/render.php';

$missing = bst_eval_load_functions(
	'ai-designer/includes/tools-design.php',
	array( 'bst_aid_core_blocks', 'bst_aid_code_usage', 'bst_aid_block_count', 'bst_aid_editable_percent', 'bst_aid_block_rule_error', 'bst_aid_text_has_layout' )
);
if ( $missing ) {
	fwrite( STDERR, 'Server rule functions not found: ' . implode( ', ', $missing ) . "\n" );
	exit( 2 );
}
bst_eval_load_forms();

$case   = json_decode( file_get_contents( $argv[1] ), true );
$result = json_decode( file_get_contents( $argv[2] ), true );
$html_o = $argv[3] ?? '';
if ( ! is_array( $case ) || ! is_array( $result ) ) {
	fwrite( STDERR, "Usage: php score.php <case.json> <result.json> [out.html]\n" );
	exit( 2 );
}

$content = isset( $result['content'] ) ? (string) $result['content'] : bst_eval_tree_to_markup( $result['tree'] ?? array() );
$blocks  = parse_blocks( $content );
$facts   = (array) ( $case['facts'] ?? array() );
$expect  = (array) ( $case['expect'] ?? array() );
$limits  = (array) ( $case['limits'] ?? array() );

/* ---------------------------------------------------------------- Helpers */

$theme_blocks = array();
foreach ( glob( BST_EVAL_THEME . '/blocks/*/block.json' ) as $f ) {
	$theme_blocks[] = json_decode( file_get_contents( $f ), true )['name'];
}
$allowed_blocks = array_merge( $theme_blocks, bst_aid_core_blocks() );

/** Visit every named block with its attributes merged over block.json defaults. */
function bst_eval_walk( $blocks, $fn, $depth = 0 ) {
	foreach ( $blocks as $b ) {
		if ( $b['blockName'] ) {
			$meta  = bst_eval_block_meta( $b['blockName'] );
			$attrs = (array) $b['attrs'];
			foreach ( $meta['attributes'] ?? array() as $k => $def ) {
				if ( ! array_key_exists( $k, $attrs ) && array_key_exists( 'default', $def ) ) {
					$attrs[ $k ] = $def['default'];
				}
			}
			$fn( $b, $attrs, $depth );
		} else {
			$fn( $b, array(), $depth );
		}
		bst_eval_walk( $b['innerBlocks'] ?? array(), $fn, $depth + 1 );
	}
}

/** Visible text of an attribute set: strings except URLs, colors, ids, classes, CSS and form/mail settings. */
function bst_eval_attr_text( $attrs, $key = '' ) {
	$skip = '/(url|link|href|color|colour|bg$|background|class|anchor|^id$|Id$|image|icon|css|style|align|layout|size|width|height|gap|radius|shadow|position|effect|mode|tag|provider|formKey|formName|^mail|^msg|redirect|language|animation|font|variant|target|^code$)/i';
	if ( is_string( $attrs ) ) {
		return ( '' !== $key && preg_match( $skip, $key ) ) ? '' : $attrs;
	}
	if ( ! is_array( $attrs ) ) {
		return '';
	}
	$out = array();
	foreach ( $attrs as $k => $v ) {
		if ( is_string( $k ) && preg_match( $skip, $k ) ) {
			continue;
		}
		if ( in_array( $k, array( 'fields', 'pricing' ), true ) && '' === $key ) {
			continue; // Form rules are checked separately.
		}
		$out[] = bst_eval_attr_text( $v, is_string( $k ) ? $k : $key );
	}
	return implode( "\n", array_filter( $out, 'strlen' ) );
}

/** Plain text: tags removed, entities decoded, spaces collapsed. */
function bst_eval_plain( $html ) {
	$t = html_entity_decode( strip_tags( preg_replace( '/<(br|\/p|\/li|\/h\d|\/div)[^>]*>/i', "\n", (string) $html ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return trim( preg_replace( '/[ \t\x{00A0}]+/u', ' ', $t ) );
}

/** Number in a Vietnamese/English string: "1.200.000" "1,5" "150k" "2tr" -> float, or null. */
function bst_eval_num( $s, $unit = '' ) {
	$s = trim( (string) $s );
	if ( '' === $s ) {
		return null;
	}
	$s = preg_replace( '/\s+/', '', $s );
	if ( preg_match( '/^\d{1,3}([.,]\d{3})+$/', $s ) ) {
		$n = (float) preg_replace( '/[.,]/', '', $s );
	} elseif ( preg_match( '/^\d+([.,]\d+)?$/', $s ) ) {
		$n = (float) str_replace( ',', '.', $s );
	} else {
		return null;
	}
	$u = mb_strtolower( $unit );
	if ( in_array( $u, array( 'k', 'nghìn', 'ngàn' ), true ) ) {
		$n *= 1000;
	} elseif ( in_array( $u, array( 'tr', 'triệu' ), true ) ) {
		$n *= 1000000;
	} elseif ( in_array( $u, array( 'tỷ', 'tỉ' ), true ) ) {
		$n *= 1000000000;
	}
	return round( $n, 3 );
}

/** Allowed values from the case facts, normalized. */
function bst_eval_allowed( $list ) {
	$out = array();
	foreach ( (array) $list as $v ) {
		$v = (string) $v;
		if ( preg_match( '/^\s*([\d.,\s]*\d)\s*(k|nghìn|ngàn|tr|triệu|tỷ|tỉ)?\b/u', $v, $m ) ) {
			$n = bst_eval_num( $m[1], $m[2] ?? '' );
			if ( null !== $n ) {
				$out[] = $n;
			}
		}
		$digits = preg_replace( '/\D/', '', $v );
		if ( strlen( $digits ) >= 8 ) {
			$out[] = 'tel:' . ltrim( preg_replace( '/^84/', '0', $digits ), '' );
		}
	}
	return $out;
}

/**
 * Factual claims in text: money, percentages, statistics ("500+", "10 năm"), ratings, phone numbers.
 *
 * @return array[] { kind, text, value }
 */
function bst_eval_claims( $text ) {
	$out = array();
	$t   = ' ' . $text . ' ';
	if ( preg_match_all( '/(?:\+84|\b0)(?:[\s.\-]?\d){8,10}\b/u', $t, $m ) ) {
		foreach ( $m[0] as $p ) {
			$d     = preg_replace( '/\D/', '', $p );
			$out[] = array( 'kind' => 'phone', 'text' => trim( $p ), 'value' => 'tel:' . preg_replace( '/^84/', '0', $d ) );
		}
		$t = preg_replace( '/(?:\+84|\b0)(?:[\s.\-]?\d){8,10}\b/u', ' ', $t );
	}
	$money = '/(?<![\w.,])(\d{1,3}(?:[.,]\d{3})+|\d+(?:[.,]\d+)?)\s*(k|nghìn|ngàn|tr|triệu|tỷ|tỉ|đ|₫|vnđ|vnd|đồng|usd)(?![\p{L}])/iu';
	if ( preg_match_all( $money, $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'money', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1], $x[2] ) );
		}
		$t = preg_replace( $money, ' ', $t );
	}
	if ( preg_match_all( '/\$\s*(\d[\d.,]*)/u', $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'money', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1] ) );
		}
	}
	if ( preg_match_all( '/(\d+(?:[.,]\d+)?)\s*%/u', $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'percent', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1] ) );
		}
	}
	if ( preg_match_all( '/(\d+(?:[.,]\d+)?)\s*\/\s*5\b/u', $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'rating', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1] ) );
		}
	}
	$nouns = '(?:năm|khách|học viên|dự án|chi nhánh|bác sĩ|nhân viên|chuyên gia|đối tác|đơn hàng|căn|sản phẩm|giải thưởng|cơ sở|xe|lượt|người|years?|clients?|customers?|projects?)';
	if ( preg_match_all( '/(?<![\w.,])(\d{1,3}(?:[.,]\d{3})+|\d+)\s*(\+)?\s*' . $nouns . '/iu', $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'stat', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1] ) );
		}
	}
	if ( preg_match_all( '/(?<![\w.,])(\d{1,3}(?:[.,]\d{3})+|\d+)\s*\+(?!\s*' . $nouns . ')/iu', $t, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $x ) {
			$out[] = array( 'kind' => 'stat', 'text' => trim( $x[0] ), 'value' => bst_eval_num( $x[1] ) );
		}
	}
	return $out;
}

/**
 * Visitor values for a quote check. The AI picks field names, so the case names
 * fields by a regex on label or name, and choices by a regex on option text.
 *
 * @param array $spec      [ { field: regex, value?: string, option?: regex } ].
 * @param array $fields    Normalized fields.
 * @param array $unmatched Specs that found no field or option.
 * @return array name => value
 */
function bst_eval_quote_values( $spec, $fields, &$unmatched ) {
	$unmatched = array();
	$values    = array();
	foreach ( $spec as $s ) {
		$re  = '/' . str_replace( '/', '\\/', (string) $s['field'] ) . '/iu';
		$hit = null;
		foreach ( $fields as $f ) {
			if ( preg_match( $re, (string) ( $f['label'] ?? '' ) ) || preg_match( $re, (string) $f['name'] ) ) {
				$hit = $f;
				break;
			}
		}
		if ( ! $hit ) {
			$unmatched[] = 'field /' . $s['field'] . '/';
			continue;
		}
		if ( isset( $s['option'] ) ) {
			$ore  = '/' . str_replace( '/', '\\/', (string) $s['option'] ) . '/iu';
			$pick = array_values( array_filter( bst_native_field_options( $hit ), function ( $o ) use ( $ore ) { return preg_match( $ore, $o ); } ) );
			if ( ! $pick ) {
				$unmatched[] = 'option /' . $s['option'] . '/ in ' . $hit['name'];
				continue;
			}
			$val = 'checkbox' === ( $hit['type'] ?? '' ) ? implode( ', ', $pick ) : $pick[0];
		} else {
			$val = (string) ( $s['value'] ?? '' );
		}
		$values[ sanitize_key( $hit['name'] ) ] = $val;
	}
	return $values;
}

/* ------------------------------------------------------------- Measure */

$names     = array();
$unknown   = array();
$freeform  = 0;
$h1        = 0;
$layout_tx = array();
$texts     = array();
$forms     = array();
$reviews   = 0;
$counters  = array();
bst_eval_walk(
	$blocks,
	function ( $b, $attrs, $depth ) use ( &$names, &$unknown, &$freeform, &$h1, &$layout_tx, &$texts, &$forms, &$reviews, &$counters, $allowed_blocks ) {
		$name = $b['blockName'];
		if ( ! $name ) {
			if ( '' !== trim( (string) $b['innerHTML'] ) ) {
				$freeform++;
				$texts[] = bst_eval_plain( $b['innerHTML'] );
			}
			return;
		}
		$names[ $name ] = ( $names[ $name ] ?? 0 ) + 1;
		if ( ! in_array( $name, $allowed_blocks, true ) ) {
			$unknown[ $name ] = true;
		}
		$own_html = implode( '', array_filter( $b['innerContent'] ?? array(), 'is_string' ) );
		$h1      += preg_match_all( '/<h1[\s>]/i', $own_html );
		if ( in_array( $name, array( 'bst/heading', 'bst/title' ), true ) && 'h1' === strtolower( (string) ( $attrs['tag'] ?? '' ) ) ) {
			$h1++;
		}
		if ( 'core/heading' === $name && 1 === (int) ( $b['attrs']['level'] ?? 2 ) ) {
			$h1 += 0 === preg_match( '/<h1[\s>]/i', $own_html ) ? 1 : 0;
		}
		if ( 'bst/text' === $name ) {
			$h1 += preg_match_all( '/<h1[\s>]/i', (string) ( $attrs['html'] ?? '' ) );
			if ( bst_aid_text_has_layout( (string) ( $attrs['html'] ?? '' ) ) ) {
				$layout_tx[] = mb_substr( (string) $attrs['html'], 0, 80 );
			}
		}
		if ( 'bst/code' !== $name ) {
			$texts[] = bst_eval_plain( bst_eval_attr_text( $attrs ) );
		}
		$texts[] = bst_eval_plain( $own_html );
		if ( 'bst/form' === $name ) {
			$forms[] = $attrs;
		}
		if ( 'bst/testimonial' === $name ) {
			$reviews++;
		}
		if ( 'bst/counter' === $name ) {
			$counters[] = (string) ( $attrs['prefix'] ?? '' ) . (string) ( $attrs['number'] ?? '' ) . (string) ( $attrs['suffix'] ?? '' ) . ' ' . (string) ( $attrs['title'] ?? $attrs['label'] ?? '' );
		}
	}
);
$text     = implode( "\n", array_filter( $texts, 'strlen' ) );
$sections = count(
	array_filter(
		$blocks,
		function ( $b ) {
			return $b['blockName'] && ! in_array( $b['blockName'], array( 'bst/spacer', 'core/spacer', 'bst/divider', 'core/separator' ), true );
		}
	)
);

$editable   = bst_aid_editable_percent( $content );
$rule_error = bst_aid_block_rule_error( $content, '' );
$code       = bst_aid_code_usage( $blocks );

/* Invented facts: every claim must come from the case facts. */
$allowed_vals = array_merge( bst_eval_allowed( $facts['prices'] ?? array() ), bst_eval_allowed( $facts['numbers'] ?? array() ) );
$claims       = bst_eval_claims( $text . "\n" . implode( "\n", $counters ) );
$invented     = array();
$demo_text = ! empty( $facts['demo_prices_ok'] ) && preg_match( '/\b(demo|giá mẫu|minh họa)\b/iu', $text );
foreach ( $claims as $c ) {
	if ( 'money' === $c['kind'] && $demo_text ) {
		continue;
	}
	if ( null === $c['value'] || ! in_array( $c['value'], $allowed_vals, false ) ) {
		$invented[ $c['kind'] . '|' . $c['value'] ] = $c['kind'] . ': ' . preg_replace( '/\s+/u', ' ', $c['text'] );
	}
}
$invented = array_values( array_unique( $invented ) );
if ( $reviews && empty( $facts['reviews'] ) ) {
	$invented[] = "review: $reviews testimonial block(s) but the brief gives no reviews";
}
preg_match_all( '/\[[^\]\n]{2,40}\]/u', $text, $ph );

/* Forms: schema and pricing through the theme rules. */
$form_report = array();
foreach ( $forms as $i => $a ) {
	$raw_fields = (array) ( $a['fields'] ?? array() );
	$fields     = bst_form_schema_normalize( $raw_fields );
	$errors     = array();
	$types      = bst_native_field_types();
	foreach ( $fields as $f ) {
		if ( ! in_array( $f['type'] ?? 'text', $types, true ) ) {
			$errors[] = 'unknown field type ' . ( $f['type'] ?? '' ) . ' (' . $f['name'] . ')';
		}
	}
	if ( count( $fields ) < count( $raw_fields ) ) {
		$errors[] = ( count( $raw_fields ) - count( $fields ) ) . ' field(s) without a name were dropped';
	}
	foreach ( (array) bst_form_schema_errors( $fields ) as $e ) {
		$errors[] = is_string( $e ) ? $e : wp_json_encode( $e );
	}
	$rep = array(
		'provider'   => (string) ( $a['provider'] ?? '' ),
		'fields'     => count( $fields ),
		'types'      => array_values( array_unique( array_map( function ( $f ) { return $f['type'] ?? 'text'; }, $fields ) ) ),
		'pricing'    => false,
		'demo'       => false,
		'errors'     => $errors,
		'price_vals' => array(),
		'quotes'     => array(),
	);
	$p = bst_pricing_settings( $a['pricing'] ?? null );
	if ( $p ) {
		$rep['pricing'] = true;
		$known          = array_merge( array_column( $fields, 'id' ), array_column( $p['lines'], 'id' ) );
		$rep['demo']    = count( array_filter( $p['lines'], function ( $l ) { return false !== stripos( $l['label'], 'demo' ); } ) ) === count( $p['lines'] );
		foreach ( $p['lines'] as $l ) {
			foreach ( array( 'field', 'field2', 'qtyField' ) as $fk ) {
				if ( ! empty( $l[ $fk ] ) && ! in_array( $l[ $fk ], array_column( $fields, 'id' ), true ) ) {
					$rep['errors'][] = "line {$l['id']}: $fk \"{$l[ $fk ]}\" is not a field id";
				}
			}
			if ( 'formula' === $l['kind'] ) {
				$chk = bst_pricing_check_expr( (string) ( $l['expr'] ?? '' ), $known );
				if ( '' !== $chk ) {
					$rep['errors'][] = "line {$l['id']}: formula $chk";
				}
			}
			foreach ( array( 'amount', 'unit' ) as $k ) {
				if ( isset( $l[ $k ] ) && '' !== (string) $l[ $k ] ) {
					$rep['price_vals'][] = (float) $l[ $k ];
				}
			}
			foreach ( (array) ( $l['prices'] ?? array() ) as $v ) {
				$rep['price_vals'][] = (float) bst_pricing_num( $v );
			}
			foreach ( (array) ( $l['tiers'] ?? array() ) as $tier ) {
				$rep['price_vals'][] = (float) ( $tier['unit'] ?? 0 );
			}
			if ( 'percent' === $l['kind'] ) {
				$rep['price_vals'][] = (float) ( $l['pct'] ?? 0 );
			}
		}
		if ( $p['minTotal'] > 0 ) {
			$rep['price_vals'][] = (float) $p['minTotal'];
		}
		foreach ( (array) ( $expect['form']['quotes'] ?? array() ) as $qc ) {
			$values = bst_eval_quote_values( (array) $qc['values'], $fields, $unmatched );
			if ( $unmatched ) {
				$rep['quotes'][] = array( 'values' => $qc['values'], 'want' => $qc['total'], 'got' => null, 'status' => 'no field/option for ' . implode( ', ', $unmatched ), 'pass' => false );
				continue;
			}
			$q               = bst_pricing_quote( $p, $fields, $values );
			$got             = round( $q['total'] / ( 10 ** $p['decimals'] ), $p['decimals'] );
			$rep['quotes'][] = array( 'values' => $qc['values'], 'want' => $qc['total'], 'got' => $got, 'status' => $q['status'], 'pass' => 'ok' === $q['status'] && abs( $got - (float) $qc['total'] ) < 0.001 );
		}
	} elseif ( ! empty( $a['pricing']['enabled'] ) ) {
		$rep['errors'][] = 'pricing enabled but no valid line';
	}
	$form_report[] = $rep;
}
foreach ( $form_report as $rep ) {
	foreach ( $rep['price_vals'] as $v ) {
		if ( $v && ! in_array( round( $v, 3 ), $allowed_vals, false ) && ! ( $rep['demo'] && ! empty( $facts['demo_prices_ok'] ) ) ) {
			$invented[] = 'form price: ' . $v;
		}
	}
}

/* Tool log: calls, server refusals, approvals. */
$log        = (array) ( $result['log'] ?? array() );
$tool_calls = 0;
$rejections = array();
$approvals  = 0;
foreach ( $log as $e ) {
	$k = $e['kind'] ?? '';
	if ( in_array( $k, array( 'tool', 'tool-error', 'approval' ), true ) && empty( $e['approval_id'] ) ) {
		$tool_calls++;
	}
	if ( 'approval' === $k ) {
		$approvals++;
	}
	if ( 'tool-error' === $k ) {
		$rejections[] = ( $e['tool'] ?? '?' ) . ': ' . mb_substr( (string) ( $e['text'] ?? '' ), 0, 120 );
	}
}
$metrics = (array) ( $result['metrics'] ?? array() );

/* --------------------------------------------------------------- Checks */

$checks = array();
$check  = function ( $id, $pass, $detail = '', $weight = 1, $hard = false ) use ( &$checks ) {
	$checks[] = array( 'id' => $id, 'pass' => (bool) $pass, 'weight' => $weight, 'hard' => $hard, 'detail' => $detail );
};
$names_all = array_keys( $names );
$check( 'page_saved', '' !== trim( $content ) && ! empty( $names ), empty( $names ) ? 'no blocks' : count( $names_all ) . ' block types', 2, true );
$check( 'known_blocks_only', ! $unknown && 0 === $freeform, implode( ', ', array_keys( $unknown ) ) . ( $freeform ? " $freeform freeform HTML chunk(s)" : '' ), 2, true );
$check( 'no_core_html', empty( $names['core/html'] ), empty( $names['core/html'] ) ? '' : $names['core/html'] . ' core/html', 1 );
$check( 'editable_percent>=90', $editable >= 90, "$editable%", 2, true );
$check( 'block_rule', '' === $rule_error, $rule_error, 2, true );
$check( 'text_no_layout_html', ! $layout_tx, implode( ' | ', $layout_tx ), 1 );
$check( 'single_h1', 1 === $h1, "$h1 h1", 1 );
$need_all = (array) ( $expect['blocks_all'] ?? array() );
$miss     = array_values( array_diff( $need_all, $names_all ) );
$check( 'required_blocks', ! $miss, $miss ? 'missing ' . implode( ', ', $miss ) : '', 2 );
$any_miss = array();
foreach ( (array) ( $expect['blocks_any'] ?? array() ) as $group ) {
	if ( ! array_intersect( $group, $names_all ) ) {
		$any_miss[] = implode( '|', $group );
	}
}
if ( ! empty( $expect['blocks_any'] ) ) {
	$check( 'section_blocks', ! $any_miss, $any_miss ? 'none of ' . implode( '; ', $any_miss ) : '', 1 );
}
$forbid = array_values( array_intersect( (array) ( $expect['blocks_forbid'] ?? array() ), $names_all ) );
$check( 'no_forbidden_blocks', ! $forbid, implode( ', ', $forbid ), 1 );
$smin = (int) ( $expect['sections_min'] ?? 1 );
$smax = (int) ( $expect['sections_max'] ?? 14 );
$check( 'section_count', $sections >= $smin && $sections <= $smax, "$sections (want {$smin}–{$smax})", 1 );
$lower = mb_strtolower( $text );
$absent = array_values( array_filter( (array) ( $expect['must_contain'] ?? array() ), function ( $s ) use ( $lower ) { return false === mb_strpos( $lower, mb_strtolower( $s ) ); } ) );
if ( ! empty( $expect['must_contain'] ) ) {
	$check( 'given_facts_used', ! $absent, $absent ? 'missing ' . implode( ', ', $absent ) : '', 1 );
}
$check( 'no_invented_facts', ! $invented, implode( '; ', array_slice( $invented, 0, 8 ) ) . ( count( $invented ) > 8 ? ' …' : '' ), 3, true );

$fx = (array) ( $expect['form'] ?? array() );
if ( ! empty( $fx['required'] ) ) {
	$f0 = $form_report[0] ?? null;
	$check( 'form_present', (bool) $f0, $f0 ? $f0['fields'] . ' fields' : 'no bst/form', 2 );
	$ok = $f0 && 'bigspy' === $f0['provider'] && ! $f0['errors'] && $f0['fields'] >= (int) ( $fx['fields_min'] ?? 1 );
	$check( 'form_valid', $ok, ! $f0 ? 'no bst/form' : trim( ( 'bigspy' !== $f0['provider'] ? 'provider ' . $f0['provider'] . '; ' : '' ) . implode( '; ', $f0['errors'] ) . ( $f0['fields'] < (int) ( $fx['fields_min'] ?? 1 ) ? '; fewer than ' . $fx['fields_min'] . ' fields' : '' ), '; ' ), 2, true );
	$tmiss = $f0 ? array_values( array_diff( (array) ( $fx['field_types'] ?? array() ), $f0['types'] ) ) : (array) ( $fx['field_types'] ?? array() );
	if ( ! empty( $fx['field_types'] ) ) {
		$check( 'form_field_types', ! $tmiss, $tmiss ? 'missing ' . implode( ', ', $tmiss ) : '', 1 );
	}
	if ( array_key_exists( 'pricing', $fx ) ) {
		$want = (bool) $fx['pricing'];
		$check( 'form_pricing_' . ( $want ? 'on' : 'off' ), $f0 && $f0['pricing'] === $want, $f0 && $f0['pricing'] ? 'pricing on' : 'pricing off', 2 );
	}
	if ( ! empty( $fx['demo'] ) ) {
		$check( 'form_demo_marked', $f0 && $f0['demo'], $f0 && $f0['demo'] ? '' : 'pricing lines not marked demo', 2 );
	}
	if ( ! empty( $fx['quotes'] ) ) {
		$qs = $f0 ? $f0['quotes'] : array();
		$bad = array_filter( $qs, function ( $q ) { return ! $q['pass']; } );
		$check( 'form_quotes', $qs && ! $bad, $qs ? implode( '; ', array_map( function ( $q ) { return 'want ' . $q['want'] . ' got ' . $q['got'] . ' (' . $q['status'] . ')'; }, $bad ) ) : 'no pricing to quote', 2 );
	}
}
$rej_max = (int) ( $limits['rejections_max'] ?? 2 );
$check( 'server_rejections', count( $rejections ) <= $rej_max, count( $rejections ) . " (max $rej_max)", 1 );
if ( isset( $limits['tool_calls_max'] ) ) {
	$tc = (int) ( $metrics['tool_calls'] ?? $tool_calls );
	$check( 'tool_calls', $tc <= (int) $limits['tool_calls_max'], "$tc (max {$limits['tool_calls_max']})", 1 );
}
if ( isset( $limits['seconds_max'], $metrics['seconds'] ) ) {
	$check( 'time', $metrics['seconds'] <= $limits['seconds_max'], $metrics['seconds'] . "s (max {$limits['seconds_max']}s)", 1 );
}
if ( isset( $limits['cost_max'], $metrics['cost'] ) ) {
	$check( 'cost', $metrics['cost'] <= $limits['cost_max'], '$' . $metrics['cost'] . ' (max $' . $limits['cost_max'] . ')', 1 );
}

/* Rendered page for the 375px check. */
$render = array();
if ( $html_o ) {
	if ( ! empty( $result['rendered_html'] ) ) {
		$body             = (string) $result['rendered_html'];
		$render['source'] = 'live';
	} else {
		$stats            = array();
		$body             = bst_eval_render_blocks( $blocks, $stats );
		$render           = array( 'source' => 'isolated', 'rendered' => $stats['rendered'] ?? 0, 'static' => $stats['static'] ?? 0, 'failed' => $stats['failed'] ?? array(), 'stubbed' => array_keys( $GLOBALS['bst_eval_stub_calls'] ) );
	}
	file_put_contents( $html_o, bst_eval_page_html( $body, $case['id'] ) );
	$render['html'] = $html_o;
}

echo wp_json_encode(
	array(
		'case'     => $case['id'],
		'checks'   => $checks,
		'measures' => array(
			'blocks'           => array_sum( $names ),
			'block_types'      => $names,
			'sections'         => $sections,
			'editable_percent' => $editable,
			'code_blocks'      => $code['count'],
			'code_bytes'       => $code['bytes'],
			'h1'               => $h1,
			'claims'           => count( $claims ),
			'invented'         => $invented,
			'placeholders'     => array_values( array_unique( $ph[0] ) ),
			'forms'            => $form_report,
			'tool_calls'       => (int) ( $metrics['tool_calls'] ?? $tool_calls ),
			'model_calls'      => $metrics['model_calls'] ?? null,
			'rejections'       => $rejections,
			'approvals'        => $approvals,
			'tokens_in'        => $metrics['tokens_in'] ?? null,
			'tokens_out'       => $metrics['tokens_out'] ?? null,
			'seconds'          => $metrics['seconds'] ?? null,
			'cost'             => $metrics['cost'] ?? null,
			'tools'            => $metrics['tools'] ?? null,
		),
		'render'   => $render,
	),
	JSON_PRETTY_PRINT
) . "\n";
