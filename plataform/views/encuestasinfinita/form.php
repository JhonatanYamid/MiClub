<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'CREARFORMULARIO', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">


					<div class="col-sm-12">
						<div class="tabbable" id="myTABS" role="tablist">
							<ul class="nav nav-tabs" id="myTab">
								<li class="<?php if (empty($_GET[tabencuesta])) echo "active"; ?>">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-home bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'DatosGenerales', LANGSESSION); ?>
									</a>
								</li>

								<?php if (SIMNet::req("action") == "edit") : ?>
									<li class="<?php if ($_GET[tabencuesta] == "formulario") echo "active"; ?>">
										<a data-toggle="tab" href="#messages">
											<i class="green ace-icon fa fa-check-circle bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Preguntas', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabencuesta] == "notificacionlocal") echo "active"; ?>">
										<a data-toggle="tab" href="#notificacion">
											<i class="green ace-icon fa fa-bell bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Notificaciones', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabencuesta] == "registros") echo "active"; ?>">
										<a data-toggle="tab" href="#invitaciones" role="tab">
											<i class="green ace-icon fa fa-ticket  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Respuestas', LANGSESSION); ?>
										</a>
									</li>

									<li class="<?php if ($_GET[tabencuesta] == "responderencuesta") echo "active"; ?>">
										<a data-toggle="tab" href="#responderencuesta" role="tab">
											<i class="green ace-icon fa fa-ticket  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'ResponderEncuesta', LANGSESSION); ?>
										</a>
									</li>

								<?php endif; ?>

							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabencuesta])) echo "in active"; ?> ">
									<?php include("encuesta.php"); ?>
								</div>

								<?php if (SIMNet::req("action") == "edit") : ?>
									<div id="messages" class="tab-pane fade <?php if ($_GET[tabencuesta] == "formulario") echo "in active"; ?>">
										<?php include("preguntas.php"); ?>
									</div>

									<div id="notificacion" class="tab-pane fade <?php if ($_GET[tabencuesta] == "notificacionlocal") echo "in active"; ?>">
										<?php include("notificacioneslocales.php"); ?>
									</div>
									<div id="responderencuesta" class="tab-pane fade <?php if ($_GET[tabencuesta] == "responderencuesta") echo "in active"; ?>">
										<?php include("responderencuesta.php"); ?>
									</div>

									<div id="invitaciones" class="tab-pane fade <?php if ($_GET[tabencuesta] == "registros") echo "in active"; ?>" role="tabpanel">
										<?php // include ("registro.php"); 
										?>

										<div style="width:100%;overflow:auto;">
											<?php if (count($array_NoPregunta) > 0) {
											?>
												<table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
													<tr>
														<td><input type="text" id="FechaInicioRep" name="FechaInicioRep" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>"></td>
														<td><input type="text" id="FechaFinRep" name="FechaFinRep" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d")  ?>"></td>
														<td>
															<input type="radio" name="tipoencabezado" value="Indice" checked><?= SIMUtil::get_traduccion('', '', 'Encabezadoconindices', LANGSESSION); ?><br>
															<!-- <input type="radio" name="tipoencabezado" value="Texto"> Encabezado con textos -->
														</td>
														<td align="center" style="width:220px;">
															<button type="button" class="btn btn-info btn-sm" id="btExporta">
																<span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
																<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>
															</button>
														</td>
													</tr>
												</table>
												<table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
													<tr>

														<!-- <input type="text" name="Cedula" id="Cedula" class="form-control search-query " placeholder="<?= SIMUtil::get_traduccion('', '', 'Ingreselosdatosdebúsqueda', LANGSESSION); ?>">
														<button type="button" class="btn btn-purple btn-sm " id="btnBuscar" rel="frmfrmBuscar">
															<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
															
															<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
														</button>

														
 -->
														<td>
															<input type="text" name="Cedula" id="Cedula" class="form-control search-query " placeholder="Ingrese número de documento">
														</td>
														<td>

															<button id="btnBuscar" class="btn btn-primary btn-sm"><?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?></button>

														</td>
													</tr>
												</table>
											<?php
											} // end if count prgunta
											?>
											<table id="grid-table"></table>
											<div id="grid-pager"></div>
										</div>
									</div>


								<?php
								endif; ?>

							</div>
						</div>
					</div>



				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
