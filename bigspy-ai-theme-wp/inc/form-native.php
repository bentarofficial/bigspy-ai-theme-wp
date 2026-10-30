<?php
/**
 * Built-in form engine for the Form block (provider "bigspy"): no form plugin needed.
 *
 * Mirrors Contact Form 7 + CF7 Redirect: fields, Mail (To / From / Subject /
 * Additional headers / Message body with [tags]), Mail (2) auto-reply, Messages,
 * redirect actions, plus saved entries. Mail goes through wp_mail(), so WP Mail
 * SMTP (or any SMTP plugin) delivers it.
 *
 * The browser only sends the form key and the field values; recipients and
 * templates are always read again from the saved block on the server.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** Default settings of a new built-in form. */
function bst_native_form_defaults() {
	return array(
		'fields'           => array(
			array( 'type' => 'text', 'name' => 'your-name', 'label' => 'Họ và tên', 'placeholder' => 'Họ và tên *', 'required' => true, 'width' => 'half' ),
			array( 'type' => 'tel', 'name' => 'your-phone', 'label' => 'Số điện thoại', 'placeholder' => 'Số điện thoại *', 'required' => true, 'width' => 'half' ),
			array( 'type' => 'email', 'name' => 'your-email', 'label' => 'Email', 'placeholder' => 'Email', 'required' => false, 'width' => 'full' ),
			array( 'type' => 'textarea', 'name' => 'your-message', 'label' => 'Nội dung', 'placeholder' => 'Nội dung cần tư vấn', 'required' => false, 'width' => 'full' ),
		),
		'submitText'       => 'Gửi yêu cầu',
		'mailTo'           => '[_site_admin_email]',
		'mailFrom'         => '[_site_title] <wordpress@[_site_domain]>',
		'mailSubject'      => '[_site_title] – Liên hệ mới từ [your-name]',
		'mailHeaders'      => 'Reply-To: [your-email]',
		'mailBody'         => "Bạn nhận được một liên hệ mới từ website [_site_title]:\n\n[all-fields]\n\n--\nTrang gửi: [_url]\nThời gian: [_date] [_time]\nIP: [_remote_ip]",
		'mailExcludeBlank' => true,
		'mailHtml'         => false,
		'mail2Active'      => false,
		'mail2To'          => '[your-email]',
		'mail2From'        => '[_site_title] <wordpress@[_site_domain]>',
		'mail2Subject'     => 'Cảm ơn bạn đã liên hệ [_site_title]',
		'mail2Headers'     => 'Reply-To: [_site_admin_email]',
		'mail2Body'        => "Chào [your-name],\n\nCảm ơn bạn đã liên hệ. Chúng tôi đã nhận được thông tin và sẽ phản hồi sớm nhất.\n\n--\n[_site_title]\n[_site_url]",
		'msgSuccess'       => 'Cảm ơn bạn! Thông tin đã được gửi thành công.',
		'msgError'         => 'Có lỗi khi gửi. Vui lòng thử lại hoặc gọi trực tiếp cho chúng tôi.',
		'msgInvalid'       => 'Một hoặc nhiều trường chưa đúng. Vui lòng kiểm tra lại.',
		'msgRequired'      => 'Vui lòng điền trường này.',
		'msgEmail'         => 'Email chưa đúng định dạng.',
		'msgTel'           => 'Số điện thoại chưa đúng.',
		'msgSpam'          => 'Gửi quá nhanh, vui lòng thử lại sau ít giây.',
		'redirectPass'     => false,
		'redirectNewTab'   => false,
		'saveEntries'      => true,
		'formName'         => '',
	);
}

/** Settings of one form block = defaults + saved attributes. */
function bst_native_form_settings( $a ) {
	$d = bst_native_form_defaults();
	foreach ( $d as $k => $v ) {
		if ( isset( $a[ $k ] ) && '' !== $a[ $k ] && null !== $a[ $k ] ) {
			$d[ $k ] = $a[ $k ];
		}
	}
	$d['fields'] = array_values( array_filter( (array) $d['fields'], function ( $f ) {
		return is_array( $f ) && ! empty( $f['name'] );
	} ) );
	return $d;
}

