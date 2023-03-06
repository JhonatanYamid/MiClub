<?php

include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "Bicicleta";
$key = "IDBicicleta";
$where = " WHERE b.IDClub = '" . SIMUser::get("club") . "' ";
$script = "administrarBicicleta";

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

                    $where .= " AND ( b.Nombre LIKE '%" . $search_object->data . "%' "
                        . "OR s.Nombre LIKE '%" . $search_object->data . "%' "
                        . "OR s.Apellido LIKE '%" . $search_object->data . "%'  "
                        . "OR i.Nombre LIKE '%" . $search_object->data . "%' "
                        . "OR i.Apellido LIKE '%" . $search_object->data . "%' "
                        . "OR si.Nombre LIKE '%" . $search_object->data . "%' "
                        . ")  ";
                    break;

                default:

                    if ($search_object->field == "Socio") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " (s.Nombre LIKE '%" . $search_object->data . "%' "
                            . "OR s.Apellido LIKE '%" . $search_object->data . "%'  "
                            . "OR i.Nombre LIKE '%" . $search_object->data . "%' "
                            . "OR i.Apellido LIKE '%" . $search_object->data . "%' "
                            . "OR si.Nombre LIKE '%" . $search_object->data . "%') ";
                    } else if ($search_object->field == "Nombre") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " b.Nombre LIKE '%" . $search_object->data . "%' ";
                    } else if ($search_object->field == "Codigo") {
                        $where .= $array_buqueda->groupOp . " "
                            .  " b.Codigo LIKE '%" . $search_object->data . "%' ";
                    } else if ($search_object->field == "Estado") {
                        $where .= $array_buqueda->groupOp . " "
                            . " IF(b.Estado=1,'Disponible',IF(b.Estado=2,'En Uso', IF(b.Estado=3,'Entregada', 'Solicitada'))) LIKE '%" . $search_object->data . "%' ";
                    } else {
                        $where .= $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    }

                    break;
            }
        } //end for
        break;
}


//echo $where;exit();

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
$status = " b.Estado = 4 DESC,";
if (!$sidx)
    $sidx = "s.Nombre, s.Apellido";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
    . "FROM $table b "
    . "LEFT JOIN Socio s ON(b.IDSocio = s.IDSocio) "
    . "LEFT JOIN Invitado i ON(b.IDInvitado = i.IDInvitado) "
    . "LEFT JOIN SocioInvitado si ON(b.IDSocioInvitado = si.IDSocioInvitado) "
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

