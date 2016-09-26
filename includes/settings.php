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

/**
 * Class Settings
 *
 * @category    Settings
 * @package     Stormpath\WordPress
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
class Settings {

	/**
	 * Current instance of Stormpath\WordPress\Stormpath
	 *
	 * @var Stormpath $stormpathInstance The current instance of the Stormpath object.
	 */
	protected $stormpathInstance;

	/**
	 * Settings constructor.
	 *
	 * @param Stormpath $instance The current instance of the Stormpath object.
	 */
	public function __construct( Stormpath $instance ) {
		$this->stormpathInstance    = $instance;
	}

	/**
	 * Adds the Stormpath submenu to the options menu.
	 *
	 * @return void
	 */
	public function add_options_page() {

		add_options_page(
			'Stormpath Configuration',
			'Stormpath',
			'create_users',
			'stormpath',
			[ $this, 'settings_page' ]
		);
	}

	/**
	 * Generate the Settings > Stormpath page
	 *
	 * @return void
	 */
	public function settings_page() {

		$instance = $this->stormpathInstance;

		require_once dirname( __FILE__ ) . '/admin/settings.php';
	}

	/**
	 * Registers the Options for Stormpath
	 *
	 * @return void
	 */
	public function register_options() {
		register_setting( 'stormpath_options', 'stormpath_client_apikey_properties' );
		register_setting( 'stormpath_options', 'stormpath_client_apikey_id' );
		register_setting( 'stormpath_options', 'stormpath_client_apikey_secret' );
		register_setting( 'stormpath_options', 'stormpath_application' );
	}
}
