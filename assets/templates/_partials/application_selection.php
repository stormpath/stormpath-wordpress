<div class="form-group">
	<label for="stormpath_client_apikey_id" class="col-sm-3 control-label">Application</label>
	<div class="col-sm-9">
		<select class="form-control" name="stormpath_application">
		<?php if ( ! \Stormpath\WordPress\ApiKeys::get_instance()->api_keys_valid() ) : ?>
			<option value="">Please save correct API Keys to show your list of applications.</option>
		<?php else : ?>
			<option value="">Please select an application</option>
	<?php $applications = \Stormpath\WordPress\Application::get_instance()->get_application_list(); ?>
		<?php foreach ( $applications as $application ) : ?>
			<?php $selected = $application->href == get_option( 'stormpath_application' ); ?>
			<option
				value="<?php esc_html_e( $application->href ); ?>"
			    <?php if ( $selected ) : ?> selected="selected" <?php endif; ?>
			>
				<?php esc_html_e( $application->name ); ?>
			</option>
		<?php endforeach; ?>
		<?php endif; ?>
		</select>

	</div>
</div>




