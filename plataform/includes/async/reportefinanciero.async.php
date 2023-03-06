<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");
  
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
		$_GET['sidx'] = "Nombre ASC";
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
 
$orden=$_GET["orden"];
$club=$_GET["club"];
$inicio=$_GET["inicio"];
$fin=$_GET["fin"];

if($club==157):
$sqlSede = "SELECT r.IDClub as club FROM  ResolucionFactura as r, Club as c WHERE r.IDClub = c.IDClub AND r.Activo = 'S' AND c.IDClubPadre = $club";
$qrySede = $dbo->query($sqlSede);
$club="";
while($row = $dbo->fetchArray($qrySede)) {
$club.=$row["club"].",";
}

$club = substr($club, 0, -1);

endif;

$result = $dbo->query("SELECT fp.IDFacturacion, c.IDClub as idclub,pf.Nombre as producto, fp.Precio as precio_producto, m.Nombre as mediopago, (fmp.ValorPagado) as debito, (fmp.ValorPagado-f.Total) as credito , s.NumeroDocumento as doc, REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, f.FechaCreacion as fecha, pf.CuentaContable as cuenta_contable, pf.Codigo as codigo, fp.Cantidad as cantidad FROM FacturacionProducto fp, MediosPago m, Facturacion f, Club c, Socio s, ProductoFacturacion pf, FacturacionMediosPago fmp WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin')  and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and f.IDFacturacion=fp.IDFacturacion and m.IDMediosPago=fmp.IDMediosPago and pf.IDProductoFacturacion=fp.IDProductoFacturacion and fmp.IDFacturacion=f.IDFacturacion ORDER BY f.IDFacturacion desc ");
 

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database
  
$count=0;
while($row = $dbo->fetchArray($result)) {
$count++;
}
if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;

 
 
/*
$sql = "SELECT COUNT(*) as cantidad, m.Nombre as mediopago, IDFacturacion, sum(ValorPagado) as totalpagado FROM `FacturacionMediosPago`, MediosPago m WHERE IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE IDClub=$club  and FechaCreacion>='$inicio' and FechaCreacion<='$fin') and FacturacionMediosPago.IDMediosPago=m.IDMediosPago GROUP by FacturacionMediosPago.IDMediosPago ORDER BY $orden desc LIMIT " . $start . "," . $limit; */


$sql = "SELECT fp.IDFacturacion, c.IDClub as idclub,pf.Nombre as producto, fp.Precio as precio_producto, m.Nombre as mediopago, (fmp.ValorPagado) as debito, (fmp.ValorPagado-f.Total) as credito , s.NumeroDocumento as doc, REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, f.FechaCreacion as fecha, pf.CuentaContable as cuenta_contable, pf.Codigo as codigo, fp.Cantidad as cantidad FROM FacturacionProducto fp, MediosPago m, Facturacion f, Club c, Socio s, ProductoFacturacion pf, FacturacionMediosPago fmp WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin')  and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and f.IDFacturacion=fp.IDFacturacion and m.IDMediosPago=fmp.IDMediosPago and pf.IDProductoFacturacion=fp.IDProductoFacturacion and fmp.IDFacturacion=f.IDFacturacion ORDER BY f.IDFacturacion desc LIMIT " . $start . "," . $limit;
// echo $sql;
// exit;
// var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

while($row = $dbo->fetchArray($result)) {

 
	$responce->rows[$i]['id'] = $row["fp.IDFacturacion"];
    
 

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
	    $key => $row["IDFacturacion"], 
            "club" =>   $row["club"], 
            "factura" =>  $row["Consecutivo"], 
            "id_tercero" =>   $row["doc"],
            "producto" =>   $row["producto"],
            "cuenta_contable" =>   $row["cuenta_contable"],
            "codigo" =>   $row["codigo"],
            "cantidad" =>   $row["cantidad"],
            "precio_producto" =>   $row["precio_producto"], 
            "mediopago" =>   $row["mediopago"], 
            "debito" => $row["debito"],
            "credito" =>  $row["credito"],   
            "fecha" =>   $row["fecha"]
             
            
		);

	$i++;
}        

echo json_encode($responce);

?>
 
