<?php

include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );
$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "Talega";
$key = "IDTalega";
$where = "WHERE t.Activo = 'S' AND t.IDClub = '" . SIMUser::get("club") . "' ";
$script = "administrarTalega";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {
    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {

            switch ($search_object->field) {
                case 'qryString':
                    $valorData = $search_object->data;
                    $where .= " AND ( t.nombre LIKE '%$valorData%' OR s.Nombre LIKE '%$valorData%' OR s.Apellido LIKE '%$valorData%' OR i.Nombre LIKE '%$valorData%' OR i.Apellido LIKE '%$valorData%' OR si.Nombre LIKE '%$valorData%' OR t.codigo LIKE '%$valorData%' OR s.Accion LIKE '%$valorData%' OR s.AccionPadre LIKE '%$valorData%' ) ";

                    break;

                default:

                    if ($search_object->field == "socio") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " (s.Nombre LIKE '%" . $search_object->data . "%' "
                            . "OR s.Apellido LIKE '%" . $search_object->data . "%'  "
                            . "OR i.Nombre LIKE '%" . $search_object->data . "%' "
                            . "OR i.Apellido LIKE '%" . $search_object->data . "%' "
                            . "OR si.Nombre LIKE '%" . $search_object->data . "%') ";
                    } else if ($search_object->field == "nombre") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " t.nombre LIKE '%" . $search_object->data . "%' ";
                    } else if ($search_object->field == "codigo") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " t.codigo LIKE '%" . $search_object->data . "%' ";
                    } else if ($search_object->field == "estado") {
                        $where .= $array_buqueda->groupOp . " " . " IF(t.estado=1,'Disponible',IF(t.estado=2,'En campo', IF(t.estado=3,'Entregada', 'Solicitada'))) LIKE '%" . $search_object->data . "%' ";
                    } else {
                        $where .= $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    }

                    break;
            }
        } //end for
        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $valorData = $search_object->data;
            $where .= " AND ( t.nombre LIKE '%$qryString%' OR s.Nombre LIKE '%$qryString%' OR s.Apellido LIKE '%$qryString%' OR i.Nombre LIKE '%$qryString%' OR i.Apellido LIKE '%$qryString%' OR si.Nombre LIKE '%$qryString%' OR t.codigo LIKE '%$qryString%' OR s.Accion LIKE '%$qryString%' OR s.AccionPadre LIKE '%$qryString%') ";
        } //end if
        break;

    case "autocomplete":
        $qryString = SIMNet::req("qryString");
        $tipo = SIMNet::req("tipo");

        switch ($tipo) {
            case "socioInvitado":
                $sqlSocioInvitado = "SELECT 
                                    IF(si.IDInvitado IS NULL, i.IDInvitado, si.IDSocioInvitado) as Id,
                                    IF(si.IDInvitado IS NULL, CONCAT(i.Nombre,' ',i.Apellido), si.Nombre) as Nombre,
                                    IF(si.IDInvitado IS NULL, '2', '1') as Tipo
                                FROM Invitado as i 
                                    LEFT JOIN SocioInvitado as si ON si.IDInvitado = i.IDInvitado
                                WHERE i.IDClub = $IDClub AND (
                                    LOWER(si.NumeroDocumento) LIKE LOWER('%$qryString%')  OR
                                    LOWER(si.Nombre) LIKE LOWER('%$qryString%')  OR
                                    LOWER(i.Nombre) LIKE LOWER('%$qryString%')  OR
                                    LOWER(i.Apellido) LIKE LOWER('%$qryString%')  OR
                                    LOWER(i.NumeroDocumento) LIKE LOWER('%$qryString%'))";

                $qrySocioInvitado = $dbo->query($sqlSocioInvitado);

                while ($rSocioInvitado = $dbo->fetchArray($qrySocioInvitado)) {
                    $arrayRes[] = $rSocioInvitado;
                }

                echo json_encode($arrayRes);
                exit;
                break;
        }

        break;
}

