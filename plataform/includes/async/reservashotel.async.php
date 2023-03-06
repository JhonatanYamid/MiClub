<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "ReservaHotel";
$key = "IDReserva";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script = "reservashotel";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

switch ($oper) {

	case "del":

		$sql_delete = "DELETE FROM Publicidad WHERE IDBanner = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query($sql_delete);

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = " Nombre";
		$_GET['sord'] = "ASC";


		break;

	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'Socio':
					$where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
					break;
				case 'Accion':
					$where .= " AND (  S.Accion LIKE '%" . $search_object->data . "%'  )  ";
					break;
				case 'Habitacion':
					//busco el habitacion
					$sql_busq = "Select * From Habitacion Where NumeroHabitacion LIKE '%" . $search_object->data . "%' or Descripcion LIKE '%" . $search_object->data . "%'";
					$result_busq = $dbo->query($sql_busq);
					while ($row_busq = $dbo->fetchArray($result_busq)) :
						$array_id_busq[] = $row_busq["IDHabitacion"];
					endwhile;
					if (count($array_id_busq) > 0) :
						$id_busq = implode(",", $array_id_busq);
					else :
						$id_busq = 0;
					endif;

					$where .= " AND   IDHabitacion in (" . $id_busq . ")";

					break;
				case 'qryString':

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					break;
			}
		} //end for




		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
		} //end if
		break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . ", Socio S " . $where . " and S.IDSocio = ReservaHotel.IDSocio   ");
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


//$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
$sql = "SELECT " . $table . ".* FROM " . $table . ", Socio S " . $where . " and S.IDSocio = ReservaHotel.IDSocio " . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$valor_con_iva = $row["Valor"] + ($row["Valor"] * $row["IVA"] / 100);

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	$responce->rows[$i]['cell'] = array(
		$key => $row[$key],
		"Editar" => '<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva_hotel.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-file-text-o bigger-130"/></a>',
		"FechaInicio" => $row["FechaInicio"],
		"FechaFin" => $row["FechaFin"],
		"Accion" => $dbo->getFields("Socio", "Accion", "IDSocio = '" . $row["IDSocio"] . "'"),
		"Socio" => $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row["IDSocio"] . "'"),
		"Habitacion" => $dbo->getFields("Habitacion", "Descripcion", "IDHabitacion = '" . $row["IDHabitacion"] . "'"),
		"CabezaReserva" => $row["CabezaReserva"],
		"DocumentoDuenoReserva" => $row["DocumentoDuenoReserva"],
		"NombreDuenoReserva" => $row["NombreDuenoReserva"],
		"Estado" => $row["Estado"],
		"Valor" => "$" . number_format($valor_con_iva, 0, '', '.'),
		"Iva" => $row["IVA"],
		"TipoReserva" => $row["TipoReserva"],
		"NumeroPersonas" => $row["NumeroPersonas"],
		"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
	);

	$i++;
}

echo json_encode($responce);
