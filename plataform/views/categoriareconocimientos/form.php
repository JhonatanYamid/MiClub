<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

								<div class="col-sm-8">
									<select name="Tipo" id="Tipo" class="form-control" required>
										<option value=""></option>
										<option value="Huella" <?php if ($frm["Tipo"] == "Huella") echo "selected"; ?>>Reconocimiento</option>
										<option value="Cultura" <?php if ($frm["Tipo"] == "Cultura") echo "selected"; ?>>Cultura de seguridad y salud en el trabajo</option>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6" id="areasaplica">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Si es Cultura seleccione las áreas a las que aplica: </label>

								<div class="col-sm-8">
									<?php
									$array_areas = explode("|", $frm["Areas"]);
									foreach (SIMResources::$areassoycentral as $key_area => $value) {	?>
										<input type="checkbox" name="Area<?php echo $key_area; ?>" value="<?php echo $key_area; ?>" <?php if (in_array($key_area, $array_areas)) echo "checked"; ?>><?php echo $value; ?><br>
									<?php }
									?>

								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>


						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Pequeña </label>
								<input name="ImagenCategoria" id=file class="" title="ImagenCategoria" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["ImagenCategoria"])) {
										echo "<img src='" . CLUB_ROOT . $frm["ImagenCategoria"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenCategoria]&campo=ImagenCategoria&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen banner </label>
								<input name="BannerCategoria" id=file class="" title="BannerCategoria" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["BannerCategoria"])) {
										echo "<img src='" . CLUB_ROOT . $frm["BannerCategoria"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[BannerCategoria]&campo=BannerCategoria&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Banner interno </label>
								<input name="BannerInterna" id=file class="" title="BannerInterna" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["BannerInterna"])) {
										echo "<img src='" . CLUB_ROOT . $frm["BannerInterna"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[BannerInterna]&campo=BannerInterna&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color Letra </label>

								<div class="col-sm-8">
									<input name="ColorLetra" type="color" value="<?php if (empty($frm["ColorLetra"])) {
																						echo "#FFFFFF";
																					} else {
																						echo $frm["ColorLetra"];
																					}    ?>" />
								</div>
							</div>
						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden </label>
								<div class="col-sm-8">
									<input type="text" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite crear reconocimientos </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReconocimiento"], 'PermiteReconocimiento', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info-circle green"></i>
								Opciones Reconocimiento
							</h3>
						</div>

						<?php
						//guardar las opciones
						$sql_opcion = "SELECT Opcion,Texto FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento = '" . $frm[$key] . "'";
						$r_opcion = $dbo->query($sql_opcion);
						while ($row_opcion = $dbo->fetchArray($r_opcion)) {
							$array_opcion[$row_opcion["Opcion"]] = $row_opcion["Texto"];
						}

						$TotalOpciones = 10;
						for ($i = 1; $i <= $TotalOpciones; $i += 2) {
							$contador_siguiente = $i + 1;
						?>
							<div class="form-group first ">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Opcion <?php echo $i; ?> </label>
									<div class="col-sm-8">
										<input type="text" id="Opcion<?php echo $i; ?>" name="Opcion<?php echo $i; ?>" placeholder="Opcion<?php echo $i; ?>" class="col-xs-12" title="Opcion<?php echo $i; ?>" value="<?php echo $array_opcion[$i]; ?>">
									</div>
								</div>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Opcion <?php echo $contador_siguiente; ?> </label>
									<div class="col-sm-8">
										<input type="text" id="Opcion<?php echo $contador_siguiente; ?>" name="Opcion<?php echo $contador_siguiente; ?>" placeholder="Opcion<?php echo $contador_siguiente; ?>" class="col-xs-12" title="Opcion<?php echo $contador_siguiente; ?>" value="<?php echo $array_opcion[$contador_siguiente]; ?>">
									</div>
								</div>
							</div>
						<?php } ?>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<input type="hidden" name="NumeroOpciones" id="NumeroOpciones" value="<?php echo $TotalOpciones;  ?>" />
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