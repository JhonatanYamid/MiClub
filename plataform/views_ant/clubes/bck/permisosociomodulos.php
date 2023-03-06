<form class="form-horizontal formvalida" role="form" method="post" id="EditPermisoSocioModulo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

				  <?php
                  $action = "InsertarPermisoSocioModulo";

                  if( $_GET["IDPermisoSocioModulo"] )
                  {
                          $EditPermisoSocioModulo =$dbo->fetchAll("PermisoSocioModulo"," IDPermisoSocioModulo = '".$_GET["IDPermisoSocioModulo"]."' ","array");
                          $action = "ModificaPermisoSocioModulo";
                          ?>
                          <input type="hidden" name="IDPermisoSocioModulo" id="IDPermisoSocioModulo" value="<?php echo $EditPermisoSocioModulo["IDPermisoSocioModulo"]?>" />
                          <?php
                  }
                  ?>



							<div  class="form-group first ">


								<div  class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario: </label>

								<div class="col-sm-8">
														 <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="doc,nombre,apellido" class="col-xs-12 autocomplete-ajax-socios" title="número" >
															 <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
								<br>
														 <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8"  multiple >
														 <?php
														 	$item=1;
															$array_invitados=explode("|||",$EditPermisoSocioModulo["InvitadoSeleccion"]);
														 foreach($array_invitados as $id_invitado => $datos_invitado):
															 	if(!empty($datos_invitado)){
																  $array_datos_invitados=explode("-",$datos_invitado);
																	$item--;
																	$IDSocioInvitacion=$array_datos_invitados[1];
																	if($IDSocioInvitacion>0):
																	$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocioInvitacion."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocioInvitacion."'" ));
																	?>
																	<option value="<?php echo "socio-".$IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
																	<?php
																	endif;
																}
														 endforeach;
								?>
													 </select>

													 <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">

								</div>
								</div>
							</div>






              <div  class="form-group first ">



              <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modulos  </label>

										<div class="col-sm-8">

                                        <div style="width:300px;">

                                         <select multiple class="chosen-select form-control" name="IDModulo[]" id="IDModulo" data-placeholder="Selecciones valores...">
                                                            			<?php
																		$r_valor_tabla = $dbo->all( "ClubModulo" , "IDClub = '".$frm[ $key ]."' and Activo = 'S' Order By Titulo");

																		$valores_guardados = $EditPermisoSocioModulo["IDModulo"] ;
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;


					                        while( $r_valor = $dbo->object( $r_valor_tabla ) ){


																			if(empty(trim($r_valor->Titulo))){
																					$NombreModulo = $dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '" . $r_valor->IDModulo . "'" );
																			}
																			else{
																					$NombreModulo = $r_valor->TituloLateral;
																			}

																		  if(empty($valores_guardados)):
																				$seleccionar = "";
																			elseif(in_array($r_valor->IDModulo,$array_valores_guardados)):
																				$seleccionar = "selected";
																			else:
																				$seleccionar = "";
																			endif;

																		  ?>
																		  <option value="<?php echo $r_valor->IDModulo ?>" <?php echo $seleccionar; ?>><?php echo $NombreModulo; ?></option>

																   <? }	?>
															</select>


										</div>
								</div>

							</div>


                            <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicios Reservar  </label>

										<div class="col-sm-8">

                                        <div style="width:300px;">

                                         <select multiple class="chosen-select form-control" name="IDServicioMaestro[]" id="IDServicioMaestro" data-placeholder="Selecciones valores...">
                                                            			<?php
																		unset($array_valores_guardados);
																		$r_valor_tabla = $dbo->all( "ServicioClub" , "IDClub = '".$frm[ $key ]."' and Activo='S' Order By TituloServicio");
																		$valores_guardados = $EditPermisoSocioModulo[IDServicioMaestro] ;
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;

					                          while( $r_valor = $dbo->object( $r_valor_tabla ) ){

																			if(empty($r_valor->TituloServicio)){
																					$NombreServicio = utf8_encode($dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $r_valor->IDServicioMaestro . "'" ));
																			}
																			else{
																				$NombreServicio = $r_valor->TituloServicio;
																			}


																		  if(empty($valores_guardados)):
																				$seleccionar = "";
																			elseif(in_array($r_valor->IDServicioMaestro,$array_valores_guardados)):
																				$seleccionar = "selected";
																			else:
																				$seleccionar = "";
																			endif;

																		  ?>
																		  <option value="<?php echo $r_valor->IDServicioMaestro ?>" <?php echo $seleccionar; ?>><?php echo $NombreServicio; ?></option>

																   <? }	?>
															</select>


										</div>
								</div>

							</div>



                            </div>







							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
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
                              <th>Usuario</th>
                              <th>Modulos</th>
                              <th>Servicios</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "PermisoSocioModulo" , "IDClub = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDPermisoSocioModulo=".$r->IDPermisoSocioModulo?>&tabclub=permisosociomodulo" class="ace-icon glyphicon glyphicon-pencil"></a>                        </td>
                              <td>
																<?php
																$array_invitados=explode("|||",$r->InvitadoSeleccion);
															 foreach($array_invitados as $id_invitado => $datos_invitado):
																 	  $array_datos_invitados=explode("-",$datos_invitado);
																		$IDSocioInvitacion=$array_datos_invitados[1];
																		if($IDSocioInvitacion>0):
																			$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocioInvitacion."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocioInvitacion."'" ));
																			echo $nombre_socio . "<br>";
																		?>
																		<?php
																		endif;
															 endforeach;

																?>
															</td>
                              <td><?php
															$array_id_modulo=explode("|",$r->IDModulo);
															$nombre_modulo="";
															if(count($array_id_modulo)>0){
																		foreach($array_id_modulo as $id_club_modulo){
																				$nombre_modulo = $dbo->getFields( "ClubModulo" , "Titulo" , "IDModulo = '" . $id_club_modulo . "' and IDClub = '".$r->IDClub."'" );
																				if(empty(trim($nombre_modulo))){
																						$nombre_modulo = $dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '" . $id_club_modulo . "'" );
																				}
																				echo utf8_encode($nombre_modulo)."<br>";
																		}
															}

															?></td>
                              <td>

																<?php
																$array_id_servicio=explode("|",$r->IDServicioMaestro);
																$nombre_servicio="";
																if(count($array_id_servicio)>0){
																			foreach($array_id_servicio as $id_servicio){
																					$nombre_servicio = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDServicioMaestro = '" . $id_servicio . "' and IDClub = '".$frm[$key]."'" );
																					if(empty($nombre_servicio)){
																							$nombre_servicio = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio . "'" );
																					}
																					echo utf8_encode($nombre_servicio)."<br>";
																			}
																}

																?>


															</td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaPermisoSocioModulo&id=<?php echo $frm[$key];?>&IDPermisoSocioModulo=<? echo $r->IDPermisoSocioModulo ?>&tabclub=permisosociomodulo" ></a>    </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>
