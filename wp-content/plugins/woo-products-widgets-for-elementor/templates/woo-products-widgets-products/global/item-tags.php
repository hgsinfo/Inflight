<?php
/**
 * Loop item tags
 */

$tags = woo_product_widgets_elementor_template_functions()->get_product_tags_list();

if ( 'yes' !== $this->get_attr( 'show_tag' ) || false === $tags ) {
	return;
}
?>

<div class="woo-products-product-tags"><?php echo $tags; ?></div>