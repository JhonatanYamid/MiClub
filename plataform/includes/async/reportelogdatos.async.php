<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "LogCambioDatos";
$key = "IDLogCambioDatos";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="reportelogdatos";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Socio WHERE IDSocio = '" . $_POST["id"] . "' AND IDClub = '" . SIMUser::get("club") . "'  LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre ASC";
		$_GET['sord'] = "ASC";


	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'Accion':
					$sql_accion = "Select * From Socio Where Accion = '".$search_object->data."' and IDClub = '".SIMUser::get("club")."'";
					$result_accion = $dbo->query($sql_accion);
					while($row_accion = $dbo->fetchArray($result_accion)):
						$array_accion[]= $row_accion["IDSocio"];
					endwhile;
					if(count($array_accion)>0):
						$id_socio = implode(",",$array_accion);
					else:
						$id_socio = 0;
					endif;
					
					$where .= " AND  ValorID in (".$id_socio.") ";
				break;
				
				case 'qryString':
					
					$where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '" . $search_object->data . "%' OR Accion LIKE '" . $search_object->data . "%' OR Email LIKE '" . $search_object->data . "%' )  ";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . " " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( Socio.Nombre LIKE '%" . $qryString . "%' OR Socio.Apellido LIKE '%" . $qryString . "%' OR Socio.NumeroDocumento LIKE '" . $qryString . "%' OR Accion LIKE '" . $qryString . "%' OR Email LIKE '".$qryString."%')  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
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

//$limit = 58;
$sql = "SELECT * FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce = "";


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	
	$responce->rows[$i]['id'] = $row[$key];
	
	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	
	if($row["Tabla"]=="Socio"):
		$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["ValorID"] . "' ", "array" );
	endif;
	
	
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
										$key => $row[$key],	
										"Accion" => $datos_socio["Accion"],
										"Fecha" => utf8_encode( $row["Fecha"] ),
										"Campo" => utf8_encode($row["Campo"]),										
										"NuevoDato" => utf8_encode($row["NuevoDato"]),
										"NombreUsuario" => utf8_encode($row["NombreUsuario"])
									);
	

	$i++;


}   



echo json_encode($responce);

?>