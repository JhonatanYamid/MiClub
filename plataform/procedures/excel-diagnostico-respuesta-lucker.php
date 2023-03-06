<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}


if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
	$condicion_fecha = " and DR.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and DR.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}

// $sql_reporte = "SELECT DR.IDSocio,DR.IDUsuario,PD.IDPreguntaDiagnostico,IDDiagnosticoRespuesta,DR.NumeroDocumento,DR.TipoUsuario,DR.Nombre,DR.FechaTrCr, IF(DR.IDSocio>0,CONCAT(IDSocio,'-',DR.FechaTrCr),CONCAT(IDUsuario,'-',DR.FechaTrCr)) as FechaRespuesta
// 									FROM PreguntaDiagnostico PD, DiagnosticoRespuesta DR
// 									Where PD.IDPreguntaDiagnostico=DR.IDPreguntaDiagnostico AND  PD.IDDiagnostico = '" . $_POST["IDDiagnostico"] . "' and DR.TipoUsuario = 'Socio' " . $condicion_fecha . "
// 									Group by FechaRespuesta";

$sql_reporte = "SELECT COUNT(DR.IDDiagnosticoRespuesta), DR.IDDiagnostico, DR.IDSocio,DR.IDUsuario,D.Nombre,DR.NumeroDocumento, DR.TipoUsuario, IF(DR.IDSocio>0,S.Accion,U.NumeroDocumento) as Accion, IF(DR.IDSocio>0,CONCAT(S.Nombre,' ',S.Apellido),U.Nombre) as Nombre, DR.Valor, IF(DR.IDSocio>0,CONCAT(DR.IDSocio,'-',DR.FechaTrCr),CONCAT(DR.IDUsuario,'-',DR.FechaTrCr)) as FechaRespuesta, DR.FechaTrCr FROM Diagnostico D, DiagnosticoRespuesta DR LEFT JOIN Socio S ON DR.IDSocio=S.IDSocio LEFT JOIN Usuario U ON DR.IDUsuario=U.IDUsuario Where DR.IDDiagnostico=D.IDDiagnostico AND DR.IDDiagnostico IN (11,16,17) and DR.TipoUsuario = 'Socio' and DR.FechaTrCr >= '2022-03-01 00:00:00' and DR.FechaTrCr <= '2022-03-07 23:59:59' Group by FechaRespuesta";

$result_reporte = $dbo->query($sql_reporte);
$datos_encuesta = $dbo->fetchAll("Diagnostico", " IDDiagnostico = '11' ", "array");

$nombre = "Registros_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);
if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>DIAGNOSTICO</th>";
	$html .= "<th>CEDULA</th>";
	$html .= "<th>TIPO</th>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>USUARIO</th>";
	//Consulto los campos dinamicos
	// $r_campos = &$dbo->all("PreguntaDiagnostico", "IDDiagnostico = '" . $_POST["IDDiagnostico"]  . "' Order by IDPreguntaDiagnostico");
	$html .= "<th>FECHA</th>";
	$html .= "<th>PREGUNTAS ELIMINADAS</th>";
	$html .= "</tr>";

	while ($Datos = $dbo->fetchArray($result_reporte)) {
		// echo '<pre>';
		// print_r($Datos);
		// Validamos que el codigo del Socio/Usuario exista en la tabla Socio/Usuario
		$validaUsuario = 0;
		if ($Datos['TipoUsuario'] == 'Socio') {
			$Val_Socio = $dbo->getFields('Socio', 'IDSocio', "IDSocio = " . $Datos['IDSocio'] . " AND IDEstadoSocio = 1 ");
			$validaUsuario = ($Val_Socio > 0) ? 1 : 0;
		} elseif ($Datos['TipoUsuario'] == 'Usuario') {
			$Val_Usuario = $dbo->getFields('Usuario', 'IDUsuario', "IDUsuario = " . $Datos['IDUsuario'] . " AND Activo = 'S' ");
			$validaUsuario = ($Val_Usuario > 0) ? 1 : 0;
		}
		if ($validaUsuario != 0) {
			$Fecha = "";

			if ($datos_encuesta["DirigidoA"] == "E") {
				$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Datos[IDSocio] . "' ", "array");
				$NombreResponde = ($datos_usuario["Nombre"]);
			} else {
				$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos[IDSocio] . "' ", "array");
				$NombreResponde = ($datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"]);
			}

			if ($Datos["TipoUsuario"] == "Externo") {
				$datos_usuario["NumeroDocumento"] = $Datos["NumeroDocumento"];
				$NombreResponde = $Datos["Nombre"];
			}
			if (!empty($datos_usuario["NumeroDocumento"])) {
				$bitacora = "";
				unset($array_datos_seguimiento);
				$html .= "<tr>";
				$html .= "<td>" . utf8_encode(($dbo->getFields("Diagnostico", "Nombre", "IDDiagnostico = '11'"))) . "</td>";
				$html .= "<td>" . $datos_usuario["NumeroDocumento"] . "</td>";
				$html .= "<td>" . $datos_usuario["TipoSocio"] . "</td>";
				$html .= "<td>" . $datos_usuario["Accion"] . "</td>";
				$html .= "<td>" . utf8_encode($NombreResponde) . "</td>";

				$sql_repuesta_socio = "Select * From DiagnosticoRespuesta DR Where IDSocio = '" . $Datos[IDSocio] . "' and FechaTrCr = '" . $Datos["FechaTrCr"] . "' " . $condicion_fecha . " Group by IDPreguntaDiagnostico";
				$r_respuesta_socio = $dbo->query($sql_repuesta_socio);
				$array_preguntas = array();
				while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
					$array_respuesta_socio[$row_respuesta["IDPreguntaDiagnostico"]] = $row_respuesta["Valor"];
					$array_preguntas[$row_respuesta["IDPreguntaDiagnostico"]] = $row_respuesta["IDPreguntaDiagnostico"];
					$Fecha = $row_respuesta["FechaTrCr"];
				endwhile;
				$html .= "<td>" . $Fecha . "</td>";

				if (count($array_preguntas) > 0) :
					foreach ($array_preguntas as $id_pregunta) :
						$html .= "<td>" .  utf8_encode($array_respuesta_socio[$id_pregunta])   . "</td>";
					endforeach;
				endif;

				$html .= "</tr>";
			}
		}
	}
	$html .= "</table>";
	// echo $html;
	// exit();
	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $html;
	exit();
} else {
	echo " No se encontraron registros";
}
