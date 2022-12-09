<?php
/**
 * Class to act as a wrapper for a single appointment
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduaspAppointment' ) ) {
class ewduaspAppointment {

	// The database ID of the current appointment
	public $id = 0;

	// Stores all of the custom field values for an appointment
	public $custom_fields = array();

	/**
	 * Load an appointment based on a specific database record
	 * @since 2.0.0
	 */
	public function load_appointment( $db_appointment ) {
		global $ewd_uasp_controller;

		$this->id 				= $db_appointment->Appointment_ID;

		$this->start			= $db_appointment->Appointment_Start;
		$this->end				= $db_appointment->Appointment_End;

		$this->location 		= $db_appointment->Location_Post_ID;
		$this->location_name	= $db_appointment->Location_Name;
		$this->service 			= $db_appointment->Service_Post_ID;
		$this->service_name		= $db_appointment->Service_Name;
		$this->provider 		= $db_appointment->Service_Provider_Post_ID;
		$this->provider_name	= $db_appointment->Service_Provider_Name;

		$this->paypal_prepaid	= $db_appointment->Appointment_Prepaid == 'Yes' ? true : false;
		$this->paypal_receipt	= $db_appointment->Appointment_PayPal_Receipt_Number;
		$this->wc_prepaid		= $db_appointment->WC_Order_Paid == 'Yes' ? true : false;
		$this->wc_order_id		= $db_appointment->WC_Order_ID;

		$this->client_name		= $db_appointment->Appointment_Client_Name;
		$this->client_phone		= $db_appointment->Appointment_Client_Phone;
		$this->client_email		= $db_appointment->Appointment_Client_Email;

		$this->reminder_sent	= explode( ',', $db_appointment->Appointment_Reminder_Email_Sent );
		$this->confirmed		= $db_appointment->Appointment_Confirmation_Received == 'Yes' ? true : false;

		foreach ( ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) ) as $custom_field ) {

			$this->custom_fields[ $custom_field->id ] = $ewd_uasp_controller->appointment_manager->get_field_value( $custom_field->id, $this->id );
		}
	}

	public function load_appointment_from_id( $appointment_id ) {
		global $ewd_uasp_controller;

		$db_appointment = $ewd_uasp_controller->appointment_manager->get_appointment_from_id( $appointment_id );

		$this->load_appointment( $db_appointment );
	}

	/**
	 * Validates a submitted appointment, and calls insert_appointment if validated
	 * @since 2.0.0
	 */
	public function process_client_appointment_submission() {
		global $ewd_uasp_controller;

		$this->validate_submission();
		if ( $this->is_valid_submission() === false ) {
			return false;
		}

		if ( $this->id ) { $this->update_appointment(); }
		else { 

			$this->insert_appointment(); 

			do_action( 'ewd_uasp_insert_appointment', $this );
		}

		if ( isset( $_POST['ewd_uasp_payment_submit'] ) and empty( $this->paypal_prepaid ) and empty( $this->wc_prepaid ) ) {
			return 'paypal_payment_required'; 
		}

		return 'success';
	}

	/**
	 * Validate submission data. Expects to find data in $_POST.
	 * @since 2.0.0
	 */
	public function validate_submission() {
		global $ewd_uasp_controller;

		$this->validation_errors = array();

		// CAPTCHA
		if ( $ewd_uasp_controller->settings->get_setting( 'use-captcha' ) ) {
			
			$modified_code = intval( $_POST['ewd_uasp_modified_captcha'] );
			$user_code = intval( $_POST['ewd_uasp_captcha'] );

			if ( $user_code != $this->decrypt_modified_code( $modified_code ) ) {

				$this->validation_errors[] = array(
					'field'		=> 'captcha',
					'error_msg'	=> 'Captcha incorrect',
					'message'	=> __( 'The number you entered for the image was incorrect.', 'ultimate-appointment-scheduling' ),
				);
			}
		}

		$this->id = empty( $_POST['ewd_uasp_appointment_id'] ) ? 0 : intval( $_POST['ewd_uasp_appointment_id'] );
		
		if ( $this->id ) { 

			$db_appointment = $ewd_uasp_controller->appointment_manager->get_appointment_from_id( $this->id );
			
			$this->load_appointment( $db_appointment ); 
		}

		// REGISTRATION
		$this->client_name = empty( $_POST['ewd_uasp_client_name'] ) ? false : sanitize_text_field( $_POST['ewd_uasp_client_name'] );
		$this->client_phone = empty( $_POST['ewd_uasp_client_phone'] ) ? false : sanitize_text_field( $_POST['ewd_uasp_client_phone'] );
		$this->client_email = empty( $_POST['ewd_uasp_client_email'] ) ? false : sanitize_email( $_POST['ewd_uasp_client_email'] );
		
		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		foreach ( $custom_fields as $custom_field ) {

			$input_name = 'ewd-uasp-custom-field-' . $custom_field->id;

			if ( $custom_field->type == 'checkbox' ) { $this->custom_fields[ $custom_field->id ] = ( empty( $_POST[ $input_name ] ) or ! is_array( $_POST[ $input_name ] ) ) ? '' : sanitize_text_field( implode( ',', $_POST[ $input_name ] ) ); }
			elseif ( $custom_field->type == 'textarea' ) { $this->custom_fields[ $custom_field->id ] = empty( $_POST[ $input_name ] ) ? false : sanitize_textarea_field( $_POST[ $input_name ] ); }
			else { $this->custom_fields[ $custom_field->id ] = empty( $_POST[ $input_name ] ) ? false : sanitize_text_field( $_POST[ $input_name ] ); }
		}

		// LOCATION
		$this->location = empty( $_POST['ewd_uasp_location_id'] ) ? 0 : intval( $_POST['ewd_uasp_location_id'] );

		if ( ! $this->location ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_location_id',
				'error_msg'	=> 'Location is blank',
				'message'	=> __( 'Please make sure to select a valid location', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->location_name = get_the_title( $this->location );
		}


		// SERVICE
		$this->service = empty( $_POST['ewd_uasp_service_id'] ) ? 0 : intval( $_POST['ewd_uasp_service_id'] );

		if ( ! $this->service ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_service_id',
				'error_msg'	=> 'Service is blank',
				'message'	=> __( 'Please make sure to select a valid service', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->service_name = get_the_title( $this->service );
		}

		// PROVIDER
		$this->provider = empty( $_POST['ewd_uasp_provider_id'] ) ? 0 : intval( $_POST['ewd_uasp_provider_id'] );

		if ( ! $this->service ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_provider_id',
				'error_msg'	=> 'Provider is blank',
				'message'	=> __( 'Please make sure to select a valid service provider', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->provider_name = get_the_title( $this->provider );
		}

		// START/END TIMES
		$this->start = empty( $_POST['ewd_uasp_appointment_start'] ) ? false : sanitize_text_field( $_POST['ewd_uasp_appointment_start'] );

		if ( ! $this->start ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_appointment_start',
				'error_msg'	=> 'Start is blank',
				'message'	=> __( 'Please make sure to select a valid start time', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->end = date( 'Y-m-d H:i:s', strtotime( $this->start ) + get_post_meta( $this->service, 'Service Duration', true ) * 60 );
		}

		do_action( 'ewd_uasp_validate_appointment_submission', $this );
	}

	/**
	 * Validates a submitted appointment, and calls insert_appointment if validated
	 * @since 2.0.0
	 */
	public function process_admin_appointment_submission() {
		global $ewd_uasp_controller;

		$this->validate_admin_submission();
		if ( $this->is_valid_submission() === false ) {
			return false;
		}
		
		if ( $this->id ) { $this->update_appointment(); }
		else { 

			$this->insert_appointment(); 

			do_action( 'ewd_uasp_admin_insert_appointment', $this );
		}

		return true;
	}

	/**
	 * Validate submission data entered via the admin page
	 * @since 2.0.0
	 */
	public function validate_admin_submission() {
		global $ewd_uasp_controller;

		$this->validation_errors = array();

		// REGISTRATION
		$this->client_name = empty( $_POST['ewd_uasp_client_name'] ) ? false : sanitize_text_field( $_POST['ewd_uasp_client_name'] );
		$this->client_phone = empty( $_POST['ewd_uasp_client_phone'] ) ? false : sanitize_text_field( $_POST['ewd_uasp_client_phone'] );
		$this->client_email = empty( $_POST['ewd_uasp_client_email'] ) ? false : sanitize_email( $_POST['ewd_uasp_client_email'] );
		
		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		foreach ( $custom_fields as $custom_field ) {

			$input_name = 'ewd-uasp-custom-field-' . $custom_field->id;

			if ( $custom_field->type == 'checkbox' ) { $this->custom_fields[ $custom_field->id ] = ( empty( $_POST[ $input_name ] ) or ! is_array( $_POST[ $input_name ] ) ) ? '' : sanitize_text_field( implode( ',', $_POST[ $input_name ] ) ); }
			elseif ( $custom_field->type == 'textarea' ) { $this->custom_fields[ $custom_field->id ] = empty( $_POST[ $input_name ] ) ? false : sanitize_textarea_field( $_POST[ $input_name ] ); }
			else { $this->custom_fields[ $custom_field->id ] = empty( $_POST[ $input_name ] ) ? false : sanitize_text_field( $_POST[ $input_name ] ); }
		}

		// LOCATION
		$this->location = empty( $_POST['ewd_uasp_location_id'] ) ? 0 : intval( $_POST['ewd_uasp_location_id'] );

		if ( ! $this->location ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_location_id',
				'error_msg'	=> 'Location is blank',
				'message'	=> __( 'Please make sure to select a valid location', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->location_name = get_the_title( $this->location );
		}


		// SERVICE
		$this->service = empty( $_POST['ewd_uasp_service_id'] ) ? 0 : intval( $_POST['ewd_uasp_service_id'] );

		if ( ! $this->service ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_service_id',
				'error_msg'	=> 'Service is blank',
				'message'	=> __( 'Please make sure to select a valid service', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->service_name = get_the_title( $this->service );
		}

		// PROVIDER
		$this->provider = empty( $_POST['ewd_uasp_provider_id'] ) ? 0 : intval( $_POST['ewd_uasp_provider_id'] );

		if ( ! $this->service ) {

			$this->validation_errors[] = array(
				'field'		=> 'ewd_uasp_provider_id',
				'error_msg'	=> 'Provider is blank',
				'message'	=> __( 'Please make sure to select a valid service provider', 'ultimate-appointment-scheduling' ),
			);
		}
		else {

			$this->provider_name = get_the_title( $this->provider );
		}

		// START/END TIMES
		if ( ! empty( $_POST['ewd_uasp_appointment_start'] ) ) {

			$this->start = sanitize_text_field( $_POST['ewd_uasp_appointment_start'] );

			if ( ! $this->start ) {

				$this->validation_errors[] = array(
					'field'		=> 'ewd_uasp_appointment_start',
					'error_msg'	=> 'Start is blank',
					'message'	=> __( 'Please make sure to select a valid start time', 'ultimate-appointment-scheduling' ),
				);
			}
			else {

				$this->end = date( 'Y-m-d H:i:s', strtotime( $this->start ) + get_post_meta( $this->service, 'Service Duration', true ) * 60 );
			}
		}
	}

	/**
	 * Check if submission is valid
	 * @since 2.0.0
	 */
	public function is_valid_submission() {

		if ( !count( $this->validation_errors ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the decrypted version of the captcha code
	 * @since 2.0.0
	 */
	public function decrypt_modified_code( $user_code ) {

		$decrypted_code = ($user_code / 3) - 5;

		return $decrypted_code;
	}

	/**
	 * Insert a new appointment into the database
	 * @since 2.0.0
	 */
	public function insert_appointment() {
		global $ewd_uasp_controller;

		$id = $ewd_uasp_controller->appointment_manager->insert_appointment( $this );

		$this->id = $id;
	}

	/**
	 * Update an appointment already in the database
	 * @since 2.0.0
	 */
	public function update_appointment() {
		global $ewd_uasp_controller;

		$ewd_uasp_controller->appointment_manager->update_appointment( $this );

	}
}
}