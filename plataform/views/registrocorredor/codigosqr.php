<form target="_blank" class="form-horizontal" role="form" method="post" id="frmCodigoqr" action="/plataform/views/registrocorredor/codigosqrpdf.php" enctype="multipart/form-data">
	<div class="form-group first ">
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carrera', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<?= SIMHTML::formPopupV2('Carrera', 'Nombre','Nombre', 'IDCarrera', 'IDCarreraPdf', "", SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"", "onchange = 'changeCategoria(\"Pdf\")'", "AND Activo = 'S' AND IDClub = $IDClub"); ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>: </label>
			<div class="col-sm-8" id="selectCategoriaPdf"></div>
		</div>
	</div>	
	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<!-- <input type="hidden" name="action" id="action" value="<?php //echo $newmode ?>" /> -->
			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
			<!-- <input id="submitForm" type="submit" class="submit" value="" style="display: none;"> -->
			<span class="input-group-btn">
				<button type="submit" class="btn btn-purple btn-sm submit">
					<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?> 
				</button>
			</span>
			<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
		</div>
	</div>
</form>