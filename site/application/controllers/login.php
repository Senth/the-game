<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends GAME_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		// Redirect to game page if team is logged in.
		if ($this->team_info->is_logged_in()) {
			log_message('debug', 'Already logged in, redirecting to game');
			redirect('game', 'refresh');
		}

		// Show login if tried to login
		if ($this->input->post('login') === FALSE) {
			$this->_show_login();
			return;
		} else {
			$this->_validate_login();
		}
	}

	public function logout() {
		$this->team_info->logout();
		$this->save_session();
		redirect('login', 'refresh');
	}

	private function _validate_login() {
		$this->load->model('Teams', 'team');

		// Check if credentials match
		$team_id = $this->team->validate(
			$this->input->post('team'),
			$this->input->post('password')
		);

		// Did not match, post error and show login
		if ($team_id === FALSE) {
			add_error('no_user', 'There is no team with that password.');
			$this->_show_login();
		} else {
			$this->_login($team_id);
		}
	}

	private function _show_login() {
		$content['login_data'] = 'login_data';
		$this->load_view('login', $content);
	}

	private function _login($team_id) {
		$team_data = $this->team->get_team($team_id);
		assert($team_data !== FALSE);
		log_message('debug', "Team $team_data->name successfully logged in");
		$this->team_info->login($team_id, $team_data->name);
		$this->save_session();

		// Redirect to game
		redirect('game');
	}
}
