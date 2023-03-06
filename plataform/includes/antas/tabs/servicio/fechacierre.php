      <div id="ServicioCierre">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarServicioCierre";

                  if( $_GET[IDServicioCierre] )
                  {
                          $EditServicioCierre =$dbo->fetchAll("ServicioCierre"," IDServicioCierre = '".$_GET[IDServicioCierre]."' ","array");
                          $action = "ModificaServicioCierre";
                          ?>
                          <input type="hidden" name="IDServicioCierre" id="IDServicioCierre" value="<?php echo $EditServicioCierre[IDServicioCierre]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">FECHAS DE CIERRE DEL SERVICIO</th>
                  </tr>
                  <tr>
                    <td width="26%">Fecha Inicio</td>
                    <td width="74%"><input id="FechaInicio" type="text" size="10" title="Fecha Inicio" name="FechaInicio" class="input mandatory calendar" value="<?php echo $EditServicioCierre["FechaInicio"] ?>" readonly /></td>
                  </tr>
                  <tr>
                    <td>Fecha Fin</td>
                    <td><input id="FechaFin" type="text" size="10" title="Fecha Fin" name="FechaFin" class="input mandatory calendar" value="<?php echo $EditServicioCierre["FechaFin"] ?>" readonly /></td>
                  </tr>
                  <tr>
                    <td>Descripcion</td>
                    <td>
                    <textarea name="Descripcion" id="Descripcion" cols="40" rows="5"><?php echo $EditServicioCierre["Descripcion"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Agregar"> </td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
              </form>
              <br />
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="15"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Fecha Inicio</th>
                              <th>Fecha Fin</th>
                              <th>Descripcion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "ServicioCierre" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDServicioCierre=".$r->IDServicioCierre."#ServicioCierre"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><?php echo $r->FechaInicio; ?></td>
                              <td><?php echo $r->FechaFin; ?></td>
                              <td><?php echo $r->Descripcion; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioCierre&id=<?php echo $frm[ $key ];?>&IDServicioCierre=<? echo $r->IDServicioCierre ?>"><img src='images/trash.png' border='0' /></a>                                </td>
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
