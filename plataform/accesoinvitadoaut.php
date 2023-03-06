<?
	include( "procedures/general.php" );
	include( "procedures/accesoinvitadoaut.php" );
	include( "cmp/seo.php" );
	$logo_club = CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );
	$logo_ruta = CLUB_DIR.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" ); 
?>
	</head>

	<body class="no-skin" onLoad="document.frmfrmBuscar.qryString.focus();">
		
		        <div id="navbar" class="navbar navbar-default">
	
			<div class="navbar-container" id="navbar-container">
				

				<div class="navbar-header pull-left">
					<a href="index.php" class="navbar-brand"><img src="assets/img/logo-interno.png" /></a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation">
                	<div style="background-color:#FFFFFF">
                    <?	
                      $imagen = getimagesize($logo_ruta);    //Sacamos la información
					  $ancho = $imagen[0];              //Ancho
					  $alto = $imagen[1];               //Alto					 
					  //echo "Ancho: $ancho<br>";
					  //echo "Alto: $alto";
					  if($ancho>100 || $alto>100):
					  	$ancho_alto = 'width="100" height="50"';
					  endif;
					  ?>
                    <img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $ancho_alto; ?> />
                    </div>
				</div>



	</div><!-- /.navbar-container -->
</div>
			

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<?php include("cmp/breadcrumb.php"); ?>

						
					</div>

					<div class="page-content">
						<?
						SIMNotify::each();
						?>
					<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								

								<div class="row">
									<div class="col-sm-12">
										<div class="widget-box transparent" id="recent-box">
											<div class="widget-header">
												<h4 class="widget-title lighter smaller">
													<i class="ace-icon fa fa-users orange"></i>Ingrese placa | documento | accion
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

																		<input type="text" name="qryString" id="busqueda_acceso" class="form-control search-query busqueda_acceso"  placeholder="Ingrese placa | documento | accion">
																		<input type="hidden" name="action" value="search">
																		<span class="input-group-btn">
																			
																			<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																				<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																				Buscar
																			</button>
																		</span>
																		<span class="input-group-btn">
																		</span>
																	</div>
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
            
		</div><!-- /.main-container -->

		
	</body>
</html>
