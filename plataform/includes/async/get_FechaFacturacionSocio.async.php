<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$accion = $frm['accion'];

$sql_Socio = "SELECT FechaIngresoClub,FechaFacturacion FROM Socio WHERE IDClub = '" . SIMUser::get('club') . "' AND Accion = '" . $accion . "' LIMIT 1";
$q_Socio = $dbo->query($sql_Socio);
$r_Socio = $dbo->assoc($q_Socio);

$responce = array('FechaFacturacion' => $r_Socio['FechaFacturacion'], 'FechaIngresoClub' => $r_Socio['FechaIngresoClub']);

echo json_encode($responce);
