<?

$url_search = "";
if (SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
}  
if (SIMNet::req("action") == "searchDate") {
	$url_search = "?oper=searchDate&inicio=" . SIMNet::get("inicio") . "&fin=" . SIMNet::get("fin");
} 

?>

<div class="widget-box transparent" id="recent-box">

	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-search orange"></i><?= SIMUtil::get_traduccion('', '', 'Consultar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
		</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form name="frmexportaviaje" id="frmexportaviaje" method="post" enctype="multipart/form-data" action="procedures/excel-carpool.php">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
								<td>Fecha de viaje inicial:</td>
								<td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="FechaInicio" value="<?php echo date("Y-m-d") ?>"></td>
								<td>Fecha de viaje final:</td>
								<td><input type="text" id="FechaFinal" name="FechaFinal" placeholder=" Fecha Final" class="col-xs-12 calendar" title="FechaFinal" value="<?php echo date("Y-m-d") ?>"></td>
								<td>Vehiculo:</td>
								<td><?php echo SIMHTML::formPopUp("TipoVehiculo", "Nombre", "Nombre", "IDTipoVehiculo", "", "[Seleccione]", "form-control", "", ""); ?></td>
							</tr>
							<tr>
								<td>Calificación desde:</td>
								<td><input type="number" name="calificacioninicial" id="calificacioninicial" class="form-control" value=""></td>
								<td>Calificación hasta:</td>
								<td><input type="number" name="calificacionfinal" id="calificacionfinal" class="form-control" value=""></td>
								<td>Motivo de calificación:</td>
								<td><?php echo SIMHTML::formPopUp("MotivosCalificacion", "Nombre", "Nombre", "IDMotivosCalificacion", "", "[Seleccione]", "form-control", "", ""); ?></td>
							</tr>
							<tr>
								<td>Creado por:</td>
								<td><input type="text" name="Persona" id="Persona" class="form-control" value=""></td>
								<td>Estado:</td>
								<td>
									<select name="Estado" id="Estado">
										<option value=1>Abierto</option>
										<option value=2>Cerrado</option>
										<option value=3>Cancelado</option>
									</select>
								</td>
								<td></td><td></td>
							</tr>
							<tr>
								<td colspan="4">
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
									<input class="btn btn-info" type="submit" name="exportarviajes" id="exportarviajes" value="<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>">
									<input class="btn btn-purple" type="button" name="buscarviajes" id="buscarviajes" rel="<?= $script; ?>.php?action=searchTravel" value="<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>">
									<input class="btn btn-primary btnRedirect" type="button" id="buscarTodos" rel="<?php echo $script; ?>.php?action=search" value="<?= SIMUtil::get_traduccion('', '', 'VerTodos', LANGSESSION); ?>">
								</td>
							</tr>
                        </table>
                    </form>
				</div>
			</div>
		</div>
	</div>

	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-list orange"></i><?= SIMUtil::get_traduccion('', '', 'listadode', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>
	
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<div id="jqGrid_container">
						<table id="grid-table"></table>
					</div>

					<div id="grid-pager"></div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalSolicitudes" tabindex="-1" role="dialog" aria-labelledby="LabelSolicitudes" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelSolicitudes"><?= SIMUtil::get_traduccion('', '', 'Solicitudes', LANGSESSION); ?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:auto;">
				<table id="tablaSolicitudes" ></table>
				<div id="tablaSolicitudes-pager"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?
include("cmp/footer_grid.php");
?>

