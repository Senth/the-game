<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// TODO extend GAME_Controller instead
class Login extends GAME_Controller {

	public function index() {
		// Redirect to game page if user is logged in.
		if ($this->user_info->is_logged_in()) {
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
		$this->user_info->logout();
		$this->session->set_userdata('user', $this->user_info);
		redirect('login', 'refresh');
	}

	private function _validate_login() {
		$this->load->model('team', 'team');

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
		$this->user_info->login($team_id, $team_data->name);
		$this->session->set_userdata('user', $this->user_info);

		// Redirect to game
		redirect('game');
	}
}
