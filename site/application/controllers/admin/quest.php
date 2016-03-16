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
		$this->load_view('admin/quests', $data);
	}

	/**
	 * View a specific quest including its hints
	 * @param quest_id
	 */
	public function view($quest_id) {
		$data['quest_id'] = $quest_id;
		$this->load_view('admin/quest', $data);
	}

	/**
	 * Get a specified quest
	 * @param quest_id the specified quest to get
	 */ 
	public function get($quest_id) {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$quest = $this->quest->get_quest($quest_id);

		if ($quest !== FALSE) {
			$json_return['success'] = TRUE;
			$json_return['quest'] = $quest;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

	/**
	 * Get all quests of the specified arc
	 */
	public function get_all() {
		log_message('debug', 'quest.get_all() called');
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$quests = $this->quest->get_all($this->input->post('arc_id'));

		if ($quests !== FALSE) {
			log_message('debug', 'quest.get_all() Found ' . count($quests) . ' quests');
			// Get all hints of the current quest
			foreach ($quests as $quest) {
				$quest->hints = $this->hint->get_hints($quest->id);
			}

			$json_return['success'] = TRUE;
			$json_return['quests'] = $quests;
		} else {
			log_message('debug', 'quest.get_all() No quests found');
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

	/**
	 * Add a new empty quest
	 */ 
	public function add() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$this->quest->insert($this->input->post('arc_id'));

		$json_return['success'] = TRUE;

		echo json_encode($json_return);
	}

	/**
	 * Edit/Update a quest
	 */
	public function edit() {
		// Only handle ajax requests
		if ($this->input->post('ajax') === FALSE) {
			return;
		}

		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$main = $this->input->post('main');
		$sub = $this->input->post('sub');
		$html = $this->input->post('html');
		$is_php = $this->input->post('is_php') == 'true' ? TRUE : FALSE;
		$answer = $this->input->post('answer');
		$points = $this->input->post('points');
		$points_first = $this->input->post('points_first');

		$this->quest->update($id, $name, $main, $sub, $html, $is_php, $answer, $points, $points_first);

		$json_return['success'] = TRUE;

		echo json_encode($json_return);
	}
}
