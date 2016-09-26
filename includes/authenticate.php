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

namespace Stormpath\WordPress;

use Stormpath\Resource\Account;
use Stormpath\Resource\Application;
use WP_Error;
use WP_User;

/**
 * Class Authenticate
 *
 * @category    Stormpath
 * @package     Stormpath\WordPress
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
class Authenticate
{
	/**
	 * The Stormpath Client.
	 *
	 * @var Client
	 */
	private $spClient;
	/**
	 * The Stormpath Application.
	 *
	 * @var Application
	 */
	private $spApplication;

	/**
	 * Authenticate constructor.
	 *
	 * @param Application       $application The Stormpath Application.
	 * @param \Stormpath\Client $client      The Stormpath Client.
	 */
	public function __construct( $application, \Stormpath\Client $client ) {
		$this->spClient = $client;
		$this->spApplication = $application;

	}

	/**
	 * The main authenticate method.
	 *
	 * @param null|WP_User|WP_Error $user     The WordPress User.
	 * @param string                $username Username.
	 * @param string                $password Password.
	 * @return bool|WP_Error|WP_User
	 */
	public function authenticate( $user, $username, $password ) {
		remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );

		if ( empty( $username ) || empty( $password ) ) {
			if ( is_wp_error( $user ) ) {
				return $user;
			}

			$error = new WP_Error();

			if ( empty( $username ) ) {
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: The username field is empty.' ) );
			}

			if ( empty( $password ) ) {
				$error->add( 'empty_password', __( '<strong>ERROR</strong>: The password field is empty.' ) );
			}

			return $error;
		}

		$authenticationRequest = new \Stormpath\Authc\UsernamePasswordRequest(
			$username,
			$password
		);

		$account = null;

		try {
			$account = $this->spApplication->authenticateAccount( $authenticationRequest )->account;
		} catch (\Exception $e) {
		    if ( 7104 === $e->getCode() ) {
				$account = $this->attempt_normal_login( $username, $password );
			} else {
				return false;
			}
		}

		if ( $account instanceof WP_Error ) {
		    return $account;
		}

		if ( ! $account instanceof \Stormpath\Resource\Account ) {
		    return false;
		}

		$wpUser = $this->get_wp_user( $account );

		if ( 0 === $wpUser->ID ) {
			$wpUser = $this->create_wp_user( $account, $password );
		}

		return $this->login_wp_user( $wpUser );

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

	/**
	 * Get a WordPress user based on the account email.
	 *
	 * @param Account $account The stormpath account.
	 * @return WP_User
	 */
	private function get_wp_user( Account $account ) {
		return new WP_User( (new WP_User())->get_data_by( 'email', $account->email )->ID );

	}

	/**
	 * Create a WordPress User.
	 *
	 * @param Account $account  The Stormpath Account.
	 * @param string  $password The Password.
	 * @return WP_User
	 */
	private function create_wp_user( Account $account, $password ) {
		$userAccountInfo = [
			'user_email'    => $account->email,
			'user_login'    => $account->username,
			'first_name'    => $account->givenName,
			'last_name'     => $account->surname,
			'user_pass'     => wp_hash_password( wp_generate_password( 32 ) ),
		];

		$newUser = wp_insert_user( $userAccountInfo );

		return new WP_User( $newUser );
	}

	/**
	 * Force Login of WordPress User
	 *
	 * @param WP_User $wpUser The WordPress User.
	 * @return WP_User
	 */
	public function login_wp_user( $wpUser ) {
		return wp_set_current_user( $wpUser->ID );
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

		$accountObj = $this->spClient->getDataStore()->instantiate( Account::class, $account );

		$this->spApplication->createAccount( $accountObj );

		wp_set_password( wp_hash_password( wp_generate_password( 32 ) ), $wpUserId );

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

			$accountObj = $this->spClient->getDataStore()->instantiate( Account::class, $account );

			return $this->spApplication->createAccount( $accountObj );

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
		$accounts = $this->spApplication->accounts->setSearch( [ 'q' => $user->user_email ] );
		if ( $accounts->size > 0 ) {

			$account = $accounts->getIterator()->current();

			$account->password = $password;
			$account->save();

			$id = $user->ID;
			wp_set_password( wp_hash_password( wp_generate_password( 32 ) ), $id );
		}
	}
}
