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

if(!empty($_GET["DocumentoSocio"])){
	$array_where [] = "S.NumeroDocumento = '".$_GET["DocumentoSocio"]."'";
}

if(!empty($_GET["NombreSocio"])){
	$array_where [] = "S.Nombre like  '%".$_GET["NombreSocio"]."%'";
}

if(!empty($_GET["AccionSocio"])){
	$array_where [] = "S.Accion like  '%".$_GET["AccionSocio"]."%'";
}


if(!empty($_GET["ApellidoSocio"])){
	$array_where [] = "S.Apellido like '%".$_GET["ApellidoSocio"]."%'";
}

if(!empty($_GET["DocumentoContratista"])){
	$array_where [] = "I.NumeroDocumento = '".$_GET["DocumentoContratista"]."'";
}

if(!empty($_GET["NombreContratista"]) || !empty($_GET["ApellidoContratista"])){	
	if(!empty($_GET["NombreContratista"]))
		$array_condicion_nombre[] = " I.Nombre like '%".$_GET["NombreContratista"]."%'";
		
	if(!empty($_GET["ApellidoContratista"]))
		$array_condicion_nombre[] = " I.Apellido like '%".$_GET["ApellidoContratista"]."%'";	
		
	if(count($array_condicion_nombre)>0)	:
		$array_where [] = " ( " . implode(" and ", $array_condicion_nombre) . " ) ";
	endif;
		
	
	//$array_where [] = " (I.Nombre like '%".$_GET["NombreContratista"]."%' or I.Apellido like '%".$_GET["ApellidoContratista"]."%') ";
}


if(!empty($_GET["PlacaContratista"])){
	$sql_placa = "Select * From LogAcceso Where Mecanismo like '%".$_GET["PlacaContratista"]."%' Order by IDLogAcceso Desc";
	$r_placa = $dbo->query($sql_placa);
	while($row_placa = $dbo->fetchArray($r_placa)):
		$array_id_invitacion [$row_placa["IDInvitacion"]] = $row_placa["IDInvitacion"];
	endwhile;
	if(count($array_id_invitacion)>0):
		$id_invitacion = implode(",",$array_id_invitacion);
	endif;	
	$array_where [] = " (  SocioAutorizacion.IDSocioAutorizacion in (".$id_invitacion.")  )  ";	
	//si la busqueda es placa no tomo en cuent las fechas
	$_GET["FechaInicio"]="2016-01-01"; 
	$_GET["FechaFin"]="2020-01-01";
}

if(!empty($_GET["PredioContratista"])){
	$array_where [] = "I.Predio like '%".$_GET["PredioContratista"]."%'";
}

if(!empty($_GET["LicenciaConduccion"])){
	$array_where [] = "I.Licencia = '".$_GET["LicenciaConduccion"]."'";
}

if(!empty($_GET["IDTipoInvitado"])){
	$array_where [] = "I.IDTipoInvitado = '".$_GET["IDTipoInvitado"]."'";
}

if(!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])){
	$array_where [] = " ( FechaInicio >= '".$_GET["FechaInicio"]."' and FechaFin <= '".$_GET["FechaFin"]."') ";
}


if(count($array_where)>0):
	$where_filtro =  " and " . implode(" and ",$array_where);
