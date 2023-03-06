<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR NUEVA <?php echo strtoupper(SIMReg::get("title")) ?>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Habitacion </label>

								<div class="col-sm-8">
									<select name="IDTipoHabitacion" id="IDTipoHabitacion" class="form-control">
										<option value=""></option>
										<?php
										$sql_tipohab_club = "Select * From TipoHabitacion  Where  IDClub = '" . SIMUser::get("club") . "'";
										$qry_tipohab_club = $dbo->query($sql_tipohab_club);
										while ($r_tipohab = $dbo->fetchArray($qry_tipohab_club)) : ?>
											<option value="<?php echo $r_tipohab["IDTipoHabitacion"]; ?>" <?php if ($r_tipohab["IDTipoHabitacion"] == $frm["IDTipoHabitacion"]) echo "selected";  ?>><?php echo $r_tipohab["Nombre"]; ?></option>
										<?php endwhile;  ?>
									</select>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Torre </label>

								<div class="col-sm-8">
									<select name="IDTorre" id="IDTorre" class="form-control">
										<option value=""></option>
										<?php
										$sql_torre_club = "Select * From Torre  Where  IDClub = '" . SIMUser::get("club") . "'";
										$qry_torre_club = $dbo->query($sql_torre_club);
										while ($r_torre = $dbo->fetchArray($qry_torre_club)) : ?>
											<option value="<?php echo $r_torre["IDTorre"]; ?>" <?php if ($r_torre["IDTorre"] == $frm["IDTorre"]) echo "selected";  ?>><?php echo $r_torre["Nombre"]; ?></option>
										<?php endwhile;  ?>
									</select>
								</div>
							</div>

						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Habitacion </label>

								<div class="col-sm-8">
									<input type="text" id="NumeroHabitacion" name="NumeroHabitacion" placeholder="Numero Habitacion" class="col-xs-12 mandatory" title="Numero Habitacion" value="<?php echo $frm["NumeroHabitacion"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first">


							<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>

							<div class="col-sm-12">
								<?php
								$oCuerpo = new FCKeditor("Descripcion");
								$oCuerpo->BasePath = "js/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["Descripcion"];
								$oCuerpo->Create();
								?>
							</div>


						</div>

						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
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