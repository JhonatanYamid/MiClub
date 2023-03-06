<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();


$frm = SIMUtil::makeSafe($_POST);

if ($frm["extrasdespuesdelturno"] == "extrasdespuesdelturno") {
    $sql = "UPDATE CheckinLaboralHorasExtras SET Estado = 2 WHERE  CheckinLaboralHorasExtras.IDClub = " . $frm['club'] . " AND CheckinLaboralHorasExtras.IDSocio=" . $frm['id'] . " AND CheckinLaboralHorasExtras.Estado = 1 ";

    echo ($query = $dbo->query($sql)) ? '1' : '0';
} else {
    $sql = "UPDATE CheckinLaboral SET Estado = 2 WHERE  CheckinLaboral.IDClub = " . $frm['club'] . " AND IDSocio=" . $frm['id'] . " AND CheckinLaboral.Estado = 1 ";

    echo ($query = $dbo->query($sql)) ? '1' : '0';
}
