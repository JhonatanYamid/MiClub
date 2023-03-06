<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "DocumentoPersonal";
$key = "IDDocumentoPersonal";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="documentospersonal";

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

				case 'Socio':

					$where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;

				case 'qryString':

					$where .= " AND ( ".$table.".Nombre LIKE '%" . $search_object->data . "%' or ".$table."Descripcion LIKE '%" . $search_object->data . "%' )";
				break;

				default:
					$where .=  $array_buqueda->groupOp . " $table." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
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



if(!$sidx) $sidx = "IDDocumentoPersonal";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 1;
}



if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)




if( empty( $limit ) )
	$limit = 1000000;



$sql = "SELECT " . $table . ".* FROM " . $table . ", Socio S ". $where . " and S.IDSocio = DocumentoPersonal.IDSocio "." ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
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

	$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array" );

	//Para el Rancho todos pueden cancelar reserva
	//if(SIMUser::get("IDPerfil") == 0):
			$btn_eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
		//else:
			//$btn_eliminar = '';
		//endif;

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Accion" => utf8_encode($datos_socio["Accion"]) ,
										"Socio" => utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) ,
										"Nombre" => utf8_encode($row["Nombre"]) ,
										"Publicar" => utf8_encode($row["Publicar"]) ,

										"Descripcion" => $row["Descripcion"],

										"Eliminar" => $btn_eliminar
									);

	$i++;
}



echo json_encode($responce);

?>
