<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");
$servicio = $_GET['servicio'];
                             //VALIDAMOS EL PERMISO DE RESERVAR PARA MOSTRAR LOS DATOS CON ESE PERMISO

                                $permite = $dbo->getFields("Servicio", "PermiteReservar", "IDServicio = '" . $servicio . "'");
       
                                if($permite=='S'):
                                $permiso="AND PermiteReservar='$permite'";
                                elseif($permite=='N'):
                                $permiso="AND PermiteReservar='$permite'";
                                else:
                                $permiso="AND PermiteReservar=''";
                                endif;
                                
                                //FIN VALIDACION
                               
                                

$table = "SocioPermisoReserva";
$key = "IDSocioPermisoReserva";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  " . $permiso . " AND ". $table .".IDServicio = '".$servicio."' ";

$script ="serviciosclub";

$servicio = $_GET['servicio'];
$club = $_GET['IDClub'];
$accion = $_GET['accion'];                         
if(!empty($servicio) and !empty($accion) and !empty($servicio)):

$sql_delete_datos = "DELETE FROM SocioPermisoReserva   WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  " . $permiso . " AND ". $table .".IDServicio = '".$servicio."' ";
		 
		$qry_delete_datos = $dbo->query( $sql_delete_datos );
		 
		header("Location: ../../serviciosclub.php?action=edit&ids=".$servicio."");
				 
		 
		
endif;


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
					
					$where .= " AND ( NumeroDocumento LIKE '%" . $search_object->data . "%' )";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( NumeroDocumento LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
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



$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
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
			"Ver" => '<a class="green" href="'.$script.'.php?ids='.$servicio.'&action=editarPermiso&tab=permisos&tab1=individual&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = "'.$script.'.php?ids='.$servicio.'&action=editarPermiso&tab=permisos&tab1=lista&id='.$row[$key].''.'" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
										
									);
	
	$i++;
}        

echo json_encode($responce);

?>
