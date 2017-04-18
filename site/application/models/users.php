<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model interacting with users
 */
class Users extends CI_Model {
	/**
	 * Checks if the user credintials are correct
	 * @param username
	 * @param password
	 * @return user id if a user exists with this password, else false is returned
	 */
	public function validate($username, $password) {
		$this->db->select('id');
		$this->db->from('user');
		$this->db->where('name', $username);
		$this->db->where('password', md5($password));

		$query = $this->db->get();

		// return id
		if ($query->num_rows() === 1) {
			return $query->row()->id;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get all user information
	 * @param id user id
	 * @return object with all user information, false if no user with this id exists
	 */ 
	public function get($id) {
		$this->db->from('user');
		$this->db->where('id', $id);

		$query = $this->db->get();

		if ($query->num_rows() === 1) {
			return $query->row();
		} else {
			return FALSE;
		}
	}
}
