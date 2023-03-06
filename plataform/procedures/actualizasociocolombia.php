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
			SIMHTML::jsRedirect("actualizasociocolombia.php");
			exit;
		}


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["NumeroDocumento"]) && !empty($_POST["Nombre"]) && !empty($_POST["Email"]) && !empty($_POST["Celular"]) && !empty($_POST["FechaNacimiento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			//$dest = "jorgechirivi@gmail.com";
			$dest = "gersecre@clubcolombia.org, sistemas@clubcolombia.org, cartera@clubcolombia.org, asiscartera@clubcolombia.org, auxcomunicaciones@clubcolombia.org ";
			$head  = "From: " . "info@miclubapp.com" . "\r\n";
			$head .= "To: " . $dest . " \r\n";

			// Ahora creamos el cuerpo del mensaje
			$msg  .= "A continuaci&oacute;n los datos del socio: \n\n";
			foreach ($_POST as $key => $value)
				if ($key <> "g-recaptcha-response")
					$msg .= $key . " : " . $value . " \n";

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));
			// Finalmente enviamos el mensaje
			if (mail($dest, "Actualizacion Socio", $msg, $head)) {

				SIMUtil::notifica_actualiza_datos("38", $_POST["Email"], $msg);
				//SIMHTML::jsRedirect( "actualizasociocolombia.php?msg=1" );
				SIMHTML::jsRedirect("miclubcolombia://open");
				exit;
			} else
				SIMHTML::jsRedirect("actualizasociocolombia.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("actualizasociocolombia.php");
			exit;
		endif;
		break;

	case "insertfuncionario":


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
			SIMHTML::jsRedirect("actualizafuncionariocolombia.php");
			exit;
		}


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Email"]) && !empty($_POST["Celular"]) && !empty($_POST["Direccion"]) && !empty($_POST["Barrio"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			//$dest = "jorgechirivi@gmail.com";
			$dest = "coordinadoragh@clubcolombia.org,sistemas@clubcolombia.org,asisfundacion@clubcolombia.org";
			$head  = "From: " . "info@miclubapp.com" . "\r\n";
			$head .= "To: " . $dest . " \r\n";

			// Ahora creamos el cuerpo del mensaje
			$msg  .= "A continuación los datos del socio: \n\n";
			foreach ($_POST as $key => $value)
				if ($key <> "g-recaptcha-response")
					$msg .= $key . " : " . $value . " \n";

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito,prontonospondremosencontacto', LANGSESSION));
			// Finalmente enviamos el mensaje
			if (mail($dest, "Actualizacion Funcionario", $msg, $head))
				SIMHTML::jsRedirect("actualizafuncionariocolombia.php?msg=1");
			else
				SIMHTML::jsRedirect("actualizafuncionariocolombia.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("actualizafuncionariocolombia.php");
			exit;
		endif;
		break;
}//end switch
