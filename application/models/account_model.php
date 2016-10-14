<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Role, contains all logic for file management
*/


class Account_model extends Entity_model {
	
	protected $me = "account";
	protected $fields = array("id","username","email");
	protected $desc = "desc";
	protected $page = 0;
	protected $sql = "select * from a3m_account where id=?"; 
	protected $sqlall = "select * from a3m_account ";
	protected $sqltotal = "select count(*) as total from a3m_account";
	protected $sqlsearch = "select * from a3m_account where  username like ? or email like ? order by ";
	protected $sqltotalsearch = "select count(*) as total from a3m_account where  username like ? or email like ? ";
	
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
	public function create($username,$password,$email)
	{
			

			// create new applicant
			$data = array("username"=>$username,"email"=>$email,"password"=>md5($password));
			$this->db->insert("a3m_account",$data);
			return $this->db->insert_id();
	}
	
	/**
	* update
	* update applicant
	*/
	public function update($id,$username,$password,$email,$roles)
	{
			// Doesn't exist? create it!
			if (!$this->show($id))
			{			
				echo "dont exist";
				$this->create($username,$password,$email);
			}		
			else
			{

			// update roles: first delete, then insert
			// delete
			$this->db->where("account_id",$id);
			$this->db->delete("a3m_rel_account_role");
			
			// Insert
			$rolesid = explode(",",$roles);
			
			foreach ($rolesid as $rl)
			{
				if ($rl=="") break;
				$data = array("role_id"=>$rl, "account_id"=>$id);
				$this->db->insert("a3m_rel_account_role",$data);
			}
						

			
	 			// update del fichero
				$data = array("username"=>$username,"email"=>$email,"password"=>md5($password));
				$this->db->where("id",$id);
				$this->db->update("a3m_account",$data);
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
			$this->db->delete("a3m_account");
			
			return true;
	}	
	
	
	/**
	* get_form
	* gets applicant form
	*/
	public function get_form($account=0,$action="create")
	{
		$xhtml = "";
		
		$id = $username = $email = $password = "";
		$idcentro = 0;

		if ($account)
		{			
			$result = $this->db->query($this->sql,array($account));
			if ($result->num_rows())
			{
				$row = $result->row_array();
				$id = $account;
				$username = $row["username"];
				$email = $row["email"]; 
				$password = $row["password"];
			}
		}	
		else
		{
				$id = $this->input->post("id");
				$username = $this->input->post("username");
				$email = $this->input->post("email"); 
				$password = $this->input->post("password"); 
		}

				  $xhtml .=  form_open("admin/account/".$action,array("id"=>"form_account_update")); 
           	  $xhtml .=  form_fieldset()."<legend>"."Account"."</legend>"; 

                 if (isset($form_result)) { 
                	$xhtml .=  "<span>".$form_result ."</span>";
                  }

				    $xhtml .= " <!-- field id -->";
   				 $xhtml .=  form_error("id");      
//   				 $xhtml .=  form_hidden("id",$id,"id") ;
                $xhtml .=  form_label($this->lang->line('id'), 'id')."<br />"; 
                $xhtml .=  form_input(array("name"=>"id","id"=>"id","value"=>$id,"readonly"=>"readonly")) ."<br />";
                         
				    $xhtml .= " <!-- field username -->";
                $xhtml .=  form_label($this->lang->line('username'), 'username'); 
				    $xhtml .=  form_error("username") ."<br />"; 
                $xhtml .=  form_input(array("name"=>"username","id"=>"username","value"=>$username)) ."<br />";

					 $xhtml .= " <!-- field email -->";
                $xhtml .=  form_label($this->lang->line('email'), 'email'); 
					 $xhtml .=  form_error('email') ."<br />"; 
                $xhtml .=  form_input(array("name"=>"email","id"=>"email","value"=>$email)) ."<br />";
				
					 $xhtml .= " <!-- field password -->";
                $xhtml .=  form_label($this->lang->line('password'), 'password'); 
					 $xhtml .=  form_error('password') ."<br />"; 
                $xhtml .=  form_input(array("name"=>"password","id"=>"password","value"=>$password)) ."<br />";

					if ($action == "update")
					{
						 $xhtml .= " <!-- field roles -->";
  		             $xhtml .=  form_label($this->lang->line('roles'), 'roles'); 
						 $xhtml .=  form_error('roles') ."<br />";
						 $xhtml .=	$this->generateAutcomplete("roles","select * from a3m_acl_role inner join a3m_rel_account_role on id=role_id where account_id=?",array($this->session->userdata('iduser')),array("id","name"),"","admin/role/autocomplete",TRUE,FALSE);

					}
					
            $xhtml .= "<div class='clear'></div>";

             $xhtml .=  form_fieldset_close(); 
             $xhtml .=  form_close(); 	
		return $xhtml;
	}
	

	/**
	* generateAutcomplete
	* genera el autocomplete
	*/
	protected function generateAutcomplete ($field,$sqlquery="",$params=null,$fields=null,$selected="",$url="",$ismultiple=FALSE,$allow_new=TRUE)
	{
			$idvalue = $visiblevalue = "";
		$rolesvalues = "";
		 $xhtml = "";
		 $xhtml .=	parent::generateAutcomplete("roles_tmp","select * from a3m_acl_role inner join a3m_rel_account_role on id=role_id where account_id=?",array($this->session->userdata('iduser')),array("id","name"),"","admin/role/autocomplete",FALSE,FALSE);
		 $xhtml .= "<input type='button' id='role_add' name='role_add' value=' + '  onclick='addrole();' />";
		 
		 $xhtml .= "<select name='roles_list' id='roles_list' size='5'>\n";
			if ($sqlquery !="")
			{
				$result = $this->db->query($sqlquery,$params);
				if ($result->num_rows())
				{
					foreach ($result->result_array() as $r)
					{
						$xhtml .= "<option value='".$r[$fields[0]]."' >";
						$xhtml .= $r[$fields[1]]."</option>\n"; 
						$rolesvalues .=  $r[$fields[0]];
					}
					
					$rolesvalues =  rtrim(",",$rolesvalues);
				}

			}
			
		 $xhtml .= "</select>\n";
		 $xhtml .= "<input type='hidden' id='roles' name='roles' value='".$rolesvalues."' />";
		 $xhtml .= "<input type='button' id='role_del' name='role_del' value=' - ' onclick='deleterole();' />";

	
			
			$xhtml .= "<script>
							function addrole()
							{
								var list = document.getElementById('roles_list');
								var unvisible_roles = document.getElementById('roles_tmp');
								var visible_roles = document.getElementById('roles_tmp_visible');
			
								list.options.add(new Option(visible_roles.value,unvisible_roles.value));
								updaterole();
							}
							
							function deleterole()
							{
								var list = document.getElementById('roles_list');
								alert('ok '+ list.selectedIndex);
								list.remove(list.selectedIndex);
								updaterole();
							}
 							 
 							function updaterole ()
 							{
								var list = document.getElementById('roles_list');
								var rolesfinal = document.getElementById('roles');
								var i = 0;

								rolesfinal.value = '';
								
								for (i=0;i<list.length;i++)
								{
									rolesfinal.value += list.options[i].value + ',';
								}
 							}
							</script>\n";
							
			return $xhtml;
	}

	
}

/* End of file file.php */
/* Location: ./application/models/file.php */