/** Field types the engine renders. */
function bst_native_field_types() {
	return array( 'text', 'email', 'tel', 'url', 'number', 'date', 'textarea', 'select', 'radio', 'checkbox', 'acceptance', 'file' );
}

/** Choices of a select, radio or checkbox field. */
function bst_native_field_options( $f ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n|,/', (string) ( $f['options'] ?? '' ) ) ) ) );
}

/** File extensions a file field accepts: its own list, limited to safe types. */
function bst_native_file_types( $f ) {
	$safe = array( 'jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt' );
	$own  = array_map( 'strtolower', preg_split( '/[\s,|]+/', (string) ( $f['options'] ?? '' ), -1, PREG_SPLIT_NO_EMPTY ) );
	return $own ? array_values( array_intersect( $own, $safe ) ) : $safe;
}

/** Check a file by size, extension and real content. Returns a safe name, or '' when refused. */
function bst_native_file_check( $f, $path, $name, $size ) {
	if ( $size <= 0 || $size > 5 * MB_IN_BYTES ) {
		return '';
	}
	$name  = sanitize_file_name( $name );
	$check = wp_check_filetype_and_ext( $path, $name );
	$ext   = strtolower( (string) ( $check['ext'] ?: pathinfo( $name, PATHINFO_EXTENSION ) ) );
	return $check['ext'] && $check['type'] && in_array( $ext, bst_native_file_types( $f ), true ) ? $name : '';
}

/** Server check of one value by field type. Returns the message, or '' when it is valid. */
function bst_native_field_error( $f, $v, $s ) {
	$type = $f['type'] ?? 'text';
	if ( ! empty( $f['required'] ) && '' === $v ) {
		return $s['msgRequired'];
	}
	if ( '' === $v ) {
		return '';
	}
	if ( 'email' === $type && ! is_email( $v ) ) {
		return $s['msgEmail'];
	}
	if ( 'tel' === $type && ! preg_match( '/^[+0-9 ().-]{8,20}$/', $v ) ) {
		return $s['msgTel'];
	}
	$ok = true;
	if ( 'url' === $type ) {
		$ok = (bool) wp_http_validate_url( $v );
	} elseif ( 'number' === $type ) {
		$ok = is_numeric( $v );
	} elseif ( 'date' === $type ) {
		$ok = (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v );
	} elseif ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) && bst_native_field_options( $f ) ) {
		$ok = ! array_diff( explode( ', ', $v ), bst_native_field_options( $f ) );
	}
	return $ok ? '' : $s['msgInvalid'];
}

