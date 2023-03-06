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
                                    <label class="col-sm-4 control-label" for="Nombre"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                    <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textointropqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="TextoIntroPqr" name="TextoIntroPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textointropqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textointropqr', LANGSESSION); ?>" value="<?php echo $frm["TextoIntroPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textomispqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="TituloMisPqr" name="TituloMisPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textomispqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textomispqr', LANGSESSION); ?>" value="<?php echo $frm["TituloMisPqr"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textotipodepqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="LabelTipoPqr" name="LabelTipoPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textotipodepqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textotipodepqr', LANGSESSION); ?>" value="<?php echo $frm["LabelTipoPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textotitulodepqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="LabelTituloPqr" name="LabelTituloPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textotitulodepqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textotitulodepqr', LANGSESSION); ?>" value="<?php echo $frm["LabelTituloPqr"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textocomentariodepqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="LabelComentarioPqr" name="LabelComentarioPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textocomentariodepqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textocomentariodepqr', LANGSESSION); ?>" value="<?php echo $frm["LabelComentarioPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitiragregarserviciodelaPQR', LANGSESSION); ?> </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSeleccionarServicios"], 'PermiteSeleccionarServicios', "class='input'") ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ObligatorioselecionarserviciodelaPQR', LANGSESSION); ?> </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioSeleccionarServicios"], 'ObligatorioSeleccionarServicios', "class='input'") ?>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textocamposeleccionarservicio', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" id="LabelServiciosPqr" name="LabelServiciosPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textocamposeleccionarservicio', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textocamposeleccionarservicio', LANGSESSION); ?>" value=" <?php echo $frm["LabelServiciosPqr"] ?>" required>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textoalguardarpqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" id="TextoGuardarPqr" name="TextoGuardarPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textoalguardarpqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textoalguardarpqr', LANGSESSION); ?>" value="<?php echo $frm["TextoGuardarPqr"] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteSeleccionarCategoria', LANGSESSION); ?> </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSeleccionarCategoria"], 'PermiteSeleccionarCategoria', "class='input mandatory'") ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelCategoriaPqr"><?= SIMUtil::get_traduccion('', '', 'TextoCategoriaPqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" id="LabelCategoriaPqr" name="LabelCategoriaPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoCategoriaPqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'LabelCategoriaPqr', LANGSESSION); ?>" value="<?php echo $frm["LabelCategoriaPqr"] ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr"><?= SIMUtil::get_traduccion('', '', 'Textoareadepqr', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="LabelAreaPqr" name="LabelAreaPqr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textoareadepqr', LANGSESSION); ?>" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textoareadepqr', LANGSESSION); ?>" value="<?php echo $frm["LabelAreaPqr"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto Intro Creacion Pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="TextoIntroCreacionPqr" name="TextoIntroCreacionPqr" placeholder="Texto Intro Creacion Pqr" class="form-control" title="Texto IntroCreacion Pqr" value="<?php echo $frm["TextoIntroCreacionPqr"] ?>"></div>
                                </div>

                            </div>

                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>
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
                                            <i class="ace-icon fa fa-check bigger-110"></i><?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?></button>
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