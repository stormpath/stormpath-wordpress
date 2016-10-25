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

use Stormpath\WordPress\Hooks\PluginManager;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Main hub for the Stormpath WordPress plugin
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class Stormpath {

	/**
	 * The singleton for Stormpath\WordPress\Stormpath.
	 *
	 * @var null|\Stormpath\WordPress\Stormpath
	 */
	protected static $instance = null;

	/**
	 * Stormpath constructor.
	 */
	protected function __construct() {
		$this->register_hook_callbacks();
	}

	/**
	 * Gets the current instance or makes a new one.
	 *
	 * @return Stormpath
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Run the Stormpath Plugin.
	 *
	 * @return void
	 */
	public function run() {

		AjaxCalls::handle();

	}

	/**
	 * Register all hook callbacks.
	 *
	 * @return void
	 */
	public function register_hook_callbacks() {
		register_activation_hook( STORMPATH_BASEFILE, [ PluginManager::class, 'activate' ] );
		register_deactivation_hook( STORMPATH_BASEFILE, [ PluginManager::class, 'deactivate' ] );
		register_uninstall_hook( STORMPATH_BASEFILE, [ PluginManager::class, 'uninstall' ] );

		add_filter( 'plugin_action_links_' . plugin_basename( STORMPATH_BASEFILE ), [ PluginManager::class, 'add_action_links' ] );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_resources' ) );
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );

	}

	/**
	 * Adds the admin user menu item.
	 *
	 * @return void
	 */
	public function admin_menus() {
		add_users_page( 'Stormpath', 'Stormpath', 'manage_options', 'stormpath', function() { Stormpath::view( 'stormpath-settings' ); } );
	}

	/**
	 * Loads the admin resources
	 *
	 * @return void
	 */
	public function load_admin_resources() {
		wp_register_script(
			'stormpath-admin-script',
			STORMPATH_PLUGIN_ROOT_URL . 'assets/js/stormpath-admin.js',
			[ 'jquery', 'backbone' ],
			STORMPATH_VERSION,
			'all'
		);

		wp_register_style(
			'stormpath-admin-style',
			STORMPATH_PLUGIN_ROOT_URL . 'assets/css/stormpath-admin.css',
			[],
			STORMPATH_VERSION,
			'all'
		);

		wp_register_style(
			'stormpath-admin-bootstrap',
			STORMPATH_PLUGIN_ROOT_URL . '/assets/css/bootstrap.min.css',
			[ 'stormpath-admin-style' ],
			STORMPATH_VERSION,
			'all'
		);

		wp_enqueue_style( 'stormpath-admin-style' );
		wp_enqueue_style( 'stormpath-admin-bootstrap' );
		wp_enqueue_script( 'stormpath-admin-script' );
	}

	/**
	 * Initialization admin  for the Plugin
	 *
	 * @return void
	 */
	public function admin_init() {
		$this->register_options();
	}

	/**
	 * Display a view from the assets/templates directory.
	 *
	 * @param string $name the name of the view you want to include.
	 * @throws \InvalidArgumentException Invalid path to the view.
	 * @return void
	 */
	public static function view( $name ) {
		$name = str_replace( '.', DIRECTORY_SEPARATOR, $name );
		$path = STORMPATH_BASEPATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $name . '.php';

		if ( ! file_exists( $path ) ) {
			throw new \InvalidArgumentException( 'This is not the view you are looking for!' );
		}
		include $path;
	}

	/**
	 * Setup the settings that this plugin has access to.
	 *
	 * @return void
	 */
	public function register_options() {
		register_setting( 'stormpath-settings', 'stormpath_client_apikey_id' );
		register_setting( 'stormpath-settings', 'stormpath_client_apikey_secret' );
		register_setting( 'stormpath-settings', 'stormpath_application' );
		register_setting( 'stormpath-settings', 'stormpath_powered_by' );
	}
}
