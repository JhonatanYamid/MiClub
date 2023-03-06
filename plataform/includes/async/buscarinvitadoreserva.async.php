<?php
header('Content-Type: text/txt; charset=UTF-8');
include "../../procedures/general_async.php";
$dbo = &SIMDB::get();

if (empty($_POST["Servicio"])) {
    $Servicio = 496;
} else {
    $Servicio = $_POST["Servicio"];
}

if (empty($_POST["Invitado"])) {
    $Invitado = "Maria Fernanda";
} else {
    $Invitado = $_POST["Invitado"];
}

if (empty($_POST["Fecha"])) {
    $Fecha = date("Y-m-d");
} else {
    $Fecha = $_POST["Fecha"];
}

$cadena = "<table id='simple-table' class='table table-striped table-bordered table-hover'>
				<tr>
					<th>Elemento del Servicio</th>
					<th>Hora</th>
					<th>Socio que Invita</th>
				</tr>
				<tbody>
";

$sqlInvitado = "SELECT IDReservaGeneral FROM ReservaGeneralInvitado WHERE Nombre LIKE '%" . $Invitado . "%' AND IDSocio = 0 ORDER BY IDReservaGeneralInvitado DESC";
$qryInvitado = $dbo->query($sqlInvitado);

while ($filaInvitado = $dbo->fetchArray($qryInvitado)) {
    $sqlReserva = "SELECT IDServicioElemento, Hora, NombreSocio FROM ReservaGeneral WHERE IDServicio = '" . $Servicio . "' AND Fecha = '" . $Fecha . "' AND IDReservaGeneral = '" . $filaInvitado["IDReservaGeneral"] . "' AND IDClub = " . SIMUser::get("club");
    $qryReserva = $dbo->query($sqlReserva);

    while ($datos = $dbo->fetchArray($qryReserva)) {
        $elemento = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = " . $datos["IDServicioElemento"]);
		
        $cadena .= "<tr>
						<td>
							" . $elemento . "
						</td>
						<td>
							" . $datos['Hora'] . "
						</td>
						<td>
							" . $datos['NombreSocio'] . "
						</td>
					</tr>";
    }
}

$cadena .= "	</tbody>
			</table>";

echo $cadena;
