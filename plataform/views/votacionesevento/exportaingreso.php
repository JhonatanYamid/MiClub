			<a href="procedures/excel-ingreso-votacion.php?IDVotacionEvento=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif"> <?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a>

			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>
					<th> <?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Persona', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></th>
					<th><?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?></th>
				</tr>
				<tbody id="listacontactosanunciante">
					<?php
					$r_datos = $dbo->query("SELECT IDSocio,IDVotacionEvento,Tipo,Fecha,IDUsuario
																											FROM LogAccesoVotacion
																											Where  IDVotacionEvento = '" . $frm[$key] . "'
																											ORDER BY IDSocio,Fecha ASC");
					while ($r = $dbo->object($r_datos)) :
						$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r->IDSocio . "'", "array");
					?>
						<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
							<td><?php echo $dbo->getFields("VotacionEvento", "Nombre", "IDVotacionEvento = '" . $r->IDVotacionEvento . "'"); ?></td>
							<td><?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?></td>
							<td><?php echo $r->Tipo; ?></td>
							<td><?php echo $r->Fecha; ?></td>
							<td><?php echo $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r->IDUsuario . "'"); ?></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>