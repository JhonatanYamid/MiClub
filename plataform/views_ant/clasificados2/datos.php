<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



	<div class="form-group first ">



		<div class="form-group first ">

			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?> </label>

				<div class="col-sm-8">
					<!--
                                          <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
											$sql_socio_club = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' Order by Nombre Asc";
											$qry_socio_club = $dbo->query($sql_socio_club);
											while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
										    <option value="<?php echo $r_socio["IDUsuario"]; ?>" <?php if ($r_socio["IDUsuario"] == $frm["IDUsuario"]) echo "selected";  ?>><?php echo $r_socio["Nombre"]; ?></option>
										    <?php
											endwhile;    ?>
									      </select>
                                          -->

					<?php

					if ($_GET["action"] == "add") :
						$frm["IDUsuario"] = SIMUser::get("IDUsuario");
					endif;


					$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
					$qry_socio_club = $dbo->query($sql_socio_club);
					$r_socio = $dbo->fetchArray($qry_socio_club); ?>
					<!-- 	<?php echo $sql_socio_club . "<br>";
								print_r($frm); ?> -->

					<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>">
					<input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>">
				</div>
			</div>
		</div>

		<div class="form-group first">




			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>

				<div class="col-sm-8"><?php echo SIMHTML::formPopUp("SeccionClasificados", "Nombre", "Nombre", "IDSeccionClasificados", $frm["IDSeccionClasificados"], "[Seleccione categoria", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "' and DirigidoA = 'E'") ?></div>

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
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>

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
	</div>
</form>