<?php

class About extends CI_Controller {

    function __construct()
    {
        parent::__construct();
		
		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form', 'account/ssl'));
		$this->lang->load(array('messages'));
	}
	
	function index()
	{
		
		$this->load->view('about', isset($data) ? $data : NULL);
	}
	
}


/* End of file about.php */
/* Location: ./system/application/controllers/about.php */