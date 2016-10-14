<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Display, contains all logic for interface generation
*/

class Display extends CI_Model {

	public $is_a_search = FALSE;
	public $order_criteria;
	public $order_by_url = "";
	public $current_dir;
	private $admin = FALSE;
	private $file_type_action = array();
	private $image_formats = array("jpg","jpeg","gif","bmp","svg");

	
	function __construct ()
	{
		parent::__construct();
		if ($this->permissions->is_admin()) { $this->admin = TRUE; } 
		$this->order_criteria = array("name desc"=>$this->lang->line("name-z-a"),
												"name asc"=>$this->lang->line("name-a-z"),
												"created desc"=>$this->lang->line("date-recent"),
												"created asc"=>$this->lang->line("date-old"),
												"size desc"=>$this->lang->line("size-desc"),
												"size asc"=>$this->lang->line("size-asc"),
												"who desc"=>$this->lang->line("user-desc"),
												"who asc"=>$this->lang->line("user-asc"),
												"type desc"=>$this->lang->line("type-desc") ,
												"type asc"=>$this->lang->line("type-asc"));
	}
	
	/**
	* menu
	* menu displayed in normal dir view
	*/
	public function menu ()
	{
		$xhtml = "<div id='filedataheader'>";
		$xhtml .= "<div id='dataheadername'>Name</div>";
		$xhtml .= "<div align='right' class='file_check'><input type='checkbox' id='file_checkbox_all' class='' value='1' /></div>";
		$xhtml .= "<div id='dataheaderprop'>Properties</div>";
		$xhtml .= $this->order_by();		
		$xhtml .= "</div>";
		return $xhtml;
	}
	

	/**
	* menu
	* menu displayed in normal dir view
	*/
	public function file_utilities ($dir)
	{
		$this->order_by_url = "/dir/view_ordered/".$dir;
		$xhtml = "<div id='filemenu'>";
//		$xhtml .= "<div id='fileupload'>";
//		$xhtml .= "</div>";
		$xhtml .= "<ul class='dirutilities'>";
		$xhtml .= "<li id='fileupload'><a href='".$dir."' class='createfile' title='".$this->lang->line("upload_file")."'>".$this->lang->line("upload")."</a></li>";
		$xhtml .= "<li class='ddmenu'>".$this->lang->line("create")." + ";
		$xhtml .= "<ul>";
		$xhtml .= "<li><a href='".$dir."' class='createdirlink' title='".$this->lang->line("make_dir")."'>".$this->lang->line("dir")."</a></li>";
		$xhtml .= "<li><a href='".$dir."' class='createfile' title='".$this->lang->line("create_file")."'>".$this->lang->line("file")."</a></li>";
		$xhtml .= "<li><a href='".$dir."' class='createlink' title='".$this->lang->line("create_link")."'>".$this->lang->line("link")."</a></li>";
		$xhtml .= "</ul></li>";
		$xhtml .= "<li class='ddmenu menuoculto'>".$this->lang->line("more")."...";
		$xhtml .= "<ul>";		
		$xhtml .= "<li class='menuoculto'><a href='".$dir."' class='movedirlink' title='".$this->lang->line("move_selected")."'>".$this->lang->line("move")."</a></li>";
		$xhtml .= "<li class='menuoculto'><a href='".$dir."' class='deletedirlink' title='".$this->lang->line("delete_selected")."'>".$this->lang->line("delete")."</a></li>";
		if ($this->admin)
		{
			$xhtml .= "<li class='menuoculto'><a href='".$dir."' class='statuslink'>".$this->lang->line("status")."</a></li>";
			$xhtml .= "<li class='menuoculto'><a href='".$dir."' class='permissionslink'>".$this->lang->line("permissions")."</a></li>";
			$xhtml .= "<li class='menuoculto'><a href='".$dir."' class='ownerslink'>".$this->lang->line("owners")."</a></li>";
		}
		$xhtml .= "</ul></li>";
		$xhtml .= "</ul>";
		//$xhtml .= "<li><a href='".$dir."' class='selectalldirlink' title='".$this->lang->line("select_all")."'>".$this->lang->line("select_all")."</a></li>";
		// ".$this->order_by()
		
		$xhtml .= "<div id='createdir' class='dialog'>";
   	$xhtml .= "<input type='text' name='newdir_txt' id='newdir_".$dir."' />";
   	$xhtml .= "<input type='button' name='newdir_button' class='newdir_button' id='newdir_button_".$dir."' value='".$this->lang->line("make_dir")."'>";
		$xhtml .= "</div>";

		$xhtml .= "</div>";
		return $xhtml;
	}	
	
