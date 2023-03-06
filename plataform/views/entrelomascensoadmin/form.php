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
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="NombrePropietario">Nombre Propietario</label>
                                    <div class="col-sm-8"><input type="text" id="NombrePropietario" name="NombrePropietario" placeholder="" class="form-control" title="NombrePropietario" value="<?php echo $frm["NombrePropietario"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Casa">Casa</label>
                                    <div class="col-sm-8"> <input type="text" id="Casa" name="Casa" placeholder="" class="form-control" title="Casa" value="<?php echo $frm["Casa"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="PersonasQueHabitan">Personas Que Habitan</label>
                                    <div class="col-sm-8"> <input type="text" id="PersonasQueHabitan" name="PersonasQueHabitan" placeholder="" class="form-control" title="PersonasQueHabitan" value="<?php echo $frm["PersonasQueHabitan"] ?>" required></div>


                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Vehiculos">Vehiculos</label>
                                    <div class="col-sm-8"><input type="text" id="Vehiculos" name="Vehiculos" placeholder="" class="form-control" title="Vehiculos" value="<?php echo $frm["Vehiculos"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Mascotas">Mascotas</label>
                                    <div class="col-sm-8"> <input type="text" id="Mascotas" name="Mascotas" placeholder="" class="form-control" title="Mascotas" value="<?php echo $frm["Mascotas"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="LlamarEnCasoDeEmergencia">Llamar En Caso De Emergencia</label>
                                    <div class="col-sm-8"><input type="text" id="LlamarEnCasoDeEmergencia" name="LlamarEnCasoDeEmergencia" placeholder="" class="form-control" title="LlamarEnCasoDeEmergencia" value="<?php echo $frm["LlamarEnCasoDeEmergencia"] ?>" required></div>

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