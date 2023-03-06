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

if (!empty($_POST["FechaTrEd"]) && !empty($_POST["FechaTrEd"])) {
    $condicion_fecha = " and FechaTrEd  >= '" . $_POST["FechaTrEd"] . "'  or FechaTrEd <= '" . $_POST["FechaTrEd"] . "'";
}


$sql_reporte = "Select * From EntreLomasCenso 
                                                 
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDEntreLomasCenso DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "EntreLomasCenso_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    /* DATOS EMPLEADO*/
    $html .= "<th>ID SOCIO</th>";
    $html .= "<th>NOMBRE SOCIO</th>";
    $html .= "<th>NOMBRE PROPIETARIO</th>";
    $html .= "<th>CASA</th>";
    $html .= "<th>PERSONAS QUE HABITAN</th>";
    $html .= "<th>VEHICULOS</th>";
    $html .= "<th>MASCOTAS</th>";
    $html .= "<th>LLAMAR EN CASO DE EMERGENCIA</th>";




    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        /*DATOS EMPLEADOS*/
        $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NombrePropietario"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Casa"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["PersonasQueHabitan"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Vehiculos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Mascotas"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["LlamarEnCasoDeEmergencia"]) . "</td>";
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