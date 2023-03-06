<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "Turnos";
$key = "IDTurnos";
$where = " WHERE IDClub = $IDClub ";
$script = "turnos";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Turnos WHERE IDTurnos = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Codigo";
		$_GET['sord'] = "ASC";

	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND (( Codigo LIKE '%" . $search_object->data . "%' ) OR ( Nombre LIKE '%" . $search_object->data . "%' ))";
				break;
				
				case 'Entrada':
					$where .=  " AND TIME_FORMAT(Entrada,'%r') LIKE '%" . $search_object->data . "%' ";
				break;
				
				case 'Salida':
					$where .=  " AND TIME_FORMAT(Salida,'%r') LIKE '%" . $search_object->data . "%' ";
				break;
				
				case 'AlmuerzoInicio':
					$where .=  " AND TIME_FORMAT(AlmuerzoInicio,'%r') LIKE '%" . $search_object->data . "%' ";
				break;
				
				case 'AlmuerzoFin':
					$where .=  " AND TIME_FORMAT(AlmuerzoFin,'%r') LIKE '%" . $search_object->data . "%' ";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if(!empty($qryString)){
			$where .= " AND (( Codigo LIKE '%" . $qryString . "%' ) OR ( Nombre LIKE '%" . $qryString . "%' ))  ";
		}//end if
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Codigo";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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


$sql = "SELECT IDTurnos, Codigo, Nombre, TIME_FORMAT(Entrada,'%r') as Entrada, TIME_FORMAT(Salida,'%r') as Salida, TIME_FORMAT(AlmuerzoInicio,'%r') as AlmuerzoInicio, TIME_FORMAT(AlmuerzoFin,'%r') as AlmuerzoFin, Observaciones, Activo
		FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
//echo $sql;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',		
			"Codigo" => $row["Codigo"],	
			"Nombre" => $row["Nombre"],
			"Entrada" => $row["Entrada"],
			"Salida" => $row["Salida"],
			"AlmuerzoInicio" => $row["AlmuerzoInicio"],
			"AlmuerzoFin" => $row["AlmuerzoFin"],
			"Observaciones" => $row["Observaciones"],
			"Activo" => $row["Activo"],								
			"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}        

echo json_encode($responce);

?>