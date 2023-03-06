sdfsdfdsf
<?php exit; ?>

<a href="procedures/excel-encuesta-respuesta.php?IDEncuesta=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif"><?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a>

<table id="simple-table" class="table table-striped table-bordered table-hover">
	<tr>

		<th><?= SIMUtil::get_traduccion('', '', 'Encuesta', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></th>
		<?php
		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("Pregunta", "IDEncuesta = '" . $frm[$key]  . "' Order by IDPregunta");
		while ($r = $dbo->object($r_campos)) :
			$array_preguntas[] = $r->IDPregunta;	?>
			<th><?php echo $r->EtiquetaCampo; ?></th>
		<?php endwhile; ?>
		<th><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></th>
		<th><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>


	</tr>
	<tbody id="listacontactosanunciante">
		<?php
		$datos_encuesta = $dbo->fetchAll("Encuesta", " IDEncuesta = '" . $frm[$key] . "' ", "array");
		$r_datos = $dbo->query("Select IDSocio,P.IDPregunta From Pregunta P,EncuestaRespuesta ER Where ER.IDPregunta=P.IDPregunta and P.IDEncuesta = '" . $frm[$key] . "' Group by IDSocio");
		while ($r = $dbo->object($r_datos)) :
			unset($array_respuesta_socio);
			$Fecha = "";
		?>
			<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
				<td><?php echo utf8_encode($dbo->getFields("Encuesta", "Nombre", "IDEncuesta = '" . $frm[$key] . "'")); ?></td>
				<td><?php
					if ($datos_encuesta["DirigidoA"] == "E") {
						echo utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r->IDSocio . "'"));
					} else {
						//echo utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocio."'" )." ".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r->IDSocio."'" ));
						$sql = "SELECT Nombre, Apellido FROM Socio WHERE IDSocio=$r->IDSocio LIMIT 1";
						$socioQuery = $dbo->query($sql);
						$socio = $dbo->fetch($socioQuery);
						echo utf8_encode("{$socio['Nombre']} {$socio['Apellido']}");
					}
					?>
				</td>

				<?php
				$sql_repuesta_socio = "Select * From EncuestaRespuesta Where IDEncuesta = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "'";
				$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
				while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
					$array_respuesta_socio[][$row_respuesta["IDPregunta"]] = $row_respuesta["Valor"];
					$Fecha = $row_respuesta["FechaTrCr"];
				endwhile;
				if (count($array_preguntas) > 0) :
					foreach ($array_preguntas as $id_pregunta) :
						$Fecha = "";
				?>
						<td>
							<?php
							$sql_repuesta_socio = "Select * From EncuestaRespuesta Where IDEncuesta = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "' and IDPregunta = '" . $id_pregunta . "'";
							$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
							while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) {
								echo $row_respuesta["Valor"] . "<br>";
								$Fecha .= $row_respuesta["FechaTrCr"] . "<br>";
							}
							?>


							<?php //echo $array_respuesta_socio[$id_pregunta]; 
							?></td>
				<?php endforeach;
				endif; ?>
				<td><?php echo $Fecha;
					$cantidad = sizeof($array_preguntas);
					?>
				</td>
				<td>
					<a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaRespuesta&IDSocio=<?php echo $r->IDSocio; ?>&IDEncuesta=<?php echo $frm[$key]; ?>&cantidad=<?php echo $cantidad; ?>"></a>
				</td>

			</tr>
		<?php endwhile; ?>
	</tbody>

</table>