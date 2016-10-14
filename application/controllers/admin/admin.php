<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Admin content navigation controller
*/
class Admin extends CI_Controller {


	public function __construct ()
	{
		parent::__construct();
		$this->load->language("admin");
		$this->load->model('display');
		$this->load->helper('form');
		$this->load->library('form_validation');		
		$this->load->model('entity_model');
		$this->load->model('account_model');
		$this->load->config('4vientos');
		$this->load->config('form_validation');
		$this->load->library('pagination');

	}

	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);

		$data = array("search_term"=>"");
		$this->load->view('admin/admin',$data);
	}
	


}
?>