	/**
	* menu_search
	* reduced menu displayed when searching
	*/
	public function menu_search ()
	{
		$this->order_by_url = "/search/keep_searching/";
		$xhtml = "<div id='filemenu'>";
		$xhtml .= "<ul class='dirutilities'><li><a href='' class='movedirlink' title='".$this->lang->line("move_selected")."'>".$this->lang->line("move")."</a></li>";
		$xhtml .= "<li><a href='' title='".$this->lang->line("delete_selected")."'>".$this->lang->line("delete")."</a></li>";
		$xhtml .= "<li><a href='' class='selectalldirlink' title='".$this->lang->line("select_all")."'>".$this->lang->line("select_all")."</a></li>";
		$xhtml .= "<li>".$this->order_by(1)."</li></ul>";
		$xhtml .= "</div>";
		echo $xhtml;
	}
	
	public function dirData ($data)
	{

		$this->file->filepath = array();
		if ($data->num_rows())
		{
			$row = $data->row(); 
			echo "<div id='fileheader'>";
			echo "<div id='file_name'><a href='".site_url()."/dir/view/".$row->id."' title='".$row->name."'><img src='".base_url()."resource/images/opendir.png' border='0' align='middle' />&nbsp;". $row->name."</a>";
			echo "<span id='total_files'>Total: ". $data->num_rows()." elementos</span></div>";
			//echo ($row->fatherid)?"<span><a href='".site_url()."/dir/view/".$row->fatherid."/". url_title($row->fname,"dash",TRUE)."' >"."..".$this->lang->line('back')."</a></div>":"";
			echo $this->file_utilities($row->id);
			echo "</div>";
		}
		else
		{
			echo "<div id='fileheader'>Directorio: / ".$this->file_utilities($row->id)."</div>";
		}


	}
	
	public function breadcrumb ($data) {
		$xhtml = "";
		$this->file->filepath = array();
		if ($data->num_rows())
		{
			$row = $data->row(); 
			$this->file->reverse_crawl($row->id);
			$xhtml .= " · <a href='".site_url()."/dir/' >".$this->lang->line('root')."</a> ";
			foreach ($this->file->filepath as $p)
			{
				if ($p[0]==1) continue;
				$xhtml .=  " &gt; <a href='".site_url()."/dir/view/".$p[0]."/". url_title($p[1],"dash",TRUE)."' >".$p[1]."</a> ";
			}
			//echo ($row->fatherid)?"<div><a href='".site_url()."/dir/view/".$row->fatherid."/". url_title($row->fname,"dash",TRUE)."' >"."..".$this->lang->line('back')."</a></div>":"";
		}
		
		return $xhtml;
	}
	
	/*
	* muestra el listado
	*/
	public function dirList($data)
	{

		echo "<div id='files'>";
		echo "<div id='newdirplaceholder'></div>";
		if ($data->num_rows())
		{

			foreach ($data->result_array() as $row)
			{
				echo $this->showFile($row);
			}
		}

		echo "</div>";
	}
	

	/*
	* muestra el listado, redux mode for Dialog
	*/
	public function dirListRedux($data)
	{
		//print_r($data);
		
		if ($data == "-1")
		{
			echo $this->lang->line('denied');
		}
		else
		{
			echo "<ul class='dirlist'>";
			if ($data->num_rows())
			{
			
				foreach ($data->result_array() as $r)
				{
					if (!$r["type"])
					{
						echo "<a href='".$r["id"]."' class='dirlistselect'><li id='dirlist_".$r["id"]."'>".$r["name"]."</li></a>";
					}
				}
			}

			echo "</ul>";
		}
	}	
	
