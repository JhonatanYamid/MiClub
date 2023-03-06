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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipopara', LANGSESSION); ?>: </label>

								<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

									<?php
									if (SIMUser::get("club") == "89" || SIMUser::get("club") == "90") { ?>
										<select name="TipoSocio" id="TipoSocio" class="form-control">
											<option value="">Seleccione Tipo</option>
											<?php $sql_tipo = "SELECT TS.IDTipoSocio,Nombre
																					 FROM ClubTipoSocio CTS,TipoSocio TS
																					 WHERE CTS.IDTipoSocio=TS.IDTipoSocio and CTS.IDClub = '" . SIMUser::get("club") . "' ";
											$r_tipo	= $dbo->query($sql_tipo);
											while ($row_tipo = $dbo->fetchArray($r_tipo)) { ?>
												<option value="<?php echo $row_tipo["Nombre"];  ?>" <?php if ($row_tipo["Nombre"] == $frm["TipoSocio"]) echo "selected"; ?>><?php echo $row_tipo["Nombre"];  ?></option>
											<?php } ?>
											?>
										</select>

									<?php } ?>

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


								</div>
							</div>



						</div>






						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm[Icono])) {
										echo "<img src='" . CLIENTE_ROOT . "$frm[Icono]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>

									<input name="Icono" id=Icono class="" title="Icono" type="file" size="25" style="font-size: 10px">



								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoCarpeta(comosemostraranlosarchivosdeestetipo)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="Tipo" id="Tipo" class="form-control">
										<option value="Lista" <?php if ($frm["Tipo"] == "Lista") echo "selected"; ?>>Mostrar archivos en lista</option>
										<option value="Icono" <?php if ($frm["Tipo"] == "Icono") echo "selected"; ?>>Mostrar archivos con icono y tipo tabla</option>
										<option value="DescargaDirecta" <?php if ($frm["Tipo"] == "DescargaDirecta") echo "selected"; ?>>Descargar al pulsar</option>
									</select>
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SolomostrarIcono', LANGSESSION); ?>? </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloIcono"], 'SoloIcono', "class='input mandatory'") ?>
								</div>

							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostraren', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<select name="Mostrar" id="Mostrar" class="form-control">
										<option value="ModuloPrincipalySubmodulo" <?php if ($frm["Mostrar"] == "ModuloPrincipal") echo "selected"; ?>>Modulo Principal y Submodulos</option>
										<option value="Submodulo" <?php if ($frm["Mostrar"] == "Submodulo") echo "selected"; ?>>Solo en Submodulo</option>

									</select>
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input" value="<?php echo $frm["Orden"] ?>" />
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
								</div>
							</div>



						</div>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo $_GET["IDModulo"] ?>" />
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