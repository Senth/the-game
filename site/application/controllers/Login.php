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
		} elseif ($this->user_info->is_logged_in()) {
			redirect('admin/arc', 'refresh');
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
		$this->user_info->logout();
		$this->save_session();
		redirect('login', 'refresh');
	}

	private function _validate_login() {
		$this->load->model('Teams', 'team');
		$this->load->model('Users', 'user');

		// Check if credentials match
		$team_id = $this->team->validate(
			$this->input->post('team'),
			$this->input->post('password')
		);

		$user_id = $this->user->validate(
			$this->input->post('team'),
			$this->input->post('password')
		);

		if ($team_id !== FALSE) {
			$this->_login_team($team_id);
		} elseif ($user_id !== FALSE) {
			$this->_login_user($user_id);
		} else {
			add_error('no_user', 'There is no team with that password.');
			$this->_show_login();
		}
	}

	private function _show_login() {
		$content['login_data'] = 'login_data';
		$this->load_view('login', $content);
	}

	private function _login_team($team_id) {
		$team_data = $this->team->get_team($team_id);
		assert($team_data !== FALSE);
		log_message('debug', "Team $team_data->name successfully logged in");
		$this->team_info->login($team_id, $team_data->name);
		$this->save_session();

		// Redirect to game
		redirect('game');
	}

	private function _login_user($user_id) {
		$user_data = $this->user->get($user_id);
		assert($user_data !== FALSE);

		$this->user_info->login($user_id, $user_data->name);
		$this->save_session();

		redirect('admin/arc');
	}
}
