<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guesses extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	const TABLE = 'guess';
	const C_ID = 'id';
	const C_TEAM_ID = 'team_id';
	const C_QUEST_ID = 'quest_id';
	const C_GUESS = 'guess';

	public function insert($team_id, $quest_id, $guess) {
		$this->db->set(self::C_TEAM_ID, $team_id);
		$this->db->set(self::C_QUEST_ID, $quest_id);
		$this->db->set(self::C_GUESS, $guess);
		$this->db->insert(self::TABLE);
	}

	/**
	 * Check if a team has already guessed this before
	 * @return true if the team has already guessed this before
	 */ 
	public function exists($team_id, $quest_id, $guess) {
		$this->db->from(self::TABLE);
		$this->db->where(self::C_TEAM_ID, $team_id);
		$this->db->where(self::C_QUEST_ID, $quest_id);
		$this->db->where(self::C_GUESS, $guess);

		return $this->db->count_all_results() == 1;
	}

	/**
	 * Get all guesses for the specific quest
	 */
	public function get_team_guesses($team_id, $quest_id) {
		$this->db->from(self::TABLE);
		$this->db->select(self::C_GUESS);
		$this->db->where(self::C_TEAM_ID, $team_id);
		$this->db->where(self::C_QUEST_ID, $quest_id);
	}
}
