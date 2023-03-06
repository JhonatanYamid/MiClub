<?php
include("../../procedures/general_async.php");
if ($_GET['idtalonera'] != '') {
    $idTalonera = $_GET['idtalonera'];
    $sql_talonera = "SELECT * FROM Talonera WHERE IDClub = '" . SIMUser::get("club") . "'" . " AND IDServicio = '$idTalonera' AND Activa = 1 ";
    $result_talonera = $dbo->query($sql_talonera);
    while ($row_talonera = $dbo->fetchArray($result_talonera)) :


        $idtalonera = $row_talonera["IDTalonera"];
        $nombre_talonera = $row_talonera["NombreTalonera"];
        echo '<option value="' . $idtalonera . '">' . $nombre_talonera . '</option>';
    endwhile;
}
if ($_POST['idSocio'] != '') {
    // echo "ingreso al post ID SOCIO <br>";
    $idClub = SIMUser::get("club");
    $idsocio = $_POST['idSocio'];
    $accion = $dbo->getFields("Socio", "Accion", "IDSocio='" . $idsocio . "'");
    //echo " la accion es:" . $accion;
    $sql_talonera = "SELECT * FROM Socio WHERE IDClub = '" . $idClub . "' AND AccionPadre = '" . $accion . "'";
    $result_talonera = $dbo->query($sql_talonera);
    // while ($row_talonera = $dbo->fetchArray($result_talonera)) :
    //echo $sql_talonera;
    /*  $idtalonera = $row_talonera["IDSocio"];
        $nombre_talonera = $row_talonera["Nombre"]; */

    while ($rowData = $dbo->fetchArray($result_talonera)) {
        $data[] = array(
            'idsocio' => $rowData['IDSocio'],
            'nombre' => $rowData['Nombre'],
            'apellido' => $rowData['Apellido'],

        );
    }
    //print_r($data);

    // endwhile;
    echo json_encode($data);
}



SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "SocioTalonera";
$key = "IDSocioTalonera";
$club=SIMUser::get("club");

if($club == 18):
$where = " WHERE  ValorPagado<>0 and " . $table . ".IDClub = '" . SIMUser::get("club") . "'";
else:
$where = " WHERE  " . $table . ".IDClub = '" . SIMUser::get("club") . "'";
endif;
 
$script = "reportetalonera";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM SocioTalonera WHERE IDSocioTalonera = '" . $_POST["id"] . "' LIMIT 1";
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

                case 'Socio':
                    $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Servicio':

                    $where .= " AND (  SE.Nombre LIKE '%" . $search_object->data . "%' or SM.Nombre LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'qryString':
                    $where .= " AND ( FechaTrCr LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'NombreTalonera':
                    $where .= " AND ( NombreTalonera LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'Dirigida':
                    $where .= " AND ( Dirigida  LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'ValorPagado':
                    $where .= " AND ( ValorPagado  LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'CantidadTotal':
                    $where .= " AND ( CantidadTotal  LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'CantidadPendiente':
                    $where .= " AND ( CantidadPendiente  LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'FechaCompra':
                    $where .= " AND ( FechaCompra  LIKE '%" . $search_object->data . "%' )";

                    break;
                case 'FechaVencimiento':
                    $where .= " AND ( FechaVencimiento  LIKE '%" . $search_object->data . "%' )";

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

            //$where .= " AND ( SE.Nombre LIKE '%" . $qryString . "%' OR SM.Nombre LIKE '%" . $qryString . "%' OR S.Nombre LIKE '%" . $qryString . "%' OR S.Apellido LIKE '%" . $qryString . "%' OR NombreTalonera LIKE '%" . $qryString . "%' OR Dirigida LIKE '%" . $qryString . "%' OR ValorPagado LIKE '%" . $qryString . "%' OR CantidadTotal LIKE '%" . $qryString . "%' OR CantidadPendiente LIKE '%" . $qryString . "%' OR FechaCompra LIKE '%" . $qryString . "%' OR FechaVencimiento LIKE '%" . $qryString . "%' )";
            $where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' OR S.Apellido LIKE '%" . $qryString . "%')";
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



