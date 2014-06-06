<div id="sidebar">
<h3>You have</h3>
<p id="points">0 points</p>
<h3>This quest is currently worth</h3>
<p id="quest_worth">0 points</p>
<p class="small">
Quest points: <span class="success" id="point_default">0p</span></br>
Team first: <span class="success" id="point_first">0p</span></br>
Hint penalty: <span class="error" id="point_hint_penalty">0p</span></br>
</p>
<br />
<h3>Next hint in</h3>
<p id="hint_next">0 seconds</p>
<p class="small">
Hint penalty: <span class="error" id="hint_next_penalty">0p</span></br>
<br />
<h3>Teams</h3>
<table id="team-standings">
<tr><th>Team</th><th>Quest</th><th>Points</th></tr>
</table>
</div>
<script type="text/javascript">
function updateSidebar() {
	var $formData = {
		ajax: true
	}

	$.ajax({
		url: '<?php echo base_url('game/get_sidebar') ?>',
		type: 'POST',
		data: $formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			$('#points').html(json.points + ' points');
			$('#quest_worth').html(json.quest_worth + ' points');
			$('#hint_next').html(json.hint_next);
			$('#point_default').html(json.point_default + 'p');
			$('#point_first').html(json.point_first + 'p');
			$('#point_hint_penalty').html(json.point_hint_penalty + 'p');
			$('#hint_next_penalty').html(json.hint_next_penalty + 'p');
			
			// Team table
			$table = $('#team-standings');

			// Remove all rows except first
			$table.find('tr:not(:first)').remove();

			for (var $i = 0; $i < json.c_teams; $i++) {
				$table.append('<tr><td>' + json.teams[$i].name +
					'</td><td>' + json.teams[$i].quest +
					'</td><td>' + json.teams[$i].points + '</td></tr>');
			}
		}
	});

	// Set timer to update the sidebar again.
	setTimeout('updateSidebar()', 1000);
}

$(document).ready(function() {
	updateSidebar();
});

</script>
