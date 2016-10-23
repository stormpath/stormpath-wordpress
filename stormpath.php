<?php
/**
 * Plugin Name: Stormpath
 * Plugin URI: https://stormpath.com
 * Description: Use Stormpath for your authentication.
 * Version: 1.0.0-develop
 * Author: Stormpath
 * Author URI: https://stormpath.com
 * Text Domain: stormpath-wordpress
 * Domain Path: /languages
 *
 * @package  Stormpath-WordPress
 */

namespace Stormpath\WordPress;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

define( 'STORMPATH_INTEGRATION',        'stormpath-wordpress' );
define( 'STORMPATH_VERSION',            '1.0.0-develop' );
define( 'STORMPATH_MIN_WP_VERSION',     '4.5.0' );
define( 'STORMPATH_MIN_PHP_VERSION',    '5.5.0' );
define( 'STORMPATH_BASEPATH',           dirname( __FILE__ ) );
define( 'STORMPATH_PLUGIN_ROOT_URL',    plugin_dir_url( __FILE__ ) );

require __DIR__ . '/vendor/autoload.php';

$stormpath = Stormpath::get_instance();

do_action( 'stormpath_pre_run', [ $stormpath ] );
$stormpath->run();

do_action( 'stormpath_post_run', [ $stormpath ] );
