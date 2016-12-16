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
?>
<?php if ( \Stormpath\WordPress\ApiKeys::get_instance()->api_keys_valid() && '' != get_option( 'stormpath_application' ) ) : ?>
	<?php update_option( 'stormpath_installed', true, false ); ?>
<?php else: ?>
	<?php update_option( 'stormpath_installed', false, false ); ?>
<?php endif; ?>


<?php $nonce = wp_create_nonce( "stormpath-settings-nonce" ); ?>
<div class="stormpath-settings-wrap" id="stormpath-settings" data-nonce="<?php esc_html_e($nonce); ?>">

	<section class="stormpath-page-header">
		<img class="stormpath-header-logo" src="<?php esc_html_e( STORMPATH_PLUGIN_ROOT_URL . 'assets/images/logo.png' ); ?>" />
	</section>



	<div class="row">

		<div class="col-sm-6">

			<div class="stormpath-container">
				<div class="stormpath-container-header">
					<h3>Settings</h3>
				</div>
				<div class="stormpath-container-body">
					<form method="POST" action="options.php" class="form-horizontal">
						<?php settings_fields( 'stormpath-settings' ); ?>
						<?php do_settings_sections( 'stormpath-settings' ); ?>

						<?php include STORMPATH_BASEPATH .'/assets/templates/_partials/apiKey_form_fields.php'; ?>

						<?php include STORMPATH_BASEPATH .'/assets/templates/_partials/application_selection.php'; ?>

						<?php submit_button( 'Update Settings', 'button-stormpath', 'submit', true, [ 'class' => 'stormpath-submit' ] ); ?>
					</form>

				</div>
			</div>

			<div class="stormpath-container">
				<div class="stormpath-container-header">
					<h3>Sync Users</h3>
				</div>
				<div class="stormpath-container-body">
					<p>When you initially set up Stormpath, it is recommended that you sync users from WordPress over to Stormpath.  This will allow your users to log into your site.  However, due to the way that WordPress stores their passwords, we are not able to import them into Stormpath. This means that you will have to require your users to reset their passwords after the sync is complete.</p>
					<button>Sync Users Now</button>

					<p>You can set syncing on the WordPress cron job.  This will make sure all data is the same for your user data is the same from Stormpath inside of your WordPress install and any new users created in WordPress are in Stormpath.  Typically you will want to keep this enabled.</p>
					<span class="stormpath-enable-toggle">
					<?php $checked = get_option( 'stormpath_id_site', true ) ? 'checked' : ''; ?>
					<input type="checkbox" <?php esc_html_e( $checked ); ?> data-toggle="toggle" class="stormpath-option-toggle enable-user-sync" data-on="Enabled" data-off="Disabled"">
					</span>

				</div>
			</div>
		</div>


		<div class="col-sm-6" id="stormpath-id-site">
			<div class="stormpath-container">
				<div class="stormpath-container-header">
					<h3>ID Site</h3>
					<span class="stormpath-enable-toggle">
							<?php $checked = get_option( 'stormpath_id_site', false ) ? 'checked' : ''; ?>
						<input type="checkbox" data-toggle="toggle" <?php esc_html_e( $checked ); ?> class="stormpath-option-toggle enable-id-site" data-on="Enabled" data-off="Disabled"">
					</span>
				</div>

				<div class="stormpath-container-body clearfix">
					<p>Stormpath Identity Portal (ID Site) gives your users seamless access to any of your applications without reauthenticating. Our ID Site feature abstracts authentication and authorization logic to a subdomain hosted by Stormpath.</p>
					<div class="stormpath-id-site-settings" style="display:none;">

						<form class="form-horizontal" id="id-site-settings">

							<?php settings_fields( 'stormpath-id-site-settings' ); ?>
							<?php do_settings_sections( 'stormpath-id-site-settings' ); ?>

						<div class="form-group">
							<label for="stormpath_id_site_domain_name" class="col-sm-4 control-label">Domain Name</label>
							<div class="col-sm-8">
								<input
									type="text"
									class="form-control"
									id="stormpath_id_site_domain_name"
									name="stormpath_id_site_domain_name"
								    disabled="disabled"
								>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_ssl_public_chain" class="col-sm-4 control-label">SSL Public Certificate / Chain</label>
							<div class="col-sm-8">
								<textarea
									class="form-control"
									id="stormpath_id_site_ssl_public_chain"
									name="stormpath_id_site_ssl_public_chain"
								></textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_ssl_private" class="col-sm-4 control-label">SSL Private Key</label>
							<div class="col-sm-8">
							<textarea
								class="form-control"
								id="stormpath_id_site_ssl_private"
								name="stormpath_id_site_ssl_private"
							></textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_authorized_javascript_origin" class="col-sm-4 control-label">Authorized Javascript Origin URLs</label>
							<div class="col-sm-8">
								<textarea
									type="text"
									class="form-control"
									id="stormpath_id_site_authorized_javascript_origin"
									name="stormpath_id_site_authorized_javascript_origin"
								></textarea>
								<span id="helpBlock" class="help-block">A list of URLs where the ID Site is hosted, use for local development or custom domain names. One URL per line.</span>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_authorized_redirect_urls" class="col-sm-4 control-label">Authorized Redirect URLs</label>
							<div class="col-sm-8">
							<textarea
								type="text"
								class="form-control"
								id="stormpath_id_site_authorized_redirect_urls"
								name="stormpath_id_site_authorized_redirect_urls"
							></textarea>
								<span id="helpBlock" class="help-block">A list of URLs that the user can be sent to after they login or register at the ID Site. One URL per line.</span>
								<span id="helpBlock" class="help-block">Please make sure <?php echo( get_site_url() . '/stormpath/callback' ); ?> is added here, otherwise ID Site will not work.</span>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_session_idle_timeout" class="col-sm-4 control-label">Session Idle Timeout</label>
							<div class="col-sm-8">
								<input
									type="text"
									class="form-control"
									id="stormpath_id_site_session_idle_timeout"
									name="stormpath_id_site_session_idle_timeout"
								>
								<span id="helpBlock" class="help-block">Time In ISO8601 Duration.</span>
							</div>
						</div>

						<div class="form-group">
							<label for="stormpath_id_site_session_max_age" class="col-sm-4 control-label">Session Max Age</label>
							<div class="col-sm-8">
								<input
									type="text"
									class="form-control"
									id="stormpath_id_site_session_max_age"
									name="stormpath_id_site_session_max_age"
								>
								<span id="helpBlock" class="help-block">Time In ISO8601 Duration.</span>
							</div>
						</div>

							<?php submit_button( 'Update Id Site', 'button-stormpath', 'submit', true, [ 'id' => 'update-id-site-settings' ] ); ?>
							<div class="stormpath-error-text"></div>
						</form>

						<span class="dashicons dashicons-yes stormpath-updated"></span>
						<span class="dashicons dashicons-no stormpath-error"></span>

					</div>
				</div>
			</div>
		</div>

	</div>



</div>




