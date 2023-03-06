



			Registrar Socio al evento
			<form class="form-horizontal formvalida" role="form" method="post" id="RegistraSocioEvento<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

										<div  class="form-group first ">

											<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
											  <div class="col-sm-8">
													<input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" value="" >
													<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

			                                        </div>
											</div>
										</div>


									 <div  class="form-group first ">
										 <?php
										 //Consulto los campos dinamicos
										 $r_campos =& $dbo->all( "CampoFormularioEvento" , "IDEvento = '" . $frm[$key]  ."'");
											while( $r = $dbo->object( $r_campos ) ): ?>
												 <div  class="col-xs-12 col-sm-6">
														 <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r->EtiquetaCampo; ?> </label>
														 <div class="col-sm-8">
															 	<input type="text" id="Campo<?php echo $r->IDCampoFormularioEvento; ?>" name="Campo<?php echo $r->IDCampoFormularioEvento; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" >
															</div>
												 </div>
										 <?php endwhile; ?>
			             </div>







										<div class="clearfix form-actions">
											<div class="col-xs-12 text-center">
			               		<input type="hidden" name="ID"  id="ID" value="<?php echo $EditCampoFormularioEvento[ $key ] ?>" />
			                 	<input type="hidden" name="IDEvento"  id="IDEvento" value="<?php echo $frm[ $key ] ?>" />
												<input type="hidden" name="action" id="action" value="insertasocioevento" />
												<input type="submit" class="submit" value="Guardar">
			                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club")?>" />



											</div>
										</div>




								</form>

						<br><a href="procedures/excel-evento-registro.php?IDEvento=<?php echo  $frm[$key] ; ?>"><img src="assets/img/xls.gif" >Exportar</a>
						<table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>

                              <th>Evento</th>
                              <th>Socio</th>
                              <th>Beneficiario</th>
                              <th>Valor</th>
                              <th>CodigoPago</th>
                              <th>EstadoTransaccion</th>
                              <th>CodigoRespuesta</th>
															<th>Fecha registro</th>

                              <?php
                              //Consulto los campos dinamicos
							  $r_campos =& $dbo->all( "CampoFormularioEvento" , "IDEvento = '" . $frm[$key]  ."'");
                              while( $r = $dbo->object( $r_campos ) ):
							  	  $array_campos[] = $r->IDCampoFormularioEvento;	?>
							  	  <th><?php echo $r->EtiquetaCampo; ?></th>
							  <?php endwhile; ?>
															<th>Eliminar</th>


                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
                              $r_datos =& $dbo->all( "EventoRegistro" , "IDEvento = '" . $frm[$key]  ."'");
                              while( $r = $dbo->object( $r_datos ) ): ?>
                                  <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                                          <td><?php echo $dbo->getFields( "Evento" , "Titular" , "IDEvento = '".$r->IDEvento."'" ); ?></td>
                                          <td><?php echo utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocio."'" )."".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r->IDSocio."'" ));
																					$corresoc=$dbo->getFields( "Socio" , "CorreoElectronico" , "IDSocio = '".$r->IDSocio."'" );
																					if(!empty($corresoc))
																						echo "(".$corresoc.")";
																					?></td>
                                          <td><?php echo utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocioBeneficiario."'" )."".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r->IDSocioBeneficiario."'" )); ?></td>
                                          <td><?php echo $r->Valor; ?></td>
                                          <td><?php echo $r->CodigoPago; ?></td>
                                          <td><?php echo $r->EstadoTransaccion; ?></td>
                                          <td><?php echo $r->CodigoRespuesta; ?></td>
																					<td><?php echo $r->FechaTrCr; ?></td>
                                          <?php
										  //Consulto los campos dinamicos
										  $r_campos =& $dbo->all( "EventoRegistroDatos" , "IDEventoRegistro = '" . $r->IDEventoRegistro  ."'");
										   while( $rdatos = $dbo->object( $r_campos ) ):
											  	$array_otros_datos[$rdatos->IDEventoRegistro][$rdatos->IDCampoFormularioEvento] =  $rdatos->Valor;
										 	endwhile;

										  if(count($array_campos)>0):
										  	foreach($array_campos as $id_campo):?>
											  		<td>&nbsp;<?php echo $array_otros_datos[$r->IDEventoRegistro][$id_campo]; ?></td>
											<?php endforeach;
										  endif; ?>

													<td align="center"><a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaRegistro&id=<?php echo $r->IDEvento;?>&IDEventoRegistro=<? echo $r->IDEventoRegistro ?>&tabevento=invitaciones" ></a></td>

											  </tr>
							<?php endwhile; ?>

                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>
