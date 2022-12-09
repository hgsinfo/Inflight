<?php
/**
 * Loop item categories
 */

$categories = woo_product_widgets_elementor_template_functions()->get_product_categories_list();

if ( 'yes' !== $this->get_attr( 'show_cat' ) || false === $categories ) {
	return;
}
?>

<div class="woo-products-product-categories"><?php echo $categories; ?></div>