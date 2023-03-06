<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$columns = array();
$origen = SIMNet::req("origen");
$IDClub = SIMUser::get("club");

$table = "CheckinFuncionarios";
$key = "IDCheckinFuncionarios";
$where = " WHERE c.IDClub = $IDClub ";
$script = "checkinfuncionarios";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM CheckinFuncionarios WHERE IDCheckinFuncionarios = '" . $_POST["id"] . "' LIMIT 1";
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

                case 'qryString':
                    $where .= " AND ( FechaTrCr LIKE '%" . $search_object->data . "%' )";
                    break;

                case 'Area':
                    $sql_area = "SELECT IDAreaUsuario FROM AreaUsuario WHERE LOWER(Nombre) LIKE LOWER ('%" . $search_object->data . "%') AND IDClub = " . SIMUser::get("club");
                    $result_area = $dbo->query($sql_area);
                    while ($row_area = $dbo->fetchArray($result_area)) :
                        $array_id_area[] = $row_area["IDAreaUsuario"];
                    endwhile;
                    if (count($array_id_area) > 0) :
                        $id_area_buscar = implode(",", $array_id_area);

                    endif;

                    $where .= " AND u.IDAreaUsuario in (" . $id_area_buscar . ")";
                    break;

                case 'Cargo':
                    $where .= " AND ( u.Cargo LIKE '%" . $search_object->data . "%' )";
                    break;

                default:
                    $where .=  $array_buqueda->groupOp . "  LOWER(" . $tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");

        if (!empty($qryString))
            // $where .= " AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR NumeroDocumento LIKE ('%$qryString%')) ";

            //area
            $sql_area = "SELECT IDAreaUsuario FROM AreaUsuario WHERE LOWER(Nombre) LIKE   LOWER ('%" . $qryString . "%') AND IDClub = " . SIMUser::get("club");
        $result_area = $dbo->query($sql_area);
        while ($row_area = $dbo->fetchArray($result_area)) :
            $array_id_area[] = $row_area["IDAreaUsuario"];
        endwhile;
        if (count($array_id_area) > 0) :
            $id_area_buscar = implode(",", $array_id_area);
            $condicionarea = " AND u.IDAreaUsuario in (" . $id_area_buscar . ")";
        endif;

        if (!empty($condicionarea)) {
            $where .= $condicionarea;
        } else {
            $where .= " AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR NumeroDocumento LIKE ('%$qryString%') OR Cargo LIKE ('%$qryString%')) ";
        }



        break;

    case "searchDate":
        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];

        if (!empty($FechaInicio) || !empty($FechaFin)) {
            $where .= " AND DATE(FechaEntrada) BETWEEN DATE('$FechaInicio') AND DATE('$FechaFin') ";
        }

        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "FechaTrEd";
// connect to the database

$result = $dbo->query("SELECT COUNT(c.IDCheckinFuncionarios) AS count FROM CheckinFuncionarios as c, Usuario as u " . $where . "  AND u.IDUsuario = c.IDUsuario");
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
$sql = "SELECT IDCheckinFuncionarios, c.IDUsuario, NumeroDocumento, Nombre,IDAreaUsuario,Cargo
        FROM CheckinFuncionarios as c, Usuario as u
        " . $where . " AND u.IDUsuario = c.IDUsuario
        GROUP BY c.IDUsuario ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;





$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {

    //Area
    $AreaUsuario = $dbo->getFields("AreaUsuario", "Nombre", "IDAreaUsuario= '" . $row["IDAreaUsuario"] . "'");

    $responce->rows[$i]['id'] = $row['IDUsuario'];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row['IDUsuario'],
            "Nombre" => $row["Nombre"],
            "NumeroDocumento" => $row["NumeroDocumento"],
            "Area" => $AreaUsuario,
            "Cargo" => $row["Cargo"],
            "Detalle" => '<a class="green" href="' . $script . '.php?action=detalle&id=' . $row['IDUsuario'] . '"><i class="ace-icon fa fa-list bigger-130"/></a>',
        );

    $i++;
}

echo json_encode($responce);
