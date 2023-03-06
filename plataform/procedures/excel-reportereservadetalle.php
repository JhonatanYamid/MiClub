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
$FechaInicio = $_POST["FechaInicial"];
$FechaFin = $_POST["FechaFinal"];
$IDServicio = (int)$_POST["Servicio"];


if (!empty($IDClub) && !empty($FechaInicio) && !empty($FechaFin)) {

    $sql_elem = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
    $r_elem = $dbo->query($sql_elem);
    while ($row_elem = $dbo->fetchArray($r_elem)) {
        $array_elem[$row_elem["IDServicioElemento"]] = $row_elem;
    }

    $sql_aux = "SELECT IDAuxiliar,Nombre FROM Auxiliar WHERE IDServicio = '" . $IDServicio . "'";
    $r_aux = $dbo->query($sql_aux);
    while ($row_aux = $dbo->fetchArray($r_aux)) {
        $array_aux[$row_aux["IDAuxiliar"]] = $row_aux;
    }

    $sql_elemento = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
    $r_elemento = $dbo->query($sql_elemento);
    while ($row_elemento = $dbo->fetchArray($r_elemento)) {
        $array_elemento[$row_elemento["IDServicioElemento"]] = $row_elemento;
    }


    $sql_reporte = "SELECT count(IDReservaGeneral) as Total, IDServicio
FROM `ReservaGeneralBck`
WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
and IDServicio = '" . $IDServicio . "'
Group By IDServicio
UNION
SELECT count(IDReservaGeneral) as Total, IDServicio
                                     FROM `ReservaGeneral`
                                     WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                     and IDServicio = '" . $IDServicio . "'
                                     Group By IDServicio
 ";
    /*    echo $sql_reporte;
    exit;
 */
    $result_reporte = $dbo->query($sql_reporte);

    $nombre = "ReservaDetalle_Reporte:" . date("Y_m_d");

    $NumSocios = $dbo->rows($result_reporte);

    if ($NumSocios > 0) {

        $html  = "";
        $html .= "<table width='100%' border='1'>";

        $html .= "<tr>";
        $html .= "<th colspan='5'>RESUMEN </th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>TOTAL SERVICIO</th>";
        $html .= "</tr>";


        while ($Datos = $dbo->fetchArray($result_reporte)) {

            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["Total"]) . "</td>";
            $html .= "</tr>";
        }




        /*Inscritos lista espera*/



        $html .= "<tr>";
        $html .= "<th>INSCRITOS LISTA ESPERA </th>";
        $html .= "</tr>";
        $sql_reporte1 = "SELECT count(IDListaEspera) as Total, IDServicio
        FROM `ListaEspera`
        WHERE IDClub = '" . $IDClub . "'  and FechaInicio>='" . $FechaInicio . " 00:00:00' and FechaFin<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio 
         ";
        $result_reporte1 = $dbo->query($sql_reporte1);
        $NumSocios1 = $dbo->rows($result_reporte1);

        while ($Datos2 = $dbo->fetchArray($result_reporte1)) {

            $sql_socio = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "'";
            $result_socio = $dbo->query($sql_socio);
            $row_socio = $dbo->fetchArray($result_socio);
            $total_socio = $row_socio["TotalSocio"];

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($Datos2["Total"]) . "</td>";

            $html .= "</tr>";
            /*         $html .= "<td>" . remplaza_tildes($Datos["PersonasQueHabitan"]) . "</td>";
                $html .= "<td>" . remplaza_tildes($Datos["Vehiculos"]) . "</td>";
                $html .= "<td>" . remplaza_tildes($Datos["Mascotas"]) . "</td>";
                $html .= "<td>" . remplaza_tildes($Datos["LlamarEnCasoDeEmergencia"]) . "</td>"; */
        }

        $html .= "<tr>";
        $html .= "<th>TOTAL CANCELACIONES RESERVA </th>";
        $html .= "</tr>";
        $sql_reporte1 = "SELECT count(IDReservaGeneral) as TotalReservasCanceladas, IDServicio
        FROM `ReservaGeneralEliminada`
        WHERE IDClub = '" . $IDClub . "'  and FechaTrCr>='" . $FechaInicio . " 00:00:00' and FechaTrCr<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio 
         ";
        $result_reporte1 = $dbo->query($sql_reporte1);
        $NumSocios1 = $dbo->rows($result_reporte1);

        while ($DatosCancelacion = $dbo->fetchArray($result_reporte1)) {



            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($DatosCancelacion["TotalReservasCanceladas"]) . "</td>";

            $html .= "</tr>";
        }


        $html .= "<tr>";
        $html .= "<th>TOTAL SOCIOS</th>";
        $html .= "<th>SOCIOS ACTIVOS EN APP A LA FECHA</th>";
        $html .= "<th>SOCIOS NUEVOS EN EL PERIODO</th>";
        $html .= "<th>SOCIOS ACTIVOS EN EL PERIODO</th>";
        $html .= "<th>TOTAL SOCIOS TALONERA</th>";
        $html .= "</tr>";

        $sql_socio = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "'";
        $result_socio = $dbo->query($sql_socio);
        $row_socio = $dbo->fetchArray($result_socio);
        $total_socio = $row_socio["TotalSocio"];

        $sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "' and Token <> ''";
        $result_socio_activo = $dbo->query($sql_socio_activo);
        $row_socio_activo = $dbo->fetchArray($result_socio_activo);
        $total_socio_activo = $row_socio_activo["TotalSocio"];

        $sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "' and Token <> '' and FechaPrimerIngreso >= '" . $_POST["FechaInicial"] . "' and FechaPrimerIngreso <= '" . $_POST["FechaFinal"] . "'";
        $result_socio_activo = $dbo->query($sql_socio_activo);
        $row_socio_activo = $dbo->fetchArray($result_socio_activo);
        $total_socio_nuevo = $row_socio_activo["TotalSocio"];

        $sql_socio_talonera = "Select count(IDSocio) TotalSocio From SocioTalonera Where IDClub = '" . $IDClub . "' and FechaCompra >= '" . $_POST["FechaInicial"] . "' and FechaCompra <= '" . $_POST["FechaFinal"] . "'";
        $result_socio_talonera = $dbo->query($sql_socio_talonera);
        $row_socio_talonera = $dbo->fetchArray($result_socio_talonera);
        $total_socio_talonera = $row_socio_talonera["TotalSocio"];

        $activos_periodo = $total_socio_activo - $total_socio_nuevo;

        $html .= "<tr>";

        $html .= "<td>" . remplaza_tildes($total_socio) . "</td>";
        $html .= "<td>" . remplaza_tildes($total_socio_activo) . "</td>";
        $html .= "<td>" . remplaza_tildes($total_socio_nuevo) . "</td>";
        $html .= "<td>" . remplaza_tildes($activos_periodo) . "</td>";
        $html .= "<td>" . remplaza_tildes($total_socio_talonera) . "</td>";

        $html .= "</tr>";


        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x DIA DE LA SEMANA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>DIA DE LA SEMANA</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte3 = "SELECT count(IDReservaGeneral) as Total, IDServicio, DAYOFWEEK(Fecha) AS Dia_Semana
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio, Dia_Semana
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, DAYOFWEEK(Fecha) AS Dia_Semana
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By IDServicio, Dia_Semana
                                    ORDER BY Dia_Semana ASC
         ";
        /*         echo $sql_reporte3;
        exit; */
        $result_reporte3 = $dbo->query($sql_reporte3);
        while ($Datos3 = $dbo->fetchArray($result_reporte3)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos3["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos3["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes(SIMResources::$dias_semana_reporte_x_periodo[$Datos3["Dia_Semana"]]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos3["Total"]) . "</td>";
            $html .= "</tr>";
        }

        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x DIA x HORA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>DIA</th>";
        $html .= "<th>HORA</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte4 = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora, DAYOFWEEK(Fecha) AS Dia_Semana
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio, Dia_Semana,Hora

        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, Hora, DAYOFWEEK(Fecha) AS Dia_Semana
                                    FROM `ReservaGeneralBck`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By IDServicio, Dia_Semana,Hora
                                    Order by Dia_Semana,Hora
         ";

        $result_reporte4 = $dbo->query($sql_reporte4);
        while ($Datos4 = $dbo->fetchArray($result_reporte4)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos4["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos4["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes(SIMResources::$dias_semana_reporte_x_periodo[$Datos4["Dia_Semana"]]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos4["Hora"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos4["Total"]) . "</td>";
            $html .= "</tr>";
        }

        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x HORA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>HORA</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte5 = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio, Hora
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, Hora
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By IDServicio, Hora
                                    Order by Total DESC
         ";

        $result_reporte5 = $dbo->query($sql_reporte5);
        while ($Datos5 = $dbo->fetchArray($result_reporte5)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos5["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos5["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos5["Hora"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos5["Total"]) . "</td>";
            $html .= "</tr>";
        }

        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x DIA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>DIA</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte6 = "SELECT count(IDReservaGeneral) as Total, IDServicio, Fecha
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By Fecha
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, Fecha
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By Fecha
                                    Order by Fecha ASC
         ";

        $result_reporte6 = $dbo->query($sql_reporte6);
        while ($Datos6 = $dbo->fetchArray($result_reporte6)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos6["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos6["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos6["Fecha"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos6["Total"]) . "</td>";
            $html .= "</tr>";
        }

        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x ELEMENTO x HORA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>HORA</th>";
        $html .= "<th>ELEMENTO</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte9 = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora,IDServicioElemento
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicio, Hora
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, Hora,IDServicioElemento
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By IDServicio, Hora
                                    Order by Total DESC
        ";
        $result_reporte9 = $dbo->query($sql_reporte9);
        while ($Datos9 = $dbo->fetchArray($result_reporte9)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos9["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos9["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos9["Hora"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($array_elem[$Datos9["IDServicioElemento"]]["Nombre"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos9["Total"]) . "</td>";
            $html .= "</tr>";
        }


        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x ELEMENTO x DIA</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>FECHA</th>";
        $html .= "<th>ELEMENTO</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte10 = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento,Fecha
		FROM `ReservaGeneralBck`
		WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
		and IDServicio = '" . $IDServicio . "'
		Group By Fecha,IDServicioElemento
		UNION
		SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento,Fecha
		FROM `ReservaGeneral`
		WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
		and IDServicio = '" . $IDServicio . "'
		Group By Fecha,IDServicioElemento
		Order by Fecha ASC";

        $result_reporte10 = $dbo->query($sql_reporte10);
        while ($Datos10 = $dbo->fetchArray($result_reporte10)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos10["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos10["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos10["Fecha"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($array_elem[$Datos10["IDServicioElemento"]]["Nombre"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos10["Total"]) . "</td>";
            $html .= "</tr>";
        }


        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x ELEMENTO</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>ELEMENTO</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte7 = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "'
        Group By IDServicioElemento
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "'
                                    Group By IDServicioElemento
                                    Order by Total DESC
         ";

        $result_reporte7 = $dbo->query($sql_reporte7);
        while ($Datos7 = $dbo->fetchArray($result_reporte7)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos7["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos7["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($array_elem[$Datos7["IDServicioElemento"]]["Nombre"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos7["Total"]) . "</td>";
            $html .= "</tr>";
        }




        $html .= "<tr>";
        $html .= "<th colspan='5'>RESERVAS x AUXILIAR</th>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
        $html .= "<th>ELEMENTO</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";

        $sql_reporte8 = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDAuxiliar
        FROM `ReservaGeneralBck`
        WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
        and IDServicio = '" . $IDServicio . "' and IDAuxiliar <> ''
        Group By IDAuxiliar
        UNION
        SELECT count(IDReservaGeneral) as Total, IDServicio, IDAuxiliar
                                    FROM `ReservaGeneral`
                                    WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
                                    and IDServicio = '" . $IDServicio . "' and IDAuxiliar <> ''
                                    Group By IDAuxiliar
                                    Order by Total DESC
         ";

        $result_reporte8 = $dbo->query($sql_reporte8);
        while ($Datos8 = $dbo->fetchArray($result_reporte8)) {
            $NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $Datos8["IDServicio"] . "'");
            if (empty($NombreSer)) {
                $IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $Datos8["IDServicio"] . "'");
                $NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_POST['IDClub'] . "' AND Activo = 'S'");
            }
            $IDAux = str_replace(",", "", $Datos8["IDAuxiliar"]);
            $NomAux = $array_aux[$IDAux]["Nombre"];
            if (empty($NomAux))
                $NomAux = "eliminado";
            else {
                $NomAux;
            }

            $html .= "<tr>";

            $html .= "<td>" . remplaza_tildes($NombreSer)  . "</td>";
            $html .= "<td>" . remplaza_tildes($NomAux) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos8["Total"]) . "</td>";
            $html .= "</tr>";
        }


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
}
?>