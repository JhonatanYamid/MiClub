<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if($frm == null)$frm = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();

$table = "BicicletaAdministracion";
$key = "IDBicicletaAdministracion";
$where = " WHERE ba.IDBicicleta = " . $frm["idBicicleta"] . " ";
$script = "administrarBicicleta";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {

                case 'Estado':
                    $where .= "AND IF(ba.Estado = 1,'Ingresa', IF(ba.Estado = 2, 'En Uso', IF(ba.Estado = 3,'Entregada', IF(ba.Estado = 4,'Solicitada', 'Editada')))) LIKE '%" . $search_object->data . "%' ";
                    break;

                case 'FechaRegistro':
                    $where .= "AND ba.FechaRegistro LIKE '%" . $search_object->data . "%' ";
                    break;

                default:
                    $sqlP = "SELECT bh.IDBicicletaAdministracion, pb.Nombre, bh.Valor 
                            FROM BicicletaHistorico as bh
                                LEFT JOIN PropiedadesBicicleta as pb ON bh.IDPropiedadesBicicleta = pb.IDPropiedadesBicicleta 
                            WHERE pb.Nombre = '".$search_object->field."' AND bh.Valor LIKE '%" . $search_object->data . "%' ";
                    
                    $resultP = $dbo->query($sqlP);
                    $contP = $dbo->rows($resultP);
                    $idsProp = "";

                    if($contP > 0){
                        $x=0;

                        while($rowP = $dbo->fetchArray($resultP)){
                            $idsProp .= $rowP['IDBicicletaAdministracion'];
                            if($x < $contP-1){
                                $idsProp .= ",";
                            }
                            $x++;
                        }

                        $where .= "AND ba.IDBicicletaAdministracion in (".$idsProp.") ";
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
    $sidx = "IDBicicletaAdministracion  ";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
          . "FROM BicicletaAdministracion ba "
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

$sql = "SELECT ba.IDBicicletaAdministracion, IF(ba.Estado = 1,'Ingresa', IF(ba.Estado = 2, 'En Uso', IF(ba.Estado = 3,'Entregada', IF(ba.Estado = 4,'Solicitada', IF(ba.Estado = 5,'Editada', 'Ingresa(por cancelación)'))))) AS NombreEstado, "
        . "ba.FechaRegistro, ba.Estado "
        . "FROM BicicletaAdministracion ba "
        . "INNER JOIN Bicicleta b ON(ba.IDBicicleta = b.IDBicicleta) "
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

$sqlPropiedades = "SELECT bd.IDPropiedadesBicicleta,pb.Nombre
                    FROM BicicletaDetalle  as bd
                        LEFT JOIN PropiedadesBicicleta as pb ON bd.IDPropiedadesBicicleta = pb.IDPropiedadesBicicleta
                    WHERE bd.IDBicicleta = ".$frm["idBicicleta"];
$resultPropiedades = $dbo->query($sqlPropiedades);

while($rowPropiedades = $dbo->fetchArray($resultPropiedades)){
    $idPr = $rowPropiedades['IDPropiedadesBicicleta'];
    $nmPr = $rowPropiedades['Nombre'];

    $arrPropiedades[$idPr] = $nmPr;
}

while ($row = $dbo->fetchArray($result)) {
    
    $resultArr = array(); 
    $responce->rows[$i]['id'] = $row[$key];
    
    $resultArr = [
        $key => $row[$key],
        "Estado" => $row["NombreEstado"],
        "FechaRegistro" => $row["FechaRegistro"]
    ];

    foreach ($arrPropiedades as $clave => $valor) {

        $idProp = $clave;
        $nombreProp = $valor;
        
        $sqlValor = "SELECT bh.Valor 
                    FROM BicicletaHistorico as bh
                        LEFT JOIN PropiedadesBicicleta as pb ON bh.IDPropiedadesBicicleta = pb.IDPropiedadesBicicleta 
                    WHERE bh.IDBicicletaAdministracion = ".$row[$key]." AND bh.IDPropiedadesBicicleta = $idProp";
                    
        $resultValor = $dbo->query($sqlValor);
        $contPr = $dbo->rows($resultValor);

        if($contPr > 0){
            $j=0;
            while($rowValor = $dbo->fetchArray($resultValor)){
                $resultArr[$nombreProp] = $rowValor['Valor'];
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