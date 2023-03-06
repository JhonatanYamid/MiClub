<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}

$query = $dbo->query("SELECT * FROM VacunaMarca");
$marcaVacunas = $dbo->fetch($query);

if (!empty($_POST["IDEstadoSocio"])) {
	$IDEstadoSocio = " AND S.IDEstadoSocio='" . $_POST["IDEstadoSocio"] . "'";
}

$sql_reporte = "SELECT S.IDSocio, S.UsuarioTrCr AS Usuario, S.FechaTrCr AS FechaCreacion, S.UsuarioTrEd AS UsuarioEdit, S.FechaTrEd AS FechaEdit, S.IDEstadoSocio,S.IDEstadoZeus,S.Accion,S.AccionPadre,S.IDParentesco,S.Genero,S.Nombre,S.Apellido,S.FechaNacimiento,S.NumeroDocumento,S.Email,S.CorreoElectronico,S.Telefono,S.Celular,S.Direccion,S.Dispositivo,S.TipoSocio,S.IDCategoria,S.NumeroInvitados,S.NumeroAccesos,S.PermiteReservar,S.Predio,S.Torre,V.Vacunado,V.IDVacunaMarca,V.Entidad,V.LugarCitaPrimera,V.FechaPrimeraDosis,FechaUltimoIngresoApp 
					From Socio S
					LEFT JOIN Vacuna V ON S.IDSocio=V.IDsocio
					Where S.IDClub = " . $_POST["IDClub"] .  $IDEstadoSocio . " Order By S.IDSocio ASC";


$result_reporte = $dbo->query($sql_reporte);

$nombre = "Usuario:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

//Consulto los campos dinamicos
$sql_campos = "SELECT IDCampoEditarSocio, CED.Nombre
							 FROM CampoEditarSocio CED
							 WHERE CED.IDClub=" . $_POST["IDClub"] . "
							 Order by CED.Orden";
$r_campos = $dbo->query($sql_campos);
while ($r = $dbo->object($r_campos)) {
	$array_preguntas[$r->IDCampoEditarSocio] = $r->Nombre;
}


