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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Codigo" name="Codigo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?>" value="<?php echo $frm["Codigo"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horaentrada', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="time" id="Entrada" name="Entrada" placeholder="<?= SIMUtil::get_traduccion('', '', 'Horaentrada', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Horaentrada', LANGSESSION); ?>" value="<?php echo $frm["Entrada"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horasalida', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="time" id="Salida" name="Salida" placeholder="<?= SIMUtil::get_traduccion('', '', 'Horasalida', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Horasalida', LANGSESSION); ?>" value="<?php echo $frm["Salida"]; ?>"></div>
							</div>
						</div>		
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Iniciodealmuerzo', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="time" id="AlmuerzoInicio" name="AlmuerzoInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'Iniciodealmuerzo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Iniciodealmuerzo', LANGSESSION); ?>" value="<?php echo $frm["AlmuerzoInicio"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Findealmuerzo', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="time" id="AlmuerzoFin" name="AlmuerzoFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'Findealmuerzo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Findealmuerzo', LANGSESSION); ?>" value="<?php echo $frm["AlmuerzoFin"]; ?>"></div>
							</div>
						</div>						
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<textarea id="Observaciones" name="Observaciones" placeholder="<?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?>" class="col-xs-12" ><?php echo $frm["Observaciones"]; ?></textarea>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>
						</div>	
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								 <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
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