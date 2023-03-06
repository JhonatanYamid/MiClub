<?php
require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
SIMUtil::cache();
session_start();


//handler de sesion
$simsession = new SIMSession( SESSION_LIMIT );

//traemos lo datos de la session
$datos = $simsession->verificar();



if( !is_object( $datos ) )
{
	SIMHTML::jsTopRedirect( "login.php?msg=NSA" );
	exit;
}//ebd if


//veriificamos el club de la sesion
if( !empty( $_SESSION["club"] ) )
	$datos->club = $_SESSION["club"];
else
	$datos->club = $datos->IDClub;


	if($datos->club==101 || $datos->club==112 || $datos->club==101 || $datos->club==112 ){
		date_default_timezone_set('America/Mexico_City');
	}
	elseif($datos->club==125){ //Uruguay
		date_default_timezone_set('America/Montevideo');
	}
	else
		date_default_timezone_set('America/Bogota');


//encapsulamos los parammetros
SIMUser::setFromStructure( $datos );


//traer datos del registro
$datos_club = $dbo->fetchById( "Club" , "IDClub" , SIMUser::get("club") , "array"  );

// Si el club solicita cambiar la clave a sus usuarios cada x meses
if($datos_club["CambiarClaveVencida"]=="S"):
	// Verifico si la clave esta vencida
	$fecha_ultimo_cambio=$datos->FechaCambioClave;
	$fecha_vencimiento = strtotime ( '+3 month' , strtotime ( $fecha_ultimo_cambio ) ) ;
	$fecha_actual_clave=strtotime(date("Y-m-d H:i:s"));
	if($fecha_vencimiento<=$fecha_actual_clave && $datos->SolicitaCambioClave=="S" ):
		if(basename($_SERVER["SCRIPT_FILENAME"])!="cambiarclave.php"):
			SIMHTML::jsTopRedirect( "cambiarclave.php?action=updateclave&IDUsuario=".base64_encode($datos->IDUsuario) );
			exit;
		endif;
	endif;
endif;

if( SIMUser::get( "Nivel" ) == 0 )
{

	//traer servicios del usuario
	$sql_servicios = "SELECT  S.*
					  FROM ServicioMaestro SM, Servicio S
					  WHERE S.IDClub =  '".SIMUser::get("club")."'
					  AND S.IDServicioMaestro = SM.IDServicioMaestro
					  AND S.IDServicioMaestro in (Select IDServicioMaestro From ServicioClub SC Where IDClub = '".SIMUser::get("club")."' and Activo = 'S') ";

	//$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE Servicio.IDClub = '" . SIMUser::get("club") . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
	$qry_servicios = $dbo->query( $sql_servicios );
	while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
	{
		$datos_servicio[ $r_servicio["IDServicio"] ] = $r_servicio;

		if((int)$datos_servicio["IDServicioInicial"]>0){
			$servicio_reserva = $dbo->fetchById( "ServicioInicial" , "IDServicioInicial" , $datos_servicio["IDServicioInicial"] , "array"  );
		}


		//traer todos los elementos
		$response_elementos = SIMWebService::get_elementos( SIMUser::get("club"),"", $r_servicio["IDServicio"] );
		$elementos[ $r_servicio["IDServicio"] ] = $response_elementos["response"];




	}//end while


}//end if
else
{
	//traer servicios del usuario
	$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . SIMUser::get("IDUsuario") . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio Order by Orden";
	$qry_servicios = $dbo->query( $sql_servicios );
	while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
	{
		$datos_servicio[ $r_servicio["IDServicio"] ] = $r_servicio;

		if( empty( $r_servicio["Nombre"] ) )
			$datos_servicio[ $r_servicio["IDServicio"] ]["Nombre"] = $dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' " );

		if((int)$datos_servicio["IDServicioInicial"]>0){
			$servicio_reserva = $dbo->fetchById( "ServicioInicial" , "IDServicioInicial" , $datos_servicio["IDServicioInicial"] , "array"  );
		}

		if( SIMUser::get("IDPerfil") < 2 )  //es un coordinador se deben traer todos los elementos
		{
			$coordinador = 1;
			//traer todos los elementos
			$response_elementos = SIMWebService::get_elementos( SIMUser::get("club"),"", $r_servicio["IDServicio"] );
			$elementos[ $r_servicio["IDServicio"] ] = $response_elementos["response"];
		}//end if
		else
		{
			$coordinador = 0;
			$response_elementos = SIMWebService::get_elementos( SIMUser::get("club"),"", $r_servicio["IDServicio"], SIMUser::get("IDUsuario") );
			$elementos[ $r_servicio["IDServicio"] ] = $response_elementos["response"];


		}//else



	}//end while

}//end else



// si el perfil es solo crear noticia y no publicar cambio el estado a no publicado y envio notificacion al admin
if(SIMUser::get("IDPerfil") == 28){
	$_POST["Publicar"]='N';
	SIMUtil::notifica_nuevo_contenido($_POST["IDClub"],$_POST["ModuloActual"],$_POST["Titular"]);
}

//seguridad para post y get
foreach( $_GET as $clave => $valor )
{
	$_GET[$clave] = SIMUtil::antiinjection( $valor );
}

foreach( $_POST as $clave => $valor )
{
	if( !array( $valor ) )
		$_POST[$clave] = SIMUtil::antiinjection( $valor );
	else
		foreach( $_POST[$clave] as $key_clave => $valor_array )
			$_POST[$clave][$key_clave] = SIMUtil::antiinjection( $valor_array );
}//end for

//traer todos los clubes en el sistema
if($_GET["Tipo"]=="Padre")
	$sql_clubes = "SELECT * FROM Club WHERE IDClubPadre='".$_GET["id"]."' Order by IDClub ASC";
elseif($_GET["ver"]=="t")
	$sql_clubes = "SELECT * FROM Club WHERE IDClubPadre=0 Order by IDClub ASC";
else
	$sql_clubes = "SELECT * FROM Club WHERE 1 Order by IDClub ASC";

$qry_clubes = $dbo->query( $sql_clubes );
while( $r_clubes = $dbo->fetchArray( $qry_clubes ) )
	$array_clubes[ $r_clubes["IDClub"] ] = $r_clubes;

$action = SIMNet::req("action");
$id = SIMNet::req("id");


if (SIMUser::get("IDPerfil")==0):
	$miga_home = "clubes.php";
elseif(SIMUser::get("IDPerfil")==1):
	$miga_home = "socios.php?action=search";
elseif(SIMUser::get("IDPerfil")==4 || SIMUser::get("IDPerfil")==9):
	$miga_home = "index.php";
else:
   $miga_home = "reservas.php";
 endif;

if($action == "add"):
	$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil") , "PermisoCrear");
	if($Permiso == 0):
		header("Location: sinpermisocrear.php");
	endif;
endif;

if($action == "edit"):
	$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil") , "PermisoModificar");
	if($Permiso == 0):
		header("Location: sinpermisomodificar.php");
	endif;
endif;

 	

$tipo_club = $dbo->getFields( "Club" , "IDTipoClub" , "IDClub = '" . SIMUser::get("club") . "'");

?>
