<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* File, contains all logic for file management
*/



class File extends CI_Model {
	public $file;
	public $who;
	private $dnum1;
	private $dnum2;
	public $dircrawl;	
	public $filepath;
	private $umask;
	public $total_files;
	private $status_criteria = "file.status in (0,4)";

	public function __construct ($file="",$who="")
	{
        // Call the Model constructor      
      parent::__construct();
      $this->load->model("permissions");
      $this->file = $file;
		$this->who = $who;
		//$this->db = new Db();
		$this->dnum1 = 26;
		$this->dnum2 = 1024;
		$this->dircrawl = array();
		$this->filepath = array();
		$this->umask = array("user_r"=>1,"user_w"=>1,"user_a"=>1,"group_r"=>1,"group_w"=>1,"group_a"=>0,"world_r"=>1,"world_w"=>0,"world_a"=>0);
		
		// Change status clause: show all if user is admin!!!
		if ($this->permissions->is_admin()) {$this->status_criteria = "file.status >-1";}
	}


	public function dirData($file)
	{
		if ($this->permissions->has_permission($file))
		{
			$sql = "select file.*, filef.name as fname from file left join file as filef on filef.id=file.fatherid where file.id=".$file ." and ".$this->status_criteria."  order by type";

			$result = $this->db->query($sql);
			return $result;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}
	
	/**
	* fileData
	* gets file basic data
	*/
	public function fileData($file,$permission=READ_PERMISSION)
	{
	
		if ($this->permissions->has_permission($file,$permission))
		{
			$r = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.id=".$file ."  and ".$this->status_criteria." order by type");
			//$filedata = $r->row_array();
			
			return $r;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}

	/**
	* file_content
	* gets file content
	*/
	public function file_content($file)
	{
	
		if ($this->permissions->has_permission($file))
		{
				$c = $this->db->query("select * from file_content inner join file on file.id=file_content.idfile where idfile=".$file ."  and ".$this->status_criteria." order by date desc limit 0,1");
				$editedfile = $c->row_array();
				$content = base64_decode($editedfile["content"]);
				
				return $content;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}


	/**
	* last_created
	* returns last created files
	*/
	public function last_created($how_many=10)
	{
			$result = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id  where ".$this->status_criteria." order by created desc limit 0,".$how_many);
			return $result;
	}

	/**
	* most_popular
	* returns last created files
	*/
	public function most_popular($how_many=10)
	{
			$result = $this->db->query("select idfile1,count(idfile1) as total, file.*,a3m_account.username as creator from auditory inner join file on auditory.idfile1=file.id  left join a3m_account on file.who=a3m_account.id where file.id<>1 and idoperation=3 and ".$this->status_criteria."  group by idfile1 order by total desc limit 0,".$how_many);
			return $result;
	}
	
	
	/**
	* most_voted
	* returns last created files
	*/
	public function most_voted($how_many=10)
	{
	
			$result = $this->db->query("select idfile,avg(vote) as total, file.*,a3m_account.username as creator from file_vote left join file on file_vote.idfile=file.id  left join a3m_account on file.who=a3m_account.id where ".$this->status_criteria."  group by idfile order by total desc limit 0,".$how_many);
			return $result;
	}

	/**
	* highlighted
	* returns highlighted files
	*/
	public function highlighted($how_many=10)
	{
	
			$result = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.status=4 order by created desc limit 0,".$how_many);
			return $result;
	}
		
	public function load_edited_file($file)
	{
	
		if ($this->permissions->has_permission($file))
		{
			$r = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.id=".$file ."  and ".$this->status_criteria."  order by type");
			$filedata = $r->row_array();
			
			//$this->load->library('output'); 
			

			// Is online edited file?
			if ($filedata["type"]==2)
			{
			// Then read content and display
				$c = $this->db->query("select * from file_content where idfile=".$file ." order by date desc limit 0,1");
				$editedfile = $c->row_array();
				$content = base64_decode($editedfile["content"]);
				$this->output->set_content_type(".html");
			
			}
			else // any other file
			{
				$filepath = $this->config->item("data_dir").$this->path($file);
				$content = file_get_contents($filepath);
				//$ext = ($filedata["ext"]=="")?"":".".$filedata["ext"];
				
				$this->output->set_header('Content-Disposition: attachment; filename="'.$filedata["name"].'"');
				$this->output->set_content_type(".".$filedata["ext"]); // You could also use ".jpeg" which will have the full stop removed before looking in config/mimes.php
    		
			}		
		
			$this->output->set_output($content);
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}
	



	/**
	* dir
	* queries every content for a dir
	*/
	public function dir ($file,$page=0,$limit=10,$order="type asc")
	{
		if ($this->permissions->has_permission($file))
		{
			$sql = "select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where fatherid=".$file . " and ".$this->status_criteria." order by " . $order;
						// Calculate total
			$tresult = $this->db->query($sql);
			$this->total_files = $tresult->num_rows();
			
			$page = ($this->total_files < $page)?0:$page;

			$sql .= " limit $page,$limit";
			
			$r = $this->db->query($sql);
			$this->auditory->audit(3,$this->session->userdata('iduser'),$file,"","jar");		

			return $r;
		}
		else
		{
			$this->auditory->audit(3,$this->session->userdata('iduser'),$file,"","Access DENIED", false);		
			return PERMISSION_DENIED;
		}
	}


	/**
	* dir_dir
	* queries dir inside a dir
	*/
	public function dir_dir ($file)
	{
		if ($this->permissions->has_permission($file))
		{
			$r = $this->db->query("select * from file where fatherid=".$file . " and type=0 and ".$this->status_criteria." order by type asc");
			return $r;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}
	
	
	/**
	* setDescription
	* set description for file
	*/
	public function setDescription ($desc)
	{
		if ($this->file == 1) {return PERMISSION_DENIED;}

		if ($this->permissions->has_permission($this->file,ADMIN_PERMISSION))
		{
			
			// update del fichero
			$data = array("description"=>$desc);
			$this->db->where("id",$this->file);
			$this->db->update("file",$data);

			return SUCCESS;
		}
		else
		{
			return PERMISSION_DENIED;
		}
				
	}

	/**
	* remove
	* Remove files
	*/	
	public function remove ($file)
	{
		if ($file == 1) {return PERMISSION_DENIED;}

		if ($this->permissions->has_permission($file,ADMIN_PERMISSION))
		{
			$r = $this->db->query("update file set status=1 where id=".$file );
			$data = array("status"=>1);
			$this->update_crawl($file,$data);

			return SUCCESS;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}

	/**
	* removeMultiple
	* removes some file/dirs
	* TODO: avoid delete of root
	*/
	public function removeMultiple ($file)
	{
		$ok = 1;	
		$files = split(",",$file);
		$delete = array();
		$notallowed = array();

		$data = array("status"=>1);
		
		foreach($files as $f )
		{	
			if ($f !=1 && $this->permissions->has_permission($f,ADMIN_PERMISSION))
			{
				$delete[] = $f;
				$this->update_crawl($f,$data);
				$this->auditory->audit(14,$this->session->userdata('iduser'),$f,null,"Delete ",1);
			}
			else
			{
				$this->auditory->audit(14,$this->session->userdata('iduser'),$f,null,"Delete not allowed",0);
				$notallowed[] = $f;
				$ok=0;
			}
		}
		
			$total = implode(",",$delete);
			$denied = implode(",",$notallowed);
			//echo $ok.":".$total.":".$denied;
			if ($delete)
			{
				$this->db->where_in("id",$delete);
				$this->db->update("file",$data);
			}			
			return $ok.":".$total.":".$denied;
	
	}
	

	/**
	* create_file
	* creates new edited file
	*/
	public function create_file ($dir,$file_name,$file_text,$desc,$tags,$type=2)
	{

		if ($this->permissions->has_permission($dir,WRITE_PERMISSION))
		{
			$r=0;
			$md5 = md5($file_text);
  			//echo "Entrada: $entrada <br />";
  			$data = array("fatherid"=>$dir,"name"=>$file_name,"description"=>$desc,"type"=>$type,"size"=>strlen($file_text),"md5"=>$md5,"who"=>$this->session->userdata('iduser'),"idgroup"=>$this->session->userdata('idgroup'),"created"=>time());
  			$data = array_merge($data,$this->umask);

			if ($this->db->insert("file",$data))	{
				$r = $this->db->insert_id();

				$data = array("idfile"=>$r,"content"=>base64_encode($file_text),"iduser"=>1);
			
				$this->db->insert("file_content",$data);
			
				return $r;
			}
			else
			{
				echo "Error al insertar : $sql " .mysql_error();
				return 0;
			}
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}	


	/**
	* update_file
	* updates edited file
	*/
	public function update_file ($id,$dir,$file_name,$file_text,$desc,$tags,$type=2)
	{
		if ($this->permissions->has_permission($id,WRITE_PERMISSION))
		{
			$r=0;
			$md5 = md5($file_text);
  			//echo "Entrada: $entrada <br />";
  			$data = array("fatherid"=>$dir,"name"=>$file_name,"description"=>$desc,"type"=>$type,"size"=>strlen($file_text),"md5"=>$md5,"updated"=>time());
  			$this->db->where("id",$id);

			if ($this->db->update("file",$data))	{

				$data = array("idfile"=>$id,"content"=>base64_encode($file_text),"iduser"=>1);
			
				$this->db->insert("file_content",$data);
			
				return $r;
			}
			else
			{
				echo "Error al modificar : $sql " .mysql_error();
				return 0;
			}
		
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}	
	
	/**
	* upload
	* insert file information into database
	* and calls savefile to store file on filesystem
	*/
	public function upload ($dir)
	{

	$overwrited = 0;


		if ($this->permissions->has_permission($dir,WRITE_PERMISSION))
		{
			$r=0;
			$entrada = $this->qqfileuploader->uploadedfile->getName();
			
			$overwrited = ($this->check_duplicated_on_upload($dir,$entrada))?1:0;
			
			$size = $this->qqfileuploader->uploadedfile->getSize();
			$md5 = "";
  			$suf = pathinfo($entrada);
  			$extension = (isset($suf['extension']))?$suf['extension']:"";
  			
  			//echo "Entrada: $entrada <br />";
  			$data = array("fatherid"=>$dir,"name"=>$entrada,"type"=>1,"ext"=>$extension,"size"=>$size,"md5"=>$md5,"who"=>$this->session->userdata('iduser'),"idgroup"=>$this->session->userdata('idgroup'),"created"=>time());
  		  	$data = array_merge($data,$this->umask);
			//$sql = "insert into file (fatherid,name,type,ext,md5) values(".$dir.",'".$entrada."',1,'".$suf['extension']."','".$md5."')";
			if ($this->db->insert("file",$data)){
				$r = $this->db->insert_id();

				if ($this->savefile(array($r,($r%$this->dnum1)."/".($r%$this->dnum2)."/") )!="")
				{
					// returns new file id
					return array($r,$overwrited);
				}
			}
			else
			{
				echo "Error al insertar : $sql " .mysql_error();
				return array(0,$overwrited);
			}
		
		}
		else
		{
			return array(PERMISSION_DENIED,$overwrited);
		}
	}
	
	
	/**
	* savefile
	* Saves file on filesystem
	*/
	private function savefile ($file)
	{
		$upload = $this->config->item("upload_path");
		$filepath = "";
		
		//echo $upload;
		$php_errormsg = "error";

  		$directory = ($file[0]%$this->dnum1)."/";

  			if (!file_exists($upload.$directory))
			{
				mkdir($upload.$directory) or die("<div class='error'>1 No pude crear el directorio: ".$upload.$directory ."<br /> ".$php_errormsg." </div>)");
			}
			$directory .= ($file[0]%$this->dnum2)."/";
  			if (!file_exists($upload.$directory))
			{
				mkdir($upload.$directory) or die("<div class='error'>2 No pude crear el directorio: ".$upload.$directory ."<br /> ".$php_errormsg." </div>)");
			}

		$filepath = $file[1].$file[0];
		//copy($source,$upload.$filepath) or die("<div class='error'>No pude mover el fichero: ".$source." a $filepath <br /> ".$php_errormsg." </div>");
		$this->qqfileuploader->handleUpload($upload,$filepath);

		return $filepath;
	}

	/**
	* mkdir
	* Creates new dir: $newdir: new dir name
	* current dir location is in $this->file
	*/
	public function mkdir ($newdir)
	{

		if ($this->permissions->has_permission($this->file,WRITE_PERMISSION))
		{
// FIX: harcoded group id
		  	$data = array("fatherid"=>$this->file,"name"=>$newdir,"type"=>0,"ext"=>"","md5"=>"","who"=>$this->session->userdata('iduser'),"idgroup"=>$this->session->userdata('idgroup'),"created"=>time());
//		  	$data = array("fatherid"=>$this->file,"name"=>$newdir,"type"=>0,"ext"=>"","md5"=>"","who"=>$this->session->userdata('iduser'),"idgroup"=>$this->session->userdata('idgroup'),"created"=>time());
		  	  		$data = array_merge($data,$this->umask);
			$this->db->insert("file",$data);
			$r = $this->db->insert_id();
			return $r;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}
	

	/**
	* direct_access
	* Acceso directo al fichero
	*/	
	public function direct_access ($newdir)
	{
		if ($this->permissions->has_permission($this->file,WRITE_PERMISSION))
		{
		  	$data = array("fatherid"=>$this->file,"name"=>$newdir,"type"=>0,"ext"=>"","md5"=>"","who"=>$this->session->userdata('iduser'),"idgroup"=>$this->session->userdata('idgroup'),"created"=>time());
		  	  		$data = array_merge($data,$this->umask);
			$this->db->insert("file",$data);
			$r = $this->db->insert_id();
			return $r;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}	
	
	
	/**
	* check_duplicated
	* checks if filename  is duplicated on destiny
	*/
	public function check_duplicated ($id,$name,$is_updated_file)
	{
		$r=0;
		$fatherid = "";
		if (!$this->permissions->has_permission($id))
		{
			return 0;
		}
		
		if ($is_updated_file)
		{
			$r = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.id=".$id ."  and ".$this->status_criteria." order by type");
			$filedata = $r->row_array();
		
			$fatherid = $filedata["fatherid"];
		
			// Check for duplicated	
			$dup = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.fatherid=".$fatherid ." and file.id<>".$id." and file.name='".$name."' and ".$this->status_criteria." order by type");
		}
		else // is a new dir or file
		{
			$dup = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.fatherid=".$id ."  and file.name='".$name."' and ".$this->status_criteria." order by type");
		}
		return $dup->num_rows();
	}	
	
	/**
	* check_duplicated_on_upload
	* checks if filename  is duplicated on destiny when upload
	*/
	public function check_duplicated_on_upload ($id,$name)
	{
		$r=0;

		// Check for duplicated	
		$dup = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.fatherid=".$id ."  and file.name='".$name."' and ".$this->status_criteria." order by type");

		return $dup->num_rows();
	}	
	
	/**
	* check_duplicated_on_move
	* checks if file/dir name is duplicated when moving to other dir
	*/
	public function check_duplicated_on_move ($idfile,$destinyid)
	{
		$r=0;

		$r = $this->db->query("select file.* from file where id=".$idfile." and ".$this->status_criteria." order by name");
		$filedata = $r->row_array();
	
		//print_r($filedata);
		$filename = $filedata["name"];
	
		// Check for duplicated	
		$dup = $this->db->query("select file.* from file where file.fatherid=".$destinyid ." and file.name='".$filename."' and ".$this->status_criteria." order by name");
		//print_r($dup->row_array());
		return $dup->num_rows();
	}
	
	/**
	* rename
	* Renombrar ficheros y permisos
	*/
	public function rename ($file,$newname)
	{
		if ($file == 1) {return;}

		if ($this->permissions->has_permission($file,WRITE_PERMISSION))
		{
			// update del fichero
			$data = array("name"=>$newname);
			$this->db->where("id",$file);
			$this->db->update("file",$data);

			return SUCCESS;
		}
		else
		{
			return PERMISSION_DENIED;
		}
	}
	
	/**
	* abort_overwrite
	* Abort Overwriting of uploaded file
	*/
	public function abort_overwrite($id)
	{
		$r=0;
		$fatherid = "";
		$upload = $this->config->item("upload_path");
		
			$r = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.id=".$id ."  and ".$this->status_criteria." order by type");
			$filedata = $r->row_array();
		
			$fatherid = $filedata["fatherid"];
			$name = $filedata["name"];

		if ($this->permissions->has_permission($fatherid,WRITE_PERMISSION))
		{	
			
			// Remove register
			$this->db->where('id', $id);
			$this->db->delete('file');
			
			// Remove file
			if (unlink($upload."/".($id%$this->dnum1)."/".($id%$this->dnum2)."/".$id))
			{
				return $id;
			} else {
				return false;
			}
		}
		else
		{
			return PERMISSION_DENIED;
		}
		
	}
	
	
	/**
	* overwrite
	* Overwrites uploaded file
	*/
	public function confirm_overwrite($origin)
	{
		$r=0;
		$fatherid = "";
		
			$r = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.id=".$origin ."  and ".$this->status_criteria." order by type");
			$filedata = $r->row_array();
		
			$fatherid = $filedata["fatherid"];
			$name = $filedata["name"];

		if ($this->permissions->has_permission($fatherid,WRITE_PERMISSION))
		{	
			// Check for duplicated	
			$dup = $this->db->query("select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where file.fatherid=".$fatherid ." and file.id<>".$origin." and file.name='".$name."' and ".$this->status_criteria." order by file.id desc");
			$dupdata = $dup->row_array();
			
			//print_r($dupdata);

			// Change status to OVERWRITE_STATUS
			$data = array("status"=>OVERWRITE_STATUS);
			$this->db->where("id",$dupdata["id"]);
			$this->db->update("file",$data);
				
			return $dupdata["id"];
		}
		else
		{
			return PERMISSION_DENIED;
		}
		
	}
	
	public function copy($source,$destiny)
	{
	}

	public function move($source,$destiny)
	{
		if ($source == 1) {return;}
		if (!$this->permissions->has_permission($destiny,WRITE_PERMISSION)) { return PERMISSION_DENIED; }
		
		$ok = 1;	
		$files = split(",",$source);
		$allowed2move = array();
		$notallowed = array();
		$duplicated = array();
		
		$destiny_data = $this->fileData($destiny);
		
		//if ($this->permissions->has_permission($source) && $this->permissions->has_permission($destiny) && !$destiny_data["type"])
		//{
			
			foreach($files as $f )
			{
				// Don't move	
				if ($f == $destiny) { continue; }
				
				// permission on destiny?
			
				if ($this->permissions->has_permission($f,WRITE_PERMISSION) )
				{
					//echo $f ." and ". $destiny;
					if ( $this->check_duplicated_on_move($f,$destiny))
					{
						$duplicated[] = $f;
						$this->auditory->audit(13,$this->session->userdata('iduser'),$f,$destiny,"Move FAILED, duplicated name",0 );

					}
					else
					{
						$allowed2move[] = $f;
						$this->auditory->audit(13,$this->session->userdata('iduser'),$f,$destiny );
					}
				}
				else
				{
					$this->auditory->audit(13,$this->session->userdata('iduser'),$f,$destiny,"Move FAILED, not owner",0 );
					$notallowed[] = $f;
					$ok=0;
				}
			}
			
			$total = $denied = $dupe = "";
			
			$total = implode(",",$allowed2move);
			$denied = implode(",",$notallowed);
			$dupe = implode(",",$duplicated);
			$r = 1;
			
			// update del fichero
			if (count($allowed2move))
			{
				$data = array("fatherid"=>$destiny);
				$this->db->where_in("id",$allowed2move);
				$this->db->update("file",$data);
			}

			
			return $ok.":".$total.":".$denied.":".$dupe;
	}

		
	/**
	* search
	* Search function, searches by name, description and tags
	*/
	public function search($description,$tags,$page=0,$limit=10,$order="type asc")
	{
		$sql = $sqlcount = "";
		
		$description = (!$description)?"":" (name like '%".$description."%' or description like '%".$description."%') and ";
		if ($tags=="")
		{
			$sql = "select file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id where ".$description." ".$this->status_criteria." ";
		}
		else
		{
			$t = split(",",$tags);
			trim($t[0]);
			$sql = "select distinct file.*,a3m_account.username as creator from file left join a3m_account on file.who=a3m_account.id  left join file_tag on (file.id=file_tag.idfile) inner join tags on (file_tag.idtag=tags.id) where ".$description." ".$this->status_criteria." and tags.tag='".$t[0]."'  ";
			array_shift($t); 
			foreach ($t as $tag)
			{
				$tag = trim($tag);
				$sql .= "and idfile in (select idfile from file_tag inner join tags on (file_tag.idtag=tags.id)  where tag='".$tag."') ";
			}


			//select file.name,tag from file inner join file_tag on (file.id=file_tag.idfile) inner join tags on (file_tag.idtag=tags.id) where ".$this->status_criteria." and tags.tag='meca' and idfile in (select idfile from file_tag inner join tags on (file_tag.idtag=tags.id)  where tag='c') order by type desc
		}
		
		$sql .=" order by " . $order;
			
		$sqlcount = $sql;
				
		$rtotal = $this->db->query($sqlcount);
		$this->total_files = $rtotal->num_rows();
		
		$page = ($this->total_files < $page)?0:$page;
		
		$sql .= " limit $page,$limit";
		//echo $tags . " eta " .$sql;
		
		$r = $this->db->query($sql);
		return $r;
//echo $sql;
	}
	

	/**
	* vote
	* Vote for file/dir
	*/
	public function vote($file,$vote)
	{
		if ($this->permissions->has_permission($file))
		{
			// Delete previous, if any
			$this->db->query("delete from file_vote where iduser=".$this->session->userdata('iduser')." and idfile=".$file);
			
			// Vote
			$data = array("idfile"=>$file,"iduser"=>$this->session->userdata('iduser'),"votedate"=>time(),"vote"=>$vote);
			$this->db->insert("file_vote",$data);			
			return SUCCESS;
		}
		else
		{
			return PERMISSION_DENIED;
		}
		
	}	
	
	/**
	* change_status
	* Change status for file/dir
	*/
	public function change_status($file,$status,$recursive)
	{
		
		if ($this->permissions->is_admin())
		{
			// update del fichero
			$data = array("status"=>$status);
			$this->db->where_in("id",explode(",",$file));
			$this->db->update("file",$data);	

			$this->auditory->audit(16,$this->session->userdata('iduser'),$file,"","New status:" . $status );		
			
			// if recursive, get dirs and move on
			if ($recursive)
			{
					$sql = "select id from file where type=0 and id in (?)";
					$result = $this->db->query($sql,array($file));
					
					foreach ($result->result_array() as $r)
					{
						$this->auditory->audit(16,$this->session->userdata('iduser'),$r["id"],"Recursive","New status:" . $status );		
						$this->update_crawl($r["id"], array("status"=>$status));
					}
					
			}
			
			return $status.":".$file;
		}
		else
		{
			$this->auditory->audit(16,$this->session->userdata('iduser'),$file,"","Access DENIED", false);
			return PERMISSION_DENIED;
		}
		
	}	
	
		/**
	* change_owners
	* Change owners for file/dir
	*/
	public function change_owners($file,$userowner,$groupowner,$recursive)
	{
		$changed = array();
		
		if ($this->permissions->is_admin())
		{

					
			// update del fichero
			$data = array("who"=>$userowner,"idgroup"=>$groupowner);
			$this->db->where_in("id",explode(",",$file));
			$this->db->update("file",$data);	

			// Get names
			$sql = "select file.*,a3m_account.username as creator,a3m_acl_role.name as creatorgroup from file left join a3m_account on file.who=a3m_account.id join a3m_acl_role on file.idgroup=a3m_acl_role.id where  type=0 and file.id in (?)";
			$resultfile = $this->db->query($sql,array($file));
			$rfile = $resultfile->row_array();
			
			$this->auditory->audit(16,$this->session->userdata('iduser'),$file,"","New owners:" . $userowner.":".$groupowner  );		
			
			// if recursive, get dirs and move on
			if ($recursive)
			{
					$sql = "select id from file where type=0 and id in (?)";
					$result = $this->db->query($sql,array($file));
					
					foreach ($result->result_array() as $r)
					{
						$this->auditory->audit(9,$this->session->userdata('iduser'),$r["id"],"Recursive","New owners:" . $userowner.":".$groupowner );		
						$this->update_crawl($r["id"], array("who"=>$userowner,"idgroup"=>$groupowner));
					}
					
			}
			
			return $userowner.":".$rfile["creator"].":".$groupowner.":".$rfile["creatorgroup"].":".$file;
		}
		else
		{
			$this->auditory->audit(9,$this->session->userdata('iduser'),$file,"","Access DENIED", false);
			return PERMISSION_DENIED;
		}
		
	}	
	
	/**
	* change_permissions
	* Change permissions for file/dir
	*/
	public function change_permissions($file,$user_r,$user_w,$user_a,$group_r,$group_w,$group_a,$world_r,$world_w,$world_a,$recursive)
	{
		
		$files = preg_split("/^,$/",$file);
		$allowed = array();
		$disallowed = array();
		
		foreach ($files as $f)
		{
			// Is admin or has any permission?
			if ($this->permissions->is_admin() || $this->permissions->has_permission($f,ADMIN_PERMISSION))
			{
				$allowed[] = $f;
				
				// update del fichero
				$data = array("user_r"=>$user_r,"user_w"=>$user_w,"user_a"=>$user_a,"group_r"=>$group_r,"group_w"=>$group_w,"group_a"=>$group_a,"world_r"=>$world_r,"world_w"=>$world_w,"world_a"=>$world_a);
				$this->db->where_in("id",explode(",",$file));
				$this->db->update("file",$data);	
				$this->auditory->audit(8,$this->session->userdata('iduser'),$file,"","New perm:" . implode(",",$data));		


				// if recursive, get dirs and move on
				if ($recursive)
				{
					// prepare...
					$sons = array($f,$this->session->userdata('iduser'),implode(",",$this->session->userdata('groups')));
					$sql = "select * from file where fatherid=? and (who=? or (idgroup in (?) and group_a=1) or world_a=1)";
					// and here we go
					$this->update_crawl_parameters($sql,$sons,$f,$data);	
				}

			}
			else
			{
				$disallowed[] = $f;
				$this->auditory->audit(8,$this->session->userdata('iduser'),$f,"","Access DENIED", false);	
			}			
		}

		return $user_r.",".$user_w.",".$user_a.",".$group_r.",".$group_w.",".$group_a.",".$world_r.",".$world_w.",".$world_a.":".implode(",",$allowed).":".implode(",",$disallowed);
		
	}	
	
	/**
	* reverse_crawl
	* searches inside a dir and gets every file/dir id
	* Limits... to 5 levels
	*/
	public function reverse_crawl($dir)
	{
			$r = $dir;
			$this->filepath = array();
			
			do {
				$result = $this->db->query("select id,name,fatherid from file where ".$this->status_criteria." and id=".$r);
				$row = $result->row_array();
				//print_r($row);
				array_unshift($this->filepath,array($row["id"],$row["name"]));
				//$this->filepath[] = array($row["id"],$row["name"]);
				$r = $row["fatherid"]; 
			} while ($r!="0" && count($this->filepath)<10);
		
			// If passed, then give ...
			if (count($this->filepath) >= 10)
					array_unshift($this->filepath,array($row["id"],"..."));
	}
	
	/**
	* update_crawl
	* searches inside a dir and update some value in every file/dir 
	*/
	public function update_crawl($dir,$data)
	{
			$this->db->where("fatherid",$dir);
			$this->db->update("file",$data);
			$result = $this->db->query("select id from file where type=0 and fatherid=".$dir);

			foreach ($result->result_array() as $row)
					$this->update_crawl($row["id"],$data);
		
	}
	
	/**
	* update_crawl_param
	* searches inside a dir and update some value in every file/dir 
	*/
	public function update_crawl_parameters($sql,$param,$dir,$data)
	{
			$this->db->where("fatherid",$dir);
			$this->db->update("file",$data);
			$result = $this->db->query($sql,$param);

			foreach ($result->result_array() as $row)
					$this->update_crawl_parameters($sql,$param,$row["id"],$data);
		
	}
	
	private function path ($id)
	{
		return (($id%26)."/".($id%1024)."/".$id);
	}
}

/* End of file file.php */
/* Location: ./application/models/file.php */
