<?php
/**
 * Move Contact Form 7 forms to the built-in form. An admin converts one form at a time:
 * posts that show it get a Form block instead (WordPress keeps a revision). The CF7 form itself is not touched.
 */
defined( 'ABSPATH' ) || exit;

function bst_cf7_forms() {
	return get_posts( array( 'post_type' => 'wpcf7_contact_form', 'post_status' => 'any', 'numberposts' => 200, 'suppress_filters' => true ) );
}

/** Turn CF7 form text into built-in fields. Returns fields, submit text and the tags that have no equivalent. */
function bst_cf7_parse( $text ) {
	$out = array( 'fields' => array(), 'submit' => '', 'skipped' => array() );
	preg_match_all( '/\[([a-z_]+)(\*?)(?:\s+([^\]]*))?\](?:(.*?)\[\/\1\])?/s', (string) $text, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	$from = 0;
	foreach ( $tags as $tag ) {
		$type   = $tag[1][0];
		$before = wp_strip_all_tags( substr( $text, $from, $tag[0][1] - $from ) );
		$from   = $tag[0][1] + strlen( $tag[0][0] );
		$args   = $tag[3][0] ?? '';
		preg_match_all( '/"([^"]*)"|\'([^\']*)\'/', $args, $quoted );
		$values = array_map(
			function ( $a, $b ) {
				return '' !== $a ? $a : $b;
			},
			$quoted[1],
			$quoted[2]
		);
		$words = preg_split( '/\s+/', trim( preg_replace( '/"[^"]*"|\'[^\']*\'/', '', $args ) ), -1, PREG_SPLIT_NO_EMPTY );
		if ( 'submit' === $type ) {
			$out['submit'] = $values[0] ?? '';
			continue;
		}
		$map = array( 'text' => 'text', 'email' => 'email', 'tel' => 'tel', 'url' => 'url', 'number' => 'number', 'date' => 'date', 'textarea' => 'textarea', 'select' => 'select', 'radio' => 'radio', 'checkbox' => 'checkbox', 'acceptance' => 'acceptance', 'file' => 'file' );
		if ( ! isset( $map[ $type ] ) ) {
			$out['skipped'][] = $type;
			continue;
		}
		$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $before ) ) );
		$field = array(
			'type'     => $map[ $type ],
			'name'     => sanitize_key( $words[0] ?? $type ),
			'label'    => (string) ( end( $lines ) ?: '' ),
			'required' => '*' === $tag[2][0],
			'width'    => 'full',
		);
		if ( in_array( 'placeholder', $words, true ) || in_array( 'watermark', $words, true ) ) {
			$field['placeholder'] = $values[0] ?? '';
		} elseif ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ) {
			$field['options'] = implode( "\n", $values );
		}
		if ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ) {
			$picks = array();
			foreach ( $words as $word ) {
				if ( preg_match( '/^default:([\d_]+)$/', $word, $picked ) ) {
					foreach ( explode( '_', $picked[1] ) as $n ) {
						if ( isset( $values[ $n - 1 ] ) ) {
							$picks[] = $values[ $n - 1 ];
						}
					}
				}
			}
			if ( $picks ) {
				$field['default'] = implode( "\n", $picks );
			}
		}
		if ( 'select' === $type && in_array( 'first_as_label', $words, true ) ) {
			$field['placeholder'] = array_shift( $values );
			$field['options']     = implode( "\n", $values );
		}
		if ( 'acceptance' === $type ) {
			$field['placeholder'] = trim( $tag[4][0] ?? '' );
			$field['required']    = ! in_array( 'optional', $words, true );
		}
		if ( 'file' === $type ) {
			foreach ( $words as $word ) {
				if ( 0 === strpos( $word, 'filetypes:' ) ) {
					$field['options'] = str_replace( '|', ',', substr( $word, 10 ) );
				}
			}
		}
		$out['fields'][] = $field;
	}
	return $out;
}

/**
 * Redirect set with Redirection for Contact Form 7, as block attributes. The plugin keeps each action
 * in a private post; older versions keep the settings on the form. 'partial' marks what the block cannot repeat.
 */
function bst_cf7_redirect( $form_id ) {
	$actions = get_posts(
		array(
			'post_type'        => 'wpcf7r_action',
			'post_status'      => 'private',
			'numberposts'      => 20,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array( 'key' => 'wpcf7_id', 'value' => $form_id ),
				array( 'key' => 'action_type', 'value' => 'redirect' ),
				array( 'key' => 'action_status', 'value' => 'on' ),
			),
		)
	);
	$sets    = array();
	foreach ( $actions as $action ) {
		$sets[] = array( $action->ID, '', 1 );
	}
	$sets[] = array( $form_id, '_wpcf7_redirect_', 1000 );
	foreach ( $sets as list( $id, $prefix, $per_second ) ) {
		$get = function ( $key ) use ( $id, $prefix ) {
			return (string) get_post_meta( $id, $prefix . $key, true );
		};
		$url = 'on' === $get( 'use_external_url' ) ? $get( 'external_url' ) : '';
		if ( '' === $url && (int) $get( 'page_id' ) ) {
			$url = (string) get_permalink( (int) $get( 'page_id' ) );
		}
		if ( '' === $url ) {
			continue;
		}
		$delay = '' === $prefix ? $get( 'delay_redirect_seconds' ) : $get( 'delay_redirect' );
		return array(
			'redirectUrl'    => $url,
			'redirectDelay'  => min( 30, max( 0, (int) $delay / $per_second ) ),
			'redirectPass'   => 'on' === $get( 'http_build_query' ),
			'redirectNewTab' => 'on' === $get( 'open_in_new_tab' ),
			'partial'        => false !== strpos( $url, '[' ) || 'on' === $get( 'http_build_query_selectively' ) || 'on' === $get( 'conditional_logic' ),
		);
	}
	return array();
}

