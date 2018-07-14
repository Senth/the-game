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
		$this->load->model('Arcs', 'arc');

		$arc = $this->arc->get($arc_id);
		if ($arc !== FALSE) {
			$data['arc_name'] = $arc->name;
		}

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
	 * Get quest HTML
	 * @param quest_id 
	 */ 
	public function get_html($quest_id) {
		$quest = $this->quest->get_quest($quest_id);

		if ($quest !== FALSE) {
			$html = $this->_get_correct_html($quest);

			$json_return['html'] = $html;
			$json_return['success'] = TRUE;
		} else {
			$json_return['success'] = FALSE;
		}

		echo json_encode($json_return);
	}

	/**
	 * Fix all shortcode links
	 * @param quest the quest
	 * @return fixed all shortcodes
	 */
	private function _get_correct_html($quest) {
		$LINK_REGEX = '/\[(.+?)\]\((.+?)\)/';
		$IMG_REGEX = '/!\((.+?)\)/';
		
		$html = $quest->html;
		$is_php = (bool) $quest->html_is_php;

		// Fix php code
		if ($is_php) {
			$html = eval($html);
		}
		$arc_id = $quest->arc_id;

		// Replace links
		$replace_url = base_url('assets/game/' . $arc_id . '/$2');
		$link_replacement = '<a href="' . $replace_url . '">$1</a>';
		$html = preg_replace($LINK_REGEX, $link_replacement, $html);

		// Replace images
		$replace_url = base_url('assets/game/' . $arc_id . '/$1');
		$img_replacement = '<img style="width: 80%;" src="' . $replace_url . '"/>';
		$html = preg_replace($IMG_REGEX, $img_replacement, $html);

		return $html;
	}

	/**
	 * Get all quests of the specified arc
	 */
	public function get_all() {
		log_message('debug', 'quest.get_all() called');
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
		$this->quest->insert($this->input->post('arc_id'));

		$json_return['success'] = TRUE;

		echo json_encode($json_return);
	}

	/**
	 * Edit/Update main sub of a quest
	 */ 
	public function edit_main_sub() {
		$id = $this->input->post('id');
		$main = $this->input->post('main');
		$sub = $this->input->post('sub');

		$this->quest->update_main_sub($id, $main, $sub);
	}

	/**
	 * Edit/Update a quest
	 */
	public function edit() {
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$main = $this->input->post('main');
		$sub = $this->input->post('sub');
		$html = $this->input->post('html');
		$is_php = $this->input->post('is_php') == 'true' ? TRUE : FALSE;
		$answer = strtolower($this->input->post('answer'));
		$points = $this->input->post('points');

		$this->quest->update($id, $name, $main, $sub, $html, $is_php, $answer, $points);

		$json_return['success'] = TRUE;

		echo json_encode($json_return);
	}

	public function remove() {
		$id = $this->input->post('id');

		log_message('debug', 'Delete quest with id: ' . $id);
		$this->quest->delete($id);

		$json_return['success'] = TRUE;
		echo json_encode($json_return);
	}
}
