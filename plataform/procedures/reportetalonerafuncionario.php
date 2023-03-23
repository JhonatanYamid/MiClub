<?


SIMReg::setFromStructure(array(
	"title" => "Reporte Talonera Funcionario",
	"table" => "LogTiqueteraFuncionarios",
	"key" => "IDLogTiqueteraFuncionarios",
	"mod" => "Socio"
));


$script = "reportetalonerafuncionario";
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
			$condiciones .= " and NumeroDocumento = '" . $_GET["IDSocio"] . "'";
		endif;

		if (!empty($_GET['FechaDesde'] && !empty($_GET['FechaDesde']))) :
			$condiciones .= " and FechaConsumo >= '" . $_GET["FechaDesde"] . "' and FechaConsumo <= '" . $_GET["FechaHasta"] . "' ";
		endif;

		$sql = "Select Nombre, NumeroDocumento, CantidadEntradas, TipoConsumo, FechaConsumo, HoraConsumo From LogTiqueteraFuncionarios  Where IDClub = '" . SIMUser::get("club") . "' " . $condiciones . " ORDER BY NumeroDocumento ASC";



		$nombre = "Reporte" . date("Y_m_d H:i:s"). ".xls";
		$qry = $dbo->query($sql);
		$Num = $dbo->rows($qry);

		$html = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>Nombre</th>";
		$html .= "<th>Numero Documento</th>";
		$html .= "<th>Cantidad Entradas</th>";
		$html .= "<th>Tipo Consumo</th>";
		$html .= "<th>Fecha Consumo</th>";
		$html .= "<th>Hora Consumo</th>";
		$html .= "</tr>";


		while ($row = $dbo->fetchArray($qry, $a)) {

			$html .= "<tr>";
			$html .= "<td>" . $row['Nombre'] . "</td>";
			$html .= "<td>" . $row['NumeroDocumento'] . "</td>";
			$html .= "<td>" . $row['CantidadEntradas'] . "</td>";
			$html .= "<td>" . $row['TipoConsumo'] . "</td>";
			$html .= "<td>" . $row['FechaConsumo'] . "</td>";
			$html .= "<td>" . $row['HoraConsumo'] . "</td>";
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
