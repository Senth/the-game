<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for adding, changing and removing quests
 */ 
class Quest extends GAME_Controller {
	/**
	 * Loads quest and hint models
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('Hints', 'hint');
		$this->load->model('Quests', 'quest');
	}

	/**
	 * View all quests of the specified arc
	 * @param arc_id
	 */ 
	public function arc($arc_id) {
		$data['arc_id'] = $arc_id;
		$this->load_view('admin/quest', $data);
	}

	/**
	 * Get all quests of the specified arc
	 */
	public function get_all() {
		$quests = $this->quest->get_all($this->input->post('arc_id'));

		// Get all hints of the current quest
		foreach ($quests as $quest) {
			$quest->hints = $this->hint->get_hints($quest->id);
		}

		$json_success['success'] = TRUE;
		$json_success['arcs'] = $quests;
	}
}
