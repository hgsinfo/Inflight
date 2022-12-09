<?php
/**
 * Categories loop item layout 2
 */


?>

<?php include $this->get_template( 'item-thumb' ); ?>
<div class="woo-products-categories-content"><?php
	include $this->get_template( 'item-title' );
	include $this->get_template( 'item-description' );
	?><div class="woo-products-category-count__wrap"><?php include $this->get_template( 'item-count' ); ?></div>
</div>
