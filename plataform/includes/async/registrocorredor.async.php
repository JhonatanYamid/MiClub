<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "RegistroCorredor";
$key = "IDRegistroCorredor";
$where = " WHERE IDClub = $IDClub ";
$script = "registrocorredor";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

switch ($oper) {

	case "del":

		$sql_delete = "DELETE FROM RegistroCorredor WHERE IDRegistroCorredor = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query($sql_delete);

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre";
		$_GET['sord'] = "ASC";

		break;

	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'qryString':
					/* 
					$where .= " AND ( LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(Apellido) LIKE LOWER('%" . $qryString . "%')  OR NumeroDocumento LIKE '%" . $search_object->data . "%' OR Email LIKE '%" . $search_object->data . "%') ";
					*/
					break;

				case 'Nombre':
					$where .= " AND ( LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(Apellido) LIKE LOWER('%" . $search_object->data . "%'))";
					break;

				case 'NumCamiseta':
					$where .= " AND  NumCamiseta = $search_object->data ";
					break;
				case 'NumeroDocumento':
					$where .= " AND  NumeroDocumento = $search_object->data ";
					break;

				case 'Email':
					$where .= " AND  Email LIKE '%" . $search_object->data . "%'";
					break;

				case 'Carrera':
					$sqlCarrera = "SELECT IDCarrera
									FROM Carrera
									WHERE 
										LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%')";

					$resultCarrera = $dbo->query($sqlCarrera);

					while ($rowCarrera = $dbo->fetchArray($resultCarrera)) {
						$arrCarrera[] = $rowCarrera['IDCarrera'];
					}

					$where .= " AND ( IDCarrera in (" . implode(',', $arrCarrera) . ")) ";
					break;

				case 'Categoria':
					$sqlCategoria = "SELECT IDCategoriaTriatlon
									FROM CategoriaTriatlon
									WHERE 
										LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%')";

					$resultCategoria = $dbo->query($sqlCategoria);

					while ($rowCategoria = $dbo->fetchArray($resultCategoria)) {
						$arrCategoria[] = $rowCategoria['IDCategoriaTriatlon'];
					}

					$where .= " AND ( IDCategoriaTriatlon in (" . implode(',', $arrCategoria) . ")) ";
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(" . $tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
					break;
			}
		} //end for
		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {
			$where .= " AND ( LOWER(Nombre) LIKE LOWER('%" . $qryString . "%') OR LOWER(Apellido) LIKE LOWER('%" . $qryString . "%') OR NumeroDocumento LIKE '%" . $qryString . "%' OR Email LIKE '%" . $qryString . "%') ";
		} //end if
		break;

	case "autocomplete":
		$qryString = SIMNet::req("qryString");

		$sqlSocio = "SELECT IDSocio, NumeroDocumento, Nombre, Apellido ,CorreoElectronico
						FROM Socio 
						WHERE 
						IDClub = $IDClub AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR LOWER(Apellido) LIKE LOWER('%$qryString%') OR 
						LOWER(NumeroDocumento) LIKE LOWER('%$qryString%') OR LOWER(Accion) LIKE LOWER('%$qryString%'))";

		$qrySocio = $dbo->query($sqlSocio);

		while ($rSocio = $dbo->fetchArray($qrySocio)) {
			$arrayRes[] = $rSocio;
		}

		echo json_encode($arrayRes);
		exit;

		break;

	case 'select':
		$idCarrera = $_GET['idCarrera'];
		$value = $_GET['val'];
		$tipo = $_GET['tipo'];
		$idSelect = 'IDCategoriaTriatlon' . $tipo;

		echo SIMHTML::formPopupV2('CategoriaTriatlon', 'Nombre', 'Nombre', 'IDCategoriaTriatlon', $idSelect, $value, SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), "", "", "AND IDCarrera = $idCarrera AND Activo = 'S' AND IDClub = $IDClub");
		exit;
		break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
	$total_pages = ceil($count / $limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
	$limit = 1000000;


$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
/* echo $sql;
exit; */
// var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$categoria = $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = " . $row['IDCategoriaTriatlon']);
	$carrera = $dbo->getFields("Carrera", "Nombre", "IDCarrera = " . $row['IDCarrera']);

	//Boton Editar
	$botones =  '<a class="green" title="Editar" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>&nbsp&nbsp';
	// Boton Eliminar
	$botones .= '<a class="red eliminar_registro" title="Eliminar" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>&nbsp&nbsp';
	//Boton ver codigo qr
	$botones .= '<a class="ace-icon glyphicon glyphicon-qrcode" title="Ver Codigo QR" href="' . TRIATLON_ROOT . "qr/" . $row['CodigoQr'] . '"></a>';

	$fechaIn = $row["FechaIngreso"] != 0 ? (new DateTime($row["FechaIngreso"]))->format('Y-m-d g:iA') : "";
	$fechaSal = $row["FechaSalida"] != 0 ? (new DateTime($row["FechaSalida"]))->format('Y-m-d g:iA') : "";

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"NumCamiseta" => $row["NumCamiseta"],
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Nombre" => $row["Nombre"] . " " . $row["Apellido"],
			"Email" => $row["Email"],
			"Carrera" => $carrera,
			"Categoria" => $categoria,
			"Email" => $row["Email"],
			"FechaIngreso" => $fechaIn,
			"FechaSalida" => $fechaSal,
			"Accion" => $botones
		);

	$i++;
}

echo json_encode($responce);
