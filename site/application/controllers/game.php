<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game extends GAME_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Team', 'team');
		$this->load->model('Quest', 'quest');
		log_message('debug', 'Game loaded successfully!');
	}

	public function index() {
		// Redirect to login page if user is not logged in
		if (!$this->user_info->is_logged_in()) {
			redirect('login', 'refresh');
		}
		
		if ($this->input->post('answer') !== FALSE) {
			$this->_try_answer($this->_current_quest());
		}
		
		$current_quest_id = NULL;
		while ($current_quest_id === NULL) {

			$current_quest_id = $this->_current_quest();

			assert($current_quest_id !== FALSE);

			// No quest, atm, set the user to get the first quest (1-1)
			if ($current_quest_id === NULL) {
				$first_quest = $this->quest->get_first_quest();
				$this->team->set_current_quest($this->user_info->get_id(), $first_quest->id);
			}
		}

		// Done with whole game
		if ($current_quest_id === '0') {
			redirect('game/completed', 'refresh');
		}
		// Show current quest
		else {
			$quest = $this->quest->get_quest($current_quest_id);

			$content = get_object_vars($quest);
			$content['html_is_php'] = (bool)$content['html_is_php'];
			$content['has_answer_box'] = (bool)$content['has_answer_box'];
			$this->load_view('game', $content);
		}
	}

	public function completed() {
		// Not completed, redirect
		if ($this->_current_quest() !== '0') {
			redirect('game', 'refresh');
		}

		$this->load_view('completed', NULL);
	}

	private function _current_quest() {
		return $this->team->get_current_quest($this->user_info->get_id());
	}

	private function _try_answer() {
		// Check if team can answer (time)
		$team = $this->team->get_team($this->user_info->get_id());
		$time_diff = time() - $team->last_answered;
		if ($time_diff < 20) {
			add_error('time', 'You still have ' . (20 - $time_diff) . ' seconds before you can answer.');
		}

		// Check if it was the right answer
		else if ($this->quest->is_right_answer($this->_current_quest(), $this->input->post('answer'))) {
			$this->_goto_next_quest();
		} else {
			add_error('wrong_anwser', 'Wrong answer please try again in 20 seconds.');
			$this->team->update_last_answered($this->user_info->get_id(), time());
		}
	}

	private function _goto_next_quest() {
		// TODO add points


		$next_quest_id = $this->quest->get_next_quest_id($this->_current_quest());

		// Set new quest
		$this->team->set_current_quest($this->user_info->get_id(), $next_quest_id);
	}
}
