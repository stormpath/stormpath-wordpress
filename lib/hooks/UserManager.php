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
use Stormpath\WordPress\ApiKeys;
use Stormpath\WordPress\Application;
use Stormpath\WordPress\Client;
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

		$wpuser = get_user_by( 'login', $account->username );

		if ( false !== $wpuser ) {
			$userAccountInfo['user_login'] = $account->email;
		}

		$newUser = wp_insert_user( $userAccountInfo );

		return new WP_User( $newUser );
	}

	/**
	 * Delete a user from Stormpath based on the WP User.
	 *
	 * @param integer $userId The WordPress User Id to delete from Stormpath.
	 *
	 * @return void
	 */
	public function delete_stormpath_user( $userId ) {
		$user = get_user_by( 'ID', $userId );
		$email = $user->user_email;

		try {
			$application = Application::get_instance()->get_application();
			$account = $application->getAccounts( [ 'email' => rawurlencode( $email ) ] );

			if ( 0 !== $account->getSize() ) {
				$account = $account->getIterator()->current();
				$account->delete();

			}
		} catch ( ResourceError $re ) {
			$message = $re->getMessage();
			wp_die( wp_kses( $message ) );
		}

	}


	/**
	 * Hook callback for when a user was registered.
	 *
	 * @param int $wpUserId The WordPress User Id.
	 * @return void
	 */
	public function user_registered( $wpUserId ) {
		if ( ! isset( $_REQUEST['_wpnonce_create-user'] ) || ! isset( $_POST['pass1'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce_create-user'] ) ), 'create-user' ) ) {
			wp_die( 'nonce not valid' );
		}
		$user = new WP_User( $wpUserId );
		$password = sanitize_text_field( wp_unslash( $_POST['pass1'] ) );
		$account = new \stdClass();
		$account->email = $user->user_email;
		$account->password = $password;
		$account->givenName = $user->user_firstname;
		$account->surname = $user->user_lastname;
		$account->username = $user->user_login;

		$application = Application::get_instance()->get_application();
		$client = Client::get_instance( ApiKeys::get_instance() )->get_client();

		$options = apply_filters( 'stormpath_user_register_options', [], $account );

		$accountObj = $client->getDataStore()->instantiate( Account::class, $account, $options );
		$account = $application->createAccount( $accountObj );

		do_action( 'stormpath_user_registered', $account, $user );
	}


	/**
	 * Register a user from a valid WP_User.
	 *
	 * @param WP_User $wpUser   The user being registered.
	 * @param string  $password The password for the user.
	 * @return Account|WP_Error|boolean
	 */
	public function register_stormpath_user( WP_User $wpUser, $password ) {
		try {
			$account = new \stdClass();
			$account->email = $wpUser->user_email;
			$account->password = $password;
			$account->givenName = $wpUser->user_firstname ?: 'Temp FirstName';
			$account->surname = $wpUser->user_lastname ?: 'Temp LastName';
			$account->username = $wpUser->user_login;

			$application = Application::get_instance()->get_application();
			$client = Client::get_instance( ApiKeys::get_instance() )->get_client();

			$accountObj = $client->getDataStore()->instantiate( Account::class, $account );
			return $application->createAccount( $accountObj );
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Hook callback for when a profile was updated.
	 *
	 * @param int    $userId  Id of User.
	 * @param object $oldData old user data.
	 * @return void
	 */
	public function profile_update( $userId, $oldData ) {
		$newData = get_userdata( $userId );
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! isset( $_POST['pass1-text'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'update-user_' . $userId ) ) {
			wp_die( 'nonce not valid' );
		}
		// Remove the nag if the password has been changed.
		if ( $newData->user_pass !== $oldData->user_pass ) {
			$user = new WP_User( $userId );
			$this->password_changed( $user, sanitize_text_field( wp_unslash( $_POST['pass1-text'] ) ) );
		}
	}


	/**
	 * Hook Callback for when a password was changed.
	 *
	 * @param WP_User $user     The User.
	 * @param string  $password The Password.
	 * @return void
	 */
	public function password_changed( $user, $password ) {
		$application = Application::get_instance()->get_application();
		$accounts = $application->accounts->setSearch( [ 'q' => rawurlencode( $user->user_email ) ] );
		if ( $accounts->size > 0 ) {
			$account = $accounts->getIterator()->current();
			$account->password = $password;
			$account->save();
			$id = $user->ID;
		}
	}
}
