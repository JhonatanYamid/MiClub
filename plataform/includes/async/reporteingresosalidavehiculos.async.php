<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);

$columns = array();
$origen = SIMNet::req("origen");

$IDClubConsulta = $IDClubConsulta = SIMUser::get("club");;
if (empty($IDClubConsulta))
    $IDClubConsulta = $frm_get["IDClub"];

$table = "LogAcceso";
$key = "IDLogAcceso";
$where = " WHERE " . $table . ".IDClub = '" . $IDClubConsulta . "' AND Mecanismo LIKE 'Vehiculo%' ";

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



                case 'IDTipoVehiculo':

                    $where .= " AND (  SocioAutorizacion.TipoAutorizacion LIKE '%" . $search_object->data . "%' )  ";
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

                    $where .= " AND (  SocioAutorizacion.IDVehiculo in (" . $id_vehiculo . ")  )  ";
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
                    $where .= $array_buqueda->groupOp . "  SocioAutorizacion." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $accion = $frm_get["Accion"];
        if (!empty($accion)) {
            $array_where[] = " AND ( Invitado.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
        }
        if (!empty($frm_get["Placa"])) {
            $array_where[] = " Mecanismo like '%" . $frm_get["Placa"] . "%' ";
        }
        if (!empty($frm_get["IDTipoVehiculo"])) {
            $array_where[] = " IDTipoVehiculo == '" . $frm_get['IDTipoVehiculo'] . "'";
        }
        if (!empty($frm_get["FechaInicio"])) {
            // $array_where[] = "FechaIngreso >= '" . $frm_get["FechaInicio"] . " 00:00:00' ";
            $array_where[] = "IF(Entrada = 'S',FechaIngreso >= '" . $frm_get['FechaInicio'] . " 00:00:00',FechaSalida >= '" . $frm_get['FechaInicio'] . " 00:00:00')";
        }
        if (!empty($frm_get["FechaFin"])) {
            // $array_where[] = "FechaIngreso <= '" . $frm_get["FechaFin"] . " 23:59:59' ";
            $array_where[] = "IF(Entrada = 'S',FechaIngreso <= '" . $frm_get['FechaFin'] . " 23:59:59',FechaSalida <= '" . $frm_get['FechaFin'] . " 23:59:59')";
        }
        if (!empty($frm_get["IDPortero"])) {
            $array_where[] = "  IDUsuario = '" . $frm_get["IDPortero"] . "'";
        }
        if (count($array_where) > 0) :
            $where_filtro = " and " . implode(" and ", $array_where);
        endif;

        break;

    default:
        // $where .= " AND FechaInicio >= CURDATE() AND FechaFin <= CURDATE()  ";
        $fecha_inicio = date("Y-m-d");
        break;
}

if (empty($fecha_inicio)) {
    $fecha_inicio = date("Y-m-d");
}

