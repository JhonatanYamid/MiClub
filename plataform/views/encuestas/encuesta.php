<!-- PAGE CONTENT BEGINS -->


<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Encuestapara', LANGSESSION); ?>: </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Aplicaparaelmódulo', LANGSESSION); ?>: </label>

			<div class="col-sm-8">
				<select name="IDModulo" id="IDModulo">
					<option value="0">Encuesta</option>
					<?php
					$sql_mod = "SELECT M.IDModulo, CM.TituloLateral,M.Nombre
																	From Modulo M,ClubModulo CM
																	Where M.IDModulo = CM.IDModulo and  IDClub = '" . SIMUser::get("club") . "' and Activo='S'
																	And M.Tipo = 'Encuesta'";
					$r_mod = $dbo->query($sql_mod);
					while ($row_mod = $dbo->fetchArray($r_mod)) { ?>
						<option value="<?php echo $row_mod["IDModulo"]; ?>" <?php if ($row_mod["IDModulo"] == $frm["IDModulo"]) echo "selected"; ?>><?php if (!empty($row_mod["TituloLateral"])) echo $row_mod["TituloLateral"];
																																					else echo $row_mod["Nombre"];  ?></option>
					<?php } ?>
				</select>

			</div>
		</div>
	</div>

	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Nombre" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" />
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Orden" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" name="Orden" class="input mandatory" value="<?php echo $frm["Orden"] ?>" />
			</div>
		</div>

	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<textarea rows="5" cols="50" id="Descripcion" name="Descripcion" class="input"><?php echo $frm["Descripcion"] ?></textarea>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Respuestaalguardar', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<textarea rows="5" cols="50" id="RespuestaGuardar" name="RespuestaGuardar" class="input"><?php echo $frm["RespuestaGuardar"] ?></textarea>
			</div>
		</div>


	</div>

	<!--Inicio Notificación -->
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EnviarCorreoa?(Cuandoalguienhagaunregistro)', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="CorreoRegistro" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'EnviarCorreoa?(Cuandoalguienhagaunregistro)', LANGSESSION); ?>" name="CorreoRegistro" class="input " value="<?php echo $frm["CorreoRegistro"] ?>" />
			</div>
		</div>
	</div>
	<!-- Fin Notificación -->


	<div class="form-group first ">



		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicioPublicacion', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaFinPublicacion', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Solicitaralabrirapp', LANGSESSION); ?>?</label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarAbrirApp"], "SolicitarAbrirApp", "title=\"Solicitar Abrir App\"") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Solopermitirllenarlaencuesta1vezporusuario', LANGSESSION); ?>?</label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["UnaporSocio"], "UnaporSocio", "title=\"UnaporSocio\"") ?>
			</div>
		</div>
	</div>



	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenDestacada', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<? if (!empty($frm[Imagen])) {
					echo "<img src='" . BANNERAPP_ROOT . "$frm[Imagen]' width=55 >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Imagen]&campo=Imagen&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
				<input name="Imagen" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'ImagenDestacada', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Imagenlistadoencuesta', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<? if (!empty($frm[ImagenEncuesta])) {
					echo "<img src='" . BANNERAPP_ROOT . "$frm[ImagenEncuesta]' width=55 >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenEncuesta]&campo=ImagenEncuesta&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
				<input name="ImagenEncuesta" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Imagenlistadoencuesta', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
			</div>
		</div>
	</div>
	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Publicar"], "Publicar", "title=\"Publicar\"") ?>
			</div>
		</div>
	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-12">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrara', LANGSESSION); ?> : </label>

			<div class="col-sm-8">
				<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="S" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "S") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'TodoslosUsuarios', LANGSESSION); ?>
				<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="SE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "SE") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'UsuariosEspecificos', LANGSESSION); ?>
				<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GS" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "GS") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'GrupodeUsuarios', LANGSESSION); ?>
				<!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="E" title="DirigidoA"/>Todos los Empleado-->
				<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="EE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "EE") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'EmpleadosEspecificos', LANGSESSION); ?>
				<!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GE" title="DirigidoA"/>Grupo de Empleados-->


			</div>
		</div>

	</div>


	<div id="SocioGrupo" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "GS") echo "";
															else echo "display:none"; ?> ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?>: </label>

			<div class="col-sm-8">
				<select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
					<option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option>
					<?php
					$sql_grupos = "Select * From GrupoSocio Where IDClub = '" . SIMUser::get("club") . "'";
					$result_grupos = $dbo->query($sql_grupos);
					while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
						<option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>" <?php if ($frm["IDGrupoSocio"] == $row_grupos["IDGrupoSocio"]) echo "selected";  ?>><?php echo $row_grupos["Nombre"]; ?></option>
					<?php endwhile; ?>
				</select>
				<a href="gruposocio.php?action=add"><?= SIMUtil::get_traduccion('', '', 'CrearGrupo', LANGSESSION); ?></a>

				<br>
				<a id="agregar_invitadoGrupo" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitadoGrupo" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
				<br>
				<select name="SocioInvitado[]" id="SocioInvitadoGrupo" class="col-xs-8" multiple>
					<?php
					$item = 1;
					$array_invitados = explode("|||", $frm["SeleccionGrupo"]);
					foreach ($array_invitados as $id_invitado => $datos_invitado) :
						if (!empty($datos_invitado)) {
							$array_datos_invitados = explode("-", $datos_invitado);
							$item--;
							$IDSocioInvitacion = $array_datos_invitados[1];
							if ($IDSocioInvitacion > 0) :
								$nombre_socio = utf8_encode($dbo->getFields("GrupoSocio", "Nombre", "IDGrupoSocio = '" . $IDSocioInvitacion . "'"));
					?>
								<option value="<?php echo "grupo-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
					<?php
							endif;
						}
					endforeach; ?>
				</select>
				<input type="hidden" name="SeleccionGrupo" id="SeleccionGrupo" value="">
			</div>
		</div>
	</div>

	<div id="SocioEspecifico" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "SE") echo "";
																else echo "display:none"; ?> ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuarios', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-socios" title=" <?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
				<br>
				<a id="agregar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
				<br>
				<select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple>
					<?php
					$item = 1;
					$array_invitados = explode("|||", $frm["InvitadoSeleccion"]);
					foreach ($array_invitados as $id_invitado => $datos_invitado) :
						if (!empty($datos_invitado)) {
							$array_datos_invitados = explode("-", $datos_invitado);
							$item--;
							$IDSocioInvitacion = $array_datos_invitados[1];
							if ($IDSocioInvitacion > 0) :
								$nombre_socio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitacion . "'"));
					?>
								<option value="<?php echo "socio-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
					<?php
							endif;
						}
					endforeach; ?>
				</select>
				<input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
			</div>
		</div>
	</div>

	<div id="EmpleadoEspecifico" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "EE") echo "";
																	else echo "display:none"; ?> ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Empleados', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<input type="text" id="AccionInvitadoUsuario" name="AccionInvitadoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionarioEncuestas" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
				<br>
				<a id="agregar_empleado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_empleado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
				<br>
				<select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8" multiple>
					<?php
					$item = 1;
					$array_invitados = explode("|||", $frm["UsuarioSeleccion"]);
					foreach ($array_invitados as $id_invitado => $datos_invitado) :
						if (!empty($datos_invitado)) {
							$array_datos_invitados = explode("-", $datos_invitado);
							$item--;
							$IDSocioInvitacion = $array_datos_invitados[1];
							if ($IDSocioInvitacion > 0) :
								$nombre_socio = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Usuario", "Apellido", "IDUsuario = '" . $IDSocioInvitacion . "'"));
					?>
								<option value="<?php echo "usuario-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
					<?php
							endif;
						}
					endforeach; ?>
				</select>
				<input type="hidden" name="UsuarioSeleccion" id="UsuarioSeleccion" value="">
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