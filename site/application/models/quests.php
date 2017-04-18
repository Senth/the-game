<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quests extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get all quests of the specified arc
	 * @param arc_id the arc to get all quests from
	 * @return all quests of the specified arc
	 */
	public function get_all($arc_id) {
		log_message('debug', 'quests.get_all(' . $arc_id . ')');

		$this->db->from('quest');
		$this->db->where('arc_id', $arc_id);
		$this->db->order_by('main', 'asc');
		$this->db->order_by('sub', 'asc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function get_quest($id) {
		$this->db->from('quest');
		$this->db->where('id', $id);

		$query = $this->db->get();

		// Return row
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return FALSE;
		}
	}

	public function get_first_quest() {
		$this->db->from('quest');
		$this->db->where('main', 1);
		$this->db->where('sub', 1);
		$this->db->order_by('arc_id', 'desc');
		$this->db->limit(1);
		
		$query = $this->db->get();

		// Return row
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return FALSE;
		}
	}

	public function get_next_quest_id($quest_id) {
		$this->db->select('main');
		$this->db->select('sub');
		$this->db->select('arc_id');
		$this->db->from('quest');
		$this->db->where('id', $quest_id);

		$query = $this->db->get();

		$next_main = (int) $query->row()->main;
		$next_sub = (int) $query->row()->sub;
		$arc_id = $query->row()->arc_id;


		// Check if there exist another sub
		$next_sub++;

		$next_id = $this->_get_next_quest_id($arc_id, $next_main, $next_sub);
		if ($next_id != 0) {
			return $next_id;
		}

		// Else check for another main
		$next_main++;
		$next_sub = 1;

		return $this->_get_next_quest_id($arc_id, $next_main, $next_sub);
	}

	/**
	 * Checks if there exist a quest with the specified main and sub
	 * @param arc_id id of the arc to search in
	 * @param main the main of the quest
	 * @param sub the sub of the quest
	 * @return id of the quest or 0 if none was found
	 */ 
	private function _get_next_quest_id($arc_id, $main, $sub) {
		$this->db->select('id');
		$this->db->from('quest');
		$this->db->where('main', $main);
		$this->db->where('sub', $sub);
		$this->db->where('arc_id', $arc_id);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row()->id;
		} else {
			return 0;
		}
	}

	public function is_right_answer($quest_id, $answer) {
		$this->db->select('id');
		$this->db->from('quest');
		$this->db->where('id', $quest_id);
		$this->db->where('answer', $answer);
		log_message('debug', 'Trying answer, Quest id: ' . $quest_id . ', answer: ' . $answer);

		$query = $this->db->get();

		return $query->num_rows() == 1;
	}

	public function set_start_time($quest_id, $time) {
		$this->db->set('start_time', $time);
		$this->db->where('id', $quest_id);
		$this->db->update('quest');
	}

	public function set_first_team($quest_id, $team_id) {
		$this->db->set('first_team_id', $team_id);
		$this->db->where('id', $quest_id);
		$this->db->update('quest');
	}

	public function update($id, $name, $main, $sub, $html, $is_php, $answer, $points, $points_first) {
		$this->db->set('name', $name);
		$this->db->set('main', $main);
		$this->db->set('sub', $sub);
		log_message('debug', 'HTML: ' . $html);
		$this->db->set('html', $html);
		$this->db->set('html_is_php', $is_php ? 1 : 0);
		$this->db->set('answer', $answer);
		$this->db->set('point_standard', $points);
		$this->db->set('point_first_extra', $points_first);
		$this->db->where('id', $id);
		$this->db->update('quest');
	}

	/**
	 * Reset all quests with the specified arc id
	 * @param arc_id
	 */ 
	public function reset($arc_id) {
		$this->db->set('first_team_id', null);
		$this->db->set('start_time', null);
		$this->db->where('arc_id', $arc_id);
		$this->db->update('quest');
	}

	public function insert($arc_id) {
		$this->db->set('arc_id', $arc_id);
		$this->db->set('html', '');
		$this->db->insert('quest');
	}

	public function delete($id) {
		$this->db->where('id', $id);
		$this->db->delete('quest');
	}
}
