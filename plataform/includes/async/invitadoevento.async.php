<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "SocioInvitadoEspecial";
$key = "IDSocioInvitadoEspecial";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  ";

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

                    $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'NumeroDocumento':

                    $where .= " AND (  I.NumeroDocumento LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Nombre':

                    $where .= " AND (  I.Nombre LIKE '%" . $search_object->data . "%' OR I.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Tipo':

                    $where .= " AND (  SocioInvitadoEspecial.TipoInvitacion LIKE '%" . $search_object->data . "%' )  ";
                    break;

                case 'Predio':
                    $where .= " AND ( (I.Predio LIKE '%" . $search_object->data . "%') OR (S.Predio LIKE '%" . $search_object->data . "%') )  ";
                    break;

                case 'Placa':
                    $sql_placa = "Select IDVehiculo From Vehiculo Where Placa like '%" . $search_object->data . "%' ";
                    $r_placa = $dbo->query($sql_placa);
                    while ($row_placa = $dbo->fetchArray($r_placa)) :
                        $array_id_vehiculo[] = $row_placa["IDVehiculo"];
                    endwhile;
                    if (count($array_id_vehiculo) > 0) :
                        $id_vehiculo = implode(",", $array_id_vehiculo);
                    endif;

                    $where .= " AND (  SocioInvitadoEspecial.IDVehiculo in (" . $id_vehiculo . ")  )  ";
                    break;

                case 'FechaInicio':
                    $where .= " AND FechaInicio >= '$search_object->data'";
                    $fecha_inicio = $search_object->data;
                    break;

                case 'FechaFin':
                    $where .= " AND FechaFin <= '$search_object->data'";
                    $fecha_inicio = $search_object->data;
                    break;

                case 'Accion':
                    $where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                default:
                    $where .= $array_buqueda->groupOp . "  SocioInvitadoEspecial." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $accion = $_GET["Accion"];
        if (!empty($accion)) {
            $where .= " AND ( Invitado.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
        }

        break;

    default:
        $where .= " AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  ";
        break;
}

if (empty($fecha_inicio)) {
    $fecha_inicio = date("Y-m-d");
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaInicio'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if (!$sidx) {
    $sidx = "FechaInicio";
}

// connect to the database

$result = $dbo->query("SELECT COUNT(*) FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioInvitadoEspecial.IDSocio = S.IDSocio AND SocioInvitadoEspecial.IDClub = '" . SIMUser::get("club") . "' AND I.IDInvitado = SocioInvitadoEspecial.IDInvitado  " . $condicion_id_invitacion);
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

if (!empty($_GET["IDInvitacion"])) :
    $condicion_id_invitacion = " and IDSocioInvitadoEspecial = '" . $_GET["IDInvitacion"] . "'";
endif;

/*
$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioInvitadoEspecial.IDSocio = S.IDSocio AND SocioInvitadoEspecial.IDClub = '" . SIMUser::get("club")  . "' AND I.IDInvitado = SocioInvitadoEspecial.IDInvitado AND SocioInvitadoEspecial.IDSocioInvitadoEspecial not in (Select IDInvitacion From LogAcceso Where Tipo = 'InvitadoAcceso' and Entrada = 'S' and DATE_FORMAT(FechaIngreso, '%Y-%m-%d') = '".$fecha_inicio."' ) " . $condicion_id_invitacion . "  ORDER BY $key $sord " . $str_limit;
 */

$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioInvitadoEspecial.IDSocio = S.IDSocio AND SocioInvitadoEspecial.IDClub = '" . SIMUser::get("club") . "' AND I.IDInvitado = SocioInvitadoEspecial.IDInvitado  " . $condicion_id_invitacion . "  ORDER BY $key $sord " . $str_limit;

//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

$sql_regla = "Select * From Regla Where IDClub = '" . SIMUser::get("club") . "'";
$qry_regla = $dbo->query($sql_regla);
$datos_regla = $dbo->fetchArray($qry_regla);

