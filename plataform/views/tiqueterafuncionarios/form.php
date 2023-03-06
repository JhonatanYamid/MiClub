<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">

                                <div class="form-group col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8 limpiar">
                                        <input type="text" id="Buscar" name="Buscar" placeholder="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> class="col-xs-12 autocomplete-ajax-funcionario-cedula" value="<?= $frm['Nombre'] ?>" />
                                        <input type="hidden" name="NumeroDocumento" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" id="NumeroDocumento" value="<?php echo $frm['NumeroDocumento'] ?>" />
                                        <input type="hidden" name="Nombre" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>" id="Nombre" value="<?php echo $frm['Nombre'] ?>" />

                                    </div>

                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaInicio"><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="date" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>" required></div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaFin"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="date" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>" required></div>

                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TiqueteraFuncionarios', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formPopup('TaloneraFunc', 'NombreTalonera', 'IDTaloneraFunc', 'IDTaloneraFunc', $frm["IDTaloneraFunc"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', 'onChange = "addCantidadEntradas()"', "AND IDClub = $IDClub"); ?>
                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Cantidad Entradas</label>
                                    <div class="col-sm-8"><input type="text" name="CantidadEntradas" id="CantidadEntradas" value="<?php echo $frm["CantidadEntradas"] ?>" /></div>

                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Desayuno </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Desayuno"], 'Desayuno', "class='input'") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Almuerzo </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Almuerzo"], 'Almuerzo', "class='input'") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cena </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Cena"], 'Cena', "class='input'") ?>
                                    </div>
                                </div>

                            </div>




                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario") ?>" />
                                        <input type="hidden" name="IDUsuarioTalonera" id="IDUsuarioTalonera" value="<?php echo $frm["IDUsuarioTalonera"] ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                        </button>
                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <th>Fecha Consumo</th>
                        <th>Tipo Consumo</th>

                    </tr>
                    <tbody id="listaConsumos">
                        <?php
                        $r_datos = &$dbo->all("LogTiqueteraFuncionarios", "IDClub = " . SIMUser::get("club") . " AND NumeroDocumento = " . $frm['NumeroDocumento']);
                        while ($r = $dbo->object($r_datos)) {
                        ?>

                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                <td>
                                    <?php echo $r->FechaConsumo; ?>
                                </td>
                                <td>
                                    <?php echo $r->TipoConsumo; ?>
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
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<script>
    function addCantidadEntradas() {
        var IDTaloneraFunc = $('#IDTaloneraFunc').val();

        jQuery.ajax({
            type: "GET",
            data: {
                oper: "autofill",
                IDTaloneraFunc: IDTaloneraFunc
            },
            dataType: "text",
            url: "includes/async/tiqueterafuncionarios.async.php",
            success: function(data) {
                console.log(data);
                $("#CantidadEntradas").val(data);
            }
        });
    }
</script>

<?
include("cmp/footer_scripts.php");
?>