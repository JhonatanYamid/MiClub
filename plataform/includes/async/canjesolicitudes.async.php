<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "CanjeSolicitud";
$key = "IDCanjeSolicitud";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script = "canjesolicitudes";




$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";


switch ($oper) {

	case "del":

		$sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query($sql_delete);

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Apellido ASC, Nombre";
		$_GET['sord'] = "ASC";


		break;

	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'qryString':

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
					break;
				case 'Numero':

					$where .= " AND ( CanjeSolicitud.Numero LIKE '%" . $search_object->data . "%' )  ";
					break;
				case 'Accion':

					$where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%' )  ";
					break;

				case 'Socio':
					$where .= " AND ( S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%')";
					break;

				case 'CorreoElectronico':
					$where .= " AND (  S.CorreoElectronico LIKE '%" . $search_object->data . "%' )  ";
					break;
				case 'FechaInicio':

					$where .= " AND ( CanjeSolicitud.FechaInicio LIKE '%" . $search_object->data . "%' )  ";
					break;
				case 'Club':

					$where .= " AND (LC.Nombre LIKE '%" . $search_object->data . "%' )  ";
					break;

				case 'Estado':

					$where .= " AND (EC.Nombre LIKE '%" . $search_object->data . "%' )  ";
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					break;
			}
		} //end for




		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {

			$where .= " AND ( S.Nombre LIKE '%" . $qryString . "%' OR S.Apellido LIKE '%" . $qryString .
				"%' OR S.Accion LIKE '%" . $qryString . "%' OR CanjeSolicitud.Numero LIKE '%" . $qryString .
				"%' OR S.CorreoElectronico LIKE '%" . $qryString . "%' OR LC.Nombre LIKE '%" . $qryString .
				"%' OR CanjeSolicitud.FechaInicio LIKE '%" . $qryString . "%' OR EC.Nombre LIKE '%" . $qryString . "%' )";
		} //end if
		break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
	$sidx = "IDCanjeSolicitud";
}



// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");

$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
	$total_pages = ceil($count / $limit);
} else {
	$total_pages = 1;
}
if ($page > $total_pages) {
	$page = $total_pages;
}
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
	$limit = 1000000;
}





//$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $table.$sidx $sord " . $str_limit;

$sql = "SELECT " . $table . ".* FROM " . $table . ", Socio S, ListaClubes LC, EstadoCanjeSolicitud EC" . $where . " and S.IDSocio = CanjeSolicitud.IDSocio and LC.IDListaClubes=CanjeSolicitud.IDListaClubes and EC.IDEstadoCanjeSolicitud= CanjeSolicitud.IDEstadoCanjeSolicitud" . " ORDER BY $table.$sidx $sord LIMIT " . $start . "," . $limit;
/* echo $sql;
exit; */
//exit;
//var_dump($sql);
//var_dump(SIMUtil::Tipo_tiempo_laboral('08:00:00', '17:00:00', '2022-01-27 08:07:44', '2022-01-27 19:52:15'));
//var_dump(SIMUtil::Tipo_tiempo_laboral('08:00:00', '17:00:00', '2022-06-11 05:18:00', '2022-06-11 22:03:11'));
//var_dump(SIMUtil::Calcular_diferencia_horas('08:00:00', '2022-10-03 07:50:00', 'Entrada'));
var_dump(SIMUtil::Tipo_tiempo_laboral('08:30:00', '18:30:00', '2022-12-24 11:38:20', '2023-01-02 15:59:41'));
exit;
//var_dump(SIMUtil::Calcular_diferencia_horas('17:00:00', '2022-10-14 22:00:00', 'Salida'));
//exit;
/* var_dump(SIMUtil::tiempo_laboral_despues_de_turno('2022-06-05 21:00:00', '2022-06-05 23:30:00'));
exit; */
//var_dump(SIMWebServiceApp::get_producto_buscador('8', '5533', '', 'carne', '1', '3'));

$horaSalida = '22:30:00';
$marca = '2023-01-02 22:30:00';
/* var_dump(SIMUtil::Calcular_diferencia_horas($horaSalida, $marca, 'Salida'));
exit; */

$frm["IDClub"] = SIMUser::get("club");
$frm["Fecha"] = date("Y-m-d H:i:s");
$r_socios["IDSocio"] = '5533';
$r_socios["IDClub"] = '8';
$r_socios["Token"] = '3';
$r_socios["Dispositivo"] = 'Ios';

/* var_dump(SIMUtil::envia_cola_notificacion($r_socios, $frm));

exit; */
/* 
echo $sql;
exit; */

$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array");

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile") {
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Numero" => $row["Numero"],
			"Accion" => utf8_decode($datos_socio["Accion"]),
			"CorreoElectronico" => utf8_decode($datos_socio["CorreoElectronico"]),
			"Socio" => $datos_socio["Nombre"] . " " .  $datos_socio["Apellido"],
			"Club" => $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $row["IDListaClubes"] . "'"),
			"FechaInicio" => $row["FechaInicio"],
			"Estado" => $dbo->getFields("EstadoCanjeSolicitud", "Nombre", "IDEstadoCanjeSolicitud = '" . $row["IDEstadoCanjeSolicitud"] . "'"),
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);
	}
	$i++;
}

echo json_encode($responce);
