/**
*Procedimientos y funciones de uso general
*
*/
var nav4 = window.event ? true : false;
var exprcontacto = /\[contacto\]\[(.*)\]/;
var exprmarca = /\[anunciante\]\[(.*)\]/;

jQuery( document ).ready(function(){


		$("#IDPais").change(function () {
				var IDPais = $(this).val();
				var LENGUAJE = 1;
				$.ajax({
					   type: "POST",
					   url: "ajax/CargaDepartamentos.php",
					   data: "IDPais="+IDPais,
					   dataType: "json",
					   success: function(msg){
							$("#IDDepartamento").removeOption(/./);							
							$("#IDDepartamento").addOption(msg, false);
					   }
				});
			});
			
			$("#IDDepartamento").change(function () {
				var IDDepartamento = $(this).val();
				var LENGUAJE = 1;
				$.ajax({
					   type: "POST",
					   url: "ajax/CargaCiudadDepto.php",
					   data: "IDDepartamento="+IDDepartamento,
					   dataType: "json",
					   success: function(msg){
							$("#IDCiudad").removeOption(/./);							
							$("#IDCiudad").addOption(msg, false);
					   }
				});
			});
			
			
		$("#IDPaisSocio").change(function () {
				var IDPais = $(this).val();
				var LENGUAJE = 1;
				$.ajax({
					   type: "POST",
					   url: "ajax/CargaDepartamentos.php",
					   data: "IDPais="+IDPais,
					   dataType: "json",
					   success: function(msg){
							$("#IDDepartamentoSocio").removeOption(/./);							
							$("#IDDepartamentoSocio").addOption(msg, false);
					   }
				});
			});
			
			$("#IDDepartamentoSocio").change(function () {
				var IDDepartamento = $(this).val();
				var LENGUAJE = 1;
				$.ajax({
					   type: "POST",
					   url: "ajax/CargaCiudadDepto.php",
					   data: "IDDepartamento="+IDDepartamento,
					   dataType: "json",
					   success: function(msg){
							$("#IDCiudadSocio").removeOption(/./);							
							$("#IDCiudadSocio").addOption(msg, false);
					   }
				});
			});		
			
			

	/*
	 * Nuestro calendario 
	 */
	Date.firstDayOfWeek = 7;
	Date.format = 'yyyy-mm-dd';

	  $( ".calendar" ).datepicker({
      showOn: "button",
      buttonImage: "jscript/imagescalendar/cal.gif",
      buttonImageOnly: true,
	  changeMonth: true,	 
	  dateFormat: 'yy-mm-dd',
	  yearRange: "1920:2025",
      changeYear: true	  
	  
    });


	
	/*
	 * Intervalo de fechas
	 */
	$( '#FechaInicio' ).bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if ( d ) 
			{
				d = new Date(d);
				$('#FechaFin').dpSetStartDate( d.addDays(1).asString() );
			}
		}
	);
	
	$( '#FechaFin' ).bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) 
			{
				d = new Date( d );
				$( '#FechaInicio' ).dpSetEndDate( d.addDays( -1 ).asString() );
			}
		}
	);
	
	$('.calendar').click(function(){
		$(this).siblings("a").click();
	});
	
	
	$('.confirma_eliminacion').click(function(){
		if(confirm("Esta seguro que desea eliminar el registro?")){
			return true;	
		}
		else{
			return false;		
		}
		
	});
	
	
	/*
	 * Toggle de los shortcuts
	 */
	$( "#hidemenuleft" ).click(function(){
	    $("#shortcuts").toggle();
		$(".shortcuts").width( "10px" );
	});
	/*
	 * denegacion de campos
	 */	 
	 //solo numeros
	$( "input.onlynumber" ).keypress(function( evt ){
		var key = nav4 ? evt.keyCode : evt.which;
		return /[\d]/.test( String.fromCharCode( key ) );
	});
	
	//monetario
	$( "input.money" ).keyup(function(){
		this.value = number_format( this.value );
	});
	
	//solo letras
	$( "input.onlyword" ).keypress(function(){
		var key = nav4 ? evt.keyCode : evt.which;
		return /[\w]/.test( String.fromCharCode( key ) );
	});
	
	$( "form.formvalida" ).submit(function(){ return EvaluaReg( this ) });
	$( "form.formvalidaregistro" ).submit(function(){ return EvaluaRegConClave( this ) });
	


    $("input[name='ApruebaComentario']").click(function(){		
		var valor_opcion=$(this).val();
		var ID=$(this).attr("id");
		var contador=$(this).attr("contextmenu");;

		jQuery.ajax( {
				"type" : "POST",
				"data" : { "ID" : $(this).attr("id") , "Tabla" : $(this).attr("alt") , "Valor" : $(this).val()  },
				"dataType" : "json",
				"url" : "ajax/actualiza_comentario.async.php" ,
				
				"beforeSend" : function(){
					addNotify();
				},
				 
				"success" : function( data ){
					alert("Estado modificado");
					if ( valor_opcion=="S"){
						$( "#notifcambio"+contador ).html( "Aprobado" );
					}
					else{
						if(valor_opcion=="N"){
							$( "#notifcambio"+contador ).html( "No Aprobado" );
						}
					}
				}
			});		
		
		
		
    });

	
	$(".activar_servicio").click(function(){		
		var IDServicioMaestro=$(this).attr("rel");
		var IDClub=$(this).attr("title");
		
		jQuery.ajax( {
				"type" : "POST",
				"data" : { "IDServicioMaestro" : IDServicioMaestro , "IDClub" : IDClub   },
				"dataType" : "json",
				"url" : "ajax/actualiza_servicio.async.php" ,
				"beforeSend" : function(){
					addNotify();
				},
				 
				"success" : function( data ){
					if(data=="S"){
						alert("Servicio activado con exito, continue con la configuracion por favor");
						window.location.href="?mod=Servicio&action=edit&IDServicioMaestro="+IDServicioMaestro;
					}
					else{
						alert("Servicio inactivado con exito");	
						window.location.href="?mod=HomeClub&action=edit&id="+IDClub;
					}
					
				}
			});		
		
    });
	
	
	
	$(".guardar_fotogaleria").click(function(){		
		var ID=$(this).attr("alt");
		var Orden=$("#Orden"+ID).val();
		var Descripcion=$("#Descripcion"+ID).val();
		
		jQuery.ajax( {
				"type" : "POST",
				"data" : { "ID" : ID , "Orden" : Orden , "Descripcion" : Descripcion  },
				"dataType" : "json",
				"url" : "ajax/actualiza_foto.async.php" ,
				"beforeSend" : function(){
					addNotify();
				},
				 
				"success" : function( data ){
					alert("Datos guardados con exito");
				}
			});		
		
    });
	
	


    $("input[name='ApruebaContenido']").click(function(){		
		var valor_opcion=$(this).val();
		var ID=$(this).attr("id");
		var contador=$(this).attr("contextmenu");;

		jQuery.ajax( {
				"type" : "POST",
				"data" : { "ID" : $(this).attr("id") , "Tabla" : $(this).attr("alt") , "Valor" : $(this).val()  },
				"dataType" : "json",
				"url" : "ajax/actualiza_estado.async.php" ,
				
				"beforeSend" : function(){
					addNotify();
				},
				 
				"success" : function( data ){
					alert("Estado modificado");
					if ( valor_opcion=="S"){
						$( "#notifcambio"+contador ).html( "Aprobado" );
					}
					else{
						if(valor_opcion=="N"){
							$( "#notifcambio"+contador ).html( "No Aprobado" );
						}
					}
				}
			});		
		
		
		
    });
	
	
	if( $.tabs )
		$( '#tabsform' ).tabs();
		

	$( "#contactos form#frmcontacto" ).submit(function(){
		var fields = $( this ).find( ".mandatory" ).get();
		
		var permiteayax = 1 ;
		
		for( var i = 0 ; i < fields.length ; i++ )
		{
			if( $( fields[i] ).val() == "" )
				permiteayax = 2;
		}
		if(permiteayax == 1 )
			return guardaContactoAnunciante( $( this ).attr( "rev" ) );
	});
	
	
	
	
	
	preparaContactoAnunciante();
	
	$( "select[class*=dependent]" ).each(function(){
		
		var qry = $( this ).attr( "class" ).match( /dependent\[(.*)\]/i );
		var _this =  this;
		
		$( "select#" + qry[1] ).change(function(){
			
			var idred = $( this ).val();
			$( _this ).find( "option[value!='']" ).remove();
			
			for( var id in Red[ idred ] )
				if( parseInt(id) )	
					$( _this ).append( '<option value="' + id + '">' + Red[ idred ][id] + '</option');
		});
		
	});
	
	
	$( "a[href^='#'].slide" ).click(function(){
		var elem2show = $( this ).attr( "href" ).substring( 1 );
		
		$( "#" + elem2show ).slideToggle( "fast" );		
		return false;
	});
	
	$( "input.generarpropuesta" ).click(function(){
		$( "input#action" ).val( "generarpropuesta" );
		return true;
	});
	
	
	//Para agregar agencias a los clientes listagencias listagenciascliente
	$( "#clienteagencia #listagencias a" ).click(function(){
		return agregaAgenciaCliente( $( this ).attr( "title" ), $( this ).attr( "rel" ) );
	});
	
	
	//Para eliminar agencias a los clientes listagencias listagenciascliente
	$( "#clienteagencia #listagenciascliente a" ).click(function(){
		return eliminaAgenciaCliente( $( this ).attr( "title" ), $( this ).attr( "rel" ) );
	});
	
	//Para eliminar agencias a los clientes listagencias listagenciascliente
	$( "#EliminarItemsPropuestas" ).click(function(){
		return eliminaItemPropuesta( );
	});


	$( "#marcas form#frmmarcas" ).submit(function(){
		var fields = $( this ).find( ".mandatory" ).get();
		
		var permiteayax = 1 ;
		
		for( var i = 0 ; i < fields.length ; i++ )
		{
			if( $( fields[i] ).val() == "" )
				permiteayax = 2;
		}
		if(permiteayax == 1)
			return guardaMarca( $( this ).attr( "rev" ) );
	});
	
	
	
	preparaMarca();
	
	$( ".linkPermisos" ).click(function(){
		console.log( "clic" );
		$( "." + $( this ).attr( "rel" ) ).attr("checked","checked");;
	});



	$( "#linkChecks" ).click(function(){
		$( "." + $( this ).attr( "rel" ) ).attr("checked","checked");;
	});
	
		$("#ArbolSecciones").treeview();

	
	$(".delFile").click(function(){			
			var param = $(this).attr("name");
			var conf = confirm("Desea eliminar este archivo?");
			if(conf == true){
				$.ajax({
				   type: "GET",
				   url: "index.php",
				   data: param,
				   success: function(msg){
				   	$(".operacionfile").empty();
				     return false;
				   }
				 });
			 }
			return false;
		});
	
});






