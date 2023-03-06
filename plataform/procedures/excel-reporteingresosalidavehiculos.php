<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(dirname(__FILE__) . "/../../admin/config.inc.php");

$nombre = "EntradaSalidas_Vehiculos_" . date("Y_m_d");
$_GET["ImprimirRespuesta"] = "N";
$IDClub = $_GET["IDClub"];
$frm_get = SIMUtil::makeSafe($_GET);

$sql_estado_inv = "SELECT IDEstadoInvitado,Nombre FROM EstadoInvitado WHERE 1 ";
$r_estado_inv = $dbo->query($sql_estado_inv);
while ($row_estado_inv = $dbo->fetchArray($r_estado_inv)) {
    $array_estado_inv[$row_estado_inv["IDEstadoInvitado"]] = $row_estado_inv["Nombre"];
}

$sql_estado_soc = "SELECT IDEstadoSocio,Nombre FROM EstadoSocio WHERE 1 ";
$r_estado_soc = $dbo->query($sql_estado_soc);
while ($row_estado_soc = $dbo->fetchArray($r_estado_soc)) {
    $array_estado_soc[$row_estado_soc["IDEstadoSocio"]] = $row_estado_soc["Nombre"];
}
$table = "LogAcceso";
$key = "IDLogAcceso";
$where = " WHERE " . $table . ".IDClub = '" . $IDClub . "' AND Mecanismo LIKE 'Vehiculo%' ";

$oper = SIMNet::req("action");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}
switch ($oper) {

    case "search":
        if (!empty($frm_get['FechaInicio'])) {
            $where_filtro .= " AND FechaTrCr = '" . $frm_get['FechaInicio'] . " 00:00:00'";
        }
        if (!empty($frm_get['FechaFin'])) {
            $where_filtro .= " AND FechaTrCr = '" . $frm_get['FechaFin'] . " 23:59:59' ";
        }
        if (!empty($frm_get['FechaInicio']) && !empty($frm_get['FechaFin'])) {
            unset($where_filtro);
            $where_filtro .= " AND FechaTrCr >= '" . $frm_get['FechaInicio'] . " 00:00:00'";
            $where_filtro .= " AND FechaTrCr <= '" . $frm_get['FechaFin'] . " 23:59:59'";
        }
        if (!empty($frm_get['Placa'])) {
            $where_filtro .= " AND Mecanismo LIKE '%" . $frm_get['Placa'] . "%'";
        }

        if (!empty($frm_get['IDPortero'])) {
            $where_filtro .= " AND IDUsuario =" . $frm_get['IDPortero'];
        }

        break;

    default:
        if (empty($frm_get["FechaInicio"])) {
            $array_where[] = " FechaTrCr >= '" . date('Y-m-d') . " 00:00:00' AND FechaTrCr <= '" . date('Y-m-d') . " 23:59:59'";
        }
        if (count($array_where) > 0) :
            $where_filtro = " and " . implode(" and ", $array_where);
        endif;
        break;
}


$Values = "IDLogAcceso,IDInvitacion,CamposAcceso,Tipo, Mecanismo,IF(Entrada = 'S','Entrada','Salida') as Movimiento,IF(Entrada = 'S',FechaIngreso,FechaSalida) as FechaMovimiento,IDUsuario,LogAcceso.UsuarioTrCr";
$sql = "SELECT " . $table . ".$Values FROM " . $table . " " . $where . " " . $where_filtro . "  ORDER BY $key $sord";


$r_sql = $dbo->query($sql);
$NumSocios = $dbo->rows($r_sql);

$html = "";
$html .= "<table width='100%' border='1'>";
$html .= "<tr>";
$html .= "<th colspan='2'>Reporte Ingreso/Salida Vehiculos </th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th colspan='2'>Se encontraron " . $NumSocios . " registro(s) </th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>NOMBRE</th>";
$html .= "<th>PLACA</th>";
$html .= "<th>TIPO VEHICULO</th>";
$html .= "<th>MOVIMIENTO</th>";
$html .= "<th>FECHA/HORA</th>";
$html .= "<th>USUARIO</th>";
if ($IDClub == 9) {
    $html .= "<th>PORTERIA</th>";
}
$html .= "</tr>";

