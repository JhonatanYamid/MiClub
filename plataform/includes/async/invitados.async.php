<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();

$origen = SIMNet::req("origen");

$table = "SocioInvitado";
$key = "IDSocioInvitado";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  ";
$script = "invitados";

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
                case 'Socio':

                    $where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '%" . $search_object->data . "%' )  ";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . "  SocioInvitado." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $accion = $_GET["Accion"];
        if (!empty($accion)) {
            $where .= " AND ( Socio.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
        }

        break;
    case "searchDate":
        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];
        if (!empty($FechaInicio) || !empty($FechaFin)) {
            $where .= " AND FechaIngreso BETWEEN '" . $FechaInicio . " 00:00:00' AND '" . $FechaFin . " 23:59:59'";
        } //end if
        break;

    default:
        $where .= " AND FechaIngreso = CURDATE()  ";
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "Titular";
}

// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . ",Socio " . $where . " AND Socio.IDClub = '" . SIMUser::get("club") . "' AND Socio.IDSocio = SocioInvitado.IDSocio   ");
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

//$sql = "SELECT " . $table . ".*, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Socio.IDClub = '" . SIMUser::get("club")  . "' AND Socio.IDSocio = SocioInvitado.IDSocio ORDER BY $key $sord " . $str_limit;
//$sql = "SELECT " . $table . ".*, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio,Accion FROM " . $table . " , Socio " . $where . " AND Socio.IDClub = '" . SIMUser::get("club")  . "' AND Socio.IDSocio = SocioInvitado.IDSocio and Estado = 'P' ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".*, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio,Accion FROM " . $table . " , Socio " . $where . " AND Socio.IDClub = '" . SIMUser::get("club") . "' AND Socio.IDSocio = SocioInvitado.IDSocio and Estado = 'P' ORDER BY $key $sord LIMIT " . $start . "," . $limit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = (int) $page;
$responce->total = (int) $total_pages;
$responce->records = (int) $contador_total;

$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {

    $datos_regla = SIMUtil::consulta_regla_invitacion($row[IDSocio], SIMUser::get("club"));

    $numero_invitados_mes_permitido = $datos_regla["MaximoInvitadoSocio"];
    $numero_invitados_dia_permitido = $datos_regla["MaximoInvitadoDia"];
    $numero_mismo_invitado_mes = $datos_regla["MaximoRepeticionInvitado"];
    $cumplimientoPasadas = $datos_regla[CumplimientoInvitados];
    $cumplimientoFuturas = $datos_regla[CumplimientoInvitadosFuturas];

    if ($datos_regla["MaximoRepeticionInvitadoSocio"] == 1) {
        $condicionsocio = " AND IDSocio = $row[IDSocio]";
    }

    if ($cumplimientoPasadas == 'S' && $cumplimientoFuturas == 'S') {
        $condicionCumplidasFyP = " AND ((FechaIngreso >= '$hoy'  AND Estado = 'P') OR (FechaIngreso <= '$hoy' AND Estado = 'I'))";
    } elseif ($cumplimientoPasadas == 'S' && $cumplimientoFuturas == 'N') {
        $condicionCumplidasFyP = " AND ((FechaIngreso <= '$hoy' AND Estado = 'I'))";
    } elseif ($cumplimientoPasadas == 'N' && $cumplimientoFuturas == 'S') {
        $condicionCumplidasFyP = " AND ((FechaIngreso >= '$hoy'  AND Estado = 'P'))";
    } else {
        $condicionCumplidasFyP = "";
    }

    $responce->rows[$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") :

        if ((int) $datos_regla["MaximoInvitadoDia"] == 0) {
            $numero_invitados_dia_permitido = 3;
            if (SIMUser::get("club") == 70) {
                $numero_invitados_dia_permitido = 10;
            }
        }

        //Consulto cuantas veces la persona ha sido invitada en el mes
        $mes_invitacion = date("m");
        $dia_invitacion = date("d");
        $year_invitacion = date("Y");
        $hoy_invitacion = date("Y-m-d");

        $sql1 = "Select * From SocioInvitado Where NumeroDocumento = '" . $row["NumeroDocumento"] . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and Year(FechaIngreso) = '" . $year_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionsocio $condicionCumplidasFyP";
        $sql_numero_invitacion = $dbo->query($sql1);
        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
        //Consulto cuantas personas ha invitado el socio en el mes
        $sql2 = "Select * From SocioInvitado Where IDSocio = '" . $row["IDSocio"] . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionCumplidasFyP";
        $sql_invitados_mes = $dbo->query($sql2);
        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
        //Consulto cuantas personas ha invitado el socio en el dia
        $sql3 = "Select * From SocioInvitado Where IDSocio = '" . $row["IDSocio"] . "' and FechaIngreso = '" . $hoy_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionCumplidasFyP";
        $sql_invitados_dia = $dbo->query($sql3);
        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

        $observacion = "";
        if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes) {
            if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes) {
                if ((int) $numero_invitados_dia_permitido > (int) $numero_invitados_dia) {
                    $color_fila = "#000000";
                } else {
                    $color_fila = "#EE080C";
                    $observacion = "Supera el max de " . $numero_invitados_dia_permitido . " invitac. dia";

                    if (!empty($datos_regla[MensajeNoValidaApp])) {
                        $observacion = $datos_regla[MensajeNoValidaApp];
                    }
                }
            } else {
                $color_fila = "#EE080C";
                $observacion = "Supera el max de " . $numero_invitados_mes_permitido . " invitac. mes";
            }
        } else if ((int) $numero_invitaciones > (int) $numero_mismo_invitado_mes && (int) $numero_mismo_invitado_mes > 0) {
            $color_fila = "#EE080C";
            $observacion = "Invitado mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
        }

        $btn_eliminar = '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

        switch ($row["Estado"]):
            case "I";
                $estado = "Ya ingreso: " . $row["FechaIngresoClub"];
                $boton_registro_ingreso = '';
                break;
            case "P";
                $estado = "Pendiente Ingreso";
                //$boton_registro_ingreso='<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id='.$row["IDSocioInvitado"].''.'" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
                $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm" href="#" id="btnrealizaringreso"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
                break;
        endswitch;

        $responce->rows[$i]['cell'] = array(
            "IDSocioInvitado" => $row["IDSocioInvitado"],
            "Ingreso" => $boton_registro_ingreso,
            "Estado" => "<font color='$color_fila'>" . $estado . "</font>",
            "Socio" => "<font color='$color_fila'>" . utf8_encode($row["Socio"]) . "</font>",
            "Accion" => "<font color='$color_fila'>" . utf8_encode($row["Accion"]) . "</font>",
            "NumeroDocumento" => "<font color='$color_fila'>" . $row["NumeroDocumento"] . "</font>",
            "Nombre" => "<font color='$color_fila'>" . addslashes($row["Nombre"]) . "</font>",
            "Obs" => "<font color='$color_fila'>" . utf8_encode($row["Observaciones"]) . "</font>",
            "FechaIngreso" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaIngreso"]) . "</font>",
            "Alerta" => $observacion,
            "FinalizarAut" => $btn_eliminar,
        );
    else :
        $responce->rows[$i]['cell'] = array(
            "IDSocioInvitado" => $row["IDSocioInvitado"],
            "Socio" => $row["IDSocio"],
            "NumeroDocumento" => $row["NumeroDocumento"],
            "Nombre" => $row["Nombre"],
            "FechaIngreso" => SIMUtil::tiempo($row["FechaIngreso"]),
        );
    endif;

    $i++;
}

echo json_encode($responce);
