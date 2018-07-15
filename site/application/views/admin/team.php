<h1>Teams</h1>
<table>
<thead><tr><th>Name</th><th>Active</th><th>Set Password</th></tr></thead>
<tbody id="team_table"></tbody>
</table>

<h2>Create Team</h2>
<?php
	$input['id'] = 'create_team';
	echo form_open('admin/team/add', $input);

	$input['name'] = 'name';
	$input['id'] = $input['name'];
	$input['maxlength'] = '25';
	$input['alt'] = 'Team name';
	$input['value'] = $input['alt'];
	echo form_input($input) . '<br />';

	$input['name'] = 'password';
	$input['id'] = $input['name'];
	$input['maxlength'] = '50';
	$input['alt'] = 'Password';
	$input['value'] = $input['alt'];
	echo form_input($input);
// 	echo '<input id="submit" type="sumbit" style="display: none;"/>';
	echo form_close();
?>

<h3><a href="<?php echo base_url('admin/arc'); ?>">View Arcs</a></h3>

<script type="text/javascript">

var baseUrl = '<?php echo base_url(); ?>';

function addTeamToTable(id, name, active) {
	let checked = '';
	if (active == 1) {
		checked = ' checked="checked"';
	}

	let html =
		'<tr id="team_id_' + id + '">' +
		'<td id="name" contenteditable="true">' + name + '</td>' +
		'<td class="centered"><input id="active" type="checkbox"' + checked + '/></td>' +
		'<td class="icon"><img class="link" id="set_password" src="' + baseUrl + 'assets/image/autorenew.png"/></td>'
		'</tr>';
	let $team = $(html);
	$team.data('id', id);
	$tbody = $('#team_table');
	$tbody.append($team);

	$team.find('#name').on('blur', function() {
		updateTeam($(this).parent());	
	});
	$team.find('#active').click(function() {
		updateTeam($(this).parent().parent());	
	});
	$team.find('#set_password').click(function() {
		// TODO
	});
}

function updateTeam($teamElement) {
	$active = $teamElement.find('#active');
	active = $active.is(':checked') ? 1 : 0;

	let formData = {
		id: $teamElement.data('id'),
		name: $teamElement.find('#name').html(),
		active: active,
	}

	$.ajax({
		url: baseUrl + 'admin/team/edit',
		type: 'POST',
		data: formData,
		dataType: 'json',
	});
}

function setPassword($teamElement) {
	// TODO
}

function getTeams() {
	let formData = {
	}

	$.ajax({
		url: baseUrl + 'admin/team/get_teams',
		type: 'GET',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			$('#team_table').children().remove();
			for (let i = 0; i < json.teams.length; ++i) {
				let team = json.teams[i];
				addTeamToTable(team.id, team.name, team.active);
			}

			displayAjaxReturnMessages(json);
		}
	});
}

let $form = $('#create_team');
$form.find('input').on('keypress', function(ev) {
	// Only continue for when pressing enter
	if (ev.which != 13) {
		return true;
	}

	$name = $form.find('#name');
	$password = $form.find('#password');
	name = $name.val();
	password = $password.val();

	if (name.length == 0 || name == $name.attr('alt')) {
		addMessage('Please enter a team name', 'error');
		return;
	}
	if (password.length == 0 || password == $password.attr('alt')) {
		addMessage('Please enter a team password', 'error');
		return;
	}

	let formData = {
		name: name,
		password: password,
	}

	$.ajax({
		url: baseUrl + 'admin/team/add',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			addTeamToTable(json.id, name, 1);

			displayAjaxReturnMessages(json);
		}
	});
});

$(document).ready(function() {
	getTeams();
});

</script>
