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
	return $texto;
}


if ($_POST["IDPerfil"] > 1) :
	//Consulto las areas
	$sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $_POST["IDUsuario"] . "'";
	$result_area_usuario = $dbo->query($sql_area_usuario);
	while ($row_area = $dbo->fetchArray($result_area_usuario)) :
		$array_areas[] = $row_area["IDArea"];
	endwhile;
	if (count($array_areas) > 0) :
		$id_areas = implode(",", $array_areas);
	endif;
	$condicion_area = " and Pqr.IDArea in (" . $id_areas . ")";
endif;

if (!empty($_GET["Numero"])) {
	$where .= " AND Pqr.Numero LIKE '%" . $_GET["Numero"] . "%'";
}
if (!empty($_GET["Fecha"])) {
	$where .= " AND Pqr.Fecha LIKE '%" . $_GET["Fecha"] . "%'";
}

if (!empty($_GET["Tipo"])) {
	$sql_tipo_pqr = "Select * From TipoPqr Where Nombre LIKE '%" . $_GET["Tipo"] . "%' and IDClub = '" . SIMUser::get("club") . "'";
	$result_tipo_pqr = $dbo->query($sql_tipo_pqr);
	while ($row_tipo_pqr = $dbo->fetchArray($result_tipo_pqr)) :
		$array_id_tipo[] = $row_tipo_pqr["IDTipoPqr"];
	endwhile;
	if (count($array_id_tipo) > 0) :
		$id_tipo_buscar = implode(",", $array_id_tipo);
	else :
		$id_tipo_buscar = 0;
	endif;

	$where .= " AND   IDTipoPqr in (" . $id_tipo_buscar . ")";
}

if (!empty($_GET["Area"])) {
	//busco el area
	$sql_area_pqr = "Select * From Area Where Nombre LIKE '%" . $_GET["Area"] . "%'";
	$result_area_pqr = $dbo->query($sql_area_pqr);
	while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
		$array_id_area[] = $row_area_pqr["IDArea"];
	endwhile;
	if (count($array_id_area) > 0) :
		$id_areas_buscar = implode(",", $array_id_area);
	else :
		$id_areas_buscar = 0;
	endif;

	$where .= " AND   IDArea in (" . $id_areas_buscar . ")";
}

if (!empty($_GET["TipoSocio"])) {

	$where .= " AND (  S.TipoSocio LIKE '%" . $_GET["TipoSocio"] . "%'  )  ";
}
if (!empty($_GET["Socio"])) {
	$where .= " AND (  S.Nombre LIKE '%" . $_GET["Socio"] . "%' or S.Apellido LIKE '%" . $_GET["Socio"] . "%'  )  ";
}

if (!empty($_GET["Predio"])) {
	$where .= " AND (  S.Predio LIKE '%" . $_GET["Predio"] . "%' )  ";
}

if (!empty($_GET["Descripcion"])) {
	$where .= " AND (  Pqr.Descripcion LIKE '%" . $_GET["Descripcion"] . "%' )  ";
}

if (!empty($_GET["IDPqrEstado"])) {
	//busco el estado


	$where .= " AND (  Pqr.IDPqrEstado LIKE '%" . $_GET["IDPqrEstado"] . "%' )  ";
}

if (!empty($_GET["Asunto"])) {
	$where .= " AND (  Asunto LIKE '%" . $_GET["Asunto"] . "%' )  ";
}
if (!empty($_GET["Responsable"])) {
	//busco el tipo
	$sql_area_pqr = "Select * From Area Where (Nombre LIKE '%" . $_GET["Responsable"] . "%' or  Responsable LIKE '%" . $_GET["Responsable"] . "%' ) and IDClub = '" . SIMUser::get("club") . "'";
	$result_area_pqr = $dbo->query($sql_area_pqr);
	while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
		$array_id_area[] = $row_area_pqr["IDArea"];
	endwhile;
	if (count($array_id_area) > 0) :
		$id_area_buscar = implode(",", $array_id_area);
	else :
		$id_area_buscar = 0;
	endif;

	$where .= " AND   IDArea in (" . $id_area_buscar . ")";
}



$sql_reporte = "Select *
					From Pqr, Socio S
					Where Pqr.IDClub = '" . $_GET["IDClub"] . "' and S.IDSocio = Pqr.IDSocio  " . $where  . " Order By IDPqr DESC";

$result_reporte = $dbo->query($sql_reporte);

/* echo $sql_reporte;
exit; */
$nombre = "Pqr_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NUMERO PQR</th>";
	$html .= "<th>TIPO</th>";
	$html .= "<th>AREA</th>";
	$html .= "<th>MEDIO</th>";
	$html .= "<th>TIPO SOCIO</th>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>SOCIO</th>";
	$html .= "<th>ESTADO</th>";
	$html .= "<th>ASUNTO</th>";
	$html .= "<th>DESCRIPCION</th>";
	$html .= "<th>FECHA QUEJA</th>";
	$html .= "<th>CALIFICACION</th>";
	$html .= "<th>COMENTARIO CALIFICACION</th>";
	$html .= "<th>FECHA FINALIZACION PQR</th>";
	//Consulto columnas del seguimiento
	for ($contador_columna = 1; $contador_columna <= 5; $contador_columna++) :
		$html .= "<th>RESPONSABLE SEGUIMIENTO " . $contador_columna . "</th>";
		$html .= "<th>FECHA SEGUIMIENTO " . $contador_columna . "</th>";
		$html .= "<th>RESPUESTA SEGUIMIENTO " . $contador_columna . "</th>";
	endfor;

	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr>";
		$html .= "<td>" . $Datos["Numero"] . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $Datos["IDTipoPqr"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("Area", "Nombre", "IDArea = '" . $Datos["IDArea"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("PqrMedio", "Nombre", "IDPqrMedio = '" . $Datos["IDPqrMedio"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
		$html .= "<td>" . $dbo->getFields("Socio", "Accion", "IDSocio = '" . $Datos["IDSocio"] . "'") . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
		$html .= "<td>" . utf8_encode($dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $Datos["IDPqrEstado"] . "'"))  . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Asunto"])  . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Descripcion"]) . "</td>";
		$html .= "<td>" . $Datos["Fecha"]   . "</td>";
		if ((int)$Datos["Calificacion"] > 0)
			$calificacion = $Datos["Calificacion"];
		else
			$calificacion = "";

		$html .= "<td>" . $calificacion   . "</td>";
		$html .= "<td>" . $Datos["ComentarioCalificacion"]   . "</td>";
		$html .= "<td>" . $Datos["FechaFinalizacion"]   . "</td>";
		//Consulto el seguimiento
		$sql_seguimiento = "Select * From Detalle_Pqr Where IDPqr = '" . $Datos["IDPqr"] . "'";
		$result_seguimiento = $dbo->query($sql_seguimiento);
		while ($row_seguimiento = $dbo->fetchArray($result_seguimiento)) :
			$html .= "<td>" . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row_seguimiento["IDUsuario"] . "'")   . "</td>";
			$html .= "<td>" . $row_seguimiento["Fecha"]   . "</td>";
			$html .= "<td>" . strip_tags(utf8_encode($row_seguimiento["Respuesta"]))   . "</td>";
		endwhile;
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
	</body>

	</html>
<?php
	exit();
} else {
	echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
?>