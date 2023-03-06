<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'CREARNUEVA', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Dirigidoa', LANGSESSION); ?>:</label>

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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarEnEncabezadodeApp', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Header"], 'Header', "class='input'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarenlaparteabajodeApp', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Footer"], 'Footer', "class='input'") ?>
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>
								<input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm[Foto1])) {
										echo "<img src='" . PUBLICIDAD_ROOT . "$frm[Foto1]' width='200' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
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
									<input type="text" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>">
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

						</div>


						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-check green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Accionalpulsarsobrelapublicidad', LANGSESSION); ?>
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Realizarlasiguienteaccion', LANGSESSION); ?>: </label>

								<div class="col-sm-8">

									<select class="form-control" id="AccionClick" name="AccionClick">
										<option value=""></option>
										<option value="Url" <? if ($frm["AccionClick"] == "Url") echo "selected='selected'"; ?>>Url</option>
										<option value="WebView" <? if ($frm["AccionClick"] == "WebView") echo "selected='selected'"; ?>>Mensaje Promoci&oacute;n</option>
										<option value="SinAccion" <? if ($frm["AccionClick"] == "SinAccion") echo "selected='selected'"; ?>>Sin Accion</option>
									</select>

								</div>
							</div>


						</div>


						<?php if ($frm["AccionClick"] == "Url")
							$oculta_url = "";
						else
							$oculta_url = "none";
						?>
						<div class="form-group first" id="Url" style="display:<?php echo $oculta_url; ?>">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Url', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Url" name="Url" placeholder="Url" class="col-xs-12 " title="Url" value="<?php echo $frm["Url"]; ?>">
								</div>

								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Abrirenventanaexterna', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["VentanaExterna"], 'VentanaExterna', "class='input'") ?>
								</div>
							</div>



						</div>


						<?php if ($frm["AccionClick"] == "WebView")
							$oculta_cuerpo = "";
						else
							$oculta_cuerpo = "none";
						?>
						<div class="form-group first" id="CuerpoPublicidad" style="display:<?php echo $oculta_cuerpo; ?>">

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




						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-puzzle-piece green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Ubicaciondentrodelapp', LANGSESSION); ?>
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">
								<?php
								// Consulto los modulos disponibles del club
								$sql_modulo_publicidad = $dbo->query("select * from PublicidadModulo where IDPublicidad = '" . $frm["IDPublicidad"] . "' and Activo = 'S'");
								while ($r_modulo_publicidad = $dbo->object($sql_modulo_publicidad)) {
									$modulo_publicidad[] = $r_modulo_publicidad->IDModulo;
								}
								?>
								<table id="simple-table" class="table table-striped table-bordered table-hover">
									<tr>
										<th class="title" colspan="13"><?= SIMUtil::get_traduccion('', '', 'Ubicarenlosmodulos', LANGSESSION); ?>:</th>
									</tr>
									<tr>
										<th><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></th>
										<th><?= SIMUtil::get_traduccion('', '', 'Modulo', LANGSESSION); ?></th>
										<th><?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?></th>
									</tr>
									<tbody id="listacontactosanunciante">
										<?php
										$r_modulo = &$dbo->all("ClubModulo", "IDClub = '" . SIMUser::get("club") . "' and IDModulo in (11,1,25,26) and Activo = 'S'");

										while ($r = $dbo->object($r_modulo)) {
										?>

											<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
												<td aling="center">
													<input type="checkbox" name="IDModulo<?php echo $r->IDModulo; ?>" id="IDModulo<?php echo $r->IDModulo; ?>" <?php if (in_array($r->IDModulo, $modulo_publicidad)) echo "checked"; ?>>
												</td>
												<td><?php echo utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r->IDModulo . "'")); ?></td>
												<td>
													<? if (!empty($r->Icono)) {
														echo "<img src='" . MODULO_ROOT . "$r->Icono' width=55 >";
													?>
													<?
													} // END if
													?>


												</td>
											</tr>
										<?php
										}
										?>
									</tbody>
									<tr>
										<th class="texto" colspan="13"></th>
									</tr>
								</table>


							</div>

						</div>


						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-puzzle-piece green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Enlasreservasde', LANGSESSION); ?>:
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">
								<?php
								// Consulto los modulos disponibles del club
								$sql_servicio_publicidad = $dbo->query("select * from PublicidadServicio where IDPublicidad = '" . $frm["IDPublicidad"] . "'");
								while ($r_servicio_publicidad = $dbo->object($sql_servicio_publicidad)) {
									$servicio_publicidad[] = $r_servicio_publicidad->IDServicio;
								}
								?>
								<table id="simple-table" class="table table-striped table-bordered table-hover">
									<tr>
										<th class="title" colspan="13"><?= SIMUtil::get_traduccion('', '', 'UbicarenlosServicios', LANGSESSION); ?>:</th>
									</tr>
									<tr>
										<th><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></th>
										<th><?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?></th>
										<th><?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?></th>
									</tr>
									<tbody id="listacontactosanunciante">
										<?php
										$r_servicio = &$dbo->all("Servicio", "IDClub = '" . SIMUser::get("club") . "'");

										while ($r = $dbo->object($r_servicio)) {
											$datos_serv_club = $dbo->fetchAll("ServicioClub", "IDClub = '" . SIMUser::get("club") . "' and IDServicioMaestro = '" . $r->IDServicioMaestro . "' ");
											if ($datos_serv_club["Activo"] == 'S') {

										?>

												<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
													<td aling="center">
														<input type="checkbox" name="IDServicio[]" value="<?php echo $r->IDServicio; ?>" <?php if (in_array($r->IDServicio, $servicio_publicidad)) echo "checked"; ?>>
													</td>
													<td>
														<?php
														$NombrePersonalizado = $datos_serv_club["TituloServicio"];
														if (!empty($NombrePersonalizado)) {
															echo $NombrePersonalizado;
														} elseif (!empty($r->Nombre)) {
															echo $r->Nombre;
														} else {
															echo $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r->IDServicioMaestro . "'");
														}
														?>
													</td>
													<td>
														<? if (!empty($r->Icono)) {
															echo "<img src='" . SERVICIO_ROOT . "$r->Icono' width=55 >";
														?>
														<?
														} // END if
														?>


													</td>
												</tr>
										<?php
											}
										}
										?>
									</tbody>
									<tr>
										<th class="texto" colspan="13"></th>
									</tr>
								</table>


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
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>