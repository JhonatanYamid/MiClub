      <div id="PropiedadProducto">
	<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarPropiedadProducto";

                  if( $_GET[IDPropiedadProducto] )
                  {
                          $EditPropiedadProducto =$dbo->fetchAll("PropiedadProducto"," IDPropiedadProducto = '".$_GET[IDPropiedadProducto]."' ","array");
                          $action = "ModificaPropiedadProducto";
                          ?>
                          <input type="hidden" name="IDPropiedadProducto" id="IDPropiedadProducto" value="<?php echo $EditPropiedadProducto[IDPropiedadProducto]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                  <tr>
                    <td width="26%"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </td>
                    <td width="74%">
                      <input id="Nombre" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" name="Nombre" class="input mandatory" value="<?php echo $EditPropiedadProducto["Nombre"] ?>" />
                    </td>
                </tr>
                <tr>
                  <td><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></td>
                  <td>
                  <select name="Tipo" id="Tipo" class="form-control" required>
                    <option value=""></option>
                    <option value="Radio" <?php if($EditPropiedadProducto["Tipo"]=="Radio") echo "selected"; ?>>Unica opcion</option>
                    <option value="Checkbox" <?php if($EditPropiedadProducto["Tipo"]=="Checkbox") echo "selected"; ?>>Multiple opción</option>
                  </select>
                  </td>
                </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?>?</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPropiedadProducto["Obligatorio"] , 'Obligatorio' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                    <td width="26%"><?= SIMUtil::get_traduccion('', '', 'Maximodeopcionespermitidasparaseleccionarcuandoesmultiple', LANGSESSION); ?> </td>
                    <td width="74%">
                      <input id="MaximoPermitido" type="text" size="25" title="Maximo Permitido" name="MaximoPermitido" class="input" value="<?php echo $EditPropiedadProducto["MaximoPermitido"] ?>" />
                    </td>
                </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPropiedadProducto["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
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
                      <th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
                      <th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
                      <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "PropiedadProducto" , "IDConfiguracionDomicilio = '" . $frm[$key]  ."' and Version = 2 ");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDPropiedadProducto=".$r->IDPropiedadProducto?>&tabsocio=caracteristica&tab=categoriap" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Tipo; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaPropiedadProducto&id=<?php echo $_GET[id];?>&IDPropiedadProducto=<? echo $r->IDPropiedadProducto ?>&tabsocio=caracteristica&tab=categoriap" ></a>                                </td>
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
