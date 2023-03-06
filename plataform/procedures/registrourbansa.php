<?php

switch ($_POST["action"]) {

	case "insert":

		require_once "recaptchalib.php";
		// tu clave secreta
		$secret = "6LdtvCYTAAAAAJKmrTc2Pak5PR2D_Uf-2c82yFsW";

		// respuesta vacía
		$response = null;

		// comprueba la clave secreta
		$reCaptcha = new ReCaptcha($secret);

		// si se detecta la respuesta como enviada
		if ($_POST["g-recaptcha-response"]) {
			$response = $reCaptcha->verifyResponse(
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
		}

		if ($response != null && $response->success) {
			$_SESSION["validcaptcha"] = "okcaptcha" . date("Y-m-d H:i:s");
			//echo $_SESSION["validcaptcha"]="okcaptcha".date("Y-m-d H:i:s");
			//echo "Hi " .  ", thanks for submitting the form!";
			$robot_verificacion = "S";
		} else {
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Debesverificarquenoeresunrobot', LANGSESSION));
			SIMHTML::jsRedirect("registrourbansa.php");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			$id = $dbo->insert($_POST, "ActualizacionUrbansa", "IDActualizacionUrbansa");

			for ($i = 1; $i <= $_POST["TotalBenef"]; $i++) {
				if ($_POST["NombreBeneficiario" . $i]) {
					$sql_benef = "INSERT INTO ActualizacionUrbansaBeneficiarios 
													(IDActualizacionUrbansa,
													NombreBeneficiario,
													ApellidoBeneficiario,
													ParentescoBeneficiario,												
													NumeroDocumentoBeneficiario,
													CorreoBeneficiario,
													ProfesionBeneficiario,
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreBeneficiario" . $i] . "',
													'" . $_POST["ApellidoBeneficiario" . $i] . "',
													'" . $_POST["ParentescoBeneficiario" . $i] . "',												
													'" . $_POST["NumeroIdentificacionBeneficiario" . $i] . "',
													'" . $_POST["CorreoBeneficiario" . $i] . "',
													'" . $_POST["ProfesionBeneficiario" . $i] . "',
													NOW())";
					$dbo->query($sql_benef);
				}
			}

			for ($i = 1; $i <= $_POST["TotalEmpleados"]; $i++) {
				if ($_POST["NombreEmpleado" . $i]) {
					$sql_benef = "INSERT INTO ActualizacionUrbansaBeneficiarios 
													(IDActualizacionUrbansa,
													NombreBeneficiario,
													ApellidoBeneficiario,													
													EdadEmpleado,													
													NumeroDocumentoBeneficiario,																									
													Empleado,
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreEmpleado" . $i] . "',
													'" . $_POST["ApellidoEmpleado" . $i] . "',
													'" . $_POST["EdadEmpleado" . $i] . "',
													'" . $_POST["NumeroIdentificacionEmpleado" . $i] . "',
													'S',
													NOW())";
					$dbo->query($sql_benef);
				}
			}

			for ($i = 1; $i <= $_POST["TotalMascotas"]; $i++) {
				if ($_POST["NombreMascota" . $i]) {

					$sql_mascota = "INSERT INTO ActualizacionMascotasUrbansa
													(IDActualizacionUrbansa,
													NombreMascota,
													EspecieMascota,													
													Raza,													
													Edad,										
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreMascota" . $i] . "',
													'" . $_POST["EspecieMascota" . $i] . "',
													'" . $_POST["RazaMascota" . $i] . "',
													'" . $_POST["EdadMascota" . $i] . "',												
													NOW())";
					$dbo->query($sql_mascota);
				}
			}

			for ($i = 1; $i <= $_POST["TotalVehiculos"]; $i++) {
				if ($_POST["PlacaVehiculo" . $i]) {

					$sql_vehiculo = "INSERT INTO ActualizacionVehiculosUrbansa
													(IDActualizacionUrbansa,
													Marca,
													Color,													
													Placa,													
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["MarcaVehiculo" . $i] . "',
													'" . $_POST["ColorVehiculo" . $i] . "',
													'" . $_POST["PlacaVehiculo" . $i] . "',
													NOW())";
					$dbo->query($sql_vehiculo);
				}
			}

			for ($i = 1; $i <= $_POST["TotalContactos"]; $i++) {
				if ($_POST["NombreContacto" . $i]) {

					$sql_contacto = "INSERT INTO ActulizacionContactosUrbansa
													(IDActualizacionUrbansa,
													Nombre,
													Telefono1,													
													Telefono2,													
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreContacto" . $i] . "',
													'" . $_POST["Telefono1Contacto" . $i] . "',
													'" . $_POST["Telefono2Contacto" . $i] . "',
													NOW())";
					$dbo->query($sql_contacto);
				}
			}

			//info del club
			$datos_club = $dbo->fetchAll("Club", " IDClub = '  8 ' ", "array");
			//socio
			$NombreSocio = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $_GET["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $_GET["IDSocio"] . "'"));

			// Ahora creamos el cuerpo del mensaje con la imagen del logo del club
			$msg  .= "<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>" . "<br><br>" . "Cordial Saludo.  \n\n" . "<br><br>" . "Se ha generado una nueva actualizacion de datos." . "<br><br>" .
				" Recuerde ingresar al sistema para conocer mas detalles. " . "<br><br>" .
				"El Propietario:" . $NombreSocio . " a colocado la  siguiente informacion." . "<br><br>";

			//Enviamos correo
			foreach ($_POST as $key => $value) {

				if (
					$key <> "g-recaptcha-response" && $key <> "IDClub" && $key <> "IDSocio" && $key <> "action"
					&& $key <> "FechaTrCr" && $key <> "FechaRegistro" && $key <> "Año"
				) {


					// $msg .= "<b>" . $key . " : " . "</b>" . $value . " <br>";
					$msg .= "<b>" . "$key" .  " : " . "</b>" .  $value . " <br>";
				}
			}

			//SIMHTML::jsAlert("Datos enviado con exito..");


			$correo = "conjuntonavarrat1@gmail.com,conjuntonavarrat2@gmail.com,conjuntonavarrat2@gmail.com,conjuntonavarrat4@gmail.com";


			$url_baja = URLROOT . "contactenos.php";
			$mail = new phpmailer();
			$array_correo = explode(",", $correo);
			if (count($array_correo) > 0) {
				foreach ($array_correo as $correo_value) {
					if (!empty($correo_value))
						$mail->AddAddress($correo_value);
				}
			}


			$mail->Subject = "Actualizacion de datos";
			$mail->Body = $msg;
			$mail->IsHTML(true);
			$mail->Sender = "info@miclubapp.com";
			$mail->Timeout = 120;
			//$mail->IsSMTP();
			$mail->Port = PUERTO_SMTP;
			$mail->SMTPAuth = true;
			$mail->Host = HOST_SMTP;
			//$mail->Mailer = 'smtp';
			$mail->Password = PASSWORD_SMPT;
			$mail->Username = USER_SMTP;
			$mail->From = "info@miclubapp.com";
			$mail->FromName = "Club";
			$mail->AddCustomHeader("List-Unsubscribe: <mailto:info@miclubapp.com>,  <$url_baja>");
			$confirm = $mail->Send();



			// Finalmente enviamos el mensaje
			if ($confirm) {

				//SIMUtil::notifica_actualiza_datos("11",$_POST["Email"],$msg);
				//SIMHTML::jsRedirect("bquillanoresidentes.php?msg=1&mensaje1=' $mensaje1 '&IDSocio=' $IDSocio '");
				SIMHTML::jsRedirect("registrourbansa.php");
				exit;
			}


			SIMHTML::jsRedirect("registrourbansa.php");

		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registrourbansa.php");
			exit;
		endif;
		break;
}//end switch
