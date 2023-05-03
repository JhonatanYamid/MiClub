<?php
class SIMWebServiceCasaHotel
{



    function get_meses_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario, $anios = 3)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $config1 = $dbo->getFields("ConfiguracionCasaHotel", "Anticipacion", "IDClub = '$IDClub'  LIMIT 1");
        $mesesanticipacion = intval($config1);
        $config = $dbo->getFields("ConfiguracionCasaHotel", "AniosDisponibles", "IDClub = '$IDClub'  LIMIT 1");
        $anios = intval($config) + 1;
        $nuevafecha = date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d'))));
        $meses =  array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');

        for ($i = 1; $i <= $anios; $i++) {
            $mesesdisp = array();
            $nuevafecha = date('Y-m-d', strtotime('+1 year', strtotime($nuevafecha)));
            $anio = explode("-", $nuevafecha)[0];

            foreach ($meses as $j => $mes) {
                $fechamin = date('Y-m-d', strtotime("+$mesesanticipacion month", strtotime(date('Y-m') . "-01")));
                $fechaCustom = date('Y-m-d', strtotime("$anio-$j-01")); // 2023-01-01
                $respuestaSe = SIMWebServiceCasaHotel::get_semanas_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario, "estadia", '[{"Ano": "' . $anio . '", "Mes": "' . strval($j) . '"}]');

                if ($respuestaSe['response'] != NULL && $fechaCustom >= $fechamin) {
                    $mesesdisp[] = array("Nombre" => $mes . $i, "Mes" => strval($j), "Disponible" => "S");
                } else {
                    $mesesdisp[] = array("Nombre" => $mes . $i, "Mes" => strval($j), "Disponible" => "N");
                }
            }
            $response[] = array("Ano" => $anio, "Meses" => $mesesdisp);
        }



        $respuesta["message"] = 'OK';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }
    function get_meses_disponibles_casa_hotel_express($IDClub, $IDSocio, $IDUsuario, $anios = 3)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $config1 = $dbo->getFields("ConfiguracionCasaHotel", "Anticipacion", "IDClub = '$IDClub'  LIMIT 1");
        $mesesanticipacion = intval($config1);
        $config = $dbo->getFields("ConfiguracionCasaHotel", "AniosDisponibles", "IDClub = '$IDClub'  LIMIT 1");
        $anios = intval($config) + 1;
        $nuevafecha = date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d'))));
        $meses =  array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');

        for ($i = 1; $i <= $anios; $i++) {
            $mesesdisp = array();
            $nuevafecha = date('Y-m-d', strtotime('+1 year', strtotime($nuevafecha)));
            $anio = explode("-", $nuevafecha)[0];

            foreach ($meses as $j => $mes) {
                $fechamin = date('Y-m-d', strtotime("+$mesesanticipacion month", strtotime(date('Y-m') . "-01")));
                $fechaCustom = date('Y-m-d', strtotime("$anio-$j-01")); // 2023-01-01
                $respuestaSe = SIMWebServiceCasaHotel::get_semanas_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario, "estadia", '[{"Ano": "' . $anio . '", "Mes": "' . strval($j) . '"}]');

                if ($respuestaSe['response'] != NULL && $fechaCustom <= $fechamin) {
                    $mesesdisp[] = array("Nombre" => $mes . $i, "Mes" => strval($j), "Disponible" => "S");
                } else {
                    $mesesdisp[] = array("Nombre" => $mes . $i, "Mes" => strval($j), "Disponible" => "N");
                }
            }
            $response[] = array("Ano" => $anio, "Meses" => $mesesdisp);
        }



        $respuesta["message"] = 'OK';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }
    function dividir_en_semanas($mes, $anio)
    {
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $fecha_inicio = new DateTime($anio . '-' . $mes . '-01');
        $fecha_inicio->modify('next friday');
        $semana_actual = 1;
        $semanas_mes = array();

        for ($i = 1; $i <= $dias_mes; $i++) {
            if ($i === 1 || $fecha_inicio->format('N') === '5') {
                $semanas_mes[$semana_actual] = array(
                    'inicio' => $fecha_inicio->format('Y-m-d'),
                    'fin' => $fecha_inicio->modify('+6 days')->format('Y-m-d')
                );
                $fecha_inicio->modify('+1 day');
                $semana_actual++;
            } else {
                $fecha_inicio->modify('+1 day');
            }

            if ($semana_actual > 4) {
                break;
            }
        }

        if ($semana_actual <= 4) {
            $mes_siguiente = $mes + 1;
            $anio_siguiente = $anio;
            if ($mes_siguiente > 12) {
                $mes_siguiente = 1;
                $anio_siguiente++;
            }
            $fecha_inicio = new DateTime($anio_siguiente . '-' . $mes_siguiente . '-01');
            $fecha_inicio->modify('next friday');

            for ($i = $semana_actual; $i <= 4; $i++) {
                $semanas_mes[$i] = array(
                    'inicio' => $fecha_inicio->format('Y-m-d'),
                    'fin' => $fecha_inicio->modify('+6 days')->format('Y-m-d')
                );
                $fecha_inicio->modify('+1 day');
            }
        }

        return $semanas_mes;
    }
    function get_semanas_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario, $TipoReserva, $Fechas)
    {

        $dbo = &SIMDB::get();
        $Fechas = str_replace("\\&quot;", '"', $Fechas);
        $Fechas = str_replace('\\"', '"', $Fechas);
        $meses = json_decode($Fechas);
        $config = $dbo->getFields("Socio", "SemanaAsociada", "IDClub = '$IDClub' AND IDSocio = '$IDSocio' LIMIT 1");
        $semanaAsociada = intval($config);

        foreach ($meses as $mes) {

            $anio = $mes->Ano;
            $meses = $mes->Mes;

            $semanas = SIMWebServiceCasaHotel::dividir_en_semanas($meses, $anio);
            foreach ($semanas as $i => $semana) {
                if ($i == $semanaAsociada) {
                    $semanaNom = "Semana" . $i;
                    $response[] = array(
                        "IDSemana" => $mes->Ano . $mes->Mes . $i, "Ano" => $mes->Ano, "Mes" => $mes->Mes,
                        "NombreSemana" => $semanaNom, "FechaInicial" => $semana['inicio'], "FechaFinal" => $semana['fin']
                    );
                }
            }
        }


        $respuesta["message"] = 'OK';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }
    function get_semanas_disponibles_cambiar_fecha_casa_hotel($IDClub, $IDSocio, $IDUsuario, $Ano, $Mes)
    {

        $dbo = &SIMDB::get();

        $anio = $Ano; // Obtener el año actual

        $meses = $Mes; // Cambiar por el número del mes que quieras            

        $config = $dbo->getFields("Socio", "SemanaAsociada", "IDClub = '$IDClub' AND IDSocio = '$IDSocio' LIMIT 1");
        $semanaAsociada = intval($config);
        $semanas = SIMWebServiceCasaHotel::dividir_en_semanas($meses, $anio);
        foreach ($semanas as $i => $semana) {
            if ($i != $semanaAsociada) {
                $semanaNom = "Semana" . $i;
                $response[] = array(
                    "IDSemana" => $anio .  $meses . $i, "Ano" => $anio, "Mes" => $meses,
                    "NombreSemana" => $semanaNom, "FechaInicial" => $semana['inicio'], "FechaFinal" => $semana['fin']
                );
            }
        }


        $respuesta["message"] = 'OK';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }
    function get_semanas_disponibles_express_casa_hotel($IDClub, $IDSocio, $IDUsuario, $Ano, $Mes)
    {

        $dbo = &SIMDB::get();

        $anio = $Ano; // Obtener el año actual

        $meses = $Mes; // Cambiar por el número del mes que quieras            

        $config = $dbo->getFields("Socio", "SemanaAsociada", "IDClub = '$IDClub' AND IDSocio = '$IDSocio' LIMIT 1");
        $semanaAsociada = intval($config);
        $semanas = SIMWebServiceCasaHotel::dividir_en_semanas($meses, $anio);
        foreach ($semanas as $i => $semana) {
            if ($i == $semanaAsociada) {
                $semanaNom = "Semana" . $i;
                $response[] = array(
                    "IDSemana" => $anio .  $meses . $i, "Ano" => $anio, "Mes" => $meses,
                    "NombreSemana" => $semanaNom, "FechaInicial" => $semana['inicio'], "FechaFinal" => $semana['fin']
                );
            }
        }


        $respuesta["message"] = 'OK';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }


    public function get_configuracion_casa_hotel($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionCasaHotel  WHERE IDClub = '" . $IDClub . "' LIMIT 1";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["LabelBotonMisReservas"] = $r["LabelBotonMisReservas"];
                $configuracion["IconoHacerReservas"] = $r["IconoHacerReservas"];
                $configuracion["LabelHacerReservas"] = $r["LabelHacerReservas"];
                $configuracion["IconoSolicitudes"] = $r["IconoSolicitudes"];
                $configuracion["LabelSolicitudes"] = $r["LabelSolicitudes"];
                $configuracion["MostrarMensajeInfoReserva"] = $r["MostrarMensajeInfoReserva"];
                $configuracion["LabelMensajeInfoReserva"] = $r["LabelMensajeInfoReserva"];
                $configuracion["PermiteTipoReserva"] = $r["PermiteTipoReserva"];
                $configuracion["MinutosEsperaReserva"] = $r["MinutosEsperaReserva"];

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response[0];
        } //End if
        else {
            $respuesta["message"] = "Hotel no está activo";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function



    public function set_reservas_casa_hotel($IDClub, $IDSocio = null, $IDUsuario = null, $TipoReserva, $Reservas)
    {

        $dbo = &SIMDB::get();

        $Reservas = str_replace("\\&quot;", '"', $Reservas);
        $Reservas = str_replace('\\"', '"', $Reservas);
        $Reservas = json_decode($Reservas);
        foreach ($Reservas as $reserva) {
            $frm = array();
            $frm["IDClub"] = $IDClub;
            $frm["Ano"] = $reserva->Ano;
            $frm["Mes"] = $reserva->Mes;
            $frm["FechaInicio"] = $reserva->FechaInicio;
            $frm["FechaFin"] = $reserva->FechaFin;
            $frm["ServicioLimpieza"] = $reserva->ServicioLimpieza;
            $frm["ServicioNinera"] = $reserva->ServicioNinera;
            $frm["ServicioAcompanante"] = $reserva->ServicioAcompanante;
            $frm["TipoReserva"] = $TipoReserva;
            $frm["IDUsuario"] = $IDUsuario;
            $frm["IDSocio"] = $IDSocio;
            $Registro = $dbo->getFields("ReservasCasaHotel", "IDReservasCasaHotel", "IDClub = '$IDClub' AND Ano = '$reserva->Ano' AND Mes = '$reserva->Mes' AND FechaInicio = '$reserva->FechaInicio' AND FechaFin = '$reserva->FechaFin'");
            if (empty($Registro)) {
                $id = $dbo->insert($frm, "ReservasCasaHotel", "IDReservasCasaHotel");
                $respuesta["message"] = 'Reservas CON exito';
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = 'El registro ya existe';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    } // fin function

    public function get_mis_reservas_casa_hotel($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $meses =  array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        if (isset($IDSocio) && $IDSocio) {
            $where = "IDClub = '$IDClub' AND IDSocio = '$IDSocio'";
        } else {
            $where = "IDClub = '$IDClub' AND IDUsuario = '$IDUsuario'";
        }
        $Registros = $dbo->fetchAll("ReservasCasaHotel", $where);
        if ($Registros) {
            foreach ($Registros as $Registro) {

                $datos = array();
                $datos['IDReserva'] = $Registro['IDReservasCasaHotel'];
                $datos['NombreReserva'] = "Tu reserva " . $meses[$Registro['Mes']];
                $datos['FechaInicial'] = $Registro['FechaInicio'];
                $datos['FechaFinal'] = $Registro['FechaFin'];
                $datos['TipoReserva'] = $Registro['TipoReserva'];
                $datos['ServicioLimpieza'] = $Registro['ServicioLimpieza'];
                $datos['ServicioNinera'] = $Registro['ServicioNinera'];
                $datos['ServicioAcompanante'] = $Registro['ServicioAcompanante'];
                $datos['Observaciones'] = $Registro['Observaciones'];
                $response[] = $datos;
            }
            $respuesta["message"] = 'Encontrados';
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = 'No se encontraron reservas';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
    public function set_eliminar_reserva_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $sql_elimina_evento_socio = $dbo->query("DELETE FROM ReservasCasaHotel WHERE IDReservasCasaHotel = '" . $IDReserva . "'");

        $respuesta["message"] = "Tu reserva ha sido eliminada";
        $respuesta["success"] = true;
        $respuesta["response"] = null;
        return $respuesta;
    }

    public function set_cambio_fecha_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDSemana)
    {
        $dbo = &SIMDB::get();
        $frm = array();
        $Ano = substr($IDSemana, 0, 4);
        $Mes = substr($IDSemana, 1, -4);
        $frm["IDClub"] = $IDClub;
        $frm["Ano"] = $Ano;
        $frm["Mes"] = $Mes;
        $frm["Semana"] = $IDSemana;
        $frm["TipoSolicitud"] = "CambioFecha";
        $frm["IDUsuario"] = $IDUsuario;
        $frm["IDSocio"] = $IDSocio;
        $id = $dbo->insert($frm, "SolicitudesCasaHotel", "IDSolicitudesCasaHotel");
        $respuesta["message"] =  "Tu solicitud de reserva esta siendo revisada";
        $respuesta["success"] = true;
        $respuesta["response"] = null;
        return $respuesta;
    }
    public function set_express_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDSemana)
    {
        $dbo = &SIMDB::get();
        $frm = array();
        $Ano = substr($IDSemana, 0, 4);
        $Mes = substr($IDSemana, 1, -4);
        $frm["IDClub"] = $IDClub;
        $frm["Ano"] = $Ano;
        $frm["Mes"] = $Mes;
        $frm["Semana"] = $IDSemana;
        $frm["TipoSolicitud"] = "Express";
        $frm["IDUsuario"] = $IDUsuario;
        $frm["IDSocio"] = $IDSocio;
        $id = $dbo->insert($frm, "SolicitudesCasaHotel", "IDSolicitudesCasaHotel");
        $respuesta["message"] =  "Tu solicitud de reserva esta siendo revisada Express";
        $respuesta["success"] = true;
        $respuesta["response"] = null;
        return $respuesta;
    }
    public function set_upgrade_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva, $IDMejora)
    {
        $dbo = &SIMDB::get();
        $frm = array();
        $frm["IDClub"] = $IDClub;
        $frm["IDReserva"] = $IDReserva;
        $frm["IDMejora"] = $IDMejora;
        $frm["IDUsuario"] = $IDUsuario;
        $frm["IDSocio"] = $IDSocio;
        $id = $dbo->insert($frm, "UpgradesReservaCasaHotel", "IDUpgradesReservaCasaHotel");
        $respuesta["message"] =  "Upgrade Hecho exito";
        $respuesta["success"] = true;
        $respuesta["response"] = null;
        return $respuesta;
    }
    public function get_mis_reservas_upgrade_casa_hotel($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $meses =  array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        if (isset($IDSocio) && $IDSocio) {
            $where = "IDClub = '$IDClub' AND IDSocio = '$IDSocio'";
        } else {
            $where = "IDClub = '$IDClub' AND IDUsuario = '$IDUsuario'";
        }
        $Registros = $dbo->fetchAll("ReservasCasaHotel", $where);
        if ($Registros) {
            foreach ($Registros as $Registro) {

                $datos = array();
                $datos['IDReserva'] = $Registro['IDReservasCasaHotel'];
                $datos['NombreReserva'] = "Tu reserva " . $meses[$Registro['Mes']];
                $datos['FechaInicial'] = $Registro['FechaInicio'];
                $datos['FechaFinal'] = $Registro['FechaFin'];
                $response[] = $datos;
            }
            $respuesta["message"] = 'Encontrados';
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = 'No se encontraron reservas';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
    public function get_upgrades_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $where2 = "";
        if (isset($IDSocio) && $IDSocio) {
            $where = " AND IDClub = '$IDClub' AND IDSocio = '$IDSocio'";
        } else {
            $where = " AND IDClub = '$IDClub' AND IDUsuario = '$IDUsuario'";
        }
        $Registros = $dbo->fetchAll("UpgradesCasaHotel", $where2);

        if ($Registros) {
            foreach ($Registros as $Registro) {
                $datos = array();



                $config = $dbo->getFields("UpgradesReservaCasaHotel", "IDUpgradesReservaCasaHotel", "IDReserva = " . $IDReserva . " AND IDMejora = " . $Registro['IDUpgradesCasaHotel'] . $where . " LIMIT 1");
                // var_dump($config);
                if ($config == false) {
                    $datos['IDMejora'] = $Registro['IDUpgradesCasaHotel'];
                    $datos['NombreMejora'] = $Registro['NombreMejora'];
                    $response[] = $datos;
                }
            }
            // die;
            $respuesta["message"] = null;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = 'No se encontraron reservas';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
} //end class
