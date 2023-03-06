<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	include("cmp/seo.php");

	$url_search = "";
	if( $_GET["action"] == "search" )
	{
		$url_search = "?oper=search_url&ids=" . $ids . "&IDElemento=" . SIMNet::get("IDElemento") . "&Accion=" . SIMNet::get("Accion") . "&Fecha=" . SIMNet::get("Fecha");
	}//end if

	$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '".$_GET["ids"]."'");
	//$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro  . "'");

	$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get( "club" )."' and IDServicioMaestro = '" . $id_servicio_maestro . "'" );
	if(empty($nombre_servicio_personalizado))
		$nombre_servicio_personalizado = $nombre_servicio_personalizado;

?>

<script>
 function envia_planilla(){
	 	var FechaInicio = document.getElementById("FechaInicio").value;
		var FechaFin = document.getElementById("FechaFin").value;
		window.location.href="downloadreservagolf.php?FechaInicio="+FechaInicio+"&FechaFin="+FechaFin+"&IDClub=<?php echo SIMUser::get("club"); ?>&IDServicio=<?php echo $ids?>";
		return false;
}
</script>

	</head>

	<body class="no-skin">
		<?
			include( "cmp/header.php" );
		?>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<?
				$menu_reservas[$ids] = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="reservas.php?ids=<?=$_GET["ids"]?>">Home</a>
							</li>

							<li>
								<a href=""><?=$datos_club["Nombre"] ?></a>
							</li>

							<li class="active"><a href="reservas.php?ids=<?=$_GET["ids"]?>">Reservas <?=$nombre_servicio_personalizado; ?></a></li>
						</ul><!-- /.breadcrumb -->


					</div>

					<div class="page-content">
						<div class="page-header">
							<h1>
							<i class="ace-icon fa fa-angle-double-right"></i>
								Reservas <?=$nombre_servicio_personalizado; ?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Listado de Reservas
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<div class="tabbable">
									<ul class="nav nav-tabs" id="myTab">
										<li>
											<a  class="noTabLink" href="reservas.php?ids=<?php echo $_GET["ids"]; ?>">
												<i class="green ace-icon fa fa-calendar bigger-120"></i>
												Reservas
											</a>
										</li>

										<?php 
											$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil") , "PermisoConfiguracion");
											if(SIMUser::get("IDPerfil")<=2 || SIMUser::get("IDPerfil")==21 || SIMUser::get("IDPerfil")==22 || SIMUser::get("IDPerfil")==23 || SIMUser::get("IDPerfil")==27 || SIMUser::get("IDPerfil")==31
													|| SIMUser::get("IDPerfil")==32 || SIMUser::get("IDPerfil")==30 || SIMUser::get("IDPerfil")==10 || SIMUser::get("IDPerfil")==7 || $Permiso == 1): ?>
										<li >
											<a data-toggle="tab" class="noTabLink" href="serviciosclub.php?action=edit&ids=<?=$ids ?>">
												<i class="green ace-icon fa fa-gear bigger-120"></i>
												Configuración
											</a>
										</li>
										<?php endif; 
											$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil") , "PermisoExportar");
											if($Permiso == 1):
											?>									
												<li >
													<a data-toggle="tab" class="noTabLink" href="exportareserva.php?action=edit&ids=<?=$ids ?>">
														<i class="green ace-icon fa fa-download bigger-120"></i>
														Exportar Reservas
													</a>
												</li>
												<li>
													<a data-toggle="tab" class="noTabLink" href="exportarsanciones.php?action=edit&ids=<?=$ids ?>">
														<i class="green ace-icon fa fa-download bigger-120"></i>
														Exportar Sanciones
													</a>
												</li>

												<li >
													<a data-toggle="tab" class="noTabLink" href="exportareservaeliminada.php?action=edit&ids=<?=$ids ?>">
														<i class="green ace-icon fa fa-download bigger-120"></i>
														Exportar Reservas Eliminadas
													</a>
												</li>
											<?php endif;?>
										<li >
											<a data-toggle="tab" class="noTabLink" href="listaespera.php?action=edit&ids=<?=$ids ?>">
												<i class="green ace-icon fa fa-bell-o bigger-120"></i>
													Inscritos Lista de espera
											</a>
										</li>

										<?php if(SIMUser::get("club")==8 || SIMUser::get("club") == 112){ ?>
												<li class="active">
													<a data-toggle="tab" class="noTabLink" href="cargamasivareservas.php?action=edit&ids=<?=$ids ?>">
														<i class="green ace-icon fa fa-bell-o bigger-120"></i>
															Cargar reservas
													</a>
												</li>
										<?php } ?>
									</ul>
									<div class="tab-content">
										<div id="home" class="tab-pane fade in active">
											<div class="widget-box transparent" id="recent-box">
												<div class="widget-body">
													<div class="widget-main padding-4">
														<div class="row">
															<div class="col-sm-12 widget-container-col ui-sortable">
																<form class="form-horizontal formvalida" role="form" method="POST" name="frm" id="frm" action="cargamasivareservas.php" enctype="multipart/form-data">
																	<div  class="form-group first ">

																		<div  class="col-xs-12 col-sm-6">
																				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

																				<div class="col-sm-8">
																					<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>" >
																				</div>
																		</div>

																		<div  class="col-xs-12 col-sm-6">
																				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

																				<div class="col-sm-8">
																					<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>" >
																				</div>
																		</div>
																	</div>
																	<div  class="form-group first ">
																		<div  class="col-xs-12 col-sm-6">
																			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Archivo </label>

																			<div class="col-sm-8">
																				<input name="file" type="file" size="20" class="form-control" required="required" />
																			</div>
																		</div>
																	</div>

																	<div  class="form-group first ">

																		<div  class="col-xs-12 col-sm-12">
																				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias </label>

																				<div class="col-sm-8">
																					<?php
																					foreach($Dia_array as $id_dia => $dia):  ?>
																						<input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>"><?php echo $dia; ?>
																					<?php endforeach; ?>
																				</div>
																		</div>

																	</div>


																	<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>"  >
																	<input type="hidden" name="IDServicio" id="IDServicio" value="<?=$ids ?>"  >
																	<input type="hidden" name="action" id="action" value="cargareservas">
																	<button class="btn btn-info btnEnviar" type="button" rel="frm" >
																		<i class="ace-icon fa fa-cloud-download bigger-110"></i>
																		Cargar
																	</button>

																</form>

																<br>
																<div class="widget-header widget-header-large">
																	<h3 class="widget-title grey lighter">
																		<i class="ace-icon fa fa-archive green"></i>
																		Lista de archivos que han sido cargados
																	</h3>
																</div>
																<form class="form-horizontal formvalida" role="form" method="POST" name="frm1" id="frm1" action="cargamasivareservas.php" enctype="multipart/form-data">
																	<table id="simple-table" class="table table-striped table-bordered table-hover">																							
																		<tr>
																			<th>Borrar Registros</th>
																			<th>Fecha Archivo</th>
																			<th>Nombre Archivo</th>
																		</tr>
																		<?php
																			$sql = "SELECT IdentificadorServicio FROM ReservaGeneral WHERE IDClub = '".SIMUser::get("club")."' AND IDServicio = '".$ids."' GROUP BY IdentificadorServicio ORDER BY FechaTrCr DESC";
																			$qry = $dbo->query($sql);																		

																			while($row = $dbo->fetchArray($qry))
																			{
																				$infoArchvio = explode("_",$row["IdentificadorServicio"]);	
																				if(sizeof($infoArchvio) > 1)
																				{																			
																					?>
																					<tr>
																						<th> 
																						
																							<input type="radio" id="<?php echo $infoArchvio[0];?>"  name="identificador" value="<?php echo $infoArchvio[0];?>"/>
																						</th>
																						<th> 
																							<?php																																															
																							echo $infoArchvio[1];																																													
																							?>
																						</th>
																						<th>
																							<?php
																							echo $infoArchvio[2];
																							?>
																						</th>
																					</tr>
																					<?php
																				}
																			}
																		?>
																	</table>

																	<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>"  >
																	<input type="hidden" name="IDServicio" id="IDServicio" value="<?=$ids ?>"  >
																	<input type="hidden" name="action" id="action" value="eliminareservas">
																	<button class="btn btn-info btnEnviar" type="button" rel="frm1" >
																		<i class="ace-icon fa fa-cloud-download bigger-110"></i>
																		Eliminar reservas de archvio
																	</button>
																<form>


															</div>
														</div>
													</div><!-- /.widget-main -->
												</div><!-- /.widget-body -->
											</div><!-- /.widget-box -->
											<script type="text/javascript">
												var $path_base = ".";//in Ace demo this will be used for editurl parameter
											</script>
											<!-- PAGE CONTENT ENDS -->
										</div> <!-- end tab -->
									</div>
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->




			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->

		<?
			include( "cmp/footer_grid.php" );
		?>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">

			jQuery(function($) {

				var grid_selector = "";
				var pager_selector = "";

				<?

				foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
				{
					$grillas[] = $key_elemento;
				?>
					grid_selector = "#grid-table<?=$key_elemento?>";
					pager_selector = "#grid-pager<?=$key_elemento?>";


					jQuery(grid_selector).jqGrid({

						url:'includes/async/reservas.async.php?idservicio=<?=$ids ?>&idelemento=<?=$datos_elemento["IDElemento"] ?><?=$url_search ?>',
						datatype: "json",
						colNames:['Fecha', 'Hora', 'Socio', 'Cancelar Reserva.' ],
						colModel:[
							{
								name:'Fecha',
								index:'Fecha',
								align:"center"


					        },
							{
								name:'Hora',
								index:'Hora',
								align:"left",
								search: false
							},
							{
								name:'Socio',
								index:'Socio',
								align:"left",
								searchoptions: {
									attr : { placeholder: "Número de derecho o número de documento" }
								}
							},
							{
								name:'Cancelar',
								index:'Cancelar',
								align:"center",
								search: false
							},
						],
						rowNum:100,
						rowList:[100,200,300],
						sortname: 'Hora',
						viewrecords: true,
						sortorder: "ASC",
						caption:"Reservas",
						height: "100%",
						width:855,
						multiselect: false,
						editurl: "includes/reservas.async.php",





						pager : pager_selector,
						altRows: true,
						//toppager: true,

						//multikey: "ctrlKey",

					    multiboxonly: true,

						loadComplete : function() {
							var table = this;
							setTimeout(function(){
								styleCheckbox(table);

								updateActionIcons(table);
								updatePagerIcons(table);
								enableTooltips(table);
							}, 0);

							preparaform();
						},

						onSelectRow: function(id){
				 		//var IDSocio = $(this).attr("rel");
						//var IDReserva = $(this).attr("id");
						//var IDClub = $(this).attr("lang");

						if (confirm("Esta seguro que desea cancelar la reserva?")){
							jQuery.ajax( {
								"type" : "POST",
								"data" : { "IDReservaGeneral" : id },
								"dataType" : "json",
								"url" : "includes/async/cancela_reserva.async.php" ,

								"success" : function( data ){
										alert("Reserva Cancelada con exito");
										$("#grid-table<?=$key_elemento?>").trigger("reloadGrid");
										return false;

								}
							});
						}
						 return false;
			  			 },





					});


					$( grid_selector ).jqGrid( 'setGridWidth', $("#grillasReserva .tab-content").width() );

					$( grid_selector ).jqGrid('sortGrid', 'Fecha', true, 'asc');
					$( grid_selector ).jqGrid('sortGrid', 'Hora', true, 'asc');


					//resize to fit page size
					$(window).on('resize.jqGrid', function () {
						$(grid_selector).jqGrid( 'setGridWidth', $("#grillasReserva .tab-content").width() );
				    })
					//resize on sidebar collapse/expand
					var parent_column = $(grid_selector).closest('[class*="col-"]');
					$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
						if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
							//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
							setTimeout(function() {
								$(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
							}, 0);
						}
				    });



				<?
				}//end for
				?>










				var datePick = function(elem)
				{
				   jQuery(elem).datepicker();
				}


				$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size



				//enable search/filter toolbar
				jQuery(grid_selector).jqGrid('filterToolbar',{defaultSearch:true,stringResult:true})
				jQuery(grid_selector).filterToolbar({});


				//switch element when editing inline
				function aceSwitch( cellvalue, options, cell ) {
					setTimeout(function(){
						$(cell) .find('input[type=checkbox]')
							.addClass('ace ace-switch ace-switch-5')
							.after('<span class="lbl"></span>');
					}, 0);
				}
				//enable datepicker
				function pickDate( cellvalue, options, cell ) {
					setTimeout(function(){
						$(cell) .find('input[type=text]')
								.datepicker({format:'yyyy-mm-dd' , autoclose:true});
					}, 0);
				}


				//navButtons
				jQuery(grid_selector).jqGrid('navGrid',pager_selector,
					{ 	//navbar options

						edit: false,
						editicon : 'ace-icon fa fa-pencil blue',
						add: false,
						addicon : 'ace-icon fa fa-plus-circle purple',
						del: false,
						delicon : 'ace-icon fa fa-trash-o red',
						search: true,
						searchicon : 'ace-icon fa fa-search orange',
						refresh: true,
						refreshicon : 'ace-icon fa fa-refresh green',
						view: true,
						viewicon : 'ace-icon fa fa-search-plus grey',
					},
					{
						//edit record form
						//closeAfterEdit: true,
						//width: 700,
						recreateForm: true,
						beforeShowForm : function(e) {
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
							style_edit_form(form);
						}
					},
					{
						//new record form
						//width: 700,
						closeAfterAdd: true,
						recreateForm: true,
						viewPagerButtons: false,
						beforeShowForm : function(e) {
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
							.wrapInner('<div class="widget-header" />')
							style_edit_form(form);
						}
					},
					{
						//delete record form
						recreateForm: true,
						beforeShowForm : function(e) {
							var form = $(e[0]);
							if(form.data('styled')) return false;

							form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
							style_delete_form(form);

							form.data('styled', true);
						},
						onClick : function(e) {
							//alert(1);
						}
					},
					{
						//search form
						recreateForm: true,
						afterShowSearch: function(e){
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
							style_search_form(form);

						},
						afterRedraw: function(){
							style_search_filters($(this));
						}
						,
						multipleSearch: true,
						/**
						multipleGroup:true,
						showQuery: true
						*/
					},
					{
						//view record form
						recreateForm: true,
						beforeShowForm: function(e){
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
						}
					}
				)



				function style_edit_form(form) {
					//enable datepicker on "sdate" field and switches for "stock" field
					form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})

					form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
							   //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
							  //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');


					//update buttons classes
					var buttons = form.next().find('.EditButton .fm-button');
					buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
					buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
					buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')

					buttons = form.next().find('.navButton a');
					buttons.find('.ui-icon').hide();
					buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
					buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
				}

				function style_delete_form(form) {
					var buttons = form.next().find('.EditButton .fm-button');
					buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
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
					if(form.data('styled')) return false;

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
					var replacement =
					{
						'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
						'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
						'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
						'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
					};
					$('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
						var icon = $(this);
						var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

						if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
					})
				}

				function enableTooltips(table) {
					$('.navtable .ui-pg-button').tooltip({container:'body'});
					$(table).find('.ui-pg-div').tooltip({container:'body'});
				}

				//var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');

				$(document).one('ajaxloadstart.page', function(e) {
					$(grid_selector).jqGrid('GridUnload');
					$('.ui-jqdialog').remove();
				});
			});
		</script>
		<input type="hidden" name="grillas" id="grillas" value="<?=implode( ",", $grillas )?>">
	</body>
</html>
