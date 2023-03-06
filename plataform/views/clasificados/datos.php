<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

			<div class="col-sm-8">

				<?php
				$sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
				$qry_socio_club = $dbo->query($sql_socio_club);
				$r_socio = $dbo->fetchArray($qry_socio_club); ?>

				<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] ?>">
				<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>

			<div class="col-sm-8"><?php echo SIMHTML::formPopUp("SeccionClasificados", "Nombre", "Nombre", "IDSeccionClasificados", $frm["IDSeccionClasificados"], "[Seleccione categoria", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "'") ?></div>
		</div>

	</div>







	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
			<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
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
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?> </label>
			<div class="col-sm-8"><input type="number" id="Telefono" name="Telefono" placeholder="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" value="<?php echo $frm["Telefono"]; ?>"></div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?> </label>
			<div class="col-sm-8"><input type="text" id="Email" name="Email" placeholder="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" value="<?php echo $frm["Email"]; ?>"></div>
		</div>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?> </label>
			<div class="col-sm-8"><input type="number" id="Valor" name="Valor" placeholder="<?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>" value="<?php echo $frm["Valor"]; ?>"></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>
			<?php echo SIMHTML::formPopUp("EstadoClasificado", "Nombre", "Nombre", "IDEstadoClasificado", $frm["IDEstadoClasificado"], "[Seleccione estado", "popup mandatory", "title = \"Estado\"") ?>
		</div>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?> </label>
			<div class="col-sm-8"><input type="text" id="Whatsapp" name="Whatsapp" placeholder="<?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?>" value="<?php echo $frm["Whatsapp"]; ?>"></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeWhatsapp', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<textarea id="MensajeWhatsapp" name="MensajeWhatsapp" cols="10" rows="5" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'MensajeWhatsapp', LANGSESSION); ?>"><?php echo $frm["MensajeWhatsapp"]; ?></textarea>
			</div>
		</div>
	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
		</div>

	</div>




	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 1 </label>
			<input name="Foto1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 1" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto1"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto1"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2 </label>
			<input name="Foto2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto2"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto2"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto2]&campo=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3</label>
			<input name="Foto3" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto3"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto3"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4</label>
			<input name="Foto4" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto4"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto4"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto4]&campo=Foto4&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5</label>
			<input name="Foto5" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto5"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto5"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto5]&campo=Foto5&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6</label>
			<input name="Foto6" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto6"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto6"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto6]&campo=Foto6&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>


	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 7</label>
			<input name="Foto7" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 7" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto7"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto7"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto7]&campo=Foto7&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 8</label>
			<input name="Foto8" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 8" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto8"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto8"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto8]&campo=Foto8&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 9</label>
			<input name="Foto9" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 9" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto9"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto9"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto9]&campo=Foto9&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 10</label>
			<input name="Foto10" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 10" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto10"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto10"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto10]&campo=Foto10&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 11</label>
			<input name="Foto11" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto11"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto11"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto11]&campo=Foto11&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
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
			<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
		</div>
	</div>
</form>