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

    if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
        $condicion_fecha = " and Fecha  >= '" . $_POST["FechaInicio"] . "'  AND Fecha <= '" . $_POST["FechaFin"] . "'";
    }

    $sql_serv = "SELECT IDServicioMaestro,TituloServicio FROM ServicioClub WHERE IDClub ='" . $_POST["IDClub"] . "'";

    $r_serv = $dbo->query($sql_serv);
    while ($row_serv = $dbo->fetchArray($r_serv)) {
        $IDServ = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $row_serv["IDServicioMaestro"] . "' AND IDClub='" . $_POST["IDClub"] . "'");
        if ($row_serv["TituloServicio"] == "") {
            $array_nombre_servicio[$IDServ] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $row_serv["IDServicioMaestro"] . "' ");
        } else {
            $array_nombre_servicio[$IDServ] = $row_serv["TituloServicio"];
        }
    }



    $sql_med = "SELECT IDSocio, Nombre, Apellido, NumeroDocumento FROM Socio WHERE IDClub ='" . $_POST["IDClub"] . "'";


    $r_med = $dbo->query($sql_med);

    while ($row_soc = $dbo->fetchArray($r_med)) {


        $sql_reserva = "SELECT COUNT(IDServicio) as TotalReservas,IDReservaGeneral,IDServicio
FROM ReservaGeneralBck
WHERE IDSocio = '" . $row_soc["IDSocio"] . "'" .  $condicion_fecha . " GROUP BY IDServicio";

        $r_reserva = $dbo->query($sql_reserva);
        while ($row_reserva = $dbo->fetchArray($r_reserva)) {

            $array_reserva[$row_soc["IDSocio"]][$row_reserva["IDServicio"]] = $row_reserva["TotalReservas"];
            $array_servicio[$row_reserva["IDServicio"]] = $row_reserva["IDServicio"];
            $array_socio[$row_soc["IDSocio"]]["Nombre"] = $row_soc["Nombre"] . " " . $row_soc["Apellido"];
            $array_socio[$row_soc["IDSocio"]]["Documento"] = $row_soc["NumeroDocumento"];
        }


        //Donde fue invitado
        $sql_inv = "SELECT COUNT(IDServicio) as TotalReservas,RG.IDReservaGeneral,IDServicio
	FROM ReservaGeneralBck RG, ReservaGeneralInvitado RGI
	WHERE RG.IDReservaGeneral=RGI.IDReservaGeneral and RGI.IDSocio = '" . $row_soc["IDSocio"] . "'" . $condicion_fecha . "' GROUP BY IDServicio";

        $r_inv = $dbo->query($sql_inv);
        while ($row_inv = $dbo->fetchArray($r_inv)) {
            //echo "<br>". $row_soc["NumeroDocumento"] . "=". $row_inv["TotalReservas"];
            $array_reserva[$row_soc["IDSocio"]][$row_inv["IDServicio"]] = $row_inv["TotalReservas"];
            $array_servicio[$row_inv["IDServicio"]] = $row_inv["IDServicio"];
        }
    }





    $nombre = "Reporte:" . date("Y_m_d");




    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";

    /* DATOS */
    $html .= "<th>Documento</th>";
    $html .= "<th>Nombre</th>";
    foreach ($array_servicio as $key_servicio => $id_serv) {
        $html .=  "<th>" . $array_nombre_servicio[$id_serv] . "</th>";
    }
    $html .= "<th>TOTAL</th>";



    $html .= "</tr>";

    $html .= "<tr>";

    foreach ($array_reserva as $key_soc => $datos) {
        $suma = 0;
        $html .= "<td>" . $array_socio[$key_soc]["Documento"] . "</td>";
        $html .= "<td>" . $array_socio[$key_soc]["Nombre"] . "</td>";

        foreach ($array_servicio as $key_servicio => $id_serv) {
            $suma += (int)$array_reserva[$key_soc][$id_serv];
            $html .= "<td>" . $array_reserva[$key_soc][$id_serv] . "</td>";
        }
        $html .= "<td>" . $suma . "</td>
                    </tr>";
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

    ?>