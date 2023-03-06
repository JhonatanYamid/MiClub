<?php
require("../admin/lib/SIMDB.inc.php");
require("../admin/lib/SIMServicioReserva.inc.php");

$validacion = SIMServicioReserva::validarReservaAreaDeportiva($IDServicio, $NombreInvitado, 1060652556);

var_dump($validacion);