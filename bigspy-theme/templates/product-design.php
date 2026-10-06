<?php
defined( 'ABSPATH' ) || exit;
global $product;
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) { echo get_the_password_form(); return; }
$bst_design = bst_product_design_page();
if ( ! $bst_design || ! $product ) { return; }
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'bs-product-design', $product ); ?>>
<?php echo do_shortcode( do_blocks( $bst_design->post_content ) ); ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
