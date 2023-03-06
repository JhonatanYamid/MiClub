
<? 
	if($frm['IDUsuario'])
		$nmUsuario = $dbo->getFields("Usuario", "Nombre", "IDUsuario = ".$frm['IDUsuario']);

	$diaLaboral = $frm['DiaLaboral'] != '' ? $frm['DiaLaboral'] : 1;
	$activo = $frm['Activo'] ? $frm['Activo'] : 'S';
?>
<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>: </label>
			<div class="col-sm-8 limpiar">
				<input type="text" id="Buscar" name="Buscar" placeholder="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario" value="<?= $nmUsuario ?>" />
				<input type="hidden" name="IDUsuario" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>"  id="IDUsuario" value="<?php echo $frm['IDUsuario'] ?>" />
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<input type="text" id="Fecha" name="Fecha" title="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?= $frm["Fecha"];?>" />
			</div>
		</div>
	</div>
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'esdialaboral', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $diaLaboral, 'DiaLaboral', "class='input mandatory'") ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6" style="display: none;" id='divTurno'>
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'turno', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formPopup('Turnos', 'Nombre', 'IDTurnos', 'IDTurnos', $frm["IDTurnos"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '', "AND Activo = 'S' AND IDClub = $IDClub"); ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6" style="display: none;" id='divDianl'>
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'dianolaboral', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formPopup('DiaNoLaboral', 'Nombre', 'IDDiaNoLaboral', 'IDDiaNoLaboral', $frm["IDDiaNoLaboral"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '', "AND Activo = 'S' AND IDClub = $IDClub"); ?>
			</div>
		</div>
	</div>	
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formradiogroup(SIMResources::$sino, $activo, 'Activo', "class='input mandatory'") ?>
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

<script>
	var diaLaboral = $("input[name='DiaLaboral']:checked").val();
	changeDiaLaboral(diaLaboral);

	$("input[name='DiaLaboral']").change(function (){
		changeDiaLaboral($(this).val());
	});

	function changeDiaLaboral(val){
		if(val == 1){
			$("#divTurno").show("slow");
			$("#divDianl").hide("slow");
		}else{
			$("#divTurno").hide("slow");
			$("#divDianl").show("slow");
		}
	}
	
</script>
