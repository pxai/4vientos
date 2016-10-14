<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Operation controller for all file operations
*/
class Operation extends CI_Controller {

	/**
	* index
	* default function
	*/
	function index()
	{
		// Información de Rendimiento
		//$this->output->enable_profiler(TRUE);
		$this->load->model('tags');
		$this->load->model('display');
	}
	
	/**
	* 	delete
	*  function to delete one FILE/DIR
	*/
	function delete()
	{
				$this->filter->sanitize_post();
		$ok = $this->file->remove($this->input->post("id"));
		if ($ok!="-1") 
		{
			echo "1";
			//audit($what, $who, $file1="",$file2="",$description="")
			$this->auditory->audit(14,$this->session->userdata('iduser'),$this->input->post("id"),"","Single delete");
		}
		else
		{
			echo "-1";
			$this->auditory->audit(14,$this->session->userdata('iduser'),$this->input->post("id"),"","Single delete",0);
		}
	}

	/**
	* 	delete_all
	*  function to delete one or more FILE/DIR
	*/
	function delete_all()
	{
				$this->filter->sanitize_post();
		$ok = $this->file->removeMultiple($this->input->post("id"));
		echo $ok;
	}

	/**
	* upload
	* function to upload one file
	*/
	function upload ()
	{
			$this->filter->sanitize_post();
		//$this->load->library('upload');
		$this->config->load('upload');
		$this->load->model('tags');
		$this->load->model('qqfileuploader');

			
		$result = $this->file->upload($_GET["dir"]);

	
		if ($result[0] > 0)
		{
			echo  '{"success":"true" , "file": "'.$result[0].'","overwrite": "'.$result[1].'"}';
			$this->auditory->audit(12,$this->session->userdata('iduser'),$result[0],$_GET["dir"],"File upload");
				//echo  "{success:true, data: " . ."}";		
		} else {
			echo  '{"success":"false" , "file": "-1","overwrite": "'.$result[1].'"}';			
			$this->auditory->audit(12,$this->session->userdata('iduser'),$result[0],$_GET["dir"],"File upload failed",0);
		}


	}
	
	
	/**
	* confirm_overwrite
	* function to confirm the overwrite  one file
	*/
	function confirm_overwrite ($id)
	{
		$this->filter->sanitize_post();

		//$this->load->library('upload');
		$this->config->load('upload');
		$this->load->model('tags');
		
		if ($result = $this->file->confirm_overwrite($id))
		{
			echo  '{"success":"true" , "file": "'.$result.'","id":"'.$id.'"}';
			$this->auditory->audit(18,$this->session->userdata('iduser'),$result,$this->input->post("id"),"File overwrite");

			//echo  "{success:true, data: " . ."}";		
		}
		else
		{
			$this->auditory->audit(18,$this->session->userdata('iduser'),$result,$this->input->post("id"),"File overwrite failed",0);
		}

	}

	/**
	* abort_overwrite
	* function to abort the overwrite  one file
	*/
	function abort_overwrite ($id)
	{
		$this->filter->sanitize_post();

		//$this->load->library('upload');
		$this->config->load('upload');
		$this->load->model('tags');
		
		if ($result = $this->file->abort_overwrite($id))
		{
			echo  '{"success":"true" , "file": "'.$result.'","id":"'.$id.'"}';
			$this->auditory->audit(18,$this->session->userdata('iduser'),$result,$this->input->post("id"),"File overwrite aborted");

			//echo  "{success:true, data: " . ."}";		
		}
		else
		{
			$this->auditory->audit(18,$this->session->userdata('iduser'),$result,$this->input->post("id"),"File overwrite abort failed",0);
		}

	}	
	/**
	* overwrite
	* function to upload one file
	*/
	function overwrite ()
	{
		$this->filter->sanitize_post();
		//$this->load->library('upload');
		$this->config->load('upload');
		$this->load->model('tags');
		
		$name = $_FILES['qqfile']['name'];
		$data = $_FILES['qqfile']['tmp_name'];
		$size = $_FILES['qqfile']['size'];
		if ($result = $this->file->overwrite($_GET["dir"],$name,$data,$size))
		{
			echo  '{"success":"true" , "file": "'.$result.'"}';
			$this->auditory->audit(12,$this->session->userdata('iduser'),$result,$_GET["dir"],"File upload");

			//echo  "{success:true, data: " . ."}";		
		}
		else
		{
			$this->auditory->audit(12,$this->session->userdata('iduser'),$result,$_GET["dir"],"File upload failed",0);
		}

	}
	