	/*
	* showFile
	* muestra un fichero
	*/
	public function showFile ($r)
	{	
			// default file class
			$class = $this->get_class($r["type"],$r["ext"]);
			$file_images = array("","","",".png");
			
			$xhtml = "";
			$xhtml .= "<div class='file status_".$r["status"]."' id='file_".$r["id"]."'>\n";
				if ($this->is_a_search) // show context
				{	
					$xhtml .= "En: ";
					$this->file->reverse_crawl($r["id"]);
					foreach ($this->file->filepath as $p)
						$xhtml .= " &gt; <a href='".site_url()."/dir/view/".$p[0]."/". url_title($p[1],"dash",TRUE)."' >".$p[1]."</a> ";
				}
				
				$xhtml .= "<div align='right' class='file_check'><input type='checkbox' id='file_checkbox_".$r["id"]."' class='file_checkbox' value='".$r["id"]."' /></div>";
				$xhtml .= "<div class='filedata'><div class='filedate' id='filedate_".$r["id"]."'>".$this->fdate($r["created"])." - by <span id='filecreator_".$r["id"]."'>".$r['creator']."</span><br />";
				$xhtml .= "<span id='filepermissions_".$r["id"]."'>".$this->get_permissions($r)."</span><div class='filesize'>".$this->fsize($r["size"],$r["type"])."</div></div>";
				$xhtml .= $this->get_rating($r["id"])."\n"."</div>\n";
			$xhtml .= "<div>";
			if (!$r["type"]) // IS A DIR
			{
				$xhtml .= "<div class='fileimage'><a href='".site_url()."/dir/view/".$r["id"]."/". url_title($r["name"],"dash",TRUE)."' id='file-".$r["id"]."' class='fileget'><img src='".base_url()."resource/images/directory.png' border='0' align='middle' /></a></div>";
				$xhtml .= "<span id='filename_".$r["id"]."' class='dirname'><strong><a href='".site_url()."/dir/view/".$r["id"]."/". url_title($r["name"],"dash",TRUE)."'  id='filen-".$r["id"]."' class='fileget' >".$r["name"]."</a></strong></span>";
				
			}
			else // IS A FILE
			{
				//$path = $this->path($r["id"]);
				$xhtml .= "<div class='fileimage'><a href='".site_url()."/operation/open_file/".$r["id"]."' class='".$class."'  id='ffilen-".$r["id"]."' ><img src='".base_url()."resource/images/".$this->get_image($r["type"],$r["ext"])."' border='0' align='middle'  id='file-".$r["id"]."' class='".$class."' /></a></div>";
				$xhtml .= "<span id='filename_".$r["id"]."' class='filename'><a href='".site_url()."/operation/open_file/".$r["id"]."'  id='filen-".$r["id"]."' class='".$class."' >".$r["name"]."</a></span>";
				//echo $r["name"]; 
			}
				
				$xhtml .= $this->description_and_tags($r["id"],$r["description"],$this->getTags($r["id"]));
				$xhtml .= "</div>";
				$xhtml .= $this->editForm($r["id"]);

				$xhtml .= $this->utilities($r["id"],$r["type"]);
				$xhtml .= "</div>\n";
				
				return $xhtml;
			
	}
	
	/*
	* showFileData
	* muestra un fichero, pero solo la información
	*/
	public function showFileData ($r)
	{	
			// default file class
			$class = $this->get_class($r["type"],$r["ext"]);
			
			$xhtml = "";
			$xhtml .= "<div class='status_".$r["status"]."' id='file_".$r["id"]."'>\n";
			
			$this->file->reverse_crawl($r["id"]);
				foreach ($this->file->filepath as $p)
						$xhtml .= " &gt; <a href='".site_url()."/dir/view/".$p[0]."/". url_title($p[1],"dash",TRUE)."' >".$p[1]."</a> ";
			
			$xhtml .= "</div>";
			
			if (!$r["type"]) // IS A DIR
			{
				$xhtml .= "<div class='fileimage'><a href='".site_url()."/dir/view/".$r["id"]."/". url_title($r["name"],"dash",TRUE)."' id='file-".$r["id"]."' class='fileget'><img src='".base_url()."resource/images/directory.png' border='0' align='middle' /></a></div>";
				$xhtml .= "<span id='filename_".$r["id"]."' class='dirname' style='font-size:2em;'><strong>".$r["name"]."</strong></span>";
				
			}
			else // IS A FILE
			{
				//$path = $this->path($r["id"]);
				$xhtml .= "<div class='fileimage'><a href='".site_url()."/operation/open_file/".$r["id"]."' class='".$class."'  id='ffilen-".$r["id"]."' ><img src='".base_url()."resource/images/".$this->get_image($r["type"],$r["ext"])."' border='0' align='middle'  id='file-".$r["id"]."' class='".$class."' /></a></div>";
				$xhtml .= "<span id='filename_".$r["id"]."'><a href='".site_url()."/operation/open_file/".$r["id"]."'  id='filen-".$r["id"]."' class='".$class."'  style='font-size:2em;'>".$r["name"]."</a></span>";
			}
				$xhtml .= "<br /><div class='filedetails'>";
				$xhtml .= "<div><span>".$this->lang->line('size').":</span><br />".$this->fsize($r["size"],$r["type"])."</div>";
				$xhtml .= "<div><span>".$this->lang->line('creation-date').":</span><br />".$this->fdate($r["created"])."</div>";
				$xhtml .= "<div><span>".$this->lang->line('update-date').":</span><br />".(($r["updated"])?$this->fdate($r["updated"]):$this->fdate($r["created"]))."</div>";
				$xhtml .= "<div><span>".$this->lang->line('creator').":</span><br />".$r['creator']."</div>";
				$xhtml .= "<div><span>".$this->lang->line('extension').":</span><br />".$r['ext']."</div>";
				$xhtml .= "<div><span>".$this->lang->line('permissions').":</span><br />".$this->get_permissions($r)."</div>";
				$xhtml .= "<div><span>".$this->lang->line('rating').":</span><br />".$this->get_rating($r["id"])."</div>";
				$xhtml .= "<div><span>".$this->lang->line('description').":</span><br />&nbsp;".$r["description"]."</div>";
				$xhtml .= "<div><span>".$this->lang->line('tags').":</span><br />&nbsp;".$this->getTags($r["id"])."</div>";
				$xhtml .= "<div><span>MD5:</span><br />&nbsp;".$r["md5"]."</div>";
				$xhtml .= "</div>";
				


				$xhtml .= "</div>";
				
				return $xhtml;
			
	}
	

