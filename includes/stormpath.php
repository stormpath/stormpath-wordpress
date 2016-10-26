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
 * @package Stormpath\WordPress
 */

namespace Stormpath\WordPress;
use Stormpath\Client;
use Stormpath\Resource\Application;
use Stormpath\WordPress\Notices\Error;
use Stormpath\WordPress\Notices\Success;
use Stormpath\WordPress\Notices\Warning;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Stormpath
 *
 * @category    Stormpath
 * @package     Stormpath\WordPress
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
class Stormpath {

	/**
	 * The Stormpath Client.
	 *
	 * @var \Stormpath\Client
	 */
	private $client;

	/**
	 * The Stormpath Applicaiton.
	 *
	 * @var \Stormpath\Resource\Application
	 */
	private $application;

	/**
	 * The Authenticate Class
	 *
	 * @var Authenticate
	 */
	private $authenticate;

	/**
	 * Stormpath constructor.
	 */
	public function __construct() {
		$this->settings     = new Settings( $this );

		$this->add_hooks();

		$this->client = $this->create_client();

		if ( $this->client ) {
			$this->application = $this->application();
		}

		if ( $this->application ) {
			$this->replace_auth();
		}

	}

	/**
	 * Register Hooks in WordPress
	 *
	 * @return void
	 */
	private function add_hooks() {
		add_action( 'admin_init', [ $this->settings, 'register_options' ] );
		add_action( 'stormpath_admin_error', [ (new Error), 'display' ], 10, 2 );
		add_action( 'stormpath_admin_warning', [ (new Warning), 'display' ], 10, 2 );
		add_action( 'stormpath_admin_success', [ (new Success), 'display' ], 10, 2 );
		add_action( 'admin_menu', [ $this->settings, 'add_options_page' ] );

	}

	/**
	 * Replace the core authentication
	 *
	 * @return void
	 */
	public function replace_auth() {
		if ( null !== $this->client ) {

			$this->authenticate = new Authenticate( $this->application, $this->client );

			add_action( 'user_register', [ $this->authenticate, 'user_registered' ], 10, 1 );
			add_action( 'profile_update', [ $this->authenticate, 'profile_update' ], 10, 2 );
			add_action( 'after_password_reset', [ $this->authenticate, 'password_changed' ], 10, 2 );
			add_filter( 'authenticate', [ $this->authenticate, 'authenticate' ], 10, 3 );
			add_filter( 'login_errors', [ $this, 'login_errors' ], 10, 1 );
		} else {
			do_action( 'stormpath_admin_error', 'could_not_contact_stormpath' );
		}
	}

	/**
	 * Override the login errors.
	 *
	 * @param \WP_Error $errors The wp_error object.
	 * @return string
	 */
	public function login_errors( $errors ) {
		global $errors;
		$err_codes = $errors->get_error_codes();

		$error = $errors->get_error_message();

		if ( in_array( 'invalid_username', $err_codes ) ) {
			$error = '<strong>ERROR</strong>: Invalid username or password.';
		}

		if ( in_array( 'invalid_email', $err_codes ) ) {
			$error = '<strong>ERROR</strong>: Invalid username or password.';
		}

		if ( in_array( 'incorrect_password', $err_codes ) ) {
			$error = '<strong>ERROR</strong>: Invalid username or password.';
		}

		if ( in_array( 'authentication_failed', $err_codes ) ) {
			$error = '<strong>ERROR</strong>: Invalid username or password.';
		}

		if ( in_array( 'stormpath_error', $err_codes ) ) {
			$error = '<strong>ERROR</strong>: There was an error logging you in. Please let the administrator know you received a ' . $errors->get_error_data()['code'] . ' code during login.';
		}

		return $error;
	}

	/**
	 * Creates a new Stormpath Client
	 *
	 * @return null|Client
	 */
	private function create_client() {
		list( $id, $secret ) = $this->resolve_api_keys();

		if ( null === $id || null === $secret ) {
			do_action( 'stormpath_admin_error', 'api_keys_could_not_be_resolved' );
			return null;
		}

		$builder = new \Stormpath\ClientBuilder();
		$client = $builder->setApiKeyProperties( "apiKey.id={$id}\napiKey.secret={$secret}" )
			->setIntegration( STORMPATH_INTEGRATION . '/' . STORMPATH_VERSION . ' WordPress/' . get_bloginfo( 'version' ) )
			->build();
		$client->getInstance();
		return $client;

	}

	/**
	 * Resolves the API Keys from direct properties, then from properties file.
	 *
	 * @return array
	 */
	private function resolve_api_keys() {
		$id = null;
		$secret = null;
		$properties = get_option( 'stormpath_client_apikey_properties', null );
		$apiKeyId = get_option( 'stormpath_client_apikey_id', null );
		$apiKeySecret = get_option( 'stormpath_client_apikey_secret', null );

		if ( ! empty( $properties ) ) {
			$file = $properties;

			if ( false === is_file( $file ) && false === is_readable( $file ) ) {
				do_action( 'stormpath_admin_error', 'api_key_properties_file_invalid' );
				return [];
			}

			$content = fopen( $file, 'r' );
			$content = explode( "\n", fread( $content, 1024 ) );
			$array = [];
			foreach ( $content as $line ) {
				if ( '' === $line ) { continue;
				}
				$parts = explode( ' = ', $line );
				if ( count( $parts ) ) {
					$array[ $parts[0] ] = $parts[1];
				}
			}

			if ( ( ( count( $array ) !== 2 ) || ( ( ! isset( $array['apiKey.id'] )) && ( ! isset( $array['apiKey.secret'] )) ) ) &&
				( empty( $apiKeyId ) || empty( $apiKeySecret ) ) ) {
				do_action( 'stormpath_admin_error', 'api_key_properties_file_invalid' );
			}

			$id = $array['apiKey.id'];
			$secret = $array['apiKey.secret'];
		}

		if ( ! empty( $apiKeyId )  && ! empty( $apiKeySecret ) ) {

			do_action( 'stormpath_admin_warning', 'api_key_properties_file_should_be_used' );

			$id = $apiKeyId;
			$secret = $apiKeySecret;
		}

		return [ $id, $secret ];
	}

	/**
	 * Get the Stormpath application.
	 *
	 * @return bool|Application
	 */
	public function application() {
		if ( $this->client ) {
			try {
				return $this->client->getDataStore()->getResource( get_option( 'stormpath_application' ), \Stormpath\Resource\Application::class );
			} catch (\Exception $e) {
				do_action( 'stormpath_admin_error', 'general', $e->getMessage() );
				return false;
			}
		}
	}
}