	/**
	* 
	*  function
	*/
	function mkdir()
	{
		$this->filter->sanitize_post();
		$this->load->model('display');
		$this->load->model('tags');
		$this->file->file = $this->input->post("id");
		if ( ($newid = $this->file->mkdir($this->input->post("dirname"))) != -1)
		{	$this->auditory->audit(11,$this->session->userdata('iduser'),$newid,$this->input->post("id"),"New dir: ".$this->input->post("dirname"));

			// with the new id we create file div
			$result = $this->file->fileData($newid);
			$filedata = $result->row_array(); 
			echo $this->display->showFile($filedata);
		}
		else
		{
			echo $newid;
		}
	}
	
		/**
	* 
	*  function
	*/
	function direct_access()
	{
		$this->filter->sanitize_post();
		$this->load->model('display');
		$this->load->model('tags');
		$this->file->file = $this->input->post("id");
		$newid = $this->file->direct_access($this->input->post("dirname"));
		$this->auditory->audit(11,$this->session->userdata('iduser'),$newid,$this->input->post("id"),"New dir: ".$this->input->post("dirname"));

		// with the new id we create file div
		$result = $this->file->fileData($newid);
		$filedata = $result->row_array(); 
		echo $this->display->showFile($filedata);
	}
	
	
	/**
	* move
	* function that moves file/dir to other dir
	*/
	function move()
	{
			$this->filter->sanitize_post();
		$this->file->dir = $this->input->post("id");
		$result = $this->file->move($this->input->post("id"),$this->input->post("des"));
		
		if ($result) 
		{
			$this->auditory->audit(13,$this->session->userdata('iduser'),$this->input->post("id"),$this->input->post("des") );

		}
		else
		{
			$this->auditory->audit(13,$this->session->userdata('iduser'),$this->input->post("id"),$this->input->post("des"),"Move FAILED",0 );
		}
			echo $result;
	}
	
	/**
	*  rename
	*  function that renames file/dir
	*/
	function rename()
	{
			$this->filter->sanitize_post();
		$result = $this->file->rename($this->input->post("id"),$this->input->post("new"));
		if ($result==1) 
		{
				
				$this->auditory->audit(6,$this->session->userdata('iduser'),$this->input->post("id"),"","Rename to: " . $this->input->post("new"));		
		}
		else
		{
				$this->auditory->audit(6,$this->session->userdata('iduser'),$this->input->post("id"),"","Rename to: " . $this->input->post("new")." FAILED",0);		
		}
		echo $result;
	}
	
	/**
	* 
	*  function
	*/
	function tags_desc()
	{
		$this->filter->sanitize_post();
				$this->load->model('tags');
				$this->file->file = $this->input->post("id");
				$resulttag = $this->tags->setTags($this->input->post("tags"),$this->input->post("id"));
				$result = $this->file->setDescription($this->input->post("desc"));

				if ($resulttag=="1" && $result=="1") 
				{	
					$this->auditory->audit(7,$this->session->userdata('iduser'),$this->input->post("id"),"","Change desc and tags to: " . $this->input->post("tags")." Desc:".$this->input->post("desc"));		
				}
				else
				{
					$this->auditory->audit(7,$this->session->userdata('iduser'),$this->input->post("id"),"","Change FAILED desc and tags to: " . $this->input->post("tags")." Desc:".$this->input->post("desc"),0);		
				}
				
				echo $result;
	}
	
