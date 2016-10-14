/**
* forms.js
* Core of the form operations
*/
$(document).ready(function() {


	var current_action = "create";
	var issearch = "";
	var order = "";

   $("#role_search").click(function(event) {

	   issearch = $("#role_search_term").val();
	   
  		event.preventDefault();
  		var result = $.ajax({
   			type: "POST",
   			url: "admin/role/search",
   			data: "search_term="+issearch,
   			async: false,
   			success: function(msg){
   					if (msg != "error")
   					{
     						$('#div_role_table').html(msg);
     					}
   			}
 			});
   });
   
	  $('a[class^="role_reorder"]').live('click',function(event)
			  {
		  		event.preventDefault();
		  		fields = $(this).attr('href').split("/");
		  		order = fields[0];
		  		
		  		if (issearch !="")
		  		{
		  			urlfinal = "admin/role/search";
		  			searchp = "&search_term="+issearch;
		  		}
		  		else
		  		{
		  			urlfinal = "admin/role/reorder";
		  			searchp = "";
		  		}
		  				  		
		  		var result = $.ajax({
	   			type: "POST",
	   			url: urlfinal,
	   			data: "order="+fields[0]+"&desc="+fields[1]+"&page="+fields[2]+searchp,
	   			async: false,
	   			success: function(msg){
	   					if (msg != "error")
	   					{
	     						$('#div_role_table').html(msg);
	     					}
	   			}
	 			});
			  });
	  
	  
	  $('#role_pagination > a').live('click',function(event)
			  {
		  		event.preventDefault();
		  		fields = $(this).attr('href').split("/");
		  		
		  		if (issearch !="")
		  		{
		  			urlfinal = "admin/role/search/"+fields[3];
		  			searchp = "&search_term="+issearch;
		  		}
		  		else
		  		{
		  			urlfinal = "admin/role/reorder/"+fields[3];
		  			searchp = "";
		  		}
		  				  		
		  		var result = $.ajax({
	   			type: "POST",
	   			url: urlfinal,
	   			data: "order="+fields[1]+"&desc="+fields[2]+"&page="+fields[3]+searchp,
	   			async: false,
	   			success: function(msg){
	   					if (msg != "error")
	   					{
	     						$('#div_role_table').html(msg);
	     					}
	   			}
	 			});
			  });
   	
   $("#form_role_create").click(function(event) {
  		id = 0;
		current_action = "create";
  		event.preventDefault();
   	var result = $.ajax({
   			type: "POST",
   			url: "admin/role/get_form",
   			data: "id="+id,
   			async: false,
   			success: function(msg){
     						$('#formdialog').html(msg);
     						$('#formdialog').dialog('open');
   			}
 			});
   });


	  $('a[class^="role_record_update_"]').live('click',function(event)
	  {
  		id = $(this).attr('href');

  		current_action = "update";
		event.preventDefault();
		var result = $.ajax({
   			type: "POST",
   			url: "admin/role/get_form/"+id+"/update",
   			async: false,
   			success: function(msg){
     					$('#formdialog').html(msg);
     		         	$('#formdialog').dialog('open');
   					}
 			});
	  });






	  $('a[class^="role_record_delete_"]').live('click',function(event)
	  {
  		id = $(this).attr('href');

  		event.preventDefault();

		if (window.confirm("¿Estás seguro?"))
		{
			var result = $.ajax({
  				 				type: "POST",
   							url: "admin/role/delete/"+id,
   							async: false,
   							success: function(msg){
									if (msg == "{'result':'ok'}")
									{
										$("#tr_role_"+id).hide("slow");
									}
									else
									{
     								$('#warningmsg').html(msg);
     		         				$('#warningmsg').dialog('open');
     		         			}						     							
   							}
 							});
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
				
	
		$('#formdialog').dialog({
					autoOpen: false,
					title: WARNING,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
					   	var result = $.ajax({
   						type: "POST",
   						url: "admin/role/"+current_action,
   						data: "id="+$("#id").val()+"&name="+$("#name").val()+"&description="+$("#description").val()+"&suspendedon="+$("#suspendedon").val(),
   						async: false,
   						success: function(msg){
   							res = msg.split("|||");
     		         		$('#formdialog').dialog('close');
     		         		if (res[0]=="error")
     		         		{
        							$('#formdialog').html(res[1]);
	     		         		$('#formdialog').dialog('open');
     		         		}
     		         		else
     		         		{
     		         			if (current_action=="create")
     		         				$('#role_table').append(res[1]);
     		         			else
     		         				$("#tr_role_"+$("#id").val()).html(res[1]);
     		         				
     		         			$('#formdialog').dialog('close');     		         			
     		         		}
   							}
 							});
						}, 
						CANCEL : function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
				
		$('#confirmar').dialog({
					autoOpen: false,
					title: WARNING,
					width: 400,
					modal: true,
					buttons: {
						"Ok": function() {  
					   	var result = $.ajax({
  				 				type: "POST",
   							url: "admin/role/delete/"+id,
   							async: false,
   							success: function(msg){
									if (msg == "{'result':'ok'}")
										alert("ok");
									else
										alert("error: " + msg);     							     							
   							}
 							});
						}, 
						CANCEL : function() { 
							$(this).dialog("close"); 
						} 
					}
				});

 });


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