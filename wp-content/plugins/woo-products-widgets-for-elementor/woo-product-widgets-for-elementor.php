<?php
/**
 * Plugin Name: Woo Products Widgets For Elementor
 * Description: WooCommerce Products widget for Elementor Page Builder
 * Version:     1.0.6
 * Author:      Themelocation
 * Author URI:  https://themelocation.com/
 * Text Domain: woo-product-widgets-for-elementor
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( ! function_exists( 'elepwwfe_fs' ) ) {
    // Create a helper function for easy SDK access.
    function elepwwfe_fs() {
        global $elepwwfe_fs;

        if ( ! isset( $elepwwfe_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $elepwwfe_fs = fs_dynamic_init( array(
                'id'                  => '3953',
                'slug'                => 'woo-product-widgets-for-elementor',
                'type'                => 'plugin',
                'public_key'          => 'pk_be3cc82c7eda35948c5e22f1a3379',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'woo-products-widgets-settings',
                    'support'        => false,
                    'parent'         => array(
                        'slug' => 'elementor',
                    ),
                ),
            ) );
        }

        return $elepwwfe_fs;
    }

    // Init Freemius.
    elepwwfe_fs();
    // Signal that SDK was initiated.
    do_action( 'elepwwfe_fs_loaded' );
}

// If class `Woo_Elementor_Product_Widgets` doesn't exists yet.
if ( ! class_exists( 'Woo_Elementor_Product_Widgets' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Woo_Elementor_Product_Widgets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '1.0.0';

		/**
		 * Framework loader instance
		 *
		 * @var object
		 */
		public $framework;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load the core functions/classes required by the rest of the plugin.
			add_action( 'after_setup_theme', array( $this, 'load_framework' ), -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );
			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Load plugin framework
		 */
		public function load_framework() {

			require $this->plugin_path( 'framework/loader.php' );

			$this->framework = new Woo_Elementor_Product_Widgets_CX_Loader(
				array(
					$this->plugin_path( 'framework/interface-builder/cherry-x-interface-builder.php' ),
					$this->plugin_path( 'framework/post-meta/cherry-x-post-meta.php' ),
				)
			);

		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {

			if ( class_exists( 'WooCommerce' ) ) {

				$this->load_files();

				woo_elementor_products_widgets_assets()->init();
				woo_elementor_product_widgets_integration()->init();

				woo_elementor_product_widgets_settings()->init();
				woo_elementor_products_widgets_shortocdes()->init();

			}

			if ( is_admin() ) {
				if ( ! $this->has_elementor() ) {
					$this->required_plugins_notice();
				}
			}

		}

		/**
		 * Show recommended plugins notice.
		 *
		 * @return void
		 */
		public function required_plugins_notice() {
			require $this->plugin_path( 'includes/lib/class-tgm-plugin-activation.php' );
			add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
		}

		/**
		 * Register required plugins
		 *
		 * @return void
		 */
		public function register_required_plugins() {

			$plugins = array(
				array(
					'name'     => 'Elementor',
					'slug'     => 'elementor',
					'required' => true,
				),
				array(
					'name'     => 'WooCommerce',
					'slug'     => 'woocommerce',
					'required' => true,
				),
			);

			$config = array(
				'id'           => 'woo-product-widgets-for-elementor',
				'default_path' => '',
				'menu'         => 'woo-elementor-product-widgets-install-plugins',
				'parent_slug'  => 'plugins.php',
				'capability'   => 'manage_options',
				'has_notices'  => true,
				'dismissable'  => true,
				'dismiss_msg'  => '',
				'is_automatic' => false,
				'strings'      => array(
					'notice_can_install_required'     => _n_noop(
						'Woo Product Widgets for Elementor requires the following plugin: %1$s.',
						'Woo Product Widgets for Elementor requires the following plugins: %1$s.',
						'woo-product-widgets-for-elementor'
					),
					'notice_can_install_recommended'  => _n_noop(
						'Woo Product Widgets for Elementor recommends the following plugin: %1$s.',
						'Woo Product Widgets for Elementor recommends the following plugins: %1$s.',
						'woo-product-widgets-for-elementor'
					),
				),
			);

			tgmpa( $plugins, $config );

		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Returns utility instance
		 *
		 * @return object
		 */
		public function utility() {
			$utility = $this->get_core()->modules['cherry-utility'];
			return $utility->utility;
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/class-woo-product-widgets-assets.php' );
			require $this->plugin_path( 'includes/class-woo-product-widgets-tools.php' );

			require $this->plugin_path( 'includes/integrations/base/class-woo-product-widgets-integration.php' );

			require $this->plugin_path( 'includes/class-woo-product-widgets-template-functions.php' );
			
			require $this->plugin_path( 'includes/class-woo-product-widgets-shortcodes.php' );

			require $this->plugin_path( 'includes/settings/class-woo-product-widgets-settings.php' );
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'woo-product-widgets-for-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'woo-product-widgets-for-elementor/template-path', 'woo-product-widgets-for-elementor/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
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

if ( ! function_exists( 'woo_product_widgets_elementor' )  ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function woo_product_widgets_elementor() {
		return Woo_Elementor_Product_Widgets::get_instance();
	}
}

woo_product_widgets_elementor(); 