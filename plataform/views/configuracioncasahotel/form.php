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
                                    <label class="col-sm-4 control-label" for="LabelBotonMisReservas">Label Boton mis Reservas</label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonMisReservas" name="LabelBotonMisReservas" placeholder="LabelBotonMisReservas" class="form-control" title="LabelBotonMisReservas" value="<?php echo $frm["LabelBotonMisReservas"] ?>" required></div>

                                </div>

                                
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelHacerReservas">Label Hacer Reservas</label>
                                    <div class="col-sm-8"><input type="text" id="LabelHacerReservas" name="LabelHacerReservas" placeholder="LabelHacerReservas" class="form-control" title="LabelHacerReservas" value="<?php echo $frm["LabelHacerReservas"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelSolicitudes">Label Solicitudes</label>
                                    <div class="col-sm-8"><input type="text" id="LabelSolicitudes" name="LabelSolicitudes" placeholder="LabelSolicitudes" class="form-control" title="LabelSolicitudes" value="<?php echo $frm["LabelSolicitudes"] ?>" required></div>

                                </div>  



                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="MostrarMensajeInfoReserva">Mostrar Mensaje Info Reserva</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["MostrarMensajeInfoReserva"], "MostrarMensajeInfoReserva", "", "");
                                        ?>
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelMensajeInfoReserva">Label Mensaje Info Reserva</label>
                                    <div class="col-sm-8"><input type="text" id="LabelMensajeInfoReserva" name="LabelMensajeInfoReserva" placeholder="LabelMensajeInfoReserva" class="form-control" title="LabelMensajeInfoReserva" value="<?php echo $frm["LabelMensajeInfoReserva"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="PermiteTipoReserva">Permite Tipo Reserva</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermiteTipoReserva"], "PermiteTipoReserva", "", "");
                                        ?>
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="MinutosEsperaReserva">Minutos Espera Reserva</label>
                                    <div class="col-sm-8"><input type="text" id="MinutosEsperaReserva" name="MinutosEsperaReserva" placeholder="MinutosEsperaReserva" class="form-control" title="MinutosEsperaReserva" value="<?php echo $frm["MinutosEsperaReserva"] ?>" required></div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="AniosDisponibles">Años Disponibles reservar</label>
                                    <div class="col-sm-8"><input type="text" id="AniosDisponibles" name="AniosDisponibles" placeholder="Años Disponibles reservar" class="form-control" title="AniosDisponibles" value="<?php echo $frm["AniosDisponibles"] ?>" required></div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Anticipacion">Anticipación</label>
                                    <div class="col-sm-8"><input type="text" id="Anticipacion" name="Anticipacion" placeholder="Anticipación" class="form-control" title="Anticipacion" value="<?php echo $frm["Anticipacion"] ?>" required></div>

                                </div>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">IconoHacerReservas</label>
                                    <div class="col-sm-8">
                                        <?
                                        if (!empty($frm["IconoHacerReservas"])) {
                                            echo "<img src='".PUBLICIDAD_ROOT.$frm['IconoHacerReservas']."' width=55 height=100 >";
                                            echo "<a href='" . $script . ".php?action=delfoto&foto=$frm[IconoHacerReservas]&campo=IconoHacerReservas&id=" . $frm[$key] . "' class='ace-icon glyphicon glyphicon-trash'>&nbsp;</a>";
                                        }
                                        ?>
                                        <input name="IconoHacerReservas" id='file' class="" title="IconoHacerReservas" type="file" size="25" style="font-size: 10px">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">IconoSolicitudes</label>
                                    <div class="col-sm-8">
                                        <?
                                        if (!empty($frm["IconoSolicitudes"])) {
                                            echo "<img src='".PUBLICIDAD_ROOT.$frm['IconoSolicitudes']."' width=55 height=100 >";
                                            echo "<a href='" . $script . ".php?action=delfoto&foto=$frm[IconoSolicitudes]&campo=IconoSolicitudes&id=" . $frm[$key] . "' class='ace-icon glyphicon glyphicon-trash'>&nbsp;</a>";
                                        }
                                        ?>
                                        <input name="IconoSolicitudes" id=file class="" title="IconoSolicitudes" type="file" size="25" style="font-size: 10px">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDUsuario" id="action" value="<?php if (empty($frm["IDUsuario"])) echo SIMUser::get("IDUsuario");
                                                                                                    else echo $frm["IDUsuario"];  ?>" />
                                        <input type="hidden" name="IDSocio" id="action" value="<?php if (empty($frm["IDSocio"])) echo SIMUser::get("IDSocio");
                                                                                                else echo $frm["IDSocio"];  ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>

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