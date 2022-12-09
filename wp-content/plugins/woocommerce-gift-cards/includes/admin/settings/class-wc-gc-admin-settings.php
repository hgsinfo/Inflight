<?php
/**
 * WC_GC_Settings class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Gift Cards
 * @since    1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_GC_Settings' ) ) :

	/**
	 * WooCommerce Gift Cards Settings.
	 *
	 * @class    WC_GC_Settings
	 * @version  1.7.3
	 */
	class WC_GC_Settings extends WC_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id    = 'gc_settings';
			$this->label = __( 'Gift Cards', 'woocommerce-gift-cards' );

			// Add settings page.
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			// Output sections.
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			// Output content.
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			// Process + save data.
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			// Handle the reverse Cart features setting value.
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'manage_disable_ui_setting' ), 10, 2 );
		}

		/**
		 * Get settings array.
		 *
		 * @return array
		 */
		public function get_settings() {

			$settings = array(

				array(
					'title' => __( 'Account', 'woocommerce-gift-cards' ),
					'type'  => 'title',
					'id'    => 'gc_settings_account'
				),

				array(
					'title'    => __( 'Enable account features', 'woocommerce-gift-cards' ),
					'desc'     => __( 'Allow customers to store gift cards in their account, and pay for orders using their account balance', 'woocommerce-gift-cards' ),
					'id'       => 'wc_gc_is_redeeming_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
					'desc_tip' => __( 'When disabled, customers will always need to enter their gift card codes manually.', 'woocommerce-gift-cards' ),
				),

				array( 'type' => 'sectionend', 'id' => 'gc_settings_account' ),

				array(
					'title' => __( 'Cart and Coupons', 'woocommerce-gift-cards' ),
					'type'  => 'title',
					'id'    => 'gc_settings_cart'
				),

				array(
					'title'    => __( 'Enable cart page features', 'woocommerce-gift-cards' ),
					'desc'     => __( 'Allow customers to apply gift cards in the cart page', 'woocommerce-gift-cards' ),
					'default'  => 'yes',
					'id'       => 'wc_gc_disable_cart_ui',
					'value'    => 'yes' === get_option( 'wc_gc_disable_cart_ui', 'yes' ) ? 'no' : 'yes',
					'type'     => 'checkbox',
					'desc_tip' => __( 'When disabled, applied gift card details will be displayed in the checkout page only.', 'woocommerce-gift-cards' ),
				),

				array(
					'title'    => __( 'Block gift card discounts', 'woocommerce-gift-cards' ),
					'desc'     => __( 'Prevent customers from using coupons to discount gift cards', 'woocommerce-gift-cards' ),
					'id'       => 'wc_gc_disable_coupons_with_gift_cards',
					'default'  => 'no',
					'type'     => 'checkbox',
					'desc_tip' => __( 'When enabled, <strong>Percentage discount</strong> and <strong>Fixed product discount</strong> coupons will not be applied to gift cards in the cart, and <strong>Fixed cart discount</strong> coupons will be rejected when purchasing gift cards.', 'woocommerce-gift-cards' ),
				),

				array( 'type' => 'sectionend', 'id' => 'gc_settings_cart' ),

			);

			if ( wc_gc_is_site_admin() ) {

				$admin_settings = array(
					array(
						'title' => __( 'Privacy', 'woocommerce-gift-cards' ),
						'type'  => 'title',
						'id'    => 'gc_settings_admin'
					),

					array(
						'title'    => __( 'Grant admin privileges to Shop Managers', 'woocommerce-gift-cards' ),
						'desc'     => __( 'Allow Shop Managers to view gift card codes', 'woocommerce-gift-cards' ),
						'id'       => 'wc_gc_unmask_codes_for_shop_managers',
						'default'  => 'no',
						'type'     => 'checkbox',
						'desc_tip' => __( 'By default, users with the Shop Manager role can see only the last 4 characters of gift card codes.', 'woocommerce-gift-cards' ),
					),

					array( 'type' => 'sectionend', 'id' => 'gc_settings_admin' ),
				);

				$settings = array_merge( $settings, $admin_settings );
			}

			return apply_filters( 'woocommerce_gc_settings', $settings );
		}

		/**
		 * Revert disable UI setting value upon save.
		 *
		 * @since 1.7.3
		 *
		 * @return array
		 */
		public function manage_disable_ui_setting( $value, $option ) {
			if ( 'wc_gc_disable_cart_ui' !== $option[ 'id' ] ) {
				return $value;
			}

			$value = 'yes' === $value ? 'no' : 'yes';
			return $value;
		}
	}

endif;

return new WC_GC_Settings();
