<?

$script = "votacionesvotante";
$url_search = "";
if (SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
} //end if

?>

<button class="btn btn-info" href="#" id="btnregistrovotante">
	<i class="ace-icon fa fa-user align-top bigger-125"></i>
	<?= SIMUtil::get_traduccion('', '', 'RegistrarVotante', LANGSESSION); ?>
</button>


<button class="btn btn-info fancybox" href="cargamasivavotante.php?IDVotacionEvento=<?php echo $_GET["id"]; ?>" data-fancybox-type="iframe">
	<i class="ace-icon fa fa-cloud-upload align-top bigger-125"></i>
	<?= SIMUtil::get_traduccion('', '', 'CargaArchivovotantes', LANGSESSION); ?>
</button>

<?php if (empty($_GET["IDVotacionVotante"]))
	$muestradiv = 'style="display:none"';
else
	$muestradiv = '';

?>

<div <?php echo $muestradiv; ?> id="divregistrovotante">
	<form class="form-horizontal formvalida" role="form" method="post" id="EditVotante<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

		<?php
		$action = "InsertarVotante";

		if ($_GET["IDVotacionVotante"]) {
			$EditVotante = $dbo->fetchAll("VotacionVotante", " IDVotacionVotante = '" . $_GET["IDVotacionVotante"] . "' ", "array");
			$action = "InsertarVotante";
		?>
			<input type="hidden" name="IDVotacionVotante" id="IDVotacionVotante" value="<?php echo $EditVotante["IDVotacionVotante"] ?>" />

		<?php
		}
		?>




		<div class="widget-header widget-header-large">
			<h3 class="widget-title grey lighter">
				<i class="ace-icon fa fa-user green"></i>
				<?= SIMUtil::get_traduccion('', '', 'Nuevovotante', LANGSESSION); ?>
			</h3>
		</div>


		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>

				<div class="col-sm-8">
					<input id=Nombre type=text size=25 name=Nombre class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?= $EditVotante["Nombre"]; ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>">
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Numerodecasa', LANGSESSION); ?>:</label>

				<div class="col-sm-8">
					<input id=NumeroCasa type=text size=25 name=NumeroCasa class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Numerodecasa', LANGSESSION); ?>" value="<?= $EditVotante["NumeroCasa"];  ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numerodecasa', LANGSESSION); ?>">
				</div>
			</div>
		</div>

		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>: </label>

				<div class="col-sm-8">
					<input id=Cedula type=number size=25 name=Cedula class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>" value="<?= $EditVotante["Cedula"]; ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>">
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?>:</label>

				<div class="col-sm-8">
					<input id=Coeficiente type=number size=25 name=Coeficiente class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?>" value="<?= $EditVotante["Coeficiente"];  ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?>" step="0.00000000001" min="0" max="100">
				</div>
			</div>
		</div>

		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Esconsejero', LANGSESSION); ?>: </label>

				<div class="col-sm-8">
					<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditVotante["Consejero"], "Consejero", "title=\"Consejero\"") ?>
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Moroso', LANGSESSION); ?>:</label>

				<div class="col-sm-8">
					<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditVotante["Moroso"], "Moroso", "title=\"Moroso\"") ?>
				</div>
			</div>
		</div>

		<div class="clearfix form-actions">
			<div class="col-xs-12 text-center">
				<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
				<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
				<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
				<input type="submit" class="submit btn btn-primary" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?> ">

				<input type="hidden" name="IDProyecto" id="IDProyecto" value="<?php echo $frm[$key] ?>" />
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />


			</div>
		</div>

	</form>



	<!--
							<div class="">
										<div  class="form-group first ">
											<div  class="col-xs-12 col-sm-6">
													<div class="col-sm-8">
														<button class="btn btn-info fancyboxpoder" href="registrapoder.php?IDVotacionVotante=<?php echo $_GET["IDVotacionVotante"]; ?>&IDClub=<?php echo SIMUser::get("club"); ?>&IDVotacionEvento=<?php echo $_GET["id"]; ?>&IDUsuarioRegistra=<?php echo SIMUser::get("IDUsuario"); ?>" data-fancybox-type="iframe">
														<i class="ace-icon fa  fa-exchange align-top bigger-125"></i>
														Registrar Poder
														</button>

													</div>
											</div>
										</div>

										<table id="simple-table" class="table table-striped table-bordered table-hover">
														<tr>
																		<th>Cedula</th>
																		<th>Nombre</th>
																		<th>Predio</th>
																		<th>Coeficiente</th>
																		<th>Moroso ?</th>
																		<th>Eliminar Poder</th>
														</tr>
														<tbody id="listacontactosanunciante">
														<?php
														$sql_poder = "SELECT IDVotacionVotanteDelegaPoder,IDVotacionPoder FROM VotacionPoder WHERE IDVotacionVotante = '" . $_GET["IDVotacionVotante"] . "'";
														$r_poder = $dbo->query($sql_poder);
														while ($row_poder = $dbo->fetchArray($r_poder)) {
															$datos_otorga = $dbo->fetchAll("VotacionVotante", " IDVotacionVotante = '" . $row_poder["IDVotacionVotanteDelegaPoder"] . "' ", "array");
														?>
															<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
																			<td><?php echo $datos_otorga["Cedula"]; ?></td>
																			<td><?php echo $datos_otorga["Nombre"]; ?></td>
																			<td><?php echo $datos_otorga["NumeroCasa"]; ?></td>
																			<td><?php echo $datos_otorga["Coeficiente"]; ?></td>
																			<td><?php echo $datos_otorga["Moroso"]; ?></td>
																			<td>
																				<?php echo '<a class="green" href="votacionesevento.php?action=EliminaPoder&IDVotacionPoder=' . $row_poder["IDVotacionPoder"] . '&tabclub=votantes&IDVotacionEvento=' . $datos_otorga["IDVotacionEvento"] . '">Eliminar</a>'; ?>
																			</td>
															</tr>
													<?php } ?>
														</tbody>

										</table>


							</div>
							-->












