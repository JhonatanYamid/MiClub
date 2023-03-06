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


$sql_reporte = "Select * From FamiliaresVacunacion
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDFamiliaresVacunacion DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "FamiliaresVacunacion_Reporte:" . date("Y_m_d");

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
    $html .= "<th>TIPO DOCUMENTO FAMILIAR 1</th>";
    $html .= "<th>NUMERO DOCUMENTO FAMILIAR 1</th>";
    $html .= "<th>NOMBRES FAMILIAR 1</th>";
    $html .= "<th>APELLIDOS FAMILIAR 1</th>";
    $html .= "<th>FECHA DE NACIMIENTO FAMILIAR 1</th>";
    $html .= "<th>EDAD FAMILIAR 1</th>";
    $html .= "<th>EPS FAMILIAR 1</th>";
    $html .= "<th>PARENTESCO FAMILIAR 1</th>";
    $html .= "<th>CIUDAD DONDE RESIDE FAMILIAR 1</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 1</th>";
    $html .= "<th>CELULAR FAMILIAR 1</th>";
    $html .= "<th>TIPO DOCUMENTO FAMILIAR 2</th>";
    $html .= "<th>NUMERO DOCUMENTO FAMILIAR 2</th>";
    $html .= "<th>NOMBRES FAMILIAR 2</th>";
    $html .= "<th>APELLIDOS FAMILIAR 2</th>";
    $html .= "<th>FECHA DE NACIMIENTO FAMILIAR 2</th>";
    $html .= "<th>EDAD FAMILIAR 2</th>";
    $html .= "<th>EPS FAMILIAR 2</th>";
    $html .= "<th>PARENTESCO FAMILIAR 2</th>";
    $html .= "<th>CIUDAD DONDE RESIDE FAMILIAR 2</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 2</th>";
    $html .= "<th>CELULAR FAMILIAR 2</th>";
    $html .= "<th>TIPO DOCUMENTO FAMILIAR 3</th>";
    $html .= "<th>NUMERO DOCUMENTO FAMILIAR 3</th>";
    $html .= "<th>NOMBRES FAMILIAR 3</th>";
    $html .= "<th>APELLIDOS FAMILIAR 3</th>";
    $html .= "<th>FECHA DE NACIMIENTO FAMILIAR 3</th>";
    $html .= "<th>EDAD FAMILIAR 3</th>";
    $html .= "<th>EPS FAMILIAR 3</th>";
    $html .= "<th>PARENTESCO FAMILIAR 3</th>";
    $html .= "<th>CIUDAD DONDE RESIDE FAMILIAR 3</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 3</th>";
    $html .= "<th>CELULAR FAMILIAR 3</th>";
    $html .= "<th>TIPO DOCUMENTO FAMILIAR 4</th>";
    $html .= "<th>NUMERO DOCUMENTO FAMILIAR 4</th>";
    $html .= "<th>NOMBRES FAMILIAR 4</th>";
    $html .= "<th>APELLIDOS FAMILIAR 4</th>";
    $html .= "<th>FECHA DE NACIMIENTO FAMILIAR 4</th>";
    $html .= "<th>EDAD FAMILIAR 4</th>";
    $html .= "<th>EPS FAMILIAR 4</th>";
    $html .= "<th>PARENTESCO FAMILIAR 4</th>";
    $html .= "<th>CIUDAD DONDE RESIDE FAMILIAR 4</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 4</th>";
    $html .= "<th>CELULAR FAMILIAR 4</th>";
    $html .= "<th>TIPO DOCUMENTO FAMILIAR 5</th>";
    $html .= "<th>NUMERO DOCUMENTO FAMILIAR 5</th>";
    $html .= "<th>NOMBRES FAMILIAR 5</th>";
    $html .= "<th>APELLIDOS FAMILIAR 5</th>";
    $html .= "<th>FECHA DE NACIMIENTO FAMILIAR 5</th>";
    $html .= "<th>EDAD FAMILIAR 5</th>";
    $html .= "<th>EPS FAMILIAR 5</th>";
    $html .= "<th>PARENTESCO FAMILIAR 5</th>";
    $html .= "<th>CIUDAD DONDE RESIDE FAMILIAR 5</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 5</th>";
    $html .= "<th>CELULAR FAMILIAR 5</th>";
    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDocumento"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumento"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombres"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Apellidos"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeNacimiento"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Edad"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Eps"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDondeReside"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDocumento2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumento2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombres2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Apellidos2"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeNacimiento2"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Edad2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Eps2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDondeReside2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDocumento3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumento3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombres3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Apellidos3"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeNacimiento3"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Edad3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Eps3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDondeReside3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDocumento4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumento4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombres4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Apellidos4"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeNacimiento4"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Edad4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Eps4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDondeReside4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDocumento5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDocumento5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombres5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Apellidos5"]) . "</td>";
        $html .= "<td>" . $Datos["FechaDeNacimiento5"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Edad5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Eps5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Parentesco5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CiudadDondeReside5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Celular5"]) . "</td>";
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