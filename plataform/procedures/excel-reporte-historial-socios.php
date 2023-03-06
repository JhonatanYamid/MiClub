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
$get_frm = SIMUtil::makeSafe($_GET);
$AnioInicio = $get_frm['a'];
$MesInicio = $get_frm['m'];
$fechaActual = $AnioInicio . '-' . $MesInicio;

$IDClub = $_SESSION['club'];
$sql_reporte = "SELECT HistorialCuotasSociales.*, DATE(FechaInicioPeriodo) AS FechaInicioPeriodo FROM HistorialCuotasSociales WHERE IDClub = '" . $IDClub . "' AND concat(Date_format(FechaInicioPeriodo,'%Y'),'-',Date_format(FechaInicioPeriodo,'%m')) = '" . $fechaActual . "'";

// $IDUsuario = $_POST['IDUsuario'];
// $tableJoin = "";
// $where = "";
// $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
// if ($datos_usuario['IDPerfil'] != 1 && $datos_usuario['IDPerfil'] != 0) {
//     $Socio_numeroDocumento = $datos_usuario['NumeroDocumento'];
//     $tableJoin .= ",Socio ";
//     $where .= " AND AuxiliosSolicitud.IDSocio = Socio.IDSocio AND Socio.DocumentoEspecialista = '" . $Socio_numeroDocumento . "' ";
// }

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Historial_Cuotas_Sociales_Corte:" . date("Y_m_d");

$NumCuotasSociales = $dbo->rows($result_reporte);
$resCuotasSociales = $dbo->assoc($result_reporte);

if ($NumCuotasSociales > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='7'>Se encontraron " . $NumCuotasSociales . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>Accion</th>";
    $html .= "<th>Socio</th>";
    $html .= "<th>Categoria</th>";
    $html .= "<th>Fecha Cuota</th>";
    $html .= "<th>Saldo</th>";
    $html .= "<th>Estado</th>";
    $html .= "<th>Abono</th>";
    $html .= "</tr>";

    $result_reporte = $dbo->query($sql_reporte);
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos['IDSocio'] . "' ", "array");
        $html .= "<tr>";
        $html .= "<td>" . $datos_usuario["Accion"] . "</td>";
        $html .= "<td>" . $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"] . "</td>";
        $html .= "<td>" . $dbo->getFields('Categoria', 'Nombre', 'IDCategoria = "' . $Datos["IDCategoria"] . '"') . "</td>";
        $html .= "<td>" . $Datos["FechaInicioPeriodo"] . "</td>";
        $html .= "<td>$" . number_format($Datos["Saldo"], 2) . "</td>";
        $html .= "<td>" . SIMResources::$EstadoPago[$Datos["Estado"]] . "</td>";

        $sql_detalle = "SELECT MontoPago FROM DetalleHistorialCuotasSociales WHERE IDHistorialCuotasSociales = " . $Datos['IDHistorialCuotasSociales'];
        $q_detalle = $dbo->query($sql_detalle);
        $sumaMonto = 0;
        while ($r_detalle = $dbo->assoc($q_detalle)) {
            $sumaMonto += $r_detalle['MontoPago'];
        }
        $html .= "<td>$" . number_format($sumaMonto, 2) . "</td>";

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
    </body>;

    </html>
<?php
    exit();
} else {
    echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
?>