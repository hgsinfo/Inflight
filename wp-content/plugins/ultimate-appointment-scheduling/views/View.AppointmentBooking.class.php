<?php

/**
 * Class to displayan appointment booking form on the front end.
 *
 * @since 2.0.0
 */
class ewduaspViewAppointmentBooking extends ewduaspView {

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 2.0.0
	 */
	public function render() {
		global $ewd_uasp_controller;

		$this->set_booking_options();

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Add css classes to the slider
		$this->classes = $this->get_classes();

		ob_start();

		$this->add_custom_styling();

		if ( $this->display == 'login' ) { $template = $this->find_template( 'login' ); }
		elseif ( $this->display == 'payment' ) { $template = $this->find_template( 'paypal' ); }
		else { $template = $this->find_template( 'appointment-booking-form' ); }
		
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		return apply_filters( 'ewd_uasp_appointment_booking_form_output', $output, $this );
	}

	/**
	 * Print the edit appointment area, if not disabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_appointment_edit_form() {
		global $ewd_uasp_controller;
		
		if ( $ewd_uasp_controller->settings->get_setting( 'disable-appointment-editing' ) ) { return; }
		
		$template = $this->find_template( 'edit-appointment' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints an action notification, if any action has happened
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_update_message() {
		global $ewd_uasp_controller;
		
		if ( empty( $this->update_message ) ) { return; }
		
		$template = $this->find_template( 'update-message' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the multistep booking process indicators, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_multistep_indicators() {
		global $ewd_uasp_controller;
		
		if ( ! $ewd_uasp_controller->settings->get_setting( 'multi-step-booking' ) ) { return; }
		
		$template = $this->find_template( 'multistep-indicators' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the registration form
	 *
	 * @since 2.0.0
	 */
	public function print_registration_form() {
		
		$template = $this->find_template( 'registration' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the service selection form
	 *
	 * @since 2.0.0
	 */
	public function print_service_form() {
		
		$template = $this->find_template( 'service' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the appointment selection form
	 *
	 * @since 2.0.0
	 */
	public function print_appointment_selection() {
		
		if ( $this->calendar_mode ) { $template = $this->find_template( 'calendar' ); }
		else { $template = $this->find_template( 'dropdown-selectors' ); }
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the appointment booking submission area
	 *
	 * @since 2.0.0
	 */
	public function print_booking_submission() {
		
		$template = $this->find_template( 'submit' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the multistep booking process indicators, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_multistep_advance() {
		global $ewd_uasp_controller;
		
		if ( ! $ewd_uasp_controller->settings->get_setting( 'multi-step-booking' ) ) { return; }
		
		$template = $this->find_template( 'multistep-advance' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the captcha field, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_captcha() {
		global $ewd_uasp_controller;
		
		if ( ! $this->use_captcha ) { return; }
		
		$template = $this->find_template( 'captcha' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the results of the edit appointment search, if there are any
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_edit_appointment_search_results() {

		if ( empty( $this->edit_appointment_search_results ) ) { return; }

		$template = $this->find_template( 'edit-appointment-results' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints a form that can be used to find your appointment for editing
	 *
	 * @since 2.0.0
	 */
	public function print_edit_appointment_search_form() {

		$template = $this->find_template( 'edit-appointment-search' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints a custom field, using the correct template based on it's type
	 *
	 * @since 2.0.0
	 */
	public function print_custom_field( $custom_field ) {
		global $ewd_uasp_controller;
		
		$this->custom_field = $custom_field;

		$this->custom_field->field_value = ! empty( $this->appointment_id ) ? $ewd_uasp_controller->appointment_manager->get_field_value( $custom_field->id, $this->appointment_id ) : '';
		$this->custom_field->field_value = $custom_field->type == 'checkbox' ? explode( ',', $this->custom_field->field_value ) : $this->custom_field->field_value;

		if ( $custom_field->type == 'text' ) { $template = $this->find_template( 'custom-field-text' ); }
		elseif ( $custom_field->type == 'number' ) { $template = $this->find_template( 'custom-field-number' ); }
		elseif ( $custom_field->type == 'textarea' ) { $template = $this->find_template( 'custom-field-textarea' ); }
		elseif ( $custom_field->type == 'select' ) { $template = $this->find_template( 'custom-field-select' ); }
		elseif ( $custom_field->type == 'radio' ) { $template = $this->find_template( 'custom-field-radio' ); }
		elseif ( $custom_field->type == 'checkbox' ) { $template = $this->find_template( 'custom-field-checkbox' ); }
		elseif ( $custom_field->type == 'link' ) { $template = $this->find_template( 'custom-field-link' ); }
		elseif ( $custom_field->type == 'date' ) { $template = $this->find_template( 'custom-field-date' ); }
		elseif ( $custom_field->type == 'datetime' ) { $template = $this->find_template( 'custom-field-datetime' ); }

		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Returns whether the edit appointment area should default to open or not
	 *
	 * @since 2.0.0
	 */
	public function display_appointment_editing_form() {

		return isset( $this->edit_appointment_search_results ) ? true : false;
	}

	public function get_field_required( $field ) {
		global $ewd_uasp_controller;

		return in_array( $field, $ewd_uasp_controller->settings->get_setting( 'required-information' ) ) ? 'required' : '';
	}

	/**
	 * Returns the name of the service that was booked (used for the PayPal form)
	 *
	 * @since 2.0.0
	 */
	public function get_selected_service_name() {

		if ( empty( $this->service ) ) { return ''; }
		
		foreach ( $this->services as $service ) {

			if ( $service->ID == $this->service ) { return $service->post_title; }
		}
	}

	/**
	 * Returns the price of the service that was booked (used for the PayPal form)
	 *
	 * @since 2.0.0
	 */
	public function get_selected_service_price() {

		if ( empty( $this->service ) ) { return ''; }
		
		return get_post_meta( $this->service, 'Service Price', true );
	}

	/**
	 * Returns the appointment ID of the service that was booked (used for the PayPal form)
	 *
	 * @since 2.0.0
	 */
	public function get_selected_appointment_id() {

		return empty( $this->appointment_id ) ? 0 : $this->appointment_id; 
	}

	/**
	 * Returns the login URL, if login enabled
	 *
	 * @since 2.0.0
	 */
	public function get_wordpress_login_url() {
		global $ewd_uasp_controller;

		return ! empty( $ewd_uasp_controller->settings->get_setting( 'wordpress-login-url' ) ) ? $ewd_uasp_controller->settings->get_setting( 'wordpress-login-url' ) : wp_login_url( get_permalink() );
	}

	/**
	 * Returns custom fields that have been created in the plugin
	 *
	 * @since 2.0.0
	 */
	public function get_custom_fields() {
		global $ewd_uasp_controller;

		return ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );
	}

	/**
	 * Returns the image for the captcha field
	 *
	 * @since 2.0.0
	 */
	public function create_captcha_image() {

		$im = imagecreatetruecolor( 50, 24 );
		$bg = imagecolorallocate( $im, 22, 86, 165 );  
		$fg = imagecolorallocate( $im, 255, 255, 255 ); 
		imagefill( $im, 0, 0, $bg );
		imagestring( $im, 5, 5, 5,  $this->get_captcha_image_code(), $fg );

  		$five_mb = 5 * 1024 * 1024;
  		$stream = fopen( 'php://temp/maxmemory:{$five_mb}', 'r+' );
  		imagepng( $im, $stream );
  		imagedestroy( $im );
  		rewind( $stream );

  		return base64_encode( stream_get_contents( $stream ) );
  	}

  	public function get_captcha_image_code() {

  		return ( $this->captcha_form_code / 3 ) - 5;
  	}

	/**
	 * Get appointments that match the information submitted in the edit appointment form
	 *
	 * @since 2.0.0
	 */
	public function retrieve_matching_appointments() {
		global $ewd_uasp_controller;

		$args = array();
		
		if ( ! empty( $_POST['client_name'] ) ) { $args['client_name'] = sanitize_text_field( $_POST['client_name'] ); }
		if ( ! empty( $_POST['client_phone'] ) ) { $args['client_phone'] = sanitize_text_field( $_POST['client_phone'] ); }
		if ( ! empty( $_POST['client_email'] ) ) { $args['client_email'] = sanitize_email( $_POST['client_email'] ); }

		if ( empty( $args ) ) { return array(); }

		$this->edit_appointment_search_results =  $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );
	}

	/**
	 * Get the initial submit faq css classes
	 * @since 2.0.0
	 */
	public function get_classes( $classes = array() ) {
		global $ewd_uasp_controller;

		$classes = array_merge(
			$classes,
			array(
				'ewd-uasp-appointment-selector',
			)
		);

		if ( $ewd_uasp_controller->settings->get_setting( 'booking-form-style' ) == 'contemporary' ) {
			$classes[] = 'ewd-uasp-contemporary';
		}

		if ( $ewd_uasp_controller->settings->get_setting( 'multi-step-booking' ) ) {
			$classes[] = 'ewd-uasp-multistep-form';
		}

		return apply_filters( 'ewd_uasp_appointment_booking_form_classes', $classes, $this );
	}

	/**
	 * Set which main template should be used based on settings and previous user interaction
	 * @since 2.0.0
	 */
	public function set_display_parameters() {
		global $ewd_uasp_controller;

		if ( $ewd_uasp_controller->settings->get_setting( 'require-login' ) and ! is_user_logged_in() ) { $this->display = 'login'; }
		elseif ( ! empty( $_REQUEST['ewd_uasp_payment_submit'] ) ) { $this->display = 'payment'; }
		else { $this->display = 'booking'; }
	}

	/**
	 * Allow some parameters to be overwritten with URL parameters, to pay for/cancel/update a specific booking
	 * @since 2.0.0
	 */
	public function set_request_parameters() {
		global $ewd_uasp_controller;

		
	}

	/**
	 * Add in default options if not overwritten by shortcode attributes
	 *
	 * @since 2.0.0
	 */
	public function set_booking_options() {
		global $ewd_uasp_controller;

		$this->calendar_mode = ( ! empty( $this->display_type ) and strtolower( $this->display_type ) == 'calendar' ) ? true : false;
		$this->multi_step_booking = $ewd_uasp_controller->settings->get_setting( 'multi-step-booking' );
		$this->paypal_prepayment = $ewd_uasp_controller->settings->get_setting( 'paypal-prepayment' );

		$args = array(
			'post_type'		=> EWD_UASP_LOCATION_POST_TYPE,
			'numberposts'	=> -1
		);
		$this->locations = get_posts( $args );

		$args = array(
			'post_type'		=> EWD_UASP_SERVICE_POST_TYPE,
			'numberposts'	=> -1
		);
		$this->services = get_posts( $args );

		$args = array(
			'post_type'		=> EWD_UASP_PROVIDER_POST_TYPE,
			'numberposts'	=> -1
		);
		$this->providers = get_posts( $args );	

		$user = wp_get_current_user();

		if ( ! empty( $this->edit_appointment_id ) ) { 

			$appointment = new ewduaspAppointment();

			$appointment->load_appointment_from_id( $this->edit_appointment_id );

			$this->appointment = $appointment;
		}

		$this->appointment_id = ! empty( $this->appointment ) ? $this->appointment->id : 0;
		$this->client_name = ! empty( $this->appointment ) ? $this->appointment->client_name : ( $user ? $user->get( 'first_name' ) . ' ' . $user->get( 'last_name' ) : '' );
		$this->client_phone = ! empty( $this->appointment ) ? $this->appointment->client_phone : '';
		$this->client_email = ! empty( $this->appointment ) ? $this->appointment->client_email : ( $user ? $user->get( 'user_email' ) : '' );
		$this->location = ! empty( $this->appointment ) ? $this->appointment->location : 0;
		$this->service = ! empty( $this->appointment ) ? $this->appointment->service : 0;
		$this->provider = ! empty( $this->appointment ) ? $this->appointment->provider : 0;

		$this->min_date = $ewd_uasp_controller->settings->get_setting( 'minimum-days-advance' ) ? date( 'Y-m-d', time() + (int) $ewd_uasp_controller->settings->get_setting( 'minimum-days-advance' ) *3600*24 ) : date( 'Y-m-d', time() + (int) $ewd_uasp_controller->settings->get_setting( 'minimum-hours-advance' ) *3600 );
		$this->max_date = date( 'Y-m-d', time() + (int) $ewd_uasp_controller->settings->get_setting( 'maximum-days-advance' ) *3600*24 );

		$this->date = ! empty( $this->appointment ) ? date( 'Y-m-d', strtotime( $this->appointment->start ) ) : date( 'Y-m-d', max( time(), strtotime( $this->min_date ) ) ) ;

		$this->use_captcha = $ewd_uasp_controller->settings->get_setting( 'use-captcha' );

		if ( $ewd_uasp_controller->settings->get_setting( 'use-captcha' ) ) {
			
			$this->captcha_form_code = ( rand( 1000, 9999 ) + 5 ) * 3;
		}

		if ( ! empty( $_POST['ewd_uasp_edit_appointment_search'] ) ) { $this->retrieve_matching_appointments(); }
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		global $ewd_uasp_controller;

		wp_enqueue_style( 'ewd-uasp-css' );
		wp_enqueue_style( 'ewd-uasp-jquery-ui' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		$calendar_offset = $ewd_uasp_controller->settings->get_setting( 'calendar-offset' );
		$calendar_offset_unit = substr( $calendar_offset, strpos( $calendar_offset, '_' ) + 1 );
		$calendar_offset_time = (int) substr( $calendar_offset, 0, strpos( $calendar_offset, '_' ) ) * ( $calendar_offset_unit == 'offsetMonth' ? 30 : ( $calendar_offset_unit == 'offsetWeek' ? 7 : 1 ) ) * 24 * 60 * 60;

		$args = array(
			'default_date' 		=> date( 'Y-m-d', time() + $calendar_offset_time ),
		);

		$ewd_uasp_controller->add_front_end_php_data( 'ewd-uasp-js', 'ewd_uasp_php_data', $args );

		wp_enqueue_script( 'ewd-uasp-js' );

		if ( $this->calendar_mode ) {

			wp_enqueue_style( 'full-calendar' );

        	$time = new \DateTime( 'now', new DateTimeZone( get_option( 'timezone_string' ) ) );

        	$args = array(
            	'time_interval' 		=> $ewd_uasp_controller->settings->get_setting( 'time-between-appointments' ), 
            	'timezone' 				=> get_option( 'timezone_string' ),
            	'timezone_offset' 		=> $time->format( 'P' ),
            	'hours_format' 			=> $ewd_uasp_controller->settings->get_setting( 'hours-format' ),
            	'starting_layout' 		=> $ewd_uasp_controller->settings->get_setting( 'calendar-starting-layout' ),
            	'starting_time' 		=> $ewd_uasp_controller->settings->get_setting( 'calendar-starting-time' ) . ':00:00',
            	'calendar_language' 	=> $ewd_uasp_controller->settings->get_setting( 'calendar-language' ),
            	'pop_up_label_location' => $this->get_label( 'label-location' ),	
   				'pop_up_label_service' 	=> $this->get_label( 'label-service' ),	
   				'pop_up_label_provider' => $this->get_label( 'label-service-provider' ),
				'default_date' 			=> date( 'Y-m-d', time() + $calendar_offset_time ),
			);

			$ewd_uasp_controller->add_front_end_php_data( 'ewd-uasp-calendar-js', 'ewd_uasp_php_calendar_data', $args );

			wp_enqueue_script( 'moment' );

			wp_enqueue_script( 'full-calendar' );

			wp_enqueue_script( 'ewd-uasp-calendar-locale' );

			wp_enqueue_script( 'ewd-uasp-calendar-js' );
		}

	}
}
