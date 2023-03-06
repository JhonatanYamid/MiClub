<?= SIMUtil::get_traduccion('', '', 'RegistrarSocioalevento', LANGSESSION); ?>
<form class="form-horizontal formvalida" role="form" method="post" id="RegistraSocioEvento<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" value="">
				<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

			</div>
		</div>
		<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Beneficiario', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="AccionBeneficiario" name="AccionBeneficiario" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-beneficiario" title="AccionBeneficiario" value="">
				<input type="hidden" name="IDSocioBeneficiario" value="<?php echo $frm["IDSocioBeneficiario"]; ?>" id="IDSocioBeneficiario" class="" title="Beneficiario">

			</div>
		</div>
	</div>


	<div class="form-group first ">
		<?php
		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("CampoFormularioEvento2", "IDEvento2 = '" . $frm[$key]  . "'");
		while ($r = $dbo->object($r_campos)) : ?>
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r->EtiquetaCampo; ?> </label>
				<div class="col-sm-8">
					<input type="text" id="Campo<?php echo $r->IDCampoFormularioEvento2; ?>" name="Campo<?php echo $r->IDCampoFormularioEvento2; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="">
				</div>
			</div>
		<?php endwhile; ?>
	</div>







	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $EditCampoFormularioEvento[$key] ?>" />
			<input type="hidden" name="IDEvento2" id="IDEvento2" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="insertasocioevento" />
			<input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />



		</div>
	</div>




</form>

<br><a href="procedures/excel-evento-registro2.php?IDEvento2=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif"><?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a>
<table id="simple-table" class="table table-striped table-bordered table-hover">
	<tr>

		<th><?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Beneficiario', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'CodigoPago', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'EstadoTransaccion', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'CodigoRespuesta', LANGSESSION); ?></th>

		<?php
		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("CampoFormularioEvento2", "IDEvento2 = '" . $frm[$key]  . "'");
		while ($r = $dbo->object($r_campos)) :
			$array_campos[] = $r->IDCampoFormularioEvento2;	?>
			<th><?php echo $r->EtiquetaCampo; ?></th>
		<?php endwhile; ?>


		<th><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>


	</tr>
	<tbody id="listacontactosanunciante">
		<?php
		$r_datos = &$dbo->all("EventoRegistro2", "IDEvento2 = '" . $frm[$key]  . "'");
		while ($r = $dbo->object($r_datos)) : ?>
			<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
				<td><?php echo $dbo->getFields("Evento2", "Titular", "IDEvento2 = '" . $r->IDEvento2 . "'"); ?></td>
				<td><?php echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocio . "'") . "" . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocio . "'"); ?></td>
				<td><?php echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocioBeneficiario . "'") . "" . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocioBeneficiario . "'"); ?></td>
				<td><?php echo $r->Valor; ?></td>
				<td><?php echo $r->CodigoPago; ?></td>
				<td><?php echo $r->EstadoTransaccion; ?></td>
				<td><?php echo $r->CodigoRespuesta; ?></td>
				<?php
				//Consulto los campos dinamicos
				$r_campos = &$dbo->all("EventoRegistroDatos2", "IDEventoRegistro2 = '" . $r->IDEventoRegistro2  . "'");

				while ($rdatos = $dbo->object($r_campos)) :
					$array_otros_datos[$rdatos->IDEventoRegistro2][$rdatos->IDCampoFormularioEvento2] =  $rdatos->Valor;
				endwhile;


				if (count($array_campos) > 0) :
					foreach ($array_campos as $id_campo) {
				?>
						<td>&nbsp;<?php echo $array_otros_datos[$r->IDEventoRegistro2][$id_campo]; ?></td>
				<?php };
				endif; ?>

				<td align="center"><a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaRegistro&id=<?php echo $r->IDEvento2; ?>&IDEventoRegistro2=<? echo $r->IDEventoRegistro2 ?>&tabevento=invitaciones"></a></td>

			</tr>
		<?php endwhile; ?>

	</tbody>
	<tr>
		<th class="texto" colspan="16"></th>
	</tr>
</table>