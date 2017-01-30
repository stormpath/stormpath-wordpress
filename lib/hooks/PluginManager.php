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
use Stormpath\ApiKey;
use Stormpath\WordPress\ApiKeys;
use Stormpath\WordPress\Application;

/**
 * PluginManager
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @subpackage  Stormpath\WordPress\Admin
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class PluginManager {

	/**
	 * PluginManager constructor.
	 */
	public function __construct() {

	}

	/**
	 * The activation hook.
	 *
	 * @return void
	 */
	public static function activate() {
		$datetime = (new \DateTime())->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'Y-m-d\TH:i:s\Z' );
		update_option( 'stormpath_activated', $datetime, false );
		$apiKeys = ApiKeys::get_instance();

		if ( ! $apiKeys->api_keys_valid() || ! Application::get_instance()->get_application() ) {
			update_option( 'stormpath_installed', false, false );
			add_action( 'activated_plugin', [ __CLASS__, 'plugin_activated' ] );
		} else {
			update_option( 'stormpath_installed', true, false );
		}

		flush_rewrite_rules();
	}

	/**
	 * Deactivate Hook.
	 *
	 * @return void
	 */
	public static function deactivate() {
		update_option( 'stormpath_activated', false, false );
		update_option( 'stormpath_installed', false, false );
		flush_rewrite_rules();
	}

	/**
	 * Uninstall Hook.
	 *
	 * @return void
	 */
	public static function uninstall() {

	}

	/**
	 * Plugin was activated.
	 *
	 * @return void
	 */
	public static function plugin_activated() {
		flush_rewrite_rules();

		wp_redirect( admin_url( '/users.php?page=stormpath&install=1' ) );
		exit();

	}

	/**
	 * Add links to the plugin page item.
	 *
	 * @param array $links The current set of links for the plugin item.
	 * @return array
	 */
	public static function add_action_links( $links ) {
		$extraLinks = array(
			'<a href="' . admin_url( '/users.php?page=stormpath' ) . '">Settings</a>',
			'<a href="https://api.stormpath.com/register" target="_blank">Register for Stormpath</a>',
			'<a href="https://support.stormpath.com" target="_blank">Support</a>',
		);
		unset( $links['edit'] );
		return array_merge( $links, $extraLinks );
	}
}
