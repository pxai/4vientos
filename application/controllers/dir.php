<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Dir content navigation controller
*/
class Dir extends CI_Controller {

	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('file');
		$this->load->model('tags');
		$this->load->model('display');
		$this->load->library('pagination');
		$this->session->unset_userdata(array('last_search'=>'','order_criteria'=>''));
		$this->load->view('dir');
	}
	
	/**
	* view
	* default view for a dir
	*/
	function view()
	{
		$this->index();

	}

	/**
	* view_ordered
	* view files ordered by a criteria
	*/
	function view_ordered()
	{
				$this->load->model('file');
		$this->load->model('tags');
		$this->load->model('display');
		$this->session->unset_userdata(array('last_search'=>'','order_criteria'=>''));
		//if (in_array($this->display->order_criteria,$this->input->post("order_by")))
				$this->session->set_userdata('order_criteria',$this->input->post("order_by"));
		//else
		//		$this->session->set_userdata('order_criteria','type asc');	

		$this->load->library('pagination');
		$this->load->view('dir');

	}
	
	function load_file($f)
	{
		$this->load->model('file');
		$this->load->model('display');
				$this->load->model('tags');
		$result = $this->file->fileData($f);
		$filedata = $result->row_array(); 
		echo $this->display->showFile($filedata);
	}
	
	
	function navigation()
	{
		$this->load->model('file');
		$this->load->model('display');

		$this->display->dirListRedux($this->file->dir_dir($this->input->post("dir")));

	}
}
?>