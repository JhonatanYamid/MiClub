<?php
class SIMWebServicePago {

	function get_configuracion_pago($IDClub,$IDSocio,$IDUsuario){

			$dbo =& SIMDB::get();
			$response = array();


			$sql = "SELECT ComercioPago,CodigoUnicoPago,TerminalPago,UsuarioPlataformaPago,ClavePago,IDComercioPago,IDAcquirerPago,ArchivoTerminosPago
							FROM Club
							WHERE IDClub = '".$IDClub."' ";
			$qry = $dbo->query( $sql );
			if( $dbo->rows( $qry ) > 0 )
			{
				$message = $dbo->rows( $qry ) . " Encontrados";
				while( $r = $dbo->fetchArray( $qry ) )
				{
						$configuracion["IDClub"] = $IDClub;
						$configuracion["Comercio"] = $r["ComercioPago"];
						$configuracion["CodigoUnico"] = $r["CodigoUnicoPago"];
						$configuracion["Terminal"] = $r["TerminalPago"];
						$configuracion["UsuarioPlataforma"] = $r["UsuarioPlataformaPago"];
						$configuracion["Clave"] = $r["ClavePago"];
						$configuracion["IDComercio"] = $r["IDComercioPago"];
						$configuracion["IDAcquirer"] = $r["IDAcquirerPago"];
						$configuracion[ "ArchivoTerminosPago" ] = CLUB_ROOT . $r[ "ArchivoTerminosPago" ];
						$configuracion[ "NumCuotasMaximo" ] = 24;

						array_push($response, $configuracion);

				}//ednw hile
					$respuesta["message"] = $message;
					$respuesta["success"] = true;
					$respuesta["response"] = $response;
			}//End if
			else
			{
					$respuesta["message"] = "Pasa la pagina no está activo";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
			}//end else

			return $respuesta;

		}// fin function



		function set_transaccion_pago($IDClub,$IDModulo,$PurchaseCode,$IDObjeto,$Aprobada,$ResultadoTranssacion){
			$dbo =& SIMDB::get();
			if( !empty( $IDClub )  && (!empty( $IDModulo ) || !empty( $PurchaseCode )  ) && !empty( $IDObjeto) && !empty( $Aprobada ) ){
							if($Aprobada=="S"){

								if($IDClub==28 && $IDModulo == 33)
									$IDModulo=2;

								switch($IDModulo){
									case "2": //Reservas
										$Modulo="Reservas";
										$query="UPDATE ReservaGeneral
												SET EstadoTransaccion='Aprobada',
													FechaTransaccion='" . date("Y-m-d H:i:s")."',
													CodigoRespuesta='" . $PurchaseCode."',
													Pagado ='S',
													PagoPayu = 'S'
												WHERE IDReservaGeneral='" . $IDObjeto."'";
										$sql_actualizar=$dbo->query($query);

									break;
									case "43": //Hotel
										$Modulo="Hotel";
										$cambia_estado="UPDATE ReservaHotel
															 SET Pagado ='S',
															 PagoPayu = 'S'
															 WHERE IDReserva='" . $IDObjeto."'";
										$result_cambia_estado=$dbo->query($cambia_estado);

									break;
									case "4": //Eventos
										$Modulo="Eventos";
										$cambia_estado="UPDATE EventoRegistro
															 SET Pagado ='S',
															 PagoPayu = 'S'
															 WHERE IDEventoRegistro='" . $IDObjeto."'";
										$result_cambia_estado=$dbo->query($cambia_estado);
									break;
									case "33": //Domicilios
										$Modulo="Domicilios";
										$Domicilios="S";
										$Version="";
									break;
									case "98": //Domicilios 2
										$Modulo="Domicilios2";
										$Domicilios="S";
										$Version="2";
									break;
									case "112": //Domicilios 3
										$Modulo="Domicilios3";
										$Domicilios="S";
										$Version="3";
									break;
									case "113": //Domicilios 4
										$Modulo="Domicilios4";
										$Domicilios="S";
										$Version="4";
									break;

								}

								if($Domicilios=="S"){
									$sql_pedido="UPDATE Domicilio".$Version."
															SET Pagado = 'S',PagoPayu='S',
															CodigoPago='".$PurchaseCode."',
															EstadoTransaccion='A',
															FechaTransaccion='".date("Y-m-d H:i:s")."'
															WHERE IDDomicilio = '".$IDObjeto."' ";
									$dbo->query($sql_pedido);
								}



							}


							$sql_cred="INSERT INTO PagoCredibanco (IDClub,Modulo,NumeroTransaccion,errorMessage,xmlResponse,reserved14,FechaTrCr)
												 VALUES ('".$IDClub."','".$Modulo."','".$PurchaseCode."','".$Aprobada."','".$ResultadoTranssacion."','".$IDObjeto."',NOW()) ";
							$dbo->query($sql_cred);


							$respuesta["message"] = "Registrado";
							$respuesta["success"] = true;
							$respuesta["response"] = $response;
			}
			else{
				$respuesta["message"] = "PG1. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}

			return $respuesta;
		}


} //end class
?>
