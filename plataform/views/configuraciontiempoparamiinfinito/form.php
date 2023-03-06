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

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonResponderAuxilios"> Label Boton Responder Auxilios </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonResponderAuxilios" name="LabelBotonResponderAuxilios" placeholder="LabelBotonResponderAuxilios" class="col-xs-12 mandatory" title="LabelBotonResponderAuxilios" value="<?php echo $frm["LabelBotonResponderAuxilios"]; ?>"></div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelHeaderSeleccion"> Label Header Seleccion</label>
                                <div class="col-sm-8"><input type="text" id="LabelHeaderSeleccion" name="LabelHeaderSeleccion" placeholder="LabelHeaderSeleccion" class="col-xs-12 mandatory" title="LabelHeaderSeleccion" value="<?php echo $frm["LabelHeaderSeleccion"]; ?>"></div>
                            </div>



                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Mostrar Estado Auxilio </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarEstadoAuxilio"], 'PermiteMostrarEstadoAuxilio', "class='input mandatory'") ?>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Mostrar Mis Auxilios</label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarMisAuxilios"], 'PermiteMostrarMisAuxilios', "class='input mandatory'") ?>
                            </div>



                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="IconoEstadoAuxilioEntregado"> Icono Estado Auxilio Entregado </label>
                                <input name="IconoEstadoAuxilioEntregado" id=file class="" title="IconoEstadoAuxilioEntregado" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoEstadoAuxilioEntregado"])) {
                                        echo "<img src='" . BANNERAPP_ROOT . $frm["IconoEstadoAuxilioEntregado"] . "' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoEstadoAuxilioEntregado]&campo=IconoEstadoAuxilioEntregado&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelTextoAprobacion"> Label Texto Aprobacion </label>
                                <div class="col-sm-8">
                                    <textarea id="LabelTextoAprobacion" name="LabelTextoAprobacion" cols="10" rows="5" class="col-xs-12" title="LabelTextoAprobacion"><?php echo $frm["LabelTextoAprobacion"]; ?></textarea>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelMotivoRechazo"> Label MotivoRechazo </label>
                                <div class="col-sm-8"><input type="text" id="LabelMotivoRechazo" name="LabelMotivoRechazo" placeholder="LabelMotivoRechazo" class="col-xs-12 mandatory" title="LabelMotivoRechazo" value="<?php echo $frm["LabelMotivoRechazo"]; ?>"></div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonMisSolicitudes">Label Boton Mis Solicitudes </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonMisSolicitudes" name="LabelBotonMisSolicitudes" placeholder="LabelBotonMisSolicitudes" class="col-xs-12 mandatory" title="LabelBotonMisSolicitudes" value="<?php echo $frm["LabelBotonMisSolicitudes"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelConfirmacionEnvioSolicitud"> Label Confirmacion Envio Solicitud </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionEnvioSolicitud" name="LabelConfirmacionEnvioSolicitud" placeholder="LabelConfirmacionEnvioSolicitud" class="col-xs-12 mandatory" title="LabelConfirmacionEnvioSolicitud" value="<?php echo $frm["LabelConfirmacionEnvioSolicitud"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelConfirmacionRespuestaSolicitud"> Label Confirmacion Respuesta Solicitud </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionRespuestaSolicitud" name="LabelConfirmacionRespuestaSolicitud" placeholder="LabelConfirmacionRespuestaSolicitud" class="col-xs-12 mandatory" title="LabelConfirmacionRespuestaSolicitud" value="<?php echo $frm["LabelConfirmacionRespuestaSolicitud"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoTipoRechazoActivo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoTipoRechazoActivo"], 'CampoTipoRechazoActivo', "class='input mandatory'") ?>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoComentarioObligatorio', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoComentarioObligatorio"], 'CampoComentarioObligatorio', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="CorreoNotificacionSolicitudAuxilio"> <?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionSolicitudAuxilio', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="CorreoNotificacionSolicitudAuxilio" name="CorreoNotificacionSolicitudAuxilio" placeholder="<?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionSolicitudAuxilio', LANGSESSION); ?>" class="col-xs-12 " title="CorreoNotificacionSolicitudAuxilio" value="<?php echo $frm["CorreoNotificacionSolicitudAuxilio"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
                            </div>
                        </div>


                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo $_GET["IDModulo"] ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button>
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