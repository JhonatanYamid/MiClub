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

if (!empty($_POST["FechaDeInicio"]) && !empty($_POST["FechaFinal"])) {
    $condicion_fecha = " and FechaDeInicio  >= '" . $_POST["FechaDeInicio"] . "'  and FechaDeInicio <= '" . $_POST["FechaFinal"] . "'";
}


$sql_reporte = "Select * From BquillaNoResidentes
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDBquillaNoResidentes DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "BquillaNoResidentes_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>ID SOCIO</th>";
    $html .= "<th>NOMBRE SOCIO</th>";
    $html .= "<th>NOMBRE VISITANTE</th>";
    $html .= "<th>NUMERO DOCUMENTO VISITANTE</th>";
    $html .= "<th>PARENTESCO</th>";
    $html .= "<th>FECHA DE INICIO</th>";
    $html .= "<th>FECHA FINAL</th>";
    $html .= "<th>CIUDAD DE PROCEDENCIA DEL VISITANTE</th>";
    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NombreVisitante"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumentoInvitado"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeInicio"]   . "</td>";
        $html .= "<td>" . $Datos["FechaFinal"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDeProcedenciaDelVisitante"]) . "</td>";
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