/** Front-end markup (same classes as Contact Form 7, so every Form block look applies). */
function bst_native_form_markup( $a ) {
	$s   = bst_native_form_settings( $a );
	$key = sanitize_key( $a['formKey'] ?? '' );
	if ( ! $key ) {
		return '';
	}
	$out = '';
	foreach ( $s['fields'] as $f ) {
		$type  = in_array( $f['type'] ?? 'text', bst_native_field_types(), true ) ? $f['type'] : 'text';
		$name  = sanitize_key( $f['name'] );
		$label = (string) ( $f['label'] ?? '' );
		$ph    = (string) ( $f['placeholder'] ?? '' );
		$req   = ! empty( $f['required'] );
		$attr  = ' name="' . esc_attr( $name ) . '"' . ( $req ? ' required aria-required="true"' : '' );
		$width = 'half' === ( $f['width'] ?? 'full' ) ? ' bs-nf-half' : '';
		$opts  = bst_native_field_options( $f );
		$lab   = $label ? '<span class="bs-nf-label">' . esc_html( $label ) . ( $req ? ' <span class="bs-nf-req">*</span>' : '' ) . '</span>' : '';
		if ( 'file' === $type ) {
			$accept  = '.' . implode( ',.', bst_native_file_types( $f ) );
			$control = '<input type="file" class="wpcf7-form-control wpcf7-file"' . $attr . ' accept="' . esc_attr( $accept ) . '">';
			$out    .= '<p class="bs-nf-field bs-nf-file' . $width . '"><label>' . $lab . '<span class="wpcf7-form-control-wrap" data-name="' . esc_attr( $name ) . '">' . $control . '</span></label></p>';
			continue;
		}
		switch ( $type ) {
			case 'textarea':
				$control = '<textarea class="wpcf7-form-control wpcf7-textarea"' . $attr . ' rows="5" maxlength="5000" placeholder="' . esc_attr( $ph ) . '"></textarea>';
				break;
			case 'select':
				$control = '<select class="wpcf7-form-control wpcf7-select"' . $attr . '>' . ( $ph ? '<option value="">' . esc_html( $ph ) . '</option>' : '' );
				foreach ( $opts as $o ) {
					$control .= '<option value="' . esc_attr( $o ) . '">' . esc_html( $o ) . '</option>';
				}
				$control .= '</select>';
				break;
			case 'radio':
			case 'checkbox':
				$control = '<span class="wpcf7-form-control wpcf7-' . $type . '">';
				foreach ( $opts as $i => $o ) {
					$control .= '<span class="wpcf7-list-item"><label><input type="' . $type . '" name="' . esc_attr( $name ) . ( 'checkbox' === $type ? '[]' : '' ) . '" value="' . esc_attr( $o ) . '"' . ( 'radio' === $type && 0 === $i ? ' checked' : '' ) . '> <span class="wpcf7-list-item-label">' . esc_html( $o ) . '</span></label></span>';
				}
				$control .= '</span>';
				break;
			case 'acceptance':
				$control = '<span class="wpcf7-form-control wpcf7-acceptance"><span class="wpcf7-list-item"><label><input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . ( $req ? ' required' : '' ) . '> <span class="wpcf7-list-item-label">' . wp_kses_post( $ph ? $ph : $label ) . '</span></label></span></span>';
				$lab     = '';
				break;
			default:
				$control = '<input type="' . esc_attr( $type ) . '" class="wpcf7-form-control wpcf7-' . esc_attr( $type ) . '"' . $attr . ' maxlength="400" placeholder="' . esc_attr( $ph ) . '"' . ( 'tel' === $type ? ' inputmode="tel" autocomplete="tel"' : ( 'email' === $type ? ' autocomplete="email"' : '' ) ) . '>';
		}
		$out .= '<p class="bs-nf-field bs-nf-' . esc_attr( $type ) . $width . '"><label>' . $lab . '<span class="wpcf7-form-control-wrap" data-name="' . esc_attr( $name ) . '">' . $control . '</span></label></p>';
	}
	$out .= '<p class="bs-nf-hp" aria-hidden="true"><label>Website<input type="text" name="bst_hp" tabindex="-1" autocomplete="off"></label></p>';
	$out .= '<p class="bs-nf-submit"><button type="submit" class="wpcf7-form-control wpcf7-submit">' . esc_html( $s['submitText'] ) . '</button><span class="wpcf7-spinner"></span></p>';
	$out .= '<div class="wpcf7-response-output" aria-live="polite" hidden></div>';
	return '<form class="wpcf7-form bs-native-form" method="post" action="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" novalidate data-bs-native-form data-key="' . esc_attr( $key ) . '" data-post="' . esc_attr( (string) get_queried_object_id() ) . '" data-ts="' . esc_attr( (string) time() ) . '" data-msg-required="' . esc_attr( $s['msgRequired'] ) . '" data-msg-email="' . esc_attr( $s['msgEmail'] ) . '" data-msg-tel="' . esc_attr( $s['msgTel'] ) . '"' . ( $s['redirectNewTab'] ? ' data-new-tab="1"' : '' ) . '>' . $out . '</form>';
}

