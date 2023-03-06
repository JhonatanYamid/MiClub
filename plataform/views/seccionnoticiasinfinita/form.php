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

							<!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seccion Padre</label>

                                        <div class="col-sm-8">
                                           <input type="hidden" id="IDSeccion" name="IDSeccion" value="<?php echo $frm["IDPadre"]; ?>">
                                           <input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields("Seccion", "Nombre", "IDSeccion = '" . $frm["IDPadre"] . "'") ?>" readonly>
											<a href="PopupSeccion.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;" class="ace-icon glyphicon glyphicon-search"></a>
                                        </div>
								</div>
							-->

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> " class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Noticiapara', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

									<?php
									if (SIMUser::get("club") == "36") {
										echo "<br>Tipo:";
										echo SIMHTML::formPopupArray(SIMResources::$tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
									} ?>

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Aplicaparaelmódulo', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<select name="IDModulo" id="IDModulo">
										<option value="">Seleccione</option>
										<?php
										$sql_mod = "SELECT M.IDModulo, CM.TituloLateral,M.Nombre
																								From Modulo M,ClubModulo CM
																								Where M.IDModulo = CM.IDModulo and  IDClub = '" . SIMUser::get("club") . "' and Activo='S'
																								And M.Tipo = 'Noticias'";
										$r_mod = $dbo->query($sql_mod);
										while ($row_mod = $dbo->fetchArray($r_mod)) { ?>
											<option value="<?php echo $row_mod["IDModulo"]; ?>" <?php if ($row_mod["IDModulo"] == $frm["IDModulo"]) echo "selected"; ?>><?php if (!empty($row_mod["TituloLateral"])) echo $row_mod["TituloLateral"];
																																										else echo $row_mod["Nombre"];  ?></option>
										<?php } ?>
									</select>

								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>



						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Iconoseccion', LANGSESSION); ?></label>

								<div class="col-sm-8">
									<?php
									if ($frm["IconoFile"]) {

										if (strstr(strtolower($frm["IconoFile"]), "http://"))
											$ruta_notfile = $frm["IconoFile"];
										else
											$ruta_notfile = IMGNOTICIA_ROOT . $frm["IconoFile"];
									?>
										<img alt="<?php echo $frm["IconoFile"] ?>" src="<?php echo $ruta_notfile; ?>" width="300" height="300">
										<a href="<? echo $script . ".php?action=DelImgNot&foto=$frm[IconoFile]&campo=IconoFile&id=" . $frm[$key] . "&IDModulo=" . $_GET["IDModulo"]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?php
									} else {
									?>
										<input type="file" name="IconoImagen" id="IconoImagen" class="popup" title="Icono Imagen">
									<?php
									}
									?>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarsoloicono', LANGSESSION); ?> ?</label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloIcono"], 'SoloIcono', "class='input mandatory'") ?></div>
							</div>





						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>



						</div>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo $_GET["IDModulo"] ?>" />
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