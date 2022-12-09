<?php
/**
 * Loop item content
 */
$excerpt = woo_product_widgets_elementor_tools()->trim_text( woo_product_widgets_elementor_template_functions()->get_product_excerpt(), $this->get_attr( 'excerpt_length' ), 'word', '...' );

if ( 'yes' !== $this->get_attr( 'show_excerpt' ) || null === $excerpt ) {
	return;
}
?>

<div class="woo-products-product-excerpt"><?php echo $excerpt; ?></div>