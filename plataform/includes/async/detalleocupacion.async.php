<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$array_adentro = SIMUtil::consulta_ocupacion($_GET,SIMUser::get("club"),"ID" );

if(count($array_adentro)>0):
	$id_log_acceso = implode(",",$array_adentro);
endif;


$table = "LogAcceso";
$key = "IDLogAcceso";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' and IDLogAcceso in (".$id_log_acceso.")";
$script ="reportesocios";

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
				case 'qryString':
					
					$where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '%" . $search_object->data . "%' )  ";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  Socio." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( Socio.Nombre LIKE '%" . $qryString . "%' OR Socio.Apellido LIKE '%" . $qryString . "%' OR Socio.NumeroDocumento LIKE '%" . $qryString . "%' OR Accion LIKE '%" . $qryString . "%' )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre123";
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



$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
//exit;
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
	$nombre="";
	$predio="";
	
	switch($row["Tipo"]):
		case "Contratista":				
		case "InvitadoSocio":
			$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row["IDInvitacion"]."'" );	
			$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );	
			$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );	
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );	
			$nombre = utf8_encode($datos_invitado["Nombre"] .  " " . $datos_invitado["Apellido"]);
			$predio = utf8_encode($datos_invitado["Predio"]);			
			$documento = $datos_invitado["NumeroDocumento"];
		break;
		case "InvitadoAcceso":
			$IDInvitado=$dbo->getFields( "SocioInvitadoEspecial" , "IDInvitado" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );	
			$IDSocioAutoriza=$dbo->getFields( "SocioInvitadoEspecial" , "IDSocio" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );	
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );	
			$tipo_invitado="Invitado Socio";
			$documento = $datos_invitado["NumeroDocumento"];	
			$nombre = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
			$predio=$dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$IDSocioAutoriza."'" );	
			$predio = utf8_encode($predio);			
			//Registrado Invitacion
			$IDUsuarioRegistroInvitacion=$dbo->getFields( "SocioInvitadoEspecial" , "UsuarioTrCr" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );
			$UsuarioRegistroInvitacion=$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$IDUsuarioRegistroInvitacion."'" );
			if(empty($UsuarioRegistroInvitacion))
				$UsuarioRegistroInvitacion=$IDUsuarioRegistroInvitacion;
		
		
			
		break;
		case "Socio":
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDInvitacion"] . "' ", "array" );	
			$documento=$datos_socio["NumeroDocumento"];
			$nombre=utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
			$predio=$dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$row["IDInvitacion"]."'" );	
			$tipo_invitado = "Socio";
		break;
		case "SocioInvitado":
			$datos_socio_invitado = $dbo->fetchAll( "SocioInvitado", " IDSocioInvitado = '" . $row["IDInvitacion"] . "' ", "array" );
			$documento=$datos_socio_invitado["NumeroDocumento"];
			$nombre=utf8_encode($datos_socio_invitado["Nombre"]);
			$predio="n/a";	
			$tipo_invitado = "Invitado por socio";
		break;
	endswitch;
	
	//Registrado por
	$UsuarioRegistro=$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$row["IDUsuario"]."'" );	
	
	
	
	

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
										$key => $row[$key],										
										"Tipo" => $tipo_invitado,
										"Documento" => $documento,
										"Nombre" => $nombre,
										"Predio" => $predio,
										"FechaIngreso" => substr($row["FechaIngreso"],0,10),
										"HoraIngreso" => substr($row["FechaIngreso"],10),
										"RegistradoPor" => utf8_encode($UsuarioRegistro),
									);
	

	$i++;


}   



echo json_encode($responce);

?>