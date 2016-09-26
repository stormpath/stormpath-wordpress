<?php
/**
 * Stormpath WordPress Plugin Bootstrapper.
 *
 * @package Stormpath\WordPress
 */

namespace Stormpath\WordPress;

/**
 * Auto-loader for the Stormpath WordPress plugin.
 *
 * @param string $className The class name that has been called.
 *
 * @return void
 */
function autoload( $className ) {

	$class = strtolower( substr( $className, strlen( __NAMESPACE__ ) ) );

	$file   = sprintf( '%s%s.php', __DIR__, str_replace( '\\', '/', $class ) );

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
spl_autoload_register( __NAMESPACE__ . '\autoload' );

