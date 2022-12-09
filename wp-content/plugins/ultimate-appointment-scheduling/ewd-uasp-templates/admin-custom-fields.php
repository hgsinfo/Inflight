<div class='ewd-uasp-container ewd-uasp-custom-fields'>
  	
	<?php foreach ( $this->get_custom_fields() as $custom_field ) { ?>

		<div class='ewd-uasp-field'>

			<div class='ewd-uasp-input-label'>
				<?php echo esc_html( $custom_field->name ); ?>
			</div>

			<div class='ewd-uasp-input'>
				<?php $this->print_custom_field( $custom_field ); ?>
			</div>

		</div>

	<?php } ?>

</div>

<div class='clear'></div>
