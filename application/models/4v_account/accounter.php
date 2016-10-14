<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* File, contains all logic for file management
*/

class Accounter extends CI_Model {
	public $file;
	public $who;
	private $dnum1;
	private $dnum2;	


    function __construct()
    {
        parent::__construct();
    }


	/**
	* simple_sing_in
	* Sign in using a database table
	*/
	public function simple_sing_in ($username,$password)
	{
		return true;
	}
	
	/**
	* ldap_sing_in
	* Sign in using ldap server
	*/
	public function ldap_sign_in ($login,$password)
	{
	
		//echo "Vamos a ver.<br />";

		$ldaprdn = $login; // ldap rdn or dn
		$ldappass = $password; // associated password
		$ldapconn = 0;

		//lets concatenate the proper username:
		$domain = $this->config->item("ldap_domain");
		$ldaprdn = $login . $domain;

		// connect to ldap server
		if ( !($ldapconn = @ldap_connect($this->config->item("ldap_server"))) )
		{
			$msg = "Could not connect to LDAP server";
		} 

		else {
			//echo "Conexión con LDAP OK<br /> probando $ldaprdn y $password<br />";
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
	

			// verify binding
			if ($ldapbind) {
				$id = $this->check_login_exists_db($login,$password);
				$msg = $id;
			} else {
				$msg = "Login incorrecto... " . $ldaprdn .":".$ldappass;
			}
		}

		return $msg;
	}
	
	/**
	* get_user_data
	* Once user login is valid through ldap or openid, check if he already exists in user table,
	* if not insert and in any case return his ID. $savepass: boolean to save pass or not
	*/
	public function get_user_data ($id_user)
	{
		$data = "";
		
		$result = $this->db->query("select * from a3m_account where id=".$id_user);

		// If user exists		
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();
			// get rest of groups:
			$resultgroups = $this->db->query("select * from a3m_rel_account_role where account_id='".$row['id']."' ");
			$groups = array();
			foreach ($resultgroups->result() as $r)
   		{
      		$groups[] = $r->role_id;
   		}
   		
   		
   		
		$data = array(
	                'iduser'  => $id_user,
// FIXED groupid        'idgroup'  => $row['idgroup'],
	                'idgroup'  => 22,
	                'groups' => $groups,	                
	                'username'  => $row['username']                
	                );

			return $data;
		}
		
