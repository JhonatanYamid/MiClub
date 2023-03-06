<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];

$script ="registroDiagnostico";

$get =  SIMUtil::makeSafe( $_GET );
$oper = SIMNet::req("oper");

//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$key = "IDDiagnostico";

$condicion = array();

// Origen de datos SQL
if($tipoReporte == "Socio"){
	$sql = "SELECT S.IDSocio,D.IDClub,D.IDDiagnostico,D.Nombre AS NomDiag,D.Descripcion,S.Celular,
				DATE(DR.FechaTrCr) AS DIA,TRIM(CONCAT(S.Nombre,' ',S.Apellido)) AS Nombre,DR.FechaTrCr,ES.Nombre AS Estado
			FROM Diagnostico D
				JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
				JOIN Socio S ON DR.IDSocio = S.IDSocio
				LEFT JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
			";
		if(!empty($get["id"]))
			$condicion[] = "DR.IDSocio = '".$get["id"]."'";

}
		// Origen de datos SQL

if($tipoReporte == "Funcionario"){
	 $sql = "SELECT S.IDUsuario AS IDSocio,D.IDClub,D.IDDiagnostico,D.Nombre AS NomDiag,D.Descripcion,S.Telefono,
				DATE(DR.FechaTrCr) AS DIA,TRIM(S.Nombre) AS Nombre,DR.FechaTrCr
			FROM Diagnostico D
				JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
				JOIN Usuario S ON DR.IDUsuario = S.IDUsuario
			";

			if(!empty($get["id"]))
			 	$condicion[] = "DR.IDUsuario = '".$get["id"]."'";
}

// Crear constrain/condiciones para filtrar datos $where


	!empty(SIMUser::get("club")) ? $condicion[] = "S.IDClub = " . SIMUser::get("club") : "";

	$condicion[] = "DR.TipoUsuario = '".$tipoReporte."'";

	if(!empty($get["DIA"]))
		$condicion[] = "DATE(DR.FechaTrCr) = '".$get["DIA"]."'";

	if(!empty($get["IDES"]) && $tipoReporte == "Socio")
		$condicion[] = "S.IDEstadoSalud = '".$get["IDES"]."'";


	if(!empty($get["week"])){
		$condicion[] = " DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL ".$get["week"]." WEEK AND CURDATE()";

	}


	if( !empty($get["qryString"] )){

//		$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";
		if($tipoReporte == "Socio")
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." OR ".SIMUtil::makeboolean("S.Apellido",$get["qryString"])." ) ";
		if($tipoReporte == "Funcionario")
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." ) ";


	}//end if


!empty($condicion)? $sql .= " WHERE ". implode(" AND ",$condicion): "";


$sql .=" GROUP BY IDSocio,DIA ";

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database


$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");

/*
SELECT count(*) FROM (SELECT S.Nombre,S.Apellido,S.IDClub,ER.IDEncuesta2
FROM Socio S
LEFT JOIN Encuesta2Respuesta ER ON ER.IDSocio = S.IDSocio
$where

SELECT count(*) FROM (SELECT S.Nombre,S.Apellido,S.IDClub,ER.IDEncuesta2
FROM Socio S
LEFT JOIN Encuesta2Respuesta ER ON ER.IDSocio = S.IDSocio
WHERE ER.IDEncuesta2 IS NULL) AS count

*/

$row = $dbo->fetchArray($result);

$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


$sql = $sql." ORDER BY $sidx $sord $groupby LIMIT " . $start . ",".$limit;

//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i=0;
//$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	// A como separador para que los IDs de las filas (row_id) en las grids que OK pues el antiinject elimina otros caracteres como el _
	// Este ID es necesario para pasar a las subgrids, si no el row?id no "pinta la subgrid"

	$responce->rows[$i]['id'] = $row[$key]."A".$row["IDSocio"]."A".$row["DIA"];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
		$responce->rows[$i]['cell'] = array(
						$key => $row[$key],
							"Editar" => '<a class="green" href="socios.php?action=edit&id='.$row["IDSocio"].'" alt=""><i class="ace-icon fa fa-pencil bigger-130"/></a>',
							"Usuario" => $row["Nombre"],
							"DIA" => $row["DIA"],
							"Estado" => $row["Estado"],
							"Celular" => $row["Celular"],
							"NomDiag" => substr($row["NomDiag"],0,40)."...",
						);

	$i++;
}

echo json_encode($responce);

?>