while ($data = $dbo->fetchArray($r_sql)) {

    switch ($data["Tipo"]):
        case "Contratista":
            $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $nombre_movimiento = ($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = ($datos_invitado["Predio"]);
            $tipo_persona = "Contratista";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";

            break;
        case "SocioInvitado":
            $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $nombre_movimiento = ($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = ($datos_invitado["Predio"]);
            $tipo_persona = "Contratista";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";
            break;
        case "Invitado":
            $datos_invitado = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitado["IDSocio"] . "' ", "array");
            $nombre_movimiento = ($datos_invitado["Nombre"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = "";
            $tipo_persona = "Invitado Socio";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";


            if ($IDClubConsulta == 124) {
                $hoy_invitacion = date("Y-m-d");
                //Consulto cuantas personas ha invitado el socio en el dia
                $sql3 = "Select * From SocioInvitado Where IDSocio = '" . $data["IDSocio"] . "' and FechaInicio = '" . $hoy_invitacion . "' and IDClub = '" . $IDClubConsulta . "' and Ingreso = 'S'";
                $sql_invitados_dia = $dbo->query($sql3);
                $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                if ((int) $maximo < (int) $numero_invitados_dia) {
                    $color_fila = "#EE080C";
                    $data["CamposAcceso"] = "Socio con mas de" . $maximo . " Invitaciones, se debe cobrar.";
                }
            }

            break;
        case "InvitadoAcceso":
            $data['IDInvitacion'];
            $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $nombre_movimiento = trim(($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]));
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            if (empty($nombre_movimiento)) {
                $nombre_movimiento = "Acceso nro " . $data["IDLogAcceso"];
            }

            $predio_movimiento = ($datos_invitado["Predio"]);
            if (empty($predio_movimiento)) {
                $predio_movimiento = ($datos_socio["Predio"]);
            }

            $tipo_persona = "Invitado Socio";
            $accion_movimiento =  $datos_socio['Accion'];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";


            break;
        case "InvitadoSocio":
            $nombre_movimiento = "Invitado anterior";
            $predio_movimiento = "";
            $tipo_persona = "Invitado v0";
            $accion_movimiento = "";
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";

            break;
        case "Socio":
        case "SocioClub":
            $whereSocio = empty($TipoSocio) ? "" : (" AND TipoSocio = '$TipoSocio' ");
            $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '$data[IDInvitacion]' $whereSocio", "array");
            $nombre_movimiento = ($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = ($datos_invitado["Predio"]);
            $tipo_persona = $datos_invitado["TipoSocio"];
            $accion_movimiento = $datos_invitado["Accion"];
            $cargo_movimiento = $datos_invitado["CargoSocio"];
            $areaempresa_movimiento = $datos_invitado["AreaEmpresa"];

            break;
        case "Usuario":
            //$whereUsuario = empty($sede) ? "" : ($sede == "brigde" ? " AND CodigoUsuario LIKE 'CBSA%' " : " AND CodigoUsuario NOT LIKE 'CBSA%'");
            $datos_invitado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $data["IDInvitacion"] . "' $whereUsuario", "array");
            $nombre_movimiento = ($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = ($datos_invitado["CodigoUsuario"]);
            $tipo_persona = "Empleado";
            $accion_movimiento = $datos_invitado["Accion"];
            $cargo_movimiento = $datos_invitado["Cargo"];
            $areaempresa_movimiento = $datos_invitado["AreaEmpresa"];
            break;

    endswitch;
    $Placa = explode(' ', $data['Mecanismo']);
    if (!empty($Placa) && $Placa > 0) {
        // $Vehiculo = $dbo->fetchAll('Vehiculo', "Placa = '" . $Placa[1] . "'", "array");
        $q_vehiculo = $dbo->query("select Placa, IDTipoVehiculo from Vehiculo where Placa = '" . $Placa[1] . "' limit 1");
        $Vehiculo = $dbo->assoc($q_vehiculo);
        if ($Vehiculo) {
            $PlacaVehiculo = $Vehiculo['Placa'];
            $TipoVehiculo = $dbo->getFields('TipoVehiculo', 'Nombre', 'IDTipoVehiculo=' . $Vehiculo['IDTipoVehiculo']);
        } else {
            $PlacaVehiculo = "---";
            $TipoVehiculo = "";
        }
    } else {
        $PlacaVehiculo = "---";
        $TipoVehiculo = "";
    }
    // if ($data["Salida"] == "S") :
    //     $TipoMovimiento = "Salida";
    //     $FechaMovimiento = $data["FechaSalida"];
    // elseif ($data["Entrada"] == "S") :
    //     $TipoMovimiento = "Entrada";
    //     $FechaMovimiento = $data["FechaIngreso"];
    // endif;
    $html .= "<tr>";
    $html .= "<td>" . utf8_decode($nombre_movimiento) . "</td>";
    $html .= "<td>" . utf8_decode($PlacaVehiculo) . "</td>";
    $html .= "<td>" . utf8_decode($TipoVehiculo) . "</td>";
    $html .= "<td>" . $data['Movimiento'] . "</td>";
    $html .= "<td>" . $data['FechaMovimiento'] . "</td>";
    $html .= "<td>" . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $data["IDUsuario"] . "'") . "</td>";
    if ($IDClub == 9) {
        $data_porteria = explode('|', $data['UsuarioTrCr']);
        $porteria = $data_porteria[1];
        $html .= "<td>" . $porteria . "</td>";
    }
    $html .= "</tr>";
}



$html .= "</table>";
// echo $html;
// exit;



header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $html;
exit;
