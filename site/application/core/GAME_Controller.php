<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Base class of all controllers in GAME
 */ 
class GAME_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();

		log_message('debug', 'GAME_Controller::__construct()');
		// Get session team info
		$team = $this->session->userdata('team');
		if ($team !== FALSE) {
			log_message('debug', 'GAME_Controller::__construct() - Using team session');
			$this->team_info = $team;
		} else {
			log_message('debug', 'GAME_Controller::__construct() - No team session found, creating new team');
			$this->team_info = new User_info();
		}

		// Get session user info
		$user = $this->session->userdata('user');
		if ($user !== FALSE) {
			log_message('debug', 'GAME_Controller::__construct() - Using user session');
			$this->user_info = $user;
		} else {
			log_message('debug', 'GAME_Controller::__construct() - No user session found, creating new user');
			$this->user_info = new User_info();
		}
		log_message('debug', 'GAME_Controller::__construct() - end');
	}

	/**
	 * Saves session variables if they have been changed
	 */ 
	protected function save_session() {
		log_message('debug', 'GAME_Controller::save_session()');
		// Set session team data if changed
		if ($this->team_info->has_changed()) {
			log_message('debug', 'GAME_Controller::save_session() - team changed');
			$this->team_info->set_not_changed();
			$this->session->set_userdata('team', $this->team_info);
		}

		// Set session user data if changed
		if ($this->user_info->has_changed()) {
			log_message('debug', 'GAME_Controller::save_session() - user changed');
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
			'team_info' => $this->team_info
		);

		$this->load->view('template/index', $view_data);
	}

	/**
	 * Validates the user access to the page. If the user doesn't have access
	 * an error page is displayed (the same as is called in #access_denied()).
	 * @param group_name the minimum required access level, should be a
	 * 	name and not the actual value.
	 */
	protected function validate_access($group_name) {
// 		$this->load->model('User', 'user');
// 		$level_required = $this->user->get_level_from_group($group_name);
// 
// 		if ($this->user_info->get_level() < $level_required) {
// 			$this->access_denied();
// 		}
	}

	/**
	 * Prints an access denied error
	 */
	protected function access_denied() {
			show_error('Access denied! You don\'t have access to this page. Try ' . anchor('login', 'logging in') . '...');	
	}

	protected $team_info;
}
