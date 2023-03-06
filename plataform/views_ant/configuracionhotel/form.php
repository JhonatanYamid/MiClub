<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?> <div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR NUEVA <?php echo strtoupper(SIMReg::get( "title" ))?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Iva </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Iva" name="Iva" placeholder="Iva" class="col-xs-12 mandatory" title="Iva" value="<?php echo $frm["Iva"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Maximo De Habitaciones En Temporada Alta Para Los Socios </label>
                                <div class="col-sm-8">
                                    <input type="text" id="MaximoHTAlta" name="MaximoHTAlta" placeholder="MaximoHTAlta" class="col-xs-12 mandatory" title="MaximoHTAlta" value="<?php echo $frm["MaximoHTAlta"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias De Antelacion En Temporada Baja Para Hacer La Reserva </label>
                                <div class="col-sm-8">
                                    <input type="text" id="DiasTBaja" name="DiasTBaja" placeholder="DiasTBaja" class="col-xs-12 mandatory" title="DiasTBaja" value="<?php echo $frm["DiasTBaja"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias Invitados Para Confirmar Reservas </label>
                                <div class="col-sm-8">
                                    <input type="text" id="DiasInvitados" name="DiasInvitados" placeholder="DiasInvitados" class="col-xs-12 mandatory" title="DiasInvitados" value="<?php echo $frm["DiasInvitados"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Veces Invitado Año </label>
                                <div class="col-sm-8">
                                    <input type="text" id="VecesInvitadoAnio" name="VecesInvitadoAnio" placeholder="Veces Invitado Anio" class="col-xs-12 mandatory" title="VecesInvitadoAnio" value="<?php echo $frm["VecesInvitadoAnio"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Veces Invitado Mes </label>
                                <div class="col-sm-8">
                                    <input type="text" id="VecesInvitadoMes" name="VecesInvitadoMes" placeholder="VecesInvitadoMes" class="col-xs-12 mandatory" title="VecesInvitadoMes	" value="<?php echo $frm["VecesInvitadoMes"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Abono </label>
                                <div class="col-sm-8">
                                    <input type="text" id="PorcentajeAbono" name="PorcentajeAbono" placeholder="PorcentajeAbono" class="col-xs-12 mandatory" title="PorcentajeAbono" value="<?php echo $frm["PorcentajeAbono"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia Cancelacion </label>
                                <div class="col-sm-8">
                                    <select name="DiaCancelacion" id="DiaCancelacion" style="form-control">
                                        <option value=""></option>
                                        <option value="Lunes" <?php if($frm["DiaCancelacion"]=="Lunes") echo "selected"; ?>>Lunes</option>
                                        <option value="Martes" <?php if($frm["DiaCancelacion"]=="Martes") echo "selected"; ?>>Martes</option>
                                        <option value="Miercoles" <?php if($frm["DiaCancelacion"]=="Miercoles") echo "selected"; ?>>Miercoles</option>
                                        <option value="Jueves" <?php if($frm["DiaCancelacion"]=="Jueves") echo "selected"; ?>>Jueves</option>
                                        <option value="Viernes" <?php if($frm["DiaCancelacion"]=="Viernes") echo "selected"; ?>>Viernes</option>
                                        <option value="Sabado" <?php if($frm["DiaCancelacion"]=="Sabado") echo "selected"; ?>>Sabado</option>
                                        <option value="Domingo" <?php if($frm["DiaCancelacion"]=="Domingo") echo "selected"; ?>>Domingo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Cancelacion </label>
                                <div class="col-sm-8">
                                    <input type="time" name="HoraCancelacion" id="HoraCancelacion" style="form-control" value="<?php echo $frm["HoraCancelacion"]?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Administrador </label>
                                <div class="col-sm-8">
                                    <input type="text" id="EmailAdministrador" name="EmailAdministrador" placeholder="EmailAdministrador" class="col-xs-12 mandatory" title="EmailAdministrador" value="<?php echo $frm["EmailAdministrador"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                         
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Notificar nuevas reservas </label>
                                <div class="col-sm-8">
								<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["NotificaReserva"] , 'NotificaReserva' , "class='input'" ) ?>

                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton pregunta adicional ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["BotonAdicional"] , 'BotonAdicional' , "class='input'" ) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton pregunta Ninera </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["BotonNinera"] , 'BotonNinera' , "class='input'" ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton pregunta corral ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["BotonCorral"] , 'BotonCorral' , "class='input'" ) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton pregunta dueño reserva invitado ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["BotonInvitado"] , 'BotonInvitado' , "class='input'" ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Observaciones"] , 'Observaciones' , "class='input'" ) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Preguntar Invitado externo o socio ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["InvitadoExternoSocio"] , 'InvitadoExternoSocio' , "class='input'" ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Formulario Personalizado cuando el dueño es invitado? (si se marca Si diligenciar que datos preguntar si se marca no se osrrar por defecto "Externo o Socio Club") </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["FormularioInvitado"] , 'FormularioInvitado' , "class='input'" ) ?>
                                    <input type="text" id="CamposInvitado" name="CamposInvitado" placeholder="Cedula,Nombre" class="col-xs-12 " title="CamposInvitado" value="<?php  if(empty($frm["CamposInvitado"])) { echo "Cedula,Nombre"; } else { echo $frm["CamposInvitado"]; }  ; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label boton pagar </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBotonPagar" name="LabelBotonPagar" placeholder="Label Boton Pagar" class="col-xs-12 mandatory" title="Label Boton Pagar" value="<?php echo $frm["LabelBotonPagar"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> LabelBotonAcompanante </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBotonAcompanante" name="LabelBotonAcompanante" placeholder="Label Boton Acompanante" class="col-xs-12 mandatory" title="LabelBotonAcompanante" value="<?php echo $frm["LabelBotonAcompanante"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label dueño reserva </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelDuenoReserva" name="LabelDuenoReserva" placeholder="Seleccione quien toma la reserva" class="col-xs-12 mandatory" title="LabelDuenoReserva" value="<?php echo $frm["LabelDuenoReserva"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label dueño invitado </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelDuenoInvitado" name="LabelDuenoInvitado" placeholder="Seleccionar invitado que toma la reserva" class="col-xs-12 mandatory" title="LabelDuenoInvitado" value="<?php echo $frm["LabelDuenoInvitado"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Texto Legal </label>
                                <div class="col-sm-8">
                                    <input type="text" id="TextoLegal" name="TextoLegal" placeholder="Texto Legal" class="col-xs-12 " title="Texto Legal" value="<?php echo $frm["TextoLegal"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar boton detalle habitacion </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["MostrarDetalleHabitacion"] , 'MostrarDetalleHabitacion' , "class='input'" ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Las tarifas se cobran por Tipo Socio? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["TarifasPorTipoSocio"] , 'TarifasPorTipoSocio' , "class='input'" ) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto de encabezado para seleccionar ls selección de fechas </label>
                                <div class="col-sm-8">
                                    <input type="text" id="SeleccionFechasHeaderLabel" name="SeleccionFechasHeaderLabel" placeholder="" class="col-xs-12 " title="Texto Legal" value="<?php echo $frm["SeleccionFechasHeaderLabel"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ver calendario tipo pop up ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteVistaFechaCalendario"] , 'PermiteVistaFechaCalendario' , "class='input'" ) ?>
                                </div>
                            </div>                            
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite eliminar las reservas por parte del socio ? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteEliminarReservaSocio"] , 'PermiteEliminarReservaSocio' , "class='input'" ) ?>
                                </div>
                            </div>                            
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto para el boton de eliminar reserva </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelEliminarReservasocio" name="LabelEliminarReservasocio" placeholder="" class="col-xs-12 " title="Texto Legal" value="<?php echo $frm["LabelEliminarReservasocio"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Editar invitados </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteEditarinvitados"] , 'PermiteEditarinvitados' , "class='input'" ) ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Pago: </label>
                                <div class="col-sm-8"> <?php
										$sql_tipo_pago_servicio = "Select * From HotelTipoPago Where IDClub = '".$frm["IDClub"]."'";
										$result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
										while($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)):
											$array_tipo_pago_servicio [] = $row_tipo_pago_servicio["IDTipoPago"];
										endwhile;
										$sql_tipo_pago = "Select * From TipoPago Where Publicar = 'S'";
										$result_tipo_pago = $dbo->query($sql_tipo_pago);
										while($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)): ?> <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if(in_array($row_tipo_pago["IDTipoPago"],$array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br> <?php endwhile; ?> </div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?> </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<?
	include( "cmp/footer_scripts.php" );
?>