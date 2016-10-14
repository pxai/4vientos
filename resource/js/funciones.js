
/**
* funciones.js
* Core of the file operations
*/
$(document).ready(function() {


	var currentdir = 1;
	var updatefile = 0;
	var PERMISSION_DENIED = "-1";
	$('#warningmsg').html("");


	
	/**
	* generic function to check permissions
	*
	*/
	function check_permissions (id,what)
	{
   			var result = $.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/check_permissions/",
   			data: "id="+id+"&op="+what,
   			async: false,
   			success: function(msg){
     				if (msg == "{'Result'='Error'}")
     				{
     						$('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         	$('#warningmsg').dialog('open');
     				}

   			}
 			});
 			return result.responseText;
	}


	/*************************************************
	* fileget, check for permissions
	****************************************************/
	$(".fileget").live('click',function(event) {
	   		event.preventDefault();
	   		
				// get the id				
				var v = $(this).attr("id").split("-");
   			id = v[1];
   			
   			var r = check_permissions(id,"r");
   	
   			if (r == "{'Result'='Error'}") 
   			{ 
   				return 0;
   			}
				else 
				{
					document.location.href=$(this).attr("href");
				}
	});
	
	

	


	$(".filedate").live('click',function(event) {
				var v = $(this).attr("id").split("_");

   			id = v[1];
				if ($("#file_checkbox_"+id).attr("checked"))
				{
					 if (!comprobarSeleccion()) {$(".menuoculto").css("visibility","hidden");}
					$("#file_"+id).css("background-color","");
					$("#file_checkbox_"+id).attr("checked","");
				}
				else
				{
					$(".menuoculto").css("visibility","visible");
					$("#file_"+id).css("background-color","yellow");
					$("#file_checkbox_"+id).attr("checked","checked");
				}

	});
	
		
	$(".file_checkbox").live('click',function(event) {
				var v = $(this).attr("id").split("_");
   			id = v[2];
   			//alert(id + $("#file_checkbox_"+id).attr("checked"));
				if ($("#file_checkbox_"+id).attr("checked"))
				{
					$(".menuoculto").css("visibility","visible");
					$("#file_"+id).css("background-color","black");
				}
				else
				{
					 if (!comprobarSeleccion()) {$(".menuoculto").css("visibility","hidden");}
					$("#file_"+id).css("background-color","");
				}

	});




	
 $("a.editor").live('click',function(event) {
		
				//$("#file_185").unbind("click",filehandler);

				// prevenimos que se vaya al link
   			event.preventDefault();
   			deselect();
   			var id = $(this).attr("href");
   			var bloque =  "#fileedit_" + id;
   			var desc = $("#file_"+id +"_desc").html();
   			var tags = "";
   			//var tags = $("#file_"+id +"_tags");
   			//alert("Espera: " + $(this).attr("href"));
   	     		//document.getElementById("applicantpreview").style.visibility = "visible";
      	  		//document.getElementById("applicantpreview").style.display = "block";
      	 
      	 $(".file_"+id +"_tag > a").each(function(index) {
    				tags += $(this).html()+", ";
			  });
			  	tags = rtrim(trim(tags),",");

      	 if ($(bloque).is(":hidden")) {
      	 		xhtml = "";
					xhtml += "<label for='file_"+id+"_desc'>Descripción</label><br />";
					xhtml += "<input type='text' name='fileeditdesc' id='file_"+id+"_input_desc' size='50' value='"+desc+"' /><br />";
					xhtml += "<label for=id='file_"+id+"_tags'>Tags</label><br />";
					xhtml += "<input type='text' name='fileedittags' id='file_"+id+"_input_tags' size='30' value='"+tags+"' /><br />";
					//xhtml += "<a href='' title='"+MSG_SEE_TAGS+"' id='viewtags'>"+ MSG_SEE +"</a><br />";
					xhtml += "<input type='button' name='fileeditbutton'  class='filebutton' id='file_button_"+id+"' value='Guardar' />";
					xhtml += "<input type='button' name='fileeditbuttoncancel'  class='filebuttoncancel' id='file_button_"+id+"_cancel' value='Cancel' />";
					$(bloque).html(xhtml);

				$(bloque).slideDown("slow");
				$(bloque).css('visibility','visible');$(bloque).css('display','block');
			} else {
				$(bloque).hide("slow");
				$(bloque).html("");
				$(bloque).css('visibility','hidden');$(bloque).css('display','none');
			}
      	  	

        		
   });
   
   //$(".filebutton").bind("mousedown mouseup", function(){
	$('.filebutton').live('click', function() {
  // Live handler called.


				// prevenimos que se vaya al link
   			//event.preventDefault();
   			deselect();
   			var v = $(this).attr("id").split("_");
   			id = v[2];
   			var descvalue = $("#file_"+id+"_input_desc").val();
   			var tagvalue = $("#file_"+id+"_input_tags").val();
   			var desc = $("#file_"+id +"_desc");
   			var tagdiv = $("#file_"+id+"_tags_div");
   			var bloque =  "#fileedit_" + id;
   
  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/tags_desc/",
   			data: "id="+id+"&op=4&desc="+descvalue+"&tags="+tagvalue,
   			success: function(msg){
     				if (msg == "1")
     				{
     					var tagarray = tagvalue.split(",");
     					xhtml = "Tags: ";
     					for (i=0;i<tagarray.length;i++)
     					{
     						tagarray[i] = trim(tagarray[i]);
     						if (tagarray[i]=="") continue;
     						xhtml += "<span class='file_"+id+"_tag'><a href='tags+php?t="+tagarray[i]+"'>"+tagarray[i]+"</a></span>"+", ";
     					}
						xhtml = rtrim(trim(xhtml),",");
     					//alert(xhtml);
     					$(desc).html(descvalue)
     					$(tagdiv).html(xhtml);
     					
     					$(bloque).hide("slow");
     					$("#tagselect").hide("slow");
     				}
     				else
     				{
     					   $('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         	$('#warningmsg').dialog('open');
     				}
   			}
 			});
 			
     					$("#tagselect").hide("slow");
      		
   });
      
      

	$('.filebuttoncancel').live('click', function() {
  // Live handler called.
   			deselect();
   			var v = $(this).attr("id").split("_");
   			id = v[2];
   			var bloque =  "#fileedit_" + id;
   
					
     		$(bloque).hide("slow");
      		
   });      
      
   /***************************** CREATE DIR *************************************/
   	/**
   	* click sobre el enlace para nuevo directorio
   	*/
     $('.createdirlink').click(function(event) {
  // Live handler called.


				// prevenimos que se vaya al link
   			event.preventDefault();
   			deselect();
   			var id = $(this).attr("href");

   			bloque= "#createdir";
   		 if ($(bloque).is(":hidden")) {				
				$(bloque).css('visibility','visible');$(bloque).css('display','block');
			} 
   			
   			$('#createdir').dialog('open');
      		
   });
   
   
   //filedirectaccess
   
   
     $('.newdir_button').click(function(event) {
  // Live handler called.


				// prevenimos que se vaya al link
   			event.preventDefault();
   			deselect();
   			var idattr = $(this).attr("id");
   			var idarr = idattr.split("_");
   			var id = idarr[2];
   			var bloque= "#createdir";
   			var dirname = $("#newdir_"+id).attr("value");
  
  				if (trim(dirname)=="")
  				{
  					$('#warningmsg').html(MSG_NOT_BLANK);
					$('#warningmsg').dialog('open');
					return ;
  				}
  				//alert("jare : " + id + " y " + dirname);
		if (check_duplicated(id,dirname,0)=="0")
		{

		  	$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/mkdir/",
   			data: "id="+id+"&dirname="+dirname,
   			success: function(msg){
     				if (msg != PERMISSION_DENIED)
     				{		
						//alert("Ok, directorio: " + dirname + "creado!" + msg);
						$("#newdirplaceholder").append(msg);						
						$('#createdir').dialog('close');
     				}
     				else // DENIED
     				{
     					   $('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         	$('#warningmsg').dialog('open');
     				}
   			}
 			});
		}
 		else
		{
			//alert("Dame id:"+id+" y el new:" + dirname);
			$('#warningmsg').html(MSG_DUPLICATED);
			$('#warningmsg').dialog('open');
		}
   });
   
   
   
   
   /*****************************************************
   * Create direct access
   *******************************************************/
   
     $('.filedirectaccess').live('click', function() {
  // Live handler called.


				// prevenimos que se vaya al link
   			event.preventDefault();
   			deselect();
   			var idattr = $(this).attr("id");
   			var idarr = idattr.split("_");
   			var id = idarr[2];
   			var  bloque= "#createdir";
   			var dirname = $("#newdir_"+id).attr("value");
  
  				//alert("jare : " + id + " y " + dirname);

  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/direct_access/",
   			data: "id="+id+"&dirname="+dirname,
   			success: function(msg){
     				if (msg != "1")
     				{		
						//alert("Ok, directorio: " + dirname + "creado!" + msg);
						$("#newdirplaceholder").append(msg);						
						$(bloque).hide("slow");
						$(bloque).css('visibility','hidden');$(bloque).css('display','none');
     				}
     				else
     				{
     					//alert("El msg es: " + msg);
     				}
   			}
 			});
      		
   });
 
   /*****************************************************************
 	* Edit file
   ******************************************************/
   var filetype = 2;
   
       $('.filedit').live('click', function(event) {

				// prevenimos que se vaya al link
   			event.preventDefault();
   			var filedata = $(this).attr("href").split("_");
   			var id = filedata[0];
   			filetype=filedata[1];
				var result = "";
				updatefile = 1;
			//	alert(id +" abs " + filetype);
				
   			$.ajax({
   			type: "POST",
   			async: false,
   			data: "id="+id,
   			url: SITE_URL+ "/operation/edit_file/",
   			success: function(msg){
     				if (msg != "-1")
     				{
						result = msg;
						//alert(msg);
		 				if (filetype==2)
 						{
   						$('#label_new_file_text').html("Texto");
 							clearEditor("#new_file_text");

 							$('#editnewfile').html(result);
							//alert($('#editnewfile').html());
 							loadEditor('new_file_text');
 						//$('#new_file_text').ckeditor();
						}
						else
						{
 	  						filetype = 3;
   						$('#label_new_file_text').html("Enlace (http://...)");
 							clearEditor("#new_file_text");
 							$('#editnewfile').html(result);
 						}

						$('#editnewfile').dialog('open');
						$('#editnewfile').css("display","block");

     				}
     				else // PERMISSION DENIED
     				{
     					   $('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         	$('#warningmsg').dialog('open');
     				}
   			}
 			});


   	});
   
   /*****************************************************
   * Create new file
   ******************************************************/
    $('.createfile').click(function(event) {

				currentdir = $(this).attr("href");
				// prevenimos que se vaya al link
   			event.preventDefault();
   			updatefile = 0;
   			filetype = 2;
   				$('#label_new_file_text').html("Texto");
 				clearEditor("#new_file_text");
 				loadEditor("#new_file_text");
				$('#editnewfile').css("display","block");
				$('#editnewfile').dialog('open');
      		
   	});
   	
	// Create new link
   	$('.createlink').click(function(event) {

				currentdir = $(this).attr("href");
				// prevenimos que se vaya al link
   			event.preventDefault();
   			updatefile = 0;
   			filetype = 3;
   				$('#label_new_file_text').html("Enlace (http://...)");
 				clearEditor("#new_file_text");
				$('#editnewfile').css("display","block");
				$('#editnewfile').dialog('open');
      		
   	});
   	
   	/*******************************************************
	* save_new_file
	*******************************************************/
	function save_new_file (id)
	{

			id = currentdir;
			idfile = $("#new_file_idfile").val();
			filename = $("#new_file_name").val();
			filetext = $("#new_file_text").val();
			filedesc = $("#new_file_desc").val();
			filetags = $("#new_file_input_tags").val();
			
			var finalurl;
			var updatemode;

			// Is update operation?
			if (updatefile)
			{
				finalurl = "update_file";
				updatemode = 1;
			}
			else // Is a new file
			{
				finalurl = "save";
				updatemode = 0;
				idfile = currentdir;
			}
			
			
			if (trim(filename)=="")
			{
				//alert("Pon algo en el nombre rata");
				$('#warningmsg').html(MSG_NOT_BLANK);
     		    $('#warningmsg').dialog('open');
				return 0;
			}

	 			//alert("Comprobando: " + id + " con " + filename + " como:" +finalurl + " en: " + currentdir);
	 			
		//	alert("Vamos " +idfile+ "\n dir:" + id + "\n nombre:" + filename + " \ntexto: " + filetext + "\n desc: " + filedesc + "\n tags:" + filetags +"\n type" + filetype);
			if (check_duplicated(idfile,filename,updatemode)=="0")	
		 	{
			 	$.ajax({
   					type: "POST",
   					url: SITE_URL+ "/operation/"+finalurl,
   					data: "idfile="+idfile+"&id="+id+"&name="+filename+"&text="+filetext+"&desc="+filedesc+"&tags="+filetags+"&type="+filetype,
   					success: function(msg){
     						if (msg != "-1")
     						{
								carga(msg);
   	  					}
     						else
     						{
     							$('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         		$('#warningmsg').dialog('open');
     						}
   					}
 				});
 				return 1;
			}
 			else
 			{

	 			$('#warningmsg').html(MSG_DUPLICATED);
				$('#warningmsg').dialog('open');
				return 0;
	 		}
 	
	}
   	
	
   /*******************************************************************
   * MOVE 
   ******************************************************************/
   	var filetomove;
		// MOVE FILE
   	$('.filemove').live('click',function(event) {

				// prevenimos que se vaya al link
   			event.preventDefault();
   			var id = $(this).attr("href");
   			filetomove = id;
				$('#dirlist').dialog('open');
      		
   	});
   	
		// MOVE DIR
     	$('.movedirlink').click(function(event) {

				// prevenimos que se vaya al link
   			event.preventDefault();
   			var id = $(this).attr("href");
         	if (!comprobarSeleccion())
         	{
	         	$('#warningmsg').dialog('open');
         		return;
         	}
				$('#dirlist').dialog('open');
      		
   	});
   
   	var selecteddestiny = 0;
   	
      $(".dirlistselect").live('click',function(event) {
      	   			event.preventDefault();
   			var id = $(this).attr("href");
  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/dir/navigation",
   			data: "dir="+id,
   			success: function(msg){
     				if (msg != "")
     				{
						
						//alert("Ok, directorio: " + dirname + "creado!" + msg);
						$("#dirlist_"+id +" > ul ").remove();		
						$("#dirlist_"+id).append(msg);		
						$("#dirlist_"+selecteddestiny).css("background-color","");
						$("#dirlist_"+id).css("background-color","pink");
						selecteddestiny = id; 				
						//$(bloque).hide("slow");
						//$(bloque).css('visibility','hidden');$(bloque).css('display','none');
     				}
   			}
 			});
      });
      
      function mover ()
      {
			var total = $(".file_checkbox:checked").map(function() {
					return $(this).val();
					}).get().join();
			
			total = (total=="")?filetomove:total;

			//alert("Moviendo " + total + " a " + selecteddestiny);
			
   							// prevenimos que se vaya al link
  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/move/",
   			data: "id="+total+"&des="+selecteddestiny,
   			success: function(msg){
     				if (msg != "-1")
     				{	
     					//alert(msg);
     					rvalue = msg.split(":");

 						moved = rvalue[1].split(",");
     					
  						for (i=0;i<moved.length;i++)
  						{
						  	$("#file_"+moved[i]).hide("slow");
  						} 
  						
  						if (rvalue[2] != "")
  						{
  							//alert("No se permitió borrar: " + rvalue[2]);
     						$('#warningmsg').html(MSG_NOT_DELETED + rvalue[2]);
     		         	$('#warningmsg').dialog('open');
  						}
  
  						 if (rvalue[3] != "")
  						{
  							//alert("No se permitió borrar: " + rvalue[2]);
     						$('#warningmsg').html(MSG_ALREADY_EXISTS + rvalue[3]);
		         		$('#warningmsg').dialog('open');
  						}				
     					//
         		}
         		else
         		{
     						$('#warningmsg').html(MSG_DENIED_DESTINY);
		         		$('#warningmsg').dialog('open');
         		}
   			}
 			});
      }
      
      /*************************************
      * Eliminar directorios
      **************************************/
      $('.deletedirlink').click(function(event) {
         	event.preventDefault();
         	if (!comprobarSeleccion())
         	{
	         	$('#warningmsg').dialog('open');
         		return;
         	}
   			$( "#dialogo" ).dialog( "option", "buttons", [
			    	{
        				text: "Ok",
        				click: function() {							
        					deletedirlink(); 
							$(this).dialog("close"); }
    					},
    				{	
    					text: "Cancel", 
						click: function() { 
							$(this).dialog("close"); 
						} 
					}	
					] );
				abrir_dialogo();
		});
		
      function deletedirlink () {

				var total = $(".file_checkbox:checked").map(function() {
					return $(this).val();
					}).get().join();

   							// prevenimos que se vaya al link
  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/delete_all/",
   			data: "id="+total,
   			success: function(msg){
     				if (msg != "")
     				{
     					rvalue = msg.split(":");

						if (rvalue[1]!="")
						{
 							deleted = rvalue[1].split(",");
     						//alert(msg);
  							for (i=0;i<deleted.length;i++)
  							{
						  		$("#file_"+deleted[i]).hide("slow");
  							} 
  						}
  						
  						if (rvalue[2] != "")
  						{
  							//alert("No se permitió borrar: " + rvalue[2]);
  							$('#warningmsg').html(MSG_NOT_DELETED + rvalue[2]);
     		         	$('#warningmsg').dialog('open');
  						}
  						
     					//
         		}
   			}
 			});
      		
   	}
   	
   	
   	/*
   	* clic to delete file/directory
   	* click para ELIMINAR UN directorio
   	*/
   	$('.filedelete').live('click',function(event) {
  // Live handler called.


				// prevenimos que se vaya al link
   			event.preventDefault();
   			var id = $(this).attr("href");
   			$( "#dialogo" ).dialog( "option", "buttons", [
			    	{
        				text: "Ok",
        				click: function() {							
        					deleteonedirlink(id); 
							$(this).dialog("close"); }
    					},
    				{	
    					text: "Cancel", 
						click: function() { 
							$(this).dialog("close"); 
						} 
					}	
					] );
				abrir_dialogo();
		});
		
		function deleteonedirlink (id) {
   			deselect();
  			$.ajax({
   			type: "POST",
   			dataType: "json",
   			url: SITE_URL+ "/operation/delete/",
   			data: "id="+id,
   			success: function(msg){
   				//alert(":"+msg+":");
   				switch (msg)
   				{
   					case 1 : 
   									$("#file_"+id).hide("slow");
   									break;
   					case -1:
     				     				$('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         				$('#warningmsg').dialog('open')   									 
   									break;
   					default:
   								   $('#warningmsg').html(MSG_ERROR_DELETING);
     		         				$('#warningmsg').dialog('open')
     		         				break;
   				}
   			}
 			});
      		
	   }
   
   /*************************************
   * Select all
   **************************************/
   	var allselected = 0;
      $('.selectalldirlink').click(function(event) {
				// prevenimos que se vaya al link
   			event.preventDefault();
   			selectAll();
   	});   

      $('#file_checkbox_all').click(function(event) {

   			selectAll();
   	});   
   	
   
   function selectAll () {
      			//var id = $(this).attr("href");
   			if (!allselected)
   			{
   				$(".file_checkbox").attr("checked","checked");
   				$("#file_checkbox_all").attr("checked","checked");
   				$(".file").css("background-color","yellow");
   				$(".menuoculto").css("visibility","visible");
   				allselected = 1;
   			}
   			else
   			{
   				allselected = 0;
   				$(".menuoculto").css("visibility","hidden");
   				deselect();
   				$("#file_checkbox_all").attr("checked","");
   			}
   			
   			var total = $(".file_checkbox:checked");
   			//alert("Total: " + total.length);
   }
   
     /**********************************************
   	* RENAME
   	***********************************************/
   	$('.filerename').live('click',function(event) {
  // Live handler called.


				// prevenimos que se vaya al link
   			event.preventDefault();
   			
   			var id = $(this).attr("href");
   			var isdir = ($("#filename_"+id).attr("class")=="dirname")?1:0;
   			//var current = ($("#filename_"+id+" > strong > a").html()!="")?$("#filename_"+id+" > strong > a").html():$("#filename_"+id+" > a").html();
   			var current = "";
   			current = $("#filename_"+id+" a").html();
   			//alert(id + ": "+current + ":"+isdir);
   			$("#filename_"+id).html("<input type='hidden' id='filenamehiddenvalue_"+id+"' name='filenamehiddenvalue' value='"+current+"' size='30' /><input type='text' id='filenamevalue_"+id+"' name='filenamevalue' value='"+current+"' size='30' /><a href='"+id+"' class='linkrename' title='Pincha para guardar'>Ok</a> | <a href='"+id+"' class='linkrenamecancel' title='Pincha para cancelar'>Cancel</a>");

		});
		
		
		  $('.linkrename').live('click',function(event) {
  

				// prevenimos que se vaya al link
   			event.preventDefault();
   			
   			var id = $(this).attr("href");
   			var isdir = ($("#filename_"+id).attr("class")=="dirname")?1:0;
    			var current = "";
   			current = $("#filenamehiddenvalue_"+id).val();
   			newvalue = $("#filenamevalue_"+id).val();
   			
   			if (trim(newvalue)==""){
   				alert("Pon algo jambo");
   				return;
   			}
   			else
   			{
	   			if (check_duplicated(id,newvalue,1)=="0")
	   			{
   					$.ajax({
   						type: "POST",
   						url:   SITE_URL+ "/operation/rename/",
   						data: "id="+id+"&new="+newvalue,
   						success: function(msg){
	   						if (msg=="1")
   	  						{	
     								finalvalue = newvalue;     					
  									if (isdir)
									{
										$("#filename_"+id).html("<strong><a href='kutxa.php?d="+id+"' >"+newvalue+"</a></strong>");
									}
  									else
									{
										$("#filename_"+id).html("<a href='kutxa.php?d="+id+"' >"+newvalue+"</a>");
									}
								}
								else
								{
     								finalvalue = current;
     								$('#warningmsg').html(MSG_PERMISSION_DENIED);
     		         			$('#warningmsg').dialog('open');				
								}
   					   }
   					});
				}
				else
				{
					$('#warningmsg').html(MSG_DUPLICATED);
					$('#warningmsg').dialog('open');
				}
 			
   			}
		});
		
		
		$('.linkrenamecancel').live('click',function(event) {
  

				// prevenimos que se vaya al link
   			event.preventDefault();
   			
   			var id = $(this).attr("href");
   			var isdir = ($("#filename_"+id).attr("class")=="dirname")?1:0;
    			var current = "";
   			current = $("#filenamehiddenvalue_"+id).val();
   			//alert(id + ": "+current + ":"+isdir);
   			
   			if (isdir)
   			{
   				$("#filename_"+id).html("<strong><a href='kutxa.php?d="+id+"' >"+current+"</a></strong>");
   			}
   			else
   			{
   				$("#filename_"+id).html("<a href='kutxa.php?d="+id+"' >"+current+"</a>");
   			}
   			//$( "#filename_"+id ).
		});
		

/*******************************+
* TAGS
***********************************/
	var actual = 0;
	
		$('input[id*="_input_tags"]').live('keyup',function()
	  	{
			actual = $(this).attr("id");
			
			tagarray = $(this).val().split(",");
			lasttag = tagarray[tagarray.length-1];

			lastone = (lasttag.length < 3)?"zzzzz":lasttag;
						
			
			//alert("The last one : " + lastone);
			
			// Less than 2? out of here
			//if (lastone.length < 3) {return; }
			
	  		$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/tag/get",
   			data: "q="+lastone,
   			success: function(msg){
     				if (msg != "0")
     				{
     					$("#tagselect").html(msg);
     					$("#tagselect").dialog('open');
     				}
     				else
     				{
     					$("#tagselect").html(MSG_NO_SIMILAR_TAGS);

     				}
     				$("#"+actual).focus();
    			}
 			});
 			
 			$('#proposedtags li').live('click',function()
	  		{
				var previous_value = $("#"+actual).val();
				tagarray = $("#"+actual).val().split(",");		//

				if (trim(previous_value) == "")
				{
					$("#"+actual).val($(this).html());

				}
				else
				{
					tagarray.pop();
					tagarray.push($(this).html());
					$("#"+actual).val(tagarray.toString()+", ");
					//$("#"+actual).val(previous_value +", "+$(this).html());
				}
				$("#"+actual).focus();
	  		});
				//	  		
	  	});
		
	/*
	  function actualizarTags (elemento)
	  	{


			var actual = elemento.id;
			alert("Id: " + actual + ": " + elemento.value);
			
			tagarray = elemento.value.split(",");
			lastone = tagarray[tagarray.length-1];
			

	  		$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/tag/get",
   			data: "q="+lastone,
   			success: function(msg){
     				if (msg != "0")
     				{
     					$("#tagselect").html(msg);
     					$("#tagselect").dialog('open');
     				}
     				else
     				{
     					$("#tagselect").html(MSG_NO_SIMILAR_TAGS);

     				}
     				$("#"+actual).focus();
    			}
 			});
 			
 			$('#proposedtags li').live('click',function()
	  		{
		//
				var previous_value = $("#"+actual).val();
				if (trim(previous_value) == "")
				{
					$("#"+actual).val($(this).html());
				}
				else
				{
					tagarray.pop();
					tagarray.push($(this).html());
					$("#"+actual).val(tagarray.toString());

				}
				$("#"+actual).focus();
	  		});
				//	  	
				
	  	}*/

