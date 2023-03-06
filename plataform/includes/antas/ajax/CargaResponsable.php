<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );
$Responsables = $dbo ->query("Select * from Usuario Where IDArea = '".$_POST['IDArea']."' ORDER BY Nombre");
while( $RResponsables = $dbo->fetchArray( $Responsables ) )
	$ArrayResponsable[$RResponsables[IDUsuario]]=$RResponsables;
?>
{
<?php
$fin=count($ArrayResponsable);
foreach($ArrayResponsable as $key => $value)
{
	if($fin-- == 1)
	{
	?>
		"<?php echo $value['IDUsuario'];?>" : "<?php echo $value['Nombre'];?>"
	<?	
	}
	else
	{
	?>
		"<?php echo $value['IDUsuario'];?>" : "<?php echo $value['Nombre'];?>",
	<?
	}
}
?>
}