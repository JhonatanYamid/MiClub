<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "Correspondencia";
$key = "IDCorrespondencia";
$where = " WHERE Correspondencia.IDClub = '" . SIMUser::get("club") . "' ";
$script = "correspondencia";
$tablas = "";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'qryString':

                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Socio':
                    $tablas .= ", Socio S";
                    $where .= " AND S.IDSocio = Correspondencia.IDSocio";
                    $where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'CreadoPor':
                    $tablas .= ", Usuario U";
                    $where .= " AND U.IDUsuario = Correspondencia.IDUsuario";
                    $where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'EntregadoPor':
                    $tablas .= ", Usuario U";
                    $where .= " AND U.IDUsuario = Correspondencia.IDUsuario";
                    $where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' )";
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
            $tablas .= ", Socio S";
            $where .= " AND S.IDSocio = Correspondencia.IDSocio";
            $where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' OR S.Apellido LIKE '%" . $qryString . "%' OR S.Accion = '" . $qryString . "' OR S.AccionPadre = '" . $qryString . "' OR S.NumeroDocumento = '" . $qryString . "' )  ";
        } //end if
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "IDCorrespondencia ";
}
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
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$sql = "SELECT Correspondencia.* FROM Correspondencia" . $tablas . " " . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce = "";

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Socio" => utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row["IDSocio"] . "'")),
            "Tipo" => $dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $row["IDTipoCorrespondencia"] . "'"),
            "CreadoPor" => $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["IDUsuarioCrea"] . "'"),
            "EntregadoPor" => $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["IDUsuarioEntrega"] . "'"),
            "Estado" => $dbo->getFields("CorrespondenciaEstado", "Nombre", "IDCorrespondenciaEstado = '" . $row["IDCorrespondenciaEstado"] . "'"),
            "FechaRecepcion" => $row["FechaRecepcion"],
            "EntregadoA" => $row["EntregadoA"],
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
        );
    }

    $i++;
}

echo json_encode($responce);
