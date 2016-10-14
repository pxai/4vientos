<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Permissions
*/

class Permissions extends CI_Model {


	public function __construct ()
	{
		parent::__construct();

	}

	/**
	* is_admin
	* function that checks if user is administrator
	*/
	public function is_admin ()
	{
		return $this->authentication->is_signed_in() && in_array(ADMINISTRATOR_ID,$this->session->userdata("groups"));
	}

	/**
	* has_permission
	* function that checks for given permission on $file
	*/
	public function has_permission ($file,$op=READ_PERMISSION)
	{
		// If admin, don't check anything
		if ($this->is_admin()) { return TRUE; }
		
		switch ($op)
		{
			case 0 :	return $this->check_r($file,$this->session->userdata("iduser"));	
										
			case 1 :	return $this->check_w($file,$this->session->userdata("iduser"));
										
			case 2 :	return $this->check_a($file,$this->session->userdata("iduser"));
										
			default:	return false;
		}
		return false;
	}
	
	/**
	* check_r
	* Check form read permissions
	*/
	public function check_r ($file,$user)
	{
			//show_error('Vamos: ' . $file ." y " . $user);
			$perm = 0;
			// Take file data
			$result = $this->db->query("select file.* from file where id=".$file);
			$f = $result->row_array();
	
			// Check World perm
			if (!$f["world_r"])
			{

				// Is the  owner?
				if ($f["who"]==$user)
					return $f["user_r"];
			
				// Is the user part of group?
				if ($f["group_r"] && in_array($f["idgroup"],$this->session->userdata("groups")) )
					return 1;
							
			
			}
			else
			{
					//show_error('Vamos, hay permiso: ' . $file ." y " . $user);
					return 1;
			}
			
			return $perm;
	}
	
	/**
	* check_w
	* Check form write permissions
	*/
	public function check_w ($file,$user)
	{

			$perm = 0;
			
			
			// Take file data
			$result = $this->db->query("select file.* from file where id=".$file);
			$f = $result->row_array();

			// Check World perm
			if (!$f["world_w"])
			{

				// Is the  owner?
				if ($f["who"]==$user)
					return $f["user_w"];
			
				// Is the user part of group?
				if ($f["group_w"] && in_array($f["idgroup"],$this->session->userdata("groups")) )
					return 1;
							
			
			}
			else
			{
					return 1;
			}
			
			return $perm;
	}

	
	/**
	* check_a
	* Check form administration permissions
	*/
	public function check_a ($file,$user)
	{
			$perm = 0;
			
			// Take file data
			$result = $this->db->query("select file.* from file where id=".$file);
			$f = $result->row_array();
			
			// Check World perm
			if (!$f["world_a"])
			{

				// Is the  owner?
				if ($f["who"]==$user)
					return $f["user_a"];
			
				// Is the user part of group?
				if ($f["group_a"] && in_array($f["idgroup"],$this->session->userdata("groups")) )
					return 1;
							
			
			}
			else
			{
					return 1;
			}
			
			return $perm;
	}


	/**
	* check_owner
	* checks permissions when user is owner of file
	*/
	private function check_world ($file, $operation)
	{
		$result = $this->db->query("select file.* from file where id=".$file." and world_".$operation."=1");
		$r = $result->row_array();
		
		return  $r["world_".$operation];
		
	}
	

	/**
	* check_owner
	* checks world permissions
	*/
	private function check_owner ($file, $operation)
	{
		$result = $this->db->query("select file.* from file where id=".$file." and world_".$operation."=1");
		$r = $result->row_array();
		
		return  $r["world_".$operation];
		
	}
	
}
/* End of file permissions.php */
/* Location: ./application/models/permissions.php */
