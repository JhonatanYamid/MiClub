<script>
	var check = 0;
	selTipoFacturacion();

	$(".selOne").each( function () {
		if(!($(this).is(':checked'))){
			check = 1;
		}
	});

	if (check == 0) {
		$("#selAll").prop("checked", true);
	}

	$(document).on("change","#selAll",function() {
		
		var arrValues = new Array();
		var stringValues = '';

		if (this.checked) {
			$(".selOne").each(function() {
				if(!($(this).attr("disabled"))){
					var val = $(this).val();
					var precio = $('#precio'+val).val();

					if(precio == '')
						$('#precio'+val).val(0);
						
					this.checked=true;

					var op = val+"|"+precio;
					arrValues.push(op);
				}
				
			});

			stringValues = arrValues.toString();
		} else {
			$(".selOne").each(function() {
				this.checked=false;
			});
		}
		$("#precios").val(stringValues);
	});
	
	$(document).on("click",".selOne",function() {
		var arrValues = new Array();
		var checkIn = 0;

		$(".selOne").each( function () {
			if($(this).is(':checked')){
				var val = $(this).val();
				var precio = $('#precio'+val).val();

				if(precio == '')
					$('#precio'+val).val(0);

				var op = val+"|"+precio;
				arrValues.push(op);
			}else{
				checkIn = 1;
			}
		});

		if (checkIn == 0) {
			$("#selAll").prop("checked", true);
		}
		else {
			$("#selAll").prop("checked", false);
		}

		var stringValues = arrValues.toString();
		$("#precios").val(stringValues);
	});

	$(document).keyup(".selInpt",function(e) {

		var idIn = $(e.currentTarget.activeElement);

		if (idIn != undefined && idIn.attr('id') != undefined) {

			var arrValues = new Array();
			var checkIn = 0;

			var idTxt = idIn.attr('id');
			var idCh = idTxt.replace('precio','');

			if(idIn.val() != ''){
				$("input[name='precio'][value='"+idCh+"']").prop('checked', true);
			}else{
				$("input[name='precio'][value='"+idCh+"']").prop('checked', false);
			}

			$(".selOne").each( function () {
				if($(this).is(':checked')){
					var val = $(this).val();
					var precio = $('#precio'+val).val();

					if(precio == '')
						$('#precio'+val).val(0);

					var op = val+"|"+precio;
					arrValues.push(op);
				}else{
					checkIn = 1;
				}
			});

			if (checkIn == 0) {
				$("#selAll").prop("checked", true);
			}
			else {
				$("#selAll").prop("checked", false);
			}

			var stringValues = arrValues.toString();
			$("#precios").val(stringValues);
		}		
	});

	$(document).on("change","#IDServicioMaestro",function() {
		changeServicio();
	});
	
	function changeServicio(){
		var idServicio = $('#IDServicioMaestro').val();
		$('.selInpt, .selOne').removeAttr("disabled");
		
		if($("#selAll").length != 0 && idServicio != "") {

			jQuery.ajax({
				type: "GET",
				data: {
					oper: "form",
					proceso: "idServicio",
					idServicio: idServicio,
				},
				dataType: "html",
				url: "includes/async/productofacturacion.async.php",
				success: function (data) {
					
					$(".selOne").each( function () {
						var val = $(this).val();
					
						if(!data.includes(val)){

							this.checked=false;
							$("#selAll").prop("checked", false);
							$("input[name='precio'][value='"+val+"']").attr("disabled", true);
							$('#precio'+val).attr("disabled", true);
							$('#precio'+val).val("");
						}
					});
				}
			}); 
		}
	}

	function funcionCheck(idCampo){
		var arrValues = new Array();
		var txtCam = "#"+idCampo+"Ch:checked";
		var txtIn = "#"+idCampo;

		$(txtCam).each( function () {
			if($(this).is(':checked')){
				arrValues.push($(this).val());
			}
		});
		
		var stringValues = arrValues.toString();
		$(txtIn).val(stringValues);
	}

	function selTipoFacturacion(){
		var idCategoria = $('#IDCategoriaFacturacion').val();
		var valueTipo = '<?= $frm["IDTipoFacturacion"]; ?>';

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "select",
				val: valueTipo,
				idCat: idCategoria
			},
			dataType: "html",
			url: "includes/async/productofacturacion.async.php",
			success: function (data) {
				$("#divTipoFacturacion").html(data);
				changeTipoFac();
			}
		}); 
	}

	function changeTipoFac(){

		var idTipo = $('#IDTipoFacturacion').val();
		var arrValues = new Array();
		var precioHtml = '<? echo $precioHtml; ?>';
		var habilitarHtml = '<? echo $habilitarHtml; ?>';

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "divs",
				idTipo: idTipo
			},
			dataType: "json",
			url: "includes/async/productofacturacion.async.php",
			success: function (data) {
			
				var divPrecioCh = document.getElementById("divPrecioCh");	
				var divHabilitar = document.getElementById("divHabilitar");
				
				if(data != null && data.Precio == 'S'){ 
					if(divHabilitar != null){
						divHabilitar.innerHTML = "";
						$('#divHabilitar').hide('slow');
					}
					
					divPrecioCh.innerHTML = precioHtml;

					$('#divPrecio').show('slow');
					$('#divPrecioCh').show('slow');
					
				}else{

					if(divHabilitar != null){
						divHabilitar.innerHTML = habilitarHtml;
						$('#divHabilitar').show('slow');
					}
					divPrecioCh.innerHTML = "";

					$('#divPrecio').hide('slow');
					$('#divPrecioCh').hide('slow');
				}
				
				if(data != null && data.PermitirReservar == 'S'){
					changeServicio();
					$('#IDServicioMaestro').addClass('mandatory');
					$('#divReserva').show('slow');
				}else{
					$('.selInpt, .selOne').removeAttr("disabled");
					$('#IDServicioMaestro').removeClass('mandatory');
					$('#divReserva').hide('slow');
				} 

				if(data != null && data.ControlAcceso == 'S'){
					$('#divAcceso').show('slow'); 
				}else{
					$('#divAcceso').hide('slow');
				} 

				if(data != null && data.NumSesiones == 'S'){ 
					$('.divSesion').show('slow');  
				}else{
					$('.divSesion').hide('slow');  
				}

				if(data == null || data.Congelaciones == 3){
					$('.divTimeCong').hide('slow');
					$('.divCong').hide('slow');
				}else if(data.Congelaciones == 1){ 
					$('.divCong').hide('slow');
					$('.divTimeCong').show('slow');
				}else if(data.Congelaciones == 2){
					$('.divTimeCong').show('slow');
					$('.divCong').show('slow'); 
				}
			}
		}); 
	}
</script>