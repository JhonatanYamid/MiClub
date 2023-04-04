<?php

//2023-03-15 09:00

class SIMWebService
{

    public function get_beneficiarios($IDClub, $IDSocio, $Fecha = "", $Hora = "", $Tipo = "")
    {

        $dbo = &SIMDB::get();
        if (!empty($IDSocio)) {
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'", "array");
            if (!empty($datos_socio["IDSocio"])) {
                //Consulto el nucleo familiar
                if (!empty($datos_socio["AccionPadre"])) : // Es beneficiario
                    $condicion_nucleo = " and (AccionPadre = '" . $datos_socio["AccionPadre"] . "' or Accion = '" . $datos_socio["AccionPadre"] . "')";
                    $tipo_socio = "Beneficiario";
                else : // es Cabeza familia
                    $condicion_nucleo = " and ( AccionPadre = '" . $datos_socio["Accion"] . "' and AccionPadre <> '' )";
                    $tipo_socio = "Socio";
                endif;

                if ($IDClub == 7) : //Especial lagartos no mostrar suegros ni papa
                    $condicion_especial = " and ( Parentesco IS null or (Parentesco not like '%suegr%' and Parentesco not like '%pad%') ) AND IDTipoSocioZeus != 'B02'";
                endif;

                //Especial uruguaty los hijos de socios separados deben salir en ambos asi sean acciones distintas
                if ($IDClub == 125 && !empty($datos_socio["ClaveSistemaExterno"])) {
                    $AccionOtro = $dbo->getFields("Socio", "Accion", "NumeroDocumento = '" . $datos_socio["ClaveSistemaExterno"] . "'");
                    if (!empty($AccionOtro)) {
                        $condicion_nucleo .= " or (AccionPadre = '" . $AccionOtro . "' and IDClub = '" . $IDClub . "')";
                    }
                }

                $response_beneficiario = array();
                $sql_nucleo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' and IDEstadoSocio <> 2 " . $condicion_nucleo . $condicion_especial;
                $qry_nucleo = $dbo->query($sql_nucleo);
                while ($datos_nucleo = $dbo->fetchArray($qry_nucleo)) :
                    $foto_nucleo = "";
                    $foto_cod_barras_nucleo = "";

                    $socio_beneficiario[IDBeneficiario] = $datos_nucleo[IDSocio];
                    $socio_beneficiario[Nombre] = $datos_nucleo[Nombre] . " " . $datos_nucleo[Apellido];
                    $socio_beneficiario[TipoBeneficiario] = "Socio";

                    array_push($response_beneficiario, $socio_beneficiario);
                endwhile;

                //Consulto los invitados vigentes del socio
                //$sql_invitado = "SELECT IDInvitado FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaInicio >= CURDATE() Union
                //                SELECT IDInvitado FROM SocioAutorizacion WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaInicio >= CURDATE()";

                /*
                $sql_invitado = "SELECT IDInvitado FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaFin >= CURDATE() Union
                SELECT IDInvitado FROM SocioAutorizacion WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaFin >= CURDATE()";

                 */
                // PARA URUGUAY Y FONTANAR NO SE PUEDEN MOSTRAR LOS INVITADOS DEL CLUB, NO TIENEN PERMITIDO SER BENEFICIARIOS
                if ($IDClub != 125 && $IDClub != 18) :
                    if (!empty($Fecha)) {
                        $condicion_fecha = " and FechaIngreso = '" . $Fecha . "' ";
                        $condicion_fecha_especial = " and FechaFin >= '" . $Fecha . "' ";
                    } else {
                        $condicion_fecha = " and FechaIngreso >= CURDATE() ";
                        $condicion_fecha_especial = " and FechaFin >= CURDATE() ";
                    }

                    $sql_invitado = "SELECT IDInvitado FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  " . $condicion_fecha_especial;
                    $qry_invitado = $dbo->query($sql_invitado);
                    while ($datos_invitado = $dbo->fetchArray($qry_invitado)) :
                        $invitado_beneficiario["IDBeneficiario"] = $datos_invitado["IDInvitado"];
                        $invitado_beneficiario["Nombre"] = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $datos_invitado["IDInvitado"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $datos_invitado["IDInvitado"] . "'");
                        $invitado_beneficiario["TipoBeneficiario"] = "Invitado";
                        array_push($response_beneficiario, $invitado_beneficiario);
                    endwhile;

                    //Consulto los invitados vigentes del socio
                    $sql_invitado = "SELECT IDSocioInvitado,Nombre FROM SocioInvitado WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' " . $condicion_fecha;
                    $qry_invitado = $dbo->query($sql_invitado);
                    while ($datos_invitado = $dbo->fetchArray($qry_invitado)) :
                        $invitado_beneficiario["IDBeneficiario"] = $datos_invitado["IDSocioInvitado"];
                        $invitado_beneficiario["Nombre"] = $datos_invitado["Nombre"];
                        $invitado_beneficiario["TipoBeneficiario"] = "Invitado";
                        array_push($response_beneficiario, $invitado_beneficiario);
                    endwhile;
                endif;

                if (count($response_beneficiario) <= 0) {
                    $response_beneficiario = array();
                    $invitado_beneficiario["IDBeneficiario"] = "";

                    if ($IDClub == 151) {
                        $respuesta_serv = "You have no associated beneficiaries";
                    } else {
                        $respuesta_serv = "No tienes asociados beneficiarios";
                    }

                    $invitado_beneficiario["Nombre"] = $respuesta_serv;
                    $invitado_beneficiario["TipoBeneficiario"] = "";
                    array_push($response_beneficiario, $invitado_beneficiario);
                }

                $response["IDClub"] = $datos_socio["IDClub"];
                $response["IDSocio"] = $datos_socio["IDSocio"];
                $response["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                $response["Beneficiarios"] = $response_beneficiario;
                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = 'Atencion faltan parametros';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "25 Atencionfaltanparametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    //Verifica si club y el servicio está abierto en la fecha indicada
    public function verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDServicioElemento = "", $Hora = "", $Tee = "")
    {
        global $ClubAbierto, $MotivoCierre;
        $dbo = &SIMDB::get();
        $respuesta = "";
        $condicion_servicio = "and IDServicio = '" . $IDServicio . "'";

        if (empty($ClubAbierto)) {
            $sql = "SELECT IDClubFechaCierre,Motivo FROM  ClubFechaCierre WHERE Fecha = '" . $Fecha . "' and IDClub = '" . $IDClub . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $r_cierre = $dbo->fetchArray($qry);
                $ClubAbierto = "N";
                $MotivoCierre = $r_cierre["Motivo"];
            } else {
                $ClubAbierto = "S";
            }
        }

        if ($ClubAbierto == "N") {
            $respuesta = "Lo sentimos club cerrado el dia: " . $Fecha . " Motivo: " . $MotivoCierre;
        } else {

            if (!empty($IDServicioElemento)) {
                $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDServicioElemento, $IDServicio, $IDClub);
                $array_multiple_id = explode(",", $condicion_multiple_elemento);
                if (count($array_multiple_id) > 1) :
                    foreach ($array_multiple_id as $id_elemento) :
                        $array_condicion_multiple_elemento[] = " SCE.IDServicioElemento = " . $id_elemento;
                    endforeach;
                    if (count($condicion_multiple_elemento) > 0) :
                        $condicion_elemento = " and (" . implode(" or ", $array_condicion_multiple_elemento) . ")";
                        $condicion_servicio = ""; // no consulto el servicio por que puede ser distintos
                    endif;
                else :
                    $condicion_elemento = " and SCE.IDServicioElemento = " . $IDServicioElemento;
                endif;
            } else {
                $condicion_elemento = " and SCE.IDServicioElemento = ''";
            }

            if (!empty($condicion_elemento)) {
                $tabla_join = " ,ServicioCierreElemento SCE ";
                $where_join = " and SC.IDServicioCierre= SCE.IDServicioCierre ";
            }

            if (!empty($Tee)) {
                $condicion_tee = " and " . $Tee . " = 'S'";
            }

            if (!empty($Hora)) :
                $condicion_hora = " and HoraInicio <= '" . $Hora . "' and HoraFin >= '" . $Hora . "' ";
                $dia_semana_fecha = date("w", strtotime($Fecha));
                //$sql_servicio = "SELECT * FROM  ServicioCierre WHERE FechaInicio <= '".$Fecha."' and FechaFin >= '".$Fecha."' and IDServicio = '".$IDServicio."' " . $condicion_hora . $condicion_elemento;
                $sql_servicio = "SELECT Dias,Descripcion,MasInformacionCierre FROM  ServicioCierre SC " . $tabla_join . " WHERE FechaInicio <= '" . $Fecha . "' and FechaFin >= '" . $Fecha . "' " . $where_join . $condicion_servicio . $condicion_hora . $condicion_elemento . $condicion_tee;

                $qry_servicio = $dbo->query($sql_servicio);
                while ($r_cierre_servicio = $dbo->fetchArray($qry_servicio)) {

                    //$r_cierre_servicio = $dbo->fetchArray( $qry_servicio );
                    if (!empty($r_cierre_servicio["Dias"])) {
                        $array_dias_cierre = explode("|", $r_cierre_servicio["Dias"]);
                        if (in_array($dia_semana_fecha, $array_dias_cierre)) {
                            $respuesta = "Lo sentimos servicio cerrado el dia: " . $Fecha . " motivo: " . $r_cierre_servicio["Descripcion"] . ":" . $r_cierre_servicio["MasInformacionCierre"];
                        }
                    } else {
                        $respuesta = "Lo sentimos servicio cerrado el dia: " . $Fecha . " Motivo: " . $r_cierre_servicio["Descripcion"] . ":" . $r_cierre_servicio["MasInformacionCierre"];
                    }
                }
            else :
                $condicion_hora = " and HoraInicio = '00:00:00' and HoraFin = '00:00:00' ";
            endif;
        }

        return $respuesta;
    }

    public function consultar_disponibilidad($qry, $IDElemento, $IDServicio, $Fecha)
    {

        $dbo = &SIMDB::get();
        //Horas Disponibles Elemento
        $response_disponibilidad = array();

        if (!empty($IDElemento)) {
            $condicion_elemento = " and IDServicioElemento = '" . $IDElemento . "'";
        }

        while ($r = $dbo->fetchArray($qry)) {

            $sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '" . $IDServicio . "' " . $condicion_elemento . " Order by Orden";
            $result_elementos_servicio = $dbo->query($sql_elementos_servicio);
            while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)) :
                unset($array_hora_reservada);
                $IDElemento = $r_elementos_servicio["IDServicioElemento"];

                //Consulto lo que  tiene reservado el elemento en la fecha indicada
                $sql_reserva_elemento = "SELECT ReservaGeneral.*, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
                $qry_reserva_elemento = $dbo->query($sql_reserva_elemento);
                $array_socio = array();
                while ($row_reserva_elemento = $dbo->fetchArray($qry_reserva_elemento)) {
                    $array_hora_reservada[] = $row_reserva_elemento["Hora"];
                    $array_socio[$row_reserva_elemento["Hora"]] = $row_reserva_elemento;
                    $array_socio[$row_reserva_elemento["Hora"]]["NombreSocio"] = utf8_encode($row_reserva_elemento["Socio"]);
                }

                //Horas generales del servicio
                $horaInicial = $r["HoraDesde"];
                $minutoAnadir = $r["IntervaloHora"];
                $hora_final = strtotime($r["HoraHasta"]);
                $hora_actual = $r["HoraDesde"];

                $dia_fecha = date('N', strtotime($Fecha));

                //Verifico si tene disponibilidad especifica el elemento
                $sql_dispo_elemento = "Select * From ElementoDisponibilidad Where IDServicioElemento = '" . $IDElemento . "'";
                $qry_dispo_elemento = $dbo->query($sql_dispo_elemento);
                if ($dbo->rows($qry_dispo_elemento) > 0) {
                    $verifica_disponibilidad_especifica = 1;
                    $sql_dispo_elemento = "Select * From ElementoDisponibilidad Where IDServicioElemento = '" . $IDElemento . "' and IDDia = '" . $dia_fecha . "' Order by HoraDesde";
                    $qry_dispo_elemento = $dbo->query($sql_dispo_elemento);
                    while ($row_dispo_elemento = $dbo->fetchArray($qry_dispo_elemento)) {
                        $horaInicial = $row_dispo_elemento["HoraDesde"];
                        $minutoAnadir = $r["IntervaloHora"];
                        $hora_final = strtotime($row_dispo_elemento["HoraHasta"]);
                        $hora_actual = strtotime($row_dispo_elemento["HoraDesde"]);

                        while ($hora_actual <= $hora_final) :
                            if (strlen($horaInicial) != 8) :
                                $horaInicial .= ":00";
                            endif;

                            $hora["Hora"] = $horaInicial;
                            $zonahoraria = date_default_timezone_get();
                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                            $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);
                            if (in_array($horaInicial, $array_hora_reservada)) {
                                $hora["Disponible"] = "N";
                                $hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
                            } else {
                                $hora["Disponible"] = "S";
                                $hora["Socio"] = "";
                            }

                            $hora["IDElemento"] = $IDElemento;
                            $hora["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDElemento . "'");
                            array_push($response_disponibilidad, $hora);

                            $array_horas_elemento[] = $horaInicial;
                            $segundos_horaInicial = strtotime($horaInicial);
                            $segundos_minutoAnadir = $minutoAnadir * 60;
                            $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                            $hora_actual = strtotime($nuevaHora);
                            $horaInicial = $nuevaHora;
                        endwhile;
                    }
                }
                // Si no tiene disponibildad especifica busco la general para elementos
                else {
                    //Verifico si tene disponibilidad  general el elemento
                    $sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N'";
                    $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                    if ($dbo->rows($qry_dispo_elemento_gral) > 0) {

                        $verifica_disponibilidad_general = 1;
                        while ($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)) {
                            $horaInicial = $row_dispo_elemento_gral["HoraDesde"];
                            $minutoAnadir = $r["IntervaloHora"];
                            $hora_final = strtotime($row_dispo_elemento_gral["HoraHasta"]);
                            $hora_actual = strtotime($row_dispo_elemento_gral["HoraDesde"]);

                            while ($hora_actual <= $hora_final) :
                                if (strlen($horaInicial) != 8) :
                                    $horaInicial .= ":00";
                                endif;

                                $hora["Hora"] = $horaInicial;
                                $zonahoraria = date_default_timezone_get();
                                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                                $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);
                                if (in_array($horaInicial, $array_hora_reservada)) {
                                    $hora["Disponible"] = "N";
                                    $hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
                                } else {
                                    $hora["Disponible"] = "S";
                                    $hora["Socio"] = "";
                                }

                                $hora["IDElemento"] = $IDElemento;
                                $hora["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDElemento . "'");
                                array_push($response_disponibilidad, $hora);

                                $array_horas_elemento[] = $horaInicial;
                                $segundos_horaInicial = strtotime($horaInicial);
                                $segundos_minutoAnadir = $minutoAnadir * 60;
                                $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                                $hora_actual = strtotime($nuevaHora);
                                $horaInicial = $nuevaHora;
                            endwhile;
                        }
                    }
                }

                // Si no se ha especificado disponibilidad general o especifica aal elemento consulto la del servicio
                if ($verifica_disponibilidad_especifica == 0 && $verifica_disponibilidad_general == 0) :
                    $horaInicial = $r["HoraDesde"];
                    $minutoAnadir = $r["IntervaloHora"];
                    $hora_final = strtotime($r["HoraHasta"]);
                    $hora_actual = strtotime($r["HoraDesde"]);

                    while ($hora_actual <= $hora_final) :

                        if (strlen($horaInicial) != 8) :
                            $horaInicial .= ":00";
                        endif;

                        $hora["Hora"] = $horaInicial;
                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);
                        if (in_array($horaInicial, $array_hora_reservada)) {
                            $hora["Disponible"] = "N";
                            $hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
                        } else {
                            $hora["Disponible"] = "S";
                            $hora["Socio"] = "";
                        }

                        $hora["IDElemento"] = $IDElemento;
                        $hora["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDElemento . "'");
                        array_push($response_disponibilidad, $hora);

                        $segundos_horaInicial = strtotime($horaInicial);
                        $segundos_minutoAnadir = $minutoAnadir * 60;
                        $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                        $hora_actual = strtotime($nuevaHora);
                        $horaInicial = $nuevaHora;

                    endwhile;
                endif;

            endwhile;
        } //ednw hile

        return $response_disponibilidad;
    }

    public function get_disponiblidad_elemento($IDClub, $IDElemento, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();

        $verifica_disponibilidad_especifica = 0;
        $verifica_disponibilidad_general = 0;

        // Verifico que el club y servicio este disponible en la fecha consultada
        $verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio);
        if (!empty($verificacion)) :
            $respuesta["message"] = $verificacion;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $response = array();
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . 'Encontrados';
            $servicio_hora["IDClub"] = $IDClub;
            $servicio_hora["IDServicio"] = $IDServicio;
            $servicio_hora["IDElemento"] = $IDElemento;
            $servicio_hora["Fecha"] = $Fecha;

            $response_disponibilidad = SIMWebService::consultar_disponibilidad($qry, $IDElemento, $IDServicio, $Fecha);
            $servicio_hora["Disponibilidad"] = $response_disponibilidad;
            array_push($response, $servicio_hora);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function timezone_offset_string($offset)
    {
        return sprintf("%s%02d:%02d", ($offset >= 0) ? '+' : '-', abs($offset / 3600), abs($offset % 3600));
    }

    public function valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial, $SoloConfirmados = "")
    {
        $dbo = &SIMDB::get();

        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio= '" . $IDServicio . "' ", "array");

        /*
            if($SoloConfirmados=="S"){
                $condicion_estado = " and ( IDEstadoReserva = 1 ) ";
            }
            else{
                $condicion_estado = " and (IDEstadoReserva = 1 or IDEstadoReserva=3) ";
            }
            */

        $condicion_estado = " and (IDEstadoReserva = 1 or IDEstadoReserva=3) ";


        //Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
        $sql_reserva_elemento_hora_fecha = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' " . $condicion_estado . " and Hora = '" . $horaInicial . "' ";
        $result_reserva_elemento_hora_fecha = $dbo->query($sql_reserva_elemento_hora_fecha);
        $total_cupos_reservados = $dbo->rows($result_reserva_elemento_hora_fecha);

        if ($IDServicio == 7616) :
            $total_cupos_reservados = 0;
        endif;

        //le sumo los invitados por cada reserva
        $total_invitados = 0;
        if ($datos_servicio["ContarInvitadosCupo"] == "S" || $datos_servicio["ContarInvitadosCupo"] == "") {
            while ($row_invitados_reserva = $dbo->fetchArray($result_reserva_elemento_hora_fecha)) :
                $sql_invitado = "Select count(IDReservaGeneralInvitado) as TotalInvitados From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_invitados_reserva["IDReservaGeneral"] . "'";
                $result_invitado = $dbo->query($sql_invitado);
                $row_invitados = $dbo->fetchArray($result_invitado);
                $total_invitados += $row_invitados["TotalInvitados"];
            endwhile;
        }

        //en el polo en practicas que es diferente le quito los invitados por que quedan como socios
        if (($IDClub == 37 && $IDServicio == 3575) || ($IDClub == 143 && $IDServicio == 28122)) {
            $total_invitados = 0;
        }

        $total_cupos_reservados += $total_invitados;

        $total_invitados = 0;

        if ($datos_servicio["MaximoInvitadosSalon"] > 0) {
            //le sumo los invitados seleccionados, por ejemplo en salones le pide al socio cuantos invitados sin necesidad de poner los nombres
            $sql_reserva_num_invitados = "SELECT SUM(CantidadInvitadoSalon) as TotalOtrosInvitados FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' ";
            $result_reserva_num_invitados = $dbo->query($sql_reserva_num_invitados);
            $row_num_invitados = $dbo->fetchArray($result_reserva_num_invitados);
            $total_num_invitados = $row_num_invitados["TotalOtrosInvitados"];
            if ($datos_servicio["ContarSocio"] == "N")
                $total_cupos_reservados = $total_num_invitados;
            else
                $total_cupos_reservados += $total_num_invitados;
        }

        return $total_cupos_reservados;
    }

    public function verifica_elemento_otro_servicio($IDElemento, $IDServicio = "", $IDClub = "")
    {
        $dbo = &SIMDB::get();

        $condicion_multiple_elemento = "";
        $condicion_multiple_elemento = $IDElemento;
        unset($array_id_elemento);
        $IdentificadorElemento = $dbo->getFields("ServicioElemento", "IdentificadorElemento", "IDServicioElemento = '" . $IDElemento . "'");
        if ((int) $IdentificadorElemento > 100000) : //esto para que valide si es un numero de documento

            if (!empty($IDServicio)) {
                $condicion_servicio = " Group by IDServicio ";
            }

            if (!empty($IDClub)) {
                $condicion_club = " and IDClub = '" . $IDClub . "' ";
            }

            $sql_elemento_otro_servicio = "SELECT IDServicioElemento From ServicioElemento Where IdentificadorElemento = '" . $IdentificadorElemento . "' " . $condicion_club . $condicion_servicio;
            $result_elemento_otro_servicio = $dbo->query($sql_elemento_otro_servicio);
            while ($row_elemento_otro_servicio = $dbo->fetchArray($result_elemento_otro_servicio)) :
                $array_id_elemento[] = $row_elemento_otro_servicio["IDServicioElemento"];
            endwhile;
            if (count($array_id_elemento) > 0) :
                $condicion_multiple_elemento = implode(",", $array_id_elemento);
            endif;
        endif;

        return $condicion_multiple_elemento;
    }

    public function verifica_auxiliar_otro_servicio($IDAuxiliar, $IDServicio = "", $IDClub = "", $DocumentoAuxiliar)
    {
        $dbo = &SIMDB::get();

        $condicion_multiple_auxiliar = "";
        $condicion_multiple_auxiliar = $IDAuxiliar;
        unset($array_id_auxiliar);
        $IdentificadorAuxiliar = $DocumentoAuxiliar;
        if ((int) $IdentificadorAuxiliar > 100000) : //esto para que valide si es un numero de documento

            if (!empty($IDServicio)) {
                $condicion_servicio = " Group by IDServicio ";
            }

            if (!empty($IDClub)) {
                $condicion_club = " and IDClub = '" . $IDClub . "' ";
            }

            $sql_auxiliar_otro_servicio = "SELECT IDAuxiliar From Auxiliar Where NumeroDocumento = '" . $IdentificadorAuxiliar . "' " . $condicion_club . $condicion_servicio;
            $result_auxiliar_otro_servicio = $dbo->query($sql_auxiliar_otro_servicio);
            while ($row_auxiliar_otro_servicio = $dbo->fetchArray($result_auxiliar_otro_servicio)) :
                $array_id_auxiliar[] = $row_auxiliar_otro_servicio["IDAuxiliar"];
            endwhile;
            if (count($array_id_auxiliar) > 0) :
                $condicion_multiple_auxiliar = implode(",", $array_id_auxiliar);
            endif;
        endif;

        return $condicion_multiple_auxiliar;
    }

    public function get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, $IDElemento = "", $Admin = "", $UnElemento = "", $NumeroTurnos = "", $IDTipoReserva = "", $Agenda = "", $MostrarTodoDia = "", $IDUsuario = "", $IDClubAsociado = "", $IDSocio = "", $HoraInicial = "", $HoraFinal = "")
    {
        if (empty($Admin) && $Agenda != "S") {
            //$suma_rand = rand(0, 1);
            //$rand_seg = rand(1, 1) + $suma_rand;
            //sleep($rand_seg);
            usleep(800000);
            usleep(100000);
        }




        $dbo = &SIMDB::get();


        if (!empty($IDClubAsociado)) :
            $IDClubAsociado;
            $IDClub = $IDClubAsociado;
        endif;

        if ($IDClub == 227) :
            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $respuesta = SIMWebServiceCountryMedellin::get_disponibilidad_elemento_servicio($IDClub, $Fecha, $IDServicio, $datos_socio["TokenCountryMedellin"], $IDTipoReserva, $HoraInicial, $HoraFinal);
            return $respuesta;
        endif;

        $datos_servicio_actual = $dbo->fetchAll("Servicio", " IDServicio = '" . $IDServicio . "' ", "array");

        if (!empty($IDUsuario)) {
            $PermiteReservarUsuario = $dbo->getFields("Usuario", "PermiteReservar", "IDUsuario = '" . $IDUsuario . "'");
        } else {
            $PermiteReservarUsuario = "N";
        }

        $CuposporHora = $datos_servicio_actual["CupoMaximoBloque"];

        //Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
        $id_servicio_maestro = $datos_servicio_actual["IDServicioMaestro"];
        $datos_servicio_mestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $id_servicio_maestro . "' ", "array");
        $id_servicio_cancha = $datos_servicio_mestro["IDServicioMaestroReservar"];
        if ((int) $id_servicio_cancha > 0) :
            // Consulto el servicio del club asociado a este servicio maestro
            $IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");
        endif;

        if ($id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30) : //Golf
            //$respuesta = SIMWebService::get_disponibilidad_campo($IDClub,$IDCampo,$Fecha, $IDServicio);
            $respuesta = SIMWebService::get_disponibilidad_campo($IDClub, "", $Fecha, $IDServicio, $Admin, $NumeroTurnos, $MostrarTodoDia, "", $IDTipoReserva);
            return $respuesta;
        endif;

        $fecha_disponible = 0;

        $verifica_disponibilidad_especifica = 0;
        $verifica_disponibilidad_general = 0;
        $verificacion = "";
        // Verifico que el club y servicio este disponible en la fecha consultada
        $verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio);

        if (!empty($verificacion)) :
            $respuesta["message"] = $verificacion;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        /*
            //if($IDClub==10):
            //valido si el servicio tiene opciones para seleccionar la opcion por ejemplo dobles o sencillos
            $sql_tipo_reserva_servicio = "Select * From ServicioTipoReserva Where IDServicio = '".$IDServicio."' and Activo = 'S'";
            $result_tipo_reserva_servicio = $dbo->query($sql_tipo_reserva_servicio);
            $total_tipo_reserva = $dbo->rows($result_tipo_reserva_servicio);
            if((int)$total_tipo_reserva>0 && empty($IDTipoReserva)):
            $respuesta["message"] = "Debe seleccionar una opcion en el paso anterior, por favor verifique";
            $respuesta["success"] = false;
            $respuesta["response"] = NULL;
            return $respuesta;
            endif;

            //endif;
             */

        if (empty($Admin)) {
            $TipoConsultaDispo = "App";
        }

        //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
        $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha, $TipoConsultaDispo, "N");
        foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
            if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                $fecha_disponible = 1;
            endif;
        endforeach;

        if ($Agenda == "S") {
            $fecha_disponible = 1;
        }




        if ($fecha_disponible == 0 && empty($Admin)) :
            //Esta fecha aún no está disponible.
            $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (t1)";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            exit;
        endif;

        $response = array();
        $response_disponibilidades = array();
        //$sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        //$qry = $dbo->query($sql);
        //if ($dbo->rows($qry) > 0) {

        if ((int) $datos_servicio_actual["IDServicio"] > 0) {


            //$datos_servicio_configuracion = $dbo->fetchArray($qry);
            $datos_servicio_configuracion = $datos_servicio_actual;

            $message = " Encontrados";
            $servicio_hora["IDClub"] = $IDClub;
            $servicio_hora["IDServicio"] = $IDServicio;
            $servicio_hora["Fecha"] = $Fecha;
            $servicio_hora["InfoMapa"] = array(
                "ImagenMapa" => $datos_servicio_actual['ImagenMapa'],
                "ImagenAlto" => $datos_servicio_actual['ImagenAlto'],
                "ImagenAncho" => $datos_servicio_actual['ImagenAncho']
            );
            //$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);

            //Horas Disponibles Elemento
            $response_disponibilidad = array();

            //Si se selecciona un tipo de resrva consulto que elemntos pueden hacer esa reserva (por ejemplo una manicurista que solo hace decoracion y las otra no)
            if (!empty($IDTipoReserva)) :
                $sql_elemento_tipo_reserva = $dbo->query("SELECT IDServicioElemento From ServicioElementoTipoReserva Where IDServicioTipoReserva = '" . $IDTipoReserva . "'");
                while ($row_elemento_tipo_reserva = $dbo->fetchArray($sql_elemento_tipo_reserva)) :
                    $array_elementos_tipo_reserva[] = $row_elemento_tipo_reserva["IDServicioElemento"];
                endwhile;
                if (count($array_elementos_tipo_reserva) > 0) :
                    $id_elemento_tipo_reserva = implode(",", $array_elementos_tipo_reserva);
                    $condicion_elemento = "and IDServicioElemento in (" . $id_elemento_tipo_reserva . ")";

                endif;
            endif;

            if (!empty($IDElemento)) {
                $condicion_elemento = " and IDServicioElemento = '" . $IDElemento . "'";
            }

            $r = $dbo->fetchArray($qry);

            $nombre_elemento_consulta = "";
            $sql_elementos_servicio = "Select IDServicioElemento,Nombre From ServicioElemento Where IDServicio = '" . $IDServicio . "' " . $condicion_elemento . " Order By Orden";

            $result_elementos_servicio = $dbo->query($sql_elementos_servicio);
            while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)) :
                unset($array_hora_reservada);

                $IDElemento = $r_elementos_servicio["IDServicioElemento"];

                //Verifico si el elemento esta en otro servicio para traer el id y verificar disponibilidad
                $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);

                $nombre_elemento_consulta = $r_elementos_servicio["Nombre"];
                //Consulto lo que  tiene reservado el elemento en la fecha indicada
                $sql_reserva_elemento = "SELECT ReservaGeneral.* FROM ReservaGeneral WHERE IDServicioElemento in (" . $condicion_multiple_elemento . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";
                $qry_reserva_elemento = $dbo->query($sql_reserva_elemento);
                $array_socio = array();

                while ($row_reserva_elemento = $dbo->fetchArray($qry_reserva_elemento)) {
                    $tipo_reserva = "";
                    $tipo_reserva_empl = "";

                    if ($IDServicio != $row_reserva_elemento["IDServicio"]) {
                        //$total_reservas_hora_elmento++;
                        $array_reservas_por_hora[$row_reserva_elemento["Hora"]][$IDElemento]++;
                    }

                    if ($row_reserva_elemento["Tipo"] == "Automatica") :
                        //averiguo si fue automatica por una clase para mostrarlo en el nombre del socio
                        $sql_reserva_padre = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '" . $row_reserva_elemento["Hora"] . "' and Tipo <> 'Automatica' and IDSocio = '" . $row_reserva_elemento["IDSocio"] . "'  ORDER BY Hora Limit 1";
                        $qry_reserva_padre = $dbo->query($sql_reserva_padre);
                        $row_reserva_padre = $dbo->fetchArray($qry_reserva_padre);
                        $id_servicio_maestro_reserva = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva_padre["IDServicio"] . "'");
                        $id_servicio_cancha_elemento = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro_reserva . "'");
                        //Si la reserva es una clase agrego la palabra clase
                        if ($id_servicio_cancha_elemento > 0) :
                            $tipo_reserva = "Clase ";
                        else :
                            $tipo_reserva = "";
                        endif;
                    endif;

                    //Verifico si el club/servicio se configuro para mostrar el nombre del socio o para mostrar un texto personalizado, para funcionarios si se muestra el nombre
                    $MostrarReserva = $datos_servicio_actual["MostrarReserva"];
                    $sql_club = "SELECT MostrarReserva, LabelPersonalizado FROM Club WHERE IDClub = '" . $IDClub . "' ";
                    $r_club = $dbo->query($sql_club);
                    $datos_club = $dbo->fetchArray($r_club);

                    if (empty($MostrarReserva)) {
                        $MostrarReserva = $datos_club["MostrarReserva"];
                    }

                    if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) :
                        $LabelPersonalizado = $datos_servicio_actual["LabelPersonalizado"];
                        if (empty($MostrarReserva)) {
                            $LabelPersonalizado = $datos_club["LabelPersonalizado"];
                        }

                        $nombre_tomo_reserva = $LabelPersonalizado;
                    else :

                        $predio_socio = "";
                        $invitados = "";
                        unset($array_aux_reserva);
                        unset($array_nombre_aux);
                        $nombres_aux = "";

                        if ($IDServicio == 2560) :
                            $nombre_tomo_reserva = $row_reserva_elemento["NombreSocio"] . "PRUEBA PISCINA";
                        endif;

                        //En Puerto penalisa se debe mostrar el predio
                        if ($IDClub == 35) {
                            $predio_socio = $dbo->getFields("Socio", "Predio", "IDSocio = '" . $row_reserva_elemento["IDSocio"] . "'");
                            $predio_socio .= " / " . $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $row_reserva_elemento["IDSocio"] . "'");

                            if (!empty($Agenda)) :
                                // BUSCAMOS SI ESTA VACUNADO
                                $SQLVacuna2 = "SELECT IDVacunado FROM Vacunado WHERE IDSocio = $row_reserva_elemento[IDSocio]";
                                $QRYVacuna2 = $dbo->query($SQLVacuna2);
                                if ($dbo->rows($QRYVacuna2) > 0) :
                                    $Vacunado = "(V)";
                                else :
                                    $Vacunado = "(NV)";
                                endif;

                            endif;
                        }

                        if ($IDClub == 220) {
                            $DatoAccion = " (" . $row_reserva_elemento["AccionSocio"] . ") ";
                        }

                        if (!empty($Agenda) && $IDClub == 191) {
                            $DatoAccion = " (" . $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $row_reserva_elemento["IDTipoPago"] . "'") . ") ";
                        }

                        // Mostrar la reserva con cual auxiliar esta
                        if (!empty($row_reserva_elemento["IDAuxiliar"]) && (empty($Admin) || $Admin == "Agenda")) {
                            $array_aux_reserva = explode(",", $row_reserva_elemento["IDAuxiliar"]);
                            if (count($array_aux_reserva) > 0) {
                                foreach ($array_aux_reserva as $id_auxiliar) {
                                    if (!empty($id_auxiliar)) {
                                        $nombre_aux = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $id_auxiliar . "'");
                                        $array_nom_corte = explode("..", $nombre_aux);
                                        $array_nombre_aux[] = $array_nom_corte[0];
                                    }
                                }
                                if (count($array_nombre_aux) > 0) {
                                    $nombres_aux = implode(",", $array_nombre_aux);
                                    $predio_socio .= " (" . $nombres_aux . ")";
                                }
                            }
                        }

                        // PARA EL COUNTRY MOSTRAMOS EL TIPO SOCIO SI ES CANJE deshabilitada temporalmente

                        if ($IDClub == 44 && !empty($Agenda)) :
                            $TipoSocio =  $dbo->getFields("Socio", "TipoSocio", "IDSocio =  $row_reserva_elemento[IDSocio]");
                            if (strtoupper($TipoSocio) == "CANJE") :
                                $row_reserva_elemento["NombreSocio"] .= " ($TipoSocio)";
                            endif;

                            // SACAMOS LOS INVITADOS DE LA RESERVA
                            $sql_invitados = "SELECT Nombre, IDSocio FROM ReservaGeneralInvitado WHERE IDReservaGeneral  = '" . $row_reserva_elemento["IDReservaGeneral"] . "'";
                            $qry_invitado = $dbo->query($sql_invitados);

                            $fila = $dbo->rows($qry_invitado);
                            if ($fila > 0) :
                                $invitados = "Inv.(";
                                while ($invitado = $dbo->fetchArray($qry_invitado)) {
                                    if ($invitado[IDSocio] == 0 || $invitado[IDSocio] == "") {
                                        $tipo = "/ Invitado Externo";
                                    } else {
                                        $tipo = "/ Invitado Socio";
                                    }

                                    if (!empty($invitado[Nombre])) {
                                        $invitados .= $invitado[Nombre] . $tipo;
                                        if ($fila > 1)
                                            $invitados .= "-";
                                    } else {
                                        $invitados .= $dbo->getFields("Socio", "Nombre", "IDSocio = $invitado[IDSocio]") . $tipo;
                                        if ($fila > 1)
                                            $invitados .= "-";
                                    }
                                }
                                $invitados .= ")\n";
                            endif;
                        endif;


                        $nombre_tomo_reserva = $row_reserva_elemento["NombreSocio"] . " " .  $DatoAccion . $Vacunado . " " . $predio_socio . " " . $invitados;

                    endif;

                    $array_hora_reservada[] = $row_reserva_elemento["Hora"];
                    $array_socio[$row_reserva_elemento["Hora"]] = $row_reserva_elemento;
                    // Si la reserva fue tomada para algun beneficiario muestro el nombre del beneficiario
                    if ($row_reserva_elemento["IDSocioBeneficiario"]) :
                        if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) :
                            $nombre_reserva = $LabelPersonalizado;
                        else :
                            $nombre_reserva = "Benef. " . $row_reserva_elemento["NombreBeneficiario"] . $predio_socio;
                        endif;
                    elseif ($row_reserva_elemento["IDInvitadoBeneficiario"]) :
                        if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) :
                            $nombre_reserva = $LabelPersonalizado;
                        else :
                            $nombre_reserva = "Inv. " . $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva_elemento["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva_elemento["IDInvitadoBeneficiario"] . "'") . " " . " " . $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_reserva_elemento["IDInvitadoBeneficiario"] . "'") . " por " . $nombre_tomo_reserva;
                        endif;
                    else :
                        $nombre_reserva = $tipo_reserva . $nombre_tomo_reserva;
                    endif;

                    if ($row_reserva_elemento["IDEstadoReserva"] == 3) :
                        $nombre_reserva = "En proceso de reserva";
                    endif;
                    $tipo_reserva_empl = "";

                    if ((int) $row_reserva_elemento["IDServicioTipoReserva"] > 0) {
                        $nombre_tipo_reserva = $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row_reserva_elemento["IDServicioTipoReserva"] . "'");
                    } else {
                        $nombre_tipo_reserva = "";
                    }

                    if ($Agenda == "S") :
                        //$tipo_reserva_empl = "";
                        //$nombre_tipo_reserva =  $dbo->getFields( "ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row_reserva_elemento[ "IDServicioTipoReserva" ] . "'" ) ;

                        if (!empty($nombre_tipo_reserva)) {
                            $tipo_reserva_empl = " / " . $nombre_tipo_reserva;
                        }

                        //Vuelvo a consultar el servicio ya que un elemento puede ser de varios servicios y se debe verificar de nuevo la reserva actual de cual servicio es
                        $id_servicio_maestro_consulta = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva_elemento["IDServicio"] . "'");
                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro_consulta . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $datos_servicio_mestro["Nombre"];
                        }

                        $tipo_reserva_empl .= "(" . $nombre_servicio_personalizado . ")";

                        if (!empty($row_reserva_elemento["Observaciones"])) {
                            $tipo_reserva_empl .= $row_reserva_elemento["Observaciones"];
                        }
                    endif;

                    //especial bttcc al socio canje mostrar Observacion
                    if ($row_reserva_elemento["IDSocio"] == 181191 && $Agenda != "S") {
                        $tipo_reserva_empl = $row_reserva_elemento["Observaciones"];
                    }

                    if ($Agenda == "S") {
                        //respuesta a preguntas personalizadas al reservar
                        $sql_respuesta_perso = "SELECT Valor,IDServicioCampo FROM  ReservaGeneralCampo WHERE IDReservaGeneral = '" . $row_reserva_elemento["IDReservaGeneral"] . "'";
                        $r_respuesta_perso = $dbo->query($sql_respuesta_perso);
                        while ($row_respuesta_perso = $dbo->fetchArray($r_respuesta_perso)) {
                            $pregunta_serv = $dbo->getFields("ServicioCampo", "Nombre", "IDServicioCampo = '" . $row_respuesta_perso["IDServicioCampo"] . "'");
                            $tipo_reserva_empl .= $pregunta_serv . ":" . $row_respuesta_perso["Valor"];
                        }
                    }

                    $datos_servicio_config = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva_elemento["IDServicio"] . "' ", "array");
                    if ($datos_servicio_config["PopInvitados"] == "N") {
                        $IDReservaPop = "";
                    } else {
                        $IDReservaPop = $row_reserva_elemento["IDReservaGeneral"];
                    }
                    $TextoAdicionales = "";
                    if ($IDClub == 16 && $IDServicio == 10251) :
                        $SQLAdicionales = "SELECT Valores FROM `ReservaGeneralAdicional` WHERE `IDReservaGeneral` = $row_reserva_elemento[IDReservaGeneral]";
                        $QRYAdicionales = $dbo->query($SQLAdicionales);
                        while ($Datos = $dbo->fetchArray($QRYAdicionales)) :
                            $TextoAdicionales .= "\n$Datos[Valores]";
                        endwhile;
                    endif;

                    //$IDReservaPop=$row_reserva_elemento[ "IDReservaGeneral" ];

                    $array_socio[$row_reserva_elemento["Hora"]]["NombreSocio"] = $nombre_reserva . $tipo_reserva_empl . $TextoAdicionales;
                    $array_socio[$row_reserva_elemento["Hora"]]["IDSocio"] = $row_reserva_elemento["IDSocio"];
                    if ((int) $row_reserva_elemento["IDTipoModalidadEsqui"] > 0) {
                        $NombreTipoModalidad = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva_elemento["IDTipoModalidadEsqui"] . "'");
                    } else {
                        $NombreTipoModalidad = "";
                    }
                    $array_socio[$row_reserva_elemento["Hora"]]["ModalidadEsquiSocio"] = $NombreTipoModalidad;
                    $array_socio[$row_reserva_elemento["Hora"]]["IDReservaGeneral"] = $IDReservaPop;
                    $array_socio[$row_reserva_elemento["Hora"]]["IDSocioBeneficiario"] = $row_reserva_elemento["IDSocioBeneficiario"];
                    $array_socio[$row_reserva_elemento["Hora"]]["IDInvitadoBeneficiario"] = $row_reserva_elemento["IDInvitadoBeneficiario"];
                    $array_socio[$row_reserva_elemento["Hora"]]["Cumplida"] = $row_reserva_elemento["Cumplida"];
                    $array_socio[$row_reserva_elemento["Hora"]]["IDUsuarioCumplida"] = $row_reserva_elemento["IDUsuarioCumplida"];
                    $array_socio[$row_reserva_elemento["Hora"]]["Tipo"] = $row_reserva_elemento["Tipo"];
                    $array_socio[$row_reserva_elemento["Hora"]]["TipoReserva"] = $nombre_tipo_reserva;
                }

                //Horas generales del servicio
                /*
                    $horaInicial=$r["HoraDesde"];
                    $minutoAnadir=$r["IntervaloHora"];
                    $hora_final = strtotime( $r["HoraHasta"] );
                    $hora_actual = $r["HoraDesde"];
                     */

                $dia_fecha = date('w', strtotime($Fecha));

                if (empty($Admin)) {
                    $condicion_dispo_solo_admin = " and SoloAdmin <> 'S'";
                }

                //Verifico que no tenga cierre el elemento en esta fecha
                $verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento);

                //para bellavista los sabados nadie puede tomar reservas por el app en las horas de la mañana

                if (empty($verifica_abierto_servicio)) {

                    //Verifico si tene disponibilidad  general el elemento

                    //$sql_dispo_elemento_gral = "SELECT HoraHasta,HoraDesde,IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%|" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin;
                    //$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                    $sql_dispo_elemento_primera = "Select HoraHasta,HoraDesde,IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%|" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin . " Order by HoraDesde";
                    $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
                    $contador_serv_disp = 0;
                    if ($dbo->rows($qry_dispo_elemento_primera) > 0) {


                        $verifica_disponibilidad_general = 1;
                        while ($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_primera)) {
                            if ($contador_serv_disp == 0) {
                                $horaInicial_reserva = $Fecha . " " . $row_dispo_elemento_primera["HoraDesde"];
                            }

                            $contador_serv_disp++;
                            $horaInicial = $row_dispo_elemento_gral["HoraDesde"];

                            $datos_reg_dispo = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");

                            //$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
                            //$minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                            $minutoAnadir = $datos_reg_dispo["Intervalo"];

                            // Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
                            if ($Fecha == date("Y-m-d")) :
                                //$medicion_tiempo = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                //$valor_tiempo_anticipacion = (int) $dbo->getFields("Disponibilidad", "Anticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                                $medicion_tiempo = $datos_reg_dispo["MedicionTiempoAnticipacion"];
                                $valor_tiempo_anticipacion = (int) $datos_reg_dispo["Anticipacion"];

                                if ($medicion_tiempo == "Horas") :
                                    $valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
                                elseif ($medicion_tiempo == "Dias") :
                                    $valor_tiempo_anticipacion = 0;
                                endif;
                            else :
                                $valor_tiempo_anticipacion = 0;
                            endif;

                            //Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
                            //$medicion_tiempo_anticipacion = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                            //$valor_anticipacion_turno = (int) $dbo->getFields("Disponibilidad", "AnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                            $medicion_tiempo_anticipacion = $datos_reg_dispo["MedicionTiempoAnticipacionTurno"];
                            $valor_anticipacion_turno = (int) $datos_reg_dispo["AnticipacionTurno"];

                            switch ($medicion_tiempo_anticipacion):
                                case "Dias":
                                    $minutos_anticipacion_turno = (60 * 24) * $valor_anticipacion_turno;
                                    break;
                                case "Horas":
                                    $minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
                                    break;
                                case "Minutos":
                                    $minutos_anticipacion_turno = $valor_anticipacion_turno;
                                    break;
                                default:
                                    $minutos_anticipacion_turno = 0;
                            endswitch;

                            //Si es administrador no tiene limite de anticipacion
                            if ($Admin == "S") {
                                $valor_tiempo_anticipacion = 0;
                                $minutos_anticipacion_turno = 0;
                            }

                            //Consulto hace una hora para mostrar los turnos anterior segun solicitud de lagartos
                            $id_servicio_inicial = $datos_servicio_mestro["IDServicioInicial"];

                            //para psicina uruguat se debe mostrar las hotras anteriores
                            if ($IDClub == 125 && $IDServicio == 23009) {
                                $minutoAnadir = 360;
                            }

                            //si es work place arrayanes mostrar horas pasadas
                            if (($IDClub == 11 && $IDServicio == 12854) || ($IDClub == 25 && $IDServicio == 1398)) {
                                $MostrarTodoDia = "S";
                            }

                            //$hace_una_hora = strtotime ( '-1 hour' , strtotime ( date("Y-m-d H:i:s") ) ) ;
                            $hace_una_hora = strtotime('-' . $minutoAnadir . ' minutes', strtotime(date("Y-m-d H:i:s")));

                            if ($Fecha == date("Y-m-d")) :
                                $hora_real = date('Y-m-d H:i:s', $hace_una_hora);
                            else :
                                $hora_real = date('Y-m-d H:i:s');
                            endif;

                            // Solo aplica lo de 1 hora antes cuando no es servicio de clases
                            if ($id_servicio_inicial == "1") :
                            //$hora_real = date('Y-m-d H:i:s');
                            endif;

                            //Cunado se consulta desde la agenda del app de empleados muestro todo el dia
                            if ($Agenda == "S" || $MostrarTodoDia == "S") :
                                $hora_real = date('Y-m-d 05:00:00');
                            endif;

                            if ($Agenda == "S" || $MostrarTodoDia == "S") :
                                $hora_real = $Fecha . " " . date('05:00:00');
                            endif;

                            // Si tiene configurado el parametro de anticipacion turno no tomo en cuenta lo de dejar ala hora actual 1 hora antes
                            if ($minutos_anticipacion_turno > 0 && $Agenda != "S") {
                                $hora_real = date('Y-m-d H:i:s');
                            }


                            $hora_empezar_reserva = strtotime('-' . $valor_tiempo_anticipacion . ' minute', strtotime($horaInicial_reserva));
                            //$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
                            $hora_actual_sistema = strtotime($hora_real);

                            $hora_final = strtotime($row_dispo_elemento_gral["HoraHasta"]);
                            $hora_actual = strtotime($row_dispo_elemento_gral["HoraDesde"]);

                            $primer_horario = 0;
                            $primer_horario_disponible = "";
                            $verifica_abierto_servicio_hora = "";

                            $datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $IDElemento . "' ", "array");



                            while ($hora_actual <= $hora_final) :



                                $hora_fecha_actual = $Fecha . " " . date('H:i:s', $hora_actual);
                                $hora_puede_reservar = strtotime('+' . $minutos_anticipacion_turno . ' minute', strtotime($hora_real));

                                if ($Agenda == "S") :
                                    $hora_puede_reservar = strtotime('+0 minute', strtotime($hora_real));
                                endif;

                                /*
                                    if($IDServicio==1116){
                                    echo "Condi " .  date("Y-m-d H:i:s",$hora_puede_reservar) ."<=".$hora_fecha_actual;
                                    echo "ANTI: " . $minutos_anticipacion_turno;
                                    if ( strtotime( $hora_fecha_actual ) >= strtotime( $hora_real ) && $hora_puede_reservar <= strtotime( $hora_fecha_actual ) ):
                                    echo "SI:".$hora_actual;
                                    endif;
                                    }
                                     */

                                $zonahoraria = date_default_timezone_get();
                                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                                $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

                                /*****************************************************************************************************
                                    Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
                                    Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
                                 ******************************************************************************************************/
                                if (strtotime($hora_fecha_actual) >= strtotime($hora_real) && $hora_puede_reservar <= strtotime($hora_fecha_actual)) :
                                    if ($IDServicio == 1116) {
                                        echo "FEC " . $fecha_disponible;
                                    }



                                    //valido si ya paso el tiempo limite despues de pasado el turno aun se puede reservar, ej: el de las 4 lo puedo tomar hasta las 4:10
                                    //$minutoPosterior = (int) $dbo->getFields("Disponibilidad", "MinutoPosteriorTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                    $minutoPosterior = (int) $datos_reg_dispo["MinutoPosteriorTurno"];
                                    $flag_hora_disponible_limite = 0;
                                    if ($minutoPosterior > 0 && $Fecha == date("Y-m-d") && (empty($Agenda) && empty($MostrarTodoDia))) :
                                        $tiempo_limite_reserva = strtotime('+' . $minutoPosterior . ' minutes', strtotime($hora_fecha_actual));
                                        if (strtotime(date("Y-m-d H:i:s")) >= $tiempo_limite_reserva) :
                                            $flag_hora_disponible_limite = 1;
                                        endif;
                                    endif;

                                    if ($flag_hora_disponible_limite == 0) :

                                        if (strlen($horaInicial) != 8) :
                                            $horaInicial .= ":00";
                                        endif;

                                        $flag_hora_disponible = 0;
                                        // Si el servicio es una clase y necesita reservar cancha verifico que exista al menos un elemento (cancha) disponible para mostrar la hora

                                        if ($id_servicio_cancha > 0) :
                                            // Consulto el servicio del club asociado a este servicio maestro
                                            //$IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");
                                            // Valido si existe una cancha disponible en el horario de la clase
                                            $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub, $IDServicioCanchaClub, $Fecha, $horaInicial, $IDElemento);
                                            if (empty($IDElemento_cancha)) :
                                                $flag_hora_disponible = 1;
                                            endif;
                                        endif;

                                        $hora_mostrar = $horaInicial;
                                        $hora["Hora"] = $horaInicial;

                                        //echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
                                        //exit;

                                        //$datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");
                                        $datos_disponibilidad = $datos_reg_dispo;

                                        // Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia
                                        $multiples_cupos = "N";
                                        $IDElementoConsultar = $IDElemento;



                                        if ((int) $datos_disponibilidad["Cupos"] > 1) {
                                            $multiples_cupos = "S";
                                            //Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
                                            $cupos_reservados = SIMWebService::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial);
                                            //Si el elemento ya tiene otra reserva en otro servicio marco esta como ya revarda asi tenga cupos disponibles
                                            $array_otro_elemento = explode(",", $condicion_multiple_elemento);
                                            if (count($array_otro_elemento) > 1) : //Si es mas de 1 quiere decir que el elemento esta en mas de un servicio y hago la verificacion
                                                foreach ($array_otro_elemento as $id_elemento_multiple) :
                                                    if ($id_elemento_multiple != $IDElemento && !empty($id_elemento_multiple)) :
                                                        //averiguo el servicio
                                                        $IDServicioMult = $dbo->getFields("ServicioElemento", "IDServicio", "IDServicioElemento = '" . $id_elemento_multiple . "'");
                                                        $num_otras_reservas = SIMWebService::valida_cupos_disponibles($IDClub, $IDServicioMult, $id_elemento_multiple, $Fecha, $horaInicial);

                                                        //$sql_reserva_elemento_multp = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento in (" . $id_elemento_multiple . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' ";
                                                        //$qry_reserva_elemento_mult = $dbo->query($sql_reserva_elemento_multp);
                                                        //$num_otras_reservas = $dbo->rows($qry_reserva_elemento_mult);                                                        
                                                        $cupos_reservados += $num_otras_reservas;
                                                        if ($num_otras_reservas > 0 && $CuposporHora >= 1) :
                                                            $multiples_cupos = "N";
                                                        endif;
                                                    endif;
                                                endforeach;
                                            endif;


                                            $cupo_disponibles = "";
                                            //$cupos_reservados += $array_reservas_por_hora[$horaInicial][$IDElemento];


                                            //Valido si todavia existe cupo en esta hora                                            
                                            if ($cupos_reservados < $datos_disponibilidad["Cupos"]) :
                                                $cupo_total = "N"; // aun hay cupos disponibles
                                                $cupos_disponibles = (int) $datos_disponibilidad["Cupos"] - (int) $cupos_reservados;
                                                if ($datos_servicio_actual["MostrarCupoDisponible"] != "N") {
                                                    $label_cupo_disponibles = " " . $cupos_disponibles . " Cupos.";
                                                } else {
                                                    $label_cupo_disponibles = " ";
                                                }
                                            else :
                                                $cupo_total = "S"; // ya no hay cupos
                                            endif;
                                        } else {
                                            $label_cupo_disponibles = "";
                                        }

                                        //Cuando es Agenda lo muestro como si tuviera el cupo total para que en el app aparezca lo sinscritos hasta el momento
                                        if ($Agenda == "S") :
                                            $cupo_total = "S";
                                        endif;

                                        //Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
                                        $hora_real_momento = strtotime(date("Y-m-d H:i:s")); //calculo de nuevo la fecha y hora real del sistema ya que pudo ser modificada con un tiempo de anticipacion anteriormente
                                        $IDAuxiliarReserva = "";

                                        // Si tiene Auxiliar y es admin mustro el auxiliar seleccionado
                                        $NombreAuxiliarReserva = "";
                                        $nombre_auxiliar = "";
                                        if (!empty($Admin)) :

                                            $IDAuxiliarReserva = $dbo->getFields("ReservaGeneral", "IDAuxiliar", "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'");
                                            if (!empty($IDAuxiliarReserva)) :
                                                $array_aux = explode(",", $IDAuxiliarReserva);
                                                if (count($array_aux) > 0) :
                                                    foreach ($array_aux as $id_aux) :
                                                        if (!empty($id_aux)) {
                                                            $nombre_auxiliar .= $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $id_aux . "'") . " - ";
                                                        }

                                                    endforeach;
                                                endif;
                                                $NombreAuxiliarReserva = "<span style='color:#696'> / " . $nombre_auxiliar . "</span>";
                                            endif;

                                            //Si es una clase consulto el padre de la reserva
                                            $TipoReservaA = $dbo->getFields("ReservaGeneral", "Tipo", "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'");

                                            if ($TipoReservaA == "Automatica") :
                                                $IDBeneficiario = $dbo->getFields("ReservaGeneral", "IDSocioBeneficiario", "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'");
                                                /*
                                                    if(!empty($IDBeneficiario)):
                                                    $condicion_benef = " and IDBeneficiario = '".$IDBeneficiario."' ";
                                                    else:
                                                    $condicion_benef = " and IDBeneficiario = '0' ";
                                                    endif;
                                                     */
                                                $sql_id_reserva_padre_e = "SELECT IDReservaGeneral FROM ReservaGeneralAutomatica WHERE  IDReservaGeneralAsociada = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "' Limit 1";
                                                $qry_id_reserva_padre_e = $dbo->query($sql_id_reserva_padre_e);
                                                $row_id_reserva_padre_e = $dbo->fetchArray($qry_id_reserva_padre_e);

                                                //$sql_reserva_padre_e = "SELECT * FROM ReservaGeneral WHERE Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '".$hora["Hora"]."' and Tipo <> 'Automatica' and IDSocio = '".$array_socio["$horaInicial"]["IDSocio"]."' ".$condicion_benef ." ORDER BY Hora Limit 1";
                                                $sql_reserva_padre_e = "SELECT IDReservaGeneral,IDServicio FROM ReservaGeneral WHERE IDReservaGeneral = '" . $row_id_reserva_padre_e["IDReservaGeneral"] . "' Limit 1";
                                                $qry_reserva_padre_e = $dbo->query($sql_reserva_padre_e);
                                                $row_reserva_padre_e = $dbo->fetchArray($qry_reserva_padre_e);
                                                $id_servicio_maestro_reserva = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva_padre_e["IDServicio"] . "'");
                                                $id_servicio_cancha_e = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro_reserva . "'");
                                                //Si la reserva es una clase agrego la palabra clase
                                                if ($id_servicio_cancha_e > 0) :
                                                    //Si se hizo una reserva automatica muestro el nombre del elemento reservado
                                                    $id_elemento_reservado = $dbo->getFields("ReservaGeneral", "IDServicioElemento", "IDReservaGeneral = '" . $row_reserva_padre_e["IDReservaGeneral"] . "'");
                                                    $elemento_reservado = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $id_elemento_reservado . "'");
                                                    if (!empty($elemento_reservado)) :
                                                        $NombreAuxiliarReserva .= "<span style='color:#696'> / " . $elemento_reservado . "</span>";
                                                    endif;
                                                endif;
                                            endif;

                                            //verifico si tiene una reserva automatica para mostrarla
                                            $id_reserva_automatica = $dbo->getFields("ReservaGeneralAutomatica", "IDReservaGeneralAsociada", " IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'");
                                            if (!empty($id_reserva_automatica)) :
                                                $detalle_reserva_auto = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $id_reserva_automatica . "' ", "array");
                                                $NombreAuxiliarReserva .= "<span style='color:#696'> / " . $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $detalle_reserva_auto["IDServicioElemento"] . "'") . "</span>";
                                            endif;
                                        endif;

                                        if ((in_array($horaInicial, $array_hora_reservada) && $multiples_cupos == "N") || ($hora_real_momento < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {

                                            if ($Agenda == "S") :
                                                //Si es para agenda no ponto las etiquetas html
                                                $NombreAuxiliarReserva = strip_tags($NombreAuxiliarReserva)  .  " " . $dbo->getFields("Socio", "MotivoBloqueoZeus", "IDSocio = '" . $array_socio["$horaInicial"]["IDSocio"] . "'");
                                            endif;

                                            $hora["Disponible"] = "N";
                                            $hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"] . $NombreAuxiliarReserva;
                                            $hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
                                            $hora["ModalidadEsquiSocio"] = $array_socio["$horaInicial"]["ModalidadEsquiSocio"];
                                            $hora["TipoReserva"] = $array_socio["$horaInicial"]["TipoReserva"];
                                            $hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];
                                            $hora["IDSocioBeneficiario"] = $array_socio["$horaInicial"]["IDSocioBeneficiario"];
                                            $hora["IDInvitadoBeneficiario"] = $array_socio["$horaInicial"]["IDInvitadoBeneficiario"];
                                            $array_reserva_hora_elem[$horaInicial]++;

                                            //verifo si el elemento lo han reservado en horas anteriorees para
                                            if ($primer_horario == 0 || $primer_horario == 1 || $primer_horario == 2) :
                                                $primer_horario_disponible = "reservado_socio";
                                            endif;
                                        } else {

                                            //Verifico que no tenga fecha de cierre en esta hora
                                            $verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento, $horaInicial);

                                            //Validacion Especial y temporal cuando es una clase de lagartos y la hora es mayor a 8pm no se muestra las 7 8 y 9 am del dia siguiente a menos que la hora de las 7 esté tomada

                                            if (($IDClub == 7 || $IDClub == 8) && ($IDServicio == 43 || $IDServicio == 41) && empty($verifica_abierto_servicio_hora)) { //Clases tenis especial
                                                $fecha_hoy_sistema = date('Y-m-d');
                                                $fecha_manana = strtotime('+1 day', strtotime($fecha_hoy_sistema));
                                                $fecha_corta_manana = date('Y-m-d', $fecha_manana);

                                                //if ( $horaInicial == "06:00:00" || $horaInicial == "07:00:00" || $horaInicial == "08:00:00" || $horaInicial == "09:00:00" ) {
                                                if ($horaInicial == "06:00:00" || $horaInicial == "07:00:00" || $horaInicial == "08:00:00") {
                                                    //echo $Fecha."==".$fecha_corta_manana;
                                                    if ($Fecha == date("Y-m-d") || ($Fecha == $fecha_corta_manana && date("H") >= 20)) {
                                                        if ($Fecha == date("Y-m-d")) {
                                                            //verifico si ya tiene algo reservado antes de la hora si es asi no bloque los horarios
                                                            $sql_reserva_elemento_antes = "SELECT * FROM ReservaGeneral WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora <= '" . $hora["Hora"] . "' and IDServicioElemento = '" . $IDElemento . "'  ORDER BY Hora ";
                                                            $result_reserva_elemento_antes = $dbo->query($sql_reserva_elemento_antes);
                                                            if ($dbo->rows($result_reserva_elemento_antes) > 0) :
                                                                $verifica_abierto_servicio_hora = "";
                                                            else :
                                                                $verifica_abierto_servicio_hora = "Hora no disponible: " . $Fecha . " Motivo:No disponible.";
                                                            endif;
                                                        } elseif ($primer_horario_disponible != "reservado_socio") { // Si la primera hora está reservada, no bloqueo estos horarios
                                                            $verifica_abierto_servicio_hora = "Hora no disponible: " . $Fecha . " Motivo:No disponible.";
                                                        }
                                                    }
                                                }
                                            }



                                            //Especial AeroClub si un avion tiene reserva encualquier hora del dia se bloquea en los demas Servicios
                                            $flag_avion_disponible = 0;
                                            if ($IDClub == 36 && ($IDServicio == 3608 || $IDServicio == 4371)) :
                                                // SI esta reservado en crucero lo bloqueo en local
                                                if ($IDServicio == 4371) { // Crucero
                                                    $sql_reserva_elemento_avion = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento in (" . $condicion_multiple_elemento . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and IDServicio = '3608' Limit 1 ";
                                                } else { // Local
                                                    $sql_reserva_elemento_avion = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento in (" . $condicion_multiple_elemento . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and IDServicio = '4371' Limit 1 ";
                                                }

                                                $r_reserva_elemento_avion = $dbo->query($sql_reserva_elemento_avion);
                                                if ($dbo->rows($r_reserva_elemento_avion) > 0) :
                                                    $flag_avion_disponible = 1;
                                                endif;
                                            endif;

                                            if (!empty($verifica_abierto_servicio_hora)) :

                                                //extraigo la razon
                                                $mensaje_cierre = explode(":", $verifica_abierto_servicio_hora);

                                                if (!empty($mensaje_cierre[3])) {
                                                    $hora["MostrarAlertaNoDisponible"] = "S";
                                                    $hora["TextoAlertaNoDisponible"] = $mensaje_cierre[3];
                                                }

                                                $hora["Disponible"] = "N";
                                                $hora["Socio"] = $mensaje_cierre[2];
                                                $hora["IDSocio"] = "";
                                                $hora["ModalidadEsquiSocio"] = "";
                                                $hora["TipoReserva"] = "";
                                                $hora["IDReserva"] = "";
                                            elseif ($flag_hora_disponible == 1) :
                                                $hora["Disponible"] = "N";
                                                $hora["Socio"] = "No hay cancha disponible para clase.";
                                                $hora["IDSocio"] = "";
                                                $hora["ModalidadEsquiSocio"] = "";
                                                $hora["TipoReserva"] = "";
                                                $hora["IDReserva"] = "";
                                            elseif ($flag_avion_disponible == 1) :
                                                $hora["Disponible"] = "N";
                                                $hora["Socio"] = "Avion ya reservado";
                                                $hora["IDSocio"] = "";
                                                $hora["ModalidadEsquiSocio"] = "";
                                                $hora["TipoReserva"] = "";
                                                $hora["IDReserva"] = "";
                                            else :

                                                //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
                                                if (!empty($IDTipoReserva)) :
                                                    $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");
                                                    $cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];
                                                else :
                                                    $cantidad_turnos = 1;
                                                endif;

                                                if ($cantidad_turnos > 1) :
                                                    //verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
                                                    $array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $horaInicial, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos);
                                                endif;
                                                if (count($array_disponible) != (int) ($cantidad_turnos - 1) && $cantidad_turnos > 1) :
                                                    $hora["Disponible"] = "N";
                                                    $hora["Socio"] = "No Disponible.";
                                                    $hora["IDSocio"] = "";
                                                    $hora["ModalidadEsquiSocio"] = "";
                                                    $hora["IDReserva"] = "";
                                                else :


                                                    //Si permite multiples cupos y hay cupos disponibles pongo la hora como disponible
                                                    if ($multiples_cupos == "S" && $cupo_total == "S") :
                                                        if ($Agenda == "S" || $datos_servicio_config["VerInscitosClaseApp"] == "S") :
                                                            unset($array_socio_multiple);
                                                            //Consulto nombre de socios incritos
                                                            $sql_socio_multiple = "SELECT Observaciones,IDReservaGeneral,IDSocio,NombreSocio,NombreBeneficiario,IDAuxiliar,IDInvitadoBeneficiario
								                                                                                FROM ReservaGeneral RG
								                                                                                WHERE RG.IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and IDEstadoReserva = 1 and Hora = '" . $horaInicial . "' and IDServicioElemento = '" . $IDElemento . "'";
                                                            $result_socio_multiple = $dbo->query($sql_socio_multiple);
                                                            $tipo_reserva_empl = "";
                                                            while ($row_socio_multiple = $dbo->fetchArray($result_socio_multiple)) :

                                                                if ($Agenda == "S") {
                                                                    $tipo_reserva_empl = $row_socio_multiple["Observaciones"];
                                                                }

                                                                if ($IDClub == 106 || $IDClub == 44) :

                                                                    $InvitadosReserva = "";
                                                                    $sql_invitados = "SELECT Nombre, IDSocio FROM ReservaGeneralInvitado WHERE IDReservaGeneral  = '" . $row_socio_multiple["IDReservaGeneral"] . "'";
                                                                    $qry_invitado = $dbo->query($sql_invitados);

                                                                    $fila = $dbo->rows($qry_invitado);
                                                                    if ($fila > 0) :
                                                                        $InvitadosReserva = "Inv.(";
                                                                        while ($invitado = $dbo->fetchArray($qry_invitado)) {
                                                                            if ($invitado[IDSocio] == 0 || $invitado[IDSocio] == "") {
                                                                                $tipo = "/ Invitado Externo";
                                                                            } else {
                                                                                $tipo = "/ Invitado Socio";
                                                                            }

                                                                            if (!empty($invitado[Nombre])) {
                                                                                $InvitadosReserva .= $invitado[Nombre] . $tipo;
                                                                                if ($fila > 1) {
                                                                                    $InvitadosReserva .= "-";
                                                                                }
                                                                            } else {
                                                                                $InvitadosReserva .= $dbo->getFields("Socio", "Nombre", "IDSocio = $invitado[IDSocio]") . $tipo;
                                                                                if ($fila > 1) {
                                                                                    $InvitadosReserva .= "-";
                                                                                }
                                                                            }
                                                                        }
                                                                        $InvitadosReserva .= ")\n";
                                                                    endif;
                                                                    // PARA EL COUNTRY MOSTRAMOS EL TIPO SOCIO SI ES CANJE
                                                                    if ($IDClub == 44) :
                                                                        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = $row_socio_multiple[IDSocio]");
                                                                        if (strtoupper($TipoSocio) == "CANJE") :
                                                                            $row_socio_multiple["NombreSocio"] .= " ($TipoSocio)";
                                                                        endif;
                                                                    endif;

                                                                    $array_socio_multiple[] = $row_socio_multiple["NombreSocio"] . $tipo_reserva_empl . " " . $InvitadosReserva . $nombre_auxiliar;

                                                                elseif ($IDClub == 35) :

                                                                    $predio_socio = $dbo->getFields("Socio", "Predio", "IDSocio = '" . $row_socio_multiple["IDSocio"] . "'");
                                                                    $predio_socio .= " / " . $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $row_reserva_elemento["IDSocio"] . "'");

                                                                    if (!empty($Agenda)) :
                                                                        // BUSCAMOS SI ESTA VACUNADO
                                                                        $SQLVacuna2 = "SELECT IDVacunado FROM Vacunado WHERE IDSocio = $row_reserva_elemento[IDSocio]";
                                                                        $QRYVacuna2 = $dbo->query($SQLVacuna2);
                                                                        if ($dbo->rows($QRYVacuna2) > 0) :
                                                                            $Vacunado = "(V)";
                                                                        else :
                                                                            $Vacunado = "(NV)";
                                                                        endif;
                                                                    endif;

                                                                    $array_socio_multiple[] = $row_socio_multiple["NombreSocio"] . " " . $Vacunado . $tipo_reserva_empl . " " . $predio_socio;

                                                                else :

                                                                    if ((int)$row_socio_multiple["IDInvitadoBeneficiario"] > 0) {

                                                                        $Invitado = $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_socio_multiple["IDInvitadoBeneficiario"] . "' and IDClub = '" . $IDClub . "' ");

                                                                        if (empty($Invitado)) :
                                                                            $Invitado = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_socio_multiple["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_socio_multiple["IDInvitadoBeneficiario"] . "'");
                                                                        endif;

                                                                        $array_socio_multiple[] = " Inv. " . $Invitado;
                                                                    } else {
                                                                        if (!empty($row_socio_multiple["NombreBeneficiario"])) {
                                                                            $array_socio_multiple[] = "Benef." . $row_socio_multiple["NombreBeneficiario"] . $tipo_reserva_empl;
                                                                        } else {
                                                                            $array_socio_multiple[] = $row_socio_multiple["NombreSocio"] . $tipo_reserva_empl;
                                                                        }
                                                                    }



                                                                endif;

                                                                $IDAuxiliarReserva .= $row_socio_multiple["IDAuxiliar"];

                                                            endwhile;
                                                            if (count($array_socio_multiple) > 0) :
                                                                $mensaje_cupo_lleno = implode(" / ", $array_socio_multiple);
                                                            else :
                                                                $mensaje_cupo_lleno = "Sin inscripciones";
                                                            endif;
                                                        else :
                                                            $mensaje_cupo_lleno = "Se llego al limite de cupos ";
                                                        endif;
                                                        $hora["Disponible"] = "N";
                                                        $hora["Socio"] = $mensaje_cupo_lleno;
                                                        $hora["IDSocio"] = "";
                                                        $hora["ModalidadEsquiSocio"] = "";
                                                        $hora["IDReserva"] = "";
                                                    else :
                                                        //Valido cuantos cupos por bloque son permitidos (pj pueden haber disponibles 4 masajistas pero solo hay dos camillas entonces el limite es 2 por bloque)
                                                        if ($CuposporHora > 0) :

                                                            if ($IDClub == 77) {
                                                                $condicion_tipo = " and IDServicioTipoReserva = '" . $IDTipoReserva . "' ";
                                                            }

                                                            //Especial cusezar si es sala vip se bloquea sala 1 y 2, si esta reservada 1 o 2 se bloque la vip
                                                            if ($IDServicio == 44907 || $IDServicio == 53181 || $IDServicio == 52968) {

                                                                switch ($IDServicio) {
                                                                    case "44907";
                                                                        $ElementoPrincipal = 17630;
                                                                        $ElementoSecundario1 = 19059;
                                                                        $ElementoSecundario2 = 17631;
                                                                        break;
                                                                    case "52968";
                                                                        $ElementoPrincipal = 20132;
                                                                        $ElementoSecundario1 = 20130;
                                                                        $ElementoSecundario2 = 20131;
                                                                        break;
                                                                    case "53181";
                                                                        $ElementoPrincipal = 20129;
                                                                        $ElementoSecundario1 = 20127;
                                                                        $ElementoSecundario2 = 20128;
                                                                        break;
                                                                }


                                                                $TodosElementos = $ElementoPrincipal . "," . $ElementoSecundario1 . "," . $ElementoSecundario2;

                                                                if ($IDElemento == $ElementoPrincipal) {
                                                                    $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' and IDServicioElemento in (" . $TodosElementos . ") " . $condicion_tipo;
                                                                    $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                                    if ((int) $dbo->rows($result_reserva_servicio) > 0 &&  $IDElemento == 17630) {
                                                                        $CuposporHora = 1;
                                                                    }
                                                                } elseif ($IDElemento == $ElementoSecundario1 || $IDElemento == $ElementoSecundario2) {
                                                                    $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' and IDServicioElemento in (" . $ElementoPrincipal . ") " . $condicion_tipo;
                                                                    $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                                    if ((int) $dbo->rows($result_reserva_servicio) > 0) {
                                                                        $CuposporHora = 1;
                                                                    }
                                                                }
                                                            } else {
                                                                //Consulto cuantas reservas hay en esta hora en este servicio
                                                                $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' " . $condicion_tipo;
                                                                $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                            }


                                                            //$num_otras_reservas = count($array_reserva_hora_elem[$horaInicial]);

                                                            $total_reservas_hora = (int) $dbo->rows($result_reserva_servicio) + $num_otras_reservas;
                                                        //echo $num_otras_reservas;
                                                        endif;

                                                        if ($CuposporHora > 0 && $total_reservas_hora >= $CuposporHora) :
                                                            $hora["Disponible"] = "N";
                                                            $hora["Socio"] = "Se llego al maximo por hora. ";
                                                            $hora["IDSocio"] = "";
                                                            $hora["ModalidadEsquiSocio"] = "";
                                                            $hora["IDReserva"] = "";
                                                        else :

                                                            $hora["Disponible"] = "S";
                                                            $hora["Socio"] = "" . $label_cupo_disponibles;
                                                            $hora["IDSocio"] = "";
                                                            $hora["ModalidadEsquiSocio"] = "";
                                                            $hora["IDReserva"] = "";
                                                            $hora["IDSocioBeneficiario"] = "";
                                                        endif;
                                                    endif;

                                                //if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
                                                //$hora["Disponible"] = "S";
                                                //$hora["Socio"] = "";
                                                //$hora["IDSocio"] = "";
                                                //$hora["ModalidadEsquiSocio"] = "";
                                                //$hora["IDReserva"] = "";

                                                //else:
                                                /*
                                                    $hora["Disponible"] = "N";
                                                    $hora["Socio"] = "Hora no disponible";
                                                    $hora["IDSocio"] = "";
                                                    $hora["IDReserva"] = "";
                                                     */
                                                //$hora["Disponible"] = "S";
                                                //$hora["Socio"] = "";
                                                //$hora["IDSocio"] = "";
                                                //$hora["IDReserva"] = "";
                                                //endif;

                                                endif;

                                            endif;
                                        }

                                        //Averiguo el numero de dias de anticipacion
                                        $hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];
                                        $hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];
                                        $hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];

                                        $hora["NumeroMinimoInvitadoClub"] = $datos_disponibilidad["NumeroMinimoInvitadoClub"];
                                        $hora["NumeroMinimoInvitadoExterno"] = $datos_disponibilidad["NumeroMinimoInvitadoExterno"];

                                        $hora["NumeroMinimoInvitadoClub"] = $datos_disponibilidad["NumeroMinimoInvitadoClub"];
                                        $hora["NumeroMinimoInvitadoExterno"] = $datos_disponibilidad["NumeroMinimoInvitadoExterno"];

                                        //Repeticion reserva
                                        $hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
                                        $hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];
                                        $hora["MedicionRepeticion"] = $datos_disponibilidad["MedicionRepeticion"];
                                        $hora["FechaFinRepeticion"] = $datos_disponibilidad["FechaFinRepeticion"];

                                        //Consulto los datos de georeferenciacion
                                        $datos_disponibilidad_geo = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_disponibilidad["IDDisponibilidad"] . "' ", "array");
                                        $hora["Georeferenciacion"] = $datos_reg_dispo["Georeferenciacion"];
                                        //Consulto los demas datos de la configuracion del servicio
                                        $datos_geo_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $IDServicio . "' ", "array");
                                        $hora["Latitud"] = $datos_servicio_actual["Latitud"];
                                        $hora["Longitud"] = $datos_servicio_actual["Longitud"];
                                        $hora["Rango"] = $datos_servicio_actual["Rango"];
                                        $hora["MensajeFueraRango"] = $datos_servicio_actual["MensajeFueraRango"];

                                        if (!empty($datos_servicio_actual["LabelDisponible"])) {
                                            $labeldisponible = $datos_servicio_actual["LabelDisponible"];
                                        } else {
                                            $labeldisponible = "Disponible";
                                        }

                                        $hora["LabelDisponible"] = $labeldisponible . $label_cupo_disponibles;

                                        $hora["IDElemento"] = $IDElemento;

                                        // Consulto las modalidades que pueda tener
                                        $nom_modalidad = array();
                                        $array_modalidad_elemento = SIMWebService::get_modalidades($IDClub, "", $IDElemento);

                                        if (count($array_modalidad_elemento) > 0) :
                                            foreach ($array_modalidad_elemento["response"] as $id_modalidad => $datos_modalidad) :
                                                $nom_modalidad[] = $datos_modalidad["Descripcion"];
                                            endforeach;
                                            $nombre_modalidad = implode("-", $nom_modalidad);
                                        endif;

                                        // FIn Modalidades

                                        //$datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $IDElemento . "' ", "array");

                                        if ($IDClub == 72 && $IDServicio == 8649 && $hora_mostrar >= '17:00:00') {
                                            $nombre_elemento_adicional = "En este horario no hay servicio de caddies";
                                        } else {
                                            $nombre_elemento_adicional = "";
                                        }

                                        $hora["NombreElemento"] = $datos_elemento["Nombre"] . " " . $nombre_elemento_adicional;
                                        $hora["IDUsuario"] = $IDUsuario;
                                        $hora["PermiteReservarUsuario"] = $PermiteReservarUsuario;

                                        $hora["ColorLetra"] = $datos_elemento["ColorLetra"];
                                        $hora["ColorFondo"] = $datos_elemento["ColorFondo"];

                                        if (!empty($datos_elemento["Foto"])) {
                                            $FotoElemento = ELEMENTOS_ROOT . $datos_elemento["Foto"];
                                        } else {
                                            $FotoElemento = "";
                                        }

                                        $hora["Foto"] = $FotoElemento;
                                        $hora["ModalidadElemento"] = $nombre_modalidad;
                                        $hora["MaximoInvitadosSalon"] = $datos_disponibilidad["MaximoInvitadosSalon"];
                                        $hora["OrdenElemento"] = $datos_elemento["Orden"];
                                        $hora["PermiteListaEspera"] = $datos_geo_servicio["PermiteListaEspera"];
                                        $hora["LabelTituloHora"] = $datos_servicio_actual["LabelTituloHora"];
                                        $hora["AccionSocio"] = $datos_servicio_actual["LabelTituloHora"];


                                        if ($datos_servicio_actual["MostrarDuracionTurno"] == "S") {
                                            $hora["LabelTituloHora"] .= "Duracion: " . $datos_disponibilidad["Intervalo"] . " minutos";
                                        }

                                        //si a la reserva ya se le marco cumplida no muesro el boton
                                        $MostrarAsistencia = "S";

                                        //if (($array_socio["$horaInicial"]["Cumplida"] == "S" || $array_socio["$horaInicial"]["Cumplida"] == "N" || $array_socio["$horaInicial"]["Cumplida"] == "P" || $array_socio["$horaInicial"]["Tipo"] == "Automatica") && $array_socio["$horaInicial"]["IDUsuarioCumplida"] != 9999999 && $datos_disponibilidad["Cupos"] <= 1) {
                                        if (($array_socio["$horaInicial"]["Cumplida"] == "S" || $array_socio["$horaInicial"]["Cumplida"] == "N" || $array_socio["$horaInicial"]["Cumplida"] == "P" || $array_socio["$horaInicial"]["Tipo"] == "Automatica") && $array_socio["$horaInicial"]["IDUsuarioCumplida"] != 9999999) {
                                            $MostrarAsistencia = "N";
                                        }

                                        $hora["MostrarBotonCumplida"] = $MostrarAsistencia;
                                        $hora["IDAuxiliar"] = $IDAuxiliarReserva;

                                        $response_inscritos = array();
                                        $datos_inscrito = array();



                                        // Es una clase grupal
                                        if ($datos_disponibilidad["Cupos"] > 1) {

                                            $hora["MostrarBotonInscritos"] = $datos_servicio_configuracion["MostrarBotonInscritos"];
                                            $hora["LabelBotonInscritos"] = $datos_servicio_configuracion["LabelBotonInscritos"];
                                            $sql_inscritos_hora = "SELECT RG.IDReservageneral,RG.NombreSocio, AccionSocio, RG.NombreBeneficiario, RG.IDSocio, RG.IDSocioBeneficiario
				                                                                                    FROM ReservaGeneral RG
				                                                                                    WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "'
				                                                                                    and IDEstadoReserva = 1  and Hora = '" . $horaInicial . "' and IDServicioElemento = '" . $IDElemento . "' and IDClub = '" . $IDClub . "'";

                                            $r_inscritos_hora = $dbo->query($sql_inscritos_hora);
                                            while ($row_inscritos_hora = $dbo->fetchArray($r_inscritos_hora)) {
                                                $InvitadosReserva = "";
                                                $datos_inscrito["IDReserva"] = $row_inscritos_hora["IDReservageneral"];

                                                $sql_Invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = $row_inscritos_hora[IDReservageneral]";
                                                $qry_invitados = $dbo->query($sql_Invitados);
                                                $fila = $dbo->rows($qry_invitados);

                                                if ($fila > 0) :
                                                    $InvitadosReserva = "Inv.(";
                                                    while ($invitado = $dbo->fetchArray($qry_invitados)) {
                                                        if (!empty($invitado[Nombre])) {
                                                            $InvitadosReserva .= $invitado[Nombre];
                                                            if ($fila > 1) {
                                                                $InvitadosReserva .= "-";
                                                            }
                                                        } else {
                                                            $InvitadosReserva .= $dbo->getFields("Socio", "Nombre", "IDSocio = $invitado[IDSocio]");
                                                            if ($fila > 1) {
                                                                $InvitadosReserva .= "-";
                                                            }
                                                        }
                                                    }
                                                    $InvitadosReserva .= ")\n";
                                                endif;

                                                if (!empty($row_inscritos_hora["NombreBeneficiario"])) {

                                                    $datos_inscrito["IDSocio"] = $row_inscritos_hora["IDSocioBeneficiario"];
                                                    $datos_inscrito["Socio"] = "Benef. " . $row_inscritos_hora["NombreBeneficiario"] . " " . $InvitadosReserva;
                                                } else {
                                                    $datos_inscrito["IDSocio"] = $row_inscritos_hora["IDSocio"];
                                                    $datos_inscrito["Socio"] = $row_inscritos_hora["NombreSocio"] . " " . $InvitadosReserva;
                                                }

                                                array_push($response_inscritos, $datos_inscrito);
                                            }
                                            $hora["Inscritos"] = $response_inscritos;
                                        } else {
                                            $hora["MostrarBotonInscritos"] = "N";
                                            $hora["LabelBotonInscritos"] = "";
                                            $hora["Inscritos"] = array();
                                        }
                                        $IDElementoP = $hora['IDElemento'];
                                        $elemento_actual = array_values(array_filter(json_decode($datos_servicio_actual['Elementos']), function ($p)  use ($IDElementoP) {
                                            return $p->IDElemento == $IDElementoP;
                                        }));
                                        $hora["RegionActivaEnMapa"] = array(
                                            "PosicionX" => strval($elemento_actual[0]->PosicionX),
                                            "PosicionY" => strval($elemento_actual[0]->PosicionY),
                                            "Ancho" => strval($elemento_actual[0]->Ancho),
                                            "Alto" => strval($elemento_actual[0]->Alto)
                                        );

                                        array_push($response_disponibilidad, $hora);

                                    endif;
                                endif;
                                $primer_horario++;
                                $array_horas_elemento[] = $horaInicial;
                                $segundos_horaInicial = strtotime($horaInicial);
                                $segundos_minutoAnadir = $minutoAnadir * 60;
                                $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                                $hora_actual = strtotime($nuevaHora);
                                $horaInicial = $nuevaHora;

                            endwhile;
                        }
                    }
                }

            endwhile;

            $orden_letra = "";
            foreach ($response_disponibilidad as $id_array => $datos_array) :
                $orden_letra = SIMResources::$abecedario_orden[$datos_array["OrdenElemento"]];
                $array_ordenado_hora[$orden_letra . "_" . $datos_array["Hora"] . $datos_array["IDElemento"]] = $datos_array;
            endforeach;

            if (count($array_ordenado_hora) > 0) {
                ksort($array_ordenado_hora);
            }

            $response_array_ordenado = array();

            if (count($array_ordenado_hora) <= 0) {
                $array_ordenado_hora = array();
            }

            foreach ($array_ordenado_hora as $id_array => $datos_array) :
                array_push($response_array_ordenado, $datos_array);
            endforeach;

            array_push($response_disponibilidades, $response_array_ordenado);

            // Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
            if (!empty($UnElemento)) :
                $servicio_hora["Disponibilidad"] = $response_array_ordenado;
            else :
                $servicio_hora["Disponibilidad"] = $response_disponibilidades;
            endif;

            //$servicio_hora["Disponibilidad"] = $response_disponibilidades;
            $servicio_hora["name"] = $nombre_elemento_consulta;

            array_push($response, $servicio_hora);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'Noseencontraronregistros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_hora_disponible($IDClub, $IDServicio, $Fecha, $Hora, $Admin = "", $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $fecha_disponible = 0;
        $verifica_disponibilidad_especifica = 0;
        $verifica_disponibilidad_general = 0;
        $datos_servicio_actual = $dbo->fetchAll("Servicio", " IDServicio = '" . $IDServicio . "' ", "array");

        // Verifico que el club y servicio este disponible en la fecha consultada
        $verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio);
        if (!empty($verificacion)) :
            $respuesta["message"] = $verificacion;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        if (empty($Admin)) {
            $TipoConsultaDispo = "App";
        }

        //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
        $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha, $TipoConsultaDispo);

        foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
            if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                $fecha_disponible = 1;
            endif;
        endforeach;

        if ($fecha_disponible == 0) :
            //Esta fecha aún no está disponible
            $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (t4)";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $response = array();
        $response_disponibilidades = array();
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {

            $message = $dbo->rows($qry) . "Encontrados";
            $servicio_hora["IDClub"] = $IDClub;
            $servicio_hora["IDServicio"] = $IDServicio;
            $servicio_hora["Fecha"] = $Fecha;
            //$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);

            //Horas Disponibles Elemento
            $response_disponibilidad = array();

            $r = $dbo->fetchArray($qry);

            $sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '" . $IDServicio . "' " . $condicion_elemento;
            $result_elementos_servicio = $dbo->query($sql_elementos_servicio);
            $total_elementos = $dbo->rows($result_elementos_servicio);

            while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)) :
                unset($array_hora_reservada);
                $IDElemento = $r_elementos_servicio["IDServicioElemento"];

                $dia_fecha = date('w', strtotime($Fecha));

                // Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
                $sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where  IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin . " Order by HoraDesde Limit 1";
                $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
                $row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
                //$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
                $horaInicial_reserva = $Fecha . " " . $row_dispo_elemento_primera["HoraDesde"];

                //Verifico si tene disponibilidad  general el elemento
                $sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin;
                $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                if ($dbo->rows($qry_dispo_elemento_gral) > 0) {

                    $verifica_disponibilidad_general = 1;
                    while ($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)) {

                        $horaInicial = $row_dispo_elemento_gral["HoraDesde"];
                        //$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
                        $minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                        // Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
                        if ($Fecha == date("Y-m-d")) :
                            $medicion_tiempo = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                            $valor_tiempo_anticipacion = (int) $dbo->getFields("Disponibilidad", "Anticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                            if ($medicion_tiempo == "Horas") :
                                $valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
                            endif;
                        else :
                            $valor_tiempo_anticipacion = 0;
                        endif;

                        //Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
                        $medicion_tiempo_anticipacion = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                        $valor_anticipacion_turno = (int) $dbo->getFields("Disponibilidad", "AnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                        switch ($medicion_tiempo_anticipacion):
                            case "Dias":
                                $minutos_anticipacion_turno = (60 * 24) * $valor_anticipacion_turno;
                                break;
                            case "Horas":
                                $minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
                                break;
                            case "Minutos":
                                $minutos_anticipacion_turno = $valor_anticipacion_turno;
                                break;
                            default:
                                $minutos_anticipacion_turno = 0;
                        endswitch;

                        //Si es administrador no tiene limite de anticipacion
                        if ($Admin == "S") {
                            $valor_tiempo_anticipacion = 0;
                            $minutos_anticipacion_turno = 0;
                        }

                        $hora_real = date('Y-m-d H:i:s');
                        $hora_empezar_reserva = strtotime('-' . $valor_tiempo_anticipacion . ' minute', strtotime($horaInicial_reserva));
                        //$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
                        $hora_actual_sistema = strtotime($hora_real);

                        $hora_final = strtotime($row_dispo_elemento_gral["HoraHasta"]);
                        $hora_actual = strtotime($row_dispo_elemento_gral["HoraDesde"]);

                        while ($hora_actual <= $hora_final) :

                            $hora_fecha_actual = $Fecha . " " . date('H:i:s', $hora_actual);
                            $hora_puede_reservar = strtotime('+' . $minutos_anticipacion_turno . ' minute', strtotime($hora_real));
                            /*****************************************************************************************************
                                Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
                                Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
                             ******************************************************************************************************/
                            if (strtotime($hora_fecha_actual) >= strtotime($hora_real) && $hora_puede_reservar <= strtotime($hora_fecha_actual)) :

                                if (strlen($horaInicial) != 8) :
                                    $horaInicial .= ":00";
                                endif;

                                $hora["Hora"] = $horaInicial;
                                $zonahoraria = date_default_timezone_get();
                                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                                $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

                                //echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
                                //exit;

                                $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");
                                //Verifico que esta hora la tenga disponible algun elemento y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos

                                // De acuerdo al numero de elementos del servicio disponibles en esta hora verifico si en esta hora ya está reservado
                                $sql_reserva_hora = "SELECT * FROM ReservaGeneral WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '" . $hora["Hora"] . "'  ORDER BY Hora ";
                                $qry_reserva_hora = $dbo->query($sql_reserva_hora);
                                $total_horas_reservadas = $dbo->rows($qry_reserva_hora);
                                $total_horas_reservadas = $dbo->rows($qry_reserva_hora);
                                while ($row_rese = $dbo->fetchArray($qry_reserva_hora)) {
                                    $ReservadoPor .= $row_rese["NombreSocio"];
                                }








                                $contador_elementos_disponibles = 0;
                                // Verifico cuantos elementos tienen esta hora disponible
                                $sql_dispo_hora = "Select IDServicioElemento From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and ('" . $hora["Hora"] . "' >= HoraDesde and '" . $hora["Hora"] . "'<=HoraHasta)  and Activo <>'N' Order by HoraDesde";
                                $qry_dispo_hora = $dbo->query($sql_dispo_hora);
                                while ($row_dispo_hora = $dbo->fetchArray($qry_dispo_hora)) {
                                    if (!empty($row_dispo_hora["IDServicioElemento"])) :
                                        $array_elementos_hora = explode("|", $row_dispo_hora["IDServicioElemento"]);
                                        foreach ($array_elementos_hora as $id_elemento_hora) :
                                            if (!empty($id_elemento_hora)) :
                                                $contador_elementos_disponibles++;
                                            endif;
                                        endforeach;
                                    endif;
                                }
                                $total_elementos = $contador_elementos_disponibles;



                                if ($total_horas_reservadas >= $total_elementos || ($hora_actual_sistema < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {
                                    $hora["Disponible"] = "N";
                                    $MostrarReserva = $datos_servicio_actual["MostrarReserva"];
                                    if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) {
                                        $LabelPersonalizado = $datos_servicio_actual["LabelPersonalizado"];
                                        if (empty($LabelPersonalizado)) {
                                            $LabelPersonalizado = "Reservado";
                                        }
                                    } else {
                                        $LabelPersonalizado = $ReservadoPor;
                                        $LabelPersonalizado = "Reservado";
                                    }
                                    $hora["Socio"] = $LabelPersonalizado;
                                } else {
                                    $hora["Disponible"] = "S";
                                    $hora["Socio"] = "";
                                }

                                $hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];
                                $hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];
                                $hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];

                                $hora["NumeroMinimoInvitadoClub"] = $datos_disponibilidad["NumeroMinimoInvitadoClub"];
                                $hora["NumeroMinimoInvitadoExterno"] = $datos_disponibilidad["NumeroMinimoInvitadoExterno"];

                                //Repeticion reserva
                                $hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
                                $hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];
                                $hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];

                                //$hora["IDElemento"] = $IDElemento;
                                //$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
                                array_push($response_disponibilidad, $hora);
                            endif;

                            $array_horas_elemento[] = $horaInicial;
                            $segundos_horaInicial = strtotime($horaInicial);
                            $segundos_minutoAnadir = $minutoAnadir * 60;
                            $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                            $hora_actual = strtotime($nuevaHora);
                            $horaInicial = $nuevaHora;

                        endwhile;
                    }
                }

            endwhile;

            //Ordeno el array y que aparezca solo una hora para todo elementos
            foreach ($response_disponibilidad as $id_array => $datos_array) :
                $array_ordenado_hora[$datos_array["Hora"]] = $datos_array;
            endforeach;

            ksort($array_ordenado_hora);

            $response_array_ordenado = array();
            foreach ($array_ordenado_hora as $id_array => $datos_array) :
                array_push($response_array_ordenado, $datos_array);
            endforeach;

            //array_push($response_array_ordenado, $array_ordenado_hora);

            $servicio_hora["Disponibilidad"] = $response_array_ordenado;

            //$servicio_hora["Disponibilidad"] = $response_disponibilidades;

            array_push($response, $servicio_hora);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'Noseencontraronregistros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    //Funcion para saber si en una fecha determinada existe por lo menos 1 elemento dismponible
    public function verifica_elemento_disponible($IDClub, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();

        $flag_disponible = 1;

        //Consulto cuantos elementos tiene el servicio
        $sql_elemento_servicio = "Select IDServicioElemento From ServicioElemento Where IDServicio = '" . $IDServicio . "'";
        $qry_elemento_servicio = $dbo->query($sql_elemento_servicio);
        $total_elemento_servicio = (int) $dbo->rows($qry_elemento_servicio);

        //Consulto si tienen fechas de cierre total
        $sql_fecha_cierre = "SELECT IDServicioElemento FROM ServicioCierre Where IDServicio = '" . $IDServicio . "' and FechaInicio <= '" . $Fecha . "' and FechaFin >= '" . $Fecha . "' and HoraInicio='00:00:00' and HoraFin = '23:00:00'";
        $qry_fecha_cierre = $dbo->query($sql_fecha_cierre);
        $r_fecha_cierre = $dbo->fetchArray($qry_fecha_cierre);
        $array_elementos_cierre = explode("|", $r_fecha_cierre["IDServicioElemento"]);
        $total_elemento_cierre = count($array_elementos_cierre) - 2;

        //Si todos los elementos estan cerrados queiere decir que no hay ninguno disponible
        if ($total_elemento_servicio == $total_elemento_cierre) :
            $flag_disponible = 0;
        endif;

        return $flag_disponible;
    }

    public function get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha = "", $Tipo = "", $ValirdarDiasAdelante = "", $IDFiltro = "", $TipoFiltro = "", $IDClubAsociado = "", $IDTipoReserva = "")
    {
        $dbo = &SIMDB::get();


        if ($IDClub == 227) :
            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
            $respuesta = SIMWebServiceCountryMedellin::get_fechas_disponibles_servicio($IDClub, $IDServicio, $Fecha);
            return $respuesta;
        endif;

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $response = array();
        $BuscarAux = "N";

        if (!empty($IDServicio)) {

            $sql_servicio = "SELECT IDServicio,IDClub,IDServicio,Nombre,IDServicioMaestro,NumeroDiasMostrar,NumeroDiasAdelante,HoraApertura,DiaApertura,SoloFechaDisponible FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "'";
            $qry_servicio = $dbo->query($sql_servicio);
            if ($dbo->rows($qry_servicio) > 0) {
                $message = $dbo->rows($qry_servicio) . " " . 'Encontrados';
                while ($r = $dbo->fetchArray($qry_servicio)) {
                    $servicio["IDServicio"] = $r["IDServicioElemento"];
                    $servicio["IDClub"] = $IDClub;
                    $servicio["IDServicio"] = $r["IDServicio"];
                    $servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");

                    //Servicios Reservas
                    $response_fechas = array();

                    $dias_adelante = $r["NumeroDiasMostrar"];

                    if ($ValirdarDiasAdelante != "N") {
                        $dias_desde = $r["NumeroDiasAdelante"];
                    } else {
                        $dias_desde = 0;
                    }

                    if ((int) $dias_adelante <= 0) {
                        $dias_adelante = 3;
                    }


                    if (!empty($Fecha)) {
                        //$fecha_actual = date('Y-m-j',$Fecha);
                        $fecha_actual = $Fecha;
                        $dias_adelante = 0;
                        $dias_desde = 0;
                    } else {
                        $fecha_actual = date('Y-m-j');
                        //$dias_adelante=$r[ "NumeroDiasMostrar" ];
                    }

                    if ((int) $dias_desde > 0) {
                        $fecha_empezar = strtotime('+' . $dias_desde . ' day', strtotime($fecha_actual));
                        $fecha_actual = date('Y-m-j', $fecha_empezar);
                    }

                    $fecha_final = strtotime('+' . $dias_adelante . ' day', strtotime($fecha_actual));
                    $fecha_final = date('Y-m-j', $fecha_final);
                    $fechaInicio = strtotime($fecha_actual);
                    $fechaFin = strtotime($fecha_final);

                    // echo $fecha_actual;
                    // echo $fecha_final;

                    $contador = 1;
                    $primera_fecha = 1;
                    $flag_disponible_hoy = 0;




                    for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {

                        $fecha_validar = date("Y-m-d", $i);
                        $fecha_fin_validar = date("Y-m-d", $fechaFin);

                        if (!empty($IDFiltro)) {
                            if ($TipoFiltro == "Elemento") {
                                $condicion_filtro = " and IDServicioElemento like '%" . $IDFiltro . "|%' ";
                                $sql_cierre = "SELECT IDServicioCierre,HoraInicio,HoraFin
	                                                            FROM  ServicioCierre
	                                                            WHERE FechaInicio <= '" . $fecha_validar . "' and FechaFin >= '" . $fecha_validar . "' and IDServicioElemento like '%|" . $IDFiltro . "|%'";
                                $r_cierre = $dbo->query($sql_cierre);
                                while ($row_cierre = $dbo->fetchArray($r_cierre)) {
                                    $time = strtotime($row_cierre["HoraInicio"]);
                                    $timeStop = strtotime($row_cierre["HoraFin"]);
                                    $diff = intval(($timeStop - $time) / 3600);
                                    if ($diff >= 6) {
                                        //Le pongo al id elemento en -1  para que no encuentre resultado ya que en esta fecha tiene cierre del dia
                                        $condicion_filtro = " and IDServicioElemento ='-1' ";
                                    }
                                }
                            }
                            if ($TipoFiltro == "Auxiliar") {
                                $BuscarAux = "S";
                                $dia_fecha = date('w', strtotime($fecha_validar));
                                $sql_dispo_aux_gral = "SELECT AUXD.*
	                                                                                        From AuxiliarDisponibilidadDetalle AUXD, AuxiliarDisponibilidad AD
	                                                                                        Where AUXD.IDAuxiliarDisponibilidad=AD.IDAuxiliarDisponibilidad and  AUXD.IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and AD.Activo='S' and IDAuxiliar like '" . $IDFiltro . "|%'";
                                $qry_dispo_aux_gral = $dbo->query($sql_dispo_aux_gral);
                                $response_auxiliar = array();
                                $TotalAux = (int) $dbo->rows($qry_dispo_aux_gral);
                            }
                        }

                        //Consulto la disponibilidad en este dia
                        $dia_semana = date('w', strtotime($fecha_validar));
                        $sql_dispo_elemento_gral = "Select IDDisponibilidad,HoraDesde From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_semana . "|%' and Activo <>'N' " . $condicion_filtro . " Order By HoraDesde ASC, HoraHasta DESC Limit 1";
                        $qry_disponibilidad = $dbo->query($sql_dispo_elemento_gral);
                        $r_disponibilidad = $dbo->fetchArray($qry_disponibilidad);
                        //Consulto la hora maxima del dia para reservar
                        $sql_dispo_max = "Select IDDisponibilidad, HoraHasta From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_semana . "|%' and Activo <>'N' " . $condicion_filtro . " Order By HoraHasta DESC Limit 1";
                        $qry_dispo_max = $dbo->query($sql_dispo_max);
                        $r_dispo_max = $dbo->fetchArray($qry_dispo_max);
                        $HoraHastaFinal = $r_dispo_max["HoraHasta"];

                        //Si permite despues de cerrado el ultimo turno del dia
                        $MinutosDespuesCerrado = (int)$dbo->getFields("Disponibilidad", "MinutoPosteriorTurno", "IDDisponibilidad = '" . $r_dispo_max["IDDisponibilidad"] . "'");

                        //PARA no cerrar con el ultimo turno del dia
                        if ($MinutosDespuesCerrado > 0) {
                            $HoraHastaFinal = strtotime('+' . $MinutosDespuesCerrado . ' minute', strtotime($HoraHastaFinal));
                            $HoraHastaFinal = date("H:i:s", $HoraHastaFinal);
                        }

                        $HoraHastaFinal = date($HoraHastaFinal);

                        $total_disponibilidades = (int) $dbo->rows($qry_disponibilidad);

                        // Si la fecha es hoy valido que la hora hasta todavia este disponible
                        if ($fecha_validar == date("Y-m-d")) :
                            //echo strtotime(date("H:i:s")) ."<=". strtotime($r_disponibilidad["HoraHasta"]);
                            if (strtotime(date("H:i:s")) <= strtotime($HoraHastaFinal)) :
                                $flag_disponible_hoy = 0;
                            else :
                                $flag_disponible_hoy = 1;
                            endif;
                        else :
                            $flag_disponible_hoy = 0;
                        endif;

                        if ($BuscarAux == "S" && $TotalAux <= 0) {
                            $flag_disponible_hoy = 1;
                        }

                        // si no permite reservar el mismo dias
                        //if($r[ "ReservaMismoDia" ]=="N" && $fecha_validar == date( "Y-m-d" )){
                        //$flag_disponible_hoy = 1;
                        //}

                        if ($IDClub == "11" && ($IDServicio == 12854)) {
                            $flag_disponible_hoy = 0;
                        }

                        //Especial villa peru bungalow   
                        if ($IDClub == "220") {
                            $dia_valida = date("w", strtotime($fecha_validar));
                            if (($IDTipoReserva == "6184" || $IDTipoReserva == "6517") && $dia_valida == 5) {
                                $flag_disponible_hoy = 1;
                            } elseif (($IDTipoReserva == "6185" || $IDTipoReserva == "6518") && $dia_valida == 2) {
                                $flag_disponible_hoy = 1;
                            }
                        }

                        if ($flag_disponible_hoy == 0) :

                            //verifico con cuanto tiempo de anticipacion se puede reservar
                            $datos_reg_dispo = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $r_disponibilidad["IDDisponibilidad"] . "' ", "array");
                            $medicion_tiempo = $datos_reg_dispo["MedicionTiempoAnticipacion"];
                            $valor_anticipacion = (int) $datos_reg_dispo["Anticipacion"];
                            switch ($medicion_tiempo):
                                case "Dias":
                                    $minutos_anticipacion = (60 * 24) * $valor_anticipacion;
                                    break;
                                case "Horas":
                                    $minutos_anticipacion = 60 * $valor_anticipacion;
                                    break;
                                case "Minutos":
                                    $minutos_anticipacion = $valor_anticipacion;
                                    break;
                            endswitch;

                            //Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
                            $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
                            if ($id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30) : //Golf
                                $dias_antes = 8;
                            else :
                                $dias_antes = 8;
                            endif;

                            //semana santa atc
                            //if($IDClub==26){
                            //$dias_antes = 10;
                            //}

                            //consulto si el servicio tiene configurado un dia y hora especifico en la semana para abrir reservas
                            $dias_semana_eng = array("1" => "Monday", "2" => "Tuesday", "3" => "Wednesday", "4" => "Thursday", "5" => "Friday", "6" => "Saturday", "7" => "Sunday");
                            if ($r["DiaApertura"] > 0 && $r["HoraApertura"] != '00:00:00') :
                                $prox_apertura = strtotime('-' . $dias_antes . ' day', strtotime($fecha_validar . " " . $r["HoraApertura"]));
                                $fecha_pro_validacion = new DateTime(date("Y-m-d", $prox_apertura));
                                $fecha_pro_validacion->modify('next ' . $dias_semana_eng[$r["DiaApertura"]]);
                                $hora_inicio_reserva = strtotime($fecha_pro_validacion->format('Y-m-d') . " " . $r["HoraApertura"]);
                            else :
                                $hora_inicio_reserva = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($fecha_validar . " " . $r_disponibilidad["HoraDesde"]));
                            endif;

                            if ($IDServicio == 4433) {
                                /*
                                echo "DIA " . $dia_semana . "MIN: " . $minutos_anticipacion . " FIN";
                                echo "VALI " .  $fecha_validar . " " . $r_disponibilidad[ "HoraDesde" ] . " VALID";
                                print_r( $hora_reservar );
                                print_r($r_disponibilidad);
                                echo "RESE" . date("Y-m-d H:i:s",$hora_inicio_reserva ) . "DISPO " . $r_disponibilidad[ "IDDisponibilidad" ] ." FIN DISPO";
                                 */
                            }

                            //Especial Arrayanes Colombia el ultima dia del mes se habilta todo el mes en tenis
                            //if ( $IDClub == "11" && ( $IDServicio == 227 || $IDServicio == 115 ) ):

                            if ($IDClub == "11" && ($IDServicio == 115)) :
                            /*
                                $fecha_ultimo_dia = new DateTime();
                                $fecha_ultimo_dia->modify( 'last day of this month' );
                                $fecha_ultimo_dia->format( 'Y-m-d' );

                                if ( strtotime( $fecha_validar ) <= strtotime( $fecha_ultimo_dia->format( 'Y-m-d' ) ) ):
                                else :
                                $hora_inicio_reserva = strtotime( $fecha_ultimo_dia->format( 'Y-m-d' ) );
                                endif;
                                 */
                            endif;

                            //$hora_inicio_reserva = strtotime ($fecha_validar . " " . $r_disponibilidad["HoraDesde"]);
                            //$fechahora_actual =  strtotime ( '-'.$minutos_anticipacion.' minute' , strtotime ( date("Y-m-d H:i:s") ) ) ;

                            $fechahora_actual = strtotime(date("Y-m-d H:i:s"));

                            //echo date("Y-m-d H:i:s");
                            //exit;

                            //echo ".";exit;
                            if ($total_disponibilidades <= 0) {
                                $fecha_reservar = "nodisponible";
                                $hora_reservar = "nodisponible";
                                $TiempoRestanteDias = "nodisponible";
                                $TiempoRestanteHoras = "nodisponible";
                                $TiempoRestanteMinutos = "nodisponible";
                                $TiempoRestanteSegundos = "nodisponible";
                            } elseif ($hora_inicio_reserva < $fechahora_actual) {
                                $activo = "S";
                                $fecha_reservar = $fecha_validar;
                                $hora_reservar = $r_disponibilidad["HoraDesde"];
                                $TiempoRestanteDias = 0;
                                $TiempoRestanteHoras = 0;
                                $TiempoRestanteMinutos = 0;
                                $TiempoRestanteSegundos = 0;
                                $TiempoRestanteMiliSegundos = 0;

                                //Validacion Especial Medellin entre las 10pm y 5:30am no se puede reservar tenis medellin
                                $fecha_hora_actual = date("Y-m-d H:i:s");
                                $fecha_inicio_nopermitido = date("Y-m-d 22:00:00");
                                //$fecha_fin_nopermitido = strtotime ( '+450 minute' , strtotime ( $fecha_inicio_nopermitido ) ) ;
                                $fecha_fin_nopermitido = date("Y-m-d 05:30:00");

                                //if($IDClub==20 && $IDServicio==571 && strtotime($fecha_hora_actual)>=strtotime($fecha_inicio_nopermitido) &&  strtotime($fecha_hora_actual)<=$fecha_fin_nopermitido):
                                if ($IDClub == 20 && ($IDServicio == 571 || $IDServicio == 549 || $IDServicio == 551 || $IDServicio == 14898 || $IDServicio == 14899) && (strtotime($fecha_hora_actual) >= strtotime($fecha_inicio_nopermitido) || strtotime($fecha_hora_actual) <= strtotime($fecha_fin_nopermitido))) :
                                    //Calculo tiempo restante para poder reservar
                                    $activo = "N";
                                    $fecha_final = $fecha_fin_nopermitido;
                                    $diff = $fecha_fin_nopermitido - strtotime($fecha_hora_actual);
                                    $dias = $diff / (60 * 60 * 24);
                                    $horas = ($dias - intval($dias)) * 24;
                                    $min = ($horas - intval($horas)) * 60;
                                    $seg = ($min - intval($min)) * 60;
                                    $miliseg = intval(round(microtime(true) * 1000));
                                    $TiempoRestanteDias = intval($dias);
                                    $TiempoRestanteHoras = intval($horas);
                                    $TiempoRestanteMinutos = intval($min);
                                    $TiempoRestanteSegundos = intval($seg);
                                    $TiempoRestanteMiliSegundos = $miliseg;
                                    $fecha_reservar = date("Y-m-d", $fecha_fin_nopermitido);
                                    $hora_reservar = date("H:i:s", $fecha_fin_nopermitido);
                                endif;
                                //Fin Validacion Especial Medellin

                                //Validacion Especial Comercio entre solo si esta entre 8am y 4pm se puede reservar
                                $fecha_hora_actual = date("Y-m-d H:i:s");
                                $fecha_inicio_sipermitido = date("Y-m-d 08:00:00");
                                //$fecha_fin_nopermitido = strtotime ( '+450 minute' , strtotime ( $fecha_inicio_nopermitido ) ) ;
                                $fecha_fin_sipermitido = date("Y-m-d 13:00:00");

                                //Validacion Especial Pradera peluqueria abre del dia 1 al 31
                                /*
                                if (($IDClub == 16 && $IDServicio == 931)) {
                                $mes_act = date("m");
                                $fecha_mes_sig = date("Y-m-01 17:00:00");
                                $mes_fecha_validar = date("m", $i);
                                if ($mes_act != $mes_fecha_validar) {
                                $fecha_fin_nopermitido = strtotime('+1 month', strtotime($fecha_mes_sig));
                                $fecha_fin_nopermitido = strtotime('-1 day', $fecha_fin_nopermitido);

                                $fecha_fin_nopermitido = date('Y-m-d H:i:s', $fecha_fin_nopermitido);
                                $fecha_fin_nopermitido = strtotime($fecha_fin_nopermitido);

                                //Calculo tiempo restante para poder reservar
                                $activo = "N";
                                $fecha_final = $fecha_fin_nopermitido;
                                $diff = $fecha_fin_nopermitido - strtotime($fecha_hora_actual);
                                $dias = $diff / (60 * 60 * 24);
                                $horas = ($dias - intval($dias)) * 24;
                                $min = ($horas - intval($horas)) * 60;
                                $seg = ($min - intval($min)) * 60;
                                $miliseg = intval(round(microtime(true) * 1000));
                                $TiempoRestanteDias = intval($dias);
                                $TiempoRestanteHoras = intval($horas);
                                $TiempoRestanteMinutos = intval($min);
                                $TiempoRestanteSegundos = intval($seg);
                                $TiempoRestanteMiliSegundos = $miliseg;
                                $fecha_reservar = date("Y-m-d", $fecha_fin_nopermitido);
                                $hora_reservar = date("H:i:s", $fecha_fin_nopermitido);

                                if ($min <= 0 && $seg <= 0 && $horas <= 0) {
                                $activo = "S";
                                }

                                }
                                }
                                 */
                                //Fin Validacion Especial pradera

                                //Validacion Especial country peluqueria abre del dia 1 al 31
                                if (($IDClub == 44 && $IDServicio == 3905)) {
                                    $mes_act = date("m");
                                    $fecha_mes_sig = date("Y-m-01 18:00:00");
                                    $mes_fecha_validar = date("m", $i);
                                    if ($mes_act != $mes_fecha_validar) {
                                        $fecha_fin_nopermitido = strtotime('+1 month', strtotime($fecha_mes_sig));
                                        $fecha_fin_nopermitido = strtotime('-1 day', $fecha_fin_nopermitido);

                                        $fecha_fin_nopermitido = date('Y-m-d H:i:s', $fecha_fin_nopermitido);
                                        $fecha_fin_nopermitido = strtotime($fecha_fin_nopermitido);

                                        //Calculo tiempo restante para poder reservar
                                        $activo = "N";
                                        $fecha_final = $fecha_fin_nopermitido;
                                        $diff = $fecha_fin_nopermitido - strtotime($fecha_hora_actual);
                                        $dias = $diff / (60 * 60 * 24);
                                        $horas = ($dias - intval($dias)) * 24;
                                        $min = ($horas - intval($horas)) * 60;
                                        $seg = ($min - intval($min)) * 60;
                                        $miliseg = intval(round(microtime(true) * 1000));
                                        $TiempoRestanteDias = intval($dias);
                                        $TiempoRestanteHoras = intval($horas);
                                        $TiempoRestanteMinutos = intval($min);
                                        $TiempoRestanteSegundos = intval($seg);
                                        $TiempoRestanteMiliSegundos = $miliseg;
                                        $fecha_reservar = date("Y-m-d", $fecha_fin_nopermitido);
                                        $hora_reservar = date("H:i:s", $fecha_fin_nopermitido);

                                        if ($min <= 0 && $seg <= 0 && $horas <= 0) {
                                            $activo = "S";
                                        }
                                    }
                                }
                                //Fin Validacion Especial country

                                if ($IDClub == 8 && $IDServicio == 272) :
                                    $minima_fecha = date("Y-m") . "-14";
                                    $maxima_fecha = new DateTime();
                                    $maxima_fecha->modify('last day of this month');
                                    $maxima_fecha->format('Y-m-d');
                                    if ((int) date("d") <= 14 && strtotime($fecha_validar) <= strtotime($minima_fecha)) :
                                        $activo = "S";
                                    elseif ((int) date("d") >= 15 && strtotime($fecha_validar) <= strtotime($maxima_fecha->format('Y-m-d'))) :
                                        $activo = "S";
                                    else :

                                        if ((int) date("d") <= 14) :
                                            $fecha_fin_nopermitido = date("Y-m-15");
                                        elseif ((int) date("d") >= 15) :
                                            $fecha_mes_actual = date('Y-m-01');
                                            $fecha_fin_nopermitido = strtotime('+1 month', strtotime($fecha_mes_actual));
                                            $fecha_fin_nopermitido = date('Y-m-d', $fecha_fin_nopermitido);
                                        endif;

                                        $fecha_fin_nopermitido = strtotime($fecha_fin_nopermitido);

                                        //Calculo tiempo restante para poder reservar
                                        $activo = "N";
                                        $fecha_final = $fecha_fin_nopermitido;
                                        $diff = $fecha_fin_nopermitido - strtotime($fecha_hora_actual);
                                        $dias = $diff / (60 * 60 * 24);
                                        $horas = ($dias - intval($dias)) * 24;
                                        $min = ($horas - intval($horas)) * 60;
                                        $seg = ($min - intval($min)) * 60;
                                        $miliseg = intval(round(microtime(true) * 1000));
                                        $TiempoRestanteDias = intval($dias);
                                        $TiempoRestanteHoras = intval($horas);
                                        $TiempoRestanteMinutos = intval($min);
                                        $TiempoRestanteSegundos = intval($seg);
                                        $TiempoRestanteMiliSegundos = $miliseg;
                                        $fecha_reservar = date("Y-m-d", $fecha_fin_nopermitido);
                                        $hora_reservar = date("H:i:s", $fecha_fin_nopermitido);

                                    endif;
                                endif;
                                //Fin Validacion Especial Guaymaral

                                //Validacion Especial Condado el dia actual no se puede reservar en squash
                                /*
                                if ( $IDClub == 51 && $IDServicio == 7721 && $fecha_validar==date("Y-m-d")):
                                //Calculo tiempo restante para poder reservar
                                $activo = "N";
                                $TiempoRestanteDias = intval( $dias );
                                $TiempoRestanteHoras = intval( $horas );
                                $TiempoRestanteMinutos = intval( $min );
                                $TiempoRestanteSegundos = intval( $seg );
                                endif;
                                 */
                                //Fin Validacion Especial Condado

                                $elemento_disponible_dia = SIMWebService::verifica_elemento_disponible($IDClub, $IDServicio, $fecha_validar);
                                if ($elemento_disponible_dia == 0) :
                                    $fecha_reservar = "nodisponible";
                                    $hora_reservar = "nodisponible";
                                    $TiempoRestanteDias = "nodisponible";
                                    $TiempoRestanteHoras = "nodisponible";
                                    $TiempoRestanteMinutos = "nodisponible";
                                    $TiempoRestanteSegundos = "nodisponible";
                                endif;
                            } else {

                                $fecha_para_reservar = strtotime('-' . ($minutos_anticipacion) . ' minute', strtotime($fecha_validar));
                                $fecha_reservar = date('Y-m-j', $fecha_para_reservar);

                                $dia_semana_reservar = date('N', strtotime($fecha_reservar));

                                $activo = "N";
                                $hora_reservar = $r_disponibilidad["HoraDesde"];
                                //Calculo tiempo restante para poder reservar
                                $fecha_final = $fecha_reservar . " " . $hora_inicio_reserva;
                                $fecha_actual = date("Y-m-d H:i:s");
                                //$diff = strtotime($fechahora_actual) - strtotime($hora_inicio_reserva);

                                //consulto si el servicio tiene configurado un dia y hora especifico en la semana para abrir reservas
                                if ($r["DiaApertura"] > 0 && $r["HoraApertura"] != '00:00:00') :
                                    $primer_dia_semana = strtotime('-' . $dias_antes . ' day', strtotime($fecha_validar . " " . $r["HoraApertura"]));
                                    $fecha_pro_validacion = new DateTime(date("Y-m-d", $primer_dia_semana));
                                    $fecha_pro_validacion->modify('next ' . $dias_semana_eng[$r["DiaApertura"]]);
                                    $hora_inicio_reserva = strtotime($fecha_pro_validacion->format('Y-m-d') . " " . $r["HoraApertura"]);
                                endif;

                                $diff = $hora_inicio_reserva - $fechahora_actual;
                                $dias = $diff / (60 * 60 * 24);
                                $horas = ($dias - intval($dias)) * 24;
                                $min = ($horas - intval($horas)) * 60;
                                $seg = ($min - intval($min)) * 60;
                                $miliseg = intval(round(microtime(true) * 1000));
                                if ($fecha_validar == "2016-03-18") :
                                //echo $tiempo_restante = "Quedan ".intval($dias)." dias ".intval($horas)."  horas ".intval($min)." minutos ".intval($seg)." segundos";
                                //exit;
                                endif;

                                $fecha_reservar = date('Y-m-d', $hora_inicio_reserva);
                                $hora_reservar = date('H:i:s', $hora_inicio_reserva);

                                // si la fecha es pasada la marco como no disponible
                                if ($dias < 0 || $horas < 0 || $min < 0) :
                                    $activo = "N";
                                    $fecha_reservar = "nodisponible";
                                    $hora_reservar = "nodisponible";
                                    $TiempoRestanteDias = "nodisponible";
                                    $TiempoRestanteHoras = "nodisponible";
                                    $TiempoRestanteMinutos = "nodisponible";
                                    $TiempoRestanteSegundos = "nodisponible";
                                else :
                                    $TiempoRestanteDias = intval($dias);
                                    $TiempoRestanteHoras = intval($horas);
                                    $TiempoRestanteMinutos = intval($min);
                                    $TiempoRestanteSegundos = intval($seg);
                                    $TiempoRestanteMiliSegundos = intval($miliseg);

                                endif;

                                $elemento_disponible_dia = SIMWebService::verifica_elemento_disponible($IDClub, $IDServicio, $fecha_validar);
                                if ($elemento_disponible_dia == 0) :
                                    $fecha_reservar = "nodisponible";
                                    $hora_reservar = "nodisponible";
                                    $TiempoRestanteDias = "nodisponible";
                                    $TiempoRestanteHoras = "nodisponible";
                                    $TiempoRestanteMinutos = "nodisponible";
                                    $TiempoRestanteSegundos = "nodisponible";
                                endif;
                            }

                            if ($fecha_validar == "2017-09-09" && $IDClub == "11" && ($IDServicio == 352 || $IDServicio == 517 || $IDServicio == 818 || $IDServicio == 824 || $IDServicio == 1297)) :
                                $activo = "S";
                                $fecha_reservar = $fecha_validar;
                                $hora_reservar = $r_disponibilidad["HoraDesde"];
                                $TiempoRestanteDias = 0;
                                $TiempoRestanteHoras = 0;
                                $TiempoRestanteMinutos = 0;
                                $TiempoRestanteSegundos = 0;
                                $TiempoRestanteMiliSegundos = 0;
                            endif;

                            $servicio_fecha["Fecha"] = $fecha_validar;
                            $servicio_fecha["Activo"] = $activo;
                            $servicio_fecha["FechaReservar"] = $fecha_reservar;
                            $servicio_fecha["HoraReservar"] = $hora_reservar;
                            $zonahoraria = date_default_timezone_get();
                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                            $servicio_fecha["GMT"] = SIMWebservice::timezone_offset_string($offset);
                            $servicio_fecha["TiempoRestanteDias"] = $TiempoRestanteDias;
                            $servicio_fecha["TiempoRestanteHoras"] = $TiempoRestanteHoras;
                            $servicio_fecha["TiempoRestanteMinutos"] = $TiempoRestanteMinutos;
                            $servicio_fecha["TiempoRestanteSegundos"] = $TiempoRestanteSegundos;
                            $servicio_fecha["TiempoRestanteMiliSegundos"] = $TiempoRestanteMiliSegundos;

                            // si la fecha no esta isponible no la envio o si el servicio esta marcado solo para mostrar las disponibles
                            if ($fecha_reservar != "nodisponible") :
                                if (($r["SoloFechaDisponible"] == "S" && $activo == "S") || $r["SoloFechaDisponible"] != "S") {
                                    array_push($response_fechas, $servicio_fecha);
                                }
                            endif;

                            $contador++;
                            $primera_fecha++;
                        endif;
                    }

                    $servicio["Fechas"] = $response_fechas;

                    array_push($response, $servicio);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = 'Noseencontraronregistros';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "4." . 'Atencionfaltanparametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_disponibilidad_fecha($IDClub, $IDCampo, $Fecha, $Hora)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM ReservaGeneral WHERE IDServicioElemento = '" . $IDCampo . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3)  ORDER BY Hora ";
        $qry = $dbo->query($sql);

        $servicio_hora["IDClub"] = $IDClub;
        $servicio_hora["IDCampo"] = $IDCampo;
        $servicio_hora["Fecha"] = $Fecha;
        $servicio_hora["Hora"] = $Hora;

        //Si existen dos reservas es que estan reservados los dos tee de lo contrario no esta reservados o solo un tee esta reservado
        if ($dbo->rows($qry) > 1) :
            //Ya esta reservado
            $servicio_hora["Disponibilidad"] = "No";
            $message = " No Disponible";
        else :
            //No esta reservado devuelkque diponible en si
            $servicio_hora["Disponibilidad"] = "Si";
            $message = "Disponible";
        endif;

        array_push($response, $servicio_hora);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_disponiblidad_fecha_hora($IDClub, $IDServicio, $Fecha, $Hora, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $Hora = SIMWebService::validar_formato_hora($Hora);

        $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio);
        foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
            if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                $fecha_disponible = 1;
            endif;
        endforeach;

        if ($fecha_disponible == 0) :
            //Esta fecha aún no está disponible.
            $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (t2)";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $response = array();
        $sql = "SELECT * FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'   ORDER BY IDServicio ";
        $qry = $dbo->query($sql);

        $servicio_hora["IDClub"] = $IDClub;
        $servicio_hora["IDServicio"] = $IDServicio;
        $servicio_hora["Fecha"] = $Fecha;
        $servicio_hora["Hora"] = $Hora;
        $zonahoraria = date_default_timezone_get();
        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
        $servicio_hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

        $sql = "SELECT * FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'   ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        $response_elemento = array();
        while ($row_elemento = $dbo->fetchArray($qry)) :

            //Verifico si tiene disponibilidad  general el elemento
            $dia_fecha = date('w', strtotime($Fecha));
            $sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and ('" . $Hora . "' >= HoraDesde and '" . $Hora . "'<=HoraHasta) and IDServicioElemento like '%" . $row_elemento[IDServicioElemento] . "|%' and Activo <>'N'  Order by HoraDesde";
            $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
            if ($dbo->rows($qry_dispo_elemento_gral) > 0) :
                $elemento[IDElemento] = $row_elemento[IDServicioElemento];
                $elemento[Nombre] = $row_elemento[Nombre];
                //verifico disponibilidad
                $sql_reserva = "SELECT * FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDServicioElemento = '" . $row_elemento[IDServicioElemento] . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3)  ORDER BY Hora ";
                $qry_reserva = $dbo->query($sql_reserva);
                if ($dbo->rows($qry_reserva) >= 1) :
                    $row_datos_reserva = $dbo->fetchArray($qry_reserva);
                    $elemento[Disponible] = "N";
                    $elemento["Socio"] = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_datos_reserva["IDSocio"] . "'"));
                    $elemento["IDSocio"] = $row_datos_reserva["IDSocio"];
                    $elemento["ModalidadEsquiSocio"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_datos_reserva["IDTipoModalidadEsqui"] . "'");
                else :
                    $elemento[Disponible] = "S";
                    $elemento["Socio"] = "";
                    $elemento["IDSocio"] = "";
                    $elemento["ModalidadEsquiSocio"] = "";
                endif;
                array_push($response_elemento, $elemento);
            endif;
        endwhile;

        $servicio_hora["Disponibilidad"] = $response_elemento;

        $message = " Disponibilidad";

        array_push($response, $servicio_hora);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function validar_permiso_reserva($IDSocio, $IDServicio = "")
    {
        $dbo = &SIMDB::get();
        $permiso_reserva = "S";
        $sql_socio = "SELECT IDPermisoServicio,PermiteReservar From Socio Where IDSocio = '" . $IDSocio . "' limit 1";
        $result_socio = $dbo->query($sql_socio);
        $row_socio = $dbo->fetchArray($result_socio);
        if ($row_socio["PermiteReservar"] == "N") :
            $permiso_reserva = "N";
        else :
            if ((int) $row_socio["IDPermisoServicio"] > 0) {
                $sql_perm = "SELECT IDServicioPermiso FROM ServicioPermiso WHERE IDServicio = '" . $IDServicio . "' and IDPermisoServicio = '" . $row_socio["IDPermisoServicio"] . "' LIMIT 1";
                $result_perm = $dbo->query($sql_perm);
                $row_perm = $dbo->fetchArray($result_perm);
                if ((int) $row_perm["IDServicioPermiso"] <= 0) {
                    $permiso_reserva = "N";
                }
            }
        endif;
        return $permiso_reserva;
    }

    public function get_disponibilidad_campo($IDClub, $IDCampo, $Fecha, $IDServicio = "", $Admin = "", $NumeroTurnos = "", $MostrarTodoDia = "", $IDClubAsociado = "", $IDTipoReserva = "")
    {

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $dbo = &SIMDB::get();
        //Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
        if ($id_servicio_maestro == 15 || $id_servicio_maestro == 30 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28) : // Golf con opcion de escoger grupos para reservas turnos seguidos
            $respuesta = SIMWebService::get_disponibilidad_campo_turno_seguido($IDClub, $IDCampo, $Fecha, $IDServicio, $Admin, $NumeroTurnos, $MostrarTodoDia, $IDTipoReserva);
            return $respuesta;
        endif;

        $fecha_disponible = 0;

        $verifica_disponibilidad_especifica = 0;
        $verifica_disponibilidad_general = 0;

        //consulto los datos del servicio
        //$IDServicio = $dbo->getFields( "ServicioElemento" , "IDServicio" , "IDServicioElemento = '" . $IDCampo . "'" );

        // Verifico que el club y servicio este disponible en la fecha consultada
        $verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio);
        if (!empty($verificacion)) :
            $respuesta["message"] = $verificacion;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar

        $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio);
        foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
            if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                $fecha_disponible = 1;
            endif;
        endforeach;

        if ($fecha_disponible == 0 && empty($Admin)) :
            $respuesta["message"] = "la fecha selecionada aún no está disponible.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $response = array();
        $response_disponibilidades = array();
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . 'Encontrados';
            $servicio_hora["IDClub"] = $IDClub;
            $servicio_hora["IDServicio"] = $IDServicio;
            //$servicio_hora["IDCampo"] = $IDCampo;
            $servicio_hora["Fecha"] = $Fecha;
            //$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);

            //Horas Disponibles Elemento
            $response_disponibilidad = array();

            if (!empty($IDCampo)) {
                $condicion_elemento = " and IDServicioElemento = '" . $IDCampo . "'";
            }

            $r = $dbo->fetchArray($qry);

            $sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '" . $IDServicio . "' " . $condicion_elemento;
            $result_elementos_servicio = $dbo->query($sql_elementos_servicio);
            $response_disponibilidad_tee = array();
            while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)) :

                unset($array_hora_reservada);
                $IDElemento = $r_elementos_servicio["IDServicioElemento"];

                $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                //Consulto lo que tiene reservado el elemento en la fecha indicada en tee1
                $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";
                // $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento IN ($condicion_multiple_elemento) and Fecha = '" . $Fecha . "' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";

                $qry_reserva_elemento_tee1 = $dbo->query($sql_reserva_elemento_tee1);
                while ($row_reserva_elemento_tee1 = $dbo->fetchArray($qry_reserva_elemento_tee1)) {
                    $array_hora_reservada_tee1[$IDElemento][] = $row_reserva_elemento_tee1["Hora"];
                    $array_socio[$row_reserva_elemento_tee1["Hora"]] = $row_reserva_elemento_tee1;
                    if ($row_reserva_elemento_tee1["IDReservaGrupos"] > 0) :
                        $array_socio[$row_reserva_elemento_tee1["Hora"]]["Tee1"]["NombreSocio"] = utf8_encode($dbo->getFields("ReservaGrupos", "Nombre", "IDReservaGrupos = '" . $row_reserva_elemento_tee1["IDReservaGrupos"] . "'"));
                    else :
                        $array_socio[$row_reserva_elemento_tee1["Hora"]]["Tee1"]["NombreSocio"] = $row_reserva_elemento_tee1["NombreSocio"];
                    endif;
                    $array_socio[$row_reserva_elemento_tee1["Hora"]]["IDSocio"] = $row_reserva_elemento_tee1["IDSocio"];
                    $array_socio[$row_reserva_elemento_tee1["Hora"]]["IDReservaGeneral"] = $row_reserva_elemento_tee1["IDReservaGeneral"];
                }

                //print_r($array_socio["06:00:00"]["Tee1"]["NombreSocio"]);

                //Consulto lo que tiene reservado el elemento en la fecha indicada en tee10
                $sql_reserva_elemento_tee10 = "SELECT ReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";
                $qry_reserva_elemento_tee10 = $dbo->query($sql_reserva_elemento_tee10);
                while ($row_reserva_elemento_tee10 = $dbo->fetchArray($qry_reserva_elemento_tee10)) {
                    $array_hora_reservada_tee10[$IDElemento][] = $row_reserva_elemento_tee10["Hora"];
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]] = $row_reserva_elemento_tee10;
                    if ($row_reserva_elemento_tee10["IDReservaGrupos"] > 0) :
                        $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["Tee10"]["NombreSocio"] = utf8_encode($dbo->getFields("ReservaGrupos", "Nombre", "IDReservaGrupos = '" . $row_reserva_elemento_tee10["IDReservaGrupos"] . "'"));
                    else :
                        $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["Tee10"]["NombreSocio"] = $row_reserva_elemento_tee10["NombreSocio"];
                    endif;
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["IDSocio"] = $row_reserva_elemento_tee10["IDSocio"];
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["IDReservaGeneral"] = $row_reserva_elemento_tee10["IDReservaGeneral"];
                }

                //Horas generales del servicio
                /*
                    $horaInicial=$r["HoraDesde"];
                    $minutoAnadir=$r["IntervaloHora"];
                    $hora_final = strtotime( $r["HoraHasta"] );
                    $hora_actual = $r["HoraDesde"];
                     */

                $dia_fecha = date('w', strtotime($Fecha));

                // Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
                $sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' Order by HoraDesde Limit 1";
                $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
                $row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
                //$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
                $horaInicial_reserva = $Fecha . " " . $row_dispo_elemento_primera["HoraDesde"];

                for ($i = 1; $i <= 2; $i++) :

                    $verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento);
                    if (empty($verifica_abierto_servicio)) {

                        //Verifico si tene disponibilidad  general el elemento
                        $sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N'";
                        $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                        if ($dbo->rows($qry_dispo_elemento_gral) > 0) {

                            $verifica_disponibilidad_general = 1;
                            while ($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)) {

                                $horaInicial = $row_dispo_elemento_gral["HoraDesde"];
                                //$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
                                $minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                                // Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
                                if ($Fecha == date("Y-m-d")) :
                                    $medicion_tiempo = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                    $valor_tiempo_anticipacion = (int) $dbo->getFields("Disponibilidad", "Anticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                    if ($medicion_tiempo == "Horas") :
                                        $valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
                                    elseif ($medicion_tiempo == "Dias") :
                                        $valor_tiempo_anticipacion = 0;
                                    endif;
                                else :
                                    $valor_tiempo_anticipacion = 0;
                                endif;

                                //Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
                                $medicion_tiempo_anticipacion = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                $valor_anticipacion_turno = (int) $dbo->getFields("Disponibilidad", "AnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                switch ($medicion_tiempo_anticipacion):
                                    case "Dias":
                                        $minutos_anticipacion_turno = (60 * 24) * $valor_anticipacion_turno;
                                        break;
                                    case "Horas":
                                        $minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
                                        break;
                                    case "Minutos":
                                        $minutos_anticipacion_turno = $valor_anticipacion_turno;
                                        break;
                                    default:
                                        $minutos_anticipacion_turno = 0;
                                endswitch;

                                //Si es administrador no tiene limite de anticipacion
                                if ($Admin == "S") {
                                    $valor_tiempo_anticipacion = 0;
                                    $minutos_anticipacion_turno = 0;
                                }

                                $hora_real = date('Y-m-d H:i:s');
                                $hora_empezar_reserva = strtotime('-' . $valor_tiempo_anticipacion . ' minute', strtotime($horaInicial_reserva));
                                //$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
                                $hora_actual_sistema = strtotime($hora_real);

                                $hora_final = strtotime($row_dispo_elemento_gral["HoraHasta"]);
                                $hora_actual = strtotime($row_dispo_elemento_gral["HoraDesde"]);

                                while ($hora_actual <= $hora_final) :

                                    $hora_fecha_actual = $Fecha . " " . date('H:i:s', $hora_actual);
                                    $hora_puede_reservar = strtotime('+' . $minutos_anticipacion_turno . ' minute', strtotime($hora_real));
                                    /*****************************************************************************************************
                                        Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
                                        Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
                                     ******************************************************************************************************/
                                    if (strtotime($hora_fecha_actual) >= strtotime($hora_real) && $hora_puede_reservar <= strtotime($hora_fecha_actual)) :

                                        //Verifico si el tee esta disponible en este horario para mostrarlo
                                        if (($row_dispo_elemento_gral["Tee1"] == "S" && $i == 1) || ($row_dispo_elemento_gral["Tee10"] == "S" && $i == 2)) :

                                            if (strlen($horaInicial) != 8) :
                                                $horaInicial .= ":00";
                                            endif;

                                            $hora["Hora"] = $horaInicial;
                                            $zonahoraria = date_default_timezone_get();
                                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                                            $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

                                            //echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
                                            //exit;

                                            $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");

                                            // Si el tee es 1
                                            if ($i == 1) :
                                                //Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
                                                if ((in_array($horaInicial, $array_hora_reservada_tee1[$IDElemento])) || ($hora_actual_sistema < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {
                                                    $hora["Disponible"] = "N";

                                                    $hora["Socio"] = $array_socio["$horaInicial"]["Tee1"]["NombreSocio"];
                                                    $hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
                                                    $hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];
                                                } else {
                                                    $hora["Disponible"] = "S";
                                                    $hora["Socio"] = "";
                                                    $hora["IDSocio"] = "";
                                                    $hora["IDReserva"] = "";
                                                }
                                            endif;

                                            // Si el tee es 10
                                            if ($i == 2) :
                                                //Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
                                                if ((in_array($horaInicial, $array_hora_reservada_tee10[$IDElemento])) || ($hora_actual_sistema < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {
                                                    $hora["Disponible"] = "N";
                                                    $hora["Socio"] = $array_socio_tee10["$horaInicial"]["NombreSocio"];
                                                    $hora["Socio"] = $array_socio_tee10["$horaInicial"]["Tee10"]["NombreSocio"];
                                                    $hora["IDSocio"] = $array_socio_tee10["$horaInicial"]["IDSocio"];
                                                    $hora["IDReserva"] = $array_socio_tee10["$horaInicial"]["IDReservaGeneral"];
                                                } else {
                                                    $hora["Disponible"] = "S";
                                                    $hora["Socio"] = "";
                                                    $hora["IDSocio"] = "";
                                                    $hora["IDReserva"] = "";
                                                }
                                            endif;

                                            $hora["IDCampo"] = $IDCampo;

                                            if ($i == 1) :
                                                $hora["Tee"] = "Tee1";
                                            else :
                                                $hora["Tee"] = "Tee10";
                                            endif;

                                            $hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];
                                            $hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];
                                            $hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];

                                            //Repeticion reserva
                                            $hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
                                            $hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];
                                            $hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];

                                            $hora["IDElemento"] = $IDElemento;
                                            $hora["NombreElemento"] = $hora["Tee"] . "-" . $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDElemento . "'");

                                            $hora2["Hora"] = "08:00:00";
                                            $hora2["GMT"] = "-05:00";
                                            $hora2["Disponible"] = "S";
                                            $hora2["Socio"] = "";
                                            $hora2["IDSocio"] = "";
                                            $hora2["ModalidadEsquiSocio"] = "";
                                            $hora2["IDReserva"] = "";
                                            $hora2["MaximoPersonaTurno"] = "0";
                                            $hora2["NumeroInvitadoClub"] = "0";
                                            $hora2["NumeroInvitadoExterno"] = "0";
                                            $hora2["IDDisponibilidad"] = "14";
                                            $hora2["PermiteRepeticion"] = "N";
                                            $hora2["MedicionRepeticion"] = "";
                                            $hora2["FechaFinRepeticion"] = "null";
                                            $hora2["Georeferenciacion"] = "N";
                                            $hora2["Latitud"] = "4.7939569";
                                            $hora2["Longitud"] = "-74.0758342";
                                            $hora2["Rango"] = "10000";
                                            $hora2["MensajeFueraRango"] = "No estas en el club para poder hacer la reserva.";
                                            $hora2["IDElemento"] = "59";
                                            $hora2["NombreElemento"] = "Wilson";
                                            $hora2["ModalidadElemento"] = " null";
                                            $hora2["MaximoInvitadosSalon"] = "0";
                                            $hora2["OrdenElemento"] = "0";

                                            array_push($response_disponibilidad_tee, $hora);
                                        endif;
                                    endif;

                                    $array_horas_elemento[] = $horaInicial;
                                    $segundos_horaInicial = strtotime($horaInicial);
                                    $segundos_minutoAnadir = $minutoAnadir * 60;
                                    $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                                    $hora_actual = strtotime($nuevaHora);
                                    $horaInicial = $nuevaHora;

                                endwhile;
                            }
                        }
                    }
                endfor;

            endwhile;

            //

            foreach ($response_disponibilidad_tee as $id_array => $datos_array) :
                $array_ordenado_hora[$datos_array["Hora"] . $datos_array["NombreElemento"]] = $datos_array;
            endforeach;

            ksort($array_ordenado_hora);

            $response_array_ordenado = array();
            foreach ($array_ordenado_hora as $id_array => $datos_array) :
                array_push($response_array_ordenado, $datos_array);
            endforeach;

            array_push($response_disponibilidades, $response_array_ordenado);

            // Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
            if (!empty($UnElemento)) :
                $servicio_hora["Disponibilidad"] = $response_array_ordenado;
            else :
                $servicio_hora["Disponibilidad"] = $response_disponibilidades;
            endif;
            //$servicio_hora["Disponibilidad"] = $response_disponibilidades;
            array_push($response, $servicio_hora);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'No se encontraron registros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_disponibilidad_campo_turno_seguido($IDClub, $IDCampo, $Fecha, $IDServicio = "", $Admin = "", $NumeroTurnos = "", $MostrarTodoDia = "", $IDTipoReserva = "")
    {
        $dbo = &SIMDB::get();

        if (empty($NumeroTurnos)) {
            $NumeroTurnos = 1;
        }


        $fecha_disponible = 0;

        $verifica_disponibilidad_especifica = 0;
        $verifica_disponibilidad_general = 0;

        //consulto los datos del servicio
        //$IDServicio = $dbo->getFields( "ServicioElemento" , "IDServicio" , "IDServicioElemento = '" . $IDCampo . "'" );

        // Verifico que el club y servicio este disponible en la fecha consultada
        $verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio);
        if (!empty($verificacion)) :
            $respuesta["message"] = $verificacion;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        if (empty($Admin)) {
            $TipoConsultaDispo = "App";
        }

        //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
        $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha, $TipoConsultaDispo);
        foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
            if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                $fecha_disponible = 1;
            endif;
        endforeach;

        if ($fecha_disponible == 0 && empty($Admin)) :
            //Esta fecha aún no está disponible.
            $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (t3)";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $response = array();
        $response_disponibilidades = array();
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . 'Encontrados';
            $servicio_hora["IDClub"] = $IDClub;
            $servicio_hora["IDServicio"] = $IDServicio;
            //$servicio_hora["IDCampo"] = $IDCampo;
            $servicio_hora["Fecha"] = $Fecha;
            //$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);

            //Horas Disponibles Elemento
            $response_disponibilidad = array();

            if (!empty($IDCampo)) {
                $condicion_elemento = " and IDServicioElemento = '" . $IDCampo . "'";
            }

            $r = $dbo->fetchArray($qry);

            $nombre_elemento_consulta = string;
            $sql_elementos_servicio = "SELECT * From ServicioElemento Where IDServicio = '" . $IDServicio . "' " . $condicion_elemento;
            $result_elementos_servicio = $dbo->query($sql_elementos_servicio);
            $response_disponibilidad_tee = array();
            while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)) :

                $nombre_elemento_consulta = $r_elementos_servicio["Nombre"];
                unset($array_hora_reservada);
                $IDElemento = $r_elementos_servicio["IDServicioElemento"];

                //Consulto lo que tiene reservado el elemento en la fecha indicada en tee1
                //$sql_reserva_elemento_tee1 = "SELECT ReservaGeneral.*, Socio.Accion, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio
                //FROM ReservaGeneral, Socio WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";

                $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                // $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral.* FROM ReservaGeneral WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";
                $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral.* FROM ReservaGeneral WHERE IDServicioElemento IN ($condicion_multiple_elemento) and Fecha = '" . $Fecha . "' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";

                $qry_reserva_elemento_tee1 = $dbo->query($sql_reserva_elemento_tee1);
                while ($row_reserva_elemento_tee1 = $dbo->fetchArray($qry_reserva_elemento_tee1)) {

                    if ($IDClub == 70) {
                        $agrega_datos = "(" . utf8_encode($row_reserva_elemento_tee1["AccionSocio"]) . ")";
                    }

                    if ($row_reserva_elemento_tee1["IDEstadoReserva"] == 3) :
                        $nombre_reserva = "En proceso de reserva";
                    else :
                        $nombre_reserva = $row_reserva_elemento_tee1["NombreSocio"] . " " . $agrega_datos;
                    endif;

                    //Verifico si el club/servicio se configuro para mostrar el nombre del socio o para mostrar un texto personalizado, para funcionarios si se muestra el nombre
                    $MostrarReserva = $r["MostrarReserva"];
                    if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) {
                        $LabelPersonalizado = $r["LabelPersonalizado"];
                        $nombre_tomo_reserva = $LabelPersonalizado;
                    } else {

                        if (!empty($row_reserva_elemento_tee1["IDSocioBeneficiario"])) {
                            $nombre_tomo_reserva = "Benef. " . $row_reserva_elemento_tee1["NombreBeneficiario"];
                        } elseif (!empty($row_reserva_elemento_tee1["IDInvitadoBeneficiario"])) {
                            $nombre_tomo_reserva = "Inv. " . $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva_elemento_tee1["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva_elemento_tee1["IDInvitadoBeneficiario"] . "'") . " " . " " . $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_reserva_elemento_tee1["IDInvitadoBeneficiario"] . "'");
                        } else {
                            $nombre_tomo_reserva = $nombre_reserva;
                        }
                    }

                    if ($r["PopInvitados"] == "N") {
                        $IDReservaPop = "";
                    } else {
                        $IDReservaPop = $row_reserva_elemento_tee1["IDReservaGeneral"];
                    }

                    $array_hora_reservada_tee1[$IDElemento][] = $row_reserva_elemento_tee1["Hora"];
                    $array_socio[$row_reserva_elemento_tee1["Hora"]] = $row_reserva_elemento_tee1;
                    $array_socio[$row_reserva_elemento_tee1["Hora"]]["Tee1"]["NombreSocio"] = $nombre_tomo_reserva;
                    $array_socio[$row_reserva_elemento_tee1["Hora"]]["IDSocio"] = $row_reserva_elemento_tee1["IDSocio"];
                    $array_socio[$row_reserva_elemento_tee1["Hora"]]["IDReservaGeneral"] = $IDReservaPop;
                }

                //print_r($array_socio["06:00:00"]["Tee1"]["NombreSocio"]);

                //Consulto lo que tiene reservado el elemento en la fecha indicada en tee10
                //$sql_reserva_elemento_tee10 = "SELECT ReservaGeneral.*, Socio.Accion, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio
                //                                                            FROM ReservaGeneral, Socio WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";

                $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                // $sql_reserva_elemento_tee10 = "SELECT ReservaGeneral.* FROM ReservaGeneral WHERE IDServicioElemento = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";
                $sql_reserva_elemento_tee10 = "SELECT ReservaGeneral.* FROM ReservaGeneral WHERE IDServicioElemento IN ($condicion_multiple_elemento) and Fecha = '" . $Fecha . "' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) ORDER BY Hora ";

                $qry_reserva_elemento_tee10 = $dbo->query($sql_reserva_elemento_tee10);
                while ($row_reserva_elemento_tee10 = $dbo->fetchArray($qry_reserva_elemento_tee10)) {

                    if ($IDClub == 70) {
                        $agrega_datos = "(" . utf8_encode($row_reserva_elemento_tee10["AccionSocio"]) . ")";
                    }

                    if ($row_reserva_elemento_tee10["IDEstadoReserva"] == 3) :
                        $nombre_reserva_tee10 = "En proceso de reserva";
                    else :
                        $nombre_reserva_tee10 = $row_reserva_elemento_tee10["NombreSocio"] . " " . $agrega_datos;
                    endif;

                    //Verifico si el club/servicio se configuro para mostrar el nombre del socio o para mostrar un texto personalizado, para funcionarios si se muestra el nombre
                    $MostrarReserva = $r["MostrarReserva"];
                    if ($MostrarReserva == "Pesonalizado" && empty($Agenda) && empty($Admin)) {
                        $LabelPersonalizado = $r["LabelPersonalizado"];
                        $nombre_tomo_reserva_tee10 = $LabelPersonalizado;
                    } else {

                        if (!empty($row_reserva_elemento_tee10["IDSocioBeneficiario"])) {
                            $nombre_tomo_reserva_tee10 = "Benef. " . $row_reserva_elemento_tee10["NombreBeneficiario"];
                        } elseif (!empty($row_reserva_elemento_tee10["IDInvitadoBeneficiario"])) {
                            $nombre_tomo_reserva_tee10 = "Inv. " . $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva_elemento_tee10["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva_elemento_tee10["IDInvitadoBeneficiario"] . "'") . " " . " " . $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_reserva_elemento_tee10["IDInvitadoBeneficiario"] . "'");
                        } else {
                            $nombre_tomo_reserva_tee10 = $nombre_reserva_tee10;
                        }
                    }
                    if ($r["PopInvitados"] == "N") {
                        $IDReservaPopTee10 = "";
                    } else {
                        $IDReservaPopTee10 = $row_reserva_elemento_tee10["IDReservaGeneral"];
                    }

                    $array_hora_reservada_tee10[$IDElemento][] = $row_reserva_elemento_tee10["Hora"];
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]] = $row_reserva_elemento_tee10;
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["Tee10"]["NombreSocio"] = $nombre_tomo_reserva_tee10;
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["IDSocio"] = $row_reserva_elemento_tee10["IDSocio"];
                    $array_socio_tee10[$row_reserva_elemento_tee10["Hora"]]["IDReservaGeneral"] = $IDReservaPopTee10;
                }

                //Horas generales del servicio
                /*
            $horaInicial=$r["HoraDesde"];
            $minutoAnadir=$r["IntervaloHora"];
            $hora_final = strtotime( $r["HoraHasta"] );
            $hora_actual = $r["HoraDesde"];
             */

                $dia_fecha = date('w', strtotime($Fecha));

                if (empty($Admin)) {
                    $condicion_dispo_solo_admin = " and SoloAdmin <> 'S'";
                }

                // Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
                $sql_dispo_elemento_primera = "SELECT * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin . " Order by HoraDesde Limit 1";
                $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
                $row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
                //$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
                $horaInicial_reserva = $Fecha . " " . $row_dispo_elemento_primera["HoraDesde"];

                for ($i = 1; $i <= 2; $i++) :

                    $verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento);

                    if (empty($verifica_abierto_servicio)) {

                        //Verifico si tene disponibilidad  general el elemento
                        $sql_dispo_elemento_gral = "SELECT * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' " . $condicion_dispo_solo_admin;
                        $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                        if ($dbo->rows($qry_dispo_elemento_gral) > 0) {

                            //Para las horas con par por tee
                            //Tee1
                            $sql_dispo = "SELECT * From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Tee1 = 'S' and Activo <>'N' " . $condicion_dispo_solo_admin;
                            $qry_dispo = $dbo->query($sql_dispo);
                            while ($row_dispo_elemento_gral_par = $dbo->fetchArray($qry_dispo)) {
                                $array_par_tee1[$row_dispo_elemento_gral_par["HoraDesde"]] = $row_dispo_elemento_gral_par["HoraPar"];
                            }
                            $sql_dispo = "SELECT * From ServicioDisponibilidad Where  IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Tee10 = 'S' and Activo <>'N' " . $condicion_dispo_solo_admin;
                            $qry_dispo = $dbo->query($sql_dispo);
                            while ($row_dispo_elemento_gral_par = $dbo->fetchArray($qry_dispo)) {
                                $array_par_tee10[$row_dispo_elemento_gral_par["HoraDesde"]] = $row_dispo_elemento_gral_par["HoraPar"];
                            }

                            $verifica_disponibilidad_general = 1;
                            while ($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)) {

                                $horaInicial = $row_dispo_elemento_gral["HoraDesde"];
                                //$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
                                $minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                                // Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
                                if ($Fecha == date("Y-m-d")) :
                                    $medicion_tiempo = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                    $valor_tiempo_anticipacion = (int) $dbo->getFields("Disponibilidad", "Anticipacion", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                    if ($medicion_tiempo == "Horas") :
                                        $valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
                                    elseif ($medicion_tiempo == "Dias") :
                                        $valor_tiempo_anticipacion = 0;
                                    endif;
                                else :
                                    $valor_tiempo_anticipacion = 0;
                                endif;

                                //Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
                                $medicion_tiempo_anticipacion = $dbo->getFields("Disponibilidad", "MedicionTiempoAnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                $valor_anticipacion_turno = (int) $dbo->getFields("Disponibilidad", "AnticipacionTurno", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");
                                switch ($medicion_tiempo_anticipacion):
                                    case "Dias":
                                        $minutos_anticipacion_turno = (60 * 24) * $valor_anticipacion_turno;
                                        break;
                                    case "Horas":
                                        $minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
                                        break;
                                    case "Minutos":
                                        $minutos_anticipacion_turno = $valor_anticipacion_turno;
                                        break;
                                    default:
                                        $minutos_anticipacion_turno = 0;
                                endswitch;

                                //Si es administrador no tiene limite de anticipacion
                                if ($Admin == "S") {
                                    $valor_tiempo_anticipacion = 0;
                                    $minutos_anticipacion_turno = 0;
                                }

                                //Consulto hace una hora para mostrar los turnos anterior segun solicitud de lagartos
                                /*
                            $hace_una_hora = strtotime ( '-1 hour' , strtotime ( date("Y-m-d H:i:s") ) ) ;
                            if($Fecha==date("Y-m-d")):
                            $hora_real = date('Y-m-d H:i:s',$hace_una_hora);
                            else:
                            $hora_real = date('Y-m-d H:i:s');
                            endif;
                             */

                                $hora_real = date('Y-m-d H:i:s');

                                if ($MostrarTodoDia == "S") :
                                    $hora_real = date('Y-m-d 05:00:00');
                                endif;

                                $hora_empezar_reserva = strtotime('-' . $valor_tiempo_anticipacion . ' minute', strtotime($horaInicial_reserva));
                                //$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
                                $hora_actual_sistema = strtotime($hora_real);

                                $hora_final = strtotime($row_dispo_elemento_gral["HoraHasta"]);
                                $hora_actual = strtotime($row_dispo_elemento_gral["HoraDesde"]);
                                $hora_par = $row_dispo_elemento_gral["HoraPar"];

                                while ($hora_actual <= $hora_final) :

                                    $hora_fecha_actual = $Fecha . " " . date('H:i:s', $hora_actual);
                                    $hora_puede_reservar = strtotime('+' . $minutos_anticipacion_turno . ' minute', strtotime($hora_real));
                                    /*****************************************************************************************************
                                Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
                                Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
                                     ******************************************************************************************************/
                                    if (strtotime($hora_fecha_actual) >= strtotime($hora_real) && $hora_puede_reservar <= strtotime($hora_fecha_actual)) :

                                        //Verifico si el tee esta disponible en este horario para mostrarlo
                                        if (($row_dispo_elemento_gral["Tee1"] == "S" && $i == 1) || ($row_dispo_elemento_gral["Tee10"] == "S" && $i == 2)) :

                                            if (strlen($horaInicial) != 8) :
                                                $horaInicial .= ":00";
                                            endif;

                                            $hora["Hora"] = $horaInicial;
                                            $zonahoraria = date_default_timezone_get();
                                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                                            $hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

                                            //echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
                                            //exit;

                                            $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");
                                            $total_invitados = 0;
                                            $reservado_por_socio = "";

                                            // Si el tee es 1
                                            if ($i == 1) :
                                                //Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
                                                if ((in_array($horaInicial, $array_hora_reservada_tee1[$IDElemento])) || ($hora_actual_sistema < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {
                                                    $hora["Disponible"] = "N";
                                                    $hora["Socio"] = $array_socio["$horaInicial"]["Tee1"]["NombreSocio"];
                                                    $hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
                                                    $hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];
                                                    $reservado_por_socio = "S";
                                                    //Permite a otros socios agregarse a un grupo de juego cuando quede un cupo
                                                    if ($r["PermiteAgregarGrupo"] == "S") :
                                                        $sql_invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'";
                                                        $result_invitado = $dbo->query($sql_invitados);
                                                        $total_invitados = $dbo->rows($result_invitado);
                                                    endif;
                                                } else {

                                                    //Verifico que no tenga fecha de cierre en esta hora
                                                    $verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento, $hora["Hora"], "Tee1");

                                                    if ($IDClub == 112 && $IDServicio == 19939 && empty($Admin)) {
                                                        $verifica_abierto_servicio_hora = "Solo starter:Solo starter:Solo Starter";
                                                    }

                                                    if (!empty($verifica_abierto_servicio_hora)) :
                                                        //extraigo la razon
                                                        $mensaje_cierre = explode(":", $verifica_abierto_servicio_hora);

                                                        $hora["Disponible"] = "N";
                                                        $hora["Socio"] = $mensaje_cierre[2];
                                                        $hora["IDSocio"] = "";
                                                        $hora["IDReserva"] = "";
                                                    else :

                                                        //if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
                                                        if ($NumeroTurnos == 1) :
                                                            //Si esta hora el par de una hora(p.j 05:50 con 08:35) verifico si ya paso la hora para poder reserva si ya paso la hora de terminacion
                                                            $hora_par_disponible = SIMWebService::valida_hora_con_par($array_par_tee1, $horaInicial, "Tee1", $IDElemento, $Fecha, $hora_real, $IDClub);



                                                            if ($hora_par_disponible == "S") :
                                                                $hora["Disponible"] = "S";
                                                                $hora["Socio"] = "";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            else :
                                                                $hora["Disponible"] = "N";
                                                                $hora["Socio"] = "No Disponible (c)";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            endif;
                                                        else :
                                                            //verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
                                                            $array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $hora["Hora"], $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, "Tee1", "", $array_par_tee1);

                                                            if (count($array_disponible) == $NumeroTurnos) :
                                                                //Si esta hora el par de una hora(p.j 05:50 con 08:35) verifico si ya paso la hora para poder reserva si ya paso la hora de terminacion
                                                                $hora_par_disponible = SIMWebService::valida_hora_con_par($array_par_tee1, $horaInicial, "Tee1", $IDElemento, $Fecha, $hora_real, $IDClub);
                                                                // Si la reserva es 9 hoyos no debe validar la hora par                                                            

                                                                if ($hora_par_disponible == "S") :
                                                                    $hora["Disponible"] = "S";
                                                                    $hora["Socio"] = "";
                                                                    $hora["IDSocio"] = "";
                                                                    $hora["IDReserva"] = "";
                                                                else :
                                                                    $hora["Disponible"] = "N";
                                                                    $hora["Socio"] = "No Disponible";
                                                                    $hora["IDSocio"] = "";
                                                                    $hora["IDReserva"] = "";
                                                                endif;
                                                            else :
                                                                $hora["Disponible"] = "N";
                                                                $hora["Socio"] = "No Disponible";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            endif;
                                                        endif;
                                                    /*
                                                else:
                                                $hora["Disponible"] = "N";
                                                $hora["Socio"] = "Hora no disponible";
                                                $hora["IDSocio"] = "";
                                                $hora["IDReserva"] = "";
                                                endif;
                                                 **/

                                                    endif;
                                                }
                                            endif;

                                            // Si el tee es 10
                                            if ($i == 2) :
                                                //Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
                                                if ((in_array($horaInicial, $array_hora_reservada_tee10[$IDElemento])) || ($hora_actual_sistema < $hora_empezar_reserva && $valor_tiempo_anticipacion > 0)) {
                                                    $hora["Disponible"] = "N";
                                                    $hora["Socio"] = $array_socio_tee10["$horaInicial"]["NombreSocio"];
                                                    $hora["Socio"] = $array_socio_tee10["$horaInicial"]["Tee10"]["NombreSocio"];
                                                    $hora["IDSocio"] = $array_socio_tee10["$horaInicial"]["IDSocio"];
                                                    $hora["IDReserva"] = $array_socio_tee10["$horaInicial"]["IDReservaGeneral"];
                                                    $reservado_por_socio = "S";
                                                    //Permite a otros socios agregarse a un grupo de juego cuando quede un cupo
                                                    if ($r["PermiteAgregarGrupo"] == "S") :
                                                        $sql_invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '" . $array_socio_tee10["$horaInicial"]["IDReservaGeneral"] . "'";
                                                        $result_invitado = $dbo->query($sql_invitados);
                                                        $total_invitados = $dbo->rows($result_invitado);
                                                    endif;
                                                } else {

                                                    //Verifico que no tenga fecha de cierre en esta hora
                                                    $verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento, $hora["Hora"], "Tee10");

                                                    if ($IDClub == 112 && $IDServicio == 19939 && empty($Admin)) {
                                                        $verifica_abierto_servicio_hora = "Solo starter:Solo starter:Solo Starter";
                                                    }

                                                    if (!empty($verifica_abierto_servicio_hora)) :
                                                        //extraigo la razon
                                                        $mensaje_cierre = explode(":", $verifica_abierto_servicio_hora);

                                                        $hora["Disponible"] = "N";
                                                        $hora["Socio"] = $mensaje_cierre[2];
                                                        $hora["IDSocio"] = "";
                                                        $hora["IDReserva"] = "";
                                                    else :

                                                        //if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
                                                        if ($NumeroTurnos == 1) :
                                                            //Si esta hora el par de una hora(p.j 05:50 con 08:35) verifico si ya paso la hora para poder reserva si ya paso la hora de terminacion
                                                            $hora_par_disponible = SIMWebService::valida_hora_con_par($array_par_tee10, $horaInicial, "Tee10", $IDElemento, $Fecha, $hora_real, $IDClub);

                                                            if ($hora_par_disponible == "S") :
                                                                $hora["Disponible"] = "S";
                                                                $hora["Socio"] = "";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            else :
                                                                $hora["Disponible"] = "N";
                                                                $hora["Socio"] = "No Disponible (c.)";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            endif;
                                                        else :
                                                            //verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
                                                            $array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $hora["Hora"], $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, "Tee10", "", $array_par_tee10);
                                                            if (count($array_disponible) == $NumeroTurnos) :
                                                                //Si esta hora el par de una hora(p.j 05:50 con 08:35) verifico si ya paso la hora para poder reserva si ya paso la hora de terminacion
                                                                $hora_par_disponible = SIMWebService::valida_hora_con_par($array_par_tee10, $horaInicial, "Tee10", $IDElemento, $Fecha, $hora_real, $IDClub);

                                                                if ($hora_par_disponible == "S") :
                                                                    $hora["Disponible"] = "S";
                                                                    $hora["Socio"] = "";
                                                                    $hora["IDSocio"] = "";
                                                                    $hora["IDReserva"] = "";
                                                                else :
                                                                    $hora["Disponible"] = "N";
                                                                    $hora["Socio"] = "No Disponible";
                                                                    $hora["IDSocio"] = "";
                                                                    $hora["IDReserva"] = "";
                                                                endif;

                                                            else :
                                                                $hora["Disponible"] = "N";
                                                                $hora["Socio"] = "No Disponible";
                                                                $hora["IDSocio"] = "";
                                                                $hora["IDReserva"] = "";
                                                            endif;
                                                        endif;
                                                    /*
                                                else:
                                                $hora["Disponible"] = "N";
                                                $hora["Socio"] = "Hora no disponible";
                                                $hora["IDSocio"] = "";
                                                $hora["IDReserva"] = "";
                                                endif;
                                                 */
                                                    endif;
                                                }
                                            endif;

                                            $hora["IDCampo"] = $IDCampo;

                                            if ($i == 1) :
                                                $hora["Tee"] = "Tee1";
                                            else :
                                                $hora["Tee"] = "Tee10";
                                            endif;

                                            //Maximo y minimo deacuerso a los turnos
                                            //$minimo_invitado = ($datos_disponibilidad["NumeroInvitadoClub"] * $NumeroTurnos)-1;
                                            //$maximo_invitado = ($datos_disponibilidad["NumeroInvitadoExterno"] * $NumeroTurnos)-1;
                                            $minimo_invitado = ($datos_disponibilidad["NumeroInvitadoClub"] * $NumeroTurnos);
                                            $maximo_invitado = ($datos_disponibilidad["MaximoInvitados"] * $NumeroTurnos);

                                            $hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];
                                            $hora["NumeroInvitadoClub"] = "$minimo_invitado";
                                            $hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];
                                            $hora["Maximoo"] = $datos_disponibilidad["MaximoInvitados"] . "*" . $NumeroTurnos . "IN" . $total_invitados;

                                            //Permite a otros socios agregarse a un grupo de juego cuando quede un cupo

                                            if ($r["PermiteAgregarGrupo"] == "S" && $reservado_por_socio == "S") :
                                                $cupos_disponibles_grupo = (int) $maximo_invitado - (int) $total_invitados;
                                                $hora["CuposDisponibles"] = $cupos_disponibles_grupo;
                                            else :
                                                $hora["CuposDisponibles"] = 0;
                                            endif;

                                            //Repeticion reserva
                                            $hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
                                            $hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];
                                            $hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];

                                            if (!empty($IDUsuario)) {
                                                $PermiteReservarUsuario = $dbo->getFields("Usuario", "PermiteReservar", "IDUsuario = '" . $IDUsuario . "'");
                                            } else {
                                                $PermiteReservarUsuario = "N";
                                            }
                                            $hora["PermiteReservarUsuario"] = "S";

                                            if ($hora["Tee"] == "Tee10" && $IDClub == 44) {
                                                $NombreDesc = " para 9 hoyos";
                                            }

                                            $hora["IDElemento"] = $IDElemento;
                                            $hora["NombreElemento"] = $hora["Tee"] . "-" . $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDElemento . "'");
                                            $hora["LabelDisponible"] = "Disponible." . $NombreDesc;
                                            //Consulto los datos de georeferenciacion
                                            $datos_disponibilidad_geo = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_disponibilidad["IDDisponibilidad"] . "' ", "array");
                                            $hora["Georeferenciacion"] = $datos_disponibilidad_geo["Georeferenciacion"];
                                            //Consulto los demas datos de la configuracion del servicio
                                            //$datos_geo_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $IDServicio . "' ", "array");
                                            $datos_geo_servicio = $datos_servicio_actual;
                                            $hora["Latitud"] = $datos_geo_servicio["Latitud"];
                                            $hora["Longitud"] = $datos_geo_servicio["Longitud"];
                                            $hora["Rango"] = $datos_geo_servicio["Rango"];
                                            $hora["MensajeFueraRango"] = $datos_geo_servicio["MensajeFueraRango"];

                                            $MostrarAsistencia = "S";

                                            if (($array_socio["$horaInicial"]["Cumplida"] == "S" || $array_socio["$horaInicial"]["Tipo"] == "Automatica") && $array_socio["$horaInicial"]["IDUsuarioCumplida"] != 9999999) {
                                                $MostrarAsistencia = "N";
                                            }

                                            $hora["MostrarBotonCumplida"] = $MostrarAsistencia;

                                            $response_inscritos = array();
                                            $datos_inscrito = array();
                                            // Es una clase grupal
                                            if ($datos_disponibilidad["Cupos"] > 1) {

                                                $hora["MostrarBotonInscritos"] = $datos_servicio_configuracion["MostrarBotonInscritos"];
                                                $hora["LabelBotonInscritos"] = $datos_servicio_configuracion["LabelBotonInscritos"];
                                                $sql_inscritos_hora = "SELECT RG.IDReservageneral,RG.NombreSocio, AccionSocio, RG.NombreBeneficiario, RG.IDSocio, RG.IDSocioBeneficiario
				                                                                                    FROM ReservaGeneral RG
				                                                                                    WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "'
				                                                                                    and IDEstadoReserva = 1  and Hora = '" . $horaInicial . "' and IDServicioElemento = '" . $IDElemento . "' and IDClub = '" . $IDClub . "'";

                                                $r_inscritos_hora = $dbo->query($sql_inscritos_hora);
                                                while ($row_inscritos_hora = $dbo->fetchArray($r_inscritos_hora)) {
                                                    $InvitadosReserva = "";
                                                    $datos_inscrito["IDReserva"] = $row_inscritos_hora["IDReservageneral"];

                                                    $sql_Invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = $row_inscritos_hora[IDReservageneral]";
                                                    $qry_invitados = $dbo->query($sql_Invitados);
                                                    $fila = $dbo->rows($qry_invitados);

                                                    if ($fila > 0) :
                                                        $InvitadosReserva = "Inv.(";
                                                        while ($invitado = $dbo->fetchArray($qry_invitados)) {
                                                            if (!empty($invitado[Nombre])) {
                                                                $InvitadosReserva .= $invitado[Nombre];
                                                                if ($fila > 1) {
                                                                    $InvitadosReserva .= "-";
                                                                }
                                                            } else {
                                                                $InvitadosReserva .= $dbo->getFields("Socio", "Nombre", "IDSocio = $invitado[IDSocio]");
                                                                if ($fila > 1) {
                                                                    $InvitadosReserva .= "-";
                                                                }
                                                            }
                                                        }
                                                        $InvitadosReserva .= ")\n";
                                                    endif;

                                                    if (!empty($row_inscritos_hora["NombreBeneficiario"])) {

                                                        $datos_inscrito["IDSocio"] = $row_inscritos_hora["IDSocioBeneficiario"];
                                                        $datos_inscrito["Socio"] = "Benef. " . $row_inscritos_hora["NombreBeneficiario"] . " " . $InvitadosReserva;
                                                    } else {
                                                        $datos_inscrito["IDSocio"] = $row_inscritos_hora["IDSocio"];
                                                        $datos_inscrito["Socio"] = $row_inscritos_hora["NombreSocio"] . " " . $InvitadosReserva;
                                                    }

                                                    array_push($response_inscritos, $datos_inscrito);
                                                }
                                                $hora["Inscritos"] = $response_inscritos;
                                            } else {
                                                $hora["MostrarBotonInscritos"] = "N";
                                                $hora["LabelBotonInscritos"] = "";
                                                $hora["Inscritos"] = array();
                                            }

                                            array_push($response_disponibilidad_tee, $hora);
                                        endif;
                                    endif;

                                    $array_horas_elemento[] = $horaInicial;
                                    $segundos_horaInicial = strtotime($horaInicial);
                                    $segundos_minutoAnadir = $minutoAnadir * 60;
                                    $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                                    $hora_actual = strtotime($nuevaHora);
                                    $horaInicial = $nuevaHora;

                                endwhile;
                            }
                        }
                    } else {
                        $respuesta["message"] = $verifica_abierto_servicio;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                endfor;

            endwhile;

            //

            foreach ($response_disponibilidad_tee as $id_array => $datos_array) :
                $array_ordenado_hora[$datos_array["Hora"] . $datos_array["NombreElemento"]] = $datos_array;
            endforeach;

            ksort($array_ordenado_hora);

            $response_array_ordenado = array();
            foreach ($array_ordenado_hora as $id_array => $datos_array) :
                array_push($response_array_ordenado, $datos_array);
            endforeach;

            array_push($response_disponibilidades, $response_array_ordenado);

            // Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
            if (!empty($UnElemento)) :
                $servicio_hora["Disponibilidad"] = $response_array_ordenado;
            else :
                $servicio_hora["Disponibilidad"] = $response_disponibilidades;
            endif;
            //$servicio_hora["Disponibilidad"] = $response_disponibilidades;
            $servicio_hora["name"] = $nombre_elemento_consulta;

            array_push($response, $servicio_hora);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'Noseencontraronregistros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function valida_hora_con_par($array_par, $horaInicial, $tee, $IDElemento, $Fecha, $hora_real, $IDClub)
    {


        $dbo = &SIMDB::get();
        $disponible = "S";
        //Valido el tee contrario
        if ($tee == "Tee1") {
            $tee = "Tee10";
        } else {
            $tee = "Tee1";
        }

        $FechaHoravalida = $Fecha . " " . $horaInicial;
        $FechaHoraPar = $Fecha . " " . $array_par[$horaInicial];

        if ($IDClub != 7) {
            $FechaHoravalida = $FechaHoraPar;
        }

        if (!empty($array_par[$horaInicial]) && $array_par[$horaInicial] != "00:00:00" && strtotime($FechaHoravalida) >= strtotime($FechaHoraPar)) :
            //verifico si esta reservado y si la hora ya pasó
            $hora_inicio_par = $Fecha . " " . $array_par[$horaInicial];
            //$hora_inicio_par = "2018-06-02" . " " .$array_par[$horaInicial];

            $sql_reserva_hora = "SELECT IDReservaGeneral, IDServicioTipoReserva FROM ReservaGeneral, Socio WHERE  ReservaGeneral.IDClub = '" . $IDClub . "' and  Fecha = '" . $Fecha . "' and Hora = '" . $array_par[$horaInicial] . "' and Tee = '" . $tee . "' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub";
            $qry_reserva_hora = $dbo->query($sql_reserva_hora);

            //if($horaInicial=="10:03:00")
            //echo $sql_reserva_hora;

            if ((int) $dbo->rows($qry_reserva_hora) > 0) {
                $disponible = "N";
            }

            if ($IDClub == 7 && $disponible == "S") {
                if (strtotime($hora_real) > strtotime($hora_inicio_par)) {
                    $disponible = "S";
                } else {
                    $disponible = "N";
                }
            }

            //Si la reserva es tipo 9 hoyos asi tenga configurado hora par no se cuenta por que no bloquea el tee contrario
            $datos_reserva = $dbo->fetchArray($qry_reserva_hora);
            if ($datos_reserva["IDServicioTipoReserva"] == 5733) {
                $disponible = "S";
            }

        endif;

        return $disponible;
    }

    public function get_reserva_asociada($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ReservaGeneral WHERE IDSocio = '" . $IDSocio . "' and IDReservaGeneral =  '" . $IDReserva . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . 'Encontrados';
            while ($row_reserva = $dbo->fetchArray($qry)) :
                $reserva["IDClub"] = $IDClub;
                $reserva["IDSocio"] = $IDSocio;
                $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                $reserva["IDServicio"] = $row_reserva["IDServicio"];
                $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
                $reserva["NombreServicio"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                $reserva["Fecha"] = $row_reserva["Fecha"];
                $reserva["Tee"] = $row_reserva["Tee"];

                $estado_reserva = $row_reserva["IDEstadoReserva"];

                if (strlen($row_reserva["Hora"]) != 8) :
                    $row_reserva["Hora"] .= ":00";
                endif;

                $reserva["Hora"] = $row_reserva["Hora"];

                $zonahoraria = date_default_timezone_get();
                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                //Reserva automaticas
                $response_otra_reserva = array();
                $sql_otra_reserva = $dbo->query("Select * From  ReservaGeneralAutomatica Where IDReservaGeneral = '" . $IDReserva . "'");
                while ($r_otra_reserva = $dbo->fetchArray($sql_otra_reserva)) :

                    //Si la reserva es Tipo = Repetir solo muestro esta en el resumen las demas no para no generar confusion
                    if ($r_otra_reserva["Tipo"] == "Repetir") :
                        unset($response_otra_reserva);
                        $response_otra_reserva = array();
                    endif;

                    $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $r_otra_reserva["IDReservaGeneralAsociada"] . "' ", "array");

                    $otra_reserva[IDReservaGeneral] = $datos_reserva["IDReservaGeneral"];
                    $otra_reserva["IDReserva"] = $datos_reserva["IDReservaGeneral"];
                    $otra_reserva["IDServicio"] = $datos_reserva["IDServicio"];
                    $id_servicio_maestro_otro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $datos_reserva["IDServicio"] . "'");

                    $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro_otro . "'");
                    if (empty($nombre_servicio_personalizado)) {
                        $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro_otro . "'");
                    }

                    $otra_reserva["NombreServicio"] = $NombreServicio;
                    $otra_reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                    if ($estado_reserva == "3") :
                        //$otra_reserva["NombreElemento"] = "Se asignará elemento automaticamente de ser necesario";
                        $otra_reserva["NombreElemento"] = $nombre_servicio_personalizado;

                    else :
                        $otra_reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'");
                    endif;
                    $otra_reserva["Fecha"] = $datos_reserva["Fecha"];
                    $otra_reserva["Tee"] = $datos_reserva["Tee"];
                    if (strlen($datos_reserva["Hora"]) != 8) :
                        $datos_reserva["Hora"] .= ":00";
                    endif;

                    $otra_reserva["Hora"] = $datos_reserva["Hora"];
                    $zonahoraria = date_default_timezone_get();
                    $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                    $otra_reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                    array_push($response_otra_reserva, $otra_reserva);
                endwhile;

                // Muestro las asociadas solo en los servicios donde se reserva otro elemnto, por ejemplo en clase de tenis se asocioa un cancha de tenis
                $ServicioAsociado = (int) $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                if ($ServicioAsociado > 0) {
                    $reserva["ReservaAsociada"] = $response_otra_reserva;
                } else {
                    $reserva["ReservaAsociada"] = "";
                }

                array_push($response, $reserva);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'Noseencontraronregistros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function verificar_disponibilidad_auxiliar($IDClub, $IDServicio, $Fecha, $Hora, $IDAuxiliar)
    {
        $dbo = &SIMDB::get();
        // Consulto los auxiliares reservados en esta fecha y hora
        $sql_reserva_auxiliar = $dbo->query("Select * From AuxiliarReserva Where IDClub = '$IDClub' and IDServicio = '$IDServicio' and Fecha = '$Fecha' and Hora = '$Hora' and IDAuxiliar = '$IDAuxiliar'");
        if ($dbo->rows($sql_reserva_auxiliar) > 0) :
            return "1"; // Si esta reservado
        else :
            return "0"; // NO esta reservado
        endif;
    }

    public function get_auxiliares($IDClub, $IDServicio, $Fecha, $Hora, $VerSoloDisponibles = "S", $IDReservaGeneral = "", $IDClubAsociado = "", $IDSocio = "")
    {
        $dbo = &SIMDB::get();


        //if(!empty($IDReservaGeneral) && $IDClub=="227"){
        if ($IDClub == "227") {
            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
            $respuesta_aux = SIMWebServiceCountryMedellin::App_AsistentesDisponibles($IDClub, $IDReservaGeneral, $IDSocio);
            return $respuesta_aux;
        }


        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $response = array();

        if (!empty($IDServicio) && !empty($Fecha) && !empty($Hora)) {
            $Hora = SIMWebService::validar_formato_hora($Hora);

            $dia_fecha = date('w', strtotime($Fecha));
            $sql_dispo_aux_gral = "SELECT AUXD.*
	                                    From AuxiliarDisponibilidadDetalle AUXD, AuxiliarDisponibilidad AD
	                                    Where AUXD.IDAuxiliarDisponibilidad=AD.IDAuxiliarDisponibilidad and  AUXD.IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and '" . $Hora . "'>=HoraDesde and '" . $Hora . "'<=HoraHasta and AD.Activo='S'";
            $qry_dispo_aux_gral = $dbo->query($sql_dispo_aux_gral);
            $response_auxiliar = array();
            if ($dbo->rows($qry_dispo_aux_gral) > 0) {
                while ($row_dispo_aux_gral = $dbo->fetchArray($qry_dispo_aux_gral)) {
                    $array_auxiliares_disponible = explode("|", $row_dispo_aux_gral["IDAuxiliar"]);
                    if (count($array_auxiliares_disponible) > 0) :
                        foreach ($array_auxiliares_disponible as $IDAuxiliar) :
                            if ($IDAuxiliar != "") {
                                //Verifico si existe el auxiliar
                                $datos_auxiliar = $dbo->fetchAll("Auxiliar", " IDAuxiliar = '" . $IDAuxiliar . "' ", "array");
                                $id_auxiliar = $datos_auxiliar["IDAuxiliar"];
                                $id_auxiliar = SIMWebService::verifica_auxiliar_otro_servicio($id_auxiliar, "", $IDClub, $datos_auxiliar["NumeroDocumento"]);
                                $array_aux_otros = explode(",", $id_auxiliar);
                                $array_condicion_aux = array();
                                $array_condicion_aux_cierre = array();
                                foreach ($array_aux_otros as $id_aux) {
                                    $array_condicion_aux[] = " IDAuxiliar like '%" . $id_aux . ",%' ";
                                    $array_condicion_aux_cierre[] = " IDAuxiliar like '%" . $id_aux . "%' ";
                                }
                                if (count($array_condicion_aux) > 0) {
                                    $condicion_aux = " and (" . implode(" or ", $array_condicion_aux) . ")";
                                    $condicion_aux_cierre = " and (" . implode(" or ", $array_condicion_aux_cierre) . ")";
                                }

                                if (!empty($id_auxiliar)) :
                                    $flag_disponible = SIMWebService::verificar_disponibilidad_auxiliar($IDClub, $IDServicio, $Fecha, $Hora, $IDAuxiliar);
                                    if ($flag_disponible == "0" && !empty($IDAuxiliar)) :
                                        //verifico que el auxiliar no este asignado en alguna reserva a esta hora
                                        $id_reserva = "";

                                        //aumento 30 min mas y 30 min menos para las reservas en que la hora no es constante
                                        if ($IDClub == 12) {
                                            $fecha_hora_reserva = $Fecha . " " . $Hora;
                                            $masminutos = strtotime('+30 minute', strtotime($fecha_hora_reserva));
                                            $menosminutos = strtotime('-30 minute', strtotime($fecha_hora_reserva));
                                            $nuevahoramas = date('H:i:s', $masminutos);
                                            $nuevahoramenos = date('H:i:s', $menosminutos);
                                            $condicion_hora = "Hora >= '" . $nuevahoramenos . "' and Hora <= '" . $nuevahoramas . "'";
                                        } else {
                                            $condicion_hora = "Hora = '" . $Hora . "'";
                                        }

                                        if (!empty($IDReservaGeneral)) {
                                            $condicion_gral = " and IDReservaGeneral <> '" . $IDReservaGeneral . "'  ";
                                        }

                                        //$id_reserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDAuxiliar like '%" . $IDAuxiliar . ",%' and Fecha = '" . $Fecha . "' and ( " . $condicion_hora . " )  " . $condicion_gral . $condicion_aux );
                                        $id_reserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", " Fecha = '" . $Fecha . "' and ( " . $condicion_hora . " )  " . $condicion_gral . $condicion_aux);

                                        if (!in_array($IDAuxiliar, $array_id_aux)) :

                                            //Valido si solo se quiere consultar solo los diponibles o mostrar todos para la lista de espera
                                            if (($VerSoloDisponibles == "S" && empty($id_reserva)) || $VerSoloDisponibles == "N") {



                                                $auxiliar["IDAuxiliar"] = $IDAuxiliar;
                                                $auxiliar["Nombre"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $IDAuxiliar . "'");
                                                $Foto = $dbo->getFields("Auxiliar", "Foto", "IDAuxiliar = '" . $IDAuxiliar . "'");
                                                $auxiliar["Foto"] = (empty($Foto)) ? "" : ELEMENTOS_ROOT . $Foto;
                                                $auxiliar["Disponible"] = empty($id_reserva) ? "S" : "N";
                                                $auxiliar["TextoDisponible"] = "";

                                                if (!empty($id_reserva)) {
                                                    $auxiliar["TextoDisponible"] = "No disponible ya reservado";
                                                }

                                                $orden = $dbo->getFields("Auxiliar", "Orden", "IDAuxiliar = '" . $IDAuxiliar . "'");
                                                //Si el orden esta repetido le pongo uno aleatorio por que si no lo hago y esta repetido no aparece
                                                if (in_array($orden, $array_orden) || $orden == "") {
                                                    $orden = rand(100, 10000);
                                                }
                                                $array_orden[] = $orden;
                                                $auxiliar["Orden"] = $orden;
                                                $auxiliar["HoraInicio"] = $row_dispo_aux_gral[HoraDesde];
                                                $auxiliar["HoraFin"] = $row_dispo_aux_gral[HoraHasta];

                                                //$tipo_auxiliar = $dbo->getFields( "Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $IDAuxiliar . "'" );
                                                //$auxiliar[ "Tipo" ] = $dbo->getFields( "AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $tipo_auxiliar . "'" );
                                                $array_id_aux[] = $IDAuxiliar;

                                                // CONSULTO SI EL AUXILIAR ESTA EN ALGUNA FECHA DE CIERRE PROGRAMADA
                                                $SQLCierre = "SELECT * FROM AuxiliarCierre WHERE FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha' AND HoraInicio <= '$Hora' AND HoraFin >= '$Hora' AND Dias LIKE '%$dia_fecha%' " . $condicion_aux_cierre;
                                                $QRYCierre = $dbo->query($SQLCierre);

                                                if ($dbo->rows($QRYCierre) <= 0) :
                                                    array_push($response_auxiliar, $auxiliar);
                                                endif;
                                            }
                                        endif;
                                    endif;
                                endif;
                            }
                        endforeach;
                    endif;
                }

                if (count($response_auxiliar) > 0) :

                    foreach ($response_auxiliar as $key_aux => $value_aux) {
                        if ((int) $value_aux["Orden"] == 0) {
                            $orden_aux = rand(1, 1000);
                        } else {
                            $orden_aux = $value_aux["Orden"];
                        }

                        $array_aux_orden[$orden_aux] = $value_aux;
                    }
                    ksort($array_aux_orden);
                    unset($response_auxiliar);
                    foreach ($array_aux_orden as $key_aux => $value_aux) {
                        $response_auxiliar[] = $value_aux;
                    }

                    $auxiliar_disponible["IDClub"] = $IDClub;
                    $auxiliar_disponible["Auxiliares"] = $response_auxiliar;
                    array_push($response, $auxiliar_disponible);
                    $respuesta["message"] = count($response_auxiliar) . 'Encontrados';
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                else :

                    if ($IDClub == 133 && (($Hora > "06:00:00" && $Hora < "07:00:00") || ($Hora > "18:00:00"))) :
                        $mensaje = "Lo sentimos, en estos horarios la reserva debe ser agendada directamente con el profesor/monitor o con la oficina de Tenis";
                    else :
                        $mensaje = "No se encontraron profesores/monitores disponibles!";
                    endif;

                    $respuesta["message"] = $mensaje;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            } else {

                if ($IDClub == 133 && (($Hora > "06:00:00" && $Hora < "07:00:00") || ($Hora > "18:00:00"))) :
                    $mensaje = "Lo sentimos, en estos horarios la reserva debe ser agendada directamente con el profesor/monitor o con la oficina de Tenis";
                else :
                    $mensaje = "No se encontraron profesores/monitores disponibles.";
                endif;

                $respuesta["message"] = $mensaje;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

        } else {
            $respuesta["message"] = "5." . 'Atencionfaltanparametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_modalidades($IDClub, $IDTipoModalidadEsqui, $IDElemento, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $response = array();

        if (!empty($IDTipoModalidadEsqui)) :
            $condicion = " and IDTipoModalidadEsqui = '" . $IDTipoModalidadEsqui . "' ";
        endif;

        if (!empty($IDElemento)) :
            // consulto las modalidades del elemento
            $sql_servicio_modalidad = "SELECT IDTipoModalidadEsqui FROM  ServicioElementoModalidad Where IDServicioElemento = '" . $IDElemento . "'";
            $qry_servicio_modalidad = $dbo->query($sql_servicio_modalidad);
            while ($r_servicio_modalidad = $dbo->fetchArray($qry_servicio_modalidad)) :
                $array_servicio_modalidad[] = $r_servicio_modalidad["IDTipoModalidadEsqui"];
            endwhile;
            if (count($array_servicio_modalidad) > 0) :
                $id_modalidades = implode(",", $array_servicio_modalidad);
            else :
                $id_modalidades = 0;
            endif;
            $condicion = " and IDTipoModalidadEsqui in (" . $id_modalidades . ")";

        endif;

        if (!empty($IDClub)) {
            $response = array();
            $sql = "SELECT TME.IDTipoModalidadEsqui,TME.Nombre,TME.IDClub,TME.Descripcion FROM TipoModalidadEsqui TME " . $tabla_join . " WHERE TME.Publicar = 'S' and TME.IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY TME.Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = 'Encontrados';
                while ($r = $dbo->fetchArray($qry)) {
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDTipoModalidad"] = $r["IDTipoModalidadEsqui"];
                    $seccion["Modalidad"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    array_push($response, $seccion);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = 'No se encontraron registros';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

        } else {
            $respuesta["message"] = "6." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function verificar_socio_grupo_fecha($IDClub, $IDSocioInvitado, $Fecha, $IDServicio)
    {

        $dbo = &SIMDB::get();

        $respuesta_valida_invitado = SIMWebService::verificar_socio_grupo($IDClub, $IDSocioInvitado, $Fecha, $IDServicio, $Hora);
        if ($respuesta_valida_invitado == 1) :
            $nombre_socio_invitado = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitado . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitado . "'");
            $respuesta["message"] = "El invitado: " . $nombre_socio_invitado . ", solo puede estar en un grupo por dia.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        else :
            $respuesta["message"] = "valido";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        endif;

        return $respuesta;
    }

    public function verificar_socio_grupo($IDClub, $IDSocio, $Fecha, $IDServicio, $Hora)
    {

        $dbo = &SIMDB::get();

        $flag_valido = 0;
        $validar_otra = "S";

        $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);
        $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array");
        $MaximoReservaSocioServicio = $datos_disponibilidad["MaximoReservaDia"];
        $PermiteReservaDespuesdeprimerturno = $datos_disponibilidad["PermiteReservaCumplirTurno"];
        $TiempoDespues = $datos_disponibilidad["TiempoDespues"];
        $MedicionTiempoDespues = $datos_disponibilidad["MedicionTiempoDespues"];
        $IntervaloTurno = $datos_disponibilidad["Intervalo"];

        //Valido si en la configuracion permite a un socio tomar otro turno dspues que cumpla el que tiene en el dia solo aplica si esta en el dia actual
        if ($PermiteReservaDespuesdeprimerturno == "S" && $Fecha == date("Y-m-d")) {
            switch ($MedicionTiempoDespues):
                case "Dias":
                    $minutos_posterior_turno = (60 * 24) * $TiempoDespues;
                    break;
                case "Horas":
                    $minutos_posterior_turno = 60 * $TiempoDespues;
                    break;
                case "Minutos":
                    $minutos_posterior_turno = $TiempoDespues;
                    break;
                default:
                    $minutos_posterior_turno = 0;
            endswitch;

            //Le sumo el intervalo del turno para calcular la siguiente hora que puede reservar despues de finalizar el turno
            $minutos_posterior_turno += (int) $IntervaloTurno;

            //Consulto cual es la utima que reserva que tiene en el dia para calcula con esa hora
            $sql_reserva_dia_hora = "SELECT Hora From ReservaGeneral Where (IDSocio = '" . $IDSocio . "' or IDSocioBeneficiario = '" . $IDSocio . "')  and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' Order by Hora Desc Limit 1";
            $result_reserva_dia_hora = $dbo->query($sql_reserva_dia_hora);
            $row_reserva_dia_hora = $dbo->fetchArray($result_reserva_dia_hora);
            $ultimo_turno_dia = $Fecha . " " . $row_reserva_dia_hora["Hora"];
            $hora_actual_peticion = date('Y-m-d H:i:s');
            $hora_volver_reservar = strtotime('+' . $minutos_posterior_turno . ' minute', strtotime($ultimo_turno_dia));
            if (strtotime($hora_actual_peticion) >= $hora_volver_reservar) :
                $validar_otra = "N";
            else :
                $validar_otra = "S";
            endif;

            if ($validar_otra == "N") {
                //Consulto como invitado
                $sql_socio_grupo = "SELECT RG.Hora
	                                                                                    FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "' or RG.IDSocio='" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.IDServicio = '" . $IDServicio . "'
	                                                                                    ORDER BY RG.Hora Desc Limit 1 ";
                $result_reserva_dia_hora = $dbo->query($sql_socio_grupo);
                $row_reserva_dia_hora = $dbo->fetchArray($result_reserva_dia_hora);
                $ultimo_turno_dia = $Fecha . " " . $row_reserva_dia_hora["Hora"];
                $hora_actual_peticion = date('Y-m-d H:i:s');
                $hora_volver_reservar = strtotime('+' . $minutos_posterior_turno . ' minute', strtotime($ultimo_turno_dia));
                if (strtotime($hora_actual_peticion) >= $hora_volver_reservar) :
                    $validar_otra = "N";
                else :
                    $validar_otra = "S";
                endif;
                //Fin Consulto como invitado
            }
        }
        //Fin Valida tiempo despues

        if ($IDClub == 106) :
            $condicion = "AND RG.IDEstadoReserva = 1";
        endif;

        $sql_socio_grupo = "SELECT RGI.*
	                            FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "' or RG.IDSocio='" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.IDServicio = '" . $IDServicio . "' $condicion
	                            ORDER BY IDReservaGeneralInvitado Desc ";
        $qry_socio_grupo = $dbo->query($sql_socio_grupo);

        if (($MaximoReservaSocioServicio <= 1 || $dbo->rows($qry_socio_grupo) >= $MaximoReservaSocioServicio) && $validar_otra == "S") {
            //Consulto si el socio esta en otro grupo de invitados el mismo dia de la reserva o si es dueño de una reserva de tenis el mismo dia
            //$sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.IDServicio = '" . $IDServicio . "' ORDER BY IDReservaGeneralInvitado Desc ";
            if ($dbo->rows($qry_socio_grupo) > 0) :
                $flag_valido = 1;
            endif;

            //Consulto que tampoco sea dueño de una reserva
            $IDSocioOtraReserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", " (IDSocio = '" . $IDSocio . "' or IDSocioBeneficiario = '" . $IDSocio . "') and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' ");
            if ((int) $IDSocioOtraReserva > 0) :
                $flag_valido = 1;
            endif;
        }

        //Cuando este parametro esta prendido no valido si el invitado esta en otra reserva ya que las reglas son otras
        if ($datos_disponibilidad["PermiteReservaComoInvitado"] == "S") {
            $flag_valido = 0;
        }

        return $flag_valido;
    }

    public function verifica_servicio_pares($IDServicio, $IDCLub)
    {
        $dbo = &SIMDB::get();

        $respuesta[Servicio] = $IDServicio;
        $respuesta[Club] = $IDCLub;

        $identificadorServicio = $dbo->getFields("Servicio", "IdentificadorServiciosPadres", "IDServicio = '$IDServicio'");

        if ((int) $identificadorServicio > 100000) {

            $arrayServicios = array();
            $arrayClubes = array();

            $SQLigualesServicio = "SELECT IDServicio, IDClub FROM Servicio WHERE IdentificadorServiciosPadres = $identificadorServicio";
            $qryIgualesServicio = $dbo->query($SQLigualesServicio);

            while ($row = $dbo->fetchArray($qryIgualesServicio)) :
                $arrayServicios[] = $row[IDServicio];
                $arrayClubes[] = $row[IDClub];
            endwhile;

            if (count($arrayServicios) > 0) :
                $IDServicio = implode(",", $arrayServicios);
            endif;

            if (count($arrayClubes) > 0) :
                $arrayClubes = array_unique($arrayClubes);
                $IDCLub = implode(",", $arrayClubes);
            endif;

            $respuesta[Servicio] = $IDServicio;
            $respuesta[Club] = $IDCLub;
        }

        return $respuesta;
    }

    public function get_reservas_servicio($IDClub, $IDServicio, $Fecha = "", $IDServicioElemento = "", $IDSocio = "", $Orden = "")
    {

        $dbo = &SIMDB::get();

        $datos_servicio = $dbo->fetchAll("Servicio", "IDServicio = $IDServicio");

        if (!empty($Orden)) {
            $order = $Orden;
        } else {
            $order = " ReservaGeneral.Fecha Desc, ReservaGeneral.Hora ASC ";
        }

        // BUSCAMOS EL PADRE DEL CLUB
        $IDClubPadre = $dbo->getFields("Club", "IDClubPadre", "IDClub = $IDClub");
        // BUSCAR LOS HERMANOS DEL CLUB

        if ($IDClubPadre > 0) :
            $SQLHermanos = "SELECT IDClub FROM Club WHERE IDClubPadre = $IDClubPadre";
            $QRYHermanos = $dbo->query($SQLHermanos);
            while ($Hermanos = $dbo->fetchArray($QRYHermanos)) :
                $arrayHermanos[] = $Hermanos[IDClub];
            endwhile;
            $ArregloClubes = implode(",", $arrayHermanos);
        else :
            $ArregloClubes = $IDClub;
        endif;

        $response = array();

        $where = "";
        if (!empty($Fecha)) {
            $where .= " AND ReservaGeneral.Fecha = '" . $Fecha . "' ";
        }

        if (!empty($IDSocio)) {
            $where .= " AND ( Socio.NumeroDocumento = '$IDSocio' OR Accion = '$IDSocio' OR Nombre LIKE '%$IDSocio%' OR Apellido LIKE '%$IDSocio%' OR ReservaGeneral.NombreBeneficiario LIKE '%$IDSocio%' )   ";

            if (empty($Fecha)) {
                $where .= " AND Fecha >= CURDATE()  ";
            }

            $order = " ReservaGeneral.Fecha ASC, ReservaGeneral.Hora ASC ";
        }

        // PARA FORET NO MOSTRAMOS LAS AUTOMATICAS
        if ($IDClub == 140) :
            $where .= " AND Tipo <> 'Automatica'";
        endif;

        if (!empty($IDServicioElemento)) {

            $datos_elemento = $dbo->fetchAll("ServicioElemento", "IDServicioElemento = $IDServicioElemento");

            if ($datos_elemento[ValidarIdentificadorElemento] == 1) :
                // VALIDO ELEMENTOS IGUALES PARA HACER BUSQUEDA DE TODOS
                $Elementos = SIMWebService::verifica_elemento_otro_servicio($IDServicioElemento, "", $IDClub);
                $where .= " AND ReservaGeneral.IDServicioElemento IN ($Elementos) ";
            else :
                $where .= " AND ReservaGeneral.IDServicioElemento = '" . $IDServicioElemento . "' ";
            endif;
        }

        // VALIDO LOS SERVICIOS CON EL MISMO IDENTIFICADOR PARA LISTAR LAS RESERVAS
        if ($datos_servicio[ValidarServiciosPadres] == 1) :
            $ServicioClubes = SIMWebService::verifica_servicio_pares($IDServicio, $IDClub);

            $condicionClubReserva = " AND ReservaGeneral.IDClub IN ($ServicioClubes[Club])";
            $condicionClubSocio = " AND Socio.IDClub IN ($ServicioClubes[Club])";
            $condicionservicio = " AND ReservaGeneral.IDServicio IN ($ServicioClubes[Servicio])";
        endif;

        $sql = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido, Socio.Accion, Socio.Predio
	        FROM ReservaGeneral, Socio
	        WHERE ReservaGeneral.IDEstadoReserva = 1
	        $condicionservicio $condicionClubSocio $condicionClubReserva
	        AND ReservaGeneral.IDSocio = Socio.IDSocio " . $where . "  ORDER BY " . $order;

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . 'Encontrados';
            while ($row_reserva = $dbo->fetchArray($qry)) :
                $reserva["IDClub"] = $IDClub;
                $reserva["IDSocio"] = $row_reserva["IDSocio"];
                $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                $reserva["Socio"] = array("TipoSocio" => $row_reserva[TipoSocio], "Nombre" => $row_reserva["Nombre"], "Apellido" => $row_reserva["Apellido"], "Accion" => $row_reserva["Accion"], "Predio" => $row_reserva["Predio"]);
                $reserva["IDServicio"] = $row_reserva["IDServicio"];
                $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                $reserva["Fecha"] = $row_reserva["Fecha"];
                $reserva["Tee"] = $row_reserva["Tee"];
                $reserva["Cumplida"] = $row_reserva["Cumplida"];
                $reserva["Tipo"] = $row_reserva["Tipo"];

                if (strlen($row_reserva["Hora"]) != 8) :
                    $row_reserva["Hora"] .= ":00";
                endif;

                if ($IDClub == 140) :

                    $Intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = $row_reserva[IDDisponibilidad]");
                    $Turnos = $dbo->getFields("ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = $row_reserva[IDServicioTipoReserva]");

                    $minutos = $Turnos * $Intervalo;
                    $HoraFinalReserva = date("H:i:s", strtotime('+' . $minutos . ' minutes', strtotime($row_reserva["Hora"])));
                    $row_reserva["Hora"] .= " Hasta " . $HoraFinalReserva;
                endif;

                $reserva["Hora"] = $row_reserva["Hora"];
                array_push($response, $reserva);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = 'No se encontraron registros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } //end function

    public function elimina_reserva_general($IDClub, $IDSocio, $IDReserva, $Admin = "", $Razon = "", $EliminarParaMi = "")
    {

        $dbo = &SIMDB::get();

        require_once LIBDIR . "SIMServicioReserva.inc.php";

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {


            if ($IDClub == 227) {
                require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
                $respuesta_eliminar = SIMWebServiceCountryMedellin::EliminaReserva($IDClub, $IDSocio, $IDReserva);
                return $respuesta_eliminar;
            }

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio= '" . $datos_reserva["IDServicio"] . "' ", "array");

            if ($IDClub == 125 && empty($datos_reserva)) :
                require_once LIBDIR . "SIMUruguay.inc.php";
                $Matricula = $dbo->getFields("Socio", "AccionPadre", "IDSocio = $IDSocio");
                $info = SIMUruguay::eliminar_torneo($Matricula, $IDReserva);
                if ($info[Correcto] == 1) :
                    $respuesta["message"] = "Reserva a torneo eliminada correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                    return $respuesta;
                else :
                    $respuesta["message"] = $info["Error"];
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endif;

            //verifico si el servicio está configurado para preguntar algun medio de pago
            $ServicioPago = "N";
            $sql_tip_pag = "SELECT IDServicioTipoPago FROM ServicioTipoPago WHERE IDServicio = '" . $datos_reserva["IDServicio"] . "' Limit 1";
            $r_tip_pag = $dbo->query($sql_tip_pag);
            while ($row_tip_pag = $dbo->fetchArray($r_tip_pag)) {
                $ServicioPago = "S";
                //Verifico si la reserva tiene alguna forma de pago si no es que le dio cancelar sin ir a la pasarela
                if ((int) $datos_reserva["IDTipoPago"] <= 0 || $IDClub == 88) {
                    $PermiteEliminar = "S";
                }
            }

            if (!empty($id_socio)) {

                //Verifico que este en el tiempo limite para reservar
                $id_disponibilidad = (int) $datos_reserva["IDDisponibilidad"];

                if ($id_disponibilidad > 0) :
                    $tiempo_cancelacion = (int) $dbo->getFields("Disponibilidad", "TiempoCancelacion", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $medicion_cancelacion = $dbo->getFields("Disponibilidad", "MedicionTiempo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    switch ($medicion_cancelacion):
                        case "Dias":
                            $minutos_anticipacion = (60 * 24) * $tiempo_cancelacion;
                            break;
                        case "Horas":
                            $minutos_anticipacion = 60 * $tiempo_cancelacion;
                            break;
                        case "Minutos":
                            $minutos_anticipacion = $tiempo_cancelacion;
                            break;
                        default:
                            $minutos_anticipacion = 2;

                    endswitch;
                else :
                    $tiempo_cancelacion = 2;
                    $medicion_cancelacion = "Horas";
                    $minutos_anticipacion = 120;
                endif;

                $fecha_reserva = $datos_reserva["Fecha"];
                $hora_reserva = $datos_reserva["Hora"];
                $aux_reserva = $datos_reserva["IDAuxiliar"];
                $id_servicio = $datos_reserva["IDServicio"];

                //Especial Country para reservas de cancha en cualquier momento si es con profesor segun configuracion
                if (($IDClub == 44 || $IDClub == 8) && empty($aux_reserva) && $id_servicio == 3941) :
                    $tiempo_cancelacion = 0;
                    $medicion_cancelacion = "minutos";
                    $minutos_anticipacion = 0;
                endif;
                //FIN ESPECIAL country

                $hora_inicio_reserva = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($fecha_reserva . " " . $hora_reserva));
                $fechahora_actual = strtotime(date("Y-m-d H:i:s"));

                //$id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
                //$envia_push_eliminacion = $dbo->getFields("Servicio", "PushEliminaReserva", "IDServicio = '" . $id_servicio . "'");
                $envia_push_eliminacion = $datos_servicio["PushEliminaReserva"];

                $id_servicio_cancha = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");

                //$fechahora_actual =  strtotime ( "2016-03-29 07:00:00" );

                //Verifico que la reserva exista
                $id_reservada_existe = $datos_reserva["IDReservaGeneral"];
                if (empty($id_reservada_existe)) :
                    $respuesta["message"] = "La reserva ya fue eliminada";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;

                //Especial atc se puede borrar pero si es antes de 3 horas le sale un mensaje
                if ($IDClub == 26) :
                    $mensaje_eliminacion = "";
                    $id_servicio = $datos_reserva["IDServicio"];
                    $hora_inicio_reserva_esp = strtotime('-180 minute', strtotime($fecha_reserva . " " . $hora_reserva));
                    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)) :
                        switch ($id_servicio):
                            case "1490":
                                $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que el turno de coliseo no sea utilizado se le cobrará el costo de coliseo.";
                                break;
                            case "2106":
                                $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que él profesor no sea utilizado en ese horario se le cobrará el costo de la clase.";
                                break;

                            case "1446":
                            case "1484":
                            case "2109":
                            case "2110":
                            case "3450":
                            case "4350":
                            case "5035":
                            case "5039":
                            case "7973":
                            case "3941":
                                $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que el turno no sea utilizado en ese horario se le cobrará el costo del turno.";
                                break;
                        endswitch;
                    endif;
                endif;
                //FIN Especial atc se puede borrar pero si es antes de 3 horas le sale un mensaje

                //Especial BTCC si elimina antes de 12 horas prof o minitor sale mensaje
                if ($IDClub == 72 && !empty($aux_reserva) && $id_servicio == 8649) :
                    $hora_inicio_reserva_esp = strtotime('-720 minute', strtotime($fecha_reserva . " " . $hora_reserva));
                    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)) :
                        $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (12 horas), debe dirigirse al caddie master para cancelar el valor del servicio de caddie y monitor.";
                    endif;
                endif;

                //Especial BTCC si elimina antes de 12 horas prof o minitor sale mensaje
                if ($IDClub == 72 && $id_servicio == 8539) :
                    $hora_inicio_reserva_esp = strtotime('-720 minute', strtotime($fecha_reserva . " " . $hora_reserva));
                    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)) :
                        $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (12 horas), debe dirigirse al caddie master para cancelar el valor del servicio de caddie y profesor.";
                    endif;
                endif;
                //Especial BTCC si elimina antes de 1 horas prof o minitor sale mensaje
                if ($IDClub == 72 && $id_servicio == 8649 && empty($aux_reserva)) :
                    $hora_inicio_reserva_esp = strtotime('-60 minute', strtotime($fecha_reserva . " " . $hora_reserva));
                    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)) :
                        $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (1 hora), debe dirigirse al caddie master para cancelar el valor del servicio de caddie.";
                    endif;
                endif;
                //FIN Especial BTCC

                //Especial Country para reservas de 6am y 7am  solo hasta las 8pm del dia anterior cuando tiene profesor
                if (($IDClub == 44 || $IDClub == 8) && empty($Admin)) :
                    $dia_manana = date('Y-m-d', time() + 84600);
                    $fecha_hoy_v = date("Y-m-d");
                    if (((date("H:i:s") >= "20:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva) && ($id_servicio == "3861" || $id_servicio == "36") && ($hora_reserva == '06:00:00' || $hora_reserva == '07:00:00' || $hora_reserva == '08:00:00')) :
                        $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con profesor/monitor hasta antes de las 8pm para turnos de 6am, 7am y 8am ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                if (($IDClub == 8 && $id_servicio == 32) || ($IDClub == 44 && $id_servicio == 11242 && empty($Admin))) :
                    $dia_manana = date('Y-m-d', time() + 84600);
                    $fecha_hoy_v = date("Y-m-d");
                    if (((date("H:i:s") >= "23:59:59" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)) :
                        $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva antes de las 12 Pm del dia anterior ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                //Especial Arrayanes en golf solo pemite eleiminar hasta las 7pm del dia anterior
                if (($IDClub == 11) && $id_servicio == 122 && empty($Admin)) :
                    $dia_manana = date('Y-m-d', time() + 84600);
                    $fecha_hoy_v = date("Y-m-d");
                    if (((date("H:i:s") >= "19:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)) :
                        $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con antes de las 7pm del dia anterior ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                /*
                    //Especial Arrayanes en tenis y clases tenis solo pemite eleiminar hasta las 8pm del dia anterior
                    if (($IDClub == 11) && ($id_servicio == 227 || $id_servicio == 129) && empty($Admin)):
                    $dia_manana = date('Y-m-d', time() + 84600);
                    $fecha_hoy_v = date("Y-m-d");
                    if (((date("H:i:s") >= "20:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)):
                    $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con antes de las 8pm del dia anterior ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    endif;
                    endif;
                     */

                // especial country solo se puede hasta las 7pm del dia anterior en las reservas del fin de semana.
                $dia_reserva = date("w", strtotime($fecha_reserva));
                if (($IDClub == 44) && empty($Admin) && ($dia_reserva == 6 || $dia_reserva == 0) && ($id_servicio == 3889 || $id_servicio == 3888)) :
                    $dia_manana = date('Y-m-d', time() + 84600);
                    $fecha_hoy_v = date("Y-m-d");
                    if (((date("H:i:s") >= "19:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)) :
                        $mensaje_eliminacion = "Su Reserva está siendo eliminada fuera del horario permitido de cancelación, en caso de no cubrirse este turno se aplicará el Reglamento Vigente.";
                        $EliminadaFueraTiempo = S;
                        $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
                    endif;
                endif;

                //Especial La sabana la reserva no se puede eliminar si existen otras personas en el grupo de golf se asigna a otro miembro del grupo
                if (($IDClub == 95 && $id_servicio == 15964) || ($IDClub == 8 && $id_servicio == 31) || ($IDClub == 32 && $id_servicio == 2062) || ($IDClub == 47 && $id_servicio == 4270) || ($IDClub == 44 && $id_servicio == 3889) || ($IDClub == 44 && $id_servicio == 3888) || ($IDClub == 110 && $id_servicio == 19454 && ($EliminarParaMi == "S" || empty($EliminarParaMi))) || ($IDClub == 112 && $id_servicio == 19939) || $EliminarParaMi == "S") {
                    $permite_reasignar = "S";

                    if ($IDClub == 44 || $IDClub == 95 || $IDClub == 110 || $IDClub == 125) {
                        $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_reserva["IDDisponibilidad"] . "' ", "array");
                        //quito 1 al dueño de la reserva
                        $MinimoPersonasTurno = $datos_disponibilidad["MinimoInvitados"];
                        $sql_invi_tot = "SELECT count(IDReservaGeneralInvitado) as TotalInv FROM ReservaGeneralInvitado Where IDReservaGeneral  = '" . $IDReserva . "' ";
                        $r_invi_tot = $dbo->query($sql_invi_tot);
                        $row_invi_tot = $dbo->fetchArray($r_invi_tot);
                        $TotInv = (int) $row_invi_tot["TotalInv"] - 1;
                        if ($MinimoPersonasTurno > 1 && $TotInv < ((int) $MinimoPersonasTurno - 1)) {
                            // no se puede eliminar no cumple con la cantidad mimima de personas para tener la reserva en ese caso no se reasigna se elimina
                            $permite_reasignar = "N";
                        }
                    }

                    $sql_invi = "SELECT IDSocio FROM ReservaGeneralInvitado Where IDReservaGeneral  = '" . $IDReserva . "' and IDSocio>0 Limit 1";
                    $r_invi = $dbo->query($sql_invi);
                    if ($dbo->rows($r_invi) > 0 && $permite_reasignar == "S") {
                        $row_invi = $dbo->fetchArray($r_invi);
                        $datos_socio_nuevo = "SELECT Nombre,Apellido FROM Socio WHERE IDSocio = '" . $row_invi["IDSocio"] . "' Limit 1";
                        $r_socio_nuevo = $dbo->query($datos_socio_nuevo);
                        $row_socio_nuevo = $dbo->fetchArray($r_socio_nuevo);
                        $NombreNuevoSocio = $row_socio_nuevo["Nombre"] . " " . $row_socio_nuevo["Apellido"];
                        $AccionNuevoSocio = $row_socio_nuevo["Accion"];

                        $sql_reasigna = "UPDATE ReservaGeneral Set IDSocio = '" . $row_invi["IDSocio"] . "', IDSocioReserva = '" . $row_invi["IDSocio"] . "',CodigoRespuesta = 'La intenta eliminar socio " . $IDSocio . " y se reasigna a socio " . $row_invi["IDSocio"] . "',NombreSocio='" . $NombreNuevoSocio . "',AccionSocio='" . $AccionNuevoSocio . "' WHERE IDReservaGeneral = '" . $IDReserva . "' ";
                        $dbo->query($sql_reasigna);
                        //borro al nuevo dueño de los invitados
                        $sql_borra_reserva_inv = "DELETE FROM ReservaGeneralInvitado Where IDReservaGeneral  = '" . $IDReserva . "' and IDSocio = '" . $row_invi["IDSocio"] . "'";
                        $dbo->query($sql_borra_reserva_inv);
                        //Envio notificacion al socio nuevo dueño de la reserva
                        SIMUtil::notificar_nueva_reserva($IDReserva, $IDTipoReserva);
                        $MensajeReasignacion = "Ha sido asignado como dueño de una reserva en la que era invitado Fecha: " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                        SIMUtil::push_notifica_reserva_socio($IDClub, $row_invi["IDSocio"], $MensajeReasignacion);

                        //reasigno la reserva pero no la elimino
                        $respuesta["message"] = "La reserva fue reasignada a otro miembro del grupo correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
                //Fin La Sabana

                //Especial campestre pereira solo cancela 2 de martesa a viernes y 2 de sabado a lunes

                if ($IDClub == 15 && empty($Admin) && $id_servicio == 305) {
                    $fecha_hoy_semana = date("Y-m-d");
                    $year = date('Y', strtotime($fecha_hoy_semana));
                    $week = date('W', strtotime($fecha_hoy_semana));
                    $dia_reserva = date("w", strtotime($fecha_hoy_semana));

                    $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . $week));

                    if ((int) $dia_reserva >= 2 && (int) $dia_reserva <= 5) {
                        $fecha_inicio_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' +1 day')); //MARTES
                        $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' +4 day')); //Viernes
                        $mensaje = "entre martes y viernes";
                    } else {
                        $fecha_inicio_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' +5 day')); //Sabado
                        $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' +7 day')); //Lunes
                        $mensaje = "entre sabado y lunes";
                    }

                    $fechaInicio = $fecha_inicio_valida . " 00:00:00";
                    $fechaFin = $fecha_fin_valida . " 00:00:00";

                    $sqlValida = "SELECT COUNT(IDReservaGeneral) AS Cantidad FROM `ReservaGeneralEliminada` WHERE `IDSocio` = " . $IDSocio . " AND `FechaTrCr` > '" . $fechaInicio . "' AND FechaTrCr < '" . $fechaFin . "' ORDER BY `IDReservaGeneral` DESC";
                    $qryValida = $dbo->query($sqlValida);
                    $dato = $dbo->fetchArray($qryValida);

                    if ($dato["Cantidad"] >= 2) {
                        $respuesta["message"] = "Lo sentimos solo puedes cancelar 2 reservas " . $mensaje;
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                $EliminadaFueraTiempo = "N";
                //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
                /*
                    if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 44 && ($id_servicio == 3901 || $id_servicio == 3902 || $id_servicio == 8169 || $id_servicio == 3866 || $id_servicio == 3878 || $id_servicio == 5001 || $id_servicio == 3843)) {
                        $mensaje_eliminacion = "Su reserva esta siendo cancelada fuera de las horas permitidas de cancelación, recuerde que se cobrará el total en caso de no cubrir este turno.";
                        $EliminadaFueraTiempo = S;
                        $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
                    }
                    */

                //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
                if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 44 && ($id_servicio == 3905)) {
                    //$mensaje_eliminacion = "Recuerde que puede cancelar su reserva hasta 12 horas antes, de lo contrario será facturado el 50% del valor total del servicio a menos que este sea tomado por otro socio";
                    //$EliminadaFueraTiempo=S;
                    //$Admin=1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
                }

                //PARA LOS SERVICIOS QUE SE PUEDEN CANCELAR ANTES DE TIEMPO SEGUN CONFIGURACIÓN
                $sqlServicio = "SELECT ValidaEliminacionFueraHora, MensajeEliminacionFueraHora FROM Servicio WHERE IDServicio = " . $id_servicio;
                $qryServicio = $dbo->query($sqlServicio);
                $datos = $dbo->fetchArray($qryServicio);
                if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $datos["ValidaEliminacionFueraHora"]) {
                    $EliminadaFueraTiempo = S;
                    $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar

                    if (empty($datos["MensajeEliminacionFueraHora"])) {
                        $mensaje_eliminacion = "Recuerde que puede cancelar su reserva hasta 12 horas antes, de lo contrario será facturado el 50% del valor total del servicio a menos que este sea tomado por otro socio";
                    } else {
                        $mensaje_eliminacion = $datos["MensajeEliminacionFueraHora"];
                    }
                }

                //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
                if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 72 && ($id_servicio != 8649 && $id_servicio != 8539)) {
                    $mensaje_eliminacion = "Su reserva esta siendo cancelada fuera de las horas permitidas de cancelación. Recuerde que deberá pagar el total del servicio, a menos que otro socio use el turno.";
                    $EliminadaFueraTiempo = S;
                    $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
                }

                if ($tiempo_cancelacion == 1) {
                    $medicion_cancelacion = str_replace("s", "", $medicion_cancelacion);
                }

                if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && (empty($PermiteEliminar) || $PermiteEliminar == "S")) :
                    $respuesta["message"] = "No se puede eliminar la reserva debe ser minimo: " . $tiempo_cancelacion . " " . $medicion_cancelacion . " antes";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                else :

                    $datos_reserva_eli = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");

                    if (($datos_reserva_eli["IDTipoPago"] == 1 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
                        ($datos_reserva_eli["IDTipoPago"] == 12 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
                        ($datos_reserva_eli["IDTipoPago"] == 19 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
                        ($datos_reserva_eli["IDTipoPago"] == 26 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
                        ($datos_reserva_eli["IDTipoPago"] == 28 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
                        ($datos_reserva_eli["IDTipoPago"] == 29 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) &&
                        $IDClub != 88
                    ) : //Para pagos con payu no dejo que se elimine cuando se devuelva solo hata confirmar el estado del pago
                        $respuesta["message"] = "Esperando respuesta de la transaccion";
                        //$respuesta["message"] = "Delete From ReservaGeneral Where IDReservaGeneral  = '".$row_reserva_auto["IDReservaGeneral"]."'";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    //para credibanco las trabsacciones pagadas no se pueden eliminar
                    /*
                        if ($datos_reserva_eli["IDTipoPago"] == 12 && empty($Admin)) {
                            $EstadoTransaccion = $dbo->getFields("PagoCredibanco", "orderStatus", "reserved12 = '" . $IDReserva . "'");
                            if ($EstadoTransaccion == 2) {
                                $respuesta["message"] = "Transaccion pagada correctamente, para eliminar comuniquese con administrador";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                        */

                    //No permito eliminar reservas recien pagadas por pasarela por el tema que se pulsa el boton de atras y se elimina involuntariamente
                    $FechaHoraActual = date('Y-m-d H:i:s');
                    $EliminarDesde = strtotime('+10 minute', strtotime($datos_reserva_eli["FechaTrCr"]));
                    $EliminarDesde = date('Y-m-d H:i:s', $EliminarDesde);
                    if ($EliminarDesde >= $FechaHoraActual && $datos_reserva_eli["EstadoTransaccion"] == "A") {
                        $respuesta["message"] = "No puede eliminar una reserva recien pagada espere por lo menos 10 minutos";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    //fin validacion recien pagada



                    //verifico en la disponibilidad si la reserva permite la eliminación cuando fue creada por el starter
                    $permite_eliminar_reserva_creada_starter = $dbo->getFields("Disponibilidad", "PermiteEliminarCreadaStarter", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    //verifico que la reserva haya sido creada por el socio si fue por el starter verifico en la disponibilidad si se puede eliminar por el socio
                    $reservada_creada_por = $dbo->getFields("ReservaGeneral", "UsuarioTrCr", "IDReservaGeneral = '" . $IDReserva . "'");
                    if ($reservada_creada_por == "Starter" && empty($Admin) && $permite_eliminar_reserva_creada_starter == "N") :
                        $respuesta["message"] = "No se puede eliminar la reserva fue creada por el Starter y solo el starter puede eliminarla";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    else :

                        $borra_automatica = 0;
                        //Copio Reserva
                        /*
                            $sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, IDServicioTipoReserva, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
                            Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, IDServicioTipoReserva, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '".$Razon."', NOW(), NOW()
                            From ReservaGeneral
                            Where IDReservaGeneral  = '".$IDReserva."'");
                             */
                        $IP = SIMUtil::get_IP();
                        $sql_copia_reserva = $dbo->query("
			                                                        INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral,IDClub,IDSocio,IDSocioReserva,IDUsuarioReserva,IDServicio,IDServicioElemento,IDEstadoReserva	,IDDisponibilidad,IDAuxiliar,IDTipoModalidadEsqui,IDServicioTipoReserva,IDReservaGrupos,IDInvitadoBeneficiario,IDSocioBeneficiario,IDUsuarioCumplida,IDTipoPago,Cumplida,CumplidaCabeza,FechaCumplida,ObservacionCumplida,CantidadInvitadoSalon,Fecha,Hora	,Tee,Observaciones,Tipo,Notificado,NumeroInscripcion,CodigoPago,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,Pagado,PagoPayu,UsuarioTrCr,FechaTrCr,Razon,MensajeEliminacion,EliminadaFueraTiempo,UsuarioTrEd,FechaTrEd)
			                                                        Select IDReservaGeneral,IDClub,IDSocio,IDSocioReserva,IDUsuarioReserva,IDServicio,IDServicioElemento,IDEstadoReserva,IDDisponibilidad,IDAuxiliar,IDTipoModalidadEsqui,IDServicioTipoReserva,IDReservaGrupos,IDInvitadoBeneficiario,IDSocioBeneficiario,IDUsuarioCumplida,IDTipoPago,Cumplida,CumplidaCabeza,FechaCumplida,ObservacionCumplida,CantidadInvitadoSalon,Fecha,Hora	,Tee,Observaciones,Tipo,Notificado,NumeroInscripcion,CodigoPago,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,Pagado,PagoPayu,UsuarioTrCr,FechaTrCr,'" . $Razon . "','" . $mensaje_eliminacion . "','" . $EliminadaFueraTiempo . "',NOW(),NOW()
			                                                        From ReservaGeneral
			                                                        Where IDReservaGeneral  = '" . $IDReserva . "'");

                        //borro reserva
                        $sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '" . $IDReserva . "'");

                        //Si esta hablitado el Crear invitación eliminamos en modulo Invitados
                        // Eliminar invitaciones en el modulo invitados
                        $CrearInvitacionExterno = $dbo->getFields('Servicio', 'CrearInvitacionExterno', "IDServicio=$IDServicio");
                        if ($CrearInvitacionExterno == 'S') {
                            $IDReservaGeneralInvitado = $dbo->fetchAll('ReservaGeneralInvitado', "IDReservaGeneral = $IDReserva", "array");
                            if (count($IDReservaGeneralInvitado) > 0) :

                                foreach ($IDReservaGeneralInvitado as $Invitado) :
                                    //borro invitacion
                                    $sql_borra_socioinvitado = $dbo->query("DELETE FROM SocioInvitado WHERE IDClub = $IDCLub AND NumeroDocumento  = '" . $Invitado['Cedula'] . "'");
                                endforeach;
                            endif;
                        }
                        //Fin Crear invitaciones en

                        //Habilito codigos cortesia nuevamente
                        $sql_habilita_codigos =  $dbo->query("Update ClubCodigoPago Set Disponible= 'S', IDReservaGeneral = ''  Where IDReservaGeneral  = '$IDReserva'");
                        //borro invitados a esa reserva
                        $sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral  = '" . $IDReserva . "'");

                        //Verifico si tiene una reserva asociada para borrarla tambien
                        //$sql_asociada = "Select * From ReservaGeneralAutomatica Where IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and Fecha = '" . $fecha_reserva . "'";
                        $sql_asociada = "Select * From ReservaGeneralAutomatica Where IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "' and Fecha = '" . $fecha_reserva . "'";
                        $result_asociada = $dbo->query($sql_asociada);
                        $TurnosUtilizados = 0;
                        while ($row_asociada = $dbo->fetchArray($result_asociada)) :
                            $sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
				                                                                        Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '" . $Razon . "', NOW(), NOW()
				                                                                        From ReservaGeneral
				                                                                        Where IDReservaGeneral  = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
                            //borro reserva
                            $sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
                            //borro invitados a esa reserva                                
                            $sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral  = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
                            $TurnosUtilizados++;
                            $borra_automatica = 1;
                        endwhile;

                        //Si la reserva es una clase elimino la cancha que se reservó con la clase

                        if ($id_servicio_cancha > 0 && $borra_automatica == 0) :
                            // Consulto el servicio del club asociado a este servicio maestro
                            $IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");

                            // Borro la cancha asociada
                            //Copio Reserva
                            $sql_reserva_auto = "Select * FRom ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicio = '" . $IDServicioCanchaClub . "' and IDEstadoReserva = 1 and Fecha = '" . $fecha_reserva . "' and Hora = '" . $hora_reserva . "' and Tipo = 'Automatica' limit 1";
                            $result_reserva_auto = $dbo->query($sql_reserva_auto);
                            $row_reserva_auto = $dbo->fetchArray($result_reserva_auto);

                            $sql_copia_reserva_auto = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
				                                                                            Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '" . $Razon . "', NOW(), NOW()
				                                                                            From ReservaGeneral
				                                                                            Where IDReservaGeneral  = '" . $row_reserva_auto["IDReservaGeneral"] . "'");
                            //borro reserva
                            $sql_borra_reserva_auto = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '" . $row_reserva_auto["IDReservaGeneral"] . "'");

                        endif;

                        // SI ES DE LA PRADERA SE DEBE ELIMINAR DE SISCLUB
                        if ($IDClub == 16) :
                            require LIBDIR . "SIMWebServicePradera.inc.php";
                            SIMWebServicePradera::cancelar_reserva_facturacion_potosi($IDReserva);
                        endif;

                        // SI LA RESERVA FUE PAGADA CON TALONERA SE REVIERTE
                        if ($datos_reserva[IDTipoPago] == 16) :
                            require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";

                            $IDSocioConsume = $IDSocio;

                            //Para nuba revierte la cantidad de la talonera con el id del hijo que se guarda en reservas generales en beneficiario
                            if ($IDClub == 196 && $datos_reserva['IDSocioBeneficiario'] > 0)
                                $IDSocioConsume = $dbo->getFields("Socio", "IDSocio", "IDClub = 196 AND IDSocio = " . $datos_reserva['IDSocioBeneficiario']);

                            $ValorPagado = $datos_reserva[ValorPagado];

                            SIMWebServiceTaloneras::revertir_cantidad_talonera($IDClub, $IDReserva, $IDSocioConsume, $ValorPagado, $TurnosUtilizados, $Admin);
                        endif;

                        SIMUtil::notificar_elimina_reserva($IDReserva, $IDTipoReserva);

                        //Si el elemento reservado es una persona (profesor, peluquero, masajista, etc) y esta creado como empleado en app empleados envio notificacion push
                        SIMUtil::push_notifica_reserva_elimina($IDClub, $IDReserva, "Empleado");

                        if ($envia_push_eliminacion == "S") :
                            SIMUtil::push_notifica_reserva_elimina_socio($IDClub, $IDReserva, $Razon);
                        endif;

                        //Envio mensaje a lista de espera
                        //SIMUtil::push_notifica_libera_reserva($IDClub,$IDReserva);

                        $codigo_canje = SIMUtil::push_notifica_codigo_pago($IDReserva);
                        if (!empty($codigo_canje)) :
                            $msg_respuesta = " Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_canje . " Lo puede consultar tambien en el modulo de Notificaciones";
                        endif;

                        $respuesta["message"] = "Reserva eliminada correctamente. " . $msg_respuesta . $mensaje_eliminacion;
                        //$respuesta["message"] = "Delete From ReservaGeneral Where IDReservaGeneral  = '".$row_reserva_auto["IDReservaGeneral"]."'";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    endif;
                endif;
            } else {
                $respuesta["message"] = 'Error e lsocio no existeo no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "9." . 'Atencionfaltanparametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_contacto($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Email) && !empty($Comentario)) {

            $sql_seccion_not = $dbo->query("INSERT INTO Contacto (IDClub, IDSocio, Telefono, Ciudad, Direccion, Email, Comentario, Nombre, UsuarioTrCr, FechaTrCr) Values ('" . $IDClub . "','" . $IDSocio . "', '" . $Telefono . "','" . $Ciudad . "','" . $Direccion . "','" . $Email . "','" . $Comentario . "','" . $Nombre . "',WebService',NOW())");
            SIMUtil::notificar_contactenos($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre);
            $respuesta["message"] = 'Guardado';
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "10." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function buscar_elemento_disponible($IDClub, $IDServicio, $Fecha, $Hora, $IDElementoPadre = "", $IDTipoReserva = "", $EdadSocio = "")
    {

        $dbo = &SIMDB::get();
        $IDElemento = "";
        $elemento_encontrado = 0;
        $validar_campo_aut = "S";
        $datos_elemento_padre = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $IDElementoPadre . "' ", "array");
        // Verifico cuantos elementos tienen esta hora disponible

        // ORDENO ALETORIAMENTE PARA TOMAR CUALQUIER DISPONINILIDAD DE PRIMERO
        if ($IDClub == 155) :
            $orden = "Rand()";
        else :
            $orden = "HoraDesde";
        endif;

        $dia_fecha = date('w', strtotime($Fecha));
        $sql_dispo_hora = "SELECT IDServicioElemento,IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and ('" . $Hora . "' >= HoraDesde and '" . $Hora . "'<=HoraHasta) and Activo <>'N' Order by $orden";
        $qry_dispo_hora = $dbo->query($sql_dispo_hora);
        while ($row_dispo_hora = $dbo->fetchArray($qry_dispo_hora)) :
            if (!empty($row_dispo_hora["IDServicioElemento"])) :
                $array_elementos_hora = explode("|", $row_dispo_hora["IDServicioElemento"]);

                $CuposDisponibilidad = $dbo->getFields("Disponibilidad", "Cupos", "IDDisponibilidad = '" . $row_dispo_hora["IDDisponibilidad"] . "'");

                if ($IDTipoReserva == 2684) :
                    $CuposDisponibilidad = 2;
                endif;

                if (count($array_elementos_hora) > 0) :
                    foreach ($array_elementos_hora as $id_elemento_hora) :
                        if (!empty($id_elemento_hora)) :
                            $array_id_elemento[] = $id_elemento_hora;
                        endif;
                    endforeach;
                    $id_elemento_permitido = implode(",", $array_id_elemento);

                    /*
                        Especial para Lagartos en donde unos profesores se les asigna unas canchas que no pueden ser reservadas automaticamente
                        pero a ellos si se les asigna solo en el horario de la mañana los fines de semana
                         */
                    if ($IDClub == 7 && ($IDServicio == 43 || $IDServicio == 221)) {

                        //revisar si esta en corea o lago y tiene cancha asignada AND (HoraInicio <= '$Hora' AND HoraFinal >= '$Hora')
                        $sql_elemento_asociado = "Select * From ServicioElementoAsociado Where IDServicioElementoPrincipal = '" . $IDElementoPadre . "'  limit 1";
                        $result_elemento_asociado = $dbo->query($sql_elemento_asociado);
                        if ($dbo->rows($result_elemento_asociado) > 0) :
                            $row_elemento_asociado_cancha = $dbo->fetchArray($result_elemento_asociado);
                            $id_elemento_hora_cancha = $row_elemento_asociado_cancha["IDServicioElementoSecundario"];
                        endif;

                        $id_elemento_corea = "193,194,195,196,197,198,199"; //Canchas de corea
                        $array_elemento_corea = array("193", "194", "195", "196", "197", "198", "199");
                        //$id_elemento_corea="197,198,199"; //Canchas de corea
                        $id_elemento_lago = "200,201,202,203,204,205,206,207,208,209,210,7458,8571,8572,8573"; //Canchas de lago
                        $array_elemento_lago = array("200", "201", "202", "203", "204", "205", "206", "207", "208", "209", "210"); //Canchas de lago
                        if (($dia_fecha == 30 || $dia_fecha == 36)) {
                            if (($Hora == "06:00:00" || $Hora == "07:00:00" || $Hora == "08:00:00")) {
                                //if(( $Hora >= "06:00:00")){
                                switch ($datos_elemento_padre["FinSemanaCancha"]) {
                                    case "Corea":
                                        $id_elemento_permitido = $id_elemento_corea;
                                        if (in_array($id_elemento_hora_cancha, $array_elemento_corea)) {
                                            $validaciones = "S";
                                        } else {
                                            $validaciones = "N";
                                        }

                                        break;
                                    case "Lago":
                                        $id_elemento_permitido = $id_elemento_lago;
                                        if (in_array($id_elemento_hora_cancha, $array_elemento_lago)) {
                                            $validaciones = "S";
                                        } else {
                                            $validaciones = "N";
                                        }

                                        break;
                                    default:
                                        $id_elemento_permitido = $id_elemento_lago;
                                }
                            } else {
                                $id_elemento_permitido = $id_elemento_lago;
                                $validaciones = "N";
                            }
                        } else {
                            $validaciones = "N";
                            $id_elemento_corea = "193,194,195,196,197,198,199"; //Canchas de corea
                            //$id_elemento_corea="197,198,199"; //Canchas de corea
                            switch ($datos_elemento_padre["EntreSemanaCancha"]) {
                                case "Corea":
                                    $id_elemento_permitido = $id_elemento_corea;
                                    if (in_array($id_elemento_hora_cancha, $array_elemento_corea)) {
                                        $validaciones = "S";
                                    } else {
                                        $validaciones = "N";
                                    }

                                    break;
                                case "Lago":
                                    $id_elemento_permitido = $id_elemento_lago;
                                    if (in_array($id_elemento_hora_cancha, $array_elemento_lago)) {
                                        $validaciones = "S";
                                    } else {
                                        $validaciones = "N";
                                    }

                                    break;
                                default:
                                    $id_elemento_permitido = $id_elemento_lago;
                            }
                        }

                        if ($IDClub == 7 && (int) $EdadSocio > 0 && (int) $EdadSocio <= 6 && $IDServicio == 221) {
                            if ($IDElementoPadre == 3320 || $IDElementoPadre == 8687) {
                                $id_elemento_permitido = "7458,7459";
                                $validaciones = "N";
                            } else {
                                $id_elemento_permitido = "8572,8573";
                                $validaciones = "N";
                            }
                        }
                    }

                    unset($array_elementos_hora);
                    //ordeno el array por el orden definido a tomar en cuenta para reservar, ejemplo: primero reservar la cancha 18 y no la 1
                    $sql_elemento_servicio = "Select IDServicioElemento,OrdenReserva From ServicioElemento Where IDServicioElemento in (" . $id_elemento_permitido . ") Order by OrdenReserva ASC";
                    $result_elemento_servicio = $dbo->query($sql_elemento_servicio);
                    while ($row_elemento_servicio = $dbo->fetchArray($result_elemento_servicio)) :
                        $array_elementos_hora[] = $row_elemento_servicio["IDServicioElemento"];
                    endwhile;
                endif;

                ksort($array_elementos_hora);

                // ORDENO ALETORIAMENTE PARA TOMAR CUALQUIER ELEMENTO DE LOS DISPONIBLES DE PRIMERO
                if ($IDClub == 155) :
                    shuffle($array_elementos_hora);
                endif;

                $contador_elemento = 0;
                foreach ($array_elementos_hora as $id_elemento_hora) :
                    $contador_elemento++;
                    //verifo que el elemento pueda ser reservado automaticamente por otro servicio (por ejemplo cancha al tomar una clase)
                    $permite_reserva_automatica = $dbo->getFields("ServicioElemento", "PermiteReservaAutomatica", "IDServicioElemento = '" . $id_elemento_hora . "'");

                    if ($validaciones == "N") {
                        $permite_reserva_automatica = "S";
                        $validar_campo_aut = "S";
                    }

                    //Si se configura un elemento con otro fijo (por ejemplo un profesor de tenis con una cancha fija) se da prioridad para reservar esa cancha asi este configurada como reserva automatica en no
                    if ($contador_elemento == 1 && $validar_campo_aut == "S" && $validaciones != "N") :
                        $sql_elemento_asociado = "Select * From ServicioElementoAsociado Where IDServicioElementoPrincipal = '" . $IDElementoPadre . "' limit 1";
                        $result_elemento_asociado = $dbo->query($sql_elemento_asociado);
                        if ($dbo->rows($result_elemento_asociado) > 0) :
                            $row_elemento_asociado = $dbo->fetchArray($result_elemento_asociado);
                            $id_elemento_hora = $row_elemento_asociado["IDServicioElementoSecundario"];
                            $permite_reserva_automatica = "S";
                        endif;
                    endif;

                    //Validacion especial para Mesa de yeguas donde si se escoje cancha niño debe solo reservar las canchas de niños

                    if ($IDClub == 9 && $IDTipoReserva == 112) :
                        //Canchas de minitenis
                        if ($id_elemento_hora == "642" || $id_elemento_hora == "643" || $id_elemento_hora == "644" || $id_elemento_hora == "645") :
                            $permite_reserva_automatica = "S";
                        else :
                            $permite_reserva_automatica = "N";
                        endif;
                    endif;

                    if ($IDClub == 9 && $IDServicio == 12262) :
                        //Se valida una por todo el servicio
                        if (empty($accion_padre)) : // Es titular
                            $array_socio[] = $IDSocio;
                            $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                            $result_nucleo = $dbo->query($sql_nucleo);
                            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                                $array_socio[] = $row_nucleo["IDSocio"];
                            endwhile;
                        else :
                            $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                            $result_nucleo = $dbo->query($sql_nucleo);
                            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                                $array_socio[] = $row_nucleo["IDSocio"];
                            endwhile;
                        endif;

                        if (count($array_socio) > 0) :
                            $id_socio_nucleo = implode(",", $array_socio);
                        endif;

                        //Consulto las reservas
                        $sql_reservas_sem = $dbo->query("SELECT IDReservaGeneral From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                        $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                        if ((int) $total_reservas_semana >= 1) :
                            $respuesta["message"] = "Lo sentimos solo se permiten 1 reservas por accion en este servicio";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;

                    //FIN Validacion especial para Mesa de yeguas

                    if (!empty($id_elemento_hora) && $permite_reserva_automatica != "N") :
                        // verifico que no este reservado
                        $sql_reserva_hora = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE  IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $id_elemento_hora . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' ";
                        $r_reserva_hora = $dbo->query($sql_reserva_hora);
                        $total_reserva_hora = (int) $dbo->rows($r_reserva_hora);

                        //$id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $id_elemento_hora . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "'");

                        if ($CuposDisponibilidad == 0) {
                            $CuposDisponibilidad = 1;
                        }

                        if ($CuposDisponibilidad > $total_reserva_hora) :
                            //verifico que no tenga cierre a esa hora
                            $verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $id_elemento_hora, $Hora);
                            if (empty($verifica_abierto_servicio_hora)) :
                                $elemento_encontrado = 1;
                                return $id_elemento_hora;
                            endif;
                        endif;
                    endif;
                endforeach;
            endif;
        endwhile;
        return $IDElemento;
    }

    public function set_separa_reserva($IDClub, $IDSocio, $IDElemento, $IDServicio, $Tee, $Fecha, $Hora, $IDTipoReserva = '', $NumeroTurnos = "", $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();
        $flag_reserva_cancha_clase = 0;

        if (!empty($IDClubAsociado)) :
            $IDClubOrigen = $IDClub;
            $IDClub = $IDClubAsociado;
        endif;

        if (empty($NumeroTurnos)) {
            $NumeroTurnos = 1;
        }

        //if ( ($IDClub == 7 && ($IDServicio==22 || $IDServicio==19563) && $Hora>="06:00:00" && $Hora<="11:00:00" && (date("H:i:s")<'05:00:00' || date("Y-m-d")!=$Fecha)  )   ):
        if (($IDClub == 7 && ($IDServicio == 22 || $IDServicio == 19563) && $Hora >= "06:00:00" && $Hora <= "08:45:00" && (date("H:i:s") < '05:00:00' || date("Y-m-d") != $Fecha))) :
            $respuesta["message"] = "Lo sentimos los turnos entre las 6am y 8:15 am solo los puede reservar despues de las 5:00am del mismo dia ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        //Especial Guaymaral si es clase se reserva la de dentro de ocho dias automaticamente siempre y cuando este aciva la fecha
        if ($IDClub == 8 && $IDServicio == 41 && $IDTipoReserva == "517") :
            $RepetirFechaFinal = strtotime('+1 week', strtotime($Fecha));
            $minima_fecha = date("Y-m") . "-14";
            $maxima_fecha = new DateTime();
            $maxima_fecha->modify('last day of this month');
            $maxima_fecha->format('Y-m-d');
            if ((int) date("d") <= 14 && (int) date("d", $RepetirFechaFinal) <= 14) :
                $permite_repetir = "S";
            elseif ((int) date("d") >= 15 && $RepetirFechaFinal <= strtotime($maxima_fecha->format('Y-m-d'))) :
                $permite_repetir = "S";
            else :
                $permite_repetir = "N";
            endif;
            if ($permite_repetir == "S") :
                $mensaje_especial_repetir = " Se realizara una reserva automatica para el día " . date("Y-m-d", $RepetirFechaFinal);
                $Repetir = "S";
            else :
                $mensaje_especial_repetir = " No se puede realizar la reserva automatica en la siguiente semana ya que la fecha aun no esta disponible";
            endif;
        endif;
        //Fin validación especial

        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
        // Si el servicio es una clase y necesita reservar cancha
        $id_servicio_cancha = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
        if ($id_servicio_cancha > 0) :
            // Consulto el servicio del club asociado a este servicio maestro
            $IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");
            // Valido si existe una cancha disponible en el horario de la clase
            $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub, $IDServicioCanchaClub, $Fecha, $Hora, $IDElemento);
            if (empty($IDElemento_cancha)) :
                $respuesta["message"] = "Lo sentimos no existe una cancha disponible para tomar la clase en este horario.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :
                $flag_reserva_cancha_clase = 1;
            endif;
        endif;

        //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
        if (!empty($IDTipoReserva)) :
            $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");
            $cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];

            if (((int) $cantidad_turnos > 1)) :
                //$cantidad_turnos-=1; // Quito uno para que no cuente la reserva primera

                // Separo las reservas
                $array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos);

                if (count($array_hora_siguiente_turno_diponible) != (int) ($cantidad_turnos - 1) || !is_array($array_hora_siguiente_turno_diponible)) :
                    $respuesta["message"] = "Se necesitan: " . $cantidad_turnos . " turnos mas seguidos y el siguiente turno no esta disponible. Por favor seleccione otra opcion.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                else :
                    $flag_separa_siguiente_turno = 1;
                endif;
            endif;
        endif;

        //Si turnos es mayor a 1  verifico que los siguientes turnos esten disponibles y los separo
        if ((int) $NumeroTurnos > 1) :
            if ($id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30) : //Golf
                $array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, $Tee, "", "");
            else :
                $array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos);
            endif;

            if (count($array_disponible) != $NumeroTurnos) :
                $respuesta["message"] = "Se necesitan " . $NumeroTurnos . " turnos mas seguidos y el siguiente turno no esta disponible. Por favor seleccione otra opcion";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :
                // separo todos los turnos necesarios
                foreach ($array_disponible as $key_hora => $dato_hora) :
                    $sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr)
				                                                                                                    Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDServicio . "','" . $IDElemento . "', '3','" . $Fecha . "', '" . $dato_hora . "','" . $Tee . "', 'WebService',NOW())");
                    $id_reserva_general = $dbo->lastID();

                endforeach;
                $respuesta["message"] = 'Guardado';
                $respuesta["success"] = true;
                $respuesta["response"] = $id_reserva_general;
                return $respuesta;
            endif;
        endif;

        // Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible
        if (empty($IDElemento)) :
            $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
            $id_servicio_inicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
            if ($id_servicio_inicial == 5) : // 5 = get_reserva_aleatoria
                $IDElemento = SIMWebService::buscar_elemento_disponible($IDClub, $IDServicio, $Fecha, $Hora, $IDElemento);
            endif;
        endif;

        $Hora = SIMWebService::validar_formato_hora($Hora);

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDElemento) && !empty($IDServicio) && !empty($Fecha) && !empty($Hora)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '$IDSocio'");

            if (!empty($id_socio)) {

                // verifico que todavia este disponible la reserva
                if (!empty($Tee)) :
                    $condicion_tee = " and Tee = '" . $Tee . "'";
                endif;

                //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
                /*
                    $suma_rand = rand(0,2);
                    $rand_seg = rand(1,3)+$suma_rand;
                    sleep($rand_seg);
                     */

                $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee");

                if (($id_servicio_maestro == "15" || $id_servicio_maestro == "27" || $id_servicio_maestro == "28") && empty($Tee) && !empty($id_reserva_disponible)) :
                    $Tee = "Tee10";
                    $condicion_tee = " and Tee = '" . $Tee . "'";
                    $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee");
                    if (!empty($id_disponible_tee)) :
                        $Tee = "Tee1";
                        $condicion_tee = " and Tee = '" . $Tee . "'";
                        $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee");
                    endif;
                endif;

                // Obtener la disponibilidad utilizada al consultar la reserva
                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);
                $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array");
                // Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia
                $cupo_total = "S"; // ya no hay cupos
                $cupos_disponibilidad = $dbo->getFields("Disponibilidad", "Cupos", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                if ((int) $cupos_disponibilidad > 1) :
                    //Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
                    $cupos_reservados = SIMWebService::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $Hora);
                    //Valido si todavia existe cupo en esta hora
                    if ($cupos_reservados <= $datos_disponibilidad["Cupos"]) :
                        $cupo_total = "N"; // aun hay cupos disponibles
                    endif;
                    $numero_inscripcion = rand(0, 999999);
                else :
                    $numero_inscripcion = 0;
                endif;

                if (empty($id_reserva_disponible) || $cupo_total == "N") :
                    //Guardo la reserva
                    $sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDServicio, IDServicioTipoReserva, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, NumeroInscripcion, UsuarioTrCr, FechaTrCr)
		                                                                        Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDServicio . "', '" . $IDTipoReserva . "' , '" . $IDElemento . "', '3','" . $Fecha . "', '" . $Hora . "','" . $Tee . "', '" . $numero_inscripcion . "','WebService Separa',NOW())");

                    if (!$sql_inserta_reserva) :
                        $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    endif;

                    $id_reserva_general = $dbo->lastID();

                    //Valido que no haya quedado dos separaciones
                    if ($cupos_disponibilidad <= 1) :
                        /*
                            $suma_rand = rand(0,2);
                            $rand_seg = rand(0,3)+$suma_rand;
                            sleep($rand_seg);
                             */
                        $sql_duplicada = "Select * From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (3) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee";
                        $result_duplicada = $dbo->query($sql_duplicada);
                        if ($dbo->rows($result_duplicada) > 1) :
                            $respuesta["message"] = "Lo sentimos la reserva ya fue o esta siendo tomada!";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;

                    //Verifico que el elemnto no hay sido reservado a esta misma hora en otro servicio
                    $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                    //Si el elemento ya tiene otra reserva en otro servicio marco esta como ya revarda asi tenga cupos disponibles
                    $array_otro_elemento = explode(",", $condicion_multiple_elemento);
                    //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
                    //$suma_rand = rand(0,1);
                    //$rand_seg = rand(1,1)+$suma_rand;
                    //sleep($rand_seg);
                    if (count($array_otro_elemento) > 1) : //Si es mas de 1 quiere decir que el elemento esta en mas de un servicio y hago la verificacion
                        foreach ($array_otro_elemento as $id_elemento_multiple) :
                            if ($id_elemento_multiple != $IDElemento && !empty($id_elemento_multiple)) :
                                $sql_reserva_elemento_multp = "SELECT * FROM ReservaGeneral WHERE IDServicioElemento in (" . $id_elemento_multiple . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva = 3 ) and Hora = '" . $Hora . "' ";
                                $qry_reserva_elemento_mult = $dbo->query($sql_reserva_elemento_multp);
                                if ($dbo->rows($qry_reserva_elemento_mult) > 0 && $cupo_total == "S") :
                                    $respuesta["message"] = "Lo sentimos la reserva ya fue o esta siendo tomada!!";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;
                            endif;
                        endforeach;
                    endif;

                    // SI el servicio es una clase y se solicta reservar una cancha realizo la reserva temporal
                    if ($flag_reserva_cancha_clase == 1) :
                        // Obtener la disponibilidad utilizada al consultar la reserva
                        $id_disponibilidad_cancha = SIMWebService::obtener_disponibilidad_utilizada($IDServicioCanchaClub, $Fecha, $IDElemento_cancha);
                        $sql_inserta_reserva_cancha = $dbo->query("INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, Tipo, UsuarioTrCr, FechaTrCr)
			                                                    Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDServicioCanchaClub . "','" . $IDElemento_cancha . "', '3','" . $id_disponibilidad . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "',
			                                                                    '" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','Automatica','WebService',NOW())");
                        $id_reserva_cancha = $dbo->lastID();
                        //Agrego la relacion de servicio padre (Clase)y servicios hijos (canchas) reservados
                        $sql_reserva_automatica = $dbo->query("INSERT INTO ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva )
			                                                                                            Values ('" . $id_reserva_general . "','" . $id_reserva_cancha . "','" . $IDClub . ".','" . $IDSocio . "','" . $IDElemento_cancha . "','" . $Fecha . "','" . $Hora . "','3')");
                    endif;

                    // Si se va a reservas mas turnos seguidos y la validacion fue exitosa borro las separacion hechas
                    if ($flag_separa_siguiente_turno == 1 && count($array_hora_siguiente_turno_diponible) > 0) :
                        foreach ($array_hora_siguiente_turno_diponible as $Hora_siguiente) :
                            // Borro la reserva separada
                            $sql_inserta_reserva = $dbo->query("Select * From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and IDEstadoReserva  = 3");
                            while ($row_turno_siguiente = $dbo->fetchArray($sql_inserta_reserva)) :
                                $sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
					                                                                                                                                Values ('" . $id_reserva_general . "','" . $row_turno_siguiente["IDReservaGeneral"] . "','" . $IDClub . "','" . $IDSocio . "','" . $row_turno_siguiente["IDServicioElemento"] . "','" . $Fecha . "','" . $Hora_siguiente . "','3')");
                            endwhile;
                        endforeach;
                    endif;

                    //Especial Guaymaral si es clase se reserva la de dentro de ocho dias automaticamente siempre y cuando este aciva la fecha
                    if ($IDClub == 8 && $IDServicio == 41 && $IDTipoReserva == "517") :
                        $RepetirFechaFinal = strtotime('+1 week', strtotime($Fecha));
                        $minima_fecha = date("Y-m") . "-14";
                        $maxima_fecha = new DateTime();
                        $maxima_fecha->modify('last day of this month');
                        $maxima_fecha->format('Y-m-d');
                        if ((int) date("d") <= 14 && (int) date("d", $RepetirFechaFinal) <= 14) :
                            $permite_repetir = "S";
                        elseif ((int) date("d") >= 15 && $RepetirFechaFinal <= strtotime($maxima_fecha->format('Y-m-d'))) :
                            $permite_repetir = "S";
                        else :
                            $permite_repetir = "N";
                        endif;
                        if ($permite_repetir == "S") :
                            $mensaje_especial_repetir = " Se realizó un reserva automatica para el día " . date("Y-m-d", $RepetirFechaFinal);
                            $Repetir = "S";
                            $Periodo = "Semana";
                            $RepetirFechaFinal = date("Y-m-d", $RepetirFechaFinal);
                            $sql_inserta_reserva_prox = $dbo->query("Insert Into ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, NumeroInscripcion, Tipo, UsuarioTrCr, FechaTrCr)
				                                                                                                Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDServicio . "','" . $IDElemento . "', '3','" . $RepetirFechaFinal . "', '" . $Hora . "','" . $Tee . "', '" . $numero_inscripcion . "','Automatica','WebService',NOW())");
                            $id_reserva_aut = $dbo->lastID();
                            $sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva,Tipo )
				                                                                                                        Values ('" . $id_reserva_general . "','" . $id_reserva_aut . "','" . $IDClub . ".','" . $IDSocio . "','" . $IDElemento . "','" . $Fecha . "','" . $Hora . "','3','Repetir')");
                            if (!$sql_inserta_reserva_prox) :
                                //Agrego la relacion de servicio padre (Clase)y servicios hijos (canchas) reservados
                                $respuesta["message"] = "La reserva automatica de la proxima semana ya fue o esta siendo tomada";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                        else :
                            $mensaje_especial_repetir = " No se pudo realizar la reserva automatica en la siguiente semana ya que la fecha aun no esta disponible";
                        endif;
                    endif;
                    //Fin validación especial

                    $respuesta["message"] = 'Guardado';
                    $respuesta["success"] = true;
                    $respuesta["response"] = $id_reserva_general;

                else :
                    $respuesta["message"] = "Lo sentimos la reserva ya fue o esta siendo tomada " . $Tee . " " . $cupos_reservados;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                endif;
            } else {
                $respuesta["message"] = 'Error el sociono existe o no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "18." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_libera_reserva($IDClub, $IDSocio, $IDElemento, $IDServicio, $Tee, $Fecha, $Hora, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio)  && !empty($IDServicio) && !empty($Fecha) && !empty($Hora)) {

            if (!empty($IDElemento))
                $cond_elemento = " and IDServicioElemento = '" . $IDElemento . "' ";
            else
                $cond_elemento = "  ";

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                if (!empty($IDClubAsociado)) :
                    $CondicionClub = " OR IDClub = $IDClubAsociado";
                endif;

                // Consulto la reserva
                $sql_reserva_general = $dbo->query("Select * From ReservaGeneral Where (IDClub = '$IDClub' $CondicionClub) and IDSocio = '" . $IDSocio . "' and IDServicio = '" . $IDServicio . "' " . $cond_elemento . " and IDEstadoReserva = 3 and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "'");
                $row_reserva_general = $dbo->fetchArray($sql_reserva_general);
                //Elimino la reserva
                //$sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva = 3 and Fecha = '".$Fecha."' and Hora = '".$Hora."'");
                $sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDReservaGeneral = '" . $row_reserva_general["IDReservaGeneral"] . "' ");
                //Elimino las relacionadas
                $sql_reserva_auto = $dbo->query("Select * From ReservaGeneralAutomatica Where  IDReservaGeneral = '" . $row_reserva_general["IDReservaGeneral"] . "'");
                while ($row_reserva_auto = $dbo->fetchArray($sql_reserva_auto)) :
                    $sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAutomatica = '" . $row_reserva_auto["IDReservaGeneralAutomatica"] . "'");
                    $sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDReservaGeneral = '" . $row_reserva_auto["IDReservaGeneralAsociada"] . "'");
                endwhile;

                $respuesta["message"] = 'Guardado';
                $respuesta["success"] = true;
                $respuesta["response"] = "reserva eliminada";
            } else {
                $respuesta["message"] = 'Error el socio no existe o no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "19." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function validar_turnos_seguidos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario = "", $TipoBeneficiario = "", $PermiteReservaSeguidaNucleo)
    {
        $dbo = &SIMDB::get();
        $flag_turno_seguido = 0;
        $array_confirmado = array();
        // Consulto los turnos reservados y confirmados del socio para no tomar los separados
        if (!empty($IDBeneficiario)) :
            $condicion_beneficiario = " and  (IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDInvitadoBeneficiario = '" . $IDBeneficiario . "' or IDInvitadoBeneficiario = '0')";
        else :
            $condicion_beneficiario = " and  IDSocioBeneficiario = '0' and IDInvitadoBeneficiario = '0'";
        endif;

        // Valido tambien para que los de la misma acción no puedan tomar turnos seguidos
        //Si en la configuracion esta marcada como "No" de lo contrario se permite turnos seguios asi sean de la misma accion
        if ($PermiteReservaSeguidaNucleo == "N") :
            $accion_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
            $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            if (empty($accion_padre)) { // Es titular
                if (!empty($accion_socio)) {
                    $array_socio[] = $IDSocio;
                    $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                    $result_nucleo = $dbo->query($sql_nucleo);
                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                        $array_socio[] = $row_nucleo["IDSocio"];
                    endwhile;
                }
            } else {
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            }
            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;
        else :
            $id_socio_nucleo = $IDSocio;
        endif;

        //$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva    = 1 " . $condicion_beneficiario;
        $sql_confirmado = "Select * From  ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ")  and IDServicio  = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
        $qry_confirmado = $dbo->query($sql_confirmado);
        while ($r_confirmado = $dbo->fetchArray($qry_confirmado)) :
            $array_confirmado[] = $r_confirmado["Hora"];
        endwhile;

        $array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, "", "");
        foreach ($array_horarios["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario) :
            if (in_array($IDSocio, $array_socio) && in_array($datos_horario["Hora"], $array_confirmado)) :
                $id_socio_turno = $IDSocio;
            elseif (empty($array_turnos_dia[$datos_horario["Hora"]])) :
                $id_socio_turno = "";
            endif;
            if (empty($array_turnos_dia[$datos_horario["Hora"]])) :
                $array_turnos_dia[$datos_horario["Hora"]] = $id_socio_turno;
            endif;
        endforeach;

        for ($i = 1; $i <= count($array_turnos_dia); $i++) :
            current($array_turnos_dia);
            //Primer Posicion
            if ($i == 1) {
                if ($i == 1 && key($array_turnos_dia) == $Hora && current($array_turnos_dia) == $IDSocio) : // Es el primer horario y lo valido
                    $flag_turno_seguido = 1;
                    // PARA EL CAMPESTRE DE MEDELLIN PUEDEN RESERVAR A LA MISMA HORA
                    if ($IDClub == 20) :
                        $flag_turno_seguido = 0;
                    endif;
                endif;
                $primera_hora = key($array_turnos_dia);

                next($array_turnos_dia);
                if (current($array_turnos_dia) == $IDSocio && $Hora == $primera_hora) :
                    //print_r($primera_hora);
                    $flag_turno_seguido = 5;
                    prev($array_turnos_dia);
                endif;
            }
            if (key($array_turnos_dia) == $Hora) :
                // me devuelvo al turno anterior
                prev($array_turnos_dia);
                if (current($array_turnos_dia) == $IDSocio) :
                    $flag_turno_seguido = 2;
                    if ($IDClub == 20 && (((key($array_turnos_dia) >= '08:30:00' && key($array_turnos_dia) <= '15:15:00') || (key($array_turnos_dia) >= '19:00:00' && key($array_turnos_dia) <= '20:30:00')) && (($Hora >= '05:30:00' && $Hora <= '07:45:00') || ($Hora >= '16:00:00' && $Hora <= '18:15:00')))) :
                        $flag_turno_seguido = 0;
                    endif;
                endif;
                //Adelanto dos turnos, si es el final solo uno                  
                next($array_turnos_dia);

                if (current($array_turnos_dia) == $IDSocio) :
                    $flag_turno_seguido = 3;
                    if ($IDClub == 20) :
                        $flag_turno_seguido = 0;
                    endif;
                endif;

                if ($i != count($array_turnos_dia)) :
                    next($array_turnos_dia);
                endif;

                if (current($array_turnos_dia) == $IDSocio) :
                    $flag_turno_seguido = 4;
                    if ($IDClub == 20 && ((($Hora >= '08:30:00' && $Hora <= '15:15:00') || ($Hora >= '19:00:00' && $Hora <= '20:30:00')) && ((key($array_turnos_dia) >= '05:30:00' && key($array_turnos_dia) <= '07:45:00') || (key($array_turnos_dia) >= '16:00:00' && key($array_turnos_dia) <= '18:15:00')))) :
                        $flag_turno_seguido = 0;
                    endif;
                endif;
            endif;
            next($array_turnos_dia);
        endfor;

        return $flag_turno_seguido;
    }

    public function validar_regla_turnos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva)
    {
        $dbo = &SIMDB::get();
        $regla_no_cumplida = 0;
        $turno_automatico = 0;
        $turno_tomado = 0;
        $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");

        // Valido tambien para que los de la misma acción no puedan tomar turnos seguidos
        $accion_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
        $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        if (empty($accion_padre)) : // Es titular
            $array_socio[] = $IDSocio;
            $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
            $result_nucleo = $dbo->query($sql_nucleo);
            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                $array_socio[] = $row_nucleo["IDSocio"];
            endwhile;
        else :
            $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
            $result_nucleo = $dbo->query($sql_nucleo);
            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                $array_socio[] = $row_nucleo["IDSocio"];
            endwhile;
        endif;
        if (count($array_socio) > 0) :
            $id_socio_nucleo = implode(",", $array_socio);
        endif;

        // HABILITAR CUANDO CONFIRMEN
        /* if($IDServicio == 89 || $IDServicio == 5583):
                $IDServicio = "89,5583";
            endif; */

        // Consulto los turnos reservados automaticos y confirmados del socio para no tomar los separados
        //$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva    = 1";
        $sql_confirmado = "Select * From  ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDServicio IN ($IDServicio) and Fecha = '" . $Fecha . "' and IDEstadoReserva	= 1";
        $qry_confirmado = $dbo->query($sql_confirmado);
        $turno_tomado = $dbo->rows($qry_confirmado);
        while ($row_confirmado = $dbo->fetchArray($qry_confirmado)) :
            if ($row_confirmado["Tipo"] == "Automatica") :
                $turno_automatico++;
            endif;
        endwhile;

        if ($turno_automatico > 0) :
            $regla_no_cumplida = 1; // 1 = Ya tomo un turno con la opcion de 2, 3, etc  tuernos seguidos
        endif;

        //Valido si ya tiene una reserva en el dia no pueda reservar ninguna de turnos seguidos
        if ((int) $turno_tomado > 0 && (int) $datos_tipo_reserva["NumeroTurnos"] > 1 && $regla_no_cumplida == 0) :
            $regla_no_cumplida = 2; // 2 = Ya tomo un turno ya no puede tomar uno en bloque
        endif;

        return $regla_no_cumplida;
    }

    public function validar_regla_turnos_tenis($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva)
    {
        $dbo = &SIMDB::get();
        $regla_no_cumplida = 0;
        $turno_dia = 0;
        $turno_tomado = 0;

        // Valido tambien para que los de la misma acción no puedan tomar mas de 2 turnos al dia por ejemplo
        $accion_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
        $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        if (empty($accion_padre)) : // Es titular
            $array_socio[] = $IDSocio;
            $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
            $result_nucleo = $dbo->query($sql_nucleo);
            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                $array_socio[] = $row_nucleo["IDSocio"];
            endwhile;
        else :
            $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
            $result_nucleo = $dbo->query($sql_nucleo);
            while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                $array_socio[] = $row_nucleo["IDSocio"];
            endwhile;
        endif;
        if (count($array_socio) > 0) :
            $id_socio_nucleo = implode(",", $array_socio);
        endif;

        // Consulto los turnos reservados confirmados del socio y su nucleo
        //$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva    = 1";
        $sql_confirmado = "Select * From  ReservaGeneral Where ( IDSocio in (" . $id_socio_nucleo . ") or IDSocioBeneficiario in (" . $id_socio_nucleo . ") )  and IDServicio  = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and IDEstadoReserva	= 1";
        $qry_confirmado = $dbo->query($sql_confirmado);
        $turno_tomado = $dbo->rows($qry_confirmado);
        while ($row_confirmado = $dbo->fetchArray($qry_confirmado)) :
            $turno_dia++;
        endwhile;

        if ($turno_dia >= 2) :
            $regla_no_cumplida = 1; // 1 = Ya tomo un turno con la opcion de 2, 3, etc  tuernos seguidos
        endif;

        return $regla_no_cumplida;
    }

    public function valida_siguiente_turno_disponible($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos)
    {
        $dbo = &SIMDB::get();
        $hora_turno_siguiente = "";
        $flag_turno_disponible = 0;
        $contador_turnos = 1;
        $array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, $IDElemento, "");

        foreach ($array_horarios["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario) :
            if ($flag_turno_siguiente == 1) :
                $respuesta = SIMWebService::set_separa_reserva($IDClub, $IDSocio, $IDElemento, $IDServicio, "", $Fecha, $datos_horario["Hora"], "", $cantidad_turnos);
                if ($respuesta == true) :
                    $hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
                    $contador_turnos++;
                    if ($contador_turnos <= $cantidad_turnos) :
                        $Hora = $datos_horario["Hora"];
                    endif;
                else :
                    unset($hora_turno_siguiente); // No se pudo separar
                endif;
            endif;

            if ($datos_horario["Hora"] == $Hora) :
                $flag_turno_siguiente = 1;
            else :
                $flag_turno_siguiente = 0;
            endif;
        endforeach;

        //Valido que se hayan podido separado los mismos turnos que se solicitaron
        if (count($hora_turno_siguiente) != $cantidad_turnos) :
            unset($hora_turno_siguiente);
        //echo "no se pudieron tomar todos";
        endif;

        return $hora_turno_siguiente;
    }

    public function valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos)
    {
        global $array_horas_elemento, $minutoAnadir, $PermiteReservaNoSeguida, $condicion_multiple_elemento, $array_hora_cerrada, $datos_reserva, $array_elemento_validar, $cupos, $array_reserva_hora, $CupoMaximoBloque, $array_elemento_servicio;
        $dbo = &SIMDB::get();
        $hora_turno_siguiente = array();
        $flag_turno_disponible = 0;
        $contador_turnos = 1;
        // Quito 1 turno por que necesito validar los siguientes
        $cantidad_turnos--;
        $dia_fecha = date('w', strtotime($Fecha));
        //if ( count( $array_horarios[ $IDElemento ] ) <= 0 ):
        //$array_horarios[ $IDElemento ] = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $IDServicio, $Fecha, $IDElemento, "" );

        if (count($array_horas_elemento) <= 0) {
            $sql_serv = "SELECT PermiteReservaTurnosNoSeguidos,CupoMaximoBloque FROM Servicio WHERE IDServicio = '" . $IDServicio . "' ";
            $r_serv = $dbo->query($sql_serv);
            $datos_serv = $dbo->fetchArray($r_serv);
            $PermiteReservaNoSeguida = $datos_serv["PermiteReservaTurnosNoSeguidos"];
            $CupoMaximoBloque = $datos_serv["CupoMaximoBloque"];

            //Consulto los elemntos del servicio

            $sql_ele = "SELECT IDServicioElemento,IdentificadorElemento FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
            $r_ele = $dbo->query($sql_ele);
            while ($row_ele = $dbo->fetchArray($r_ele)) {
                $array_ele[] = $row_ele["IDServicioElemento"];
                $array_elemento_servicio[] = $row_ele["IDServicioElemento"];
                $array_elemento_validar[$row_ele["IDServicioElemento"]][] = $row_ele["IDServicioElemento"];
                if ((int) $row_ele["IdentificadorElemento"] > 100000) {
                    $sql_multiple_elemento = "SELECT IDServicioElemento FROM ServicioElemento WHERE IdentificadorElemento = '" . $row_ele["IdentificadorElemento"] . "' and IDServicioElemento <> '" . $row_ele["IDServicioElemento"] . "'";
                    $r_multiple_elemento = $dbo->query($sql_multiple_elemento);
                    while ($row_multiple_elemento = $dbo->fetchArray($r_multiple_elemento)) {
                        $array_ele[] = $row_multiple_elemento["IDServicioElemento"];
                        $array_elemento_validar[$row_ele["IDServicioElemento"]][] = $row_multiple_elemento["IDServicioElemento"];
                    }
                }
            }

            if (count($array_ele) > 0) :
                $id_elementos = implode(",", $array_ele);
            endif;

            ///Consulto de una vez todos lo reservado en el servicio
            //$sql_reserva_gral="SELECT IDReservaGeneral,IDServicioElemento,Hora FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and IDSocio <> '".$IDSocio."' and IDServicio = '".$IDServicio."'";
            //$sql_reserva_gral = "SELECT IDReservaGeneral,IDServicioElemento,Hora FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and IDSocio <> '" . $IDSocio . "' and IDServicioElemento in (" . $id_elementos . ") ";
            $sql_reserva_gral = "SELECT IDReservaGeneral,IDServicioElemento,Hora FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and IDServicioElemento in (" . $id_elementos . ") ";
            $r_reser = $dbo->query($sql_reserva_gral);
            while ($row_reser = $dbo->fetchArray($r_reser)) {
                $datos_reserva[$row_reser["IDServicioElemento"]][$row_reser["Hora"]] = $row_reser["IDReservaGeneral"];
                $datos_reserva_hora[$row_reser["IDServicioElemento"]][$row_reser["Hora"]]++;
                $array_reserva_hora[$row_reser["Hora"]]++;
            }

            // Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
            $sql_dispo_elemento_primera = "Select IDDisponibilidad,HoraDesde From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' Order by HoraDesde Limit 1";
            $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
            $row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
            $HoraDesde = $row_dispo_elemento_primera["HoraDesde"];

            $sql_dispo = "SELECT Intervalo,Cupos From Disponibilidad Where IDDisponibilidad = '" . $row_dispo_elemento_primera["IDDisponibilidad"] . "'";
            $qry_dispo = $dbo->query($sql_dispo);
            $row_dispo = $dbo->fetchArray($qry_dispo);
            $minutoAnadir = $row_dispo["Intervalo"];
            $cupos = $row_dispo["Cupos"];

            //endif;
        }

        //$sql_disponibilidad_dia = "SELECT HoraDesde,HoraHasta From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' Order by HoraDesde Asc";
        $sql_disponibilidad_dia = "SELECT HoraDesde,HoraHasta From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' Order by HoraDesde Asc";
        $qry_disponibilidad_dia = $dbo->query($sql_disponibilidad_dia);
        $array_horas_elemento = array();
        while ($row_disponibilidad_dia = $dbo->fetchArray($qry_disponibilidad_dia)) {
            $HoraDesde = $row_disponibilidad_dia["HoraDesde"];
            $HoraHasta = $row_disponibilidad_dia["HoraHasta"];
            while ($HoraDesde <= $HoraHasta) {
                //$array_horas_elemento[]=$HoraDesde.":00";
                $array_horas_elemento[$HoraDesde] = $HoraDesde;
                $NuevaHora = strtotime('+' . $minutoAnadir . ' minutes', strtotime($HoraDesde));
                $HoraDesde = date("H:i:s", $NuevaHora);
            }
        }

        //$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");

        //foreach ( $array_horarios[ $IDElemento ][ "response" ][ 0 ][ "Disponibilidad" ][ 0 ] as $id_horario => $datos_horario ):
        $contador_horas = 0;
        $contador_horas_disponibilidad = 1;
        ksort($array_horas_elemento);

        foreach ($array_horas_elemento as $id_horario => $datos_horario["Hora"]) :

            //Verifico el intervalo para saber si el siguiente turno es seguido y no tomar el de una hora despues de un intervalo ej hora de almuerzo
            if ($contador_horas == 0) {
                //Guardo array con las horas siguientes esperadas
                $hora_real = $Fecha . " " . $Hora;
                for ($i = 1; $i <= $cantidad_turnos; $i++) {
                    $hora_real = strtotime('+' . $minutoAnadir . ' minute', strtotime($hora_real));
                    $hora_real = date('H:i:s', $hora_real);
                    $array_hora_esperada[$i] = $hora_real;
                }
            }

            if ($flag_turno_siguiente == 1) :
                //Hago esta validacion menos para simulador de areoclub que si permite horas no continuas
                // Con este dato $PermiteReservaNoSeguida valido tambien si el servicio se configuro con horas no seguidas pero debe permitir separar los turnos dobles sin importr que haya un intervalo
                if ($datos_horario["Hora"] == $array_hora_esperada[$contador_horas_disponibilidad] || $IDServicio == 3609 || $PermiteReservaNoSeguida == "S") {
                    // verifico si esta disponible la reserva
                    //$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$datos_horario["Hora"]."'" );
                    if ($IDClub == 36) : // para aero club no verifico hora ya que el avion puede ser reservado todo el dia
                        $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "'  " . $condicion_elemento);
                    else :
                        /*
                            $sql_reserva_gral="SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and Hora = '" . $datos_horario[ "Hora" ] . "' and IDSocio <> '".$IDSocio."' " . $condicion_elemento . "Limit 1";
                            $r_reser=$dbo->query($sql_reserva_gral);
                            $row_reser=$dbo->fetchArray($r_reser);
                            $id_reserva_disponible=$row_reser["IDReservaGeneral"];
                             */
                        //$id_reserva_disponible = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDEstadoReserva in (1,3) and Fecha = '" . $Fecha . "' and Hora = '" . $datos_horario[ "Hora" ] . "' and IDSocio <> '".$IDSocio."' " . $condicion_elemento );
                        $con_reserva_hora = "N";
                        if (count($array_elemento_validar[$IDElemento]) > 0) {
                            foreach ($array_elemento_validar[$IDElemento] as $key_elem => $value_elem) {
                                $id_reserva_disponible = $datos_reserva[$value_elem][$datos_horario["Hora"]];
                                if ((int) $cupos > 1 && (count($datos_reserva_hora[$value_elem][$datos_horario["Hora"]]) <= $cupos)) {
                                    $id_reserva_disponible = "";
                                }
                                if (!empty($id_reserva_disponible)) {
                                    $con_reserva_hora = "S";
                                }
                            }
                        }
                    endif;

                    //Verifico que no haya fecha/hora de cierre en el turno siguiente
                    //$hora_cerrada[$datos_horario[ "Hora" ]] = SIMWebService::verifica_club_servicio_abierto( $IDClub, $Fecha, $IDServicio, $IDElemento, $datos_horario[ "Hora" ] );
                    if (count($array_hora_cerrada[$datos_horario["Hora"]][$IDElemento]) <= 0) {
                        $validacierre = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento, $datos_horario["Hora"]);
                        if (!empty($validacierre)) {
                            $array_hora_cerrada[$datos_horario["Hora"]][$IDElemento] = "S";
                        } else {
                            $array_hora_cerrada[$datos_horario["Hora"]][$IDElemento] = "N";
                        }
                    }

                    if ($con_reserva_hora == "N" && $array_hora_cerrada[$datos_horario["Hora"]][$IDElemento] == "N") :
                        //$respuesta = SIMWebService::set_separa_reserva( $IDClub, $IDSocio, $IDElemento, $IDServicio, "", $Fecha, $datos_horario[ "Hora" ], "", "" );
                        $respuesta = true;
                    else :
                        $respuesta = false;
                    endif;

                    if ((int) $CupoMaximoBloque > 0 && $respuesta == true) {
                        $contador_reserva_bloque = 0;
                        foreach ($array_elemento_servicio as $id_elemento_serv) {
                            $datos_reserva_hora[$row_reser["IDServicioElemento"]][$row_reser["Hora"]]++;
                            if ($datos_reserva_hora[$id_elemento_serv][$datos_horario["Hora"]]) {
                                $contador_reserva_bloque++;
                            }
                        }
                        if ($contador_reserva_bloque < (int) $CupoMaximoBloque) {
                            $respuesta = true;
                        } else {
                            $respuesta = false;
                        }
                    }

                    if ($respuesta == true) :
                        $hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
                        $contador_turnos++;
                        if ($contador_turnos <= $cantidad_turnos) :
                            $Hora = $datos_horario["Hora"];
                        endif;
                    else :
                        unset($hora_turno_siguiente); // No se pudo separar
                    endif;
                }
                $contador_horas_disponibilidad++;
            endif;

            //echo "<br>" . $datos_horario[ "Hora" ] ."==". $Hora;
            if ($datos_horario["Hora"] == $Hora) :
                $flag_turno_siguiente = 1;
            else :
                $flag_turno_siguiente = 0;
            endif;

            $contador_horas++;
        endforeach;

        //Valido que se hayan podido separado los mismos turnos que se solicitaron
        if (count($hora_turno_siguiente) != $cantidad_turnos) :
            unset($hora_turno_siguiente);
        //echo "no se pudieron tomar todos";
        endif;

        //echo "TOTAL " .count($hora_turno_siguiente);

        return $hora_turno_siguiente;
    }

    public function valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos, $Tee, $TipoReserva = "", $array_tee)
    {

        global $array_horarios_servicio;
        $dbo = &SIMDB::get();
        $hora_verify = $Hora;
        $hora_turno_siguiente = array();
        $flag_turno_disponible = 0;
        $contador_turnos = 1;
        if (count($array_horarios_servicio) <= 0) :
            //$array_horarios_servicio = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $IDServicio, $Fecha, $IDElemento, "","","","","","","" );
            $array_horarios_servicio = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, $IDElemento, "", "", "", "", "", "", "");
        endif;
        //print_r($array_horarios);

        foreach ($array_horarios_servicio["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario) :
            // valido solo las horas mayores a la que solicita para mejorar rendimiento
            $hora_disponible = strtotime($datos_horario["Hora"]);
            $hora_consultada = strtotime($Hora);

            if ($datos_horario["Tee"] == $Tee && $hora_disponible >= $hora_consultada) : // Solo verifico el tee que recibe
                if ($contador_turnos <= $cantidad_turnos) :

                    //Si el tipo de reserva viene vacio es que se está separando o verificando, si es reserva no tengo en cuenta las separadas
                    if ($TipoReserva == "Reserva") :
                        $id_tipo_reserva = "1";
                    else :
                        $id_tipo_reserva = "1,3";
                    endif;
                    // verifico si esta disponible la reserva
                    $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (" . $id_tipo_reserva . ") and Fecha = '" . $Fecha . "' and Hora = '" . $datos_horario["Hora"] . "' and Tee = '" . $Tee . "'");
                    $hora_real = date('Y-m-d H:i:s');

                    //Verifico que no haya fecha/hora de cierre en el turno siguiente
                    $hora_cerrada = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $IDServicio, $IDElemento, $datos_horario["Hora"], $Tee);

                    $hora_par_disponible = SIMWebService::valida_hora_con_par($array_tee, $datos_horario["Hora"], $Tee, $IDElemento, $Fecha, $hora_real, $IDClub);

                    if (empty($id_reserva_disponible) && $hora_par_disponible == "S" && $hora_cerrada == "") :
                        $hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
                        $Hora = $datos_horario["Hora"];
                    else :
                        unset($hora_turno_siguiente); // No se pudo separar
                    endif;
                    $contador_turnos++;
                endif;
            endif;
        endforeach;

        //Valido que se hayan podido separado los mismos turnos que se solicitaron
        if (count($hora_turno_siguiente) != $cantidad_turnos) :
            unset($hora_turno_siguiente);
        //echo "no se pudieron tomar todos";
        endif;

        return $hora_turno_siguiente;
    }

    public function validar_formato_hora($Hora)
    {
        $hora_militar = "";
        if (strlen($Hora) > 8) :
            $cadena = strtotime($Hora);
            $cadena = date("H:i:s", $cadena);
            $hora_militar = $cadena;
        else :
            $hora_militar = $Hora;
        endif;

        return $hora_militar;
    }

    public function obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora = "", $Tee = "")
    {
        $dbo = &SIMDB::get();
        //verifico la disponibilidad que se utilizo
        if (!empty($Hora)) :
            $condicion_hora = " and ('" . $Hora . "' >= HoraDesde and '" . $Hora . "'<=HoraHasta) ";
        endif;

        if ($Tee == "Tee1") {
            $condicion_tee = " and Tee1='S' ";
        }

        if ($Tee == "Tee10") {
            $condicion_tee = " and Tee10='S' ";
        }

        $dia_fecha = date('w', strtotime($Fecha));
        $sql_disponibilidad = $dbo->query("Select IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $IDElemento . "|%' and Activo <>'N' " . $condicion_hora . $condicion_tee . "  Limit 1");
        $row_disponibilidad = $dbo->fetchArray($sql_disponibilidad);
        $id_disponibilidad = $row_disponibilidad["IDDisponibilidad"];
        return $id_disponibilidad;
    }

    public function busca_cabeza_grupo($Invitados, $NumeroTurnos, $IDSocio)
    {
        //Resto un turno ya que el primero debe ser el socio que realiza la reserva
        $NumeroTurnos -= 1;
        $array_cabeza_grupo = array();
        $total_cabeza = 1;
        $datos_invitado_turno = json_decode($Invitados, true);
        foreach ($datos_invitado_turno as $detalle_datos_turno) :
            $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
            $NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
            if (!empty($IDSocioInvitadoTurno) && $total_cabeza <= $NumeroTurnos) :
                $array_cabeza_grupo[] = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
                $total_cabeza++;
            endif;
        endforeach;

        //Verifico que los invitados sean socio para ponerlos como cabeza ya que si son externos el cabeza debe ser el socio que realiza la reserva
        if (count($array_cabeza_grupo) < $NumeroTurnos) :
            for ($i_cabeza = 0; $i_cabeza <= $NumeroTurnos; $i_cabeza++) :
                if (empty($array_cabeza_grupo[$i_cabeza])) :
                    $array_cabeza_grupo[$i_cabeza] = $IDSocio;
                endif;
            endfor;
        endif;

        return $array_cabeza_grupo;
    }

    public function set_reserva_generalV2($IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, $Observaciones = "", $Admin = "", $Tee = "", $IDDisponibilidad = "", $Repetir = "", $Periodo = "", $RepetirFechaFinal = "", $IDTipoModalidadEsqui = "", $IDAuxiliar = "", $IDTipoReserva = "", $NumeroTurnos = "", $IDReservaGrupos, $IDBeneficiario = "", $TipoBeneficiario = "", $IDUsuarioReserva = "", $CantidadInvitadoSalon = "", $ListaAuxiliar = "", $Altitud = "", $Longitud = "", $AdicionalesSocio = "", $IDCaddieSocio = "", $IDClubAsociado = "", $HoraFinal = "")
    {


        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMServicioReserva.inc.php";
        require_once LIBDIR . "SIMWebServiceUsuarios.inc.php";
        require_once LIBDIR . "SIMWebServiceReservas.inc.php";

        $IDClubOrigen = $IDClub;
        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;




        $Invitados = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Invitados));


        //para nuba, si se hace la reserva desde el administrador se busca al padre del socio para asignarle a el la reserva y colocar al socio como beneficiario 
        if ($IDClub == 196 && $Admin == 'Admin') :
            $hijo = $dbo->getFields("Socio", array("AccionPadre", "TipoSocio"), "IDClub = 196 AND IDSocio = $IDSocio");

            if ($hijo['AccionPadre'] != "" && $hijo['TipoSocio'] == 'HIJO') :

                $IDBeneficiario = $IDSocio;
                $IDSocio = $dbo->getFields("Socio", "IDSocio", "IDClub = 196 AND NumeroDocumento = '" . $hijo['AccionPadre'] . "' AND Accion = '" . $hijo['AccionPadre'] . "'");
                $TipoBeneficiario = "Socio";

                if (!$IDSocio) :
                    $respuesta["message"] = "Lo sentimos, el socio no tiene un padre para asociar la reserva";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            else :
                $respuesta["message"] = "Lo sentimos, unicamente se permiten reservas a hijos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

        endif;

        $FechaHoraSistemaActual = date("Y-m-d H:i:s");

        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio= '" . $IDServicio . "' ", "array");
        $datos_servicio_mestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");

        $permiteConLista = $datos_servicio["permisoReserva"];
        $permitereservar = $datos_servicio["PermiteReservar"];
        $permiteConTipo = $datos_servicio["PermisoReservaTipo"];
        $permiteHorario = $datos_servicio["horarioPermiso"];
        $documentoSocio = $datos_socio["NumeroDocumento"];
        $CuposporHora = $datos_servicio["CupoMaximoBloque"];




        $horaInicio = $datos_servicio["horaInicioPermiso"];
        $horaFin = $datos_servicio["horaFinPermiso"];
        $fechaInicio = $datos_servicio["fechaInicioPermiso"];
        $fechaFin = $datos_servicio["fechaFinPermiso"];

        if ($IDClub == 125 && $IDServicio == 23013) {
            // Validar que un Socio menor de 21 años no pueda invitar a piscina externa Club Golf de Uruguay
            $Edad = SIMUtil::Calcular_Edad($datos_socio['FechaNacimiento']);
            if ($datos_socio['FechaNacimiento'] == '0000-00-00' || $Edad < 21) {
                $datos_invitado_edad = json_decode($Invitados, true);
                if (count($datos_invitado_edad) > 0) {
                    $respuesta["message"] = "Lo sentimos, los Socios menores a 21 años no pueden invitar a este servicio: ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
            // Fin Validar que un Socio menor de 21 años no pueda invitar Club Golf de Uruguay
        }

        if ($IDClub == 227) :
            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
            if ($IDServicio == 46189) {
                $respuesta = SIMWebServiceCountryMedellin::App_GenerarReservaZonaInteractiva($Fecha, $Hora, $HoraFinal, $IDElemento, $IDServicio, $IDTipoReserva, $datos_socio["TokenCountryMedellin"]);
                return $respuesta;
            } else {
                $IDTurno = SIMWebServiceCountryMedellin::BuscarTurno($IDServicio, $IDElemento, $Fecha, $Hora, $datos_socio["TokenCountryMedellin"], $IDTipoReserva, $IDClub);
                // GUARDAR LA RESERVA EN EL SERVICIO DEL COUNTRY MEDELLIN                    
                if ($IDTurno != false) :
                    //$IDUbicacion = $dbo->getFields("ServicioElemento", "IDSistemaExterno", "IDServicioElemento = $IDElemento");
                    $IDUbicacion = $IDElemento;


                    $reservas = SIMWebServiceCountryMedellin::App_GenerarReserva($Fecha, $IDTurno, $IDUbicacion, $IDServicio, $IDTipoReserva, $datos_socio["TokenCountryMedellin"]);

                    if ($reservas[idReserva] >= 0) :
                        if ($reservas[estado] == "false")
                            $respuesta["success"] = false;
                        else
                            $respuesta["success"] = true;

                        $respuesta["message"] = $reservas["mensaje"];
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :
                        $respuesta["message"] = "RM1. Problemas al guardar su reserva";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    endif;

                else :
                    $respuesta["message"] = "Turno en CM no disponible";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }
        endif;



        if ($permiteConLista == 'S') {
            $datos["total"] = 0;
            $total_invitados = 0;
            $datos_invitado_turno = json_decode($Invitados, true);
            foreach ($datos_invitado_turno as $detalle_datos_turno) :
                $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
                if (!empty($IDSocioInvitadoTurno)) :
                    $ids .= $IDSocioInvitadoTurno . ",";
                endif;
            endforeach;
            if (!empty($ids)) :
                $ids_socios = substr($ids, 0, -1);

                $total_invitados_socios = explode(",", $ids_socios);
                $total_invitados = count($total_invitados_socios);
            endif;

            if ($permitereservar == 'S') :

                if ($permiteConTipo == 'S') {
                    $socioConPermiso = "SELECT * FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND NumeroDocumento = '" . $documentoSocio . "' AND PermiteReservar='S' AND IDTipoReserva = '" . $IDTipoReserva . "'";
                    $resultado = $dbo->query($socioConPermiso);
                    $datos = $dbo->fetchArray($resultado);

                    if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && empty($datos)) {
                        $respuesta["message"] = "Lo sentimos, no tiene permiso para tomar las reservas de este tipo.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        //VALIDAMOS QUE LOS INVITADOS TENGAN PERMISO DE RESERVAR
                        $socioConPermiso = "SELECT COUNT(*) as total FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND PermiteReservar='S' AND IDTipoReserva = '" . $IDTipoReserva . "' AND IDSocio IN ($ids_socios)";
                        $resultado = $dbo->query($socioConPermiso);
                        $datos = $dbo->fetchArray($resultado);
                        $datos["total"];

                        if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && $datos["total"] != $total_invitados) {
                            $respuesta["message"] = "Lo sentimos, alguno de sus invitados no tiene permitido reservar ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;



                            return $respuesta;
                        }
                    }
                } else {

                    $socioConPermiso = "SELECT * FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "'  AND IDServicio = '" . $IDServicio . "' AND PermiteReservar='S'  AND NumeroDocumento = '" . $documentoSocio . "'";
                    $resultado = $dbo->query($socioConPermiso);
                    $datos = $dbo->fetchArray($resultado);

                    if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && empty($datos)) {
                        $respuesta["message"] = "Lo sentimos, no tiene permiso para tomar las reservas.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        //VALIDAMOS QUE LOS INVITADOS TENGAN PERMISO DE RESERVAR
                        $socioConPermiso = "SELECT COUNT(*) as total FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "'  AND IDServicio = '" . $IDServicio . "' AND PermiteReservar='S'  AND IDSocio IN ($ids_socios)";
                        $resultado = $dbo->query($socioConPermiso);
                        $datos = $dbo->fetchArray($resultado);
                        $datos["total"];
                        if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) &&  $datos["total"] != $total_invitados) {
                            $respuesta["message"] = "Lo sentimos, alguno de sus invitados no tiene permitido reservar ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }

            else :

                if ($permiteConTipo == 'S') {
                    $socioConPermiso = "SELECT * FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND NumeroDocumento = '" . $documentoSocio . "' AND PermiteReservar='N' AND IDTipoReserva = '" . $IDTipoReserva . "'";
                    $resultado = $dbo->query($socioConPermiso);
                    $datos = $dbo->fetchArray($resultado);

                    if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && !empty($datos)) {
                        $respuesta["message"] = "Lo sentimos, no tiene permiso para tomar las reservas de este tipo.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        //VALIDAMOS QUE LOS INVITADOS TENGAN PERMISO DE RESERVAR
                        $socioConPermiso = "SELECT COUNT(*) as total FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND IDSocio IN ($ids_socios) AND PermiteReservar='N' AND IDTipoReserva = '" . $IDTipoReserva . "'";
                        $resultado = $dbo->query($socioConPermiso);
                        $datos = $dbo->fetchArray($resultado);
                        $datos["total"];
                        if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && $datos["total"] > 0) {
                            $respuesta["message"] = "Lo sentimos, alguno de sus invitados no tiene permitido reservar ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                } else {

                    $socioConPermiso = "SELECT * FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND PermiteReservar='N' AND NumeroDocumento = '" . $documentoSocio . "'";
                    $resultado = $dbo->query($socioConPermiso);
                    $datos = $dbo->fetchArray($resultado);

                    if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) && !empty($datos)) {
                        $respuesta["message"] = "Lo sentimos, no tiene permiso para tomar las reservas.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        //VALIDAMOS QUE LOS INVITADOS TENGAN PERMISO DE RESERVAR
                        $socioConPermiso = "SELECT COUNT(*) as total FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDServicio = '" . $IDServicio . "' AND PermiteReservar='N'  AND IDSocio IN ($ids_socios)";
                        $resultado = $dbo->query($socioConPermiso);
                        $datos = $dbo->fetchArray($resultado);
                        $datos["total"];
                        if (($Fecha >= $fechaInicio && $Fecha <= $fechaFin) && ($Hora >= $horaInicio && $Hora <= $horaFin) &&  $datos["total"] > 0) {
                            $respuesta["message"] = "Lo sentimos, alguno de sus invitados no tiene permitido reservar ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }


            endif;
        }


        //  valido que solo los vacunados puedan reservar
        if ($datos_servicio["SoloVacunado"] == "S") {

            if (!empty($IDBeneficiario)) {
                $IDSocioValidarVac = $IDBeneficiario;
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $IDSocioValidarVac = $IDSocio;
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }

            $IDRegVac = $dbo->getFields("Vacuna2", "IDVacuna", "IDSocio = '" . $IDSocioValidarVac . "'");

            if (empty($IDRegVac)) {

                $IDRegVac = $dbo->getFields("Vacuna", "IDVacuna", "IDSocio = '" . $IDSocioValidarVac . "'");

                if (empty($IDRegVac)) {

                    if ($datos_servicio["ValidarEdadVacunados"] == 1) {

                        $dia_actual = date("Y-m-d");
                        $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
                        $EdadSocio = $edad_diff->format('%y');

                        if ($EdadSocio > $datos_servicio["EdadVacunados"]) {
                            $respuesta["message"] = "Lo sentimos, solo puede reservar si registra su vacuna en el app.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    } else {

                        $respuesta["message"] = "Lo sentimos, solo puede reservar si registra su vacuna en el app.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
            }
        }


        if ($datos_servicio["ValidarReservasActivas"] == "S" && $IDClub != 44 && $IDClub != 26) {
            $ValidaSeman = $datos_servicio["ValidaReservasActivasSemana"];
            $ValidaFin = $datos_servicio["ValidaReservasActivasFin"];
            $NumeroSeman = $datos_servicio["NumeroReservasActivasSemana"];
            $NumeroFin = $datos_servicio["NumeroReservasActivasFin"];
            $ValidaGeneral = $datos_servicio["ValidarReservasActivasGeneral"];
            $NumeroGeneral = $datos_servicio["NumeroReservasActivasGeneral"];

            $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);

            $Intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = $id_disponibilidad");
            $Turnos = $datos_tipo_reserva[NumeroTurnos];
            $minutos = $Turnos * $Intervalo;

            if ($IDClub == 201) :
                $minutos = $Intervalo;
            endif;




            $validar = SIMServicioReserva::valida_reservas_activas($IDClub, $IDServicio, $Fecha, $Hora, $IDSocio, $IDBeneficiario, $ValidaSeman, $ValidaFin, $NumeroSeman, $NumeroFin, $Invitados, 0, $ValidaGeneral, $NumeroGeneral, $minutos);

            if ($validar["success"] == false) {
                return $validar;
            }
        }
        // SE ACTIVA EL BLOQUEO DE RESERVAS EN UN HORARIO ESPECIFICO
        if ($datos_servicio[BloquearReservasHorario] == 1) :
            $HoraInicioBloqueo = $datos_servicio[HoraInicioBloqueo];
            $HoraFinBloqueo = $datos_servicio[HoraFinBloqueo];
            $HoraActual = date("H:i:s");

            if ((($HoraActual >= $HoraInicioBloqueo || $HoraActual <= $HoraFinBloqueo) && $HoraInicioBloqueo > $HoraFinBloqueo) || (($HoraActual >= $HoraInicioBloqueo && $HoraActual <= $HoraFinBloqueo) && $HoraInicioBloqueo < $HoraFinBloqueo)) :

                $Mensaje = $datos_servicio[MensajeBloqueoHorario];
                if (empty($Mensaje)) :
                    $Mensaje = "Horario bloqueado para hacer reserva desde $HoraInicioBloqueo hasta $HoraFinBloqueo";
                endif;

                $respuesta["message"] = $Mensaje;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;

            endif;
        endif;

        $IP = SIMUtil::get_IP();

        //Consulto el siguiente consecutivo
        if (!empty($datos_servicio["IdentificadorServicio"])) {
            $sql_max_numero = "Select MAX(ConsecutivoServicio) as NumeroMaximo From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "'";
            $result_numero = $dbo->query($sql_max_numero);
            $row_numero = $dbo->fetchArray($result_numero);
            $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;
            $IdentificadorServicio = $datos_servicio["IdentificadorServicio"];
            $ConsecutivoServicio = $siguiente_consecutivo;
        }

        //valido si esta en horario permitido para reservar
        if ($datos_servicio["ValidarHorario"] == "S" && empty($Admin) && (date("H:i:s") < $datos_servicio["HoraInicio"] || date("H:i:s") > $datos_servicio["HoraFin"])) {
            $respuesta["message"] = "Lo sentimos, el horario para reserva es de " . $datos_servicio["HoraInicio"] . " " . $datos_servicio["HoraFin"];
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        //valido si se puede reservar para el mismo dia
        if ($datos_servicio["ReservaMismoDia"] == "N" && $Fecha == date("Y-m-d") && empty($Admin)) {
            $respuesta["message"] = "Lo sentimos, no es posible realizar reservas para el mismo día";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        //Valido si el tipo de reserva solo permite cierta cantidad de boleadores/profesores
        if (!empty($IDTipoReserva)) {
            $datos_auxiliares_reser = json_decode($ListaAuxiliar, true);
            if (count($datos_auxiliares_reser) > 0 && count($datos_auxiliares_reser) > (int) $datos_tipo_reserva["MaximoBoleador"]) {
                $respuesta["message"] = " Supera el maximo de profesores/boleadores permitido para este tipo de reserva";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if (count($datos_auxiliares_reser) < (int) $datos_tipo_reserva["MinimoBoleador"]) {
                $respuesta["message"] = " Debe agregar por lo menos  " . $datos_tipo_reserva["MinimoBoleador"] . "  profesor/boleador para este tipo de reserva";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $NombreSocioReserva = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
        $AccionSocioReserva = $datos_socio["Accion"];

        if (!empty($IDBeneficiario)) {
            $datos_beneficiario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDBeneficiario . "' ", "array");
            $NombreBenefReserva = $datos_beneficiario["Nombre"] . " " . $datos_beneficiario["Apellido"];
            $AccionBenefReserva = $datos_beneficiario["Accion"];
        }

        $respuesta_sesion = SIMWebServiceUsuarios::valida_cierre_sesion($IDSocio);
        if ($respuesta_sesion == 1 && empty($Admin)) :
            //borro el id para no mostrar mas este mensaje
            $delete_cerrar_sesion = "delete from CierreSesionSocio Where IDSocio = '" . $IDSocio . "' Limit 1";
            $dbo->query($delete_cerrar_sesion);
            $nom_socio_validar = $NombreSocioReserva;
            $respuesta["message"] = "Es usted " . $nom_socio_validar . "?  Si no es " . $nom_socio_validar . " por favor cierre sesion y vuelva a ingresar con su usuario y clave para poder tomar la reserva";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        if (!empty($IDUsuarioReserva)) :
            //verifico si el usuario tienen permiso para hacer reservas
            $permite_funcionario_reserva = $dbo->getFields("Usuario", "PermiteReservar", "IDUsuario = '" . $IDUsuarioReserva . "'");
            if ($permite_funcionario_reserva == "N") :
                $respuesta["message"] = "Lo sentimos, no tiene permiso para realizar reservas para socios";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
        // Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible
        if (empty($IDElemento)) :
            $id_servicio_inicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
            if ($id_servicio_inicial == 5) : // 5 = get_reserva_aleatoria
                $IDElemento = SIMWebService::buscar_elemento_disponible($IDClub, $IDServicio, $Fecha, $Hora, $IDElemento);
            endif;
        endif;

        if (($id_servicio_maestro == "15" || $id_servicio_maestro == "27" || $id_servicio_maestro == "28") && empty($Tee)) :
            $respuesta["message"] = "Para poder reservar debe actualizar la app a la ultima version";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;






        //Valido si el invitado ha superado el maximo de ingresos a area deportiva
        $datos_invitado = json_decode($Invitados, true);
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $detalle_datos) :

                $IDSocioInvitado = $detalle_datos["IDSocio"];
                $NombreInvitado = $detalle_datos["Nombre"];
                $CedulaInvitado = $detalle_datos["Cedula"];

                if ($IDSocioInvitado == 0 || $CedulaInvitado != "") {

                    $validacion = SIMServicioReserva::validarReservaAreaDeportiva($IDClub, $IDServicio, $NombreInvitado, $CedulaInvitado);

                    if ($validacion["response"] == "N") :
                        $respuesta["message"] = $validacion["message"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                }

            endforeach;
        endif;

        //VALIDO QUE LOS INVITADOS EXTERNO NO SE ENVIEN CON COMAS o VACIOS Y QUE SI ES BENEFICIARIO NO ESTE COMO INVITADO
        $datos_invitado = json_decode($Invitados, true);
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $detalle_datos) :

                $IDSocioInvitado = $detalle_datos["IDSocio"];
                $NombreInvitado = $detalle_datos["Nombre"];
                $CedulaInvitado = $detalle_datos["Cedula"];

                if ($IDSocioInvitado == 0) {
                    $pos = strpos($NombreInvitado, ",");
                    if ($pos === false) {
                    } else {
                        $respuesta["message"] = "Los Invitados no puede ir separados por comas deben ser independiente";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    $countLen = strlen($NombreInvitado);
                    if ($countLen <= 3) {
                        $respuesta["message"] = "El nombre del invitado '" . $NombreInvitado . "' no es valido";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                if (!empty($IDBeneficiario)) {
                    if ($IDSocioInvitado == $IDBeneficiario) :
                        $respuesta["message"] = "El beneficiario no puede estar como invitado tambien";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                }

            endforeach;
        endif;

        //Valido si el socio puede reservar
        $permiso_reserva = SIMWebService::validar_permiso_reserva($IDSocio, $IDServicio);
        if ($permiso_reserva == "N" || $datos_socio["IDEstadoSocio"] == 2 || $datos_socio["IDEstadoSocio"] == 3 || $datos_socio["IDEstadoSocio"] == 4) :
            if ($IDClub == 239) :
                $respuesta["message"] = "Lo sentimos no puede crear una Reserva porque,  su acción presenta un retraso a la fecha";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :
                $respuesta["message"] = "Lo sentimos no tiene permiso para realizar reservas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        // VALIDAR PARA IZCARAGUA SI PUEDE RESERVAR
        if ($IDClub == 189) :
            require LIBDIR . "SIMWebServiceIzcaragua.inc.php";

            if (!empty($IDBeneficiario)) :
                $respuestaWS = SIMWebServiceIzcaragua::DeudaSocio($IDBeneficiario);
            else :
                $respuestaWS = SIMWebServiceIzcaragua::DeudaSocio($IDSocio);
            endif;

            if ($respuestaWS[success] == false) :
                return $respuestaWS;
            endif;

            // VALIDOS LOS INVITADOS
            $datos_invitado = json_decode($Invitados, true);
            foreach ($datos_invitado as $detalle_datos) :
                $IDSocioInvitado = $detalle_datos["IDSocio"];
                $NombreInvitado = $detalle_datos["Nombre"];

                if (empty($NombreInvitado)) :
                    $NombreInvitado = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocioInvitado") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = $IDSocioInvitado");
                endif;

                if ($IDSocioInvitado > 0) :

                    $respuestaInvi = SIMWebServiceIzcaragua::DeudaSocio($IDSocioInvitado);

                    if ($respuestaInvi[success] == false) :

                        $respuesta[message] = "El Invitado $NombreInvitado tiene una deuda con el club y no puede ser agregado a la reserva.";
                        $respuesta[success] = false;
                        $respuesta[response] = null;

                        return $respuesta;
                    endif;

                endif;
            endforeach;

        endif;


        //Valido si solo permite reservar por edades y si el club es diferente a nuba(196)
        if ($datos_servicio["ValidarEdad"] == "S" && $TipoBeneficiario != "Invitado" && $IDClub != 196) {
            if ((int) $IDBeneficiario > 0) {
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }
            //$fecha_nacimiento = $datos_socio["FechaNacimiento"];
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio = $edad_diff->format('%y');
            if ($EdadSocio >= $datos_servicio["EdadMinima"] && $EdadSocio <= $datos_servicio["EdadMaxima"]) {
                $edadpermitida == "S";
            } else {
                $respuesta["message"] = "Lo sentimos no tiene la edad permitida para realizar la reserva";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //Barranquilla validar edad en cierto horario
        if ($IDServicio == 19540) {
            if ((int) $IDBeneficiario > 0) {
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio = $edad_diff->format('%y');
            if ($EdadSocio < 18 && $Hora < "08:00:00") {
                $respuesta["message"] = "Lo sentimos no tiene la edad permitida para realizar la reserva en este horario";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //VALIDO EDAD PARA LOS INIVTADOS
        if ($datos_servicio["ValidarEdadInvitados"] == "S") {

            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :
                $cantidadExternos = 0;
                foreach ($datos_invitado as $detalle_datos) :

                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                    $FechaNacimientoSocioInvitado = $detalle_datos["FechaNacimiento"];

                    if ($datos_servicio["PermiteInvitadoExternoFechaNacimiento"] == "S") :

                        $dia_actual = date("Y-m-d");
                        $edad_diff = date_diff(date_create($FechaNacimientoSocioInvitado), date_create($dia_actual));
                        $EdadInvitado = $edad_diff->format('%y');
                        if ($EdadInvitado >= $datos_servicio["EdadMinima"] && $EdadInvitado <= $datos_servicio["EdadMaxima"]) {
                            $edadpermitida == "S";
                        } else {
                            $respuesta["message"] = "Lo sentimos el invitado $NombreSocioInvitado no tiene la edad para poder ir a la reserva";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }

                    endif;

                    $FechaNacimientoSocioInvitado = $dbo->getFields("Socio", "FechaNacimiento", "IDSocio = $IDSocioInvitado");
                    $NombreSocioInvitado = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocioInvitado");

                    $dia_actual = date("Y-m-d");
                    $edad_diff = date_diff(date_create($FechaNacimientoSocioInvitado), date_create($dia_actual));
                    $EdadInvitado = $edad_diff->format('%y');
                    if ($EdadInvitado >= $datos_servicio["EdadMinima"] && $EdadInvitado <= $datos_servicio["EdadMaxima"]) {
                        $edadpermitida == "S";
                    } else {
                        $respuesta["message"] = "Lo sentimos el invitado $NombreSocioInvitado no tiene la edad para poder ir a la reserva";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                endforeach;
            endif;
        }

        //VALIDO SI LOS TIPO RESERVA PERMITE POR EDADES
        if ($datos_tipo_reserva["ValidarEdad"] == 1 && empty($Admin) && $TipoBeneficiario != "Invitado") {

            if ((int) $IDBeneficiario > 0) {
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio = $edad_diff->format('%y');
            if ($EdadSocio >= $datos_tipo_reserva["EdadMinima"] && $EdadSocio <= $datos_tipo_reserva["EdadMaxima"]) {
                $edadpermitida == "S";
            } else {
                $respuesta["message"] = "Lo sentimos no tiene la edad permitida para realizar la reserva";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //Verifico la lista de espera, en el caso que solo pueda tomar este turno los que se inscribieron a la lista de espera
        if ($datos_servicio["PermiteListaEspera"] == "S" && $datos_servicio["SoloReservaListaEspera"] == "S") {
            $array_dia_valida = explode("|", $datos_servicio["DiasListaEsperaReserva"]);
            $dia_reserva_espera = date("w", strtotime($Fecha));
            if (in_array($dia_reserva_espera, $array_dia_valida)) {
                $validarEspera = SIMServicioReserva::valida_lista_espera($IDSocio, $Fecha, $Hora, $IDServicio);
                if ($validarEspera == "nolista") {
                    $respuesta["message"] = "Lo sentimos, este turno solo lo puede tomar los inscritos a la lista de espera";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }

        //Lagartos siempre verifico la edad
        if ($IDClub == 7 && empty($EdadSocio)) {
            if ((int) $IDBeneficiario > 0) {
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }
            //$fecha_nacimiento = $datos_socio["FechaNacimiento"];
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio = $edad_diff->format('%y');
        }

        //Espeial country el beneficiario debe tener menos de 5 años
        /*
            if($IDServicio=="12507" && $IDClub=44){
            if((int)$IDBeneficiario<=0){
            $respuesta[ "message" ] = "Lo sentimos la reserva debe ser a nombre de un beneficiario menor de 5 años";
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
            }
            else{
            $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio=$edad_diff->format('%y');
            if($EdadSocio>5 ){
            $respuesta[ "message" ] = "Lo sentimos el invitado: ".$nombre_socio_sancion." debe tener 5 años o menos";
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
            }
            }
            }
             */

        //Valido si el Beneficiario puede reservar
        if ((int) $IDBeneficiario > 0) {
            $permiso_reserva = SIMWebService::validar_permiso_reserva($IDBeneficiario, $IDServicio);
            if ($permiso_reserva == "N") :
                $respuesta["message"] = "Lo sentimos no tiene permiso para realizar reservas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        }

        //Valido si el socio puede reservar    si es un canje o invitado
        $permiso_reserva = SIMWebServiceUsuarios::validar_canje_activo($IDSocio, $Fecha);
        if ($permiso_reserva == "1") :
            $respuesta["message"] = "Lo sentimos no tiene permiso para realizar reservas, las fechas estan vencidas";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;



        // Verifico si tiene sanciones
        $sancion = SIMWebServiceReservas::verifica_sancion_socio($IDClub, $IDSocio, $IDServicio, $Fecha, $IDBeneficiario);
        //if ( $sancion && ( $IDClub == "8" || $IDClub == "15" ) ):
        if ($sancion) :
            $respuesta["message"] = "Lo sentimos tiene una sanción vigente por incumplimiento de una reserva";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        // Si tiene invitados verifico que los invitados no tengan sanciones
        $datos_invitado = json_decode($Invitados, true);
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $detalle_datos) :
                $IDSocioInvitado = $detalle_datos["IDSocio"];
                if (!empty($IDSocioInvitado)) :
                    $datos_invitado_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitado . "' ", "array");
                    $nombre_socio_sancion = $datos_invitado_reserva["Nombre"] . " " . $datos_invitado_reserva["Apellido"];
                    $sancion = SIMWebServiceReservas::verifica_sancion_socio($IDClub, $IDSocioInvitado, $IDServicio, $Fecha);
                    if ($sancion && $IDClub == "8") :
                        $respuesta["message"] = "Lo sentimos  el invitado " . $nombre_socio_sancion . " tiene una sancion vigente, la reserva no puede ser tomada";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    //edad

                    if ($datos_invitado_reserva["PermiteReservar"] == "N") {
                        $respuesta["message"] = $nombre_socio_sancion . " socio inactivo";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    if ($datos_servicio["ValidarEdad"] == "S") {
                        $fecha_nacimiento_invitado = $dbo->getFields("Socio", "FechaNacimiento", "IDSocio = '" . $IDSocioInvitado . "' and IDClub = '" . $IDClub . "'");
                        $dia_actual = date("Y-m-d");
                        $edad_diff = date_diff(date_create($fecha_nacimiento_invitado), date_create($dia_actual));
                        $EdadSocioInvitado = $edad_diff->format('%y');
                        if ($EdadSocioInvitado >= $datos_servicio["EdadMinima"] && $EdadSocioInvitado <= $datos_servicio["EdadMaxima"]) {
                            $edadpermitida == "S";
                        } else {
                            $respuesta["message"] = "Lo sentimos el invitado: " . $nombre_socio_sancion . " no tiene la edad permitida";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                    //fin edad
                    if ((int) $CantidadInvitadoSalon <= 0) {
                        $CantidadInvitadoSalon = count($datos_invitado) - 1;
                    }

                endif;
            endforeach;
        endif;

        //Si es admin valido si el auxiliar boleador esta disponible de nuevo
        if (!empty($Admin) && (!empty($IDAuxiliar))) :

            $flag_aux_disp = 0;
            $response_dispo_aux = SIMWebService::get_auxiliares($IDClub, $IDServicio, $Fecha, $Hora);
            $response_dispo_aux["success"];
            if ($response_dispo_aux["success"] == 0) :
                $flag_aux_disp = 1;
            else :
                $flag_aux_disp = 1;
                foreach ($response_dispo_aux["response"] as $datos_conf_aux) :
                    foreach ($datos_conf_aux["Auxiliares"] as $datos_aux) :
                        if ($IDAuxiliar == $datos_aux["IDAuxiliar"]) :
                            $flag_aux_disp = 0;
                            $HoraFinalAux = $datos_aux["HoraFin"];
                        endif;
                    endforeach;
                endforeach;
            endif;

            if ($flag_aux_disp == 1) :
                $respuesta["message"] = "Lo sentimos, el auxiliar no esta disponible en ese horario";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :

                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);

                $Intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = $id_disponibilidad");

                $Turnos = $datos_tipo_reserva[NumeroTurnos];
                $minutos = $Turnos * $Intervalo;
                $HoraFinalReserva = date("H:i:s", strtotime('+' . $minutos . ' minutes', strtotime($Hora)));

                $dia_fecha = date("w", strtotime($Fecha));
                $IDAuxiliarValidar = $IDAuxiliar;

                // VALIDAMOS QUE LA HORA FINAL DE LA RESERVA NO SEA MAYOR A LA FINAL DEL AUXILIAR
                $HoraFinalAuxReserva = date("H:i:s", strtotime('+' . $Intervalo . ' minutes', strtotime($HoraFinalAux)));
                if ($HoraFinalReserva > $HoraFinalAuxReserva) :
                    $respuesta["message"] = "Lo sentimos el horario de la reserva es mayor al tiempo disponible del auxiliar";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                // CONSULTO SI EL AUXILIAR ESTA EN ALGUNA FECHA DE CIERRE PROGRAMADA
                $SQLCierre = "SELECT * FROM AuxiliarCierre WHERE IDServicio = '$IDServicio' AND IDAuxiliar LIKE '%$IDAuxiliarValidar%' AND FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha' AND (HoraInicio < '$HoraFinalReserva' AND HoraFin > '$HoraFinalReserva') AND Dias LIKE '%$dia_fecha%'";
                $QRYCierre = $dbo->query($SQLCierre);

                if ($dbo->rows($QRYCierre) > 0) :
                    $respuesta["message"] = "Admin Lo sentimos el horario de la reserva es mayor al tiempo disponible del auxiliar";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                // BUSCAMOS LAS RESERVAS QUE TENGA EL AUXILIAR EN EL RANGO DE LA RESERVA
                $SQLReservas = "SELECT * FROM ReservaGeneral WHERE IDServicio = '$IDServicio' AND IDAuxiliar LIKE '%$IDAuxiliarValidar%' AND Fecha = '$Fecha' AND (Hora >= '$Hora' AND Hora < '$HoraFinalReserva')";
                $QRYReservas = $dbo->query($SQLReservas);

                if ($dbo->rows($QRYReservas) > 0 && ($IDClub == 133 || $IDClub == 48)) :
                    $respuesta["message"] = "Lo sentimos pero uno de los auxiliares no esta disponible en ese horario, porque ya esta asignado a otra reserva";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

            endif;

        endif;

        //Verifico de nuevo que la lista de auxiliares seleccionados esten disponibles
        if (!empty($ListaAuxiliar)) :
            $datos_auxiliares_revisar = json_decode($ListaAuxiliar, true);
            $response_dispo_aux = SIMWebService::get_auxiliares($IDClub, $IDServicio, $Fecha, $Hora);
            foreach ($response_dispo_aux["response"] as $datos_conf_aux) :
                foreach ($datos_conf_aux["Auxiliares"] as $datos_aux) :
                    $array_aux_disponibles[] = $datos_aux["IDAuxiliar"];
                    $HorasFinales[$datos_aux["IDAuxiliar"]] = $datos_aux["HoraFin"];
                endforeach;
            endforeach;

            if (count($datos_auxiliares_revisar) > 0) :
                foreach ($datos_auxiliares_revisar as $key_aux => $auxiliar_seleccionado) :
                    if (!in_array($auxiliar_seleccionado["IDAuxiliar"], $array_aux_disponibles)) :

                        $respuesta["message"] = "El auxiliar " . $auxiliar_seleccionado["Nombre"] . " no esta disponible en ese horario";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :
                        $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);

                        $Intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = $id_disponibilidad");

                        $Turnos = $datos_tipo_reserva[NumeroTurnos];
                        $minutos = $Turnos * $Intervalo;
                        $HoraFinalReserva = date("H:i:s", strtotime('+' . $minutos . ' minutes', strtotime($Hora)));

                        $dia_fecha = date("w", strtotime($Fecha));
                        $IDAuxiliarValidar = $auxiliar_seleccionado["IDAuxiliar"];

                        $HoraFinalAux = $HorasFinales[$auxiliar_seleccionado["IDAuxiliar"]];

                        // VALIDAMOS QUE LA HORA FINAL DE LA RESERVA NO SEA MAYOR A LA FINAL DEL AUXILIAR
                        $HoraFinalAuxReserva = date("H:i:s", strtotime('+' . $Intervalo . ' minutes', strtotime($HoraFinalAux)));

                        if ($HoraFinalReserva > $HoraFinalAuxReserva) :
                            $respuesta["message"] = "Lo sentimos el horario de la reserva es mayor al tiempo disponible del auxiliar";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        // CONSULTO SI EL AUXILIAR ESTA EN ALGUNA FECHA DE CIERRE PROGRAMADA
                        $SQLCierre = "SELECT * FROM AuxiliarCierre WHERE IDServicio = '$IDServicio' AND IDAuxiliar LIKE '%$IDAuxiliarValidar%' AND FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha' AND (HoraInicio < '$HoraFinalReserva' AND HoraFin > '$HoraFinalReserva') AND Dias LIKE '%$dia_fecha%'";
                        $QRYCierre = $dbo->query($SQLCierre);

                        if ($dbo->rows($QRYCierre) > 0) :
                            $respuesta["message"] = "Lo sentimos el horario de la reserva es mayor al tiempo disponible del auxiliar";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        // BUSCAMOS LAS RESERVAS QUE TENGA EL AUXILIAR EN EL RANGO DE LA RESERVA
                        $SQLReservas = "SELECT * FROM ReservaGeneral WHERE IDServicio = '$IDServicio' AND IDAuxiliar LIKE '%$IDAuxiliarValidar%' AND Fecha = '$Fecha' AND (Hora >= '$Hora' AND Hora < '$HoraFinalReserva')";
                        $QRYReservas = $dbo->query($SQLReservas);

                        if ($dbo->rows($QRYReservas) > 0 && $IDClub == 133) :
                            $respuesta["message"] = "Lo sentimos pero uno de los auxiliares no esta disponible en ese horario, porque ya esta asignado a otra reserva";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                    endif;
                endforeach;
            endif;
        endif;

        //Especial Pereira restaurante 40 por dia sin importar la hora
        if ($IDServicio == 5609 && $IDClub == 15) {
            //Verifico cuantas personas estan inscritas
            $LimiteCuposServicio = 40;
            $sql_invitados = "SELECT SUM(CantidadInvitadoSalon) as TotalInvitado FROM ReservaGeneral WHERE IDServicio = '" . $IDServicio . "' and IDClub='" . $IDClub . "' and Fecha = '" . $Fecha . "'";
            $result_invitado = $dbo->query($sql_invitados);
            $row_invitado_servicio = $dbo->fetchArray($result_invitado);
            if ((int) $row_invitado_servicio["TotalInvitado"] > $LimiteCuposServicio) {
                $respuesta["message"] = "Lo sentimos se llegó al máximo permitido de " . $LimiteCuposServicio . " personas al dia";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } else {
                //Verifico que los seleccionados no superen el permitido
                $TotalNuevoInvitado = (int) $CantidadInvitadoSalon + (int) $row_invitado_servicio["TotalInvitado"];
                if ($TotalNuevoInvitado > $LimiteCuposServicio) {
                    $CuposRestantes = (int) $LimiteCuposServicio - (int) $row_invitado_servicio["TotalInvitado"];
                    $respuesta["message"] = "Lo sentimos solo quedan " . $CuposRestantes . " cupos";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }

        //Especial Lagartos caddies 50 por dia sin importar la hora
        if ($IDServicio == 93 && $IDClub == 7) {
            //Verifico cuantas personas estan inscritas
            $LimiteCuposServicio = 50;
            $sql_invitados = "SELECT COUNT(IDReservaGeneral) as TotalReservas FROM ReservaGeneral WHERE IDServicio = '" . $IDServicio . "' and IDClub='" . $IDClub . "' and Fecha = '" . $Fecha . "'";
            $result_invitado = $dbo->query($sql_invitados);
            $row_invitado_servicio = $dbo->fetchArray($result_invitado);
            if ((int) $row_invitado_servicio["TotalReservas"] > $LimiteCuposServicio) {
                $respuesta["message"] = "Lo sentimos se llegó al máximo permitido de " . $LimiteCuposServicio . " caddies al dia";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //Validacion especial para Pradera en Esqui en la cual no se permite al grupo familiar tomar una reserva si alguein de su grupo eliminó una previamente
        $MinutosRestriccion = $datos_servicio["BloquearMinutosGrupo"];
        //if( ($IDClub==16) && $IDServicio==327):
        if ((int) $MinutosRestriccion > 0) :

            $minutos_restriccion = (int) $MinutosRestriccion;
            //verifico si alguien del grupo ha eliminado reserva

            $grupo_familiar = SIMWebService::get_beneficiarios($IDClub, $IDSocio);
            if (count($grupo_familiar["response"]["Beneficiarios"]) > 0) :
                foreach ($grupo_familiar["response"]["Beneficiarios"] as $datos_nucleo) :
                    if ($datos_nucleo["TipoBeneficiario"] == "Socio") :
                        $array_id_benef[] = $datos_nucleo["IDBeneficiario"];
                    endif;
                endforeach;
            endif;

            if (count($array_id_benef) > 0) :
                $condicion_benef = " or IDSocio in (" . implode(",", $array_id_benef) . ") ";
            endif;

            if ($IDClub == 7) { //para lagartos no se debe bloquera al que hace la reserva
                $Condiciones = "IDSocio = '1'  $condicion_benef";
            } else {
                $Condiciones = "IDSocio = '$IDSocio'  $condicion_benef";
            }


            // para pradera solo berifico las de mis beneficiarios
            if ($IDServicio == "327") {
                $Condiciones = $condicion_benef;
            }

            $sql_eliminada = "Select * From ReservaGeneralEliminada Where ( $Condiciones )  and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' order by IDReservaGeneral Desc limit 1";
            $result_eliminada = $dbo->query($sql_eliminada);
            if ($dbo->rows($result_eliminada) > 0) :
                //verifico si ya cumplio el tiempo limite para poder intentar reservar
                $row_reserva_eliminada = $dbo->fetchArray($result_eliminada);
                $FechaHoraEliminacion = strtotime('+' . $minutos_restriccion . ' minute', strtotime($row_reserva_eliminada["FechaTrEd"]));
                $FechaHoraActual = strtotime(date("Y-m-d H:i:s"));
                if ($FechaHoraActual <= $FechaHoraEliminacion && $IDSocio != "135054") :
                    $respuesta["message"] = "La reserva no puede ser tomada ya que alguien de su grupo familiar hizo una reserva y la eliminó para esta fecha, puede volver a intentar a las: " . date("H:i:s", $FechaHoraEliminacion);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endif;
        endif;

        if ($IDClub == 141 || $IDClub == 183) {
            date_default_timezone_set('America/Caracas');
        }

        //Especial para atc solo dos turnos por semana
        if ($IDClub == 26 && empty($Admin)) :
            if (
                $IDServicio == "1490" || $IDServicio == "2106" || $IDServicio == "2109" || $IDServicio == "2110"
                || $IDServicio == "4350" || $IDServicio == "1484" || $IDServicio == "5035" || $IDServicio == "5039"
                || $IDServicio == "7973" || $IDServicio == "2719" || $IDServicio == "17286" || $IDServicio == "1434"
            ) : // tenis y clase tenis hasta 2
                $ReservasPermitidaSemana = 3;
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            elseif ($IDServicio == "1462") : // masajes manicure hasta 3
                $ReservasPermitidaSemana = 3;
                $condicion_reserva_verif = "";
            elseif ($IDServicio == "2720") : // coliseos
                $ReservasPermitidaSemana = 2;
                $condicion_reserva_verif = "";
            else : // las demas
                $ReservasPermitidaSemana = 100;
                $condicion_reserva_verif = "";
            endif;

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

            $accion_padre = $datos_socio["AccionPadre"];
            $accion_socio = $datos_socio["Accion"];

            if (empty($accion_padre)) : // Es titular
                $array_socio[] = $IDSocio;
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;

            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;

            if ((int) $dia_reserva_atc >= 1 && (int) $dia_reserva_atc <= 5) {
                $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 4 day')); //Viernes
                $mensaje_reserva = " entre semana";
            } else {
                $proximo_sabado = strtotime('next Saturday');
                $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
                $mensaje_reserva = " los fines de semana";
            }

            //Consulto las de hoy pasada la hora actual
            $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            $total_reservas_semana = $dbo->rows($sql_reservas_sem);


            //Consulto la de mañana en adelante
            $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por semana por accion ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

            // VALIDAR LOS INVITADOS

            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) {
                foreach ($datos_invitado as $detalle_datos) {
                    $total_reservas_semana_inv = 0;
                    $total_reservas_dia_hora = 0;
                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    if (!empty($IDSocioInvitado)) {

                        //Consulto las de hoy pasada la hora actual
                        $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in ($IDSocioInvitado) and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                        $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                        //Consulto la de mañana en adelante
                        $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in ($IDSocioInvitado) and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                        $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

                        if ((int) $total_reservas_semana_inv >= $ReservasPermitidaSemana) {
                            $respuesta["message"] = "Lo sentimos el invitado ya tiene " . $ReservasPermitidaSemana . " reservas activas";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }
            }
        endif;

        //Especial country 2 entre semana 1 fin semana para clase de golf
        /*
            if ( $IDClub == 44  && $IDServicio == "3866"  && empty( $Admin ) ):

            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            $fecha_hoy_semana = date( "Y-m-d" );
            $hora_hoy_semana = date( "H:i:s" );
            $year = date( 'Y', strtotime( $Fecha ) );
            $week = date( 'W', strtotime( $Fecha ) );
            $dia_reserva_atc = date("w",strtotime($Fecha)) ;
            $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo

            if( (int)$dia_reserva_atc>=1 && (int)$dia_reserva_atc<=5){
            $ReservasPermitidaSemana = 2;
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 4 day' ) ); //Viernes
            $mensaje_reserva=" entre semana";
            }
            else{
            $ReservasPermitidaSemana = 1;
            $proximo_sabado = strtotime('next Saturday');
            $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
            $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
            $mensaje_reserva=" los fines de semana";
            }

            if((int)$IDBeneficiario>0){
            $sql_valida="SELECT * FROM ReservaGeneral Where ( (IDSocio = '".$IDBeneficiario."'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '".$IDBeneficiario."' )  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            }
            else{
            $sql_valida="SELECT * FROM ReservaGeneral Where ( (IDSocio = '".$IDSocio."' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '".$IDSocio."' )  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            }

            $sql_reservas_sem = $dbo->query( $sql_valida );
            $total_reservas_semana = $dbo->rows( $sql_reservas_sem );

            if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
            $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas " . $mensaje_reserva;
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
            endif;
            endif;
             */

        //Especial Serrezuela 2 fin semana
        if ($IDClub == 113 && $IDServicio == "20199" && empty($Admin)) :

            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

            if ((int) $dia_reserva_atc >= 1 && (int) $dia_reserva_atc <= 5) {
                // Entre semana por ahora no hay restriccion
            } else {
                $ReservasPermitidaSemana = 2;
                $proximo_sabado = strtotime('next Saturday');
                $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
                $mensaje_reserva = " los fines de semana";

                if ($fecha_hoy_semana == $Fecha) {
                    if (!empty($IDBeneficiario)) {
                        $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . $Fecha . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                    } else {
                        $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . $Fecha . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                    }
                    $total_reservas_semana = $dbo->rows($sql_reservas_sem);
                    $condicion_otras = ">";
                } else {
                    $condicion_otras = ">=";
                }

                //Consulto la de mañana en adelante
                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha " . $condicion_otras . " '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                } else {
                    $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha " . $condicion_otras . " '" . $fecha_inicio_valida . "'  and Fecha <= '" . $fecha_fin_valida . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                }

                $sql_reservas_sem = $dbo->query($sql_valida);
                $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                    $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas " . $mensaje_reserva;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }

        endif;
        //FIN Serrezuela 2 fin semana

        //Especial cerro de los alpes solo permitir reservas dia por medio si reservo el martes la proxima solo piede ser el jueves en adelante
        if ($IDClub == 81 && empty($Admin) && ($IDServicio == "11580")) {
            $hoy_val = date("Y-m-d");
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            //Consulto la ultima reserva
            $sql_ult = "SELECT IDReservaGeneral,Fecha FROM ReservaGeneral WHERE IDSocio = '" . $IDSocio . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva=1 ORDER BY Fecha DESC LIMIT 1";
            $r_ult = $dbo->query($sql_ult);
            $row_ult = $dbo->fetchArray($r_ult);

            if (!empty($row_ult["Fecha"])) {
                /*
                    if ($row_ult["Fecha"] >= $hoy_val) {
                    $respuesta["message"] = "Lo sentimos solo puede reservar despues de pasada la meda noche de su reserva activa";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;

                    }
                     */

                // le sumo un dia a la fecha de la ultima reserva
                $DiaSiguienteUltima = strtotime('+1 day', strtotime($row_ult["Fecha"]));
                $FechaDiaSiguiente = date("Y-m-d", $DiaSiguienteUltima);
                if ($FechaDiaSiguiente == $Fecha) {
                    $respuesta["message"] = "Lo sentimos solo puede reservar dia por medio";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }
        //Fin especial cerro alpes

        //ESPECIAL ANAPOIMA JACUZZI
        /*
            if ($IDClub == 46 && empty($Admin) && ($IDServicio == "4161" || $IDServicio == "4208")) {

            $hoy_val=date("Y-m-d");
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            //Consulto la ultima reserva
            $sql_ult = "SELECT IDReservaGeneral, Fecha FROM ReservaGeneral WHERE IDSocio = '" . $IDSocio . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva=1 ORDER BY Fecha DESC LIMIT 1";
            $r_ult = $dbo->query($sql_ult);
            $row_ult = $dbo->fetchArray($r_ult);

            if (!empty($row_ult["Fecha"])) {

            $DiaSiguienteUltima = strtotime('+1 day', strtotime($row_ult["Fecha"]));
            $FechaDiaSiguiente = date("Y-m-d", $DiaSiguienteUltima);

            if ($FechaDiaSiguiente == $Fecha) {
            $respuesta["message"] = "Lo sentimos solo puede reservar dia por medio";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            }
            }
            }
            //FIN ESPECIAL ANAPOIMA JACUZZI
             */

        /*
            //Especial para Arrayanes ecuador solo 3 turnos por semana stalin
            if ( $IDClub == 23 && empty( $Admin ) && ($IDServicio == "5681")):
            $ReservasPermitidaSemana = 3;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date( "Y-m-d" );
            $hora_hoy_semana = date( "H:i:s" );
            $year = date( 'Y', strtotime( $Fecha ) );
            $week = date( 'W', strtotime( $Fecha ) );
            $dia_reserva_atc = date("w",strtotime($Fecha)) ;
            $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
            $mensaje_reserva=" por semana";
            //Consulto la de mañana en adelante
            $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where (IDSocio = '".$IDSocio."' or IDSocioBeneficiario = '".$IDSocio."' )  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

            if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
            $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por semana ";
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
            endif;
            endif;
            //Fin especial arrayanes ecuador
             */

        //Especial medellin psicina solo puede dos reservas al dia pero si es a la miam hora
        if ($IDClub == 20 && empty($Admin) && ($IDServicio == "21732")) {
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . $Fecha . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1	 ");
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1 ");
            }
            $total_reservas_semana_p = (int) $dbo->rows($sql_reservas_sem);
            if ((int) $total_reservas_semana_p >= 1) {
                $row_datos_r = $dbo->fetchArray($sql_reservas_sem);
                if ($row_datos_r["Hora"] != $Hora) {
                    $respuesta["message"] = "Lo sentimos solo puede hacer una segunda reserva en el mismo día solo si es a la misma hora ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }
        //FIN ESPECIAL MEDELLIN PISCINA

        //Especial para country barranquilla 1 por semana en tenis
        if ($IDClub == 110 && empty($Admin) && ($IDServicio == "19540")) :
            $ReservasPermitidaSemana = 1;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $mensaje_reserva = " por semana";

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' )  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' )  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "B1. Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reserva activa ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;
        //Fin especial B/quilla

        if ($IDClub == 125) {
            date_default_timezone_set('America/Montevideo');
        }

        // CONDICION URUGUAY PARA TIPO SENCILLO SOLO UNA AL DIA O 2 SI ES EL MISMO DIA Y 5 HORAS ENTES

        //Especial para uruguay 2 activas en piscinas o tenis
        if ($IDClub == 125 && empty($Admin) && ($IDServicio == "23009" || $IDServicio == "23067" || $IDServicio == "23010" /* || $IDServicio == "23030" */ || $IDServicio == "23059" || $IDServicio == "23054" || $IDServicio == "22896")) :
            date_default_timezone_set('America/Montevideo');
            $mifecha = date('Y-m-d H:i:s');

            if ($Hora <= "10:00:00") {
                $NuevaFecha = strtotime('+10 hour', strtotime($mifecha));
            } else {
                $NuevaFecha = strtotime('+5 hour', strtotime($mifecha));
            }

            $FechaHoraValU = strtotime($Fecha . " " . $Hora);

            if ($FechaHoraValU >= $NuevaFecha) {

                if ($IDServicio == "23009") :
                    // VALIDAMOS LAS · HORAS DE ANTICIPACIÓN
                    $HoraActual = date('Y-m-d H:i:s');
                    $HorasMas3 = strtotime("+3 hour", strtotime($HoraActual));

                    if ($FechaHoraValU < $HorasMas3) :
                        $respuesta["message"] = "Debe reservar con 3 horas de anticipación";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                if ($IDServicio == "23009") {
                    $ReservasPermitidaSemana = 2;
                } else {
                    $ReservasPermitidaSemana = 1;
                }

                if ($IDServicio == "23059" || $IDServicio == "23054") {

                    $ServiciosValidar = "23059,23054";
                    $ReservasPermitidaSemana = 2;

                    if ($IDServicio == "23059") :
                        $condicion_reserva_verif = " AND Fecha = '$Fecha'";
                        $CantidadMismoDia = 1;
                    endif;
                } else {
                    $ServiciosValidar = $IDServicio;
                }

                $condicion_reserva_verif .= " and Tipo <> 'Automatica'";

                if ($IDServicio == "23059" || $IDServicio == "23009") {
                    $condicion_reserva_verif .= " and Cumplida <> 'S'";
                }

                $hora_hoy_semana = date("H:i:s");
                $year = date('Y', strtotime($Fecha));
                $week = date('W', strtotime($Fecha));
                $dia_reserva_atc = date("w", strtotime($Fecha));
                $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
                $fecha_lunes = $fechaInicioSemana; //Lunes
                $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 15 day')); //Domingo
                $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 15 day')); //Domingo
                $mensaje_reserva = " por semana";

                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "') and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                } else {
                    $sql = "Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                    $sql_reservas_sem = $dbo->query($sql);
                }

                //Consulto las de hoy pasada la hora actual
                //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
                $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                //Consulto la de mañana en adelante
                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "' and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                } else {
                    $sql = "Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                    $sql_reservas_sem = $dbo->query($sql);
                }

                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

                //Consulto donde sea invitado
                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem = $dbo->query("Select RG.* From ReservaGeneral RG,ReservaGeneralInvitado RGI Where RG.IDReservageneral=RGI.IDReservageneral and ( RGI.IDSocio = '" . $IDBeneficiario . "') and (Fecha = '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                } else {
                    $sql_reservas_sem = $dbo->query("Select RG.* From ReservaGeneral RG,ReservaGeneralInvitado RGI Where RG.IDReservageneral=RGI.IDReservageneral and ( RGI.IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                }
                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem = $dbo->query("Select RG.* From ReservaGeneral RG,ReservaGeneralInvitado RGI Where RG.IDReservageneral=RGI.IDReservageneral and  ( RGI.IDSocio = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "' and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                } else {
                    $sql_reservas_sem = $dbo->query("Select RG.* From ReservaGeneral RG,ReservaGeneralInvitado RGI Where  RG.IDReservageneral=RGI.IDReservageneral and ( RGI.IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                }
                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);
                // Consulto los invitados de esta reserva que no tenga otras reservas

                //Consulto que los invitados no tengan en la misma hora reserva
                $datos_invitado = json_decode($Invitados, true);
                if (count($datos_invitado) > 0) {
                    foreach ($datos_invitado as $detalle_datos) {
                        $total_reservas_semana_inv = 0;
                        $total_reservas_dia_hora = 0;
                        $IDSocioInvitado = $detalle_datos["IDSocio"];
                        if (!empty($IDSocioInvitado)) {

                            //Consulto cuantas reservas tiene es invitado
                            $sql_reservas_sem_inv = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocioInvitado . ") ) and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                            $total_reservas_semana_inv += (int) $dbo->rows($sql_reservas_sem_inv);

                            $sql_reservas_sem_inv = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocioInvitado . ") ) and  Fecha > '" . date("Y-m-d") . "' and IDServicio in ( " . $ServiciosValidar . " ) and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                            $total_reservas_semana_inv += (int) $dbo->rows($sql_reservas_sem_inv);

                            ///Consulto donde sea invitado
                            $sql_invitado_hora = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG
																		WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocioInvitado . "') and
																		RG.IDClub = '" . $IDClub . "' and RG.Fecha >= '" . date("Y-m-d") . "' and
																		RG.IDServicio in ($IDServicio) $condicion_reserva_verif
																		ORDER BY IDReservaGeneralInvitado Desc ";

                            $qry_invitado_hora = $dbo->query($sql_invitado_hora);
                            $total_reservas_semana_inv += $dbo->rows($qry_invitado_hora);

                            if ((int) $total_reservas_semana_inv >= $ReservasPermitidaSemana) {
                                $respuesta["message"] = "Lo sentimos el invitado ya tiene " . $ReservasPermitidaSemana . " reservas activas";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                    }
                }
                //Fin Validar

                // PARA TENIS TURNOS SOLO ES UNA RESERVA POR DIA CUANDO EL DIA QUE SE TOMA LA RESERVA ES DIFERENTE AL ACTUAL
                if ($total_reservas_semana >= $CantidadMismoDia && $IDServicio == "23059") :
                    $respuesta["message"] = "Lo sentimos solo se permiten " . $CantidadMismoDia . " reservas activa un mismo día";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                    $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activa ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }
        endif;

        //URUGUAY
        // Para Clase adulto depende el tipo de reserva
        if ($IDClub == 125 && empty($Admin) && ($IDServicio == "22900" || $IDServicio == "23029" || $IDServicio == "23028" || $IDServicio == "23067" || $IDServicio == "23034" || $IDServicio == "23034"
            || $IDServicio == "23035" || $IDServicio == "23032" || $IDServicio == "23030" || $IDServicio == "23036" || $IDServicio == "23033" || $IDServicio == "23034" || $IDServicio == "23027")) :

            date_default_timezone_set('America/Montevideo');
            $mifecha = date('Y-m-d H:i:s');
            $NuevaFecha = strtotime('+3 hour', strtotime($mifecha));
            $FechaHoraValU = strtotime($Fecha . " " . $Hora);

            if ($FechaHoraValU >= $NuevaFecha) {

                if ($IDServicio == 23027) {
                    $ReservasPermitidaSemana = 2;
                } else {
                    $ReservasPermitidaSemana = 1;
                }

                $sql_serv_ele = "SELECT IDServicioTipoReserva FROM ServicioElementoTipoReserva WHERE IDServicioElemento = '$IDElemento' ";
                $r_serv_ele = $dbo->query($sql_serv_ele);
                while ($row_serv_ele = $dbo->fetchArray($r_serv_ele)) {
                    $sql_serv_ele_asoc = "SELECT IDServicioElemento FROM ServicioElementoTipoReserva WHERE IDServicioTipoReserva = '" . $row_serv_ele["IDServicioTipoReserva"] . "' ";
                    $r_serv_ele_asoc = $dbo->query($sql_serv_ele_asoc);
                    while ($row_serv_ele_asoc = $dbo->fetchArray($r_serv_ele_asoc)) {
                        $array_ele_asoc[] = $row_serv_ele_asoc["IDServicioElemento"];
                    }
                }

                if (count($array_ele_asoc) > 0) {
                    $id_elem_aso = implode(",", $array_ele_asoc);
                } else {
                    $id_elem_aso = $IDElemento;
                }

                //$ReservasPermitidaSemana = 1;
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";

                $fecha_hoy_semana = date("Y-m-d");
                $hora_hoy_semana = date("H:i:s");
                $year = date('Y', strtotime($Fecha));
                $week = date('W', strtotime($Fecha));
                $dia_reserva_atc = date("w", strtotime($Fecha));
                $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
                $fecha_lunes = $fechaInicioSemana; //Lunes
                $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 20 day')); //Domingo
                $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 20 day')); //Domingo
                $mensaje_reserva = " por semana";

                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem2 = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha = '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' )  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in (" . $id_elem_aso . ") " . $condicion_reserva_verif);
                } else {
                    $sql_reservas_sem2 = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "'  and Hora >= '" . date("H:i:s") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in (" . $id_elem_aso . ") " . $condicion_reserva_verif);
                }

                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem2);

                if (!empty($IDBeneficiario)) {
                    $sql_reservas_sem2 = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha> '" . date("Y-m-d") . "'  )  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in (" . $id_elem_aso . ") " . $condicion_reserva_verif);
                } else {
                    $sql_reservas_sem2 = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha>'" . date("Y-m-d") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in (" . $id_elem_aso . ") " . $condicion_reserva_verif);
                }
                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem2);

                //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
                //$total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem2 );

                if ((int) $total_reservas_semana >= $ReservasPermitidaSemana) :
                    $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " activa o por dia por esta clase de reserva";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }
        endif;

        //Fin especial Uruguay



        // ESPCIAL CHICUREO MENOSRES DE EDAD SABADO, DOMINGO, FESTVIOS SOLO DESPUES DE LAS 13

        if ($IDClub == 178 && $IDServicio == 35583) :
            $DiaReserva = date("w", strtotime($Fecha));
            $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$Fecha' AND IDPais = '9'");

            $dia_actual = date("Y-m-d");

            if (!empty($IDBeneficiario)) {
                $fecha_nacimiento = $datos_beneficiario["FechaNacimiento"];
            } else {
                $fecha_nacimiento = $datos_socio["FechaNacimiento"];
            }

            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $EdadSocio = $edad_diff->format('%y');

            if (($DiaReserva == '6' || $DiaReserva == '0' || !empty($IDFestivo)) && $EdadSocio < 18 && $Hora < '13:00:00') :
                $respuesta["message"] = "Lo sentimos, Socios menores de 18 años no pueden jugar antes de las 13:00:00 los sabados, domingos o festivos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        // Para Calse adulto depende el tipo de reserva
        /*
            if ( $IDClub == 125 && empty( $Admin ) && ($IDServicio == "22900")):

            $ReservasPermitidaSemana = 1;
            $sql_serv_ele="SELECT IDServicioTipoReserva FROM ServicioElementoTipoReserva WHERE IDServicioElemento = '$IDElemento' ";
            $r_serv_ele=$dbo->query($sql_serv_ele);
            while($row_serv_ele=$dbo->fetchArray($r_serv_ele)){
            $sql_serv_ele_asoc="SELECT IDServicioElemento FROM ServicioElementoTipoReserva WHERE IDServicioTipoReserva = '".$row_serv_ele["IDServicioTipoReserva"]."' ";
            $r_serv_ele_asoc=$dbo->query($sql_serv_ele_asoc);
            while($row_serv_ele_asoc=$dbo->fetchArray($r_serv_ele_asoc)){
            $array_ele_asoc[]=$row_serv_ele_asoc["IDServicioElemento"];
            }
            }

            if(count($array_ele_asoc)>0){
            $id_elem_aso =implode(",",$array_ele_asoc);
            }
            else{
            $id_elem_aso = $IDElemento;
            }

            $ReservasPermitidaSemana = 1;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date( "Y-m-d" );
            $hora_hoy_semana = date( "H:i:s" );
            $year = date( 'Y', strtotime( $Fecha ) );
            $week = date( 'W', strtotime( $Fecha ) );
            $dia_reserva_atc = date("w",strtotime($Fecha)) ;
            $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
            $mensaje_reserva=" por semana";

            if(!empty($IDBeneficiario)){
            $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where ( IDSocioBeneficiario = '".$IDBeneficiario."') and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' )  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in ('".$id_elem_aso."') " . $condicion_reserva_verif );
            }
            else{
            $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in ('".$id_elem_aso."') " . $condicion_reserva_verif );
            }

            //Consulto las de hoy pasada la hora actual
            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            //$total_reservas_semana = $dbo->rows( $sql_reservas_sem );

            //Consulto la de mañana en adelante

            if(!empty($IDBeneficiario)){
            $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where ( IDSocioBeneficiario = '".$IDBeneficiario."') and  Fecha > '" . date( "Y-m-d" ) . "'  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDElemento in ('".$id_elem_aso."') " . $condicion_reserva_verif );
            }
            else{
            $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and  Fecha > '" . date( "Y-m-d" ) . "'  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento in ('".$id_elem_aso."') " . $condicion_reserva_verif );
            }

            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

            if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
            $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reserva activa ";
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
            endif;
            endif;
             */
        //Fin especial Uruguay

        //Especial para Lagartos natacion 3 por semana
        if ($IDClub == 7 && empty($Admin) && ($IDServicio == "37" || $IDServicio == "622")) :
            if ($IDServicio == "37") {
                $ReservasPermitidaSemana = 3;
            } elseif ($IDServicio == "622") {
                $ReservasPermitidaSemana = 6;
            } else {
                $ReservasPermitidaSemana = 3;
            }

            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");

            if ($fecha_hoy_semana == $Fecha) {
                $condicion_hora = " and Hora >= '" . date("H:i:s") . "' ";
            }

            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 15 day')); //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 15 day')); //Domingo
            $mensaje_reserva = " por semana";

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //Consulto las de hoy pasada la hora actual
            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reserva activas ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;
        //Fin especial lagartos

        // ESPECIAL FORET SOLO 5 RESERVAS AL MES PARA SALON SOCIAL Y DE JUNTAS

        if ($IDClub == 140 && ($IDServicio == 27504 || $IDServicio == 27506)) :
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            $ReservasPermitidaMes = 5;

            $mes_reserva = substr($Fecha, 5, 2);

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario') and (MONTH(Fecha) = '$mes_reserva') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocio in ($IDSocio) and IDSocioBeneficiario = '$IDBeneficiario') and (MONTH(Fecha) = '$mes_reserva') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }
            $total_reservas_mes = $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_mes >= (int) $ReservasPermitidaMes) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaMes . " reservas al mes";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        //Distrital Y nadesba clase obligar a poner profesor
        if (($IDClub == 52 && $IDServicio == "5186" && $IDTipoReserva == 3127) /* || ($IDClub == 106 && $IDServicio == "18686") */) {
            $datos_auxiliares_val = json_decode($ListaAuxiliar, true);
            if (count($datos_auxiliares_val) > 0) {
                $respuesta["message"] = "RESERVA No realizada. En este tipo de reserva no es posible seleccionar un profesor, por favor verifique. ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        $datos_invitado_clase = json_decode($Invitados, true);
        if ($IDClub == 52 && $IDServicio == "5186" && $IDTipoReserva == 3129 && count($datos_invitado_clase) > 0) {
            $respuesta["message"] = "RESERVA No realizada. En este tipo de reserva no es posible seleccionar un invitado, por favor verifique. ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        //Fin Distrital

        //Arsa en sencillos y dobles no permitir seleccionar profesor
        if ($IDClub == 40 && $IDServicio == "3295" && $IDTipoReserva == 2366 && (empty($ListaAuxiliar) || $ListaAuxiliar == "[]")) {
            $respuesta["message"] = "No fue posible realizar la reserva, debe seleccionar un profesor. ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        //Fin Arsa

        //Especial para Peñalisa solo 3 turnos por semana por accion

        if ($IDClub == 35 && empty($Admin) && ($IDServicio == "2536" || $IDServicio == "19114")) :
            $ReservasPermitidaSemana = 3;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            // Valido tambien los de la misma acción
            //$accion_padre = $dbo->getFields( "Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'" );
            //$accion_socio = $dbo->getFields( "Socio", "Accion", "IDSocio = '" . $IDSocio . "'" );
            $accion_padre = $datos_socio["AccionPadre"];
            $accion_socio = $datos_socio["Accion"];

            if (empty($accion_padre)) : // Es titular
                $array_socio[] = $IDSocio;
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;
            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $mensaje_reserva = " por semana";
            //Consulto la de mañana en adelante
            $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ")  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por semana por accion ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        //Fin especial puerto peñalisa

        //Especial para Campin solo 2 turnos por semana por accion
        /*
        if ( $IDClub == 96 && empty( $Admin ) && ($IDServicio == "15722")):
        $ReservasPermitidaSemana = 2;
        $condicion_reserva_verif = " and Tipo <> 'Automatica'";

        $fecha_hoy_semana = date( "Y-m-d" );
        $hora_hoy_semana = date( "H:i:s" );
        $year = date( 'Y', strtotime( $Fecha ) );
        $week = date( 'W', strtotime( $Fecha ) );
        $dia_reserva_atc = date("w",strtotime($Fecha)) ;
        $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
        $fecha_lunes = $fechaInicioSemana; //Lunes
        $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
        // Valido tambien los de la misma acción
        //$accion_padre = $dbo->getFields( "Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'" );
        //$accion_socio = $dbo->getFields( "Socio", "Accion", "IDSocio = '" . $IDSocio . "'" );
        $accion_padre = $datos_socio["AccionPadre"];
        $accion_socio = $datos_socio["Accion"];

        if ( empty( $accion_padre ) ): // Es titular
        $array_socio[] = $IDSocio;
        $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
        $result_nucleo = $dbo->query( $sql_nucleo );
        while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
        endwhile;
        else :
        $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
        $result_nucleo = $dbo->query( $sql_nucleo );
        while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
        endwhile;
        endif;

        $datos_invitado = json_decode( $Invitados, true );
        if ( count( $datos_invitado ) > 0 ){
        foreach ( $datos_invitado as $detalle_datos ){
        $IDSocioInvitado = $detalle_datos[ "IDSocio" ];
        if ( !empty( $IDSocioInvitado ) ){
        $array_socio[] = $IDSocioInvitado;
        }
        }
        }

        if ( count( $array_socio ) > 0 ):
        $id_socio_nucleo = implode( ",", $array_socio );
        endif;
        $fecha_inicio_valida = $fechaInicioSemana; //Lunes
        $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
        $mensaje_reserva=" por semana";
        //Consulto la de mañana en adelante
        $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ")  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
        $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

        if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
        $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por semana por accion y para sus invitados ";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        endif;
        endif;
         */
        //Fin campin

        //Especial para sabana solo dos turnos por fin semana
        /*
        if ( $IDClub == 95 && empty( $Admin ) ):

        if ( $IDServicio == "15964" || $IDServicio == "16047" ): // tenis y clase tenis hasta 2

        $condicion_reserva_verif = " and Tipo <> 'Automatica'";
        $fecha_hoy_semana = date( "Y-m-d" );
        $hora_hoy_semana = date( "H:i:s" );
        $year = date( 'Y', strtotime( $Fecha ) );
        $week = date( 'W', strtotime( $Fecha ) );
        $dia_reserva_atc = date("w",strtotime($Fecha)) ;
        $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
        $fecha_lunes = $fechaInicioSemana; //Lunes
        $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
        $accion_padre = $datos_socio["AccionPadre"];
        $accion_socio = $datos_socio["Accion"];

        if ( empty( $accion_padre ) ): // Es titular
        $array_socio[] = $IDSocio;
        $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
        $result_nucleo = $dbo->query( $sql_nucleo );
        while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
        endwhile;
        else :
        $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
        $result_nucleo = $dbo->query( $sql_nucleo );
        while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
        endwhile;
        endif;

        if ( count( $array_socio ) > 0 ):
        $id_socio_nucleo = implode( ",", $array_socio );
        endif;

        if( (int)$dia_reserva_atc>=1 && (int)$dia_reserva_atc<=5){
        $fecha_inicio_valida = $fechaInicioSemana; //Lunes
        $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 4 day' ) ); //Viernes
        $mensaje_reserva=" entre semana";
        $ReservasPermitidaSemana = 20;

        }
        else{
        $ReservasPermitidaSemana=1;
        if($dia_reserva_atc==0){
        $fecha_inicio_valida = date("Y-m-d",strtotime($Fecha."- 1 days"));
        }
        $fecha_fin_valida = $Fecha; //Domingo
        $mensaje_reserva=" los fines de semana";
        }

        //Consulto la de mañana en adelante
        $sql_revisar="Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
        $sql_reservas_sem = $dbo->query( $sql_revisar );
        $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

        if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
        $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas " . $mensaje_reserva;
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        endif;

        endif;
        endif;
         */

        ///Especial atc no puede tener dos turnos el mismo dia
        if ($IDClub == 26 && empty($Admin)) {
            if ($IDServicio == "1447" || $IDServicio == "2719" || $IDServicio == "2720" || $IDServicio == "5039") {

                if (!empty($IDBeneficiario)) :
                    $accion_padre = $datos_beneficiario["AccionPadre"];
                    $accion_socio = $datos_beneficiario["Accion"];
                else :
                    $accion_padre = $datos_socio["AccionPadre"];
                    $accion_socio = $datos_socio["Accion"];
                endif;

                if (empty($accion_padre)) : // Es titular
                    $array_socio[] = $IDSocio;
                    $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                    $result_nucleo = $dbo->query($sql_nucleo);
                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                        $array_socio[] = $row_nucleo["IDSocio"];
                    endwhile;
                else :
                    $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                    $result_nucleo = $dbo->query($sql_nucleo);
                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                        $array_socio[] = $row_nucleo["IDSocio"];
                    endwhile;
                endif;
                if (count($array_socio) > 0) :
                    $id_socio_nucleo = implode(",", $array_socio);
                endif;

                $ServiciosValidar = "5039,2719,2720,1447";
                $CantidaMaxima = 1;
                if (!empty($IDBeneficiario)) {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario' or IDSocio = '$IDBeneficiario' ) and (Fecha = '$Fecha' ) and IDServicio in ($ServiciosValidar)  and IDEstadoReserva = '1' AND Tipo <> 'Automatica' AND Cumplida <> 'S'";
                } else {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( (IDSocio = $IDSocio AND IDSocioBeneficiario = '$IDSocioBeneficiario') or IDSocioBeneficiario = '$IDSocio' ) and (Fecha='$Fecha') and IDServicio in ($ServiciosValidar) and IDEstadoReserva = '1' AND Tipo <> 'Automatica' AND Cumplida <> 'S'";
                }
                $r_reserva_otro = $dbo->query($sql_reserva_otro);
                $Reservas = $dbo->rows($r_reserva_otro);

                $mensaje_cruce = "Lo sentimos no puede tener 2 reservas activas en los servicios de Tenis el mismo día";

                if ($Reservas >= $CantidaMaxima) {
                    $respuesta["message"] = $mensaje_cruce;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $datos_invitado = json_decode($Invitados, true);
                if (count($datos_invitado) > 0) {
                    $ReservasPermitidaSemana = 1;
                    foreach ($datos_invitado as $detalle_datos) {
                        $total_reservas_semana_inv = 0;
                        $total_reservas_dia_hora = 0;
                        $IDSocioInvitado = $detalle_datos["IDSocio"];
                        if (!empty($IDSocioInvitado)) {

                            //Consulto las de hoy pasada la hora actual
                            $sql_reservas_sem = $dbo->query("SELECT IDReservaGeneral From ReservaGeneral Where ( (IDSocio = $IDSocioInvitado AND IDSocioBeneficiario = '$IDSocioInvitado') or IDSocioBeneficiario = '$IDSocioInvitado' ) and (Fecha='$Fecha') and IDServicio in ($ServiciosValidar) and IDEstadoReserva = '1' AND Tipo <> 'Automatica' AND Cumplida <> 'S'");
                            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                            //Consulto la de mañana en adelante
                            $sql_reservas_sem = $dbo->query("SELECT IDReservaGeneral From ReservaGeneral Where ( (IDSocio = $IDSocioInvitado AND IDSocioBeneficiario = '$IDSocioInvitado') or IDSocioBeneficiario = '$IDSocioInvitado' ) and (Fecha='$Fecha') and IDServicio in ($ServiciosValidar) and IDEstadoReserva = '1' AND Tipo <> 'Automatica' AND Cumplida <> 'S'");
                            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

                            if ((int) $total_reservas_semana_inv >= $ReservasPermitidaSemana) {
                                $respuesta["message"] = "Lo sentimos el invitado ya tiene " . $ReservasPermitidaSemana . " reservas activas";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                    }
                }
            }
        }
        ///FIN Especial atc no puede tener dos turnos el mismo dia



        ///Especial atc si reserva clase ya debe tener una cancha
        if ($IDClub == 26 && empty($Admin)) {
            if ($IDServicio == "1434") {
                if (!empty($IDBeneficiario)) {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDSocio = '" . $IDBeneficiario . "' ) and (Fecha='" . $Fecha . "' and Hora = '" . $Hora . "' ) and IDServicio in (2719,2720)  and IDEstadoReserva = '1' ";
                } else {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( (IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "') or IDSocioBeneficiario='" . $IDSocio . "' ) and (Fecha='" . $Fecha . "' and Hora = '" . $Hora . "' ) and IDServicio in (2719,2720) and IDEstadoReserva = '1' ";
                }
                $mensaje_cruce = "Primero debe tener una reserva de cancha antes de hacer la reserva de clase";
                $r_reserva_otro = $dbo->query($sql_reserva_otro);
                if ($dbo->rows($r_reserva_otro) <= 0) {
                    $respuesta["message"] = $mensaje_cruce;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }

            /*
            if ($IDServicio == "5039" || $IDServicio == "2719") {
                if (!empty($IDBeneficiario)) {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDSocio = '" . $IDBeneficiario . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio in (5039,2719)  and IDEstadoReserva = '1' ";
                } else {
                    $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( (IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "') or IDSocioBeneficiario='" . $IDSocio . "' ) and (Fecha='" . $Fecha . "'  ) and IDServicio in (5039,2719) and IDEstadoReserva = '1' ";
                }
                $mensaje_cruce = "Lo sentimos ya tiene una reserva en dobles o campos";
                $r_reserva_otro = $dbo->query($sql_reserva_otro);
                if ($dbo->rows($r_reserva_otro) > 0) {
                    $respuesta["message"] = $mensaje_cruce;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
            */
        }

        // ESPECIA CAMPESTRE PEREIRA PARA RESEVAR EN TRANKMAN DEBE TENER RESERVA DE PROFESOR DE GOLF
        if ($IDClub == 15 && $IDServicio == 4366) :

            if (!empty($IDBeneficiario)) {
                $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario' or IDSocio = '$IDBeneficiario' ) and (Fecha='$Fecha' and Hora = '$Hora' ) and IDServicio = 12789  and IDEstadoReserva = '1' ";
            } else {
                $sql_reserva_otro = "SELECT IDReservaGeneral From ReservaGeneral Where (IDSocio = '$IDSocio' or IDSocioBeneficiario='$IDSocio') and (Fecha='$Fecha' and Hora = '$Hora' ) and IDServicio = 12789 and IDEstadoReserva = '1' ";
            }
            $mensaje_cruce = "Primero debe tener una reserva con un profesor de golf.";
            $r_reserva_otro = $dbo->query($sql_reserva_otro);
            if ($dbo->rows($r_reserva_otro) <= 0) {
                $respuesta["message"] = $mensaje_cruce;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        endif;
        ///Especial atc si reserva clase ya debe tener una cancha

        /*
        ///Especial atc si reserva clase o cancha no debe tener escuela
        if ( $IDClub == 26 && empty( $Admin )) {
        if ( $IDServicio == "1484" || $IDServicio == "1490" || $IDServicio == "2106" || $IDServicio == "2109" || $IDServicio == "2110" || $IDServicio == "4350" || $IDServicio == "5035"
        || $IDServicio == "2106" || $IDServicio == "5039" || $IDServicio == "7973"){
        $sql_reserva_otro="";
        $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."'  and IDServicio in (1446) and IDEstadoReserva=1 ";
        $mensaje_cruce="Ya tienen una reserva de cancha o clase, no puede inscribirse a una escuela";
        if(!empty($sql_reserva_otro)){
        $r_reserva_otro=$dbo->query($sql_reserva_otro);
        if($dbo->rows($r_reserva_otro)>0){
        $respuesta[ "message" ] = $mensaje_cruce;
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        }
        }
        }
        elseif($IDServicio == "1446"){
        $sql_reserva_otro="";
        $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and IDServicio in (1484,1490,2106,2109,2110,5035,2106,5039,7973) and IDEstadoReserva=1 ";
        $mensaje_cruce="Ya tienen una reserva de escuela, no puede reservar una cancha o clase";
        if(!empty($sql_reserva_otro)){
        $r_reserva_otro=$dbo->query($sql_reserva_otro);
        if($dbo->rows($r_reserva_otro)>0){
        $respuesta[ "message" ] = $mensaje_cruce;
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        }
        }

        }
        }
        ///Especial atc si reserva clase ya debe tener una cancha
         */

        //Especial comercio pereira el lunes puede hacer la reserva de maximo 3 turnos por accion con un mismo Profesor despues del lunes si puede hacer las que quiera
        $dia_actual_app = date("N");
        if ($IDClub == 48 && empty($Admin) && $dia_actual_app == 1) {
            if ($IDServicio == "4433" || $IDServicio == "4514" || $IDServicio == "7768" || $IDServicio == "4434" || $IDServicio == "4499") { // tenis y clase tenis hasta 3
                $ReservasPermitidaSemana = 6;
            } else {
                $ReservasPermitidaSemana = 100;
            }

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            // Valido tambien los de la misma acción
            $accion_padre = $datos_socio["AccionPadre"];
            $accion_socio = $datos_socio["Accion"];
            if (empty($accion_padre)) : // Es titular
                $array_socio[] = $IDSocio;
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;
            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;

            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $mensaje_reserva = " semana";

            //Consulto las de hoy pasada la hora actual
            $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento = '" . $IDElemento . "' " . $condicion_reserva_verif);
            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante
            $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento = '" . $IDElemento . "' " . $condicion_reserva_verif);
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permite crear el dia de hoy: " . $ReservasPermitidaSemana . " reservas por accion ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        }


        // VALIDACIONES ESPECIALES PARA URUGUAY CON INVIACIONES EXTERNAS
        if ($IDClub == 125 && ($IDServicio == 23059 || $IDServicio == 22972 || $IDServicio == 23009 || $IDServicio == 23013)) :
            // PARA URUGUAY SE VALIDA QUE EL INVITADO NO SOCIO NO HAYA SIDO INGRESADO POR OTRO SOCIO EN EL MISMO MES
            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :

                foreach ($datos_invitado as $detalle_datos) :

                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                    $CorreoSocioInvitado = $detalle_datos["Correo"];

                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S") :
                        // VALIDO QUE EL SOCIO NO HAYA INVITADO A UN NO SOCIO EL MISMO MES
                        $validar = SIMServicioReserva::valida_invitaciones_socio_uruguay($IDSocio, $Fecha);
                        if (!$validar["success"]) :
                            $respuesta["message"] = $validar["message"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        $Validacion = SIMServicioReserva::valida_invitados_uruguay($IDSocio, $CedulaSocioInvitado, $NombreSocioInvitado, $Fecha);
                        if (!$Validacion["success"]) :
                            $respuesta["message"] = $Validacion["message"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                    endif;
                endforeach;
            endif;
        endif;

        if ($IDClub == 141) :
            // PARA LGUNITA SE DEBE TENER LA CEDULA Y CON MINIMO 6 CARACTERES
            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :
                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S") :
                        if (trim($CedulaSocioInvitado) == "") :
                            $respuesta["message"] = "La cedula debe tener datos, no se puede enviar vacia.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        if (strlen(trim($CedulaSocioInvitado)) < 6) :
                            $respuesta["message"] = "La cedula debe tener minimo 6 digitos";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;

        // VALIDACION URUGUAY INVITADOS EXTERNOS LA CEDULA NO PUEDE TENER CARACTERES SOLO NUMEROS Y LETRAS
        if ($IDClub == 125) :
            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :

                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                    $CorreoSocioInvitado = $detalle_datos["Correo"];

                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S") :
                        // VALIDO QUE EL SOCIO NO HAYA INVITADO A UN NO SOCIO EL MISMO MES
                        $validar = SIMServicioReserva::valida_cedula_invitado_externo($CedulaSocioInvitado, $NombreSocioInvitado);
                        if (!$validar["success"]) :
                            $respuesta["message"] = $validar["message"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                    endif;
                endforeach;
            endif;
        endif;

        // VALIDACION ESPECIAL CANTEGRIL
        if ($IDClub == 185 && $IDServicio == 36930) :
            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :

                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                    $CorreoSocioInvitado = $detalle_datos["Correo"];

                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S") :
                        // VALIDO QUE EL SOCIO NO HAYA INVITADO A UN NO SOCIO EL MISMO MES
                        $validar = SIMServicioReserva::valida_invitados_cantegril_uruguay($IDSocio, $CedulaSocioInvitado, $NombreSocioInvitado, $Fecha);
                        if (!$validar["success"]) :
                            $respuesta["message"] = $validar["message"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                    endif;
                endforeach;
            endif;
        endif;

        //verificar del country si el socio es tipo invitado o caje y reserva cuando no puede no lo deje.
        if ($IDClub == 44) {
            $FechaInicoInvitado = $datos_socio["FechaInicioInvitado"];
            $FechaFinInivtado = $datos_socio["FechaFinInvitado"];
            $FechaInicoCanje = $datos_socio["FechaInicioCanje"];
            $FechaFinCanje = $datos_socio["FechaFinCanje"];

            if ($datos_socio['TipoSocio'] == "Invitado" && ($Fecha < $FechaInicoInvitado || $Fecha > $FechaFinInivtado)) {
                $respuesta["message"] = "Lo sentimos las fechas para tomar reservas son de: " . $FechaInicioInvitado . " hasta " . $FechaFinInivtado;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if ($datos_socio['TipoSocio'] == "Canje" && ($Fecha < $FechaInicoCanje || $Fecha > $FechaFinCanje)) {
                $respuesta["message"] = "Lo sentimos las fechas para tomar reservas son de: " . $FechaInicoCanje . " hasta " . $FechaFinCanje;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        // PARA CANJES NACIONALES SOLO SE PUEDE RESERVAR ENTRE SEMANA SOLO PARA EL COUNTRY BOGOTA        
        if ($IDClub == 44 && empty($Admin) && $datos_socio[TipoSocio] == "Canje" && ($datos_socio[TipoCanje] == "Nacional" || $datos_beneficiario[TipoCanje] == "Nacional")) :
            $dia_reserva = date("w", strtotime($Fecha));
            if (($dia_reserva == 6 || $dia_reserva == 0) && $Hora <= "12:00:00" && ($IDServicio == 12053 || $IDServicio == 3866 || $IDServicio == 3888 || $IDServicio == 3889 || $IDServicio == 3941 || $IDServicio == 12056)) :
                //$respuesta["message"] = "Lo sentimos, eres un canje nacional y no tienes habilitadas las reservas lo fines de semana";
                $respuesta["message"] = "Lo sentimos, eres un canje nacional y solo puedes reservar turnos despues de las 12";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

        /*
            // PARA CANJES DEL CAMPESTRE CALI, LLANO GRANDE MEDELLIN, y CAMPOESTRE MEDELLIN
            if ($datos_socio[ClubCanje] != 106 || $datos_socio[ClubCanje] != 104):
                $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$Fecha' and IDPais = 1");
                if (($dia_reserva == 6 || $dia_reserva == 0 || ($dia_reserva == 1 && empty($IDFestivo))) && ($IDServicio != 3888 || $IDServicio != 3889) && ($Hora >= "12:00:00")):
                    $respuesta["message"] = "Lo sentimos, eres un canje nacional y no tienes habilitadas las reservas entre semana";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endif;
            */
        endif;



        //Especial para Country solo dos turnos entre semana y 2 fin de semana
        if ($IDClub == 44 && empty($Admin) && ($IDServicio == "3908" || $IDServicio == "18257")) :

            $ReservasPermitidaSemana = 5;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            // $fecha
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = $fecha_domingo;
            $mensaje_reserva = " entre semana";

            //Consulto las de hoy pasada la hora actual ENTRE SEMANA
            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") ) and (Fecha= '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            $total_reservas_semana = $dbo->rows($sql_reservas_sem);
            //Consulto la de mañana en adelante
            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") ) and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //$sql_reservas_sem = $dbo->query( "SELECT * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activas  " . $mensaje_reserva;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        /*  */

        // PARA EL COUNTRY BOGOTA EN SALON DE BELLEZA SOLO SE PUEDE RESERVAR 4 VECES AL MES
        /* if ($IDClub == 44 && ($IDServicio == 3905)):

            $ReservasPosibles = 4;
            $condicion_reserva_verif = " AND Tipo <> 'Automatica'";
            $IDServicioValidar = "3905";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $mes = date('m', strtotime($Fecha));

            if (!empty($IDBeneficiario)) {
                $SQLReservas = "SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario' OR IDSocio = '$IDBeneficiario') AND (MONTH(Fecha) = '$mes' AND Fecha > '$fecha_hoy_semana') and IDServicio IN ('$IDServicioValidar') and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            } else {
                $SQLReservas = "SELECT * From ReservaGeneral Where ( IDSocio = '$IDSocio' AND IDSocioBeneficiario = 0 ) AND (MONTH(Fecha) = '$mes' AND Fecha > '$fecha_hoy_semana') and IDServicio IN ('$IDServicioValidar') and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            }
            $sql_reservas_sem = $dbo->query($SQLReservas);
            $Reservas = $dbo->rows($sql_reservas_sem);

            if ($Reservas >= $ReservasPosibles):

                $respuesta["message"] = "Lo sentimos solo estan permitidas 4 reservas en un mismo mes.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif; */

        //Especial para Country solo dos turnos entre semana y 2 fin de semana
        if ($IDClub == 44 && empty($Admin) && ($IDServicio == "3941" || $IDServicio == "3861" /* || $IDServicio == "9965" */ /* || $IDServicio == "3905" */)) :
            if ($IDServicio == "3941" || $IDServicio == "3861") : // tenis y clase tenis hasta 2
                $ReservasPermitidaSemana = 2;
                // $condicion_reserva_verif = " and Tipo <> 'Automatica' and Cumplida <> 'S'";
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            elseif ($IDServicio == "9965" && $IDElemento == "8032") : //GISMASIO
                $ReservasPermitidaSemana = 10000;
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            elseif ($IDServicio == "3905") : //SALON DE BELLEZA
                $ReservasPermitidaSemana = 10000;
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
            else : // las demas
                $ReservasPermitidaSemana = 100;
                $condicion_reserva_verif = "";
            endif;

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva = date("w", strtotime($Fecha));
            $dia = date("w");
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

            //si el proximo Lunes es festivo se permite 3 reservas el fin de semana
            $proximo_Lunes = strtotime('next Monday');
            $fecha_prox_lunes = date('Y-m-d', $proximo_Lunes);
            $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $fecha_prox_lunes . "' and IDPais = 1");

            // SI EL DIA DE RESERVA ES SABADO O DOMINGO VALIDAMOS SOLO FIN DE SEMANA Y FESTIVO
            if ((int) $dia_reserva == 6 || (int) $dia_reserva == 0 || ($IDFestivo > 0 && (int) $dia_reserva == 1)) {

                $proximo_sabado = strtotime('next Saturday');
                $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
                $mensaje_reserva = " los fines de semana!";

                if (!empty($IDFestivo)) {

                    $ReservasPermitidaSemana = 3;
                    $sabado_pasado = strtotime('last Saturday');
                    $fecha_inicio_valida = date('Y-m-d', $sabado_pasado);
                    $fecha_fin_valida = $fecha_prox_lunes;
                }
            } else {
                // VALIDO QUE EL LUNES NO SEA FESTIVO
                $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $fechaInicioSemana . "' and IDPais = 1");

                if ($IDFestivo > 0) {

                    // SI LA RESERVA SE HIZO EL MISMO LUNES Y ES FESTIVO DEBEMOS VALIDAR PARA EL PASADO
                    if ($dia == 1 && $dia_reserva == 1) :

                        $ReservasPermitidaSemana = 2;
                        $sabado_pasado = strtotime('last Saturday');
                        $fecha_inicio_valida = date('Y-m-d', $sabado_pasado);
                        $fecha_fin_valida = $fechaInicioSemana;
                    else :
                        $fecha_inicio_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 1 day'));
                        $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 4 day')); //Viernes

                    endif;
                } else {
                    $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                    $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 4 day')); //Viernes
                }
                $mensaje_reserva = " entre semana";
            }

            if ($fecha_inicio_valida <= $Fecha) {
                $CondHora = " and Hora >= '" . date("H:i:s") . "' ";
            }

            //Consulto las de hoy pasada la hora actual
            if (!empty($IDBeneficiario)) {
                $sql_sem = "Select * From ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' ) and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;

                $sql_reservas_sem = $dbo->query($sql_sem);
            } else {
                $sql_sem = "Select * From ReservaGeneral Where  ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )   and (Fecha= '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva= '" . $IDTipoReserva . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_reservas_sem = $dbo->query($sql_sem);
            }

            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante
            if (!empty($IDBeneficiario)) {
                $sql_sem2 = "Select * From ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' )  and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_reservas_sem = $dbo->query($sql_sem2);
            } else {
                $sql_sem2 = "Select * From ReservaGeneral Where  ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )  and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva = '" . $IDTipoReserva . "'and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_reservas_sem = $dbo->query($sql_sem2);
            }

            //$sql_reservas_sem = $dbo->query( "SELECT * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            /*
            if($IDSocio==149891 || $IDSocio==135054){
            $respuesta["message"] = $sql_sem2 . " TOT SEM " .$total_reservas_semana;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            }
             */

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activas  " . $mensaje_reserva;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        //ESPECIAL PARA JACARANDA PARA LOS PLANES SOLO CIERTA CANTIDAD ENTRE SEMANA
        if ($IDClub == 55 && empty($Admin) && ($IDServicio == "5516" || $IDServicio == "5520" || $IDServicio == "5534")) :

            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            if ($IDServicio == "5516") :
                $ReservasPermitidaSemana = 6;
            elseif ($IDServicio == "5520") :
                $ReservasPermitidaSemana = 9;
            else :
                $ReservasPermitidaSemana = 15;
            endif;

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva = date("w", strtotime($Fecha));
            $dia = date("w");
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day'));

            $fecha_inicio_valida = $fechaInicioSemana; //LUNES INICIO DE SEMANA
            $fecha_fin_valida = $fecha_domingo; //DOMINGO AL FINAL DE LA SEMANA

            if (!empty($IDBeneficiario)) {
                $accion_padre = $datos_beneficiario["AccionPadre"];
                $accion_socio = $datos_beneficiario["Accion"];
            } else {
                $accion_padre = $datos_socio["AccionPadre"];
                $accion_socio = $datos_socio["Accion"];
            }

            if (empty($accion_padre)) :
                $array_socio[] = $IDSocio;
                $sql_nucleo = "SELECT IDSocio FROM Socio WHERE AccionPadre = '$accion_socio' AND IDClub = '$IDClub'";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "SELECT IDSocio FROM Socio WHERE (AccionPadre = '$accion_padre' OR Accion = '$accion_padre') AND IDClub = '$IDClub'";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;

            if (count($arr3ay_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;

            //Consulto las de hoy pasada la hora actual
            $sql_dia = "SELECT * FROM ReservaGeneral WHERE IDSocio IN ($id_socio_nucleo) AND (Fecha = '" . date("Y-m-d") . "' AND Hora >= '" . date("H:i:s") . "') AND (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' $condicion_reserva_verif";
            $sql_reservas_sem = $dbo->query($sql_dia);
            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante
            $sql_sema = "SELECT * FROM ReservaGeneral WHERE IDSocio IN ($id_socio_nucleo) AND Fecha > '" . date("Y-m-d") . "'  AND (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' $condicion_reserva_verif";
            $sql_reservas_sem = $dbo->query($sql_sema);

            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten  $ReservasPermitidaSemana reservas activas por nuclo familiar para este servicio";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        if ($IDClub == 44 && empty($Admin) && $IDServicio == "12053") :

            $ReservasPermitidaSemana = 2;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));

            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Viernes

            //Consulto las de hoy pasada la hora actual ENTRE SEMANA
            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = 0 ) and (Fecha= '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }
            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            //Consulto la de mañana en adelante
            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = 0 ) and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activas  " . $mensaje_reserva;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        //Especial country si reserva en un campo de golf ya no puede reservar en el otro
        /* if ($IDClub == 44 && ($IDServicio == "3888" || $IDServicio == "3889")) {
        $IDServValidar = "3888,3889";
        if (!empty($IDBeneficiario)) {
        $sql_reservas_rep = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServValidar . ") and IDEstadoReserva = '1' AND Tee = '$Tee'");
        } else {
        $sql_reservas_rep = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServValidar . ") and IDEstadoReserva = '1' AND Tee = '$Tee'");
        }
        $total_reservas_rep += (int) $dbo->rows($sql_reservas_rep);
        if ((int) $total_reservas_rep >= 1):
        $respuesta["message"] = "Lo sentimos ya tiene una reserva por otro campo en la misma fecha";
        $respuesta["success"] = false;
        $respuesta["response"] = null;
        return $respuesta;
        endif;
        } */
        if ($IDClub == 44 && ($IDServicio == "3888" || $IDServicio == "3889")) {
            $IDServValidar = "3888,3889";
            if (!empty($IDBeneficiario)) {
                $sql_reservas_rep = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServValidar . ") and IDEstadoReserva = '1' AND Tee = 'Tee1'");
            } else {
                $sql_reservas_rep = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServValidar . ") and IDEstadoReserva = '1' AND Tee = 'Tee1'");
            }
            $total_reservas_rep += (int) $dbo->rows($sql_reservas_rep);
            if ((int) $total_reservas_rep >= 1) :
                $respuesta["message"] = "Lo sentimos ya tiene una reserva por otro campo en la misma fecha";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        }

        //Especial atc para Iluminados y Exteriores los sabados, Domingos solo se puede reservar por app hasta las 7am
        if ($IDClub == 26 && empty($Admin)) :
            $dia_semana_reserva = date("w", strtotime($Fecha));
            if (date("H:i:s") >= "07:00:00" && ((($IDServicio == "1490" || $IDServicio == "2109") && ($dia_semana_reserva == "6" || $dia_semana_reserva == "0")))) :
            //$respuesta[ "message" ] = "Lo sentimos solo se permiten reservar por el app hasta las 7am para este dia ";
            //$respuesta[ "success" ] = false;
            //$respuesta[ "response" ] = NULL;
            //return $respuesta;
            endif;
        endif;
        //FIN ESPECIAL atc

        //ESPECIAL CLUB DE GOLF CUCUTA PARA NO TENER MAS DE 3 ENTRE SEMANA EN TENIS
        if ($IDClub == 155 && empty($Admin) && $IDServicio == "30843" && (($Hora >= "06:00:00" && $Hora <= "07:00:00") || ($Hora >= "18:00:00" && $Hora <= "19:00:00"))) :

            $condicion_reserva_verif = " AND Tipo <> 'Automatica'";
            $ReservasPermitidaSemana = 3;
            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva = date("w", strtotime($Fecha));
            $dia = date("w");
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

            // SOLO SE VALLIDA ENTRE SEMANA
            if ((int) $dia_reserva >= 1 && (int) $dia_reserva <= 5) {

                $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' +4 day')); //Viernes

                $mensaje_reserva = " entre semana";
            }

            //Consulto las de hoy pasada la hora actual
            $CondicionHora = "AND ((Hora >= '06:00:00' AND Hora <= '07:00:00') || (Hora >= '18:00:00' AND Hora <= '19:00:00'))";

            if (!empty($IDBeneficiario)) {
                // $sql_sem="SELECT * FROM ReservaGeneral WHERE (IDSocioBeneficiario = '$IDBeneficiario') AND (Fecha='".date("Y-m-d")."' $CondicionHora) and (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_sem = "SELECT * FROM ReservaGeneral WHERE (IDSocioBeneficiario = '$IDBeneficiario') $CondicionHora and (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_reservas_sem = $dbo->query($sql_sem);
            } else {
                // $sql_sem="SELECT * From ReservaGeneral Where  (IDSocio = '$IDSocio' and IDSocioBeneficiario = 0)  and (Fecha= '".date("Y-m-d")."' $CondicionHora) and (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_sem = "SELECT * From ReservaGeneral Where  (IDSocio = '$IDSocio' and IDSocioBeneficiario = 0)  $CondicionHora and (Fecha >= '$fecha_inicio_valida' and Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
                $sql_reservas_sem = $dbo->query($sql_sem);
            }

            $total_reservas_semana = $dbo->rows($sql_reservas_sem);

            /* //Consulto la de mañana en adelante
            if (!empty($IDBeneficiario)) {
            $sql_sem2="SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario') and  (Fecha > '".date("Y-m-d")."' $CondicionHora) AND (Fecha >= '$fecha_inicio_valida' AND Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql_sem2);
            } else {
            $sql_sem2="SELECT * From ReservaGeneral Where (IDSocio = '$IDSocio' and IDSocioBeneficiario = 0 )  and  (Fecha > '".date("Y-m-d")."' $CondicionHora) AND (Fecha >= '$fecha_inicio_valida' AND Fecha <= '$fecha_fin_valida') and IDServicio = '$IDServicio' and IDEstadoReserva = '1' " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql_sem2);
            } */

            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

        /*  if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana):
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activas  en horario de 6 am a 8 am" . $mensaje_reserva;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif; */
        endif;

        if ($IDClub == 155 && $IDServicio == 30811 && !empty($Admin)) :
            $HoraActual = date("H:i:s");
            if ($HoraActual < "17:00:00" or $HoraActual > "21:00:00") :
                $respuesta["message"] = "Lo sentimos, por el administrador solo es posible hacer reservas entre las 5 pm y las 9 pm";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        if ( /* ($IDClub == 8  && $IDServicio == 12023) ||  */($IDClub == 44 && $IDServicio == 11242)) :
            $dia_semana_reserva = date("w", strtotime($Fecha));
            $dia = date("w");
            $hora_dia = date("H:i:s");
            if ($dia_semana_reserva >= 2 && $dia_semana_reserva <= 5) {
                if ($dia_semana_reserva == $dia) {
                    if (($hora_dia >= "12:00:00")) {
                        $respuesta["message"] = "Lo sentimos, solo puede reservar hasta antes de las 12 m";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
            }
        endif;

        //especual country solo 1 por semana en 3 a 6 años equitacion
        if ( /* ($IDClub == 8  && $IDServicio == 31) ||  */($IDClub == 44 && $IDServicio == 11242 && $IDTipoReserva == 2394) && empty($Admin)) :
            $ReservasPermitidaSemana = 1;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $mensaje_reserva = " por semana";

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where  IDSocio = '" . $IDSocio . "'  and (Fecha= '" . date("Y-m-d") . ") and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva= '" . $IDTipoReserva . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //Consulto las de hoy pasada la hora actual
            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            //$total_reservas_semana = $dbo->rows( $sql_reservas_sem );

            //Consulto la de mañana en adelante

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where  IDSocio = '" . $IDSocio . "'  and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva = '" . $IDTipoReserva . "'and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reserva activa ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        if ( /* ($IDClub == 8  && $IDServicio == 31) ||  */($IDClub == 44 && $IDServicio == 11242 && $IDTipoReserva == 2401)) :
            $ReservasPermitidaSemana = 2;
            $condicion_reserva_verif = " and Tipo <> 'Automatica'";

            $fecha_hoy_semana = date("Y-m-d");
            $hora_hoy_semana = date("H:i:s");
            $year = date('Y', strtotime($Fecha));
            $week = date('W', strtotime($Fecha));
            $dia_reserva_atc = date("w", strtotime($Fecha));
            $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $fecha_lunes = $fechaInicioSemana; //Lunes
            $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
            $mensaje_reserva = " por semana";

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where  IDSocio = '" . $IDSocio . "'  and (Fecha= '" . date("Y-m-d") . ") and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva= '" . $IDTipoReserva . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //Consulto las de hoy pasada la hora actual
            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            //$total_reservas_semana = $dbo->rows( $sql_reservas_sem );

            //Consulto la de mañana en adelante

            if (!empty($IDBeneficiario)) {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            } else {
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where  IDSocio = '" . $IDSocio . "'  and  (Fecha > '" . date("Y-m-d") . "' OR Fecha < '" . date("Y-m-d") . "')  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDServicioTipoReserva = '" . $IDTipoReserva . "'and IDEstadoReserva = '1' " . $condicion_reserva_verif);
            }

            //$sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $IDSocio . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
            $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

            if (((int) $total_reservas_semana - 1) >= (int) $ReservasPermitidaSemana) :
                $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reserva activa ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;



        //Especial Country para reservas de 6am y 7am  solo hasta las 8pm del dia anterior
        if (($IDClub == 44 || $IDClub == 8) && empty($Admin)) :
            $dia_manana = date('Y-m-d', time() + 84600);
            $fecha_hoy_v = date("Y-m-d");
            if (((date("H:i:s") >= "20:00:00" && $dia_manana == $Fecha) || $fecha_hoy_v == $Fecha) && ($IDServicio == "3941" || $IDServicio == "36") && ($Hora == '06:00:00' || $Hora == '07:00:00') && (!empty($ListaAuxiliar) && $ListaAuxiliar != "[]")) :
                $respuesta["message"] = "Lo sentimos solo se permiten reservar con profesor/monitor por el app hasta antes de las 8pm para turnos de 6am y 7am " . $ListaAuxiliar;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        // ESPECIAL PARA LIGA DE RISARALDA, RESERVAS CON PROFESOR PARA TURNOS DE LA MAÑANA SOLO HASTA LAS 8 PM DEL DIA ANTERIOR
        if ($IDClub == 85 && empty($Admin)) :
            $dia_manana = date('Y-m-d', time() + 84600);
            $fecha_hoy_v = date("Y-m-d");
            if (((date("H:i:s") >= "20:00:00" && $dia_manana == $Fecha) || $fecha_hoy_v == $Fecha) && ($IDServicio == "12718") && ($Hora == '06:00:00' || $Hora == '07:00:00') && (!empty($ListaAuxiliar) && $ListaAuxiliar != "[]")) :
                $respuesta["message"] = "Lo sentimos solo se permiten reservar con profesor/monitor por el app hasta antes de las 8pm para turnos de 6am y 7am " . $ListaAuxiliar;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;
        endif;

        $dia_reserva_lagartos = date("w", strtotime($Fecha));
        if (($IDClub == 7 && $IDServicio == 629) && empty($Admin) && ($dia_reserva_lagartos >= 2 || $dia_reserva_lagartos <= 2)) :
            $dia_manana = date('Y-m-d', time() + 84600);
            if (((date("H:i:s") >= "20:00:00" && $dia_manana == $Fecha))) :
                //verifico si ya tiene algo reservado antes de la hora si es asi no bloque los horarios
                $sql_reserva_man = "SELECT * FROM ReservaGeneral WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1)";
                $result_reserva_man = $dbo->query($sql_reserva_man);
                if ($dbo->rows($result_reserva_man) <= 0) {
                    $respuesta["message"] = "Lo sentimos solo se permite reservar hasta las 8pm";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            endif;
        endif;
        //FIN ESPECIAL country



        //Especial Country Sala belleza servicios que no se pueden cruzar
        if (($IDClub == 44) && empty($Admin)) :
            $sql_reserva_otro = "";
            switch ($IDServicio) {
                case "11734": //maquillaje
                    //no se puede tener otra reserva en el mismo horario
                    $sql_reserva_otro = "SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio in (3897,11732,11733,11736,12470) and IDEstadoReserva = 1";
                    $mensaje_cruce = "Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar maquillaje a la misma hora";
                    break;
                case "11736": //tratamientos quimicos
                    $sql_reserva_otro = "SELECT IDReservageneral FROM ReservaGeneral WHERE IDSocio ='" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio in (11732,11733,11734,11736,12470) and IDEstadoReserva = 1";
                    $mensaje_cruce = "Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar tratamiento quimicos a la misma hora";
                    break;
                case "11733": // corte
                case "11732": // cepillado
                    $sql_reserva_otro = "SELECT IDReservageneral FROM ReservaGeneral WHERE IDSocio ='" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio in (11736,11734) and IDEstadoReserva = 1 ";
                    $mensaje_cruce = "Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar peinado/corte/cepillado a la misma hora";
                    break;
            }

            if (!empty($sql_reserva_otro)) {
                $r_reserva_otro = $dbo->query($sql_reserva_otro);
                if ($dbo->rows($r_reserva_otro) >= 1) {
                    $respuesta["message"] = $mensaje_cruce;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        endif;
        //FIN ESPECIAL country

        //FIN ESPECIAL country

        //Especial Comercio pereira cuando se selecciona auxiliar solo permite cuando es 1 hora
        /*
        if ( ($IDClub == 48 ) ){
        if($ListaAuxiliar=="null")
        $ListaAuxiliar="";

        if ( ($IDServicio == "4514") && $IDTipoReserva != 881){
        if( (!empty( $ListaAuxiliar ) && $ListaAuxiliar!="[]")   ):
        $con_aux="S";
        elseif($IDAuxiliar!=""):
        $con_aux="S";
        endif;

        if($con_aux=="S"){
        $respuesta[ "message" ] = "Para reservas con monitor solo se pemite 1 hora no 2 horas. ";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        }
        }
        }
         */
        //FIN COMERCIO

        //Especial Lagartos la cancha nocturnas solo se puede hasta las 5:30pm
        if (($IDClub == 7 && $IDServicio == 221 && date("Y-m-d") == $Fecha && $Hora >= "18:00:00" && date("H:i:s") >= '17:30:00') && empty($Admin)) :
            $respuesta["message"] = "Lo sentimos solo se permiten reservar estas canchas antes de las 5:30pm";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        //if ( ($IDClub == 7 && $IDServicio==22 && $Hora>="05:00:00" && $Hora<="11:00:00" && date("H:i:s")<='04:59:00' ) && empty( $Admin ) ):
        if (($IDClub == 7 && ($IDServicio == 22 || $IDServicio == 19563) && $Hora >= "06:00:00" && $Hora <= "08:45:00" && (date("H:i:s") < '05:00:00' || date("Y-m-d") != $Fecha))) :
            $respuesta["message"] = "Lo sentimos los turnos entre las 6am y 8:15 am solo los puede reservar despues de las 5:00am del mismo dia ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        //Especial lagartos y rincon valido que solo tenga 1 turno en clase o cancha
        //if($IDClub == 7 && ($IDServicio==221 || $IDServicio==43 || $IDServicio==2321 || $IDServicio==12014 || $IDServicio==12015 || $IDServicio==12016 || $IDServicio==12017 || $IDServicio==12018 || $IDServicio==12019 || $IDServicio==12020 ) ){
        if (($IDClub == 7 || $IDClub == 10) &&
            ($IDServicio == 221 || $IDServicio == 43 || $IDServicio == 4960 || $IDServicio == 84 || $IDServicio == 225 || $IDServicio == 12018 || $IDServicio == 22)
        ) {

            if ($IDClub == 7 && ($IDServicio == 12018 || $IDServicio == 22)) {
                $IDServVal = "12018,22";
                $serviciolabel = " esqui ";
            } elseif ($IDClub == 7) {
                $IDServVal = "221,43";
                $serviciolabel = " tenis ";
            } elseif ($IDClub == 10) {
                $IDServVal = "84,225";
                $serviciolabel = " tenis ";
            }

            if ($Fecha == date("Y-m-d")) {
                $condicion_hora_un = " and Hora >= '" . date("H:i:s") . "' ";
            } else {
                $condicion_hora_un = "";
            }

            if ((int) $IDBeneficiario > 0) {
                $sql_valida_un = "SELECT * FROM ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' )  and Fecha= '" . $Fecha . "' and IDServicio in (" . $IDServVal . ")  and IDEstadoReserva = '1' " . $condicion_hora_un;
            } else {
                $sql_valida_un = "SELECT * FROM ReservaGeneral Where ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )  and Fecha= '" . $Fecha . "' and IDServicio in (" . $IDServVal . ") and IDEstadoReserva = '1' " . $condicion_hora_un;
            }
            $sql_reservas_un = $dbo->query($sql_valida_un);
            $total_reservas_dia_hora_un = $dbo->rows($sql_reservas_un);
            if ((int) $total_reservas_dia_hora_un > 0) {
                $respuesta["message"] = "LT. Lo sentimos solo puede tener una reserva activa en " . $serviciolabel . " al dia ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //FIN ESPECIAL Lagartos

        // Especial lagartos si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis y Esqui
        if ($IDServicio == 43 || $IDServicio == 221 || $IDServicio == 22 || $IDServicio == 629 || $IDServicio == 1072 || $IDServicio == 12014 || $IDServicio == 12015 || $IDServicio == 12016 || $IDServicio == 12017 || $IDServicio == 12018 || $IDServicio == 12019 || $IDServicio == 12020 || $IDServicio == 98 || $IDServicio == 99) {
            $bloqueo_incumplida = "N";
            $Hace8dias = strtotime('-7 day', strtotime($Fecha));
            $Hace8dias = date('Y-m-d', $Hace8dias);
            if ((int) $IDBeneficiario > 0) {
                $IDSocValidar = $IDBeneficiario;
            } else {
                $IDSocValidar = $IDSocio;
            }
            if ($IDServicio == 43 || $IDServicio == 221 || $IDServicio == 22 || $IDServicio == 629 || $IDServicio == 1072) {
                $sql_inc = "SELECT IDReservaGeneral,IDSocioBeneficiario,IDSocio FROM ReservaGeneral WHERE Fecha='" . $Hace8dias . "' and (Cumplida = 'N' or Cumplida = 'P' ) and (IDSocio = '" . $IDSocValidar . "' or IDSocioBeneficiario = '" . $IDSocValidar . "') and IDServicio in (43, 221, 629,1072)";
                $nombre_serv_bloq = " tenis";
            } elseif ($IDServicio == 98 || $IDServicio == 99) {
                $sql_inc = "SELECT IDReservaGeneral,IDSocioBeneficiario,IDSocio FROM ReservaGeneral WHERE Fecha='" . $Hace8dias . "' and (Cumplida = 'N' or Cumplida = 'P' ) and (IDSocio = '" . $IDSocValidar . "' or IDSocioBeneficiario = '" . $IDSocValidar . "') and IDServicio in (98,99)";
                $nombre_serv_bloq = " golf";
            } else {
                $sql_inc = "SELECT IDReservaGeneral,IDSocioBeneficiario,IDSocio FROM ReservaGeneral WHERE Fecha='" . $Hace8dias . "' and (Cumplida = 'N' or Cumplida = 'P' ) and (IDSocio = '" . $IDSocValidar . "' or IDSocioBeneficiario = '" . $IDSocValidar . "') and IDServicio in ($IDServicio)";
                $nombre_serv_bloq = " escuelas ";
            }

            $result_incumplidat = $dbo->query($sql_inc);
            if ($dbo->rows($result_incumplidat) > 0) {
                while ($row_datos_reserva_inc = $dbo->fetchArray($result_incumplidat)) {

                    if ($row_datos_reserva_inc["IDSocioBeneficiario"] > 0 && ($row_datos_reserva_inc["IDSocioBeneficiario"] == $IDSocio || $row_datos_reserva_inc["IDSocioBeneficiario"] == $IDBeneficiario)) {
                        $bloqueo_incumplida = "S";
                    } elseif ($row_datos_reserva_inc["IDSocioBeneficiario"] <= 0 && $row_datos_reserva_inc["IDSocio"] == $IDSocio) {
                        $bloqueo_incumplida = "S";
                    }

                    if ($bloqueo_incumplida == "S") {
                        $respuesta["message"] = "Lo sentimos, el dia " . $Hace8dias . " tiene una reserva incumplida, no puede tomar reservas el dia de hoy para " . $nombre_serv_bloq;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
            }
        }

        // FIN Especial lagartos si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis$Fecha = "2019-04-11";

        // Especial bogotatenis  si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis$Fecha = "2019-04-11";
        if ($IDServicio == 8539 || $IDServicio == 8649) {

            $bloqueo_incumplida = "N";
            $Hace8dias = strtotime('-7 day', strtotime($Fecha));
            $Hace8dias = date('Y-m-d', $Hace8dias);

            $accion_padre = $datos_socio["AccionPadre"];
            $accion_socio = $datos_socio["Accion"];
            if (empty($accion_padre)) : // Es titular
                $array_socio[] = $IDSocio;
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;
            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;

            $sql_inc = "SELECT IDReservaGeneral,IDSocioBeneficiario,IDSocio FROM ReservaGeneral WHERE Fecha='" . $Hace8dias . "' and (Cumplida = 'N' or Cumplida = 'P' ) and (IDSocio in ($id_socio_nucleo) or IDSocioBeneficiario in ('" . $id_socio_nucleo . "')) and IDServicio in (8539, 8649)";
            $result_incumplidat = $dbo->query($sql_inc);
            if ($dbo->rows($result_incumplidat) > 0) {
                $row_datos_reserva_inc = $dbo->fetchArray($result_incumplidat);
                if ($row_datos_reserva_inc["IDSocioBeneficiario"] > 0 && ($row_datos_reserva_inc["IDSocioBeneficiario"] == $IDSocio || $row_datos_reserva_inc["IDSocioBeneficiario"] == $IDBeneficiario)) {
                    $bloqueo_incumplida = "S";
                } elseif ($row_datos_reserva_inc["IDSocioBeneficiario"] <= 0 && $row_datos_reserva_inc["IDSocio"] == $IDSocio) {
                    $bloqueo_incumplida = "S";
                }

                $bloqueo_incumplida = "S";
                if ($bloqueo_incumplida == "S") {
                    $respuesta["message"] = "Lo sentimos, el dia " . $Hace8dias . " usted o un miembro de su familia no cumplió con alguna norma de los turnos de tenis.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }
        // FIN Especial lagartos si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis$Fecha = "2019-04-11";



        //Especial para atc solo dos turnos por semana
        if ($IDClub == 7 && empty($Admin)) {
            if ($IDServicio == "2209") : // Retos lagartos
                $ReservasPermitidaSemana = 1;
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";

                $fecha_hoy_semana = date("Y-m-d");
                $hora_hoy_semana = date("H:i:s");
                $year = date('Y', strtotime($Fecha));
                $week = date('W', strtotime($Fecha));
                $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
                $fecha_lunes = $fechaInicioSemana; //Lunes
                $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
                // Valido tambien los de la misma acción
                $accion_padre = $datos_socio["AccionPadre"];
                $accion_socio = $datos_socio["Accion"];

                if (empty($accion_padre)) : // Es titular
                    $array_socio[] = $IDSocio;
                /*
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query( $sql_nucleo );
                while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
                $array_socio[] = $row_nucleo[ "IDSocio" ];
                endwhile;
                 */
                endif;
                if (count($array_socio) > 0) :
                    $id_socio_nucleo = implode(",", $array_socio);
                endif;

                //Consulto las de hoy pasada la hora actual
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "' and (Fecha='" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_lunes . "' and Fecha <= '" . $fecha_domingo . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                $total_reservas_semana = $dbo->rows($sql_reservas_sem);

                //Consulto la de mañana en adelante
                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "' and  Fecha > '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_lunes . "' and Fecha <= '" . $fecha_domingo . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                $total_reservas_semana += (int) $dbo->rows($sql_reservas_sem);

                if ((int) $total_reservas_semana >= (int) $ReservasPermitidaSemana) :
                    $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por fin de semana ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endif;
        }

        //Especial Guaymaral si es clase se reserva la de dentro de ocho dias automaticamente siempre y cuando este aciva la fecha
        if ($IDClub == 8 && $IDServicio == 41 && $IDTipoReserva == "517") :
            $RepetirFechaFinal = strtotime('+1 week', strtotime($Fecha));
            $minima_fecha = date("Y-m") . "-14";
            $maxima_fecha = new DateTime();
            $maxima_fecha->modify('last day of this month');
            $maxima_fecha->format('Y-m-d');
            if ((int) date("d") <= 14 && (int) date("d", $RepetirFechaFinal) <= 14) :
                $permite_repetir = "S";
            elseif ((int) date("d") >= 15 && $RepetirFechaFinal <= strtotime($maxima_fecha->format('Y-m-d'))) :
                $permite_repetir = "S";
            else :
                $permite_repetir = "N";
            endif;
            if ($permite_repetir == "S") :
                $mensaje_especial_repetir = " Se realizó un reserva automatica para el día " . date("Y-m-d", $RepetirFechaFinal);
                $Repetir = "S";
                $Periodo = "Semana";
                $RepetirFechaFinal = strtotime('+8 day', strtotime($Fecha));
                $RepetirFechaFinal = date("Y-m-d", $RepetirFechaFinal);
            else :
                $mensaje_especial_repetir = " No se pudo realizar la reserva automatica en la siguiente semana ya que la fecha aun no esta disponible";
            endif;
        endif;
        //Fin validación especial

        //Especial Aeroclub cuando es crucero separa varios días
        //if ( $IDClub == 36 && $IDServicio == 3608 && ( $IDTipoReserva == 718 || $IDTipoReserva == 745 || $IDTipoReserva == 746 || $IDTipoReserva == 747 || $IDTipoReserva == 748 ) ):
        if ($IDClub == 36 && $IDServicio == 4371 && ($IDTipoReserva == 787 || $IDTipoReserva == 788 || $IDTipoReserva == 789 || $IDTipoReserva == 790 || $IDTipoReserva == 791)) :

            $cantidad_dias_agregar = $datos_tipo_reserva["NumeroDias"];
            if ((int) $datos_tipo_reserva["NumeroDias"] > 1) :
                //$IDTipoReserva = "";
                $Repetir = "S";
                $Periodo = "Dia";
                $HastaFechaFinal = strtotime('+' . $cantidad_dias_agregar . ' day', strtotime($Fecha));
                $RepetirFechaFinal = date("Y-m-d", $HastaFechaFinal);
                // de una vez valido que el avion no haya sido reservado en los siguientes dias
                $fechaInicioVal = strtotime($Fecha);
                $fechaFin = strtotime($RepetirFechaFinal);
                $condicion_multiple_elemento_av = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                $cuentafecha = 0;
                for ($contador_fecha = $fechaInicioVal; $contador_fecha <= $fechaFin; $contador_fecha += 86400) :

                    if ($cuentafecha > 0) : // valido desde la segunda fecha en adelante
                        $fecha_validar .= "S" . $cuentafecha;
                        $fecha_validar_avion = date("Y-m-d", $contador_fecha);
                        $sql_reserva_elemento_avion = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento in (" . $condicion_multiple_elemento_av . ") and Fecha = '" . $fecha_validar_avion . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) Limit 1 ";
                        $r_reserva_elemento_avion = $dbo->query($sql_reserva_elemento_avion);
                        if ($dbo->rows($r_reserva_elemento_avion) > 0) :
                            $respuesta["message"] = "Lo sentimos el avion ya esta reservado el " . $fecha_validar_avion;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                    $cuentafecha++;
                endfor;
            endif;
        endif;
        //Fin validación especial

        //Fin validación especial

        $fecha = date('Y-m-d', strtotime($Fecha));
        $festivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $fecha . "' and IDPais = 1");

        //Especial Arrayanes Colombia para golf fines de semana solo permite reservas de socios con handicap
        if ($IDClub == 11 && $IDServicio == 122 && (date("w", strtotime($Fecha)) == 0 || date("w", strtotime($Fecha)) == 6 || (date("w", strtotime($Fecha)) == 1 && !empty($festivo))) && $Hora <= "11:00:00") :
            $datos_socio_valida_per = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $id_socio_permiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio_valida_per["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
            if (empty($id_socio_permiso)) :
                $respuesta["message"] = "Lo sentimos no tiene permisos para reservar los fines de semana ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :
                //NUEVA PARTE PARA VERIFICAR SI LOS INVITADOS TIENEN HANDICAP Y CUMPLEN CON LOS REQUERIMIENTOS


                //BENEFICIARIO
                if (!empty($IDBeneficiario)) {
                    $datos_beneficiario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDBeneficiario . "' ", "array");
                    $id_socio_permiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" .       $datos_beneficiario["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");


                    if (empty($id_socio_permiso)) :
                        $respuesta["message"] = "Lo sentimos no tiene permisos para reservar los fines de semana ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                }



                $datos["total"] = 0;
                $total_invitados = 0;
                $ids = "";
                $datos_invitado_turno = json_decode($Invitados, true);
                foreach ($datos_invitado_turno as $detalle_datos_turno) :
                    $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
                    if (!empty($IDSocioInvitadoTurno)) :
                        $datos_socio_invitado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitadoTurno . "' ", "array");
                        $id_socio_invitado = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" .                          $datos_socio_invitado["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "' LIMIT 1");

                        $ids .= $id_socio_invitado . ",";
                    endif;
                endforeach;
                if (!empty($ids)) :
                    $ids_socios = substr($ids, 0, -1);
                    $total_invitados_socios = explode(",", $ids_socios);
                    $total_invitados = count($total_invitados_socios);
                endif;

                //VALIDAMOS QUE LOS INVITADOS TENGAN PERMISO DE RESERVAR
                $socioConPermiso = "SELECT COUNT(*) as total FROM SocioPermisoReserva WHERE IDClub = '" . $IDClub . "' AND IDSocioPermisoReserva IN ($ids_socios)";
                $resultado = $dbo->query($socioConPermiso);
                $datos = $dbo->fetchArray($resultado);
                $datos["total"];
                if ($datos["total"] != $total_invitados) {
                    $respuesta["message"] = "Lo sentimos, alguno de sus invitados no tiene permitido reservar";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

            /*
                // verifico que los invitados tambien tengan handicap
                $nuevacadena_hand = str_replace('Optional("', "", $Invitados);
                $nuevacadena_hand = str_replace('")', "", $nuevacadena_hand);
                $Invitados_hand = $nuevacadena_hand;
                $datos_invitado_hand = json_decode($Invitados_hand, true);

                if (count($datos_invitado_hand) > 0) :
                    foreach ($datos_invitado_hand as $detalle_datos) :
                        $IDSocioInvitadoHand = $detalle_datos["IDSocio"];
                        if (!empty($IDSocioInvitadoHand)) :
                            $datos_socio_valida_per_hand = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitadoHand . "' ", "array");
                            $id_socio_permiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio_valida_per_hand["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                            if (empty($id_socio_permiso)) :
                                $respuesta["message"] = "Lo sentimos su invitado " . $datos_socio_valida_per_hand["Nombre"] . " " . $datos_socio_valida_per_hand["Apellido"] . " " . "no tiene permisos para reservar los fines de semana";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                        endif;
                    endforeach;
                endif; */

            endif;
        endif;
        //Fin validación especial




        // CONDICION APP TO-DO: https://3.basecamp.com/4226699/buckets/12221793/todos/4248585013
        // CONDICION RANCHO BOGOTA PARA GOLF INVITADOS EXTERNOS
        if ($IDClub == 12 && $IDServicio == 144 && empty($Admin)) :
            $festivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $Fecha . "' and IDPais = 1");
            $dia_manana = date('Y-m-d', time() + 84600);
            $fecha_hoy_v = date("Y-m-d");

            $dia = date("w", strtotime($Fecha));

            if (($dia == 0 || $dia == 6 || ($dia == 1 && !empty($festivo))) && ($Hora >= '06:00:00' && $Hora <= '12:00:00') && ((date("H:i:s") <= "19:00:00" && $dia_manana == $Fecha) || ($fecha_hoy_v == $Fecha && date("H:i:s") <= "06:00:00"))) :

                $datos_invitado = json_decode($Invitados, true);
                if (count($datos_invitado) > 0) {
                    foreach ($datos_invitado as $detalle_datos) {
                        $IDSocioInvitado = $detalle_datos["IDSocio"];
                        if ($IDSocioInvitado == 0 || $IDSocioInvitado == "") {
                            $respuesta["message"] = "Lo sentimos no se pueden agregar invitados externos en estas hora, solo esta permitido, un dia antes desde las 7 pm o el mismo dia despues de las 6 am ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }
            endif;
        endif;

        //Especial San Jacinto debe seleccionar cualquier persona un caddie para poder continuar
        if ($IDClub == 126 && $IDServicio == 23139 && ($IDTipoReserva == 5960 || $IDTipoReserva == 5959 || $IDTipoReserva == 6007)) {
            $NumCaddie = 0;
            //Calculo el numero de caddies seleccionados
            if ((int)$IDCaddieSocio > 0) {
                $NumCaddie = 1;
            }

            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :
                    $IDCaddieInvitado = $detalle_datos["IDCaddieInvitado"];
                    if ((int)$IDCaddieInvitado > 0) {
                        $NumCaddie++;
                    }
                endforeach;
            endif;

            if ($NumCaddie < 1) {
                $respuesta["message"] = "Lo sentimos debe agregar por lo menos 1 caddie ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }


        //Especial Villa peru
        if ($IDClub == 220 && $IDServicio == 44694) {
            $NumCaddie = 0;
            //Calculo el numero de caddies seleccionados
            if ((int)$IDCaddieSocio > 0) {
                $NumCaddie = 1;
            }

            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :
                    $IDCaddieInvitado = $detalle_datos["IDCaddieInvitado"];
                    if ((int)$IDCaddieInvitado > 0) {
                        $NumCaddie++;
                    }
                endforeach;
            endif;

            $festivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $Fecha . "' and IDPais = 5");
            $dia = date("w", strtotime($Fecha));
            $Integrantes = count($datos_invitado) + 1;
            if (($dia == 0 || $dia == 6 || !empty($festivo)) && ($Hora >= '07:00:00' && $Hora <= '11:00:00')) {
                if ($Integrantes >= 2 &&  $Integrantes <= 3 && $NumCaddie < 1) {
                    $respuesta["message"] = "Lo sentimos debe agregar por lo menos 1 caddie ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } elseif ($Integrantes >= 4 && $NumCaddie < 2) {
                    $respuesta["message"] = "Lo sentimos debe tener 2 caddies minimo ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }
        //Villa peru los hijos no pueden reservar
        $IDParentesco = $datos_socio["IDParentesco"];
        if ($IDClub == 220 && empty($Admin) && ($IDParentesco == 7 || $IDParentesco == 8 || $IDParentesco == 9 || $IDParentesco == 10 || $IDParentesco == 11 || $IDParentesco == 12 || $IDParentesco == 19 || $IDParentesco == 21 || $IDParentesco == 45 || $IDParentesco == 55 || $IDParentesco == 56 || $IDParentesco == 57)) {
            $respuesta["message"] = "Lo sentimos, está bloqueado el servicio de reservas. Consulte con el Dpto. de Asociados del CCV.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if ($IDClub == 220 && ($IDServicio == 44704 || $IDServicio == 44700)) {
            $AccionInicial1 = substr($datos_socio["Accion"], 0, 1);
            $AccionInicial2 = substr($datos_socio["Accion"], 0, 2);
            if (strtoupper($AccionInicial1) == "J" || strtoupper($AccionInicial1) == "JD") {
                $respuesta["message"] = "Lo sentimos, está bloqueado el servicio de reservas de bungalow. Consulte con el Dpto. de Asociados del CCV.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //Fin Villa peru



        //Especia Valle arriba athletic
        if ($IDClub == 239) {
            $reservas_manana = 0;
            $reservas_tarde = 0;
            if ($IDServicio == 48554 || $IDServicio == 48569) {
                //Las dos reservas diarias que puede hacer un socio deben ser 1 en la mañana y 1 en la tarde.                
                if ((int) $IDBeneficiario > 0) {
                    $sql_reserva = "SELECT IDReservaGeneral,Hora FROM ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' )  and Fecha= '" . $Fecha . "' and IDServicio in ($IDServicio)  and IDEstadoReserva = '1' ORDER BY Hora LIMIT 1 ";
                } else {
                    $sql_reserva = "SELECT IDReservaGeneral,Hora FROM ReservaGeneral Where ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )  and Fecha= '" . $Fecha . "' and IDServicio in ($IDServicio) and IDEstadoReserva = '1' ORDER BY Hora LIMIT 1 ";
                }
                $r_reserva = $dbo->query($sql_reserva);
                $row_reserva = $dbo->fetchArray($r_reserva);
                if ((int)$row_reserva["IDReservaGeneral"] > 0) {
                    if ($row_reserva["Hora"] <= "11:59:00") {
                        $reservas_manana = 1;
                    } else {
                        $reservas_tarde = 1;
                    }
                    if ($Hora <= '11:59:00' && $reservas_manana > 0) {
                        $respuesta["message"] = "Lo sentimos, ya tiene una reserva en la mañana solo puede hacer otra en la tarde";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($reservas_tarde > 0) {
                        $respuesta["message"] = "Lo sentimos, ya tiene una reserva en la tarde solo puede hacer otra en la mañana";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                //El siguiente día de reserva no puede ser seguido, es decir, las reservas son interdiarias.
                $hoy_val = date("Y-m-d");
                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
                //Consulto la ultima reserva

                if ((int) $IDBeneficiario > 0) {
                    $sql_ult = "SELECT IDReservaGeneral,Hora,Fecha FROM ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' )  and IDServicio in ($IDServicio)  and IDEstadoReserva = '1' ORDER BY Fecha DESC LIMIT 1 ";
                } else {
                    $sql_ult = "SELECT IDReservaGeneral,Hora,Fecha FROM ReservaGeneral Where ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )  and IDServicio in ($IDServicio) and IDEstadoReserva = '1' ORDER BY Fecha DESC LIMIT 1 ";
                }
                $r_ult = $dbo->query($sql_ult);
                $row_ult = $dbo->fetchArray($r_ult);
                if (!empty($row_ult["Fecha"])) {
                    // le sumo un dia a la fecha de la ultima reserva
                    $DiaSiguienteUltima = strtotime('+1 day', strtotime($row_ult["Fecha"]));
                    $FechaDiaSiguiente = date("Y-m-d", $DiaSiguienteUltima);
                    if ($FechaDiaSiguiente == $Fecha) {
                        $respuesta["message"] = "Lo sentimos solo puede reservar dia por medio";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        // le resto un dia a la fecha de la ultima reserva
                        $DiaAnteriorUltima = strtotime('-1 day', strtotime($row_ult["Fecha"]));
                        $FechaDiaAnterior = date("Y-m-d", $DiaAnteriorUltima);
                        if ($FechaDiaAnterior == $Fecha) {
                            $respuesta["message"] = "Lo sentimos solo puede reservar dia por medio!";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }
            }
        }
        //FIN Especia Valle arriba athletic



        //Especial Rancho Colombia para golf fines de semana solo permite reservas de socios con handicap
        if (($IDClub == 12 && $IDServicio == 144 && (date("w", strtotime($Fecha)) == 0 || date("w", strtotime($Fecha)) == 6) && $Hora >= "07:00:00" && $Hora < ":00:00") || $IDServicio == 145) :

            $sumaHandicap = 0;
            $id_socio_permiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
            //suma handicap del socio
            $handicap = $dbo->getFields("SocioPermisoReserva", "Handicap", "NumeroDocumento = '" . $datos_socio["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
            $sumaHandicap += $handicap;



            if (empty($id_socio_permiso)) :
                $respuesta["message"] = "Lo sentimos no tiene permisos para reservar los fines de semana ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            else :
                // verifico que los invitados tambien tengan handicap
                $nuevacadena_hand = str_replace('Optional("', "", $Invitados);
                $nuevacadena_hand = str_replace('")', "", $nuevacadena_hand);
                $Invitados_hand = $nuevacadena_hand;
                $datos_invitado_hand = json_decode($Invitados_hand, true);

                if (count($datos_invitado_hand) > 0) :
                    foreach ($datos_invitado_hand as $detalle_datos) :
                        $IDSocioInvitadoHand = $detalle_datos["IDSocio"];
                        if (!empty($IDSocioInvitadoHand)) :

                            $datos_socio_valida_per_hand = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitadoHand . "' ", "array");
                            $id_socio_permiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio_valida_per_hand["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");

                            $handicapInvitado = $dbo->getFields("SocioPermisoReserva", "Handicap", "NumeroDocumento = '" . $datos_socio_valida_per_hand["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                            $sumaHandicap += $handicapInvitado;

                            if (empty($id_socio_permiso)) :
                                $respuesta["message"] = "Lo sentimos su invitado " . $datos_socio_valida_per_hand["Nombre"] . " " . $datos_socio_valida_per_hand["Apellido"] . " " . "no tiene permisos para reservar los fines de semana";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                        endif;
                    endforeach;

                    if ($sumaHandicap > 120 && $IDServicio == 145) :
                        $respuesta["message"] = "Lo sentimos tu handicap y el de tus invitados no alcanza para reservar";
                        $respuesta["success"] = false;
                        $respuesta["response"] = NULL;
                        return $respuesta;
                    endif;
                else :
                    if ($sumaHandicap > 120 && $IDServicio == 145) :
                        $respuesta["message"] = "Lo sentimos tu handicap no alcanza para reservar";
                        $respuesta["success"] = false;
                        $respuesta["response"] = NULL;
                        return $respuesta;
                    endif;
                endif;
            endif;
        endif;
        //Fin validación especial


        //Especial Cartagena en un solo dia en unos horarios permite reservar con suma de handicap
        if (($IDClub == 218 && $IDServicio == 43999 && (date("w", strtotime($Fecha)) == 0 || date("w", strtotime($Fecha)) == 6))) :

            $sumaHandicap = 0;
            $sumaHandicap += $datos_socio["Handicap"];

            // verifico que los invitados tambien tengan handicap
            $nuevacadena_hand = str_replace('Optional("', "", $Invitados);
            $nuevacadena_hand = str_replace('")', "", $nuevacadena_hand);
            $Invitados_hand = $nuevacadena_hand;
            $datos_invitado_hand = json_decode($Invitados_hand, true);

            if (count($datos_invitado_hand) >= 4 && $Tee = "Tee1" && $Hora < "08:30:00") :
                $respuesta["message"] = "lo sentimos los fivesome solo podran iniciar a jugar por el hoyo 1 a partir de las 08:30 a.m. ";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                return $respuesta;
            endif;

            if (count($datos_invitado_hand) > 0) :
                foreach ($datos_invitado_hand as $detalle_datos) :
                    $IDSocioInvitadoHand = $detalle_datos["IDSocio"];
                    if (!empty($IDSocioInvitadoHand)) :
                        $datos_socio_valida_per_hand = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitadoHand . "' ", "array");
                        $sumaHandicap += $datos_socio_valida_per_hand["Handicap"];
                    endif;
                endforeach;
            endif;

            if ($sumaHandicap > 90 && $Tee = "Tee1" && $Hora >= "07:00:00" && $Hora <= "09:30:00") :
                $respuesta["message"] = "Lo sentimos tu handicap y el de tus invitados es superior al maximo permitido!";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                return $respuesta;
            endif;

        endif;
        //Fin validación especial



        //Validacion especial Polo solo 1 turno por elemento en practicas
        if (($IDClub == 37 && $IDServicio == "3575") || ($IDClub == 143 && $IDServicio == "28122")) {
            $id_reserva_general_soc = (int) $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDSocio = '" . IDSocio . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 1 and IDServicioElemento = '" . $IDElemento . "'");
            if ($id_reserva_general_soc > 0) {
                $respuesta["message"] = "Ya tiene una reserva en esta hora por favor verifique Mis Reservas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //Especial para cuando el campo de repetir se pregunta en la reserva
        $array_Campos = $Campos;
        $array_Campos = json_decode($Campos, true);
        if (count($array_Campos) > 0) :
            foreach ($array_Campos as $id_valor_campo => $valor_campo) :
                if (($valor_campo["IDCampo"] == 31 && (int) $valor_campo["Valor"] > 0) || ($valor_campo["IDCampo"] == 762 && (int) $valor_campo["Valor"] > 0)) {
                    $Repetir = "S";
                    $Periodo = "Dia";
                    $dias_repetir = $valor_campo["Valor"] - 1;
                    $RepetirFechaFinal = strtotime('+' . $dias_repetir . ' day', strtotime($Fecha));
                    $RepetirFechaFinal = date("Y-m-d", $RepetirFechaFinal);
                    $EspecialAnapoima = "Especial";
                }
            endforeach;

            if ($Repetir == "S") {
                $FechasDisponibles = "S";
                $FechaIniVal = strtotime($Fecha);
                $FechaFinVal = strtotime($RepetirFechaFinal);
                for ($contador_fecha = $FechaIniVal; $contador_fecha <= $FechaFinVal; $contador_fecha += 86400) {
                    $FechaVal = date("Y-m-d", $contador_fecha);
                    $sql_val_reser = "SELECT IDReservaGeneral From ReservaGeneral Where Fecha = '" . $FechaVal . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento = '" . $IDElemento . "' LIMIT 1";
                    $sql_reserva_dia_val = $dbo->query($sql_val_reser);
                    $row_reser_dia = $dbo->fetchArray($sql_reserva_dia_val);
                    if ((int) $row_reser_dia["IDReservaGeneral"] > 0) {
                        $FechasDisponibles = "N";
                        $fechan = $FechaVal;
                    }

                    $Dia = date("w", strtotime($FechaVal));

                    // BUSCAMOS QUE NO TENGA UNA FECHA DE CIERRE
                    $SQLCierre = "SELECT SC.IDServicioCierre FROM ServicioCierre SC, ServicioCierreElemento SCE  WHERE SC.IDServicioCierre= SCE.IDServicioCierre and SC.IDServicio = '$IDServicio' AND SC.FechaInicio <= '$FechaVal' AND SC.FechaFin >= '$FechaVal' AND SC.HoraInicio <= '$Hora' AND SC.HoraFin >= '$Hora' and SCE.IDServicioElemento='" . $IDElemento . "' ";
                    $QRYCierre = $dbo->query($SQLCierre);
                    $Cierre = $dbo->fetchArray($QRYCierre);
                    if ($Cierre[IDServicioCierre] > 0) :
                        $respuesta["message"] = "No es posible realizar la reserva, El dia $FechaVal  se encuentra con fecha de cierre";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                }
                if ($FechasDisponibles == "N") {
                    $respuesta["message"] = "No es posible realizar la reserva, El dia " . $fechan . " ya esta reservado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }

        endif;



        //Validacion especial Farallones solo se puede 1 depilación por hora
        /*
        if($IDClub==29 && $IDServicio=="1772"){
        $id_reserva_general_soc=(int)$dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 1 and IDServicioTipoReserva = '618' " );
        if($id_reserva_general_soc>0){
        $respuesta[ "message" ] = "Lo sentimos ya existe una reserva de depilacion a esta hora y solo es posible 1 por hora, intente con otra hora";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        }
        }
         */

        //Valido si la reserva pide un tipo (sencillo, doble, 2 turnos, etc) y si esta vacio le asigno alguno
        $tipo_reserva_servicio = "Select * From ServicioTipoReserva Where IDServicio = '" . $IDServicio . "' and Activo = 'S' Order by Orden Desc";
        $result_reserva_servicio = $dbo->query($tipo_reserva_servicio);
        if (count($dbo->rows($result_reserva_servicio) > 0) && empty($IDTipoReserva)) :
            //le asigno algun tipo ya que es obligatorio
            while ($row_reserva_servicio = $dbo->fetchArray($result_reserva_servicio)) :
                $IDTipoReserva = $row_reserva_servicio["IDServicioTipoResrvicioTipoReserva"];
            endwhile;
        endif;

        //Validacion del formato de hora, el app puede enviar con a.m o p.m
        $Hora = SIMWebService::validar_formato_hora($Hora);



        //if ( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) && $Hora != "00:00:00" ) {
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDElemento) && !empty($IDServicio) && !empty($Fecha) && !empty($Hora)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $datos_socio["IDSocio"];

            if (!empty($id_socio)) {
                // Obtener la disponibilidad utilizada al consultar la reserva
                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora, $Tee);

                $datos_disponibilidad_reserva = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array");

                if ((int) $IDTipoReserva > 0) {
                    $resp_permite = SIMServicioReserva::verificar_apertura_reserva_tipo_reserva($IDTipoReserva, $id_disponibilidad, $Fecha);
                    if (!empty($resp_permite)) {
                        $respuesta["message"] = $resp_permite;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                if ($datos_servicio[ValidarTiempoTipoSocio] == 1) :
                    $TipoSocio = $datos_socio["TipoSocio"];
                    $resp_permite = SIMServicioReserva::verificar_apertura_reserva_tipo_socio($datos_servicio, $Fecha, $TipoSocio, $id_disponibilidad);
                    if (!empty($resp_permite)) {
                        $respuesta["message"] = $resp_permite;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                endif;




                //Valido que no se pueda tomar varios turnos seguidos
                //$PermiteReservaSeguida = $dbo->getFields("Disponibilidad", "PermiteReservaSeguida", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                //$PermiteReservaSeguidaNucleo = $dbo->getFields("Disponibilidad", "PermiteReservaSeguidaNucleo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                //$DuracionTurno = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                $PermiteReservaSeguida = $datos_disponibilidad_reserva["PermiteReservaSeguida"];
                $PermiteReservaSeguidaNucleo = $datos_disponibilidad_reserva["PermiteReservaSeguidaNucleo"];
                $DuracionTurno = $datos_disponibilidad_reserva["Intervalo"];


                if ($PermiteReservaSeguida != "S") {
                    $flag_turno_seguido = SIMWebService::validar_turnos_seguidos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario, $TipoBeneficiario, $PermiteReservaSeguidaNucleo);

                    // SI TIENE INVITADOS VALIDO QUE NO SE TENGAN TURNOS SEGIDOS TAMBIEN
                    $datos_invitado = json_decode($Invitados, true);
                    if (count($datos_invitado) > 0) :
                        foreach ($datos_invitado as $detalle_datos) :
                            $IDSocioInvitado = $detalle_datos["IDSocio"];
                            if ($IDSocioInvitado > 0) :
                                $flag_turno_seguidoInvitados = SIMWebService::validar_turnos_seguidos($Fecha, $Hora, $IDSocioInvitado, $IDServicio, $IDClub, "", "", "S");

                                if ($flag_turno_seguidoInvitados > 0) :
                                    $respuesta["message"] = "Lo sentimos, un invitado tiene un turno separado para en uno de los siguientes turnos y no es posible invitarlo";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;
                            endif;
                        endforeach;
                    endif;
                } else {
                    $flag_turno_seguido = 0;
                }



                // VALIDAR QUE NO SE HAYA PASADO DEL TIEMPO MAXIMO DE RESERVA AL MOMENTO DE HACER LA RESERVA
                $minutoPosterior = (int) $datos_disponibilidad_reserva["MinutoPosteriorTurno"];

                if ($minutoPosterior > 0 && $Fecha == date("Y-m-d") && empty($Admin)) :
                    $HoraActual = strtotime(date("H:i:s"));
                    $tiempo_limite_reserva = strtotime('+' . $minutoPosterior . ' minutes', strtotime($Hora));
                    if ($HoraActual > $tiempo_limite_reserva) :
                        $respuesta["message"] = "Lo sentimos ya paso el maximo tiempo para hacer la reserva despues del turno que tomo";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :
                        $ValidoTiempoDespues = 1;
                    endif;
                endif;

                // VALIDAR TIEMPO DE ANTICIPACÓN EN LA RESERVA                
                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);

                $medicion_tiempo_anticipacion = $datos_disponibilidad_reserva["MedicionTiempoAnticipacionTurno"];
                $valor_anticipacion_turno = (int) $datos_disponibilidad_reserva["AnticipacionTurno"];

                if ($valor_anticipacion_turno > 0 && $Fecha == date("Y-m-d") && empty($Admin)) :
                    switch ($medicion_tiempo_anticipacion):
                        case "Dias":
                            $minutos_anticipacion_turno = (60 * 24) * $valor_anticipacion_turno;
                            break;
                        case "Horas":
                            $minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
                            break;
                        case "Minutos":
                            $minutos_anticipacion_turno = $valor_anticipacion_turno;
                            break;
                        default:
                            $minutos_anticipacion_turno = 0;
                    endswitch;

                    $Hora_Actual = date("H:i:s");
                    $hora_puede_reservar = strtotime("+ $minutos_anticipacion_turno minute", strtotime($Hora_Actual));

                    if ($hora_puede_reservar > strtotime($Hora)) :
                        if ($ValidoTiempoDespues != 1) :
                            $respuesta["message"] = "Lo sentimos, ya paso el tiempo maximo en el que se puede reservar, solo se puede maximo $valor_anticipacion_turno $medicion_tiempo_anticipacion antes";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                endif;
                //Especial para medellin dobles no se permite los sabados
                if ($IDClub == 20 && (date("N", strtotime($Fecha)) == "6") && ($IDTipoReserva == 96 || $IDTipoReserva == 94)) :
                    $respuesta["message"] = "Lo sentimos dobles no se puede los Sabados";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;



                //Especial rancho tenis entre semana si deja mas de una en la misma hora
                if ($IDServicio == 151) {
                    $dia_semana = date("w", strtotime($Fecha));
                    if ($dia_semana != 0 && $dia_semana != 6) {
                        $datos_servicio["PermiteReservaMismaHora"] = "S";
                    }
                }
                //Fin especial rancho




                //Validar solo una reserva por hora
                if ($datos_servicio["PermiteReservaMismaHora"] == "N") {

                    if ($datos_servicio["ValidarReservasMismaHoraServicio"] == 1) :
                        $IDServicioValidar = $IDServicio;
                    else :
                        //Consulto los servicios del club
                        $sql_serv_club = "SELECT IDServicio FROM Servicio WHERE IDClub = '" . $IDClub . "' ";
                        $r_serv_club = $dbo->query($sql_serv_club);
                        while ($row_serv_club = $dbo->fetchArray($r_serv_club)) {
                            $array_serv_club[] = $row_serv_club["IDServicio"];
                        }
                        if (count($array_serv_club) > 0) {
                            $IDServicioValidar = implode(",", $array_serv_club);
                        }
                    endif;

                    if ($IDClub == 44) :
                    /* $HORA = substr($Hora, 0, 2);
                    $HORADESPUES = (int) $HORA + 1;
                    $HoraMinima = $HORA . ":00:00";

                    if ($HORADESPUES < 10) {
                    $HoraMaxima = "0" . $HORADESPUES . ":00:00";
                    } else {
                    $HoraMaxima = $HORADESPUES . ":00:00";
                    } */
                    else :
                        //$DuracionTurno = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $DuracionTurno = $datos_disponibilidad_reserva["Intervalo"];
                        $HoraMinima = $Hora;
                        $HoraMaxima = date("H:i:s", strtotime('+' . $DuracionTurno . ' minutes', strtotime($HoraMinima)));
                    endif;

                    if ((int) $IDBeneficiario > 0) {
                        $sql_valida = "SELECT * FROM ReservaGeneral Where ( (IDSocio = '" . $IDBeneficiario . "'  and IDSocioBeneficiario=0 ) or IDSocioBeneficiario = '" . $IDBeneficiario . "' )  and Fecha= '" . $Fecha . "' and Hora >= '" . $HoraMinima . "' and Hora < '" . $HoraMaxima . "' and IDServicio in ($IDServicioValidar)  and IDEstadoReserva = '1' ";
                        $IDValidarHora = $IDBeneficiario;
                    } else {
                        $sql_valida = "SELECT * FROM ReservaGeneral Where ( (IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocio . "' )  and Fecha= '" . $Fecha . "' and Hora >= '" . $HoraMinima . "' and Hora < '" . $HoraMaxima . "' and IDServicio in ($IDServicioValidar) and IDEstadoReserva = '1' ";
                        $IDValidarHora = $IDSocio;
                    }

                    $sql_reservas_sem = $dbo->query($sql_valida);
                    $total_reservas_dia_hora = $dbo->rows($sql_reservas_sem);

                    ///Consulto donde sea invitado
                    $sql_invitado_hora = "SELECT RGI.*
												FROM ReservaGeneralInvitado RGI, ReservaGeneral RG
												WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDValidarHora . "') and
												RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.Hora ='" . $Hora . "' and
												RG.IDServicio in ($IDServicioValidar)
												ORDER BY IDReservaGeneralInvitado Desc ";
                    $qry_invitado_hora = $dbo->query($sql_invitado_hora);
                    $total_reservas_dia_hora += $dbo->rows($qry_invitado_hora);

                    if ((int) $total_reservas_dia_hora >= 1) :
                        $respuesta["message"] = "Lo sentimos solo se permite 1 reserva en la misma hora en el mismo dia, ya tiene otra reserva a la misma hora";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    //Consulto que los invitados no tengan en la misma hora reserva
                    $datos_invitado = json_decode($Invitados, true);
                    if (count($datos_invitado) > 0) {
                        foreach ($datos_invitado as $detalle_datos) {
                            $total_reservas_dia_hora = 0;
                            $IDSocioInvitado = $detalle_datos["IDSocio"];
                            if (!empty($IDSocioInvitado)) {

                                $sql_valida = "SELECT * FROM ReservaGeneral Where ( (IDSocio = '" . $IDSocioInvitado . "' and IDSocioBeneficiario=0  ) or IDSocioBeneficiario = '" . $IDSocioInvitado . "' )  and Fecha= '" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio in ( $IDServicioValidar )  and IDEstadoReserva = '1' ";
                                $sql_reservas_diahora = $dbo->query($sql_valida);
                                $total_reservas_dia_hora = $dbo->rows($sql_reservas_diahora);

                                ///Consulto donde sea invitado
                                $sql_invitado_hora = "SELECT RGI.*
														FROM ReservaGeneralInvitado RGI, ReservaGeneral RG
														WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocioInvitado . "') and
														RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.Hora ='" . $Hora . "' and
														RG.IDServicio in ($IDServicioValidar)
														ORDER BY IDReservaGeneralInvitado Desc ";
                                $qry_invitado_hora = $dbo->query($sql_invitado_hora);
                                $total_reservas_dia_hora += $dbo->rows($qry_invitado_hora);

                                if ((int) $total_reservas_dia_hora >= 1) :
                                    $respuesta["message"] = "Lo sentimos el invitado ya tiene una reserva en esta misma fecha y hora";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;
                            }
                        }
                    }
                    //Fin Consulta
                }
                //Fin Validar



                if (($IDClub == "9" || $IDClub == "8") && empty($Admin) && ($IDServicio == "89" /* || $IDServicio == "5583" */)) :
                    //Valido regla especial en Esqui si tiene dos turnos seguidos no permite reservar mas si no solo deja las configuradas (Caso especial Mesa de Yeguas)
                    $regla_no_cumplida = SIMWebService::validar_regla_turnos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva);
                    if ($regla_no_cumplida > 0) :
                        switch ($regla_no_cumplida):
                            case "1":
                                $mensaje_regla_no_cumplida = "Lo sentimos, ya tomo dos turnos seguidos, no puede reservas mas turnos en este dia ";
                                break;
                            case "2":
                                $mensaje_regla_no_cumplida = "Lo sentimos, ya reservo un turno, no puede tomar turnos seguidos en este dia";
                                break;
                        endswitch;
                        $respuesta["message"] = $mensaje_regla_no_cumplida;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                if ((($IDClub == "900" || $IDClub == "800") && empty($Admin) && ($IDServicio == "240" || $IDServicio == "479" || $IDServicio == "89" || $IDServicio == "5583")) || (($IDClub == "20") && empty($Admin) && ($IDServicio == "571") && date("N", strtotime($Fecha)) == 6)) :
                    //Valido regla especial tenis my solo dos turnos por accion en un mismo dia en Temporada alta
                    //Valido regla especial tenis MEdellin solo dos turnos por accion en un mismo dia los sabados
                    $regla_no_cumplida_tenis = SIMWebService::validar_regla_turnos_tenis($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva);
                    if ((int) $regla_no_cumplida_tenis > 0) :
                        $mensaje_regla_no_cumplida_tenis = "Lo sentimos, solo se puede tomar dos turnos por accion en el horario solicitado";
                        $respuesta["message"] = $mensaje_regla_no_cumplida_tenis;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                //Especial para Lagartos en clases natación solo permite tomar reservas antes de las 8am el mismo dia
                /*
                if ( ( $IDClub == "7" || $IDClub == "800" ) && empty( $Admin ) && $IDServicio == "94" ):
                if ( date( "Y-m-d" ) == $Fecha && strtotime( date( "Y-m-d H:i:s" ) ) >= strtotime( date( "Y-m-d 08:00:00" ) ) ):
                $respuesta[ "message" ] = "Lo sentimos, solo se puede reservar clases para hoy hasta antes de las 8 a.m.";
                $respuesta[ "success" ] = false;
                $respuesta[ "response" ] = NULL;
                return $respuesta;
                endif;
                endif;
                 */

                //$rand_seg2 = rand(1,2);
                //sleep($rand_seg2);

                //Valido que se tengan los invitados minimos y maximos para reservar
                // elimino la pabra optional segun bug detectado en una actualizacion en ios
                $nuevacadena = str_replace('Optional("', "", $Invitados);
                $nuevacadena = str_replace('")', "", $nuevacadena);
                $Invitados = $nuevacadena;
                $datos_invitado = json_decode($Invitados, true);

                // Si el numero de turnos es mayor a 1 multiplico el minimo de la disponibilidad * el numero de turnos para saber el minimo y validar
                if ((int) $NumeroTurnos > 1) :
                    //$MinimoInvitadosDisponibilidad = $dbo->getFields("Disponibilidad", "MinimoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $MinimoInvitadosDisponibilidad = $datos_disponibilidad_reserva["MinimoInvitados"];
                    $MinimoInvitados = (int) ($MinimoInvitadosDisponibilidad * $NumeroTurnos) - 1; // Le resto 1 para que cuente al socio que hace la reserva
                else :
                    //$MinimoInvitadosDisponibilidad = $dbo->getFields("Disponibilidad", "MinimoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $MinimoInvitadosDisponibilidad = $datos_disponibilidad_reserva["MinimoInvitados"];
                    $MinimoInvitados = (int) $MinimoInvitadosDisponibilidad - 1;
                endif;

                if ((int) $NumeroTurnos > 1) :
                    //$MaximoInvitadosDisponibilidad = $dbo->getFields("Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $MaximoInvitadosDisponibilidad = $datos_disponibilidad_reserva["MaximoInvitados"];
                    $MaximoInvitados = $MaximoInvitadosDisponibilidad * $NumeroTurnos;
                else :
                    //$MaximoInvitados = $dbo->getFields("Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $MaximoInvitados = $datos_disponibilidad_reserva["MaximoInvitados"];
                endif;

                // Si es desde el administrador permito agregar los invitados
                if (!empty($Admin)) {
                    $MaximoInvitados = 100;
                }

                $cantidad_invitado = json_decode($Invitados, true);

                // Si agrega un aux por ejemplo boleador lo cuento como invitado
                if (!empty($IDAuxiliar)) :

                    $IDAuxiliar = $IDAuxiliar . ",";
                    $cantidad_auxiliar = 1;
                    //Verifico si el auxiliar esta disponible en esta fecha hora
                    $id_reserva_aux = "";
                    if (($IDClub == "8" || $IDClub == "10") && ($IDServicio == "316" || $IDServicio == "317")) :
                        $id_reserva_aux = SIMWebServiceApp::validar_disponibilidad_auxiliar($IDAuxiliar, $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub);
                        $mensaje_auxiliar_no_dispo = "La masajista seleccionada no esta disponible en esta fecha/hora, por favor seleccione otra";
                    else :
                        //Pongo un tiempo de espera por si ingresan varios al mismo tiempo
                        $id_reserva_aux = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)");
                        $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora";
                    endif;
                    if (!empty($id_reserva_aux)) :
                        $respuesta["message"] = $mensaje_auxiliar_no_dispo;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                else :
                    $cantidad_auxiliar = 0;
                endif;

                //Cuando se puede escoger multiples auxiliares
                $datos_auxiliares = json_decode($ListaAuxiliar, true);
                if (count($datos_auxiliares) > 0) :
                    $cantidad_auxiliar = count($datos_auxiliares);
                    //$ArrayAuxiliar = implode(",",$datos_auxiliares);
                    foreach ($datos_auxiliares as $key_aux => $auxiliar_seleccionado) :
                        $array_id_auxiliar[] = $auxiliar_seleccionado["IDAuxiliar"];

                    endforeach;
                    if (count($array_id_auxiliar) > 0) :
                        $IDAuxiliar = implode(",", $array_id_auxiliar) . ",";
                    endif;
                endif;

                if (!empty($IDAuxiliar)) :
                    //Actualizo la reserva separada con el dato del auxiliar
                    $update_reserva = "Update ReservaGeneral Set IDAuxiliar = '" . $IDAuxiliar . "', IDSocioBeneficiario='" . $IDBeneficiario . "' Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio = '" . $IDServicio . "' and IDSocio = '" . $IDSocio . "' and IDEstadoreserva = '3' ";
                    $dbo->query($update_reserva);
                endif;

                if ($cantidad_auxiliar >= 1 && $MaximoInvitados == 0) {
                    $MaximoInvitados = $cantidad_auxiliar;
                }



                if ((count($datos_invitado) + $cantidad_auxiliar) >= (int) $MinimoInvitados) :
                    if ((count($datos_invitado) + $cantidad_auxiliar) <= (int) $MaximoInvitados) :
                        //if (1==1):

                        // Si es Admin si puede reservas turnos seguidos
                        // PARA EL CONTRY BOGOTA POR EL ADMINISTRADOR NO PERMITE TAMPOCO TURNOS SEGUIDOS
                        if (!empty($Admin) && $IDClub != 44) :
                            $flag_turno_seguido = 0;
                        endif;

                        if ($flag_turno_seguido == 0) :
                            $fecha_disponible = 0;

                            //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
                            $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha);

                            foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha) :
                                if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"] == "S") :
                                    $fecha_disponible = 1;
                                endif;
                            endforeach;

                            // Si es Admin si puede reservas cualquier fecha
                            //Ene 27 El rancho solicitan que ellos no puedan tomar turnos si no esta activo el dia
                            //if ( !empty( $Admin ) && $IDClub != "12" ):
                            if (!empty($Admin)) :
                                $fecha_disponible = 1;
                            endif;

                            if (!empty($IDBeneficiario) && !empty($TipoBeneficiario)) :
                                if ($TipoBeneficiario == "Invitado") {
                                    $IDInvitadoBeneficiario = $IDBeneficiario;
                                } elseif ($TipoBeneficiario == "Socio") {
                                    $IDSocioBeneficiario = $IDBeneficiario;
                                }

                            endif;

                            //Si el numero maximo de invitados esta en 0 no dejo que la reserva sea a un invitado seleccionado por beneficiarios

                            if ($MaximoInvitados <= 0 && (int) $IDInvitadoBeneficiario > 0) :
                                $respuesta["message"] = "Lo sentimos el dia de hoy no es posible tomar reserva a nombre de un invitado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            if ($MaximoInvitados <= 0 && (int) $IDInvitadoBeneficiario > 0) :
                                $respuesta["message"] = "Lo sentimos, en este horario no es posible tomar reserva a nombre de un invitado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            //VALIDA SI EL ELEMENTO ADICIONAL ESTÁ DISPONIBLE
                            $datos_invitado = json_decode($Invitados, true);

                            $elementoAdicional = [];

                            $consecutivo = uniqid();

                            if (count($datos_invitado) > 0) :
                                $dbo->getFields("Club", "Nombre", "IDClub = '" . $_SESSION[IDClub] . "'");
                                $cantidad += count($datos_invitado);
                                foreach ($datos_invitado as $detalle_datos) :
                                    $datos_respuesta = $detalle_datos["Adicionales"];
                                    if (count($datos_respuesta) > 0) :
                                        foreach ($datos_respuesta as $detalle_carac) :
                                            $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                            $ValoresID = $detalle_carac["ValoresID"];

                                            if (!empty($IDPropiedadProducto)) {
                                                $array_id_carac = explode(",", $ValoresID);
                                                if (count($array_id_carac) > 0) {
                                                    foreach ($array_id_carac as $id_carac) {
                                                        //Valida si no hay disponibilidad del elemento
                                                        $validacion = SIMServicioReserva::validar_disponibilidad_elemento_adicional($id_carac, $Fecha);
                                                        if ($validacion["response"] == "N") :
                                                            $respuesta["message"] = $validacion["message"];
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        else :
                                                            SIMServicioReserva::ElementoAdicionalEnReserva($id_carac, $detalle_datos["IDSocio"], $consecutivo, "INV", $Fecha);
                                                            $elementoAdicional[$id_carac]++;
                                                            if ($validacion["cantidad"] < $elementoAdicional[$id_carac]) :
                                                                $respuesta["message"] = "El elemento {$validacion['nombre']} solo tiene {$validacion['cantidad']} unidad(es) disponibles";
                                                                $respuesta["success"] = false;
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            endif;
                                                        endif;
                                                        //Fin validación
                                                    }
                                                }
                                            }
                                        endforeach;
                                    endif;
                                endforeach;
                            endif;

                            $array_Adicional = json_decode($AdicionalesSocio, true);

                            if (count($array_Adicional) > 0) {
                                foreach ($array_Adicional as $detalle_carac) {
                                    $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                    $ValoresID = $detalle_carac["ValoresID"];

                                    if (!empty($IDPropiedadProducto)) {
                                        $array_id_carac = explode(",", $ValoresID);
                                        if (count($array_id_carac) > 0) {
                                            foreach ($array_id_carac as $id_carac) {
                                                //Valida si no hay disponibilidad del elemento
                                                $validacion = SIMServicioReserva::validar_disponibilidad_elemento_adicional($id_carac, $Fecha);
                                                if ($validacion["response"] == "N") :
                                                    $respuesta["message"] = $validacion["message"];
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                else :
                                                    SIMServicioReserva::ElementoAdicionalEnReserva($id_carac, $IDSocio, $consecutivo, "SOC", $Fecha);
                                                    $elementoAdicional[$id_carac]++;
                                                    if ($validacion["cantidad"] < $elementoAdicional[$id_carac]) :
                                                        $respuesta["message"] = "El elemento {$validacion['nombre']} solo tiene {$validacion['cantidad']} unidad(es) disponibles";
                                                        $respuesta["success"] = false;
                                                        $respuesta["response"] = null;
                                                        return $respuesta;
                                                    endif;
                                                endif;
                                                //Fin validación
                                                //VALIDAR EDAD PARA ADICIONALES
                                                $ValidarEdadAdicional = SIMServicioReserva::validar_edad_adicionales($id_carac, $IDSocio, $datos_socio["FechaNacimiento"], $IDBeneficiario, $datos_beneficiario["FechaNacimiento"]);
                                                if (!$ValidarEdadAdicional[success]) :
                                                    $respuesta["message"] = $ValidarEdadAdicional[message];
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;
                                                //FIN VALIDACIÓN

                                            }
                                        }
                                    }
                                }
                            }
                            //

                            //ESPECIAL LA PRADRERA CADDIES EN ADICIONALES
                            /* if($IDClub == 8 && $IDServicio == 31 && $datos_servicio["PermiteAdicionarServicios"] == 'S') */
                            if ($IDClub == 16 && $IDServicio == 329 && empty($Admin) && $datos_servicio["PermiteAdicionarServicios"] == 'S') {
                                $datos_invitado = json_decode($Invitados, true);
                                $cantidadPropiedad = 0;
                                $cantidad = 1;

                                if (count($datos_invitado) > 0) :
                                    $cantidad += count($datos_invitado);
                                    foreach ($datos_invitado as $detalle_datos) :
                                        $datos_respuesta = $detalle_datos["Adicionales"];
                                        if (count($datos_respuesta) > 0) :
                                            foreach ($datos_respuesta as $detalle_carac) :
                                                $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                                if ($IDPropiedadProducto == 9) {
                                                    $cantidadPropiedad++;
                                                }
                                            endforeach;
                                        endif;
                                    endforeach;
                                endif;

                                $array_Adicional = json_decode($AdicionalesSocio, true);

                                foreach ($array_Adicional as $detalle_carac) {
                                    $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];

                                    if ($IDPropiedadProducto == 9) {
                                        $cantidadPropiedad++;
                                    }
                                }

                                if ($cantidad <= 3 && $cantidadPropiedad < 1) {
                                    $respuesta["message"] = "Lo sentimos debe seleccionar por lo menos 1 caddie para realizar la reserva.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                } elseif ($cantidad >= 4 && $cantidadPropiedad < 2) {
                                    $respuesta["message"] = "Lo sentimos debe seleccionar por lo menos 2 caddies para realizar la reserva.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                }
                            }

                            if ($fecha_disponible == 1) :

                                if ($IDClub == 1 || $IDClub == 23 || $IDClub == 16 || $IDClub == 44 || $IDClub == 125) :
                                    //Para Guaymaral si deja tomar otro turno asi tenga reserva de cancha automatica
                                    $condicion_automatica = " and Tipo <> 'Automatica' ";
                                endif;

                                //Verifico que el socio no tenga mas de x reservas en el mismo dia dependiendo la conf de disponibilidad
                                //Para Mess Yeguas en temporada alta no pemite asi se la haya realizado a un invitado o benef para los demas clubes si lo permite
                                //Para Medellin si es sabado no permite mas de dos reservas asi sea para beneficiario
                                if ($IDClub == "900" || $IDClub == 800) :
                                    $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and Tipo <> 'Automatica'");
                                else :
                                    //$sql_reservas_dia = $dbo->query( "Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDSocioBeneficiario = '" . ( int )$IDSocioBeneficiario . "' and IDEstadoReserva = '1' " . $condicion_automatica );
                                    //$sql_reservas_dia = $dbo->query( "Select * From ReservaGeneral Where  Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and (  ( IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario = '" . ( int )$IDSocioBeneficiario . "') or IDSocioBeneficiario = '".$IDSocio."') and IDEstadoReserva = '1' " . $condicion_automatica );

                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "' or (IDSocio = '" . $IDBeneficiario . "' and IDSocioBeneficiario = 0) ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica);
                                    } else {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( (IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "') or IDSocioBeneficiario='" . $IDSocio . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica);
                                    }

                                endif;

                                //Para medellin se valida que no tenga ,mas de dos reservas en el dia en tenis y golf
                                if ($IDClub == 20 && ($IDServicio == 571 || $IDServicio == 549 || $IDServicio == 551)) {

                                    if ($IDServicio == 571) :
                                        if ($Hora >= '05:30:00' && $Hora <= '07:45:00') :
                                            if (!empty($IDBeneficiario)) {
                                                $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDSocio = '" . $IDBeneficiario . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' AND (Hora >= '05:30:00' AND Hora <= '07:45:00') " . $condicion_automatica);
                                            } else {
                                                $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( (IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "') or IDSocioBeneficiario='" . $IDSocio . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' AND (Hora >= '05:30:00' AND Hora <= '07:45:00') " . $condicion_automatica);
                                            }

                                        //$sql_reservas_dia = $dbo->query("SELECT * From ReservaGeneral Where IDSocio = '$IDSocio' and Fecha = '$Fecha' and IDEstadoReserva = '1' and Tipo <> 'Automatica' and IDServicio = 571 AND (Hora >= '05:30:00' AND Hora <= '07:45:00')");
                                        endif;

                                        if ($Hora >= '16:00:00' && $Hora <= '18:15:00') :
                                            if (!empty($IDBeneficiario)) {
                                                $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDSocio = '" . $IDBeneficiario . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' AND (Hora >= '16:00:00' AND Hora <= '18:15:00') " . $condicion_automatica);
                                            } else {
                                                $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( (IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "') or IDSocioBeneficiario='" . $IDSocio . "' ) and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' AND (Hora >= '16:00:00' AND Hora <= '18:15:00') " . $condicion_automatica);
                                            }
                                        //$sql_reservas_dia = $dbo->query("SELECT * From ReservaGeneral Where IDSocio = '$IDSocio' and Fecha = '$Fecha' and IDEstadoReserva = '1' and Tipo <> 'Automatica' and IDServicio = 571 AND (Hora >= '16:00:00' AND Hora <= '18:15:00')");
                                        endif;
                                    endif;
                                    $sql_reservas_dia = $dbo->query("SELECT * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDEstadoReserva = '1' and Tipo <> 'Automatica' and IDServicio in (549,551,14898,14899,571 )");
                                }

                                $total_reserva_socio = (int) $dbo->rows($sql_reservas_dia);



                                //Valido que el beneficiario al que se esta tomando la reserva ya no tenga otra reserva y asi cumplir lo de maximo de reservas por dia tambien
                                $condicion_beneficiario = "";
                                if (!empty($IDBeneficiario)) :

                                    if ($TipoBeneficiario != "Invitado" && (!empty($IDSocioBeneficiario) || !empty($IDBeneficiario))) :
                                        //$sql_reservas_dia_benef = $dbo->query("Select * From ReservaGeneral Where IDSocio = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDSocioBeneficiario= 0 " . $condicion_automatica);
                                        //$total_reserva_socio += (int) $dbo->rows($sql_reservas_dia_benef);

                                        $sql_reservas_dia_benef = $dbo->query("Select * From ReservaGeneral Where IDSocio <> '" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica);
                                        $total_reserva_socio += (int) $dbo->rows($sql_reservas_dia_benef);
                                    endif;

                                    //Invitado
                                    if (!empty($IDInvitadoBeneficiario)) :
                                        $sql_reservas_dia_benef = $dbo->query("Select * From ReservaGeneral Where IDInvitadoBeneficiario = '" . $IDInvitadoBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica);
                                        $total_reserva_socio += (int) $dbo->rows($sql_reservas_dia_benef);
                                    endif;

                                endif;
                                //Fin Validacion



                                //Valido si en la configuracion permite a un socio tomar otro turno dspues que cumpla el que tiene en el dia solo aplica si esta en el dia actual
                                //$datos_disponibilidad_otro = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array");
                                $datos_disponibilidad_otro = $datos_disponibilidad_reserva;

                                $PermiteReservaDespuesdeprimerturno = $datos_disponibilidad_otro["PermiteReservaCumplirTurno"];
                                if ($PermiteReservaDespuesdeprimerturno == "S" && $Fecha == date("Y-m-d") && $total_reserva_socio >= $MaximoReservaSocioServicio) :
                                    $TiempoDespues = (int) $datos_disponibilidad_otro["TiempoDespues"];
                                    $MedicionTiempoDespues = $datos_disponibilidad_otro["MedicionTiempoDespues"];
                                    $IntervaloTurno = $datos_disponibilidad_otro["Intervalo"];

                                    switch ($MedicionTiempoDespues):
                                        case "Dias":
                                            $minutos_posterior_turno = (60 * 24) * $TiempoDespues;
                                            break;
                                        case "Horas":
                                            $minutos_posterior_turno = 60 * $TiempoDespues;
                                            break;
                                        case "Minutos":
                                            $minutos_posterior_turno = $TiempoDespues;
                                            break;
                                        default:
                                            $minutos_posterior_turno = 0;
                                    endswitch;

                                    //Le sumo el intervalo del turno para calcular la siguiente hora que puede reservar despues de finalizar el turno
                                    $minutos_posterior_turno += (int) $IntervaloTurno;

                                    //Consulto cual es la utima que reserva que tiene en el dia para calcula con esa hora
                                    if (!empty($IDBeneficiario && !empty($IDSocioBeneficiario))) {
                                        // es para un beneficiario
                                        $sql_reserva_dia_hora = "SELECT * From ReservaGeneral Where (IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' or IDSocio = '" . $IDSocioBeneficiario . "') and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' Order by Hora Desc Limit 1";
                                    } else {
                                        $sql_reserva_dia_hora = "SELECT * From ReservaGeneral Where (IDSocio = '" . $IDSocio . "' ) and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDSocioBeneficiario = '" . (int) $IDBeneficiario . "' Order by Hora Desc Limit 1";
                                    }

                                    $result_reserva_dia_hora = $dbo->query($sql_reserva_dia_hora);
                                    $row_reserva_dia_hora = $dbo->fetchArray($result_reserva_dia_hora);
                                    $ultimo_turno_dia = $Fecha . " " . $row_reserva_dia_hora["Hora"];
                                    $hora_actual_peticion = date('Y-m-d H:i:s');
                                    $hora_volver_reservar = strtotime('+' . $minutos_posterior_turno . ' minute', strtotime($ultimo_turno_dia));
                                    //echo "Puede reservar a las " . date("Y-m-d H:i:s",$hora_volver_reservar);
                                    if (strtotime($hora_actual_peticion) >= $hora_volver_reservar) :
                                        $total_reserva_socio = 0;
                                    else :
                                        $mensaje_opcion_reserva = "Puede volver a reservar despues de: " . $TiempoDespues . " " . $MedicionTiempoDespues . " de cumplir la reserva del dia";
                                    endif;
                                endif;

                                // Si es Admin si puede reservar mas de un turno por dia
                                if (!empty($Admin)) :

                                    if ($IDClub != "12" && $IDClub != "8000") : //para el Rincon no permite asi sea admin
                                        //verifico que si pueda reservar mientras no sea la misma hora en el mismo servicio
                                        $sql_reservas_dia_hora = $dbo->query("Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and Hora = '" . $Hora . "' and IDServicioElemento <> '" . $IDElemento . "' and  IDSocioBeneficiario = '" . (int) $IDBeneficiario . "' and IDEstadoReserva = '1' and IDServicioElemento = '" . $IDElemento . "'");
                                        $total_reserva_socio_hora = (int) $dbo->rows($sql_reservas_dia_hora);
                                        if ($total_reserva_socio_hora > 0) :
                                            $total_reserva_socio = 100;
                                        else :
                                            $total_reserva_socio = 0;
                                        endif;
                                    endif;

                                    //$total_reserva_socio = 0;
                                    $UsuarioCreacion = "Starter";
                                else :
                                    if (!empty($IDUsuarioReserva)) {
                                        $UsuarioCreacion = "Empleado";
                                    } else {
                                        $UsuarioCreacion = "Socio";
                                    }

                                endif;

                                //Consulto el parametro en disponibilidad de cuantas veces puede reervar el socio el mismo servicio en el mismo dia
                                //$MaximoReservaSocioServicio = $dbo->getFields("Disponibilidad", "MaximoReservaDia", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                                $MaximoReservaSocioServicio = $datos_disponibilidad_reserva["MaximoReservaDia"];
                                //if($total_reserva_socio<1):

                                //Especial Bogota tenis si es fin semana y reserva cancha despues de 12:15
                                if ($IDClub == 72) :
                                    $dia_semana_reserva = date("w", strtotime($Fecha));
                                    if ($Hora >= "12:15:00" && ((($IDServicio == "8649") && ($dia_semana_reserva == "6" || $dia_semana_reserva == "0")))) :
                                        $MaximoReservaSocioServicio = 2;
                                    endif;
                                endif;
                                //FIN BTCC

                                if ($IDClub == 227) :
                                    $MaximoReservaSocioServicio = 10;
                                endif;

                                if (!empty($Admin) && $IDClub == 7) {
                                    $MaximoReservaSocioServicio = 100;
                                }

                                if ($IDClub == 44 && empty($Admin) && ($IDServicio == 3889 || $IDServicio == 3888)) :

                                    $ReservasPosibles = 1;
                                    $condicion_reserva_verif = " and Tipo <> 'Automatica'";
                                    $IDServicioValidar = "3889,3888";

                                    $fecha_hoy_semana = date("Y-m-d");
                                    $hora_hoy_semana = date("H:i:s");

                                    $year = date('Y', strtotime($Fecha));
                                    $week = date('W', strtotime($Fecha));
                                    $dia_reserva = date("w", strtotime($Fecha));
                                    $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
                                    $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo

                                    $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                                    $fecha_fin_valida = $fecha_domingo;

                                    $HoraParaValidar = date("H:i:s", strtotime("- 150 minute", strtotime($hora_hoy_semana)));

                                    if (!empty($IDBeneficiario)) {
                                        $SQLReservas = "SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '$IDBeneficiario' OR IDSocio = '$IDBeneficiario') AND (Hora > '$HoraParaValidar' AND Fecha = '$fecha_hoy_semana') and IDServicio IN ('$IDServicioValidar') and IDEstadoReserva = '1' AND Tee = '$Tee'" . $condicion_reserva_verif;
                                    } else {
                                        $SQLReservas = "SELECT * From ReservaGeneral Where ( IDSocio = '$IDSocio' AND IDSocioBeneficiario = 0 ) AND (Hora > '$HoraParaValidar' AND Fecha = '$fecha_hoy_semana') and IDServicio IN ('$IDServicioValidar') and IDEstadoReserva = '1'  AND Tee = '$Tee'" . $condicion_reserva_verif;
                                    }
                                    $sql_reservas_sem = $dbo->query($SQLReservas);
                                    $Reservas = $dbo->rows($sql_reservas_sem);

                                    if ($Reservas >= $ReservasPosibles) :

                                        $respuesta["message"] = "Solo puede reservas 2 horas 30 minutos despues de la ultima reserva que se tenga del día para estos servicios";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;
                                endif;

                                if ($IDClub == 44 && ($IDServicio == "3888" || $IDServicio == "3889")) {
                                    $IDServValidar = "3888,3889";
                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_rep = $dbo->query("SELECT * From ReservaGeneral Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServicio . ") and IDEstadoReserva = '1' AND Tee = '$Tee'");
                                    } else {
                                        $sql_reservas_rep = $dbo->query("Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '" . $IDBeneficiario . "') and Fecha='" . $Fecha . "' and IDServicio in (" . $IDServicio . ") and IDEstadoReserva = '1' AND Tee = '$Tee'");
                                    }
                                    $total_reservas_rep += (int) $dbo->rows($sql_reservas_rep);
                                    if ((int) $total_reservas_rep <= 1) :
                                        $total_reserva_socio = 0;
                                    endif;
                                }

                                // VALIDAMOS CANTIDAD DE RESERVAS POR TIPO SOCIO

                                if (!empty($IDBeneficiario)) {
                                    $TipoSocio = $datos_beneficiario[TipoSocio];
                                } else {
                                    $TipoSocio = $datos_socio[TipoSocio];
                                }

                                $SQLCantidad = "SELECT * FROM CantidadReservasTipoSocio WHERE IDClub = $IDClub AND TipoSocio = '$TipoSocio'";
                                $QRYCantidad = $dbo->query($SQLCantidad);
                                if ($dbo->rows($QRYCantidad) > 0) :

                                    $Anio = date('Y', strtotime($Fecha));
                                    $Mes = date('m', strtotime($Fecha));

                                    $condicion_reserva_verif = " AND Tipo <> 'Automatica' ";

                                    $Datos = $dbo->fetchArray($QRYCantidad);
                                    $CantidadDia = $Datos[NumeroReservaDia];
                                    $CantidadMes = $Datos[NumeroReservasMes];
                                    $CantidadAnno = $Datos[NumeroReservasAnno];

                                    // RESERVAS EN EL DIA
                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = '$IDBeneficiario' OR IDSocioBeneficiario = '$IDBeneficiario') and (Fecha='$Fecha') and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1	 ");
                                    } else {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = $IDSocio OR IDSocioBeneficiario = '$IDSocio') and (Fecha='$Fecha' ) and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1 ");
                                    }

                                    if ($dbo->row($sql_reservas_dia) > $CantidadDia) :
                                        $respuesta["message"] = "El Tipo Socio $TipoSocio supera el maximo de $CantidadDia en el dia";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;

                                    // RESERVAS EN EL MES
                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = '$IDBeneficiario' OR IDSocioBeneficiario = '$IDBeneficiario') and (YEAR(Fecha) = '$Anio' and MONTH(Fecha) = '$Mes') and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1	 ");
                                    } else {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = $IDSocio OR IDSocioBeneficiario = '$IDSocio') and (YEAR(Fecha) = '$Anio' and MONTH(Fecha) = '$Mes') and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1 ");
                                    }

                                    if ($dbo->row($sql_reservas_dia) > $CantidadMes) :
                                        $respuesta["message"] = "El Tipo Socio $TipoSocio supera el maximo de $CantidadMes en el mes";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;

                                    // RESERVAS EN EL AÑO

                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = '$IDBeneficiario' OR IDSocioBeneficiario = '$IDBeneficiario') and (YEAR(Fecha) = '$Anio') and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1	 ");
                                    } else {
                                        $sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where ( IDSocio = $IDSocio OR IDSocioBeneficiario = '$IDSocio') and (YEAR(Fecha) = '$Anio') and IDEstadoReserva = '1' " . $condicion_reserva_verif . " Limit 1 ");
                                    }

                                    if ($dbo->row($sql_reservas_dia) > $CantidadAnno) :
                                        $respuesta["message"] = "El Tipo Socio $TipoSocio supera el maximo de $CantidadAnno en el año";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;

                                endif;


                                if ($datos_disponibilidad_reserva["PermiteReservaComoInvitado"] == "S") {

                                    //se valida cuantas reservas tiene el socio como invitado para ver si cumple la regla
                                    if (!empty($IDBeneficiario)) {
                                        $sql_reservas_dia_invi = $dbo->query("SELECT RG.IDReservaGeneral From ReservaGeneralInvitado RGI, ReservaGeneral RG Where RG.IDReservaGeneral=RGI.IDReservaGeneral and ( RG.IDSocio = '" . $IDBeneficiario . "') and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' " . $condicion_automatica);
                                    } else {
                                        $sql_reservas_dia_invi = $dbo->query("SELECT RG.IDReservaGeneral From ReservaGeneralInvitado RGI, ReservaGeneral RG Where RG.IDReservaGeneral=RGI.IDReservaGeneral and ( RG.IDSocio = '" . $IDSocio . "') and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' " . $condicion_automatica);
                                    }
                                    $total_reserva_socio_invitado = (int) $dbo->rows($sql_reservas_dia_invi);

                                    if ($total_reserva_socio_invitado >= $datos_disponibilidad_reserva["MaximoReservaDiaInvitado"]) {
                                        $respuesta["message"] = "Usted ya tiene el número de reservas máxima para hora pico como invitado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    } else {
                                        //valido las reservas de los invitados
                                        $datos_invitado = json_decode($Invitados, true);
                                        if (count($datos_invitado) > 0) :
                                            foreach ($datos_invitado as $detalle_datos) :
                                                $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                if ($IDSocioInvitado > 0) {
                                                    $sql_reserva_inv = "SELECT RG.IDReservaGeneral From ReservaGeneralInvitado RGI, ReservaGeneral RG Where RG.IDReservaGeneral=RGI.IDReservaGeneral and ( RG.IDSocio = '" . $IDSocioInvitado . "') and (Fecha='" . $Fecha . "' ) and IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' " . $condicion_automatica;
                                                    $sql_reservas_dia_invi = $dbo->query($sql_reserva_inv);
                                                    $total_reserva_socio_invitado_reserva = (int) $dbo->rows($sql_reservas_dia_invi);
                                                    if ($total_reserva_socio_invitado_reserva >= $datos_disponibilidad_reserva["MaximoReservaDiaInvitado"]) {
                                                        $respuesta["message"] = "El invitado " . $detalle_datos["Nombre"] . " ya tiene el número de reservas máxima para hora pico como invitado";
                                                        $respuesta["success"] = false;
                                                        $respuesta["response"] = null;
                                                        return $respuesta;
                                                    }
                                                }

                                            endforeach;
                                        endif;
                                    }
                                }




                                if ($total_reserva_socio < $MaximoReservaSocioServicio) {

                                    if ($Repetir == "S") :

                                        //Consulto el limite de reservas que se pueda hacer para calcular la fecha final
                                        $MedicionRepeticion = $dbo->getFields("Disponibilidad", "MedicionRepeticion", "IDDisponibilidad = '" . $IDDisponibilidad . "'");
                                        $numero_repeticion = $dbo->getFields("Disponibilidad", "NumeroRepeticion", "IDDisponibilidad = '" . $IDDisponibilidad . "'");

                                        //$PermiteRepeticion = $dbo->getFields("Disponibilidad", "PermiteRepeticion", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                                        $PermiteRepeticion = $datos_disponibilidad_reserva["PermiteRepeticion"];

                                        switch ($MedicionRepeticion):
                                            case "Dia":
                                                $periodo_sumar = "day";
                                                $dato_sumar = 1;
                                                break;
                                            case "Semana":
                                                $periodo_sumar = "week";
                                                $dato_sumar = 1;
                                                break;
                                            case "Quincenal":
                                                $periodo_sumar = "day";
                                                $dato_sumar = 14;
                                                break;
                                            case "Mes":
                                                $periodo_sumar = "month";
                                                $dato_sumar = 1;
                                                break;
                                            default:
                                                $periodo_sumar = "day";
                                                $dato_sumar = 1;
                                        endswitch;

                                        //periodo a sumar dependiendo de lo que el socio escoja en el app
                                        switch ($Periodo):
                                            case "Dia":
                                                $periodo_sumar_app = "day";
                                                $dato_sumar = 1;
                                                break;
                                            case "Semana":
                                                $periodo_sumar_app = "week";
                                                $dato_sumar = 1;
                                                break;
                                            case "Quincenal":
                                                $periodo_sumar_app = "day";
                                                $dato_sumar = 14;
                                                break;
                                            case "Mes":
                                                $periodo_sumar_app = "month";
                                                $dato_sumar = 1;
                                                break;
                                            default:
                                                $periodo_sumar_app = "day";
                                                $dato_sumar = 1;
                                        endswitch;

                                        // Este sirve para establecer el limite deacuerdo al admin en el parametro limite de repeticion
                                        //$fechaFin = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;

                                        //Toma la fecha final de lo que seleccione el usuario en el app
                                        if (!empty($RepetirFechaFinal)) :

                                            $fechaFin = strtotime('+1 day', strtotime($RepetirFechaFinal));

                                            //VALIDAR QUE NO PERMITA MAS ALLA DE LO QUE SE CONFIGURO

                                            $FechaMaximaPermitida = strtotime('+' . $numero_repeticion . ' ' . $periodo_sumar, strtotime($Fecha));

                                            $FechaMaxima = date("Y-m-d", $FechaMaximaPermitida);
                                            if ($fechaFin > $FechaMaximaPermitida && empty($Admin) && empty($EspecialAnapoima)) :
                                                $respuesta["message"] = "Lo sentimos la fecha maxima de repeticion es maximo $numero_repeticion $MedicionRepeticion, es decir hasta $FechaMaxima";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                            endif;

                                        else :
                                            $fechaFin = strtotime($Fecha);
                                        endif;
                                    else :
                                        $numero_repeticion = 1;
                                        $fechaFin = strtotime($Fecha);
                                        $periodo_sumar = "day";
                                        $periodo_sumar_app = "day";
                                        $dato_sumar = 1;
                                    endif;

                                    $fechaInicio = strtotime($Fecha);
                                    //$fechaFin=strtotime($fecha_fin_reserva );
                                    //echo date("Y-m-d",$fechaFin);
                                    //exit;

                                    if (!empty($IDBeneficiario) && !empty($TipoBeneficiario)) :
                                        if ($TipoBeneficiario == "Invitado") {
                                            $IDInvitadoBeneficiario = $IDBeneficiario;
                                        } elseif ($TipoBeneficiario == "Socio") {
                                            $IDSocioBeneficiario = $IDBeneficiario;
                                        }

                                    endif;



                                    for ($contador_fecha = $fechaInicio; $contador_fecha <= $fechaFin; $contador_fecha += 86400) :

                                        $flag_reserva_cancha_clase = 0;
                                        // verifico que todavia este disponible la reserva

                                        if (!empty($Tee)) :
                                            $condicion_tee = " and Tee = '" . $Tee . "'";
                                        endif;

                                        // Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia
                                        $cupo_total = "S"; // ya no hay cupos
                                        //$cupos_disponibilidad = $dbo->getFields("Disponibilidad", "Cupos", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                                        $cupos_disponibilidad = $datos_disponibilidad_reserva["Cupos"];
                                        $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array");
                                        if ((int) $cupos_disponibilidad > 1) :
                                            //Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
                                            $cupos_reservados = SIMWebService::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $Hora, "S");
                                            //Valido si todavia existe cupo en esta hora
                                            if ($cupos_reservados <= $cupos_disponibilidad) :
                                                $cupo_total = "N"; // aun hay cupos disponibles
                                            endif;
                                            $numero_inscripcion = rand(0, 999999);
                                        else :
                                            $numero_inscripcion = 0;
                                        endif;

                                        //Verifico que los cupos libres mas los invitados no supere el maximo permitido
                                        if ((int) $cupos_disponibilidad > 1) {

                                            // SI SE TIENE LA OPCION DE INVITADOS SALONES HAY QUE SUMAR TAMBIEN PARA QUE NO SOBRE PASE EL CUPO
                                            if ($datos_servicio["MaximoInvitadosSalon"] > 0) :
                                                $cupos_reservados += $CantidadInvitadoSalon;
                                            endif;

                                            $suma_cupos = (int) $cupos_reservados + count($datos_invitado);
                                            if ($suma_cupos > $cupos_disponibilidad) {
                                                $cup_dispo = (int) $cupos_disponibilidad - (int) $cupos_reservados;
                                                $respuesta["message"] = "Lo sentimos solo queda " . $cup_dispo . " cupos disponibles \ncupos reservados:" . $cupos_reservados . "\nCupos posibles:" . $cupos_disponibilidad;
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                            }
                                        }
                                        //FIN Verifico que los cupos libres mas los invitados no supere el maximo permitido

                                        $id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee ");




                                        if (empty($id_reserva_disponible) || $cupo_total == "N") :


                                            $datos_invitado = json_decode($Invitados, true);

                                            //Verifico que el socio no este como invitado en el mismo servicio en otra hora
                                            if ($Fecha == date("Y-m-d")) :
                                                $hora_actual_sistema_valida = date("H:i:s");
                                            else :
                                                $hora_actual_sistema_valida = "01:00:00";
                                            endif;

                                            if (!empty($IDBeneficiario)) {
                                                $IDSociovalidar = $IDBeneficiario;
                                            } else {
                                                $IDSociovalidar = $IDSocio;
                                            }

                                            $sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSociovalidar . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.Hora >='" . $hora_actual_sistema_valida . "' and RG.IDServicio = '" . $IDServicio . "' ORDER BY IDReservaGeneralInvitado Desc ";
                                            $qry_socio_grupo = $dbo->query($sql_socio_grupo);

                                            if ($dbo->rows($qry_socio_grupo) > 0 && ($MaximoReservaSocioServicio <= 1 || $dbo->rows($qry_socio_grupo) >= $MaximoReservaSocioServicio) && empty($Admin) && $datos_disponibilidad_reserva["PermiteReservaComoInvitado"] != "S") :
                                                $nombre_socio_invitado = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSociovalidar . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSociovalidar . "'"));
                                                $respuesta["message"] = $nombre_socio_invitado . ", ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique.";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                                exit;
                                            endif;





                                            // Si es golf verifico que los invitado no este en mas de un grupo el mismo dia
                                            if (count($datos_invitado) > 0 && $MaximoReservaSocioServicio <= 1 && $datos_disponibilidad_reserva["PermiteReservaComoInvitado"] != "S") :
                                                foreach ($datos_invitado as $detalle_datos) :
                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];

                                                    if (!empty($IDSocioInvitado)) :
                                                        $respuesta_valida_invitado = SIMWebService::verificar_socio_grupo($IDClub, $IDSocioInvitado, $Fecha, $IDServicio, $Hora);
                                                        if ($respuesta_valida_invitado == 1) :
                                                            $nombre_socio_invitado = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitado . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitado . "'");
                                                            $respuesta["message"] = "El invitado: " . utf8_encode($nombre_socio_invitado) . ", solo puede estar en un grupo por dia";
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                            exit;
                                                        endif;
                                                    endif;
                                                endforeach;
                                            endif;

                                            // Si el servicio es una clase y necesita reservar cancha
                                            $id_servicio_cancha = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                                            if ($id_servicio_cancha > 0) :
                                                // Consulto el servicio del club asociado a este servicio maestro
                                                $IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");
                                                // Valido si existe una cancha disponible en el horario de la clase
                                                $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub, $IDServicioCanchaClub, $Fecha, $Hora, $IDElemento, $IDTipoReserva, $EdadSocio);

                                                if (empty($IDElemento_cancha)) :
                                                    $respuesta["message"] = "Lo sentimos no hay una cancha disponible para tomar la clase en este horario ";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                else :
                                                    $flag_reserva_cancha_clase = 1;
                                                endif;
                                            endif;

                                            /*
                                            //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
                                            //Si el servicio maestro tiene definido permitir turnos seguidos cuando los invitados sean mas de X personas
                                            $numero_para_reservar_turnos = $dbo->getFields( "ServicioMaestro" , "InvitadoTurnos" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
                                            if( ( (int)$numero_para_reservar_turnos>0 ) && count($datos_invitado)>=$numero_para_reservar_turnos):
                                            //if($id_servicio_maestro==14): //Tennis
                                            $cantidad_turnos = 1; // Para validar los siguientes X turnos esten disponibles
                                            $array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_disponible($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );
                                            if(count($array_hora_siguiente_turno_diponible)!=$cantidad_turnos):
                                            $respuesta["message"] = "Se necesitan ".$cantidad_turnos." turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otro opcion.";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = NULL;
                                            return $respuesta;
                                            else:
                                            $flag_separa_siguiente_turno=1;
                                            endif;
                                            endif;
                                            */



                                            //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
                                            if (!empty($IDTipoReserva)) :
                                                $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");
                                                $cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];
                                                $cantidad_minima_participantes = (int) $datos_tipo_reserva["MinimoParticipantes"];

                                                //Consulto cuantos turnos automaticos se deben separar , pj en salones despues de una reserva se toma un turno mas para que se pueda realizar el aseo
                                                $TurnoMantenimiento = (int) $datos_servicio["TurnoMantenimiento"];
                                                $cantidad_turnos += $TurnoMantenimiento;
                                                $TotalInvitados_mas_aux = (int) count($datos_invitado) + (int) $cantidad_auxiliar;
                                                if (!empty($Admin) || (((int) $TotalInvitados_mas_aux >= (int) $cantidad_minima_participantes) || $cantidad_minima_participantes == 0)) {
                                                    // valido que no vengas mas de los participantes que es necesario
                                                    //if ((((int)$TotalInvitados_mas_aux) == (int) $cantidad_minima_participantes) || (int) $cantidad_minima_participantes == 0):
                                                    if (((int) $cantidad_turnos > 1)) :
                                                        //$cantidad_turnos-=1; // Quito uno para que no cuente la reserva primera
                                                        // Separo las reservas
                                                        $array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos);
                                                        if ((count($array_hora_siguiente_turno_diponible) != (int) ($cantidad_turnos - 1) || !is_array($array_hora_siguiente_turno_diponible)) && (int) $cupos_disponibilidad <= 1) :
                                                            $respuesta["message"] = "Se necesitan " . $cantidad_turnos . ". turnos mas seguidos y el siguiente turno no esta disponible. Por favor seleccione otra opcion! " . $cupos_disponibilidad;
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        else :
                                                            $flag_separa_siguiente_turno = 1;
                                                        endif;
                                                    endif;
                                                    //else:
                                                    //$respuesta["message"] = "Lo sentimos, el maximo numero de invitados debe ser de " . $cantidad_minima_participantes;
                                                    //$respuesta["success"] = false;
                                                    //$respuesta["response"] = null;
                                                    //return $respuesta;
                                                    //endif;
                                                } else {
                                                    $respuesta["message"] = "Lo sentimos, el minimo numero de invitados debe ser de: " . $cantidad_minima_participantes;
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }

                                                //VALIDO LA CANTIDAD MAXIMA DE PERSONAS EN LA RESERVA
                                                $cantidad_maxima_participantes = (int) $datos_tipo_reserva["MaximoParticipantes"];
                                                $TotalPersonas = (int) count($datos_invitado);

                                                if ($TotalPersonas > $cantidad_maxima_participantes && $cantidad_maxima_participantes != 0) :
                                                    $respuesta["message"] = "Lo sentimos, el maximo numero de invitados para este tipo de reserva debe ser de " . $cantidad_maxima_participantes;
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;
                                            endif;



                                            //Si turnos es mayor a 1 verifico que los siguientes turnos esten disponibles y los reservo
                                            if ((int) $NumeroTurnos > 1) :

                                                if ($id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30) : //Golf
                                                    $array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, $Tee, "Reserva", "");
                                                else :
                                                    $array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos);
                                                endif;

                                                //Cuando es por grupos busco aleatoriamente de los invitados los socios que quedaran como cabeza de grupo
                                                $array_cabeza_grupo = SIMWebService::busca_cabeza_grupo($Invitados, $NumeroTurnos, $IDSocio);

                                                if (count($array_disponible) != $NumeroTurnos) :
                                                    $respuesta["message"] = "Se necesitan: " . $NumeroTurnos . " turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otra opcion.";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                else :
                                                    $contador_turno = 0;
                                                    // separo los siguientes turnos disponibles menos el primero que se realiza en el siguiente proceso
                                                    foreach ($array_disponible as $key_hora => $dato_hora) :
                                                        if ($contador_turno > 0) :

                                                            // VALIDAR INVITADO EXTERNO EN MODULO INVITADOS
                                                            $datos_invitado = json_decode($Invitados, true);
                                                            if (count($datos_invitado) > 0) :
                                                                foreach ($datos_invitado as $detalle_datos) :
                                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                                    $ValoresFomulario = json_encode($detalle_datos["CamposDinamicos"]);

                                                                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S" && $datos_servicio["ValidarInvitadoExternoModuloInvitado"] == "S") :
                                                                        $invitacionExterna = SIMServicioReserva::validar_invitados($IDClub, $IDSocio, $CedulaSocioInvitado, $Fecha);
                                                                        if (!$invitacionExterna["success"]) {
                                                                            $respuesta["success"] = false;
                                                                            $respuesta["message"] = $invitacionExterna["message"];
                                                                            $respuesta["response"] = null;

                                                                            return $respuesta;
                                                                        }
                                                                    endif;
                                                                endforeach;
                                                            endif;

                                                            $socios_cabeza = 0;
                                                            $contador_socio_cabeza_real = 0;
                                                            $IDSocioCabeza = $array_cabeza_grupo[($contador_turno - 1)];
                                                            if (empty($IDSocioCabeza)) {
                                                                $IDSocioCabeza = $IDSocio;
                                                            }

                                                            // Registro los socios cabeza como ingresados para que no queden como invitados
                                                            foreach ($array_cabeza_grupo as $id_socio_cabeza => $datos_socio_cabeza) :
                                                                $array_invitado_agregado[] = $datos_socio_cabeza;
                                                                if ($IDSocio != $datos_socio_cabeza) :
                                                                    $contador_socio_cabeza_real++;
                                                                endif;
                                                            endforeach;

                                                            //Recorre los invitados para crear las invitaciones
                                                            $datos_invitado = json_decode($Invitados, true);
                                                            if (count($datos_invitado) > 0) :
                                                                foreach ($datos_invitado as $detalle_datos) :
                                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                                    $ValoresFomulario = json_encode($detalle_datos["CamposDinamicos"]);

                                                                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)) :
                                                                        if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S" && $datos_servicio["CrearInvitacionExterno"] == "S") {
                                                                            $invitacionExterna = SIMServicioReserva::CrearInvitacionExterno($IDClub, $IDSocio, $CedulaSocioInvitado, $NombreSocioInvitado, $CorreoSocioInvitado, $Fecha, $Fecha, $IDServicio, $ValoresFomulario);
                                                                            if (!$invitacionExterna["success"]) {
                                                                                $respuesta["success"] = false;
                                                                                $respuesta["message"] = $invitacionExterna["message"];
                                                                                $respuesta["response"] = null;
                                                                                return $respuesta;
                                                                            }
                                                                        }
                                                                    endif;
                                                                endforeach;
                                                            endif;

                                                            $sql_inserta_reserva_turno = $dbo->query("INSERT Into ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Tee, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,IP,IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
																																					Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocioCabeza . "', '" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "',
																																					'" . $dato_hora . "','" . $Tee . "', '" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','" . $Altitud . "','" . $Longitud . "','" . $NombreSocioReserva . "','" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','WebService Automatica','" . $FechaHoraSistemaActual . "')");

                                                            if (!$sql_inserta_reserva_turno) :
                                                                $respuesta["message"] = "No se pudo realizar la reserva, intente de nuevo (m1)";
                                                                $respuesta["success"] = false;
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            endif;

                                                            $id_reserva_general_turno = $dbo->lastID();

                                                            //Inserto los invitados
                                                            $datos_invitado_turno = json_decode($Invitados, true);
                                                            //Reparto los jugadores por turnos
                                                            $total_invitados_turno = count($datos_invitado_turno);

                                                            if ($contador_socio_cabeza_real >= 1) {
                                                                $socios_cabeza = $contador_socio_cabeza_real + 1;
                                                            } else {
                                                                $socios_cabeza = 0;
                                                            }

                                                            $invitados_por_turno = ((int) (($total_invitados_turno + 1) - $socios_cabeza) / $NumeroTurnos);

                                                            $contador_invitado_agregado = 1;
                                                            if (count($datos_invitado_turno) > 0) :
                                                                $cantidadExternos = 0;
                                                                foreach ($datos_invitado_turno as $detalle_datos_turno) :
                                                                    $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
                                                                    $NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
                                                                    $CorreoSocioInvitadoTurno = $detalle_datos_turno["Correo"];
                                                                    $CedulaSocioInvitadoTurno = $detalle_datos_turno["Cedula"];

                                                                    if ($IDSocioInvitadoTurno == 0 || $IDSocioInvitadoTurno == "") {
                                                                        $codigocort = $detalle_datos['CodigoCortesia'];
                                                                        $verificarcodigocortesiareservas = SIMWebServiceReservas::verificarcodigocortesiareservas($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDElemento, $Fecha, $Hora, $IDClub, $codigocort);
                                                                        if ($verificarcodigocortesiareservas['success'] !== true) {
                                                                            $cantidadExternos++;
                                                                        } else {
                                                                            $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDReservaGeneral = '$id_reserva_general_turno' Where  Codigo = '$codigocort'";
                                                                            $dbo->query($sql_actualiza_codigo);
                                                                        }
                                                                        $IDSocioInvita = $IDSocioCabeza;
                                                                        $Externo = "S";
                                                                    }
                                                                    // Guardo los invitados socios o externos
                                                                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
                                                                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)) :
                                                                        if ($contador_invitado_agregado <= (int) $invitados_por_turno) :
                                                                            $sql_inserta_invitado_turno = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre, Cedula, Correo,Externo,IDSocioInvita)
																										values ('" . $id_reserva_general_turno . "','" . $IDSocioInvitadoTurno . "', '" . $NombreSocioInvitadoTurno . "', '" . $CedulaSocioInvitadoTurno . "', '" . $CorreoSocioInvitadoTurno . "','" . $Externo . "','" . $IDSocioInvita . "')");
                                                                            $id_invitado_reserva_general_turno = $dbo->lastID();

                                                                            //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                                                                            $datos_respuesta = $detalle_datos_turno["Adicionales"];
                                                                            $SumaEspeciales = 0;
                                                                            if (count($datos_respuesta) > 0) :
                                                                                foreach ($datos_respuesta as $detalle_carac) :
                                                                                    $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                                                                    $ValoresCarac = $detalle_carac["Valores"];
                                                                                    $ValoresID = $detalle_carac["ValoresID"];
                                                                                    $Total = $detalle_carac["Total"];
                                                                                    $SumaEspeciales += $Total;

                                                                                    if (!empty($IDPropiedadProducto)) {
                                                                                        $array_id_carac = explode(",", $ValoresID);
                                                                                        if (count($array_id_carac) > 0) {
                                                                                            foreach ($array_id_carac as $id_carac) {
                                                                                                $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $id_reserva_general_turno . "','" . $id_invitado_reserva_general_turno . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                endforeach;
                                                                                SIMServicioReserva::LiberarOcupacionElementoAdicional($consecutivo, "INV");
                                                                            endif;
                                                                            //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                                                                            //INSERTO LOS CAMPOS DE PREGUNTAS ADICIONALES POR INVITADO SI APLICA

                                                                            $datos_campos = $detalle_datos_turno[CamposDinamicos];
                                                                            if (count($datos_campos) > 0) :
                                                                                foreach ($datos_campos as $campos) :
                                                                                    $IDCampoInvitadoExterno = $campos[IDCampoInvitadoExterno];
                                                                                    $Valor = $campos[Valor];
                                                                                    $SQL = "INSERT INTO RespuestasCampoInvitadoExterno (IDCampoInvitadoExterno, IDReservaGeneralInvitado, Valor) VALUES ('$IDCampoInvitadoExterno','$id_invitado_reserva_general_turno','$Valor')";
                                                                                    $dbo->query($SQL);
                                                                                endforeach;
                                                                            endif;

                                                                            $array_invitado_agregado[] = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
                                                                            //Envio push al invitado para notificarle si es un invitado socio
                                                                            if (!empty($IDSocioInvitadoTurno)) {
                                                                                SIMUtil::push_socio_invitado($IDClub, $id_reserva_general_turno, $IDSocioInvitadoTurno);
                                                                            }
                                                                        endif;
                                                                    else :
                                                                        $contador_invitado_agregado = 0;
                                                                    endif;
                                                                    $contador_invitado_agregado++;
                                                                endforeach;
                                                            endif;
                                                            // Borro la reserva separada
                                                            $sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $dato_hora . "' and IDEstadoReserva  = 3");
                                                        endif;

                                                        $contador_turno++;
                                                    endforeach;
                                                //$respuesta["message"] = "Guardado";
                                                //$respuesta["success"] = true;
                                                //$respuesta["response"] = $id_reserva_general;
                                                //return $respuesta;
                                                endif;
                                            endif;

                                            // Si se va a reservas mas turnos seguidos y la validacion fue exitosa borro las separacion hechas
                                            if ($flag_separa_siguiente_turno == 1 && count($array_hora_siguiente_turno_diponible) > 0) :
                                                foreach ($array_hora_siguiente_turno_diponible as $Hora_siguiente) :
                                                    // Borro la reserva separada
                                                    $sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and IDEstadoReserva  = 3");
                                                    // Borro la reserva separada automaticas
                                                    $sql_inserta_reserva_aut = $dbo->query("Delete From ReservaGeneralAutomatica Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and IDEstadoReserva  = 3");

                                                endforeach;
                                            endif;




                                            //Especial para clubes que solo se permite x reserva por accion por dia
                                            $maximo_turnos_dia = $datos_servicio["NumeroReservasPermitidaAccion"];
                                            if ((int) $maximo_turnos_dia > 0 && empty($Admin)) :
                                                unset($array_socio);
                                                $condicion_reserva_verif = " and Tipo <> 'Automatica'";
                                                $ReservasPermitidaSemana = $maximo_turnos_dia;
                                                // Valido tambien los de la misma acción
                                                $accion_padre = $datos_socio["AccionPadre"];
                                                $accion_socio = $datos_socio["Accion"];

                                                if (empty($accion_padre)) : // Es titular
                                                    $array_socio[] = $IDSocio;
                                                    $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                                                    $result_nucleo = $dbo->query($sql_nucleo);
                                                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                                                        $array_socio[] = $row_nucleo["IDSocio"];
                                                    endwhile;
                                                else :
                                                    $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
                                                    $result_nucleo = $dbo->query($sql_nucleo);
                                                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                                                        $array_socio[] = $row_nucleo["IDSocio"];
                                                    endwhile;
                                                endif;
                                                if (count($array_socio) > 0) :
                                                    $id_socio_nucleo = implode(",", $array_socio);
                                                endif;

                                                //$sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (".$id_socio_nucleo.") and (Fecha='".$Fecha."' and Hora >= '".date("H:i:s")."' )  and IDServicio = '".$IDServicio."' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
                                                $sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . $Fecha . "')  and IDServicio = '" . $IDServicio . "' and (IDEstadoReserva = '1') " . $condicion_reserva_verif);
                                                $total_reservas_dia_nucleo = $dbo->rows($sql_reservas_sem);
                                                if ((int) $total_reservas_dia_nucleo >= (int) $ReservasPermitidaSemana) :
                                                    $respuesta["message"] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por dia por accion ";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;

                                            endif;



                                            //Especial para clubes que solo se permite x reserva  por dia por socio
                                            $maximo_turnos_dia_socio = $datos_servicio["NumeroReservasPermitidaSocioDia"];
                                            if ((int) $maximo_turnos_dia_socio > 0 && empty($Admin)) :
                                                if (!empty($IDBeneficiario))
                                                    $sql_reservas_dia_soc = $dbo->query("SELECT IDReservaGeneral From ReservaGeneral Where IDSocio = '$IDBeneficiario'  and (Fecha='" . $Fecha . "')  and IDServicio = '" . $IDServicio . "' and (IDEstadoReserva = '1') " . $condicion_reserva_verif);
                                                else
                                                    $sql_reservas_dia_soc = $dbo->query("SELECT IDReservaGeneral From ReservaGeneral Where IDSocio = '$IDSocio'  and (Fecha='" . $Fecha . "')  and IDServicio = '" . $IDServicio . "' and (IDEstadoReserva = '1') " . $condicion_reserva_verif);

                                                $total_reservas_dia_soc = $dbo->rows($sql_reservas_dia_soc);
                                                if ((int) $total_reservas_dia_soc >= (int) $maximo_turnos_dia_socio) :
                                                    $respuesta["message"] = "Lo sentimos solo se permiten " . $maximo_turnos_dia_socio . " reservas por dia por socio!. ";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;
                                            endif;

                                            //Vuelvo a validar que nadie haya tomado la reserva nuevamente por los casos de milisegundos, para esto creo un espera de x segundos
                                            $suma_rand = rand(0, 1);
                                            $rand_seg = rand(1, 1) + $suma_rand;
                                            sleep($rand_seg);

                                            $id_reserva_disponible2 = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee ");
                                            if (!empty($id_reserva_disponible2) && ((int) $cupos_disponibilidad <= 1 || empty($cupos_disponibilidad))) :
                                                $respuesta["message"] = "Lo sentimos, la reserva ya fue tomada";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                            endif;



                                            if (!empty($IDAuxiliar)) :
                                                $suma_rand = rand(0, 2);
                                                $rand_seg = rand(1, 1) + $suma_rand;
                                                sleep($rand_seg);
                                                //Verifico de nuevo que el auxiliar boleador no esté reservado

                                                $id_reserva_aux = "";
                                                $sql_aux = "Select *
																						From ReservaGeneral
																						Where IDAuxiliar = '" . $IDAuxiliar . "'
																						and Fecha = '" . $Fecha . "'
																						and Hora = '" . $Hora . "'
																						and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)";
                                                $r_aux = $dbo->query($sql_aux);
                                                while ($row_aux = $dbo->fetchArray($r_aux)) :
                                                    if ($row_aux["IDSocio"] == $IDSocio && $row_aux["IDSocioBeneficiario"] == (int) $IDBeneficiario) :
                                                        $id_reserva_aux = "";
                                                    else :
                                                        $id_reserva_aux = "1";
                                                    endif;
                                                endwhile;

                                                /*$id_reserva_aux = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and (IDSocio <> '".$IDSocio."' and IDSocioBeneficiario <> '".$IDSocioBeneficiario."') and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );
                                                */
                                                /*
                                                $respuesta["message"] = $sql_aux;
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = NULL;
                                                return $respuesta;
                                                */

                                                $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora";
                                                if (!empty($id_reserva_aux)) :
                                                    $respuesta["message"] = $mensaje_auxiliar_no_dispo;
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;

                                                //Cuando viene con array
                                                foreach ($array_id_auxiliar as $id_auxiliar_valida) :
                                                    if ((int) $id_auxiliar_valida >= 0) :
                                                        $id_auxiliar_valida = $id_auxiliar_valida . ",";
                                                        $id_reserva_aux = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDAuxiliar like '" . $id_auxiliar_valida . "%' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDSocio <> '" . $IDSocio . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)");
                                                        $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora.";
                                                        if (!empty($id_reserva_aux)) :
                                                            $respuesta["message"] = $mensaje_auxiliar_no_dispo;
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        endif;

                                                    endif;
                                                endforeach;

                                            endif;



                                            //Verifico que el elemnto no hay sido reservado a esta misma hora en otro servicio
                                            $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio($IDElemento, "", $IDClub);
                                            //Si el elemento ya tiene otra reserva en otro servicio marco esta como ya revarda asi tenga cupos disponibles
                                            $array_otro_elemento = explode(",", $condicion_multiple_elemento);
                                            //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
                                            $suma_rand = rand(0, 1);
                                            $rand_seg = rand(1, 1) + $suma_rand;
                                            sleep($rand_seg);
                                            if (count($array_otro_elemento) > 1) : //Si es mas de 1 quiere decir que el elemento esta en mas de un servicio y hago la verificacion
                                                foreach ($array_otro_elemento as $id_elemento_multiple) :
                                                    if ($id_elemento_multiple != $IDElemento && !empty($id_elemento_multiple)) :
                                                        $sql_reserva_elemento_multp = "SELECT * FROM ReservaGeneral WHERE IDServicioElemento in (" . $id_elemento_multiple . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 ) and Hora = '" . $Hora . "' ";
                                                        $qry_reserva_elemento_mult = $dbo->query($sql_reserva_elemento_multp);
                                                        if ($dbo->rows($qry_reserva_elemento_mult) > 0 && $cupos_disponibilidad <= 1) :
                                                            $respuesta["message"] = "La persona o elemento seleccionado ya fue reservado en otro servicio en esta misma fecha/hora";
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        endif;
                                                    endif;
                                                endforeach;
                                            endif;



                                            // VALIDAR INVITADO EXTERNO EN MODULO INVITADOS
                                            $datos_invitado = json_decode($Invitados, true);
                                            if (count($datos_invitado) > 0) :
                                                foreach ($datos_invitado as $detalle_datos) :
                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                    if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S" && $datos_servicio["ValidarInvitadoExternoModuloInvitado"] == "S") :
                                                        $invitacionExterna = SIMServicioReserva::validar_invitados($IDClub, $IDSocio, $CedulaSocioInvitado, $Fecha);
                                                        if (!$invitacionExterna["success"]) {
                                                            $respuesta["success"] = false;
                                                            $respuesta["message"] = $invitacionExterna["message"];
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        }
                                                    endif;
                                                endforeach;
                                            endif;



                                            // SE ESTA INSERTANDO EL IDCaddieSocio DE SER EL CASO
                                            //Guardo la reserva maestra
                                            //     echo "INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
                                            //     Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, IP, IdentificadorServicio,ConsecutivoServicio,IDCaddie,UsuarioTrCr, FechaTrCr)
                                            // Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "',
                                            // '" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','" . $Altitud . "' , '" . $Longitud . "','" . $NombreSocioReserva . "',
                                            // '" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','$IDCaddieSocio','" . $UsuarioCreacion . "','" . $FechaHoraSistemaActual . "')";

                                            // Si esta hablitado el Crear invitación registramos en Invitados
                                            // Crear invitaciones en el modulo invitados
                                            if ($CrearInvitacionExterno == 'S') {
                                                require_once LIBDIR . "SIMWebServiceAccesos.inc.php";
                                                $datos_invitado = json_decode($Invitados, true);
                                                if (count($datos_invitado) > 0) :

                                                    foreach ($datos_invitado as $detalle_datos) :
                                                        $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                        $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                        $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                        $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                        $FechaNacimientoSocioInvitado = $detalle_datos["FechaNacimiento"];
                                                        $IDCaddieInvitado = $detalle_datos["IDCaddieInvitado"]; //SE INSERTA EL CADDIE DE SER EL CASO

                                                        if ($IDSocioInvitado <= 0 && $CedulaSocioInvitado > 0) :

                                                            $respuesta = SIMWebServiceAccesos::set_invitado($IDClub, $IDSocio, $CedulaSocioInvitado, $NombreSocioInvitado, $Fecha, $Campos, $IDUsuario);
                                                            if ($respuesta['success'] == false) {
                                                                $respuesta["message"] = $respuesta['message'];
                                                                $respuesta["success"] = false;
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            }
                                                        endif;
                                                    endforeach;
                                                endif;
                                            }
                                            //Fin Crear invitaciones en el modulo invitados


                                            //Valido cupos hora de nuevo
                                            if ($CuposporHora > 0) :
                                                if ($IDClub == 77) {
                                                    $condicion_tipo = " and IDServicioTipoReserva = '" . $IDTipoReserva . "' ";
                                                }



                                                //Nuevo
                                                //Especial cusezar si es sala vip se bloquea sala 1 y 2, si esta reservada 1 o 2 se bloque la vip
                                                if ($IDServicio == 44907 || $IDServicio == 53181 || $IDServicio == 52968) {

                                                    switch ($IDServicio) {
                                                        case "44907";
                                                            $ElementoPrincipal = 17630;
                                                            $ElementoSecundario1 = 19059;
                                                            $ElementoSecundario2 = 17631;
                                                            break;
                                                        case "52968";
                                                            $ElementoPrincipal = 20132;
                                                            $ElementoSecundario1 = 20130;
                                                            $ElementoSecundario2 = 20131;
                                                            break;
                                                        case "53181";
                                                            $ElementoPrincipal = 20129;
                                                            $ElementoSecundario1 = 20127;
                                                            $ElementoSecundario2 = 20128;
                                                            break;
                                                    }


                                                    $TodosElementos = $ElementoPrincipal . "," . $ElementoSecundario1 . "," . $ElementoSecundario2;

                                                    if ($IDElemento == $ElementoPrincipal) {
                                                        $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' and IDServicioElemento in (" . $TodosElementos . ") " . $condicion_tipo;
                                                        $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                        if ((int) $dbo->rows($result_reserva_servicio) > 0 &&  $IDElemento == 17630) {
                                                            $CuposporHora = 1;
                                                        }
                                                    } elseif ($IDElemento == $ElementoSecundario1 || $IDElemento == $ElementoSecundario2) {
                                                        $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $horaInicial . "' and IDServicioElemento in (" . $ElementoPrincipal . ") " . $condicion_tipo;
                                                        $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                        if ((int) $dbo->rows($result_reserva_servicio) > 0) {
                                                            $CuposporHora = 1;
                                                        }
                                                    }
                                                } else {
                                                    //Consulto cuantas reservas hay en esta hora en este servicio
                                                    $sql_reserva_servicio = "SELECT IDReservaGeneral FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' AND Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '" . $Hora . "' and IDSocio <> '" . $IDSocio . "' " . $condicion_tipo;
                                                    $result_reserva_servicio = $dbo->query($sql_reserva_servicio);
                                                    $total_reservas_hora = (int) $dbo->rows($result_reserva_servicio) + $num_otras_reservas;
                                                }
                                                //Fin Nuevo

                                                if ($CuposporHora > 0 && $total_reservas_hora >= $CuposporHora) {
                                                    $respuesta["message"] = "Lo sentimos, se llego al maximo de reservas por hora.";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }
                                            endif;



                                            $sql_inserta_reserva = $dbo->query("INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
																					Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, IP, IdentificadorServicio,ConsecutivoServicio,IDCaddie,UsuarioTrCr, FechaTrCr)
																				Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "',
																				'" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','" . $Altitud . "' , '" . $Longitud . "','" . $NombreSocioReserva . "',
																				'" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','$IDCaddieSocio','" . $UsuarioCreacion . "','" . $FechaHoraSistemaActual . "')");

                                            if (!$sql_inserta_reserva) :
                                                $respuesta["message"] = "No se pudo realizar la reserva, intente de nuevo (m2)";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                            endif;

                                            $id_reserva_general = $dbo->lastID();


                                            //Recorre los invitados para crear las invitaciones
                                            $datos_invitado = json_decode($Invitados, true);
                                            if (count($datos_invitado) > 0) :
                                                foreach ($datos_invitado as $detalle_datos) :
                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                    $ValoresFomulario = json_encode($detalle_datos["CamposDinamicos"]);

                                                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)) :
                                                        if (($IDSocioInvitado == 0 || $IDSocioInvitado == "") && $datos_servicio["PermiteInvitadoExternoCedula"] == "S" &&  $datos_servicio["CrearInvitacionExterno"] == "S") {
                                                            $invitacionExterna = SIMServicioReserva::CrearInvitacionExterno($IDClub, $IDSocio, $CedulaSocioInvitado, $NombreSocioInvitado, $CorreoSocioInvitado, $Fecha, $Fecha, $IDServicio, $ValoresFomulario);
                                                            if (!$invitacionExterna["success"]) {
                                                                $respuesta["success"] = false;
                                                                $respuesta["message"] = $invitacionExterna["message"];
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            }
                                                        }
                                                    endif;
                                                endforeach;
                                            endif;




                                            $datos_invitado = json_decode($Invitados, true);

                                            if (count($datos_invitado) > 0) :
                                                $cantidadExternos = 0;

                                                foreach ($datos_invitado as $detalle_datos) :
                                                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                                                    $NombreSocioInvitado = $detalle_datos["Nombre"];
                                                    $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                                    $CorreoSocioInvitado = $detalle_datos["Correo"];
                                                    $FechaNacimientoSocioInvitado = $detalle_datos["FechaNacimiento"];
                                                    $IDCaddieInvitado = $detalle_datos["IDCaddieInvitado"]; //SE INSERTA EL CADDIE DE SER EL CASO
                                                    $IDTicketDescuento = $detalle_datos["IDTicket"];

                                                    if ($IDSocioInvitado == 0 || $IDSocioInvitado == "") {
                                                        $codigocort = $detalle_datos['CodigoCortesia'];
                                                        $verificarcodigocortesiareservas = SIMWebServiceReservas::verificarcodigocortesiareservas($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDElemento, $Fecha, $Hora, $IDClub, $detalle_datos['CodigoCortesia']);
                                                        if ($verificarcodigocortesiareservas['success'] !== true) {
                                                            $cantidadExternos++;
                                                        } else {
                                                            $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDReservaGeneral = '$id_reserva_general' Where  Codigo = '$codigocort'";
                                                            $dbo->query($sql_actualiza_codigo);
                                                        }
                                                        $IDSocioInvita = $IDSocio;
                                                        $Externo = "S";
                                                    } else {
                                                        $cantidadInvitadoSocio++;
                                                    }

                                                    $datos_invitado_actual = $IDSocioInvitado . "-" . $NombreSocioInvitado;
                                                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)) :
                                                        // Guardo los invitados socios o externos
                                                        $sql = "INSERT Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre, Cedula, Correo, IDCaddie, FechaNacimiento,IDTicketDescuento,Externo,IDSocioInvita)
                                            Values ('" . $id_reserva_general . "','" . $IDSocioInvitado . "', '" . $NombreSocioInvitado . "', '" . $CedulaSocioInvitado . "','" . $CorreoSocioInvitado . "','$IDCaddieInvitado','$FechaNacimientoSocioInvitado','$IDTicketDescuento','$Externo','$IDSocioInvita')";
                                                        $sql_inserta_invitado = $dbo->query($sql);
                                                        $id_invitado_reserva_general_turnoi = $dbo->lastID();



                                                        //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                                                        $datos_respuesta = $detalle_datos["Adicionales"];
                                                        $SumaEspeciales = 0;
                                                        if (count($datos_respuesta) > 0) :
                                                            foreach ($datos_respuesta as $detalle_carac) :
                                                                $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                                                $ValoresCarac = $detalle_carac["Valores"];
                                                                $ValoresID = $detalle_carac["ValoresID"];
                                                                $Total = $detalle_carac["Total"];
                                                                $SumaEspeciales += $Total;

                                                                if (!empty($IDPropiedadProducto)) {
                                                                    $array_id_carac = explode(",", $ValoresID);
                                                                    if (count($array_id_carac) > 0) {
                                                                        foreach ($array_id_carac as $id_carac) {
                                                                            $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $id_reserva_general . "','" . $id_invitado_reserva_general_turnoi . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                                                        }
                                                                    }
                                                                }
                                                            endforeach;
                                                            SIMServicioReserva::LiberarOcupacionElementoAdicional($consecutivo, "INV");
                                                        endif;
                                                        //INSERTO LOS CAMPOS DE PREGUNTAS ADICIONALES POR INVITADO SI APLICA

                                                        $datos_campos = $detalle_datos[CamposDinamicos];
                                                        if (count($datos_campos) > 0) :
                                                            foreach ($datos_campos as $campos) :
                                                                $IDCampoInvitadoExterno = $campos[IDCampoInvitadoExterno];
                                                                $Valor = $campos[Valor];
                                                                $SQL = "INSERT INTO RespuestasCampoInvitadoExterno (IDCampoInvitadoExterno, IDReservaGeneralInvitado, Valor) VALUES ('$IDCampoInvitadoExterno','$id_invitado_reserva_general_turnoi','$Valor')";
                                                                $dbo->query($SQL);
                                                            endforeach;
                                                        endif;

                                                        //// Para las reservas del polo de practicas los invitado se crean como socio invitado y se les crea la reserva
                                                        if (($IDClub == 37 && $IDServicio == 3575) || ($IDClub == 143 && $IDServicio == 28122)) {
                                                            $sql_reserva_invitado = "INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
																																					Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, IP, IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
																																	Values ('" . $IDClub . "','$IDClubOrigen','" . $IDNuevoSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "',
																																	'" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','" . $NombreSocioReserva . "','" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','InvitadoPolo','" . $FechaHoraSistemaActual . "')";

                                                            if (empty($IDSocioInvitado)) {
                                                                $sql_inserta_invitado_socio = $dbo->query("Insert Into Socio (IDClub, IDCategoria, IDEstadoSocio, Accion, Nombre, NumeroDocumento, Email, Clave, TipoSocio, FechaInicioInvitado, FechaFinInvitado, ObservacionEspecial)
																																	Values ('" . $IDClub . "','43', '1','999999991','" . $NombreSocioInvitado . "','','invitadoreserva','','Invitado','" . $Fecha . "','" . $Fecha . "','Creado Automaticamente por practica de polo')");
                                                                $IDNuevoSocio = $dbo->lastID();
                                                            } else {
                                                                $IDNuevoSocio = $IDSocioInvitado;
                                                            }
                                                            $numero_inscripcion_inv = rand(0, 999999);
                                                            //Crear la reserva para el invitado
                                                            $sql_reserva_invitado = "INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, NombreSocio,
																															AccionSocio,NombreBeneficiario,AccionBeneficiario, IP, IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
																																Values ('" . $IDClub . "',$IDClubOrigen,'" . $IDNuevoSocio . "','" . $IDNuevoSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "',
																																'" . $CantidadInvitadoSalon . "','" . $numero_inscripcion_inv . "','" . $NombreSocioInvitado . "','','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','InvitadoPolo','" . $FechaHoraSistemaActual . "')";
                                                            $dbo->query($sql_reserva_invitado);
                                                        }

                                                        //Envio push al invitado para notificarle si es un invitado socio
                                                        if (!empty($IDSocioInvitado)) {
                                                            SIMUtil::push_socio_invitado($IDClub, $id_reserva_general, $IDSocioInvitado);
                                                        }

                                                    endif;
                                                endforeach;
                                            endif;

                                            $array_Campos = $Campos;
                                            $array_Campos = json_decode($Campos, true);

                                            if (count($array_Campos) > 0) :
                                                foreach ($array_Campos as $id_valor_campo => $valor_campo) :
                                                    // Guardo los campos personalizados
                                                    $sql_inserta_campo = $dbo->query("INSERT Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor)
																													Values ('" . $id_reserva_general . "','" . $valor_campo["IDCampo"] . "', '" . $valor_campo["Valor"] . "')");
                                                endforeach;
                                            endif;

                                            $array_Adicional = $AdicionalesSocio;
                                            $array_Adicional = json_decode($AdicionalesSocio, true);

                                            if (count($array_Adicional) > 0) {
                                                foreach ($array_Adicional as $detalle_carac) {
                                                    $SumaEspeciales = 0;

                                                    $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                                    $ValoresCarac = $detalle_carac["Valores"];
                                                    $ValoresID = $detalle_carac["ValoresID"];
                                                    $Total = $detalle_carac["Total"];
                                                    $SumaEspeciales += $Total;

                                                    if (!empty($IDPropiedadProducto)) {
                                                        $array_id_carac = explode(",", $ValoresID);
                                                        if (count($array_id_carac) > 0) {
                                                            foreach ($array_id_carac as $id_carac) {
                                                                $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicional (IDReservaGeneral, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $id_reserva_general . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                                            }
                                                        }
                                                    }
                                                }
                                                SIMServicioReserva::LiberarOcupacionElementoAdicional($consecutivo, "SOC");
                                            }

                                            // Si se va a reservas mas turnos seguidos y la validacion fue exitosa ingreso las demas reservas
                                            if ($flag_separa_siguiente_turno == 1 && count($array_hora_siguiente_turno_diponible) > 0) :
                                                foreach ($array_hora_siguiente_turno_diponible as $Hora_siguiente) :

                                                    $validacion_auxiliar = 0;
                                                    if (!empty($IDAuxiliar)) :

                                                        if (!empty($IDAuxiliar) && count($array_id_auxiliar) <= 0) :
                                                            $array_id_auxiliar = explode(",", $IDAuxiliar);
                                                        endif;

                                                        //Cuando viene con array
                                                        foreach ($array_id_auxiliar as $id_auxiliar_valida) :
                                                            if ((int) $id_auxiliar_valida >= 0) :
                                                                $id_auxiliar_valida = $id_auxiliar_valida . ",";
                                                                $id_reserva_aux = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDAuxiliar like '" . $id_auxiliar_valida . "%' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)");
                                                                $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora: " . $Fecha . "/" . $Hora_siguiente;
                                                                if (!empty($id_reserva_aux)) :
                                                                    $validacion_auxiliar = 1;
                                                                    $borra_reserva_primera = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "'");
                                                                    $respuesta["message"] = $mensaje_auxiliar_no_dispo;
                                                                    $respuesta["success"] = false;
                                                                    $respuesta["response"] = null;
                                                                    return $respuesta;
                                                                endif;

                                                            endif;
                                                        endforeach;
                                                    endif;

                                                    //Vuelvo a validar que nadie haya tomado la reserva nuevamente por los casos de milisegundos

                                                    $id_reserva_disponible3 = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "'");

                                                    //En las reservas que permiten varios cupos si se puede varias a la misma fecha y hora entonces no valida
                                                    if ($cupos_disponibilidad > 1) {
                                                        $id_reserva_disponible3 = "";
                                                    }

                                                    if (empty($id_reserva_disponible3)) :
                                                        //Guardo la reserva si nadie la ha tomado
                                                        $sql_inserta_reserva_duplicar = $dbo->query("INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio,IDUsuarioReserva,  IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui,NumeroInscripcion,Tipo,NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,IP,IdentificadorServicio,ConsecutivoServicio,CantidadInvitadoSalon,UsuarioTrCr, FechaTrCr,Longitud)
																																	Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora_siguiente . "','" . $Observaciones . "'
																																		,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion . "','Automatica','" . $NombreSocioReserva . "','" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','" . $CantidadInvitadoSalon . "','" . $UsuarioCreacion . "',
																																		'" . $FechaHoraSistemaActual . "','AT.')");

                                                        $FechaHoraAut = $Fecha . " " . $Hora_siguiente;
                                                        if (!$sql_inserta_reserva_duplicar) :
                                                            $respuesta["message"] = "No se pudo realizar la reserva, intente de nuevo (m3)";
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        endif;

                                                        $id_reserva_general_duplicar = $dbo->lastID();
                                                        // Duplico los invitados de la reserva padre
                                                        $sql_invitado_duplicar = $dbo->query("Insert into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre, Cedula, Correo) Select '" . $id_reserva_general_duplicar . "', IDSocio, Nombre, Cedula, Correo From ReservaGeneralInvitado Where IDReservaGeneral = '" . $id_reserva_general . "'");
                                                        // Guardar relacion de reservas automaticas
                                                        $sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
																																Values ('" . $id_reserva_general . "','" . $id_reserva_general_duplicar . "','" . $IDClub . "','" . $IDSocio . "','" . $IDElemento . "','" . $Fecha . "','" . $Hora_siguiente . "','1')");
                                                    endif;

                                                endforeach;
                                            endif;

                                            // SI el servicio es una clase y se solicta reservar una cancha realizo la reserva
                                            if ($flag_reserva_cancha_clase == 1) :
                                                //Verifico de nuevo que la cancha este disponible por que por milesimas de seg puede queadr 2 al tiempo

                                                //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
                                                $suma_rand = rand(0, 1);
                                                $rand_seg = rand(1, 1) + $suma_rand;
                                                sleep($rand_seg);

                                                $CuposDisponibilidad = (int) $datos_disponibilidad_otro["Cupos"];
                                                if ($CuposDisponibilidad == 0) {
                                                    $CuposDisponibilidad = 1;
                                                }

                                                $sql_reserva_hora = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE  IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicioCanchaClub . "' and IDServicioElemento = '" . $IDElemento_cancha . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' ";
                                                $r_reserva_hora = $dbo->query($sql_reserva_hora);
                                                $total_reserva_hora = (int) $dbo->rows($r_reserva_hora);

                                                //$id_reserva_disponible = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicioCanchaClub . "' and IDServicioElemento = '" . $IDElemento_cancha . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "'");

                                                //if (empty($id_reserva_disponible)):
                                                if ($CuposDisponibilidad > $total_reserva_hora) :
                                                    // Obtener la disponibilidad utilizada al consultar la reserva
                                                    $id_disponibilidad_cancha = SIMWebService::obtener_disponibilidad_utilizada($IDServicioCanchaClub, $Fecha, $IDElemento_cancha);

                                                    $sql = "INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, NumeroInscripcion, Tipo, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,IP,IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
															Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicioCanchaClub . "','" . $IDElemento_cancha . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "'
															,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion . "','Automatica','" . $NombreSocioReserva . "','" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','" . $UsuarioCreacion . "','" . $FechaHoraSistemaActual . "')";
                                                    $sql_inserta_reserva_cancha = $dbo->query($sql);

                                                    if (!$sql_inserta_reserva_cancha) :
                                                        $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (m5)";
                                                        $respuesta["success"] = false;
                                                        $respuesta["response"] = null;
                                                        return $respuesta;
                                                    endif;

                                                    $id_reserva_cancha = $dbo->lastID();

                                                    // Guardar relacion de reservas automaticas
                                                    $sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
																											Values ('" . $id_reserva_general . "','" . $id_reserva_cancha . "','" . $IDClub . "','" . $IDSocio . "','" . $IDElemento_cancha . "','" . $Fecha . "','" . $Hora . "','1')");

                                                    // SI ES PARA MEDELLIN Y EL TIPO ES CLASE DE NATACIÓN SE DEBEN SEPARA LOS DOS CUPOS
                                                    if ($IDClub == 20 && $IDTipoReserva == 2684) :
                                                        $numero_inscripcion_esp = 2;
                                                        $sql_medellin = "INSERT INTO ReservaGeneral (IDClub,IDClubOrigen, IDSocio, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, NumeroInscripcion, Tipo, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,IP,IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
                                                        Values ('" . $IDClub . "','$IDClubOrigen','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicioCanchaClub . "','" . $IDElemento_cancha . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "'
                                                                ,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion_esp . "','Automatica','" . $NombreSocioReserva . "','" . $AccionSocioReserva . "','" . $NombreBenefReserva . "','" . $AccionBenefReserva . "','" . $IP . "','" . $IdentificadorServicio . "','" . $ConsecutivoServicio . "','" . $UsuarioCreacion . "','" . $FechaHoraSistemaActual . "')";

                                                        $sql_inserta_reserva_cancha_medeillin = $dbo->query($sql_medellin);

                                                        if (!$sql_inserta_reserva_cancha_medeillin && $IDSocio == 66756) :
                                                            $respuesta["message"] = "La reserva solicitada ya fue o esta siendo tomada (m5).1";
                                                            $respuesta["success"] = false;
                                                            $respuesta["response"] = null;
                                                            return $respuesta;
                                                        endif;

                                                        $id_reserva_cancha_medellin = $dbo->lastID();

                                                        $sql_reserva_automatica_medellin = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
                                                                                    Values ('" . $id_reserva_general . "','" .  $id_reserva_cancha_medellin . "','" . $IDClub . "','" . $IDSocio . "','" . $IDElemento_cancha . "','" . $Fecha . "','" . $Hora . "','1')");

                                                    endif;
                                                else :
                                                    //No se pudo tomar por que la cancha ya fue reservada por alguien mas en el mismo segundo
                                                    //Borro la reserva asociadas
                                                    $borra_reserva_primera = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "'");
                                                    $respuesta["message"] = "Lo sentimos, no hay una cancha disponible para tomar la clase en este horario.(" . $datos_disponibilidad_otro["Cupos"] . ")";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;

                                            endif;



                                            //Especial Country Sala belleza Keratina y antifrizz se reserva turno a otras personas
                                            if (($IDClub == 44) && $IDServicio == 11736 && ($IDTipoReserva == "1482" || $IDTipoReserva == "1483" || $IDTipoReserva == "1484")) {
                                                $mensaje_otra_persona = "Se necesita otra profesional para completar la reserva pero no esta disponible, intente en otro horario";
                                                $reserva_aut_otra = SIMWebService::reserva_otra_elemento($IDClub, $IDSocio, $IDServicio, $IDTipoReserva, $Fecha, $Hora, $id_reserva_general);
                                                if (!$reserva_aut_otra) {
                                                    //Borro reserva maestra
                                                    $borra_reserva_primera = $dbo->query("DELETE FROM ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "' Limit 1");
                                                    $respuesta["message"] = $reserva_aut_otra;
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }
                                            }
                                            //FIN ESPECIAL country

                                            // Borro la reserva separada Padre
                                            //$sql_inserta_reserva = $dbo->query( "Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento     = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3" );
                                            $sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3");
                                            // Borro la reserva separada automaticas
                                            $sql_inserta_reserva_aut = $dbo->query("Delete From ReservaGeneralAutomatica Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3");

                                            SIMUtil::notificar_nueva_reserva($id_reserva_general, $IDTipoReserva);

                                            if ($datos_servicio["EnviarPushEncuestaCrear"] == "S" && $datos_servicio["IDEncuesta"] > 0) {
                                                SIMUtil::notificar_encuesta($id_reserva_general, $datos_servicio["IDEncuesta"]);
                                            }

                                            if ($datos_servicio["NotificarSocioPushReserva"] == "S" && !empty($datos_servicio["MensajePushReserva"])) {
                                                if ((int) $IDSocioBeneficiario > 0) {
                                                    $IDSocioNotificacion = $IDSocioBeneficiario;
                                                } else {
                                                    $IDSocioNotificacion = $IDSocio;
                                                }
                                                SIMUtil::push_notifica_reserva_socio($IDClub, $IDSocioNotificacion, $datos_servicio["MensajePushReserva"]);
                                            }

                                            //Si el elemento reservado es una persona (profesor, peluquero, masajista, etc) y esta creado como empleado en app empleados envio notificacion push
                                            SIMUtil::push_notifica_reserva($IDClub, $id_reserva_general, "Empleado");

                                            // si se configura un push para recordar el fin de la reserva x minutos antes
                                            if ($datos_servicio["PermiteRecordatorio"] == "S") {
                                                $Intervalo = (int) $datos_disponibilidad_otro["Intervalo"];
                                                $MinutosaAntesNotif = $Intervalo - (int) $datos_servicio["NotifMinutosAntes"];
                                                if (empty($FechaHoraAut)) { //Si la reserva no hace otras reservas aut por ejemplo separar dos turnos seguidos utilizo la hora de la reserva
                                                    $FechaHoraUltimaReserva = $Fecha . " " . $Hora;
                                                } else {
                                                    $FechaHoraUltimaReserva = $FechaHoraAut;
                                                }
                                                $FechaHoraRecordatorio = strtotime('+' . $MinutosaAntesNotif . ' minute', strtotime($FechaHoraUltimaReserva));
                                            } else {
                                                $FechaHoraRecordatorio = "";
                                            }
                                            $datos_reserva[PermiteRecordatorio] = $datos_servicio["PermiteRecordatorio"];
                                            $datos_reserva[FechaRecordatorio] = date("Y-m-d H:i:s", $FechaHoraRecordatorio);
                                            $datos_reserva[MensajeRecordatorio] = $datos_servicio["MensajeNotifTerminaReserva"];
                                            //Fin si se configura un push para recordar el fin de la reserva x minutos antes

                                            //Datos reserva
                                            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                                            $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");
                                            $response_reserva = array();
                                            $datos_reserva["IDReserva"] = (int) $id_reserva_general;
                                            //Calculo el valor de la reserva

                                            $valor_inicial_reserva += (float) $datos_servicio["ValorReserva"];
                                            $TurnosSeparar = (float) $dbo->getFields("ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = '" . $IDTipoReserva . "'");
                                            $consul = "ServicioTipoReserva " . " NumeroTurnos " . "IDServicioTipoReserva = '" . $IDTipoReserva . "'";



                                            //Calculo el valor de los caddies
                                            if ((int)$IDCaddieSocio > 0) {
                                                $PrecioCaddie = $dbo->getFields("Caddie2", "Precio", "IDCaddie = '" . $IDCaddieSocio . "' ");
                                            }

                                            $datos_invitado_cadd = json_decode($Invitados, true);
                                            if (count($datos_invitado_cadd) > 0) :
                                                foreach ($datos_invitado_cadd as $detalle_datos) :
                                                    $IDCaddieInvitado = $detalle_datos["IDCaddieInvitado"];
                                                    if ((int)$IDCaddieInvitado > 0) {
                                                        $PrecioCaddie += $dbo->getFields("Caddie2", "Precio", "IDCaddie = '" . $detalle_datos["IDCaddieInvitado"] . "' ");
                                                    }
                                                endforeach;
                                            endif;



                                            if ($TurnosSeparar > 1 && $IDClub != 88 && $IDClub != 11) :
                                                $ValorReserva = (float) $valor_inicial_reserva * (float) $TurnosSeparar;
                                            else :
                                                $ValorReserva = (float) $valor_inicial_reserva;
                                            endif;

                                            if ($datos_tipo_reserva["Valor"] > 0 && $IDTipoReserva > 0) {
                                                $ValorReserva = (float) $datos_tipo_reserva["Valor"];
                                            }

                                            if ($datos_servicio[PermitePrecioElementos] == 1) :
                                                (float) $ValorReserva = SIMServicioReserva::calcular_precio_elemento($Fecha, $Hora, $IDServicio, $IDElemento);
                                            endif;

                                            if ($IDClub == 28) : // Valor Especial Liga de tenis
                                                $ValorReserva = SIMUtil::calcular_tarifa($IDClub, $IDSocio, $IDServicio, $Fecha, $Hora, $IDElemento, $id_reserva_general, $IDTipoReserva);
                                                if ((int) $ValorReserva == 0) {
                                                    $ValorReserva = (float) $datos_servicio["ValorReserva"];
                                                }
                                            endif;

                                            if ($IDClub == 106) : // Valor Especial nadesba
                                                (float) $ValorReserva = SIMUtil::calcular_tarifa2($IDSocio, $Invitados, $IDTipoReserva);
                                            endif;

                                            if ($IDClub == 221) : // Valor Especial Sta monica                                    
                                                $Cobrar = SIMServicioReserva::valor_reserva_santa_monica($IDSocio, $Fecha, $Hora, $IDServicio, $datos_socio, $id_reserva_general);
                                                if ($Cobrar == "N") {
                                                    $ValorReserva = 0;
                                                }
                                            endif;


                                            if ($IDClub == 226) : // Valor Especial San Luis
                                                (float) $ValorReserva = SIMServicioReserva::valor_reserva_san_luis($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva, $IDElemento, $TurnosSeparar);
                                                if ((int) $ValorReserva == 0) {
                                                    $ValorReserva = (float) $datos_servicio["ValorReserva"] * (float) $TurnosSeparar;
                                                }
                                            endif;


                                            /*
                                            if ($IDClub == 88 && ($IDServicio == 13698 || $IDServicio == 13697)) :
                                                (float) $ValorReserva = SIMServicioReserva::valor_serena($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva);
                                                if ((int) $ValorReserva == 0) {
                                                    $ValorReserva = (float) $datos_servicio["ValorReserva"];
                                                }
                                            endif;
                                            */



                                            if ($IDClub == 112 && ($IDServicio == 19939 || $IDServicio == 19940/* || $IDServicio == 233 */)) :
                                                (float) $ValorReserva = SIMUtil::valor_reserva_bellavista($AdicionalesSocio);
                                            endif;

                                            if ($IDClub == 178) :

                                                if ($IDServicio == 35545) : //PRRECIO PARA CANASTO DE PELOTAS
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_canosto_pelotas($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio);
                                                endif;

                                                if ($IDServicio == 35402) :
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_arriendo_carros($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio);
                                                endif;

                                                if ($IDServicio == 35484) :
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_golf($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio);
                                                endif;

                                                if ($IDServicio == 35583) :
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_tenis($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva);
                                                endif;

                                                if ($IDServicio == 35511) :
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_padel($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva);
                                                endif;

                                                if ($IDServicio == 35555) :
                                                    (float) $ValorReserva = SIMServicioReserva::valor_reserva_chicureo_squash($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva);
                                                endif;

                                            endif;

                                            if ($IDClub == 85 && $IDServicio == 12718) :
                                                (float) $ValorReserva = SIMUtil::valor_reserva_liga_risaralda($Hora, $ListaAuxiliar, $IDElemento);
                                            endif;

                                            if ($IDClub == 85 && $IDServicio == 12676) :
                                                (float) $ValorReserva = SIMUtil::valor_reserva_liga_risaralda_clases_grupales($Hora, $ListaAuxiliar, $IDElemento, $Fecha);
                                            endif;

                                            $ValorReserva += $PrecioCaddie;
                                            if ($datos_servicio["InvitadoExternoPago"] == "S") {
                                                $ValorReserva += ($cantidadExternos * $datos_servicio["InvitadoExternoValor"]);
                                            }

                                            $datos_reserva["ValorReserva"] = (float) $ValorReserva;
                                            $TextoValor = number_format((float) $ValorReserva, 1, ",", ".");
                                            if ($IDClub == 124) :
                                                $TextoValor = $ValorReserva;
                                            endif;

                                            $datos_reserva["ValorPagoTexto"] = $datos_club_otros["SignoPago"] . " " . $TextoValor . " " . $datos_club_otros["TextoPago"];





                                            if ($datos_servicio["CobrarPorInvitado"] == "S") {
                                                $ValorConInvitadoExterno = $ValorReserva;
                                                if ($IDServicio == 49803) { // Para este servicio siempre se calcula x 1
                                                    $ValorConInvitadoSocio = 1 * $datos_servicio["ValorReserva"];
                                                } else
                                                    $ValorConInvitadoSocio = $cantidadInvitadoSocio * $datos_servicio["ValorReserva"];

                                                $ValorReserva = ($ValorConInvitadoExterno + $ValorConInvitadoSocio);
                                                $TextoValor = $ValorReserva;
                                                $datos_reserva["ValorPagoTexto"] = $datos_club_otros["SignoPago"] . " " . $ValorReserva;
                                            }




                                            //array_push($response_reserva, $datos_reserva);
                                            //Produccion
                                            $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
                                            //Produccion
                                            $usuarioId = $datos_club["MerchantId"]; //c0digo inicio del cliente

                                            $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                                            $iva = 0; //impuestos calculados de la transacciÛn
                                            $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                                            $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                                            $moneda = "COP"; //la moneda con la que se realiza la compra
                                            $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba

                                            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                                            if (empty($nombre_servicio_personalizado)) {
                                                $nombre_servicio_personalizado = $datos_servicio["Nombre"];
                                            }
                                            $descripcion = "Pago Reserva Mi Club (" . $nombre_servicio_personalizado . ")";


                                            if ($IDClub == 28) {
                                                $descripcion = "Pago Reserva " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Sede: " . $nombre_servicio_personalizado;
                                            }
                                            $url_respuesta = URLROOT . "respuesta_transaccion.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                                            $url_confirmacion = URLROOT . "confirmacion_pagos.php";
                                            $emailSocio = $datos_socio["CorreoElectronico"];
                                            if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                                                $emailComprador = $emailSocio;
                                            } else {
                                                $emailComprador = "";
                                            }

                                            $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                                            $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                                            $extra1 = $id_reserva_general;

                                            //  ACTUALIZO EL VALOR DE LA RESERVA PARA SOLO ELIMINAR LAS QUE TIENE UN VALOR MAYOR A CERO EN EL CRON.
                                            $actulizaValor = "UPDATE ReservaGeneral SET ValorPagado = '" . $ValorReserva . "' WHERE IDReservaGeneral ='" . $id_reserva_general . "'";
                                            $dbo->query($actulizaValor);

                                            $datos_reserva["Action"] = $datos_club["URL_PAYU"];

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
                                            $datos_post["valor"] = (string) $datos_club["AccountId"];
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
                                            $datos_post["valor"] = (string) $ValorReserva;
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
                                            $datos_post["valor"] = (string) "Reservas";
                                            array_push($response_parametros, $datos_post);

                                            $datos_post["llave"] = "IDSocio";
                                            $datos_post["valor"] = $IDSocio;
                                            array_push($response_parametros, $datos_post);

                                            $datos_reserva["ParametrosPost"] = $response_parametros;

                                            //PAGO
                                            $datos_post_pago = array();
                                            $datos_post_pago["iva"] = 0;
                                            $datos_post_pago["purchaseCode"] = $refVenta;
                                            $datos_post_pago["totalAmount"] = $ValorReserva * 100;
                                            $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                                            $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                                            //FIN PAGO

                                            /*
                                            $datos_reserva["Action"] = $datos_club["URL_PAYU"];
                                            $datos_reserva["moneda"] = (string)$moneda;
                                            $datos_reserva["ref"] = $refVenta;
                                            $datos_reserva["llave"] = $llave_encripcion;
                                            $datos_reserva["userid"] = $usuarioId;
                                            $datos_reserva["usuarioId"] = $usuarioId;
                                            $datos_reserva["accountId"] = (string)$datos_club["AccountId"];
                                            $datos_reserva["descripcion"] = $descripcion;
                                            $datos_reserva["extra1"] = (string)$extra1;
                                            $datos_reserva["extra2"] = $IDClub;
                                            $datos_reserva["refVenta"] = $refVenta;
                                            $datos_reserva["valor"] =  $datos_reserva["ValorReserva"];
                                            $datos_reserva["iva"] = "0";
                                            $datos_reserva["baseDevolucionIva"] = "0";
                                            $datos_reserva["firma"] = $firma;
                                            $datos_reserva["emailComprador"] = $emailComprador;
                                            $datos_reserva["prueba"] = (string)$datos_club["IsTest"];
                                            $datos_reserva["url_respuesta"] = (string)$url_respuesta;
                                            $datos_reserva["url_confirmacion"] = (string)$url_confirmacion;
                                            */



                                            // VAMOS A BUSCAR SI EL SOCIO TIENE UNA TALONERA DISPONIBLES
                                            require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
                                            $IDTaloneraDisponible = SIMWebServiceTaloneras::talonera_disponible($IDClub, $IDSocio, $IDServicio, $Fecha, $IDSocioBeneficiario);

                                            if ($IDTaloneraDisponible == null) {
                                                $IDTaloneraDisponible = 0;
                                            }

                                            $datos_reserva[IDTaloneraDisponible] = $IDTaloneraDisponible;

                                            $datos_reserva[PagarTotalLabel] = $datos_servicio["PagarTotalLabel"];
                                            $datos_reserva[PermiteSistemaAbono] = $datos_servicio["PermiteSistemaAbono"];

                                            if ($datos_servicio["PermiteSistemaAbono"] == 'S') :
                                                $datos_reserva[PagarAbonoLabel] = $datos_servicio["PagarAbonoLabel"];
                                                require_once LIBDIR . "SIMWebServiceReservas.inc.php";
                                                $OpcionesSistemaAbono = SIMWebServiceReservas::Abonos($IDClub, $ValorReserva, $IDServicio, $datos_club, $datos_club_otros, $datos_persona);
                                                $datos_reserva[OpcionesSistemaAbono] = $OpcionesSistemaAbono;
                                            endif;

                                            $datos_reserva[PermitePagarMasTarde] = $datos_servicio["PermitePagarMasTarde"];
                                            $datos_reserva[LabelPagarMasTarde] = $datos_servicio["LabelPagarMasTarde"];

                                            $datos_reserva[PermiteBonos] = $datos_servicio["PermiteBonos"];

                                            if ($datos_servicio["PermiteBonos"] == 'S') :
                                                $datos_reserva[LabelBotonBonos] = $datos_servicio["LabelBotonBonos"];

                                                require_once LIBDIR . "SIMWebServiceReservas.inc.php";
                                                $SistemaBonos = SIMWebServiceReservas::SistemaBonos($IDClub, $IDSocio, $id_reserva_general, $ValorReserva, $IDServicio, $datos_club, $datos_club_otros, $datos_persona);
                                                $datos_reserva[SistemaBonos] = $SistemaBonos;
                                            endif;

                                            $mensaje_guardar = $datos_servicio["MensajeReservaGuardada"];
                                            if (!empty($mensaje_guardar)) {
                                                $mensaje_guardado = $mensaje_guardar;
                                            } else {
                                                $mensaje_guardado = 'Guardado';
                                            }

                                            //Para aeroclub avion si es 8 dias antes no esta sujeta a verificación y es menos días si aparece mensaje
                                            if ($IDClub == 36) :
                                                $mensaje_guardado = "Reservado con exito";
                                            endif;
                                            if ($IDServicio == "3608" || $IDServicio == "4371" || $IDServicio == "3608" || $IDServicio == "3609") :
                                                $fecha_reser = $Fecha;
                                                $nuevafecha = strtotime('-8 day', strtotime($fecha_reser));
                                                $nuevafecha = date('Y-m-j', $nuevafecha);
                                                $fecha_hoy_reser = date("Y-m-d");
                                                if (strtotime($nuevafecha) <= strtotime($fecha_hoy_reser)) :
                                                    $mensaje_especial_repetir = "RESERVA EN PROCESO Como su solicitud esta siendo procesada con menos de 8 días de antelación, queda sujeta a disponibilidad. Pronto le confirmaremos";
                                                    $mensaje_guardado = "";
                                                else :
                                                    $mensaje_guardado = "Reservado con exito";
                                                endif;
                                            endif;

                                            //Validacion para ACTIVE BODYTECH
                                            $idPadre = SIMUtil::IdPadre($IDClub);
                                            if ($idPadre == 157) {
                                                $permisoServicioSocio = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "IDClub = $IDClub AND IDSocio = $IDSocio");

                                                if ($permisoServicioSocio) { //Verifica que tenga permisos para hacer la reserva

                                                    $taloneraDisponible = $dbo->getFields("SocioTalonera", "IDSocioTalonera", "IDServicio = $IDServicio AND IDSocio = $IDSocio AND Activo = 1 AND CantidadPendiente > 0 ORDER BY FechaCompra ASC LIMIT 1");

                                                    if ($taloneraDisponible != 0) { //Si tiene una talonera disponible, realiza el pago con ella
                                                        require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
                                                        $respuestaTalonera = SIMWebServiceTaloneras::pagar_reserva($IDClub, $IDSocio, $id_reserva_general, 16, $taloneraDisponible);
                                                    }
                                                } else {
                                                    $respuesta["message"] = "Lo sentimos, no tiene permiso para tomar la reserva.";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }
                                            }



                                            //validacion para NUBA, revisa que tenga una talonera disponible
                                            if ($IDClub == 196) :

                                                $sqlTalonera = "SELECT IDSocioTalonera
                                                    FROM SocioTalonera
                                                    WHERE
                                                        (IDSocio = $IDSocio OR SociosPosibles LIKE '%$IDBeneficiario%') AND  FechaVencimiento >= '" . date("Y-m-d") . "'  AND Activo = 1 AND CantidadPendiente > 0 AND
                                                        (IDServicio = $IDServicio OR
                                                        TodosLosServicios = 1 OR
                                                        IDTalonera IN( SELECT IDTalonera FROM TaloneraServicios WHERE IDServicio = $IDServicio))
                                                    ORDER BY FechaCompra ASC LIMIT 1";
                                                $rTalonera = $dbo->query($sqlTalonera);
                                                $rowTalonera = $dbo->fetchArray($rTalonera);
                                                $rowsTalonera = $dbo->rows($rTalonera);

                                                if ($rowsTalonera > 0) :
                                                    require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
                                                    $respuestaTalonera = SIMWebServiceTaloneras::pagar_reserva($IDClub, $IDBeneficiario, $id_reserva_general, 16, $rowTalonera['IDSocioTalonera']);
                                                    if ($respuestaTalonera["success"] == false) :
                                                        $sql_elimina = "DELETE FROM ReservaGeneral WHERE IDReservaGeneral= '" . $id_reserva_general . "' ";
                                                        $dbo->query($sql_elimina);
                                                        $respuesta["message"] = $respuestaTalonera["message"];
                                                        $respuesta["success"] = false;
                                                        $respuesta["response"] = null;
                                                        return $respuesta;
                                                    endif;
                                                else :
                                                    //elimino reserva
                                                    $sql_elimina = "DELETE FROM ReservaGeneral WHERE IDReservaGeneral= '" . $id_reserva_general . "' ";
                                                    $dbo->query($sql_elimina);
                                                    $respuesta["message"] = "Lo sentimos, no cuenta con cupos disponibles o su talonera está vencida";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                endif;
                                            endif;

                                            $respuesta["message"] = $mensaje_guardado . $mensaje_especial_repetir;
                                            $respuesta["success"] = true;
                                            $respuesta["response"] = $datos_reserva;
                                        else :
                                            $respuesta["message"] = "Lo sentimos la reserva ya fue tomada en el dia o dias que necesita";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;

                                        endif;

                                        //$contador_fecha = strtotime ( '+1 '.$periodo_sumar ,  $contador_fecha  ) ;
                                        //$contador_fecha = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;
                                        $contador_fecha = strtotime('+' . $dato_sumar . ' ' . $periodo_sumar_app, strtotime($Fecha));
                                        $Fecha = date("Y-m-d", $contador_fecha);

                                    endfor;
                                } else {
                                    $respuesta["message"] = "Lo sentimos, solo se permite " . $MaximoReservaSocioServicio . " reservas por dia. " . $mensaje_opcion_reserva . $total_reserva_socio;
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            else :
                                $respuesta["message"] = "Lo sentimos, fecha no disponible";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            endif;
                        else :
                            $respuesta["message"] = "Lo sentimos no se puede reservar turnos seguidos, debe haber un lapso de por lo menos 1 hora ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        endif;

                    else :
                        $respuesta["message"] = "Lo sentimos, el maximo numero de invitados para poder reservar es: " . $MaximoInvitados . " - " . $id_disponibilidad;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    endif;

                else :
                    $respuesta["message"] = "Lo sentimos el minimo de personas para reservas es de : " . ($MinimoInvitados + 1);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            } else {
                $respuesta["message"] = 'Error el socio no existe o no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "21." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function reserva_otra_elemento($IDClub, $IDSocio, $IDServicio, $IDTipoReserva, $Fecha, $Hora, $IDReservaPadre)
    {

        $dbo = &SIMDB::get();
        $FechaHoraServicio = $Fecha . " " . $Hora;
        $doc_segundo_elemento_dispo = "";
        $ServicioAsociadoEst["IDServicio"] = "11732";
        $ServicioAsociadoEst["IDTipoServicio"] = "1518";
        $ServicioAsociadoEst["IDElemento"][1035912851] = "3424";
        $ServicioAsociadoEst["IDElemento"][1019084378] = "3422";
        $sql_estilista = "SELECT IDServicioElemento,IdentificadorElemento FROM ServicioElemento WHERE IdentificadorElemento = '1035912851' or IdentificadorElemento = '1019084378' Order by IdentificadorElemento DESC"; //leira
        $r_estilista = $dbo->query($sql_estilista);
        while ($row_estilista = $dbo->fetchArray($r_estilista)) {
            $array_id_estilista[$row_estilista["IdentificadorElemento"]][] = $row_estilista["IDServicioElemento"];
        }
        //print_r($array_id_estilista);

        switch ($IDTipoReserva) {
            case "1482": //Keratina media cabeza 90 min.
                $H_Siguiente = strtotime('+30 minute', strtotime($FechaHoraServicio));
                $HoraSiguiente = date("H:i:s", $H_Siguiente);
                $NumeroBloques = 4;
                break;
            case "1483": //keratina completa 135 min.
                $H_Siguiente = strtotime('+45 minute', strtotime($FechaHoraServicio));
                $HoraSiguiente = date("H:i:s", $H_Siguiente);
                $NumeroBloques = 6;
                break;
            case "1484": // Antifrizz 90 min.
                $H_Siguiente = strtotime('+30 minute', strtotime($FechaHoraServicio));
                $HoraSiguiente = date("H:i:s", $H_Siguiente);
                $NumeroBloques = 4;
                break;
        }

        foreach ($array_id_estilista as $identifica_est => $datos_est) {
            $bloques_disponibles = "S";
            if (empty($doc_segundo_elemento_dispo)) {
                foreach ($datos_est as $id_est) {
                    $array_id_est[] = $id_est;
                }
                if (count($array_id_est) > 0) {
                    $id_est = implode(",", $array_id_est);
                    for ($i_bloque = 1; $i_bloque <= $NumeroBloques; $i_bloque++) {
                        if ($bloques_disponibles == "S") {
                            /*
                            echo "<br>".$sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE Fecha = '".$Fecha."' and Hora = '".$HoraSiguiente."' and IDServicioElemento in ($id_est) Limit 1";
                            $r_est=$dbo->query($sql_reserva_otro);
                            $row_est=$dbo->fetchArray($r_est);
                            if(!empty($row_est["IDReservageneral"])){
                            $bloques_disponibles="N";
                            }
                             */
                            // Obtener la disponibilidad utilizada al consultar la reserva
                            $id_disponibilidad_est = SIMWebService::obtener_disponibilidad_utilizada($ServicioAsociadoEst["IDServicio"], $Fecha, $ServicioAsociadoEst["IDElemento"][$identifica_est], $HoraSiguiente);

                            if (!empty($id_disponibilidad_est)) {
                                $verificacion_est = SIMWebService::verifica_club_servicio_abierto($IDClub, $Fecha, $ServicioAsociadoEst["IDServicio"], $ServicioAsociadoEst["IDElemento"][$identifica_est], $HoraSiguiente, "");
                                if (empty($verificacion_est)) {
                                    $Observaciones = "Reserva automatica de keratina o antifrrizz";
                                    $sql_insert_rese = "INSERT INTO ReservaGeneral (IDClub, IDSocio, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, NumeroInscripcion, Tipo, UsuarioTrCr, FechaTrCr)
																								Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $ServicioAsociadoEst["IDServicio"] . "','" . $ServicioAsociadoEst["IDElemento"][$identifica_est] . "', '1','" . $id_disponibilidad_est . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $ServicioAsociadoEst["IDTipoServicio"] . "','" . $Fecha . "', '" . $HoraSiguiente . "','" . $Observaciones . "'
																									,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion . "','Automatica','" . $UsuarioCreacion . "',NOW())";

                                    $sql_inserta_reserva_est = $dbo->query($sql_insert_rese);
                                    $dbo->query($sql_inserta_reserva_est);
                                    $id_reserva_est = $dbo->lastID();
                                    // Guardar relacion de reservas automaticas
                                    $sql_reserva_automatica = $dbo->query("INSERT INTO ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
																																		Values ('" . $IDReservaPadre . "','" . $id_reserva_est . "','" . $IDClub . "','" . $IDSocio . "','" . $ServicioAsociadoEst["IDElemento"][$identifica_est] . "','" . $Fecha . "','" . $HoraSiguiente . "','1')");

                                    $array_id_reserva_est_aut[] = $id_reserva_est;

                                    if (!$sql_inserta_reserva_est) {
                                        $bloques_disponibles = "N";
                                        if (count($array_id_reserva_est_aut) > 0) {
                                            foreach ($array_id_reserva_est_aut as $id_reserva_aut_est) {
                                                $borra_reserva_apartada = "DELETE FROM ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_aut_est . "' Limit 1";
                                                $dbo->query($borra_reserva_apartada);
                                            }
                                        }
                                    }
                                } else {
                                    $bloques_disponibles = "N";
                                }
                            } else {
                                $bloques_disponibles = "N";
                            }
                        }
                        $FechaHoraServicio = $Fecha . " " . $HoraSiguiente;
                        $H_Siguiente = strtotime('+15 minute', strtotime($FechaHoraServicio));
                        $HoraSiguiente = date("H:i:s", $H_Siguiente);
                    }

                    // como si se pudo reservar todo salgo del ciclo
                    if ($bloques_disponibles == "S") {
                        $doc_segundo_elemento_dispo = $identifica_est;
                    }
                }
            }
        }

        if ($doc_segundo_elemento_dispo == "") {
            return false;
        } else {
            return true;
        }
        return false;
    }

    public function set_invitado_servicio($IDClub, $IDReserva, $Invitados)
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMServicioReserva.inc.php";

        if (!empty($IDClub) && !empty($IDReserva)) {

            //verifico que el socio exista y pertenezca al club
            $id_reserva = $IDReserva;

            $nuevacadena = str_replace('Optional("', "", $Invitados);
            $nuevacadena = str_replace('")', "", $nuevacadena);
            $nuevacadena = trim(preg_replace('/\s+/', ' ', $nuevacadena));
            $Invitados = $nuevacadena;
            $datos_invitado = json_decode($Invitados, true);

            $TotalInvitadosAgregados = count($datos_invitado);

            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");

            //Para bellavista solo el dueño de la reserva puede eliminar invitados para los sabados

            //Para bellavista el dueño de la reserva puede modificar el turno de lunes a miercoles
            if (($IDClub == 112 && $datos_reserva["IDServicio"] == 19939)) {
                $dia_semana = date("w", strtotime($datos_reserva["Fecha"]));
                if ((int) $dia_semana == 6) {
                    //Verifico que este en los dias
                    $HoraEmpieza = "23:59:00";
                    $FechaHoraReserva = $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                    $DiaPermitido = strtotime('-3 day', strtotime(date($FechaHoraReserva)));
                    $HoraPermitida = strtotime(date("Y-m-d", $DiaPermitido) . " " . $HoraEmpieza);
                    $HoraActual = strtotime(date("Y-m-d H:i:s"));
                    if ($HoraActual >= $HoraPermitida) {
                        $respuesta["message"] = "Lo sentimos solo puede modificar la reserva para el Sabado de Lunes a miercoles: ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }
                }
            }

            if ($datos_servicio["PermiteEditarReserva"] != "N") {
                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["IDServicioElemento"], $datos_reserva["Hora"]);
                $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_reserva[IDDisponibilidad] . "' ", "array");
                //quito 1 al dueño de la reserva
                $MinimoPersonasTurno = $datos_disponibilidad["MinimoInvitados"] - 1;
                $MaximoPersonasTurno = $datos_disponibilidad["MaximoInvitados"];

                $MaximoReservaSocioServicio = $datos_disponibilidad["MaximoReservaDia"];
                $MaximoInvitados = $datos_disponibilidad["MaximoInvitados"];

                if ($datos_reserva["IDServicioTipoReserva"] > 0) {
                    $datos_servicio_tipo = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $datos_reserva["IDServicioTipoReserva"] . "' ", "array");
                    $MinimoPersonasTurno = $datos_servicio_tipo["MinimoParticipantes"];
                }

                if ($TotalInvitadosAgregados > $MaximoInvitados) {
                    $respuesta["message"] = "Supera el maximo de invitados permitidos";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($TotalInvitadosAgregados >= $MinimoPersonasTurno) {



                    //Verifico que la persona no este en otro grupo
                    if (count($datos_invitado) > 0) {
                        foreach ($datos_invitado as $detalle_datos) {
                            $IDSocioInvitado = $detalle_datos["IDSocio"];
                            if ((int) $IDSocioInvitado > 0) {

                                //aqui valida
                                $datos_socio_agregado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioInvitado . "' ", "array");
                                $nombre_socio_invitado = $datos_socio_agregado["Nombre"] . " " . $datos_socio_agregado["Apellido"];
                                if ($datos_socio_agregado["PermiteReservar"] == "N") {
                                    $respuesta["message"] = "Lo sentimos el invitado " . $nombre_socio_invitado . " no tiene permiso para reservar";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                }

                                if ($datos_servicio["ValidarEdad"] == "S") {
                                    $fecha_nacimiento_invitado = $datos_socio_agregado["FechaNacimiento"];
                                    $dia_actual = date("Y-m-d");
                                    $edad_diff = date_diff(date_create($fecha_nacimiento_invitado), date_create($dia_actual));
                                    $EdadSocioInvitado = $edad_diff->format('%y');
                                    if ($EdadSocioInvitado >= $datos_servicio["EdadMinima"] && $EdadSocioInvitado <= $datos_servicio["EdadMaxima"]) {
                                        $edadpermitida == "S";
                                    } else {
                                        $respuesta["message"] = "Lo sentimos el invitado: " . $nombre_socio_invitado . " no tiene la edad permitida";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    }
                                }

                                $hora_actual_sistema_valida = date("H:i:s");
                                $sql_socio_grupo = "SELECT RGI.*
																		FROM ReservaGeneralInvitado RGI, ReservaGeneral RG
																		WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocioInvitado . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $datos_reserva["Fecha"] . "' and RG.Hora <> '" . $datos_reserva["Hora"] . "' and RG.IDServicio = '" . $datos_reserva["IDServicio"] . "' ORDER BY IDReservaGeneralInvitado Desc ";
                                $qry_socio_grupo = $dbo->query($sql_socio_grupo);

                                if ($dbo->rows($qry_socio_grupo) > 0 && ($MaximoReservaSocioServicio <= 1 || $dbo->rows($qry_socio_grupo) >= $MaximoReservaSocioServicio)) {
                                    $respuesta["message"] = $nombre_socio_invitado . ", ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                    exit;
                                } elseif ($MaximoReservaSocioServicio <= 1 || $dbo->rows($qry_socio_grupo) >= $MaximoReservaSocioServicio) {
                                    $sql_reserva_dia_hora = "SELECT IDReservageneral
																								 FROM ReservaGeneral
																								 Where IDSocio = '" . $IDSocioInvitado . "' and Fecha = '" . $datos_reserva["Fecha"] . "' and IDServicio = '" . $datos_reserva["IDServicio"] . "' and IDEstadoReserva = '1' Order by Hora Desc Limit 1";
                                    $result_reserva_dia_hora = $dbo->query($sql_reserva_dia_hora);
                                    $row_reserva_dia_hora = $dbo->fetchArray($result_reserva_dia_hora);
                                    if (!empty($row_reserva_dia_hora["IDReservageneral"])) {
                                        $respuesta["message"] = $nombre_socio_invitado . ", ya tiene una reserva para ese mismo dia";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                        exit;
                                    }
                                }
                            }
                        }
                    }

                    //Verifico la reserva si tiene invitados que se agregaron con el boton de mas cupos, ellos no se pueden borrar de la reserva
                    $array_socio_agregado = array();
                    $SocioAgregado = "N";
                    $sql_socio_grupo = "SELECT IDSocio,AgregadoBotonPublico
																	FROM ReservaGeneralInvitado RGI
																	WHERE IDReservageneral = '" . $IDReserva . "' and AgregadoBotonPublico ='S'";
                    $qry_socio_grupo = $dbo->query($sql_socio_grupo);
                    while ($row_soc_gr = $dbo->fetchArray($qry_socio_grupo)) {
                        $array_socio_agregado[] = $row_soc_gr["IDSocio"];
                        $SocioAgregado = "S";
                    }
                    if (count($datos_invitado) <= 0 && $SocioAgregado == "S") {
                        $respuesta["message"] = "No es posible eliminar los invitados, una persona se agregó a este grupo y no es posible borrarlo ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }

                    if (count($datos_invitado) > 0 && $SocioAgregado == "S") {
                        foreach ($datos_invitado as $detalle_datos) {
                            $array_nuevos_soc[] = $detalle_datos["IDSocio"];
                        }

                        foreach ($array_socio_agregado as $id_soc_agre) {
                            if (!in_array($id_soc_agre, $array_nuevos_soc)) {
                                $respuesta["message"] = "Uno de los invitados se agregó a este grupo no es posible borrarlo";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                                exit;
                            }
                        }
                    }

                    //quito del array de invitados los socios que se agregaron
                    $contador_pos = 0;
                    if (count($datos_invitado) > 0 && $SocioAgregado == "S") {
                        foreach ($datos_invitado as $detalle_datos) {
                            if (in_array($detalle_datos["IDSocio"], $array_socio_agregado)) {
                                unset($datos_invitado[$contador_pos]);
                            }
                            $contador_pos++;
                        }
                    }

                    if (!empty($id_reserva)) {
                        //Borro los invitado anteriores
                        $del_invitado = "Delete From ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "' and AgregadoBotonPublico <> 'S' ";
                        $dbo->query($del_invitado);

                        if (count($datos_invitado) > 0) :
                            foreach ($datos_invitado as $detalle_datos) :

                                $IDSocioInvitado = $detalle_datos["IDSocio"];
                                $NombreSocioInvitado = $detalle_datos["Nombre"];
                                $CedulaSocioInvitado = $detalle_datos["Cedula"];
                                $CorreoSocioInvitado = $detalle_datos["Correo"];
                                $FechaNacimientoSocioInvitado = $detalle_datos["FechaNacimiento"];

                                if ($datos_servicio["ValidarEdadInvitados"] == "S") :
                                    if ($datos_servicio["PermiteInvitadoExternoFechaNacimiento"] == "S") :

                                        $dia_actual = date("Y-m-d");
                                        $edad_diff = date_diff(date_create($FechaNacimientoSocioInvitado), date_create($dia_actual));
                                        $EdadInvitado = $edad_diff->format('%y');
                                        if ($EdadInvitado >= $datos_servicio["EdadMinima"] && $EdadInvitado <= $datos_servicio["EdadMaxima"]) {
                                            $edadpermitida == "S";
                                        } else {
                                            $respuesta["message"] = "Lo sentimos el invitado $NombreSocioInvitado no tiene la edad para poder ir a la reserva";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;
                                            return $respuesta;
                                        }

                                    endif;
                                endif;

                                // Guardo los invitados socios o externos
                                $sql_inserta_invitado = $dbo->query("INSERT Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre, Cedula, Correo, FechaNacimiento)
																										Values ('" . $id_reserva . "','" . $IDSocioInvitado . "', '" . $NombreSocioInvitado . "', '" . $CedulaSocioInvitado . "', '" . $CorreoSocioInvitado . "', '$FechaNacimientoSocioInvitado')");

                                $id_invitado_reserva_general_turnoi = $dbo->lastID();
                                //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                                $datos_respuesta = $detalle_datos["Adicionales"];
                                $SumaEspeciales = 0;
                                if (count($datos_respuesta) > 0) :
                                    //Valida si el elemento adicional esta disponible
                                    require LIBDIR . "SIMServicioReserva.inc.php";
                                    $validacion = SIMServicioReserva::validar_disponibilidad_elemento_adicional($id_carac, $datos_reserva["Fecha"]);
                                    if ($validacion["response"] == "N") :
                                        $respuesta["message"] = $validacion["message"];
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;
                                    //Fin validación
                                    foreach ($datos_respuesta as $detalle_carac) :
                                        $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                                        $ValoresCarac = $detalle_carac["Valores"];
                                        $ValoresID = $detalle_carac["ValoresID"];
                                        $Total = $detalle_carac["Total"];
                                        $SumaEspeciales += $Total;

                                        if (!empty($IDPropiedadProducto)) {
                                            $array_id_carac = explode(",", $ValoresID);
                                            if (count($array_id_carac) > 0) {
                                                foreach ($array_id_carac as $id_carac) {
                                                    $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $id_reserva . "','" . $id_invitado_reserva_general_turnoi . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                                }
                                            }
                                        }
                                    endforeach;
                                endif;
                                //INSERTO LOS CAMPOS DE PREGUNTAS ADICIONALES POR INVITADO SI APLICA

                                $datos_campos = $detalle_datos[CamposDinamicos];
                                if (count($datos_campos) > 0) :
                                    foreach ($datos_campos as $campos) :
                                        $IDCampoInvitadoExterno = $campos[IDCampoInvitadoExterno];
                                        $Valor = $campos[Valor];
                                        $SQL = "INSERT INTO RespuestasCampoInvitadoExterno (IDCampoInvitadoExterno, IDReservaGeneralInvitado, Valor) VALUES ('$IDCampoInvitadoExterno','$id_invitado_reserva_general_turnoi','$Valor')";
                                        $dbo->query($SQL);
                                    endforeach;
                                endif;

                            endforeach;
                        endif;

                        if ($datos_servicio["ValidarReservasActivas"] == "S" && $IDClub != 44 && $IDClub != 26) {
                            $ValidaSeman = $datos_servicio["ValidaReservasActivasSemana"];
                            $ValidaFin = $datos_servicio["ValidaReservasActivasFin"];
                            $NumeroSeman = $datos_servicio["NumeroReservasActivasSemana"];
                            $NumeroFin = $datos_servicio["NumeroReservasActivasFin"];
                            $ValidaGeneral = $datos_servicio["ValidarReservasActivasGeneral"];
                            $NumeroGeneral = $datos_servicio["NumeroReservasActivasGeneral"];

                            $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio, $Fecha, $IDElemento, $Hora);

                            $Intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = $id_disponibilidad");

                            $Turnos = $datos_tipo_reserva[NumeroTurnos];
                            $minutos = $Turnos * $Intervalo;

                            $validar = SIMServicioReserva::valida_reservas_activas($IDClub, $datos_servicio[IDServicio], $datos_reserva[Fecha], $datos_reserva[Hora], $datos_reserva[IDSocio], $datos_reserva[IDSocioBeneficiario], $ValidaSeman, $ValidaFin, $NumeroSeman, $NumeroFin, $Invitados, 1, $ValidaGeneral, $NumeroGeneral, $minutos, $IDReserva);

                            if ($validar["success"] == false) {
                                return $validar;
                            }
                        }

                        $respuesta["message"] = 'Guardado';
                        $respuesta["success"] = true;
                        $respuesta["response"] = 'Guardado';
                    } else {
                        $respuesta["message"] = "Error la reserva no existe o no pertenece al club";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "La reserva no puede ser actulizada porque la cantidad de invitados es menor a la permitida";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "No es posible editar la reserva, debe eliminar y volver a crear";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "22." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function del_invitado_servicio($IDClub, $IDReserva, $IDReservaGeneralInvitado, $EliminarParaMi = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReserva) && !empty($IDReservaGeneralInvitado)) {

            //verifico que el invitado exista y pertenezca al club
            $id_invitado_reserva = $dbo->getFields("ReservaGeneralInvitado", "IDReservaGeneralInvitado", "IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "'");

            if (!empty($id_invitado_reserva)) {

                $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");

                //Para bellavista solo el dueño de la reserva puede eliminar invitados para los sabados
                if ($IDClub == 8 || $IDClub == 112) {
                    $dia_semana = date("w", strtotime($datos_reserva["Fecha"]));
                    if ((int) $dia_semana == 6) {
                        $respuesta["message"] = "Lo sentimos solo el capitan puede modificar la reserva";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["IDServicioElemento"], $datos_reserva["Hora"]);
                $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array");

                //quito 1 al dueño de la reserva
                $MinimoPersonasTurno = $datos_disponibilidad["MinimoInvitados"] - 1;

                if ($datos_reserva["IDServicioTipoReserva"] > 0) {
                    $datos_servicio_tipo = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $datos_reserva["IDServicioTipoReserva"] . "' ", "array");
                    $MinimoPersonasTurno = $datos_servicio_tipo["MinimoParticipantes"];
                }

                //Invitados de la reserva
                $sql_invitados = $dbo->query("SELECT count(IDReservaGeneralInvitado) as TotalInvitados
																			 FROM ReservaGeneralInvitado
																			 WHERE IDReservaGeneral = '" . $IDReserva . "'");

                $row_invitado = $dbo->fetchArray($sql_invitados);
                //Le quito el invitado que trata de eliminar para saber si cumple con la condición
                $TotalInvitadoReserva = (int) $row_invitado["TotalInvitados"] - 1;

                if ($TotalInvitadoReserva >= $MinimoPersonasTurno) {
                    // Borrar los invitados socios o externos
                    $sql_elimina_invitado = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "'");
                    $respuesta["message"] = "eliminado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "eliminado";
                } else {
                    $respuesta["message"] = "El invitado no puede ser eliminado por que la reserva no cumple con el numero mínimo para hacer la reserva";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Error la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "23." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_reserva_golf($IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, $Tee)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDElemento) && !empty($IDServicio) && !empty($Fecha) && !empty($Hora) && !empty($Tee)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                //Guardo la reserva
                $sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr)
											    Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $Fecha . "', '" . $Hora . "', '" . $Tee . "', 'WebService',NOW())");

                $id_reserva_general = $dbo->lastID();

                $array_Invitados = $Invitados;
                if (count($array_Invitados) > 0) :
                    foreach ($array_Invitados as $id_valor => $valor) :
                        // Guardo los invitados socios o externos
                        $sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre, Cedula, Correo)
																						Values ('" . $id_reserva_general . "','" . $valor["IDSocioInvitado"] . "', '" . $valor["NombreInvitado"] . "','" . $valor["Cedula"] . "','" . $valor["Correo"] . "')");
                    endforeach;
                endif;

                $array_Campos = $Campos;
                if (count($array_Campos) > 0) :
                    foreach ($array_Campos as $id_valor_campo => $valor_campo) :
                        // Guardo los campos personalizados
                        $sql_inserta_campo = $dbo->query("Insert Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor)
																						Values ('" . $id_reserva_general . "','" . $valor_campo["IDServicioCampo"] . "', '" . $valor_campo["Valor"] . "')");
                    endforeach;
                endif;

                $respuesta["message"] = 'Guardado';
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = 'Error el socio no existe o no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "24." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
    
} //end class
