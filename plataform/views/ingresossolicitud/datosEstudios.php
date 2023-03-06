<div class="content-form form2">
    <h3>Estudios</h3>
    <?php
    $i = 1;
    while ($frm_IngresosEstudios = $dbo->assoc($q_IngresosEstudios)) {
    ?>
        <form class="form-horizontal formvalida" role="form" method="post" id="frmEstudios<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
            <div class="col-xs-12 col-10 form-content">
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Estudio <?= $i ?></h3>
                </div>

                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Nivel educativo</label>
                            <select name="NivelEstudio" id="NivelEstudio" class="form-control mandatory" title="Nivel Educativo">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_NivelEducativo = SIMVistasLuker::get_data_vista_luker('VLK_EST_NIVEL_ATG');
                                foreach ($arr_NivelEducativo as $NivelEducativo) {
                                    $selected = ($frm_IngresosEstudios['NivelEstudio'] == $NivelEducativo['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $NivelEducativo['id'] ?>" <?= $selected ?>><?= $NivelEducativo['value'] ?></option>
                                <?php }
                                ?>
                            </select>


                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Pa&iacute;s</label>
                            <select name="Pais" id="Pais" class="form-control mandatory" title="Pais">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Pais = SIMVistasLuker::get_data_vista_luker('vlk_paises_atg');
                                foreach ($arr_Pais as $Pais) {
                                    $selected = ($frm_IngresosEstudios['Pais'] == $Pais['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Departamento</label>
                            <select name="Depto" id="Depto" class="form-control mandatory" title="Departamento">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Departamento = SIMVistasLuker::get_data_vista_luker('vlk_depto_atg');
                                foreach ($arr_Departamento as $Departamento) {
                                    $selected = ($frm_IngresosEstudios['Depto'] == $Departamento['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Departamento['id'] ?>" <?= $selected ?>><?= $Departamento['value'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Ciudad</label>
                            <select name="Ciudad" id="Ciudad" class="form-control mandatory" title="Ciudad">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Ciudad = SIMVistasLuker::get_data_vista_luker('vlk_ciudad_atg');
                                foreach ($arr_Ciudad as $Ciudad) {
                                    $selected = ($frm_IngresosEstudios['Ciudad'] == $Ciudad['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Ciudad</label>
                            <input type="text" name="CiudadExt" id="CiudadExt" class="form-control" value="<?= $frm_IngresosEstudios['CiudadExt'] ?>" title="Ciudad">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Instituci&oacute;n</label>
                            <select name="Institucion" id="Institucion" class="form-control mandatory" title="Institucion">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Institucion = SIMVistasLuker::get_data_vista_luker('vlk_instituciones_atg');
                                foreach ($arr_Institucion as $Institucion) {
                                    $selected = ($frm_IngresosEstudios['Institucion'] == $Institucion['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Institucion['id'] ?>" <?= $selected ?>><?= $Institucion['value'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Instituci&oacute;n descripci&oacute;n adicional</label>
                            <input type="text" name="DescripcionInstitucion" id="DescripcionInstitucion" class="form-control" value="<?= $frm_IngresosEstudios['DescripcionInstitucion'] ?>" title="Institución descripción adicional">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Titulo</label>
                            <select name="Titulo" id="Titulo" class="form-control mandatory" title="Titulo">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Titulo = SIMVistasLuker::get_data_vista_luker('vlk_profesiones_atg');
                                foreach ($arr_Titulo as $Titulo) {
                                    $selected = ($frm_IngresosEstudios['Titulo'] == $Titulo['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Titulo['id'] ?>" <?= $selected ?>><?= $Titulo['value'] ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Titulo descripci&oacute;n adicional</label>
                            <input type="text" name="DescripcionTitulo" id="DescripcionTitulo" class="form-control" value="<?= $frm_IngresosEstudios['DescripcionTitulo'] ?>" title="Titulo descripción adicional">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Fecha Graduacion</label>
                            <input type="date" name="FechaGraduacion" id="FechaGraduacion" class="form-control mandatory calendar" value="<?= $frm_IngresosEstudios['FechaGraduacion'] ?>" title="Fecha Graduacion">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Anexo(PDF): Copia del acta o diploma</label>
                            <input type="file" name="Anexo" id="Anexo" class="form-control mandatory" title="Anexo(PDF): Copia del acta o diploma">
                            <?php if (!empty($frm_IngresosEstudios['Anexo'])) : ?>
                                <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_IngresosEstudios['Anexo'] ?>">Ver archivo</a>
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
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm_IngresosEstudios['IDIngresosEstudios'] ?>" />
                                <input type="hidden" name="action" id="action" value="Experiencias" />
                                <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                                <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmEstudios<?php echo $i; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_IngresosEstudios['IDIngresosEstudios'] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_IngresosEstudios['IDIngresosEstudios'] ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php $i++;
    } ?>

</div>