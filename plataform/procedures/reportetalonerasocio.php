<?


SIMReg::setFromStructure(array(
	"title" => "Reporte Talonera Socio",
	"table" => "RegistrarAlimentosCasino",
	"key" => "IDRegistrarAlimentosCasino",
	"mod" => "Socio"
));


$script = "reportetalonerasocio";
// var_dump($_GET);
// die;

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

	case "add":
		$view = "views/" . $script . "/form.php";
		$newmode = "insert";
		$titulo_accion = "Crear";
		break;

	case "search":
		$view = "views/" . $script . "/list.php";
		break;
	case "Exportar":
		$frm = SIMUtil::varsLOG($_POST);


		if (isset($_GET['IDSocio']) && $_GET['IDSocio'] && !empty($_GET['IDSocio'])) :
			$condiciones .= " and R.Cedula = '" . $_GET["IDSocio"] . "'";
		endif;

		if (!empty($_GET['FechaDesde'] && !empty($_GET['FechaDesde']))) :
			$condiciones .= " and R.FechaRegistro >= '" . $_GET["FechaDesde"] . "' and R.FechaRegistro <= '" . $_GET["FechaHasta"] . "' ";
		endif;

		$sql = "Select R.Cedula, R.Desayuno, R.Almuerzo, R.Comida, R.Cena, R.FechaRegistro, R.HoraRegistro FROM RegistrarAlimentosCasino AS R RIGHT JOIN (SELECT * FROM ConsumoAlimentosCasino WHERE IDSocio IS NOT NULL AND IDSocio <> 0) as C ON R.Cedula = C.Cedula WHERE R.IDClub = '" . SIMUser::get("club") . "' " . $condiciones . " ORDER BY R.Cedula ASC";


		$nombre = "Reporte" . date("Y_m_d H:i:s"). ".xls";
		$qry = $dbo->query($sql);
		$Num = $dbo->rows($qry);

		$html = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>Cedula</th>";
		$html .= "<th>Desayuno</th>";
		$html .= "<th>Almuerzo</th>";
		$html .= "<th>Comida</th>";
		$html .= "<th>Cena</th>";
		$html .= "<th>Fecha Registro</th>";
		$html .= "<th>Hora Registro</th>";
		$html .= "</tr>";


		while ($row = $dbo->fetchArray($qry, $a)) {

			$html .= "<tr>";
			$html .= "<td>" . $row['Cedula'] . "</td>";
			$html .= "<td>" . $row['Desayuno'] . "</td>";
			$html .= "<td>" . $row['Almuerzo'] . "</td>";
			$html .= "<td>" . $row['Comida'] . "</td>";
			$html .= "<td>" . $row['Cena'] . "</td>";
			$html .= "<td>" . $row['FechaRegistro'] . "</td>";
			$html .= "<td>" . $row['HoraRegistro'] . "</td>";
			$html .= "</tr>";
		}

		$html .= "</table>";
		$filename = $nombre;

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=" . $filename . "");


		echo $html;
		exit;
		die;
		//Verifico que no exista la categoria
		SIMHTML::jsRedirect($script . ".php");
		exit;
		break;
	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
