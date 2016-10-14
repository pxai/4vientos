<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Auditory
*/

class Auditory extends CI_Model {


	public function __construct ()
	{
		parent::__construct();

	}


	/**
	* audit
	* saves event in auditory file
	*/
	public function audit ($what, $who, $file1="",$file2="",$description="",$success=true)
	{
	
			$data = array("idoperation"=>$what,"when"=>time(),"iduser"=>$this->session->userdata('iduser'),"idfile1"=>$file1,"idfile2"=>$file2,"description"=>$description,"success"=>$success,"ip"=>$_SERVER['REMOTE_ADDR']);
			
			$result = $this->db->insert("auditory",$data);
			
			return $result;

	}
	
	


	
}
?>