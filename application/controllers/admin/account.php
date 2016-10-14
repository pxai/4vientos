<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Dir content navigation controller
*/
class Account extends CI_Controller {


	public function __construct ()
	{
        // Call the Model constructor      
      parent::__construct();
      //$this->load->model("account");
		$this->load->language("admin");
		$this->load->model('display');
		$this->load->helper('form','url');
		$this->load->library('form_validation');		
		$this->load->model('entity_model');
		$this->load->model('account_model');
		$this->load->config('4vientos');
		$this->load->config('form_validation');
		$this->load->library('pagination');

	}
	
	
	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$data = array("id"=>0,"nif"=>"","nombre"=>"","search_term"=>"");
		
		$this->load->view('admin/account',$data);
		
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
	* get_form
	* default view for a dir
	*/
	function get_form($account="",$action="")
	{
		//echo $this->account_model->get_form($account,$action);
		echo $this->account_model->get_form($account,$action);
		
	}
	

	/**
	* search
	* search account
	*/
	function search($page=0)
	{
//			$data = array("result"=>$this->lang->line("form_data_error"));
		if (preg_match("/^[a-zA-Z0-9\s]{2,}$/",$this->input->post("search_term")) )
		{
			$order = ($this->input->post("order"))?$this->input->post("order"):"id";
			$result = $this->account_model->search($this->input->post("search_term"),$page,$order,$this->input->post("desc"));
			echo $result;
		}
		else
		{
			echo "Los terminos de búsqueda deben tener dos caracteres.";					
		}
	}

	/**
	* reorder
	* reorder table data for account
	*/
	function reorder()
	{
//			$data = array("result"=>$this->lang->line("form_data_error"));
		if (preg_match("/^[a-zA-Z0-9\s]{2,}$/",$this->input->post("order")) )
		{
			$result = $this->account_model->table($this->input->post("page"),$this->input->post("order"),$this->input->post("desc"));
			echo $result;
		}
		else
		{
			echo "Los terminos de búsqueda deben tener dos caracteres.";					
		}
	}
	
	/**
	* create
	* create account
	*/
	function create()
	{

//			$data = array("result"=>$this->lang->line("form_data_error"));
		if ($this->form_validation->run("admin/account/create") )
		{
			$new_id = $this->account_model->create($this->input->post("username"),$this->input->post("password"),$this->input->post("email"));
			$new_row = $this->account_model->get_row($new_id);
			echo "ok|||".$new_row;
		}
		else
		{
			$data = array("form_result"=>$this->lang->line("form_data_error"),"id"=>$this->input->post("id"),"username"=>$this->input->post("username"),"password"=>"",$this->input->post("email"));
			//$this->load->view('account',$data);
			echo "error|||".$this->account_model->get_form(0);	
		}
	}
	
	/**
	* update
	* update account
	*/
	function update($account="")
	{
		
		$result = array();

		if (preg_match("/^[0-9]+$/",$account) && ($record = $this->account_model->show($account)))
		{
			$data = array("id"=>$record["id"],"username"=>$record["username"],"password"=>$record["password"],"email"=>$record["email"]);		
			//$this->load->view('account',$data);
			echo "error|||".$this->account_model->get_form($account);			
		}
		elseif ($this->form_validation->run("admin/account/update") && $this->account_model->check($this->input->post("id")) )
		{
			$account = $this->input->post("id");
			$record = $this->account_model->show($this->input->post("id"));
			$data = array("form_result"=>$this->lang->line("form_data_success"),"id"=>$record["id"],"username"=>$this->input->post("username"),"password"=>$this->input->post("password"),"email"=>$this->input->post("email"));			
			$result = $this->account_model->update($this->input->post("id"),$this->input->post("username"),$this->input->post("password"),$this->input->post("email"),$this->input->post("roles"));
			echo "ok|||".$result;
		}
		else
		{		
			$data = array("form_result"=>$this->lang->line("form_data_error"),"id"=>$this->input->post("id"),"username"=>$this->input->post("username"),"password"=>"","email"=>$this->input->post("email"));
			//$this->load->view('account',$data);
			echo "error|||".$this->account_model->get_form($account);
		}
		
		
	}


	/**
	* delete
	* delete account
	*/
	function delete($account)
	{
		$result = array();

		if (preg_match("/^[0-9]+$/",$account) && ($record = $this->account_model->delete($account) ))
		{
				echo "{'result':'ok'}";
		}
		else
		{
			echo "{'result':'error'}";
		}
		
	}	


}
?>