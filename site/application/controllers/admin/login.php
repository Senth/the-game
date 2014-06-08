<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administration login
 */ 
class Login extends GAME_Controller {

	/**
	 * Does nothing
	 */ 
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Index page
	 */ 
	public function index() {
		// Redirect to admin page if user is logged in
		if ($this->user_info->is_logged_in()) {
			log_message('debug', 'Already logged in, redirecting to arc page');
			redirect(self::REDIRECT_LOGGED_IN, 'refresh');
		}
		// Show login form
		else {
			$this->load_view('admin/login');
		}
	}

	/**
	 * Tries to login
	 */ 
	public function validate_login() {
		// Only allow ajax
		if ($this->input->post('ajax') === false) {
			return;
		}

		$json_return['success'] = FALSE;

		$this->load->model('user', 'user');

		// Check if credentials match
		$user_id = $this->user->validate(
			$this->input->post('username'),
			$this->input->post('password')
		);

		// Did not match, post error and show login
		if ($user_id === FALSE) {
			add_error_json('There is no team with that password.', $json_return);
		} else {
			$this->_login($user_id);
			$json_return['success'] = TRUE;
			$json_return['redirect'] = self::REDIRECT_LOGGED_IN;
		}

		echo json_encode($json_return);
	}

	public function logout() {
		log_message('debug', 'Admin::logout()');
		$this->user_info->logout();
		$this->save_session();
		log_message('debug', 'Admin::logout() - Logged out, refreshing');
		redirect('game', 'refresh');
	}

	/**
	 * Login the specified user
	 */
	private function _login($user_id) {
		$user_data = $this->user->get($user_id);
		assert($user_data !== FALSE);
		$this->user_info->login($user_id, $user_data->name);
		$this->save_session();
	}

	const REDIRECT_LOGGED_IN = 'admin/arc';
}