include("cmp/footer_grid.php");
?>
<script type="text/javascript">
	jQuery(function($) {

		$("#btExporta").click(function() {
			var Inicio = $("#FechaInicioRep").val();
			var Fin = $("#FechaFinRep").val();
			var TipoEncabezado = $('input:radio[name=tipoencabezado]:checked').val();
			if (TipoEncabezado == "Indice") {
				window.location.href = "./procedures/excel-Encuesta.php?id=<?php echo $_GET["id"]; ?>&" + '&FechaInicio=' + Inicio + '&FechaFin=' + Fin;
			} else {
				window.location.href = "./procedures/excel-encuesta-respuesta.php?id=<?php echo $_GET["id"]; ?>&" + '&FechaInicio=' + Inicio + '&FechaFin=' + Fin;
			}


		});

		var grid_selector = "#grid-table";
		var pager_selector = "#grid-pager";



		<?php if (count($array_NoPregunta) > 0) {

		?>

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

			$('#myTABS a[href="#invitaciones"]').on('click', function(e) {
				jQuery(grid_selector).jqGrid({
					// url: 'includes/async/get_encuestasresp.async.php?id=<?php echo $_GET["id"]; ?>',
					url: 'includes/async/get_Encuesta.async.php?id=<?php echo $_GET["id"]; ?>',
					datatype: "json",
					//colNames:['Usuario','Valor','Fecha' ],
					<?php

					echo "colNames: ['Eliminar','Usuario','Fecha','Predio','Numero de casa','" . str_replace(",", "','", implode(",", $array_preguntas)) . '\'],'; ?>
					colModel: [{
							name: 'Eliminar',
							index: '',
							width: '70',
							align: 'center',
							sortable: false
						},
						{
							name: 'Nombre',
							index: 'Nombre',
							width: '190',
							sortable: false
						},
						{
							name: 'Fecha',
							index: 'Fecha',
							width: '90',
							sortable: false
						},
						{
							name: 'Predio',
							index: 'Predio',
							width: '90',
							sortable: false
						},
						{
							name: 'NumeroDeCasa',
							index: 'NumeroDeCasa',
							width: '90',
							sortable: false
						},
						<?php $numcols = 1;
						foreach ($array_NoPregunta as $colData) {
							echo "{name:'" . $colData . "',index:'" . $colData . "', align:'left',width:'200',sortable:false},";
							$numcols++;
						}
						?>
					],
					rowNum: 200,
					rowList: [200, 300, 400, 500, 600, 700, 1000],
					sortname: 'Nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption: "",
					height: "100%",
					pager: pager_selector,
					altRows: true,
					toppager: true,
					//multikey: "ctrlKey",
					//multiboxonly: true,
					loadComplete: function() {
						var table = this;
						setTimeout(function() {
							updatePagerIcons(table);
							enableTooltips(table);
						}, 0);
					},


				});
			});
		<?php
		} // END IF count preguntas
		?>
		$(window).on('resize.jqGrid', function() {
			$(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
		});

		//resize on sidebar collapse/expand
		var parent_column = $(grid_selector).closest('[class*="col-"]');




		$(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size 





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

		$(document).ready(function() {

			$('#btnBuscar').click(function() {

				var cedula = jQuery("#Cedula").val();
				//alert('Cedula' + Cedula);
				//var fecha_fin = jQuery("#FechaFin").val();
				var ruta = 'includes/async/get_Encuesta.async.php?id=<?php echo $_GET["id"]; ?><?= $url_search ?>';
				var dateFilter = {
					groupOp: "AND",
					rules: [{
							"field": "Cedula",
							"op": "true",
							"data": cedula
						}

					]
				}

				$("#grid-table").jqGrid('setGridParam', {
					url: ruta,
					search: true,
					postData: {
						filters: JSON.stringify(dateFilter)
					}
				}).trigger('reloadGrid');
			});

		});
	});

	$(window).bind('resize', function() {
		var width = $('#jqGrid_container').width();
		$('#jqList').setGridWidth(width);
	});
</script>