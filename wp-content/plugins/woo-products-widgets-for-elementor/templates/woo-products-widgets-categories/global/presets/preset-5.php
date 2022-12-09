<?php
/**
 * Categories loop item layout 5
 */

?>

<div class="woo-products-categories-thumbnail__wrap"><?php include $this->get_template( 'item-thumb' ); ?></div>
<div class="woo-products-categories-content">
	<div class="woo-products-category-content__inner">
	  <?php
	  include $this->get_template( 'item-title' );
	  include $this->get_template( 'item-description' );
	  ?>
	</div>
	<div class="woo-products-category-count__wrap"><?php include $this->get_template( 'item-count' ); ?></div>
</div>