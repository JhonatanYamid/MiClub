<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "ValetParking";
$key = "IDValetParking";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="valet";

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

				case 'Tercero':

					$where .= " AND (  NombreTercero LIKE '%" . $search_object->data . "%' or CedulaTercero LIKE '%" . $search_object->data . "%'  )  ";
				break;

				case 'RecibidoPor':
					//busco el area
					$sql_busq = "Select * From Usuario Where IDClub = '".SIMUser::get("club")."' and Nombre like '%".$search_object->data."%' ";
					$result_busq = $dbo->query($sql_busq);
					while($row_busq = $dbo->fetchArray($result_busq)):
						$array_id_usuario[]=$row_busq["IDUsuario"];
					endwhile;
					if(count($array_id_usuario)>0):
						$id_usuario_buscar = implode(",",$array_id_usuario);
					else:
						$id_usuario_buscar = 0;
					endif;

					$where .= " AND   IDUsuarioRecibe in (".$id_usuario_buscar.")";

				break;

				case 'EntregadoPor':
					//busco el area
					$sql_busq = "Select * From Usuario Where IDClub = '".SIMUser::get("club")."' and Nombre like '%".$search_object->data."%' ";
					$result_busq = $dbo->query($sql_busq);
					while($row_busq = $dbo->fetchArray($result_busq)):
						$array_id_usuario[]=$row_busq["IDUsuario"];
					endwhile;
					if(count($array_id_usuario)>0):
						$id_usuario_buscar = implode(",",$array_id_usuario);
					else:
						$id_usuario_buscar = 0;
					endif;

					$where .= " AND   IDUsuarioEntrega in (".$id_usuario_buscar.")";

				break;


				case 'Responsable':
					//busco el tipo
					$sql_area_pqr = "Select * From Area Where (Nombre LIKE '%" . $search_object->data . "%' or  Responsable LIKE '%" . $search_object->data . "%' ) and IDClub = '".SIMUser::get("club")."'";
					$result_area_pqr = $dbo->query($sql_area_pqr);
					while($row_area_pqr = $dbo->fetchArray($result_area_pqr)):
						$array_id_area[]=$row_area_pqr["IDArea"];
					endwhile;
					if(count($array_id_area)>0):
						$id_area_buscar = implode(",",$array_id_area);
					else:
						$id_area_buscar = 0;
					endif;

					$where .= " AND   IDArea in (".$id_area_buscar.")";

				break;

				default:
					 $where .=   $array_buqueda->groupOp . " " .$search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for




	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{

			$where .= " AND ( Estado LIKE '%" . $qryString . "%' or Placa LIKE '%" . $qryString . "%' or CedulaTercero LIKE '%" . $qryString . "%' or NombreTercero LIKE '%" . $qryString . "%'  or Nombre LIKE '%" . $qryString . "%'   or Apellido LIKE '%" . $qryString . "%')  ";
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



$sql = "SELECT " . $table . ".* FROM " . $table . ", Socio S ". $where . " and S.IDSocio = ValetParking.IDSocio "." ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
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


		//Para el Rancho todos pueden cancelar reserva
		if(SIMUser::get("IDPerfil") <= 1 ):
				$btn_eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
		else:
			$btn_eliminar = '';
		endif;




	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Socio" => utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$row["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$row["IDSocio"] . "'" )),
										"RecibidoPor" => utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$row["IDUsuarioRecibe"] . "'" ) ),
										"EntregadoPor" => utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$row["IDUsuarioEntrega"] . "'" ) ),
										"Placa" => $row["Placa"],
										"Tercero" => $row["CedulaTercero"] . " " . $row["NombreTercero"],
										"FechaRecibido" => $row["FechaRecibe"],
										"FechaEntregado" => $row["FechaEntrega"],
										"NumeroParqueadero" => $row["NumeroParqueadero"],
										"Estado" => $row["Estado"],
										"Eliminar" => $btn_eliminar
									);


	$i++;
}

echo json_encode($responce);

?>
