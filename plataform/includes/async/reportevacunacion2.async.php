<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$table = "Vacuna2";
$key = "IDVacuna";
$where = " WHERE V.IDClub = '" . SIMUser::get("club") . "' ";
$script = "reportevacunacion2";

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
                    $where .= " AND ( V.EstoyVacunado LIKE '%" . $search_object->data . "%' OR V.Lugar LIKE '%" . $search_object->data . "%' OR V.FechaCitaVacuna LIKE '" . $search_object->data . "%' OR S.NumeroDocumento LIKE '" . $search_object->data . "%' OR U.NumeroDocumento LIKE '" . $search_object->data . "%' OR S.Nombre LIKE '" . $search_object->data . "%' OR U.Nombre LIKE '" . $search_object->data . "%' OR S.CorreoElectronico LIKE '" . $search_object->data . "%' OR U.Email LIKE '" . $search_object->data . "%' OR S.Telefono LIKE '" . $search_object->data . "%' OR U.Telefono LIKE '" . $search_object->data . "%')  ";
                    break;


                default:
                    if ($search_object->field == "NumeroDocumento") {
                        $where .=  $array_buqueda->groupOp . "  (S.NumeroDocumento LIKE '%" . $search_object->data . "%' OR U.NumeroDocumento LIKE '%" . $search_object->data . "%') ";
                    } elseif ($search_object->field == "Nombre") {
                        $where .=  $array_buqueda->groupOp . "  (S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' OR U.Nombre LIKE '%" . $search_object->data . "%') ";
                    } elseif ($search_object->field == "Email") {
                        $where .=  $array_buqueda->groupOp . "  (S.CorreoElectronico LIKE '%" . $search_object->data . "%' OR U.Email LIKE '%" . $search_object->data . "%') ";
                    } elseif ($search_object->field == "Telefono") {
                        $where .=  $array_buqueda->groupOp . "  (S.Telefono LIKE '%" . $search_object->data . "%' OR U.Telefono LIKE '%" . $search_object->data . "%') ";
                    } else {
                        $where .=  $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    }


                    break;
            }
        } //end foreach
        break;
}

//Condiciones de Busqueda
$tipoReporte = $get["reporte"];

if (isset($get["estadoVacunados"]) && !is_null($get["estadoVacunados"])) {
    if ($get["estadoVacunados"] == 'N') {
        $where .= " AND V.EstoyVacunado<>'S'";
    } else {
        $where .= " AND V.EstoyVacunado='{$get['estadoVacunados']}'";
    }
}
if (isset($get["TipoUsuario"]) && !is_null($get["TipoUsuario"])) {
    if ($get["TipoUsuario"] == 'Socio') {
        $where .= " AND V.IDSocio<>'0'";
    } elseif ($get["TipoUsuario"] == 'Usuario') {
        $where .= " AND V.IDUsuario <> '0'";
    }
}
if (empty($get["tipoInvitado"]) && isset($get["TipoSocio"]) && !is_null($get["TipoSocio"])) {
    $where .= " AND S.TipoSocio = '" . $get['TipoSocio'] . "'";
}

if (isset($get["fechaInicio"]) && !is_null($get["fechaInicio"])) {
    $fechaInicio = $get["fechaInicio"];
    $fechaFin = $get["fechaFin"];
    $where .= " AND (V.FechaCitaVacuna BETWEEN '$fechaInicio' AND '$fechaFin')";
}

if (isset($get["citaVacuna"]) && !is_null(($get["citaVacuna"]))) {
    $citaVacuna = $get["citaVacuna"];
    if ($citaVacuna == "ninguna") {
        $where .= " AND V.FechaPrimeraDosis=0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
    } else if ($citaVacuna == "primera") {
        $where .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
    } else if ($citaVacuna == "segunda") {
        $where .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis<>0000-00-00";
    } else if ($citaVacuna == "ambas") {
        $where .= " AND (V.FechaPrimeraDosis<>0000-00-00 OR V.FechaSegundaDosis<>0000-00-00)";
    }
}

if (isset($get["tipoInvitado"]) && !is_null($get["tipoInvitado"])) {
    $tipoInvitado = $get["tipoInvitado"];
    $columnasInvitado = "I.IDInvitado,
    I.IDTipoInvitado,
    I.NumeroDocumento,
    I.Nombre,
    I.Apellido,
    I.Email,
     I.Telefono,
    CI.IDClasificacionInvitado,";
    $invitadoJoin = "INNER JOIN Invitado I ON V.IDInvitado=I.IDInvitado
		LEFT JOIN TipoInvitado TI ON I.IDTipoInvitado = TI.IDTipoInvitado
	   LEFT JOIN ClasificacionInvitado CI ON I.IDClasificacionInvitado = CI.IDClasificacionInvitado";
    $validaClub = "V.IDClub";
    $where .= " AND I.IDTipoInvitado=$tipoInvitado";
} else {
    $columnasInvitado = "";
    $invitadoJoin = "";
    $columnasSocio = "V.IDSocio,V.IDUsuario,S.TipoSocio,";
    $socioJoin = "LEFT JOIN Socio S ON V.IDSocio=S.IDSocio LEFT JOIN Usuario U ON V.IDUsuario=U.IDUsuario";
    $validaClub = "V.IDClub";
    $where .= " AND (S.IDEstadoSocio = 1 || U.Activo = 'S')";
}

