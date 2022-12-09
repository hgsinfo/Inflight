<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Woo_Product_Widgets_Elementor_Settings' ) ) {

	/**
	 * Define Woo_Product_Widgets_Elementor_Settings class
	 */
	class Woo_Product_Widgets_Elementor_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'woo-products-widgets-settings';

		/**
		 * [$widgets description]
		 * @var null
		 */
		public $widgets  = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * Global Available Widgets array
		 *
		 * @var array
		 */
		public $global_available_widgets = array();

		/**
		 * Init page
		 */
		public function init() {

			add_action( 'admin_enqueue_scripts', array( $this, 'init_widgets' ), 0 );
			add_action( 'admin_menu', array( $this, 'register_page' ), 99 );
			add_action( 'init', array( $this, 'save' ), 40 );
			add_action( 'admin_notices', array( $this, 'saved_notice' ) );

			foreach ( glob( woo_product_widgets_elementor()->plugin_path( 'includes/widgets/global/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class'=>'Class', 'name' => 'Name', 'slug'=>'Slug' ) );

				$slug = basename( $file, '.php' );
				$this->global_available_widgets[ $slug] = $data['name'];
			}

		}

		/**
		 * Initialize page widgets module if reqired
		 *
		 * @return [type] [description]
		 */
		public function init_widgets() {

			if ( ! isset( $_REQUEST['page'] ) || $this->key !== $_REQUEST['page'] ) {
				return;
			}

			$widgets_data = woo_product_widgets_elementor()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			$this->widgets = new CX_Interface_Builder(
				array(
					'path' => $widgets_data['path'],
					'url'  => $widgets_data['url'],
				)
			);

		}

		/**
		 * Show saved notice
		 *
		 * @return bool
		 */
		public function saved_notice() {

			if ( ! isset( $_GET['settings-saved'] ) ) {
				return false;
			}

			$message = esc_html__( 'Settings saved', 'woo-product-widgets-for-elementor' );

			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );

			return true;

		}

		/**
		 * Save settings
		 *
		 * @return void
		 */
		public function save() {

			if ( ! isset( $_REQUEST['page'] ) || $this->key !== $_REQUEST['page'] ) {
				return;
			}

			if ( ! isset( $_REQUEST['action'] ) || 'save-settings' !== $_REQUEST['action'] ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$current = get_option( $this->key, array() );
			$data    = $_REQUEST;

			unset( $data['action'] );

			foreach ( $data as $key => $value ) {
				$current[ $key ] = is_array( $value ) ? $value : esc_attr( $value );
			}

			update_option( $this->key, $current );

			$redirect = add_query_arg(
				array( 'dialog-saved' => true ),
				$this->get_settings_page_link()
			);

			wp_redirect( $redirect );
			die();

		}

		/**
		 * Return settings page URL
		 *
		 * @return string
		 */
		public function get_settings_page_link() {

			return add_query_arg(
				array(
					'page' => $this->key,
				),
				esc_url( admin_url( 'admin.php' ) )
			);

		}

		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;

		}

		/**
		 * Register add/edit page
		 *
		 * @return void
		 */
		public function register_page() {

			add_submenu_page(
				'elementor',
				esc_html__( 'Woo Product Widgets Settings', 'woo-product-widgets-for-elementor' ),
				esc_html__( 'Woo Product Widgets Settings', 'woo-product-widgets-for-elementor' ),
				'manage_options',
				$this->key,
				array( $this, 'render_page' )
			);

		}

		/**
		 * Render settings page
		 *
		 * @return void
		 */
		public function render_page() {

			foreach ( $this->global_available_widgets as $key => $value ) {
				$default_global_available_widgets[ $key ] = 'true';
			}

			$this->widgets->register_section(
				array(
					'woo_products_widgets_settings' => array(
						'type'   => 'section',
						'scroll' => false,
						'title'  => esc_html__( 'Woo Elementor Product Widgets Settings', 'woo-product-widgets-for-elementor' ),
					),
				)
			);

			$this->widgets->register_form(
				array(
					'woo_products_widgets_settings_form' => array(
						'type'   => 'form',
						'parent' => 'woo_products_widgets_settings',
						'action' => add_query_arg(
							array( 'page' => $this->key, 'action' => 'save-settings' ),
							esc_url( admin_url( 'admin.php' ) )
						),
					),
				)
			);

			$this->widgets->register_settings(
				array(
					'settings_top' => array(
						'type'   => 'settings',
						'parent' => 'woo_products_widgets_settings_form',
					),
					'settings_bottom' => array(
						'type'   => 'settings',
						'parent' => 'woo_products_widgets_settings_form',
					),
				)
			);

			$this->widgets->register_component(
				array(
					'woo_products_widgets_tab_vertical' => array(
						'type'   => 'component-tab-vertical',
						'parent' => 'settings_top',
					),
				)
			);

			$this->widgets->register_settings(
				array(
					'available_widgets_options' => array(
						'parent'      => 'woo_products_widgets_tab_vertical',
						'title'       => esc_html__( 'Available Widgets', 'woo-product-widgets-for-elementor' ),
					),
				)
			);

			$this->widgets->register_control(
				array(
					'global_available_widgets' => array(
						'type'        => 'checkbox',
						'id'          => 'global_available_widgets',
						'name'        => 'global_available_widgets',
						'parent'      => 'available_widgets_options',
						'value'       => $this->get( 'global_available_widgets', $default_global_available_widgets ),
						'options'     => $this->global_available_widgets,
						'title'       => esc_html__( 'Global Available Widgets', 'woo-product-widgets-for-elementor' ),
						'description' => esc_html__( 'List of widgets that will be available when editing the page', 'woo-product-widgets-for-elementor' ),
						'class'       => 'woo_products_widgets_settings_form__checkbox-group'
					),
				)
			);

			$this->widgets->register_settings(
				array(
					'product_thumb_effect_options' => array(
						'parent' => 'woo_products_widgets_tab_vertical',
						'title'  => esc_html__( 'Product Thumb Effect', 'woo-product-widgets-for-elementor' ),
					),
				)
			);

			$this->widgets->register_control(
				array(
					'enable_product_thumb_effect' => array(
						'type'        => 'switcher',
						'id'          => 'enable_product_thumb_effect',
						'name'        => 'enable_product_thumb_effect',
						'parent'      => 'product_thumb_effect_options',
						'title'       => esc_html__( 'Enable Thumbnails Effect', 'woo-product-widgets-for-elementor' ),
						'description' => esc_html__( 'Enable thumbnails switch on hover', 'woo-product-widgets-for-elementor' ),
						'value'       => $this->get( 'enable_product_thumb_effect' ),
						'toggle'      => array(
							'true_toggle'  => 'On',
							'false_toggle' => 'Off',
						),
					),
				)
			);

			$this->widgets->register_control(
				array(
					'product_thumb_effect' => array(
						'type'    => 'select',
						'id'      => 'product_thumb_effect',
						'name'    => 'product_thumb_effect',
						'parent'  => 'product_thumb_effect_options',
						'value'   => $this->get( 'product_thumb_effect', 'slide-left' ),
						'options' => array(
							'slide-left'     => esc_html__( 'Slide Left', 'woo-product-widgets-for-elementor' ),
							'slide-right'    => esc_html__( 'Slide Right', 'woo-product-widgets-for-elementor' ),
							'slide-top'      => esc_html__( 'Slide Top', 'woo-product-widgets-for-elementor' ),
							'slide-bottom'   => esc_html__( 'Slide Bottom', 'woo-product-widgets-for-elementor' ),
							'fade'           => esc_html__( 'Fade', 'woo-product-widgets-for-elementor' ),
							'fade-with-zoom' => esc_html__( 'Fade With Zoom', 'woo-product-widgets-for-elementor' ),
						),
						'title'   => esc_html__( 'Thumbnails Effect:', 'woo-product-widgets-for-elementor' ),
					)
				)
			);

			$this->widgets->register_settings(
				array(
					'product_common_options' => array(
						'parent' => 'woo_products_widgets_tab_vertical',
						'title'  => esc_html__( 'Product Common', 'woo-product-widgets-for-elementor' ),
					),
				)
			);

			$this->widgets->register_control(
				array(
					'product_max_limit' => array(
						'type'   => 'text',
						'id'      => 'product_max_limit',
						'name'    => 'product_max_limit',
						'parent' => 'product_common_options',	
						'value'   => $this->get( 'product_max_limit', '50' ),	
						'title'   => esc_html__( 'Maximum Product Limits:', 'woo-product-widgets-for-elementor' ),
					),
				)
			);			

			$this->widgets->register_control(
				array(
					'cate_max_limit' => array(
						'type'   => 'text',
						'id'      => 'cate_max_limit',
						'name'    => 'cate_max_limit',
						'parent' => 'product_common_options',	
						'value'   => $this->get( 'cate_max_limit', '50' ),	
						'title'   => esc_html__( 'Maximum Category Limits:', 'woo-product-widgets-for-elementor' ),
					),
				)
			);

			$this->widgets->register_html(
				array(
					'save_button' => array(
						'type'   => 'html',
						'parent' => 'settings_bottom',
						'class'  => 'cx-component dialog-save',
						'html'   => '<button type="submit" class="button button-primary">' . esc_html__( 'Save', 'woo-product-widgets-for-elementor' ) . '</button>',
					),
				)
			);


			echo '<div class="woo-products-widgets-settings-page">';
				$this->widgets->render();
				$this->render_banner_html();
			echo '</div>';
		}

		/**
		 * Render banner html.
		 */
		public function render_banner_html() {
			$html = '<div class="woo-products-widgets-banner">
						<a class="woo-products-widgets-banner__link" href="https://www.themelocation.com/woo-products-widgets-for-elementor/" target="_blank">
							<img class="woo-products-widgets-banner__img" src="%1$s" alt="%2$s">
						</a>
					</div>';
			printf( $html, woo_product_widgets_elementor()->plugin_url( 'assets/images/banner.png' ), esc_attr__( 'Themelocation', 'woo-product-widgets-for-elementor' ) );
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

/**
 * Returns instance of Woo_Product_Widgets_Elementor_Settings
 *
 * @return object
 */
function woo_elementor_product_widgets_settings() {
	return Woo_Product_Widgets_Elementor_Settings::get_instance();
}

