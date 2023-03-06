<form class="form-horizontal formvalida" role="form" method="post" id="EditPreguntaDotacion<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

        <?php
        $action = "InsertarPreguntaDotacion";
        if ($_GET[IDPreguntaDotacion]) {
                $EditPreguntaDotacion = $dbo->fetchAll("PreguntaDotacion", " IDPreguntaDotacion = '" . $_GET["IDPreguntaDotacion"] . "' ", "array");
                $action = "ModificaPreguntaDotacion";
        ?>
                <input type="hidden" name="IDPreguntaDotacion" id="IDPreguntaDotacion" value="<?php echo $EditPreguntaDotacion[IDPreguntaDotacion] ?>" />
        <?php
        }
        ?>



        <div class="form-group first ">

                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PreguntaDotacion', LANGSESSION); ?> </label>
                        <div class="col-sm-8">
                                <input type="text" id="Nombre" name="EtiquetaCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'EtiquetaCampo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'EtiquetaCampo', LANGSESSION); ?>" value="<?php echo $EditPreguntaDotacion["EtiquetaCampo"]; ?>">

                        </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Tipoderespuesta', LANGSESSION); ?> </label>

                        <div class="col-sm-8">
                                <select class="form-control" id="TipoCampo" name="TipoCampo">
                                        <optgroup label="Estándar">
                                                <option value="text" <?php if ($EditPreguntaDotacion["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                                                <option value="textarea" <?php if ($EditPreguntaDotacion["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                                                <option value="radio" <?php if ($EditPreguntaDotacion["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                                                <option value="checkbox" <?php if ($EditPreguntaDotacion["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                                <option value="select" <?php if ($EditPreguntaDotacion["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                                                <option value="number" <?php if ($EditPreguntaDotacion["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
                                                <!--<option value="page">Page Break</option>-->
                                        </optgroup>
                                        <optgroup label="Elegantes">
                                                <option value="date" <?php if ($EditPreguntaDotacion["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
                                                <option value="time" <?php if ($EditPreguntaDotacion["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
                                                <option value="email" <?php if ($EditPreguntaDotacion["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                                        </optgroup>
                                        <optgroup label="Titulo">
                                                <option value="titulo" <?php if ($EditPreguntaDotacion["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>

                                </select>
                        </div>
                </div>

        </div>





        <div class="form-group first ">

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Opcionesderespuesta(separadosporcoma)', LANGSESSION); ?></label>

                        <div class="col-sm-8">
                                <textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPreguntaDotacion["Valores"]; ?></textarea>
                        </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

                        <div class="col-sm-8">
                                <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $EditPreguntaDotacion["Orden"]; ?>">
                        </div>
                </div>


        </div>

        <div class="form-group first ">



                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?> </label>

                        <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPreguntaDotacion["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
                </div>



                <div class="col-xs-12 col-sm-6">

                        <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPreguntaDotacion["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
                        </div>


                </div>



        </div>







        <div class="clearfix form-actions">
                <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID" id="ID" value="<?php echo $EditPreguntaDotacion[$key] ?>" />
                        <input type="hidden" name="IDDotacion" id="IDDotacion" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditPreguntaDotacion[$key] ?>" />
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

                $r_documento = &$dbo->all("PreguntaDotacion", "IDDotacion = '" . $frm[$key]  . "'");

                while ($r = $dbo->object($r_documento)) {
                ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                <td align="center" width="64">
                                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDPreguntaDotacion=" . $r->IDPreguntaDotacion ?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
                                </td>
                                <td><?php echo $r->EtiquetaCampo; ?></td>
                                <td><?php echo $r->TipoCampo; ?></td>
                                <td><?php echo $r->Obligatorio; ?></td>
                                <td><?php echo $r->Orden; ?></td>
                                <td><?php echo $r->Publicar; ?></td>
                                <td align="center" width="64">
                                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPreguntaDotacion&id=<?php echo $EditPreguntaDotacion[$key]; ?>&IDPreguntaDotacion=<? echo $r->IDPreguntaDotacion ?>&tabPreguntaDotacion=formulario"></a>
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