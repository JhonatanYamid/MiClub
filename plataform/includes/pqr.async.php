<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


if(SIMUser::get("IDPerfil") == 11):
	$condicion_area = " and IDArea = '".SIMUser::get("IDArea")."'";
endif;	

$table = "Pqr";
$key = "IDPqr";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' " . $condicion_area;
$script ="pqr";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Apellido ASC, Nombre";
		$_GET['sord'] = "ASC";


	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "IDPqr";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = mysql_fetch_array($result,MYSQL_ASSOC);
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



$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
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
	
	
	switch($row["Tipo"]){
		case "P":
			$tipo_pqr = "Peticion";
		break;	
		case "Q":
			$tipo_pqr = "Queja";
		break;	
		case "R":
			$tipo_pqr = "Reclamo";
		break;	
		
	}
	
	
	
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Numero" => $row["IDPqr"],	
										"Fecha" => substr($row["Fecha"],0,10),	
										"Tipo" =>  utf8_encode($dbo->getFields( "TipoPqr" , "Nombre" , "IDTipoPqr = '" .$row["IDTipoPqr"] . "'" )),
										"Area" => utf8_encode($dbo->getFields( "Area" , "Nombre" , "IDArea = '" .$row["IDArea"] . "'" )),
										"TipoSocio" => $dbo->getFields( "Socio" , "TipoSocio" , "IDSocio = '" .$row["IDSocio"] . "'" ),
										"Socio" => $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$row["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$row["IDSocio"] . "'" ),										
										"Asunto" => utf8_encode($row["Asunto"]),										
										"Descripcion" => utf8_encode(substr($row["Descripcion"],0,50)),			
										"Estado" =>  $dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $row["IDPqrEstado"] . "'" ),										
										"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
									);
	else
		$responce->rows[$i]['cell'] = array( 
										$key => $row[$key],
										"Editar" => '<div class="hidden-sm hidden-xs action-buttons"><a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a></div>',
										"Numero" => $row["IDPqr"],	
										"Fecha" => substr($row["Fecha"],0,10),	
										"Tipo" => $tipo_pqr,										
										"Area" => $dbo->getFields( "Area" , "Nombre" , "IDArea = '" .$row["IDArea"] . "'" ),
										"Socio" => $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$row["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$row["IDSocio"] . "'" ),										
										"Asunto" => $row["Asunto"],										
										"Descripcion" => substr($row["Descripcion"],0,50),										
										"Estado" =>  $dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $row["IDPqrEstado"] . "'" ),	
										"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
									);

	$i++;
}        

echo json_encode($responce);

?>