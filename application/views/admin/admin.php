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


	

<div id="content">

	
	<div id="admin">
	
	<?php
	
		echo $this->lang->line('welcome');
			
	?>
	Admin
	<?php
		$this->load->view("admin/adminmenu.php");
	?>
	


	
	</div>
	

</div>
</div>
<?php
	$this->load->view("footer.php");
?>

</body>
</html>