      <div id="ServicioCampos">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarServicioCampo";

                  if( $_GET[IDServicioCampo] )
                  {
                          $EditServicioCampo =$dbo->fetchAll("ServicioCampo"," IDServicioCampo = '".$_GET[IDServicioCampo]."' ","array");
                          $action = "ModificaServicioCampo";
                          ?>
                          <input type="hidden" name="IDServicioCampo" id="IDServicioCampo" value="<?php echo $EditServicioCampo[IDServicioCampo]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">CAMPOS RESERVA</th>
                  </tr>
                  <tr>
                    <td width="26%">Nombre Campo</td>
                    <td width="74%"><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditServicioCampo["Nombre"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Descripcion</td>
                    <td>
                    <textarea name="Descripcion" id="Descripcion" cols="40" rows="5"><?php echo $EditServicioCampo["Descripcion"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td>Tipo</td>
                    <td>
                    <select  name="Tipo" id="Tipo" class="mandatory" title="Tipo Campo"  >
                    	<option value="">Seleccione</option>
                        <option value="Texto" <?php if ($EditServicioCampo["Tipo"]=="Texto" ) echo "selected"; ?>>Texto</option>
                        <option value="Radio" <?php if ($EditServicioCampo["Tipo"]=="Radio" ) echo "selected"; ?>>Radio Button</option>
                        <option value="Check" <?php if ($EditServicioCampo["Tipo"]=="Check" ) echo "selected"; ?>>Check</option>
                        <option value="Lista" <?php if ($EditServicioCampo["Tipo"]=="Lista" ) echo "selected"; ?>>Lista</option>
                    </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Valores (separados por coma, solo aplica para radio, check y lista)</td>
                    <td><textarea name="Valor" id="Valor" cols="40" rows="5"><?php echo $EditServicioCampo["Valor"] ?></textarea></td>
                  </tr>
                  <tr>
                    <td>Publicar</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioCampo["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Enviar"> </td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
              </form>
              <br />
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="16"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Campo</th>
                              <th>Tipo</th>
                              <th>Valores</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "ServicioCampo" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDServicioCampo=".$r->IDServicioCampo."#ServicioCampos"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Tipo; ?></td>
                              <td><?php echo $r->Valor; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioCampo&id=<?php echo $frm[ $key ];?>&IDServicioCampo=<? echo $r->IDServicioCampo ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>



</div>
