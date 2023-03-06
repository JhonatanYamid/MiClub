<?php

class SIMWebServiceEcaddie
{
    public function get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) :

            // BUSCAMOS QUE EL USUARIO SEA UN CADDIE REGISTRADO
            if ($IDUsuario > 0 || $IDSocio > 0) :
                $IDCaddiesEcaddie = $dbo->getFields("CaddiesEcaddie", "IDCaddiesEcaddie", "IDUsuario = $IDUsuario");
                if ($IDCaddiesEcaddie > 0 || $IDSocio > 0) :
                    $SQLConfiguracion = "SELECT * FROM ConfiguracionCaddies WHERE IDClub = $IDClub AND Publicar = 1";
                    $QRYConfiguracion = $dbo->query($SQLConfiguracion);

                    if ($dbo->rows($QRYConfiguracion) > 0) :
                        $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                        while ($Datos = $dbo->fetchArray($QRYConfiguracion)) :
                            $InfoConfiguracion[IDClub] = $IDClub;
                            $InfoConfiguracion[IDConfiguracionCaddies] = $Datos[IDConfiguracionCaddies];
                            $InfoConfiguracion[CantidadCaddiesEspecial] = $Datos[CantidadCaddiesEspecial];
                            $InfoConfiguracion[ImagenEspera] = CADDIE_ROOT . $Datos[ImagenEspera];
                            $InfoConfiguracion[TextoEspera] = $Datos[TextoEspera];
                            $InfoConfiguracion[LabelExplicacionSeleccionCaddie] = $Datos[LabelExplicacionSeleccionCaddie];
                            $InfoConfiguracion[LabelSeleccionServicio] = $Datos[LabelSeleccionServicio];
                            $InfoConfiguracion[LabelMisReservasCaddies] = $Datos[LabelMisReservasCaddies];
                            $InfoConfiguracion[TextoHeaderBuscadorCaddie] = $Datos[TextoHeaderBuscadorCaddie];
                            $InfoConfiguracion[PermiteSeleccionarClub] = $Datos[PermiteSeleccionarClub];
                            $InfoConfiguracion[LabelCarritoCompra] = $Datos[LabelCarritoCompra];
                            $InfoConfiguracion[MensajeResultadoPagoCompleto] = $Datos[MensajeResultadoPagoCompleto];
                            $InfoConfiguracion[TextoResumen] = $Datos[TextoResumen];
                            $InfoConfiguracion[LabelSeleccionCategoriaCaddie] = $Datos[LabelSeleccionCategoriaCaddie];
                            $InfoConfiguracion[TiempoEsperaSolicitud] = $Datos[TiempoEsperaSolicitud];

                            $InfoConfiguracion[PermiteContactoWhatsapp] = $Datos[PermiteContactoWhatsapp];
                            $InfoConfiguracion[LabelBotonWhatsapp] = $Datos[LabelBotonWhatsapp];
                            //se agrego este parametro para la act de e-caddies
                            $InfoConfiguracion[PermitePagarPrimeroCaddie] = $Datos[PermitePagarPrimeroCaddie];
                            $InfoConfiguracion[OcultarCaddieEspecifico] = $Datos[OcultarCaddieEspecifico];
                            $InfoConfiguracion[OcultarTurboCaddie] = $Datos[OcultarTurboCaddie];
                            $InfoConfiguracion["ValorGeneral"] = $Datos["ValorGeneral"];


                            $Dias = explode(",", $Datos[DiasDisponiblesAgendarEmpleado]);

                            foreach ($Dias as $id => $Dia) :
                                $ResponseDias[] = $Dia;
                            endforeach;

                            $InfoConfiguracion[DiasDisponiblesAgendarEmpleado] = $ResponseDias;
                            $InfoConfiguracion[LabelMiDisponibilidadEmpleado] = $Datos[LabelMiDisponibilidadEmpleado];
                            $InfoConfiguracion[LabelIntroduccionAgendaCaddie] = $Datos[LabelIntroduccionAgendaCaddie];
                        endwhile;

                        $response = $InfoConfiguracion;

                        $respuesta[success] = true;
                        $respuesta[message] = $message;
                        $respuesta[response] = $response;
                    else :
                        $respuesta[success] = false;
                        $respuesta[message] = "No hay configuraciones creadas";
                        $respuesta[response] = "";
                    endif;

                else :
                    $respuesta[success] = false;
                    $respuesta[message] = "EL USUARIO NO TIENE UN CADDIE REGISTRADO.";
                    $respuesta[response] = "";
                endif;
            endif;
        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_clubes_caddies($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $Configuracion = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
            $IDConfiguracionCaddies = $Configuracion[response][IDConfiguracionCaddies];

            $SQLClubes = "SELECT * FROM ClubesCaddies WHERE IDConfiguracionCaddies = $IDConfiguracionCaddies";
            $QRYClubes = $dbo->query($SQLClubes);

            if ($dbo->rows($QRYClubes) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRYClubes)) :

                    $NombreClub = $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = $Datos[IDListaClubes]");
                    $InfoResponse[IDClub] = $Datos[IDListaClubes];
                    $InfoResponse[Nombre] = $NombreClub;

                    array_push($response, $InfoResponse);

                endwhile;

                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;
            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay configuraciones creadas";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_servicios_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClubSeleccion)) :
            $CondicionClub = " AND ClubesAplicaServicio LIKE '%$IDClubSeleccion%'";
        endif;

        if (!empty($IDClub)) :
            $SQLServicios = "SELECT * FROM ServiciosCaddie WHERE IDClub = $IDClub AND Activo = 1 $CondicionClub";
            $QRYServicios = $dbo->query($SQLServicios);

            if ($dbo->rows($QRYServicios) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRYServicios)) :

                    $InfoResponse[IDClub] = $Datos[IDClub];
                    $InfoResponse[IDServicio] = $Datos[IDServiciosCaddie];
                    $InfoResponse[Nombre] = $Datos[Nombre];
                    $InfoResponse[LabelSeleccionarElemento] = $Datos[LabelSeleccionarElemento];
                    $InfoResponse[PermiteCategorias] = $Datos[PermiteCategorias];

                    // CATEGORIAS
                    $Categorias = array();
                    $SQLCategorias = "SELECT * FROM  CategoriasEcaddie WHERE IDServiciosCaddie = $Datos[IDServiciosCaddie]";
                    $QRYCategorias = $dbo->query($SQLCategorias);
                    while ($DatosCategoria = $dbo->fetchArray($QRYCategorias)) :
                        $InfoCategoria[IDCategoria] = $DatosCategoria[IDCategoriasEcaddie];
                        $InfoCategoria[Nombre] = $DatosCategoria[Nombre];
                        array_push($Categorias, $InfoCategoria);
                    endwhile;

                    $InfoResponse[Categorias] = $Categorias;

                    array_push($response, $InfoResponse);

                endwhile;

                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay datos";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_fechas_disponibles_servicio_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $IDServicio)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDServicio)) :

            $SQLServicio = "SELECT * FROM ServiciosCaddie WHERE IDServiciosCaddie = $IDServicio";
            $QRYServicio = $dbo->query($SQLServicio);

            if ($dbo->rows($QRYServicio) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                while ($Datos = $dbo->fetchArray($QRYServicio)) :

                    $DiasDespues = $Datos[NumeroDiasMostrar];
                    $FechaInicio = date("Y-m-d");
                    $FechaFin = date("Y-m-d", strtotime('+' . $DiasDespues . 'day', strtotime($FechaInicio)));
                    $Inicio = strtotime($FechaInicio);
                    $Fin = strtotime($FechaFin);

                    for ($i = $Inicio; $i <= $Fin; $i += 86400) :

                        $FechaResponde = date("Y-m-d", $i);
                        $Fechas[Fecha] = $FechaResponde;

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $Fechas[GMT] = SIMWebservice::timezone_offset_string($offset);

                        array_push($response, $Fechas);
                    endfor;
                endwhile;

                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay datos";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_disponibilidad_elemento_servicio_caddie($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $IDServicio, $Fecha)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDServicio) && !empty($Fecha)) :

            $Dia = date("w", strtotime($Fecha));
            $HoraActual = strtotime(date("H:i:s"));
            $FechaHoy = date("Y-m-d");

            $SQLDisponibilidad = "SELECT * FROM  DisponibilidadServiciosCaddies WHERE IDServiciosCaddie = $IDServicio AND Dias LIKE '%$Dia%'";
            $QRYDisponibilidad = $dbo->query($SQLDisponibilidad);

            if ($dbo->rows($QRYDisponibilidad) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                while ($Datos = $dbo->fetchArray($QRYDisponibilidad)) :

                    $Intervalo = $Datos[Intervalo];
                    $Medicion = $Datos[MedicionIntervalo];

                    switch ($Medicion):

                        case "Dias":
                            $Minutos = (60 * 24) * $Intervalo;
                            break;

                        case "Horas":
                            $Minutos = 60 * $Intervalo;
                            break;

                        case "Minutos":
                            $Minutos = $Intervalo;
                            break;

                        default:
                            $Minutos = 0;

                    endswitch;

                    $HoraDesde = strtotime($Datos[HoraDesde]);
                    $HoraHasta = strtotime($Datos[HoraHasta]);

                    while ($HoraDesde <= $HoraHasta) :

                        if ($Fecha != $FechaHoy || $HoraDesde > $HoraActual) :
                            $InfoResponse[Hora] = date("H:i:s", $HoraDesde);

                            $zonahoraria = date_default_timezone_get();
                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                            $InfoResponse[GMT] = SIMWebservice::timezone_offset_string($offset);

                            $InfoResponse[IDDisponibilidad] = $Datos[IDDisponibilidadServiciosCaddies];
                            $InfoResponse[IDElemento] = $Datos[IDElementoServiciosCaddies];

                            $NombreElemento = $dbo->getFields("ElementoServiciosCaddies", "Nombre", "IDElementoServiciosCaddies = $Datos[IDElementoServiciosCaddies]");

                            $InfoResponse[NombreElemento] = $NombreElemento;
                            array_push($response, $InfoResponse);

                        endif;
                        $HoraDesde = strtotime('+' . $Minutos . 'minutes', $HoraDesde);

                    endwhile;

                endwhile;

                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay datos";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_caddies_disponibles_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $Tag, $Fecha, $IDServicio, $Hora, $IDElemento, $IDCategoria)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($Fecha) && !empty($Hora) && !empty($IDElemento)) :

            if (!empty($Tag)) :
                $CondicionTag = " AND Nombre LIKE '%$Tag%'";
            endif;

            if (!empty($IDCategoria)) :
                $CondicionCategoria = " AND  IDCategoriasEcaddie = '$IDCategoria'";
            endif;

            // BUSCAMOS LAS DISPONIBILIDADES DE LOS CADDIES
            $Dia = date("w", strtotime($Fecha));
            $SQLDisponibilidades = "SELECT IDCaddiesEcaddie FROM DisponibilidiadCaddiesEcaddie WHERE Dias LIKE '%$Dia%' AND (HoraDesde <= '$Hora' AND HoraHasta >= '$Hora') AND (FechaDesde <= '$Fecha' AND FechaHasta >= '$Fecha') AND IDClubSeleccion = '$IDClubSeleccion' GROUP BY IDCaddiesEcaddie";
            $QRYDisponibilidades = $dbo->query($SQLDisponibilidades);

            if ($dbo->rows($QRYDisponibilidades) > 0) :

                while ($DatosDisponibilidades = $dbo->fetchArray($QRYDisponibilidades)) :
                    $ArrayCaddies[] = $DatosDisponibilidades[IDCaddiesEcaddie];
                endwhile;

                $CaddiesBuscar = implode(",", $ArrayCaddies);

                $SQLDisponibilidadCaddies = "SELECT * FROM CaddiesEcaddie WHERE IDClub = $IDClub AND IDServiciosCaddie = $IDServicio AND IDCaddiesEcaddie IN ($CaddiesBuscar) $CondicionTag $CondicionCategoria";
                $QRYDisponibilidadCaddies = $dbo->query($SQLDisponibilidadCaddies);

                if ($dbo->rows($QRYDisponibilidadCaddies) > 0) :
                    $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    while ($Datos = $dbo->fetchArray($QRYDisponibilidadCaddies)) :

                        if ($Datos[IDCategoriasEcaddie] > 0) :
                            $Categoria = $dbo->getFields("CategoriasEcaddie", "Nombre", "IDCategoriasEcaddie = $Datos[IDCategoriasEcaddie]");
                            $Categoria = "Categoria Caddie: $Categoria";
                        endif;

                        $InfoResponse[IDCaddie] = $Datos[IDCaddiesEcaddie];
                        $InfoResponse[Nombre] = $Datos[Nombre];
                        $InfoResponse[Categoria] = $Datos[Categoria];
                        $InfoResponse[Texto] = $Datos[Descripcion] . "\n" . $Categoria;
                        $InfoResponse[IDUsuario] = $Datos[IDUsuario];

                        array_push($response, $InfoResponse);
                    endwhile;

                    $respuesta[success] = true;
                    $respuesta[message] = $message;
                    $respuesta[response] = $response;

                else :
                    $respuesta[success] = false;
                    $respuesta[message] = "No hay datos";
                    $respuesta[response] = "";
                endif;
            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay disponibilidades de caddies para esta fecha y hora";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_solicitar_caddie_rappi($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $Fecha, $IDServicio, $Hora, $IDElemento, $TipoCaddie, $OpcionesCaddies, $IDCategoria)
    {

        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($Fecha) && !empty($Hora) && !empty($TipoCaddie)) :

            $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

            $Configuracion = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
            $PrimeroPagar = $Configuracion["response"]["PermitePagarPrimeroCaddie"];
            if ($PrimeroPagar == "S") {
                $ValorPagar = $Configuracion["response"]["ValorGeneral"];
                $EstadoSolicitud = "RevisandoPago";
            } else {
                $ValorPagar = 0;
                $EstadoSolicitud = "EsperandoCaddie";
            }


            if (trim($TipoCaddie) == 'Especial') :
                $Caddies = json_decode($OpcionesCaddies, true);
                if (count($Caddies) <= 0) :
                    $respuesta[success] = false;
                    $respuesta[message] = "Debe seleccionar los caddies que quiere especificamente $OpcionesCaddies";
                    $respuesta[response] = $OpcionesCaddies;
                    return $respuesta;
                endif;

                $Mensaje = "Hay un nuevo turno para ser tomado\nFecha: $Fecha\nHora: $Hora.";
            elseif (trim($TipoCaddie) == 'Turbo') :

                $respuesta[success] = false;
                $respuesta[message] = "La opción Tubo caddy no se encuentra disponible para este evento.";
                $respuesta[response] = $OpcionesCaddies;
                return $respuesta;

                $Caddies = array();
                $SQLTurbos = "SELECT * FROM CaddiesEcaddie WHERE TurboCaddieActivado = 'S'";
                $QRYTurbos = $dbo->query($SQLTurbos);
                while ($DatosCaddie = $dbo->fetchArray($QRYTurbos)) :
                    $InfoCaddie[IDCaddies] = $DatosCaddie[IDCaddiesEcaddie];
                    array_push($Caddies, $InfoCaddie);
                endwhile;

                $Mensaje = "Un Socio necesita un Turbo Caddie.";

            else :
                // SI ES ALEATORIO BUSCAMOS LOS CADDIES QUE ESTAN DISPONIBLES EN ESE HORARIOS
                $CaddiesDisponibles = SIMWebServiceEcaddie::get_caddies_disponibles_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, "", $Fecha, $IDServicio, $Hora, $IDElemento, $IDCategoria);
                $Caddies = $CaddiesDisponibles[response];
                $Mensaje = "Hay un nuevo turno para ser tomado\nFecha: $Fecha\nHora: $Hora.";

            endif;



            $SETSolicitud = "INSERT INTO SolicitudCaddieRappi (IDSocio, IDClub, IDClubSeleccion,IDServiciosCaddie,IDElementoServiciosCaddies,EstadoSolicitud, Fecha, Hora, TipoCaddie, Opciones, Valor, FechaTrCr,UsuarioTrCr) 
                                    VALUES ('$IDSocio','$IDClub','$IDClubSeleccion','$IDServicio','$IDElemento','$EstadoSolicitud','$Fecha','$Hora','$TipoCaddie','$OpcionesCaddies','" . $ValorPagar . "',NOW(),'SOCIO-$IDSocio')";
            $dbo->query($SETSolicitud);
            $IDSolicitudCaddieRappi = $dbo->lastID();

            if (trim($TipoCaddie) == 'Especial') :
                foreach ($Caddies as $id => $Caddie) :
                    // INSERTAMOS LA SOLICITUD ESPECIAL PARA SOLO MOSTRARLA AL CADDIE
                    $Insert = "INSERT INTO SolicitudEspecialCaddie (IDSolicitudCaddieRappi,IDCaddiesEcaddie) VALUES ('$IDSolicitudCaddieRappi','$Caddie[IDCaddie]')";
                    $dbo->query($Insert);
                endforeach;
            endif;

            // ENVIAMOS NOTIFICACION AL CADDIE O CADDIES         

            $IDSocioNotifica = 0;
            $EnviaLink = 'S';
            $VerReservas = 'N';
            $Titular = "Nueva reserva disponible para ser tomada";
            $FechaInicio = date("Y-m-d");
            $FechaFin = $Fecha;
            $DiaReserva = date("w", strtotime($Fecha)) . "|";
            $Cantidad = count($Caddies);
            $IDSolicitud = $IDSolicitudCaddieRappi;

            foreach ($Caddies as $id => $Caddie) :

                /*  // VERIFICAMOS QUE EL CADDIE NO TENGA UNA RESERVA PARA ESE DIA Y FECHA
                $SQLReservas = "SELECT IDSolicitudCaddieRappi FROM SolicitudCaddieRappi WHERE Fecha = '$Fecha' AND Hora = '$Hora' AND IDCaddiesEcaddie = $Caddie[IDCaddie]";
                $QRYReservas = $dbo->query($SQLReservas);

                if($dbo->rows($QRYReservas) <= 0): */
                $IDUsuario = $dbo->getFields("CaddiesEcaddie", "IDUsuario", "IDCaddiesEcaddie = $Caddie[IDCaddie]");
                SIMWebServiceEcaddie::EnviarNotificacion($IDClub, $IDSocioNotifica, $IDUsuario, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud);
            /* endif;  */

            endforeach;


            $response[IDSolicitudCaddieRappi] = $IDSolicitudCaddieRappi;
            $response[Caddies] = $Caddies;

            if ($IDSolicitudCaddieRappi > 0) :
                $respuesta["message"] = "Solicitud creada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else :
                $respuesta["message"] = "Error Solicitud no creada";
                $respuesta["success"] = false;
                $respuesta["response"] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros. set_solicitar_caddie_rappi Fecha: $Fecha, Hora: $Hora, TipoCaddie: $TipoCaddie";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_estado_solicitud_caddie_rappi($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();
        $FechaHoraActual = date("Y-m-d H:i:s");


        $Configuracion = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
        $TiempoEsperaSolicitud = $Configuracion[response][TiempoEsperaSolicitud];

        if (!empty($IDClub)) :

            $SQLSolicitudes = "SELECT * FROM SolicitudCaddieRappi WHERE IDSocio = $IDSocio ORDER BY IDSolicitudCaddieRappi DESC LIMIT 1";
            $QRYSolicitudes = $dbo->query($SQLSolicitudes);

            if ($dbo->rows($QRYSolicitudes) > 0) :

                // ARMAMOS EL ARREGLO DE SOLICITUDES Y DESPUES LAS ACTUALIZAMOS
                while ($DatosSolicitud = $dbo->fetchArray($QRYSolicitudes)) :

                    $InfoResponse[IDSolicitud] = $DatosSolicitud[IDSolicitudCaddieRappi];
                    $InfoResponse[Estado] = $DatosSolicitud[EstadoSolicitud];

                    $FechaHoraReserva = strtotime($DatosSolicitud[FechaTrCr]);
                    $Mas20Minutos = date("Y-m-d H:i:s", strtotime("+$TiempoEsperaSolicitud minute", $FechaHoraReserva));

                    if ($Mas20Minutos < $FechaHoraActual) :
                        $update = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'Expirada' WHERE IDSolicitudCaddieRappi = $DatosSolicitud[IDSolicitudCaddieRappi]";
                        $dbo->query($update);
                    endif;


                    $PrimeroPagar = $Configuracion["response"]["PermitePagarPrimeroCaddie"];
                    if ($PrimeroPagar == "S" && $DatosSolicitud["Pagado"] == "N") {
                        $success = true;
                        $message = "Estamos procesando tu solicitud de pago";
                    } else {
                        if ($DatosSolicitud[EstadoSolicitud] == 'PendientePago') :
                            $success = true;
                            $message = "Solicitud pendiente de pago";
                        elseif ($DatosSolicitud[EstadoSolicitud] == 'Confirmada') :
                            $success = true;
                            $message = "Solicitud Confirmada";
                        elseif ($DatosSolicitud[EstadoSolicitud] == 'Expirada') :
                            $success = true;
                            $message = "Solicitud Vencida, intente de nuevo";
                        elseif ($DatosSolicitud[EstadoSolicitud] == 'Cancelada') :
                            $success = true;
                            $message = "Solicitud Cancelada";
                        else :
                            $success = true;
                            $message = "Esperando Caddie";
                        endif;
                    }

                    array_push($response, $InfoResponse);

                endwhile;

                $respuesta[success] = $success;
                $respuesta[message] = $message;
                $respuesta[response] = $response;

            else :

                $InfoResponse[IDSolicitud] = "";
                $InfoResponse[Estado] = "SinSolicitud";

                array_push($response, $InfoResponse);

                $respuesta[success] = true;
                $respuesta[message] = "No hay solicitudes";
                $respuesta[response] = $response;
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_mis_reservas_caddies_socio($IDClub, $IDSocio, $IDUsuario, $IDSolicitud)
    {
        $dbo = SIMDB::get();
        $response = array();
        $Hoy = date("Y-m-d");


        if (!empty($IDClub) && !empty($IDSocio)) :

            $Configuracion = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
            $MensajeResultadoPagoCompleto = $Configuracion[response][MensajeResultadoPagoCompleto];
            $IDConfiguracionCaddies = $Configuracion[response][IDConfiguracionCaddies];
            $TextoResumen = $Configuracion[response][TextoResumen];

            $PermiteContactoWhatsapp = $Configuracion[response][PermiteContactoWhatsapp];
            $LabelBotonWhatsapp = $Configuracion[response][LabelBotonWhatsapp];

            $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub", "array");
            $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");

            if (!empty($IDSolicitud)) :
                $CondicionSolicitud = " AND IDSolicitudCaddieRappi = $IDSolicitud";
            endif;

            $SQLSolicitudes = "SELECT * FROM SolicitudCaddieRappi WHERE IDSocio = $IDSocio AND Fecha >= '$Hoy' $CondicionSolicitud ORDER BY IDSolicitudCaddieRappi DESC";
            $QRYSolicitudes = $dbo->query($SQLSolicitudes);

            if ($dbo->rows($QRYSolicitudes) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                // DATOS SERVICIOS
                $SQLServicios = "SELECT IDServiciosCaddie, Nombre FROM ServiciosCaddie WHERE IDClub = $IDClub";
                $QRYServicios = $dbo->query($SQLServicios);

                while ($DatosServicio = $dbo->fetchArray($QRYServicios)) :
                    $datos_servicio[$DatosServicio[IDServiciosCaddie]] = $DatosServicio;
                endwhile;

                // DATOS CADDIES
                $SQLCaddies = "SELECT IDCaddiesEcaddie, Nombre, Valor, NumeroCelular FROM CaddiesEcaddie WHERE IDClub = $IDClub";
                $QRYCaddies = $dbo->query($SQLCaddies);

                while ($DatosCaddies = $dbo->fetchArray($QRYCaddies)) :
                    $datos_caddie[$DatosCaddies[IDCaddiesEcaddie]] = $DatosCaddies;
                endwhile;

                // DATOS ESTADOS
                $SQLEstadoSolicitud = "SELECT * FROM EstadoSolicitudCaddie WHERE 1";
                $QRYEstadoSolicitud = $dbo->query($SQLEstadoSolicitud);

                while ($DatosEstadoSolicitud = $dbo->fetchArray($QRYEstadoSolicitud)) :
                    $datos_estados[$DatosEstadoSolicitud[Nombre]] = $DatosEstadoSolicitud[NombrePublico];
                endwhile;

                while ($Datos = $dbo->fetchArray($QRYSolicitudes)) :

                    if ($Datos[EstadoSolicitud] == "Cancelada") :
                        $Cancelado = explode("-", "$Datos[UsuarioTrEd]");
                        if ($Cancelado[0] == 'SOCIO') :
                            $EstadoSolicitud = "Cancelado por Socio";
                        else :
                            $EstadoSolicitud = "Cancelado por Caddie";
                        endif;
                    else :
                        $EstadoSolicitud =  $datos_estados[$Datos[EstadoSolicitud]];
                    endif;

                    $NombreServicio = $datos_servicio[$Datos[IDServiciosCaddie]][Nombre];

                    // DATOS ELEMENTOS
                    $SQLElementos = "SELECT Nombre FROM ElementoServiciosCaddies WHERE IDElementoServiciosCaddies = $Datos[IDElementoServiciosCaddies]";
                    $QRYElementos = $dbo->query($SQLElementos);
                    $DatosElementos = $dbo->fetchArray($QRYElementos);

                    $NombreElemento = $DatosElementos[Nombre];
                    $NombreCaddie = $datos_caddie[$Datos[IDCaddiesEcaddie]][Nombre];
                    $NumeroWhatsapp = $datos_caddie[$Datos[IDCaddiesEcaddie]][NumeroCelular];


                    $InfoResponse[IDSolicitud] = $Datos[IDSolicitudCaddieRappi];
                    $InfoResponse[IDServicio] = $Datos[IDServiciosCaddie];
                    //LA IMAGEN DEL E-CADDIE


                    $sql_caddie = "SELECT * FROM `CaddiesEcaddie` WHERE IDCaddiesEcaddie=$Datos[IDCaddiesEcaddie]";
                    $img = $dbo->query($sql_caddie);
                    $datos_caddie = $dbo->fetchArray($img);

                    $Usuario = $datos_caddie["IDUsuario"];
                    $sql_usuario = "SELECT * FROM `Usuario` WHERE IDUsuario=$Usuario";
                    $usuario = $dbo->query($sql_usuario);
                    $datos_usuario = $dbo->fetchArray($usuario);
                    $foto = USUARIO_ROOT . $datos_usuario["Foto"];

                    $InfoResponse[FotoCaddie] = $foto;

                    $InfoResponse[NombreServicio] = $NombreServicio;
                    $InfoResponse[IDElemento] = $Datos[IDElementoServiciosCaddies];
                    $InfoResponse[NombreElemento] = $NombreElemento;
                    $InfoResponse[Fecha] = $Datos[Fecha];
                    $InfoResponse[Hora] = $Datos[Hora];
                    $InfoResponse[NombreCaddie] = $NombreCaddie;
                    $InfoResponse[TipoCaddie] = $Datos[TipoCaddie];
                    $InfoResponse[TextoResumen] = $TextoResumen;
                    $InfoResponse[IDClub] = $Datos[IDClubSeleccion];
                    $InfoResponse[Estado] = $Datos[EstadoSolicitud];
                    $InfoResponse[EstadoText] = $EstadoSolicitud;

                    $InfoResponse[PermiteContactoWhatsapp] = $PermiteContactoWhatsapp;
                    $InfoResponse[NumeroWhatsapp] = $NumeroWhatsapp;
                    $InfoResponse[LabelBotonWhatsapp] = $LabelBotonWhatsapp;

                    if ($Datos[EstadoSolicitud] == 'PendientePago' || $Datos[EstadoSolicitud] == 'RevisandoPago') :

                        $InfoResponse[MensajeResultadoPagoCompleto] = $MensajeResultadoPagoCompleto;
                        $Valor = number_format((float) $Datos[Valor], 1, ",", ".");
                        $InfoResponse[Valor] = $Datos[Valor];
                        $InfoResponse[ValorPagoTexto] = $ValorPagoTexto = $datos_club_otros["SignoPago"] . " " . $Valor . " " . $datos_club_otros["TextoPago"];
                        $InfoResponse[Action] = "";

                        $moneda = "COP";
                        $refVenta = time();
                        $llave_encripcion = $datos_club["ApiKey"];
                        $usuarioId = $datos_club["MerchantId"];
                        $accountId = (string) $datos_club["AccountId"];
                        $descripcion = "Pago Ecaddie App Mi Club " . $datos_club[Nombre];
                        // $extra1 = $IDSocioTalonera;
                        $extra1 = $Datos['IDSolicitudCaddieRappi'];
                        $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda";
                        $firma = md5($firma_cadena);

                        $emailSocio = $datos_persona["CorreoElectronico"];
                        if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                            $emailComprador = $emailSocio;
                        } else {
                            $emailComprador = "";
                        }

                        // ESTA URLS SE VUELVEN DINAMICAS DEPENDIENDO DE LA MODALIDAD DE PAGO; SE PUEDEN VER EN LOS PAGOS SIMPasarelaPagos.inc.php
                        $url_respuesta = URLROOT . "respuesta_transaccion.php";
                        $url_confirmacion = URLROOT . "confirmacion_pagos.php";

                        $response_parametros = array();
                        $datos_post["llave"] = "moneda";
                        $datos_post["valor"] = (string) $moneda;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "ref";
                        $datos_post["valor"] = $refVenta;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "llave";
                        $datos_post["valor"] = $llave_encripcion;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "userid";
                        $datos_post["valor"] = $usuarioId;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "usuarioId";
                        $datos_post["valor"] = $usuarioId;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "accountId";
                        $datos_post["valor"] = $accountId;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "descripcion";
                        $datos_post["valor"] = $descripcion;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "extra1";
                        $datos_post["valor"] = (string) $extra1;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "extra2";
                        $datos_post["valor"] = $IDClub;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "refVenta";
                        $datos_post["valor"] = $refVenta;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "valor";
                        $datos_post["valor"] = (string) $Datos["Valor"];
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "iva";
                        $datos_post["valor"] = "0";
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "baseDevolucionIva";
                        $datos_post["valor"] = "0";
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "firma";
                        $datos_post["valor"] = $firma;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "emailComprador";
                        $datos_post["valor"] = $emailComprador;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "prueba";
                        $datos_post["valor"] = (string) $datos_club["IsTest"];
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "url_respuesta";
                        $datos_post["valor"] = (string) $url_respuesta;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "url_confirmacion";
                        $datos_post["valor"] = (string) $url_confirmacion;
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "Modulo";
                        $datos_post["valor"] = (string) "Ecaddie";
                        array_push($response_parametros, $datos_post);

                        $datos_post["llave"] = "IDSocio";
                        $datos_post["valor"] = $IDSocio;
                        array_push($response_parametros, $datos_post);

                        $InfoResponse[ParametrosPost] = $response_parametros;

                        //PAGO CON CREDIBANCO VERSION VIEJA, NO SE QUITA POR PREVENCIÓN
                        $datos_post_pago = array();
                        $datos_post_pago["iva"] = 0;
                        $datos_post_pago["purchaseCode"] = $refVenta;
                        $datos_post_pago["totalAmount"] = $ValorPagado * 100;
                        $datos_post_pago["ipAddress"] = SIMUtil::get_IP();

                        $InfoResponse[ParametrosPaGo] = $datos_post_pago;
                        //FIN PAGO

                        // SACAMOS LOS TIPO PAGO DE LA CONFIGURACIÓN GENERAL DE LAS TALONERAS
                        $TipoPago = array();
                        $SQlTipoPago = "SELECT * FROM ConfiguracionCaddiesTipoPago CTP, TipoPago TP  WHERE CTP.IDTipoPago = TP.IDTipoPago and IDConfiguracionCaddies = '$IDConfiguracionCaddies'";
                        $QRYTipoPago = $dbo->query($SQlTipoPago);
                        if ($dbo->rows($QRYTipoPago) > 0) :
                            while ($DatosTipoPago = $dbo->fetchArray($QRYTipoPago)) :

                                $InfoTipoPago["IDClub"] = $IDClub;
                                $InfoTipoPago["IDServicio"] = $Datos[IDServiciosCaddie];
                                $InfoTipoPago["IDTipoPago"] = $DatosTipoPago["IDTipoPago"];
                                $InfoTipoPago["PasarelaPago"] = $DatosTipoPago["PasarelaPago"];
                                $InfoTipoPago["Action"] = SIMUtil::obtener_accion_pasarela($DatosTipoPago["IDTipoPago"], $IDClub);
                                $InfoTipoPago["Nombre"] = $DatosTipoPago["Nombre"];
                                $InfoTipoPago["PaGoCredibanco"] = $DatosTipoPago["PaGoCredibanco"];

                                switch ($DatosTipoPago["IDTipoPago"]):

                                    case "1":
                                        $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                        break;

                                    case "2":
                                        $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                        break;

                                    case "3":
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;

                                    case "11":
                                        $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                        break;

                                    default:
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;

                                endswitch;

                                $InfoTipoPago["Imagen"] = $imagen;

                                array_push($TipoPago, $InfoTipoPago);

                            endwhile;
                        endif;

                        $InfoResponse[TipoPago] = $TipoPago; //ARREGLO DE TIPOS DE PAGO DE eCaddie                     

                    endif;

                    array_push($response, $InfoResponse);
                endwhile;

                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay datos";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_mis_reservas_caddies_empleado($IDClub, $IDSocio, $IDUsuario, $IDSolicitud)
    {
        $dbo = SIMDB::get();
        $response = array();
        $Hoy = date("Y-m-d");

        if (!empty($IDClub) && !empty($IDUsuario)) :
            // BUSCAMOS QUE EL USUARIO TENGA UN CADDIE ASOCIOADO
            $DatosCaddie = $dbo->fetchAll("CaddiesEcaddie", "IDUsuario = $IDUsuario ORDER BY IDCaddiesEcaddie DESC LIMIT 1");

            $IDCaddiesEcaddie = $DatosCaddie[IDCaddiesEcaddie];

            if ($IDCaddiesEcaddie > 0) :

                $Configuracion = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
                $MensajeResultadoPagoCompleto = $Configuracion[response][MensajeResultadoPagoCompleto];
                $IDConfiguracionCaddies = $Configuracion[response][IDConfiguracionCaddies];
                $TextoResumen = $Configuracion[response][TextoResumen];
                $PermiteContactoWhatsapp = $Configuracion[response][PermiteContactoWhatsapp];
                $LabelBotonWhatsapp = $Configuracion[response][LabelBotonWhatsapp];

                $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub", "array");
                $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");

                if (!empty($IDSolicitud)) :
                    $CondicionSolicitud = " AND IDSolicitudCaddieRappi = $IDSolicitud";
                endif;

                // NO MOSTRAMOS LAS QUE EL CADDIE RECHAZO
                $SQLRechazadas = "SELECT * FROM SolicitudRechazaCaddie WHERE IDCaddiesEcaddie = $IDCaddiesEcaddie";
                $QRYRechazadas = $dbo->query($SQLRechazadas);

                if ($dbo->rows($QRYRechazadas)) :
                    while ($Rechazada = $dbo->fetchArray($QRYRechazadas)) :
                        $ArrayRechazadas[] = $Rechazada[IDSolicitudCaddieRappi];
                    endwhile;

                    $SolicitudesRechazadas = implode(",", $ArrayRechazadas);
                    $CondicionRechazadas = " AND IDSolicitudCaddieRappi NOT IN ($SolicitudesRechazadas)";
                endif;


                $SQLSolicitudes = "SELECT * FROM SolicitudCaddieRappi WHERE (IDCaddiesEcaddie = $IDCaddiesEcaddie OR (IDCaddiesEcaddie = 0 AND EstadoSolicitud != 'Cancelada' )) AND Fecha >= '$Hoy' $CondicionSolicitud $CondicionRechazadas ORDER BY IDSolicitudCaddieRappi DESC";
                $QRYSolicitudes = $dbo->query($SQLSolicitudes);

                if ($dbo->rows($QRYSolicitudes) > 0) :
                    $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    // DATOS SERVICIOS
                    $SQLServicios = "SELECT IDServiciosCaddie, Nombre FROM ServiciosCaddie WHERE IDClub = $IDClub";
                    $QRYServicios = $dbo->query($SQLServicios);

                    while ($DatosServicio = $dbo->fetchArray($QRYServicios)) :
                        $datos_servicio[$DatosServicio[IDServiciosCaddie]] = $DatosServicio;
                    endwhile;

                    // DATOS ESTADOS
                    $SQLEstadoSolicitud = "SELECT * FROM EstadoSolicitudCaddie WHERE 1";
                    $QRYEstadoSolicitud = $dbo->query($SQLEstadoSolicitud);

                    while ($DatosEstadoSolicitud = $dbo->fetchArray($QRYEstadoSolicitud)) :
                        $datos_estados[$DatosEstadoSolicitud[Nombre]] = $DatosEstadoSolicitud[NombrePublico];
                    endwhile;

                    while ($Datos = $dbo->fetchArray($QRYSolicitudes)) :

                        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocio]");

                        $Mostrar = 1; //MOSTRAMOS SIMEPRE LA SOLICITUD

                        if ($Datos[EstadoSolicitud] == "Cancelada") :
                            $Cancelado = explode("-", "$Datos[UsuarioTrEd]");
                            if ($Cancelado[0] == 'SOCIO') :
                                $EstadoSolicitud = "Cancelado por Socio";
                            else :
                                $EstadoSolicitud = "Cancelado por Caddie";
                            endif;
                        elseif ($Datos[EstadoSolicitud] == "PendientePago") :
                            $EstadoSolicitud = 'En proceso';
                        else :
                            $EstadoSolicitud =  $datos_estados[$Datos[EstadoSolicitud]];
                        endif;

                        $NombreServicio = $datos_servicio[$Datos[IDServiciosCaddie]][Nombre];

                        // DATOS ELEMENTOS
                        $SQLElementos = "SELECT Nombre FROM ElementoServiciosCaddies WHERE IDElementoServiciosCaddies = $Datos[IDElementoServiciosCaddies]";
                        $QRYElementos = $dbo->query($SQLElementos);
                        $DatosElementos = $dbo->fetchArray($QRYElementos);

                        $NombreElemento = $DatosElementos[Nombre];
                        $Nombre = $DatosSocio[Nombre] . " " . $DatosSocio[Apellido];
                        $NumeroWhatsapp = $DatosSocio[Celular];

                        $InfoResponse[IDSolicitud] = $Datos[IDSolicitudCaddieRappi];
                        $InfoResponse[IDServicio] = $Datos[IDServiciosCaddie];
                        $InfoResponse[NombreServicio] = $NombreServicio;
                        $InfoResponse[IDElemento] = $Datos[IDElementoServiciosCaddies];
                        $InfoResponse[NombreElemento] = $NombreElemento;
                        $InfoResponse[Fecha] = $Datos[Fecha];
                        $InfoResponse[Hora] = $Datos[Hora];
                        $InfoResponse[NombreCaddie] = $Nombre;
                        $InfoResponse[TipoCaddie] = $Datos[TipoCaddie];
                        $InfoResponse[TextoResumen] = $TextoResumen;
                        $InfoResponse[IDClub] = $Datos[IDClubSeleccion];
                        $InfoResponse[Estado] = $Datos[EstadoSolicitud];
                        $InfoResponse[EstadoText] = $EstadoSolicitud;

                        $InfoResponse[PermiteContactoWhatsapp] = $PermiteContactoWhatsapp;
                        $InfoResponse[NumeroWhatsapp] = $NumeroWhatsapp;
                        $InfoResponse[LabelBotonWhatsapp] = $LabelBotonWhatsapp;

                        // SOLICITUDES ESPECIALES SOLO A ESOS CADDIES

                        if ($Datos[TipoCaddie] == 'Especial') :
                            $Opciones = $Datos[Opciones];
                            $Mostrar = 0;
                            $Caddies = json_decode($Opciones, true);
                            foreach ($Caddies as $id => $Caddie) :
                                if ($Caddie[IDCaddie] == $IDCaddiesEcaddie) :
                                    $Mostrar = 1;
                                    break;
                                endif;
                            endforeach;
                        endif;

                        if ($Mostrar == 1)
                            array_push($response, $InfoResponse);

                    endwhile;

                    $respuesta[success] = true;
                    $respuesta[message] = $message;
                    $respuesta[response] = $response;

                else :
                    $respuesta[success] = false;
                    $respuesta[message] = "NO TIENE SOLICITUDES EN ESTE MOMENTO";
                    $respuesta[response] = null;
                endif;

            else :
                $respuesta["message"] = "EL USUARIO NO TIENE UN CADDIE ASOCIADO.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_tipo_pago_caddie_reserva($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $IDTipoPago, $CodigoPago)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSolicitud) && !empty($IDTipoPago)) :

            $DatosSolicitud = $dbo->fetchAll("SolicitudCaddieRappi", "IDSolicitudCaddieRappi = $IDSolicitud");

            if (count($DatosSolicitud) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $datos_codigo = $dbo->fetchAll("ClubCodigoPago", "Codigo = '$CodigoPago' and IDClub = '$IDClub'");

                    $id_codigo = $datos_codigo[IDClubCodigoPago];
                    $codigo_disponible = $datos_codigo[Disponible];
                    $valorCodigo = $datos_codigo[Valor];

                    if (empty($id_codigo)) :

                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    elseif ($codigo_disponible != "S") :
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :
                        $sql_actualiza_codigo = "UPDATE ClubCodigoPago SET Disponible = 'N', IDSocio = '$IDSocio'  WHERE   Codigo = '$CodigoPago' AND IDClub = '$IDClub'";
                        $dbo->query($sql_actualiza_codigo);
                    endif;

                endif;

                if ($IDSocio > 0 && $datos_persona["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $Actualiza = "";
                $Pagado = 'N';

                if ($IDTipoPago == 3 || $IDTipoPago == 23) :
                    // ACTULIZAMOS A PAGADA
                    $Actualiza = ", EstadoSolicitud = 'Confirmada'";
                    $Pagado = 'S';
                endif;

                $sql_tipo_pago = "UPDATE SolicitudCaddieRappi SET IDTipoPago =  '$IDTipoPago', CodigoPago = '$CodigoPago', Pagado = '$Pagado' $Actualiza WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Pago de Caddie registrado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "La solicitud no existe";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_eliminar_caddie_reserva($IDClub, $IDSocio, $IDUsuario, $IDSolicitud)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDSolicitud)) :

            // CONSULTAMOS LA SOLICITUD
            $DatosSolicitud = $dbo->fetchAll("SolicitudCaddieRappi", "IDSolicitudCaddieRappi = $IDSolicitud");

            if (count($DatosSolicitud) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                $FechaHoraReserva = $DatosSolicitud[Fecha] . " " . $DatosSolicitud[Hora];
                $FechaActual = date("Y-m-d H:i:s");
                $Hoy = date("Y-m-d");

                if ($FechaActual < $FechaHoraReserva) :

                    $UPDATE = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'Cancelada', FechaCancelacion = NOW(), UsuarioCancelacion = 'SOCIO-$IDSocio', FechaTrEd = NOW(), UsuarioTrEd = 'SOCIO-$IDSocio' WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                    $dbo->query($UPDATE);
                else :
                    $respuesta[success] = false;
                    $respuesta[message] = "La hora de la reserva ya paso y no se puede cancelar";
                    $respuesta[response] = null;

                    return $respuesta;
                endif;

                if ($DatosSolicitud[EstadoSolicitud] == 'Confirmada') :
                    $DoceHorasDespues = date("Y-m-d H:i:s", strtotime('+12 hour', strtotime($FechaActual)));
                    $UnaHorasDespues = date("Y-m-d H:i:s", strtotime('+1 hour', strtotime($FechaActual)));

                    $CaddieIngreso = 0;

                    // BUSCAMOS EL USAURIO DEL CADDIE
                    $IDUsuarioCaddie = $dbo->getFields("CaddiesEcaddie", "IDUsuario", "IDCaddiesEcaddie = $DatosSolicitud[IDCaddiesEcaddie]");

                    $SQLEntra = "SELECT * FROM LogAcceso WHERE IDInvitacion = $IDUsuarioCaddie AND Entrada = 'S' AND DATE_FORMAT(FechaIngreso, '%Y-%m-%d') = '$Hoy'";
                    $QRYEntra = $dbo->query($SQLEntra);

                    if ($dbo->rows($QRYEntra) > 0) :
                        $CaddieIngreso = 1;
                    endif;

                    $GeneraCodigo = 0;
                    if ($DoceHorasDespues > $FechaHoraReserva) :
                        $GeneraCodigo = 1;
                    else :
                        $msg_respuesta = "No se genero el codigo ya que se cancelo antes de las 12 horas de la reserva";
                    endif;

                    if ($UnaHorasDespues > $FechaHoraReserva && $Hoy == $DatosSolicitud[Fecha] && $CaddieIngreso == 1) :
                        $GeneraCodigo = 1;
                    else :
                        $msg_respuesta = "No se genero el codigo ya que se cancelo una hora antes y el caddie ya esta en el club";
                    endif;

                    if ($GeneraCodigo == 1) :
                        $codigo_canje = SIMWebServiceEcaddie::push_notifica_codigo_pago($IDClub, $IDSocio, $IDSolicitud);
                        if (!empty($codigo_canje)) :
                            $msg_respuesta = "Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_canje . " Lo puede consultar tambien en el modulo de Notificaciones";
                        endif;
                    endif;
                endif;

                $IDSocioNotifica = 0;
                $EnviaLink = 'S';
                $VerReservas = 'N';
                $Titular = "Reserva cancelada por socio";
                $FechaInicio = date("Y-m-d");
                $FechaFin = date("Y-m-d");
                $DiaReserva = date("w", strtotime(date("Y-m-d"))) . "|";
                $Cantidad = 1;
                $IDUsuarioNotifica = $IDUsuarioCaddie;

                $Mensaje = "El socio cancelo la reserva que se tenia para $FechaHoraReserva";

                SIMWebServiceEcaddie::EnviarNotificacion($IDClub, $IDSocioNotifica, $IDUsuarioNotifica, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud);

                $respuesta[success] = true;
                $respuesta[message] = "Solicitud de caddie cancelada\n\n" . $msg_respuesta;
                $respuesta[response] = null;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "La solicitud no existe no se puede eliminar";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_eliminar_caddie_reserva_empleado($IDClub, $IDUsuario, $IDSolicitud)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDSolicitud) && !empty($IDUsuario)) :

            // CONSULTAMOS LA SOLICITUD
            $DatosSolicitud = $dbo->fetchAll("SolicitudCaddieRappi", "IDSolicitudCaddieRappi = $IDSolicitud");

            if (count($DatosSolicitud) > 0) :
                $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                $DatosCaddie = $dbo->fetchAll("CaddiesEcaddie", "IDUsuario = $IDUsuario ORDER BY IDCaddiesEcaddie DESC LIMIT 1");
                $IDCaddiesEcaddie = $DatosCaddie[IDCaddiesEcaddie];
                if ($IDCaddiesEcaddie > 0) :

                    if ($DatosSolicitud[TipoCaddie] == 'Aleatorio') :
                        // SI ES ALEATORIO DEBE VOLVER A BUSCAR
                        $UPDATE = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'EsperandoCaddie' WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                        $dbo->query($UPDATE);

                    // ENVIAR NOTIFICACIÓN SOLO A CADDIES DEL MISMO PRECIO 
                    else :

                        $Caddies = json_decode($DatosSolicitud[Opciones], true);
                        if (count($Caddies) > 1) :
                            // SI HAY MAS DE UN CADDIE BUSCAMOS LOS DEMAS CADDIES Y ENVIAMOS NOTIFICACIÓN
                            foreach ($Caddies as $id => $Caddie) :
                                if ($Caddie[IDCaddie] != $IDCaddiesEcaddie) :

                                    $IDSocio = 0;
                                    $IDUsuario = $dbo->getFields("CaddiesEcaddie", "IDUsuario", "IDCaddiesEcaddie = $Caddie[IDCaddie]");
                                    $EnviaLink = 'S';
                                    $VerReservas = 'N';
                                    $Titular = "Nueva reserva disponible para ser tomada";
                                    $Mensaje = "Hay un nuevo turno para ser tomado\nFecha: $Fecha\nHora: $Hora.";
                                    $FechaInicio = date("Y-m-d");
                                    $FechaFin = $Fecha;
                                    $DiaReserva = date("w", strtotime($Fecha)) . "|";
                                    $Cantidad = count($Caddies);
                                    $IDSolicitud = $IDSolicitudCaddieRappi;

                                    SIMWebServiceEcaddie::EnviarNotificacion($IDClub, $IDSocio, $IDUsuario, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud);

                                endif;
                            endforeach;
                        else :
                            // GENERAMOS CODIGO SI YA FUE PAGADA

                            $codigo_canje = SIMWebServiceEcaddie::push_notifica_codigo_pago($IDClub, $IDSocio, $IDSolicitud);
                            if (!empty($codigo_canje)) :
                                $msg_respuesta = "Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_canje . " Lo puede consultar tambien en el modulo de Notificaciones";
                            endif;

                            $UPDATE = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'Cancelada', IDCaddiesEcaddie = '$IDCaddiesEcaddie', FechaTrEd = NOW(), UsuarioTrEd = 'USUARIO-$IDUsuario-CADDIE-$IDCaddiesEcaddie', FechaCancelacion = NOW(), UsuarioCancelacion = 'USUARIO-$IDUsuario-CADDIE-$IDCaddiesEcaddie' WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                            $dbo->query($UPDATE);

                            $Titular = "El caddie ha rechazado la solicitud";
                            $Mensaje = "El caddie especial ha rechazado la solicitud, su reserva queda cancelada";

                            $IDSocio = 0;
                            $IDUsuario = $dbo->getFields("CaddiesEcaddie", "IDUsuario", "IDCaddiesEcaddie = $Caddie[IDCaddie]");
                            $EnviaLink = 'S';
                            $VerReservas = 'N';
                            $FechaInicio = date("Y-m-d");
                            $FechaFin = $Fecha;
                            $DiaReserva = date("w", strtotime($Fecha)) . "|";
                            $Cantidad = count($Caddies);
                            $IDSolicitud = $IDSolicitudCaddieRappi;

                            SIMWebServiceEcaddie::EnviarNotificacion($IDClub, $IDSocio, $IDUsuario, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud);


                        endif;

                    endif;


                    $InsertRechazadas = "INSERT INTO SolicitudRechazaCaddie (IDSolicitudCaddieRappi, IDCaddiesEcaddie) VALUES ('$IDSolicitud','$IDCaddiesEcaddie')";
                    $dbo->query($InsertRechazadas);

                    $respuesta[success] = true;
                    $respuesta[message] = "Solicitud cancelada";
                    $respuesta[response] = null;

                else :
                    $respuesta["message"] = "EL USUARIO NO TIENE UN CADDIE ASOCIADO.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "La solicitud no existe no se puede eliminar";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_agenda_caddie($IDClub, $IDUsuario, $HorasSeleccionadas, $DiasSeleccionados, $IDClubSeleccionado, $TurboCaddieActivado)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDUsuario) && !empty($HorasSeleccionadas) && !empty($DiasSeleccionados)) :

            // BUSCAMOS EL CADDIE
            $DatosCaddie = $dbo->fetchAll("CaddiesEcaddie", "IDUsuario = $IDUsuario ORDER BY IDCaddiesEcaddie DESC LIMIT 1");
            $IDCaddiesEcaddie = $DatosCaddie[IDCaddiesEcaddie];
            if ($IDCaddiesEcaddie > 0) :

                // ELIMINAMOS DISPONIBILIDAD DEL CADDIE PARA NO GUARDAR DATOS BASURA
                $Delete = "DELETE FROM DisponibilidiadCaddiesEcaddie WHERE IDCaddiesEcaddie = $IDCaddiesEcaddie";
                $dbo->query($Delete);

                // SI SE ACTIVO COMO TURBO CADDIE SE PONE EL CADDIE COMO TURBO
                if ($TurboCaddieActivado == 'S') :
                    $UPDATE = "UPDATE CaddiesEcaddie SET TurboCaddieActivado = '$TurboCaddieActivado' WHERE IDCaddiesEcaddie = $IDCaddiesEcaddie";
                    $dbo->query($UPDATE);
                else :
                    $UPDATE = "UPDATE CaddiesEcaddie SET TurboCaddieActivado = 'N' WHERE IDCaddiesEcaddie = $IDCaddiesEcaddie";
                    $dbo->query($UPDATE);
                endif;

                // LAS FECHAS SERAN DE LA ACTUAL HASTA EL PROXIMO DOMINGO

                $FechaDesde = date("Y-m-d");
                $FechaHasta = date("Y-m-d", strtotime('next Sunday'));

                $Horas = json_decode($HorasSeleccionadas, true);

                if (count($Horas) > 0) :
                    foreach ($Horas as $id => $Hora) :

                        $HoraDesde = $Hora[HoraInicial];
                        $HoraHasta = $Hora[HoraFinal];

                        $DiasSeleccionados = str_replace("[", "", $DiasSeleccionados);
                        $DiasSeleccionados = str_replace("]", "", $DiasSeleccionados);
                        $DiasSeleccionados = str_replace("\"", "", $DiasSeleccionados);

                        $InsertDisponibilidad = "INSERT INTO DisponibilidiadCaddiesEcaddie (IDCaddiesEcaddie,IDClubSeleccion, HoraDesde, HoraHasta, Dias, FechaDesde, FechaHasta) 
                                                    VALUES ('$IDCaddiesEcaddie', '$IDClubSeleccionado','$HoraDesde', '$HoraHasta', '$DiasSeleccionados', '$FechaDesde', '$FechaHasta')";
                        $dbo->query($InsertDisponibilidad);

                        $IDDisponibilidiadCaddiesEcaddie = $dbo->lastID();

                        if ($IDDisponibilidiadCaddiesEcaddie <= 0) :
                            $respuesta["message"] = "ERROR AL INSERTAR LA DISPONIBILIDAD";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        else :
                            $respuesta["message"] = "DISPONIBILIDAD CREADA, RECUERDE QUE EL PROXIMO LUNES DEBERA CREAR DE NUEVO SU AGENDA";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        endif;

                    endforeach;
                else :
                    $respuesta["message"] = "Debe seleccionar horas para la agenda " . $HorasSeleccionadas;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            else :
                $respuesta["message"] = "EL USUARIO NO TIENE UN CADDIE ASOCIADO.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_mi_agenda_caddie($IDClub, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDUsuario)) :

            $DatosCaddie = $dbo->fetchAll("CaddiesEcaddie", "IDUsuario = $IDUsuario ORDER BY IDCaddiesEcaddie DESC LIMIT 1");
            $IDCaddiesEcaddie = $DatosCaddie[IDCaddiesEcaddie];
            $TurboCaddieActivado = $DatosCaddie[TurboCaddieActivado];
            if ($IDCaddiesEcaddie > 0) :

                // BUSCAMOS LA DISPONIBILIDAD DEL CADDIE

                $SQLDisponibilidad = "SELECT * FROM DisponibilidiadCaddiesEcaddie WHERE IDCaddiesEcaddie = $IDCaddiesEcaddie";
                $QRYDisponibilidad = $dbo->query($SQLDisponibilidad);

                if ($dbo->rows($QRYDisponibilidad) > 0) :
                    $message = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    $ResponseHoras = array();

                    while ($Datos = $dbo->fetchArray($QRYDisponibilidad)) :


                        $Dias = explode(",", $Datos[Dias]);

                        foreach ($Dias as $id => $Dia) :
                            $ResponseDias[] = $Dia;
                        endforeach;

                        // $ResponseDias = $Datos[Dias];                       

                        $InfoHoras[HoraInicial] = $Datos[HoraDesde];
                        $InfoHoras[HoraFinal] = $Datos[HoraHasta];

                        array_push($ResponseHoras, $InfoHoras);

                        $IDClubSeleccionado = $Datos[IDClubSeleccion];


                    endwhile;

                    $InfoResponse[DiasSeleccionados] = $ResponseDias;
                    $InfoResponse[HorasSeleccionadas] = $ResponseHoras;

                    $InfoResponse[IDClubSeleccionado] = $IDClubSeleccionado;

                    $NombreClubSeleccionado = $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = $IDClubSeleccionado");
                    $InfoResponse[NombreClubSeleccionado] = $NombreClubSeleccionado;
                    $InfoResponse[TurboCaddieActivado] = $TurboCaddieActivado;

                    $response = $InfoResponse;

                    $respuesta[success] = true;
                    $respuesta[message] = $message;
                    $respuesta[response] = $response;

                else :
                    $respuesta[success] = false;
                    $respuesta[message] = "No hay datos";
                    $respuesta[response] = "";
                endif;

            else :
                $respuesta["message"] = "EL USUARIO NO TIENE UN CADDIE ASOCIADO.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_respuesta_de_caddie_a_reserva($IDClub, $IDUsuario, $IDSolicitud, $AceptaSolicitud)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDSolicitud) && !empty($AceptaSolicitud)) :

            // CONSULTAMOS LA SOLICITUD
            $DatosSolicitud = $dbo->fetchAll("SolicitudCaddieRappi", "IDSolicitudCaddieRappi = $IDSolicitud");

            if (count($DatosSolicitud) > 0) :
                // VALIDAMOS QUE EL USUARIO SEA CADDIE 
                $DatosCaddie = $dbo->fetchAll("CaddiesEcaddie", "IDUsuario = $IDUsuario ORDER BY IDCaddiesEcaddie DESC LIMIT 1");
                $IDCaddiesEcaddie = $DatosCaddie[IDCaddiesEcaddie];
                if ($IDCaddiesEcaddie > 0) :
                    // VALIDAMOS QUE LA RESERVA NO ESTE CONFIRMADA POR ALGUIEN MAS
                    if ($DatosSolicitud[EstadoSolicitud] != 'Confirmada') :

                        if ($AceptaSolicitud == 'S') :
                        //VALIDAMOS SI YA HA TOMADO ALGUNA SOLICITUD HOY
       $total = "SELECT COUNT(*) as total FROM `SolicitudCaddieRappi` WHERE IDCaddiesEcaddie=$IDCaddiesEcaddie AND EstadoSolicitud ='PendientePago' AND FECHA=NOW() " ; 
        $solicitudes=  $dbo->query($total);
        $solicitudes_caddie = $dbo->fetchArray($solicitudes);
  
        
        if($solicitudes_caddie["total"]==0):
        
                            
                            
                            
                            // SI ACEPTA LA SOLICITUD ASIGNAMOS EL CADDIE Y PASAMOS DE ESTADO
                            $UPDATE = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'PendientePago', Valor = '$DatosCaddie[Valor]', IDCaddiesEcaddie = '$IDCaddiesEcaddie', FechaTrEd = NOW(), UsuarioTrEd = 'USUARIO-$IDUsuario-CADDIE-$IDCaddiesEcaddie' WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                            $dbo->query($UPDATE);

                            $Titular = "Un Caddie ha confirmado la reserva";
                            $Mensaje = "Reserva confirmada por el caddie: $DatosCaddie[Nombre]\nFecha: $DatosSolicitud[Fecha]\nHora: $DatosSolicitud[Hora]\n Por favor haga el pago de su reserva.";

                            $message = "SOLICITUD ACEPTADA";
                            else:
                                 $message = "LO SETIMOS YA TIENE UNA  SOLICITUD EL DIA DE HOY.";
                                 endif;
                        else :
                            $InsertRechazadas = "INSERT INTO SolicitudRechazaCaddie (IDSolicitudCaddieRappi, IDCaddiesEcaddie) VALUES ('$IDSolicitud','$IDCaddiesEcaddie')";
                            $dbo->query($InsertRechazadas);

                            if ($DatosSolicitud[TipoCaddie] == 'Especial') :
                                $Caddies = json_decode($DatosSolicitud[Opciones], true);
                                if (count($Caddies) <= 1) :
                                    // SI SOLO ES UN CADDIE SE CANCE LA RESERVA Y SE NOTIFICA AL SOCIO
                                    $UPDATE = "UPDATE SolicitudCaddieRappi SET EstadoSolicitud = 'Cancelada', FechaCancelacion = NOW(), UsuarioCancelacion = 'USUARIO-$IDUsuario-CADDIE-$IDCaddiesEcaddie', FechaTrEd = NOW(), UsuarioTrEd = 'USUARIO-$IDUsuario-CADDIE-$IDCaddiesEcaddie' WHERE IDSolicitudCaddieRappi = '$IDSolicitud'";
                                    $dbo->query($UPDATE);

                                    $Titular = "El caddie ha rechazado la solicitud";
                                    $Mensaje = "El caddie especial ha rechazado la solicitud, su reserva queda cancelada";

                                    $IDSocioNotifica = $DatosSolicitud[IDSocio];
                                    $IDUsuarioNotifica = 0;
                                    $EnviaLink = 'S';
                                    $VerReservas = 'N';
                                    $FechaInicio = date("Y-m-d");
                                    $FechaFin = date("Y-m-d");
                                    $DiaReserva = date("w", strtotime($DatosSolicitud[Fecha])) . "|";
                                    $Cantidad = count($Caddies);

                                    SIMWebServiceEcaddie::EnviarNotificacion($IDClub, $IDSocioNotifica, $IDUsuarioNotifica, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud);

                                endif;
                            endif;

                            $message = "SOLICITUD RECHAZADA";
                        endif;

                        $respuesta[success] = true;
                        $respuesta[message] = $message;
                        $respuesta[response] = null;

                    elseif ($DatosSolicitud[EstadoSolicitud] == 'Cancelada') :

                        $respuesta["message"] = "LA RESERVA FUE CANCELADA";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = "LA RESERVA YA FUE CONFIRMADA";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    endif;
                else :
                    $respuesta["message"] = "EL USUARIO NO TIENE UN CADDIE ASOCIADO.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "La solicitud no existe";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function EnviarNotificacion($IDClub, $IDSocio, $IDUsuario, $EnviaLink, $VerReservas, $Titular, $Mensaje, $FechaInicio, $FechaFin, $DiaReserva, $Cantidad, $IDSolicitud)
    {

        $dbo = SIMDB::get();

        $array_ios = array();
        $array_android = array();

        if ($IDSocio > 0) :
            $Campo = "IDSocio";
            $Tabla = "Socio";
            $Valor = $IDSocio;
            $TipoApp = "Socio";
            $Dirigido = "S";
        elseif ($IDUsuario > 0) :
            $Campo = "IDUsuario";
            $Tabla = "Usuario";
            $Valor = $IDUsuario;
            $TipoApp = "Empleado";
            $Dirigido = "E";
        endif;

        if ($VerReservas == 'S') :
            $idsubmodulo = 0;
            $AñadidoLink = "&submodule=0";
        else :
            $idsubmodulo = 1;
            $AñadidoLink = "&detail=$IDSolicitud";
            $iddetalle = $IDSolicitud;
        endif;

        if ($EnviaLink = 'S') :
            $UrlLink = "miclub://show?module=request_caddies$AñadidoLink";
        endif;

        $InfoPersona = $dbo->fetchAll($Tabla, "$Campo = $Valor");



        $users = array(
            array(
                "id" => $InfoPersona[$Campo],
                "idclub" => $IDClub,
                "registration_key" => $InfoPersona["Token"],
                "deviceType" => $InfoPersona["Dispositivo"],
                'link'   => $EnviaLink,
                'urllink'   => $UrlLink
            )
        );

        $custom["tipo"] = "General";
        $custom["idseccion"] = (string)"0";
        $custom["iddetalle"] = $iddetalle;
        $custom["titulo"] = $Titular;
        $custom["idmodulo"] = 168;
        $custom["idsubmodulo"] = $idsubmodulo;

        if ($InfoPersona["Dispositivo"] == "iOS")
            $array_ios[] = $InfoPersona["Token"];
        elseif ($InfoPersona["Dispositivo"] == "Android")
            $array_android[] = $InfoPersona["Token"];

        SIMUtil::sendAlerts_V2($users, $Mensaje, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
        // INSERTAMOS EN LA TABLA DE NOTIFICACIONES GENERALES      

        $Insert[IDClub] = $IDClub;
        $Insert[TituloMensaje] = $Titular;
        $Insert[Mensaje] = $Mensaje;
        $Insert[Notificaciones] = $Cantidad;
        $Insert[DirigidoAGeneral] = $Dirigido;
        $Insert[Link] = $UrlLink;
        $Insert[FechaInicio] = $FechaInicio;
        $Insert[FechaFin] = $FechaFin;
        $Insert[HoraEnvio] = "";
        $Insert[Dias] = $DiaReserva;
        $Insert[UsuarioTrCr] = "SERVICIO eCaddies";
        $Insert[FechaTrCr] = date("Y-m-d");

        $IDNotificacionesGenerales = $dbo->insert($Insert, "NotificacionesGenerales", "IDNotificacionesGenerales");

        $InsertLog[IDNotificacionesGenerales] = $IDNotificacionesGenerales;
        $InsertLog[$Campo] = $Valor;
        $InsertLog[IDClub] = $IDClub;
        $InsertLog[Token] = $InfoPersona["Token"];
        $InsertLog[Dispositivo] = $InfoPersona["Dispositivo"];
        $InsertLog[Fecha] = date("Y-m-d");
        $InsertLog[App] = $TipoApp;
        $InsertLog[Tipo] = $custom["tipo"];
        $InsertLog[Titulo] = $Titular;
        $InsertLog[Mensaje] = $Mensaje;
        $InsertLog[Modulo] = 168;
        $InsertLog[IDDetalle] = $iddetalle;
        $InsertLog[Link] = $UrlLink;
        $InsertLog[SubModulo] = $idsubmodulo;

        $dbo->insert($InsertLog, "LogNotificacion", "IDLogNotificacion");
    }

    public function push_notifica_codigo_pago($IDClub, $IDSocio, $IDSolicitud)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSolicitud)) :

            $DatosSolicitud = $dbo->fetchAll("SolicitudCaddieRappi", "IDSolicitudCaddieRappi = $IDSolicitud");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '$IDSocio'");
            $valor = $DatosSolicitud[Valor];

            if ($DatosSolicitud[Pagado] == 'S' || true) :

                //generar un codigo valido para redimir
                $codigo_redimir = SIMUtil::generarPassword("6");
                //Inserto el codigo
                $sql_codigo = "INSERT INTO ClubCodigoPago (IDClub, IDSocio, Codigo, Disponible, IDServicio, Valor, UsuarioTrCr, FechaTrCr ) 
                                        VALUES ('$IDClub','$IDSocio', '$codigo_redimir','S','$IDSolicitud','$valor','CANCELACION SERVICIO eCaddie',NOW())";
                $dbo->query($sql_codigo);


                $users = array(
                    array(
                        "id" => $IDSocio,
                        "idclub" => $IDClub,
                        "registration_key" => $datos_socio["Token"],
                        "deviceType" => $datos_socio["Dispositivo"]
                    )
                );

                $Mensaje = "Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_redimir;

                $custom["tipo"] = "app";
                $custom["idseccion"] = (string)"0";
                $custom["iddetalle"] = (string)"0";
                $custom["titulo"] = "Codigo eCaddie";
                $custom["idmodulo"] = (string)"2";


                if ($datos_socio["Dispositivo"] == "iOS")
                    $array_ios[] = $datos_socio["Token"];
                elseif ($datos_socio["Dispositivo"] == "Android")
                    $array_android[] = $datos_socio["Token"];

                SIMUtil::sendAlerts_V2($users, $Mensaje, $custom, "Socio", $array_android, $array_ios, $IDClub);

                $InsertLog[IDNotificacionesGenerales] = "";
                $InsertLog[IDSocio] = $IDSocio;
                $InsertLog[IDClub] = $IDClub;
                $InsertLog[Token] = $InfoPersona["Token"];
                $InsertLog[Dispositivo] = $datos_socio["Token"];
                $InsertLog[Fecha] = date("Y-m-d");
                $InsertLog[App] = "Socio";
                $InsertLog[Tipo] = "app";
                $InsertLog[Titulo] = $custom["titulo"];
                $InsertLog[Mensaje] = $Mensaje;
                $InsertLog[Modulo] = $custom["idmodulo"];
                $InsertLog[IDDetalle] = $custom["iddetalle"];

                $dbo->insert($InsertLog, "LogNotificacion", "IDLogNotificacion");
            endif;
        endif;

        return $codigo_redimir;
    }
}
