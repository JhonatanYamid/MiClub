<?php

include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Socio";
$key = "IDSocio";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' AND AccionPadre = '' AND TipoSocio = 'TITULAR' AND IDEstadoSocio NOT IN (2) ";
$script = "cuotassociales";


$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Socio WHERE IDSocio = '" . $_POST["id"] . "' AND IDClub = '" . SIMUser::get("club") . "'  LIMIT 1";
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
                    $where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '" . $search_object->data . "%' OR Accion LIKE '" . $search_object->data . "%' OR Email LIKE '" . $search_object->data . "%' OR Predio LIKE '" . $search_object->data . "%' )  ";
                    break;

                case "Estado":

                    //busco el estado
                    $sql_estado_pqr = "Select * From EstadoSocio Where Nombre = '" . $search_object->data . "'";
                    $result_estado_pqr = $dbo->query($sql_estado_pqr);
                    while ($row_estado_pqr = $dbo->fetchArray($result_estado_pqr)) :
                        $array_id_estado[] = $row_estado_pqr["IDEstadoSocio"];
                    endwhile;
                    if (count($array_id_estado) > 0) :
                        $id_estado_buscar = implode(",", $array_id_estado);
                    else :
                        $id_estado_buscar = 0;
                    endif;

                    $where .= " AND   IDEstadoSocio in (" . $id_estado_buscar . ")";

                    break;

                default:
                    $where .=  $array_buqueda->groupOp . "  Socio." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Socio.Nombre LIKE '%" . $qryString . "%' OR Socio.Apellido LIKE '%" . $qryString . "%' OR Socio.NumeroDocumento LIKE '" . $qryString . "%' OR Accion LIKE '" . $qryString . "%' OR Email LIKE '" . $qryString . "%' OR Predio LIKE '" . $qryString . "%')  ";
        } //end if
        break;
    case "searchurlaccion":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND (  Accion LIKE '" . $qryString . "%' )   ";
        } //end if
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
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


//$limit = 58;
$sql = "SELECT IDSocio, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) as Socio,IDEstadoSocio,PermiteReservar,Accion,Nombre,Apellido,TipoSocio FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

$result = $dbo->query($sql);

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i = 0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');

$sql_estado = "SELECT Nombre,IDEstadoSocio From EstadoSocio WHERE 1";
$r_estado_socio = $dbo->query($sql_estado);
while ($row_estado = $dbo->fetchArray($r_estado_socio)) {
    $array_estado[$row_estado["IDEstadoSocio"]] = $row_estado["Nombre"];
}



while ($row = $dbo->assoc($result)) {
    $sqlEstados = "SELECT * FROM EstadoSocio";
    $qryEstado = $dbo->query($sqlEstados);

    while ($Estados = $dbo->fetchArray($qryEstado)) {
        if ($Estados["IDEstadoSocio"] == $row["IDEstadoSocio"])
            $checkEstado = 'checked';
        else
            $checkEstado = '';

        $btn_incluir .= '<input type="radio" value="' . $Estados['IDEstadoSocio'] . '" class="btncambiosocio" name="estado' . $row[$key] . '"  campo = "Estado" idsocio="' . $row[$key] . '" ' . $checkEstado . '>' . $Estados['Nombre'] . '<br>';
    }

    $btn_incluir .= "<div name='msgupdate" . $row[$key] . "' id='msgupdate" . $row[$key] . "'></div>";

    if ($row["PermiteReservar"] == 'S') {
        $checkPermiteSi = 'checked';
        $checkPermiteNo = '';
    } else {
        $checkPermiteNo = 'checked';
        $checkPermiteSi = '';
    }

    $btn_permitir = '<input type="radio" value="S" class="btncambiosocio" name="permite' . $row[$key] . '"  campo = "PermiteReserva" idsocio="' . $row[$key] . '" ' . $checkPermiteSi . '>Si<br>';
    $btn_permitir .= '<input type="radio" value="N" class="btncambiosocio" name="permite' . $row[$key] . '"  campo = "PermiteReserva" idsocio="' . $row[$key] . '" ' . $checkPermiteNo . '>No<br>';
    $btn_permitir .= "<div name='msgupdateReserva" . $row[$key] . "' id='msgupdateReserva" . $row[$key] . "'></div>";


    $responce->rows[$i]['id'] = $row[$key];

    if ((int)SIMUser::get("IDPerfil") <= 1) :
        $btn_eliminar_3 = "<a class='red eliminar_registro' rel=" . $table . " id=" . $row[$key] . " lang = " . $script . " href='#'><i class='ace-icon fa fa-trash-o bigger-130'/></a>";
    else :
        $btn_eliminar_3 = '';
    endif;

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Accion" => $row["Accion"],
            "Nombre" => ($row["Nombre"]),
            "Apellido" => ($row["Apellido"] != '') ? $row["Apellido"] : '',
            "TipoSocio" => $row["TipoSocio"],
            // "PermiteReservar" => $btn_permitir,
            "Detallar" => '<a class="green" href="' . $script . '.php?action=detallar&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-list bigger-130"/></a>',
        );

    $i++;
}
// echo '<pre>';
// var_dump(json_encode($responce));
// die();
echo json_encode($responce);