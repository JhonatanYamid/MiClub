<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-8">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Hora Inicio Desayuno</label>
                            <div class="col-sm-4"><input type="time" id="HoraInicioDesayuno" name="HoraInicioDesayuno"  class="form-control" title="Hora Inicio Desayuno" value="<?php echo $frm["HoraInicioDesayuno"] ?>" required></div>
                        </div>
                        <div class="form-group first ">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Hora Inicio Almuerzo</label>
                            <div class="col-sm-4"><input type="time" id="HoraInicioAlmuerzo" name="HoraInicioAlmuerzo"  class="form-control" title="Hora Inicio Almuerzo" value="<?php echo $frm["HoraInicioAlmuerzo"] ?>" required></div>
                        </div>
                        <div class="form-group first ">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Hora Inicio Cena</label>
                            <div class="col-sm-4"><input type="time" id="HoraInicioCena" name="HoraInicioCena"  class="form-control" title="Hora Inicio Cena" value="<?php echo $frm["HoraInicioCena"] ?>" required></div>
                        </div>

                </div>
                <div class="form-group first ">

                    <div class="col-xs-4 col-sm-4">

                        <br><br> <br><br>

                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>
                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
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