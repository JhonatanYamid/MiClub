/**
*Procedimientos y funciones de uso general
*
*/
var nav4 = window.event ? true : false;
jQuery( document ).ready(function(){

	

	$(".btnEnviar").click(function(){
		var form = $(this).attr("rel");
		$( "#" + form ).submit();
	});
	
	
	$("#masfotos").click(function () {
    	  $("#CargarImg").toggle("slow");
    });
	
	$( "#frmUpdateInvitado" ).submit(function(){		
			var detalle;		
			$( "#InvitadoSeleccion" ).val("");
			$("#SocioInvitado option").map(function(i, el) {
				detalle = $( "#InvitadoSeleccion" ).val()+$(el).val()+"|||";				
				$( "#InvitadoSeleccion" ).val(detalle);	
				//$("#InvitadoSeleccion").val($(el).val());
				//$(el).attr('selected', 'selected');
			});
			
			return true;
		
	});
	
	$( "#frmDeleteReserva" ).submit(function(){		
			var detalle;		
			var razon = $( "#RazonCancelacion" ).val();
			var IDReserva = $( "#IDReservaGeneral" ).val();
			if(razon==""){
				alert("Debe digitar la razon de la cancelacion");
				return false;	
			}
			else{
				if (confirm("Esta seguro que desea cancelar la reserva?")){		
					jQuery.ajax( {
						"type" : "POST",
						"data" : { "IDReservaGeneral" : IDReserva,"Razon": razon },
						"dataType" : "json",
						"url" : "includes/async/cancela_reserva.async.php" ,						 
						"success" : function( data ){
								alert("Reserva Cancelada con exito");
								return false;
								//$("#grid-table<?=$key_elemento?>").trigger("reloadGrid");
								return false;
						}
					});		
				}
				else{
					return false;	
				}
				
			}
			return false;		
	});
	
	$( "#frmReservaGeneral" ).submit(function(){		
			var detalle;		
			$( "#InvitadoSeleccion" ).val("");
			$("#SocioInvitado option").map(function(i, el) {
				detalle = $( "#InvitadoSeleccion" ).val()+$(el).val()+"|||";				
				$( "#InvitadoSeleccion" ).val(detalle);	
				//$("#InvitadoSeleccion").val($(el).val());
				//$(el).attr('selected', 'selected');
			});
			
			return true;
		
	});
	
	
	
	
	
	

	$(".btnShow").click(function(){
		var element = $(this).attr("rel");
		$( "#" + element ).removeClass("hide");
		$( "#" + element ).show(300);
		return false;
	});

	
	preparaform();
	

});

