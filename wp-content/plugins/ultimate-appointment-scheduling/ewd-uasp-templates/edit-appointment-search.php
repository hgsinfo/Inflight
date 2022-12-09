<form method='post' action='#'>

	<div class='ewd-uasp-edit-appointment-field'>

		<label for='client_name'>
			<?php echo esc_html( $this->get_label( 'label-name' ) ); ?>
		</label>

		<input name='ewd_uasp_client_name' type='text' value='<?php echo ( ! empty( $this->client_name ) ? esc_attr( $this->client_name ) : '' ); ?>' />
	
	</div>

	<div class='ewd-uasp-edit-appointment-field'>

		<label for='client_phone'>
			<?php echo esc_html( $this->get_label( 'label-phone' ) ); ?>
		</label>

		<input name='ewd_uasp_client_phone' type='text' value='<?php echo ( ! empty( $this->client_phone ) ? esc_attr( $this->client_phone ) : '' ); ?>' />
	
	</div>

	<div class='ewd-uasp-edit-appointment-field'>

		<label for='client_email'>
			<?php echo esc_html( $this->get_label( 'label-email' ) ); ?>
		</label>

		<input name='ewd_uasp_client_email' type='text' value='<?php echo ( ! empty( $this->client_email ) ? esc_attr( $this->client_email ) : '' ); ?>' />
	
	</div>

	<input type='submit' name='ewd_uasp_edit_appointment_search' value='<?php echo esc_attr( $this->get_label( 'label-find-appointment' ) ); ?>' />
	  
</form>