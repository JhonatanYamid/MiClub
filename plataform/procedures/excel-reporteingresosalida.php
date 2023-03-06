<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$nombre = "EntradaSalidas_" . date("Y_m_d");
require dirname(__FILE__) . "/../../admin/config.inc.php";
$_GET["ImprimirRespuesta"] = "N";
include("../../plataform/includes/async/reporteingresosalida.async.php");
$IDClub = $_GET["IDClub"];

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
/* echo $sql;
exit; */
$r_sql = $dbo->query($sql);

$html = "";
$html .= "<table width='100%' border='1'>";
$html .= "<tr>";
$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>TIPO</th>";
$html .= "<th>TIPO USUARIO</th>";
$html .= "<th>ACCION</th>";
$html .= "<th>DOCUMENTO</th>";
$html .= "<th>NOMBRE</th>";
$html .= "<th>DEPARTAMENTO</th>";
$html .= "<th>CARGO</th>";
$html .= "<th>ESTADO</th>";
$html .= "<th>PREDIO SOCIO</th>";
$html .= "<th>TIPO MOVIMIENTO</th>";
$html .= "<th>FECHA</th>";
$html .= "<th>HORA</th>";
$html .= "<th>MECANISMO</th>";
$html .= "<th>USUARIO</th>";
$r_campos = &$dbo->all("PreguntaAcceso", "IDClub = '" . $_GET["IDClub"] . "' Order by IDPreguntaAcceso");
$html .= "<th>FOTO</th>";
while ($r = $dbo->object($r_campos)) {
    $html .= "<th>" . $r->EtiquetaCampo . "</th>";
}
$html .= "</tr>";

