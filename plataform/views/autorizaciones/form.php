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
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio</label>

                                <div class="col-sm-8">
                                    <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($newmode == "updateingreso") echo "readonly"; ?> value="<?php echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $frm["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $frm["IDSocio"] . "'") ?>">
                                    <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Ingreso</label>

                                <div class="col-sm-8">
                                    <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 <?php if ($newmode != "updateingreso") echo "calendariohoy"; ?> " title="Fecha Ingreso" value="<?php if ($frm["FechaInicio"] == "0000-00-00" || $frm["FechaInicio"] == "") echo date("Y-m-d");
                                                                                                                                                                                                                                        else echo $frm["FechaInicio"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Ingreso</label>

                                <div class="col-sm-8">
                                    <input type="time" name="HoraInicio" id="HoraInicio" class="input" title="Hora Inicio" value="<?php echo $frm["HoraInicio"]; ?>">
                                </div>
                            </div>
                            <!--  &nbsp;
                               <div></div>
                                &nbsp; -->
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin</label>

                                <div class="col-sm-8">
                                    <?php if (SIMUser::get("club") == 78) { ?>
                                        <input type="date" min="<?php echo date("Y-m-d") ?>" max="<?php echo date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 year")); ?>" id="FechaFin" name="FechaFin" placeholder="ccc" title="Recuerda que el máximo tiempo permito es un año a partir de hoy" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") echo date("Y-m-d");
                                                                                                                                                                                                                                                                                                        else echo $frm["FechaFin"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
                                    <?php } else { ?>
                                        <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar " title="Fecha Fin" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") echo date("Y-m-d");
                                                                                                                                                                        else echo $frm["FechaFin"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Salida</label>

                                <div class="col-sm-8">
                                    <input type="time" name="HoraSalida" id="HoraSalida" class="input" title="Hora Salida" value="<?php echo $frm["HoraFin"]; ?>">
                                </div>
                            </div>

                            <?php
                            if ($_GET["action"] == "editinfo") {
                            ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Creacion Autorización</label>

                                    <div class="col-sm-8">
                                        <input type="text" name="FechaTrCr" id="FechaTrCr" class="input" title="Fecha" disabled="disabled" value="<?php echo $frm["FechaTrCr"]; ?>">
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                            $Dias = explode(',', $frm['Dias']);
                            ?>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias</label>

                                <div class="col-sm-8">
                                    <!--   <input type="radio" name="TipoAutorizacion" id="TipoAutorizacion<?php echo $cont_invitado; ?>" value="<?php echo $dato_tipo; ?>" > -->
                                    <br>

                                    <input type="checkbox" name="Dias[]" id="Dias" value="1" <?= (in_array('L', $Dias) || in_array('1', $Dias)) ? 'checked' : ''; ?>><!--  &nbsp; --> Lunes

                                    <input type="checkbox" name="Dias[]" id="Dias" value="2" <?= (in_array('M', $Dias) || in_array('2', $Dias)) ? 'checked' : ''; ?>> <!-- &nbsp; --> Martes

                                    <input type="checkbox" name="Dias[]" id="Dias" value="3" <?= (in_array('MI', $Dias) || in_array('3', $Dias)) ? 'checked' : ''; ?>><!--  &nbsp; --> Miercoles

                                    <input type="checkbox" name="Dias[]" id="Dias" value="4" <?= (in_array('J', $Dias) || in_array('4', $Dias)) ? 'checked' : ''; ?>> <!-- &nbsp; --> Jueves

                                    <input type="checkbox" name="Dias[]" id="Dias" value="5" <?= (in_array('V', $Dias) || in_array('5', $Dias)) ? 'checked' : ''; ?>><!--  &nbsp; --> Vienes

                                    <input type="checkbox" name="Dias[]" id="Dias" value="6" <?= (in_array('S', $Dias) || in_array('6', $Dias)) ? 'checked' : ''; ?>><!--  &nbsp; --> Sabado

                                    <input type="checkbox" name="Dias[]" id="Dias" value="0" <?= (in_array('D', $Dias) || in_array('0', $Dias)) ? 'checked' : ''; ?>> <!-- &nbsp; --> Domingo

                                </div>
                            </div>



                        </div>




                        <?php
                        $sql_tipodoc = $dbo->query("Select * From TipoDocumento Where Publicar = 'S'");
                        while ($row_tipo_doc = $dbo->fetchArray($sql_tipodoc)) :
                            $array_tipo_doc[$row_tipo_doc["IDTipoDocumento"]] = $row_tipo_doc["Nombre"];
                        endwhile;


                        if (SIMNet::req("action") == "editinfo")
                            $total_caja_invitado = 1;
                        else
                            $total_caja_invitado = 10;

                        for ($cont_invitado = 1; $cont_invitado <= $total_caja_invitado; $cont_invitado++) :
                            unset($datos_invitado_edit);
                            unset($datos_placa_edit);

                            $mandatory = ($cont_invitado == 1) ? "mandatory" : "";

                            if ($cont_invitado == 1) :
                                $IDInvitadoEdit = $frm["IDInvitado"];
                                if (!empty($IDInvitadoEdit)) :
                                    $datos_invitado_edit = $dbo->fetchAll("Invitado", " IDInvitado = '" . $IDInvitadoEdit . "' ", "array");
                                    $datos_invitado_edit["TipoAutorizacion"] = $frm["TipoAutorizacion"];
                                    $datos_placa_edit = $dbo->getFields("Vehiculo", "Placa", "IDVehiculo = '" . $frm["IDVehiculo"] . "'");
                                endif;
                            endif; ?>

                            <div class="col-sm-12">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <h4 class="smaller">
                                            Contratista <?php echo $cont_invitado; ?>
                                        </h4>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <p class="muted">

                                            <div class="form-group first ">

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> * Documento </label>

                                                    <div class="col-sm-8">
                                                        <input id="NumeroDocumento<?php echo $cont_invitado; ?>" type="text" size="25" title="Numero Documento" name="NumeroDocumento<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input autocomplete-ajax_tblinvitado txtPistola <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["NumeroDocumento"])) {
                                                                                                                                                                                                                                                                                                                                            echo $datos_invitado_edit["NumeroDocumento"];
                                                                                                                                                                                                                                                                                                                                        } ?>" />
                                                        <input type="hidden" name="IDInvitado<?php echo $cont_invitado; ?>" value="<?php echo $frm["IDInvitado"]; ?>" id="IDInvitado<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" title="Numero Documento">

                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> * Tipo Documento </label>

                                                    <div class="col-sm-8">
                                                        <select name="IDTipoDocumento<?php echo $cont_invitado; ?>" id="IDTipoDocumento<?php echo $cont_invitado; ?>" class="popup <?= $mandatory ?>" title="Tipo Documento">
                                                            <option value=""></option>
                                                            <?php foreach ($array_tipo_doc as $keytipodoc => $nomtipodoc) : ?>
                                                                <option value="<?php echo $keytipodoc; ?>" <?php if ($datos_invitado_edit["IDTipoDocumento"] == $keytipodoc) echo "selected"; ?>><?php echo $nomtipodoc; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group first ">
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> * Nombre </label>

                                                    <div class="col-sm-8">
                                                        <input id="Nombre<?php echo $cont_invitado; ?>" type="text" size="25" title="Nombre" name="Nombre<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["Nombre"])) {
                                                                                                                                                                                                                                                                        echo $datos_invitado_edit["Nombre"];
                                                                                                                                                                                                                                                                    } ?>" />
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> * Apellido </label>

                                                    <div class="col-sm-8">
                                                        <input id="Apellido<?php echo $cont_invitado; ?>" type="text" size="25" title="Apellido" name="Apellido<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["Apellido"])) {
                                                                                                                                                                                                                                                                                echo $datos_invitado_edit["Apellido"];
                                                                                                                                                                                                                                                                            } ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group first ">
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

                                                    <div class="col-sm-8">
                                                        <input id="Email<?php echo $cont_invitado; ?>" type="text" size="25" title="Email" name="Email<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if (!empty($datos_invitado_edit["Email"])) {
                                                                                                                                                                                                                                                    echo $datos_invitado_edit["Email"];
                                                                                                                                                                                                                                                } ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>

                                                    <div class="col-sm-8">
                                                        <input id="Telefono<?php echo $cont_invitado; ?>" type="text" size="25" title="Telefono" name="Telefono<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input" value="<?php if (!empty($datos_invitado_edit["Telefono"])) {
                                                                                                                                                                                                                                                            echo $datos_invitado_edit["Telefono"];
                                                                                                                                                                                                                                                        } ?>" />

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group first ">
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Nacimiento </label>

                                                    <div class="col-sm-8">
                                                        <input type="text" id="FechaNacimiento<?php echo $cont_invitado; ?>" name="FechaNacimiento<?php echo $cont_invitado; ?>" placeholder="Fecha Nacimiento" class="col-xs-12 calendar" title="Fecha Nacimiento" value="<?php if (!empty($datos_invitado_edit["FechaNacimiento"])) {
                                                                                                                                                                                                                                                                                echo $datos_invitado_edit["FechaNacimiento"];
                                                                                                                                                                                                                                                                            } ?>">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Sangre/Empresa </label>

                                                    <div class="col-sm-8">
                                                        <input id="TipoSangre<?php echo $cont_invitado; ?>" type="text" size="25" title="TipoS angre" name="TipoSangre<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input" value="<?php if (!empty($datos_invitado_edit["TipoSangre"])) {
                                                                                                                                                                                                                                                                    echo $datos_invitado_edit["TipoSangre"];
                                                                                                                                                                                                                                                                } ?>" />

                                                    </div>
                                                </div>


                                            </div>

                                            <div class="form-group first ">

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> * Lugar al que se dirige o Predio</label>

                                                    <div class="col-sm-8">
                                                        <input id="Predio<?php echo $cont_invitado; ?>" type="text" size="25" title="Predio" name="Predio<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["Predio"])) {
                                                                                                                                                                                                                                                                        echo $datos_invitado_edit["Predio"];
                                                                                                                                                                                                                                                                    } ?>" />
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Autorizacion</label>

                                                    <div class="col-sm-8">
                                                        <input id="CodigoAutorizacion<?php echo $cont_invitado; ?>" type="text" size="25" title="Codigo Autorizacion" name="CodigoAutorizacion<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input" value="<?php if (!empty($frm["CodigoAutorizacion"])) {
                                                                                                                                                                                                                                                                                            echo $frm["CodigoAutorizacion"];
                                                                                                                                                                                                                                                                                        } ?>" />
                                                    </div>
                                                </div>



                                            </div>

                                            <div class="form-group first ">

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion</label>

                                                    <div class="col-sm-8">
                                                        <textarea id="Observaciones<?php echo $cont_invitado; ?>" rows="4" title="Observaciones" name="Observaciones<?php echo $cont_invitado; ?>" class="form-control"><?php echo $datos_invitado_edit["ObservacionGeneral"]; ?></textarea>
                                                    </div>
                                                </div>
                                                <?php //if (SIMUser::get('club') != 35) : 
                                                ?>
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion Socio</label>

                                                    <div class="col-sm-8">

                                                        <textarea id="ObservacionSocio<?php echo $cont_invitado; ?>" rows="4" title="ObservacionSocio" name="ObservacionSocio<?php echo $cont_invitado; ?>" class="form-control"><?php echo $frm["ObservacionSocio"] . $otras_obs; ?></textarea>
                                                    </div>
                                                </div>
                                                <?php //endif; 
                                                ?>

                                            </div>
                                            <p>Campos adicionales</p>
                                            <br>
                                            <?php
                                            // Insertamos los campos dinamicos al formulario si el club lo solicito

                                            $response_campo_formulario = array();
                                            $sql_campo_form = "SELECT * FROM CampoFormularioContratista WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                                            $qry_campo_form = $dbo->query($sql_campo_form);
                                            if ($dbo->rows($qry_campo_form) > 0) { ?>
                                                <div class="form-group first ">
                                                    <?php
                                                    while ($r_campo = $dbo->fetchArray($qry_campo_form)) :
                                                        $mandatory = ($r_campo['Obligatorio'] == 'S') ? 'mandatory' : '';

                                                        //otros datos
                                                        $sql_otros = "SELECT * FROM SocioAutorizacionOtrosDatos WHERE IDSocioAutorizacion = '" . $frm["IDSocioAutorizacion"] . "' AND IDCampoFormularioContratista = " . $r_campo['IDCampoFormularioContratista'];
                                                        $r_otros = $dbo->query($sql_otros);
                                                        $row_otros = $dbo->assoc($r_otros);
                                                        $r_campo["Valor"] = ($row_otros > 0) ? $row_otros['Valor'] : "";
                                                    ?>
                                                        <div class="col-xs-12 col-sm-6 first">
                                                            <?php
                                                            switch ($r_campo['TipoCampo']):
                                                                case 'textarea': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <textarea id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>"><?= $row_otros["Valor"]; ?></textarea>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'radio': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <?php
                                                                        $options = explode(',', $r_campo['Valores']);
                                                                        $radiogroup = "";
                                                                        foreach ($options as $key => $val) {
                                                                            $val = trim($val); //Eliminar espacios

                                                                            $radiogroup .= ' <label class="radiogroup"><input type="radio" name="' . $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado . '" id="' . $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado . '" title="' . $r_campo['EtiquetaCampo'] . '" class="' . $mandatory . '" value="' . $val . '"';

                                                                            $radiogroup .= ($val == $row_otros['Valor']) ? " checked" : "";

                                                                            $radiogroup .= "> " . $val . "</label>";
                                                                        }
                                                                        echo $radiogroup;
                                                                        ?>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'checkbox': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <?php
                                                                        $options = explode(',', $r_campo['Valores']);
                                                                        $respuesta  = explode(',', $row_otros['Valor']);
                                                                        foreach ($options as $i => $option) {
                                                                            $checked = (in_array($option, $respuesta)) ? "checked" : ""; ?>
                                                                            <input type="checkbox" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado . "[]" ?>" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado ?>" class="<?= $mandatory ?>" value="<?= $option ?>" <?= $checked ?>>
                                                                            &nbsp;<?= $option ?>
                                                                        <?php
                                                                        }
                                                                        ?>


                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'select': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <select name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>">
                                                                            <?php
                                                                            $options = explode(',', $r_campo['Valores']);
                                                                            foreach ($options as $key => $value) :
                                                                                $selected = ($value == $row_otros['Valor']) ? "selected" : "";
                                                                            ?>
                                                                                <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
                                                                            <?php
                                                                            endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'number': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="number" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'date': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="date" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'time': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="time" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'email': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="email" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'rating': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <i class="fa fa-star"> <?= $row_otros['Valor'] ?> </i>
                                                                    </div>
                                                                <?php
                                                                    break;
                                                                case 'imagen': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="file" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory ?>">
                                                                        <? if (!empty($r_campo['Valor'])) { ?>
                                                                            <a target="_blank" href="<?= PQR_ROOT . $row_otros['Valor'] ?>">
                                                                                <?php //echo mb_strimwidth($r_campo['Valor'], 0, 45, '...');
                                                                                ?>
                                                                                Ver archivo
                                                                            </a>
                                                                        <?
                                                                        } // END if
                                                                        ?>
                                                                    </div>
                                                                <?php
                                                                    break;

                                                                case 'imagenarchivo': ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="file" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory ?>">

                                                                        <? if (!empty($r_campo['Valor'])) { ?>
                                                                            <a target="_blank" href="<?= PQR_ROOT . $row_otros['Valor'] ?>">
                                                                                <?php //echo mb_strimwidth($r_campo['Valor'], 0, 45, '...');
                                                                                ?>
                                                                                Ver archivo
                                                                            </a>
                                                                        <?
                                                                        } // END if
                                                                        ?>
                                                                    </div>
                                                                <?php
                                                                    break;

                                                                case 'titulo':
                                                                    break;

                                                                default: ?>
                                                                    <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" id="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioContratista'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
                                                                    </div>
                                                            <?php
                                                                    break;
                                                            endswitch;
                                                            ?>
                                                        </div>


                                                    <?php endwhile; //end while 
                                                    ?>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="form-group first ">
                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Autorizacion </label>

                                                    <div class="col-sm-8">
                                                        <?php foreach (SIMResources::$tipoautorizacion as $key_tipo => $dato_tipo) : ?>
                                                            <input type="radio" name="TipoAutorizacion<?php echo $cont_invitado; ?>" id="TipoAutorizacion<?php echo $cont_invitado; ?>" value="<?php echo $dato_tipo; ?>" <?php if ($datos_invitado_edit["TipoAutorizacion"] == $dato_tipo) echo "checked"; ?>><?php echo $dato_tipo; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            if (SIMUser::get("club") == 16 || SIMUser::get("club") == 8) {
                                            ?>
                                                <div class="form-group first ">
                                                    <div class="col-xs-12 col-sm-6">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ARL </label>
                                                        <div class="col-sm-8">
                                                            <input id="ARL<?php echo $cont_invitado; ?>" type="text" size="25" title="ARL" name="ARL<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["ARL"])) {
                                                                                                                                                                                                                                                                    echo $datos_invitado_edit["ARL"];
                                                                                                                                                                                                                                                                } ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-6">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> EPS </label>
                                                        <div class="col-sm-8">
                                                            <input id="EPS<?php echo $cont_invitado; ?>" type="text" size="25" title="EPS" name="EPS<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if (!empty($datos_invitado_edit["EPS"])) {
                                                                                                                                                                                                                                                    echo $datos_invitado_edit["EPS"];
                                                                                                                                                                                                                                                } ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group first ">
                                                    <div class="col-xs-12 col-sm-6">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vencimiento ARL </label>
                                                        <div class="col-sm-8">
                                                            <input id="FechaVencimientoArl<?php echo $cont_invitado; ?>" type="text" size="25" title="Vencimiento ARL" name="FechaVencimientoArl<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="col-xs-12 calendar <?= $mandatory ?>" value="<?php if (!empty($datos_invitado_edit["FechaVencimientoArl"])) {
                                                                                                                                                                                                                                                                                                                            echo $datos_invitado_edit["FechaVencimientoArl"];
                                                                                                                                                                                                                                                                                                                        } ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-6">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vencimiento EPS </label>
                                                        <div class="col-sm-8">
                                                            <input id="FechaVencimientoEps<?php echo $cont_invitado; ?>" type="text" size="25" title="FechaVencimientoEps" name="FechaVencimientoEps<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="col-xs-12 calendar" value="<?php if (!empty($datos_invitado_edit["FechaVencimientoEps"])) {
                                                                                                                                                                                                                                                                                                                echo $datos_invitado_edit["FechaVencimientoEps"];
                                                                                                                                                                                                                                                                                                            } ?>" />
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php
                                            }
                                            ?>



                                            <p>Veh&iacute;culo</p>
                                            <?php
                                            $cont_vehiculo = 1;
                                            for ($cont_vehiculo = 1; $cont_vehiculo <= 1; $cont_vehiculo++) : ?>
                                                <div class="form-group first ">

                                                    <div class="col-xs-12 col-sm-4">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Placa </label>

                                                        <div class="col-sm-8">
                                                            <input id="Placa<?php echo $cont_invitado; ?>" type="text" size="25" title="Placa" name="Placa<?php echo $cont_invitado; ?>" alt="<?php echo $cont_vehiculo; ?>" class="input autocomplete-ajax_vehiculo" value="<?php if (!empty($datos_placa_edit) && $cont_vehiculo == 1) {
                                                                                                                                                                                                                                                                                    echo $datos_placa_edit;
                                                                                                                                                                                                                                                                                } ?>" />
                                                            <input type="hidden" name="IDVehiculo<?php echo $cont_invitado; ?>" value="<?php echo $frm["IDVehiculo"]; ?>" id="IDVehiculo<?php echo $cont_invitado; ?>" alt="<?php echo $cont_vehiculo; ?>" title="Vehiculo">
                                                        </div>
                                                    </div>

                                                    <!-- arl file -->
                                                    <div class="col-xs-12 col-sm-6">
                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> PDF ARL </label>

                                                        <div class="col-sm-8">
                                                            <?php
                                                            $ruta_adjunto1file = string;
                                                            if ($datos_invitado_edit["ARLFILE"]) {

                                                                if (strstr(strtolower($datos_invitado_edit["ARLFILE"]), "http://"))
                                                                    $ruta_adjunto1file = $datos_invitado_edit["ARLFILE"];
                                                                else
                                                                    $ruta_adjunto1file = IMGNOTICIA_ROOT . $datos_invitado_edit["ARLFILE"];
                                                            ?>
                                                                <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $datos_invitado_edit["ARLFILE"] ?></a>
                                                                <a href="<? echo $script . ".php?action=DelDocNot&cam=ARLFILE&id=" . $frm["IDInvitado"]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <input type="file" name="ARLFILE<?php echo $cont_invitado; ?>" id="ARLFILE<?php echo $cont_invitado; ?>" class="popup" title="Documento">
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <!-- fin arl file -->

                                                </div>

                                            <?php endfor; ?>
                                            </p>
                                        </div>
                                    </div>


                                </div>


                            </div><!-- /.col -->

                            <div class="form-group first ">

                            </div>

                        <?php
                        endfor;
                        ?>
                        <?php
                        if ($newmode == "updateobservacion") : ?>
                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones</label>

                                    <div class="col-sm-8">
                                        <textarea id="Observaciones" rows="4" title="Observaciones" name="Observaciones" class="form-control" /><?php echo $frm["Observaciones"] ?></textarea>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha/Hora Ingreso</label>

                                    <div class="col-sm-8">
                                        <input type="text" id="FechaInicioClub" name="FechaInicioClub" placeholder="Fecha Ingreso Club" class="col-xs-12" title="Fecha Ingreso Club" value="<?php if ($newmode == "updateingreso") : echo date("Y-m-d H:i:s");
                                                                                                                                                                                            else : echo "";
                                                                                                                                                                                            endif; ?>" readonly>
                                    </div>
                                </div>

                            </div>
                        <?php endif; ?>




                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <input type="hidden" name="NumeroInvitados" id="NumeroInvitados" value="<?php echo $cont_invitado;  ?>" />

                                <button class="btn btn-info btnEnviar" type="button" rel="frm">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                </button>

                                <br>
                                <?php if ($newmode != "insert") : ?>
                                    <button class="btn btn-app btn-light btn-xs" onClick="javascript:window.print()">
                                        <i class="ace-icon fa fa-print bigger-160"></i>
                                        Imprimir
                                    </button>
                                <?php endif; ?>


                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />

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

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>