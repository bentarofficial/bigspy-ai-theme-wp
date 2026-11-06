<?php
/** Theme Showcase block: version, counts, features and updates read from the installed theme. */
defined( 'ABSPATH' ) || exit;
$a        = $attributes;
$theme    = bst_welcome_data( 'theme' );
$designer = bst_welcome_data( 'designer' );
$features = array_merge( $theme['features'] ?? array(), $designer['features'] ?? array() );
$news     = array_slice( $theme['news'] ?? array(), 0, max( 1, min( 20, (int) ( $a['newsCount'] ?? 5 ) ) ) );
$button   = trim( (string) ( $a['buttonText'] ?? '' ) ) ?: __( 'Get the theme', 'bigspy-ai-theme-wp' );
$url      = trim( (string) ( $a['buttonUrl'] ?? '' ) );
$note     = trim( (string) ( $a['note'] ?? '' ) ) ?: __( 'Join the Telegram group to read the guides and get the GitHub download link.', 'bigspy-ai-theme-wp' );
$stats    = array(
	array( BST_VERSION, __( 'Current version', 'bigspy-ai-theme-wp' ) ),
	array( count( bst_block_names() ), __( 'Blocks', 'bigspy-ai-theme-wp' ) ),
	array( count( $features ), __( 'Feature groups', 'bigspy-ai-theme-wp' ) ),
	array( count( $theme['news'] ?? array() ), __( 'Recent updates', 'bigspy-ai-theme-wp' ) ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-tsc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $a['showHero'] ) ) : ?>
		<div class="bs-tsc__hero">
			<p class="bs-tsc__eyebrow"><?php echo esc_html( 'BIGSPY AI · ' . BST_VERSION ); ?></p>
			<h2 class="bs-tsc__title"><?php echo esc_html( $theme['title'] ?? 'BIGSPY AI Theme WP' ); ?></h2>
			<p class="bs-tsc__lead"><?php echo esc_html( $theme['lead'] ?? '' ); ?></p>
			<dl class="bs-tsc__stats">
				<?php foreach ( $stats as $stat ) : ?>
					<div><dt><?php echo esc_html( $stat[1] ); ?></dt><dd><?php echo esc_html( $stat[0] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<?php if ( $url ) : ?>
				<p class="bs-tsc__cta"><a class="bs-tsc__button" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $button ); ?></a></p>
				<p class="bs-tsc__note"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $a['showFeatures'] ) && $features ) : ?>
		<div class="bs-tsc__features">
			<?php foreach ( $features as $item ) : ?>
				<div class="bs-tsc__card">
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<p><?php echo esc_html( $item['summary'] ); ?></p>
					<?php if ( ! empty( $item['since'] ) ) : ?>
						<span class="bs-tsc__badge"><?php echo esc_html( sprintf( __( 'Available since %s', 'bigspy-ai-theme-wp' ), $item['since'] ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $a['showNews'] ) && $news ) : ?>
		<div class="bs-tsc__news">
			<h3><?php esc_html_e( 'What is new', 'bigspy-ai-theme-wp' ); ?></h3>
			<ul>
				<?php foreach ( $news as $line ) : $parts = explode( ': ', $line, 2 ); ?>
					<li><?php if ( 2 === count( $parts ) ) : ?><strong><?php echo esc_html( $parts[0] ); ?></strong> <?php echo esc_html( $parts[1] ); ?><?php else : echo esc_html( $line ); endif; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
