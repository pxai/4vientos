<?php
	$search = false;
	$page = 0;
	$limit = $this->config->item("pagination");
	
	$order = (!$this->session->userdata('order_criteria'))?'type asc':$this->session->userdata('order_criteria');
	
	$newpage = $this->uri->segment(4, 0);
	
	if ($newpage != "" && preg_match("/^[0-9]+$/",$newpage)) { $page = $newpage;}
	
	if ($this->input->post("search")=="")
	{
		$dir = ($this->uri->segment(3, 0)=="")?1:$this->uri->segment(3, 0);
		$dirdata = $this->file->dirData($dir);
	}
	elseif ($this->input->post("search")=="" && $this->input->post("order_by")!="")
	{
		$dir = ($this->uri->segment(3, 0)=="")?1:$this->uri->segment(3, 0);
		$dirdata = $this->file->dirData($dir);
	}
	else 
	{
		$search = true;
		$newpage = $this->uri->segment(3, 0);
		if ($newpage != "" && preg_match("/^[0-9]+$/",$newpage)) { $page = $newpage;}
	
		$dir = 1;
		$dirdata = $this->file->search($this->input->post("namesearch"),$this->input->post("tagssearch"),$page,$limit,$order);
	}
	
		$this->display->current_dir = $dir;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
	$this->load->view("meta");
?>

	<base href="<?=base_url()?>" />
  <link rel="stylesheet" href="resource/css/4vientos.css" type="text/css" />
  <link rel="stylesheet" href="resource/css/dinastyle.php" type="text/css" />
    	<link type="text/css" href="resource/css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="resource/js/jquery.js"></script>
    <script type="text/javascript" src="resource/js/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="resource/js/jquery.ui.js"></script>
    <script type="text/javascript" src="resource/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="resource/js/ckeditor/adapters/jquery.js"></script>
    <script type="text/javascript">
		SITE_URL = "<?=base_url()?>";
		WARNING = "<?=$this->lang->line('warning')?>";
		MSG_PERMISSION_DENIED = "<?=$this->lang->line('denied')?>";
		MSG_NOT_BLANK = "<?=$this->lang->line('blank')?>";
		MSG_DUPLICATED = "<?=$this->lang->line('duplicated')?>";
		SAVE = "<?=$this->lang->line('save')?>";
 		CANCEL = "<?=$this->lang->line('cancel')?>";
		MSG_DENIED_DESTINY = "<?=$this->lang->line('denied_destiny')?>";
 		MSG_ALREADY_EXISTS = "<?=$this->lang->line('already_exists')?>";
 		MSG_NOT_DELETED = "<?=$this->lang->line('not_deleted')?>";
 		MSG_ERROR_DELETING = "<?=$this->lang->line('error_deleting')?>";
 		MSG_NO_SIMILAR_TAGS= "<?=$this->lang->line('no_similar_tags')?>";
 		MSG_SEE= "<?=$this->lang->line('see')?>";
 		MSG_SEE_TAGS= "<?=$this->lang->line('see-tags')?>";
 		DIR_LIST = '<?=$this->lang->line('dir_list')?>';
 		EDIT_FILE = '<?=$this->lang->line('edit_file')?>'; 
 		MAKE_DIR = '<?=$this->lang->line('make_dir')?>'; 
 		TAG_SELECT = '<?=$this->lang->line('tag_select')?>';
 		INFO = '<?=$this->lang->line('info')?>';
 	</script>

    <script type="text/javascript" src="resource/js/funciones.js"></script>

	<?php if ($this->permissions->is_admin()) { ?>
    <script type="text/javascript" src="resource/js/funcionesadmin.js"></script>	
	<?php } ?>


<title>4vientos</title>
</head>

