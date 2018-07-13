<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game extends GAME_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Teams', 'team');
		$this->load->model('Quests', 'quest');
		$this->load->model('Hints', 'hint');
		log_message('debug', 'Game loaded successfully!');

		$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
	}

	public function index() {
		$this->load_view('game', NULL);
	}

	public function get_quest() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		log_message('debug', 'Getting quest');

		$json_return['success'] = FALSE;

		// Return early if quest hasn't started yet
		if (!$this->_has_arc_started()) {
			$json_return['started'] = FALSE;
			log_message('debug', 'Arc hasn\'t started');
			echo json_encode($json_return);
			return;
		}


		// Update current quest
		while ($this->_current_quest_id === NULL) {

			assert($this->_current_quest_id !== FALSE);

			// No quest, atm, set the user to get the first quest (1-1)
			if ($this->_current_quest_id === NULL) {
				$first_quest = $this->quest->get_first_quest();
				$this->_current_quest_id = $first_quest;
				$this->team->set_current_quest($this->team_info->get_id(), $first_quest->id);
			}
		}

		// Done with whole game
		if ($this->_current_quest_id === '0') {
			$json_return['completed'] = TRUE;
			$json_return['success'] = TRUE;
			echo json_encode($json_return);
		}
		// Show current quest
		else {
			$quest = $this->quest->get_quest($this->_current_quest_id);

			// Update start time if the quest doesn't have any start time
			if ($quest->start_time === NULL) {
				$this->quest->set_start_time($quest->id, time());
			}

			// Convert from object to array
			$json_return['quest'] = get_object_vars($quest);
			$json_return['quest']['has_answer_box'] = (bool)$json_return['quest']['has_answer_box'];
			$json_return['quest']['html'] = $this->_get_correct_html($quest);

			$json_return['success'] = TRUE;
			echo json_encode($json_return);
		}		
	}
	
	/**
	 * Fix all shortcode links
	 * @param quest the quest
	 * @return fixed all shortcodes
	 */
	private function _get_correct_html($quest) {
		$LINK_REGEX = '/\[(.+?)\]\((.+?)\)/';
		$IMG_REGEX = '/!\((.+?)\)/';
		
		$html = $quest->html;
		$is_php = (bool) $quest->html_is_php;

		// Fix php code
		if ($is_php) {
			$html = eval($html);
		}
		$arc_id = $quest->arc_id;

		// Replace links
		$replace_url = base_url('assets/game/' . $arc_id . '/$2');
		$link_replacement = '<a href="' . $replace_url . '">$1</a>';
		$html = preg_replace($LINK_REGEX, $link_replacement, $html);

		// Replace images
		$replace_url = base_url('assets/game/' . $arc_id . '/$1');
		$img_replacement = '<img style="width: 80%;" src="' . $replace_url . '"/>';
		$html = preg_replace($IMG_REGEX, $img_replacement, $html);

		return $html;
	}
	
	public function get_hints() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$json_return['success'] = TRUE;
		
		if ($this->_current_quest_id == null) {
			echo json_encode($json_return);
			return;
		}

		$team = $this->team->get_team($this->team_info->get_id());

		// Get the next hint
		$current_hint = $this->hint->get_hint($team->current_hint);
		if ($current_hint === null) {
			$next_hint = $this->hint->get_first_hint($team->current_quest_id);
		} else {
			$next_hint = $this->hint->get_next_hint($team->current_hint);
		}


		// Move to next hint if enough time has passed
		if ($next_hint !== null && $next_hint->time > 0) {
			$time_since_started = time() - $team->started_quest;

			if ($time_since_started > $next_hint->time) {
				$this->team->set_hint($this->team_info->get_id(), $next_hint->id);
			}
		}
		

		// Check which hints shall be shown
		if ($team->current_hint !== null) {
			$hints = $this->hint->get_hints($team->current_quest_id);

			foreach ($hints as $hint) {
				$json_return['hint'][$hint->order - 1] = $hint->text;

				// Found current hint, don't show the rest
				if ($hint->id == $team->current_hint) {
					break;
				}
			}
		}	

		echo json_encode($json_return);
	}

	public function next_hint() {
		$team_id = $this->team_info->get_id();

		// Get the team's current hint
		$current_hint_id = $this->team->get_current_hint($team_id);

		// Get the next hint
		if ($current_hint_id === null) {
			$next_hint = $this->hint->get_first_hint($this->_current_quest_id);
		} else {
			$next_hint = $this->hint->get_next_hint($current_hint_id);
		}

		if ($next_hint !== null && $next_hint->skippable == 1) {
			$this->team->set_hint($team_id, $next_hint->id);
		}

		$json_return['success'] = TRUE;
		echo json_encode($json_return);
	}

	public function try_html() {
		$this->load_view('try_html', NULL);
	}

	/**
	 * Checks if the arc has started
	 */
	private function _has_arc_started() {
		$this->load->model('Arcs', 'arc');

		$arc = $this->arc->get_latest();
		if ($arc !== FALSE) {
			return $arc->start_time !== NULL;
		} else {
			return FALSE;
		}
	}

	/**
	 * Checks if the arc has finished
	 */ 
	private function _has_arc_ended() {
		$this->load->model('Arcs', 'arc');

		$arc = $this->arc->get_latest();
		if ($arc !== FALSE) {
			$endTime = $arc->start_time + $arc->length;
			return time() >= $endTime;
		} else {
			return FALSE;
		}
	}

	public function completed() {
		// Not completed, redirect
		if ($this->_current_quest_id === '0') {
			$this->load_view('completed', NULL);
		} elseif ($this->_has_arc_ended()) {
			$this->load_view('ended', NULL);
		} else {
			redirect('game', 'refresh');
		}
	}

	public function try_answer() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$json_return['success'] = FALSE;

		// Check if team can answer (time)
		$team = $this->team->get_team($this->team_info->get_id());
		$time_diff = (time() - $team->last_answered);
		if ($time_diff < self::ANSWER_DELAY) {
			$time_left = self::ANSWER_DELAY - $time_diff;
			add_error_json('You still have <span class="time_left">' . $time_left . '</span> seconds before you can answer.', $json_return);
			$json_return['time_left'] = $time_left;
	
		}

		// Check if it was the right answer
		else if ($this->quest->is_right_answer($this->_current_quest_id, $this->input->post('answer'))) {
			$this->_goto_next_quest();
			$json_return['success'] = TRUE;
			set_success_json('Correct answer! :D', $json_return);
		} else {
			add_error_json('Wrong answer please try again in <span class="time_left">20</span> seconds.', $json_return);
			$json_return['time_left'] = self::ANSWER_DELAY;
			$this->team->update_last_answered($this->team_info->get_id(), time());
		}

		echo json_encode($json_return);
	}

	private function _goto_next_quest() {
		// Add points to the team
		$points = $this->_calculate_points();
		$this->team->add_points($this->team_info->get_id(), $points);

		// Set new quest
		$next_quest_id = $this->quest->get_next_quest_id($this->_current_quest_id);
		$this->team->set_current_quest($this->team_info->get_id(), $next_quest_id);
		$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
	}

	private function _calculate_points() {
		$quest = $this->quest->get_quest($this->_current_quest_id);
		$points = $quest->points;

		// Remove shown hint points
		$team = $this->team->get_team($this->team_info->get_id());
		$current_hint = $this->hint->get_hint($team->current_hint);

		if ($current_hint !== null) {
			$hints = $this->hint->get_hints($this->_current_quest_id);

			$hint_penalty = 0;
			foreach ($hints as $hint) {
				$hint_penalty += $hint->point_deduction;

				if ($hint->id == $current_hint->id) {
					break;
				}
			}
			$points -= $hint_penalty;
		}

		return $points;
	}

	private $_current_quest_id = NULL;
	const ANSWER_DELAY = 20;
}
