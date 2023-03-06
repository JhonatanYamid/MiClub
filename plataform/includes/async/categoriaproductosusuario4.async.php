<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$version = "4";
$table = "CategoriaProducto" . $version;
$key = "IDCategoriaProducto";
$where = " WHERE CP.IDClub = '" . SIMUser::get("club") . "' ";
$whereCou = " WHERE CP.IDClub = '" . SIMUser::get("club") . "' ";
$script = "categoriaproductosusuario" . $version;

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Apellido ASC, Nombre";
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

                case 'Nombre':
                    $where .= " AND ( CP.Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Restaurante':

                    $sqlProdc = "SELECT IDRestauranteDomicilio FROM RestauranteDomicilio$version WHERE Nombre LIKE '%$search_object->data%'";
                    $qryProdc = $dbo->query($sqlProdc);

                    while ($row = $dbo->fetchArray($qryProdc)) :
                        $arrayProductos[] = $row["IDRestauranteDomicilio"];
                    endwhile;

                    if (count($arrayProductos) > 0) :
                        $idprodBuscar = implode(",", $arrayProductos);
                    else :
                        $idprodBuscar = 0;
                    endif;

                    $where .= " AND ( CP.IDRestauranteDomicilio IN ($idprodBuscar))";
                    break;

                case 'Descripcion':
                    $where .= " AND ( CP.Descripcion LIKE '%" . $search_object->data . "%' )";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "CP.Nombre";
}

// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . " CP" . $whereCou . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$sql = "SELECT CP.* FROM " . $table . " CP" . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    $Restaurante = $dbo->getFields("RestauranteDomicilio" . $version, "Nombre", "IDRestauranteDomicilio = '" . $row["IDRestauranteDomicilio"] . "'");
    if (empty($Restaurante)) {
        $Restaurante = "Todos";
    }

    if ($row["Publicar"] == 'S') {
        $checkPermiteSi = 'checked';
        $checkPermiteNo = '';
    } else {
        $checkPermiteNo = 'checked';
        $checkPermiteSi = '';
    }

    $btn_permitir = '<input type="radio" value="S" class="btnpublicarcategoria" name="permite' . $row[$key] . '" version = "' . $version . '"  IDCategoria="' . $row[$key] . '" ' . $checkPermiteSi . '>Si<br>';
    $btn_permitir .= '<input type="radio" value="N" class="btnpublicarcategoria" name="permite' . $row[$key] . '"  version = "' . $version . '" IDCategoria="' . $row[$key] . '" ' . $checkPermiteNo . '>No<br>';
    $btn_permitir .= "<div name='msgupdatecategoria" . $row[$key] . "' id='msgupdatecategoria" . $row[$key] . "'></div>";

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Nombre" => $row["Nombre"],
            "Restaurante" => $Restaurante,
            "Descripcion" => $row["Descripcion"],
            "Publicar" => $btn_permitir,
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
        );
    }

    $i++;
}

echo json_encode($responce);