//echo $where;exit();

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
$status = " t.estado = 4 DESC,";
if (!$sidx)
    $sidx = "s.Nombre";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
    . "FROM $table t "
    . "LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) "
    . "LEFT JOIN Invitado i ON(t.IDInvitado = i.IDInvitado) "
    . "LEFT JOIN SocioInvitado si ON(t.IDSocioInvitado = si.IDSocioInvitado) "
    . "$where  ";

$result = $dbo->query($sqlCount);
$row =  $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages)
    $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)
if (empty($limit))
    $limit = 1000000;
/*  $sidx $sord   */
//$limit = 58;

$sql = "SELECT  si.IDSocioInvitado,t.IDInvitado,s.IDSocio,t.IDTalega, t.estado, t.nombre, t.tipoCodigo, t.codigo, t.codigoArchivo, c.Nombre,ce.Nombre as LugarEntrega,
                 s.Nombre as nombreSocio,s.Apellido as ApellidoSocio, IF(t.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS socio, 
                 t.localizacion, t.fechaRegistro,t.FechaSolicitud, t.FechaEntrega ,t.idUsuarioRegistra, t.IdSocioRegistra 
        FROM $table t 
            LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) 
            LEFT JOIN Invitado i ON(t.IDInvitado = i.IDInvitado) 
            LEFT JOIN SocioInvitado si ON(t.IDSocioInvitado = si.IDSocioInvitado) 
            LEFT JOIN ConfiguracionTalegasLugar c ON (t.IDConfiguracionTalegasLugar = c.IDConfiguracionTalegasLugar) 
            LEFT JOIN ConfiguracionTalegasLugar ce ON (t.IDConfiguracionTalegasLugarEntrega = ce.IDConfiguracionTalegasLugar)
        $where 
        ORDER BY $status $sidx  $sord LIMIT " . $start . "," . $limit;
