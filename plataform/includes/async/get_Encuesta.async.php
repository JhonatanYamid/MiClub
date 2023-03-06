<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$get = SIMUtil::makeSafe($_GET);

$columns = array();
$origen = SIMNet::req("origen");

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Nombre ASC";
        $_GET['sord'] = "ASC";


        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'Cedula':
                    $encuestaPara = $dbo->getFields("Encuesta", "DirigidoA", "IDEncuesta = " . $get["id"]);
                    $cedula = $search_object->data;
                    //si la encuesta es para empleados
                    if ($encuestaPara == "E") {
                        $sql_usuario = "SELECT IDUsuario FROM Usuario WHERE NumeroDocumento = '" . $cedula . "' AND IDClub = " . SIMUser::get("club");
                        $result_usuario = $dbo->query($sql_usuario);
                        $row_Usuario = $dbo->fetchArray($result_usuario);
                    }
                    //si la encuesta es para socios
                    elseif ($encuestaPara == "S") {
                        $sql_socio = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $cedula . "' AND IDClub = " . SIMUser::get("club");
                        $result_socio = $dbo->query($sql_socio);
                        $row_Socio = $dbo->fetchArray($result_socio);
                    }
                    //si la encuesta es para los dos
                    else {
                        $sql_usuario = "SELECT IDUsuario FROM Usuario WHERE NumeroDocumento = '" . $cedula . "' AND IDClub = " . SIMUser::get("club");
                        $result_usuario = $dbo->query($sql_usuario);
                        $row_Usuario = $dbo->fetchArray($result_usuario);

                        $sql_socio = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $cedula . "' AND IDClub = " . SIMUser::get("club");
                        $result_socio = $dbo->query($sql_socio);
                        $row_Socio = $dbo->fetchArray($result_socio);
                    }



                    if (empty($cedula)) {
                        $where .= "";
                    } elseif ($row_Socio["IDSocio"] != "") {
                        $where .= " AND S.IDSocio = '$row_Socio[IDSocio]" . "'";
                    } elseif ($row_Usuario["IDUsuario"] != "") {
                        $where .= " AND U.IDUsuario = '$row_Usuario[IDUsuario]" . "'";
                    } elseif ($encuestaPara == "T") {
                        $where .= " AND (S.IDSocio = '$row_Socio[IDSocio]" . "' OR U.IDUsuario = '$row_Usuario[IDUsuario]" . "')";
                    }



                    break;
                case 'qryString':

                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
                    break;

                default:
                    $where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}


$encuestaPara = $dbo->getFields("Encuesta", "DirigidoA", "IDEncuesta = " . $get["id"]);

$sql_preguntas = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
							FROM Pregunta P
							WHERE 	P.IDEncuesta = " . SIMNet::reqInt("id") . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_NoPregunta = array();

while ($rowPregunta = $dbo->fetchArray($result)) {

    $array_preguntas["_" . $rowPregunta["IDPregunta"]] = "";
    $array_NoPregunta[$rowPregunta["IDPregunta"]] = $rowPregunta["IDPregunta"];
}

