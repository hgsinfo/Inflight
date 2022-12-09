<?php
/**
 * Loop item description
 */

$description = woo_product_widgets_elementor_tools()->trim_text( $category->description, $this->get_attr( 'desc_length' ), 'word', $this->get_attr( 'desc_after_text' ) );

if ( '' === $description ) {
	return;
}
?>

<div class="woo-products-category-excerpt"><?php echo $description; ?></div>