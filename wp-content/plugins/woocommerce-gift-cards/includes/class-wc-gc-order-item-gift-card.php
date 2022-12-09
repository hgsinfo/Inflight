<?php
/**
 * WC_GC_Order_Item_Gift_Card class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Gift Cards
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gift Card Order Item Model class.
 *
 * @class    WC_GC_Order_Item_Gift_Card
 * @version  1.8.0
 */
class WC_GC_Order_Item_Gift_Card extends WC_Order_Item {

	/**
	 * Extra item data required for Gift Cards.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'giftcard_id' => 0,
		'code'        => '',
		'amount'      => 0
	);

	/*---------------------------------------------------*/
	/*  Setters.                                         */
	/*---------------------------------------------------*/

	/**
	 * Set item amount.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function set_name( $value ) {
		return $this->set_code( $value );
	}

	/**
	 * Set item amount.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function set_code( $value ) {
		$this->set_prop( 'code', wc_clean( $value ) );
	}

	/**
	 * Set item amount.
	 *
	 * @param  float $value
	 * @return void
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', wc_format_decimal( $value ) );
	}

	/**
	 * Set giftcard id.
	 *
	 * @param  int $value
	 * @return void
	 */
	public function set_giftcard_id( $value ) {
		$this->set_prop( 'giftcard_id', absint( $value ) );
	}

	/*---------------------------------------------------*/
	/*  Getters.                                         */
	/*---------------------------------------------------*/

	/**
	 * Magic getters.
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function &__get( $name ) {
		$value = '';

		switch ( $name ) {
			case 'code':
				$value = $this->get_code();
				break;
		}

		return $value;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_code( $context );
	}

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'gift_card';
	}

	/**
	 * Get code.
	 *
	 * @return string
	 */
	public function get_code( $context = 'view' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Get type.
	 *
	 * @return float
	 */
	public function get_amount( $context = 'view' ) {
		return (float) $this->get_prop( 'amount', $context );
	}

	/**
	 * Get giftcard id.
	 *
	 * @return float
	 */
	public function get_giftcard_id( $context = 'view' ) {
		return (int) $this->get_prop( 'giftcard_id', $context );
	}
}