function addNotify()
{
	if( !window.contactonotify )
	{
		window.contactonotify = $( '<div id="volatilnotif" class="mensaje info">Obteniendo datos...</div>' )
					.css({ 
							"position":"absolute",
							"top" : "300px",
							"left" : "450px",
							"width" : "300px"
				}).get();
				
		$( document.body ).append( window.contactonotify );
	}
	else
		$( window.contactonotify ).show();
	
	setTimeout( function(){$( "#volatilnotif" ).fadeOut( "slow" )} , 600 );
		
}

function eliminaItemPropuesta()
{
	var val = [];
	var params = {};
	var tmpname = "";
	var myfield;
	params[ "context" ] = "";
	$(':checkbox:checked').each(function(i){
    	val[i] = $(this).val();
		params[ "context" ] += $( this ).attr( "title" ) + "|" + $( this ).attr( "name" ) + "+";
    });	
	
	
	params[ "action" ] = "del";
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/propuesta/propuesta.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			
			$(':checkbox:checked').each(function(i){
					$(this).parent().parent().remove();
			});	
			
			$( "#volatilnotif" ).html( "Items Borrados!" );

			setTimeout( function(){
				$( "#volatilnotif" ).fadeOut( "slow" );
			} , 700);

		}
	});
	return false;	
}


