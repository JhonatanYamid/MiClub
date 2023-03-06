<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if($frm == null)$frm = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();

$table = "RegistroSocioProductoHistoria";
$key = "IDRegistroSocioProductoHistoria";
$where = " WHERE rh.IDRegistroSocioProducto = " . $frm["idRegistro"] . " ";
$script = "socio";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
				default:
                    $where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
                break;
            }
        }//end for
    break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx)
    $sidx = "IDRegistroSocioProductoHistoria";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
          . "FROM RegistroSocioProductoHistoria rh "
          . "$where  ";

$result = $dbo->query($sqlCount);
$row =  $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages)
    $page = $total_pages;

$start = $limit * $page - $limit; // do not put $limit*($page - 1)
if (empty($limit))
    $limit = 1000000;

$sql = "SELECT rh.Fecha,rh.Evento,rh.FechaIniciaEvento,rh.Observacion,u.Nombre
        FROM $table as rh, Usuario as u $where AND rh.IDUsuario = u.IDUsuario ORDER BY  $sidx $sord LIMIT " . $start . "," . $limit;
//echo $sql;
$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {
    
    $resultArr = array(); 
    $responce->rows[$i]['id'] = $row[$key];
    
    $resultArr = [
        $key => $row[$key],
        'Fecha' => $row['Fecha'],
        'Evento' => $row['Evento'],
        'FechaIniciaEvento' => $row['FechaIniciaEvento'],
        'Usuario' => $row['Nombre'],
        'Observacion' => $row['Observacion'],
    ];

    $responce->rows[$i]['cell'] = $resultArr;
    $i++;
}
echo json_encode($responce);
?>