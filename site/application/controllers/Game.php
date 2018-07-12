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

		// Check if hints shall be shown
		$team = $this->team->get_team($this->team_info->get_id());
		$time_since_started = time() - $team->started_quest;

		if ($this->_current_quest_id !== NULL) {
			$hints = $this->hint->get_hints($this->_current_quest_id);
			$i = 0;
			foreach ($hints as $hint) {
				if ($time_since_started >= $hint->time) {
					$json_return['hint'][$i] = $hint->text;
					$i++;
				}
			}
		}

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
		log_message('debug', 'check for right answer');
		if ($time_diff < self::ANSWER_DELAY) {
			$time_left = self::ANSWER_DELAY - $time_diff;
			add_error_json('You still have <span class="time_left">' . $time_left . '</span> seconds before you can answer.', $json_return);
			$json_return['time_left'] = $time_left;
	
		}

		// Check if it was the right answer
		else if ($this->quest->is_right_answer($this->_current_quest_id, $this->input->post('answer'))) {
			log_message('debug', 'correct answer');
			$this->_goto_next_quest();
			log_message('debug', 'Game::try_answer() - after goto_next_quest()');
			$json_return['success'] = TRUE;
			set_success_json('Correct answer! :D', $json_return);
			log_message('debug', 'Game::try_answer() - added json return message');
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

		// Answered first? Set first_team_id then
		$quest = $this->quest->get_quest($this->_current_quest_id);
		if ($quest->first_team_id === NULL) {
			$this->quest->set_first_team($quest->id, $this->team_info->get_id());
		}

		// Set new quest
		$next_quest_id = $this->quest->get_next_quest_id($this->_current_quest_id);
		$this->team->set_current_quest($this->team_info->get_id(), $next_quest_id);
		$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
	}

	private function _calculate_points() {
		$quest = $this->quest->get_quest($this->_current_quest_id);
		$points = $quest->point_standard;

		// Check if hints can be seen, use their points instead then
		$team = $this->team->get_team($this->team_info->get_id());
		$time_since_started = time() - $team->started_quest;

		$hints = $this->hint->get_hints($this->_current_quest_id);

		$hint_penalty = 0;
		foreach ($hints as $hint) {
			if ($time_since_started >= $hint->time) {
				$hint_penalty += $hint->point_deduction;
			}
		}
		$points -= $hint_penalty;

		// Are we first?
		$team_first = 0;
		if ($quest->first_team_id === NULL) {
			$team_first = $quest->point_first_extra;
		}
		$points += $team_first;

		return $points;
	}

	private $_current_quest_id = NULL;
	const ANSWER_DELAY = 20;
}
