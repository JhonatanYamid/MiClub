<?php
include( "../config.inc.php" ); 
$dbo =& SIMDB::get();
$dbo->query( "UPDATE $_POST[tabla] SET  Nombre = '" . $_POST[des] . "' WHERE $_POST[id] = '" . $_POST[val] . "' " );
echo $_POST[des];
?>