while ($data = $dbo->fetchArray($r_sql)) {

    switch ($data["Tipo"]):
        case "Contratista":
            $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $tipo_persona = "Contratista";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";
            $estado_persona = $array_estado_inv[$datos_invitado["IDEstadoInvitado"]];
            break;
        case "SocioInvitado":
        case "Invitado":
            $datos_invitado = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitado["IDSocio"] . "' ", "array");
            $nombre_movimiento = utf8_encode($datos_invitado["Nombre"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $predio_movimiento = "";
            $tipo_persona = "Invitado Socio";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";

            $estado_persona = "";

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
            $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $data["IDInvitacion"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $nombre_movimiento = trim(utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]));
            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            if (empty($nombre_movimiento)) {
                $nombre_movimiento = "Acceso nro " . $data["IDLogAcceso"];
            }

            $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
            if (empty($predio_movimiento)) {
                $predio_movimiento = utf8_encode($datos_socio["Predio"]);
            }

            $tipo_persona = "Invitado Socio";
            $accion_movimiento = $datos_socio["Accion"];
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";

            $estado_persona = $array_estado_inv[$datos_invitado["IDEstadoInvitado"]];
            break;
        case "InvitadoEvento":
            $datos_invitado = $dbo->fetchAll("NoSocios", " IDNoSocios = '" . $row["IDInvitacion"] . "' ", "array");
            $nombre_movimiento = trim($datos_invitado["Nombre"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            if (empty($nombre_movimiento)) {
                $nombre_movimiento = "Acceso nro " . $row["IDLogAcceso"];
            }
            $accion_movimiento =  "";
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";
            $predio_movimiento = "";
            $tipo_persona = "Invitado Evento";
        case "InvitadoSocio":
            $nombre_movimiento = "Invitado anterior";
            $predio_movimiento = "";
            $tipo_persona = "Invitado v0";
            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $accion_movimiento = "";
            $cargo_movimiento = "";
            $areaempresa_movimiento = "";

            break;
        case "Socio":
        case "SocioClub":
            $whereSocio = empty($TipoSocio) ? "" : (" AND TipoSocio = '$TipoSocio' ");
            $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '$data[IDInvitacion]' $whereSocio", "array");
            $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
            $tipo_persona = $datos_invitado["TipoSocio"];
            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $accion_movimiento = $datos_invitado["Accion"];
            $cargo_movimiento = $datos_invitado["CargoSocio"];
            $areaempresa_movimiento = $datos_invitado['AreaEmpresa'];


            $estado_persona = $array_estado_soc[$datos_invitado["IDEstadoSocio"]];
            break;
        case "Usuario":
            //$whereUsuario = empty($sede) ? "" : ($sede == "brigde" ? " AND CodigoUsuario LIKE 'CBSA%' " : " AND CodigoUsuario NOT LIKE 'CBSA%'");
            $datos_invitado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $data["IDInvitacion"] . "' $whereUsuario", "array");
            $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $documento_movimiento = $datos_invitado["NumeroDocumento"];
            $predio_movimiento = utf8_encode($datos_invitado["CodigoUsuario"]);

            $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            $tipo_persona = "Empleado";
            $accion_movimiento = $datos_invitado["Accion"];
            $cargo_movimiento = $datos_invitado["Cargo"];
            $areaempresa_movimiento = $datos_invitado['AreaEmpresa'];


            $estado_persona = "";
            break;

    endswitch;

    if ($data["Salida"] == "S") :
        $TipoMovimiento = "Salida";
        $FechaMovimiento = $data["FechaSalida"];
    elseif ($data["Entrada"] == "S") :
        $TipoMovimiento = "Entrada";
        $FechaMovimiento = $data["FechaIngreso"];
    endif;
    $FechaMovimientoFinal = explode(" ", $FechaMovimiento);
    /* echo ('hola'.$data['IDLogAcceso']); */

    //print_r($data['OtrosDatos']);
    $TipoUsuario = ($data['Tipo'] == 'Usuario') ? $dbo->getFields('Usuario', 'TipoUsuario', "IDUsuario=" . $data['IDInvitacion']) : 'no entro';
    $html .= "<tr>";
    $html .= "<td>" . utf8_decode($tipo_persona) . "</td>";
    $html .= "<td>" . utf8_decode($TipoUsuario) . "</td>";
    $html .= "<td>" . $accion_movimiento . "</td>";
    $html .= "<td>" . $documento_movimiento . "</td>";
    $html .= "<td>" . utf8_decode($nombre_movimiento) . "</td>";
    $html .= "<td>" . utf8_decode($areaempresa_movimiento) . "</td>";
    $html .= "<td>" . utf8_decode($cargo_movimiento) . "</td>";
    $html .= "<td>" . $estado_persona . "</td>";
    $html .= "<td>" . $predio_movimiento . "</td>";
    $html .= "<td>" . $TipoMovimiento . "</td>";
    $html .= "<td>" . $FechaMovimientoFinal[0] . "</td>";
    $html .= "<td>" . $FechaMovimientoFinal[1] . "</td>";
    $html .= "<td>" . $data["Mecanismo"] . "</td>";
    $html .= "<td>" . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $data["IDUsuario"] . "'") . "</td>";
    if ($datos_invitado["FotoFile"] != '') {
        $html .= "<td><a href='" . $ruta_foto . "'>Ver Foto</a></td>";
    } else {

        $html .= "<td><a>Sin foto</a></td>";
    }
    if ($_GET["IDClub"] != 9 && $_GET["IDClub"] != 70) {
        //Consulto los campos dinamicos
        //$r_campos_acceso = &$dbo->all("AccesosOtrosDatos", "IDClub = '" . $_GET["IDClub"] . "' and IDLogAcceso = '" . $data["IDLogAcceso"] . "' Order by IDPreguntaAcceso");
        while ($r_campos = $dbo->object($r_campos_acceso)) {
            $html .= "<th>" . $r_campos->Valor . "</th>";
        }
    }
    $html .= "</tr>";
}



$html .= "</table>";
// echo $html;
// print_r($html);
// exit;



header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $html;

exit;


//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "LogAcceso";
$key = "IDLogAcceso";
$where = " WHERE " . $table . ".IDClub = '" . $_GET["IDClub"] . "'  ";

