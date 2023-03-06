<form class="form-horizontal formvalida" role="form" method="post" id="EditSocioHabitacion<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data"> <?php
                                                                                                                                                                                            $action = "InsertarSocioHabitacion";

                                                                                                                                                                                            if ($_GET[IDSocioHabitacion]) {
                                                                                                                                                                                                $EditSocioHabitacion = $dbo->fetchAll("SocioHabitacion", " IDSocioHabitacion = '" . $_GET[IDSocioHabitacion] . "' ", "array");
                                                                                                                                                                                                $action = "ModificaSocioHabitacion";
                                                                                                                                                                                            ?> <input type="hidden" name="IDSocioHabitacion" id="IDSocioHabitacion" value="<?php echo $EditSocioHabitacion[IDSocioHabitacion] ?>" /> <?php
                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                        ?>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Habitación', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <select name=IDHabitacion>
                    <option value=0><?= SIMUtil::get_traduccion('', '', '[ESCOGELAHABITACIÓN]', LANGSESSION); ?></option>
                    <?php
                    $sql = "SELECT * FROM Habitacion WHERE IDClub = " . SIMUser::get("club");
                    $qry = $dbo->query($sql);
                    while ($Datos = $dbo->fetchArray($qry)) :
                        $NombreTipo = $dbo->getFields("TipoHabitacion", "Nombre", "IDTipoHabitacion = $Datos[IDTipoHabitacion]");
                    ?>
                        <option value="<?php echo $Datos[IDHabitacion]; ?>" <?php if ($Datos[IDHabitacion] == $EditSocioHabitacion[IDHabitacion]) echo "selected"; ?>><?php echo $NombreTipo; ?></option>
                    <?php
                    endwhile;
                    ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumerodeFracciones', LANGSESSION); ?></label>
            <div class="col-sm-8">
                <input type="text" id="NumeroFracciones" name="NumeroFracciones" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumerodeFracciones', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumerodeFracciones', LANGSESSION); ?>" value="<?php echo $EditSocioHabitacion["NumeroFracciones"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechadeInicioFraccion', LANGSESSION); ?></label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicioFraccion" name="FechaInicioFraccion" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechadeInicioFraccion', LANGSESSION); ?>" class="col-xs-12 calendar" title="fecha de inicio cortesía" value="<?php echo $EditSocioHabitacion["FechaInicioFraccion"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaFinFraccion', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="FechaFinFraccion" name="FechaFinFraccion" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFinFraccion', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFinFraccion', LANGSESSION); ?>" value="<?php echo $EditSocioHabitacion["FechaFinFraccion"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nochesdisponibles', LANGSESSION); ?></label>
            <div class="col-sm-8">
                <input type="text" id="Noches" name="Noches" placeholder="<?= SIMUtil::get_traduccion('', '', 'Noches', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Nochesdisponibles', LANGSESSION); ?>" value="<?php echo $EditSocioHabitacion["Noches"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estadiasdisponibles', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Estadias" name="Estadias" placeholder="<?= SIMUtil::get_traduccion('', '', 'Estadias', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Estadiasdisponibles', LANGSESSION); ?>" value="<?php echo $EditSocioHabitacion["Estadias"]; ?>">
            </div>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm[$key] ?>" />
            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'guardar', LANGSESSION); ?>">
            <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Habitaciones', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Fracciones', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Inicio', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Fin', LANGSESSION); ?></th>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

                                            $r_datos = &$dbo->all("SocioHabitacion", "IDSocio = '" . $frm[$key]  . "'");

                                            while ($r = $dbo->object($r_datos)) {
                                                $TipoHabitacion = $dbo->getFields("Habitacion", "IDTipoHabitacion", "IDHabitacion = $r->IDHabitacion");
                                                $NombreTipo = $dbo->getFields("TipoHabitacion", "Nombre", "IDTipoHabitacion = $TipoHabitacion");
                                            ?>
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDSocioHabitacion=" . $r->IDSocioHabitacion ?>&tabsocio=habitaciones" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $NombreTipo; ?></td>
                <td><?php echo $r->NumeroFracciones; ?></td>
                <td><?php echo $r->FechaInicioFraccion; ?></td>
                <td><?php echo $r->FechaFinFraccion; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaSocioHabitacion&id=<?php echo $frm[$key]; ?>&IDSocioHabitacion=<? echo $r->IDSocioHabitacion ?>&tabsociodo=habitaciones"></a>
                </td>
            </tr> <?php
                                            }
                    ?>
    </tbody>
    <tr>
        <th class="texto" colspan="14"></th>
    </tr>
</table>