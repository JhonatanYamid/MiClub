<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Seccion', LANGSESSION); ?></label>

			<div class="col-sm-8">

				<input type="hidden" id="IDSeccionGaleria" name="IDSeccionGaleria" value="<?php echo $frm["IDSeccionGaleria"]; ?>">
				<input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields("SeccionGaleria", "Nombre", "IDSeccionGaleria = '" . $frm["IDSeccionGaleria"] . "'") ?>" readonly>
				<a href="PopupSeccionGaleria.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;" class="ace-icon glyphicon glyphicon-search"></a>

			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Galeriapara', LANGSESSION); ?>: </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>
				<?php
				if (SIMUser::get("club") == "36") {
					echo "<br>Tipo:";
					echo SIMHTML::formPopupArray(SIMResources::$tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
				} ?>

			</div>
		</div>



	</div>


	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" />
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<textarea rows="5" cols="50" id="Descripcion" name="Descripcion" class="input"><?php echo $frm["Descripcion"] ?></textarea>
			</div>
		</div>



	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<? if (!empty($frm[Foto])) {
					if (strstr(strtolower($frm[Foto]), "http://"))
						$ruta_imagen = $frm[Foto];
					else
						$ruta_imagen = GALERIA_ROOT . $frm[Foto];

					echo "<img src='$ruta_imagen' width=55 >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
				<input name="Foto" id=file class="" title="Foto" type="file" size="25" style="font-size: 10px">

			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="text" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha" value="<?php echo $frm["Fecha"] ?>">
			</div>
		</div>

	</div>



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificaciónaSocios', LANGSESSION); ?> ? </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
		</div>



	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Publicar"], "Publicar", "title=\"Publicar\"") ?></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Publicarenelhome', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<?= SIMHTML::formRadioGroup(SIMResources::$sino, SIMResources::$sino[substr($frm["Home"], 0, 1)], "Home") ?>

			</div>
		</div>

	</div>

	<div class="form-group first ">


		<div class="col-xs-12 col-sm-6">
			<!-- <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Galeria Destacada<br /> (primera en la seccion de la galeria) </label> -->
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'GaleriaDestacada(primeraenlasecciondelagaleria)', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<?= SIMHTML::formRadioGroup(SIMResources::$sino, SIMResources::$sino[substr($frm["Destacada"], 0, 1)], "Destacada") ?>
			</div>
		</div>


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Orden" type="number" size="25" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" name="Orden" class="input mandatory" value="<?php echo $frm["Orden"] ?>" />
			</div>
		</div>

	</div>



	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
			<input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get("title"); ?>" />
			<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																	else echo $frm["IDClub"];  ?>" />
			<button class="btn btn-info btnEnviar" type="button" rel="frm">
				<i class="ace-icon fa fa-check bigger-110"></i>
				<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
			</button>


		</div>
	</div>

</form>