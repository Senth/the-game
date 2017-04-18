<div id="sidebar">
<div class="meter" style="width: 100%"><span id="arc_time_progress" style="width: 100%;"></span></div>
<p style="margin-bottom: 2em;" class="progress_text" id="arc_time_text"></p>
<?php
if ($team_info->is_logged_in()) {
	$html = '
		<div class="meter" style="width: 100%"><span id="completed_progress"></span></div>
		<p class="progress_text"><span id="completed_text">0/0</span> quests completed</p>
		<h3>Score: <span id="points">0 points</span></h3>
		<h3 style="margin-bottom: 0px;">Quest is worth: <span id="quest_worth">0 points</span></h3>
		<p class="progress_text">
		Quest points: <span class="success" id="point_default">0p</span><br />
		Hint penalty: <span class="error" id="point_hint_penalty">0p</span><br />
		</p>
		<h3 style="margin-bottom: 0px;">Next hint in: <span id="hint_next">0 seconds</span></h3>
		<p class="progress_text">
		Hint penalty: <span class="error" id="hint_next_penalty">0p</span></p>';
	echo $html;
}
?>
<h2>Team Standings</h2>
<table id="team-standings">
<tr><th>Team</th><th>Quest</th><th>Points</th></tr>
</table>
</div>
<script type="text/javascript">
var isTeam = <?php echo $team_info->is_logged_in() ? 'true' : 'false'; ?>;

String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;
    return time;
}

function updateSidebar() {
	var $formData = {
		ajax: true
	}

	$.ajax({
		url: '<?php echo base_url('sidebar/get_info') ?>',
		type: 'POST',
		data: $formData,
		dataType: 'json',
		success: function(json) {
			if (json === null || json.success === undefined) {
				addMessage('Return message is null, contact administrator', 'error');
				return;
			}

			if (isTeam) {
				$('#points').html(json.points + ' points');
				$('#quest_worth').html(json.quest_worth + ' points');
				$('#hint_next').html(json.hint_next);
				$('#point_default').html(json.point_default + 'p');
				$('#point_first').html(json.point_first + 'p');
				$('#point_hint_penalty').html(json.point_hint_penalty + 'p');
				$('#hint_next_penalty').html(json.hint_next_penalty + 'p');

				// Calculate completed
				var completed = json.quests_completed;
				var total = json.quests_total;
				$('#completed_text').html(completed + '/' + total);

				if (total > 0) {
					var completedPercent = completed / total * 100;
					$('#completed_progress').css('width', completedPercent + '%');
				}
			}

			// Arc time left
			var arcLength = json.arc_length;
			var arcLeft = json.arc_left;
			if (arcLength > 0) {
				var arcLeftPercent = arcLeft / arcLength * 100;
				var $arcTimeProgress = $('#arc_time_progress');
				$arcTimeProgress.css('width', arcLeftPercent + '%');

				// Set color
				var $arcTimeParent = $arcTimeProgress.parent();
				if (arcLeftPercent > 20) {
					$arcTimeParent.removeClass('red');
					$arcTimeParent.removeClass('orange');
				} else if (arcLeftPercent > 6) {
					$arcTimeParent.removeClass('red');
					$arcTimeParent.addClass('orange');
				} else {
					$arcTimeParent.removeClass('orange');
					$arcTimeParent.addClass('red');
				}

				// Set Text
				$('#arc_time_text').html(String(arcLeft).toHHMMSS());
			}

			
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
