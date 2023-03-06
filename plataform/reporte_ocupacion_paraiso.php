<?
	include( "procedures/general.php" );
	include( "procedures/reporte_ocupacion_paraiso.php" );
	include( "cmp/seo.php" );
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
				$menu_home = " class=\"active\" ";
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


					
						<?
						SIMNotify::each();
						
						?>


						<div class="page-header">
                        	<?php include("cmp/migapan.php"); ?>
							
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								

								<div class="row">
									<div class="col-sm-12">
										<div class="widget-box transparent" id="recent-box">
											<div class="widget-header">
												<h4 class="widget-title lighter smaller">
													<i class="ace-icon fa fa-users orange"></i>CONSULTAR FECHA
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

																		<!-- <input type="text" name="qryString" class="form-control search-query "  placeholder="Ingrese datos"> -->

																		<!-- <div  class="form-group first "> -->
																						
																			<!-- <div  class="col-xs-12 col-sm-6"> -->
																				<!-- <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label> -->
																					
																					<!-- <div class="col-sm-8"> -->
																						<input type="text" id="Fecha" name="Fecha" placeholder="Fecha " class="col-xs-12 calendar" title="Fecha" value="<?php echo date("Y-m-d") ?>" >
																					<!-- </div> -->
																			<!-- </div> -->
																		<!-- </div> -->



																		<input type="hidden" name="action" value="search">
																		<span class="input-group-btn">
																			
																			<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																				<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																				Buscar Fecha
																			</button>
																		</span>
																		<!-- <span class="input-group-btn">
																			
																			<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="reportereservas.php?action=search">
																				Ver Todos
																			</button>
																		</span> -->
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
