<div class="content-form form2">
    <h3>Experiencia</h3>
    <?php
    $i = 1;
    $ContRows = $dbo->rows($q_IngresosExperiencias);
    while ($frm_IngresosExperiencias = $dbo->assoc($q_IngresosExperiencias)) {
    ?>
        <form class="form-horizontal formvalida" role="form" method="post" id="frmExperiencias<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
            <div class="col-xs-12 col-10 form-content">
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Empleo <?= $i ?></h3>
                </div>
                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Empresa</label>
                            <input type="text" name="Empresa" id="Empresa" placeholder="" class="form-control mandatory" title="Empresa" value="<?= $frm_IngresosExperiencias['Empresa'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Dirección</label>
                            <input type="text" name="Direccion" id="Direccion" placeholder="" class="form-control" title="Direccion" value="<?= $frm_IngresosExperiencias['Direccion'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Tel&eacute;fono</label>
                            <input type="text" name="Telefono" id="Telefono" placeholder="" class="form-control" title="Telefono" value="<?= $frm_IngresosExperiencias['Telefono'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Fecha Ingreso</label>
                            <input type="text" name="FechaIngreso" id="FechaIngreso" placeholder="" class="form-control calendar mandatory" title="FechaIngreso" value="<?= $frm_IngresosExperiencias['FechaIngreso'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Fecha Retiro</label>
                            <input type="text" name="FechaRetiro" id="FechaRetiro" placeholder="" class="form-control calendar" title="FechaRetiro" value="<?= $frm_IngresosExperiencias['FechaRetiro'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Causa Retiro</label>
                            <select name="CausaRetiro" id="CausaRetiro" class="form-control" title="CausaRetiro">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_CausaRetiro = SIMVistasLuker::get_data_vista_luker('vlk_causa_retiro');
                                foreach ($arr_CausaRetiro as $CausaRetiro) {
                                    $selected = ($frm_IngresosExperiencias['CausaRetiro'] == $CausaRetiro['id']) ? "selected" : ""; ?>
                                    <option value="<?= $CausaRetiro['id']; ?>" <?= $selected ?>><?= $CausaRetiro['value']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Jefe Inmediato</label>
                            <input type="text" name="JefeInmediato" id="JefeInmediato" placeholder="" class="form-control" title="Jefe Inmediato" value="<?= $frm_IngresosExperiencias['JefeInmediato'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Cargo Desempeñado</label>
                            <input type="text" name="CargoDesempenado" id="CargoDesempenado" placeholder="" class="form-control" title="Cargo Desempeñado" value="<?= $frm_IngresosExperiencias['CargoDesempenado'] ?>">
                            <input type="hidden" name="IDIngresosExperiencias" id="IDIngresosExperiencias" value="<?= $frm_IngresosExperiencias['IDIngresosExperiencias'] ?>" />

                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Anexo: diploma o acta</label>
                            <input type="file" name="AnexoExperiencia" id="AnexoExperiencia" class="form-control" title="Anexo: diploma o acta">
                            <input type="hidden" name="AnexoExperiencia1" id="AnexoExperiencia1" class="mandatory" title="Anexo: diploma o acta" value="<?= $frm_IngresosExperiencias['AnexoExperiencia'] ?>">
                            <?php if (!empty($frm_IngresosExperiencias['AnexoExperiencia'])) : ?>
                                <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_IngresosExperiencias['AnexoExperiencia'] ?>">Ver archivo</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <hr>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="form-group first ">

                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm_IngresosExperiencias['IDIngresosExperiencias'] ?>" />
                                <input type="hidden" name="action" id="action" value="Experiencias" />
                                <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                                <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmExperiencias<?php echo $i; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_IngresosExperiencias['IDIngresosExperiencias'] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_IngresosExperiencias['IDIngresosExperiencias'] ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php $i++;
    }

    if ($ContRows == 0) {
        echo "No se ingresaron experiencias";
    }

    ?>


</div>