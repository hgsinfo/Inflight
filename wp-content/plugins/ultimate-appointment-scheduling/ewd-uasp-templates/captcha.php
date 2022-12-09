<div class='ewd-uasp-captcha-div'>
	
	<label for='captcha_image'></label>
	<img src='data:image/png;base64,<?php echo $this->create_captcha_image(); ?>' alt='captcha' />
	<input type='hidden' name='ewd_uasp_modified_captcha' value='<?php echo esc_attr( $this->captcha_form_code ); ?>' />

</div>

<div class='ewd-uasp-captcha-response'><label for='captcha_text'><?php echo esc_html( $this->get_label( 'label-image-number' ) ); ?>: </label>
	<input type='text' name='ewd_uasp_captcha' value='' />
</div>