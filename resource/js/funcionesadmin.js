
/**
* funciones.js
* Core of the file operations
*/
$(document).ready(function() {


   
   
   /***************************************************
   * STATUS *********************************************
   *******************************************************/
      
    $("a.filestatus").live('click',function(event) {

   			event.preventDefault();
   			deselect();
   			var id = $(this).attr("href");
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/get_status/",
   				data: "id="+id,
   				success: function(msg){
					     $("#warningmsg").html(msg);
     					  $("#warningmsg").dialog('open');
   				}
	 			});

        		
   });
   
   
   $("a.filestatus_change").live('click',function(event) {

   			event.preventDefault();
   			deselect();
   			var hrefdata = $(this).attr("href").split("_");
   			var id = hrefdata[0];
   			var newstatus = hrefdata[1]; 
   			var recursive = ($("#recursivechange").attr('checked'))?1:0;

   			
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/change_status/",
   				data: "id="+id+"&status="+newstatus+"&recursive="+recursive,
   				success: function(msg){
   					if (msg != "-1")
   					{
     						rvalue = msg.split(":");
   						$("#file_"+ id).attr("class","file status_"+rvalue[0]);
   						$("#warningmsg").dialog('close');
   					}
   				}
	 			});

        		
   });
   

   /*************************************
      * Cambiar status
      **************************************/
      $('.statuslink').click(function(event) {
         	event.preventDefault();
         	if (!comprobarSeleccion())
         	{
  					$('#warningmsg').html(MSG_NOT_BLANK);
	         	$('#warningmsg').dialog('open');
         		return;
         	}
         	id = -1;
   		  	$.ajax({
   				type: "POST",
   				url: SITE_URL+ "/operation/get_status/1",
   				data: "id="+id,
   				success: function(msg){
   					$( "#warningmsg" ).dialog( "option", "buttons", [
			   	 
    						{	
    						text: "Cancel", 
							click: function() { 
								$(this).dialog("close"); 
							} 
						}	
						] );
						$("#warningmsg").html(msg);
						$("#warningmsg").dialog("open");
   				}
	 			});
	 			

		});
		
       $("a.filestatus_change_mul").live('click',function(event) {
   			event.preventDefault();
   			
   			var hrefdata = $(this).attr("href").split("_");
   			var id = hrefdata[0];
   			var newstatus = hrefdata[1]; 
   			var recursive = ($("#recursivechange").attr('checked'))?1:0;
   			
				var total = $(".file_checkbox:checked").map(function() {
					return $(this).val();
					}).get().join();

   							// prevenimos que se vaya al link
  			$.ajax({
   			type: "POST",
   			url: SITE_URL+ "/operation/change_status/",
   			data: "id="+total+"&status="+newstatus+"&recursive="+recursive,
   			success: function(msg){
     				if (msg != "-1")
     				{
     					rvalue = msg.split(":");

						if (rvalue[1]!="")
						{
 							changed = rvalue[1].split(",");
  							for (i=0;i<changed.length;i++)
  							{
						  		   $("#file_"+ changed[i]).attr("class","file status_"+rvalue[0]);
  							} 
  							deselect();
  						}
  						$("#warningmsg").dialog('close');
     					//
         		}
         		
   			}
 			});
      		
    });  
   
 
 
   /***************************************************
   * OWNERS *********************************************
   *******************************************************/
      
    $("a.fileowners").live('click',function(event) {

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
   						var fileuserowner = $("#fileuserowner").val(); 
   						var filegroupowner = $("#filegroupowner").val(); 
   			
   		  				$.ajax({
   							type: "POST",
   							url: SITE_URL+ "/operation/change_owners/",
   							data: "id="+id+"&fileuserowner="+fileuserowner+"&filegroupowner="+filegroupowner+"&recursive="+recursive,
   							success: function(msg){
   							var permstring = "";
   							if (msg != "-1")
   							{
  // 								alert(msg);
     								rvalue = msg.split(":");
     								
     								
									$("#filecreator_"+rvalue[4]).html(rvalue[1]);   								
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
   				url: SITE_URL+ "/operation/get_owners/",
   				data: "id="+id,
   				success: function(msg){
					     $("#permissions").html(msg);
     					  $("#permissions").dialog('open');
   				}
	 			});   			
   			


        		
   });
   

   

   /*************************************
      * Cambiar owners
      **************************************/
      $('.ownerslink').click(function(event) {
         	event.preventDefault();

         	if (!comprobarSeleccion())
         	{
  					$('#warningmsg').html(MSG_NOT_BLANK);
	         	$('#warningmsg').dialog('open');
         		return;
         	}
         	id = -1;

   			var recursive = ($("#recursivechange").attr('checked'))?1:0;
   			
				var total = $(".file_checkbox:checked").map(function() {
					return $(this).val();
					}).get().join();
					
			//Dialog			
				$('#permissions').dialog({
					autoOpen: false,
					title: DIR_LIST,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
							var id = total;
   						var recursive = ($("#recursivechange").attr('checked'))?1:0;
   						var fileuserowner = $("#fileuserowner").val(); 
   						var filegroupowner = $("#filegroupowner").val(); 
   			
   		  				$.ajax({
   							type: "POST",
   							url: SITE_URL+ "/operation/change_owners/",
   							data: "id="+total+"&fileuserowner="+fileuserowner+"&filegroupowner="+filegroupowner+"&recursive="+recursive,
   							success: function(msg){
   							var permstring = "";
   							
   							if (msg != "-1")
   							{
   								
   								rvalue = msg.split(":");
			
									if (rvalue[4]!="")
									{
 										changed = rvalue[4].split(",");
  										for (i=0;i<changed.length;i++)
  										{
  											$("#filecreator_"+changed[i]).html(rvalue[1]);
  										} 
  										deselect();
  									}
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
   				url: SITE_URL+ "/operation/get_owners/",
   				data: "id="+id,
   				success: function(msg){
					     $("#permissions").html(msg);
     					  $("#permissions").dialog('open');
   				}
	 			}); 
	 			

		});



 });

