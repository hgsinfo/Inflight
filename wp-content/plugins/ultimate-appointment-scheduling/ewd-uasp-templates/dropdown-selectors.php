<div class='ewd-uasp-container ewd-uasp-find-appointment <?php echo ( $this->multi_step_booking ? 'ewd-uasp-hidden' : '' ); ?>'>

	<div class='ewd-uasp-title ewd-uasp-find-appointment-label'>
		<?php echo esc_html( $this->get_label( 'label-appointment-title' ) ); ?>
	</div>

	<div class='ewd-uasp-find-appointment-content'>
		
		<div class='ewd-uasp-field'>
			
			<div class='ewd-uasp-input-label'>
				<?php echo esc_html( $this->get_label( 'label-appointment-date' ) ); ?>
			</div>

			<div class='ewd-uasp-input'>
				<input class='ewd-uasp-datepicker' id='ewd-uasp-date' type='text' name='ewd_uasp_date' placeholder='<?php echo esc_html( $this->get_label( 'label-click-select-date' ) ); ?>' value='<?php echo esc_attr( $this->date ); ?>' min='<?php echo esc_attr( $this->min_date ); ?>' max='<?php echo esc_attr( $this->max_date ); ?>'/>
			</div>

		</div>

		<div class='clear'></div>

		<div class='ewd-uasp-field'>

			<div class='ewd-uasp-input-label'></div>
		
			<div class='ewd-uasp-input'>
  		
  				<div class='ewd-uasp-button-container'>
					<button type='button' id='ewd-uasp-find-appointment' class='button button-primary'><?php echo esc_html( $this->get_label( 'label-find-appointment' ) ); ?></button>
  				</div>
		
			</div>

		</div>

		<div class='clear'></div>

		<div id='ewd-uasp-appointment-times'></div>
  	
  	</div>

  	<div class='clear'></div>

</div>