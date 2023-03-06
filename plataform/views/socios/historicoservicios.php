<style type="text/css">
	.tallest {
		vertical-align: bottom;
		padding-top: 7px;
	}

	.modal {
		text-align: center;
		padding: 0 !important;
	}

	.modal:before {
		content: '';
		display: inline-block;
		height: 100%;
		vertical-align: middle;
		margin-right: -4px;
		/* Adjusts for spacing */
	}

	.modal-dialog {
		display: inline-block;
		text-align: left;
		vertical-align: middle;

		width: inherit;
		max-height: 860px;

		margin-left: auto;
		pointer-events: all;
	}
</style>
<?
$url_search = "";
if (SIMNet::req("action") == "search") {
	$url_search = "&oper=search_url&qryString=" . SIMNet::get("qryString");
}
?>
<div>
	<div id="jqGrid_containerServicios">
		<table id="serviciosTable"></table>
	</div>
	<div id="serviciospager"></div>
</div>


<div class="modal fade" id="modalConfigurar" tabindex="-1" role="dialog" aria-labelledby="LabelConfigurar" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-lg">
			<div class="modal-header">
				<h5 class="modal-title" id="LabelConfigurar"> <?= ucwords(SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION)); ?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">

				<body data-spy="scroll" data-target="#navbar-Configurar">
					<div id="navbar-Configurar">
						<div class="container-fluid">
							<div class="row">
								<div class="col-sm-12 form-group first">
									<label class="col-sm-3 control-label tallest no-padding-right text-right"><?= SIMUtil::get_traduccion('', '', 'Opcionesdeconfiguración', LANGSESSION); ?>: </label>
									<div class="col-sm-3 text-left" id="opConfiguracion"></div>
								</div>
								<div id="divConfig">
									<div class="col-sm-12 form-group first divFechaIni infoConfig" style="display:none">
										<label class="col-sm-3 control-label tallest no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'Fechadeinicioactual', LANGSESSION); ?>: </label>
										<label class="col-sm-3 text-left tallest" id="fechaInicio"></label>
									</div>
									<div class="col-sm-12 form-group first divFechaIni infoConfig" style="display:none">
										<label class="col-sm-3 control-label tallest no-padding-right text-right"><?= SIMUtil::get_traduccion('', '', 'Nuevafechadeinicio', LANGSESSION); ?>: </label>
										<div class="col-sm-2 text-left">
											<input type="text" id="FechaInicioNew" name="FechaInicioNew" title="<?= SIMUtil::get_traduccion('', '', 'Nuevafechadeinicio', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nuevafechadeinicio', LANGSESSION); ?>" class="col-sm-12 calendar" value="<?= $hoy; ?>" />
										</div>

										<div class="col-sm-1 text-left"></div>

										<label class="col-sm-3 control-label tallest no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'Nuevafechafinal', LANGSESSION); ?>: </label>
										<label class="col-sm-2 tallest text-left" id="fechaFinCalc"></label>
										<input type="hidden" name="FechaFinNewCalc" id="FechaFinNewCalc" value="" />
									</div>
									<div class="col-sm-12 for2-group first divFechaFin infoConfig" style="display:none">
										<label class="col-sm-3 control-label tallest no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'Fechafinalactual', LANGSESSION); ?>: </label>
										<label class="col-sm-2 text-left tallest" id="fechaFin"></label>

										<div class="col-sm-1 text-left"></div>

										<label class="col-sm-3 control-label tallest no-padding-right text-right"><?= SIMUtil::get_traduccion('', '', 'Nuevafechafinal', LANGSESSION); ?>: </label>
										<div class="col-sm-3 text-left">
											<input type="text" id="FechaFinNew" name="FechaFinNew" title="<?= SIMUtil::get_traduccion('', '', 'Nuevafechafinal', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nuevafechafinal', LANGSESSION); ?>" class="col-sm-12 calendar" value="<?= $hoy; ?>" />
										</div>
									</div>
									<div class="col-sm-12 form-group first divCongela infoConfig" style="display:none">
										<label class="col-sm-3 control-label tallest no-padding-right text-right"><span id="tipoCongela"></span> <?= SIMUtil::get_traduccion('', '', 'acongelar', LANGSESSION); ?>: </label>
										<div class="col-sm-2 text-left">
											<input type="text" id="numCongela" name="numCongela" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?>" value=0 />
										</div>

										<div class="col-sm-1 text-left"></div>

										<label class="col-sm-3 control-label tallest no-padding-right text-right"><?= SIMUtil::get_traduccion('', '', 'Fechadeiniciocongelacion', LANGSESSION); ?>: </label>
										<div class="col-sm-3 text-left">
											<input type="text" id="FechaCongela" name="FechaCongela" title="<?= SIMUtil::get_traduccion('', '', 'Fechadeiniciocongelacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechadeiniciocongelacion', LANGSESSION); ?>" class="col-sm-12 calendar" value="<?= $hoy; ?>" />
										</div>
									</div>
									<div class="col-sm-12 form-group first divTransfiere infoConfig" style="display:none">
										<label class="col-sm-3 control-label tallest no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>: </label>
										<div class="col-sm-9 text-left">
											<input type="text" id="BuscarBeneficiario" name="Socio" placeholder="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>" class="col-sm-12 autocomplete-ajax-transferencia" value="" />
											<input type="hidden" name="IDSocioTransfiere" id="IDSocioTransfiere" value="" />
										</div>
									</div>
									<div class="col-sm-12 form-group first divTransfiere infoConfig" style="display:none">

										<label class="col-sm-3 control-label tallest no-padding-right text-right"><?= SIMUtil::get_traduccion('', '', 'Fechadetransferencia', LANGSESSION); ?>: </label>
										<div class="col-sm-3 text-left">
											<input type="text" id="FechaTransfiere" name="FechaTransfiere" title="<?= SIMUtil::get_traduccion('', '', 'Fechadetransferencia', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechadetransferencia', LANGSESSION); ?>" class="col-sm-12 calendar" value="<?= $hoy; ?>" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION); ?></button>
			<button type="button" class="btn btn-primary" id="guardarConf"><?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?></button>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="LabelDetalle" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="LabelDetalle"><?= SIMUtil::get_traduccion('', '', 'HistóricodeInventario', LANGSESSION); ?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:auto;">
				<table id="tablaDetalle"></table>
				<div id="tablaDetalle-pager"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'Cerrar', LANGSESSION); ?></button>
			</div>
		</div>
	</div>
