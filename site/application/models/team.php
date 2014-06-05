<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model interacting with the user table
 */
class Team extends CI_Model {
	/**
	 * Constructor, does nothing
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Checks if the user credentials are correct
	 * @param name the user's name
	 * @param password the user's password
	 * @return if the user credentials are correct an id of the
	 * 		user is returned, else false is returned
	 */ 
	public function validate($name, $password) {
		$this->db->select('id');
		$this->db->from('team');
		$this->db->where('name', $name);
		$this->db->where('password', md5($password));

		$query = $this->db->get();

		// Return the id
		if ($query->num_rows() == 1) {
			return $query->row()->id;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns an array with all the user information
	 * @param id the user's id to fetch information about
	 * @return array with all the user information, false if the user doesn't exist.
	 */
	public function get_team($id) {
		$this->db->from('team');
		$this->db->where('id', $id);

		$query = $this->db->get();

		if ($query->num_rows() === 1) {
			$row = $query->row();
			return $row;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns the current quest of the team
	 */
	public function get_current_quest($team_id) {
		$this->db->select('current_quest_id');
		$this->db->from('team');
		$this->db->where('id', $team_id);
		
		$query = $this->db->get();

		if ($query->num_rows() === 1) {
			$row = $query->row()->current_quest_id;
			return $row;
		} else {
			return FALSE;
		}
	}

	public function set_current_quest($team_id, $quest_id) {
		$this->db->set('current_quest_id', $quest_id);
		$this->db->set('started_quest', time());
		$this->db->set('last_answered', 0);
		$this->db->where('id', $team_id);

		$this->db->update('team');
	}

	public function update_last_answered($id, $time) {
		$this->db->set('last_answered', $time);
		$this->db->where('id', $id);
		$this->db->update('team');
	}
}
