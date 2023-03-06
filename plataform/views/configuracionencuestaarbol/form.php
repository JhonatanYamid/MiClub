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
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Correo notificación </label>
                                <div class="col-sm-8"><input type="text" id="EmailNotificacion" name="EmailNotificacion" placeholder="Email Notificacion" class="col-xs-12" title="Email Notificacion" value="<?php echo $frm["EmailNotificacion"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="MostrarBotonHistorial"> Mostrar Boton Historial? </label>
                                <div class="col-sm-8"> <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonHistorial"], 'MostrarBotonHistorial', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="TextoBotonHistorial"> Texto Boton Historial </label>
                                <div class="col-sm-8"><input type="text" id="TextoBotonHistorial" name="TextoBotonHistorial" placeholder="TextoBotonHistorial" class="col-xs-12 mandatory" title="TextoBotonHistorial" value="<?php echo $frm["TextoBotonHistorial"]; ?>"></div>
                            </div>
                        </div>
                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon"></i> Par&aacute;metros encuesta AJE
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="TextoBotonHistorial"> Objetivo encuesta </label>
                                <div class="col-sm-8"><input type="text" id="ObjetivoEncuesta" name="ObjetivoEncuesta" placeholder="Objetivo encuesta" class="col-xs-12 mandatory" title="Objetivo encuesta" value="<?php echo $frm["ObjetivoEncuesta"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="TextoBotonHistorial"> Cumplimiento ideal </label>
                                <div class="col-sm-8"><input type="text" id="CumplimientoIdeal" name="CumplimientoIdeal" placeholder="Cumplimiento ideal" class="col-xs-12 mandatory" title="CumplimientoIdeal" value="<?php echo $frm["CumplimientoIdeal"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
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