if (!empty($_GET["Documento"])) {
    //busco los invitados o socio con el numero de documento
    $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $_GET["Documento"] . "'");

    if (!empty($id_invitado)) :

        //Busco las invitaciones
        $sql_autorizacion = "Select * From SocioInvitadoEspecial Where IDInvitado = '" . $id_invitado . "'";
        $result_autorizacion = $dbo->query($sql_autorizacion);
        while ($row_autorizacion = $dbo->fetchArray($result_autorizacion)) :
            if (!empty($row_autorizacion["IDSocioInvitadoEspecial"])) :
                $array_autorizaciones[] = $row_autorizacion["IDSocioInvitadoEspecial"];
                $TipoBusqueda = 'InvitadoAcceso';
            endif;
        endwhile;

        //Busco las autorizaciones a contratistas
        $sql_autorizacion = "Select * From SocioAutorizacion Where IDInvitado = '" . $id_invitado . "'";
        $result_autorizacion = $dbo->query($sql_autorizacion);
        while ($row_autorizacion = $dbo->fetchArray($result_autorizacion)) :
            if (!empty($row_autorizacion["IDSocioAutorizacion"])) :
                $array_autorizaciones[] = $row_autorizacion["IDSocioAutorizacion"];
                $TipoBusqueda = 'Contratista';
            endif;
        endwhile;

        $sql_autorizacion = "Select * From SocioInvitado Where IDInvitado = '" . $id_invitado . "'";
        $result_autorizacion = $dbo->query($sql_autorizacion);
        while ($row_autorizacion = $dbo->fetchArray($result_autorizacion)) :
            if (!empty($row_autorizacion["IDSocioInvitado"])) :
                $array_autorizaciones[] = $row_autorizacion["IDSocioInvitado"];
                $TipoBusqueda = 'Invitado';
            endif;
        endwhile;

    endif;

    //Busco los socios
    $id_socio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $_GET["Documento"] . "' and IDClub = '" . SIMUser::get("club") . "' ");
    if (!empty($id_socio)) :
        $array_autorizaciones[] = $id_socio;
        $TipoBusqueda = 'Socio';
    endif;

    //Busco los funcionarios
    $id_funcionario = $dbo->getFields("Usuario", "IDUsuario", "NumeroDocumento = '" . $_GET["Documento"] . "' and IDClub = '" . SIMUser::get("club") . "' ");
    if (!empty($id_funcionario)) :
        $array_autorizaciones[] = $id_funcionario;
        $TipoBusqueda = 'Funcionario';
    endif;

    if (count($array_autorizaciones) > 0) :
        $id_autorizaciones = implode(",", $array_autorizaciones);
    else :
        $id_autorizaciones = "1"; //para que no encuentre resultados
    endif;

    $array_where[] = " IDInvitacion in (" . $id_autorizaciones . ") ";
}

if (!empty($_GET["AccionBusqueda"]) || !empty($_GET["PredioBusqueda"])) {

    if (!empty($_GET["AccionBusqueda"])) {
        $sql_accion = "Select * From Socio Where Accion like '" . $_GET["AccionBusqueda"] . "' or AccionPadre like '" . $_GET["AccionBusqueda"] . "' and IDClub = '" . SIMUser::get("club") . "'";
    } else {
        $sql_accion = "Select * From Socio Where Predio like '%" . $_GET["PredioBusqueda"] . "%' and IDClub = '" . SIMUser::get("club") . "'";
    }

    $r_accion = $dbo->query($sql_accion);
    while ($row_accion = $dbo->fetchArray($r_accion)) :
        $array_autorizaciones[] = $row_accion["IDSocio"];
    endwhile;
    $TipoBusqueda = 'Socio';

    if (count($array_autorizaciones) > 0) :
        $id_autorizaciones = implode(",", $array_autorizaciones);
    else :
        $id_autorizaciones = "1"; //para que no encuentre resultados
    endif;

    $array_where[] = " IDInvitacion in (" . $id_autorizaciones . ") and Tipo = '" . $TipoBusqueda . "' ";
}

if (!empty($_GET["Placa"])) {
    $array_where[] = " Mecanismo like '%" . $_GET["Placa"] . "%' ";
}

