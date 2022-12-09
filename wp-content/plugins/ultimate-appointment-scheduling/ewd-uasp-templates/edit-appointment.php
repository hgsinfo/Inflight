<div class='ewd-uasp-edit-appointment-toggle <?php echo ( $this->display_appointment_editing_form() ? 'ewd-uasp-hidden' : '' ); ?>'>
	<?php _e( 'Edit Appointment', 'ultimate-appointment-scheduling' ); ?>
</div>

<div class='ewd-uasp-edit-appointment <?php echo ( ! $this->display_appointment_editing_form() ? 'ewd-uasp-hidden' : '' ); ?>'>

	<?php $this->maybe_print_edit_appointment_search_results(); ?>

	<?php $this->print_edit_appointment_search_form(); ?>
	
</div>

<div class="ewd-uasp-clear"></div>