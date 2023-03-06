<?
	include( "procedures/general.php" );
	include( "procedures/reporteaccesos.php" );
	include("cmp/seo.php");

	$url_search = "";
	if( $_GET["action"] == "search" )
	{
		$url_search = "?oper=search_url&Accion=" . SIMNet::get("Accion");
	}//end if

?>
		


	</head>

	<body class="no-skin">
		<?
			if ($_GET["action"]!="add"):
				include( "cmp/header.php" );
			endif;	
		?>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<?
				$menu_invitados = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<?php include("cmp/breadcrumb.php"); ?>

						
					</div>

					<div class="page-content">
                    
                    <?	SIMNotify::each(); ?>

						

						<div class="page-header">
							<h1>
								<?php echo strtoupper(SIMReg::get( "title" ))?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									 <?=SIMUtil::tiempo( date("Y-m-d") ) ?>
								</small>
							</h1>
						</div><!-- /.page-header -->

<div class="widget-body">
												<div class="widget-main padding-4">
													<div class="row">
														<div class="col-xs-12">
															<!-- PAGE CONTENT BEGINS -->
                                                            
                                                            
                                                            <form name="frmBusqueda" id="frmBusqueda" action="" method="post" enctype="multipart/form-data">
                                                                
                                                            </form>
                                                            
                                                            
                                                            
															<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI()?>" method="get">
																
																<div class="col-xs-12 col-sm-12">
                                                                
                                                                
																	
                                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                        <td>Documento Socio</td>
                                                                        <td><input type="text" name="DocumentoSocio" id="DocumentoSocio" class="form-control"  >   </td>
                                                                        <td>Nombre Socio</td>
                                                                        <td><input type="text" name="NombreSocio" id="NombreSocio" class="form-control"></td>
                                                                      <td>Apellido Socio</td>
                                                                        <td><input type="text" name="ApellidoSocio" id="ApellidoSocio" class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td>Accion Socio</td>
                                                                      <td><input type="text" name="AccionSocio" id="AccionSocio" class="form-control"  ></td>
                                                                      <td>&nbsp;</td>
                                                                      <td>&nbsp;</td>
                                                                      <td>&nbsp;</td>
                                                                      <td>&nbsp;</td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td>Documento Contratista</td>
                                                                      <td><input type="text" name="DocumentoContratista" id="DocumentoContratista" class="form-control"  ></td>
                                                                      <td>Nombre Contratista</td>
                                                                      <td><input type="text" name="NombreContratista" id="NombreContratista" class="form-control"></td>
                                                                      <td>Apellido Contratista</td>
                                                                      <td><input type="text" name="ApellidoContratista" id="ApellidoContratista" class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td>Placa</td>
                                                                      <td><input type="text" name="PlacaContratista" id="PlacaContratista" class="form-control"  ></td>
                                                                      <td>Predio Contratista</td>
                                                                      <td><input type="text" name="PredioContratista" id="PredioContratista" class="form-control"  ></td>
                                                                      <td>Numero Licencia</td>
                                                                      <td><input type="text" name="LicenciaConduccion" id="LicenciaConduccion" class="form-control"  ></td>
                                                                    </tr>
                                                                    <tr>
                                                                      <td>Tipo</td>
                                                                      <td>
																	   <select name = "IDTipoInvitado" id="IDTipoInvitado" class="form-control" >
								                                        	<option value=""></option>
																	  <?php 
																	  	$sql_tipo_lista = "Select * From TipoInvitado Where IDClub = '".SIMUser::get("club")."'";
																		$qry_tipo_lista = $dbo->query($sql_tipo_lista);
																		while ($r_tipo_lista = $dbo->fetchArray($qry_tipo_lista)): ?>
																			<option value="<?php echo $r_tipo_lista["IDTipoInvitado"]; ?>" <?php if($r_tipo_lista["IDTipoInvitado"]==$frm["IDTipoInvitado"]) echo "selected";  ?>><?php echo $r_tipo_lista["Nombre"]; ?></option>
																		<?php endwhile; ?>
                                                                         </select>   
																	  </td>
                                                                      <td>Fecha Desde</td>
                                                                      <td><span class="col-sm-8">
                                                                        <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 calendar " title="Fecha Ingreso" value="<?php  if($frm["FechaInicio"]=="0000-00-00" || $frm["FechaInicio"]=="" ) echo ""; else echo $frm["FechaInicio"]; ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>>
                                                                      </span></td>
                                                                      <td>Fecha Hasta</td>
                                                                      <td><span class="col-sm-8">
                                                                        <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar " title="Fecha Fin" value="<?php  if($frm["FechaFin"]=="0000-00-00" || $frm["FechaFin"]=="" ) echo ""; else echo $frm["FechaFin"]; ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>>
                                                                      </span></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="6" align="center">
                                                                        
                                                                        <input type="hidden" name="action" value="search">
																		<span class="input-group-btn">
																			
																			<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																				<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																				Buscar <?php echo SIMReg::get( "title" )?>
																			</button>
																		</span>
																		<span class="input-group-btn">
																			
																			<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="<?php echo $script; ?>.php?action=search">
																				Ver Todos
																			</button>
																		</span>
                                                                        
                                                                        </td>
                                                                    </tr>
                                                                </table>
																		
																	
																</div>


																

															</form>
														</div>
													</div>




												</div><!-- /.widget-main -->
											</div><!-- /.widget-body -->
										</div><!-- /.widget-box -->



										<?
											include( $view );
										?>



									</div><!-- /.col -->

									
								</div><!-- /.row -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include("cmp/footer.php");
			?>
           <script>
		   	$('#gritter-center').on(ace.click_event, function(){
					$.gritter.add({
						title: 'This is a centered notification',
						text: 'Just add a "gritter-center" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
						class_name: 'gritter-info gritter-center' + (!$('#gritter-light').get(0).checked ? ' gritter-light' : '')
					});
			
					return false;
				});
		   </script>
            
            
            
		</div><!-- /.main-container -->

		
	</body>
</html>
