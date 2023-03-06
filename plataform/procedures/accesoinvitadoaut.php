 <?

	SIMReg::setFromStructure(array(
		"title" => "AccesoInvitados",
		"table" => "SocioInvitado",
		"key" => "IDSocioInvitado",
		"mod" => "SocioInvitado"
	));


	$script = "accesoinvitadoaut";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");


	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "search":

			if (!empty(SIMNet::req("qryString"))) {
				$qryString = str_replace(".", "", SIMNet::req("qryString"));
				$qryString = str_replace(",", "", $qryString);


				//realizo busquedas
				//Guardo el Log de la busqueda
				//$sql_log_peticion =$dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('".SIMUser::get("club")."','".SIMUser::get("IDUsuario")."','".SIMNet::req("qryString")."','Consulta',NOW())");

				//BUSQUEDA CONTRATISTA
				if ($total_resultados <= 0) :
					if (ctype_digit($qryString)) {
						// si es solo numeros en un numero de documento
						$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '" . (int)$qryString . "' or I.NumeroDocumento = '" . $qryString . "') and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '" . SIMUser::get("club") . "'";
						$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa
						//Consulto en invitaciones accesos
						$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '" . SIMUser::get("club") . "'";
						$modo_busqueda = "PLACA";
					}

					$result_invitacion = $dbo->query($sql_invitacion);
					$total_resultados = $dbo->rows($result_invitacion);
					$datos_invitacion = $dbo->fetchArray($result_invitacion);

					if ($datos_invitacion["Ingreso"] == "N") {
						$accion_acceso = "ingreso";
						$label_accion_acceso = "Ingres&oacute;";
					} elseif ($datos_invitacion["Salida"] == "N") {
						$accion_acceso	= "salio";
						$label_accion_acceso	= "Sali&oacute;";
					}

					$datos_invitacion["TipoInvitacion"] = "Contratista " . $datos_invitacion["TipoAutorizacion"];
					//$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
					$datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
					$modulo = "SocioAutorizacion";
					$id_registro = $datos_invitacion["IDSocioAutorizacion"];
				endif;
				//FIN BUSQUEDA CONTRATISTA


				//BUSQUEDA INVITADOS ACCESOS
				if ($total_resultados <= 0) {

					if (ctype_digit($qryString)) {
						// si es solo numeros en un numero de documento
						$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and ( I.NumeroDocumento = '" . (int)$qryString . "' or I.NumeroDocumento = '" . $qryString . "' ) and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '" . SIMUser::get("club") . "' Order By IDSocioInvitadoEspecial";
						$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa
						//Consulto en invitaciones accesos
						$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '" . SIMUser::get("club") . "' Order By IDSocioInvitadoEspecial";
						$modo_busqueda = "PLACA";
					}

					$result_invitacion = $dbo->query($sql_invitacion);
					$total_resultados = $dbo->rows($result_invitacion);
					$datos_invitacion = $dbo->fetchArray($result_invitacion);
					$datos_invitacion["TipoInvitacion"] = "Invitado " . $datos_invitacion["TipoInvitacion"];
					$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
					$datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

					if ($datos_invitacion["Ingreso"] == "N") {
						$accion_acceso = "ingreso";
						$label_accion_acceso = "Ingres&oacute;";
					} elseif ($datos_invitacion["Salida"] == "N") {
						$accion_acceso	= "salio";
						$label_accion_acceso	= "Sali&oacute;";
					}
					$modulo = "SocioInvitadoEspecial";
					$id_registro = $datos_invitacion["IDSocioInvitadoEspecial"];

					//Consulto grupo Familiar
					if ($datos_invitacion["CabezaInvitacion"] == "S") :
						$sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
						$result_grupo = $dbo->query($sql_grupo);
					endif;
				}
				//FIN BUSQUEDA INVITADOS ACCESOS



				//BUSQUEDA INVITADOS GENERAL
				if ($total_resultados <= 0) :
					if (ctype_digit($qryString)) {
						// si es solo numeros en un numero de documento
						$sql_invitacion = "Select SI.* From SocioInvitado SI Where (SI.NumeroDocumento = '" . (int)$qryString . "' or SI.NumeroDocumento = '" . $qryString . "' ) and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . SIMUser::get("club") . "'";
						$modo_busqueda = "DOCUMENTO";
						$result_invitacion = $dbo->query($sql_invitacion);
						$total_resultados = $dbo->rows($result_invitacion);
						$datos_invitacion = $dbo->fetchArray($result_invitacion);
						$datos_invitacion["TipoInvitacion"] = "Invitado ";
						$datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
						$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
						$datos_invitado["Nombre"]  = $datos_invitacion["Nombre"];
						$datos_invitado["NumeroDocumento"] = $datos_invitacion["NumeroDocumento"];
						$datos_invitado["IDTipoDocumento"] = "2"; // le pongo cc a todos ya que en el modulo no se solicita este dato
						$modulo = "SocioInvitado";
						$id_registro = $datos_invitacion["IDSocioInvitado"];
						if ($datos_invitacion["Estado"] == "P") {
							$accion_acceso = "ingreso";
							$label_accion_acceso = "Ingres&oacute;";
						}
					}
				endif;
				//FIN BUSQUEDA CONTRATISTA


				//BUSQUEDA USUARIO FUNCIONARIO
				//para lagartos y my solo pueden ingresar los que tengan invitacion
				//if($total_resultados<=0 && SIMUser::get("club") != 7):
				if ($total_resultados <= 0 && SIMUser::get("club") != 70000 &&  SIMUser::get("club") != 9) :
					$sql_invitacion = "Select * From Usuario Where (NumeroDocumento = '" . $qryString . "' or NumeroDocumento = '" . (int)$qryString . "' ) and  Activo = 'S' and IDClub = '" . SIMUser::get("club") . "'
									UNION
									Select U.* From Usuario U,VehiculoUsuario VU Where U.IDUsuario=VU.IDUsuario and VU.Placa = '" . $qryString . "' and U.Activo  = 'S'  and IDClub = '" . SIMUser::get("club") . "' ";
					$modo_busqueda = "DOCUMENTO";


					$result_invitacion = $dbo->query($sql_invitacion);
					$total_resultados = $dbo->rows($result_invitacion);
					$datos_invitacion = $dbo->fetchArray($result_invitacion);
					$datos_invitacion["TipoInvitacion"] = "Usuario";
					$datos_socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $datos_invitacion["IDUsuario"] . "' ", "array");
					$datos_invitado = $datos_socio;
					$modulo = "Usuario";
					$id_registro = $datos_invitacion["IDUsuario"];

					// BUSACMOS LOS CLUBES QUE TENGAN CONFIGURACIÓN DE VACUNACIÓN CREADA, ESOS YA TIENEN EL MODULO ACTIVO
					$SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion2 WHERE IDClub = " . SIMUser::get("club");
					$QRYConfiguracion = $dbo->query($SQLConfiguracion);

					$Campo = "IDUsuario";
					$alerta_diagnostico = "";

					$AlertaVacunado = true; //LA ALERTA SIEMPRE EN TRUE Y SE CAMBIA SI ES NECESARIO

					if ($dbo->rows($QRYConfiguracion) > 0) :
						// BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
						$SQLVacuna2 = "SELECT * FROM Vacuna2 WHERE $Campo ='$id_registro'";
						$QRYVacuna2 = $dbo->query($SQLVacuna2);
						if ($dbo->rows($QRYVacuna2) > 0) :
							$AlertaVacunado = false;
						else :
							$SQLVacuna2 = "SELECT * FROM Vacunado WHERE $Campo ='$id_registro'";
							$QRYVacuna2 = $dbo->query($SQLVacuna2);
							$datoVacunado = $dbo->fetchArray($QRYVacuna2);

							if (!empty($datoVacunado[CodigoQrGobierno]) || !empty($datoVacunado[ArchivoVacuna])) :
								$AlertaVacunado = false;
							endif;
						endif;
					else :
						// VERFICAMOS LA VERSION 1 DE VACUANCIÓN
						$SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion WHERE IDClub = " . SIMUser::get("club");
						$QRYConfiguracion = $dbo->query($SQLConfiguracion);

						if ($dbo->rows($QRYConfiguracion) > 0) :
							// BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
							$SQLVacuna = "SELECT * FROM Vacuna WHERE $Campo ='$id_registro'";
							$QRYVacuna = $dbo->query($SQLVacuna);
							if ($dbo->rows($QRYVacuna) > 0) :
								$AlertaVacunado = false;
							endif;
						endif;
					endif;

					if ($AlertaVacunado) :
						$alerta_diagnostico .= "<font color='#F14823' size='4px'><b> Atención la persona no ha llenado su información de vacunación.</b></font><br>";
					else :
						$parametros_codigo_qr = URLROOT . "PaginaQRVacunacion.php?Fuente=PC&IDClub=" . SIMUser::get("club") . "&$Campo=$id_registro";

						$alerta_diagnostico .= "<font color='#059C1C' size='4px'><b> Datos de vacunación registrados </b></font><br>";
						$alerta_diagnostico .= "<a href = '$parametros_codigo_qr'><font color='#059C1C' size='2px'><b> Ver Información Vacunación </b></font></a><br>";
					endif;


				endif;
				//FIN BUSQUEDA USUARIO FUNCIONARIO


				//BUSQUEDA SOCIO
				if ($total_resultados <= 0) :
					if (ctype_digit($qryString)) {
						// si es solo numeros en un numero de documento
						$sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" . $qryString . "' or NumeroDocumento = '" . (int)$qryString . "' or Accion = '" . $qryString . "' or Accion = '" . $qryString . $secuencia . "' or NumeroDerecho = '" . $qryString . "' or CodigoCarne = '" . $qryString . "'  ) and IDClub = '" . SIMUser::get("club") . "' AND IDEstadoSocio <> 2 Order by AccionPadre ASC";
						$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa	o una accion
						//Consulto las placas de vehiculos de socios
						$sql_invitacion = "SELECT *
							  						FROM Socio WHERE (Accion = '" . $qryString . "' or Accion = '" . $qryString . $secuencia . "'  or NumeroDerecho = '" . $qryString . "' or CodigoCarne = '" . $qryString . "' or  Predio like '" . $qryString . "'or Email = '" . $qryString . "' or NumeroDocumento = '" . $qryString . "') and IDClub = '" . SIMUser::get("club") . "'AND IDEstadoSocio <> 2
							  						UNION
													SELECT S.* FROM Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . SIMUser::get("club") . "'  and AccionPadre = '" . $qryString . $secuencia . "' AND IDEstadoSocio <> 2
													UNION
													SELECT S.* FROM Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . SIMUser::get("club") . "' AND IDEstadoSocio <> 2
													ORDER BY IDSocio ASC";
					}

					$result_invitacion = $dbo->query($sql_invitacion);
					$total_resultados = $dbo->rows($result_invitacion);
					$datos_invitacion = $dbo->fetchArray($result_invitacion);
					$datos_invitacion["TipoInvitacion"] = "Socio Club";
					$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
					$datos_invitado = $datos_socio;
					$modulo = "Socio";
					$id_registro = $datos_invitacion["IDSocio"];

					//Consulto grupo Familiar
					if (empty($datos_socio["AccionPadre"])) : // Es Cabeza
						$nucleo_socio = 1;
						$condicion_nucleo = " and AccionPadre = '" . $datos_socio["Accion"] . "'";
						$datos_invitacion["CabezaInvitacion"] = "S";
						$response_nucleo = array();
						$sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento FROM Socio WHERE IDClub = '" . SIMUser::get("club") . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' " . $condicion_nucleo;
						$result_grupo = $dbo->query($sql_grupo);
					endif;

					// BUSACMOS LOS CLUBES QUE TENGAN CONFIGURACIÓN DE VACUNACIÓN CREADA, ESOS YA TIENEN EL MODULO ACTIVO
					$SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion2 WHERE IDClub = " . SIMUser::get("club");
					$QRYConfiguracion = $dbo->query($SQLConfiguracion);

					$Campo = "IDSocio";
					$alerta_diagnostico = "";

					$AlertaVacunado = true; //LA ALERTA SIEMPRE EN TRUE Y SE CAMBIA SI ES NECESARIO

					if ($dbo->rows($QRYConfiguracion) > 0) :
						// BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
						$SQLVacuna2 = "SELECT * FROM Vacuna2 WHERE $Campo ='$id_registro'";
						$QRYVacuna2 = $dbo->query($SQLVacuna2);
						if ($dbo->rows($QRYVacuna2) > 0) :
							$AlertaVacunado = false;
						else :
							$SQLVacuna2 = "SELECT * FROM Vacunado WHERE $Campo ='$id_registro'";
							$QRYVacuna2 = $dbo->query($SQLVacuna2);
							$datoVacunado = $dbo->fetchArray($QRYVacuna2);

							if (!empty($datoVacunado[CodigoQrGobierno]) || !empty($datoVacunado[ArchivoVacuna])) :
								$AlertaVacunado = false;
							endif;
						endif;
					else :
						// VERFICAMOS LA VERSION 1 DE VACUANCIÓN
						$SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion WHERE IDClub = " . SIMUser::get("club");
						$QRYConfiguracion = $dbo->query($SQLConfiguracion);

						if ($dbo->rows($QRYConfiguracion) > 0) :
							// BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
							$SQLVacuna = "SELECT * FROM Vacuna WHERE $Campo ='$id_registro'";
							$QRYVacuna = $dbo->query($SQLVacuna);
							if ($dbo->rows($QRYVacuna) > 0) :
								$AlertaVacunado = false;
							endif;
						endif;
					endif;

					if ($AlertaVacunado) :
						$alerta_diagnostico .= "<font color='#F14823' size='4px'><b> Atención la persona no ha llenado su información de vacunación.</b></font><br>";
					else :
						$parametros_codigo_qr = URLROOT . "PaginaQRVacunacion.php?Fuente=PC&IDClub=" . SIMUser::get("club") . "&$Campo=$id_registro";

						$alerta_diagnostico .= "<font color='#059C1C' size='4px'><b> Datos de vacunación registrados </b></font><br>";
						$alerta_diagnostico .= "<a href = '$parametros_codigo_qr'><font color='#059C1C' size='2px'><b> Ver Información Vacunación </b></font></a><br>";
					endif;

				endif;
				//FIN BUSQUEDA SOCIO

				// Si no lo encuntra busco si tiene alguna autorización para otro dia

				if ($total_resultados <= 0) :
					$datos_inv_prox = $dbo->fetchAll("Invitado", " NumeroDocumento = '" . $qryString . "' ", "array");
					if ((int)$datos_inv_prox["IDInvitado"] > 0) :
						//Autorizaciones para otro dia
						$sql_auto_post = "Select * From SocioAutorizacion Where IDInvitado = '" . $datos_inv_prox["IDInvitado"] . "' and FechaInicio > '" . date("Y-m-d") . "'";
						$result_auto_post = $dbo->query($sql_auto_post);
						if ($dbo->rows($result_auto_post) > 0) :
							$row_auto_post = $dbo->fetchArray($result_auto_post);
							$array_proxima_autorizacion[] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una autorizacion para el " .  $row_auto_post["FechaInicio"];
						endif;
						//Invitaciones para otro dia
						$sql_inv_post = "Select * From SocioInvitadoEspecial Where IDInvitado = '" . $datos_inv_prox["IDInvitado"] . "' and FechaInicio > '" . date("Y-m-d") . "'";
						$result_inv_post = $dbo->query($sql_inv_post);
						if ($dbo->rows($result_inv_post) > 0) :
							$row_inv_post = $dbo->fetchArray($result_inv_post);
							$array_proxima_autorizacion[] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una invitacion para el " .  $row_inv_post["FechaInicio"];
						endif;
					endif;
				endif;
			}



			$view = "views/" . $script . "/list.php";
			break;

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
