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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cumpleañospara', LANGSESSION); ?> : </label>
                                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">

                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoBotonCumpleAños', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonCumpleAnos" name="LabelBotonCumpleAnos" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBotonCumpleAños', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBotonCumpleAños', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonCumpleAnos"]; ?>"></div>
                            </div>
                        </div>
                        <?php
                        $IDClub = SIMUser::get("club");
                        if ($IDClub == 8 || $IDClub == 15) { ?>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?><?= SIMUtil::get_traduccion('', '', 'MenoresDe12años', LANGSESSION); ?> </label>
                                    <input name="ImagenCumpleanosMenores" id="ImagenCumpleanosMenores" class="" title="<?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["ImagenCumpleanosMenores"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["ImagenCumpleanosMenores"] . "' height='300px' width='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenCumpleanosMenores]&campo=ImagenCumpleanosMenores&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', 'MayoresDe12años', LANGSESSION); ?> </label>
                                    <input name="ImagenCumpleanosMayores" id="ImagenCumpleanosMayores" class="" title="<?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["ImagenCumpleanosMayores"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["ImagenCumpleanosMayores"] . "' height='300px' width='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenCumpleanosMayores]&campo=ImagenCumpleanosMayores&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?> </label>
                                <input name="ImagenCumpleanos" id="ImagenCumpleanos" class="" title="<?= SIMUtil::get_traduccion('', '', 'ImagenCumpleaños', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["ImagenCumpleanos"])) {
                                        echo "<img src='" . BANNERAPP_ROOT . $frm["ImagenCumpleanos"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenCumpleanos]&campo=ImagenCumpleanos&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>



                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificacionapersonaquecumpleaños', LANGSESSION); ?>? </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPush"], 'EnviarPush', "class='input mandatory'") ?>
                                </div>


                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajenotificacionapersonaquecumpleaños', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <textarea id="MensajePush" name="MensajePush" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Mensajenotificacionapersonaquecumpleaños', LANGSESSION); ?>"><?php echo $frm["MensajePush"]; ?></textarea>
                                    </div>
                                </div>

                                &nbsp;

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EnviarNotificacionatodaslaspersonasquecumpleaños', LANGSESSION); ?>? </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPushTodos"], 'EnviarPushTodos', "class='input mandatory'") ?>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajenotificacionparatodaslaspersonasquecumpleaños', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <textarea id="MensajePushTodos" name="MensajePushTodos" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Mensajenotificacionparatodaslaspersonasquecumpleaños', LANGSESSION); ?>"><?php echo $frm["MensajePushTodos"]; ?></textarea>
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