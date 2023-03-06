<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">

	function agregarPago(){
		let idMedioPago = $("#IDMediosPago").val();
		let nmMedioPago = $("#IDMediosPago option[value='"+idMedioPago+"']").text();
		let valorPago = Number($("#ValorPagado").val());
		let observacion = $("#Observacion").val();

		$("#IDMediosPago").val("");
		$("#ValorPagado").val("");
		$("#Observacion").val("");
		
		if(idMedioPago == ""){
			alert('<?= SIMUtil::get_traduccion('', '', 'porfavorseleccioneunpago', LANGSESSION);?>')
		}
		else if(valorPago <= 0){
			alert('<?= SIMUtil::get_traduccion('', '', 'porfavoringreseunvalormayora0', LANGSESSION);?>')
		}else{
			let arrMp= {'IDMediosPago':idMedioPago,'Nombre':nmMedioPago,'ValorPagado':valorPago, 'Observacion':observacion};
			arrMediosPago.push(arrMp);

			cargarPagos();
		}
		
	}

	function eliminarPago(keyPago){
		arrMediosPago.splice(keyPago, 1);
		cargarPagos();
	}

	function cargarPagos(){

		$("#listaPagos").html("");
		$("#Pagos").val("");

		let valorTotal = Number($("#Total").val());
		let totalPagado = 0;
			
		for(let key in arrMediosPago){
			let valorPagado = Number(arrMediosPago[key].ValorPagado);

			body = "<tr>";
			body += "<td>"+arrMediosPago[key].Nombre+"</td>";
			body += "<td>"+valorPagado.toLocaleString(undefined, {maximumFractionDigits: 0})+"</td>";
			body += "<td>"+arrMediosPago[key].Observacion+"</td>";
			body += "<td align='center' valign='middle'><button onclick=\"eliminarPago("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-trash red'></i></button></td>";
			body += "</tr>";

			$("#listaPagos").append(body);
			totalPagado = totalPagado + valorPagado;
		}

		let pendiente = valorTotal - totalPagado;

		$('#TotalPagado').val(totalPagado);
		$('#TotalPagadoFinal').text(totalPagado.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#ValorPendiente').text(pendiente.toLocaleString(undefined, {maximumFractionDigits: 0}));

		if(totalPagado >= valorTotal)
			$("#Pagos").val(JSON.stringify(arrMediosPago));
	}

</script>