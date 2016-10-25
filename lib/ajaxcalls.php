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

namespace Stormpath\WordPress;

use Stormpath\Resource\ResourceError;

/**
 * Ajax Calls
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class AjaxCalls {

	/**
	 * AjaxCalls constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_sync_roles', [ $this, 'sync_roles' ] );
		add_action( 'wp_ajax_nopriv_sync_roles', [ $this, 'sync_roles' ] );

		add_action( 'wp_ajax_set_stormpath_option', [ $this, 'update_stormpath_option' ] );

		add_action( 'wp_ajax_stormpath_get_id_site_settings', [ $this, 'get_id_site_options' ] );
		add_action( 'wp_ajax_stormpath_update_id_site_settings', [ $this, 'update_id_site_options' ] );
		add_action( 'wp_ajax_nopriv_stormpath_update_id_site_settings', [ $this, 'update_id_site_options' ] );
	}

	/**
	 * Handle setting up the ajax calls.
	 *
	 * @return AjaxCalls
	 */
	public static function handle() {
		return new self;
	}

	/**
	 * Update an option via Ajax.
	 *
	 * @return void
	 */
	public function update_stormpath_option() {
		if ( ! isset( $_REQUEST['_nonce'] ) || ! isset( $_POST['option'] ) || ! isset( $_POST['value'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'stormpath-settings-nonce' ) ) {
			wp_die( 'nonce not valid' );
		}

		$option = sanitize_text_field( wp_unslash( $_POST['option'] ) );
		$value = sanitize_text_field( wp_unslash( $_POST['value'] ) );

		update_option( $option, $value );
		wp_die();
	}

	/**
	 * Synchronize roles from WordPress to Stormpath
	 *
	 * @return void
	 */
	public function sync_roles() {
		// This is how you get access to the database.
		// Get all WordPress roles.
		$roles = wp_roles();

		// Get all Stormpath Groups.
		$groups = Application::get_instance()->get_application()->getGroups();
		// Add Groups to Stormpath and update caps.
		foreach ( $roles->roles as $role => $details ) {
			$role_name = $role;
			$caps = $details['capabilities'];
			$group = null;
			$groupSearch = null;

			$groupSearch = $groups->setSearch( [ 'name' => $role_name ] );

			$addGroup = 0 === $groupSearch->getSize();

			if ( $addGroup ) {
				$group = \Stormpath\Resource\Group::instantiate([
					'name' => $role_name,
					'description' => 'Imported from WordPress ' . get_bloginfo( 'name' ),
				]);

				$group = Application::get_instance()->get_application()->createGroup( $group );
			} else {
				$group = Application::get_instance()->get_application()->getGroups( [ 'name' => $role_name ] )->getIterator()->current();
			}

			// Update the custom data for all caps.
			$customData = $group->getCustomData();
			$customData->capabilities = $caps;
			$customData->save();
		}

		wp_die();
	}

	/**
	 * Get all the ID Site options.
	 *
	 * @return void
	 */
	public function get_id_site_options() {
		$idSiteUrl = Client::get_instance( ApiKeys::get_instance() )->get_client()->getCurrentTenant()->getProperty( 'idSites' )->href;

		$idSite = Client::get_instance( ApiKeys::get_instance() )->get_client()->getDataStore()->getResource( $idSiteUrl, \Stormpath\Resource\Resource::class );

		wp_send_json( $idSite->getProperty( 'items' )[0] );

		wp_die();
	}

	/**
	 * Update the ID Site Options
	 *
	 * @return void
	 */
	public function update_id_site_options() {

		if ( ! isset( $_REQUEST['_nonce'] ) || ! isset( $_POST['data'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'stormpath-settings-nonce' ) ) {
			wp_die( 'nonce not valid' );
		}

		$idSitesUrl = Client::get_instance( ApiKeys::get_instance() )->get_client()->getCurrentTenant()->getProperty( 'idSites' )->href;

		$idSites = Client::get_instance( ApiKeys::get_instance() )->get_client()->getDataStore()->getResource( $idSitesUrl, \Stormpath\Resource\Resource::class );

		$idSiteUrl = $idSites->getProperty( 'items' )[0]->href;

		$idSite = Client::get_instance( ApiKeys::get_instance() )->get_client()->getDataStore()->getResource( $idSiteUrl, IdSiteSettings::class );

		if ( isset( $_POST['data'] ) ) {
			$data = sanitize_text_field( wp_unslash( $_POST['data'] ) );
		}

		$origins = trim( $data['authorizedOrigins'] );
		$originsArr = explode( "\n", $origins );
		$originsArr = array_filter( $originsArr, 'trim' );

		$redirects = trim( $data['redirectUris'] );
		$redirectsArr = explode( "\n", $redirects );
		$redirectsArr = array_filter( $redirectsArr, 'trim' );

		$idSite->publicCert = $data['sslPublic'];
		$idSite->privateCert = $data['sslPrivate'];
		$idSite->origin = $originsArr;
		$idSite->redirectUri = $redirectsArr;
		$idSite->tti = $data['tti'];
		$idSite->ttl = $data['ttl'];

		try {
			$idSite->save();
			wp_send_json_success();
		} catch (ResourceError $re) {
			wp_send_json_error( $re->getMessage() );
		}

		wp_die();
	}
}

/**
 * Helper for IdSiteSettings
 *
 * @category    Plugin
 * @package     Stormpath\WordPress
 * @subpackage  Stormpath\WordPress
 * @author      Brian Retterer <brian@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com/
 * @since       1.0.0
 */
class IdSiteSettings extends \Stormpath\Resource\InstanceResource {

	const PUBLIC_CERT = 'tlsPublicCert';
	const PRIVATE_CERT = 'tlsPrivateKey';
	const ORIGIN = 'authorizedOriginUris';
	const REDIRECT_URI = 'authorizedRedirectUris';
	const TTI = 'sessionTti';
	const TTL = 'sessionTtl';

	/**
	 * Sets the publicCert property.
	 *
	 * @param string $publicCert The publicCert of the object.
	 * @return self
	 */
	public function setPublicCert( $publicCert ) {
	    $this->setProperty( self::PUBLIC_CERT, $publicCert );

	    return $this;
	}

	/**
	 * Sets the privateCert property.
	 *
	 * @param string $privateCert The privateCert of the object.
	 * @return self
	 */
	public function setPrivateCert( $privateCert ) {
	    $this->setProperty( self::PRIVATE_CERT, $privateCert );

	    return $this;
	}

	/**
	 * Sets the origin property.
	 *
	 * @param string $origin The origin of the object.
	 * @return self
	 */
	public function setOrigin( $origin ) {
	    $this->setProperty( self::ORIGIN, $origin );

	    return $this;
	}

	/**
	 * Sets the redirectUri property.
	 *
	 * @param string $redirectUri The redirectUri of the object.
	 * @return self
	 */
	public function setRedirectUri( $redirectUri ) {
	    $this->setProperty( self::REDIRECT_URI, $redirectUri );

	    return $this;
	}

	/**
	 * Sets the ttl property.
	 *
	 * @param string $ttl The ttl of the object.
	 * @return self
	 */
	public function setTtl( $ttl ) {
	    $this->setProperty( self::TTL, $ttl );

	    return $this;
	}

	/**
	 * Sets the tti property.
	 *
	 * @param string $tti The tti of the object.
	 * @return self
	 */
	public function setTti( $tti ) {
	    $this->setProperty( self::TTI, $tti );

	    return $this;
	}
}
