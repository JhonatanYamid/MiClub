<?php
setlocale(LC_ALL, 'es_ES');
$url_search = "";

if (SIMNet::req("action") == "search") {
	$url_search = "?oper=searchurl&FechaInicio=" . SIMNet::get("FechaInicio") . "&FechaFin=" . SIMNet::req("FechaFin") . "&FechaHistorico=" . SIMNet::req('FechaHistorico') . "&Categoria=" . SIMNet::req('Categoria');
}
//end if
// $data_chartFormaPago = '';
// foreach ($array_dataFormaPago as  $FormaPago) {
// 	list($tipo, $total) = explode("|", $FormaPago);
// 	$data_chartFormaPago .= "['" . $tipo . "'," . $total . "],";
// }

// Meses historico

if (!empty($frm_get['FechaHistorico'])) {
	$FechaHistorico = $frm_get['FechaHistorico'];
} else {
	$FechaHistorico = date('Y-m-d');
}
$MesActual = date('m', strtotime($FechaHistorico));
$MesAntes = date('m', strtotime('-1 months', strtotime($FechaHistorico)));
$DosMesAntes = date('m', strtotime('-2 months', strtotime($FechaHistorico)));
$TresMesAntes = date('m', strtotime('-3 months', strtotime($FechaHistorico)));


$MesActual = SIMResources::$meses[(int)$MesActual - 1];
$MesAntes = SIMResources::$meses[(int)$MesAntes - 1];
$DosMesAntes = SIMResources::$meses[(int)$DosMesAntes - 1];
$TresMesAntes = SIMResources::$meses[(int)$TresMesAntes - 1];

// Fin Meses historico

?>
<style>
	.flex {
		-webkit-box-flex: 1;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto
	}

	#container {
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	@media (max-width:991.98px) {
		.padding {
			padding: 1.5rem
		}
	}

	@media (max-width:767.98px) {
		.padding {
			padding: 1rem
		}
	}

	.padding {
		padding: 5rem
	}

	.card {
		background: #fff;
		border-width: 0;
		border-radius: .25rem;
		box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
		margin-bottom: 1.5rem
	}

	.card {
		position: relative;
		display: flex;
		flex-direction: column;
		min-width: 0;
		word-wrap: break-word;
		background-color: #fff;
		background-clip: border-box;
		border: 1px solid rgba(19, 24, 44, .125);
		border-radius: .25rem
	}

	.card-header {
		padding: .75rem 1.25rem;
		margin-bottom: 0;
		background-color: rgba(19, 24, 44, .03);
		border-bottom: 1px solid rgba(19, 24, 44, .125)
	}

	.card-body {
		width: 100%;
		height: 100%;
	}

	.card-header:first-child {
		border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
	}

	.card-footer,
	.card-header {
		background-color: transparent;
		border-color: rgba(160, 175, 185, .15);
		background-clip: padding-box
	}

	.card-info,
	.card-charts {
		background-color: #97b1c8;
		border-radius: 1rem;
		text-align: center;
		color: #000;
		margin: 5px;
		padding-top: 1rem;
		padding-bottom: 3rem;
	}

	.card-charts {
		background-color: #b5ddb7;
		padding: 1rem;
	}

	#top_x_div {
		width: 100%;
		height: 100%;
	}
</style>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="tabbable" id="myTABS" role="tablist">
				<ul class="nav nav-tabs" id="myTab">
					<li class="active">
						<a data-toggle="tab" href="#home">
							<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
							Seguimiento de encuestas IPP
						</a>
					</li>
					<li class="">
						<a data-toggle="tab" href="#historico" role="tab">
							<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
							Historico IPP
						</a>
					</li>
					<li class="">
						<a data-toggle="tab" href="#graficas" role="tab">
							<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
							Graficas IPP
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="home" class="tab-pane fade in active">
						<?php include("seguimiento.php") ?>
					</div>
					<div id="historico" class="tab-pane fade in">
						<?php include("historico.php") ?>
					</div>
					<div id="graficas" class="tab-pane fade in">
						<?php include("graficas.php") ?>
					</div>
				</div>
			</div>
		</div>
	</div>