	/*
	* showFileData
	* muestra un fichero, pero solo la información
	*/
	public function showFileDataRedux ($r)
	{	
			// default file class
			$class = $this->get_class($r["type"],$r["ext"]);
			
			$xhtml = "";


				//$path = $this->path($r["id"]);
				$xhtml .= "<div class='fileimage'><a href='".site_url()."/operation/open_file/".$r["id"]."' class='".$class."'  id='ffilen-".$r["id"]."' ><img src='".base_url()."resource/images/".$this->get_image($r["type"],$r["ext"])."' border='0' align='middle'  id='file-".$r["id"]."' class='".$class."' /></a></div>";
				$xhtml .= "<span id='filename_".$r["id"]."' style='font-size:2em;'>".$r["name"]."</span>";
			
				$xhtml .= "<div style='clear: both;'>&nbsp;</div><div class='filedetails'>";
				$xhtml .= "<div><span>".$this->lang->line('rating').":</span>".$this->get_rating($r["id"])."</div>";
				$xhtml .= "<div><span>".$this->lang->line('description').":</span> &nbsp;".$r["description"]."</div>";
				$xhtml .= "<div><span>".$this->lang->line('tags').":</span> &nbsp;".$this->getTags($r["id"])."</div>";
				$xhtml .= "</div>";
				


				$xhtml .= "</div>";
				
				return $xhtml;
			
	}
		
	
	/*
	* show_file_redux
	* muestra un fichero
	*/
	public function show_file_redux ($r)
	{
			// default file class
			$class = $this->get_class($r["type"],$r["ext"]);

			
			$xhtml = "<div class='fileredux'>\n";
	
			if (!$r["type"]) // IS A DIR
			{
				$xhtml .= "<a href='".site_url()."/dir/view/".$r["id"]."/". url_title($r["name"],"dash",TRUE)."' id='file-".$r["id"]."' class='fileget'><img src='".base_url()."resource/images/gnome-fs-directory.png' border='0' align='middle' /></a>";
				$xhtml .= "<span id='ffilename_".$r["id"]."' class='dirname'><strong><a href='".site_url()."/dir/view/".$r["id"]."/". url_title($r["name"],"dash",TRUE)."'  id='ffilen-".$r["id"]."' class='fileget' >".$r["name"]."</a></strong></span>";
				
			}
			else // IS A FILE
			{
				$path = $this->path($r["id"]);
				$xhtml .= "<a href='".site_url()."/operation/open_file/".$path."' class='fileget'  id='ffilen-".$r["id"]."' ><img src='".base_url()."resource/images/".$this->get_image($r["type"],$r["ext"],"-fs")."' border='0' align='middle'  id='file-".$r["id"]."' class='fileget' /></a>";
				$xhtml .= "<span id='ffilename_".$r["id"]."'><a href='".site_url()."/operation/open_file/".$r["id"]."'  id='ffilen-".$r["id"]."' class='fileget' >".$r["name"]."</a></span>";
				//echo $r["name"]; 
			}
				
			$xhtml .= "<span><br />&nbsp;&nbsp;". $r["creator"]."</span>";
			if (isset($r["total"])) { $xhtml .= ", " . $r["total"];}
			$xhtml .="</div>\n";
				
				return $xhtml;
			
	}
	
	
	/**
	* utilities
	* links for file operation
	*/
	private function utilities ($id,$type) {
		$xhtml = "";
	
			$xhtml .= "<div class='fileutilities'>";			
			$xhtml  .= "<ul>";
			if ($type==2 || $type==3)
			{
				$xhtml  .= "<li><a href='".$id."_".$type."' class='filedit'  title='".$this->lang->line("edit")."'>".$this->lang->line("edit")."</a></li>";
			}
			/*else
			{
				$xhtml  .= "<li><a href='".site_url()."/operation/open_file/".$id."' class='filedownload' title='".$this->lang->line("download")."'>".$this->lang->line("download")."</a></li>";
			}*/
			$xhtml  .= "<li><a href='".$id."' class='filerename' title='".$this->lang->line("rename")."'>".$this->lang->line("rename")."</a></li>";
			$xhtml  .= "<li><a href='".$id."' class='filedetails' title='".$this->lang->line("details")."'>".$this->lang->line("details")."</a></li>";
			//$xhtml  .= "<li><a href='".$id."' class='filedirectaccess' title='".$this->lang->line("create-link")."'>".$this->lang->line("create-link")."</a></li>";
			$xhtml  .= "<li><a href='".$id."' class='filemove' title='".$this->lang->line("move")."'>".$this->lang->line("move")."</a></li>";
			$xhtml  .= "<li><a href='".$id."' class='filedelete' title='".$this->lang->line("delete")."'>".$this->lang->line("delete")."</a></li>";
			
			if ($this->admin) 
			{
				$xhtml  .= "<li><a href='".$id."' class='filepermissions' title='".$this->lang->line("change_permissions")."'>".$this->lang->line("permissions")."</a></li>";
				$xhtml  .= "<li><a href='".$id."' class='fileowners' title='".$this->lang->line("change_owners")."'>".$this->lang->line("owners")."</a></li>";
				$xhtml  .= "<li><a href='".$id."' class='filestatus' title='".$this->lang->line("change_status")."'>".$this->lang->line("status")."</a></li>";			
			} 
			$xhtml  .= "</ul>";
			$xhtml .= "</div>";		
		return $xhtml;
	}
	
