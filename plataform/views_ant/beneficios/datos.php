<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?> <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="form-group first ">

        <div class="form-group first">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Beneficio para: </label>

                <div class="col-sm-8">
                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formPopUp("SeccionBeneficio", "Nombre", "Nombre", "IDSeccionBeneficio", $frm["IDSeccionBeneficio"], "[Seleccione categoria]", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Introduccion </label>
            <div class="col-sm-8">
                <textarea id="Introduccion" name="Introduccion" cols="10" rows="5" class="col-xs-12 mandatory" title="Introduccion"><?php echo $frm["Introduccion"]; ?></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion Corta </label>
            <div class="col-sm-8">
                <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
            </div>
        </div>
    </div>
    <div class="form-group first"> Descripcion <div class="col-sm-12"> <?php
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
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>
            <div class="col-sm-8">
                <input type="number" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pagina Web </label>
            <div class="col-sm-8">
                <input type="text" id="PaginaWeb" name="PaginaWeb" placeholder="Pagina Web" class="col-xs-12" title="Pagina Web" value="<?php echo $frm["PaginaWeb"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Latitud </label>
            <div class="col-sm-8">
                <input type="number" id="Latitud" name="Latitud" placeholder="Latitud" class="col-xs-12 mandatory" title="Latitud" value="<?php echo $frm["Latitud"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Longitud </label>
            <div class="col-sm-8">
                <input type="text" id="Longitud" name="Longitud" placeholder="Longitud" class="col-xs-12" title="Longitud" value="<?php echo $frm["Longitud"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
            <div class="col-sm-8">
                <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Icono Telefono </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarTelefono"], 'OcultarTelefono', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Icono Web </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarPaginaWeb"], 'OcultarPaginaWeb', "class='input mandatory'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Mapa </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarMapa"], 'OcultarMapa', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Boton Ruta </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarBotonRuta"], 'OcultarBotonRuta', "class='input mandatory'") ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Imagen </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarImagen"], 'OcultarImagen', "class='input mandatory'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificación ? </label>
            <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?> </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 1 </label>
            <input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
            <div class="col-sm-8">
                <? if (!empty($frm["Foto1"])) {
                    echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto1"] . "' >";
                ?>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf adjunto 1 </label>
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
                                                                                                                                                                                            ?> <input type="file" name="Adjunto1Documento" id="Adjunto1Documento" class="popup" title="Noticia Documento"> <?php
                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                            ?>
            </div>
        </div>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar a : </label>

            <div class="col-sm-8">
                <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="S" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "S") echo "checked"; ?> />Todos los Usuarios
                <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="SE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "SE") echo "checked"; ?> />Usuarios Especificos
                <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GS" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "GS") echo "checked"; ?> />Grupo de Usuarios
                <!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="E" title="DirigidoA"/>Todos los Empleado-->
                <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="EE" title="DirigidoA" <?php if ($frm["DirigidoAGeneral"] == "EE") echo "checked"; ?> />Empleados Especificos
                <!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GE" title="DirigidoA"/>Grupo de Empleados-->


            </div>
        </div>

    </div>


    <div id="SocioGrupo" class="form-group first " style="<?php if ($frm["DirigidoAGeneral"] == "GS") echo "";
                                                            else echo "display:none"; ?> ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seleccione el Grupo: </label>

            <div class="col-sm-8">
                <select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
                    <option value="">Seleccion Grupo</option>
                    <?php
                    $sql_grupos = "Select * From GrupoSocio Where IDClub = '" . SIMUser::get("club") . "'";
                    $result_grupos = $dbo->query($sql_grupos);
                    while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
                        <option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>" <?php if ($frm["IDGrupoSocio"] == $row_grupos["IDGrupoSocio"]) echo "selected";  ?>><?php echo $row_grupos["Nombre"]; ?></option>
                    <?php endwhile; ?>
                </select>
                <a href="gruposocio.php?action=add">Crear Grupo</a>

                <br>
                <a id="agregar_invitadoGrupo" href="#">Agregar</a> | <a id="borrar_invitadoGrupo" href="#">Borrar</a>
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
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuarios: </label>
            <div class="col-sm-8">
                <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho">
                <br>
                <a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
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
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Empleados: </label>
            <div class="col-sm-8">
                <input type="text" id="AccionInvitadoUsuario" name="AccionInvitadoUsuario" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-funcionarioEncuestas" title="número de derecho">
                <br>
                <a id="agregar_empleado" href="#">Agregar</a> | <a id="borrar_empleado" href="#">Borrar</a>
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
                <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> </button>
            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
        </div>
    </div>
</form>