<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Vacuna";
$key = "IDVacuna";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script = "reportevacunacion";

//Condiciones de Busqueda
$condiciones = '';
$tipoReporte = $get["reporte"];

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
                    $condiciones .= " AND ( V.EstoyVacunado LIKE '%" . $search_object->data . "%' OR V.Lugar LIKE '%" . $search_object->data . "%' OR V.FechaCitaVacuna LIKE '" . $search_object->data . "%' OR S.NumeroDocumento LIKE '" . $search_object->data . "%' OR U.NumeroDocumento LIKE '" . $search_object->data . "%' OR S.Nombre LIKE '" . $search_object->data . "%' OR U.Nombre LIKE '" . $search_object->data . "%' OR S.CorreoElectronico LIKE '" . $search_object->data . "%' OR U.Email LIKE '" . $search_object->data . "%' OR S.Telefono LIKE '" . $search_object->data . "%' OR U.Telefono LIKE '" . $search_object->data . "%')  ";
                    break;


                default:
                    switch ($search_object->field) {
                        case 'NumeroDocumento':
                            $condiciones .=  $array_buqueda->groupOp . "  (S.NumeroDocumento LIKE '%" . $search_object->data . "%' OR U.NumeroDocumento LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'Nombre':
                            $condiciones .=  $array_buqueda->groupOp . "  (S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' OR U.Nombre LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'Email':
                            $condiciones .=  $array_buqueda->groupOp . "  (S.Email LIKE '%" . $search_object->data . "%' OR U.Email LIKE '%" . $search_object->data . "%' OR IF(V.IDInvitado>0,U.Email LIKE '%" . $search_object->data . "%','')) ";
                            break;
                        case 'Telefono':
                            $condiciones .=  $array_buqueda->groupOp . "  (S.Telefono LIKE '%" . $search_object->data . "%' OR U.Telefono LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'Vacunado':
                            if ($search_object->data == 'N' || $search_object->data == 'n') {
                                $condiciones .=  $array_buqueda->groupOp . "  (V.Vacunado LIKE '%" . $search_object->data . "%' OR V.Vacunado = '') ";
                            } else {
                                $condiciones .=  $array_buqueda->groupOp . "  (V.Vacunado LIKE '%" . $search_object->data . "%') ";
                            }
                            break;
                        case 'LugarCitaPrimera':
                            $condiciones .=  $array_buqueda->groupOp . "  (V.Lugar LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'FechaPrimeraDosis':
                            $condiciones .=  $array_buqueda->groupOp . "  (V.FechaPrimeraDosis LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'LugarSegundaDosis':
                            $condiciones .=  $array_buqueda->groupOp . "  (V.LugarSegundaDosis LIKE '%" . $search_object->data . "%') ";
                            break;
                        case 'FechaSegundaDosis':
                            $condiciones .=  $array_buqueda->groupOp . "  (V.FechaSegundaDosis LIKE '%" . $search_object->data . "%') ";
                            break;

                        default:
                            $condiciones .=  $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                            break;
                    }

                    break;
            }
        } //end foreach
        break;
}

if (isset($get["estadoVacunados"]) && !is_null($get["estadoVacunados"])) {
    if ($get["estadoVacunados"] == 'N') {
        $condiciones .= " AND V.Vacunado<>'S'";
    } else {
        $condiciones .= " AND V.Vacunado='{$get['estadoVacunados']}'";
    }
}

if (isset($get["fechaInicio"]) && !is_null($get["fechaInicio"])) {
    $fechaInicio = $get["fechaInicio"];
    $fechaFin = $get["fechaFin"];
    $condiciones .= " AND (V.FechaPrimeraDosis BETWEEN '$fechaInicio' AND '$fechaFin' OR V.FechaSegundaDosis BETWEEN '$fechaInicio' AND '$fechaFin')";
}

if (isset($get["citaVacuna"]) && !is_null(($get["citaVacuna"]))) {
    $citaVacuna = $get["citaVacuna"];
    if ($citaVacuna == "ninguna") {
        $condiciones .= " AND V.FechaPrimeraDosis=0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
    } else if ($citaVacuna == "primera") {
        $condiciones .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
    } else if ($citaVacuna == "segunda") {
        $condiciones .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis<>0000-00-00";
    } else if ($citaVacuna == "ambas") {
        $condiciones .= " AND (V.FechaPrimeraDosis<>0000-00-00 OR V.FechaSegundaDosis<>0000-00-00)";
    }
}


