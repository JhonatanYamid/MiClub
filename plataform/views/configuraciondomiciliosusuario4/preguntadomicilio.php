<div id="DomicilioPregunta">
<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
<table id="simple-table" class="table table-striped table-bordered table-hover">
  <tr>
    <td>


<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <td valign="top">


            <?php
            $action = "InsertarDomicilioPregunta";

            if( $_GET[IDDomicilioPregunta] )
            {
                    $EditDomicilioPregunta =$dbo->fetchAll("DomicilioPregunta"," IDDomicilioPregunta = '".$_GET[IDDomicilioPregunta]."' ","array");
                    $action = "ModificaDomicilioPregunta";
                    ?>
                    <input type="hidden" name="IDDomicilioPregunta" id="IDDomicilioPregunta" value="<?php echo $EditDomicilioPregunta[IDDomicilioPregunta]?>" />
                    <?php
            }
            ?>
            <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


            <tr>
              <td width="26%">Nombre </td>
              <td width="74%">
                <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Nombre"] ?>" />
              </td>
          </tr>
              <tr>
                <td width="26%">Descripcion </td>
              <td width="74%">
                <input id="Descripcion" type="text" size="25" title="Descripcion" name="Descripcion" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Descripcion"] ?>" />
              </td>
            </tr>
            <tr>
              <td>Tipo</td>
              <td>
              <select name="Tipo" id="Tipo" class="form-control" required>
                <option value=""></option>
                <option value="Text" <?php if($EditDomicilioPregunta["Tipo"]=="Text") echo "selected"; ?>>Texto</option>
                <option value="Radio" <?php if($EditDomicilioPregunta["Tipo"]=="Radio") echo "selected"; ?>>Unica opcion</option>
                <option value="Checkbox" <?php if($EditDomicilioPregunta["Tipo"]=="Checkbox") echo "selected"; ?>>Multiple opción</option>
              </select>
              </td>
            </tr>
            <tr>
              <td>Valores separados por coma (cuando tipo sea unica o multiple)</td>
              <td>
                <textarea rows="4" cols="3" id="Valor" name="Valor" class="form-control mandatory"><?php echo $EditDomicilioPregunta["Valor"] ?></textarea>
              </td>
            </tr>
            <tr>
              <td>Obligatorio?</td>
              <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditDomicilioPregunta["Obligatorio"] , 'Obligatorio' , "class='input'" ) ?></td>
            </tr>
            <tr>
              <td>Orden</td>
              <td><input id="Orden" type="number" size="25" title="Orden" name="Orden" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Orden"] ?>" /></td>
            </tr>
            <tr>
              <td>Publicar</td>
              <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditDomicilioPregunta["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
            </tr>
            </table>
            <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
            <input type="hidden" name="IDConfiguracionDomicilio" id="IDConfiguracionDomicilio" value="<?php echo $frm[ $key ]?>" />
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
                        <th>Descripcion</th>
                        <th>Tipo</th>
                        <th>Publicar</th>
                        <th align="center" valign="middle" width="64">Eliminar</th>
                </tr>
                <tbody id="listacontactosanunciante">
                <?php

                        $r_documento =& $dbo->all( "DomicilioPregunta" , "IDConfiguracionDomicilio = '" . $frm[$key]  ."' and Version = 4 ");

                        while( $r = $dbo->object( $r_documento ) )
                        {
                ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                        <td align="center" width="64">
                        <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDDomicilioPregunta=".$r->IDDomicilioPregunta?>&tabsocio=caracteristica&tab=domiciliopregunta" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                        <td><?php echo $r->Nombre; ?></td>
                        <td><?php echo $r->Descripcion; ?></td>
                        <td><?php echo $r->Tipo; ?></td>
                        <td><?php echo $r->Publicar; ?></td>
                        <td align="center" width="64">
                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaDomicilioPregunta&id=<?php echo $_GET[id];?>&IDDomicilioPregunta=<? echo $r->IDDomicilioPregunta ?>&tabsocio=caracteristica&tab=domiciliopregunta" ></a>                                </td>
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
