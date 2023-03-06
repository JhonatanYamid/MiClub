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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>
                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Reserva </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaReserva" name="FechaReserva" placeholder="Fecha Reserva" class="col-xs-12 calendar" title="Fecha Reserva" value="<?php echo $frm["FechaReserva"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Invitados </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaInivitados" name="FechaInivitados" placeholder="Fecha Invitados" class="col-xs-12 calendar" title="fecha invitados" value="<?php echo $frm["FechaInivitados"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Cancelaci&oacute;n </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaCancelacion" name="FechaCancelacion" placeholder="Fecha Cancelacion" class="col-xs-12 calendar" title="Fecha Cancelacion" value="<?php echo $frm["FechaCancelacion"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habitación para las fechas de corto plazo: </label>
                                <div class="col-sm-8">
                                    <select name=IDHabitacion>
                                        <option value=0>[ESCOGE LA HABITACIÓN]</option> <?php
											$sql = "SELECT * FROM Habitacion WHERE IDClub = ". SIMUser::get("club");
											$qry = $dbo->query($sql);
											while($Datos = $dbo->fetchArray($qry)):
												if(empty($Datos[NombreHabitacion])):
													$Datos[NombreHabitacion] = $dbo->getFields("TipoHabitacion","Nombre","IDTipoHabitacion = $Datos[IDTipoHabitacion]");
												endif;

												$NombreTipo = $dbo->getFields("TipoHabitacion","Nombre","IDTipoHabitacion = $Datos[IDTipoHabitacion]");

												?> <option value="<?php echo $Datos[IDHabitacion]; ?>" <?php if($Datos[IDHabitacion] == $frm[IDHabitacion]) echo "selected"; ?>><?php echo $NombreTipo . " - " . $Datos[NombreHabitacion]; ?></option> <?php
											endwhile;
										?>
                                    </select>
                                </div>
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