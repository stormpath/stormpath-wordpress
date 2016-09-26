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
 * Class Warning
 *
 * @category    Warning
 * @package     Stormpath\WordPress\Notices
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
class Warning extends Notices
{
	/**
	 * The message property for the notice.
	 *
	 * @var string $message The message of the notice.
	 */
	protected $message = 'Something you did with Stormpath just triggered this warning Message.';

	/**
	 * The level of the notice to be displayed.
	 *
	 * @var string $level The level of the notice.
	 */
	protected $level = 'warning';

	/**
	 * The dismissible property.
	 *
	 * @var bool $dismissible Should the notice be dismissible.
	 */
	protected $dismissible = true;

	/**
	 * Display an apiKey properties file should be used.
	 *
	 * @return void
	 */
	protected function api_key_properties_file_should_be_used() {
		$this->message = 'Providing the API Key ID and Secret is a less secure way.  You should provide the file instead.';
	}
}
