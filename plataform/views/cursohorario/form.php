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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm" name="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoSede", "Nombre", "Nombre", "IDCursoSede", $frm["IDCursoSede"], "[Seleccione]", "form-control", "title = \"Sede\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cursotipo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoTipo", "Nombre", "Nombre", "IDCursoTipo", $frm["IDCursoTipo"], "[Seleccione]", "form-control", "title = \"Tipo\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>


						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Entrenador', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoEntrenador", "Nombre", "Nombre", "IDCursoEntrenador", $frm["IDCursoEntrenador"], "[Seleccione]", "form-control", "title = \"Entrenador\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cupos', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Cupo" name="Cupo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Cupos', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Cupos', LANGSESSION); ?>" value="<?php echo $frm["Cupo"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Edad', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoEdad", "Nombre", "Nombre", "IDCursoEdad", $frm["IDCursoEdad"], "[Seleccione]", "form-control", "title = \"Edad\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nivel', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoNivel", "Nombre", "Nombre", "IDCursoNivel", $frm["IDCursoNivel"], "[Seleccione]", "form-control", "title = \"Nivel\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ValorMesWeb', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="ValorMes" name="ValorMes" placeholder="<?= SIMUtil::get_traduccion('', '', 'ValorMesWeb', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'ValorMesWeb', LANGSESSION); ?>" value="<?php echo $frm["ValorMes"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ValorMesPresencial', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="ValorMesPresencial" name="ValorMesPresencial" placeholder="<?= SIMUtil::get_traduccion('', '', 'ValorMesPresencial', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'ValorMesPresencial', LANGSESSION); ?>" value="<?php echo $frm["ValorMesPresencial"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ValorTrimestre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="ValorTrimestre" name="ValorTrimestre" placeholder="<?= SIMUtil::get_traduccion('', '', 'ValorTrimestre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'ValorTrimestre', LANGSESSION); ?>" value="<?php echo $frm["ValorTrimestre"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HoraDesde', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="time" id="HoraDesde" name="HoraDesde" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraDesde', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraDesde', LANGSESSION); ?>" value="<?php echo $frm["HoraDesde"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HoraHasta', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="time" id="HoraHasta" name="HoraHasta" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraHasta', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraHasta', LANGSESSION); ?>" value="<?php echo $frm["HoraHasta"]; ?>">
								</div>
							</div>
						</div>


						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>
						</div>





						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm">
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