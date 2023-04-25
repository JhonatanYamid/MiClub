<?php
class SIMWebServiceAuxiliosInfinito
{

	function get_configuracion_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDModulo)
	{
		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDSocio)) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		} else {
			$datos_usuario = $dbo->fetchAll("Usuario", "IDUsuario = '" . $IDUsuario . "' ", "array");
		}

		$sql = "SELECT * FROM ConfiguracionAuxiliosInfinito  WHERE IDClub = '" . $IDClub . "' AND IDModulo=  '" . $IDModulo . "'";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {

				if ($r['GestionDeUnAprobador'] == 'S' && $r['NumeroDocumentoUnicoAprobador'] == $datos_usuario['NumeroDocumento']) {
					$configuracion["PermiteResponderAuxilios"] = 'S';
				} else {
					$configuracion["PermiteResponderAuxilios"] = $datos_usuario["PermiteResponderAuxilios"];
				}

				$configuracion["LabelBotonResponderAuxilios"] = $r["LabelBotonResponderAuxilios"];
				$configuracion["LabelTextoAprobacion"] = $r["LabelTextoAprobacion"];
				$configuracion["LabelMotivoRechazo"] = $r["LabelMotivoRechazo"];
				$configuracion["LabelBotonResponderAuxilios"] = $r["LabelBotonResponderAuxilios"];
				$configuracion["LabelBotonMisSolicitudes"] = $r["LabelBotonMisSolicitudes"];
				$configuracion["LabelConfirmacionEnvioSolicitud"] = $r["LabelConfirmacionEnvioSolicitud"];
				$configuracion["LabelConfirmacionRespuestaSolicitud"] = $r["LabelConfirmacionRespuestaSolicitud"];
				$configuracion["LabelHeaderSeleccion"] = $r["LabelHeaderSeleccion"];
				$configuracion["PermiteMostrarEstadoAuxilio"] = $r["PermiteMostrarEstadoAuxilio"];
				$configuracion["PermiteMostrarMisAuxilios"] = $r["PermiteMostrarMisAuxilios"];
				$configuracion["IconoEstadoAuxilioEntregado"] = BANNERAPP_ROOT . $r["IconoEstadoAuxilioEntregado"];
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

	function get_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDModulo)
	{
		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDSocio)) {
			$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
			$aplicaPara = $datos_socio['IDAreaSocio'];
			$Area = SIMResources::$areassoycentral[$aplicaPara];
		} else {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
			if ($datos_usuario['IDPerfil'] == 1) {
				$Area = '';
			} else {
				$aplicaPara = $datos_usuario['IDAreaUsuario'];
				$Area = SIMResources::$areassoycentral[$aplicaPara];
			}
		}

		// Cambiar club para luker

		$arrIDClubLuker = SIMResources::$arrIDClubLuker;
		if (!in_array($IDClub, $arrIDClubLuker)) {
			$condicionArea = "and AplicaPara LIKE '%" . $Area . "%'";
		} else {
			$condicionArea = "";
		}
		$sql = "SELECT * FROM AuxiliosInfinito  WHERE IDClub = '" . $IDClub . "' and Publicar = 'S' $condicionArea  AND IDModulo=  '" . $IDModulo . "'";

		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$auxilio["IDClub"] = $r["IDClub"];
				$auxilio["IDAuxilio"] = $r["IDAuxiliosInfinito"];
				$auxilio["Nombre"] = $r["Nombre"];
				$auxilio["Descripcion"] = $r["Descripcion"];
				$auxilio["Icono"] = BANNERAPP_ROOT . $r["Icono"];

				if (!empty($IDSocio)) {

					$sql_a = "SELECT  IDAuxiliosInfinito FROM AuxiliosInfinitoSolicitud  WHERE IDAuxiliosInfinito = '" . $r["IDAuxiliosInfinito"] . "' and IDSocio = '" . $IDSocio . "' and IDEstado = 1";
				} else {

					$sql_a = "SELECT  IDAuxiliosInfinito FROM AuxiliosInfinitoSolicitud  WHERE IDAuxiliosInfinito = '" . $r["IDAuxiliosInfinito"] . "' and IDUsuario = '" . $IDUsuario . "' and IDEstado = 1";
				}

				$qry_a = $dbo->query($sql_a);

				if ($dbo->rows($qry_a) > 0) {
					$auxilio["RecibioAuxilio"]  = "S";
				} else {
					$auxilio["RecibioAuxilio"]  = "N";
				}
				// Cusezar no limita las solicitudes de auxilios
				if ($IDClub == 222) {
					$auxilio["RecibioAuxilio"]  = "N";
				}

				/* $respuesta["message"] = "AX1. Auxilios no está activo".$r["IDAuxiliosInfinito"] ;
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				/* 	return $respuesta; */
				//Preguntas
				$pregunta = array();
				$response_pregunta = array();
				$sql_respuesta = "SELECT * From PreguntaAuxiliosInfinito Where IDAuxiliosInfinito = '" . $r["IDAuxiliosInfinito"] . "'  AND IDModulo=  '" . $IDModulo . "' and Publicar = 'S'  Order by Orden";
				$r_encuesta = $dbo->query($sql_respuesta);
				while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
					$pregunta["ParametroEnvioPost"] = "";
					$pregunta["IDPregunta"] = $row_pregunta["IDPreguntaAuxiliosInfinito"];
					$pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
					$pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
					$pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
					$pregunta["Valores"] = $row_pregunta["Valores"];
					$pregunta["Orden"] = $row_pregunta["Orden"];
					if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "imagenarchivo") {
						$pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaAuxiliosInfinito"];
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
			if (!in_array($IDClub, $arrIDClubLuker)) {
				$message = "LI1. No tiene licencias activos";
			} else {
				$message = "AX1. Auxilios no tiene auxilios activos";
			}
			$respuesta["message"] = $message;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_tiporechazo_auxilio_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT * FROM AuxiliosInfinitoRechazo  WHERE IDClub = '" . $IDClub . "'  AND IDModulo=  '" . $IDModulo . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$auxilio["IDTipoRechazo"] = $r["IDAuxiliosInfinitoRechazo"];
				$auxilio["NombreRechazo"] = $r["Nombre"];
				array_push($response, $auxilio);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			if (!in_array($IDClub, $arrIDClubLuker)) {
				$message = "AX2. Tipo rechazo no se encontraron";
			} else {
				$message = "AX2. Auxilios rechazo no se encontraron";
			}
			$respuesta["message"] = $message;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_auxilios_por_aprobar_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo)
	{

		$dbo = &SIMDB::get();
		$response = array();

		// Validar configuracion del Modulo
		$ConfiguracionAuxiliosInfinito = $dbo->fetchAll("ConfiguracionAuxiliosInfinito", "IDClub = $IDClub and IDModulo = $IDModulo", "array");


		if (!empty($IDUsuario)) {
			$NumeroDocumento = $dbo->getFields("Usuario", "NumeroDocumento", " IDUsuario = '" . $IDUsuario . "' ");
		} elseif (!empty($IDSocio)) {
			$NumeroDocumento = $dbo->getFields("Socio", "NumeroDocumento", " IDSocio = '" . $IDSocio . "' ");
		} else {
			exit;
		}
		// Cambiar club para luker
		$arrIDClubLuker = SIMResources::$arrIDClubLuker;

		if ($ConfiguracionAuxiliosInfinito['GestionDeUnAprobador'] == 'S') {
			if ($ConfiguracionAuxiliosInfinito['NumeroDocumentoUnicoAprobador'] == $NumeroDocumento) {

				$where = "a.IDClub = " . $IDClub . " AND a.IDEstado NOT IN (2,3,4)";

				$sql = "SELECT a.*, IF(s.IDSocio>0,CONCAT(s.Nombre,' ',s.Apellido),u.Nombre) as Nombre, IF(s.IDSocio>0,s.Foto,u.Foto) as Foto FROM AuxiliosInfinitoSolicitud a LEFT JOIN Socio s ON a.IDSocio=s.IDSocio LEFT JOIN Usuario u ON a.IDUsuario=u.IDUsuario WHERE  $where ORDER BY a.FechaTrCr DESC";
			} else {
				$Respuesta['message'] = "No se encontraron solicitudes";
				$Respuesta['success'] = false;
				$Respuesta['response'] = "";
				return $Respuesta;
			}
		} else {

			$sql_Jefe = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' AND DocumentoJefe = " . $NumeroDocumento;
			$q_Jefe = $dbo->query($sql_Jefe);
			$n_Jefe = $dbo->rows($q_Jefe);

			$sql_Especialista = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' AND DocumentoEspecialista = " . $NumeroDocumento;
			$q_Especialista = $dbo->query($sql_Especialista);
			$n_Especialista = $dbo->rows($q_Especialista);
			$where = '';


			if ($n_Jefe > 0) {

				$where .= " AND a.IDEstado NOT IN (2,3,4)";
			} else if ($n_Especialista > 0) {
				if (in_array($IDClub, $arrIDClubLuker)) {
					$where .= " AND a.IDEstado NOT IN (1,2,3)";
				} else {
					$where .= " AND a.IDEstado NOT IN (2,3)";
				}
			}


			$sql = "SELECT a.*, IF(s.IDSocio>0,CONCAT(s.Nombre,' ',s.Apellido),u.Nombre) as Nombre, IF(s.IDSocio>0,s.Foto,u.Foto) as Foto FROM AuxiliosInfinitoSolicitud a LEFT JOIN Socio s ON a.IDSocio=s.IDSocio LEFT JOIN Usuario u ON a.IDUsuario=u.IDUsuario WHERE (u.DocumentoJefe = " . $NumeroDocumento . " OR u.DocumentoEspecialista = " . $NumeroDocumento . " OR s.DocumentoJefe= " . $NumeroDocumento . " OR s.DocumentoEspecialista=" . $NumeroDocumento . ") $where ORDER BY a.FechaTrCr DESC";
		}


		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";

			while ($r = $dbo->fetchArray($qry)) {
				// $datos_UsuarioSolicitante =
				if (!empty($r["Foto"])) {
					if ($r['IDSocio' > 0]) {
						$foto = SOCIO_ROOT . $r["Foto"];
					} else {
						$foto = USUARIO_ROOT . $r["Foto"];
					}
				}
				$datos_aux = $dbo->fetchAll("AuxiliosInfinito", " IDAuxiliosInfinito = '" . $r["IDAuxiliosInfinito"] . "' ", "array");
				$Estado = SIMResources::$EstadoAuxilio[$r["IDEstado"]];
				$auxilio["Estado"] = $Estado;
				$auxilio["Fecha"] = $r["FechaTrCr"];
				$auxilio["Solicitante"] = $r["Nombre"];
				$auxilio["Foto"] = $foto;
				$auxilio["IDSolicitud"] = $r["IDAuxiliosInfinitoSolicitud"];
				$auxilio["Cargo"] = '';
				$auxilio["IDAuxilio"] = $r["IDAuxiliosInfinito"];
				$auxilio["NombreAuxilio"] = $datos_aux["Nombre"];

				//Respuestas
				$respuesta_aux = array();
				$response_resp_aux = array();
				$sql_resp = "SELECT * From AuxiliosInfinitoRespuesta Where IDAuxiliosInfinitoSolicitud = '" . $r["IDAuxiliosInfinitoSolicitud"] . "'";
				$r_resp = $dbo->query($sql_resp);
				while ($row_resp = $dbo->FetchArray($r_resp)) :
					$datos_preg = $dbo->fetchAll("PreguntaAuxiliosInfinito", " IDPreguntaAuxiliosInfinito = '" . $row_resp["IDPreguntaAuxiliosInfinito"] . "' ", "array");
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
				unset($foto);
			} //edn while
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "TM2. No se encontraron solicitudes";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function get_mis_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo)
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


		$sql = "SELECT * FROM AuxiliosInfinitoSolicitud  WHERE " . $CampoUsuario . " = '" . $IDInserta . "'  AND IDModulo=  '" . $IDModulo . "'";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$datos_aux = $dbo->fetchAll("AuxiliosInfinito", " IDAuxiliosInfinito = '" . $r["IDAuxiliosInfinito"] . "' ", "array");
				$Estado = SIMResources::$EstadoAuxilio[$r["IDEstado"]];
				$auxilio["Estado"] = $Estado;
				$auxilio["Fecha"] = $r["FechaTrCr"];
				$auxilio["Solicitante"] = $Solicitante;
				$auxilio["Foto"] = $foto;
				$auxilio["IDSolicitud"] = $r["IDAuxiliosInfinitoSolicitud"];
				$auxilio["Cargo"] = $Cargo;
				$auxilio["IDAuxilio"] = $r["IDAuxiliosInfinito"];
				$auxilio["NombreAuxilio"] = $datos_aux["Nombre"];
				$auxilio["ComentarioAprobacion"] = $r["Comentarios"];
				$auxilio["IDTipoRechazo"] = $r["IDAuxiliosInfinitoRechazo"];
				if ((int) $r["IDTipoRechazo"] > 0) {
					$datos_rech = $dbo->fetchAll("AuxiliosInfinitoRechazo", " IDAuxiliosInfinitoRechazo = '" . $r["IDAuxiliosInfinitoRechazo"] . "' ", "array");
				}
				$auxilio["NombreRechazo"] = $datos_rech["Nombre"];


				//Respuestas
				$respuesta_aux = array();
				$response_resp_aux = array();
				$sql_resp = "SELECT * From AuxiliosInfinitoRespuesta Where IDAuxiliosInfinitoSolicitud = '" . $r["IDAuxiliosInfinitoSolicitud"] . "' AND IDModulo=  '" . $IDModulo . "'";
				$r_resp = $dbo->query($sql_resp);
				while ($row_resp = $dbo->FetchArray($r_resp)) :
					$datos_preg = $dbo->fetchAll("PreguntaAuxiliosInfinito", " IDPreguntaAuxiliosInfinito = '" . $row_resp["IDPreguntaAuxiliosInfinito"] . "' ", "array");

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
			if (!in_array($IDClub, $arrIDClubLuker)) {
				$message = "LI2. No tiene licencias registradas";
			} else {
				$message = "AX2. No tiene auxilios registrados";
			}
			$respuesta["message"] = $message;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo = "", $File = "", $IDModulo)
	{
		$dbo = &SIMDB::get();
		$FechaFin = '';
		$Tiempo = '';

		// Cambiar club para luker

		$arrIDClubLuker = SIMResources::$arrIDClubLuker;
		if (in_array($IDClub, $arrIDClubLuker)) {
			$FechaHoy = new DateTime('NOW');
			$Hoy = $FechaHoy->format('Y-m-d');

			// Validar que no se solicite despues de 15 dias de cumplir años
			$auxiliosInfinito = $dbo->fetchAll('AuxiliosInfinito', 'IDauxiliosInfinito = ' . $IDAuxilio, 'array');
			if ($auxiliosInfinito['Nombre'] == 'Tarde de cumpleaños') {

				$FechaNacimiento = $dbo->getFields($TipoUsuario, 'FechaNacimiento', 'ID' . $TipoUsuario . '=' . $IDInserta);
				// Ajustamos la fecha de nacimiento a año actual
				$DiaNacimiento = date('Y') . '-' . date('m-d', strtotime($FechaNacimiento));
				$DiaNacimientoActual = $DiaNacimiento;
				$DiaNacimiento = strtotime($DiaNacimiento);
				// Sumamos 15 dias a la fecha de nacimiento para establecer un limite.
				$MaxDiaNacimiento = date('Y-m-d', strtotime($DiaNacimientoActual . "+ 15 days"));
				$MaxDia = strtotime($MaxDiaNacimiento);
				$Hoy = date('Y-m-d');
				$DiaHoy = strtotime($Hoy);

				if ($FechaNacimiento == '0000-00-00') {
					$respuesta["message"] = "Se debe proporcionar una fecha de nacimiento, comuníquese con el area encargada.";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				} elseif ($DiaHoy < $DiaNacimiento) {
					$respuesta["message"] = "Se debe solicitar la licencia durante o después de la fecha de cumpleaños.";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				} elseif ($DiaHoy > $MaxDia) {
					$respuesta["message"] = "Se debe solicitar máximo quince (15) días después de la fecha de cumpleaños.";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				}
			}
			$Respuesta = json_decode($Respuestas);
			$FechaInicio = ($Respuesta[0]->Valor != '') ? $Respuesta[0]->Valor : $Hoy;
			$TipoTiempo = ($auxiliosInfinito['TipoTiempo'] == 'Dias') ? 'Dias' : 'Horas';
			$HoraInicio = ($TipoTiempo == 'Horas' && $Respuesta[1]->Valor != '') ? $Respuesta[1]->Valor : '';

			$SabadoLaboral = $auxiliosInfinito['SabadoLaboral'];
			$TiempoMaximo = $auxiliosInfinito['TiempoMaximo'];
			$Tiempo = ($TipoTiempo == 'Horas') ? $Respuesta[2]->Valor : $Respuesta[1]->Valor;
			if ($TiempoMaximo > 0 && $TiempoMaximo != '') {
				if ($Tiempo > $TiempoMaximo) {
					$respuesta["message"] = "El tiempo solicitado no puede ser mayor a $TiempoMaximo $TipoTiempo";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				} else {
					$ParametrosLicencia['SabadoLaboral'] = $SabadoLaboral;
					$ParametrosLicencia['TipoTiempo'] = $TipoTiempo;
					$Resultado = SIMWebServiceAuxiliosInfinito::get_laboral_calcula_fechafin(8, $FechaInicio, $HoraInicio, $Tiempo, '', '', $IDInserta, '', $ParametrosLicencia);
					if ($Resultado['success']) {
						$FechaFin = $Resultado['response']['Fecha'];
						$FechaReintegro = explode(': ', $Resultado['response']['Mensaje']);
						$FechaReintegro = $FechaReintegro[1];
						$messageLicencia = $Resultado['response']['Mensaje'];

						//Validamos si ya existe una solicitud en las fechas solicitadas.
						$sql_validaFechas = "SELECT * FROM AuxiliosInfinitoSolicitud WHERE IDAuxiliosInfinito = $IDAuxilio AND $CampoUsuario = $IDInserta AND FechaInicio = '$FechaInicio' AND IDEstado IN (1,3,4)";
						$q_validaFechas = $dbo->query($sql_validaFechas);
						$n_validaFechas = $dbo->rows($q_validaFechas);
						if ($n_validaFechas > 0) {
							$respuesta["message"] = "Usted ya ha solicitado una licencia para esta fecha: $FechaInicio";
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						}
					} else {
						$respuesta["message"] = $Resultado["message"];
						$respuesta["success"] = $Resultado['success'];
						$respuesta["response"] = $Resultado["response"];
						return $respuesta;
					}
				}
			}
		}

		//Guardo la solictud
		$inserta_solictud = "INSERT INTO AuxiliosInfinitoSolicitud (IDClub,IDModulo," . $CampoUsuario . ",IDAuxiliosInfinito,IDEstado,FechaInicio,FechaFin,Tiempo,FechaReintegro,UsuarioTrCr,FechaTrCr)
																	VALUES ('" . $IDClub . "','" . $IDModulo . "','" . $IDInserta . "','" . $IDAuxilio . "','1','" . $FechaInicio . "','" . $FechaFin . "','" . $Tiempo . "','" . $FechaReintegro . "','App',NOW())";
		if ($dbo->query($inserta_solictud)) {
			$id_solicitud = $dbo->lastID();

			$sql_pregunta = "SELECT IDPreguntaAuxiliosInfinito, Obligatorio,TipoCampo FROM PreguntaAuxiliosInfinito WHERE IDAuxiliosInfinito = '" . $IDAuxilio . "' ";
			$r_pregunta = $dbo->query($sql_pregunta);
			while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
				$array_pregunta[$row_pregunta["IDPreguntaAuxiliosInfinito"]] = $row_pregunta["Obligatorio"];
				$array_preguntaImage[$row_pregunta["IDPreguntaAuxiliosInfinito"]] = $row_pregunta["TipoCampo"];
			}

			$datos_correctos = "S";
			$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
			$datos_respuesta = json_decode($Respuestas, true);
			if (count($datos_respuesta) > 0) :
				foreach ($datos_respuesta as $detalle_respuesta) {
					if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPreguntaAuxiliosInfinito"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPreguntaAuxiliosInfinito"]] != "imagen") {
						$datos_correctos = "N";
						$PreguntaValida = $detalle_respuesta["IDPreguntaAuxiliosInfinito"];
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
					$sql_datos_form = "INSERT INTO AuxiliosInfinitoRespuesta (IDAuxiliosInfinitoSolicitud,IDModulo, IDAuxiliosInfinito, " . $CampoUsuario . ", IDPreguntaAuxiliosInfinito,  TipoUsuario, Valor, FechaTrCr)
																						Values ('" . $id_solicitud . "','" . $IDModulo . "','" .  $IDAuxilio . "','" . $IDInserta . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())";
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
						$actualiza_pregunta = "UPDATE AuxiliosInfinitoRespuesta SET Valor = '" . $Archivo . "' WHERE IDPreguntaAuxiliosInfinito ='" . $IDPreguntaActualiza . "' and IDAuxiliosInfinito  = '" . $IDAuxilio . "' and " . $CampoUsuario . " = '" . $IDInserta . "'";
						$dbo->query($actualiza_pregunta);
						$nombrefoto .= $actualiza_pregunta;
					}
				}
			}

			// Registro en LUKER_SOLI_VAC para notificaciones
			// Cambiar club para luker
			$arrIDClubLuker = SIMResources::$arrIDClubLuker;
			if (in_array($IDClub, $arrIDClubLuker)) {
				$tabla = ($TipoUsuario == 'Socio') ? "Socio" : "Usuario";
				$NumeroDocumentoSolicitante = $dbo->getFields($tabla, "NumeroDocumento", "ID$tabla = $IDInserta");
				$conn = SIMUtil::ConexionBDLuker();
				$sql = "SELECT CODIGO FROM EMPLEADOS_APPS WHERE CEDULA = " . $NumeroDocumentoSolicitante . "  ";
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

				$sqlInsertLuker = "INSERT INTO LUKER_SOLIC_LIC (EMP_CODIGO,TAUS_CODIGO,RAUS_FEC_DESDE,RAUS_ESTADO) VALUES ('" . $row_vac['CODIGO'] . "'," . $auxiliosInfinito['CodigoAusencia'] . ",to_date('" . $FechaInicio . "','YYYY-MM-DD'),'Pend1')";
				$stmt = $conn->prepare($sqlInsertLuker);
				$stmt->execute();
				// Fin Resgitro en la tabla LUKERLICENCIAS de Luker
			}
			//Fin Registro en LUKER_SOLI_VAC para notificaciones
			SIMUtil::notificar_nueva_AuxiliosSolicitud_infinito($id_solicitud);
		}

		if (in_array($IDClub, $arrIDClubLuker) && $TipoTiempo == 'Dias') {
			$RespuestaEncuestaArbol = $messageLicencia;
		} else {
			$RespuestaEncuestaArbol = "Guardado con exito.";
		}
		// $RespuestaEncuestaArbol = "Guardado con exito." . $nombrefoto;
		$respuesta["message"] = $RespuestaEncuestaArbol;
		$respuesta["success"] = true;
		$respuesta["response"] = NULL;
		return $respuesta;
	}
	function set_auxilio_infinito($IDClub, $IDSocio, $IDAuxilio, $Respuestas, $IDUsuario, $Archivo = "", $File = "", $IDModulo)
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

			$sql_Auxilios = "SELECT Periocidad FROM AuxiliosInfinito WHERE IDAuxiliosInfinito = " . $IDAuxilio;
			$query_Auxilios = $dbo->query($sql_Auxilios);
			$row_Auxilios = $dbo->assoc($query_Auxilios);

			$sql_AuxiliosSolicitud = "SELECT FechaTrCr FROM AuxiliosInfinitoSolicitud WHERE IDAuxiliosInfinito = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " ORDER BY FechaTrCr DESC LIMIT 1 ";
			$query_AuxiliosSolicitud = $dbo->query($sql_AuxiliosSolicitud);
			$num_AuxiliosSolicitud = $dbo->rows($query_AuxiliosSolicitud);
			$row_AuxiliosSolicitud = $dbo->assoc($query_AuxiliosSolicitud);


			$periocidad = $row_Auxilios['Periocidad'];
			$hoy = date('Y-m-d H:i:s');
			$fechaSolicitud = $row_AuxiliosSolicitud['FechaTrCr'];




			if ($num_AuxiliosSolicitud > 0) {
				if ($periocidad == 'Único pago') {

					$respuesta["message"] = "Solo puedes solicitar una vez este Tiempo";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				} elseif ($periocidad == '2 veces al año') {
					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosInfinitoSolicitud FROM AuxiliosInfinitoSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxiliosInfinito = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 2) {
						$respuesta["message"] = "Solo puedes solicitar este Tiempo 2 veces al año";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxiliosInfinito;
						$respuesta = $obj->save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File, $IDModulo);
					}
				} elseif ($periocidad == 'Anual') {

					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosInfinitoSolicitud FROM AuxiliosInfinitoSolicitud WHERE IDClub = '" . $IDClub . "' AND IDAuxiliosInfinito = " . $IDAuxilio . " AND " . $CampoUsuario . " = " . $IDInserta . " AND  FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 1) {
						$respuesta["message"] = "Solo puedes solicitar este Tiempo una vez al año.";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxiliosInfinito;
						$respuesta = $obj->save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File, $IDModulo);
					}
				} elseif ($periocidad == 'Una vez por tipo de auxilio al año') {

					$date = strtotime(date('Y-m-d'));
					$ano = date('Y', $date);
					$inicioAnio = $ano . '-01-01';

					$TipoAuxilio = $dbo->getFields('Auxilios', 'IDTipoAuxilio', 'IDAuxilios="' . $IDAuxilio . '"');

					$s_AuxiliosSolicitud = "SELECT IDAuxiliosInfinitoSolicitud FROM AuxiliosInfinitoSolicitud as AuS,AuxiliosInfinito as A WHERE AuS.IDAuxiliosInfinito=A.IDAuxiliosInfinito AND  AuS.IDClub = '" . $IDClub . "' AND A.IDTipoAuxilio = '" . $TipoAuxilio . "' AND " . $CampoUsuario . " = " . $IDInserta . " AND  AuS.FechaTrCr BETWEEN '" . $inicioAnio . "' AND NOW() AND AuS.IDEstado IN (1,3) ";
					$q_AuxiliosSolicitud = $dbo->query($s_AuxiliosSolicitud);
					$n_AuxiliosSolicitud = $dbo->rows($q_AuxiliosSolicitud);
					if ($n_AuxiliosSolicitud >= 1) {
						$respuesta["message"] = "Solo puedes solicitar este Tipo de Tiempo una vez al año.";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					} else {
						$obj = new SIMWebServiceAuxiliosInfinito;
						$respuesta = $obj->save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File, $IDModulo);
					}
				} elseif ($periocidad == 'Acontecimiento (Nacimiento,muerte,etc)') {
					$obj = new SIMWebServiceAuxiliosInfinito;
					$respuesta = $obj->save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File, $IDModulo);
				}
			} else {

				$obj = new SIMWebServiceAuxiliosInfinito;
				$respuesta = $obj->save_AuxiliosSolicitud_infinito($CampoUsuario, $IDInserta, $TipoUsuario, $IDClub, $IDAuxilio, $Respuestas, $Archivo, $File, $IDModulo);
			}
		} else {
			$respuesta["message"] = "A6. Atención faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}


	function set_respuesta_solicitud_auxilio_infinito($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $IDTipoRechazo, $Comentarios, $IDModulo)
	{
		$dbo = &SIMDB::get();

		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSolicitud) && !empty($Aprueba)) {
			$ConfiguracionAuxiliosInfinito = $dbo->fetchAll('ConfiguracionAuxiliosInfinitoSolicitud', "IDClub = $IDClub and IDModulo = $IDModulo", 'array');
			$AuxiliosInfinitoSolicitud = $dbo->fetchAll('AuxiliosInfinitoSolicitud', 'IDAuxiliosInfinitoSolicitud = ' . $IDSolicitud, 'array');
			$CodigoAusencia = $dbo->getFields('AuxiliosInfinito', 'CodigoAusencia', 'IDAuxiliosInfinito = ' . $AuxiliosInfinitoSolicitud['IDAuxiliosInfinito']);

			if ($AuxiliosInfinitoSolicitud['IDSocio'] > 0) {
				$NumeroDocumentoSolicitante = $dbo->getFields('Socio', 'NumeroDocumento', 'IDSocio = ' . $AuxiliosInfinitoSolicitud['IDSocio']);
				$tabla = "Socio";
				$campo = "IDSocio";
				$IDCodigo = $AuxiliosInfinitoSolicitud['IDSocio'];
			} else {
				$NumeroDocumentoSolicitante = $dbo->getFields('Usuario', 'NumeroDocumento', 'IDUsuario = ' . $AuxiliosInfinitoSolicitud['IDUsuario']);
				$tabla = "Usuario";
				$campo = "IDUsuario";
				$IDCodigo = $AuxiliosInfinitoSolicitud['IDUsuario'];
			}

			// Validamos si quien aprueba es Socio o Jefe
			if ($IDSocio > 0) {
				$NumeroDocumento = $dbo->getFields('Socio', 'NumeroDocumento', " IDClub = $IDClub and IDSocio = " . $IDSocio);
			} else {
				$NumeroDocumento = $dbo->getFields('Usuario', 'NumeroDocumento', " IDClub = $IDClub and IDUsuario = " . $IDUsuario);
			}
			// Fin Validamos si quien aprueba es Socio o Jefe
			if ($ConfiguracionAuxiliosInfinito['GestionDeUnAprobador'] == 'S') {
				if ($ConfiguracionAuxiliosInfinito['NumeroDocumentoUnicoAprobador'] == $NumeroDocumento) {
					$r_Jefe = 1;
				} else {
					$Respuesta['message'] = "No se encontraron solicitudes";
					$Respuesta['success'] = false;
					$Respuesta['response'] = "";
					return $Respuesta;
				}
			} else {
				$sqlJefe = "SELECT IDSocio FROM Socio s LEFT JOIN Usuario u ON s.NumeroDocumento = u.NumeroDocumento WHERE s.IDClub = $IDClub AND (s.DocumentoJefe =  $NumeroDocumento OR u.DocumentoJefe =  $NumeroDocumento)";

				$sqlAprobador = "SELECT IDUsuario FROM Socio s LEFT JOIN Usuario u ON s.NumeroDocumento = u.NumeroDocumento WHERE s.IDClub = $IDClub AND (s.DocumentoJefe =  $NumeroDocumento OR u.DocumentoJefe =  $NumeroDocumento)";

				$q_Jefe = $dbo->query($sqlJefe);
				$q_Aprobador = $dbo->query($sqlAprobador);
				$r_Jefe = $dbo->rows($q_Jefe);
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
			// Cambiar club para luker
			$arrIDClubLuker = SIMResources::$arrIDClubLuker;

			if ($r_Jefe > 0) {
				if ($Aprueba == "S") {
					if (in_array($IDClub, $arrIDClubLuker)) {
						$IDEstado = 4;
						$EstadoLuker = "Pend2";
					} else {
						$IDEstado = 3;
					}
					$Aprobado = 1;
				} else {
					$IDEstado = 2;
					$Aprobado = 0;
					$EstadoLuker = "Rechazada";
				}

				$sql_solic = "UPDATE AuxiliosInfinitoSolicitud
														 SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',Aprobado='" . $Aprueba . "',IDAuxiliosInfinitoRechazo='" . $IDTipoRechazo . "', ApruebaJefe = $Aprobado,
														 Comentarios='" . $Comentarios . "',FechaRevision=NOW(),FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDAuxiliosInfinitoSolicitud = '" . $IDSolicitud . "'	";
				if ($dbo->query($sql_solic)) {
					if ($Aprobado == 1) {
						SIMUtil::notificar_nueva_AuxiliosSolicitud_infinito($IDSolicitud, 'Aprobador');
					} else {
						SIMUtil::notificar_respuesta_AuxilioSolicitud_infinito($IDSolicitud, $Comentarios);
					}

					// Registro en LUKER_SOLI_LIC para notificaciones
					// Cambiar club para luker
					$arrIDClubLuker = [95, 96, 97, 98, 122];
					if (in_array($IDClub, $arrIDClubLuker)) {
						$conn = SIMUtil::ConexionBDLuker();
						$sql = "SELECT CODIGO FROM EMPLEADOS_APPS WHERE CEDULA = " . $NumeroDocumentoSolicitante;
						$stmt = $conn->prepare($sql);
						$stmt->execute();
						$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

						$sqlUpdateLuker = "UPDATE LUKER_SOLIC_LIC SET RAUS_ESTADO = '$EstadoLuker' WHERE EMP_CODIGO = '" . $row_vac['CODIGO'] . "' AND TAUS_CODIGO = $CodigoAusencia";
						$stmt = $conn->prepare($sqlUpdateLuker);
						$stmt->execute();
					}
					//Fin Registro en LUKER_SOLI_LIC para notificaciones
					$respuesta["message"] = "Registro exitoso";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
				}
			} else {
				if ($Aprueba == "S") {
					$IDEstado = 3;
					$Aprobado = 1;
					$EstadoLuker = "Aprobada";
					if (in_array($IDClub, $arrIDClubLuker)) {
						if ($AuxiliosInfinitoSolicitud['ApruebaJefe'] == 1 && $Aprueba == 'S') {
							// Resgitro en la tabla LUKERLICENCIAS de Luker
							if (in_array($IDClub, $arrIDClubLuker)) {
								$IDEstado = 3;

								$conn = SIMUtil::ConexionBDLuker();
								$sql = "SELECT CODIGO FROM EMPLEADOS_APPS WHERE CEDULA = " . $NumeroDocumentoSolicitante . "  ";
								$stmt = $conn->prepare($sql);
								$stmt->execute();
								$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

								$AuxiliosInfinitoSolicitud['FechaInicio'] = date('d-m-Y', strtotime($AuxiliosInfinitoSolicitud['FechaInicio']));
								$AuxiliosInfinitoSolicitud['FechaFin'] = date('d-m-Y', strtotime($AuxiliosInfinitoSolicitud['FechaFin']));
								$Estado = 'APR';
								$sqlUpdate = "INSERT INTO LUKERLICENCIAS (RAUS_CONSEC,EMP_CODIGO,TAUS_CODIGO,RAUS_FEC_DESDE,RAUS_FEC_HASTA,RAUS_UNIDADES,RAUS_COMENTARIO,REP_ESTADO) VALUES (" . $IDSolicitud . ",'" . $row_vac['CODIGO'] . "'," . $CodigoAusencia . ",to_date('" . $AuxiliosInfinitoSolicitud['FechaInicio'] . "','DD-MM-YYYY'), to_date('" . $AuxiliosInfinitoSolicitud['FechaFin'] . "','DD-MM-YYYY')," . $AuxiliosInfinitoSolicitud['Tiempo'] . ",'" . $Comentarios . "','" . $Estado . "')";
								$stmt = $conn->prepare($sqlUpdate);
								$stmt->execute();
								// Fin Resgitro en la tabla LUKERLICENCIAS de Luker
							} else {
								$IDEstado = 3;
							}
						} else {
							$IDEstado = 2;
						}
					} else {
						if ($Aprueba == 'S') {
							$IDEstado = 3;
						} else {
							$IDEstado = 2;
						}
					}
					// Fin Resgitro en la tabla LUKERLICENCIAS de Luker

				} else {
					$IDEstado = 2;
					$Aprobado = 0;
					$EstadoLuker = "Rechazada";
				}

				$sql_solic = "UPDATE AuxiliosInfinitoSolicitud
														 SET IDEstado = '" . $IDEstado . "',IDAuxiliosInfinitoRechazo='" . $IDTipoRechazo . "',
														 ComentarioAprobador='" . $Comentarios . "',ApruebaAprobador = $Aprobado,FechaCambioEstadoAprobador=NOW(),FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDAuxiliosInfinitoSolicitud = '" . $IDSolicitud . "'	";
				if ($dbo->query($sql_solic)) {
					SIMUtil::notificar_respuesta_AuxilioSolicitud_infinito($IDSolicitud, $Comentarios);

					// Registro en LUKER_SOLI_LIC para notificaciones
					// Cambiar club para luker
					$arrIDClubLuker = [95, 96, 97, 98, 122];
					if (in_array($IDClub, $arrIDClubLuker)) {
						$NumeroDocumentoSolicitante = $dbo->getFields($tabla, "NumeroDocumento", "$campo = $IDCodigo");
						$conn = SIMUtil::ConexionBDLuker();
						$sql = "SELECT CODIGO FROM EMPLEADOS_APPS WHERE CEDULA = " . $NumeroDocumentoSolicitante . "  ";
						$stmt = $conn->prepare($sql);
						$stmt->execute();
						$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

						$sqlUpdateLuker = "UPDATE LUKER_SOLIC_LIC SET RAUS_ESTADO = '$EstadoLuker' WHERE EMP_CODIGO = '" . $row_vac['CODIGO'] . "' AND TAUS_CODIGO = $CodigoAusencia";
						$stmt = $conn->prepare($sqlUpdateLuker);
						$stmt->execute();
					}
					//Fin Registro en LUKER_SOLI_LIC para notificaciones
					$respuesta["message"] = "Registro exitoso";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
				}
			}
		} else {
			$respuesta["message"] = "AX8. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}
	function get_laboral_calcula_fechafin($IDClub, $fechaInicial, $HoraInicial = "", $Tiempo, $DiasDinero = "", $DiasNormales = "", $IDSocio = "", $IDUsuario = "", $ParametrosLicencia = "")
	{
		$dbo = &SIMDB::get();
		$response = array();
		//Identificar los días
		$diasNoLaborales = [];
		$diasNoLaborales[] = "Sunday";

		//Consulta si el día sabado es laboral
		$ConfiguracionLaboral = $dbo->query("SELECT SabadoDialaboral, PermiteVacacionesDiasDinero, DiasMinDinero,DiasMaxDinero FROM ConfiguracionLaboral WHERE IDClub=" . $IDClub . " AND ACTIVO='S' Limit 1");
		$r_ConfiguracionLaboral = $dbo->assoc($ConfiguracionLaboral);
		if ($r_ConfiguracionLaboral['SabadoDialaboral'] == "N") {
			$diasNoLaborales[] = "Saturday";
		}
		// Cambiar club para luker

		$arrIDClubLuker = [95, 96, 97, 98, 122, 169];
		if (in_array($IDClub, $arrIDClubLuker)) {
			if (!empty($IDSocio && $IDSocio != "")) {
				$DocumentoSolicitante = $dbo->getFields('Socio', 'NumeroDocumento', 'IDSocio = ' . $IDSocio);
			} else {
				$DocumentoSolicitante = $dbo->getFields('Usuario', 'NumeroDocumento', 'IDUsuario = ' . $IDUsuario);
			}
			$tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . HOST_LUKER . ")(PORT = " . PORT_LUKER . ")))
                        (CONNECT_DATA = (SERVICE_NAME = " . BASE_LUKER . ")))";
			try {
				$conn = new PDO("oci:dbname=" . $tns, USER_LUKER, PASSWORD_LUKER);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
			}
			$sql = "SELECT EMP_TIP_CAL FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $DocumentoSolicitante . "  ";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			while ($row_vac = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$TipoCalendario = $row_vac['EMP_TIP_CAL'];
			}

			if ($TipoCalendario == 1) {
				$diasNoLaborales[] = "Saturday";
			} else {
				unset($diasNoLaborales);
				$diasNoLaborales[] = "Sunday";
			}
		}
		if ($ParametrosLicencia['SabadoLaboral' == 'S']) {
			$diasNoLaborales[] = "Saturday";
		} else {
			unset($diasNoLaborales);
			$diasNoLaborales[] = "Sunday";
		}

		//Identificar los días festivos
		$queryPais = $dbo->query("SELECT P.IDPais FROM ConfiguracionClub C INNER JOIN Pais P ON C.IDPais = P.IDPais WHERE C.IDClub=" . $IDClub);
		$IDPais = $dbo->fetch($queryPais);
		$queryFestivo = $dbo->query("SELECT Fecha FROM Festivos WHERE IDPais=" . $IDPais['IDPais']);
		$fechasNoLaborales = [];
		while ($festivos = $dbo->assoc($queryFestivo)) {
			$fechasNoLaborales[] = date("Y-m-d", strtotime($festivos["Fecha"]));
		}
		//Fin identificar los días festivos
		if (isset($festivos["Fecha"])) {
			$fechasNoLaborales[] = date("Y-m-d", strtotime($festivos["Fecha"]));
		} else {
			foreach ($festivos as $value) {
				$fechasNoLaborales[] = date("Y-m-d", strtotime($value["Fecha"]));
			}
		}
		//Fin identificar los días festivos

		$HoraInicial = ((!empty($HoraInicial))) ? $HoraInicial : '00:00:00';
		$fechaHoy = date("Y-m-d", strtotime($fechaInicial));
		$diaSemana = date("l", strtotime($fechaHoy));


		if (in_array($diaSemana, $diasNoLaborales) || in_array($fechaHoy, $fechasNoLaborales)) {
			$calcula_fecha["Fecha"] = "0000-00-00";
			$calcula_fecha["Mensaje"] = "Día no hábil para iniciar vacaciones";
			$calcula_fecha["DiasMaxDinero"] = intval(0);
			$calcula_fecha["DiasMinDinero"] = intval(0);

			$respuesta["message"] = "Día no hábil";
			$respuesta["success"] = false;
			$respuesta["response"] = $calcula_fecha;
			return $respuesta;
		} else {
			$diasUsados = 0;
			$fechaSiguiente = date("Y-m-d", strtotime($fechaInicial)) . ' ' . date("H:i:s", strtotime($HoraInicial));

			$DiasSolicitados = ($DiasNormales > 0) ? $DiasNormales : $Tiempo;
			while ($diasUsados < $DiasSolicitados) {

				$fechaHoy = date("Y-m-d H:i:s", strtotime($fechaSiguiente));
				$diaHoy = explode(' ', $fechaHoy);
				$diaSemana = date("l", strtotime($fechaHoy));
				if (!in_array($diaSemana, $diasNoLaborales) && !in_array($diaHoy[0], $fechasNoLaborales)) {
					if ($ParametrosLicencia['TipoTiempo'] == 'Dias') {
						$diasUsados++;
					} else {
						$diasUsados += $Tiempo;
					}
				}
				if ($ParametrosLicencia['TipoTiempo'] == 'Dias') {
					$fechaSiguiente = date("Y-m-d H:i:s", strtotime($fechaHoy . "+ 1 days"));
				} else {
					$fechaSiguiente = date("Y-m-d H:i:s", strtotime($fechaHoy . "+ $Tiempo hour"));
				}
			}
			$fechaFin = ($ParametrosLicencia['TipoTiempo'] == 'Dias') ? date('Y-m-d H:i:s', strtotime($fechaHoy)) : date('Y-m-d H:i:s', strtotime($fechaSiguiente));
			if ($ParametrosLicencia['TipoTiempo'] == 'Dias') {
				$fechaSiguiente = date("Y-m-d H:i:s", strtotime($fechaHoy . "+ 1 days"));
				$diaRetorno = date("l", strtotime($fechaSiguiente));
				while (in_array($diaRetorno, $diasNoLaborales)) {
					$fechaSiguiente = date("Y-m-d H:i:s", strtotime($fechaSiguiente . "+ 1 days"));
					$diaRetorno = date("l", strtotime($fechaSiguiente));
				}
			}
			$calcula_fecha["Fecha"] = $fechaFin;
			$fechaRegreso = ($ParametrosLicencia['TipoTiempo'] == 'Dias') ? date('Y-m-d', strtotime($fechaSiguiente)) : $fechaFin;
			$calcula_fecha["Mensaje"] = "Vuelve al trabajo el dia: " . $fechaRegreso;
			if ($r_ConfiguracionLaboral['PermiteVacacionesDiasDinero'] != 'S') {
				$calcula_fecha["DiasMaxDinero"] = intval(0);
				$calcula_fecha["DiasMinDinero"] = intval(0);
			} else {

				$diasMaxDinero = ($DiasNormales > 1) ? round($DiasNormales - 1) : 0;
				$diasMinDinero = ($DiasSolicitados > 1) ? $r_ConfiguracionLaboral['DiasMinDinero'] : 0;
				$calcula_fecha["DiasMaxDinero"] = intval($diasMaxDinero);
				$calcula_fecha["DiasMinDinero"] = intval($diasMinDinero);
			}
			$respuesta["message"] = "Fecha Calculada";
			$respuesta["success"] = true;
			$respuesta["response"] = $calcula_fecha;
			return $respuesta;
		}
	}
} //end class
