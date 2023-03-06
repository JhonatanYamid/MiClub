<?
include("procedures/general.php");
include("procedures/cursoinscripcion.php");
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

					if ($view <> "views/" . $script . "/form.php") {
					?>
						<div class="ace-settings-container" id="ace-settings-container">

							<button class="btn btn-danger btnRedirect" rel="<?php echo $script ?>.php?action=add">
								<i class="ace-icon fa fa-file align-top bigger-125"></i>
								<?= SIMUtil::get_traduccion('', '', 'Nuevo', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
							</button>


						</div>
					<?
					} //end if
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
												<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'Consultar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
											</h4>


										</div>

										<div class="widget-body">
											<div class="widget-main padding-4">
												<div class="row">
													<div class="col-xs-12">
														<!-- PAGE CONTENT BEGINS -->

														<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

															<div class="col-xs-12 col-sm-12">


																<?php if ($_GET["action"] != "add") { ?>
																	<table id="simple-table" class="table table-striped table-bordered table-hover">
																		<tr>
																			<td><?= SIMUtil::get_traduccion('', '', 'DocumentoSocio', LANGSESSION); ?></td>
																			<td><input type="text" name="Documento" id="Documento" class="form-control" value="<?php echo $_GET["Documento"] ?>"> </td>
																			<td><?= SIMUtil::get_traduccion('', '', 'NombreSocio', LANGSESSION); ?></td>
																			<td><input type="text" name="Socio" id="Socio" class="form-control" value="<?php echo $_GET["Socio"] ?>"></td>
																			<td><?= SIMUtil::get_traduccion('', '', 'Nivel', LANGSESSION); ?></td>
																			<td><?php echo SIMHTML::formPopUp("CursoNivel", "Nombre", "Nombre", "IDCursoNivel", $_GET["IDCursoNivel"], "[Seleccione]", "form-control", "title = \"Nivel\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?></td>
																		</tr>
																		<tr>
																			<td><?= SIMUtil::get_traduccion('', '', 'NombreCurso', LANGSESSION); ?></td>
																			<td><input type="text" name="Curso" id="Curso" class="form-control" value="<?php echo $_GET["Curso"] ?>"></td>
																			<td><?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?></td>
																			<td><?php echo SIMHTML::formPopUp("CursoSede", "Nombre", "Nombre", "IDCursoSede", $_GET["IDCursoSede"], "[Seleccione]", "form-control", "title = \"Sede\"", " and IDClub = '" . SIMUser::get("club") . "' " . $condicion_sede); ?></td>
																			<td><?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?></td>
																			<td><?php echo SIMHTML::formPopUp("CursoTipo", "Nombre", "Nombre", "IDCursoTipo", $_GET["IDCursoTipo"], "[Seleccione]", "form-control", "title = \"Horario\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?></td>
																		</tr>
																		<tr>
																			<td><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?></td>
																			<td>
																				<span class="col-sm-8">
																					<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar " title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $_GET["FechaInicio"] ?>">
																				</span>
																			</td>
																			<td><?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?></td>
																			<td><span class="col-sm-8">
																					<input type="time" id="Hora" name="Hora" placeholder="<?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?>" value="<?php echo $_GET["Hora"] ?>">
																				</span></td>
																			<td><?= SIMUtil::get_traduccion('', '', 'Entrenador', LANGSESSION); ?></td>
																			<td><?php echo SIMHTML::formPopUp("CursoEntrenador", "Nombre", "Nombre", "IDCursoEntrenador", $_GET["IDCursoEntrenador"], "[Seleccione]", "form-control", "title = \"Entrenador\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?></td>
																		</tr>

																		<tr>
																			<td><?= SIMUtil::get_traduccion('', '', 'FechaInicioExportar', LANGSESSION); ?></td>
																			<td>
																				<span class="col-sm-8">
																					<input type="text" id="FechaInicioExportar" name="FechaInicioExportar" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicioExportar', LANGSESSION); ?>" class="col-xs-12 calendar " title="<?= SIMUtil::get_traduccion('', '', 'FechaInicioExportar', LANGSESSION); ?>" value="<?php echo $_GET["FechaInicioExportar"] ?>">
																				</span>
																			</td>
																			<td><?= SIMUtil::get_traduccion('', '', 'FechaFinExportar', LANGSESSION); ?></td>
																			<td>
																				<span class="col-sm-8">
																					<input type="text" id="FechaFinExportar" name="FechaFinExportar" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFinExportar', LANGSESSION); ?>" class="col-xs-12 calendar " title="<?= SIMUtil::get_traduccion('', '', 'FechaFinExportar', LANGSESSION); ?>" value="<?php echo $_GET["FechaFinExportar"] ?>">
																				</span>
																			</td>

																		</tr>
																		<tr>
																			<td colspan="6" align="center">

																				<input type="hidden" name="action" value="search">
																				<span class="input-group-btn">

																					<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																						<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																						<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
																					</button>
																				</span>
																				<span class="input-group-btn">

																					<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="<?php echo $script; ?>.php?action=search">
																						<?= SIMUtil::get_traduccion('', '', 'VerTodos', LANGSESSION); ?>
																					</button>
																				</span>

																			</td>
																		</tr>
																	</table>
																<?php } else { ?>

																	<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

																		<div class="col-xs-12 col-sm-8">
																			<div class="input-group">
																				<span class="input-group-btn">

																					<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="<?php echo $script; ?>.php?action=search">
																						<?= SIMUtil::get_traduccion('', '', 'VerInscritos', LANGSESSION); ?>
																					</button>
																				</span>
																			</div>
																		</div>




																	</form>


																<?php } ?>


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
		<script>
			$('#gritter-center').on(ace.click_event, function() {
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