if (!empty($_GET["IDTipoInvitado"])) {
    switch ($_GET["IDTipoInvitado"]):
        case "Socio":
            $array_where[] = " (Tipo = 'SocioClub' OR Tipo = 'Socio')";
            break;
        case "ContratistaSocio":
            $array_where[] = " Tipo = 'Contratista' ";
            break;
        case "InvitadoSocio":
            $array_where[] = " (Tipo = 'InvitadoAcceso' OR Tipo = 'Invitado' OR Tipo = 'SocioInvitado')";
            break;
        case "Empleado":
            $array_where[] = " Tipo = 'Usuario' ";
            break;
        default:
            $sql_tipo_invitado = "Select * From Invitado Where IDTipoInvitado = '" . $_GET["IDTipoInvitado"] . "'";
            $result_tipo_invitado = $dbo->query($sql_tipo_invitado);
            while ($row_tipo_invitado = $dbo->fetchArray($result_tipo_invitado)) :
                $array_id_invitado_tipo[] = $row_tipo_invitado["IDInvitado"];
            endwhile;
            if (count($array_id_invitado_tipo) > 0) :
                $id_invitado_tipo = implode(",", $array_id_invitado_tipo);
                //Busco las autorizaciones
                $sql_autorizacion = "Select * From SocioAutorizacion Where IDInvitado in (" . $id_invitado_tipo . ")";
                $result_autorizacion = $dbo->query($sql_autorizacion);
                while ($row_autorizacion = $dbo->fetchArray($result_autorizacion)) :
                    $array_autorizaciones_tipo[] = $row_autorizacion["IDSocioAutorizacion"];
                    $TipoBusqueda = 'Contratista';
                endwhile;
            endif;
            if (count($array_autorizaciones_tipo) > 0) :
                $id_autorizaciones_tipo = implode(",", $array_autorizaciones_tipo);
            else :
                $id_autorizaciones_tipo = "1"; //para que no encuentre resultados
            endif;

            $array_where[] = " IDInvitacion in (" . $id_autorizaciones_tipo . ") and Tipo = '" . $TipoBusqueda . "' ";

            break;

    endswitch;
}

if (!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])) {
    $array_where[] = " ( FechaTrCr >= '" . $_GET["FechaInicio"] . " 00:00:00' and FechaTrCr <= '" . $_GET["FechaFin"] . " 23:59:59') ";
}

if (!empty($_GET["IDPortero"])) {
    $array_where[] = "  IDUsuario = '" . $_GET["IDPortero"] . "'";
}

if (!empty($_GET["IDCursoSede"])) {
    //Busco lo usuarios de esa sede
    $sql_usu_sede = "SELECT IDUsuario FROM Usuario WHERE IDCursoSede like '%|" . $_GET["IDCursoSede"] . "|%' ";
    $r_usu_sede = $dbo->query($sql_usu_sede);
    $array_usu_sede = array();
    while ($row_usu_sede = $dbo->fetchArray($r_usu_sede)) {
        $array_usu_sede[] = $row_usu_sede["IDUsuario"];
    }
    if (count($array_usu_sede) > 0) {
        $id_iusuario_sede = implode(",", $array_usu_sede);
    } else {
        $id_iusuario_sede = 0;
    }
    $array_where[] = "  IDUsuario in (" . $id_iusuario_sede . ") ";
}

if (!empty($_GET["Sede"])) {
    $sede = $_GET["Sede"];
}

if (!empty($_GET["TipoSocio"])) {
    $TipoSocio = $_GET["TipoSocio"];
}

if (count($array_where) > 0) :
    $where_filtro = " and " . implode(" and ", $array_where);
endif;

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
                case 'NombreSocio':

                    $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'NumeroDocumento':

                    $where .= " AND (  I.NumeroDocumento LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Accion':

                    $where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%' )  ";
                    break;

                case 'NombreInvitado':

                    $where .= " AND (  I.Nombre LIKE '%" . $search_object->data . "%' OR I.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Tipo':

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
        $accion = $_GET["Accion"];
        if (!empty($accion)) {
            $where .= " AND ( Invitado.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
        }

        break;

    default:
        // $where .= " AND FechaInicio >= CURDATE() AND FechaFin <= CURDATE()  ";
        $fecha_inicio = date("Y-m-d");
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

