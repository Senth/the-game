<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base class of all controllers in GAME
 */ 
class GAME_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->library('session');

		// Get session team info
		$team = $this->session->userdata('team');
		if ($team != null) {
			$this->team_info = $team;
		} else {
			$this->team_info = new User_info();
		}

		// Get session user info
		$user = $this->session->userdata('user');
		if ($user != null) {
			$this->user_info = $user;
		} else {
			$this->user_info = new User_info();
		}

		$this->_validate_access();
	}

	/**
	 * Saves session variables if they have been changed
	 */ 
	protected function save_session() {
		// Set session team data if changed
		if ($this->team_info->has_changed()) {
			$this->team_info->set_not_changed();
			$this->session->set_userdata('team', $this->team_info);
		}

		// Set session user data if changed
		if ($this->user_info->has_changed()) {
			$this->user_info->set_not_changed();
			$this->session->set_userdata('user', $this->user_info);
		}
	}

	/**
	 * Loads the specified view with the passed content information
	 * @param view view to load (as content page)
	 * @param data information for the content
	 */ 
	protected function load_view($view, $data = NULL) {
		$inner_content = array(
			'view' => $view,
			'data' => $data
		);

		$view_data = array(
			'inner_content' => $inner_content,
			'team_info' => $this->team_info,
			'user_info' => $this->user_info
		);

		$this->load->view('template/index', $view_data);
	}

	/**
	 * Validate if the user/team has access to the current page
	 */
	protected function _validate_access() {
		$uri = uri_string();

		if ($uri == '') {
			$uri = 'game';
		}

		$validation_method_needed = FALSE;

		$patterns = array(
			'/game/' => 'team',
			'/admin\/.*/' => 'user'
		);

		foreach ($patterns as $pattern => $validation_method) {
			$success = preg_match($pattern, $uri);

			if ($success !== FALSE && $success == 1) {
				$validation_method_needed = $validation_method;
			}
		}

		if ($validation_method_needed !== FALSE) {
			$redirect = FALSE;

			if ($validation_method_needed == 'team' && !$this->team_info->is_logged_in()) {
				$redirect = TRUE;
			} elseif ($validation_method_needed == 'user' && !$this->user_info->is_logged_in()) {
				$redirect = TRUE;
			}


			if ($redirect) {
				redirect('login', 'refresh');
			}
		}
	}

	protected $team_info;
}
