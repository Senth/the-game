<div id="middle_form-wrapper" style="margin-top: 100px;">
<div id="middle_form" class="fine_border">
<h1 style="text-align: center;">Admin Login</h1>
<?php
	$input['id'] = 'login_form';
	echo form_open('admin/login/validate_login', $input);
	
	$input['class'] = 'middle_form big';
	$input['type'] = 'text';

	$input['name'] = 'username';
	$input['maxlength'] = '25';
	$input['alt'] = 'Username';
	$input['id'] = $input['name'];
	$input['value'] = set_value($input['name'], $input['alt']);
	echo form_input($input);
	echo '<br />';

	$input['name'] = 'password';
	$input['maxlength'] = '50';
	$input['placeholder'] = 'password';
	$input['alt'] = 'Password';
	$input['id'] = $input['name'];
	$input['value'] = set_value($input['name'], $input['alt']);
	echo form_input($input);
	echo '<br />';
?>
	<div style="text-align: center">
		<?php echo form_submit('login', 'Login', 'class="big"'); ?>
	</div>
<?php
	echo form_close();
?>
</div>
</div>
<script type="text/javascript">
var $messages = $('#messages');
$('h1').after($messages);
$('#content').remove('#messages');

var baseUrl = '<?php echo base_url(); ?>';

var $loginForm = $('#login_form');
$loginForm.submit(function (ev) {
	// Ajax login form
	var formData = {
		ajax: true,
		username: $('#username').val(),
		password: $('#password').val()
	}

	$.ajax({
		url: $loginForm.attr('action'),
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			// Successful login
			if (json.success) {
				window.location.replace(baseUrl + json.redirect);
			}


			displayAjaxReturnMessages(json);
		}
	});

	ev.preventDefault();
	return false;
});
</script>
