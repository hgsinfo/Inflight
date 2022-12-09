<?php

/**
 * Create a shortcode to display an appointment booking form
 * @since 2.0.0
 */
function ewd_uasp_appointment_booking_shortcode( $atts ) {
	global $ewd_uasp_controller;

	// Define shortcode attributes
	$booking_atts = array(
		'appointment_selection'			=> '',
		'display_type'					=> '',
		'redirect_page'					=> '',
		'cancellation_success_message' 	=> __( 'Your appointment has been cancelled. Thank you for being courteous!', 'ultimate-appointment-scheduling' ),
		'cancellation_failure_message' 	=> __( 'Your appointment could not be cancelled. Please contact the site administrator.', 'ultimate-appointment-scheduling' ),
		'confirmation_success_message' 	=> __( 'Your appointment has been confirmed. Thank you!', 'ultimate-appointment-scheduling' ),
		'confirmation_failure_message' 	=> __( 'Your appointment could not be confirmed. Please contact the site administrator.', 'ultimate-appointment-scheduling' ),
	);

	// Create filter so addons can modify the accepted attributes
	$booking_atts = apply_filters( 'ewd_uasp_appointment_booking_shortcode_atts', $booking_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $booking_atts, $atts );

	$action = ! empty( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	
	// Handle cancellations and confirmations
	if ( $action == 'confirm_appointment' ) {

		$confirmation = $ewd_uasp_controller->appointment_manager->verify_appointment_confirmation( sanitize_text_field( $_GET['email'] ), intval( $_GET['appointment_id'] ) );

		if ( $confirmation ) { $args['update_message'] = $args['confirmation_success_message']; }
		else { $args['update_message'] = $args['confirmation_failure_message']; }
	}
	elseif ( $action == 'cancel_appointment' ) {

		$confirmation = $ewd_uasp_controller->appointment_manager->verify_appointment_cancellation( sanitize_text_field( $_GET['email'] ), intval( $_GET['appointment_id'] ) );

		if ( $confirmation ) { $args['update_message'] = $args['cancellation_success_message']; }
		else { $args['update_message'] = $args['cancellation_failure_message']; }
	}

	// Handle booking submission
	if ( isset( $_POST['ewd_uasp_payment_submit'] ) or isset( $_POST['ewd_uasp_submit_booking'] ) ) {

		$args['booking_submitted'] = true;

		$appointment = new ewduaspAppointment();
		$status = $appointment->process_client_appointment_submission();

		if ( ! $status ) {

			$args['update_message'] = '';

			foreach ( $appointment->validation_errors as $validation_error ) {

				$args['update_message'] .= '<br />' . $validation_error['message'];
			}
		}
		elseif ( $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) and empty( $appointment->wc_prepaid ) ) {
			
			$ewd_uasp_controller->woocommerce->add_possible_appointment_deletion( $appointment );

			$ewd_uasp_controller->woocommerce->add_service_to_cart( $appointment );

			$checkout_url = get_permalink( get_option( 'woocommerce_cart_page_id' ) );

			wp_redirect( $checkout_url );

			exit();
		}
		elseif ( $status == 'paypal_payment_required' ) {

			$args['appointment'] = $appointment;

			$args['update_message'] = $ewd_uasp_controller->settings->get_setting( 'label-payment-required' ); 
		}
		elseif ( ! empty( $args['redirect_page'] ) ) { 
			
			wp_safe_redirect( $args['redirect_page'] );

			exit();
		}
		else { $args['update_message'] = $ewd_uasp_controller->settings->get_setting( 'label-thank-you-submit' ); }
	}

	// Handle appointment editing search 
	if ( isset( $_POST['ewd_uasp_edit_appointment_search'] ) ) {

		$args = array(
			'client_name'	=> ! empty( $_POST['ewd_uasp_client_name'] ) ? sanitize_text_field( $_POST['ewd_uasp_client_name'] ) : '',
			'client_phone'	=> ! empty( $_POST['ewd_uasp_client_phone'] ) ? sanitize_text_field( $_POST['ewd_uasp_client_phone'] ) : '',
			'client_email'	=> ! empty( $_POST['ewd_uasp_client_email'] ) ? sanitize_text_field( $_POST['ewd_uasp_client_email'] ) : '',
		);

		$args['edit_appointment_search_results'] = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

		if ( empty( $args['edit_appointment_search_results'] ) ) { $args['update_message'] = __( 'No matching appointments were found', 'ultimate-appointment-scheduling' ); }
	}

	// Handle appointment editing based on appointment ID
	if ( ! empty( $_POST['ewd_uasp_appointment_id'] ) ) { $args['edit_appointment_id'] = intval( $_POST['ewd_uasp_appointment_id'] ); }

	// Render booking form
	ewd_uasp_load_view_files();

	$booking = new ewduaspViewAppointmentBooking( $args );

	$booking->set_display_parameters();

	$booking->set_request_parameters();

	$output = $booking->render();

	return $output;
}
add_shortcode( 'ultimate-appointment-dropdown', 'ewd_uasp_appointment_booking_shortcode' );

