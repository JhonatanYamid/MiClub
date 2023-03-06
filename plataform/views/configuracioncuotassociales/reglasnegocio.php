<form class="form-horizontal formvalida" role="form" method="post" id="EditReglasNegocio<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <?php
    $action = "InsertarReglasNegocio";
    if ($_GET['IDDetalleConfiguracionCuotasSociales']) {
        $EditReglasNegocio = $dbo->fetchAll("DetalleConfiguracionCuotasSociales", " IDDetalleConfiguracionCuotasSociales = '" . $_GET["IDDetalleConfiguracionCuotasSociales"] . "' ", "array");
        $action = "ModificarReglasNegocio";
    ?>
        <input type="hidden" name="IDDetalleConfiguracionCuotasSociales" id="IDDetalleConfiguracionCuotasSociales" value="<?php echo $EditReglasNegocio['IDDetalleConfiguracionCuotasSociales'] ?>" />
    <?php
    }
    ?>


    <div class="col-xs-12 col-sm-12">
        Opciones de respuesta para cuando sea de multiple respuesta o seleccion
        <table id="simple-table" class="table table-bordered table-hover">
            <tr>
                <td>Criterio</td>
                <td>Validaci&oacute;n</td>
                <td>Valor Criterio</td>
                <td>Descuento %</td>
                <td>Publicar</td>
            </tr>
            <?php
            $sql_opciones = "SELECT * FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $_GET["IDPreguntaVial"] . "' Order by Orden";
            $r_opciones = $dbo->query($sql_opciones);
            $contador = 1;
            while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                $array_opciones[$contador] = $row_opciones;
                $contador++;
            }
            ksort($array_opciones);
            $CantidadOpciones = 1;
            for ($i = 1; $i <= $CantidadOpciones; $i++) {
                $valor_ReglasNegocio = $array_opciones[$i];
            ?>
                <tr>
                    <td>
                        <select class="form-control CampoCriterio" onchange="Criterio(this)" name="CampoCriterio<?php echo $i; ?>" id="CampoCriterio<?php echo $i; ?>" dataCont="<?php echo $i ?>" dataID="<?php echo $EditReglasNegocio['IDDetalleConfiguracionCuotasSociales']; ?>" required>
                            <option value="">Seleccione</option>
                            <?php
                            foreach (SIMResources::$CriterioReglasNegocio as $indice => $value) { ?>
                                <option value="<?php echo $indice; ?>" <?php echo $selected = ($indice == $EditReglasNegocio['CampoCriterio']) ? "selected" : ''; ?>><?php echo $value; ?></option>
                            <?php }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="Validacion<?php echo $i; ?>" id="Validacion<?php echo $i; ?>" required>
                            <?php
                            foreach (SIMResources::$ValidacionReglasNegocio as $indice => $value) { ?>
                                <option value="<?php echo $indice; ?>" <?php echo $selected = ($indice == $EditReglasNegocio['Validacion']) ? "selected" : ''; ?>><?php echo $value; ?></option>
                            <?php }
                            ?>
                        </select>
                    </td>
                    <td id="valorcriterio<?php echo $i ?>">
                        <div id="vacio<?php echo $i ?>">
                            <input type="text" name="" value="<?php echo $EditReglasNegocio['CampoCriterio']; ?>" disabled="disabled" class="form-control">
                        </div>
                        <div id="criterio<?php echo $i ?>">
                        </div>
                    </td>
                    <!-- <td id="validacion"><input type="text" name="" value="" disabled="disabled"></td> -->
                    <td><input type="number" name="Descuento<?php echo $i; ?>" value="<?php echo $EditReglasNegocio["Descuento"]; ?>" required></td>
                    <td>
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
                        <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditReglasNegocio["Publicar"], 'Publicar' . $i, "class='input mandatory'") ?></div>
                    </td>
                </tr>
            <?php } ?>

        </table>
    </div>


    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $EditReglasNegocio[$key] ?>" />
            <input type="hidden" name="IDConfiguracionCuotasSociales" id="IDConfiguracionCuotasSociales" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="submit" class="submit" value="Guardar">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditReglasNegocio[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
            <input type="hidden" name="CantidadOpciones" id="CantidadOpciones" value="<?php echo $CantidadOpciones ?>" />
        </div>
    </div>




</form>

<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Criterio</th>
        <th>Validaci&oacute;n</th>
        <th>Valor Criterio</th>
        <th>Descuento %</th>
        <th>Publicar</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $sql_ReglasNegocio = "SELECT * FROM DetalleConfiguracionCuotasSociales WHERE IDConfiguracionCuotasSociales = '" . $frm['IDConfiguracionCuotasSociales']  . "'";
        $q_ReglasNegocio = $dbo->query($sql_ReglasNegocio);

        while ($r = $dbo->object($q_ReglasNegocio)) {
        ?>
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDDetalleConfiguracionCuotasSociales=" . $r->IDDetalleConfiguracionCuotasSociales ?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo SIMResources::$CriterioReglasNegocio[$r->CampoCriterio]; ?></td>
                <td><?php echo SIMResources::$ValidacionReglasNegocio[$r->Validacion]; ?></td>
                <td><?php echo $r->ValorCriterio; ?></td>
                <td><?php echo $r->Descuento; ?></td>
                <td><?php echo $r->Publicar; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminarReglasNegocio&id=<?php echo $frm[$key]; ?>&IDDetalleConfiguracionCuotasSociales=<? echo $r->IDDetalleConfiguracionCuotasSociales ?>&tabencuesta=formulario&IDAuxilios=<?php echo $frm[$key]; ?>"></a>
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


<script>
    $(document).ready(function() {
        $('.CampoCriterio').change();
    });


    function Criterio(e) {
        var i = $(e).attr('dataCont');
        var id = $(e).attr('dataID');
        var data = $(e).val();
        if ($(e).val() == '') {
            $('#criterio' + i).hide();
            $('#vacio' + i).show();
        } else {
            $('#vacio' + i).hide();
            $('#criterio' + i).show();
            $.ajax({
                method: 'POST',
                url: 'includes/async/reglasNegocio.async.php',
                dataType: 'html',
                data: {
                    'criterio': data,
                    'id': id,
                    'cont': i
                }
            }).done(function(data, textStatus, jqXHR) {
                $('#criterio' + i).html(data)
            });


        }
    };
</script>