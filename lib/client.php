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

use Stormpath\ClientBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Client for the Stormpath application.
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class Client {

	/**
	 * The singleton for Stormpath\WordPress\Client.
	 *
	 * @var null|\Stormpath\WordPress\Client
	 */
	protected static $instance = null;

	/**
	 * Client constructor.
	 *
	 * @param ApiKeys $apiKeys The api keys object.
	 */
	protected function __construct( ApiKeys $apiKeys ) {
		$this->apiKeys = $apiKeys;

		$this->stormpath_client = $this->build_stormpath_client();
	}

	/**
	 * Gets the current instance or makes a new one.
	 *
	 * @param ApiKeys $apiKeys The api keys object.
	 * @return Client
	 */
	public static function get_instance( ApiKeys $apiKeys ) {

		if ( null === self::$instance ) {
			self::$instance = new self( $apiKeys );
		}

		return self::$instance;
	}

	/**
	 * Build an instance of Stormpath Client.
	 *
	 * @return null|\Stormpath\Client
	 */
	private function build_stormpath_client() {
		$id = $this->apiKeys->get_id();
		$secret = $this->apiKeys->get_secret();

		if ( null === $id || null === $secret ) {
			return null;
		}

		$clientBuilder = new ClientBuilder();
		return $clientBuilder->setApiKeyProperties( "apiKey.id={$id}\napiKey.secret={$secret}" )
			->build();
	}

	/**
	 * Get the Stormpath Client.
	 *
	 * @return null|\Stormpath\Client
	 */
	public function get_client() {
		return $this->stormpath_client;
	}
}
