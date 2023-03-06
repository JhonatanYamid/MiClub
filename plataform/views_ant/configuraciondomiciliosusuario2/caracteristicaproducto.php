      <div id="CaracteristicaProducto">
	<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarCaracteristicaProducto";

                  if( $_GET[IDCaracteristicaProducto] )
                  {
                          $EditCaracteristicaProducto =$dbo->fetchAll("CaracteristicaProducto"," IDCaracteristicaProducto = '".$_GET[IDCaracteristicaProducto]."' ","array");
                          $action = "ModificaCaracteristicaProducto";
                          ?>
                          <input type="hidden" name="IDCaracteristicaProducto" id="IDCaracteristicaProducto" value="<?php echo $EditCaracteristicaProducto[IDCaracteristicaProducto]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">

                    <tr>
                      <td width="26%"><?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </td>
                      <td width="74%">
                        <select name="IDPropiedadProducto" id="IDPropiedadProducto" class="input mandatory" required>
                          <option value=""><?= SIMUtil::get_traduccion('', '', 'Seleccione', LANGSESSION); ?></option>
                          <?php $sql="SELECT IDPropiedadProducto, Nombre
                                      FROM PropiedadProducto
                                      WHERE IDClub = '".$frm["IDClub"]."' and Version= 2  ";
                                $r_prop=$dbo->query($sql);
                                while ($row_prop = $dbo->fetchArray($r_prop)) { ?>
                                  <option value="<?php echo $row_prop["IDPropiedadProducto"] ?>" <?php if($row_prop["IDPropiedadProducto"]==$EditCaracteristicaProducto["IDPropiedadProducto"]) echo "selected"; ?> ><?php echo $row_prop["Nombre"] ?></option>
                                <?php }  ?>
                        </select>
                      </td>
                  </tr>

                  <tr>
                    <td width="26%"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </td>
                    <td width="74%">
                      <input id="Nombre" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" name="Nombre" class="input mandatory" value="<?php echo $EditCaracteristicaProducto["Nombre"] ?>" />
                    </td>
                </tr>

                <tr>
                  <td width="26%"><?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?> </td>
                  <td width="74%">
                    <input id="Valor" type="number" size="25" title="<?= SIMUtil::get_traduccion('', '', 'valor', LANGSESSION); ?>" name="Valor" class="input" value="<?php echo $EditCaracteristicaProducto["Valor"] ?>" />
                  </td>
              </tr>
              <tr>
                  <td width="26%"><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </td>
                  <td width="74%">
                    <input id="Orden" type="number" size="25" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" name="Orden" class="input" value="<?php echo $EditCaracteristicaProducto["Orden"] ?>" />
                  </td>
                </tr>

                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditCaracteristicaProducto["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDConfiguracionDomicilio" id="IDConfiguracionDomicilio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm["IDClub"]?>" />
                  <input type="hidden" name="Version" id="Version" value="2" />

                </td>
                <td valign="top">


              </td>
            </tr>
        </table>

        </td>
        </tr>
        <tr>
        	<td align="center"><input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?>"></td>
        </tr>

        </table>
</form>


              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                      <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
                      <th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
                      <th><?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?></th>
                      <th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
                        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $sql_carac="SELECT CP.*,PP.Nombre as Categoria
                                          FROM CaracteristicaProducto CP, PropiedadProducto PP
                                          WHERE CP.IDPropiedadProducto = PP.IDPropiedadProducto
                                          And CP.IDConfiguracionDomicilio = '" . $frm[$key]  ."' and PP.Version=2";
                              $r_carac=$dbo->query($sql_carac);
                              while( $r = $dbo->object( $r_carac ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDCaracteristicaProducto=".$r->IDCaracteristicaProducto?>&tabsocio=caracteristica&tab=caracteristicap" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Categoria; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaCaracteristicaProducto&id=<?php echo $_GET[id];?>&IDCaracteristicaProducto=<? echo $r->IDCaracteristicaProducto ?>&tabsocio=caracteristica&tab=caracteristicap" ></a>                                </td>
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
