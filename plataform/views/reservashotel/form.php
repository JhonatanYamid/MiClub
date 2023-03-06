<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?> <div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR NUEVA <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal" role="formfechahotel" method="get" id="frmfechahotel" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendario_inicio_hotel" title="Fecha Inicio" value="<?php if (empty($_GET["FechaInicio"]))  echo "";
                                                                                                                                                                                            else echo $_GET["FechaInicio"]; ?>" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar calendario_fin_hotel" title="fecha fin" value="<?php if (empty($_GET["FechaFin"]))  echo "";
                                                                                                                                                                                        else echo $_GET["FechaFin"]; ?>" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> </label>
                                <div class="col-sm-8">
                                    <a id="calendariohotel" class="fancybox" href="calendariohotel/index.php?IDClub=<?php echo SIMUser::get("club"); ?>&ver=<?php echo rand(1, 1000) ?>" data-fancybox-type="iframe">Ver Calendario.</a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <input type="hidden" name="action" id="action" value="add" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frmfechahotel">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Buscar </button>
                            </div>
                        </div>
                    </form> <?php if (!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])) { ?> <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
                                    <div class="col-sm-8"> <?php
                                                            $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                                            $qry_socio_club = $dbo->query($sql_socio_club);
                                                            $r_socio = $dbo->fetchArray($qry_socio_club); ?> <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] ?>">
                                        <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
                                    </div>
                                </div>
                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pagado </label>
                                        <div class="col-sm-8">
                                            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Pagado"], 'Pagado', "class='input'") ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cabeza Reserva </label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(array("Socio" => "Socio", "Invitado" => "Invitado")), $frm["CabezaReserva"], 'CabezaReserva', "class='input duenoreservahotel' form-control") ?>
                                        <div id="div_NombreDuenoReserva" style="display:none">
                                            <br>Documento del invitado dueño de la reserva: <input type="number" name="DocumentoDuenoReserva" id="DocumentoDuenoReserva" class="form-control">
                                            <br>Nombre del invitado dueño de la reserva: <input type="text" name="NombreDuenoReserva" id="NombreDuenoReserva" class="form-control">
                                            <br>Correo del invitado dueño de la reserva: <input type="email" name="EmailDuenoReserva" id="EmailDuenoReserva" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitados Adicionales </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho o Nombre del invitado" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho">
                                        <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
                                        <br>
                                        <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple> <?php
                                                                                                                        $item = 1;
                                                                                                                        foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                                                                                            $item--;
                                                                                                                            if ($datos_invitado["IDSocio"] > 0) :
                                                                                                                                $nombre_socio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_invitado["IDSocio"] . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_invitado["IDSocio"] . "'");
                                                                                                                        ?> <option value="<?php echo "socio-" . $datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?></option> <?php
                                                                                                                                                                                                                                    else : ?> <option value="<?php echo "externo-" . $datos_invitado["Nombre"]; ?>"><?php echo $datos_invitado["Nombre"]; ?></option> <?php
                                                                                                                                                                                                                                                                                                                                                                    endif;
                                                                                                                                                                                                                                                                                                                                                                endforeach;
                                                                                                                                                                                                                                                                                                                                                                        ?> </select>
                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                    </div>
                                </div>
                                <!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado Reserva </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup(array_flip(array("cola" => "Cola", "pendiente" => "Pendiente", "enfirme" => "enfirme", "cancelada" => "cancelada")), $frm["Estado"], 'Estado', "class='input' form-control") ?>
    									</div>
								</div>
							-->
                            </div>
                            <!--
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Niñera </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Ninera"], 'Ninera', "class='input'") ?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Corral </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Corral"], 'Corral', "class='input'") ?>
										</div>
								</div>

							</div>
						-->
                            <!-- Campos dinámicos-->
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info green"></i> Otros campos
                                </h3>
                            </div> <?php
                                    $query = $dbo->query("SELECT IDCampoHotel, Nombre, Tipo, Valores, Obligatorio, Orden 
							FROM CampoHotel 							
							WHERE IDClub=" . SIMUser::get("club") . " 
							ORDER BY Orden ASC");
                                    $camposhotel = $dbo->fetch($query);
                                    if (isset($camposhotel["Nombre"])) {
                                        $camposhotel = [$camposhotel];
                                    }

                                    $camposhotel = array_chunk($camposhotel, 2);
                                    $key = 0;
                                    foreach ($camposhotel as $value) :

                                    ?> <div class="form-group first "> <?php

                                                                        foreach ($value as $campos_hotel) :


                                                                            $IDCampoHotel = $campos_hotel["IDCampoHotel"];

                                                                            $query = $dbo->query("SELECT IDHotelCampoHotel, Valor 
									FROM HotelCampoHotel
									WHERE IDCampoHotel=$IDCampoHotel									
									AND IDSocio={$_GET["id"]}
									LIMIT 1");

                                                                            $campo = $dbo->fetch($query);

                                                                        ?> <div class="col-xs-12 col-sm-6">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?php echo $campos_hotel["Nombre"] ?></label>
                                            <div class="col-sm-8"> <?php $campos = explode(",", $campos_hotel["Valores"]); ?> <?php if ($campos_hotel["Valores"] != null && $campos_hotel["Valores"] != "") : ?> <select name="campos_dinamicos[Valor_<?php echo $key ?>]">
                                                        <option></option> <?php foreach ($campos as $option) : ?> <option value="<?php echo $option ?>" <?php if ($option == $campo["Valor"]) {
                                                                                                                                                            echo 'selected';
                                                                                                                                                        } ?>><?php echo $option ?></option> <?php endforeach; ?>
                                                    </select> <?php else : ?> <input type="text" name="campos_dinamicos[Valor_<?php echo $key ?>]" placeholder="<?php echo $campos_vacuna["Nombre"] ?>" class="col-xs-12" value="<?php echo $campo["Valor"] ?>" /> <?php endif; ?>
                                                <!--input type="hidden" name="campos_dinamicos[IDSocio_<?php //echo $key
                                                                                                        ?>]" value="<?php //echo $frm["IDSocio"] 
                                                                                                                    ?>"-->
                                                <input type="hidden" name="campos_dinamicos[IDCampoHotel_<?php echo $key ?>]" value="<?php echo $campos_hotel["IDCampoHotel"] ?>">
                                                <input type="hidden" name="campos_dinamicos[IDHotelCampoHotel_<?php echo $key++ ?>]" value="<?php echo $campo["IDHotelCampoHotel"] ?>">
                                            </div>
                                        </div> <?php
                                                                        endforeach;

                                                ?> <input type="hidden" name="campos_dinamicos[keys]" value="<?php echo $key ?>">
                                </div> <?php

                                    endforeach;

                                        ?> <div class="form-group first ">
                                <!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Adicional	  </label>

										<div class="col-sm-8">
										  <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Adicional"], 'Adicional', "class='input'") ?>
										</div>
								</div>
							-->
                            </div>
                            <div id="tab" class="tab-pane">
                                <table id="disponibilidad<?= $key_elemento ?>" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Habitacion</th>
                                            <th class="hidden-480"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?


                                        foreach ($resultado_habitacion["response"][0]["Habitaciones"] as $key_disp => $datos_habitacion) {
                                            foreach ($datos_habitacion["HabitacionTorre"] as $id_hab => $habitacion) { ?>
                                                <tr>
                                                    <td> <?php echo $habitacion["NombreHabitacion"] . " " . $habitacion["NumeroHabitacion"] . " Capacidad: " . $habitacion["CapacidadHabitacion"]  ?> </td>
                                                    <td class="hidden-480">
                                                        <span id="txtmsjreserva<?php echo $habitacion["IDHabitacion"] ?>"><a href="#reservashotel" class="btnReservaHotel" rel="<?php echo $habitacion["IDHabitacion"] ?>">Reservar</a></span>
                                                    </td>
                                                </tr> <?php
                                                    } //end for
                                                }
                                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDHabitacion" id="IDHabitacion" value="" />
                                    <input type="hidden" name="FechaInicio" id="FechaInicio" value="<?php echo $_GET["FechaInicio"] ?>" />
                                    <input type="hidden" name="FechaFin" id="FechaFin" value="<?php echo $_GET["FechaFin"] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <!--
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
									</button>
								-->
                                </div>
                            </div>
                        </form> <?php  } ?>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<?
include("cmp/footer_scripts.php");
include("cmp/footer_grid.php");
?>