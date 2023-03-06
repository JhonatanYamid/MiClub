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
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
                                    <div class="col-sm-8">
                                        <?php
                                        $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" onselect="prueba()" title="número de derecho" <?php if ($_GET["action"] != "add") echo "disabled"; ?> value="<?php echo utf8_decode($r_socio["Nombre"] . " " . $r_socio["Apellido"]) ?>">
                                        <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title=" Socio">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dirigida </label>
                                    <div class="col-sm-8">
                                        <input type="radio" id="Dirigida" name="Dirigida" value="S" <?php if ($frm[Dirigida] == "S") echo "checked"; ?>>Socio
                                        <input type="radio" id="Dirigida" name="Dirigida" value="F" <?php if ($frm[Dirigida] == "F") echo "checked"; ?>>Familia
                                        <input type="radio" id="Dirigida" name="Dirigida" value="M" <?php if ($frm[Dirigida] == "M") echo "checked"; ?>>Miembro
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socios posibles: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-SocioTalonera" title="número de derecho">
                                        <br>
                                        <a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
                                        <br>
                                        <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_invitados = explode("|", $frm["SociosPosibles"]);


                                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                if (!empty($datos_invitado)) {
                                                    $array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;

                                                    $IDSocioInvitacion = $array_datos_invitados[0];


                                                    if ($IDSocioInvitacion > 0) :
                                                        $nombre_socio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitacion . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitacion . "'"));
                                                        echo $nombre_socio;
                                            ?>
                                                        <option value="<?php echo  $IDSocioInvitacion . "-" . $nombre_socio . "|"; ?>"><?php echo $nombre_socio; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="SociosPosibles" id="SociosPosibles" value="">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones </label>
                                    <div class="col-sm-8">
                                        <textarea id="Observaciones" name="Observaciones" cols="10" rows="3" class="col-xs-12"><?php echo $frm["Observaciones"]; ?></textarea>
                                    </div>
                                </div>
							</div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicio </label>
                                    <div class="col-sm-8">
                                        <select name="IDServicio" id="IDServicio" class="form-control" required>
                                            <option value=""></option>
                                            <?php

                                            $sql_servicios = "Select SC.* From ServicioClub SC Where SC.IDClub = '" . SIMUser::get("club") . "' and SC.Activo = 'S' Order by TituloServicio";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) :

                                                $IDServicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $row_servicios["IDServicioMaestro"] . "' and IDClub = '" . SIMUser::get("club") . "' ");

                                                if (!empty($row_servicios["TituloServicio"]))
                                                    $nombre_servicio = $row_servicios["TituloServicio"];
                                                else
                                                    $nombre_servicio = utf8_encode($dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $row_servicios["IDServicioMaestro"] . "'")); ?>

                                                <option value="<?php echo $IDServicio ?>" <?php if ($frm["IDServicio"] == $IDServicio) echo "selected";  ?>><?php echo  $nombre_servicio ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Talonera </label>
                                    <div class="col-sm-8">
                                        <select name="IDTalonera" id="IDTalonera" class="form-control mandatory" title="Talonera" required>
                                            <option value=""></option>

                                            <?php

                                            $sql_servicios = "Select T.* From Talonera T Where T.IDClub = '" . SIMUser::get("club") . "' and T.Activa = '1' Order by T.IDTalonera";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) :

                                            ?>

                                                <option value="<?php echo $row_servicios["IDTalonera"] ?>" <?php if ($frm["IDTalonera"] == $row_servicios["IDTalonera"]) echo "selected";  ?>><?php echo  $row_servicios["NombreTalonera"] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <?php if ($_GET["action"] == "add") {
                                $estilo = "display:none;";
                            } else $estilo = "display:block;";
                            ?>

                            <div class="form-group first">

                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Valor pagado</label>
                                    <div class="col-sm-8"> <input type="text" id="ValorPagado" name="ValorPagado" placeholder="Valor Pagado" class="form-control" title="Valor Pagado" value="<?php echo $frm["ValorPagado"] ?>" required></div>
                                </div>

                                <div class="col-xs-12 col-md-6" style="<?php echo $estilo; ?>">
                                    <label class="col-sm-4 control-label" for="CantidadTotal">Cantidad total</label>
                                    <div class=" col-sm-8"> <input type="text" id="CantidadTotal" name="CantidadTotal" placeholder="Cantidad Total" class="form-control" title="Cantidad Total" value="<?php echo $frm["CantidadTotal"] ?>" required>
                                    </div>

                                </div>

                            </div>

                            <div class="form-group first" style="<?php echo $estilo; ?>">

                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Cantidad pendiente</label>
                                    <div class="col-sm-8"> <input type="text" id="CantidadPendiente" name="CantidadPendiente" placeholder="Cantidad Pendiente" class="form-control" title="Cantidad Pendiente" value="<?php echo $frm["CantidadPendiente"] ?>" required></div>

                                </div>


                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Fecha compra</label>
                                    <div class="col-sm-8"> <input type="date" id="FechaCompra" name="FechaCompra" placeholder="Fecha Compra" class="form-control" title="Fecha Compra" value="<?php echo $frm["FechaCompra"] ?>" <?php if ($_GET["action"] == "add") echo "readonly"; ?>></div>

                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="col-xs-12 col-md-6" style="<?php echo $estilo; ?>">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Fecha vencimiento</label>
                                    <div class="col-sm-8"> <input type="date" id="FechaVencimiento" name="FechaVencimiento" placeholder="Fecha Vencimiento" class="form-control" title="Fecha Vencimiento" value="<?php echo $frm["FechaVencimiento"] ?>" <?php if ($_GET["action"] == "add") echo "readonly"; ?>></div>

                                </div>

                            </div>

                            <div class="form-group first" style="<?php echo $estilo; ?>">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo monedero </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["TipoMonedero"], 'TipoMonedero', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Saldo monedero</label>
                                    <div class="col-sm-8"> <input type="text" id="SaldoMonedero" name="SaldoMonedero" placeholder="Saldo Monedero" class="form-control" title="Saldo Monedero" value="<?php echo $frm["SaldoMonedero"] ?>"></div>

                                </div>
                            </div>

                            <div class="form-group first" style="<?php echo $estilo; ?>">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Todos los servicios </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["TodosLosServicios"], 'TodosLosServicios', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Estado transaccion</label>
                                    <div class="col-sm-8"> <input type="text" id="EstadoTransaccion" name="EstadoTransaccion" placeholder="Estado Transaccion" class="form-control" title="EstadoTransaccion" value="<?php echo $frm["EstadoTransaccion"] ?>" <?php if ($_GET["action"] == "edit") echo "disabled" ?>></div>

                                </div>
                            </div>

                            <div class="form-group first" style="<?php echo $estilo; ?>">
                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Pagado</label>
                                    <div class="col-sm-8"> <input type="text" id="Pagado" name="Pagado" placeholder="Pagado" class="form-control" title="Pagado" value="<?php echo $frm["Pagado"] ?>" <?php if ($_GET["action"] == "edit") echo "disabled" ?>></div>

                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Medio de pago</label>
                                    <div class="col-sm-8"> <input type="text" id="MedioPago" name="MedioPago" placeholder="Medio Pago" class="form-control" title="MedioPago" value="<?php echo $frm["MedioPago"] ?>" <?php if ($_GET["action"] == "edit") echo "disabled" ?>></div>

                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activo"], 'Activo', "class='input'") ?>
                                    </div>
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

                                </div>
                            </div>
                        </div>

                        <?php if ($_GET["action"] == "edit") {

                        ?>

                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                    Consumos
                                </h3>
                            </div>

                            <br />
                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                <tr>

                                    <th>Socio que utilizo talonera</th>
                                    <th>Fecha consumo</th>
                                    <th>Fecha reserva</th>
                                    <th>Hora reserva</th>
                                    <th>Servicio</th>
                                    <th>Reserva Eliminada</th>
                                    <th>Valor pagado reserva</th>

                                </tr>
                                <tbody id="listacontactosanunciante">
                                    <?php
                                    $sql_reporte = "Select  CT.SocioConsume,CT.FechaConsumo,CT.IDReservaGeneral,CT.IDClub From ConsumoSocioTalonera CT
                                    Where CT.IDClub = '" . SIMUser::get("club") . "' AND CT.IDSocioTalonera='" . $frm["IDSocioTalonera"] . "'";


                                    $result_reporte = $dbo->query($sql_reporte);




                                    while ($Datos = $dbo->fetchArray($result_reporte)) {

                                        //nombre del servicio
                                        $IDServicio = $dbo->getFields("ReservaGeneral", "IDServicio", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "'");
                                        $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
                                        $sql_servicio = "SELECT TituloServicio FROM ServicioClub WHERE IDClub='" . SIMUser::get("club") . "'" . " AND IDServicioMaestro='" . $IDServicioMaestro . "'" . " AND Activo='S'";
                                        $servicio = $dbo->query($sql_servicio);
                                        $frm = $dbo->fetchArray($servicio);
                                        $TituloServicio = $frm["TituloServicio"];

                                        if (!empty($TituloServicio)) {
                                            $nombreServicio = $TituloServicio;
                                        } else {
                                            $nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDServicioMaestro . "'");
                                        }
                                    ?>

                                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">

                                            <td><?php echo $Datos["SocioConsume"]; ?></td>
                                            <td><?php echo $Datos["FechaConsumo"]; ?></td>
                                            <td><?php echo $dbo->getFields("ReservaGeneral", "Fecha", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "' AND IDClub='" . $Datos["IDClub"] . "'"); ?></td>
                                            <td><?php echo $dbo->getFields("ReservaGeneral", "Hora", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "'  AND IDClub='" . $Datos["IDClub"] . "'"); ?></td>
                                            <td><?php echo $nombreServicio; ?></td>

                                            <?php $IDReservaEliminada = $dbo->getFields("ReservaGeneralEliminada", "IDReservaGeneral", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "' AND IDClub='" . $Datos["IDClub"] . "'");
                                            if ($IDReservaEliminada > 0) {
                                                $ReservaEliminada = "Si";
                                                $Total += 0;
                                            } else if ($IDReservaEliminada == "") {
                                                $ReservaEliminada = "No";
                                                $Total += $dbo->getFields("ReservaGeneral", "ValorPagado", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "'");
                                            }
                                            ?>
                                            <td><?php echo $ReservaEliminada; ?></td>

                                            <td><?php echo $dbo->getFields("ReservaGeneral", "ValorPagado", "IDReservaGeneral = '" . $Datos["IDReservaGeneral"] . "' AND IDClub='" . $Datos["IDClub"] . "'"); ?></td>


                                        </tr>



                                    <?php }
                                    ?>
                                    <tr>
                                        <td align="right" colspan="7">Total:<?php echo number_format($Total, 0, ",", "."); ?></td>
                                    </tr>
                                </tbody>
                                <tr>
                                    <th class="texto" colspan="7"></th>
                                </tr>
                            </table>

                        <?php }
                        ?>
                </div>
                </form>




            </div>
        </div>
    </div><!-- /.widget-main -->
</div><!-- /.widget-body -->
</div><!-- /.widget-box -->


<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>

</script>


<?
include("cmp/footer_scripts.php");
?>