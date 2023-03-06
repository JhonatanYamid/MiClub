<?
include('estilos.php');

$url_search = "";
if (SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
} //end if

?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-search orange"></i><?= SIMUtil::get_traduccion('', '', 'Consultarporfecha', LANGSESSION); ?>
		</h4>
	</div>

	<div id="filtroGrid" class="marginFiltro">
		<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="marginElem calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>"></td>
		<input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="marginElem calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>"></td>
		<button id="btFiltrar" class="btn btn-primary btn-sm">Buscar</button>
	</div>

	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-list orange"></i><?= SIMUtil::get_traduccion('', '', 'listadode', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("titleB"), LANGSESSION)); ?>
		</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">

					<div id="jqGrid_container" class="col-xs-12">
						<table id="grid-table"></table>
					</div>

					<div id="grid-pager"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- MODAL INGRESAR MOTIVO ANULACION -->
<div class="modal fade" id="modalAnular" tabindex="-1" role="dialog" aria-labelledby="LabelAnular" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-sm">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelAnular"> <?= ucwords(SIMUtil::get_traduccion('', '', 'anularfactura', LANGSESSION));?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<div class="container-fluid">
      				<div class="row">
					  	<div class="col-sm-12 text-left">
						  	<label class="col-sm-12 tallest"><strong><?= SIMUtil::get_traduccion('', '', 'motivodecancelacion', LANGSESSION);?></strong></label>
						</div>
						<div class="col-sm-12">
							<textarea id="motivo" name="motivo" cols='20' rows='3' value="" ></textarea>
							<input type="hidden" name="idFactura" id="idFactura" value="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
				<button type="button" class="btn btn-primary" id="anularFactura"><?= SIMUtil::get_traduccion('', '', 'Anular', LANGSESSION);?></button>
			</div>
		</div>
	</div>
