<?php
/**
 * Products loop start template
 */

$classes = array(
	'woo-products-products',
	'woo-products-products--' . $this->get_attr( 'presets' ),
	'col-row',
	woo_product_widgets_elementor_tools()->gap_classes( $this->get_attr( 'columns_gap' ), $this->get_attr( 'rows_gap' ) ),
);

$equal = $this->get_attr( 'equal_height_cols' );

if ( $equal ) {
	$classes[] = 'woo-products-equal-cols';
}

?>

<div class="<?php echo implode( ' ', $classes ); ?>">