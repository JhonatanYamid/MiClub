<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if($frm == null)$frm = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


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
                case 'qryString':

                    $where .= " AND (   s.Nombre LIKE '%" . $search_object->data . "%' OR "
                                      . "s.Apellido LIKE '%" . $search_object->data . "%' OR "
                                      . "s.Nombre LIKE '%" . $search_object->data . "%' "
                                      . "OR s.Apellido LIKE '%" . $search_object->data . "%' "
                                      . "OR c.nombreTercero LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
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
          . "INNER JOIN Bicicleta b ON(ba.IDBicicleta = b.IDBicicleta) "
          . "LEFT JOIN Socio s ON(b.IDSocio = s.IDSocio) "
          . "LEFT JOIN Caddie c ON(ba.IDCaddie = c.IDCaddie) "
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

$sql = "SELECT IF(ba.Estado = 1,'Ingresa', IF(ba.Estado = 2, 'En Uso', IF(ba.Estado = 3,'Entregada', IF(ba.Estado = 4,'Solicitada', IF(ba.Estado = 5,'Editada', 'Cancelada'))))) AS NombreEstado, 
            IF(ba.IDConfiguracionBicicletaLugar > 0,l.Nombre,'') as NombreLugar,
            ba.NombreTercero AS tercero, CONCAT_WS(' ',c.Nombre, c.Apellido) AS caddie, ba.FechaRegistro, ba.Estado, ba.Observaciones,
            ba.IDUsuarioRegistra, u.Nombre as Usuario, ba.IDSocioRegistra
        FROM BicicletaAdministracion ba
            INNER JOIN Bicicleta b ON(ba.IDBicicleta = b.IDBicicleta)
            LEFT JOIN Caddie c ON(ba.IDCaddie = c.IDCaddie) 
            LEFT JOIN Usuario u ON (ba.IDUsuarioRegistra = u.IDUsuario)
            LEFT JOIN ConfiguracionBicicletaLugar l ON(ba.IDConfiguracionBicicletaLugar = l.IDConfiguracionBicicletaLugar) "
        . "$where "
        . "ORDER BY  $sidx $sord LIMIT " . $start . "," . $limit;

$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row[$key];
    $nmSocio = "";

    $infoS = $dbo->getFields("Socio", "CONCAT_WS(' ',Nombre, Apellido)", "IDSocio = ".$row['IDSocioRegistra']);
    $infoI = $dbo->getFields("Invitado", "CONCAT_WS(' ',Nombre, Apellido)", "IDInvitado = ".$row['IDSocioRegistra']);
    $infoSi =$dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = ".$row['IDSocioRegistra']);
    
    if(!is_null($infoS) && $infoS != ''){
        $nmSocio = $infoS;
    } 
    else if(!is_null($infoI) && $infoI != ''){
        $nmSocio = $infoI;
    }
    else if(!is_null($infoSi) && $infoSi != ''){
        $nmSocio = $infoSi;
    }

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
    {     
        $socioReg = $row["tercero"] != "" ? $row["tercero"] : $nmSocio;   
        $usuarioReg = $row["caddie"] != "" ? $row["caddie"] : $row['Usuario'];

        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Estado" => $row["NombreEstado"],
            "FechaRegistro" => $row["FechaRegistro"],
            "Lugar" => $row["NombreLugar"],
            "Socio" => $socioReg,
            "Usuario" => $usuarioReg,
            "Observaciones" => $row["Observaciones"],
        );
    }

    $i++;
}

echo json_encode($responce);
?>