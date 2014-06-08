<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hint extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function get_hints($quest_id) {
		$this->db->from('hint');
		$this->db->where('quest_id', $quest_id);
		$this->db->order_by('time', 'asc');

		$query = $this->db->get();

		$i = 0;
		$result = null;
		foreach ($query->result() as $row) {
			$result[$i]['text'] = $row->text;
			$result[$i]['time'] = $row->time;
			$result[$i]['point_deduction'] = $row->point_deduction;
			$i++;
		}

		return $result;
	}
}
