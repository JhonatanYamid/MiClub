			<a href="procedures/excel-encuesta2-respuesta.php?IDEncuesta2=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif">Exportar</a>

			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>

					<th><?= SIMUtil::get_traduccion('', '', 'Encuesta', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></th>

					<?php
					//Consulto los campos dinamicos
					$r_campos = &$dbo->all("PreguntaEncuesta2", "IDEncuesta2 = '" . $frm[$key]  . "' Order by IDPreguntaEncuesta2");
					while ($r = $dbo->object($r_campos)) :
						$array_preguntas[] = $r->IDPreguntaEncuesta2;	?>
						<th><?php echo $r->EtiquetaCampo; ?></th>
					<?php endwhile; ?>
					<th><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></th>


				</tr>
				<tbody id="listacontactosanunciante">
					<?php
					$datos_encuesta = $dbo->fetchAll("Encuesta2", " IDEncuesta2 = '" . $frm[$key] . "' ", "array");
					$r_datos = $dbo->query("Select IDSocio,P.IDPreguntaEncuesta2 From PreguntaEncuesta2 P,Encuesta2Respuesta ER Where ER.IDPreguntaEncuesta2=P.IDPreguntaEncuesta2 and P.IDEncuesta2 = '" . $frm[$key] . "' Group by IDSocio");
					while ($r = $dbo->object($r_datos)) :
						unset($array_respuesta_socio);
						$Fecha = "";
					?>
						<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
							<td><?php echo utf8_encode($dbo->getFields("Encuesta2", "Nombre", "IDEncuesta2 = '" . $frm[$key] . "'")); ?></td>
							<td><?php
								if ($datos_encuesta["DirigidoA"] == "E") {
									echo utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r->IDSocio . "'"));
								} else {
									echo utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocio . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocio . "'"));
								}
								?>
							</td>

							<?php
							$sql_repuesta_socio = "Select * From Encuesta2Respuesta Where IDEncuesta2 = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "'";
							$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
							while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
								$array_respuesta_socio[][$row_respuesta["IDPreguntaEncuesta2"]] = $row_respuesta["Valor"];
								$Fecha = $row_respuesta["FechaTrCr"];
							endwhile;
							if (count($array_preguntas) > 0) :
								foreach ($array_preguntas as $id_pregunta) :
									$Fecha = "";
							?>
									<td>
										<?php
										$sql_repuesta_socio = "Select * From Encuesta2Respuesta Where IDEncuesta2 = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "' and IDPreguntaEncuesta2 = '" . $id_pregunta . "'";
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
							<td><?php echo $Fecha; ?></td>

						</tr>
					<?php endwhile; ?>
				</tbody>

			</table>