$sql = "SELECT  si.IDSocioInvitado,b.IDInvitado,s.IDSocio,b.IDBicicleta, b.Estado, b.Nombre, b.TipoCodigo, b.Codigo, b.CodigoArchivo, c.Nombre as Lugar,ce.Nombre as LugarEntrega,
            s.Nombre as nombreSocio,s.Apellido as ApellidoSocio, IF(b.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS Socio, 
            b.Localizacion, b.FechaRegistro,b.FechaSolicitud, b.FechaEntrega ,b.IDUsuarioRegistra, b.IDSocioRegistra 
        FROM $table b 
            LEFT JOIN Socio s ON(b.IDSocio = s.IDSocio) 
            LEFT JOIN Invitado i ON(b.IDInvitado = i.IDInvitado) 
            LEFT JOIN SocioInvitado si ON(b.IDSocioInvitado = si.IDSocioInvitado) 
            LEFT JOIN ConfiguracionBicicletaLugar c ON (b.IDConfiguracionBicicletaLugar = c.IDConfiguracionBicicletaLugar) 
            LEFT JOIN ConfiguracionBicicletaLugar ce ON (b.IDConfiguracionBicicletaLugarEntrega = ce.IDConfiguracionBicicletaLugar)
        $where 
        ORDER BY $status $sidx  $sord LIMIT " . $start . "," . $limit;
// echo $sql;
// exit(); 
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
    
    $infoS = $dbo->getFields("Socio", "CONCAT_WS(' ',Nombre, Apellido)", "IDSocio = ".$row['IDSocioRegistra']);
    $infoI = $dbo->getFields("Invitado", "CONCAT_WS(' ',Nombre, Apellido)", "IDInvitado = ".$row['IDSocioRegistra']);
    $infoSi =$dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = ".$row['IDSocioRegistra']);
   
    if(!is_null($infoS) && $infoS != ''){
        $nmSocio = $infoS;
    } 
    else if(!is_null($infoI) && $infoI != ''){
        $nmSocio = $infoI;
    }
    else if(!is_null($infoSi) && $infoSi != ''){
        $nmSocio = $infoSi;
    }

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile") {

        $botones = "";
        $estado = "";

        if ($row["Estado"] == 1) {
            $estado = "Disponible";
            $botones .= '<a class="red eliminar_registro" rel="' . $table . '" title="Eliminar" id="' . $row[$key] . '" lang="' . $script . '" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=edit&id=' . $row[$key] . '" title="Editar"><i class="ace-icon fa fa-check-square-o bigger-130"/></a>&nbsp;';
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Entregar Bicicleta"><i class="ace-icon fa fa-sign-out bigger-130"/></a>&nbsp;';
            //
        }
        else if ($row['Estado'] == 2) {
            $estado = "En campo";
        }
        else if ($row['Estado'] == 3) {
            $estado = "Entregada";
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Recibir Bicicleta"><i class="ace-icon fa fa-mail-reply bigger-130"/></a>&nbsp;';
            $lugarEntrega = $row['LugarEntrega'] ;
            $fechaEntrega = $row['FechaEntrega'] ;
        }
        else if ($row['Estado'] == 4) {
            $estado = "Solicitado por: $nmSocio";
            $botones .= '<a href="' . $script . '.php?action=admin&id=' . $row[$key] . '" title="Entregar Bicicleta"><i class="ace-icon fa fa-sign-out bigger-130"/></a>&nbsp;';
            $lugarSolicitud =$row['Lugar'] ;
            $fechaSolicutud =$row['FechaSolicitud'] ;
        }

        if ($row["CodigoArchivo"] != "") {
            $icono = "fa-barcode";
            if ($row["TipoCodigo"] == 2) $icono = "fa-qrcode";

            $botones .= '<a href="javaScript:void(0);" class="imprimirCodigoBarras" ruta="' . BICICLETA_ROOT . $row["CodigoArchivo"] . '" title="Imprimir codigo"><i class="ace-icon fa ' . $icono . ' bigger-130"/></a>&nbsp;&nbsp;';
        }

        $botones .= '<a href="javaScript:void(0)" class="btnHistorico" title="Ver histórico" bicicleta="' . $row[$key] . '"><i class="ace-icon fa fa-history bigger-130"/></a>&nbsp;&nbsp;';
        
        $sqlPropiedades = "SELECT pb.IDPropiedadesBicicleta, pb.Nombre
                            FROM BicicletaDetalle  as pd, PropiedadesBicicleta as pb
                            WHERE pd.IDPropiedadesBicicleta = pb.IDPropiedadesBicicleta AND pd.IDBicicleta =". $row[$key];
                
        $resultPropiedades = $dbo->query($sqlPropiedades);
        $cont = $dbo->rows($resultPropiedades);
        $nombresProp = "";

        $j=0;
        while($rowPropiedades = $dbo->fetchArray($resultPropiedades)){
            $nombresProp .= $rowPropiedades['Nombre'];
            if($j < $cont-1)
                $nombresProp .= ",";
            
            $j++;
        }
        $botones .= '<a href="javaScript:void(0)" class="btnHistoricoInventario" title="Ver histórico de inventario" bicicleta="'.$row[$key].'" nombresProp="'.$nombresProp.'" ><i class="ace-icon fa fa-trophy"/></a>&nbsp;';
        
        $color_linea = "#31a32f";
        $c_lugarSolicitud =  "";
        $c_fechaSolicutud = "";
        $c_lugarEntrega=  "";
        $c_fechaEntrega = "";
        
        if($row['Estado'] == 3){
            $c_lugarEntrega =  "<font color='" . $color_linea . "'>" .$lugarEntrega. "</font>";
            $c_fechaEntrega = "<font color='" . $color_linea . "'>" . $fechaEntrega. "</font>";

        }else if($row['Estado'] == 4){
            $color_linea = "#F43125"; 
            $c_lugarSolicitud =  "<font color='" . $color_linea . "'>" .$lugarSolicitud. "</font>";
            $c_fechaSolicutud = "<font color='" . $color_linea . "'>" . $fechaSolicutud. "</font>";
        }

        $c_estados =  "<font color='" . $color_linea . "'>" .  $estado . "</font>";
        $c_socio =   "<font color='" . $color_linea . "'>" .   $row['Socio'] . "</font>";
        $c_localizacion  = "<font color='" . $color_linea . "'>" .   $row["Localizacion"] . "</font>";
        $c_codigo = "<font color='" . $color_linea . "'>" .   $row["Codigo"] . "</font>";
        $c_nombre_t = "<font color='" . $color_linea . "'>" .   $row["Nombre"] . "</font>";
        $c_fecha_registro = "<font color='" . $color_linea . "'>" .   $row["FechaRegistro"] . "</font>";

        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Codigo" => $c_codigo,
            "Nombre" => $c_nombre_t,
            "Socio" => $c_socio,
            "Localizacion" => $c_localizacion,
            "FechaRegistro" => $c_fecha_registro,
            "Estado" => $c_estados,
            "LugarSolicitud" => $c_lugarSolicitud,
            "FechaSolicitud" => $c_fechaSolicutud,
            "LugarEntrega" => $c_lugarEntrega ,
            "FechaEntrega" => $c_fechaEntrega,
            "Acciones" => '<center>' . $botones . '</center>',
        );
    }

    $i++;
}



echo json_encode($responce);
