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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDCategoriaDirectorioSocio" id="IDCategoriaDirectorioSocio" class="form-control">
										<option value=""></option>
										<?php
										$sql_catdir_club = "Select * From CategoriaDirectorioSocio  Where  IDClub = '" . SIMUser::get("club") . "'";
										$qry_catdir_club = $dbo->query($sql_catdir_club);
										while ($r_catdir = $dbo->fetchArray($qry_catdir_club)) : ?>
											<option value="<?php echo $r_catdir["IDCategoriaDirectorioSocio"]; ?>" <?php if ($r_catdir["IDCategoriaDirectorioSocio"] == $frm["IDCategoriaDirectorioSocio"]) echo "selected";  ?>><?php echo $r_catdir["Nombre"]; ?></option>
										<?php endwhile;  ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Telefono" name="Telefono" placeholder="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>">
									<br><?= SIMUtil::get_traduccion('', '', 'Sitieneextension,separadoporcoma(,)ej:7888888,123', LANGSESSION); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteCalificar', LANGSESSION); ?>? </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCalificar"], 'PermiteCalificar', "class='input mandatory'") ?></div>
							</div>


						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Whatsapp" name="Whatsapp" placeholder="<?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Whatsapp', LANGSESSION); ?>" value="<?php echo $frm["Whatsapp"]; ?>">

								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoWhatsapp', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelWhatsapp" name="LabelWhatsapp" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoWhatsapp', LANGSESSION); ?> " class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'TextoWhatsapp', LANGSESSION); ?> " value="<?php echo $frm["LabelWhatsapp"]; ?>">

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoWhatsapp', LANGSESSION); ?> </label>
								<input name="IconoWhatsapp" id=file class="" title="IconoWhatsapp" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm[IconoWhatsapp])) {
										echo "<img src='" . DIRECTORIO_ROOT . "$frm[IconoWhatsapp]' width='300' height='300'>";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoWhatsapp]&campo=IconoWhatsapp&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarIconodetelefonoparamarcacion', LANGSESSION); ?>? </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarIcono"], 'MostrarIcono', "class='input'") ?></div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'MostrarIconoemail', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarIconoEmail"], 'MostrarIconoEmail', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

						</div>








						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info-circle green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Otrosdatos', LANGSESSION); ?>
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 " title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Email" name="Email" placeholder="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" class="col-xs-12 " title="Email" value="<?php echo $frm["Email"]; ?>">
								</div>
							</div>

						</div>




						<div class="form-group first ">
							<?php
							$contador_campos = 1;
							$contador_columnas = 1;
							$sql_campos = "Select * From CampoDirectorioSocio Where IDClub = '" . SIMUser::get("club") . "'";
							$result_campos = $dbo->query($sql_campos);
							while ($row_campos = $dbo->fetchArray($result_campos)) : ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $row_campos["Nombre"]; ?> </label>

									<div class="col-sm-8">
										<input type="text" id="OtroCampo<?php echo $contador_campos; ?>" name="OtroCampo<?php echo $contador_campos; ?>" placeholder="<?php echo $row_campos["Nombre"]; ?>" class="col-xs-12 " title="<?php echo $row_campos["Nombre"]; ?>" value="<?php echo $dbo->getFields("CampoDirectorioSocioValor", "Valor", "IDDirectorioSocio = '" . $frm[$key] . "' and IDCampoDirectorioSocio = '" . $row_campos["IDCampoDirectorioSocio"] . "'") ?>">
										<input type="hidden" id="IdentificadorCampo<?php echo $contador_campos; ?>" name="IdentificadorCampo<?php echo $contador_campos; ?>" value="<?php echo $row_campos["IDCampoDirectorioSocio"]; ?>">
									</div>
								</div>
							<?php
								$contador_campos++;
								if ($contador_columnas == 2) :
									echo '</div><div  class="form-group first ">';
									$contador_columnas = 1;
								endif;
								$contador_columnas++;
							endwhile;
							$contador_campos--;
							?>
						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>
								<input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm[Foto1])) {
										echo "<img src='" . DIRECTORIO_ROOT . "$frm[Foto1]' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
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
								<input type="hidden" name="NumeroCampos" id="NumeroCampos" value="<?php echo $contador_campos ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>


							</div>
						</div>

					</form>



					<div class="widget-header widget-header-large">
						<h3 class="widget-title grey lighter">
							<i class="ace-icon fa  fa-comment green"></i>
							<?= SIMUtil::get_traduccion('', '', 'Calificaciones', LANGSESSION); ?>
						</h3>
					</div>

					<div class="form-group first ">

						<div class="col-xs-12 col-sm-12">



							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<tr>
									<th><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></th>
									<th><?= SIMUtil::get_traduccion('', '', 'Calificacion', LANGSESSION); ?></th>
									<th><?= SIMUtil::get_traduccion('', '', 'Comentario', LANGSESSION); ?></th>
									<th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
								</tr>
								<tbody id="listacontactosanunciante">
									<?php

									$r_calificacion = &$dbo->all("DirectorioSocioCalificacion", "IDDirectorioSocio = '" . $frm[$key] . "'");

									while ($r = $dbo->object($r_calificacion)) {
										$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r->IDSocio . "' ", "array");
									?>

										<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
											<td aling="center">
												<?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>
											</td>
											<td><?php echo $r->Calificacion; ?></td>
											<td><?php echo $r->ComentarioCalificacion; ?></td>
											<td><input type="radio" name="Publicar<?php echo $r->IDDirectorioSocioCalificacion ?>" value="S" <?php if ($r->Publicar == "S") echo "checked"; ?> alt="<?php echo $r->IDDirectorioSocioCalificacion ?>" lang="DirectorioSocioCalificacion" class="PublicarCalificacionDirectorio">Si
												<input type="radio" name="Publicar<?php echo $r->IDDirectorioSocioCalificacion ?>" value="N" <?php if ($r->Publicar == "N") echo "checked"; ?> alt="<?php echo $r->IDDirectorioSocioCalificacion ?>" lang="DirectorioSocioCalificacion" class="PublicarCalificacionDirectorio">No
											</td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>

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