<?php

namespace Enable\Cors\Api;

use Enable\Cors\Helpers\LicenseManager;
use Enable\Cors\Helpers\ToApi;
use Enable\Cors\Traits\Api;
use Enable\Cors\Traits\Singleton;
use WP_REST_Request;
use WP_REST_Server;
use const Enable\Cors\FILE;

final class Support
{


	use Singleton;
	use Api;

	public function register()
	{
		register_rest_route(
			$this->namespace,
			'/support',
			array(
				array(
					'methods' => WP_REST_Server::CREATABLE,
					'callback' => array($this, 'support'),
					'permission_callback' => array($this, 'permissions_check'),
					'args' => array(),
				),
			)
		);
	}

	public function support(WP_REST_Request $request)
	{
		$response = array();
		$license = get_option(LicenseManager::KEY);
		if (empty($license)) {
			$response = array(
				'message' => __('Please re-activate this plugin to register automated support system.', 'enable-cors'),
				'success' => false,
			);
		} else {
			$data = $request->get_json_params();
			$empty = array_filter($data, array($this, 'validate_data'), ARRAY_FILTER_USE_BOTH);
			if (!empty($empty)) {
				$response = array(
					'message' => __('Invalid data', 'enable-cors'),
					'success' => false,
				);
			} else {
				$data = array_map('sanitize_text_field', $data);
				$data['email'] = get_option('admin_email');
				$data['plugins'] = $this->get_active_plugins();
				$data['server'] = $this->get_server_data();
				$api_response = ToApi::instance()->post('support', $data);
				if (422 === $api_response['code']) {
					$response = array(
						'message' => __('Invalid data', 'enable-cors'),
						'success' => false,
					);

				} elseif (200 === $api_response['code']) {
					$response = array(
						'message' => sprintf(
							'%s %s.',
							__('We will get back to you shortly to', 'enable-cors'),
							$data['email']
						),
						'success' => true,

					);
				} else {
					$response = array(
						'message' => __('You have already submitted a support request', 'enable-cors'),
						'success' => false,
					);
				}
			}
		}

		return rest_ensure_response($response);
	}

	/**
	 * Retrieves an array of active plugins.
	 *
	 * This function retrieves an array of all active plugins in WordPress.
	 * It first checks if the `get_plugins` function exists. If not, it includes
	 * the `plugin.php` file from the WordPress admin directory.
	 * It then calls the `get_plugins` function to retrieve an array of all plugins.
	 * It initializes an empty array called `$active_plugins` to store the active plugins.
	 * It also retrieves the list of active plugin keys from the `active_plugins` option.
	 *
	 * For each plugin in the `$plugins` array, it checks if the plugin file is the same as the
	 * current file. If so, it skips the iteration.
	 * It then extracts the name, version, and network status from the plugin data and stores it
	 * in the `$formatted` array.
	 * If the plugin is active (i.e., its file key is present in the `active_plugins` array),
	 * it adds the plugin to the `$active_plugins` array using the plugin's directory name as the key.
	 *
	 * @return array An associative array of active plugins, where the keys are the plugin directory names
	 *               and the values are arrays containing the plugin's name, version, and network status.
	 */
	public function get_active_plugins(): array
	{
		if (!function_exists('get_plugins')) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$active_plugins = array();
		$active_plugins_keys = get_option('active_plugins', array());

		foreach ($plugins as $plugin_file => $plugin_data) {
			if (plugin_basename(FILE) === $plugin_file) {
				continue;
			}
			// Extract the name, version, author, network, and plugin URI from the plugin data.
			$formatted = array(
				'name' => wp_strip_all_tags($plugin_data['Name']),
				'version' => array_key_exists(
					'Version',
					$plugin_data
				) ? wp_strip_all_tags($plugin_data['Version']) : '-',
				'network' => array_key_exists(
					'Network',
					$plugin_data
				) ? wp_strip_all_tags($plugin_data['Network']) : false,
			);
			// If a plugin is active, add it to the `active_plugins` array.
			if (in_array($plugin_file, $active_plugins_keys, true)) {
				$active_plugins[explode('/', $plugin_file)[0]] = $formatted;
			}
		}

		return $active_plugins;
	}

	/**
	 * Returns an array containing server data.
	 *
	 * This function retrieves information about the server environment,
	 * such as the software version, PHP version, and MySQL version.
	 * The server data array is then returned.
	 *
	 * @return array An array containing server data.
	 */
	private function get_server_data(): array
	{
		global $wpdb;

		$server_data = array();

		if (!empty($_SERVER['SERVER_SOFTWARE'])) {
			$server_data['software'] = $_SERVER['SERVER_SOFTWARE'];
		}

		if (function_exists('phpversion')) {
			$server_data['php'] = phpversion();
		}

		$server_data['mysql'] = $wpdb->db_version();

		return $server_data;
	}

	private function validate_data($value, $key): bool
	{
		if ('plugin' === $key) {
			$plugins = $this->get_active_plugins();
			if (!in_array($value, array_keys($plugins), true)) {
				return true;
			}
		}

		return empty($value);
	}
}
