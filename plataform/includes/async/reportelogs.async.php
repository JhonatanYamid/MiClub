<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$fecha = date('Y-m-j');
$nuevafecha = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
$table = "LogReporte";
$key = "IDLogReporte";
$parametro_club = "\"IDClub\":\"".SIMUser::get("club")."\"";
$where = " WHERE " . $table . ".IDClub = '".SIMUser::get("club")."' and Fecha >= '" . $nuevafecha."'";
$script ="reportelogs";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{	
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'Socio':					
					$where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;
				
				case 'AccionSocio':
					
					$where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%'  )  ";
				break;
				
				default:
					 $where .=   $array_buqueda->groupOp . " " .$search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{			
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%' OR Apellido LIKE '%" . $qryString . "%' )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database


$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . ",Socio S " .$where . " and S.IDSocio=".$table.".IDSocio ORDER BY $sidx $sord");
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

$sql = "SELECT ".$table.".*, S.Nombre as NombreSocio, S.Apellido as ApellidoSocio FROM " . $table . ",Socio S " .$where . " and S.IDSocio=".$table.".IDSocio ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce = "";


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	
	$responce->rows[$i]['id'] = $row[$key];
	
	$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row["IDServicio"] . "'" );
	$nombre_servicio_maestro = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro. "'" );
	
	switch ($row["Accion"]):
		case "setseparareserva";
			$MovimientoLog = "Reserva Separada";
		break;
		case "eliminareservageneral";
			$MovimientoLog = "Reserva Eliminada";
		break;
		case "getsociosclub";
			$MovimientoLog = "Consulta Socios";
		break;
		case "setreservageneral";
			$MovimientoLog = "Solicitud Guardar Reserva";
		break;
		default:
			$MovimientoLog = $row["Accion"];
		
	endswitch;
	

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
										$key => $row[$key],
										"Movimiento" => $MovimientoLog,
										"Socio" => utf8_encode($row["NombreSocio"] . " " . $row["ApellidoSocio"]),
										"AccionSocio" => utf8_encode($dbo->getFields( "Socio" , "Accion" , "IDSocio = '" .$row["IDSocio"] . "'" )),
										"Servicio" => utf8_encode($nombre_servicio_maestro),
										"Respuesta" => utf8_encode($row["Mensaje"]),
										"FechaReserva" => $row["FechaReserva"],
										"FechaPeticion" => $row["Fecha"]
										
									);
	
	

	$i++;


}   



echo json_encode($responce);

?>