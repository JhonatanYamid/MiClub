<?php

include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Usuario";
$key = "IDUsuario";
$where = " WHERE 1 ";
$script ="usuarios";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";


if(SIMUser::get( "Nivel" ) > 0 ):
	$condicion_club = " and IDClub = '".SIMUser::get("club")."'";
endif;

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

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' OR Email LIKE '%" . $search_object->data . "%' OR User LIKE '%" . $search_object->data . "%' OR NumeroDocumento LIKE '%" . $search_object->data . "%' )  ";
				break;
				case 'Club':
					$sql_club = "Select * From Club Where Nombre Like '%".$search_object->data."%'";
					$result_club = $dbo->query($sql_club);
					while($row_club = $dbo->fetchArray($result_club)):
						$array_id_club []= $row_club["IDClub"];
					endwhile;
					if(count($array_id_club)>0):
						$id_club_busqueda = implode(",",$array_id_club);
					endif;
					$where .= " AND IDClub in (".$id_club_busqueda.")";

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

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%' OR Email LIKE '%" . $qryString . "%' OR User LIKE '%" . $qryString . "%' OR NumeroDocumento LIKE '%" . $qryString . "%')  ";
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



 $sql = "SELECT " . $table . ".* FROM " . $table . $where . " " . $condicion_club. " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];


	$Nombre= SIMUTil::remplaza_acentos($row["Nombre"]);

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Club" =>  $dbo->getFields( "Club" , "Nombre" , "IDClub = '" . $row["IDClub"] . "'" ) ,
										"Nombre" => utf8_encode($Nombre),
										"Email" => $row["Email"],
										"User" => $row["User"],
										"NumeroDocumento" => $row["NumeroDocumento"],
										"Autorizado" => $row["Autorizado"],
										"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
									);

	$i++;
}

echo json_encode($responce);

?>
