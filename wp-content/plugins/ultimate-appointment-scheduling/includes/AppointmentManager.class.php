<?php
/**
 * Class to handle all appointment interactions for the Ultimate Appointment Scheduling plugin
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduaspAppointmentManager' ) ) {
class ewduaspAppointmentManager {

	// The name of the appointments table, set in the constructor
	public $appointments_table_name;

	// The name of the meta table, set in the constructor
	public $appointments_meta_table_name;

	// Array containing the arguments for the query
	public $args = array();

	// Array containing retrieved appointment objects
	public $appointments = array();

	public function __construct() {
		global $wpdb;

		$this->appointments_table_name = $wpdb->prefix . "EWD_UASP_Appointments";
		$this->appointments_meta_table_name = $wpdb->prefix . "EWD_UASP_Custom_Fields_Meta";
	} 

	/**
	 * Creates the tables used to store appointments and their meta information
	 * @since 2.0.0
	 */
	public function create_tables() {

		$sql = "CREATE TABLE $this->appointments_table_name (
  			Appointment_ID mediumint(9) NOT NULL AUTO_INCREMENT,
  			Location_Name text  NULL,
  			Location_Post_ID mediumint(9) DEFAULT 0 NOT NULL,
			Service_Name text   NULL,
			Service_Post_ID mediumint(9) DEFAULT 0 NOT NULL,
			Service_Provider_Name text NULL,
			Service_Provider_Post_ID mediumint(9) DEFAULT 0 NOT NULL,
			Appointment_Prepaid text NULL,
			Appointment_PayPal_Receipt_Number text NULL,
			Appointment_Start datetime DEFAULT '0000-00-00 00:00:00' NULL,
			Appointment_End datetime DEFAULT '0000-00-00 00:00:00' NULL,
			Appointment_Client_Name text NULL,
			Appointment_Client_Phone text NULL,
			Appointment_Client_Email text NULL,
			Appointment_Reminder_Email_Sent text NULL,
			Appointment_Confirmation_Received text NULL,
			WC_Order_ID mediumint(9) DEFAULT 0 NOT NULL,
			WC_Order_Paid text NULL,
  			UNIQUE KEY id (Appointment_ID)
    		)
			DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

   		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   		
   		dbDelta($sql);


   		$sql = "CREATE TABLE $this->appointments_meta_table_name (
    		Meta_ID mediumint(9) NOT NULL AUTO_INCREMENT,
    		Field_ID mediumint(9) DEFAULT '0',
    		Appointment_ID mediumint(9) DEFAULT '0',
    		Meta_Value text DEFAULT '' NOT NULL,
    		UNIQUE KEY id (Meta_ID)
    		)
    		DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
    
    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    	
    	dbDelta($sql);
	}

	/**
	 * Returns a single appointment given its appointment ID
	 * @since 2.0.0
	 */
	public function get_appointment_from_id( $appointment_id ) {
		global $wpdb;

		$db_appointment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->appointments_table_name WHERE Appointment_ID=%d", $appointment_id ) );

		return $db_appointment;
	}

	/**
	 * Returns appointments matching the arguments supplied
	 * @since 2.0.0
	 */
	public function get_matching_appointments( $args ) {

		$this->appointments = array();

		$defaults = array(
			'appointments_per_page'	=> 20,
			'date_range'			=> 'upcoming',
			'order'					=> 'ASC',
			'paged'					=> 1,
		);

		$this->args = wp_parse_args( $args, $defaults );

		$this->prepare_args();

		$this->run_query();

		return $this->appointments;
	}

	/**
	 * Return the counts for appointments being displayed on the admin appointments page
	 * @since 2.0.0
	 */
	public function get_appointment_counts( $args ) {
		global $wpdb;

		$this->args = $args;

		$this->prepare_args();

		$args = $this->args;

		$query_string = "SELECT count( * ) AS num_appointments, 
			sum( IF( Appointment_Confirmation_Received = 'Yes', 1, 0) ) as num_confirmed, 
			sum( IF( ( Appointment_Prepaid = 'Yes' OR WC_Order_Paid = 'Yes' ), 1, 0) ) as num_paid
			FROM $this->appointments_table_name
			WHERE 1=%d
		";

		$query_args = array(1);

		if ( ! empty( $args['location'] ) ) {

			$query_string .= " AND Location_Post_ID=%d";
			$query_args[] = intval( $args['location'] );
		}

		if ( ! empty( $args['service'] ) ) {

			$query_string .= " AND Service_Post_ID=%d";
			$query_args[] = intval( $args['service'] );
		}

		if ( ! empty( $args['provider'] ) ) {

			$query_string .= " AND Service_Provider_Post_ID=%d";
			$query_args[] = intval( $args['provider'] );
		}

		if ( ! empty( $args['after'] ) ) {

			$query_string .= " AND Appointment_Start>=%s";
			$query_args[] = $args['after'];
		}

		if ( ! empty( $args['before'] ) ) {

			$query_string .= " AND Appointment_Start<=%s";
			$query_args[] = $args['before'];
		}

		if ( ! empty( $args['date'] ) ) {

			$query_string .= " AND DATE(Appointment_Start)=%s";
			$query_args[] = $args['date'];
		}

		$count_result = $wpdb->get_results( $wpdb->prepare( $query_string, $query_args ) );
		
		$counts = array(
			'total' 	=> (int) $count_result[0]->num_appointments,
			'confirmed'	=> (int) $count_result[0]->num_confirmed,
			'paid'		=> (int) $count_result[0]->num_paid,
		);

		return $counts;
	}

	/**
	 * Prepares the arguments before the query is run
	 * @since 2.0.0
	 */
	public function prepare_args() {

		$args = $this->args;

		if ( ! empty( $args['date_range'] ) and ( empty( $args['before'] ) and empty( $args['after'] ) ) and is_string( $args['date_range'] ) ) {

			if ( !empty( $args['start_date'] ) || !empty( $args['end_date'] ) ) {

				if ( !empty( $args['start_date'] ) ) {
					$args['after'] = sanitize_text_field( $args['start_date'] ) . ( ( isset( $args['start_time'] ) and $args['start_time'] ) ? $args['start_time'] : '' );
				}

				if ( !empty( $args['end_date'] ) ) {
					$args['before'] = sanitize_text_field( $args['end_date'] ) . ( ( isset( $args['end_time'] ) and $args['end_time'] ) ? $args['end_time'] : ' 23:59' );
				}
			} elseif ( $args['date_range'] === 'today' ) {

				$args['after'] = date( 'Y-m-d H:i:s', strtotime( 'today midnight' ) );
				$args['before'] = date( 'Y-m-d H:i:s', strtotime( 'tomorrow midnight' ) );

			} elseif ( $args['date_range'] === 'upcoming' ) {

				$args['after'] = date( 'Y-m-d H:i:s', strtotime( '-1 hour' ) );
			} elseif ( $args['date_range'] === 'past' ) {
				
				$args['before'] = date( 'Y-m-d H:i:s', strtotime( 'now' ) );
			}
		}

		$this->args = $args;

		return $this->args;
	}

	/**
	 * Create and run the SQL query based on the arguments received
	 * @since 2.0.0
	 */
	public function run_query() {
		global $wpdb;

		$args = $this->args;

		$query_string = "SELECT * FROM $this->appointments_table_name WHERE 1=%d";

		$query_args = array(1);

		if ( ! empty( $args['id'] ) ) {

			$query_string .= " AND Appointment_ID=%d";
			$query_args[] = intval( $args['id'] );
		}

		if ( ! empty( $args['location'] ) ) {

			$query_string .= " AND Location_Post_ID=%d";
			$query_args[] = intval( $args['location'] );
		}

		if ( ! empty( $args['service'] ) ) {

			$query_string .= " AND Service_Post_ID=%d";
			$query_args[] = intval( $args['service'] );
		}

		if ( ! empty( $args['provider'] ) ) {

			$query_string .= " AND Service_Provider_Post_ID=%d";
			$query_args[] = intval( $args['provider'] );
		}

		if ( ! empty( $args['client_name'] ) ) {

			$query_string .= " AND Appointment_Client_Name=%s";
			$query_args[] = $args['client_name'];
		}

		if ( ! empty( $args['client_phone'] ) ) {

			$query_string .= " AND Appointment_Client_Phone=%s";
			$query_args[] = $args['client_phone'];
		}

		if ( ! empty( $args['client_email'] ) ) {

			$query_string .= " AND Appointment_Client_Email=%s";
			$query_args[] = $args['client_email'];
		}

		if ( ! empty( $args['reminder_sent'] ) ) {

			$query_string .= " AND Appointment_Reminder_Email_Sent=%s";
			$query_args[] = $args['reminder_sent'];
		}

		if ( ! empty( $args['confirmation'] ) ) {

			$query_string .= " AND Appointment_Confirmation_Received=%s";
			$query_args[] = $args['confirmation'];
		}

		if ( ! empty( $args['paid'] ) ) {

			$query_string .= " AND ( Appointment_Prepaid=%s OR WC_Order_Paid=%s)";
			$query_args[] = $args['paid'];
			$query_args[] = $args['paid'];
		}

		if ( ! empty( $args['after'] ) ) {

			$query_string .= " AND Appointment_Start>=%s";
			$query_args[] = $args['after'];
		}

		if ( ! empty( $args['before'] ) ) {

			$query_string .= " AND Appointment_Start<=%s";
			$query_args[] = $args['before'];
		}

		if ( ! empty( $args['date'] ) ) {

			$query_string .= " AND DATE(Appointment_Start)=%s";
			$query_args[] = $args['date'];
		}

		if ( $args['appointments_per_page'] > 0 ) {

			$query_string .= ' LIMIT ' . intval( ( $args['paged'] - 1 ) * $args['appointments_per_page'] ) . ', ' . intval( $args['appointments_per_page'] );
		}

		$db_appointments = $wpdb->get_results( $wpdb->prepare( $query_string, $query_args ) );

		foreach ( $db_appointments as $db_appointment ) {

			$appointment = new ewduaspAppointment();

			$appointment->load_appointment( $db_appointment );

			$this->appointments[] = $appointment;
		}
	}

	/**
	 * Returns the value for a given field/appointment id pair
	 * @since 2.0.0
	 */
	public function get_appointment_field( $field, $appointment_id ) {
		global $wpdb;

		$db_appointment =  $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->appointments_table_name WHERE Appointment_ID=%d", $appointment_id ) );

		$appointment = new ewduaspAppointment();
		$appointment->load_appointment( $db_appointment );

		return ! empty( $appointment->$field ) ? $appointment->$field : '';
	}

	/**
	 * Returns the value for a given custom_field/appointment pair
	 * @since 2.0.0
	 */
	public function get_field_value( $custom_field_id, $appointment_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT Meta_Value FROM $this->appointments_meta_table_name WHERE Field_ID=%d AND Appointment_ID=%d", $custom_field_id, $appointment_id ) );
	}

	/**
	 * Accepts an appointment object, inserts it into the database, and returns the ID of the newly inserted appointment
	 * @since 2.0.0
	 */
	public function insert_appointment( $appointment ) {
		global $wpdb;
		global $ewd_uasp_controller;

		$query_args = array(
			'Appointment_Start' 				=> ! empty( $appointment->start ) ? $appointment->start : '0000-00-00 00:00:00',
			'Appointment_End' 					=> ! empty( $appointment->end ) ? $appointment->end : '0000-00-00 00:00:00',
			'Location_Name' 					=> ! empty( $appointment->location_name ) ? $appointment->location_name : null,
			'Location_Post_ID' 					=> ! empty( $appointment->location ) ? $appointment->location : 0,
			'Service_Name' 						=> ! empty( $appointment->service_name ) ? $appointment->service_name : null,
			'Service_Post_ID' 					=> ! empty( $appointment->service ) ? $appointment->service : 0,
			'Service_Provider_Name' 			=> ! empty( $appointment->provider_name ) ? $appointment->provider_name : null,
			'Service_Provider_Post_ID' 			=> ! empty( $appointment->provider ) ? $appointment->provider : 0,
			'Appointment_Client_Name' 			=> ! empty( $appointment->client_name ) ? $appointment->client_name : null,
			'Appointment_Client_Phone' 			=> ! empty( $appointment->client_phone ) ? $appointment->client_phone : null,
			'Appointment_Client_Email' 			=> ! empty( $appointment->client_email ) ? $appointment->client_email : null,
			'Appointment_Confirmation_Received'	=> ! empty( $appointment->confirmed ) ? 'Yes' : 'No',
			'Appointment_Reminder_Email_Sent' 	=> ! empty( $appointment->reminder_sent ) ? implode( ',', $appointment->reminder_sent ) : null,
			'Appointment_Prepaid'				=> ! empty( $appointment->paypal_prepaid ) ? 'Yes' : 'No',
			'Appointment_PayPal_Receipt_Number' => ! empty( $appointment->paypal_receipt ) ? $appointment->paypal_receipt : null,
			'WC_Order_Paid'						=> ! empty( $appointment->wc_prepaid ) ? 'Yes' : 'No',
			'WC_Order_ID' 						=> ! empty( $appointment->wc_order_id ) ? $appointment->wc_order_id : 0,
		);

		$wpdb->insert(
			$this->appointments_table_name,
			$query_args
		);
		
		$appointment_id = $wpdb->insert_id;

		if ( ! $appointment_id ) { return $appointment_id; }

		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		foreach ( $custom_fields as $custom_field ) {

			if ( empty( $appointment->custom_fields[ $custom_field->id ] ) ) { continue; }

			$query_args = array(
				'Field_ID'			=> $custom_field->id,
				'Appointment_ID'	=> $appointment_id,
				'Meta_Value'		=> $appointment->custom_fields[ $custom_field->id ]
			);

			$wpdb->insert(
				$this->appointments_meta_table_name,
				$query_args
			);
		}

		return $appointment_id;
	}

	/**
	 * Accepts an appointment object, updates it in the database, and returns the ID if successful or false otherwise
	 * @since 2.0.0
	 */
	public function update_appointment( $appointment ) {
		global $wpdb;
		global $ewd_uasp_controller;

		if ( empty( $appointment->id ) ) { return false; }

		$query_args = array(
			'Appointment_Start' 				=> ! empty( $appointment->start ) ? $appointment->start : '0000-00-00 00:00:00',
			'Appointment_End' 					=> ! empty( $appointment->end ) ? $appointment->end : '0000-00-00 00:00:00',
			'Location_Name' 					=> ! empty( $appointment->location_name ) ? $appointment->location_name : null,
			'Location_Post_ID' 					=> ! empty( $appointment->location ) ? $appointment->location : 0,
			'Service_Name' 						=> ! empty( $appointment->service_name ) ? $appointment->service_name : null,
			'Service_Post_ID' 					=> ! empty( $appointment->service ) ? $appointment->service : 0,
			'Service_Provider_Name' 			=> ! empty( $appointment->provider_name ) ? $appointment->provider_name : null,
			'Service_Provider_Post_ID' 			=> ! empty( $appointment->provider ) ? $appointment->provider : 0,
			'Appointment_Client_Name' 			=> ! empty( $appointment->client_name ) ? $appointment->client_name : null,
			'Appointment_Client_Phone' 			=> ! empty( $appointment->client_phone ) ? $appointment->client_phone : null,
			'Appointment_Client_Email' 			=> ! empty( $appointment->client_email ) ? $appointment->client_email : null,
			'Appointment_Confirmation_Received'	=> ! empty( $appointment->confirmed ) ? 'Yes' : 'No',
			'Appointment_Reminder_Email_Sent' 	=> ! empty( $appointment->reminder_sent ) ? implode( ',', $appointment->reminder_sent ) : null,
			'Appointment_Prepaid'				=> ! empty( $appointment->paypal_prepaid ) ? 'Yes' : 'No',
			'Appointment_PayPal_Receipt_Number' => ! empty( $appointment->paypal_receipt ) ? $appointment->paypal_receipt : null,
			'WC_Order_Paid'						=> ! empty( $appointment->wc_prepaid ) ? 'Yes' : 'No',
			'WC_Order_ID' 						=> ! empty( $appointment->wc_order_id ) ? $appointment->wc_order_id : 0,
		);

		$wpdb->update(
			$this->appointments_table_name,
			$query_args,
			array( 'Appointment_ID' => $appointment->id )
		);

		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		foreach ( $custom_fields as $custom_field ) {

			$delete_args = array(
				'Field_ID'			=> $custom_field->id,
				'Appointment_ID'	=> $appointment->id,
			);

			$wpdb->delete(
				$this->appointments_meta_table_name,
				$delete_args 
			);

			if ( ! empty( $appointment->custom_fields[ $custom_field->id ] ) ) { 

				$query_args = array(
					'Meta_Value' 		=> $appointment->custom_fields[ $custom_field->id ],
					'Field_ID'			=> $custom_field->id,
					'Appointment_ID'	=> $appointment->id,
				);

				$wpdb->insert(
					$this->appointments_meta_table_name,
					$query_args,
				);
			}
		}

		return $appointment->id;
	}

	/**
	 * Accepts an appointment id, deletes the corresponding appointment
	 * @since 2.0.0
	 */
	public function delete_appointment( $appointment_id ) {
		global $wpdb;

		$wpdb->delete(
			$this->appointments_table_name,
			array( 'Appointment_ID' => $appointment_id )
		);

		$wpdb->delete(
			$this->appointments_meta_table_name,
			array( 'Appointment_ID' => $appointment_id )
		);
	}

	/**
	 * Accepts an email and appointment id, verify that they match the saved data,
	 * and set the corresponding appointment to confirmed if they do match
	 * @since 2.0.0
	 */
	public function verify_appointment_confirmation( $email, $appointment_id ) {
		global $wpdb;

		$client_email = $wpdb->get_var( $wpdb->prepare( "SELECT Appointment_Client_Email FROM $this->appointments_table_name WHERE Appointment_ID=%d", $appointment_id ) );

		if ( $client_email == $email ) { 

			$this->set_appointment_to_confirmed( $appointment_id );

			return true;
		}

		return false;
	}

	/**
	 * Accepts an email and appointment id, verify that they match the saved data,
	 * and delete the appointment if they do match
	 * @since 2.0.0
	 */
	public function verify_appointment_cancellation( $email, $appointment_id ) {
		global $wpdb;

		$client_email = $wpdb->get_var( $wpdb->prepare( "SELECT Appointment_Client_Email FROM $this->appointments_table_name WHERE Appointment_ID=%d", $appointment_id ) );

		if ( $client_email == $email ) { 

			$this->delete_appointment( $appointment_id );

			return true;
		}

		return false;
	}

	/**
	 * Accepts an appointment id, set the corresponding appointment to confirmed
	 * @since 2.0.0
	 */
	public function set_appointment_to_confirmed( $appointment_id ) {
		global $wpdb;

		$wpdb->update(
			$this->appointments_table_name,
			array( 'Appointment_Confirmation_Received' => 'Yes' ),
			array( 'Appointment_ID' => $appointment_id )
		);
	}

	/**
	 * Accepts an appointment id, set the corresponding appointment to confirmed
	 * @since 2.0.0
	 */
	public function set_appointment_prepaid( $appointment_id ) {
		global $wpdb;

		$wpdb->update(
			$this->appointments_table_name,
			array( 'Appointment_Prepaid' => 'Yes' ),
			array( 'Appointment_ID' => $appointment_id )
		);
	}
}
}