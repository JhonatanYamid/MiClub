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
$table = "Domicilio" . $version;
$key = "IDDomicilio";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'" . $condicion_restaurante . " AND " . $table . ".IDUsuario <> '' ";
$script = "domiciliosusuarios" . $version;

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Domicilio" . $version . " WHERE IDDomicilio = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Numero";
        $_GET['sord'] = "ASC";

        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {

                case 'FechaInicio':
                    $fechainicio = $search_object->data;
                    if ($fechainicio != "") {
                        $where .= " AND FechaTrCr >= '$fechainicio" . " 00:00:00'";
                    }
                    break;

                case 'FechaFin':
                    $fechafin = $search_object->data;
                    if ($fechafin != "") {
                        $where .= " AND FechaTrCr <= '$fechafin" . " 23:59:59'";
                    }
                    break;

                case 'Estado':
                    $sql_est = "SELECT IDEstadoDomicilio FROM EstadoDomicilio WHERE Nombre LIKE '%" . $search_object->data . "%' AND IDClub = " . SIMUser::get("club");
                    $result_est = $dbo->query($sql_est);
                    while ($row_est = $dbo->fetchArray($result_est)) :
                        $array_id_est[] = $row_est["IDEstadoDomicilio"];
                    endwhile;
                    if (count($array_id_est) > 0) :
                        $id_est_buscar = implode(",", $array_id_est);
                    else :
                        $id_est_buscar = 1;
                    endif;

                    $where .= " AND IDEstadoDomicilio in (" . $id_est_buscar . ")";
                    break;

                case 'Restaurante':
                    $restaurantes = explode(",", $search_object->data);
                    if (!empty($restaurantes)) {
                        $count = count($restaurantes);
                        $i = 1;
                        $sql_res = "SELECT IDRestauranteDomicilio FROM RestauranteDomicilio$version WHERE ";

                        foreach ($restaurantes as $restaurante) {
                            $sql_res .= "Nombre LIKE '%" . $restaurante . "%' ";
                            if ($i < $count) {
                                $sql_res .= "OR ";
                            }
                            $i++;
                        }
                        $result_res = $dbo->query($sql_res);
                        while ($row_res = $dbo->fetchArray($result_res)) :
                            $array_id_res[] = $row_res["IDRestauranteDomicilio"];
                        endwhile;
                        if (count($array_id_res) > 0) :
                            $id_res_buscar = implode(",", $array_id_res);
                        else :
                            $id_res_buscar = 0;
                        endif;

                        $where .= " AND IDRestauranteDomicilio in (" . $id_res_buscar . ")";
                    }
                    break;


                case 'FormaDePago':
                    $pagos = explode(",", $search_object->data);

                    $count = count($pagos);
                    $i = 1;
                    $sql_pago = "SELECT IDTipoPago FROM TipoPago WHERE ";

                    foreach ($pagos as $pago) {
                        $sql_pago .= "Nombre LIKE '%" .  $pago . "%' ";
                        if ($i < $count) {
                            $sql_pago .= "OR ";
                        }
                        $i++;
                    }
                    $result_pago = $dbo->query($sql_pago);
                    while ($row_res = $dbo->fetchArray($result_pago)) :
                        $array_id_pago[] = $row_res["IDTipoPago"];
                    endwhile;
                    if (count($array_id_pago) > 0) :
                        $id_pago_buscar = implode(",", $array_id_pago);
                    else :
                        $id_pago_buscar = 0;
                    endif;

                    $where .= " AND FormaPago LIKE '%" . $search_object->data . "%'";

                    break;


                case 'Funcionario':
                    //busco el area
                    $sql_usu = "SELECT IDUsuario FROM Usuario WHERE Nombre LIKE '%" . $search_object->data . "%'";
                    $result_usu = $dbo->query($sql_usu);
                    while ($row_usu = $dbo->fetchArray($result_usu)) :
                        $array_id_usu[] = $row_usu["IDUsuario"];
                    endwhile;
                    if (count($array_id_usu) > 0) :
                        $id_usu_buscar = implode(",", $array_id_usu);
                    else :
                        $id_usu_buscar = 0;
                    endif;

                    $where .= " AND   IDUsuario in (" . $id_usu_buscar . ")";

                    break;

                    // case 'qryString':

                    //     $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' or NumeroPedido LIKE '%" . $qryString . "%' )";
                    //     break;

                default:
                    $where .= $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Numero = '" . $qryString . "' )  ";
        } //end if
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "Numero";
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

$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;

$i = 0;
$hoy = date('Y-m-d');

