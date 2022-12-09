<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduaspAJAX' ) ) {
	/**
	 * Class to handle AJAX interactions for Ultimate Appointment Scheduling
	 *
	 * @since 2.0.0
	 */
	class ewduaspAJAX {

		public function __construct() { 

			add_action( 'wp_ajax_ewd_uasp_get_events', array( $this, 'get_events' ) );
			add_action( 'wp_ajax_nopriv_ewd_uasp_get_events', array( $this, 'get_events' ) );

			add_action( 'wp_ajax_ewd_uasp_delete_appointment', array( $this, 'admin_delete_appointment' ) );

			add_action( 'wp_ajax_ewd_uasp_get_appointments', array( $this, 'get_appointments_times' ) );
			add_action( 'wp_ajax_nopriv_ewd_uasp_get_appointments', array( $this, 'get_appointments_times' ) );

			add_action( 'wp_ajax_ewd_uasp_get_service_providers', array( $this, 'get_service_providers' ) );
			add_action( 'wp_ajax_nopriv_ewd_uasp_get_service_providers', array( $this, 'get_service_providers' ) );

			add_action( 'wp_ajax_ewd_uasp_send_test_email', array( $this, 'send_test_email' ) );
		}

		public function get_events() {
			global $ewd_uasp_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uasp-calendar-js', 'nonce' ) ) {
				ewduaspHelper::bad_nonce_ajax();
			}

			$time_between_appointments = $ewd_uasp_controller->settings->get_setting( 'time-between-appointments' );
		
			$location_id = intval( $_POST['location'] );
			$service_id = intval( $_POST['service'] );
			$selected_provider = sanitize_text_field( $_POST['service_provider'] );

			$start_time = intval( $_POST['start'] );
			$end_time = intval( $_POST['end'] );

			$service_duration = get_post_meta( $service_id, 'Service Duration', true);

			$minimum_time = $ewd_uasp_controller->settings->get_setting( 'minimum-hours-advance' ) ? time() + ( $ewd_uasp_controller->settings->get_setting( 'minimum-hours-advance' ) * 60 * 60 ) : time() + ( $ewd_uasp_controller->settings->get_setting( 'minimum-days-advance' ) * 24 * 60 * 60 );
			$maximum_time = time() + ( $ewd_uasp_controller->settings->get_setting( 'maximum-days-advance' ) * 24 * 60 * 60 );

			$minimum_time = $minimum_time + ( 3600 - ( $minimum_time % 3600 ) );

			$date_counter = $start_time;

			$minimum_midnight = strtotime( '0:00', $minimum_time );
			
			$days = array();
			
			while ( $date_counter <= $end_time ) {

				if ( $date_counter >= $minimum_midnight and $date_counter <= $maximum_time ) {
					
					$date_string = date( 'Y-m-d', $date_counter );
					$days[$date_string] = date( 'l', $date_counter ); 
				}

				$date_counter += ( 24 * 60 * 60 );
			}

			$events = array();

			$args = array(
				'post_type'		=> EWD_UASP_EXCEPTION_POST_TYPE,
				'numberposts'	=> -1,
			);

			$exceptions = get_posts( $args );
		
			foreach ( $days as $date => $day ) {

				$args = array(
					'location_id' 	=> $location_id,
					'service_id' 	=> $service_id,
					'day'			=> $day
				);
				
				$service_providers = $ewd_uasp_controller->cpts->get_service_providers( $args );
				
				if ( $selected_provider != 'all' ) { 

					foreach ( $service_providers as $key => $service_provider ) {

						if ( $service_provider->ID != $selected_provider ) { unset( $service_providers[ $key ] ); }
					}
				}
				
				foreach ( $service_providers as $service_provider ) {
		
					$status_changes = array();
					$status_changes[] = array( 'time' => max( $minimum_time, strtotime( $date . get_post_meta($service_provider->ID, $day . ' Start', true ) ) ), 'event' => 'shift', 'status' => 'available');
					$status_changes[] = array( 'time' => min( $maximum_time, strtotime( $date . get_post_meta($service_provider->ID, $day . ' Finish', true ) ) ) - ($service_duration * 60) + 1, 'event' => 'shift', 'status' => 'unavailable');
		
					$args = array(
						'provider'	=> $service_provider->ID,
						'date'		=> $date
					);

					$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );
					
					foreach ( $appointments as $appointment ) {

						$start_time = strtotime( $appointment->start ) - ( $time_between_appointments * 60 ) + 1 - ( $service_duration * 60 );
						$end_time = strtotime( $appointment->end ) + ( $time_between_appointments * 60 );

						$status_changes[] = array( 'time' => $start_time, 'event' => 'appointment', 'status' => 'unavailable' );
						$status_changes[] = array( 'time' => $end_time, 'event' => 'appointment', 'status' => 'available' );
					}

					foreach ( $exceptions as $exception ) {

						if ( get_post_meta( $exception->ID, 'location_id', true ) and get_post_meta( $exception->ID, 'location_id', true ) != $location_id ) { continue; }

						if ( get_post_meta( $exception->ID, 'provider_id', true ) and get_post_meta( $exception->ID, 'provider_id', true ) != $service_provider->ID ) { continue; }

						$status_changes[] = array('time' => strtotime( get_post_meta( $exception->ID, 'start', true ) ), 'event' => 'exception', 'status' => 'unavailable');

						$status_changes[] = array('time' => strtotime( get_post_meta( $exception->ID, 'end', true ) ), 'event' => 'exception', 'status' => 'available');
					}

					usort( $status_changes, array( $this, 'sort_changes_by_time' ) );

					$current_event = false;
					$working = false;
					$appointment = false;
					$exception = false;

					$event = array(
						'title' => $service_provider->post_title, 
						'provider' => $service_provider->ID
					);

					foreach ( $status_changes as $status ) {

						if ( $status['event'] == 'shift' ) {

							$working = $status['status'] == 'available' ? true : false;
						}

						if ( $status['event'] == 'appointment' ) {

							$appointment = $status['status'] == 'available' ? false : true;
						}

						if ( $status['event'] == 'exception' ) {

							$exception = $status['status'] == 'available' ? false : true;
						}

						if ( ! $current_event and $working and ! $appointment and ! $exception ) {

							$event['start'] = date( 'Y-m-d H:i:s', $status['time'] );
							$current_event = true;
						}
						elseif ( $current_event ) {

							$event['end'] = date( 'Y-m-d H:i:s', $status['time'] );

							if ( $event['start'] != $event['end'] ) { $events[] = $event; }

							$current_event = false;

							$event = array( 'title' => $service_provider->post_title, 'provider' => $service_provider->ID );
						}
					}
				}
			}

			wp_send_json_success(
				array(
					'events' => $events,
				)
			);
		}

		public function admin_delete_appointment() {
			global $ewd_uasp_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uasp-admin-js', 'nonce' ) || ! current_user_can( 'manage_options' ) ) {
				ewduaspHelper::admin_nopriv_ajax();
			}

			if ( ! current_user_can( $ewd_uasp_controller->settings->get_setting( 'access-role' ) ) ) { return; }

			$ewd_uasp_controller->appointment_manager->delete_appointment( intval( $_POST['appointment_id'] ) );
		}
		
		public function get_appointments_times() {
			global $ewd_uasp_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uasp-js', 'nonce' ) ) {
				ewduaspHelper::bad_nonce_ajax();
			}

			$time_between_appointments = $ewd_uasp_controller->settings->get_setting( 'time-between-appointments' );

			$minimum_time = $ewd_uasp_controller->settings->get_setting( 'minimum-hours-advance' ) ? time() + ( $ewd_uasp_controller->settings->get_setting( 'minimum-hours-advance' ) * 60 * 60 ) : time() + ( $ewd_uasp_controller->settings->get_setting( 'minimum-days-advance' ) * 24 * 60 * 60 );

			$maximum_time = time() + ( $ewd_uasp_controller->settings->get_setting( 'maximum-days-advance' ) * 24 * 60 * 60 );

			$selected_service_provider = sanitize_text_field( $_POST['service_provider_id'] );

			$service_duration_hours = get_post_meta( intval( $_POST['service_id'] ), 'Service Duration', true ) / 60;

			$location = intval( $_POST['location_id'] );
			$service = intval( $_POST['service_id'] );
			
			$appointment_id = isset( $_POST['appointment_id'] ) ? intval( $_POST['appointment_id'] ) : 0;

			$selected_appointment = new ewduaspAppointment();
			if ( $appointment_id ) { $selected_appointment->load_appointment_from_id( $appointment_id ); }

			$date = sanitize_text_field( $_POST['date'] );
			$day = date( 'l', strtotime( $date ) );

			$args = array(
				'location_id' 	=> $location,
				'service_id' 	=> $service,
				'day'			=> $day
			);

			$service_providers = $ewd_uasp_controller->cpts->get_service_providers( $args );

			if ( $selected_service_provider != 'all' ){

				foreach ( $service_providers as $key => $service_provider ) {

					if ( $service_provider->ID != $selected_service_provider ) { unset( $service_providers[ $key ] ); }
				}
			}

			$args = array(
				'post_type'		=> EWD_UASP_EXCEPTION_POST_TYPE,
				'numberposts'	=> -1
			);

			$exceptions = get_posts( $args );

			$appointments_found = false;

			$output = '<input type="hidden" name="ewd_uasp_appointment_start" />';

			foreach ( $service_providers as $service_provider ) {

				$args = array(
					'appointments_per_page'	=> -1,
					'provider'				=> $service_provider->ID,
					'date'					=> $date,
				);

				$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

				$start = get_post_meta( $service_provider->ID, $day . ' Start', true);
				$finish = get_post_meta( $service_provider->ID, $day . ' Finish', true);

				$start_time = $this->convert_time_to_number( $start );
				$finish_time = $this->convert_time_to_number( $finish );

				// make sure that min/max start and end times are respected
				$min_start = ( max( strtotime( $date ) + $start_time * 3600, $minimum_time ) - strtotime( $date ) ) / 3600;
				$max_finish = ( min( strtotime( $date ) + $finish_time * 3600, $maximum_time ) - strtotime( $date ) ) / 3600;

				$time_counter = round( $min_start ) * 60;

				$appointment_times = array();

				if( 0 > $time_between_appointments ) {
					$time_between_appointments = 30;
				}

				while ( ( $time_counter / 60 ) <= ( $max_finish - $service_duration_hours ) ) {
					$appointment_times[] = $time_counter / 60;
					$time_counter = $time_counter + $time_between_appointments;
				}

				foreach ( $appointments as $appointment ) {

					if ( $selected_appointment->id == $appointment->id ) { continue; }

					$appointment_start_time = $this->convert_time_to_number( substr( $appointment->start, 11, 5 ) );
					$appointment_end_time = $this->convert_time_to_number( substr( $appointment->end, 11, 5 ) );

					foreach ( $appointment_times as $key => $appt_time ) {

						if ( ( ( $appt_time + $service_duration_hours + ( $time_between_appointments / 60 ) - .001) > $appointment_start_time ) and ( $appt_time < $appointment_end_time + ( $time_between_appointments / 60 ) - .001 ) ) {
							
							unset( $appointment_times[ $key ] );
						}
					}
				}

				$date_to_time = strtotime( $date );

				foreach ( $exceptions as $exception ) {

					$ex_start = strtotime( get_post_meta( $exception->ID, 'start', true ) );
					$ex_end = strtotime( get_post_meta( $exception->ID, 'end', true ) );

					if ( $ex_start > $date_to_time or $ex_end < $date_to_time ) { continue; }

					if ( get_post_meta( $exception->ID, 'location_id', true ) and get_post_meta( $exception->ID, 'location_id', true ) != $location ) { continue; }

					if ( get_post_meta( $exception->ID, 'provider_id', true ) and get_post_meta( $exception->ID, 'provider_id', true ) != $service_provider->ID ) { continue; }


					$exception_start_time = $this->convert_time_to_number( substr( $exception->start, 11, 5 ) );
					$exception_end_time = $this->convert_time_to_number( substr($exception->end, 11, 5) );

					foreach ( $appointment_times as $key => $appt_time ) {

						if ( ( $appt_time + $service_duration_hours) > $exception_start_time and $appt_time < $exception_end_time ) {

							unset( $appointment_times[$key] );
						}
					}
				}
			
				if ( count( $appointment_times ) > 0 ) {

					$appointments_found = true;

					if ( $selected_service_provider == 'all' ) { $output.= '<div class="ewd-uasp-appointments-provider-label">' . $service_provider->post_title . '</div>'; }

					$output .= "<div class='ewd-uasp-available-appointments'>";

					foreach ($appointment_times as $appointment_time) {
						
						$appointment_start_time = $this->convert_number_to_time($appointment_time);
						$appointment_start_display_time = $ewd_uasp_controller->settings->get_setting( 'hours-format' ) == '12' ? $this->convert_number_to_12_hour_time( $appointment_time ) : $appointment_start_time;
						
						$output .= '<div class="ewd-uasp-appointment-listing">';
						$output .= '<a class="ewd-uasp-appointment-link" data-service_provider_id="' . $service_provider->ID . '" data-appointment_start_time="' . $appointment_start_time . '">';
						$output .= $appointment_start_display_time;
						$output .= '</a>';
						$output .= '</div>';
					}
					$output .= '</div>';
					$output .= '<div class="clear"></div>';
				}
			}
		
			if ( empty( $appointments_found ) ) {
				$output .= __( 'No Appointments Found', 'ultimate-appointment-scheduling' );
			}
			
			wp_send_json_success(
				array(
					'output' => $output,
				)
			);

			die();
		}

		/**
		 * Returns the available service providers given a location and service
		 * @since 2.0.0
		 */
		public function get_service_providers() {
			global $ewd_uasp_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uasp-js', 'nonce' ) ) {
				ewduaspHelper::bad_nonce_ajax();
			}

			$args = array(
				'location_id' 	=> intval( $_POST['location_id'] ),
				'service_id' 	=> intval( $_POST['service_id'] ),
			);

			$service_providers = $ewd_uasp_controller->cpts->get_service_providers( $args );

			ob_start();

			?>

			<?php if ( ! sizeOf( $service_providers ) ) { ?>

				<div><?php _e( 'No providers available for that location/service combination.', 'ultimate-appointment-scheduling' ); ?></div>
			
			<?php } else { ?>

				<select id='ewd-uasp-das-service-provider'  class='ewd-uasp-das-select' name='ewd_uasp_provider_id'>

					<?php if ( sizeof( $service_providers ) > 1 ) { ?> 

						<option value='all'><?php echo $ewd_uasp_controller->settings->get_setting( 'label-any' ); ?></option>

					<?php } ?>

					<?php foreach ( $service_providers as $service_provider ) { ?>

						<option value='<?php echo esc_attr( $service_provider->ID ); ?>'><?php echo esc_html( $service_provider->post_title ); ?></option>

					<?php } ?>

				</select>

			<?php } ?>

			<?php

			$output = ob_get_clean();

			wp_send_json_success(
				array(
					'output' => $output,
				)
			);

		    die();
		}

		/**
		 * Send a test email for the appointment emails
		 * @since 2.0.0
		 */
		public function send_test_email() {
			global $ewd_uasp_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uasp-admin-js', 'nonce' ) || ! current_user_can( 'manage_options' ) ) {
				ewduaspHelper::admin_nopriv_ajax();
			}

			$email_address = sanitize_email( $_POST['email_address'] );
			$email_to_send = intval( $_POST['email_to_send'] );
			$appointment = new ewduaspAppointment();

			$mail_success = $ewd_uasp_controller->notifications->send_email( $email_to_send, $email_address, $appointment );

			if ( ! empty( $mail_success ) ) { 

				echo '<div class="ewd-uasp-test-email-response">Success: Email has been sent successfully.</div>';
			}
			else {

				echo '<div class="ewd-uasp-test-email-response">Error: Please check your email settings, or try using an SMTP email plugin to change email settings.</div>';
			}

			die();
		}

		public function sort_changes_by_time( $a, $b ) {

			return $a['time'] - $b['time'];
		}

		public function convert_time_to_number( $time ) {
			global $ewd_uasp_controller;

			$hours = substr( $time, 0, strpos( $time, ':' ) );
			$minutes = substr( $time, strpos( $time, ':' ) + 1 );
		
			return $hours + ( $minutes / 60 );
		}

		function convert_number_to_time( $time ) {

			$hours = floor( $time );
			$minutes = ( $time - $hours ) * 60;
		
			while ( strlen( $minutes ) < 2 ) { $minutes = '0' . $minutes; }
		
			return $hours . ':' . $minutes;;
		}

		function convert_number_to_12_hour_time( $time ) {

			$hours = floor( $time );
			$minutes = ( $time - $hours ) * 60;
			
			$hours = ( $hours > 12 ) ? $hours - 12 : $hours;
		
			while ( strlen( $minutes ) < 2 ) { $minutes = '0' . $minutes; }
		
			$ending = ( $time >= 12 ) ? ' p.m.' : ' a.m.';
		
			return $hours . ':' . $minutes . $ending;
		}
	}
}