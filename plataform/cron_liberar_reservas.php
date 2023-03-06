#!/usr/bin/php -q
<?php

//require( "../admin/config.inc.php" );
//include("/var/www/vhosts/miclubapp.com/httpdocs/admin/config.inc.php");
include("/home/http/miclubapp/httpdocs/admin/config.inc.php");


// realizo copia de las reservas que se van a borrar
//$sql_copia_reserva = "Insert Ignore Into ReservaGeneralBck Select * From  ReservaGeneral Where IDEstadoReserva = 3  AND FechaTrCr <= DATE_SUB(now(), interval 5 minute) order by IDReservaGeneral DESC";
//$dbo->query($sql_copia_reserva);

$sql_liberar_reserva = "DELETE FROM ReservaGeneral Where IDEstadoReserva = 3  AND FechaTrCr <= DATE_SUB(now(), interval 5 minute) order by IDReservaGeneral DESC";
$dbo->query($sql_liberar_reserva);

echo "Terminado";
?>
