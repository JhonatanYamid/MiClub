<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

if (SIMUser::get("club") == 70) {
    if (SIMUser::get("IDPerfil") == 0) {
        $condicion_creacion = "";
    } else {
        $condicion_creacion = " and SA.UsuarioTrCr = '" . SIMUser::get("IDUsuario") . "'";
    }
}

$table = "SocioAutorizacion";
$key = "IDSocioAutorizacion";
$where = " WHERE SA.IDClub = '" . SIMUser::get("club") . "'  ";

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

                    $where .= " AND (  I.NumeroDocumento = '" . $search_object->data . "'  )  ";
                    break;

                case 'Nombre':

                    $where .= " AND (  I.Nombre LIKE '%" . $search_object->data . "%' OR I.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Tipo':

                    $where .= " AND (  SA.TipoAutorizacion LIKE '%" . $search_object->data . "%' )  ";
                    break;

                case 'Predio':
                    $where .= " AND ( (I.Predio LIKE '%" . $search_object->data . "%') OR (S.Predio LIKE '%" . $search_object->data . "%') )  ";
                    break;

                case 'Accion':
                    $where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%' )  ";
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

                    $where .= " AND (  SA.IDVehiculo in (" . $id_vehiculo . ")  )  ";
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
                    $where .= $array_buqueda->groupOp . "  SA." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
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

$sql = "SELECT COUNT(*), CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I, Socio S" . $where . " AND SA.IDSocio = S.IDSocio AND I.IDInvitado = SA.IDInvitado AND SA.Ingreso != 'S' AND DATE(SA.FechaSalida)  != '2021-10-05'";
$result = $dbo->query($sql);
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

//$sql = "SELECT " . $table . ".*, CONCAT( Invitado.Nombre, ' ', Invitado.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Invitado.IDClub = '" . SIMUser::get("club")  . "' AND Invitado.IDSocio = SA.IDSocio ORDER BY $key $sord " . $str_limit;
//$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I, Socio S" . $where . " AND SA.IDSocio = S.IDSocio AND I.IDInvitado = SA.IDInvitado AND SA.IDSocioAutorizacion not in (Select IDInvitacion From LogAcceso Where Tipo = 'Contratista' and Entrada = 'S' and DATE_FORMAT(FechaIngreso, '%Y-%m-%d') = '".$fecha_inicio."' )  ORDER BY $key $sord " . $str_limit;
//$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I, Socio S" . $where . " AND SA.IDSocio = S.IDSocio AND I.IDInvitado = SA.IDInvitado ".$condicion_creacion." ORDER BY $key $sord " . $str_limit;

//Consulta otro campo socio autorizacion
/*$sqlCampoContratista = "SELECT IDCampoFormularioContratista, EtiquetaCampo FROM CampoFormularioContratista WHERE Orden=1 AND IDClub=" . SIMUser::get("club");
$queryCampoContratista = $dbo->query($sqlCampoContratista);
$campoContratista = $dbo->fetch($queryCampoContratista);
$IDCampoFormularioContratista = $campoContratista["IDCampoFormularioContratista"];
$EtiquetaCampo =  $campoContratista["EtiquetaCampo"];*/

// SQL Actualizado para mostrar autorizaciones vigentes que han marcado ingreso/salida 20230116
// $sql = "SELECT SA.*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado
// FROM Invitado I
// JOIN SocioAutorizacion SA ON I.IDInvitado = SA.IDInvitado
// JOIN Socio S ON S.IDSocio = SA.IDSocio
// " . $where . " " . $condicion_creacion . "  AND (SA.Ingreso != 'S' OR DATE(SA.FechaIngreso) != CURDATE()) AND (DATE(SA.FechaSalida)  != CURDATE() or SA.FechaSalida is null)
// ORDER BY SA.IDSocioAutorizacion DESC " . $str_limit;

$sql = "SELECT SA.*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado
FROM Invitado I
JOIN SocioAutorizacion SA ON I.IDInvitado = SA.IDInvitado
JOIN Socio S ON S.IDSocio = SA.IDSocio
" . $where . " " . $condicion_creacion . "  AND (SA.Ingreso != 'S' OR DATE(SA.FechaIngreso) != CURDATE()) AND (DATE(SA.FechaSalida)  != CURDATE() or SA.FechaSalida is null)
ORDER BY SA.IDSocioAutorizacion DESC " . $str_limit;

