<?php

namespace Enable\Cors;

/*
|--------------------------------------------------------------------------
| If this file is called directly, abort.
|--------------------------------------------------------------------------
*/
if ( ! defined( 'Enable\Cors\NAME' ) ) {
	exit;
}

use Enable\Cors\Admin\Menu;
use Enable\Cors\Api\Settings;
use Enable\Cors\Api\Support;
use Enable\Cors\Helpers\Cors;
use Enable\Cors\Helpers\LicenseManager;

/**
 * Class Plugin
 *
 * @package Enable\Cors
 */
final class Plugin {


	/**
	 * Plugin option key.
	 */
	public const OPTION = NAME . '-options';

	/**
	 * It will load during activation
	 *
	 * @return void
	 */
	public static function activate() {
		self::enable_updates();
		Cors::instance()->modify_htaccess();
		LicenseManager::instance();
		// redirect you to the settings page after activation.
		add_action( 'activated_plugin', array( self::class, 'redirect' ) );
	}

	/**
	 * Enable plugin's auto-update on activation.
	 *
	 * @return void
	 */
	private static function enable_updates() {
		$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
		$plugin = plugin_basename( FILE );
		if ( false === in_array( $plugin, $auto_updates, true ) ) {
			$auto_updates[] = $plugin;
			update_site_option( 'auto_update_plugins', $auto_updates );
		}
	}

	/**
	 * It will load during deactivation
	 *
	 * @return void
	 */
	public static function deactivate() {
		self::disable_updates();
		Cors::instance()->restore_htaccess();
	}


	/**
	 * Disable auto-update deactivation or uninstall
	 *
	 * @return void
	 */
	private static function disable_updates() {
		$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
		$plugin = plugin_basename( FILE );
		$update = array_diff( $auto_updates, array( $plugin ) );
		update_site_option( 'auto_update_plugins', $update );
	}

	/**
	 * It will load during un installation
	 *
	 * @return void
	 */
	public static function uninstall() {
		LicenseManager::instance();
		self::disable_updates();
		delete_option( self::OPTION );
		delete_option( LicenseManager::KEY );
	}

	/**
	 * Plugin Initiator.
	 *
	 * @return void
	 */
	public static function init() {
		// add links under plugin name.
		add_filter( 'plugin_action_links_' . plugin_basename( FILE ), array( self::class, 'actions' ) );
		// Scripts for web.
		Cors::instance()->headers();
		// Scripts for rest API.
		add_action( 'rest_api_init', array( self::class, 'api' ) );
		if ( is_admin() ) {
			$menu = Menu::instance();
			// Register Menu.
			add_action( 'admin_menu', array( $menu, 'register' ) );

			if ( array_key_exists( 'page', $_GET ) && 'enable-cors' === $_GET['page'] ) {
				// Load script.
				add_action( 'admin_enqueue_scripts', array( $menu, 'scripts' ) );
				// Add module attribute.
				add_filter( 'script_loader_tag', array( $menu, 'add_module' ), 10, 3 );

				add_filter(
					'admin_footer_text',
					function () {
						return sprintf( "<strong>%s ❤ %s <a style='text-decoration: none' href='%s' target='_blank'><strong>%s</strong></a><strong/>", __( 'Created with', 'enable-cors' ), __( 'by', 'enable-cors' ), esc_url_raw( 'https://phprtsan.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=enable-cors' ), __( 'phprtsan', 'enable-cors' ) );
					}
				);
				add_filter(
					'update_footer',
					function () {
						return sprintf( 'You are using <strong>%s</strong> version', VERSION );
					},
					11
				);
			}
		}
	}

	/**
	 * Scripts for api.
	 *
	 * @return void
	 */
	public static function api() {
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		add_filter( 'rest_pre_serve_request', array( Cors::instance(), 'headers' ) );
		Settings::instance()->register();
		Support::instance()->register();
	}

	/**
	 * It redirects to the admin page if the plugin is enabled.
	 *
	 * @param string $plugin activated plugin name.
	 */
	public static function redirect( string $plugin ) {
		if ( plugin_basename( FILE ) === $plugin ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . NAME ) );
			exit();
		}
	}

	/**
	 * This PHP function adds a "Settings" link to an array of actions.
	 *
	 * @param array $actions collections.
	 *
	 * @return array
	 */
	public static function actions( array $actions ): array {
		$actions[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_admin_url( null, 'admin.php?page=enable-cors' ) ),
			esc_attr__( 'Settings', 'enable-cors' )
		);

		return $actions;
	}
}
