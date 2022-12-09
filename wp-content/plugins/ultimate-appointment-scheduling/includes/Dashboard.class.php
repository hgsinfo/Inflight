<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduaspDashboard' ) ) {
/**
 * Class to handle plugin dashboard
 *
 * @since 2.0.0
 */
class ewduaspDashboard {

	public $message;
	public $status = true;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_dashboard_to_menu' ), 99 );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
	}

	public function add_dashboard_to_menu() {
		global $ewd_uasp_controller;
		global $submenu;

		add_submenu_page( 
			'ewd-uasp-appointments', 
			'Dashboard', 
			'Dashboard', 
			$ewd_uasp_controller->settings->get_setting( 'access-role' ), 
			'ewd-uasp-dashboard', 
			array($this, 'display_dashboard_screen') 
		);

		// Create a new sub-menu in the order that we want
		$new_submenu = array();
		$menu_item_count = 3;

		if ( ! isset( $submenu['ewd-uasp-appointments'] ) or  ! is_array($submenu['ewd-uasp-appointments']) ) { return; }
		
		foreach ( $submenu['ewd-uasp-appointments'] as $key => $sub_item ) {
			
			if ( $sub_item[0] == 'Dashboard' ) { $new_submenu[0] = $sub_item; }
			elseif ( $sub_item[0] == 'Appointments' ) { $new_submenu[1] = $sub_item; }
			elseif ( $sub_item[0] == 'Settings' ) { $new_submenu[ sizeof($submenu) ] = $sub_item; }
			else {
				
				$new_submenu[$menu_item_count] = $sub_item;
				$menu_item_count++;
			}
		}

		ksort($new_submenu);
		
		$submenu['ewd-uasp-appointments'] = $new_submenu;
	}

	// Enqueues the admin script so that our hacky sub-menu opening function can run
	public function enqueue_scripts() {
		global $admin_page_hooks;

		$currentScreen = get_current_screen();
		if ( $currentScreen->id == 'appointments_page_ewd-uasp-dashboard' ) {
			wp_enqueue_style( 'ewd-uasp-admin-css', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-admin.css', array(), EWD_UASP_VERSION );
			wp_enqueue_script( 'ewd-uasp-admin-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-admin.js', array( 'jquery' ), EWD_UASP_VERSION, true );
		}
	}

	public function display_dashboard_screen() { 
		global $ewd_uasp_controller;

		$permission = $ewd_uasp_controller->permissions->check_permission( 'styling' );

		$args = array();

		$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

		?>

		<div id="ewd-uasp-dashboard-content-area">

			<div id="ewd-uasp-dashboard-content-left">
		
				<?php if ( ! $permission or get_option("EWD_UASP_Trial_Happening") == "Yes" ) {
					$premium_info = '<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full">';
					$premium_info .= '<div class="ewd-uasp-dashboard-new-widget-box-top">';
					$premium_info .= sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to upgrade to premium.'), 'https://www.etoilewebdesign.com/premium-upgrade-instructions/' );
					$premium_info .= '</div>';
					$premium_info .= '</div>';

					$premium_info = apply_filters( 'ewd_dashboard_top', $premium_info, 'UASP', 'https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1' );

					echo $premium_info;
				} ?>
		
				<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full" id="ewd-uasp-dashboard-support-widget-box">
					<div class="ewd-uasp-dashboard-new-widget-box-top"><?php _e('Get Support', 'ultimate-appointment-scheduling'); ?><span id="ewd-uasp-dash-mobile-support-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-uasp-dash-mobile-support-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="ewd-uasp-dashboard-new-widget-box-bottom">
						<ul class="ewd-uasp-dashboard-support-widgets">
							<li>
								<a href="https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw/videos" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-youtube.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-support-widgets-text"><?php _e('YouTube Tutorials', 'ultimate-appointment-scheduling'); ?></div>
								</a>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/ultimate-appointment-scheduling/#faq" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-faqs.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-support-widgets-text"><?php _e('Plugin FAQs', 'ultimate-appointment-scheduling'); ?></div>
								</a>
							</li>
							<li>
								<a href="https://www.etoilewebdesign.com/support-center/?Plugin=UASP&Type=FAQs" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-documentation.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-support-widgets-text"><?php _e('Documentation', 'ultimate-appointment-scheduling'); ?></div>
								</a>
							</li>
							<li>
								<a href="https://www.etoilewebdesign.com/support-center/" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-forum.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-support-widgets-text"><?php _e('Get Support', 'ultimate-appointment-scheduling'); ?></div>
								</a>
							</li>
						</ul>
					</div>
				</div>
		
				<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full" id="ewd-uasp-dashboard-optional-table">
					<div class="ewd-uasp-dashboard-new-widget-box-top"><?php _e('Upcoming Appointments', 'ultimate-appointment-scheduling'); ?><span id="ewd-uasp-dash-optional-table-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-uasp-dash-optional-table-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="ewd-uasp-dashboard-new-widget-box-bottom">
						<table class='ewd-uasp-overview-table wp-list-table widefat fixed striped posts'>
							<thead>
								<tr>
									<th><?php _e("Name", 'ultimate-appointment-scheduling'); ?></th>
									<th><?php _e("Phone", 'ultimate-appointment-scheduling'); ?></th>
									<th><?php _e("Date/Time", 'ultimate-appointment-scheduling'); ?></th>
									<th><?php _e("Service", 'ultimate-appointment-scheduling'); ?></th>
									<th><?php _e("Provider", 'ultimate-appointment-scheduling'); ?></th>
									<th><?php _e("Location", 'ultimate-appointment-scheduling'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( empty( $appointments ) ) {echo "<tr><td colspan='3'>" . __("No appointments to display yet. Create an appointment for it to be displayed here.", 'ultimate-appointment-scheduling') . "</td></tr>";}
									else {
										foreach ( $appointments as $appointment ) { ?>
											<tr>
												<td><?php echo $appointment->client_name; ?></td>
												<td><?php echo $appointment->client_phone; ?></td>
												<td><?php echo $appointment->start; ?></td>
												<td><?php echo $appointment->service_name; ?></td>
												<td><?php echo $appointment->provider_name; ?></td>
												<td><?php echo $appointment->location_name; ?></td>
											</tr>
										<?php }
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
		
				<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full">
					<div class="ewd-uasp-dashboard-new-widget-box-top">What People Are Saying</div>
					<div class="ewd-uasp-dashboard-new-widget-box-bottom">
						<ul class="ewd-uasp-dashboard-testimonials">
							<?php $randomTestimonial = rand(0,2);
							if($randomTestimonial == 0){ ?>
								<li id="ewd-uasp-dashboard-testimonial-one">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-testimonial-title">"Works Great! Excellent Support!"</div>
									<div class="ewd-uasp-dashboard-testimonial-author">- @looksharp</div>
									<div class="ewd-uasp-dashboard-testimonial-text">It has most of the features you need and it works great! When it comes to support, Etoile Web Design provides excellent customer support... <a href="https://wordpress.org/support/topic/works-great-excellent-support-17/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 1){ ?>
								<li id="ewd-uasp-dashboard-testimonial-two">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-testimonial-title">"Great Appointment Plugin!"</div>
									<div class="ewd-uasp-dashboard-testimonial-author">- @lefo1959</div>
									<div class="ewd-uasp-dashboard-testimonial-text">This plugin not only does what I want, but it does it perfectly! <a href="https://wordpress.org/support/topic/great-appointment-plugin-2/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 2){ ?>
								<li id="ewd-uasp-dashboard-testimonial-three">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-uasp-dashboard-testimonial-title">"Fantastic plugin, fantastic support"</div>
									<div class="ewd-uasp-dashboard-testimonial-author">- @speechless</div>
									<div class="ewd-uasp-dashboard-testimonial-text">I love this plugin, it gives everything you could need for a scheduler and is very customisable.... <a href="https://wordpress.org/support/topic/fantastic-plugin-fantastic-support-6/" target="_blank">read more</a></div>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
		
				<?php if ( ! $permission or get_option("EWD_UASP_Trial_Happening") == "Yes" ) { ?>
					<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full" id="ewd-uasp-dashboard-guarantee-widget-box">
						<div class="ewd-uasp-dashboard-new-widget-box-top">
							<div class="ewd-uasp-dashboard-guarantee">
								<div class="ewd-uasp-dashboard-guarantee-title">14-Day 100% Money-Back Guarantee</div>
								<div class="ewd-uasp-dashboard-guarantee-text">If you're not 100% satisfied with the premium version of our plugin - no problem. You have 14 days to receive a FULL REFUND. We're certain you won't need it, though.</div>
							</div>
						</div>
					</div>
				<?php } ?>
		
			</div> <!-- left -->
		
			<div id="ewd-uasp-dashboard-content-right">
		
				<?php if ( ! $permission or get_option("EWD_UASP_Trial_Happening") == "Yes" ) { ?>
					<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full" id="ewd-uasp-dashboard-get-premium-widget-box">
						<div class="ewd-uasp-dashboard-new-widget-box-top">Get Premium</div>

						<?php if ( get_option( "EWD_UASP_Trial_Happening" ) == "Yes" ) { do_action( 'ewd_trial_happening', 'UASP' ); } ?>

						<div class="ewd-uasp-dashboard-new-widget-box-bottom">
							<div class="ewd-uasp-dashboard-get-premium-widget-features-title"<?php echo ( ( get_option("EWD_UASP_Trial_Happening") == "Yes" ) ? "style='padding-top: 20px;'" : ""); ?>>GET FULL ACCESS WITH OUR PREMIUM VERSION AND GET:</div>
							<ul class="ewd-uasp-dashboard-get-premium-widget-features">
								<li>Accept Payments for Bookings</li>
								<li>Send Email Appointment Reminders</li>
								<li>Admin &amp; Service Provider Notifications</li>
								<li>Styling &amp; Labelling Options</li>
								<li>+ More</li>
							</ul>
							<a href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" class="ewd-uasp-dashboard-get-premium-widget-button" target="_blank">UPGRADE NOW</a>
							
							<?php if ( ! get_option("EWD_UASP_Trial_Happening") ) { 
								$trial_info = sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to get a free 7-day trial of the premium plugin.'), 'https://www.etoilewebdesign.com/premium-upgrade-instructions/' );		

								echo apply_filters( 'ewd_trial_button', $trial_info, 'UASP' );
							} ?>
				</div>
					</div>
				<?php } ?>
		
				<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full">
					<div class="ewd-uasp-dashboard-new-widget-box-top">Other Plugins by Etoile</div>
					<div class="ewd-uasp-dashboard-new-widget-box-bottom">
						<ul class="ewd-uasp-dashboard-other-plugins">
							<li>
								<a href="https://wordpress.org/plugins/ultimate-faqs/" target="_blank"><img src="<?php echo plugins_url( '../assets/img/ewd-ufaq-icon.png', __FILE__ ); ?>"></a>
								<div class="ewd-uasp-dashboard-other-plugins-text">
									<div class="ewd-uasp-dashboard-other-plugins-title">Ultimate FAQs</div>
									<div class="ewd-uasp-dashboard-other-plugins-blurb">An easy-to-use FAQ plugin that lets you create, order and publicize FAQs, with many styles and options!</div>
								</div>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/order-tracking/" target="_blank"><img src="<?php echo plugins_url( '../assets/img/ewd-otp-icon.png', __FILE__ ); ?>"></a>
								<div class="ewd-uasp-dashboard-other-plugins-text">
									<div class="ewd-uasp-dashboard-other-plugins-title">Status Tracking</div>
									<div class="ewd-uasp-dashboard-other-plugins-blurb">Allows you to manage orders or projects quickly and easily by posting updates that can be viewed through the front-end of your site!</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
		
			</div> <!-- right -->	
		
		</div> <!-- us-dashboard-content-area -->
		
		<?php if ( ! $permission or get_option("EWD_UASP_Trial_Happening") == "Yes" ) { ?>
			<div id="ewd-uasp-dashboard-new-footer-one">
				<div class="ewd-uasp-dashboard-new-footer-one-inside">
					<div class="ewd-uasp-dashboard-new-footer-one-left">
						<div class="ewd-uasp-dashboard-new-footer-one-title">What's Included in Our Premium Version?</div>
						<ul class="ewd-uasp-dashboard-new-footer-one-benefits">
							<li>Accept Payments for Bookings</li>
							<li>WooCommerce Sync for Payments</li>
							<li>Add Custom Fields to Booking Form</li>
							<li>Require Login to Book</li>
							<li>Admin Appointment Notifications</li>
							<li>Service Provider Update Notifications</li>
							<li>Create Appointment Time Exceptions</li>
							<li>Send Email Appointment Reminders</li>
							<li>Two Booking Form Styles</li>
							<li>Add a Captcha to Booking Form</li>
							<li>Advanced Styling &amp; Labelling Options</li>
							<li>Email Support</li>
						</ul>
					</div>
					<div class="ewd-uasp-dashboard-new-footer-one-buttons">
						<a class="ewd-uasp-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" target="_blank">UPGRADE NOW</a>
					</div>
				</div>
			</div> <!-- us-dashboard-new-footer-one -->
		<?php } ?>	
		<div id="ewd-uasp-dashboard-new-footer-two">
			<div class="ewd-uasp-dashboard-new-footer-two-inside">
				<img src="<?php echo plugins_url( '../assets/img/ewd-logo-white.png', __FILE__ ); ?>" class="ewd-uasp-dashboard-new-footer-two-icon">
				<div class="ewd-uasp-dashboard-new-footer-two-blurb">
					At Etoile Web Design, we build reliable, easy-to-use WordPress plugins with a modern look. Rich in features, highly customizable and responsive, plugins by Etoile Web Design can be used as out-of-the-box solutions and can also be adapted to your specific requirements.
				</div>
				<ul class="ewd-uasp-dashboard-new-footer-two-menu">
					<li>SOCIAL</li>
					<li><a href="https://www.facebook.com/EtoileWebDesign/" target="_blank">Facebook</a></li>
					<li><a href="https://twitter.com/EtoileWebDesign" target="_blank">Twitter</a></li>
					<li><a href="https://www.etoilewebdesign.com/blog/" target="_blank">Blog</a></li>
				</ul>
				<ul class="ewd-uasp-dashboard-new-footer-two-menu">
					<li>SUPPORT</li>
					<li><a href="https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw/videos" target="_blank">YouTube Tutorials</a></li>
					<li><a href="https://www.etoilewebdesign.com/support-center/?Plugin=UASP&Type=FAQs" target="_blank">Documentation</a></li>
					<li><a href="https://www.etoilewebdesign.com/support-center/" target="_blank">Get Support</a></li>
					<li><a href="https://wordpress.org/plugins/ultimate-appointment-scheduling/#faq" target="_blank">FAQs</a></li>
				</ul>
			</div>
		</div> <!-- ewd-uasp-dashboard-new-footer-two -->
		
	<?php }

	public function display_notice() {
		if ( $this->status ) {
			echo "<div class='updated'><p>" . $this->message . "</p></div>";
		}
		else {
			echo "<div class='error'><p>" . $this->message . "</p></div>";
		}
	}
}
} // endif
