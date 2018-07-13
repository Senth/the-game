<h1>Arcs</h1>
<table id="arc_table">
<tr><th>Name</th><th>Length</th><th>Start</th><th>Reset</th></tr>
</table>

<h2>Create Arc</h2>
<?php 
	$input['id'] = 'add_form';
	echo form_open('admin/arc/add', $input);

	$input['name'] = 'name';
	$input['id'] = $input['name'];
	$input['maxlength'] = '32';
	$input['alt'] = 'Arc Name';
	$input['value'] = $input['alt'];
	echo form_input($input);
	echo form_close();
?>

<script type="text/javascript">
var baseUrl = '<?php echo base_url(); ?>';

String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;
    return time;
}

function addArc(id, name, length, started) {
	let html = '<tr>' + 
		'<td><a href="' + baseUrl + 'admin/quest/arc/' + id + '">' + name + '</a></td>' +
		'<td contenteditable="true">' + length.toHHMMSS() + '</td>' +
		'<td style="text-align: center;" id="start">';

	if (started) {
		html += 'started';
	} else {
		html += '<img class="link" id="start_arc" src="' + baseUrl + 'assets/image/alarm.png" />';
	}

	html += '</td>' +
		'<td style="text-align: center;"><img class="link" id="reset_arc" src="' + baseUrl + 'assets/image/restore.png" /></td>' + 
		'</tr>';
	$arc = $(html);
	$arc.data('id', id);
	$table = $('#arc_table');
	$table.append($arc);

	if (!started) {
		$arc.find('#start_arc').click(function() {
			var formData = {
				ajax: true,
				arc_id: id
			}

			$.ajax({
				url: baseUrl + 'admin/arc/start_arc',
				type: 'POST',
				data: formData,
				dataType: 'json',
				success: function(json) {
					if (json === null || json.success === undefined) {
						addMessage('Return message is null, contact administrator', 'error');
						return;
					}

					if (json.success) {
						$('#arc_id_' + id).find('#start').html('started');
					}

					displayAjaxReturnMessages(json);
				}
			});
		});
	}

	$arc.find('#reset_arc').click(function() {
		var formData = {
			ajax: true,
			arc_id: id
		}

		$.ajax({
			url: baseUrl + 'admin/arc/reset',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(json) {
				if (json === null || json.success === undefined) {
					addMessage('Return message is null, contact administrator', 'error');
					return;
				}

				if (json.success) {
					getAndAddArcs();
				}

				displayAjaxReturnMessages(json);
			}
		});
	});
}

$(document).ready(function() {
	getAndAddArcs();	
});

function getAndAddArcs() {
	var formData = {
		ajax: true
	}

	$.ajax({
		url: baseUrl + 'admin/arc/get_arcs',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			$('#arc_table').find('tr:not(:first)').remove();
			for (var i = 0; i < json.arcs.length; ++i) {
				var arc = json.arcs[i];
				addArc(arc.id, arc.name, arc.length, arc.start_time != null);
			}

			displayAjaxReturnMessages(json);
		}
	});
}

$form = $('#add_form');
$form.submit(function(ev) {
	var formData = {
		ajax: true,
		name: $('#name').val()
	}

	$.ajax({
		url: $form.attr('action'),
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			// Successful -> Append to table
			if (json.success) {
				addArc(json.arc_id, formData.name);
			}

			displayAjaxReturnMessages(json);
		}
	});

	ev.preventDefault();
	return false;
});

</script>
