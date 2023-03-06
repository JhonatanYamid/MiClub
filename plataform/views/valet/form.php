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

									<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"] . $label_accion) ?>">
									<?= SIMUtil::get_traduccion('', '', 'Busquedapor:Accion,Nombre,Apellido,NumeroDocumento', LANGSESSION); ?>
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">

								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Placa', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Placa" name="Placa" placeholder="<?= SIMUtil::get_traduccion('', '', 'Placa', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Placa', LANGSESSION); ?>" value="<?php echo $frm["Placa"]; ?>">
								</div>
							</div>

						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'RecibidoPor', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php
									if ($_GET["action"] == "add") :
										$frm["IDUsuarioRecibe"] = SIMUser::get("IDUsuario");
									endif;


									$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuarioRecibe"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club); ?>

									<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" value="<?php echo utf8_encode($r_socio["Nombre"]) ?>">
									<input type="hidden" name="IDUsuarioRecibe" value="<?php echo $frm["IDUsuarioRecibe"]; ?>" id="IDUsuarioRecibe" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaRecibido', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaRecibe" name="FechaRecibe" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaRecibe', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaRecibe', LANGSESSION); ?>" value="<?php if (!empty($frm["FechaRecibe"])) echo $frm["FechaRecibe"];
																																																																							else echo date("Y-m-d"); ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EntregadoPor', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php
									if ($_GET["action"] == "add") :
										$frm["IDUsuarioEntrega"] = SIMUser::get("IDUsuario");
									endif;


									$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuarioEntrega"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club); ?>

									<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" value="<?php echo utf8_encode($r_socio["Nombre"]) ?>">
									<input type="hidden" name="IDUsuarioEntrega" value="<?php echo $frm["IDUsuarioEntrega"]; ?>" id="IDUsuarioEntrega" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaEntregado', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaEntrega" name="FechaEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" value="<?php if (!empty($frm["FechaEntrega"])) echo $frm["FechaEntrega"];
																																																																								else echo date("Y-m-d"); ?>">
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CedulaTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CedulaTercero" name="CedulaTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'CedulaTercero', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'CedulaTercero', LANGSESSION); ?>" value="<?php echo $frm["CedulaTercero"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NombreTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="NombreTercero" name="NombreTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'NombreTercero', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'NombreTercero', LANGSESSION); ?>" value="<?php echo $frm["NombreTercero"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumerodeParqueadero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="NumeroParqueadero" name="NumeroParqueadero" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumerodeParqueadero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumerodeParqueadero', LANGSESSION); ?>" value="<?php echo $frm["NumeroParqueadero"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<select name="Estado" id="Estado" class="form-control mandatory">
										<option value=""><?= SIMUtil::get_traduccion('', '', 'Seleccioneopcion', LANGSESSION); ?></option>
										<option value="Recibido" <?php if ($frm["Estado"] == "Recibido") echo "selected"; ?>>Recibido</option>
										<option value="Solicitado" <?php if ($frm["Estado"] == "Solicitado") echo "selected"; ?>>Solicitado</option>
										<option value="Entregado" <?php if ($frm["Estado"] == "Entregado") echo "selected"; ?>>Entregado</option>
										<option value="Cancelado" <?php if ($frm["Estado"] == "Cancelado") echo "selected"; ?>>Cancelado</option>
									</select>

								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Pago </label>

								<div class="col-sm-8">
									<select name="IDTiposDePagoValetParking" id="IDTiposDePagoValetParking">
										<option value=""></option>
										<?php
										$sql_tipopago = "Select IDTiposDePagoValetParking,Nombre From TiposDePagoValetParking Where IDClub = '" . SIMUser::get("club") . "'";
										$qry_tipopago = $dbo->query($sql_tipopago);
										while ($r_tipopago = $dbo->fetchArray($qry_tipopago)) : ?>
											<option value="<?php echo $r_tipopago["IDTiposDePagoValetParking"]; ?>" <?php if ($r_tipopago["IDTiposDePagoValetParking"] == $frm["IDTiposDePagoValetParking"]) echo "selected";  ?>><?php echo $r_tipopago["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero De Pago </label>

								<div class="col-sm-8">
									<input type="text" id="NumeroPago" name="NumeroPago" placeholder="" class="col-xs-12 " title="Numero Pago" value="<?php echo $frm["NumeroPago"]; ?>">
								</div>
							</div>


						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Vehiculo </label>

								<div class="col-sm-8">
									<select name="IDVehiculoValetParking" id="IDVehiculoValetParking">
										<option value=""></option>
										<?php
										$sql_vehiculo = "Select IDVehiculoValetParking,Nombre From VehiculoValetParking Where IDClub = '" . SIMUser::get("club") . "'";
										$qry_vehiculo = $dbo->query($sql_vehiculo);
										while ($r_vehiculo = $dbo->fetchArray($qry_vehiculo)) : ?>
											<option value="<?php echo $r_vehiculo["IDVehiculoValetParking"]; ?>" <?php if ($r_vehiculo["IDVehiculoValetParking"] == $frm["IDVehiculoValetParking"]) echo "selected";  ?>><?php echo $r_vehiculo["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
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

					</form>
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>