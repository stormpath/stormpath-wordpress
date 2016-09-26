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

namespace Stormpath\WordPress\Notices;

/**
 * Class Notices
 *
 * @category    Notices
 * @package     Stormpath\WordPress\Notices
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
abstract class Notices
{
	/**
	 * The message property for the notice.
	 *
	 * @var string $message The message of the notice.
	 */
	protected $message = 'There was an unknown notice from the Stormpath Plugin';

	/**
	 * The level of the notice to be displayed.
	 *
	 * @var string $level The level of the notice.
	 */
	protected $level = 'notice';

	/**
	 * The dismissible property.
	 *
	 * @var bool $dismissible Should the notice be dismissible.
	 */
	protected $dismissible = false;


	/**
	 * Adds the action to admin_notices to display the notice.
	 *
	 * @param string      $method  The name of the method that is being displayed.
	 * @param string|null $message The body of the notice.
	 * @return void
	 */
	public function display( $method, $message = null ) {
		add_action('admin_notices', function() use ( $method, $message ) {
			$this->handle( $method, $message );
		});
	}

	/**
	 * Handle the output to show the notice.
	 *
	 * @param string      $method  The name of the method that is being displayed.
	 * @param string|null $message The body of the notice.
	 * @return void
	 */
	public function handle( $method, $message ) {

		$this->{$method}($message);
		$class = 'notice notice-'.$this->level;
		if ( $this->dismissible ) {
			$class = $class . ' is-dismissible';
		}

		$message = wp_sprintf( __( '<div class="%s"><p>%s</p></div>', 'package-wordpress' ), $class, $this->message );

		print wp_kses( $message, [
			'div' => [ 'class' => [] ],
			'p' => [],
			'b' => [],
		]);

	}
}
