<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model interacting with the teams
 */
class Teams extends CI_Model {
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
		$this->db->where('active', 1);
		$this->db->where('name', $name);
		$this->db->where('password', md5($password));

		$query = $this->db->get();

		log_message('debug', 'rows: ' . $query->num_rows());

		// Return the id
		if ($query->num_rows() == 1) {
			return $query->row()->id;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns an array with all the team information
	 * @param id the team's id to fetch information about
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

	public function get_teams() {
		$this->db->from('team');
		return $this->db->get()->result();
	}

	public function get_active_teams() {
		$this->db->from('team');
		$this->db->where('active', 1);
		$query = $this->db->get();

		$i = 0;
		$teams = array();
		foreach($query->result() as $row) {
			$teams[$i]['name'] = $row->name;
			$teams[$i]['points'] = $row->points;
			$teams[$i]['quest'] = $row->current_quest_id;
			$i++;
		}

		return $teams;
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
		$this->db->set('current_hint', null);
		$this->db->where('id', $team_id);

		$this->db->update('team');
	}

	public function get_current_hint($team_id) {
		$this->db->from('team');
		$this->db->select('current_hint');
		$this->db->where('id', $team_id);
		$row = $this->db->get()->row();

		return $row->current_hint;
	}

	public function set_hint($team_id, $hint_id) {
		log_message('debug', 'Teams::set_hint(' . $team_id . ', ' . $hint_id . ')');
		$this->db->set('current_hint', $hint_id);
		$this->db->where('id', $team_id);
		$this->db->update('team');
	}

	public function update_last_answered($team_id, $time) {
		$this->db->set('last_answered', $time);
		$this->db->where('id', $team_id);
		$this->db->update('team');
	}

	public function add_points($team_id, $points) {
		$this->db->set('`points`', "`points` + $points", FALSE);
		$this->db->where('id', $team_id);
		$this->db->update('team');
	}

	public function insert($name, $password) {
		$this->db->set('name', $name);
		$this->db->set('password', $password);
		$this->db->insert('team');
		return $this->db->insert_id();
	}

	public function update($id, $name, $password, $active) {
		if ($name !== null) {
			$this->db->set('name', $name);
		}
		if ($password !== null) {
			$this->db->set('password', $password);
		}
		if ($active !== null) {
			$this->db->set('active', $active);
		}
		$this->db->where('id', $id);
		$this->db->update('team');
	}
}
