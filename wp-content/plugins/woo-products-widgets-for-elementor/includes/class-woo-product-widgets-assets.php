<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Woo_Product_Widgets_Elementor_Assets' ) ) {

	/**
	 * Define Woo_Product_Widgets_Elementor_Assets class
	 */
	class Woo_Product_Widgets_Elementor_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function init() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action('elementor/frontend/after_register_scripts', array($this, 'register_frontend_scripts'), 10);
			add_action( 'elementor/frontend/after_enqueue_scripts', array( 'WC_Frontend_Scripts', 'localize_printed_scripts' ), 5 );
			add_action( 'admin_enqueue_scripts',  array( $this, 'admin_enqueue_styles' ) );
		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_styles() {
			$font_path   = WC()->plugin_url() . '/assets/fonts/';
			$inline_font = '@font-face {
			font-family: "WooCommerce";
			src: url("' . $font_path . 'WooCommerce.eot");
			src: url("' . $font_path . 'WooCommerce.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'WooCommerce.woff") format("woff"),
				url("' . $font_path . 'WooCommerce.ttf") format("truetype"),
				url("' . $font_path . 'WooCommerce.svg#WooCommerce") format("svg");
			font-weight: normal;
			font-style: normal;
			}';

			wp_register_style( 'woo-elementor-slick', woo_product_widgets_elementor()->plugin_url( 'assets/css/lib/slick.css' ) );

			wp_enqueue_style(
				'woo-product-widgets-for-elementor',
				woo_product_widgets_elementor()->plugin_url( 'assets/css/woo-products-widgets.css' ),
				false,
				woo_product_widgets_elementor()->get_version()
			);			
			
			wp_enqueue_style(
				'woo-product-widgets-for-elementor',
				woo_product_widgets_elementor()->plugin_url( 'assets/css/woo-products-widgets.css' ),
				false,
				woo_product_widgets_elementor()->get_version()
			);

			wp_add_inline_style(
				'woo-product-widgets-for-elementor',
				$inline_font
			);

		}

		/**
		 * Enqueue plugin scripts only with elementor scripts
		 *
		 * @return void
		 */
		public function register_frontend_scripts() {
			wp_register_script('woo-elementor-slick-js', woo_product_widgets_elementor()->plugin_url( 'assets/js/slick.min.js' ), array( 'jquery' ), false, true );
		}

		public function enqueue_scripts() {
			

			wp_enqueue_script(
				'woo-product-widgets-for-elementor',
				woo_product_widgets_elementor()->plugin_url( 'assets/js/woo-products-widgets.js' ),
				array( 'jquery', 'elementor-frontend' ),
				woo_product_widgets_elementor()->get_version(),
				true
			);

			wp_localize_script(
				'woo-product-widgets-for-elementor',
				'WooProWidgetsData',
				apply_filters( 'woo-product-widgets-elementor/frontend/localize-data', array() )
			);

		}

		/**
		 * Enqueue admin styles
		 *
		 * @return void
		 */
		public function admin_enqueue_styles() {
			$screen = get_current_screen();
			// Jet setting page check
			if ( 'elementor_page_woo-products-widgets-settings' === $screen->base ) {
				wp_enqueue_style(
					'woo-product-widgets-elementor-admin-css',
					woo_product_widgets_elementor()->plugin_url( 'assets/css/woo-products-woo-widgets-admin.css' ),
					false,
					woo_product_widgets_elementor()->get_version()
				);
			}
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Woo_Product_Widgets_Elementor_Assets
 *
 * @return object
 */
function woo_elementor_products_widgets_assets() {
	return Woo_Product_Widgets_Elementor_Assets::get_instance();
}