</div>
<!-- MODAL MOTIVO ANULACION -->
<div class="modal fade" id="modalMotivo" tabindex="-1" role="dialog" aria-labelledby="LabelMotivo" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-sm">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelMotivo"> <?= ucwords(SIMUtil::get_traduccion('', '', 'motivodecancelacion', LANGSESSION));?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<div class="container-fluid">
      				<div class="row">
					  	<div class="col-sm-12 text-left">
						  	<label id="motivoTxt" class="col-sm-12 tallest"></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
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
		var grid_selector_list = "#grid-table";
		var pager_selector_list = "#grid-pager";

		//resize to fit page size
		$(window).on('resize.jqGrid', function() {
			$(grid_selector_list).jqGrid('setGridWidth', $(".page-content").width());
		})
		//resize on sidebar collapse/expand
		var parent_column = $(grid_selector_list).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
			if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selector_list).jqGrid('setGridWidth', parent_column.width());
				}, 0);
			}
		})

		jQuery(grid_selector_list).jqGrid({

			url: 'includes/async/<?php echo $script; ?>.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: [	<? if($IDClub == $idPadre && !empty($hijos))
							echo "'".SIMUtil::get_traduccion('', '', 'sede', LANGSESSION)."',";
						?> 
					   '<?= SIMUtil::get_traduccion('', '', 'factura', LANGSESSION); ?> No.', 
					   '<?= SIMUtil::get_traduccion('', '', 'fecha', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'hora', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Cliente', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'documento', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Valortotal', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>',
					   '<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?>'],
			colModel: [
				<? if($IDClub == $idPadre && !empty($hijos))
					echo "{name: 'Sede',index: 'c.Nombre',align: 'left',width: 200},"; 
				?> 
				
				{name: 'Consecutivo',index: 'Consecutivo',align: "left"},
				{name: 'FechaCreacion',index: 'FechaCreacion',align: "left"},
				{name: 'HoraCreacion',index: 'HoraCreacion',align: "left"},
				{name: 'Cliente',index: 'Cliente',align: "left"},
				{name: 'NumeroDocumento',index: 'NumeroDocumento',align: "left"},
				{name: 'Total',index: 'Total',align: "left", formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}},
				{name: 'Estado',index: 'Estado',align: "left"},
				{name: 'Accion',index: 'Accion',search: false,align: "center"},
			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'f.FechaTrCr',
			viewrecords: true,
			sortorder: "DESC",
			caption: "<?= SIMUtil::get_traduccion('', '', SIMReg::get("titleB"), LANGSESSION); ?>",
			height: "100%",
			width: 855,
			multiselect: true,
			editurl: "includes/async/<?php echo $script; ?>.async.php",
			pager: pager_selector_list,
			multiselect: false,

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
		jQuery(grid_selector_list).jqGrid('filterToolbar', {
			defaultSearch: true,
			stringResult: true
		})
		jQuery(grid_selector_list).filterToolbar({});


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
		jQuery(grid_selector_list).jqGrid('navGrid', pager_selector_list, { //navbar options
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

		function style_edit_form(form) {
			//enable datepicker on "sdate" field and switches for "stock" field
			form.find('input[name=sdate]').datepicker({
				format: 'yyyy-mm-dd',
				autoclose: true
			})

			form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');

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

		//var selr = jQuery(grid_selector_list).jqGrid('getGridParam','selrow');

		$(document).one('ajaxloadstart.page', function(e) {
			$(grid_selector_list).jqGrid('GridUnload');
			$('.ui-jqdialog').remove();
		});

		$(document).on("click", ".btnAnular", function (){ 
			var idFactura = $(this).attr("factura");
			$("#idFactura").val(idFactura);
			
			$("#modalAnular").modal("show");
		});
		
		$(document).on("click", "#anularFactura", function (){ 
			var idFactura = $("#idFactura").val();
			var motivo = $("#motivo").val();

			console.log(idFactura);
			jQuery.ajax({
				type: "GET",
				data: {
					oper: "anular",
					idFactura: idFactura,
					motivo: motivo
				},
				dataType: "json",
				url: "includes/async/facturacion.async.php",
				success: function (data) {
					//alert('entra');
					jQuery(grid_selector_list).trigger('reloadGrid');
					$("#modalAnular").modal("hide");
				}
			}); 
		});

		$(document).on("click", ".btnMotivo", function (){ 
			var motivo = $(this).attr("motivo");
			$("#motivoTxt").text(motivo);
			
			$("#modalMotivo").modal("show");
		});

		$(document).on("click", "#btFiltrar", function (){ 
			var fecha_inicio = jQuery("#FechaInicio").val();
			var fecha_fin = jQuery("#FechaFin").val();
			var ruta = 'includes/async/<?php echo $script; ?>.async.php<?=$url_search ?>';
			var dateFilter = {
					groupOp: "AND",
					rules: [
						{ "field": "FechaInicio", "op": "true", "data": fecha_inicio },
						{ "field": "FechaFin", "op": "true", "data": fecha_fin }
					]
			}
	
			$(grid_selector_list).jqGrid('setGridParam',{
				url:ruta,
				search:true,
				postData: {
					filters: JSON.stringify(dateFilter)
				}
			}).trigger('reloadGrid');
		});

		$(document).on("click", ".btnEstupendo", function (){ 
			var idFactura = $(this).attr("factura");
			
			jQuery.ajax({
				type: "GET",
				data: {
					oper: "crearArchivo",
					idFactura: idFactura
				},
				dataType: "json",
				url: "includes/async/estupendo.async.php",
				success: function (data) {
					alert("Archivo cargado;")
					//cargarArchivo(data['archivo'],data['nombre']);
				}
			}); 
		});

		function cargarArchivo(archivo, nombre){
			let urlEstupendo = '<?= URL_CARGAR_ESTUPENDO;?>';
			
			jQuery.ajax({
				type: "POST",
				data: {
					txtEncode: archivo
				},
				dataType: "json",
				url: urlEstupendo,
				success: function (data) {
					console.log(data);
				}
			}); 
		}

		// function respuestaFactura(){

		// }
	});

	$(window).bind('resize', function() {
		var width = $('#jqGrid_container').width();
		$('#jqList').setGridWidth(width);
	});
</script>