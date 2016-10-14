<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Dir content navigation controller
*/
class Role extends CI_Controller {


	public function __construct ()
	{
        // Call the Model constructor      
      parent::__construct();
      //$this->load->model("role");
		$this->load->language("admin");
		$this->load->model('display');
		$this->load->helper('form','url');
		$this->load->library('form_validation');		
		$this->load->model('entity_model');
		$this->load->model('role_model');
		$this->load->config('4vientos');
		$this->load->config('form_validation');
		$this->load->library('pagination');

	}
	
	
	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$data = array("id"=>0,"name"=>"","description"=>"","suspendedon"=>"","search_term"=>"");
		
		$this->load->view('admin/role',$data);
		
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
	function get_form($role="",$action="")
	{
		//echo $this->role_model->get_form($role,$action);
		echo $this->role_model->get_form($role,$action);
		
	}
	

	/**
	* search
	* search role
	*/
	function search($page=0)
	{
//			$data = array("result"=>$this->lang->line("form_data_error"));
		if (preg_match("/^[a-zA-Z0-9\s]{2,}$/",$this->input->post("search_term")) )
		{
			$order = ($this->input->post("order"))?$this->input->post("order"):"id";
			$result = $this->role_model->search($this->input->post("search_term"),$page,$order,$this->input->post("desc"));
			echo $result;
		}
		else
		{
			echo "Los terminos de búsqueda deben tener dos caracteres.";					
		}
	}

	/**
	* reorder
	* reorder table data for role
	*/
	function reorder()
	{
//			$data = array("result"=>$this->lang->line("form_data_error"));
		if (preg_match("/^[a-zA-Z0-9\s]{2,}$/",$this->input->post("order")) )
		{
			$result = $this->role_model->table($this->input->post("page"),$this->input->post("order"),$this->input->post("desc"));
			echo $result;
		}
		else
		{
			echo "Los terminos de búsqueda deben tener dos caracteres.";					
		}
	}
	
	/**
	* create
	* create role
	*/
	function create()
	{

//			$data = array("result"=>$this->lang->line("form_data_error"));
		if ($this->form_validation->run("admin/role/create") )
		{
			$new_id = $this->role_model->create($this->input->post("name"),$this->input->post("description"),$this->input->post("suspendedon"));
			$new_row = $this->role_model->get_row($new_id);
			echo "ok|||".$new_row;
		}
		else
		{
			$data = array("form_result"=>$this->lang->line("form_data_error"),"id"=>$this->input->post("id"),"name"=>$this->input->post("name"),"description"=>"",$this->input->post("suspendedon"));
			//$this->load->view('role',$data);
			echo "error|||".$this->role_model->get_form(0);	
		}
	}
	
	/**
	* update
	* update role
	*/
	function update($role="")
	{
		
		$result = array();

		if (preg_match("/^[0-9]+$/",$role) && ($record = $this->role_model->show($role)))
		{
			$data = array("id"=>$record["id"],"name"=>$record["name"],"description"=>$record["description"],"suspendedon"=>$record["suspendedon"]);		
			//$this->load->view('role',$data);
			echo "error|||".$this->role_model->get_form($role);			
		}
		elseif ($this->form_validation->run("admin/role/update") && $this->role_model->check($this->input->post("id")) )
		{
			$role = $this->input->post("id");
			$record = $this->role_model->show($this->input->post("id"));
			$data = array("form_result"=>$this->lang->line("form_data_success"),"id"=>$record["id"],"name"=>$this->input->post("name"),"description"=>$this->input->post("description"),"suspendedon"=>$this->input->post("suspendedon"));			
			$result = $this->role_model->update($this->input->post("id"),$this->input->post("name"),$this->input->post("description"),$this->input->post("suspendedon"));
			echo "ok|||".$result;
		}
		else
		{		
			$data = array("form_result"=>$this->lang->line("form_data_error"),"id"=>$this->input->post("id"),"name"=>$this->input->post("name"),"description"=>"","suspendedon"=>$this->input->post("suspendedon"));
			//$this->load->view('role',$data);
			echo "error|||".$this->role_model->get_form($role);
		}
		
		
	}


	/**
	* delete
	* delete role
	*/
	function delete($role)
	{
		$result = array();

		if (preg_match("/^[0-9]+$/",$role) && ($record = $this->role_model->delete($role) ))
		{
				echo "{'result':'ok'}";
		}
		else
		{
			echo "{'result':'error'}";
		}
		
	}	


	/**
	 * autocomplete
	 * autocompleta la lista de centros
	 */
	public function autocomplete ()
	{
			$sql = "select * from a3m_acl_role where name like ? or description like ?";
			$roles = "[";
			 

			$result = $this->db->query($sql,array("%".$_GET["term"]."%","%".$_GET["term"]."%"));
			if ($result->num_rows())
			{
				foreach ($result->result_array() as $r)
				{
					$roles .= '{"id":"'.$r["id"].'","label":"'.$r["name"].'","value":"'.$r["name"].'"},';	
				}
				$roles= rtrim($roles,",");
			}
			
			echo $roles."]";
			
	}

}
?>