//Si es el club el rincon, solo me trae los registro de entrada
if (SIMUser::get("club") == 10) {
    $where .= " AND Entrada='S'";
}

//$sql = "SELECT " . $table . ".*, CONCAT( Invitado.Nombre, ' ', Invitado.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Invitado.IDClub = '" . SIMUser::get("club")  . "' AND Invitado.IDSocio = SocioAutorizacion.IDSocio ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".* FROM " . $table . " " . $where . " " . $where_filtro . "  ORDER BY $key $sord LIMIT " . $start . "," . $limit;

/* var_dump($sql);
exit; */
$result = $dbo->query($sql);

$responce->page = (int) $page;
$responce->total = (int) $total_pages;
$responce->records = (int) $count;
$i = 0;
$hoy = date('Y-m-d');
$results = [];

if (SIMUser::get("club") == 124) {
    $reglas = "SELECT * FROM Regla WHERE IDClub = 124";
    $qry = $dbo->query($reglas);
    $datosRegla = $dbo->fetchArray($qry);

    if ($datosRegla[MaximoInvitadoDiaValidaApp] == 0) {
        $maximo = $datosRegla[MaximoInvitadoDia];
    }
}

while ($row = $dbo->fetchArray($result)) {

    if (SIMUser::get("club") == 10) {
        $sql = "SELECT * FROM `LogAcceso`
				WHERE IDClub=10
				AND Salida='S'
				AND IDInvitacion={$row['IDInvitacion']}
				AND IDLogAcceso>{$row['IDLogAcceso']}
				ORDER BY IDLogAcceso ASC
				LIMIT 1";

        $logAccesoSalidas = $dbo->query($sql);
        $rowLogAccesoSalidas = $dbo->fetch($logAccesoSalidas);

        $mecanismoEntrada = explode(' ', $row["Mecanismo"])[0];
        $mecanismoSalida = explode(' ', $rowLogAccesoSalidas["Mecanismo"])[0];
        $placaEntrada = explode(' ', $row["Mecanismo"])[1];
        $placaSalida = explode(' ', $rowLogAccesoSalidas["Mecanismo"])[1];
    }


    $results[] = $row;

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") :

        $color_fila = "#000000";

        switch ($row["Tipo"]):
            case "Contratista":
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $row["IDInvitacion"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                $documento_movimiento = $datos_invitado["NumeroDocumento"];
                $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
                $tipo_persona = "Contratista";
                $accion_movimiento = "";
                $cargo_movimiento = "";
                $areaempresa_movimiento = "";

                break;
            case "SocioInvitado":
            case "Invitado":
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                $datos_invitado = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $row["IDInvitacion"] . "' ", "array");
                $nombre_movimiento = utf8_encode($datos_invitado["Nombre"]);
                $documento_movimiento = $datos_invitado["NumeroDocumento"];
                $predio_movimiento = "";
                $tipo_persona = "Invitado Socio";
                $accion_movimiento = "";
                $cargo_movimiento = "";
                $areaempresa_movimiento = "";


                if (SIMUser::get("club") == 124) {
                    $hoy_invitacion = date("Y-m-d");
                    //Consulto cuantas personas ha invitado el socio en el dia
                    $sql3 = "Select * From SocioInvitado Where IDSocio = '" . $row["IDSocio"] . "' and FechaInicio = '" . $hoy_invitacion . "' and IDClub = '" . SIMUser::get("club") . "' and Ingreso = 'S'";
                    $sql_invitados_dia = $dbo->query($sql3);
                    $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                    if ((int) $maximo < (int) $numero_invitados_dia) {
                        $color_fila = "#EE080C";
                        $row["CamposAcceso"] = "Socio con mas de" . $maximo . " Invitaciones, se debe cobrar.";
                    }
                }

                break;
            case "InvitadoAcceso":
                $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $row["IDInvitacion"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $nombre_movimiento = trim(utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]));
                $documento_movimiento = $datos_invitado["NumeroDocumento"];
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                if (empty($nombre_movimiento)) {
                    $nombre_movimiento = "Acceso nro " . $row["IDLogAcceso"];
                }

                $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
                if (empty($predio_movimiento)) {
                    $predio_movimiento = utf8_encode($datos_socio["Predio"]);
                }

                $tipo_persona = "Invitado Socio";
                $accion_movimiento = "";
                $cargo_movimiento = "";
                $areaempresa_movimiento = "";
                break;
            case "InvitadoSocio":
                $nombre_movimiento = "Invitado anterior";
                $predio_movimiento = "";
                $tipo_persona = "Invitado v0";
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                $accion_movimiento = "";
                $cargo_movimiento = "";
                $areaempresa_movimiento = "";
                break;
            case "Socio":
            case "SocioClub":
                $whereSocio = empty($TipoSocio) ? "" : (" AND TipoSocio = '$TipoSocio' ");
                $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '$row[IDInvitacion]' $whereSocio", "array");
                $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                $documento_movimiento = $datos_invitado["NumeroDocumento"];
                $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
                $tipo_persona = $datos_invitado["TipoSocio"];
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                $accion_movimiento = $datos_invitado["Accion"];
                $cargo_movimiento = $datos_socio["CargoSocio"];
                $areaempresa_movimiento = $datos_invitado['AreaEmpresa'];
                break;
            case "Usuario":
                //$whereUsuario = empty($sede) ? "" : ($sede == "brigde" ? " AND CodigoUsuario LIKE 'CBSA%' " : " AND CodigoUsuario NOT LIKE 'CBSA%'");
                $datos_invitado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row["IDInvitacion"] . "' $whereUsuario", "array");
                $nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                $documento_movimiento = $datos_invitado["NumeroDocumento"];
                $predio_movimiento = utf8_encode($datos_invitado["Predio"]);
                $tipo_persona = "Empleado";
                $ruta_foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                $accion_movimiento = $datos_invitado["Accion"];
                $cargo_movimiento = $datos_socio["Cargo"];
                $areaempresa_movimiento = $datos_invitado['AreaEmpresa'];


                $data['Predio'] = $datos_invitado["CodigoUsuario"];
                echo "aca";
                exit;
                break;

        endswitch;

        if ($row["Salida"] == "S") :
            $TipoMovimiento = "Salida";
            $FechaMovimiento = $row["FechaSalida"];
        elseif ($row["Entrada"] == "S") :
            $TipoMovimiento = "Entrada";
            $FechaMovimiento = $row["FechaIngreso"];
        endif;

        if (!empty($datos_invitado)) {
            /*      $responce->rows[$i]['id'] = $row[$key]; */
            $responce->rows[$i]/* ['cell'] */ = array(
                "IDLogAcceso" => $row["IDLogAcceso"],
                "Tipo" =>  $tipo_persona,
                "Documento" =>  $documento_movimiento,
                "Accion" =>  $accion_movimiento,
                "Nombre" =>  utf8_decode($nombre_movimiento),
                "Departamento" =>  utf8_decode($areaempresa_movimiento),
                "Cargo" =>  utf8_decode($cargo_movimiento),
                "Predio" =>  $predio_movimiento,
                "TipoMovimiento" =>  $TipoMovimiento,
                "FechaHora" =>  $FechaMovimiento,
                "Mecanismo" =>  $row["Mecanismo"],
                "OtrosDatos" =>  $row["CamposAcceso"],
                "Usuario" => $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["IDUsuario"] . "'"),
            );
        } else {
            $responce->records--;
            $i--;
        }

        if (SIMUser::get("club") == 10) {
            $responce->rows[$i]['id'] = $row[$key];
            $responce->rows[$i]['cell']["FechaHoraSalida"] = $rowLogAccesoSalidas["FechaSalida"];
            $responce->rows[$i]['cell']["MecanismoSalida"] = $rowLogAccesoSalidas["Mecanismo"];
            $responce->rows[$i]['cell']["UsuarioSalida"] = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $rowLogAccesoSalidas["IDUsuario"] . "'");
            $responce->rows[$i]['cell']["PlacaEntrada"] = $placaEntrada;
            $responce->rows[$i]['cell']["PlacaSalida"] = $placaSalida;
        }

    endif;

    $i++;

    $sql_reporte = "SELECT Tipo, IDInvitacion, IDLogAcceso, Salida, Entrada, FechaSalida, FechaIngreso, Mecanismo, IDUsuario FROM " . $table . " " . $where . " " . $where_filtro;

    //$sql_reporte = "SELECT " . $table . ".* FROM " . $table . " " . $where . " ". $where_filtro. "  ORDER BY IDLogAcceso Desc LIMIT 40000";

    $result_reporte = $dbo->query($sql_reporte);

    /* var_dump($sql_reporte);
    exit; */

    //var_dump("Pasa la consulta de los log de acceso");

    $nombre = "EntradaSalidas_" . date("Y_m_d");

    $NumSocios = $dbo->rows($result_reporte);


    if ($NumSocios > 0) {
        $html = "";
        $html .= "<table width='100%' border='1'>";
        $html .= "<tr>";
        $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<th>TIPO</th>";
        $html .= "<th>ACCION</th>";
        $html .= "<th>DOCUMENTO</th>";
        $html .= "<th>NOMBRE</th>";
        $html .= "<th>DEPARTAMENTO</th>";
        $html .= "<th>CARGO</th>";
        $html .= "<th>PREDIO SOCIO</th>";
        $html .= "<th>TIPO MOVIMIENTO</th>";
        $html .= "<th>FECHA/HORA</th>";
        $html .= "<th>MECANISMO</th>";
        $html .= "<th>USUARIO</th>";
        $html .= "<th>FOTO</th>";
        //$r_campos = &$dbo->all("PreguntaAcceso", "IDClub = '" . $_GET["IDClub"] . "' Order by IDPreguntaAcceso");
        while ($r = $dbo->object($r_campos)) {
            $html .= "<th>" . $r->EtiquetaCampo . "</th>";
        }
        $html .= "</tr>";
    }


    echo "sig";
    //construimos el excel
    /* header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
 */
    /* echo $html;
exit(); */
}

