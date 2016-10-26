<?php
/**
 * Plugin Name: Stormpath
 * Plugin URI: https://stormpath.com
 * Description: Use Stormpath for your authentication
 * Version: 0.1.6
 * Author: Stormpath
 * Author URI: https://stormpath.com
 * Text Domain: stormpath-wordpress
 * Domain Path: /languages
 *
 * @package  Stormpath-WordPress
 */

namespace Stormpath\WordPress;

define( 'STORMPATH_INTEGRATION',    'stormpath-wordpress' );
define( 'STORMPATH_VERSION',        '0.1.6' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';
require_once dirname( __FILE__ ) . '/includes/bootstrap.php';
require_once dirname( __FILE__ ) . '/includes/stormpath.php';
require_once dirname( __FILE__ ) . '/includes/settings.php';

/**
 * Bootstrap the plugin
 *
 * @return void
 */
function init() {
	new Stormpath;

	load_plugin_textdomain( 'stormpath', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\init' );