<!-- inline scripts related to this page -->
<script type="text/javascript">
	jQuery(function($) {
		var grid_selector = "#grid-table";
		var pager_selector = "#grid-pager";

		//resize to fit page size
		$(window).on('resize.jqGrid', function() {
			$(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
		})
		//resize on sidebar collapse/expand
		var parent_column = $(grid_selector).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
			if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selector).jqGrid('setGridWidth', parent_column.width());
				}, 0);
			}
		})

		jQuery(grid_selector).jqGrid({

			url: 'includes/async/<?php echo $script; ?>.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: ['<?= SIMUtil::get_traduccion('', '', 'Creadopor', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Origen', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Destino', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Lugardeencuentro', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Vehiculo', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Cupos', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Disponibles', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Valor', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?>'],
			colModel: [
				{name: 'Persona', index: 'Persona', align: "left"},
				{name: 'Origen', index: 'Origen', align: "left"},
				{name: 'Destino', index: 'Destino', align: "left"},
				{name: 'Fecha', index: 'Fecha', align: "left", width:80},
				{name: 'Hora', index: 'Hora', align: "left", width:80},
				{name: 'LugarEncuentro', index: 'LugarEncuentro', align: "left", width:150},
				{name: 'TipoVehiculo', index: 'TipoVehiculo', align: "left"},
				{name: 'CuposTotales', index: 'CuposTotales', align: "left", width:60},
				{name: 'CuposDisponibles', index: 'CuposDisponibles', align: "left", width:80},
				{name: 'ValorCupo', index: 'ValorCupo', align: "left", width:70, formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},
				{name: 'Estado', index: 'Estado', align: "left"},
				{name: 'Accion', index: '', search: false, align: "center"}
			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'Fecha',
			viewrecords: true,
			sortorder: "DESC",
			caption: "<?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>",
			height: "100%",
			width: 855,
			multiselect: true,
			editurl: "includes/async/<?php echo $script; ?>.async.php",
			pager: pager_selector,
			altRows: true,
			//toppager: true,

			multiselect: false,
			//multikey: "ctrlKey",
			multiboxonly: true,

			loadComplete: function() {
				var table = this;
				setTimeout(function() {
					styleCheckbox(table);

					updateActionIcons(table);
					updatePagerIcons(table);
					enableTooltips(table);
				}, 0);

				preparaform();
			},
		});

		var datePick = function(elem) {
			jQuery(elem).datepicker();
		}

		$(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size

		//enable search/filter toolbar
		jQuery(grid_selector).jqGrid('filterToolbar', {
			defaultSearch: true,
			stringResult: true
		})
		jQuery(grid_selector).filterToolbar({});


		//switch element when editing inline
		function aceSwitch(cellvalue, options, cell) {
			setTimeout(function() {
				$(cell).find('input[type=checkbox]')
					.addClass('ace ace-switch ace-switch-5')
					.after('<span class="lbl"></span>');
			}, 0);
		}
		//enable datepicker
		function pickDate(cellvalue, options, cell) {
			setTimeout(function() {
				$(cell).find('input[type=text]')
					.datepicker({
						format: 'yyyy-mm-dd',
						autoclose: true
					});
			}, 0);
		}


		//navButtons
		jQuery(grid_selector).jqGrid('navGrid', pager_selector, { //navbar options
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
			//edit record form
			//closeAfterEdit: true,
			//width: 700,
			recreateForm: true,
			beforeShowForm: function(e) {
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
				style_edit_form(form);
			}
		}, {
			//new record form
			//width: 700,
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
			/**
			multipleGroup:true,
			showQuery: true
			*/
		}, {
			//view record form
			recreateForm: true,
			beforeShowForm: function(e) {
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
			}
		})



		function style_edit_form(form) {
			//enable datepicker on "sdate" field and switches for "stock" field
			form.find('input[name=sdate]').datepicker({
				format: 'yyyy-mm-dd',
				autoclose: true
			})

			form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
			//don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
			//.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');


			//update buttons classes
			var buttons = form.next().find('.EditButton .fm-button');
			buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide(); //ui-icon, s-icon
			buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
			buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')

			buttons = form.next().find('.navButton a');
			buttons.find('.ui-icon').hide();
			buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
			buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
		}

		function style_delete_form(form) {
			var buttons = form.next().find('.EditButton .fm-button');
			buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide(); //ui-icon, s-icon
			buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
			buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
		}

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

		function beforeDeleteCallback(e) {
			var form = $(e[0]);
			if (form.data('styled')) return false;

			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
			style_delete_form(form);

			form.data('styled', true);
		}

		function beforeEditCallback(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
			style_edit_form(form);
		}


		//it causes some flicker when reloading or navigating grid
		//it may be possible to have some custom formatter to do this as the grid is being created to prevent this
		//or go back to default browser checkbox styles for the grid
		function styleCheckbox(table) {
			/**
					$(table).find('input:checkbox').addClass('ace')
					.wrap('<label />')
					.after('<span class="lbl align-top" />')
			
			
					$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
					.find('input.cbox[type=checkbox]').addClass('ace')
					.wrap('<label />').after('<span class="lbl align-top" />');
				*/
		}

		//unlike navButtons icons, action icons in rows seem to be hard-coded
		//you can change them like this in here if you want
		function updateActionIcons(table) {
			/**
			var replacement = 
			{
				'ui-ace-icon fa fa-pencil' : 'ace-icon fa fa-pencil blue',
				'ui-ace-icon fa fa-trash-o' : 'ace-icon fa fa-trash-o red',
				'ui-icon-disk' : 'ace-icon fa fa-check green',
				'ui-icon-cancel' : 'ace-icon fa fa-times red'
			};
			$(table).find('.ui-pg-div span.ui-icon').each(function(){
				var icon = $(this);
				var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
				if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
			})
			*/
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

		//var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');

		$(document).one('ajaxloadstart.page', function(e) {
			$(grid_selector).jqGrid('GridUnload');
			$('.ui-jqdialog').remove();
		});

		//Grilla Solicitudes
		jQuery("#tablaSolicitudes").jqGrid({
			datatype: "json",
			colNames: [
				'<?= SIMUtil::get_traduccion('', '', 'Solicitadopor', LANGSESSION); ?>', 
				'<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>', 
				'<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>', 
				'<?= SIMUtil::get_traduccion('', '', 'Calificacion', LANGSESSION); ?>', 
				'<?= SIMUtil::get_traduccion('', '', 'Motivo', LANGSESSION); ?>'
			],
			colModel: [
				{name: 'Persona', index: 'Persona', align: "left", width:170},
				{name: 'FechaTrCr', index: 'FechaTrCr', align: "left", width:170},
				{name: 'Estado', index: 'Estado', align: "left", width:120},
				{name: 'Calificacion', index: 'Calificacion', align: "left", width:120},
				{name: 'MotivosCalificacion', index: 'MotivosCalificacion', align: "left", width:270},
			],
			rowNum: 10,
			rowList: [10, 40, 100],
			sortname: 'FechaTrCr',
			viewrecords: true,
			sortorder: "DESC",
			caption: "",
			height: "100%",
			pager: "#tablaSolicitudes-pager",
			altRows: true,
			multiboxonly: true,

			loadComplete: function () {
				$("#tablaSolicitudes").jqGrid('setGridWidth', 850, true);
			},

		});
		
		jQuery("#tablaSolicitudes").jqGrid('filterToolbar', {defaultSearch: true, stringResult: true})
		jQuery("#tablaSolicitudes").filterToolbar({});

		$(document).on("click", ".btnSolicitudes", function ()
		{
			var idViaje = $(this).attr("viaje");
			$("#modalSolicitudes").modal("show");
			
			jQuery("#tablaSolicitudes").jqGrid('setGridParam',{url:'includes/async/solicitudesviaje.async.php?idViaje='+idViaje}).trigger("reloadGrid");

		});
	});

	$(window).bind('resize', function() {
		var width = $('#jqGrid_container').width();
		$('#jqList').setGridWidth(width);
	});
</script>