<?php
/**
 * Loop item thumbnail
 */

$size      = $this->get_attr( 'thumb_size' );
$thumbnail = woo_product_widgets_elementor_template_functions()->get_category_thumbnail( $category->term_id, $this->get_attr( 'thumb_size' ) );

if ( null === $thumbnail ) {
	return;
}
?>
<div class="woo-products-category-thumbnail">
	<a href="<?php echo esc_url( get_category_link( $category->term_id ) ) ?>" rel="bookmark"><?php echo $thumbnail; ?></a>
	<div class="woo-products-category-img-overlay"></div>
	<div class="woo-products-category-img-overlay__hover"></div>
</div>