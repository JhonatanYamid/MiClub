<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
/* SIMUtil::cache( "text/json" ); */
$dbo =& SIMDB::get();

$pais = $_POST["pais"];
$sqlCiudad="SELECT Nombre, IDCiudad FROM Ciudad WHERE IDPais = '".$pais."' ORDER BY Nombre ASC";
$qryCiudad = $dbo->query($sqlCiudad);

$cadena = "<select name = 'IDCiudad' id='IDCiudad' title='Ciudad' class='form-control mandatory' placeholder='Ciudad' required >";
$cadena .= "<option value='0'>Seleccione la Ciudad</option>";

while ($r_tipoinv = $dbo->fetchArray($qryCiudad)):
	$cadena .= "<option value= ".$r_tipoinv['IDCiudad']." > ".utf8_decode($r_tipoinv['Nombre'])."</option>";
endwhile;

$cadena .= "</select>";

echo  $cadena

?>
