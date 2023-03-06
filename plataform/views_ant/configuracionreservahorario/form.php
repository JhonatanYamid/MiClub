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
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Header Horario</label>
                                    <div class="col-sm-8"><input type="text" id="LabelHeaderHorario" name="LabelHeaderHorario" placeholder="" class="form-control" title="Texto Header Horario" value="<?php echo $frm["LabelHeaderHorario"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Header Resumen</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelHeaderResumen" name="LabelHeaderResumen" placeholder="" class="form-control" title="Texto Header Resumen" value="<?php echo $frm["LabelHeaderResumen"] ?>" required></div>
                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Boton Seleccionar Hijo</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelBotonSeleccionarHijo" name="LabelBotonSeleccionarHijo" placeholder="" class="form-control" title="Texto Boton Seleccionar Hijo" value="<?php echo $frm["LabelBotonSeleccionarHijo"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Header Seleccionar Hijo</label>
                                    <div class="col-sm-8"><input type="text" id="LabelHeaderSeleccionarHijo" name="LabelHeaderSeleccionarHijo" placeholder="" class="form-control" title="Texto Header Seleccionar Hijo" value="<?php echo $frm["LabelHeaderSeleccionarHijo"] ?>" required></div>
                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Header Seleccion Hijo</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelHeaderSeleccionHijo" name="LabelHeaderSeleccionHijo" placeholder="" class="form-control" title="Texto Header Seleccion Hijo" value="<?php echo $frm["LabelHeaderSeleccionHijo"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Boton Reservar</label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonReservar" name="LabelBotonReservar" placeholder="" class="form-control" title="Texto Boton Reservar" value="<?php echo $frm["LabelBotonReservar"] ?>" required></div>
                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Boton Mis Reservas</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelBotonMisReservas" name="LabelBotonMisReservas" placeholder="" class="form-control" title="Texto Boton Mis Reservas" value="<?php echo $frm["LabelBotonMisReservas"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Boton Eliminar Reserva</label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonEliminarReserva" name="LabelBotonEliminarReserva" placeholder="" class="form-control" title="Texto Boton Eliminar Reserva" value="<?php echo $frm["LabelBotonEliminarReserva"] ?>" required></div>
                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Numero De Semanas a Mostrar</label>
                                    <div class="col-sm-8"> <input type="number" id="NumeroSemanas" name="NumeroSemanas" placeholder="" class="form-control" title="Numero De Semanas" value="<?php echo $frm["NumeroSemanas"] ?>" required></div>
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