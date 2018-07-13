<div id="quest_wrapper">
<div id="quest_info">
<h1 id="quest_header">Quest Name</h1>
<div id="quest_form">
<span class="input_info">Name:</span><input type="text" name="quest_name" id="quest_name"/><br />
<span class="input_info">Main-Sub:</span><input maxlength="1" style="width: 1.5em;" type="text" name="main" id="main"/>-<input maxlength="1" style="width: 1.5em;" type="text" name="sub" id="sub"/><br />
<span class="input_info">Points:</span><input type="text" name="points" id="points"/><br />
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

				var headers = '<thead><tr>' +
					'<th class="icon"><img src="' + baseUrl + 'assets/image/swap_vert.png"/></th>' + 
					'<th>Text</th>' + 
					'<th class="icon"><img src="' + baseUrl + 'assets/image/arrow_downward.png"/></th>' +
					'<th class="icon"><img src="' + baseUrl + 'assets/image/fast_forward.png"/></th>' +
					'<th class="icon"><img src="' + baseUrl + 'assets/image/timer.png"/></th>' +
					'<th>Del</th></tr></thead><tbody></tbody>';
				$('#hints').append(headers);
				$tbody = $('#hints').find('tbody');
				

				if (json.hints !== undefined && json.hints != null) {
					for (var i = 0; i < json.hints.length; ++i) {
						hint = json.hints[i];
						skippable = '';
						if (hint['skippable'] == 1) {
							skippable = ' checked="checked"';
						}

						var html = '<tr>' +
							'<td id="reorder"><img class="reorder" src="' + baseUrl + 'assets/image/reorder.png" /></td>' +
							'<td contenteditable="true" id="text">' + hint['text'] + '</td>' +
							'<td class="centered" contenteditable="true" id="points">' + hint['point_deduction'] + '</td>' +
							'<td class="icon"><input type="checkbox" id="skippable"' + skippable + '/></td>' +
							'<td contenteditable="true" id="time">' + hint['time'] + '</td>' +
							'<td class="icon"><img class="link" id="delete" src="' + baseUrl + 'assets/image/delete.png" /></td>' +
							'</tr>';

						$html = $(html);
						$html.data('id', hint['id']);
						$html.find('td').on('blur', function() {
							updateHint($(this).parent());	
						});
						$html.find('#skippable').click(function() {
							updateHint($(this).parent().parent());
						});
						$html.find('#delete').click(function() {
							let $td = $(this).parent();
							let $tr = $td.parent();
							deleteHint($tr);
						});

						$tbody.append($html);
						$tbody.sortable({
							cursor: 'move',
							handle: '#reorder',
							stop: stopHintSort
						});
					}
				}
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function stopHintSort(event, ui) {
	let hint = ui.item;
	let newOrder = hint.index() + 1;

	var formData = {
		id: hint.data('id'),
		order: newOrder
	}

	$.ajax({
		url: baseUrl + 'admin/hint/move',
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
				$('#points').val(json.quest['points']);
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
	skippable = $hintElement.find('#skippable').is(':checked') ? 1 : 0;
	points = $hintElement.find('#points').html();

	var formData = {
		ajax: true,
		id: id,
		text: text,
		skippable: skippable,
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