endif;



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
				case 'NombreSocio':
					
					$where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;
				
				case 'NumeroDocumento':
					
					$where .= " AND (  I.NumeroDocumento LIKE '%" . $search_object->data . "%'  )  ";
				break;
				
				case 'Accion':
					
					$where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%' )  ";
				break;
				
				case 'NombreInvitado':
					
					$where .= " AND (  I.Nombre LIKE '%" . $search_object->data . "%' OR I.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;
				
				case 'Tipo':
					
					$where .= " AND (  SocioAutorizacion.TipoAutorizacion LIKE '%" . $search_object->data . "%' )  ";
				break;
				
				case 'Placa':
					$sql_placa = "Select IDVehiculo From Vehiculo Where Placa like '%".$search_object->data."%' ";
					$r_placa = $dbo->query($sql_placa);
					while($row_placa = $dbo->fetchArray($r_placa)):
						$array_id_vehiculo [] = $row_placa["IDVehiculo"];
					endwhile;
					if(count($array_id_vehiculo)>0):
						$id_vehiculo = implode(",",$array_id_vehiculo);
					endif;
					
					$where .= " AND (  SocioAutorizacion.IDVehiculo in (".$id_vehiculo.")  )  ";
				break;
				
				case 'FechaInicio':					
					$where .= " AND FechaInicio = '$search_object->data'";
					$fecha_inicio = $search_object->data;
				break;
				
				case 'FechaFin':					
					$where .= " AND FechaFin = '$search_object->data'";
					$fecha_inicio = $search_object->data;
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
		$where .= " AND FechaInicio >= CURDATE() AND FechaFin <= CURDATE()  ";
		$fecha_inicio = date("Y-m-d");
	break;

	
}

if(empty($fecha_inicio))
	$fecha_inicio = date("Y-m-d");

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaInicio'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if(!$sidx) $sidx = "FechaInicio";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioAutorizacion.IDSocio = S.IDSocio AND I.IDInvitado = SocioAutorizacion.IDInvitado " .  $where_filtro);
$row = $dbo->fetchArray($result);
$count = $row['count'];



if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
	$start = ((int)$limit*(int)$page) - (int)$limit;// do not put $limit*($page - 1)
	
	
if( empty( $limit ) )
	$limit = 1000000;


//$sql = "SELECT " . $table . ".*, CONCAT( Invitado.Nombre, ' ', Invitado.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Invitado.IDClub = '" . SIMUser::get("club")  . "' AND Invitado.IDSocio = SocioAutorizacion.IDSocio ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado,I.NumeroDocumento,I.IDTipoInvitado FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioAutorizacion.IDSocio = S.IDSocio AND I.IDInvitado = SocioAutorizacion.IDInvitado ". $where_filtro. "  ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
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
		$tipo_invitado = $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$datos_invitado["IDTipoInvitado"]."'" );
		
		if($row["UsuarioTrCr"]=="Socio"):
			$creada_por = "Socio";
		else:
			$creada_por = $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$row["UsuarioTrCr"]."'" );
		endif;
		
			
		$responce->rows[$i]['cell'] = array( 										
										"IDSocioAutorizacion" => $row["IDSocioAutorizacion"],
										"Accion" => $datos_socio["Accion"],
										"Predio" => "<font color='$color_fila'>" .$datos_socio["Predio"] . "</font>",
										"DocSocio" => "<font color='$color_fila'>" . $datos_socio["NumeroDocumento"] . "</font>",	
										"NombreSocio" => "<font color='$color_fila'>" . utf8_encode( $datos_socio["Nombre"]." " . $datos_socio["Apellido"] ) . "</font>",
										"DocumentoInvitado" => "<font color='$color_fila'>" . $datos_invitado["NumeroDocumento"] . "</font>",	
										"NombreInvitado" => "<font color='$color_fila'>" . utf8_encode( $datos_invitado["Nombre"]." " . $datos_invitado["Apellido"] ) . "</font>",	
										"TipoInvitado" => "<font color='$color_fila'>" . utf8_encode( $tipo_invitado ) . "</font>",	
										"CodigoAutorizacion" => "<font color='$color_fila'>" . $row["CodigoAutorizacion"] . "</font>",
										"CreadaPor" => "<font color='$color_fila'>" . $creada_por . "</font>",
										"FechaInicio" => "<font color='$color_fila'>" .SIMUtil::tiempo( $row["FechaInicio"] ). "</font>",
										"FechaFin" => "<font color='$color_fila'>" .SIMUtil::tiempo( $row["FechaFin"] ). "</font>"
									);
	endif;								

	$i++;
}        

echo json_encode($responce);

?>