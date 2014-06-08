<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Create, delete and rename arcs.
 */
public Arc extends GAME_Controller {
	/**
	 * Loads arc model
	 */ 
	public function __construct() {
		parent::__construct();
		$this->load->model('arc', 'arc');
	}

	/**
	 * Index page, show arcs
	 */ 
	public function index() {
		$this->load_view('admin/arc');
	}
}
