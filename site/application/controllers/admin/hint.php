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

		 // TODO
	 }
 }
