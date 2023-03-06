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


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm" name="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                        <div class="form-group first ">



                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Imagen', LANGSESSION); ?>
                                </label>
                                <div class="col-sm-8">
                                    <?php
                                    if ($frm["RestauranteFile"]) {
                                    ?>
                                        <img alt="<?php echo $frm["RestauranteFile"] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["RestauranteFile"] ?>" width="100px">
                                        <a href="<? echo $script . ".php?action=DelImgNot&cam=$frm[RestauranteFile]&campo=RestauranteFile&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="file" name="RestauranteImagen" id="RestauranteImagen" class="popup" title="<?= SIMUtil::get_traduccion('', '', 'Imagen', LANGSESSION); ?>">
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $frm["Orden"]; ?>">
                                </div>
                            </div>

                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Ipimpresora" name="Ipimpresora" placeholder="<?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>" value="<?php echo $frm["Ipimpresora"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="PuertoImpresora" name="PuertoImpresora" placeholder=" <?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?>" class="col-xs-12 " title=" <?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?>" value="<?php echo $frm["PuertoImpresora"]; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Restaurantepara', LANGSESSION); ?>
                                    para: </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>
                                </div>
                            </div>
                        </div>





                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                </button>


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