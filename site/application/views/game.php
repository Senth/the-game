<div id="middle_form-wrapper">
<div id="game">
<div id="quest">
<?php
echo '<h1>Quest ' . $main . '–' . $sub . '</h1>';

if ($html_is_php === TRUE) {
	eval($html);
} else {
	echo $html;
}?>
</div>
<?php
if (isset($hint_1_text)) {
	echo '<p><strong>Hint 1:</strong> ' . $hint_1_text . '</p>';
}
if (isset($hint_2_text)) {
	echo '<p><strong>Hint 2:</strong> ' . $hint_1_text . '</p>';
}

if ($has_answer_box === TRUE) {
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

	echo '</div>';
}
?>
</div>
</div>
<script type="text/javascript">
	var $messages = $('#messages');
	$('#answer').before($messages);
	$('#content').remove($messages);
</script>
