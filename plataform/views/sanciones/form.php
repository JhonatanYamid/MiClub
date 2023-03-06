<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>">

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <select name="IDServicioMaestro" id="IDServicioMaestro" class="form-control mandatory" title="Servicio">
                    <option value=""></option>
                    <?php $IDClub = SIMUser::get("club");
                    if ($IDClub == 8 || $IDClub == 185) {
                    ?>
                        <option value="0" <?php if ($frm['IDServicioMaestro'] == 0) echo 'selected' ?>>Todos</option>
                    <?php } ?>
                    <?php
                    $sql_servicio = "Select SC.* From ServicioClub SC Where SC.IDClub = '" . SIMUser::get("club") . "' and SC.Activo = 'S'";
                    $result_servicio = $dbo->query($sql_servicio);
                    while ($row_servicio = $dbo->fetchArray($result_servicio)) : ?>
                        <option value="<?php echo $row_servicio["IDServicioMaestro"]; ?>" <?php if ($row_servicio["IDServicioMaestro"] == $frm[IDServicioMaestro]) echo "selected";  ?>>
                            <?php
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . SIMUser::get("club") . "' and IDServicioMaestro = '" . $row_servicio["IDServicioMaestro"] . "'");
                            if (empty($nombre_servicio_personalizado))
                                $nombre_servicio_personalizado = $nombre_servicio_personalizado;
                            if (!empty($nombre_servicio_personalizado)) {
                                echo $nombre_servicio_personalizado;
                            } else {
                                echo $nombre_servicio = utf8_encode($dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $row_servicio["IDServicioMaestro"] . "'"));
                            }
                            ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'NombreSancion', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <input id=Nombre type=text size=25 name=Nombre class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'NombreSancion', LANGSESSION); ?>" value="<?= $frm[Nombre] ?>">
            </div>
        </div>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cuandoreservasea', LANGSESSION); ?>:</label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$tipocumplimientoreserva), $frm["Cumplida"], "Cumplida", "title=\"Cumplida\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> : </label>

            <div class="col-sm-8">
                <input id=Descripcion type=text size=25 name=Descripcion class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>" value="<?= $frm[Descripcion] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numerodereservasincumplidas', LANGSESSION); ?> : </label>

            <div class="col-sm-8">
                <input id=NumeroIncumplida type=number size=25 name=NumeroIncumplida class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Numerodereservasincumplidas', LANGSESSION); ?>" value="<?= $frm[NumeroIncumplida] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Seguidas', LANGSESSION); ?>?: </label>

            <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Seguida"], "Seguida", "title=\"Publicar\"") ?></div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numerodediasabloquear', LANGSESSION); ?> : </label>

            <div class="col-sm-8">
                <input id=NumeroDiasBloqueo type=number size=25 name=NumeroDiasBloqueo class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Numerodediasabloquear', LANGSESSION); ?>" value="<?= $frm[NumeroDiasBloqueo] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activa', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Publicar"], "Publicar", "title=\"Publicar\"") ?>
            </div>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
            </button>
        </div>
    </div>
</form>

<?
include("cmp/footer_scripts.php");
?>

<!-- <br /> -->
<!-- <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Servicio</th>
        <th>Nombre Sancion</th>
        <th>Descripcion</th>
        <th>Cumplida</th>
        <th>NumeroIncumplida</th>
        <th>Seguida</th>
        <th>NumeroDiasBloqueo</th>
        <th>Publicar</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_documento = &$dbo->all("Sancion", "IDClub = '" . $frm[$key]  . "'");

        while ($r = $dbo->object($r_documento)) {
        ?>

        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
            <td align="center" width="64">
                <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDSancion=" . $r->IDSancion ?>&tabclub=sanciones" class="ace-icon glyphicon glyphicon-pencil"></a>
            </td>
            <td><?php echo utf8_encode($dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r->IDServicioMaestro . "'")) ?></td>
            <td><?php echo $r->Nombre; ?></td>
            <td><?php echo $r->Descripcion; ?></td>
            <td><?php echo $r->Cumplida; ?></td>
            <td><?php echo $r->NumeroIncumplida; ?></td>
            <td><?php echo $r->Seguida; ?></td>
            <td><?php echo $r->NumeroDiasBloqueo; ?></td>
            <td><?php echo $r->Publicar; ?></td>
            <td align="center" width="64">
                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaSancion&id=<?php echo $frm[$key]; ?>&IDSancion=<? echo $r->IDSancion ?>&tabclub=sanciones"></a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
    <tr>
        <th class="texto" colspan="17"></th>
    </tr>
</table> -->