<body>
<div id="container">

	<div id="topbar">
		<div id="logo">
			<a href="<?=base_url()?>/dir" title="<?=$this->lang->line('home')?>">
				<img src="resource/images/logotx.png" alt="4vientos <?=$this->lang->line('root')?>" title="4vientos <?=$this->lang->line('root')?>" border="0" />
			</a>

				Hola <?=$this->session->userdata('username');?> 
	<?php echo " | ".anchor("admin/admin","Admin",array("title"=>"Admin"));?> 
	|<? print_r($this->session->userdata('idgroup'));?>&nbsp;
		 <a href="<?=base_url()?>/account/sign_out">exit</a -->
		</div>




	 		<div id="divsearch">
			<form name='' method='post' action='<?=base_url()?>/search'>
			<label for='namesearch'><?=$this->lang->line('search')?></label><input type='text' name='namesearch' id='namesearch' value='' size='20' />
			<label for='tagssearch'><?=$this->lang->line('tags')?></label><input type='text' name='tagssearch' id='tagssearch' value='' size='15' />
			</label><input type='submit' name='search' id='search' src='resource/images/icon_lupa.gif' value="Buscar" />
			</form>
		</div>	
		

	</div>



	<div id="dialogo" title="Confirmar acción" class='dialog'>
		<?=$this->lang->line('are_you_sure')?>
	</div>

	<div id="overwrite" title="Confirmar acción" class='dialog'>
		<?=$this->lang->line('are_you_sure_overwrite')?>		
	</div>
	
	<div id='dirlist' title='Lista de directorios' class='dialog'>
		<?php
			//$dir = ($dir==1)?0:$dir;
			$this->display->dirListRedux($this->file->dir_dir(1));
		?>
	</div>
	
	<div id='info' title='<?=$this->lang->line('info')?>'  class='dialog'>
	</div>
	
	<div id='warningmsg' title='Atención'  class='dialog'>
	</div>

	<div id='permissions' title='<?=$this->lang->line("change-permissions")?>'  class='dialog'>
	</div>

	<div id='owners' title='Atención'  class='dialog'>
	</div>

	<div id='tagselect' title='Etiquetas existentes'  class='dialog'>
	</div>

	<div id='editnewfile' title='Atención' class='dialog'>
		<label for='new_file_name'>Nombre</label><br />
		<input type='hidden' id='new_file_idfile' name='new_file_idfile' value='' size='40' />
		<input type='text' id='new_file_name' name='new_file_name' value='' size='40' /><br />
		<label for='new_file_text' id='label_new_file_text'>Texto</label><br />
		<textarea id='new_file_text' name='new_file_text' cols='40'></textarea><br />
		<label for='new_file_desc'>Descripción</label><br />
		<input type='text' id='new_file_desc' name='new_file_desc' value='' size='50' /><br />
		<label for='new_file_tags'>Tags</label><br />
		<input type='text' id='new_file_input_tags' name='new_file_input_tags' value='' size='30' /><br />
	</div>

	
    <script src="resource/js/fileuploader.js" type="text/javascript"></script>
    <script>        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('fileupload'),
                action: '<?=base_url()?>/operation/upload/',
                // additional data to send, name-value pairs
        			params: {
            		dir: '<?php echo $dir; ?>'
        			},
					onComplete: 
									function(id, fileName, responseJSON)
									{					
											//alert("Fichero: "+ responseJSON.file);
											if (responseJSON.file != "-1" && responseJSON.overwrite != "1")
											{
												carga(responseJSON.file);
											} 
											else if (responseJSON.file != "-1" && responseJSON.overwrite == "1") 
											{
												confirma_y_carga(responseJSON.file);
											}
											else
											{
												alert("Permission Denied");
											}	
									},
        			//showMessage: function(message){ alert(message); },
                debug: true
            });           
        }
        
        // in your app create uploader as soon as the DOM is ready
        // don't wait for the window to load  
        window.onload = createUploader;   

    </script> 
	

