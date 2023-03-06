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

$IDClub = $_GET['IDClub'];

$sql = "SELECT IDEvento2, IDNoSocios, UsuarioTrCr, FechaTrCr
        FROM EventoRegistro2
        WHERE IDClub = $IDClub AND IDNoSocios > 0 ORDER BY FechaTrCr DESC";

// echo $sql;
// exit();
$result = $dbo->query($sql);

$nombre = "Participantes_Externos2_Reporte:" . date("Y_m_d");

$registros = $dbo->rows($result);

if ($registros > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='16'>Se encontraron " . $registros . " registro(s) </th>";
    $html .= "</tr>";
    
    $html .= "<tr>";
    $html .= "<th colspan='5'>INFORMACION PARTICIPANTE</th>";
    $html .= "<th colspan='9'>INFORMACION EVENTO</th>";
    $html .= "<th colspan='2'>INFORMACION REGISTRO</th>";
    $html .= "</tr>";

    $html .= "<tr>";
    //DATOS NOSOCIO
    $html .= "<th>NUMERO DOCUMENTO</th>";
    $html .= "<th>NOMBRE</th>";
    $html .= "<th>EDAD</th>";
    $html .= "<th>EMAIL</th>";
    $html .= "<th>CELULAR</th>";
    //DATOS EVENTO
    $html .= "<th>SECCION</th>";
    $html .= "<th>EVENTO</th>";
    $html .= "<th>INTRODUCCION</th>";
    $html .= "<th>LUGAR</th>";
    $html .= "<th>HORA</th>";
    $html .= "<th>FECHA INICIO</th>";
    $html .= "<th>FECHA FIN</th>";
    $html .= "<th>VALOR</th>";
    $html .= "<th>MAXIMO PARTICIPANTES</th>";
    //DATOS PAGO
    $html .= "<th>REGISTRADO POR</th>";
    $html .= "<th>FECHA DE REGISTRO</th>";

    $html .= "</tr>";

    while ($Datos = $dbo->fetchArray($result)) {

        //NoSocios
        $nosocio = $dbo->fetchAll("NoSocios", " IDNosocios = " . $Datos["IDNoSocios"], "array");

        //Evento
        $evento = $dbo->getFields("Evento2", array("IDSeccionEvento2", "Titular", "Introduccion", "Lugar", "Hora", "FechaEvento","FechaFinEvento", "Valor", "MaximoParticipantes"), "IDEvento2 = " . $Datos["IDEvento2"]);

        //Seccion
        $seccion = $dbo->getFields("SeccionEvento2", "Nombre", "IDSeccionEvento2 = " . $evento["IDSeccionEvento2"]);
        
        $html .= "<tr>";
        //DATOS SOCIO
        $html .= "<td>" . $nosocio["NumeroDocumento"] . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($nosocio["Nombre"])) ."</td>";
        $html .= "<td>" . SIMUtil::Calcular_Edad($nosocio['FechaNacimiento']) . "</td>";
        $html .= "<td>" . $nosocio["CorreoElectronico"] . "</td>";
        $html .= "<td>" . $nosocio["Celular"] . "</td>";
        //DATOS EVENTO
        $html .= "<td>" . remplaza_tildes(utf8_decode($seccion)) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode($evento["Titular"])) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode($evento["Introduccion"])) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode($evento["Lugar"])) . "</td>";
        $html .= "<td>" . $evento["Hora"] . "</td>";
        $html .= "<td>" . $evento["FechaEvento"] . "</td>";
        $html .= "<td>" . $evento["FechaFinEvento"] . "</td>";
        $html .= "<td>" . $evento["Valor"] . "</td>";
        $html .= "<td>" . $evento["MaximoParticipantes"] . "</td>";
        //DATOS REGISTRO
        $html .= "<td>" . remplaza_tildes(utf8_decode($Datos["UsuarioTrCr"])) . "</td>";
        $html .= "<td>" . $Datos["FechaTrCr"] . "</td>";
        $html .= "</tr>";

    }
    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $html;
    exit();
}
