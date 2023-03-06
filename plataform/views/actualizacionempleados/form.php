<?php
	$id = $_GET['id'];
	$frm_actualizacion_empleado['IDSocio'] = $_GET['id'];
?> <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<div class="container">
    <div class="">
        <div class="widget-header">
            <h4 class="widget-title lighter smaller">
                <i class="ace-icon fa fa-users orange"></i> Información de empleados
            </h4>
        </div>
        <div class="">
            <div class="">
                <div class="">
                    <div class="">
                        <form class="" role="form" method="post" id="frm_empleado" action="actualizacionempleados.php" enctype="multipart/form-data">
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info green"></i> I. DATOS PERSONALES
                                </h3>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="EMP_CEDULA"> Cédula* : </label>
                                    <input type="hidden" id="EMP_CODIGO" name="EMP_CODIGO" placeholder="" class="" title="EMP_CODIGO" value="<?php echo $frm["EMP_CODIGO"] ?>">
                                    <input type="text" disabled id="EMP_CEDULA" name="EMP_CEDULA" placeholder="" class="form-control" title="CEDULA" value="<?php echo $frm["EMP_CEDULA"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="EMP_APELLIDO1"> Primer apellido* </label>
                                    <input type="text" disabled id="EMP_APELLIDO1" name="EMP_APELLIDO1" placeholder="" class="form-control" title="APELLIDO1" value="<?php echo $frm["EMP_APELLIDO1"] ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_APELLIDO2"> Segundo apellido </label>
                                    <input type="text" disabled id="EMP_APELLIDO2" name="EMP_APELLIDO2" placeholder="" class="form-control" title="APELLIDO2" value="<?php echo $frm["EMP_APELLIDO2"] ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_NOMBRE"> Nombre completo </label>
                                    <input type="text" disabled id="EMP_NOMBRE" name="EMP_NOMBRE" placeholder="" class="form-control" title="NOMBRE" value="<?php echo $frm["EMP_NOMBRE"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="EMP_TIPO_SANGRE"> Grupo sanguíneo* </label>
                                    <input type="text" disabled id="EMP_TIPO_SANGRE" name="EMP_TIPO_SANGRE" placeholder="" class="form-control" title="Tipo sangre" value="<?php echo $frm["EMP_TIPO_SANGRE"] ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_SANGRE_RH"> RH* </label>
                                    <input type="text" disabled id="EMP_SANGRE_RH" name="EMP_SANGRE_RH" placeholder="" class="form-control" title="RH sangre" value="<?php echo $frm["EMP_SANGRE_RH"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="EMP_ESTADO_CIVIL"> Estado civil* : </label>
                                    <input type="text" disabled id="EMP_ESTADO_CIVIL" name="EMP_ESTADO_CIVIL" placeholder="" class="form-control" title="Estado civil" value="<?php echo $frm["EMP_ESTADO_CIVIL_DETALLE"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="TIEM_CAMPO_ALF1"> Nivel académico* </label>
                                    <input type="text" disabled id="TIEM_CAMPO_ALF1" name="TIEM_CAMPO_ALF1" placeholder="" class="form-control" title="Nivel académico" maxlength="30" value="<?php echo $frm["TIEM_CAMPO_ALF1_DETALLE"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="EMP_PROFESION"> Profesión* : </label>
                                    <input type="text" disabled id="EMP_PROFESION" name="EMP_PROFESION" placeholder="" class="form-control" title="Profesión" value="<?php echo $frm["EMP_PROFESION_DETALLE"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="EMP_MATRICULA_PROFESIONAL"> Tarjeta profesional </label>
                                    <input type="text" disabled id="EMP_MATRICULA_PROFESIONAL" name="EMP_MATRICULA_PROFESIONAL" placeholder="" class="form-control" title="Tarjeta Profesional" maxlength="15" value="<?php echo $frm["EMP_MATRICULA_PROFESIONAL"] ?> ">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="UGN1_CODIGO_RESID"> País residencia* </label>
                                    <input type="text" disabled id="UGN1_CODIGO_RESID" name="UGN1_CODIGO_RESID" placeholder="" class="form-control" title="País residencia" maxlength="15" value="<?php echo $frm["UGN1_CODIGO_RESID_DETALLE"] ?> ">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="UGN2_CODIGO_RESID"> Departamento residencia* </label>
                                    <input type="text" disabled id="UGN2_CODIGO_RESID" name="UGN2_CODIGO_RESID" placeholder="" class="form-control" title="Departamento residencia" maxlength="15" value="<?php echo $frm["UGN2_CODIGO_RESID_DETALLE"] ?> ">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="UGN3_CODIGO_RESID"> Ciudad residencia* </label>
                                    <input type="text" disabled id="UGN3_CODIGO_RESID" name="UGN3_CODIGO_RESID" placeholder="" class="form-control" title="Ciudad residencia" maxlength="15" value="<?php echo $frm["UGN3_CODIGO_RESID_DETALLE"] ?> ">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="TIEM_CAMPO_ALF4"> Zona vivienda* </label>
                                    <input type="text" disabled id="TIEM_CAMPO_ALF4" name="TIEM_CAMPO_ALF4" placeholder="" class="form-control" title="Zona vivienda" maxlength="15" value="<?php echo $frm["TIEM_CAMPO_ALF4"] ?> ">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="EMP_DIRECCION"> Dirección* </label>
                                    <input type="text" disabled id="EMP_DIRECCION" name="EMP_DIRECCION" placeholder="" class="form-control" title="Dirección" maxlength="250" value="<?php echo $frm["EMP_DIRECCION"] ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_BARRIO"> Barrio* </label>
                                    <input type="text" disabled id="EMP_BARRIO" name="EMP_BARRIO" placeholder="" class="form-control" title="Barrio" maxlength="20" value="<?php echo $frm["EMP_BARRIO"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="TIEM_CAMPO_ALF3"> Localidad* </label>
                                    <input type="text" disabled id="TIEM_CAMPO_ALF3" name="TIEM_CAMPO_ALF3" placeholder="" class="form-control" title="Localidad" maxlength="15" value="<?php echo $frm["TIEM_CAMPO_ALF3"] ?> ">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="EMP_TELEFONO"> Teléfono celular* </label>
                                    <input type="text" disabled id="EMP_TELEFONO" name="EMP_TELEFONO" placeholder="" class="form-control" title="Teléfono celular" maxlength="30" value="<?php echo $frm["EMP_TELEFONO"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="BENEF_CAMPO_ALF1"> Correo electrónico personal* </label>
                                    <input type="text" disabled id="BENEF_CAMPO_ALF1" name="BENEF_CAMPO_ALF1" placeholder="" class="form-control" title="Correo electrónico personal" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF1"] ?>">
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info green"></i> II. EN CASO DE EMERGENCIA CONTACTAR
                                </h3>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="EMP_PERSONA_ACUDIENTE"> Nombre contacto* </label>
                                    <input type="text" disabled id="EMP_PERSONA_ACUDIENTE" name="EMP_PERSONA_ACUDIENTE" placeholder="" class="form-control" title="Nombre acudiente" maxlength="50" value="<?php echo $frm["EMP_PERSONA_ACUDIENTE"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="EMP_TELEFONO_ACUDIENTE"> Teléfono contacto* </label>
                                    <input type="text" disabled id="EMP_TELEFONO_ACUDIENTE" name="EMP_TELEFONO_ACUDIENTE" placeholder="" class="form-control" title="Teléfono acudiente" maxlength="35" value="<?php echo $frm["EMP_TELEFONO_ACUDIENTE"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="EMP_DIRECCION_ACUDIENTE"> Dirección contacto* </label>
                                    <input type="text" disabled id="EMP_DIRECCION_ACUDIENTE" name="EMP_DIRECCION_ACUDIENTE" placeholder="" class="form-control" title="Dirección acudiente" maxlength="30" value="<?php echo $frm["EMP_DIRECCION_ACUDIENTE"] ?>">
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info green"></i> III. DATOS SOCIO-ECONÓMICOS
                                </h3>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="EMP_VIVIENDA"> ¿Tiene vivienda propia?* </label>
                                    <input type="text" disabled id="EMP_VIVIENDA" name="EMP_VIVIENDA" placeholder="" class="form-control" title="Vivienda propia" maxlength="20" value="<?php echo $frm["EMP_VIVIENDA"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="EMP_VIVIENDA"> ¿Adquirida por medio de Luker?* </label>
                                    <input type="text" disabled id="EMP_ADQ_EMPRESA" name="EMP_ADQ_EMPRESA" placeholder="" class="form-control" title="Adquirida por medio de Luker" maxlength="20" value="<?php echo $frm["EMP_ADQ_EMPRESA"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="TIEM_CAMPO_ALF2"> Rol en la familia* </label>
                                    <input type="text" disabled id="TIEM_CAMPO_ALF2" name="TIEM_CAMPO_ALF2" placeholder="" class="form-control" title="Rol en la familia" maxlength="30" value="<?php echo $frm["TIEM_CAMPO_ALF2"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="BENEF_CAMPO_IND1"> ¿Es cabeza de hogar?* </label>
                                    <input type="text" disabled id="BENEF_CAMPO_IND1" name="BENEF_CAMPO_IND1" placeholder="" class="form-control" title="cabeza de hogar" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_IND1"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="BENEF_CAMPO_IND2"> ¿Le han diagnosticado alguna enfermedad?* </label>
                                    <input type="text" disabled id="BENEF_CAMPO_IND2" name="BENEF_CAMPO_IND2" placeholder="" class="form-control" title="Le han diagnosticado alguna enfermedad" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_IND2"] ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="BENEF_CAMPO_ALF5"> Enfermedad diagnosticada* </label>
                                    <input type="text" disabled id="BENEF_CAMPO_ALF5" name="BENEF_CAMPO_ALF5" placeholder="" class="form-control" title="Enfermedad diagnosticada" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF5"] ?>">
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_IND3"> ¿Tiene algún tipo de discapacidad?* </label>
                            <input type="text" disabled id="BENEF_CAMPO_IND3" name="BENEF_CAMPO_IND3" placeholder="" class="form-control" title="Tiene algún tipo de discapacidad" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_IND3"] ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_NUM1"> % Discapacidad* </label>
                            <input type="number" min="0" max="100" id="BENEF_CAMPO_NUM1" name="BENEF_CAMPO_NUM1" placeholder="" class="form-control" title="Discapacidad" value="<?php echo $frm["BENEF_CAMPO_NUM1"] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_IND5"> ¿Tiene interés en adquirir vivienda?* </label>
                            <input type="text" disabled min="0" max="100" id="BENEF_CAMPO_IND5" name="BENEF_CAMPO_IND5" placeholder="" class="form-control" title="Tiene interés en adquirir vivienda" value="<?php echo $frm["BENEF_CAMPO_IND5"] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_IND6"> ¿Comparte domicilio con cónyuge?* </label>
                            <input type="text" disabled min="0" max="100" id="BENEF_CAMPO_IND6" name="BENEF_CAMPO_IND6" placeholder="" class="form-control" title="Comparte domicilio con cónyuge" value="<?php echo $frm["BENEF_CAMPO_IND6"] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_ALF2"> Clase de vivienda que vive actualmente* </label>
                            <input type="text" disabled id="BENEF_CAMPO_ALF2" name="BENEF_CAMPO_ALF2" placeholder="" class="form-control" title="Clase de vivienda que vive actualmente" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF2"] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_ALF3"> Tipo de vivienda que poseé* </label>
                            <input type="text" disabled id="BENEF_CAMPO_ALF3" name="BENEF_CAMPO_ALF3" placeholder="" class="form-control" title="Tipo de vivienda que poseé" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF3"] ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="BENEF_CAMPO_NUM3"> Estrato socio-económico* </label>
                            <input type="text" disabled id="BENEF_CAMPO_NUM3" name="TIEM_CAMPO_ALF4" placeholder="" class="form-control" title="Estrato socio-económico" maxlength="15" value="<?php echo $frm["BENEF_CAMPO_NUM3"] ?> ">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="BENEF_CAMPO_NUM2"> ¿Con cuántas personas vive?* </label>
                            <input type="text" disabled id="BENEF_CAMPO_NUM2" name="BENEF_CAMPO_NUM2" placeholder="" class="form-control" title="Con cuántas personas vive" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_NUM2"] ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="BENEF_CAMPO_NUM4"> ¿Cuántas personas dependen económicamente de ud?* </label>
                            <input type="text" disabled id="BENEF_CAMPO_NUM4" name="BENEF_CAMPO_NUM4" placeholder="" class="form-control" title="Cuántas personas dependen económicamente de ud" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_NUM4"] ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="BENEF_CAMPO_NUM5"> ¿Cuántos hijos tiene?* </label>
                            <input type="text" disabled id="BENEF_CAMPO_NUM5" name="BENEF_CAMPO_NUM5" placeholder="" class="form-control" title="Cuántos hijos tiene" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_NUM5"] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for=""> Foto </label>
                            <img src="<?php echo URLROOT. "file/luker/actualizacionempleados/empleados/". $frm["Foto"]?>" width="200">
                        </div>
                    </div>
                    <a class="btn btn-info" href="actualizacionempleados.php?action=view-beneficiarios&id=<?php echo $id?>">
                        <i class="ace-icon fa fa-cloud-upload align-top bigger-125"></i> Ver beneficiarios </a>
                    <a class="btn btn-info" href="actualizacionempleados.php?action=view-estudios&id=<?php echo $id?>">
                        <i class="ace-icon fa fa-cloud-upload align-top bigger-125"></i> Ver estudios </a>
                    <div class="widget-header widget-header-large">
                        <h3 class="widget-title grey lighter">
                            <i class="ace-icon fa fa-info green"></i> Respuesta de funcionario
                        </h3>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="">Observación</label>
                            <textarea name="Motivo" id="Motivo" class="form-control"></textarea>
                        </div>
                    </div>
                    <hr>
                    <!--  <input type="hidden" name="action" id="action" value="confirmar"/>
                        <input type="hidden" name="ID" value="<?php echo $_GET['id']?>"/>
                        <button class="btn btn-info btnEnviar" type="button" rel="frm_empleado">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            Aceptar
                        </button> -->
                    </form>
                    <br>
                    <a href="actualizacionempleados.php?id=<?php echo $_GET['id']?>&action=confirmar" id="Aceptar" type="button" class="btn btn-success btn-lg btn-block">Aceptar</a>
                    <!--nput class="submit" type="submit" value="SUBMIT"-->
                    <!-- <a href="actualizacionempleados.php?id=<?php echo $_GET['id']?>&action=rechazar" id="" type="button" class="btn btn-danger btn-lg btn-block">Rechazar</a> -->
                    <button id="rechazar" type="button" class="btn btn-danger btn-lg btn-block">Rechazar</button>
                    <hr>
                </div>
            </div>
        </div>
    </div><!-- /.widget-main -->
</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
</div>
<?
	include( "cmp/footer_scripts.php" );
?>
<script>
$("#rechazar").click(function() {
    campos = {
        IDLukerEmpleado: "<?php echo $_GET['id']?>",
        EMP_CEDULA: $("#EMP_CEDULA").val(),
        Motivo: $("#Motivo").val(),
        action: "rechazar"
    };
    jQuery.ajax({
        "type": "POST",
        "data": campos,
        "dataType": "json",
        "url": "actualizacionempleados.php",
        "success": function(data) {
            alert(data.message);
            return true;
        }
    });
});
</script>