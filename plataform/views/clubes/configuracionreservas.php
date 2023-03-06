<div id="CampoConfiguracionReservas">
    <form name="frmproConfiguracionReservas" id="frmproConfiguracionReservas" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>


                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">


                                <?php
                                $action = "InsertarConfiguracionReservas";

                                if ($_GET[IDConfiguracionReservas]) {
                                    $EditConfiguracionReservas = $dbo->fetchAll("ConfiguracionReservas", " IDConfiguracionReservas = '" . $_GET[IDConfiguracionReservas] . "' ", "array");
                                    $action = "ModificarConfiguracionReservas";
                                ?>
                                    <input type="hidden" name="IDConfiguracionReservas" id="IDConfiguracionReservas" value="<?php echo $EditConfiguracionReservas[IDConfiguracionReservas] ?>" />
                                <?php
                                }
                                ?>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                                    <tr>
                                        <td width="10%">Tipo inicio </td>
                                        <td width="50%"> <?php
                                                            $tiposeccion = array("Normal" => "Normal", "Sedes" => "Sedes", "Categorias" => "Categorias");
                                                            echo SIMHTML::formradiogroup(array_flip($tiposeccion), $EditConfiguracionReservas["TipoInicio"], 'TipoInicio', "class='input mandatory'");
                                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>

                                    <tr>
                                        <td width="26%"> Mostrar Boton Mis Listas Espera</td>
                                        <td width="74%">
                                            <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionReservas["MostrarBotonMisListasEspera"], 'MostrarBotonMisListasEspera', "class='input mandatory'") ?>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>
                                    <tr>
                                        <td width="26%"> Texto Boton Mis Listas Espera </td>
                                        <td width="74%">
                                            <input id="LabelBotonMisListasEspera" type="text" size="25" title="LabelBotonMisListasEspera" name="LabelBotonMisListasEspera" class="input mandatory" value="<?php echo $EditConfiguracionReservas["LabelBotonMisListasEspera"] ?>" />

                                        </td>

                                    </tr>
                                    <tr>
                                        <td width="26%"> Mostrar boton en ver mis reservar para ver historial de reservas?  </td>
                                        <td width="74%">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionReservas["MostrarHistorialMisReservas"], 'MostrarHistorialMisReservas', "class='input mandatory'") ?>

                                        </td>

                                    </tr>
                                    <tr>
                                        <td width="26%"> Texto Boton: "Historial Mis Reservas" </td>
                                        <td width="74%">
                                            <input id="TextoBotonHistorialMisReservas" type="text" size="25" title="TextoBotonHistorialMisReservas" name="TextoBotonHistorialMisReservas" class="input mandatory" value="<?php echo $EditConfiguracionReservas["TextoBotonHistorialMisReservas"] ?>" />

                                        </td>

                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>

                                    <tr>
                                        <td>Activa</td>
                                        <td>
                                            <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditConfiguracionReservas["Activo"], 'Activo', "class='input mandatory'") ?>
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
            <th>Mostrar Boton Mis Listas Espera</th>
            <th>Texto Boton Mis Listas Espera</th>
            <th>Activa</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = &$dbo->all("ConfiguracionReservas", "IDClub = '" . $frm[$key]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDConfiguracionReservas=" . $r->IDConfiguracionReservas ?>&tabclub=parametros&tabparametro=ConfiguracionReservas" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->TipoInicio; ?></td>
                    <td><?php echo $r->MostrarBotonMisListasEspera; ?></td>
                    <td><?php echo $r->LabelBotonMisListasEspera; ?></td>
                    <td><?php echo $r->Activo; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaConfiguracionReservas&id=<?php echo $frm[$key]; ?>&IDConfiguracionReservas=<? echo $r->IDConfiguracionReservas ?>&tabclub=parametros&tabparametro=configuracionreservas"></a>
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