<?php
include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<!--
																					<select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
												<option value=""></option>
												<?php
												$sql_socio_club = "Select * From Socio Where IDClub = '" . SIMUser::get("club") . "' Order by Apellido Asc";
												$qry_socio_club = $dbo->query($sql_socio_club);
												while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
												<option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if ($r_socio["IDSocio"] == $frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]); ?></option>
												<?php
												endwhile;    ?>
												</select>
																					-->
									<?php
									$sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club);
									if (!empty($frm["IDSocio"])) {
										$label_accion = " Accion: " . $r_socio["Accion"];
										if ($frm[IDClub] == 35)
											$label_accion = " Casa: " . $r_socio["Predio"];
									}
									?>

									<input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"] . $label_accion) ?>">
									<?= SIMUtil::get_traduccion('', '', 'Busquedapor:Accion,Nombre,Apellido,NumeroDocumento', LANGSESSION); ?>
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Titulo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>



						</div>






						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha" value="<?php echo $frm["Fecha"] ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<? if (!empty($frm[Archivo1])) { ?>
										<a target="_blank" href="<?php echo DOCUMENTO_ROOT . $frm[Archivo1] ?>"><?php echo $frm[Archivo1]; ?></a>
										<a href="<? echo $script . ".php?action=delDoc&doc=$frm[Archivo1]&campo=Archivo1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Archivo1" id=file class="" title="Archivo1" type="file" size="25" style="font-size: 10px">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>
								<input name="Icono" id=file class="" title="Icono" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm[Icono])) {
										echo "<img src='" . DOCUMENTO_ROOT . "$frm[Icono]' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>


						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 " title="Orden" value="<?php echo $frm["Orden"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DebeAceptarTerminos', LANGSESSION); ?> </label>


								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["DebeAceptarTerminos"], 'DebeAceptarTerminos', "class='input mandatory'") ?></div>

							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TerminosAceptados', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["TerminosAceptados"], 'TerminosAceptados', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaAceptacionTerminos', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaAceptacionTerminos" name="FechaAceptacionTerminos" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaAceptacionTerminos', LANGSESSION); ?>" class="col-xs-12 calendar" title="FechaAceptacionTerminos" value="<?php echo $frm["FechaAceptacionTerminos"] ?>">
								</div>
							</div>

						</div>

						<div class="form-group first">
							<?= SIMUtil::get_traduccion('', '', 'TextoTerminosHtml', LANGSESSION); ?>
							<div class="col-sm-12">
								<?php
								$oCuerpoInvi = new FCKeditor("TextoTerminosHtml");
								$oCuerpoInvi->BasePath = "js/fckeditor/";
								$oCuerpoInvi->Height = 200;
								//$oCuerpo->EnterMode = "p";
								$oCuerpoInvi->Value =  $frm["TextoTerminosHtml"];
								$oCuerpoInvi->Create();
								?>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeBotonAceptarTerminos', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonAceptarTerminosLabel" name="BotonAceptarTerminosLabel" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeBotonAceptarTerminos', LANGSESSION); ?> " class="col-xs-12 " title="BotonAceptarTerminosLabel" value="<?php echo $frm["BotonAceptarTerminosLabel"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeTerminosAceptados', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelTerminosAceptados" name="LabelTerminosAceptados" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeTerminosAceptados', LANGSESSION); ?>" class="col-xs-12 " title="LabelTerminosAceptados" value="<?php echo $frm["LabelTerminosAceptados"]; ?>">
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
						</div>
						<!-- Fin Notificación -->




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