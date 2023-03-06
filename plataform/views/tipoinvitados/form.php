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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Aplica Tipo Socio </label>
								<div class="col-sm-8">
									<select name="TipoSocio[]" id="TipoSocio" title="Tipo Socio" class="form-control chosen-select mandatory" multiple data-placeholder="[Seleccione Tipo Socio]">
										<?php
										$sql_TipoSocio = "SELECT ts.IDTipoSocio,ts.Nombre FROM TipoSocio as ts, ClubTipoSocio as cts WHERE ts.IDTipoSocio=cts.IDTipoSocio and ts.Publicar = 'S' and cts.IDClub = '" . SIMUser::get('club') . "'";
										// $sql_TipoSocio = "SELECT ts.IDTipoSocio,ts.Nombre FROM TipoSocio as ts WHERE ts.Publicar = 'S' AND IDClub = '" . SIMUser::get('club') . "'";
										$q_TipoSocio = $dbo->query($sql_TipoSocio);
										$r_TipoSocio = explode('|', $frm['TipoSocio']);
										while ($tiposocio = $dbo->object($q_TipoSocio)) {
											if ($frm['TipoSocio'] == '') {
												$selected = "";
											} elseif (in_array($tiposocio->Nombre, $r_TipoSocio)) {
												$selected = "selected";
											} else {
												$selected = "";
											}
										?>
											<option value="<?php echo $tiposocio->Nombre ?>" <?php echo $selected; ?>><?php echo $tiposocio->Nombre ?></option>
										<?php
										}
										?>
									</select>
									<br>
									<button type="button" class="btn btn-info btn-sm chosen-selected" rel="TipoSocio">Seleccionar Todos</button>
									<button type="button" class="btn btn-danger btn-sm chosen-deselect" rel="TipoSocio">Borrar Todos</button>
								</div>
							</div>
						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Max. invitados al mes?</label>
								<div class="col-sm-8">
									<input type="number" id="MaxInvitadosMes" name="MaxInvitadosMes" placeholder="" class="col-xs-12 mandatory" title="Max. invitados al mes?" value="<?php echo $frm["MaxInvitadosMes"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitaciones al mes por invitado? </label>
								<div class="col-sm-8">
									<input type="number" id="InvitacionesMesPorInvitado" name="InvitacionesMesPorInvitado" placeholder="" class="col-xs-12 mandatory" title="Invitaciones al mes por invitado?" value="<?php echo $frm["InvitacionesMesPorInvitado"]; ?>">
								</div>
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