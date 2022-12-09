<?php
/**
 * Loop item title
 */

$title = woo_product_widgets_elementor_tools()->trim_text( woo_product_widgets_elementor_template_functions()->get_product_title(), $this->get_attr( 'title_length' ) , 'word', '...' );
$title_link = woo_product_widgets_elementor_template_functions()->get_product_title_link();
if ( 'yes' !== $this->get_attr( 'show_title' ) || '' === $title ) {
	return;
}
?>

<div class="woo-products-product-title"><a href="<?php echo $title_link; ?>" rel="bookmark"><?php echo $title; ?></a></div>
