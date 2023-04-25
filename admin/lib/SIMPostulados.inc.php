<?php
class SIMPostulados
{

	function get_configuracion_postulados($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT *
							FROM ConfiguracionPostulados
							WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$configuracion["IDClub"] = $IDClub;
				$configuracion["LabelBotonComentar"] = $r["LabelBotonComentar"];
				$configuracion["LabelTituloResumen"] = $r["LabelTituloResumen"];
				$configuracion["LabelEnviarComentario"] = $r["LabelEnviarComentario"];

				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function get_postulados($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();
		$sql = "SELECT *
								FROM Postulado
								WHERE IDClub = '" . $IDClub . "' and Publicar = 'S' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$postulados["IDClub"] = $IDClub;
				$postulados["IDPostulado"] = $r["IDPostulado"];
				$postulados["Nombre"] = $r["Nombre"];
				$postulados["Resumen"] = $r["Resumen"];
				$postulados["Tipo"] = $r["Tipo"];
				$postulados["Vuelta"] = $r["Vuelta"];
				$postulados["Imagen"] = SOCIO_ROOT . "/" . $r["Imagen"];
				$postulados["Documento"] = SOCIO_ROOT . "/" . $r["Adjunto1File"];
				$response_benef = array();
				for ($i = 1; $i <= 6; $i++) {
					if (!empty($r["NombreBeneficiario" . $i])) {
						$Beneficiario["NombreBeneficiario"] = $r["NombreBeneficiario" . $i];
						$Beneficiario["ParentescoBeneficiario"] = $r["ParentescoBeneficiario" . $i];
						$Beneficiario["ImagenBeneficiario"] = SOCIO_ROOT . "/" . $r["ImagenBeneficiario" . $i];
						array_push($response_benef, $Beneficiario);
					}
				}
				$postulados["Beneficiarios"] = $response_benef;

				array_push($response, $postulados);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function


	function set_comentario_postulado($IDClub, $IDPostulado, $Comentario, $IDSocio)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDPostulado)  && !empty($Comentario)) {

			//verifico que el clasificado exista y pertenezca al club
			$datos_postulado = $dbo->fetchAll("Postulado", " IDPostulado = '" . $IDPostulado . "' and IDClub = '" . $IDClub . "' ", "array");

			if (!empty($datos_postulado["IDPostulado"])) {

				$sql_pregunta = $dbo->query("INSERT INTO PostuladoComentario (IDPostulado, IDSocio, Comentario, UsuarioTrCr, FechaTrCr) Values ('" . $IDPostulado . "','" . $IDSocio . "', '" . $Comentario . "','App',NOW())");
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Comentarioenviadoconexito', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelpostuladonoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		} else {
			$respuesta["message"] = "PT1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}
} //end class
