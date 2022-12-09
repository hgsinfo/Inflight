<?php
/**
 * Products list loop item template
 */
?>
<li class="woo-products-products-list__item">
	<div class="woo-products-products-list__inner-box">
		<div class="woo-products-products-list__item-img"><?php  include $this->get_template( 'item-thumb' ); ?></div>
	 <div class="woo-products-products-list__item-content"><?php
	   include $this->get_template( 'item-categories' );
	   include $this->get_template( 'item-title' );
	   include $this->get_template( 'item-price' );
	   include $this->get_template( 'item-content' );
	   include $this->get_template( 'item-button' );
	   include $this->get_template( 'item-rating' );
	   ?></div>
	</div>
</li>