<?php
/**
 * Categories loop item layout 1
 */

?>

<div class="woo-products-categories-thumbnail__wrap"><?php include $this->get_template( 'item-thumb' );?>
	<div class="woo-products-category-count__wrap"><?php include $this->get_template( 'item-count' ); ?></div>
</div>
<div class="woo-products-categories-content"><?php
	include $this->get_template( 'item-title' );
	include $this->get_template( 'item-description' );
	?></div>