<div <?php echo ewd_format_classes( $this->classes ); ?> >

	<?php $this->maybe_print_update_message(); ?>

	<?php $this->maybe_print_appointment_edit_form(); ?>

	<?php $this->maybe_print_multistep_indicators(); ?>

	<form action='#' method='post' class='ewd-uasp-appointment-form'>

		<?php echo ( ! empty( $this->appointment ) ? '<input type="hidden" name="ewd_uasp_appointment_id" value="' . $this->appointment->id . '">' : '' ); ?>

		<?php $this->print_registration_form(); ?>
	
		<?php $this->print_service_form(); ?>
	
		<?php $this->print_appointment_selection(); ?>
	
		<?php $this->print_booking_submission(); ?>

		<?php $this->maybe_print_multistep_advance(); ?>

	</form>

</div>