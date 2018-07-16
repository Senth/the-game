<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Create, delete and rename arcs.
 */
class Upgrade extends CI_Controller {
	const VERSION = 102000;

	/**
	 * Loads arc model
	 */ 
	public function __construct() {
		parent::__construct();
		$this->load->model('Versions', 'version');
	}

	/**
	 * Try to upgrade the DB if needed
	 */ 
	public function index() {
		// Get current version of DB
		$db_version = $this->version->get();

		// 0 -> 1.2
		if ($db_version == 0) {
			$this->version->upgrade_0_to_1_2();
		}

		// Set DB version table
		$this->version->set(self::VERSION);

		redirect('login', 'refresh');

		$data['state'] = 'Upgrade successful!';
		$this->_load_view('upgrade', $data);
	}
	
	/**
	 * Loads the specified view with the passed content information
	 * @param view view to load (as content page)
	 * @param data information for the content
	 */ 
	private function _load_view($view, $data = NULL) {
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
}
