<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");
$idPadre = SIMUtil::IdPadre($IDClub);
$arrHijos = SIMUtil::ObtenerHijosClubPadre($idPadre);

$columns = array();
$origen = SIMNet::req("origen");

$table = "ProductoFacturacion";
$key = "IDProductoFacturacion";
$where = " WHERE p.IDClub = $idPadre ";
$script = "productofacturacion";

if($IDClub != $idPadre )
	$where .= " AND IDProductoFacturacion IN (SELECT IDProductoFacturacion FROM ProductoPrecio WHERE IDClub = $IDClub) ";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{
	case "del":

		$sql_delete = "DELETE FROM ProductoFacturacion WHERE IDProductoFacturacion = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre";
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
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;

	case "form":
		$proceso = $_GET['proceso'];
		
		switch ($proceso) {
			case 'select':
				$idCat = $_GET['idCat'];
				$value = $_GET['val'];
				$whTipo = $IDClub == $idPadre ? "" : "AND IDTipoFacturacion IN (SELECT IDTipoFacturacion FROM TipoFacturacionClub WHERE IDClub = $IDClub)";

				$menu = SIMHTML::formPopup('TipoFacturacion', 'Nombre', 'Nombre', 'IDTipoFacturacion', $value, SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), 'mandatory', 'onchange="changeTipoFac()" title="'.SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION).'"', "AND IDCategoriaFacturacion = $idCat AND Activo = 'S' AND IDClub = $idPadre $whTipo");

				echo $menu;
				exit;
			break;

			case 'divs':
				$idTipo = $_GET['idTipo'];
				$arrResponce = array();
				
				$sqlTipo = "SELECT * FROM TipoFacturacion WHERE IDTipoFacturacion = $idTipo";
				$resultTipo = $dbo->query($sqlTipo);
				$arrResponce = $dbo->fetchArray($resultTipo);
				
				echo json_encode($arrResponce);
				exit;
			break;

			case "idServicio":
				$idServicio = $_GET['idServicio'];
				$sqlServicio = "SELECT sc.IDClub 
								FROM ServicioClub as sc, Club as c
								WHERE 
									sc.IDClub = c.IDClub AND c.IDClubPadre = $idPadre AND sc.IDServicioMaestro = $idServicio AND  sc.Activo = 'S'";

				$resultServicio = $dbo->query($sqlServicio);
				
				while($rowServicio = $dbo->fetchArray($resultServicio)){
					$arrResponce[] = $rowServicio['IDClub'];
				}
				
				echo json_encode($arrResponce);
				exit;
			break;
			
			default:
				
			break;
		}

	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if(!empty($qryString)){
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

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table ."as p ". $where);
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

$sql = "SELECT p.IDProductoFacturacion,c.Nombre as Categoria, p.Nombre, p.Descripcion, t.Nombre as Tipo, p.Codigo, p.CuentaContable, p.Activo, p.Editar, p.Eliminar 
		FROM ProductoFacturacion as p, CategoriaFacturacion as c, TipoFacturacion as t 
		". $where . " AND (p.IDCategoriaFacturacion = c.IDCategoriaFacturacion AND p.IDTipoFacturacion = t.IDTipoFacturacion) ORDER BY $sidx $sord " . $str_limit;
//$sql = "SELECT " . $table . ".* FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
//echo $sql;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];
	$editar = '';
	$eliminar = '';

	if($row['Editar'] == 'S' || $IDClub == $idPadre)
		$editar = '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>';

	if($row['Eliminar'] == 'S' || $IDClub == $idPadre)
		$eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
	
	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Editar" => $editar,
			"Categoria" => $row["Categoria"],
			"Tipo" => $row["Tipo"],
			"Nombre" => $row["Nombre"],
			"Descripcion" => $row["Descripcion"],
			"Codigo" => $row["Codigo"],
			"Activo" => $row["Activo"],	
			"Eliminar" => $eliminar
		);

	$i++;
}        

echo json_encode($responce);

?>