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



                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Botón Crear Registro</label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonCrearRegistro" name="LabelBotonCrearRegistro" placeholder="" class="form-control" title="Texto Boton Crear Registro" value="<?php echo $frm["LabelBotonCrearRegistro"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Titulo Categorías</label>
                                    <div class="col-sm-8"><input type="text" id="LabelTituloCategorias" name="LabelTituloCategorias" placeholder="" class="form-control" title="Texto Titulo Categorias" value="<?php echo $frm["LabelTituloCategorias"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Botón Confirmar Registro</label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonConfirmarRegistro" name="LabelBotonConfirmarRegistro" placeholder="" class="form-control" title="Texto Boton Confirmar Registro" value="<?php echo $frm["LabelBotonConfirmarRegistro"] ?>" required></div>

                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Activo"], "Activo", "title=\"Activo\"") ?>
                                    </div>
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