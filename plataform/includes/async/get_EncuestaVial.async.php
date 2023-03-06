<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$get = SIMUtil::makeSafe($_GET);

$encuestaPara = $dbo->getFields("EncuestaVial", "DirigidoA", "IDEncuestaVial = " . $get["id"]);

$sql_preguntas = " SELECT P.IDPreguntaVial,P.EtiquetaCampo,P.Orden
							FROM PreguntaVial P
							WHERE 	P.IDEncuestaVial = " . SIMNet::reqInt("id") . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_NoPregunta = array();


while ($rowPregunta = $dbo->fetchArray($result)) {

    $array_preguntas["_" . $rowPregunta["IDPreguntaVial"]] = "";
    $array_NoPregunta[$rowPregunta["IDPreguntaVial"]] = $rowPregunta["IDPreguntaVial"];
}

$array_preguntasDefault = $array_preguntas;

//si la encuensta es para empleados
if ($encuestaPara == "E") {
    $sql = "SELECT U.IDUsuario, U.Nombre AS Nombre,
				P.IDPreguntaVial,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM EncuestaVial E
				JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
				JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
				JOIN Usuario U ON ER.IDSocio = U.IDUsuario
				WHERE E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuestaVial = " . $get["id"] . "
					AND P.Publicar = 'S'
					ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
}
//si la encuesta es para socios
elseif ($encuestaPara == "S") {
    $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
				P.IDPreguntaVial,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM EncuestaVial E
				JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
				JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
				JOIN Socio S ON ER.IDSocio = S.IDSocio
				WHERE E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuestaVial = " . $get["id"] . "
					AND P.Publicar = 'S'
					ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
}
//si la encuesta es para los dos
else {
    $sql = "SELECT S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS NombreSocio, CONCAT( U.Nombre, ' ', COALESCE(U.Apellido, '') ) AS NombreFuncionario
					P.IDPreguntaVial,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
					FROM EncuestaVial E
					JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
					JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
					JOIN Socio S ON ER.IDSocio = S.IDSocio
					JOIN Usuario U ON ER.IDSocio = U.IDUsuario
					WHERE E.IDClub = " . SIMUser::get("club") . "
						AND E.IDEncuestaVial = " . $get["id"] . "
						AND P.Publicar = 'S'
						ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
}
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

    if (empty($rowItem["IDUsuario"]))
        $ID = $rowItem["IDSocio"];
    else
        $ID = $rowItem["IDUsuario"];

    $eliminar = '<a class="red" href="encuestavial.php?action=EliminarEncuestaRespuestaVial&IDSocio=' . $ID . '&IDEncuestaVial=' . $get['id'] . '&cantidad=' . $cantidad . '"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

    //caso para cuando la escuesta es para empleados
    if ($encuestaPara == "E") {
        $keyRow = $rowItem["IDUsuario"] . substr($rowItem["FechaTrCr"], 0, 13);

        if (!array_key_exists($keyRow, $arrayItems)) {

            $arrayUsuario = array(
                'Eliminar' => $eliminar,
                'IDUsuario' => $rowItem["IDUsuario"],
                'Nombre' => $rowItem['Nombre'],
                'Fecha' => $rowItem['Fecha'],
            );

            $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
        }
    }
    //caso para cuando la escuesta es para socios
    elseif ($encuestaPara == 'S') {
        $keyRow = $rowItem["IDSocio"] . substr($rowItem["FechaTrCr"], 0, 13);

        if (!array_key_exists($keyRow, $arrayItems)) {

            $arrayUsuario = array(
                'Eliminar' => $eliminar,
                'IDSocio' => $rowItem["IDSocio"],
                'Nombre' => $rowItem['Nombre'],
                'Fecha' => $rowItem['Fecha'],
            );

            $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
        }
    }
    //caso para cuando la escuesta es para empleados y socios
    else {
        //verifico si saco la info del socio
        if (!empty($rowItem["NombreSocio"])) {
            $keyRow = $rowItem["IDSocio"] . substr($rowItem["FechaTrCr"], 0, 13);

            if (!array_key_exists($keyRow, $arrayItems)) {

                $arrayUsuario = array(
                    'Eliminar' => $eliminar,
                    'IDSocio' => $rowItem["IDSocio"],
                    'Nombre' => $rowItem['Nombre'],
                    'Fecha' => $rowItem['Fecha'],
                );

                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
        //verifico si saco la info del empleado
        else {
            $keyRow = $rowItem["IDUsuario"] . substr($rowItem["FechaTrCr"], 0, 13);

            if (!array_key_exists($keyRow, $arrayItems)) {

                $arrayUsuario = array(
                    'Eliminar' => $eliminar,
                    'IDUsuario' => $rowItem["IDUsuario"],
                    'Nombre' => $rowItem['Nombre'],
                    'Fecha' => $rowItem['Fecha'],
                );

                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
    }

    if ($rowItem["TipoCampo"] == "imagen" && !empty($rowItem["Valor"])) {
        $ruta_imagen = PQR_ROOT . $rowItem["Valor"];
        $contenido_resp = "<img src= '" . $ruta_imagen . "' width=200 height=200 ";
    } else {
        $contenido_resp = $rowItem["Valor"];
    }

    $arrayItems[$keyRow]["_" . $rowItem["IDPreguntaVial"]] = $contenido_resp;
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

    if ($encuestaPara == 'E') {
        $responce->rows[$i]['id'] = $row["IDUsuario"];
    } elseif ($encuestaPara == 'S') {
        $responce->rows[$i]['id'] = $row["IDSocio"];
    } else {
        if (!empty($rowItem["NombreSocio"])) {
            $responce->rows[$i]['id'] = $row["IDSocio"];
        } else {
            $responce->rows[$i]['id'] = $row["IDUsuario"];
        }
    }
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