if (isset($get["tipoClasificacionInvitado"]) && !is_null($get["tipoClasificacionInvitado"])) {
    $tipoClasificacionInvitado = $get["tipoClasificacionInvitado"];
    $where .= " AND CI.IDClasificacionInvitado=$tipoClasificacionInvitado ";
}
/* fin con */
if (isset($get["idVacunaMarca"]) && !is_null($get["idVacunaMarca"])) {
    $idMarcaVacuna = $get["idVacunaMarca"];
    $vacunaMarca = $dbo->getFields('VacunaMarca', 'Nombre', 'IDVacunaMarca = ' . $idMarcaVacuna);
    $where .= " AND V.Marca like '%" . $vacunaMarca . "%' ";
}
if (isset($get["entidadVacuna"]) && !is_null($get["entidadVacuna"])) {
    $entidad = $get["entidadVacuna"];
    $where .= " AND V.EntidadDosis LIKE '%$entidad%' ";
}

if (isset($get["numeroDocumento"]) && !is_null($get["numeroDocumento"])) {
    $numeroDocumento = $get["numeroDocumento"];
    if ($get["tipoVacunados"] == "Socio") {
        $where .= " AND  S.NumeroDocumento='$numeroDocumento'";
    } else if ($get["tipoVacunados"] == "Usuario") {
        $where .= " AND  U.NumeroDocumento='$numeroDocumento'";
    } else {
        $where .= " AND  (U.NumeroDocumento='$numeroDocumento' OR S.NumeroDocumento='$numeroDocumento')";
    }
}
if (isset($get["Email"]) && !is_null($get["Email"])) {
    $numeroDocumento = $get["numeroDocumento"];
    if ($get["tipoVacunados"] == "Socio") {
        $where .= " AND  S.CorreoElectronico='$numeroDocumento'";
    } else if ($get["tipoVacunados"] == "Usuario") {
        $where .= " AND  U.Email='$numeroDocumento'";
    } else {
        $where .= " AND  (U.Email='$numeroDocumento' OR S.CorreoElectronico='$numeroDocumento')";
    }
}
if (isset($get["Accion"]) && !is_null($get["Accion"])) {
    $Accion = $get["Accion"];
    $where .= " AND  S.Accion='$Accion'";
}

$columReportSocios = implode(",", array_keys($array_columnas_socio));
$columReportUsuarios = implode(",", array_keys($array_columnas_usuario));

// Origen de datos SQL
$key = "IDSocio";
$pagSql = "SELECT COUNT(*) AS count FROM Vacuna2 V LEFT JOIN Socio S ON V.IDSocio=S.IDSocio LEFT JOIN Usuario U ON V.IDUsuario=U.IDUsuario $where";
$result = $dbo->query($pagSql);
$row = $dbo->fetchArray($result);

$count = ($row['count']);

$columns = array();
$origen = SIMNet::req("origen");
$_GET['page'] = intval($_GET['page']);
$page = ($_GET['page'] > 0) ? $_GET['page'] : 1; // get the requested page
$limit = intval($_GET['rows']); // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "V.FechaTrCr";
$responce['page'] = $page;
$responce['records'] = $count;

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
$responce['total'] = $total_pages;

if ($page > $total_pages) $page = $total_pages;

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
    $limit = 1000000;

$sql = "
    SELECT
    IDVacuna, 
    Lugar,
    FechaCitaVacuna,
    V.EstoyVacunado,
    " . $columnasSocio . "
    " . $columnasInvitado . "
    V.EntidadCita AS 'Entidad_Vacuna',
    V.EntidadDosis AS 'Entidad vacuna',
    VM.Nombre AS 'Marca vacuna'
            FROM Vacuna2 V
            " . $socioJoin . "
            " . $invitadoJoin . "
            LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca $where  ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
$sqlQuery = $dbo->query($sql);

$i = 0;
while ($row = $dbo->fetchArray($sqlQuery)) {
    if (!empty($row['IDSocio']) && $row['IDSocio'] != 0) {
        $rowSocio = $dbo->fetchAll('Socio', 'IDSocio=' . $row['IDSocio'], 'array');
        $Perfil = 'socios';
        $row['Nombre'] = $rowSocio['Nombre'];
        $row['Apellido'] = $rowSocio['Apellido'];
        $row['Email'] = $rowSocio['Email'];
        $row['Telefono'] = $rowSocio['Telefono'];
        $row['NumeroDocumento'] = $rowSocio['NumeroDocumento'];
        $email = $rowSocio['CorreoElectronico'];
    } else if (!empty($row['IDUsuario']) && $row['IDUsuario'] != 0) {
        $rowUsuario = $dbo->fetchAll('Usuario', 'IDUsuario=' . $row['IDUsuario'], 'array');
        $Perfil = 'usuarios';
        $row['Nombre'] = $rowUsuario['Nombre'];
        $row['Apellido'] = $rowUsuario['Apellido'];
        $row['Email'] = $rowUsuario['Email'];
        $row['Telefono'] = $rowUsuario['Telefono'];
        $row['NumeroDocumento'] = $rowUsuario['NumeroDocumento'];
        $email = $rowUsuario['Email'];
    }

    $key = ($row['IDSocio'] > 0) ? 'IDSocio' : 'IDUsuario';

    $responce['rows'][$i]['id'] = $row[$key];
    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($tipovacunado = "socio") {
        $row["Nombre"] = "{$row['Nombre']} {$row['Apellido']}";
    }


    $responce['rows'][$i]['cell'] = array(
        $key => $row[$key],
        "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
        "EstoyVacunado" => $row["EstoyVacunado"],
        "TipoSocio" => $row["TipoSocio"],
        "Lugar" => $row["Lugar"],
        "FechaCitaVacuna" => $row["FechaCitaVacuna"],
        "NumeroDocumento" => $row["NumeroDocumento"],
        "Perfil" => $Perfil,
        "Nombre" => $row["Nombre"],
        "Email" => $email,
        "Telefono" => $row["Telefono"],
        "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row['IDVacuna'] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
    );


    $i++;
}
// var_dump($responce);

echo json_encode($responce);