/** Find the saved Form block with this key (the page shown, then any post that contains it). */
function bst_native_form_find( $key, $post_id = 0 ) {
	$key = sanitize_key( $key );
	if ( ! $key ) {
		return null;
	}
	$search = function ( $blocks ) use ( &$search, $key ) {
		foreach ( $blocks as $b ) {
			if ( 'bst/form' === ( $b['blockName'] ?? '' ) && $key === ( $b['attrs']['formKey'] ?? '' ) ) {
				return $b['attrs'];
			}
			if ( ! empty( $b['innerBlocks'] ) ) {
				$hit = $search( $b['innerBlocks'] );
				if ( $hit ) {
					return $hit;
				}
			}
		}
		return null;
	};
	$ids = array();
	if ( $post_id ) {
		$ids[] = (int) $post_id;
	}
	global $wpdb;
	$like = '%' . $wpdb->esc_like( '"formKey":"' . $key . '"' ) . '%';
	$ids  = array_merge( $ids, array_map( 'intval', (array) $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_content LIKE %s LIMIT 5", $like ) ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	foreach ( array_unique( $ids ) as $id ) {
		$post = get_post( $id );
		if ( $post && 'publish' === $post->post_status && false !== strpos( $post->post_content, $key ) ) {
			$hit = $search( parse_blocks( $post->post_content ) );
			if ( $hit ) {
				$hit['__post'] = $id;
				return $hit;
			}
		}
	}
	return null;
}

/** Replace [tags] in a mail template. */
function bst_native_form_tags( $text, $values, $fields, $exclude_blank, $html = false ) {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$all  = array();
	foreach ( $fields as $f ) {
		$n = sanitize_key( $f['name'] );
		$v = $values[ $n ] ?? '';
		if ( '' === $v && $exclude_blank ) {
			continue;
		}
		$label = ! empty( $f['label'] ) ? $f['label'] : $n;
		$all[] = $html ? '<p><strong>' . esc_html( $label ) . ':</strong><br>' . nl2br( esc_html( $v ) ) . '</p>' : $label . ': ' . $v;
	}
	$map = array(
		'[all-fields]'         => implode( $html ? '' : "\n", $all ),
		'[_site_title]'        => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		'[_site_url]'          => home_url( '/' ),
		'[_site_domain]'       => preg_replace( '/^www\./', '', (string) $host ),
		'[_site_admin_email]'  => get_option( 'admin_email' ),
		'[_date]'              => wp_date( get_option( 'date_format' ) ),
		'[_time]'              => wp_date( get_option( 'time_format' ) ),
		'[_url]'               => esc_url_raw( wp_get_referer() ? wp_get_referer() : home_url( '/' ) ),
		'[_remote_ip]'         => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
		'[_user_agent]'        => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
	);
	foreach ( $values as $n => $v ) {
		$map[ '[' . $n . ']' ] = $html ? esc_html( $v ) : $v;
	}
	$text = strtr( (string) $text, $map );
	return preg_replace( '/\[[a-z0-9_-]+\]/i', '', $text );
}

/** One email (Mail or Mail 2). Returns true when wp_mail() accepted it. */
function bst_native_form_send( $prefix, $s, $values, $files = array() ) {
	$fields  = $s['fields'];
	$exclude = ! empty( $s['mailExcludeBlank'] );
	$html    = ! empty( $s['mailHtml'] );
	$to      = bst_native_form_tags( $s[ $prefix . 'To' ], $values, $fields, $exclude );
	$to      = array_values( array_filter( array_map( 'trim', explode( ',', $to ) ), 'is_email' ) );
	if ( ! $to ) {
		return false;
	}
	$subject = wp_strip_all_tags( bst_native_form_tags( $s[ $prefix . 'Subject' ], $values, $fields, $exclude ) );
	$body    = bst_native_form_tags( $s[ $prefix . 'Body' ], $values, $fields, $exclude, $html );
	$headers = array();
	$from    = trim( bst_native_form_tags( $s[ $prefix . 'From' ], $values, $fields, $exclude ) );
	if ( $from ) {
		$headers[] = 'From: ' . str_replace( array( "\r", "\n" ), '', $from );
	}
	foreach ( preg_split( '/\r\n|\r|\n/', bst_native_form_tags( $s[ $prefix . 'Headers' ], $values, $fields, $exclude ) ) as $line ) {
		$line = trim( $line );
		// Only well-formed headers with a value (Reply-To with an empty tag is dropped).
		if ( preg_match( '/^(Reply-To|Cc|Bcc):\s*(.+)$/i', $line, $m ) && is_email( preg_replace( '/^.*<|>.*$/', '', trim( $m[2] ) ) ) ) {
			$headers[] = $m[1] . ': ' . trim( $m[2] );
		}
	}
	if ( $html ) {
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$body      = wpautop( $body );
	}
	return wp_mail( $to, $subject, $body, $headers, $files );
}

/** AJAX submit handler (logged in and visitors). */
function bst_native_form_submit() {
	$key  = isset( $_POST['bst_form_key'] ) ? sanitize_key( wp_unslash( $_POST['bst_form_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form, protected by honeypot, timing and rate limit (page caches break nonces).
	$post = isset( $_POST['bst_form_post'] ) ? absint( $_POST['bst_form_post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$a    = bst_native_form_find( $key, $post );
	if ( ! $a ) {
		wp_send_json( array( 'ok' => false, 'message' => __( 'Form not found.', 'bigspy-ai-theme-wp' ) ), 404 );
	}
	$s = bst_native_form_settings( $a );

	// Spam guards: honeypot, minimum fill time, 5 sends per minute per IP.
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$hp   = isset( $_POST['bst_hp'] ) ? trim( (string) wp_unslash( $_POST['bst_hp'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$ts   = isset( $_POST['bst_form_ts'] ) ? absint( $_POST['bst_form_ts'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$rate = 'bst_nf_' . md5( $ip );
	$hits = (int) get_transient( $rate );
	if ( '' !== $hp || ( $ts && time() - $ts < 3 ) || $hits >= 5 ) {
		wp_send_json( array( 'ok' => false, 'message' => $s['msgSpam'] ) );
	}
	set_transient( $rate, $hits + 1, MINUTE_IN_SECONDS );

	$values = array();
	$errors = array();
	$files  = array(); // Name => PHP temp path: mailed as attachments, never stored on the site.
	foreach ( $s['fields'] as $f ) {
		$n    = sanitize_key( $f['name'] );
		$type = $f['type'] ?? 'text';
		if ( 'file' === $type ) {
			$up = $_FILES[ $n ] ?? null; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput
			$v  = '';
			if ( $up && UPLOAD_ERR_NO_FILE !== (int) $up['error'] ) {
				$v = UPLOAD_ERR_OK === (int) $up['error'] && is_uploaded_file( $up['tmp_name'] ) ? bst_native_file_check( $f, $up['tmp_name'], $up['name'], (int) $up['size'] ) : '';
				if ( '' === $v ) {
					$errors[ $n ] = $s['msgInvalid'];
					continue;
				}
				$files[ $v ] = $up['tmp_name'];
			}
			$values[ $n ] = $v;
			if ( ! empty( $f['required'] ) && '' === $v ) {
				$errors[ $n ] = $s['msgRequired'];
			}
			continue;
		}
		$raw  = $_POST[ $n ] ?? ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $raw ) ) {
			$v = implode( ', ', array_map( 'sanitize_text_field', wp_unslash( $raw ) ) );
		} else {
			$v = 'textarea' === $type ? sanitize_textarea_field( wp_unslash( $raw ) ) : sanitize_text_field( wp_unslash( $raw ) );
		}
		$v = mb_substr( $v, 0, 'textarea' === $type ? 5000 : 400 );
		if ( 'acceptance' === $type ) {
			$v = '' !== $v ? __( 'Yes', 'bigspy-ai-theme-wp' ) : '';
		}
		$error = bst_native_field_error( $f, $v, $s );
		if ( '' !== $error ) {
			$errors[ $n ] = $error;
		}
		$values[ $n ] = $v;
	}
	if ( $errors ) {
		wp_send_json( array( 'ok' => false, 'message' => $s['msgInvalid'], 'errors' => $errors ) );
	}

	$sent = bst_native_form_send( 'mail', $s, $values, $files );
	if ( $sent && ! empty( $s['mail2Active'] ) ) {
		bst_native_form_send( 'mail2', $s, $values );
	}
	if ( ! empty( $s['saveEntries'] ) ) {
		bst_native_form_save_entry( $s, $values, (int) ( $a['__post'] ?? 0 ), $sent );
	}
	if ( ! $sent ) {
		wp_send_json( array( 'ok' => false, 'message' => $s['msgError'] ) );
	}
	$redirect = esc_url_raw( (string) ( $a['redirectUrl'] ?? '' ) );
	if ( $redirect && ! empty( $s['redirectPass'] ) ) {
		$redirect = add_query_arg( array_map( 'rawurlencode', $values ), $redirect );
	}
	wp_send_json( array( 'ok' => true, 'message' => $s['msgSuccess'], 'redirect' => $redirect ) );
}
add_action( 'wp_ajax_bst_form_submit', 'bst_native_form_submit' );
add_action( 'wp_ajax_nopriv_bst_form_submit', 'bst_native_form_submit' );

/* ------------------------------------------------------------------ Entries */

add_action(
	'init',
	function () {
		register_post_type(
			'bst_form_entry',
			array(
				'labels'          => array(
					'name'          => __( 'Form entries', 'bigspy-ai-theme-wp' ),
					'singular_name' => __( 'Form entry', 'bigspy-ai-theme-wp' ),
					'menu_name'     => __( 'Form entries', 'bigspy-ai-theme-wp' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 26,
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'supports'        => array( 'title' ),
			)
		);
	}
);

function bst_native_form_save_entry( $s, $values, $post_id, $sent ) {
	$first = '';
	foreach ( $values as $v ) {
		if ( '' !== $v ) {
			$first = $v;
			break;
		}
	}
	$id = wp_insert_post(
		array(
			'post_type'   => 'bst_form_entry',
			'post_status' => 'private',
			'post_title'  => wp_trim_words( ( $s['formName'] ? $s['formName'] . ' – ' : '' ) . $first, 12, '…' ),
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_bst_values', $values );
		update_post_meta( $id, '_bst_fields', wp_list_pluck( $s['fields'], 'label', 'name' ) );
		update_post_meta( $id, '_bst_source', $post_id );
		update_post_meta( $id, '_bst_sent', $sent ? 1 : 0 );
	}
}

add_action(
	'add_meta_boxes_bst_form_entry',
	function ( $post ) {
		add_meta_box(
			'bst_entry_values',
			__( 'Submitted values', 'bigspy-ai-theme-wp' ),
			function ( $post ) {
				$values = (array) get_post_meta( $post->ID, '_bst_values', true );
				$labels = (array) get_post_meta( $post->ID, '_bst_fields', true );
				$source = (int) get_post_meta( $post->ID, '_bst_source', true );
				echo '<table class="widefat striped"><tbody>';
				foreach ( $values as $n => $v ) {
					echo '<tr><th style="width:30%">' . esc_html( $labels[ $n ] ?? $n ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
				}
				echo '</tbody></table><p>' . esc_html__( 'Email sent:', 'bigspy-ai-theme-wp' ) . ' <strong>' . ( get_post_meta( $post->ID, '_bst_sent', true ) ? esc_html__( 'yes', 'bigspy-ai-theme-wp' ) : esc_html__( 'no – check the SMTP settings', 'bigspy-ai-theme-wp' ) ) . '</strong>';
				if ( $source ) {
					echo ' · ' . esc_html__( 'Form on:', 'bigspy-ai-theme-wp' ) . ' <a href="' . esc_url( get_permalink( $source ) ) . '" target="_blank">' . esc_html( get_the_title( $source ) ) . '</a>';
				}
				echo '</p>';
			},
			'bst_form_entry',
			'normal',
			'high'
		);
	}
);

add_filter(
	'manage_bst_form_entry_posts_columns',
	function ( $cols ) {
		return array( 'cb' => $cols['cb'], 'title' => __( 'Entry', 'bigspy-ai-theme-wp' ), 'bst_sent' => __( 'Email sent', 'bigspy-ai-theme-wp' ), 'date' => $cols['date'] );
	}
);
add_action(
	'manage_bst_form_entry_posts_custom_column',
	function ( $col, $id ) {
		if ( 'bst_sent' === $col ) {
			echo get_post_meta( $id, '_bst_sent', true ) ? '✅' : '⚠️';
		}
	},
	10,
	2
);
