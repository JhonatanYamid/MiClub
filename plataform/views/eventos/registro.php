<?= SIMUtil::get_traduccion('', '', 'RegistrarSocioalevento', LANGSESSION); ?>
<form class="form-horizontal formvalida" role="form" method="post" id="RegistraSocioEvento<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<div class="form-group first">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Elparticipanteessocio', LANGSESSION); ?>: </label>
			<div class="col-sm-8">
				<? echo SIMHTML::formradiogroup(SIMResources::$sino, !empty($frm["EsSocio"]) ? $frm["EsSocio"] : 'S', 'EsSocio', "class='input'") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 socio">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="Socio" name="Socio" placeholder=" <?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax" title="Socio" value="">
				<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
			</div>
		</div>
		<br><br>
		<div class="col-xs-12 col-sm-6 socio">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Beneficiario', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<select name="IDSocioBeneficiario" id="IDSocioBeneficiario" class="form-control " title="IDSocioBeneficiario">
					<option value=""></option>
				</select>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 externo" style="display: none;">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder=" <?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" class="col-xs-12 externoinp" title=" <?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" value="<?php echo $frm["NumeroDocumento"]; ?>">
			</div>
		</div>
	</div>
	<div class="form-group first externo" style="display: none;">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="NombreNoSocio" name="NombreNoSocio" placeholder=" <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" class="col-xs-12 externoinp" title=" <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="CorreoElectronico" name="CorreoElectronico" placeholder=" <?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?>" class="col-xs-12 externoinp" title=" <?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?>" value="<?php echo $frm["CorreoElectronico"]; ?>">
			</div>
		</div>
	</div>
	<div class="form-group first externo" style="display: none;">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="Celular" name="Celular" placeholder=" <?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?>" class="col-xs-12" title=" <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" value="<?php echo $frm["Celular"]; ?>">
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaNacimiento', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder=" <?= SIMUtil::get_traduccion('', '', 'FechaNacimiento', LANGSESSION); ?>" class="col-xs-12 calendar" title=" <?= SIMUtil::get_traduccion('', '', 'FechaNacimiento', LANGSESSION); ?>" value="<?php echo $frm["FechaNacimiento"]; ?>">
			</div>
		</div>
	</div>


	<div class="form-group first ">
		<?php
		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("CampoFormularioEvento", "IDEvento = '" . $frm[$key]  . "'");
		while ($r = $dbo->object($r_campos)) : ?>
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r->EtiquetaCampo; ?> </label>
				<div class="col-sm-8">
					<input type="text" id="Campo<?php echo $r->IDCampoFormularioEvento; ?>" name="Campo<?php echo $r->IDCampoFormularioEvento; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="">
				</div>
			</div>
		<?php endwhile; ?>
	</div>

	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $EditCampoFormularioEvento[$key] ?>" />
			<input type="hidden" name="IDEvento" id="IDEvento" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="insertasocioevento" />
			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
			<input id="submitForm" type="submit" class="submit" value="" style="display: none;">

			<span class="input-group-btn">
				<button onclick="guardarParticipante()" type="button" class="btn btn-purple btn-sm">
					<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>
				</button>
			</span>
		</div>
	</div>

</form>

