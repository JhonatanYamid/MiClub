<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">

	$("#editarFecha").on("click",function(){
		let vigencia = Number(objProducto.Vigencia);
		let tipo = Number(objProducto.TipoVigencia);
		let fecha = $('#fechaActiva').val();
		let keyBf = $("#fechaActiva").attr('idKey');

		if(fecha != ""){
			fechaFin = sumar_vigencia(fecha,vigencia,tipo);

			arrBeneficiarios[keyBf]['FechaInicio'] = tipo == 1 ? fecha.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : new Date(fecha).toISOString().slice(0, 10);
			arrBeneficiarios[keyBf]['FechaFin'] = tipo == 1 ? fechaFin.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : fechaFin.toISOString().slice(0, 10);
		}else{
			arrBeneficiarios[keyBf]['FechaInicio'] = "";
			arrBeneficiarios[keyBf]['FechaFin'] = "";
		}
		
		cargarBeneficiarios();

		$("#modalFecha").modal("hide");
	});
	
	// FUNCIONES

	function adminBeneficiarios(val,oper = 'add',retornar = false){
		let shBenf = document.getElementById("BuscarBeneficiario");
		let cantidad = $('#Cantidad').val();

		if(oper == 'add'){
			let fechaIn = hoy;
			let vigencia = Number(objProducto.Vigencia);
			let tipo = Number(objProducto.TipoVigencia);

			if(objProducto.FechaActivacion != 'S'){

				if(objProducto.PermitirReservar == 'S'){
					let mes = hoy.getMonth();
					let year = hoy.getFullYear();

					fechaIn = new Date(year,mes,'1');

					if(hoy.getDate() > 5 )
						fechaIn = sumar_vigencia(fechaIn,1,3);
				}
				
				fechaFin = sumar_vigencia(fechaIn,vigencia,tipo);
				
				val['FechaInicio'] = tipo == 1 ? fechaIn.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : fechaIn.toISOString().slice(0, 10);
				val['FechaFin'] = tipo == 1 ? fechaFin.format('Y-m-d hh:mm').toLocaleString("en-US", {timeZone: "America/New_York"}) : fechaFin.toISOString().slice(0, 10);
			}else{
				val['FechaInicio'] = "";
				val['FechaFin'] = "";
			}

			if(retornar)
				return val;
			
			arrBeneficiarios.push(val);
			
			if(arrBeneficiarios.length == cantidad)
				shBenf.disabled = true;
			
		}
		if(oper == 'del'){
			arrBeneficiarios.splice(val, 1);
			
			if(arrBeneficiarios.length < cantidad && shBenf.disabled)
				shBenf.disabled = false
		}
		cargarBeneficiarios();
	}

	function editFechaActivo(key){
		$('#fechaActiva').attr('idKey', key);
		$("#modalFecha").modal("show");
	}

	function cargarBeneficiarios(){
		$("#headBenef").html("");
		$("#listaBenef").html("");
		
		head = "<tr>";
		
		if(objProducto.FechaActivacion == 'S')
			head += "<th align='center' valign='middle' width='64'><?= ucwords(SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION));?></th>";

		head += "<th><?= ucwords(SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION));?></th>";
		head += "<th><?= ucwords(SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION));?></th>";
		head += "<th><?= ucwords(SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION));?></th>";
		head += "<th><?= ucwords(SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION));?></th>";
		head += "<th align='center' valign='middle' width='64'><?= ucwords(SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION));?></th></tr>";

		$("#headBenef").html(head);

		for(let key in arrBeneficiarios){

			body = "<tr>";
			
			if(objProducto.FechaActivacion == 'S')
				body += "<td><button onclick=\"editFechaActivo("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-pencil green'></i></button></td>";
				
			body += "<td>"+arrBeneficiarios[key].Documento+"</td>";
			body += "<td>"+arrBeneficiarios[key].Nombre+"</td>";
			body += "<td>"+arrBeneficiarios[key].FechaInicio+"</td>";
			body += "<td>"+arrBeneficiarios[key].FechaFin+"</td>";
			body += "<td><button onclick=\"adminBeneficiarios("+key+",'del')\" type='button' class='button_style'><i class='ace-icon fa fa-trash red'></i></button></td>";
			body += "</tr>";

        	$("#listaBenef").append(body);
		}
	}

</script>