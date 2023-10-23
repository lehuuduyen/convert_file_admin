<?php

namespace Enable\Cors\Helpers;

/*
|--------------------------------------------------------------------------
| If this file is called directly, abort.
|--------------------------------------------------------------------------
*/
if ( ! defined( 'Enable\Cors\NAME' ) ) {
	exit;
}

use Enable\Cors\Traits\Singleton;
use const Enable\Cors\NAME;
use const Enable\Cors\VERSION;

/**
 * Class License
 *
 * @package Enable\Cors
 */
final class LicenseManager {
	use Singleton;

	public const KEY = NAME . '-license-key';


	/**
	 * LicenseManager constructor.
	 *
	 */
	private function __construct() {
		$this->register();
	}


	/**
	 * Register this plugin user to support and license system.
	 *
	 * @return bool Returns true if the registration is successful, false otherwise.
	 */
	private function register(): bool {

		if ( ! in_array( $this->get_current_action(), array( 'activate', 'deactivate', 'uninstall' ), true ) ) {
			return false;
		}

		$data = array(
			'website' => esc_url( home_url() ),
			'action'  => $this->get_current_action(),
			'version' => VERSION,
			'name'    => NAME,
		);

		$response = ToApi::instance()->post( 'license', $data );

		if ( 200 === $response['code'] ) {
			if ( array_key_exists( 'key', $response['data'] ) ) {
				update_option( self::KEY, $response['data']['key'], false );
			}
		}

		return true;
	}

	/**
	 * Get the current action.
	 *
	 * @return string The current action.
	 */
	private function get_current_action(): string {
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );

		return esc_attr( $backtrace[4]['function'] );
	}
}
