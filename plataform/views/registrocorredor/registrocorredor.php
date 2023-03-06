<?
$actionFrm = SIMNet::req("action") == 'edit' ? SIMUtil::lastURI() : "/plataform/$script.php?action=add";
?>

<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?= $actionFrm ?>" enctype="multipart/form-data">
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>: </label>
			<div class="col-sm-8"><input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" value="<?php echo $frm["NumeroDocumento"]; ?>" class="col-xs-12 mandatory autocomplete-ajax-corredor" onkeyup="limpiar()" title="<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>"></div>
			<input type="hidden" name="IDSocio" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>" id="IDSocio" value="<?php echo $frm['IDSocio'] ?>" />
			<input type="hidden" name="nmDoc" id="nmDoc" value="<?= $frm["NumeroDocumento"]; ?>" />
		</div>
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>
			<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>"></div>
			<input type="hidden" name="nom" id="nom" value="<?= $frm["Nombre"]; ?>" />
		</div>
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'apellido', LANGSESSION); ?>: </label>
			<div class="col-sm-8"><input type="text" id="Apellido" name="Apellido" placeholder="<?= SIMUtil::get_traduccion('', '', 'apellido', LANGSESSION); ?>" value="<?php echo $frm["Apellido"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'apellido', LANGSESSION); ?>"></div>
			<input type="hidden" name="apl" id="apl" value="<?= $frm["Apellido"]; ?>" />
		</div>
	</div>
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>: </label>
			<div class="col-sm-8"><input type="text" id="Email" name="Email" placeholder="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" value="<?php echo $frm["Email"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>"></div>
			<input type="hidden" name="mail" id="mail" value="<?= $frm["Email"]; ?>" />
		</div>
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carrera', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formPopup('Carrera', 'Nombre', 'IDCarrera', 'IDCarrera', $frm["IDCarrera"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', 'onchange = "changeCategoria()"', "AND Activo = 'S' AND IDClub = $IDClub"); ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>: </label>
			<div class="col-sm-8" id="selectCategoria"></div>
		</div>
	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ingresar Camiseta manual </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["IngresarCamisetaManual"], 'IngresarCamisetaManual', "class='input mandatory'") ?>
			</div>
		</div>
		<? if (SIMNet::req("action") == 'add') { ?>
			<div class="col-xs-12 col-sm-4">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>: </label>
				<div class="col-sm-8"><input type="text" id="CampoIngresarCamisetaManual" name="CampoIngresarCamisetaManual" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>" value="<?php echo $frm["NumCamiseta"]; ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>"></div>
			</div>
		<? } ?>
	</div>
	<? if (SIMNet::req("action") == 'edit') { ?>
		<div class="form-group first ">
			<div class="col-xs-12 col-sm-4">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>: </label>
				<div class="col-sm-8"><input type="text" id="NumCamiseta" name="NumCamiseta" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>" value="<?php echo $frm["NumCamiseta"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumeroCamiseta', LANGSESSION); ?>"></div>
			</div>
		</div>
	<? } ?>
	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub ?>" />
			<input id="submitForm" type="submit" class="submit" value="" style="display: none;">
			<span class="input-group-btn">
				<button onclick="guardarCorredor()" type="button" class="btn btn-purple btn-sm">
					<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
				</button>
			</span>
			<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
		</div>
	</div>
</form>