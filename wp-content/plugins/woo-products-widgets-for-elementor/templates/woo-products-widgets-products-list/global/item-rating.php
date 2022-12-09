<?php
/**
 * Loop item price
 */

$rating = woo_product_widgets_elementor_template_functions()->get_product_rating();

if ( 'yes' !== $this->get_attr( 'show_rating' ) || '' === $rating ) {
	return;
}
?>

<div class="woo-products-product-rating"><?php echo $rating; ?></div>