	/**
	* description
	* get file description html
	*/
		private function description_and_tags ($id,$desc,$tags) {
		$xhtml = "";
	
			$xhtml .= "<div class='filedescription' id='file_".$id."_desc_div'>";
			//$xhtml .= "<a href='".$id."' class='editor'>".$this->lang->line("tags")."</a> ";		
			$xhtml .= "<a href='".$id."' class='editor'><img src='".base_url()."resource/images/tag.png' border='0' alt='".$this->lang->line("tags")."' title='".$this->lang->line("tags")."' /></a> ";		
			$xhtml .= "<span>".$tags."</span>";
			$xhtml .= "<br /><span id='file_".$id."_desc'>".$desc."</span>";
			$xhtml .= "</div>";		
		return $xhtml;
	}
	
	
	/**
	* get_permissions
	* Get user permissions code
	*/
	private function get_permissions($r)
	{
		$xhtml = "";

		$xhtml .= ($r["user_r"])?"r":"-";
		$xhtml .= ($r["user_w"])?"w":"-";
		$xhtml .= ($r["user_a"])?"a":"-";

		$xhtml .= ($r["group_r"])?"r":"-";
		$xhtml .= ($r["group_w"])?"w":"-";
		$xhtml .= ($r["group_a"])?"a":"-";

		$xhtml .= ($r["world_r"])?"r":"-";
		$xhtml .= ($r["world_w"])?"w":"-";
		$xhtml .= ($r["world_a"])?"a":"-";
		
		return $xhtml;
	}


	/**
	* get_rating
	* Get file rating
	*/
	public function get_rating($file)
	{
		$sql = "select avg( vote ) as voteavg, count( id ) as votetotal from file_vote where idfile =$file ";
		
		$vote_result = $this->db->query($sql);
		$file_votes = $vote_result->row_array();

		
		// set avg value, preventing 0 value
		$avg = (!$file_votes["votetotal"])?0:$file_votes["voteavg"];		
		
		$xhtml = "<div class='votingpanel' id='votingpaneln_".$file."'>";
		$class= "full";
		
		for ($i=1;$i<6;$i++)
		{
			$class = ($avg>= $i)?"full":((round($avg) == $i)?"mid":"");
			$xhtml .= "<a href='#' id='votestar_".$i."_".$file."' class='".$class."'></a>";

		}			
		
		$xhtml .= "<span class='votinginfo'>".sprintf(_("%d of %d"),$avg,$file_votes["votetotal"])."</span>\n";
		$xhtml .= "</div>";
		

		return $xhtml;
	}

	/**
	* getTags
	* get tags for file
	*/
	private function getTags ($id) {
		$xhtml = "<span class='filetags' id='file_".$id."_tags_div'>";
		
		// Cargamos tags ASÍ en el caso de que display fuera una librería
		/*$CI =& get_instance();
		$CI->load->model('tags');
		$CI->tags->halo();

		exit;*/
		$result = $this->tags->getTags($id);
		//$xhtml .= ($result->num_rows())?$this->lang->line("tags").": ":"";
		if (count($result->num_rows()))
		{
			foreach ($result->result_array() as $t)
			{
				$xhtml .= "<span class='file_".$id."_tag'><a href='".site_url()."/search/by_tag/".$t["tag"]."'>".$t["tag"]."</a></span>".", ";
			}
			$xhtml = rtrim($xhtml,", ");
		}
		$xhtml .= "</span>";
		return $xhtml;
	}
	
	/**
	* order_by
	* order_by list select
	*/
	public function order_by ($is_search=0) {

		$xhtml = "\n<form name='form_order_by' method='post' action='".site_url().$this->order_by_url."' >\n";
		if ($is_search)
				$xhtml .= "<input type='hidden' name='search' id='searchh'  value='search' />\n";
		$xhtml .= "<select name='order_by' id='order_by' onchange='document.form_order_by.submit()'>";
		$xhtml .= "<option value='0' selected='selected'>".$this->lang->line("order-by")."</option>\n";
			
		foreach ($this->order_criteria as $k => $c )
		{
			$selected = ($this->session->userdata('order_criteria')==$k)?"selected='selected'":"";
			$xhtml .= "<option value='$k' $selected>$c</option>\n";
		}
		$xhtml .= "</select>\n</form>\n";
		return $xhtml;
	}

