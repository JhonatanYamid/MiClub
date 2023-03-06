<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "CursoHorario";
$key = "IDCursoHorario";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="cursohorario";

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

				case 'Sede':
					//busco el area
					$sql_sede = "Select * From CursoSede Where Nombre LIKE '%" . $search_object->data . "%'";
					$result_sede = $dbo->query($sql_sede);
					while($row_sede = $dbo->fetchArray($result_sede)):
						$array_id_sede[]=$row_sede["IDCursoSede"];
					endwhile;
					if(count($array_id_sede)>0):
						$id_sede_buscar = implode(",",$array_id_sede);
					else:
						$id_sede_buscar = 0;
					endif;

					$where .= " AND   IDCursoSede in (".$id_sede_buscar.")";

				break;

				case 'Nivel':
					//busco el area
					$sql_nivel = "Select * From CursoNivel Where Nombre LIKE '%" . $search_object->data . "%'";
					$result_nivel = $dbo->query($sql_nivel);
					while($row_nivel = $dbo->fetchArray($result_nivel)):
						$array_id_nivel[]=$row_nivel["IDCursoNivel"];
					endwhile;
					if(count($array_id_nivel)>0):
						$id_nivel_buscar = implode(",",$array_id_nivel);
					else:
						$id_nivel_buscar = 0;
					endif;

					$where .= " AND   IDCursoNivel in (".$id_nivel_buscar.")";

				break;

				case 'Edad':
					//busco el area
					$sql_edad = "Select * From CursoEdad Where Nombre LIKE '%" . $search_object->data . "%'";
					$result_edad = $dbo->query($sql_edad);
					while($row_edad = $dbo->fetchArray($result_edad)):
						$array_id_edad[]=$row_edad["IDCursoEdad"];
					endwhile;
					if(count($array_id_edad)>0):
						$id_edad_buscar = implode(",",$array_id_edad);
					else:
						$id_edad_buscar = 0;
					endif;

					$where .= " AND   IDCursoEdad in (".$id_edad_buscar.")";

				break;

				case 'Entrenador':
					//busco el area
					$sql_entre = "Select * From CursoEntrenador Where Nombre LIKE '%" . $search_object->data . "%'";
					$result_entre = $dbo->query($sql_entre);
					while($row_entre = $dbo->fetchArray($result_entre)):
						$array_id_entre[]=$row_entre["IDCursoEntrenador"];
					endwhile;
					if(count($array_id_entre)>0):
						$id_entre_buscar = implode(",",$array_id_entre);
					else:
						$id_entre_buscar = 0;
					endif;

					$where .= " AND   IDCursoEntrenador in (".$id_entre_buscar.")";

				break;


				case 'qryString':

					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
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

			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
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
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"Nombre" => utf8_encode($row["Nombre"]),
										"Sede" => utf8_encode($dbo->getFields( "CursoSede" , "Nombre" , "IDCursoSede = '" . $row["IDCursoSede"] . "'")),
										"Nivel" => utf8_encode($dbo->getFields( "CursoNivel" , "Nombre" , "IDCursoNivel = '" . $row["IDCursoNivel"] . "'")),
										"Edad" => utf8_encode($dbo->getFields( "CursoEdad" , "Nombre" , "IDCursoEdad = '" . $row["IDCursoEdad"] . "'")),
										"ValorMes" => "$".number_format($row["ValorMes"],0,',','.'),
										"ValorTrimestre" => "$".number_format($row["ValorTrimestre"],0,',','.'),
										"HoraDesde" => $row["HoraDesde"],
										"HoraHasta" => $row["HoraHasta"],
										"Entrenador" => utf8_encode($dbo->getFields( "CursoEntrenador" , "Nombre" , "IDCursoEntrenador = '" . $row["IDCursoEntrenador"] . "'")),
										"Publicar" => $row["Publicar"],
										"Eliminar" => '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
									);

	$i++;
}

echo json_encode($responce);

?>
