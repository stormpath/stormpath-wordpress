<?php
/**
 * Plugin Name: Stormpath
 * Plugin URI: https://stormpath.com
 * Description: Use Stormpath for your authentication
 * Version: 0.0.0-alpha
 * Author: Stormpath
 * Author URI: https://stormpath.com
 * Text Domain: stormpath
 * Domain Path: /languages
 *
 * @package  Stormpath
 */

namespace Stormpath;

define( 'STORMPATH_INTEGRATION',    'stormpath-wordpress' );
define( 'STORMPATH_VERSION',        '0.0.0-alpha' );

require_once __DIR__ . '/includes/bootstrap.php';
