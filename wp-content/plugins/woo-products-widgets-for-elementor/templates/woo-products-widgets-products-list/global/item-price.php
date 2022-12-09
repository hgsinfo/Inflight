<?php
/**
 * Loop item price
 */

$price = woo_product_widgets_elementor_template_functions()->get_product_price();

if ( 'yes' !== $this->get_attr( 'show_price' ) || '' === $price ) {
	return;
}
?>

<div class="woo-products-product-price"><?php echo $price; ?></div>