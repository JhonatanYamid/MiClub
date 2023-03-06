<div id="DomicilioPregunta">
  <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td>


          <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
              <td valign="top">


                <?php
                $action = "InsertarDomicilioPregunta";

                if ($_GET[IDDomicilioPregunta]) {
                  $EditDomicilioPregunta = $dbo->fetchAll("DomicilioPregunta", " IDDomicilioPregunta = '" . $_GET[IDDomicilioPregunta] . "' ", "array");
                  $action = "ModificaDomicilioPregunta";
                ?>
                  <input type="hidden" name="IDDomicilioPregunta" id="IDDomicilioPregunta" value="<?php echo $EditDomicilioPregunta[IDDomicilioPregunta] ?>" />
                <?php
                }
                ?>
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                  <tr>
                    <td width="26%"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </td>
                    <td width="74%">
                      <input id="Nombre" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" name="Nombre" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Nombre"] ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="26%"><?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </td>
                    <td width="74%">
                      <input id="Descripcion" type="text" size="25" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>" name="Descripcion" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Descripcion"] ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></td>
                    <td>
                      <select name="Tipo" id="Tipo" class="form-control" required>
                        <option value=""></option>
                        <option value="Text" <?php if ($EditDomicilioPregunta["Tipo"] == "Text") echo "selected"; ?>>Texto</option>
                        <option value="Radio" <?php if ($EditDomicilioPregunta["Tipo"] == "Radio") echo "selected"; ?>>Unica opcion</option>
                        <option value="Checkbox" <?php if ($EditDomicilioPregunta["Tipo"] == "Checkbox") echo "selected"; ?>>Multiple opción</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'Valoresseparadosporcoma(cuandotiposeaunicaomultiple)', LANGSESSION); ?></td>
                    <td>
                      <textarea rows="4" cols="3" id="Valor" name="Valor" class="form-control mandatory"><?php echo $EditDomicilioPregunta["Valor"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?>?</td>
                    <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditDomicilioPregunta["Obligatorio"], 'Obligatorio', "class='input'") ?></td>
                  </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></td>
                    <td><input id="Orden" type="number" size="25" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" name="Orden" class="input mandatory" value="<?php echo $EditDomicilioPregunta["Orden"] ?>" /></td>
                  </tr>
                  <tr>
                    <td><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></td>
                    <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditDomicilioPregunta["Publicar"], 'Publicar', "class='input'") ?></td>
                  </tr>
                </table>
                <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[$key] ?>" />
                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                <input type="hidden" name="IDConfiguracionDomicilio" id="IDConfiguracionDomicilio" value="<?php echo $frm[$key] ?>" />
                <input type="hidden" name="Version" id="Version" value="3" />
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
      <th><?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?></th>
      <th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
      <th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
      <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
    </tr>
    <tbody id="listacontactosanunciante">
      <?php

      $r_documento = &$dbo->all("DomicilioPregunta", "IDConfiguracionDomicilio = '" . $frm[$key]  . "' and Version = 3 ");

      while ($r = $dbo->object($r_documento)) {
      ?>

        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
          <td align="center" width="64">
            <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&id=" . $_GET[id] . "&IDDomicilioPregunta=" . $r->IDDomicilioPregunta ?>&tabsocio=caracteristica&tab=domiciliopregunta" class="ace-icon glyphicon glyphicon-pencil"></a>
          </td>
          <td><?php echo $r->Nombre; ?></td>
          <td><?php echo $r->Descripcion; ?></td>
          <td><?php echo $r->Tipo; ?></td>
          <td><?php echo $r->Publicar; ?></td>
          <td align="center" width="64">
            <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaDomicilioPregunta&id=<?php echo $_GET[id]; ?>&IDDomicilioPregunta=<? echo $r->IDDomicilioPregunta ?>&tabsocio=caracteristica&tab=domiciliopregunta"></a>
          </td>
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