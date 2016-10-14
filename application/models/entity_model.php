<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Entity_model, contains all logic for entity management
*/


class Entity_model extends CI_Model {
	
	protected $me = "applicant";
	protected $fields = array("id","nif","nombre");
	protected $desc = "desc";
	protected $page = 0;
	protected $sql = "select * from applicant where id=?"; 
	protected $sqlall = "select * from applicant ";
	protected $sqltotal = "select count(*) as total from applicant";
	protected $sqlsearch = "select * from applicant where  nombre like ? or nif like ? order by ";
	protected $sqltotalsearch = "select count(*) as total from applicant where  nombre like ? or nif like ? ";
	
	/**
	* __construct
	*
	*/
	public function __construct ()
	{
        // Call the Model constructor      
      parent::__construct();
	}

	
	
	/**
	* table
	* gets entity table
	*/
	public function table($page=0,$order="id",$desc="desc",$params=array())
	{
			$xhtml = "";

			$resulttotal = $this->db->query($this->sqltotal,$params);
			// table data
			$rowtotal =  $resulttotal->row_array();

			$this->desc = ($desc=="desc")?"asc":"desc";
			$this->page = (preg_match("/^[0-9]+$/",$page) && $page < $rowtotal['total'] )?$page:0;
			$next = $this->page;
			
			// create new applicant
			$sql = $this->sqlall ." order by ".$order." " .$desc. " limit ".$next .", " . $this->config->item("pagination"); 
			$result = $this->db->query($sql,$params);

			//$this->			
				
			echo $sql;
			
			if (!$result->num_rows())
			{
				return $xhtml;
			}
			else
			{
				$xhtml .= $this->display_table($result);
			}

			// Pagination
			$config['base_url'] = $this->me.'/'.$order.'/'.$desc.'/';
			$config['total_rows'] = $rowtotal['total'];
			$config['per_page'] = $this->config->item("pagination");

			$this->pagination->initialize($config);

			$xhtml .= "<span id='".$this->me."_pagination'>".$this->pagination->create_links()."</span>";
			
			return $xhtml;
	}
	
	
	/**
	* search
	* search and get applicant table
	*/
	public function search($searchterm, $page=0,$order="id",$desc="desc")
	{

			$sqltotal = ""; 
			$resulttotal = $this->db->query($this->sqltotalsearch,array($searchterm,$searchterm));
			$rowtotal =  $resulttotal->row_array();
			
			$this->desc = ($desc=="desc")?"asc":"desc";
			$this->page = (preg_match("/^[0-9]+$/",$page) && $page < $rowtotal['total'] )?$page:0;
			$next = $this->page;
									
			$xhtml = "";
			$searchterm = "%".$searchterm ."%";
			
			// search for applicant
			$sql = $this->sqlsearch . $order." " .$desc. " limit ".$next .", ".$this->config->item("pagination"); 
			$result = $this->db->query($sql,array($searchterm,$searchterm));

						
			if (!$result->num_rows())
			{
				$xhtml = "0 registros encontrados.";
			}
			else
			{
				$xhtml = $this->display_table($result);
			}
			
			// Pagination
			$config['base_url'] = $this->me.'/'.$order.'/'.$desc.'/';
			$config['total_rows'] = $rowtotal['total'];
			//$config['anchor_class'] = "applicant_page";
			$config['per_page'] = $this->config->item("pagination");

			$this->pagination->initialize($config);

			$xhtml .= "<span id='".$this->me."_pagination'>".$this->pagination->create_links()."</span>";
			
			
			return $xhtml;
	}
	

	/**
	* display_table
	* draws data table with given fields and records
	*/
	private function display_table ($records) 
	{
		$xhtml = "";
					$xhtml .= "<table summary='".$this->me."' id='".$this->me."_table'>\n<tr>";
				
				// table headers
				foreach ($this->fields as $f)
					$xhtml .= "<th><a href='".$f."/".$this->desc."/".$this->page."'  class='".$this->me."_reorder'>".ucfirst($f)."</a></th>";

				$xhtml .= "<th>Update</th><th>Delete</th>";
				$xhtml .= "</tr>\n";
				
				// table data
				foreach ($records->result_array() as $row)
				{
					$xhtml .= "<tr id='tr_".$this->me."_".$row['id']."'>";
					foreach ($this->fields as $f)
						$xhtml .= "<td>".$row[$f]."</td>";
					
					$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_update_".$row['id']."' title='update ".$this->me."'>Update</a></td>";
					$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_delete_".$row['id']."' title='delete ".$this->me."'>Delete</a></td>";
					$xhtml .= "</tr>";
				}
				
				$xhtml .= "</table>";	
		return $xhtml;
	}
	
	
	
