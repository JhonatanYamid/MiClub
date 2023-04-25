<?php
class SIMWebServiceVial
{

	public function get_configuracion_seguridad_vial($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();

		$response = array();
		$response_encuesta = array();

		if (!empty($IDSocio))
			$condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
		elseif (!empty($IDUsuario))
			$condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";

		$sql = "SELECT * FROM ConfiguracionEncuestaVial WHERE Activo = 'S' and IDClub = '" . $IDClub . "' Limit 1 ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {


				$configuracion["LabelConfirmacionEnvioCarro"] = $r["LabelConfirmacionEnvioCarro"];
				$configuracion["LabelConfirmacionEnvio"] = $r["LabelConfirmacionEnvio"];
				$configuracion["LabelBotonRecargar"] = $r["LabelBotonRecargar"];
				$configuracion["PermiteRegistrarCarro"] = $r["PermiteRegistrarCarro"];
				$configuracion["LabelRegistrarCarro"] = $r["LabelRegistrarCarro"];
				$configuracion["LabelInactivarCarro"] = $r["LabelInactivarCarro"];
				$configuracion["LabelActivarCarro"] = $r["LabelActivarCarro"];
				$configuracion["PermiteInactivarActivarCarro"] = $r["PermiteInactivarActivarCarro"];
				$configuracion["MostrarBotonHistorial"] = $r["MostrarBotonHistorial"];
				//Pregunta
				$pregunta = array();
				$response_pregunta = array();
				$sql_pregunta = "Select * From PreguntaVialCarro Where IDClub = '" . $IDClub . "' and Publicar = 'S' Order by Orden";
				$r_pregunta = $dbo->query($sql_pregunta);
				while ($row_pregunta = $dbo->FetchArray($r_pregunta)) :
					$pregunta["IDPregunta"] = $row_pregunta["IDPreguntaVialCarro"];
					$pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
					$pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
					$pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
					$pregunta["Valores"] = $row_pregunta["Valores"];
					$pregunta["Orden"] = $row_pregunta["Orden"];
					if ($row_pregunta["TipoCampo"] == "imagen") {
						$pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaVial"];
					}
					array_push($response_pregunta, $pregunta);
				endwhile;

				$configuracion["FormularioCarro"] = $response_pregunta;
				array_push($response, $configuracion);
			}

			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $configuracion;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else


