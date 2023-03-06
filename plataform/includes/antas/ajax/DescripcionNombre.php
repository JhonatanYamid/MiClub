<?php
include( "../config.inc.php" ); 
$dbo =& SIMDB::get();
$dbo->query( "UPDATE $_POST[Tabla] SET  Descripcion = '" . $_POST[Descripcion] . "' , Nombre = '" . $_POST[Nombre] . "' WHERE $_POST[id] = '" . $_POST[val] . "' " );
echo $_POST[des];
?>