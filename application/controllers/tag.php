<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Tag content navigation controller
*/
class Tag extends CI_Controller {

	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);


	}
	

	/**
	* get
	* gets similar tags for autocomplete input
	*/
	function get()
	{
		//if ($this->input->post("q") != "")
		//{
			$this->load->model('tags');
			echo $this->tags->get_similar($this->input->post("q"));
		//}
	}
	

}
/* End of file tag.php */
/* Location: ./application/controller/tag.php */