<form class="form-horizontal formvalida" role="form" method="post" id="EditPredio<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
	
				  <?php
                  $action = "InsertarPredio";

                  if( $_GET[IDPredio] )
                  {
                          $EditPredio =$dbo->fetchAll("Predio"," IDPredio = '".$_GET[IDPredio]."' ","array");
                          $action = "ModificaPredio";
                          ?>
                          <input type="hidden" name="IDPredio" id="IDPredio" value="<?php echo $EditPredio[IDPredio]?>" />
                          <?php
                  }
                  ?>
    
    
    					
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Predio:  </label>

										<div class="col-sm-8">
										  <input type="text" id="Predio" name="Predio" placeholder="Predio" class="col-xs-12" title="Predio" value="<?php echo $EditPredio["Predio"] ?>" >
										</div>
								</div>
									
							</div>

							
                           
								
									
						
                            
                            

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
                                     <input type="hidden" name="IDSocio"  id="IDSocio" value="<?php echo $frm[ $key ] ?>" />
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
                              <th>Predio</th>                              
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_datos =& $dbo->all( "Predio" , "IDSocio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_datos ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDPredio=".$r->IDPredio?>&tabsocio=predios" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->Predio; ?></td>                              
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaPredio&id=<?php echo $frm[$key];?>&IDPredio=<? echo $r->IDPredio ?>&tabsociodo=predios" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="14"></th>
                      </tr>
              </table>                    
                                    
                                    
										
									