function preparaContactoAnunciante()
{
	$( "#contactos a.editcontact" ).click(function(){
		return contactoAnunciante( $( this ) , "read" , $( this ).attr( "rev" ) );
	});
	
	$( "#contactos a.deletecontact" ).click(function(){
		return contactoAnunciante( $( this ) , "delete" , $( this ).attr( "rev" ) );
	});
}

function preparaMarca()
{
	$( "#marcas a.editmarca" ).click(function(){
		return leerMarca( $( this ) , "read" , $( this ).attr( "rev" ) );
	});
	
	$( "#marcas a.deletemarca" ).click(function(){
		return leerMarca( $( this ) , "delete" , $( this ).attr( "rev" ) );
	});
}

function leerMarca( obj , statuss , context )
{
	jQuery.ajax( {
		"type" : "POST",
		"data" : { "IDAnunciante" : obj.attr( "rel" ) , "marcaestado" : statuss , "context" : context },
		"dataType" : "json",
		"url" : "includes/cliente/marca.async.php" ,
		
		"beforeSend" : function(){
			
			$( "#volatilnotif" ).html( "Obteniendo Informaci&oacute;n de la Marca" );
			
			$('html,body').animate({scrollTop: 0 }, 500);
			
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column;
			
			if( !data.deleteok )
			{
				$( "div#marcas input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( data[ _this.attr( "id" ).match( exprmarca )[1] ] );
					else
					{
						if( _this.attr( "id" ) == "marcaestado" )
							_this.val( "update" );
						else if( _this.attr( "type" ) == "hidden" )
							_this.val( data[ _this.attr( "id" ) ] );
					}
					
				});
			}
			else
			{
				$( "div#marcas input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( "" );
					else
					{
						if( _this.attr( "id" ) == "marcaestado" )
							_this.val( "insert" );
					}
				});
				
				$( "#listamarcas a[rel="+ data.IDAnunciante + "]" ).parent().parent().remove();
				$( "#volatilnotif" ).html( "Marca Borrada!" );
			}
			
			setTimeout( function(){$( "#volatilnotif" ).fadeOut( "slow" )} , 600 );
		}
	});
	
	return false;
}


