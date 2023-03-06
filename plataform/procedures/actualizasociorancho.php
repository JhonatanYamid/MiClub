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
			SIMHTML::jsRedirect("actualizasociorancho.php");
			exit;
		}


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre1"]) && !empty($_POST["Email"]) && !empty($_POST["Celular"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for


			$id = $dbo->insert($_POST, "ActualizacionRancho", "IDActualizacionRancho");

			// Ahora creamos el cuerpo del mensaje
			$msg  .= "A continuaci&oacute;n los datos del socio: \n\n";
			foreach ($_POST as $key => $value)
				if ($key <> "g-recaptcha-response")
					$msg .= $key . " : " . $value . " <br>";

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));


			$correo = "comunicaciones@clubelrancho.com";
			$url_baja = URLROOT . "contactenos.php";
			$mail = new phpmailer();
			$array_correo = explode(",", $correo);
			if (count($array_correo) > 0) {
				foreach ($array_correo as $correo_value) {
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

				SIMUtil::notifica_actualiza_datos("12", $_POST["Email"], $msg);
				SIMHTML::jsRedirect("actualizasociorancho.php?msg=1");
				//SIMHTML::jsRedirect( "miclubrancho://open" );
				exit;
			} else
				SIMHTML::jsRedirect("actualizasociorancho.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("actualizasociorancho.php");
			exit;
		endif;
		break;
}//end switch
