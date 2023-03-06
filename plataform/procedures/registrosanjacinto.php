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
			SIMHTML::jsRedirect("registrosanjacinto.php");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			$id = $dbo->insert($_POST, "ActualizacionSanJacinto", "IDAActualizacionSanJacinto");

			for ($i = 1; $i <= $_POST["TotalBenef"]; $i++) {

				if ($_POST["NombreBeneficiario" . $i] != "") {
					$sql_benef = "INSERT INTO ActualizacionSanJacintoBeneficiarios 
													(IDActualizacionSanJacinto,
													NombreBeneficiario,
													ApellidoBeneficiario,
													EtapaBeneficiario,
													CasaBeneficiario,
													FechaNacimientoBeneficiario,
													NumeroDocumentoBeneficiario,
													CorreoBeneficiario,
													TelefonoBeneficiario,
													Correo2Beneficiario,
													Telefono2Beneficiario,
													DeportesFeneficiario,
													TipoBeneficiario,
													FechaTrCr)
											VALUES	('" . $id . "',
													'" . $_POST["NombreBeneficiario" . $i] . "',
													'" . $_POST["ApellidoBeneficiario" . $i] . "',
													'" . $_POST["EtapaBeneficiario" . $i] . "',
													'" . $_POST["CasaBeneficiario" . $i] . "',
													'" . $_POST["FechaNacimientoBeneficiario" . $i] . "',
													'" . $_POST["NumeroIdentificacionBeneficiario" . $i] . "',
													'" . $_POST["CorreoBeneficiario" . $i] . "',
													'" . $_POST["TelefonoBeneficiario" . $i] . "',
													'" . $_POST["Correo2Beneficiario" . $i] . "',
													'" . $_POST["Telefono2Beneficiario" . $i] . "',
													'" . $_POST["DeportesBeneficiario" . $i] . "',
													'" . $_POST["TipoBeneficiario" . $i] . "',
													NOW())";
					$dbo->query($sql_benef);
				}
			}


			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));
			SIMHTML::jsRedirect("registrosanjacinto.php");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registrosanjacinto.php");
			exit;
		endif;
		break;
}//end switch
