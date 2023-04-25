<?php
require_once LIBDIR . 'nusoap/lib/nusoap.php';
class SIMPasarelaPagos2
{
    /* .... ZONA PAGOS .... */

    public function zona_pagos_pagar($datos_club, $IDSocio, $Valor, $IDReserva, $Tipo)
    {

        $dbo = &SIMDB::get();

        $wsdl = "http://www.zonapagos.com/ws_inicio_pagov2/Zpagos.asmx?wsdl";
        $client = new nusoap_client($wsdl, 'wsdl');
        $err = $client->getError();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $sql_insert_pago = "INSERT INTO ZonaPagosPagos (IDClub, IDSocio,Valor,Fecha,IDReserva,Tipo) VALUES ('" . $datos_club["IDClub"] . "', '" . $IDSocio . "','" . $Valor . "',NOW(),'" . $IDReserva . "','" . $Tipo . "')";
        $dbo->query($sql_insert_pago);
        $lastid = $dbo->lastID();

        $params = array(
            'id_tienda' => $datos_club["IDTiendaZona"],
            'clave' => $datos_club["ClaveZona"],
            'total_con_iva' => $Valor,
            'valor_iva' => '0',
            'id_pago' => $lastid,
            'descripcion_pago' => 'Pago Hotel',
            'email' => $datos_socio["CorreoElectronico"],
            'id_cliente' => $IDSocio,
            'tipo_id' => '1',
            'nombre_cliente' => $datos_socio["Nombre"],
            'apellido_cliente' => $datos_socio["Apellido"],
            'telefono_cliente' => $datos_socio["Celular"],
            'info_opcional1' => '',
            'info_opcional2' => '',
            'info_opcional3' => '',
            'codigo_servicio_principal' => $datos_club["CodigoServicioZona"],
            'lista_codigos_servicio_multicredito' => null,
            'lista_nit_codigos_servicio_multicredito' => null,
            'lista_valores_con_iva' => null,
            'lista_valores_iva' => null,
            'total_codigos_servicio' => '0'
        );

        $client->setUseCurl('0');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;
        $result = $client->call('inicio_pagoV2', $params, '', '', false, true);

        if ($client->fault) {
            $array_ruta["Estado"] = "error";
            $array_ruta["Descripcion"] = $result["inicio_pagoV2Result"];
            $array_ruta["Ruta"] = "";
        } else {
            $err = $client->getError();
            if ($err) {
                $array_ruta["Estado"] = "error";
                $array_ruta["Descripcion"] = $err;
                $array_ruta["Ruta"] = "";
            } elseif (!is_numeric($result["inicio_pagoV2Result"])) {
                $array_ruta["Estado"] = "error";
                $array_ruta["Descripcion"] = $result["inicio_pagoV2Result"];
                $array_ruta["Ruta"] = "";
            } else {
                $ruta = 'https://www.zonapagos.com/' . $datos_club["CodigoRutaZona"] . '/pago.asp?estado_pago=iniciar_pago&identificador=' . $result['inicio_pagoV2Result'];
                $array_ruta["Estado"] = "ok";
                $array_ruta["Descripcion"] = "ok";
                $array_ruta["Ruta"] = $ruta;
                $identificadorTransaccion = $result['inicio_pagoV2Result'];
                $sql_actualiza_pago = "UPDATE ZonaPagosPagos SET IdentificadorTransaccion = '$identificadorTransaccion' WHERE IDZonaPagosPagos = '" . $lastid . "'";
                $dbo->query($sql_actualiza_pago);
            }
        }

        return $array_ruta;
    }

    public function zona_pagos_verificar_pago($IDPago)
    {
        $dbo = &SIMDB::get();
        $wsdl = "https://www.zonapagos.com/WsVerificarPagoV4/VerificarPagos.asmx?wsdl";
        $client = new nusoap_client($wsdl, 'wsdl');
        $err = $client->getError();

        $datos_pago = $dbo->fetchAll("ZonaPagosPagos", " IDZonaPagosPagos = '" . $IDPago . "' Limit 1", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pago["IDClub"] . "' ", "array");
        $params = array(
            'int_id_comercio' => $datos_club["IDTiendaZona"],
            'str_usr_comercio' => $datos_club["UsuarioTransaccionZona"],
            'str_pwd_Comercio' => $datos_club["ClaveTransaccionZona"],
            'str_id_pago' => $IDPago,
            'int_no_pago' => '-1',
            'int_error' => 0,
            'str_detalle' => 0,
            'str_res_pago' => 0,
            'int_cantidad_pagos' => 0,
        );
        $client->setUseCurl('0');
        //$client->soap_defencoding = 'UTF-8';
        //$client->decode_utf8 = false;
        $result = $client->call('verificar_pago_v4', $params, '', '', false, true);
        //print_r($params);
        //echo "<br><br>";
        //echo "verifica";
        //print_r($result);
        //exit;

        // Si es cero es que se encontraron pagos
        $array_respuesta = explode("|", $result["str_res_pago"]);

        print_r($array_respuesta);

        exit;

        $sql_actualiza_pago = "UPDATE ZonaPagosPagos
										SET EstadoPago = '" . trim($array_respuesta[1]) . "',
										ValorPagado= '" . trim($array_respuesta[2]) . "',
										ValorIvaPagado= '" . trim($array_respuesta[3]) . "',
										Descripcion= '" . trim($array_respuesta[4]) . "',
										IDCliente= '" . trim($array_respuesta[5]) . "',
										Nombre= '" . trim($array_respuesta[6]) . "',
										Apellido= '" . trim($array_respuesta[7]) . "',
										Telefono= '" . trim($array_respuesta[8]) . "',
										Email= '" . trim($array_respuesta[9]) . "',
										Campo1= '" . trim($array_respuesta[10]) . "',
										Campo2= '" . trim($array_respuesta[11]) . "',
										Campo3= '" . trim($array_respuesta[12]) . "',
										FechaTransaccion= '" . trim($array_respuesta[13]) . "',
										IDFormaPago= '" . trim($array_respuesta[14]) . "',
										TicketID= '" . trim($array_respuesta[15]) . "',
										CodigoServicio= '" . trim($array_respuesta[16]) . "',
										CodigoBanco= '" . trim($array_respuesta[17]) . "',
										NombreBanco= '" . trim($array_respuesta[18]) . "',
										CodigoTransaccion= '" . trim($array_respuesta[19]) . "',
										CicloTransaccion= '" . trim($array_respuesta[20]) . "'
										WHERE IDZonaPagosPagos = '" . $IDPago . "'";
        $dbo->query($sql_actualiza_pago);

        if (trim($array_respuesta[1]) == "1") {
            //actualizo la reserva como pagada
            if ($datos_pago["Tipo"] == "ReservaHotel") {

                $actualizaReserva = "UPDATE ReservaHotel
											SET Estado = 'enfirme', Pagado = 'S', PagoPayu = 'S', MedioPago = '" . $array_respuesta[14] . "',
											FechaTransaccion = '" . $array_respuesta[13] . "', Codigorespuesta = '" . $array_respuesta[19] . "',
											EstadoTransaccion = 'A'
											WHERE IDReserva ='" . $datos_pago["IDReserva"] . "' ";
                $dbo->query($actualizaReserva);
            }
        }
        ///para pagos pendientes la marco con estado pendiente para no eliminar la reserva
        elseif (trim($array_respuesta[1]) == "999") {
            $actualizaReserva = "UPDATE ReservaHotel
												SET EstadoTransaccion = 'Pendiente'
												WHERE IDReserva ='" . $datos_pago["IDReserva"] . "' ";
            $dbo->query($actualizaReserva);
        }

        return $result;
    }

    public function zona_pagos_pagarv2($datos_club, $datos_pago, $Tipo)
    {

        $dbo = &SIMDB::get();

        $IDSocio = $datos_pago["IDSocio"];
        $IDClub = $datos_pago["extra2"];
        $Valor = $datos_pago["valor"];
        $IDReserva = $datos_pago["extra1"];

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        if (empty($datos_socio["Apellido"])) {
            $nombre = explode(" ", $datos_socio["Nombre"]);
            if (isset($nombre[3])) {
                $datos_socio["Apellido"] = $nombre[2] . " " . $nombre[3];
            } else {
                $datos_socio["Apellido"] = $nombre[1] . " " . $nombre[2];
            }
        }

        $sql_insert_pago = "INSERT INTO ZonaPagosPagos (IDClub, IDSocio, Valor, Fecha, IDReserva, Tipo) VALUES ('" . $IDClub . "', '" . $IDSocio . "','" . $Valor . "',NOW(),'" . $IDReserva . "','" . $Tipo . "')";
        $dbo->query($sql_insert_pago);
        $lastid = $dbo->lastID();

        if ($datos_pago["Tipo"] == "ReservaGeneral") {
            $actualizaReserva = "	UPDATE ReservaGeneral
									SET IDEstadoReserva = '3'
									WHERE IDReservaGeneral ='" . $IDReserva . "' ";

            //$dbo->query($actualizaReserva);
        }

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://www.zonapagos.com/Apis_CicloPago/api/InicioPago?",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>
                "{
						\r\n  \"InformacionPago\":
							{
								\r\n    \"flt_total_con_iva\":" . $datos_pago["valor"] . ",
								\r\n    \"flt_valor_iva\": " . $datos_pago["iva"] . ",
								\r\n    \"str_id_pago\": \"" . $lastid . "\",
								\r\n    \"str_descripcion_pago\": \"" . $datos_pago["descripcion"] . "\",
								\r\n    \"str_email\": \"" . $datos_pago["emailComprador"] . "\",
								\r\n    \"str_id_cliente\": \"" . $datos_socio["NumeroDocumento"] . "\",
								\r\n    \"str_tipo_id\": \"1\",
								\r\n    \"str_nombre_cliente\": \"" . $datos_socio["Nombre"] . "\",
								\r\n    \"str_apellido_cliente\": \"" . $datos_socio["Apellido"] . "\",
								\r\n    \"str_telefono_cliente\": \"" . $datos_socio["Celular"] . "\",
								\r\n    \"str_opcional1\": \"\",
								\r\n    \"str_opcional2\": \"\",
								\r\n    \"str_opcional3\": \"\",
								\r\n    \"str_opcional4\": \"\",
								\r\n    \"str_opcional5\": \"\"\r\n
							},

						\r\n  \"InformacionSeguridad\":
							{
								\r\n    \"int_id_comercio\":" . $datos_club["IDTiendaZona"] . ",
								\r\n    \"str_usuario\": \"" . $datos_club["UsuarioTransaccionZona"] . "\",
								\r\n    \"str_clave\": \"" . $datos_club["ClaveZona"] . "\",
								\r\n    \"int_modalidad\":1\r\n
							},

						\r\n  \"AdicionalesPago\":
							[
								\r\n    {
											\r\n      \"int_codigo\": 111,
											\r\n      \"str_valor\": \"1\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 112,
											\r\n      \"str_valor\": \"0\"
											\r\n
										}\r\n
							],

						\r\n \"AdicionalesConfiguracion\":
							[
								\r\n    {
											\r\n      \"int_codigo\": 50,
											\r\n      \"str_valor\": \"" . $datos_club["CodigoServicioZona"] . "\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 100,
											\r\n      \"str_valor\": \"0\" /*Varios medios de pago*/
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 101,
											\r\n      \"str_valor\": \"0\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 102,
											\r\n      \"str_valor\": \"0\" /*+ de 1 pago por PSE*/
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 103,
											\r\n      \"str_valor\": \"0\" /*+ de 1 pago con varias TC*/
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 104,
											\r\n      \"str_valor\": \"https://www.miclubapp.com/respuesta_transaccion_zonav2.php?id_pago=" . $lastid . "\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 105,
											\r\n      \"str_valor\": \"10000\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 106,
											\r\n      \"str_valor\": \"3\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 107,
											\r\n      \"str_valor\": \"0\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 108,
											\r\n      \"str_valor\": \"1\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 109,
											\r\n      \"str_valor\": \"0\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 110,
											\r\n      \"str_valor\": \"0\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 113,
											\r\n      \"str_valor\": \"0\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 114,
											\r\n      \"str_valor\": \"96\"
								\r\n    },
								\r\n    {
											\r\n      \"int_codigo\": 115,
											\r\n      \"str_valor\": \"1\"
								\r\n    }\r\n
							]\r\n
                }\r\n",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = json_decode($response, true);

        if ($DATOS["int_codigo"] != 1) {
            $array_ruta["Estado"] = "Error";
            $array_ruta["Descripcion"] = $DATOS["str_descripcion_error"];
            $array_ruta["Ruta"] = "";
        } else {
            $ruta = $DATOS["str_url"];

            $array_ruta["Estado"] = "ok";
            $array_ruta["Descripcion"] = "ok";
            $array_ruta["Ruta"] = $ruta;

            $identificadorTransaccion = $datos_pago["refVenta"];

            $sql_actualiza_pago = "UPDATE ZonaPagosPagos SET IdentificadorTransaccion = '$identificadorTransaccion' WHERE IDZonaPagosPagos = '" . $lastid . "'";
            $dbo->query($sql_actualiza_pago);
        }

