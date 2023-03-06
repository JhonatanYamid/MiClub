<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "RegistroCaddie";
$key = "IDRegistroCaddie";
$where = " WHERE c.IDClub = '" . SIMUser::get("club") . "' AND DATE_FORMAT(r.fechaRegistro,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') ";
$script = "sorteoCaddie";





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

                    $where .= " AND ( c.nombre LIKE '%" . $search_object->data . "%' OR c.apellido LIKE '%" . $search_object->data . "%' OR c.numeroDocumento LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . "  c." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
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
    $sidx = "c.nombre";
// connect to the database

$sqlCount = "SELECT COUNT(c.IDCaddie) AS count "
                . "FROM $table r "
                . "INNER JOIN Caddie c ON(r.IDCaddie = c.IDCaddie) "
                . "$where  "
                . "AND c.IDClub = " . SIMUser::get("club");

$result = $dbo->query($sqlCount);
$row = $dbo->fetchArray($result);
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

//$limit = 58;

    $sql = "SELECT c.IDCaddie, c.numeroDocumento, c.nombre, c.apellido, c.IDCategoriaCaddie, cc.nombre AS categoria "
                . "FROM $table r "
                . "INNER JOIN Caddie c ON(r.IDCaddie = c.IDCaddie)"
                . "INNER JOIN CategoriaCaddie cc ON(c.IDCategoriaCaddie = cc.IDCategoriaCaddie) "
                . "$where "
                . "ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

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

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "nombre" => utf8_encode($row["nombre"]),
            "apellido" => utf8_encode($row["apellido"]),
            "numeroDocumento" => $row["numeroDocumento"],
            "categoria" => $row["categoria"],
        );

    $i++;
}



echo json_encode($responce);
?>
