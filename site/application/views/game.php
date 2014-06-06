<div id="middle_form-wrapper">
<div id="game">
<div id="quest"></div>
<div id="hints"></div>
<?php
	echo '<div id="answer_box">';

	$form['id'] = 'answer_form';
	echo form_open('game/try_answer', $form);

	$input['type'] = 'text';
	$input['name'] = 'answer';
	$input['id'] = $input['name'];
	$input['maxlength'] = '50';
	$input['alt'] = 'Your answer';
	$input['value'] = $input['alt'];

	echo form_input($input);
	echo form_close();

	echo '</div>';
?>
</div>
</div>
<script type="text/javascript">
var $messages = $('#messages');
$('#answer_form').before($messages);
$('#content').remove('#messages');


$(document).ready(function() {
	// Init quest and hint data
	$('#quest').data('id', -1);
	$('#hints').data('count', 0);

	getQuest(true);
	getHints(true);
});

var baseUrl = '<?php echo base_url(); ?>';

var $answer = $('#answer');
var $answer_form = $('#answer_form');
$answer_form.submit(function (ev) {
	var formData = {
		ajax: true,
		answer: $answer.val()
	}

	$.ajax({
		url: $answer_form.attr('action'),
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}
			
			if (json.success) {
				getQuest();
				getHints();
				$('#messages').children().remove();
				displayAjaxReturnMessages(json);
			}
			else {
				$timeLeft = $('.time_left')
				if ($timeLeft.length == 0 || ($timeLeft.length > 0 && $timeLeft.data('time') == 0)) {
					updateTimeLeft(json.time_left);
					addMessage(json.error_messages, 'error', json.time_left);
				}
			}

		}
	});
	$answer.val('');

	ev.preventDefault();
	return false;	
});

function updateTimeLeft(timeLeft) {
	$timeLeft = $('.time_left');
	$timeLeft.html(timeLeft);
	$timeLeft.data('time', timeLeft);

	if (timeLeft > 0) {
		setTimeout('updateTimeLeft(' + (timeLeft - 1) + ')', 1000);
	}
}

function getQuest($use_timeout) {
	var formData = {
		ajax: true
	}

	$.ajax({
		url: baseUrl + 'game/get_quest',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}
			
			if (json.completed == true) {
				window.location = baseUrl + 'game/completed';
			} else {
				// New quest?
				if (json.quest['id'] != $('#quest').data('id')) {
					$('#quest').children().remove();
					$('#hints').children().remove();

					$('#quest').append('<h1>Quest ' + json.quest['main'] + '–' + json.quest['sub'] + '</h1>');
					$('#quest').append(json.quest['html']);
					$('#quest').append('<div id="hints"></div>');

					$('#hints').data('count', 0);
					$('#quest').data('id', json.quest['id']);
				}
			}
		}
	});

	if ($use_timeout === true) {
		setTimeout('getQuest(true)', 1000);
	}
}

function getHints($use_timeout) {
	var formData = {
		ajax: true
	}

	$.ajax({
		url: baseUrl + 'game/get_hints',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.hint !== undefined) {
				hints = json.hint;
				cHintsAlready = $('#hints').data('count');
				// Append hints that doesn't exist
				for (i = cHintsAlready; i < hints.length; ++i) {
					$('#hints').append('<p><strong>Hint ' + (i+1) + ':</strong> ' + hints[i] + '</p>');
				}
				$('#hints').data('count', hints.length);
			}

			displayAjaxReturnMessages(json);
		}
	});

	if ($use_timeout === true) {
		setTimeout('getHints(true)', 1000);
	}
}
</script>
