<h1>Arcs</h1>
<table id="arc_table">
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

function addArc(arcId, arcName) {
	var $table = $('#arc_table');

	var html = '<tr><td><a href="' + baseUrl + 'admin/quest/view/' + arcId + '">' + arcName + '</a></td></tr>';
	$table.append(html);
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

			for (var i = 0; i < json.arcs.length; ++i) {
				addArc(json.arcs[i].id, json.arcs[i].name);
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
