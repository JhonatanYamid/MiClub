<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);


$IDUsuario = SIMUser::get("IDUsuario");
$columns = array();
$origen = SIMNet::req("origen");

$table = "AuxiliosInfinitoSolicitud";
$tableJoin = " LEFT JOIN Usuario U ON AuxiliosInfinitoSolicitud.IDUsuario = U.IDUsuario ";
$tableJoin .= " LEFT JOIN Socio S ON AuxiliosInfinitoSolicitud.IDSocio = S.IDSocio ";
$tableJoin .= " INNER JOIN AuxiliosInfinito A ON AuxiliosInfinitoSolicitud.IDAuxiliosInfinito = A.IDAuxiliosInfinito ";
$key = "IDAuxiliosInfinitoSolicitud";
$IDModulo = 145;
$arrIDClubLuker = [95, 96, 97, 98, 122];

$script = "tiempoparamisolicitud";

// Validacion Club para mostrar las solicitudes de los negocios de luker
if (in_array(SIMUser::get('club'), $arrIDClubLuker)) {
    $NegociosLuker = implode(',', $arrIDClubLuker);
    $validaIDClub = " AuxiliosInfinitoSolicitud.IDClub in (" . $NegociosLuker . ") ";
} else {
    $validaIDClub = " AuxiliosInfinitoSolicitud.IDClub = '" . SIMUser::get("club") . "' ";
}
// Fin Validacion Club para mostrar las solicitudes de los negocios de luker

$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
if ($datos_usuario['IDPerfil'] != 1 && $datos_usuario['IDPerfil'] != 0) {

    // $Jefe = $dbo->getFields('Socio', 'IDSocio', "DocumentoJefe = " . $datos_usuario['NumeroDocumento']);
    $sql_Jefe = "SELECT * FROM Socio WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'];
    $q_Jefe = $dbo->query($sql_Jefe);
    $n_Jefe = $dbo->rows($q_Jefe);

    // $Especialista = $dbo->getFields('Socio', 'IDSocio', "DocumentoEspecialista = " . $datos_usuario['NumeroDocumento']);
    $sql_Especialista = "SELECT * FROM Socio WHERE IDClub = '" . SIMUser::get("club") . "' AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'];
    $q_Especialista = $dbo->query($sql_Especialista);
    $n_Especialista = $dbo->rows($q_Especialista);

    if ($n_Jefe > 0) {
        $where .= " WHERE AuxiliosInfinitoSolicitud.IDEstado NOT IN (3,2) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "') AND $validaIDClub and AuxiliosInfinitoSolicitud.IDModulo='" . $IDModulo . "'  ";
    } else {
        // Cambiar club para luker
        if (in_array(SIMUser::get('club'), $arrIDClubLuker) || SIMUser::get('club') == 169) {
            $where .= " WHERE AuxiliosInfinitoSolicitud.IDEstado NOT IN (1,2,3) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "') AND $validaIDClub and AuxiliosInfinitoSolicitud.IDModulo='" . $IDModulo . "'  ";
        } else {
            $where .= " WHERE AuxiliosInfinitoSolicitud.IDEstado NOT IN (2,3) AND (U.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoJefe = '" . $datos_usuario['NumeroDocumento'] . "' OR S.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "' OR U.DocumentoEspecialista = '" . $datos_usuario['NumeroDocumento'] . "') AND $validaIDClub and AuxiliosInfinitoSolicitud.IDModulo='" . $IDModulo . "'  ";
        }
    }
} else {
    $where = " WHERE $validaIDClub  and AuxiliosInfinitoSolicitud.IDModulo='" . $IDModulo . "' ";
}
//

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM AuxiliosInfinitoSolicitud WHERE IDAuxiliosInfinitoSolicitud = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Comentarios";
        $_GET['sord'] = "ASC";


        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);

        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'Nombre':
                    $where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR U.Nombre LIKE '%" . $search_object->data . "%' )";
                    $where .= " OR ( S.Apellido LIKE '%" . $search_object->data . "%' )";
                    break;
                case 'Tiempo':
                    $where .= " AND ( A.Nombre LIKE '%" . $search_object->data . "%' )";
                    break;
                case 'AuxilioRechazo':
                    $q_AuxiliosInfinitoRechazo = $dbo->query("select IDAuxiliosInfinitoRechazo from AuxiliosInfinitoRechazo where Nombre like '%" . $search_object->data . "%' order by Nombre limit 1");

                    $row_AuxiliosInfinitoRechazo = $dbo->rows($q_AuxiliosInfinitoRechazo);
                    if ($row_AuxiliosInfinitoRechazo > 0) {
                        $IDAuxiliosInfinitoRechazo = $dbo->assoc($q_AuxiliosInfinitoRechazo);
                        $where .= " AND ( AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoRechazo = " . $IDAuxiliosInfinitoRechazo['IDAuxiliosInfinitoRechazo'] . " )";
                    }

                    break;
                case 'Estado':
                    $Estado = ucwords($search_object->data);
                    $key_EstadoAux = array_search($Estado, SIMResources::$EstadoAuxilio);
                    if ($key_EstadoAux) {
                        $where .= " AND ( AuxiliosInfinitoSolicitud.IDEstado = $key_EstadoAux )";
                    }
                    break;
                case 'FechaSolicitud':
                    $where .= " AND ( AuxiliosInfinitoSolicitud.FechaTrCr like '%" . $search_object->data . "%' )";
                    break;
                case 'qryString':
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $q_AuxiliosInfinitoRechazo = $dbo->query("select IDAuxiliosInfinitoRechazo from AuxiliosInfinitoRechazo where Nombre like '%" . $qryString . "%' order by Nombre limit 1");

            $row_AuxiliosInfinitoRechazo = $dbo->rows($q_AuxiliosInfinitoRechazo);
            if ($row_AuxiliosInfinitoRechazo > 0) {
                $IDAuxiliosInfinitoRechazo = $dbo->assoc($q_AuxiliosInfinitoRechazo);
                $whereAuxiliosInfinitoRechazo .= " OR  AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoRechazo = " . $IDAuxiliosInfinitoRechazo['IDAuxiliosInfinitoRechazo'];
            }

            $Estado = ucwords($qryString);
            $key_EstadoAux = array_search($Estado, SIMResources::$EstadoAuxilio);
            if ($key_EstadoAux) {
                $whereEstadoAux .= " OR AuxiliosInfinitoSolicitud.IDEstado = $key_EstadoAux ";
            }

            $where .= " AND(
                S.Nombre LIKE '%" . $qryString . "%' OR U.Nombre LIKE '%" . $qryString . "%'
                OR S.Apellido LIKE '%" . $qryString . "%'
                OR A.Nombre LIKE '%" . $qryString . "%' 
                OR AuxiliosInfinitoSolicitud.FechaTrCr like '%" . $qryString . "%'
