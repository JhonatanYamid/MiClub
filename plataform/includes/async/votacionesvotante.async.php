<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "VotacionVotante";
$key = "IDVotacionVotante";

//$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' and IDVotacionEvento = '".SIMNet::req("IDVotacionEvento")."' ";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' and IDVotacionEvento = '".$_GET["IDVotacionEvento"]."'";
$script ="votacionesevento";

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

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' or Cedula LIKE '%" . $search_object->data . "%' )";
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

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%' or Cedula LIKE '%" . $qryString . "%' )  ";
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

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i=0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');

while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	if($row["Presente"]=="S"){
		$checksi="checked";
		$checkno="";
	}
	else{
		$checkno="checked";
		$checksi="";
}

	$btn_presente='<input type="radio" value="S" class="btnvotantepresente" name="VotantePresente'.$row[$key].'" idvotante="'.$row[$key].'" usuarioregistra="'.SIMUser::get("IDUsuario").'" '.$checksi.'>Si <input type="radio" value="N" class="btnvotantepresente" name="VotantePresente'.$row[$key].'" idvotante="'.$row[$key].'" usuarioregistra="'.SIMUser::get("IDUsuario").'" '.$checkno.' >No';
	$btn_presente.="<div name='msgupdate".$row[$key]."' id='msgupdate".$row[$key]."'></div>";

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row["IDVotacionEvento"].'&tabclub=votantes&IDVotacionEvento='.$row["IDVotacionEvento"].'&IDVotacionVotante='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Nombre" => utf8_encode($row["Nombre"]),
										"NumeroCasa" => $row["NumeroCasa"],
										"Cedula" => $row["Cedula"],
										"Coeficiente" => $row["Coeficiente"],
										"Consejero" => $row["Consejero"],
										"Moroso" => $row["Moroso"],
										"Presente" => $row["Presente"],
										"EliminarBoton" => '<a class="" href="'.$script.'.php?action=EliminaVotante&id='.$row["IDVotacionEvento"].'&tabclub=votantes&IDVotacionEvento='.$row["IDVotacionEvento"].'&IDVotacionVotante='.$row[$key].''.'"><i class="ace-icon fa fa-trash-o bigger-130"/></a>',
										"Eliminar" => $btn_presente
									);


	$i++;
}

echo json_encode($responce);

?>
