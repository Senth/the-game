<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hints extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function get_hints($quest_id) {
		$this->db->from('hint');
		$this->db->where('quest_id', $quest_id);
		$this->db->order_by('order', 'ASC');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function get_first_hint($quest_id) {
		if ($quest_id !== null) {
			$this->db->from('hint');
			$this->db->where('quest_id', $quest_id);
			$this->db->where('order', 1);
			return $this->db->get()->row();
		}
		return null;
	}

	public function get_hint($hint_id) {
		if ($hint_id !== null) {
			$this->db->from('hint');
			$this->db->where('id', $hint_id);
			return $this->db->get()->row();
		}
		return null;
	}

	public function get_next_hint($hint_id) {
		if ($hint_id !== null) {
			$current_hint = $this->get_hint($hint_id);

			// Get the next hint
			if ($current_hint !== null) {
				$this->db->from('hint');
				$this->db->where('quest_id', $current_hint->quest_id);
				$this->db->where('order >', $current_hint->order);
				$this->db->limit(1);
				$this->db->order_by('order', 'ASC');

				$next_hint = $this->db->get()->row();
				return $next_hint;
			}
		}

		return null;
	}

	public function insert($quest_id) {
		// Get number of hints for this quest to set the correct order
		$this->db->from('hint');
		$this->db->where('quest_id', $quest_id);
		$hints = $this->db->count_all_results();

		$this->db->set('quest_id', $quest_id);
		$this->db->set('order', $hints+1);
		$this->db->insert('hint');
	}

	public function update($id, $text, $time, $skippable, $points) {
		$this->db->set('text', $text);
		$this->db->set('time', $time);
		$this->db->set('skippable', $skippable);
		$this->db->set('point_deduction', $points);
		$this->db->where('id', $id);
		$this->db->update('hint');
	}

	public function copy_to($from_quest_id, $to_quest_id) {
		$hints = $this->get_hints($from_quest_id);

		if ($hints !== null) {
			$this->db->trans_start();
			foreach ($hints as $hint) {
				$hint->quest_id = $to_quest_id;
				unset($hint->id);
				$this->db->insert('hint', $hint);
			}

			$this->db->trans_complete();
		}
	}

	public function move($id, $order) {
		$this->db->from('hint');
		$this->db->select('order');
		$this->db->where('id', $id);
		$row = $this->db->get()->row();

		if ($row) {
			$oldOrder = $row->order;
			$update = FALSE;

			// Moved down (increased the order)
			if ($order > $oldOrder) {
				$update = TRUE;
				$this->db->set('`order`', '`order` - 1', FALSE);
				$this->db->where('order >', $oldOrder);
				$this->db->where('order <=', $order);
				$this->db->update('hint');
			}
			// Moved up (decreased the order)
			elseif ($order < $oldOrder) {
				$update = TRUE;
				$this->db->set('`order`', '`order` + 1', FALSE);
				$this->db->where('order >=', $order);
				$this->db->where('order <', $oldOrder);
				$this->db->update('hint');
			}
			// Else - didn't change the position

			// Update the moved hint
			if ($update) {
				$this->db->set('order', $order);
				$this->db->where('id', $id);
				$this->db->update('hint');
			}
		}
	}

	public function delete_all_from_quest($quest_id) {
		$this->db->where('quest_id', $quest_id);
		$this->db->delete('hint');
	}

	public function delete($id) {
		// Get hint order and move up all hints under it
		$this->db->from('hint');
		$this->db->select('quest_id');
		$this->db->select('order');
		$this->db->where('id', $id);
		$row = $this->db->get()->row();

		// Move up all hints
		if ($row) {
			$deleted_order = $row->order;
			$quest_id = $row->quest_id;

			$this->db->set('`order`', '`order` - 1', FALSE);
			$this->db->where('order >', $deleted_order);
			$this->db->where('quest_id', $quest_id);
			$this->db->update('hint');
		}

		// Delete hint
		$this->db->where('id', $id);
		$this->db->delete('hint');
	}

	public function fix_hints() {
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

			$this->db->set('order', $hint_order);
			$this->db->where('id', $row->id);
			$this->db->update('hint');
		}
	}
}
