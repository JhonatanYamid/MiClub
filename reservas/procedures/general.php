<?php
require("../admin/config.inc.php");
require_once LIBDIR . "SIMWebServiceReservas.inc.php";
SIMUtil::cache();
session_start();

//handler de sesion
$simsession_socio = new SIMSession(SESSION_LIMIT);


//traemos lo datos de la session
$datos = $simsession_socio->verificar_cliente();



if (!is_object($datos)) {
	if ($_GET["IDClub"] == 28 || $_GET["IDClub"] == 28)
		SIMHTML::jsTopRedirect("indexcurso.php?msg=NSA&IDClub=" . $_GET["IDClub"]);
	else
		SIMHTML::jsTopRedirect("index.php?msg=NSA&IDClub=" . $_GET["IDClub"]);

	exit;
} //ebd if

//veriificamos el club de la sesion
if (!empty($_SESSION["club"]))
	$datos->club = $_SESSION["club"];
else
	$datos->club = $datos->IDClub;


//encapsulamos los parammetros
SIMUser::setFromStructure($datos);


//traer datos del registro
$datos_club = $dbo->fetchById("Club", "IDClub", SIMUser::get("club"), "array");


if (SIMUser::get("Nivel") == 0) {

	//traer servicios del usuario
	$sql_servicios = "SELECT  S.*
					  FROM ServicioMaestro SM, Servicio S
					  WHERE S.IDClub =  '" . SIMUser::get("club") . "'
					  AND S.IDServicioMaestro = SM.IDServicioMaestro
					  AND S.IDServicioMaestro in (Select IDServicioMaestro From ServicioClub SC Where IDClub = '" . SIMUser::get("club") . "' and Activo = 'S') ";

	//$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE Servicio.IDClub = '" . SIMUser::get("club") . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
	$qry_servicios = $dbo->query($sql_servicios);
	while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
		$datos_servicio[$r_servicio["IDServicio"]] = $r_servicio;

		$servicio_reserva = $dbo->fetchById("ServicioInicial", "IDServicioInicial", $datos_servicio["IDServicioInicial"], "array");


		//traer todos los elementos
		$response_elementos = SIMWebServiceReservas::get_elementos(SIMUser::get("club"), "", $r_servicio["IDServicio"]);
		$elementos[$r_servicio["IDServicio"]] = $response_elementos["response"];
	} //end while


} //end if
else {


	//traer servicios del usuario
	$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . SIMUser::get("IDUsuario") . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
	$qry_servicios = $dbo->query($sql_servicios);
	while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
		$datos_servicio[$r_servicio["IDServicio"]] = $r_servicio;

		if (empty($r_servicio["Nombre"]))
			$datos_servicio[$r_servicio["IDServicio"]]["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' ");

		$servicio_reserva = $dbo->fetchById("ServicioInicial", "IDServicioInicial", $datos_servicio["IDServicioInicial"], "array");

		if (SIMUser::get("IDPerfil") < 2)  //es un coordinador se deben traer todos los elementos
		{
			$coordinador = 1;
			//traer todos los elementos
			$response_elementos = SIMWebService::get_elementos(SIMUser::get("club"), "", $r_servicio["IDServicio"]);
			$elementos[$r_servicio["IDServicio"]] = $response_elementos["response"];
		} //end if
		else {
			$coordinador = 0;
			$response_elementos = SIMWebService::get_elementos(SIMUser::get("club"), "", $r_servicio["IDServicio"], SIMUser::get("IDUsuario"));
			$elementos[$r_servicio["IDServicio"]] = $response_elementos["response"];
		} //else



	} //end while

} //end else




//seguridad para post y get
foreach ($_GET as $clave => $valor) {
	$_GET[$clave] = SIMUtil::antiinjection($valor);
}

foreach ($_POST as $clave => $valor) {
	if (!array($valor))
		$_POST[$clave] = SIMUtil::antiinjection($valor);
	else
		foreach ($_POST[$clave] as $key_clave => $valor_array)
			$_POST[$clave][$key_clave] = SIMUtil::antiinjection($valor_array);
} //end for

//traer todos los clubes en el sistema
$sql_clubes = "SELECT * FROM Club ";
$qry_clubes = $dbo->query($sql_clubes);
while ($r_clubes = $dbo->fetchArray($qry_clubes))
	$array_clubes[$r_clubes["IDClub"]] = $r_clubes;

$action = SIMNet::req("action");
$id = SIMNet::req("id");


if (SIMUser::get("IDPerfil") == 0) :
	$miga_home = "clubes.php";
elseif (SIMUser::get("IDPerfil") == 1) :
	$miga_home = "socios.php?action=search";
elseif (SIMUser::get("IDPerfil") == 4 || SIMUser::get("IDPerfil") == 9) :
	$miga_home = "index.php";
else :
	$miga_home = "reservas.php";
endif;


$tipo_club = $dbo->getFields("Club", "IDTipoClub", "IDClub = '" . SIMUser::get("club") . "'");
