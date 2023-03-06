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
$table = "Producto" . $version;
$key = "IDProducto";
$where = " WHERE P.IDClub = '" . SIMUser::get("club") . "' ";
$whereCou = " WHERE P.IDClub = '" . SIMUser::get("club") . "' ";
$script = "productos" . $version;

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
                    $where .= " AND ( P.Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Categoria':

                    $sqlCat = "SELECT IDCategoriaProducto FROM CategoriaProducto$version WHERE IDClub = ".SIMUser::get("club")." AND Nombre LIKE '%$search_object->data%'";
                    $qryCat = $dbo->query($sqlCat);

                    while($row = $dbo->fetchArray($qryCat)):
						$arrayCategorias[]=$row["IDCategoriaProducto"];
					endwhile;

					if(count($arrayCategorias)>0):
						$idcatBuscar = implode(",",$arrayCategorias);
					else:
						$idcatBuscar = 0;
					endif;

					$sqlProdc = "SELECT IDProducto FROM ProductoCategoria$version WHERE IDCategoriaProducto IN ($idcatBuscar)";
                    $qryProdc = $dbo->query($sqlProdc);

                    while($row = $dbo->fetchArray($qryProdc)):
						$arrayProductos[]=$row["IDProducto"];
					endwhile;

					if(count($arrayProductos)>0):
						$idprodBuscar = implode(",",$arrayProductos);
					else:
						$idprodBuscar = 0;
					endif;

                    $where .= " AND ( P.IDProducto IN ($idprodBuscar))";
                break;

                case 'Existencias':
                    $where .= " AND ( P.Existencias = " . $search_object->data . " )";
                    break;

                case 'Descripcion':
                    $where .= " AND ( P.Descripcion LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Nombre':
                    $where .= " AND ( P.Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                default:
                    $where .= " AND ( CP.Nombre LIKE '%" . $search_object->data . "%' )";
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
    $sidx = "P.Nombre";
}

// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table ."P ". $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . " P " . $whereCou . "    ");
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

// $sql = "SELECT * FROM " . $table . " P" . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

$sql = "SELECT P.IDProducto, P.Publicar, P.Nombre, P.Descripcion, P.Existencias 
                FROM Producto$version P $where  ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = (int) $page;
$responce->total = (int) $total_pages;
$responce->records = (int) $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

     $Categoria = "";
    $sql_cat = "SELECT CP.Nombre
    From ProductoCategoria$version PC,CategoriaProducto$version CP
    WHERE PC.IDCategoriaProducto = CP.IDCategoriaProducto and PC.IDProducto = '" . $row[$key] . "' ";
    $r_cat = $dbo->query($sql_cat);
    while ($row_cat = $dbo->fetchArray($r_cat)) {
    $Categoria .= $row_cat["Nombre"];
    }

    if ($row["Publicar"] == 'S') {
        $checkPermiteSi = 'checked';
        $checkPermiteNo = '';
    } else {
        $checkPermiteNo = 'checked';
        $checkPermiteSi = '';
    }

    $btn_permitir = '<input type="radio" value="S" class="btnpublicarproducto" name="permite' . $row[$key] . '" version = "'.$version.'"  idproducto="' . $row[$key] . '" ' . $checkPermiteSi . '>Si<br>';
    $btn_permitir .= '<input type="radio" value="N" class="btnpublicarproducto" name="permite' . $row[$key] . '"  version = "'.$version.'" idproducto="' . $row[$key] . '" ' . $checkPermiteNo . '>No<br>';
    $btn_permitir .= "<div name='msgupdateProducto" . $row[$key] . "' id='msgupdateProducto" . $row[$key] . "'></div>";

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Nombre" => $row["Nombre"],            
            "Categoria" => $Categoria,
            "Descripcion" => $row["Descripcion"],
            "Existencias" => $row["Existencias"],
            "Publicar" => $btn_permitir,
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
        );
    }

    $i++;
}

echo json_encode($responce);
