<h1><?php echo $arc_name; ?></h1>
<table>
<thead><tr><th>#</th><th>Name</th><th>Points</th><th>Delete</th></tr></thead>
<tbody id="quest_container"></tbody>
</table>
<input class="big" type="submit" id="add_quest" value="Add Quest"></h2>

<script type="text/javascript">
var baseUrl = '<?php echo base_url(); ?>';
var arcId = <?php echo $arc_id; ?>;

function repopulateQuests(quests) {
	$quest_container = $('#quest_container');
	$quest_container.children().remove();

	for (var i = 0; i < quests.length; ++i) {
		quest = quests[i];

		let html = '<tr>' +
			'<td id="main_sub" contenteditable="true">' + quest['main'] + '-' + quest['sub'] + '</td>' +
			'<td id="quest_name"><a href="' + baseUrl + 'admin/quest/view/' + quest['id'] + '">' + quest['name'] + '</a></td>' +
			'<td class="centered" id="points">' + quest['points'] + '</td>' +
			'<td class="icon"><img class="link" id="delete" src="' + baseUrl + 'assets/image/delete.png" /></td>' +
			'</tr>';

		let $tr = $(html);
		$tr.data('id', quest['id']);
		$tr.find('#delete').click(function() {
			let $td = $(this).parent();
			let $tr = $td.parent();
			deleteQuest($tr);
		});
		$tr.find('#main_sub').on('blur', function() {
			editQuest($(this).parent());
		});
		
		$quest_container.append($tr);
	}
}

function editQuest($questElement) {
	let main_sub = $questElement.find('#main_sub').html().split('-');

	if (main_sub.length != 2) {
		addMessage('Invalid format', 'error');
		return;
	}

	let formData = {
		id: $questElement.data('id'),
		main: main_sub[0],
		sub: main_sub[1],
	}

	$.ajax({
		url: baseUrl + 'admin/quest/edit-main-sub',
		type: 'POST',
		data: formData,
		dataType: 'json',
	});

}

function deleteQuest($questElement) {
	let formData = {
		id: $questElement.data('id'),
	}

	$.ajax({
		url: baseUrl + 'admin/quest/remove',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				$questElement.remove();
			}

			displayAjaxReturnMessages(json);
		}
	});

}

function repopulateAllQuests() {
	var formData = {
		ajax: true,
		arc_id: arcId
	}

	$.ajax({
		url: baseUrl + 'admin/quest/get_all',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				repopulateQuests(json.quests)
			}

			displayAjaxReturnMessages(json);
		}
	});
}

function addQuest() {
	var formData = {
		ajax: true,
		arc_id: arcId
	}

	$.ajax({
		url: baseUrl + 'admin/quest/add',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (json.success) {
				repopulateAllQuests();
			}

			displayAjaxReturnMessages(json);
		}
	});
}

$(document).ready(function() {
	repopulateAllQuests();

	$('#add_quest').click(function() {
		addQuest();	
	});
});
</script>
