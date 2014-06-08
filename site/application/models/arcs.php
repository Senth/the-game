<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model for interacting with arcs
 */
class Arcs extends CI_Model {

	/**
	 * @return all existing arcs, FALSE if none was found
	 */
	public function get_all() {
		$this->db->from('arc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	/**
	 * Add a new arc
	 * @param name name of the arc
	 * @return id of the newly created arc
	 */
	public function add($name) {
		$this->db->set('name', $name);
		$this->db->insert('arc');
		return $this->db->insert_id();
	}
}