function contactoAnunciante( obj , statuss , context )
{
	jQuery.ajax( {
		"type" : "POST",
		"data" : { "IDContacto" : obj.attr( "rel" ) , "contactoestado" : statuss , "context" : context },
		"dataType" : "json",
		"url" : "includes/anunciante/contacto.async.php" ,
		
		"beforeSend" : function(){
			
			$( "#volatilnotif" ).html( "Obteniendo Informaci&oacute;n del Contacto..." );
			
			$('html,body').animate({scrollTop: 0 }, 500);
			
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column;
			
			if( !data.deleteok )
			{
				$( "div#contactos input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( data[ _this.attr( "id" ).match( exprcontacto )[1] ] );
					else
					{
						if( _this.attr( "id" ) == "contactoestado" )
							_this.val( "update" );
						else if( _this.attr( "type" ) == "hidden" )
							_this.val( data[ _this.attr( "id" ) ] );
					}
					
				});
			}
			else
			{
				$( "div#contactos input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( "" );
					else
					{
						if( _this.attr( "id" ) == "contactoestado" )
							_this.val( "insert" );
					}
				});
				
				$( "#listacontactosanunciante a[rel="+ data.IDContacto + "]" ).parent().parent().remove();
				$( "#volatilnotif" ).html( "Contacto Borrado!" );
			}
			
			setTimeout( function(){$( "#volatilnotif" ).fadeOut( "slow" )} , 600 );
		}
	});
	
	return false;
}

function guardaContactoAnunciante( context )
{
	var myforminputs = $( "div#contactos input, div#contactos select" ).get();
	var params = {};
	var tmpname = "";
	var myfield;

	for( var i = 0 ; i < myforminputs.length ; i++ )
	{
		myfield = $( myforminputs[i] );

		if( myfield.attr( "type" ) != "submit" )
		{
			if( tmpname = myfield.attr( "id" ).match( exprcontacto ) )
				params[ tmpname[1] ] = myfield.val();
			else
				params[ myfield.attr( "id" ) ] = myfield.val();
			
			if( myfield.attr( "type" ) != "hidden" ) myfield.val( "" );
				
		}
	}	
	
	params[ "ID" + context ] = $( "#frm input#ID" ).val();
	
	/*if( $( "#contactos form#frmcontacto" ).attr( "rev" ) == "Anunciante" )
		params[ "IDAnunciante" ] = $( "#frm input#ID" ).val();
	else
	{
		if( $( "#contactos form#frmcontacto" ).attr( "rev" ) == "Anunciante" )
			params[ "IDAgencia" ] = $( "#frm input#ID" ).val();
	}*/
	
	params[ "context" ] = context;
	
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/anunciante/contacto.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			if( $( "div#contactos input#contactoestado" ).val() == "update" )
			{
				$( "#listacontactosanunciante a[rel="+ data.IDContacto + "]" ).parent().parent().remove();
				$( "div#contactos input#contactoestado" ).val( "insert" )
			}
			
			var html = '<tr>';
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDContacto + '" class="editcontact" rev="' + context + '"><img src="images/edit.png" border="0" /></a></td>';
			html += '<td>' +  data.Apellido + " " + data.Nombre + '</td>';
			html += '<td>' +  data.Direccion + '</td>';
			html += '<td>' +  data.Telefono + '</td>';
			html += '<td>' +  data.Ciudad + '</td>';
			html += '<td>' +  data.Email + '</td>';
			html += '<td>' +  data.Cargo + '</td>';
			html += '<td>' +  data.Celular + '</td>'; 
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDContacto + '" class="deletecontact" rev="' + context + '"><img src="images/trash.png" border="0" /></a></td>';
			html += '</tr>';
			
			$( "#listacontactosanunciante" ).append( html );
			
			preparaContactoAnunciante();
			
			$( "#volatilnotif" ).html( "Contacto Guardado!!" );
			
			setTimeout( function(){
				$( "#volatilnotif" ).fadeOut( "slow" );
			} , 700);
		}
	});
	return false;
}


