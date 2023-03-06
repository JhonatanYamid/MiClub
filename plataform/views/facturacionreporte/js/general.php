<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">
	let hoy = new Date();
	let tipoClub = <?= $tipo; ?>;
	let objDescuentos;
	let objProducto;
	let objSocio;
	let arrBeneficiarios = new Array();
	let arrProductos = new Array();
	let arrMediosPago = new Array();

	// GRILLA PRODUCTOS
	let grid_selector = "#productosTable";
 	let pager_selector = "#productospager";

	let percent = 0.96;

	//resize to fit page size
	$(window).on('resize.jqGrid', function() {
		$(grid_selector).jqGrid('setGridWidth', ($(".page-content").width()*percent));
	})
	//resize on sidebar collapse/expand
	var parent_column = $(grid_selector).closest('[class*="col-"]');
	$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
		if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
			//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
			setTimeout(function() {
				$(grid_selector).jqGrid('setGridWidth', (parent_column.width()*percent));
			}, 0);
		}
	})

	jQuery(grid_selector).jqGrid({
		datatype: "local",
        height: "100%",
		colNames: [
			'<?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>/PLU',
			'<?= SIMUtil::get_traduccion('', '', 'producto', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'precio', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'subtotal', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'base', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'impuesto', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?>'
		],
		colModel: [
			{name: 'Editar', index: 'Editar', align: "center", width: 150},  
			{name: 'Codigo', index: 'Codigo', align: "center"},
			{name: 'Nombre', index: 'Nombre', align: "left", width: 250},                
			{name: 'PrecioSinIva', index: 'PrecioSinIva', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},  
			{name: 'Cantidad', index: 'Cantidad', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},             
			{name: 'SubTotal', index: 'SubTotal', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},                    
			{name: 'Descuento', index: 'Descuento', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},                  
			{name: 'Base', index: 'Base', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},             
			{name: 'Impuesto', index: 'Impuesto', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}}, 
			{name: 'Total', index: 'Total', align: "center",formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},
			{name: 'Eliminar', index: 'Eliminar', align: "center", width: 150},  
		],
		pager: pager_selector,
		rowList: [],
    	pgbuttons: false,
    	pgtext: '',
    	viewrecords: false,   
		footerrow: true,
    	userDataOnFooter: true,

		gridComplete: function () {
			let sumSubTotal = $(this).jqGrid("getCol","SubTotal", false, "sum");
			let sumDescuento = $(this).jqGrid("getCol","Descuento", false, "sum");
			let sumBase = $(this).jqGrid("getCol","Base", false, "sum");
			let sumImpuesto = $(this).jqGrid("getCol","Impuesto", false, "sum");
			let sumTotal = $(this).jqGrid("getCol","Total", false, "sum");

			$(this).jqGrid("footerData", "set", {
				Editar: "Total:", 
				SubTotal: sumSubTotal,
				Descuento: sumDescuento,
				Base: sumBase,
				Impuesto: sumImpuesto,
				Total: sumTotal,
			});

			sumarTotales();
		}
	});

	$(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size

	jQuery(grid_selector).navGrid(pager_selector,{edit:false,add:false,del:false,search:false,refresh:false}).navButtonAdd(pager_selector,{
		caption:"<?= SIMUtil::get_traduccion('', '', 'agregarproducto', LANGSESSION);?>", 
		buttonicon: 'ace-icon fa fa-plus',
		onClickButton: function(){ 
			
			$("#divBuscarP").show();

			$("#BuscarProducto").val("");

			$("#divContentP input").val(0);
			$(".txtCont").text('');
			$("#divContentP").hide();

			$("#beneficiarios").hide();
			$("#listaBeneficiarios").html("");
			$("#BuscarBeneficiario").val("");

			selDescuentos();
			
			$("#agregarProducto").text('<?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION);?>');
			$('#agregarProducto').removeAttr('idEdit');
			
			if($('#IDSocio').val() != ""){
				$("#modalProductos").modal("show");
			}else{
				alert('<?= SIMUtil::get_traduccion('', '', 'porfavorseleccioneunclienteantesdecontinuar', LANGSESSION);?>')
			}
		}, 
		position:"last"
	});
	
	//JS MODAL
	// $('.modal-body').change(function(){
	// 	setInterval(function(){ 
	// 		$('.modal').modal('handleUpdate'); 
	// 	}, 100);
	// });

	// $(window).on('resize', function() {
	// 	setInterval(function(){ 
	// 		$('.modal').modal('handleUpdate'); 
	// 	}, 100);
	// });

	// JS FACTURACION
	infoClub();

	$("#Cantidad").on("keyup", function (){
		let shBenf = document.getElementById("BuscarBeneficiario");
		let benLen = arrBeneficiarios.length;
		let cantidad = $(this).val();

		if(benLen > cantidad && cantidad != ""){
			alert('<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunbeneficiarioporcadaunidaddeproducto', LANGSESSION);?>')
		}else if(benLen < cantidad){
			if(shBenf.disabled)
				shBenf.disabled = false
		}else{
			shBenf.disabled = true
		}

		calcular();
    });

	$('#IDClub').change(function(){
		infoClub();		
	});

	function infoClub(){
		let idClubSend = tipoClub == 1 ? $('#IDClub').val() : $('#IDClub').val();

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "sedes",
				idClub: idClubSend
			},
			dataType: "json",
			url: "includes/async/facturacion.async.php",
			
			success: function (data) {
				
				arrBeneficiarios = new Array();
				arrProductos = new Array();
				arrMediosPago = new Array();
				reloadGrid();

				$("#vendedores").html(data['vendedores']);

				$(".limpiar input").each(function() {
					$(this).val("");
				});
				$(".limpiar label, .limpiar span").each(function() {
					$(this).text('');
				});
				
				$(".limpiarNum input").each(function() {
					$(this).val(0);
				});

				$(".limpiarNum span").each(function() {
					$(this).text('0');
				});
				
				$('#listaPagos').html("");

				$("#divCliente").hide("slow");

				if(data){
					let nombreClub = data.Nombre;
					let numeroFac = data.ConsecutivoFacturas;
					let prefijo = data.Prefijo

					$("#nombreClub").text(nombreClub);
					$("#consecutivoTxt").text(prefijo+numeroFac);
					$("#Consecutivo").val(numeroFac);
					$("#Prefijo").val(prefijo);
					$("#IDResolucionFactura").val(data.IDResolucionFactura);

				}else{
					alert("<?= SIMUtil::get_traduccion('', '', 'Noesposiblecrearunafactura,Elclubnocuentaconunaresolucionactiva', LANGSESSION);?>");
					
					if(tipoClub == 1)
						location.href="facturacion.php?action=search";
				}
			}
		});
	}

	function reloadGrid(){
		$(grid_selector).jqGrid('clearGridData');
		$("#productos").val("");
		
		let i=1;
		for(let key in arrProductos){
			let eliminar = "<button onclick=\"eliminarProducto("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-trash red'></i></button>"
			let editar = "<button onclick=\"editarProducto("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-pencil green'></i></button>"
			arrProductos[key]['Eliminar'] = eliminar;
			arrProductos[key]['Editar'] = editar;

			jQuery(grid_selector).jqGrid('addRowData',i,arrProductos[key]);
			i++;
		}
		
		if(arrProductos.length != 0 ){
			$("#productos").val(JSON.stringify(arrProductos));
		}
		
	}

	function sumar_vigencia(fecha,valor,tipo){	
		fecha = new Date(fecha);
		if(tipo == 1){
			fecha = new Date(fecha.setHours(fecha.getHours() + valor));
		}
		else if(tipo == 2){
			fecha = new Date(fecha.setDate(fecha.getDate() + valor));
		}
		else if(tipo == 3){
			fecha = new Date(fecha.setMonth(fecha.getMonth() + valor));
		}
		
		return fecha;
	}

	function sumarTotales(){

		let sumSubTotal = 0;
		let sumDescuento = 0;
		let sumBase = 0;
		let sumImpuesto = 0;
		let sumTotal = 0;
		let totalPagado = Number($('#TotalPagado').val());

		arrProductos.forEach(function(value, index){
			sumSubTotal += Number(value.SubTotal);
			sumDescuento += Number(value.Descuento);
			sumBase += Number(value.Base);
			sumImpuesto += Number(value.Impuesto);
			sumTotal += Number(value.Total);
		});
		
		let pendiente = sumTotal - totalPagado;

		$('#SubTotal').val(sumSubTotal);
		$('#Descuento').val(sumDescuento);
		$('#Base').val(sumBase);
		$('#Impuesto').val(sumImpuesto);
		$('#Total').val(sumTotal);

		$('#SubTotalFinal').text(sumSubTotal.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#DescuentoFinal').text(sumDescuento.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#BaseFinal').text(sumBase.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#ImpuestoFinal').text(sumImpuesto.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#TotalFinal').text(sumTotal.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#ValorPendiente').text(pendiente.toLocaleString(undefined, {maximumFractionDigits: 0}));
		$('#TotalPagadoFinal').text(totalPagado.toLocaleString(undefined, {maximumFractionDigits: 0}));
		
	}
</script>