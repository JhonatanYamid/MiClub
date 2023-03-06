<form class="form-horizontal formvalida" role="form" method="post" id="EditVehiculo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data"> <?php
                                                                                                                                                                                    $action = "InsertarVehiculo";

                                                                                                                                                                                    if ($_GET[IDVehiculo]) {
                                                                                                                                                                                        $EditVehiculo = $dbo->fetchAll("Vehiculo", " IDVehiculo = '" . $_GET[IDVehiculo] . "' ", "array");
                                                                                                                                                                                        $action = "ModificaVehiculo";
                                                                                                                                                                                    ?> <input type="hidden" name="IDVehiculo" id="IDVehiculo" value="<?php echo $EditVehiculo[IDVehiculo] ?>" /> <?php
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                    ?> <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Placa', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <input id=Placa type=text size=25 name=Placa class="col-xs-12" title="Placa" value="<?= $EditVehiculo[Placa] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoVehiculo', LANGSESSION); ?>: </label>
            <div class="col-sm-8"> <?php echo SIMHTML::formPopUp("TipoVehiculo", "Nombre", "Nombre", "IDTipoVehiculo", $EditVehiculo["IDTipoVehiculo"], "[Seleccione el Tipo]", "popup mandatory", "title = \"Tipo Vehiculo\"") ?> </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaTecnomecanica', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <input type="text" id="FechaTecnomecanica" name="FechaTecnomecanica" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaTecnomecanica', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaTecnomecanica', LANGSESSION); ?>" value="<?php echo $EditVehiculo["FechaTecnomecanica"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaSeguro', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <input type="text" id="FechaSeguro" name="FechaSeguro" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaSeguro', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaSeguro', LANGSESSION); ?>" value="<?php echo $EditVehiculo["FechaSeguro"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Númerodeparqueadero', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <input type="text" id="NumeroParqueadero" name="NumeroParqueadero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodeparqueadero', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Númerodeparqueadero', LANGSESSION); ?>" value="<?php echo $EditVehiculo["NumeroParqueadero"] ?>">
            </div>
        </div>
    </div>
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Placa', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'FechaTecnomecanica', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'FechaSoat', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Númerodeparqueadero', LANGSESSION); ?></th>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

                                            $r_datos = &$dbo->all("Vehiculo", "IDSocio = '" . $frm[$key]  . "'");

                                            while ($r = $dbo->object($r_datos)) {
                                            ?> <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDVehiculo=" . $r->IDVehiculo ?>&tabsocio=vehiculos" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->Placa; ?></td>
                <td><?php echo $r->FechaTecnomecanica; ?></td>
                <td><?php echo $r->FechaSeguro; ?></td>
                <td><?php echo $r->NumeroParqueadero; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaVehiculo&id=<?php echo $frm[$key]; ?>&IDVehiculo=<? echo $r->IDVehiculo ?>&tabsocio=vehiculos"></a>
                </td>
            </tr> <?php
                                            }
                    ?> </tbody>
    <tr>
        <th class="texto" colspan="15"></th>
    </tr>
</table>