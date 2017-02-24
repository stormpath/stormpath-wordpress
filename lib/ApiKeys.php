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

use Stormpath\Resource\ResourceError;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * ApiKey Management for Plugin
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class ApiKeys {

	/**
	 * The singleton for Stormpath\WordPress\ApiKeys.
	 *
	 * @var null|\Stormpath\WordPress\ApiKeys
	 */
	protected static $instance = null;

	/**
	 * The API Key Id.
	 *
	 * @var string|null
	 */
	protected $apiKeyId = null;

	/**
	 * The API Key Secret.
	 *
	 * @var string|null
	 */
	protected $apiKeySecret = null;

	/**
	 * ApiKeys constructor.
	 */
	protected function __construct() {
		$this->apiKeyId = ( defined( 'STORMPATH_CLIENT_APIKEY_ID' ) ) ? STORMPATH_CLIENT_APIKEY_ID : get_option( 'stormpath_client_apikey_id' );
		$this->apiKeySecret = ( defined( 'STORMPATH_CLIENT_APIKEY_SECRET' ) ) ? STORMPATH_CLIENT_APIKEY_SECRET : get_option( 'stormpath_client_apikey_secret' );
	}

	/**
	 * Gets the current instance or makes a new one.
	 *
	 * @return ApiKeys
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Tells if the api keys are valid by attempting to get the current tenant.
	 *
	 * @return bool
	 */
	public function api_keys_valid() {
		if ( empty( $this->apiKeyId ) || empty( $this->apiKeySecret ) ) {
			return false;
		}

		$client = Client::get_instance( $this );

		try {
			$client->get_client()->getCurrentTenant();
			return true;
		} catch ( ResourceError $re ) {
			return false;
		}

	}

	/**
	 * The API Key Id.
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->apiKeyId;
	}

	/**
	 * The API Key Secret.
	 *
	 * @return string|null
	 */
	public function get_secret() {
		return $this->apiKeySecret;
	}
}
