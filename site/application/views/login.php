<div id="middle_form-wrapper" style="margin-top: 100px;">
<div id="middle_form" class="fine_border">
<h1 style="text-align: center;"> Login </h1>
<?php
	echo form_open('login');
	
	$input['class'] = 'middle_form big';
	$input['type'] = 'text';

	$input['name'] = 'team';
	$input['maxlength'] = '25';
	$input['alt'] = 'Team name';
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
	$('#content').remove($messages);
</script>
