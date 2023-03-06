<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "Viaje";
$key = "IDViaje";
$where = " WHERE IDClub = $IDClub ";
$script = "historialviaje";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Viaje WHERE IDViaje = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre";
		$_GET['sord'] = "ASC";

	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
				break;

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

				case 'Origen':

					$club = $dbo->getFields("Club", "Nombre", "IDClub = $IDClub");
					$where .= " AND LOWER(IF(Sentido = 1, Direccion, '$club')) LIKE LOWER('%" . $search_object->data . "%') ";

				break;

				case 'Destino':

					$club = $dbo->getFields("Club", "Nombre", "IDClub = $IDClub");
					$where .= " AND LOWER(IF(Sentido = 1, '$club', Direccion)) LIKE LOWER('%" . $search_object->data . "%') ";
					
				break;

				case 'TipoVehiculo':

					$arrIds = [];

					$sqlTipoVehiculo = "SELECT IDTipoVehiculo FROM TipoVehiculo WHERE LOWER(Nombre) LIKE LOWER('%".$search_object->data."%') AND Publicar = 'S'";
					$resTipoVehiculo = $dbo->query($sqlTipoVehiculo);
					
					while($rowTipoVehiculo = $dbo->fetchArray($resTipoVehiculo)) {
						array_push($arrIds, $rowTipoVehiculo['IDTipoVehiculo']);
					}
					
					$idsTipoVehiculo = count($arrIdsUs) > 0 ? implode(",",$arrIdsUs) : 0;

					$where .= " AND IDTipoVehiculo IN($idsTipoVehiculo) ";	
					
				break;

				case 'Estado':

					$where .= " AND LOWER(IF(Estado = 1, 'Abierto', IF(Estado = 2, 'Cerrado', 'Cancelado'))) LIKE LOWER('%" . $search_object->data . "%') ";
					
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		
		if(!empty($qryString)){

			$arrIdsUs = [];
			$arrIdsSoc = [];

			//Busca en Usuarios
			$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%$qryString%') AND IDClub = $IDClub";
			$resUsuario = $dbo->query($sqlUsuario);
			while($rowUsuario = $dbo->fetchArray($resUsuario)) {
				array_push($arrIdsUs, $rowUsuario['IDUsuario']);
			}
			
			$idsUsuario = count($arrIdsUs) > 0 ? implode(",",$arrIdsUs) : 0;

			//Busca en Socios
			$sqlSocio = "SELECT IDSocio FROM Socio WHERE (LOWER(Nombre) LIKE LOWER('%$qryString%') OR LOWER(Apellido) LIKE LOWER('%$qryString%')) AND IDClub = $IDClub";
			$resSocio = $dbo->query($sqlSocio);
			while($rowSocio = $dbo->fetchArray($resSocio)) {
				array_push($arrIdsSoc, $rowSocio['IDSocio']);
			}
			
			$idsSocio = count($arrIdsSoc) > 0 ? implode(",",$arrIdsSoc) : 0;

			$where .= " AND (IDUsuario IN($idsUsuario) OR IDSocio IN($idsSocio)) ";	
		}

	break;

    case "searchTravel":

        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];
		$IDTipoVehiculo = $frm_get['IDTipoVehiculo'];
		$calificacioninicial = $frm_get['calificacioninicial'];
		$calificacionfinal = $frm_get['calificacionfinal'];
		$IDMotivosCalificacion = $frm_get['IDMotivosCalificacion'];
		$Persona = $frm_get['Persona'];
		$Estado = $frm_get['Estado'];

        if (!empty($FechaInicio) && !empty($FechaFin)) {
            $where .= " AND DATE(Fecha) BETWEEN DATE('$FechaInicio') AND DATE('$FechaFin') ";
        }

		if(!empty($IDTipoVehiculo)){
			$where .= " AND IDTipoVehiculo = $IDTipoVehiculo ";
		}

		if(!empty($calificacioninicial) && !empty($calificacionfinal)){
			$arrIdsCal = [];

			$sqlCal = "SELECT sel.IDViaje, sel.Calificacion
						FROM 
							(SELECT sv.IDViaje, ROUND(AVG(cc.Calificacion),0) as Calificacion
							FROM CalificacionCarPool as cc, SolicitudViaje as sv
							WHERE cc.IDSolicitudViaje = sv.IDSolicitudViaje
							GROUP BY cc.Calificacion) as sel
						WHERE sel.Calificacion BETWEEN $calificacioninicial AND $calificacionfinal ";

			$resultCal = $dbo->query($sqlCal);
			while($rowCal = $dbo->fetchArray($resultCal)) {
				array_push($arrIdsCal, $rowCal['IDViaje']);
			}
			
			$idsCal = count($arrIdsCal) > 0 ? implode(",",$arrIdsCal) : 0;

			$where .= " AND IDViaje IN($idsCal) ";
		}

		if(!empty($IDMotivosCalificacion)){
			$arrIdsMot = [];
		
			$sqlMot = "SELECT sv.IDViaje
						FROM CalificacionCarPool as cc, SolicitudViaje as sv
						WHERE 
							cc.IDSolicitudViaje = sv.IDSolicitudViaje AND 
							cc.IDMotivosCalificacion = $IDMotivosCalificacion";
		
			$resultMot = $dbo->query($sqlMot);
			while($rowMot = $dbo->fetchArray($resultMot)) {
				array_push($arrIdsMot, $rowMot['IDViaje']);
			}
			
			$idsMot = count($arrIdsMot) > 0 ? implode(",",$arrIdsMot) : 0;
		
			$where .= " AND IDViaje IN($idsMot) ";
		}

		if(!empty($Persona)){
			$arrIdsUs = [];
			$arrIdsSoc = [];

			//Busca en Usuarios
			$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%$Persona%') AND IDClub = $IDClub";
			$resUsuario = $dbo->query($sqlUsuario);
			while($rowUsuario = $dbo->fetchArray($resUsuario)) {
				array_push($arrIdsUs, $rowUsuario['IDUsuario']);
			}
			
			$idsUsuario = count($arrIdsUs) > 0 ? implode(",",$arrIdsUs) : 0;

			//Busca en Socios
			$sqlSocio = "SELECT IDSocio FROM Socio WHERE (LOWER(Nombre) LIKE LOWER('%$Persona%') OR LOWER(Apellido) LIKE LOWER('%$Persona%')) AND IDClub = $IDClub";
			$resSocio = $dbo->query($sqlSocio);
			while($rowSocio = $dbo->fetchArray($resSocio)) {
				array_push($arrIdsSoc, $rowSocio['IDSocio']);
			}
			
			$idsSocio = count($arrIdsSoc) > 0 ? implode(",",$arrIdsSoc) : 0;

			$where .= " AND (IDUsuario IN($idsUsuario) OR IDSocio IN($idsSocio)) ";	
		}

		if(!empty($Estado)){
			$where .= " AND Estado = $Estado ";
		}

		
    break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Fecha";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where);
