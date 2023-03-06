<div id="CampoConfiguracionDirectorioSocio">
    <form name="frmproConfiguracionDirectorioSocio" id="frmproConfiguracionDirectorioSocio" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>


                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">


                                <?php
                                $action = "InsertarConfiguracionDirectorioSocio";

                                if ($_GET[IDConfiguracionDirectorioSocio]) {
                                    $EditConfiguracionDirectorioSocio = $dbo->fetchAll("ConfiguracionDirectorioSocio", " IDConfiguracionDirectorioSocio = '" . $_GET[IDConfiguracionDirectorioSocio] . "' ", "array");
                                    $action = "ModificarConfiguracionDirectorioSocio";
                                ?>
                                    <input type="hidden" name="IDConfiguracionDirectorioSocio" id="IDConfiguracionDirectorioSocio" value="<?php echo $EditConfiguracionDirectorioSocio[IDConfiguracionDirectorioSocio] ?>" />
                                <?php
                                }
                                ?>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                                    <tr>
                                        <td width="26%">Tipo inicio </td>
                                        <td width="74%"> <?php
                                                            $tiposeccion = array("SeccionGrid" => "Grilla de seccion (iconos)", "SeccionHeader" => "Secciones en encabezado");
                                                            echo SIMHTML::formradiogroup(array_flip($tiposeccion), $EditConfiguracionDirectorioSocio["TipoInicio"], 'TipoInicio', "class='input mandatory'");
                                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>


                                    <tr>
                                        <td>Activa</td>
                                        <td>
                                            <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditConfiguracionDirectorioSocio["Activa"], 'Activa', "class='input mandatory'") ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center">&nbsp;</td>
                                    </tr>
                                </table>
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

                            </td>
                            <td valign="top">

                                <?php
                                //$action = "InsertarDisponibilidadElemento";
                                ?>

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
            <th>Tipo Inicio</th>
            <th>Activa</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = &$dbo->all("ConfiguracionDirectorioSocio", "IDClub = '" . $frm[$key]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDConfiguracionDirectorioSocio=" . $r->IDConfiguracionDirectorioSocio ?>&tabclub=parametros&tabparametro=configuraciondirectoriosocio" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->TipoInicio; ?></td>
                    <td><?php echo $r->Activa; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaConfiguracionDirectorioSocio&id=<?php echo $frm[$key]; ?>&IDConfiguracionDirectorioSocio=<? echo $r->IDConfiguracionDirectorioSocio ?>&tabclub=parametros&tabparametro=configuraciondirectoriosocio"></a>
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