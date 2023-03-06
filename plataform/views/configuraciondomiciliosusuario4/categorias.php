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
              <td width="26%">Nombre </td>
              <td width="74%">
                <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditPropiedadProducto["Nombre"] ?>" />
              </td>
          </tr>
          <tr>
            <td>Tipo</td>
            <td>
            <select name="Tipo" id="Tipo" class="form-control" required>
              <option value=""></option>
              <option value="Radio" <?php if($EditPropiedadProducto["Tipo"]=="Radio") echo "selected"; ?>>Unica opcion</option>
              <option value="Checkbox" <?php if($EditPropiedadProducto["Tipo"]=="Checkbox") echo "selected"; ?>>Multiple opción</option>
            </select>
            </td>
          </tr>
            <tr>
              <td>Obligatorio?</td>
              <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPropiedadProducto["Obligatorio"] , 'Obligatorio' , "class='input'" ) ?></td>
            </tr>
            <tr>
              <td width="26%">Maximo de opciones permitidas para seleccionar cunado es multiple </td>
              <td width="74%">
                <input id="MaximoPermitido" type="text" size="25" title="Maximo Permitido" name="MaximoPermitido" class="input" value="<?php echo $EditPropiedadProducto["MaximoPermitido"] ?>" />
              </td>
          </tr>
            <tr>
              <td>Publicar</td>
              <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPropiedadProducto["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
            </tr>
            </table>
            <input type="hidden" name="IDConfiguracionDomicilio" id="IDConfiguracionDomicilio" value="<?php echo $frm[ $key ]?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm["IDClub"]?>" />
            <input type="hidden" name="Version" id="Version" value="4" />

          </td>
          <td valign="top">


        </td>
      </tr>
  </table>

  </td>
  </tr>
  <tr>
    <td align="center"><input type="submit" class="submit" value="Agregar"></td>
  </tr>

  </table>
</form>


        <br />
        <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                        <th align="center" valign="middle" width="64">Editar</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Publicar</th>
                        <th align="center" valign="middle" width="64">Eliminar</th>
                </tr>
                <tbody id="listacontactosanunciante">
                <?php

                        $r_documento =& $dbo->all( "PropiedadProducto" , "IDConfiguracionDomicilio = '" . $frm[$key]  ."' and Version = 4 ");

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
