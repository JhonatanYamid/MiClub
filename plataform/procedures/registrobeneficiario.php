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
			SIMHTML::jsRedirect("registrobeneficiario.php");
			exit;
		}


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["Email"]) && !empty($_POST["Telefono"]) && !empty($_POST["UnidadResidencial"]) && !empty($_POST["Parentesco"]) && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			$AccionNuevo = $_POST["UnidadResidencial"] . rand(1, 90000);
			$post = [
				'key' => 'CEr0CLUB',
				'IDClub' => '32',
				'action'   => 'setsocio',
				'Accion'   => $AccionNuevo,
				'AccionPadre'   => $_POST["UnidadResidencial"],
				'Parentesco'   => $_POST["Parentesco"],
				'Genero'   => $_POST["Genero"],
				'Nombre'   => $_POST["Nombre"],
				'Apellido'   => $_POST["Apellido"],
				'NumeroDocumento'   => $_POST["UnidadResidencial"],
				'CorreoElectronico'   => $_POST["Email"],
				'Telefono'   => $_POST["Telefono"],
				'Celular'   => $_POST["Celular"],
				'Direccion'   => $_POST["Direccion"],
				'TipoSocio'   => "Beneficiario",
				'EstadoSocio'   => "I",
				'InvitacionesPermitidasMes'   => "100",
			];

			$ch = curl_init('https://www.miclubapp.com/services/club.php');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			// execute!
			$response = curl_exec($ch);
			// close the connection, release resources used
			curl_close($ch);
			// do anything you want with your response
			//var_dump($response);


			//$dest = "jpereira@22cero2.com,jorgechirivi@gmail.com";
			$dest = "recepcion@phbijao.com";
			$head  = "From: " . "info@miclubapp.com" . "\r\n";
			$head .= "To: " . $dest . " \r\n";

			// Ahora creamos el cuerpo del mensaje
			$msg  .= "A continuación los datos de solicitud: \n\n";
			foreach ($_POST as $key => $value)
				if ($key <> "g-recaptcha-response")
					$msg .= $key . " : " . $value . " \n";


			$IDSocio  = $dbo->getFields("Socio", "IDSocio", "Accion = '" . $AccionNuevo . "' and IDClub = '32'");
			$msg .= "\n\nIngrese a la plataforma para aceptar/negar la solicitud: https://www.miclubapp.com/plataform/socios.php?action=edit&id=" . $IDSocio;

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito,prontonospondremosencontacto', LANGSESSION));
			// Finalmente enviamos el mensaje
			if (mail($dest, "Solicitud registro beneficiario Bijao Mi Club App", $msg, $head))
				SIMHTML::jsRedirect("registrobeneficiario.php?msg=1");
			else
				SIMHTML::jsRedirect("registrobeneficiario.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registrobeneficiario.php");
			exit;
		endif;
		break;
}//end switch
