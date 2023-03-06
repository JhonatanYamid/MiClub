 <?

	SIMReg::setFromStructure(array(
		"title" => "ReservaGeneral",
		"table" => "ReservaGeneral",
		"key" => "IDReservaGeneral",
		"mod" => "ReservaGeneral"
	));





	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//Verificar permisos
	//SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	$script = "reportereservassocio";


	switch (SIMNet::req("action")) {


		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		default;
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
