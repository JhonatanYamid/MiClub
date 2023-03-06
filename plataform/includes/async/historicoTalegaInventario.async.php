<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if($frm == null)$frm = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();

$table = "TalegaAdministracion";
$key = "IDTalegaAdministracion";
$where = " WHERE ta.IDTalega = " . $frm["idTalega"] . " ";
$script = "administrarTalega";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {

                case 'estado':
                    $where .= "AND IF(ta.estado = 1,'Ingresa', IF(ta.estado = 2, 'En campo', IF(ta.estado = 3,'Entregada', IF(ta.estado = 4,'Solicitada', 'Editada')))) LIKE '%" . $search_object->data . "%' ";
                    break;

                case 'fechaRegistro':
                    $where .= "AND ta.fechaRegistro LIKE '%" . $search_object->data . "%' ";
                    break;

                default:
                    $sqlP = "SELECT th.IDTalegaAdministracion, pt.nombre, th.valor 
                            FROM TalegaHistorico as th
                                LEFT JOIN PropiedadesTalega as pt ON th.IDPropiedadesTalega = pt.IDPropiedadesTalega 
                            WHERE pt.nombre = '".$search_object->field."' AND th.valor LIKE '%" . $search_object->data . "%' ";
                    
                    $resultP = $dbo->query($sqlP);
                    $contP = $dbo->rows($resultP);
                    $idsProp = "";

                    if($contP > 0){
                        $x=0;

                        while($rowP = $dbo->fetchArray($resultP)){
                            $idsProp .= $rowP['IDTalegaAdministracion'];
                            if($x < $contP-1){
                                $idsProp .= ",";
                            }
                            $x++;
                        }

                        $where .= "AND ta.IDTalegaAdministracion in (".$idsProp.") ";
                    }
                    
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
    $sidx = "IDTalegaAdministracion  ";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
          . "FROM TalegaAdministracion ta "
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

$sql = "SELECT ta.IDTalegaAdministracion, IF(ta.estado = 1,'Ingresa', IF(ta.estado = 2, 'En campo', IF(ta.estado = 3,'Entregada', IF(ta.estado = 4,'Solicitada', IF(ta.estado = 5,'Editada', 'Ingresa(por cancelación)'))))) AS nombreEstado, "
        . "ta.fechaRegistro, ta.estado "
        . "FROM TalegaAdministracion ta "
        . "INNER JOIN Talega t ON(ta.IDTalega = t.IDTalega) "
        . "$where "
        . "ORDER BY  $sidx $sord LIMIT " . $start . "," . $limit;

$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

$arrPropiedades = array();

$sqlPropiedades = "SELECT td.IDPropiedadesTalega,pt.nombre
                    FROM TalegaDetalle  as td
                        LEFT JOIN PropiedadesTalega as pt ON td.IDPropiedadesTalega = pt.IDPropiedadesTalega
                    WHERE td.IDTalega = ".$frm["idTalega"];
$resultPropiedades = $dbo->query($sqlPropiedades);

while($rowPropiedades = $dbo->fetchArray($resultPropiedades)){
    $idPr = $rowPropiedades['IDPropiedadesTalega'];
    $nmPr = $rowPropiedades['nombre'];

    $arrPropiedades[$idPr] = $nmPr;
}

while ($row = $dbo->fetchArray($result)) {
    
    $resultArr = array(); 
    $responce->rows[$i]['id'] = $row[$key];
    
    $resultArr = [
        $key => $row[$key],
        "estado" => $row["nombreEstado"],
        "fechaRegistro" => $row["fechaRegistro"]
    ];

    foreach ($arrPropiedades as $clave => $valor) {

        $idProp = $clave;
        $nombreProp = $valor;
        
        $sqlValor = "SELECT th.valor 
                    FROM TalegaHistorico as th
                        LEFT JOIN PropiedadesTalega as pt ON th.IDPropiedadesTalega = pt.IDPropiedadesTalega 
                    WHERE th.IDTalegaAdministracion = ".$row[$key]." AND th.IDPropiedadesTalega = $idProp";

        $resultValor = $dbo->query($sqlValor);
        $contPr = $dbo->rows($resultValor);

        if($contPr > 0){
            $j=0;
            while($rowValor = $dbo->fetchArray($resultValor)){
                $resultArr[$nombreProp] = $rowValor['valor'];
                $j++;
            }
            
        }else{
            $resultArr[$nombreProp] = 0;
        }      
    }

    $responce->rows[$i]['cell'] = $resultArr;
    $i++;
}
echo json_encode($responce);
?>