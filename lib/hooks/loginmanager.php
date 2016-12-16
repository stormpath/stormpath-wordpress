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

namespace Stormpath\WordPress\Hooks;
use Stormpath\WordPress\Application;

/**
 * Class LoginManager
 *
 * @category PHP
 * @package Stormpath
 * @author Stormpath <support@stormpath.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link https://stormpath.com
 */
class LoginManager {

	/**
	 * Should we use ID Site for Login.
	 * ddd
	 *
	 * @var bool
	 */
	protected $useIdSite;

	/**
	 * LoginManager constructor.
	 */
	public function __construct() {
		$this->useIdSite = get_option( 'stormpath_id_site' );
	}

	/**
	 * Create a new instance of the Login Manager
	 *
	 * @return LoginManager
	 */
	public static function handle() {
		$manager = new self;

		if ( $manager->useIdSite ) {
			$manager->redirect_to_id_site();
		}

		return $manager;
	}


	/**
	 * Redirect to IdSite.
	 *
	 * @return void
	 */
	public function redirect_to_id_site() {
		$application = Application::get_instance();

		wp_redirect( $application->get_application()->createIdSiteUrl( [ 'callbackUri' => get_site_url() . '/stormpath/callback' ] ) );
		exit;
	}

	/**
	 * Logout from Id Site.
	 *
	 * @param null|string $state The state passed from the JWT.
	 * @return void
	 */
	public function logout( $state = null ) {
		$application = Application::get_instance();
		$properties = [ 'callbackUri' => get_site_url() . '/stormpath/callback', 'logout' => true ];

		if ( null !== $state ) {
			$properties['state'] = $state;
		}
		wp_redirect( $application->get_application()->createIdSiteUrl( $properties ) );
		exit;
	}
}
