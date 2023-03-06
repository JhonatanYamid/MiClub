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
							<div class="col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>: </label>
								<div class="col-sm-8 limpiar">
									<input type="text" id="Buscar" name="Buscar" placeholder="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario" value="<?= $nmUsuario ?>" />
									<input type="hidden" name="IDUsuario" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>"  id="IDUsuario" value="<?php echo $frm['IDUsuario'] ?>" />
								</div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label" for="FechaInicio"><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>:</label>
								<div class="col-sm-8"><input type="datetime-local" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>:" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>" required></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label" for="FechaFin"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>:</label>
								<div class="col-sm-8"><input type="datetime-local" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>:" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>" required></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Afectaeltiempotrabajado', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Afecta"], 'Afecta', "class='input mandatory'") ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<textarea id="Observaciones" name="Observaciones" placeholder="<?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?>" class="col-xs-12" ><?php echo $frm["Observaciones"]; ?></textarea>
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