if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>ID</th>";
	$html .= "<th>Estado</th>";

	if ($_POST["IDClub"] == 70) {
		$html .= "<th>Estado Zeus</th>";
	}

	$html .= "<th>Accion</th>";
	$html .= "<th>Accion Padre</th>";
	$html .= "<th>Parentesco</th>";
	$html .= "<th>Genero</th>";
	$html .= "<th>Nombre</th>";
	$html .= "<th>Apellido</th>";
	$html .= "<th>Fecha Nacimiento</th>";
	$html .= "<th>Numero Documento</th>";
	$html .= "<th>Usuario App</th>";
	$html .= "<th>Correo</th>";
	$html .= "<th>Telefono App</th>";
	$html .= "<th>Celular</th>";
	$html .= "<th>Direccion</th>";
	$html .= "<th>Dispositivo</th>";
	$html .= "<th>Tipo Socio</th>";
	$html .= "<th>Categoria</th>";
	$html .= "<th>Numero Invitados</th>";
	$html .= "<th>Numero Accesos</th>";
	$html .= "<th>Permite Reservar</th>";
	$html .= "<th>Predio</th>";
	$html .= "<th>Torre</th>";
	$html .= "<th>Creador Socio</th>";
	$html .= "<th>Fecha Creacion Socio</th>";
	$html .= "<th>Fecha Ultimo Ingreso A La Aplicacion</th>";

	foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
		$html .= "<th>" . remplaza_tildes(utf8_decode($value_pregunta)) . "</th>";
	}

	$html .= "<th>VACUNADO</th>";
	$html .= "<th>MARCA</th>";
	$html .= "<th>ENTIDAD</th>";
	$html .= "<th>LUGRA PRIMERA DOSIS</th>";
	$html .= "<th>FECHA PRIMERA DOSIS</th>";
	$html .= "<th>LUGRA SEGUNDA DOSIS</th>";
	$html .= "<th>FECHA SEGUNDA DOSIS</th>";

	$html .= "</tr>";

	$style = 'mso-number-format:"@";';
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		unset($array_respuesta);
		//Consulto los campos dinamicos
		$sql_campos = "SELECT CED.IDCampoEditarSocio, SCES.Valor,CED.Nombre
									FROM CampoEditarSocio CED, SocioCampoEditarSocio SCES
									 WHERE SCES.IDCampoEditarSocio=CED.IDCampoEditarSocio AND SCES.IDSocio=" . $Datos["IDSocio"] . "
									 Group by SCES.IDCampoEditarSocio
									 Order by CED.Orden";

		$r_campos = $dbo->query($sql_campos);
		while ($r = $dbo->object($r_campos)) {
			$array_respuesta[$r->IDCampoEditarSocio] = $r->Valor;
		}


		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr>";
		$html .= "<td>" . $Datos["IDSocio"] . "</td>";
		$html .= "<td>" . $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = '" . $Datos["IDEstadoSocio"] . "'") . "</td>";

		if ($_GET["IDClub"] == 70) {
			foreach (SIMResources::$EstadoZeus as $id_tipo => $tipo) {
				if ($id_tipo == $Datos["IDEstadoZeus"]) {
					$html .= "<td>" . $tipo . "</td>";
				}
			}
		}

		$html .= "<td style='" . $style . "'>" . $Datos["Accion"] . "</td>";
		$html .= "<td>" . $Datos["AccionPadre"] . "</td>";
		$html .= "<td>" . $dbo->getFields("Parentesco", "Nombre", "IDParentesco = '" . $Datos["IDParentesco"] . "'") . "</td>";
		$html .= "<td>" . $Datos["Genero"] . "</td>";
		$html .= "<td>" . utf8_decode($Datos["Nombre"]) . "</td>";
		$html .= "<td>" . utf8_decode($Datos["Apellido"]) . "</td>";
		$html .= "<td>" . $Datos["FechaNacimiento"] . "</td>";
		$html .= "<td style='" . $style . "'>" . $Datos["NumeroDocumento"] . "</td>";
		$html .= "<td>" . $Datos["Email"] . "</td>";
		$html .= "<td>" . $Datos["CorreoElectronico"] . "</td>";
		$html .= "<td>" . $Datos["Telefono"] . "</td>";
		$html .= "<td>" . $Datos["Celular"] . "</td>";
		$html .= "<td>" . $Datos["Direccion"] . "</td>";
		$html .= "<td>" . $Datos["Dispositivo"] . "</td>";
		$html .= "<td>" . $Datos["TipoSocio"] . "</td>";
		$html .= "<td>" . $dbo->getFields("Categoria", "Nombre", "IDCategoria = '" . $Datos["IDCategoria"] . "'") . " " . $Datos["Categoria"] . "</td>";
		$html .= "<td>" . $Datos["NumeroInvitados"] . "</td>";
		$html .= "<td>" . $Datos["NumeroAccesos"] . "</td>";
		$html .= "<td>" . $Datos["PermiteReservar"] . "</td>";
		$html .= "<td>" . $Datos["Predio"] . "</td>";
		$html .= "<td>" . $Datos["Torre"] . "</td>";

		if (empty($Datos["Usuario"]))
			$Datos["Usuario"] = $Datos["UsuarioEdit"];

		if ($Datos["FechaCreacion"] == "0000-00-00 00:00:00")
			$Datos["FechaCreacion"] = $Datos["FechaEdit"];

		$html .= "<td>" . $Datos["Usuario"] . "</td>";
		$html .= "<td>" . $Datos["FechaCreacion"] . "</td>";
		$html .= "<td>" . $Datos["FechaUltimoIngresoApp"] . "</td>";


		foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
			$html .= "<th>" . remplaza_tildes(utf8_decode($array_respuesta[$key_pregunta])) . "</th>";
		}

		$html .= "<td>" . utf8_encode($Datos["Vacunado"])  . "</td>";
		$html .= "<td>" . utf8_encode($marcaVacunas[$Datos["IDVacunaMarca"]]["Nombre"])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["Entidad"])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["LugarCitaPrimera "])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["FechaPrimeraDosis"])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["LugarCitaPrimera "])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["FechaPrimeraDosis"])  . "</td>";

		$html .= "</tr>";
	}
	$html .= "</table>";

	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $html;
	exit();
}
