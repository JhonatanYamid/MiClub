<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if($frm == null)$frm = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();

$table = "SolicitudViaje";
$key = "IDSolicitudViaje";
$where = " WHERE IDViaje = " . $frm["idViaje"] . " ";
$script = "solicitudesviaje";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {

                case 'Persona':

					$arrIdsUs = [];
					$arrIdsSoc = [];

					//Busca en Usuarios
					$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%".$search_object->data."%') AND IDClub = $IDClub";
					$resUsuario = $dbo->query($sqlUsuario);
					while($rowUsuario = $dbo->fetchArray($resUsuario)) {
						array_push($arrIdsUs, $rowUsuario['IDUsuario']);
					}
					
					$idsUsuario = count($arrIdsUs) > 0 ? implode(",",$arrIdsUs) : 0;

					//Busca en Socios
					$sqlSocio = "SELECT IDSocio FROM Socio WHERE (LOWER(Nombre) LIKE LOWER('%".$search_object->data."%') OR LOWER(Apellido) LIKE LOWER('%".$search_object->data."%')) AND IDClub = $IDClub";
					$resSocio = $dbo->query($sqlSocio);
					while($rowSocio = $dbo->fetchArray($resSocio)) {
						array_push($arrIdsSoc, $rowSocio['IDSocio']);
					}
					
					$idsSocio = count($arrIdsSoc) > 0 ? implode(",",$arrIdsSoc) : 0;

					$where .= " AND (IDUsuario IN($idsUsuario) OR IDSocio IN($idsSocio)) ";	

				break;

                case 'Estado':
                    $where .= "AND IF(Estado = 1,'Por Aprobar', IF(Estado = 2, 'Aprobado', IF(Estado = 3,'Rechazado', 'Cancelado'))) LIKE '%" . $search_object->data . "%' ";
                break;

                case 'Calificacion':

                    $arrCalifica = [];

                    $sqlCalifica = "SELECT IDSolicitudViaje FROM CalificacionCarPool WHERE Calificacion LIKE '%".$search_object->data."%'";
					$resCalifica = $dbo->query($sqlSocio);
					
                    while($rowCalifica = $dbo->fetchArray($resCalifica)) {
						array_push($arrIdsSoc, $rowSocio['IDSocio']);
					}

                    $idsCalifica = count($arrCalifica) > 0 ? implode(",",$arrCalifica) : 0;

                    $where .= "AND IDSolicitudViaje IN($idsCalifica)";
                break;

                case 'MotivosCalificacion':

                    $arrCalifica = [];

                    $sqlCalifica = "SELECT DISTINCT cc.IDSolicitudViaje 
                                    FROM CalificacionCarPool as cc , MotivosCalificacion as mc 
                                    WHERE cc.IDMotivosCalificacion = mc.IDMotivosCalificacion  AND LOWER(mc.Nombre) LIKE LOWER('%".$search_object->data."%')";
					$resCalifica = $dbo->query($sqlSocio);
					
                    while($rowCalifica = $dbo->fetchArray($resCalifica)) {
						array_push($arrIdsSoc, $rowSocio['IDSocio']);
					}

                    $idsCalifica = count($arrCalifica) > 0 ? implode(",",$arrCalifica) : 0;

                    $where .= "AND IDSolicitudViaje IN($idsCalifica)";
                    
                break;

                default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
            }
        }//end for
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx)
    $sidx = "IDSolicitudViaje  ";
// connect to the database

$sqlCount = "SELECT COUNT($key) AS count "
          . "FROM SolicitudViaje ba "
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

$sql = "SELECT IDSolicitudViaje, IDSocio, IDUsuario, Estado, FechaTrCr FROM SolicitudViaje"
        . "$where "
        . "ORDER BY  $sidx $sord LIMIT " . $start . "," . $limit;

$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');

while ($row = $dbo->fetchArray($result)) {
    
    $resultArr = array(); 
    $responce->rows[$i]['id'] = $row[$key];

    if($row['IDSocio'] > 0){
		$persona = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = ".$row['IDSocio']);
	}else{
		$persona = $dbo->getFields("Usuario", "Nombre", "IDUsuario = ".$row['IDUsuario']);
	}

    $fecha = date('d-m-Y h:i a', strtotime($row['FechaTrCr']));

    if($row['Estado'] == 1){
		$color = "orange";
		$estado = "Por Aprobar";
	}
	else if($row['Estado'] == 2){
		$color = "green";
		$estado = "Aprobado";
	}
	else if($row['Estado'] == 3){
		$color = "red";
		$estado = "Rechazado";
	}
	else{
		$color = "grey";
		$estado = "Cancelado";
	}

    $c_estado =  "<font color='$color'>$estado</font>";

    $sqlCal = "SELECT ROUND(AVG(Calificacion)) as Calificacion, GROUP_CONCAT(DISTINCT Nombre ORDER BY Nombre ASC SEPARATOR ',<br>') as MotivosCalificacion
                FROM CalificacionCarPool as cc, MotivosCalificacion as mc 
                WHERE cc.IDMotivosCalificacion = mc.IDMotivosCalificacion AND cc.IDSolicitudViaje = ".$row[$key];
    
    $resultCal = $dbo->query($sqlCal);
    $rowCal = $dbo->fetchArray($resultCal);
    
    $resultArr = [
        $key => $row[$key],
        "Persona" => $persona,
        "FechaTrCr" => $fecha,
        "Estado" => $c_estado,
        "Calificacion" => $rowCal['Calificacion'],
        "MotivosCalificacion" => $rowCal['MotivosCalificacion']
    ];

    $responce->rows[$i]['cell'] = $resultArr;
    $i++;
}
echo json_encode($responce);
?>