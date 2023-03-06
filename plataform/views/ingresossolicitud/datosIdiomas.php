<div class="content-form form2">
    <h3>Idiomas</h3>
    <?php
    $i = 1;
    $ContRows = $dbo->rows($q_IngresosIdiomas);

    while ($frm_IngresosIdiomas = $dbo->assoc($q_IngresosIdiomas)) {
    ?>
        <form class="form-horizontal formvalida" role="form" method="post" id="frmIdiomas<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
            <div class="col-xs-12 col-10 form-content">
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Idioma <?= $i ?></h3>
                </div>

                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">

                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Idioma</label>
                            <select name="Idioma" id="Idioma" class="form-control mandatory" title="Idioma">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Idiomas = SIMVistasLuker::get_data_vista_luker('vlk_idiomas_atg');
                                foreach ($arr_Idiomas as $Idiomas) {
                                    $selected = ($frm_IngresosIdiomas['Idioma'] == $Idiomas['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Idiomas['id'] ?>" <?= $selected ?>><?= utf8_decode($Idiomas['value']) ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Cualidad</label>
                            <select name="Cualidad" id="Cualidad" class="form-control mandatory" title="Cualidad">
                                <option value="">Seleccione</option>
                                <option value="ESC" <?= $selected = ($frm_IngresosIdiomas['Cualidad'] == 'ESC') ? "selected" : ""; ?>>ESCRIBE</option>
                                <option value="ELH" <?= $selected = ($frm_IngresosIdiomas['Cualidad'] == 'ELH') ? "selected" : ""; ?>>ESCRIBE-LEE-HABLA</option>
                                <option value="HAB" <?= $selected = ($frm_IngresosIdiomas['Cualidad'] == 'HAB') ? "selected" : ""; ?>>HABLA</option>
                                <option value="LEE" <?= $selected = ($frm_IngresosIdiomas['Cualidad'] == 'LEE') ? "selected" : ""; ?>>LEE</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Cualidad</label>
                            <select name="Estado" id="Estado" class="form-control mandatory" title="Estado">
                                <option value="">Seleccione</option>
                                <option value="AVZ" <?= $selected = ($frm_IngresosIdiomas['Estado'] == 'AVZ') ? "selected" : ""; ?>>AVANZADO</option>
                                <option value="BSC" <?= $selected = ($frm_IngresosIdiomas['Estado'] == 'BSC') ? "selected" : ""; ?>>BASICO</option>
                                <option value="BIE" <?= $selected = ($frm_IngresosIdiomas['Estado'] == 'BIE') ? "selected" : ""; ?>>BIEN</option>
                                <option value="MDO" <?= $selected = ($frm_IngresosIdiomas['Estado'] == 'MDO') ? "selected" : ""; ?>>MEDIO</option>
                                <option value="REG" <?= $selected = ($frm_IngresosIdiomas['Estado'] == 'REG') ? "selected" : ""; ?>>REGULAR</option>
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
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm_IngresosIdiomas['IDIngresosIdiomas'] ?>" />
                                <input type="hidden" name="action" id="action" value="Idiomas" />
                                <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                                <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio; ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmIdiomas<?php echo $i; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_IngresosIdiomas['IDIngresosIdiomas'] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_IngresosIdiomas['IDIngresosIdiomas'] ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php $i++;
    }
    if ($ContRows == 0) {
        echo "No se ingresaron idiomas";
    }


    ?>

</div>