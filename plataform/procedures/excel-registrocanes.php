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


$sql_reporte = "Select * From Mascota 
                                                 
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDMascota DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "RegistroCanes_Reporte:" . date("Y_m_d");

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
    $html .= "<th>ACCION</th>";
    $html .= "<th>NOMBRE SOCIO</th>";
    $html .= "<th>NOMBRE CANINO</th>";
    $html .= "<th>TAMAÑO</th>";
    $html .= "<th>RAZA</th>";
    $html .= "<th>CELULAR</th>";
    $html .= "<th>FECHA DE INGRESO</th>";
    $html .= "<th>FECHA FIN</th>";
    $html .= "<th>CEDULA</th>";
    $html .= "<th>DESCRIPCION DE LA MASCOTA</th>";
    $html .= "<th>A QUIEN PERTENECE</th>";




    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        /*DATOS EMPLEADOS*/
        $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Accion", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombre"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Tipo"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Raza"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["FechaDeIngreso"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["FechaFin"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Cedula"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Descripcion"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["AQuienPertenece"]) . "</td>";
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