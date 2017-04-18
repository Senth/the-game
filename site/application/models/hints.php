<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hints extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function get_hints($quest_id) {
		$this->db->from('hint');
		$this->db->where('quest_id', $quest_id);
		$this->db->order_by('time', 'asc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function insert($quest_id) {
		$this->db->set('quest_id', $quest_id);
		$this->db->insert('hint');
	}

	public function update($id, $text, $time, $points) {
		$this->db->set('text', $text);
		$this->db->set('time', $time);
		$this->db->set('point_deduction', $points);
		$this->db->where('id', $id);
		$this->db->update('hint');
	}

	public function delete($id) {
		$this->db->where('id', $id);
		$this->db->delete('hint');
	}
}
