<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?> <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="form-group first ">

        <div class="form-group first">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Beneficiopara', LANGSESSION); ?>: </label>

                <div class="col-sm-8">
                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formPopUp("SeccionBeneficio", "Nombre", "Nombre", "IDSeccionBeneficio", $frm["IDSeccionBeneficio"], "[Seleccione categoria]", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Introduccion', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <textarea id="Introduccion" name="Introduccion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Introduccion', LANGSESSION); ?>"><?php echo $frm["Introduccion"]; ?></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DescripcionCorta', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'DescripcionCorta', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
            </div>
        </div>
    </div>
    <div class="form-group first"><?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> <div class="col-sm-12"> <?php
                                                                                                                                $oCuerpo = new FCKeditor("DescripcionHtml");
                                                                                                                                $oCuerpo->BasePath = "js/fckeditor/";
                                                                                                                                $oCuerpo->Height = 400;
                                                                                                                                //$oCuerpo->EnterMode = "p";
                                                                                                                                $oCuerpo->Value =  $frm["DescripcionHtml"];
                                                                                                                                $oCuerpo->Create();
                                                                                                                                ?> </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="number" id="Telefono" name="Telefono" placeholder="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" value="<?php echo $frm["Telefono"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PaginaWeb', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="PaginaWeb" name="PaginaWeb" placeholder="<?= SIMUtil::get_traduccion('', '', 'PaginaWeb', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'PaginaWeb', LANGSESSION); ?>" value="<?php echo $frm["PaginaWeb"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="number" id="Latitud" name="Latitud" placeholder="<?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?>" value="<?php echo $frm["Latitud"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Longitud" name="Longitud" placeholder="<?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?>" value="<?php echo $frm["Longitud"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?></label>
            <div class="col-sm-8">
                <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarCorreo', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarCorreo"], 'MostrarCorreo', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Correo', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Correo" name="Correo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Correo', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Correo', LANGSESSION); ?>" value="<?php echo $frm["Correo"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarIconoTelefono', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarTelefono"], 'OcultarTelefono', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarIconoWeb', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarPaginaWeb"], 'OcultarPaginaWeb', "class='input mandatory'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarMapa', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarMapa"], 'OcultarMapa', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarBotonRuta', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarBotonRuta"], 'OcultarBotonRuta', "class='input mandatory'") ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarUrlDetalle', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["OcultarUrlDetalle"], "OcultarUrlDetalle", "title=\"OcultarUrlDetalle\"") ?>
            </div>
        </div>



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'OcultarTelefonoDetalle', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["OcultarTelefonoDetalle"], "OcultarTelefonoDetalle", "title=\"OcultarTelefonoDetalle\"") ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'OcultarImagen', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarImagen"], 'OcultarImagen', "class='input mandatory'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Publicar', LANGSESSION); ?> </label>
            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>
            <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?> </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>1 </label>
            <input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
            <div class="col-sm-8">
                <? if (!empty($frm["Foto1"])) {
                    echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto1"] . "' height='300px' width='300px' > ";

                ?>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FotoPortada', LANGSESSION); ?> </label>
            <input name="FotoPortada" id=file class="" title="FotoPortada" type="file" size="25" style="font-size: 10px">
            <div class="col-sm-8">
                <? if (!empty($frm["FotoPortada"])) {
                    echo "<img src='" . CLASIFICADOS_ROOT . $frm["FotoPortada"] . "' height='300px' width='300px' > ";

                ?>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[FotoPortada]&campo=FotoPortada&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> </label>
            <div class="col-sm-8"> <?php
                                    $ruta_adjunto1file = string;
                                    if ($frm["Adjunto1File"]) {

                                        if (strstr(strtolower($frm["Adjunto1File"]), "http://"))
                                            $ruta_adjunto1file = $frm["Adjunto1File"];
                                        else
                                            $ruta_adjunto1file = CLASIFICADOS_ROOT . $frm["Adjunto1File"];
                                    ?> <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto1File"] ?></a>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Adjunto1File]&campo=Adjunto1File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a> <?php
                                                                                                                                                                                        } else {
                                                                                                                                                                                            ?> <input type="file" name="Adjunto1File" id="Adjunto1File" class="popup" title="Noticia Documento"> <?php
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                    ?>
            </div>
        </div>
    </div>





    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrara', LANGSESSION); ?>: </label>

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
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SeleccioneelGrupo', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
                    <option value="">><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option>
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
                <input type="hidden" name="SeleccionGrupo" id="SeleccionGrupo" value="">
            </div>
        </div>
    </div>

    <div id="SocioEspecifico" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "SE") echo "";
                                                                else echo "display:none"; ?> ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuarios', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-socios" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
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
                <input type="text" id="AccionInvitadoUsuario" name="AccionInvitadoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionarioEncuestas" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
                <br>
                <a id="agregar_empleado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_empleado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                <br>
                <select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8" multiple>
                    <?php
                    $item = 1;
                    $array_invitados = explode("|||", $frm["UsuarioSeleccion"]);
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
                <input type="hidden" name="UsuarioSeleccion" id="UsuarioSeleccion" value="">
            </div>
        </div>
    </div>


    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                    else echo $frm["IDClub"];  ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i> <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?> </button>
            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
        </div>
    </div>
</form>