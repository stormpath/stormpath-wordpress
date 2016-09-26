<?php
/**
 * The Stormpath Settings Page
 *
 * @package Stormpath\WordPress
 * @author Stormpath <support@stormpath.com>
 */

if ( ! isset( $instance ) ) {
	die( 'Nope!' );
}


?>

<div class="wrap">
	<h1><?php esc_html_e( 'Stormpath Settings', 'stormpath' ); ?></h1>

	<form method="POST" action="options.php">
		<?php settings_fields( 'stormpath_options' ); ?>
		<?php do_settings_sections( 'stormpath_options' ); ?>

		<h2>Api Key Properties</h2>
		<p>The field below lets you add your api key properties file.  At Stormpath, we suggest storing your api keys in a apiKey.properties file in a secure location that your website can access.<br/>
		If you are on a hosting provider that does not let you do so, you can add your actual keys in the id and secret fields.  Be warned, this is a less secure way of storing your api keys.</p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Properties File</th>
				<td>
					<input type="text" name="stormpath_client_apikey_properties" class="regular-text" value="<?php echo esc_attr( get_option( 'stormpath_client_apikey_properties' ) ); ?>" />
					<p class="description">The absolute path of your apiKey.properties file. (eg. /var/www/mysite.com/.stormpath/apiKey.properties)</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">ApiKey ID</th>
				<td>
					<input type="text" name="stormpath_client_apikey_id" class="regular-text" value="<?php echo esc_attr( get_option( 'stormpath_client_apikey_id' ) ); ?>" />
					<p class="description">This will be stored in plain text in the database.</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">ApiKey Secret</th>
				<td>
					<input type="password" name="stormpath_client_apikey_secret" class="regular-text" value="<?php echo esc_attr( get_option( 'stormpath_client_apikey_secret' ) ); ?>" />
					<p class="description">This will be stored in plain text in the database.</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Application Href</th>
				<td>
					<input type="text" name="stormpath_application" class="regular-text" value="<?php echo esc_attr( get_option( 'stormpath_application' ) ); ?>" />
					<p class="description">The full Application Href</p>
				</td>
			</tr>
		</table>

		<?php submit_button( 'Update Options' ); ?>
	</form>



</div>

