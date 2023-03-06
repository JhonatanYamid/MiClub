<div class="content-form form2">
    <h3>Experiencia</h3>
    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
        <div class="col-xs-12 col-10 form-content">
            <div class="col-xs-12 col-sm-12">
                <h4 class="col-12">
                    <strong>
                        Ingrese estos datos de sus ultimos 4 empleos
                    </strong>
                </h4>
            </div>
            <?php
            foreach ($frm_Experiencias as $i => $Experiencia) :
            ?>
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Empleo <?= $i + 1 ?></h3>
                </div>
                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label for="">Empresa <?= $Experiencia['Empresa'] ?></label>
                            <input type="text" name="Empresa<?= $i + 1 ?>" id="Empresa" placeholder="" class="form-control mandatory" title="Empresa" value="<?= $Experiencia['Empresa'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Dirección</label>
                            <input type="text" name="Direccion<?= $i + 1 ?>" id="Direccion" placeholder="" class="form-control" title="Direccion" value="<?= $Experiencia['Direccion'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Tel&eacute;fono</label>
                            <input type="text" name="Telefono<?= $i + 1 ?>" id="Telefono" placeholder="" class="form-control" title="Telefono" value="<?= $Experiencia['Telefono'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Fecha Ingreso</label>
                            <input type="text" name="FechaIngreso<?= $i + 1 ?>" id="FechaIngreso" placeholder="" class="form-control calendar mandatory" title="FechaIngreso" value="<?= $Experiencia['FechaIngreso'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label for="">Fecha Retiro</label>
                            <input type="text" name="FechaRetiro<?= $i + 1 ?>" id="FechaRetiro" placeholder="" class="form-control calendar" title="FechaRetiro" value="<?= $Experiencia['FechaRetiro'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Causa Retiro</label>
                            <select name="CausaRetiro<?= $i + 1 ?>" id="CausaRetiro" class="form-control" title="CausaRetiro">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_CausaRetiro = SIMVistasLuker::get_data_vista_luker('vlk_causa_retiro');
                                foreach ($arr_CausaRetiro as $CausaRetiro) {
                                    $selected = ($Experiencia['CausaRetiro'] == $CausaRetiro['id']) ? "selected" : ""; ?>
                                    <option value="<?= $CausaRetiro['id']; ?>" <?= $selected ?>><?= $CausaRetiro['value']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Jefe Inmediato</label>
                            <input type="text" name="JefeInmediato<?= $i + 1 ?>" id="JefeInmediato" placeholder="" class="form-control" title="Jefe Inmediato" value="<?= $Experiencia['JefeInmediato'] ?>">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="">Cargo Desempeñado</label>
                            <input type="text" name="CargoDesempenado<?= $i + 1 ?>" id="CargoDesempenado" placeholder="" class="form-control" title="Cargo Desempeñado" value="<?= $Experiencia['CargoDesempenado'] ?>">
                            <input type="hidden" name="IDIngresosExperiencias<?= $i + 1 ?>" id="IDIngresosExperiencias" value="<?= $Experiencia['IDIngresosExperiencias'] ?>" />

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
                            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                            <input type="hidden" name="action" id="action" value="Experiencias" />
                            <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i + 1 ?>" />
                            <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                            <input type="hidden" name="IDSocio" id="IDSocio" value="<?= SIMUser::get('IDSocio'); ?>" />
                            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                <?php echo $titulo_accion; ?>
                            </button>
                            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>