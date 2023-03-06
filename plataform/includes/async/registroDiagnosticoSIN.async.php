<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$tipoReporte = empty($_SESSION["TipoRepDiagnostico"]) ? exit : $_SESSION["TipoRepDiagnostico"];

$get =  SIMUtil::makeSafe($_GET);

//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$key = "IDSocio";

$condicion = array();
$condicionSubQuery = array();

if (!empty(SIMUser::get("club"))) {
	$condicion[] = " AND S.IDClub = " . SIMUser::get("club");

	$condicionSubQuery[] = "D.IDClub = " . SIMUser::get("club");
}

// Origen de datos SQL 
if ($tipoReporte == "Socio") {

	$sql = "SELECT S.IDSocio,S.IDClub,TRIM(CONCAT(S.Nombre,' ',S.Apellido)) AS Nombre,ES.Nombre AS Estado,S.Celular
		FROM Socio S
		INNER JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
	
		WHERE S.IDSocio NOT IN 
		";

	$sqlSubQuery = "SELECT DR.IDSocio
			FROM Diagnostico D
			INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
			";
} elseif ($tipoReporte == "Funcionario") {

	$sql = "SELECT S.IDUSuario AS IDSocio,S.IDClub,TRIM(CONCAT(S.Nombre)) AS Nombre,S.Telefono
		FROM Usuario S
		WHERE S.IDUsuario NOT IN 
		";

	$sqlSubQuery = "SELECT DR.IDUsuario
			FROM Diagnostico D
			INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
			";

	$condicion[] = " S.Autorizado = 'S'";
	$condicionSubQuery[] = "DR.TipoUsuario = 'Funcionario' ";
}


// Crear constrain/condiciones para filtrar datos $where




if (!empty($get["DIA"]))
	$condicionSubQuery[] = "DATE(DR.FechaTrCr) = '" . $get["DIA"] . "'";
else
	$condicionSubQuery[] = "DATE(DR.FechaTrCr) = CURDATE() ";

$get["qryString"] = trim($get["qryString"]);

if (!empty($get["qryString"])) {

	//		$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";		
	if ($tipoReporte == "Socio")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " OR " . SIMUtil::makeboolean("S.Apellido", $get["qryString"]) . " ) ";
	if ($tipoReporte == "Funcionario")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " ) ";
} //end if


!empty($condicionSubQuery) ? $sqlSubQuery .= " WHERE " . implode(" AND ", $condicionSubQuery) : "";

//echo "SUB ".$sqlSubQuery;
if (!empty(SIMUser::get("club"))) {
	$sql .= " ( $sqlSubQuery )  " . implode(" AND ", $condicion);
} else {
	$sql .= " ( $sqlSubQuery ) AND " . implode(" AND ", $condicion);
}

$sql .= " GROUP BY IDSocio ";

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");

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


$sql = $sql . " ORDER BY S.$sidx $sord $groupby LIMIT " . $start . "," . $limit;

//exit;
/* var_dump($sql);
exit; */
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i = 0;
//$hoy = date('Y-m-d');
//print_r($result);

while ($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="socios.php?action=edit&id=' . $row["IDSocio"] . '" alt=""><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Usuario" => $row["Nombre"],
			"Estado" => $row["Estado"],
			"Celular" => $row["Celular"],
		);

	$i++;
}

echo json_encode($responce);
