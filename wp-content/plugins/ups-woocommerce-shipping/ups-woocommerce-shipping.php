<?php
/*
	Plugin Name: WooCommerce UPS Shipping Plugin with Print Label
	Plugin URI: https://www.pluginhive.com/product/woocommerce-ups-shipping-plugin-with-print-label/
	Description: Lets you completely automate UPS shipping. Display live shipping rates on WooCommerce Cart & Checkout pages, Pay Postage, Print Labels Manually or in Bulk, Track shipments, and more.
	Version: 4.2.8
	Author: PluginHive
	Author URI: https://www.pluginhive.com/about/
	WC requires at least: 3.0.0
	WC tested up to: 5.6.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define PH_UPS_PLUGIN_VERSION
if ( !defined( 'PH_UPS_PLUGIN_VERSION' ) )
{
	define( 'PH_UPS_PLUGIN_VERSION', '4.2.8' );
}

// Include API Manager
if ( !class_exists( 'PH_UPS_API_Manager' ) ) {

	include_once( 'ph-api-manager/ph_api_manager_ups.php' );
}

if ( class_exists( 'PH_UPS_API_Manager' ) ) {

	$ph_ups_api_obj 	= new PH_UPS_API_Manager( __FILE__, '', PH_UPS_PLUGIN_VERSION, 'plugin', 'https://www.pluginhive.com/', 'UPS' );
}

// Include Common Class
if( ! class_exists('Ph_UPS_Woo_Shipping_Common') ) {
	require_once 'class-ph-ups-woo-shipping-common.php';
}

function wf_ups_pre_activation_check(){
	//check if basic version is there
	if ( is_plugin_active('ups-woocommerce-shipping-method/ups-woocommerce-shipping.php') ){
        deactivate_plugins( basename( __FILE__ ) );
		wp_die( __("Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete UPS(Basic) Woocommerce Extension and then try again", "ups-woocommerce-shipping" ), "", array('back_link' => 1 ));
	}
}
register_activation_hook( __FILE__, 'wf_ups_pre_activation_check' );

// Required functions
if ( ! function_exists( 'wf_is_woocommerce_active' ) ) {
	require_once( 'wf-includes/wf-functions.php' );
}
// WC active check
if ( ! wf_is_woocommerce_active() ) {
	return;
}

if (!defined('WF_UPS_ID')){
	define("WF_UPS_ID", "wf_shipping_ups");
}

if (!defined('WF_UPS_ADV_DEBUG_MODE')){
	define("WF_UPS_ADV_DEBUG_MODE", "off"); // Turn 'on' for demo/test sites.
}

// Setting active plugins as global variable for further usage
global $xa_active_plugins;
if( empty($xa_active_plugins) ) {
	$xa_active_plugins = get_option( 'active_plugins');
}

/**
 * WC_UPS class
 */
