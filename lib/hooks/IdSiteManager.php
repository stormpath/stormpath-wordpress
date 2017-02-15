<?php
/**
 * Stormpath WordPress is a WordPress plugin to authenticate against a Stormpath Directory.
 * Copyright (C) 2016  Stormpath
 *
 * This file is part of Stormpath WordPress.
 *
 * Stormpath WordPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Stormpath WordPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Stormpath\WordPress;
 */

namespace Stormpath\WordPress\Hooks;
use Stormpath\WordPress\Application;

/**
 * Class IdSiteManager
 *
 * @category PHP
 * @package Stormpath
 * @author Stormpath <support@stormpath.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link https://stormpath.com
 */
class IdSiteManager {

	/**
	 * The Stormpath application resource.
	 *
	 * @var null|\Stormpath\Resource\Application
	 */
	protected $application;

	/**
	 * IdSiteManager constructor.
	 */
	public function __construct() {
		$this->application = Application::get_instance()->get_application();
	}

	/**
	 * Listens for anything coming into stormpath/callback.
	 *
	 * @param string $template The template to render.
	 * @return mixed
	 */
	public static function add_id_site_callback( $template ) {

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$callback_path = apply_filters( 'stormpath_callback_path', 'stormpath/callback' );

		$request_uri = esc_url_raw( $_SERVER['REQUEST_URI'] );

		// Remove any starting slashes
		if ( substr( $request_uri, 0, 1 ) == '/' ) {
			$request_uri_no_starting_slash = substr( $request_uri, 1 );
		}

		$path = substr( $request_uri_no_starting_slash, 0, strlen( $callback_path ) );

		if ( $path === $callback_path ) {

			$manager = new self;

			try {
				$response = $manager->application->handleIdSiteCallback( $request_uri );

				switch ( strtolower( $response->status ) ) {
					case 'authenticated' :
						self::authenticate( $response );
						break;
					case 'registered' :
						self::register( $response );
						break;
					case 'logout' :
						self::logout( $response );
						break;
				}
			} catch ( \Exception $e ) {
				wp_die( esc_html( $e->getMessage() ) );
			}
		}
	}

	/**
	 * Logout method.
	 *
	 * @param \stdClass $response The response from ID Site.
	 * @return void
	 */
	public static function logout( $response ) {
		if ( '' !== $response->state ) {

			wp_die( wp_kses( $response->state, array(
				'a' => array( 'href' => array() ),
			)) );
		}

		do_action( 'stormpath_callback_logout', $response );

		$redirect_to = '/wp-login.php?loggedout=true';

		$user = get_user_by( 'email', $response->account->email );

		$redirect_to = apply_filters( 'logout_redirect', $redirect_to, '', $user );

		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Authenticate the WordPress use that just logged in via Stormpath ID Site.
	 *
	 * @param \stdClass $response The response from ID Site.
	 * @return void
	 */
	public static function authenticate( $response ) {
		$userManager = new UserManager();
		$user = $userManager->find_user_by_email( $response->account->email );

		// We do not have a user, so we should create the Stormpath user In WordPress so we can log them in.
		if ( false === $user ) {
			try {
				$user = $userManager->create_wp_user( $response->account );
			} catch (\Exception $e) {
				$loginManager = new LoginManager();
				$loginManager->logout( $e->getMessage() );
				wp_die();
			}
		}

		wp_set_current_user( $user->ID, $user->user_login );
		wp_set_auth_cookie( $user->ID );

		do_action( 'stormpath_callback_authenticate', $response );

		$redirect_to = apply_filters( 'login_redirect', admin_url(), '' , wp_get_current_user() );
		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Register the WordPress user that just logged in via Stormpath ID Site.
	 *
	 * Since the register and authenticate are basically the same, lets just use
	 * the authenticate method.
	 *
	 * @param \stdClass $response The response from ID Site.
	 * @return void
	 */
	public static function register( $response ) {
		self::authenticate( $response );
	}
}
