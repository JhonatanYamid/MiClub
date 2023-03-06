<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
require dirname(__FILE__) . "/../../admin/config.inc.php";

include("../../plataform/includes/async/reporteinvitaciones.async.php");

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
$html .= "<th>EXONERADO</th>";
$html .= "<th>OBSERVACIONES</th>";
$html .= "<th>TIPO DE INVITACION</th>";
// $html .= "</tr>";
$f_campos = $dbo->all("CampoFormularioInvitado", "IDClub = '" . $_GET["IDClub"] . "' AND Publicar = 'S' Order by IDCampoFormularioInvitado");
$IDCampoFormularioInvitado = [];


while ($f = $dbo->object($f_campos)) {


    $IDCampoFormularioInvitado[] = $f->IDCampoFormularioInvitado;
    $html .= "<th>" . utf8_encode($f->EtiquetaCampo) . "</th>";
}
// $html .= "</tr>";

$html .= "</tr>";


while ($data = $dbo->fetchArray($r_sql)) {

    switch ($data["Estado"]):
        case "I";
            $estado = "Ya ingreso: " . $data["FechaIngresoClub"];
            $boton_registro_ingreso = '';
            $fechaingresoclub = explode(' ', $data["FechaIngresoClub"]);
            $horaIngreso = strtotime($fechaingresoclub[1]);
            $horaLimite = strtotime("19:00:00");

            if ($horaIngreso >= $horaLimite) {
                $exonerado = 'Si';
            } else {
                $exonerado = 'No';
            }

            break;
        case "P";
            $estado = "Pendiente Ingreso";
            $boton_registro_ingreso = '<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id=' . $Datos["IDSocioInvitado"] . '' . '" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
            $exonerado = 'No';
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
    $html .= "<td>" . utf8_decode($data["Nombre"]) . "</td>";
    $html .= "<td>" . utf8_decode($data["FechaIngreso"]) . "</td>";
    $html .= "<td>" . utf8_decode($exonerado) . "</td>";
    $html .= "<td>" . utf8_decode($data["Observaciones"]) . "</td>";
    $html .= "<td>" . utf8_decode($data["TipoInvitacion"]) . "</td>";

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

// exit();

//construimos el excel
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;
exit();

//->>>>>>>>>>>>>>>>>>>><

// Consulto los dias asignados como fecha especial para no tomar en cuenta en reporte
/* $sql_fecha_Especial = $dbo->query("Select Fecha From FechaEspecialInvitado Where IDClub = '" . SIMUser::get("club") . "'");
while ($row_fecha_especial = $dbo->fetchArray($sql_fecha_Especial)):
    $condicion_fecha .= " and FechaIngreso <> '" . $row_fecha_especial["Fecha"] . "'";
endwhile;


if (!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])) {
    $where = " AND SocioInvitado.FechaIngreso >= '" . $_GET["FechaInicio"] . " 00:00:00' and SocioInvitado.FechaIngreso <= '" . $_GET["FechaFin"] . " 23:59:59'";
}


$sql_reporte = "Select SocioInvitado.*, Socio.IDSocio, Accion, TipoSocio, Socio.NumeroDocumento as DocumentoSocio, Email, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio From Socio, SocioInvitado Where Socio.IDClub = '" . $_GET["IDClub"] . "' and Socio.IDSocio = SocioInvitado.IDSocio" . $where ;
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Invitaciones_" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);


if ($NumSocios > 0) {
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
    $html .= "<th>APELLIDO</th>";
    $html .= "<th>EMAIL</th>";
    $html .= "<th>ESTADO</th>";
    $html .= "<th>DOCUMENTO INVITADO</th>";
    $html .= "<th>NOMBRE INVITADO</th>";
    $html .= "<th>FECHA INGRESO</th>";
    $html .= "<th>OBSERVACIONES</th>";    */

    //Campos dinamicos del formulario de invitados
    /* $f_campos = $dbo->all("CampoFormularioInvitado", "IDClub = '" . $_GET["IDClub"] . "' Order by IDCampoFormularioInvitado");
    $IDCampoFormularioInvitado = [];
    while ($f = $dbo->object($f_campos)) {
        $IDCampoFormularioInvitado[] = $f->IDCampoFormularioInvitado;
        $html .= "<th>" . $f->EtiquetaCampo . "</th>";
    }
    $html .= "</tr>";

    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        switch ($Datos["Estado"]):
			case "I";
				$estado = "Ya ingreso: " . $Datos["FechaIngresoClub"];
				$boton_registro_ingreso = '';
				break;
			case "P";
				$estado = "Pendiente Ingreso";
				$boton_registro_ingreso = '<a class="green btn btn-primary btn-sm btnModal fancybox" href="invitados.php?action=registraingreso&id=' . $Datos["IDSocioInvitado"] . '' . '" data-fancybox-type="iframe"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
				break;
        endswitch;



        $html .= "<tr>";
        $html .= "<td>" . $Datos["TipoSocio"] . "</td>";
        $html .= "<td>" . $Datos["Accion"] . "</td>";
        $html .= "<td>" . $Datos["DocumentoSocio"] . "</td>";
        $html .= "<td>" . utf8_decode($Datos["Socio"]) . "</td>";
        $html .= "<td>" . utf8_decode($Datos["Apellido"]) . "</td>";
        $html .= "<td>" . utf8_decode($Datos["Email"]) . "</td>";
        $html .= "<td>" . $estado . "</td>";
        $html .= "<td>" . utf8_decode($Datos["NumeroDocumento"]) . "</td>";
        $html .= "<td>" . utf8_decode($Datos["Nombre"]) . "</td>";
        $html .= "<td>" . utf8_decode($Datos["FechaIngreso"]) . "</td>";
        $html .= "<td>" . utf8_decode($Datos["Observaciones"]) . "</td>";

        //Campos dinamicos del formulario de invitados
        foreach ($IDCampoFormularioInvitado as $id) {
                $sql = "SELECT Valor FROM InvitadosOtrosDatos
					WHERE IDCampoFormularioInvitado=$id
					AND IDInvitacion=" . $Datos["IDSocioInvitado"] . "
					LIMIT 1";

                $d = $dbo->query($sql);
                $d = $dbo->fetch($d);

                $html .= "<td>" . $d["Valor"] . "</td>";

        }
        $html .= "</tr>";
    }
    $html .= "</table>";

    construimos el excel
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $html; *
    exit();
}
 */
