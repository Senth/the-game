<div id="quest_wrapper">
<div id="quest_info">
<h1 id="quest_header">Quest Name</h1>
<div id="quest_form">
<span class="input_info">Name:</span><input type="text" name="quest_name" id="quest_name"/><br />
<span class="input_info">Main-Sub:</span><input maxlength="1" style="width: 1.5em;" type="text" name="main" id="main"/>-<input maxlength="1" style="width: 1.5em;" type="text" name="sub" id="sub"/><br />
<span class="input_info">Points:</span><input type="text" name="points" id="points"/><br />
<span class="input_info">Points First:</span><input type="text" name="points_first" id="points_first"><br />
<span class="input_info">Answer:</span><input type="text" name="answer" id="answer"/><br />
<span class="input_info">Is php:</span><input type="checkbox" name="is_php" id="is_php"/><br />
<span class="input_info">Html:</span><br />
<textarea name="html" id="html" style="width: 95%; height: 20%;"></textarea>
</div>

<h1>Hints</h1>
<table id="hints"></table>
<h2>New Hint</h2>
<input type="submit" id="add_hint" value="Add Hint" />
</div>
<div id="quest_content">
</div>
</div>

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

				var headers = '<tr><th>Text</th><th>Time</th><th>-Points</th><th>Del</th></tr>';
				$('#hints').append(headers);

				if (json.hints !== undefined && json.hints != null) {
					for (var i = 0; i < json.hints.length; ++i) {
						hint = json.hints[i];

						var html = '<tr>' +
							'<td contenteditable="true" id="text">' + hint['text'] + '</td>' +
							'<td contenteditable="true" id="time">' + hint['time'] + '</td>' +
							'<td contenteditable="true" id="points">' + hint['point_deduction'] + '</td>' +
							'<td><img class="link" id="delete" src="' + baseUrl + 'assets/image/delete.png" /></td>' +
							'</tr>';

						$html = $(html);
						$html.data('id', hint['id']);
						$html.find('td').on('blur', function() {
							updateHint($(this).parent());	
						});
						$html.find('#delete').click(function() {
							$td = $(this).parent();
							$tr = $td.parent();
							deleteHint($tr);
						});

						$('#hints').append($html);
					}
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
				$('#html').val(json.quest['html']);
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

function getQuestHtml() {
	var formData = {
		ajax: true
	}

	$.ajax({
		url: baseUrl + 'admin/quest/get_html/' + questId,
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				$('#quest_content').html(json.html);
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function addHint() {
	var formData = {
		ajax: true,
		quest_id: questId
	}

	$.ajax({
		url: baseUrl + 'admin/hint/add',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				getHints();
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function updateHint($hintElement) {
	id = $hintElement.data('id');
	text = $hintElement.find('#text').html();
	time = $hintElement.find('#time').html();
	points = $hintElement.find('#points').html();

	var formData = {
		ajax: true,
		id: id,
		text: text,
		time: time,
		points: points
	}

	$.ajax({
		url: baseUrl + 'admin/hint/edit',
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
}

function deleteHint($hintElement) {
	id = $hintElement.data('id');

	var formData = {
		ajax: true,
		id: id,
	}

	$.ajax({
		url: baseUrl + 'admin/hint/remove',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				$hintElement.remove();
			}

			displayAjaxReturnMessages(json);
		}
	});
}

$(document).ready(function() {
	getQuest();
	getQuestHtml();
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
		getQuestHtml();
	});

	$('#add_hint').click(function() {
		addHint();
	});
});
</script>
