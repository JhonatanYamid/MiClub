<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );


//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$key = "IDSocio";

// Origen de datos SQL 

   $sql="SELECT CED.IDCampoEditarSocio,SCES.Valor,CED.Nombre
			FROM CampoEditarSocio CED
			INNER JOIN SocioCampoEditarSocio SCES ON CED.IDCampoEditarSocio = SCES.IDCampoEditarSocio
                        ";


// Crear constrain/condiciones para filtrar datos $where
	$condicion = array();

	$qryString = SIMNet::req( "id" );
	if( !empty( $qryString ) )
	{
		$condicion[] = " SCES.IDSocio = $qryString ";
				
	}//end if
	
	if(!empty(SIMUser::get("club"))){
		$condicion[] = " CED.IDClub = " . SIMUser::get("club");
	}

		
!empty($condicion)? $sql .= " WHERE ". implode(" AND ", $condicion): "";

$sql .=" GROUP BY SCES.IDCampoEditarSocio ";

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Orden";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");

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


$sql = $sql." ORDER BY CED.$sidx $sord $groupby LIMIT " . $start . ",".$limit;

//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i=0;

while($row = $dbo->fetchArray($result)) {
	
	$responce->rows[$i]['id'] = $row[$key];

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
						$key => $row[$key],
							"Pregunta" => $row["Nombre"],
							"Respuesta" => $row["Valor"],
						);

	$i++;
}

echo json_encode($responce);

?>
