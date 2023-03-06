<?php
//Script para exportar reporte de contactos por rango de fechas
require dirname(__FILE__) . "/../admin/config.inc.php";

//Fecha Ultimo Backup
$FechaBackup = '2018-05-30';

$sql_socios = "SELECT IDCategoria,IDSocio,NumeroDocumento,Accion,AccionPadre,Nombre,Apellido,TipoSocio,Email,CorreoElectronico,FechaNacimiento,Celular FROM Socio WHERE IDClub = '" . $_GET["IDClub"] . "'";
$r_socios = $dbo->query($sql_socios);
while ($row_socios = $dbo->fetchArray($r_socios)) {
    $array_socios[$row_socios["IDSocio"]] = $row_socios;
}

$sql = "SELECT IDServicio,IDServicioMaestro FROM Servicio WHERE IDClub = '" . $_GET["IDClub"] . "'";
$r_sql = $dbo->query($sql);
while ($row = $dbo->fetchArray($r_sql)) {
    $array_servicio[$row["IDServicio"]] = $row["IDServicioMaestro"];
}

$sql = "SELECT Nombre,IDServicioMaestro FROM ServicioMaestro WHERE 1 ";
$r_sql = $dbo->query($sql);
while ($row = $dbo->fetchArray($r_sql)) {
    $array_servicio_m[$row["IDServicioMaestro"]] = $row["Nombre"];
}

$sql = "SELECT TituloServicio,IDServicioMaestro FROM ServicioClub WHERE IDClub = '" . $_GET["IDClub"] . "' ";
$r_sql = $dbo->query($sql);
while ($row = $dbo->fetchArray($r_sql)) {
    $array_servicio_c[$row["IDServicioMaestro"]] = $row["TituloServicio"];
}

$sql = "SELECT IDAuxiliar,Nombre FROM Auxiliar WHERE 1 ";
$r_sql = $dbo->query($sql);
while ($row = $dbo->fetchArray($r_sql)) {
    $array_lista_auxiliar[$row["IDAuxiliar"]] = $row["Nombre"];
}

$sql = "SELECT IDUsuario,Nombre FROM Usuario WHERE IDClub = '" . $_GET["IDClub"] . "' ";
$r_sql = $dbo->query($sql);
while ($row = $dbo->fetchArray($r_sql)) {
    $array_usuario[$row["IDUsuario"]] = $row["Nombre"];
}

if ((int) substr($_GET["FechaInicio"], 0, 4) <= 2017) {
    $tabla_reserva = "ReservaGeneralBck";
} else {
    $tabla_reserva = "ReservaGeneral";
}

if ($_GET["IDServicio"] == "T") : //Se quiere consultar todos los servicios
    $condicion_servicio = "";
else :
    $condicion_servicio = " and IDServicio = '" . $_GET["IDServicio"] . "' ";
endif;



if (!empty($_GET['Cumplida'])) :
    $condicion_servicio .= " and Cumplida = '$_GET[Cumplida]'";
endif;

if (!empty($_GET["NumeroDocumento"])) {
    $otroSelect = ", Socio S";
    $condiciones_tablas = "RG.IDSocio = S.IDSocio and";
    $condiciones_tablas2 = "RGB.IDSocio = S.IDSocio and";
    $condicion_servicio .= " and S.NumeroDocumento ='" . $_GET["NumeroDocumento"] . "'";
}

if (!empty($_GET["Accion"]) && empty($_GET["NumeroDocumento"])) {
    $otroSelect = ", Socio S";
    $condiciones_tablas = "RG.IDSocio = S.IDSocio and";
    $condiciones_tablas2 = "RGB.IDSocio = S.IDSocio and";
    $condicion_servicio .= " and S.Accion ='" . $_GET["Accion"] . "'";
}

