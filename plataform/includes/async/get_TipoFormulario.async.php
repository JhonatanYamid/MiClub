<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$get = SIMUtil::makeSafe($_GET);



$sql_preguntas = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
							FROM PreguntaRegistrosDinamicos P
							WHERE 	P.IDTipoFormulario = " . SIMNet::reqInt("id") . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_NoPregunta = array();

while ($rowPregunta = $dbo->fetchArray($result)) {

    $array_preguntas["_" . $rowPregunta["IDPreguntaRegistrosDinamicos"]] = "";
    $array_NoPregunta[$rowPregunta["IDPreguntaRegistrosDinamicos"]] = $rowPregunta["IDPreguntaRegistrosDinamicos"];
}

$array_preguntasDefault = $array_preguntas;



$sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
				P.IDPreguntaRegistrosDinamicos,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM TipoFormulario E, PreguntaRegistrosDinamicos P, RegistrosDinamicos ER, Socio S
                WHERE P.IDTipoFormulario = E.IDTipoFormulario 
                AND ER.IDPreguntaRegistrosDinamicos = P.IDPreguntaRegistrosDinamicos 
                AND ER.IDSocio = S.IDSocio 
                AND E.IDClub = " . SIMUser::get("club") . "
				AND E.IDTipoFormulario = " . $get["id"] . "
				AND P.Publicar = 'S'
				ORDER BY ER.IDRegistrosDinamicos DESC";

/* echo $sql;
exit; */




// ORDER BY Fecha,S.IDSocio,P.Orden

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid

/*$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";

$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");
$row = $dbo->fetchArray($result);
$count = $row['count'];*/
//$limit = 58;
/* $sql .= " LIMIT ".($limit * count($array_preguntas)); */

$result = $dbo->query($sql);

//    echo $responce->records;
//    $numPregunta = 1;
$cantidad = count($array_preguntas);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {

    $FechaCreacion = explode(" ", $rowItem["FechaTrCr"]);


    $eliminar = '<a class="red" href="encuestas.php?action=EliminarRespuesta&IDSocio=' . $ID . '&IDEncuesta=' . $get['id'] . '&cantidad=' . $cantidad . '"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';



    //mostrar datos

    $keyRow = $rowItem["IDSocio"] . substr($rowItem["FechaTrCr"], 0, 13);

    if (!array_key_exists($keyRow, $arrayItems)) {

        $arrayUsuario = array(

            'IDSocio' => $rowItem["IDSocio"],
            'Nombre' => $rowItem['Nombre'],
            'Fecha' => $rowItem['Fecha'],
            'Hora' => $FechaCreacion[1],


        );

        $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
    }
    //fin datos

    if (($rowItem["TipoCampo"] == "imagen" || $rowItem["TipoCampo"] == "firmadigital") && !empty($rowItem["Valor"])) {
        $ruta_imagen = REGISTROSINFINITO_ROOT . $rowItem["Valor"];
        $contenido_resp = "<img src= '" . $ruta_imagen . "' width=200 height=200 ";
    } else {
        $contenido_resp = $rowItem["Valor"];
    }

    $arrayItems[$keyRow]["_" . $rowItem["IDPreguntaRegistrosDinamicos"]] = $contenido_resp;
}

$count = count($arrayItems);
//exit;

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$responce->page = (int) $page;
$responce->total = (int) $total_pages;
$responce->records = (int) $count;

//while($row = $dbo->fetchArray($result)) {
$i = 0;
foreach ($arrayItems as $row) {

    //inicio response
    $responce->rows[$i]['id'] = $row["IDSocio"];


    //  $class = "a-edit-modal btnAddReg";
    //$attr = "rev=\"reload_grid\"";
    //if( $origen <> "mobile" )
    $responce->rows[$i]['cell'] = $row;
    /*array(
    //"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
    "IDSocio" => $row["IDSocio"],
    "Nombre" => $row["Nombre"],
    "Valor" => $row["Valor"],
    "Nombre" =>  $row["Nombre"] ,
    "Fecha" => $row["Fecha"],
    );*/

    $i++;
}
echo json_encode($responce);
