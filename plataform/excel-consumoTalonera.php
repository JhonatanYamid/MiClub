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
    $texto = str_replace("Ñ", "&Ntilde;", $texto);
    $texto = str_replace("Á", "&Aacute;", $texto);
    $texto = str_replace("É", "&Eacute;", $texto);
    $texto = str_replace("Í", "&Iacute;", $texto);
    $texto = str_replace("Ó", "&Oacute;", $texto);
    $texto = str_replace("Ú", "&Uacute;", $texto);
    return $texto;
}

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " AND FechaTrCr BETWEEN '" . $_POST["FechaInicio"] . " 00:00.00'  AND '" . $_POST["FechaFin"] . " 23:59:59'";
}

$sql_reporte = "SELECT IDSocioTalonera, IDReservaGeneral, SocioConsume, FechaConsumo
                FROM ConsumoSocioTalonera 
                WHERE IDClub = ".$_POST["IDClub"].$condicion_fecha." ORDER BY IDConsumoSocioTalonera DESC";

echo $sql_reporte;
exit();

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Reporte_Reservas_Con_Talonera:" . date("Y_m_d");

$Registros = $dbo->rows($result_reporte);

if ($Registros > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='12'>Se encontraron " . $Registros . " registro(s) </th>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<th colspan='2'>INFORMACION SOCIOS </th>";
    $html .= "<th colspan='5'>INFORMACION RESERVA </th>";
    $html .= "<th colspan='5'>INFORMACION TALONERA </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    /*DATOS SOCIOS */
    $html .= "<th>SOCIO</th>";
    $html .= "<th>PERSONA QUE CONSUME</th>";
    /*DATOS RESERVA */
    $html .= "<th>ID_RESERVA</th>";
    $html .= "<th>SERVICIO</th>";
    $html .= "<th>FECHA DE RESERVA</th>";
    $html .= "<th>HORA RESERVA</th>";
    $html .= "<th>RESERVA CUMPLIDA</th>";
    /*DATOS TALONERA*/
    $html .= "<th>ID_TALONERA</th>";
    $html .= "<th>TALONERA</th>";
    $html .= "<th>FECHA COMPRA</th>";
    $html .= "<th>FECHA VENCIMIENTO</th>";
    $html .= "<th>SALDO MONEDERO</th>";

    $html .= "</tr>";

    while ($Datos = $dbo->fetchArray($result_reporte)) {

        //SocioTalonera
        $camposST = ["IDTalonera", "IDSocio", "FechaCompra", "FechaVencimiento", "SaldoMonedero"];
        $socioTalonera = $dbo->getFields("SocioTalonera", $camposST, "IDSocioTalonera = ".$Datos['IDSocioTalonera']);

        //Reserva
        $camposR = ["IDServicio", "Fecha", "Hora", "IF(Cumplida = 'S','Si',IF(Cumplida = 'N','No',IF(Cumplida = 'I','Incumplida',''))) as Cumplida"];
        $reserva = $dbo->getFields("ReservaGeneral", $camposR, "IDReservaGeneral = ".$Datos['IDReservaGeneral']);

        //Talonera
        $talonera = $dbo->getFields("Talonera", "NombreTalonera", "IDTalonera = ". $socioTalonera['IDTalonera']);

        //Nombre socio
        $NombreSocio = $dbo->getFields("Socio", "CONCAT(Nombre,' ',Apellido)", "IDSocio = ". $socioTalonera["IDSocio"]);

        //Nombre del servicio
        $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = ".$reserva['IDServicio']);
        $NombreServicio = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = ".$_POST["IDClub"]." AND IDServicioMaestro = ".$IDServicioMaestro." AND Activo='S'");

        if (empty($NombreServicio)) 
            $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = " . $IDServicioMaestro);
        
        $html .= "<tr>";
        /*Datos Socio*/
        $html .= "<td>" . remplaza_tildes(utf8_encode($NombreSocio)) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode($Datos["SocioConsume"])) . "</td>";

        /*Datos Reserva*/
        $html .= "<td>" . $Datos['IDReservaGeneral'] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode($NombreServicio)) . "</td>";
        $html .= "<td>" . utf8_encode($reserva['Fecha']) . "</td>";
        $html .= "<td>" . utf8_encode($reserva['Hora']) . "</td>";
        $html .= "<td>" . utf8_encode($reserva['Cumplida']) . "</td>";

        /*Datos Talonera*/
        $html .= "<td>" . $Datos['IDSocioTalonera'] . "</td>";
        $html .= "<td>" . remplaza_tildes($talonera) . "</td>";
        $html .= "<td>" . $socioTalonera['FechaCompra'] . "</td>";
        $html .= "<td>" . $socioTalonera['FechaVencimiento'] . "</td>";
        $html .= "<td>" . $socioTalonera['SaldoMonedero'] . "</td>";
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
?>