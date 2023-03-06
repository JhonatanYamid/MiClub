<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">
	
	$("#agregarDescuento").on("click",function(){
		let idDescuento = $('#Descuentos').val();
		let dscntoSel = objDescuentos[idDescuento];
		
		if(dscntoSel.PermisoAdmin == 'S'){
			$("#modalPermiso").modal("show");
		}else{
			calcular();
		}
		
		$("#modalDescuento").modal("hide");
	});

	$("#autorizar").on("click",function(){
		let usuario = $('#usuario').val();
		let psw = $('#clave').val();

		jQuery.ajax({
			type: "POST",
			data: {
				oper: "form",
				proceso: "usuario",
				usuario: usuario,
				clave: psw,
				idClub: $('#IDClub').val()
			},
			dataType: "text",
			url: "includes/async/facturacion.async.php",
			success: function (data) {
	
				if(data != '1'){

					if(data == '3')
						alert('<?= SIMUtil::get_traduccion('', '', 'errorelusuarionoexiste', LANGSESSION);?>');
					
					if(data == '2')
						alert('<?= SIMUtil::get_traduccion('', '', 'errornotienelospermisospararealizarestaoperacion', LANGSESSION);?>');

					
					$("#Descuentos option[value='']").prop('selected', true);
					$("#valDescuento").val("");
				}
				calcular();
				$("#modalPermiso").modal("hide");
			}
		});	
	});

	$(".cerrar").on("click",function(){
		$("#Descuentos option[value='']").prop('selected', true);
		$("#valDescuento").val("");
		calcular();

		$("#modalPermiso").modal("hide");
	});

	// FUNCIONES
	function selDescuentos(val = ""){
		let idClub = $('#IDClub').val();

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "descuentos",
				idClub: idClub,
				val: val
			},
			dataType: "json",
			url: "includes/async/facturacion.async.php",
			success: function (data) {
				objDescuentos = data.descuentos;
				$("#selDescuentos").html(data.menu);
			}
		}); 
	}

	function adminDescuento(){
		$("#valDescuento").val("");
		$("#nomDescuento").val("");

		let idDescuento = $('#Descuentos').val();
		let dscntoSel = objDescuentos[idDescuento];
		
		if(idDescuento != ""){
			$("#nomDescuento").val(dscntoSel.Nombre);

			if(dscntoSel.EnFactura == "S"){
				let tipoDescuento = dscntoSel.TipoCalculo == 1 ? '<?= SIMUtil::get_traduccion('', '', 'porcentajededescuento', LANGSESSION);?>' : '<?= SIMUtil::get_traduccion('', '', 'valordedescuento', LANGSESSION);?>'
				$("#valDescuento").attr('placeholder',tipoDescuento);

				$("#modalDescuento").modal("show");

			}else{
				$("#valDescuento").val(dscntoSel.ValorDescuento);
				
				if(dscntoSel.PermisoAdmin == 'S'){
					$("#modalPermiso").modal("show");
				}else{
					calcular();
				}
			}
		}else{
			$("#valDescuento").val(0);
			calcular();
		}
		
	}
</script>