<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Dir content navigation controller
* leñe
*/
class Login extends CI_Controller {

	var $errormsg;

	
	function index()
	{

		$this->errormsg = "";
		$this->load->view('login');
		//echo "fuck yeah";		
	}

	function sign_in()
	{
		$msg = "";
		$this->load->model('4v_account/accounter');
//	$this->load->model("Account");
		
		if (preg_match("/^[\w\W]+$/",$this->input->post("login")) && preg_match("/^[\w\W]+$/",$this->input->post("password")))
		{ 

		$msg = $this->accounter->ldap_sign_in($this->input->post("login"),$this->input->post("password"));
	//	$msg = "OK:6:6:6";

			$response = split(":",$msg);
			if ( $response[0] == "OK" )
			{
				$this->load->model('file');
				$this->load->model('tags');
				$this->load->model('display');
	        	$data = array(
	                'iduser'  => $response[1],
	                'idgroup'  => $response[2],
	                'groups' => explode(",",$response[3]),	                
	                'username'  => $this->input->post("login"),
                   'logged_in'  => TRUE
                );
         	$this->session->set_userdata($data);

		 	redirect('/dir/', 'refresh');
				//$this->load->view('dir');
			}
			else
			{
				$this->errormsg = $response[0];
				$this->load->view('login');
			}
		}
		else
		{
				$this->errormsg = "Escribe algo o que";
				$this->load->view('login');
		}
	}


	/**
	* direct_sign_in
	* Entrada directa solo con el login
	*/
	function direct_sign_in()
	{
		echo "Vamos a ver: " .$_SESSION["idsession"];exit;
		$msg = "";
		$this->load->model('4v_account/accounter');
//	$this->load->model("Account");
		
		if (preg_match("/^[\w\W]+$/",$this->input->post("login")) && preg_match("/^[\w\W]+$/",$this->input->post("password")))
		{ 

		$msg = $this->accounter->ldap_sign_in($this->input->post("login"),$this->input->post("password"));
	//	$msg = "OK:6:6:6";

			$response = split(":",$msg);
			if ( $response[0] == "OK" )
			{
				$this->load->model('file');
				$this->load->model('tags');
				$this->load->model('display');
	        	$data = array(
	                'iduser'  => $response[1],
	                'idgroup'  => $response[2],
	                'groups' => explode(",",$response[3]),	                
	                'username'  => $this->input->post("login"),
                   'logged_in'  => TRUE
                );
         	$this->session->set_userdata($data);

		 	redirect('/dir/', 'refresh');
				//$this->load->view('dir');
			}
			else
			{
				$this->errormsg = $response[0];
				$this->load->view('login');
			}
		}
		else
		{
				$this->errormsg = "Escribe algo o que";
				$this->load->view('login');
		}
	}
	
	
	/**
	* sign_up
	* signs up new account
	*/
	function sign_up()
	{
		$msg = "";
		$this->load->model('4v_account/accounter');
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array("result"=>"Error in validation");
			$this->load->view('sign_up',$data);
		}
		else
		{
			$result = $this->accounter->create_new($this->input->post("login"),$this->input->post("password"),$this->input->post("email"));
			$data = array("result"=>"Success: <br />" . $result,"case"=>"new");
			$this->load->view('login',$data);
			
		}
		
		//			$this->load->view('sign_up',$data);
		
	}

	/**
	* validate
	* validates account for duplications
	*/
	function validate_account()
	{
		//return TRUE;
		$errormsg = "";
		
		if ($msg = $this->accounter->check_duplicated($this->input->post("login"),$this->input->post("email")) )
		{
		
			$errorarray = preg_split("/:/",$msg);
			if ($errorarray[0]=="1")
			{
				$errormsg = "Login duplicated";
			}

			if ($errorarray[1]=="1")
			{
				$errormsg .= " Email duplicated";
			}
			
			$this->form_validation->set_message('validate_account',$errormsg);

			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}


	/**
	* sign_in_simple
	* Sing in using database
	*/
	function sign_in_simple()
	{
		$this->load->model('4v_account/accounter');
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
		
		if ($this->form_validation->run() && ($msg = $this->accounter->sign_in($this->input->post("login"),$this->input->post("password"))) )
		{	
			$response = preg_split("/:/",$msg);
				        	$data = array(
	                'iduser'  => $response[1],
	                'idgroup'  => $response[2],
	                'groups' => explode(",",$response[3]),	                
	                'username'  => $this->input->post("login"),
                   'logged_in'  => TRUE
                );
         	$this->session->set_userdata($data);
         	
			redirect('/dir/', 'refresh');
		}
		else
		{
			//$this->form_validation->set_message('required','Login incorrect');
			$this->load->view('sign_in_simple');
		}


	}	

	/**
	* recover
	* recovers account 
	*/
	function recover()
	{
		//return TRUE;
		$errormsg = "";
		$this->load->model('4v_account/accounter');
				$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
		
		if (!$this->input->post("login"))
		{
					$data = array("result"=>"Please provide data to recover");
					$this->load->view('recover',$data);
		}
						
		if ($this->accounter->recover($this->input->post("login"),$this->input->post("email")) )
		{
			$data = array("result"=>"Recover successful. Check email to finnish recovery.");
		}
		else
		{
			$data = array("result"=>"Recovery failed.");
		}
		$this->load->view('recover',$data);
		
	}
	
	
	/**
	* activation
	* activates account from email link
	*/
	function activate($who,$iduser,$key,$keyvalue)
	{
		$errormsg = "";
				$this->load->model('4v_account/accounter');
		
		echo $keyvalue;

		if ($this->accounter->activate($iduser,$keyvalue) )
		{
			$data = array("result"=>"Activation successful.");
		}
		else
		{
			$data = array("result"=>"Activation failed.");
		}
			$this->load->view('activate',$data);
	}
	
	
	/**
	* logout
	* Logs out from app
	*/
	function logout()
	{
		$msg = "";
		$this->load->model('4v_account/accounter');
		$this->session->sess_destroy();
		 redirect('/login/', 'refresh');

	}
}
/* End of file Account.php */
/* Location: ./system/application/controllers/login.php */
?>