<div id="content">

	<!--div id='sidebar'>
		<div id="highlighted" >
			<?php
				echo $this->display->highlighted();
			?>
		</div>
		<div id="popular" >
			<?php
				echo $this->display->most_voted();
			?>
		</div>
		<div id="popular" >
			<?php
				echo $this->display->most_popular();
			?>
		</div>
		<div id="last_created" >
			<?php
				echo $this->display->last_created();
			?>
		</div>
	</div -->

				
	<?php
	
	//	echo $this->lang->line('welcome');

	
	//echo "</div>";
		
	if (!$search)
	{
				
			?>
			
		<div id='dirheader'>
			<div>
				<b>2016-2017: </b><a 
href="/4vientos/index.php/dir/view/173/cuaderno-del-profesorado">CUADERNO PROF</a> | 
	<a href="/4vientos/index.php/dir/view/4017/af">AF</a> |  
	<a href="/4vientos/index.php/dir/view/4018/ci">CI</a> | 
	<a href="/4vientos/index.php/dir/view/4019/tl">TL</a> | 
	<a href="/4vientos/index.php/dir/view/4020/gevec">GVEC</a> | 
	<a href="/4vientos/index.php/dir/view/4016/asir">ASIR</a> | 
	<a href="/4vientos/index.php/dir/view/4021/dam">DAM</a> | 
	<a href="/4vientos/index.php/dir/view/4022/ga">GA</a> | 
	<a href="/4vientos/index.php/dir/view/4023/acom">ACOM</a> | 
	<a href="/4vientos/index.php/dir/view/4024/smi">SMI</a> |
	<a href="/4vientos/index.php/dir/view/4025/fpb">FPB</a> | 
	<a href="/4vientos/index.php/dir/view/4026/fpe">FPE</a> | 
	<a href="/4vientos/index.php/dir/view/4027/acceso">PREP</a>
			</div>
			<div> Curso Anterior 
	<a href="/4vientos/index.php/dir/view/181/af">AF</a> |  
	<a href="/4vientos/index.php/dir/view/182/ci">CI</a> | 
	<a href="/4vientos/index.php/dir/view/180/tyl">TL</a> | 
	<a href="/4vientos/index.php/dir/view/3592/gevec">GVEC</a> | 
	<a href="/4vientos/index.php/dir/view/178/asir">ASIR</a> | 
	<a href="/4vientos/index.php/dir/view/179/dam">DAM</a> | 
	<a href="/4vientos/index.php/dir/view/185/ga">GA</a> | 
	<a href="/4vientos/index.php/dir/view/184/acom">ACOM</a> | 
	<a href="/4vientos/index.php/dir/view/3498/smi">SMI</a> |
	<a href="/4vientos/index.php/dir/view/2754/fp-bsica">FPB</a> | 
	<a href="/4vientos/index.php/dir/view/187/fpe">FPE</a> | 
	<a href="/4vientos/index.php/dir/view/188/prep">PREP</a>
			</div>

			<div id='breadcrumb'>
				<?php
					echo $this->lang->line('you-are-here').":".  $this->display->breadcrumb($dirdata);
				?>
			</div>
				
		<?php
		$this->display->dirData($dirdata);
		echo $this->display->menu();

		echo "</div>";
		$this->display->dirList($this->file->dir($dir,$page,$limit,$order));

		$config['base_url'] = base_url()."/dir/view/".$dir;
		$config['uri_segment'] = 4;
		$config['total_rows'] = $this->file->total_files;
		$config['per_page'] =  $this->config->item("pagination");

		$this->pagination->initialize($config);

		echo $this->pagination->create_links();
		?>

		<?php
	}
	else
	{
		

		?>

			<div id='dirheader'>
			<div id='breadcrumb'>
				&gt;<a href="<?=base_url()?>/dir"><?=$this->lang->line('root')?></a>
			</div>
			<div class="divhead"><?=$this->lang->line('search-results')?>: <?=$this->file->total_files?></div>

		<?php
		$this->display->is_a_search = TRUE;
		$this->display->menu_search();
		echo "</div>";
		$this->display->dirList($dirdata);
		
		$config['base_url'] = base_url()."/search/keep_searching";
		$config['uri_segment'] = 3;
		$config['total_rows'] = $this->file->total_files;
		$config['per_page'] =  $this->config->item("pagination");

		$this->pagination->initialize($config);

		echo $this->pagination->create_links();
		
		?>

		<?php
	}
	
	//$this->file->dir_crawl($dir);
	//print_r($this->file->dircrawl);
?>


<?php
	$this->load->view("footer.php");
?>
	

</div>

</div>


</body>
</html>
