<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
require dirname(__FILE__) . "/../../admin/config.inc.php";

include("../../plataform/includes/async/reporteinvitacionesespeciales.async.php");

$nombre = "Invitaciones_" . date("Y_m_d");

$r_sql = $dbo->query($sql);
$NumSocios = $dbo->rows($r_sql);

$html = "";
$html .= "<table width='100%' border='1'>";
$html .= "<tr>";
$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>TIPO SOCIO</th>";
$html .= "<th>NUMERO DERECHO</th>";
$html .= "<th>DOCUMENTO</th>";
$html .= "<th>NOMBRE</th>";
/* $html .= "<th>APELLIDO</th>"; */
$html .= "<th>EMAIL</th>";
$html .= "<th>ESTADO</th>";
$html .= "<th>DOCUMENTO INVITADO</th>";
$html .= "<th>NOMBRE INVITADO</th>";
$html .= "<th>FECHA INGRESO</th>";
$html .= "<th>OBSERVACIONES</th>";
// $html .= "</tr>";
$f_campos = $dbo->all("CampoFormularioInvitado", "IDClub = '" . $_GET["IDClub"] . "' Order by IDCampoFormularioInvitado");
$IDCampoFormularioInvitado = [];


while ($f = $dbo->object($f_campos)) {


    $IDCampoFormularioInvitado[] = $f->IDCampoFormularioInvitado;
    $html .= "<th>" . $f->EtiquetaCampo . "</th>";
}
$html .= "</tr>";

$html .= "</tr>";


while ($data = $dbo->fetchArray($r_sql)) {

    switch ($data["Estado"]):
        case "I";
            $estado = "Ya ingreso: " . $data["FechaIngresoClub"];
            $boton_registro_ingreso = '';
            break;
        case "P";
            $estado = "Pendiente Ingreso";
            $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id=' . $Datos["IDSocioInvitado"] . '' . '" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
            break;
    endswitch;



    $html .= "<tr>";
    $html .= "<td>" . $data["TipoSocio"] . "</td>";
    $html .= "<td>" . $data["Accion"] . "</td>";
    $html .= "<td>" . $data["DocumentoSocio"] . "</td>";
    $html .= "<td>" . utf8_decode($data["Socio"]) . "</td>";
    /*  $html .= "<td>" . utf8_decode($data["Apellido"]) . "</td>"; */
    $html .= "<td>" . utf8_decode($data["Email"]) . "</td>";
    $html .= "<td>" . $estado . "</td>";
    $html .= "<td>" . utf8_decode($data["NumeroDocumento"]) . "</td>";
    $html .= "<td>" . ($data["NombreInvitado"]) . "</td>";
    $html .= "<td>" . utf8_decode($data["FechaInicio"]) . "</td>";
    $html .= "<td>" . utf8_decode($data["Observaciones"]) . "</td>";

    //Campos dinamicos del formulario de invitados
    foreach ($IDCampoFormularioInvitado as $id) {
        $sql = "SELECT Valor FROM InvitadosOtrosDatos
                WHERE IDCampoFormularioInvitado=$id
                AND IDInvitacion=" . $data["IDSocioInvitado"] . "
                LIMIT 1";

        $d = $dbo->query($sql);
        $d = $dbo->fetch($d);

        $html .= "<td>" . $d["Valor"] . "</td>";
    }
    $html .= "</tr>";
}
$html .= "</table>";
// echo $html;
// die();
//construimos el excel
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;
exit();