</div>
</div>
<?
include("cmp/footer_grid_chart.php");
include("cmp/footer_grid.php");

// include("cmp/footer_scripts.php");
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#btExporta").click(function() {
			var AnioInicio = $("#AnioInicio").val();
			var MesInicio = $("#MesInicio").val();
			var TipoEncabezado = $('input:radio[name=tipoencabezado]:checked').val();
			window.location.href = "./procedures/excel-reporte-historial-socios.php?a=" + AnioInicio + "&m=" + MesInicio;
		});
	});
</script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
	jQuery(function($) {
		var grid_selector = "#grid-table2";
		var pager_selector = "#grid-pager2";

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
			url: 'includes/async/<?php echo $script; ?>historico.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: [
				'<?= SIMUtil::get_traduccion('', '', 'Ciudad', LANGSESSION); ?>',
				'<?= $TresMesAntes ?>',
				'<?= $DosMesAntes ?>',
				'<?= $MesAntes ?>',
				'<?= $MesActual ?>',
				'Obj IPP',
				'VS Obj',
				'VS Mes Ant',
				'VS Trim',
			],
			colModel: [{
					name: 'Ciudad',
					index: 'Ciudad',
					align: "center"
				},
				{
					name: 'mes3',
					index: 'mes3',
					align: "left"
				},
				{
					name: 'mes2',
					index: 'mes2',
					align: "left"
				},
				{
					name: 'mes1',
					index: 'mes1',
					align: "left"
				},
				{
					name: 'mes0',
					index: 'mes0',
					align: "center"
				},
				{
					name: 'ObjIpp',
					index: 'ObjIpp',
					align: "left"
				},
				{
					name: 'VsObj',
					index: 'VsObj',
					align: "center"
				},
				{
					name: 'VsMesAnt',
					index: 'VsMesAnt',
					align: "center"
				},
				{
					name: 'VsTrim',
					index: 'VsTrim',
					align: "center"
				},

			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'IDCiudad',
			viewrecords: true,
			sortorder: "ASC",
			caption: "<?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>",
			height: "100%",
			width: 855,
			multiselect: true,
			editurl: "includes/async/<?php echo $script; ?>historico.async.php",





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

			onSelectRow: function(id) {
				location.href = "<?php echo $script ?>.php?action=edit&id=" + id;
				return false;
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
	});

	$(window).bind('resize', function() {
		var width = $('#jqGrid_container').width();
		$('#jqList').setGridWidth(width);
	});
</script>
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
			colNames: [
				'<?= SIMUtil::get_traduccion('', '', 'Zona', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Ejecutivos', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Objetivos encuesta', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Encuestas', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Cumplimiento encuesta', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Ideal', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Faltante', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Proyeccion cierre', LANGSESSION); ?>',
				'<?= SIMUtil::get_traduccion('', '', 'Proyeccion', LANGSESSION); ?>'
			],
			colModel: [{
					name: 'Zona',
					index: 'Zona',
					align: "center"
				},
				{
					name: 'Ejecutivos',
					index: 'Ejecutivos',
					align: "left"
				},
				{
					name: 'Objetivos',
					index: 'Objetivos',
					align: "left"
				},
				{
					name: 'Encuestas',
					index: 'Encuestas',
					align: "left"
				},
				{
					name: 'Cumplimiento',
					index: 'Cumplimiento',
					align: "center"
				},
				{
					name: 'Ideal',
					index: 'Ideal',
					align: "left"
				},
				{
					name: 'Faltante',
					index: 'Faltante',
					align: "center"
				},
				{
					name: 'Proyeccion_cierre',
					index: 'Proyeccion_cierre',
					align: "center"
				},
				{
					name: 'Proyeccion',
					index: 'Proyeccion',
					align: "center"
				},

			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'Zona',
			viewrecords: true,
			sortorder: "ASC",
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

			onSelectRow: function(id) {
				location.href = "<?php echo $script ?>.php?action=edit&id=" + id;
				return false;
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
	});

	$(window).bind('resize', function() {
		var width = $('#jqGrid_container').width();
		$('#jqList').setGridWidth(width);
	});
</script>