<?
include("procedures/general.php");
include("procedures/pqr.php");
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
					$permiso_escritura = $dbo->getFields("Usuario", "Permiso", "IDUsuario = '" . SIMUser::get("IDUsuario") . "'");

					if ($view <> "views/" . $script . "/form.php") {

						if ($permiso_escritura != "L") {
					?>
							<div class="ace-settings-container" id="ace-settings-container">

								<button class="btn btn-danger btnRedirect" rel="<?php echo $script ?>.php?action=add">
									<i class="ace-icon fa fa-file align-top bigger-125"></i>
									<?= SIMUtil::get_traduccion('', '', 'Nuevo', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>


							</div>
					<?
						}
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

															<table id="simple-table" class="table table-striped table-bordered table-hover">
																<div class="col-xs-12 col-sm-12">
																	<div class="input-group">

																		<?php
																		$action = $_GET["action"];
																		if ($action != "add" && $action != "edit") {

																		?>

																			<tr>
																				<td><?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?></td>
																				<td><input type="text" name="Numero" id="Numero" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?> </td>
																				<td>
																					<span class="col-sm-8">
																						<input type="text" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 calendar " title="Fecha" value="">
																					</span>
																				</td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></td>
																				<td><input type="text" name="Tipo" id="Tipo" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Area', LANGSESSION); ?></td>

																				<td>
																					<input type="text" name="Area" id="Area" class="form-control">
																				</td>
																			</tr>
																			<tr>
																				<td><?= SIMUtil::get_traduccion('', '', 'TipoSocio', LANGSESSION); ?></td>
																				<td><input type="text" name="TipoSocio" id="TipoSocio" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </td>
																				<td>

																					<input type="text" name="Socio" id="Socio" class="form-control">

																				</td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Predio', LANGSESSION); ?></td>
																				<td><input type="text" name="Predio" id="Predio" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?></td>
																				<td><input type="text" name="Descripcion" id="Descripcion" class="form-control"></td>
																			</tr>

																			<tr>
																				<td><?= SIMUtil::get_traduccion('', '', 'Asunto', LANGSESSION); ?></td>
																				<td><input type="text" name="Asunto" id="Asunto" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Responsable', LANGSESSION); ?></td>
																				<td><input type="text" name="Responsable" id="Responsable" class="form-control"></td>

																				<td><?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?></td>
																				<td colspan="3">
																					<div class="col-sm-8"><?php echo SIMHTML::formPopUp("PqrEstado", "Nombre", "Nombre", "IDPqrEstado", $frm["IDPqrEstado"], "[Seleccione el estado]", "form-control", "title = \"IDTipo Archivo\"") ?></div>
																				</td>
																			</tr>

																		<?php } ?>

																		<tr>
																			<td colspan="6" align="center">

																				<input type="text" name="qryString" class="form-control search-query " placeholder="<?= SIMUtil::get_traduccion('', '', 'Ingreselosdatosdebúsqueda', LANGSESSION); ?>">
																			</td>
																			<td>
																				<input type="hidden" name="action" value="search">
																				<span class="input-group-btn">

																					<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																						<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																						<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
																					</button>
																				</span>
																			</td>

																			<td>
																				<span class="input-group-btn">

																					<button type="button" class="btn btn-primary btn-sm btnRedirect" rel="<?php echo $script; ?>.php?action=search">
																						<?= SIMUtil::get_traduccion('', '', 'VerTodos', LANGSESSION); ?>
																					</button>
																				</span>
																			</td>

																		</tr>


																	</div>
																</div>


															</table>

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