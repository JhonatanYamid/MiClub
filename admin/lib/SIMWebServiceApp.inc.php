<?php

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class SIMWebServiceApp
{

    public function validar_disponibilidad_auxiliar($IDAuxiliar, $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub)
    {
        $dbo = &SIMDB::get();
        $disponible = "";
        $fecha_hora_solicitud = strtotime($Fecha . " " . $Hora);
        //Selecciono los auxiliares que tengan el mismo numero de documento, ya que un auiliar puede estar en mas servicios por ejemplo masajista hombre y mujer
        $documento_auxiliar = $dbo->getFields("Auxiliar", "NumeroDocumento", "IDAuxiliar = '" . $IDAuxiliar . "'");
        $sql_auxiliar = "Select * From Auxiliar Where NumeroDocumento = '" . $documento_auxiliar . "'";
        $result_auxiliar = $dbo->query($sql_auxiliar);
        while ($row_auxiliar = $dbo->fetchArray($result_auxiliar)) :
            $array_id_auxiliar[] = $row_auxiliar["IDAuxiliar"];
        endwhile;
        if (count($array_id_auxiliar) > 0) :
            $id_auxiliar_doc = implode(",", $array_id_auxiliar);
        endif;
        //Consulto que el auxiliar no este reservado en otro servicio en la misma fecha y hora con un lapso de 2 horas
        $sql_reserva_aux = "Select * From ReservaGeneral Where IDAuxiliar in (" . $id_auxiliar_doc . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva  = 1)";
        $result_reserva_aux = $dbo->query($sql_reserva_aux);
        while ($row_reserva_aux = $dbo->fetchArray($result_reserva_aux)) :
            //consulto el intervalo de la disponibilidad
            $intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_reserva_aux["IDDisponibilidad"] . "'");
            $fecha_hora_inicial = strtotime($row_reserva_aux["Fecha"] . " " . $row_reserva_aux["Hora"]);
            $fecha_hora_final = strtotime('+' . $intervalo . ' minute', $fecha_hora_inicial);

            //echo "<br>Solicitud: " . date("Y-m-d H:i:s",$fecha_hora_solicitud);
            //echo "<br>Inicial: " . date("Y-m-d H:i:s",$fecha_hora_inicial);
            //echo "<br>Final: " . date("Y-m-d H:i:s",$fecha_hora_final);
            if ($fecha_hora_solicitud >= $fecha_hora_inicial && $fecha_hora_solicitud <= $fecha_hora_final) :
                $disponible = "N";
                break;
            endif;
        endwhile;
        //echo "Dispo " . $disponible;
        //exit;
        return $disponible;
    }

    public function set_reserva_cumplida($IDClub, $IDSocio, $IDUsuario, $IDReservaGeneral, $ReservaCumplida, $ReservaCumplidaSocio, $Observacion, $Invitados)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDUsuario) && !empty($IDReservaGeneral) && !empty($ReservaCumplida) && !empty($ReservaCumplidaSocio)) {

            $datos_invitado = json_decode($Invitados, true);
            //Recorro los datos de los invitados
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :
                    $IDReservaGeneralInvitado = $detalle_datos["IDReservaGeneralInvitado"];
                    $ReservaCumplidaInvitado = $detalle_datos["ReservaCumplidaInvitado"];
                    if ($ReservaCumplidaInvitado == "S") {
                        $invitado_asiste++;
                    }

                    $sql_actualiza_invitado = "Update ReservaGeneralInvitado Set Cumplida = '" . $ReservaCumplidaInvitado . "' Where IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "' and IDReservaGeneral = '" . $IDReservaGeneral . "'";
                    $dbo->query($sql_actualiza_invitado);
                endforeach;
            endif;

            if (count($datos_invitado) > 0 && ($invitado_asiste != count($datos_invitado) || $ReservaCumplidaSocio == "N")) {
                $estado_reserva_cumplida = "N";
            } elseif (count($datos_invitado) <= 0) {
                $estado_reserva_cumplida = $ReservaCumplidaSocio;
            } else {
                $estado_reserva_cumplida = $ReservaCumplida;
            }

            //Actualizo Estado de reserva
            $sql_reserva_estado = "Update ReservaGeneral Set Cumplida = '" . $estado_reserva_cumplida . "', CumplidaCabeza = '" . $ReservaCumplidaSocio . "', FechaCumplida = NOW(), IDUsuarioCumplida = '" . $IDUsuario . "', ObservacionCumplida = '" . $Observacion . "' Where IDReservaGeneral = '" . $IDReservaGeneral . "'";
            $dbo->query($sql_reserva_estado);

            // BUSCAMOS LAS RESERVAS AUTOMATICAS Y LAS ACTULIZAMOS IGUAL QUE LAS DEMAS

            $sqlAutomaticas = "SELECT IDReservaGeneralAsociada FROM ReservaGeneralAutomatica WHERE IDReservaGeneral = $IDReservaGeneral";
            $qryAutomaticas = $dbo->query($sqlAutomaticas);

            while ($row = $dbo->fetchArray($qryAutomaticas)) {
                $sqlUpdate = "UPDATE ReservaGeneral
                                    SET Cumplida = '$estado_reserva_cumplida',
                                    FechaCumplida = NOW(),
                                    IDUsuarioCumplida = '$IDUsuario',
                                    CumplidaCabeza = '$ReservaCumplidaSocio'
                                    WHERE IDReservaGeneral = '$row[IDReservaGeneralAsociada]'";
                $qryUpdate = $dbo->query($sqlUpdate);
            }

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "121. Atencion faltan parametros ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_reconfirma_reserva($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $MinimoInvitados = 0;
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {
            //Actualizo Estado de reserva
            $sql_invitados_reserva = $dbo->query("SELECT count(*) as TotalConfirmado FROM ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "' and Confirmado = 'S' ");
            $r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva);
            if ((int) $r_invitados_reserva["TotalConfirmado"] >= $MinimoInvitados) {
                $sql_reserva_estado = "UPDATE ReservaGeneral Set ConfirmadaSocio = 'S', FechaConfirmadaSocio = NOW() Where IDReservaGeneral = '" . $IDReserva . "'";
                $dbo->query($sql_reserva_estado);
                $respuesta["message"] = "Su reserva fue confirmada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "No es posible confirmar la reserva sin al menos " . $MinimoInvitados . " invitados confirmados";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "121. Atencion faltan parametros ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_invitado_grupo_servicio($IDClub, $IDSocio, $IDReserva, $Invitados)
    {
        $dbo = &SIMDB::get();
        //if( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDReserva ) ){
        if (!empty($IDClub) && !empty($IDReserva)) {

            //solo se puede hacer confirmaciones antes de las 4pm del jueves
            if ($IDClub == 112 || $IDClub == 8) {
                $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral  = '" . $IDReserva . "' ", "array");
                $Hoy = date("Y-m-d H:i:s");
                $Fecha = $datos_reserva["Fecha"];
                $FechaMaxima = strtotime('-2 day', strtotime($Fecha));
                $FechaMaxima = date("Y-m-d 16:00:00", $FechaMaxima);
                if ($Hoy <= $FechaMaxima) {
                    //Bien
                } else {
                    $respuesta["message"] = "El tiempo limite para confirmar reserva fue superado (Jueves 16:00:00)";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }

            $datos_invitado = json_decode($Invitados, true);
            $TotalConfirmados = 0;
            //Recorro los datos de los invitados
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :
                    $IDReservaGeneralInvitado = $detalle_datos["IDReservaGeneralInvitado"];
                    $SeleccionadoGrupo = $detalle_datos["SeleccionadoGrupo"];
                    if ($SeleccionadoGrupo == "S") {
                        $TotalConfirmados++;
                    }

                    if ($TotalConfirmados <= 4) {
                        $sql_actualiza_invitado = "UPDATE ReservaGeneralInvitado Set Confirmado = '" . $SeleccionadoGrupo . "' Where IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "' and IDReservaGeneral = '" . $IDReserva . "'";
                        $dbo->query($sql_actualiza_invitado);
                    }
                endforeach;
            endif;

            if ($TotalConfirmados <= 4) {
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Atencion solo es posible confirmar maximo a 4 invitados";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "CF1. Atencion faltan parametros " . $IDClub . "-" . $IDSocio . "-" . $IDReserva;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_edita_auxiliar_reserva($IDClub, $IDSocio, $IDReservaGeneral, $ListaAuxiliar)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReservaGeneral) && !empty($ListaAuxiliar)) {


            if ($IDClub == "227") {
                require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
                $respuesta_editar = SIMWebServiceCountryMedellin::App_AgregarAsistenteAReserva($IDClub, $IDReservaGeneral, $IDSocio, $ListaAuxiliar);
                return $respuesta_editar;
            }

            //Verifico de nuevo que la lista de auxiliares seleccionados esten disponibles
            if (!empty($ListaAuxiliar)) :
                $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
                $datos_auxiliares_revisar = json_decode($ListaAuxiliar, true);
                $response_dispo_aux = SIMWebService::get_auxiliares($IDClub, $datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["Hora"], "N", $IDReservaGeneral);
                foreach ($response_dispo_aux["response"] as $datos_conf_aux) :
                    foreach ($datos_conf_aux["Auxiliares"] as $datos_aux) :
                        $array_aux_disponibles[] = $datos_aux["IDAuxiliar"];
                    endforeach;
                endforeach;

                if (count($datos_auxiliares_revisar) > 0) :
                    foreach ($datos_auxiliares_revisar as $key_aux => $auxiliar_seleccionado) :
                        if (!in_array($auxiliar_seleccionado["IDAuxiliar"], $array_aux_disponibles)) :
                            $respuesta["message"] = "El auxiliar " . $auxiliar_seleccionado["Nombre"] . " no esta disponible en ese horario" . $IDReservaGeneral;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endforeach;
                endif;
            endif;

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

            //Actualizo Estado de reserva
            $sql_reserva_estado = "Update ReservaGeneral Set IDAuxiliar = '" . $IDAuxiliar . "' Where IDReservaGeneral = '" . $IDReservaGeneral . "' Limit 1";
            $dbo->query($sql_reserva_estado);

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "A121. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function valida_pago_reserva($IDClub, $IDSocio, $IDReservaGeneral)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM ReservaGeneral WHERE IDReservaGeneral = '" . $IDReservaGeneral . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4) { // payU
                    if ($r["EstadoTransaccion"] == "") :
                        $respuesta["message"] = "P0. No se ha obtenido respuesta de la transaccion de pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4") :
                        $respuesta["message"] = "P1. Reserva pagada correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = "P4. El pago no fue realizado";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    endif;
                } elseif ($r["IDTipoPago"] == 12) {
                    if ($r["EstadoTransaccion"] == "A") :
                        $respuesta["message"] = "C1. Reserva pagada correctamente!";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :

                        //Compruebo de nuevo la transaccion para confirmar que no este pagada
                        $orden = $dbo->getFields("PagoCredibanco", "NumeroFactura", "reserved12 = '" .  $r["IDReservaGeneral"] . "'");
                        if (empty($orden)) {
                            $respuesta["message"] = "C2.Reserva en espera de confirmacion de pago";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        } else {
                            $repuesta = SIMPasarelaPagos::CredibancoRespuestaV2($orden);
                            if ($repuesta["success"]) {
                                $estado = $repuesta["response"]["orderStatus"];
                                switch ($estado) {
                                    case "0":
                                        // $estadoTx = "NO PAGADO";
                                        $respuesta["message"] = "C10. No pagado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    case "1":
                                    case "7":
                                        // $estadoTx = "PENDIENTE";
                                        $respuesta["message"] = "C11. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;

                                    case "2":
                                        // $estadoTx = "APROBADO";
                                        $respuesta["message"] = "C12.Reserva pagada correctamente.";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                    case "3":
                                    case "6":
                                        // $estadoTx = "RECHAZADO";
                                        $respuesta["message"] = "C13.Transaccion rechazada";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    default:
                                        // $estadoTx = "OTRO";
                                        $respuesta["message"] = "C14. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                }
                            } else {
                                $respuesta["message"] = "C4. El pago no fue realizado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = $response;
                            }
                        }
                    endif;
                } elseif ($r["IDTipoPago"] == 16 && $r[MedioPago] == 'Talonera') {

                    $respuesta["message"] = "Talonera descontada exitosamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } elseif ($r["IDTipoPago"] == 15 && $r[MedioPago] == 'ORDEN CLUB') {

                    $respuesta["message"] = "Orden en Club creada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } elseif (($r["IDTipoPago"] == 18 || $r["IDTipoPago"] == 19 || $r["IDTipoPago"] == 13) && $r[MedioPago] == 'WOMPI-APPROVED') {

                    $respuesta["message"] = "Reserva pagada con wompi correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } elseif ($r["IDTipoPago"] == 20 && $r[MedioPago] == "ePayco-OK") {

                    $respuesta["message"] = "Reserva pagada con ePayco correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } elseif ($r[PagoParaMasTarde] == 'S') {
                    $respuesta["message"] = "Recuerde el pago de la reserva lo puede hacer desde la sección de mis reservas";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "La reserva no fue pagada por pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            }
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function verifica_place_to_pay($IDClub, $IDSocio, $IDEventoRegistro)
    {

        $dbo = &SIMDB::get();
        $transaccion_pendiente = "";
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_transaccion = $dbo->fetchAll("PeticionesPlacetoPay", " tipo='EventoRegistro' and IDClub = '" . $IDClub . "' and IDMaestro = '" . $IDEventoRegistro . "' and estado_transaccion = 'OK' ", "array");
        $login = $datos_club["ApiLogin"];
        $secretKey = $datos_club["ApiKey"];

        if ((int) $datos_transaccion["IDPeticionesPlacetoPay"] > 0) {
            //obtención de nonce
            if (function_exists('random_bytes')) {
                $nonce = bin2hex(random_bytes(16));
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $nonce = bin2hex(openssl_random_pseudo_bytes(16));
            } else {
                $nonce = mt_rand();
            }
            $nonceBase64 = base64_encode($nonce);

            $seed = date('c');
            $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

            $auth = array(
                "auth" => array(
                    "login" => $login,
                    "seed" => $seed,
                    "nonce" => $nonceBase64,
                    "tranKey" => $tranKey
                ),
            );

            if ($datos_club["IsTest"] == 1) {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
            } else {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
            }
            $url_place_to_pay . 'redirection/api/session/' . $datos_transaccion["request_id"];
            $ch = curl_init($url_place_to_pay . 'redirection/api/session/' . $datos_transaccion["request_id"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = ['Content-Type:application/json; charset=utf-8'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth));
            $response = curl_exec($ch);
            curl_close($ch);
            // do anything you want with your response
            $respuesta = json_decode($response, true);

            if ($respuesta["status"]["status"] == "PENDING") {
                $transaccion_pendiente = 1;
            }
        }

        return $transaccion_pendiente;
    }

    public function set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio = "", $Categoria = "", $Masivo = "", $CodigoCarne, $ClaveApp = "", $IDSocioSistemaExterno = "", $array_socios = "", $PermiteReservar = "", $SocioMora = "", $foto = "")
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($NumeroDocumento) && !empty($Nombre)) {

            // DESHABILITADO PARA LA HACIENDA FONTANAR
            if ($IDClub == 18) :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'NoestapermitidalacreacióndeusuariosparaHaciendaFontanarporelServicioWebdeMiClub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;

                return $respuesta;
            endif;

            $Documento = trim($row["NumeroDocumento"]);
            $Nombre = str_replace("'", "", $Nombre);
            $Apellido = str_replace("'", "", $Apellido);
            //Consulto si el socio ya existe
            if (count($array_socios) > 0 && is_array($array_socios)) {
                $con_array = "S";
                if ((int) $array_socios[trim($NumeroDocumento)] > 0) {
                    $row_socio["IDSocio"] = $array_socios[trim($NumeroDocumento)];
                }
            } else {
                $con_array = "N";
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                $result_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($result_socio);
            }

            //Estado Socio
            if ($EstadoSocio == "A") :
                $estado_socio = 1;
            else :
                $estado_socio = 2;
            endif;

            if ($IDClub == 70) {
                $estado_socio = $EstadoSocio;

                $con_array = "N";
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                $result_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($result_socio);
            }

            if ($IDClub == 9) :
                $con_array = "N";
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                $result_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($result_socio);
            endif;

            if (empty($UsuarioApp)) :
                $UsuarioApp = $NumeroDocumento;
            endif;

            if (empty($ClaveApp)) {
                $clave_socio = sha1(trim($NumeroDocumento));
            } else {
                $clave_socio = $ClaveApp;
            }

            if (empty($PermiteReservar)) {
                $Permite_Reservar = 'S';
            } else {
                $Permite_Reservar = $PermiteReservar;
            }

            if ($IDClub == 135) {

                $Permite_Reservar = 'S';
                $estado_socio = $EstadoSocio;

                if ($PermiteReservar == 'S')
                    $PermiteServicio = 5;
                else
                    $PermiteServicio = 6;

                $actulizaPermiteServicio = ", IDPermisoServicio = '" . $PermiteServicio . "' ";

                if ($SocioMora == 'M') :
                    $Permite_Reservar = 'N';
                endif;
            }

            //$id_perentesco=$dbo->getFields( "Parentesco" , "IDParentesco" , "Nombre = '" . $Parentesco . "'" );

            if (!empty($Categoria)) {
                $id_categoria = $dbo->getFields("Categoria", "IDCategoria", "IDSistemaExterno = '" . $Categoria . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_categoria)) {
                    $id_categoria = $Categoria;
                }
            }

            if ($IDClub == 135) {
                $id_categoria = $Categoria;
            }

            if ($IDClub == 130 || $IDClub == 232) {
                $CambiarClave = 'N';
            } else {
                $CambiarClave = 'S';
            }

            if ($IDClub == 141) {
                $estado_socio = $EstadoSocio;
            }

            if ($IDClub != 141)
                $InvitacionesPermitidasMes = 100;

            if ((int) $row_socio["IDSocio"] <= 0) :
                //Crear Socio
                $inserta_socio = "INSERT INTO Socio (IDClub, IDSocioSistemaExterno, IdentificadorExterno, IDEstadoSocio, Accion, AccionPadre, IDParentesco, Parentesco, TipoSocio, IDCategoria, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Telefono, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar,IDPermisoServicio, CambioClave,FotoActualizadaSocio,Predio,CodigoCarne)
											  Values ('" . $IDClub . "','" . $IDSocioSistemaExterno . "','" . $IDSocioSistemaExterno . "','" . $estado_socio . "','" . $Accion . "','" . $AccionPadre . "','" . $Parentesco . "','" . $Parentesco . "','" . $TipoSocio . "','" . $id_categoria . "','" . $Genero . "','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $UsuarioApp . "','" . $clave_socio . "','" . $CorreoElectronico . "',
												'" . $Telefono . "','" . $Celular . "','" . $FechaNacimiento . "','Cron',NOW(),'$InvitacionesPermitidasMes','$InvitacionesPermitidasMes','" . $Permite_Reservar . "', '" . $PermiteServicio . "', '" . $CambiarClave . "','S','" . $Predio . "','" . $CodigoCarne . "')";

                //echo "<br>".$inserta_socio;
                $dbo->query($inserta_socio);
                $id = $dbo->lastID();
                $IDSocio = $id;
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','" . $inserta_socio . "','" . json_encode($parameters) . "')");

                $parametros_codigo_barras = $NumeroDocumento;
                //$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$id);
                //actualizo codigo barras
                //$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");

                if ($frm[IDClub] == 34) :
                    $parametros_codigo_qr = $frm[NumeroDocumento];
                else :
                    $parametros_codigo_qr = $frm[NumeroDocumento] . "\r\n";
                endif;

                if ($Masivo != "S") :
                    $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                    $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $id . "'");
                endif;

            else :
                //Actualiza Socio

                $actualiza_socio = "Update Socio
                                        set IDEstadoSocio = '" . $estado_socio . "',
                                        IDSocioSistemaExterno='" . $IDSocioSistemaExterno . "',
                                        IdentificadorExterno='" . $IDSocioSistemaExterno . "',
                                        Accion = '" . $Accion . "',
                                        AccionPadre='" . $AccionPadre . "',
                                        Parentesco = '" . $Parentesco . "',
                                        TipoSocio = '" . $TipoSocio . "',
                                        Telefono = '" . $Telefono . "',
                                        IDCategoria= '" . $id_categoria . "',
                                        Celular = '" . $Celular . "',
                                        Direccion = '" . $Direccion . "',
                                        Nombre = '" . trim($Nombre) . "',
                                        Apellido = '" . trim($Apellido) . "',
                                        NumeroDocumento = '" . $NumeroDocumento . "',
                                        CorreoElectronico = '" . $CorreoElectronico . "',
                                        FechaNacimiento = '" . $FechaNacimiento . "',
                                        NumeroInvitados = '$InvitacionesPermitidasMes',
                                        NumeroAccesos = '$InvitacionesPermitidasMes',
                                        Email='" . $UsuarioApp . "',
                                        Predio = '" . $Predio . "',
                                        CodigoCarne = '" . $CodigoCarne . "',
                                        UsuarioTrEd = 'Cron',
                                        FechaTrEd = NOW(),
                                        PermiteReservar = '" . $Permite_Reservar . "'
                                        " . $actulizaPermiteServicio . " Where IDSocio = '" . $row_socio["IDSocio"] . "'";
                $dbo->query($actualiza_socio);
                $IDSocio = $row_socio['IDSocio'];
            //echo "SQ:".$actualiza_socio;
            //exit;
            endif;


            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {

            $respuesta["message"] = "11." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cumpleanos_leido($IDClub, $IDSocio, $IDUsuario, $Leido)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

            if (!empty($IDSocio))
                $sql_leida = $dbo->query("UPDATE Socio Set MostrarMensajeCumpleanos = 'N' Where IDSocio = '" . $IDSocio . "'");
            else
                $sql_leida = $dbo->query("UPDATE Usuario Set MostrarMensajeCumpleanos = 'N' Where IDUsuario = '" . $IDUsuario . "' ");

            $respuesta["message"] = "Notificacion marcada como leida con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "21. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_invitado_grupo_reserva($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();


        if (!empty($IDSocio) && !empty($IDReserva)) {

            //Verifico que todavia queden cupos
            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
            $IDDisponibilidad = $datos_reserva["IDDisponibilidad"];
            if ((int) $IDDisponibilidad == 0) {
                $IDDisponibilidad = SIMWebService::obtener_disponibilidad_utilizada($datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["IDServicioElemento"], $datos_reserva["Hora"]);
            }

            $MaximoInvitados = $dbo->getFields("Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $IDDisponibilidad . "'");
            $MaximoReservaSocioServicio = $dbo->getFields("Disponibilidad", "MaximoReservaDia", "IDDisponibilidad = '" . $IDDisponibilidad . "'");

            //validacion especial arrayanes ec solo permite agregarse a un grupo despues de x horas antes de la reserva
            if ($IDClub == 23) {
                $HoraEmpieza = "09:30:00";
                $FechaHoraReserva = $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                //$HoraPermitida = strtotime( '-' . $HorasAntes . ' hour', strtotime( date( $FechaHoraReserva ) ) );
                $DiaPermitido = strtotime('-2 day', strtotime(date($FechaHoraReserva)));
                $HoraPermitida = strtotime(date("Y-m-d", $DiaPermitido) . " " . $HoraEmpieza);
                $HoraActual = strtotime(date("Y-m-d H:i:s"));
                if ($HoraActual <= $HoraPermitida) {
                    $respuesta["message"] = "Lo sentimos solo puede agregarse a un grupo libre despues de las: " . $HoraEmpieza;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }
            }

            //validacion especial bellavista solo permite agregarse a un grupo despues de x horas antes de la reserva
            $dia_semana = date("w", strtotime($datos_reserva["Fecha"]));
            if (($IDClub == 112 || $IDClub == 8) && (int) $dia_semana == 6) {
                $HoraEmpieza = "08:00:00";
                $FechaHoraReserva = $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                //$HoraPermitida = strtotime( '-' . $HorasAntes . ' hour', strtotime( date( $FechaHoraReserva ) ) );
                $DiaPermitido = strtotime('-2 day', strtotime(date($FechaHoraReserva)));
                $HoraPermitida = strtotime(date("Y-m-d", $DiaPermitido) . " " . $HoraEmpieza);
                $HoraActual = strtotime(date("Y-m-d H:i:s"));
                if ($HoraActual <= $HoraPermitida) {
                    $respuesta["message"] = "Lo sentimos solo puede agregarse a un grupo libre desde el jueves anterior despues de las: " . $HoraEmpieza;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }
            }

            //Verifico que el socio no tenga mas reserva
            $sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $datos_reserva["Fecha"] . "' and RG.IDServicio = '" . $datos_reserva["IDServicio"] . "' ORDER BY IDReservaGeneralInvitado Desc ";
            $qry_socio_grupo = $dbo->query($sql_socio_grupo);
            if ($dbo->rows($qry_socio_grupo) > 0 && $MaximoReservaSocioServicio <= 1) :
                $respuesta["message"] = "Lo sentimos ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            endif;
            $IDReservaOtra = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDSocio = '" . $IDSocio . "' and Fecha = '" . $datos_reserva["Fecha"] . "' and IDServicio = '" . $datos_reserva["IDServicio"] . "' ");
            if (!empty($IDReservaOtra)) {
                $respuesta["message"] = "Lo sentimos ya tiene una reserva activa para el mismo dia";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            }
            //Fin

            $datos_socio_agregado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            if ($datos_socio_agregado["PermiteReservar"] == "N") {
                $respuesta["message"] = "Lo sentimos no tiene permiso para reservar";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            $sql_invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '" . $IDReserva . "'";
            $result_invitado = $dbo->query($sql_invitados);
            $total_invitados = $dbo->rows($result_invitado);

            if ((int) $MaximoInvitados > (int) $total_invitados) :

                //inserto al invitado
                $insert_invitado = "INSERT INTO ReservaGeneralInvitado (IDReservaGeneral, IDSocio,AgregadoBotonPublico,FechaTrCr ) Values ('" . $IDReserva . "','" . $IDSocio . "','S',NOW())";
                $dbo->query($insert_invitado);

                //Notifico al dueño de la reserva que alguien se agregó al grupo
                $datos_socio_dueno_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
                $Mensaje = "Se agrego el socio " . $datos_socio_agregado["Nombre"] . " " . $datos_socio_agregado["Apellido"] . " a la reserva del día: " . $datos_reserva["Fecha"] . " Hora: " . $datos_reserva["Hora"];
                //SIMUtil::enviar_notificacion_push_general($IDClub,$datos_socio_dueno_reserva["IDSocio"],$Mensaje);

                $respuesta["message"] = "Agregado correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = "Lo sentimos la reserva ya tiene el cupo completo de invitados";
                $respuesta["success"] = false;
                $respuesta["response"] = null;

            endif;
        } else {
            $respuesta["message"] = "14. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function actualiza_payande($IDSocio = "")
    {
        $dbo = &SIMDB::get();

        $IDClub = 27;

        if (!empty($IDSocio)) :
            $condicion = " and IDSocio = '" . $IDSocio . "'";
        endif;

        //La base de Mi Club es la maestra entre mi club y el sistema de la página
        $sql_socio = "Select * From Socio Where IDClub = '" . $IDClub . "' " . $condicion . " Order by IDSocio ASC";
        $result_socio = $dbo->query($sql_socio);
        while ($row_socio_bd = $dbo->fetchArray($result_socio)) :
            $IDSocio = $row_socio_bd["IDSocio"];
            $DocumentoSocioPadre = "";
            $NumeroDocumento = $row_socio_bd["NumeroDocumento"];
            if ($row_socio_bd["TipoSocio"] == "Socio") :
                $TipoSocio = "TITULAR";
            else :
                $TipoSocio = "BENEFICIARIO";
                //Averiguo el socio padre
                if (!empty($row_socio_bd["AccionPadre"])) {
                    $DocumentoSocioPadre = $dbo->getFields("Socio", "NumeroDocumento", "Accion = '" . $row_socio_bd["AccionPadre"] . "'");
                }

            endif;

            $Nombre = $row_socio_bd["Nombre"];
            $Apellido = $row_socio_bd["Apellido"];
            $Telefono = $row_socio_bd["Telefono"];
            $Celular = $row_socio_bd["Celular"];
            $Email = $row_socio_bd["CorreoElectronico"];
            $FechaNacimiento = $row_socio_bd["FechaNacimiento"];
            $Usuario = $row_socio_bd["Email"];
            $Clave = $row_socio_bd["Clave"];
            $Accion = $row_socio_bd["Accion"];
            if ($row_socio_bd["IDEstadoSocio"] == "1") {
                $Autorizado = "S";
            } else {
                $Autorizado = "N";
            }

            $IDSocioPadre = $row_socio_bd["IDSocioSistemaExterno"];
            $key = "P4y4nd3Reser";
            $action = "actualizasocio";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio' => $IDSocio,
                'NumeroDocumento' => $NumeroDocumento,
                'TipoSocio' => $TipoSocio,
                'Nombre' => $Nombre,
                'Apellido' => $Apellido,
                'Telefono' => $Telefono,
                'Celular' => $Celular,
                'Email' => $Email,
                'Usuario' => $Usuario,
                'Clave' => $Clave,
                'Accion' => $Accion,
                'Autorizado' => $Autorizado,
                'IDSocioSistemaExterno' => $IDSocioSistemaExterno,
                'DocumentoSocioPadre' => $DocumentoSocioPadre,

            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            //print_r($response);
            //inserta _log
            $contador++;
        endwhile;

        return "<br>Proceso terminado con exito. Registros: " . $contador;
    }


    // funcion que valida si el popup esta configurado para determinados usuarios
    public function verifica_ver_popup($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_popup = 1;
        $IDConfiguracionClub = $r["IDConfiguracionClub"];

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoAGeneral"] == "S" || $r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "EE")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_popup[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_popup)) {
                    $mostrar_popup = 1;
                } else {
                    $mostrar_popup = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "EE") {
                $array_invitados = explode("|||", $r["UsuarioEmpleado"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_popup[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_popup)) {
                    $mostrar_popup = 1;
                } else {
                    $mostrar_popup = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {

                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_popup = 1;
                    } else {
                        $mostrar_popup = 0;
                    }
                }
            }
        }

        if ($r["DirigidoAGeneral"] == 'S') {

            $sql_resp = "Select * From ConfiguracionClub Where IDClub  = '" . $IDClub . "'";
            $r_resp = $dbo->query($sql_resp);
            if ($dbo->rows($r_resp) > 0) {
                $mostrar_popup = 1;
            }
        }

        return $mostrar_popup;
    }


    // funcion que valida si el popup esta configurado para determinados usuarios
    public function verifica_ver_popup_empl($r, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_popup = 1;
        $IDConfiguracionClub = $r["IDConfiguracionClub"];
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoAGeneral"] == "S" || $r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "EE")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_popup[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDUsuario, $array_socios_popup)) {
                    $mostrar_popup = 1;
                } else {
                    $mostrar_popup = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "EE") {
                $array_invitados = explode("|||", $r["UsuarioEmpleado"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_popup[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDUsuario, $array_socios_popup)) {
                    $mostrar_popup = 1;
                } else {
                    $mostrar_popup = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                //si va dirigido a varios grupos
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDUsuario, $array_socios_encuesta)) {
                        $mostrar_popup = 1;
                    } else {
                        $mostrar_popup = 0;
                    }
                }
            }
        }

        if ($r["DirigidoAGeneral"] == 'S') {

            $sql_resp = "Select * From ConfiguracionClub Where IDClub  = '" . $IDClub . "'";
            $r_resp = $dbo->query($sql_resp);
            if ($dbo->rows($r_resp) > 0) {
                $mostrar_popup = 1;
            }
        }

        return $mostrar_popup;
    }





    public function verifica_ver_encuesta($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        $IDEncuesta = $r["IDEncuesta"];

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T' || $r["DirigidoA"] == 'E') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "EE")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "EE") {
                $array_invitados = explode("|||", $r["UsuarioSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                //si va dirigido a varios grupos
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }
                }
            }
        }

        if ($r["UnaporSocio"] == 'S') {
            $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta = '" . $IDEncuesta . "' Limit 1";
            $r_resp = $dbo->query($sql_resp);
            if ($dbo->rows($r_resp) > 0) {
                $mostrar_encuesta = 0;
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_dotacion($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                //si va dirigido a varios grupos
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }
                }
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_encuesta2($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                //si va dirigido a varios grupos
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }
                }
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_diagnostico($r, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }
                }
            } elseif ($r["DirigidoAGeneral"] == "E") {
                $mostrar_encuesta = 1;
                //$mostrar_encuesta=0;

            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_votacion($r, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "L")) {

            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {

                //si va dirigido a varios grupos
                //esta funcionalidad es para los grupos

                $array_invitado = explode("|||", $r["SeleccionGrupo"]); //dividimos en array

                $id_grupos = str_replace("grupo-", "", $array_invitado); //quitamos el grupo-
                $id_grupos = implode(',', $id_grupos); //separamos en string por coma para hacer el where in (1,2,3)

                $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql
                $longitud = substr_count($id, ',');  //contamos las comas para saber cuantos grupos tenemos

                if ($longitud < 1) {
                    $id = substr($id_grupos, 0, -1); //quitamos la ultima coma, por que nos da error en la consulta sql

                } else {
                    $id; //dejamos asi
                }

                $separador = ","; //seleccionamos el separador para cada array
                $arreglo = explode($separador, $id); //se hace la separacion

                foreach ($arreglo as $id) {

                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$id'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_invitados = explode("|||", $SociosGrupo);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            $array_socios_encuesta[] = $datos_invitado;
                        }
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }
                }
            } elseif ($r["DirigidoAGeneral"] == "L") {
                $IDVotacionEvento = $dbo->getFields("VotacionEventoVotacion", "IDVotacionEvento", "IDVotacion = '" . $r["IDVotacion"] . "'  and Activa = 'S'");
                if (empty($IDVotacionEvento)) {
                    $mostrar_encuesta = 0;
                } else {

                    $sql_votante = "SELECT IDSocio
														FROM VotacionVotante
														WHERE Presente = 'S' and Moroso= 'N' and IDVotacionEvento = '" . $IDVotacionEvento . "'";

                    $r_votante = $dbo->query($sql_votante);
                    while ($row_votante = $dbo->fetchArray($r_votante)) {
                        $array_socios_encuesta[] = $row_votante["IDSocio"];
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }

                    if ($mostrar_encuesta == 1) {
                        $ActivaVitacion = $dbo->getFields("VotacionEventoVotacion", "Activa", "IDVotacion = '" . $r["IDVotacion"] . "' and IDVotacionEvento = '" . $IDVotacionEvento . "'");
                        if ($ActivaVitacion == "S") {
                            $mostrar_encuesta = 1;
                        } else {
                            $mostrar_encuesta = 0;
                        }
                    }
                    //verifico si la votacion esta activa

                }
            }
        }

        if ($mostrar_encuesta == 1) {
            $datos_votacion = $dbo->fetchAll("Votacion", " IDVotacion = '" . $r["IDVotacion"] . "' ", "array");
            if ($datos_votacion["UnaporSocio"] == "S") {
                //verifico si la persona ya voto

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                }

                $sql_votacion = "SELECT * FROM VotacionRespuesta WHERE IDVotacion = '" . $r["IDVotacion"] . "' AND IDSocio = '" . $IDSocio . "'";
                $result_votacion = $dbo->query($sql_votacion);
                if ($dbo->rows($result_votacion) <= 0) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } else {
                $mostrar_encuesta = 1;
            }
        }

        return $mostrar_encuesta;
    }

    public function verificar_notificacion_local($IDSocio, $IDUsuario, $IDClub)
    {
        $dbo = &SIMDB::get();

        //Diagnostico
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $r_sql = $dbo->query($sql);
        while ($r = $dbo->fetchArray($r_sql)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_diagnostico($r, $IDSocio, $IDUsuario);
            if ($mostrar_encuesta == 1) {
                $array_modulos["99"][] = $r["IDDiagnostico"];
            }
        }
        //Fin Diagnostico

        //Diagnostico
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Dotacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        $message = $dbo->rows($qry) . " Encontrados";
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_dotacion($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["102"][] = $r["IDDotacion"];
            }
        }
        //Fin Diagnostico

        //Encuesta Calificada
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta2 WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta2($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["101"][] = $r["IDEncuesta2"];
            }
        }
        //Fin encuesta

        //Encuesta Normal
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["58"][] = $r["IDEncuesta"];
            }
        }
        //Fin encuesta

        //Votacion
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Votacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        $message = $dbo->rows($qry) . " Encontrados";
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_votacion($r, $IDSocio, $IDUsuario);
            if ($mostrar_encuesta == 1) {
                $array_modulos["70"][] = $r["IDVotacion"];
            }
        }
        //Fin Votacion

        foreach ($array_modulos as $id_modulo => $detalle_modulo) {
            foreach ($detalle_modulo as $id_detalle => $valor_detalle) {
                $array_condicion_modulo[] = " (IDModulo = '" . $id_modulo . "' and IDDetalle = '" . $valor_detalle . "') ";
            }
        }

        if (count($array_condicion_modulo) > 0) {
            $condicion_notif = implode(" or ", $array_condicion_modulo);
        } else {
            $condicion_notif = " and IDModulo = 0 and IDSeccion = 0 ";
        }

        $condicion = " and  (" . $condicion_notif . ")";
        return $condicion;
    }

    public function set_codigo_qr($IDClub, $IDSocio, $CodigoQR)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($CodigoQR)) {

            //Por ahora guardo el qr en registro temporal para verificar que guarda
            $actualiza = "Update Socio Set ObservacionEspecial='" . $CodigoQR . "' Where IDSocio = '5533'";
            $dbo->query($actualiza);

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_header_club($IDClub)
    {
        $dbo = &SIMDB::get();
        $response = array();

        //Obtener clima
        $apiKey = "4fb76e57d4c8d4ea7aa61f8ce5cb4eaa";
        $cityId = "3652462";
        $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response_temp = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response_temp);
        $currentTime = time();

        $temperatura = (int) $data->main->temp_max;

        //Header Reserva, clima, bienvenida
        $header_club["IDClub"] = $IDClub;
        $header_club["Tipo"] = "Reserva";
        $header_club["TextoBienvenida"] = "Bienvenido al club";
        $header_club["Latitud"] = "0.097035";
        $header_club["Longitud"] = "78.493674";
        $header_club["Icono"] = "https://miclubapp.com/img/iconos/iconogolfheader.png";
        $header_club["TextoIcono"] = "QTGC Golf.";
        $header_club["LinkIcono"] = "https://www.feg.org.ec/";
        $header_club["Clima"] = $temperatura . "º";
        $header_club["IconoClima"] = "https://miclubapp.com/img/iconos/iconoclima.png";
        $header_club["FotoFondo"] = "https://miclubapp.com/img/iconos/fotoarbol.png";

        array_push($response, $header_club);

        //Header Noticia
        unset($header_club);
        $sql = "SELECT * FROM Noticia WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "'" . " ORDER BY FechaInicio DESC,Orden Limit 2";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $header_club["IDClub"] = $IDClub;
            $header_club["Tipo"] = "Noticia";
            $header_club["IDNoticia"] = $r["IDNoticia"];
            $header_club["IDSeccion"] = $r["IDSeccion"];
            $header_club["Titular"] = $r["Titular"];
            $header_club["Introduccion"] = $r["Introduccion"];
            if (!empty($r["NoticiaFile"])) :
                if (strstr(strtolower($r["NoticiaFile"]), "http://")) {
                    $foto1 = $r["NoticiaFile"];
                } else {
                    $foto1 = IMGNOTICIA_ROOT . $r["NoticiaFile"];
                }

            //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
            else :
                $foto1 = "";
            endif;
            $header_club["Imagen"] = $foto1;
            array_push($response, $header_club);
        }

        //Header Evento
        unset($header_club);
        $orden = " FechaEvento ASC";
        $sql = "SELECT * FROM Evento WHERE (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and FechaInicio <= NOW() and FechaFin >= NOW() " . $CondicionFechaEvento . " and IDClub = '" . $IDClub . "' " . $condiciones_noticia . " ORDER BY " . $orden . " Limit 2";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $header_club["IDClub"] = $r["IDClub"];
            $header_club["Tipo"] = "Evento";
            $header_club["IDEvento"] = $r["IDEvento"];
            $header_club["IDSeccion"] = $r["IDSeccionEvento"];
            $header_club["Titular"] = $r["FechaEvento"] . ": " . $r["Titular"];
            $header_club["Introduccion"] = $r["Introduccion"];

            if (!empty($r["EventoFile"])) :
                if (strstr(strtolower($r["EventoFile"]), "http://")) {
                    $foto1 = $r["EventoFile"];
                } else {
                    $foto1 = IMGEVENTO_ROOT . $r["EventoFile"];
                }

            //$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
            else :
                $foto1 = "";
            endif;
            $header_club["Imagen"] = $foto1;
        }

        if ($header_club["IDEvento"] > 0) {
            array_push($response, $header_club);
        }

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function

    public function get_datos_socio_ws($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $Accion = 01;

        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client(ENDPOINT_CONDADO, 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
					<ns1:Socio xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
					  <ns1:Request>
						<ns1:TypeSQL></ns1:TypeSQL>
						<ns1:DynamicProperty></ns1:DynamicProperty>
						<ns1:SessionID></ns1:SessionID>
						<ns1:Action></ns1:Action>
						<ns1:Body><![CDATA[<generatortoken><security user='ClubesWS' password='Zeus1234%' />    </generatortoken>]]>
					</ns1:Body>
					  </ns1:Request>
					</ns1:Socio>";

        $result = $client->call('Socio', $params, '', '');

        echo "1a";
        print_r($client);
        exit;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        //print_r( $result);
        //echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
        //echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];

        return $result;

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function

    public function set_estado_cuenta_ws($IDClub, $IDRegistro, $NumeroDocumento, $Accion, $Secuencia, $Concepto, $Valor, $Fecha, $Observaciones)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDRegistro) && !empty($NumeroDocumento) && !empty($Accion) && !empty($Secuencia) && !empty($Valor) && !empty($Fecha)) {
            $datos_estado = $dbo->fetchAll("SocioEstadoCuentaWS", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $NumeroDocumento . "' ", "array");
            if ($datos_estado["IDSocioEstadoCuentaWS"] > 0) {
                $sql_inserta = "INSERT INTO SocioEstadoCuentaWS (IDClub,IDRegistro,NumeroDocumento,Accion,Secuencia,Concepto,Valor,Fecha,Observaciones,UsuarioTrCr,FechaTrCr)
														VALUES('" . $IDClub . "','" . $IDRegistro . "','" . $NumeroDocumento . "','" . $Accion . "','" . $Secuencia . "','" . $Concepto . "','" . $Valor . "','" . $Fecha . "','" . $Observaciones . "','WS',NOW())";
                $dbo->query($sql_inserta);
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "EC2. El registro ya existe, por favor verifique";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "EC1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_borrar_estado_cuenta_ws($IDClub, $NumeroDocumento)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($NumeroDocumento)) {
            $datos_estado = $dbo->fetchAll("SocioEstadoCuentaWS", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $NumeroDocumento . "' ", "array");
            if ($datos_estado["IDSocioEstadoCuentaWS"] > 0) {
                $sql_borra = "DELETE FROM  SocioEstadoCuentaWS WHERE IDClub = '" . $IDClub . "' and  NumeroDocumento = '" . $NumeroDocumento . "'";
                $dbo->query($sql_borra);
                $respuesta["message"] = "Registro borrardo correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "EC3. El registro no existe, por favor verifique";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "EC1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_place_to_pay_transacciones($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $response = array();

        switch ($IDClub) {
            case "23": //Arrayanes es factura
                $TipoBusqueda = 'Factura';
                break;
            default:
                $TipoBusqueda = 'Servicio_Pago_Factura';
        }

        $sql = "SELECT *
				   FROM PeticionesPlacetoPay
				   WHERE IDSocio = '" . $IDSocio . "' and tipo = '" . $TipoBusqueda . "'
					 ORDER BY IDPeticionesPlacetoPay DESC
					 LIMIT 10";

        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($row_reg = $dbo->fetchArray($qry)) :

                switch ($row_reg["estado_transaccion"]) {
                    case "OK":
                    case "APPROVED":
                        $estadoTx = "APROBADA";
                        $color = "verde";
                        break;
                    case "REJECTED":
                        $estadoTx = "RECHAZADA";
                        $color = "rojo";
                        break;
                    case "PENDING":
                        $estadoTx = "PENDIENTE";
                        $color = "amarillo";
                        break;
                    case "APPROVED_PARTIAL":
                        $estadoTx = "APROBADO PARCIAL";
                        $color = "verde";
                        break;
                    case "PARTIAL_EXPIRED":
                        $estadoTx = "PARCIALMENTE EXPIRADO";
                        $color = "rojo";
                        break;
                    case "PENDING_VALIDATION":
                        $estadoTx = "PENDIENTE DE VALIDACION";
                        $color = "rojo";
                        break;
                    case "REFUNDED":
                        $estadoTx = "REINTEGRADO";
                        $color = "azul";
                        break;
                    default:
                        $estadoTx = $estado_transaccion;
                        $color = "rojo";
                }

                if ($row_reg["tipo"] == "EventoRegistro") {
                    $Descripcion = utf8_decode($dbo->getFields("Evento", "Titular", "IDEvento = '" . $row_reg["IDEvento"] . "'"));
                } elseif ($row_reg["tipo"] == "Deuda" || $row_reg["tipo"] == "Servicio_Pago_Factura") {
                    $Descripcion = "Pagos pendientes";
                } else {
                    $Descripcion = "Pagos";
                }

                $documentos_transacc = "";
                /*
                $array_documentos = json_decode( $row_reg["Documento"], true );
                if ( count( $array_documentos ) > 0 ){
                foreach ( $array_documentos as $detalle_documento ){
                $documentos_transacc.=$detalle_documento["NumeroDocumento"];
                }
                $mensajes_documentos=" Documentos: " . $documentos_transacc;
                }
                 */
                if (!empty($row_reg["Documento"])) {
                    $mensajes_documentos = " Documento: " . $row_reg["Documento"];
                }

                $transaccion["IDClub"] = $IDClub;
                $transaccion["IDSocio"] = $row_reg["IDSocio"];
                $transaccion["Referencia"] = $row_reg["referencia"] . $mensajes_documentos;
                $transaccion["Estado"] = $estadoTx;
                $transaccion["Descripcion"] = $Descripcion;
                $transaccion["Color"] = $color;
                $fecha_hora = substr($row_reg["fecha_peticion"], 0, 19);
                $fecha_hora = str_replace("T", " ", $fecha_hora);
                $transaccion["Fecha"] = $fecha_hora;
                $transaccion["Valor"] = $row_reg["valor"];
                array_push($response, $transaccion);
            endwhile;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No tienes historial de transacciones.";
            $respuesta["success"] = false;
            $respuesta["response"] = $response;
        } //end else
        return $respuesta;
    }

    public function set_factura_consumo($IDClub, $Accion, $Cedula, $TipoSocio, $NumeroDocumentoFactura, $Total, $Iva, $Servicio, $Estado, $IDFactura, $Detalle, $TextoRecibo, $SubTotal0, $SubTotal12, $RucAsociado = "")
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " NumeroDocumento = '" . $Cedula . "' and IDClub = '" . $IDClub . "' ", "array");
        if (!empty($datos_socio["IDSocio"])) {
            if (!empty($IDClub) && !empty($Accion) && !empty($Cedula) && !empty($TipoSocio) && !empty($Total) && !empty($Estado)) {

                //verifico si la factura ya existe para crear o editar
                $datos_factura = $dbo->fetchAll("FacturaConsumo", " NumeroDocumentoFactura = '" . $NumeroDocumentoFactura . "' and IDClub = '" . $IDClub . "' ", "array");

                if (empty($datos_factura["IDFacturaConsumo"])) {
                    $sql_factura = $dbo->query("INSERT INTO FacturaConsumo (IDClub, IDSocio, IDFactura, Detalle, TextoRecibo, Accion, Cedula, NumeroDocumentoFactura, TipoSocio, Total, Iva, SubTotal0, SubTotal12, RucAsociado, Servicio, Estado, UsuarioTrCr, FechaTrCr )
																						Values ('" . $IDClub . "','" . $datos_socio["IDSocio"] . "','" . $IDFactura . "','" . $Detalle . "','" . $TextoRecibo . "', '" . $Accion . "', '" . $Cedula . "','" . $NumeroDocumentoFactura . "'
																							,'" . $TipoSocio . "','" . $Total . "','" . $Iva . "','" . $SubTotal0 . "','" . $SubTotal12 . "','" . $RucAsociado . "','" . $Servicio . "','" . $Estado . "','WS',NOW())");

                    //Envio push con notificacion de factura
                    $Mensaje = "Se genero una nueva factura " . $NumeroDocumentoFactura . "Lo invitamos a diligenciar nuestra encuesta";
                    SIMUtil::enviar_notificacion_push_general($IDClub, $datos_socio["IDSocio"], $Mensaje, 58, "151");
                    $respuesta["message"] = "Factura registrada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {

                    if ($Estado == "Pagada" || $Estado == "Anulada") {
                        $sql_factura = $dbo->query("UPDATE  FacturaConsumo SET Estado = '" . $Estado . "', UsuarioTrEd = 'WS', FechaTrEd= NOW()
																							WHERE IDFacturaConsumo = '" . $datos_factura["IDFacturaConsumo"] . "' and Estado <> 'Pagado' ");
                    } else {
                        $sql_factura = $dbo->query("UPDATE  FacturaConsumo SET  Total = '" . $Total . "', $SubTotal12='" . $SubTotal12 . "', $SubTotal0 ='" . $SubTotal0 . "', Iva = '" . $Iva . "',
																							Servicio = '" . $Servicio . "', Estado = '" . $Estado . "', UsuarioTrEd = 'WS',IDfactura='" . $IDFactura . "', Detalle = '" . $Detalle . "', FechaTrEd= NOW()
																							WHERE IDFacturaConsumo = '" . $datos_factura["IDFacturaConsumo"] . "' and Estado <> 'Pagado' ");
                    }

                    $respuesta["message"] = "Factura modificada con exito.";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "FC1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "FC2. Atencion socio no existe";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_estado_factura_consumo($IDClub, $Accion, $Cedula, $NumeroDocumentoFactura)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM FacturaConsumo WHERE Accion = '" . $Accion . "' and  Cedula = '" . $Cedula . "'  and NumeroDocumentoFactura = '" . $NumeroDocumentoFactura . "' LIMIT 1";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $facturaconsumo["NumeroDocumentoFactura"] = $r["NumeroDocumentoFactura"];
                $facturaconsumo["Total"] = $r["Total"];
                $facturaconsumo["Estado"] = $r["Estado"];
                $facturaconsumo["NumeroAprobacion"] = $r["NumeroAprobacion"];
                $facturaconsumo["Tarjeta"] = $r["Tarjeta"];
                array_push($response, $facturaconsumo);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron facturas";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function curso_buscar($IDClub, $IDSocio, $IDSede, $IDCursoTipo, $IDCursoEntrenador)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        if ($datos_socio["IDCursoNivel"] == 0) {
            $respuesta["message"] = "Lo sentimos no tiene un nivel asignado, debe comunicarse con la sede";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDCursoTipo)) {
            $array_condicion_curso[] = "CH.IDCursoTipo = '" . $IDCursoTipo . "'";
        }

        if (!empty($IDCursoEntrenador)) {
            $array_condicion_curso[] = "CH.IDCursoEntrenador = '" . $IDCursoEntrenador . "'";
        }

        if (count($array_condicion_curso) > 0) {
            $condicion_curso = " and " . implode(" and ", $array_condicion_curso);
        }

        $sql_entrenador = "SELECT * FROM CursoEntrenador WHERE IDClub = '" . $IDClub . "'";
        $r_entrenador = $dbo->query($sql_entrenador);
        while ($row_entrenador = $dbo->fetchArray($r_entrenador)) {
            $array_entrenador[$row_entrenador["IDCursoEntrenador"]] = $row_entrenador["Nombre"];
        }

        $sql_nivel = "SELECT * FROM CursoNivel WHERE IDClub = '" . $IDClub . "'";
        $r_nivel = $dbo->query($sql_nivel);
        while ($row_nivel = $dbo->fetchArray($r_nivel)) {
            $array_nivel[$row_nivel["IDCursoNivel"]] = $row_nivel["Nombre"];
        }

        $sql_edad = "SELECT * FROM CursoEdad WHERE IDClub = '" . $IDClub . "'";
        $r_edad = $dbo->query($sql_edad);
        while ($row_edad = $dbo->fetchArray($r_edad)) {
            $array_edad[$row_edad["IDCursoEdad"]] = $row_edad["Nombre"];
        }

        $sql_sede = "SELECT * FROM CursoSede WHERE IDClub = '" . $IDClub . "'";
        $r_sede = $dbo->query($sql_sede);
        while ($row_sede = $dbo->fetchArray($r_sede)) {
            $array_sede[$row_sede["IDCursoSede"]] = $row_sede["Nombre"];
        }

        $sql_tipo = "SELECT * FROM CursoTipo WHERE IDClub = '" . $IDClub . "'";
        $r_tipo = $dbo->query($sql_tipo);
        while ($row_tipo = $dbo->fetchArray($r_tipo)) {
            $array_tipo[$row_tipo["IDCursoTipo"]] = $row_tipo["Nombre"];
        }

        if (date("m") == 1) {
            $mes_actual = 1;
            $year_actual = date("Y");
        } else {
            $mes_actual = date("m") - 1;
            $year_actual = date("Y");
        }

        $fecha_consulta = $year_actual . "-" . $mes_actual . "-01";

        $sql = "SELECT *
						FROM CursoHorario CH, CursoCalendario CC
						WHERE CH.IDCursoTipo=CC.IDCursoTipo
						AND CH.Publicar='S'
						AND CH.IDClub = '" . $IDClub . "'
						AND CH.IDCursoSede = '" . $IDSede . "'
						AND CH.IDCursoNivel = '" . $datos_socio["IDCursoNivel"] . "' " . $condicion_curso . "
						AND CC.FechaInicio >= '" . $fecha_consulta . "'
                        AND CH.Publicar= 'S' AND CC.Publicar = 'S'
						ORDER BY FechaInicio, HoraDesde";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                $curso["IDClub"] = $r["IDClub"];
                $curso["IDCursoHorario"] = $r["IDCursoHorario"];
                $curso["IDCursoEntrenador"] = $r["IDCursoEntrenador"];
                $curso["IDCursoCalendario"] = $r["IDCursoCalendario"];
                $curso["Entrenador"] = $array_entrenador[$r["IDCursoEntrenador"]];
                $curso["IDCursoNivel"] = $r["IDCursoNivel"];
                $curso["Nivel"] = $array_nivel[$r["IDCursoNivel"]];
                $curso["IDCursoEdad"] = $r["IDCursoEdad"];
                $curso["Edad"] = $array_edad[$r["IDCursoEdad"]];
                $curso["IDCursoSede"] = $r["IDCursoSede"];
                $curso["Sede"] = $array_sede[$r["IDCursoSede"]];
                $curso["IDCursoTipo"] = $r["IDCursoTipo"];
                $curso["Dia"] = $array_tipo[$r["IDCursoTipo"]];
                $curso["Nombre"] = $r["Nombre"];
                $curso["Descripcion"] = $r["Descripcion"];
                $curso["Cupo"] = $r["Cupo"];
                $curso["ValorMes"] = $r["ValorMes"];
                $curso["ValorTrimestre"] = $r["ValorTrimestre"];
                $curso["HoraDesde"] = $r["HoraDesde"];
                $curso["HoraHasta"] = $r["HoraHasta"];
                $curso["FechaInicio"] = $r["FechaInicio"];
                $curso["FechaFin"] = $r["FechaFin"];
                array_push($response, $curso);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron cursos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_curso_inscribir($IDClub, $IDSocio, $IDCursoHorario, $IDCursoCalendario, $IDUsuarioInscribe, $HoraDesde, $Cupos, $Valor, $referencia = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDCursoHorario) && !empty($IDCursoCalendario) && !empty($HoraDesde) && !empty($Valor)) {
            //Verifico que queden Cupos
            $inscritos = SIMWebServiceApp::get_curso_inscritos($IDClub, $IDCursoHorario, $IDCursoCalendario, $HoraDesde);
            if ($Cupos > 0 && $Cupos > $inscritos) {

                if (!empty($IDUsuarioInscribe)) {
                    $Estado = "Confirmado";
                    $CreadoPor = "Starter-" . $IDUsuarioInscribe;
                } else {
                    $Estado = "EsperaPago";
                    $CreadoPor = "Socio";
                }

                $sql_inscribe = "INSERT INTO CursoInscripcion (IDClub,IDSocio, IDCursoHorario, IDCursoCalendario, Referencia, HoraDesde, Valor, EstadoInscripcion, IDUsuarioInscribe,UsuarioTrCr,FechaTrCr )
															 VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDCursoHorario . "','" . $IDCursoCalendario . "','" . $referencia . "','" . $HoraDesde . "','" . $Valor . "','" . $Estado . "','" . $IDUsuarioInscribe . "','" . $CreadoPor . "',NOW())";
                $dbo->query($sql_inscribe);
                $id_inscripcion = $dbo->lastID();
                $respuesta["message"] = "Inscripcion realizada-. NUMERO: " . $id_inscripcion;
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "No hay cupos disponibles!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "C1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_curso_inscritos($IDClub, $IDCursoHorario, $IDCursoCalendario, $HoraDesde)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDCursoHorario) && !empty($IDCursoCalendario) && !empty($HoraDesde)) {
            $sql_inscrito = "SELECT COUNT(IDCursoInscripcion) as Total
															 FROM CursoInscripcion
															 WHERE  IDClub='" . $IDClub . "' and IDCursoHorario='" . $IDCursoHorario . "' and  IDCursoCalendario = '" . $IDCursoCalendario . "' and HoraDesde = '" . $HoraDesde . "' and (EstadoInscripcion = 'Confirmado' OR  EstadoInscripcion ='EsperaPago')";
            $r_inscrito = $dbo->query($sql_inscrito);
            $row_inscrito = $dbo->fetchArray($r_inscrito);
            $total_inscrito = $row_inscrito["Total"];
        } else {
            $total_inscrito = 0;
        }
        return $total_inscrito;
    }

    public function set_push_general($IDClub, $Accion, $Mensaje)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Accion) && !empty($Mensaje)) {
            $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Accion . "' and IDClub = '" . $IDClub . "' ", "array");
            SIMUtil::enviar_notificacion_push_general($IDClub, $datos_socio["IDSocio"], $Mensaje);
            $respuesta["message"] = "Enviado con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "PUSH1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function crear_pqr_ws($IDClub, $IDPqr)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr)) {
            $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_pqr["IDSocio"] . "' ", "array");
            $datos_tipo_pqr = $dbo->fetchAll("TipoPqr", " IDTipoPqr = '" . $datos_pqr["IDTipoPqr"] . "' ", "array");

            $URLHUB = URLHUB;
            $IDHUB = IDHUB;
            $ACCESSTOKENHUB = ACCESSTOKENHUB;
            $PASSWORDTOKENHUB = PASSWORDTOKENHUB;
            $curl = curl_init();
            $Prioridad = 1;

            if ($datos_tipo_pqr["TipoExterno"] == "REQ_FLR") {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $URLHUB,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_FLR\",
											\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",\n \"id_equipment\": \"" . $IDEquipo . "\",
											\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"title\": \"" . $datos_pqr["Asunto"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\",\n \"priority\": \"1\"\n}\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
            } elseif ($datos_tipo_pqr["TipoExterno"] == "REQ_HSK") {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $URLHUB,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_HSK\",
									\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",\n \"priority\": \"" . $Prioridad . "\",
									\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\"\n}\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
            } elseif ($datos_tipo_pqr["TipoExterno"] == "REQ_QTY") {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $URLHUB,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_QTY\",
									\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",
									\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\"\n}\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
            }

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
    }

    public function get_labels($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $datos_modulo = array();
        $datos_modulo = array();
        $sql_modulos = "SELECT IDClubLabelModulo, Nombre
										FROM ClubLabelModulo
										WHERE 1";
        $qry_modulo = $dbo->query($sql_modulos);
        if ($dbo->rows($qry_modulo) > 0) {
            while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                //$datos_modulo[ "Nombre" ] = $r_modulo[ "Nombre" ];
                //etiquetas
                $response_label = array();
                $array_label = array();

                $sql_label = "SELECT Etiqueta,Valor FROM ClubLabel WHERE IDClubLabelModulo = '" . $r_modulo["IDClubLabelModulo"] . "' and IDClub = '" . $IDClub . "'";
                $r_label = $dbo->query($sql_label);
                while ($row_label = $dbo->fetchArray($r_label)) {
                    $array_label[$row_label["Etiqueta"]] = $row_label["Valor"];
                }
                array_push($response_label, $array_label);
                $datos_modulo[$r_modulo["Nombre"]] = $response_label;
            }
        }


        $sqlParametros = "SELECT PermiteCargarExcelInvitado, LabelBotonEjemploFormato,LabelNombreInvitado,LabelNumeroIdentificacion,TextoBotonAgregar,HintFechaInvitado,TextoInvitadoAnterior,TextoMisInvitados FROM ParametroAcceso WHERE IDClub = $IDClub";
        $qryParametros = $dbo->query($sqlParametros);
        $Datos = $dbo->fetchArray($qryParametros);




        // Parametros campos modulo invitados
        $datos_modulo['Invitados'][0]["PermiteCargarExcel"] = $Datos["PermiteCargarExcelInvitado"];
        $datos_modulo['Invitados'][0]["LabelBotonEjemploFormato"] = $Datos["LabelBotonEjemploFormato"];
        $datos_modulo['Invitados'][0]["LabelNombreInvitado"] = $Datos["LabelNombreInvitado"];
        $datos_modulo['Invitados'][0]["LabelNumeroIdentificacion"] = $Datos["LabelNumeroIdentificacion"];
        $datos_modulo['Invitados'][0]["TextoMisInvitado"] = $Datos["TextoMisInvitados"];
        $datos_modulo['Invitados'][0]["TextoInvitadoAnterior"] = $Datos["TextoInvitadoAnterior"];
        $datos_modulo['Invitados'][0]["HintFechaInvitado"] = $Datos["HintFechaInvitado"];
        $datos_modulo['Invitados'][0]["TextoBotonAgregar"] = $Datos["TextoBotonAgregar"];
        // Fin Parametros campos modulo invitados

        array_push($response, $datos_modulo);
        $respuesta["message"] = "Encontrado";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function actualizar_pqr_ws($IDClub, $IDPqr, $Hub_Code, $Status)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr)) {
            $actualizacion_hub = "CODE:" . $IDPqr . " Hub_Code " . $Hub_Code . " Status " . $Status;
            $sql_pqr = "UPDATE Pqr SET NombreColaborador = '" . $actualizacion_hub . "' WHERE IDPqr='" . $IDPqr . "'";
            $dbo->query($sql_pqr);
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Actualizacionexitosa', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "PQW" . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_tipo_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("ReservaHotel", "IDReserva", "IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    if (empty($id_codigo)) {
                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($codigo_disponible != "S") {
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_actualiza_codigo);
                    }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update ReservaHotel Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
} //end class
