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
			SIMHTML::jsRedirect("registromocawa.php");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			if (filter_var($_POST["CorreoElectronico"], FILTER_VALIDATE_EMAIL)) {
				$_POST["Clave"] = sha1($_POST["Clave"]);

				$club = "SELECT FotoLogoApp, Nombre, CorreoRemitente FROM Club WHERE IDClub = " . $_POST["IDClub"];
				$qryClub = $dbo->query($club);
				$datos_club = $dbo->fetchArray($qryClub);
				$id = $dbo->insert($_POST, "SocioTemporal", "IDSocio");


				$link = "https://www.miclubapp.com/registromocawaConfirmacion.php?IDSocio=" . $id;

				$msg = "<br>Cordial Saludo,<br><br>
								Se ha recibio correctamente los datos.<br><br>Confirma tu correo electronico y establece una clave y contraseña para la APP								
								haciendo clic <a href=" . $link . ">aqui</a><br><br>

								Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
								Cordialmente<br><br>
								<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

				$mensaje = "
										<body>
											<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
												<tr>
													<td>
														<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
													</td>
												</tr>
												<tr>
													<td>" .
					$msg
					. "</td>
												</tr>
											</table>
										</body>
								";


				$url_baja = URLROOT . "contactenos.php";
				$mail = new phpmailer();
				$mail->AddAddress($_POST["CorreoElectronico"]);
				$mail->Subject = "Confirmacion de Correo";
				$mail->Body = $mensaje;
				$mail->IsHTML(true);
				$mail->Sender = $datos_club["CorreoRemitente"];
				$mail->Timeout = 120;
				//$mail->IsSMTP();
				$mail->Port = PUERTO_SMTP;
				$mail->SMTPAuth = true;
				$mail->Host = HOST_SMTP;
				//$mail->Mailer = 'smtp';
				$mail->Password = PASSWORD_SMPT;
				$mail->Username = USER_SMTP;
				$mail->From = $datos_club["CorreoRemitente"];
				$mail->FromName = "Mocawa Resort Club";
				$mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
				$confirm = $mail->Send();

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));
				SIMHTML::jsRedirect("registromocawa.php");
			} else {

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'CorreoInvalido', LANGSESSION));
				SIMHTML::jsRedirect("registromocawa.php");
				exit;
			}

		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registromocawa.php");
			exit;
		endif;
		break;
	case "confirmar":

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
			SIMHTML::jsRedirect("registromocawaConfirmacion.php?IDSocio=$_POST[IDSocioTemp]");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			if (filter_var($_POST["CorreoElectronico"], FILTER_VALIDATE_EMAIL)) {
				$_POST["Clave"] = sha1($_POST["NumeroDocumento"]);

				$id = $dbo->insert($_POST, "Socio", "IDSocio");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));
				SIMHTML::jsRedirect("registromocawa.php");
			} else {
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'CorreoInvalido', LANGSESSION));
				SIMHTML::jsRedirect("registromocawa.php");
				exit;
			}

		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registromocawaConfirmacion.php?IDSocio=$_POST[IDSocioTemp]");
			exit;
		endif;
		break;
}//end switch
