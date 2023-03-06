<div id="CampoPreguntasInvitados">

    <form name="frmCampoPreguntasInvitados" id="frmCampoPreguntasInvitados" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">


        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td valign="top">


                    <?php
                    $action = "InsertarPreguntaInvitadosExternos";
                    if ($_GET["IDCampoInvitadoExterno"]) {
                        $EditPreguntaInvitadosExternos = $dbo->fetchAll("CampoInvitadoExterno", " IDCampoInvitadoExterno = '" . $_GET["IDCampoInvitadoExterno"] . "' ", "array");
                        $action = "ModificaPreguntaInvitadosExternos";
                    ?>
                        <input type="hidden" name="IDCampoInvitadoExterno" id="IDCampoInvitadoExterno" value="<?php echo $EditPreguntaInvitadosExternos["IDCampoInvitadoExterno"] ?>" />
                    <?php
                    }
                    ?>

                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                        <tr>
                            <td>Pregunta </td>
                            <td><input type="text" id="Nombre" name="EtiquetaCampo" placeholder="EtiquetaCampo" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditPreguntaInvitadosExternos["EtiquetaCampo"]; ?>"></td>


                            <td>Tipo Respuesta</td>
                            <td>
                                <select class="form-control" id="TipoCampo" name="TipoCampo">
                                    <optgroup label="Estándar">
                                        <option value="text" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                                        <option value="textarea" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                                        <option value="radio" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                                        <option value="checkbox" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                        <option value="select" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                                        <option value="number" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
                                        <!--<option value="page">Page Break</option>-->
                                    </optgroup>
                                    <optgroup label="Elegantes">
                                        <option value="date" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
                                        <option value="time" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
                                        <option value="email" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                                    </optgroup>
                                    <optgroup label="Titulo">
                                        <option value="titulo" <?php if ($EditPreguntaInvitadosExternos["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>

                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Opciones de respuesta (separados por pie [|] ) </td>
                            <td><textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPreguntaInvitadosExternos["Valores"]; ?></textarea></td>
                            <td>Orden</td>
                            <td>
                                <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPreguntaInvitadosExternos["Orden"]; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td>Obligatorio </td>
                            <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPreguntaInvitadosExternos["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></td>

                        </tr>

                        <tr>
                            <td>Publicar </td>
                            <td><? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditPreguntaInvitadosExternos["Activo"], 'Activo', "class='input mandatory'") ?></td>
                            <td></td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" align="center">
                                <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[$key] ?>" />
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
        
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_documento = &$dbo->all("CampoInvitadoExterno", "IDServicio = '" . $frm[$key]  . "'");

        while ($r = $dbo->object($r_documento)) {
        ?>

            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDCampoInvitadoExterno=" . $r->IDCampoInvitadoExterno ?>&tab=preguntasinvitados" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->EtiquetaCampo; ?></td>
                <td><?php echo $r->TipoCampo; ?></td>
                <td><?php echo $r->Obligatorio; ?></td>
                <td><?php echo $r->Orden; ?></td>                
                <td align="center" width="64">
                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPreguntaInvitadosExternos&ids=<?php echo $_GET["ids"]; ?>&IDCampoInvitadoExterno=<? echo $r->IDCampoInvitadoExterno ?>&tab=preguntasinvitados"></a>
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