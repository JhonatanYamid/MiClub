<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

// Configuracion laboral
$FiltrarPorJefe = $dbo->getFields('ConfiguracionLaboral', "FiltrarPorJefe", "IDClub = " . SIMUser::get("club"));
$NumeroDocumento = $dbo->getFields("Usuario", "NumeroDocumento", " IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");


$columns = array();
$origen = SIMNet::req("origen");


$table = "LaboralPermiso";
$key = "IDLaboralPermiso";
$where = " WHERE LP.IDClub = '" . SIMUser::get("club") . "' ";
$script = "laboralpermisos";

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
				case 'qryString':

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  LV." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					break;
			}
		} //end for




		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
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
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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
$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . SIMUser::get("IDUsuario") . "' ", "array");

if ($FiltrarPorJefe != 'S' || $datos_usuario['IDPerfil'] == 1 || $datos_usuario['IDPerfil'] == 0) {
	//$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
	$sql = "SELECT LP.*, U.Nombre AS NombreUsuario, CONCAT(S.Nombre, ' ', S.Apellido) AS NombreSocio
	FROM LaboralPermiso LP
    LEFT JOIN Usuario U ON LP.IdUsuario = U.IDUsuario
    LEFT JOIN Socio S ON LP.IDSocio = S.IDSocio 
    $where  ORDER BY $sidx $sord " . $str_limit;
} else {
	$sql = "SELECT LP.*, u.Nombre AS NombreUsuario, CONCAT(s.Nombre, ' ',s.Apellido) AS NombreSocio 
	FROM LaboralPermiso LP
	LEFT JOIN Socio s ON LP.IDSocio=s.IDSocio 
	LEFT JOIN Usuario u ON LP.IDUsuario=u.IDUsuario 
	$where AND (u.DocumentoJefe = " . $NumeroDocumento . " OR u.DocumentoEspecialista = " . $NumeroDocumento . " OR s.DocumentoJefe= " . $NumeroDocumento . " OR s.DocumentoEspecialista=" . $NumeroDocumento . ")  ORDER BY $sidx $sord " . $str_limit;
}
// echo $sql;
// die();
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";

	//Transformar datos a retornar
	if (!empty($row["NombreUsuario"])) {
		$usuario = $row['NombreUsuario'];
	} else if (!empty($row["NombreSocio"])) {
		$usuario = $row['NombreSocio'];
	}
	$estado = $estadosLaboral[$row["IDEstado"]];

	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Usuario" => $usuario,
			"FechaInicio" => $row["FechaInicio"],
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
			"FechaInicio" => $row["FechaInicio"],
			"FechaFin" => $row["FechaFin"],
			"DiasTomar" => $row["DiasTomar"],
			"Estado" => $estado,
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}

echo json_encode($responce);
