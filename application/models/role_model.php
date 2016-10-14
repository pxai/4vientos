<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Role, contains all logic for file management
*/


class Role_model extends Entity_model {
	
	protected $me = "role";
	protected $fields = array("id","name","description","suspendedon");
	protected $desc = "desc";
	protected $page = 0;
	protected $sql = "select * from a3m_acl_role where id=?"; 
	protected $sqlall = "select * from a3m_acl_role ";
	protected $sqltotal = "select count(*) as total from a3m_acl_role";
	protected $sqlsearch = "select * from a3m_acl_role where  name like ? or description like ? order by ";
	protected $sqltotalsearch = "select count(*) as total from a3m_acl_role where  name like ? or description like ? ";
	
	/**
	* __construct
	*
	*/
	public function __construct ()
	{
		if (!$this->permissions->is_admin()) { $this->redirect("account/sign_in"); } 
        // Call the Model constructor      
      parent::__construct();
	}

	
	
	
	/**
	* create
	* Creates new applicant
	*/
	public function create($name,$description,$suspendedon)
	{
			

			// create new role
			$data = array("name"=>$name,"suspendedon"=>$suspendedon,"description"=>$description);
			$this->db->insert("a3m_acl_role",$data);
			return $this->db->insert_id();
	}
	
	/**
	* update
	* update applicant
	*/
	public function update($id,$name,$description,$suspendedon)
	{
			// Doesn't exist? create it!
			if (!$this->show($id))
			{			
				echo "dont exist";
				$this->create($name,$description,$suspendedon);
			}		
			else
			{
	 			// update del fichero
				$data = array("name"=>$name,"suspendedon"=>$suspendedon,"description"=>$description);
				$this->db->where("id",$id);
				$this->db->update("a3m_acl_role",$data);
				// return new row
				return $this->get_row($id,FALSE);
			}
			
			return "error";
	}


	/**
	* delete
	* delete applicant
	*/
	public function delete($id)
	{
			// delete
			$this->db->where("id",$id);
			$this->db->delete("a3m_acl_role");
			
			return true;
	}	
	
	
	/**
	* get_form
	* gets applicant form
	*/
	public function get_form($role=0,$action="create")
	{
		$xhtml = "";
		
		$id = $name = $suspendedon = $description = "";
		$idcentro = 0;

		if ($role)
		{			
			$result = $this->db->query($this->sql,array($role));
			if ($result->num_rows())
			{
				$row = $result->row_array();
				$id = $role;
				$name = $row["name"];
				$suspendedon = $row["suspendedon"]; 
				$description = $row["description"];
			}
		}	
		else
		{
				$id = $this->input->post("id");
				$name = $this->input->post("name");
				$suspendedon = $this->input->post("suspendedon"); 
				$description = $this->input->post("description"); 
		}

				  $xhtml .=  form_open("admin/role/".$action,array("id"=>"form_role_update")); 
           	  $xhtml .=  form_fieldset()."<legend>"."role"."</legend>"; 

                 if (isset($form_result)) { 
                	$xhtml .=  "<span>".$form_result ."</span>";
                  }

				    $xhtml .= " <!-- field id -->";
   				 $xhtml .=  form_error("id");      
//   				 $xhtml .=  form_hidden("id",$id,"id") ;
                $xhtml .=  form_label($this->lang->line('id'), 'id')."<br />"; 
                $xhtml .=  form_input(array("name"=>"id","id"=>"id","value"=>$id,"readonly"=>"readonly")) ."<br />";
                         
				    $xhtml .= " <!-- field name -->";
                $xhtml .=  form_label($this->lang->line('name'), 'name'); 
				    $xhtml .=  form_error("name") ."<br />"; 
                $xhtml .=  form_input(array("name"=>"name","id"=>"name","value"=>$name)) ."<br />";
				
					 $xhtml .= " <!-- field description -->";
                $xhtml .=  form_label($this->lang->line('description'), 'description'); 
					 $xhtml .=  form_error('description') ."<br />"; 
                $xhtml .=  form_input(array("name"=>"description","id"=>"description","value"=>$description)) ."<br />";

					 $xhtml .= " <!-- field suspendedon -->";
                $xhtml .=  form_label($this->lang->line('suspendedon'), 'suspendedon'); 
					 $xhtml .=  form_error('suspendedon') ."<br />"; 
                $xhtml .=  form_input(array("name"=>"suspendedon","id"=>"suspendedon","value"=>$suspendedon)) ."<br />";


            $xhtml .= "<div class='clear'></div>";

             $xhtml .=  form_fieldset_close(); 
             $xhtml .=  form_close(); 	
		return $xhtml;
	}
	
	

	
}

/* End of file file.php */
/* Location: ./application/models/file.php */