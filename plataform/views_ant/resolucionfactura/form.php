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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Numero" name="Numero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" value="<?php echo $frm["Numero"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type='text' id='Fecha' name='Fecha' value="<?php echo $frm["Fecha"]; ?>" class='calendar' placeholder='<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION);?>'   $title="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION);?>">
								</div>
							</div>
						</div>			
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Tipo" name="Tipo" placeholder="<?= SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION); ?>" value="<?php echo $frm["Tipo"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Activo"], 'Activo', "class='input'") ?>
								</div>
							</div>
						</div>	
						<div class="form-group first ">
							<?
								$valInicial = $frm["ValorInicial"] == '' ? 0 : $frm["ValorInicial"];
								$valFinal = $frm["ValorFin"] == '' ? 0 : $frm["ValorFin"];
							?>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'valorinicial', LANGSESSION); ?>(<?= SIMUtil::get_traduccion('', '', 'consecutivo', LANGSESSION); ?>): </label>
								<div class="col-sm-8">
									<input type="number" id="ValorInicial" name="ValorInicial" onkeyup="cambiarRango()" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'valorinicial', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'valorinicial', LANGSESSION); ?>" value="<?= $valInicial; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'valorfinal', LANGSESSION); ?>(<?= SIMUtil::get_traduccion('', '', 'consecutivo', LANGSESSION); ?>): </label>
								<div class="col-sm-8">
									<input type="number" id="ValorFin" name="ValorFin" onkeyup="cambiarRango()" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'valorfinal', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'valorfinal', LANGSESSION); ?>" value="<?= $valFinal; ?>">
								</div>
							</div>
						</div>				
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'serialprefijo', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Prefijo" name="Prefijo" onkeyup="cambiarRango()" placeholder="<?= SIMUtil::get_traduccion('', '', 'serialprefijo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'serialprefijo', LANGSESSION); ?>" value="<?php echo $frm["Prefijo"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'rango', LANGSESSION); ?>: </label>
								<label class="col-sm-8" id="rango"> </label>
							</div>
						</div>						
						<div class="form-group first ">
							<?
								$valCnsctvoFac = $frm["ConsecutivoFacturas"] == '' ? 0 : $frm["ConsecutivoFacturas"];
								$valCnsctvoRes = $frm["ConsecutivoRecibos"] == '' ? 0 : $frm["ConsecutivoRecibos"];
							?>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'consecutivofacturas', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="number" id="ConsecutivoFacturas" name="ConsecutivoFacturas" <?php if (SIMUser::get("Nivel") != 0) echo "disabled";?> onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'consecutivofacturas', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'consecutivofacturas', LANGSESSION); ?>" value="<?= $valCnsctvoFac; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'consecutivorecibos', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="number" id="ConsecutivoRecibos" name="ConsecutivoRecibos" <?php if (SIMUser::get("Nivel") != 0) echo "disabled";?> onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'consecutivorecibos', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'consecutivorecibos', LANGSESSION); ?>" value="<?= $valCnsctvoRes; ?>">
								</div>
							</div>
						</div>			
						<div class="form-group first ">
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

<script>

	cambiarRango();	

	function cambiarRango(){
		var valInicial = $("#ValorInicial").val();
		var valFin = $("#ValorFin").val();
		var prefijo = $("#Prefijo").val();

		var text = "Aut de " + prefijo + "-" + valInicial + " a " + prefijo + "-" + valFin;
		$("#rango").text(text);
	}

</script>