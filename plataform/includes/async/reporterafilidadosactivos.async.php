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

$result = $dbo->query("SELECT REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo,  pf.Nombre as producto, cf.Nombre as categoria, f.FechaCreacion as fecha_inicia,f.FechaVence as fecha_vence, fp.Cantidad as cantidad,fp.Precio as valor_producto, s.IDSocio as idsocio, s.Nombre as nombre_socio, s.Apellido as apellido_socio, s.Genero as genero , s.NumeroDocumento as doc, s.FechaNacimiento as nacimiento, TIMESTAMPDIFF(YEAR,s.FechaNacimiento,CURDATE()) AS edad, s.Direccion as direccion, s.CorreoElectronico  as email, s.Telefono as telefono, s.Celular as celular, TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence) AS dias_restantes FROM FacturacionProducto fp, Socio s, CategoriaFacturacion cf, ProductoFacturacion pf,  Facturacion f, Club c WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin') and c.IDClub=f.IDClub and fp.IDFacturacion=f.IDFacturacion and s.IDSocio=f.IDSocio and pf.IDCategoriaFacturacion=cf.IDCategoriaFacturacion and s.IDEstadoSocio=1 and fp.IDProductoFacturacion=pf.IDProductoFacturacion  and TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence)>= 0 ORDER BY f.FechaCreacion desc");
 

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


$sql = "SELECT REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo,  pf.Nombre as producto, cf.Nombre as categoria, f.FechaCreacion as fecha_inicia,f.FechaVence as fecha_vence, fp.Cantidad as cantidad,fp.Precio as valor_producto, s.IDSocio as idsocio, s.Nombre as nombre_socio, s.Apellido as apellido_socio, s.Genero as genero , s.NumeroDocumento as doc, s.FechaNacimiento as nacimiento, TIMESTAMPDIFF(YEAR,s.FechaNacimiento,CURDATE()) AS edad, s.Direccion as direccion, s.CorreoElectronico  as email, s.Telefono as telefono, s.Celular as celular, TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence) AS dias_restantes FROM FacturacionProducto fp, Socio s, CategoriaFacturacion cf, ProductoFacturacion pf,  Facturacion f, Club c WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin') and c.IDClub=f.IDClub and fp.IDFacturacion=f.IDFacturacion and s.IDSocio=f.IDSocio and pf.IDCategoriaFacturacion=cf.IDCategoriaFacturacion and s.IDEstadoSocio=1 and fp.IDProductoFacturacion=pf.IDProductoFacturacion  and TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence)>= 0 ORDER BY f.FechaCreacion desc LIMIT " . $start . "," . $limit;
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
            "producto" =>   $row["producto"],
            "categoria" =>   $row["categoria"],
            "fecha_inicia" => $row["fecha_inicia"],
            "fecha_vence" =>  $row["fecha_vence"],
            "cantidad" =>   $row["cantidad"], 
            "valor_producto" =>   $row["valor_producto"],
            "idsocio" =>   $row["idsocio"],
            "nombre_socio" =>   $row["nombre_socio"],
            "apellido_socio" =>   $row["apellido_socio"],
            "genero" =>   $row["genero"],
            "doc" =>   $row["doc"],
            "nacimiento" =>   $row["nacimiento"],
            "edad" =>   $row["edad"],
            "direccion" =>   $row["direccion"],
            "email" =>   $row["email"],
            "telefono" =>   $row["telefono"],
            "celular" =>   $row["celular"],
            "dias_restantes" =>   $row["dias_restantes"]
            
     
            
            
		);

	$i++;
}        

echo json_encode($responce);

?>
 
