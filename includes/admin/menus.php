<?php
/**
 * Stormpath WordPress Administration Menu Hooks
 *
 * @package Stormpath-WordPress
 */

namespace Stormpath\WordPress\Admin;


/**
 * Add the root level menu to the admin area.
 *
 * @invokes add_menu_page at 100
 * @return void
 */
function add_admin_menu_root() {

	add_menu_page(
		'Stormpath Configuration',
		'Stormpath',
		'create_users',
		'stormpath',
		__NAMESPACE__ . '\configuration_page',
		plugin_dir_url( __DIR__ ) . 'images/dashicon.png',
		100
	);
}

add_action( 'admin_menu', __NAMESPACE__ . '\add_admin_menu_root' );

