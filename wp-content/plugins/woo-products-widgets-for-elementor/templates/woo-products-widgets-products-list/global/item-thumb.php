<?php
/**
 * Loop item thumbnail
 */

$size = $this->get_attr( 'thumb_size' );
$thumbnail = woo_product_widgets_elementor_template_functions()->get_product_thumbnail( $size );

if ( 'yes' !== $this->get_attr( 'show_image' ) || null === $thumbnail ) {
	return;
}
?>

<div class="woo-products-product-thumbnail"><?php echo $thumbnail; ?></div>