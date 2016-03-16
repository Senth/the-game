<h1 id="quest_header">Quest Name</h1>
<div id="quest_form">
<span class="input_info">Name:</span><input type="text" name="quest_name" id="quest_name"/><br />
<span class="input_info">Main-Sub:</span><input maxlength="1" style="width: 1.5em;" type="text" name="main" id="main"/>-<input maxlength="1" style="width: 1.5em;" type="text" name="sub" id="sub"/><br />
<span class="input_info">Points:</span><input type="text" name="points" id="points"/><br />
<span class="input_info">Points First:</span><input type="text" name="points_first" id="points_first"><br />
<span class="input_info">Answer:</span><input type="text" name="answer" id="answer"/><br />
<span class="input_info">Is php:</span><input type="checkbox" name="is_php" id="is_php"/><br />
<span class="input_info">Html:</span><br />
<textarea name="html" id="html" style="width: 40%; height: 20%;"></textarea>
</div>

<h1>Hints</h1>
<table id="hints"></table>
<h2>New Hint</h2>
<form method="post" action="<?php echo base_url('admin/hint/add'); ?>">
<input type="hidden" name="quest_id" value="<?php echo $quest_id; ?>" />
<span class="input_info">Text:</span><input type="text" name="text" /><br />
<span class="input_info">Time:</span><input type="text" name="time" /><br />
<span class="input_info">Points:</span><input type="text" name="points" /><br />
<input type="submit" value="Add hint" />
</form>

<script type="text/javascript">
var baseUrl = '<?php echo base_url(); ?>';
var questId = <?php echo $quest_id; ?>;

function getHints() {
	var formData = {
		ajax: true
	}

	$.ajax({
		url: baseUrl + 'admin/hint/get_all/' + questId,
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				$('#hints').children().remove();

				var headers = '<tr><th>Text</th><th>Time</th><th>-Points</th></tr>';
				$('#hints').append(headers);

				for (var i = 0; i < json.hints.length; ++i) {
					hint = json.hints[i];

					var html = '<tr id="hint_id_"' + hint['id'] + '">' +
						'<td contenteditable="true" id="text">' + hint['text'] + '</td>' +
						'<td contenteditable="true" id="time">' + hint['time'] + '</td>' +
						'<td contenteditable="true" id="points">' + hint['point_deduction'] + '</td>' +
						'</tr>';

					$('#hints').append(html);
				}
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function getQuest() {
	var formData = {
		ajax: true,
	}

	$.ajax({
		url: baseUrl + 'admin/quest/get/' + questId,
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
			}

			if (json.success) {
				$('#quest_header').html(json.quest['name']);
				$('#quest_name').val(json.quest['name']);
				$('#main').val(json.quest['main']);
				$('#sub').val(json.quest['sub']);
				$('#points').val(json.quest['point_standard']);
				$('#points_first').val(json.quest['point_first_extra']);
				$('#answer').val(json.quest['answer']);
				if (json.quest['html_is_php'] == 1) {
					$('#is_php').prop('checked', true);
				} else {
					$('#is_php').prop('checked', false);
				}
				$('#html').html(json.quest['html']);
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function updateQuest() {
	var formData = {
		ajax: true,
		id: questId,
		name: $('#quest_name').val(),
		main: $('#main').val(),
		sub: $('#sub').val(),
		points: $('#points').val(),
		points_first: $('#points_first').val(),
		answer: $('#answer').val(),
		is_php: $('#is_php').prop('checked'),
		html: $('#html').val()
	}

	$.ajax({
		url: baseUrl + 'admin/quest/edit',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
			}

			displayAjaxReturnMessages(json);
		}
	});

	$('#quest_header').html($('#quest_name').val());
}

function updateHtml() {
	// TODO update the HTML on the side
}

$(document).ready(function() {
	getQuest();
	getHints();
	
	
	$('#quest_form :input').on('input', function() {
		updateQuest();
	});

	$('#is_php').change(function() {
		updateQuest();
	});

	$('#html').on('input', function() {
		updateQuest();
	});

	$('#html').change(function() {
		updateHtml();
	});
});
</script>
