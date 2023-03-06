<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");
$idPadre = SIMUtil::IdPadre($IDClub);
$idSocio = SIMNet::req("idsocio");

$columns = array();
$origen = SIMNet::req("origen");

$table = "FacturacionProducto";
$key = "IDFacturacionProducto";
$where = " WHERE f.Estado != 3 AND f.IDSocio = $idSocio ";
$script = "FacturacionProducto";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;
	case "subgrid":
		$id = $_GET['id'];
		$i=0;
		
		$sqlSub = "SELECT r.IDRegistroSocioProducto, s.NumeroDocumento, CONCAT(s.Nombre,' ',s.Apellido) as Nombre, r.FechaInicio, r.FechaFin 
					FROM RegistroSocioProducto as r, Socio as s, FacturacionProducto as fp
					WHERE 
						r.IDSocio = s.IDSocio AND fp.IDFacturacion = r.IDFacturacion AND 
						fp.IDProductoFacturacion = r.IDProductoFacturacion AND fp.IDFacturacionProducto = $id";
						
		$resultSub = $dbo->query($sqlSub);

		while($row = $dbo->fetchArray($resultSub)) {
			$responce->rows[$i]['id'] = $row['IDRegistroSocioProducto'];
			$responce->rows[$i]['cell'] = array($row['NumeroDocumento'],$row['Nombre'],$row['FechaInicio'],$row['FechaFin']);
			$i++;
		} 

		echo json_encode($responce);
		exit;
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "FechaCreacion";
// connect to the database
$sqlCount = "SELECT COUNT(*) AS count
			FROM FacturacionProducto as fp, Facturacion as f, Club as c, Socio as s, ProductoFacturacion as p
				".$where ." AND (fp.IDFacturacion = f.IDFacturacion AND f.IDClub = c.IDClub AND f.IDSocio = s.IDSocio AND fp.IDProductoFacturacion = p.IDProductoFacturacion)";

$result = $dbo->query($sqlCount);
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


$sql = "SELECT IDFacturacionProducto, IF(IDClubPadre = 157,REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH'), c.Nombre) as Sede, 
			CONCAT(Prefijo,Consecutivo) as Consecutivo, FechaCreacion, p.Nombre as Producto, Cantidad, fp.Total, fp.IDFacturacion
		FROM FacturacionProducto as fp, Facturacion as f, Club as c, Socio as s, ProductoFacturacion as p
		".$where ." AND (fp.IDFacturacion = f.IDFacturacion AND f.IDClub = c.IDClub AND f.IDSocio = s.IDSocio 
			AND fp.IDProductoFacturacion = p.IDProductoFacturacion) ORDER BY $sidx $sord " . $str_limit;

$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$responce->rows[$i]['cell'] = array( 
		$key => $row[$key],
		"Ver" => '<a class="blue" title="Ver Factura" href="facturacion.php?action=factura&id='.$row['IDFacturacion'].''.'"><i class="ace-icon fa fa-eye bigger-130"/></a>',
		"Sede" => $row["Sede"],
		"Consecutivo" => $row["Consecutivo"],
		"FechaCreacion" => $row["FechaCreacion"],
		"Producto" => $row["Producto"],
		"Cantidad" => $row["Cantidad"],
		"Total" => $row["Total"],
	);

	$i++;
}        

echo json_encode($responce);

?>