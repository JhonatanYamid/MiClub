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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formPopup('Tributo', 'Nombre', 'Nombre', 'IDTributo', $frm["IDTributo"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', 'onchange="selTrubuto()"', ''); ?>
									<input type="hidden" name="TipoImpuesto" id="TipoImpuesto" value="" />
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1" id='lblValor'> <?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>(%) </label>
								<div class="col-sm-8">
									<input type="number" id="ValorImpuesto" name="ValorImpuesto" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>(%)" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>(%)" value="<?php echo $frm["ValorImpuesto"]; ?>">
								</div>
							</div>
						</div>				
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="CuentaContable" name="CuentaContable" placeholder="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" value="<?php echo $frm["CuentaContable"]; ?>"></div>
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
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $idPadre?>" />
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
<script>
	selTrubuto();

	function selTrubuto(){
		let tributo = $('#IDTributo option:selected').html();
		$('#TipoImpuesto').val(tributo);
	}
</script>