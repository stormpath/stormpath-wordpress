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
		global $wp;

		if ( $wp->request === 'stormpath/callback' && strpos( $template, '404' ) ) {

			$manager = new self;
			if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
				return $template;
			}

			try {
				$response = $manager->application->handleIdSiteCallback( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

				switch ( strtolower( $response->status ) ) {
					case 'authenticated' :
						self::authenticate( $response );
						break;
					case 'logout' :
						self::logout( $response );
						break;
				}
			} catch ( \Exception $e ) {
				wp_die( esc_html( $e->getMessage() ) );
			}

			wp_redirect( '/wp-admin' );
			exit;
		}

		return $template;
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

		wp_redirect( '/' );
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
		return;

	}
}
