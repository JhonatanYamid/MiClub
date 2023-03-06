<?php
include("procedures/general.php");
include("procedures/reservashotel.php");
include("cmp/seo.php");
?> </head>

<body class="no-skin">
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <?
                    SIMNotify::each();
                    ?>
                    <div class="page-header">
                        <h1> Home <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?> <i class="ace-icon fa fa-angle-double-right"></i> DETALLE RESERVA HOTEL </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12"> <?php if ($_GET["tipo"] != "horario") { ?> <form id="frmDuenoInvitadoHotel" name="frmDuenoInvitadoHotel" action="" method="post" enctype="multipart/form-data">
                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <td>Club</td>
                                                    <td><?php echo $dbo->getFields("Club", "Nombre", "IDClub = '" . $detalle_reserva["IDClub"] . "'"); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha Creacion Reserva</td>
                                                    <td><?php echo $detalle_reserva["FechaTrCr"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Creada por</td>
                                                    <td><?php echo $detalle_reserva["UsuarioTrCr"] . " - " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle_reserva["IDUsuarioReserva"] . "'"); ?> </td>
                                                </tr>
                                                <tr>
                                                    <td>Numero Reserva</td>
                                                    <td><?php echo $detalle_reserva["IDReserva"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Habitacion</td>
                                                    <td><?php
                                                            $datos_hab = $dbo->fetchAll("Habitacion", " IDHabitacion = '" . $detalle_reserva["IDHabitacion"] . "' ", "array");
                                                            echo $datos_hab["NumeroHabitacion"] . " - " . $dbo->getFields("Torre", "Nombre", "IDTorre = '" . $datos_hab["IDTorre"] . "'") . " - " .
                                                                $dbo->getFields("TipoHabitacion", "Nombre", "IDTipoHabitacion = '" . $datos_hab["IDTipoHabitacion"] . "'");
                                                        ?> </td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha Reserva</td>
                                                    <td><?php echo "Inicio: " . $detalle_reserva["FechaInicio"] . " Fin:" .  $detalle_reserva["FechaFin"] . " Temporada: " . $detalle_reserva["Temporada"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Estado</td>
                                                    <td><?php echo $detalle_reserva["Estado"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tipo Reserva</td>
                                                    <td><?php echo $detalle_reserva["TipoReserva"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Valor</td>
                                                    <td><?php
                                                            $tota_reserva = $detalle_reserva["Valor"] + ($detalle_reserva["Valor"] * $detalle_reserva["IVA"] / 100);
                                                            echo "$" . number_format($detalle_reserva["Valor"], 0, '', '.') . " IVA: " . $detalle_reserva["IVA"] . " TOTAL: " . "$" . number_format($tota_reserva, 0, '', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Pagado</td>
                                                    <td><?php echo $detalle_reserva["Pagado"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Responsable Reserva</td>
                                                    <td><?php echo $detalle_reserva["CabezaReserva"]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Pagada:</td>
                                                    <td>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $detalle_reserva["Pagado"], 'Pagado', "class='input mandatory'") ?>
                                                    </td>
                                                </tr> <?php if ($detalle_reserva["CabezaReserva"] == "Invitado") { ?> <tr>
                                                        <td>Documento Invitado Dueño de la Reserva:</td>
                                                        <td>
                                                            <input type="text" name="DocumentoDuenoReserva" id="DocumentoDuenoReserva" class="form-control" value="<?php echo $detalle_reserva["DocumentoDuenoReserva"]; ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nombre Invitado Dueño de la Reserva:</td>
                                                        <td>
                                                            <input type="text" name="NombreDuenoReserva" id="NombreDuenoReserva" class="form-control" value="<?php echo $detalle_reserva["NombreDuenoReserva"]; ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Correo Invitado Dueño de la Reserva:</td>
                                                        <td><input type="text" name="EmailDuenoReserva" id="EmailDuenoReserva" class="form-control" value="<?php echo $detalle_reserva["EmailDuenoReserva"]; ?>"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Enviar Correo Invitado Dueño de la Reserva con valor y link de pago?:</td>
                                                        <td>
                                                            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), '', 'CorreoPago', "class='input'") ?>
                                                        </td>
                                                    </tr> <?php } ?> <?php
                                                                        //Si se solicita otros campos al momento de reservar muestro los valores
                                                                        $sql_otro_dato = "Select * From ReservaHotelCampo Where IDReserva = '" . $detalle_reserva["IDReserva"] . "'";
                                                                        $result_otro_dato = $dbo->query($sql_otro_dato);
                                                                        while ($row_otro_dato = $dbo->fetchArray($result_otro_dato)) : ?> <tr>
                                                        <td><?php echo $dbo->getFields("ServicioCampo", "Nombre", "IDServicioCampo = '" . $row_otro_dato["IDServicioCampo"] . "'"); ?></td>
                                                        <td><?php echo $row_otro_dato["Valor"]; ?></td>
                                                    </tr> <?php endwhile; ?> <?php
                                                                                //if(count($array_invitados)>0 || $detalle_reserva["IDServicio"] == "24" || $detalle_reserva["IDServicio"] == "289" ):
                                                                                $invitados = "S";
                                                                                ?> <?php
                                                                                    //if($id_servicio_maestro==15): //15 = Golf
                                                                                    if ($invitados == "S" || $id_servicio_maestro > 0 || (SIMUser::get("PermiteCambiarReserva") == "S" || SIMUser::get("IDPerfil") == 0)) : //15 = Golf	 
                                                                                    ?> <tr>
                                                        <td align="center" colspan="2">
                                                            <input type="hidden" name="action" id="action" value="updateduenoinvitadohotel">
                                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $detalle_reserva["IDClub"]; ?>">
                                                            <input type="hidden" name="CabezaReserva" id="CabezaReserva" value="<?php echo $detalle_reserva["CabezaReserva"]; ?>">
                                  <input type="hidden" name="FechaInicio" id="Valor" value="<?php echo $detalle_reserva["FechaInicio"]; ?>">
                                  <input type="hidden" name="FechaFin" id="Valor" value="<?php echo $detalle_reserva["FechaFin"]; ?>">
                                                            <input type="hidden" name="Valor" id="Valor" value="<?php echo $detalle_reserva["Valor"]; ?>">
                                                            <input type="hidden" name="IDReservaHotel" id="IDReservaHotel" value="<?php echo $detalle_reserva["IDReserva"]; ?>">
                    <!-- <input type="hidden" name="Pagado" id="Valor" value="<?php echo  $detalle_reserva["Pagado"]; ?>">  -->   
                                                            <input type="submit" name="actualiza_dueno_reserva_hotel" id="actualiza_dueno_reserva_hotel" value="Actualizar datos">
                                                    </tr> <?php endif; ?>
                                            </table>
                                        </form>
                                        <form id="frmUpdateInvitadoHotel" name="frmUpdateInvitadoHotel" action="" method="post" enctype="multipart/form-data">
                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <td>Socio</td>
                                                    <td><?php
                                                            if (SIMUser::get("PermiteCambiarReserva") == "S" || SIMUser::get("IDPerfil") == 0) :
                                                                $sql_socio_club = "Select * From Socio Where IDSocio = '" . $detalle_reserva["IDSocio"] . "'";
                                                                $qry_socio_club = $dbo->query($sql_socio_club);
                                                                $r_socio = $dbo->fetchArray($qry_socio_club); ?> <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho, nombre, apellido" value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] ?>">
                                                            <input type="hidden" name="IDSocio" value="<?php echo $detalle_reserva["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio"> <?php
                                                                                                                                                                                                else :
                                                                                                                                                                                                    echo $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $detalle_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $detalle_reserva["IDSocio"] . "'");
                                                                                                                                                                                                endif;


                                                                                                                                                                                                    ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Invitados</td>
                                                    <td>
                                                        <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho">
                                                        <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
                                                        <br>
                                                        <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple> <?php
                                                                                                                                        $item = 1;
                                                                                                                                        foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                                                                                                            $item--;

                                                                                                                                            if ($datos_invitado["TipoInvitado"] == "Socio") {
                                                                                                                                                $nombre_socio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_invitado["IDSocioInvitado"] . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_invitado["IDSocioInvitado"] . "'");
                                                                                                                                                $doc_socio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $datos_invitado["IDSocioInvitado"] . "'");
                                                                                                                                            } else {
                                                                                                                                                $nombre_socio = $dbo->getFields("ReservaHotelInvitado", "Nombre", "IDReservaHotelInvitado = '" . $datos_invitado["IDReservaHotelInvitado"] . "'") . "  " . $dbo->getFields("ReservaHotelInvitado", "Apellido", "IDReservaHotelInvitado = '" . $datos_invitado["IDReservaHotelInvitado"] . "'");
                                                                                                                                                $doc_socio = $dbo->getFields("ReservaHotelInvitado", "NumeroDocumento", "IDReservaHotelInvitado = '" . $datos_invitado["IDReservaHotelInvitado"] . "'");
                                                                                                                                            }


                                                                                                                                            if ($datos_invitado["TipoInvitado"] == "Socio") :
                                                                                                                                        ?> <option value="<?php echo "socio-" . $datos_invitado["IDSocioInvitado"]; ?>"><?php echo $nombre_socio .  " Doc: " . $doc_socio; ?></option> <?php
                                                                                                                                                                                                                                                                                    else : ?> <option value="<?php echo "externo-" . $nombre_socio; ?>"><?php echo $nombre_socio .  " Doc: " . $doc_socio; ?></option> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                    endif;
                                                                                                                                                                                                                                                                                                                                                                                                                endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                        ?> </select>
                                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                                    </td>
                                                </tr> <?php //endif; 
                                                        ?> <?php
                                                            //if($id_servicio_maestro==15): //15 = Golf
                                                            if ($invitados == "S" || $id_servicio_maestro > 0 || (SIMUser::get("PermiteCambiarReserva") == "S" || SIMUser::get("IDPerfil") == 0)) : //15 = Golf	 
                                                            ?> <tr>
                                                        <td align="center" colspan="2">
                                                            <input type="hidden" name="action" id="action" value="updateinvitadohotel">
                                                            <input type="hidden" name="IDReservaHotel" id="IDReservaHotel" value="<?php echo $detalle_reserva["IDReserva"]; ?>">
                                                            <input type="hidden" name="IDSocioOrig" id="IDSocioOrig" value="<?php echo $detalle_reserva["IDSocio"]; ?>">
                                                            <input type="submit" name="actualiza_participante_hotel" id="actualiza_participante_hotel" value="Actualizar Invitados / Socio ">
                                                        </td>
                                                    </tr> <?php endif; ?>
                                            </table>
                                        </form> <?php } ?>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
        <?
        include("cmp/footer_scripts.php");
        ?>
        <?
        include("cmp/footer.php");
        ?>
    </div><!-- /.main-container -->
</body>

</html>