// echo $sql;
// exit;
$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row[$key];

    $nmSocio = "";

    $infoS = $dbo->getFields("Socio", "CONCAT_WS(' ',Nombre, Apellido)", "IDSocio = " . $row['IdSocioRegistra']);
    $infoI = $dbo->getFields("Invitado", "CONCAT_WS(' ',Nombre, Apellido)", "IDInvitado = " . $row['IdSocioRegistra']);
    $infoSi = $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = " . $row['IdSocioRegistra']);

    if (!is_null($infoS) && $infoS != '') {
        $nmSocio = $infoS;
    } else if (!is_null($infoI) && $infoI != '') {
        $nmSocio = $infoI;
    } else if (!is_null($infoSi) && $infoSi != '') {
        $nmSocio = $infoSi;
    }

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile") {

        $botones = "";
        $estado = "";

        if ($row["estado"] == 1) {
            $estado = "Disponible";
            $botones .= '<a class="red eliminar_registro" rel="' . $table . '" title="Eliminar" id="' . $row[$key] . '" lang="' . $script . '" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=edit&id=' . $row[$key] . '" title="Editar"><i class="ace-icon fa fa-check-square-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Entregar Talega"><i class="ace-icon fa fa-sign-out bigger-130"/></a>&nbsp;';
            //
        } else if ($row['estado'] == 2) {
            $estado = "En campo";
        } else if ($row['estado'] == 3) {
            $estado = "Entregada";
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Recibir Talega"><i class="ace-icon fa fa-mail-reply bigger-130"/></a>&nbsp;';
            $lugarEntrega = $row['LugarEntrega'];
            $fechaEntrega = $row['FechaEntrega'];
        } else if ($row['estado'] == 4) {
            $estado = "Solicitado por: $nmSocio";
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Entregar Talega"><i class="ace-icon fa fa-sign-out bigger-130"/></a>&nbsp;';
            $lugarSolicitud = $row['Nombre'];
            $fechaSolicutud = $row['FechaSolicitud'];
        } else if ($row['estado'] == 5) {
            $estado = "Pendiente de Confirmación";
            $botones .= '<a class="red eliminar_registro" rel="' . $table . '" title="Eliminar" id="' . $row[$key] . '" lang="' . $script . '" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=edit&id=' . $row[$key] . '" title="Editar"><i class="ace-icon fa fa-check-square-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Entregar Talega"><i class="ace-icon fa fa-sign-out bigger-130"/></a>&nbsp;';
        }

        if ($row["codigoArchivo"] != "") {
            $icono = "fa-barcode";
            if ($row["tipoCodigo"] == 2) $icono = "fa-qrcode";

            $botones .= '<a href="javaScript:void(0);" class="imprimirCodigoBarras" ruta="' . TALEGA_ROOT . $row["codigoArchivo"] . '" title="Imprimir codigo"><i class="ace-icon fa ' . $icono . ' bigger-130"/></a>&nbsp;';
        }

        $botones .= '<a href="javaScript:void(0)" class="btnHistorico" title="Ver histórico" talega="' . $row[$key] . '"><i class="ace-icon fa fa-history bigger-130"/></a>&nbsp;';

        $sqlPropiedades = "SELECT pt.IDPropiedadesTalega, pt.nombre
                            FROM TalegaDetalle  as td
                                LEFT JOIN PropiedadesTalega as pt ON td.IDPropiedadesTalega = pt.IDPropiedadesTalega
                            WHERE td.IDTalega =" . $row[$key];

        $resultPropiedades = $dbo->query($sqlPropiedades);
        $cont = $dbo->rows($resultPropiedades);
        $nombresProp = "";
        $j = 0;
        while ($rowPropiedades = $dbo->fetchArray($resultPropiedades)) {
            $nombresProp .= $rowPropiedades['nombre'];
            if ($j < $cont - 1) {
                $nombresProp .= ",";
            }
            $j++;
        }
        $botones .= '<a href="javaScript:void(0)" class="btnHistoricoInventario" title="Ver histórico de inventario" talega="' . $row[$key] . '" nombresProp="' . $nombresProp . '" ><i class="ace-icon fa fa-trophy"/></a>&nbsp;';

        $color_linea = "#31a32f";
        $c_lugarSolicitud =  "";
        $c_fechaSolicutud = "";
        $c_lugarEntrega =  "";
        $c_fechaEntrega = "";

        if ($row['estado'] == 3) {
            $c_lugarEntrega =  "<font color='" . $color_linea . "'>" . $lugarEntrega . "</font>";
            $c_fechaEntrega = "<font color='" . $color_linea . "'>" . $fechaEntrega . "</font>";
        } else if ($row['estado'] == 4) {
            $color_linea = "#F43125";
            $c_lugarSolicitud =  "<font color='" . $color_linea . "'>" . $lugarSolicitud . "</font>";
            $c_fechaSolicutud = "<font color='" . $color_linea . "'>" . $fechaSolicutud . "</font>";
        }

        $c_estados =  "<font color='" . $color_linea . "'>" .  $estado . "</font>";
        $c_socio =   "<font color='" . $color_linea . "'>" .   $row['socio'] . "</font>";
        $c_localizacion  = "<font color='" . $color_linea . "'>" .   $row["localizacion"] . "</font>";
        $c_codigo = "<font color='" . $color_linea . "'>" .   $row["codigo"] . "</font>";
        $c_nombre_t = "<font color='" . $color_linea . "'>" .   $row["nombre"] . "</font>";
        $c_fecha_registro = "<font color='" . $color_linea . "'>" .   $row["fechaRegistro"] . "</font>";

        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "codigo" => $c_codigo,
            "nombre" => $c_nombre_t,
            "socio" => $c_socio,
            "localizacion" => $c_localizacion,
            "fechaRegistro" => $c_fecha_registro,
            "estado" => $c_estados,
            "lugarSolicitud" => $c_lugarSolicitud,
            "fechaSolicitud" => $c_fechaSolicutud,
            "lugarEntrega" => $c_lugarEntrega,
            "fechaEntrega" => $c_fechaEntrega,
            "Acciones" => '<center>' . $botones . '</center>',
        );
    }

    $i++;
}

echo json_encode($responce);
