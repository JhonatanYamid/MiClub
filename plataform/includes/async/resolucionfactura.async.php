<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");
$idPadre = SIMUtil::IdPadre($IDClub);
$hijos = SIMUtil::ObtenerHijosClubPadre($IDClub);

$columns = array();
$origen = SIMNet::req("origen");

$table = "ResolucionFactura";
$key = "IDResolucionFactura";
$where = " WHERE IDClub = $IDClub ";
$script = "resolucionfactura";

if($IDClub == $idPadre && !empty($hijos)){
	$idsClub = implode(',',array_values($hijos));
	$where = " WHERE IDClub in ($idsClub)";
}

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM ResolucionFactura WHERE IDResolucionFactura = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "IDClub,FechaTrCr";
		$_GET['sord'] = "ASC";

	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND ( Numero LIKE '%" . $search_object->data . "%' )";
				break;
				
				case 'Club':
					$arrValue = array();

					$sqlClubSh = "SELECT IDClub FROM Club WHERE IDClubPadre = $idPadre AND LOWER(Nombre) LIKE LOWER('%". $search_object->data ."%')";
                    $resultClubSh = $dbo->query($sqlClubSh);
                   
					while ($rowClubSh = $dbo->fetchArray($resultClubSh)){
						array_push($arrValue, $rowClubSh['IDClub']);
					}
					
					$idsClubSh = 0;			
                    if (count($arrValue) > 0){
                        $idsClubSh = implode(",", $arrValue);
					}

                    $where .= " AND IDClub in (" . $idsClubSh . ")";
    
				break;

				case 'Rango':
					$where .= " AND LOWER(CONCAT('Aut de ',Prefijo,'-',ValorInicial,' a ',Prefijo,'-',ValorFin)) LIKE LOWER('%" . $search_object->data . "%') ";
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
			$where .= " AND ( Numero LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "IDClub,FechaTrCr";
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


$sql = "SELECT *,CONCAT('Aut de ',Prefijo,'-',ValorInicial,' a ',Prefijo,'-',ValorFin) as Rango FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
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

	$sqlClub = "SELECT Nombre FROM Club WHERE IDClub = ".$row['IDClub'];
	$resultClub = $dbo->query($sqlClub);
	$rowClub = $dbo->fetchArray($resultClub);
	$club = $rowClub['Nombre'];


	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Club" => $club,		
			"Numero" => $row["Numero"],	
			"Fecha" => $row["Fecha"],	
			"Rango" => $row["Rango"],	
			"ConsecutivoFacturas" => $row["ConsecutivoFacturas"],	
			"ConsecutivoRecibos" => $row["ConsecutivoRecibos"],	
			"Activo" => $row["Activo"],								
			"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}        

echo json_encode($responce);

?>