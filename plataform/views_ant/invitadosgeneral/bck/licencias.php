<form class="form-horizontal formvalida" role="form" method="post" id="EditLicenciaInvitado<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
	
				  <?php
                  $action = "InsertarLicenciaInvitado";

                  if( $_GET[IDLicenciaInvitado] )
                  {
                          $EditLicenciaInvitado =$dbo->fetchAll("LicenciaInvitado"," IDLicenciaInvitado = '".$_GET[IDLicenciaInvitado]."' ","array");
                          $action = "ModificaLicenciaInvitado";
                          ?>
                          <input type="hidden" name="IDLicenciaInvitado" id="IDLicenciaInvitado" value="<?php echo $EditLicenciaInvitado[IDLicenciaInvitado]?>" />
                          <?php
                  }
                  ?>
    
    
    					
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria:  </label>
								  <div class="col-sm-8">
										  <select name="Categoria" id="Categoria" class="popup">
                                          	<option value=""></option>
                                            <option value="A1" <?php if($EditLicenciaInvitado[Categoria]=="A1") echo "selected"; ?>>A1</option>
                                            <option value="A2" <?php if($EditLicenciaInvitado[Categoria]=="A2") echo "selected"; ?>>A2</option>
                                            <option value="B1" <?php if($EditLicenciaInvitado[Categoria]=="B1") echo "selected"; ?>>B1</option>
                                            <option value="B2" <?php if($EditLicenciaInvitado[Categoria]=="B2") echo "selected"; ?>>B2</option>
                                            <option value="B3" <?php if($EditLicenciaInvitado[Categoria]=="B3") echo "selected"; ?>>B3</option>
                                            <option value="C1" <?php if($EditLicenciaInvitado[Categoria]=="C1") echo "selected"; ?>>C1</option>
                                            <option value="C2" <?php if($EditLicenciaInvitado[Categoria]=="C2") echo "selected"; ?>>C2</option>
                                            <option value="C3" <?php if($EditLicenciaInvitado[Categoria]=="C3") echo "selected"; ?>>C3</option>
                                            
                                          </select>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Vencimiento:  </label>

										<div class="col-sm-8">
										  <input type="text" id="FechaVencimiento" name="FechaVencimiento" placeholder="Fecha Vencimiento" class="col-xs-12 calendar" title="Fecha Vencimiento" value="<?php echo $EditLicenciaInvitado["FechaVencimiento"] ?>" >
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
                              <th>Categoria</th>
                              <th>Fecha Vencimiento</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_datos =& $dbo->all( "LicenciaInvitado" , "IDInvitado = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_datos ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDLicenciaInvitado=".$r->IDLicenciaInvitado?>&tabinvitado=licencias" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->Categoria; ?></td>
                              <td><?php echo $r->FechaVencimiento; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaLicenciaInvitado&id=<?php echo $frm[$key];?>&IDLicenciaInvitado=<? echo $r->IDLicenciaInvitado ?>&tabinvitado=licencias" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="14"></th>
                      </tr>
              </table>                    
                                    
                                    
										
									