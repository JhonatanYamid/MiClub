<?php

switch ($_POST["action"]) {

	case "insert":


		require_once "recaptchalib.php";
		// tu clave secreta
		$secret = "6LdtvCYTAAAAAJKmrTc2Pak5PR2D_Uf-2c82yFsW";

		// respuesta vacía
		$response = null;

		// comprueba la clave secreta


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["NumeroDocumentoTitular"])) :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			//$id = $dbo->insert($_POST, "ActualizacionArrayanes", "IDActualizacionArrayanes");

			$cant = $_POST["TotalBenef"];

			//recibo los datos del titular
			$tipoid = $_POST["Tipo"];
			$numero = $_POST["NumeroDocumentoTitular"];
			$primer_nom = $_POST["PrimerNombreTitular"];
			$segundo_nomb = $_POST["SegundoNombreTitular"];
			$primer_apellido = $_POST["PrimerApellidoTitular"];
			$segundo_apellido = $_POST["SegundoApellidoTitular"];
			$invitador = $_POST["NumeroDocumentoTitular"];

			$validar_invitados = "SELECT COUNT(*) AS Total  from ParticipantesEvento where IDInvita='$numero'";
			$result = $dbo->query($validar_invitados);

			while ($consulta = mysqli_fetch_array($result)) {


				$total = $consulta['Total'];



				if ($total >= 3) {

					SIMHTML::jsAlert("Lo sentimos, ya ingreso el maximo de invitados");

					SIMHTML::jsRedirect("participantes.php");
				} else {

					for ($i = 1; $i <= $cant; $i++) {
						if ($_POST["PrimerNombre" . $i]) {
							$Tipo = "Acompañante";
							$sql_invitados = "INSERT INTO `ParticipantesEvento`( `IDTipoDocumento`, `NumeroDocumento`, `PrimerNombre`, `SegundoNombre`, `PrimerApellido`, `SegundoApellido`, `IDInvita`, `Tipo`, `date`) VALUES('" . $_POST["Tipo" . $i] . "','" . $_POST["NumeroDocumento" . $i] . "','" . $_POST["PrimerNombre" . $i] . "','" . $_POST["SegundoNombre" . $i] . "','" . $_POST["PrimerApellido" . $i] . "','" . $_POST["SegundoApellido" . $i] . "', '" . $invitador . "', '" . $Tipo . "',NOW())";
							$dbo->query($sql_invitados);
						}
					}
				}


				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito', LANGSESSION));

				SIMHTML::jsRedirect("participantes.php");
			}


			$validar_invitados = "SELECT COUNT(*) AS Totales  from ParticipantesEvento where NumeroDocumento='$numero'";
			$result = $dbo->query($validar_invitados);

			while ($consulta = mysqli_fetch_array($result)) {


				$total = $consulta['Totales'];



				if ($total == 0) {
					$Tipo = "Participante";

					$sql_titular = "INSERT INTO `ParticipantesEvento`( `IDTipoDocumento`, `NumeroDocumento`, `PrimerNombre`, `SegundoNombre`, `PrimerApellido`, `SegundoApellido`,`Tipo`, `date`) VALUES('" . $tipoid . "','" . $numero . "','" . $primer_nom . "','" . $segundo_nomb . "','" . $primer_apellido . "','" . $segundo_apellido . "','" . $Tipo . "',NOW())";

					//$sql_titular = "INSERT INTO `ParticipantesEvento`( `IDTipoDocumento`, `NumeroDocumento`, `PrimerNombre`, `SegundoNombre`, `PrimerApellido`, `SegundoApellido`, `IDInvita`, `date`) VALUES('" . $tipoid . "','" . $numero . "','" . $primer_nom . "','" . $segundo_nomb . "','" . $primer_apellido . "','" . $segundo_apellido . "','" . $invitador . "',NOW())";
					$dbo->query($sql_titular);
				}
			}







		// Ahora creamos el cuerpo del mensaje


		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("participantes.php");
			exit;
		endif;
		break;
}//end switch
