<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model for interacting with arcs
 */
class Arcs extends CI_Model {

	/**
	 * Set the start time of the arc
	 * @param id
	 * @param start_time
	 */ 
	public function set_start_time($id, $start_time) {
		$this->db->set('start_time', $start_time);
		$this->db->where('id', $id);
		$this->db->update('arc');
	}

	/**
	 * @return all existing arcs, null if none was found
	 */
	public function get_all() {
		$this->db->from('arc');

		return $this->db->get()->result();
	}

	/**
	 * Get the latest arc
	 * @return latest arc, null if none was found
	 */ 
	public function get_latest() {
		$this->db->from('arc');
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	/**
	 * Get arc information
	 * @param id arc id
	 * @return arc with the specified id, FALSE if no arc was found with that id
	 */
	public function get($id) {
		$this->db->from('arc');
		$this->db->where('id', $id);

		return $this->db->get()->row();
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

	public function update($id, $name, $length) {
		$this->db->set('name', $name);
		$this->db->set('length', $length);
		$this->db->where('id', $id);
		$this->db->update('arc');
	}
}
