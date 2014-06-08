<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Create, delete and rename arcs.
 */
class Arc extends GAME_Controller {
	/**
	 * Loads arc model
	 */ 
	public function __construct() {
		parent::__construct();
		$this->load->model('arcs', 'arc');
	}

	/**
	 * Index page, create empty table
	 */ 
	public function index() {
		$this->load_view('admin/arc');
	}

	/**
	 * Get all arcs
	 */
	public function get_arcs() {
		log_message('debug', 'arc::get_arcs()');
		$arcs = $this->arc->get_all();
		log_message('debug', 'arc::get_arcs() - after model');

		if ($arcs !== FALSE) {
			$json_return['success'] = TRUE;
			$json_return['arcs'] = $arcs;
		} else {
			$json_return['success'] = FALSE;
		}
		
		echo json_encode($json_return);
	}

	/**
	 * Add a new arc
	 */ 
	public function add() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$id = $this->arc->add($this->input->post('name'));

		if (isset($id)) {
			$json_return['success'] = TRUE;
			$json_return['arc_id'] = $id;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}
}
