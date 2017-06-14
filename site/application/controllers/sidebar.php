<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sidebar extends GAME_Controller {
	public function __construct() {
		log_message('debug', 'Sidebar::__construct()');
		parent::__construct();
		$this->load->model('Teams', 'team');
		$this->load->model('Quests', 'quest');
		$this->load->model('Hints', 'hint');
		$this->load->model('Arcs', 'arc');

		if ($this->team_info->is_logged_in()) {
			$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
		}
	}

	public function get_info() {
		log_message('debug', 'Sidebar::get_info()');
		// Only handle ajax updates
		if ($this->input->post('ajax') === FALSE) {
			return;
		}


		$json_return['success'] = FALSE;

		if ($this->team_info->is_logged_in()) {
			if ($this->_current_quest_id === NULL) {
				log_message('debug', 'Getting new quest id');
				$this->_current_quest_id = $this->team->get_current_quest($this->team_info->get_id());
			}
			log_message('debug', 'Quest id: ' . $this->_current_quest_id);

			if ($this->_current_quest_id !== NULL) {
				log_message('debug', 'Sidebar::get_info() - team logged in');

				$team = $this->team->get_team($this->team_info->get_id());
				$json_return['points'] = $team->points;

				$quest = $this->quest->get_quest($this->_current_quest_id);
				log_message('debug', 'Sidebar::get_info() - before calculate points');
				$json_return['quest_worth'] = $this->_calculate_points($json_return);

				// Completed
				$this->_calculate_completed($quest->arc_id, $json_return);

				// hints
				$hint_time = -1;
				$hint_point_deduction = 0;

				$team = $this->team->get_team($this->team_info->get_id());
				$time_since_started = time() - $team->started_quest;

				$hints = $this->hint->get_hints($this->_current_quest_id);
				foreach ($hints as $hint) {
					if ($time_since_started <= $hint->time) {
						$hint_time = $hint->time - $time_since_started;
						$hint_point_deduction = $hint->point_deduction;
						break;
					}
				}
				if ($hint_time == -1) {
					$hint_time = 'No hints left';
				}

				$json_return['hint_next'] = $hint_time;
				$json_return['hint_next_penalty'] = $hint_point_deduction;
				log_message('debug', 'Sidebar::get_info() - Hint penalty: ' . $hint_point_deduction);
			} else {
				$json_return['points'] = 0;
				$json_return['quest_worth'] = 0;
				$json_return['point_default'] = 0;
				$json_return['point_first'] = 0;
				$json_return['quests_completed'] = 0;
				$json_return['quests_total'] = '??';
				$json_return['hint_next'] = 0;
				$json_return['hint_next_penalty'] = 0;
				$json_return['point_hint_penalty'] = 0;
			}
		}

		$this->_calculate_arc_time($json_return);

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

	/**
	 * Calculate number of completed quests
	 * @param arc_id
	 */ 
	private function _calculate_completed($arc_id, &$json_return) {
		$quests = $this->quest->get_all($arc_id);

		$completed = 0;
		$found_current = FALSE;
		foreach ($quests as $quest) {
			if (!$found_current) {
				if ($quest->id == $this->_current_quest_id) {
					$found_current = TRUE;
				} else {
					$completed++;
				}
			}
		}

		$json_return['quests_completed'] = $completed;
		$json_return['quests_total'] = count($quests);
	}

	/**
	 * Calculate time left for arc
	 */
	private function _calculate_arc_time(&$json_return) {
		$arc = $this->arc->get_latest();

		$json_return['arc_length'] = $arc->length;

		if ($arc->start_time != NULL) {
			$passed_time = time() - $arc->start_time;
			$json_return['arc_left'] = max($arc->length - $passed_time, 0);
		} else {
			$json_return['arc_left'] = $arc->length;
		}
	}

	/**
	 * Calculate points for the current quest
	 */ 
	private function _calculate_points(&$json_return = NULL) {
		if ($this->_current_quest_id === NULL) {
			log_message('debug', 'No current quest set');
			return;
		}

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
			if ($time_since_started >= $hint->time) {
				$hint_penalty += $hint->point_deduction;
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

	private $_current_quest_id = NULL;
}
