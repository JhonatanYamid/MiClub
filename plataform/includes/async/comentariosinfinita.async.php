<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

if (empty($_GET["IDNoticia"]))
	$_GET["IDNoticia"] = 13312;

$table = "NoticiaComentarioInfinita";
$key = "IDNoticiaInfinita";
$where = " WHERE " . $table . ".IDNoticiaInfinita = '" . $_GET["IDNoticiaInfinita"] . "'";
$script = "comentariosinfinita";

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
				case 'qryString':

					$where .= " AND ( Comentario LIKE '%" . $search_object->data . "%' )";
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  NoticiaComentario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
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
}




$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "IDNoticiaComentario";
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



$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY Publicar ASC, FechaTrCr DESC LIMIT " . $start . "," . $limit;
/* echo $sql;
exit; */
//var_dump($sql);
$result = $dbo->query($sql);

//Buscar comentarios plublicados
$sql_asociada = "SELECT  IDNoticiaComentarioInfinita, Publicar FROM NoticiaComentarioInfinita WHERE IDNoticiaInfinita = '" . $_GET["IDNoticiaInfinita"] . "'";
$r_asociada = $dbo->query($sql_asociada);
/* echo $sql_asociada;
exit; */
while ($row_asociada = $dbo->fetchArray($r_asociada)) {
	if ($row_asociada["Publicar"] == "S")
		$array_id_votacion[] = $row_asociada["IDNoticiaComentarioInfinita"];
}

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
	/* print_r($row); */

	$responce->rows[$i]['id'] = $row[$key];

	if (in_array($row["IDNoticiaComentarioInfinita"], $array_id_votacion)) {
		$checksi = "checked";
		$checkno = "";
	} else {
		$checkno = "checked";
		$checksi = "";
	}


	$btn_incluir = '	<input type="radio" value="S" class="btnpublicarcomentarionoticiainfinita" name="Publicar' . $row["IDNoticiaComentarioInfinita"] . '"  idnoticiacomentario="' . $row["IDNoticiaComentarioInfinita"] . '" ' . $checksi . '>Si	<input type="radio" value="N" class="btnpublicarcomentarionoticiainfinita" name="Publicar' . $row["IDNoticiaComentarioInfinita"] . '"  idnoticiacomentario="' . $row["IDNoticiaComentarioInfinita"] . '" ' . $checkno . '>No';
	$btn_incluir .= "<div name='msgupdate" . $row["IDNoticiaComentarioInfinita"] . "' id='msgupdate" . $row["IDNoticiaComentarioInfinita"] . "'></div>";

	/* echo $btn_incluir; */



	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")

		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Socio" => $dbo->getFields("Socio", "Nombre", "IDSocio = " . $row["IDSocio"]) . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = " . $row["IDSocio"]),
			"Comentario" => $row["Comentario"],
			"Publicar" => $btn_incluir,
		);

	/* print_r($responce);
		exit; */

	$i++;
}

echo json_encode($responce);
