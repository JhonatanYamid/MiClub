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
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'AplicaParaCualModuloDeEventos', LANGSESSION); ?>:</label>

                                    <div class="col-sm-8">
                                        <select name="VersionEvento" id="VersionEvento">
                                            <option value="1" <?php if ($frm["VersionEvento"] == 1) echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?> 1</option>
                                            <option value="2" <?php if ($frm["VersionEvento"] == 2) echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?> 2</option>


                                        </select>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TipoFechaEncabezado', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <select name="TipoFechaHeader" id="TipoFechaHeader">
                                            <option value="Hoy" <?php if ($frm["TipoFechaHeader"] == "Hoy") echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Hoy', LANGSESSION); ?></option>
                                            <option value="Evento" <?php if ($frm["TipoFechaHeader"] == "Evento") echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?></option>


                                        </select>
                                    </div>

                                </div>



                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMostrarCalendarioColores', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarCalendarioColores"], 'PermiteMostrarCalendarioColores', "class='input mandatory'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteBuscadorPorNombre', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBuscadorNombre"], 'PermiteBuscadorNombre', "class='input mandatory'") ?>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteMostrarLugarDelEvento', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarLugarFormularioEvento"], 'PermiteMostrarLugarFormularioEvento', "class='input mandatory'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteMostrarNombreDelEvento', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarNombreFormularioEvento"], 'PermiteMostrarNombreFormularioEvento', "class='input mandatory'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteBotonContactoEventos', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBotonContactoEventos"], 'PermiteBotonContactoEventos', "class='input mandatory'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoBotonEventos(Contactenos)', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <input type="text" id="LabelBotonEventos" name="LabelBotonEventos" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBotonEventos', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'TextoBotonEventos', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonEventos"]; ?>">
                                    </div>
                                </div>

                            </div>


                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoInscribirBotonEventos', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <input type="text" id="LabelInscribirBotonEventos" name="LabelInscribirBotonEventos" placeholder=" <?= SIMUtil::get_traduccion('', '', 'TextoInscribirBotonEventos', LANGSESSION); ?>" class="col-xs-12" title=" <?= SIMUtil::get_traduccion('', '', 'TextoInscribirBotonEventos', LANGSESSION); ?>" value="<?php echo $frm["LabelInscribirBotonEventos"]; ?>">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ColorCalendario', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <input name="ColorCalendarioSeleccionado" type="color" value="<?php if (empty($frm["ColorCalendarioSeleccionado"])) {
                                                                                                            echo "#FFFFFF";
                                                                                                        } else {
                                                                                                            echo $frm["ColorCalendarioSeleccionado"];
                                                                                                        }    ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteRangoFechasEventos', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRangoFechasEventos"], 'PermiteRangoFechasEventos', "class='input mandatory'") ?>
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