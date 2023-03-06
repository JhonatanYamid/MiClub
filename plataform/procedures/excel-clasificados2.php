<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " and FechaInicio >= '" . $_POST["FechaInicio"] . " 00:00:00'  and FechaFin <= '" . $_POST["FechaFin"] . " 23:59:59'";
}


$sql_seccion_cla = "SELECT Nombre,IDSeccionClasificados2 FROM SeccionClasificados2 Where IDClub = '" . $_POST["IDClub"] . "'";
$r_seccion_cla = $dbo->query($sql_seccion_cla);
while ($row_seccion_cla = $dbo->fetchArray($r_seccion_cla)) {
    $array_seccion[$row_seccion_cla["IDSeccionClasificados"]] = $row_seccion_cla["Nombre"];
}

$sql_estado = "SELECT Nombre,IDEstadoClasificado FROM EstadoClasificado Where 1 ";
$r_estado = $dbo->query($sql_estado);
while ($row_estado = $dbo->fetchArray($r_estado)) {
    $array_estado[$row_estado["IDEstadoClasificado"]] = $row_estado["Nombre"];
}

$sql_reporte = "Select * From Clasificado Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order by IDClasificado Desc";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Clasificado_Funcionarios" . date("Y_m_d");

$Num = $dbo->rows($result_reporte);

if ($Num > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $Num . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>Numero</th>";
    $html .= "<th>Seccion</th>";
    $html .= "<th>Nombre Usuario</th>";
    $html .= "<th>Estado</th>";
    $html .= "<th>Descripcion</th>";
    $html .= "<th>Telefono</th>";
    $html .= "<th>Email</th>";
    $html .= "<th>Valor</th>";
    $html .= "<th>Fecha Inicio</th>";
    $html .= "<th>Fecha Fin</th>";
    $html .= "<th>Foto1</th>";
    $html .= "<th>Foto2</th>";
    $html .= "<th>Foto3</th>";
    $html .= "<th>Foto4</th>";
    $html .= "<th>Foto5</th>";
    $html .= "<th>Foto6</th>";

    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        if (!empty($Datos["Foto1"]))
            $Foto1 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto1"] . "'>" . $Datos["Foto1"] . "</a>";
        if (!empty($Datos["Foto2"]))
            $Foto2 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto2"] . "'>" . CLASIFICADOS_ROOT . $Datos["Foto2"] . "</a>";;
        if (!empty($Datos["Foto3"]))
            $Foto3 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto3"] . "'>" . CLASIFICADOS_ROOT . $Datos["Foto3"] . "</a>";;
        if (!empty($Datos["Foto4"]))
            $Foto4 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto4"] . "'>" . CLASIFICADOS_ROOT . $Datos["Foto4"] . "</a>";;
        if (!empty($Datos["Foto5"]))
            $Foto5 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto5"] . "'>" . CLASIFICADOS_ROOT . $Datos["Foto5"] . "</a>";;
        if (!empty($Datos["Foto6"]))
            $Foto6 = "<a href='" . CLASIFICADOS_ROOT . $Datos["Foto6"] . "'>" . CLASIFICADOS_ROOT . $Datos["Foto6"] . "</a>";;

        $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Datos["IDUsuario"] . "'  ", "array");
        $html .= "<tr>";
        $html .= "<td>" . $Datos["IDClasificado"] . "</td>";
        $html .= "<td>" . $array_seccion[$Datos["IDSeccionClasificados"]] . "</td>";
        $html .= "<td>" . $datos_usuario["Nombre"]  . "</td>";
        $html .= "<td>" . $array_estado[$Datos["IDEstadoClasificado"]] . "</td>";
        $html .= "<td>" . $Datos["Descripcion"] . "</td>";
        $html .= "<td>" . $Datos["Telefono"] . "</td>";
        $html .= "<td>" . $Datos["Email"] . "</td>";
        $html .= "<td>" . $Datos["Valor"] . "</td>";
        $html .= "<td>" . $Datos["FechaInicio"] . "</td>";
        $html .= "<td>" . $Datos["FechaFin"] . "</td>";
        $html .= "<td>" . $Foto1 . "</td>";
        $html .= "<td>" . $Foto2 . "</td>";
        $html .= "<td>" . $Foto3 . "</td>";
        $html .= "<td>" . $Foto4 . "</td>";
        $html .= "<td>" . $Foto5 . "</td>";
        $html .= "<td>" . $Foto6 . "</td>";
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
?>