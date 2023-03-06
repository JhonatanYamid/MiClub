<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "SocioAutorizacion";
$key = "IDSocioAutorizacion";
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
					
					$where .= " AND ( Invitado.Nombre LIKE '%" . $search_object->data . "%' OR Invitado.Apellido LIKE '%" . $search_object->data . "%' OR Invitado.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '%" . $search_object->data . "%' )  ";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  SocioAutorizacion." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$accion = $_GET["Accion"];
		if(!empty($accion))
			$where .= " AND ( Invitado.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
	break;

	default:
		$where .= " AND FechaInicio = CURDATE()  ";
	break;

	
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaInicio'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if(!$sidx) $sidx = "FechaInicio";
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



//$sql = "SELECT " . $table . ".*, CONCAT( Invitado.Nombre, ' ', Invitado.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Invitado.IDClub = '" . SIMUser::get("club")  . "' AND Invitado.IDSocio = SocioAutorizacion.IDSocio ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM " . $table . " , Invitado I " . $where . " AND I.IDInvitado = SocioAutorizacion.IDInvitado and Salida = 'N' ORDER BY $key $sord " . $str_limit;
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
	
			//// Consulto las reglas que aplica al socio para invitaciones
			$array_datos_regla = SIMUtil::consulta_regla_invitacion($row["IDSocio"],SIMUser::get("club"));
			
			$numero_invitados_mes_permitido = $array_datos_regla["MaximoInvitadoSocio"];
			$numero_invitados_dia_permitido = $array_datos_regla["MaximoInvitadoDia"];
			$numero_mismo_invitado_mes = $array_datos_regla["MaximoRepeticionInvitado"];
			// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
			$cumplimiento_obligatorio_limite = $array_datos_regla["CumplimientoInvitados"];
		
			
			/*
			$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".SIMUser::get("club")."'" );
			$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".SIMUser::get("club")."'" );
			// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
			$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".SIMUser::get("club")."'" );
			*/

	
	
				
			
			//Consulto cuantas veces la persona ha sido invitada en el mes 
			$mes_invitacion = date("m");	
			$dia_invitacion = date("d");			
			$year_invitacion = date("Y");
			$hoy_invitacion = date("Y-m-d");			
			$sql_numero_invitacion = $dbo->query("Select * From SocioAutorizacion Where NumeroDocumento = '".$row["NumeroDocumento"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and Year(FechaInicio) = '".$year_invitacion."'IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
			//Consulto cuantas personas ha invitado el socio en el mes						
			$sql_invitados_mes = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$row["IDSocio"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
			//Consulto cuantas personas ha invitado el socio en el dia									
			$sql_invitados_dia = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$row["IDSocio"]."' and FechaInicio = '".$hoy_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
			
			
				if($row["Ingreso"]=="N"):
					$boton_registro_ingreso='<a class="green btn btn-primary btn-sm" href="#" id="btnrealizarsalida"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';									
				endif;	
		
		
		$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $row["IDInvitado"] . "' ", "array" );	
		$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array" );	
		
			
		$responce->rows[$i]['cell'] = array( 										
										"IDSocioAutorizacion" => $row["IDSocioAutorizacion"],
										"Ingreso" => $boton_registro_ingreso,										
										"NumeroDocumento" => "<font color='$color_fila'>" .$datos_invitado["NumeroDocumento"]. "</font>",
										"Nombre" => "<font color='$color_fila'>" .addslashes( $row["NombreInvitado"] ). " ".$row["IDSocioAutorizacion"] . "</font>",										
										"Tipo" => "<font color='$color_fila'>" .addslashes( $row["TipoAutorizacion"] ). "</font>",										
										"FechaInicio" => "<font color='$color_fila'>" .SIMUtil::tiempo( $row["FechaInicio"] ). "</font>",
										"Socio" => "<font color='$color_fila'>" . utf8_encode( $datos_socio["Nombre"]." " . $datos_socio["Apellido"] ) . "</font>"										
									);
	endif;								

	$i++;
}        

echo json_encode($responce);

?>