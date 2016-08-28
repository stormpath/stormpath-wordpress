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
 * @package Stormpath
 */

namespace Stormpath;

/**
 * Auto-loader for the Stormpath WordPress plugin.
 *
 * @param string $className The class name that has been called.
 *
 * @return void
 */
function autoload( $className ) {

	$class = substr( $className, strlen( __NAMESPACE__ ) );

	$file   = sprintf( '%s/%s.php', __DIR__, str_replace( '\\', '/', $class ) );

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
spl_autoload_register( __NAMESPACE__ . 'autoload' );
