<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$IDUsuario = SIMUser::get("IDUsuario");
$columns = array();
$origen = SIMNet::req("origen");

$table = "IngresosSolicitud";
$tableJoin = "";
$key = "IDIngresosSolicitud";
$IDModulo = 145;

$script = "ingresossolicitud";


$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");

$where = " WHERE  IngresosSolicitud.IDClub = '" . SIMUser::get("club") . "'";


$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM IngresosSolicitud WHERE IDIngresosSolicitud = '" . $_POST["id"] . "' LIMIT 1";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Comentarios";
        $_GET['sord'] = "ASC";
        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'qryString':

                    $where .= " AND ( Comentarios LIKE '%" . $search_object->data . "%' )";
                    break;
            }
        } //end for
        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Comentarios LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "IDIngresosSolicitud";
// connect to the database

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

$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
//exit;
$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    // Validar si es un Usuario o un Socio
    if ($row["IDUsuario"] > 0) {
        $sqlUsuario = "SELECT Nombre FROM Usuario WHERE IDUsuario = " . $row['IDUsuario'];
        $queryUsuario = $dbo->query($sqlUsuario);
        $rowUser = $dbo->assoc($queryUsuario);
    } else {
        $sqlSocio = "SELECT CONCAT(Nombre,' ',Apellido) as Nombre FROM Socio WHERE IDSocio = " . $row['IDSocio'];
        $querySocio = $dbo->query($sqlSocio);
        $rowUser = $dbo->assoc($querySocio);
    }

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";

    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Nombre" => $rowUser['Nombre'],
            "Estado" => SIMResources::$EstadoIngresos[$row["IDEstado"]],
            "FechaSolicitud" => $row["FechaTrCr"],
            "Descargar PDF" => '<a class="blue descargar-pdf" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' data-url=' . INGRESOS_DIR . ' data-socio=' . $row['IDSocio'] . '><i class="ace-icon fa fa-download bigger-130"/></a>',
            "Descargar" => '<a class="blue descargar-zip" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' data-url=' . INGRESOS_DIR . ' data-socio=' . $row['IDSocio'] . '><i class="ace-icon fa fa-download bigger-130"/></a>',
            "Exportar" => '<a class="green exportar-luker" rel=' . $table . ' data-socio=' . $row['IDSocio'] . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#" ><i class="ace-icon fa fa-floppy-o bigger-130"/></a>',
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
        );
    else
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<div class="hidden-sm hidden-xs action-buttons"><a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a></div>',
            "Nombre" => $rowUser['Nombre'],
            "Estado" => SIMResources::$EstadoIngresos[$row["IDEstado"]],
            "FechaSolicitud" => $row["FechaTrCr"],
            "Descargar PDF" => '<a class="blue descargar-pdf" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' data-url=' . INGRESOS_DIR . ' data-socio=' . $row['IDSocio'] . '><i class="ace-icon fa fa-download bigger-130"/></a>',
            "Descargar" => '<a class="blue descargar-zip" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' data-url=' . INGRESOS_DIR . ' data-socio=' . $row['IDSocio'] . '><i class="ace-icon fa fa-download bigger-130"/></a>',
            "Exportar" => '<a class="green exportar-luker" rel=' . $table . ' data-socio=' . $row['IDSocio'] . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-floppy-o bigger-130"/></a>',
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
        );

    $i++;
}
echo json_encode($responce);
