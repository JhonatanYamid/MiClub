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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Club', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <select name="IDClub" id="IDClub" class="form-control">
                                        <option value="">SELECCIONE EL CLUB</option> <?php
                                                                                        if (SIMUser::get("Nivel") == 0) :
                                                                                            $condicion_club = "  1";
                                                                                        else :
                                                                                            $condicion_club = " IDClub = '" . SIMUser::get("club") . "' OR IDClubPadre = '" . SIMUser::get("club") . "'";
                                                                                        endif;

                                                                                        $sql_club_lista = "Select * From Club Where $condicion_club ";
                                                                                        $qry_club_lista = $dbo->query($sql_club_lista);
                                                                                        while ($r_club_lista = $dbo->fetchArray($qry_club_lista)) : ?> <option value="<?php echo $r_club_lista["IDClub"]; ?>" <?php if ($r_club_lista["IDClub"] == $frm["IDClub"]) echo "selected";  ?>><?php echo $r_club_lista["Nombre"]; ?></option> <?php
                                                                                                                                                                                                                                                                                                                                    endwhile;    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder=" <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title=" <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisoModulos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php
                                                        // Consulto los modulos disponibles del perfil
                                                        $sql_modulo_perfil = $dbo->query("select * from ModuloPerfil where IDPerfil = '" . $frm["IDPerfil"] . "'");
                                                        while ($r_modulo_perfil = $dbo->object($sql_modulo_perfil)) {
                                                            $modulo_perfil[] = $r_modulo_perfil->IDModulo;
                                                        }
                                                        $arrayop = array();
                                                        // consulto los modulos
                                                        $query_modulos = $dbo->query("SELECT * FROM Modulo WHERE MostrarEnPerfiles = 'S' ORDER BY OrdenPerfiles ASC");
                                                        while ($r = $dbo->object($query_modulos)) {
                                                            $arraymodulos[$r->Nombre] = $r->IDModulo;
                                                        }
                                                        echo SIMHTML::formCheckGroup($arraymodulos, $modulo_perfil, "ModuloPerfil[]"); ?> </div>
                            </div>
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i><?= SIMUtil::get_traduccion('', '', 'PermisosmodulosdeNoticias', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisoModulos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php


                                                        $query_modulos = $dbo->query("SELECT * FROM Modulo WHERE Tipo = 'Noticias' ORDER BY IDModulo ASC");
                                                        while ($r = $dbo->object($query_modulos)) {

                                                            $SQLActivo = "SELECT * FROM ClubModulo WHERE IDModulo = $r->IDModulo AND IDClub = '" . SIMUser::get("club") . "'";
                                                            $QRYActvio = $dbo->query($SQLActivo);
                                                            $DatosActivo = $dbo->fetchArray($QRYActvio);

                                                            if ($DatosActivo[Activo] == 'S') :
                                                                if (!empty($DatosActivo[TituloLateral])) :
                                                                    $r->Nombre = $DatosActivo[TituloLateral];
                                                                endif;
                                                                $arraymodulosNoticias[$r->Nombre] = $r->IDModulo;
                                                            endif;
                                                        }
                                                        echo SIMHTML::formCheckGroup($arraymodulosNoticias, $modulo_perfil, "ModuloPerfil[]"); ?> </div>
                            </div>
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i><?= SIMUtil::get_traduccion('', '', 'PermisosmodulosdeEncuentas', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisoModulos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php


                                                        $query_modulos = $dbo->query("SELECT * FROM Modulo WHERE Tipo = 'Encuesta' ORDER BY IDModulo ASC");
                                                        while ($r = $dbo->object($query_modulos)) {

                                                            $SQLActivo = "SELECT * FROM ClubModulo WHERE IDModulo = $r->IDModulo AND IDClub = '" . SIMUser::get("club") . "'";
                                                            $QRYActvio = $dbo->query($SQLActivo);
                                                            $DatosActivo = $dbo->fetchArray($QRYActvio);

                                                            if ($DatosActivo[Activo] == 'S') :
                                                                if (!empty($DatosActivo[TituloLateral])) :
                                                                    $r->Nombre = $DatosActivo[TituloLateral];
                                                                endif;
                                                                $arraymodulosEncuestas[$r->Nombre] = $r->IDModulo;
                                                            endif;
                                                        }
                                                        echo SIMHTML::formCheckGroup($arraymodulosEncuestas, $modulo_perfil, "ModuloPerfil[]"); ?> </div>
                            </div>
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i><?= SIMUtil::get_traduccion('', '', 'PermisosmodulosdeDocumento', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermisoModulos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php


                                                        $query_modulos = $dbo->query("SELECT * FROM Modulo WHERE Tipo = 'Documentos' ORDER BY IDModulo ASC");
                                                        while ($r = $dbo->object($query_modulos)) {

                                                            $SQLActivo = "SELECT * FROM ClubModulo WHERE IDModulo = $r->IDModulo AND IDClub = '" . SIMUser::get("club") . "'";
                                                            $QRYActvio = $dbo->query($SQLActivo);
                                                            $DatosActivo = $dbo->fetchArray($QRYActvio);

                                                            if ($DatosActivo[Activo] == 'S') :
                                                                if (!empty($DatosActivo[TituloLateral])) :
                                                                    $r->Nombre = $DatosActivo[TituloLateral];
                                                                endif;
                                                                $arraymodulosDocumentos[$r->Nombre] = $r->IDModulo;
                                                            endif;
                                                        }
                                                        echo SIMHTML::formCheckGroup($arraymodulosDocumentos, $modulo_perfil, "ModuloPerfil[]"); ?> </div>
                            </div>
                        </div>
                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i><?= SIMUtil::get_traduccion('', '', 'PermisosmodulosdeAuxilios', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisoModulos', LANGSESSION); ?> </label>
                                <div class="col-sm-8"> <?php


                                                        $query_modulos = $dbo->query("SELECT * FROM Modulo WHERE Tipo = 'Auxilios' ORDER BY IDModulo ASC");
                                                        while ($r = $dbo->object($query_modulos)) {

                                                            $SQLActivo = "SELECT * FROM ClubModulo WHERE IDModulo = $r->IDModulo AND IDClub = '" . SIMUser::get("club") . "'";
                                                            $QRYActvio = $dbo->query($SQLActivo);
                                                            $DatosActivo = $dbo->fetchArray($QRYActvio);

                                                            if ($DatosActivo[Activo] == 'S') :
                                                                if (!empty($DatosActivo[TituloLateral])) :
                                                                    $r->Nombre = $DatosActivo[TituloLateral];
                                                                endif;
                                                                $arraymodulosAuxilios[$r->Nombre] = $r->IDModulo;
                                                            endif;
                                                        }
                                                        echo SIMHTML::formCheckGroup($arraymodulosAuxilios, $modulo_perfil, "ModuloPerfil[]"); ?> </div>
                            </div>
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i> <?= SIMUtil::get_traduccion('', '', 'Lossiguientespermisossonengeneralparalosmodulosqueseactiven', LANGSESSION); ?>
                            </h3>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisosdeCrearRegistros', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoCrear"], 'PermisoCrear', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisosdeBorrarRegistros', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoBorrar"], 'PermisoBorrar', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisosdeModificarRegistros', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoModificar"], 'PermisoModificar', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermisosdeExportarRegistros', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoExportar"], 'PermisoExportar', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-info-circle green"></i><?= SIMUtil::get_traduccion('', '', 'Permisosparalasreservas', LANGSESSION); ?>
                            </h3>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permisosdeeliminarreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoEliminarReserva"], 'PermisoEliminarReserva', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióndereservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoConfiguracion"], 'PermisoConfiguracion', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióngeneralenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoGeneral"], 'PermisoGeneral', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióndedisponibilidadenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoDisponibilidad"], 'PermisoDisponibilidad', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióndeauxiliaresenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoAuxiliares"], 'PermisoAuxiliares', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióndeelementosenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoElementos"], 'PermisoElementos', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermitirconfiguracióndeTipoReservaenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoElementos"], 'PermisoTipoReserva', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirconfiguracióndepreguntasreservasenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoPreguntas"], 'PermisoPreguntas', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permitirfechasdecierreenreservas', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermisoFechasCierre"], 'PermisoFechasCierre', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PuedeverelmodulodeagendaenelApp', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PuedeVerAgenda"], 'PuedeVerAgenda', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
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
<?
include("cmp/footer_scripts.php");
?>