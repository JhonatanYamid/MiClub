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
			SIMHTML::jsRedirect("registrofontanar.php");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			$id = $dbo->insert($_POST, "ActualizacionFontanar", "IDAActualizacionFontanar");

			for ($i = 1; $i <= $_POST["TotalBenef"]; $i++) {
				if ($_POST["NombreBeneficiario" . $i]) {
					$sql_benef = "INSERT INTO ActualizacionFontanarBeneficiarios 
													(IDActualizacionFontanar,
													NombreBeneficiario,
													ApellidoBeneficiario,
													ConjuntoBeneficiario,
													CasaBeneficiario,
													FechaNacimientoBeneficiario,
													NumeroDocumentoBeneficiario,
													CorreoBeneficiario,
													TelefonoBeneficiario,
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreBeneficiario" . $i] . "',
													'" . $_POST["ApellidoBeneficiario" . $i] . "',
													'" . $_POST["Conjunto"] . "',
													'" . $_POST["Casa"] . "',
													'" . $_POST["FechaNacimientoBeneficiario" . $i] . "',
													'" . $_POST["NumeroIdentificacionBeneficiario" . $i] . "',
													'" . $_POST["CorreoBeneficiario" . $i] . "',
													'" . $_POST["TelefonoBeneficiario" . $i] . "',
													NOW())";
					$dbo->query($sql_benef);
				}
			}

			for ($i = 1; $i <= $_POST["TotalEmpleados"]; $i++) {
				if ($_POST["NombreEmpleado" . $i]) {
					$sql_benef = "INSERT INTO ActualizacionFontanarBeneficiarios 
													(IDActualizacionFontanar,
													NombreBeneficiario,
													ApellidoBeneficiario,
													ConjuntoBeneficiario,
													CasaBeneficiario,
													FechaNacimientoBeneficiario,
													NumeroDocumentoBeneficiario,
													CorreoBeneficiario,
													TelefonoBeneficiario,
													Empleado,
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreEmpleado" . $i] . "',
													'" . $_POST["ApellidoEmpleado" . $i] . "',
													'" . $_POST["Conjunto"] . "',
													'" . $_POST["Casa"] . "',
													'" . $_POST["FechaNacimientoEmpleado" . $i] . "',
													'" . $_POST["NumeroIdentificacionEmpleado" . $i] . "',
													'" . $_POST["CorreoEmpleado" . $i] . "',
													'" . $_POST["TelefonoEmpleado" . $i] . "',
													'" . $_POST["Empleado" . $i] . "',
													NOW())";
					$dbo->query($sql_benef);
				}
			}


			// Ahora creamos el cuerpo del mensaje
			$msg  .= "A continuaci&oacute;n los datos del socio: \n\n";
			foreach ($_POST as $key => $value)
				if ($key <> "g-recaptcha-response")
					$msg .= $key . " : " . $value . " <br>";

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));


			$correo = "juandvillamontoya@gmail.com";
			$url_baja = URLROOT . "contactenos.php";
			$mail = new phpmailer();
			$array_correo = explode(",", $correo);
			if (count($array_correo) > 0) {
				foreach ($array_correo as $correo_value) {
					if (empty($correo_value))
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
				SIMHTML::jsRedirect("registrofontanar.php?msg=1");
				//SIMHTML::jsRedirect( "miclubrancho://open" );
				exit;
			} else
				SIMHTML::jsRedirect("registrofontanar.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registrofontanar.php");
			exit;
		endif;
		break;
}//end switch
