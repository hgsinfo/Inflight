<?php

/**
 * Class to display an appointment add/edit form in the admin.
 *
 * @since 2.0.0
 */
class ewduaspViewAdminAppointmentBooking extends ewduaspViewAppointmentBooking {

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

		$template = $this->find_template( 'admin-booking-form' );
		
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		return apply_filters( 'ewd_uasp_admin_appointment_booking_form_output', $output, $this );
	}

	/**
	 * Prints the currently selected date/time of the appointment, if any
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_appointment_selected_time() {

		if ( empty( $this->appointment ) ) { return; }

		$template = $this->find_template( 'admin-selected-date-time' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints location/service/provider appointment area
	 *
	 * @since 2.0.0
	 */
	public function print_admin_service_select() {

		$template = $this->find_template( 'admin-service' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints appointment selection/re-selection area
	 *
	 * @since 2.0.0
	 */
	public function print_admin_appointment_selection() {

		if ( $this->calendar_mode ) { $template = $this->find_template( 'admin-calendar' ); }
		else { $template = $this->find_template( 'admin-find-appointment' ); }
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints appointment selection/re-selection area
	 *
	 * @since 2.0.0
	 */
	public function print_admin_client_details() {

		$template = $this->find_template( 'admin-client-details' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints custom fields area
	 *
	 * @since 2.0.0
	 */
	public function print_admin_custom_fields() {

		$template = $this->find_template( 'admin-custom-fields' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the add/update button section
	 *
	 * @since 2.0.0
	 */
	public function print_admin_submit() {

		$template = $this->find_template( 'admin-submit' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Add in default options when displaying in the admin appointments area
	 *
	 * @since 2.0.0
	 */
	public function set_booking_options() {
		global $ewd_uasp_controller;

		$this->calendar_mode = ( ! empty( $this->display_type ) and strtolower( $this->display_type ) == 'calendar' ) ? true : false;
		$this->multi_step_booking = false;
		$this->paypal_prepayment = 'none';

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

		$this->appointment_id = ! empty( $this->appointment ) ? $this->appointment->id : 0;
		$this->client_name = ! empty( $this->appointment ) ? $this->appointment->client_name :  '';
		$this->client_phone = ! empty( $this->appointment ) ? $this->appointment->client_phone : '';
		$this->client_email = ! empty( $this->appointment ) ? $this->appointment->client_email : '';
		$this->location = ! empty( $this->appointment ) ? $this->appointment->location : 0;
		$this->service = ! empty( $this->appointment ) ? $this->appointment->service : 0;
		$this->provider = ! empty( $this->appointment ) ? $this->appointment->provider : 0;
		$this->date = ! empty( $this->appointment ) ? date( 'Y-m-d', strtotime( $this->appointment->start ) ) : date( 'Y-m-d' );

		$this->min_date = date( 'Y-m-d', time() );

		$this->use_captcha = false;
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		global $ewd_uasp_controller;

		//wp_enqueue_style( 'ewd-uasp-css' );
		wp_enqueue_style( 'ewd-uasp-jquery-ui' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		$calendar_offset = $ewd_uasp_controller->settings->get_setting( 'calendar-offset' );
		$calendar_offset_unit = substr( $calendar_offset, strpos( $calendar_offset, '_' ) + 1 );
		$calendar_offset_time = intval( substr( $calendar_offset, 0, strpos( $calendar_offset, '_' ) ) ) * ( $calendar_offset_unit == 'offsetMonth' ? 30 : ( $calendar_offset_unit == 'offsetWeek' ? 7 : 1 ) ) * 24 * 60 * 60;

		$args = array(
			'default_date' 		=> date( 'Y-m-d', time() + $calendar_offset_time ),
		);

		if ( ! empty( $this->appointment ) ) { $args['appointment_id'] = $this->appointment->id; }

		wp_localize_script( 'ewd-uasp-js', 'ewd_uasp_php_data', $args );

		wp_enqueue_script( 'ewd-uasp-js' );

		if ( $this->calendar_mode ) {

			wp_enqueue_style( 'full-calendar' );

        	$time = new \DateTime('now', new DateTimeZone( $ewd_uasp_controller->settings->get_setting( 'timezone' ) ) );

        	$args = array(
            	'time_interval' 		=> $ewd_uasp_controller->settings->get_setting( 'time-between-appointments' ), 
            	'timezone' 				=> $ewd_uasp_controller->settings->get_setting( 'timezone' ),
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

			wp_localize_script( 'ewd-uasp-calendar-js', 'ewd_uasp_php_calendar_data', $args );

			wp_enqueue_script( 'moment' );

			wp_enqueue_script( 'full-calendar' );

			wp_enqueue_script( 'ewd-uasp-calendar-locale' );

			wp_enqueue_script( 'ewd-uasp-calendar-js' );
		}

	}
}