	/**
	* get_row
	* get table row for a record
	*/
	public function get_row($key,$rowhead=TRUE)
	{
			$xhtml = "";

			// select one
			$result = $this->db->query($this->sql,array($key));
			
			if (!$result->num_rows())
			{
				return $xhtml;
			}
			else
			{
				// table data
				$row =  $result->row_array();
				
				$xhtml .= ($rowhead)?"<tr id='tr_".$this->me."_".$row['id']."'>":"";
					foreach ($this->fields as $f)
						$xhtml .= "<td>".$row[$f]."</td>";
					
				$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_update_".$row['id']."' title='update ".$this->me."'>Update</a></td>";
				$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_delete_".$row['id']."' title='delete ".$this->me."'>Delete</a></td>";
				$xhtml .= ($rowhead)?"</tr>":"";
			}
			
			return $xhtml;
	}
	

	/**
	* show
	* gets applicant data
	*/
	public function show($id)
	{
			// select one
			$result = $this->db->query($this->sql,array($id));
			
			if (!$result->num_rows())
			{
				return FALSE;
			}
			else
			{
				return $result->row_array();
			}
	}
	
	/**
	* check
	* check if applicant exist
	*/
	public function check($id)
	{
			// check for record
			$result = $this->db->query($this->sql,array($id));
			
			return $result->num_rows();
	}
	
	/**
	* create
	* Creates new applicant
	*/
	public function create($nif,$nombre,$idcentro)
	{
			
			// Check for FK new records
 			if ($idcentro=="0" && $this->input->post("idcentro_visible"))
 			{
 				$data = array("nombrecentro"=>$this->input->post("idcentro_visible"));
				$this->db->insert("tbcentros",$data);
				$idcentro = $this->db->insert_id();
 				
 			}
			// create new applicant
			$data = array("nif"=>$nif,"nombre"=>$nombre,"idcentro"=>$idcentro);
			$this->db->insert("applicant",$data);
			return $this->db->insert_id();
	}
	
