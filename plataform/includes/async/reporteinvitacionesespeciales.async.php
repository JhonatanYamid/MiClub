<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$IDClubConsulta = $IDClubConsulta = SIMUser::get("club");;
if (empty($IDClubConsulta))
    $IDClubConsulta = $_GET["IDClub"];

$table = "SocioInvitadoEspecial";
$key = "IDSocioInvitadoEspecial";
$where = " WHERE " . $table . ".IDClub = '" . $IDClubConsulta . "' and Socio.IDSocio=SocioInvitadoEspecial.IDSocio  AND SocioInvitadoEspecial.IDInvitado=Invitado.IDInvitado";
$script = "reporteinvitacionesespeciales";

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

                    $where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '%" . $search_object->data . "%' )  ";
                    break;

                default:
                    switch ($search_object->field):
                        case "NombreInvitado":
                            $search_object->field = "Nombre";
                            $where .= $array_buqueda->groupOp . "  SocioInvitadoEspecial." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                            break;

                        case "FechaIngreso":
                            $where .= $array_buqueda->groupOp . "  SocioInvitadoEspecial." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                            break;

                        case "NumeroDocumento":
                            $where .= $array_buqueda->groupOp . "  SocioInvitadoEspecial." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                            break;
                        case 'FechaInicio':
                            $where .= " AND FechaInicio = '$search_object->data'";
                            $fecha_inicio = $search_object->data;
                            break;

                        case 'FechaFin':
                            $where .= " AND FechaFin = '$search_object->data'";
                            $fecha_inicio = $search_object->data;
                            break;

                        default:
                            $where .= $array_buqueda->groupOp . "  Socio." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                            break;

                    endswitch;

                    break;
            }
        } //end for

        break;

    case "searchurl":


        $qryString = SIMNet::req("qryString");

        if (!empty($qryString)) {

            $where .= " AND ( Socio.Nombre LIKE '%" . $qryString . "%' OR SocioInvitadoEspecial.Nombre LIKE '%" . $qryString . "%' OR Socio.Apellido LIKE '%" . $qryString . "%' OR Socio.NumeroDocumento LIKE '%" . $qryString . "%'  OR SocioInvitadoEspecial.NumeroDocumento LIKE '%" . $qryString . "%' OR Accion LIKE '%" . $qryString . "%' )  ";
        } //end if
        break;
}


