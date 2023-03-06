<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$get = SIMUtil::makeSafe($_GET);

$encuestaPara = $dbo->getFields("Encuesta", "DirigidoA", "IDEncuesta = " . $get["encuesta"]);

$sql_preguntas4 = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
							FROM Pregunta P
							WHERE 	P.IDEncuesta = " . $get["encuesta"] . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result4 = $dbo->query($sql_preguntas4);
$numPregunta = 1;
$array_preguntas4 = array();
$array_NoPregunta4 = array();

while ($rowPregunta4 = $dbo->fetchArray($result4)) {

    $array_preguntas4["_" . $rowPregunta4["IDPregunta"]] = "";
    $array_NoPregunta4[$rowPregunta4["IDPregunta"]] = $rowPregunta4["IDPregunta"];
}

$array_preguntasDefault = $array_preguntas4;
//si la encuensta es para empleados
if ($encuestaPara == "E") {
    $sql = "SELECT U.IDUsuario, U.Nombre AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Usuario U
				WHERE
                P.IDEncuesta = E.IDEncuesta
                AND ER.IDPregunta = P.IDPregunta
                AND ER.IDSocio = U.IDUsuario
                AND E.IDClub = 195
					AND E.IDEncuesta =  " . $get["encuesta"] . "
					AND ER.IDSocio = " . $get["id"] . "
					AND P.Publicar = 'S'
					ORDER BY ER.IDEncuestaRespuesta DESC";
}
//si la encuesta es para socios
elseif ($encuestaPara == "S") {

//sacamos la cantidad de pregunta de la encuesta
  $sql = "SELECT COUNT(*) as total FROM Pregunta WHERE IDEncuesta=" . $get["encuesta"] . "";
               $qry = $dbo->query($sql);
               $r = $dbo->fetchArray($qry);
               $total= $r["total"]; 

//sacamos todos los datos de las respuesta de esa encuesta
$datos_respuestas = "SELECT * FROM  EncuestaRespuesta WHERE IDEncuesta=" . $get["encuesta"] . " AND IDSocio=" . $get["id"] . "";
//$datos_respuestas = "SELECT * FROM  EncuestaRespuesta WHERE IDEncuesta=" . $get["encuesta"] . " AND  FechaTrCr LIKE '%00:00:00' ORDER by IDEncuestaRespuesta";
                        $datos1 = $dbo->query($datos_respuestas);
                        //contadores, uno para sumar el dia y otro controla cada encuesta.
                        $s=1;
                        $contador=0;
                        
                        //se recorre y se hace la operacion de actualizar las fechas
                        while ($row = $dbo->fetchArray($datos1)) {
                            $id = $row["IDEncuestaRespuesta"];
                            $fecha_actual = $row["FechaTrCr"];
 
if($contador==$total){
$s++;  
//sumo 1 día
$NuevaFecha= date("Y-m-d",strtotime($fecha_actual."+ $s  days")); 
$contador=0;
}
if(!isset($NuevaFecha)){
$NuevaFecha=$row["FechaTrCr"];
}

$actualizar = "UPDATE EncuestaRespuesta SET  FechaTrCr='$NuevaFecha' WHERE IDEncuestaRespuesta='$id'";
                        $actualizar_respuestas = $dbo->query($actualizar);
 

  $contador=$contador+1;

                          
                        }
                        //fin actualizacion
    $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,S.Predio,S.NumeroDeCasa
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Socio S
                WHERE P.IDEncuesta = E.IDEncuesta 
                AND ER.IDPregunta = P.IDPregunta 
                AND ER.IDSocio = S.IDSocio 
                AND E.IDClub = 195
				AND E.IDEncuesta =  " . $get["encuesta"] . "
				AND ER.IDSocio = " . $get["id"] . "
				AND P.Publicar = 'S'
				ORDER BY ER.IDEncuestaRespuesta DESC";

    // $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
    // 			P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,S.Predio,S.NumeroDeCasa
    // 			FROM Encuesta E
    // 			JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
    // 			JOIN EncuestaRespuesta ER ON ER.IDPregunta = P.IDPregunta
    // 			JOIN Socio S ON ER.IDSocio = S.IDSocio
    // 			WHERE E.IDClub = " . SIMUser::get("club") . "
    // 				AND E.IDEncuesta = " . $get["id"] . "
    // 				AND P.Publicar = 'S'
    // 				ORDER BY ER.IDEncuestaRespuesta DESC";
}
//si la encuesta es para los dos
else {
    $sql = "SELECT S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS NombreSocio,U.Nombre AS NombreFuncionario,
					P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
					FROM Encuesta E, Pregunta P, EncuestaRespuesta ER LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio LEFT JOIN Usuario U ON  ER.IDSocio = U.IDUsuario
					WHERE  P.IDEncuesta = E.IDEncuesta 
                    AND ER.IDPregunta = P.IDPregunta 
                    AND E.IDClub = 195
						AND E.IDEncuesta =  " . $get["encuesta"] . "
						 AND ER.IDSocio = " . $get["id"] . "
						 AND P.Publicar = 'S'
						ORDER BY ER.IDEncuestaRespuesta DESC";
    // $sql = "SELECT S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS NombreSocio,U.Nombre AS NombreFuncionario,
    // 				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
    // 				FROM Encuesta E
    // 				JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
    // 				JOIN EncuestaRespuesta ER ON ER.IDPregunta = P.IDPregunta
    // 				JOIN Socio S ON ER.IDSocio = S.IDSocio
    // 				JOIN Usuario U ON ER.IDSocio = U.IDUsuario
    // 				WHERE E.IDClub = " . SIMUser::get("club") . "
    // 					AND E.IDEncuesta = " . $get["id"] . "
    // 					AND P.Publicar = 'S'
    // 					ORDER BY ER.IDEncuestaRespuesta DESC";
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

    $eliminar = '<a class="red" href="encuestas.php?action=EliminarRespuesta&IDSocio=' . $ID . '&IDEncuesta=' . $get['encuesta'] . '&cantidad=' . $cantidad . '"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

    //caso para cuando la escuesta es para empleados
    if ($encuestaPara == "E") {
        $keyRow = $rowItem["IDUsuario"] . substr($rowItem["FechaTrCr"], 0, 13);

        if (!array_key_exists($keyRow, $arrayItems)) {

            $arrayUsuario = array(
                'Eliminar' => $eliminar,
                'IDUsuario' => $rowItem["IDUsuario"],
                'Nombre' => $rowItem['Nombre'],
                'Fecha' => $rowItem['Fecha'],
                'Predio' => $rowItem['Predio'],
                'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
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
                'Predio' => $rowItem['Predio'],
                'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
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
                    'Nombre' => $rowItem['NombreSocio'],
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
                    'Nombre' => $rowItem['NombreFuncionario'],
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

    $arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
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
