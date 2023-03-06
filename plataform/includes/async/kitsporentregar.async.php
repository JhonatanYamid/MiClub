<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "RegistroCorredor";
$key = "IDRegistroCorredor";
$IDCarrera = $_GET["IDCarrera"];
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' AND IDCarrera='" . $IDCarrera . "' AND KitEntregado=''";
$script = "registrocorredor";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
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

                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'IDCategoriaTriatlon1':
                    $idCategoriaTriatlon = $search_object->data;
                    if ($idCategoriaTriatlon != "") {
                        $where .= " AND IDCategoriaTriatlon = '$idCategoriaTriatlon'";
                    }
                    break;

                case 'Nombre':
                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";

                    break;

                case 'Apellido':
                    $where .= " AND ( Apellido LIKE '%" . $search_object->data . "%' )";

                    break;

                case 'NumeroCamiseta':
                    $where .= " AND ( NumCamiseta = '" . $search_object->data . "' )";

                    break;

                case 'Categoria':
                    $sql_est = "SELECT IDCategoriaTriatlon FROM CategoriaTriatlon WHERE Nombre LIKE '%" . $search_object->data . "%' AND IDClub = " . SIMUser::get("club");
                    $result_est = $dbo->query($sql_est);
                    while ($row_est = $dbo->fetchArray($result_est)) :
                        $array_id_est[] = $row_est["IDCategoriaTriatlon"];
                    endwhile;
                    if (count($array_id_est) > 0) :
                        $id_est_buscar = implode(",", $array_id_est);

                    endif;

                    $where .= " AND IDCategoriaTriatlon in (" . $id_est_buscar . ")";
                    break;


                default:
                    $where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( IDCategoria LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}



$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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



$sql = "SELECT " . $table . ".* FROM " . $table . $where  . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
/* var_dump($sql);
exit; */
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],


            "Nombre" => $row["Nombre"],
            "Apellido" => $row["Apellido"],
            "NumeroCamiseta" => $row["NumCamiseta"],
            "Carrera" => $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $row["IDCarrera"] . "'"),
            "Categoria" => $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = '" . $row["IDCategoriaTriatlon"] . "'"),


        );


    $i++;
}

echo json_encode($responce);
