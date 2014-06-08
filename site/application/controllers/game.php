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
		// Redirect to login page if user is not logged in
		if (!$this->team_info->is_logged_in()) {
			log_message('debug', 'Not logged in, redirecting to login page');
			redirect('login', 'refresh');
		}
		
		log_message('debug', 'game view');
		$this->load_view('game', NULL);
	}

	public function get_quest() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$json_return['success'] = FALSE;


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


			$json_return['success'] = TRUE;
			echo json_encode($json_return);
		}		
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

		$hints = $this->hint->get_hints($this->_current_quest_id);
		$i = 0;
		foreach ($hints as $hint) {
			if ($time_since_started >= $hint['time']) {
				$json_return['hint'][$i] = $hint['text'];
				$i++;
			}
		}

		echo json_encode($json_return);
	}

	public function try_html() {
		$this->load_view('try_html', NULL);
	}

	public function completed() {
		// Not completed, redirect
		if ($this->_current_quest_id !== '0') {
			redirect('game', 'refresh');
		}

		$this->load_view('completed', NULL);
	}

	public function get_sidebar() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$json_return['success'] = FALSE;

		if ($this->team_info->is_logged_in()) {
			$team = $this->team->get_team($this->team_info->get_id());
			$json_return['points'] = $team->points;

			$quest = $this->quest->get_quest($this->_current_quest_id);
			$json_return['quest_worth'] = $this->_calculate_points($json_return);


			// hints
			$hint_time = -1;
			$hint_point_deduction = 0;

			$team = $this->team->get_team($this->team_info->get_id());
			$time_since_started = time() - $team->started_quest;

			$hints = $this->hint->get_hints($this->_current_quest_id);

			foreach ($hints as $hint) {
				if ($time_since_started <= $hint['time']) {
					$hint_time = $hint['time'] - $time_since_started;
					$hint_point_deduction = $hint['point_deduction'];
					break;
				}
			}
			if ($hint_time == -1) {
				$hint_time = 'No hints left';
			}

			$json_return['hint_next'] = $hint_time;
			$json_return['hint_next_penalty'] = $hint_point_deduction;
			log_message('debug', 'Hint penalty: ' . $hint_point_deduction);
		} else {
			$json_return['points'] = 0;
			$json_return['quest_worth'] = 0;
			$json_return['hint_next'] = 0;
			$json_return['hint_next_penalty'] = 0;
			$json_return['point_default'] = 0;
			$json_return['point_first'] = 0;
			$json_return['point_hint_penalty'] = 0;

		}

		// Get team table
		$teams = $this->team->get_teams();

		// Replace quest with main-sub instead of id
		$c_teams = count($teams);
		for ($i = 0; $i < $c_teams; $i++) {
			if ($teams[$i]['quest'] === NULL) {
				$teams[$i]['quest'] = 'Not Started';
			} else if ($teams[$i]['quest'] === '0') {
				$teams[$i]['quest'] = 'Done!';
			} else {
				// Get quest info
				$quest = $this->quest->get_quest($teams[$i]['quest']);
				$teams[$i]['quest'] = $quest->main . '-' . $quest->sub;
				log_message('debug', 'Quest main: ' . $quest->main . ', sub: ' . $quest->sub);
			}
		}
		$json_return['teams'] = $teams;
		$json_return['c_teams'] = $c_teams;

		$json_return['success'] = TRUE;
		echo json_encode($json_return);
	}

	public function try_answer() {
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			echo 'not ajax';
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
			$json_return['success'] = TRUE;
			add_success_json('Correct answer! :D', $json_return);
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
		$quest = $this->quest->get_quest($this->team_info->get_id());
		if ($quest->first_team_id === NULL) {
			$this->quest->set_first_team($quest->id, $this->team_info->get_id());
		}

		log_message('debug', '_goto_next_quest() - set new quest');
		// Set new quest
		$next_quest_id = $this->quest->get_next_quest_id($this->_current_quest_id);
		$this->team->set_current_quest($this->team_info->get_id(), $next_quest_id);
		$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
	}

	private function _calculate_points(&$json_return = NULL) {
		$quest = $this->quest->get_quest($this->_current_quest_id);
		$points = $quest->point_standard;

		if (isset($json_return)) {
			$json_return['point_default'] = $points;
		}
	
		// Check if hints can be seen, use their points instead then
		$team = $this->team->get_team($this->team_info->get_id());
		$time_since_started = time() - $team->started_quest;

		$hints = $this->hint->get_hints($this->_current_quest_id);

		$hint_penalty = 0;
		foreach ($hints as $hint) {
			if ($time_since_started >= $hint['time']) {
				$hint_penalty += $hint['point_deduction'];
			}
		}
		$points -= $hint_penalty;

		if (isset($json_return)) {
			$json_return['point_hint_penalty'] = $hint_penalty;
		}

		// Are we first?
		$team_first = 0;
		if ($quest->first_team_id === NULL) {
			$team_first = $quest->point_first_extra;
		}
		$points += $team_first;
		if (isset($json_return)) {
			$json_return['point_first'] = $team_first;
		}

		return $points;
	}

	private $_current_quest_id;
	const ANSWER_DELAY = 20;
}