/** Block attributes of the built-in form that matches one CF7 form. */
function bst_cf7_block_attrs( $form ) {
	$parsed = bst_cf7_parse( get_post_meta( $form->ID, '_form', true ) );
	$mail   = (array) get_post_meta( $form->ID, '_mail', true );
	$mail2  = (array) get_post_meta( $form->ID, '_mail_2', true );
	$msg    = (array) get_post_meta( $form->ID, '_messages', true );
	$pairs  = array(
		'mailTo'       => $mail['recipient'] ?? '',
		'mailFrom'     => $mail['sender'] ?? '',
		'mailSubject'  => $mail['subject'] ?? '',
		'mailHeaders'  => $mail['additional_headers'] ?? '',
		'mailBody'     => $mail['body'] ?? '',
		'mail2To'      => $mail2['recipient'] ?? '',
		'mail2From'    => $mail2['sender'] ?? '',
		'mail2Subject' => $mail2['subject'] ?? '',
		'mail2Headers' => $mail2['additional_headers'] ?? '',
		'mail2Body'    => $mail2['body'] ?? '',
		'msgSuccess'   => $msg['mail_sent_ok'] ?? '',
		'msgError'     => $msg['mail_sent_ng'] ?? '',
		'msgInvalid'   => $msg['validation_error'] ?? '',
		'msgRequired'  => $msg['invalid_required'] ?? '',
		'msgEmail'     => $msg['invalid_email'] ?? '',
		'msgTel'       => $msg['invalid_tel'] ?? '',
		'msgSpam'      => $msg['spam'] ?? '',
		'submitText'   => $parsed['submit'],
	);
	return array_filter( $pairs, 'strlen' ) + array_diff_key( bst_cf7_redirect( $form->ID ), array( 'partial' => 1 ) ) + array(
		'provider'         => 'bigspy',
		'formKey'          => 'cf7' . $form->ID,
		'formName'         => $form->post_title,
		'fields'           => $parsed['fields'],
		'mailHtml'         => ! empty( $mail['use_html'] ),
		'mailExcludeBlank' => ! empty( $mail['exclude_blank'] ),
		'mail2Active'      => ! empty( $mail2['active'] ),
	);
}

function bst_cf7_block( $attrs ) {
	return serialize_block( array( 'blockName' => 'bst/form', 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
}

/** A Form block set to this CF7 form keeps its own look and redirect, and gets the form's fields and mail. */
function bst_cf7_upgrade_block( $found, $form ) {
	$old = json_decode( $found[1], true );
	if ( ! is_array( $old ) || 'cf7' !== ( $old['provider'] ?? 'cf7' ) || $form->ID !== (int) ( $old['formId'] ?? 0 ) ) {
		return $found[0];
	}
	$new = bst_cf7_block_attrs( $form );
	if ( ! empty( $old['redirectUrl'] ) ) {
		$new = array_diff_key( $new, array_flip( array( 'redirectUrl', 'redirectDelay', 'redirectPass', 'redirectNewTab' ) ) );
	}
	unset( $old['formId'] );
	return bst_cf7_block( $new + $old );
}

function bst_cf7_id_matches( $form, $id ) {
	$hash = (string) get_post_meta( $form->ID, '_hash', true );
	return (string) $form->ID === (string) $id || ( '' !== (string) $id && '' !== $hash && 0 === strpos( $hash, (string) $id ) );
}

/** Replace every appearance of this CF7 form in the content with the given block. */
function bst_cf7_replace( $content, $form, $block ) {
	$swap = function ( $found ) use ( $form, $block ) {
		$hit = preg_match( '/\bid=["\']?([\w-]+)/', $found[0], $id ) && bst_cf7_id_matches( $form, $id[1] );
		if ( ! $hit && preg_match( '/"(?:id|hash)":"?([\w-]+)/', $found[0], $id ) ) {
			$hit = bst_cf7_id_matches( $form, $id[1] );
		}
		return $hit ? $block : $found[0];
	};
	$short   = '\[contact-form-7\b[^\]]*\]';
	$content = preg_replace_callback(
		'#<!-- wp:bst/form (\{(?:(?!-->).)*?\}) (?:/-->|-->\s*<!-- /wp:bst/form -->)#s',
		function ( $found ) use ( $form ) {
			return bst_cf7_upgrade_block( $found, $form );
		},
		$content
	);
	$content = preg_replace_callback( '#<!-- wp:contact-form-7/contact-form-selector .*?(?:/-->|<!-- /wp:contact-form-7/contact-form-selector -->)#s', $swap, $content );
	$content = preg_replace_callback( '#<!-- wp:shortcode -->\s*' . $short . '\s*<!-- /wp:shortcode -->#', $swap, $content );
	$content = preg_replace_callback( '#<p>\s*' . $short . '\s*</p>#', $swap, $content );
	return preg_replace_callback( '#' . $short . '#', $swap, $content );
}

/** Posts that show this form, paired with their new content. */
function bst_cf7_targets( $form, $block ) {
	global $wpdb;
	$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_status IN ('publish','draft','pending','future','private') AND post_type NOT IN ('revision','wpcf7_contact_form') AND (post_content LIKE '%contact-form-7%' OR post_content LIKE '%wp:bst/form %')" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$out = array();
	foreach ( $ids as $id ) {
		$old = get_post_field( 'post_content', $id );
		$new = bst_cf7_replace( $old, $form, $block );
		if ( $new !== $old ) {
			$out[ (int) $id ] = $new;
		}
	}
	return $out;
}

function bst_cf7_migrate( $form ) {
	$targets = bst_cf7_targets( $form, bst_cf7_block( bst_cf7_block_attrs( $form ) ) );
	foreach ( $targets as $id => $content ) {
		wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $content ) ) );
	}
	return count( $targets );
}

