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
                                <label class="col-sm-4 control-label no-padding-right" for="TiempoMinimo">Tiempo minimo de solicitud (minutos): </label>
                                <div class="col-sm-8"><input type="number" id="TiempoMinimo" name="TiempoMinimo"  onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="Tiempo Minimo de Solicitud" class="col-xs-12" title="Tiempo Minimo de Solicitud" value="<?php// echo $frm["TiempoMinimo"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonSolicitarTalega">Texto del boton de solicitud: </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonSolicitarTalega" name="LabelBotonSolicitarTalega" placeholder="Solicitar Talega" class="col-xs-12 mandatory" title="Texto del boton de solicitud" value="<?php echo $frm["LabelBotonSolicitarTalega"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelFechaSolicitarTalega">Texto del boton de fecha de solicitud: </label>
                                <div class="col-sm-8"><input type="text" id="LabelFechaSolicitarTalega" name="LabelFechaSolicitarTalega" placeholder="Fecha de Solicitud" class="col-xs-12 mandatory" title="Texto del boton de fecha de solicitud" value="<?php echo $frm["LabelFechaSolicitarTalega"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelLugarSolicitarTalega">Texto del boton de lugar de solicitud: </label>
                                <div class="col-sm-8"><input type="text" id="LabelLugarSolicitarTalega" name="LabelLugarSolicitarTalega" placeholder="Lugar de Solicitud" class="col-xs-12 mandatory" title="Texto del boton del lugar de solicitud" value="<?php echo $frm["LabelLugarSolicitarTalega"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonSolicitarTalega">Texto del boton de historial de talegas: </label>
                                <div class="col-sm-8"><input type="text" id="LabelVerMiHistorialTalega" name="LabelVerMiHistorialTalega" placeholder="Ver Historial" class="col-xs-12 mandatory" title="Texto del boton de historial de talegas" value="<?php echo $frm["LabelVerMiHistorialTalega"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelFechaSolicitarTalega">Texto del boton para refrescar pagina: </label>
                                <div class="col-sm-8"><input type="text" id="LabelRefrescarTalega" name="LabelRefrescarTalega" placeholder="Refrescar Talega" class="col-xs-12 mandatory" title="Texto del boton para refrescar pagina" value="<?php echo $frm["LabelRefrescarTalega"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite cancelar talega?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCancelarTalega"], 'PermiteCancelarTalega', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6 cancelar">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelBotonCancelarTalega">Texto del boton de cancelar solicitud: </label>
                                <div class="col-sm-8"><input type="text" id="LabelBotonCancelarTalega" name="LabelBotonCancelarTalega" placeholder="Cancelar Solicitud" class="col-xs-12 " title="Texto del boton de cancelar solicitud" value="<?php echo $frm["LabelBotonCancelarTalega"]; ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite ver beneficiarios?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSolicitarMisBeneficiarios"], 'PermiteSolicitarMisBeneficiarios', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6 cancelar">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelDescripcionSeleccionBeneficiarios">Texto previo al boton de cambio de usuario: </label>
                                <div class="col-sm-8"><input type="text" id="LabelDescripcionSeleccionBeneficiarios" name="LabelDescripcionSeleccionBeneficiarios" placeholder="Cambiar de usuario en el siguiente filtro" class="col-xs-12 " title="Texto para el cambio de usuario" value="<?php echo $frm["LabelDescripcionSeleccionBeneficiarios"]; ?>"></div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite solicitar inventario?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSolicitarInventario"], 'PermiteSolicitarInventario', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6 cancelar">
                                <label class="col-sm-4 control-label no-padding-right" for="LabelVerSolicitudInventario">Texto del boton de inventario: </label>
                                <div class="col-sm-8"><input type="text" id="LabelVerSolicitudInventario" name="LabelVerSolicitudInventario" placeholder="Ver Inventario" class="col-xs-12 " title="Texto del boton de inventario" value="<?php echo $frm["LabelVerSolicitudInventario"]; ?>"></div>
                            </div>
                        </div>
                        
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite ver inventario solicitado?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteVerInventarioSolicitado"], 'PermiteVerInventarioSolicitado', "class='input mandatory'") ?>
                            </div>
                            
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite mostrar lugar?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarLugar"], 'PermiteMostrarLugar', "class='input mandatory'") ?>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Permite mostrar fecha?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarFecha"], 'PermiteMostrarFecha', "class='input mandatory'") ?>
                            </div>
                            
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">¿Obligatorio mostrar lugar?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioMostrarLugar"], 'ObligatorioMostrarLugar', "class='input mandatory'") ?>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Obligatorio mostrar fecha?: </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioMostrarFecha"], 'ObligatorioMostrarFecha', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
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