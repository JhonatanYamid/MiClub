<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="form-group first ">
                                <div class="col-md-6">
                                    <h3 class="widget-title grey lighter">

                                        I. DATOS PERSONALES
                                    </h3>
                                </div>
                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label no-padding-right" for="EMP_CEDULA"> Cédula* : </label>
                                    <input type="hidden" id="EMP_CODIGO" name="EMP_CODIGO" placeholder="" class="" title="EMP_CODIGO">
                                    <div class="col-sm-8"> <input type="text" id="EMP_CEDULA" name="EMP_CEDULA" placeholder="" class="form-control" title="EMP_CEDULA" value="<?php echo $frm["EMP_CEDULA"]; ?>"></div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for=" EMP_APELLIDO1"> Primer apellido* </label>
                                    <div class="col-sm-8"><input type="text" id="EMP_APELLIDO1" name="EMP_APELLIDO1" placeholder="" class="form-control" title="APELLIDO1" value="<?php echo $frm["EMP_APELLIDO1"]; ?>"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_APELLIDO2"> Segundo apellido </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_APELLIDO2" name="EMP_APELLIDO2" placeholder="" class="form-control" title="APELLIDO2" value="<?php echo $frm["EMP_APELLIDO2"]; ?>"></div>

                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_NOMBRE"> Nombre completo </label>
                                    <div class="col-sm-8"><input type="text" id="EMP_NOMBRE" name="EMP_NOMBRE" placeholder="" class="form-control" title="NOMBRE" value="<?php echo $frm["EMP_NOMBRE"]; ?>"></div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_SANGRE_RH"> Sangre RH* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" title="Sangre RH" type="radio" name="EMP_SANGRE_RH" id="EMP_SANGRE_RH" value="+" required <?php echo ($frm["EMP_SANGRE_RH"] == "+" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_SANGRE_RH">
                                            +
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" title="Sangre RH" type="radio" name="EMP_SANGRE_RH" id="EMP_SANGRE_RH" value="-" <?php echo ($frm["EMP_SANGRE_RH"] == "-" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_SANGRE_RH">
                                            -
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_TIPO_SANGRE"> Grupo sanguíneo* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" title="Grupo sanguíneo" name="EMP_TIPO_SANGRE" id="EMP_TIPO_SANGRE" value="A" required <?php echo ($frm["EMP_TIPO_SANGRE"] == "A" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_TIPO_SANGRE">
                                            A
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" title="Grupo sanguíneo" name="EMP_TIPO_SANGRE" id="EMP_TIPO_SANGRE" value="B" <?php echo ($frm["EMP_TIPO_SANGRE"] == "B" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_TIPO_SANGRE">
                                            B
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" title="Grupo sanguíneo" name="EMP_TIPO_SANGRE" id="EMP_TIPO_SANGRE" value="AB" <?php echo ($frm["EMP_TIPO_SANGRE"] == "AB" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_TIPO_SANGRE">
                                            AB
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" title="Grupo sanguíneo" name="EMP_TIPO_SANGRE" id="EMP_TIPO_SANGRE" value="O" <?php echo ($frm["EMP_TIPO_SANGRE"] == "O" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="EMP_TIPO_SANGRE">
                                            O
                                        </label>
                                    </div>

                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_ESTADO_CIVIL"> Estado civil* : </label>
                                    <div class="col-sm-8">
                                        <select id="EMP_ESTADO_CIVIL" class="form-control buscador-selector" name="EMP_ESTADO_CIVIL" title="ESTADO CIVIL" required value="<?php echo $frm["EMP_ESTADO_CIVIL"]; ?>">
                                            <option value=""></option>
                                            <option value="Divorciado" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Divorciado") echo "selected"; ?>>Divorciado</option>
                                            <option value="Casado" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Casado") echo "selected"; ?>>Casado</option>
                                            <option value="Union Libre" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Union Libre<") echo "selected"; ?>>Union Libre</option>
                                            <option value="Viudo" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Viudo") echo "selected"; ?>>Viudo</option>
                                            <option value="Soltero" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Soltero") echo "selected"; ?>>Soltero</option>
                                            <option value="Separado" <?php if ($frm["EMP_ESTADO_CIVIL"] == "Separado") echo "selected"; ?>>Separado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="TIEM_CAMPO_ALF1"> Nivel académico* </label>
                                    <div class="col-sm-8">
                                        <select id="TIEM_CAMPO_ALF1" class="form-control buscador-selector" name="TIEM_CAMPO_ALF1" title="Nivel académico" required value="<?php echo $frm["TIEM_CAMPO_ALF1"]; ?>">
                                            <option value="Estudios Primarios">Estudios Primarios</option>
                                            <option value="Tecnico/Profesional">Tecnico/Profesional</option>
                                            <option value="Especializcion/Postgrado">Especializcion/Postgrado</option>
                                            <option value="Bachiller">Bachiller</option>

                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_PROFESION"> Profesión* : </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_PROFESION" name="EMP_PROFESION" placeholder=" Profesión" class="form-control" title="EMP_PROFESION" value="<?php echo $frm["EMP_PROFESION"]; ?>"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_MATRICULA_PROFESIONAL"> Tarjeta profesional </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_MATRICULA_PROFESIONAL" name="EMP_MATRICULA_PROFESIONAL" placeholder="" class="form-control" title="Tarjeta Profesional" maxlength="15" value="<?php echo $frm["EMP_MATRICULA_PROFESIONAL"]; ?>"></div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="UGN1_CODIGO_RESID"> País residencia* </label>
                                    <div class="col-sm-8"> <input type="text" id="UGN1_CODIGO_RESID" name="UGN1_CODIGO_RESID" placeholder=" " class="form-control" title="UGN1_CODIGO_RESID" value="<?php echo $frm["UGN1_CODIGO_RESID"]; ?>"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <div id="DIV_UGN2_CODIGO_RESID"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <div id="DIV_UGN3_CODIGO_RESID"></div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="TIEM_CAMPO_ALF4"> Zona vivienda* </label>
                                    <div class="col-sm-8">
                                        <select id="TIEM_CAMPO_ALF4" class="form-control buscador-selector" name="TIEM_CAMPO_ALF4" title="Zona vivienda" required value="<?php echo $frm["TIEM_CAMPO_ALF4"]; ?>">
                                            <option value=""></option>
                                            <option value="Rural" <?php if ($frm["TIEM_CAMPO_ALF4"] == "Rural") echo "selected"; ?>>Rural</option>
                                            <option value="Urbana" <?php if ($frm["TIEM_CAMPO_ALF4"] == "Urbana") echo "selected"; ?>>Urbana</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_DIRECCION"> Dirección* </label>
                                    <div class="col-sm-8"><input type="text" id="EMP_DIRECCION" name="EMP_DIRECCION" placeholder=" Direccion" class="form-control" title="Dirección" maxlength="250" value="<?php echo $frm["EMP_DIRECCION"]; ?>"></div>

                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_BARRIO"> Barrio* </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_BARRIO" name="EMP_BARRIO" placeholder="Barrio" class="form-control" title="Barrio" maxlength="20" required value="<?php echo $frm["EMP_BARRIO"]; ?>"></div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_TELEFONO"> Teléfono celular* </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_TELEFONO" name="EMP_TELEFONO" placeholder="" class="form-control" title="Teléfono celular" maxlength="30" required value="<?php echo $frm["EMP_TELEFONO"]; ?>"></div>

                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_ALF1"> Correo electrónico personal* </label>
                                    <div class="col-sm-8"> <input type="text" id="BENEF_CAMPO_ALF1" name="BENEF_CAMPO_ALF1" placeholder="" class="form-control" title="Correo electrónico personal" maxlength="30" required value="<?php echo $frm["BENEF_CAMPO_ALF1"]; ?>"></div>
                                </div>


                            </div>
                            <!--  <div class="row">
                    <div id="div_localidad" class="form-group col-md-6">
                        <?php if ($frm["UGN1_CODIGO_RESID"] == 170 && $frm["UGN2_CODIGO_RESID"] == 11 && $frm["UGN3_CODIGO_RESID"] == 1) : ?>
                            <label for="form-field-1"> Localidad Bogotá* </label>
                            <select id="TIEM_CAMPO_ALF3" class="form-control buscador-selector" name="TIEM_CAMPO_ALF3" title="Localidad">
                                <option value=""></option>
                                <?php foreach ($localidades as $value) :
                                    $id = explode("-", $value['id'])[0];
                                    echo '<option';
                                    echo ($frm["TIEM_CAMPO_ALF3"] == $id ? ' selected' : "");
                                    echo ' value="' . $value['id'] . '">' . $value['value'] . '</option>';
                                endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>-->
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <h3 class="widget-title grey lighter">

                                        II. EN CASO DE EMERGENCIA CONTACTAR
                                    </h3>
                                </div>
                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_PERSONA_ACUDIENTE"> Nombre acudiente* </label>
                                    <div class="col-sm-8"><input type="text" id="EMP_PERSONA_ACUDIENTE" name="EMP_PERSONA_ACUDIENTE" placeholder="" class="form-control" title="Nombre acudiente" maxlength="50" required value="<?php echo $frm["EMP_PERSONA_ACUDIENTE"]; ?>"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_TELEFONO_ACUDIENTE"> Teléfono acudiente* </label>
                                    <div class="col-sm-8"><input type="text" id="EMP_TELEFONO_ACUDIENTE" name="EMP_TELEFONO_ACUDIENTE" placeholder="" class="form-control" title="Teléfono acudiente" maxlength="35" required value="<?php echo $frm["EMP_TELEFONO_ACUDIENTE"]; ?>"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_DIRECCION_ACUDIENTE"> Dirección acudiente* </label>
                                    <div class="col-sm-8"> <input type="text" id="EMP_DIRECCION_ACUDIENTE" name="EMP_DIRECCION_ACUDIENTE" placeholder="" class="form-control" title="Dirección acudiente" maxlength="30" value="<?php echo $frm["EMP_DIRECCION_ACUDIENTE"]; ?>"></div>

                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <h3 class="widget-title grey lighter">

                                        III. DATOS SOCIO-ECONÓMICOS
                                    </h3>
                                </div>

                            </div>
                            <div class="form-group first">

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_VIVIENDA"> ¿Tiene vivienda propia?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="EMP_VIVIENDA" id="EMP_VIVIENDA" value="S" required <?php echo ($frm["EMP_VIVIENDA"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="EMP_VIVIENDA" id="EMP_VIVIENDA" value="N" <?php echo ($frm["EMP_VIVIENDA"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="EMP_ADQ_EMPRESA"> ¿Adquirida por medio de Luker?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="EMP_ADQ_EMPRESA" id="EMP_ADQ_EMPRESA" value="S" required <?php echo ($frm["EMP_ADQ_EMPRESA"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="EMP_ADQ_EMPRESA" id="EMP_ADQ_EMPRESA" value="N" <?php echo ($frm["EMP_ADQ_EMPRESA"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>



                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="TIEM_CAMPO_ALF2"> Rol en la familia </label>
                                    <div class="col-sm-8">
                                        <select id="TIEM_CAMPO_ALF2" class="form-control buscador-selector" name="TIEM_CAMPO_ALF2" title="Rol en la familia" value="<?php echo $frm["TIEM_CAMPO_ALF2"]; ?>">
                                            <option value="Padre" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Padre") echo "selected"; ?>>Padre</option>
                                            <option value="Otro" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Otro") echo "selected"; ?>>Otro</option>
                                            <option value="Madre" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Madre") echo "selected"; ?>>Madre</option>
                                            <option value="Hijo" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Hijo") echo "selected"; ?>>Hijo</option>
                                            <option value="Hermano" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Hermano") echo "selected"; ?>>Hermano</option>
                                            <option value="Esposo(A)" <?php if ($frm["TIEM_CAMPO_ALF2"] == "Esposo(A)") echo "selected"; ?>>Esposo(A)</option>
                                        </select>
                                    </div>

                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_IND1"> ¿Es cabeza de hogar?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND1" id="BENEF_CAMPO_IND1" value="S" required <?php echo ($frm["BENEF_CAMPO_IND1"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND1" id="BENEF_CAMPO_IND1" value="N" <?php echo ($frm["BENEF_CAMPO_IND1"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_IND2"> ¿Le han diagnosticado alguna enfermedad?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND2" id="BENEF_CAMPO_IND2_S" value="S" required <?php echo ($frm["BENEF_CAMPO_IND2"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND2" id="BENEF_CAMPO_IND2_N" value="N" <?php echo ($frm["BENEF_CAMPO_IND2"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">


                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_ALF5"> Cual Enfermedad* </label>
                                    <div class="col-sm-8"> <input type="text" id="BENEF_CAMPO_ALF5" name="BENEF_CAMPO_ALF5" placeholder="" class="form-control" title="Enfermedad diagnosticada" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF5"]; ?>"></div>

                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for=" BENEF_CAMPO_IND3"> ¿Tiene algún tipo de discapacidad?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND3" id="BENEF_CAMPO_IND3_S" value="S" required <?php echo ($frm["BENEF_CAMPO_IND3_S"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND3" id="BENEF_CAMPO_IND3_N" value="N" <?php echo ($frm["BENEF_CAMPO_IND3_N"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">


                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_NUM1"> Cual Discapacidad* </label>
                                    <div class="col-sm-8"> <input type="text" id="BENEF_CAMPO_NUM1" name="BENEF_CAMPO_NUM1" placeholder="" class="form-control" title="Discapacidad" value="<?php echo $frm["BENEF_CAMPO_NUM1"]; ?>"></div>


                                </div>



                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_IND5"> ¿Tiene interés en adquirir vivienda?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND5" id="BENEF_CAMPO_IND5" value="S" required <?php echo ($frm["BENEF_CAMPO_IND5"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND5" id="BENEF_CAMPO_IND5" value="N" <?php echo ($frm["BENEF_CAMPO_IND5"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_IND6"> ¿Comparte domicilio con cónyuge?* </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND6" id="BENEF_CAMPO_IND6" value="S" required <?php echo ($frm["BENEF_CAMPO_IND6"] == "S" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="S">
                                            SI
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="BENEF_CAMPO_IND6" id="BENEF_CAMPO_IND6" value="N" <?php echo ($frm["BENEF_CAMPO_IND6"] == "N" ? "checked" : "") ?>>
                                        <label class="form-check-label" for="N">
                                            NO
                                        </label>
                                    </div>
                                </div>



                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_ALF2"> Clase de vivienda que vive actualmente* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_ALF2" class="form-control buscador-selector" name="BENEF_CAMPO_ALF2" title="¿Qué clase de vivienda vive actualmente?" required value="<?php echo $frm["BENEF_CAMPO_ALF2"]; ?>">
                                            <option value=""></option>
                                            <option value="Propia" <?php if ($frm["BENEF_CAMPO_ALF2"] == "Propia") echo "selected"; ?>>Propia</option>
                                            <option value="Alquilada" <?php if ($frm["BENEF_CAMPO_ALF2"] == "Alquilada") echo "selected"; ?>>Alquilada</option>
                                            <option value="Familiar" <?php if ($frm["BENEF_CAMPO_ALF2"] == "Familiar") echo "selected"; ?>>Familiar</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_ALF3"> Tipo de vivienda que poseé* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_ALF3" class="form-control buscador-selector" name="BENEF_CAMPO_ALF3" title="Tipo de vivienda que poseé" required value="<?php echo $frm["BENEF_CAMPO_ALF3"]; ?>">
                                            <option value=""></option>
                                            <option value="Casa" <?php if ($frm["BENEF_CAMPO_ALF3"] == "Casa") echo "selected"; ?>>Casa</option>
                                            <option value="Apartamento" <?php if ($frm["BENEF_CAMPO_ALF3"] == "Apartamento") echo "selected"; ?>>Apartamento</option>
                                            <option value="Lote" <?php if ($frm["BENEF_CAMPO_ALF3"] == "Lote") echo "selected"; ?>>Lote</option>
                                            <option value="Finca" <?php if ($frm["BENEF_CAMPO_ALF3"] == "Finca") echo "selected"; ?>>Finca</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_NUM3"> Estrato socio-económico* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_NUM3" class="form-control buscador-selector" name="BENEF_CAMPO_NUM3" title="Estrato socio económica" required value="<?php echo $frm["BENEF_CAMPO_NUM3"]; ?>">
                                            <option value=""></option>
                                            <option value="1" <?php if ($frm["BENEF_CAMPO_NUM3"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["BENEF_CAMPO_NUM3"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["BENEF_CAMPO_NUM3"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["BENEF_CAMPO_NUM3"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["BENEF_CAMPO_NUM3"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["BENEF_CAMPO_NUM3"] == "6") echo "selected"; ?>>6</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_NUM2"> ¿Con cuántas personas vive?* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_NUM2" class="form-control buscador-selector" name="BENEF_CAMPO_NUM2" title="¿Con cuántas personas vive?" required value="<?php echo $frm["BENEF_CAMPO_NUM2"]; ?>">
                                            <option value=""></option>
                                            <option value="0" <?php if ($frm["BENEF_CAMPO_NUM2"] == "0") echo "selected"; ?>>0</option>
                                            <option value="1" <?php if ($frm["BENEF_CAMPO_NUM2"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["BENEF_CAMPO_NUM2"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["BENEF_CAMPO_NUM2"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["BENEF_CAMPO_NUM2"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["BENEF_CAMPO_NUM2"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["BENEF_CAMPO_NUM2"] == "6") echo "selected"; ?>>6</option>
                                            <option value="7" <?php if ($frm["BENEF_CAMPO_NUM2"] == "7") echo "selected"; ?>>7</option>
                                            <option value="8" <?php if ($frm["BENEF_CAMPO_NUM2"] == "8") echo "selected"; ?>>8</option>
                                            <option value="9" <?php if ($frm["BENEF_CAMPO_NUM2"] == "9") echo "selected"; ?>>9</option>
                                            <option value="10" <?php if ($frm["BENEF_CAMPO_NUM2"] == "10") echo "selected"; ?>>10</option>
                                            <option value="11" <?php if ($frm["BENEF_CAMPO_NUM2"] == "11") echo "selected"; ?>>11</option>
                                            <option value="12" <?php if ($frm["BENEF_CAMPO_NUM2"] == "12") echo "selected"; ?>>12</option>
                                            <option value="13" <?php if ($frm["BENEF_CAMPO_NUM2"] == "13") echo "selected"; ?>>13</option>
                                            <option value="14" <?php if ($frm["BENEF_CAMPO_NUM2"] == "14") echo "selected"; ?>>14</option>
                                            <option value="15" <?php if ($frm["BENEF_CAMPO_NUM2"] == "15") echo "selected"; ?>>15</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_NUM4"> ¿Cuántas personas dependen económicamente de ud?* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_NUM4" class="form-control buscador-selector" name="BENEF_CAMPO_NUM4" title="¿Cuántas personas dependen económicamente de ud?" required value="<?php echo $frm["BENEF_CAMPO_NUM4"]; ?>">
                                            <option value=""></option>
                                            <option value="0" <?php if ($frm["BENEF_CAMPO_NUM4"] == "0") echo "selected"; ?>>0</option>
                                            <option value="1" <?php if ($frm["BENEF_CAMPO_NUM4"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["BENEF_CAMPO_NUM4"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["BENEF_CAMPO_NUM4"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["BENEF_CAMPO_NUM4"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["BENEF_CAMPO_NUM4"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["BENEF_CAMPO_NUM4"] == "6") echo "selected"; ?>>6</option>
                                            <option value="7" <?php if ($frm["BENEF_CAMPO_NUM4"] == "7") echo "selected"; ?>>7</option>
                                            <option value="8" <?php if ($frm["BENEF_CAMPO_NUM4"] == "8") echo "selected"; ?>>8</option>
                                            <option value="9" <?php if ($frm["BENEF_CAMPO_NUM4"] == "9") echo "selected"; ?>>9</option>
                                            <option value="10" <?php if ($frm["BENEF_CAMPO_NUM4"] == "10") echo "selected"; ?>>10</option>
                                            <option value="11" <?php if ($frm["BENEF_CAMPO_NUM4"] == "11") echo "selected"; ?>>11</option>
                                            <option value="12" <?php if ($frm["BENEF_CAMPO_NUM4"] == "12") echo "selected"; ?>>12</option>
                                            <option value="13" <?php if ($frm["BENEF_CAMPO_NUM4"] == "13") echo "selected"; ?>>13</option>
                                            <option value="14" <?php if ($frm["BENEF_CAMPO_NUM4"] == "14") echo "selected"; ?>>14</option>
                                            <option value="15" <?php if ($frm["BENEF_CAMPO_NUM4"] == "15") echo "selected"; ?>>15</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="BENEF_CAMPO_NUM5"> ¿Cuántos hijos tiene?* </label>
                                    <div class="col-sm-8">
                                        <select id="BENEF_CAMPO_NUM5" class="form-control buscador-selector" name="BENEF_CAMPO_NUM5" title="¿Cuántos hijos tiene?" value="<?php echo $frm["BENEF_CAMPO_NUM5"]; ?>">
                                            <option value=""></option>
                                            <option value="0" <?php if ($frm["BENEF_CAMPO_NUM5"] == "0") echo "selected"; ?>>0</option>
                                            <option value="1" <?php if ($frm["BENEF_CAMPO_NUM5"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["BENEF_CAMPO_NUM5"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["BENEF_CAMPO_NUM5"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["BENEF_CAMPO_NUM5"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["BENEF_CAMPO_NUM5"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["BENEF_CAMPO_NUM5"] == "6") echo "selected"; ?>>6</option>
                                            <option value="7" <?php if ($frm["BENEF_CAMPO_NUM5"] == "7") echo "selected"; ?>>7</option>
                                            <option value="8" <?php if ($frm["BENEF_CAMPO_NUM5"] == "8") echo "selected"; ?>>8</option>
                                            <option value="9" <?php if ($frm["BENEF_CAMPO_NUM5"] == "9") echo "selected"; ?>>9</option>
                                            <option value="10" <?php if ($frm["BENEF_CAMPO_NUM5"] == "10") echo "selected"; ?>>10</option>
                                            <option value="11" <?php if ($frm["BENEF_CAMPO_NUM5"] == "11") echo "selected"; ?>>11</option>
                                            <option value="12" <?php if ($frm["BENEF_CAMPO_NUM5"] == "12") echo "selected"; ?>>12</option>
                                            <option value="13" <?php if ($frm["BENEF_CAMPO_NUM5"] == "13") echo "selected"; ?>>13</option>
                                            <option value="14" <?php if ($frm["BENEF_CAMPO_NUM5"] == "14") echo "selected"; ?>>14</option>
                                            <option value="15" <?php if ($frm["BENEF_CAMPO_NUM5"] == "15") echo "selected"; ?>>15</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Anexar Foto </label>
                                        <input name="Foto" id="Foto" class="" title="Foto" type="file" size="25" style="font-size: 10px" value="<?php echo $frm["Foto"]; ?>">
                                        <div class="col-sm-8">
                                            <? if (!empty($frm["Foto"])) {
                                                echo "<img src='" . BANNERAPP_ROOT . $frm["Foto"] . "' >";
                                            ?>
                                                <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                            <?
                                            } // END if
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">

                                <table class="table">

                                    <thead class="thead-dark">
                                        <div>
                                            <h3>III DATOS BENEFICIARIOS</h3>
                                        </div>
                                        <tr>
                                            <th>Ver</th>
                                            <th>NOMBRE</th>
                                            <th>APELLIDO1</th>
                                            <th>APELLIDO2</th>
                                            <th>PARENTESCO</th>
                                            <th>SEXO</th>

                                        </tr>
                                    </thead>

                                    <tbody id="tabla-beneficiarios">
                                        <?php
                                        $ID = $_GET['id'];
                                        $sql = "SELECT * FROM PrimaderaBeneficiario WHERE IDPrimaderaEmpleados='$ID'";

                                        $query = $dbo->query($sql);
                                        $beneficiariosTable = $dbo->fetch($query);
                                        $beneficiariosTable = isset($beneficiariosTable["IDPrimaderaBeneficiario"]) ? [$beneficiariosTable] : $beneficiariosTable;
                                        foreach ($beneficiariosTable as $beneficiario) {
                                            $beneficiarios[] = $beneficiario;
                                        ?>
                                            <tr>
                                                <td>
                                                    <a href="primaderabeneficiario.php?action=edit&id=<?php echo $beneficiario["IDPrimaderaEmpleados"] ?>&IDPrimaderaBeneficiario=<?php echo $beneficiario["IDPrimaderaBeneficiario"] ?>" class="btn_editar_beneficiario">Ver</a>
                                                </td>

                                                <td><?php echo $beneficiario['NOMBRE'] ?></td>
                                                <td><?php echo $beneficiario['APELLIDO1'] ?></td>
                                                <td><?php echo $beneficiario['APELLIDO2'] ?></td>
                                                <td><?php echo $beneficiario['RELAC_FAM'] ?></td>
                                                <td><?php echo $beneficiario['SEXO'] ?></td>



                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <hr>

                            </div>

                            <div class="form-group col-md-12">

                                <table class="table">

                                    <thead class="thead-dark">
                                        <div>
                                            <h3>III DATOS ESTUDIOS</h3>
                                        </div>
                                        <tr>
                                            <th>Ver</th>
                                            <th>TITULO</th>


                                        </tr>
                                    </thead>

                                    <tbody id="tabla-beneficiarios">
                                        <?php
                                        $ID = $_GET['id'];
                                        $sql = "SELECT * FROM PrimaderaEstudio WHERE IDPrimaderaEmpleados='$ID'";

                                        $query = $dbo->query($sql);
                                        $estudiosTable = $dbo->fetch($query);
                                        $estudiosTable = isset($estudiosTable["IDPrimaderaEstudio"]) ? [$estudiosTable] : $estudiosTable;
                                        foreach ($estudiosTable as $estudio) {
                                            $beneficiarios[] = $estudio;
                                        ?>
                                            <tr>
                                                <td>
                                                    <a href="primaderaestudio.php?action=edit&id=<?php echo $estudio["IDPrimaderaEmpleados"] ?>&IDPrimaderaEstudio=<?php echo $estudio["IDPrimaderaEstudio"] ?>" class="btn_editar_beneficiario">Ver</a>
                                                </td>

                                                <td><?php echo "{$estudio["ESXB_TITULO"]}" ?></td>



                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <hr>

                            </div>





                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
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
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>