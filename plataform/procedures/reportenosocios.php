 <?

	SIMReg::setFromStructure(array(
		"title" => "NoSocios",
		"table" => "NoSocios",
		"key" => "IDNoSocios",
		"mod" => "Reportes"
	));

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	$script = "reportenosocios";

		$view = "views/reportenosocios/list.php";

	?>