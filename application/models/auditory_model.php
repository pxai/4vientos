<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Auditory_model, gets information about a file
*/


class Auditory_model extends Entity_model {
	
	protected $me = "applicant";
	protected $fields = array("auditid","username","file","operation","description");
	protected $desc = "desc";
	protected $page = 0;
	protected $sql = "select auditory.id as auditid, from_unixtime(auditory.when) as auditdate, a3m_account.username as user,file.name as file,operation_type.name as operation,auditory.* from auditory inner join a3m_account on a3m_account.id=auditory.iduser inner join file on file.id=idfile1 inner join operation_type on operation_type.id=idoperation  WHERE auditory.id=? "; 
	protected $sqlall = "select  auditory.id as auditid, from_unixtime(auditory.when) as auditdate, a3m_account.username,file.name as file,operation_type.name as operation,auditory.* from auditory inner join a3m_account on a3m_account.id=auditory.iduser inner join file on file.id=idfile1 inner join operation_type on operation_type.id=idoperation  WHERE idfile1=?"; 
	protected $sqltotal = "select count(*) as total from auditory where idfile1=?";
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
				
			//echo $sql;
			
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

			//	$xhtml .= "<th>Update</th><th>Delete</th>";
				$xhtml .= "</tr>\n";
				
				// table data
				foreach ($records->result_array() as $row)
				{
					$xhtml .= "<tr id='tr_".$this->me."_".$row['id']."'>";
					foreach ($this->fields as $f)
						$xhtml .= "<td>".$row[$f]."</td>";
					
				//	$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_update_".$row['id']."' title='update ".$this->me."'>Update</a></td>";
				//	$xhtml .= "<td><a href='".$row['id']."' class='".$this->me."_record_delete_".$row['id']."' title='delete ".$this->me."'>Delete</a></td>";
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
	
}

/* End of file entity_model.php */
/* Location: ./application/models/entity_model.php */