<?php

/**
 * Class to handle sending notifications when an FAQ is submitted
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduaspNotifications' ) ) {
class ewduaspNotifications {

	public function __construct() {

		add_action( 'widgets_init', array( $this, 'check_email_reminders' ) );
		
		add_action( 'ewd_uasp_insert_appointment', array( $this, 'admin_notification_email' ) );
		add_action( 'ewd_uasp_insert_appointment', array( $this, 'user_notification_email' ) );
		add_action( 'ewd_uasp_insert_appointment', array( $this, 'provider_notification_email' ) );

		add_action( 'ewd_uasp_admin_insert_appointment', array( $this, 'provider_notification_email' ) );
	}

	/**
	 * Check whether appointment reminders should be sent out
	 *
	 * @since 2.0.0
	 */
	public function check_email_reminders() {
		global $ewd_uasp_controller;

		if ( get_transient( 'ewd-uasp-send-reminders' ) ) { return; }

		set_transient( 'ewd-uasp-send-reminders', true, $ewd_uasp_controller->settings->get_setting( 'reminders-cache-time' ) * 60 );

		$reminders = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'email-reminders' ) );

		date_default_timezone_set( get_option( 'timezone_string' ) );

		foreach ( $reminders as $reminder ) {

			$seconds_offset = $reminder->interval * ( $reminder->unit == 'days' ? 24 * 60 : 60 ) * 60;

			$args = array(
				'before' 	=> date( 'Y-m-d H:i:s', time() + $seconds_offset )
			);

			$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

			foreach ( $appointments as $appointment ) {

				if ( $reminder->conditional == 'yes' and $appointment->confirmed ) { continue; }

				if ( in_array( $reminder->id, $appointment->reminder_sent ) ) { continue; }

				$email_address = $appointment->client_email;

				if ( ! $email_address ) { return; }
		
				if ( $reminder->email_id < 0 ) {

					$args = array(
						'Email_ID'			=> $reminder->email_id * -1,
						'appointment_id'	=> $appointment->id,
						'Email_Address'		=> $email_address
					);
		
					if ( function_exists( 'EWD_UWPM_Send_Email_To_Non_User' ) ) { EWD_UWPM_Send_Email_To_Non_User( $args ); }
				}
				else {
		
					$this->send_email( $reminder->email_id, $email_address, $appointment );
				}

				$appointment->reminder_sent[] = $reminder->id;

				$appointment->update_appointment();
			}
		}
	}

	/**
	 * Send an email to the site admin when an appointment is created, if selected
	 *
	 * @since 2.0.0
	 */
	public function admin_notification_email( $appointment ) {
		global $ewd_uasp_controller;

		$email_id = $ewd_uasp_controller->settings->get_setting( 'admin-appointment-notification' );

		if ( ! $email_id ) { return; }

		$email_address = $ewd_uasp_controller->settings->get_setting( 'admin-email-address' ) ? $ewd_uasp_controller->settings->get_setting( 'admin-email-address' ) : get_option( 'admin_email' );
	
		if ( $email_id < 0 ) {

			$args = array(
				'Email_ID'			=> $email_id * -1,
				'appointment_id'	=> $appointment->id,
				'Email_Address'		=> $email_address
			);

			if ( function_exists( 'EWD_UWPM_Send_Email_To_Non_User' ) ) { EWD_UWPM_Send_Email_To_Non_User( $args ); }
		}
		else {

			$this->send_email( $email_id, $email_address, $appointment );
		}
	}

	/**
	 * Send an email to the client when an appointment is created, if selected
	 *
	 * @since 2.0.0
	 */
	public function user_notification_email( $appointment ) {
		global $ewd_uasp_controller;

		$email_id = $ewd_uasp_controller->settings->get_setting( 'client-email-details' );

		if ( ! $email_id ) { return; }

		$email_address = $appointment->client_email;

		if ( ! $email_address ) { return; }
		
		if ( $email_id < 0 ) {

			$args = array(
				'Email_ID'			=> $email_id * -1,
				'appointment_id'	=> $appointment->id,
				'Email_Address'		=> $email_address
			);

			if ( function_exists( 'EWD_UWPM_Send_Email_To_Non_User' ) ) { EWD_UWPM_Send_Email_To_Non_User( $args ); }
		}
		else {

			$this->send_email( $email_id, $email_address, $appointment );
		}
	}

	/**
	 * Send an email to the client when an appointment is created, if selected
	 *
	 * @since 2.0.0
	 */
	public function provider_notification_email( $appointment ) {
		global $ewd_uasp_controller;

		$email_id = $ewd_uasp_controller->settings->get_setting( 'service-provider-notification' );

		if ( ! $email_id ) { return; }

		$email_address = get_post_meta( $appointment->provider, 'Service Provider Email', true );

		if ( ! $email_address ) { return; }
		
		if ( $email_id < 0 ) {

			$args = array(
				'Email_ID'			=> $email_id * -1,
				'appointment_id'	=> $appointment->id,
				'Email_Address'		=> $email_address
			);

			if ( function_exists( 'EWD_UWPM_Send_Email_To_Non_User' ) ) { EWD_UWPM_Send_Email_To_Non_User( $args ); }
		}
		else {

			$this->send_email( $email_id, $email_address, $appointment );
		}
	}

	/**
	 * Send an email using an admin created template
	 *
	 * @since 2.0.0
	 */
	public function send_email( $email_id, $email_address, $appointment ) {
		global $ewd_uasp_controller;

		$email_messages = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'email-messages-array' ) );

		foreach ( $email_messages as $email_message ) {

			if ( $email_message->id != $email_id ) { continue; }

			if( isset( $appointment->id ) && ! empty( $appointment->id ) ) {
				// Test email throws plenty of issues
				$message = $this->substitute_email_text( $this->get_email_template( $email_message ), $appointment );
				$subject = $this->substitute_email_text( $email_message->subject, $appointment);
			}
			else {
				$message = $this->get_email_template( $email_message );
				$subject = $email_message->subject;
			}

			$headers = array( 'Content-Type: text/html; charset=UTF-8' );

			$mail_success = wp_mail( $email_address, $subject, $message, $headers );

			return $mail_success;
		}
	}

	/**
	 * Replace plugin-defined tags with appointment information
	 *
	 * @since 2.0.0
	 */
	function substitute_email_text( $text, $appointment ) {
		global $ewd_uasp_controller;
	
		$args = array(
			'appointment_id'	=> $appointment->id,
			'email'				=> $appointment->client_email,
			'action'			=> 'confirm_appointment'
		);
	
		$confirmation_url = add_query_arg( $args, $ewd_uasp_controller->settings->get_setting( 'appointment-booking-page' ) );
	
		$confirmation_link = "[button link='" . $confirmation_url . "']" . __( 'Confirm your appointment', 'ultimate-appointment-scheduling' ) . "[/button]";
	
		$args = array(
			'appointment_id'	=> $appointment->id,
			'email'				=> $appointment->client_email,
			'action'			=> 'cancel_appointment'
		);
	
		$cancellation_url = add_query_arg( $args, $ewd_uasp_controller->settings->get_setting( 'appointment-booking-page' ) );
	
		$cancellation_link = "[button link='" . $cancellation_url . "']" . __( 'Cancel your appointment', 'ultimate-appointment-scheduling' ) . "[/button]";
	
		$admin_appointment_link = get_site_url() . 'wp-admin/admin.php?page=ewd-uasp-add-edit-appointment&appointment_id=' . $appointment->id;
	
		date_default_timezone_set( get_option( 'timezone_string' ) );
	
		$search = array(
			"[appointment-time]",
			"[client]",
			"[phone]",
			"[email]",
			"[location]",
			"[service]",
			"[service-provider]",
			"[confirmation-link]",
			"[cancellation-link]",
			"[admin-appointment-link]"
		);
	
		$replace = array(
			date( $ewd_uasp_controller->settings->get_setting( 'date-format' ), strtotime( $appointment->start ) ),
			$appointment->client_name,
			$appointment->client_phone,
			$appointment->client_email,
			$appointment->location_name,
			$appointment->service_name,
			$appointment->provider_name,
			$confirmation_link,
			$cancellation_link,
			$admin_appointment_link
		);
	
		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );
	
		foreach ( $custom_fields as $custom_field ) {

			$value = $ewd_uasp_controller->appointment_manager->get_field_value( $custom_field->id, $appointment->id );
		
			$search[] = '[' . $custom_field->slug . ']';
			$replace[] = $value;
		}
	
		$appointment_text = str_replace( $search, $replace, $text );
	
		return $this->replace_email_content( $appointment_text );
	}

	/**
	 * Returns a template of the email message, along with admin styling for it
	 *
	 * @since 2.0.0
	 */
	public function get_email_template( $email_message ) {
		global $ewd_uasp_controller;

		$message_title = $email_message->subject;
		$message_content = $this->replace_email_content( stripslashes( $email_message->message ) );
	
		$background_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-background-color' );
		$inner_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-inner-color' );
		$text_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-text-color' );
		$button_background_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-button-bg-color' );
		$button_text_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-button-text-color' );
		$button_background_hover_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-button-bg-hover-color' );
		$button_text_hover_color = $ewd_uasp_controller->settings->get_setting( 'styling-email-button-text-hover-color' );
	
		$message =   <<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>$message_title</title>
	
	
<style type="text/css">
	
.body-wrap {
	background-color: {$background_color} !important;
}
.btn-primary {
	background-color: {$button_background_color} !important;
	border-color: $button_background_color !important;
	color: {$button_text_color} !important;
}
.btn-primary:hover {
	background-color: {$button_background_hover_color} !important;
	border-color: $button_background_hover_color !important;
	color: {$button_text_hover_color} !important;
}
.main {
	background: $inner_color !important;
	color: $text_color;
}
	
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}

