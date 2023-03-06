<form class="form-horizontal formvalida" role="form" method="post" id="EditPregunta<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<?php
	$action = "InsertarPregunta";
	if ($_GET["IDPreguntaEncuestaArbol"]) {
		$EditPregunta = $dbo->fetchAll("PreguntaEncuestaArbol", " IDPreguntaEncuestaArbol = '" . $_GET["IDPreguntaEncuestaArbol"] . "' ", "array");
		$action = "ModificaPregunta";
	?>
		<input type="hidden" name="IDPreguntaEncuestaArbol" id="IDPreguntaEncuestaArbol" value="<?php echo $EditPregunta[IDPreguntaEncuestaArbol] ?>" />
	<?php
	}
	?>



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pregunta', LANGSESSION); ?> </label>
			<div class="col-sm-8">
				<input type="text" id="Nombre" name="EtiquetaCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'EtiquetaCampo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'EtiquetaCampo', LANGSESSION); ?>" value="<?php echo $EditPregunta["EtiquetaCampo"]; ?>">

			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipoderespuesta', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<select class="form-control" id="TipoCampo" name="TipoCampo">
					<optgroup label="Estándar">
						<option value="text" <?php if ($EditPregunta["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
						<option value="textarea" <?php if ($EditPregunta["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
						<option value="radio" <?php if ($EditPregunta["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
						<option value="checkbox" <?php if ($EditPregunta["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
						<option value="select" <?php if ($EditPregunta["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
						<option value="number" <?php if ($EditPregunta["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
						<!--<option value="page">Page Break</option>-->
					</optgroup>
					<optgroup label="Elegantes">
						<option value="date" <?php if ($EditPregunta["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
						<option value="time" <?php if ($EditPregunta["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
						<option value="email" <?php if ($EditPregunta["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
					</optgroup>
					<optgroup label="Titulo">
						<option value="titulo" <?php if ($EditPregunta["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>

				</select>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>
			<div class="col-sm-8">
				<select class="form-control" id="IDCategoriaEncuestaArbol" name="IDCategoriaEncuestaArbol">
					<?php
					$html = "";
					$sql_CategoriaEncuestaArbol = "SELECT * FROM CategoriaEncuestaArbol WHERE IDClub = '" . SIMUser::get('club') . "' AND Publicar = 'S'";
					$q_CategoriaEncuestaArbol = $dbo->query($sql_CategoriaEncuestaArbol);

					while ($CategoriaEncuestaArbol = $dbo->assoc($q_CategoriaEncuestaArbol)) {
						$selected = ($EditPregunta["IDCategoriaEncuestaArbol"] == $CategoriaEncuestaArbol['IDCategoriaEncuestaArbol']) ? "selected" : "";
						$html .= '<option value="' . $CategoriaEncuestaArbol['IDCategoriaEncuestaArbol'] . '" ' . $selected . '> ' . $CategoriaEncuestaArbol['Nombre'] . '</option>';
					}
					echo $html;
					?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroPregunta', LANGSESSION); ?></label>

			<div class="col-sm-8">
				<input id="NumeroPregunta" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'NumeroPregunta', LANGSESSION); ?>" name="NumeroPregunta" class="input mandatory" value="<?php echo $EditPregunta["NumeroPregunta"] ?>" />
			</div>
		</div>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $EditPregunta["Orden"]; ?>">
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?> </label>

			<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPregunta["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
		</div>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Puntos', LANGSESSION); ?> </label>

			<div class="col-sm-8">
				<input type="number" id="Puntos" name="Puntos" placeholder="<?= SIMUtil::get_traduccion('', '', 'Puntos', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Puntos', LANGSESSION); ?>" value="<?php echo $EditPregunta["Puntos"]; ?>">
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

			<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPregunta["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
		</div>
	</div>

	<div class="col-xs-12 col-sm-12">
		<?= SIMUtil::get_traduccion('', '', 'Opcionesderespuestaparacuandoseademultiplerespuestaoseleccion', LANGSESSION); ?>
		<table id="simple-table" class="table table-bordered table-hover">
			<tr>
				<td><?= SIMUtil::get_traduccion('', '', 'OpcionRespuesta', LANGSESSION); ?></td>
				<td><?= SIMUtil::get_traduccion('', '', 'Irapregunta', LANGSESSION); ?></td>
				<td><?= SIMUtil::get_traduccion('', '', 'Puntos', LANGSESSION); ?></td>
				<td><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></td>
			</tr>
			<?php
			$sql_opciones = "SELECT * FROM EncuestaArbolOpcionesRespuesta WHERE IDEncuestaArbolPregunta = '" . $_GET["IDPreguntaEncuestaArbol"] . "' Order by Orden";
			$r_opciones = $dbo->query($sql_opciones);
			$contador = 1;
			while ($row_opciones = $dbo->fetchArray($r_opciones)) {
				$array_opciones[$contador] = $row_opciones;
				$contador++;
			}
			ksort($array_opciones);
			$CantidadOpciones = 20;
			for ($i = 1; $i <= $CantidadOpciones; $i++) {
				$valor_opciones = $array_opciones[$i];
			?>
				<tr>
					<td><textarea rows="2" cols="50" name="Respuesta<?php echo $i; ?>"><?php echo $valor_opciones["Opcion"]; ?></textarea></td>
					<td>
						<select name="IDEncuestaArbolPreguntaSiguiente<?php echo $i; ?>[]" multiple id="IDEncuestaArbolPreguntaSiguiente<?php echo $i; ?>" class="form-control chosen-select">
							<option value="">Seleccione...</option>
							<?php
							$sql_area_club = string;
							$sql_preg_club = "Select * From PreguntaEncuestaArbol Where IDEncuestaArbol = '" . $frm[$key] . "' and Publicar = 'S' order by EtiquetaCampo";
							$qry_preg_club = $dbo->query($sql_preg_club);
							while ($r_preg = $dbo->fetchArray($qry_preg_club)) :
								$arr_preguntas = explode('|', $valor_opciones["IDEncuestaArbolPreguntaSiguiente"]);
							?>
								<option value="<?php echo $r_preg["IDPreguntaEncuestaArbol"]; ?>" <?php if (in_array($r_preg["IDPreguntaEncuestaArbol"], $arr_preguntas)) echo "selected";  ?>><?php echo $r_preg["EtiquetaCampo"]; ?></option>
							<?php
							endwhile;
							?>
						</select>
					</td>
					<td><input type="number" name="Puntos<?php echo $i; ?>" step="any" value="<?php echo $valor_opciones["Puntos"]; ?>"></td>
					<td><input type="text" name="Orden<?php echo $i; ?>" value="<?php echo $valor_opciones["Orden"]; ?>"></td>
				</tr>
			<?php } ?>

		</table>
	</div>








	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $EditPregunta[$key] ?>" />
			<input type="hidden" name="CantidadOpciones" id="CantidadOpciones" value="<?php echo $CantidadOpciones ?>" />
			<input type="hidden" name="IDEncuestaArbol" id="IDEncuestaArbol" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
			<input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditPregunta[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />


		</div>
	</div>




</form>










<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
	<tr>
		<th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Puntos', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
		<th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
	</tr>
	<tbody id="listacontactosanunciante">
		<?php

		$r_documento = &$dbo->all("PreguntaEncuestaArbol", "IDEncuestaArbol = '" . $frm[$key]  . "'");

		while ($r = $dbo->object($r_documento)) {
		?>

			<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
				<td align="center" width="64">
					<a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDPreguntaEncuestaArbol=" . $r->IDPreguntaEncuestaArbol ?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
				</td>
				<td><?php echo $r->EtiquetaCampo; ?></td>
				<td><?php echo $r->TipoCampo; ?></td>
				<td><?php echo $r->Obligatorio; ?></td>
				<td><?php echo $r->Puntos; ?></td>
				<td><?php echo $r->Orden; ?></td>
				<td><?php echo $r->Publicar; ?></td>
				<td align="center" width="64">
					<a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPregunta&id=<?php echo $frm[$key]; ?>&IDPreguntaEncuestaArbol=<? echo $r->IDPreguntaEncuestaArbol ?>&tabpregunta=formulario"></a>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
	<tr>
		<th class="texto" colspan="16"></th>
	</tr>
</table>