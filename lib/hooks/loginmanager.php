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
 * Class LoginManager
 *
 * @category PHP
 * @package Stormpath
 * @author Stormpath <support@stormpath.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link https://stormpath.com
 */
class LoginManager {

	/**
	 * Should we use ID Site for Login.
	 * ddd
	 *
	 * @var bool
	 */
	protected $useIdSite;

	/**
	 * LoginManager constructor.
	 */
	public function __construct() {
		$this->useIdSite = get_option( 'stormpath_id_site' );
	}

	/**
	 * Create a new instance of the Login Manager
	 *
	 * @return LoginManager
	 */
	public static function handle() {
		$manager = new self;

		if ( $manager->useIdSite ) {
			$manager->redirect_to_id_site();
		}

		return $manager;
	}


	/**
	 * Redirect to IdSite.
	 *
	 * @return void
	 */
	public function redirect_to_id_site() {
		$application = Application::get_instance();

		wp_redirect( $application->get_application()->createIdSiteUrl( [ 'callbackUri' => get_site_url() . '/stormpath/callback' ] ) );
		exit;
	}

	/**
	 * Logout from Id Site.
	 *
	 * @param null|string $state The state passed from the JWT.
	 * @return void
	 */
	public function logout( $state = null ) {

		if ( $this->useIdSite ) {
			$application = Application::get_instance();
			$properties = [ 'callbackUri' => get_site_url() . '/stormpath/callback', 'logout' => true ];

			if ( null !== $state ) {
				$properties['state'] = $state;
			}
			wp_redirect( $application->get_application()->createIdSiteUrl( $properties ) );
			exit;
		}
	}

	/**
	 * The main authenticate method.
	 *
	 * @param mixed  $user     The WordPress User.
	 * @param string $username Username.
	 * @param string $password Password.
	 * @return mixed
	 */
	public function authenticate( $user, $username, $password ) {
		remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );

		if ( empty( $username ) || empty( $password ) ) {
			if ( is_wp_error( $user ) ) {
				return $user;
			}
		}

		$authenticationRequest = new \Stormpath\Authc\UsernamePasswordRequest(
			$username,
			$password
		);

		$account = null;
		try {
			$application = Application::get_instance()->get_application();

			$account = $application->authenticateAccount( $authenticationRequest )->account;

			$userManager = new UserManager();
			$user = $userManager->find_user_by_email( $account->email );

			if ( false === $user ) {
				try {
					$user = $userManager->create_wp_user( $account );
				} catch (\Exception $e) {
					$loginManager = new LoginManager();
					$loginManager->logout( $e->getMessage() );
					wp_die();
				}
			}

			wp_set_auth_cookie( $user->ID );
			return wp_set_current_user( $user->ID, $user->user_login );

		} catch (\Exception $e) {
			return false;
		}

	}

	/**
	 * Attempt a normal login with WordPress core function.
	 *
	 * @param string $username The username of login attempt.
	 * @param string $password The password of the login attempt.
	 * @return boolean|\Stormpath\Resource\Account
	 */
	private function attempt_normal_login( $username, $password ) {
		$user = get_user_by( 'login', $username );
		if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			return $this->register_stormpath_user( $user, $password );
		}
		$user = get_user_by( 'email', $username );
		if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			return $this->register_stormpath_user( $user, $password );
		}
		return false;
	}
}
