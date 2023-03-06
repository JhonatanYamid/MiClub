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
						<? $arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
							if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
												$arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
												foreach($arrOpciones as $key => $val){              
													if($frm["IDClub"] == $val){
														echo '<option value="' .$val. '" selected="selected">' .$key. '</option>';
													}else{
														echo '<option value="' .$val. '">' .$key. '</option>';
													}
												}
											?>
										</select>
									</div>
								</div>
							</div>
						<? }else{ ?>
							<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
						<? } ?>
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
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
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