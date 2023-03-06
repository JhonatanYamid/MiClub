#!/usr/bin/php -q
<?php
//include('../../admin/lib/dompdf_v2/autoload.inc.php');
include("/home/http/miclubapp/httpdocs/admin/lib/dompdf_v2/autoload.inc.php");

use Dompdf\Dompdf;
$dompdf = new Dompdf();

ob_start(); //iniciamos un output buffer

?>
    <style> *{ font-weight: bold; } </style>
<?php

require_once('pantallaparaiso.php'); // llamamos el archivo que se supone contiene el html y dejamoso que se renderize
$dompdf->load_html(ob_get_clean());//y ponemos todo lo que se capturo con ob_start() para que sea capturado por DOMPDF

$dompdf->set_paper('a3', 'landscape');
// , 'landscape'
$dompdf->render();

//Guardalo en una variable
$output = $dompdf->output();

// $dompdf->stream('Reporte.pdf');

$pdf_name="Reporte";

$file_location = "/home/http/miclubapp/httpdocs/plataform/screen/".$pdf_name.".pdf";
//$file_location = $_SERVER['DOCUMENT_ROOT']."/plataform/screen/".$pdf_name.".pdf";
file_put_contents($file_location,$output);

include_once('email.php');

?>