if(!class_exists('UPS_WooCommerce_Shipping')){
	class UPS_WooCommerce_Shipping {
		/**
		 * Constructor
		 */
		public function __construct() {

			global $xa_active_plugins;
			$this->active_plugins = $xa_active_plugins;			// Array of all active plugins

			$this->ph_init();

			add_action( 'init', array( $this, 'init' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wf_plugin_action_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'wf_shipping_init') );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'wf_ups_add_method') );
			add_action( 'admin_enqueue_scripts', array( $this, 'wf_ups_scripts') );
		}

		public function ph_init() {

			if ( is_admin() ) {
				
				// Product Level Fields
				include_once ( 'includes/class-wf-ups-admin-options.php' );
			}
		}

		public function init() {
			//To support third party plugins
			$this->third_party_plugin_support();

			include('includes/wf-automatic-label-generation.php');
			
			if ( ! class_exists( 'wf_order' ) ) {
		  		include_once 'includes/class-wf-legacy.php';
		  	}
			// Add Notice Class
			include_once ( 'includes/class-wf-admin-notice.php' );
			// WF Print Shipping Label.
			include_once ( 'includes/class-wf-shipping-ups-admin.php' );
			
			include_once ( 'includes/class-wf-ups-accesspoint-locator.php' );

			include_once ( 'includes/class-ph-ups-custom-fields.php' );

			if ( is_admin() ) {
				
				//include pickup functionality
				include_once ( 'includes/class-wf-ups-pickup-admin.php' );
				
			}
			// Localisation
			load_plugin_textdomain( 'ups-woocommerce-shipping', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/' );
		}

		/**
		 * It will decide that which third party plugin support file has to be included depending on active plugins.
		 */
		public function third_party_plugin_support() {

			// To Support Woocommerce Bundle Product Plugin
			if( in_array( 'woocommerce-product-bundles/woocommerce-product-bundles.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-bundle-product-support.php';
			}
			// For Woocommerce Composite Product plugin
			if( in_array( 'woocommerce-composite-products/woocommerce-composite-products.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-composite-product-support.php';
			}
			// For Woocommerce Shipping Multiple Address
			if( in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-address.php', $this->active_plugins) || in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-addresses.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-shipping-multiple-address.php';
			}
			// For Multicurrency for woocommerce
			if( in_array( 'woo-multi-currency/woo-multi-currency.php', $this->active_plugins) || in_array( 'woo-multi-currency-pro/woo-multi-currency-pro.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-multicurrency-for-woocommerce.php';
			}
			// For woocommerce subscriptions Plugin
			if( in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-subscription.php';
			}
			// For Woocommerce Multi-Currency
			if( in_array( 'woocommerce-multicurrency/woocommerce-multicurrency.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-multicurrency.php';
			}
			// For WooCommerce Mix and Match Products
			if( in_array( 'woocommerce-mix-and-match-products/woocommerce-mix-and-match-products.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-mix-and-match.php';
			}
			// For Woocommerce Blocks plugin
			if( in_array( 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php', $this->active_plugins) ) {
				require_once 'includes/third-party-plugin-support/func-ph-ups-woocommerce-blocks.php';
			}
		}

		/**
		 * Plugin page links
		 */
		public function wf_plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wf_shipping_ups' ) . '">' . __( 'Settings', 'ups-woocommerce-shipping' ) . '</a>',
				'<a href="https://www.pluginhive.com/knowledge-base/category/woocommerce-ups-shipping-plugin-with-print-label/" target="_blank">' . __('Documentation', 'ups-woocommerce-shipping') . '</a>',
	            '<a href="https://www.pluginhive.com/support/" target="_blank">' . __('Support', 'ups-woocommerce-shipping') . '</a>'
			);
			return array_merge( $plugin_links, $links );
		}
		
		/**
		 * wc_ups_init function.
		 *
		 * @access public
		 * @return void
		 */
		function wf_shipping_init() {
			include_once( 'includes/class-wf-shipping-ups.php' );
		}

		/**
		 * wc_ups_add_method function.
		 *
		 * @access public
		 * @param mixed $methods
		 * @return void
		 */
		function wf_ups_add_method( $methods ) {
			$methods[] = 'WF_Shipping_UPS';
			return $methods;
		}

		/**
		 * wc_ups_scripts function.
		 *
		 * @access public
		 * @return void
		 */
		function wf_ups_scripts() {
			wp_enqueue_script( 'jquery-ui-sortable' );
			// wp_enqueue_script( 'wf-ups-common-script', plugins_url( '/resources/js/wf_common.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wf-ups-script', plugins_url( '/resources/js/wf_ups.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style( 'ph-ups-common-style', plugins_url( '/resources/css/wf_common_style.css', __FILE__ ));
		}
	}
}
new UPS_WooCommerce_Shipping();

/* Add a new country to countries list */
if(!function_exists('wf_add_puert_rico_country')){
	function wf_add_puert_rico_country( $country ) {
	  $country["PR"] = 'Puerto Rico';  
		return $country; 
	}
	add_filter( 'woocommerce_countries', 'wf_add_puert_rico_country', 10, 1 );
}
