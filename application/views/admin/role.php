<!DOCTYPE html>
<html lang="en">
<head>
<?php
	$this->load->view("meta");
?>

	<base href="<?=base_url()?>" />
  <link rel="stylesheet" href="resource/css/4vientos.css" type="text/css" />
  <link rel="stylesheet" href="resource/css/admin.css" type="text/css" />
  <link rel="stylesheet" href="resource/css/dinastyle.php" type="text/css" />
    	<link type="text/css" href="resource/css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="resource/js/jquery.js"></script>
    <script type="text/javascript" src="resource/js/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="resource/js/jquery.ui.js"></script>
    <script type="text/javascript" src="resource/js/roleadminforms.js"></script>
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
 		DIR_LIST = '<?=$this->lang->line('dir_list')?>';
 		EDIT_FILE = '<?=$this->lang->line('edit_file')?>'; 
 		TAG_SELECT = '<?=$this->lang->line('tag_select')?>';
 		INFO = '<?=$this->lang->line('info')?>';
 	</script>

    <script type="text/javascript" src="resource/js/funciones.js"></script>


<title>4vientos</title>
</head>

<body>
<div id="container">

	<div id="topbar">
		<div id="divsearch">
			<form name='' method='post' action='<?=base_url()?>/search'>
			<label for='namesearch'>Buscar</label><input type='text' name='namesearch' id='namesearch' value='' />
			<label for='tagssearch'>Tags</label><input type='text' name='tagssearch' id='tagssearch' value='' />
			</label><input type='image' name='search' id='search' src='icon_lupa.gif' value="search" />
			</form>
		</div>	
	Hola <?=$this->session->userdata('username');?> | <?=$this->session->userdata('iduser')?>| <?php echo anchor("admin","Admin",array("title"=>"Admin"));?> |<? print_r($this->session->userdata('groups'));?>&nbsp;|&nbsp;<a href="<?=base_url()?>/account/sign_out">Logout</a> | <a href="<?=base_url()?>/about">about</a>
	</div>


	<div id="formdialog"></div>

<div id="content">

	
	<div id="admin">
	
	<?php
	
		echo $this->lang->line('welcome');
			
	?>
	Admin
	<?php
		$this->load->view("admin/adminmenu.php");
	?>
	

		<a href='admin/role' title=''>Ver todo</a>&nbsp;|&nbsp;
		<a href='admin/role' id='form_role_create' title=''>Crear nuevo</a>&nbsp;|&nbsp;
			<?php echo form_open("admin/role/search"); ?>
           	 <?php echo form_fieldset(); ?><legend><?php echo $this->lang->line('search'); ?></legend>

                <?php if (isset($form_result)) : ?>
                	<span><?php echo $form_result;?></span>
                 <?php endif; ?>	
        
						<!-- field search term -->
                <?php echo form_label($this->lang->line('search_term'), 'search_term'); ?>
					 <?php echo form_error('search_term'); ?>
                <br />
                <?php echo form_input(array("name"=>"search_term","value"=>$search_term,"size"=>20,"id"=>"role_search_term")); ?>

               	<a href='buscar' title='Buscar' id='role_search'>Buscar</a><br />
               

            <div class="clear"></div>

            <?php echo form_fieldset_close(); ?>
            <?php echo form_close(); ?>	
       
		<div id='div_role_table'>
		<h3><?=$this->lang->line('role-mng');?></h3>
			<?php echo $this->role_model->table(); ?>
		</div>	
	
	</div>
	

</div>
</div>
<?php
	$this->load->view("footer.php");
?>

</body>
</html>