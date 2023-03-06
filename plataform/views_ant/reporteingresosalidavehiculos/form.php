<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
		</h4>


	</div>



	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->

					<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">






						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio</label>

								<div class="col-sm-8">
									<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($newmode == "updateingreso") echo "readonly"; ?> value="<?php echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $frm["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $frm["IDSocio"] . "'") ?>">
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Ingreso</label>

								<div class="col-sm-8">
									<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 <?php if ($newmode != "updateingreso") echo "calendariohoy"; ?> " title="Fecha Ingreso" value="<?php if ($frm["FechaInicio"] == "0000-00-00" || $frm["FechaInicio"] == "") echo date("Y-m-d");
																																																									else echo $frm["FechaInicio"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
								</div>
							</div>

						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin</label>

								<div class="col-sm-8">
									<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 <?php if ($newmode != "updateingreso") echo "calendariohoy"; ?> " title="Fecha Fin" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") echo date("Y-m-d");
																																																						else echo $frm["FechaFin"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
								</div>
							</div>

						</div>




						<?php
						$sql_tipodoc = $dbo->query("Select * From TipoDocumento Where Publicar = 'S'");
						while ($row_tipo_doc = $dbo->fetchArray($sql_tipodoc)) :
							$array_tipo_doc[$row_tipo_doc["IDTipoDocumento"]] = $row_tipo_doc["Nombre"];
						endwhile;
						for ($cont_invitado = 1; $cont_invitado <= 5; $cont_invitado++) :
						?>

							<div class="col-sm-12">
								<div class="widget-box">
									<div class="widget-header">
										<h4 class="smaller">
											Contratista <?php echo $cont_invitado; ?>
										</h4>
									</div>

									<div class="widget-body">
										<div class="widget-main">
											<p class="muted">

											<div class="form-group first ">
												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Documento </label>

													<div class="col-sm-8">
														<select name="IDTipoDocumento<?php echo $cont_invitado; ?>" id="IDTipoDocumento<?php echo $cont_invitado; ?>" class="popup">
															<option value=""></option>
															<?php foreach ($array_tipo_doc as $keytipodoc => $nomtipodoc) : ?>
																<option value="<?php echo $keytipodoc; ?>" <?php if ($datos_invitado_edit["IDTipoDocumento"] == $keytipodoc) echo "selected"; ?>><?php echo $nomtipodoc; ?></option>
															<?php endforeach; ?>
														</select>

													</div>
												</div>

												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Documento </label>

													<div class="col-sm-8">
														<input id="NumeroDocumento<?php echo $cont_invitado; ?>" type="text" size="25" title="Numero Documento" name="NumeroDocumento<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input autocomplete-ajax_tblinvitado" value="<?php if (!empty($datos_invitado_edit["NumeroDocumento"])) {
																																																																												echo $datos_invitado_edit["NumeroDocumento"];
																																																																											} ?>" />
														<input type="hidden" name="IDInvitado<?php echo $cont_invitado; ?>" value="<?php echo $frm["IDInvitado"]; ?>" id="IDInvitado<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" title="Numero Documento">

													</div>
												</div>



											</div>


											<div class="form-group first ">


												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

													<div class="col-sm-8">
														<input id="Nombre<?php echo $cont_invitado; ?>" type="text" size="25" title="Nombre" name="Nombre<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if (!empty($datos_invitado_edit["Nombre"])) {
																																																														echo $datos_invitado_edit["Nombre"];
																																																													} ?>" />
													</div>
												</div>

												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido </label>

													<div class="col-sm-8">
														<input id="Apellido<?php echo $cont_invitado; ?>" type="text" size="25" title="Apellido" name="Apellido<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if (!empty($datos_invitado_edit["Apellido"])) {
																																																															echo $datos_invitado_edit["Apellido"];
																																																														} ?>" />
													</div>
												</div>


											</div>

											<div class="form-group first ">

												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

													<div class="col-sm-8">
														<input id="Email<?php echo $cont_invitado; ?>" type="text" size="25" title="Email" name="Email<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if (!empty($datos_invitado_edit["Email"])) {
																																																													echo $datos_invitado_edit["Email"];
																																																												} ?>" />
													</div>
												</div>

												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>

													<div class="col-sm-8">
														<input id="Telefono<?php echo $cont_invitado; ?>" type="text" size="25" title="Telefono" name="Telefono<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input" value="<?php if (!empty($datos_invitado_edit["Telefono"])) {
																																																															echo $datos_invitado_edit["Telefono"];
																																																														} ?>" />

													</div>
												</div>




											</div>

											<div class="form-group first ">





												<div class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Autorizacion </label>

													<div class="col-sm-8">
														<?php foreach (SIMResources::$tipoautorizacion as $key_tipo => $dato_tipo) : ?>
															<input type="radio" name="TipoAutorizacion<?php echo $cont_invitado; ?>" id="TipoAutorizacion<?php echo $cont_invitado; ?>" value="<?php echo $dato_tipo; ?>" <?php if ($frm["TipoInvitacion"] == $dato_tipo) echo "checked"; ?>><?php echo $dato_tipo; ?>
														<?php endforeach; ?>
													</div>
												</div>



											</div>



											<p>Veh&iacute;culo</p>



											<?php
											$cont_vehiculo = 1;
											for ($cont_vehiculo = 1; $cont_vehiculo <= 1; $cont_vehiculo++) : ?>
												<div class="form-group first ">

													<div class="col-xs-12 col-sm-4">
														<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Placa </label>

														<div class="col-sm-8">
															<input id="Placa<?php echo $cont_vehiculo; ?>" type="text" size="25" title="Placa" name="Placa<?php echo $cont_vehiculo; ?>" alt="<?php echo $cont_vehiculo; ?>" class="input autocomplete-ajax_vehiculo" value="<?php if (!empty($datos_placa_edit) && $cont_vehiculo == 1) {
																																																																					echo $datos_placa_edit;
																																																																				} ?>" />
															<input type="hidden" name="IDVehiculo<?php echo $cont_vehiculo; ?>" value="<?php echo $frm["IDVehiculo"]; ?>" id="IDVehiculo<?php echo $cont_vehiculo; ?>" alt="<?php echo $cont_vehiculo; ?>" title="Vehiculo">
														</div>
													</div>

													<!--
                                                                         <div  class="col-xs-12 col-sm-4">
                                                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> SOAT </label>
                                        
                                                                                <div class="col-sm-8">
                                                                                  <input type="text" id="Soat<?php echo $cont_vehiculo; ?>" name="Soat<?php echo $cont_vehiculo; ?>" class="col-xs-12 calendar" title="Soat" value="<?php echo $frm["Soat"] ?>" >
                                                                                </div>
                                                                        </div>
                                                                        
                                                                         <div  class="col-xs-12 col-sm-4">
                                                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tecnomec </label>
                                        
                                                                                 <div class="col-sm-8">
                                                                                  <input type="text" id="Tecnomecanica<?php echo $cont_vehiculo; ?>" name="Tecnomecanica<?php echo $cont_vehiculo; ?>" class="col-xs-12 calendar" title="Tecnomecanica" value="<?php echo $frm["Tecnomecanica"] ?>" >
                                                                                </div>
                                                                        </div>
                                                                        -->
												</div>
											<?php endfor; ?>



											</p>
										</div>
									</div>
								</div>
							</div><!-- /.col -->

						<?php

						endfor;
						?>


						<?php
						if ($newmode == "updateobservacion") : ?>
							<div class="form-group first">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones</label>

									<div class="col-sm-8">
										<textarea id="Observaciones" rows="4" title="Observaciones" name="Observaciones" class="form-control" /><?php echo $frm["Observaciones"] ?></textarea>
									</div>
								</div>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha/Hora Ingreso</label>

									<div class="col-sm-8">
										<input type="text" id="FechaInicioClub" name="FechaInicioClub" placeholder="Fecha Ingreso Club" class="col-xs-12" title="Fecha Ingreso Club" value="<?php if ($newmode == "updateingreso") : echo date("Y-m-d H:i:s");
																																															else : echo "";
																																															endif; ?>" readonly>
									</div>
								</div>

							</div>
						<?php endif; ?>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<input type="hidden" name="NumeroInvitados" id="NumeroInvitados" value="<?php echo $cont_invitado;  ?>" />

								<button class="btn btn-info btnEnviar" type="button" rel="frm">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
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

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>