/********************************************************************
* Votastar
*********************************************************************/

   //$("div.votingpanel").find("a").each(function(i) {
   $("a[id^=votestar]").live('click',function(event) {
		
				// prevenimos que se vaya al link
   			event.preventDefault(); 				
				
				artmp = $(this).attr("id").split("_");
				
				voto = parseInt(artmp[1]);
			
				artmp = $(this).attr("id").split("_");
				
				// La id de fichero va en el id del div. 
				voteid = artmp[2];
				
				deselect_one(voteid);
			
	  			$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/vote/",
   				data: "id="+voteid+"&vote="+voto,
   				success: function(msg){
					     $("#votingpaneln_"+voteid).html(msg);
   				}
	 			});

   });	  	
   
   
    $("a.filedetails").live('click',function(event) {

   			event.preventDefault();
   			deselect();
   			var id = $(this).attr("href");
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/details/",
   				data: "id="+id,
   				success: function(msg){
					     $("#info").html(msg);
     					  $("#info").dialog('open');
   				}
	 			});

        		
   });
   
   
    $("a.filedirectopen").live('click',function(event) {

   			event.preventDefault();
   			deselect();
   			var idattr = $(this).attr("id").split("-");
   			id = idattr[1];
   			
   			
   			$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/direct_open_file/",
   				data: "id="+id,
   				success: function(msg){
					     $("#info").html(msg);
     					  $("#info").dialog('open');
   				}
	 			});
	 			     					  

        		
   });
    
     /***************************************************
   * PERMISSIONS *********************************************
   *******************************************************/
      
    $("a.filepermissions").live('click',function(event) {

   			event.preventDefault();
   			deselect();
   			var id = $(this).attr("href");
		
			// Dialog			
				$('#permissions').dialog({
					autoOpen: false,
					title: DIR_LIST,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
							var id = $("#filerecursivechange").val();
   						var recursive = ($("#recursivechange").attr('checked'))?1:0;
							var user_r = ($("#user_r").attr('checked'))?1:0;
							var user_w = ($("#user_r").attr('checked'))?1:0;
							var user_a = ($("#user_r").attr('checked'))?1:0;
							var group_r = ($("#group_r").attr('checked'))?1:0;
							var group_w = ($("#group_w").attr('checked'))?1:0;
							var group_a = ($("#group_a").attr('checked'))?1:0;
							var world_r = ($("#world_r").attr('checked'))?1:0;
							var world_w = ($("#world_w").attr('checked'))?1:0;
							var world_a = ($("#world_a").attr('checked'))?1:0;
   			
   		  				$.ajax({
   							type: "POST",
   							url: SITE_URL+ "/operation/change_permissions/",
   							data: "id="+id+"&user_r="+user_r+"&user_w="+user_w+"&user_a="+user_a+"&group_r="+group_r+"&group_w="+group_w+"&group_a="+group_a+"&world_r="+world_r+"&world_w="+world_w+"&world_a="+world_a+"&recursive="+recursive,
   							success: function(msg){
   							var permstring = "";
   							if (msg != "-1")
   							{
  // 								alert(msg);
     								rvalue = msg.split(":");
     								perm = rvalue[0].split(",");
     								
     								permstring += (perm[0]=="1")?"r":"-"; 
     								permstring += (perm[1]=="1")?"w":"-"; 
     								permstring += (perm[2]=="1")?"a":"-"; 
     								permstring += (perm[3]=="1")?"r":"-"; 
     								permstring += (perm[4]=="1")?"w":"-"; 
     								permstring += (perm[5]=="1")?"a":"-"; 
     								permstring += (perm[6]=="1")?"r":"-"; 
     								permstring += (perm[7]=="1")?"w":"-"; 
     								permstring += (perm[8]=="1")?"a":"-"; 
     								
									$("#filepermissions_"+rvalue[1]).html(permstring);   								
   								//$("#file_"+ id).attr("class","file status_"+rvalue[0]);
   								$("#permissions").dialog('close');
   							}
   						 }
	 						});

							$(this).dialog("close");
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/get_permissions_form/",
   				data: "id="+id,
   				success: function(msg){
					     $("#permissions").html(msg);
     					  $("#permissions").dialog('open');
   				}
	 			});

        		
   });
   
   

   
   /*************************************
      * Cambiar status multiple
      **************************************/
      $('.permissionslink').click(function(event) {
         	event.preventDefault();
         	if (!comprobarSeleccion())
         	{
  					$('#warningmsg').html(MSG_NOT_BLANK);
	         	$('#warningmsg').dialog('open');
         		return;
         	}
         	id = -1;
         				// Dialog			
				$('#permissions').dialog({
					autoOpen: false,
					title: DIR_LIST,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
							var id = $("#filerecursivechange").val();
   						var recursive = ($("#recursivechange").attr('checked'))?1:0;
							var user_r = ($("#user_r").attr('checked'))?1:0;
							var user_w = ($("#user_r").attr('checked'))?1:0;
							var user_a = ($("#user_r").attr('checked'))?1:0;
							var group_r = ($("#group_r").attr('checked'))?1:0;
							var group_w = ($("#group_w").attr('checked'))?1:0;
							var group_a = ($("#group_a").attr('checked'))?1:0;
							var world_r = ($("#world_r").attr('checked'))?1:0;
							var world_w = ($("#world_w").attr('checked'))?1:0;
							var world_a = ($("#world_a").attr('checked'))?1:0;
   						var total = $(".file_checkbox:checked").map(function() {
							return $(this).val();
							}).get().join();
					
   		  				$.ajax({
   							type: "POST",
   							url: SITE_URL+ "/operation/change_permissions/1",
   							data: "id="+total+"&user_r="+user_r+"&user_w="+user_w+"&user_a="+user_a+"&group_r="+group_r+"&group_w="+group_w+"&group_a="+group_a+"&world_r="+world_r+"&world_w="+world_w+"&world_a="+world_a+"&recursive="+recursive,
   							success: function(msg){
   								var permstring = "";
   								if (msg != "-1")
     								{
     									rvalue = msg.split(":");
										
										if (rvalue[1]!="")
										{
 											changed = rvalue[1].split(",");
		     								perm = rvalue[0].split(",");

     										permstring = (perm[0]=="1")?"r":"-"; 
     										permstring += (perm[1]=="1")?"w":"-"; 
     										permstring += (perm[2]=="1")?"a":"-"; 
     										permstring += (perm[3]=="1")?"r":"-"; 
     										permstring += (perm[4]=="1")?"w":"-"; 
     										permstring += (perm[5]=="1")?"a":"-"; 
     										permstring += (perm[6]=="1")?"r":"-"; 
     										permstring += (perm[7]=="1")?"w":"-"; 
     										permstring += (perm[8]=="1")?"a":"-"; 
     								
  											for (i=0;i<changed.length;i++)
  											{
												$("#filepermissions_"+changed[i]).html(permstring);   	
  											} 
  											deselect();
  										}
  								
  										$("#permissions").dialog('close');     					//
         						}
   						 }
	 						});

							$(this).dialog("close");
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/get_permissions_form/1",
   				data: "id="+id,
   				success: function(msg){
						$("#permissions").html(msg);
						$("#permissions").dialog("open");
   				}
	 			});
	 			

		});
		
  
   
 

   
   // hover effect... TODO
   /*$("a[id^=votestar]").live('hover',function(event) {
		

				artmp = $(this).attr("id").split("_");
				
				voto = parseInt(artmp[1]);
			
				artmp = $(this).attr("id").split("_");
				
				voteid = artmp[2];
				
				for (i=voto-1;i>0;i--)
				{
					$('#votestar_'+i+'_'+voteid).css("background-position","0 -60");
				}
			

   });	*/
	  	
 /*****************************************************
   * Check duplicated name in same dir
   *******************************************************/
   
     function check_duplicated(id,newname,updatemode) {
		var result = 0;
  			$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/check_duplicated/",
   				data: "id="+id+"&name="+newname+"&update="+updatemode,
   				async:false,
   				success: function(msg){
					result = msg;
   				}
 			});
      		return result;
    }
	  	

	  	
	  	
	  	
	/****************** Dialogos **************************/   
	

				
					
		// Dialog			
		$('#dialogo').dialog({
					autoOpen: false,
					title: WARNING,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() { 
							deletedirlink(); 
							$(this).dialog("close");
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
				
			// Dialog			
		$('#dirlist').dialog({
					autoOpen: false,
					title: DIR_LIST,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
							$(this).dialog("close");
							mover();
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});

			// Dialog			
		$('#warningmsg').dialog({
					autoOpen: false,
					title: WARNING,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
							$(this).dialog("close");
						}
					}
				});
				
					// Dialog			
		$('#info').dialog({
					autoOpen: false,
					title: INFO,
					width: 700,
					height: 500,
					modal: true,
					buttons: {
						"Ok": function() {  
							$(this).dialog("close");
						}
					}
				});
				
		$('#editnewfile').dialog({
					autoOpen: false,
					title: EDIT_FILE,
					width: '90%',
					modal: true,
					buttons: {
						SAVE : function() { 
							if (save_new_file())
							$(this).dialog("close");
						}, 
						CANCEL : function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
		// Dialog			
		$('#tagselect').dialog({
					autoOpen: false,
					title: TAG_SELECT,
					width: 400,
					position: "right",
					closeOnEscape: true,
					modal: false,
					buttons: {
						"Cancel" : function() {
							$("#"+actual).blur();
							$(this).dialog("close"); 
						} 
					}
				});
				
		$('#createdir').dialog({
					autoOpen: false,
					title: MAKE_DIR,
					width: 300,
					modal: true,
					buttons: {

						CANCEL : function() { 
							$(this).dialog("close");
						} 
					}
				});
				
   var dialogo = 0;
   


   	


 });


   
   function deselect ()
   {
  				allselected = 0;
  				$(".file_checkbox").attr("checked","");
  				$(".file").css("background-color","");
   }
   
   /**
   * deselect only one file
   * 
   */
   function deselect_one (f)
   {
  				$("#file_checkbox_"+f).attr("checked","");
  				$("#file_"+f).css("background-color","");
   }
   
   function abrir_dialogo (msg)
   {
   		$('#dialogo').dialog('open');
			return false;

   }
   
/**
* loadEditor
* before loading CKeditor, destroys previous instance
*/

function loadEditor(id)
{
    var instance = CKEDITOR.instances[id];
    if(instance)
    {
        CKEDITOR.remove(instance);
    }
 //   CKEDITOR.replace(id);
 				$('#new_file_text').ckeditor();
}

/**
* clearEditor
* clears editor data
*/
function clearEditor (id)
{
    		$("#new_file_idfile").val("");
			$("#new_file_name").val("");
			$("#new_file_text").val("");
			$("#new_file_desc").val("");
			$("#new_file_input_tags").val("");
    var instance = CKEDITOR.instances[id];
    if(instance)
    {
        CKEDITOR.remove(instance);
    }
}


function confirma_y_carga (id)
{	
			
			$('#overwrite').dialog({
					autoOpen: false,
					title: WARNING,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {
							$.ajax({
   							type: "POST",
   							dataType: "json",
   							url: SITE_URL+ "/operation/confirm_overwrite/"+id,
   							complete: function (msg,status){
									carga(id);
   							},
   							success: function (msg) {
   								$("#file_"+msg.file).hide("slow");
   							}						
 							});
							$(this).dialog("close");
						}, 
						"Cancel": function() { 
							$.ajax({
   							type: "POST",
   							dataType: "json",
   							url: SITE_URL+ "/operation/abort_overwrite/"+id,
   							complete: function (msg,status){
									//console.log(status);
   							},
   							success: function (msg) {
   								//alert("aborted"+msg.file);
   							}						
 							});						
							$(this).dialog("close"); 
						} 
					}
				});
				
				$("#overwrite").dialog("open");
			
	
}

/**
* carga
* whne new file is uploaded, it appears
*/
function carga (id)
{	
			$.ajax({
   			type: "POST",
   			dataType: "html",
   			url: SITE_URL+ "/dir/load_file/"+id,
   			success: function(msg){
   				$("#newdirplaceholder").append(msg);
   			},
   			complete: function (msg){

   					$("#newdirplaceholder").append(msg);
   			}
 			});
	
}
	
/*
* comprueba que realmente se ha seleccionado algo
*/
function comprobarSeleccion ()
{
   			var total = $(".file_checkbox:checked");
   			return(total.length);
}

function mostrar(objeto)
{
	var elemento = document.getElementById(objeto);
	elemento.style.display = "block";
	elemento.style.visibility = "visible";
}

function ocultar (objeto) 
{
	var elemento = document.getElementById(objeto);
	elemento.style.display = "none";
	elemento.style.visibility = "hidden";
}

function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}


function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
   							// prevenimos que se vaya al link
  		