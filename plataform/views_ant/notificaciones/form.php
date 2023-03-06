<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="TituloMensaje" name="TituloMensaje" placeholder="<?= SIMUtil::get_traduccion('', '', 'TituloMensaje', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TituloMensaje', LANGSESSION); ?>" value="<?php echo $frm["TituloMensaje"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Link" name="Link" placeholder="<?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?>" value="<?php echo $frm["Link"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajemax70caracteres', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <textarea id="Mensaje" name="Mensaje" cols="10" rows="5" class="col-xs-12 mandatory" onKeyPress="valida_longitud()" title="<?= SIMUtil::get_traduccion('', '', 'Mensajemax70caracteres', LANGSESSION); ?>"><?php echo $frm["Mensaje"]; ?></textarea>
                                    <input type="text" name="numerocaracter" id="numerocaracter" value="0" readonly>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'HoraEnvio', LANGSESSION); ?></label>
                                <div class="col-sm-8">
                                    <select name="HoraEnvio" id="HoraEnvio">
                                        <?php
                                        $Hora = date("00:00:00");
                                        ?>
                                        <option value="<?php echo $Hora ?>" <?php if ($frm["HoraEnvio"] == $Hora) echo "selected" ?>><?php echo $Hora ?></option>
                                        <?php
                                        for ($i = 0; $i < 47; $i++) :
                                            $HoraSumada = strtotime('+30 minute', strtotime($Hora));
                                            $Hora = date("H:i:s", $HoraSumada);
                                        ?>
                                            <option value="<?php echo $Hora ?>" <?php if ($frm["HoraEnvio"] == $Hora) echo "selected" ?>><?php echo $Hora ?></option>
                                        <?php
                                        endfor;
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicioEnvio', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicioEnvio', LANGSESSION); ?>" class="col-xs-12 calendar " title="<?= SIMUtil::get_traduccion('', '', 'FechaInicioEnvio', LANGSESSION); ?>" value="<?php if ($frm["FechaInicio"] == "0000-00-00" || $frm["FechaInicio"] == "") /* echo date("Y-m-d") */;
                                                                                                                                                                                                                                                                                                        else echo $frm["FechaInicio"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFinEnvio', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFinEnvio', LANGSESSION); ?>" class="col-xs-12 calendar " title="<?= SIMUtil::get_traduccion('', '', 'FechaFinEnvio', LANGSESSION); ?>" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") /* echo date("Y-m-d") */;
                                                                                                                                                                                                                                                                                            else echo $frm["FechaFin"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-12">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php
                                                        if (!empty($frm["Dias"])) :
                                                            $array_dias = explode("|", $frm["Dias"]);
                                                        endif;
                                                        array_pop($array_dias);
                                                        foreach ($Dia_array as $id_dia => $dia) :  ?> <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if (in_array($id_dia, $array_dias) && $dia != "") echo "checked"; ?>><?php echo $dia; ?> <?php endforeach; ?> </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-12">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Enviara', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="S" title="DirigidoA" /><?= SIMUtil::get_traduccion('', '', 'TodoslosSocios', LANGSESSION); ?> <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="SE" title="DirigidoA" /><?= SIMUtil::get_traduccion('', '', 'SociosEspecificos', LANGSESSION); ?> <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GS" title="DirigidoA" /><?= SIMUtil::get_traduccion('', '', 'GrupodeSocios', LANGSESSION); ?> <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="E" title="DirigidoA" /><?= SIMUtil::get_traduccion('', '', 'TodoslosEmpleados', LANGSESSION); ?>
                                    <!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="EE" title="DirigidoA"/>Empleados Especificos-->
                                    <input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GE" title="DirigidoA" /><?= SIMUtil::get_traduccion('', '', 'GrupodeEmpleados', LANGSESSION); ?>
                                </div>
                            </div>
                        </div>
                        <div id="SocioEspecifico" class="form-group first " style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socios', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-socios" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>">
                                    <br><a id="agregar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_invitado" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                    <br>
                                    <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple> <?php
                                                                                                                    $item = 1;
                                                                                                                    foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                                                                                        $item--;
                                                                                                                        if ($datos_invitado["IDSocio"] > 0) :
                                                                                                                            $nombre_socio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_invitado["IDSocio"] . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_invitado["IDSocio"] . "'"));
                                                                                                                    ?> <option value="<?php echo "socio-" . $datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?></option> <?php
                                                                                                                                                                                                                                else : ?> <option value="<?php echo "externo-" . $datos_invitado["Nombre"]; ?>"><?php echo $datos_invitado["Nombre"]; ?></option> <?php
                                                                                                                                                                                                                                                                                                                                                                endif;
                                                                                                                                                                                                                                                                                                                                                            endforeach;
                                                                                                                                                                                                                                                                                                                                                                    ?> </select>
                                    <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                </div>
                            </div>
                        </div>
                        <div id="SocioGrupo" class="form-group first " style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'SeleccioneelGrupo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
                                        <option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option> <?php
                                                                                                                                            $sql_grupos = "Select * From GrupoSocio Where IDClub = '" . SIMUser::get("club") . "'";
                                                                                                                                            $result_grupos = $dbo->query($sql_grupos);
                                                                                                                                            while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?> <option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>"><?php echo $row_grupos["Nombre"]; ?></option> <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="EmpleadoEspecifico" class="form-group first " style="display:none">
                        </div>
                        <div id="EmpleadoGrupo" class="form-group first " style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SeleccioneelGrupo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name="IDGrupoUsuario" id="IDGrupoUsuario" class="form-control">
                                        <option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option> <?php
                                                                                                                                            $sql_grupos = "Select * From GrupoUsuario Where IDClub = '" . SIMUser::get("club") . "'";
                                                                                                                                            $result_grupos = $dbo->query($sql_grupos);
                                                                                                                                            while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?> <option value="<?php echo $row_grupos["IDGrupoUsuario"]; ?>"><?php echo $row_grupos["Nombre"]; ?></option> <?php endwhile; ?>
                                    </select>
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<script language="javascript">
    contenido_textarea = ""
    num_caracteres_permitidos = 69;

    function valida_longitud() {
        num_caracteres = document.forms[1].Mensaje.value.length
        if (num_caracteres > num_caracteres_permitidos) {
            document.forms[1].Mensaje.value = contenido_textarea;
        } else {
            contenido_textarea = document.forms[1].Mensaje.value;
        }
        document.forms[1].numerocaracter.value = num_caracteres;
    }
</script>
<?
include("cmp/footer_scripts.php");
?>