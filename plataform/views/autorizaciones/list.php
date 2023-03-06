<?

$url_search = "";
if (SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
} //end if
?>




<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>Contratistas PENDIENTES DE INGRESO
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">


					<table id="grid-table"></table>

					<div id="grid-pager"></div>

					<script type="text/javascript">
						var $path_base = "."; //in Ace demo this will be used for editurl parameter
					</script>

					<!-- PAGE CONTENT ENDS -->
				</div>
			</div>
		</div>
	</div>

	<br><br>

	<div class="page-header">
		<h1>
			<a href="#" id="veringresado">Ver Contratistas que ya ingresaron</a>
			<small>
				<i class="ace-icon fa fa-angle-double-right"></i>
				<?= SIMUtil::tiempo(date("Y-m-d")) ?>
			</small>
		</h1>
	</div><!-- /.page-header -->


	<div id="divingresado" style="display:none">
		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">

						<table id="grid-tableingresado"></table>

						<div id="grid-pageringresado"></div>

						<script type="text/javascript">
							var $path_base = "."; //in Ace demo this will be used for editurl parameter
						</script>

						<!-- PAGE CONTENT ENDS -->
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<?php
//Consulta otro campo socio autorizacion
/*$sqlCampoContratista = "SELECT EtiquetaCampo FROM CampoFormularioContratista WHERE Orden=1 AND IDClub=" . SIMUser::get("club");
		$queryCampoContratista = $dbo->query($sqlCampoContratista);
		$campoContratista = $dbo->fetch($queryCampoContratista);*/
?>




<?
include("cmp/footer_grid.php");
?>