//PARA LAGARTOS CONSULTO TODOS LOS SERICIOS DE CLASES
if ($_GET["IDServicio"] == "2321" || $_GET["IDServicio"] == "12014" || $_GET["IDServicio"] == "12015" || $_GET["IDServicio"] == "12016" || $_GET["IDServicio"] == "12017" || $_GET["IDServicio"] == "12018" || $_GET["IDServicio"] == "12019" || $_GET["IDServicio"] == "12020" || $_GET["IDServicio"] == "8046" || $_GET["IDServicio"] == "3466" || $_GET["IDServicio"] == "12534") {
    $condicion_servicio = " and IDServicio in (2321,12014,12015,12016,12017,12018,12019,12020,8046,3466,12534) ";
}

//POR ELEMENTOS
if (!empty($_GET['IDServicioElemento'])) :
    $condicion_servicio .= " and IDServicioElemento = '" . $_GET['IDServicioElemento'] . "'";
endif;

//$sql = "Select * From ".$tabla_reserva." Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and IDServicio = '".$_GET["IDServicio"]."' Order By Fecha,Hora";

//$sql = "Select * From ReservaGeneral Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and IDServicio = '".$_GET["IDServicio"]."'
//    UNION Select * From ReservaGeneralBck Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and IDServicio = '".$_GET["IDServicio"]."' and IDEstadoReserva = 1 Order By Fecha,Hora";

$sql = "Select *, RG.FechaTrCr as FechaCreacion From ReservaGeneral RG " . $otroSelect . " Where   " . $condiciones_tablas . " RG.IDEstadoReserva = 1 and RG.Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and RG.IDClub = '" . $_GET["IDClub"] . "' " . $condicion_servicio . "
				UNION Select *, RGB.FechaTrCr as FechaCreacion From ReservaGeneralBck RGB" . $otroSelect . " Where " . $condiciones_tablas2 . " RGB.IDEstadoReserva = 1 and RGB.Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and RGB.IDClub = '" . $_GET["IDClub"] . "' and RGB.IDEstadoReserva = 1 " . $condicion_servicio . " Order By Fecha,Hora";
/* echo $sql;
exit; */
$nombre = "Reservas" . date("Y_m_d H:i:s");

