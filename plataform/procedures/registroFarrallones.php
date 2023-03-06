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
			SIMHTML::jsRedirect("registroENtrelomasMascotas.php");
			exit;
		}
		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Apellido"]) && !empty($_POST["NumeroDocumento"])  && $robot_verificacion == "S") :

			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			}

			for ($i = 1; $i <= $_POST["TotalMascotas"]; $i++) {
				if ($_POST["NombreMascota" . $i]) {

					if (isset($_FILES)) {

						if (!empty($_FILES["FotoVacunasMascota" . $i]['name'])) {
							$files =  SIMFile::upload($_FILES["FotoVacunasMascota" . $i], SOCIO_DIR, "IMAGE");
							if (empty($files) && !empty($_FILES["FotoVacunasMascota" . $i]["name"]))
								SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
							$fotoVacuna = $files[0]["innername"];
						}

						if (!empty($_FILES["FotoMascota" . $i]['name'])) {
							$files =  SIMFile::upload($_FILES["FotoMascota" . $i], SOCIO_DIR, "IMAGE");
							if (empty($files) && !empty($_FILES["FotoMascota" . $i]["name"]))
								SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
							$fotoMascota =  $files[0]["innername"];
						}
					}

					$frm1["NombreMascota"] = $_POST["NombreMascota" . $i];
					$frm1["TipoAnimal"] = $_POST["TipoAnimalMascota" . $i];
					$frm1["Raza"] = $_POST["RazaMascota" . $i];
					$frm1["Edad"] = $_POST["EdadMascota" . $i];
					$frm1["FotoVacunas"] = $fotoVacuna;
					$frm1["FotoMascotas"] = $fotoMascota;
					$frm1["DocumentoDueño"] = $_POST["NumeroDocumento"];
					$frm1["FechaTrCr"] = date("Y-m-d");

					$id = $dbo->insert($frm1, 'ActualizacionMascotasFarrallones', 'IDActualizacionMascotasFarrallones');

					$documento = $dbo->getFields("Socio", "IDSocio", " NumeroDocumento = '" . $_POST["NumeroDocumento"] . "' AND IDClub = 29");

					$frm2["IDSocio"] = $documento;
					$frm2["Nombre"] = $_POST["NombreMascota" . $i];
					$frm2["Edad"] = $_POST["EdadMascota" . $i];
					$frm2["Raza"] = $_POST["RazaMascota" . $i];
					$frm2["Tipo"] = $_POST["TipoAnimalMascota" . $i];
					$frm2["Foto"] = $fotoMascota;
					$frm2["FotoVacuna"] = $fotoVacuna;
					$frm2["FechaTrCr"] = date("Y-m-d");

					$id = $dbo->insert($frm2, 'Mascota', 'IDMascota');
				}
			}

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));

		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("registroEntrelomasMascotas.php");
			exit;
		endif;
		break;
}//end switch
