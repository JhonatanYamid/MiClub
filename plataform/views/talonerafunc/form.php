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
                    <form class="form-horizontal formvalida" role="form" method="post" name="frm<?php echo $script; ?>" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">
                                <!--   <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dirigido A </label>

                                    <div class="col-sm-8">
                                        <?php /* echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") */ ?>

                                    </div>

                                </div> -->

                                <!--  <input type="hidden" name="DirigidoA" id="DirigidoA" value="S" /> -->


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="NombreTalonera">Nombre Talonera</label>
                                    <div class="col-sm-8"> <input type="text" id="NombreTalonera" name="NombreTalonera" placeholder="Nombre Talonera" class="form-control mandatory " title="NombreTalonera" value="<?php echo $frm["NombreTalonera"] ?>"></div>


                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescripcionTalonera">DescripcionTalonera</label>
                                    <div class="col-sm-8"><input type="text" id="DescripcionTalonera" name="DescripcionTalonera" placeholder="Descripcion Talonera" class="form-control mandatory" title="DescripcionTalonera" value="<?php echo $frm["DescripcionTalonera"] ?>"></div>


                                </div>

                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Cantidad Entradas </label>
                                    <div class="col-sm-8"> <input type="text" id="CantidadEntradas" name="CantidadEntradas" placeholder="Cantidad Entradas " class="form-control mandatory" title="CantidadEntradas" value="<?php echo $frm["CantidadEntradas"] ?>"></div>

                                </div>
                            </div>

                            
                            <div class="form-group first">
                               
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Activa </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activa"], 'Activa', "class='input'") ?>
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
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
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