//$responce->sql = $sql ;
//$responce->result = $results;
// $responce->query = $whereUsuario;
// $responce->sede = $sede;


$datosR = $responce->rows;



foreach ($datosR as $data) {

    /* echo ('hola'.$data['IDLogAcceso']); */


    //print_r($data['OtrosDatos']);

    $html .= "<tr>";
    $html .= "<td>" . utf8_decode($data['Tipo']) . "</td>";
    $html .= "<td>" . $data['Accion'] . "</td>";
    $html .= "<td>" . $data['Documento'] . "</td>";
    $html .= "<td>" . utf8_decode($data['Nombre']) . "</td>";
    $html .= "<td>" . utf8_decode($data['Departamento']) . "</td>";
    $html .= "<td>" . utf8_decode($data['Cargo']) . "</td>";
    $html .= "<td>" . $data['Predio'] . "</td>";
    $html .= "<td>" . $data['TipoMovimiento'] . "</td>";
    $html .= "<td>" . $data['FechaHora'] . "</td>";
    $html .= "<td>" . $data["Mecanismo"] . "</td>";
    $html .= "<td>" . $data["Usuario"] . "</td>";
    $html .= "<td><a href='" . $ruta_foto . "'>Ver Foto</a></td>";

    if ($_GET["IDClub"] != 9 && $_GET["IDClub"] != 70) {
        //Consulto los campos dinamicos
        //$r_campos_acceso = &$dbo->all("AccesosOtrosDatos", "IDClub = '" . $_GET["IDClub"] . "' and IDLogAcceso = '" . $data["IDLogAcceso"] . "' Order by IDPreguntaAcceso");
        while ($r_campos = $dbo->object($r_campos_acceso)) {
            $html .= "<th>" . $r_campos->Valor . "</th>";
        }
    }
    $html .= "</tr>";
}



$html .= "</table>";

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $html;
exit();


        //echo json_encode($responce->rows['tipo_persona']);
