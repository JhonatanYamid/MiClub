      <div id="NucleoFamiliar">
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="15"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>Email</th>
                              <th>Tipo</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
					  		
							// Si es socio busco el nucleo familiar
					  		if (empty($frm["AccionPadre"])):
								$condicion_nucleo = " and AccionPadre = '" . $frm["Accion"]  ."'";
							else:
							// Si es beneficiario busco el nucleo familiar y el socio padre
								$condicion_nucleo = " and (Accion = '" . $frm["AccionPadre"]  ."' or AccionPadre = '".$frm["AccionPadre"]."') and IDSocio <> '".$frm["IDSocio"]."'";
							endif;
					  			
	                          $r_socio_nucleo =& $dbo->all( "Socio" , "IDClub = '".$_SESSION[IDClub]."' " . $condicion_nucleo);

                              while( $r = $dbo->object( $r_socio_nucleo ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->IDSocio?>'><img src='images/edit.png' border='0'></a><a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDServicioCampo=".$r->IDServicioCampo."#ServicioCampos"?>"></a>                                </td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Email; ?></td>
                              <td><?php 
							  if ($r->AccionPadre!="")
							  	echo "Beneficiario";
							  else
							  	echo "Socio";	
							  
							  ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioCampo&id=<?php echo $frm[ $key ];?>&IDServicioCampo=<? echo $r->IDServicioCampo ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="15"></th>
                      </tr>
              </table>



</div>
