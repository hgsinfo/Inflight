<?php
/**
 * Class to handle all custom post type definitions for Ultimate Appointment Scheduling
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduaspCustomPostTypes' ) ) {
class ewduaspCustomPostTypes {

	// An array holding lowercase and uppercase versions of the days of the week
	public $days = array(
		'monday' 	=> 'Monday',
		'tuesday' 	=> 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday' 	=> 'Thursday',
		'friday' 	=> 'Friday',
		'saturday' 	=> 'Saturday',
		'sunday' 	=> 'Sunday'
	);

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'admin_init', 		array( $this, 'create_nonce' ) );
		add_action( 'init', 			array( $this, 'load_cpts' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', 		array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', 			array( $this, 'save_meta' ) );

		// Add columns for the various CPTs
		add_filter( 'manage_uasp-location_posts_columns', 			array( $this, 'register_location_table_columns' ) );
		add_action( 'manage_uasp-location_posts_custom_column', 	array( $this, 'display_location_columns_content' ), 10, 2 );
		add_filter( 'manage_uasp-service_posts_columns', 			array( $this, 'register_service_table_columns' ) );
		add_action( 'manage_uasp-service_posts_custom_column', 		array( $this, 'display_service_columns_content' ), 10, 2 );
		add_filter( 'manage_uasp-provider_posts_columns', 			array( $this, 'register_provider_table_columns' ) );
		add_action( 'manage_uasp-provider_posts_custom_column', 	array( $this, 'display_provider_columns_content' ), 10, 2 );
		add_filter( 'manage_uasp-exception_posts_columns', 			array( $this, 'register_exception_table_columns' ) );
		add_action( 'manage_uasp-exception_posts_custom_column', 	array( $this, 'display_exception_columns_content' ), 10, 2 );

		// Remove any exceptions more than ~1 day old
		add_action( 'admin_init', 		array( $this, 'remove_expired_exceptions' ) );		
	}

	/**
	 * Initialize custom post types
	 * @since 2.0.0
	 */
	public function load_cpts() {
		global $ewd_uasp_controller;

		// Define the location custom post type
		$args = array(
			'labels' 		=> array(
				'name' 					=> __( 'Locations',           			'ultimate-appointment-scheduling' ),
				'singular_name' 		=> __( 'Location',                   	'ultimate-appointment-scheduling' ),
				'menu_name'         	=> __( 'Locations',          			'ultimate-appointment-scheduling' ),
				'name_admin_bar'    	=> __( 'Locations',                  	'ultimate-appointment-scheduling' ),
				'add_new'           	=> __( 'Add New',                 		'ultimate-appointment-scheduling' ),
				'add_new_item' 			=> __( 'Add New Location',           	'ultimate-appointment-scheduling' ),
				'edit_item'         	=> __( 'Edit Location',               	'ultimate-appointment-scheduling' ),
				'new_item'          	=> __( 'New Location',                	'ultimate-appointment-scheduling' ),
				'view_item'         	=> __( 'View Location',               	'ultimate-appointment-scheduling' ),
				'search_items'      	=> __( 'Search Locations',           	'ultimate-appointment-scheduling' ),
				'not_found'         	=> __( 'No Locations found',          	'ultimate-appointment-scheduling' ),
				'not_found_in_trash'	=> __( 'No Locations found in trash', 	'ultimate-appointment-scheduling' ),
				'all_items'         	=> __( 'Locations',              		'ultimate-appointment-scheduling' ),
			),
			'public' 		=> false,
			'has_archive' 	=> false,
			'query_var' 	=> false,
			'show_ui'		=> true,
			'rewrite'		=> array( 
				'slug' 			=> 'locations'
			),
			'supports' 		=> array( 
				'title' 
			),
			'show_in_rest' 	=> true,
			'show_in_menu'	=> 'ewd-uasp-appointments'
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_uasp_locations_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_uasp_locations_pre_register' );

		// Register the post type
		register_post_type( EWD_UASP_LOCATION_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_uasp_locations_post_register' );

		// Define the service custom post type
		$args = array(
			'labels' 		=> array(
				'name' 					=> __( 'Services',           			'ultimate-appointment-scheduling' ),
				'singular_name' 		=> __( 'Service',                   	'ultimate-appointment-scheduling' ),
				'menu_name'         	=> __( 'Services',          			'ultimate-appointment-scheduling' ),
				'name_admin_bar'    	=> __( 'Services',                  	'ultimate-appointment-scheduling' ),
				'add_new'           	=> __( 'Add New',                 		'ultimate-appointment-scheduling' ),
				'add_new_item' 			=> __( 'Add New Service',           	'ultimate-appointment-scheduling' ),
				'edit_item'         	=> __( 'Edit Service',               	'ultimate-appointment-scheduling' ),
				'new_item'          	=> __( 'New Service',                	'ultimate-appointment-scheduling' ),
				'view_item'         	=> __( 'View Service',               	'ultimate-appointment-scheduling' ),
				'search_items'      	=> __( 'Search Services',           	'ultimate-appointment-scheduling' ),
				'not_found'         	=> __( 'No Services found',          	'ultimate-appointment-scheduling' ),
				'not_found_in_trash'	=> __( 'No Services found in trash', 	'ultimate-appointment-scheduling' ),
				'all_items'         	=> __( 'Services',              		'ultimate-appointment-scheduling' ),
			),
			'public' 		=> false,
			'has_archive' 	=> false,
			'query_var' 	=> false,
			'show_ui'		=> true,
			'rewrite'		=> array( 
				'slug' 			=> 'services'
			),
			'supports' 		=> array( 
				'title' 
			),
			'show_in_rest' 	=> true,
			'show_in_menu'	=> 'ewd-uasp-appointments'
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_uasp_services_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_uasp_services_pre_register' );

		// Register the post type
		register_post_type( EWD_UASP_SERVICE_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_uasp_services_post_register' );

		// Define the service provider custom post type
		$args = array(
			'labels' 		=> array(
				'name' 					=> __( 'Service Providers',           			'ultimate-appointment-scheduling' ),
				'singular_name' 		=> __( 'Service Provider',                   	'ultimate-appointment-scheduling' ),
				'menu_name'         	=> __( 'Service Providers',          			'ultimate-appointment-scheduling' ),
				'name_admin_bar'    	=> __( 'Service Providers',                  	'ultimate-appointment-scheduling' ),
				'add_new'           	=> __( 'Add New',                 				'ultimate-appointment-scheduling' ),
				'add_new_item' 			=> __( 'Add New Service Provider',           	'ultimate-appointment-scheduling' ),
				'edit_item'         	=> __( 'Edit Service Provider',               	'ultimate-appointment-scheduling' ),
				'new_item'          	=> __( 'New Service Provider',                	'ultimate-appointment-scheduling' ),
				'view_item'         	=> __( 'View Service Provider',               	'ultimate-appointment-scheduling' ),
				'search_items'      	=> __( 'Search Service Providers',           	'ultimate-appointment-scheduling' ),
				'not_found'         	=> __( 'No Service Providers found',          	'ultimate-appointment-scheduling' ),
				'not_found_in_trash'	=> __( 'No Service Providers found in trash', 	'ultimate-appointment-scheduling' ),
				'all_items'         	=> __( 'Service Providers',              		'ultimate-appointment-scheduling' ),
			),
			'public' 		=> false,
			'has_archive' 	=> false,
			'query_var' 	=> false,
			'show_ui'		=> true,
			'rewrite'		=> array( 
				'slug' 			=> 'providers'
			),
			'supports' 		=> array( 
				'title' 
			),
			'show_in_rest' 	=> true,
			'show_in_menu'	=> 'ewd-uasp-appointments'
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_uasp_service_providers_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_uasp_service_providers_pre_register' );

		// Register the post type
		register_post_type( EWD_UASP_PROVIDER_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_uasp_service_providers_post_register' );

		// Define the exception custom post type
		$args = array(
			'labels' 		=> array(
				'name' 					=> __( 'Exceptions',           			'ultimate-appointment-scheduling' ),
				'singular_name' 		=> __( 'Exception',                   	'ultimate-appointment-scheduling' ),
				'menu_name'         	=> __( 'Exceptions',          			'ultimate-appointment-scheduling' ),
				'name_admin_bar'    	=> __( 'Exceptions',                  	'ultimate-appointment-scheduling' ),
				'add_new'           	=> __( 'Add New',                 		'ultimate-appointment-scheduling' ),
				'add_new_item' 			=> __( 'Add New Exception',           	'ultimate-appointment-scheduling' ),
				'edit_item'         	=> __( 'Edit Exception',               	'ultimate-appointment-scheduling' ),
				'new_item'          	=> __( 'New Exception',                	'ultimate-appointment-scheduling' ),
				'view_item'         	=> __( 'View Exception',               	'ultimate-appointment-scheduling' ),
				'search_items'      	=> __( 'Search Exceptions',           	'ultimate-appointment-scheduling' ),
				'not_found'         	=> __( 'No Exceptions found',          	'ultimate-appointment-scheduling' ),
				'not_found_in_trash'	=> __( 'No Exceptions found in trash', 	'ultimate-appointment-scheduling' ),
				'all_items'         	=> __( 'Exceptions',              		'ultimate-appointment-scheduling' ),
			),
			'public' 		=> false,
			'has_archive' 	=> false,
			'query_var' 	=> false,
			'show_ui'		=> true,
			'rewrite'		=> array( 
				'slug' 			=> 'exceptions'
			),
			'supports' 		=> array( 
				'title',
			),
			'show_in_rest' 	=> true,
			'show_in_menu'	=> 'ewd-uasp-appointments'
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_uasp_exceptions_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_uasp_exceptions_pre_register' );

		// Register the post type
		register_post_type( EWD_UASP_EXCEPTION_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_uasp_exceptions_post_register' );
	}

	/**
	 * Generate a nonce for secure saving of metadata
	 * @since 2.0.0
	 */
	public function create_nonce() {

		$this->nonce = wp_create_nonce( basename( __FILE__ ) );
	}

	/**
	 * Add in new columns for the various CPTs
	 * @since 2.0.0
	 */
	public function add_meta_boxes() {

		$meta_boxes = array(

			// Add in the Exception meta information
			'exception_meta' => array (
				'id'		=>	'uasp-exception',
				'title'		=> esc_html__( 'Exception Details', 'ultimate-appointment-scheduling' ),
				'callback'	=> array( $this, 'show_exception_meta' ),
				'post_type'	=> EWD_UASP_EXCEPTION_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in the Location meta information
			'location_meta' => array (
				'id'		=>	'uasp-location',
				'title'		=> esc_html__( 'Location Details', 'ultimate-appointment-scheduling' ),
				'callback'	=> array( $this, 'show_location_meta' ),
				'post_type'	=> EWD_UASP_LOCATION_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in the Service meta information
			'service_meta' => array (
				'id'		=>	'uasp-service',
				'title'		=> esc_html__( 'Service Details', 'ultimate-appointment-scheduling' ),
				'callback'	=> array( $this, 'show_service_meta' ),
				'post_type'	=> EWD_UASP_SERVICE_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in the Provider meta information
			'provider_meta' => array (
				'id'		=>	'uasp-provider',
				'title'		=> esc_html__( 'Service Provider Details', 'ultimate-appointment-scheduling' ),
				'callback'	=> array( $this, 'show_provider_meta' ),
				'post_type'	=> EWD_UASP_PROVIDER_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in a link to the documentation for the plugin
			'ewd_urp_meta_need_help' => array (
				'id'		=>	'ewd_uasp_meta_need_help',
				'title'		=> esc_html__( 'Need Help?', 'ultimate-appointment-scheduling' ),
				'callback'	=> array( $this, 'show_need_help_meta' ),
				'post_type'	=> array( EWD_UASP_LOCATION_POST_TYPE, EWD_UASP_SERVICE_POST_TYPE, EWD_UASP_PROVIDER_POST_TYPE ),
				'context'	=> 'side',
				'priority'	=> 'high'
			),
		);

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'ewd_uasp_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}
	}

	/**
	 * Adds in the meta fields for exception post type
	 * @since 2.0.0
	 */
	public function show_exception_meta( $post ) {

		$start = get_post_meta( $post->ID, 'start', true );
		$end = get_post_meta( $post->ID, 'end', true );

		$args = array(
			'post_type'		=> EWD_UASP_LOCATION_POST_TYPE,
			'numberposts'	=> -1
		);

		$locations = get_posts( $args );

		$args = array(
			'post_type'		=> EWD_UASP_PROVIDER_POST_TYPE,
			'numberposts'	=> -1
		);

		$providers = get_posts( $args );  

		?>
	
		<input type="hidden" name="ewd_uasp_nonce" value="<?php echo $this->nonce; ?>">

		<div class='ewd-uasp-meta-field'>

			<label for='Exception Location'>
				<?php _e( 'Exception Location:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<select name='ewd_uasp_exception_location'>

				<option value=''></option>

				<?php foreach( $locations as $location ) { ?>

					<option value='<?php echo $location->ID; ?>' <?php echo ( get_post_meta( $post->ID, 'location_id', true ) == $location->ID ? 'selected' : '' ); ?> ><?php echo esc_html( $location->post_title ); ?></option>
				<?php } ?>

			</select>

			<p>
				<?php _e( 'The location this exception applies to. Leave this blank if you\'re adding an exception for a specific service provider instead.', 'ultimate-appointment-scheduling' ); ?>
			</p>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Exception Provider'>
				<?php _e( 'Exception Service Provider:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<select name='ewd_uasp_exception_provider'>

				<option value=''></option>

				<?php foreach( $providers as $provider ) { ?>

					<option value='<?php echo $provider->ID; ?>' <?php echo ( get_post_meta( $post->ID, 'provider_id', true ) == $provider->ID ? 'selected' : '' ); ?> ><?php echo esc_html( $provider->post_title ); ?></option>
				<?php } ?>

			</select>

			<p>
				<?php _e( 'The service provider this exception applies to. Leave this blank if you\'re adding an exception for a specific location instead.', 'ultimate-appointment-scheduling' ); ?>
			</p>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Exception Reason'>
				<?php _e( 'Exception Reason:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<textarea id='ewd-uasp-exception-description' name='exception_description'>
				<?php echo esc_html( strip_tags( get_the_content( null, false, $post->ID ) ) ); ?>
			</textarea>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Exception Start'>
				<?php _e( 'Exception Start:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input class='ewd-uasp-datepicker' type='date' name='ewd_uasp_exception_start_date' value='<?php echo esc_attr( substr( $start, 0, strpos( $start, ' ' ) ) ); ?>' />

			<select name='ewd_uasp_exception_start_time'>

				<?php echo $this->return_select_hours( substr( get_post_meta( $post->ID, 'start', true ), strpos( get_post_meta( $post->ID, 'start', true ), ' ' ) + 1 ) ); ?>

			</select>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Exception End'>
				<?php _e( 'Exception End:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input class='ewd-uasp-datepicker' type='date' name='ewd_uasp_exception_end_date' value='<?php echo esc_attr( substr( $end, 0, strpos( $end, ' ' ) ) ); ?>' />

			<select name='ewd_uasp_exception_end_time'>

				<?php echo $this->return_select_hours( substr( get_post_meta( $post->ID, 'end', true ), strpos( get_post_meta( $post->ID, 'end', true ), ' ' ) + 1 ) ) ?>

			</select>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Exception Status'>
				<?php _e( 'Exception Status:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<select name='ewd_uasp_exception_status'>
				<option value='open' <?php echo ( get_post_meta( $post->ID, 'status', true ) == 'open' ? 'selected' : '' ); ?>><?php _e( 'Open', 'ultimate-appointment-scheduling' ); ?></option>
				<option value='closed' <?php echo ( get_post_meta( $post->ID, 'status', true ) == 'closed' ? 'selected' : '' ); ?>><?php _e( 'Closed', 'ultimate-appointment-scheduling' ); ?></option>
			</select>

		</div>

		<?php 
	} 

	/**
	 * Adds in the meta fields for location post type
	 * @since 2.0.0
	 */
	public function show_location_meta( $post ) { ?>
	
		<input type="hidden" name="ewd_uasp_nonce" value="<?php echo $this->nonce; ?>">

		<div class='ewd-uasp-meta-field'>

			<label for='Location Description'>
				<?php _e( 'Location Description:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<textarea id='ewd-uasp-location-description' name='location_description'>
				<?php echo esc_html( strip_tags( get_the_content( null, false, $post->ID ) ) ); ?>
			</textarea>

		</div>

		<?php foreach ( $this->days as $lowercase_day => $day ) { ?>

			<div class='ewd-uasp-meta-field'>

				<label for='<?php echo $day; ?>'>
					<?php echo $day; ?>
				</label>

				<select name='<?php echo $lowercase_day; ?>_open'>
					<?php echo $this->return_select_hours( get_post_meta( $post->ID, $day . ' Open', true ) ); ?>
				</select>

				<select name='<?php echo $lowercase_day; ?>_close'>
					<?php echo $this->return_select_hours( get_post_meta( $post->ID, $day . ' Close', true ) ); ?>
				</select>

				<input type='text' name='<?php echo $lowercase_day; ?>_note' value='<?php echo esc_attr( get_post_meta( $post->ID, $day . ' Note', true ) ); ?>' />

			</div>

		<?php } ?>

		<?php 
	} 

	/**
	 * Adds in the meta fields for service post type
	 * @since 2.0.0
	 */
	public function show_service_meta( $post ) { ?>
	
		<input type="hidden" name="ewd_uasp_nonce" value="<?php echo $this->nonce; ?>">

		<div class='ewd-uasp-meta-field'>

			<label for='Service Description'>
				<?php _e( 'Service Description:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<textarea id='ewd-uasp-service-description' name='service_description'>
				<?php echo esc_html( strip_tags( get_the_content( null, false, $post->ID ) ) ); ?>
			</textarea>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Service Capacity'>
				<?php _e( 'Service Capacity:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input type='text' id='ewd-uasp-service-capacity' name='service_capacity' value='<?php echo esc_attr( get_post_meta( $post->ID, 'Service Capacity', true ) ); ?>' />

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Service Duration'>
				<?php _e( 'Service Duration:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input type='text' id='ewd-uasp-service-duration' name='service_duration' value='<?php echo esc_attr( get_post_meta( $post->ID, 'Service Duration', true ) ); ?>' />

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Service Price'>
				<?php _e( 'Service Price:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input type='text' id='ewd-uasp-service-price' name='service_price' value='<?php echo esc_attr( get_post_meta( $post->ID, 'Service Price', true ) ); ?>' />

		</div>

	<?php } 

	/**
	 * Adds in the meta fields for provider post type
	 * @since 2.0.0
	 */
	public function show_provider_meta( $post ) { 

			$args = array(
				'posts_per_page' 	=> -1, 
				'post_type' 		=> EWD_UASP_SERVICE_POST_TYPE
			);

			$services = get_posts( $args );

			$args = array(
				'posts_per_page' 	=> -1, 
				'post_type' 		=> EWD_UASP_LOCATION_POST_TYPE
			);

			$locations = get_posts( $args );

			$provider_services = explode( ',', get_post_meta( $post->ID, 'Service Provider Services', true ) );
		?>
	
		<input type="hidden" name="ewd_uasp_nonce" value="<?php echo $this->nonce; ?>">

		<div class='ewd-uasp-meta-field'>

			<label for='Service Provider Description'>
				<?php _e( 'Service Provider Description:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<textarea id='ewd-uasp-provider-description' name='provider_description'>
				<?php echo esc_html( strip_tags( get_the_content( null, false, $post->ID ) ) ); ?>
			</textarea>

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Service Provider Email'>
				<?php _e( 'Service Provider Email:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<input type='text' id='ewd-uasp-provider-email' name='provider_email' value='<?php echo esc_attr( get_post_meta( $post->ID, 'Service Provider Email', true ) ); ?>' />

		</div>

		<div class='ewd-uasp-meta-field'>

			<label for='Services Offered'>
				<?php _e( 'Services Offered:', 'ultimate-appointment-scheduling' ); ?>
			</label>

			<?php foreach ( $services as $service ) { ?>

				<input type='checkbox' name='provider_services[]' value='<?php echo $service->ID; ?>' <?php echo ( in_array( $service->ID, $provider_services ) ? 'checked' : '' ); ?> /> <?php echo esc_html( $service->post_title ); ?>

			<?php } ?>

		</div>

		<?php foreach ( $this->days as $lowercase_day => $day ) { ?>

			<div class='ewd-uasp-meta-field'>

				<label for='<?php echo $day; ?>'>
					<?php echo $day; ?>
				</label>

				<select name='<?php echo $lowercase_day; ?>_start'>
					<?php echo $this->return_select_hours( get_post_meta( $post->ID, $day . ' Start', true ) ); ?>
				</select>

				<select name='<?php echo $lowercase_day; ?>_finish'>
					<?php echo $this->return_select_hours( get_post_meta( $post->ID, $day . ' Finish', true ) ); ?>
				</select>

				<select name='<?php echo $lowercase_day; ?>_location'>

					<?php foreach ( $locations as $location ) { ?>

						<option value='<?php echo $location->ID; ?>' <?php echo ( get_post_meta( $post->ID, $day . ' Location', true ) == $location->ID ? 'selected' : '' ); ?>>
							<?php echo esc_html( $location->post_title ); ?>
						</option>

					<?php } ?>

				</select>

			</div>

		<?php } ?>

		<?php 
	} 

	/**
	 * Add in a link to the plugin documentation
	 * @since 2.0.0
	 */
	public function show_need_help_meta() { ?>
    
    	<div class='ewd-uasp-need-help-box'>
    		<div class='ewd-uasp-need-help-text'>Visit our Support Center for documentation and tutorials</div>
    	    <a class='ewd-uasp-need-help-button' href='https://www.etoilewebdesign.com/support-center/?Plugin=UASP' target='_blank'>GET SUPPORT</a>
    	</div>

	<?php }

	/**
	 * Save the metabox data for each review
	 * @since 2.0.0
	 */
	public function save_meta( $post_id ) {
		global $ewd_uasp_controller;

		// Verify nonce
		if ( ! isset( $_POST['ewd_uasp_nonce'] ) || ! wp_verify_nonce( $_POST['ewd_uasp_nonce'], basename( __FILE__ ) ) ) {

			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return $post_id;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		remove_action( 'save_post', array( $this, 'save_meta' ) );

		if ( get_post_type( $post_id ) == EWD_UASP_EXCEPTION_POST_TYPE ) { $this->save_exception_meta( $post_id ); }
		elseif ( get_post_type( $post_id ) == EWD_UASP_LOCATION_POST_TYPE ) { $this->save_location_meta( $post_id ); }
		elseif ( get_post_type( $post_id ) == EWD_UASP_SERVICE_POST_TYPE ) { $this->save_service_meta( $post_id ); }
		elseif ( get_post_type( $post_id ) == EWD_UASP_PROVIDER_POST_TYPE ) { $this->save_provider_meta( $post_id ); }

		add_action( 'save_post', array( $this, 'save_meta' ) );
	}

	/**
	 * Saves the various meta fields for the exception custom post type
	 * @since 2.0.0
	 */
	public function save_exception_meta( $post_id ) {

		$args = array(
			'ID'			=> $post_id,
			'post_content' 	=> sanitize_textarea_field( $_POST['exception_description'] ),
		);

		wp_update_post( $args );

		update_post_meta( $post_id, 'location_id', sanitize_text_field( $_POST[ 'ewd_uasp_exception_location' ] ) );
		update_post_meta( $post_id, 'provider_id', sanitize_text_field( $_POST[ 'ewd_uasp_exception_provider' ] ) );

		update_post_meta( $post_id, 'start', sanitize_text_field( $_POST[ 'ewd_uasp_exception_start_date' ] ) . ' ' . sanitize_text_field( $_POST[ 'ewd_uasp_exception_start_time' ] ) );
		update_post_meta( $post_id, 'end', sanitize_text_field( $_POST[ 'ewd_uasp_exception_end_date' ] ) . ' ' . sanitize_text_field( $_POST[ 'ewd_uasp_exception_end_time' ] ) );
		update_post_meta( $post_id, 'status', sanitize_text_field( $_POST[ 'ewd_uasp_exception_status' ] ) );
	}

	/**
	 * Saves the various meta fields for the location custom post type
	 * @since 2.0.0
	 */
	public function save_location_meta( $post_id ) {

		$args = array(
			'ID'			=> $post_id,
			'post_content' 	=> sanitize_textarea_field( $_POST['location_description'] ),
		);

		wp_update_post( $args );

		foreach ( $this->days as $lowercase_day => $day ) {

			update_post_meta( $post_id, $day . ' Open', sanitize_text_field( $_POST[ $lowercase_day . '_open' ] ) );
			update_post_meta( $post_id, $day . ' Close', sanitize_text_field( $_POST[ $lowercase_day . '_close' ] ) );
			update_post_meta( $post_id, $day . ' Note', sanitize_text_field( $_POST[ $lowercase_day . '_note' ] ) );
		}
	}

	/**
	 * Saves the various meta fields for the service custom post type
	 * @since 2.0.0
	 */
	public function save_service_meta( $post_id ) {

		$args = array(
			'ID'			=> $post_id,
			'post_content' 	=> sanitize_textarea_field( $_POST['service_description'] ),
		);

		wp_update_post( $args );

		update_post_meta( $post_id, 'Service Capacity', sanitize_text_field( $_POST['service_capacity'] ) );
		update_post_meta( $post_id, 'Service Duration', sanitize_text_field( $_POST['service_duration'] ) );
		update_post_meta( $post_id, 'Service Price', sanitize_text_field( $_POST['service_price'] ) );
	}

	/**
	 * Saves the various meta fields for the provider custom post type
	 * @since 2.0.0
	 */
	public function save_provider_meta( $post_id ) {

		$args = array(
			'ID'			=> $post_id,
			'post_content' 	=> sanitize_textarea_field( $_POST['provider_description'] ),
		);

		wp_update_post( $args );

		update_post_meta( $post_id, 'Service Provider Email', sanitize_email( $_POST['provider_email'] ) );
		update_post_meta( $post_id, 'Service Provider Services', ( is_array( $_POST['provider_services'] ) ? implode( ',', array_map( 'sanitize_text_field', $_POST['provider_services'] ) ) : '' ) );

		foreach ( $this->days as $lowercase_day => $day ) {

			update_post_meta( $post_id, $day . ' Start', sanitize_text_field( $_POST[ $lowercase_day . '_start' ] ) );
			update_post_meta( $post_id, $day . ' Finish', sanitize_text_field( $_POST[ $lowercase_day . '_finish' ] ) );
			update_post_meta( $post_id, $day . ' Location', sanitize_text_field( $_POST[ $lowercase_day . '_location' ] ) );
		}
	}

	/**
	 * Add in new columns for the uasp-location post type
	 * @since 2.0.0
	 */
	public function register_location_table_columns( $defaults ) {
		global $ewd_uasp_controller;

		unset( $defaults['date'] );
		
		$defaults['ewd_uasp_location_description'] 	= __( 'Description', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_location_hours'] 		= __( 'Hours', 'ultimate-appointment-scheduling' );

		return $defaults;
	}

	/**
	 * Add in new columns for the uasp-service post type
	 * @since 2.0.0
	 */
	public function register_service_table_columns( $defaults ) {
		global $ewd_uasp_controller;

		unset( $defaults['date'] );
		
		$defaults['ewd_uasp_service_description'] 	= __( 'Description', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_service_capacity'] 		= __( 'Capacity', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_service_duration'] 		= __( 'Duration', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_service_price']	 		= __( 'Price', 'ultimate-appointment-scheduling' );

		return $defaults;
	}

	/**
	 * Add in new columns for the uasp-provider post type
	 * @since 2.0.0
	 */
	public function register_provider_table_columns( $defaults ) {
		global $ewd_uasp_controller;

		unset( $defaults['date'] );
		
		$defaults['ewd_uasp_provider_description'] 	= __( 'Description', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_provider_services'] 	= __( 'Services', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_provider_email'] 		= __( 'Email', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_provider_hours']	 	= __( 'Hours', 'ultimate-appointment-scheduling' );

		return $defaults;
	}

	/**
	 * Add in new columns for the uasp-exeception post type
	 * @since 2.0.0
	 */
	public function register_exception_table_columns( $defaults ) {
		global $ewd_uasp_controller;

		unset( $defaults['date'] );
		
		$defaults['ewd_uasp_exception_start'] 		= __( 'Start', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_exception_end'] 		= __( 'End', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_exception_location'] 	= __( 'Location', 'ultimate-appointment-scheduling' );
		$defaults['ewd_uasp_exception_provider']	= __( 'Provider', 'ultimate-appointment-scheduling' );

		return $defaults;
	}

	/**
	 * Set the content for the custom columns
	 * @since 2.0.0
	 */
	public function display_location_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'ewd_uasp_location_description' ) {

			echo esc_html( strip_tags( get_the_content( null, false, $post_id ) ) );
		}

		if ( $column_name == 'ewd_uasp_location_hours' ) {

			foreach ( $this->days as $day ) {

				if ( get_post_meta( $post_id, $day . ' Open', true ) == get_post_meta( $post_id, $day . ' Close', true ) ) {

					echo $day . ' - ' . __( 'Closed', 'ultimate-appointment-scheduling' ) . '<br />';
				}
				else {
					
					echo $day . ' ' . get_post_meta( $post_id, $day . ' Open', true ) . '-' . get_post_meta( $post_id, $day . ' Close', true ) . '<br/>';
				}
			}
		}
	}

	/**
	 * Set the content for the custom columns
	 * @since 2.0.0
	 */
	public function display_service_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'ewd_uasp_service_description' ) {

			echo esc_html( strip_tags( get_the_content( null, false, $post_id ) ) );
		}

		if ( $column_name == 'ewd_uasp_service_capacity' ) {

			echo esc_html( get_post_meta( $post_id, 'Service Capacity', true ) );
		}

		if ( $column_name == 'ewd_uasp_service_duration' ) {

			echo esc_html( get_post_meta( $post_id, 'Service Duration', true ) );
		}

		if ( $column_name == 'ewd_uasp_service_price' ) {

			echo esc_html( get_post_meta( $post_id, 'Service Price', true ) );
		}
	}

	/**
	 * Set the content for the custom columns
	 * @since 2.0.0
	 */
	public function display_provider_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'ewd_uasp_provider_description' ) {

			echo esc_html( strip_tags( get_the_content( null, false, $post_id ) ) );
		}

		if ( $column_name == 'ewd_uasp_provider_services' ) {

			$services = get_post_meta( $post_id, 'Service Provider Services', true );

			$service_ids = explode( ',', $services );

			foreach ( $service_ids as $service_id ) {

				$service = get_post( $service_id );

				echo esc_html( $service->post_title ) . ', '; 
			}
		}

		if ( $column_name == 'ewd_uasp_provider_email' ) {

			echo esc_html( get_post_meta( $post_id, 'Service Provider Email', true ) );
		}

		if ( $column_name == 'ewd_uasp_provider_hours' ) {

			foreach ( $this->days as $day ) {

				if ( get_post_meta( $post_id, $day . ' Start', true ) == get_post_meta( $post_id, $day . ' Finish', true ) ) {
					
					echo $day . ' - ' . __( 'Off', 'ultimate-appointment-scheduling' ) . '<br />';
				}
				else {
					
					echo $day . ' ' . get_post_meta( $post_id, $day . ' Start', true ) . '-' . get_post_meta( $post_id, $day . ' Finish', true ) . '<br/>';
				}
			}
		}
	}

	/**
	 * Set the content for the custom columns
	 * @since 2.0.0
	 */
	public function display_exception_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'ewd_uasp_exception_start' ) {

			echo esc_html( get_post_meta( $post_id, 'start', true ) );
		}

		if ( $column_name == 'ewd_uasp_exception_end' ) {

			echo esc_html( get_post_meta( $post_id, 'end', true ) );
		}

		if ( $column_name == 'ewd_uasp_exception_location' ) {

			$location_id = get_post_meta( $post_id, 'location_id', true );

			$location_post = $location_id ? get_post( $location_id ) : $location_id;

			echo $location_post ? $location_post->post_title : '';
		}

		if ( $column_name == 'ewd_uasp_exception_provider' ) {

			$provider_id = get_post_meta( $post_id, 'provider_id', true );

			$provider_post = $provider_id ? get_post( $provider_id ) : $provider_id;

			echo $provider_post ? $provider_post->post_title : '';
		}

	}

	/**
	 * Check every ~2 hours for old exceptions and remove them
	 * @since 2.0.0
	 */
	public function remove_expired_exceptions() {

		if ( get_transient( 'ewd-uasp-clear-exceptions' ) ) { return; }

		set_transient( 'ewd-uasp-clear-exceptions', true, 7200 );

		$args = array(
			'post_type'		=> EWD_UASP_EXCEPTION_POST_TYPE,
			'numberposts'	=> -1
		);

		$exceptions = get_posts( $args );

		foreach ( $exceptions as $exception ) {

			if ( time() < strtotime( get_post_meta( $exception->ID, 'end', true ) ) + 24 * 3600 ) { continue; }

			wp_delete_post( $exception->ID, true );
		}
	}

	/**
	 * Returns locations meeting the arguments passed in to the function
	 * @since 2.0.0
	 */
	public function get_locations( $args ) {

		$location_args = array(
			'post_type'	=> EWD_UASP_LOCATION_POST_TYPE,
			'numberposts'	=> -1
		);

		return get_posts( $location_args );
	}

	/**
	 * Returns services meeting the arguments passed in to the function
	 * @since 2.0.0
	 */
	public function get_services( $args ) {

		$service_args = array(
			'post_type'	=> EWD_UASP_SERVICE_POST_TYPE,
			'numberposts'	=> -1
		);

		return get_posts( $service_args );
	}

	/**
	 * Returns providers meeting the arguments passed in to the function
	 * @since 2.0.0
	 */
	public function get_service_providers( $args ) {

		$provider_args = array(
			'post_type'	=> EWD_UASP_PROVIDER_POST_TYPE,
			'numberposts'	=> -1
		);

		$providers = get_posts( $provider_args );

		foreach ( $providers as $key => $provider ) {

			if ( ! empty( $args['service_id'] ) ) {

				$provider_services = explode( ',', get_post_meta( $provider->ID, 'Service Provider Services', true ) );

				if ( ! in_array( $args['service_id'], $provider_services ) ) { 

					unset( $providers[ $key ] );

					continue;
				}
			}

			if ( ! empty( $args['location_id'] ) ) {
				
				$location_match = false;
				
				if ( ! empty( $args['day'] ) ) {

					if ( get_post_meta( $provider->ID, $args['day'] . ' Location', true ) == $args['location_id'] ) { $location_match = true; }
				}
				else {
				
					foreach ( $this->days as $day ) {

						if ( get_post_meta( $provider->ID, $day . ' Location', true ) == $args['location_id'] ) { $location_match = true; }
					}
				}

				if ( ! $location_match ) { 

					unset( $providers[ $key ] );

					continue;
				}
			}
		}

		return $providers;
	}

	/**
	 * Returns options for starting/ending times
	 * @since 2.0.0
	 */
	public function return_select_hours( $selected_hour ) { 

		ob_start();
		?>
		
		<option value='24:00' <?php if ( $selected_hour == '24:00' ) { echo 'selected'; } ?>><?php _e( 'Off/Closed', 'ultimate-appointment-scheduling' ); ?></option>
		<option value='0:00' <?php if ( $selected_hour == '0:00' ) { echo 'selected'; } ?>><?php _e( 'Midnight', 'ultimate-appointment-scheduling' ); ?></option>
		<option value='0:30' <?php if ( $selected_hour == '0:30' ) { echo 'selected'; } ?>>0:30</option>
		<option value='1:00' <?php if ( $selected_hour == '1:00' ) { echo 'selected'; } ?>>1:00</option>
		<option value='1:30' <?php if ( $selected_hour == '1:30' ) { echo 'selected'; } ?>>1:30</option>
		<option value='2:00' <?php if ( $selected_hour == '2:00' ) { echo 'selected'; } ?>>2:00</option>
		<option value='2:30' <?php if ( $selected_hour == '2:30' ) { echo 'selected'; } ?>>2:30</option>
		<option value='3:00' <?php if ( $selected_hour == '3:00' ) { echo 'selected'; } ?>>3:00</option>
		<option value='3:30' <?php if ( $selected_hour == '3:30' ) { echo 'selected'; } ?>>3:30</option>
		<option value='4:00' <?php if ( $selected_hour == '4:00' ) { echo 'selected'; } ?>>4:00</option>
		<option value='4:30' <?php if ( $selected_hour == '4:30' ) { echo 'selected'; } ?>>4:30</option>
		<option value='5:00' <?php if ( $selected_hour == '5:00' ) { echo 'selected'; } ?>>5:00</option>
		<option value='5:30' <?php if ( $selected_hour == '5:30' ) { echo 'selected'; } ?>>5:30</option>
		<option value='6:00' <?php if ( $selected_hour == '6:00' ) { echo 'selected'; } ?>>6:00</option>
		<option value='6:30' <?php if ( $selected_hour == '6:30' ) { echo 'selected'; } ?>>6:30</option>
		<option value='7:00' <?php if ( $selected_hour == '7:00' ) { echo 'selected'; } ?>>7:00</option>
		<option value='7:30' <?php if ( $selected_hour == '7:30' ) { echo 'selected'; } ?>>7:30</option>
		<option value='8:00' <?php if ( $selected_hour == '8:00' ) { echo 'selected'; } ?>>8:00</option>
		<option value='8:30' <?php if ( $selected_hour == '8:30' ) { echo 'selected'; } ?>>8:30</option>
		<option value='9:00' <?php if ( $selected_hour == '9:00' ) { echo 'selected'; } ?>>9:00</option>
		<option value='9:30' <?php if ( $selected_hour == '9:30' ) { echo 'selected'; } ?>>9:30</option>
		<option value='10:00' <?php if ( $selected_hour == '10:00' ) { echo 'selected'; } ?>>10:00</option>
		<option value='10:30' <?php if ( $selected_hour == '10:30' ) { echo 'selected'; } ?>>10:30</option>
		<option value='11:00' <?php if ( $selected_hour == '11:00' ) { echo 'selected'; } ?>>11:00</option>
		<option value='11:30' <?php if ( $selected_hour == '11:30' ) { echo 'selected'; } ?>>11:30</option>
		<option value='12:00' <?php if ( $selected_hour == '12:00' ) { echo 'selected'; } ?>>12:00</option>
		<option value='12:30' <?php if ( $selected_hour == '12:30' ) { echo 'selected'; } ?>>12:30</option>
		<option value='13:00' <?php if ( $selected_hour == '13:00' ) { echo 'selected'; } ?>>13:00</option>
		<option value='13:30' <?php if ( $selected_hour == '13:30' ) { echo 'selected'; } ?>>13:30</option>
		<option value='14:00' <?php if ( $selected_hour == '14:00' ) { echo 'selected'; } ?>>14:00</option>
		<option value='14:30' <?php if ( $selected_hour == '14:30' ) { echo 'selected'; } ?>>14:30</option>
		<option value='15:00' <?php if ( $selected_hour == '15:00' ) { echo 'selected'; } ?>>15:00</option>
		<option value='15:30' <?php if ( $selected_hour == '15:30' ) { echo 'selected'; } ?>>15:30</option>
		<option value='16:00' <?php if ( $selected_hour == '16:00' ) { echo 'selected'; } ?>>16:00</option>
		<option value='16:30' <?php if ( $selected_hour == '16:30' ) { echo 'selected'; } ?>>16:30</option>
		<option value='17:00' <?php if ( $selected_hour == '17:00' ) { echo 'selected'; } ?>>17:00</option>
		<option value='17:30' <?php if ( $selected_hour == '17:30' ) { echo 'selected'; } ?>>17:30</option>
		<option value='18:00' <?php if ( $selected_hour == '18:00' ) { echo 'selected'; } ?>>18:00</option>
		<option value='18:30' <?php if ( $selected_hour == '18:30' ) { echo 'selected'; } ?>>18:30</option>
		<option value='19:00' <?php if ( $selected_hour == '19:00' ) { echo 'selected'; } ?>>19:00</option>
		<option value='19:30' <?php if ( $selected_hour == '19:30' ) { echo 'selected'; } ?>>19:30</option>
		<option value='20:00' <?php if ( $selected_hour == '20:00' ) { echo 'selected'; } ?>>20:00</option>
		<option value='20:30' <?php if ( $selected_hour == '20:30' ) { echo 'selected'; } ?>>20:30</option>
		<option value='21:00' <?php if ( $selected_hour == '21:00' ) { echo 'selected'; } ?>>21:00</option>
		<option value='21:30' <?php if ( $selected_hour == '21:30' ) { echo 'selected'; } ?>>21:30</option>
		<option value='22:00' <?php if ( $selected_hour == '22:00' ) { echo 'selected'; } ?>>22:00</option>
		<option value='22:30' <?php if ( $selected_hour == '22:30' ) { echo 'selected'; } ?>>22:30</option>
		<option value='23:00' <?php if ( $selected_hour == '23:00' ) { echo 'selected'; } ?>>23:00</option>
		<option value='23:30' <?php if ( $selected_hour == '23:30' ) { echo 'selected'; } ?>>23:30</option>

		<?php 

		$output = ob_get_clean();

		return $output;
	}
}
} // endif;