add_action(
	'admin_post_bst_cf7_migrate',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'bigspy-ai-theme-wp' ) );
		}
		check_admin_referer( 'bst_cf7_migrate' );
		$form = get_post( absint( $_POST['form'] ?? 0 ) );
		$done = $form && 'wpcf7_contact_form' === $form->post_type ? bst_cf7_migrate( $form ) : 0;
		wp_safe_redirect( admin_url( 'admin.php?page=bigspy-form-migrate&done=' . $done ) );
		exit;
	}
);

add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'bigspy-ai-theme-wp', __( 'Move CF7 forms', 'bigspy-ai-theme-wp' ), __( 'Move CF7 forms', 'bigspy-ai-theme-wp' ), 'manage_options', 'bigspy-form-migrate', 'bst_cf7_page' );
	},
	20
);

function bst_cf7_page() {
	echo '<div class="wrap"><h1>' . esc_html__( 'Move CF7 forms', 'bigspy-ai-theme-wp' ) . '</h1>';
	echo '<p>' . esc_html__( 'Replaces a Contact Form 7 form with the built-in Form block in posts and pages. Forms in widgets or theme options are not changed. Check each form after moving it, then turn the plugin off yourself.', 'bigspy-ai-theme-wp' ) . '</p>';
	if ( isset( $_GET['done'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- display only.
		/* translators: %d: number of posts changed */
		echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Posts updated: %d', 'bigspy-ai-theme-wp' ), absint( $_GET['done'] ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification
	}
	$forms = bst_cf7_forms();
	if ( ! $forms ) {
		echo '<p>' . esc_html__( 'No Contact Form 7 forms found.', 'bigspy-ai-theme-wp' ) . '</p></div>';
		return;
	}
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Form', 'bigspy-ai-theme-wp' ) . '</th><th>' . esc_html__( 'Fields', 'bigspy-ai-theme-wp' ) . '</th><th>' . esc_html__( 'Not carried over', 'bigspy-ai-theme-wp' ) . '</th><th>' . esc_html__( 'Posts to update', 'bigspy-ai-theme-wp' ) . '</th><th></th></tr></thead><tbody>';
	foreach ( $forms as $form ) {
		$parsed = bst_cf7_parse( get_post_meta( $form->ID, '_form', true ) );
		$count  = count( bst_cf7_targets( $form, 'x' ) );
		if ( ! empty( bst_cf7_redirect( $form->ID )['partial'] ) ) {
			$parsed['skipped'][] = __( 'redirect conditions or mail tags', 'bigspy-ai-theme-wp' );
		}
		echo '<tr><td>' . esc_html( $form->post_title ) . '</td><td>' . count( $parsed['fields'] ) . '</td><td>' . esc_html( implode( ', ', array_unique( $parsed['skipped'] ) ) ) . '</td><td>' . (int) $count . '</td><td>';
		if ( $count ) {
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" onsubmit="return confirm(this.dataset.ask)" data-ask="' . esc_attr__( 'Replace this form in the listed posts? WordPress keeps a revision of each post.', 'bigspy-ai-theme-wp' ) . '">';
			wp_nonce_field( 'bst_cf7_migrate' );
			echo '<input type="hidden" name="action" value="bst_cf7_migrate"><input type="hidden" name="form" value="' . (int) $form->ID . '">';
			submit_button( __( 'Move', 'bigspy-ai-theme-wp' ), 'secondary', 'submit', false );
			echo '</form>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table></div>';
}