<br><a href="procedures/excel-evento-registro.php?IDEvento=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif">Exportar</a>
<table id="simple-table" class="table table-striped table-bordered table-hover">
	<tr>

		<th><?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Participante', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Beneficiario', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'CodigoPago', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'EstadoTransaccion', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'CodigoRespuesta', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'FechaRegistro', LANGSESSION); ?></th>

		<?php
		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("CampoFormularioEvento", "IDEvento = '" . $frm[$key]  . "'");
		while ($r = $dbo->object($r_campos)) :
			$array_campos[] = $r->IDCampoFormularioEvento;	?>
			<th><?php echo $r->EtiquetaCampo; ?></th>
		<?php endwhile; ?>

		<th><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></th>


	</tr>
	<tbody id="listacontactosanunciante">
		<?php
		$r_datos = $dbo->all("EventoRegistro", "IDEvento = '" . $frm[$key]  . "'");

		while ($r = $dbo->object($r_datos)) :

			//trae la informacion de la persona que ingresa dependiendo si es socio o no
			if ($r->IDNoSocios > 0) {
				$persona = $dbo->getFields("NoSocios", array("NumeroDocumento", "Nombre", "CorreoElectronico"), "IDNoSocios = '" . $r->IDNoSocios . "'");
				$tipo = "Externo";
			} else  if ($r->IDSocio > 0) {
				$persona = $dbo->getFields("Socio", array("NumeroDocumento", "CONCAT(Nombre,' ',Apellido) as Nombre", "CorreoElectronico"), "IDSocio = '" . $r->IDSocio . "'");
				$tipo = "Socio";
			} else {
				$persona = $dbo->getFields("Usuario", array("NumeroDocumento", "Nombre"), "IDUsuario = '" . $r->IDUsuario . "'");
				$tipo = "Usuario";
			}
		?>
			<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
				<td><?php echo $dbo->getFields("Evento", "Titular", "IDEvento = '" . $r->IDEvento . "'"); ?></td>
				<td><?= $persona['NumeroDocumento'] ?></td>
				<td><?= $persona['Nombre'] ?></td>
				<td><?= $tipo ?></td>
				<td><?= $persona['CorreoElectronico'] ?></td>
				<td><?php echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocioBeneficiario . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocioBeneficiario . "'"); ?></td>
				<td><?php echo $r->Valor; ?></td>
				<td><?php echo $r->CodigoPago; ?></td>
				<td><?php echo $r->EstadoTransaccion; ?></td>
				<td><?php echo $r->CodigoRespuesta; ?></td>
				<td><?php echo $r->FechaTrCr; ?></td>
				<?php
				//Consulto los campos dinamicos
				$r_campos = &$dbo->all("EventoRegistroDatos", "IDEventoRegistro = '" . $r->IDEventoRegistro  . "'");

				while ($rdatos = $dbo->object($r_campos)) :
					$array_otros_datos[$rdatos->IDEventoRegistro][$rdatos->IDCampoFormularioEvento] =  $rdatos->Valor;
				endwhile;

				if (count($array_campos) > 0) :
					foreach ($array_campos as $id_campo) : ?>
						<td>&nbsp;<?php echo $array_otros_datos[$r->IDEventoRegistro][$id_campo]; ?></td>
				<?php endforeach;
				endif; ?>

				<td align="center">
					<a class="ace-icon glyphicon glyphicon-trash confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaRegistro&id=<?php echo $r->IDEvento; ?>&IDEventoRegistro=<? echo $r->IDEventoRegistro ?>&tabevento=invitaciones"></a>&nbsp;&nbsp;
					<a class="ace-icon glyphicon glyphicon-qrcode" href="<?= SOCIO_ROOT . "qr/" . $r->Qr ?>"></a>
				</td>

			</tr>
		<?php endwhile; ?>

	</tbody>
	<tr>
		<th class="texto" colspan="16"></th>
	</tr>
</table>

<script>
	let form = document.createElement('form');

	function changeEsSocio(event) {
		let value = event.target.value;
		var elemExterno = document.getElementsByClassName("externo");
		var elemSocio = document.getElementsByClassName("socio");

		if (value == 'N') {
			Array.from(elemExterno).forEach(function(element) {
				element.style.display = "block";
			});

			Array.from(elemSocio).forEach(function(element) {
				element.style.display = "none";
			});
		} else {
			Array.from(elemExterno).forEach(function(element) {
				element.style.display = "none";
			});

			Array.from(elemSocio).forEach(function(element) {

				element.style.display = "block";
			});
		}

		document.getElementById('IDSocio').classList.toggle("mandatory");

		document.getElementById('NumeroDocumento').classList.toggle("mandatory");
		document.getElementById('CorreoElectronico').classList.toggle("mandatory");
		document.getElementById('NombreNoSocio').classList.toggle("mandatory");
	}

	document.querySelectorAll("input[name='EsSocio']").forEach((input) => {
		input.addEventListener('change', changeEsSocio);
	});

	function guardarParticipante() {
		let documento = document.getElementById('NumeroDocumento').value;
		let nombre = document.getElementById('NombreNoSocio').value;
		let correo = document.getElementById('CorreoElectronico').value;

		if (documento != '' && nombre != '' && correo != '') {

			const Http = new XMLHttpRequest();
			const url = 'includes/async/eventos.async.php?oper=searchdoc&doc=' + documento + '&nombre=' + nombre + '&correo=' + correo;
			Http.open("GET", url, false);
			Http.send(null);

			if (Http.status === 200) {
				let response = Http.responseText;

				if (response != '') {
					let msgConf = "Atencion! El correo electronico:" + correo + " y el nombre:" + nombre + " no coinciden con los datos asociados al documento:" + documento + " registrados en nuestra base de datos. Si continua seran reemplazados ¿Desea continuar?";

					if (confirm(msgConf) === false) {
						return;
					}
				}
			}
		}

		document.getElementById('submitForm').click();
	}
</script>