	/**
	* get_class
	* get class for file depending on extension
	*/
	public function get_class ($type,$extension) 
	{

		// If edited file
		if ($type == 2 || ($type==1 && in_array($extension,$this->image_formats)))
		{
			return "filedirectopen";
		}
		else
		{
			return "fileget";
		}
	}
	
	/**
	* get_image
	* get image for file depending on extension
	*/
	public function get_image ($type,$extension,$redux="") 
	{

		// If edited file
		if ($type == 2)
		{
			return "edit".$redux.".png";	
		} 
		// If link
		elseif ($type == 3)
		{
			return "link".$redux.".png";	
		} 
		elseif ($type==1 && in_array($extension,$this->image_formats))
		{
			return "img".$redux.".png";
		}
		else
		{
			return "regularfile".$redux.".png";
		}
	}
		
	private function editForm ($id) {
		$xhtml = "<div class='fileedit' id='fileedit_".$id."'>";
		//$xhtml .= "<input type='button' name='fileeditbutton'  class='filebutton' id='file_button_".$id."' value='Guardarr' />";

		$xhtml .= "</div>";
		return $xhtml;
	}


	public function editFile ($data)
	{
	
			$edittags = "";
			$result = $this->tags->getTags($data["id"]);
			if (count($result->num_rows()))
			{
				foreach ($result->result_array() as $t)
					$edittags .= $t["tag"].", ";
			}
			
		$xhtml = "";
		$xhtml .= "<label for='new_file_name'>Nombre</label><br />";
		$xhtml .= "<input type='hidden' id='new_file_idfile' name='new_file_idfile' value='".$data["id"]."' size='40' />";
		$xhtml .= "<input type='text' id='new_file_name' name='new_file_name' value='".$data["name"]."' size='40' /><br />";
		$xhtml .= "<label for='new_file_text' id='label_new_file_text'>Texto</label><br />";
		$xhtml .= "<textarea id='new_file_text' name='new_file_text' cols='40'>".$data["content"]."</textarea><br />";
		$xhtml .= "<label for='new_file_desc'>Descripción</label><br />";
		$xhtml .= "<input type='text' id='new_file_desc' name='new_file_name' value='".$data["description"]."' size='50' /><br />";
		$xhtml .= "<label for='new_file_tags'>Tags</label><br />";
		// <a href='' title='".$this->lang->line("see-tags")."' id='viewtags'>".$this->lang->line("see")."</a>
		$xhtml .= "<input type='text' id='new_file_input_tags' name='new_file_input_tags' value='".$edittags."' size='30' /><br />";
		
		return $xhtml;
	}

	private function path ($id)
	{
		return (($id%26)."/".($id%1024)."/".$id);
	}
	
	/**
	* fdate
	* Formats date with CI functions, unix_timestamp as parameter
	*/
	private function fdate ($ut)
	{
		$format = '%a, %e %B %Y,%H:%m';
		return strftime($format, $ut);
	}
	
	/**
	* fsize
	* formats file size data
	*/
	private function fsize ($size_in_bytes,$type)
	{
		if (!$type)
		{
			return "";
		}
		// depending on size, use Bytes, KBytes or  MBytes
		elseif ($size_in_bytes < 1024)
		{
			return $size_in_bytes ." Bytes";
		}  
		elseif ($size_in_bytes >= 1024 && $size_in_bytes < 1048576)
		{
			return round(($size_in_bytes/1024),1) . " KBytes";
		}
		else
		{
			return round(($size_in_bytes/1048576),1) . " MBytes";
		}
	}
	/**
	* last_created
	*
	*/
	public function last_created () {
		$xhtml = "<div class='title'>".$this->lang->line("newly-created")."</div>";

		$data = $this->file->last_created();
		
		if ($data->num_rows())
		{

			foreach ($data->result_array() as $row)
			{
				$xhtml .= $this->show_file_redux($row);
			}
		}
		
		$xhtml .= "";
		return $xhtml;
	}
	
	/**
	* most_popular
	*
	*/
	public function most_popular () {
		$xhtml = "<div class='title'>".$this->lang->line("most-popular")."</div>";

		$data = $this->file->most_popular();
		
		if ($data->num_rows())
		{

			foreach ($data->result_array() as $row)
			{
				$xhtml .= $this->show_file_redux($row);
			}
		}
		
		$xhtml .= "";
		return $xhtml;
	}
	
	
	/**
	* most_voted
	*
	*/
	public function most_voted () {
		$xhtml = "<div class='title'>".$this->lang->line("most-voted")."</div>";

		$data = $this->file->most_voted();
		
		if ($data->num_rows())
		{

			foreach ($data->result_array() as $row)
			{
				$xhtml .= $this->show_file_redux($row);
			}
		}
		
		$xhtml .= "";
		return $xhtml;
	}
	

