<h1>Quests</h1>
<table id="quest_container"></table>
<input class="big" type="submit" id="add_quest" value="Add Quest"></h2>

<script type="text/javascript">
var baseUrl = '<?php echo base_url(); ?>';
var arcId = <?php echo $arc_id; ?>;

function repopulateQuests(quests) {
	$('#quest_container').children().remove();

	var headers = '<tr><th>#</th><th>Name</th><th>Points</th></tr>';
	$('#quest_container').append(headers);

	for (var i = 0; i < quests.length; ++i) {
		quest = quests[i];

		var html = '<tr id="quest_id_' + quest['id'] + '">' +
			'<td id="main_sub">' + quest['main'] + '-' + quest['sub'] + '</td>' +
			'<td id="quest_name"><a href="' + baseUrl + 'admin/quest/view/' + quest['id'] + '">' + quest['name'] + '</a></td>' +
			'<td id="points">' + quest['point_standard'] + '</td>' + 
			'</tr>';

		$('#quest_container').append(html);
	}
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
