<div id="CampoPreguntasInvitados">

    <form name="frmCampoPreguntasInvitados" id="frmCampoPreguntasInvitados" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">


        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td valign="top">


                    <?php
                    $action = "InsertarPreguntaInvitados";
                    if ($_GET["IDCampoFormularioInvitado"]) {
                        $EditPreguntaInvitados = $dbo->fetchAll("CampoFormularioInvitado", " IDCampoFormularioInvitado = '" . $_GET["IDCampoFormularioInvitado"] . "' ", "array");
                        $action = "ModificaPreguntaInvitados";
                    ?>
                        <input type="hidden" name="IDCampoFormularioInvitado" id="IDCampoFormularioInvitado" value="<?php echo $EditPreguntaInvitados["IDCampoFormularioInvitado"] ?>" />
                    <?php
                    }
                    ?>

                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                        <tr>
                            <td>Pregunta </td>
                            <td><input type="text" id="Nombre" name="EtiquetaCampo" placeholder="EtiquetaCampo" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditPreguntaInvitados["EtiquetaCampo"]; ?>"></td>


                            <td>Tipo Respuesta</td>
                            <td>
                                <select class="form-control" id="TipoCampo" name="TipoCampo">
                                    <optgroup label="Estándar">
                                        <option value="text" <?php if ($EditPreguntaInvitados["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                                        <option value="textarea" <?php if ($EditPreguntaInvitados["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                                        <option value="radio" <?php if ($EditPreguntaInvitados["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                                        <option value="checkbox" <?php if ($EditPreguntaInvitados["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                        <option value="select" <?php if ($EditPreguntaInvitados["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                                        <option value="number" <?php if ($EditPreguntaInvitados["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
                                        <!--<option value="page">Page Break</option>-->
                                    </optgroup>
                                    <optgroup label="Elegantes">
                                        <option value="date" <?php if ($EditPreguntaInvitados["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
                                        <option value="time" <?php if ($EditPreguntaInvitados["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
                                        <option value="email" <?php if ($EditPreguntaInvitados["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                                    </optgroup>
                                    <optgroup label="Titulo">
                                        <option value="titulo" <?php if ($EditPreguntaInvitados["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>
                                    </optgroup>
                                    <optgroup label="Archivo">
                                        <option value="imagen" <?php if ($EditPreguntaInvitados["TipoCampo"] == "imagen") echo "selected"; ?>>Imagen</option>
                                        <option value="imagenarchivo" <?php if ($EditPreguntaInvitados["TipoCampo"] == "imagenarchivo") echo "selected"; ?>>Imagen archivo</option>
                                    </optgroup>

                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Opciones de respuesta (separados por coma) </td>
                            <td><textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPreguntaInvitados["Valores"]; ?></textarea></td>
                            <td>Orden</td>
                            <td>
                                <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPreguntaInvitados["Orden"]; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td>Obligatorio </td>
                            <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPreguntaInvitados["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></td>

                        </tr>

                        <tr>
                            <td>Publicar </td>
                            <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPreguntaInvitados["Publicar"], 'Publicar', "class='input mandatory'") ?></td>
                            <td></td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" align="center">
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                                <input type="submit" class="submit" value="Agregar">
                            </td>

                        </tr>


                    </table>


                </td>
                <td valign="top">

                    <?php
                    //$action = "InsertarDisponibilidadElemento";
                    ?>

                </td>
            </tr>
        </table>






    </form>

</div>








<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Obligatorio</th>
        <th>Orden</th>
        <th>Publicar</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_documento = &$dbo->all("CampoFormularioInvitado", "IDClub = '" . $frm[$key]  . "'");

        while ($r = $dbo->object($r_documento)) {
        ?>

            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDCampoFormularioInvitado=" . $r->IDCampoFormularioInvitado ?>&tabclub=parametros&tabparametro=preguntasinvitados" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->EtiquetaCampo; ?></td>
                <td><?php echo $r->TipoCampo; ?></td>
                <td><?php echo $r->Obligatorio; ?></td>
                <td><?php echo $r->Orden; ?></td>
                <td><?php echo $r->Publicar; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminarPreguntaInvitados&id=<?php echo $frm[$key]; ?>&IDCampoFormularioInvitado=<? echo $r->IDCampoFormularioInvitado ?>&tabclub=parametros&tabparametro=preguntasinvitados"></a>
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