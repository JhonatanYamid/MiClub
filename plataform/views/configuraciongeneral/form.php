<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?> <div class="widget-box transparent" id="recent-box">
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
                            <?php if (SIMUser::get("club") == 189) : ?>
                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> VALIDAR DEUDA IZACARAGUA </label>
                                        <div class="col-sm-8">
                                            <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarDeudaIzcaragua"], 'ValidarDeudaIzcaragua', "class='input '") ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa  fa-check-square green"></i> General
                                </h3>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pais </label>
                                    <div class="col-sm-8"> <?php echo SIMHTML::formPopUp("Pais", "Nombre", "Nombre", "IDPais", $frm["IDPais"], "[Seleccione Pais]", "form-control", "title = \"Pais\"") ?> </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Zona Horaria </label>
                                    <div class="col-sm-8"> <?php echo SIMHTML::formPopUp("ZonaHoraria", "Nombre", "Nombre", "IDZonaHoraria", $frm["IDZonaHoraria"], "[Seleccione Zona Horaria]", "form-control", "title = \"Zona Horaria\"") ?> </div>
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
                                    <div class="col-sm-8"> <?= SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm['ActivarNotificacionesPeriodicas'], 'ActivarNotificacionesPeriodicas', 'class="input"') ?> </div>
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
                                        <select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8" multiple> <?php
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
                                                                                                                                    ?> <option value="<?php echo  $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option> <?php
                                                                                                                                                                                                                            endif;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                    endforeach; ?> </select>
                                        <input type="hidden" name="UsuarioSeleccion" id="UsuarioSeleccion" value="">
                                    </div>
                                </div>
                                
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo Notificación Comentario Noticia </label>
                                    <div class="col-sm-8">
                                        <input id=CorreoNotificacionComentarioNoticia type=text name=CorreoNotificacionComentarioNoticia class="col-xs-12" title="CorreoNotificacionComentarioNoticia" value="<?= $frm["CorreoNotificacionComentarioNoticia"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar Botón Busqueda General </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonBusquedaGeneral"], 'MostrarBotonBusquedaGeneral', "class='input '") ?>
                                </div>
                            </div>

                            <div class="form-group first"> Mensaje para invitados <div class="col-sm-12"> <?php
                                                                                                            $oCuerpoInvi = new FCKeditor("MensajeInvitados");
                                                                                                            $oCuerpoInvi->BasePath = "js/fckeditor/";
                                                                                                            $oCuerpoInvi->Height = 200;
                                                                                                            //$oCuerpo->EnterMode = "p";
                                                                                                            $oCuerpoInvi->Value =  $frm["MensajeInvitados"];
                                                                                                            $oCuerpoInvi->Create();
                                                                                                            ?> </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa  fa-barcode green"></i> Configuraci&oacute;n Carne
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
                                            <a href="<? echo "$script.php?action=delfoto&foto=$frm[IconoTelefono]&campo=IconoTelefono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
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
                                            <a href="<? echo "$script.php?action=delfoto&foto=$frm[IconoEmail]&campo=IconoEmail&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                        <input name="IconoEmail" id=IconoEmail class="col-xs-12" title="Icono Mail" type="file" size="25" style="font-size: 10px">
                                    </div>
                                </div>
                            </div>



                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">

                                    <i class="ace-icon fa fa-eye-slash green"></i> Splash
                                </h3>
                            </div>

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
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar splah en el Home</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarSplashHome"], 'MostrarSplashHome', "class='input '") ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Texto para el boton del splash en home</label>
                                    <div class="col-sm-8">
                                        <input id="LabelBotonSplashHome" type=text size=25 name="LabelBotonSplashHome" class="col-xs-12" title="LabelBotonSplashHome" value="<?= $frm["LabelBotonSplashHome"] ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Imagen splash Home </label>
                                    <div class="col-sm-8">
                                        <? if (!empty($frm[ImagenSplashHome])) {
                                            echo "<img src='" . CLUB_ROOT . "$frm[ImagenSplashHome]' width=55 >";
                                        ?>
                                            <a href="<? echo "$script.php?action=delfoto&foto=$frm[ImagenSplashHome]&campo=ImagenSplashHome&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                        <input name="ImagenSplashHome" id=file class="col-xs-12" title="ImagenSplashHome" type="file" size="25" style="font-size: 10px">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Link para splah de home</label>
                                    <div class="col-sm-8">
                                        <input id="LinkSplashHome" type=text size=25 name="LinkSplashHome" class="col-xs-12" title="LinkSplashHome" value="<?= $frm["LinkSplashHome"] ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Identificar de un modulo para ir (solo valido para modulos infinitos) </label>
                                    <div class="col-sm-8">
                                        <input id="IDModuloLinkSplashHome" type=text size=25 name="IDModuloLinkSplashHome" class="col-xs-12" title="IDModuloLinkSplashHome" value="<?= $frm["IDModuloLinkSplashHome"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-12">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrara', LANGSESSION); ?> : </label>

                                    <div class="col-sm-8">
                                        <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="S" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "S") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'TodoslosUsuarios', LANGSESSION); ?>
                                        <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="SE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "SE") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'UsuariosEspecificos', LANGSESSION); ?>
                                        <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GS" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "GS") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'GrupodeUsuarios', LANGSESSION); ?>
                                        <!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="E" title="DirigidoA"/>Todos los Empleado-->
                                        <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="EE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "EE") echo "checked"; ?> /><?= SIMUtil::get_traduccion('', '', 'EmpleadosEspecificos', LANGSESSION); ?>
                                        <!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GE" title="DirigidoA"/>Grupo de Empleados-->


                                    </div>
                                </div>

                            </div>


                            <div id="SocioGrupo" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "GS") echo "";
                                                                                    else echo "display:none"; ?> ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?>: </label>

                                    <div class="col-sm-8">
                                        <select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
                                            <option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option>
                                            <?php
                                            $sql_grupos = "Select * From GrupoSocio Where IDClub = '" . SIMUser::get("club") . "'";
                                            $result_grupos = $dbo->query($sql_grupos);
                                            while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
                                                <option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>" <?php if ($frm["IDGrupoSocio"] == $row_grupos["IDGrupoSocio"]) echo "selected";  ?>><?php echo $row_grupos["Nombre"]; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                        <a href="gruposocio.php?action=add"><?= SIMUtil::get_traduccion('', '', 'CrearGrupo', LANGSESSION); ?></a>

                                        <br>
                                        <a id="agregar_invitadoGrupo" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitadoGrupo" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                        <br>
                                        <select name="SocioInvitado[]" id="SocioInvitadoGrupo" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_invitados = explode("|||", $frm["SeleccionGrupo"]);
                                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                if (!empty($datos_invitado)) {
                                                    $array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;
                                                    $IDSocioInvitacion = $array_datos_invitados[1];
                                                    if ($IDSocioInvitacion > 0) :
                                                        $nombre_socio = utf8_encode($dbo->getFields("GrupoSocio", "Nombre", "IDGrupoSocio = '" . $IDSocioInvitacion . "'"));
                                            ?>
                                                        <option value="<?php echo "grupo-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="text" name="SeleccionGrupo" id="SeleccionGrupo" value="">
                                    </div>
                                </div>
                            </div>

                            <div id="SocioEspecifico" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "SE") echo "";
                                                                                        else echo "display:none"; ?> ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuarios', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-socios" title=" <?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
                                        <br>
                                        <a id="agregar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                        <br>
                                        <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_invitados = explode("|||", $frm["InvitadoSeleccion"]);
                                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                if (!empty($datos_invitado)) {
                                                    $array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;
                                                    $IDSocioInvitacion = $array_datos_invitados[1];
                                                    if ($IDSocioInvitacion > 0) :
                                                        $nombre_socio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitacion . "'"));
                                            ?>
                                                        <option value="<?php echo "socio-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                    </div>
                                </div>
                            </div>

                            <div id="EmpleadoEspecifico" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "EE") echo "";
                                                                                            else echo "display:none"; ?> ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Empleados', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="AccionInvitadoUsuario1" name="AccionInvitadoUsuario1" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-add" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
                                        <br>
                                        <a id="agregar_empleado1" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_empleado1" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                        <br>
                                        <select name="SocioInvitadoUsuario1[]" id="SocioInvitadoUsuario1" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_invitados = explode("|||", $frm["UsuarioEmpleado"]);
                                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                if (!empty($datos_invitado)) {
                                                    $array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;
                                                    $IDSocioInvitacion = $array_datos_invitados[1];
                                                    if ($IDSocioInvitacion > 0) :
                                                        $nombre_socio = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Usuario", "Apellido", "IDUsuario = '" . $IDSocioInvitacion . "'"));
                                            ?>
                                                        <option value="<?php echo "usuario-" . $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="UsuarioEmpleado" id="UsuarioEmpleado" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-gavel green"></i> Clasificados
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
                                    <i class="ace-icon fa fa-info-circle green"></i> Publicidad
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
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Publicidad" <?php if ($frm["TipoHeaderApp"] == "Publicidad") echo "checked"; ?>> Publicidad (rota im&aacute;genes) <br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Clasico" <?php if ($frm["TipoHeaderApp"] == "Clasico") echo "checked"; ?>> Clasico (imagen fija logo club) <br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="PublicidadFoto" <?php if ($frm["TipoHeaderApp"] == "PublicidadFoto") echo "checked"; ?>> PublicidadFoto (Publicidad mas foto)<br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Noticias" <?php if ($frm["TipoHeaderApp"] == "Noticias") echo "checked"; ?>> Solo Noticias<br>
                                        <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Mixta" <?php if ($frm["TipoHeaderApp"] == "Mixta") echo "checked"; ?>> Foto y Noticias
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo Rotar Publicidad Header? </label>
                                    <div class="col-sm-8">
                                        <input id="TiempoPublicidadHeader" type=text size=25 name="TiempoPublicidadHeader" class="col-xs-12" title="Tiempo Publicidad Header" value="<?= $frm["TiempoPublicidadHeader"] ?>">segundos
                                    </div>
                                </div>
                            </div>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i> Inicio de App
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Socios deben tener segunda clave? </label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ManejoSegundaClave"], 'ManejoSegundaClave', "class='input '") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Cambio segunda clave </label>
                                    <div class="col-sm-8">
                                        <input id=LabelCambioSegundaClave type=text size=25 name=LabelCambioSegundaClave class="col-xs-12" title="Label Cambio Segunda Clave" value="<?= $frm["LabelCambioSegundaClave"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Solicitar editar perfil al ingresar al app? </label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaEditarPerfil"], 'SolicitaEditarPerfil', "class='input '") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio Editar Perfil </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="FechaInicioEditarPerfil" name="FechaInicioEditarPerfil" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php if ($frm["FechaInicioEditarPerfil"] == "" || $frm["FechaInicioEditarPerfil"] == "0000-00-00") echo "";
                                                                                                                                                                                                    else echo $frm["FechaInicioEditarPerfil"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Editar perfil </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="SolicitaEditarPefilLabel" name="SolicitaEditarPefilLabel" placeholder="Solicita Editar PefilLabel" class="col-xs-12" title="Solicita Editar PefilLabel" value="<?php echo $frm["SolicitaEditarPefilLabel"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Registro Usuario</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRegistroUsuario"], 'PermiteRegistroUsuario', "class='input '") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Registro Usuario </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="UrlRegistroUsuario" name="UrlRegistroUsuario" placeholder="Url Registro Usuario" class="col-xs-12" title="UrlRegistroUsuario" value="<?php echo $frm["UrlRegistroUsuario"]; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Registro Usuario </label>
                                    <div class="col-sm-8">
                                        <input id=EmailCartera type=text name=LabelRegistroUsuario class="col-xs-12" title="LabelRegistroUsuario" value="<?= $frm["LabelRegistroUsuario"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar icono mis reservas home superior</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotomMisReservasHome"], 'MostrarBotomMisReservasHome', "class='input '") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar icono de ojo para ver contraseña</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonOjoContrasenaLogin"], 'MostrarBotonOjoContrasenaLogin', "class='input '") ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Pedir numero de documento para recuperar la clave</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaCambioContrasenaDocumento"], 'SolicitaCambioContrasenaDocumento', "class='input '") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar inicio publico</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarInicioPublico"], 'MostrarInicioPublico', "class='input '") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label solicitar cambio de foto de perfil </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="SolicitarCambioFotoPerfilLabel" name="SolicitarCambioFotoPerfilLabel" placeholder="Label Solicitar Cambio Foto Perfil" class="col-xs-12" title="label foto perfil" value="<?php echo $frm["SolicitarCambioFotoPerfilLabel"]; ?>" required>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite enviar correo al socio al momento de activarlo</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarCorreoSocio"], 'EnviarCorreoSocio', "class='input '") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Validar webservices externos (solo poner en no cuando el servidor externo no responda)</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ConectarWsExterno"], 'ConectarWsExterno', "class='input '") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i> Formatos de Fechas y Horas
        </h3>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Formato Fecha </label>
            <div class="col-sm-8">
                <select name="FormatoFecha" class="form-control">
                    <option value="">[SELECCIONA UNA OPCION]</option> <?php
                                                                        foreach (SIMResources::$formatos_fecha as $Formato => $FormatoDate) :
                                                                        ?> <option value="<?= $Formato ?>" <?= $Formato == $frm[FormatoFecha] ? "selected" : "" ?>> <?= $Formato . " Ejemplo: (" . date($FormatoDate) . ")" ?></option> <?php
                                                                                                                                                                                                                                    endforeach;
                                                                                                                                                                                                                                        ?>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Formato Hora </label>
            <div class="col-sm-8">
                <select name="FormatoHora" class="form-control">
                    <option value="">[SELECCIONA UNA OPCION]</option> <?php
                                                                        foreach (SIMResources::$formatos_hora as $Formato => $FormatoDate) :
                                                                        ?> <option value="<?= $Formato ?>" <?= $Formato == $frm[FormatoHora] ? "selected" : "" ?>> <?= $Formato . " Ejemplo: (" . date($FormatoDate) . ")" ?></option> <?php
                                                                                                                                                                                                                                    endforeach;
                                                                                                                                                                                                                                        ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Formato Fecha Hora</label>
            <div class="col-sm-8">
                <select name="FormatoFechaHora" class="form-control">
                    <option value="">[SELECCIONA UNA OPCION]</option> <?php
                                                                        foreach (SIMResources::$formatos_fecha_hora as $Formato => $FormatoDate) :
                                                                        ?> <option value="<?= $Formato ?>" <?= $Formato == $frm[FormatoFechaHora] ? "selected" : "" ?>> <?= $Formato . " Ejemplo: (" . date($FormatoDate) . ")" ?></option> <?php
                                                                                                                                                                                                                                        endforeach;
                                                                                                                                                                                                                                            ?>
                </select>
            </div>
        </div>
    </div>


                            <!-- INICIO VENTANA OBJETOS -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i> Ventana Objetos
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
                                    <i class="ace-icon fa fa-info-circle green"></i> Ventana Vacunación
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
                                    <i class="ace-icon fa fa-info-circle green"></i> Campo Fecha Nacimiento
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
                                    <i class="ace-icon fa fa-info-circle green"></i> Campo Observacion General
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
                                    <i class="ace-icon fa fa-credit-card green"></i> Parametros WebView
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 1</label>
                                    <div class="col-sm-8"> <?php
                                                            $oCuerpo = new FCKeditor("MensajeWebView1");
                                                            $oCuerpo->BasePath = "js/fckeditor/";
                                                            $oCuerpo->Height = 300;
                                                            $oCuerpo->Width = 300;
                                                            //$oCuerpo->EnterMode = "p";
                                                            $oCuerpo->Value =  $frm["MensajeWebView1"];
                                                            $oCuerpo->Create();
                                                            ?> </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 2</label>
                                    <div class="col-sm-8"> <?php
                                                            $oCuerpo = new FCKeditor("MensajeWebView2");
                                                            $oCuerpo->BasePath = "js/fckeditor/";
                                                            $oCuerpo->Height = 300;
                                                            $oCuerpo->Width = 300;
                                                            //$oCuerpo->EnterMode = "p";
                                                            $oCuerpo->Value =  $frm["MensajeWebView2"];
                                                            $oCuerpo->Create();
                                                            ?> </div>
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
                            <div class="form-group first"> T&eacute;rminos y Condiciones <div class="col-sm-12"> <?php
                                                                                                                    $oCuerpo = new FCKeditor("Terminos");
                                                                                                                    $oCuerpo->BasePath = "js/fckeditor/";
                                                                                                                    $oCuerpo->Height = 400;
                                                                                                                    //$oCuerpo->EnterMode = "p";
                                                                                                                    $oCuerpo->Value =  $frm["Terminos"];
                                                                                                                    $oCuerpo->Create();
                                                                                                                    ?> </div>
                            </div>
                            <!-- Cuotas sociales configuracion -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i> Parametros Factura Cuotas Sociales
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
                                    <div class="col-sm-8"> <?php
                                                            $oCuerpo = new FCKeditor("CampoPlazoPagoFactura");
                                                            $oCuerpo->BasePath = "js/fckeditor/";
                                                            $oCuerpo->Height = 300;
                                                            $oCuerpo->Width = 300;
                                                            //$oCuerpo->EnterMode = "p";
                                                            $oCuerpo->Value =  $frm["CampoPlazoPagoFactura"];
                                                            $oCuerpo->Create();
                                                            ?> </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Campo Pie Pagina Factura</label>
                                    <div class="col-sm-8"> <?php
                                                            $oCuerpo = new FCKeditor("CampoPiePaginaFactura");
                                                            $oCuerpo->BasePath = "js/fckeditor/";
                                                            $oCuerpo->Height = 300;
                                                            $oCuerpo->Width = 300;
                                                            //$oCuerpo->EnterMode = "p";
                                                            $oCuerpo->Value =  $frm["CampoPiePaginaFactura"];
                                                            $oCuerpo->Create();
                                                            ?> </div>
                                </div>
                            </div>
                            <!-- Cuotas sociales configuracion End -->
                            <!-- Pantalla de acceso configuracion -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i> Parametros Pantalla de acceso
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir multiple entrada sin tener salida?</label>
                                    <div class="col-sm-8"> <?php
                                                            echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermitirMultipleAcceso"], "PermitirMultipleAcceso", "", "");
                                                            ?> </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Limitar ingresos a la fecha final del contrato en pantalla de accesos?</label>
                                    <div class="col-sm-8"> <?php
                                                            echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["LimitarIngresosPorFechaFinalContrato"], "LimitarIngresosPorFechaFinalContrato", "", "");
                                                            ?> </div>
                                </div>
                            </div>
                            <!-- Pantalla de acceso configuracion End -->
                            <!-- Configuracion Invitados -->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-users green"></i>
                                    Configuraci&oacute;n Invitados
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Filtrar Tipo Invitado por Tipo de Socio?</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $frm['FiltrarTipoInvitado'] = ($frm['FiltrarTipoInvitado'] == '') ? 'N' : $frm['FiltrarTipoInvitado'];
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["FiltrarTipoInvitado"], "FiltrarTipoInvitado", "", "");
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Compartir Codigo QR por whatsapp</label>
                                    <div class="col-sm-8"> <?php
                                                            echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermitePopupCompartir"], "PermitePopupCompartir", "", "");
                                                            ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Boton de Ver QR en app en "mis Invitados"?</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["VerQrInvitados"], "VerQrInvitados", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Boton de Ver QR en app en "mis Contratistas"?</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["VerQrContratistas"], "VerQrContratistas", "", "");
                                        ?>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Limitar invitados por Acción?</label>
                                    <div class="col-sm-8">
                                        <?php
                                        if ($frm['LimitarInvitadosPorAccion'] == '' || $frm['LimitarInvitadosPorAccion'] == 'N') {
                                            $frm['LimitarInvitadosPorAccion'] = 'N';
                                            $display = 'style="display: none"';
                                        } else {
                                            $display = 'style="display: block"';
                                        }

                                        $frm['LimitarInvitadosPorAccion'] = ($frm['LimitarInvitadosPorAccion'] == '') ? 'N' : $frm['LimitarInvitadosPorAccion'];
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["LimitarInvitadosPorAccion"], "LimitarInvitadosPorAccion", "class='LimitarInvitadosPorAccion'", "LimitarInvitadosPorAccion");
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 NumeroInvitadosPorAccion" <?= $display; ?>>
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">N&uacute;mero de invitados al día por acci&oacute;n</label>
                                    <div class="col-sm-8">
                                        <input id="MaxInvitadosDiaPorAccion" type="number" name="MaxInvitadosDiaPorAccion" class="col-xs-12" title="Número de invitados al día por acción" value="<?= $frm["MaxInvitadosDiaPorAccion"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Invitados Anteriores</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $frm['VerInvitadosAnteriores'] = ($frm['VerInvitadosAnteriores'] == '') ? 'N' : $frm['VerInvitadosAnteriores'];
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["VerInvitadosAnteriores"], "VerInvitadosAnteriores", "", "VerInvitadosAnteriores");
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al invitar </label>

                                    <div class="col-sm-8">
                                        <input id="LabelInvitacion" type=text size=25 name="LabelInvitacion" class="col-xs-12" title="Label Invitacion" value="<?= $frm["LabelInvitacion"]; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i> Única sesión por Dispositivo
                                </h3>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar que solo se permita 1 sesion por dispositivo x usuario</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["UnicaSesionPorDispositivo"], "UnicaSesionPorDispositivo", "", "");
                                        ?>
                                    </div>
                                </div>
                            </div>


                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i> Configuración Módulo Ingresos empleados
                                </h3>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo notificación nuevo ingreso</label>
                                    <div class="col-sm-8">
                                        <input id="CorreoJefeIngreso" type="text" size="250" name="CorreoJefeIngreso" class="col-xs-12" title="Correo notificación nuevo ingreso" value="<?= $frm["CorreoJefeIngreso"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i> Configuración Handicap
                                </h3>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1">Texto Header Seleccione Club</label>
                                    <div class="col-sm-8">
                                        <input id="LabelHeaderSeleccioneClub" type="text" name="LabelHeaderSeleccioneClub" class="col-xs-12" title="Texto Header Seleccione Club" value="<?= $frm["LabelHeaderSeleccioneClub"] ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Header Seleccione Marca</label>
                                    <div class="col-sm-8">
                                        <input id="LabelHeaderSeleccioneMarca" type="text" name="LabelHeaderSeleccioneMarca" class="col-xs-12" title="Texto Header Seleccione Marca" value="<?= $frm["LabelHeaderSeleccioneMarca"] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Header Seleccione Campo</label>
                                    <div class="col-sm-8">
                                        <input id="LabelHeaderSeleccioneCampo" type="text" name="LabelHeaderSeleccioneCampo" class="col-xs-12" title="Texto Header Seleccione Campo" value="<?= $frm["LabelHeaderSeleccioneCampo"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i> Parametros Plataforma de pago
                                </h3>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon"></i> Pay Way
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Pay Way </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="URLPayWay" name="URLPayWay" placeholder="URL PayWay" class="col-xs-12" title="URL PayWay" value="<?php echo $frm["URLPayWay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> MerchantID PayWay</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="MerchantIDPayWay" name="MerchantIDPayWay" placeholder="MerchantID PayWay" class="col-xs-12" title="MerchantID PayWay" value="<?php echo $frm["MerchantIDPayWay"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> FormID PayWay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="FormIDPayWay" name="FormIDPayWay" placeholder="FormID PayWay" class="col-xs-12" title="FormID PayWay" value="<?php echo $frm["FormIDPayWay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> TerminalID PayWay</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="TerminalIDPayWay" name="TerminalIDPayWay" placeholder="TerminalID PayWay" class="col-xs-12" title="TerminalID PayWay" value="<?php echo $frm["TerminalIDPayWay"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Retorno PayWay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="UrlRetornoPayWay" name="UrlRetornoPayWay" placeholder="UrlRetorno PayWay" class="col-xs-12" title="UrlRetorno PayWay" value="<?php echo $frm["UrlRetornoPayWay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> APIKEY PayWay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="APIKEY_PayWay" name="APIKEY_PayWay" placeholder="APIKEY PayWay" class="col-xs-12" title="APIKEY PayWay" value="<?php echo $frm["APIKEY_PayWay"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon"></i> Luka Pay
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL LukaPay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="URLLukaPay" name="URLLukaPay" placeholder="URL LukaPay" class="col-xs-12" title="URL LukaPay" value="<?php echo $frm["URLLukaPay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Prubas LukaPay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="URLLukaPayPruebas" name="URLLukaPayPruebas" placeholder="URL LukaPay Pruebas" class="col-xs-12" title="URL LukaPay Pruebas" value="<?php echo $frm["URLLukaPayPruebas"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario LukaPay</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="UsuarioLukaPay" name="UsuarioLukaPay" placeholder="Usaurio LukaPay" class="col-xs-12" title="Usaurio LukaPay" value="<?php echo $frm["UsuarioLukaPay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave LukaPay </label>
                                    <div class="col-sm-8">
                                        <input type="password" id="ClaveLukaPay" name="ClaveLukaPay" placeholder="Clave LukaPay" class="col-xs-12" title="Clave LukaPay" value="<?php echo $frm["ClaveLukaPay"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Mi Club Pasarela LukaPay </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="UrlLocalLukaPay" name="UrlLocalLukaPay" placeholder="UrlRetorno LukaPay" class="col-xs-12" title="UrlRetorno LukaPay" value="<?php echo $frm["UrlLocalLukaPay"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo </label>
                                    <div class="col-sm-8">
                                        <select name="IsTestLukaPay" id="IsTestLukaPay" class="form-control">
                                            <option value="1" <?php if ($frm["IsTestLukaPay"] == 1) echo "selected"; ?>>Pruebas</option>
                                            <option value="0" <?php if ($frm["IsTestLukaPay"] == 0) echo "selected"; ?>>Producción</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon"></i> Mercado Pago
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL MercadoPago </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="URLMercadoPago" name="URLMercadoPago" placeholder="URL MercadoPago" class="col-xs-12" title="URL MercadoPago" value="<?php echo $frm["URLMercadoPago"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> AccessToken MercadoPago</label>
                                    <div class="col-sm-8">
                                        <input type="password" id="AccessTokenMercadoPago" name="AccessTokenMercadoPago" placeholder="AccessToken MercadoPago" class="col-xs-12" title="AccessToken MercadoPago" value="<?php echo $frm["AccessTokenMercadoPago"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> PublicKey MercadoPago </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="PublicKeyMercadoPago" name="PublicKeyMercadoPago" placeholder="PublicKey MercadoPago" class="col-xs-12" title="PublicKey MercadoPago" value="<?php echo $frm["PublicKeyMercadoPago"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo </label>
                                    <div class="col-sm-8">
                                        <select name="IsTestMercadoPago" id="IsTestMercadoPago" class="form-control">
                                            <option value="1" <?php if ($frm["IsTestMercadoPago"] == 1) echo "selected"; ?>>Pruebas</option>
                                            <option value="0" <?php if ($frm["IsTestMercadoPago"] == 0) echo "selected"; ?>>Producción</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon"></i> Yappy
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Yappy </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="URLYappy" name="URLYappy" placeholder="URL Yappy" class="col-xs-12" title="URL Yappy" value="<?php echo $frm["URLYappy"]; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ID_DEL_COMERCIO_YAPPY</label>
                                    <div class="col-sm-8">
                                        <input type="password" id="ID_DEL_COMERCIO_YAPPY" name="ID_DEL_COMERCIO_YAPPY" placeholder="ID_DEL_COMERCIO_YAPPY" class="col-xs-12" title="ID_DEL_COMERCIO_YAPPY" value="<?php echo $frm["ID_DEL_COMERCIO_YAPPY"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> CLAVE_SECRETA_YAPPY</label>
                                    <div class="col-sm-8">
                                        <input type="password" id="CLAVE_SECRETA_YAPPY" name="CLAVE_SECRETA_YAPPY" placeholder="CLAVE_SECRETA" class="col-xs-12" title="CLAVE_SECRETA" value="<?php echo $frm["CLAVE_SECRETA_YAPPY"]; ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo </label>
                                    <div class="col-sm-8">
                                        <select name="IsTestYappy" id="IsTestYappy" class="form-control">
                                            <option value="1" <?php if ($frm["IsTestYappy"] == 1) echo "selected"; ?>>Pruebas</option>
                                            <option value="0" <?php if ($frm["IsTestYappy"] == 0) echo "selected"; ?>>Producción</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Configuracion Notificaciones -->

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon"></i> Notificaciones
                                </h3>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar abrir las notificaciones</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["SolicitaAbrirNotificaciones"], "SolicitaAbrirNotificaciones", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al abrir notificaciones</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="LabelAbrirNotificaciones" name="LabelAbrirNotificaciones" placeholder="Label al abrir notificaciones" class="col-xs-12" title="LabelAbrirNotificaciones" value="<?php echo $frm["LabelAbrirNotificaciones"]; ?>">
                                    </div>
                                </div>
                            </div>


                            <!-- Configuracion Invitados End -->
                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> </button>
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