		return "";
	}	
	
	

	/**
	* check_login_exists_db
	* Once user login is valid through ldap or openid, check if he already exists in user table,
	* if not insert and in any case return his ID. $savepass: boolean to save pass or not
	*/
	private function check_login_exists_db ($login,$password,$savepass = 0)
	{
		$result = $this->db->query("select * from a3m_account where username='".$login."' ");

		// If user exists		
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();
			// get rest of groups:
			$resultgroups = $this->db->query("select * from a3m_rel_account_role where account_id='".$row['id']."' ");
			$groups = array();
			foreach ($resultgroups->result() as $r)
   		{
      		$groups[] = $r->role_id;
   		}
   		

			return "OK:".$row['id'].":".$row['idgroup'].":".implode(",",$groups);
		}
		else // Insert user, and group then return ID
		{
			// Create new group
			$data = array("name"=>$login,"description"=>"User default group");
			$this->db->insert("a3m_acl_role",$data);
			
			$idgroup = $this->db->insert_id();

			if ($savepass)
				$data = array("username"=>$login,"password"=>sha1($password),"createdon"=>date("Y-m-d H:i:s"),"idgroup"=>$idgroup);
			else
				$data = array("username"=>$login,"password"=>sha1(md5(time())),"createdon"=>date("Y-m-d H:i:s"),"idgroup"=>$idgroup);
				
			$this->db->insert("a3m_account",$data);
			$idaccount = $this->db->insert_id();
			
			// Insert user/group relation, include group 22 (magic number omg)
			$data = array("account_id"=>$idaccount,"role_id"=>$idgroup);
			$this->db->insert("a3m_rel_account_role",$data);
			$data = array("account_id"=>$idaccount,"role_id"=>22);
			$this->db->insert("a3m_rel_account_role",$data);

			return "OK:".$idaccount.":".$idgroup.":".$idgroup;
		}
	}	
	
	
	/**
	* check_duplicated
	* Check if user login or email already exists
	*/
	public function check_duplicated ($login,$email)
	{
		$result = $this->db->query("select * from a3m_account where username='".$login."' or email='".$email."'");
		$msg = "";

		// If user exists, check if email or login is duplicated
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();
			$msg = ($row['login'] == $login)?"1:":"0:";
			$msg .= ($row['email'] == $email)?"1":"0";

			echo $msg;
			return $msg;
		}
		else // Insert user, and group then return ID
		{
			return FALSE;
		}
	}	
	

	/**
	* check_direct_access
	* Check if user login or email already exists
	*/
	public function check_direct_access ($login,$password)
	{
		$result = $this->db->query("SELECT a3m_account.* FROM a3m_account_details inner join a3m_account on a3m_account.id=a3m_account_details.account_id WHERE fullname='".$login."' and picture='".$password."'");

		// If user exists, check if email or login is duplicated
		if  ($result->num_rows() > 0) {
			   $row = $result->row_array();

			// get rest of groups:
			$resultgroups = $this->db->query("select * from a3m_rel_account_role where account_id='".$row['id']."' ");
			$groups = array();
			foreach ($resultgroups->result() as $r)
	   		{
      			$groups[] = $r->role_id;
	   		}
   		
			return "OK:".$row['id'].":".$row['idgroup'].":".implode(",",$groups);
		} 
		else 
		{
			return FALSE;
		}
	}	
	
	
	/**
	* sign_in
	* sign_in user on db
	*/	
	public function sign_in ($login,$password)
	{
	
		$result = $this->db->query("select * from a3m_account where username='".$login."' and password=sha1('".$password."') ");
		$msg = "";

		// If user exists, SUCCESS
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();

			// get rest of groups:
			$resultgroups = $this->db->query("select * from a3m_rel_account_role where account_id='".$row['id']."' ");
			$groups = array();
			foreach ($resultgroups->result() as $r)
	   		{
      			$groups[] = $r->role_id;
	   		}
   		
			return "OK:".$row['id'].":".$row['idgroup'].":".implode(",",$groups);
		}
		else // Insert user, and group then return ID
		{
			return FALSE;
		}
	}
	
	/**
	* create_new
	* Creates new user from conventional registration (not ldap or openid)
	*/	
	public function create_new ($login,$password,$email,$savepass = 1)
	{
			// Create new group
			$data = array("name"=>$login,"description"=>"User default group");
			$this->db->insert("a3m_account_groups",$data);
			
			$idgroup = $this->db->insert_id();
			
			// If activation code is required, then prepare
			$activation_code =sha1(md5(time().$login));
			$activation = ($this->config->item("require_activation"))?$activation_code:"";

			if ($savepass)
				$data = array("username"=>$login,"password"=>sha1($password),"email"=>$email,"createdon"=>date("Y-m-d H:i:s"),"idgroup"=>$idgroup);
			else
				$data = array("username"=>$login,"password"=>sha1(md5(time())),"email"=>$email,"createdon"=>date("Y-m-d H:i:s"),"idgroup"=>$idgroup);
				
			$this->db->insert("a3m_account",$data);
			$idaccount = $this->db->insert_id();
			
			// If insert is correct, send email for activation
		/*	if ($idaccount && $activation)
			{
				$this->load->library('email');

				$this->email->from('4vientos@cuatrovientos.org', '4vientos');
				$this->email->to($email);
				//$this->email->cc('4vientos@cuatrovientos.org');

				$this->email->subject('Email Test');
				$this->email->message('Activate your account here: ' . site_url().'/login/activate/who/'.$idaccount.'/key/'.$activation_code);

				$this->email->send();
				
				//echo $this->email->print_debugger();				
			}*/
			
			// Insert user/group relation
			$data = array("idaccount"=>$idaccount,"idgroup"=>$idgroup);
			$this->db->insert("a3m_account_user_group",$data);

			return "OK:".$idaccount.":".$idgroup.":".$idgroup;	
	}
	
	
	/**
	* activate
	* activate user on db
	*/	
	public function activate ($userid,$key)
	{
	
		$result = $this->db->query("select * from a3m_account where id='".$userid."' and  activation='".$key."'");

		// If user exists, SUCCESS
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();

			// update user
			$data = array("activation"=>"");
			$this->db->where("id",$userid);
			$this->db->update("a3m_account",$data);
			   		
			return TRUE;
		}
		else // user doesn't exists
		{
			return FALSE;
		}
	}
	
	
	
	/**
	* recover
	* Recover user from lost password
	*/	
	public function recover ($login,$email)
	{
				$result = $this->db->query("select * from a3m_account where username='".$login."' and email='".$email."'");
		$msg = "";

		// If user exists, check if email or login is duplicated
		if ($result->num_rows() > 0)
		{
		   $row = $result->row_array();

			$activation_code =sha1(md5(time().$login));
			
			// update user
			$data = array("activation"=>$activation_code);
			$this->db->where("login",$login);
			$this->db->update("a3m_account",$data);

			$this->load->library('email');

				$this->email->from('4vientos@cuatrovientos.org', '4vientos');
				$this->email->to($row['email']);
				//$this->email->cc('4vientos@cuatrovientos.org');

				$this->email->subject('Account recovery');
				$this->email->message('To recover account, click here: ' . site_url().'/login/activate/who/'.$row['id'].'/key/'.$activation_code);

				$this->email->send();
				
				//echo $this->email->print_debugger();				
				return TRUE;
				
		}
		else // Insert user, and group then return ID
		{
			return FALSE;
		}
		
	}
}
/* End of file accounter.php */
/* Location: ./application/models/4v_account/accounter.php */
