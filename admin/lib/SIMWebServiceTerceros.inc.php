<?php

class SIMWebServiceTerceros
{
    public function Encuestas($IDClub, $FechaEncuesta, $FechaIncioRespuestas, $FechaFinRespuestas)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($FechaEncuesta)) :
            $CondicionFechaEncuesta = " AND FechaInicio < '$FechaEncuesta' AND FechaFin > '$FechaEncuesta' ";
        endif;

        if (!empty($FechaRespuestas)) :
            $CondicionFechaRespuesta = " AND FechaTrCr < '$FechaFinRespuestas' AND FechaTrCr > '$FechaIncioRespuestas' ";
        endif;

        // LISTAMOS LAS ENCUESTAS EXISTENTES DEL CLUB
        $SQLEncuestas = "SELECT IDEncuesta, Nombre, Descripcion, FechaInicio, FechaFin FROM Encuesta WHERE IDClub = $IDClub  $CondicionFechaEncuesta AND Publicar = 'S'";
        $QRYEncuestas = $dbo->query($SQLEncuestas);

        while ($DatosEncuesta = $dbo->fetchArray($QRYEncuestas)) :

            $RespuestasSocios = array();

            $array_socios_responden = [];
            $array_preguntas_encuesta = [];
            $array_socios_respuestas = [];

            $InfoResponse[NombreEncuesta] = $DatosEncuesta[Nombre];
            $InfoResponse[DescripcionEncuesta] = $DatosEncuesta[Descripcion];
            $InfoResponse[FechaInicioPublicacion] = $DatosEncuesta[FechaInicio];
            $InfoResponse[FechaFinPublicacion] = $DatosEncuesta[FechaFin];

            // BUSCAMOS LOS SOCIOS QUE HAN RESPODIO LA ENCUESTA

            $SQLRespuestasSocios = "SELECT IDSocio FROM `EncuestaRespuesta` WHERE `IDEncuesta` = $DatosEncuesta[IDEncuesta] GROUP BY IDSocio";
            $QRYRespuestasSocios = $dbo->query($SQLRespuestasSocios);
            while ($DatosSocioRespuestas = $dbo->fetchArray($QRYRespuestasSocios)) :
                $array_socios_responden[$DatosSocioRespuestas[IDSocio]] = $DatosSocioRespuestas[IDSocio];
            endwhile;

            $SQLPreguntas = "SELECT IDPregunta, EtiquetaCampo, TipoCampo FROM Pregunta WHERE IDEncuesta = $DatosEncuesta[IDEncuesta]";
            $QRYPreguntas = $dbo->query($SQLPreguntas);

            while ($DatosPreguntas = $dbo->fetchArray($QRYPreguntas)) :
                $array_preguntas_encuesta[$DatosPreguntas[IDPregunta]] = $DatosPreguntas;
            endwhile;


            foreach ($array_socios_responden as $IDSocio) :

                $SQLRespuestas = "SELECT IDPregunta, Valor, FechaTrCr FROM EncuestaRespuesta WHERE IDSocio = $IDSocio AND IDEncuesta = $DatosEncuesta[IDEncuesta] $CondicionFechaRespuesta ORDER BY IDEncuestaRespuesta DESC";
                $QRYRespuestas = $dbo->query($SQLRespuestas);

                while ($DatosRespuestas = $dbo->fetchArray($QRYRespuestas)) :
                    $array_socios_respuestas[$IDSocio][$DatosRespuestas[FechaTrCr]][$DatosRespuestas[IDPregunta]] = $DatosRespuestas;
                endwhile;

            endforeach;

            foreach ($array_socios_responden as $IDSocio) :

                $SQLSocio = "SELECT Nombre, Apellido, Accion, NumeroDocumento FROM Socio WHERE IDSocio = $IDSocio";
                $QRYSocio = $dbo->query($SQLSocio);
                $Socio = $dbo->fetchArray($QRYSocio);

                $InfoRespuestaSocio[Socio] = trim($Socio[Nombre]) . " " . trim($Socio[Apellido]);
                $InfoRespuestaSocio[DocumentoSocio] = $Socio[NumeroDocumento];
                $InfoRespuestaSocio[AccionSocio] = $Socio[Accion];

                $Repuestas = array();

                foreach ($array_socios_respuestas[$IDSocio] as $FechaRespuesta => $RespuestasPreguntas) :
                    $InfoRespuestas = [];
                    $InfoRespuestas[FechaRespuesta] = $FechaRespuesta;

                    foreach ($array_preguntas_encuesta as $IDPregunta => $InfoPregunta) :

                        if ($InfoPregunta[TipoCampo] == "imagen") :
                            $RepuestaFinal = PQR_ROOT . $RespuestasPreguntas[$IDPregunta][Valor];
                        else :
                            $RepuestaFinal = $RespuestasPreguntas[$IDPregunta][Valor];
                        endif;

                        $InfoRespuestas[$InfoPregunta[EtiquetaCampo]] = $RepuestaFinal;
                    endforeach;

                    array_push($Repuestas, $InfoRespuestas);
                endforeach;

                $InfoRespuestaSocio[RespuestasSocio] = $Repuestas;

                array_push($RespuestasSocios, $InfoRespuestaSocio);

            endforeach;

            $InfoResponse[Socios] = $RespuestasSocios;

            array_push($response, $InfoResponse);

        endwhile;

        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;

        return $respuesta;
    }

    public function InfoVacunacionSocio($IDClub, $Documento, $Accion)
    {
        require LIBDIR . "SIMWebServiceVacunacion.inc.php";
        $dbo = SIMDB::get();

        $SQLDatosSocio = "SELECT * FROM Socio WHERE IDClub = $IDClub AND (NumeroDocumento = '$Documento' OR Accion = '$Accion')";
        $QRYDatosSocio = $dbo->query($SQLDatosSocio);

        if ($dbo->rows($QRYDatosSocio) > 0) :

            $DatosSocio = $dbo->fetchArray($QRYDatosSocio);
            $IDSocio = $DatosSocio[IDSocio];

            $DatosVacunacion = SIMWebServiceVacunacion::datos_persona_vacunacion($IDClub, $IDSocio, "", "");

            if (!empty($DatosVacunacion)) :

                $DatosResponse[IDSocio] = $DatosVacunacion[IDSocio];
                $DatosResponse[SocioVacunado] = strtoupper($DatosVacunacion[EstoyVacunado]);
                $DatosResponse[NombreSocio] = $DatosVacunacion[DatosInfo][Nombre];
                $DatosResponse[DocumentoSocio] = $DatosVacunacion[DatosInfo][Documento];
                $DatosResponse[QRVacunacionSocio] = $DatosVacunacion[DatosInfo][QRCode];

                $ProximaDosis = $dbo->getFields("Dosis", "NombreDosis", "IDDosis = $DatosVacunacion[IDDosisProximaAplicar]");

                $DatosResponse[ProximaDosis] = $ProximaDosis;
                $DatosResponse[DosisAplicadas] = $DatosVacunacion[Dosis];

                $respuesta[message] = "Datos del socio encontrados";
                $respuesta[success] = true;
                $respuesta[response] = $DatosResponse;

            else :

                $respuesta[message] = "El socio no tiene información de vacuanción registrada";
                $respuesta[success] = false;
                $respuesta[response] = "";

            endif;

        else :

            $respuesta[message] = "Socio no encontrado";
            $respuesta[success] = false;
            $respuesta[response] = "";

        endif;

        return $respuesta;
    }

    public function Invitados($IDClub, $Fecha, $Accion)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (empty($Fecha)) :
            $Fecha = date("Y-m-d");
        endif;

        if (!empty($Accion)) :
            $CondicionesSocio = " AND Accion = '$Accion'";
        endif;

        $SQLInvitados = "SELECT Nombre, Apellido, NumeroDocumento, IDInvitado FROM Invitado WHERE IDClub = $IDClub";
        $QRYInvitados = $dbo->query($SQLInvitados);

        $SQLDatosInvitados = "SELECT * FROM SocioInvitado WHERE IDClub = $IDClub AND FechaIngreso <= '$Fecha' AND FechaIngreso >= '$Fecha'";
        $QRYDatosInvitados = $dbo->query($SQLDatosInvitados);

        $SQLDatosInvitaciones = "SELECT FechaInicio,FechaFin,FechaIngreso,FechaSalida,IDSocio,IDInvitado FROM SocioInvitadoEspecial WHERE IDClub = $IDClub AND FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha'";
        $QRYDatosInvitaciones = $dbo->query($SQLDatosInvitaciones);

        $SQLDatosContratistas = "SELECT IDSocio,FechaInicio,FechaFin,FechaIngreso,FechaSalida, IDInvitado FROM SocioAutorizacion WHERE IDClub = $IDClub AND FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha'";
        $QRYDatosContratistas = $dbo->query($SQLDatosContratistas);

        $SQLSocios = "SELECT IDSocio, Nombre, Apellido, Accion, NumeroDocumento FROM Socio WHERE IDClub = $IDClub $CondicionesSocio";
        $QRYSocios = $dbo->query($SQLSocios);

        if ($dbo->rows($QRYInvitados) > 0) :

            while ($DatosInvitado = $dbo->fetchArray($QRYInvitados)) :
                $arrayInvitados[$DatosInvitado[IDInvitado]] = $DatosInvitado;
            endwhile;

            while ($DatosInvitados = $dbo->fetchArray($QRYDatosInvitados)) :
                $arraySocioInvitado[$DatosInvitados[IDInvitado]][] = $DatosInvitados;
            endwhile;

            while ($DatosInvitaciones = $dbo->fetchArray($QRYDatosInvitaciones)) :
                $arraySocioInvitadoEspecial[$DatosInvitaciones[IDInvitado]][] = $DatosInvitaciones;
            endwhile;

            while ($DatosContratistas = $dbo->fetchArray($QRYDatosContratistas)) :
                $arrayContratistas[$DatosContratistas[IDInvitado]][] = $DatosContratistas;
            endwhile;

            while ($DatosSocio = $dbo->fetchArray($QRYSocios)) :
                $arraySocios[$DatosSocio[IDSocio]] = $DatosSocio;
            endwhile;

            foreach ($arrayInvitados as $IDInvitado => $DatosInvitado) :

                $Invitaciones = array();

                $InfoInvitado[Nombre] = $DatosInvitado[Nombre];
                $InfoInvitado[Apellido] = $DatosInvitado[Apellido];
                $InfoInvitado[NumeroDocumento] = $DatosInvitado[NumeroDocumento];

                if (isset($arraySocioInvitadoEspecial[$IDInvitado])) :

                    foreach ($arraySocioInvitadoEspecial[$IDInvitado] as $id => $DatosInvitaciones) :

                        $InfoInvitacion[NombreSocio] = $arraySocios[$DatosInvitaciones[IDSocio]][Nombre] . " " . $arraySocios[$DatosInvitaciones[IDSocio]][Apellido];
                        $InfoInvitacion[AccionSocio] =  $arraySocios[$DatosInvitaciones[IDSocio]][Accion];
                        $InfoInvitacion[NumeroDocumentoSocio] =  $arraySocios[$DatosInvitaciones[IDSocio]][NumeroDocumento];
                        $InfoInvitacion[FechaInicio] = $DatosInvitaciones[FechaInicio];
                        $InfoInvitacion[FechaFin] = $DatosInvitaciones[FechaFin];
                        $InfoInvitacion[FechaIngreso] = $DatosInvitaciones[FechaIngreso];
                        $InfoInvitacion[FechaSalida] = $DatosInvitaciones[FechaSalida];

                        array_push($Invitaciones, $InfoInvitacion);

                    endforeach;

                    $InfoInvitado[Invitaciones] = $Invitaciones;

                    if (isset($arraySocios[$DatosInvitaciones[IDSocio]])) :
                        array_push($response, $InfoInvitado);
                    endif;

                elseif (isset($arrayContratistas[$IDInvitado])) :

                    foreach ($arrayContratistas[$IDInvitado] as $id => $DatosContratista) :

                        $InfoInvitacion[NombreSocio] = $arraySocios[$DatosContratista[IDSocio]][Nombre] . " " . $arraySocios[$DatosContratista[IDSocio]][Apellido];
                        $InfoInvitacion[AccionSocio] =  $arraySocios[$DatosContratista[IDSocio]][Accion];
                        $InfoInvitacion[NumeroDocumentoSocio] =  $arraySocios[$DatosContratista[IDSocio]][NumeroDocumento];
                        $InfoInvitacion[FechaInicio] = $DatosContratista[FechaInicio];
                        $InfoInvitacion[FechaFin] = $DatosContratista[FechaFin];
                        $InfoInvitacion[FechaIngreso] = $DatosContratista[FechaIngreso];
                        $InfoInvitacion[FechaSalida] = $DatosContratista[FechaSalida];

                        array_push($Invitaciones, $InfoInvitacion);
                    endforeach;

                    $InfoInvitado[Invitaciones] = $Invitaciones;

                    if (isset($arraySocios[$DatosContratista[IDSocio]])) :
                        array_push($response, $InfoInvitado);
                    endif;

                elseif (isset($arraySocioInvitado[$IDInvitado])) :

                    foreach ($arraySocioInvitado[$IDInvitado] as $id => $DatosInvitado) :

                        $InfoInvitacion[NombreSocio] = $arraySocios[$DatosInvitado[IDSocio]][Nombre] . " " . $arraySocios[$DatosInvitado[IDSocio]][Apellido];
                        $InfoInvitacion[AccionSocio] =  $arraySocios[$DatosInvitado[IDSocio]][Accion];
                        $InfoInvitacion[NumeroDocumentoSocio] =  $arraySocios[$DatosInvitado[IDSocio]][NumeroDocumento];
                        $InfoInvitacion[FechaIngreso] = $DatosInvitado[FechaIngreso];
                        $InfoInvitacion[Estado] = $DatosInvitado[Estado];
                        $InfoInvitacion[FechaIngresoClub] = $DatosInvitado[FechaIngresoClub];

                        if (isset($arraySocios[$DatosInvitado[IDSocio]])) :
                            array_push($Invitaciones, $InfoInvitacion);
                        endif;

                    endforeach;

                    $InfoInvitado[Invitaciones] = $Invitaciones;

                    if (count($Invitaciones) > 0) :
                        array_push($response, $InfoInvitado);
                    endif;

                endif;

            endforeach;

            if (count($response) > 0) :
                $respuesta[message] = "DATOS INVITADOS";
                $respuesta[success] = true;
                $respuesta[response] = $response;
            else :
                $respuesta[message] = "NO HAY INIVTACIONES PARA LA FECHA";
                $respuesta[success] = false;
                $respuesta[response] = "";
            endif;


        else :

            $respuesta[message] = "EL CLUB NO TIENE INVITADOS";
            $respuesta[success] = false;
            $respuesta[response] = "";

        endif;

        return $respuesta;
    }

    public function CrearActulizarSocio($IDClub, $Accion, $AccionPadre, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $EstadoSocio, $PermiteReservar, $UsuarioApp, $ClaveApp)
    {
        $dbo = SIMDB::get();

        if (!empty($IDClub) && !empty($Nombre) && !empty($Apellido) && !empty($NumeroDocumento)) :

            $NumeroDocumento = trim($NumeroDocumento);
            $Nombre = str_replace("'", "", $Nombre);
            $Apellido = str_replace("'", "", $Apellido);
            // BUSCAMOS SI EL SOCIO YA ESXISTE
            $IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '$NumeroDocumento' AND IDClub = $IDClub");
            if (!empty($IDSocio)) :
                // SI EL SOCIO EXISTE ACTULIZAMOS PERO NO LA CLAVE NI LA CONTRASEÑA

                $UPDATE = "UPDATE Socio SET Accion = '$Accion', AccionPadre = '$AccionPadre', Genero = '$Genero', Nombre = '$Nombre', Apellido = '$Apellido, FechaNacimiento = '$FechaNacimiento', NumeroDocumento = '$NumeroDocumento', CorreoElectronico = '$CorreoElectronico', Telefono = '$Telefono', Celular = '$Celular', Direccion = '$Direccion', IDEstadoSocio = '$EstadoSocio', PermiteReservar = '$PermiteReservar', UsuarioTrEd = 'SERVICIO-CrearActulizarSocio', FechaTrEd = NOW() WHERE IDSocio = $IDSocio";
                $dbo->query($UPDATE);

                $Mensaje = "Usuario actulizado correctamente";
            else :

                if (empty($UsuarioApp)) :
                    $UsuarioApp = $NumeroDocumento;
                endif;

                if (empty($ClaveApp)) {
                    $ClaveApp = sha1(trim($NumeroDocumento));
                } else {
                    $ClaveApp = sha1($ClaveApp);
                }


                $INSERT = "INSERT INTO Socio (IDClub,Accion, AccionPadre, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, CorreoElectronico, Telefono, Celular, Direccion, IDEstadoSocio, PermiteReservar, Email, Clave, UsuarioTrCr, FechaTrCr) VALUES ('$IDClub','$Accion','$AccionPadre','$Genero','$Nombre','$Apellido','$FechaNacimiento','$NumeroDocumento','$CorreoElectronico','$Telefono','$Celular','$Direccion','$EstadoSocio','$PermiteReservar','$UsuarioApp','$ClaveApp','SERVICIO-CrearActulizarSocio',NOW())";
                $dbo->query($INSERT);

                $Mensaje = "Usuario Insertado con exito";
            endif;
            $respuesta[message] = $Mensaje;
            $respuesta[success] = false;
            $respuesta[response] = "";
        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function ConsultarSocio($IDClub, $NumeroDocumento)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($NumeroDocumento) && !empty($IDClub)) :
            $SQLSocio = "SELECT * FROM Socio WHERE IDClub = $IDClub AND NumeroDocumento = '$NumeroDocumento'";
            $QRYSocio = $dbo->query($SQLSocio);
            if ($dbo->rows($QRYSocio)) :
                while ($Datos = $dbo->fetchArray($QRYSocio)) :
                    $InfoResponse[IDSocio] = $Datos[IDSocio];
                    $InfoResponse[Nombre] = $Datos[Nombre];
                    $InfoResponse[Apellido] = $Datos[Apellido];
                    $InfoResponse[FechaNacimiento] = $Datos[FechaNacimiento];
                    $InfoResponse[NumeroDocumento] = $Datos[NumeroDocumento];
                    $InfoResponse[Estado] = $Datos[IDEstadoSocio];
                    $InfoResponse[PermiteReservar] = $Datos[PermiteReservar];
                    $InfoResponse[UsuarioApp] = $Datos[Email];
                    $InfoResponse[Telefono] = $Datos[Telefono];
                    $InfoResponse[Celular] =  $Datos[Celular];
                    $InfoResponse[Foto] = SOCIO_ROOT . $Datos[Foto];
                    $InfoResponse[CorreoElectronico] = $Datos[CorreoElectronico];

                    array_push($response, $InfoResponse);
                endwhile;
                $respuesta[message] = "ENCONTRADOS";
                $respuesta[success] = true;
                $respuesta[response] = $response;
            else :
                $respuesta[message] = "NO EXISTEN COINCIDENCIAS";
                $respuesta[success] = false;
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function ConsultarBaseSocio($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :
            $SQLSocio = "SELECT * FROM Socio WHERE IDClub = $IDClub";
            $QRYSocio = $dbo->query($SQLSocio);

            if ($dbo->rows($QRYSocio)) :
                while ($Datos = $dbo->fetchArray($QRYSocio)) :
                    $InfoResponse[IDSocio] = $Datos[IDSocio];
                    $InfoResponse[Nombre] = $Datos[Nombre];
                    $InfoResponse[Apellido] = $Datos[Apellido];
                    $InfoResponse[FechaNacimiento] = $Datos[FechaNacimiento];
                    $InfoResponse[NumeroDocumento] = $Datos[NumeroDocumento];
                    $InfoResponse[Estado] = $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = $Datos[IDEstadoSocio]");
                    $InfoResponse[PermiteReservar] = $Datos[PermiteReservar];
                    $InfoResponse[UsuarioApp] = $Datos[Email];
                    $InfoResponse[Telefono] = $Datos[Telefono];
                    $InfoResponse[Celular] =  $Datos[Celular];
                    $InfoResponse[Foto] = SOCIO_ROOT . $Datos[Foto];
                    $InfoResponse[CorreoElectronico] = $Datos[CorreoElectronico];

                    array_push($response, $InfoResponse);
                endwhile;
                $respuesta[message] = "ENCONTRADOS";
                $respuesta[success] = true;
                $respuesta[response] = $response;
            else :
                $respuesta[message] = "NO EXISTEN COINCIDENCIAS";
                $respuesta[success] = false;
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function placa($IDClub, $Placa)
    {
        $dbo = SIMDB::get();
        $Hoy = date("Y-m-d");

        if (!empty($IDClub) && !empty($Placa)) :
            $SQLBusca = "SELECT * FROM Vehiculo WHERE Placa = '$Placa' ORDER BY IDSocio DESC";
            $QRYBusca = $dbo->query($SQLBusca);
            if ($dbo->rows($QRYBusca) > 0) :
                $DatosVehiculo = $dbo->fetchArray($QRYBusca);
                if ($DatosVehiculo[IDSocio] > 0) :
                    $SQLSocio = "SELECT * FROM Socio WHERE IDSocio = $DatosVehiculo[IDSocio]";
                    $QRYSocio = $dbo->query($SQLSocio);
                    if ($dbo->rows($QRYSocio) > 0) :
                        $datos_socio = $dbo->fetchArray($QRYSocio);
                        if ($datos_socio[IDClub] == $IDClub) :
                            if ($datos_socio[IDEstadoSocio] != 2) :
                                $respuesta[message] = "PLACA AUTORIZADA";
                                $respuesta[success] = true;
                                $respuesta[response] = "";
                            else :
                                $respuesta[message] = "EL SOCIO NO ESTA ACTIVO";
                                $respuesta[success] = false;
                                $respuesta[response] = "";
                            endif;
                        else :
                            $respuesta[message] = "EL SOCIO NO ESTA EN LA BASE DEL CLUB";
                            $respuesta[success] = false;
                            $respuesta[response] = "";
                        endif;
                    else :
                        $respuesta[message] = "EL SOCIO DUEÑO DE LA PLACA NO EXISTE";
                        $respuesta[success] = false;
                        $respuesta[response] = "";
                    endif;
                else :
                    $IDInvitado = $DatosVehiculo[IDInvitado];
                    $SQLSocioInvitado = "SELECT * FROM SocioInvitado WHERE IDInvitado = $IDInvitado AND IDClub = $IDClub";
                    $QRYSocioInvitado = $dbo->query($SQLSocioInvitado);
                    if ($dbo->rows($QRYSocioInvitado) > 0) :
                        $DatosSocioInvitado = $dbo->fetchArray($QRYSocioInvitado);
                        if ($DatosSocioInvitado[FechaIngreso] == $Hoy) :
                            $respuesta[message] = "PLACA AUTORIZADA";
                            $respuesta[success] = true;
                            $respuesta[response] = "";
                        else :
                            $respuesta[message] = "LA PLACA NO ESTA AUTORIZADA PARA EL DIA ACTUAL";
                            $respuesta[success] = false;
                            $respuesta[response] = "";
                        endif;
                    else :
                        $SQLSocioInvitadoEspecial = "SELECT * FROM SocioInvitadoEspecial WHERE IDInvitado = $IDInvitado AND IDClub = $IDClub";
                        $QRYSocioInvitadoEspecial = $dbo->query($SQLSocioInvitadoEspecial);
                        if ($dbo->rows($QRYSocioInvitadoEspecial) > 0) :
                            $DatosSocioInvitadoEspecial = $dbo->fetchArray($QRYSocioInvitadoEspecial);
                            if ($DatosSocioInvitadoEspecial[FechaInicio] >= $Hoy && $DatosSocioInvitadoEspecial[FechaFin] <= $Hoy) :
                                $respuesta[message] = "PLACA AUTORIZADA";
                                $respuesta[success] = true;
                                $respuesta[response] = "";
                            else :
                                $respuesta[message] = "LA PLACA NO ESTA AUTORIZADA PARA EL DIA ACTUAL";
                                $respuesta[success] = false;
                                $respuesta[response] = "";
                            endif;
                        else :
                            $SQLSocioAutorizacion = "SELECT * FROM SocioAutorizacion WHERE IDInvitado = $IDInvitado AND IDClub = $IDClub";
                            $QRYSocioAutorizacion = $dbo->query($SQLSocioAutorizacion);
                            if ($dbo->rows($QRYSocioAutorizacion) > 0) :
                                $DatosSocioSocioAutorizacion = $dbo->fetchArray($QRYSocioSocioAutorizacion);
                                if ($DatosSocioSocioAutorizacion[FechaInicio] >= $Hoy && $DatosSocioSocioAutorizacion[FechaFin] <= $Hoy) :
                                    $respuesta[message] = "PLACA AUTORIZADA";
                                    $respuesta[success] = true;
                                    $respuesta[response] = "";
                                else :
                                    $respuesta[message] = "LA PLACA NO ESTA AUTORIZADA PARA EL DIA ACTUAL";
                                    $respuesta[success] = false;
                                    $respuesta[response] = "";
                                endif;
                            else :
                                $respuesta[message] = "LA PLACA NO SE ENCUENTRA REGISTRADA EN EL CLUB";
                                $respuesta[success] = false;
                                $respuesta[response] = "";
                            endif;
                        endif;
                    endif;
                endif;
            else :
                $respuesta[message] = "LA PLACA NO SE ENCUENTRA REGISTRADA";
                $respuesta[success] = false;
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function placas_club($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            // BUSCAMOS LOS SOCIOS DEL CLUB PARA FILRAR LAS PLACAS
            $SQLSocios = "SELECT IDSocio FROM Socio WHERE IDClub = $IDClub";
            $QRYSocios = $dbo->query($SQLSocios);

            while ($Socios = $dbo->fetchArray($QRYSocios)) :
                $ArraySocio[] = $Socios[IDSocio];
            endwhile;

            $SociosPosibles = implode(",", $ArraySocio);

            $SQLPlacas = "SELECT Placa FROM Vehiculo WHERE IDSocio IN ($SociosPosibles)";
            $QRYPlacas = $dbo->query($SQLPlacas);

            while ($Datos = $dbo->fetchArray($QRYPlacas)) :
                $InfoResponse[Placa] = $Datos[Placa];

                array_push($response, $InfoResponse);
            endwhile;

            $respuesta[message] = "PLACAS";
            $respuesta[success] = true;
            $respuesta[response] = $response;

        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function invitados_club($IDClub, $FechaInicio, $FechaFin, $Cedula = "")
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            //Compartir Qr por whatsapp
            $config_club = $dbo->fetchAll("ConfiguracionClub ", " IDClub = '" . $IDClub . "' ", "array");

            if (!empty($FechaInicio) && !empty($FechaFin)) {
                $condicion = " AND (FechaIngreso>='$FechaInicio' AND FechaIngreso<='$FechaFin')";
            }

            if (!empty($Cedula)) {
                $IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = $Cedula");
                $condicionSocio = " AND IDSocio='$IDSocio'";
            }

            $SQLSocio = "SELECT * FROM SocioInvitado WHERE IDClub = $IDClub  $condicion  $condicionSocio";

            $QRYSocio = $dbo->query($SQLSocio);
            if ($dbo->rows($QRYSocio)) :
                while ($Datos = $dbo->fetchArray($QRYSocio)) :

                    $datos = explode(" ", $Datos[Nombre]);
                    if (count($datos) == 1) {
                        $NombreInvitado = $datos[0];
                        $ApellidoInvitado = "";
                    } elseif (count($datos) == 2) {
                        $NombreInvitado = $datos[0];
                        $ApellidoInvitado = $datos[1];
                    } elseif (count($datos) == 3) {
                        $NombreInvitado = $datos[0] . " " . $datos[1];
                        $ApellidoInvitado = $datos[2];
                    }

                    $InfoResponse[NombreSocio] = $dbo->getFields("Socio", "Nombre", "IDSocio = $Datos[IDSocio]");
                    $InfoResponse[ApellidoSocio] = $dbo->getFields("Socio", "Apellido", "IDSocio = $Datos[IDSocio]");
                    $InfoResponse[NumeroDocumentoSocio] = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = $Datos[IDSocio]");
                    $InfoResponse[NombreInvitado] =  $NombreInvitado;
                    $InfoResponse[ApellidoInvitado] =  $ApellidoInvitado;
                    $InfoResponse[NumeroDocumentoInvitado] = $Datos[NumeroDocumento];
                    $InfoResponse[FechaIngreso] =  $Datos[FechaIngreso];
                    $InfoResponse[FechaIngresoClub] =  $Datos[FechaIngresoClub];
                    $InfoResponse[CodigoQr] =  $Datos[NumeroDocumento];


                    //$Urlinvitacion = URLROOT . "admin/lib/pdf_invitacion.php?club=$IDClub&seccion=socioinvitado&id=" . $Datos["IDSocioInvitado"];
                    // $InfoResponse[URL] = $Urlinvitacion;
                    $InfoResponse["UrlCodigoQr"] = SIMUtil::generar_qr($Datos["IDSocioInvitado"], $Datos["NumeroDocumento"], "MostrarSoloImagen");


                    array_push($response, $InfoResponse);
                endwhile;
                $respuesta[message] = "ENCONTRADOS";
                $respuesta[success] = true;
                $respuesta[response] = $response;
            else :
                $respuesta[message] = "NO EXISTEN INVITADOS EN EL CLUB";
                $respuesta[success] = false;
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[message] = "FALTAN PARAMETROS";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }
}
