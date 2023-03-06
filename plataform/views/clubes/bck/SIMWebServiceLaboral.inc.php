<?php
class SIMWebServiceLaboral {

	function get_configuracion_laboral($IDClub,$IDSocio,$IDUsuario){

			$dbo =& SIMDB::get();
			$response = array();

			if(!empty( $IDUsuario )){
				$datos_usuario = $dbo->fetchAll( "Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array" );
			}
			elseif(!empty( $IDSocio )){
				$datos_usuario = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );
			}



			$sql = "SELECT *
							FROM ConfiguracionLaboral
							WHERE IDClub = '".$IDClub."' ";
			$qry = $dbo->query( $sql );
			if( $dbo->rows( $qry ) > 0 )
			{
				$message = $dbo->rows( $qry ) . " Encontrados";
				while( $r = $dbo->fetchArray( $qry ) )
				{
						$configuracion["IDClub"] = $IDClub;
						$configuracion["Vacaciones"] = $r["Vacaciones"];
						$configuracion["VacacionesIcono"] = CLUB_ROOT.$r["VacacionesIcono"];
						$configuracion["VacacionesNombre"] = $r["VacacionesNombre"];
						$configuracion["VacacionesOrden"] = $r["VacacionesOrden"];
						$configuracion["Compensatorio"] = $r["Compensatorio"];
						$configuracion["CompensatorioIcono"] = CLUB_ROOT.$r["CompensatorioIcono"];
						$configuracion["CompensatorioNombre"] = $r["CompensatorioNombre"];
						$configuracion["CompesatorioOrden"] = $r["CompesatorioOrden"];
						$configuracion["Permisos"] = $r["Permisos"];
						$configuracion["PermisosIcono"] = CLUB_ROOT.$r["PermisosIcono"];
						$configuracion["PermisosNombre"] = $r["PermisosNombre"];
						$configuracion["PermisosOrden"] = $r["PermisosOrden"];
						$configuracion["Extracto"] = $r["Extracto"];
						$configuracion["ExtractoIcono"] = CLUB_ROOT.$r["ExtractoIcono"];
						$configuracion["ExtractoNombre"] = $r["ExtractoNombre"];
						$configuracion["ExtractoOrden"] = $r["ExtractoOrden"];
						$configuracion["Certificado"] = $r["Certificado"];
						$configuracion["CertificadoIcono"] = CLUB_ROOT.$r["CertificadoIcono"];
						$configuracion["CertificadoNombre"] = $r["CertificadoNombre"];
						$configuracion["CertificadoOrden"] = $r["CertificadoOrden"];
						$configuracion["LabelMisPermisos"] = $r["LabelMisPermisos"];
						$configuracion["AprobarVacacionesNombre"] = $r["AprobarVacacionesNombre"];
						$configuracion["AprobarVacaciones"] = $datos_usuario["AprobarVacaciones"];
						$configuracion["AprobarVacacionesIcono"] = $r["AprobarVacacionesIcono"];
						$configuracion["AprobarVacacionesOrden"] = $r["AprobarVacacionesOrden"];
						$configuracion["BotonFiltroAprobarVacacionesTexto"] = $r["BotonFiltroAprobarVacacionesTexto"];
						$configuracion["LabelAprobarSolicitudes"] = $r["LabelAprobarSolicitudes"];

						//Reviso las vacaciones y compensatorios que tiene disponible
						$response_vacaciones = array();
						if(!empty($IDSocio)){
							$Campo="IDSocio";
							$Valor=$IDSocio;
						}
						else{
							$Campo="IDUsuario";
							$Valor=$IDUsuario;
						}
						$sql_vac="SELECT Periodo,Dias,DiasCompensatorio
											FROM  LaboralVacacionesPendientes
											WHERE " . $Campo . "= '".$Valor."' ";
						$r_vac=$dbo->query($sql_vac);
						while($row_vac=$dbo->fetchArray($r_vac)){
							$vac[ "Periodo" ] = $row_vac["Periodo"];
							$vac[ "Dias" ] = $row_vac["Dias"];
							$dias_compensatorio+=$row_vac["DiasCompensatorio"];
							array_push( $response_vacaciones, $vac );
						}
						$configuracion["VacacionesPeriodo"] = $response_vacaciones;
						$configuracion["DiasCompensatorio"] = $dias_compensatorio;


						$response_motivo_permiso = array();
						foreach(SIMResources::$motivo_laboral as $id_motivo => $motivo){
								$motivos[ "IDMotivo" ] = $id_motivo;
								$motivos[ "Motivo" ] = $motivo;
								array_push( $response_motivo_permiso, $motivos );
						}
						$configuracion["MotivoPermiso"] = $response_motivo_permiso;

						$response_tipo_certificado = array();
						foreach(SIMResources::$tipo_certificado_laboral as $id_certif => $certif){
								$certificado[ "IDTipoCertificado" ] = $id_certif;
								$certificado[ "Certificado" ] = $certif;
								array_push( $response_tipo_certificado, $certificado );
						}
						$configuracion["TipoCertificado"] = $response_tipo_certificado;

						array_push($response, $configuracion);

				}//ednw hile
					$respuesta["message"] = $message;
					$respuesta["success"] = true;
					$respuesta["response"] = $response;
			}//End if
			else
			{
					$respuesta["message"] = "L1. No se encontraron registros";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
			}//end else

