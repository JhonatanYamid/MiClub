<?php
include("../../procedures/general_async.php");

if ($_POST['idSocio'] != '') {
	// echo "ingreso al post ID SOCIO <br>";
	$idClub = SIMUser::get("club");
	$idsocio = $_POST['idSocio'];
	$accion = $dbo->getFields("Socio", "Accion", "IDSocio='" . $idsocio . "'");
	//echo " la accion es:" . $accion;
	$sql_talonera = "SELECT * FROM Socio WHERE IDClub = '" . $idClub . "' AND AccionPadre = '" . $accion . "'";
	$result_talonera = $dbo->query($sql_talonera);
	// while ($row_talonera = $dbo->fetchArray($result_talonera)) :
	//echo $sql_talonera;
	/*  $idtalonera = $row_talonera["IDSocio"];
        $nombre_talonera = $row_talonera["Nombre"]; */

	while ($rowData = $dbo->fetchArray($result_talonera)) {
		$data[] = array(
			'idsocio' => $rowData['IDSocio'],
			'nombre' => $rowData['Nombre'],
			'apellido' => $rowData['Apellido'],

		);
	}
	//print_r($data);

	// endwhile;
	echo json_encode($data);
}
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Evento";
$key = "IDEvento";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script = "eventos";

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
		$_GET['sidx'] = "Titular ASC";
		$_GET['sord'] = "ASC";


		break;

	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'Seccion':
					//busco la seccion
					$sql_secc = "Select * From SeccionEvento Where Nombre LIKE '%" . $search_object->data . "%' and IDClub = '" . SIMUser::get("club") . "'";
					$result_secc = $dbo->query($sql_secc);
					while ($row_secc = $dbo->fetchArray($result_secc)) :
						$array_id_secc[] = $row_secc["IDSeccionEvento"];
					endwhile;
					if (count($array_id_secc) > 0) :
						$id_secc_buscar = implode(",", $array_id_secc);
					else :
						$id_secc_buscar = 0;
					endif;

					$where .= " AND   IDSeccionEvento in (" . $id_secc_buscar . ")";

					break;
				case 'TotalRegistrado':
					$where .= " AND(SELECT COUNT(IDEventoRegistro) FROM EventoRegistro WHERE IDEvento = Evento.IDEvento) = " . $search_object->data;
					break;
				case 'qryString':

					$where .= " AND ( Titular LIKE '%" . $search_object->data . "%' )";
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

			$where .= " AND ( Titular LIKE '%" . $qryString . "%'  )  ";
		} //end if
		break;

	case "searchdoc":
		$documento = SIMNet::req("doc");
		$correo = SIMNet::req("correo");
		$nombre = SIMNet::req("nombre");
		echo $dbo->getFields("NoSocios", "IDNoSocios", "NumeroDocumento = '$documento' AND (Nombre != '$nombre' OR CorreoElectronico != '$correo') AND IDClub = " . SIMUser::get("club"));
		break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Titular";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
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

//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$total = $dbo->getFields("EventoRegistro", "COUNT(IDEventoRegistro)", "IDEvento = '" . $row[$key] . "'");

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")

		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Seccion" => $dbo->getFields("SeccionEvento", "Nombre", "IDSeccionEvento = '" . $row["IDSeccionEvento"] . "'"),
			"Titular" => $row["Titular"],
			"MaximoParticipantes" => $row["MaximoParticipantes"],
			"TotalRegistrado" => $total,
			"Fecha" => $row["Fecha"],
			"Lugar" => $row["Lugar"],
			"Publicar" => $row["Publicar"],
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}

echo json_encode($responce);
