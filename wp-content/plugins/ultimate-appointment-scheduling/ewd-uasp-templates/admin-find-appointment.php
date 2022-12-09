<div class='ewd-uasp-container ewd-uasp-find-appointment'>
		
	<div class='ewd-uasp-field'>
		
		<div class='ewd-uasp-input-label'>
			<?php _e( 'Date', 'ultimate-appointment-scheduling' ); ?>
		</div>

		<div class='ewd-uasp-input'>
			<input class='ewd-uasp-datepicker' id='ewd-uasp-date' type='text' name='ewd_uasp_date' value='<?php echo esc_attr( $this->date ); ?>' min='<?php echo esc_attr( $this->min_date ); ?>' />
		</div>

	</div>

	<div class='clear'></div>

	<div class='ewd-uasp-field'>

		<div class='ewd-uasp-input-label'></div>
	
		<div class='ewd-uasp-input'>
	
			<div class='ewd-uasp-button-container'>
				<button type='button' id='ewd-uasp-find-appointment' class='button button-primary'><?php _e( 'Find Appointment', 'ultimate-appointment-scheduling' ); ?></button>
			</div>
	
		</div>

	</div>

	<div class='clear'></div>

	<div id='ewd-uasp-appointment-times'></div>

</div>

<div class='clear'></div>
