<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
setlocale(LC_TIME, 'es_ES.UTF-8');


$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$columns = array();
$origen = SIMNet::req("origen");

$type = $frm_get["type"];
$id = $frm_get["id"];



$table = "CheckinLaboral";
$key = "IDCheckinLaboral";
$where = " WHERE  " . $table . ".IDClub = '" . SIMUser::get("club") . "' AND ID" . $type . "=" . $id . " AND " . $table . ".Estado = 1";
$script = "chekinlaboral";

// $Usuario = $dbo->fetchAll('Usuario', 'IDUsuario = ' . SIMUser::get('IDUsuario'), 'array');
// if ($Usuario['IDPerfil'] != 1 || $Usuario['IDPerfil'] != 0) {
//     $where .= " AND (S.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " OR U.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " ) ";
// }

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM CheckinLaboral WHERE IDCheckinLaboral = '" . $_POST["id"] . "' LIMIT 1";
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


                case 'HoraInicioLaboral':
                    $where .= " AND (  HoraEntradaEstablecida LIKE '%" . $search_object->data . "%')";
                    break;
                case 'HoraFinalLaboral':
                    $where .= " AND (  HoraSalidaEstablecida LIKE '%" . $search_object->data . "%')";
                    break;
                case 'FechaMovimientoEntrada':
                    $where .= " AND (  FechaMovimientoEntrada LIKE '%" . $search_object->data . "%')";
                    break;
                case 'FechaMovimientoSalida':
                    $where .= " AND (  FechaMovimientoSalida LIKE '%" . $search_object->data . "%')";
                    break;
                case 'ComentarioRevision':
                    $where .= " AND (  ComentarioRevision LIKE '%" . $search_object->data . "%')";
                    break;
                case 'ObservacionEntrada':
                    $where .= " AND (  ObservacionEntrada LIKE '%" . $search_object->data . "%')";
                    break;
                case 'ObservacionSalida':
                    $where .= " AND ( ObservacionSalida LIKE '%" . $search_object->data . "%')";
                    break;
                case 'Registro':
                    $where .= " AND ( UsuarioTrCr LIKE '%" . $search_object->data . "%')";
                    break;
                case 'Estado':
                    if ($search_object->data == "pendiente" || $search_object->data == "Pendiente") {

                        $where .= " AND Estado(  Estado ='1' )";
                    }
                    if ($search_object->data == "aprobado" || $search_object->data == "Aprobado") {

                        $where .= " AND Estado(  Estado ='2' )";
                    }
                    if ($search_object->data == "no Aprobado" || $search_object->data == "No Aprobado") {

                        $where .= " AND Estado(  Estado ='3' )";
                    }
                    break;
                case 'Dia':
                    if ($search_object->data == "lunes" || $search_object->data == "Lunes") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='0'";
                    }
                    if ($search_object->data == "martes" || $search_object->data == "Martes") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='1'";
                    }
                    if ($search_object->data == "miercoles" || $search_object->data == "Miercoles") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='2'";
                    }
                    if ($search_object->data == "jueves" || $search_object->data == "Jueves") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='3'";
                    }
                    if ($search_object->data == "viernes" || $search_object->data == "Viernes") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='4'";
                    }
                    if ($search_object->data == "sabado" || $search_object->data == "Sabado") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='5'";
                    }
                    if ($search_object->data == "domingo" || $search_object->data == "Domingo") {

                        $where .= " AND  WEEKDAY(FechaMovimientoEntrada)='6'";
                    }

                    break;

                case 'qryString':
                    $where .= " AND ( FechaTrCr LIKE '%" . $search_object->data . "%' )";
                    // $where .= " AND ( NombreVisitante LIKE '%" . $search_object->data . "%' OR FechaDeInicio LIKE '%" . $search_object->data . "%' OR FechaFinal LIKE '%"  . $search_object->data . "%' ) ";
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

            $where .= " AND ( Nombres LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
    case "searchDate":
        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];
        if (!empty($FechaInicio) || !empty($FechaFin)) {
            $where .= " AND FechaMovimientoEntrada BETWEEN '" . $FechaInicio . " 00:00:00' AND '" . $FechaFin . " 23:59:59'";
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
$sql = "SELECT  " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
/* echo $sql;
exit; */
$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$dataUsuario = $dbo->fetchAll($type, "ID" . $type . "=" . $id, "array");


$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];
    $Dia = ucfirst(strftime('%A', strtotime($row['FechaMovimientoEntrada'])));
    $n_Dia = date('w', strtotime($row['FechaMovimientoEntrada']));

    $FechaMovimientos = explode(" ", $row['FechaMovimientoEntrada']);
    //hago la consulta para saber si el dia que marco es festivo
    $sql_dia_festivo = "SELECT Fecha FROM Festivos WHERE Fecha='" . $FechaMovimientos[0] . "' AND IDPais='1'";
    $query = $dbo->query($sql_dia_festivo);
    $Dia_festivos = $dbo->fetchArray($query);
    $DiaFestivo = $Dia_festivos;

    if ($row["IDSocio"] > 0) {
        //Horas socio
        /*   $HoraInicioLaboralSocio = $dbo->getFields("Socio", "HoraInicioLaboral", "IDSocio = '" . $row["IDSocio"] . "'");
        $HoraFinalLaboralSocio = $dbo->getFields("Socio", "HoraFinalLaboral", "IDSocio = '" . $row["IDSocio"] . "'");

        $TipoTiempoEntrada = SIMUtil::Tipo_tiempo_laboral($HoraInicioLaboralSocio, $HoraFinalLaboralSocio, $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
        $TipoTiempoSalida = SIMUtil::Tipo_tiempo_laboral($HoraInicioLaboralSocio, $HoraFinalLaboralSocio, $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']); */

        //cambio si se coloca las horas nocturnas
        $TipoTiempoEntrada = SIMUtil::Tipo_tiempo_laboral($row['HoraEntradaEstablecida'], $row['HoraSalidaEstablecida'], $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
        $TipoTiempoSalida = SIMUtil::Tipo_tiempo_laboral($row['HoraEntradaEstablecida'], $row['HoraSalidaEstablecida'], $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
    } elseif ($row["IDUsuario"] > 0) {
        //Horas usuario
        /*   $HoraInicioLaboralUsuario = $dbo->getFields("Usuario", "HoraInicioLaboral", "IDUsuario = '" . $row["IDUsuario"] . "'");
        $HoraFinalLaboralUsuario = $dbo->getFields("Usuario", "HoraFinalLaboral", "IDUsuario = '" . $row["IDUsuario"] . "'");

        $TipoTiempoEntrada = SIMUtil::Tipo_tiempo_laboral($HoraInicioLaboralUsuario, $HoraFinalLaboralUsuario, $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
        $TipoTiempoSalida = SIMUtil::Tipo_tiempo_laboral($HoraInicioLaboralUsuario, $HoraFinalLaboralUsuario, $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']); */



        //cambio si se coloca las horas nocturnas
        $TipoTiempoEntrada = SIMUtil::Tipo_tiempo_laboral($row['HoraEntradaEstablecida'], $row['HoraSalidaEstablecida'], $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
        $TipoTiempoSalida = SIMUtil::Tipo_tiempo_laboral($row['HoraEntradaEstablecida'], $row['HoraSalidaEstablecida'], $row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida']);
    }

    if ($n_Dia == '6' || $n_Dia == '0' || !empty($DiaFestivo["Fecha"])) {
        $TiempoExtraEntrada = SIMUtil::Calcular_diferencia_horas($row['FechaMovimientoEntrada'], $row['FechaMovimientoSalida'], 'Entrada');
        $TiempoExtraSalida = 0;
        $TipoTiempoSalida = 0;
    } else {
        //cambios fabian
        /*   $TiempoExtraEntrada = SIMUtil::Calcular_diferencia_horas($row['HoraInicioLaboral'], $row['FechaMovimientoEntrada'], 'Entrada');
        $TiempoExtraSalida = SIMUtil::Calcular_diferencia_horas($row['HoraFinalLaboral'], $row['FechaMovimientoSalida'], 'Salida'); */

        $TiempoExtraEntrada = SIMUtil::Calcular_diferencia_horas($row['HoraEntradaEstablecida'], $row['FechaMovimientoEntrada'], 'Entrada');

        $TiempoExtraSalida = SIMUtil::Calcular_diferencia_horas($row['HoraSalidaEstablecida'], $row['FechaMovimientoSalida'], 'Salida');
    }

    if ($TiempoExtraEntrada != 0) {
        if (!empty($TipoTiempoEntrada[0])) {
            $TipoExtraEntrada = "Extra diurna";
        } elseif (!empty($TipoTiempoEntrada[1])) {
            $TipoExtraEntrada = "Extra nocturna";
        } elseif (!empty($TipoTiempoEntrada[2])) {
            $TipoExtraEntrada = "Extra diurna s&aacute;bado";
        } elseif (!empty($TipoTiempoEntrada[3])) {
            $TipoExtraEntrada = "Extra nocturna s&aacute;bado";
        } elseif (!empty($TipoTiempoEntrada[4])) {
            $TipoExtraEntrada = "Extra diurna dominical";
        } elseif (!empty($TipoTiempoEntrada[5])) {
            $TipoExtraEntrada = "Extra nocturna dominical";
        } else {
            $TipoExtraEntrada = "";
        }
    } else {
        $TipoExtraEntrada = '';
    }


    if ($TiempoExtraSalida != 0) {
        if (!empty($TipoTiempoSalida[0])) {
            $TipoExtraSalida = "Extra diurna";
        } elseif (!empty($TipoTiempoSalida[1])) {
            $TipoExtraSalida = "Extra nocturna";
        } elseif (!empty($TipoTiempoSalida[2])) {
            $TipoExtraSalida = "Extra diurna s&aacute;bado";
        } elseif (!empty($TipoTiempoSalida[3])) {
            $TipoExtraSalida = "Extra nocturna s&aacute;bado";
        } elseif (!empty($TipoTiempoSalida[4])) {
            $TipoExtraSalida = "Extra diurna domingo";
        } elseif (!empty($TipoTiempoSalida[5])) {
            $TipoExtraSalida = "Extra nocturna domingo";
        } else {
            $TipoExtraSalida = "";
        }
    } else {
        $TipoExtraSalida = '';
    }

    //Dias Festivos
    if (!empty($DiaFestivo["Fecha"])) {
        $TipoExtraEntrada = "Extra Festivo";
        $TipoExtraSalida = "Extra Festivo";
    }
    // $TipoTiempoEntrada = ($TiempoExtraEntrada != '0') ? $TipoTiempoEntrada : '';
    // $TipoTiempoSalida = ($TiempoExtraSalida != '0') ? $TipoTiempoSalida : '';

    if ($TiempoExtraEntrada > "00:00:00" ||  $TiempoExtraSalida > "00:00:00") {

        $disabled = ($TiempoExtraEntrada == '0' && $TiempoExtraSalida == '0') ? 'disabled' : '';

        $btn_incluir = '';
        $Estados = SIMResources::$estado_checkin_laboral;

        foreach ($Estados as $e => $estado) {
            $checkEstado = ($e == $row["Estado"]) ? 'checked' : '';
            $btn_incluir .= '<input type="radio" value="' . $e . '" class="" name="Estado' . $row[$key] . '"  campo="Estado" id="Estado' . $row[$key] . '" ' . $checkEstado . ' ' . $disabled . '>' . $estado . '<br>';
        }
        $btn_incluir .= "<div name='msgupdate" . $row[$key] . "' id='msgupdate" . $row[$key] . "'></div>";
        $htmlComentario = '<textarea id="ComentarioRevision' . $row[$key] . '" name="ComentarioRevision' . $row[$key] . '" cols="12" rows="3" class="col-12 col-xs-12" title="Comentario"  ' . $disabled . '>' . $row["ComentarioRevision"] . '</textarea>';



        $class = "a-edit-modal btnAddReg";
        $attr = "rev=\"reload_grid\"";

        if ($origen <> "mobile")
            $responce->rows[$i]['cell'] = array(
                $key => $row[$key],

                "HoraInicioLaboral" => $row['HoraEntradaEstablecida'],
                "HoraFinalLaboral" => $row['HoraSalidaEstablecida'],
                "FechaMovimientoEntrada" => $row["FechaMovimientoEntrada"],
                "FechaMovimientoSalida" => $row["FechaMovimientoSalida"],
                "Dia" => $Dia,
                "TiempoExtraEntrada" => $TiempoExtraEntrada,
                "TipoTiempoEntrada" => $TipoExtraEntrada,
                "TiempoExtraSalida" => $TiempoExtraSalida,
                "TipoTiempoSalida" => $TipoExtraSalida,
                "Estado" => $btn_incluir,
                "ObservacionEntrada" => $row["ObservacionEntrada"],
                "ObservacionSalida" => $row["ObservacionSalida"],
                "ComentarioRevision" => $htmlComentario,
                "Registro" => $row["UsuarioTrCr"],
                "Guardar" => '<i class="ace-icon fa fa-save bigger-130 blue update_registro"  rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . '/>',
                "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '&idPersona=' . $_GET[id] . '&type=' . $_GET[type] . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
                "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'

            );


        $i++;

        // Resetear las variables
        unset($Dia);
        unset($n_Dia);
        unset($HoraInicioLaboralSocio);
        unset($HoraFinalLaboralSocio);
        unset($HoraInicioLaboralUsuario);
        unset($HoraFinalLaboralUsuario);
        unset($TipoTiempoEntrada);
        unset($TipoTiempoSalida);
        unset($TiempoExtraEntrada);
        unset($TiempoExtraSalida);
        unset($TipoExtraEntrada);
        unset($TipoExtraSalida);
        //Fin Resetear las variables
    }
}

echo json_encode($responce);
