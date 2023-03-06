<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();


$table = "Estudio";
$key = "IDEstudio";
$origen = SIMNet::req("origen");

if( SIMUser::get("IDAgencia") == '0' ){ 
	$age = $_GET['idag'];
	if (($age == '')or($age == '0')){
		$where = "";	
	} else {
		$where = " WHERE IDAgencia = '$age' ";
	}
} else {
	$age = SIMUser::get("IDAgencia");
	$where = " WHERE IDAgencia = '$age' ";
}

switch( SIMNet::req("oper") )
{
	case "del":
		$array_id = explode(",", $frm["id"]);
		foreach( $array_id as $key_id => $value_id )
		{
			$sql_del = "DELETE FROM " . $table . " WHERE " . $key . " = '" . $value_id . "' ";
			$qry_del = $dbo->query( $sql_del );
		}//end for
	break;
	case "search":
		$frm_get =  SIMUtil::makeSafe( $_GET );
		if( !empty( $frm_get["strSearch"] ) )
			$where .= " WHERE ( Nombre LIKE '%" . $frm_get["strSearch"] . "%'  ) ";

		if( !empty( $frm_get["idcategoria"] ) )
			$where .= " WHERE ( IDCategoria = '" . $frm_get["idcategoria"] . "'  ) ";

			
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'IDEstudio'; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . "   ");
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


$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT $start , $limit";
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];
	
	$estado = $row['Estado'];
	$Categoria = $dbo->getFields( "Categoria", "Nombre", "IDCategoria = '" . $row["IDCategoria"] . "'" );
	$ide = $row['IDEstudio'];
	$sql1 = "SELECT count(*) as cantasis FROM Estudioasistente WHERE IDEstudio = '$ide';";
	$result1 = $dbo->query( $sql1 );
	$row1 = $dbo->fetchArray($result1);
	$link = "estudios_admin.php?action=edit&id=" . $row[$key];
	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	
	
	if (($estado == 'Abierto')and($row1["cantasis"] < 1)){
		$categoria = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" . $Categoria . "</a>";
		$nombre = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" . $row["Nombre"] . "</a>";
		$fechainicio = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" . $row["FechaInicio"] . "</a>";
		$fechafin = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" . $row["FechaFin"] . "</a>";
		$estadoestudio = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" . $row["Estado"] . "</a>";
		$verasis = "<a href='asistentes.php?ide=".$row["IDEstudio"]."'>Asistentes</a>";
		$agreasis = "<a href='todosasistentes.php?ide=".$row["IDEstudio"]."'>Agregar Asistentes</a>";
	
	} else if (($estado == 'Abierto')and($row1["cantasis"] > 0)){
		$categoria = $Categoria;
		$nombre = $row["Nombre"];
		$fechainicio  = $row["FechaInicio"];
		$fechafin = $row["FechaFin"];
		$estadoestudio = $row["Estado"];
		$verasis = "<a href='asistentes.php?ide=".$row["IDEstudio"]."'>Asistentes</a>";
		$agreasis = "<a href='todosasistentes.php?ide=".$row["IDEstudio"]."'>Agregar Asistentes</a>";
		
	}  else if ($estado == 'Cerrado'){
		$categoria = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" .$Categoria. "</a>";
		$nombre = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" .$row["Nombre"]. "</a>";
		$fechainicio  = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" .$row["FechaInicio"]. "</a>";
		$fechafin = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" .$row["FechaFin"]. "</a>";
		$estadoestudio = "<a href='" . $link . "' class='" . $class . "' " . $attr . ">" .$row["Estado"]. "</a>";
		$verasis = "<a href='asistentes.php?ide=".$row["IDEstudio"]."'>Asistentes</a>";
		$agreasis = 'No Disponible';
	}
	
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$categoria,
										$nombre,
										$fechainicio,
										$fechafin,
										$estadoestudio,
										$verasis,
										$agreasis
									);

	else
		$responce->rows[$i]['cell'] = array(
										"Categoria" => $Categoria,
										"Nombre" => $row["Nombre"],
										"FechaInicio" => $row["FechaInicio"],
										"FechaFin" => $row["FechaFin"],
										"canasis" => $row1["cantasis"],
										"Estado" => $row["Estado"]
									);


	$i++;
}        
echo json_encode($responce);

?>