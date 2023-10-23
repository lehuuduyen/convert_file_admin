<?php

namespace Enable\Cors\Api;

/*
|--------------------------------------------------------------------------
| If this file is called directly, abort.
|--------------------------------------------------------------------------
*/
if ( ! defined( 'Enable\Cors\NAME' ) ) {
	exit;
}

use Enable\Cors\Helpers\Cors;
use Enable\Cors\Plugin;
use Enable\Cors\Traits\Api;
use Enable\Cors\Traits\Singleton;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Settings
 *
 * @package Enable\Cors
 */
final class Settings {

	use Api;
	use Singleton;

	/**
	 * Register the routes for serving data from custom table
	 */
	public function register() {
		register_rest_route(
			$this->namespace,
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'settings' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'settings' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get data from custom table
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function settings( WP_REST_Request $request ) {
		$option   = get_option( Plugin::OPTION, false );
		$response = array(
			'data'    => null,
			'message' => null,
			'success' => false,
		);
		if ( false !== $option ) {
			$response['data']    = $option;
			$response['success'] = true;
		}
		if ( $request->get_method() === WP_REST_Server::CREATABLE ) {
			$options = $request->get_json_params();
			if ( array_key_exists( 'allowedFor', $options ) ) {
				update_option( Plugin::OPTION, $options );
				Cors::instance()->modify_htaccess();
				wp_cache_flush();
				$response['data']    = $options;
				$response['message'] = __( 'Settings Updated', 'enable-cors' );
				$response['success'] = true;
			} else {
				$response['data']    = $option;
				$response['message'] = __( 'Something went wrong!', 'enable-cors' );
				$response['success'] = false;
			}
		}

		return rest_ensure_response( $response );
	}
}
