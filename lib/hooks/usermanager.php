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

use Stormpath\Resource\Account;
use Stormpath\Resource\ResourceError;
use WP_Error;
use WP_User;

/**
 * Class UserManager
 *
 * @category PHP
 * @package Stormpath
 * @author Stormpath <support@stormpath.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link https://stormpath.com
 */
class UserManager {

	/**
	 * UserManager Constructor
	 */
	public function __construct() {

	}


	/**
	 * Get the WordPress user by email.
	 *
	 * @param string $email User email address.
	 * @return boolean | \WP_User
	 */
	public function find_user_by_email( $email ) {
		$wp_user = get_user_by( 'email', $email );

		return $wp_user;
	}

	/**
	 * Create a WordPress User.
	 *
	 * @param Account $account The Stormpath Account.
	 *
	 * @throws \InvalidArgumentException A user already exists in WordPress with that username.
	 * @return WP_User
	 */
	public function create_wp_user( Account $account ) {
		$userAccountInfo = [
			'user_email'    => $account->email,
			'user_login'    => $account->username,
			'first_name'    => $account->givenName,
			'last_name'     => $account->surname,
			'user_pass'     => wp_hash_password( wp_generate_password( 32 ) ),
		];
		$newUser = wp_insert_user( $userAccountInfo );

		if ( $newUser instanceof WP_Error ) {

			wp_mail( get_option( 'admin_email' ), 'Stormpath authentication error.', 'A user tried to log into the 
			WordPress website, and succeeded, however the user was not able to log in fully because the creation
			of the WordPress user failed.  The reason: ' . $newUser->get_error_message() );

			throw new \InvalidArgumentException( 'Invalid Username/Email or Password.  Please try again, or contact the 
			the site administrator. <a href="/">Home</a>' );
		}

		return new WP_User( $newUser );
	}
}
