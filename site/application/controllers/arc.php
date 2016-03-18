<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Arc extends GAME_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Arcs', 'arc');
	}

	/**
	 * Checks if the arc has started
	 */ 
	public function has_arc_started() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$arc = $this->arc->get_latest();
		if ($arc !== FALSE) {
			$json_return['arc_started'] = $arc->start_time != NULL;
			$json_return['success'] = TRUE;
		} else {
			$json_return['success'] = FALSE;
		}
		echo json_encode($json_return);
	}

	/**
	 * Get the time left for the quest
	 */
	public function get_arc_time_left() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$arc = $this->arc->get_latest();

		if ($arc !== FALSE) {
			$endTime = $arc->start_time + $arc->length;
			$json_return['arc_time_left'] = max(0, $endTime - time());
			$json_return['success'] = TRUE;
		} else {
			$json_return['success'] = FALSE;
		}
		echo json_encode($json_return);
	}

	/**
	 * Checks if the arc has finished
	 */ 
	public function has_arc_ended() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$arc = $this->arc->get_latest();
		if ($arc !== FALSE) {
			$endTime = $arc->start_time + $arc->length;
			$json_return['arc_ended'] = time() >= $endTime;
			$json_return['success'] = TRUE;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

}