<!-- inline scripts related to this page -->
<script type="text/javascript">
	jQuery(function($) {

		$("#veringresado").click(function() {
			$("#divingresado").toggle();
			return false
		});


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





			url: 'includes/async/autorizaciones.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: ['Documento', 'Nombre', 'Tipo', 'Predio' /*, '<?php //echo $campoContratista["EtiquetaCampo"]
																	?>'*/ , 'Fecha Inicio', 'Fecha Fin', 'Socio', 'Accion', 'Observacion', 'Creada por', 'Fecha Creación Aut', 'Finalizar Aut.'],
			colModel: [

				{
					name: 'NumeroDocumento',
					index: 'NumeroDocumento',
					align: "left"
				},
				{
					name: 'Nombre',
					index: 'Nombre',
					align: "center"
				},
				{
					name: 'Tipo',
					index: 'Tipo',
					align: "center"
				},
				{
					name: 'Predio',
					index: 'Predio',
					align: "center"
				},
				//{name:'<?php //echo $campoContratista["EtiquetaCampo"]
							?>', index:'<?php // echo $campoContratista["EtiquetaCampo"]
										?>', align:"center"},
				{
					name: 'FechaInicio',
					index: 'FechaInicio',
					align: "center",
					autosearch: true,
					searchoptions: {
						// dataInit is the client-side event that fires upon initializing the toolbar search field for a column
						// use it to place a third party control to customize the toolbar
						dataInit: function(element) {
							$(element).datepicker({
									format: 'yyyy-mm-dd',
									//minDate: new Date(2010, 0, 1),


								})
								.on("changeDate", function(e) {
									$(grid_selector).trigger('triggerToolbar');
								});
						},
					}

				},
				{
					name: 'FechaFin',
					index: 'FechaFin',
					align: "center",
					autosearch: true,
					searchoptions: {
						// dataInit is the client-side event that fires upon initializing the toolbar search field for a column
						// use it to place a third party control to customize the toolbar
						dataInit: function(element) {
							$(element).datepicker({
									format: 'yyyy-mm-dd',
									//minDate: new Date(2010, 0, 1),


								})
								.on("changeDate", function(e) {
									$(grid_selector).trigger('triggerToolbar');
								});
						},
					}

				},
				{
					name: 'Socio',
					index: 'Socio',
					align: "left"
				},
				{
					name: 'Accion',
					index: 'Accion',
					align: "left"
				},
				{
					name: 'ObservacionSocio',
					index: 'ObservacionSocio',
					align: "left"
				},
				{
					name: 'CreadaPor',
					index: 'CreadaPor',
					align: "left"
				},
				{
					name: 'FechaCreacionAut',
					index: 'FechaCreacionAut',
					align: "left"
				},
				{
					name: 'FinalizarAut',
					index: 'FinalizarAut',
					align: "center"
				},

			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'FechaIngreso',
			viewrecords: true,
			sortorder: "ASC",
			caption: "Pendientes Ingreso",
			height: "100%",
			width: 855,
			multiselect: true,
			editurl: "includes/autorizaciones.async.php",





			pager: pager_selector,
			altRows: true,
			//toppager: true,

			multiselect: true,
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

			onCellSelect: function(rowid, icol, cellcontent, e) {
				if (icol == 1) {
					//var IDSocio = $(this).attr("rel");
					//var IDReserva = $(this).attr("id");
					//var IDClub = $(this).attr("lang");
					if (confirm("Esta seguro de registrar el ingreso?")) {
						jQuery.ajax({
							"type": "POST",
							"data": {
								"IDSocioAutorizacion": rowid
							},
							"dataType": "json",
							"url": "includes/async/ingreso_autorizacion.async.php",

							"success": function(data) {
								alert("Ingresado con exito");
								$("#grid-table").trigger("reloadGrid");
								$("#grid-tableingresado").trigger("reloadGrid");
								return false;
							}
						});
					}

					return false;
				} else {
					if (icol == 2 || icol == 3 || icol == 4 || icol == 5 || icol == 6 || icol == 7) {
						$("#detalle" + rowid).click();
						//location.href="autorizaciones.php?action=edit&id="+rowid;
						//$('.fancybox').click();
						//$.fancybox.open
						$.fancybox.open([{
							type: 'iframe',
							href: 'autorizaciones.php?action=editinfo&id=' + rowid,
							afterClose: function() { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
								$("#grid-table0").trigger("reloadGrid");
								$("#grid-table1").trigger("reloadGrid");
								$("#grid-table2").trigger("reloadGrid");
								$("#grid-table3").trigger("reloadGrid");
								$("#grid-table").trigger("reloadGrid");
							}

						}], {
							padding: 0
						});
						return false;
					}

				}
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
			search: true,
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




		//INGRESADOS


		var grid_selectoringresado = "#grid-tableingresado";
		var pager_selector = "#grid-pageringresado";

		//resize to fit page size
		$(window).on('resize.jqGrid', function() {
			$(grid_selectoringresado).jqGrid('setGridWidth', $(".page-content").width());
		})
		//resize on sidebar collapse/expand
		var parent_column = $(grid_selectoringresado).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
			if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selectoringresado).jqGrid('setGridWidth', parent_column.width());
				}, 0);
			}
		})



		jQuery(grid_selectoringresado).jqGrid({





			url: 'includes/async/autorizacionesingreso.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: ['Registrar Salida', 'Documento', 'Nombre', 'Tipo', 'Fecha de Ingreso', 'Socio'],
			colModel: [{
					name: 'Ingreso',
					index: 'Ingreso',
					align: "center"
				},
				{
					name: 'NumeroDocumento',
					index: 'NumeroDocumento',
					align: "left"
				},
				{
					name: 'Nombre',
					index: 'Nombre',
					align: "center"
				},
				{
					name: 'Tipo',
					index: 'Tipo',
					align: "center"
				},
				{
					name: 'FechaInicio',
					index: 'FechaInicio',
					align: "center",
					autosearch: true,
					searchoptions: {
						// dataInit is the client-side event that fires upon initializing the toolbar search field for a column
						// use it to place a third party control to customize the toolbar
						dataInit: function(element) {
							$(element).datepicker({
									format: 'yyyy-mm-dd',
									//minDate: new Date(2010, 0, 1),


								})
								.on("changeDate", function(e) {
									$(grid_selectoringresado).trigger('triggerToolbar');
								});
						},
					}

				},
				{
					name: 'Socio',
					index: 'Socio',
					align: "left"
				},


			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'FechaIngreso',
			viewrecords: true,
			sortorder: "ASC",
			caption: "Invitados",
			height: "100%",
			width: 855,
			multiselect: true,
			editurl: "includes/autorizaciones.async.php",





			pager: pager_selector,
			altRows: true,
			//toppager: true,

			multiselect: true,
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
			onCellSelect: function(rowid, icol, cellcontent, e) {

				if (icol == 1) {
					//var IDSocio = $(this).attr("rel");
					//var IDReserva = $(this).attr("id");
					//var IDClub = $(this).attr("lang");
					if (confirm("Esta seguro de registrar la salida?")) {
						jQuery.ajax({
							"type": "POST",
							"data": {
								"IDSocioAutorizacion": rowid,
								"Tipo": "Salida"
							},
							"dataType": "json",
							"url": "includes/async/ingreso_autorizacion.async.php",

							"success": function(data) {
								alert("Salida registrada con exito");
								$("#grid-table").trigger("reloadGrid");
								$("#grid-tableingresado").trigger("reloadGrid");
								return false;
							}
						});
					}

					return false;
				} else {
					if (icol == 2 || icol == 3 || icol == 4 || icol == 5 || icol == 6 || icol == 7) {
						$("#detalle" + rowid).click();
						//location.href="invitadosespeciales.php?action=edit&id="+rowid;
						//$('.fancybox').click();
						//$.fancybox.open
						$.fancybox.open([{
							type: 'iframe',
							href: 'autorizaciones.php?action=editinfo&id=' + rowid,
							afterClose: function() { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
								$("#grid-table0").trigger("reloadGrid");
								$("#grid-table1").trigger("reloadGrid");
								$("#grid-table2").trigger("reloadGrid");
								$("#grid-table3").trigger("reloadGrid");
								$("#grid-table").trigger("reloadGrid");
							}

						}], {
							padding: 0
						});
						return false;
					}

				}
			},








		});

		var datePick = function(elem) {
			jQuery(elem).datepicker();
		}


		$(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size



		//enable search/filter toolbar
		jQuery(grid_selectoringresado).jqGrid('filterToolbar', {
			defaultSearch: true,
			stringResult: true
		})
		jQuery(grid_selectoringresado).filterToolbar({});


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
		jQuery(grid_selectoringresado).jqGrid('navGrid', pager_selector, { //navbar options

			edit: false,
			editicon: 'ace-icon fa fa-pencil blue',
			add: false,
			addicon: 'ace-icon fa fa-plus-circle purple',
			del: false,
			delicon: 'ace-icon fa fa-trash-o red',
			search: true,
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
</script>