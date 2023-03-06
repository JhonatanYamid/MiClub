<div class="widget-box transparent" id="recent-box">
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre configuración </label>
                                <div class="col-sm-8">
                                    <input id=Nombre type=text size=25 name=Nombre class="input mandatory title=" Nombre configuración" value="<?= $frm["Nombre"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto ver mis permisos </label>
                                <div class="col-sm-8">
                                    <input id=LabelMisPermisos type=text size=25 name=LabelMisPermisos class="input mandatory" title="Texto ver mis permisos" value="<?= $frm["LabelMisPermisos"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto aprobar vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="AprobarVacacionesNombre" type=text size="25" name="AprobarVacacionesNombre" class="input mandatory" title="AprobarVacacionesNombre" value="<?= $frm["AprobarVacacionesNombre"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono aprobar vacaciones </label>
                                <input name="AprobarVacacionesIcono" id="AprobarVacacionesIcono" class="" title="AprobarVacacionesIcono" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["AprobarVacacionesIcono"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["AprobarVacacionesIcono"] . "' >";
                                    ?>
                                        <a href="<? echo $script . " .php?action=delfoto&foto=$frm[AprobarVacacionesIcono]&campo=AprobarVacacionesIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden aprobar vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="AprobarVacacionesOrden" type="number" size="25" name="AprobarVacacionesOrden" class="input mandatory" title="AprobarVacacionesOrden" value="<?= $frm["AprobarVacacionesOrden"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto filtro aprobar vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="BotonFiltroAprobarVacacionesTexto" type=text size="25" name="BotonFiltroAprobarVacacionesTexto" class="input mandatory" title="BotonFiltroAprobarVacacionesTexto" value="<?= $frm["BotonFiltroAprobarVacacionesTexto"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto aprobar solicitudes </label>
                                <div class="col-sm-8">
                                    <input id="LabelAprobarSolicitudes" type=text size="25" name="LabelAprobarSolicitudes" class="input mandatory" title="LabelAprobarSolicitudes" value="<?= $frm["LabelAprobarSolicitudes"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Minimo días de vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="VacacionesDiasMinimo" type=text size="25" name="VacacionesDiasMinimo" class="input mandatory" title="Días minimo de Vacaciones" value="<?= $frm["VacacionesDiasMinimo"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Días de anticipaci&oacute;n para solicitar vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="VacacionesDiasAnticipacion" type=text size="25" name="VacacionesDiasAnticipacion" class="input mandatory" title="Días de anticipacion para solicitar Vacaciones" value="<?= $frm["VacacionesDiasAnticipacion"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> M&aacute;ximo días para aprobar vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="VacacionesDiasAprobar" type=text size="25" name="VacacionesDiasAprobar" class="input mandatory" title="Máximo días para aprobar Vacaciones" value="<?= $frm["VacacionesDiasAprobar"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Estado Pendiente Compensatorio </label>
                                <input id="LabelEstadoPendienteCompensatorio" type=text size="25" name="LabelEstadoPendienteCompensatorio" class="input mandatory" title="Texto Estado Pendiente Compensatorio" value="<?= $frm["LabelEstadoPendienteCompensatorio"] ?>">
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Estado Pendiente Vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="LabelEstadoPendienteVacaciones" type=text size="25" name="LabelEstadoPendienteVacaciones" class="input mandatory" title="Texto Estado Pendiente Vacaciones" value="<?= $frm["LabelEstadoPendienteVacaciones"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Estado Pendiente Permisos </label>
                                <input id="LabelEstadoPendientePermisos" type=text size="25" name="LabelEstadoPendientePermisos" class="input mandatory" title="Texto Estado Pendiente Permisos" value="<?= $frm["LabelEstadoPendientePermisos"] ?>">
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Estado Pendiente Certificado </label>
                                <div class="col-sm-8">
                                    <input id="LabelEstadoPendienteCertificado" type=text size="25" name="LabelEstadoPendienteCertificado" class="input mandatory" title="Texto Estado Pendiente Certificado" value="<?= $frm["LabelEstadoPendienteCertificado"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir días a remunerar en dinero </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteVacacionesDiasDinero"], 'PermiteVacacionesDiasDinero', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto días a remunerar en dinero </label>
                                <div class="col-sm-8">
                                    <input id="LabelVacacionesDiasDinero" type=text size="25" name="LabelVacacionesDiasDinero" class="input mandatory" title="Label Vacaciones Dias Dinero" value="<?= $frm["LabelVacacionesDiasDinero"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto días a tomar de vacaciones </label>
                                <div class="col-sm-8">
                                    <input id="LabelVacacionesDiasNormales" type=text size="25" name="LabelVacacionesDiasNormales" class="input mandatory" title="LabelVacacionesDiasNormales" value="<?= $frm["LabelVacacionesDiasNormales"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> M&iacute;nimo días de vacaciones en dinero </label>
                                <div class="col-sm-8">
                                    <input id="DiasMinDinero" type=text size="25" name="DiasMinDinero" class="input mandatory" title="Mínimo días Vacaciones en dinero" value="<?= $frm["DiasMinDinero"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo notificación certificados </label>
                                <div class="col-sm-8">
                                    <input id="CorreoNotificacionCertificados" type=text size="25" name="CorreoNotificacionCertificados" class="input mandatory" title="Correo Notificaci&oacute;n Certificados" value="<?= $frm["CorreoNotificacionCertificados"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoTipoRechazoActivo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoTipoRechazoActivo"], 'CampoTipoRechazoActivo', "class='input mandatory'") ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoComentarioObligatorio', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoComentarioObligatorio"], 'CampoComentarioObligatorio', "class='input mandatory'") ?>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permiteadjuntararchivo', LANGSESSION); ?></label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteFotoArchivoCompensatorio"], 'PermiteFotoArchivoCompensatorio', "class='input mandatory'") ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textoadjuntararchivo', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input id="LabelFotoArchivoCompensatorio" type=text size="25" name="LabelFotoArchivoCompensatorio" class="input" title="<?= SIMUtil::get_traduccion('', '', 'Textoadjuntararchivo', LANGSESSION); ?>" value="<?= $frm["LabelFotoArchivoCompensatorio"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivoadjuntoobligatorio', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFotoArchivoCompensatorio"], 'ObligatorioFotoArchivoCompensatorio', "class='input mandatory'") ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Sabado dia Laboral </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SabadoDialaboral"], 'SabadoDialaboral', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Filtrar solicitudes por jefe? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["FiltrarPorJefe"], 'FiltrarPorJefe', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-12">
                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th>Activo</th>
                                        <th>Modulo</th>
                                        <th>Titulo Club</th>
                                        <th>Icono</th>
                                        <th>Orden</th>
                                    </tr>
                                    <tbody id="listacontactosanunciante">
                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                            <td aling="center">
                                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Vacaciones"], 'Vacaciones', "class='input mandatory'") ?>
                                            </td>
                                            <td> Vacaciones </td>
                                            <td>
                                                <input id="VacacionesNombre" type=text size=25 name="VacacionesNombre" class="col-xs-12" title="Titulo" value="<?= $frm["VacacionesNombre"] ?>" placeholder="Nombre Vacaciones">
                                            </td>
                                            <td>
                                                <?
                                                if (!empty($frm["VacacionesIcono"])) {
                                                    echo "<img src='" . CLUB_ROOT . $frm["VacacionesIcono"] . "' >";
                                                ?>
                                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[VacacionesIcono]&campo=VacacionesIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                } // END if
                                                ?>
                                                <input name="VacacionesIcono" id=VacacionesIcono class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                <input type="hidden" name="ImagenOriginalVacaciones" id="ImagenOriginalVacaciones" value="<?php echo $frm["VacacionesIcono"] ?>">
                                            </td>
                                            <td>
                                                <input id=VacacionesOrden type=text size=25 name=VacacionesOrden class="col-xs-12" title="Orden" value="<?= $frm["VacacionesOrden"] ?>">
                                            </td>
                                        </tr>
                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                            <td aling="center">
                                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Compensatorio"], 'Compensatorio', "class='input mandatory'") ?>
                                            </td>
                                            <td> Compensatorio </td>
                                            <td>
                                                <input id="CompensatorioNombre" type=text size=25 name="CompensatorioNombre" class="col-xs-12" title="Titulo" value="<?= $frm["CompensatorioNombre"] ?>" placeholder="Nombre Compensatorio">
                                            </td>
                                            <td>
                                                <?
                                                if (!empty($frm["CompensatorioIcono"])) {
                                                    echo "<img src='" . CLUB_ROOT . $frm["CompensatorioIcono"] . "' >";
                                                ?>
                                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[CompensatorioIcono]&campo=CompensatorioIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                } // END if
                                                ?>
                                                <input name="CompensatorioIcono" id=CompensatorioIcono class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                <input type="hidden" name="ImagenOriginalCompensatorio" id="ImagenOriginalCompensatorio" value="<?php echo $frm["CompensatorioIcono"] ?>">
                                            </td>
                                            <td>
                                                <input id=CompensatorioOrden type=text size=25 name=CompensatorioOrden class="col-xs-12" title="Orden" value="<?= $frm["CompensatorioOrden"] ?>">
                                            </td>
                                        </tr>
                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                            <td aling="center">
                                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Permisos"], 'Permisos', "class='input mandatory'") ?>
                                            </td>
                                            <td> Permisos </td>
                                            <td>
                                                <input id="PermisosNombre" type=text size=25 name="PermisosNombre" class="col-xs-12" title="Titulo" value="<?= $frm["PermisosNombre"] ?>" placeholder="Nombre Permisos">
                                            </td>
                                            <td>
                                                <?
                                                if (!empty($frm["PermisosIcono"])) {
                                                    echo "<img src='" . CLUB_ROOT . $frm["PermisosIcono"] . "' >";
                                                ?>
                                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[PermisosIcono]&campo=PermisosIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                } // END if
                                                ?>
                                                <input name="PermisosIcono" id=PermisosIcono class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                <input type="hidden" name="ImagenOriginalPermisos" id="ImagenOriginalPermisos" value="<?php echo $frm["PermisosIcono"] ?>">
                                            </td>
                                            <td>
                                                <input id=PermisosOrden type=text size=25 name=PermisosOrden class="col-xs-12" title="Orden" value="<?= $frm["PermisosOrden"] ?>">
                                            </td>
                                        </tr>
                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                            <td aling="center">
                                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Extracto"], 'Extracto', "class='input mandatory'") ?>
                                            </td>
                                            <td> Extracto </td>
                                            <td>
                                                <input id="ExtractoNombre" type=text size=25 name="ExtractoNombre" class="col-xs-12" title="Titulo" value="<?= $frm["ExtractoNombre"] ?>" placeholder="Nombre Extracto">
                                            </td>
                                            <td>
                                                <?
                                                if (!empty($frm["ExtractoIcono"])) {
                                                    echo "<img src='" . CLUB_ROOT . $frm["ExtractoIcono"] . "' >";
                                                ?>
                                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ExtractoIcono]&campo=ExtractoIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                } // END if
                                                ?>
                                                <input name="ExtractoIcono" id=ExtractoIcono class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                <input type="hidden" name="ImagenOriginalExtracto" id="ImagenOriginalExtracto" value="<?php echo $frm["ExtractoIcono"] ?>">
                                            </td>
                                            <td>
                                                <input id=ExtractoOrden type=text size=25 name=ExtractoOrden class="col-xs-12" title="Orden" value="<?= $frm["ExtractoOrden"] ?>">
                                            </td>
                                        </tr>
                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                            <td aling="center">
                                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Certificado"], 'Certificado', "class='input mandatory'") ?>
                                            </td>
                                            <td> Certificado </td>
                                            <td>
                                                <input id="CertificadoNombre" type=text size=25 name="CertificadoNombre" class="col-xs-12" title="Titulo" value="<?= $frm["CertificadoNombre"] ?>" placeholder="Nombre Certificado">
                                            </td>
                                            <td>
                                                <?
                                                if (!empty($frm["CertificadoIcono"])) {
                                                    echo "<img src='" . CLUB_ROOT . $frm["CertificadoIcono"] . "' >";
                                                ?>
                                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[CertificadoIcono]&campo=CertificadoIcono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                } // END if
                                                ?>
                                                <input name="CertificadoIcono" id=CertificadoIcono class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                <input type="hidden" name="ImagenOriginalCertificado" id="ImagenOriginalCertificado" value="<?php echo $frm["CertificadoIcono"] ?>">
                                            </td>
                                            <td>
                                                <input id=CertificadoOrden type=text size=25 name=CertificadoOrden class="col-xs-12" title="Orden" value="<?= $frm["CertificadoOrden"] ?>">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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