	/**
	* 
	*  function
	*/
	function edit_new()
	{
		$this->index();
	}
	
	
	/**
	* save
	* function to save one new file
	*/
	function save ()
	{
		$operation = ($this->input->post("type")==3)?17:16;
		$this->load->model('tags');
		$this->config->load('upload');
		
		//$this->file->create_file ($dir,$file_name,$file_text,$desc,$tags);
			//data: "id="+id+"&name="+filename+"&text="+filetext+"&desc="+filedesc+"&tags="+filetags,
	  	$result = $this->file->create_file($this->input->post("id"),$this->input->post("name"),$this->input->post("text"),$this->input->post("desc"),$this->input->post("tags"),$this->input->post("type"));
		if ($result != "-1")
		{
			$ok = $this->tags->setTags($this->input->post("tags"),$result);
			//echo '{"success":"true","file":"'.$result.'"}';
			$this->auditory->audit($operation,$this->session->userdata('iduser'),$this->input->post("id"),"","Created file name: " .$this->input->post("name"));		
			echo $result;
		}
		else
		{
			echo -1;
			$this->auditory->audit($operation,$this->session->userdata('iduser'),$this->input->post("id"),"","Created file name: " .$this->input->post("name") . " FAILED",0);		
		}

		

	}
	
	
	/**
	* edit_file
	* edits online text
	* loads data for file: name,text, tags, description
	*/
	function edit_file()
	{

		$this->load->model('tags');
		$this->load->model('display');

		$result = $this->file->fileData($this->input->post("id"),WRITE_PERMISSION);

		if ($result != "-1" && $result->num_rows())
		{
			$filedata = $result->row_array();
		 
				// If edited file, get content
				if ($filedata["type"] == 2 || $filedata["type"]==3) 
				{
					$c = $this->db->query("select * from file_content where idfile=".$this->input->post("id") ." order by date desc limit 0,1");
					$content = $c->row_array();
					$filedata["content"] = base64_decode($content["content"]);
					$this->auditory->audit(3,$this->session->userdata('iduser'),$this->input->post("id"),$filedata["type"],"Load file name: " .$filedata["name"]);		
				}
				else
				{
					$this->auditory->audit(3,$this->session->userdata('iduser'),$this->input->post("id"),$filedata["type"],"Load file name: " .$filedata["name"] . " FAILED",0);		
				}
				echo $this->display->editFile($filedata);
		}
		else
		{
			echo "-1";
		}
	}	
	
	
	/**
	* update_file
	* update file: name,text, tags, description
	*/
	function update_file()
	{

		$this->load->model('tags');
		$this->config->load('upload');
		
//				print_r($_POST);
		//$this->file->create_file ($dir,$file_name,$file_text,$desc,$tags);
			//data: "id="+id+"&name="+filename+"&text="+filetext+"&desc="+filedesc+"&tags="+filetags,
  		
		if ($result = $this->file->update_file($this->input->post("idfile"),$this->input->post("id"),$this->input->post("name"),$this->input->post("text"),$this->input->post("desc"),$this->input->post("tags"),$this->input->post("type")))
		{
			$ok = $this->tags->setTags($this->input->post("tags"),$result);
			//echo '{"success":"true","file":"'.$result.'"}';
			echo $result;
				$this->auditory->audit(18,$this->session->userdata('iduser'),$this->input->post("idfile"),$this->input->post("type"),"Updating file name: " .$this->input->post("name"));		
		}
		else
		{
				$this->auditory->audit(18,$this->session->userdata('iduser'),$this->input->post("idfile"),$this->input->post("type"),"Updating file name: " .$this->input->post("name"). " FAILED",0);		
			echo "0";
		}

		

	}	
	
