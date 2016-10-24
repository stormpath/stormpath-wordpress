<div class="form-group">
	<label for="stormpath_client_apikey_id" class="col-sm-2 control-label">API Key Id</label>
	<div class="col-sm-10">
		<input
			type="text"
			class="form-control"
			id="stormpath_client_apikey_id"
			placeholder="1SCEJ0Y1R9MFM8NSUM2HXM7TF"
			value="<?php esc_html_e( get_option( 'stormpath_client_apikey_id' ) ); ?>"
		>
	</div>
</div>

<div class="form-group">
	<label for="stormpath_client_apikey_secret" class="col-sm-2 control-label">API Key Secret</label>
	<div class="col-sm-10">
		<input
			type="password"
			class="form-control"
			id="stormpath_client_apikey_secret"
			placeholder="2e+rv1NClLife+HmAigI1Kg6PRFqRTFb422DRdsVwKs"
			value="<?php esc_html_e( get_option( 'stormpath_client_apikey_secret' ) ); ?>"
		>
	</div>
</div>