			return $respuesta;

		}// fin function


		function get_filtro_solicitudes_laborales_por_aprobar($IDClub,$IDSocio,$IDUsuario){

				$dbo =& SIMDB::get();
				$response = array();


							$message = $dbo->rows( $qry ) . " Encontrados";

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

			}// fin function


			function get_solicitudes_laborales_por_aprobar($IDClub,$IDSocio,$IDUsuario,$IDFiltro){

					$dbo =& SIMDB::get();
					$response = array();

					$Totalregistros=0;
					$response_modulo = array();

					//Compensatorio
					$response_compen = array();
					$sql = "SELECT * FROM LaboralCompensatorio WHERE IDEstado=1  ORDER BY FechaTrCr DESC";
					$qry = $dbo->query( $sql );
					while( $r = $dbo->fetchArray( $qry ) ){
						$Totalregistros++;
						$compensa["Modulo"] = "Compensatorio";
						if($r["IDEstado"]==1){
							$compensa["Tipo"] = "Pendientes";
						}
						else{
							$compensa["Tipo"] = "Realizados";
						}

						if( $r["IDUsuario"]>0 ){
							$datos_usuario = $dbo->fetchAll( "Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
							$Solicitante=$datos_usuario["Nombre"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = USUARIO_ROOT . $datos_usuario["Foto"];
							}
						}
						elseif($r["IDSocio"]>0){
							$datos_usuario = $dbo->fetchAll( "Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
								$Solicitante=$datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = SOCIO_ROOT . $datos_usuario["Foto"];
							}
						}

						$compensa["IDSolicitud"] = $r["IDLaboralCompensatorio"];
						$compensa["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
						$compensa["FechaInicio"] = $r["FechaInicio"];
						$compensa["DiasTomar"] = $r["DiasTomar"];
						$compensa["Comentario"] = $r["Comentario"];
						$compensa["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
						$compensa["FechaAprobacion"] = $r["FechaAprobacion"];
						$compensa["Solicitante"] = $Solicitante;
						$compensa["Cargo"] = "Solictud Compensatorio";
						$compensa["Foto"] = $foto;
						$compensa["DetalleHtml"] = "detalle compensa";
						array_push($response_modulo, $compensa);
					}//end while
					$solicitudes["Compensatorio"] = $response_compen;

					//Permisos
					$response_permiso = array();
					$sql = "SELECT * FROM LaboralPermiso WHERE IDEstado=1  ORDER BY FechaTrCr DESC";
					$qry = $dbo->query( $sql );
					while( $r = $dbo->fetchArray( $qry ) ){
						$Totalregistros++;
						$permiso["Modulo"] = "Permisos";
						if($r["IDEstado"]==1){
							$permiso["Tipo"] = "Pendientes";
						}
						else{
							$permiso["Tipo"] = "Realizados";
						}

						if( $r["IDUsuario"]>0 ){
							$datos_usuario = $dbo->fetchAll( "Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
							$Solicitante=$datos_usuario["Nombre"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = USUARIO_ROOT . $datos_usuario["Foto"];
							}
						}
						elseif($r["IDSocio"]>0){
							$datos_usuario = $dbo->fetchAll( "Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
								$Solicitante=$datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = SOCIO_ROOT . $datos_usuario["Foto"];
							}
						}

						$permiso["IDSolicitud"] = $r["IDLaboralPermiso"];
						$permiso["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
						$permiso["FechaInicio"] = $r["FechaInicio"];
						$permiso["FechaFin"] = $r["FechaFin"];
						$permiso["DiasHabiles"] = $r["DiasHabiles"];
						$permiso["Remunerado"] = $r["Remunerado"];
						$permiso["Comentario"] = $r["Comentario"];
						$permiso["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
						$permiso["FechaAprobacion"] = $r["FechaAprobacion"];
						$permiso["Foto"] = $foto;
						$permiso["Solicitante"] = $Solicitante;
						$permiso["Cargo"] = "Solictud Permiso";
						$permiso["DetalleHtml"] = "detalle permiso";
						array_push($response_modulo, $permiso);
					}//end while
					$solicitudes["Permisos"] = $response_permiso;

					//Vacaciones
					$response_vacac = array();
					$sql = "SELECT * FROM LaboralVacaciones WHERE IDEstado=1  ORDER BY FechaTrCr DESC";
					$qry = $dbo->query( $sql );
					while( $r = $dbo->fetchArray( $qry ) ){
						$Totalregistros++;
						$vacac["Modulo"] = "Vacaciones";
						if($r["IDEstado"]==1){
							$vacac["Tipo"] = "Pendientes";
						}
						else{
							$vacac["Tipo"] = "Realizados";
						}


						if( $r["IDUsuario"]>0 ){
							$datos_usuario = $dbo->fetchAll( "Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
							$Solicitante=$datos_usuario["Nombre"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = USUARIO_ROOT . $datos_usuario["Foto"];
							}
						}
						elseif($r["IDSocio"]>0){
							$datos_usuario = $dbo->fetchAll( "Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array" );
							$Cargo=$datos_usuario["Cargo"];
								$Solicitante=$datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
							if (!empty($datos_usuario["Foto"])) {
									$foto = SOCIO_ROOT . $datos_usuario["Foto"];
							}
						}


						$vacac["IDSolicitud"] = $r["IDLaboralVacaciones"];
						$vacac["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
						$vacac["FechaInicio"] = $r["FechaInicio"];
						$vacac["FechaFin"] = $r["FechaFin"];
						$vacac["DiasTomar"] = $r["DiasTomar"];
						$vacac["Comentario"] = $r["Comentario"];
						$vacac["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
						$vacac["FechaAprobacion"] = $r["FechaAprobacion"];
						$vacac["Foto"] = $foto;
						$vacac["Solicitante"] = $Solicitante;
						$vacac["Cargo"] = "Solicitud Vacaciones";
						$vacac["DetalleHtml"] = "detalle vac";
						array_push($response_modulo, $vacac);
					}//end while
					$solicitudes["Vacaciones"] = $response_vacac;

					array_push($response, $response_modulo);

					if($Totalregistros>0){
						$message = $dbo->rows( $qry ) . " Encontrados";
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response_modulo;
					}
					else
					{
							$respuesta["message"] = "No se encontraron registros";
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;
					}//end else

					return $respuesta;


				}// fin function


				function set_solicitud_laboral_vacaciones($IDClub,$IDSocio,$IDUsuario,$IDSolicitud,$Aprueba,$Comentarios,$Modulo)	{
					$dbo =& SIMDB::get();


					if( !empty( $IDClub ) && (!empty( $IDSocio ) || !empty( $IDUsuario )) && !empty( $IDSolicitud ) && !empty( $Aprueba ) ){

							if($Aprueba=="S"){
								$IDEstado=2;
							}
							else{
								$IDEstado=3;
							}

							if(!empty( $IDUsuario )){
								$CampoUsuario="IDUsuarioAutoriza";
								$IDInserta=$IDUsuario;
								$TipoUsuario="Funcionario";
							}
							elseif(!empty( $IDSocio )){
								$CampoUsuario="IDSocioAutoriza";
								$IDInserta=$IDSocio;
								$TipoUsuario="Socio";
							}
							else{
								exit;
							}

							switch ($Modulo) {
									case 'Compensatorio':
									$sql_solic ="UPDATE LaboralCompensatorio
														 SET IDEstado = '".$IDEstado."',".$CampoUsuario."='".$IDInserta."',
														 ComentarioAprobacion='".$Comentarios."',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='".$IDInserta."'
														 WHERE IDLaboralCompensatorio = '".$IDSolicitud."'	";
									$dbo->query($sql_solic);
									break;
									case 'Permisos':
									$sql_solic ="UPDATE LaboralPermiso
														 SET IDEstado = '".$IDEstado."',".$CampoUsuario."='".$IDInserta."',
														 ComentarioAprobacion='".$Comentarios."',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='".$IDInserta."'
														 WHERE IDLaboralPermiso = '".$IDSolicitud."'	";
									$dbo->query($sql_solic);
									break;
									case 'Vacaciones':
									$sql_solic ="UPDATE LaboralVacaciones
														 SET IDEstado = '".$IDEstado."',".$CampoUsuario."='".$IDInserta."',
														 ComentarioAprobacion='".$Comentarios."',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='".$IDInserta."'
														 WHERE IDLaboralVacaciones = '".$IDSolicitud."'	";
									$dbo->query($sql_solic);
									break;

								default:
									$sql_solic ="UPDATE LaboralVacaciones
														 SET IDEstado = '".$IDEstado."',".$CampoUsuario."='".$IDInserta."',
														 ComentarioAprobacion='".$Comentarios."',FechaAprobacion=CURDATE(),FechaTrEd=NOW(),UsuarioTrEd='".$IDInserta."'
														 WHERE IDLaboralVacaciones = '".$IDSolicitud."'	";
									$dbo->query($sql_solic);
								break;

							}



						$respuesta["message"] = "guardado";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;

				}
				else{
					$respuesta["message"] = "L10. Atencion faltan parametros";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}

				return $respuesta;

				}



		function set_laboral_permiso($IDClub,$IDSocio,$IDUsuario,$IDMotivo,$FechaHoraInicio,$FechaHoraFin,$DiasHabiles,$Remunerado,$Comentario)
		{
			$dbo =& SIMDB::get();
			if( !empty( $IDClub ) && !empty( $IDMotivo )  && !empty( $FechaHoraInicio ) && !empty( $FechaHoraFin )  && !empty( $DiasHabiles ) ){

					if(!empty($IDSocio)){
						$Campo="IDSocio";
						$Valor=$IDSocio;
					}
					else{
						$Campo="IDUsuario";
						$Valor=$IDUsuario;
					}

					$sql_inserta = "INSERT INTO LaboralPermiso ( IDClub,".$Campo.",IDMotivo,IDEstado,FechaInicio,FechaFin,DiasHabiles,Remunerado,Comentario,UsuarioTrCr, FechaTrCr)
												  VALUES ('".$IDClub."','".$Valor."','".$IDMotivo."',1, '".$FechaHoraInicio."','".$FechaHoraFin."','".$DiasHabiles."','".$Remunerado."','".$Comentario."','App',NOW())";
					$dbo->query($sql_inserta);

					$respuesta["message"] = "permiso enviado con exito";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
			}
			else{
				$respuesta["message"] = "L2. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			return $respuesta;
		}

		function set_laboral_vacaciones($IDClub,$IDSocio,$IDUsuario,$FechaInicio,$FechaFin,$Dias,$Comentario){

			$dbo =& SIMDB::get();

			if( !empty( $IDClub ) && !empty( $FechaInicio ) && !empty( $FechaFin ) && !empty( $Dias ) ){

					if(!empty($IDSocio)){
						$Campo="IDSocio";
						$Valor=$IDSocio;
					}
					else{
						$Campo="IDUsuario";
						$Valor=$IDUsuario;
					}

					$sql_inserta = "INSERT INTO LaboralVacaciones ( IDClub, ".$Campo.",IDEstado,FechaInicio,FechaFin,DiasTomar,Comentario,UsuarioTrCr, FechaTrCr)
													VALUES ('".$IDClub."','".$Valor."',1, '".$FechaInicio."','".$FechaFin."','".$Dias."','".$Comentario."','App',NOW())";
					$dbo->query($sql_inserta);

					$respuesta["message"] = "Solicitud de vacaciones enviadas con exito";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
			}
			else{
				$respuesta["message"] = "L3. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			return $respuesta;
		}

		function set_laboral_compensatorio($IDClub,$IDSocio,$IDUsuario,$FechaInicio,$Dias,$Comentario){
			$dbo =& SIMDB::get();
			if( !empty( $IDClub ) && !empty( $FechaInicio ) && !empty( $Dias ) ){

					if(!empty($IDSocio)){
						$Campo="IDSocio";
						$Valor=$IDSocio;
					}
					else{
						$Campo="IDUsuario";
						$Valor=$IDUsuario;
					}

					$sql_inserta = "INSERT INTO LaboralCompensatorio ( IDClub,".$Campo.",IDEstado,FechaInicio,DiasTomar,Comentario,UsuarioTrCr, FechaTrCr)
													VALUES ('".$IDClub."','".$Valor."',1, '".$FechaInicio."','".$Dias."','".$Comentario."','App',NOW())";
					$dbo->query($sql_inserta);

					$respuesta["message"] = "Solicitud de  compensatorio enviado con exito";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
			}
			else{
				$respuesta["message"] = "L4. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			return $respuesta;
		}

		function set_laboral_certificado($IDClub,$IDSocio,$IDUsuario,$IDTipoCertificado,$Fechas,$ANombreDe,$Comentario){

			$dbo =& SIMDB::get();
			if( !empty( $IDClub ) && !empty( $IDTipoCertificado ) && !empty( $Fechas )  && !empty( $ANombreDe ) ){

					if(!empty($IDSocio)){
						$Campo="IDSocio";
						$Valor=$IDSocio;
					}
					else{
						$Campo="IDUsuario";
						$Valor=$IDUsuario;
					}

					$sql_inserta = "INSERT INTO LaboralCertificado ( IDClub,".$Campo.",IDEstado,IDTipoCertificado,Fechas,AnombreDe,Comentario,UsuarioTrCr, FechaTrCr)
													VALUES ('".$IDClub."','".$Valor."',1,'".$IDTipoCertificado."','".$FechaInicio."','".$Dias."','".$Comentario."','App',NOW())";
					$dbo->query($sql_inserta);

					$respuesta["message"] = "Solicitud de certificado enviada con exito";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;
			}
			else{
				$respuesta["message"] = "L5. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			return $respuesta;

		}

		function get_mis_solicitudes_laborales($IDClub,$IDSocio,$IDUsuario)
		{

			$dbo =& SIMDB::get();
			$response = array();

			if(!empty($IDSocio)){
				$Campo="IDSocio";
				$Valor=$IDSocio;
			}
			else{
				$Campo="IDUsuario";
				$Valor=$IDUsuario;
			}


			$solicitudes["IDClub"] = $IDClub;
			$solicitudes["IDSocio"] = $IDSocio;
			$solicitudes["IDUsuario"] = $IDUsuario;
			$Totalregistros=0;

			//Certificados
			$response_certif = array();
			$sql = "SELECT * FROM LaboralCertificado WHERE ".$Campo." = '".$Valor."'  ORDER BY FechaTrCr DESC";
			$qry = $dbo->query( $sql );
			while( $r = $dbo->fetchArray( $qry ) ){
				$Totalregistros++;
				$certif["Modulo"] = "Certificado";
				if($r["IDEstado"]==1){
					$certif["Tipo"] = "Pendientes";
				}
				else{
					$certif["Tipo"] = "Realizados";
				}
				$certif["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
				$certif["Fechas"] = $r["Fechas"];
				$certif["AnombreDe"] = $r["AnombreDe"];
				$certif["Comentario"] = $r["Comentario"];
				$certif["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
				$certif["FechaAprobacion"] = $r["FechaAprobacion"];
				$certif["Archivo"] = CLUB_ROOT.$r["Archivo"];
				array_push($response_certif, $certif);
			}//end while
			$solicitudes["Certificados"] = $response_certif;


			//Compensatorio
			$response_compen = array();
			$sql = "SELECT * FROM LaboralCompensatorio WHERE ".$Campo." = '".$Valor."'  ORDER BY FechaTrCr DESC";
			$qry = $dbo->query( $sql );
			while( $r = $dbo->fetchArray( $qry ) ){
				$Totalregistros++;
				$compensa["Modulo"] = "Compensatorio";
				if($r["IDEstado"]==1){
					$compensa["Tipo"] = "Pendientes";
				}
				else{
					$compensa["Tipo"] = "Realizados";
				}
				$compensa["Estado"] = SIMResources::$estado_laboral[$r["IDEstado"]];
				$compensa["FechaInicio"] = $r["FechaInicio"];
				$compensa["DiasTomar"] = $r["DiasTomar"];
				$compensa["Comentario"] = $r["Comentario"];
				$compensa["ComentarioAprobacion"] = $r["ComentarioAprobacion"];
				$compensa["FechaAprobacion"] = $r["FechaAprobacion"];
				array_push($response_compen, $compensa);
			}//end while
			$solicitudes["Compensatorio"] = $response_compen;

			//Permisos
			$response_permiso = array();
			$sql = "SELECT * FROM LaboralPermiso WHERE ".$Campo." = '".$Valor."'  ORDER BY FechaTrCr DESC";
			$qry = $dbo->query( $sql );
			while( $r = $dbo->fetchArray( $qry ) ){
				$Totalregistros++;
				$permiso["Modulo"] = "Permisos";
				if($r["IDEstado"]==1){
					$permiso["Tipo"] = "Pendientes";
				}
				else{
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
			}//end while
			$solicitudes["Permisos"] = $response_permiso;

			//Vacaciones
			$response_vacac = array();
			$sql = "SELECT * FROM LaboralVacaciones WHERE ".$Campo." = '".$Valor."'  ORDER BY FechaTrCr DESC";
			$qry = $dbo->query( $sql );
			while( $r = $dbo->fetchArray( $qry ) ){
				$Totalregistros++;
				$vacac["Modulo"] = "Vacaciones";
				if($r["IDEstado"]==1){
					$vacac["Tipo"] = "Pendientes";
				}
				else{
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
			}//end while
			$solicitudes["Vacaciones"] = $response_vacac;

			array_push($response, $solicitudes);

			if($Totalregistros>0){
				$message = $dbo->rows( $qry ) . " Encontrados";
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			}
			else
			{
					$respuesta["message"] = "No se encontraron registros";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
			}//end else

			return $respuesta;

		}// fin function


		function get_laboral_calcula_fechafin($IDClub, $fechaInicial, $dias){

			$dbo =& SIMDB::get();
			$response = array();

			//Identificar los días
			$diasNoLaborales = [];
			$diasNoLaborales[] = "Sunday";

			//Consulta si el día sabado es laboral
			$querySabadoLaboral = $dbo->query("SELECT SabadoDialaboral FROM ConfiguracionLaboral WHERE IDClub=" . $IDClub . " AND ACTIVO='S'");
			$consultaSabadoLaboral = $dbo->fetch($querySabadoLaboral)["SabadoDialaboral"];
			if($consultaSabadoLaboral=="N"){
				$diasNoLaborales[] = "Saturday";
			}

			//Identificar los días festivos
			$queryPais = $dbo->query("SELECT P.IDPais FROM Club C INNER JOIN Pais P ON C.IDPais = P.IDPais WHERE C.IDClub=" . $IDClub);
			$IDPais = $dbo->fetch($queryPais)["IDPais"];

			$queryFestivo = $dbo->query("SELECT Fecha FROM Festivos WHERE IDPais=" . $IDPais);
			$festivos = $dbo->fetch($queryFestivo);

			$fechasNoLaborales = [];
			//Si encuentra una sola fecha o muchas
			if(isset($festivos["Fecha"])){
				$fechasNoLaborales[] = date("Y-m-d", strtotime($festivos["Fecha"]));
			}else{
				foreach($festivos as $value){
					$fechasNoLaborales[] = date("Y-m-d", strtotime($value["Fecha"]));
				}
			}
			//Fin identificar los días festivos


			$diasUsados = 0;
			$fechaSiguiente = date("Y-m-d", strtotime($fechaInicial));

			while($diasUsados < $dias){

				$fechaHoy = date("Y-m-d", strtotime($fechaSiguiente));

				$diaSemana = date("l", strtotime($fechaHoy));
				if(!in_array($diaSemana, $diasNoLaborales) && !in_array($fechaHoy, $fechasNoLaborales)){
					$diasUsados++;
				}

				$fechaSiguiente = date("Y-m-d", strtotime($fechaHoy . "+ 1 days"));

			}

			$calcula_fecha["Fecha"] = $fechaHoy;
			$calcula_fecha["Mensaje"] = "Vuelve al trabajo el dia: " . $fechaHoy;



			array_push($response, $calcula_fecha);

			$respuesta["message"] = "Fecha Calculada";
			$respuesta["success"] = true;
			$respuesta["response"] = $calcula_fecha;
			return $respuesta;
		}



} //end class
?>