$array_preguntasDefault = $array_preguntas;
//si la encuensta es para empleados
if ($encuestaPara == "E") {
    $sql = "SELECT U.IDUsuario, U.Nombre AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Usuario U
				WHERE
                P.IDEncuesta = E.IDEncuesta
                AND ER.IDPregunta = P.IDPregunta
                AND ER.IDSocio = U.IDUsuario
                AND E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuesta = " . $get["id"] . "
					AND P.Publicar = 'S'
					ORDER BY ER.IDEncuestaRespuesta DESC";
}
//si la encuesta es para socios
elseif ($encuestaPara == "S") {
    $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,S.Predio,S.NumeroDeCasa
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Socio S
                WHERE P.IDEncuesta = E.IDEncuesta 
                AND ER.IDPregunta = P.IDPregunta 
                AND ER.IDSocio = S.IDSocio 
                AND E.IDClub = " . SIMUser::get("club") . "
				AND E.IDEncuesta = " . $get["id"] . "
				AND P.Publicar = 'S' $where
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
                    AND E.IDClub = " . SIMUser::get("club") . "
						AND E.IDEncuesta = " . $get["id"] . "
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
/* este es lo del row


$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre ASC";
		$_GET['sord'] = "ASC";


	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					
					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  Usuario." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}
			
		}//end for

		

			
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{
			
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
 
$count =10;

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


ORDER BY ER.IDEncuestaRespuesta LIMIT " . $start . "," . $limit;



*/
// ORDER BY Fecha,S.IDSocio,P.Orden



/*$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";

$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");
$row = $dbo->fetchArray($result);
$count = $row['count'];*/
//$limit = 58;
/* $sql .= " LIMIT ".($limit * count($array_preguntas)); */

/* echo  $sql;
exit; */
$result = $dbo->query($sql);

//    echo $responce->records;
//    $numPregunta = 1;
$cantidad = count($array_preguntas);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {

    $FechaCreacion = explode(" ", $rowItem["FechaTrCr"]);
    if (empty($rowItem["IDUsuario"]))
        $ID = $rowItem["IDSocio"];
    else
        $ID = $rowItem["IDUsuario"];

    $eliminar = '<a class="red" href="encuestas.php?action=EliminarRespuesta&IDSocio=' . $ID . '&IDEncuesta=' . $get['id'] . '&cantidad=' . $cantidad . '"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

    //caso para cuando la escuesta es para empleados
    if ($encuestaPara == "E") {
        $keyRow = $rowItem["IDUsuario"] . substr($rowItem["FechaTrCr"], 0, 13);

        if (!array_key_exists($keyRow, $arrayItems)) {

            $arrayUsuario = array(
                'Eliminar' => $eliminar,
                'IDUsuario' => $rowItem["IDUsuario"],
                'Nombre' => $rowItem['Nombre'],
                'Fecha' => $rowItem['Fecha'],
                'Hora' => $FechaCreacion[1],
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
                'Hora' => $FechaCreacion[1],
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
                    'Hora' => $FechaCreacion[1],
                    'Predio' => $rowItem['Predio'],
                    'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
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
                    'Hora' => $FechaCreacion[1],
                    'Predio' => $rowItem['Predio'],
                    'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
                );

                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
    }

    if (($rowItem["TipoCampo"] == "imagen" || $rowItem["TipoCampo"] == "firmadigital") && !empty($rowItem["Valor"])) {
        $ruta_imagen = PQR_ROOT . $rowItem["Valor"];
        $contenido_resp = "<img src= '" . $ruta_imagen . "' width=200 height=200 ";
    } else {
        $contenido_resp = $rowItem["Valor"];
    }

    $arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";

$count = count($arrayItems);
//exit;

if ($count > 0) {
    $total_pages = ceil($count / 10);
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

unset($arrayItems);
unset($array_preguntas);
unset($array_NoPregunta);

$encuestaPara = $dbo->getFields("Encuesta", "DirigidoA", "IDEncuesta = " . $get["id"]);

$sql_preguntas = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
							FROM Pregunta P
							WHERE 	P.IDEncuesta = " . SIMNet::reqInt("id") . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_NoPregunta = array();

while ($rowPregunta = $dbo->fetchArray($result)) {

    $array_preguntas["_" . $rowPregunta["IDPregunta"]] = "";
    $array_NoPregunta[$rowPregunta["IDPregunta"]] = $rowPregunta["IDPregunta"];
}

$array_preguntasDefault = $array_preguntas;
//si la encuensta es para empleados
if ($encuestaPara == "E") {
    $sql = "SELECT U.IDUsuario, U.Nombre AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Usuario U
				WHERE
                P.IDEncuesta = E.IDEncuesta
                AND ER.IDPregunta = P.IDPregunta
                AND ER.IDSocio = U.IDUsuario
                AND E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuesta = " . $get["id"] . "
					AND P.Publicar = 'S' $where
					ORDER BY ER.IDEncuestaRespuesta DESC";
}
//si la encuesta es para socios
elseif ($encuestaPara == "S") {
    $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', COALESCE(S.Apellido, '') ) AS Nombre,
				P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,S.Predio,S.NumeroDeCasa
				FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Socio S
                WHERE P.IDEncuesta = E.IDEncuesta 
                AND ER.IDPregunta = P.IDPregunta 
                AND ER.IDSocio = S.IDSocio 
                AND E.IDClub = " . SIMUser::get("club") . "
				AND E.IDEncuesta = " . $get["id"] . "
				AND P.Publicar = 'S' $where
                       ORDER BY ER.IDEncuestaRespuesta DESC LIMIT " . $start . "," . $limit;
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
                    AND E.IDClub = " . SIMUser::get("club") . "
						AND E.IDEncuesta = " . $get["id"] . "
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

/* echo $sql;
exit; */

$result = $dbo->query($sql);

//    echo $responce->records;
//    $numPregunta = 1;
$cantidad = count($array_preguntas);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {

    $FechaCreacion = explode(" ", $rowItem["FechaTrCr"]);
    if (empty($rowItem["IDUsuario"]))
        $ID = $rowItem["IDSocio"];
    else
        $ID = $rowItem["IDUsuario"];

    $eliminar = '<a class="red" href="encuestas.php?action=EliminarRespuesta&IDSocio=' . $ID . '&IDEncuesta=' . $get['id'] . '&cantidad=' . $cantidad . '"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

    //caso para cuando la escuesta es para empleados
    if ($encuestaPara == "E") {
        $keyRow = $rowItem["IDUsuario"] . substr($rowItem["FechaTrCr"], 0, 13);

        if (!array_key_exists($keyRow, $arrayItems)) {

            $arrayUsuario = array(
                'Eliminar' => $eliminar,
                'IDUsuario' => $rowItem["IDUsuario"],
                'Nombre' => $rowItem['Nombre'],
                'Fecha' => $rowItem['Fecha'],
                'Hora' => $FechaCreacion[1],
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
                'Hora' => $FechaCreacion[1],
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
                    'Hora' => $FechaCreacion[1],
                    'Predio' => $rowItem['Predio'],
                    'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
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
                    'Hora' => $FechaCreacion[1],
                    'Predio' => $rowItem['Predio'],
                    'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
                );

                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
    }

    if (($rowItem["TipoCampo"] == "imagen" || $rowItem["TipoCampo"] == "firmadigital") && !empty($rowItem["Valor"])) {
        $ruta_imagen = PQR_ROOT . $rowItem["Valor"];
        $contenido_resp = "<img src= '" . $ruta_imagen . "' width=200 height=200 ";
    } else {
        $contenido_resp = $rowItem["Valor"];
    }

    $arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
}


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
