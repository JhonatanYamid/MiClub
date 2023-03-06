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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Numero" name="Numero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" value="<?php echo $frm["Numero"]; ?>" readonly>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
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
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>

										<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
										<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">




									</div>
								</div>


							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'AccionSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="AccionSocio" name="AccionSocio" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionSocio', LANGSESSION); ?>" class="col-xs-12 form-control" title="<?= SIMUtil::get_traduccion('', '', 'AccionSocio', LANGSESSION); ?>" value="<?php echo $r_socio["Accion"]; ?>" readonly>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CorreoElectronico" name="CorreoElectronico" placeholder="<?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?>" class="col-xs-12 form-control" title="<?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?>" value="<?php echo $r_socio["CorreoElectronico"]; ?>" readonly>
								</div>
							</div>


						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CelularSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Celular" name="Celular" placeholder="<?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?>" class="col-xs-12 form-control" title="<?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?>" value="<?php echo $r_socio["Celular"]; ?>" readonly>
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div id="SocioEspecifico" class="form-group first ">

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'BeneficiariosSocios', LANGSESSION); ?>: </label>

									<div class="col-sm-8">
										<input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-socios-beneficiarioscanjes" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
										<br><a id="agregar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
										<br>
										<select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple>
											<?php
											$item = 1;
											$array_invitados = explode("|", $frm["IDSocioBeneficiario"]);
											foreach ($array_invitados as $id_invitado => $datos_invitado) :
												if (!empty($datos_invitado)) {
													//$array_datos_invitados = explode("-", $datos_invitado);
													$item--;
													$IDSocioInvitacion = $datos_invitado;
													if ($IDSocioInvitacion > 0) :
														$nombre_socio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitacion . "'"));
											?>
														<option value="<?php echo  $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
											<?php
													endif;
												}
											endforeach;
											?>
										</select>

										<input type="hidden" name="IDSocioBeneficiario" id="IDSocioBeneficiario" value="">

									</div>



								</div>

							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pais', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDPais" id="IDPais">
										<option value=""></option>
										<?php
										$sql_lista_paises = "Select IDPais,Nombre From Pais Where Publicar = 'S' Order By Nombre";
										$qry_lista_paises = $dbo->query($sql_lista_paises);
										while ($r_listapaises = $dbo->fetchArray($qry_lista_paises)) : ?>
											<option value="<?php echo $r_listapaises["IDPais"]; ?>" <?php if ($r_listapaises["IDPais"] == $frm["IDPais"]) echo "selected";  ?>><?php echo $r_listapaises["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Ciudad', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDCiudad" id="IDCiudad">
										<option value=""></option>
										<?php
										$sql_lista_ciudad = "Select IDCiudad,Nombre From Ciudad Where Publicar = 'S' Order By Nombre";
										$qry_lista_ciudad = $dbo->query($sql_lista_ciudad);
										while ($r_listaciudad = $dbo->fetchArray($qry_lista_ciudad)) : ?>
											<option value="<?php echo $r_listaciudad["IDCiudad"]; ?>" <?php if ($r_listaciudad["IDCiudad"] == $frm["IDCiudad"]) echo "selected";  ?>><?php echo $r_listaciudad["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>
						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Club', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDListaClubes" id="IDListaClubes">
										<option value=""></option>
										<?php
										$sql_lista_club = "Select * From ListaClubes Where Publicar = 'S' Order By Nombre";
										$qry_lista_club = $dbo->query($sql_lista_club);
										while ($r_listaclub = $dbo->fetchArray($qry_lista_club)) : ?>
											<option value="<?php echo $r_listaclub["IDListaClubes"]; ?>" <?php if ($r_listaclub["IDListaClubes"] == $frm["IDListaClubes"]) echo "selected";  ?>><?php echo $r_listaclub["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"]; ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>>
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CantidadDias', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CantidadDias" name="CantidadDias" placeholder="<?= SIMUtil::get_traduccion('', '', 'CantidadDias', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'CantidadDias', LANGSESSION); ?>" value="<?php echo $frm["CantidadDias"]; ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDEstadoCanjeSolicitud" id="IDEstadoCanjeSolicitud " class="EstadoSolicitudCanje">
										<option value="">[Selecciona una Opcion]</option>
										<?php
										$sql_estadocanj_club = "Select * From EstadoCanjeSolicitud Where IDClub = '" . SIMUser::get("club") . "'";
										$qry_estadocanj_club = $dbo->query($sql_estadocanj_club);
										while ($r_estadocanj = $dbo->fetchArray($qry_estadocanj_club)) : ?>
											<option value="<?php echo $r_estadocanj["IDEstadoCanjeSolicitud"]; ?>" <?php if ($r_estadocanj["IDEstadoCanjeSolicitud"] == $frm["IDEstadoCanjeSolicitud"]) echo "selected";  ?>><?php echo $r_estadocanj["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>



						</div>


						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentarioSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="ComentariosSocio" name="ComentariosSocio" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentarioSocio', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["ComentariosSocio"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentarioClub', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="ComentariosClub" name="ComentariosClub" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentarioClub', LANGSESSION); ?>"><?php echo $frm["ComentariosClub"]; ?></textarea>

								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarcorreoalClubdestino', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["EnviarCorreo"], 'EnviarCorreo', "class='input mandatory'") ?>
								</div>

							</div>
						</div>


						<?php
						$sql_estadocanje_aprobado = "Select * From EstadoCanjeSolicitud Where IDClub = '" . SIMUser::get("club") . "' AND Descripcion='Aprobado'";
						$qry_estadocanje_aprobado = $dbo->query($sql_estadocanje_aprobado);
						$estadoCanje = $dbo->fetchArray($qry_estadocanje_aprobado);
						$frm1 = $estadoCanje;

						if ($frm1["IDEstadoCanjeSolicitud"] == $frm["IDEstadoCanjeSolicitud"]) {


							$estado = "display:block;";
						} else $estado = "display:none;";
						?>
						<div class="form-group first " id="archivosCarnetVacunacion" style="<?php echo $estado; ?>">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fotocarnetvacunacion', LANGSESSION); ?> </label>
								<input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["Foto1"])) {
										echo "<img src='" . BANNERAPP_ROOT . $frm["Foto1"] . "' width='200px' height='200px'>";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Archivocarnetvacunacion', LANGSESSION); ?> </label>
								<input name="Foto2" id=file class="" title="Foto2" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["Foto2"])) {
										echo "<img src='" . BANNERAPP_ROOT . $frm["Foto2"] . "' width='200px' height='200px'>";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto2]&campo=Foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
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


							</div>
						</div>



						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-glass green"></i>
								<?= SIMUtil::get_traduccion('', '', 'DetalleBeneficiarios', LANGSESSION); ?>
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<?php

								// Consulto los beneficiarios seleccionados
								$array_beneficarios = explode("|", $frm["IDSocioBeneficiario"]);

								?>


								<table id="simple-table" class="table table-striped table-bordered table-hover">
									<tr>

										<th><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></th>
										<th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
									</tr>
									<tbody id="listacontactosanunciante">
										<?php

										if (count($array_beneficarios) > 0) :
											foreach ($array_beneficarios as $id_socio) {
												$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $id_socio . "' ", "array");

										?>

												<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
													<td aling="center">
														<?
														echo $datos_socio["Accion"];
														?>
													</td>
													<td>
														<?php
														echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
														?>
													</td>
												</tr>
										<?php
											}
										endif;
										?>
									</tbody>
									<tr>
										<th class="texto" colspan="13"></th>
									</tr>
								</table>









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