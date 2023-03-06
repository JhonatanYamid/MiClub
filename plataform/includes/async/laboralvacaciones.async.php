<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
$IDUsuario = SIMUser::get("IDUsuario");

$columns = array();
$origen = SIMNet::req("origen");
$arrIDClubLuker = [95, 96, 97, 98, 122];

$table = "LaboralVacaciones";
$tableJoin = " LEFT JOIN Usuario U ON AuxiliosInfinitoSolicitud.IDUsuario = U.IDUsuario ";
$tableJoin .= " LEFT JOIN Socio S ON AuxiliosInfinitoSolicitud.IDSocio = S.IDSocio ";
$key = "IDLaboralVacaciones";


// Validacion Club para mostrar las solicitudes de los negocios de luker
if (in_array(SIMUser::get('club'), $arrIDClubLuker)) {
	$NegociosLuker = implode(',', $arrIDClubLuker);
	$where = " WHERE LV.IDClub in (" . $NegociosLuker . ") ";
} else {
	$where = " WHERE LV.IDClub = '" . SIMUser::get("club") . "' ";
}
// Fin Validacion Club para mostrar las solicitudes de los negocios de luker

$script = "laboralvacaciones";



$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
if ($datos_usuario['IDPerfil'] != 1 && $datos_usuario['IDPerfil'] != 0) {
	// if ($datos_usuario['IDPerfil'] != 0) {


	$sql_Jefe = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'] . " UNION SELECT IDSocio FROM Socio WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'];
	$q_Jefe = $dbo->query($sql_Jefe);
	$n_Jefe = $dbo->rows($q_Jefe);
	$sql_Especialista = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'] . " UNION SELECT IDSocio FROM Socio WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'];
	$q_Especialista = $dbo->query($sql_Especialista);
	$n_Especialista = $dbo->rows($q_Especialista);
	if ($n_Jefe > 0) {

		$where .= " AND LV.IDEstado NOT IN (3,2) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "')";
	} else if ($n_Especialista > 0) {
		// Cambiar club para luker
		if (in_array(SIMUser::get('club'), $arrIDClubLuker) || SIMUser::get('club') == 169) {
			$where .= " AND LV.IDEstado NOT IN (1,2,3) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "')";
		} else {
			$where .= " AND LV.IDEstado NOT IN (2,3) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "')";
		}
	} else {
		exit;
	}
}

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

switch ($oper) {

	case "del":

		$sql_delete = "DELETE FROM $table WHERE $key = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query($sql_delete);

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Apellido ASC, Nombre";
		$_GET['sord'] = "ASC";


		break;

	case "search":
		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'Usuario':
					$where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR U.Nombre LIKE '%" . $search_object->data . "%' )";
					$where .= " OR ( S.Apellido LIKE '%" . $search_object->data . "%' )";
					break;
				case 'FechaInicio':
					$where .= " AND ( LV.FechaInicio like '%" . $search_object->data . "%' )";
					break;
				case 'FechaFin':
					$where .= " AND ( LV.FechaFin like '%" . $search_object->data . "%' )";
					break;
				case 'DiasTomar':
					$where .= " AND ( LV.DiasTomar = '" . $search_object->data . "' )";
					break;
				case 'Estado':
					$Estado = ucwords($search_object->data);
					$key_EstadoLaboral = array_search($Estado, SIMResources::$estado_laboral);

					if ($key_EstadoLaboral) {
						$where .= " AND ( LV.IDEstado = $key_EstadoLaboral )";
					}
					break;
				case 'qryString':

					break;
			}
		} //end for

		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {

			$Estado = ucwords($qryString);
			$key_EstadoLaboral = array_search($Estado, SIMResources::$estado_laboral);
			if ($key_EstadoLaboral) {
				$whereEstado = " OR LV.IDEstado = $key_EstadoLaboral ";
			}

			$where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' 
			OR U.Nombre LIKE '%" . $qryString . "%'
			OR S.Apellido LIKE '%" . $qryString . "%'
			OR LV.FechaInicio like '%" . $qryString . "%'
			OR LV.FechaFin like '%" . $qryString . "%'
			OR LV.DiasTomar = '" . $qryString . "'
			$whereEstado
			)";
		} //end if
		break;

	case "caculafechafin":
		$fechaInicio = $_GET["fechainicio"];
		$dias = $_GET["dias"];
		$idClub = SIMUser::get("IDClub");
		$fechaFin = SIMWebServiceLaboral::get_laboral_calcula_fechafin($idClub, $fechaInicio, $dias);
		echo json_encode($fechaFin);
		die;
		break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . " LV " . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
	$total_pages = ceil($count / $limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
	$limit = 1000000;


$estadosLaboral =  SIMResources::$estado_laboral;
$sql = "SELECT LV.*, U.Nombre AS NombreUsuario, CONCAT(S.Nombre, ' ', S.Apellido) AS NombreSocio
	FROM LaboralVacaciones LV
LEFT JOIN Usuario U ON LV.IDUsuario = U.IDUsuario
    LEFT JOIN Socio S ON LV.IDSocio = S.IDSocio 
    $where  ORDER BY $sidx $sord " . $str_limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];
	$responce->rows[$i]['IDEstado'] = $row['IDEstado'];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";

	//Transformar datos a retornar
	if (!empty($row["NombreUsuario"])) {
		$usuario = $row['NombreUsuario'];
	} else if (!empty($row["NombreSocio"])) {
		$usuario = $row['NombreSocio'];
	}
	$estado = $estadosLaboral[$row["IDEstado"]];
	$FechaInicio = date('Y-m-d', strtotime($row['FechaInicio']));

	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Usuario" => $usuario,
			"FechaInicio" => $FechaInicio,
			"FechaFin" => $row["FechaFin"],
			"DiasTomar" => $row["DiasTomar"],
			"Estado" => $estado,
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);
	else
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Usuario" => $usuario,
			"FechaInicio" => $FechaInicio,
			"FechaFin" => $row["FechaFin"],
			"DiasTomar" => $row["DiasTomar"],
			"Estado" => $estado,
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}

echo json_encode($responce);