        return $array_ruta;
    }

    public function zona_pagos_verificar_pagov2($IDPago)
    {

        $dbo = &SIMDB::get();

        $datos_pago = $dbo->fetchAll("ZonaPagosPagos", " IDZonaPagosPagos = '" . $IDPago . "' Limit 1", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pago["IDClub"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_pago["IDSocio"] . "' ", "array");

        if ($datos_pago["Tipo"] == "ReservaGeneral") {
            $actualizaReserva = "	UPDATE 	ReservaGeneral
									SET 	IDTipoPago = '1', Pagado = 'N', PagoPayu = 'N', MedioPago = 'Pendiente de confirmacion',
											FechaTransaccion = '" . $datos_pago["Fecha"] . "', CodigoRespuesta = 'Pendiente de confirmacion',
											EstadoTransaccion = 'P'
									WHERE	IDReservaGeneral ='" . $datos_pago["IDReserva"] . "'";

            $dbo->query($actualizaReserva);
        }

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://www.zonapagos.com/Apis_CicloPago/api/VerificacionPago",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>
                "{
						\r\n    \"int_id_comercio\": " . $datos_club["IDTiendaZona"] . ",
						\r\n    \"str_usr_comercio\": \"" . $datos_club["UsuarioTransaccionZona"] . "\",
						\r\n    \"str_pwd_Comercio\": \"" . $datos_club["ClaveZona"] . "\",
						\r\n    \"str_id_pago\": \"" . $IDPago . "\",
						\r\n    \"int_no_pago\":-1\r\n
					}",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = json_decode($response, true);

        // Si es cero es que se encontraron pagos
        $array_respuesta = explode(" | ", $DATOS["str_res_pago"]);

        $sql_actualiza_pago = "	UPDATE 	ZonaPagosPagos
									SET 	EstadoPago = '" . trim($array_respuesta[4]) . "',
											ValorPagado = '" . trim($array_respuesta[5]) . "',
											ValorIvaPagado = '" . trim($array_respuesta[7]) . "',
											Descripcion = '" . trim($array_respuesta[8]) . "',
											IDCliente = '" . trim($array_respuesta[9]) . "',
											Nombre = '" . trim($array_respuesta[10]) . "',
											Apellido = '" . trim($array_respuesta[11]) . "',
											Telefono = '" . trim($array_respuesta[12]) . "',
											Email = '" . trim($array_respuesta[13]) . "',
											Campo1 = '" . trim($array_respuesta[14]) . "',
											Campo2 = '" . trim($array_respuesta[15]) . "',
											Campo3 = '" . trim($array_respuesta[16]) . "',
											FechaTransaccion = '" . trim($array_respuesta[19]) . "',
											IDFormaPago = '" . trim($array_respuesta[20]) . "',
											TicketID = '" . trim($array_respuesta[21]) . "',
											CodigoServicio = '" . trim($array_respuesta[22]) . "',
											CodigoBanco = '" . trim($array_respuesta[23]) . "',
											NombreBanco = '" . trim($array_respuesta[24]) . "',
											CodigoTransaccion = '" . trim($array_respuesta[25]) . "',
											FechaActulizaPago= NOW(),
											CicloTransaccion = '" . trim($array_respuesta[26]) . "'
									WHERE 	IDZonaPagosPagos = '" . $IDPago . "'";

        $dbo->query($sql_actualiza_pago);

        if (trim($array_respuesta[4]) == "1" || trim($array_respuesta[4]) == "999" || trim($array_respuesta[4]) == "4001" || trim($array_respuesta[4]) == "888") {
            //actualizo la reserva como pagada
            if ($datos_pago["Tipo"] == "ReservaGeneral") {
                $actualizaReserva = "	UPDATE 	ReservaGeneral
											SET 	IDEstadoReserva = '1', Pagado = 'S', PagoPayu = 'S', MedioPago = '" . $array_respuesta[20] . "',
													FechaTransaccion = '" . $array_respuesta[19] . "', CodigoRespuesta = '" . $array_respuesta[25] . "',
													EstadoTransaccion = 'A'
											WHERE	IDReservaGeneral ='" . $datos_pago["IDReserva"] . "'";

                $dbo->query($actualizaReserva);
            } elseif ($datos_pago["Tipo"] == "Consumos") {

                $IDClubConsulta = $datos_pago["IDClub"];
                $IDSocioConsulta = $datos_pago["IDSocio"];
                $Valor = $datos_pago["Valor"];

                $sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocioConsulta . "','PAGO_EXITOSO_EXTRACTO_ZONAPAGOS','" . json_encode($datos_pago) . "','" . json_encode($DATOS) . "')");

                SIMUtil::notifica_pago_extracto($IDClubConsulta, $IDSocioConsulta, $Valor);
            }
        } elseif (trim($array_respuesta[4]) == "1000" || trim($array_respuesta[4]) == "1001" || trim($array_respuesta[4]) == "1002" || trim($array_respuesta[4]) == "4000" || trim($array_respuesta[4]) == "4003") {
            //actualizo la reserva como anulada
            if ($datos_pago["Tipo"] == "ReservaGeneral") {
                $actualizaReserva = "	UPDATE 	ReservaGeneral
											SET 	IDEstadoReserva = '2', Pagado = 'N', PagoPayu = 'N', MedioPago = '" . $array_respuesta[20] . "',
													FechaTransaccion = '" . $array_respuesta[19] . "', CodigoRespuesta = '" . $array_respuesta[25] . "',
													EstadoTransaccion = 'R'
											WHERE 	IDReservaGeneral ='" . $datos_pago["IDReserva"] . "'";

                $dbo->query($actualizaReserva);
            } elseif ($datos_pago["Tipo"] == "Consumos") {
                $sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocioConsulta . "','PAGO_RECHAZADO_EXTRACTO_ZONAPAGOS','" . json_encode($datos_pago) . "','" . json_encode($DATOS) . "')");
            }
        }

        $estadoAntiguo = $datos_pago["EstadoPago"];
        $estadoNuevo = $array_respuesta[4];

        if ($estadoNuevo != $estadoAntiguo) {
            switch ($estadoNuevo) {
                case "1":
                    $estadoNuevo = "APROBADA";
                    break;

                case "1002":
                case "1001":
                case "1000":
                case "4003":
                case "4000":
                    $estadoNuevo = "RECHAZADA";
                    break;

                case "4001":
                case "999":
                case "888":
                    $estadoNuevo = "PENDIENTE";
                    break;

                default:
                    $estadoNuevo = "OTRO";
            }

            switch ($estadoAntiguo) {
                case "1":
                    $estadoAntiguo = "APROBADA";
                    break;

                case "1002":
                case "1001":
                case "1000":
                case "4003":
                case "4000":
                    $estadoAntiguo = "RECHAZADA";
                    break;

                case "4001":
                case "999":
                case "888":
                    $estadoAntiguo = "PENDIENTE";
                    break;

                default:
                    $estadoAntiguo = "OTRO";
            }

            $Mensaje = "El estado de su transacción ha cambiado de: " . $estadoAntiguo . " a estado: " . $estadoNuevo;
            $IDModulo = 44;

            SIMUtil::enviar_notificacion_push_general($datos_socio["IDClub"], $datos_socio["IDSocio"], $Mensaje, $IDModulo, "");
        }

        /* $users = array(array("id" => $datos_socio["IDSocio"],
        "idclub" => $datos_socio["IDClub"],
        "registration_key" => $datos_socio["Token"],
        "deviceType" => $datos_socio["Dispositivo"]),
        );

        $custom["tipo"] = "app";
        $custom["idmodulo"] = (string) "44";
        $custom["titulo"] = "Estado Trasacción";
        $custom["idseccion"] = "0";
        $custom["iddetalle"] = "0";

        if ($datos_socio["Dispositivo"] == "iOS") {
        $array_ios[] = $datos_socio["Token"];
        } elseif ($datos_socio["Dispositivo"] == "Android") {
        $array_android[] = $datos_socio["Token"];
        }

        SIMUtil::sendAlerts_V2($users, $message, $custom, "", $array_android, $array_ios, $datos_socio["IDClub"]); */

        return $DATOS;
    }

    /* .... FIN ZONA PAGOS .... */

    /* .... PAYPHONE .... */

    public function payphone_regiones($IDClub)
    {

        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pay.payphonetodoesposible.com/api/Regions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer " . $datos_club["TokenPayPhone"],
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: ARRAffinity=1a2b58b1b5dd58e9a8cded4d1e806e90e62523443adcee8a080db4d3e33171a9",
                "Host: pay.payphonetodoesposible.com",
                "Postman-Token: 7b54cf85-13fb-4022-beed-4e38d60ebae0,13db1eb6-19f6-4263-b560-07b6f0817451",
                "User-Agent: PostmanRuntime/7.20.1",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

        return $result;
    }

    public function payphone_sonda()
    {

        $dbo = &SIMDB::get();

        $sql_transacciones = "SELECT * FROM PagosWebPayPhone Where Estado = 'Proceso'";
        $r_transacciones = $dbo->query($sql_transacciones);
        while ($row_transaccion = $dbo->fetchArray($r_transacciones)) {

            switch ($row_transaccion["Tipo"]) {
                case 'FacturaConsumo':
                    $datos_factura = $dbo->fetchAll("FacturaConsumo", " IDFacturaConsumo = '" . $row_transaccion["IDPadre"] . "' ", "array");
                    $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_factura["IDClub"] . "' ", "array");

                    break;
                default:
                    break;
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://pay.payphonetodoesposible.com/api/Sale/" . $row_transaccion["IDTransaccionPayPhone"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $datos_club["TokenPayPhone"],
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                //echo "<br>" . $response;
                $array_respuesta = json_decode($response);

                switch ($array_respuesta->statusCode) {
                    case '1':
                        $estado = "Proceso";
                        break;
                    case '2': // Rechazada por el cliente
                        $estado = "Rechazada";
                        break;
                    case '3': // Pagada
                        $estado = "Pagada";
                        //actualizo la factura como pagada
                        $actualiza_factura = "UPDATE FacturaConsumo SET Estado = 'Pagada', NumeroAprobacion = '" . $array_respuesta->authorizationCode . "', Tarjeta='" . $array_respuesta->cardType . "', MedioPago = 'PayPhone' WHERE IDFacturaConsumo = '" . $array_respuesta->optionalParameter2 . "'";
                        $dbo->query($actualiza_factura);
                        break;
                    default:
                        $estado = "Proceso";
                        break;
                }

                $actualiza_log = "UPDATE PagosWebPayPhone
													SET Estado = '" . $estado . "', cardType='" . $array_respuesta->cardType . "',bin='" . $array_respuesta->bin . "',
																lastDigits='" . $array_respuesta->lastDigits . "',deferredCode='" . $array_respuesta->deferredCode . "',
																deferred='" . $array_respuesta->deferred . "', cardBrand='" . $array_respuesta->cardBrand . "',
																amount='" . $array_respuesta->amount . "', statusCode='" . $array_respuesta->statusCode . "',
																transactionStatus='" . $array_respuesta->transactionStatus . "', message='" . $array_respuesta->message . "',
																messageCode='" . $array_respuesta->messageCode . "'
														WHERE IDTransaccionPayPhone = '" . $row_transaccion["IDTransaccionPayPhone"] . "'";

                $dbo->query($actualiza_log);
            }
        }
    }

    /* .... FIN PAYPHONE .... */

    /* .... PLACETOPAY .... */

    public function getRealIpAddr()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function transacciones_pendientes_place_to_pay($IDSocio, $link_continuar, $ReferenciaPago)
    {
        $dbo = &SIMDB::get();

        $array_transaccion = array();
        //Actualizar los pagos de place to pay
        $sql_transacciones = "SELECT * FROM PeticionesPlacetoPay Where IDSocio = '" . $IDSocio . "' and estado_transaccion = 'PENDING' and referencia <> '" . $ReferenciaPago . "'";
        $r_transacciones = $dbo->query($sql_transacciones);
        $filas = $dbo->rows($r_transacciones);
        if ($filas > 0) {
            $mensaje =
                "
				<style>
					table {
					width: 100%;
					border: 1px solid #999;
					text-align: left;
					border-collapse: collapse;
					margin: 0 0 1em 0;
					caption-side: top;
					}
					caption, td, th {
					padding: 0.3em;
					}
					th {
					border-bottom: 1px solid #999;
					width: 25%;
						background: #f4faa9;
					}
					td {
					border-bottom: 1px solid #999;
					width: 25%;
						background: #FFF;
					}
					caption {
					font-weight: bold;
					font-style: italic;
					}
					.enlaceboton {
						PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-WEIGHT: bold; FONT-SIZE: 10pt; PADDING-BOTTOM: 4px; COLOR: #666666; PADDING-TOP: 4px; FONT-FAMILY: verdana, arial, sans-serif; BACKGROUND-COLOR: #ffffcc; TEXT-DECORATION: none
					}
					.enlaceboton:link {
						BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
					}
					.enlaceboton:visited {
						BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
					}
					.enlaceboton:hover {
						BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #666666 2px solid; BORDER-LEFT: #666666 2px solid; BORDER-BOTTOM: #cccccc 1px solid
					}
				</style>";

            $mensaje .= "<br>Tienes las siguientes transacciones pendientes<br><br>";
            $mensaje .= "<table class='table' border=1><tr><th align='center'>Referencia</th><th align='center'>Fecha</th></tr>";

            while ($row_transaccion = $dbo->fetchArray($r_transacciones)) {
                $fecha_hora = substr($row_transaccion["fecha_peticion"], 0, 19);
                $fecha_hora = str_replace("T", " ", $fecha_hora);
                $mensaje .= "<tr><td>" . $row_transaccion["referencia"] . "</td><td>" . $fecha_hora . "</td></tr>";
            }
            $mensaje .= "</table>";

            $mensaje .= "<br><br> <input type = 'submit' class=enlaceboton value= 'Clic aqui si desas continuar de todas formas.' >";

            /* $mensaje.= "<br><br><a class=enlaceboton   href='".$link_continuar."' >Clic aqui si desas continuar de todas formas.</a>"; */

            echo $mensaje;
            exit;
        }
    }

    public function sonda_place_to_pay($IDSocio = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) {
            $condicion_socio = " and IDSocio = '" . $IDSocio . "' ";
        }

        //Actualizar los pagos de place to pay
        $sql_transacciones = "SELECT * FROM PeticionesPlacetoPay Where (estado_transaccion = 'PENDING' or estado_transaccion = '' or estado_transaccion = 'OK') or (estado_transaccion = 'APPROVED' and EnviadaWS <> 'S' and tipo = 'Deuda' ) " . $condicion_socio;
        $r_transacciones = $dbo->query($sql_transacciones);
        while ($row_transaccion = $dbo->fetchArray($r_transacciones)) :
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $row_transaccion["IDClub"] . "' ", "array");

            $login = trim($datos_club["LoginPlaceToPay"]);
            $secretKey = trim($datos_club["SecretKeyPlaceToPay"]);

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

            if ($row_transaccion["IDClub"] == 51) {

                if ($datos_club["IsTest"] == 1) {
                    $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
                } else {
                    $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
                }
            } else {
                if ($datos_club["IsTestPlaceToPay"] == 1) {
                    $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
                } else {
                    $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
                }
            }

            $ch = curl_init($url_place_to_pay . '/api/session/' . $row_transaccion["request_id"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = ['Content-Type:application/json; charset=utf-8'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth));
            $response = curl_exec($ch);
            curl_close($ch);
            // do anything you want with your response
            $respuesta = json_decode($response, true);

            /* var_dump($respuesta); */

            //actualizo la transaccion
            $Lote = $respuesta["payment"][0]["processorFields"][5]["value"];
            $Linea = $valor_linea = $respuesta["payment"][0]["processorFields"][6]["value"];
            $Voucher = $valor_linea = $respuesta["payment"][0]["receipt"];
            $update_transacc = "UPDATE PeticionesPlacetoPay
										SET estado_transaccion = '" . $respuesta["status"]["status"] . "',
										Lote = '" . $Lote . "', Linea = '" . $Linea . "', Voucher = '" . $Voucher . "'
										WHERE IDPeticionesPlacetoPay = '" . $row_transaccion["IDPeticionesPlacetoPay"] . "'";
            $dbo->query($update_transacc);

            switch ($respuesta["status"]["status"]) {
                case "OK":
                case "APPROVED":
                    $estadoTx = "APROBADA";

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';
                    break;
                case "REJECTED":
                    $estadoTx = "RECHAZADA";

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';

                    break;
                case "PENDING":
                    $estadoTx = "PENDIENTE";

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';

                    break;
                case "APPROVED_PARTIAL":
                    $estadoTx = "APROBADO PARCIAL";

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';
                    break;
                case "PARTIAL_EXPIRED":
                    $estadoTx = "PARCIALMENTE EXPIRADO";

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';
                    break;
                case "PENDING_VALIDATION":
                    $estadoTx = "PENDIENTE DE VALIDACION";

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';
                    break;
                case "REFUNDED":
                    $estadoTx = "REINTEGRADO";

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';
                    break;
                default:
                    $estadoTx = $estado_transaccion;
            }

            switch ($row_transaccion["tipo"]) {
                case "Factura":
                    if ($respuesta["status"]["status"] == "APPROVED") {
                        $update_transacc = "UPDATE FacturaConsumo
																	SET Estado = 'Pagada',
																	NumeroAprobacion = '" . $Voucher . "', Tarjeta = '" . $respuesta["payment"]["0"]["issuerName"] . "'
																	WHERE IDFacturaConsumo = '" . $row_transaccion["IDMaestro"] . "'";
                        $dbo->query($update_transacc);
                    }
                    break;

                case "Domicilios":

                    $version = $row_transaccion[Linea];

                    if ($version == 0) {
                        $version = "";
                    }

                    $query = "UPDATE Domicilio " . $version . "
                    SET EstadoTransaccion = '" . $estado . "',
                        FechaTransaccion = NOW(),
                        CodigoRespuesta = '" . $row_transaccion["reference"] . "',
                        MedioPago = 'Placetopay',
                        TipoMedioPago = '1', Pagago = '$pagado', PagoPayu = '$pagado'
                    WHERE IDDomicilio = '" . $row_transaccion["IDReserva"] . "'";

                    $sql_actualizar = $dbo->query($query);
                    break;

                case "ReservaGeneral":

                    $actualizaReserva = "	UPDATE ReservaGeneral
                                SET IDTipoPago = '1', IDEstadoReserva = '" . $estadoReserva . "', Pagado = '" . $pagado . "', PagoPayu = '" . $pagado . "', MedioPago = 'Place To Pay',
                                FechaTransaccion = NOW(), CodigoRespuesta = '" . $respuesta["request"]["payment"]["reference"] . "',
                                EstadoTransaccion = '" . $estado . "'
                                WHERE IDReservaGeneral ='" . $row_transaccion["IDReserva"] . "' ";
                    $dbo->query($actualizaReserva);

                    break;
            }

        endwhile;
        //echo "Terminado.";
    }

    public function PlaceToPay($DATOS)
    {
        if (empty($DATOS["descripcion"])) {
            $DATOS["descripcion"] = "Pago Mi Club";
        }

        // print_r($DATOS);
        // exit;

        $dbo = &SIMDB::get();

        //obtención de nonce
        if (function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }

        $nonceBase64 = base64_encode($nonce);

        $IDClub = $DATOS["extra2"];

        if ($IDClub == 93) :
            if ($DATOS[Modulo] == 'Reservas') :
                $IDServicio = $dbo->getFields("ReservaGeneral", "IDServicio", "IDReservaGeneral = $DATOS[extra1]");

                $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = $IDServicio");
                $NombreServicio = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = $IDServicioMaestro AND IDClub = $IDClub");
                if (empty($NombreServicio))
                    $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $IDServicioMaestro");

                $DATOS["descripcion"] = "Pago Reserva $NombreServicio";
            elseif ($DATOS[Modulo] == 'Evento') :

                $version = substr($DATOS[url_respuesta], "-1");
                if ($version == "=") {
                    $version = "";
                }

                $IDEvento = $dbo->getFields("EventoRegistro$version", "IDEvento$version", "IDEventoRegistro = $DATOS[extra1]");
                $NombreEvento = $dbo->getFields("Evento$version", "Titular", "Evento$version = $IDEvento");

                $DATOS["descripcion"] = "Pago evento $NombreEvento";

            endif;

        endif;

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $login = trim($datos_club["LoginPlaceToPay"]);
        $secretKey = trim($datos_club["SecretKeyPlaceToPay"]);

        $valor = number_format((float)$DATOS['valor'], 2, ".", "");

        $seed = date('c');
        $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

        $expiracion = strtotime('+20 minute', strtotime($seed)); //sumar al menos 15 minutos
        $expiracion = date('c', $expiracion);

        $auth = array(
            "auth" => array(
                "login" => $login,
                "seed" => $seed,
                "nonce" => $nonceBase64,
                "tranKey" => $tranKey,
            ),
        );

        $ice = array(
            "kind" => "ice",
            "amount" => $valor,
            "base" => null,
        );

        $taxes = array($ice);

        $DATOS['iva'] = "si";
        $valor_iva_base = (float)$DATOS['valor'] / 1.12;
        $valor_iva = (float)$DATOS['valor'] - $valor_iva_base;
        $valor_iva = number_format($valor_iva, 2, ".", "");

        if ($DATOS['iva'] == "si") {

            $iva = array(
                "kind" => "valueAddedTax",
                "amount" => number_format($valor_iva, 2, ".", ""),
                "base" => number_format($valor_iva_base, 2, ".", ""),
            );
            $taxes = array($ice, $iva);
        }

        if (isset($taxes)) {

            $amount = array(
                "taxes" => $taxes,
                "currency" => "USD",
                "total" => (float)$valor,
            );
        } else {
            $amount = array(
                "currency" => "USD",
                "total" => (float)$valor,
            );
        }

        // $datos_reg = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $DATOS["extra1"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $DATOS["IDSocio"] . "' ", "array");

        $nombre_usuario = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];

        /* if (!filter_var($DATOS['emailComprador'], FILTER_VALIDATE_EMAIL)) {
            echo "<h2>CORREO INVALIDO DEBE TENER CONFIGURADO UN CORREO VALIDO</h2>";
            exit;
        }

        if (strlen($datos_socio["NumeroDocumento"]) != 10) {
            echo "<h2>NUMERO DE DOCUMENTO INVALIDO, EL NUMERO DE DOCUMENTO DEBE TENER 10 CARACTERES EXACTOS</h2>";
            exit;
        }

        if (strlen($datos_socio["Celular"]) < 8) {
            echo "<h2>CELULAR INVALIDO, DEBE TENER MENOS DE 8 DIGITOS EL NUMERO DE CELULAR</h2>";
            exit;
        } */

        $data = array(
            "locale" => "es_US",
            "buyer" => array(
                "name" => $datos_socio["Nombre"],
                "surname" => $datos_socio["Apellido"],
                "email" => $DATOS['emailComprador'],
                "document" => $datos_socio["NumeroDocumento"],
                "documentType" => "CI",
                "mobile" => $datos_socio["Celular"],
            ),
            "payment" => array(
                "reference" => $DATOS["ref"], //editar
                "description" => $DATOS['descripcion'],
                "amount" => $amount,
            ),
            "expiration" => $expiracion,
            "ipAddress" => self::getRealIpAddr(),
            "returnUrl" => URLROOT . "respuesta_transaccion_ptp2.php?reference=" . $DATOS["ref"],
            "userAgent" => $_SERVER['HTTP_USER_AGENT'],
            "paymentMethod" => "",
        );

        if (isset($DATOS['pagador'])) {
            $pagador = array(
                "payer" => array(
                    "name" => $datos_socio["Nombre"],
                    "surname" => $datos_socio["Apellido"],
                    "email" => $DATOS['emailComprador'],
                    "document" => $datos_socio["NumeroDocumento"],
                    "documentType" => "CI",
                    "mobile" => $datos_socio["Celular"],
                )
            );
            $data = array_merge($data, $pagador);
        }

        $data = array_merge($data, $auth);

        if ($datos_club["IsTestPlaceToPay"] == 1) {
            $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
        } else {
            $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
        }



        $pagina_consulta = $url_place_to_pay . '/api/session';
        $headers = ['Content-Type:application/json; charset=utf-8'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $pagina_consulta,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        $DATA = json_decode($response, true);

        curl_close($curl);

        if ($DATA['status']['status'] == "FAILED") {

            $respuesta["message"] = "PASARELA FALLIDA";
            $respuesta["success"] = false;
            $respuesta["response"] = $DATA;
        } else {

            switch ($DATOS[Modulo]) {
                case "Domicilio":
                    $version = substr($DATOS[url_respuesta], "-1");
                    if ($version == "=") {
                        $version = "";
                    }

                    $tipo = "Domicilios";
                    break;

                case "Reservas":
                    $tipo = "ReservaGeneral";
                    $version = "";
                    break;

                case "Evento":
                    $version = substr($DATOS[url_respuesta], "-1");
                    if ($version == "=") {
                        $version = "";
                    }

                    $tipo = "EventoRegistro";
                    break;

                default:
                    $version = substr($DATOS[url_respuesta], "-1");
                    if ($version == "=") {
                        $version = "";
                    }

                    $tipo = "EventoRegistro";
                    break;
            }

            $sql = "INSERT INTO `PeticionesPlacetoPay`(`IDClub`, `IDSocio`, `referencia`,`tipo`,`IDMaestro`,`IDReserva`, `estado_transaccion`,  `request_id`, `fecha_peticion`, `url_proceso`,`valor`,`Linea`)
				VALUES (
				'" . $DATOS["extra2"] . "',
				'" . $DATOS["IDSocio"] . "',
				'" . $DATOS["ref"] . "',
				'" . $tipo . "',
				'" . $DATOS["extra1"] . "',
				'" . $DATOS["extra1"] . "',
				'" . $DATA["status"]["status"] . "',
				'" . $DATA["requestId"] . "',
				'" . $DATA["status"]["date"] . "',
				'" . $DATA["processUrl"] . "',
				'" . $valor . "',
				'" . $version . "'
			)";
            $dbo->query($sql);

            $respuesta["message"] = "PASARELA EXITOSA";
            $respuesta["success"] = true;
            $respuesta["response"] = $DATA;
        }

        return $respuesta;
    }

    public function respuestaPlaceToPay($referencia)
    {
        $dbo = &SIMDB::get();

        $datos_transaccion = $dbo->fetchAll("PeticionesPlacetoPay", " referencia = '" . $referencia . "'", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_transaccion["IDClub"] . "' ", "array");

        $login = trim($datos_club['LoginPlaceToPay']);
        $secretKey = trim($datos_club['SecretKeyPlaceToPay']);

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

        if ($datos_club["IsTestPlaceToPay"] == 1) {
            $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
        } else {
            $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
        }

        $pagina_consulta = $url_place_to_pay . '/api/session/' . $datos_transaccion["request_id"];
        $headers = ['Content-Type:application/json; charset=utf-8'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $pagina_consulta,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($auth),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $DATA = json_decode($response, true);

        curl_close($curl);

        $estado_transaccion = $DATA["status"]["status"];
        $mensaje = $DATA["status"]["message"];
        $requestId = $DATA["requestId"];
        $reference = $DATA["request"]["payment"]["reference"];

        $sql_actualiza = "UPDATE PeticionesPlacetoPay SET estado_transaccion = '" . $estado_transaccion . "', razon='" . $mensaje . "', request_id = '" . $requestId . "'  WHERE referencia = '" . $reference . "'";
        $dbo->query($sql_actualiza);

        if ($datos_transaccion["tipo"] == "ReservaGeneral") {
            switch ($estado_transaccion) {
                case "OK":
                case "APPROVED":

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';

                    break;

                case "REJECTED":

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';

                    break;

                case "PENDING":

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';

                    break;

                case "APPROVED_PARTIAL":

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';

                    break;

                case "PARTIAL_EXPIRED":

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';

                    break;

                case "PENDING_VALIDATION":

                    $estadoReserva = 1;
                    $pagado = 'S';
                    $estado = 'A';

                    break;

                case "REFUNDED":

                    $estadoReserva = 2;
                    $pagado = 'N';
                    $estado = 'R';

                    break;

                default:
                    $estado_transaccion = "OTRO";
            }

            $actualizaReserva = "    UPDATE ReservaGeneral
            SET IDTipoPago = '1', IDEstadoReserva = '" . $estadoReserva . "', Pagado = '" . $pagado . "', PagoPayu = '" . $pagado . "', MedioPago = 'Place To Pay',
            FechaTransaccion = NOW(), CodigoRespuesta = '" . $reference . "',
            EstadoTransaccion = '" . $estado . "'
            WHERE IDReservaGeneral ='" . $datos_transaccion["IDReserva"] . "' ";

            $dbo->query($actualizaReserva);
        } elseif ($datos_transaccion["tipo"] == "Domicilios") {

            switch ($estado_transaccion) {
                case "OK":
                case "APPROVED":
                    $estado = 'A';
                    $pagado = 'S';
                    break;

                case "REJECTED":
                    $estado = 'R';
                    $pagado = 'N';
                    break;

                case "PENDING":
                    $estado = 'A';
                    $pagado = 'S';
                    break;

                case "APPROVED_PARTIAL":
                    $estado = 'A';
                    $pagado = 'S';
                    break;

                case "PARTIAL_EXPIRED":
                    $estado = 'R';
                    $pagado = 'N';
                    break;

                case "PENDING_VALIDATION":
                    $estado = 'A';
                    $pagado = 'S';
                    break;

                case "REFUNDED":
                    $estado = 'R';
                    $pagado = 'N';
                    break;

                default:
                    $estado = "OTRO";
            }

            $version = $dato_transaccion[Linea];

            if ($version == 0) {
                $version = "";
            }

            $query = "UPDATE Domicilio " . $version . "
                SET EstadoTransaccion = '" . $estado . "',
                    FechaTransaccion = NOW(),
                    CodigoRespuesta = '" . $reference . "',
                    MedioPago = 'Placetopay',
                    TipoMedioPago = '1', Pagago = '$pagado', PagoPayu = '$pagado'
                WHERE IDDomicilio = '" . $datos_transaccion["IDReserva"] . "'";

            $sql_actualizar = $dbo->query($query);
        } elseif ($datos_transaccion["tipo"] == "EventoRegistro") {

            $query = "UPDATE EventoRegistro
                            SET EstadoTransaccion='" . $estado_transaccion . "',
                                FechaTransaccion='" . $fecha . "',
                                CodigoRespuesta='" . $mensaje . "',
                                MedioPago='" . $requestId . "',
                                TipoMedioPago='" . $txtfirma . "'
                            WHERE IDEventoRegistro='" . $datos_transaccion["IDMaestro"] . "'";
            $sql_actualizar = $dbo->query($query);

            if ($firma == $firma_sitio) {
                if ($estado_transaccion == "APPROVED") {
                    $cambia_estado = "UPDATE EventoRegistro" . $datos_transaccion["Linea"] . "
                                                            SET EstadoTransaccion='A',
                                                            Pagado ='S',
                                                            PagoPayu = 'S'
                                                            WHERE IDEventoRegistro='" . $datos_transaccion["IDMaestro"] . "'";
                    $result_cambia_estado = $dbo->query($cambia_estado);
                } elseif ($estado_transaccion == "REJECTED") {
                    SIMWebServiceApp::valida_pago_evento($IDClub, $IDSocio, $IDEventoRegistro);
                }
            }
        } elseif ($datos_transaccion["tipo"] == "Deuda") {
            //Aqui se envia al servicio del condado el pago

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_transaccion["IDSocio"] . "' and IDClub = '" . $datos_transaccion["IDClub"] . "'", "array");
            $accion_socio = $datos_socio["Accion"];
            if (!empty($accion_socio)) {
                $array_documentos = json_decode($datos_transaccion["Documento"], true);
                //Verifico si la membresia existe
                $endpoint = ENDPOINT_CONDADO;
                $wsdlFile = ENDPOINT_CONDADO;
                if (count($array_documentos) > 0) {
                    foreach ($array_documentos as $detalle_documento) {
                        $num_recibo = time() . rand(0, 100);
                        $valor_documento = $detalle_documento["Valor"];
                        $numero_documento = $detalle_documento["NumeroDocumento"];
                        try {

                            /*
                            if ($estado_transaccion=="APPROVED") {
                            $client = new SoapClient($wsdlFile, array('exceptions' => 0));
                            $parameters = array( AplicarCabecera => array( FacturaCabecera => array(
                            Comentario=>"pago cartera",
                            Fecha=>date("Y-m-d"),
                            Id_Cliente=>$accion_socio,
                            Id_Recibo=>$num_recibo,
                            Monto=>$valor_documento,
                            Observacion=>"pago cartera documento",
                            Papeleta=>"",
                            Tipo=>"TC"
                            )),
                            AplicarDetalle => array( FacturaDetalle => array(
                            Id_Cliente=>$accion_socio,
                            Id_Recibo=>$num_recibo,
                            Monto_Documento=>$valor_documento,
                            Numero_Documento=>$numero_documento,
                            Observacion=>"pago cartera documento.",
                            Tipo_Documento=>"1",
                            ))
                            );
                            $result = $client->Pago_Factura($parameters);
                            }
                             */
                            $sql = "INSERT INTO `PeticionesPlacetoPay`(`IDClub`, `IDSocio`, `IDMaestro`, `tipo`, `referencia`,`estado_transaccion`,`request_id`,`fecha_peticion`,`razon`,`medio`,`Documento`, `RespuestaWS`,`num_recibo`,`valor`)
                                VALUES (
                                    '" . $datos_transaccion["IDClub"] . "',
                                    '" . $datos_transaccion["IDSocio"] . "',
                                    '" . $datos_transaccion["IDMaestro"] . "',
                                    'Servicio_Pago_Factura',
                                '" . $reference . "',
                                '" . $estado_transaccion . "',
                                '" . $requestId . "',
                                '" . $fecha . "',
                                '" . $mensaje . "',
                                'AUTOMATICO',
                                '" . $numero_documento . "',
                                '" . $result->Pago_FacturaResult . "',
                                '" . $num_recibo . "',
                                '" . $valor_documento . "'
                                )";
                            $dbo->query($sql);
                            //print_r($result);
                        } catch (SoapFault $fault) {
                            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                        }
                    }
                }
            }
        }

        $respuesta["message"] = "PASARELA EXITOSA";
        $respuesta["success"] = true;
        $respuesta["response"] = $DATA;

        return $respuesta;
    }

    /* ....  FIN PLACETOPAY .... */

    /* .... CREDIBANCO NUEVA VERSION .... */

    public function CredibancoSolicitudV2($Datos)
    {
        $dbo = &SIMDB::get();

        // SACAMOS LOS DATOS DEL CLUB Y DEL SOCIO PARA ARMAR LA SOLICITUD HE INSERTAR LA ORDEN EN PAGOS CREDIBANCO

        $datos_club = $dbo->fetchAll("Club", "IDClub = '" . $Datos["extra2"] . "'");
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $Datos["IDSocio"] . "'");
        if ($Datos["Modulo"] == "Domicilio") {
            $version = $Datos["version"];
        } else {
            $version = "";
        }

        if ($Datos["Modulo"] == "FacturasPraderaPotosi") :
            $version = $Datos["Almacen"];
            $Propina = $Datos["Propina"];
        endif;

        if ($Datos[Modulo] == "FacturasPraderaPotosi") {
            $Datos["valor"] += $Datos["Propina"];
        }

        if ($Datos[Modulo] == "ConsumosPereira") {

            if ($Datos['Propina'] == 'S') {
                $Propina = ((int)$Datos['valor'] * 10) / 100; // Regla de tres
                $Propina = round($Propina, 0);  // Quitar los decimales
                $Datos["valor"] += $Propina;
            }
        }

        //insertamos los datos en la tabla CuestionarioParaOtrosPagos para el club el bosque
        if ($Datos[Modulo] == "CuestionarioOtrosPagos") {
            $sql_insertar_datos = "INSERT INTO CuestionarioParaOtrosPagos(NumeroDelDerechoSocial,Concepto,Valor,IDSocio,FechaTrCr) VALUES('" . $Datos[NumeroDelDerechoSocial] . "','" . $Datos[Concepto] . "','" . $Datos[valor] . "','" . $Datos[IDSocio] . "',NOW())";
            $dbo->query($sql_insertar_datos);
        }

        // INSERTAMOS EN LA TABLA DE CREDIBANCO LA ORDEN CON LOS DATOS INICIALES
        $insert = "INSERT INTO PagoCredibanco (IDClub, IDSocio, Modulo, NumeroTransaccion, NumeroDocumento, ValorPago, Email, NombreSocio, ApellidoSocio, NumeroAccion, Telefono, Direccion, Genero, FechaTransaccion, reserved12, reserved13, reserved14, Factura, FechaTrCr, UsuarioTrCr, UsuarioTrEd)
									VALUES ('" . $Datos[extra2] . "', '" . $Datos[IDSocio] . "', '" . $Datos[Modulo] . "', '" . $Datos[ref] . "', '" . $datos_socio[NumeroDocumento] . "', '" . $Datos[valor] . "', '" . $datos_socio[CorreoElectronico] . "',
									'" . $datos_socio[Nombre] . "', '" . $datos_socio[Apellido] . "', '" . $datos_socio[Accion] . "', '" . $datos_socio[Celular] . "', '" . $datos_socio[Direccion] . "', '" . $datos_socio[Genero] . "', NOW(), '" . $Datos[extra1] . "', '" . $version . "','$Propina','" . $Datos['Factura'] . "',NOW(), 'SIMPasarelaPagos2', 'API')";
        $dbo->query($insert);
        $lastid = $dbo->lastID();

        // SE VERIFICA SI SON PRUEBAS O NO PARA CAMBIAR EL LINK

        if ($datos_club["IsTestCredibancoApi"] == 1) {
            $URL = "https://ecouat.credibanco.com/payment/rest/register.do?";
        } else {
            $URL = "https://eco.credibanco.com/payment/rest/register.do?";
        }

        // ARMAMOS LA SOLICITUD PARA HACER EL PEDIDO A L API DE CREDIBANCO

        $usuario = $datos_club["UsuarioApiCredibanco"];
        $pass = $datos_club["PassApiCredibanco"];
        $numeroOrden = $lastid;
        $UrlRetorno = $datos_club["UrlRetornoApiCredibanco"];

        // SECCION DE CODIGO CUANDO SE PUEDE PASA
        if ($Datos[Modulo] == "Reservas") :
            $datos_reserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = $Datos[extra1]");
            $datos_pago = $dbo->fetchAll("CredibancoNuevaVersionServicio", "IDServicio = $datos_reserva[IDServicio]");

            if ($datos_pago[IDCredibancoNuevaVersionServicio] > 0) :
                $usuario = $datos_pago["UsuarioApiCredibanco"];
                $pass = $datos_pago["PassApiCredibanco"];
            endif;
        endif;

        $valor = $Datos["valor"] . "00";


        $solicitud = $URL . "userName=" . $usuario . "&password=" . $pass . "&orderNumber=" . $numeroOrden . "&returnUrl=" . $UrlRetorno . "&amount=" . $valor;

        $consumo = file_get_contents($solicitud);

        $DATOS = json_decode($consumo, true);
        // VERIFICACAMOS QUE NO LLEGUEN ERRORES DE LA PASARELA, DE SER ASÍ SE DEBE ENVIAR EL ERROR.

        if (isset($DATOS["errorCode"])) {
            // SI HAY UN ERROR SE GUARDA EN LA BASE DE DATOS PARA AUDITORIA.
            $update = "UPDATE PagoCredibanco SET errorMessage = '" . $DATOS["errorMessage"] . "', additionalObservations = '" . $DATOS["errorMessage"] . "', errorCode = '" . $DATOS["errorCode"] . "' WHERE IDPagoCredibanco = " . $lastid;
            $dbo->query($update);

            $respuesta["message"] = $DATOS["errorMessage"];
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            // SI NO HOAY ERRORES ACTUALIZAMOS LA TABLA CON LA ORDEN QUE NOS RETORNA PARA HACER LA ACTUALIZACION CON LOS VALORES DE REOTRNO DE LA PASARELA.
            $update = "UPDATE PagoCredibanco SET NumeroFactura = '" . $DATOS[orderId] . "' WHERE IDPagoCredibanco = " . $lastid;
            $dbo->query($update);

            $respuesta["message"] = "PASARELA EXITOSA";
            $respuesta["success"] = true;
            $respuesta["response"] = $DATOS;
        }

        return $respuesta;
    }

    public function CredibancoRespuestaV2($orden)
    {
        $dbo = &SIMDB::get();

        // OBTENEMOS LA INFORMACION PARA PROCESAR LA SOLICITUD A LA PLATAFORMA DE CREDIBANCO

        $pago = "SELECT * FROM PagoCredibanco WHERE NumeroFactura = '" . $orden . "'";
        $resulPago = $dbo->query($pago);
        $datos_pago = $dbo->fetchArray($resulPago);

        $club = "SELECT UsuarioApiCredibanco, PassApiCredibanco, IsTestCredibancoApi FROM Club WHERE IDClub = '" . $datos_pago["IDClub"] . "'";
        $resulClub = $dbo->query($club);
        $datos_club = $dbo->fetchArray($resulClub);

        if ($datos_club["IsTestCredibancoApi"] == 1) {
            $URL = "https://ecouat.credibanco.com/payment/rest/getOrderStatusExtended.do?";
        } else {
            $URL = "https://eco.credibanco.com/payment/rest/getOrderStatusExtended.do?";
        }

        $usuario = $datos_club["UsuarioApiCredibanco"];
        $pass = $datos_club["PassApiCredibanco"];

        // SE ARMA Y REALIZA LA SOLICITUD A CREDIBANCO
        $solicitud = $URL . "userName=" . $usuario . "&password=" . $pass . "&orderId=" . $orden;
        $consumo = file_get_contents($solicitud);
        $DATOS = json_decode($consumo, true); //DATOS DE CREDIBANCO
        $DATOS["Club"] = $datos_pago["IDClub"];
        // SI erroCode  ES 0 LA SOLICITUD SE HIZO CORRECTAMENTE, DE LO OONTRARIO REPORTAMOS UN ERROR.
        if ($DATOS["errorCode"] == 0) {
            // ACTUALIZACMOS LA TABLA DE CREDIBANCO CON LOS DATOS DE RESPUESTA.
            $update = "	UPDATE PagoCredibanco
							SET 	errorCode = '" . $DATOS["errorCode"] . "',
									errorMessage = '" . $DATOS["errorMessage"] . "',
									orderStatus = '" . $DATOS["orderStatus"] . "',
									acquirerId = '" . $DATOS["terminalId]"] . "',
									purchaseIpAddress = '" . $DATOS["ip"] . "',
									purchaseCurrencyCode = '" . $DATOS["currency"] . "',
									cardNumber = '" . $DATOS["cardAuthInfo"]["pan"] . "',
                                    xmlResponse='" . json_encode($DATOS) . "',
									FechaTrEd = NOW()
							WHERE 	IDPagoCredibanco = '" . $datos_pago["IDPagoCredibanco"] . "'";
            $dbo->query($update);

            $reserva = $datos_pago["reserved12"];
            $valor = $datos_pago["ValorPago"];
            $Modulo = $datos_pago["Modulo"];
            $Version = $datos_pago["reserved13"];


            $select = "SELECT NumeroDocumento, Nombre, Apellido, Accion FROM Socio WHERE IDSocio = " . $datos_pago["IDSocio"];
            $result = $dbo->query($select);
            $datos_socio = $dbo->fetchArray($result);

            $estado = $DATOS["orderStatus"];

            //SACAMOS LA INFORMACION IMPORTANTE DE CADA ESTADO QUE NO RESULTA EN CREDIBANCO.
            switch ($estado) {
                case "0":
                    // $estadoTx = "NO PAGADO";
                    $estadoFinal = 2;
                    $pagado = "N";
                    $estadoTransaccion = "R";
                    break;

                case "1":
                case "7":
                    // $estadoTx = "PENDIENTE";
                    $estadoFinal = 3;
                    $pagado = "N";
                    $estadoTransaccion = "P";
                    break;

                case "2":
                    // $estadoTx = "APROBADO";
                    $estadoFinal = 1;
                    $pagado = "S";
                    $estadoTransaccion = "A";
                    break;

                case "3":
                case "6":
                    // $estadoTx = "RECHAZADO";
                    $estadoFinal = 2;
                    $pagado = "N";
                    $estadoTransaccion = "R";
                    break;

                default:
                    // $estadoTx = "OTRO";
                    $estadoFinal = 2;
                    $pagado = "N";
                    $estadoTransaccion = "R";
                    break;
            }

            // DEPENDIENDO DEL MODULO ACTUALIZAMOS LA TABLA CORERSPONDIENTE PARA QUE SE ACEPTE EL PAGO O TRANSACCION.
            switch ($Modulo) {
                case "Domicilio":

                    $sql_pedido = "UPDATE Domicilio" . $Version . "
											SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
												CodigoPago='" . $datos_pago["NumeroTransaccion"] . "',
												EstadoTransaccion='" . $estadoTransaccion . "',
												FechaTransaccion= NOW(),
												CodigoRespuesta='" . $estado . "',
												MedioPago='CREDIBANCO V API',
												TipoMedioPago='CREDIBANCO V API'
											WHERE IDDomicilio = '" . $datos_pago["reserved12"] . "' and IDClub = '" . $datos_pago["IDClub"] . "'";
                    $dbo->query($sql_pedido);

                    break;

                case "Evento":

                    $sql_evento = "UPDATE EventoRegistro
                                                    SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
                                                        CodigoPago='" . $datos_pago["NumeroTransaccion"] . "',
                                                        EstadoTransaccion='" . $estadoTransaccion . "',
                                                        FechaTransaccion= NOW(),
                                                        CodigoRespuesta='" . $estado . "',
                                                        MedioPago='CREDIBANCO V API',
                                                        TipoMedioPago='CREDIBANCO V API'
                                                    WHERE IDEventoRegistro = '" . $datos_pago["reserved12"] . "' and IDClub = '" . $datos_pago["IDClub"] . "'";
                    $dbo->query($sql_evento);

                    break;

                case "Inscripcion";

                    $dia_actual = date("d");
                    $mes_actual = date("m");
                    $datos_producto = $dbo->fetchAll("ProductoLiga", " Valor = '" . $datos_pago["ValorPago"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ", "array");
                    $NumeroMeses = $datos_producto["Meses"];
                    $FechaInicial = date("Y-m") . "-05";

                    $nuevafechapago = strtotime('+' . $NumeroMeses . ' month', strtotime($FechaInicial));
                    $PagadoHasta = date('Y-m-d', $nuevafechapago);

                    $sql_pedido = "UPDATE Socio
									SET IDEstadoSocio = '1',PagadoHasta='" . $PagadoHasta . "',FechaPago='" . date("Y-m-d") . "',
											IDProductoLiga='" . $datos_producto["IDProductoLiga"] . "'
									WHERE IDSocio = '" . $datos_pago["IDSocio"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ";
                    $dbo->query($sql_pedido);

                    break;

                case "Extracto":

                    $IDClubConsulta = $datos_pago["IDClub"];
                    $IDSocioConsulta = $datos_pago["IDSocio"];
                    $Valor = $datos_pago["ValorPago"];

                    $sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocioConsulta . "','apruebapagocuotaisraeli','" . json_encode($datos_pago) . "','" . json_encode($DATOS) . "')");

                    if ($IDClubConsulta == 98 && $estado == 2) {
                        require LIBDIR . "SIMWebServiceIsraeli.inc.php";
                        SIMWebServiceIsraeli::pago_cuota($datos_socio["NumeroDocumento"], $Valor, $datos_pago["IDSocio"]);
                    }

                    if ($estado == 2) {
                        SIMUtil::notifica_pago_extracto($IDClubConsulta, $IDSocioConsulta, $Valor);
                    }

                    break;

                case "Donacion":

                    //Envio mensaje de pago exitoso
                    $IDClubConsulta = $datos_pago["IDClub"];
                    $IDSocioConsulta = $datos_pago["IDSocio"];
                    $Valor = $datos_pago["ValorPago"];
                    $frm["UsuarioTrCr"] = "Donacion";
                    $frm["FechaTrCr"] = date("Y-m-d H:i:S");
                    $frm["Valor"] = $datos_pago["ValorPago"];
                    $frm["IDClub"] = $datos_pago["IDClub"];
                    $frm["IDSocio"] = $datos_pago["IDSocio"];
                    $frm["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                    $frm["Accion"] = $datos_socio["Accion"];
                    $id = $dbo->insert($frm, "Donacion", "IDDonacion");

                    break;

                case "Reservas":

                    $actualizaReserva = "UPDATE ReservaGeneral SET Pagado = '$pagado', PagoPayu = '$pagado', MedioPago = 'CREDIBANCO V API',
                                        FechaTransaccion = NOW(), CodigoRespuesta = '$estado',
                                        EstadoTransaccion = '$estadoTransaccion '
                                        WHERE IDReservaGeneral ='$reserva'";

                    $dbo->query($actualizaReserva);

                    break;
                case "FacturasPraderaPotosi":

                    require LIBDIR . "SIMWebServicePradera.inc.php";

                    $NumeroFactura = $datos_pago[NumeroTransaccion];
                    $IDFactura = $datos_pago[reserved12];
                    $TipoPago = $DATOS["cardAuthInfo"]["paymentSystem"];
                    $PagoTotal =  $datos_pago["ValorPago"];

                    $DATOS[Almacen] = $datos_pago[reserved13];
                    $DATOS[Propina] = $datos_pago[reserved14];

                    SIMWebServicePradera::actuliza_vntas_pos_split_pgos($NumeroFactura, $IDFactura, $TipoPago, $PagoTotal, $DATOS);

                    break;

                case "Taloneras":

                    if ($pagado == 'S') :
                        $actualiza = ", Activo = 1";
                    endif;
                    $update = "UPDATE SocioTalonera SET EstadoTransaccion = '$estadoTransaccion', Pagado = '$pagado', MedioPago = 'CREDIBANCO V API' $actualiza WHERE IDSocioTalonera = '$datos_pago[reserved12]'";
                    $dbo->query($update);
                    break;

                case "CarteraPereira":
                    require LIBDIR . "SIMWebServiceCampestrePereira.inc.php";
                    if ($pagado == 'S') {
                        SIMWebServiceCampestrePereira::abono2($datos_pago);
                        // FacturaPereira::guardar_factura($datos_pago);
                        $arr_Factura = explode('/', $datos_pago['Factura']);
                        foreach ($arr_Factura as  $facturaPagada) {
                            if (!empty($facturaPagada)) :
                                $sqlFactura = "update CarteraVencida set FechaPago = NOW() where Factura = '" . $facturaPagada . "'";
                                $q_Factura = $dbo->query($sqlFactura);
                            endif;
                        }
                    }


                    break;
                case "ConsumosPereira":
                    require LIBDIR . "SIMWebServiceCampestrePereira.inc.php";
                    if ($pagado == 'S') {

                        // Definimos el numero consecutivo de la factura
                        $IDFactura = $dbo->getFields('FacturaConsumo', "IDFactura", " IDClub = " . $datos_pago['IDClub'] . " ORDER BY IDFacturaConsumo DESC LIMIT 1");

                        if (!empty($IDFactura)) {
                            $IDFactura += 1;
                        } else {
                            $IDFactura = 14;
                        }

                        // Obtenemos la información del consumo pagado
                        $ConsumosPereira = SIMWebServiceCampestrePereira::Consumos($datos_socio['NumeroDocumento']);
                        $Facturas = explode("/", $datos_pago['Factura']);
                        foreach ($Facturas as $i => $factura) {
                            $arr_factura = explode("|", $factura);
                            $Consecutivo = $arr_factura[1];
                            foreach ($ConsumosPereira as $consumo) {
                                if ($Consecutivo == $consumo['consecutivo']) {
                                    $detalle = $consumo['consecutivoControl'] . "|" . $consumo['id'] . "|" . $consumo['consecutivo'] . "|" . $consumo['productoId'] . "|" . $consumo['nombreProducto'];

                                    $sql_FacturaConsumo = "INSERT INTO `FacturaConsumo`(`IDClub`, `IDSocio`, IDFactura, `Detalle`, `NumeroDocumentoFactura`, `TipoSocio`, `Iva`, `Total`, `Estado`, `NumeroTransaccion`, `IDTransaccionCredibanco`, `MedioPago`, `UsuarioTrCr`, `FechaTrCr`) VALUES (" . $datos_pago["IDClub"] . "," . $datos_pago["IDSocio"] . ",$IDFactura,'$detalle','" . $datos_pago["NumeroFactura"] . "','Socio','" . $datos_pago['reserved14'] . "','" . $datos_pago['ValorPago'] . "','Pagada','" . $datos_pago['IDPagoCredibanco'] . "','" . $datos_pago['IDPagoCredibanco'] . "','CrediBanco','WS',NOW())";

                                    $dbo->query($sql_FacturaConsumo);
                                }
                            }
                        }
                        //Fin Obtenemos la información del consumo pagado
                        SIMWebServiceCampestrePereira::Factura($datos_pago["NumeroFactura"]);
                    }

                    break;
            }

            // RETORNAMOS DATOS PARA LA PANTALLA DEL SOCIO SE VEA LA INFORMACION NECESARIO.

            $respuesta["message"] = "PASARELA EXITOSA";
            $respuesta["success"] = true;
            $respuesta["response"] = $DATOS;
        } else {
            // SI HAY UN ERROR SE GUARDA EN LA BASE DE DATOS PARA AUDITORIA.
            $update = "UPDATE PagoCredibanco SET errorMessage = '" . $DATOS["errorMessage"] . "', additionalObservations = '" . $DATOS["errorMessage"] . "', errorCode = '" . $DATOS["errorCode"] . "' WHERE NumeroFactura = " . $orden;
            $dbo->query($update);

            $respuesta["message"] = $DATOS["errorMessage"];
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    /* .... FIN CREDIBANCO NUEVA VERSION .... */

    /* .... WOMPI .... */
    public function bancos($ApiKey, $IDClub)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '$IDClub' ", "array");

        $curl = curl_init();

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/pse/financial_institutions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $ApiKey,
            ),
        ));

        $response = curl_exec($curl);

        $DATA = json_decode($response, true);

        curl_close($curl);

        $respuesta["message"] = "PASARELA EXITOSA";
        $respuesta["success"] = true;
        $respuesta["response"] = $DATA;

        return $respuesta;
    }

    public function AutenticacionWompi($IDClub)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '$IDClub' ", "array");

        $ApiKey = $datos_club[ApiKeyWompi];

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/merchants/' . $ApiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;
    }

    public function Wompi($DATOS)
    {

        if (empty($DATOS[descripcion]))
            $DATOS[descripcion] = "Pago APP Mi Club";

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $DATOS["extra2"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $DATOS["IDSocio"] . "' ", "array");

        $sqlWompi = "   INSERT INTO PagosWompi (IDReserva,IDClub, IDSocio, Tipo, Estado, Valor, FechaTrCr, UsuarioTrCr)
                        VALUES ($DATOS[extra1],$DATOS[extra2], $DATOS[IDSocio], '$DATOS[Modulo]', 'PENDIENTE' ,$DATOS[valor], NOW(), '$datos_socio[Nombre]')";
        $qryWompi = $dbo->query($sqlWompi);
        $ID = $dbo->lastID();

        $ApiKey = $datos_club[ApiKeyWompi];

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        // CONSEGUIMOS LA AUTENTICACION
        $DatosAuteticacion = SIMPasarelaPagos2::AutenticacionWompi($DATOS["extra2"]);
        $Autenticacion = $DatosAuteticacion[data][presigned_acceptance][acceptance_token];

        $solicitud = '
        {
            "payment_method":
            {
                "type": "PSE",
                "user_type": 0,
                "user_legal_id_type": "CC",
                "user_legal_id": "' . $datos_socio[NumeroDocumento] . '",
                "financial_institution_code": "' . $DATOS[Banco] . '",
                "payment_description": "' . $DATOS[descripcion] . '"
            },
            "redirect_url":"https://www.miclubapp.com/respuesta_transaccion_wompi.php?IDClub=8&IDPagosWompi=' . $ID . '",
            "amount_in_cents": ' . $DATOS[valor] . '00,
            "reference": "' . $ID . '",
            "customer_email": "' . $datos_socio[CorreoElectronico] . '",
            "currency": "COP",
            "acceptance_token" : "' . $Autenticacion . '"
        }';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/transactions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $solicitud,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $ApiKey,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function TransaccionWompi($IDClub, $ReferenciaPago)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '$IDClub' ", "array");

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        $solicitud = $Wompi . "/transactions//" . $ReferenciaPago;
        $solicitud = str_replace("//", "/", $solicitud);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $solicitud,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function confirmacion($IDClub, $IDPagosWompi)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ", "array");

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        $solicitud = $Wompi . "/transactions?reference=" . $IDPagosWompi;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $solicitud,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $datos_club[PrivKeyWompi]
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function RespuestaWompi($IDClub, $IDPagosWompi, $ReferenciaPago, $Sonda = 0)
    {
        $dbo = SIMDB::get();



        if ($Sonda == 0) :
            $DATA = SIMPasarelaPagos2::TransaccionWompi($IDClub, $ReferenciaPago);
        else :
            $DATA = SIMPasarelaPagos2::confirmacion($IDClub, $IDPagosWompi);
        endif;


        if (count($DATA[data]) > 0 && $Sonda == 1) {
            foreach ($DATA[data] as $datos_transaccion) {
                $array_ultimo_reporte = $datos_transaccion;
            }
            $DATA[data] = $array_ultimo_reporte;
        }

        // ACTUALIZAMOS LA TABLA DE PAGOS
        $Estado = $DATA[data][status];
        $MensajeEstado = $DATA[data][status_message];
        $ReferenciaWompi = $DATA[data][id];
        $FechaTransaccion = $DATA[data][created_at];
        $MetodoPago = json_encode($DATA[data][payment_method]);
        $UrlRespuesta = $DATA[data][redirect_url] . "&id=" . $ReferenciaWompi;
        $Response = json_encode($DATA);

        $UpdatePagos = "UPDATE PagosWompi 
                        SET Estado = '$Estado', 
                            MensajeEstado = '$MensajeEstado', 
                            ReferenciaWompi = '$ReferenciaWompi', 
                            FechaTransaccion = '$FechaTransaccion', 
                            MetodoPago = '$MetodoPago', 
                            UrlRespuesta = '$UrlRespuesta',
                            Response = '$Response',
                            FechaTrEd = NOW(),
                            UsuarioTrEd = 'PASARELA WOMPI'
                            WHERE IDPagosWompi = $IDPagosWompi";
        $dbo->query($UpdatePagos);

        $datos_pago = $dbo->fetchAll("PagosWompi", " IDPagosWompi = '$IDPagosWompi' ", "array");

        $Modulo = $datos_pago[Tipo];
        //SACAMOS LA INFORMACION IMPORTANTE DE CADA ESTADO QUE NO RESULTA EN CREDIBANCO.
        switch ($Estado):

            case "APPROVED":
                $estadoFinal = 1;
                $pagado = "S";
                $estadoTransaccion = "A";
                $success = true;
                break;

            case "DECLINED":
            case "ERROR":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "R";
                $success = false;
                break;

            default:
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "P";
                $success = true;
                break;

        endswitch;

        // DEPENDIENDO DEL MODULO ACTUALIZAMOS LA TABLA CORERSPONDIENTE PARA QUE SE ACEPTE EL PAGO O TRANSACCION.
        switch ($Modulo):

            case "Domicilio":

                $sql_pedido = "UPDATE Domicilio" . $Version . "
                                        SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
                                            CodigoPago='" . $datos_pago["NumeroTransaccion"] . "',
                                            EstadoTransaccion='" . $estadoTransaccion . "',
                                            FechaTransaccion= NOW(),
                                            CodigoRespuesta='" . $Estado . "',
                                            MedioPago='CREDIBANCO V API',
                                            TipoMedioPago='CREDIBANCO V API'
                                        WHERE IDDomicilio = '" . $datos_pago["reserved12"] . "' and IDClub = '" . $datos_pago["IDClub"] . "'";
                $dbo->query($sql_pedido);

                break;

            case "Inscripcion";

                $dia_actual = date("d");
                $mes_actual = date("m");
                $datos_producto = $dbo->fetchAll("ProductoLiga", " Valor = '" . $datos_pago["ValorPago"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ", "array");
                $NumeroMeses = $datos_producto["Meses"];
                $FechaInicial = date("Y-m") . "-05";

                $nuevafechapago = strtotime('+' . $NumeroMeses . ' month', strtotime($FechaInicial));
                $PagadoHasta = date('Y-m-d', $nuevafechapago);

                $sql_pedido = "UPDATE Socio
                                SET IDEstadoSocio = '1',PagadoHasta='" . $PagadoHasta . "',FechaPago='" . date("Y-m-d") . "',
                                        IDProductoLiga='" . $datos_producto["IDProductoLiga"] . "'
                                WHERE IDSocio = '" . $datos_pago["IDSocio"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ";
                $dbo->query($sql_pedido);

                break;

            case "Extracto":

                $IDClubConsulta = $datos_pago["IDClub"];
                $IDSocioConsulta = $datos_pago["IDSocio"];
                $Valor = $datos_pago["ValorPago"];

                $sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocioConsulta . "','apruebapagocuotaisraeli','" . json_encode($datos_pago) . "','" . json_encode($DATOS) . "')");

                if ($IDClubConsulta == 98 && $estadoFinal == 1) {
                    require LIBDIR . "SIMWebServiceIsraeli.inc.php";
                    SIMWebServiceIsraeli::pago_cuota($datos_socio["NumeroDocumento"], $Valor, $datos_pago["IDSocio"]);
                }

                if ($estadoFinal == 1) {
                    SIMUtil::notifica_pago_extracto($IDClubConsulta, $IDSocioConsulta, $Valor);
                }

                break;

            case "Donacion":

                //Envio mensaje de pago exitoso
                $IDClubConsulta = $datos_pago["IDClub"];
                $IDSocioConsulta = $datos_pago["IDSocio"];
                $Valor = $datos_pago["ValorPago"];
                $frm["UsuarioTrCr"] = "Donacion";
                $frm["FechaTrCr"] = date("Y-m-d H:i:S");
                $frm["Valor"] = $datos_pago["ValorPago"];
                $frm["IDClub"] = $datos_pago["IDClub"];
                $frm["IDSocio"] = $datos_pago["IDSocio"];
                $frm["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $frm["Accion"] = $datos_socio["Accion"];
                $id = $dbo->insert($frm, "Donacion", "IDDonacion");

                break;

            case "Reservas":

                $reserva = $datos_pago[IDReserva];

                if ($pagado == "S") {
                    $EstadoT = " IDEstadoReserva = '1' , ";
                } else {
                    $condicion_no_aprobado = " and EstadoTransaccion <> 'A' ";
                }


                $actualizaReserva = "UPDATE ReservaGeneral SET " . $EstadoT . " Pagado = '$pagado', PagoPayu = '$pagado', MedioPago = 'WOMPI-$Estado',
                                    FechaTransaccion = NOW(), CodigoRespuesta = '$Estado',
                                    EstadoTransaccion = '$estadoTransaccion'
                                    WHERE IDReservaGeneral ='$reserva' " . $condicion_no_aprobado;

                $dbo->query($actualizaReserva);

                /*
                $fila_afectada=$dbo->affected($dblink);	
                if($fila_afectada<=0){
                    $correo = "jorgechirivi@gmail.com";
                    $Mensaje = "No se actualizo la reserva numero " . $reserva;
                    $Asunto = "Reserva Paga No Actualizada";
                    SIMUtil::envia_correo_general(8, $correo, $Mensaje, $Asunto);
                }
                */

                $no_permitidas = array("'", "\"");
                $permitidas = array(" ", " ",);
                $sql_actua = str_replace($no_permitidas, $permitidas, $actualizaReserva);
                $UpdatePagos = "UPDATE PagosWompi SET Observaciones = '" . $sql_actua . " desde SONDA' WHERE IDPagosWompi = $IDPagosWompi";
                $dbo->query($UpdatePagos);
                if ($pagado == "S") {
                    $actualizaReserva = "UPDATE ReservaGeneral SET IDEstadoReserva = '1', EstadoTransaccion = 'A' WHERE IDReservaGeneral ='$reserva'";
                    $dbo->query($actualizaReserva);
                }


                break;

            case "CursoTrimestral":
            case "Curso":
                $reserva = $datos_pago[IDReserva];
                if (!empty($reserva)) {
                    $query = "UPDATE CursoInscripcion
                                SET EstadoTransaccion='" . $Estado . "',
                                    FechaTransaccion=NOW(),
                                    CodigoRespuesta='" . $estado . "',
                                    MedioPago='WOMPI-$Estado'                                
                                WHERE Referencia='" . $reserva . "'";
                    $dbo->query($query);


                    if ($pagado == "S") {
                        $EstadoT = "Confirmado";
                    } else {
                        $EstadoT = "NoPagado";
                    }
                    $cambia_estado = "UPDATE CursoInscripcion
                                                    SET EstadoInscripcion ='" . $EstadoT . "',
                                                    PagoPayu = 'S',
                                                    Pagado = 'S'
                                                    WHERE Referencia='" . $reserva . "'";
                    $dbo->query($cambia_estado);
                }

                break;

            case "Evento":

                if ($pagado == "S") {
                } else {
                    $condicion_no_aprobado = " and EstadoTransaccion <> 'A' ";
                }


                $reserva = $datos_pago[IDReserva];
                $sql_evento = "UPDATE EventoRegistro
                                                    SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
                                                        CodigoPago='" . $datos_pago["ReferenciaWompi"] . "',
                                                        EstadoTransaccion='" . $Estado . "',
                                                        FechaTransaccion= NOW(),
                                                        CodigoRespuesta='" . $Estado . "',
                                                        MedioPago='CREDIBANCO V API',
                                                        TipoMedioPago='CREDIBANCO V API'
                                                    WHERE IDEventoRegistro = '" . $reserva . "' " . $condicion_no_aprobado;
                $dbo->query($sql_evento);
                break;




            case "FacturasPraderaPotosi":

                require LIBDIR . "SIMWebServicePradera.inc.php";


                $NumeroFactura = $datos_pago[ref];
                $IDFactura = $datos_pago[extra1];
                $TipoPago = $DATOS["payerData"]["paymentWay"] . "|" . $DATOS["payerData"]["paymentSystem"];
                $PagoTotal =  $datos_pago["ValorPago"];

                SIMWebServicePradera::actuliza_vntas_pos_split_pgos($NumeroFactura, $IDFactura, $TipoPago,  $PagoTotal);

                break;

            case "Taloneras":
                if ($pagado == "S") {
                    $estado_talonera = ", Activo='1' ";
                } else {
                    $estado_talonera = ", Activo='0' ";
                }

                $update = "UPDATE SocioTalonera SET EstadoTransaccion = '$estadoTransaccion', Pagado = '$pagado', MedioPago = 'WOMPI-$Estado' " . $estado_talonera . " WHERE IDSocioTalonera = $datos_pago[IDReserva]";
                $dbo->query($update);

                break;

        endswitch;

        if (count($Response) > 0) :

            $DATOS[wompi] = $DATA;
            $DATOS[MiClub] = $datos_pago;

            $respuesta["message"] = "RESPUESA CORRECTA";
            $respuesta["success"] = $success;
            $respuesta["response"] = $DATOS;

        else :
            $respuesta["message"] = "NO HAY RESPUESTA DE LA TRANSACCIÓN, COMUNIQUESE CON EL ADMINISTRADOR";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    /* .... FIN WOMPI .... */
    /* .... PAYWAY .... */
    public function bancosPayWay($ApiKey, $IDClub)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '$IDClub' ", "array");

        $curl = curl_init();

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/pse/financial_institutions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $ApiKey,
            ),
        ));

        $response = curl_exec($curl);

        $DATA = json_decode($response, true);

        curl_close($curl);

        $respuesta["message"] = "PASARELA EXITOSA";
        $respuesta["success"] = true;
        $respuesta["response"] = $DATA;

        return $respuesta;
    }

    public function AutenticacionPayWay($IDClub)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '$IDClub' ", "array");

        $ApiKey = $datos_club[ApiKeyWompi];

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/merchants/' . $ApiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;
    }

    public function PayWay($DATOS)
    {

        if (empty($DATOS[descripcion]))
            $DATOS[descripcion] = "Pago APP Mi Club";

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $DATOS["extra2"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $DATOS["IDSocio"] . "' ", "array");

        $sqlWompi = "   INSERT INTO PagosWompi (IDReserva,IDClub, IDSocio, Tipo, Estado, Valor, FechaTrCr, UsuarioTrCr)
                        VALUES ($DATOS[extra1],$DATOS[extra2], $DATOS[IDSocio], '$DATOS[Modulo]', 'PENDIENTE' ,$DATOS[valor], NOW(), '$datos_socio[Nombre]')";
        $qryWompi = $dbo->query($sqlWompi);
        $ID = $dbo->lastID();

        $ApiKey = $datos_club[ApiKeyWompi];

        if ($datos_club["IsTestWompi"] == 1) {
            $Wompi = TEST_WOMPI;
        } else {
            $Wompi = WOMPI;
        }

        // CONSEGUIMOS LA AUTENTICACION
        $DatosAuteticacion = SIMPasarelaPagos2::AutenticacionWompi($DATOS["extra2"]);
        $Autenticacion = $DatosAuteticacion[data][presigned_acceptance][acceptance_token];

        $solicitud = '
        {
            "payment_method":
            {
                "type": "PSE",
                "user_type": 0,
                "user_legal_id_type": "CC",
                "user_legal_id": "' . $datos_socio[NumeroDocumento] . '",
                "financial_institution_code": "' . $DATOS[Banco] . '",
                "payment_description": "' . $DATOS[descripcion] . '"
            },
            "redirect_url":"https://www.miclubapp.com/respuesta_transaccion_wompi.php?IDClub=8&IDPagosWompi=' . $ID . '",
            "amount_in_cents": ' . $DATOS[valor] . '00,
            "reference": "' . $ID . '",
            "customer_email": "' . $datos_socio[CorreoElectronico] . '",
            "currency": "COP",
            "acceptance_token" : "' . $Autenticacion . '"
        }';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Wompi . '/transactions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $solicitud,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $ApiKey,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function TransaccionPayWay($IDClub, $IDPagosPayWay)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ", "array");

        $solicitud = "https://merchant.paymentsway.co/api/external/v1/getbyexternal/" . $IDPagosPayWay;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $solicitud,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                // 'Authorization:' . $datos_club['APIKEY_PayWay'],
                'x-api-key:' . $datos_club['APIKEY_PayWay'],
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function confirmacionPayWay($IDClub, $IDPagosPayWay)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ", "array");

        $solicitud = "https://merchant.paymentsway.co/api/external/v1/getbyexternal/" . $IDPagosPayWay;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $solicitud,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                // 'Authorization:' . $datos_club['APIKEY_PayWay'],
                'x-api-key:' . $datos_club['APIKEY_PayWay'],
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function RespuestaPayWay($IDClub, $IDPagosPayWay, $ReferenciaPago, $Sonda = 0)
    {
        $dbo = SIMDB::get();


        if ($Sonda == 0) :
            $DATA = SIMPasarelaPagos2::TransaccionPayWay($IDClub, $IDPagosPayWay);
        else :
            $DATA = SIMPasarelaPagos2::confirmacionPayWay($IDClub, $IDPagosPayWay);
        endif;
        $DATA = $DATA[0];

        // $DATA['idstatus']['id'] = 34;
        // $DATA['idstatus']['nombre'] = 'Exitosa';

        // $DATA['idstatus']['id'] = 36;
        // $DATA['idstatus']['nombre'] = 'Fallida';

        // ACTUALIZAMOS LA TABLA DE PAGOS
        $Estado = $DATA['idstatus']['nombre'];
        // $MensajeEstado = $DATA[data][status_message];
        $ReferenciaPayWay = $DATA['id'];
        $FechaTransaccion = $DATA['idstatus']['createdat'];
        $MetodoPago = json_encode($DATA['paymentmethod']);
        $UrlRespuesta = $DATA['responseUrl'];
        $Response = json_encode($DATA);

        $UpdatePagos = "UPDATE PagosPayWay 
                        SET Estado = '$Estado', 
                            MensajeEstado = '$Estado', 
                            ReferenciaPayWay = '$ReferenciaPayWay', 
                            FechaTransaccion = '$FechaTransaccion', 
                            MetodoPago = '$MetodoPago', 
                            UrlRespuesta = '$UrlRespuesta',
                            Response = '$Response',
                            FechaTrEd = NOW(),
                            UsuarioTrEd = 'PASARELA PAYWAY'
                            WHERE IDPagosPayWay = $IDPagosPayWay";
        $dbo->query($UpdatePagos);

        $datos_pago = $dbo->fetchAll("PagosPayWay", " IDPagosPayWay = '$IDPagosPayWay' ", "array");

        $Modulo = $datos_pago['Tipo'];
        //SACAMOS LA INFORMACION IMPORTANTE DE CADA ESTADO QUE NO RESULTA EN CREDIBANCO.
        /*
        Estados Pay Way
        1 "Creada"
        34 "Exitosa"
        35 "Pendiente"
        36 "Fallida"
        37 "Rechazada ClearSale"
        38 "Cancelada"
        */
        switch ($DATA['idstatus']['id']):

            case "1":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "P";
                $success = false;
                break;
            case "34":
                $estadoFinal = 1;
                $pagado = "S";
                $estadoTransaccion = "A";
                $success = true;
                break;

            case "35":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "P";
                $success = false;
                break;
            case "36":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "R";
                $success = false;
                break;
            case "37":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "R";
                $success = false;
                break;
            case "38":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "R";
                $success = false;
                break;

            default:
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "P";
                $success = true;
                break;

        endswitch;

        // DEPENDIENDO DEL MODULO ACTUALIZAMOS LA TABLA CORERSPONDIENTE PARA QUE SE ACEPTE EL PAGO O TRANSACCION.
        switch ($Modulo):

            case "Domicilio":

                $sql_pedido = "UPDATE Domicilio" . $Version . "
                                        SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
                                            CodigoPago='" . $datos_pago["NumeroTransaccion"] . "',
                                            EstadoTransaccion='" . $estadoTransaccion . "',
                                            FechaTransaccion= NOW(),
                                            CodigoRespuesta='" . $Estado . "',
                                            MedioPago='CREDIBANCO V API',
                                            TipoMedioPago='CREDIBANCO V API'
                                        WHERE IDDomicilio = '" . $datos_pago["reserved12"] . "' and IDClub = '" . $datos_pago["IDClub"] . "'";
                $dbo->query($sql_pedido);

                break;

            case "Inscripcion";

                $dia_actual = date("d");
                $mes_actual = date("m");
                $datos_producto = $dbo->fetchAll("ProductoLiga", " Valor = '" . $datos_pago["ValorPago"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ", "array");
                $NumeroMeses = $datos_producto["Meses"];
                $FechaInicial = date("Y-m") . "-05";

                $nuevafechapago = strtotime('+' . $NumeroMeses . ' month', strtotime($FechaInicial));
                $PagadoHasta = date('Y-m-d', $nuevafechapago);

                $sql_pedido = "UPDATE Socio
                                SET IDEstadoSocio = '1',PagadoHasta='" . $PagadoHasta . "',FechaPago='" . date("Y-m-d") . "',
                                        IDProductoLiga='" . $datos_producto["IDProductoLiga"] . "'
                                WHERE IDSocio = '" . $datos_pago["IDSocio"] . "' and IDClub = '" . $datos_pago["IDClub"] . "' ";
                $dbo->query($sql_pedido);

                break;

            case "Extracto":

                $IDClubConsulta = $datos_pago["IDClub"];
                $IDSocioConsulta = $datos_pago["IDSocio"];
                $Valor = $datos_pago["ValorPago"];

                $sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocioConsulta . "','apruebapagocuotaisraeli','" . json_encode($datos_pago) . "','" . json_encode($DATOS) . "')");

                if (
                    $IDClubConsulta == 98 && $estadoFinal == 1
                ) {
                    require LIBDIR . "SIMWebServiceIsraeli.inc.php";
                    SIMWebServiceIsraeli::pago_cuota($datos_socio["NumeroDocumento"], $Valor, $datos_pago["IDSocio"]);
                }

                if (
                    $estadoFinal == 1
                ) {
                    SIMUtil::notifica_pago_extracto($IDClubConsulta, $IDSocioConsulta, $Valor);
                }

                break;

            case "Donacion":

                //Envio mensaje de pago exitoso
                $IDClubConsulta = $datos_pago["IDClub"];
                $IDSocioConsulta = $datos_pago["IDSocio"];
                $Valor = $datos_pago["ValorPago"];
                $frm["UsuarioTrCr"] = "Donacion";
                $frm["FechaTrCr"] = date("Y-m-d H:i:S");
                $frm["Valor"] = $datos_pago["ValorPago"];
                $frm["IDClub"] = $datos_pago["IDClub"];
                $frm["IDSocio"] = $datos_pago["IDSocio"];
                $frm["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $frm["Accion"] = $datos_socio["Accion"];
                $id = $dbo->insert($frm, "Donacion", "IDDonacion");

                break;

            case "Reservas":

                $reserva = $datos_pago['IDReserva'];

                if ($pagado == "S") {
                    $EstadoT = " IDEstadoReserva = '1' , ";
                } else {
                    $condicion_no_aprobado = " and EstadoTransaccion <> 'A' ";
                }


                $actualizaReserva = "UPDATE ReservaGeneral SET " . $EstadoT . " Pagado = '$pagado', MedioPago = 'PAYWAY-$Estado',
                                    FechaTransaccion = NOW(), CodigoRespuesta = '$Estado',
                                    EstadoTransaccion = '$estadoTransaccion'
                                    WHERE IDReservaGeneral ='$reserva' " . $condicion_no_aprobado;

                $dbo->query($actualizaReserva);

                /*
                $fila_afectada=$dbo->affected($dblink);	
                if($fila_afectada<=0){
                    $correo = "jorgechirivi@gmail.com";
                    $Mensaje = "No se actualizo la reserva numero " . $reserva;
                    $Asunto = "Reserva Paga No Actualizada";
                    SIMUtil::envia_correo_general(8, $correo, $Mensaje, $Asunto);
                }
                */

                // $no_permitidas = array("'", "\"");
                // $permitidas = array(" ", " ",);
                // $sql_actua = str_replace($no_permitidas, $permitidas, $actualizaReserva);
                // $UpdatePagos = "UPDATE PagosPayWay SET Observaciones = '" . $sql_actua . " desde SONDA' WHERE IDPagosPayWay = $IDPagosPayWay";
                // $dbo->query($UpdatePagos);
                // if ($pagado == "S") {
                //     $actualizaReserva = "UPDATE ReservaGeneral SET IDEstadoReserva = '1', EstadoTransaccion = 'A' WHERE IDReservaGeneral ='$reserva'";
                //     $dbo->query($actualizaReserva);
                // }


                break;

            case "CursoTrimestral":
            case "Curso":
                $reserva = $datos_pago[IDReserva];
                if (!empty($reserva)) {
                    $query = "UPDATE CursoInscripcion
                                SET EstadoTransaccion='" . $Estado . "',
                                    FechaTransaccion=NOW(),
                                    CodigoRespuesta='" . $estado . "',
                                    MedioPago='WOMPI-$Estado'                                
                                WHERE Referencia='" . $reserva . "'";
                    $dbo->query($query);


                    if (
                        $pagado == "S"
                    ) {
                        $EstadoT = "Confirmado";
                    } else {
                        $EstadoT = "NoPagado";
                    }
                    $cambia_estado = "UPDATE CursoInscripcion
                                                    SET EstadoInscripcion ='" . $EstadoT . "',
                                                    PagoPayu = 'S',
                                                    Pagado = 'S'
                                                    WHERE Referencia='" . $reserva . "'";
                    $dbo->query($cambia_estado);
                }

                break;

            case "Evento":

                if ($pagado == "S") {
                } else {
                    $condicion_no_aprobado = " and EstadoTransaccion <> 'A' ";
                }


                $reserva = $datos_pago[IDReserva];
                $sql_evento = "UPDATE EventoRegistro
                                                    SET Pagado = '" . $pagado . "',PagoPayu='" . $pagado . "',
                                                        CodigoPago='" . $datos_pago["ReferenciaWompi"] . "',
                                                        EstadoTransaccion='" . $Estado . "',
                                                        FechaTransaccion= NOW(),
                                                        CodigoRespuesta='" . $Estado . "',
                                                        MedioPago='CREDIBANCO V API',
                                                        TipoMedioPago='CREDIBANCO V API'
                                                    WHERE IDEventoRegistro = '" . $reserva . "' " . $condicion_no_aprobado;
                $dbo->query($sql_evento);
                break;




            case "FacturasPraderaPotosi":

                require LIBDIR . "SIMWebServicePradera.inc.php";


                $NumeroFactura = $datos_pago[ref];
                $IDFactura = $datos_pago[extra1];
                $TipoPago = $DATOS["payerData"]["paymentWay"] . "|" . $DATOS["payerData"]["paymentSystem"];
                $PagoTotal =  $datos_pago["ValorPago"];

                SIMWebServicePradera::actuliza_vntas_pos_split_pgos($NumeroFactura, $IDFactura, $TipoPago,  $PagoTotal);

                break;

            case "Taloneras":
                if ($pagado == "S") {
                    $estado_talonera = ", Activo='1' ";
                } else {
                    $estado_talonera = ", Activo='0' ";
                }

                $update = "UPDATE SocioTalonera SET EstadoTransaccion = '$estadoTransaccion', Pagado = '$pagado', MedioPago = 'WOMPI-$Estado' " . $estado_talonera . " WHERE IDSocioTalonera = $datos_pago[IDReserva]";
                $dbo->query($update);

                break;

        endswitch;

        if (count($Response) > 0) :

            $DATOS['payway'] = $DATA;
            $DATOS['MiClub'] = $datos_pago;

            $respuesta["message"] = "Estado transacción: {$Estado}";
            $respuesta["success"] = $success;
            $respuesta["response"] = $DATOS;

        else :
            $respuesta["message"] = "NO HAY RESPUESTA DE LA TRANSACCIÓN, COMUNIQUESE CON EL ADMINISTRADOR";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }
    /* .... FIN PAYWAY .... */

    /* .... PAYPAL .... */

    public function respuestaPayPal($DATOS)
    {
        require(LIBDIR . "SIMWebServiceLagunita.inc.php");
        $dbo = &SIMDB::get();

        $id = $dbo->insert($DATOS, "PagosPaypal", "IDPagosPaypal");
        $ID = $dbo->lastID();

        SIMWebServiceLagunita::reportePagos($DATOS[IDSocio], $ID, "", "S");
    }
    /* .... FIN PAYPAL .... */

    /* .... EPAYCO .... */
    public function TokenEpayco($IDClub)
    {
        $dbo = SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", "IDClub = $IDClub");

        $Usuario = trim($datos_club[UsuarioEpayco]);
        $Pass = trim($datos_club[PassEpayco]);
        $PublicKey = trim($datos_club[PublicKeyEpayco]);

        $Authorization = base64_encode("$Usuario:$Pass");
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_EPAYCO . '/login/mail',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'public_key: ' . $PublicKey,
                'Authorization: Basic ' . $Authorization
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA[token];
    }

    public function BancosEpayco($IDClub)
    {
        $Token = SIMPasarelaPagos2::TokenEpayco($IDClub);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_EPAYCO . '/payment/pse/banks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $Token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);

        return $DATA;
    }

    public function PagoEpayco($DATOS)
    {
        $dbo = SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $DATOS[IDSocio]");

        $IDClub = $DATOS[extra2];
        $bank = $DATOS[Banco];
        $value = $DATOS[valor];
        $docNumber = $datos_socio[NumeroDocumento];
        $name = $datos_socio[Nombre];
        $lastName = $datos_socio[Apellido];
        $email = $datos_socio[CorreoElectronico];
        $cellPhone = $datos_socio[Celular];
        $urlResponse = "https://www.miclubapp.com/respuesta_transaccion_ePayco.php?IDClub=$IDClub";
        $urlConfirmation = "https://www.miclubapp.com/confirmacion_transaccion_ePayco.php?IDClub=$IDClub";

        // INSERTAMOS LA INFORMACIÖN DE PAGO EN LA TABLA PagosEpayco
        $SQLInsertPago = "INSERT INTO PagosEpayco (IDClub, IDReserva,IDSocio, Modulo, UrlRespuesta, UrlConfirmacion, DatosEnvio, FechaTrCr, UsuarioTrCr) 
                                VALUES ($IDClub, '$DATOS[extra1]','$DATOS[IDSocio]', '$DATOS[Modulo]', '$urlResponse', '$urlConfirmation', '" . json_encode($DATOS) . "', NOW(), 'SOCIO-$DATOS[IDSocio]')";
        $dbo->query($SQLInsertPago);
        $IDPagosEpayco = $dbo->lastID();

        $urlResponse .= "&IDPagosEpayco=$IDPagosEpayco";
        $urlConfirmation .= "&IDPagosEpayco=$IDPagosEpayco";

        $Token = SIMPasarelaPagos2::TokenEpayco($IDClub);

        $POST =
            '{
            "bank":"' . $bank . '",
            "value":"' . $value . '", 
            "docType":"CC", 
            "docNumber":"1234567", 
            "name":"' . $name . '",
            "lastName":"' . $lastName . '",
            "email":"' . $email . '", 
            "cellPhone":"' . $cellPhone . '",
            "ip":"' . self::getRealIpAddr() . '",
            "urlResponse":"' . $urlResponse . '",
            "urlConfirmation":"' . $urlConfirmation . '",
            "extra1":"' . $IDPagosEpayco . '"
        }';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_EPAYCO . '/payment/process/pse',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $Token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);

        $DATA[data][FechaTrEd] = date("Y-m-d H:i:s");
        $DATA[data][UsuarioTrEd] = 'SOCIO-' . $DATOS[IDSocio];
        $DATA[data][DatosRespuesta] = $response;

        // ACTUALIZAMOS LA TABLA
        $dbo->update($DATA[data], "PagosEpayco", "IDPagosEpayco", $IDPagosEpayco);

        return $DATA;
    }

    public function RespuestaEpayco($DATOS)
    {
        $dbo = SIMDB::get();

        $IDClub = $DATOS[IDClub];
        $IDPagosEpayco = $DATOS[IDPagosEpayco];

        $datos_pago = $dbo->fetchAll("PagosEpayco", "IDPagosEpayco = $IDPagosEpayco");
        $transactionID = $datos_pago[transactionID];

        $POST =
            '{
            "transactionID":' . $transactionID . '
        }';

        $Token = SIMPasarelaPagos2::TokenEpayco($IDClub);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_EPAYCO . '/payment/pse/transaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $Token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        $Response = json_encode($DATA);
        $success = $DATA[success];

        // ACTUALIZAMOS LA TABLA    

        $DATA[data][FechaTrEd] = date("Y-m-d H:i:s");
        $DATA[data][UsuarioTrEd] = 'Acatualizado Pasarela';

        $dbo->update($DATA[data], "PagosEpayco", "IDPagosEpayco", $IDPagosEpayco);

        $Modulo = $datos_pago[Modulo];
        $Estado = $DATA[titleResponse];

        if ($Estado == 'OK') :

            $estadoFinal = 1;
            $pagado = 'S';
            $estado = $transactionID;
            $estadoTransaccion = 'A';

        elseif ($Estado == 'PENDING') :

            $estadoFinal = 1;
            $pagado = 'S';
            $estado = $transactionID;
            $estadoTransaccion = 'A';

        else :

            $estadoFinal = 2;
            $pagado = 'N';
            $estado = $transactionID;
            $estadoTransaccion = 'R';

        endif;

        switch ($Modulo):
            case "Reservas":
                $reserva = $datos_pago[IDReserva];

                $actualizaReserva = "UPDATE ReservaGeneral SET IDEstadoReserva = $estadoFinal, Pagado = '$pagado', PagoPayu = '$pagado', MedioPago = 'ePayco-$Estado',
                                    FechaTransaccion = NOW(), CodigoRespuesta = '$estado',
                                    EstadoTransaccion = '$estadoTransaccion'
                                    WHERE IDReservaGeneral ='$reserva'";

                $dbo->query($actualizaReserva);
                break;
        endswitch;

        if (count($Response) > 0) :

            // SACAMOS DE NUEVO LOS DATOS ACTULIZADOS
            $datos_pago = $dbo->fetchAll("PagosEpayco", "IDPagosEpayco = $IDPagosEpayco");

            $DATOS[Pasarela] = $DATA;
            $DATOS[MiClub] = $datos_pago;

            $respuesta["message"] = "RESPUESA CORRECTA";
            $respuesta["success"] = $success;
            $respuesta["response"] = $DATOS;

        else :
            $respuesta["message"] = "NO HAY RESPUESTA DE LA TRANSACCIÓN, COMUNIQUESE CON EL ADMINISTRADOR";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }
    /* .... FIN EPAYCO .... */


    /* INICIO WOMPI BILLETERA*/
    public function get_indicativos_paises($IDClub)
    {
        $dbo = &SIMDB::get();

        $sql = "SELECT *  FROM PaisIndicativo  WHERE IDClub = '" . $IDClub . "'  And Activo='S'";
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            $array_datos_indicativos = array();
            while ($r = $dbo->fetchArray($qry)) {
                $configuracionPais["IDPais"] = $r["IDPaisIndicativo"];
                $configuracionPais["TextoIndicativo"] = "+" . $r["Indicativo"];
                $configuracionPais["Indicativo"] = $r["Indicativo"];
                $configuracionPais["ImagenPais"] = PAIS_ROOT . $r["ImagenPais"];

                array_push($array_datos_indicativos, $configuracionPais);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $array_datos_indicativos;
        } //End if
        else {
            $respuesta["message"] = "No hay datos paises";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else */

        return $respuesta;
    }

    public function get_wompi_user_info($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio > 0) {
            $Valor = $IDSocio;
            $Campo = "IDSocio";
        } else if ($IDUsuario > 0) {
            $Valor = $IDUsuario;
            $Campo = "IDUsuario";
        }

        $sql = "SELECT *  FROM WompiUsuario  WHERE IDClub = '" . $IDClub . "'  And $Campo='" . $Valor . "' LIMIT 1";
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $Usuario["Nombres"] = $r["Nombres"];
                $Usuario["IDPais"] =  $r["IDPais"];
                $Usuario["Indicativo"] = $r["Indicativo"];
                $Usuario["Celular"] = $r["Celular"];
                $Usuario["Correo"] = $r["Correo"];
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $Usuario;
        } //End if
        else {
            $Usuario["Nombres"] = "";
            $Usuario["IDPais"] =  "";
            $Usuario["Indicativo"] = "";
            $Usuario["Celular"] = "";
            $Usuario["Correo"] = "";

            $respuesta["message"] = "";
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        }  //end else */

        return $respuesta;
    }

    public function set_wompi_user_info($IDClub, $IDSocio, $IDUsuario, $Nombres, $Celular, $IDPais, $Indicativo, $Correo)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && ($IDSocio > 0 || $IDUsuario > 0) && !empty($Nombres) && !empty($Celular) && !empty($IDPais) && !empty($Indicativo) && !empty($Correo)) {

            if ($IDSocio > 0) {
                $Valor = $IDSocio;
                $Campo = "IDSocio";
            } else if ($IDUsuario > 0) {
                $Valor = $IDUsuario;
                $Campo = "IDUsuario";
            }

            $sql = "SELECT *  FROM WompiUsuario  WHERE IDClub = '" . $IDClub . "'  And $Campo='" . $Valor . "' LIMIT 1";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $sql_borra_usuario = $dbo->query("Delete From WompiUsuario Where   $Campo= '" . $Valor . "' AND IDClub='" . $IDClub . "'");
            }

            $sql_insertar_usuario = $dbo->query("INSERT INTO WompiUsuario(IDClub,IDSocio,IDUsuario,IDPais,Nombres,Indicativo,Celular,Correo,FechaTrCr)VALUES('$IDClub','$IDSocio', '$IDUsuario', '$IDPais','$Nombres', '$Indicativo', '$Celular', '$Correo',NOW())");
            /*   $sql_insertar_usuario = "INSERT INTO WompiUsuario(IDClub,IDSocio,IDUsuario,IDPais,Nombres,Indicativo,Celular,Correo,FechaTrCr)VAUES('$IDClub','$IDSocio', '$IDUsuario','$IDPais','$Nombres', '$Celular', '$IDPais', '$Indicativo', '$Correo',NOW())";
            echo $sql_insertar_usuario; */
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Debes llenar todos los datos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  set_transaccion_wompi($IDClub, $IDSocio, $IDUsuario, $IDModulo, $IDTransaccion, $reference)
    {

        $dbo = &SIMDB::get();

        if (($IDSocio > 0 || $IDUsuario > 0)) {



            $sql_insertar_transaccion = $dbo->query("INSERT INTO TransaccionWompi(IDClub,IDSocio,IDUsuario,IDModulo,IDTransaccion,reference,FechaTrCr)VALUES('$IDClub','$IDSocio', '$IDUsuario', '$IDModulo','$IDTransaccion', '$reference',NOW())");

            $respuesta["message"] = "Se realizo transaccion correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }


    public function  set_crear_fuente_pago_wompi($IDClub, $IDSocio, $IDUsuario, $token, $acceptance_token, $TipoPago, $NombrePago, $customer_email, $full_name, $phone_number)
    {

        $dbo = &SIMDB::get();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($token) && !empty($acceptance_token) && !empty($customer_email) && !empty($full_name) && !empty($phone_number)) {



            $sql_insertar_fuente_pago = $dbo->query("INSERT INTO FuentePagoWompi(IDClub,IDSocio,IDUsuario,Token,AcceptanceToken,TipoPago,NombrePago,CustomerEmail,FullName,PhoneNumber,FechaTrCr)VALUES('$IDClub','$IDSocio', '$IDUsuario', '$token','$acceptance_token', '$TipoPago','$NombrePago','$customer_email','$full_name','$phone_number',NOW())");
            $id_fuente_pago = $dbo->lastID("IDFuentePagoWompi");

            if ($id_fuente_pago > 0) {

                $array_datos_respuesta = array();
                $array_datos_respuesta["IDFuentePagoWompi"] = (string) $id_fuente_pago;
                $array_datos_respuesta["TipoPago"] = $TipoPago;
                $array_datos_respuesta["Nombre"] = $NombrePago;

                // $respuesta["message"] = "Se creo correctamente fuente de pago.";
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_datos_respuesta;
            } else {
                $respuesta["message"] = "No se pudo registrar fuente de pago.";
                //$respuesta["message"] = "";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  set_crear_transaccion_wompi_fuente_pago($IDClub, $IDSocio, $IDUsuario, $IDFuentePagoWompi, $amount_in_cents, $currency, $reference, $installments)
    {

        $dbo = &SIMDB::get();

        if (($IDSocio > 0 || $IDUsuario > 0)) {



            $sql_insertar_fuente_pago_transaccion = $dbo->query("INSERT INTO TransaccionWompiFuentePago(IDClub,IDSocio,IDUsuario,IDFuentePagoWompi,AmountInCents,Currency,Reference,Installments,FechaTrCr)VALUES('$IDClub','$IDSocio', '$IDUsuario', '$IDFuentePagoWompi','$amount_in_cents', '$currency','$reference','$installments',NOW())");
            $id_fuente_pago_transaccion = $dbo->lastID("IDTransaccionWompiFuentePago");

            if ($id_fuente_pago_transaccion > 0) {

                $array_datos_respuesta = array();
                $array_datos_respuesta_transaccion["IDTransaccion"] = (string) $id_fuente_pago_transaccion;


                //$respuesta["message"] = "Se realizo transaccion correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_datos_respuesta_transaccion;
            } else {
                $respuesta["message"] = "No se pudo registrar fuente de pago.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_fuentes_pago_wompi($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio > 0) {
            $Valor = $IDSocio;
            $Campo = "IDSocio";
        } else if ($IDUsuario > 0) {
            $Valor = $IDUsuario;
            $Campo = "IDUsuario";
        }

        $sql = "SELECT *  FROM FuentePagoWompi  WHERE IDClub = '" . $IDClub . "'  And $Campo='" . $Valor . "'";
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            $array_datos = array();
            while ($r = $dbo->fetchArray($qry)) {
                $FuentesDePago["IDFuentePagoWompi"] = $r["IDFuentePagoWompi"];
                $FuentesDePago["TipoPago"] =  $r["TipoPago"];
                $FuentesDePago["Nombre"] = $r["NombrePago"];

                array_push($array_datos, $FuentesDePago);
            } //ednw hile
            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = $array_datos;
        } //End if
        else {
            $FuentesDePago["IDFuentePagoWompi"] = "";
            $FuentesDePago["TipoPago"] =  "";
            $FuentesDePago["Nombre"] = "";

            $respuesta["message"] = "No hay fuentes de pago.";
            $respuesta["success"] = false;
            $respuesta["response"] = $FuentesDePago;
        } //end else */

        return $respuesta;
    }


    public function set_eliminar_fuente_pago_wompi($IDClub, $IDSocio, $IDUsuario, $IDFuentePagoWompi)
    {

        $dbo = &SIMDB::get();

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDClub) && !empty($IDFuentePagoWompi)) {


            if ($IDSocio > 0) {
                $Valor = $IDSocio;
                $Campo = "IDSocio";
            } else if ($IDUsuario > 0) {
                $Valor = $IDUsuario;
                $Campo = "IDUsuario";
            }

            $sql_borrar_fuente_de_pago = $dbo->query("DELETE FROM FuentePagoWompi WHERE IDClub='" . $IDClub . "' AND $Campo='" . $Valor . "' AND IDFuentePagoWompi='" . $IDFuentePagoWompi . "'");

            //buscar la fuente de pago para saber si se elimino
            $sql_buscar_fuente_de_pago = "SELECT IDFuentePagoWompi  FROM FuentePagoWompi  WHERE IDClub = '" . $IDClub . "'  And $Campo='" . $Valor . "'  AND IDFuentePagoWompi='" . $IDFuentePagoWompi . "'";
            $qry = $dbo->query($sql_buscar_fuente_de_pago);
            if ($dbo->rows($qry) == 0) {

                $respuesta["message"] = "Fuente de pago se eliminó correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = '';
            } else {
                $respuesta["message"] = "No se pudo eliminar fuente de pago.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }
} //end class
