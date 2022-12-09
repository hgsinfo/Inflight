<?php
/**
 * Products loop item template
 */

$classes = array(
	woo_product_widgets_elementor_tools()->col_classes( array(
		'desk' => $this->get_attr( 'columns' ),
		'tab'  => $this->get_attr( 'columns_tablet' ),
		'mob'  => $this->get_attr( 'columns_mobile' ),
	) )
);

$enable_thumb_effect = filter_var( woo_elementor_product_widgets_settings()->get( 'enable_product_thumb_effect' ), FILTER_VALIDATE_BOOLEAN );

if ( $enable_thumb_effect ){
	array_push( $classes, 'woo-products-thumb-with-effect' );
}
?>
<div class="woo-products-products__item <?php echo implode( ' ', $classes ); ?>">
	<div class="woo-products-products__inner-box"><?php include $this->get_product_preset_template(); ?></div>
</div>