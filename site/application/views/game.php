<div id="middle_form-wrapper">
<div id="game">
<div id="quest">
<?php
// echo '<h1>Quest ' . $main . '–' . $sub . '</h1>';
// 
// if ($html_is_php === TRUE) {
// 	eval($html);
// } else {
// 	echo $html;
//}
?>
<div id="hints"></div>
</div>
<?php
// // Hint 1
// if ($show_hint_1) {
// 	echo '<p><strong>Hint 1:</strong> ' . $hint_1_text . '</p>';
// }
// // Hint 2
// if ($show_hint_2) {
// 	echo '<p><strong>Hint 2:</strong> ' . $hint_1_text . '</p>';
// }
// 
// if ($has_answer_box === TRUE) {
	echo '<div id="answer_box">';
	echo form_open('game');

// 	$input['class'] = 'big';
	$input['type'] = 'text';
	$input['name'] = 'answer';
	$input['id'] = $input['name'];
	$input['maxlength'] = '50';
	$input['alt'] = 'Your answer';
	$input['value'] = $input['alt'];
	echo form_input($input);

	echo form_submit('answer_submit', 'Answer');
	echo form_close();

	echo '</div>';
// }
?>
</div>
</div>
<script type="text/javascript">
var $messages = $('#messages');
$('#answer').before($messages);
$('#content').remove('#messages');


$(document).ready(function() {
	// Init quest and hint data
	$('#quest').data('id', -1);
	$('#hints').data('count', 0);

	getQuest(true);
});

function getQuest($use_timeout) {
	var $formData = {
		ajax: true
	}

	var $base_url = '<?php echo base_url(); ?>';


	$.ajax({
		url: '<?php echo base_url('game/get_quest'); ?>',
		type: 'POST',
		data: $formData,
		dataType: 'json',
		success: function($json) {
			if ($json === null || $json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}
			
			if ($json.completed == true) {
				window.location = '<?php echo base_url('game/completed'); ?>';
			} else {
				// New quest?
				if ($json.quest['id'] != $('#quest').data('id')) {
					$('#quest').children().remove();
					$('#quest').append('<h1>Quest ' + $json.quest['main'] + '–' + $json.quest['sub'] + '</h1>');
					$('#quest').append($json.quest['html']);
					$('#quest').append('<div id="hints"></div>');
					$('#hints').data('count', 0);
					$('#quest').data('id', $json.quest['id']);

// 					// Has answer box
// 					if ($json.quest['has_answer_box'] == true) {
// 						$('#quest').append('<form action="' + $base_url + 'game/try_answer');
// 					}
				}

				// New hints?
				if ($json.quest['show_hint_1'] && $('#hints').data('count') < 1) {
					$('#hints').append('<p><strong>Hint 1:</strong> ' + $json.quest['hint_1_text'] + '</p>');
					$('#hints').data('count', 1);
				}
				if ($json.quest['show_hint_2'] && $('#hints').data('count') < 2) {
					$('#hints').append('<p><strong>Hint 2:</strong> ' + $json.quest['hint_2_text'] + '</p>');
					$('#hints').data('count', 2);
				}
			}
		}
	});

	if ($use_timeout === true) {
		setTimeout('getQuest(true)', 1000);
	}
}
</script>
