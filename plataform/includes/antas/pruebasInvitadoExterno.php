<?php
require( "config.inc.php" );
require( "lib/SIMServicioReserva.inc.php" );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$respuesta = SIMServicioReserva::CrearInvitacionExterno(8, 374302, "", "Maria Leonilde Salgado", "malesamo@gmail.com", "2021-06-15 7:00", "2021-06-15 16:00");

echo "<pre>";
var_dump($respuesta);