</div>



<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'listadode', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">



					<div id="jqGrid_container">
						<table id="grid-table-voto"></table>
					</div>

					<div id="grid-pager"></div>

					<script type="text/javascript">
						var $path_base = "."; //in Ace demo this will be used for editurl parameter
					</script>

					<!-- PAGE CONTENT ENDS -->
				</div>
			</div>
		</div>
	</div>
</div>





<!-- inline scripts related to this page -->

<script type="text/javascript">
	$('#btnregistrovotante').click(function() {
		$("#divregistrovotante").toggle("slow");
	});


	$(".fancyboxpoder").fancybox({
		maxWidth: 800,
		maxHeight: 600,
		fitToView: false,
		width: '80%',
		height: '80%',
		autoSize: false,
		closeClick: false,
		openEffect: 'none',
		closeEffect: 'none',
		afterClose: function() { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
			//parent.jQuery.fancybox.location.reload();
			parent.location.reload();
			//alert("Cerrar");
		}

	});



	jQuery(function($) {
		var grid_selector = "#grid-table-voto";
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





			url: 'includes/async/<?php echo $script; ?>.async.php<?= $url_search ?>?IDVotacionEvento=<?php echo $_GET["id"] ?>',
			datatype: "json",
			colNames: ['<?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Numerodecasa', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Consejero', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Moroso', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Registrado', LANGSESSION); ?>'],
			colModel: [{
					name: 'Editar',
					index: '',
					align: "center"
				},
				{
					name: 'Nombre',
					index: 'Nombre',
					align: "left"
				},
				{
					name: 'Cedula',
					index: 'Cedula',
					align: "left"
				},
				{
					name: 'NumeroCasa',
					index: 'NumeroCasa',
					align: "left"
				},
				{
					name: 'Coeficiente',
					index: 'Coeficiente',
					align: "left"
				},
				{
					name: 'Consejero',
					index: 'Consejero',
					align: "left"
				},
				{
					name: 'Moroso',
					index: 'Moroso',
					align: "left"
				},
				{
					name: 'Presente',
					index: 'Presente',
					align: "center"
				},

			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'Nombre',
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