$sql = "SELECT $table.* FROM  $table,Socio S  $where" . "AND S.IDSocio=" . $table . ".IDSocio" . " ORDER BY $sidx $sord LIMIT $start, $limit";
//$sql = "SELECT $table.* FROM  $table  $where  ORDER BY $sidx $sord LIMIT $start, $limit";
// $sql = "SELECT " . $table . ".* , T.*,SocioTalonera.IDServicio as IDServicioSocioTalonera  FROM " . $table . ", Talonera T , Socio S, Servicio SE, ServicioMaestro SM " . $where . " and T.IDTalonera = " . $table . ".IDTalonera " . " and S.IDSocio = " . $table . ".IDSocio" . " and SE.IDServicio = T.IDServicio " . " and SE.IDServicioMaestro = SM.IDServicioMaestro " . " ORDER BY SocioTalonera.$sidx $sord LIMIT " . $start . "," . $limit;

//var_dump($sql);
/* echo $sql;
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

    if ($row["Activo"] == '1') {
        $checkPermiteSi = 'checked';
        $checkPermiteNo = '';
    } else {
        $checkPermiteNo = 'checked';
        $checkPermiteSi = '';
    }

    $btn_permitir = '<input type="radio" value="1" class="btnActivarTalonera" name="permite' . $row[$key] . '"  idtalonera="' . $row["IDTalonera"] . '" idsociotalonera="' . $row["IDSocioTalonera"] . '"' . $checkPermiteSi . '>Si<br>';
    $btn_permitir .= '<input type="radio" value="0" class="btnActivarTalonera" name="permite' . $row[$key] . '"   idtalonera="' . $row["IDTalonera"] . '" idsociotalonera="' . $row["IDSocioTalonera"] . '"' . $checkPermiteNo . '>No<br>';
    $btn_permitir .= "<div name='msgupdateTalonera" . $row["IDTalonera"] . "' id='msgupdateTalonera" . $row["IDTalonera"] . "'></div>";


    if ($row[IDServicio] > 0) :
        //nombre del servicio
        $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '$row[IDServicio]'");
        $sql_servicio = "SELECT TituloServicio FROM ServicioClub WHERE IDClub='" . SIMUser::get("club") . "'" . " AND IDServicioMaestro='" . $IDServicioMaestro . "'" . " AND Activo='S'";
        $servicio = $dbo->query($sql_servicio);
        $frm = $dbo->fetchArray($servicio);
        $TituloServicio = $frm["TituloServicio"];

        if (!empty($TituloServicio)) {
            $nombreServicio = $TituloServicio;
        } else {
            $nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDServicioMaestro . "'");
        }
    else :
        if ($row[TodosLosServicios] == 1) :
            $nombreServicio = "Todos los Servicio";
        endif;
    endif;

    $row["NombreTalonera"] = $dbo->getFields("Talonera", "NombreTalonera", "IDTalonera = $row[IDTalonera]");


    if ($row["TipoMonedero"] == '1') {
        $tipomonedero = "Si";
    } else if ($row["TipoMonedero"] == '0') {
        $tipomonedero = "No";
    }



    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(

            $key => $row[$key],


            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Socio" => $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row["IDSocio"] . "'"),
            "Servicio" => $nombreServicio,
            "NombreTalonera" => $row["NombreTalonera"],
            "Dirigida" => $row["Dirigida"],
            "ValorPagado" => $row["ValorPagado"],
            "CantidadTotal" => $row["CantidadTotal"],
            "CantidadPendiente" => $row["CantidadPendiente"],
            "FechaCompra" => $row["FechaCompra"],
            "FechaVencimiento" => $row["FechaVencimiento"],
            "TipoMonedero" => $tipomonedero,
            "SaldoMonedero" => $row["SaldoMonedero"],
            "Activo" => $btn_permitir,


            //"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'

        );

    $i++;
}

echo json_encode($responce);