if (isset($get["tipoInvitado"]) && !is_null($get["tipoInvitado"])) {
    $tipoInvitado = $get["tipoInvitado"];
    $columnasInvitado = "I.IDInvitado,
    I.IDTipoInvitado,
    I.NumeroDocumento,
    CONCAT(I.Nombre,' ',I.Apellido) AS Nombre,
    I.Email,
     I.Telefono,
    CI.IDClasificacionInvitado,";
    $invitadoJoin = "INNER JOIN Invitado I ON V.IDInvitado=I.IDInvitado
		LEFT JOIN TipoInvitado TI ON I.IDTipoInvitado = TI.IDTipoInvitado
	   LEFT JOIN ClasificacionInvitado CI ON I.IDClasificacionInvitado = CI.IDClasificacionInvitado";
    $validaClub = "I.IDClub";
    $condiciones .= " AND I.IDTipoInvitado=$tipoInvitado ";

    $socioJoin = "";
    $columnas = "";
} else {
    $columnasInvitado = "";
    $invitadoJoin = "";
    $columnas = "V.IDSocio,V.IDUsuario,";
    $socioJoin = " LEFT JOIN Socio S ON V.IDSocio=S.IDSocio";
    $socioJoin .= " LEFT JOIN Usuario U ON V.IDUsuario=U.IDUsuario";
    $validaClub = "S.IDClub";
}


if (isset($get["tipoClasificacionInvitado"]) && !is_null($get["tipoClasificacionInvitado"])) {
    $tipoClasificacionInvitado = $get["tipoClasificacionInvitado"];
    $condiciones .= " AND CI.IDClasificacionInvitado=$tipoClasificacionInvitado ";
}


/* fin con */
if (isset($get["idVacunaMarca"]) && !is_null($get["idVacunaMarca"])) {
    $idMarcaVacuna = $get["idVacunaMarca"];
    $condiciones .= " AND V.IDVacunaMarca=$idMarcaVacuna ";
}

if (isset($get["entidadVacuna"]) && !is_null($get["entidadVacuna"])) {
    $entidad = $get["entidadVacuna"];
    $condiciones .= " AND V.Entidad LIKE '%$entidad%' ";
}
if (
    isset($get["numeroDocumento"]) && !empty($get["numeroDocumento"])
    && empty($get["tipoInvitado"])
) {
    $numeroDocumento = $get["numeroDocumento"];
    if ($get["tipoVacunados"] == "Socio") {
        $tablaVacunado = "S";
        $condiciones .= " AND  $tablaVacunado.NumeroDocumento='$numeroDocumento'";
    } else if ($get["tipoVacunados"] == "Usuario") {
        $tablaVacunado = "U";
        $condiciones .= " AND  $tablaVacunado.NumeroDocumento='$numeroDocumento'";
    } else {
        $condiciones .= " AND  (U.NumeroDocumento='$numeroDocumento' OR S.NumeroDocumento='$numeroDocumento')";
    }
}


/* $array_columnas_socio = array(
    "S.IDSocio" => "",
    "V.Vacunado" => "",
    "V.Entidad AS 'Entidad Vacuna'" => "",
	"VM.Nombre AS 'Marca vacuna'"=>"",
	"VE.Nombre AS 'Entidad vacuna'"=>"",
    "V.LugarCitaPrimera" => "",
    "V.FechaPrimeraDosis" => "",
    "V.FechaSegundaDosis" => "",
    "V.LugarCitaSegunda" => "",
    "S.NumeroDocumento"=> "",
    "S.Nombre"=>"",
    "S.Apellido"=>"",
    "S.Email"=> "",
    "S.Telefono"=> "",
    
); */

/* $array_columnas_usuario = array(
    "U.IDUsuario" => "",
    "V.Vacunado" => "",
    "V.Entidad AS 'Entidad Vacuna'" => "",
	"VM.Nombre AS 'Marca vacuna'"=>"",
	"VE.Nombre AS 'Entidad vacuna'"=>"",
    "V.LugarCitaPrimera" => "",
    "V.FechaPrimeraDosis" => "",
    "V.FechaSegundaDosis" => "",
    "V.LugarCitaSegunda" => "",  
    "U.NumeroDocumento" => "",
    "U.Nombre" => "",
    "U.Email" => "",
    "U.Telefono" => "",    
    
); */


/* $array_columnas_socio = array(
    "S.IDSocio" => "",
    "V.Vacunado" => "",
    "V.Entidad AS 'Entidad Vacuna'" => "",
	"VM.Nombre AS 'Marca vacuna'"=>"",
	"VE.Nombre AS 'Entidad vacuna'"=>"",
    "V.LugarCitaPrimera" => "",
    "V.FechaPrimeraDosis" => "",
    "V.FechaSegundaDosis" => "",
    "V.LugarCitaSegunda" => "",
    "S.NumeroDocumento"=> "",
    "S.Nombre"=>"",
    "S.Apellido"=>"",
    "S.Email"=> "",
    "S.Telefono"=> "",
    
); */

