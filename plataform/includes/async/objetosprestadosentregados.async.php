<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "ObjetosPrestados";
$key = "IDObjetosPrestados";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' AND Estado='2'";
$script = "objetosprestados";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM ObjetosPrestados WHERE IDObjetosPrestados = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "FechaTrCr";
        $_GET['sord'] = "ASC";


        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {



                case 'LabelCrearDisponibilidad':
                    $where .= " AND ( LabelCrearDisponibilidad LIKE '%" . $search_object->data . "%' )";
                    break;
                case 'LabelHaciaClub':
                    $where .= " AND ( LabelHaciaClub LIKE '%" . $search_object->data . "%' )";
                    break;


                case 'Activo':
                    if ($search_object->data == "S") {
                        $activo = "1";
                    } else {
                        $activo = "0";
                    }
                    $where .= " AND ( Activo LIKE '%" . $activo . "%' )";
                    break;


                default:
                    $where .=  $array_buqueda->groupOp . " " . $table . "." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Nombres LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "FechaTrEd";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 1;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}




$sql = "SELECT " . $table . ".* FROM " . $table  . $where .  " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;

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


            "Socio" => $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row["IDSocioPrestamo"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row["IDSocioPrestamo"] . "'"),
            "Lugar" => $dbo->getFields("LugarObjetosPrestados", "Nombre", "IDLugarObjetosPrestados = '" . $row["IDLugarObjetosPrestados"] . "'"),
            "NombreObjeto" => $row["Nombre"],
            "CantidadPrestada" => $row["CantidadPrestada"],
            "CantidadPendiente" => $row["CantidadPendiente"],
            "CantidadEntregada" => $row["CantidadEntregada"],


            "Eliminar" => '<a class="red eliminar_objeto_prestado" rel=' . $table . ' id=' . $row[$key] . ' idcategoria=' . $row[IDCategoriaObjetosPrestados] .  ' cantidadprestada=' . $row[CantidadPrestada] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
            "Entregar" => '<a class="green entregar_objeto_prestado" rel=' . $table . ' id=' . $row[$key] . ' idcategoria=' . $row[IDCategoriaObjetosPrestados] .  ' cantidadprestada=' . $row[CantidadPrestada] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-check bigger-130"/></a>'

        );

    $i++;
}

echo json_encode($responce);
