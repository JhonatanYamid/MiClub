<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Nombre" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" />
			</div>
		</div>


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Aplica para la carrera: </label>

			<div class="col-sm-8">
				<select name="IDCarrera" id="IDCarrera" class="mandatory">
					<option value="">Seleccione Carrera</option>
					<?php
					$sql_carrera = "SELECT Nombre,IDCarrera From Carrera Where  IDClub = '" . SIMUser::get("club") . "' and Activo='S'";
					$r_carrera = $dbo->query($sql_carrera);
					while ($row_carrera = $dbo->fetchArray($r_carrera)) { ?>
						<option value="<?php echo $row_carrera["IDCarrera"]; ?>" <?php if ($row_carrera["IDCarrera"] == $frm["IDCarrera"]) echo "selected"; ?>><?php echo $row_carrera["Nombre"];  ?></option>
					<?php } ?>
				</select>

			</div>
		</div>
	</div>







	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Activo"], "Activo", "title=\"Activo\"") ?>
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
			<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
				<i class="ace-icon fa fa-check bigger-110"></i>
				<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
			</button>


		</div>
	</div>

</form>