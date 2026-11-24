<?php
/**
 * Tracking pixels and site verification from plain IDs (Customizer > Advanced >
 * Custom scripts), so nothing has to be pasted as code. Google tags live in setup.php.
 */
defined( 'ABSPATH' ) || exit;

/** Settings: key => array( label, help, pattern the cleaned value must match ). */
function bst_tracking_fields() {
	return array(
		'pixel_meta'     => array( __( 'Meta (Facebook) Pixel ID', 'bigspy-ai-theme-wp' ), __( 'Numbers only, e.g. 1234567890123456.', 'bigspy-ai-theme-wp' ), '/^[0-9]{10,20}$/' ),
		'pixel_tiktok'   => array( __( 'TikTok Pixel ID', 'bigspy-ai-theme-wp' ), __( 'e.g. C4ABCDEF1234567890AB.', 'bigspy-ai-theme-wp' ), '/^[A-Z0-9]{10,30}$/' ),
		'pixel_clarity'  => array( __( 'Microsoft Clarity project ID', 'bigspy-ai-theme-wp' ), __( 'Free heatmaps and session recordings. e.g. abcd1234ef.', 'bigspy-ai-theme-wp' ), '/^[a-z0-9]{6,20}$/' ),
		'pixel_bing'     => array( __( 'Microsoft Ads (Bing UET) tag ID', 'bigspy-ai-theme-wp' ), __( 'Numbers only.', 'bigspy-ai-theme-wp' ), '/^[0-9]{5,15}$/' ),
		'pixel_linkedin' => array( __( 'LinkedIn Insight partner ID', 'bigspy-ai-theme-wp' ), __( 'Numbers only.', 'bigspy-ai-theme-wp' ), '/^[0-9]{4,12}$/' ),
		'verify_google'    => array( __( 'Google Search Console verification', 'bigspy-ai-theme-wp' ), __( 'Choose the “HTML tag” method and paste the whole <meta> tag or just its content value.', 'bigspy-ai-theme-wp' ), '/^[A-Za-z0-9_\-]{10,100}$/' ),
		'verify_bing'      => array( __( 'Bing Webmaster verification', 'bigspy-ai-theme-wp' ), __( 'msvalidate.01 tag or its content value.', 'bigspy-ai-theme-wp' ), '/^[A-Za-z0-9]{10,64}$/' ),
		'verify_facebook'  => array( __( 'Facebook domain verification', 'bigspy-ai-theme-wp' ), __( 'facebook-domain-verification tag or its content value.', 'bigspy-ai-theme-wp' ), '/^[a-z0-9]{10,64}$/' ),
		'verify_pinterest' => array( __( 'Pinterest verification', 'bigspy-ai-theme-wp' ), __( 'p:domain_verify tag or its content value.', 'bigspy-ai-theme-wp' ), '/^[a-f0-9]{10,64}$/' ),
		'verify_yandex'    => array( __( 'Yandex verification', 'bigspy-ai-theme-wp' ), __( 'yandex-verification tag or its content value.', 'bigspy-ai-theme-wp' ), '/^[a-f0-9]{10,64}$/' ),
	);
}

/** Keeps only the ID: accepts a pasted <meta>/<script> snippet and pulls the value out. */
function bst_tracking_clean( $value, $key ) {
	$value = trim( wp_unslash( (string) $value ) );
	if ( preg_match( '/content\s*=\s*["\']([^"\']+)["\']/i', $value, $m ) ) {
		$value = $m[1];
	}
	$value  = trim( wp_strip_all_tags( $value ) );
	$fields = bst_tracking_fields();
	if ( 'pixel_tiktok' === $key ) {
		$value = strtoupper( $value );
	} elseif ( 'pixel_clarity' === $key ) {
		$value = strtolower( $value );
	}
	return ( isset( $fields[ $key ] ) && preg_match( $fields[ $key ][2], $value ) ) ? $value : '';
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$priority = 4;
		foreach ( bst_tracking_fields() as $key => $field ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => '',
					'sanitize_callback' => function ( $value ) use ( $key ) {
						return bst_tracking_clean( $value, $key );
					},
				)
			);
			$wp_customize->add_control( $key, array( 'section' => 'bst_scripts', 'type' => 'text', 'label' => $field[0], 'description' => $field[1], 'priority' => $priority++ ) );
		}
	},
	20
);

function bst_tracking_id( $key ) {
	return bst_tracking_clean( get_theme_mod( $key, '' ), $key );
}

add_action(
	'wp_head',
	function () {
		$metas = array(
			'verify_google'    => 'google-site-verification',
			'verify_bing'      => 'msvalidate.01',
			'verify_facebook'  => 'facebook-domain-verification',
			'verify_pinterest' => 'p:domain_verify',
			'verify_yandex'    => 'yandex-verification',
		);
		foreach ( $metas as $key => $name ) {
			$id = bst_tracking_id( $key );
			if ( $id ) {
				echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $id ) . '" />' . "\n";
			}
		}
	},
	1
);

add_action(
	'wp_head',
	function () {
		if ( is_customize_preview() ) {
			return;
		}
		$snippets = array(
			'pixel_meta'     => <<<'JS'
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','__ID__');fbq('track','PageView');
JS,
			'pixel_tiktok'   => <<<'JS'
!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var s=d.createElement("script");s.type="text/javascript",s.async=!0,s.src=r+"?sdkid="+e+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(s,a)};ttq.load('__ID__');ttq.page();}(window,document,'ttq');
JS,
			'pixel_clarity'  => <<<'JS'
(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","__ID__");
JS,
			'pixel_bing'     => <<<'JS'
(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"__ID__",enableAutoSpaTracking:true};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");
JS,
			'pixel_linkedin' => <<<'JS'
window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push("__ID__");(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};window.lintrk.q=[]}var s=document.getElementsByTagName("script")[0];var b=document.createElement("script");b.type="text/javascript";b.async=true;b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";s.parentNode.insertBefore(b,s);})(window.lintrk);
JS,
		);
		foreach ( $snippets as $key => $js ) {
			$id = bst_tracking_id( $key );
			if ( $id ) {
				// ID is validated against a strict pattern in bst_tracking_clean().
				echo '<script>' . str_replace( '__ID__', esc_js( $id ), trim( $js ) ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	},
	2
);
