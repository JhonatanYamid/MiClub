      <div id="ServicioReserva">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarServicioReserva";

                  if( $_GET[IDReservaGeneral] )
                  {
                          $EditServicioReserva =$dbo->fetchAll("ReservaGeneral"," IDReservaGeneral = '".$_GET[IDReservaGeneral]."' ","array");
                          $action = "ModificaServicioReserva";
                          ?>
                          <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $EditServicioReserva[IDReservaGeneral]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">ELEMENTOS SERVICIO</th>
                  </tr>
                  <tr>
                    <td>Socio</td>
                    <td>
					<select name="IDSocio" id="IDSocio" class="popup mandatory" title="Socio">
                    	<option value="">[Seleccione]</option>
					<?php 
						$qry_socio = $dbo->all( "Socio", " IDClub = '".$_SESSION[IDClub]."'" );
						while ( $r_socio = $dbo->object( $qry_socio ) ): ?>
							<option value="<?php echo $r_socio->IDSocio?>" <?php if($r_socio->IDSocio==$EditServicioReserva[IDSocio]) echo "selected"; ?>> <?php echo $r_socio->Nombre . " " . $r_socio->Apellido; ?></option>
                        <?php	
						endwhile;
					?>
                    </select>
                    </td>
                  </tr>
                  <tr>
                    <td width="26%"> Elemento</td>
                    <td width="74%"><select name="IDServicioElemento" id="IDServicioElemento" class="popup mandatory" title="Elemento">
                      <option value="">[Seleccione]</option>
                      <?php 
						$qry_elemento = $dbo->all( "ServicioElemento", " IDServicio = '".$frm[ $key ]."'" );
						while ( $r_elemento = $dbo->object( $qry_elemento ) ): ?>
                      <option value="<?php echo $r_elemento->IDServicioElemento?>" <?php if($r_elemento->IDServicioElemento==$EditServicioReserva[IDServicioElemento]) echo "selected"; ?>> <?php echo $r_elemento->Nombre; ?> </option>
                      <?php	
						endwhile;
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Fecha</td>
                    <td><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input mandatory calendar" value="<?php echo $EditServicioReserva["Fecha"] ?>" readonly /></td>
                  </tr>
                  <tr>
                    <td>Hora</td>
                    <td><input type="time" name="Hora" id="Hora" class="input" title="Hora" value="<?php echo $EditServicioReserva[Hora] ?>"></td>
                  </tr>
                  <tr>
                    <td>Tee (Solo para golf)</td>
                    <td>
                    	<select name="Tee" id="Tee" class="popup">
                        	<option value="">[Seleccione]</option>
                            <option value="Tee1" <?php if($EditServicioReserva[Tee]=="Tee1") echo "selected";  ?>>Tee 1</option>
                            <option value="Tee10" <?php if($EditServicioReserva[Tee]=="Tee10") echo "selected";  ?>>Tee 10</option>
                        </select>
                    </td>
                  </tr>
                  
                  <tr>
                    <td>Estado</td>
                    <td><?php echo SIMHTML::formPopUp( "EstadoReserva" , "Nombre" , "Nombre" , "IDEstadoReserva" , $EditServicioReserva["IDEstadoReserva"] , "[Seleccione el estado]" , "popup" , "title = \"Estado\"" )?></td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Enviar"> </td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
              </form>
              
            <?php if(!empty($_GET[IDReservaGeneral])): ?>  
              
              <br />
              
              
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="13">Invitados</th>
                      </tr>
                      <tr>
                              <th>Socio</th>
                              <th>Nombre Exterior</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
                              $r_invitado =& $dbo->all( "ReservaGeneralInvitado" , "IDReservaGeneral = '" . $_GET[IDReservaGeneral]  ."'");

                              while( $row_invitado = $dbo->object( $r_invitado ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row_invitado->IDSocio . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row_invitado->IDSocio . "'" ); ?></td>
                              <td><?php echo $row_invitado->Nombre; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaInvitadoReserva&id=<?php echo $frm[ $key ];?>&IDReservaGeneralInvitado=<? echo $row_invitado->IDReservaGeneralInvitado ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="13"></th>
                      </tr>
              </table>
			<br>	
             <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="12">Campos Personalizados</th>
                      </tr>
                      <tr>
                              <th>Campo</th>
                              <th>Valor</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_campo =& $dbo->all( "ReservaGeneralCampo" , "IDReservaGeneral = '" . $_GET[IDReservaGeneral] ."'");

                              while( $row_campo = $dbo->object( $r_campo ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td><?php echo $dbo->getFields( "ServicioCampo" , "Nombre" , "IDServicioCampo = '" . $row_campo->IDServicioCampo . "'" ); ?></td>
                              <td><?php echo $row_campo->Valor; ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="12"></th>
                      </tr>
              </table> 
            <?php endif; ?>  
              
  <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="17"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Socio</th>
                              <th>Elemento</th>
                              <th>Fecha</th>
                              <th>Hora</th>
                              <th>Estado</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "ReservaGeneral" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDReservaGeneral=".$r->IDReservaGeneral."#ServicioReserva"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $r->IDSocio . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $r->IDSocio . "'" ); ?></td>
                              <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $r->IDServicioElemento . "'" ); ?></td>
                              <td><?php echo $r->Fecha; ?></td>
                              <td><?php echo $r->Hora; ?></td>
                              <td><?php echo $dbo->getFields( "EstadoReserva" , "Nombre" , "IDEstadoReserva = '" . $r->IDEstadoReserva . "'" ); ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioReserva&id=<?php echo $frm[ $key ];?>&IDReservaGeneral=<? echo $r->IDReservaGeneral ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="17"></th>
                      </tr>
              </table>
              
              <br><br>
              



</div>
