 <?

	SIMReg::setFromStructure(array(
		"title" => "ContratoClub",
		"table" => "ContratosClub",
		"key" => "IDContratosClub",
		"mod" => "ContratosClub"
	));


	$script = "contratoclub";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


	$view = "views/" . $script . "/form.php";





	?>