	/**
	* highlighted
	*
	*/
	public function highlighted () {
		$xhtml = "<div class='title'>".$this->lang->line("highlighted")."</div>";

		$data = $this->file->highlighted();
		
		if ($data->num_rows())
		{

			foreach ($data->result_array() as $row)
			{
				$xhtml .= $this->show_file_redux($row);
			}
		}
		
		$xhtml .= "";
		return $xhtml;
	}	
	
	/**
	* file_preview
	*
	*/
	public function file_preview ($file) {
		$xhtml = "";
		$fd = $this->file->fileData($file);
		$r = $fd->row_array();

		$xhtml .= $this->showFileData($r);
		//$xhtml .= "<h3>".$r["name"]."</h3>\n";
		//$xhtml .= "<p><i>".$r["description"]."</i></p>\n";
		
		return $xhtml;
	}
	
	/**
	* file_details
	*
	*/
	public function file_details ($file) {
		$xhtml = "";
		$fd = $this->file->fileData($file);
		$r = $fd->row_array();

		$xhtml .= $this->showFileData($r);
		
		if ($this->admin)
		{
			$xhtml .= "<h3>".$this->lang->line("file-auditory")."</h3>";
			$xhtml .= $this->auditory_model->table(0,"auditdate","desc",array($file));
		}
		//$xhtml .= "<h3>".$r["name"]."</h3>\n";
		//$xhtml .= "<p><i>".$r["description"]."</i></p>\n";
		
		return $xhtml;
	}
	

	/**
	* get_status
	* Get file status
	*/
	public function get_status($file,$multiple=0)
	{

		$xhtml = "";
		
		if ($file > -1)
		{
			// get file status
			$sql = "select * from file where id = ? ";
			$result = $this->db->query($sql, array($file));
			$currentfile = $result->row_array();
		}
		
		// get possible status
		$sql = "select * from file_status order by id";
		$result = $this->db->query($sql);
	
		$mul = ($multiple)?"_mul":"";
		
		$xhtml .= "<div style='color: black;'>";
		$xhtml .= "<input type='checkbox' id='recursivechange' name='recursivechange' value='1' />";
		$xhtml .= "<label for='recursivechange'>".$this->lang->line("recursive")."</label>";
		
		if ($file > -1)
			$xhtml .= "<h2>".$currentfile["name"]."</h2>";

		$xhtml .= "<ul>";
		
		foreach ($result->result_array() as $row)
			{
				$xhtml .= "<li><a href='".$file."_".$row["id"]."' class='filestatus_change".$mul."'>".$this->lang->line($row["status"]).":</a> ".$this->lang->line($row["description"])."</li>";
			}
		
		$xhtml .= "</ul>\n";
		$xhtml .= "</div>";
		

		return $xhtml;
	}	
	
	
	/**
	* get_owners
	* Get file owners
	*/
	public function get_owners($file,$multiple=0)
	{

		$xhtml = "";
		
		if ($file > -1)
		{
			// get file status
			$sql = "select * from file where id = ? ";
			$result = $this->db->query($sql, array($file));
			$currentfile = $result->row_array();
		}
		
	
		$mul = ($multiple)?"_mul":"";
		
		$xhtml .= "<div style='color: black;'>";
		$xhtml .= "<input type='hidden' id='filerecursivechange' name='filerecursivechange' value='".$file."' />";
		$xhtml .= "<input type='checkbox' id='recursivechange' name='recursivechange' value='1' />";
		$xhtml .= "<label for='recursivechange'>".$this->lang->line("recursive")."</label><br />";
		
		if ($file > -1)
			$xhtml .= "<h2>".$currentfile["name"]."</h2>";

		$xhtml .= "<label for='fileuserowner'>".$this->lang->line("user-owner")."</label><br />\n";
		$xhtml .= "<select name='fileuserowner' id='fileuserowner'>";
			// get possible users
			$sql = "select id,username from a3m_account order by username";
			$resultuser = $this->db->query($sql);
			foreach ($resultuser->result_array() as $row)
			{
				$selected = ($file>-1 && $currentfile["who"]==$row["id"])?" selected='selected' ":"";
				$xhtml .= "<option value='".$row["id"]."' ".$selected.">".$row["username"]."</option>\n";
			}
		$xhtml .= "</select><br /><br />";
	
		$xhtml .= "<label for='filegroupowner'>".$this->lang->line("group-owner")."</label><br />\n";
		$xhtml .= "<select name='filegroupowner' id='filegroupowner'>";
			// get possible groups
			$sql = "select id,name from a3m_acl_role order by name";
			$resultgroup = $this->db->query($sql);
			foreach ($resultgroup->result_array() as $row)
			{
				$selected = ($file>-1 && $currentfile["idgroup"]==$row["id"])?" selected='selected' ":"";
				$xhtml .= "<option value='".$row["id"]."' ".$selected.">".$row["name"]."</option>\n";
			}
		$xhtml .= "</select>";	
		
		$xhtml .= "</div>";
		

		return $xhtml;
	}	
	
