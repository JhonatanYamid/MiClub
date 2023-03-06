<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroPqr', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo $frm["Numero"]; ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Area', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDArea" id="IDArea">
										<option value=""></option>
										<?php
										$sql_area_club = string;
										$sql_area_club = "Select * From AreaFuncionario Where IDClub = '" . SIMUser::get("club") . "' and Activo= 'S' order by Nombre";
										$qry_area_club = $dbo->query($sql_area_club);
										while ($r_area = $dbo->fetchArray($qry_area_club)) : ?>
											<option value="<?php echo $r_area["IDArea"]; ?>" <?php if ($r_area["IDArea"] == $frm["IDArea"]) echo "selected";  ?>><?php echo $r_area["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>

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
										$frm["IDUsuarioCreacion"] = SIMUser::get("IDUsuario");
									endif;


									$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuarioCreacion"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club); ?>

									<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>">
									<input type="hidden" name="IDUsuarioCreacion" value="<?php echo $frm["IDUsuarioCreacion"]; ?>" id="IDUsuarioCreacion" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>">
								</div>
							</div>

						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDTipoPqr" id="IDTipoPqr" <?php if ($_GET["action"] != "add" && SIMUser::get("IDPerfil") > 1) echo "disabled"; ?>>
										<option value=""></option>
										<?php
										$sql_tipopqr_club = "Select * From TipoPqrFuncionario Where IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S' ";
										$qry_tipopqr_club = $dbo->query($sql_tipopqr_club);
										while ($r_tipopqr = $dbo->fetchArray($qry_tipopqr_club)) : ?>
											<option value="<?php echo $r_tipopqr["IDTipoPqr"]; ?>" <?php if ($r_tipopqr["IDTipoPqr"] == $frm["IDTipoPqr"]) echo "selected";  ?>><?php echo $r_tipopqr["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Asunto', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Asunto" name="Asunto" placeholder="<?= SIMUtil::get_traduccion('', '', 'Asunto', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Asunto', LANGSESSION); ?>" value="<?php echo $frm["Asunto"]; ?>" <?php if ($_GET["action"] != "add") echo "readonly='readonly'"; ?>>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly='readonly'"; ?>><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 <?php if ($_GET["action"] != "add") echo ""; ?>" title="Fecha" value="<?php if ($_GET["action"] != "add") echo $frm["Fecha"];
																																																													else echo date("Y-m-d H:i:s"); ?>" <?php echo "readonly='readonly'"; ?>>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

								<div class="col-sm-8"><?php echo SIMHTML::formPopUp("PqrEstado", "Nombre", "Nombre", "IDPqrEstado", $frm["IDPqrEstado"], "[Seleccione el estado]", "popup mandatory", "title = \"IDTipo Archivo\"") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> </label>
								<div class="col-sm-8">

									<? if (!empty($frm[Archivo1])) { ?>
										<a target="_blank" href="<?php echo PQR_ROOT . $frm[Archivo1] ?>"><?php echo $frm[Archivo1]; ?></a>
										<!-- 	<a href="<? echo $script . ".php?action=delDoc&doc=$frm[Archivo1]&campo=Archivo1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a> -->
									<?
									} // END if

									if ($_GET["action"] == "edit") {
										$MostrarCampoArchivo = "disabled";
									} else {
										$MostrarCampoArchivo = "";
									}
									?>
									<input name="Archivo1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px" <?php echo $MostrarCampoArchivo ?>>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> 2 </label>
								<div class="col-sm-8">

									<? if (!empty($frm[Archivo2])) { ?>
										<a target="_blank" href="<?php echo PQR_ROOT . $frm[Archivo2] ?>"><?php echo $frm[Archivo2]; ?></a>
										<!-- <a href="<? echo $script . ".php?action=delDoc&doc=$frm[Archivo2]&campo=Archivo2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a> -->
									<?
									} // END if
									?>
									<input name="Archivo2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> 2" type="file" size="25" style="font-size: 10px" <?php echo $MostrarCampoArchivo ?>>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> 3 </label>
								<div class="col-sm-8">

									<? if (!empty($frm[Archivo3])) { ?>
										<a target="_blank" href="<?php echo PQR_ROOT . $frm[Archivo3] ?>"><?php echo $frm[Archivo3]; ?></a>
										<!-- <a href="<? echo $script . ".php?action=delDoc&doc=$frm[Archivo3]&campo=Archivo3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a> -->
									<?
									} // END if
									?>
									<input name="Archivo3" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> 3" type="file" size="25" style="font-size: 10px" <?php echo $MostrarCampoArchivo ?>>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Calificación:(1sobre5)', LANGSESSION); ?> </label>

								<div class="col-sm-8"><?php echo "<strong>" . $frm["Calificacion"] . "</strong> " . $frm["ComentarioCalificacion"]; ?></div>
							</div>

						</div>



						<div class="form-group first">
							<?= SIMUtil::get_traduccion('', '', 'AgregarRespuesta', LANGSESSION); ?>

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

						<div class="form-group first">
							<div class="col-sm-12">
								<input type="checkbox" name="NotificarCliente" id="NotificarCliente" <?php if ($frm["IDArea"] != "0") {  ?> checked="checked" <?php } ?> value="S" />
								<b><?= SIMUtil::get_traduccion('', '', 'NotificarvíaemailalClientelarespuesta', LANGSESSION); ?></b>
							</div>
						</div>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDAreaAnt" id="IDAreaAnt" value="<?php echo $frm["IDArea"]; ?>" />
								<input type="hidden" name="IDPqrEstadoAnt" id="IDPqrEstadoAnt" value="<?php echo $frm["IDPqrEstado"]; ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>


							</div>
						</div>

					</form>




					<div id="timeline-1">
						<div class="row">
							<div class="col-xs-12 col-sm-10 col-sm-offset-1">
								<div class="timeline-container">
									<div class="timeline-label">
										<span class="label label-primary arrowed-in-right label-lg">
											<b><?= SIMUtil::get_traduccion('', '', 'BitácoradeSeguimiento', LANGSESSION); ?></b>
										</span>
									</div>

									<?
									$sql_detalle = "SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $_GET[id] . "' Order By 	IDDetallePqr Desc";
									$qry_detalle = $dbo->query($sql_detalle);
									while ($row_detalle = $dbo->object($qry_detalle)) {
										$detalles[$row_detalle->IDDetallePqr] = $row_detalle;
									}
									$datos_club = $dbo->fetchAll("Club", " IDClub = '" . SIMUser::get("club") . "' ", "array");
									if (isset($detalles)) : ?>
										<?php foreach ($detalles as $detalle) : ?>


											<div class="timeline-items">
												<div class="timeline-item clearfix">
													<div class="timeline-info">
														<?php if ($detalle->IDUsuario > 0) { ?>
															<img alt="<?php echo $datos_club[Nombre]; ?>" src="<?php echo CLUB_ROOT . $datos_club[FotoLogoApp] ?>" />
														<?php
														} elseif ($detalle->IDUsuarioCreacion > 0) { ?>
															<img alt="<?php echo $datos_club[Nombre]; ?>" src="assets/avatars/avatar2.png" />
														<?php } ?>

														<span class="label label-info label-sm"><?php echo substr($detalle->FechaTrCr, 10); ?></span>
													</div>

													<div class="widget-box transparent">
														<div class="widget-header widget-header-small">
															<h5 class="widget-title smaller">
																<a href="#" class="blue">
																	<?php if ($detalle->IDUsuario > 0) {
																		$nombre_responsable =  $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuario . "'");
																		echo (isset($nombre_responsable) ? $nombre_responsable : '<em>N/A</em>');
																	} elseif ($detalle->IDSocio > 0) {
																		$nombre_cliente = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuarioCreacion . "'");
																		echo "(Funcionario)"
																			. (isset($nombre_cliente) ? $nombre_cliente : '<em>N/A</em>');
																	}
																	?>
																</a>
																<span class="grey"><?= SIMUtil::get_traduccion('', '', 'agregóuncomentario', LANGSESSION); ?></span>
															</h5>

															<span class="widget-toolbar no-border">
																<i class="ace-icon fa fa-clock-o bigger-110"></i>
																<?php echo $detalle->Fecha; ?>
															</span>

															<span class="widget-toolbar">
																<a href="#" data-action="reload">
																	<i class="ace-icon fa fa-refresh"></i>
																</a>

																<a href="#" data-action="collapse">
																	<i class="ace-icon fa fa-chevron-up"></i>
																</a>
															</span>
														</div>

														<div class="widget-body">
															<div class="widget-main">
																<?php echo $detalle->Respuesta; ?>



																<div class="space-6"></div>

																<div class="widget-toolbox clearfix">

																</div>
															</div>
														</div>
													</div>
												</div>





											</div><!-- /.timeline-items -->

									<?php
										endforeach;
									endif;
									?>
								</div><!-- /.timeline-container -->


							</div>
						</div>
					</div>









				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>