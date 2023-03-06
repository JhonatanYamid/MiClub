      <div id="FechaCierre">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarFechaCierre";

                  if( $_GET[IDClubFechaCierre] )
                  {
                          $EditFechaCierre =$dbo->fetchAll("ClubFechaCierre"," IDClubFechaCierre = '".$_GET[IDClubFechaCierre]."' ","array");
                          $action = "ModificaFechaCierre";
                          ?>
                          <input type="hidden" name="IDClubFechaCierre" id="IDClubFechaCierre" value="<?php echo $EditFechaCierre[IDClubFechaCierre]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">FECHAS DE CIERRE</th>
                  </tr>
                  <tr>
                    <td width="26%">Fecha</td>
                    <td width="74%"><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input mandatory calendar" value="<?php echo $EditFechaCierre["Fecha"] ?>" readonly /></td>
                  </tr>
                  <tr>
                    <td>Motivo</td>
                    <td>
                    <textarea name="Motivo" id="Motivo" cols="40" rows="5"><?php echo $EditFechaCierre["Motivo"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Enviar"> </td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
              </form>
              <br />
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="14"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Fecha</th>
                              <th>Motivo</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php	
                              $r_documento =& $dbo->all( "ClubFechaCierre" , "IDClub = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDClubFechaCierre=".$r->IDClubFechaCierre."#FechaCierre"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><?php echo $r->Fecha; ?></td>
                              <td><?php echo $r->Motivo; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaFechaCierre&id=<?php echo $frm[ $key ];?>&IDClubFechaCierre=<? echo $r->IDClubFechaCierre ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="14"></th>
                      </tr>
              </table>



</div>
