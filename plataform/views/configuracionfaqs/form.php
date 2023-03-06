<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EmailNotificacion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="EmailNotificacion" name="EmailNotificacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'EmailNotificacion', LANGSESSION); ?>" class="col-xs-12 " title="Email Notificacion" value="<?php echo $frm["EmailNotificacion"]; ?>">
								</div>
							</div>


						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Mostrar Fecha? </label>
								<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarFecha"], 'PermiteMostrarFecha', "class='input mandatory'") ?>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Botones Like </label>
								<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarBotonesLike"], 'OcultarBotonesLike', "class='input mandatory'") ?>
							</div>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Iconomegusta', LANGSESSION); ?> </label>
								<input name="ImagenThumbMeGusta" id=file class="" title="ImagenThumbMeGusta" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["ImagenThumbMeGusta"])) {
										echo "<img src='" . CLUB_ROOT . $frm["ImagenThumbMeGusta"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenThumbMeGusta]&campo=ImagenThumbMeGusta&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icononomegusta', LANGSESSION); ?> </label>
								<input name="ImagenThumbNoMeGusta" id=file class="" title="ImagenThumbNoMeGusta" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["ImagenThumbNoMeGusta"])) {
										echo "<img src='" . CLUB_ROOT . $frm["ImagenThumbNoMeGusta"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenThumbNoMeGusta]&campo=ImagenThumbMeGusta&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcrearpregunta', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarCrearPregunta"], "MostrarCrearPregunta", "title=\"Crear pregunta\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?></div>
							</div>



						</div>



						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>


							</div>
						</div>

					</form>
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>