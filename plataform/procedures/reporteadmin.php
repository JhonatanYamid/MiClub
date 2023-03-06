 <?

	SIMReg::setFromStructure(array(
		"title" => "Club",
		"table" => "Club",
		"key" => "IDClub",
		"mod" => "Club"
	));


	$script = "clubes";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


	if (!empty($_GET["IDClub"])) :
		$condicion_club_busqueda .= " and IDClub = '" . $_GET["IDClub"] . "'";
	endif;

	$TablaReserva = "ReservaGeneral";

	if (!empty($_GET["FechaInicio"])) :
		$condicion_fecha  .= " and FechaTrCr >= '" . $_GET["FechaInicio"] . "'";
		$condicion_fecha_reserva  .= " and Fecha >= '" . $_GET["FechaInicio"] . "'";
		if ((int)substr($_GET["FechaInicio"], 0, 4) <= 2017)
			$TablaReserva = "ReservaGeneralBck";
	endif;

	if (!empty($_GET["FechaFin"])) :
		$condicion_fecha  .= " and FechaTrCr <= '" . $_GET["FechaFin"] . "'";
		$condicion_fecha_reserva  .= " and Fecha <= '" . $_GET["FechaFin"] . "'";
	endif;

	echo $condicion;

	if (SIMUser::get("Nivel") == 0) :
		$condicion_club = $_GET["IDClub"];
	//$condicion_club = " IDClub >0 ";
	else :
		$condicion_club = " IDClub = '" . SIMUser::get("club") . "'";
	endif;


	//traer todos los clubes en el sistema
	$sql_clubes_busqueda = "SELECT * FROM Club Where " . $condicion_club . " " . $condicion_club_busqueda;
	$qry_clubes_busqueda = $dbo->query($sql_clubes_busqueda);
	while ($r_clubes_busqueda = $dbo->fetchArray($qry_clubes_busqueda))
		$array_clubes_busqueda[$r_clubes_busqueda["IDClub"]] = $r_clubes_busqueda;


	?>