function preparaform(){

	$( "form.formvalida" ).submit(function(){
		
		$(".money").each(function(){
		  $(this).val( getNum( $( this ).val() ) );
		});

		return EvaluaReg( this );
	});
	
	
	
	
	


	$( ".calendar" ).datepicker( { 
		format: "yyyy-mm-dd",
	} );
	
	
		var dateToday = new Date();
		 $(".calendariohoy").datepicker({			
			 format: "yyyy-mm-dd",
			 startDate: new Date(),
			 endDate: '+2d'
			 
		});
	   
	

	$('.calendar_reservas').datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function(e) {
        var fecha = e.format();
        
        var grillas = $("#grillas").val();
        var array_grillas = [];
        array_grillas = grillas.split(',');

        $('.calendar_reservas').datepicker('hide');

        

        $.each( array_grillas, function( index, value ) {
			var grilla = "#grid-table" + value;
			$( grilla ).jqGrid('setGridParam', { 
				postData: {"fecha":fecha }
			}).trigger('reloadGrid'); 

		});



		$("#contentFechaActual").html( fecha );
		
		
		var Fecha = fecha;		
		var IDClubSeleccionado = $( "#IDClubSeleccionado" ).val();		
		var IDServicioSeleccionado = $( "#IDServicioSeleccionado").val();				
		$("#cargaexterna").html("Cargando...");			
		$.post("view_reserva_app.php", {fecha: Fecha, IDClub: IDClubSeleccionado, IDServicio: IDServicioSeleccionado}, function(htmlexterno){
		  $("#cargaexterna").html(htmlexterno);
		});


    });


    $('.calendar_nueva_reservas').datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function(e) {
        var fecha = e.format();
        var ids = $("#ids").val();
        
        location.href = "reservas_admin.php?ids=" + ids + "&action=add&fecha=" + fecha;
       


    });


	$(".guardar_fotogaleria").click(function(){
		var identificador_foto = $(this).attr("alt");
		var orden_foto = $("#Orden"+identificador_foto).val();
		var descripcion_foto = $("#Descripcion"+identificador_foto).val();		
		//location.href = link;
		jQuery.ajax( {
				"type" : "POST",
				"data" : { "ID" : identificador_foto , "Orden" : orden_foto, "Descripcion" : descripcion_foto   },
				"dataType" : "json",
				"url" : "includes/async/actualiza_foto.async.php" ,
				"success" : function( data ){
						alert("Datos guardados con exito");
						//window.location.href=redireccionar;
					
				}
			});		
		return false;
	});

    
    $(".btnBuscarSocio").click(function(){
		
		var grillas = $("#grillas").val();
		var array_grillas = [];
        array_grillas = grillas.split(',');
		$.each( array_grillas, function( index, value ) {
			var grilla = "#grid-table" + value;
			$( grilla ).jqGrid('setGridParam', { 
				postData: {
					"Accion":$("#Accion").val(),
					"oper":"searchurl"
				}
			}).trigger('reloadGrid');
		});

		return false;
	});
	
	$(".eliminar_registro").click(function(){
		var tabla = $(this).attr("rel");
		var id = $(this).attr("id");
		var redireccionar = $(this).attr("lang");
		if (confirm("Esta seguro que desea borrar el registro?")){			
			jQuery.ajax( {
				"type" : "POST",
				"data" : { "Tabla" : tabla , "ID" : id   },
				"dataType" : "json",
				"url" : "includes/async/elimina_registro.async.php" ,
				 
				"success" : function( data ){
						alert("Registro Eliminado con exito");
						window.location.href=redireccionar+".php";
					
				}
			});		
		}
		
		return false;
	});
	
	$(".cancelar_reserva").click(function(){
		var IDSocio = $(this).attr("rel");
		var IDReserva = $(this).attr("id");
		var IDClub = $(this).attr("lang");
	
		if (confirm("Esta seguro que desea cancelar la reserva?")){			
			jQuery.ajax( {
				"type" : "POST",
				"data" : { "IDSocio" : IDSocio , "IDReserva" : IDReserva, "IDClub" : IDClub   },
				"dataType" : "json",
				"url" : "includes/async/cancela_reserva.async.php" ,
				 
				"success" : function( data ){
						alert("Reserva Cancelada con exito");
						window.location.href=redireccionar;
					
				}
			});		
		}
		
		return false;
	});


	$("#frmdisponibilidad input[name='PermiteRepeticion']").click(function(){		
		if ($(this).val() === 'S') {
		  $('#div_repeticion').show();
		} else if ($(this).val() === 'N') {
		  $('#div_repeticion').hide();
		} 
  });
	
	$(".btnRedirect").click(function(){
		var link = $(this).attr("rel");
		location.href = link;
		return false;
	});


	
	$( "#TipoSocio" ).change( function(){
		
		var tiposocio = $(this).val();

		$(".contentAuxiliar input").removeClass("mandatory");

		switch(tiposocio) {
		    case "Beneficiario":
		    	$(".contentAuxiliar").addClass("hide");
		    	$(".contentBeneficiario").removeClass("hide");

		    	$(".contentBeneficiario input").addClass("mandatory");

		    break;
		    case "Canje":
		    	$(".contentAuxiliar").addClass("hide");
		    	$(".contentCanje").removeClass("hide");

		    	$(".contentCanje input").addClass("mandatory");

		    break;
		    case "Cortesia":
		    	$(".contentAuxiliar").addClass("hide");
		    	$(".contentCortesia").removeClass("hide");

		    	$(".contentCortesia input").addClass("mandatory");


		    break;
		    
		}//end switch

		return false;

	} );

	/*
	//cargar socios en el select
	$("#frmReservaGeneral input#Accion").blur(function(){
		var accion = $(this).val();
		jQuery.ajax( {
			"type" : "POST",
			"data" : { "accion" : accion   },
			"dataType" : "json",
			"url" : "includes/async/get_beneficiarios.async.php" ,
			 
			"success" : function( data ){
					$('#IDSocio')
				    .find('option')
				    .remove()
				    .end()
;
					console.log( data );

					$.each( data.rows, function( index, value ) {
						
						console.log(value.Socio);
						$('#IDSocio')
					    .append('<option value="' +  value.IDSocio + '">' + value.Socio + '</option>')
					    .end()

					});


				
			}
		});		
		
		return false;
	});
	*/


	
	$(".brnReservaGeneral").click(function(){
		var elemento = $(this).attr("rel");
		var hora = $(this).attr("rev");
		var tee = $(this).attr("lang");

		$("#idelemento").val( elemento );
		$("#hora").val( hora );
		$("#tee").val( tee );

		$("#frmReservaGeneral").submit();

		return false;
	});
	
	$("#agregar_invitado").click(function(){
		var elemento = $("#AccionInvitado").val();	
		var id_elemento = "externo-"+$("#AccionInvitado").val();		
		if(elemento!=""){
			$('#SocioInvitado').append('<option value="'+id_elemento+'">'+elemento+'</option>');
			//$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
			$('#AccionInvitado').val('');
			alert("Invitado agregado");
		}
		else{
			alert("Por favor digite o seleccione un invitado");
		}
		return false;
	});
	
	
	$("#borrar_invitado").click(function(){
			var index_del="";
			$("#SocioInvitado option:selected").map(function(i, el) {
				index_del = $(el).val();
				$("#SocioInvitado option[value='"+index_del+"']").remove();	
			});
			if(index_del==""){
				alert("Seleccione un invitado");
			}
		return false;
	});

	$(".fancybox").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '80%',
		height		: '80%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		afterClose: function () { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
        $("#grid-table0").trigger("reloadGrid");
		$("#grid-table1").trigger("reloadGrid");
		$("#grid-table2").trigger("reloadGrid");
		$("#grid-table3").trigger("reloadGrid");
		$("#grid-table").trigger("reloadGrid");
    }
		
	});
	
	

	$('.noTabLink').click(function (e) {
        e.preventDefault();
        location.href = $(this).attr('href');
    });

    


	
}
