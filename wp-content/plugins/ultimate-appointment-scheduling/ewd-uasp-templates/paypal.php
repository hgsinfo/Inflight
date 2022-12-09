<div class='ewd-uasp-payment-form'>

	<form action='https://www.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>
    	<input type='hidden' name='item_name_1' value='<?php echo esc_attr( $this->get_selected_service_name() ); ?>' />
    	<input type='hidden' name='quantity_1' value='1' />
    	<input type='hidden' name='amount_1' value='<?php echo esc_attr( $this->get_selected_service_price() ); ?>' />
 		<input type='hidden' name='custom' value='<?php echo esc_attr( $this->get_selected_appointment_id() ); ?>' />

    	<input type='hidden' name='cmd' value='_cart' />
    	<input type='hidden' name='upload' value='1' />
    	<input type='hidden' name='business' value='<?php echo esc_attr( $this->get_option( 'paypal-email-address' ) ); ?>' />
 
    	<input type='hidden' name='currency_code' value='<?php echo esc_attr( $this->get_option( 'currency-code' ) ); ?>' />
    	<input type='hidden' name='return' value='<?php echo esc_attr( $this->get_option( 'thank-you-url' ) ); ?>' />
    	<input type='hidden' name='notify_url' value='<?php echo esc_attr( get_site_url() ); ?>' />
 
    	<input type='submit' class='submit-button' value='<?php echo esc_attr( $this->get_label( 'label-proceed-to-payment' ) ); ?>' />
	</form>
	
</div>