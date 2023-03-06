<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);

$sql = "SELECT * FROM Socio WHERE AccionPadre = '' and IDClub = 8";
$q_Socio = $dbo->query($sql);
while ($r = $dbo->assoc($q_Socio)) {
    echo '<pre>';
    print_r($r);
    die();
}


$result['mensaje'] =  '¡Facturas enviadas con exito!';

echo $response = json_encode($result);
