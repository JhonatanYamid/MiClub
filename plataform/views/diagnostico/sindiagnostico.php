<form name="frmexportapqr" id="frmexportapqr" method="post" enctype="multipart/form-data" action="procedures/excel-sindiagnostico.php">
<table>
	<tr>
			<td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>" ></td>
			<td><input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d")  ?>" ></td>
			<td>
				<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
				<input type="hidden" name="IDPerfil" id="IDPerfil" value="<?php echo SIMUser::get("IDPerfil"); ?>">
				<input type="hidden" name="IDDiagnostico" id="IDDiagnostico" value="<?php echo $frm[$key]; ?>">
				<input class="btn btn-info" type="submit" name="exppqr" id="exppqr" value="Exportar" >
				<!-- <a href="procedures/excel-pqr.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDUsuario=<?php echo SIMUser::get("IDUsuario"); ?>&IDPerfil=<?php echo SIMUser::get("IDPerfil"); ?>"><img src="assets/img/xls.gif" >Exportar</a>-->
			</td>
	<tr>
</table>
</form>


              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>

                              <th>Encuesta</th>
                              <th>Usuario</th>

                              <?php
                              //Consulto los campos dinamicos
							  //$r_campos =& $dbo->all( "PreguntaDiagnostico" , "IDDiagnostico = '" . $frm[$key]  ."' Order by IDPreguntaDiagnostico");
                              while( $r = $dbo->object( $r_campos ) ):
							  	  $array_preguntas[] = $r->IDPreguntaDiagnostico;	?>
							  	  <th><?php echo $r->EtiquetaCampo; ?></th>
							  <?php endwhile; ?>
						  	<th>Fecha</th>


                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
															//$datos_encuesta=$dbo->fetchAll( "Diagnostico", " IDDiagnostico = '" . $frm[$key] . "' ", "array" );
                              //$r_datos = $dbo->query("Select IDSocio,P.IDPreguntaDiagnostico From PreguntaDiagnostico P,DiagnosticoRespuesta ER Where ER.IDPreguntaDiagnostico=P.IDPreguntaDiagnostico and P.IDDiagnostico = '".$frm[$key]."' Group by IDSocio Limit 10");
                              while( $r = $dbo->object( $r_datos ) ):
						  		unset($array_respuesta_socio);
						  		$Fecha="";
						  ?>
                                  <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                                          <td><?php echo utf8_encode($dbo->getFields( "Diagnostico" , "Nombre" , "IDDiagnostico = '".$frm[$key]."'" )); ?></td>
                                          <td><?php
																					if($datos_encuesta["DirigidoA"]=="E"){
																						echo utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$r->IDSocio."'" ));
																					}
																					else{
																						echo utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocio."'" )." ".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r->IDSocio."'" ));
																					}
																					?>
																				</td>

                                          <?php
									  	//$sql_repuesta_socio="Select * From DiagnosticoRespuesta Where IDDiagnostico = '".$frm[$key]."' and IDSocio = '".$r->IDSocio."' Limit 10";
									  	//$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
									  	while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
									  		$array_respuesta_socio[][$row_respuesta["IDPreguntaDiagnostico"]]=$row_respuesta["Valor"];
												$Fecha=$row_respuesta["FechaTrCr"];
									    endwhile;
											if(count($array_preguntas)>0):
										  	foreach($array_preguntas as $id_pregunta):
													$Fecha="";
													?>
											  		<td>
															<?php
															//$sql_repuesta_socio="Select * From DiagnosticoRespuesta Where IDDiagnostico = '".$frm[$key]."' and IDSocio = '".$r->IDSocio."' and IDPreguntaDiagnostico = '".$id_pregunta."' Limit 10";
													  	$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
													  	while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)){
																echo $row_respuesta["Valor"] . "<br>";
																$Fecha.=$row_respuesta["FechaTrCr"]."<br>";
															}
															?>


															<?php //echo $array_respuesta_socio[$id_pregunta]; ?></td>
											<?php endforeach;
										  endif; ?>
									  <td><?php echo $Fecha; ?></td>

											  </tr>
							<?php endwhile; ?>
                      </tbody>

              </table>
