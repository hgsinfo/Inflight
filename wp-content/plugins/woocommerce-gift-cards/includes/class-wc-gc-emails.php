<?php
/**
 * WC_GC_Emails class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Gift Cards
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gift Card emails manager.
 *
 * @class    WC_GC_Emails
 * @version  1.2.0
 */
class WC_GC_Emails {

	/**
	 * Email Templates collection.
	 *
	 * @since 1.2.0
	 *
	 * @var array
	 */
	private $templates;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );

		// Load Templates.
		$this->load_templates();

		// Parts.
		add_action( 'woocommerce_email_gift_card_html', array( $this, 'gift_card_email_html' ), 10, 6 );
	}

	/**
	 * Registers custom emails actions.
	 *
	 * @param  array  $actions
	 * @return array
	 */
	public function email_actions( $actions ) {
		$actions[] = 'woocommerce_gc_send_gift_card_to_customer';
		$actions[] = 'woocommerce_gc_force_send_gift_card_to_customer';
		$actions[] = 'woocommerce_gc_schedule_send_gift_card_to_customer';

		return $actions;
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @param  array  $emails
	 * @return array
	 */
	public function email_classes( $emails ) {
		$emails[ 'WC_GC_Email_Gift_Card_Received' ] = include 'emails/class-wc-gc-email-gift-card-received.php';

		return $emails;
	}

	/**
	 * Load email templates.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	protected function load_templates() {

		$this->templates = (array) apply_filters( 'woocommerce_gc_email_templates', array(
			'WC_GC_Email_Template_Default'
		) );

		foreach ( $this->templates as $template_class ) {
			$template = new $template_class;
			$this->templates[ $template->get_id() ] = $template;
		}
	}

	/**
	 * Get template object by template id.
	 *
	 * @since 1.2.0
	 *
	 * @param  string    $template_id
	 * @return false|WC_GC_Email_Template
	 */
	public function get_template( $template_id ) {

		if ( ! empty( $this->templates[ $template_id ] ) ) {
			return $this->templates[ $template_id ];
		}

		return false;
	}

	/**
	 * Get template object by product.
	 *
	 * @since 1.2.0
	 *
	 * @param  WC_Product  $product
	 * @return false|WC_GC_Email_Template
	 */
	public function get_template_by_product( $product ) {

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return false;
		}

		$template_id = $product->get_meta( '_gift_card_template', true );
		$template_id = ! empty( $template_id ) ? $template_id : 'default';

		return $this->get_template( $template_id );
	}

	/**
	 * Prints code in the email.
	 *
	 * @param  WC_GC_Gift_Card  $giftcard
	 * @param  string           $intro_content
	 * @param  WC_Order         Deprecated
	 * @param  bool             Deprecated
	 * @param  string           Deprecated
	 * @param  WC_Email         $email
	 * @return void
	 */
	public function gift_card_email_html( $giftcard, $intro_content, $deprecated_order, $deprecated_sent_to_admin = null, $deprecated_plain_text = null, $email = null ) {

		// Backwards compatible arguments.
		if ( is_a( $deprecated_order, 'WC_Email' ) && is_null( $email ) ) {
			$email = $deprecated_order;
		}

		// Default template params.
		$template_args = array(
			'giftcard'           => $email->get_gift_card(),
			'intro_content'      => $email->get_intro_content(),
			'email'              => $email
		);

		// Redeem button.
		$template_args[ 'show_redeem_button' ] = false;
		if ( $giftcard->is_redeemable() ) {
			$customer = get_user_by( 'email', $giftcard->get_recipient() );
			if ( $customer && is_a( $customer, 'WP_User' ) ) {
				$template_args[ 'show_redeem_button' ] = true;
			}
		}

		// Fetch the template.
		$template      = WC_GC()->emails->get_template( $giftcard->get_template_id() );
		$template_args = array_merge( $template_args, $template->get_args( $email ) );

		// Render giftcard part.
		ob_start();
		wc_get_template(
				'emails/html-gift-card-container.php',
				(array) apply_filters( 'woocommerce_gc_email_template_container_args', $template_args, $giftcard, $email ),
				false,
				WC_GC()->get_plugin_path() . '/templates/'
			);
		echo ob_get_clean();
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public function style_giftcards( $css, $email = null ) {
		_deprecated_function( __METHOD__ . '()', '1.2.0', 'WC_GC_Email_Template::add_stylesheets()' );
		return WC_GC_Email_Template::add_stylesheets( $css, $email );
	}
}
