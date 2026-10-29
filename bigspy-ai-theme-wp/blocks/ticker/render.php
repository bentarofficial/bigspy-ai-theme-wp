<?php
/** Infinity Flow (ticker) wrapper; motion and clones are progressive enhancements. */
defined( 'ABSPATH' ) || exit;
$a = $attributes;
$width = function ( $key, $default ) use ( $a ) {
    $v = (string) ( $a[ $key ] ?? $default );
    return preg_match( '/^(auto|(?:[1-9][0-9]{0,3})(?:px|%))$/D', $v ) ? $v : $default;
};
$config = array( 'direction' => bst_choice( $a['direction'] ?? 'left', array( 'left', 'right' ), 'left' ), 'mode' => bst_choice( $a['mode'] ?? 'loop', array( 'loop', 'once' ), 'loop' ), 'speed' => bst_block_num( $a, 'speed', 50, 1, 500 ), 'pause' => ! empty( $a['pauseHover'] ), 'widths' => array( $width( 'widthDesktop', 'auto' ), $width( 'widthTablet', 'auto' ), $width( 'widthMobile', '85%' ) ) );
$style = '--bs-ticker-gap:' . bst_block_num( $a, 'gap', 24, 0, 160 ) . 'px;--bs-ticker-align:' . bst_choice( $a['verticalAlign'] ?? 'center', array( 'start', 'center', 'end' ), 'center' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bs-ticker-block', 'style' => $style ) ); ?>>
<div class="bs-ticker<?php echo ! empty( $a['fadeEdges'] ) ? ' bs-ticker--fade' : ''; ?>" data-bs-ticker="<?php echo esc_attr( wp_json_encode( $config ) ); ?>" role="region" aria-label="<?php echo esc_attr( $a['label'] ?: __( 'Infinity Flow', 'bigspy-ai-theme-wp' ) ); ?>" tabindex="0">
<div class="bs-ticker__track"><div class="bs-ticker__sequence"><?php echo $content; // Rendered child blocks. ?></div></div>
</div>
</div>
