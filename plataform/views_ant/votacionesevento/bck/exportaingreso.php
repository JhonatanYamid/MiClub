			<a href="procedures/excel-ingreso-votacion.php?IDVotacionEvento=<?php echo  $frm[$key] ; ?>"><img src="assets/img/xls.gif" >Exportar</a>

              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
													<th>Evento</th>
													<th>Persona</th>
													<th>Tipo</th>
													<th>Fecha</th>
													<th>Usuario</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
															$r_datos = $dbo->query("SELECT IDSocio,IDVotacionEvento,Tipo,Fecha,IDUsuario
																											FROM LogAccesoVotacion
																											Where  IDVotacionEvento = '".$frm[$key]."'
																											ORDER BY IDSocio,Fecha ASC");
                              while( $r = $dbo->object( $r_datos ) ):
																	$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $r->IDSocio . "'", "array" );
																 ?>
                                  <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
									  								<td><?php echo $dbo->getFields( "VotacionEvento", "Nombre", "IDVotacionEvento = '" . $r->IDVotacionEvento . "'" ); ?></td>
																		<td><?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?></td>
																		<td><?php echo $r->Tipo; ?></td>
																		<td><?php echo $r->Fecha; ?></td>
																		<td><?php echo $dbo->getFields( "Usuario", "Nombre", "IDUsuario = '" . $r->IDUsuario . "'" ); ?></td>
											  					</tr>
															<?php endwhile; ?>
                      </tbody>
              </table>
