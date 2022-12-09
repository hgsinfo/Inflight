<?php
/**
 * Loop item title
 */

$title = $category->name;;

if ( 'yes' !== $this->get_attr( 'show_title' ) ) {
	return;
}
?>

<div class="woo-products-category-title">
	<a href="<?php echo woo_product_widgets_elementor_tools()->get_term_permalink( $category->term_id ) ?>" class="woo-products-category-title__link"><?php echo $title; ?></a>
</div>