<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

//si el perfil es diferente de administrador averiguo los restaurantes que tiene a cargo
if (SIMUser::get("IDPerfil") != 0) :
    //Consulto las areas
    $sql_restaurante = "Select * From UsuarioRestaurante Where IDUsuario = '" . SIMUser::get("IDUsuario") . "'";
    $result_restaurante_usuario = $dbo->query($sql_restaurante);
    while ($row_restaurante = $dbo->fetchArray($result_restaurante_usuario)) :
        $array_restaurantes[] = $row_restaurante["IDRestauranteDomicilio"];
    endwhile;
    if (count($array_restaurantes) > 0) {
        $id_restaurantes = implode(",", $array_restaurantes);
        $condicion_restaurante = " and IDRestauranteDomicilio in (" . $id_restaurantes . ")";
    } else {
        $condicion_restaurante = "";
    }

endif;

$version = "";
$table = "RestauranteDomicilio" . $version;
$key = "IDRestauranteDomicilio";
$where = " WHERE RD.IDClub = '" . SIMUser::get("club") . "'" . $condicion_restaurante . " AND  RD.DirigidoA = 'E' ";
$whereCou = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  AND " . $table . ".DirigidoA = 'E'";
$script = "restaurantedomiciliousuario" . $version;

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
        $_GET['sidx'] = "Nombre";
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
                    $where .= " AND ( RD.Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Descripcion':
                    $where .= " AND ( RD.Descripcion LIKE '%" . $search_object->data . "%' )";
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
    $sidx = "Nombre";
}

// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $whereCou . "    ");
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

$sql = "SELECT RD.* FROM " . $table . " RD" . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
/* echo $sql;
exit; */
$result = $dbo->query($sql);

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    if ($row["Publicar"] == 'S') {
        $checkPermiteSi = 'checked';
        $checkPermiteNo = '';
    } else {
        $checkPermiteNo = 'checked';
        $checkPermiteSi = '';
    }

    $btn_permitir = '<input type="radio" value="S" class="btnpublicarrestaurante" name="permite' . $row[$key] . '" version = "' . $version . '"  IDRestaurante="' . $row[$key] . '" ' . $checkPermiteSi . '>Si<br>';
    $btn_permitir .= '<input type="radio" value="N" class="btnpublicarrestaurante" name="permite' . $row[$key] . '"  version = "' . $version . '" IDRestaurante="' . $row[$key] . '" ' . $checkPermiteNo . '>No<br>';
    $btn_permitir .= "<div name='msgupdaterestaurante" . $row[$key] . "' id='msgupdaterestaurante" . $row[$key] . "'></div>";


    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Nombre" => $row["Nombre"],
            "Descripcion" => $row["Descripcion"],
            "Publicar" => $btn_permitir,
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
        );
    }

    $i++;
}

echo json_encode($responce);
