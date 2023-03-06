			<a href="procedures/excel-dotacion-respuesta.php?IDDotacion=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif">Exportar</a>

			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>

					<th><?= SIMUtil::get_traduccion('', '', 'Dotacion', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?></th>

					<?php
					//Consulto los campos dinamicos
					$r_campos = &$dbo->all("PreguntaDotacion", "IDDotacion = '" . $frm[$key]  . "' Order by IDPreguntaDotacion");
					while ($r = $dbo->object($r_campos)) :
						$array_PreguntaDotacions[] = $r->IDPreguntaDotacion;	?>
						<th><?php echo $r->EtiquetaCampo; ?></th>
					<?php endwhile; ?>
					<th><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></th>


				</tr>
				<tbody id="listacontactosanunciante">
					<?php
					$datos_Dotacion = $dbo->fetchAll("Dotacion", " IDDotacion = '" . $frm[$key] . "' ", "array");
					$r_datos = $dbo->query("Select IDSocio,P.IDPreguntaDotacion From PreguntaDotacion P,DotacionRespuesta ER Where ER.IDPreguntaDotacion=P.IDPreguntaDotacion and P.IDDotacion = '" . $frm[$key] . "' Group by IDSocio");
					while ($r = $dbo->object($r_datos)) :
						unset($array_respuesta_socio);
						$Fecha = "";
					?>
						<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
							<td><?php echo utf8_encode($dbo->getFields("Dotacion", "Nombre", "IDDotacion = '" . $frm[$key] . "'")); ?></td>
							<td><?php
								if ($datos_Dotacion["DirigidoA"] == "E") {
									echo utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r->IDSocio . "'"));
								} else {
									echo utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocio . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocio . "'"));
								}
								?>
							</td>

							<?php
							$sql_repuesta_socio = "Select * From DotacionRespuesta Where IDDotacion = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "'";
							$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
							while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
								$array_respuesta_socio[][$row_respuesta["IDPreguntaDotacion"]] = $row_respuesta["Valor"];
								$Fecha = $row_respuesta["FechaTrCr"];
							endwhile;
							if (count($array_PreguntaDotacions) > 0) :
								foreach ($array_PreguntaDotacions as $id_PreguntaDotacion) :
									$Fecha = "";
							?>
									<td>
										<?php
										$sql_repuesta_socio = "Select * From DotacionRespuesta Where IDDotacion = '" . $frm[$key] . "' and IDSocio = '" . $r->IDSocio . "' and IDPreguntaDotacion = '" . $id_PreguntaDotacion . "'";
										$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
										while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) {
											echo $row_respuesta["Valor"] . "<br>";
											$Fecha .= $row_respuesta["FechaTrCr"] . "<br>";
										}
										?>


										<?php //echo $array_respuesta_socio[$id_PreguntaDotacion]; 
										?></td>
							<?php endforeach;
							endif; ?>
							<td><?php echo $Fecha; ?></td>

						</tr>
					<?php endwhile; ?>
				</tbody>

			</table>