      <div id="GaleriaVideo">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarVideoGaleria";

                  if( $_GET[IDGaleriaVideo] )
                  {
                          $EditNoticia =$dbo->fetchAll("GaleriaVideo"," IDGaleriaVideo = '".$_GET[IDGaleriaVideo]."' ","array");
                          $action = "ModificaGaleriaVideo";
                          ?>
                          <input type="hidden" name="IDGaleriaVideo" id="IDGaleriaVideo" value="<?php echo $EditNoticia[IDGaleriaVideo]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">Galeria Noticia</th>
                  </tr>
                  <tr>
                    <td>Nombre</td>
                    <td><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditNoticia["Nombre"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Descripcion</td>
                    <td>
                    <textarea name="Descripcion" id="Descripcion" cols="40" rows="5"><?php echo $EditNoticia["Descripcion"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                          <td> Imagen </td>
                          <td>
                          <?php
                          if($EditNoticia["NombreFile"])
                          {
                                  ?>
                                  <a href="<?php echo GALERIA_ROOT.$EditNoticia["NombreFile"]?>"><?php echo $EditNoticia["NombreFile"] ?></a>
                                  <a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&action=DelDocNot&id=".$frm[ $key ]."&idd=" .$EditNoticia["IDGaleria"]?>"><img src='images/trash.png' border='0'></a>
                          <?php
                          }
                          else
                          {
                          ?>
                          <input type="file" name="NombreFile" id="NombreFile" class="popup" title="NombreFile">
                          <?php
                          }
                          ?>                            </td>
                  </tr>
                  <tr>
                    <td> URL Video</td>
                    <td><input id="URLVideo" type="text" size="25" title="Url" name="URLVideo" class="input mandatory" value="<?php echo $EditNoticia["URLVideo"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Orden</td>
                    <td><input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input mandatory" value="<?php echo $EditNoticia["Orden"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Publicar</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditNoticia["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Enviar"> </td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDGaleria" id="IDGaleria" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
              </form>
              <br />
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="15"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Galeria</th>
                              <th>Url</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "GaleriaVideo" , "IDGaleria = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDGaleriaVideo=".$r->IDGaleriaVideo."#GaleriaVideo"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><img src="<?php echo GALERIA_ROOT.$r->File?>" width="100" border="0"></td>
                              <td><?php echo $r->URLVideo; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaGaleriaVideo&id=<?php echo $frm[ $key ];?>&IDGaleriaVideo=<? echo $r->IDGaleriaVideo ?>"><img src='images/trash.png' border='0' /></a>                                </td>
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