		return $respuesta;
	} // fin function

	public function get_seguridad_vial_ant($IDClub, $IDSocio, $IDUsuario, $IDCarro = "")
	{
		$dbo = &SIMDB::get();

		$response = array();
		$response_encuesta = array();

		if (!empty($IDSocio))
			$condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
		elseif (!empty($IDUsuario))
			$condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";

		$sql = "SELECT * FROM ConfiguracionEncuestaVial WHERE Activo = 'S' and IDClub = '" . $IDClub . "' Limit 1 ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {

				$mostrar_encuesta = 1;
				if ($mostrar_encuesta == 1) {
					$configuracion["LabelConfirmacionEnvio"] = $r["LabelConfirmacionEnvio"];
					$configuracion["LabelBotonRecargar"] = $r["LabelBotonRecargar"];

					$sql_enc = "SELECT * FROM EncuestaVial WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ASC ";
					$qry_enc = $dbo->query($sql_enc);
					if ($dbo->rows($qry_enc) > 0) {
						$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
						$contador_encuesta = 1;
						while ($r_enc = $dbo->fetchArray($qry_enc)) {

							if ($contador_encuesta == 1) {
								//verifico si el socio ya constesto este formulario
								$sql_ultima_encuesta = "SELECT FechaTrCr FROM EncuestaRespuestaVial WHERE IDSocio = '" . $IDSocio . "' and IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and IDEncuestaVialCarro = '" . $IDCarro . "' ORDER BY FechaTrCr Desc limit 1 ";
								$qr_ultima_encuesta = $dbo->query($sql_ultima_encuesta);
								$row_ultima_encuesta = $dbo->assoc($qr_ultima_encuesta);
								$FechaTrCr = $row_ultima_encuesta['FechaTrCr'];

								$sql_resp = "SELECT EncuestaRespuestaVial.IDEncuestaVialRespuestaVial, EncuestaRespuestaVial.IDPreguntaVial, PreguntaVial.TipoCampo, EncuestaRespuestaVial.Valor From EncuestaRespuestaVial, PreguntaVial Where EncuestaRespuestaVial.IDPreguntaVial=PreguntaVial.IDPreguntaVial AND IDSocio = '" . $IDSocio . "' and EncuestaRespuestaVial.IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and IDEncuestaVialCarro = '" . $IDCarro . "' and EncuestaRespuestaVial.FechaTrCr = '" . $FechaTrCr . "'";

								if ($r_enc["UnaporSocio"] == 'S') {
									$FechaHoy = date('Y-m-d');
									$contFechasVencidas = 0;
									$r_resp = $dbo->query($sql_resp);
									if ($dbo->rows($r_resp) > 0) {
										while ($result = $dbo->fetchArray($r_resp)) {
											if ($result['TipoCampo'] == 'date') {
												if ($result['Valor'] < $FechaHoy) {
													SIMUtil::notificar_fecha_vencida_Encuesta_Vial($IDClub, $IDSocio, $IDUsuario, $result['IDPreguntaVial'], $result['Valor'], $FechaTrCr);
													$contFechasVencidas++;
												}
											}
										}
										if ($contFechasVencidas > 0) {
											$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
										}
									} else {
										$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
									}
								} else {
									$r_resp = $dbo->query($sql_resp);
									if ($dbo->rows($r_resp) <= 0) {
										$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
									}
								}
							} else {
								$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
							}

							$mostrar_encuesta = 1;
							if ($mostrar_encuesta == 1) {
								$encuesta["IDClub"] = $r_enc["IDClub"];
								$encuesta["IDFormulario"] = $r_enc["IDEncuestaVial"];
								$encuesta["Nombre"] = $r_enc["Nombre"];
								$encuesta["Descripcion"] = $r_enc["Descripcion"];

								//Pregunta
								$pregunta = array();
								$response_pregunta = array();
								$sql_respuesta = "Select * From PreguntaVial Where IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and Publicar = 'S' Order by Orden";
								$r_encuesta = $dbo->query($sql_respuesta);
								while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
									$pregunta["IDPreguntaFormulario"] = $row_pregunta["IDPreguntaVial"];
									$pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
									$pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
									$pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
									//Consulto los valores
									$sql_opciones = "SELECT * FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $row_pregunta["IDPreguntaVial"] . "' order by Orden";
									$r_opciones = $dbo->query($sql_opciones);
									$opciones_respuesta = array();
									$response_valores = array();
									while ($row_opciones = $dbo->fetchArray($r_opciones)) {
										$opciones_respuesta["IDPreguntaFormulario"] = $row_opciones["IDPreguntaVial"];
										$opciones_respuesta["IDPreguntaOpcionesRespuesta"] = $row_opciones["IDPreguntaVialOpcionesRespuesta"];
										$opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
										$opciones_respuesta["Terminar"] = $row_opciones["Terminar"];
										$opciones_respuesta["Peso"] = $row_opciones["Peso"];
										array_push($response_valores, $opciones_respuesta);
									}
									$pregunta["Valores"] = $response_valores;

									$pregunta["Orden"] = $row_pregunta["Orden"];
									if ($row_pregunta["TipoCampo"] == "imagen") {
										$pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaVial"];
									}
									array_push($response_pregunta, $pregunta);
								endwhile;

								$encuesta["Preguntas"] = $response_pregunta;
								array_push($response_encuesta, $encuesta);
							}
							$contador_encuesta++;
						} //ednw hile
						// var_dump($array_encuestas_activas);

						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;
					} //End if
					$configuracion["Formularios"] = $response_encuesta;
					$configuracion["FormulariosActivos"] = $array_encuestas_activas;
					//array_push($response, $configuracion);

				}
			} //ednw hile


			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $configuracion;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else


		return $respuesta;
	} // fin function


	public function get_seguridad_vial($IDClub, $IDSocio, $IDUsuario, $IDCarro = "")
	{
		$dbo = &SIMDB::get();


		$response = array();
		$response_encuesta = array();

		if (!empty($IDSocio))
			$condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
		elseif (!empty($IDUsuario))
			$condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";

		$sql = "SELECT * FROM ConfiguracionEncuestaVial WHERE Activo = 'S' and IDClub = '" . $IDClub . "' Limit 1 ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {

				$mostrar_encuesta = 1;
				if ($mostrar_encuesta == 1) {

					$sql_enc = "SELECT * FROM EncuestaVial WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ASC ";
					$qry_enc = $dbo->query($sql_enc);
					if ($dbo->rows($qry_enc) > 0) {
						$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
						$contador_encuesta = 1;
						while ($r_enc = $dbo->fetchArray($qry_enc)) {

							if ($contador_encuesta == 1) {
								//verifico si el socio ya constesto este formulario
								$sql_ultima_encuesta = "SELECT FechaTrCr FROM EncuestaRespuestaVial WHERE IDSocio = '" . $IDSocio . "' and IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and IDEncuestaVialCarro = '" . $IDCarro . "' ORDER BY FechaTrCr Desc limit 1 ";
								$qr_ultima_encuesta = $dbo->query($sql_ultima_encuesta);
								$row_ultima_encuesta = $dbo->assoc($qr_ultima_encuesta);
								$FechaTrCr = $row_ultima_encuesta['FechaTrCr'];

								$sql_resp = "SELECT EncuestaRespuestaVial.IDEncuestaVialRespuestaVial, EncuestaRespuestaVial.IDPreguntaVial, PreguntaVial.TipoCampo, EncuestaRespuestaVial.Valor From EncuestaRespuestaVial, PreguntaVial Where EncuestaRespuestaVial.IDPreguntaVial=PreguntaVial.IDPreguntaVial AND IDSocio = '" . $IDSocio . "' and EncuestaRespuestaVial.IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and IDEncuestaVialCarro = '" . $IDCarro . "' and EncuestaRespuestaVial.FechaTrCr = '" . $FechaTrCr . "'";
								if ($r_enc["UnaporSocio"] == 'S') {
									$FechaHoy = date('Y-m-d');
									$contFechasVencidas = 0;
									$r_resp = $dbo->query($sql_resp);
									if ($dbo->rows($r_resp) > 0) {
										while ($result = $dbo->fetchArray($r_resp)) {
											if ($result['TipoCampo'] == 'date') {
												if ($result['Valor'] < $FechaHoy) {
													SIMUtil::notificar_fecha_vencida_Encuesta_Vial($IDClub, $IDSocio, $IDUsuario, $result['IDPreguntaVial'], $result['Valor'], $FechaTrCr);
													$contFechasVencidas++;
												}
											}
										}
										if ($contFechasVencidas > 0) {
											$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
										}
									} else {
										$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
									}
								} else {
									$r_resp = $dbo->query($sql_resp);
									// if ($dbo->rows($r_resp) <= 0) {
									$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
									// }
								}
							} else {
								$array_encuestas_activas[] = $r_enc["IDEncuestaVial"];
							}

							$mostrar_encuesta = 1;
							if ($mostrar_encuesta == 1) {
								$encuesta["IDClub"] = $r_enc["IDClub"];
								$encuesta["IDFormulario"] = $r_enc["IDEncuestaVial"];
								$encuesta["Nombre"] = $r_enc["Nombre"];
								$encuesta["Descripcion"] = $r_enc["Descripcion"];

								//Pregunta
								$pregunta = array();
								$response_pregunta = array();
								$sql_respuesta = "Select * From PreguntaVial Where IDEncuestaVial = '" . $r_enc["IDEncuestaVial"] . "' and Publicar = 'S' Order by Orden";
								$r_encuesta = $dbo->query($sql_respuesta);
								while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
									$pregunta["IDPreguntaFormulario"] = $row_pregunta["IDPreguntaVial"];
									$pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
									$pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
									$pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
									//Consulto los valores
									$sql_opciones = "SELECT * FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $row_pregunta["IDPreguntaVial"] . "' order by Orden";
									$r_opciones = $dbo->query($sql_opciones);
									$opciones_respuesta = array();
									$response_valores = array();
									while ($row_opciones = $dbo->fetchArray($r_opciones)) {
										$opciones_respuesta["IDPreguntaFormulario"] = $row_opciones["IDPreguntaVial"];
										$opciones_respuesta["IDPreguntaOpcionesRespuesta"] = $row_opciones["IDPreguntaVialOpcionesRespuesta"];
										$opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
										$opciones_respuesta["Terminar"] = $row_opciones["Terminar"];
										$opciones_respuesta["Peso"] = $row_opciones["Peso"];
										array_push($response_valores, $opciones_respuesta);
									}
									$pregunta["Valores"] = $response_valores;

									$pregunta["Orden"] = $row_pregunta["Orden"];
									if ($row_pregunta["TipoCampo"] == "imagen") {
										$pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaVial"];
									}
									array_push($response_pregunta, $pregunta);
								endwhile;

								$encuesta["Preguntas"] = $response_pregunta;
								array_push($response_encuesta, $encuesta);
							}
							$contador_encuesta++;
						} //ednw hile
						// var_dump($array_encuestas_activas);
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;
					} //End if
					$configuracion["Formularios"] = $response_encuesta;
					$configuracion["FormulariosActivos"] = $array_encuestas_activas;
					//array_push($response, $configuracion);

				}
			} //ednw hile


			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $configuracion;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else


		return $respuesta;
	} // fin function


	public function get_carros_seguridad_vial($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();

		$response = array();



		$sql = "SELECT * FROM EncuestaVialCarro WHERE IDSocio = '" . $IDSocio . "'  ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {
				$carro["IDCarro"] = $r["IDEncuestaVialCarro"];
				$carro["Placa"] = $r["Placa"];
				if ($r["Estado"] == "activo")
					$color_estado = "#1C590B";
				else
					$color_estado = "#CC0C21";

				$carro["ColorHtml"] = $color_estado;
				$carro["Estado"] = $r["Estado"];

				$response_respuesta = array();
				$sql_respuesta = "SELECT * FROM EncuestaRespuestaVialCarro WHERE IDSocio = '" . $IDSocio . "' and IDEncuestaVialCarro = '" . $r["IDEncuestaVialCarro"] . "'";
				$qr_respuesta = $dbo->query($sql_respuesta);
				while ($row_respuesta = $dbo->assoc($qr_respuesta)) {
					$respuesta["Label"] =  $dbo->getFields("PreguntaVialCarro", "EtiquetaCampo", "IDPreguntaVialCarro = '" . $row_respuesta["IDPreguntaVialCarro"] . "'");
					$respuesta["Valor"] = $row_respuesta["Valor"];
					array_push($response_respuesta, $respuesta);
				}

				$carro["Caracteristicas"] = $response_respuesta;
				array_push($response, $carro);
			} //ednw hile


			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "C1." .  SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else


		return $respuesta;
	} // fin function

	public function set_seguridad_vial($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario = "", $IDCarro = "", $Archivo, $File = "")
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDEncuesta)) {

			if (!empty($IDUsuario)) {
				$IDSocio = $IDUsuario;
				$TipoUsuario = "Funcionario";
			} else {
				$TipoUsuario = "Socio";
			}
			$guardar_encuesta = 1;

			if ($guardar_encuesta == 1) {
				$sql_pregunta = "SELECT IDPreguntaVial, Obligatorio,TipoCampo FROM PreguntaVial WHERE IDEncuestaVial = '" . $IDEncuesta . "' ";
				$r_pregunta = $dbo->query($sql_pregunta);
				while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
					$array_pregunta[$row_pregunta["IDPreguntaVial"]] = $row_pregunta["Obligatorio"];
					$array_preguntaImage[$row_pregunta["IDPreguntaVial"]] = $row_pregunta["TipoCampo"];
				}


				$datos_correctos = "S";
				$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
				$datos_respuesta = json_decode($Respuestas, true);
				if (count($datos_respuesta) > 0) :

					foreach ($datos_respuesta as $detalle_respuesta) {
						if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPreguntaFormulario"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPreguntaFormulario"]] != "imagen") {
							$datos_correctos = "N";
							$PreguntaValida = $detalle_respuesta["IDPreguntaFormulario"];
							break;
						} else {
							$datos_correctos = "S";
						}
					}
					if ($datos_correctos == "N") {
						$respuesta["message"] = SIMUtil::get_traduccion('', '', 'DatosNofueronenviados,algunadelasrespuestasesincorrecta,porfavorverifique.', LANG) . " " . $PreguntaValida;
						$respuesta["success"] = false;
						$respuesta["response"] = null;
						return $respuesta;
					}
					$suma_peso = 0;
					foreach ($datos_respuesta as $detalle_respuesta) :
						if ($detalle_respuesta["Valor"] != "null") {
							$inserta_resp = "INSERT INTO EncuestaRespuestaVial (IDEncuestaVial, IDSocio, IDPreguntaVial, IDEncuestaVialCarro, IDPreguntaVialOpcionesRespuesta, TipoUsuario, Valor, Peso, FechaTrCr) VALUES ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDPreguntaFormulario"] . "', '" . $IDCarro . "','" . $detalle_respuesta["ValorID"] . "', '" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $detalle_respuesta["Peso"] . "',NOW())";
							$dbo->query($inserta_resp);

							$datos_pregunta = $dbo->fetchAll("PreguntaVial", " IDPreguntaVial = '" . $detalle_respuesta["IDPreguntaFormulario"] . "' ", "array");

							$respuestas_EncuestaVial .= $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"] . "<br>";

							if ($detalle_respuesta["Peso"] > 0) {
								$suma_peso += $detalle_respuesta["Peso"];
							}
						}
					endforeach;

				endif;

				$Result_encuestaVial = $dbo->fetchAll("EncuestaVial", "IDEncuestaVial = '" . $IDEncuesta . "' ", "array");
				$sql_respuesta = "SELECT * FROM EncuestaRespuestaVial WHERE IDSocio = '" . $IDSocio . "' and IDEncuestaVial = '" . $IDEncuesta . "' ORDER BY FechaTrCr Desc Limit 1";
				$qr_respuesta = $dbo->query($sql_respuesta);
				$row_respuesta = $dbo->assoc($qr_respuesta);



				if ($suma_peso > $Result_encuestaVial['PesoMaximo']) {
					$message = $Result_encuestaVial['MensajePesoSuperado'];
					SIMUtil::noticar_EncuestaVial_Peso_No_Permitido($IDSocio, $row_respuesta['IDEncuestaVial'], $row_respuesta['FechaTrCr'], $suma_peso, $message);

					if (!empty($Result_encuestaVial['MensajePesoSuperado'])) {
						$respuesta_enc = $Result_encuestaVial['MensajePesoSuperado'];
					} else {
						$respuesta_enc = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
					}
				} else {
					if (!empty($Result_encuestaVial['MensajePesoPermitido'])) {
						$respuesta_enc = $Result_encuestaVial['MensajePesoPermitido'];
					} else {
						$respuesta_enc = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
					}
				}

				//subir las imagenes
				if (isset($File)) {
					//$nombrefoto.=json_encode($File);
					foreach ($File as $nombre_archivo => $archivo) {
						$ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
						$IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
						//$nombrefoto.=$archivo["name"];
						//$nombrefoto.=json_encode($archivo);
						$tamano_archivo = $archivo["size"];
						if ($tamano_archivo >= 6000000) {
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						} else {
							$files = SIMFile::upload($File[$nombre_archivo], PQR_DIR, "IMAGE");
							if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = null;
								return $respuesta;
							endif;

							$Archivo = $files[0]["innername"];
							$actualiza_pregunta = "UPDATE EncuestaRespuestaVial SET Valor = '" . $Archivo . "' WHERE IDPreguntaVial ='" . $IDPreguntaActualiza . "' and IDEncuestaVial  = '" . $IDEncuesta . "' and IDSocio = '" . $IDSocio . "' ORDER BY FechaTrCr DESC LIMIT 1";
							$dbo->query($actualiza_pregunta);
							//$nombrefoto.=    $actualiza_pregunta;
						}
					}
				}


				$respuesta["message"] = $respuesta_enc;
				$respuesta["success"] = true;
				$respuesta["response"] = $datos_reserva;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Estaencuestayahabíasidocontestadaporusted,solosepermite1vez', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_carro_seguridad_vial($IDClub, $IDSocio, $Placa, $Respuestas, $IDUsuario = "", $Archivo, $File = "")
	{

		$dbo = &SIMDB::get();
		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($Placa)) {

			if (!empty($IDUsuario)) {
				$IDSocio = $IDUsuario;
				$TipoUsuario = "Funcionario";
				$cond_placa = " and IDUsuario = '" . $IDUsuario . "'";
			} else {
				$TipoUsuario = "Socio";
				$cond_placa = " and IDSocio = '" . $IDSocio . "'";
			}

			//Verifico que esa placa ya no este agregada
			$placa_verif =  $dbo->getFields("EncuestaVialCarro", "IDEncuestaVialCarro", "Placa = '" . $Placa . "' " . $cond_placa);
			if (!empty($placa_verif)) {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Laplaca', LANG) . " " . $Placa . " " . SIMUtil::get_traduccion('', '', 'yaexiste', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
				return $respuesta;
			}

			$sql_inserta_carro = "INSERT INTO EncuestaVialCarro (IDClub,IDSocio,IDUsuario,Placa,Estado,UsuarioTrCr,FechaTrCr)
												  VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDUsuario . "','" . $Placa . "','activo','Socio', NOW()) ";
			$dbo->query($sql_inserta_carro);
			$id_carro = $dbo->lastID();



			$sql_pregunta = "SELECT IDPreguntaVial, Obligatorio,TipoCampo FROM PreguntaVialCarro WHERE IDClub = '" . $IDClub . "' ";
			$r_pregunta = $dbo->query($sql_pregunta);
			while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
				$array_pregunta[$row_pregunta["IDPreguntaVialCarro"]] = $row_pregunta["Obligatorio"];
				$array_preguntaImage[$row_pregunta["IDPreguntaVialCarro"]] = $row_pregunta["TipoCampo"];
			}


			$datos_correctos = "S";
			$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
			$datos_respuesta = json_decode($Respuestas, true);
			if (count($datos_respuesta) > 0) :

				foreach ($datos_respuesta as $detalle_respuesta) {
					if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPreguntaFormulario"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPreguntaFormulario"]] != "imagen") {
						$datos_correctos = "N";
						$PreguntaValida = $detalle_respuesta["IDPreguntaFormulario"];
						break;
					} else {
						$datos_correctos = "S";
					}
				}
				if ($datos_correctos == "N") {
					$respuesta["message"] = SIMUtil::get_traduccion('', '', 'DatosNofueronenviados,algunadelasrespuestasesincorrecta,porfavorverifique.', LANG) . " " . $PreguntaValida;
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				}
				$suma_peso = 0;
				foreach ($datos_respuesta as $detalle_respuesta) :
					if ($detalle_respuesta["Valor"] != "null") {
						$inserta_resp = "INSERT INTO EncuestaRespuestaVialCarro (IDEncuestaVialCarro, IDSocio, IDUsuario, IDPreguntaVialCarro, TipoUsuario, Valor,  FechaTrCr) VALUES ('" . $id_carro . "','" . $IDSocio . "','" . $IDUsuario . "','" . $detalle_respuesta["IDCampo"] . "', '" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())";
						$dbo->query($inserta_resp);
					}
				endforeach;
			endif;

			$Result_encuestaVial = $dbo->fetchAll("EncuestaVialCarro", "IDEncuestaVialCarro = '" . $IDEncuesta . "' ", "array");
			$sql_respuesta = "SELECT * FROM EncuestaRespuestaVialCarro WHERE IDSocio = '" . $IDSocio . "' and IDEncuestaVialCarro = '" . $IDEncuesta . "' ORDER BY FechaTrCr Desc Limit 1";
			$qr_respuesta = $dbo->query($sql_respuesta);
			$row_respuesta = $dbo->assoc($qr_respuesta);



			//subir las imagenes
			if (isset($File)) {
				//$nombrefoto.=json_encode($File);
				foreach ($File as $nombre_archivo => $archivo) {
					$ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
					$IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
					//$nombrefoto.=$archivo["name"];
					//$nombrefoto.=json_encode($archivo);
					$tamano_archivo = $archivo["size"];
					if ($tamano_archivo >= 6000000) {
						$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
						$respuesta["success"] = false;
						$respuesta["response"] = null;
						return $respuesta;
					} else {
						$files = SIMFile::upload($File[$nombre_archivo], PQR_DIR, "IMAGE");
						if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						endif;

						$Archivo = $files[0]["innername"];
						$actualiza_pregunta = "UPDATE EncuestaRespuestaVialCarro SET Valor = '" . $Archivo . "' WHERE IDPreguntaVialCarro ='" . $IDPreguntaActualiza . "' and IDEncuestaVialCarro  = '" . $IDEncuesta . "' and IDSocio = '" . $IDSocio . "' ORDER BY FechaTrCr DESC LIMIT 1";
						$dbo->query($actualiza_pregunta);
						//$nombrefoto.=    $actualiza_pregunta;
					}
				}
			}


			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Carroagregadocorrectamente', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = "";
		} else {
			$respuesta["message"] = "V6." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function get_mis_diagnosticos($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();

		//Socio
		if (!empty($IDSocio) || !empty($IDUsuario)) {

			if (!empty($IDSocio)) {
				$condicion = " IDSocio = '" . $IDSocio . "' ";
				$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
				$info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
			}

			if (!empty($IDUsuario)) {
				$condicion = " IDUsuario = '" . $IDUsuario . "' ";
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
				$info = $datos_usuario["Nombre"];
			}

			$response = array();
			$sql = "SELECT IDEncuestaVial, FechaTrCr FROM EncuestaRespuestaVial WHERE  " . $condicion . " GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
			$qry = $dbo->query($sql);
			if ($dbo->rows($qry) > 0) {
				$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
				while ($r = $dbo->fetchArray($qry)) {
					$DetalleResp = "";
					$objeto["IDSeguridadVial"] = $r["IDDiagnostico"];
					$objeto["Fecha"] = substr($r["FechaTrCr"], 0, 10);
					$objeto["Hora"] = substr($r["FechaTrCr"], 10);
					$objeto["Texto"] = $info;

					//Consulta las respuestas del diagnostico
					$sql_detalle = "SELECT PD.EtiquetaCampo, DR.IDDiagnostico, DR.IDDiagnosticoRespuesta,DR.FechaTrCr, DR.Valor
											FROM EncuestaRespuestaVial ER, PreguntaVial PV
											WHERE " . $condicion . " and ER.IDPreguntaEncuestavial=PV.IDPreguntaEncuestavial
											AND ER.IDEncuestaVial = '" . $r["IDEncuestaVial"] . "' and DR.FechaTrCr between '" . substr($r["FechaTrCr"], 0, 10) . " 00:00:00' and '" . substr($r["FechaTrCr"], 0, 10) . " 23:59:59' ";
					$qry_detalle = $dbo->query($sql_detalle);
					while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
						$DetalleResp .= "<b>" . $r_detalle["EtiquetaCampo"] . "</b>=" . $r_detalle["Valor"] . "<br>";
					}

					$objeto["Descripcion"] = $DetalleResp;
					array_push($response, $objeto);
				} //ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			} //End if
			else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			} //end else
		} else {
			$respuesta["message"] = "HV." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}
		return $respuesta;
	} // fin function



	public function set_activar_carro_seguridad_vial($IDClub, $IDCarro, $IDSocio, $IDUsuario, $Estado)
	{

		$dbo = &SIMDB::get();
		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($Estado)) {

			$actualiza_carro = "UPDATE EncuestaVialCarro SET Estado = '" . ucfirst($Estado) . "' WHERE IDEncuestaVialCarro ='" . $IDCarro . "'";
			$dbo->query($actualiza_carro);


			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Estadomodificadoa', LANG) . " " . ucfirst($Estado) . " " . SIMUtil::get_traduccion('', '', 'correctamente', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = null;
		} else {
			$respuesta["message"] = "V5." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}


	public function get_historial_seguridad_vial($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();

		//Socio
		if (!empty($IDSocio) || !empty($IDUsuario)) {

			if (!empty($IDSocio)) {
				$condicion = " IDSocio = '" . $IDSocio . "' ";
				$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
				$info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
			}

			if (!empty($IDUsuario)) {
				$condicion = " IDUsuario = '" . $IDUsuario . "' ";
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
				$info = $datos_usuario["Nombre"];
			}

			$response = array();
			$sql = "SELECT IDEncuestaVialRespuestaVial,IDEncuestaVial, FechaTrCr FROM  EncuestaRespuestaVial WHERE  " . $condicion . " GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
			$qry = $dbo->query($sql);
			if ($dbo->rows($qry) > 0) {
				$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
				while ($r = $dbo->fetchArray($qry)) {
					$DetalleResp = "";
					$objeto["IDSeguridadVial"] = $r["IDEncuestaVial"];
					$objeto["Fecha"] = substr($r["FechaTrCr"], 0, 10);
					$objeto["Hora"] = substr($r["FechaTrCr"], 10);
					$objeto["Texto"] = $info;

					//Consulta las respuestas del diagnostico

					$sql_detalle = "SELECT PV.EtiquetaCampo, ERV.IDEncuestaVial,ERV.FechaTrCr, ERV.Valor
																	FROM `EncuestaRespuestaVial`  ERV, PreguntaVial PV
																	WHERE ERV.IDPreguntaVial = PV.IDPreguntaVial  and  " . $condicion . " AND
																				ERV.IDEncuestaVial = '" . $r["IDEncuestaVial"] . "' AND
																				ERV.FechaTrCr between '" . substr($r["FechaTrCr"], 0, 10) . " 00:00:00' and '" . substr($r["FechaTrCr"], 0, 10) . " 23:59:59'";

					$qry_detalle = $dbo->query($sql_detalle);
					while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
						$DetalleResp .= "<b>" . $r_detalle["EtiquetaCampo"] . "</b>=" . $r_detalle["Valor"] . "<br>";
					}

					$objeto["Descripcion"] = $DetalleResp;
					array_push($response, $objeto);
				} //ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			} //End if
			else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			} //end else
		} else {
			$respuesta["message"] = "DR." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}
		return $respuesta;
	} // fin function


} //end class
