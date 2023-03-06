<div class="content-form form2">
    <h3>Beneficiarios</h3>
    <?php
    $i = 1;
    $ContRows = $dbo->rows($q_IngresosBeneficiarios);

    while ($frm_IngresosBeneficiarios = $dbo->assoc($q_IngresosBeneficiarios)) {
    ?>
        <form class="form-horizontal formvalida" role="form" method="post" id="frmBeneficiarios<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
            <div class="col-xs-12 col-10 form-content">
                <div class="col-xs-12 col-sm-12">
                    <br><br>
                    <h3>Beneficiario <?= $i ?></h3>
                </div>
                <div class="col-xs-12 col-sm-12 bg-gray">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Primer apellido</label>
                            <input type="text" name="PrimerApellido" id="PrimerApellido" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios['PrimerApellido'] ?>" title="Primer Apellido">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Segundo Apellido</label>
                            <input type="text" name="SegundoApellido" id="SegundoApellido" class="form-control " value="<?= $frm_IngresosBeneficiarios['SegundoApellido'] ?>" title="Segundo Apellido">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Nombre Completo</label>
                            <input type="text" name="NombreCompleto" id="NombreCompleto" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios['NombreCompleto'] ?>" title="Nombre Completo">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Dirección*</label>
                            <input type="text" name="Direccion" id="Direccion" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios['Direccion'] ?>" title="Direccion">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Telefono</label>
                            <input type="" name="Telefono" id="Telefono" class="form-control" value="<?= $frm_IngresosBeneficiarios['Telefono'] ?>" title="Telefono">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Parentesco</label>
                            <select name="Parentesco" id="Parentesco" class="form-control mandatory" title="Parentesco">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Parentesco = SIMVistasLuker::get_data_vista_luker('vlk_relac_fam');
                                foreach ($arr_Parentesco as $Parentesco) {
                                    $selected = ($frm_IngresosBeneficiarios['Parentesco'] == $Parentesco['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Parentesco['id'] ?>" <?= $selected ?>><?= $Parentesco['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Genero</label>
                            <select name="Genero" id="Genero" class="form-control mandatory" title="Genero">
                                <option value="">Seleccione</option>
                                <option value="MAS" <?= ($frm_IngresosBeneficiarios['Genero'] == 'MAS') ? "selected" : "" ?>>Masculino</option>
                                <option value="FEM" <?= ($frm_IngresosBeneficiarios['Genero'] == "FEM") ? "selected" : "" ?>>Femenino</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Estado Civil</label>
                            <select name="EstadoCivil" id="EstadoCivil" class="form-control mandatory" title="Estado Civil">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_EstadoCivil = SIMVistasLuker::get_data_vista_luker('VLK_ESTCIV_ATG');
                                foreach ($arr_EstadoCivil as $EstadoCivil) {
                                    $selected = ($frm_IngresosBeneficiarios['EstadoCivil'] == $EstadoCivil['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $EstadoCivil['id'] ?>" <?= $selected ?>><?= $EstadoCivil['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Grupo sanguineo</label>
                            <input type="text" name="GrupoSanguineo" id="GrupoSanguineo" class="form-control" value="<?= $frm_IngresosBeneficiarios['GrupoSanguineo'] ?>" title="Grupo sanguineo">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Sangre RH*</label>
                            <input type="text" name="RH" id="RH" class="form-control" value="<?= $frm_IngresosBeneficiarios['RH'] ?>" title="Sangre RH">
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Fecha Nacimiento</label>
                            <input type="date" name="FechaNacimiento" id="FechaNacimiento" class="form-control mandatory calendar" value="<?= $frm_IngresosBeneficiarios['FechaNacimiento'] ?>" title="Fecha Nacimiento">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Pa&iacute;s Nacimiento</label>
                            <select name="PaisNacimiento" id="PaisNacimiento" class="form-control mandatory" title="Pais Nacimiento">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Pais = SIMVistasLuker::get_data_vista_luker('vlk_paises_atg');
                                foreach ($arr_Pais as $Pais) {
                                    $selected = ($frm_IngresosBeneficiarios['PaisNacimiento'] == $Pais['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col col-xs-12 col-md-6 depto-nacimiento">
                            <label class="col-sm-6">Departamento Nacimiento</label>
                            <select name="DeptoNacimiento" id="DeptoNacimiento" class="form-control " title="Departamento Nacimiento">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Depto = SIMVistasLuker::get_data_vista_luker('vlk_depto_atg');
                                foreach ($arr_Depto as $Depto) {
                                    $selected = ($frm_IngresosBeneficiarios['DeptoNacimiento'] == $Depto['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Depto['id'] ?>" <?= $selected ?>><?= $Depto['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col col-xs-12 col-md-6 ciudad-nacimiento">
                            <label class="col-sm-6">Municipio Nacimiento</label>
                            <select name="MunicipioNacimiento" id="MunicipioNacimiento" class="form-control" title="Municipio Nacimiento">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Ciudad = SIMVistasLuker::get_data_vista_luker('vlk_ciudad_atg');
                                foreach ($arr_Ciudad as $Ciudad) {
                                    $selected = ($frm_IngresosBeneficiarios['MunicipioNacimiento'] == $Ciudad['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Municipio Nacimiento</label>
                            <input type="text" name="MunicipioNacimientoExt" id="MunicipioNacimientoExt" class="form-control" value="<?= $frm_IngresosBeneficiarios['MunicipioNacimientoExt'] ?>" title="Municipio Nacimiento">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Tipo Documento</label>
                            <select name="TipoDocumento" id="TipoDocumento" class="form-control mandatory" title="Tipo Documento">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_TipoIdentificacion = SIMVistasLuker::get_data_vista_luker('vlk_tip_doc_ident');
                                foreach ($arr_TipoIdentificacion as $TipoIdentificacion) {
                                    $selected = ($frm_IngresosBeneficiarios['TipoDocumento'] == $TipoIdentificacion['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $TipoIdentificacion['id'] ?>" <?= $selected ?>><?= $TipoIdentificacion['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Numero Documento</label>
                            <input type="text" name="NumeroDocumento" id="NumeroDocumento" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios['NumeroDocumento'] ?>" title="Numero Documento">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Pa&iacute;s Expedicion</label>
                            <select name="PaisExpedicion" id="PaisExpedicion" class="form-control " title="Pais Expedicion">
                                <option value="">Seleccione</option>
                                <?php
                                foreach ($arr_Pais as $Pais) {
                                    $selected = ($frm_IngresosBeneficiarios['PaisExpedicion'] == $Pais['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="col-xs-12 col-md-6 depto-expedicion">
                            <label class="col-sm-6">Departamento Expedicion</label>
                            <select name="DeptoExpedicion" id="DeptoExpedicion" class="form-control" title="Departamento Expedicion">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Depto = SIMVistasLuker::get_data_vista_luker('vlk_depto_atg');
                                foreach ($arr_Depto as $Depto) {
                                    $selected = ($frm_IngresosBeneficiarios['DeptoExpedicion'] == $Depto['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Depto['id'] ?>" <?= $selected ?>><?= $Depto['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6 ciudad-expedicion">
                            <label class="col-sm-6">Municipio Expedici&oacute;n</label>
                            <select name="MunicipioExpedicion" id="MunicipioExpedicion" class="form-control" title="Municipio Expedicion">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Ciudad = SIMVistasLuker::get_data_vista_luker('vlk_ciudad_atg');
                                foreach ($arr_Ciudad as $Ciudad) {
                                    $selected = ($frm_IngresosBeneficiarios['MunicipioExpedicion'] == $Ciudad['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Municipio Expedici&oacute;n</label>
                            <input type="text" name="MunicipioExpedicionExt" id="MunicipioExpedicionExt" class="form-control" value="<?= $frm_IngresosBeneficiarios['MunicipioExpedicionExt'] ?>" title="Municipio Expedicion">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Profesion</label>
                            <select name="Profesion" id="Profesion" class="form-control mandatory" title="Profesion">
                                <option value="">Seleccione</option>
                                <?php
                                $arr_Profesion = SIMVistasLuker::get_data_vista_luker('vlk_profesiones_atg');
                                foreach ($arr_Profesion as $Profesion) {
                                    $selected = ($frm_IngresosBeneficiarios['Parentesco'] == $Profesion['id']) ? "selected" : "";
                                ?>
                                    <option value="<?= $Profesion['id'] ?>" <?= $selected ?>><?= $Profesion['value'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Reside con el empleado?</label>
                            <select name="ResideConEmpleado" id="ResideConEmpleado" class="form-control" title="Reside con el empleado?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['ResideConEmpleado'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['ResideConEmpleado'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Depende economicamente?</label>
                            <select name="DependeEconomicamente" id="DependeEconomicamente" class="form-control" value="<?= $frm_IngresosBeneficiarios[''] ?>" title="Depende economicamente?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['DependeEconomicamente'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['DependeEconomicamente'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Afiliar a Caja de Compensación?</label>
                            <select name="AfiliarACaja" id="AfiliarACaja" class="form-control mandatory" title="Afiliar a Caja de Compensación?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['AfiliarACaja'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['AfiliarACaja'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Es beneficiario en salud?</label>
                            <select name="BeneficiarioSalud" id="BeneficiarioSalud" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios[''] ?>" title="Es beneficiario en salud?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['BeneficiarioSalud'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['BeneficiarioSalud'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Tiene alguna enfermedad diagnosticada?</label>
                            <select name="EnfermedadDiagnosticada" id="EnfermedadDiagnosticada" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios[''] ?>" title="Tiene alguna enfermedad diagnosticada?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['EnfermedadDiagnosticada'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['EnfermedadDiagnosticada'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6 cual-enfermedad">
                            <label class="col-sm-6">Cual?</label>
                            <input type="text" name="Cual" id="Cual" class="form-control " value="<?= $frm_IngresosBeneficiarios['Cual'] ?>" title="Cual?">
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label class="col-sm-6">Tiene algun tipo de discapacidad?</label>
                            <select name="TieneDiscapacidad" id="TieneDiscapacidad" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios[''] ?>" title="Tiene algun tipo de discapacidad?">
                                <option value="">Seleccione</option>
                                <option value="S" <?= $selected = ($frm_IngresosBeneficiarios['TieneDiscapacidad'] == 'S') ? "selected" : "" ?>>Si</option>
                                <option value="N" <?= $selected = ($frm_IngresosBeneficiarios['TieneDiscapacidad'] == 'N') ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-6 porcentaje-discapacidad">
                            <label class="col-sm-6">% discapacidad</label>
                            <input type="number" max="3" name="PorcentajeDiscapacidad" id="PorcentajeDiscapacidad" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios['PorcentajeDiscapacidad'] ?>" title="% discapacidad">
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <label class="col-sm-6">Anexo(PDF):
                                <br>
                                Menores de 7 años: Registro civil de nacimiento
                                <br>
                                Mayores de 7 años: tarjeta de identidad
                                <br>
                                Mayores de 18: documento de identidad.
                                <br>
                                Si los padres son beneficiarios, el empleado debe adjuntar su registro civil para verificar parentesco
                                <br>

                            </label>
                            <input type="file" name="AnexoBeneficiario" id="AnexoBeneficiario" class="form-control mandatory" value="<?= $frm_IngresosBeneficiarios[''] ?>" title="Anexo(PDF)">
                            <?php if (!empty($frm_IngresosBeneficiarios['AnexoBeneficiario'])) : ?>
                                <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_IngresosBeneficiarios['AnexoBeneficiario'] ?>">Ver archivo</a>
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
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm_IngresosBeneficiarios['IDIngresosBeneficiarios'] ?>" />
                                <input type="hidden" name="action" id="action" value="Beneficiarios" />
                                <input type="hidden" name="NumeroFormularios" id="NumeroFormularios" value="<?= $i ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                                <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio; ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmBeneficiarios<?php echo $i; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_IngresosBeneficiarios['IDIngresosBeneficiarios'] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_IngresosBeneficiarios['IDIngresosBeneficiarios'] ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php $i++;
    }

    if ($ContRows == 0) {
        echo "No se ingresaron beneficiarios";
    }
    ?>

</div>