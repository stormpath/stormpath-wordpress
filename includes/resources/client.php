<?php
/**
 * Stormpath WordPress Client Resource
 *
 * @package     Stormpath\WordPress
 */

namespace Stormpath\WordPress\Resources;

/**
 * The Client Resource for Stormpath
 *
 * @category    Class
 * @package     Stormpath\WordPress
 * @author      Stormpath <support@stormpath.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link        https://stormpath.com
 */
class Client {


	/**
	 * Storage location for the current instance of the Client Resource class.
	 *
	 * @var self The Current instance of Client.
	 */
	protected static $instance;

	/**
	 * Create a new instance of Client or pull from singleton.
	 *
	 * @return self
	 */
	public static function init() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}


	/**
	 * Get the current instance of Client.
	 *
	 * @return Client|null
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Clear the current client instance.
	 *
	 * @return void
	 */
	public static function tear_down() {
		self::$instance = null;
	}


	/**
	 * Client constructor.
	 */
	public function __construct() {
		$this->resolve_api_keys();
	}

	/**
	 * Gets the api key options and sets them up for use.
	 *
	 * @return void
	 */
	private function resolve_api_keys() {

		$id = null;
		$secret = null;

		list($id, $secret) = $this->parse_api_key_file();
	}

	/**
	 * Parse the stormpath.yml file for api keys.
	 *
	 * @return array An array in the form of [id, secret] if found, or [null, null] if not]
	 */
	private function parse_api_key_file() {

		if ( get_option( 'stormpath_client_apikey_file' )
			&& is_file( get_option( 'stormpath_client_apikey_file' ) ) ) {
			$parsed = yaml_parse_file( get_option( 'stormpath_client_apikey_file' ) );
			return [ $parsed['apiKey.id'], $parsed['apiKey.secret'] ];
		}

		add_action( 'admin_notices', function() {
			$class = 'notice notice-warning is-dismissible';
			$message = __( 'We suggest that you use the <b>API Key file</b> for your API Keys.', 'stormpath-wordpress' );

			echo '<div class="notice notice-warning is-dismissible"><p>' . wp_kses( $message, array( 'b' => 'keep' ) ) . '</p></div>';

		});

		return [ null, null ];

	}
}
