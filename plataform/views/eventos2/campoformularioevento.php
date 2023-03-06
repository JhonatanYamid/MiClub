<form class="form-horizontal formvalida" role="form" method="post" id="EditCampoFormularioEvento<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

        <?php
        $action = "InsertarCampoFormularioEvento";
        if ($_GET[IDCampoFormularioEvento2]) {
                $EditCampoFormularioEvento = $dbo->fetchAll("CampoFormularioEvento2", " IDCampoFormularioEvento2 = '" . $_GET["IDCampoFormularioEvento2"] . "' ", "array");
                $action = "ModificaCampoFormularioEvento";
        ?>
                <input type="hidden" name="IDCampoFormularioEvento2" id="IDCampoFormularioEvento2" value="<?php echo $EditCampoFormularioEvento[IDCampoFormularioEvento2] ?>" />
        <?php
        }
        ?>



        <div class="form-group first ">

                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Etiquetadelcampo', LANGSESSION); ?> </label>
                        <div class="col-sm-8">
                                <input type="text" id="Nombre" name="EtiquetaCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Etiquetadelcampo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditCampoFormularioEvento["EtiquetaCampo"]; ?>">

                        </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipodeCampo', LANGSESSION); ?> </label>

                        <div class="col-sm-8">
                                <select class="form-control" id="TipoCampo" name="TipoCampo">
                                        <optgroup label="Estándar">
                                                <option value="text" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                                                <option value="textarea" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                                                <option value="radio" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                                                <option value="checkbox" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                                <option value="select" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                                                <option value="number" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
                                                <!--<option value="page">Page Break</option>-->
                                        </optgroup>
                                        <optgroup label="Elegantes">
                                                <option value="date" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
                                                <option value="time" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
                                                <option value="email" <?php if ($EditCampoFormularioEvento["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                                        </optgroup>

                                </select>
                        </div>
                </div>

        </div>





        <div class="form-group first ">

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Valores(separadosporcoma)', LANGSESSION); ?></label>

                        <div class="col-sm-8">
                                <textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditCampoFormularioEvento["Valores"]; ?></textarea>
                        </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

                        <div class="col-sm-8">
                                <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditCampoFormularioEvento["Orden"]; ?>">
                        </div>
                </div>


        </div>

        <div class="form-group first ">



                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?> </label>

                        <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditCampoFormularioEvento["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
                </div>



                <div class="col-xs-12 col-sm-6">

                        <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditCampoFormularioEvento["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
                        </div>


                </div>



        </div>







        <div class="clearfix form-actions">
                <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID" id="ID" value="<?php echo $EditCampoFormularioEvento[$key] ?>" />
                        <input type="hidden" name="IDEvento2" id="IDEvento2" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditCampoFormularioEvento[$key] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />


                </div>
        </div>




</form>










<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
                <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
                <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
        </tr>
        <tbody id="listacontactosanunciante">
                <?php

                $r_documento = &$dbo->all("CampoFormularioEvento2", "IDEvento2 = '" . $frm[$key]  . "'");

                while ($r = $dbo->object($r_documento)) {
                ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                <td align="center" width="64">
                                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDCampoFormularioEvento2=" . $r->IDCampoFormularioEvento2 ?>&tabevento=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
                                </td>
                                <td><?php echo $r->EtiquetaCampo; ?></td>
                                <td><?php echo $r->TipoCampo; ?></td>
                                <td><?php echo $r->Obligatorio; ?></td>
                                <td><?php echo $r->Orden; ?></td>
                                <td><?php echo $r->Publicar; ?></td>
                                <td align="center" width="64">
                                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCampoFormularioEvento2&id=<?php echo $EditCampoFormularioEvento[$key]; ?>&IDCampoFormularioEvento2=<? echo $r->IDCampoFormularioEvento2 ?>&tabevento=formulario"></a>
                                </td>
                        </tr>
                <?php
                }
                ?>
        </tbody>
        <tr>
                <th class="texto" colspan="16"></th>
        </tr>
</table>