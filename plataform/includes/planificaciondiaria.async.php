<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");

$columns = array();
$origen = SIMNet::req("origen");

$table = "PlanificacionDiaria";
$key = "IDPlanificacionDiaria";
$where = " WHERE IDClub = $IDClub ";
$script = "planificaciondiaria";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM PlanificacionDiaria WHERE IDPlanificacionDiaria = '" . $_POST["id"] . "' LIMIT 1";
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
					$where .= " AND ( Fecha LIKE '%" . $search_object->data . "%' )";
				break;

				case 'PlanDia':

					$sqlTurnos = "SELECT IDTurnos FROM Turnos WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultTurnos = $dbo->query($sqlTurnos);

					while ($rowTurnos = $dbo->fetchArray($resultTurnos)) :
						$arrayTurnos[] = $rowTurnos["IDTurnos"];
					endwhile;
					
					$idsTurnos = count($arrayTurnos) > 0 ? implode(",", $arrayTurnos) : 0;
					
					$sqlDiasnl = "SELECT IDDiaNoLaboral FROM DiaNoLaboral WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultDiasnl = $dbo->query($sqlDiasnl);

					while ($rowDiasnl = $dbo->fetchArray($resultDiasnl)) :
						$arrayDiasnl[] = $rowDiasnl["IDDiaNoLaboral"];
					endwhile;
					
					$idsDiasnl = count($arrayDiasnl) > 0 ? implode(",", $arrayDiasnl) : 0;

					$where .= " AND (IDTurnos in($idsTurnos) OR IDDiaNoLaboral in($idsDiasnl)) ";

				break;

				case 'Usuario':
					$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultUsuario = $dbo->query($sqlUsuario);

					while ($rowUsuario = $dbo->fetchArray($resultUsuario)) :
						$arrayIds[] = $rowUsuario["IDUsuario"];
					endwhile;
					
					$idsUsuario = count($arrayIds) > 0 ? implode(",", $arrayIds) : 0;

					$where .= " AND IDUsuario in($idsUsuario) ";
				break;

				case 'NumeroDocumento':
					$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE NumeroDocumento LIKE ('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultUsuario = $dbo->query($sqlUsuario);

					while ($rowUsuario = $dbo->fetchArray($resultUsuario)) :
						$arrayIds[] = $rowUsuario["IDUsuario"];
					endwhile;
					
					$idsUsuario = count($arrayIds) > 0 ? implode(",", $arrayIds) : 0;

					$where .= " AND IDUsuario in($idsUsuario) ";
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

			$sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE IDClub = $IDClub AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR NumeroDocumento LIKE ('%$qryString%'))";
			$resultUsuario = $dbo->query($sqlUsuario);

			while ($rowUsuario = $dbo->fetchArray($resultUsuario)) :
				$arrayIds[] = $rowUsuario["IDUsuario"];
			endwhile;
			
			$idsUsuario = count($arrayIds) > 0 ? implode(",", $arrayIds) : 0;

			$where .= " AND (IDUsuario in($idsUsuario) OR Fecha LIKE '%$qryString%')";
		}
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Fecha";
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
	
$sqla = "SELECT IDPlanificacionDiaria, IDTurnos, IDDiaNoLaboral, IDUsuario, Fecha, DiaLaboral, Activo
FROM PlanificacionDiaria ". $where;

$resulta = $dbo->query( $sqla );

while($rowa = $dbo->fetchArray($resulta)) {

	$idCheck = $dbo->getFields("CheckinFuncionarios", "IDCheckinFuncionarios", "IDClub = ".$IDClub." AND DATE(FechaEntrada) = '".$rowa["Fecha"]."' AND IDUsuario = ".$rowa['IDUsuario']);

	if($idCheck){
		$idCheck = $dbo->update(array('IDPlanificacionDiaria'=>$rowa[$key]), 'CheckinFuncionarios', 'IDCheckinFuncionarios', $idCheck);
	}

}

$sql = "SELECT IDPlanificacionDiaria, IDTurnos, IDDiaNoLaboral, IDUsuario, Fecha, DiaLaboral, Activo
		FROM PlanificacionDiaria ". $where . " ORDER BY $sidx $sord " . $str_limit;
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

	if($row['DiaLaboral'] == 1){
		$planDia = $dbo->getFields("Turnos", "Nombre", "IDTurnos = ".$row['IDTurnos']);
	}else{
		$planDia = $dbo->getFields("DiaNoLaboral", "Nombre", "IDDiaNoLaboral = ".$row['IDDiaNoLaboral']);
	}

	$usuario = $dbo->getFields("Usuario", array("Nombre","NumeroDocumento"), "IDUsuario = ".$row['IDUsuario']);

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',		
			"NumeroDocumento" => $usuario["NumeroDocumento"],
			"Usuario" => $usuario["Nombre"],	
			"Fecha" => $row["Fecha"],
			"PlanDia" => $planDia,
			"Activo" => $row["Activo"],								
			"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}        

echo json_encode($responce);

?>