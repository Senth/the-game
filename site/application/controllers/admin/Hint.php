 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
  * Controller for adding, changing and removing hints
  */ 
 class Hint extends GAME_Controller {
	 /**
	  * Loads hint models
	  */
	 public function __construct() {
		 parent::__construct();
		 $this->load->model('Hints', 'hint');
	 }

	 /**
	  * Get all hints for a specified quest
	  * @param quest_id id of the quest
	  */ 
	 public function get_all($quest_id) {
		 // Only handle ajax requests
		 if ($this->input->post('ajax') === FALSE) {
			 return;
		 }

		 $hints = $this->hint->get_hints($quest_id);

		 if ($hints !== FALSE) {
			 $json_return['success'] = TRUE;
			 $json_return['hints'] = $hints;
		 } else {
			 $json_return['success'] = FALSE;
		 }

		 echo json_encode($json_return);
	 }

	 /**
	  * Add a new hint to the specified quest
	  */
	 public function add() {
		 // Only handle ajax requests
		 if ($this->input->post('ajax') === FALSE) {
			 return;
		 }

		 $this->hint->insert($this->input->post('quest_id'));

		 $json_return['success'] = TRUE;
		 echo json_encode($json_return);
	 }

	 /**
	  * Edit an existing quest
	  */ 
	 public function edit() {
		 // Only handle ajax requests
		 if ($this->input->post('ajax') === FALSE) {
			 return;
		 }

		 $id = $this->input->post('id');
		 $text = $this->input->post('text');
		 $time = $this->input->post('time');
		 $skippable = $this->input->post('skippable');
		 $points = $this->input->post('points');

		 $this->hint->update($id, $text, $time, $skippable, $points);

		 $json_return['success'] = TRUE;
		 echo json_encode($json_return);
	 }

	 /**
	  * Move/Change the order of a hint. Will automatically update all the other hints
	  */
	 public function move() {
		 $id = $this->input->post('id');
		 $order = $this->input->post('order');

		 $this->hint->move($id, $order);

		 $json_return['success'] = TRUE;
		 echo json_encode($json_return);
	 }

	 /**
	  * Remove a hint
	  */
	 public function remove() {
		 // Only handle ajax requests
		 if ($this->input->post('ajax') === FALSE) {
			 return;
		 }

		 $id = $this->input->post('id');

		 $this->hint->delete($id);

		 $json_return['success'] = TRUE;
		 echo json_encode($json_return);
	 }

	 public function fix_hints() {
		 $this->hint->fix_hints();
	 }
 }
