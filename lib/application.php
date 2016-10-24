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
 * Application for the Stormpath application.
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class Application {

	/**
	 * The singleton for Stormpath\WordPress\Application.
	 *
	 * @var null|\Stormpath\WordPress\Application
	 */
	protected static $instance = null;

	/**
	 * Application constructor.
	 */
	protected function __construct() {
		$this->client = Client::get_instance( ApiKeys::get_instance() );
	}

	/**
	 * Gets the current instance or makes a new one.
	 *
	 * @return Application
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Gets all applications from the transient.
	 *
	 * @return mixed
	 */
	public function get_application_list() {
		$list = get_transient( 'stormpath_application_list' );

		if ( ! $list ) {
			$list = $this->client->get_client()->getCurrentTenant()->applications;
			set_transient( 'stormpath_application_list', $list, 3600 );
		}

		return $list;

	}
}
