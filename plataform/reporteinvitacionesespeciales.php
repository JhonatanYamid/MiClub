<?
include("procedures/general.php");
include("procedures/reporteinvitacionesespeciales.php");
include("cmp/seo.php");
?>
</head>

<body class="no-skin">


	<?
	include("cmp/header.php");
	?>


	<div class="main-container" id="main-container">
		<script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>

		<?
		$menu_home = " class=\"active\" ";
		include("cmp/menu.php");
		?>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs" id="breadcrumbs">
					<script type="text/javascript">
						try {
							ace.settings.check('breadcrumbs', 'fixed')
						} catch (e) {}
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
												<i class="ace-icon fa fa-users orange"></i>CONSULTAR INVITACIONES
											</h4>


										</div>

										<div class="widget-body">
											<div class="widget-main padding-4">
												<div class="row">
													<div class="col-xs-12">
														<!-- PAGE CONTENT BEGINS -->
														<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

															<div class="col-xs-12 col-sm-8">
																<div class="input-group">
																	<span class="input-group-addon">
																		<i class="ace-icon fa fa-check"></i>
																	</span>







																	<input type="text" name="qryString" class="form-control search-query " placeholder="Ingrese el número de derecho o nombre del socio">
																	<input type="hidden" name="action" value="search">

																</div>

																&nbsp;
																<div class="input-group">
																	<span class="input-group-addon">
																		Fecha Desde
																	</span>

																	<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 calendar " title="Fecha Ingreso" value="<?php if ($_GET["FechaInicio"] == "0000-00-00" || $frm["FechaInicio"] == "") echo date("Y-m-d");
																																																				else echo $frm["FechaInicio"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
																	<input type="hidden" name="action" value="search">
																</div>


																&nbsp;
																<div class="input-group">
																	<span class="input-group-addon">
																		Fecha Hasta
																	</span>

																	<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar " title="Fecha Fin" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") echo date("Y-m-d");
																																																	else echo $frm["FechaFin"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
																	<input type="hidden" name="action" value="search">
																</div>




																<span class="input-group-btn">

																	<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																		<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																		Buscar Invitaciones
																	</button>
																</span>

																&nbsp;

																<span class="input-group-btn">

																	<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="reporteinvitacionesespeciales.php?action=search">
																		Ver Todos
																	</button>
																</span>
															</div>




														</form>
													</div>
												</div>




											</div><!-- /.widget-main -->
										</div><!-- /.widget-body -->
									</div><!-- /.widget-box -->



									<?
									include($view);
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