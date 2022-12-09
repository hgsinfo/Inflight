<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduaspReviewAsk' ) ) {
/**
 * Class to handle plugin review ask
 *
 * @since 2.0.0
 */
class ewduaspReviewAsk {

	public function __construct() {
		
		add_action( 'admin_notices', array( $this, 'maybe_add_review_ask' ) );

		add_action( 'wp_ajax_ewd_uasp_hide_review_ask', array( $this, 'hide_review_ask' ) );
		add_action( 'wp_ajax_ewd_uasp_send_feedback', array( $this, 'send_feedback' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_review_ask_scripts' ) );
	}

	public function maybe_add_review_ask() { 
		
		$ask_review_time = get_option( 'ewd-uasp-review-ask-time' );

		$install_time = get_option( 'ewd-uasp-installation-time' );
		if ( ! $install_time ) { update_option( 'ewd-uasp-installation-time', time() ); $install_time = time(); }

		$ask_review_time = $ask_review_time != '' ? $ask_review_time : $install_time + 3600*24*4;
		
		if ( $ask_review_time < time() and $install_time != '' and $install_time < time() - 3600*24*4 ) {
			
			global $pagenow;

			if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) { ?>
	
				<div class='notice notice-info is-dismissible ewd-uasp-main-dashboard-review-ask' style='display:none'>
					<div class='ewd-uasp-review-ask-plugin-icon'></div>
					<div class='ewd-uasp-review-ask-text'>
						<p class='ewd-uasp-review-ask-starting-text'>Enjoying using the Ultimate Appointment Scheduling?</p>
						<p class='ewd-uasp-review-ask-feedback-text ewd-uasp-hidden'>Help us make the plugin better! Please take a minute to rate the plugin. Thanks!</p>
						<p class='ewd-uasp-review-ask-review-text ewd-uasp-hidden'>Please let us know what we could do to make the plugin better!<br /><span>(If you would like a response, please include your email address.)</span></p>
						<p class='ewd-uasp-review-ask-thank-you-text ewd-uasp-hidden'>Thank you for taking the time to help us!</p>
					</div>
					<div class='ewd-uasp-review-ask-actions'>
						<div class='ewd-uasp-review-ask-action ewd-uasp-review-ask-not-really ewd-uasp-review-ask-white'>Not Really</div>
						<div class='ewd-uasp-review-ask-action ewd-uasp-review-ask-yes ewd-uasp-review-ask-green'>Yes!</div>
						<div class='ewd-uasp-review-ask-action ewd-uasp-review-ask-no-thanks ewd-uasp-review-ask-white ewd-uasp-hidden'>No Thanks</div>
						<a href='https://wordpress.org/support/plugin/ultimate-appointment-scheduling/reviews/' target='_blank'>
							<div class='ewd-uasp-review-ask-action ewd-uasp-review-ask-review ewd-uasp-review-ask-green ewd-uasp-hidden'>OK, Sure</div>
						</a>
					</div>
					<div class='ewd-uasp-review-ask-feedback-form ewd-uasp-hidden'>
						<div class='ewd-uasp-review-ask-feedback-explanation'>
							<textarea></textarea>
							<br>
							<input type="email" name="feedback_email_address" placeholder="<?php _e('Email Address', 'ultimate-appointment-scheduling'); ?>">
						</div>
						<div class='ewd-uasp-review-ask-send-feedback ewd-uasp-review-ask-action ewd-uasp-review-ask-green'>Send Feedback</div>
					</div>
					<div class='ewd-uasp-clear'></div>
				</div>

			<?php
			}
		}
		else {
			wp_dequeue_script( 'ewd-uasp-review-ask-js' );
			wp_dequeue_style( 'ewd-uasp-review-ask-css' );
		}
	}

	public function enqueue_review_ask_scripts() {

		wp_enqueue_style( 'ewd-uasp-review-ask-css', EWD_UASP_PLUGIN_URL . '/assets/css/dashboard-review-ask.css' );
		wp_enqueue_script( 'ewd-uasp-review-ask-js', EWD_UASP_PLUGIN_URL . '/assets/js/dashboard-review-ask.js', array( 'jquery' ), EWD_UASP_VERSION, true  );

		wp_localize_script(
			'ewd-uasp-review-ask-js',
			'ewd_uasp_review_ask',
			array(
				'nonce' => wp_create_nonce( 'ewd-uasp-review-ask-js' )
			)
		);
	}

	public function hide_review_ask() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-review-ask-js', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$ask_review_time = sanitize_text_field( $_POST['ask_review_time'] );

		if ( get_option( 'ewd-uasp-review-ask-time' ) < time() + 3600*24 * $ask_review_time ) {
			update_option( 'ewd-uasp-review-ask-time', time() + 3600*24 * $ask_review_time );
		}

die();
	}

	public function send_feedback() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-uasp-review-ask-js', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			ewduaspHelper::admin_nopriv_ajax();
		}

		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
		$feedback = sanitize_text_field( $_POST['feedback'] );
		$feedback .= '<br /><br />Email Address: ';
		$feedback .= sanitize_email( $_POST['email_address'] );

		wp_mail('contact@etoilewebdesign.com', 'UASP Feedback - Dashboard Form', $feedback, $headers);

		die();
	} 
}

}