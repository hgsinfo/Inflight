<?php
/**
 * Products loop item layout 5
 */
?>
<div class="woo-products-products__thumb-wrap"><?php include $this->get_template( 'item-thumb' ); ?>
	<div class="woo-products-products__item-content hovered-content"><?php
	  include $this->get_template( 'item-categories' );
	  include $this->get_template( 'item-title' );
	  include $this->get_template( 'item-price' );
	  include $this->get_template( 'item-content' );
	  include $this->get_template( 'item-button' );
	  include $this->get_template( 'item-rating' );
	  include $this->get_template( 'item-tags' );
	  ?></div>
</div>