<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduaspDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 2.0.0
 */
class ewduaspDeactivationSurvey {

	public function __construct() {
		add_action( 'current_screen', array( $this, 'maybe_add_survey' ) );
	}

	public function maybe_add_survey() {
		if ( in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_scripts') );
			add_action( 'admin_footer', array( $this, 'add_deactivation_html') );
		}
	}

	public function enqueue_deactivation_scripts() {
		wp_enqueue_style( 'ewd-uasp-deactivation-css', EWD_UASP_PLUGIN_URL . '/assets/css/plugin-deactivation.css' );
		wp_enqueue_script( 'ewd-uasp-deactivation-js', EWD_UASP_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ) );

		wp_localize_script( 'ewd-uasp-deactivation-js', 'ewd_uasp_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {
		
		$install_time = get_option( 'ewd-uasp-installation-time' );

		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'ultimate-appointment-scheduling' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'ultimate-appointment-scheduling' ),
				'details' => esc_html__( 'Please share which plugin', 'ultimate-appointment-scheduling' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'ultimate-appointment-scheduling' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'ultimate-appointment-scheduling' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'ultimate-appointment-scheduling' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'ultimate-appointment-scheduling' ),
				'details' => esc_html__( 'Please share the reason', 'ultimate-appointment-scheduling' ),
			),
		);
		?>
		<div class="ewd-uasp-deactivate-survey-modal" id="ewd-uasp-deactivate-survey-ultimate-appointment-scheduling">
			<div class="ewd-uasp-deactivate-survey-wrap">
				<form class="ewd-uasp-deactivate-survey" method="post" data-installtime="<?php echo $install_time; ?>">
					<span class="ewd-uasp-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'ultimate-appointment-scheduling' ); ?></span>
					<span class="ewd-uasp-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Ultimate Appointment Scheduling:', 'ultimate-appointment-scheduling' ); ?></span>
					<div class="ewd-uasp-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="ewd-uasp-deactivate-survey-option">
								<label for="ewd-uasp-deactivate-survey-option-ultimate-appointment-scheduling-<?php echo $id; ?>" class="ewd-uasp-deactivate-survey-option-label">
									<input id="ewd-uasp-deactivate-survey-option-ultimate-appointment-scheduling-<?php echo $id; ?>" class="ewd-uasp-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" />
									<span class="ewd-uasp-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="ewd-uasp-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="ewd-uasp-deactivate-survey-footer">
						<button type="submit" class="ewd-uasp-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'ultimate-appointment-scheduling' ); ?></button>
						<a href="#" class="ewd-uasp-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'ultimate-appointment-scheduling' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}