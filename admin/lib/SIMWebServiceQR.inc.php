<?php
class SIMWebServiceQR
{

	function set_qr($IDClub, $IDSocio, $IDUsuario, $Codigo)
	{

		$dbo = &SIMDB::get();

		if ($IDClub == 75 || $IDClub == 800) {
			require(LIBDIR . "SIMWebServicePuertas.inc.php");
			$datos_puerta = $dbo->fetchAll("Puerta", " CodigoDispositivo = '" . $Codigo . "' LIMIT 1", "array");
			$IDSocio = $IDSocio;
			$IDUsuario = $IDUsuario;
			if ((int)$datos_puerta["IDPuerta"] > 0)
				$respuesta = SIMWebServicePuertas::set_abrir_puerta($IDClub, $IDSocio, $datos_puerta["IDPuerta"]);
			else {
				$respuesta["message"] = "QR no encontrado " . $Codigo;
				$respuesta["success"] = true;
				$respuesta["response"] = "ok";
			}
		} elseif ($IDClub == 8) {


			if (isset($Codigo) && $Codigo) {

				//hago la consulta para saber si ya se registro en el dia un desayuno
				$config = "SELECT * FROM ConfiguracionConsumosTalonera WHERE Publicar = 'S' ORDER BY IDConfiguracionConsumosTalonera DESC LIMIT 1";
				$queryconfig = $dbo->query($config);
				$datosconfig = $dbo->fetchArray($queryconfig);
				date_default_timezone_set('America/Bogota');

				$tipoComida = "Otro";
				if (date('H:i:s') <= $datosconfig["HoraFinDesayuno"] && date('H:i:s') >= $datosconfig["HoraInicioDesayuno"]) {
					$tipoComida = 'Desayuno';
				} elseif (date('H:i:s') >= $datosconfig["HoraInicioAlmuerzo"] && date('H:i:s') <= $datosconfig["HoraFinAlmuerzo"]) {
					$tipoComida = 'Almuerzo';
				} elseif (date('H:i:s') >= $datosconfig["HoraInicioCena"] && date('H:i:s') < $datosconfig["HoraFinCena"]) {
					$tipoComida = 'Cena';
				} else {
					$tipoComida = 'No permitido';
				}

				if ($tipoComida <> 'No permitido') {

					$Cedula = $Codigo;
					$sql = "SELECT * FROM TiqueteraFuncionarios WHERE NumeroDocumento = '$Cedula' ";
					$query = $dbo->query($sql);
					$datos = $dbo->fetchArray($query);

					if ($datos) {
						if ((int)$datos['CantidadEntradas'] <> 0) {
							if ($datos[$tipoComida] == "1") {


								$datos['TipoConsumo'] = $tipoComida;
								$datos['FechaConsumo'] = date('Y-m-d');
								$datos['HoraConsumo'] = date('h:i:s');
								$id = $dbo->insert($datos, 'LogTiqueteraFuncionarios', $key);

								$Nombre = $datos["Nombre"];
								$CantidadEntradas = $datos["CantidadEntradas"];
								$dbo->query("UPDATE TiqueteraFuncionarios SET CantidadEntradas = " . (intval($CantidadEntradas) - 1) . " WHERE NumeroDocumento = " . $Cedula . " LIMIT 1 ;");


								$respuesta["message"] = "Registro valido para " . $tipoComida . ", quedan " . $datos['CantidadEntradas'] . " tickets disponibles";
								$respuesta["success"] = true;
								$respuesta["response"] = NULL;
							} else {
								$label = "LabelError" . $tipoComida;
								$respuesta["message"] = $datosconfig[$label];
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;
							}
						} else {
							$respuesta["message"] = "No hay entradas suficientes";
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;
						}
					} else {
						$respuesta["message"] = "No se ha encontrado el documento";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}
				} else {
					$respuesta["message"] = "En este momento no se pueden realizar consumos";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
			} else {
				$respuesta["message"] = "No se pudo leer el documento";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		} elseif ((int)$IDClub > 0) {

			if ($IDClub == 125) //Uruguay
				date_default_timezone_set('America/Montevideo');


			//Medellin solo valida que sea en la misma fecha	 	
			if ($IDClub == 20) {
				$condicion_hora = "";
			} else {
				$condicion_hora = " and Hora >='" . $HoraValidar . "' ";
			}

			$MinuntosAntes = 5;

			$FechaHoraActual = date("Y-m-d H:i:s");
			$NuevaFechaHora = strtotime('-60 minute', strtotime($FechaHoraActual));
			$HoraValidar = date('H:i:s', $NuevaFechaHora);
			//Para uruguay reviso si teiene reservas y marco la reserva como cumplida
			$array_codigo = explode("|", $Codigo);
			$Hoy = date("Y-m-d");
			if (strtolower($array_codigo[0]) == "servicio" && !empty($array_codigo[1]) && !empty($array_codigo[2])) {
				$IDServicioValidar = $array_codigo[1];


				//Para medellin ahora se valida solo que sea mayor de edad
				if ($IDClub == 20) {
					$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'", "array");
					$fecha_nacimiento = $datos_socio["FechaNacimiento"];
					$dia_actual = date("Y-m-d");
					$edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
					$EdadSocio = $edad_diff->format('%y');
					if ($EdadSocio <= 13) {
						$respuesta["message"] = "Lo sentimos, no tienen la edad requerida!";
						$respuesta["success"] = false;
						$respuesta["response"] = null;
						return $respuesta;
					} else {
						//realizo el acceso como porteria gym
						$sql_inserta_historial = $dbo->query("INSERT INTO LogAcceso (IDInvitacion, IDClub, Tipo, Entrada, Mecanismo, FechaIngreso,FechaTrCr, IDUsuario) 
																 Values ('" . $IDSocio . "','" . $IDClub . "', 'Socio','S','GYM','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "','19142')");
						$respuesta["message"] = "Ingreso correcto!";
						$respuesta["success"] = true;
						$respuesta["response"] = null;
						return $respuesta;
					}
				}

				$sql_reserva = "	SELECT IDReservaGeneral,Hora
									FROM ReservaGeneral
									WHERE IDServicio in (" . $IDServicioValidar . ") and Fecha = '" . $Hoy . "' " . $condicion_hora . " and IDEstadoReserva = 1 and IDSocio = '" . $IDSocio . "' ORDER BY Hora ASC LIMIT 1";
				$r_reserva = $dbo->query($sql_reserva);
				$row_reserva = $dbo->fetchArray($r_reserva);
				if ((int)$row_reserva["IDReservaGeneral"] > 0) {
					//echo "Si tiene reserva";
					//Verifico que sea x min antes
					$HoraReserva = $row_reserva["Hora"];
					$FechaHoraReserva = date('Y-m-d ' . $HoraReserva);
					$NuevaFecha = strtotime('-' . $MinuntosAntes . ' minute', strtotime($FechaHoraReserva));
					$NuevaFecha = date('Y-m-d H:i:s', $NuevaFecha);
					$IDUsuario = 9999999;
					if (strtotime($FechaHoraActual) >= strtotime($NuevaFecha) || $IDClub == 20) {
						$respuesta = SIMWebServiceApp::set_reserva_cumplida($IDClub, $IDSocio, $IDUsuario, $row_reserva["IDReservaGeneral"], "S", "S", "AutoCheckin", "");
						$respuesta["message"] = "Reserva marcada como cumplida correctamente";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;
					} else {
						$respuesta["message"] = "Solo puedes hacer checkin " . $MinuntosAntes . " minutos antes de la hora de la reserva" . $NuevaFecha;
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}
				} else {
					$respuesta["message"] = "No se pudo realizar el checkin no se encontró una reserva";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
			} else {
				$respuesta["message"] = "QR invalido: " . $Codigo;
				$respuesta["success"] = true;
				$respuesta["response"] = "ok";
			}
		} else {
			$respuesta["message"] = "QR Leido con exito: " . $Codigo;
			$respuesta["success"] = true;
			$respuesta["response"] = "ok";
		}



		return $respuesta;
	}
}//end class
