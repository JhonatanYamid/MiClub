<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Seccion', LANGSESSION); ?></label>

			<div class="col-sm-8">

				<input type="hidden" id="IDSeccion" name="IDSeccion" value="<?php echo $frm["IDSeccion"]; ?>">
				<input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields("Seccion2", "Nombre", "IDSeccion = '" . $frm["IDSeccion"] . "'") ?>" readonly>
				<a href="PopupSeccion2.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;" class="ace-icon glyphicon glyphicon-search"></a>

			</div>
		</div>


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Noticiapara', LANGSESSION); ?>: </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

				<?php
				if (SIMUser::get("club") == "36") {
					echo "<br>Tipo:";
					echo SIMHTML::formPopupArray(SIMResources::$tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
				}
				?>

				<select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
					<option value="">Seleccion Grupo</option>
					<?php
					$sql_grupos = "Select * From GrupoSocio Where IDClub = '" . SIMUser::get("club") . "'";
					$result_grupos = $dbo->query($sql_grupos);
					while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
						<option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>" <?php if ($frm["IDGrupoSocio"] == $row_grupos["IDGrupoSocio"]) echo "selected";  ?>><?php echo $row_grupos["Nombre"]; ?></option>
					<?php endwhile; ?>
				</select>
				<a href="gruposocio.php?action=add"> <?= SIMUtil::get_traduccion('', '', 'CrearGrupo', LANGSESSION); ?></a>

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


	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Titular', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="Titular" type="text" size="25" title="Titular" name="Titular" class="input mandatory" value="<?php echo $frm["Titular"] ?>" />
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Introduccion', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<textarea rows="5" cols="50" id="Introduccion" name="Introduccion" class="input"><?php echo $frm["Introduccion"] ?></textarea>
			</div>
		</div>



	</div>


	<div class="form-group first">


		<?= SIMUtil::get_traduccion('', '', 'Cuerpo', LANGSESSION); ?>
		<div class="col-sm-12">
			<?php
			$oCuerpo = new FCKeditor("Cuerpo");
			$oCuerpo->BasePath = "js/fckeditor/";
			$oCuerpo->Height = 400;
			//$oCuerpo->EnterMode = "p";
			$oCuerpo->Value =  $frm["Cuerpo"];
			$oCuerpo->Create();
			?>
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
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Publicar', LANGSESSION); ?> </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Publicar"], "Publicar", "title=\"Publicar\"") ?></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input" value="<?php echo $frm["Orden"] ?>" />
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaIncio', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaIncio', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>">
			</div>
		</div>

	</div>
	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 1 </label>

			<div class="col-sm-8">
				<?php
				$ruta_adjunto1file = string;
				if ($frm["Adjunto1File"]) {

					if (strstr(strtolower($frm["Adjunto1File"]), "http://"))
						$ruta_adjunto1file = $frm["Adjunto1File"];
					else
						$ruta_adjunto1file = IMGNOTICIA_ROOT . $frm["Adjunto1File"];
				?>
					<a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto1File"] ?></a>
					<a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto1File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="Adjunto1Documento" id="Adjunto1Documento" class="popup" title="Noticia Documento">
				<?php
				}
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 2 </label>

			<div class="col-sm-8">
				<?php
				if ($frm["Adjunto2File"]) {

					if (strstr(strtolower($frm["Adjunto2File"]), "http://"))
						$ruta_adjunto2file = $frm["Adjunto2File"];
					else
						$ruta_adjunto2file = IMGNOTICIA_ROOT . $frm["Adjunto2File"];
				?>
					<a target="_blank" href="<?php echo $ruta_adjunto2file; ?>"><?php echo $frm["Adjunto2File"] ?></a>
					<a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto2File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="Adjunto2Documento" id="Adjunto2Documento" class="popup" title="Noticia Documento">
				<?php
				}
				?>

			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 3 </label>

			<div class="col-sm-8">
				<?php
				$ruta_adjunto1file = string;
				if ($frm["Adjunto3File"]) {

					if (strstr(strtolower($frm["Adjunto3File"]), "http://"))
						$ruta_adjunto1file = $frm["Adjunto3File"];
					else
						$ruta_adjunto1file = IMGNOTICIA_ROOT . $frm["Adjunto3File"];
				?>
					<a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto3File"] ?></a>
					<a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto3File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="Adjunto3Documento" id="Adjunto3Documento" class="popup" title="Noticia Documento">
				<?php
				}
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 4 </label>

			<div class="col-sm-8">
				<?php
				if ($frm["Adjunto4File"]) {

					if (strstr(strtolower($frm["Adjunto4File"]), "http://"))
						$ruta_adjunto2file = $frm["Adjunto4File"];
					else
						$ruta_adjunto2file = IMGNOTICIA_ROOT . $frm["Adjunto4File"];
				?>
					<a target="_blank" href="<?php echo $ruta_adjunto2file; ?>"><?php echo $frm["Adjunto4File"] ?></a>
					<a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto4File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="Adjunto4Documento" id="Adjunto4Documento" class="popup" title="Noticia Documento">
				<?php
				}
				?>

			</div>
		</div>

	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenNoticia', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<?php
				if ($frm["NoticiaFile"]) {

					if (strstr(strtolower($frm["NoticiaFile"]), "http://"))
						$ruta_notfile = $frm["NoticiaFile"];
					else
						$ruta_notfile = IMGNOTICIA_ROOT . $frm["NoticiaFile"];
				?>
					<img alt="<?php echo $frm["NoticiaFile"] ?>" src="<?php echo $ruta_notfile; ?>" width="300" height="300">
					<a href="<? echo $script . ".php?action=DelImgNot&cam=NoticiaFile&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="NoticiaImagen" id="NoticiaImagen" class="popup" title="Noticia Imagen">
				<?php
				}
				?>
			</div>
		</div>


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fotoportada(opcional)', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<?php
				if ($frm["FotoPortada"]) {

					if (strstr(strtolower($frm["FotoPortada"]), "http://"))
						$ruta_notfile = $frm["FotoPortada"];
					else
						$ruta_notfile = IMGNOTICIA_ROOT . $frm["FotoPortada"];
				?>
					<img alt="<?php echo $frm["FotoPortada"] ?>" src="<?php echo $ruta_notfile; ?>" width="300" height="300">
					<a href="<? echo $script . ".php?action=DelImgNot&cam=FotoPortada&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="FotoPortada" id="FotoPortada" class="popup" title="Foto Portada">
				<?php
				}
				?>
			</div>
		</div>



		<!--
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 2</label>

										<div class="col-sm-8">
											<?php
											if ($frm["FotoDestacada"]) {
												if (strstr(strtolower($frm["FotoDestacada"]), "http://"))
													$ruta_dest = $frm["FotoDestacada"];
												else
													$ruta_dest = IMGNOTICIA_ROOT . $frm["FotoDestacada"];


											?>
												  <img alt="<?php echo $frm["FotoDestacada"] ?>" src="<?php echo $ruta_dest; ?>" />
                                                  <a href="<? echo $script . ".php?action=DelImgNot&cam=FotoDestacada&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
												  <?php
												} else {
													?>
												  <input type="file" name="FotoDestacada" id="FotoDestacada" class="popup" title="Noticia FotoDestacada" />
												  <?php
												}
													?>
										</div>
								</div>
                                -->

	</div>

	<!--
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 3</label>

										<div class="col-sm-8">
											<?php
											if ($frm["Foto1"]) {
												if (strstr(strtolower($frm["Foto1"]), "http://"))
													$ruta_foto1 = $frm["Foto1"];
												else
													$ruta_foto1 = IMGNOTICIA_ROOT . $frm["Foto1"];

											?>
											  <img alt="<?php echo $frm["Foto1"] ?>" src="<?php echo $ruta_foto1; ?>" />
                                              <a href="<? echo $script . ".php?action=DelImgNot&cam=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											  <?php
											} else {
												?>
											  <input type="file" name="Foto1" id="Foto1" class="popup" title="Noticia Detalle" />
											  <?php
											}
												?>
										</div>
								</div>


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 4</label>

										<div class="col-sm-8">
											<?php
											if ($frm["Foto2"]) {
												if (strstr(strtolower($frm["Foto2"]), "http://"))
													$ruta_foto2 = $frm["Foto2"];
												else
													$ruta_foto2 = IMGNOTICIA_ROOT . $frm["Foto2"];
											?>
											  <img alt="<?php echo $frm["Foto2"] ?>" src="<?php echo $ruta_foto2; ?>" />
                                              <a href="<? echo $script . ".php?action=DelImgNot&cam=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											  <?php
											} else {
												?>
											  <input type="file" name="Foto2" id="Foto2" class="popup" title="Noticia Detalle" />
											  <?php
											}
												?>
										</div>
								</div>


							</div>
                             -->




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

<?
include("cmp/footer_grid.php");
?>