$columReportSocios = implode(",", array_keys($array_columnas_socio));
$columReportUsuarios = implode(",", array_keys($array_columnas_usuario));

// Origen de datos SQL
/* if( $get["tipoVacunados"]=="socio"){ */
$key = "IDSocio";

$sql = "
    SELECT 
    V.Vacunado,
    " . $columnas . "
    " . $columnasInvitado . "
    V.Entidad AS 'Entidad Vacuna',
    VM.Nombre AS 'Marca vacuna',
    VE.Nombre AS 'Entidad vacuna',
    V.Lugar,
    V.FechaPrimeraDosis,
    V.FechaSegundaDosis,
    V.LugarCitaSegunda,V.LugarSegundaDosis
            FROM Vacuna V
            " . $socioJoin . "
            " . $invitadoJoin . "
            LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
            LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
                  WHERE " . $validaClub . "=" . SIMUser::get("club") . " " .
    $condiciones .
    " ORDER BY V.FechaTrCr"




    /* 



    $sql = "
   SELECT $columReportSocios
     FROM Vacuna V
     INNER JOIN Socio S ON V.IDSocio=S.IDSocio
     LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
	 LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
           WHERE S.IDClub=".SIMUser::get("club")." ".
           $condiciones .
           " ORDER BY S.IDSocio" */;


/*    
    $tipovacunado = "Socio"; */

/* } */

/* if($get["tipoVacunados"]=="Usuario"){
    $key = "IDUsuario";
    $sql = "
    SELECT $columReportUsuarios
      FROM Vacuna V
      LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
	  LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
      INNER JOIN Usuario U ON V.IDUsuario=U.IDUsuario
            WHERE U.IDClub=".SIMUser::get("club")." ".
            $condiciones .
            " ORDER BY U.IDUsuario"
        ;

        $tipovacunado = "Usuario"; 
    }

    if($get["tipoVacunados"]=="Estudiante"){
        $key = "IDSocio";
        $sql = "
       SELECT $columReportSocios
         FROM Vacuna V
         INNER JOIN Socio S ON V.IDSocio=S.IDSocio
         LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
         LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
               WHERE S.IDClub=".SIMUser::get("club")." ".
               $condiciones .
               " AND S.TipoSocio = 'Estudiante' ORDER BY S.IDSocio"
            ;
        
        $tipovacunado = "socio";
        
    }  

    if($get["tipoVacunados"]=="Empleado"){
        $key = "IDSocio";
        $sql = "
       SELECT $columReportSocios
         FROM Vacuna V
         INNER JOIN Socio S ON V.IDSocio=S.IDSocio
         LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
         LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
               WHERE S.IDClub=".SIMUser::get("club")." ".
               $condiciones .
               " AND S.TipoSocio = 'Empleado' ORDER BY S.IDSocio"
            ;
        
        $tipovacunado = "Socio";
        
    }   */

$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");

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

//echo $sql;
//exit; 
$sqlQuery = $dbo->query($sql);


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
$responce['page'] = $page;
//$responce['total'] = $total_pages;
$responce['records'] = $count;

$i = 0;
while ($row = $dbo->fetchArray($sqlQuery)) {
    $responce['rows'][$i]['id'] = $row[$key];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($tipovacunado = "socio") {
        $row["Nombre"] = "{$row['Nombre']} {$row['Apellido']}";
    }

    if (!empty($row['IDSocio']) && $row['IDSocio'] != 0) {
        $rowSocio = $dbo->fetchAll('Socio', 'IDSocio=' . $row['IDSocio'], 'array');
        $Perfil = 'socios';
        $row['Nombre'] = $rowSocio['Nombre'] . ' ' . $rowSocio['Apellido'];
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


    $responce['rows'][$i]['cell'] = array(
        $key => $row[$key],
        "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
        "Vacunado" => ($row["Vacunado"] != '') ? $row['Vacunado'] : 'N',
        "Entidad" => $row["Descripcion"],
        "LugarCitaPrimera" => $row["Lugar"],
        "FechaPrimeraDosis" => $row["FechaPrimeraDosis"],
        "LugarSegundaDosis" => $row["LugarSegundaDosis"],
        "FechaSegundaDosis" => $row["FechaSegundaDosis"],
        "NumeroDocumento" => $row["NumeroDocumento"],
        "Nombre" => $row["Nombre"],
        "Email" => $row["Email"],
        "Telefono" => $row["Telefono"],
        "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
    );


    $i++;
}
//var_dump($responce);

echo json_encode($responce);
