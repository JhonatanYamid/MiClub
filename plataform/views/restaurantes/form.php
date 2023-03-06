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


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Lugar', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Lugar" name="Lugar" placeholder="<?= SIMUtil::get_traduccion('', '', 'Lugar', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Lugar" value="<?php echo $frm["Lugar"]; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Menu', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="Menu" name="Menu" cols="10" rows="5" class="col-xs-12 mandatory" title="Menu"><?php echo $frm["Menu"]; ?></textarea>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horario', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="Horario" name="Horario" cols="10" rows="5" class="col-xs-12 mandatory" title="Horario"><?php echo $frm["Horario"]; ?></textarea>
                                </div>
                            </div>

                        </div>




                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Localizacion', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Localizacion" name="Localizacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Localizacion', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Localizacion" value="<?php echo $frm["Localizacion"]; ?>">

                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>

                                <div class="col-sm-8">

                                    <?php
                                    if ($frm["RestauranteIcono"]) {
                                    ?>
                                        <img alt="<?php echo $frm["RestauranteIcono"] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["RestauranteIcono"] ?>" width="100px">
                                        <a href="<? echo $script . ".php?action=DelImgNot&cam=RestauranteIcono&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="file" name="RestauranteIcono" id="RestauranteIcono" class="popup" title="Restaurante icono">
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Imagen', LANGSESSION); ?> </label>

                                <div class="col-sm-8">

                                    <?php
                                    if ($frm["RestauranteFile"]) {
                                    ?>
                                        <img alt="<?php echo $frm["RestauranteFile"] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["RestauranteFile"] ?>" width="100px">
                                        <a href="<? echo $script . ".php?action=DelImgNot&cam=RestauranteFile&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="file" name="RestauranteImagen" id="RestauranteImagen" class="popup" title="Restaurante Imagen">
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?> 1 (640px) </label>

                                <div class="col-sm-8">

                                    <?php
                                    if ($frm["CartaFile"]) {
                                    ?>
                                        <img alt="<?php echo $frm["CartaFile"] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["CartaFile"] ?>" width="100px">
                                        <a href="<? echo $script . ".php?action=DelImgNot&cam=CartaFile&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="file" name="CartaImagen" id="CartaImagen" class="popup" title="Carta Imagen">
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?>2 (640px) </label>

                                <div class="col-sm-8">

                                    <?php
                                    if ($frm["CartaFile2"]) {
                                    ?>
                                        <img alt="<?php echo $frm["CartaFile2"] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["CartaFile2"] ?>" width="100px">
                                        <a href="<? echo $script . ".php?action=DelImgNot&cam=CartaFile2&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="file" name="CartaImagen2" id="CartaImagen2" class="popup" title="Carta Imagen">
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>

                        </div>


                        <?php for ($i = 3; $i <= 18; $i++) { ?>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?> <?php echo $i; ?> (640px) </label>

                                    <div class="col-sm-8">

                                        <?php
                                        if ($frm["CartaFile" . $i]) {
                                        ?>
                                            <img alt="<?php echo $frm["CartaFile" . $i] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["CartaFile" . $i] ?>" width="100px">
                                            <a href="<? echo $script . ".php?action=DelImgNot&cam=CartaFile" . $i . "&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?php
                                        } else {
                                        ?>
                                            <input type="file" name="CartaImagen<?php echo $i; ?>" id="CartaImagen<?php echo $i; ?>" class="popup" title="Carta Imagen">
                                        <?php
                                        }
                                        ?>

                                    </div>
                                </div>

                                <?php $i += 1; ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?> <?php echo $i; ?> (640px) </label>

                                    <div class="col-sm-8">

                                        <?php
                                        if ($frm["CartaFile" . $i]) {
                                        ?>
                                            <img alt="<?php echo $frm["CartaFile" . $i] ?>" src="<?php echo IMGEVENTO_ROOT . $frm["CartaFile" . $i] ?>" width="100px">
                                            <a href="<? echo $script . ".php?action=DelImgNot&cam=CartaFile" . $i . "&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?php
                                        } else {
                                        ?>
                                            <input type="file" name="CartaImagen<?php echo $i; ?>" id="CartaImagen<?php echo $i; ?>" class="popup" title="Carta Imagen">
                                        <?php
                                        }
                                        ?>

                                    </div>
                                </div>

                            </div>


                        <?php } ?>







                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="Number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>">
                                </div>
                            </div>

                        </div>




                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get("title"); ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
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