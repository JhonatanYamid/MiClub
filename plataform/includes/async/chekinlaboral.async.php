<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );


$columns = array();
$origen = SIMNet::req("origen");

$table = " CheckinLaboral C";
$key = "IDCheckinLaboral";
$where = " WHERE  C.IDClub = '" . SIMUser::get("club") . "' AND C.Estado = 1";
$script = "chekinlaboral";

$Usuario = $dbo->fetchAll('Usuario', 'IDUsuario = ' . SIMUser::get('IDUsuario'), 'array');
if ($Usuario['IDPerfil'] != 1 && $Usuario['IDPerfil'] != 0 && $Usuario['IDPerfil'] != 62) {
    $where .= " AND (S.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " OR S.DocumentoJefe2 = " .  $Usuario['NumeroDocumento'] . " OR U.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " OR U.DocumentoJefe2 = " . $Usuario['NumeroDocumento'] . " ) ";
}

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM CheckinLaboral WHERE IDCheckinLaboral = '" . $_POST["id"] . "' LIMIT 1";
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

                case 'Nombre':
                    $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%')  ";
                    break;

                case 'NumeroDocumento':
                    $where .= " AND (  S.NumeroDocumento LIKE '%" . $search_object->data . "%')";
                    break;

                case 'qryString':
                    $where .= " AND ( FechaTrCr LIKE '%" . $search_object->data . "%' )";
                    break;
                default:
                    $where .=  $array_buqueda->groupOp . " (U." . $search_object->field . " LIKE '%" . $search_object->data . "%' OR S." . $search_object->field . " LIKE '%" . $search_object->data . "%') ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        $datos = explode(" ", $qryString);
        // buscador 
        if (count($datos) == 1) {
            $where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' OR U.Nombre LIKE '%" . $qryString . "%'  OR S.Apellido LIKE '%" .  $qryString . "%' OR S.NumeroDocumento LIKE '%" . $qryString . "%' OR U.NumeroDocumento LIKE '%" . $qryString . "%')  ";
        } else 
		if (count($datos) == 2) {
            $where .= " AND ( S.Nombre LIKE '%" . $datos[0] . "%' AND S.Apellido LIKE '%" . $datos[1] . "%')";
        }
        /*   if (!empty($qryString)) {

            $where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' OR U.Nombre LIKE '%" . $qryString . "%'  OR S.Apellido LIKE '%" .  $qryString . "%' OR S.NumeroDocumento LIKE '%" . $qryString . "%' OR U.NumeroDocumento LIKE '%" . $qryString . "%')  ";
        } //end if */
        break;
    case "searchDate":
        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];
        if (!empty($FechaInicio) || !empty($FechaFin)) {
            $where .= " AND FechaMovimientoEntrada BETWEEN '" . $FechaInicio . " 00:00:00' AND '" . $FechaFin . " 23:59:59'";
        } //end if
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "FechaTrEd";
// connect to the database

// echo "SELECT IF(C.IDSocio>0,C.IDSocio,C.IDUsuario) as UserLaboral,COUNT(*) AS count FROM " . $table . $where . "  GROUP BY UserLaboral  ";
$result = $dbo->query("SELECT IF(C.IDSocio>0,C.IDSocio,C.IDUsuario) as UserLaboral,COUNT(*) AS count FROM " . $table . $where . " GROUP BY UserLaboral");
$rows = $dbo->rows($result);
$count = $rows;

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
$sql = "SELECT  
IF(C.IDSocio>0,C.IDSocio,C.IDUsuario) as UserLaboral, 
IF(C.IDSocio>0,'Socio','Usuario') as TipoUsuario,
IF(C.IDSocio>0,CONCAT(S.Nombre,' ',S.Apellido),U.Nombre) as Nombre,
IF(C.IDSocio>0,S.NumeroDocumento,U.NumeroDocumento) as NumeroDocumento,C.*
FROM CheckinLaboral as C LEFT JOIN Socio as S ON C.IDSocio=S.IDSocio LEFT JOIN Usuario as U ON C.IDUsuario=U.IDUsuario " . $where . " GROUP BY UserLaboral ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

/* echo $sql;
exit; */
$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row['UserLaboral'];
    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row['UserLaboral'],
            "TipoUsuario" => $row["TipoUsuario"],
            "Nombre" => $row["Nombre"],
            "NumeroDocumento" => $row["NumeroDocumento"],
            "Detalle" => '<a class="green" href="' . $script . '.php?action=detalle&id=' . $row['UserLaboral'] . '&type=' . $row['TipoUsuario'] . '"><i class="ace-icon fa fa-list bigger-130"/></a>',
            // "Latitud" => $row["Latitud"],
            // "Longitud" => $row["Longitud"],
            // "Entrada" => $row["Entrada"],
            // "Salida" => $row["Salida"],
            // "Estado" => SIMResources::$estado_laboral[$row["Estado"]],
            // "FechaMovimiento" => $row["FechaMovimiento"],
            // "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'

        );

    $i++;
}

echo json_encode($responce);
