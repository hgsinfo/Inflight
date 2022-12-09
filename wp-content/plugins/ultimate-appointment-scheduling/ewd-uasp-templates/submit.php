<div class='ewd-uasp-book-button-container <?php echo ( $this->multi_step_booking ? 'ewd-uasp-hidden' : '' ); ?>'>

	<?php $this->maybe_print_captcha(); ?>

	<?php if ( $this->paypal_prepayment != 'none' ) { ?>
		<input type='submit' class='ewd-uasp-book-button' name='ewd_uasp_payment_submit' value='<?php echo esc_html( $this->get_label( 'label-pay-in-advance' ) ); ?>' />
	<?php } ?>

	<?php if ( $this->paypal_prepayment != 'required' ) { ?>
		<input type='submit' class='ewd-uasp-book-button' name='ewd_uasp_submit_booking' value='<?php echo esc_html( $this->get_label( 'label-book-appointment' ) ); ?>' />
	<?php } ?>

</div>