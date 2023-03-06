<?php
require( "../../../admin/config.inc.php" );


SIMUtil::cache();

if(!isset($_SESSION))
	session_start();

//handler de sesion
$simsession = new SIMSession( SESSION_LIMIT );

//traemos lo datos de la session
$datos = $simsession->verificar();
$active_sucursal = $_SESSION["sucursal"];

if( !is_object( $datos ) )
{
	header( "location:login.php?msg=NSA" );
	exit;
}//ebd if

//veriificamos el club de la sesion
if( !empty( $_SESSION["club"] ) )
	$datos->club = $_SESSION["club"];
else
	$datos->club = $datos->IDClub;

//encapsulamos los parammetros
SIMUser::setFromStructure( $datos );


//traer datos del registro
$datos_club = $dbo->fetchById( "Club" , "IDClub" , SIMUser::get("club") , "array"  );

//traer servicios del usuario
$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . SIMUser::get("IDUsuario") . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
$qry_servicios = $dbo->query( $sql_servicios );
while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
{
	$datos_servicio[ $r_servicio["IDServicio"] ] = $r_servicio;

	//$servicio_reserva = $dbo->fetchById( "ServicioInicial" , "IDServicioInicial" , $datos_servicio["IDServicioInicial"] , "array"  );

	if( SIMUser::get("IDPerfil") < 2 )  //es un coordinador se deben traer todos los elementos
	{
		$coordinador = 1;
		//traer todos los elementos
		$elementos = SIMWebService::get_elementos( SIMUser::get("club"),"", $r_servicio["IDServicio"] );
	}//end if
	else
	{
		$coordinador = 0;
		$elementos = SIMWebService::get_elementos( SIMUser::get("club"),"", $r_servicio["IDServicio"], SIMUser::get("IDUsuario") );
	}//else



}//end while




//traer todos los clubes en el sistema
$sql_clubes = "SELECT * FROM Club ";
$qry_clubes = $dbo->query( $sql_clubes );
while( $r_clubes = $dbo->fetchArray( $qry_clubes ) )
	$array_clubes[ $r_clubes["IDClub"] ] = $r_clubes;



?>
