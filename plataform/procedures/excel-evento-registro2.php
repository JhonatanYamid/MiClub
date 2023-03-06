<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$texto = str_replace("ñ", "&ntilde;", $texto);
	$texto = str_replace("á", "&aacute;", $texto);
	$texto = str_replace("é", "&eacute;", $texto);
	$texto = str_replace("í", "&iacute;", $texto);
	$texto = str_replace("ó", "&oacute;", $texto);
	$texto = str_replace("ú", "&uacute;", $texto);
	$texto = str_replace("Ñ", "&Ntilde;", $texto);
	$texto = str_replace("Á", "&Aacute;", $texto);
	$texto = str_replace("É", "&Eacute;", $texto);
	$texto = str_replace("Í", "&Iacute;", $texto);
	$texto = str_replace("Ó", "&Oacute;", $texto);
	$texto = str_replace("Ú", "&Uacute;", $texto);
	return $texto;
}


$sql_reporte = "Select *
					From EventoRegistro2
					Where IDEvento2 = '" . $_GET["IDEvento2"] . "'  Order By IDEventoRegistro2 DESC";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Registros_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>Evento</th>";
	$html .= "<th>DOCUMENTO</th>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>PARTICIPANTE</th>";
	$html .= "<th>EDAD</th>";
	$html .= "<th>TIPO</th>";
	$html .= "<th>CELULAR</th>";
	$html .= "<th>CORREO</th>";
	$html .= "<th>BENEFICIARIO</th>";
	$html .= "<th>EDAD BENEFICIARIO</th>";
	$html .= "<th>FECHA NACIMIENTO BENEFICIARIO</th>";
	$html .= "<th>CEDULA BENEFICIARIO</th>";
	$html .= "<th>TIPO PAGO</th>";
	$html .= "<th>VALOR</th>";
	$html .= "<th>CODIGO PAGO</th>";
	$html .= "<th>ESTADO TRANSACCION</th>";
	$html .= "<th>CODIGO RESPUESTA</th>";
	$html .= "<th>USUARIO</th>";
	$html .= "<th>FECHA REGISTRO</th>";

	//Consulto los campos dinamicos
	$r_campos = &$dbo->all("CampoFormularioEvento2", "IDEvento2 = '" . $_GET["IDEvento2"]  . "'");
	while ($r = $dbo->object($r_campos)) :
		$array_campos[] = $r->IDCampoFormularioEvento2;
		$html .= "<th>" . $r->EtiquetaCampo . "</th>";
	endwhile;

	//Especial lagartos
	if ($_GET["IDEvento2"] == 3043) {
		$html .= "<th>MEDIO DE PAGO</th>";
	}


	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		if ($Datos["UsuarioTrCr"] == "WebService") {
			$NombreUsuario = "Socio";
		} else {
			$DatosIDUsuario = $Datos["UsuarioTrCr"];
			$IDUsuario = explode(" ", $DatosIDUsuario);
			$NombreUsuario = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuario[2] . "'");
		}

		//trae la informacion de la persona que ingresa dependiendo si es socio o no
		if ($Datos["IDNoSocios"] > 0) {
			$persona = $dbo->getFields("NoSocios", array("NumeroDocumento", "Nombre", "CorreoElectronico", "FechaNacimiento", "Celular"), "IDNoSocios = '" . $Datos["IDNoSocios"] . "'");
			$tipo = "Externo";
		} else {
			$persona = $dbo->getFields("Socio", array("NumeroDocumento", "CONCAT(Nombre,' ',Apellido) as Nombre", "CorreoElectronico", "Accion", "FechaNacimiento", "Celular"), "IDSocio = '" . $Datos["IDSocio"] . "'");
			$tipo = "Socio";
		}

		if ($Datos[IDSocioBeneficiario] > 0) :
			$datos_beneficiario = $dbo->fetchAll("Socio", "IDSocio = '$Datos[IDSocioBeneficiario]'");

			if ($datos_beneficiario[IDClub] == $Datos[IDClub]) :
				$NombreBeneficiario = "Benef. " . $datos_beneficiario[Nombre] . " " . $datos_beneficiario[Apellido];
				$EdadBeneficiario =  SIMUtil::Calcular_Edad($datos_beneficiario['FechaNacimiento']);
				$FechaNacimientoBeneficiario = $datos_beneficiario['FechaNacimiento'];
				$CedulaBeneficiario = $datos_beneficiario['NumeroDocumento'];
			else :
				$datos_inivitado = $dbo->fetchAll("SocioInvitado", "IDSocioInvitado = '$Datos[IDSocioBeneficiario]'");
				$NombreBeneficiario = "Inv. " . $datos_inivitado[Nombre];
				$EdadBeneficiario =   "";
				$FechaNacimientoBeneficiario =  "";
				$CedulaBeneficiario = $datos_inivitado['NumeroDocumento'];
			endif;
		else :
			$NombreBeneficiario = "";
			$EdadBeneficiario =   "";
			$FechaNacimientoBeneficiario =  "";
			$CedulaBeneficiario =  "";
		endif;
		$bitacora = "";
		unset($array_datos_seguimiento);
		$cedulaBeneficiario = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $Datos['IDSocioBeneficiario'] . "'");
		$fechaNacimientoBeneficiario = $dbo->getFields("Socio", "FechaNacimiento", "IDSocio = '" . $Datos['IDSocioBeneficiario'] . "'");
		$EdadBeneficiario = ($fechaNacimientoBeneficiario != '') ? SIMUtil::Calcular_Edad($fechaNacimientoBeneficiario) : '';
		$html .= "<tr>";
		$html .= "<td>" . remplaza_tildes(($dbo->getFields("Evento2", "Titular", "IDEvento2 = '" . $Datos["IDEvento2"] . "'"))) . "</td>";
		$html .= "<td>" . utf8_decode($persona["NumeroDocumento"]) . "</td>";
		$html .= "<td>" . utf8_decode(isset($persona["Accion"]) ? $persona['Accion'] : "") . "</td>";
		$html .= "<td>" . remplaza_tildes($persona["Nombre"]) . "</td>";
		$html .= "<td>" . SIMUtil::Calcular_Edad($persona['FechaNacimiento']) . "</td>";
		$html .= "<td>" . utf8_decode($tipo) . "</td>";
		$html .= "<td>" . utf8_decode($persona["Celular"]) . "</td>";
		$html .= "<td>" . $persona["CorreoElectronico"]   . "</td>";
		$html .= "<td>" . remplaza_tildes($NombreBeneficiario) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($EdadBeneficiario)) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($FechaNacimientoBeneficiario)) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($CedulaBeneficiario)) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $Datos["IDTipoPago"] . "'"))) . "</td>";
		$html .= "<td>" . $Datos["Valor"]   . "</td>";
		$html .= "<td>" . $Datos["CodigoPago"]   . "</td>";
		$html .= "<td>" . $Datos["EstadoTransaccion"]   . "</td>";
		$html .= "<td>" . $Datos["CodigoRespuesta"]   . "</td>";


		$html .= "<td>" . remplaza_tildes(utf8_decode($NombreUsuario))  . "</td>";
		$html .= "<td>" . $Datos["FechaTrCr"]   . "</td>";

		//Consulto los campos dinamicos
		$r_campos = &$dbo->all("EventoRegistroDatos2", "IDEventoRegistro2 = '" . $Datos["IDEventoRegistro2"]  . "'");
		while ($rdatos = $dbo->object($r_campos)) :
			$array_otros_datos[$rdatos->IDEventoRegistro2][$rdatos->IDCampoFormularioEvento2] =  ($rdatos->Valor);
		endwhile;

		if (count($array_campos) > 0) :
			foreach ($array_campos as $id_campo) :
				$html .= "<td>" . $array_otros_datos[$Datos["IDEventoRegistro2"]][$id_campo] . "</td>";
			endforeach;
		endif;

		//Especial lagartos
		if ($_GET["IDEvento2"] == 3043) {
			$html .= "<td>" . $array_otros_datos[$Datos["IDEventoRegistro2"]][100000] . "</td>";
		}

		$html .= "</tr>";
	}
	$html .= "</table>";
	//construimos el excel
	header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

	<body>
		<?php
		echo $html;
		?>
	</body>;

	</html>
<?php
	exit();
}
