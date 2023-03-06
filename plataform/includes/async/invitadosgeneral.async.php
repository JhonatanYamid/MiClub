<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Invitado";
$key = "IDInvitado";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="invitadosgeneral";

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

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' or Apellido LIKE '%" . $search_object->data . "%' )";
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  Invitado." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for




	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%' or Apellido LIKE '%" . $qryString . "%'  or NumeroDocumento Like '%".$qryString."%')  ";
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
//$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT 58" ;
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


	if(SIMUser::get("IDPerfil") <= 2 ):
			$btn_eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
		else:
			$btn_eliminar = '';
		endif;


		$sql_arl="SELECT IDArl,Nombre FROM Arl Where 1";
		$r_arl=$dbo->query($sql_arl);
		while($row_d=$dbo->fetchArray($r_arl)){
			$array_arl[$row_d["IDArl"]]=$row_d["Nombre"];
		}


		$sql_eps="SELECT IDEps,Nombre FROM Eps Where 1";
		$r_eps=$dbo->query($sql_eps);
		while($row_d=$dbo->fetchArray($r_eps)){
			$array_eps[$row_d["IDEps"]]=$row_d["Nombre"];
		}

		$sql_afp="SELECT IDAfp,Nombre FROM Afp Where 1";
		$r_afp=$dbo->query($sql_afp);
		while($row_d=$dbo->fetchArray($r_afp)){
			$array_afp[$row_d["IDAfp"]]=$row_d["Nombre"];
		}

		$sql_tipoinv="SELECT IDTipoInvitado,Nombre FROM TipoInvitado Where IDClub= '".SIMUser::get("club")."'";
		$r_tipoinv=$dbo->query($sql_tipoinv);
		while($row_d=$dbo->fetchArray($r_tipoinv)){
			$array_tipoinv[$row_d["IDTipoInvitado"]]=$row_d["Nombre"];
		}

		$sql_clasifinv="SELECT IDClasificacionInvitado,Nombre FROM ClasificacionInvitado Where 1 ";
		$r_clasifinv=$dbo->query($sql_clasifinv);
		while($row_d=$dbo->fetchArray($r_clasifinv)){
			$array_clasifinv[$row_d["IDClasificacionInvitado"]]=$row_d["Nombre"];
		}

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"NumeroDocumento" => $row["NumeroDocumento"],
										"Nombre" => utf8_encode($row["Nombre"]),
										"Apellido" => utf8_encode($row["Apellido"]),
										"Email" => $row["Email"],
										"Tipo" => utf8_encode($array_tipoinv[$row["IDTipoInvitado"]]),
										"Clasificacion" => utf8_encode($array_clasifinv[$row["IDClasificacionInvitado"]]),
										"Arl" => $row["FechaVencimientoArl"],
										"Predio" => $row["Predio"],
										"Eliminar" => $btn_eliminar
									);
	$i++;
}

echo json_encode($responce);

?>
