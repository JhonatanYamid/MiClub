<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");
  
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre ASC";
		$_GET['sord'] = "ASC";


	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}
 



$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database
$sqlCount = "SELECT COUNT(*) AS count 
			FROM Facturacion as f, Club as c, Socio as s
			".$where . " AND (f.IDClub = c.IDClub AND f.IDSocio = s.IDSocio) ";

$result = $dbo->query($sqlCount);
$row = $dbo->fetchArray($result);
$count = 10;

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


$sql = "SELECT COUNT(*) as cantidad, m.Nombre as mediopago, IDFacturacion, sum(ValorPagado) as totalpagado FROM `FacturacionMediosPago`, MediosPago m WHERE IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE IDClub='160') and FacturacionMediosPago.IDMediosPago=m.IDMediosPago GROUP by FacturacionMediosPago.IDMediosPago ORDER BY cantidad";
// echo $sql;
// exit;
// var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

while($row = $dbo->fetchArray($result)) {

 
	$responce->rows[$i]['id'] = $row["IDFacturacion"];
 

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
	    $key => $row["IDFacturacion"], 
            "cantidad" =>   $row["cantidad"], 
            "mediopago" =>  $row["mediopago"],
            "totalpagado" => $row["totalpagado"]
		);

	$i++;
}        

echo json_encode($responce);

?>
 
