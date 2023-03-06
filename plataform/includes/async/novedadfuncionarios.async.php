<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "NovedadFuncionarios";
$key = "IDNovedadFuncionarios";
$where = " WHERE n.IDClub = $IDClub ";
$script = "novedadfuncionarios";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM NovedadFuncionarios WHERE IDNovedadFuncionarios = '" . $_POST["id"] . "' LIMIT 1";
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
					$where .= " AND (LOWER(Nombre) LIKE LOWER('%$search_object->data%') OR NumeroDocumento LIKE ('%$search_object->data%')) ";
				break;

				case 'Afecta':
						$where .=  " AND IF(Afecta = 1, 'S','N') = LOWER('". $search_object->data . "') ";
				break;
					
				default:
						$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");

        if (!empty($qryString)) 
            $where .= " AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR NumeroDocumento LIKE ('%$qryString%')) ";
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "FechaInicio";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM NovedadFuncionarios as n ". $where . "    ");
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


$sql = "SELECT IDNovedadFuncionarios, NumeroDocumento, Nombre, FechaInicio, FechaFin, IF(Afecta = 1, 'S','N') as Afecta, Observaciones
		FROM NovedadFuncionarios as n, Usuario as u ". $where . " AND u.IDUsuario = n.IDUsuario ORDER BY $sidx $sord " . $str_limit;
// echo $sql;
// exit;
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
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Nombre" => $row["Nombre"],
			"FechaInicio" => (new DateTime($row["FechaInicio"]))->format('Y-m-d h:i:s A'),	
			"FechaFin" => (new DateTime($row["FechaFin"]))->format('Y-m-d h:i:s A'),
			"Afecta" => $row["Afecta"],
			"Observaciones" => $row["Observaciones"],				
			"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}        

echo json_encode($responce);

?>