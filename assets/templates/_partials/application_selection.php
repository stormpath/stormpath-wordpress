<?php
if ( ! \Stormpath\WordPress\Stormpath::get_instance()->api_keys_valid() ) {
	return;
}
?>

<div class="form-group">
	<label for="stormpath_client_apikey_id" class="col-sm-2 control-label">API Key Id</label>
	<div class="col-sm-10">
		<select class="form-control">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
		</select>
	</div>
</div>
