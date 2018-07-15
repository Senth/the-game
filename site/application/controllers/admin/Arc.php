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
		$this->load->model('Arcs', 'arc');
	}

	/**
	 * Index page, create empty table
	 */ 
	public function index() {
		$this->load_view('admin/arc');
	}

	/**
	 * Reset/Restart the arc
	 */ 
	public function reset() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$arc_id = $this->input->post('arc_id');

		if ($arc_id !== FALSE) {
			// Reset arc
			$this->arc->set_start_time($arc_id, null);

			// Reset quests
			$this->load->model('Quests', 'quest');
			$this->quest->reset($arc_id);

			$json_return['success'] = TRUE;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

	/**
	 * Start an arc
	 */ 
	public function start_arc() {
		log_message('debug', 'arc::start_arc()');
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$arc_id = $this->input->post('arc_id');
		log_message('debug', 'arc::start_arc() - here');

		if ($arc_id != NULL) {
			$this->arc->set_start_time($arc_id, time());
			log_message('debug', 'arc::start_arc() - update success');
			$json_return['success'] = TRUE;
		} else {
			log_message('debug', 'arc::start_arc() - no arc id');
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
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
			
			// Get arc length
			$arc = $this->arc->get($id);
			$json_return['length'] = $arc->length;
			$json_return['arc_id'] = $id;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

	/**
	 * Edit an arc
	 */
	public function edit() {
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$length = $this->input->post('length');

		$this->arc->update($id, $name, $length);

		$json_return['success'] = TRUE;
		echo json_encode($json_return);
	}
}
