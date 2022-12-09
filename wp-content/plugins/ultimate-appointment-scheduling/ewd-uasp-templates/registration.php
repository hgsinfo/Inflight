<div class='ewd-uasp-container ewd-uasp-registration-form'>
  	
  	<div class='ewd-uasp-title ewd-uasp-registration-form-label'>
  		<?php echo esc_html( $this->get_label( 'label-sign-up-title' ) ); ?>
  	</div>

  	<div class='ewd-uasp-registration-form-content'>

		<div class='ewd-uasp-field ewd-uasp-field-client-name'>

			<label for='ewd_uasp_client_name'>
				<?php echo esc_html( $this->get_label( 'label-name' ) ); ?>
			</label>

			<input name='ewd_uasp_client_name' type='text' value='<?php echo ( ! empty( $this->client_name ) ? esc_attr( $this->client_name ) : '' ); ?>' <?php echo $this->get_field_required( 'name' ); ?> />
		
		</div>

		<div class='ewd-uasp-field ewd-uasp-field-client-phone'>

			<label for='ewd_uasp_client_phone'>
				<?php echo esc_html( $this->get_label( 'label-phone' ) ); ?>
			</label>

			<input name='ewd_uasp_client_phone' type='text' value='<?php echo ( ! empty( $this->client_phone ) ? esc_attr( $this->client_phone ) : '' ); ?>' <?php echo $this->get_field_required( 'phone' ); ?> />
		
		</div>

		<div class='ewd-uasp-field ewd-uasp-field-client-email'>

			<label for='ewd_uasp_client_email'>
				<?php echo esc_html( $this->get_label( 'label-email' ) ); ?>
			</label>

			<input name='ewd_uasp_client_email' type='text' value='<?php echo ( ! empty( $this->client_email ) ? esc_attr( $this->client_email ) : '' ); ?>' <?php echo $this->get_field_required( 'email' ); ?> />
		
		</div>

		<?php foreach ( $this->get_custom_fields() as $custom_field ) { ?>

			<div class='ewd-uasp-field'>

				<label for='<?php echo esc_attr( $custom_field->name ); ?>'>
					<?php echo esc_html( $custom_field->name ); ?>
				</label>

				<?php $this->print_custom_field( $custom_field ); ?>

			</div>

		<?php } ?>

	</div>

	<div class='clear'></div>

</div>