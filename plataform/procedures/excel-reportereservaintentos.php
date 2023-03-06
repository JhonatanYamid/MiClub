<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");


function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}
/* print_r($_POST);
exit; */
$IDClub = $_POST["IDClub"];
$Fecha = $_POST["FechaInicio"];
$IDServicio = (int)$_POST["IDServicioMaestro"];


$dt1 = date($Fecha . "00:00:00");
$dt2 = date($Fecha . "23:59:59");


$sql_socios = "SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '" . $IDClub . "'";
$r_socios = $dbo->query($sql_socios);
while ($row_socios = $dbo->fetchArray($r_socios)) {
    $array_socios[$row_socios["IDSocio"]] = $row_socios;
}

$sql_elemento = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
$r_elemento = $dbo->query($sql_elemento);
while ($row_elemento = $dbo->fetchArray($r_elemento)) {
    $array_elemento[$row_elemento["IDServicioElemento"]] = $row_elemento;
}


//$busqueda="/.*2021-05-27.*/";
//$filter = ['IDServicio' => 21696, 'FechaPeticionTexto'=>'2021-05-27 18:39:04.933100'];
//$filter = ['IDServicio' => $IDServicio,'FechaPeticionTexto'=>new MongoDB\BSON\Regex('^'.$Fecha, 'i')];


$filter = ['IDServicio' => $IDServicio, 'FechaPeticion' => array('$gte' => new MongoDB\BSON\UTCDateTime(strtotime("$dt1") * 1000), '$lte' => new MongoDB\BSON\UTCDateTime(strtotime("$dt2") * 1000))];

//$filter = ['IDServicio' => 21696];
$Coleccion = "Operacion";
$options = [
    'projection' => ['_id' => 0],
    'sort' => ['IDServicio' => -1],
];


$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $dblinkMongo->executeQuery(DBNAMEMongo . '.Operacion', $query);







//$result_reporte = $dbo->query($sql_reporte);

$nombre = "Reserva_Intentos:" . date("Y_m_d");

//$NumSocios = $dbo->rows($result_reporte);

if ($cursor > 0) {

    $html  = "";
    $html .= "<table width='100%' border='1'>";

    $html .= "<tr>";
    $html .= "<th colspan='5'>RESUMEN </th>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<th>Movimiento</th>";
    $html .= "<th>Respuesta APP</th>";
    $html .= "<th>Servicio</th>";
    $html .= "<th>Elemento</th>";
    $html .= "<th>Tee</th>";
    $html .= "<th>Socio</th>";
    $html .= "<th>Fecha del turno</th>";
    $html .= "<th>Hora del turno</th>";
    $html .= "<th>Dispositivo</th>";
    $html .= "<th>Fecha Peticion</th>";

    $html .= "</tr>";

    $contador_intento = 0;
    $contador_exitosa = 0;
    foreach ($cursor as $row_separa_servicio) {


        $mostrar = "N";
        if ($row_separa_servicio->Servicio == "setreservageneral") {
            $mostrar = "S";
        } elseif ($row_separa_servicio->Servicio == "setseparareserva") {

            $mostrar = "S";
        } elseif ($row_separa_servicio->Servicio == "getfechasdisponiblesservicio") {

            $mostrar = "S";
        } else {

            $mostrar = "N";
        }


        if ($mostrar == "S") {


            $html .= "<tr>";


            $mostrar = "N";

            if ($row_separa_servicio->Servicio == "setreservageneral") {
                $servicio = "Pulsar en confirmar reserva";
                $mostrar = "S";
                if ($row_separa_servicio->RespuestaServicio == "exitoso")
                    $contador_exitosa++;
            } elseif ($row_separa_servicio->Servicio == "setseparareserva") {
                $servicio = "Intento reserva";
                $mostrar = "S";
                $contador_intento++;
            } elseif ($row_separa_servicio->Servicio == "getfechasdisponiblesservicio") {
                $servicio = "Ver fechas disponibles";
                $mostrar = "S";
                $contador_intento++;
            } else {
                $servicio = $row_separa_servicio->Servicio;
                $mostrar = "N";
            }

            $html .= "<td>" . $servicio . "</td>";


            //if($array_respuesta["message"]=="Esta fecha au00fan no estu00e1 disponible")
            $hora_pet = $row_separa_servicio->FechaPeticion;

            if ($row_separa_servicio->RespuestaServicio == "Guardado") {
                $respuestaapp = "exitoso";
            } elseif ($row_separa_servicio->RespuestaServicio == "") {
                $respuestaapp = "exitoso.";
            } else {
                $respuestaapp = $row_separa_servicio->RespuestaServicio;
            }

            if ($hora_pet >= "10:00:00" and $IDClub == "7" && $array_respuesta["message"] == "Esta fecha au00fan no estu00e1 disponible") {
                $respuestaapp = "Lo sentimos la reserva ya fue o esta siendo tomada";
            }
            $html .= "<td>" . $respuestaapp . "</td>";


            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio->IDServicio . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio->IDServicio . "'");
                $NombreSer = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'");
            }

            $html .= "<td>" . $NombreSer . "</td>";


            if ((int)$row_separa_servicio->IDElemento > 0) {

                $html .= "<td>" . $array_elemento[$row_separa_servicio->IDElemento]["Nombre"] . "</td>";
            } else {
                $html .= "<td>" . " " . "</td>";
            }

            $html .= "<td>" .  $array_parametros["Tee"] . "</td>";


            if (!empty($row_separa_servicio->IDSocio)) {
                $IDSoc = $row_separa_servicio->IDSocio;
                $NombreSocio = $array_socios[$IDSoc]["Nombre"] . " " . $array_socios[$IDSoc]["Apellido"];
            } else {
                if (!empty($array_usuario[$array_parametros["IDUsuario"]]["Nombre"])) {
                    $NombreSocio = "Empleado: " . $row_separa_servicio->LogServicioDiario . " : " . $array_usuario[$array_parametros["IDUsuario"]]["Nombre"];
                } else {
                    $NombreSocio = "starter";
                }
            }

            $html .= "<td>" .  $NombreSocio . "</td>";

            if (!empty($row_separa_servicio->Fecha)) {
                $utcdatetime = new MongoDB\BSON\UTCDateTime((string)$row_separa_servicio->Fecha);
                $datetime = $utcdatetime->toDateTime();
                //var_dump($datetime->format('r'));
                //var_dump($datetime->format('U.u'));
                //var_dump($datetime->getTimezone());
                $html .= "<td>" . $datetime->format('Y-m-d') . "</td>";
            } else {
                $html .= "<td>" . " " . "</td>";
            }

            $html .= "<td>" . $row_separa_servicio->DatosApp->Hora . "</td>";
            $html .= "<td>" . $row_separa_servicio->DatosApp->Dispositivo . "</td>";
            $html .= "<td>" . $row_separa_servicio->FechaPeticionTexto . "</td>";
            $html .= "</tr>";
        }
    }



    $html .= "</table>";


    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th>Resumen</th>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<th>Intentos</th>";
    $html .= "<td>" . $contador_intento . "</td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<th>Reservas Exitosas</th>";
    $html .= "<td>" . $contador_exitosa . "</td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<th>Reservas Exitosas Semana</th>";
    $html .= "<td>" . $TotalReservasSemana . "</td>";
    $html .= "</tr>";
    $html .= "</table>";



    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <?php
        echo $html;
        ?>
    </body>

    </html>
<?php
    exit();
}

?>