	/**
	* update
	* update applicant
	*/
	public function update($id,$nif,$nombre,$idcentro)
	{
			// Doesn't exist? create it!
			if (!$this->show($id))
			{			
				echo "dont exist";
				$this->create($nif,$nombre,$idcentro);
			}		
			else
			{
				// Check for FK new records
	 			if (!$idcentro && $this->input->post("idcentro_visible"))
	 			{
	 				$data = array("nombrecentro"=>$this->input->post("idcentro_visible"));
					$this->db->insert("tbcentros",$data);
					$idcentro = $this->db->insert_id();
	 				
	 			}

	 			// update del fichero
				$data = array("nif"=>$nif,"nombre"=>$nombre,"idcentro"=>$idcentro);
				$this->db->where("id",$id);
				$this->db->update("applicant",$data);
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
			$this->db->delete("applicant");
			
			return true;
	}	
	
	
	/**
	* get_form
	* gets applicant form
	*/
	public function get_form($applicant=0,$action="create")
	{
		$xhtml = "";
		$id = $nif = $nombre = "";
		$idcentro = 0;

		if ($applicant)
		{			
			$result = $this->db->query($this->sql,array($applicant));
			if ($result->num_rows())
			{
				$row = $result->row_array();
				$id = $applicant;
				$nif = $row["nif"];
				$nombre = $row["nombre"]; 
				$idcentro = $row["idcentro"];
			}
		}	
		else
		{
				$id = $this->input->post("id");
				$nif = $this->input->post("nif");
				$nombre = $this->input->post("nombre"); 
		}

				  $xhtml .=  form_open("applicant/".$action,array("id"=>"form_applicant_update")); 
           	  $xhtml .=  form_fieldset()."<legend>"."applicant"."</legend>"; 

                 if (isset($form_result)) { 
                	$xhtml .=  "<span>".$form_result ."</span>";
                  }

				$xhtml .= " <!-- field id -->";
   				$xhtml .=  form_error("id");      
//   				 $xhtml .=  form_hidden("id",$id,"id") ;
                $xhtml .=  form_label($this->lang->line('id'), 'id')."<br />"; 
                $xhtml .=  form_input(array("name"=>"id","id"=>"id","value"=>$id,"readonly"=>"readonly")) ."<br />";
                         
				$xhtml .= " <!-- field nif -->";
                $xhtml .=  form_label($this->lang->line('nif'), 'nif'); 
				$xhtml .=  form_error("nif") ."<br />"; 
                $xhtml .=  form_input(array("name"=>"nif","id"=>"nif","value"=>$nif)) ."<br />";

				$xhtml .= " <!-- field nombre -->";
                $xhtml .=  form_label($this->lang->line('nombre'), 'nombre'); 
				$xhtml .=  form_error('nombre') ."<br />"; 
                $xhtml .=  form_input(array("name"=>"nombre","id"=>"nombre","value"=>$nombre)) ."<br />";
				
				$xhtml .= " <!-- field idcentro -->";
                $xhtml .=  form_label($this->lang->line('idcentro'), 'idcentro'); 
				$xhtml .=  form_error('idcentro') ."<br />"; 
				$xhtml .= $this->generateAutcomplete("idcentro","select * from tbcentros where codcentro=?",array($idcentro),array("codcentro","nombrecentro"),"","autocomplete/centros");

				//$xhtml .= "<script>$('#idcentro_visible').autocomplete('Pepe Juan Domingo Dominga');</script>";
                
               	 //$xhtml .=  form_submit("submit_applicant",$this->lang->line('submit')) ."<br />";
               

            $xhtml .= "<div class='clear'></div>";

             $xhtml .=  form_fieldset_close(); 
             $xhtml .=  form_close(); 	
		return $xhtml;
	}
	
	
	protected function generateAutcomplete ($field,$sqlquery="",$params=null,$fields=null,$selected="",$url="",$ismultiple=FALSE,$allow_new=TRUE)
	{
			$idvalue = $visiblevalue = "";

			// change input mode if multiple			
			if ($ismultiple) 
			{
				$multiple = "multiple: true, mustMatch: true, "; 

 				$ajaxresult = "\$('#".$field."').val( (\$('#".$field."').val() ? \$('#".$field."').val() + \";\" : \$('#".$field."').val()) + ui.item.id)";
				$textbox = form_textarea(array("name"=>$field."_visible","id"=>$field."_visible","value"=>$visiblevalue,"rows"=>3,"cols"=>40));
			}
			else
			{
				$multiple = "";
				$ajaxresult = "\$('#".$field."').val(ui.item.id)";
				$textbox = form_input(array("name"=>$field."_visible","id"=>$field."_visible","value"=>$visiblevalue));
			}
		
			if ($sqlquery !="")
			{
				$result = $this->db->query($sqlquery,$params);
				if ($result->num_rows())
				{
					$row = $result->row_array();
					$idvalue = $row[$fields[0]];
					$visiblevalue = $row[$fields[1]]; 
				}
			}
			
			$xhtml = "";
			$xhtml .=  "<input type='hidden' id='".$field."' name='".$field."' value='".$idvalue."' />";
			$xhtml .=  $textbox;
			
			if ($allow_new)
			{
				$xhtml .= "<a href='' id='cb_".$field."' style='color: gray;' >¿es nuevo?</a>&nbsp;&nbsp;|";
				$xhtml .= "&nbsp;&nbsp;<span class='new_note' id='".$field."_note'></span><br />\n";
			}			
			
			$xhtml .= "<script>
						var c = 0;
 							\$(function(){   
  							  \$('#".$field."_visible').autocomplete({
  							  											source: '".$url."',
  							  											minLength: 0,".$multiple."
  							  											select: function( event, ui,formatted ) {
																		if ( ui.item)
																		{
																			".$ajaxresult."
																		}
																			}
  							  											
																	});

 							 });
 							 
 							\$('#cb_".$field."').click(function(ev) {
									ev.preventDefault();
									if ($(this).attr('href')=='')
									{	
										\$(this).css('color','green');
	  									\$(this).attr('href','1');
	  									\$(this).html('¡nuevo!');
  										if(!c){\$(\"#".$field."\").val(\"0\");c=0;\$(\"#".$field."_note\").text(\"Atención: se insertará un nuevo registro.\");}
  									}
  									else 
  									{
  									\$(\"#".$field."\").val(\"0\");c=0;\$(\"#".$field."_visible\").val(\"\");
  										\$(this).css('color','gray');
	  									\$(this).html('¿es nuevo?');
  										\$(this).attr('href','');
  										\$(\"#".$field."_note\").text(\"\");
										}  
  									}); 
							</script>\n";
			return $xhtml;
	}
	
	
}

/* End of file entity_model.php */
/* Location: ./application/models/entity_model.php */