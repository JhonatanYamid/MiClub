<?php
SIMReg::setFromStructure(array(
	"title" => "Vacunación2",
	"table" => "Vacuna2",
	"key" => "IDVacuna2",
	"mod" => "Reportes"
));

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

$script = "reportevacunacion2";

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);
switch (SIMNet::req("action")) {

	case "search":
		$view = "views/reportefuncionarios/list.php";
		break;
} // End switch



if (empty($view))
	$view = "views/reportevacunacion2/list.php";
