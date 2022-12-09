<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduaspBlocks' ) ) {
/**
 * Class to handle plugin Gutenberg blocks
 *
 * @since 2.0.0
 */
class ewduaspBlocks {

	public function __construct() {

		add_action( 'init', array( $this, 'add_appointment_blocks' ) );
		
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
	}

	/**
	 * Add the Gutenberg block to the list of available blocks
	 * @since 2.0.0
	 */
	public function add_appointment_blocks() {

		if ( ! function_exists( 'render_block_core_block' ) ) { return; }

		$this->enqueue_assets();   

		$args = array(
			'attributes' => array(
				'display_type' => array(
					'type' => 'string',
				),
				'redirect_page' => array(
					'type' => 'string',
				),
			),
			'editor_script'   	=> 'ewd-uasp-blocks-js',
			'editor_style'  	=> 'ewd-uasp-blocks-css',
			'render_callback' 	=> 'ewd_uasp_appointment_booking_shortcode',
		);

		register_block_type( 'ultimate-appointment-scheduling/ewd-uasp-display-booking-block', $args );
	}

	/**
	 * Create a new category of blocks to hold our block
	 * @since 2.0.0
	 */
	public function add_block_category( $categories ) {
		
		$categories[] = array(
			'slug'  => 'ewd-uasp-blocks',
			'title' => __( 'Ultimate Appointment Scheduling', 'ultimate-appointment-scheduling' ),
		);

		return $categories;
	}

	/**
	 * Register the necessary JS and CSS to display the block in the editor
	 * @since 2.0.0
	 */
	public function enqueue_assets() {

		wp_register_script( 'ewd-uasp-blocks-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-blocks.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), EWD_UASP_VERSION );
		wp_register_style( 'ewd-uasp-blocks-css', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-blocks.css', array( 'wp-edit-blocks' ), EWD_UASP_VERSION );
	}
}

}