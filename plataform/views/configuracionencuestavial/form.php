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
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> <?= SIMUtil::get_traduccion('', '', 'Correonotificación', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="EmailNotificacion" name="EmailNotificacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Correonotificación', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Correonotificación', LANGSESSION); ?>" value="<?php echo $frm["EmailNotificacion"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelConfirmacionEnvio"> <?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvio', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionEnvio" name="LabelConfirmacionEnvio" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvio', LANGSESSION); ?>" value="<?php echo $frm["LabelConfirmacionEnvio"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonRecargar"> <?= SIMUtil::get_traduccion('', '', 'TextoBotonRecargar', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonRecargar" name="LabelBotonRecargar" placeholder=" <?= SIMUtil::get_traduccion('', '', 'TextoBotonRecargar', LANGSESSION); ?>" class="col-xs-12 mandatory" title=" <?= SIMUtil::get_traduccion('', '', 'TextoBotonRecargar', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonRecargar"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="TextoConfirmacionEnvioCarro"> <?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvioCarro', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelConfirmacionEnvioCarro" name="LabelConfirmacionEnvioCarro" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvioCarro', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoConfirmacionEnvioCarro', LANGSESSION); ?>" value="<?php echo $frm["LabelConfirmacionEnvioCarro"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="TextoRegistrarCarro"> <?= SIMUtil::get_traduccion('', '', 'TextoRegistrarCarro', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelRegistrarCarro" name="LabelRegistrarCarro" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoRegistrarCarro', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LabelRegistrarCarro', LANGSESSION); ?>" value="<?php echo $frm["LabelRegistrarCarro"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelInactivarCarro"> <?= SIMUtil::get_traduccion('', '', 'TextoInactivarCarro', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelInactivarCarro" name="LabelInactivarCarro" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoInactivarCarro', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoInactivarCarro', LANGSESSION); ?>" value="<?php echo $frm["LabelInactivarCarro"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelActivarCarro"> <?= SIMUtil::get_traduccion('', '', 'TextoActivarCarro', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelActivarCarro" name="LabelActivarCarro" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoActivarCarro', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoActivarCarro', LANGSESSION); ?>" value="<?php echo $frm["LabelActivarCarro"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteRegistrarCarro', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRegistrarCarro"], 'PermiteRegistrarCarro', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteInactivarYActivarCarro', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteInactivarActivarCarro"], 'PermiteInactivarActivarCarro', "class='input mandatory'") ?>
                            </div>


                        </div>





                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarBotonHistorial', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonHistorial"], 'MostrarBotonHistorial', "class='input mandatory'") ?>
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