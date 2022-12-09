<div>
	
	<?php $this->maybe_print_update_message(); ?>

	<form action='#' method='post' class='ewd-uasp-appointment-form' id="ewd-uasp-new-edit-appointment-screen">

		<?php echo ( ! empty( $this->appointment ) ? '<input type="hidden" name="ewd_uasp_appointment_id" value="' . $this->appointment->id . '">' : '' ); ?>
	
		<div class='ewd-uasp-admin-add-edit-appointment-content'>

			<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full ewd-uasp-admin-edit-product-left-full-widget-box" id="ewd-uasp-admin-edit-appointment-details-widget-box">

				<div class="ewd-uasp-dashboard-new-widget-box-top"><?php _e('Appointment Details', 'ultimate-appointment-scheduling'); ?></div>
				
				<div class="ewd-uasp-dashboard-new-widget-box-bottom">

					<?php $this->maybe_print_appointment_selected_time(); ?>

					<?php $this->print_admin_service_select(); ?>
			
					<?php $this->print_admin_appointment_selection(); ?>

				</div>

			</div>

			<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full ewd-uasp-admin-edit-product-left-full-widget-box" id="ewd-uasp-admin-edit-customer-details-widget-box">

				<div class="ewd-uasp-dashboard-new-widget-box-top"><?php _e('Client Details', 'ultimate-appointment-scheduling'); ?></div>
				
				<div class="ewd-uasp-dashboard-new-widget-box-bottom">

					<?php $this->print_admin_client_details(); ?>

				</div>

			</div>

		</div>

		<div class='ewd-uasp-admin-add-edit-appointment-sidebar'>
		
			<?php $this->print_admin_submit(); ?>

			<div class="ewd-uasp-dashboard-new-widget-box ewd-widget-box-full" id="ewd-uasp-admin-edit-appointment-custom-fields-widget-box">
				
			<div class="ewd-uasp-dashboard-new-widget-box-top"><?php _e('Custom Fields', 'ultimate-appointment-scheduling'); ?></div>
				
				<div class="ewd-uasp-dashboard-new-widget-box-bottom">

					<?php $this->print_admin_custom_fields(); ?>

				</div>

			</div>

		</div>

	</form>

</div>