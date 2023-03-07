 <?
	//Consulto Club
	$datos_club = $dbo->fetchAll("Club", " IDClub = '" . $_GET["IDClub"] . "' ", "array");

	if (!empty($_GET["IDClub"]))
		$IDClubActual = $_GET["IDClub"];
	elseif (!empty(SIMUser::get("club")))
		$IDClubActual = SIMUser::get("club");

	switch ($IDClubActual):
		case "20": //Medellin
			$hoja_estilos = "styles.css";
			$carpeta_imagenes = "medellin";
			break;
		case "25": //Gun Club
			$hoja_estilos = "stylesgun.css";
			$carpeta_imagenes = "gunclub";
			$linkpagina = "http://desarrollo.22cero2.com/gunclub/";
			break;
		case "28": //Liga de tenis
			$hoja_estilos = "stylesliga.css";
			$carpeta_imagenes = "ligatenis";
			break;
		case "52": //Liga de tenis
			$hoja_estilos = "stylesdistrital.css";
			$carpeta_imagenes = "distrital";
			break;
		case "1": //Guaymaral
			$hoja_estilos = "stylesguaymaral.css?" . rand(0, 1000);
			$carpeta_imagenes = "guaymaral";
			break;
		case "96": //Campin
			$hoja_estilos = "stylescampin.css?" . rand(0, 1000);
			$carpeta_imagenes = "campin";
			break;
			// case "15": //Pereira
		case "15": //Pereira
			$hoja_estilos = "stylespereira.css?" . rand(0, 1000);
			$carpeta_imagenes = "pereira";
			break;
		case "154": //Puerta Cortes
			$hoja_estilos = "stylesPuertaCortes.css?" . rand(0, 1000);
			$carpeta_imagenes = "puertacortes";

			break;
		default:
			$hoja_estilos = "styles.css";
			$carpeta_imagenes = "medellin";
	endswitch;




	?>
