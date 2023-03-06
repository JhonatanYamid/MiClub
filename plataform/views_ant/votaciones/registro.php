			<a href="procedures/excel-votacion-respuesta.php?IDVotacion=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif"><?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a>

			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>

					<th><?= SIMUtil::get_traduccion('', '', 'Votacion', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Votante', LANGSESSION); ?></th>


					<?php
					//Consulto los campos dinamicos
					$r_campos = &$dbo->all("PreguntaVotacion", "IDVotacion = '" . $frm[$key]  . "' Order by IDPregunta");
					while ($r = $dbo->object($r_campos)) :
						$array_preguntas[] = $r->IDPregunta;	?>
						<th><?php echo $r->EtiquetaCampo; ?></th>
					<?php endwhile; ?>
					<th><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></th>


				</tr>
				<tbody id="listacontactosanunciante">
					<?php
					$datos_encuesta = $dbo->fetchAll("Votacion", " IDVotacion = '" . $frm[$key] . "' ", "array");
					$r_datos = $dbo->query("Select IDSocio,P.IDPregunta From PreguntaVotacion P,VotacionRespuesta ER Where ER.IDPregunta=P.IDPregunta and P.IDVotacion = '" . $frm[$key] . "' Group by IDSocio");
					while ($r = $dbo->object($r_datos)) :
						unset($array_respuesta_socio);
						$Fecha = "";
					?>
						<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
							<td><?php echo utf8_encode($dbo->getFields("Votacion", "Nombre", "IDVotacion = '" . $frm[$key] . "'")); ?></td>
							<td>
								<?php
								$datos_persona = $dbo->fetchAll("Socio", " IDSocio = '" . $r->IDSocio . "' ", "array");
								echo $datos_persona["Nombre"] . " " . $datos_persona["Apellido"];
								?>
							</td>


							<?php
							$sql_repuesta_socio = "Select * From VotacionRespuesta Where IDSocio = '" . $r->IDSocio . "' and IDVotacion = '" . $frm[$key] . "' and valor <> 'null' Group by IDPregunta";
							$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
							while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
								$array_respuesta_socio[$row_respuesta["IDPregunta"]] = $row_respuesta["Valor"];
								$Fecha = $row_respuesta["FechaTrCr"];
							endwhile;
							if (count($array_preguntas) > 0) :
								foreach ($array_preguntas as $id_pregunta) : ?>
									<td>
										<?php
										//echo $array_respuesta_socio[$id_pregunta];
										echo "privado";
										?>
									</td>
							<?php endforeach;
							endif; ?>
							<td><?php echo $Fecha; ?></td>

						</tr>
					<?php endwhile; ?>
				</tbody>

			</table>