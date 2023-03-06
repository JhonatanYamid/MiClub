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
			SIMHTML::jsRedirect("registrotowers.php");
			exit;
		}

		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			$id = $dbo->insert($_POST, "ActualizacionTowers", "IDActualizacionTowers");

			for ($i = 1; $i <= $_POST["TotalBenef"]; $i++) {
				if ($_POST["NombreBeneficiario" . $i]) {
					$sql_benef = "INSERT INTO ActualizacionTowersBeneficiarios 
													(IDActualizacionTowers,
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
					$sql_benef = "INSERT INTO ActualizacionTowersBeneficiarios 
													(IDActualizacionTowers,
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

					$sql_mascota = "INSERT INTO ActualizacionMascotasTowers
													(IDActualizacionTowers,
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

					$sql_vehiculo = "INSERT INTO ActualizacionVehiculosTowers
													(IDActualizacionTowers,
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

					$sql_contacto = "INSERT INTO ActulizacionContactosTowers
													(IDActualizacionTowers,
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

			SIMHTML::jsRedirect("registrotowers.php");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registrotowers.php");
			exit;
		endif;
		break;
}//end switch
