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
 * Class Error
 *
 * @category    Error
 * @package     Stormpath\WordPress\Notices
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 */
class Error extends Notices
{
	/**
	 * The message property for the error.
	 *
	 * @var string $message The message of the error
	 */
	protected $message = 'There was an unknown error from the Stormpath Plugin';

	/**
	 * The level of the error to be displayed.
	 *
	 * @var string $level The level of the error.
	 */
	protected $level = 'error';

	/**
	 * The dismissible property.
	 *
	 * @var bool $dismissible Should the error be dismissible
	 */
	protected $dismissible = false;


	/**
	 * Display an apiKey properties file invalid error.
	 *
	 * @return void
	 */
	protected function api_key_properties_file_invalid() {

		$this->message = 'The Stormpath Client could not be created, Please check to make sure your api keys are valid and readable by WordPress.';

	}

	/**
	 * Display an apiKeys could not be resolved error.
	 *
	 * @return void
	 */
	protected function api_keys_could_not_be_resolved() {

		$this->message = 'The Stormpath API Keys could not be resolved, please confirm they are correct.';

	}

	/**
	 * Display communication error.
	 *
	 * @return void
	 */
	protected function could_not_contact_stormpath() {
		$this->message = 'We could not communicate with Stormpath, Please confirm your Api Key\'s are correct.';
	}

	/**
	 * Display general error.
	 *
	 * @param string $message The message for the general error.
	 * @return void
	 */
	protected function general( $message ) {
	    $this->message = 'There was an error with Stormpath: ' . $message;
	}
}
