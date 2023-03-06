<?php
//include("../../procedures/general_async.php");
//include("plataform/procedures/general_async.php");
require("../../../admin/config.inc.php");
$dbo = &SIMDB::get();
$Categoria = $_POST['Categoria'];


$sql_categorias = "SELECT  count(Categoria) as totalCategorias FROM TorneoChiquitinesDeTenis WHERE CodigoRespuesta <>''  AND Categoria='" . $Categoria . "'";

$query = $dbo->query($sql_categorias);

$datosCategoria = $dbo->fetchArray($query);
$frm = $datosCategoria;


/* if ($Categoria == "1" && $frm["totalCategorias"] >= 6) {

    echo json_encode(array('resultado' => 'ok'));
}
if ($Categoria == "2" && $frm["totalCategorias"] >= 6) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "3" && $frm["totalCategorias"] >= 8) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "4" && $frm["totalCategorias"] >= 4) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "5" && $frm["totalCategorias"] >= 0) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "6" && $frm["totalCategorias"] >= 4) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "7" && $frm["totalCategorias"] >= 0) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "8" && $frm["totalCategorias"] >= 8) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "9" && $frm["totalCategorias"] >= 12) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "10" && $frm["totalCategorias"] >= 4) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "11" && $frm["totalCategorias"] >= 10) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "12" && $frm["totalCategorias"] >= 5) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "13" && $frm["totalCategorias"] >= 8) {

    echo json_encode(array('resultado' => 'ok'));
}

if ($Categoria == "14" && $frm["totalCategorias"] >= 8) {

    echo json_encode(array('resultado' => 'ok'));
} */

echo json_encode(array('resultado' => 'ok'));
