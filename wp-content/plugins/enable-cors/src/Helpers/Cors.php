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

use Enable\Cors\Plugin;
use Enable\Cors\Traits\Singleton;
use const Enable\Cors\NAME;

/**
 * Class Cors
 *
 * @package Enable\Cors
 */
final class Cors {
	use Singleton;

	/**
	 * It modifies the .htaccess file to add headers for allowing fonts and css
	 */
	public function modify_htaccess() {
		$option = get_option( Plugin::OPTION, false );
		if ( empty( $option ) ) {
			$lines = array(
				'<IfModule mod_headers.c>',
				'<FilesMatch "\.(ttf|ttc|otf|eot|woff|font.css|css|woff2)$">',
				'Header set Access-Control-Allow-Origin "*"',
				'Header set Access-Control-Allow-Credentials "true"',
				'</FilesMatch>',
				'</IfModule>',
				'<IfModule mod_headers.c>',
				'<FilesMatch "\.(avifs?|bmp|cur|gif|ico|jpe?g|jxl|a?png|svgz?|webp)$">',
				'Header set Access-Control-Allow-Origin "*"',
				'Header set Access-Control-Allow-Credentials "true"',
				'</FilesMatch>',
				'</IfModule>',
			);
		} elseif ( $option['enable'] ) {
			$lines = array( '<IfModule mod_headers.c>' );
			$credential = $option['allowCredentials'] ? 'true' : 'false';
			if ( $option['allowFont'] ) {
				$font = array(

					'<FilesMatch "\.(ttf|ttc|otf|eot|woff|font.css|css|woff2)$">',
					'Header set Access-Control-Allow-Origin "' . $option['allowedFor'] . '"',
					'Header set Access-Control-Allow-Credentials "' . $credential . '"',
					'</FilesMatch>',
				);
				$lines = array_merge( $lines, $font );
			}
			if ( $option['allowImage'] ) {
				$font = array(
					'<FilesMatch "\.(avifs?|bmp|cur|gif|ico|jpe?g|jxl|a?png|svgz?|webp)$">',
					'Header set Access-Control-Allow-Origin "' . $option['allowedFor'] . '"',
					'Header set Access-Control-Allow-Credentials "' . $credential . '"',
					'</FilesMatch>',
				);
				$lines = array_merge( $lines, $font );
			}
			$lines = array_merge( $lines, array( '</IfModule>' ) );
		} else {
			$lines = array( '' );
		}

		// Ensure get_home_path() is declared.
		$this->write_htaccess( $lines );
	}

	/**
	 * Inserts an array of strings into a file (.htaccess), placing it between
	 * BEGIN and END markers.
	 *
	 * @param array $lines need to write.
	 *
	 * @return void
	 */
	private function write_htaccess( array $lines ): void {
		// Ensure get_home_path() is declared.
		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( ! function_exists( 'insert_with_markers' ) || ! function_exists( 'got_mod_rewrite' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		$htaccess_file = get_home_path() . '.htaccess';

		if ( got_mod_rewrite() ) {
			insert_with_markers( $htaccess_file, NAME, $lines );
		}
	}

	/**
	 * It writes an empty array to the .htaccess file.
	 */
	public function restore_htaccess() {
		$lines = array( '' );
		$this->write_htaccess( $lines );
	}

	/**
	 * It sets headers for Cross-Origin Resource Sharing (CORS) based on options set in the
	 * plugin's settings.
	 *
	 * @return void If the `` variable is empty, the function will return nothing (void).
	 */
	public function headers(): void {
		$options = get_option( Plugin::OPTION );
		if ( empty( $options ) ) {
			return;
		}

		if ( array_key_exists( 'enable', $options ) && true === $options['enable'] ) {
			if ( array_key_exists( 'allowedFor', $options ) ) {
				header( 'Access-Control-Allow-Origin: ' . $options['allowedFor'] );
			}
			if ( array_key_exists( 'allowedMethods', $options ) && gettype( $options['allowedMethods'] ) === 'array' ) {
				header( 'Access-Control-Allow-Methods: ' . implode( ',', $options['allowedMethods'] ) );
			}
			if ( array_key_exists( 'allowedHeader', $options ) && gettype( $options['allowedHeader'] ) === 'array' ) {
				header( 'Access-Control-Allow-Headers: ' . implode( ', ', $options['allowedHeader'] ) );
			}
			if ( array_key_exists( 'allowCredentials', $options ) ) {
				header( 'Access-Control-Allow-Credentials: ' . $options['allowCredentials'] );
			}
		}
	}
}
