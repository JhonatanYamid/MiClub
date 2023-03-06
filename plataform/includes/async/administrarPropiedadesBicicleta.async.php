<?php

include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "PropiedadesBicicleta";
$key = "IDPropiedadesBicicleta";
$where = " WHERE IDClub = '" . SIMUser::get("club") . "' ";
$script = "administrarPropiedadesBicicleta";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM PropiedadesBicicleta WHERE IDPropiedadesBicicleta = '" . $_POST["id"] . "' AND IDClub = '" . SIMUser::get("club") . "'  LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Nombre ASC";
        $_GET['sord'] = "ASC";


        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'qryString':

                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . " " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( nombre LIKE '%" . $qryString . "%' )  ";
        } //end if
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx)
    $sidx = "Nombre";
// connect to the database


$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . " " . $where . "    ");
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
$sql = "SELECT IDPropiedadesBicicleta, Nombre "
    . "FROM " . $table . "  "
    . $where . " "
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

    if ((int) SIMUser::get("IDPerfil") <= 1) :
        $btn_eliminar_3 = "<a class='red eliminar_registro' rel=" . $table . " id=" . $row[$key] . " lang = " . $script . " href='#'><i class='ace-icon fa fa-trash-o bigger-130'/></a>";
    else :
        $btn_eliminar_3 = '';
    endif;



    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Nombre" => $row["Nombre"],
            "Eliminar" => '<a class="red eliminar_registro" rel="' . $table . '" id="' . $row[$key] . '" lang="' . $script . '" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'

        );

    $i++;
}



echo json_encode($responce);
