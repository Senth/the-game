<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quest extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function get_quest($id) {
		$this->db->select('main');
		$this->db->select('sub');
		$this->db->select('html');
		$this->db->select('html_is_php');
		$this->db->select('first_team_id');
		$this->db->select('has_answer_box');
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
		$this->db->from('quest');
		$this->db->where('id', $quest_id);

		$query = $this->db->get();

		$next['main'] = (int) $query->row()->main;
		$next['sub'] = (int) $query->row()->sub;


		// Check if there exist another sub
		$next['sub']++;

		$this->db->select('id');
		$this->db->from('quest');
		$this->db->where('main', $next['main']);
		$this->db->where('sub', $next['sub']);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row()->id;
		}

		// Else check for another main
		$next['main']++;
		$next['sub'] = 1;
		
		$this->db->select('id');
		$this->db->from('quest');
		$this->db->where('main', $next['main']);
		$this->db->where('sub', $next['sub']);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row()->id;
		}

		// Else no additional quest exists
		return 0;
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
}