$sql_usuarios = "SELECT IDUsuario,Nombre FROM Usuario WHERE IDClub = '" . SIMUser::get("club") . "'";
/* echo $sql_usuarios;
exit; */
$r_usuarios = $dbo->query($sql_usuarios);
while ($row_usuarios = $dbo->fetchArray($r_usuarios)) {
    $array_usuarios[$row_usuarios["IDUsuario"]] = $row_usuarios;
}

$sql_rest = "SELECT IDRestauranteDomicilio,Nombre FROM RestauranteDomicilio$version WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_rest = $dbo->query($sql_rest);
while ($row_rest = $dbo->fetchArray($r_rest)) {
    $array_rest[$row_rest["IDRestauranteDomicilio"]] = $row_rest["Nombre"];
}

while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row[$key];

    if ($row["IDEstadoDomicilio"] == 3) {
        $color_linea = "#F43125";
    } else {
        if (date("Y-m-d") == substr($row["FechaTrCr"], 0, 10)) {
            $color_linea = "#005610";
        } else {
            $color_linea = "#000";
        }
    }
    $btn_incluir = "";
    $sqlEstados = "SELECT * FROM EstadoDomicilio WHERE IDClub = " . SIMUser::get("club") . " OR IDEstadoDomicilio = 1";
    $qryEstado = $dbo->query($sqlEstados);

    while ($Estados = $dbo->fetchArray($qryEstado)) {

        if ($Estados["IDEstadoDomicilio"] == $row["IDEstadoDomicilio"]) {
            $checkEstado = 'checked';
        } else {
            $checkEstado = '';
        }

        $btn_incluir .= '<input type="radio" value="' . $Estados["IDEstadoDomicilio"] . '" class="btnestadodomicilio" name = "estado' . $row[$key] . '" version = "' . $version . '" IDDomicilio="' . $row[$key] . '" ' . $checkEstado . '>' . $Estados['Nombre'] . '<br>';
    }

    $btn_incluir .= "<div name='msgupdatedomi" . $row[$key] . "' id='msgupdatedomi" . $row[$key] . "'></div>";
    $btn_permitir = '<input type="radio" value="S" class="btnnotificadomicilio" name="permite' . $row[$key] . '" version = "' . $version . '"  IDSocio="' . $array_usuarios[$row["IDSocio"]]["IDSocio"] . '" ' . $checkPermiteSi . '>Notificar<br>';
    $btn_permitir .= "<div name='msgupdatenotifica" . $row[$key] . "' id='msgupdatenotifica" . $row[$key] . "'></div>";

    //Para el Rancho todos pueden cancelar reserva
    if (SIMUser::get("IDPerfil") == 0) :
    //$btn_eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
    //$btn_eliminar = '';
    else :
        $btn_eliminar = '';
    endif;


    $Pago = $row["FormaPago"];
    if ($row["IDTipoPago"] > 0) {
        $TipoPago = "-" . $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $row["IDTipoPago"] . "'");
    } else $TipoPago = "";


    $datos_pagoCredibanco = $dbo->fetchAll("PagoCredibanco", " reserved12 = '" . $row["IDDomicilio"] . "' ", "array");
    if ($datos_pagoCredibanco["Modulo"] == "Domicilio" && ($row[IDClub] == 8 || $row[IDClub] == 16)) {

        $TipoCrediBanco = $datos_pagoCredibanco["xmlResponse"];
        $data = json_decode($TipoCrediBanco, true);
        $dataPagoCredibanco = "-" . $data["paymentWay"];
    } else $dataPagoCredibanco = "";

    $FormaDePago = $Pago .  $TipoPago  . $dataPagoCredibanco;

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Restaurante" => "<font color='" . $color_linea . "'>" . $array_rest[$row["IDRestauranteDomicilio"]] . "</font>",
            "Numero" => "<font color='" . $color_linea . "'>" . $row["Numero"] . "</font>",
            "Funcionario" => "<font color='" . $color_linea . "'>" . $array_usuarios[$row["IDUsuario"]]["Nombre"]  . "</font>",
            "Comentario" => "<font color='" . $color_linea . "'>" . utf8_encode($row["ComentariosSocio"]) . "</font>",
            "NumeroMesa" => "<font color='" . $color_linea . "'>" . $row["NumeroMesa"] . "</font>",
            "Total" => "<font color='" . $color_linea . "'>" . $row["Total"] . "</font>",
            "Fecha" => "<font color='" . $color_linea . "'>" . $row["FechaTrCr"] . "</font>",
            "FormaDePago" => "<font color='" . $color_linea . "'>" .  $FormaDePago . "</font>",
            "Estado" =>  $btn_incluir,
            "Notificar" => $btn_permitir,
            "Eliminar" => $btn_eliminar,
        );
    }

    $i++;
}

echo json_encode($responce);
