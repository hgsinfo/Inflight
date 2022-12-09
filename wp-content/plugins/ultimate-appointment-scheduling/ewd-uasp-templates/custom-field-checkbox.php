<?php $options = explode( ',', $this->custom_field->options ); ?>

<?php foreach ( $options as $option ) { ?>

	<input type='checkbox' name='ewd-uasp-custom-field-<?php echo esc_attr( $this->custom_field->id ); ?>[]' <?php echo ( in_array( $option, $this->custom_field->field_value ) ? 'checked' : '' ); ?> value='<?php echo esc_attr( $option ); ?>' /> <?php echo esc_html( $option ); ?><br/>

<?php } ?>