//exit;
// var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $OtrosDatos = "";
    $responce->rows[$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen != "mobile") :

        //// Consulto las reglas que aplica al socio para invitaciones
        $array_datos_regla = SIMUtil::consulta_regla_invitacion($row["IDSocio"], SIMUser::get("club"));

        $numero_invitados_mes_permitido = $array_datos_regla["MaximoInvitadoSocio"];
        $numero_invitados_dia_permitido = $array_datos_regla["MaximoInvitadoDia"];
        $numero_mismo_invitado_mes = $array_datos_regla["MaximoRepeticionInvitado"];
        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
        $cumplimiento_obligatorio_limite = $array_datos_regla["CumplimientoInvitados"];

        /*
        $numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".SIMUser::get("club")."'" );
        $numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".SIMUser::get("club")."'" );
        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
        $cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".SIMUser::get("club")."'" );
         */

        /*
        //Consulto cuantas veces la persona ha sido invitada en el mes
        $mes_invitacion = date("m");
        $dia_invitacion = date("d");
        $year_invitacion = date("Y");
        $hoy_invitacion = date("Y-m-d");
        $sql_numero_invitacion = $dbo->query("Select * From SocioAutorizacion Where NumeroDocumento = '".$row["NumeroDocumento"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and Year(FechaInicio) = '".$year_invitacion."'IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
        //Consulto cuantas personas ha invitado el socio en el mes
        $sql_invitados_mes = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$row["IDSocio"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
        //Consulto cuantas personas ha invitado el socio en el dia
        $sql_invitados_dia = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$row["IDSocio"]."' and FechaInicio = '".$hoy_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);
         */

        $observacion = "";
        if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes) {
            if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes) {
                if ((int) $numero_invitados_dia_permitido > (int) $numero_invitados_dia) {
                    $color_fila = "#000000";
                } else {
                    $$color_fila = "#EE080C";
                    $observacion = "Supera el max. de:" . $numero_invitados_mes_permitido . " invitac. dia";
                }
            } else {
                $color_fila = "#EE080C";
                $observacion = "Supera el max. de:" . $numero_invitados_mes_permitido . " invitac. mes";
            }
        } else {
            $color_fila = "#EE080C";
            $observacion = "Invitado mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
        }

        $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm" href="#" id="btnrealizarsalida"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';

        $color_fila = "#000000";
        $observacion = "";

        $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $row["IDInvitado"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array");

        if ($row["UsuarioTrCr"] == "Socio") :
            $creadapor = "Socio";
        else :
            $creadapor = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["UsuarioTrCr"] . "'");
        endif;

        if ($row["FechaTrEd"] == "0000-00-00 00:00:00")
            $FechaCreacion = $row["FechaTrCr"];
        else
            $FechaCreacion = $row["FechaTrEd"];

        $sql_otros_dat = "SELECT Valor From SocioAutorizacionOtrosDatos WHERE IDSocioAutorizacion = '" . $row['IDSocioAutorizacion'] . "'";
        $r_otros_dat = $dbo->query($sql_otros_dat);
        while ($row_otros_dat = $dbo->fetchArray($r_otros_dat)) {
            $OtrosDatos .=  $row_otros_dat["Valor"];
        }


        $responce->rows[$i]['cell'] = array(
            "IDSocioAutorizacion" => $row["IDSocioAutorizacion"],
            "Ingreso" => $boton_registro_ingreso,
            "NumeroDocumento" => "<font color='$color_fila'>" . utf8_encode($datos_invitado["NumeroDocumento"]) . "</font>",
            "Nombre" => "<font color='$color_fila'>" . utf8_encode(SIMUtil::remplaza_acentos($row["NombreInvitado"])) . "</font>",
            "Tipo" => "<font color='$color_fila'>" . addslashes($row["TipoAutorizacion"]) . "</font>",
            "Predio" => "<font color='$color_fila'>" . addslashes($datos_invitado["Predio"]) . " " . utf8_encode($datos_socio["Predio"]) . "</font>",
            //"$EtiquetaCampo" => "<font color='$color_fila'>" . $row["Valor"] . "</font>",
            "FechaInicio" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaInicio"]) . "</font>",
            "FechaFin" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaFin"]) . "</font>",
            "Socio" => "<font color='$color_fila'>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "</font>",
            "Accion" => "<font color='$color_fila'>" . utf8_encode($datos_socio["Accion"]) . "</font>",
            "ObservacionSocio" => "<font color='$color_fila'>" .  $row['ObservacionSocio'] . " " . $OtrosDatos  . "</font>",
            "CreadaPor" => "<font color='$color_fila'>" . utf8_encode($creadapor) . "</font>",
            "FechaCreacionAut" => "<font color='$color_fila'>" . $FechaCreacion . "</font>",
            "FinalizarAut" => '<a class="red finalizar_aut" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-minus-square bigger-130"/></a>',
        );
    endif;

    $i++;
}

echo json_encode($responce);
