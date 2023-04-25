<?php
class SIMWebServiceDomiciliario
{



	function get_configuracion_domiciliarios($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT * FROM ConfiguracionDomiciliario WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$datos["IDConfiguracionDomiciliario"] = $r["IDConfiguracionDomiciliario"];
				$datos["LabelFechaIngreso"] = $r["LabelFechaIngreso"];
				$datos["IDClub"] = $r["IDClub"];
				$datos["LabelDocumento"] = $r["LabelDocumento"];

				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $datos;
		} //End if


		return $respuesta;
	}



	function set_domiciliario($IDClub, $IDSocio, $IDUsuario, $Empresa, $Fecha, $Hora, $NombreDomiciliario, $DocumentoDomiciliario, $IDDomicilio)
	{

		$dbo = &SIMDB::get();


		if (!empty($IDClub) && !empty($Empresa)  && !empty($Fecha) && !empty($Hora)) {

			if (!empty($IDSocio)) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
				$HoraIngreso = "";
				$Estado = "P";
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
				$HoraIngreso = date("Y-m-d H:i:s");
				$Estado = "R";
			}
			include("SIMWebServiceAccesos.inc.php");
			if (empty($IDDomicilio)) {
				$sql_inserta = "INSERT INTO Domiciliario ( IDClub," . $Campo . ",Empresa,Fecha,Hora,Nombre,Documento,Estado,FechaHoraIngreso,UsuarioTrCr, FechaTrCr)
				VALUES ('" . $IDClub . "','" . $Valor . "','" . $Empresa . "','" . $Fecha . "', '" . $Hora . "','" . $NombreDomiciliario . "','" . $DocumentoDomiciliario . "','" . $Estado . "','" . $HoraIngreso . "','App',NOW())";
				$dbo->query($sql_inserta);

				SIMWebServiceAccesos::set_autorizacion_contratista($IDClub, $IDSocio, "Diaria", $Fecha, $Fecha, 2, $DocumentoDomiciliario, $NombreDomiciliario, ".", "", "", "", $Hora, "", "Domiciliario", "", "", "", "", "", "", "", "", "", "Domiciliario", "", "0,1,2,3,4,5,6");
			} else {
				$sql_update = "UPDATE Domiciliario
													SET IDClub='" . $IDClub . "',
													IDUsuario='" . $IDUsuario . "',
													Empresa='" . $Empresa . "',
													Fecha='" . $Fecha . "',
													Hora='" . $Hora . "',
													Nombre='" . $NombreDomiciliario . "',
													Documento='" . $DocumentoDomiciliario . "',
													UsuarioTrEd='" . $IDUsuario . "',
													FechaHoraIngreso='" . $HoraIngreso . "',
													Estado='" . $Estado . "',													
													FechaTrEd=NOW()
													WHERE IDDomiciliario = '" . $IDDomicilio . "' ";
				$dbo->query($sql_update);
				SIMWebServiceAccesos::set_contratista_update($IDClub, $IDSocio, "", "", "", 2, $DocumentoDomiciliario, $NombreDomiciliario, ".", "", "");
			}

			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Registradoconexito', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
		} else {
			$respuesta["message"] = "DC1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	} // fin function

	function get_Historial_Domiciliarios($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT *
							FROM Domiciliario
							WHERE IDSocio = '" . $IDSocio . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$datos["IDDomicilio"] = $r["IDDomiciliario"];
				$datos["Empresa"] = $r["Empresa"];
				$datos["Fecha"] = $r["Fecha"];
				$datos["Hora"] = $r["Hora"];
				$datos["NombreDomiciliario"] = $r["Nombre"];
				$datos["DocumentoDomiciliario"] = $r["Documento"];

				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "L2." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}

	function get_Domiciliarios_Pendientes($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();


		$sql = "SELECT *
							FROM Domiciliario
							WHERE IDSocio = '" . $IDSocio . "' and Estado = 'P' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$datos["IDDomicilio"] = $r["IDDomiciliario"];
				$datos["Empresa"] = $r["Empresa"];
				$datos["Fecha"] = $r["Fecha"];
				$datos["Hora"] = $r["Hora"];
				$datos["NombreDomiciliario"] = $r["Nombre"];
				$datos["DocumentoDomiciliario"] = $r["Documento"];

				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "L3." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}


	function get_Domiciliarios_Buscador($IDClub, $IDUsuario, $Tag)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT D.* FROM Domiciliario D, Socio S	WHERE D.IDSocio = S.IDSocio AND (S.Nombre LIKE '%$Tag%' OR S.Predio LIKE '%$Tag%' OR S.Accion LIKE '%$Tag%' OR S.NumeroDocumento LIKE '%$Tag%') AND D.IDClub = '$IDClub' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$datos_socio = $dbo->fetchAll("Socio", "IDSocio=$r[IDSocio]");

				$datos["IDDomicilio"] = $r["IDDomiciliario"];
				$datos["Empresa"] = $r["Empresa"];
				$datos["Fecha"] = $r["Fecha"];
				$datos["Hora"] = $r["Hora"];
				$datos["NombreDomiciliario"] = $r["Nombre"] . "\nAutorizado por: $datos_socio[Nombre] $datos_socio[Apellido]";
				$datos["DocumentoDomiciliario"] = $r["Documento"];

				if ($r[Estado] == "P") :
					$Estado = "Pendiente";
					$Color = "#c92516";
				else :
					$Estado = "Recibido";
					$Color = "#139119";
				endif;

				$datos["EstadoDomiciliario"] = $Estado;
				$datos["ColorEstadoDomiciliario"] = $Color;

				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "L3." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}
} //end class
