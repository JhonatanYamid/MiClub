<form name="frmnotifica" class="form-horizontal formvalida" role="form" method="post" id="EditNotificacionLocal<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

				  <?php
                  $action = "InsertarNotificacionLocal";
                  if( $_GET["IDNotificacionLocal"] )
                  {
													$EditNotificacionLocal =$dbo->fetchAll("NotificacionLocal"," IDNotificacionLocal = '".$_GET["IDNotificacionLocal"]."' ","array");
                          $action = "ModificaNotificacionLocal";
                          ?>
                          <input type="hidden" name="IDNotificacionLocal" id="IDNotificacionLocal" value="<?php echo $EditNotificacionLocal[IDNotificacionLocal]?>" />
                          <?php
                  }
                  ?>



							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo </label>
								  <div class="col-sm-8">
								    <input type="text" id="Titulo" name="Titulo" placeholder="Titulo" class="col-xs-12 mandatory" title="Titulo" value="<?php echo $EditNotificacionLocal["Titulo"]; ?>" >

                  </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje max 70 caracteres </label>

										<div class="col-sm-8">
											<textarea id="Mensaje" name="Mensaje" cols="10" rows="5" class="col-xs-12 mandatory"  onKeyPress="valida_longitud()" title="Mensaje"><?php echo $EditNotificacionLocal["Mensaje"]; ?></textarea>
											<input type="text" name="numerocaracter" id="numerocaracter" value="0" readonly>
									</div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio </label>

										<div class="col-sm-8">
											<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $EditNotificacionLocal["FechaInicio"] ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

										<div class="col-sm-8">
											<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $EditNotificacionLocal["FechaFin"] ?>" >
										</div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio </label>

										<div class="col-sm-8">
											<input type="time" id="HoraInicio" name="HoraInicio" placeholder="Hora Inicio" class="col-xs-12" title="Hora Inicio" value="<?php echo $EditNotificacionLocal["HoraInicio"] ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin </label>

										<div class="col-sm-8">
											<input type="time" id="HoraFin" name="HoraFin" placeholder="Hora Fin" class="col-xs-12" title="fecha fin" value="<?php echo $EditNotificacionLocal["HoraFin"] ?>" >
										</div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo los días (opcional) </label>
								  <div class="col-sm-8">
										<?php
												$array_dias=explode("|",$EditNotificacionLocal["Dias"]);
												array_pop($array_dias);
												foreach($Dia_array as $id_dia => $dia):  ?>
															<input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
											 <?php endforeach; ?>
                  </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Recordar cada: </label>

										<div class="col-sm-8">
											<input type="number" id="Periodicidad" name="Periodicidad" placeholder="Periodicidad" class="col-xs-12 mandatory" title="Periodicidad" value="<?php echo $EditNotificacionLocal["Periodicidad"]; ?>" >minutos
									</div>
								</div>

							</div>


								<div  class="form-group first ">
									<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>

													<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditNotificacionLocal["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></div>
									</div>
									</div>


							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                  <input type="hidden" name="ID"  id="ID" value="<?php echo $EditNotificacionLocal[ $key ] ?>" />
									<input type="hidden" name="CantidadOpciones"  id="CantidadOpciones" value="<?php echo $CantidadOpciones ?>" />
                	<input type="hidden" name="IDDiagnostico"  id="IDDiagnostico" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<input type="submit" class="submit" value="Guardar">
                                      <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ "IDClub" ]?>" />
                 			 <input type="hidden" name="action" id="action" value="<?php echo $action?>" />


								</div>
							</div>




					</form>










              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Titulo</th>
                              <th>Mensaje</th>
                              <th>FechaInicio</th>
                              <th>FechaFin</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
															$r_documento =& $dbo->all( "NotificacionLocal" , "IDClub = '" . $frm["IDClub"]  ."' and IDModulo ='99' and IDDetalle = '".$frm[$key]."'");
                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDNotificacionLocal=".$r->IDNotificacionLocal?>&tabencuesta=notificacionlocal" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->Titulo; ?></td>
                              <td><?php echo $r->Mensaje; ?></td>
                              <td><?php echo $r->FechaInicio; ?></td>
                              <td><?php echo $r->FechaFin; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                            	<a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaNotificacionLocal&id=<?php echo $frm[$key];?>&IDNotificacionLocal=<? echo $r->IDNotificacionLocal ?>&tabNotificacionLocal=notificacionlocal" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>

							<script language="javascript">
							        contenido_textarea = ""
							        num_caracteres_permitidos = 69;

							        function valida_longitud(){

							           num_caracteres = document.forms["EditNotificacionLocal<?php echo $script; ?>"].Mensaje.value.length
												 if (num_caracteres > num_caracteres_permitidos){
							              document.forms["EditNotificacionLocal<?php echo $script; ?>"].Mensaje.value = contenido_textarea;
							           }else{
							              contenido_textarea = document.forms["EditNotificacionLocal<?php echo $script; ?>"].Mensaje.value;
							           }

									     document.forms["EditNotificacionLocal<?php echo $script; ?>"].numerocaracter.value = num_caracteres;
							        }


							    </script>
