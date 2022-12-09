<?php
/**
 * Loop item thumbnail
 */

$size       = $this->get_attr( 'thumb_size' );
$thumbnail  = woo_product_widgets_elementor_template_functions()->get_product_thumbnail( $size, true );
$sale_badge = woo_product_widgets_elementor_template_functions()->get_product_sale_flash( $this->get_attr( 'sale_badge_text' ), $this->get_attr( 'show_stock_badges' ));

if ( null === $thumbnail ) {
	return;
}
?>
<div class="woo-products-product-thumbnail">
	<a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark"><?php echo $thumbnail; ?></a>
	<div class="woo-products-product-img-overlay"></div><?php
		if ( null != $sale_badge && 'yes' === $this->get_attr( 'show_badges' ) ) {
			echo sprintf( '<div class="woo-products-product-badges">%s</div>', $sale_badge );
		}
	?></div>