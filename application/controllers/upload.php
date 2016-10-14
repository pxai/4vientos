
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* upload.php, file upload options
*/
class Upload extends CI_Controller {

	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('file');
		$this->load->model('tags');
		$this->load->model('display');
		
		$this->load->view('dir');
	}
	
	/**
	* upload
	*
	*/	
	function upload()
	{
		$this->index();
	}
}
?>
