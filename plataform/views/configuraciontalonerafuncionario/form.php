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
                                <!--   <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dirigido A </label>

                                    <div class="col-sm-8">
                                        <?php /* echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") */ ?>

                                    </div>

                                </div> -->
                                <input type="hidden" name="DirigidoA" id="DirigidoA" value="E" />


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelConfirmacionEnvio">Confirmacion de envio</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelConfirmacionEnvio" name="LabelConfirmacionEnvio" placeholder="Confirmacion Envio" class="form-control" title="Confirmacion Envio" value="<?php echo $frm["LabelConfirmacionEnvio"] ?>" required></div>


                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelServicioComprar"> Servicio comprar</label>
                                    <div class="col-sm-8"><input type="text" id="LabelServicioComprar" name="LabelServicioComprar" placeholder="Servicio Comprar" class="form-control" title="Servicio Comprar" value="<?php echo $frm["LabelServicioComprar"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelTipoPaquete">Tipo de paquete</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelTipoPaquete" name="LabelTipoPaquete" placeholder="Tipo Paquete" class="form-control" title="Tipo Paquete" value="<?php echo $frm["LabelTipoPaquete"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelBotonConfirmarCompra">Confirmar compra</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelBotonConfirmarCompra" name="LabelBotonConfirmarCompra" placeholder="Boton Confirmar Compra" class="form-control" title="Boton Confirmar Compra" value="<?php echo $frm["LabelBotonConfirmarCompra"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelBotonComprar"> Boton Comprar</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelBotonComprar" name="LabelBotonComprar" placeholder="Boton Comprar" class="form-control" title="Boton Comprar" value="<?php echo $frm["LabelBotonComprar"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelMisTaloneras"> Mis Taloneras</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelMisTaloneras" name="LabelMisTaloneras" placeholder="Mis Taloneras" class="form-control" title="Mis Taloneras" value="<?php echo $frm["LabelMisTaloneras"] ?>" required></div>

                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelParaMi">Texto para elegir la talonera cuando la talonera es para el socio que la compra ( se recomienda “para mi”)</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelParaMi" name="LabelParaMi" placeholder="Para Mi" class="form-control" title="Para Mi" value="<?php echo $frm["LabelParaMi"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelParaMiFamilia">Texto para elegir la talonera cuando la talonera es para el grupo familiar incluyendo el socio que la compra ( se recomienda “para mi familia”)</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelParaMiFamilia" name="LabelParaMiFamilia" placeholder="Para Mi Familia" class="form-control" title="Para Mi Familia" value="<?php echo $frm["LabelParaMiFamilia"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CorreoNotificaciones">Correo Notificaciones</label>
                                    <div class="col-sm-8"> <input type="text" id="CorreoNotificaciones" name="CorreoNotificaciones" placeholder="Correo Notificaciones" class="form-control" title="Correo Notificaciones" value="<?php echo $frm["CorreoNotificaciones"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Descontar de la talonera los invitados incluidos?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["DescontarInvitados"], 'DescontarInvitados', "class='input'") ?>
                                </div>                                
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Permite comprar taloneras para todos los servicios?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteTalonerasMultipleServicios"], 'PermiteTalonerasMultipleServicios', "class='input'") ?>
                                </div>                                
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Mostrar solo las taloneras que son validas para todos los servicios?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["SoloTalonerasMultiplesServicios"], 'SoloTalonerasMultiplesServicios', "class='input'") ?>
                                </div>                                
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Se puede comprar taloneras desde la aplicación?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermitirComprarTaloneraPorApp"], 'PermitirComprarTaloneraPorApp', "class='input'") ?>
                                </div>                                
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Se permite recargar los monederos?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermitirRecargarMonedero"], 'PermitirRecargarMonedero', "class='input'") ?>
                                </div>                                
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados">Cuando se elimina la reserva desde el administrador, retornar valor a la talonera?</label>
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["RetornarValorEliminaAdmin"], 'RetornarValorEliminaAdmin', "class='input'") ?>
                                </div>                                
                                                       
                            </div>
                            
                            
                            
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados"> Mostrar Boton Planes</label>
                 <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino),  $frm["MostrarBotonPlanes"], 'MostrarBotonPlanes', "class='input '") ?>

                                </div>                     
                            </div> 
                             
                            <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CorreoNotificaciones">Texto Boton Planes</label>
                                    <div class="col-sm-8"> <input type="text" id="TextoBotonPlanes" name="TextoBotonPlanes" placeholder="Texto Boton Planes" class="form-control" title="Correo Notificaciones" value="<?php echo $frm["TextoBotonPlanes"] ?>" ></div>

                                </div>
                            </div>
                               <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescontarInvitados"> Cargar archivo</label>
                    <div class="col-sm-8"> <input type="file" id="file" name="ArchivoPlanes" placeholder="Cargar archivo" class="form-control" title="Correo Notificaciones" value="<?php echo $frm["ArchivoPlanes"] ?>" >
                    <?php if($frm["ArchivoPlanes"]==""){
                    echo "No ha subido archivos aun!";
                    
                    }else{
 ?>
 <a href="<?php echo $frm["ArchivoPlanes"]?>">ver archivo actual</a>
 
                 <?php   } ?>
                    
                    </div>
                                </div>                     
                            </div> 
 
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activa </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activa"], 'Activa', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pago: </label>
                                    <div class="col-sm-8">
                                        <?php
                                        $sql_tipo_pago_servicio = "Select * From ConfiguracionTaloneraTipoPago Where IDConfiguracionTalonera = '" . SIMNet::reqInt("id") . "'";

                                        $result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
                                        while ($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)) :
                                            $array_tipo_pago_servicio[] = $row_tipo_pago_servicio["IDTipoPago"];

                                        endwhile;
                                        $sql_tipo_pago = "Select * From TipoPago Where Publicar = 'S'";
                                        $result_tipo_pago = $dbo->query($sql_tipo_pago);
                                        while ($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)) : ?>
                                            <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if (in_array($row_tipo_pago["IDTipoPago"], $array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br>
                                        <?php endwhile; ?>
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