function guardaMarca( context )
{
	var myforminputs = $( "div#marcas input" ).get();
	var params = {};
	var tmpname = "";
	var myfield;
	var agencia = $("div#marcas select option:selected").val();
	var txtagencia = $("div#marcas select option:selected").text();

	for( var i = 0 ; i < myforminputs.length ; i++ )
	{
		myfield = $( myforminputs[i] );
		
		if( myfield.attr( "type" ) != "submit" )
		{
			
			if( tmpname = myfield.attr( "id" ).match( exprmarca ) )
				params[ tmpname[1] ] = myfield.val();
			else
				params[ myfield.attr( "id" ) ] = myfield.val();
			
			if( myfield.attr( "type" ) != "hidden" ) myfield.val( "" );
				
		}
	}	
	
	params[ "IDAgencia" ] = agencia;
	params[ "ID" + context ] = $( "#frm input#ID" ).val();
	

	
	params[ "context" ] = context;
	
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/cliente/marca.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			if( $( "div#marcas input#marcaestado" ).val() == "update" )
			{
				$( "#listamarcas a[rel="+ data.IDAnunciante + "]" ).parent().parent().remove();
				$( "div#marcas input#marcaestado" ).val( "insert" )
			}
			
			var html = '<tr>';
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDAnunciante + '" class="editmarca" rev="' + context + '"><img src="images/edit.png" border="0" /></a></td>';
			html += '<td>' +  txtagencia + '</td>';
			html += '<td>' +  data.Nombre + '</td>';
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDAnunciante + '" class="deletemarca" rev="' + context + '"><img src="images/trash.png" border="0" /></a></td>';
			html += '</tr>';
			
			$( "#listamarcas" ).append( html );
			
			preparaMarca();
			
			$( "#volatilnotif" ).html( "Marca Guardado!!" );
			
			setTimeout( function(){
				$( "#volatilnotif" ).fadeOut( "slow" );
			} , 700);
		}
	});
	return false;
}


function agregaAgenciaCliente( idagencia, idcliente )
{
	//var myforminputs = $( "div#contactos input" ).get();
	var params = {};
	var tmpname = "";
	var myfield;

	params[ "IDAgencia" ] = idagencia;
	params[ "IDCliente" ] = idcliente;
	params[ "action" ] = "insert";
	
	
	//params[ "context" ] = context;
	
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/cliente/clienteagencia.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			if( data.registro )
			{
			
				var html = '<p class="row1">';
				html += '<A href="javascript:void(0)" title="' + data.IDAgencia + '" rel="' + idcliente + '" onclick="eliminaAgenciaCliente(' + data.IDAgencia + ' , ' + idcliente + ')">';
				html += '<img src="images/trash.png" border="0" hspace="5">';
				html += '</A>';
				html += data.Nombre;
				html += '</p>';
							
				$( "#listagenciascliente" ).append( html );
				
				//preparaContactoAnunciante();
				
				$( "#volatilnotif" ).html( "Agencia Agregada!!" );
				
				setTimeout( function(){
					$( "#volatilnotif" ).fadeOut( "slow" );
				} , 700);
			}
			else
			{
				$( "#volatilnotif" ).html( "Registro ya existe!!" );
				
				setTimeout( function(){
					$( "#volatilnotif" ).fadeOut( "slow" );
				} , 700);
			}
		}
	});
	return false;
}


