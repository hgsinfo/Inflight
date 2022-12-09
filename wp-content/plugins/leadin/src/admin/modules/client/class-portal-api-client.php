<?php

namespace Leadin\admin\client;

use Leadin\client\HubSpot_Base_Api_Client;

/**
 * Client for retrieving portal information.
 */
class Portal_Api_Client extends HubSpot_Base_Api_Client {

	/**
	 * Make an API request to get the portal's details including the hublet it's in.
	 *
	 * @param integer $portal_id portal id.
	 */
	public function get_portal_details( $portal_id ) {
		$api_path     = "/account-info/v3/details?portalId=$portal_id";
		$cross_hublet = true;

		$response      = $this->authenticated_request(
			$api_path,
			'GET',
			'',
			$cross_hublet
		);
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		return $response_body;
	}

}