	/**
	* open_file
	* Open file and show content
	*/
	function open_file ($f)
	{
		//$this->load->library('output');
		$result = $this->file->fileData($f);

		$filedata = $result->row_array(); 
		switch ($filedata["type"])
		{
			case 3 :	$new_url = $this->file->file_content($f);
						redirect($new_url);
						break;
			default: $this->file->load_edited_file($f);
						break;
		}
	}
	
	
	/**
	* direct_open_file
	* Get HTML to open file in a dialog
	*/
	function direct_open_file ()
	{
			$this->load->model('display');
					$this->load->model('tags');
		echo $this->display->file_content($this->input->post("id"));
	
	}
	
	/**
	* open_file
	* Open file and show content
	*/
	function vote()
	{
		$this->load->model('display');
		$result = $this->file->vote($this->input->post("id"),$this->input->post("vote"));
		echo  $this->display->get_rating($this->input->post("id"));
		//echo "ok : " . $this->input->post("id") . ":";		
	}
	
	
	/**
	* details
	* Open file and get details
	*/
	function details ()
	{
			$this->load->model('display');
			$this->load->model('tags');
			$this->load->model('entity_model');
					$this->load->config('4vientos');
		$this->load->config('form_validation');
		$this->load->library('pagination');
			$this->load->model('auditory_model');
			echo $this->display->file_details($this->input->post("id"));
	}
	
	
	/**
	* get_status
	* Open file and get status form
	*/
	function get_status ($multiple=0)
	{
			$this->load->model('display');
			echo $this->display->get_status($this->input->post("id"),$multiple);
	}
	
	
		/**
	* change_status
	* Open file and get status form
	*/
	function change_status ()
	{
			$this->load->model('file');
			echo $this->file->change_status($this->input->post("id"),$this->input->post("status"),$this->input->post("recursive"));
	}
	
		/**
	* get_owners
	* Open file and get owners form
	*/
	function get_owners ($multiple=0)
	{
			$this->load->model('display');
			echo $this->display->get_owners($this->input->post("id"),$multiple);
	}
	
	
		/**
	* change_owners
	* Change file owners
	*/
	function change_owners ()
	{
			$this->load->model('file');
			echo $this->file->change_owners($this->input->post("id"),$this->input->post("fileuserowner"),$this->input->post("filegroupowner"),$this->input->post("recursive"));
	}
	
		/**
	* get_status
	* Open file and get status form
	*/
	function get_permissions_form ($multiple=0)
	{
			$this->load->model('display');
			echo $this->display->get_permissions_form($this->input->post("id"),$multiple);
	}
	
	
		/**
	* change_status
	* Open file and get status form
	*/
	function change_permissions ()
	{
		$this->output->enable_profiler(TRUE);
			$this->load->model('file');
			echo $this->file->change_permissions($this->input->post("id"),$this->input->post("user_r"),$this->input->post("user_w"),$this->input->post("user_a"),$this->input->post("group_r"),$this->input->post("group_w"),$this->input->post("group_a"),$this->input->post("world_r"),$this->input->post("world_w"),$this->input->post("world_a"),$this->input->post("recursive"));
	}
	
	/**
	* check_permissions
	* Check for permissions
	*/
	function check_permissions ()
	{
		$result = $this->permissions->has_permission($this->input->post("id"),$this->input->post("name"));
		if (!$result)
		{
			echo "{'Result'='Error'}";
		}
		else
		{	
			echo "{'Result'='Ok'}";
		}
	}
	
		/**
		* check_duplicated
		* Check duplicated file/dir name on same dir
		*/
	function check_duplicated ()
	{
		//echo $this->input->post("id").":".$this->input->post("name").":".$this->input->post("update")."<br />";
		$result = $this->file->check_duplicated($this->input->post("id"),$this->input->post("name"),$this->input->post("update"));
		if (!$result)
		{
			echo "0";
		}
		else
		{	
			echo "1";
		}
	}
	
}
?>