$numero_invitados_mes_permitido = $datos_regla["MaximoInvitadoSocio"];
$numero_invitados_dia_permitido = $datos_regla["MaximoInvitadoDia"];
$numero_mismo_invitado_mes = $datos_regla["MaximoRepeticionInvitado"];
// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
$cumplimiento_obligatorio_limite = $datos_regla["CumplimientoInvitados"];

while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") :

        $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $row["IDInvitado"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array");

        //Consulto cuantas veces la persona ha sido invitada en el mes
        $mes_invitacion = date("m");
        $dia_invitacion = date("d");
        $year_invitacion = date("Y");
        $hoy_invitacion = date("Y-m-d");

        $sql1 = "Select * From SocioInvitadoEspecial Where IDInvitado = '" . $row["IDInvitado"] . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and Year(FechaInicio) = '" . $year_invitacion . "' AND IDClub = '" . SIMUser::get("club") . "' and Ingreso = 'S'";
        $sql_numero_invitacion = $dbo->query($sql1);
        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
        //Consulto cuantas personas ha invitado el socio en el mes
        $sql2 = "Select * From SocioInvitadoEspecial Where IDSocio = '" . $row["IDSocio"] . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' and Ingreso = 'S'";
        $sql_invitados_mes = $dbo->query($sql2);
        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
        //Consulto cuantas personas ha invitado el socio en el dia
        $sql3 = "Select * From SocioInvitadoEspecial Where IDSocio = '" . $row["IDSocio"] . "' and FechaInicio = '" . $hoy_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' and Ingreso = 'S'";
        $sql_invitados_dia = $dbo->query($sql3);
        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);



        if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes) {
            if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes) {
                if ((int) $numero_invitados_dia_permitido > (int) $numero_invitados_dia) {
                    $color_fila = "#000000";
                } else {
                    $color_fila = "#EE080C";
                    $observacion = "Supera el max. de:" . $numero_invitados_dia_permitido . " invitac. dia";

                    if (SIMUser::get("club") == 124) {
                        $observacion = "Socio con mas de" . $numero_invitados_dia_permitido . " Invitaciones, se debe cobrar.";
                    }
                }
            } else {
                $color_fila = "#EE080C";
                $observacion = "Supera el max. de:" . $numero_invitados_mes_permitido . " invitac. mes";
            }
        } else {
            $color_fila = "#EE080C";
            $observacion = "Invitado mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
        }

        $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm" href="#" ><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';

        if ($row["FechaTrEd"] == "0000-00-00 00:00:00")
            $FechaCreacion = $row["FechaTrCr"];
        else
            $FechaCreacion = $row["FechaTrEd"];

        $responce->rows[$i]['cell'] = array(
            "IDSocioInvitadoEspecial" => $row["IDSocioInvitadoEspecial"],
            "Ingreso" => $boton_registro_ingreso,
            "NumeroDocumento" => "<font color='$color_fila'>" . $datos_invitado["NumeroDocumento"] . "</font>",
            "Nombre" => "<font color='$color_fila'>" . addslashes($row["NombreInvitado"]) . "</font>",
            "Tipo" => "<font color='$color_fila'>" . addslashes($row["TipoInvitacion"]) . "</font>",
            "Predio" => "<font color='$color_fila'>" . addslashes($datos_invitado["Predio"]) . " " . utf8_encode($datos_socio["Predio"]) . "</font>",
            "Placa" => "<font color='$color_fila'>" . $veh . "</font>",
            "FechaInicio" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaInicio"]) . "</font>",
            "FechaFin" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaFin"]) . "</font>",
            "Socio" => "<font color='$color_fila'>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "</font>",
            "Obs" => "<font color='$color_fila'>" . $observacion . "</font>",
            "Accion" => "<font color='$color_fila'>" . utf8_encode($datos_socio["Predio"]) . " " . utf8_encode($datos_socio["Accion"]) . "</font>",
            "FechaCreacionAut" => "<font color='$color_fila'>" . $FechaCreacion . "</font>",
            "CreadoPor" => "<font color='$color_fila'>" . utf8_encode($row["UsuarioTrCr"]) . "</font>",
        );
    endif;

    $i++;
}

echo json_encode($responce);
