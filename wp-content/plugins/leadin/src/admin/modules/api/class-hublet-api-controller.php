<?php

namespace Leadin\admin\api;

use Leadin\api\Base_Api_Controller;
use Leadin\data\Portal_Options;
use Leadin\admin\client\Portal_Api_Client;

/**
 * Hublet Api. Used to fetch portal's hublet and update in case of region migration
 */
class Hublet_Api_Controller extends Base_Api_Controller {

	/**
	 * Class constructor, register route.
	 */
	public function __construct() {
		self::register_leadin_admin_route(
			'/hublet',
			\WP_REST_Server::READABLE,
			array( $this, 'get_hublet' )
		);
		self::register_leadin_admin_route(
			'/hublet',
			\WP_REST_Server::EDITABLE,
			array( $this, 'update_hublet' )
		);
	}

	/**
	 * Get's correct hublet and returns it.
	 */
	public function get_hublet() {
		$portal_id      = Portal_Options::get_portal_id();
		$client         = new Portal_Api_Client();
		$portal_details = $client->get_portal_details( $portal_id );
		$hublet         = $portal_details['dataHostingLocation'];
		if ( ! $hublet ) {
			return new \WP_REST_Response( 'Failed to load hublet', 500 );
		}

		return new \WP_REST_Response(
			array(
				'hublet' => $hublet,
			),
			200
		);
	}

	/**
	 * Get's correct hublet and updates it in Options
	 *
	 * @param array $request Request body.
	 */
	public function update_hublet( $request ) {
		$data   = json_decode( $request->get_body(), true );
		$hublet = $data['hublet'];

		if ( ! $hublet ) {
			return new \WP_REST_Response( 'Hublet is required', 400 );
		}
		Portal_Options::set_hublet( $hublet );
		return new \WP_REST_Response( $hublet, 200 );
	}

}
