<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "SocioInvitado";
$key = "IDSocioInvitado";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  ";

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
					
					$where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '" . $search_object->data . "%' )  ";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  SocioInvitado." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
			$where .= " AND ( SocioInvitado.NumeroDocumento like '%" . $_GET["qryString"] . "%' )  ";
	break;

	default:
		$where .= " AND FechaIngreso = CURDATE()  ";
	break;

	
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaIngreso'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if(!$sidx) $sidx = "FechaIngreso";
// connect to the database



$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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



//$sql = "SELECT " . $table . ".*, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Socio.IDClub = '" . SIMUser::get("club")  . "' AND Socio.IDSocio = SocioInvitado.IDSocio ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".*FROM " . $table . " " . $where . " AND SocioInvitado.IDClub = '" . SIMUser::get("club")  . "'  Group by NumeroDocumento ORDER BY $key $sord " . $str_limit;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {


	$responce->rows[$i]['id'] = $row[$key];
	
	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" ):
	
	
			$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".SIMUser::get("club")."'" );
			$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".SIMUser::get("club")."'" );
			// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
			$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".SIMUser::get("club")."'" );
			
			
			//Consulto cuantas veces la persona ha sido invitada en el mes 
			$mes_invitacion = date("m");			
			$sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".SIMUser::get("club")."'");
			$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
			//Consulto cuantas personas ha invitado el socio en el mes			
			$sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$row["IDSocio"]."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".SIMUser::get("club")."'");
			
			$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
			$observacion = "";
			if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes ){
				if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes){
					$color_fila = "#000000";		
				}
				else{
					$color_fila = "#EE080C";				
					$observacion = "Supera el max. de:".$numero_invitados_mes_permitido." invitac. mes";
				}
			}
			else{
				$color_fila = "#EE080C";			
				$observacion = "Invitado mas de ".$numero_mismo_invitado_mes." veces en este mes.";
			}
			
			switch($row["Estado"]):
				case "I";
					$estado = "Ya ingreso: " . $row["FechaIngresoClub"];
					$boton_registro_ingreso='';
				break;
				case "P";
					$estado = "Pendiente Ingreso";
					$boton_registro_ingreso='<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id='.$row["IDSocioInvitado"].''.'" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
				break;
			endswitch;
			
			
		$responce->rows[$i]['cell'] = array( 										
										"IDSocioInvitado" => $row["IDSocioInvitado"],
										"Ingreso" => $boton_registro_ingreso,
										"Estado" => "" . $estado ,
										"Socio" => "" . utf8_encode( $row["Socio"] ) ,
										"NumeroDocumento" => "" .$row["NumeroDocumento"],
										"Nombre" => "" .addslashes( $row["Nombre"] ),
										"Obs" => "" .utf8_encode( $row["Observaciones"] ),
										"FechaIngreso" => "" .SIMUtil::tiempo( $row["FechaIngreso"] ),
										"Alerta" =>  $observacion
									);
	else:
		$responce->rows[$i]['cell'] = array( 
										"IDSocioInvitado" => $row["IDSocioInvitado"],
										"Socio" => $row["IDSocio"],
										"NumeroDocumento" => $row["NumeroDocumento"],
										"Nombre" => $row["Nombre"],
										"FechaIngreso" => SIMUtil::tiempo( $row["FechaIngreso"] )
									);
	endif;								

	$i++;
}        

echo json_encode($responce);

?>