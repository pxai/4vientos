<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Dir content navigation controller
*/
class Search extends CI_Controller {

	/**
	* 
	*
	*/
	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('file');
		$this->load->model('display');
		$this->load->model('tags');
		$this->load->library('pagination');
		$this->session->unset_userdata(array('last_search'=>'','order_criteria'=>''));
		$this->session->set_userdata('last_search',array($this->input->post("namesearch"),$this->input->post("tagssearch")));		
		$this->load->view('dir');
	}
	
	/**
	* keep_searching
	* called after firts search. Keeps order criteria and search terms for pagination
	*/
	function keep_searching ()
	{
		
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('file');
		$this->load->model('display');
		$this->load->model('tags');		
		$this->load->library('pagination');
		$_POST["search"] = "search";
		if ($this->input->post("order_by")!= "") 
			$this->session->set_userdata('order_criteria',$this->input->post("order_by"));
		$searchdata = $this->session->userdata('last_search');
		$_POST["namesearch"] = $searchdata[0]; 
		$_POST["tagsearch"]  = $searchdata[1];

		$this->load->view('dir');
	}
	
	/**
	* by_tag
	* performs search by tag
	*/
	function by_tag ()
	{
		
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('file');
		$this->load->model('display');
		$this->load->model('tags');		
		$this->load->library('pagination');

		if ($this->uri->segment(3) === FALSE)
		{
    		redirect(site_url()."/dir/");
		}
		else
		{
    		$tag = $this->uri->segment(3);
		}
		
		$this->session->unset_userdata(array('last_search'=>'','order_criteria'=>''));
		$this->session->set_userdata('last_search',array("",$tag));		
		
		$_POST["search"] = "search";
		$_POST["namesearch"] = ""; 
		$_POST["tagssearch"]  = $tag;
		
		$this->load->view('dir');
	}
	
	

}
/* End of file search.php */
/* Location: ./application/controllers/search.php */