	/**
	* get_permissions_form
	* Get file permissions_form
	*/
	public function get_permissions_form($file,$multiple=0)
	{

		$xhtml = "";
		$checked = array("","","","","","","","","");

		
		if ($file > -1)
		{
			// get file permissions
			$sql = "select * from file where id = ? ";
			$result = $this->db->query($sql, array($file));
			$currentfile = $result->row_array();
			
			$checked[0] = ($currentfile["user_r"])?"checked='checked'":"";
			$checked[1] = ($currentfile["user_w"])?"checked='checked'":"";
			$checked[2] = ($currentfile["user_a"])?"checked='checked'":"";
			$checked[3] = ($currentfile["group_r"])?"checked='checked'":"";
			$checked[4] = ($currentfile["group_w"])?"checked='checked'":"";
			$checked[5] = ($currentfile["group_a"])?"checked='checked'":"";
			$checked[6] = ($currentfile["world_r"])?"checked='checked'":"";
			$checked[7] = ($currentfile["world_w"])?"checked='checked'":"";
			$checked[8] = ($currentfile["world_a"])?"checked='checked'":"";
		}
		
	
		$mul = ($multiple)?"_mul":"";
		
		$xhtml .= "<div style='color: black;'><h3>".$this->lang->line("recursive")."</h3>";
		$xhtml .= "<input type='checkbox' id='recursivechange' name='recursivechange' value='1' />";
		$xhtml .= "<label for='recursivechange'>".$this->lang->line("recursive")."</label>";
		$xhtml .= "<input type='hidden' id='filerecursivechange' name='filerecursivechange' value='".$file."' />";
		
		
		if ($file > -1)
			$xhtml .= "<h2>".$currentfile["name"]."</h2>";
		$xhtml .= "<h4>".$this->lang->line("user-perm")."</h4>";
		$xhtml .= "<div><input type='checkbox' id='user_r' name='user_r' value='1' ".$checked[0]." /><label for='user_r'>".$this->lang->line("owner-read")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='user_w' name='user_w' value='1' ".$checked[1]." /><label for='user_w'>".$this->lang->line("owner-modify")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='user_a' name='user_a' value='1' ".$checked[2]." /><label for='user_a'>".$this->lang->line("owner-delete")."</label></div>\n";
		$xhtml .= "<h4>".$this->lang->line("group-perm")."</h4>";
		$xhtml .= "<div><input type='checkbox' id='group_r' name='group_r' value='1' ".$checked[3]." /><label for='group_r'>".$this->lang->line("group-read")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='group_w' name='group_w' value='1' ".$checked[4]." /><label for='group_w'>".$this->lang->line("group-modify")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='group_a' name='group_a' value='1' ".$checked[5]." /><label for='group_a'>".$this->lang->line("group-delete")."</label></div>\n";
		$xhtml .= "<h4>".$this->lang->line("world-perm")."</h4>";
		$xhtml .= "<div><input type='checkbox' id='world_r' name='world_r' value='1' ".$checked[6]." /><label for='world_r'>".$this->lang->line("any-read")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='world_w' name='world_w' value='1' ".$checked[7]." /><label for='world_w'>".$this->lang->line("any-modify")."</label></div>\n";
		$xhtml .= "<div><input type='checkbox' id='world_a' name='world_a' value='1' ".$checked[8]." /><label for='world_a'>".$this->lang->line("any-delete")."</label></div>\n";
		$xhtml .= "</div>";
		

		return $xhtml;
	}	
	
	
	/**
	* file_content
	* gets html to display file content directly on a dialog
	*/
	public function file_content ($f)
	{
		$xhtml = "";
		
		$fdata = $this->file->fileData($f);
		$filedata = $fdata->row_array();

		$xhtml .= "<div id='filecontent'>\n";
		$xhtml .= $this->showFileDataRedux($filedata);
		
		// If edited file
		if ($filedata["type"] == 2)
		{
			$xhtml .= $this->file->file_content($f);
		}
		
		elseif ($filedata["type"]==1 && in_array($filedata["ext"],$this->image_formats))
		{
			$desc = ($filedata["description"]!="")?$filedata["description"]:$filedata["name"];
			$xhtml .= "<img src='".site_url()."/operation/open_file/".$f."' alt='".$desc."' title='".$desc."' />";
		}

		$xhtml .= "</div>";
		
		return $xhtml;
	}
	
}

/* End of file display.php */
/* Location: ./application/models/display.php */