if (!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])) {
    $where_a .= " AND ( SocioInvitadoEspecial.FechaInicio >= '" . $_GET["FechaInicio"] . " 00:00:00' and SocioInvitadoEspecial.FechaInicio <= '" . $_GET["FechaFin"] . " 23:59:59') ";
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction

$condicion_fecha = "";
// // Consulto los dias asignados como fecha especial para no tomar en cuenta en reporte
// $sql_fecha_Especial = $dbo->query("Select Fecha From FechaEspecialInvitado Where IDClub = '" . SIMUser::get("club") . "'");
// while ($row_fecha_especial = $dbo->fetchArray($sql_fecha_Especial)) :
//     $condicion_fecha .= " and FechaIngreso <> '" . $row_fecha_especial["Fecha"] . "'";
// endwhile;

if (!$sidx) {
    $sidx = "SocioInvitadoEspecial.FechaInicio";
}

// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . ",Socio,Invitado" . $where . " " . $condicion_fecha . "    " . $where_a . "  ");
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

if (!$start || (int) $start <= 0) {
    $start = "0";
}

$sql = "SELECT " . $table . ".*, CONCAT(Invitado.Nombre,' ',Invitado.Apellido) AS NombreInvitado,Invitado.NumeroDocumento, Socio.IDSocio, Accion, TipoSocio, Socio.NumeroDocumento as DocumentoSocio, Socio.Email, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . ",Socio,Invitado" . $where . " " . $condicion_fecha . "   "  . $where_a . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
// print_R($sql);
// exit;
if ($_GET["ImprimirRespuesta"] != "N") {
    $result = $dbo->query($sql);



    $responce = "";

    $responce->page = (int) $page;
    $responce->total = (int) $total_pages;
    $responce->records = (int) $count;
    $i = 0;
    $hoy = date('Y-m-d');
    while ($row = $dbo->fetchArray($result)) {

        $datos_regla = SIMUtil::consulta_regla_invitacion($row[IDSocio], SIMUser::get("club"));

        $numero_invitados_mes_permitido = $datos_regla["MaximoInvitadoSocio"];
        $numero_invitados_dia_permitido = $datos_regla["MaximoInvitadoDia"];
        $numero_mismo_invitado_mes = $datos_regla["MaximoRepeticionInvitado"];
        $cumplimientoPasadas = $datos_regla[CumplimientoInvitados];
        $cumplimientoFuturas = $datos_regla[CumplimientoInvitadosFuturas];

        if (SIMUser::get("club") == 110)
            $numero_mismo_invitado_mes = 1000;


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

        $sql1 = "Select * From SocioInvitadoEspecial Where NumeroDocumento = '" . $row["NumeroDocumento"] . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and Year(FechaIngreso) = '" . $year_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionsocio $condicionCumplidasFyP";
        $sql_numero_invitacion = $dbo->query($sql1);
        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
        //Consulto cuantas personas ha invitado el socio en el mes
        $sql2 = "Select * From SocioInvitadoEspecial Where IDSocio = '" . $row["IDSocio"] . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionCumplidasFyP";
        $sql_invitados_mes = $dbo->query($sql2);
        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
        //Consulto cuantas personas ha invitado el socio en el dia
        $sql3 = "Select * From SocioInvitadoEspecial Where IDSocio = '" . $row["IDSocio"] . "' and FechaIngreso = '" . $hoy_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' $condicionCumplidasFyP";
        $sql_invitados_dia = $dbo->query($sql3);
        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

        $alerta = "";
        if ((int) $numero_invitaciones <= (int) $numero_mismo_invitado_mes) {
            if ((int) $numero_invitados_mes_permitido >= (int) $numero_invitados_mes) {
                if ((int) $numero_invitados_dia_permitido >= (int) $numero_invitados_dia) {
                    $color_fila = "#000000";
                } else {
                    $color_fila = "#EE080C";
                    $alerta = "Supera el max de " . $numero_invitados_dia_permitido . " invitac. dia";

                    if (!empty($datos_regla[MensajeNoValidaApp])) {
                        $alerta = $datos_regla[MensajeNoValidaApp];
                    }
                }
            } else {
                $color_fila = "#EE080C";
                $alerta = "Supera el max de " . $numero_invitados_mes_permitido . " invitac. mes";
            }
        } else {
            $color_fila = "#EE080C";
            $alerta = "Invitado mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
        }

        $responce->rows[$i]['id'] = $row[$key];

        $class = "a-edit-modal btnAddReg";
        $attr = "rev=\"reload_grid\"";

        if ($row['Ingreso'] != 'N') :
            $estado = "Ya ingreso: " . $row["FechaIngreso"];
            $boton_registro_ingreso = '';
        else :
            $estado = "Pendiente Ingreso";
            $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id=' . $row["IDSocioInvitadoEspecial"] . '' . '" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
        endif;

        if ($origen != "mobile") {
            $responce->rows[$i]['cell'] = array(
                $key => $row[$key],
                "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
                "TipoSocio" => "<font color='$color_fila'>" . $row["TipoSocio"] . "</font>",
                "Accion" => "<font color='$color_fila'>" . $row["Accion"] . "</font>",
                "Documento" => "<font color='$color_fila'>" . $row["DocumentoSocio"] . "</font>",
                "Nombre" => "<font color='$color_fila'>" . utf8_encode($row["Socio"]) . "</font>",
                "Apellido" => "<font color='$color_fila'>" . $row["Apellido"] . "</font>",
                "Email" => "<font color='$color_fila'>" . $row["Email"] . "</font>",
                "Estado" => "<font color='$color_fila'>" . $estado . "</font>",
                "NumeroDocumento" => "<font color='$color_fila'>" . $row["NumeroDocumento"] . "</font>",
                "NombreInvitado" => "<font color='$color_fila'>" . utf8_encode($row["NombreInvitado"]) . "</font>",
                "FechaIngreso" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaInicio"]) . "</font>",
                "Observaciones" => "<font color='$color_fila'>" . $row["Observaciones"] . "</font>",
                "Alerta" => "<font color='$color_fila'>" . $alerta . "</font>",

            );
        }

        $i++;
    }

    echo json_encode($responce);
}
