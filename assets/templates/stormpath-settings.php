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

<div class="stormpath-settings-wrap">
	<section class="stormpath-page-header">
		<img class="stormpath-header-logo" src="<?php esc_html_e( STORMPATH_PLUGIN_ROOT_URL . 'assets/images/logo.png' ); ?>" />
	</section>
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
			</form>
		</div>
	</div>
</div>
