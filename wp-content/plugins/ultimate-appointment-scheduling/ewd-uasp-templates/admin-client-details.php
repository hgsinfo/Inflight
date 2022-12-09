<div class='ewd-uasp-container ewd-uasp-registration-form'>
  	
	<div class='ewd-uasp-field ewd-uasp-field-client-name'>

		<div class='ewd-uasp-input-label'>
			<?php _e( 'Client Name', 'ultimate-appointment-scheduling' ); ?>
		</div>

		<div class='ewd-uasp-input'>
			<input name='ewd_uasp_client_name' type='text' value='<?php echo ( ! empty( $this->client_name ) ? esc_attr( $this->client_name ) : '' ); ?>' />
		</div>
	
	</div>

	<div class='ewd-uasp-field ewd-uasp-field-client-phone'>

		<div class='ewd-uasp-input-label'>
			<?php _e( 'Client Phone', 'ultimate-appointment-scheduling' ); ?>
		</div>

		<div class='ewd-uasp-input'>
			<input name='ewd_uasp_client_phone' type='text' value='<?php echo ( ! empty( $this->client_phone ) ? esc_attr( $this->client_phone ) : '' ); ?>' />
		</div>
	
	</div>

	<div class='ewd-uasp-field ewd-uasp-field-client-email'>

		<div class='ewd-uasp-input-label'>
			<?php _e( 'Client Email', 'ultimate-appointment-scheduling' ); ?>
		</div>

		<div class='ewd-uasp-input'>
			<input name='ewd_uasp_client_email' type='text' value='<?php echo ( ! empty( $this->client_email ) ? esc_attr( $this->client_email ) : '' ); ?>' />
		</div>
	
	</div>

</div>

<div class='clear'></div>