function ewd_uasp_calendar_shortcode( $atts ) {

	return do_shortcode( '[ultimate-appointment-dropdown display_type="Calendar"]' );
}
add_shortcode( 'ultimate-appointment-calendar', 'ewd_uasp_calendar_shortcode' );

function ewd_uasp_appointment_shortcode_removal_bridge( $atts ) {

	echo ewd_uasp_appointment_booking_shortcode( $atts );
}
add_shortcode( 'edit-appointment', 'ewd_uasp_appointment_shortcode_removal_bridge' );
add_shortcode( 'confirm-appointment', 'ewd_uasp_appointment_shortcode_removal_bridge' );

function ewd_uasp_load_view_files() {

	$files = array(
		EWD_UASP_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'ewd_uasp_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}

}

if ( ! function_exists( 'ewd_uasp_validate_captcha' ) ) {
function ewd_uasp_validate_captcha() {

	$modifiedcode = intval( $_POST['ewd_uasp_modified_captcha'] );
	$usercode = intval( $_POST['ewd_uasp_captcha'] );

	$code = ewd_uasp_decrypt_catpcha_code( $modifiedcode );

	$validate_captcha = $code == $usercode ? 'Yes' : 'No';

	return $validate_captcha;
}
}

if ( ! function_exists( 'ewd_uasp_encrypt_captcha_code' ) ) {
function ewd_uasp_encrypt_captcha_code( $code ) {
	
	$modifiedcode = ($code + 5) * 3;

	return $modifiedcode;
}
}

if ( ! function_exists( 'ewd_uasp_encrypt_captcha_code' ) ) {
function ewd_uasp_decrypt_catpcha_code( $modifiedcode ) {
	
	$code = ($modifiedcode / 3) - 5;

	return $code;
}
}

if ( ! function_exists( 'ewd_uasp_decode_infinite_table_setting' ) ) {
function ewd_uasp_decode_infinite_table_setting( $values ) {
	
	return is_array( json_decode( html_entity_decode( $values ) ) ) ? json_decode( html_entity_decode( $values ) ) : array();
}
}

// add an output buffer layer for the plugin
add_action(	'init', 'ewd_uasp_add_ob_start' );
add_action(	'shutdown', 'ewd_uasp_flush_ob_end' );

// If there's an IPN request, add our setup function to potentially handle it
if ( isset($_POST['ipn_track_id']) ) { add_action( 'init', 'ewd_uasp_setup_paypal_ipn', 11 ); }

/**
 * Sets up the PayPal IPN process
 * @since 2.0.0
 */
if ( !function_exists( 'ewd_uasp_setup_paypal_ipn' ) ) {
function ewd_uasp_setup_paypal_ipn() {
	global $ewd_uasp_controller;

	if ( $ewd_uasp_controller->settings->get_setting( 'require-deposit' ) == 'none' ) { return; }
	
	ewd_uasp_handle_paypal_ipn();
}
} // endif;

/**
 * Handle PayPal IPN requests
 * @since 2.0.0
 */
if ( !function_exists( 'ewd_uasp_handle_paypal_ipn' ) ) {
function ewd_uasp_handle_paypal_ipn() {
	
	// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
	// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
	// Set this to 0 once you go live or don't require logging.
	$debug = get_option( 'ewd_uasp_enable_payment_debugging' );

	// Set to 0 once you're ready to go live
	define("EWD_UASP_USE_SANDBOX", 0);
	define("EWD_UASP_LOG_FILE", "ipn.log");
	// Read POST data
	// reading posted data directly from $_POST causes serialization
	// issues with array data in POST. Reading raw POST data from input stream instead.
	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
		$keyval = explode ('=', $keyval);
		if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
	}
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	if(function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}
	foreach ($myPost as $key => $value) {
		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}
	// Post IPN data back to PayPal to validate the IPN data is genuine
	// Without this step anyone can fake IPN data
	if(EWD_UASP_USE_SANDBOX == true) {
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$response = wp_remote_post($paypal_url, array(
		'method' => 'POST',
		'body' => $req,
		'timeout' => 30
	));
	
	// Inspect IPN validation result and act accordingly
	// Split response headers and payload, a better way for strcmp
	$tokens = explode("\r\n\r\n", trim($response['body'])); 
	$res = trim(end($tokens));

	if ( $debug ) {
		update_option( 'ewd_uasp_debugging', get_option( 'ewd_uasp_debugging' ) . print_r( date('[Y-m-d H:i e] '). "IPN response: $res - $req ". PHP_EOL, true ) );
	}

	if (strcmp ($res, "VERIFIED") == 0) {
			
		$paypal_receipt_number = sanitize_text_field( $_POST['txn_id'] );
		$payment_amount = sanitize_text_field( $_POST['mc_gross'] );
		
		$appointment_id = intval( $_POST['custom'] );
		
		$appointment = new ewduaspAppointment();
		$appointment->load_appointment_from_id( $appointment_id );

		if ( ! $appointment->id ) { return; }
			
		$appointment->paypal_prepaid = 'Yes';
		$appointment->paypal_receipt = sanitize_text_field( $paypal_receipt_number );

		$appointment->update_appointment();

		do_action( 'ewd_uasp_appointment_paid', $appointment );
	}
}
} // endif;

/**
 * Opens a buffer when handling PayPal IPN requests
 * @since 2.0.0
 */
if ( !function_exists( 'ewd_uasp_add_ob_start' ) ) {
function ewd_uasp_add_ob_start() { 
    ob_start();
}
} // endif;

/**
 * Closes a buffer when handling PayPal IPN requests
 * @since 2.0.0
 */
if ( !function_exists( 'ewd_uasp_flush_ob_end' ) ) {
function ewd_uasp_flush_ob_end() {
    if ( ob_get_length() ) { ob_end_clean(); }
}
} // endif;

if ( ! function_exists( 'ewd_hex_to_rgb' ) ) {
function ewd_hex_to_rgb( $hex ) {

	$hex = str_replace("#", "", $hex);

	// return if the string isn't a color code
	if ( strlen( $hex ) !== 3 and strlen( $hex ) !== 6 ) { return '0,0,0'; }

	if(strlen($hex) == 3) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = $r . ", " . $g . ", " . $b;
  
	return $rgb;
}
}

if ( ! function_exists( 'ewd_format_classes' ) ) {
function ewd_format_classes( $classes ) {

	if ( count( $classes ) ) {
		return ' class="' . join( ' ', $classes ) . '"';
	}
}
}

if ( ! function_exists( 'ewd_add_frontend_ajax_url' ) ) {
function ewd_add_frontend_ajax_url() { ?>
    
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php }
}

if ( ! function_exists( 'ewd_random_string' ) ) {
function ewd_random_string( $length = 10 ) {

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';

    for ( $i = 0; $i < $length; $i++ ) {

        $randstring .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
    }

    return $randstring;
}
}