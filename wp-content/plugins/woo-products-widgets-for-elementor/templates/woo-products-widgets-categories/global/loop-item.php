<?php
/**
 * Categories loop item template
 */
?>
<div class="woo-products-categories__item <?php echo woo_product_widgets_elementor_tools()->col_classes( array(
	'desk' => $this->get_attr( 'columns' ),
	'tab'  => $this->get_attr( 'columns_tablet' ),
	'mob'  => $this->get_attr( 'columns_mobile' ),
) ); ?>">
	<div class="woo-products-categories__inner-box"><?php include $this->get_category_preset_template(); ?></div>
</div>