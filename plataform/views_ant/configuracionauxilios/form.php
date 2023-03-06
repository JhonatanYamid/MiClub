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
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonResponderAuxilios"> <?= SIMUtil::get_traduccion('', '', 'TextoBotonResponderAuxilios', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonResponderAuxilios" name="LabelBotonResponderAuxilios" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBotonResponderAuxilios', LANGSESSION); ?>" class="col-xs-12 mandatory" title="LabelBotonResponderAuxilios" value="<?php echo $frm["LabelBotonResponderAuxilios"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelTextoAprobacion"> <?= SIMUtil::get_traduccion('', '', 'TextoAprobacion', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <textarea id="LabelTextoAprobacion" name="LabelTextoAprobacion" cols="10" rows="5" class="col-xs-12" title="LabelTextoAprobacion"><?php echo $frm["LabelTextoAprobacion"]; ?></textarea>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelMotivoRechazo"> <?= SIMUtil::get_traduccion('', '', 'TextoMotivoRechazo', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelMotivoRechazo" name="LabelMotivoRechazo" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoMotivoRechazo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="LabelMotivoRechazo" value="<?php echo $frm["LabelMotivoRechazo"]; ?>"></div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonMisSolicitudes"><?= SIMUtil::get_traduccion('', '', 'TextoBotonMisSolicitudes', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonMisSolicitudes" name="LabelBotonMisSolicitudes" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBotonMisSolicitudes', LANGSESSION); ?>" class="col-xs-12 mandatory" title="LabelBotonMisSolicitudes" value="<?php echo $frm["LabelBotonMisSolicitudes"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelConfirmacionEnvioSolicitud"> <?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvioSolicitud', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionEnvioSolicitud" name="LabelConfirmacionEnvioSolicitud" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvioSolicitud', LANGSESSION); ?>" class="col-xs-12 mandatory" title="LabelConfirmacionEnvioSolicitud" value="<?php echo $frm["LabelConfirmacionEnvioSolicitud"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelConfirmacionRespuestaSolicitud"> <?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionRespuestaSolicitud', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionRespuestaSolicitud" name="LabelConfirmacionRespuestaSolicitud" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionRespuestaSolicitud', LANGSESSION); ?>" class="col-xs-12 mandatory" title="LabelConfirmacionRespuestaSolicitud" value="<?php echo $frm["LabelConfirmacionRespuestaSolicitud"]; ?>"></div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoTipoRechazoActivo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoTipoRechazoActivo"], 'CampoTipoRechazoActivo', "class='input mandatory'") ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CampoComentarioObligatorio', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoComentarioObligatorio"], 'CampoComentarioObligatorio', "class='input mandatory'") ?>
                            </div>



                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>
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
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
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