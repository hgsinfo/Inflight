<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

class ewduaspInstallationWalkthrough {

	// An array holding lowercase and uppercase versions of the days of the week
	public $days = array(
		'monday' 	=> 'Monday',
		'tuesday' 	=> 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday' 	=> 'Thursday',
		'friday' 	=> 'Friday',
		'saturday' 	=> 'Saturday',
		'sunday' 	=> 'Sunday'
	);

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_install_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_install_screen_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );

		add_action( 'admin_head', array( $this, 'admin_enqueue' ) );

		add_action( 'wp_ajax_ewd_uasp_welcome_add_service', array( $this, 'create_service' ) );
		add_action( 'wp_ajax_ewd_uasp_welcome_add_location', array( $this, 'create_location' ) );
		add_action( 'wp_ajax_ewd_uasp_welcome_add_provider', array( $this, 'create_provider' ) );
		add_action( 'wp_ajax_ewd_uasp_welcome_add_booking_page', array( $this, 'add_booking_page' ) );
		add_action( 'wp_ajax_ewd_uasp_welcome_set_options', array( $this, 'set_options' ) );
	}

	/**
	 * On activation, redirect the user if they haven't used the plugin before
	 * @since 2.0.0
	 */
	public function redirect() {
		if ( ! get_transient( 'ewd-uasp-getting-started' ) ) 
			return;

		delete_transient( 'ewd-uasp-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		if ( ! empty( get_posts( array( 'post_type' => EWD_UASP_LOCATION_POST_TYPE ) ) ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=ewd-uasp-getting-started' ) );
		exit;
	}

	/**
	 * Create the installation admin page
	 * @since 2.0.0
	 */
	public function register_install_screen() {

		add_dashboard_page(
			esc_html__( 'Ultimate Appointment Scheduling - Welcome!', 'ultimate-appointment-scheduling' ),
			esc_html__( 'Ultimate Appointment Scheduling - Welcome!', 'ultimate-appointment-scheduling' ),
			'manage_options',
			'ewd-uasp-getting-started',
			array($this, 'display_install_screen')
		);
	}

	/**
	 * Hide the installation admin page from the WordPress sidebar menu
	 * @since 2.0.0
	 */
	public function hide_install_screen_menu_item() {

		remove_submenu_page( 'index.php', 'ewd-uasp-getting-started' );
	}

	/**
	 * Adds a new Ultimate Appointment Scheduling service
	 * @since 2.0.0
	 */
	public function create_service() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-getting-started', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$args = array(
	        'post_title' => isset( $_POST['service_name'] ) ? sanitize_text_field( $_POST['service_name'] ) : '',
	        'post_content' => isset( $_POST['service_description'] ) ? sanitize_text_field( $_POST['service_description'] ) : '',
	        'post_status' => 'publish',
	        'post_type' => EWD_UASP_SERVICE_POST_TYPE
	    );

	    $post_id = wp_insert_post( $args );	
	
	    if ( $post_id ) {

	        update_post_meta( $post_id, 'Service Capacity', sanitize_text_field( $_POST['service_capacity'] ) );
	        update_post_meta( $post_id, 'Service Duration', sanitize_text_field( $_POST['service_duration'] ) );
	    }

	    wp_send_json_success( 
	    	array(
	    		'post_id'	=> $post_id,
	    	)
	    );
	
	    exit();
	}

	/**
	 * Adds a new Ultimate Appointment Scheduling location
	 * @since 2.0.0
	 */
	public function create_location() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-getting-started', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$args = array(
			'post_title' => isset( $_POST['location_name'] ) ? sanitize_text_field( $_POST['location_name'] ) : '',
			'post_content' => isset( $_POST['location_description'] ) ? sanitize_text_field( $_POST['location_description'] ) : '',
			'post_status' => 'publish',
			'post_type' => EWD_UASP_LOCATION_POST_TYPE
		);

		$post_id = wp_insert_post( $args );	

		if ( $post_id ) {

			$location_openings = json_decode( stripslashes( $_POST['location_openings'] ), true );
			$location_closings = json_decode( stripslashes( $_POST['location_closings'] ), true );

			foreach ( $this->days as $lowercase_day => $day ) {

				update_post_meta( $post_id, $day . ' Open', sanitize_text_field( $location_openings[ $lowercase_day ] ) );
				update_post_meta( $post_id, $day . ' Close', sanitize_text_field( $location_closings[ $lowercase_day ] ) );
			}
		}

		wp_send_json_success(
			array(
				'post_id'	=> $post_id,
			)
		);

		die();
	}

	/**
	 * Adds a new Ultimate Appointment Scheduling provider
	 * @since 2.0.0
	 */
	public function create_provider() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-getting-started', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$args = array(
			'post_title' => isset( $_POST['provider_name'] ) ? sanitize_text_field( $_POST['provider_name'] ) : '',
			'post_content' => isset( $_POST['provider_description'] ) ? sanitize_text_field( $_POST['provider_description'] ) : '',
			'post_status' => 'publish',
			'post_type' => EWD_UASP_PROVIDER_POST_TYPE
		);

		$post_id = wp_insert_post( $args );

		if ( $post_id ) {

			$services = json_decode( stripslashes( $_POST['provider_services'] ), true );
			$services_offered = is_array( $services ) ? sanitize_text_field( implode( ',', $services ) ) : array();

			update_post_meta( $post_id, 'Service Provider Services', $services_offered );
			update_post_meta( $post_id, 'Service Provider Email', sanitize_text_field( $_POST['provider_email'] ) );

			$provider_openings = json_decode( stripslashes( $_POST['provider_openings'] ), true );
			$provider_closings = json_decode( stripslashes( $_POST['provider_closings'] ), true );
			$provider_locations = json_decode( stripslashes( $_POST['provider_locations'] ), true );

			foreach ( $this->days as $lowercase_day => $day ) {

				update_post_meta( $post_id, $day . ' Start', sanitize_text_field( $provider_openings[ $lowercase_day ] ) );
				update_post_meta( $post_id, $day . ' Finish', sanitize_text_field( $provider_closings[ $lowercase_day ] ) );
				update_post_meta( $post_id, $day . ' Location', sanitize_text_field( $provider_locations[ $lowercase_day ] ) );
			}
		}

		exit();
	}

	/**
	 * Add in a page with the ultimate-appointment-calendar shortcode
	 * @since 2.0.0
	 */
	public function add_booking_page() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-getting-started', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$args = array(
    	    'post_title' => isset($_POST['booking_page_title'] ) ? sanitize_text_field( $_POST['booking_page_title'] ) : '',
        	'post_content' => '<!-- wp:paragraph --><p> [ultimate-appointment-calendar] </p><!-- /wp:paragraph -->',
    	    'post_status' => 'publish',
    	    'post_type' => 'page'
    	);

    	wp_insert_post( $args );
	
	    exit();
	}

	/**
	 * Set a number of key options selected during the walk-through process
	 * @since 2.0.0
	 */
	public function set_options() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-getting-started', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$ewd_uasp_options = get_option( 'ewd-uasp-settings' );

		if ( isset( $_POST['multi_step_booking'] ) ) { $ewd_uasp_options['multi-step-booking'] = intval( $_POST['multi_step_booking'] ); }
		if ( isset( $_POST['time_between_appointments'] ) ) { $ewd_uasp_options['time-between-appointments'] = intval( $_POST['time_between_appointments'] ); }
		if ( isset( $_POST['hours_format'] ) ) { $ewd_uasp_options['hours-format'] = intval( $_POST['hours_format'] ); }
		if ( isset( $_POST['calendar_starting_layout'] ) ) { $ewd_uasp_options['calendar-starting-layout'] = sanitize_text_field( $_POST['calendar_starting_layout'] ); }

		update_option( 'ewd-uasp-settings', $ewd_uasp_options );

		exit();
	}

	/**
	 * Enqueue the admin assets necessary to run the walk-through and display it nicely
	 * @since 2.0.0
	 */
	public function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'ewd-uasp-getting-started' ) { return; }

		wp_enqueue_style( 'ewd-uasp-admin-css', EWD_UASP_PLUGIN_URL . '/assets/css/admin.css', array(), EWD_UASP_VERSION );
		wp_enqueue_style( 'ewd-uasp-sap-admin-css', EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/css/admin.css', array(), EWD_UASP_VERSION );
		wp_enqueue_style( 'ewd-uasp-welcome-screen', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-welcome-screen.css', array(), EWD_UASP_VERSION );
		wp_enqueue_style( 'ewd-uasp-admin-settings-css', EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/css/admin-settings.css', array(), EWD_UASP_VERSION );

		wp_enqueue_script( 'ewd-uasp-getting-started', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-welcome-screen.js', array( 'jquery' ), EWD_UASP_VERSION );
		wp_enqueue_script( 'ewd-uasp-admin-settings-js', EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/js/admin-settings.js', array( 'jquery' ), EWD_UASP_VERSION );
		wp_enqueue_script( 'ewd-uasp-admin-spectrum-js', EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/js/spectrum.js', array( 'jquery' ), EWD_UASP_VERSION );

		wp_localize_script(
			'ewd-uasp-getting-started',
			'ewd_uasp_getting_started',
			array(
				'nonce' => wp_create_nonce( 'ewd-uasp-getting-started' )
			)
		);
	}

	/**
	 * Output the HTML of the walk-through screen
	 * @since 2.0.0
	 */
	public function display_install_screen() { 
		global $ewd_uasp_controller;

		$multi_step_booking = $ewd_uasp_controller->settings->get_setting( 'multi-step-booking' );
		$time_between_appointments = $ewd_uasp_controller->settings->get_setting( 'time-between-appointments' );
		$hours_format = $ewd_uasp_controller->settings->get_setting( 'hours-format' );
		$calendar_starting_layout = $ewd_uasp_controller->settings->get_setting( 'calendar-starting-layout' );

		?>

		<div class='ewd-uasp-welcome-screen'>
			
			<div class='ewd-uasp-welcome-screen-header'>
				<h1><?php _e('Welcome to Ultimate Appointment Scheduling', 'ultimate-appointment-scheduling'); ?></h1>
				<p><?php _e('Thanks for choosing Ultimate Appointment Scheduling! The following will help you get started with the setup by creating your first service, location and service provider, as well as adding an appointment booking page and configuring a few key options.', 'ultimate-appointment-scheduling'); ?></p>
			</div>

			<div class='ewd-uasp-welcome-screen-box ewd-uasp-welcome-screen-services ewd-uasp-welcome-screen-open' data-screen='services'>
				<h2><?php _e('1. Services', 'ultimate-appointment-scheduling'); ?></h2>
				<div class='ewd-uasp-welcome-screen-box-content'>
					<p><?php _e('Create your first services. Don\'t worry, you can always add more later.', 'ultimate-appointment-scheduling'); ?></p>
					<table class='form-table ewd-uasp-welcome-screen-created-categories'>
						<tr class='ewd-uasp-welcome-screen-add-service-name ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Name', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-service-description ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Description', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-service-capacity ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Capacity', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='number' value='1'>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-service-duration ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Duration (minutes)', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='number' value='30'>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-uasp-welcome-screen-add-service-button'><?php _e('Add Service', 'ultimate-appointment-scheduling'); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e('Created Services', 'ultimate-appointment-scheduling'); ?></h3>
								<table class='ewd-uasp-welcome-screen-show-created-services'>
									<tr>
										<th class='ewd-uasp-welcome-screen-show-created-services-name'><?php _e('Name', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-services-description'><?php _e('Description', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-services-capacity'><?php _e('Capacity', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-services-duration'><?php _e('Duration', 'ultimate-appointment-scheduling'); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<div class='ewd-uasp-welcome-clear'></div>
					<div class='ewd-uasp-welcome-screen-next-button' data-nextaction='locations'><?php _e('Next', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-clear'></div>
				</div>
			</div>

			<div class='ewd-uasp-welcome-screen-box ewd-uasp-welcome-screen-locations' data-screen='locations'>
				<h2><?php _e('2. Location', 'ultimate-appointment-scheduling'); ?></h2>
				<div class='ewd-uasp-welcome-screen-box-content'>
					<p><?php _e('Create your first locations. Don\'t worry, you can always add more later.', 'ultimate-appointment-scheduling'); ?></p>
					<table class='form-table ewd-uasp-welcome-screen-created-categories'>
						<tr class='ewd-uasp-welcome-screen-add-location-name ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Name', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-location-description ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Description', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>

						<?php foreach ( $this->days as $lowercase_day => $day ) { ?> 
							
							<tr class='ewd-uasp-welcome-screen-add-location-open ewd-uasp-welcome-screen-box-content-divs' data-day='<?php echo $lowercase_day; ?>'>
								<th scope='row'><?php echo $day . __( ' Opening Time', 'ultimate-appointment-scheduling' ); ?></th>
								<td class='ewd-uasp-welcome-screen-option'>
									<select>
										<?php echo $ewd_uasp_controller->cpts->return_select_hours( '' ); ?>
									</select>
								</td>
							</tr>

							<tr class='ewd-uasp-welcome-screen-add-location-close ewd-uasp-welcome-screen-box-content-divs' data-day='<?php echo $lowercase_day; ?>'>
								<th scope='row'><?php echo $day . __( ' Closing Time', 'ultimate-appointment-scheduling' ); ?></th>
								<td class='ewd-uasp-welcome-screen-option'>
									<select>
										<?php echo $ewd_uasp_controller->cpts->return_select_hours( '' ); ?>
									</select>
								</td>
							</tr>

						<?php } ?>

						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-uasp-welcome-screen-add-location-button'><?php _e('Add Location', 'ultimate-appointment-scheduling'); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e('Created Locations', 'ultimate-appointment-scheduling'); ?></h3>
								<table class='ewd-uasp-welcome-screen-show-created-locations'>
									<tr>
										<th class='ewd-uasp-welcome-screen-show-created-locations-name'><?php _e('Name', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-locations-description'><?php _e('Description', 'ultimate-appointment-scheduling'); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<div class='ewd-uasp-welcome-clear'></div>
					<div class='ewd-uasp-welcome-screen-next-button' data-nextaction='providers'><?php _e('Next', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-welcome-screen-previous-button' data-previousaction='services'><?php _e('Previous', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-clear'></div>
				</div>
			</div>

			<div class='ewd-uasp-welcome-screen-box ewd-uasp-welcome-screen-providers' data-screen='providers'>
				<h2><?php _e('3. Service Provider', 'ultimate-appointment-scheduling'); ?></h2>
				<div class='ewd-uasp-welcome-screen-box-content'>
					<p><?php _e('Create your first service providers. Don\'t worry, you can always add more later.', 'ultimate-appointment-scheduling'); ?></p>
					<table class='form-table ewd-uasp-welcome-screen-created-categories'>
						<tr class='ewd-uasp-welcome-screen-add-provider-name ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Name', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-provider-description ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Description', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>
						<tr class='ewd-uasp-welcome-screen-add-provider-email ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Email', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='email'>
							</td>
						</tr>

						<tr class='ewd-uasp-welcome-screen-add-provider-services'>
							<th scope='row'><?php _e( 'Services', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-provider-services'>
							</td>
						</tr>

						<?php foreach ( $this->days as $lowercase_day => $day ) { ?> 
							
							<tr class='ewd-uasp-welcome-screen-add-provider-open ewd-uasp-welcome-screen-box-content-divs' data-day='<?php echo $lowercase_day; ?>'>
								<th scope='row'><?php echo $day . __( ' Starting Time', 'ultimate-appointment-scheduling' ); ?></th>
								<td class='ewd-uasp-welcome-screen-option'>
									<select>
										<?php echo $ewd_uasp_controller->cpts->return_select_hours( '' ); ?>
									</select>
								</td>
							</tr>

							<tr class='ewd-uasp-welcome-screen-add-provider-close ewd-uasp-welcome-screen-box-content-divs' data-day='<?php echo $lowercase_day; ?>'>
								<th scope='row'><?php echo $day . __( ' Finish Time', 'ultimate-appointment-scheduling' ); ?></th>
								<td class='ewd-uasp-welcome-screen-option'>
									<select>
										<?php echo $ewd_uasp_controller->cpts->return_select_hours( '' ); ?>
									</select>
								</td>
							</tr>

							<tr class='ewd-uasp-welcome-screen-add-provider-location ewd-uasp-welcome-screen-box-content-divs' data-day='<?php echo $lowercase_day; ?>'>
								<th scope='row'><?php echo $day . __( ' Location', 'ultimate-appointment-scheduling' ); ?></th>
								<td class='ewd-uasp-welcome-screen-option'>
									<select class='ewd-uasp-welcome-screen-provider-location-select'></select>
								</td>
							</tr>

						<?php } ?>

						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-uasp-welcome-screen-add-provider-button'><?php _e('Add Service Provider', 'ultimate-appointment-scheduling'); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e('Service Providers', 'ultimate-appointment-scheduling'); ?></h3>
								<table class='ewd-uasp-welcome-screen-show-created-providers'>
									<tr>
										<th class='ewd-uasp-welcome-screen-show-created-providers-name'><?php _e('Name', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-providers-description'><?php _e('Description', 'ultimate-appointment-scheduling'); ?></th>
										<th class='ewd-uasp-welcome-screen-show-created-providers-email'><?php _e('Email', 'ultimate-appointment-scheduling'); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<div class='ewd-uasp-welcome-clear'></div>
					<div class='ewd-uasp-welcome-screen-next-button' data-nextaction='booking-page'><?php _e('Next', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-welcome-screen-previous-button' data-previousaction='locations'><?php _e('Previous', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-clear'></div>
				</div>
			</div>

			<div class='ewd-uasp-welcome-screen-box ewd-uasp-welcome-screen-booking-page' data-screen='booking-page'>
				<h2><?php _e('4. Add an Appointment Booking Page', 'ultimate-appointment-scheduling'); ?></h2>
				<div class='ewd-uasp-welcome-screen-box-content'>
					<p><?php _e('You can create a dedicated appointment booking page below, or skip this step and add the appointment booking shortcode to a page you\'ve already created manually.', 'ultimate-appointment-scheduling'); ?></p>
					<table class='form-table ewd-uasp-welcome-screen-booking-page'>
						<tr class='ewd-uasp-welcome-screen-add-booking-page-name ewd-uasp-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Page Title', 'ultimate-appointment-scheduling' ); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-uasp-welcome-screen-add-booking-page-button' data-nextaction='options'><?php _e( 'Create Page', 'ultimate-appointment-scheduling' ); ?></div>
							</td>
						</tr>
					</table>

					<div class='ewd-uasp-welcome-clear'></div>
					<div class='ewd-uasp-welcome-screen-next-button' data-nextaction='options'><?php _e('Next', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-welcome-screen-previous-button' data-previousaction='providers'><?php _e('Previous', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-clear'></div>
				</div>
			</div>

			<div class='ewd-uasp-welcome-screen-box ewd-uasp-welcome-screen-options' data-screen='options'>
				<h2><?php _e('4. Set Key Options', 'ultimate-appointment-scheduling'); ?></h2>
				<div class='ewd-uasp-welcome-screen-box-content'>
					<p><?php _e('Options can always be changed later, but here are a few that a lot of users want to set for themselves.', 'ultimate-appointment-scheduling'); ?></p>
					<table class='form-table'>
						<tr>
							<th scope='row'><?php _e('Multi-Step Booking', 'ultimate-appointment-scheduling'); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<fieldset>
									<div class='sap-admin-hide-radios'>
										<input type='checkbox' name='multi_step_booking' value='1'>
									</div>
									<label class='sap-admin-switch'>
										<input type='checkbox' class='sap-admin-option-toggle' data-inputname='multi_step_booking' <?php if ( $multi_step_booking == '1' ) { echo 'checked'; } ?>>
										<span class='sap-admin-switch-slider round'></span>
									</label>		
									<p class='description'><?php _e('Should booking an appointment be split into multiple steps or happen all in one place?', 'ultimate-appointment-scheduling'); ?></p>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('Time Between Appointments', 'ultimate-appointment-scheduling'); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<fieldset>
									<div>
										<input type='text' name='time_between_appointments' value='<?php echo intval( $time_between_appointments ); ?>' />
									</div>		
									<p class='description'><?php _e('How much time should there be between scheduled appointments? (in minutes)', 'ultimate-appointment-scheduling'); ?></p>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('Hours Format', 'ultimate-appointment-scheduling'); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<fieldset>
									<select name='hours_format'>
										<option value='24' <?php if ( $hours_format == '24' ) { echo 'selected';} ?> ><?php _e( '24 Hour', 'ultimate-appointment-scheduling' ); ?></option>
										<option value='12' <?php if ( $hours_format == '12' ) { echo 'selected';} ?> ><?php _e( '12 Hour', 'ultimate-appointment-scheduling' ); ?></option>
									</select>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('Calendar Starting Layout', 'ultimate-appointment-scheduling'); ?></th>
							<td class='ewd-uasp-welcome-screen-option'>
								<fieldset>
									<select name='calendar_starting_layout'>
										<option value='agendaDay' <?php if ( $calendar_starting_layout == 'agendaDay' ) { echo 'selected';} ?> ><?php _e( 'Day', 'ultimate-appointment-scheduling' ); ?></option>
										<option value='agendaWeek' <?php if ( $calendar_starting_layout == 'agendaWeek' ) { echo 'selected';} ?> ><?php _e( 'Week', 'ultimate-appointment-scheduling' ); ?></option>
					  					<option value='month' <?php if ( $calendar_starting_layout == 'month' ) { echo 'selected';} ?> ><?php _e( 'Month', 'ultimate-appointment-scheduling' ); ?></option>
					  					<option value='listWeek' <?php if ( $calendar_starting_layout == 'listWeek' ) { echo 'selected';} ?> ><?php _e( 'List', 'ultimate-appointment-scheduling' ); ?></option>
									</select>
									<p class='description'><?php _e('What layout should the calendar start in?', 'ultimate-appointment-scheduling'); ?></p>
								</fieldset>
							</td>
						</tr>
					</table>
		
					<div class='ewd-uasp-welcome-screen-save-options-button'><?php _e('Save Options', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-welcome-clear'></div>
					<div class='ewd-uasp-welcome-screen-previous-button' data-previousaction='booking-page'><?php _e('Previous', 'ultimate-appointment-scheduling'); ?></div>
					<div class='ewd-uasp-welcome-screen-finish-button'><a href='admin.php?page=ewd-uasp-settings'><?php _e('Finish', 'ultimate-appointment-scheduling'); ?></a></div>
					
					<div class='ewd-uasp-clear'></div>
				</div>
			</div>

			<div class='ewd-uasp-welcome-screen-skip-container'>
				<a href='admin.php?page=ewd-uasp-settings'><div class='ewd-uasp-welcome-screen-skip-button'><?php _e('Skip Setup', 'ultimate-appointment-scheduling'); ?></div></a>
			</div>
		</div>

	<?php }
}


?>