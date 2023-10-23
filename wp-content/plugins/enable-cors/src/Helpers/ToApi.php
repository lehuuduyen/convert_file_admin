<?php

namespace Enable\Cors\Helpers;

use Enable\Cors\Traits\Singleton;

final class ToApi {

	use Singleton;

	public const DOMAIN = 'https://phprtsan.com/api/org/';

	private $headers = array();

	private function __construct() {
		$site_url = esc_url( home_url() );

		$this->headers = array(
			'user-agent'    => 'Enable\Cors ' . password_hash( $site_url, PASSWORD_BCRYPT ),
			'Accept'        => 'application/json',
			'Origin'        => $site_url,
			'Referer'       => $site_url,
			'Cache-Control' => 'no-cache',
		);
		$license       = get_option( LicenseManager::KEY );

		if ( $license ) {
			$this->headers['Authorization'] = $license;
		}
	}

	public function post( string $url, array $data ) {

		$this->headers['Content-Type'] = 'application/json';

		$response = wp_remote_post(
			self::DOMAIN . $url,
			array(
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'headers'     => $this->headers,
				'body'        => wp_json_encode( $data ),
				'sslverify'   => false,
				'cookies'     => array(),
			)
		);

		$body = wp_remote_retrieve_body( $response );
		return array(
			'data' => json_decode( $body, true ),
			'code' => wp_remote_retrieve_response_code( $response ),
		);
	}
}
