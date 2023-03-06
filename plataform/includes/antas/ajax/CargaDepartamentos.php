<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

$Departamentos = $dbo ->all("Departamento","IDPais = '".$_POST['IDPais']."' ORDER BY Nombre");
	while( $RDepartamentos = $dbo->fetchArray( $Departamentos ) )
		$ArrayDepartamentos[$RDepartamentos[IDDepartamento]]=$RDepartamentos;

?>
{
<?php
$fin=count($ArrayDepartamentos);

foreach($ArrayDepartamentos as $key => $value)
{
	if($fin-- == 1)
	{
	?>
		"<?php echo $value['IDDepartamento'];?>" : "<?php echo $value['Nombre'];?>",
	<?	
	}
	else
	{
	?>
		"<?php echo $value['IDDepartamento'];?>" : "<?php echo $value['Nombre'];?>",
	<?
	}
}
?>
"0" : "[Seleccione]"
}