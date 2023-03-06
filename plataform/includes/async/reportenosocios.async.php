<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$key = "IDEventoRegistro";

$where = " WHERE er.IDNoSocios = ns.IDNoSocios AND er.IDEvento = e.IDEvento AND er.IDClub = ".SIMUser::get("club");
$script ="reportenosocios";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{
	case "search":

		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					$where .= " AND (LOWER(ns.Nombre) LIKE LOWER('%".$search_object->data."%') OR NumeroDocumento LIKE '%".$search_object->data."%') ";
				break;

				case 'Seccion':
					$arr_SE = [];
					$sql_SE = "SELECT IDSeccionEvento FROM SeccionEvento WHERE LOWER(Nombre) LIKE LOWER('%".$search_object->data."%') ";
					$result_SE = $dbo->query($sql_SE);

					while($row_SE = $dbo->fetchArray($result_SE)) {
						$arr_SE[] = $row_SE['IDSeccionEvento'];
					}

					$where .= " AND e.IDSeccionEvento in(".implode(',',$arr_SE).") ";
				break;

				default:
					$where .=  $array_buqueda->groupOp . " LOWER(" . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}

		}//end for

	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			$where .= " AND ( LOWER(ns.Nombre) LIKE LOWER('%$qryString%') OR NumeroDocumento LIKE '%$qryString%')  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "er.FechaTrCr";

$result = $dbo->query("SELECT COUNT(*) AS count FROM EventoRegistro er, NoSocios ns, Evento e ". $where);
$row = $dbo->fetchArray($result);
$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


$sql = "SELECT NumeroDocumento, ns.Nombre, CorreoElectronico,e.IDSeccionEvento,
			Titular, er.FechaTRCR as FechaRegistro 
		FROM EventoRegistro er, NoSocios ns, Evento e ".$where . " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;

// var_dump($sql);exit;
$result = $dbo->query($sql);

$responce = "";

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');

while($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];
	
	$seccion = $row['IDSeccionEvento'] > 0 ? $dbo->getFields("SeccionEvento", "Nombre", "IDSeccionEvento = ".$row["IDSeccionEvento"]) : '';

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Nombre" => $row["Nombre"],
			"CorreoElectronico" => $row["CorreoElectronico"],
			"Seccion" => $seccion,
			"Evento" => $row["Titular"],
			"FechaRegistro" => $row["FechaRegistro"],
		);

	$i++;

}



echo json_encode($responce);

?>
