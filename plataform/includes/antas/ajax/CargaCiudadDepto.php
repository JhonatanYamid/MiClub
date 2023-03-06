<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

//$Ciudades = $dbo ->all("Ciudad","IDPais = '".$_POST['IDPais']."' ORDER BY Nombre");
$Ciudades = $dbo ->query("Select * from Ciudad Where IDLenguaje = 1 AND IDDepartamento = '".$_POST['IDDepartamento']."' ORDER BY Nombre");
	while( $RCiudades = $dbo->fetchArray( $Ciudades ) )
		$ArrayCiudad[$RCiudades[IDCiudad]]=$RCiudades;

?>
{
<?php
$fin=count($ArrayCiudad);
foreach($ArrayCiudad as $key => $value)
{
	if($fin-- == 1)
	{
	?>
		"<?php echo $value['IDCiudad'];?>" : "<?php echo $value['Nombre'];?>"
	<?	
	}
	else
	{
	?>
		"<?php echo $value['IDCiudad'];?>" : "<?php echo $value['Nombre'];?>",
	<?
	}
}
?>
}