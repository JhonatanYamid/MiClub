 <?

	SIMReg::setFromStructure(array(
		"title" => "Viajes",
		"table" => "Viaje",
		"key" => "IDViaje",
		"mod" => "Viaje"
	));

	$script = "historialviajes";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");

	$IDClub = SIMUser::get("club");

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	switch ($action) {

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/list.php";

?>