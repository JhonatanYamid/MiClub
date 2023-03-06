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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cumpleaños para: </label>
                                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">

                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Boton CumpleAños </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonCumpleAnos" name="LabelBotonCumpleAnos" placeholder="Label Boton CumpleAños" class="col-xs-12 mandatory" title="LabelBotonCumpleAnos" value="<?php echo $frm["LabelBotonCumpleAnos"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Cumpleaños </label>
                                <input name="ImagenCumpleanos" id="ImagenCumpleanos" class="" title="ImagenCumpleanos" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["ImagenCumpleanos"])) {
                                        echo "<img src='" . BANNERAPP_ROOT . $frm["ImagenCumpleanos"] . "' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenCumpleanos]&campo=ImagenCumpleanos&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificacion a  persona que cumpleaños?  </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPush"], 'EnviarPush', "class='input mandatory'") ?>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje notificacion a personas que cumpleaños </label>
                              
                                <div class="col-sm-8">
                                    <textarea id="MensajePush" name="MensajePush" cols="10" rows="5" class="col-xs-12" title="MensajePush"><?php echo $frm["MensajePush"]; ?></textarea>
                                </div>
                            </div>


                            &nbsp;
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Enviar Notificacion a todas las personas que cumpleaños?  </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPushTodos"], 'EnviarPushTodos', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje notificacion para todas las personas que cumpleaños </label>
                              
                                <div class="col-sm-8">
                                      <textarea id="MensajePushTodos" name="MensajePushTodos" cols="10" rows="5" class="col-xs-12" title="MensajePushTodos"><?php echo $frm["MensajePushTodos"]; ?></textarea>
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