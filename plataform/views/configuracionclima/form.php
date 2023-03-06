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

                            <div class="form-group first ">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Token', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Token" name="Token" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Token', LANGSESSION); ?>" value="<?php echo $frm["Token"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Estación', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Estacion" name="Estacion" placeholder="" class="form-control " title="<?= SIMUtil::get_traduccion('', '', 'Estacion', LANGSESSION); ?>" value="<?php echo $frm["Estacion"] ?>" required></div>

                                </div>

                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="ColorActivo"> <?= SIMUtil::get_traduccion('', '', 'ColorHeaderFondoClaro', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <input name="ColorHeaderFondoClaro" type="color" value="<?php if (empty($frm["ColorHeaderFondoClaro"])) {
                                                                                                    echo "#FFFFFF";
                                                                                                } else {
                                                                                                    echo $frm["ColorHeaderFondoClaro"];
                                                                                                }    ?>" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="ColorActivo"> <?= SIMUtil::get_traduccion('', '', 'ColorHeaderFondoOscuro', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <input name="ColorHeaderFondoOscuro" type="color" value="<?php if (empty($frm["ColorHeaderFondoOscuro"])) {
                                                                                                        echo "#FFFFFF";
                                                                                                    } else {
                                                                                                        echo $frm["ColorHeaderFondoOscuro"];
                                                                                                    }    ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> Viento</label>
                                    <input name="IconoViento" id=file class="" title="IconoViento" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoViento"])) {
                                            echo "<img src='" . CLIMA_ROOT . $frm["IconoViento"] . "' width='300px' height='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoViento]&campo=IconoViento&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> Truenos</label>
                                    <input name="IconoTruenos" id=file class="" title="IconoTruenos" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoTruenos"])) {
                                            echo "<img src='" . CLIMA_ROOT . $frm["IconoTruenos"] . "' width='300px' height='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoTruenos]&campo=IconoTruenos&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>






                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="AplicaPara" id="AplicaPara" value="S" />
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