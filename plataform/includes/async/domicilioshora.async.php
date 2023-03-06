<?php
require("../../../admin/config.inc.php");
require_once LIBDIR . "SIMWebServiceDomicilios.inc.php";


//$dbo = &SIMDB::get();
if ($_GET['fecha'] != '') {

    $Fecha = $_GET['fecha'];
    $IDRestaurante = $_GET['idRestaurante'];
    $IDClub = $_GET['idClub'];
    $Version = $_GET['version'];

    if ($Version == "1") {
        $Version = "";
    }



    $respuesta = SIMWebServiceDomicilios::get_horas_entrega($IDClub, $Fecha, $Version, $IDRestaurante);
    $respuestaDatos = $respuesta["response"];
    //  print_r($respuestaDatos);
    foreach ($respuestaDatos as $key => $value) {
        $Hora = $value["Hora"];
        // echo $Hora . "<br>";
        echo '<option value="' . $Hora . '">' . $Hora . '</option>';
    }
}
