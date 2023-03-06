<?

SIMReg::setFromStructure(array(
    "title" => "ExporteGestionCartera",
    "table" => "ExporteGestionCartera",
    "key" => "IdExporteGestionCartera",
    "mod" => "Socio"
));


$script = "exportegestioncartera";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {
    case "exportar":

        //Script para exportar reporte de contactos por rango de fechas
        require dirname(__FILE__) . "/../admin/config.inc.php";

        //Fecha Ultimo Backup
        $FechaBackup = '2018-05-30';

        $sql = "Select * From GestionCartera Where FechaRegistro between  '" . $_GET["FechaInicio"] . "' AND '" . $_GET["FechaFin"] . "' AND IDClub = '" . $_GET["IDClub"] . "' AND IDSocio = '" . $_GET["IDSocio"] . "'";
        $nombre = "Reservas" . date("Y_m_d H:i:s");

        $qry = $dbo->query($sql);
        $Num = $dbo->rows($qry);

        $html = "";
        $html .= "<table width='100%' border='1'>";
        $html .= "<tr>";
        $html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<th>SOCIO</th>";
        $html .= "<th>OBSERVACION</th>";
        $html .= "<th>FECHA REGISTRO</th>";
        $html .= "<th>USUARIO</th>";
        $html .= "</tr>";


        while ($row = $dbo->fetchArray($qry, $a)) {

            $html .= "<tr>";
            $html .= "<td>" . $row['IDSocio'] . "</td>";
            $html .= "<td>" . $row['Observacion'] . "</td>";
            $html .= "<td>" . $row['FechaRegistro'] . "</td>";
            $html .= "<td>" . $row['UsuarioTrEd'] . "</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";

        //construimos el excel
        header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
        header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0"); ?>
        <html>

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        </head>

        <body>
            <?php
            echo $html;
            exit();
            ?>
        </body>

        </html>
<?php
        exit();
        break;
}







?>