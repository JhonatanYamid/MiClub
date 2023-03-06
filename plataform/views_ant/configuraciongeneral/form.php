<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

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

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo duración Splash socios: </label>

                                    <div class="col-sm-8">
                                        <input type="TiempoSplash" name="TiempoSplash" class="form-control" placeholder="TiempoSplash" value="<?php echo $frm["TiempoSplash"]; ?>"> segundos
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo duración Splash funcionarios: </label>

                                    <div class="col-sm-8">
                                        <input type="text" name="TiempoSplashFuncionarios" class="form-control" placeholder="TiempoSplash" value="<?php echo $frm["TiempoSplashFuncionarios"]; ?>"> segundos
                                    </div>
                                </div>



                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Cartera </label>

                                    <div class="col-sm-8">
                                        <input id=EmailCartera type=text size=25 name=EmailCartera class="col-xs-12" title="EmailCartera" value="<?= $frm["EmailCartera"] ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo Panico</label>

                                    <div class="col-sm-8">
                                        <input id=CorreoPanico type=text size=25 name=CorreoPanico class="col-xs-12" title="CorreoPanico" value="<?= $frm["CorreoPanico"] ?>">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Diseño home: </label>

                                    <div class="col-sm-8">
                                        <select name="TipoDisenoHome" id="TipoDisenoHome" class="popup">
                                            <option value=""></option>
                                            <option value="2x2" <? if ($frm["TipoDisenoHome"] == "2x2") echo " selected='selected' " ?>>2x2</option>
                                            <option value="3x3" <? if ($frm["TipoDisenoHome"] == "3x3") echo " selected='selected' " ?>>3x3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activar notificaciones peri&oacute;dicas</label>
                                    <div class="col-sm-8">
                                        <?= SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm['ActivarNotificacionesPeriodicas'], 'ActivarNotificacionesPeriodicas', 'class="input"') ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificaciones Objetos Perdidos </label>

                                    <div class="col-sm-8">
                                        <input id=EmailObjetosPerdidos type=text size=25 name=EmailObjetosPerdidos class="col-xs-12" title="EmailObjetosPerdidos" value="<?= $frm["EmailObjetosPerdidos"] ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Como desea nombrar a los socios: </label>

                                    <div class="col-sm-8">
                                        <input type="text" name="TextoSocios" class="form-control" placeholder="" value="<?php echo $frm["TextoSocios"]; ?>">
                                    </div>
                                </div>
                            </div>






                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Empleados Correo Panico: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="AccionInvitadoUsuario" name="AccionInvitadoUsuario" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-funcionarioConfiguracionGeneral" title="número de derecho">
                                        <br>
                                        <a id="agregar_empleado" href="#">Agregar</a> | <a id="borrar_empleado" href="#">Borrar</a>
                                        <br>
                                        <select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_invitados = explode("|||", $frm["UsuarioSeleccion"]);


                                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                if (!empty($datos_invitado)) {
                                                    //$array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;
                                                    $IDSocioInvitacion = $datos_invitado;
                                                    if ($IDSocioInvitacion > 0) :
                                                        $nombre_socio = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Usuario", "Apellido", "IDUsuario = '" . $IDSocioInvitacion . "'"));
                                                        echo $nombre_socio;
                                            ?>
                                                        <option value="<?php echo  $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="UsuarioSeleccion" id="UsuarioSeleccion" value="">
                                    </div>
                                </div>


                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pais Club</label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formPopUp("Pais", "Nombre", "Nombre", "IDPais", $frm["IDPais"], "[Seleccione Pais]", "form-control", "title = \"Pais\"") ?>
                                    </div>
                                </div>


                            </div>


                            <div class="form-group first">
                                Mensaje para invitados
                                <div class="col-sm-12">
                                    <?php
                                    $oCuerpoInvi = new FCKeditor("MensajeInvitados");
                                    $oCuerpoInvi->BasePath = "js/fckeditor/";
                                    $oCuerpoInvi->Height = 200;
                                    //$oCuerpo->EnterMode = "p";
                                    $oCuerpoInvi->Value =  $frm["MensajeInvitados"];
                                    $oCuerpoInvi->Create();
                                    ?>
                                </div>
                            </div>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa  fa-barcode green"></i>
                                    Configuraci&oacute;n Carne
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo para carn&eacute;: </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$tipocodigocarne), $frm["TipoCodigoCarne"], 'TipoCodigoCarne', "class='input '") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color Fondo Carne </label>

                                    <div class="col-sm-8">
                                        <input name="ColorFondoCarne" type="color" value="<?php if (empty($frm["ColorFondoCarne"])) {
                                                                                                echo "#FFFFFF";
                                                                                            } else {
                                                                                                echo $frm["ColorFondoCarne"];
                                                                                            }    ?>" />
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Informaci&oacute;n Carn&eacute;: </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$datosCarne), $frm["DatosCarne"], 'DatosCarne', "class='input '") ?>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr Android? </label>

                                    <div class="col-sm-8">
                                        <input id="PorcentajeQrAndroid" type=text size=25 name="PorcentajeQrAndroid" class="col-xs-12" title="Porcentaje Qr Android" value="<?= $frm["PorcentajeQrAndroid"] ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr IOS: </label>

                                    <div class="col-sm-8">
                                        <input id="PorcentajeQrIOS" type=text size=25 name="PorcentajeQrIOS" class="col-xs-12" title="Porcentaje Qr IOS" value="<?= $frm["PorcentajeQrIOS"] ?>">
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Identificador Usuario </label>

                                    <div class="col-sm-8">
                                        <input id=LabelIdentificadorUsuario type=text size=25 name=LabelIdentificadorUsuario class="col-xs-12" title="Label Identificador Usuario" value="<?= $frm["LabelIdentificadorUsuario"] ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Datos carne: </label>

                                    <div class="col-sm-8">
                                        <input id=LabelEstadoUsuario type=text size=25 name=LabelEstadoUsuario class="col-xs-12" title="Label Estado Usuario" value="<?= $frm["LabelEstadoUsuario"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir siempre el cambio de foto? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCambioFotoPerfil"], 'PermiteCambioFotoPerfil', "class='input '") ?>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Telefono directorio </label>

                                    <div class="col-sm-8">
                                        <? if (!empty($frm[IconoTelefono])) {
                                            echo "<img src='" . CLUB_ROOT . "$frm[IconoTelefono]' width=55 >";
                                        ?>
                                            <a href="<? echo $script . " .php?action=delfoto&foto=$frm[IconoTelefono]&campo=IconoTelefono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                        <input name="IconoTelefono" id=IconoTelefono class="col-xs-12" title="Icono Telefono" type="file" size="25" style="font-size: 10px">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Mail directorio </label>
                                    <div class="col-sm-8">
                                        <? if (!empty($frm[IconoEmail])) {
                                            echo "<img src='" . CLUB_ROOT . "$frm[IconoEmail]' width=55 >";
                                        ?>
                                            <a href="<? echo $script . " .php?action=delfoto&foto=$frm[IconoEmail]&campo=IconoEmail&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                        <input name="IconoEmail" id=IconoEmail class="col-xs-12" title="Icono Mail" type="file" size="25" style="font-size: 10px">
                                    </div>
                                </div>

                            </div>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-gavel green"></i>
                                    Clasificados
                                </h3>
                            </div>


                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir a socio crear clasificado?: </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CrearClasificado"], 'CrearClasificado', "class='input '") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Crear Clasificado en: </label>

                                    <div class="col-sm-8">
                                        <select name="TipoCrearClasificado" id="TipoCrearClasificado" class="popup" title="Tipo Crear Clasificado">
                                            <option value="">[Seleccione el tipo]</option>
                                            <option value="app" <? if ($frm["TipoCrearClasificado"] == "app") echo " selected='selected' " ?>>App</option>
                                            <option value="url" <? if ($frm["TipoCrearClasificado"] == "url") echo " selected='selected' " ?>>Url</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url para crear clasificado: </label>

                                    <div class="col-sm-8">
                                        <input type="text" id="URLCLasificado" name="URLCLasificado" placeholder="URL CLasificado" class="col-xs-12" title="URL CLasificado" value="<?php echo $frm["URLCLasificado"]; ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Whatsapp </label>

                                    <div class="col-sm-8">
                                        <input type="text" id="LabelWhatsapp" name="LabelWhatsapp" placeholder="Label Whatsapp" class="col-xs-12" title="Label Whatsapp" value="<?php echo $frm["LabelWhatsapp"]; ?>">
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Boton Mis Clasificados </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBotonMisClasificados"], 'PermiteBotonMisClasificados', "class='input '") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Boton Contactar</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBotonContactar"], 'PermiteBotonContactar', "class='input '") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Boton Preguntar</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBotonPreguntar"], 'PermiteBotonPreguntar', "class='input '") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Boton WhatsApp</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBotonWhatsApp"], 'PermiteBotonWhatsApp', "class='input '") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Boton WhatsApp </label>

                                    <div class="col-sm-8">
                                        <input type="text" id="TextoBotonWhatsApp" name="TextoBotonWhatsApp" placeholder="Texto Boton WhatsApp" class="col-xs-12" title="Texto Boton WhatsApp" value="<?php echo $frm["TextoBotonWhatsApp"]; ?>">
                                    </div>
                                </div>
                            </div>


                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Publicidad
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Publicidad? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicidad"], 'Publicidad', "class='input '") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo rotar Publicidad? </label>

                                    <div class="col-sm-8">
                                        <input id="TiempoPublicidad" type=text size=25 name="TiempoPublicidad" class="col-xs-12" title="Tiempo Publicidad" value="<?= $frm["TiempoPublicidad"] ?>">segundos
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Header App? </label>

                                    <div class="col-sm-8">
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Publicidad" <?php if ($frm["TipoHeaderApp"] == "Publicidad") echo "checked"; ?>> Publicidad (rota im&aacute;genes)
                                        <br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Clasico" <?php if ($frm["TipoHeaderApp"] == "Clasico") echo "checked"; ?>> Clasico (imagen fija logo club)
                                        <br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="PublicidadFoto" <?php if ($frm["TipoHeaderApp"] == "PublicidadFoto") echo "checked"; ?>> PublicidadFoto (Publicidad mas foto)
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo Rotar Publicidad Header? </label>

                                    <div class="col-sm-8">
                                        <input id="TiempoPublicidadHeader" type=text size=25 name="TiempoPublicidadHeader" class="col-xs-12" title="Tiempo Publicidad Header" value="<?= $frm["TiempoPublicidadHeader"] ?>">segundos
                                    </div>
                                </div>

                            </div>


                            <!-- INICIO VENTANA OBJETOS -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Ventana Objetos
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Ventana Objetos? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["VentanaObjetos"], 'VentanaObjetos', "class='input '") ?>
                                    </div>
                                </div>


                            </div>




                            <!-- FIN VENTANA OBJETOS -->


                            <!-- INICIO VENTANA VACUNACION -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Ventana Vacunación
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Ventana Vacunación? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["VentanaVacunacion"], 'VentanaVacunacion', "class='input '") ?>
                                    </div>
                                </div>


                            </div>




                            <!-- FIN VENTANA VACUNACION -->


                            <!-- INICIO CAMMPO NACIMIENTO -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Campo Fecha Nacimiento
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Campo Fecha Nacimiento? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoFechaNacimiento"], 'CampoFechaNacimiento', "class='input '") ?>
                                    </div>
                                </div>


                            </div>




                            <!-- FIN CAMPO NACIMIENTO -->

                            <!-- INICIO  CampoObservacionGeneral -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Campo Observacion General
                                </h3>
                            </div>

                            <div class="form-group first ">



                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Campo Observacion General? </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoObservacionGeneral"], 'CampoObservacionGeneral', "class='input '") ?>
                                    </div>
                                </div>


                            </div>




                            <!-- FIN CampoObservacionGeneral-->

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i>
                                    Parametros WebView
                                </h3>
                            </div>

                            <div class="form-group first ">


                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 1</label>

                                    <div class="col-sm-8">

                                        <?php
                                        $oCuerpo = new FCKeditor("MensajeWebView1");
                                        $oCuerpo->BasePath = "js/fckeditor/";
                                        $oCuerpo->Height = 300;
                                        $oCuerpo->Width = 300;
                                        //$oCuerpo->EnterMode = "p";
                                        $oCuerpo->Value =  $frm["MensajeWebView1"];
                                        $oCuerpo->Create();
                                        ?>

                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 2</label>

                                    <div class="col-sm-8">

                                        <?php
                                        $oCuerpo = new FCKeditor("MensajeWebView2");
                                        $oCuerpo->BasePath = "js/fckeditor/";
                                        $oCuerpo->Height = 300;
                                        $oCuerpo->Width = 300;
                                        //$oCuerpo->EnterMode = "p";
                                        $oCuerpo->Value =  $frm["MensajeWebView2"];
                                        $oCuerpo->Create();
                                        ?>

                                    </div>
                                </div>



                            </div>

                            <div class="form-group first ">


                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 1</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView1" type=text size=25 name="UrlWebView1" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView1"] ?>">

                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 2</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView2" type=text size=25 name="UrlWebView2" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView2"] ?>">

                                    </div>
                                </div>

                            </div>


                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 3</label>
                                    <div class="col-sm-8">
                                        <input id="MensajeWebView3" type=text size=25 name="MensajeWebView3" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView3"] ?>">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 3</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView3" type=text size=25 name="UrlWebView3" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView3"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 4</label>
                                    <div class="col-sm-8">
                                        <input id="MensajeWebView4" type=text size=25 name="MensajeWebView4" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView4"] ?>">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 4</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView4" type=text size=25 name="UrlWebView4" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView4"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 5</label>
                                    <div class="col-sm-8">
                                        <input id="MensajeWebView5" type=text size=25 name="MensajeWebView5" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView5"] ?>">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 5</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView5" type=text size=25 name="UrlWebView5" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView5"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 6</label>
                                    <div class="col-sm-8">
                                        <input id="MensajeWebView6" type=text size=25 name="MensajeWebView6" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView6"] ?>">

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 6</label>
                                    <div class="col-sm-8">
                                        <input id="UrlWebView6" type=text size=25 name="UrlWebView6" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView6"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first">


                                T&eacute;rminos y Condiciones

                                <div class="col-sm-12">
                                    <?php
                                    $oCuerpo = new FCKeditor("Terminos");
                                    $oCuerpo->BasePath = "js/fckeditor/";
                                    $oCuerpo->Height = 400;
                                    //$oCuerpo->EnterMode = "p";
                                    $oCuerpo->Value =  $frm["Terminos"];
                                    $oCuerpo->Create();
                                    ?>
                                </div>


                            </div>


                            <!-- Cuotas sociales configuracion -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i>
                                    Parametros Factura Cuotas Sociales
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo Factura Cuotas Sociales</label>
                                    <div class="col-sm-8">
                                        <input id="TituloFacturaCuotasSociales" type="text" name="TituloFacturaCuotasSociales" class="col-xs-12" title="Titulo Factura Cuotas Sociales" value="<?= $frm["TituloFacturaCuotasSociales"] ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Campo Plazo Pago Factura</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $oCuerpo = new FCKeditor("CampoPlazoPagoFactura");
                                        $oCuerpo->BasePath = "js/fckeditor/";
                                        $oCuerpo->Height = 300;
                                        $oCuerpo->Width = 300;
                                        //$oCuerpo->EnterMode = "p";
                                        $oCuerpo->Value =  $frm["CampoPlazoPagoFactura"];
                                        $oCuerpo->Create();
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Campo Pie Pagina Factura</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $oCuerpo = new FCKeditor("CampoPiePaginaFactura");
                                        $oCuerpo->BasePath = "js/fckeditor/";
                                        $oCuerpo->Height = 300;
                                        $oCuerpo->Width = 300;
                                        //$oCuerpo->EnterMode = "p";
                                        $oCuerpo->Value =  $frm["CampoPiePaginaFactura"];
                                        $oCuerpo->Create();
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <!-- Cuotas sociales configuracion End -->
                            <!-- Pantalla de acceso configuracion -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i>
                                    Parametros Pantalla de acceso
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> No permitir multiple entrada sin tener salida?</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermitirMultipleAcceso"], "PermitirMultipleAcceso", "", "");
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Pantalla de acceso configuracion End -->
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