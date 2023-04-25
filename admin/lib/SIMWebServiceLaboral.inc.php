<?php
class SIMWebServiceLaboral
{

	function get_configuracion_laboral($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDUsuario)) {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
		} elseif (!empty($IDSocio)) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		}

		$sql = "SELECT *
							FROM ConfiguracionLaboral
							WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$configuracion["IDClub"] = $IDClub;
				$configuracion["Vacaciones"] = $r["Vacaciones"];
				$configuracion["VacacionesIcono"] = CLUB_ROOT . $r["VacacionesIcono"];
				$configuracion["VacacionesNombre"] = $r["VacacionesNombre"];
				$configuracion["VacacionesOrden"] = $r["VacacionesOrden"];
				$configuracion["Compensatorio"] = $r["Compensatorio"];
				$configuracion["CompensatorioIcono"] = CLUB_ROOT . $r["CompensatorioIcono"];
				$configuracion["CompensatorioNombre"] = $r["CompensatorioNombre"];
				$configuracion["CompesatorioOrden"] = $r["CompesatorioOrden"];
				$configuracion["Permisos"] = $r["Permisos"];
				$configuracion["PermisosIcono"] = CLUB_ROOT . $r["PermisosIcono"];
				$configuracion["PermisosNombre"] = $r["PermisosNombre"];
				$configuracion["PermisosOrden"] = $r["PermisosOrden"];
				$configuracion["Extracto"] = $r["Extracto"];
				$configuracion["ExtractoIcono"] = CLUB_ROOT . $r["ExtractoIcono"];
				$configuracion["ExtractoNombre"] = $r["ExtractoNombre"];
				$configuracion["ExtractoOrden"] = $r["ExtractoOrden"];
				$configuracion["Certificado"] = $r["Certificado"];
				$configuracion["CertificadoIcono"] = CLUB_ROOT . $r["CertificadoIcono"];
				$configuracion["CertificadoNombre"] = $r["CertificadoNombre"];
				$configuracion["CertificadoOrden"] = $r["CertificadoOrden"];
				$configuracion["LabelMisPermisos"] = $r["LabelMisPermisos"];
				$configuracion["AprobarVacacionesNombre"] = $r["AprobarVacacionesNombre"];
				$configuracion["AprobarVacaciones"] = $datos_usuario["AprobarVacaciones"];
				$configuracion["AprobarVacacionesIcono"] = CLUB_ROOT . $r["AprobarVacacionesIcono"];
				$configuracion["AprobarVacacionesOrden"] = $r["AprobarVacacionesOrden"];
				$configuracion["BotonFiltroAprobarVacacionesTexto"] = $r["BotonFiltroAprobarVacacionesTexto"];
				$configuracion["LabelAprobarSolicitudes"] = $r["LabelAprobarSolicitudes"];
				$configuracion["CampoTipoRechazoActivo"] = $r["CampoTipoRechazoActivo"];
				$configuracion["CampoComentarioObligatorio"] = $r["CampoComentarioObligatorio"];
				$configuracion["PermiteFotoArchivoCompensatorio"] = $r["PermiteFotoArchivoCompensatorio"];
				$configuracion["ObligatorioFotoArchivoCompensatorio"] = $r["ObligatorioFotoArchivoCompensatorio"];
				$configuracion["LabelFotoArchivoCompensatorio"] = $r["LabelFotoArchivoCompensatorio"];
				$configuracion["LabelEstadoPendienteCompensatorio"] = $r["LabelEstadoPendienteCompensatorio"];
				$configuracion["LabelEstadoPendienteVacaciones"] = $r["LabelEstadoPendienteVacaciones"];
				$configuracion["LabelEstadoPendientePermisos"] = $r["LabelEstadoPendientePermisos"];
				$configuracion["LabelEstadoPendienteCertificado"] = $r["LabelEstadoPendienteCertificado"];


				//Reviso las vacaciones y compensatorios que tiene disponible
				$response_vacaciones = array();
				if (!empty($IDSocio)) {
					$Campo = "IDSocio";
					$Valor = $IDSocio;
				} else {
					$Campo = "IDUsuario";
					$Valor = $IDUsuario;
				}
				$dias_compensatorio = 0;
				if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
					$SocioDocumento = $dbo->getFields('Socio', 'NumeroDocumento', 'IDSocio = ' . $IDSocio);
					if ($SocioDocumento) {
						$tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . HOST_LUKER . ")(PORT = " . PORT_LUKER . ")))
					(CONNECT_DATA = (SERVICE_NAME = " . BASE_LUKER . ")))";
						try {
							$conn = new PDO("oci:dbname=" . $tns, USER_LUKER, PASSWORD_LUKER);
							$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						} catch (PDOException $e) {
							echo 'ERROR: ' . $e->getMessage();
						}
						$sql = "SELECT * FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $SocioDocumento . "  ";
						$stmt = $conn->prepare($sql);
						$stmt->execute();
						while ($row_vac = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$vac["Periodo"] = $row_vac["PERIODO_INI"] . ' / ' . $row_vac["PERIODO_FIN"];
							$vac["Dias"] = $row_vac["DIAS_PENDIENTES"];
							$dias_compensatorio += $row_vac["DIAS_PENDIENTES"];
							array_push($response_vacaciones, $vac);
						}
					}
					$configuracion["VacacionesPeriodo"] = $response_vacaciones;
					$configuracion["DiasCompensatorio"] = $dias_compensatorio;
					$configuracion["PermiteVacacionesDiasDinero"] = $r['PermiteVacacionesDiasDinero'];
					$configuracion["LabelVacacionesDiasDinero"] = $r['LabelVacacionesDiasDinero'];
					$configuracion["LabelVacacionesDiasNormales"] = $r['LabelVacacionesDiasNormales'];
				} else {

					$sql_vac = "SELECT Periodo,Dias,DiasCompensatorio
											FROM  LaboralVacacionesPendientes
											WHERE " . $Campo . "= '" . $Valor . "' ";
					$r_vac = $dbo->query($sql_vac);
					while ($row_vac = $dbo->fetchArray($r_vac)) {
						$vac["Periodo"] = $row_vac["Periodo"];
						$vac["Dias"] = $row_vac["Dias"];
						$dias_compensatorio += $row_vac["DiasCompensatorio"];
						array_push($response_vacaciones, $vac);
					}
					$configuracion["VacacionesPeriodo"] = $response_vacaciones;
					$configuracion["DiasCompensatorio"] = $dias_compensatorio;
				}

				$response_motivo_permiso = array();
				foreach (SIMResources::$motivo_laboral as $id_motivo => $motivo) {
					$motivos["IDMotivo"] = $id_motivo;
					$motivos["Motivo"] = $motivo;
					array_push($response_motivo_permiso, $motivos);
				}
				$configuracion["MotivoPermiso"] = $response_motivo_permiso;

				$response_tipo_certificado = array();
				// inhabilitar tipos de certificados para el club medellin
				if ($IDClub == 20) {
					$arrTipoCertificado = array(2, 3, 5);
				} else {
					$arrTipoCertificado = array();
				}
				foreach (SIMResources::$tipo_certificado_laboral as $id_certif => $certif) {
					if (!in_array($id_certif, $arrTipoCertificado)) {
						$certificado["IDTipoCertificado"] = $id_certif;
						$certificado["Certificado"] = $certif;
						array_push($response_tipo_certificado, $certificado);
					}
				}
				$configuracion["TipoCertificado"] = $response_tipo_certificado;

				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "L1. No se encontraron registros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function get_filtro_solicitudes_laborales_por_aprobar($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$message = $dbo->rows($qry) . " Encontrados";

		$configuracion["IDFiltro"] = "1";
		$configuracion["Nombre"] = "Abiertas";
		$configuracion["Descripcion"] = "Solicitudes aprobadas";
		array_push($response, $configuracion);

		$configuracion["IDFiltro"] = "2";
		$configuracion["Nombre"] = "Aprobadas";
		$configuracion["Descripcion"] = "Solicitudes aprobadas";
		array_push($response, $configuracion);


		$respuesta["message"] = $message;
		$respuesta["success"] = true;
		$respuesta["response"] = $response;


		return $respuesta;
	} // fin function


	function get_solicitudes_laborales_por_aprobar($IDClub, $IDSocio, $IDUsuario, $IDFiltro)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$Totalregistros = 0;

		$response_modulo = array();
		if (!empty($IDUsuario)) {
			$NumeroDocumento = $dbo->getFields("Usuario", "NumeroDocumento", " IDUsuario = '" . $IDUsuario . "' ");
			$sql_Jefe = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . $IDClub . "' AND DocumentoJefe = " . $NumeroDocumento;
			$q_Jefe = $dbo->query($sql_Jefe);
			$n_Jefe = $dbo->rows($q_Jefe);

			$sql_Especialista = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . $IDClub . "' AND DocumentoEspecialista = " . $NumeroDocumento;
			$q_Especialista = $dbo->query($sql_Especialista);
			$n_Especialista = $dbo->rows($q_Especialista);
		} elseif (!empty($IDSocio)) {
			$NumeroDocumento = $dbo->getFields("Socio", "NumeroDocumento", " IDSocio = '" . $IDSocio . "' ");
			$sql_Jefe = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' AND DocumentoJefe = " . $NumeroDocumento;
			$q_Jefe = $dbo->query($sql_Jefe);
			$n_Jefe = $dbo->rows($q_Jefe);

			$sql_Especialista = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' AND DocumentoEspecialista = " . $NumeroDocumento;
			$q_Especialista = $dbo->query($sql_Especialista);
			$n_Especialista = $dbo->rows($q_Especialista);
		} else {
			exit;
		}

		// Configuracion laboral
		$FiltrarPorJefe = $dbo->getFields('ConfiguracionLaboral', "FiltrarPorJefe", "IDClub = $IDClub");

		//Compensatorio
		$response_compen = array();
		if ($FiltrarPorJefe != 'S') {
			$sql = "SELECT * FROM LaboralCompensatorio WHERE IDClub = $IDClub AND  IDEstado=1  ORDER BY FechaTrCr DESC";
		} else {
			$sql = "SELECT l.* FROM LaboralCompensatorio l LEFT JOIN Socio s ON l.IDSocio=s.IDSocio LEFT JOIN Usuario u ON l.IDUsuario=u.IDUsuario WHERE l.IDClub = $IDClub AND (u.DocumentoJefe = " . $NumeroDocumento . " OR u.DocumentoEspecialista = " . $NumeroDocumento . " OR s.DocumentoJefe= " . $NumeroDocumento . " OR s.DocumentoEspecialista=" . $NumeroDocumento . ") AND  l.IDEstado=1  ORDER BY FechaTrCr DESC";
		}

		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$compensa["Modulo"] = "Compensatorio";
			if ($r["IDEstado"] == 1) {
				$compensa["Tipo"] = "Pendientes";
			} else {
				$compensa["Tipo"] = "Realizados";
			}

			if ($r["IDUsuario"] > 0) {
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array");
				$TipoCampo = "Usuario";
				$IdUser = "IDUsuario";
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"];
				if (!empty($datos_usuario["Foto"])) {
					$foto = USUARIO_ROOT . $datos_usuario["Foto"];
				}
			} elseif ($r["IDSocio"] > 0) {
				$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array");
				$TipoCampo = "Socio";
				$IdUser = "IDSocio";
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
				if (!empty($datos_usuario["Foto"])) {
					$foto = SOCIO_ROOT . $datos_usuario["Foto"];
				}
			}

			$fechaFin = SIMWebServiceLaboral::get_laboral_calcula_fechafin($IDClub, $r["FechaInicio"], $r["DiasTomar"]);

			$compensa["IDSolicitud"] = $r["IDLaboralCompensatorio"];
			$compensa["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$compensa["FechaInicio"] = $r["FechaInicio"];
			$compensa["DiasTomar"] = $r["DiasTomar"];
			$compensa["FechaFin"] = $fechaFin['response']['Fecha'];
			$compensa["Comentario"] = $r["Comentario"];
			$compensa["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$compensa["FechaAprobacion"] = $r["FechaAprobacion"];
			$compensa["Solicitante"] = $Solicitante;
			$compensa["Cargo"] = "Solictud Compensatorio";
			$compensa["Foto"] = $foto;
			$compensa["Archivo"] = $r["Archivo"] != "" ? LABORAL_ROOT . $r["Archivo"] : "";
			$compensa["DetalleHtml"] = "detalle compensa";
			array_push($response_modulo, $compensa);
			unset($foto);
			unset($datos_usuario);
		} //end while
		$solicitudes["Compensatorio"] = $response_compen;

		//Permisos
		$response_permiso = array();
		if ($FiltrarPorJefe != 'S') {
			$sql = "SELECT LaboralPermiso.* FROM LaboralPermiso WHERE IDClub = $IDClub AND   IDEstado=1  ORDER BY FechaTrCr DESC";
		} else {
			$sql = "SELECT l.* FROM LaboralPermiso l LEFT JOIN Socio s ON l.IDSocio=s.IDSocio LEFT JOIN Usuario u ON l.IDUsuario=u.IDUsuario WHERE l.IDClub = $IDClub AND (u.DocumentoJefe = " . $NumeroDocumento . " OR u.DocumentoEspecialista = " . $NumeroDocumento . " OR s.DocumentoJefe= " . $NumeroDocumento . " OR s.DocumentoEspecialista=" . $NumeroDocumento . ") AND   l.IDEstado=1  ORDER BY FechaTrCr DESC";
		}
		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$permiso["Modulo"] = "Permisos";
			if ($r["IDEstado"] == 1) {
				$permiso["Tipo"] = "Pendientes";
			} else {
				$permiso["Tipo"] = "Realizados";
			}

			if ($r["IDUsuario"] > 0) {
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array");
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"];
				if (!empty($datos_usuario["Foto"])) {
					$foto = USUARIO_ROOT . $datos_usuario["Foto"];
				}
			} elseif ($r["IDSocio"] > 0) {
				$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array");
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
				if (!empty($datos_usuario["Foto"])) {
					$foto = SOCIO_ROOT . $datos_usuario["Foto"];
				}
			}

			$permiso["IDSolicitud"] = $r["IDLaboralPermiso"];
			$permiso["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$permiso["FechaInicio"] = $r["FechaInicio"];
			$permiso["FechaFin"] = $r["FechaFin"];
			$permiso["DiasHabiles"] = $r["DiasHabiles"];
			$permiso["DiasTomar"] = $r["DiasHabiles"];
			$permiso["Remunerado"] = $r["Remunerado"];
			$permiso["Comentario"] = $r["Comentario"];
			$permiso["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$permiso["FechaAprobacion"] = $r["FechaAprobacion"];
			$permiso["Foto"] = $foto;
			$permiso["Solicitante"] = $Solicitante;
			$permiso["Cargo"] = "Solictud Permiso";
			$permiso["DetalleHtml"] = "detalle permiso";
			array_push($response_modulo, $permiso);
			unset($foto);
			unset($datos_usuario);
		} //end while
		$solicitudes["Permisos"] = $response_permiso;

		//Vacaciones

		$ConfiguracionLaboral = $dbo->getFields('ConfiguracionLaboral', 'PermiteVacacionesDiasDinero', 'IDClub = ' . $IDClub);
		$response_vacac = array();


		$where = '';


		// Validacion Club para mostrar las solicitudes de los negocios de luker
		if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
			$NegociosLuker = implode(',', SIMResources::$arrIDClubLuker);
			$where = " AND l.IDClub in (" . $NegociosLuker . ") ";
		} else {
			$where = " AND l.IDClub = '" . $IDClub . "' ";
		}
		// Fin Validacion Club para mostrar las solicitudes de los negocios de luker

		if ($n_Jefe > 0) {

			$where .= " AND l.IDEstado NOT IN (2,3,4)";
		} else if ($n_Especialista > 0) {
			if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
				$where .= " AND l.IDEstado NOT IN (1,2,3)";
			} else {
				$where .= " AND l.IDEstado NOT IN (2,3)";
			}
		}
		if ($FiltrarPorJefe != 'S') {
			$sql = "SELECT l.* FROM LaboralVacaciones l  WHERE 1 " . $where . " ORDER BY l.FechaTrCr DESC";
		} else {
			$sql = "SELECT l.* FROM LaboralVacaciones l LEFT JOIN Socio s ON l.IDSocio=s.IDSocio LEFT JOIN Usuario u ON l.IDUsuario=u.IDUsuario WHERE(u.DocumentoJefe = " . $NumeroDocumento . " OR u.DocumentoEspecialista = " . $NumeroDocumento . " OR s.DocumentoJefe= " . $NumeroDocumento . " OR s.DocumentoEspecialista=" . $NumeroDocumento . ") " . $where . " ORDER BY l.FechaTrCr DESC";
		}


		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$vacac["Modulo"] = "Vacaciones";
			if ($r["IDEstado"] == 1) {
				$vacac["Tipo"] = "Pendientes";
			} else {
				$vacac["Tipo"] = "Realizados";
			}


			if ($r["IDUsuario"] > 0) {
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array");
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"];
				$NumeroDocumento = $datos_usuario["NumeroDocumento"];
			} elseif ($r["IDSocio"] > 0) {
				$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array");
				$Cargo = $datos_usuario["Cargo"];
				$Solicitante = $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
				$NumeroDocumento = $datos_usuario["NumeroDocumento"];
			}
			$foto = (!empty($datos_usuario["Foto"])) ?  SOCIO_ROOT . $datos_usuario["Foto"] : "";
			$FechaInicio = date('Y-m-d', strtotime($r['FechaInicio']));
			$vacac["IDSolicitud"] = $r["IDLaboralVacaciones"];
			$vacac["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$vacac["FechaInicio"] = $FechaInicio;
			$vacac["FechaFin"] = $r["FechaFin"];
			if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
				$vacac["DiasTomar"] = $r["DiasNormales"];
			} else {
				$vacac["DiasTomar"] = $r["DiasTomar"];
			}
			$vacac["DiasDinero"] = $r["DiasDinero"];
			$vacac["Comentario"] = $r["Comentario"] . "<br><b>Estado: </b>" . SIMResources::$estado_laboral[$r["IDEstado"]];
			$vacac["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$vacac["FechaAprobacion"] = $r["FechaAprobacion"];
			$vacac["Foto"] = $foto;
			$vacac["Solicitante"] = $Solicitante;
			$vacac["NumeroDocumento"] = $NumeroDocumento;
			$vacac["Cargo"] = "Solicitud Vacaciones";
			$vacac["DetalleHtml"] = "";
			$vacac["PermiteVacacionesDiasDinero"] = $ConfiguracionLaboral['PermiteVacacionesDiasDinero'];
			array_push($response_modulo, $vacac);

			unset($foto);
			unset($datos_usuario);
		} //end while
		$solicitudes["Vacaciones"] = $response_vacac;

		array_push($response, $response_modulo);

		if ($Totalregistros > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response_modulo;
		} else {
			$respuesta["message"] = "No se encontraron registros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_tipo_rechazo_vacaciones($IDClub)
	{
		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT * FROM LaboralVacacionesRechazo  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			while ($r = $dbo->fetchArray($qry)) {
				$auxilio["IDTipoRechazo"] = $r["IDLaboralVacacionesRechazo"];
				$auxilio["NombreRechazo"] = $r["Nombre"];
				array_push($response, $auxilio);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$message = "VC2. Tipo rechazo no se encontraron";
			$respuesta["message"] = $message;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function set_solicitud_laboral_vacaciones($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $Comentarios, $Modulo)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSolicitud) && !empty($Aprueba)) {


			// Validamos si quien aprueba es Socio o Usuario
			$LaboralVacaciones = $dbo->fetchAll('LaboralVacaciones', 'IDLaboralVacaciones = ' . $IDSolicitud, "array");
			if ($LaboralVacaciones['IDSocio'] > 0) {
				$DocumentoSolicitante = $dbo->getFields('Socio', 'NumeroDocumento', 'IDSocio = ' . $LaboralVacaciones['IDSocio']);
				$IdUser = $LaboralVacaciones['IDSocio'];
				$TipoUsuario = "socio";
			} else {
				$DocumentoSolicitante = $dbo->getFields('Usuario', 'NumeroDocumento', 'IDUsuario = ' . $LaboralVacaciones['IDUsuario']);
				$IdUser = $LaboralVacaciones['IDUsuario'];
				$TipoUsuario = "usuario";
			}
			if ($IDSocio > 0) {
				$datos_usuario = $dbo->fetchAll('Socio', 'IDSocio = "' . $IDSocio . '"', 'array');
			} elseif ($IDUsuario > 0) {
				$datos_usuario = $dbo->fetchAll('Usuario', 'IDUsuario = "' . $IDUsuario . '"', 'array');
			}
			// Fin Validamos si quien aprueba es Socio o Usuario


			$ClubJefe = (in_array($IDClub, SIMResources::$arrIDClubLuker)) ? SIMResources::$arrIDClubLuker : $IDClub;
			if (count($ClubJefe) > 1) {
				$ClubJefe = implode(",", $ClubJefe);
			} else {
				$ClubJefe = $IDClub;
			}
			if (!empty($IDUsuario)) {
				$CampoUsuario = "IDUsuarioAutoriza";
				$IDInserta = $IDUsuario;

				$sqlJefe = "SELECT IDUsuario as Usuario FROM Usuario WHERE IDClub in ({$ClubJefe}) AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'] . " UNION SELECT IDSocio  as Usuario FROM Socio WHERE IDClub in ({$ClubJefe}) AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'];
				$q_Jefe = $dbo->query($sqlJefe);
				$r_Jefe = $dbo->rows($q_Jefe);
				$sqlAprobador = "SELECT IDUsuario as Usuario FROM Usuario WHERE IDClub in ({$ClubJefe}) AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'] . " UNION SELECT IDSocio as Usuario FROM Socio WHERE IDClub in ({$ClubJefe}) AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'];
				$q_Aprobador = $dbo->query($sqlAprobador);
				$r_Aprobador = $dbo->rows($q_Aprobador);
			} elseif (!empty($IDSocio)) {
				$CampoUsuario = "IDUsuarioAutoriza";
				$IDInserta = $IDSocio;
				$sqlJefe = "SELECT IDSocio FROM Socio WHERE IDClub in ({$ClubJefe}) AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'];
				$q_Jefe = $dbo->query($sqlJefe);
				$r_Jefe = $dbo->rows($q_Jefe);
				$sqlAprobador = "SELECT IDSocio FROM Socio WHERE IDClub in ({$ClubJefe}) AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'];
				$q_Aprobador = $dbo->query($sqlAprobador);
				$r_Aprobador = $dbo->rows($q_Aprobador);
			} else {
				exit;
			}


			if ($Aprueba == "S") {
				$IDEstado = 2;
			} else {
				$IDEstado = 3;
			}
			switch ($Modulo) {
				case 'Compensatorio':
					$sql_solic = "UPDATE LaboralCompensatorio
														 SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobacion='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralCompensatorio = '" . $IDSolicitud . "'	";
					$result = $dbo->query($sql_solic);
					if ($result === false) {
						$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
						$respuesta['success'] = false;
						$respuesta['response'] = "";
						return $respuesta;
					}
					$Mensaje = "Su solicitud de certificado a cambiando de estado";

					break;
				case 'Permisos':

					$sql_solic = "UPDATE LaboralPermiso
														 SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobacion='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralPermiso = '" . $IDSolicitud . "'	";
					$result = $dbo->query($sql_solic);
					if ($result === false) {
						$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
						$respuesta['success'] = false;
						$respuesta['response'] = "";
						return $respuesta;
					}
					$Mensaje = "Su solicitud de permiso a cambiando de estado";
					break;
				case 'Vacaciones':

					if ($r_Jefe > 0) {
						if ($Aprueba == "S") {
							// Cambiar club para luker

							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								$IDEstado = 4;
								$EstadoLuker = 'Pend2';
							} else {
								$IDEstado = 2;
							}
							$Aprobado = 1;
						} else {
							$IDEstado = 3;
							$Aprobado = 0;
							$EstadoLuker = 'No aprob1';
						}

						$sql_solic = "UPDATE LaboralVacaciones SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobacion='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaCambioEstadoJefe=CURDATE(),ApruebaJefe = " . $Aprobado . ", FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralVacaciones = '" . $IDSolicitud . "'	";
						if ($dbo->query($sql_solic)) {

							// Actualizar en LUKER_SOLI_VAC para notificaciones
							// Cambiar club para luker
							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								$Solicitante = $dbo->fetchAll('LaboralVacaciones', "IDLaboralVacaciones = $IDSolicitud", "array");
								if ($Solicitante['IDSocio'] > 0) {
									$tabla = 'Socio';
									$Campo = 'IDSocio';
									$Valor = $Solicitante['IDSocio'];
								} else {
									$tabla = 'Usuario';
									$Campo = 'IDUsuario';
									$Valor = $Solicitante['IDUsuario'];
								}
								$NumeroDocumentoSolicitante = $dbo->getFields($tabla, "NumeroDocumento", "$Campo = $Valor");
								$conn = SIMUtil::ConexionBdLuker();
								$sql = "SELECT EMP_CODIGO FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $NumeroDocumentoSolicitante . "  ";
								$stmt = $conn->prepare($sql);
								$stmt->execute();
								$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

								$sqlUpdataLuker = "UPDATE LUKER_SOLIC_VAC SET SVAC_ESTADO = '$EstadoLuker' WHERE EMP_CODIGO = '" . $row_vac['EMP_CODIGO'] . "'";
								$stmt = $conn->prepare($sqlUpdataLuker);
								$stmt->execute();
							}
							//Fin Actualizar en LUKER_SOLI_VAC para notificaciones

							if ($IDEstado == 4) {
								SIMUtil::noticar_nueva_LaboralVacaciones($IDSolicitud, 'Aprobador');
							} else {
								SIMUtil::noticar_respuesta_LaboralVacaciones($IDSolicitud, $Comentarios);
							}
						} else {
							$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
							$respuesta['success'] = false;
							$respuesta['response'] = "";
							return $respuesta;
						}
					} elseif ($r_Aprobador > 0) {
						if ($Aprueba == "S") {
							$IDEstado = 2;
							$Aprobado = 1;
							$EstadoLuker = 'Aprobada';
							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								if ($LaboralVacaciones['ApruebaJefe'] == 1) {

									$tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . HOST_LUKER . ")(PORT = " . PORT_LUKER . ")))
                        (CONNECT_DATA = (SERVICE_NAME = " . BASE_LUKER . ")))";
									try {
										$conn = new PDO("oci:dbname=" . $tns, USER_LUKER, PASSWORD_LUKER);
										$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									} catch (PDOException $e) {
										echo 'ERROR: ' . $e->getMessage();
									}
									$sql = "SELECT EMP_CODIGO FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $DocumentoSolicitante . "  ";
									$stmt = $conn->prepare($sql);
									$stmt->execute();
									$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);


									$LaboralVacaciones['FechaInicio'] = date('Y-m-d', strtotime($LaboralVacaciones['FechaInicio']));
									$sqlUpdate = "INSERT INTO LUKERSOLICVAC (EMP_CODIGO,SVAC_DIAS_DINERO,SVAC_DIAS_TIEMPO,SVAC_FEC_SALIDA,SVAC_FEC_FIN_DISF) VALUES ('" . $row_vac['EMP_CODIGO'] . "'," . $LaboralVacaciones['DiasDinero'] . "," . $LaboralVacaciones['DiasNormales'] . ",
                             to_date('" . $LaboralVacaciones['FechaInicio'] . "','yyyy-mm-dd'), 
                             to_date('" . $LaboralVacaciones['FechaFin'] . "','yyyy-mm-dd'))";

									$stmt = $conn->prepare($sqlUpdate);
									$stmt->execute();
								}
							}
						} else {
							$IDEstado = 3;
							$Aprobado = 0;
							$EstadoLuker = 'Rechazada';
						}

						$sql_solic = "UPDATE LaboralVacaciones SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobador='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaCambioEstadoAprobador=CURDATE(),ApruebaAprobador = " . $Aprobado . ", FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralVacaciones = '" . $IDSolicitud . "'	";
						if ($dbo->query($sql_solic)) {

							// Actualizar en LUKER_SOLI_VAC para notificaciones
							// Cambiar club para luker
							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								$Solicitante = $dbo->fetchAll('LaboralVacaciones', "IDLaboralVacaciones = $IDSolicitud", "array");
								if ($Solicitante['IDSocio'] > 0) {
									$tabla = 'Socio';
									$Campo = 'IDSocio';
									$Valor = $Solicitante['IDSocio'];
								} else {
									$tabla = 'Usuario';
									$Campo = 'IDUsuario';
									$Valor = $Solicitante['IDUsuario'];
								}
								$NumeroDocumentoSolicitante = $dbo->getFields($tabla, "NumeroDocumento", "$Campo = $Valor");

								$conn = SIMUtil::ConexionBDLuker();
								$sql = "SELECT EMP_CODIGO FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $NumeroDocumentoSolicitante . "  ";
								$stmt = $conn->prepare($sql);
								$stmt->execute();
								$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

								$sqlUpdataLuker = "UPDATE LUKER_SOLIC_VAC SET SVAC_ESTADO = '$EstadoLuker' WHERE EMP_CODIGO = '" . $row_vac['EMP_CODIGO'] . "'";
								$stmt = $conn->prepare($sqlUpdataLuker);
								$stmt->execute();
							}
							//Fin Actualizar en LUKER_SOLI_VAC para notificaciones


							SIMUtil::noticar_respuesta_LaboralVacaciones($IDSolicitud, $Comentarios);
						} else {
							$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
							$respuesta['success'] = false;
							$respuesta['response'] = "";
							return $respuesta;
						}
					} else {
						$respuesta["message"] = "No tiene permiso para responder esta solicitud";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
						return $respuesta;
					}

					break;

				default:
					if ($r_Jefe > 0) {
						if ($Aprueba == "S") {
							// Cambiar club para luker
							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								$IDEstado = 4;
							} else {
								$IDEstado = 2;
							}
							$Aprobado = 1;
						} else {
							$IDEstado = 3;
							$Aprobado = 0;
						}

						$sql_solic = "UPDATE LaboralVacaciones SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobacion='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaCambioEstadoJefe=CURDATE(),ApruebaJefe = " . $Aprobado . " FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralVacaciones = '" . $IDSolicitud . "'	";
						if ($dbo->query($sql_solic)) {
							SIMUtil::noticar_nueva_LaboralVacaciones($IDSolicitud, 'Aprobador');
						} else {
							$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
							$respuesta['success'] = false;
							$respuesta['response'] = "";
							return $respuesta;
						}
						if ($Aprobado == 0) {
							SIMUtil::noticar_respuesta_LaboralVacaciones($IDSolicitud, $Comentarios);
						}
					} elseif ($r_Aprobador > 0) {
						if ($Aprueba == "S") {
							$IDEstado = 2;
							$Aprobado = 1;
						} else {
							$IDEstado = 3;
							$Aprobado = 0;
						}

						$sql_solic = "UPDATE LaboralVacaciones SET IDEstado = '" . $IDEstado . "'," . $CampoUsuario . "='" . $IDInserta . "',
														 ComentarioAprobador='" . $Comentarios . "',FechaAprobacion=CURDATE(),FechaCambioEstadoAprobador=CURDATE(),ApruebaJefe = " . $Aprobado . ", FechaTrEd=NOW(),UsuarioTrEd='" . $IDInserta . "'
														 WHERE IDLaboralVacaciones = '" . $IDSolicitud . "'	";
						if ($dbo->query($sql_solic)) {
							SIMUtil::noticar_respuesta_LaboralVacaciones($IDSolicitud, $Comentarios);
						} else {
							$respuesta['message'] = "L1.Ocurrio un error, por favor contacte a su proveedor";
							$respuesta['success'] = false;
							$respuesta['response'] = "";
							return $respuesta;
						}
					} else {
						$respuesta["message"] = "No tiene permiso para responder esta solicitud";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
						return $respuesta;
					}
					break;
			}

			if ($TipoUsuario == "usuario") {
				SIMUtil::enviar_notificacion_push_general_funcionario($IDClub, $IdUser, $Mensaje);
			} else {
				SIMUtil::enviar_notificacion_push_general($IDClub, $IdUser, $Mensaje, $Modulo, "");
			}


			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "L10. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}

	function set_laboral_permiso($IDClub, $IDSocio, $IDUsuario, $IDMotivo, $FechaHoraInicio, $FechaHoraFin, $DiasHabiles, $Remunerado, $Comentario)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDMotivo)  && !empty($FechaHoraInicio) && !empty($FechaHoraFin)  && $DiasHabiles >= 0) {

			if (!empty($IDSocio)) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
			}

			$sql_inserta = "INSERT INTO LaboralPermiso ( IDClub," . $Campo . ",IDMotivo,IDEstado,FechaInicio,FechaFin,DiasHabiles,Remunerado,Comentario,UsuarioTrCr, FechaTrCr)
												  VALUES ('" . $IDClub . "','" . $Valor . "','" . $IDMotivo . "',1, '" . $FechaHoraInicio . "','" . $FechaHoraFin . "','" . $DiasHabiles . "','" . $Remunerado . "','" . $Comentario . "','App',NOW())";
			$dbo->query($sql_inserta);
			$Id = $dbo->lastID();

			SIMUtil::Notificar_solicitud_laboral($IDClub, $IDSocio, $IDUsuario, $Id, "Permiso");
			$respuesta["message"] = "permiso enviado con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "L2. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}

	function set_laboral_vacaciones($IDClub, $IDSocio, $IDUsuario, $FechaInicio, $FechaFin, $Dias, $Comentario, $DiasDinero = "", $DiasNormales = "")
	{

		$dbo = &SIMDB::get();
		$IDSocio = (empty($IDSocio)) ? 0 : $IDSocio;
		$IDUsuario = (empty($IDUsuario)) ? 0 : $IDUsuario;
		if (!empty($IDClub) && !empty($FechaInicio) && !empty($FechaFin) && !empty($Dias)) {

			if (!empty($IDSocio) && $IDSocio > 0) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
				$tabla = "Socio";
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
				$tabla = "Usuario";
			}
			$sql_ConfiguracionLaboral = "SELECT * FROM ConfiguracionLaboral WHERE IDClub = " . $IDClub . " AND Activo = 'S' ";
			$query_ConfiguracionLaboral = $dbo->query($sql_ConfiguracionLaboral);
			$result_ConfiguracionLaboral = $dbo->assoc($query_ConfiguracionLaboral);

			$CalcularFechas = self::get_laboral_calcula_fechafin($IDClub, $FechaInicio, $Dias, $DiasDinero, $DiasNormales, $IDSocio, $IDUsuario);
			$FechaReintegro = explode(": ", $CalcularFechas['response']['Mensaje']);
			$FechaReintegro = $FechaReintegro[1];

			if ($result_ConfiguracionLaboral['Activo'] == 'S') {

				$Hoy = date('Y-m-d H:i:s');
				$fechaHoy = new DateTime($Hoy);
				$fechaInicial = new DateTime($FechaInicio);

				$anticipacion = $fechaHoy->diff($fechaInicial);
				$diasAticipados = $anticipacion->days;

				if ($Dias >= $result_ConfiguracionLaboral['VacacionesDiasMinimo']) {
					if ($diasAticipados >= $result_ConfiguracionLaboral['VacacionesDiasAnticipacion']) {
						//Validamos si ya existe una solicitud en las fechas solicitadas.
						$whereSolicitante = ($IDSocio > 0) ? " AND IDSocio = $IDSocio " : " AND IDUsuario = $IDUsuario ";
						$sql_validaFechas = "SELECT * FROM LaboralVacaciones WHERE IDClub = $IDClub AND FechaInicio = '$FechaInicio' AND IDEstado IN (1,2,4) $whereSolicitante";

						$q_validaFechas = $dbo->query($sql_validaFechas);
						$n_validaFechas = $dbo->rows($q_validaFechas);
						if ($n_validaFechas > 0) {
							$respuesta["message"] = "Usted ya ha solicitado vacaciones para esta fecha: $FechaInicio";
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						}

						$sql_inserta = "INSERT INTO LaboralVacaciones ( IDClub, " . $Campo . ",IDEstado,FechaInicio,FechaFin,DiasTomar,Comentario,DiasDinero,DiasNormales,FechaReintegro,UsuarioTrCr, FechaTrCr)
						VALUES ('" . $IDClub . "','" . $Valor . "',1, '" . $FechaInicio . "','" . $FechaFin . "','" . $Dias . "','" . $Comentario . "','" . $DiasDinero . "','" . $DiasNormales . "','$FechaReintegro','App',NOW())";
						if ($dbo->query($sql_inserta)) {
							$id_solicitud = $dbo->lastID();
							SIMUtil::noticar_nueva_LaboralVacaciones($id_solicitud);

							$respuesta["message"] = "Solicitud de vacaciones enviada con exito";
							$respuesta["success"] = true;
							$respuesta["response"] = NULL;

							// Registro en LUKER_SOLI_VAC para notificaciones
							// Cambiar club para luker
							if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
								$NumeroDocumentoSolicitante = $dbo->getFields($tabla, "NumeroDocumento", "$Campo = $Valor");
								$conn = SIMUtil::ConexionBDLuker();
								$sql = "SELECT EMP_CODIGO FROM VLK_VAC_PEND_ATG WHERE EMP_CEDULA = " . $NumeroDocumentoSolicitante . "  ";
								$stmt = $conn->prepare($sql);
								$stmt->execute();
								$row_vac = $stmt->fetch(PDO::FETCH_ASSOC);

								$sqlInsertLuker = "INSERT INTO LUKER_SOLIC_VAC (EMP_CODIGO,SVAC_DIAS_TIEMPO,SVAC_FEC_SALIDA,SVAC_ESTADO) VALUES ('" . $row_vac['EMP_CODIGO'] . "',$DiasNormales,to_date('" . $FechaInicio . "','YYYY-MM-DD'),'Pend1')";
								$stmt = $conn->prepare($sqlInsertLuker);
								$stmt->execute();
							}
							//Fin Registro en LUKER_SOLI_VAC para notificaciones

						} else {
							$respuesta["message"] = "L3. Se produjo un error";
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;
						}
					} else {
						$respuesta["message"] = "L3. Debes solicitar las vacaciones con " . $result_ConfiguracionLaboral['VacacionesDiasAnticipacion'] . " días de anticipación";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}
				} else {
					$respuesta["message"] = "L3. Los días de vacaciones deben ser igual ó superior a " . $result_ConfiguracionLaboral['VacacionesDiasMinimo'];
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
			}
			// else {

			// 	//Validamos si ya existe una solicitud en las fechas solicitadas.
			// 	$sql_validaFechas = "SELECT * FROM LaboralVacaciones WHERE IDClub = $IDClub AND FechaInicio = '$FechaInicio' AND IDEstado IN (1,2,4) AND (IDSocio in ($IDSocio) OR IDUsuario in ($IDUsuario))";
			// 	$q_validaFechas = $dbo->query($sql_validaFechas);
			// 	$n_validaFechas = $dbo->rows($q_validaFechas);
			// 	if ($n_validaFechas > 0) {
			// 		$respuesta["message"] = "Usted ya ha solicitado vacaciones para esta fecha: $FechaInicio";
			// 		$respuesta["success"] = false;
			// 		$respuesta["response"] = null;
			// 		return $respuesta;
			// 	}

			// 	$sql_inserta = "INSERT INTO LaboralVacaciones ( IDClub, " . $Campo . ",IDEstado,FechaInicio,FechaFin,DiasTomar,Comentario,DiasDinero,DiasNormales,FechaReintegro,UsuarioTrCr, FechaTrCr)
			// 	VALUES ('" . $IDClub . "','" . $Valor . "',1, '" . $FechaInicio . "','" . $FechaFin . "','" . $Dias . "','" . $Comentario . "','" . $DiasDinero . "','" . $DiasNormales . "','$FechaReintegro','App',NOW())";
			// 	$dbo->query($sql_inserta);
			// 	$id_solicitud = $dbo->lastID();

			// 	SIMUtil::noticar_nueva_LaboralVacaciones($id_solicitud);
			// 	$respuesta["message"] = "Solicitud de vacaciones enviada con exito";
			// 	$respuesta["success"] = true;
			// 	$respuesta["response"] = NULL;
			// }

		} else {
			$respuesta["message"] = "L3. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}

	function set_laboral_compensatorio($IDClub, $IDSocio, $IDUsuario, $FechaInicio, $FechaFin, $Dias, $Comentario, $File = "")
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($FechaInicio) && !empty($Dias)) {

			if (!empty($IDSocio)) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
			}

			if (isset($File)) {

				$arrArchivo = empty($File["Archivo"]) ? $File["Imagen"] : $File["Archivo"];

				$tamano_archivo = $arrArchivo["size"];
				if ($tamano_archivo >= 6000000) {
					$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				}

				$files = SIMFile::upload($arrArchivo, LABORAL_DIR, "IMAGE");
				if (empty($files) && !empty($arrArchivo["name"])) :
					$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacarga.Verifiquequeelarchivonocontengaerroresyqueeltipodearchivoseapermitido', LANG);
					$respuesta["success"] = false;
					$respuesta["response"] = null;
					return $respuesta;
				endif;
				$Archivo = $files[0]["innername"];
			}

			$sql_inserta = "INSERT INTO LaboralCompensatorio ( IDClub," . $Campo . ",IDEstado,FechaInicio,DiasTomar,Comentario,Archivo,UsuarioTrCr, FechaTrCr)
													VALUES ('" . $IDClub . "','" . $Valor . "',1, '" . $FechaInicio . "','" . $Dias . "','" . $Comentario . "','" . $Archivo . "','App',NOW())";
			$dbo->query($sql_inserta);
			$Id = $dbo->lastID();

			SIMUtil::Notificar_solicitud_laboral($IDClub, $IDSocio, $IDUsuario, $Id, "Compensatorio");

			$respuesta["message"] = "Solicitud de  compensatorio enviado con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "L4. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}

	function set_laboral_certificado($IDClub, $IDSocio, $IDUsuario, $IDTipoCertificado, $Fechas, $ANombreDe, $Comentario)
	{

		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDTipoCertificado) && !empty($Fechas)  && !empty($ANombreDe)) {

			if (!empty($IDSocio)) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
			}

			$sql_inserta = "INSERT INTO LaboralCertificado 
			( IDClub," . $Campo . ",IDEstado,IDTipoCertificado,Fechas,AnombreDe,Comentario,UsuarioTrCr, FechaTrCr)
            VALUES ('" . $IDClub . "','" . $Valor . "',1,'" . $IDTipoCertificado . "','" . $Fechas . "','" . $ANombreDe . "','" . $Comentario . "','App',NOW())";
			if ($dbo->query($sql_inserta)) {
				$id_solicitud = $dbo->lastID();
				SIMUtil::notifica_nueva_solicitud_certificado($IDClub, $id_solicitud);
			}

			$respuesta["message"] = "Solicitud de certificado enviada con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "L5. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}


	function get_mis_solicitudes_laborales($IDClub, $IDSocio, $IDUsuario)
	{

		$dbo = &SIMDB::get();
		$response = array();

		if (!empty($IDSocio)) {
			$Campo = "IDSocio";
			$Valor = $IDSocio;
		} else {
			$Campo = "IDUsuario";
			$Valor = $IDUsuario;
		}


		$solicitudes["IDClub"] = $IDClub;
		$solicitudes["IDSocio"] = $IDSocio;
		$solicitudes["IDUsuario"] = $IDUsuario;
		$Totalregistros = 0;

		//Certificados
		$response_certif = array();
		$sql = "SELECT * FROM LaboralCertificado WHERE " . $Campo . " = '" . $Valor . "'  ORDER BY FechaTrCr DESC";
		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$certif["Modulo"] = "Certificado";
			if ($r["IDEstado"] == 1) {
				$certif["Tipo"] = "Pendientes";
			} else {
				$certif["Tipo"] = "Realizados";
			}
			$certif["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$certif["Fechas"] = $r["Fechas"];
			$certif["AnombreDe"] = $r["AnombreDe"];
			$certif["Comentario"] = $r["Comentario"];
			$certif["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$certif["FechaAprobacion"] = $r["FechaAprobacion"];
			$certif["Archivo"] = CLUB_ROOT . $r["Archivo"];
			array_push($response_certif, $certif);
		} //end while
		$solicitudes["Certificados"] = $response_certif;


		//Compensatorio
		$response_compen = array();
		$sql = "SELECT * FROM LaboralCompensatorio WHERE " . $Campo . " = '" . $Valor . "'  ORDER BY FechaTrCr DESC";
		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$compensa["Modulo"] = "Compensatorio";

			if ($r["IDEstado"] == 1) {
				$compensa["Tipo"] = "Pendientes";
			} else {
				$compensa["Tipo"] = "Realizados";
			}

			$fechaFin = SIMWebServiceLaboral::get_laboral_calcula_fechafin($IDClub, $r["FechaInicio"], $r["DiasTomar"]);

			$compensa["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$compensa["FechaInicio"] = $r["FechaInicio"];
			$compensa["DiasTomar"] = $r["DiasTomar"];
			$compensa["FechaFin"] = $fechaFin['response']['Fecha'];
			$compensa["Comentario"] = $r["Comentario"];
			$compensa["Archivo"] = $r["Archivo"] != "" ? LABORAL_ROOT . $r["Archivo"] : "";
			$compensa["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$compensa["FechaAprobacion"] = $r["FechaAprobacion"];
			array_push($response_compen, $compensa);
		} //end while
		$solicitudes["Compensatorio"] = $response_compen;

		//Permisos
		$response_permiso = array();
		$sql = "SELECT * FROM LaboralPermiso WHERE " . $Campo . " = '" . $Valor . "'  ORDER BY FechaTrCr DESC";
		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$permiso["Modulo"] = "Permisos";
			if ($r["IDEstado"] == 1) {
				$permiso["Tipo"] = "Pendientes";
			} else {
				$permiso["Tipo"] = "Realizados";
			}
			$permiso["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$permiso["FechaInicio"] = $r["FechaInicio"];
			$permiso["FechaFin"] = $r["FechaFin"];
			$permiso["DiasHabiles"] = $r["DiasHabiles"];
			$permiso["Remunerado"] = $r["Remunerado"];
			$permiso["Comentario"] = $r["Comentario"];
			$permiso["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$permiso["FechaAprobacion"] = $r["FechaAprobacion"];
			array_push($response_permiso, $permiso);
		} //end while
		$solicitudes["Permisos"] = $response_permiso;

		//Vacaciones
		$response_vacac = array();
		$sql = "SELECT * FROM LaboralVacaciones WHERE " . $Campo . " = '" . $Valor . "'  ORDER BY FechaTrCr DESC";
		$qry = $dbo->query($sql);
		while ($r = $dbo->fetchArray($qry)) {
			$Totalregistros++;
			$vacac["Modulo"] = "Vacaciones";
			if ($r["IDEstado"] == 1) {
				$vacac["Tipo"] = "Pendientes";
			} else {
				$vacac["Tipo"] = "Realizados";
			}
			$vacac["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
			$vacac["FechaInicio"] = $r["FechaInicio"];
			$vacac["FechaFin"] = $r["FechaFin"];
			$vacac["DiasTomar"] = $r["DiasTomar"];
			$vacac["Comentario"] = $r["Comentario"];
			$vacac["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
			$vacac["FechaAprobacion"] = $r["FechaAprobacion"];
			array_push($response_vacac, $vacac);
		} //end while
		$solicitudes["Vacaciones"] = $response_vacac;

		array_push($response, $solicitudes);

		if ($Totalregistros > 0) {
			$message = $dbo->rows($qry) . " Encontrados";
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} else {
			$respuesta["message"] = "No se encontraron registros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function get_laboral_calcula_fechafin($IDClub, $fechaInicial, $dias, $DiasDinero = "", $DiasNormales = "", $IDSocio = "", $IDUsuario = "")
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

		if (in_array($IDClub, SIMResources::$arrIDClubLuker)) {
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


		// Dias no laborales desde LaboralVacacionesPendientes
		if (!empty($IDSocio)) {
			$SabadoLaboral = $dbo->getFields("LaboralVacacionesPendientes", "SabadoLaboral", "IDSocio = $IDSocio LIMIT 1");
		} elseif (!empty($IDUsuario)) {
			$SabadoLaboral = $dbo->getFields("LaboralVacacionesPendientes", "SabadoLaboral", "IDUsuario = $IDUsuario LIMIT 1");
		}

		if (!in_array($IDClub, SIMResources::$arrIDClubLuker)) {
			if ($SabadoLaboral == "N") {
				$diasNoLaborales[] = "Saturday";
			} else {
				unset($diasNoLaborales);
				$diasNoLaborales[] = "Sunday";
			}
		}

		// Dias no laborales desde LaboralVacacionesPendientes

		//Identificar los días festivos
		$queryPais = $dbo->query("SELECT P.IDPais FROM ConfiguracionClub C INNER JOIN Pais P ON C.IDPais = P.IDPais WHERE C.IDClub=" . $IDClub . " ORDER BY IDConfiguracionClub DESC LIMIT 1");
		$IDPais = $dbo->fetch($queryPais);
		$queryFestivo = $dbo->query("SELECT Fecha FROM Festivos WHERE IDPais=" . $IDPais['IDPais']);
		$fechasNoLaborales = [];
		while ($festivos = $dbo->assoc($queryFestivo)) {
			$fechasNoLaborales[] = date("Y-m-d", strtotime($festivos["Fecha"]));
		}
		//Fin identificar los días festivos
		$fechaHoy = date("Y-m-d", strtotime($fechaInicial));
		$diaSemana = date("l", strtotime($fechaHoy));

		if (in_array($diaSemana, $diasNoLaborales) || in_array($fechaHoy, $fechasNoLaborales)) {
			$calcula_fecha["Fecha"] = "0000-00-00";
			$calcula_fecha["Mensaje"] = "Día no hábil";
			$calcula_fecha["DiasMaxDinero"] = intval(0);
			$calcula_fecha["DiasMinDinero"] = intval(0);

			$respuesta["message"] = "Día no hábil";
			$respuesta["success"] = false;
			$respuesta["response"] = $calcula_fecha;
			return $respuesta;
		} else {
			$diasUsados = 0;
			$fechaSiguiente = date("Y-m-d", strtotime($fechaInicial));
			$DiasSolicitados = ($DiasNormales > 0) ? $DiasNormales : $dias;
			while ($diasUsados < $DiasSolicitados) {

				$fechaHoy = date("Y-m-d", strtotime($fechaSiguiente));

				$diaSemana = date("l", strtotime($fechaHoy));
				if (!in_array($diaSemana, $diasNoLaborales) && !in_array($fechaHoy, $fechasNoLaborales)) {
					$diasUsados++;
				}

				$fechaSiguiente = date("Y-m-d", strtotime($fechaHoy . "+ 1 days"));
			}

			$fechaSiguiente = date("Y-m-d", strtotime($fechaHoy . "+ 1 days"));
			$diaRetorno = date("l", strtotime($fechaSiguiente));
			while (in_array($diaRetorno, $diasNoLaborales) || in_array($fechaSiguiente, $fechasNoLaborales)) {
				$fechaSiguiente = date("Y-m-d", strtotime($fechaSiguiente . "+ 1 days"));
				$diaRetorno = date("l", strtotime($fechaSiguiente));
			}

			$calcula_fecha["Fecha"] = $fechaHoy;
			$calcula_fecha["Mensaje"] = "Vuelve al trabajo el dia: " . $fechaSiguiente;

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
