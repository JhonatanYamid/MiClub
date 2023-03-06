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

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Textoalcrearclasificado', LANGSESSION); ?></label>
                                <div class="col-sm-8"><input type="text" id="TextoClasificado" name="TextoClasificado" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textoalcrearclasificado', LANGSESSION); ?>" value="<?php echo $frm["TextoClasificado"] ?>"></div>

                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PublicarClasificadosAutomaticamente', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PublicarClasificadosAutomaticamente"], 'PublicarClasificadosAutomaticamente', "class='input mandatory'") ?></div>
                            </div>



                        </div>

                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMostrarPalabraMeta', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarPalabraMeta"], 'PermiteMostrarPalabraMeta', "class='input mandatory'") ?></div>
                                <div class="col-sm-8">
                                    <img src="/img/clasificadosmiclub.png" alt="" width="300" height="300">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoPalabraMeta', LANGSESSION); ?></label>
                                <div class="col-sm-8"><input type="text" id="TextoPalabraMeta" name="TextoPalabraMeta" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TextoPalabraMeta', LANGSESSION); ?>" value="<?php echo $frm["TextoPalabraMeta"] ?>"></div>

                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMostrarFechaInicioYFechaFin', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarFechas"], 'PermiteMostrarFechas', "class='input mandatory'") ?></div>
                            </div>




                        </div>

                        <div class="form-group first ">

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoValor(Metaderecaudoennúmeros)', LANGSESSION); ?></label>
                                <div class="col-sm-8"><input type="text" id="LabelValor" name="LabelValor" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TextoValor(Metaderecaudoennúmeros)', LANGSESSION); ?>" value="<?php echo $frm["LabelValor"] ?>"></div>

                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteAgregarMasFotos', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAgregarMasFotos"], 'PermiteAgregarMasFotos', "class='input mandatory'") ?></div>
                            </div>


                        </div>

                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?></div>
                            </div>

                        </div>



                        <div class="form-group first ">
                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="ConfiguracionPara" id="ConfiguracionPara" value="S" />
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