@media only screen and (max-width: 640px) {
body {
  padding: 0 !important;
}
h1 {
  font-weight: 800 !important; margin: 20px 0 5px !important;
}
h2 {
  font-weight: 800 !important; margin: 20px 0 5px !important;
}
h3 {
  font-weight: 800 !important; margin: 20px 0 5px !important;
}
h4 {
  font-weight: 800 !important; margin: 20px 0 5px !important;
}
h1 {
  font-size: 22px !important;
}
h2 {
  font-size: 18px !important;
}
h3 {
  font-size: 16px !important;
}
.container {
  padding: 0 !important; width: 100% !important;
}
.content {
  padding: 0 !important;
}
.content-wrap {
  padding: 10px !important;
}
.invoice {
  width: 100% !important;
}
}
</style>
</head>
	
<body itemscope itemtype="http://schema.org/EmailMessage" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
	
<table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
<td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
<div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
<meta itemprop="name" content="Please Review" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
$message_content
</div>
</td>
<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
</tr></table></body>
</html>
	
EOT;
	
	  return $message;
	}

	/**
	 * Replace the structure elements of an email template
	 *
	 * @since 2.0.0
	 */
	public function replace_email_content( $unprocessed_message ) {

		$search = array('[section]', '[/section]', '[footer]', '[/footer]', '[/button]');
		$replace = array(
			'<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">',
			'</td></tr>',
			'</table></td></tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;"><table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top">',
			'</td></tr></table></div>',
			'</a></td></tr>'
		);
		$intemediate_message = str_replace( $search, $replace, $unprocessed_message );
		$processed_message = $this->replace_email_links( $intemediate_message );

  		return $processed_message;
	}

	/**
	 * Replace all of the button links used in the email template
	 *
	 * @since 2.0.0
	 */
	public function replace_email_links( $unprocessed_message ) {
	
		$pattern = "/\[button link=\'(.*?)\'\]/";
	
		preg_match_all( $pattern, $unprocessed_message, $matches );
	
		$replace = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><a href="INSERTED_LINK" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">';
		$message = preg_replace( $pattern, $replace, $unprocessed_message );
	
		if ( is_array( $matches[1] ) ) {
	
			foreach ( $matches[1] as $link ) {
	
				$pos = strpos( $message, "INSERTED_LINK" );
	
				if ($pos !== false) {
	
				    $intermediate_message = substr_replace( $message, $link, $pos, 13 );
				    $message = $intermediate_message;
				}
			}
		}
	
		return $message;
	}
}
} // endif;

