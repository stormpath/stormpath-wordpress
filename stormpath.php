<?php
/**
 * Plugin Name: Stormpath
 * Plugin URI: https://stormpath.com
 * Description: Use Stormpath for your authentication
 * Version: 0.0.0-alpha
 * Author: Stormpath
 * Author URI: https://stormpath.com
 * Text Domain: stormpath-wordpress
 * Domain Path: /languages
 *
 * @package  Stormpath-WordPress
 */

namespace Stormpath\WordPress;

define( 'STORMPATH_INTEGRATION',    'stormpath-wordpress' );
define( 'STORMPATH_VERSION',        '0.0.0-alpha' );


require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin.php';
