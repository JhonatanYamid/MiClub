<div class="col-12">
    <form class="form-horizontal formvalida" role="form" method="post" id="frmDatosPersonales<?php echo $i; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
        <div class="col-xs-12 col-10 form-content">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Primer apellido</label>
                        <input type="text" name="PrimerApellido" id="PrimerApellido" class="form-control mandatory col-sm-8" title="Primer apellido" value="<?= $frm_DatosPersonales['PrimerApellido'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Segundo apellido</label>
                        <input type="text" name="SegundoApellido" id="SegundoApellido" class="form-control col-sm-8" title="Segundo apellido" value="<?= $frm_DatosPersonales['SegundoApellido'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" class="form-control mandatory col-sm-8" title="Nombre" value="<?= $frm_DatosPersonales['Nombre'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-12">
                        <label for="Foto">Foto: debe ser en formato jpg, fondo blanco, camisa oscura, medio cuerpo, no escaneada, no selfie</label>
                        <input type="file" name="Foto" id="Foto" class="form-control col-sm-8" title="Foto: debe ser en formato jpg, fondo blanco, camisa oscura, medio cuerpo, no escaneada, no selfie">
                        <br>
                        <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['Foto'] ?>">Ver archivo</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tipo identificaci&oacute;n</label>
                        <select name="TipoIdentificacion" id="TipoIdentificacion" class="form-control mandatory col-sm-8" title="Tipo identificacion">
                            <option value="">Seleccione</option>

                            <?php
                            $selected = "";
                            $arr_TipoIdentificacion = SIMVistasLuker::get_data_vista_luker('vlk_tip_doc_ident');
                            foreach ($arr_TipoIdentificacion as $TipoIdentificacion) {
                                $selected = ($frm_DatosPersonales['TipoIdentificacion'] == $TipoIdentificacion['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $TipoIdentificacion['id'] ?>" <?= $selected ?>><?= $TipoIdentificacion['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">N&uacute;mero identificaci&oacute;n</label>
                        <input type="text" name="NumeroIdentificacion" id="NumeroIdentificacion" class="form-control mandatory col-sm-8" title="Número identificación" value="<?= $frm_DatosPersonales['NumeroIdentificacion'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Pa&iacute;s expedici&oacute;n</label>
                        <select name="PaisExpedicion" id="PaisExpedicion" class="form-control mandatory col-sm-8" title="Pais Expedicion">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_Pais = SIMVistasLuker::get_data_vista_luker('vlk_paises_atg');

                            foreach ($arr_Pais as $Pais) {
                                $selected = ($frm_DatosPersonales['PaisExpedicion'] == $Pais['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Departamento expedici&oacute;n</label>
                        <select name="DeptoExpedicion" id="DeptoExpedicion" class="form-control col-sm-8" title="Depto Expedicion">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_Depto = SIMVistasLuker::get_data_vista_luker('vlk_depto_atg');

                            foreach ($arr_Depto as $Depto) {
                                $selected = ($frm_DatosPersonales['DeptoExpedicion'] == $Depto['id']) ? "selected" : "";
                                echo $frm_DatosPersonales['DeptoExpedicion'];
                                echo "  ";
                                echo $Depto['id'];

                            ?>
                                <option value="<?= $Depto['id'] ?>" <?= $selected ?>><?= $Depto['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Municipio expedici&oacute;n</label>
                        <select name="MunicipioExpedicion" id="MunicipioExpedicion" class="form-control col-sm-8" title="Municipio Expedicion">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_Ciudad = SIMVistasLuker::get_data_vista_luker('vlk_ciudad_atg');
                            foreach ($arr_Ciudad as $Ciudad) {
                                $selected = ($frm_DatosPersonales['MunicipioExpedicion'] == $Ciudad['id']) ? "selected" : "";

                            ?>
                                <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Municipio expedici&oacute;n</label>
                        <input type="text" name="MunicipioExpedicionExt" id="MunicipioExpedicionExt" class="form-control" title="Municipio expedición" value="<?= $frm_DatosPersonales['MunicipioExpedicionExt'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Fecha expedici&oacute;n</label>
                        <input type="date" name="FechaExpedicion" id="FechaExpedicion" class="form-control calendar mandatory" title="Fecha expedición" value="<?= $frm_DatosPersonales['FechaExpedicion'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-12">
                        <label for="Foto">Anexo: documento de identidad legible</label>
                        <input type="file" name="DocumentoIdentidad" id="DocumentoIdentidad" class="form-control col-sm-8" title="Anexo: documento de identidad legible">
                        <br>
                        <?php if (!empty($frm_DatosPersonales['DocumentoIdentidad'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['DocumentoIdentidad'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12"></div>

            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Genero</label>
                        <select name="Genero" id="Genero" class="form-control mandatory col-sm-8" title="Genero">
                            <option value="">Seleccione</option>
                            <option value="MAS" <?= ($frm_DatosPersonales['Genero'] == "MAS") ? "selected" : ""; ?>>Masculino</option>
                            <option value="FEM" <?= ($frm_DatosPersonales['Genero'] == "FEM") ? "selected" : ""; ?>>Femenino</option>
                        </select>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Grupo sanguineo</label>
                        <input type="text" name="GrupoSanguineo" id="GrupoSanguineo" placeholder="" class="form-control mandatory col-sm-8" title="Grupo sanguineo" value="<?= $frm_DatosPersonales['GrupoSanguineo'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">RH</label>
                        <input type="text" name="RH" id="RH" placeholder="" class="form-control mandatory col-sm-8" title="RH" value="<?= $frm_DatosPersonales['RH'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">

                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Estado civil</label>
                        <select name="EstadoCivil" id="EstadoCivil" class="form-control mandatory col-sm-8" title="Estado civil">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_EstadoCivil = SIMVistasLuker::get_data_vista_luker('VLK_ESTCIV_ATG');
                            foreach ($arr_EstadoCivil as $EstadoCivil) {
                                $selected = ($frm_DatosPersonales['EstadoCivil'] == $EstadoCivil['id']) ? "selected" : "";

                            ?>
                                <option value="<?= $EstadoCivil['id'] ?>" <?= $selected ?>><?= $EstadoCivil['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="Foto">Anexo: copia documento del conyuge, registro civil de matrimonio ó declaracion juramentada según el caso</label>
                        <input type="file" name="DocumentoConyuge" id="DocumentoConyuge" class="form-control col-sm-8" title="Anexo: copia documento del conyuge, registro civil de matrimonio ó declaracion juramentada según el caso">
                        <br>
                        <?php if (!empty($frm_DatosPersonales['DocumentoConyuge'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['DocumentoConyuge'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <h2>Nacimiento</h2>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Pa&iacute;s</label>
                        <select name="PaisNacimiento" id="PaisNacimiento" class="form-control mandatory col-sm-8" title="Pais nacimiento">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Pais as $Pais) {
                                $selected = ($frm_DatosPersonales['PaisNacimiento'] == $Pais['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Departamento</label>
                        <select name="DeptoNacimiento" id="DeptoNacimiento" class="form-control col-sm-8" title="Depto nacimiento">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Depto as $Depto) {
                                $selected = ($frm_DatosPersonales['DeptoNacimiento'] == $Depto['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Depto['id'] ?>" <?= $selected ?>><?= $Depto['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Municipio</label>
                        <select name="MunicipioNacimiento" id="MunicipioNacimiento" class="form-control col-sm-8" title="Municipio Nacimiento">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Ciudad as $Ciudad) {
                                $selected = ($frm_DatosPersonales['MunicipioNacimiento'] == $Ciudad['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Municipio nacimiento</label>
                        <input type="text" name="MunicipioNacimientoExt" id="MunicipioNacimientoExt" class="form-control" title="Municipio nacimiento" value="<?= $frm_DatosPersonales['MunicipioNacimientoExt'] ?>">
                    </div>

                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="FechaNacimiento">Fecha nacimiento</label>
                        <input type="date" name="FechaNacimiento" id="FechaNacimiento" placeholder="Fecha nacimiento" class="form-control calendar mandatory" title="Fecha nacimiento" value="<?= $frm_DatosPersonales['FechaNacimiento'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <h2>Residencia</h2>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Pais Residencia</label>
                        <select name="PaisResidencia" id="PaisResidencia" class="form-control mandatory col-sm-8" title="Pais Residencia">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Pais as $Pais) {
                                $selected = ($frm_DatosPersonales['PaisResidencia'] == $Pais['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Pais['id'] ?>" <?= $selected ?>><?= $Pais['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Departamento Residencia</label>
                        <select name="DepartamentoResidencia" id="DepartamentoResidencia" class="form-control mandatory col-sm-8" title="Departamento Residencia">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Depto as $Depto) {
                                $selected = ($frm_DatosPersonales['DepartamentoResidencia'] == $Depto['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Depto['id'] ?>" <?= $selected ?>><?= $Depto['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>

                    </div>
                </div>
                <div class="form-group first">

                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Municipio Residencia</label>
                        <select name="MunicipioResidencia" id="MunicipioResidencia" class="form-control mandatory col-sm-8" title="Municipio Residencia">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($arr_Ciudad as $Ciudad) {
                                $selected = ($frm_DatosPersonales['MunicipioResidencia'] == $Ciudad['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Ciudad['id'] ?>" <?= $selected ?>><?= $Ciudad['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Localidad Residencia</label>
                        <select name="LocalidadResidencia" id="LocalidadResidencia" class="form-control" title="Localidad Residencia">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_Localidad = SIMVistasLuker::get_data_vista_luker('vlk_loc_bogo_atg');
                            foreach ($arr_Localidad as $Localidad) {
                                $selected = ($frm_DatosPersonales['LocalidadResidencia'] == $Localidad['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Localidad['id'] ?>" <?= $selected ?>><?= $Localidad['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group first">

                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6"> Dirección Residencia* </label>
                        <input type="text" name="DireccionResidencia" id="DireccionResidencia" class="form-control col-sm-8" title="Dirección Residencia" value="<?= $frm_DatosPersonales['DireccionResidencia'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Teléfono Residencia</label>
                        <input type="text" name="TelefonoResidencia" id="TelefonoResidencia" placeholder="" class="form-control mandatory col-sm-8" title="Teléfono Residencia" value="<?= $frm_DatosPersonales['TelefonoResidencia'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Barrio Residencia</label>
                        <input type="text" name="BarrioResidencia" id="BarrioResidencia" placeholder="" class="form-control mandatory col-sm-8" title="Barrio Residencia" value="<?= $frm_DatosPersonales['BarrioResidencia'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tiene vivienda?</label>
                        <select name="TieneVivienda" id="TieneVivienda" class="form-control mandatory col-sm-8" title="Tiene vivienda?">
                            <option value="">Seleccione</option>
                            <option value="S" <?= ($frm_DatosPersonales['TieneVivienda'] == 'S') ? "selected" : ""; ?>>Si</option>
                            <option value="N" <?= ($frm_DatosPersonales['TieneVivienda'] == 'N') ? "selected" : ""; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group first">

                    <!-- <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Adquirida por medio de la empresa?</label>
                        <select name="AdquiridaPorEmpresa" id="AdquiridaPorEmpresa" class="form-control col-sm-8" title="Adquirida por medio de la empresa?">
                            <option value="">Seleccione</option>
                            <option value="S" <?= ($frm_DatosPersonales['AdquiridaPorEmpresa'] == 'S') ? "selected" : ""; ?>>Si</option>
                            <option value="N" <?= ($frm_DatosPersonales['AdquiridaPorEmpresa'] == 'N') ? "selected" : ""; ?>>No</option>
                        </select>
                    </div> -->
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tiene interes de adquirir vivienda?</label>
                        <select name="TieneInteresAdquirirVivienda" id="TieneInteresAdquirirVivienda" class="form-control col-sm-8" title="Tiene interes de adquirir vivienda?">
                            <option value="">Seleccione</option>
                            <option value="S" <?= ($frm_DatosPersonales['TieneInteresAdquirirVivienda'] == 'S') ? "selected" : ""; ?>>Si</option>
                            <option value="N" <?= ($frm_DatosPersonales['TieneInteresAdquirirVivienda'] == 'N') ? "selected" : ""; ?>>No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <h2>Nivel Educativo</h2>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nivel educativo</label>
                        <select name="NivelEducativo" id="NivelEducativo" class="form-control mandatory col-sm-8" title="Nivel Educativo">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_NivelEducativo = SIMVistasLuker::get_data_vista_luker('vlk_nivacademico_atg');
                            foreach ($arr_NivelEducativo as $NivelEducativo) {
                                $selected = ($frm_DatosPersonales['NivelEducativo'] == $NivelEducativo['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $NivelEducativo['id'] ?>" <?= $selected ?>><?= $NivelEducativo['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Profesi&oacute;n</label>
                        <select name="Profesion" id="Profesion" class="form-control mandatory col-sm-8" title="Profesion">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_Profesion = SIMVistasLuker::get_data_vista_luker('vlk_profesiones_atg');
                            foreach ($arr_Profesion as $Profesion) {
                                $selected = ($frm_DatosPersonales['Profesion'] == $Profesion['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $Profesion['id'] ?>" <?= $selected ?>><?= $Profesion['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Matricula/tarjeta Profesional</label>
                        <input type="text" name="Matricula" id="Matricula" placeholder="" class="form-control col-sm-8" title="Matricula/tarjeta Profesional" value="<?= $frm_DatosPersonales['Matricula'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Fecha Matricula</label>
                        <input type="date" name="FechaMatricula" id="FechaMatricula" placeholder="" class="form-control calendar" title="Fecha Matricula" value="<?= $frm_DatosPersonales['FechaMatricula'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Correo electronico personal</label>
                        <input type="text" name="CorreoElectronicoPersonal" id="CorreoElectronicoPersonal" placeholder="" class="form-control mandatory col-sm-8" title="Correo electronico personal" value="<?= $frm_DatosPersonales['CorreoElectronicoPersonal'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-sm-6">
                        <label for="Foto">Anexo: diploma o acta</label>
                        <input type="file" name="DiplomaActa" id="DiplomaActa" class="form-control col-sm-8" title="Anexo: diploma o acta">
                        <br>
                        <?php if (!empty($frm_DatosPersonales['DiplomaActa'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['DiplomaActa'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <label for="Foto">Anexo: tarjeta profesional</label>
                        <input type="file" name="TarjetaProfesional" id="TarjetaProfesional" class="form-control col-sm-8" title="Anexo: tarjeta profesional">
                        <br>
                        <?php if (!empty($frm_DatosPersonales['TarjetaProfesional'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['TarjetaProfesional'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-12">
                        <label for="Autorizacion">
                            <input type="checkbox" name="Autorizacion" id="Autorizacion" title="Autorización" <?= ($frm_DatosPersonales['Autorizacion'] == 'on') ? "checked" : ""; ?>>
                            Autorizo expresamente a CasaLuker S.A. para que me remita información o documentación propia de la relación laboral de manera física a la dirección que he registrado en la compañía o de manera electrónica al correo registrado en este documento y que estos datos de contacto haga parte de la hoja de vida laboral que reposa en la compañía y se tenga como anexo al contrato de trabajo, entendiendo y reconociendo que el recaudo de esta información se da en desarrollo de las obligaciones laborales que mantengo con la compañía.
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Estrato socioeconomico</label>
                        <input type="text" name="EstratoSocioeconomico" id="EstratoSocioeconomico" placeholder="" class="form-control mandatory col-sm-8" title="Estrato socioeconomico" value="<?= $frm_DatosPersonales['EstratoSocioeconomico'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Clase vivienda</label>
                        <select name="ClaseVivienda" id="ClaseVivienda" class="form-control mandatory col-sm-8" title="Clase Vivienda">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_ClaseVivienda = SIMVistasLuker::get_data_vista_luker('vlk_clase_viv_atg');
                            foreach ($arr_ClaseVivienda as $ClaseVivienda) {
                                $selected = ($frm_DatosPersonales['ClaseVivienda'] == $ClaseVivienda['value']) ? "selected" : "";
                            ?>
                                <option value="<?= $ClaseVivienda['value'] ?>" <?= $selected ?>><?= $ClaseVivienda['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tipo Vivienda</label>
                        <select name="TipoVivienda" id="TipoVivienda" class="form-control mandatory col-sm-8" title="Tipo Vivienda">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_TipoVivienda = SIMVistasLuker::get_data_vista_luker('vlk_tipo_viv_atg');
                            foreach ($arr_TipoVivienda as $TipoVivienda) {
                                $selected = ($frm_DatosPersonales['TipoVivienda'] == $TipoVivienda['value']) ? "selected" : "";
                            ?>
                                <option value="<?= $TipoVivienda['value'] ?>" <?= $selected ?>><?= $TipoVivienda['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Zona Vivienda</label>
                        <select name="ZonaVivienda" id="ZonaVivienda" class="form-control mandatory col-sm-8" title="Zona Vivienda">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_ZonaVivienda = SIMVistasLuker::get_data_vista_luker('vlk_zona_viv_atg');
                            foreach ($arr_ZonaVivienda as $ZonaVivienda) {
                                $selected = ($frm_DatosPersonales['ZonaVivienda'] == $ZonaVivienda['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $ZonaVivienda['id'] ?>" <?= $selected ?>><?= $ZonaVivienda['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Tiene alguna enfermedad diagnosticada?</label>
                        <select name="Enfermedad" id="Enfermedad" class="form-control mandatory col-sm-8" title="Enfermedad">
                            <option value="">Seleccione</option>
                            <option value="S" <?= ($frm_DatosPersonales['Enfermedad'] == 'S') ? "selected" : ""; ?>>Si</option>
                            <option value="N" <?= ($frm_DatosPersonales['Enfermedad'] == 'N') ? "selected" : ""; ?>>No</option>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Cual?</label>
                        <input type="text" name="CualEnfermedad" id="CualEnfermedad" placeholder="" class="form-control col-sm-8" title="Cual?" value="<?= $frm_DatosPersonales['CualEnfermedad'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tiene alguna discapacidad?</label>
                        <select name="Discapacidad" id="Discapacidad" class="form-control mandatory col-sm-8" title="Tiene alguna discapacidad?">
                            <option value="">Seleccione</option>
                            <option value="S" <?= ($frm_DatosPersonales['Discapacidad'] == 'S') ? "selected" : ""; ?>>Si</option>
                            <option value="N" <?= ($frm_DatosPersonales['Discapacidad'] == 'N') ? "selected" : ""; ?>>No</option>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Porcentaje discapacidad</label>
                        <input type="text" name="PorcentajeDiscapacidad" id="PorcentajeDiscapacidad" placeholder="" class="form-control col-sm-8" title="Porcentaje discapacidad" value="<?= $frm_DatosPersonales['PorcentajeDiscapacidad'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
            </div>

            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Comparte domicilio con el conyuge?</label>
                        <input type="text" name="ComparteDomicilioConyugue" id="ComparteDomicilioConyugue" placeholder="" class="form-control mandatory col-sm-8" title="Comparte domicilio con el conyuge?" value="<?= $frm_DatosPersonales['ComparteDomicilioConyugue'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Con cuantas personas vive?</label>
                        <input type="text" name="CuantasPersonasVive" id="CuantasPersonasVive" placeholder="" class="form-control mandatory col-sm-8" title="Con cuantas personas vive?" value="<?= $frm_DatosPersonales['CuantasPersonasVive'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Cuantos hijos tiene?</label>
                        <input type="text" name="CuantosHijosTiene" id="CuantosHijosTiene" placeholder="" class="form-control mandatory col-sm-8" title="Cuantos hijos tiene?" value="<?= $frm_DatosPersonales['CuantosHijosTiene'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Es cabeza de hogar?</label>
                        <input type="text" name="CabezaHogar" id="CabezaHogar" placeholder="" class="form-control mandatory col-sm-8" title="Es cabeza de hogar?" value="<?= $frm_DatosPersonales['CabezaHogar'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Rol en la familia</label>
                        <!-- <select name="RolFamilia" id="RolFamilia" class="form-control mandatory col-sm-8" title="Rol Familia">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_RolFamilia = SIMVistasLuker::get_data_vista_luker('vlk_rol_fam_atg');
                            $frm_RolFamilia = explode(',', $frm_DatosPersonales['RolFamilia']);
                            foreach ($arr_RolFamilia as $RolFamilia) {
                                if (in_array($RolFamilia['id'], $frm_RolFamilia)) {
                                    $selected =  "selected";
                                } else {
                                    $selected =  "";
                                }
                            ?>
                                <option value="<?= $RolFamilia['id'] ?>" <?= $selected ?>><?= $RolFamilia['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select> -->
                        <input type="text" name="RolFamilia" id="RolFamilia" placeholder="" class="form-control mandatory col-sm-8" title="Rol en la familia" value="<?= $frm_DatosPersonales['RolFamilia'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6"># dependientes economicos</label>
                        <input type="text" name="DependientesEconomicos" id="DependientesEconomicos" placeholder="" class="form-control mandatory col-sm-8" title="# dependientes economicos" value="<?= $frm_DatosPersonales['DependientesEconomicos'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">EPS</label>
                        <select name="EPS" id="EPS" class="form-control mandatory col-sm-8" title="EPS">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_EPS = SIMVistasLuker::get_data_vista_luker('vlk_eps_atg');
                            foreach ($arr_EPS as $EPS) {
                                $selected = ($frm_DatosPersonales['EPS'] == $EPS['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $EPS['id'] ?>" <?= $selected ?>><?= $EPS['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Anexo(PDF): Certificado activo de eps</label>
                        <input type="file" name="CertificadoEPS" id="CertificadoEPS" class="form-control" title="Anexo(PDF): Certificado activo de eps">
                        <?php if (!empty($frm_DatosPersonales['CertificadoEPS'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['CertificadoEPS'] ?>">Ver archivo</a>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Fondo Pensiones</label>
                        <select name="AFP" id="AFP" class="form-control col-sm-8" title="AFP">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_AFP = SIMVistasLuker::get_data_vista_luker('vlk_afp_atg');
                            foreach ($arr_AFP as $AFP) {
                                $selected = ($frm_DatosPersonales['AFP'] == $AFP['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $AFP['id'] ?>" <?= $selected ?>><?= $AFP['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Anexo(PDF): Certificado activo de afp, no es obligatorio para aprendices o practicante</label>
                        <input type="file" name="CertificadoAFP" id="CertificadoAFP" class="form-control" title="Anexo(PDF): Certificado activo de afp, no es obligatorio para aprendices o practicante">
                        <?php if (!empty($frm_DatosPersonales['CertificadoAFP'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['CertificadoAFP'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Fondo cesantias</label>
                        <select name="FCES" id="FCES" class="form-control" title="FCES">
                            <option value="">Seleccione</option>
                            <?php
                            $arr_FCES = SIMVistasLuker::get_data_vista_luker('vlk_fces_atg');
                            foreach ($arr_FCES as $FCES) {
                                $selected = ($frm_DatosPersonales['FCES'] == $FCES['id']) ? "selected" : "";
                            ?>
                                <option value="<?= $FCES['id'] ?>" <?= $selected ?>><?= $FCES['value'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Anexo(PDF): Certificado activo de fces, no es obligatorio</label>
                        <input type="file" name="CertificadoFCES" id="CertificadoFCES" class="form-control" title="Anexo(PDF): Certificado activo de fces, no es obligatorio">
                        <?php if (!empty($frm_DatosPersonales['CertificadoFCES'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['CertificadoFCES'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <h2>En caso de emergencia avisar a:</h2>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreEmergencia" id="NombreEmergencia" placeholder="" class="form-control mandatory col-sm-8" title="Nombre" value="<?= $frm_DatosPersonales['NombreEmergencia'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Direcci&oacute;n</label>
                        <input type="text" name="DireccionEmergencia" id="DireccionEmergencia" placeholder="" class="form-control mandatory col-sm-8" title="Direccion" value="<?= $frm_DatosPersonales['DireccionEmergencia'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Tel&eacute;fono</label>
                        <input type="text" name="TelefonoEmergencia" id="TelefonoEmergencia" placeholder="" class="form-control mandatory col-sm-8" title="Teléfono" value="<?= $frm_DatosPersonales['TelefonoEmergencia'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Anexo(PDF): Hoja de Vida Actualizada (formato pdf)</label>
                        <input type="file" name="HojaVida" id="HojaVida" class="form-control" title="Anexo(PDF): Hoja de Vida Actualizada (formato pdf)">
                        <?php if (!empty($frm_DatosPersonales['HojaVida'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['HojaVida'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-12">Anexo(PDF): Certificación bancaria (Davivienda o Bancolombia)</label>
                        <input type="file" name="CertificacionBancaria" id="CertificacionBancaria" class="form-control" title="Anexo(PDF): Certificación bancaria (Davivienda o Bancolombia)">
                        <?php if (!empty($frm_DatosPersonales['CertificacionBancaria'])) : ?>
                            <a target="_blank" href="<?= INGRESOS_ROOT . $frm_datosSocio['NumeroDocumento'] . "/" . $frm_DatosPersonales['CertificacionBancaria'] ?>">Ver archivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <h2>Datos Familiares (Personas con las que vive)</h2>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <span>1</span>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreFamiliar1" id="NombreFamiliar1" placeholder="" class="form-control" title="Nombre" value="<?= $frm_DatosPersonales['NombreFamiliar1'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Telefono</label>
                        <input type="text" name="TelefonoFamiliar1" id="TelefonoFamiliar1" placeholder="" class="form-control" title="Telefono" value="<?= $frm_DatosPersonales['TelefonoFamiliar1'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Parentesco</label>
                        <input type="text" name="ParentescoFamiliar1" id="ParentescoFamiliar1" placeholder="" class="form-control" title="Parentesco" value="<?= $frm_DatosPersonales['ParentescoFamiliar1'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12"></div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <span>2</span>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreFamiliar2" id="NombreFamiliar2" placeholder="" class="form-control" title="Nombre" value="<?= $frm_DatosPersonales['NombreFamiliar2'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Telefono</label>
                        <input type="text" name="TelefonoFamiliar2" id="TelefonoFamiliar2" placeholder="" class="form-control" title="Telefono" value="<?= $frm_DatosPersonales['TelefonoFamiliar2'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Parentesco</label>
                        <input type="text" name="ParentescoFamiliar2" id="ParentescoFamiliar2" placeholder="" class="form-control" title="Parentesco" value="<?= $frm_DatosPersonales['ParentescoFamiliar2'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12"></div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <span>3</span>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreFamiliar3" id="NombreFamiliar3" placeholder="" class="form-control" title="Nombre" value="<?= $frm_DatosPersonales['NombreFamiliar3'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Telefono</label>
                        <input type="text" name="TelefonoFamiliar3" id="TelefonoFamiliar3" placeholder="" class="form-control" title="Telefono" value="<?= $frm_DatosPersonales['TelefonoFamiliar3'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Parentesco</label>
                        <input type="text" name="ParentescoFamiliar3" id="ParentescoFamiliar3" placeholder="" class="form-control" title="Parentesco" value="<?= $frm_DatosPersonales['ParentescoFamiliar3'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12"></div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <span>4</span>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreFamiliar4" id="NombreFamiliar4" placeholder="" class="form-control" title="Nombre" value="<?= $frm_DatosPersonales['NombreFamiliar4'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Telefono</label>
                        <input type="text" name="TelefonoFamiliar4" id="TelefonoFamiliar4" placeholder="" class="form-control" title="Telefono" value="<?= $frm_DatosPersonales['TelefonoFamiliar4'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Parentesco</label>
                        <input type="text" name="ParentescoFamiliar4" id="ParentescoFamiliar4" placeholder="" class="form-control" title="Parentesco" value="<?= $frm_DatosPersonales['ParentescoFamiliar4'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12"></div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <span>5</span>
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Nombre</label>
                        <input type="text" name="NombreFamiliar5" id="NombreFamiliar5" placeholder="" class="form-control" title="Nombre" value="<?= $frm_DatosPersonales['NombreFamiliar5'] ?>">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Telefono</label>
                        <input type="text" name="TelefonoFamiliar5" id="TelefonoFamiliar5" placeholder="" class="form-control" title="Telefono" value="<?= $frm_DatosPersonales['TelefonoFamiliar5'] ?>">
                    </div>
                </div>
                <div class="form-group first">
                    <div class="col-xs-12 col-sm-6">
                        <label for="" class="col-sm-6">Parentesco</label>
                        <input type="text" name="ParentescoFamiliar5" id="ParentescoFamiliar5" placeholder="" class="form-control" title="Parentesco" value="<?= $frm_DatosPersonales['ParentescoFamiliar5'] ?>">
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12">
                <hr>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group first ">

                    <div class="clearfix form-actions">
                        <div class="col-xs-12 text-center">
                            <input type="hidden" name="ID" id="ID" value="<?php echo $frm_DatosPersonales['IDIngresosDatosPersonales'] ?>" />
                            <input type="hidden" name="action" id="action" value="datosPersonales" />
                            <input type="hidden" name="IDClub" id="IDClub" value="<?= (empty($frm_get["club"])) ? SIMUser::get("club") : $frm_get["IDClub"];  ?>" />
                            <input type="hidden" name="IDSocio" id="IDSocio" value="<?= $IDSocio; ?>" />
                            <button class="btn btn-info btnEnviar" type="button" rel="frmDatosPersonales<?php echo $i; ?>">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                <?php echo $titulo_accion; ?>
                            </button>
                            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm_DatosPersonales['IDIngresosDatosPersonales'] ?>" />
                            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm_DatosPersonales['IDIngresosDatosPersonales'] ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>