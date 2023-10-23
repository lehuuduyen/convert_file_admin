<?php

namespace Enable\Cors\Admin;

/*
|--------------------------------------------------------------------------
| If this file is called directly, abort.
|--------------------------------------------------------------------------
*/
if ( ! defined( 'Enable\Cors\NAME' ) ) {
	exit;
}

use Enable\Cors\Api\Support;
use Enable\Cors\Traits\Singleton;
use const Enable\Cors\NAME;
use const Enable\Cors\URL;
use const Enable\Cors\VERSION;

/**
 * Class Menu
 *
 * @package Enable\Cors
 */
final class Menu {

	use Singleton;


	/**
	 * It registers the menu page for admin.
	 *
	 * @return void
	 */
	public function register() {
		add_menu_page(
			__( 'Enable CORS', 'enable-cors' ),
			__( 'Enable CORS', 'enable-cors' ),
			'manage_options',
			'enable-cors',
			array( $this, 'render' )
		);
	}

	/**
	 * It will display dashboard template. Also remove admin footer text.
	 *
	 * @return void
	 */
	public function render() {
		echo wp_kses_post( '<div id="' . NAME . '"></div>' );
	}

	/**
	 * It will add module attribute to script tag.
	 *
	 * @param string $tag of script.
	 * @param string $id of script.
	 *
	 * @return string
	 */
	public function add_module( string $tag, string $id ): string {
		if ( NAME === $id ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}

		return $tag;
	}

	/**
	 * It loads scripts based on plugin's mode, dev or prod.
	 *
	 * @return void
	 */
	public function scripts() {
			wp_enqueue_style( NAME, URL . 'assets/dist/index.css', array(), VERSION );
			wp_enqueue_script( NAME, URL . 'assets/dist/index.js', array( 'wp-i18n' ), VERSION, true );
		$link = esc_url_raw( 'https://wordpress.org/support/plugin/enable-cors/reviews/' );
		wp_localize_script(
			NAME,
			'enable_cors',
			array(
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'endpoint' => get_rest_url() . 'enable-cors/v1/',
				'strings'  => array(
					'name'     => __( 'Enable CORS', 'enable-cors' ),
					'tagline'  => __( 'Enable Cross-Origin Resource Sharing for any or specific origin.', 'enable-cors' ),
					'form'     => array(
						'basic' => array(
							'enable'  => array(
								'label' => __( 'Enable CORS', 'enable-cors' ),
								'hint'  => __(
									'Configure the server to include CORS headers in the response to allow cross-origin requests.',
									'enable-cors'
								),
							),
							'website' => array(
								'label' => __( 'Allowed Websites', 'enable-cors' ),
								'hint'  => __(
									'Specify the specific website (e.g., https://devkabir.shop) that is allowed to make requests.',
									'enable-cors'
								),
							),
							'method'  => array(
								'label' => __( 'Allowed Request Methods', 'enable-cors' ),
								'hint'  => __(
									'Specify the allowed HTTP methods (e.g., GET,POST,OPTIONS) for cross-origin requests.',
									'enable-cors'
								),
							),
							'header'  => array(
								'label' => __( 'Set Response Headers', 'enable-cors' ),
								'hint'  => __(
									'Set the desired response headers (e.g., Content-Type,Authorization) to be included in the response for other websites.',
									'enable-cors'
								),
							),
							'cred'    => array(
								'label' => __( 'Allow Credentials', 'enable-cors' ),
								'hint'  => __(
									'Configure the server to allow credentials (such as cookies or authorization headers) to be included in the cross-origin request.',
									'enable-cors'
								),
							),
							'font'    => array(
								'label' => __( 'Allow Fonts Sharing', 'enable-cors' ),
								'hint'  => __(
									'Configure the server to share fonts (such as ttf or font.css) to be included in the cross-origin request.',
									'enable-cors'
								),
							),
							'image'   => array(
								'label' => __( 'Allow Images Sharing', 'enable-cors' ),
								'hint'  => __(
									'Configure the server to share Images (such as svg or webp) to be included in the cross-origin request.',
									'enable-cors'
								),
							),
						),
						'save'  => __( 'Save', 'enable-cors' ),
						'reset' => __( 'Reset', 'enable-cors' ),
					),
					'thankYou' => array(
						'title'   => __( 'Thank You', 'enable-cors' ),
						'peoples' => array(
							'mehbubrashid' => array(
								'name'  => __( 'Mehbub Rashid', 'enable-cors' ),
								'image' => sanitize_text_field( '7a692106063b5b14dfe8962e83a738f0' ),
								'issue' => __( 'Found issue on non-root server installations', 'enable-cors' ),
								'link'  => sanitize_text_field( 'error-404-while-saving-the-settings' ),
							),
						),

					),
					'notice'   => array(
						'title'    => __( 'Notice', 'enable-cors' ),
						'notices'  => array(
							'endpoint' => array(
								'title'   => __( 'Your API endpoint is', 'enable-cors' ),
								'content' => sprintf( '<a href="%s" target="_blank">%s</a>', get_rest_url(), get_rest_url() ),
							),
							'review'   => array(
								'title'   => __( 'Before posting your review', 'enable-cors' ),
								'content' => "<li>Kindly make sure to <strong>clear your cache after saving the settings</strong> to ensure smooth functionality. If the issue persists, please don't hesitate to reach out for assistance.</li><li>According to WordPress guidelines, it's important to recognize that <strong>you are considered a valued user of this plugin,</strong>and I am here to assist you. I kindly request that you share your reviews exclusively if you have utilized this plugin for your needs. If you wish to share your thoughts and experiences with the plugin, I encourage you to post a review on <a href='{$link}' target='_blank'>WordPress.org</a>.</li><li>Thank you for your understanding and cooperation!</li>",
							),
						),
						'security' => array(
							'title'   => __( 'Security Warning', 'enable-cors' ),
							'content' => sprintf( '<strong>*</strong> %s', __( 'means that any website can send a request to your WordPress site and access the server’s response. This can be a potential security risk.', 'enable-cors' ) ),
							'type'    => 'danger',
						),
						'unsaved'  => array(
							'title'   => __( 'Unsaved Settings', 'enable-cors' ),
							'content' => __( 'To enable CORS on your site, please save settings.', 'enable-cors' ),
							'type'    => 'warning',
						),
					),
					'support'  => array(
						'title' => __( 'Support', 'enable-cors' ),
						'form'  => array(
							'inputs' => array(
								'plugin'  => array(
									'label'       => __( 'Plugin', 'enable-cors' ),
									'options'     => Support::instance()->get_active_plugins(),
									'placeholder' => __( 'Select a plugin', 'enable-cors' ),
								),
								'error'   => array(
									'label'       => __( 'Console Error', 'enable-cors' ),
									'placeholder' => __( 'Paste console error here', 'enable-cors' ),
								),
								'consent' => array(
									'prefix' => __( 'I agree to', 'enable-cors' ),
									'label'  => sprintf(
										"<span class='sr-only'>%s</span>%s</span>",
										__( 'I agree to', 'enable-cors' ),
										__( 'share my site\'s URL, email address and a list of activated plugins with phprtsan for the purpose of providing support.' )
									),
								),
							),
							'send'   => __( 'Send', 'enable-cors' ),
						),
					),
				),
			)
		);
	}
}