</div>

<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">
	let infoConfig = new Array();

	// GRILLA servicios PRODUCTOS
	let grid_selector_service = "#serviciosTable";
	let pager_selector_service = "#serviciospager";

	//resize to fit page size
	$(window).on('resize.jqGrid', function() {
		$(grid_selector_service).jqGrid('setGridWidth', $(".tab-content").width());
	})
	//resize on sidebar collapse/expand
	var parent_column = $(grid_selector_service).closest('[class*="col-"]');
	$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
		if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
			//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
			setTimeout(function() {
				$(grid_selector_service).jqGrid('setGridWidth', parent_column.width());
			}, 0);
		}
	})

	jQuery(grid_selector_service).jqGrid({
		url: 'includes/async/historicoservicios.async.php?idsocio=<?= SIMNet::req("id"); ?><?= $url_search ?>',
		datatype: "json",
		colNames: [
			'<?= SIMUtil::get_traduccion('', '', 'Producto', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'fechadecompra', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'Disponible', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?>',
		],
		colModel: [{
				name: 'Producto',
				index: 'pf.Nombre',
				align: "left"
			},
			{
				name: 'Sede',
				index: 'c.Nombre',
				align: "left"
			},
			{
				name: 'FechaCreacion',
				index: 'FechaCreacion',
				align: "left"
			},
			{
				name: 'FechaInicio',
				index: 'FechaInicio',
				align: "left"
			},
			{
				name: 'FechaFin',
				index: 'FechaFin',
				align: "left"
			},
			{
				name: 'Disponible',
				index: 'Disponible',
				align: "left",
				search: false
			},
			{
				name: 'Estado',
				index: 'Estado',
				align: "left"
			},
			{
				name: 'Accion',
				index: 'Accion',
				search: false,
				align: "center"
			},
		],
		rowNum: 100,
		rowList: [20, 40, 100],
		sortname: 'FechaCreacion',
		viewrecords: true,
		sortorder: "DESC",
		caption: "<?= SIMUtil::get_traduccion('', '', 'historialservicios', LANGSESSION); ?>",
		height: "100%",
		pager: pager_selector_service,
		altRows: true,
		multiboxonly: true,

		loadComplete: function() {
			var table = this;
			setTimeout(function() {
				updatePagerIcons(table);
				enableTooltips(table);
			}, 0);
		},
	});
	$(window).triggerHandler('resize.jqGrid');
	//enable search/filter toolbar
	jQuery(grid_selector_service).jqGrid('filterToolbar', {
		defaultSearch: true,
		stringResult: true
	})
	jQuery(grid_selector_service).filterToolbar({});

	//navButtons
	jQuery(grid_selector_service).jqGrid('navGrid', pager_selector_service, { //navbar options
		edit: false,
		editicon: 'ace-icon fa fa-pencil blue',
		add: false,
		addicon: 'ace-icon fa fa-plus-circle purple',
		del: false,
		delicon: 'ace-icon fa fa-trash-o red',
		search: false,
		searchicon: 'ace-icon fa fa-search orange',
		refresh: true,
		refreshicon: 'ace-icon fa fa-refresh green',
		view: true,
		viewicon: 'ace-icon fa fa-search-plus grey',
	}, {
		recreateForm: true,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
			style_edit_form(form);
		}
	}, {

		closeAfterAdd: true,
		recreateForm: true,
		viewPagerButtons: false,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
				.wrapInner('<div class="widget-header" />')
			style_edit_form(form);
		}
	}, {
		//delete record form
		recreateForm: true,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			if (form.data('styled')) return false;

			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
			style_delete_form(form);

			form.data('styled', true);
		},
		onClick: function(e) {
			//alert(1);
		}
	}, {
		//search form
		recreateForm: true,
		afterShowSearch: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
			style_search_form(form);

		},
		afterRedraw: function() {
			style_search_filters($(this));
		},
		multipleSearch: true,

	}, {
		//view record form
		recreateForm: true,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
		}
	})

	function style_search_filters(form) {
		form.find('.delete-rule').val('X');
		form.find('.add-rule').addClass('btn btn-xs btn-primary');
		form.find('.add-group').addClass('btn btn-xs btn-success');
		form.find('.delete-group').addClass('btn btn-xs btn-danger');
	}

	function style_search_form(form) {
		var dialog = form.closest('.ui-jqdialog');
		var buttons = dialog.find('.EditTable')
		buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
		buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
		buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
	}

	//replace icons with FontAwesome icons like above
	function updatePagerIcons(table) {
		var replacement = {
			'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
			'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
			'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
			'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
		};
		$('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
			var icon = $(this);
			var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

			if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
		})
	}

	function enableTooltips(table) {
		$('.navtable .ui-pg-button').tooltip({
			container: 'body'
		});
		$(table).find('.ui-pg-div').tooltip({
			container: 'body'
		});
	}

	//js acciones para los modal. 
	jQuery('#modalDetalle').on('hidden.bs.modal', function(e) {
		var contenido = "<table id='tablaDetalle'></table> <div id='tablaDetalle-pager'></div>";
		jQuery(this).find('.modal-body').empty();
		jQuery(this).find('.modal-body').html(contenido);
	});

	$(document).on("click", ".btnDetalle", function() {

		let idRegistro = $(this).attr("IDRegistroSocioProducto");

		let Names = ['<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?>'];
		let Model = [{
				name: 'Fecha',
				index: 'Fecha',
				align: "left"
			},
			{
				name: 'Evento',
				index: 'Evento',
				align: "left"
			},
			{
				name: 'FechaIniciaEvento',
				index: 'FechaIniciaEvento',
				align: "left"
			},
			{
				name: 'Usuario',
				index: 'u.Nombre',
				align: "left"
			},
			{
				name: 'Observacion',
				index: 'Observacion',
				align: "left",
				width: 300
			},
		]

		jQuery("#tablaDetalle").jqGrid({
			datatype: "json",
			colNames: Names,
			colModel: Model,
			url: 'includes/async/registroSocioProductoHistoria.async.php?idRegistro=' + idRegistro,
			rowNum: 10,
			rowList: [10, 40, 100],
			sortname: 'IDRegistroSocioProductoHistoria',
			viewrecords: true,
			sortorder: "DESC",
			caption: "",
			height: "100%",
			// shrinkToFit:false,
			// forceFit:true,
			// autowidth:true
			width: 1500,
			pager: "#tablaDetalle-pager",
			altRows: true,
			multiboxonly: true

		});
		jQuery("#tablaDetalle").jqGrid('filterToolbar', {
			defaultSearch: true,
			stringResult: true
		})
		jQuery("#tablaDetalle").filterToolbar({});

		$("#modalDetalle").modal("show");
	});

	$(document).on("click", ".btnConfigurar", function() {
		let idRegistro = $(this).attr("IDRegistroSocioProducto");
		let idProducto = $(this).attr("IDProductoFacturacion");

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "modal",
				tipo: "configuracion",
				idRegistro: idRegistro,
				idProducto: idProducto
			},
			dataType: "json",
			url: "includes/async/historicoservicios.async.php",
			success: function(data) {
				$("#opConfiguracion").html(data.menu);
				infoConfig = data.infoRegistro
				infoConfig['idRegistro'] = idRegistro;

				let tipoC = 'Días'

				if (infoConfig.TipoCongelacion == 3)
					tipoC = 'Meses'

				if (infoConfig.TipoCongelacion == 1)
					tipoC = 'Horas'

				$("#fechaInicio").text(infoConfig.FechaInicio);
				$("#fechaFin").text(infoConfig.FechaFin);
				$("#tipoCongela").text(tipoC);

				$(".infoConfig").hide();

				$("#modalConfigurar").modal("show");
			}
		});
	});

	$(document).on("click", "#idConfiguracion", function() {

		let idconf = $("#idConfiguracion").val();
		$("#divConfig input").each(function() {
			$(this).val("");
		});

		$("#fechaFinCalc").text();

		$(".infoConfig").hide();

		if (idconf == 1)
			$(".divFechaIni").show();

		if (idconf == 2)
			$(".divFechaFin").show();

		if (idconf == 3)
			$(".divCongela").show();

		if (idconf == 4)
			$(".divTransfiere").show();
	});

	$("#FechaInicioNew").blur(function() {
		let fechanew = $("#FechaInicioNew").val();
		let vigencia = Number(infoConfig.Vigencia);
		let tipo = Number(infoConfig.TipoVigencia);
		let fechaFin = infoConfig.FechaFin;
		let fechaFinNew = infoConfig.FechaFin;

		if (fechanew != '') {
			fechaFinNew = sumar_vigencia(fechanew, vigencia, tipo);
			fechaFin = fechaFinNew.toISOString().slice(0, 10);
		}

		$("#fechaFinCalc").text(fechaFin);
		$("#FechaFinNewCalc").val(fechaFin);
	});

	$("#numCongela").on("keyup", function() {
		let numCongela = $(this).val();
		let timeCongela = infoConfig.TimeCongelacion;

		if (numCongela > Number(timeCongela)) {
			$(this).val(0);
			alert("<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunnumero1y', LANGSESSION); ?> " + timeCongela);
		}
	});

	$("#guardarConf").on("click", function() {
		let idConf = $("#idConfiguracion").val();
		let FechaInicio = new Date(infoConfig.FechaInicio);
		let FechaFin = new Date(infoConfig.FechaFin);
		let arrMod = {};

		if (idConf == 1) {

			if ($("#FechaInicioNew").val() != "") {
				arrMod = {
					FechaInicioNew: $("#FechaInicioNew").val(),
					FechaFinNew: $("#FechaFinNewCalc").val(),
					FechaInicio: infoConfig.FechaInicio
				};
			} else {
				alert('<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunafechaparacontinuar', LANGSESSION); ?>.');
			}
		} else if (idConf == 2) {
			let FechaFinNew = new Date($("#FechaFinNew").val());
			let FechaFin = new Date(infoConfig.FechaFin);

			if ($("#FechaFinNew").val() != "") {
				if (FechaFinNew > FechaInicio) {
					arrMod = {
						FechaFinNew: $("#FechaFinNew").val(),
						FechaFin: infoConfig.FechaFin
					}
				} else {
					alert('<?= SIMUtil::get_traduccion('', '', 'Lafechafinalnopuedesermenoroigualalafechadeinicio', LANGSESSION); ?>');
				}
			} else {
				alert('<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunafechaparacontinuar', LANGSESSION); ?>.');
			}
		} else if (idConf == 3) {
			let FechaCongela = new Date($("#FechaCongela").val());

			if (($("#numCongela").val() != 0 || $("#numCongela").val() != "") && $("#FechaCongela").val() != "") {
				if (FechaCongela >= FechaInicio && FechaCongela < FechaFin) {
					arrMod = {
						numCongela: $("#numCongela").val(),
						FechaCongela: $("#FechaCongela").val(),
					}
				} else {
					alert("<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunafechavalida,entre', LANGSESSION); ?>" + infoConfig.FechaInicio + " <?= SIMUtil::get_traduccion('', '', 'y', LANGSESSION); ?> " + infoConfig.FechaFin);
				}
			} else {
				alert('<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunafechaparacontinuar', LANGSESSION); ?>.');
			}
		} else if (idConf == 4) {
			let FechaTransfiere = new Date($("#FechaTransfiere").val());
			if ($("#IDSocioTransfiere").val() != "" && $("#FechaTransfiere").val() != "") {
				if (FechaTransfiere >= FechaInicio && FechaTransfiere < FechaFin) {
					arrMod = {
						IDSocioTransfiere: $("#IDSocioTransfiere").val(),
						FechaTransfiere: $("#FechaTransfiere").val()
					}
				} else {
					alert("<?= SIMUtil::get_traduccion('', '', 'Porfavoringreseunafechavalida,entre', LANGSESSION); ?> " + infoConfig.FechaInicio + " <?= SIMUtil::get_traduccion('', '', 'y', LANGSESSION); ?> " + infoConfig.FechaFin);
				}
			} else {
				alert('<?= SIMUtil::get_traduccion('', '', 'Porfavorseleccioneunsocioofechavalidosparacontinuar', LANGSESSION); ?>.');
			}
		} else {
			alert('<?= SIMUtil::get_traduccion('', '', 'Porfavorseleccioneunaopciondeconfiguración', LANGSESSION); ?>');
		}

		if (Object.keys(arrMod).length != 0) {
			let arrConfiguracion = JSON.stringify(arrMod);

			jQuery.ajax({
				type: "POST",
				data: {
					oper: "modal",
					tipo: "guardar",
					idRegistro: infoConfig.idRegistro,
					idConf: idConf,
					arrMod: arrConfiguracion
				},
				dataType: "json",
				url: "includes/async/historicoservicios.async.php",
				success: function(data) {
					jQuery(grid_selector_service).trigger('reloadGrid');
					$("#modalConfigurar").modal("hide");
				}
			});
		}
	});

	function sumar_vigencia(fecha, valor, tipo) {
		fecha = new Date(fecha);
		if (tipo == 1) {
			fecha = new Date(fecha.setHours(fecha.getHours() + valor));
		} else if (tipo == 2) {
			fecha = new Date(fecha.setDate(fecha.getDate() + valor));
		} else if (tipo == 3) {
			fecha = new Date(fecha.setMonth(fecha.getMonth() + valor));
		}

		return fecha;
	}
</script>