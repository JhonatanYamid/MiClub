<div class="content-form form2">
    <h3>Deportes</h3>
    <?php
    $i = 1;
    while ($frm_IngresosDeportes = $dbo->assoc($q_IngresosDeportes)) {
    ?>
        <form class="form-horizontal formvalida" role="form" method="post" id="frmDeportes<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
            <div class="col-xs-12 col-10 form-content">
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Deporte <?= $i ?></h3>
                </div>

                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">

                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Deporte</label>
                            <select name="Deporte" id="Deporte" class="form-control mandatory" title="Deporte">
                                <option value="">Seleccione</option>
                                <?php
                                $Deportes = SIMVistasLuker::get_data_vista_luker('vlk_deportes_atg');
                                foreach ($Deportes as $Deporte) {
                                    $selected = ($frm_IngresosDeportes['Deporte'] == $Deporte['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Deporte['id'] ?>" <?= $selected ?>><?= $Deporte['value'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Estado</label>
                            <select name="Estado" id="Estado" class="form-control mandatory" title="Estado">
                                <option value="">Seleccione</option>
                                <option value="Apr" <?= $selected = ($frm_IngresosDeportes['Estado'] == 'Apr') ? "selected" : ""; ?>>Aprenderia</option>
                                <option value="Pra" <?= $selected = ($frm_IngresosDeportes['Estado'] == 'Pra') ? "selected" : ""; ?>>Practica</option>
                                <option value="Rea" <?= $selected = ($frm_IngresosDeportes['Estado'] == 'Rea') ? "selected" : ""; ?>>Realizaria</option>
                            </select>
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
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm_IngresosDeportes['IDIngresosDeportes'] ?>" />
                                <input type="hidden" name="action" id="action" value="Deportes" />
                                <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                                <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio; ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmDeportes<?php echo $i; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_IngresosDeportes['IDIngresosDeportes'] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_IngresosDeportes['IDIngresosDeportes'] ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php $i++;
    } ?>

</div>