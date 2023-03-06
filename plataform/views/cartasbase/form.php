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

									<input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"] . $label_accion) ?>">
									<?= SIMUtil::get_traduccion('', '', 'Busquedapor:Accion,Nombre,Apellido,NumeroDocumento', LANGSESSION); ?>
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">

								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Numero" name="Numero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" class="col-xs-12 Numero" title="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" value="<?php echo $frm["Numero"]; ?>"></div>
							</div>
						</div>



						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombres', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Nombres" name="Nombres" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombres', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Nombres', LANGSESSION); ?>" value="<?php echo $frm["Numero"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Carta" name="Carta" placeholder="<?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?>" value="<?php echo $frm["Carta"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PorVencer', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="PorVencer" name="PorVencer" placeholder="<?= SIMUtil::get_traduccion('', '', 'PorVencer', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'PorVencer', LANGSESSION); ?>" value="<?php echo $frm["PorVencer"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>30 </label>
								<div class="col-sm-8"><input type="text" id="Dia30" name="Dia30" placeholder="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>30" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>30" value="<?php echo $frm["Dia30"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>60 </label>
								<div class="col-sm-8"><input type="text" id="Dia60" name="Dia60" placeholder="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>60" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>60" value="<?php echo $frm["Dia60"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>90 </label>
								<div class="col-sm-8"><input type="text" id="Dia90" name="Dia90" placeholder="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>90" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>90" value="<?php echo $frm["Dia90"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>120 </label>
								<div class="col-sm-8"><input type="text" id="Dia120" name="Dia120" placeholder="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>120" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>120" value="<?php echo $frm["Dia120"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'mas', LANGSESSION); ?>120 </label>
								<div class="col-sm-8"><input type="text" id="Mas120" name="Mas120" placeholder="<?= SIMUtil::get_traduccion('', '', 'mas', LANGSESSION); ?>120" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'mas', LANGSESSION); ?>120" value="<?php echo $frm["Mas120"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'SaldoVencido', LANGSESSION); ?>60 </label>
								<div class="col-sm-8"><input type="text" id="SaldoVencido60" name="SaldoVencido60" placeholder="<?= SIMUtil::get_traduccion('', '', 'SaldoVencido', LANGSESSION); ?>60" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'SaldoVencido', LANGSESSION); ?>60" value="<?php echo $frm["SaldoVencido60"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'GeneralValor ', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="GeneralValor" name="GeneralValor" placeholder="<?= SIMUtil::get_traduccion('', '', 'GeneralValor ', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'GeneralValor ', LANGSESSION); ?>" value="<?php echo $frm["GeneralValor"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaAbono ', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="FechaAbono" name="FechaAbono" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaAbono ', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'FechaAbono ', LANGSESSION); ?>" value="<?php echo $frm["FechaAbono"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'AbonoActual ', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="AbonoActual" name="AbonoActual" placeholder="<?= SIMUtil::get_traduccion('', '', 'AbonoActual ', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'AbonoActual ', LANGSESSION); ?>" value="<?php echo $frm["AbonoActual"]; ?>"></div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NuevoSaldo ', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="NuevoSaldo" name="NuevoSaldo" placeholder="<?= SIMUtil::get_traduccion('', '', 'NuevoSaldo ', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'NuevoSaldo ', LANGSESSION); ?>" value="<?php echo $frm["NuevoSaldo"]; ?>"></div>
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
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
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