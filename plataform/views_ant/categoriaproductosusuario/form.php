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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm" name="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Restaurante', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio">
										<option value="">Todos</option>
										<?php
										$sql_rest_club = string;
										$sql_rest_club = "SELECT IDRestauranteDomicilio,Nombre FROM RestauranteDomicilio WHERE IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S' order by Nombre";
										$qry_rest_club = $dbo->query($sql_rest_club);
										while ($r_rest = $dbo->fetchArray($qry_rest_club)) : ?>
											<option value="<?php echo $r_rest["IDRestauranteDomicilio"]; ?>" <?php if ($r_rest["IDRestauranteDomicilio"] == $frm["IDRestauranteDomicilio"]) echo "selected";  ?>><?php echo $r_rest["Nombre"]; ?></option>
										<?php endwhile; ?>
									</select>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>





						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentariosCategoria', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="ComentarioCategoria" name="ComentarioCategoria" placeholder="<?= SIMUtil::get_traduccion('', '', 'ComentariosCategoria', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentariosCategoria', LANGSESSION); ?>" value="<?php echo $frm["ComentarioCategoria"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $frm["Orden"]; ?>">
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
							</div>

						</div>





						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm">
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