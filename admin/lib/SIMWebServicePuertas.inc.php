<?php
class SIMWebServicePuertas
{

	function get_puertas($IDClub, $Tag = "")
	{

		$dbo = &SIMDB::get();

		// Tag
		if (!empty($Tag)) :
			$array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
		endif;


		if (count($array_condiciones) > 0) :
			$condiciones = implode(" and ", $array_condiciones);
			$condiciones_puerta = " and " . $condiciones;
		endif;



		$response = array();
		$response_lista_producto = array();
		$sql = "SELECT * FROM PuertaUbicacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_puerta . " ORDER BY Nombre ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {
				$ubicacion_puerta["IDClub"] = $r["IDClub"];
				$ubicacion_puerta["IDPuertaUbicacion"] = $r["IDPuertaUbicacion"];
				$ubicacion_puerta["Nombre"] = utf8_encode($r["Nombre"]);
				$ubicacion_puerta["Descripcion"] = utf8_encode($r["Descripcion"]);
				//Busco las puertas de esa ubicacion
				$response_puertas = array();
				$sql_puerta = "Select * From Puerta Where IDPuertaUbicacion = '" . $r["IDPuertaUbicacion"] . "' and Publicar = 'S' ";
				$result_puerta = $dbo->query($sql_puerta);
				while ($row_puerta = $dbo->fetchArray($result_puerta)) :

					$puerta["IDPuerta"] = $row_puerta["IDPuerta"];
					$puerta["IDPuertaUbicacion"] = $row_puerta["IDPuertaUbicacion"];
					$puerta["Descripcion"] = utf8_encode($row_puerta["Descripcion"]);
					$puerta["CodigoPuerta"] = $row_puerta["CodigoPuerta"];
					$puerta["CodigoDispositivo"] = utf8_encode($row_puerta["CodigoDispositivo"]);
					$puerta["PinEquipo"] = $row_puerta["PinEquipo"];
					$puerta["TiempoApertura"] = $row_puerta["TiempoApertura"];
					$puerta["TiempoEspera"] = $row_puerta["TiempoEspera"];
					$puerta["IdentificadorCliente"] = $row_puerta["IdentificadorCliente"];
					$puerta["Publicar"] = $row_puerta["Publicar"];
					array_push($response_puertas, $puerta);

				endwhile;

				$ubicacion_puerta["Puertas"] = $response_puertas;
				array_push($response, $ubicacion_puerta);
			} //ednw hile

			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function



	function set_abrir_puerta($IDClub, $IDSocio, $IDPuerta)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDSocio) && !empty($IDPuerta)) {

			$datos_puerta = $dbo->fetchAll("Puerta", " IDPuerta = '" . $IDPuerta . "' ", "array");
			if ($datos_puerta["IDPuerta"] > 0) {

				$sql_log_apertura = $dbo->query("INSERT INTO LogAperturaPuerta(IDClub,IDPuerta,IDSocio,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDClub . "', '" . $IDPuerta . "', '" . $IDSocio . "','Socio App', NOW() ) ");



				if ($IDClub == 75) { //Entrelomas especial

					//Verifico si tiene reserva para poder abrirla

					//$resp= self::abrir_puerta_nextonia($datos_puerta,$IDSocio);

					$Hoy = date("Y-m-d");
					$HoyHora = date("Y-m-d H:i:s");

					$FechaHoraActual = date("Y-m-d H:i:s");
					$NuevaFechaHora = strtotime('-90 minute', strtotime($FechaHoraActual));
					$HoraValidar = date('H:i:s', $NuevaFechaHora);


					$datos_puerta = $dbo->fetchAll("Puerta", " IDPuerta = '" . $IDPuerta . "' ", "array");
					if ($datos_puerta["ValidarReserva"] == "S" && (int)$datos_puerta["IDServicio"] > 0) {

						if ($IDPuerta == 4) { //Especial Entrelomas esta puerta abre varios servicios
							$IDServicioValidar = "9188,9189,9240,9241,18190,9200";
						} else {
							$IDServicioValidar = $datos_puerta["IDServicio"];
						}

						$sql_reserva = "SELECT IDReservaGeneral,Hora
																	FROM ReservaGeneral
																	WHERE IDServicio in (" . $IDServicioValidar . ") and Fecha = '" . $Hoy . "' and Hora >='" . $HoraValidar . "' and IDEstadoReserva = 1 and IDSocio = '" . $IDSocio . "' ORDER BY Hora ASC LIMIT 1 ";
						$r_reserva = $dbo->query($sql_reserva);
						$row_reserva = $dbo->fetchArray($r_reserva);
						if ((int)$row_reserva["IDReservaGeneral"] > 0) {
							//echo "Si tiene reserva";
							//Verifico que sea x min antes
							$HoraReserva = $row_reserva["Hora"];
							$FechaHoraReserva = date('Y-m-d ' . $HoraReserva);
							$datos_puerta["MinutoAnterioridad"];
							$NuevaFecha = strtotime('-' . $datos_puerta["MinutoAnterioridad"] . ' minute', strtotime($FechaHoraReserva));
							$NuevaFecha = date('Y-m-d H:i:s', $NuevaFecha);
							if ($datos_puerta["MinutoAnterioridad"] >= 0 && strtotime($HoyHora) >= strtotime($NuevaFecha)) {
								$resp = self::abrir_puerta_nextonia($datos_puerta, $IDSocio);
							} else {
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Solopuedesabrirlapuerta', LANG) . $datos_puerta["MinutoAnterioridad"] . SIMUtil::get_traduccion('', '', 'minutosantesdelahoradelareserva', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;
								return $respuesta;
							}
						} else {
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Parapoderabrirlapuertadebestenerunareserva', LANG) . $sql_reserva;
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;
							return $respuesta;
						}
					} else {
						$resp = self::abrir_puerta_nextonia($datos_puerta, $IDSocio);
					}




					$array_resp = json_decode($resp);
					if ($array_resp->status != "A200") {
						$err = "S";
						switch ($array_resp->status) {
							case "A400":
								//$mensaje_error="Faltan parametros!";
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A410":
								//$mensaje_error="Error procesando!";
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A413":
								//$mensaje_error="Dispositivo no está en linea";
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A414":
								//$mensaje_error="Se perdio la conexion";
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A415":
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A401":
								//$mensaje_error="No autorizado";
								$mensaje_error = SIMUtil::get_traduccion('', '', 'Enelmomentoeldispositivonoseencuentraconectado,porfavoracércatealarecepción', LANG);
								break;
							case "A500":
								//$mensaje_error="Problema interno";
								$mensaje_error = "En el momento el dispositivo no se encuentra conectado, por favor acércate a la recepción";
								break;
							case "A501":
								//$mensaje_error="No implementado";
								$mensaje_error = "En el momento el dispositivo no se encuentra conectado, por favor acércate a la recepción";
								break;
						}
					} else {
						$array_respuesta->code = 0;
					}
				} else {

					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_PORT => "18083",
						CURLOPT_URL => "http://broker.miclubapp.com:18083/api/v4/mqtt/publish",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => "{\"qos\":1,\"retain\": false, \"topic\":\"" . $datos_puerta["IDClub"] . "/" . $datos_puerta["CodigoDispositivo"] . "/commando\", \"payload\":\"{\\\"cmd\\\":\\\"activar\\\",\\\"pin\\\":\\\"" . $datos_puerta["PinEquipo"] . "\\\",\\\"tactivo\\\":\\\"" . $datos_puerta["TiempoApertura"] . "\\\",\\\"tespera\\\":\\\"" . $datos_puerta["TiempoEspera"] . "\\\"}\" , \"client_id\": \"" . $datos_puerta["IdentificadorCliente"] . "\"}",
						CURLOPT_HTTPHEADER => array(
							"Accept: application/json",
							"Authorization: Basic bWFwcHM6MTIzNDU2Nw==",
							"Content-Type: application/json"
						),
					));

					$response = curl_exec($curl);

					$err = curl_error($curl);
					curl_close($curl);
					$array_respuesta = json_decode($response);
					$mensaje_error = SIMUtil::get_traduccion('', '', 'Problemaalabrirlapuerta', LANG);
				}


				if ($err) {
					$respuesta["message"] = $mensaje_error;
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
					return $respuesta;
				}


				if ($array_respuesta->code == 0) {
					$respuesta["message"] = SIMUtil::get_traduccion('', '', 'PuertaAbierta', LANG);
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
				} else {
					$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Problemaalabrirlapuerta', LANG);
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
					return $respuesta;
				}
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Lapuertanoexiste', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;
			}
		} else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'P1.Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}


	function abrir_puerta_nextonia($datos, $IDSocio)
	{

		$QR = $datos["CodigoDispositivo"];
		$User = $IDSocio;
		$businessId = $datos["IdentificadorCliente"];
		$verApp = "";
		$Code = 'mBuyuMPG345rIzh5Yfl%2Fuz%2FM1T8KfSSWVdXzlmMZxIlFnRq3whqz4w%3D%3D';


		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://sensinactive.azurewebsites.net/api/ssaopendoor?code=' . $Code,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{"qr": "' . $QR . '", "user":"' . $User . '", "businessId": "' . $businessId . '", "verApp":"' . $verApp . '"}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;
		return $response;
	}
} //end class