function eliminaAgenciaCliente( idagencia, idcliente )
{
	//var myforminputs = $( "div#contactos input" ).get();
	var params = {};
	var tmpname = "";
	var myfield;

	params[ "IDAgencia" ] = idagencia;
	params[ "IDCliente" ] = idcliente;
	params[ "action" ] = "del";
	
	//params[ "context" ] = context;
	
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/cliente/clienteagencia.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			$( "#listagenciascliente a[title="+ idagencia + "]" ).parent().remove();
			$( "#volatilnotif" ).html( "Agencia Borrada!" );

			setTimeout( function(){
				$( "#volatilnotif" ).fadeOut( "slow" );
			} , 700);

		}
	});
	return false;
}



function subTotalPlan()
{
	var _suma = [], _total = 0;

	$( "input.subtotal", this.parentNode.parentNode ).each(function(){
		_suma.push( parseFloat( getNum( $( this ).val() ) ) );
	});
	
	_total = ( _suma[0] * _suma[1]) / 1000;
	
	if( !isNaN(_total) )	
		$( "input[id*='param[detalle][SubTotal]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}


function totalPlan()
{
	var _total = 0;
	var subtotal = parseFloat( getNum( $( "input[id*='param[detalle][SubTotal]']" , this.parentNode.parentNode ).val() ) );
	var descuento = parseFloat( getNum( $( "input[id*='param[detalle][Descuento]']" , this.parentNode.parentNode ).val() ) );
	
	if( descuento )
		_total = subtotal - ( (subtotal * descuento) / 100 );
	else
		_total = subtotal;
		
	if( !isNaN(_total) )	
		$( "input[id*='param[detalle][Total]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}

function visitasEsperadasPlan()
{
	var _total = 0;
	
	var efectividad = parseFloat( getNum( $( "input[id*='param[detalle][Efectividad]']" , this.parentNode.parentNode ).val() ) );
	
	var cantidad = parseFloat( getNum( $( "input[id*='param[detalle][Cantidad]']" , this.parentNode.parentNode ).val() ) );
	
	_total = cantidad * efectividad;
	
	if( !isNaN(_total) )
		$( "input[id*='param[detalle][VisitasEsperadas]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}


function costoxvisita()
{
	var _total = 0;
	var totalsitio = parseFloat( getNum( $( "input[id*='param[detalle][Total]']" , this.parentNode.parentNode ).val() ) );
	var visitasesperadas = parseFloat( getNum( $( "input[id*='param[detalle][VisitasEsperadas]']" , this.parentNode.parentNode ).val() ) );
	
	_total = totalsitio / visitasesperadas;
	
	if( !isNaN(_total) )
		$( "input[id*='param[detalle][CostoxVisita]']" , this.parentNode.parentNode ).val( _total );
	return true;
}


function checkall( obj , selector )
{
	var checked_status = obj.checked;
	$( selector ).each(function(){
		this.checked = checked_status;
	});
}

function number_format( num )
{
	var tmpnum = [], 
	cont = 1 , 
	charlen = 0;
	
	var fparts = [];
	
	num = getNum( num ).toString();
	
	if( num.indexOf(".") != -1 )
	{
		fparts = num.split(".");
		num = fparts[0];	
	}

	fparts = fparts[1] || fparts[1] == "" ? "." + fparts[1] : "";
		
	charlen = num.length - 1;
	
	for( var i = charlen ; i >= 0 ; i-- )
	{
		tmpnum.unshift( num.charAt( i ) );
			
		if( cont == 3 && i != 0 )
		{
			cont = 1;
			tmpnum.unshift( "," );
			continue;
		}
		cont++;
	}
	
	tmpnum.push( fparts );		
	return tmpnum.join("");
}


function acceptNum(evt){ 
	var key = !nav4 ? evt.which : evt.keyCode; 
	return (key <= 13 || (key >= 46 && key <= 57));
}

function getNum(strNum)
{
	//console.log(strNum);
	num = strNum.toString().replace(/\$|\,/g,'');
	return isNaN( num ) ? 0 : num;
}