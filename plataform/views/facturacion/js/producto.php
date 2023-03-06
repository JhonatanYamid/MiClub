<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">
	
	$("#fechaInicio").blur(function (){
		fechaInicio();
    });

	$("#agregarProducto").on("click",function(){
		let cantidad = $('#Cantidad').val();
		let arrBnLn = arrBeneficiarios.length;
		let idEdit = $("#agregarProducto").attr('idEdit');
		let agregado = 1;
		let prcntjeDscnto = 0;

		if(cantidad != 0){
			if(cantidad >= arrBnLn){
				let descP = $('#DescuentoProd').val()
				let subt = $('#SubTotalProd').val();

				if(descP > 0){
					prcntjeDscnto = (descP/subt) * 100;
				}

				arrProducto = { 
					IDProductoFacturacion: $("#IDProductoFacturacion").val(),
					Codigo: objProducto.Codigo,
					Nombre: objProducto.Nombre, 
					Precio: $('#Precio').val(), 
					PrecioSinIva: $('#PrecioSinIva').val(), 
					Cantidad: $('#Cantidad').val(),
					SubTotal: subt,
					PorcentajeDescuento: prcntjeDscnto,
					Descuento: descP,
					NombreDescuento: $('#nomDescuento').val(),
					PorcentajeImpuesto: objProducto.ValorImpuesto,
					Impuesto: $('#ImpuestoProd').val(),
					Base: $('#BaseProd').val(),
					Total: $('#TotalProd').val(),
					valDescuento : $("#valDescuento").val(),
					Descuentos: $('#Descuentos').val(),
					objProducto: objProducto
				}
				
				if(objProducto.Beneficiarios == 'S'){
					if((arrBnLn == 1 || arrBnLn == 0) && arrBnLn != cantidad){
						let confirma = "<?= SIMUtil::get_traduccion('','', 'DebeexistirunbeneficiarioporcadaunidaddeProducto,deseacompletarlalistaautomaticamente', LANGSESSION);?>";
						let arrBeneficiario;

						if(confirm(confirma)){
							let shBenf = document.getElementById("BuscarBeneficiario");

							if(arrBnLn == 0){
								arrBeneficiario = {'IDSocio':objSocio.data,'Nombre':objSocio.nombre,'Documento':objSocio.documento};
								arrBeneficiario = adminBeneficiarios(arrBeneficiario,'add',true);
							}else{
								let dataBen = arrBeneficiarios[0];
								arrBeneficiario ={
									'IDSocio' : dataBen['IDSocio'],
									'Nombre' : dataBen['Nombre'],
									'Documento' :dataBen['Documento'],
									'FechaActivacion': " ",
									'FechaFin' : " "
								}

								if(objProducto['FechaInicio'] != 'S'){
									arrBeneficiario = adminBeneficiarios(arrBeneficiario,'add',true);
								}
							}	
							
							for(let x = arrBnLn; x < cantidad ;x++){
								arrBeneficiarios.push(arrBeneficiario);
							}

							shBenf.disabled = true
							cargarBeneficiarios();
						}
						agregado = 0;
					}
					else if(cantidad > arrBnLn){
						alert('<?= SIMUtil::get_traduccion('','', 'Porfavoringreseunbeneficiarioporcadaunidaddeproducto', LANGSESSION);?>')
						agregado = 0;
					}
					
				}else{
					arrBeneficiarios = new Array();

					let arrBeneficiario = {
						'IDSocio':objSocio.data,
						'Nombre':objSocio.nombre,
						'Documento':objSocio.documento,
					};
					
					if(objProducto.FechaActivacion != "S"){
						$('#fechaInicio').val(0);
						fechaInicio();
					}

					arrBeneficiario['FechaInicio'] = $('#fechaInicio').val(),
					arrBeneficiario['FechaFin'] = $('#fechaFin').val()
					
					if(objProducto.PermitirReservar == 'S'){
						for(let x = arrBnLn; x < cantidad ;x++){
							arrBeneficiarios.push(arrBeneficiario);
						}
					}else{
						arrBeneficiarios.push(arrBeneficiario);
					}
				}
				
				if(agregado == 1){
					arrProducto['Beneficiarios'] = arrBeneficiarios;

					if(idEdit){
						arrProductos[idEdit] = arrProducto;
						$('#agregarProducto').removeAttr('idEdit');
					}
					else{
						arrProductos.push(arrProducto);
					}

					console.log(arrProductos);
					$("#modalProductos").modal("hide");
					reloadGrid();	
				}			
			}else{
				alert('<?= SIMUtil::get_traduccion('','', 'Porfavoringreseunbeneficiarioporcadaunidaddeproducto', LANGSESSION);?>')
			}
		}else{
			alert('<?= SIMUtil::get_traduccion('', '', 'atencion,Lacantidaddelproductodebesermayora0,porfavorverifiquela', LANGSESSION);?>');
		}
	});

	// FUNCIONES
	function eliminarProducto(keyProducto){
		arrProductos.splice(keyProducto, 1);
		reloadGrid();
	}
	
	function editarProducto(keyProducto){
		$("#divBuscarP").hide();

		arrProd = arrProductos[keyProducto];
		objProducto = arrProd.objProducto;
		objProducto['Descuentos'] = arrProd.Descuentos;
		selDescuentos(arrProd.Descuentos);

		let precio = Number(objProducto.Precio);
		let valImpuesto = Number(objProducto.ValorImpuesto);

		$("#Cantidad").val(arrProd.Cantidad);
		$("#valDescuento").val(arrProd.valDescuento);
		$("#Descuentos").val(arrProd.Descuentos);
		$("#nomDescuento").val(arrProd.nomDescuento);
		
		$("#IDProductoFacturacion").val(objProducto.IDProductoFacturacion);
		$("#nmProducto").text("- "+objProducto.Nombre);
		$("#codProducto").text(objProducto.Codigo);

		$(".precio").text(precio.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#Precio").val(precio);

		$("#nmImpuesto").text(objProducto.Impuesto);
		$("#valImpuesto").val(valImpuesto);
		
		calcular();

		arrBeneficiarios = arrProd.Beneficiarios

		$("#fechas").hide();
		$("#fechaInicio").val("");
		$("#fechaFin").val("");
		$(".fechafin").text("");

		if(objProducto.Beneficiarios == 'S'){
			cargarBeneficiarios();
			
			$("#beneficiarios").show();
		}else{
			$("#beneficiarios").hide();
			$("#listaBeneficiarios").html("");

			if(objProducto.FechaActivacion == 'S'){
				$("#fechas").show();
				$("#fechaInicio").val(arrBeneficiarios[0].FechaInicio);
				$("#fechaFin").val(arrBeneficiarios[0].FechaFin);
				$(".fechafin").text(arrBeneficiarios[0].FechaFin);
			}
		}
		$("#divContentP").show();
				
		$("#agregarProducto").text('<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION);?>');
		$("#agregarProducto").attr('idEdit', keyProducto);
		$("#modalProductos").modal("show");
	}

	function calcular(){

		let idDescuento = $('#Descuentos').val();
		let dscntoSel = objDescuentos[idDescuento];

		let precio = $("#Precio").val();
		let valImpuesto = $("#valImpuesto").val();
		let cantidad = $('#Cantidad').val();
		let valDescuento = Number($("#valDescuento").val());
		let descuento = valDescuento;
		let porImpuesto = valImpuesto/100;
		let precioSinIva = precio/(porImpuesto+1);
		//let precioSinIva = valImpuesto == 0 ? precio : precio/(por+1);

		let subtotal = precioSinIva*cantidad;

		if(valDescuento != 0)
			descuento = dscntoSel.TipoCalculo != 1 ? valDescuento : subtotal*(valDescuento/100);
		
		if(descuento > subtotal){
			alert('<?= SIMUtil::get_traduccion('', '', 'erroreldescuentonosepuedeaplicarelvalordedescuentosuperaelvalorapagar', LANGSESSION);?>');
			$("#Descuentos option[value='']").prop('selected', true);
			descuento = 0;
		}

		let base = subtotal-descuento;
		let impuesto = base*(porImpuesto);

		let total = base+impuesto;
	
		$(".precioSinIva").text(precioSinIva.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#PrecioSinIva").val(precioSinIva);

		$(".subtotal").text(subtotal.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#SubTotalProd").val(subtotal);

		$('#DescuentoProd').val(descuento);
		$('.descuento').text(descuento.toLocaleString(undefined, {maximumFractionDigits: 0}));

		$(".base").text(base.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#BaseProd").val(base);

		$("#impuestotxt").text(impuesto.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#ImpuestoProd").val(impuesto);

		$(".total").text(total.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$("#TotalProd").val(total);
	}

	function fechaInicio(){
		let vigencia = Number(objProducto.Vigencia);
		let tipo = Number(objProducto.TipoVigencia);
		let fechaInicio = $('#fechaInicio').val();
		let fecha = $('#fechaInicio').val() == 0 ? hoy: fechaInicio;

		fechaFin = sumar_vigencia(fecha,vigencia,tipo);
		
		if (fecha != hoy)
			fecha = new Date(fechaInicio)

		fechaIn = tipo == 1 ? fecha.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : fecha.toISOString().slice(0, 10);
		fechaFin = tipo == 1 ? fechaFin.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : fechaFin.toISOString().slice(0, 10);
		
		$("#fechaInicio").val(fechaIn);
		$("#fechaFin").val(fechaFin);
		$(".fechafin").text(fechaFin);
	}

</script>