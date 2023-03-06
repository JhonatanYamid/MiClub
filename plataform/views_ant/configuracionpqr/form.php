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
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Nombre">Nombre </label>
                                    <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="form-control" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto intro pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="TextoIntroPqr" name="TextoIntroPqr" placeholder="Texto intro Pqr" class="form-control" title="Texto intro Pqr" value="<?php echo $frm["TextoIntroPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto mis pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="TituloMisPqr" name="TituloMisPqr" placeholder="Titulo mis Pqr" class="form-control" title="Texto mis pqr" value="<?php echo $frm["TituloMisPqr"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto tipo de pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelTipoPqr" name="LabelTipoPqr" placeholder="Texto tipo de pqr" class="form-control" title="Texto tipo de pqr" value="<?php echo $frm["LabelTipoPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto titulo de pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelTituloPqr" name="LabelTituloPqr" placeholder="Texto titulo de pqr" class="form-control" title="Texto titulo de pqr" value="<?php echo $frm["LabelTituloPqr"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto comentario de pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelComentarioPqr" name="LabelComentarioPqr" placeholder="Texto comentario de pqr" class="form-control" title="Texto comentario de pqr" value="<?php echo $frm["LabelComentarioPqr"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">                              
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir agregar servicio de la PQR </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSeleccionarServicios"], 'PermiteSeleccionarServicios', "class='input'") ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio selecionar servicio de la PQR </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioSeleccionarServicios"], 'ObligatorioSeleccionarServicios', "class='input'") ?>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto campo seleccionar servicio</label>
                                    <div class="col-sm-8"> 
                                        <input type="text" id="LabelServiciosPqr" name="LabelServiciosPqr" placeholder="Label Servicios Pqr" class="form-control" title="Label Servicios Pqr value="<?php echo $frm["LabelServiciosPqr"] ?>" required>
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoIntroPqr">Texto area de pqr</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelAreaPqr" name="LabelAreaPqr" placeholder="Texto area de pqr" class="form-control" title="Texto area de pqr" value="<?php echo $frm["LabelAreaPqr"] ?>" required></div>
                                </div>
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
                                            <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> </button>
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