<?php
/**
 * Categories loop item layout 4
 */

?>

<?php include $this->get_template( 'item-thumb' ); ?>
<div class="woo-products-categories-content">
	<div class="woo-products-categories-title__wrap"><?php
	  include $this->get_template( 'item-title' );
	  include $this->get_template( 'item-count' );
	  ?></div><?php
	include $this->get_template( 'item-description' );
	?></div>