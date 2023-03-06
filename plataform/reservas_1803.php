<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );


	include("cmp/seo.php");

	$url_search = "";
	if( $_GET["action"] == "search" )
	{
		$url_search = "?oper=search_url&ids=" . $ids . "&IDElemento=" . SIMNet::get("IDElemento") . "&Accion=" . SIMNet::get("Accion") . "&Fecha=" . SIMNet::get("Fecha");
	}//end if

?>
		


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
								<a href="index.php">Home</a>
							</li>

							<li>
								<a href=""><?=$datos_club["Nombre"] ?></a>
							</li>

							<li class="active">Reservas <?=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $datos_servicio[$ids]["IDServicioMaestro"] . "'") ?></li>
						</ul><!-- /.breadcrumb -->

						
					</div>

					<div class="page-content">
						
						<div class="ace-settings-container" id="ace-settings-container">
								
							<button class="btn btn-danger  fancybox" href="reservas_admin.php?ids=<?=$ids?>" data-fancybox-type="iframe">
								<i class="ace-icon fa fa-file align-top bigger-125"></i>
								Crear Reserva							</button>

							
						</div>

						<div class="page-header">
							<h1>
							<i class="ace-icon fa fa-angle-double-right"></i>
								Reservas <?=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $datos_servicio[$ids]["IDServicioMaestro"] . "'") ?>
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
											<li class="active">
												<a data-toggle="tab" href="reservas.php">
													<i class="green ace-icon fa fa-calendar bigger-120"></i>
													Reservas
												</a>
											</li>
                                            <?php if(SIMUser::get("IDPerfil")<=2): ?>
											<li >
												<a data-toggle="tab" class="noTabLink" href="serviciosclub.php?action=edit&ids=<?=$ids ?>">
													<i class="green ace-icon fa fa-gear bigger-120"></i>
													Configuración
												</a>
											</li>
                                            <?php endif; ?>
                                            
										</ul>

										
										<div class="tab-content">
											<div id="home" class="tab-pane fade in active">
										
												<div class="widget-box transparent" id="recent-box">
													<div class="widget-header">
														<h4 class="widget-title lighter smaller">
															<i class="ace-icon fa fa-users orange"></i>
															BUSCAR RESERVAS
														</h4>

														
													</div>

													<div class="widget-body">
														<div class="widget-main padding-4">
															<div class="row">
																<div class="col-xs-12">
																	<!-- PAGE CONTENT BEGINS -->
																	<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI()?>" method="get">
																		
																		<div class="col-xs-12 col-sm-8">
																			<div class="input-group">
																				<span class="input-group-addon">
																					<i class="ace-icon fa fa-check"></i>
																				</span>

																				<input type="text" id="Accion" name="Accion" class="form-control search-query "  placeholder="Ingrese el número de derecho o nombre del socio">
																				<input type="hidden" name="action" value="searchurl">
																				<span class="input-group-btn">
																					
																					<button type="button" class="btn btn-purple btn-sm btnBuscarSocio" >
																						<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																						Buscar Reservas
																					</button>
																				</span>
                                                                                
                                                                               <!--
																				<span class="input-group-btn">
																					
																					<a  class="btn btn-primary btn-sm btnModal fancybox" href="reservas_admin.php?ids=<?=$ids?>" data-fancybox-type="iframe" >
																						Crear Nueva Reserva
																					</a>
			                                                                        
																				</span>
                                                                               -->
																				
																			</div>
																		</div>


																		

																	</form>
																</div>
															</div>

														</div><!-- /.widget-main -->
													</div><!-- /.widget-body -->
												</div><!-- /.widget-box -->
												


												<div class="widget-box transparent" id="recent-box">
													<div class="widget-header">
														<h4 class="widget-title lighter smaller">
															<i class="ace-icon fa fa-calendar orange"></i>
															
															MOSTRANDO RESERVAS DE <span id="contentFechaActual"><? echo strtoupper( SIMUtil::tiempo( $fecha ) ) ?></span> - 
															<input type="hidden" id="fechareserva" value="" />
															<a href="javascript:void(0);" class="calendar_reservas">
																Consultar otra fecha
															</a>
														</h4>

														
													</div>


													<div class="widget-body">
														<div class="widget-main padding-4">
															<div class="row">
																<div class="col-sm-12 widget-container-col ui-sortable">
																	<div class="widget-box ui-sortable-handle">
																		<div class="widget-header widget-header-small">
																			<h5 class="widget-title smaller"><?=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $datos_servicio[$ids]["IDServicioMaestro"] . "'") ?></h5>

																			<div class="widget-toolbar no-border">
																				<ul class="nav nav-tabs" id="myTab">
																					<?
																					$active = " class=\"active\" ";
																					$aria_expanded = "true";
																					foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
																					{

																					?>
																						<li <?=$active ?> >
																							<a data-toggle="tab" href="#tab<?=$key_elemento ?>" aria-expanded="<?=$aria_expanded ?>"><?=$datos_elemento["Nombre"] ?></a>
																						</li>
																					<?
																						$active = "";
																						$aria_expanded = "false";
																					}//end for
																					?>

																					
																				</ul>
																			</div>
																		</div>

																		<div class="widget-body" id="grillasReserva">
																			<div class="widget-main padding-6">
																				<div class="tab-content">
																					
																					<?
																					$active = " active ";
																					$aria_expanded = "true";
																					foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
																					{

																					?>
																						<div id="tab<?=$key_elemento ?>" class="tab-pane <?=$active ?>">
																							
																							<table id="grid-table<?=$key_elemento ?>"></table>

																							<div id="grid-pager<?=$key_elemento ?>"></div>

																						</div>
																					<?
																						$active = "";
																					}//end for
																					?>
			                                                                        
			                                                                        
			                                                                        
																					
																				</div>
																			</div>
																		</div>
																	</div>
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
