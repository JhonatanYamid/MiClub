<?
include("procedures/general.php");
include("procedures/ajustesregistros.php");
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



									</div><!-- /.widget-box -->



									<div class="widget-box transparent" id="recent-box">
										<div class="widget-header">
											<h4 class="widget-title lighter smaller">
												<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'AJUSTAR', LANGSESSION); ?>
											</h4>


										</div>

										<div class="widget-body">
											<div class="widget-main padding-4">
												<div class="row">
													<div class="col-xs-12">
														<!-- PAGE CONTENT BEGINS -->


														<form class="form-horizontal" method="post" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

															<div class="form-group first ">

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?> </label>

																	<div class="col-sm-8">
																		<select name="Tipo" id="Tipo" class="form-control">
																			<option value=""></option>
																			<option value="Socio"><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></option>
																			<option value="Invitado"><?= SIMUtil::get_traduccion('', '', 'Invitado-Contratista,etc', LANGSESSION); ?></option>

																		</select <input type="text" id="DocumentoReal" name="DocumentoReal" placeholder="DocumentoReal" class="col-xs-12 mandatory" title="Documento Real" value="">
																	</div>
																</div>

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'DocumentoReal', LANGSESSION); ?> </label>

																	<div class="col-sm-8">
																		<input type="text" id="DocumentoReal" name="DocumentoReal" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoReal', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'DocumentoReal', LANGSESSION); ?>" value="">
																	</div>
																</div>

															</div>




															<div class="form-group first ">

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 1</label>

																	<div class="col-sm-8">
																		<input type="text" id="DocumentoFicticio1" name="DocumentoFicticio1" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 1" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 1">
																	</div>
																</div>

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 2</label>

																	<div class="col-sm-8">
																		<input type="text" id="DocumentoFicticio2" name="DocumentoFicticio2" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 2" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 2">
																	</div>
																</div>

															</div>

															<div class="form-group first ">

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 3</label>

																	<div class="col-sm-8">
																		<input type="text" id="DocumentoFicticio3" name="DocumentoFicticio3" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 3" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 3">
																	</div>
																</div>

																<div class="col-xs-12 col-sm-6">
																	<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 4</label>

																	<div class="col-sm-8">
																		<input type="text" id="DocumentoFicticio4" name="DocumentoFicticio4" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 4" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'DocumentoFicticio', LANGSESSION); ?> 4">
																	</div>
																</div>

															</div>





															<div class="clearfix form-actions">
																<div class="col-xs-12 text-center">
																	<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
																	<input type="hidden" name="action" id="action" value="update" />
																	<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																															else echo $frm["IDClub"];  ?>" />
																	<button class="btn btn-info btnEnviar" type="submit">
																		<i class="ace-icon fa fa-check bigger-110"></i>
																		<?= SIMUtil::get_traduccion('', '', 'Unificarregistros', LANGSESSION); ?>
																	</button>


																</div>
															</div>

														</form>
													</div>
												</div>




											</div><!-- /.widget-main -->
										</div><!-- /.widget-body -->
									</div><!-- /.widget-box -->



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