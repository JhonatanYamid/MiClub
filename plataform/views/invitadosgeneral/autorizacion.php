              
              <? if($_GET["refiere"]!="porteria"): ?>
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                        <td colspan="9">
                        <a href="procedures/excel-reporteingresosalida.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDInvitado=<?php echo $frm[$key] ?>"><img src="assets/img/xls.gif" >Exportar Ingresos/Salidas</a> |
                        <a href="procedures/excel-reporteaccesos.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDInvitado=<?php echo $frm[$key] ?>"><img src="assets/img/xls.gif" >Exportar Autorizaciones</a>                        
                        </td>
                      </tr>
                      <tr>
                              <th>Tipo</th>
                              <th>Fecha Inicio Aut.</th>
                              <th>Fecha Fin Aut.</th>
                              <th>Tipo Movimiento</th>
                              <th>Fecha Movimiento</th>
                              <th>Vehiculo</th>
                              <th>Socio que Invito</th>
                              <th>Documento Socio</th>
                              <th>Accion</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
									$sql_log_accesos = "Select * 	
												    From SocioAutorizacion SA, LogAcceso LA 
													Where SA.IDSocioAutorizacion = LA.IDInvitacion
													And IDInvitado = '".$frm[$key]."'
													And LA.IDClub= '".SIMUser::get("club")."'
													Order By LA.IDLogAcceso DESC
													";
								
													
											
							   $query_log_accesos = $dbo->query($sql_log_accesos);
							   
                               //$r_datos =& $dbo->all( "SocioAutorizacion" , "IDInvitado = '" . $frm[$key]  ."' Order By IDSocioAutorizacion DESC");
                              while( $r = $dbo->object( $query_log_accesos ) ){ ?>
                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td><?php echo $r->TipoAutorizacion; ?></td>
                              <td><?php echo $r->FechaInicio; ?></td>
                              <td><?php echo $r->FechaFin; ?></td>
                              <td><?php 
							  if($r->Entrada == "S"):
							  	echo "Entrada";
							  else:
							  	echo "Salida";	
							  endif;
							  ?></td>
                              <td>
                              <?php
                              if($r->Entrada == "S"):
							  	echo $r->FechaIngreso;
							  else:
							  	echo $r->FechaSalida;	
							  endif;
							  ?>
                              </td>
                              <td><?php echo $dbo->getFields( "Vehiculo" , "Placa" , "IDVehiculo = '".$r->IDVehiculo."'" ); ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "NumeroDocumento" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="19"></th>
                      </tr>
              </table>   
              
              <?php endif; ?>                 
                                    
                                    
										
									