$page = $frm_get['page']; // get the requested page
$limit = $frm_get['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaInicio'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if (!$sidx) {
    $sidx = "FechaInicio";
}

// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . "  " . $where . " " . $where_filtro);
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

$start = ((int) $limit * (int) $page) - (int) $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$Values = "IDLogAcceso,IDInvitacion,Tipo,CamposAcceso, Mecanismo,IF(Entrada = 'S','Entrada','Salida') as Movimiento,IF(Entrada = 'S',FechaIngreso,FechaSalida) as FechaMovimiento,IDUsuario";
$sql = "SELECT " . $table . ".$Values FROM " . $table . " " . $where . " " . $where_filtro . "  ORDER BY $key $sord LIMIT " . $start . "," . $limit;



if ($frm_get["ImprimirRespuesta"] != "N") {
    $result = $dbo->query($sql);

    $responce->page = (int) $page;
    $responce->total = (int) $total_pages;
    $responce->records = (int) $count;
    $i = 0;
    $hoy = date('Y-m-d');
    $results = [];

    while ($row = $dbo->fetchArray($result)) {
        $results[] = $row;

        $class = "a-edit-modal btnAddReg";
        $attr = "rev=\"reload_grid\"";
        if ($origen != "mobile") :

            $color_fila = "#000000";

            switch ($row["Tipo"]):
                case "Contratista":
                    $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $row["IDInvitacion"] . "' ", "array");
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
                    $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $row["IDInvitacion"] . "' ", "array");
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
                    $datos_invitado = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $row["IDInvitacion"] . "' ", "array");
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
                        $sql3 = "Select * From SocioInvitado Where IDSocio = '" . $row["IDSocio"] . "' and FechaInicio = '" . $hoy_invitacion . "' and IDClub = '" . $IDClubConsulta . "' and Ingreso = 'S'";
                        $sql_invitados_dia = $dbo->query($sql3);
                        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                        if ((int) $maximo < (int) $numero_invitados_dia) {
                            $color_fila = "#EE080C";
                            $row["CamposAcceso"] = "Socio con mas de" . $maximo . " Invitaciones, se debe cobrar.";
                        }
                    }

                    break;
                case "InvitadoAcceso":
                    $row['IDInvitacion'];
                    $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $row["IDInvitacion"] . "' ", "array");
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $nombre_movimiento = trim(($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]));
                    $documento_movimiento = $datos_invitado["NumeroDocumento"];
                    if (empty($nombre_movimiento)) {
                        $nombre_movimiento = "Acceso nro " . $row["IDLogAcceso"];
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
                    $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '$row[IDInvitacion]' $whereSocio", "array");
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
                    $datos_invitado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row["IDInvitacion"] . "' $whereUsuario", "array");
                    $nombre_movimiento = ($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                    $documento_movimiento = $datos_invitado["NumeroDocumento"];
                    $predio_movimiento = ($datos_invitado["CodigoUsuario"]);
                    $tipo_persona = "Empleado";
                    $accion_movimiento = $datos_invitado["Accion"];
                    $cargo_movimiento = $datos_invitado["Cargo"];
                    $areaempresa_movimiento = $datos_invitado["AreaEmpresa"];
                    break;

            endswitch;

            // if ($row["Salida"] == "S") :
            //     $TipoMovimiento = "Salida";
            //     $FechaMovimiento = $row["FechaSalida"];
            // elseif ($row["Entrada"] == "S") :
            //     $TipoMovimiento = "Entrada";
            //     $FechaMovimiento = $row["FechaIngreso"];
            // endif;

            $Placa = explode(' ', $row['Mecanismo']);
            $Vehiculo = $dbo->fetchAll('Vehiculo', "Placa = '" . $Placa[1] . "'", "array");
            if ($Vehiculo) {
                $PlacaVehiculo = $Vehiculo['Placa'];
                $TipoVehiculo = $dbo->getFields('TipoVehiculo', 'Nombre', 'IDTipoVehiculo=' . $Vehiculo['IDTipoVehiculo']);
            } else {
                $PlacaVehiculo = "---";
                $TipoVehiculo = "";
            }
            if (!empty($datos_invitado)) {
                $responce->rows[$i]['id'] = $row[$key];
                $responce->rows[$i]['cell'] = array(
                    "Nombre" => "<font color='$color_fila'>" . ($nombre_movimiento) . "</font>",
                    "Placa" => "<font color='$color_fila'>" . $PlacaVehiculo . "</font>",
                    "TipoVehiculo" => "<font color='$color_fila'>" . $TipoVehiculo . "</font>",
                    "Movimiento" => "<font color='$color_fila'>" . $row['Movimiento'] . "</font>",
                    "FechaMovimiento" => "<font color='$color_fila'>" . $row['FechaMovimiento'] . "</font>",
                    "Usuario" => $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["IDUsuario"] . "'") . "</font>",
                );
            } else {
                $responce->records--;
                $i--;
            }
        endif;

        $i++;
    }
    //$responce->sql = $sql ;
    //$responce->result = $results;
    // $responce->query = $whereUsuario;
    // $responce->sede = $sede;
    echo json_encode($responce);
}
