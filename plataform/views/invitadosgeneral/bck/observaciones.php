<form class="form-horizontal formvalida" role="form" method="post" id="EditObservacion<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
	
				  <?php
                  $action = "InsertarObservacion";

                  if( $_GET[IDObservacionInvitado] )
                  {
                          $EditObservacion =$dbo->fetchAll("ObservacionInvitado"," IDObservacionInvitado = '".$_GET[IDObservacionInvitado]."' ","array");
                          $action = "ModificaObservacion";
                          ?>
                          <input type="hidden" name="IDObservacionInvitado" id="IDObservacionInvitado" value="<?php echo $EditObservacion[IDObservacionInvitado]?>" />
                          <?php
                  }
                  ?>
    
    
    					
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion:  </label>
								  <div class="col-sm-8">
										  <textarea name="Observacion" id="Observacion" class="form-control"><?php echo $EditObservacion["Observacion"]; ?></textarea>                                          
								  </div>
								</div>
								
									
  </div>

							
                            <div  class="form-group first ">
    	                        <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio Bloqueo:  </label>

										<div class="col-sm-8">
										  <input type="text" id="FechaInicioBloqueo" name="FechaInicioBloqueo" placeholder="Fecha Inicio Bloqueo" class="col-xs-12 calendar" title="Fecha Inicio Bloqueo" value="<?php echo $EditObservacion["FechaInicioBloqueo"] ?>" >
										</div>
								</div>
                            <div  class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin Bloqueo:  </label>
								  <div class="col-sm-8">
										  <input type="text" id="FechaFinBloqueo" name="FechaFinBloqueo" placeholder="Fecha Fin Bloqueo" class="col-xs-12 calendar" title="Fecha Fin Bloqueo" value="<?php echo $EditObservacion["FechaFinBloqueo"] ?>" >
										</div>
								</div>
								 
  </div>
                            
                            <div  class="form-group first ">
    	                        <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio Bloqueo:  </label>

										<div class="col-sm-8">
										  <input id="HoraInicioBloqueo" type="time" size="10" title="Hora Inicio" name="HoraInicioBloqueo" class="input" value="<?php echo $EditObservacion["HoraInicioBloqueo"] ?>"  />
										</div>
								</div>
                            <div  class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin Bloqueo:  </label>
								  <div class="col-sm-8">
								    <input id="HoraFinBloqueo" type="time" size="10" title="Hora Fin" name="HoraFinBloqueo" class="input" value="<?php echo $EditObservacion["HoraFinBloqueo"] ?>"  />
								  </div>
							  </div>
								 
							</div>

								
									
							
                            
                            

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
                                     <input type="hidden" name="IDInvitado"  id="IDInvitado" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<input type="submit" class="submit" value="Guardar">
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                 			 <input type="hidden" name="action" id="action" value="<?php echo $action?>" />

									
								</div>
							</div>
                            
                          
                  

					</form>







              

          
              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Observacion</th>
                              <th>Fecha Inicio Bloqueo</th>
                              <th>Fecha Fin bloqueo</th>
                              <th>Creado Por</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_datos =& $dbo->all( "ObservacionInvitado" , "IDInvitado = '" . $frm[$key]  ."' Order By FechaTrCr DESC");

                              while( $r = $dbo->object( $r_datos ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                              	<?php if(SIMUser::get("IDPerfil") <= 1): ?>
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDObservacionInvitado=".$r->IDObservacionInvitado?>&tabinvitado=observaciones" class="ace-icon glyphicon glyphicon-pencil"></a>                                
                                  <?php endif; ?>     
                                      
                                      </td>
                              <td><?php echo $r->Observacion; ?></td>
                              <td><?php echo $r->FechaInicioBloqueo . " " . $r->HoraInicioBloqueo; ?></td>
                              <td><?php echo $r->FechaFinBloqueo . " " . $r->HoraFinBloqueo; ?></td>
                              <td><?php echo $r->UsuarioTrEd; ?></td>
                              <td align="center" width="64">
                                      <?php if(SIMUser::get("IDPerfil") <= 1): ?>
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaObservacion&id=<?php echo $frm[$key];?>&IDObservacionInvitado=<? echo $r->IDObservacionInvitado ?>&tabinvitado=observaciones" ></a>                                
                                      <?php endif; ?>
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
                                    
                                    
										
									