$whereAuxiliosInfinitoRechazo
$whereEstadoAux
                
            )";
        } //end if
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "IDAuxiliosInfinitoSolicitud";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
    $limit = 1000000;



$sql = "SELECT " . $table . ".* FROM " . $table . $tableJoin . $where . " ORDER BY $sidx $sord " . $str_limit;
//exit;
$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    // Validar si es un Usuario o un Socio
    if ($row["IDUsuario"] > 0) {
        $sqlUsuario = "SELECT Nombre FROM Usuario WHERE IDUsuario = " . $row['IDUsuario'];
        $queryUsuario = $dbo->query($sqlUsuario);
        $rowUser = $dbo->assoc($queryUsuario);
    } else {
        $sqlSocio = "SELECT Nombre,Apellido FROM Socio WHERE IDSocio = " . $row['IDSocio'];
        $querySocio = $dbo->query($sqlSocio);
        $rowUser = $dbo->assoc($querySocio);
        $rowUser['Nombre'] = $rowUser['Nombre'] . ' ' . $rowUser['Apellido'];
    }

    // Obtener nombre del AuxiliosRechazo
    $sqlAuxiliosRechazo = "SELECT * FROM AuxiliosInfinitoRechazo WHERE IDAuxiliosInfinitoRechazo = " . $row['IDAuxiliosInfinitoRechazo'];
    $queryAuxiliosRechazo = $dbo->query($sqlAuxiliosRechazo);
    $rowAuxiliosRechazo = $dbo->assoc($queryAuxiliosRechazo);

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Nombre" => $rowUser['Nombre'],
            "Tiempo" => $dbo->getFields('AuxiliosInfinito', 'Nombre', 'IDAuxiliosInfinito = "' . $row['IDAuxiliosInfinito'] . '"'),
            "AuxilioRechazo" => $rowAuxiliosRechazo["Nombre"],
            "Estado" => SIMResources::$EstadoAuxilio[$row["IDEstado"]],
            "FechaSolicitud" => $row["FechaTrCr"],
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
        );
    else
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<div class="hidden-sm hidden-xs action-buttons"><a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a></div>',
            "Nombre" => $rowUser['Nombre'],
            "Tiempo" => $dbo->getFields('AuxiliosInfinito', 'Nombre', 'IDAuxiliosInfinito = "' . $row['IDAuxiliosInfinito'] . '"'),
            "AuxilioRechazo" => $rowAuxiliosRechazo["Nombre"],
            "Estado" => SIMResources::$EstadoAuxilio[$row["IDEstado"]],
            "FechaSolicitud" => $row["FechaTrCr"],
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
        );

    $i++;
}
echo json_encode($responce);
