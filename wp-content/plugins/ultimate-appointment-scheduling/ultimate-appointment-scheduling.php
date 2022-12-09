<?php
/*
Plugin Name: Ultimate Appointment Scheduling
Plugin URI: http://www.EtoileWebDesign.com/plugins/ultimate-appointment-scheduling/
Description: Appointment booking calendar and scheduling plugin that lets you set up different services, service providers, locations and availability
Author: Etoile Web Design
Author URI: http://www.EtoileWebDesign.com/plugins/ultimate-appointment-scheduling/
Terms and Conditions: http://www.etoilewebdesign.com/plugin-terms-and-conditions/
Text Domain: ultimate-appointment-scheduling
Version: 2.1.1
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'ewduaspInit' ) ) {
class ewduaspInit {

	// Any data that needs to be passed from PHP to our JS files 
	public $front_end_php_js_data = array();

	/**
	 * Initialize the plugin and register hooks
	 */
	public function __construct() {

		self::constants();
		self::includes();
		self::instantiate();
		self::wp_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function constants() {

		define( 'EWD_UASP_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'EWD_UASP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'EWD_UASP_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
		define( 'EWD_UASP_TEMPLATE_DIR', 'ewd-uasp-templates' );
		define( 'EWD_UASP_VERSION', '2.1.1' );

		define( 'EWD_UASP_EXCEPTION_POST_TYPE', 'uasp-exception' );
		define( 'EWD_UASP_LOCATION_POST_TYPE', 'uasp-location' );
		define( 'EWD_UASP_SERVICE_POST_TYPE', 'uasp-service' );
		define( 'EWD_UASP_PROVIDER_POST_TYPE', 'uasp-provider' );
	}

	/**
	 * Include necessary classes.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function includes() {

		require_once( EWD_UASP_PLUGIN_DIR . '/includes/AdminAppointments.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Ajax.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Appointment.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/AppointmentManager.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Blocks.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/CustomPostTypes.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Dashboard.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/DeactivationSurvey.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Export.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Helper.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Import.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/InstallationWalkthrough.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Notifications.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Permissions.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/ReviewAsk.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/Settings.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/template-functions.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/UltimateWPMail.class.php' );
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/WooCommerce.class.php' );
	}

	/**
	 * Spin up instances of our plugin classes.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function instantiate() {

		new ewduaspDashboard();
		new ewduaspDeactivationSurvey();
		new ewduaspInstallationWalkthrough();
		new ewduaspReviewAsk();

		$this->admin_appointments	= new ewduaspAdminAppointments();
		$this->appointment_manager	= new ewduaspAppointmentManager();
		$this->cpts 				= new ewduaspCustomPostTypes();
		$this->notifications 		= new ewduaspNotifications();
		$this->permissions 			= new ewduaspPermissions();
		$this->settings 			= new ewduaspSettings(); 
		$this->woocommerce = new ewduaspWooCommerce();

		new ewduaspAJAX();
		new ewduaspBlocks();
		new ewduaspExport();
		new ewduaspImport();
		new ewduaspUltimateWPMail();
	}

	/**
	 * Run walk-through, load assets, add links to plugin listing, etc.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {

		register_activation_hook( __FILE__, 	array( $this, 'run_walkthrough' ) );
		register_activation_hook( __FILE__, 	array( $this, 'convert_options' ) );
		register_activation_hook( __FILE__, 	array( $this, 'create_tables' ) );

		add_action( 'init',			        	array( $this, 'load_view_files' ) );

		add_action( 'plugins_loaded',        	array( $this, 'load_textdomain' ) );

		add_action( 'admin_notices', 			array( $this, 'display_header_area' ) );
		add_action( 'admin_notices', 			array( $this, 'maybe_display_helper_notice' ) );

		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_assets' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', 		array( $this, 'register_assets' ) );
		add_action( 'wp_head',					'ewd_add_frontend_ajax_url' );
		add_action( 'wp_footer', 				array( $this, 'assets_footer' ), 2 );

		add_filter( 'plugin_action_links',		array( $this, 'plugin_action_links' ), 10, 2);

		add_action( 'wp_ajax_ewd_uasp_hide_helper_notice', array( $this, 'hide_helper_notice' ) );
	}

	/**
	 * Run the options conversion function on update if necessary
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	public function convert_options() {
		
		require_once( EWD_UASP_PLUGIN_DIR . '/includes/BackwardsCompatibility.class.php' );
		new ewduaspBackwardsCompatibility();
	}

	/**
	 * Creates the tables where appointments and their meta information are stored
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	public function create_tables() {

		$this->appointment_manager->create_tables();
	}

	/**
	 * Load files needed for views
	 * @since 2.0.0
	 * @note Can be filtered to add new classes as needed
	 */
	public function load_view_files() {
	
		$files = array(
			EWD_UASP_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
		);
	
		$files = apply_filters( 'ewd_uasp_load_view_files', $files );
	
		foreach( $files as $file ) {
			require_once( $file );
		}
	
	}

	/**
	 * Load the plugin textdomain for localisation
	 * @since 2.0.0
	 */
	public function load_textdomain() {
		
		load_plugin_textdomain( 'ultimate-appointment-scheduling', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Set a transient so that the walk-through gets run
	 * @since 2.0.0
	 */
	public function run_walkthrough() {

		set_transient( 'ewd-uasp-getting-started', true, 30 );
	} 

	/**
	 * Enqueue the admin-only CSS and Javascript
	 * @since 2.0.0
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post;

		wp_enqueue_script( 'ewd-uasp-helper-notice', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-helper-install-notice.js', array( 'jquery' ), EWD_UASP_VERSION, true );
		wp_localize_script(
			'ewd-uasp-helper-notice',
			'ewd_uasp_helper_notice',
			array( 'nonce' => wp_create_nonce( 'ewd-uasp-helper-notice' ) )
		);

		wp_enqueue_style( 'ewd-uasp-helper-notice', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-helper-install-notice.css', array(), EWD_UASP_VERSION );

		$screen = get_current_screen();

		$candidates = array(
			EWD_UASP_LOCATION_POST_TYPE,
			EWD_UASP_SERVICE_POST_TYPE,
			EWD_UASP_PROVIDER_POST_TYPE,
			EWD_UASP_EXCEPTION_POST_TYPE,

			'appointments_page_ewd-uasp-dashboard',
			'toplevel_page_ewd-uasp-appointments',
			'appointments_page_ewd-uasp-add-edit-appointment',
			'appointments_page_ewd-uasp-import',
			'appointments_page_ewd-uasp-export',
			'appointments_page_ewd-uasp-settings',

			'widgets.php',
		);

   		// Return if not UASP post types, we're not on a post-type page, or we're not on the settings or widget pages
		if ( ! in_array( $hook, $candidates )
			and ( empty( $screen->post_type ) or ! in_array ( $screen->post_type, $candidates ) )
			and ! in_array( $screen->id, $candidates )
		) {
			return;
		}

		wp_enqueue_style( 'ewd-uasp-admin-css', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-admin.css', array(), EWD_UASP_VERSION );
		wp_enqueue_script( 'ewd-uasp-admin-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-admin.js', array( 'jquery' ), EWD_UASP_VERSION, true );

		$settings = array(
			'nonce' => wp_create_nonce( 'ewd-uasp-admin-js' ),
		);

		wp_localize_script( 'ewd-uasp-admin-js', 'ewd_uasp_admin_php_data', $settings );
	}

	/**
	 * Register the front-end CSS and Javascript for the FAQs
	 * @since 2.0.0
	 */
	function register_assets() {
		global $ewd_uasp_controller;

		$calendar_language = $ewd_uasp_controller->settings->get_setting( 'calendar-language' );

		wp_register_style( 'ewd-uasp-css', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp.css', EWD_UASP_VERSION );
		wp_register_style( 'ewd-uasp-jquery-ui', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-jquery-ui.css', EWD_UASP_VERSION );
		wp_register_style( 'full-calendar', EWD_UASP_PLUGIN_URL . '/assets/css/fullcalendar.css', EWD_UASP_VERSION );
		
		wp_register_script( 'ewd-uasp-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp.js', array( 'jquery' ), EWD_UASP_VERSION, true );
		wp_localize_script(
			'ewd-uasp-js',
			'ewd_uasp',
			array(
				'nonce' => wp_create_nonce( 'ewd-uasp-js' )
			)
		);

		wp_register_script( 'full-calendar', EWD_UASP_PLUGIN_URL . '/assets/js/fullcalendar.js', array( 'jquery' ), EWD_UASP_VERSION, true );
		
		if( $calendar_language != "en" ) {

			wp_register_script( 'ewd-uasp-calendar-locale', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-fc-locales/' . $calendar_language . '.js', array( 'jquery', 'full-calendar' ) );
		}

		wp_register_script( 'ewd-uasp-calendar-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-calendar.js', array( 'jquery', 'full-calendar' ), EWD_UASP_VERSION, true );

		wp_localize_script(
			'ewd-uasp-calendar-js',
			'ewd_uasp_calendar',
				array(
				'nonce' => wp_create_nonce( 'ewd-uasp-calendar-js' )
			)
		);
	}

	/**
	 * Print out any PHP data needed for our JS to work correctly
	 * @since 2.1.0
	 */
	public function assets_footer() {

		if ( empty( $this->front_end_php_js_data ) ) { return; }

		$print_variables = array();

		foreach ( (array) $this->front_end_php_js_data as $variable => $values ) {

			if ( empty( $values ) ) { continue; }

			$print_variables[ $variable ] = ewduaspHelper::escape_js_recursive( $values );
		}

		foreach ( $print_variables as $variable => $values ) {

			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo 'var ' . esc_attr( $variable ) . ' = ' . wp_json_encode( $values ) . "\n";
			echo "/* ]]> */\n";
			echo "</script>\n";
		}
	}

	/**
	 * Adds a variable to be passed to our front-end JS
	 * @since 2.1.0
	 */
	public function add_front_end_php_data( $handle, $variable, $data ) {

		$this->front_end_php_js_data[ $variable ] = $data;
	}

	/**
	 * Returns the corresponding front-end JS variable if it exists, otherwise an empty array
	 * @since 2.1.0
	 */
	public function get_front_end_php_data( $handle, $variable ) {

		return ! empty( $this->front_end_php_js_data[ $variable ] ) ? $this->front_end_php_js_data[ $variable ] : array();
	}

	/**
	 * Add links to the plugin listing on the installed plugins page
	 * @since 2.0.0
	 */
	public function plugin_action_links( $links, $plugin ) {

		if ( $plugin == EWD_UASP_PLUGIN_FNAME ) {

			$links['settings'] = '<a href="admin.php?page=ewd-uasp-settings" title="' . __( 'Head to the settings page for Ultimate Appointment Scheduling', 'ultimate-appointment-scheduling' ) . '">' . __( 'Settings', 'ultimate-appointment-scheduling' ) . '</a>';
		}

		return $links;

	}



	/**
	 * Adds in a menu bar for the plugin
	 * @since 2.0.0
	 */
	public function display_header_area() {
		global $ewd_uasp_controller;

		$screen = get_current_screen();
		
		if ( empty( $screen->parent_file ) or $screen->parent_file != 'ewd-uasp-appointments' ) { return; }
		
		if ( ! $ewd_uasp_controller->permissions->check_permission( 'styling' ) or get_option( 'EWD_UASP_Trial_Happening' ) == 'Yes' ) {
			?>
			<div class="ewd-uasp-dashboard-new-upgrade-banner">
				<div class="ewd-uasp-dashboard-banner-icon"></div>
				<div class="ewd-uasp-dashboard-banner-buttons">
					<a class="ewd-uasp-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" target="_blank">UPGRADE NOW</a>
				</div>
				<div class="ewd-uasp-dashboard-banner-text">
					<div class="ewd-uasp-dashboard-banner-title">
						GET FULL ACCESS WITH OUR PREMIUM VERSION
					</div>
					<div class="ewd-uasp-dashboard-banner-brief">
						Add premium appointment booking functionality to your site
					</div>
				</div>
			</div>
			<?php
		}
		
		?>
		<div class="ewd-uasp-admin-header-menu">
			<h2 class="nav-tab-wrapper">
			<a id="ewd-uasp-dash-mobile-menu-open" href="#" class="menu-tab nav-tab"><?php _e("MENU", 'ultimate-appointment-scheduling'); ?><span id="ewd-uasp-dash-mobile-menu-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-uasp-dash-mobile-menu-up-caret">&nbsp;&nbsp;&#9650;</span></a>
			<a id="dashboard-menu" href='admin.php?page=ewd-uasp-dashboard' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-location_ewd-uasp-dashboard' ) {echo 'nav-tab-active';}?>"><?php _e("Dashboard", 'ultimate-appointment-scheduling'); ?></a>
			<a id="appointments-menu" href='admin.php?page=ewd-uasp-appointments' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-location_ewd-uasp-appointments' ) {echo 'nav-tab-active';}?>"><?php _e("Appointments", 'ultimate-appointment-scheduling'); ?></a>
			<a id="location-menu" href='edit.php?post_type=uasp-location' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-location' ) {echo 'nav-tab-active';}?>"><?php _e("Locations", 'ultimate-appointment-scheduling'); ?></a>
			<a id="service-menu" href='edit.php?post_type=uasp-service' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-service' ) {echo 'nav-tab-active';}?>"><?php _e("Services", 'ultimate-appointment-scheduling'); ?></a>
			<a id="provider-menu" href='edit.php?post_type=uasp-provider' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-provider' ) {echo 'nav-tab-active';}?>"><?php _e("Providers", 'ultimate-appointment-scheduling'); ?></a>
			<a id="exception-menu" href='edit.php?post_type=uasp-exception' class="menu-tab nav-tab <?php if ( $screen->id == 'uasp-exception' ) {echo 'nav-tab-active';}?>"><?php _e("Exceptions", 'ultimate-appointment-scheduling'); ?></a>
			<a id="export-menu" href='admin.php?page=ewd-uasp-export' class="menu-tab nav-tab <?php if ( $screen->id == 'ewd-uasp-export' ) {echo 'nav-tab-active';}?>"><?php _e("Export", 'ultimate-appointment-scheduling'); ?></a>
			<a id="import-menu" href='admin.php?page=ewd-uasp-import' class="menu-tab nav-tab <?php if ( $screen->id == 'ewd-uasp-import' ) {echo 'nav-tab-active';}?>"><?php _e("Import", 'ultimate-appointment-scheduling'); ?></a>
			<a id="options-menu" href='admin.php?page=ewd-uasp-settings' class="menu-tab nav-tab <?php if ( $screen->id == 'ewd_uasp_page_ewd-uasp-settings' ) {echo 'nav-tab-active';}?>"><?php _e("Settings", 'ultimate-appointment-scheduling'); ?></a>
			</h2>
		</div>
		<?php
	}

	public function maybe_display_helper_notice() {
		global $ewd_uasp_controller;

		if ( empty( $ewd_uasp_controller->permissions->check_permission( 'premium' ) ) ) { return; }

		if ( is_plugin_active( 'ewd-premium-helper/ewd-premium-helper.php' ) ) { return; }

		if ( get_transient( 'ewd-helper-notice-dismissed' ) ) { return; }

		?>

		<div class='notice notice-error is-dismissible ewd-uasp-helper-install-notice'>
			
			<div class='ewd-uasp-helper-install-notice-img'>
				<img src='<?php echo EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/img/options-asset-exclamation.png' ; ?>' />
			</div>

			<div class='ewd-uasp-helper-install-notice-txt'>
				<?php _e( 'You\'re using the Ultimate Appointment Scheduling premium version, but the premium helper plugin is not active.', 'ultimate-appointment-scheduling' ); ?>
				<br />
				<?php echo sprintf( __( 'Please re-activate the helper plugin, or <a target=\'_blank\' href=\'%s\'>download and install it</a> if the plugin is no longer installed to ensure continued access to the premium features of the plugin.', 'ultimate-appointment-scheduling' ), 'https://www.etoilewebdesign.com/2021/12/11/requiring-premium-helper-plugin/' ); ?>
			</div>

			<div class='ewd-uasp-clear'></div>

		</div>

		<?php 
	}

	public function hide_helper_notice() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-helper-notice', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		set_transient( 'ewd-helper-notice-dismissed', true, 3600*24*7 );

		die();
	}

}
} // endif;

global $ewd_uasp_controller;
$ewd_uasp_controller = new ewduaspInit();

do_action( 'ewd_uasp_initialized' );