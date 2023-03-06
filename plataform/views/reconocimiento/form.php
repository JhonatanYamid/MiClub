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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario a reconocer </label>

								<div class="col-sm-8">
									<div class="col-sm-8">
										<?php
										$sql_socio_club = "SELECT IDSocio,NumeroDocumento,Nombre,Apellido FROM Socio Where IDSocio = '" . $frm["IDSocioVotado"] . "'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>

										<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
										<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocioVotado"]; ?>" id="IDSocio" class="mandatory" title="Socio">
									</div>
								</div>
							</div>





						</div>


						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CategoriaReconocimiento", "Nombre", "Nombre", "IDCategoriaReconocimiento", $frm["IDCategoriaReconocimiento"], "[Seleccione categoria]", "form-control", "title = \"Categoria\"") ?>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario que reconoció </label>

								<div class="col-sm-8">
									<div class="col-sm-8">
										<?php
										$sql_socio_club = "SELECT IDSocio,NumeroDocumento,Nombre,Apellido From Socio Where IDSocio = '" . $frm["IDSocioVotante"] . "'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>
										<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"]; ?>
										<input type="hidden" id="IDSocioVotante" name="IDSocioVotante" value="<?php echo $frm["IDSocioVotante"]; ?>">

									</div>
								</div>
							</div>
						</div>


						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentario </label>

								<div class="col-sm-8">
									<textarea id="Comentario" name="Comentario" cols="10" rows="5" class="col-xs-12" title="Comentario" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["Comentario"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificacion al usuario ? </label>

								<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
							</div>

						</div>


						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info-circle green"></i>
								Reconocimientos:
							</h3>
						</div>

						<?php

						$sql_cat = "SELECT IDCategoriaReconocimiento,Nombre FROM CategoriaReconocimiento WHERE 1 AND IDClub='" . SIMUser::get("club") . "'";
						$r_cat = $dbo->query($sql_cat);
						while ($row_cat = $dbo->fetchArray($r_cat)) {
							$array_cat[$row_cat["IDCategoriaReconocimiento"]] = $row_cat["Nombre"];
						}

						//guardar las opciones
						$sql_opcion = "SELECT IDOpcionReconocimiento FROM ReconocimientoOpcion WHERE IDReconocimiento = '" . $frm[$key] . "'";
						$r_opcion = $dbo->query($sql_opcion);
						while ($row_opcion = $dbo->fetchArray($r_opcion)) {
							$array_opcion[] = $row_opcion["IDOpcionReconocimiento"];
						}

						$contador_siguiente = $i + 1;
						?>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<?php
							//$sql_opcionesrec="SELECT IDOpcionReconocimiento,Texto FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento='".$frm["IDCategoriaReconocimiento"]."' ";
							//$sql_opcionesrec = "SELECT IDOpcionReconocimiento,Texto,IDCategoriaReconocimiento FROM OpcionReconocimiento WHERE 1 ORDER BY IDCategoriaReconocimiento";
							$sql_opcionesrec = "SELECT IDOpcionReconocimiento,Texto,IDCategoriaReconocimiento FROM OpcionReconocimiento WHERE 1 AND IDClub='" . SIMUser::get("club") . "' ORDER BY IDCategoriaReconocimiento";
							$r_opcionesrec = $dbo->query($sql_opcionesrec);
							$NuevaCategoria = "";
							while ($row_opcionesrec = $dbo->fetchArray($r_opcionesrec)) {
								if ($NuevaCategoria != $row_opcionesrec["IDCategoriaReconocimiento"]) {
									$NuevaCategoria = $row_opcionesrec["IDCategoriaReconocimiento"];
							?>
									<tr>
										<td colspan="2"><b>CATEGORIA: <?php echo $array_cat[$row_opcionesrec["IDCategoriaReconocimiento"]]; ?></b></td>

									</tr>
								<?php
								}


								if (!empty($row_opcionesrec["Texto"])) { ?>
									<tr>
										<td><input type="checkbox" name="Opcion<?php echo $row_opcionesrec["IDOpcionReconocimiento"]; ?>" <?php if (in_array($row_opcionesrec["IDOpcionReconocimiento"], $array_opcion)) echo "checked"; ?>> </td>
										<td><?php echo $row_opcionesrec["Texto"]; ?></td>
									</tr>
							<?php
								}
							} ?>

						</table>


						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
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