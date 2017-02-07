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

			<div class="stormpath-container" >
				<div class="stormpath-container-header">
					<h3>Cache Settings</h3>
				</div>
				<div class="stormpath-container-body">
					<form class="form-horizontal" id="stormpath-cache-settings">

						<?php settings_fields( 'stormpath-id-site-settings' ); ?>
						<?php do_settings_sections( 'stormpath-id-site-settings' ); ?>

						<div class="form-group">
							<label for="stormpath_cache_driver" class="col-sm-3 control-label">Cache Driver</label>
							<div class="col-sm-9">
								<?php $drivers = [ 'Null', 'Array', 'Memcached', 'Redis' ]; ?>
								<select class="form-control" name="stormpath_cache_driver" id="stormpath_cache_driver">
								<option value="">Select Cache Driver</option>
									<?php foreach ( $drivers as $driver ) : ?>
										<?php $selected = $driver == get_option(
											'stormpath_cache_driver' ); ?>
										<option
											value="<?php esc_html_e( $driver ); ?>"
											<?php if ( $selected ) : ?> selected="selected" <?php endif; ?>
										>
											<?php esc_html_e( $driver ); ?>
										</option>
									<?php endforeach; ?>
								</select>

							</div>
						</div>

						<div class="form-group" id="stormpath_cache_driver_memcached_settings" <?php echo 'Memcached'
						!=
						get_option( 'stormpath_cache_driver' ) ? 'style="display:none;"' : '' ?> >
							<div class="row">
								<label class="col-sm-3 control-label">Memcached Info</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label">Host</label>
								<div class="col-sm-8">
									<input
										type="text"
										class="form-control"
										id="stormpath_memcached_host"
										name="stormpath_memcached_host"
									    value="<?php echo get_option( 'stormpath_memcached_host' ); ?>"
									>
								</div>
							</div>
							<div class="row">
								<label for="stormpath_id_site_domain_name" class="col-sm-4
								control-label">Port</label>
								<div class="col-sm-8">
									<input
										type="text"
										class="form-control"
										id="stormpath_memcached_port"
										name="stormpath_memcached_port"
									    value="<?php echo get_option( 'stormpath_memcached_port' ); ?>"
									>
								</div>
							</div>
						</div>


						<div class="form-group" id="stormpath_cache_driver_redis_settings" <?php echo 'Redis'
						!=
						get_option( 'stormpath_cache_driver' ) ? 'style="display:none;"' : '' ?>>
							<div class="row">
								<label class="col-sm-3 control-label">Redis Info</label>
							</div>
							<div class="row">
								<label class="col-sm-4 control-label">Host</label>
								<div class="col-sm-8">
									<input
										type="text"
										class="form-control"
										id="stormpath_redis_host"
										name="stormpath_redis_host"
									    value="<?php echo get_option( 'stormpath_redis_host' ); ?>"
									>
								</div>
							</div>
							<div class="row">
								<label for="stormpath_id_site_domain_name" class="col-sm-4
								control-label">Password</label>
								<div class="col-sm-8">
									<input
										type="password"
										class="form-control"
										id="stormpath_redis_password"
										name="stormpath_redis_password"
									    value="<?php echo get_option( 'stormpath_redis_password' ); ?>"
									>
								</div>
							</div>
						</div>


						<?php submit_button( 'Update Cache Settings', 'button-stormpath', 'submit', true, [ 'id' =>
							'update-stormpath-cache-settings' ] ); ?>
						<div class="stormpath-error-text"></div>
					</form>
					<span class="dashicons dashicons-yes stormpath-updated"></span>
					<span class="dashicons dashicons-no stormpath-error"></span>
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
								<?php $callback_path = apply_filters('stormpath_callback_path', 'stormpath/callback')
								; ?>
								<span id="helpBlock" class="help-block">Please make sure <?php echo( get_site_url() . "/{$callback_path}" ); ?> is added here, otherwise ID Site will not work.</span>
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




