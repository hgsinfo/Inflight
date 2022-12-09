<div class='ewd-uasp-container ewd-uasp-selected-time'>

	<div class='ewd-uasp-field'>

		<div class='ewd-uasp-input-label'>
			<?php _e( 'Date & Time', 'ultimate-appointment-scheduling' ); ?>
		</div>

		<div class='ewd-uasp-input'>
			<input type='text' name='ewd_uasp_current_datetime' value='<?php echo esc_attr( $this->appointment->start ); ?>' disabled />
		</div>

	</div>

</div>

<div class='clear'></div>