$row = $dbo->fetchArray($result);
$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


$sql = "SELECT IDViaje, IDClub, IDSocio, IDUsuario, IDTipoVehiculo, Fecha, Hora, LugarEncuentro, Sentido, Direccion, 
			   CuposTotales, CuposDisponibles, Modelo, Color, ValorCupo, Estado
		FROM Viaje
		$where ORDER BY $sidx $sord $str_limit";

$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];

	if($row['IDSocio'] > 0){
		$persona = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = ".$row['IDSocio']);
	}else{
		$persona = $dbo->getFields("Usuario", "Nombre", "IDUsuario = ".$row['IDUsuario']);
	}

	$club = $dbo->getFields("Club", "Nombre", "IDClub = ".$row['IDClub']);
	$origen = $row['Sentido'] == 1 ? $row['Direccion'] : $club;
	$destino = $row['Sentido'] == 1 ? $club : $row['Direccion'];

	$fecha = date('d-m-Y', strtotime($row['Fecha']));
	$hora = date('h:i a', strtotime($row['hora'])); 

	$tipovehiculo = $dbo->getFields("TipoVehiculo", "Nombre", "IDTipoVehiculo = ".$row['IDTipoVehiculo']);

	if($row['Estado'] == 1){
		$color = "orange";
		$estado = "Abierto";
	}
	else if($row['Estado'] == 2){
		$color = "green";
		$estado = "Cerrado";
	}
	else{
		$color = "red";
		$estado = "Cancelado";
	}

	$c_estado =  "<font color='$color'>$estado</font>";

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Persona" => $persona,
			"Origen" => $origen,
			"Destino" => $destino,
			"Fecha" => $fecha,
			"Hora" => $hora,
			"LugarEncuentro" => $row["LugarEncuentro"],
			"TipoVehiculo" => $tipovehiculo,
			"CuposTotales" => $row["CuposTotales"],
			"CuposDisponibles" => $row["CuposDisponibles"],
			"ValorCupo" => $row["ValorCupo"],
			"Estado" => $c_estado,
			"Accion" => '<a href="javaScript:void(0)" class="btnSolicitudes" title="Ver solicitudes" viaje="'.$row[$key].'" ><i class="ace-icon fa fa-list-ol"/></a>'
		);

	$i++;
	
}        

echo json_encode($responce);

?>