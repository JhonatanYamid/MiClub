<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$accion = SIMNet::req("accion");

$table = "Socio";
$key = "IDSocio";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";


					
$where .= " AND (  Accion = '" . $accion . "' OR AccionPadre = '" . $accion . "'  )  ";





$sql = "SELECT " . $table . ".*, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . $where . " ORDER BY Apellido " ;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$i=0;

while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];
	
	
	$responce->rows[$i] = array( 
									$key => $row[$key],
									"TipoSocio" => $row["TipoSocio"],
									"Accion" => $row["Accion"],
									"Socio" => $row["Socio"],
									"Nombre" => $row["Nombre"],
									"Apellido" => $row["Apellido"]
								);

	$i++;
}        

echo json_encode($responce);

?>