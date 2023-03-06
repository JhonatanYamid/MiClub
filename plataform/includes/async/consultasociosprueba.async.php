<?php
include("../../procedures/general_async.php");
//SIMUtil::cache("text/json");
// $dbo = &SIMDB::get();

// $frm = SIMUtil::makeSafe($_POST);
// $frm_get =  SIMUtil::makeSafe($_GET);
// $oper = SIMNet::req("oper");

// $IDSocio = SIMNet::req("idSocio");
// $IDEncuesta = SIMNet::req("IDEncuesta");
$oper = $_POST['oper'];
$IDSocio = $_POST['idSocio'];
$IDEncuesta = $_POST['IDEncuesta'];

// echo 'id socio: ' . $IDSocio . ', id encuesta: ' . $IDEncuesta;

$sql = "SELECT UnaporSocio FROM Encuesta WHERE IDEncuesta= '$IDEncuesta' ";
// echo $sql;

$query = $dbo->query($sql);
$encuesta = $dbo->fetchArray($query);
$frm1 = $encuesta;


if ($frm1["UnaporSocio"] == 'S') {

    $sql1 = "SELECT IDSocio FROM EncuestaRespuesta WHERE IDEncuesta= '$IDEncuesta' AND IDSocio ='$IDSocio' ";
    // echo $sql1;
    $query1 = $dbo->query($sql1);
    $encuestarespuesta = $dbo->fetchArray($query1);
    $frm2 = $encuestarespuesta;
    if ($frm2["IDSocio"] <> "") {
        // echo "<script>alert('Solo puede realizar la encuesta 1 vez');'</script>";
        echo json_encode(array('resultado' => 'ok'));
    } else {
        echo json_encode(array('resultado' => 'error'));
    }
}