$qry = $dbo->query($sql);
$Num = $dbo->rows($qry);
//caso especial de orden para NADESBA
if ($_GET["IDClub"] == 106) {
    $html = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>SERVICIO</th>";
    $html .= "<th>USUARIO</th>";
    $html .= "<th>SOCIO</th>";
    $html .= "<th>NUMERO ACCION</th>";
    $html .= "<th>BENEFICIARIO</th>";
    $html .= "<th>INVITADOS</th>";
    $html .= "<th>PROFESOR</th>";
    $html .= "<th>ELEMENTO</th>";
    $html .= "<th>TIPO RESERVA</th>";
    $html .= "<th>FECHA RESERVA</th>";
    $html .= "<th>HORA</th>";
    $html .= "<th>FECHA CREACION RESERVA</th>";
    $html .= "<th>VALOR PAGADO</th>";
    $html .= "<th>OBSERVACIONES</th>";
    $html .= "<th>TIPO PAGO</th>";
    $html .= "<th>CODIGO</th>";
    $html .= "<th>MEDIO PAGO</th>";
    $html .= "<th>PAGO CONFIRMADO</th>";
    $html .= "<th>ESTADO TRANSACCION</th>";
    $html .= "<th>TIPO SOCIO</th>";
    $html .= "<th>EDAD SOCIO</th>";
    $html .= "<th>CORREO</th>";
    $html .= "<th>AÑOS BENEFICIARIO</th>";
    $html .= "<th>TEE</th>";
    $html .= "<th>MODALIDAD</th>";
    $html .= "<th>PAX</th>";
    $html .= "<th>CREADA POR</th>";
    $html .= "<th>COMENTARIO STARTER</th>";
    $html .= "<th>CUMPLIDA SOCIO</th>";
    $html .= "<th>USUARIO QUE AGREGO COMO CUMPLIDA S/N</th>";
    $html .= "<th>FECHA SE MARCO COMO CUMPLIDA S/N </th>";
    $html .= "<th>CANCHA</th>";
    $html .= "<th>EQUIPO</th>";
    $html .= "<th>IP</th>";
    $html .= "<th>CONSECUTIVO</th>";


    //Consulto los campos dinamicos
    $r_campos = &$dbo->all("ServicioCampo", "IDServicio = '" . $_GET["IDServicio"] . "' Order by IDServicioCampo");
    while ($r = $dbo->object($r_campos)) :
        $array_preguntas[] = $r->IDServicioCampo;
        $html .= "<th>" . $r->Nombre . "</th>";
    endwhile;
    $html .= "</tr>";
    $item = 0;

    while ($row = $dbo->fetchArray($qry, $a)) {
        unset($array_respuesta_socio);

        $html .= "<tr>";

        $IDServicioMaestro = $array_servicio[$row["IDServicio"]];
        $NombreServicio = $array_servicio_m[$IDServicioMaestro];
        $NombreServicioPersonalidado = $array_servicio_c[$IDServicioMaestro];

        if (!empty($NombreServicioPersonalidado)) {
            $Servicio = $NombreServicioPersonalidado;
        } else {
            $Servicio = $NombreServicio;
        }

        $datos_socio_array = $array_socios[$row["IDSocio"]];

        $fechaNacimiento = $datos_socio_array[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
        $añosS = $edad_diff->format('%y');

        $datos_socio_b_array = $array_socios[$row["IDSocioBeneficiario"]];

        $fechaNacimiento = $datos_socio_b_array[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
        $añosB = $edad_diff->format('%y');

        $array_auxiliar = explode(",", $row["IDAuxiliar"]);

        unset($array_nom_auxiliar);
        if (count($array_auxiliar) > 0) :
            foreach ($array_auxiliar as $id_auxiliar) :
                //$array_nom_auxiliar[]=$dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $id_auxiliar . "'");
                $array_nom_auxiliar[] = $array_lista_auxiliar[$id_auxiliar];
            endforeach;
        endif;
        if (count($array_nom_auxiliar) > 0) :
            $auxiliares = implode(",", $array_nom_auxiliar);
        endif;

        //Invitados

        $datos_invitado = "";
        $total_invitados = 0;
        $sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row["IDReservaGeneral"] . "'";
        $result_invitado = $dbo->query($sql_invitado);
        while ($row_invitado = $dbo->fetchArray($result_invitado)) :
            $total_invitados++;
            if (!empty($row_invitado["IDSocio"])) {
                $tipo_invitado = "Socio: ";
            } else {
                $tipo_invitado = "Externo: ";
            }

            $nom_invitado = $row_invitado["Nombre"];
            $cumplida = $row_invitado["Cumplida"];

            if (!empty($row_invitado["IDSocio"])) :
                $datos_socio_i_array = $array_socios[$row_invitado["IDSocio"]];
                $nom_invitado = $datos_socio_i_array[Nombre] . " " . $datos_socio_i_array[Apellido] . " " . $datos_socio_i_array[Email];
            endif;
            $datos_invitado .= $tipo_invitado . " " . strtoupper($nom_invitado) . "  Cumplida: " . $cumplida . "<br>";
        endwhile;

        if ((int) $row["CantidadInvitadoSalon"] > 0) {
            $paxreserva = $row["CantidadInvitadoSalon"];
        } else {
            $paxreserva = $total_invitados;
        }

        $html .= "<td>" . $Servicio . "</td>";
        $html .= "<td>" . $datos_socio_array[Email] . "</td>";
        $html .= "<td>" . $datos_socio_array[Nombre] . " " . $datos_socio_array[Apellido] . "</td>";
        $html .= "<td>" . $datos_socio_array[Accion] . "</td>";
        $html .= "<td>" . $datos_socio_b_array[Nombre] . " " . $datos_socio_b_array[Apellido] . "</td>";
        $html .= "<td>" . $datos_invitado . "</td>";
        $html .= "<td>" . $auxiliares . "</td>";
        $html .= "<td>" . strtoupper($dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row["IDServicioElemento"] . "'")) . "</td>";
        $html .= "<td>" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row["IDServicioTipoReserva"] . "'") . "</td>";
        $html .= "<td>" . strtoupper($row["Fecha"]) . "</td>";
        $html .= "<td>" . strtoupper($row["Hora"]) . "</td>";
        $html .= "<td>" . strtoupper($row["FechaCreacion"]) . "</td>";
        $html .= "<td>" . $dbo->getFields("ZonaPagosPagos", "ValorPagado", "CodigoTransaccion = '" . $row["CodigoRespuesta"] . "'") . "</td>";
        $html .= "<td>" . strtoupper($row["Observaciones"]) . "</td>";
        $html .= "<td>" . $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $row["IDTipoPago"] . "'") . "</td>";
        $html .= "<td>" . $dbo->getFields("ZonaPagosPagos", "IDZonaPagosPagos", "CodigoTransaccion = '" . $row["CodigoRespuesta"] . "'") . "</td>";
        $html .= "<td>" . $row["MedioPago"] . "</td>";
        $html .= "<td>" . $row["Pagado"] . "</td>";
        $html .= "<td>" . $row["EstadoTransaccion"] . "</td>";
        $html .= "<td>" . $datos_socio_array[TipoSocio] . "</td>";
        $html .= "<td>" . $añosS . "</td>";
        $html .= "<td>" . $datos_socio_array[CorreoElectronico] . "</td>";
        $html .= "<td>" . $añosB . "</td>";
        $html .= "<td>" . strtoupper($row["Tee"]) . "</td>";
        $html .= "<td>" . $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row["IDTipoModalidadEsqui"] . "'") . "</td>";
        $html .= "<td>" . $paxreserva . "</td>";

        if ($row["UsuarioTrCr"] == "Starter" || $row["UsuarioTrCr"] == "Empleado") :
            //$creada_por =  $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $row["IDUsuarioReserva"] . "'" );
            $creada_por = $array_usuario[$row["IDUsuarioReserva"]];
        else :
            $creada_por = $row["UsuarioTrCr"];
        endif;

        $html .= "<td>" . $creada_por . "</td>";
        $html .= "<td>" . $row["ObservacionCumplida"] . "</td>";
        $html .= "<td>" . $row["CumplidaCabeza"] . "</td>";
        $html .= "<td>" . $array_usuario[$row["IDUsuarioCumplida"]] . "</td>";
        $html .= "<td>" . $row["FechaCumplida"] . "</td>";
        $html .= "<td>" . $row["Cancha"] . "</td>";
        $html .= "<td>" . $row["Equipo"] . "</td>";
        $html .= "<td>" . $row["IP"] . "</td>";
        $html .= "<td>" . $row["IdentificadorServicio"] . "-" . $row["ConsecutivoServicio"] . "</td>";

        $sql_repuesta_socio = "SELECT IDServicioCampo,Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row["IDReservaGeneral"] . "'";
        $r_respuesta_socio = $dbo->query($sql_repuesta_socio);
        while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
            $array_respuesta_socio[$row_respuesta["IDServicioCampo"]] = $row_respuesta["Valor"];
        endwhile;
        if (count($array_preguntas) > 0) :
            foreach ($array_preguntas as $id_pregunta) :
                $html .= "<td>" . $array_respuesta_socio[$id_pregunta] . "</td>";
            endforeach;
        endif;

        $html .= "</tr>";
    }

    $html .= "</table>";
} else {
    $html = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>ID_RESERVA</th>";
    $html .= "<th>SERVICIO</th>";

    if ($_GET["IDClub"] == 70) {
        $html .= "<th>CONSECUTIVO SERVICIO</th>";
    }

    $html .= "<th>NUMERO ACCION</th>";
    $html .= "<th>NUMERO ACCION PADRE</th>";
    $html .= "<th>FECHA DE NACIMIENTO</th>";
    $html .= "<th>TIPO SOCIO</th>";
    $html .= "<th>SOCIO</th>";
    $html .= "<th>CATEGORIA SOCIO</th>";
    $html .= "<th>ELEMENTO ADICIONAL</th>";
    $html .= "<th>EDAD SOCIO</th>";
    $html .= "<th>CORREO</th>";
    $html .= "<th>CEULAR</th>";
    $html .= "<th>USUARIO</th>";
    $html .= "<th>BENEFICIARIO</th>";
    $html .= "<th>AÑOS BENEFICIARIO</th>";
    $html .= "<th>BOLEADOR</th>";
    $html .= "<th>TEE</th>";
    $html .= "<th>MODALIDAD</th>";

    if ($_GET["IDServicio"] == 12789) {
        $html .= "<th>Numero de Horas</th>";
    }

    $html .= "<th>TIPO RESERVA</th>";
    $html .= "<th>PAX</th>";
    $html .= "<th>ELEMENTO</th>";
    $html .= "<th>FECHA CREACION RESERVA</th>";
    $html .= "<th>FECHA RESERVA</th>";
    $html .= "<th>HORA</th>";
    $html .= "<th>HORA SALIDA PRIMEROS 9 HOYOS</th>";
    $html .= "<th>HORA TERMINACIÓN PRIMEROS 9 HOYOS</th>";
    $html .= "<th>HORA SALIDA SEGUNDOS 9 HOYOS</th>";
    $html .= "<th>HORA TERMINACIÓN SEGUNDOS 9 HOYOS</th>";
    $html .= "<th>INVITADOS</th>";
    $html .= "<th>ELEMENTO ADICIONAL INVITADO</th>";
    $html .= "<th>OBSERVACIONES</th>";
    $html .= "<th>CREADA POR</th>";
    $html .= "<th>COMENTARIO STARTER</th>";
    $html .= "<th>CUMPLIDA GENERAL</th>";
    $html .= "<th>CUMPLIDA SOCIO</th>";
    $html .= "<th>USUARIO QUE AGREGO COMO CUMPLIDA S/N</th>";
    $html .= "<th>FECHA SE MARCO COMO CUMPLIDA S/N </th>";
    $html .= "<th>TIPO PAGO</th>";
    $html .= "<th>CODIGO</th>";
    $html .= "<th>ESTADO TRANSACCION</th>";
    $html .= "<th>CODIGO RESPUESTA</th>";
    $html .= "<th>MEDIO PAGO</th>";
    $html .= "<th>PAGO CONFIRMADO</th>";

    $html .= "<th>PORCENTAJE ABONADO</th>";
    $html .= "<th>VALOR PORCENTAJE</th>";

    $html .= "<th>VALOR PAGADO</th>";

    $html .= "<th>CANCHA</th>";
    $html .= "<th>EQUIPO</th>";
    $html .= "<th>IP</th>";
    $html .= "<th>CONSECUTIVO</th>";
    $html .= "<th>CONFIRMADA SOCIO</th>";

    //Consulto los campos dinamicos
    $r_campos = &$dbo->all("ServicioCampo", "IDServicio = '" . $_GET["IDServicio"] . "' Order by IDServicioCampo");
    while ($r = $dbo->object($r_campos)) :
        $array_preguntas[] = $r->IDServicioCampo;
        $html .= "<th>" . $r->Nombre . "</th>";
    endwhile;
    $html .= "</tr>";
    $item = 0;

    while ($row = $dbo->fetchArray($qry, $a)) {

        unset($array_respuesta_socio);

        $html .= "<tr>";

        $IDServicioMaestro = $array_servicio[$row["IDServicio"]];
        $NombreServicio = $array_servicio_m[$IDServicioMaestro];
        $NombreServicioPersonalidado = $array_servicio_c[$IDServicioMaestro];

        if (!empty($NombreServicioPersonalidado)) {
            $Servicio = $NombreServicioPersonalidado;
        } else {
            $Servicio = $NombreServicio;
        }

        $datos_socio_array = $array_socios[$row["IDSocio"]];
        // $datos_socio=explode("|",$datos_socio_array);

        $fechaNacimiento = $datos_socio_array[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
        $añosS = $edad_diff->format('%y');

        $html .= "<td>" . $row[IDReservaGeneral] . "</td>";
        $html .= "<td>" . $Servicio . "</td>";
        if ($_GET["IDClub"] == 70) {
            $html .= "<th>" . $row[ConsecutivoServicio] . "</th>";
        }
        $html .= "<td>" . $datos_socio_array[Accion] . "</td>";
        $html .= "<td>" . $datos_socio_array[AccionPadre] . "</td>";
        $html .= "<td>" . $datos_socio_array[FechaNacimiento] . "</td>";
        $html .= "<td>" . $datos_socio_array[TipoSocio] . "</td>";
        $html .= "<td>" . $datos_socio_array[Nombre] . " " . $datos_socio_array[Apellido] . "</td>";
        $html .= "<td>" . $dbo->getFields("Categoria", "Nombre", "IDCategoria = $datos_socio_array[IDCategoria]") . "</td>";

        //Consulta elementos adicionales
        $query = "SELECT SA.Nombre AS NombreServicio, RGA.IDReservaGeneralAdicional, RGA.Retornado
					FROM ReservaGeneral RG
						INNER JOIN ReservaGeneralAdicional RGA ON RG.IDReservaGeneral=RGA.IDReservaGeneral
						INNER JOIN ServicioAdicional SA ON RGA.IDServicioAdicional=SA.IDServicioAdicional
						WHERE RG.IDReservaGeneral={$row['IDReservaGeneral']}";
        $reservaAdicionalSocio = $dbo->fetch($query);

        $reservaAdicionalSocio = (isset($reservaAdicionalSocio["NombreServicio"])) ? [$reservaAdicionalSocio] : $reservaAdicionalSocio;

        $html .= "<td>";
        foreach ($reservaAdicionalSocio as $reservaItem) {
            $html .= $reservaItem["NombreServicio"] . "<br>" . PHP_EOL;
        }
        $html .= "</td>";

        $html .= "<td>" . $añosS . "</td>";
        $html .= "<td>" . $datos_socio_array[CorreoElectronico] . "</td>";
        $html .= "<td>" . $datos_socio_array[Celular] . "</td>";
        $html .= "<td>" . $datos_socio_array[Email] . "</td>";

        $datos_socio_b_array = $array_socios[$row["IDSocioBeneficiario"]];

        $fechaNacimiento = $datos_socio_b_array[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
        $añosB = $edad_diff->format('%y');

        $html .= "<td>" . $datos_socio_b_array[Nombre] . " " . $datos_socio_b_array[Apellido] . "</td>";
        $html .= "<td>" . $añosB . "</td>";
        $array_auxiliar = explode(",", $row["IDAuxiliar"]);

        unset($array_nom_auxiliar);
        if (count($array_auxiliar) > 0) :
            foreach ($array_auxiliar as $id_auxiliar) :
                //$array_nom_auxiliar[]=$dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $id_auxiliar . "'");
                $array_nom_auxiliar[] = $array_lista_auxiliar[$id_auxiliar];
            endforeach;
        endif;
        if (count($array_nom_auxiliar) > 0) :
            $auxiliares = implode(",", $array_nom_auxiliar);
        endif;

        //Invitados

        $datos_invitado = "";
        $datos_invitado_adicional = "";
        $total_invitados = 0;
        $sql_invitado = "SELECT SA.Nombre AS NombreServicio, RGAI.IDReservaGeneralAdicionalInvitado,
						RGI.IDSocio, RGI.Nombre, RGI.Cedula, RGI.Correo, RGI.Cumplida, RGI.Confirmado
					FROM ReservaGeneralInvitado RGI
					LEFT JOIN ReservaGeneralAdicionalInvitado RGAI ON RGI.IDReservaGeneralInvitado=RGAI.IDReservaGeneralInvitado
					LEFT JOIN ServicioAdicional SA ON RGAI.IDServicioAdicional=SA.IDServicioAdicional
					WHERE RGI.IDReservaGeneral = '" . $row["IDReservaGeneral"] . "'";
        $result_invitado = $dbo->query($sql_invitado);

        while ($row_invitado = $dbo->fetchArray($result_invitado)) :
            //var_dump($row_invitado);

            $total_invitados++;
            if (!empty($row_invitado["IDSocio"])) {
                $tipo_invitado = "Socio: ";
            } else {
                $tipo_invitado = "Externo: ";
            }

            $nom_invitado = $row_invitado["Nombre"] . "($row_invitado[Cedula] / $row_invitado[Correo])";
            $cumplida = $row_invitado["Cumplida"];
            $confirmado = $row_invitado["Confirmado"];

            if (!empty($row_invitado["IDSocio"])) :
                $datos_socio_i_array = $array_socios[$row_invitado["IDSocio"]];
                $nom_invitado = $datos_socio_i_array[Nombre] . $datos_socio_i_array[Apellido] . " " . $datos_socio_i_array[Email];
            endif;

            if ($_GET["IDClub"] == 112) {
                $datos_invitado .= $tipo_invitado . " " . strtoupper($nom_invitado) . "  Cumplida: " . $cumplida . " Confirmada: " . $confirmado . "<br>";
            } else {
                $datos_invitado .= $tipo_invitado . " " . strtoupper($nom_invitado) . "  Cumplida: " . $cumplida . "<br>";
            }
            $datos_invitado_adicional .= ($row_invitado["NombreServicio"] == null || $row_invitado["NombreServicio"] == "") ? "_<br>" : $row_invitado["NombreServicio"] . "<br>";
        endwhile;

        if ((int) $row["CantidadInvitadoSalon"] > 0) {
            $paxreserva = $row["CantidadInvitadoSalon"];
        } else {
            $paxreserva = $total_invitados;
        }

        $html .= "<td>" . $auxiliares . "</td>";
        $html .= "<td>" . strtoupper($row["Tee"]) . "</td>";
        $html .= "<td>" . $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row["IDTipoModalidadEsqui"] . "'") . "</td>";
        if ($_GET["IDServicio"] == 12789) {
            $horas = $dbo->getFields("ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = '" . $row["IDServicioTipoReserva"] . "'");
            $TotalHoras = ((float) $horas * 0.5);
            $TotalHoras = (string)$TotalHoras;
            $html .= "<th>" . str_replace('.', ',', $TotalHoras)  . "</th>";
        }
        $html .= "<td>" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row["IDServicioTipoReserva"] . "'") . "</td>";
        $html .= "<td>" . $paxreserva . "</td>";
        $html .= "<td>" . strtoupper($dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row["IDServicioElemento"] . "'")) . "</td>";
        $html .= "<td>" . strtoupper($row["FechaCreacion"]) . "</td>";
        $html .= "<td>" . strtoupper($row["Fecha"]) . "</td>";
        $html .= "<td>" . strtoupper($row["Hora"]) . "</td>";
        $html .= "<td>" . strtoupper($row["HoraSalidaPrimero9hoyos"]) . "</td>";
        $html .= "<td>" . strtoupper($row["HoraFinPrimero9Hoyos"]) . "</td>";
        $html .= "<td>" . strtoupper($row["HoraSalidaSegundo9Hoyos"]) . "</td>";
        $html .= "<td>" . strtoupper($row["HoraFinSegundo9Hoyos"]) . "</td>";
        $html .= "<td>" . $datos_invitado . "</td>";
        $html .= "<td>" . $datos_invitado_adicional . "</td>";
        $html .= "<td>" . strtoupper($row["Observaciones"]) . "</td>";

        if ($row["UsuarioTrCr"] == "Starter" || $row["UsuarioTrCr"] == "Empleado") :
            //$creada_por =  $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $row["IDUsuarioReserva"] . "'" );
            $creada_por = $array_usuario[$row["IDUsuarioReserva"]];
        else :
            $creada_por = $row["UsuarioTrCr"];
        endif;

        $html .= "<td>" . $creada_por . "</td>";
        $html .= "<td>" . $row["ObservacionCumplida"] . "</td>";
        $html .= "<td>" . $row["Cumplida"] . "</td>";
        $html .= "<td>" . $row["CumplidaCabeza"] . "</td>";
        $html .= "<td>" . $array_usuario[$row["IDUsuarioCumplida"]] . "</td>";
        $html .= "<td>" . $row["FechaCumplida"] . "</td>";

        //Pago
        /*      if ($_GET["IDClub"] == 28) { //Solo para la liga consulto los pagos web
            $html .= "<td>" . $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $row["IDTipoPago"] . "'") . "</td>";
            $html .= "<td>" . $row["CodigoPago"] . "</td>";
            $html .= "<td>" . $dbo->getFields("PAYUEstadoTransaccion", "Descripcion", "Estado_Pol = '" . $row["EstadoTransaccion"] . "'") . "</td>";
            $html .= "<td>" . $dbo->getFields("PAYUMediosPago", "Descripcion", "Medio_Pago = '" . $row["MedioPago"] . "'") . "</td>";
            $html .= "<td>" . $row["Pagado"] . "</td>";
            $html .= "<td>" . " " . "</td>";
            $html .= "<td>" . " " . "</td>";
            $html .= "<td>" . $dbo->getFields("PagosWeb", "value", "extra1 = '" . $row["IDReservaGeneral"] . "'") . "</td>";
        } else { */
        $html .= "<td>" . $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $row["IDTipoPago"] . "'") . "</td>";
        $html .= "<td>" . $row["CodigoPago"] . "</td>";
        $html .= "<td>" . $row["EstadoTransaccion"] . "</td>";
        $html .= "<td>" . $row["CodigoRespuesta"] . "</td>";
        $html .= "<td>" . $row["MedioPago"] . "</td>";
        $html .= "<td>" . $row["Pagado"] . "</td>";

        $datosabono = $dbo->fetchAll('PorcentajeAbono', 'IDPorcentajeAbono = ' . $row[IDPorcentajeAbono]);
        $datosAbono1 = $datosabono[Porcentaje];
        $datosAbono2 = (($datosabono[Porcentaje] * $row[ValorPagado]) / 100);

        if (!empty($datosAbono1)) {
            $datosAbono1 = $datosAbono1;
        } else {
            $datosAbono1 = "";
        }

        if (!empty($datosAbono2)) {
            $datosAbono2 = $datosAbono2;
        } else {
            $datosAbono2 = "";
        }

        /*       $html .= "<td>" . $datosabono[Porcentaje] . "%</td>";
            $html .= "<td>" . (($datosabono[Porcentaje] * $row[ValorPagado]) / 100) . "</td>"; */
        $html .= "<td>" . $datosAbono1 . "%</td>";
        $html .= "<td>" . $datosAbono2 . "</td>";

        $html .= "<td>" . $row["ValorPagado"] . "</td>";
        // }

        //Fin Pago
        $html .= "<td>" . $row["Cancha"] . "</td>";
        $html .= "<td>" . $row["Equipo"] . "</td>";
        $html .= "<td>" . $row["IP"] . "</td>";
        $html .= "<td>" . $row["IdentificadorServicio"] . "-" . $row["ConsecutivoServicio"] . "</td>";
        $html .= "<td>" . $row["ConfirmadaSocio"] . "</td>";

        $sql_repuesta_socio = "SELECT IDServicioCampo,Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row["IDReservaGeneral"] . "'";
        $r_respuesta_socio = $dbo->query($sql_repuesta_socio);
        while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
            $array_respuesta_socio[$row_respuesta["IDServicioCampo"]] = $row_respuesta["Valor"];
        endwhile;
        if (count($array_preguntas) > 0) :
            foreach ($array_preguntas as $id_pregunta) :
                $html .= "<td>" . $array_respuesta_socio[$id_pregunta] . "</td>";
            endforeach;
        endif;

        $html .= "</tr>";
    }

    $html .= "</table>";
}

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
    exit();
    ?>
</body>

</html>

exit();

?>