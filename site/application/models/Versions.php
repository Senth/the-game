<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model interacting with the teams
 */
class Versions extends CI_Model {
	const TABLE = 'version';
	const C_VERSION = 'version';

	/**
	 * Constructor, does nothing
	 */
	public function __construct() {
		parent::__construct();
	}

	public function get() {
		// Check if version table exists
		if ($this->db->table_exists(self::TABLE)) {
			$row = $this->db->get(self::TABLE)->row();
			return $row->version;
		} else {
			return 0;
		}
	}

	public function upgrade_0_to_1_2() {
		$this->load->dbforge();


		// Arc
		$fields = array(
			'id' => array(
				'name' => 'id',
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
			)
		);
		$this->dbforge->modify_column('arc', $fields);


		// ci_sessions
		$fields = array(
			'session_id' => array(
				'name' => 'id',
				'type' => 'VARCHAR',
				'constraint' => '40',
				'default' => '0',
			),
			'last_activity' => array(
				'name' => 'timestamp',
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			),
			'user_data' => array(
				'name' => 'data',
				'type' => 'TEXT'
			)
		);
		$this->dbforge->modify_column('ci_sessions', $fields);
		$this->dbforge->drop_column('ci_sessions', 'user_agent');


		// Guess
		$this->dbforge->add_field('id');	
		$fields = array(
			'team_id' => array(
				'type' => 'INT',
				'constraint' => 9,
			),
			'quest_id' => array(
				'type' => 'INT',
				'constraint' => 9,
			),
			'guess' => array(
				'type' => 'VARCHAR',
				'constraint' => '150'
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->create_table('guess');


		// Hint
		$fields = array(
			'id' => array(
				'name' => 'id',
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
			),
			'quest_id' => array(
				'name' => 'quest_id',
				'type' => 'INT',
				'constraint' => 9,
			)
		);
		$this->dbforge->modify_column('hint', $fields);

		$fields = array(
			'skippable' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'after' => 'time',
			),
			'order' => array(
				'type' => 'TINYINT',
				'constraint' => 10,
				'default' => 0,
			)
		);
		$this->dbforge->add_column('hint', $fields);

		// Fix existing hints
		$this->db->from('hint');
		$this->db->order_by('quest_id', 'ASC');
		$this->db->order_by('time', 'ASC');

		$query = $this->db->get();

		$quest_id = -1;
		$hint_order = 1;
		foreach ($query->result() as $row) {
			if ($quest_id !== $row->quest_id) {
				$quest_id = $row->quest_id;
				$hint_order = 1;
			} else {
				$hint_order++;
			}

			$this->db->set('skippable', 0);
			$this->db->set('order', $hint_order);
			$this->db->where('id', $row->id);
			$this->db->update('hint');
		}



		// Quest
		$fields = array(
			'id' => array(
				'name' => 'id',
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
			),
			'arc_id' => array(
				'name' => 'arc_id',
				'type' => 'INT',
				'constraint' => 9,
			),
			'point_standard' => array(
				'name' => 'points',
				'type' => 'INT',
				'constraint' => 3,
				'default' => 0,
			)
		);
		$this->dbforge->modify_column('quest', $fields);

		$this->dbforge->drop_column('quest', 'first_team_id');
		$this->dbforge->drop_column('quest', 'point_first_extra');


		// Team
		$fields = array(
			'id' => array(
				'name' => 'id',
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
			)
		);
		$this->dbforge->modify_column('team', $fields);

		$fields = array(
			'current_hint' => array(
				'type' => 'INT',
				'constraint' => 9,
				'null' => TRUE,
			)
		);
		$this->dbforge->add_column('team', $fields);


		// User
		$fields = array(
			'id' => array(
				'name' => 'id',
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
			)
		);
		$this->dbforge->modify_column('user', $fields);


		// Version
		$fields = array(
			'version' => array(
				'type' => 'INT',
				'constraint' => 11
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('version', TRUE);
		$this->dbforge->create_table('version');
		$this->db->set('version', 0);
		$this->db->insert('version');

	}

	public function set($version) {
		$this->db->set(self::C_VERSION, $version);
		$this->db->update(self::TABLE);
	}
}
