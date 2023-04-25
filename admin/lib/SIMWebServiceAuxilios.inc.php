<?php
class SIMWebServiceAuxilios
{

	function get_configuracion_auxilios($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDSocio)) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		}

		$sql = "SELECT * FROM ConfiguracionAuxilios  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$configuracion["PermiteResponderAuxilios"] = $datos_usuario["PermiteResponderAuxilios"];
				$configuracion["LabelBotonResponderAuxilios"] = $r["LabelBotonResponderAuxilios"];
				$configuracion["LabelTextoAprobacion"] = $r["LabelTextoAprobacion"];
				$configuracion["LabelMotivoRechazo"] = $r["LabelMotivoRechazo"];
				$configuracion["LabelBotonResponderAuxilios"] = $r["LabelBotonResponderAuxilios"];
				$configuracion["LabelBotonMisSolicitudes"] = $r["LabelBotonMisSolicitudes"];
				$configuracion["LabelConfirmacionEnvioSolicitud"] = $r["LabelConfirmacionEnvioSolicitud"];
				$configuracion["LabelConfirmacionRespuestaSolicitud"] = $r["LabelConfirmacionRespuestaSolicitud"];
				$configuracion["CampoTipoRechazoActivo"] = $r["CampoTipoRechazoActivo"];
				$configuracion["CampoComentarioObligatorio"] = $r["CampoComentarioObligatorio"];

				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "Auxilios no está activo!";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_auxilios($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDSocio)) {
			$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
			$aplicaPara = $datos_socio['IDAreaSocio'];
			$Area = SIMResources::$areassoycentral[$aplicaPara];
		} else {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
			if ($datos_usuario['IDPerfil'] == 1 && $datos_usuario['IDPerfil'] == 0) {
				$Area = '';
			} else {
				$aplicaPara = $datos_usuario['IDAreaUsuario'];
				$Area = SIMResources::$areassoycentral[$aplicaPara];
			}
		}
		$sql = "SELECT * FROM Auxilios  WHERE IDClub = '" . $IDClub . "' and Publicar = 'S' and AplicaPara LIKE '%" . $Area . "%'";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$auxilio["IDClub"] = $r["IDClub"];
				$auxilio["IDAuxilio"] = $r["IDAuxilios"];
				$auxilio["Nombre"] = $r["Nombre"];
				$auxilio["Descripcion"] = $r["Descripcion"];
				$auxilio["Icono"] = BANNERAPP_ROOT . $r["Icono"];
				//Preguntas
				$pregunta = array();
				$response_pregunta = array();
				$sql_respuesta = "SELECT * From PreguntaAuxilios Where IDAuxilios = '" . $r["IDAuxilios"] . "' and Publicar = 'S' Order by Orden";
				$r_encuesta = $dbo->query($sql_respuesta);
				while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
					$pregunta["ParametroEnvioPost"] = "";
					$pregunta["IDPregunta"] = $row_pregunta["IDPreguntaAuxilios"];
					$pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
					$pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
					$pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
					$pregunta["Valores"] = $row_pregunta["Valores"];
					$pregunta["Orden"] = $row_pregunta["Orden"];
					if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "imagenarchivo") {
						$pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaAuxilios"];
					}

					array_push($response_pregunta, $pregunta);
				endwhile;

				$auxilio["Preguntas"] = $response_pregunta;


				array_push($response, $auxilio);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "AX1. Auxilios no está activo";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_tiporechazo_auxilio($IDClub, $IDSocio, $IDUsuario, $IDAuxilio)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT * FROM AuxiliosRechazo  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$auxilio["IDTipoRechazo"] = $r["IDAuxiliosRechazo"];
				$auxilio["NombreRechazo"] = $r["Nombre"];
				array_push($response, $auxilio);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "AX2. Auxilios rechazo no se encontraron";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_auxilios_por_aprobar($IDClub, $IDSocio, $IDUsuario, $IDAuxilio)
	{
		$dbo = &SIMDB::get();
		$response = array();
		if (!empty($IDUsuario)) {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
		} elseif (!empty($IDSocio)) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		} else {
			exit;
		}
		$CampoUsuario = "IDSocio";
		$TipoUsuario = "Socio";
		$IDInserta = $IDSocio;

		$sql = "SELECT a.IDAuxiliosSolicitud,a.IDAuxilios,a.IDEstado,a.FechaTrCr,Socio.Nombre,Socio.Apellido,Socio.CargoSocio FROM AuxiliosSolicitud a, $TipoUsuario  WHERE a.IDSocio" . "=" . $TipoUsuario . "." . $CampoUsuario . " AND " . $TipoUsuario . ".DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' AND a.IDClub = '" . $IDClub . "' and IDEstado ='1'  ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";

			while ($r = $dbo->fetchArray($qry)) {
				$Solicitante = $r["Nombre"] . " " . $r["Apellido"];
				$Cargo = (!empty($r["CargoSocio"])) ? $r['CargoSocio'] : '';;
				if (!empty($datos_usuario["Foto"])) {
					$foto = SOCIO_ROOT . $datos_usuario["Foto"];
				}
				$datos_aux = $dbo->fetchAll("Auxilios", " IDAuxilios = '" . $r["IDAuxilios"] . "' ", "array");
				$Estado = SIMResources::$EstadoAuxilio[$r["IDEstado"]];
				$auxilio["Estado"] = $Estado;
				$auxilio["Fecha"] = $r["FechaTrCr"];
				$auxilio["Solicitante"] = $Solicitante;
				$auxilio["Foto"] = $foto;
				$auxilio["IDSolicitud"] = $r["IDAuxiliosSolicitud"];
				$auxilio["Cargo"] = $Cargo;
				$auxilio["IDAuxilio"] = $r["IDAuxilios"];
				$auxilio["NombreAuxilio"] = $datos_aux["Nombre"];

				//Respuestas
				$respuesta_aux = array();
				$response_resp_aux = array();
				$sql_resp = "SELECT * From AuxiliosRespuesta Where IDAuxiliosSolicitud = '" . $r["IDAuxiliosSolicitud"] . "'";
				$r_resp = $dbo->query($sql_resp);
				while ($row_resp = $dbo->FetchArray($r_resp)) :
					$datos_preg = $dbo->fetchAll("PreguntaAuxilios", " IDPreguntaAuxilios = '" . $row_resp["IDPreguntaAuxilios"] . "' ", "array");

					if ($datos_preg["TipoCampo"] == "imagen" || $datos_preg["TipoCampo"] == "imagenarchivo") {
						$Valor = PQR_ROOT . $row_resp["Valor"];
						$TipoCampo = "archivo";
					} else {
						$Valor = $row_resp["Valor"];
						$TipoCampo = "texto";
					}
					$respuesta_aux["TipoRespuesta"] = $TipoCampo;
					$respuesta_aux["Etiqueta"] = $datos_preg["EtiquetaCampo"];

					$respuesta_aux["Respuesta"] = $Valor;

					array_push($response_resp_aux, $respuesta_aux);
				endwhile;

				$auxilio["Respuestas"] = $response_resp_aux;

				array_push($response, $auxilio);
			} //edn while
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "AX2. Auxilios rechazo no se encontraron";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function get_mis_auxilios($IDClub, $IDSocio, $IDUsuario, $IDAuxilio)
	{
		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDUsuario)) {
			$CampoUsuario = "IDUsuario";
			$IDInserta = $IDUsuario;
			$TipoUsuario = "Funcionario";
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
			$Solicitante = $datos_usuario["Nombre"];
			$Cargo = $datos_usuario["Cargo"];
			if (!empty($datos_usuario["Foto"])) {
				$foto = USUARIO_ROOT . $datos_usuario["Foto"];
			}
		} elseif (!empty($IDSocio)) {
			$CampoUsuario = "IDSocio";
			$IDInserta = $IDSocio;
			$TipoUsuario = "Socio";
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
			$Solicitante = $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
			$Cargo = $datos_usuario["Cargo"];
			if (!empty($datos_usuario["Foto"])) {
				$foto = SOCIO_ROOT . $datos_usuario["Foto"];
			}
		} else {
			exit;
		}


		$sql = "SELECT * FROM AuxiliosSolicitud  WHERE " . $CampoUsuario . " = '" . $IDInserta . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$datos_aux = $dbo->fetchAll("Auxilios", " IDAuxilios = '" . $r["IDAuxilios"] . "' ", "array");
				$Estado = SIMResources::$EstadoAuxilio[$r["IDEstado"]];
				$mensajeRechazo = ($Estado == 'Rechazado') ? " -- (Remita una nueva solicitud sí es necesario)</p>" : "";
				$auxilio["Estado"] = $Estado;
				$auxilio["Fecha"] = $r["FechaTrCr"];
				$auxilio["Solicitante"] = $Solicitante;
				$auxilio["Foto"] = $foto;
				$auxilio["IDSolicitud"] = $r["IDAuxiliosSolicitud"];
				$auxilio["Cargo"] = $Cargo;
				$auxilio["IDAuxilio"] = $r["IDAuxilios"];
				$auxilio["NombreAuxilio"] = $datos_aux["Nombre"];
				$auxilio["ComentarioAprobacion"] = $r["Comentarios"] . $mensajeRechazo;
				$auxilio["IDTipoRechazo"] = $r["IDAuxiliosRechazo"];
				if ((int) $r["IDTipoRechazo"] > 0) {
					$datos_rech = $dbo->fetchAll("AuxiliosRechazo", " IDAuxiliosRechazo = '" . $r["IDAuxiliosRechazo"] . "' ", "array");
				}
				$auxilio["NombreRechazo"] = $datos_rech["Nombre"];


				//Respuestas
				$respuesta_aux = array();
				$response_resp_aux = array();
				$sql_resp = "SELECT * From AuxiliosRespuesta Where IDAuxiliosSolicitud = '" . $r["IDAuxiliosSolicitud"] . "'";
				$r_resp = $dbo->query($sql_resp);
				while ($row_resp = $dbo->FetchArray($r_resp)) :
					$datos_preg = $dbo->fetchAll("PreguntaAuxilios", " IDPreguntaAuxilios = '" . $row_resp["IDPreguntaAuxilios"] . "' ", "array");

					if ($datos_preg["TipoCampo"] == "imagen" || $datos_preg["TipoCampo"] == "imagenarchivo") {
						$Valor = PQR_ROOT . $row_resp["Valor"];
						$TipoCampo = "archivo";
					} else {
						$Valor = $row_resp["Valor"];
						$TipoCampo = "texto";
					}

					$respuesta_aux["TipoRespuesta"] = $TipoCampo;
					$respuesta_aux["Etiqueta"] = $datos_preg["EtiquetaCampo"];
					$respuesta_aux["Respuesta"] = $Valor;

					array_push($response_resp_aux, $respuesta_aux);
				endwhile;

				$auxilio["Respuestas"] = $response_resp_aux;



				array_push($response, $auxilio);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "AX2. No tiene auxilios registrados";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo = "", $File = "")
	{
		$dbo = &SIMDB::get();

		//Guardo la solictud
		$inserta_solictud = "INSERT INTO AuxiliosSolicitud (IDClub," . $CampoUsuario . ",IDAuxilios,IDEstado,UsuarioTrCr,FechaTrCr)
																	VALUES ('" . $IDClub . "','" . $IDInserta . "','" . $IDAuxilio . "','1','App',NOW())";
		$dbo->query($inserta_solictud);
		$id_solicitud = $dbo->lastID();

		$sql_pregunta = "SELECT IDPregunta, Obligatorio,TipoCampo FROM PreguntaAuxilios WHERE IDAuxilios = '" . $IDAuxilio . "' ";
		$r_pregunta = $dbo->query($sql_pregunta);
		while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
			$array_pregunta[$row_pregunta["IDPregunta"]] = $row_pregunta["Obligatorio"];
			$array_preguntaImage[$row_pregunta["IDPregunta"]] = $row_pregunta["TipoCampo"];
		}

		$datos_correctos = "S";
		$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
		$datos_respuesta = json_decode($Respuestas, true);
		if (count($datos_respuesta) > 0) :
			foreach ($datos_respuesta as $detalle_respuesta) {
				if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPregunta"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPregunta"]] != "imagen") {
					$datos_correctos = "N";
					$PreguntaValida = $detalle_respuesta["IDPregunta"];
					break;
				} else {
					$datos_correctos = "S";
				}
			}
			if ($datos_correctos == "N") {
				$respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique." . $PreguntaValida;
				$respuesta["success"] = false;
				$respuesta["response"] = null;
				return $respuesta;
			}

			foreach ($datos_respuesta as $detalle_respuesta) :
				$sql_datos_form = "INSERT INTO AuxiliosRespuesta (IDAuxiliosSolicitud, IDAuxilios, " . $CampoUsuario . ", IDPreguntaAuxilios,  TipoUsuario, Valor, FechaTrCr)
																						Values ('" . $id_solicitud . "','" . $IDAuxilio . "','" . $IDInserta . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())";
				$dbo->query($sql_datos_form);
			endforeach;
		endif;

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
					$respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				} else {
					$files = SIMFile::upload($File[$nombre_archivo], PQR_DIR, "IMAGE");
					if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
						$respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
						$respuesta["success"] = false;
						$respuesta["response"] = null;
						return $respuesta;
					endif;

					$Archivo = $files[0]["innername"];
					$actualiza_pregunta = "UPDATE AuxiliosRespuesta SET Valor = '" . $Archivo . "' WHERE IDPreguntaAuxilios ='" . $IDPreguntaActualiza . "' and IDAuxilios  = '" . $IDAuxilio . "' and " . $CampoUsuario . " = '" . $IDInserta . "'";
					$dbo->query($actualiza_pregunta);
					$nombrefoto .=    $actualiza_pregunta;
				}
			}
		}
		SIMUtil::noticar_nueva_AuxiliosSolicitud($id_solicitud);

		$RespuestaEncuestaArbol = "Guardado con exito.";
		// $RespuestaEncuestaArbol = "Guardado con exito." . $nombrefoto;

		$respuesta["message"] = $RespuestaEncuestaArbol;
		$respuesta["success"] = true;
		$respuesta["response"] = $response;
		return $respuesta;
	}
	function set_auxilio($IDClub, $IDSocio, $IDAuxilio, $Respuestas, $IDUsuario, $Archivo = "", $File = "")
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub)  && (!empty($IDSocio) || !empty($IDUsuario) || !empty($IDAuxilio))) {

			if (!empty($IDUsuario)) {
				$CampoUsuario = "IDUsuario";
				$IDInserta = $IDUsuario;
				$TipoUsuario = "Funcionario";
			} elseif (!empty($IDSocio)) {
				$CampoUsuario = "IDSocio";
				$IDInserta = $IDSocio;
				$TipoUsuario = "Socio";
			} else {
				exit;
			}

			$sql_Auxilios = "SELECT Periocidad FROM Auxilios WHERE IDAuxilios = " . $IDAuxilio;
			$query_Auxilios = $dbo->query($sql_Auxilios);
			$row_Auxilios = $dbo->assoc($query_Auxilios);
			$periocidad = $row_Auxilios['Periocidad'];

			$sql_AuxiliosSolicitud = "SELECT FechaTrCr FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND " . $CampoUsuario . " = " . $IDInserta . " AND IDAuxilios = '" . $IDAuxilio . "' ORDER BY FechaTrCr DESC LIMIT 1 ";
			$query_AuxiliosSolicitud = $dbo->query($sql_AuxiliosSolicitud);
			$num_AuxiliosSolicitud = $dbo->rows($query_AuxiliosSolicitud);

			if ($num_AuxiliosSolicitud > 0) {

				if ($periocidad == 'Único pago') {

					$respuesta["message"] = "Solo puedes solicitar una vez este Auxilio";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				} elseif ($periocidad == '2 veces al año') {
					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxilios = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 2) {
						$respuesta["message"] = "Solo puedes solicitar este Auxilio 2 veces al año";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == '3 veces al año') {
					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxilios = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 3) {
						$respuesta["message"] = "Solo puedes solicitar este Auxilio 3 veces al año";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == '4 veces al año') {
					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxilios = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 4) {
						$respuesta["message"] = "Solo puedes solicitar este Auxilio 4 veces al año";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == '12 veces al año') {
					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxilios = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 12) {
						$respuesta["message"] = "Solo puedes solicitar este Auxilio 12 veces al año";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == 'Anual') {

					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxilios = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 1) {
						$respuesta["message"] = "Solo puedes solicitar este Auxilio una vez al año.";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == 'Una vez por tipo de auxilio al año') {

					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$TipoAuxilio = $dbo->getFields('Auxilios', 'IDTipoAuxilio', 'IDAuxilios="' . $IDAuxilio . '"');

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosSolicitud FROM AuxiliosSolicitud as AuS,Auxilios as A WHERE AuS.IDAuxilios=A.IDAuxilios AND  AuS.IDClub = '" . $IDClub . "' AND A.IDTipoAuxilio = '" . $TipoAuxilio . "' AND " . $CampoUsuario . " = " . $IDInserta . " AND  AuS.FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND AuS.IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 1) {
						$respuesta["message"] = "Solo puedes solicitar este Tipo de Auxilio una vez al año.";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxilios;
						$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
					}
				} elseif ($periocidad == 'Acontecimiento (Nacimiento,muerte,etc)') {
					$obj = new SIMWebServiceAuxilios;
					$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
				}
			} else {

				$obj = new SIMWebServiceAuxilios;
				$respuesta = $obj->save_AuxiliosSolicitud($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File);
			}
		} else {
			$respuesta["message"] = "A6. Atención faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}


	function set_respuesta_solicitud_auxilio($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $IDTipoRechazo, $Comentarios)
	{
		$dbo = &SIMDB::get();

		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSolicitud) && !empty($Aprueba)) {

			if ($Aprueba == "S") {
				$IDEstado = 3;
			} else {
				$IDEstado = 2;
			}

			if (!empty($IDUsuario)) {
				$CampoUsuario = "IDUsuarioAprueba";
				$IDInserta = $IDUsuario;
				$TipoUsuario = "Funcionario";
			} elseif (!empty($IDSocio)) {
				$CampoUsuario = "IDSocioAprueba";
				$IDInserta = $IDSocio;
				$TipoUsuario = "Socio";
			} else {
				exit;
			}

			$sql_solic = "UPDATE AuxiliosSolicitud
													 SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',Aprobado='" . $Aprueba . "',IDAuxiliosRechazo='" . $IDTipoRechazo . "',
													 Comentarios='" . $Comentarios . "',FechaRevision=NOW(),FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
													 WHERE IDAuxiliosSolicitud = '" . $IDSolicitud . "'	";
			$dbo->query($sql_solic);

			SIMUtil::noticar_respuesta_AuxilioSolicitud($IDSolicitud, $Comentarios);

			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "AX8. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}
} //end class
