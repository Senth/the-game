<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Create, delete and rename arcs.
 */
class Team extends GAME_Controller {
	/**
	 * Loads arc model
	 */ 
	public function __construct() {
		parent::__construct();
		$this->load->model('Teams', 'team');
	}

	/**
	 * Index page
	 */
	public function index() {
		$this->load_view('admin/team');
	}

	/**
	 * Get teams
	 */ 
	public function get_teams() {
		$json_return['teams'] = $this->team->get_teams();
		echo json_encode($json_return);
	}

	/**
	 * Add a new team
	 */ 
	public function add() {
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$md5_password = md5($password);

		$json['id'] = $this->team->insert($name, $md5_password);
		echo json_encode($json_return);
	}

	/**
	 * Edit a team
	 */ 
	public function edit() {
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$active = $this->input->post('active');
		
		$password = $this->input->post('password');
		$md5_password = null;
		if ($password !== null) {
			$md5_password = md5($password);
		}

		$this->team->update($id, $name, $md5_password, $active);
	}
}
