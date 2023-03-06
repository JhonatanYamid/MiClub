<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");
$sql_serv = "SELECT IDServicioMaestro,TituloServicio FROM ServicioClub WHERE IDClub = 8";
$r_serv = $dbo->query($sql_serv);
while ($row_serv = $dbo->fetchArray($r_serv)) {
	$IDServ = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $row_serv["IDServicioMaestro"] . "' and IDClub = 8 ");
	if ($row_serv["TituloServicio"] == "") {
		$array_nombre_servicio[$IDServ] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $row_serv["IDServicioMaestro"] . "' ");
	} else {
		$array_nombre_servicio[$IDServ] = $row_serv["TituloServicio"];
	}
}


$sql_med = "SELECT IDSocio, Nombre, Apellido, NumeroDocumento FROM Socio WHERE IDClub = 8";
$r_med = $dbo->query($sql_med);
while ($row_soc = $dbo->fetchArray($r_med)) {


	$sql_reserva = "SELECT COUNT(IDServicio) as TotalReservas,IDReservaGeneral,IDServicio
FROM ReservaGeneralBck
WHERE IDSocio = " . $row_soc["IDSocio"] . " and Fecha >= '2021-01-01' and Fecha <= '2021-12-31' GROUP BY IDServicio";
	$r_reserva = $dbo->query($sql_reserva);
	while ($row_reserva = $dbo->fetchArray($r_reserva)) {
		//echo "<br>". $row_reserva["IDServicio"] . "=" . $array_nombre_servicio[$row_reserva["IDServicio"]] ."=". $row_soc["NumeroDocumento"] . "=". $row_reserva["TotalReservas"];
		$array_reserva[$row_soc["IDSocio"]][$row_reserva["IDServicio"]] = $row_reserva["TotalReservas"];
		$array_servicio[$row_reserva["IDServicio"]] = $row_reserva["IDServicio"];
		$array_socio[$row_soc["IDSocio"]]["Nombre"] = $row_soc["Nombre"] . " " . $row_soc["Apellido"];
		$array_socio[$row_soc["IDSocio"]]["Documento"] = $row_soc["NumeroDocumento"];
	}


	//Donde fue invitado
	$sql_inv = "SELECT COUNT(IDServicio) as TotalReservas,RG.IDReservaGeneral,IDServicio
	FROM ReservaGeneralBck RG, ReservaGeneralInvitado RGI
	WHERE RG.IDReservaGeneral=RGI.IDReservaGeneral and RGI.IDSocio = " . $row_soc["IDSocio"] . " and Fecha >= '2021-01-01' and Fecha <= '2021-12-31' GROUP BY IDServicio";
	$r_inv = $dbo->query($sql_inv);
	while ($row_inv = $dbo->fetchArray($r_inv)) {
		//echo "<br>". $row_soc["NumeroDocumento"] . "=". $row_inv["TotalReservas"];
		$array_reserva[$row_soc["IDSocio"]][$row_inv["IDServicio"]] = $row_inv["TotalReservas"];
		$array_servicio[$row_inv["IDServicio"]] = $row_inv["IDServicio"];
	}
}

echo "<table border=1>
			<tr>
				<td>Documento</td>
				<td>Nombre</td>";
foreach ($array_servicio as $key_servicio => $id_serv) {
	echo "<td>" . $array_nombre_servicio[$id_serv] . "</td>";
}
echo "<td>Total</td>
			</tr>";
foreach ($array_reserva as $key_soc => $datos) {
	$suma = 0;
	echo "<td>" . $array_socio[$key_soc]["Documento"] . "</td>";
	echo "<td>" . $array_socio[$key_soc]["Nombre"] . "</td>";
	foreach ($array_servicio as $key_servicio => $id_serv) {
		$suma += (int)$array_reserva[$key_soc][$id_serv];
		echo "<td>" . $array_reserva[$key_soc][$id_serv] . "</td>";
	}
	echo "<td>" . $suma . "</td>
			</tr>";
}
echo "</tr>
		</table>";
exit;
