<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> <?= SIMUtil::get_traduccion('', '', 'socio', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Socio" name="Socio" placeholder="<?= SIMUtil::get_traduccion('', '', 'socio', LANGSESSION); ?>" class="col-xs-12" disabled title="<?= SIMUtil::get_traduccion('', '', 'socio', LANGSESSION); ?>" value="<?= $frm["Nombre"] . ' ' . $frm['Apellido'] ?>">
                                    <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $frm['IDSocio'] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="IDTipoTarjetaRotativa"> <?= SIMUtil::get_traduccion('', '', 'TipoTarjetaRotativa', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <select name="IDTipoTarjetaRotativa" id="IDTipoTarjetaRotativa" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TipoTarjetaRotativa', LANGSESSION); ?>">
                                        <option value="">Seleccione</option>
                                        <?php
                                        $sqlTipoTarjetaRotativa = "SELECT * FROM TipoTarjetaRotativa WHERE IDClub = " . SIMUser::get('club') . " AND  Publicar ='S' ORDER BY Nombre";
                                        $q_TipoTarjetaRotativa = $dbo->query($sqlTipoTarjetaRotativa);

                                        while ($arr_TipoTarjetaRotativa = $dbo->assoc($q_TipoTarjetaRotativa)) {
                                            $selected = ($arr_TipoTarjetaRotativa['IDTipoTarjetaRotativa'] == $frm['IDTipoTarjetaRotativa']) ? "selected" : "";
                                        ?>
                                            <option value="<?= $arr_TipoTarjetaRotativa['IDTipoTarjetaRotativa'] ?>" <?= $selected ?>><?= $arr_TipoTarjetaRotativa['Nombre'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'cupos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Cupos" name="Cupos" placeholder="<?= SIMUtil::get_traduccion('', '', 'cupos', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'cupos', LANGSESSION); ?>" value="<?php echo $frm["Cupos"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'numerotarjeta', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="NumeroTarjeta" name="NumeroTarjeta" placeholder="<?= SIMUtil::get_traduccion('', '', 'numerotarjeta', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'numerotarjeta', LANGSESSION); ?>" value="<?php echo $frm["NumeroTarjeta"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="date" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendary mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?= $frm["FechaInicio"] ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaCaducidad', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="date" id="FechaCaducidad" name="FechaCaducidad" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCaducidad', LANGSESSION); ?>" class="col-xs-12 calendary" title="<?= SIMUtil::get_traduccion('', '', 'FechaCaducidad', LANGSESSION); ?>" value="<?= $frm["FechaCaducidad"] ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm["IDClub"])) ? SIMUser::get("club") : $frm["IDClub"];  ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");

?>