<?php

/**
* Tags
*/

class Tags extends CI_Model {


	public function __construct ()
	{
		parent::__construct();

	}

	/**
	* getTags
	* Get tags for a given file
	*/
	public function getTags ($file)
	{

			$result = $this->db->query("SELECT tags.id, tags.tag FROM file_tag inner join (tags) on (file_tag.idtag=tags.id) WHERE idfile=".$file);
			return $result;
	}
	

	/**
	* setTags
	* setTags for file
	*/	
	public function setTags ($tags,$file)
	{
	
		if ($this->permissions->has_permission($file))
		{
			$finaltags = array();
			$finaltagstext = array();
			
			$ts = split(",",$tags);
			$ok = 1;
		
			foreach ($ts as $t)
			{
				$t = trim($t);
			
				if ($t=="" || in_array($t,$finaltagstext)) continue;
			
				$result = $this->db->query("select * from tags where tag='".$t."'");
 
				if ($result->num_rows())
				{
					$r = $result->row_array();
					$finaltags[] = $r["id"];
				}
				else
				{
					$data = array("tag"=>$t);
					$this->db->insert("tags",$data);
					$lastid = $this->db->insert_id();

					$finaltags[] = $lastid;
				}
					$finaltagstext[] = $t;
			}//foreach		
		
			// We delete previous
			$this->db->delete("file_tag", array("idfile" => $file)); 
	
			// And insert
			foreach ($finaltags as $ft)
			{
				$data = array("idfile"=>$file,"idtag"=>$ft);
				$this->db->insert("file_tag",$data);
			}//foreach
			
			return SUCCESS;
		}
		else // permission denied
		{
			return PERMISSION_DENIED;
		}

		return "";
		
	}
	
	/**
	* get_similar
	* Get similar tags for autocomplete form input
	*/
	public function get_similar ($tags)
	{
			$result = "";

		if ($tags != "") {
			$tagdata = $this->db->query("SELECT tags.tag FROM tags WHERE tag like '%".$tags."%' order by tag");
			
			if ($tagdata->num_rows())
			{
					$result .= "<h3>Similares</h3><ul id='proposedtags'>";
					foreach ($tagdata->result_array() as $row)
						$result .= "<li>".$row["tag"]."</li>\n";
		
				$result .= "</ul>";
			}
		}
		
		$tagdata = $this->db->query("SELECT tags.tag FROM tags WHERE habitual=1 order by tag");
			
		if ($tagdata->num_rows())
		{
				$result .= "<h3>Habituales</h3><ul id='proposedtags'>";
				foreach ($tagdata->result_array() as $row)
					$result .= "<li>".$row["tag"]."</li>\n";
		
			$result .= "</ul>";
		}			
			return $result;
	}
	
}
/* End of file tags.php */
/* Location: ./application/models/tags.php */