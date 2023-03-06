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
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pais', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Pais", "Nombre", "Nombre", "IDPais", $frm["IDPais"], "[Seleccione Pais]", "form-control", "title = \"Pais\"") ?>
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Ciudad', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php
									$sql_ciudad = "Select IDCiudad, Nombre From Ciudad Where IDCiudad = '" . $frm["IDCiudad"] . "' AND Publicar='S'";
									$qry_ciudad = $dbo->query($sql_ciudad);
									$r_ciudad = $dbo->fetchArray($qry_ciudad);

									?>
									<select name="IDCiudad" id="IDCiudad" class="form-control mandatory" title="IDCiudad">

										<option value="<?php echo $r_ciudad["IDCiudad"] ?>"><?php echo $r_ciudad["Nombre"] ?></option>
									</select>
								</div>
							</div>

							<!-- 	<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciudad </label>

								<div class="col-sm-8">
									
									<select name="IDCiudad" id="IDCiudad" class="form-control mandatory" title="IDCiudad">
									<option value="">Seleccione</option>
									<?php
									$sql_ciudad = "Select IDCiudad, Nombre From Ciudad Where IDPais = '1' AND Publicar='S' Order by Nombre";
									$qry_ciudad = $dbo->query($sql_ciudad);
									while ($r_ciudad = $dbo->fetchArray($qry_ciudad)) { ?>
											<option value="<?php echo $r_ciudad["IDCiudad"] ?>"  <?php if ($r_ciudad["IDCiudad"] == $frm["IDCiudad"]) {
																										echo "selected";
																									} ?>  > <?php echo utf8_decode($r_ciudad["Nombre"]) ?></option>
									<?php } ?>

									?>
										
									</select>
								</div>
							</div> -->
						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ClubAsociadoa109Apps', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select name="IDClubApp" id="IDClubApp">
										<option value=""></option>
										<?php
										$sql_club = string;
										$sql_club = "Select * From Club Where 1 order by Nombre";
										$qry_club = $dbo->query($sql_club);
										while ($r_club = $dbo->fetchArray($qry_club)) : ?>
											<option value="<?php echo $r_club["IDClub"]; ?>" <?php if ($r_club["IDClub"] == $frm["IDClubApp"]) echo "selected";  ?>><?php echo $r_club["Nombre"]; ?></option>
										<?php
										endwhile;
										?>
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