<?php
class SIMWebServiceCanjes
{
    public function set_eliminar_canje($IDClub, $IDSocio, $IDUsuario, $IDCanjeSolicitud)
    {
        $dbo = SIMDB::get();

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDClub) && !empty($IDCanjeSolicitud)) :




            $Eliminar = true;
            // PARA EL CAMPESTRE PEREIRA SE DEBE VALIDAR QUE SEA EN ESTADO PENDIENTE
            $datos_canje = $dbo->fetchAll("CanjeSolicitud", "IDCanjeSolicitud = " . $IDCanjeSolicitud);
            if ($IDClub == 15 && ($datos_canje[IDEstadoCanjeSolicitud] == 2 || $datos_canje[IDEstadoCanjeSolicitud] == 38)) :
                $Eliminar = false;
                $Razon = "Razon: El canje ya fue aprobado y no es posible eliminarlo";
            endif;

            // validamos el tiempo permitido para eliminar canje
            $TiempoEliminarCanje = $dbo->getFields("ClubCanje", "TiempoEliminarCanje", "IDClub = $IDClub AND Activo = 'S'");
            $MedicionTiempoEliminarCanje = $dbo->getFields("ClubCanje", "MedicionTiempoEliminarCanje", "IDClub = $IDClub AND Activo = 'S'");
            $TiempoEliminarCanje = (int) $TiempoEliminarCanje;
            if ($TiempoEliminarCanje > 0 && !empty($MedicionTiempoEliminarCanje)) {
                switch ($MedicionTiempoEliminarCanje):
                    case "Dias":
                        $Tiempo = "days";
                        break;

                endswitch;

                $Hoy = date("Y-m-d H:i:s");

                //$tiempodespues = strtotime('+' . $TiempoEliminarCanje . $Tiempo, strtotime($Hoy));
                date("Y-m-d", strtotime($FechaInicioCanje . '+' . $Dias . ' day'));

                $tiempodespues = date("Y-m-d H:i:s", strtotime($Hoy . '+' . $TiempoEliminarCanje . $Tiempo));

                if ($tiempodespues > $datos_canje[FechaInicio]) {
                    $Eliminar = false;
                    $motivo = "Razon: El canje no se puede eliminar antes de:" . $TiempoEliminarCanje . " " .  $MedicionTiempoEliminarCanje;
                }
            }

            if ($Eliminar) :

                $SQLDelete = "DELETE FROM CanjeSolicitud WHERE IDCanjeSolicitud = $IDCanjeSolicitud";
                $dbo->query($SQLDelete);

                $respuesta["message"] = "Solicitud de canje eliminada";
                $respuesta["success"] = true;
                $respuesta["response"] = "";

            else :

                $respuesta["message"] = "El Canje no pudo ser eliminado\n" . $Razon . $motivo;
                $respuesta["success"] = false;
                $respuesta["response"] = "";

            endif;

        else :
            $respuesta["message"] = "Faltan parametros para eliminar el canje";
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        endif;

        return $respuesta;
    }

    public function get_solicitud_canje($IDClub, $IDSocio, $IDCanjeSolicitud)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDSocio;

        if (!empty($IDCanjeSolicitud)) {
            $condicion = " AND IDCanjeSolicitud = '" . $IDCanjeSolicitud . "'";
        }

        $PermiteEliminar = $dbo->getFields("ClubCanje", "PermiteEliminar", "IDClub = $IDClub AND Activo = 'S'");


        $sql = "SELECT * FROM CanjeSolicitud WHERE IDClub = '$IDClub' AND (IDSocio = '$IDSocio' OR 	IDSocioBeneficiario LIKE '%$IDSocio|%') $condicion ORDER BY IDCanjeSolicitud Desc ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($row_solicitud = $dbo->fetchArray($qry)) :
                $solicitud["IDClub"] = $IDClub;
                $solicitud["IDSocio"] = $IDSocio;
                $solicitud["IDCanjeSolicitud"] = $row_solicitud["IDCanjeSolicitud"];
                $solicitud["IDListaClubes"] = $row_solicitud["IDListaClubes"];
                $solicitud["Nombre"] = $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $row_solicitud["IDListaClubes"] . "'");
                $solicitud["IDEstadoCanjeSolicitud"] = $row_solicitud["IDEstadoCanjeSolicitud"];
                $solicitud["Estado"] = utf8_encode($dbo->getFields("EstadoCanjeSolicitud", "Nombre", "IDEstadoCanjeSolicitud = '" . $row_solicitud["IDEstadoCanjeSolicitud"] . "'"));
                $solicitud["Numero"] = $row_solicitud["Numero"] . "\n" . $solicitud["Estado"];
                $solicitud["FechaInicio"] = $row_solicitud["FechaInicio"];

                $fecha = date('Y-m-d');
                $cantidad_dias_canje = (int) $row_solicitud["CantidadDias"] - 1;
                $fechafinal = strtotime('+' . $cantidad_dias_canje . ' day', strtotime($solicitud["FechaInicio"]));
                $nuevafechafinal = date('Y-m-d', $fechafinal);
                $solicitud["FechaFin"] = $nuevafechafinal;
                $solicitud["CantidadDias"] = $row_solicitud["CantidadDias"];

                if ($fecha > $nuevafechafinal) {
                    $PermiteEliminar = 'N';
                }


                $solicitud["PermiteEliminar"] = $PermiteEliminar;

                //Beneficiarios
                $response_benef = array();
                $array_beneficarios = explode("|", $row_solicitud["IDSocioBeneficiario"]);
                if (count($array_beneficarios) > 0) :
                    foreach ($array_beneficarios as $id_socio) :
                        if (!empty($id_socio)) :
                            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $id_socio . "' ", "array");
                            $benef["IDSocio"] = $id_socio;
                            $benef["Nombre"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                            $foto = "";
                            if (!empty($datos_socio["Foto"])) {
                                $foto = SOCIO_ROOT . $datos_socio["Foto"];
                            }
                            $benef["Foto"] = $foto;
                            $benef["Tipo"] = utf8_encode($datos_socio["TipoSocio"]);
                            array_push($response_benef, $benef);
                        endif;
                    endforeach;
                endif;
                $solicitud["Beneficiarios"] = $response_benef;
                array_push($response, $solicitud);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se han encontrado solicitudes registradas";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_solicitud_canje($IDClub, $IDSocio, $IDListaClubes, $FechaInicio, $CantidadDias, $Beneficiarios = "", $ValoresFormulario = "", $IDCiudad = "", $IDPais = "", $Dispositivo = "", $ComentariosSocio = "")
    {

        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDListaClubes) && !empty($IDSocio) && !empty($FechaInicio) && !empty($CantidadDias)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            //verifico que en la configuracion Permita que los canjes queden activos al momento de llegar la solicitud
            $PermiteCanjeActivo = $dbo->getFields("ConfiguracionCanjes", "PermiteCanjeActivo", "IDClub = '" . $IDClub . "' AND Activo='1'");
            //verifico en la configuracion  si al crear el canje en el app se envia correo al club destino
            $PermiteEnviarCorreoClubDestino = $dbo->getFields("ConfiguracionCanjes", "PermiteEnviarCorreoClubDestino", "IDClub = '" . $IDClub . "' AND Activo='1'");

            //verifico si existe un mensaje al crear el canje
            $mensaje_crear_canje = $dbo->getFields("DetalleClubCanje", "MensajeAlCrearCanje", "IDListaClubes = '" . $IDListaClubes . "' and IDClub = '" . $IDClub . "'");
            //verifico si existe un maximo de dias por canje
            $maximoDiasCanje = $dbo->getFields("DetalleClubCanje", "MaximoDias", "IDListaClubes = '" . $IDListaClubes . "' and IDClub = '" . $IDClub . "'");

            //verifico si hay dias de cierre de los canjes
            // $FechaInicioBloqueoCanje = $dbo->getFields("FechasBloqueoCanje", "FechaInicio", "IDListaClubes = '" . $IDListaClubes . "' and IDClub = '" . $IDClub . "'");
            //$FechaFinBloqueoCanje = $dbo->getFields("FechasBloqueoCanje", "FechaFin", "IDListaClubes = '" . $IDListaClubes . "' and IDClub = '" . $IDClub . "'");
            if (!empty($id_socio)) {
                //Consulto el siguiente consecutivo
                $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From CanjeSolicitud Where IDClub = '" . $IDClub . "'";
                $result_numero = $dbo->query($sql_max_numero);
                $row_numero = $dbo->fetchArray($result_numero);
                $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

                //Beneficiarios
                $datos_benef = json_decode($Beneficiarios, true);
                foreach ($datos_benef as $detalle_datos) :
                    $array_benef[] = $detalle_datos["IDSocio"];
                    // VALIDAMOS PARA LAGARTOS QUE SI EL TIPO EN ZEUS ES B02 NO PUEDA HACER LA SOLICITUD
                    if ($IDClub == 7) :
                        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $detalle_datos[IDSocio]", "array");
                        if ($datos_socio[IDTipoSocioZeus] == "B02") :
                            $respuesta["message"] = "Lo sentimos el beneficiario $datos_socio[Nombre] no puede tomar canjes.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                endforeach;

                if (count($array_benef) > 0) :
                    $IDSocioBeneficiario = implode("|", $array_benef) . "|";
                else :
                    $IDSocioBeneficiario = "";
                endif;

                // PARA LAGARTOS Y LA PRADERA VALIDAMOS QUE LE FECHA EN QUE SE EOLICITO NO SE PARA MAS ALLA DE 30 DIAS
                //if ($IDClub == 7 || $IDClub == 16) :
                if ($IDClub == 7) :

                    $Hoy = date("Y-m-d");
                    $MesDespues = strtotime('+30 day', strtotime($Hoy));

                    if (strtotime($FechaInicio) > $MesDespues) :
                        $respuesta["message"] = "No se pueden hacer canjes para fechas de más de 1 mes";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                endif;

                //para la pradera se cuentan los dias de a 7 y que los dias no sean mayor a 28 por año
                if ($IDClub == 16) {
                    /*  if ($CantidadDias <= 7) {
                        $CantidadDias = 7;
                    } else if ($CantidadDias > 7 && $CantidadDias <= 14) {
                        $CantidadDias = 7 + 7;
                    } else if ($CantidadDias > 14 && $CantidadDias <= 21) {
                        $CantidadDias = 7 + 7 + 7;
                    } else if ($CantidadDias > 21 && $CantidadDias <= 8) {
                        $CantidadDias = 7 + 7 + 7 + 7;
                    } */
                    $FechaInicioCanje = explode("-", $FechaInicio);
                    $sql_dias_lapradera = "Select SUM(CantidadDias) as ConteoDias From CanjeSolicitud Where IDClub = '" . $IDClub . "' and YEAR(FechaInicio)='" . $FechaInicioCanje[0] . "' and IDSocio = '" .  $IDSocio . "' and (IDEstadoCanjeSolicitud='2' or IDEstadoCanjeSolicitud='44') AND IDListaClubes='" . $IDListaClubes . "'";

                    //echo "HOLA" . $sql_dias_lapradera;

                    $query_dias_lapradera = $dbo->query($sql_dias_lapradera);
                    $ConteoDiaslapradera = $dbo->fetchArray($query_dias_lapradera);
                    $DiasLaPradera = $ConteoDiaslapradera["ConteoDias"];
                    $TotalDias = $DiasLaPradera + $CantidadDias;
                    if ($TotalDias > 28) {

                        $respuesta["message"] = "No se pueden hacer canjes el maximo de dias es de:28 y lleva " . $ConteoDiaslapradera["ConteoDias"] . " Dias";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                //validamos que no haya bloqueos de canjes en las fechas seleccionadas
                $sql_bloqueos_canjes = "SELECT * FROM FechasBloqueoCanje WHERE IDListaClubes ='" . $IDListaClubes . "' AND IDClub='" . $IDClub . "'";
                $r_bloqueos_canjes = $dbo->query($sql_bloqueos_canjes);
                while ($row_bloqueos_canjes = $dbo->FetchArray($r_bloqueos_canjes)) {
                    $fechaInicioBloqueoCanje = "";
                    $fechaFinBloqueoCanje = "";
                    $fechaInicioBloqueoCanje = $row_bloqueos_canjes["FechaInicio"];
                    $fechaFinBloqueoCanje = $row_bloqueos_canjes["FechaFin"];

                    if ($Dispositivo == "Android") {
                        $FechaInicioCanje = $FechaInicio;
                    } else {
                        $FechaInicioDelCanje = explode(" ", $FechaInicio);
                        $FechaInicioCanje = $FechaInicioDelCanje[0];
                    }

                    $Dias = $CantidadDias - 1;
                    $diaFinalCanje = date("Y-m-d", strtotime($FechaInicioCanje . '+' . $Dias . ' day'));
                    $FechaFinCanje =  $diaFinalCanje;

                    //recorremos las fechas que selecciono el usuario
                    for ($i = $FechaInicioCanje; $i <= $FechaFinCanje; $i = date("Y-m-d", strtotime($i . "+ 1 day"))) {



                        //recorremos las fechas en donde no se permite hacer canjes
                        for ($j = $fechaInicioBloqueoCanje; $j <= $fechaFinBloqueoCanje; $j = date("Y-m-d", strtotime($j . "+ 1 day"))) {

                            if ($i ==  $j) {
                                $respuesta["message"] = "No se pueden hacer canjes en la fecha seleccionada Fecha Inicio Bloqueo:" . $fechaInicioBloqueoCanje . " Fecha Fin Bloqueo:" . $fechaFinBloqueoCanje;
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                    }
                }


                /*    if (!empty($FechaInicioBloqueoCanje) && !empty($FechaFinBloqueoCanje)) :
                    $fechaInicioBloqueoCanje = $FechaInicioBloqueoCanje;
                    $fechaFinBloqueoCanje = $FechaFinBloqueoCanje;

                    $FechaInicioCanje = $FechaInicio;
                    $Dias = $CantidadDias - 1;
                    $diaFinalCanje = date("Y-m-d", strtotime($FechaInicioCanje . '+' . $Dias . ' day'));
                    $FechaFinCanje =  $diaFinalCanje;

                    //recorremos las fechas que selecciono el usuario
                    for ($i = $FechaInicioCanje; $i <= $FechaFinCanje; $i = date("Y-m-d", strtotime($i . "+ 1 day"))) {

                        //recorremos las fechas en donde no se permite hacer canjes
                        for ($j = $fechaInicioBloqueoCanje; $j <= $fechaFinBloqueoCanje; $j = date("Y-m-d", strtotime($j . "+ 1 day"))) {

                            if ($i ==  $j) {
                                $respuesta["message"] = "No se pueden hacer canjes en la fecha seleccionada Fecha Inicio Bloqueo:" . $FechaInicioBloqueoCanje . " Fecha Fin Bloqueo:" . $FechaFinBloqueoCanje;
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                    }
                endif; */





                //validar el estado aprobado del canje para insertarlo como activo
                if ($PermiteCanjeActivo == "S") {
                    //consulto el id del estado aprobado del canje
                    $IDEstadoCanjeSolicitud = $dbo->getFields("EstadoCanjeSolicitud", "IDEstadoCanjeSolicitud", "IDClub = '" . $IDClub . "' AND Descripcion = 'Aprobado'");
                }
                //Para club colombia quedan aprobados al enviarse
                if ($IDClub == 38 || !empty($IDEstadoCanjeSolicitud)) {
                    $IDEstadoCanje = $IDEstadoCanjeSolicitud;
                    //$IDEstadoCanje = 8;
                } else {
                    $IDEstadoCanje = 1;
                }

                if ($maximoDiasCanje > 0) {


                    $accion = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $id_socio . "' and IDClub = '" . $IDClub . "'");
                    $FechaInicioCanje = explode("-", $FechaInicio);


                    $sql_IDSocio = "SELECT IDSocio FROM Socio WHERE AccionPadre='" . $accion . "' and IDClub='" . $IDClub . "'";
                    $query = $dbo->query($sql_IDSocio);

                    while ($DatosIDSocios = $dbo->fetchArray($query)) {
                        $ArrayidSocio[] = $DatosIDSocios["IDSocio"];
                    }
                    $DatosArray = implode(",", $ArrayidSocio);
                    $sql_dias = "Select SUM(CantidadDias) as ConteoDias From CanjeSolicitud Where IDClub = '" . $IDClub . "' and IDListaClubes='" . $IDListaClubes . "' and YEAR(FechaInicio)='" . $FechaInicioCanje[0] . "' and IDSocio in (" .  $DatosArray . ")";
                    $query_dias = $dbo->query($sql_dias);
                    $ConteoDias = $dbo->fetchArray($query_dias);
                    $Dias = $ConteoDias["ConteoDias"];
                    $TotalDias = $Dias + $CantidadDias;

                    if ($TotalDias <= $maximoDiasCanje) {
                        $sql_solicitud = $dbo->query("Insert Into CanjeSolicitud (IDClub,IDCiudad,IDPais, IDSocio, IDListaClubes,  IDEstadoCanjeSolicitud, Numero, FechaInicio, CantidadDias, IDSocioBeneficiario,ComentariosSocio, UsuarioTrCr, FechaTrCr)
                        Values ('" . $IDClub . "','$IDCiudad','$IDPais','" . $IDSocio . "','" . $IDListaClubes . "','" . $IDEstadoCanje . "','" . $siguiente_consecutivo . "','" . $FechaInicio . "','" . $CantidadDias . "','" . $IDSocioBeneficiario . "','" . $ComentariosSocio . "','WebService',NOW())");
                        $id_solicitud = $dbo->lastID();
                    } else {
                        $respuesta["message"] = "No se pueden hacer canjes el maximo de dias es de:" . $maximoDiasCanje . " y lleva " . $ConteoDias["ConteoDias"] . " Dias";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                } else {

                    $sql_solicitud = $dbo->query("Insert Into CanjeSolicitud (IDClub,IDCiudad,IDPais, IDSocio, IDListaClubes,  IDEstadoCanjeSolicitud, Numero, FechaInicio, CantidadDias, IDSocioBeneficiario,ComentariosSocio, UsuarioTrCr, FechaTrCr)
                                                Values ('" . $IDClub . "','$IDCiudad','$IDPais','" . $IDSocio . "','" . $IDListaClubes . "','" . $IDEstadoCanje . "','" . $siguiente_consecutivo . "','" . $FechaInicio . "','" . $CantidadDias . "','" . $IDSocioBeneficiario . "','" . $ComentariosSocio . "','WebService',NOW())");
                    $id_solicitud = $dbo->lastID();
                }

                //Guardo los datos de los campos
                $datos_formulario = json_decode($ValoresFormulario, true);
                if (count($datos_formulario) > 0) :
                    foreach ($datos_formulario as $detalle_datos) :
                        $IDSocioInvitado = $detalle_datos["IDSocio"];
                        $sql_datos_form = $dbo->query("Insert Into CanjeOtrosDatos (IDCanje, IDCampoFormularioCanje, Valor) Values ('" . $id_solicitud . "','" . $detalle_datos["IDCampoFormularioCanje"] . "','" . $detalle_datos["Valor"] . "')");
                        $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];
                    endforeach;
                endif;

                if ($PermiteEnviarCorreoClubDestino == "S") {
                    SIMUtil::notificar_solicitud_canje($id_solicitud, "destino");
                }

                SIMUtil::notificar_solicitud_canje($id_solicitud, "");

                if ($IDClub == 8) {
                    $ClubApp = $dbo->getFields("ListaClubes", "IDClubApp", "IDListaClubes = '" . $IDListaClubes . "'");
                    $CanjeUtomatico = $dbo->getFields("Club", "CanjesAutomaticos", "IDClub = '" . $ClubApp . "'");

                    if ($CanjeUtomatico == "S") {
                        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                        $ClaveApp = sha1(trim($datos_socio["NumeroDocumento"]));
                        $FechaFin = date("Y-m-d", strtotime($FechaInicio . "+ " . $CantidadDias . " days"));

                        $inserta_socio = "	INSERT INTO Socio
                                                    (IDClub, IDCategoria, IDParentesco, IDEstadoSocio, Accion, AccionPadre, Parentesco, Nombre, Apellido,
                                                    FechaNacimiento, NumeroDocumento, Email, Clave, Telefono, Celular, CorreoElectronico, TipoSocio,
                                                    Genero, FechaInicioCanje, FechaFinCanje, ClubCanje,	NumeroInvitados, NumeroAccesos,
                                                    PermiteReservar, FotoActualizadaSocio, UsuarioTrCr,	FechaTrCr)
                                                    Values(
                                                    '" . $ClubApp . "', '" . $datos_socio["IDCategoria"] . "', '" . $datos_socio["IDParentesco"] . "',
                                                    '2', '" . $datos_socio["Accion"] . "', '" . $datos_socio["AccionPadre"] . "', '" . $datos_socio["Parentesco"] . "',
                                                    '" . $datos_socio["Nombre"] . "', '" . $datos_socio["Apellido"] . "', '" . $datos_socio["FechaNacimiento"] . "',
                                                    '" . $datos_socio["NumeroDocumento"] . "', '" . $datos_socio["NumeroDocumento"] . "',
                                                    '" . $ClaveApp . "', '" . $datos_socio["Telefono"] . "', '" . $datos_socio["Celular"] . "',
                                                    '" . $datos_socio["CorreoElectronico"] . "', 'Canje',
                                                    '" . $datos_socio["Genero"] . "', '" . $FechaInicio . "', '" . $FechaFin . "',
                                                    '" . $IDClub . "', '1', '20', 'S', '" . $datos_socio["FotoActualizadaSocio"] . "', 'WEB SERVICE: set_solicitud_canje',
                                                    NOW())";

                        $dbo->query($inserta_socio);
                        $id = $dbo->lastID();

                        $CodigBarras = SIMUtil::generar_codigo_barras($datos_socio["NumeroDocumento"], $id);
                        $CodigoQR = SIMUtil::generar_carne_qr($id, $datos_socio["NumeroDocumento"]);

                        $update = $dbo->query("UPDATE Socio SET CodigoBarras = '" . $CodigBarras . "', CodigoQR = '" . $CodigoQR . "'  WHERE IDSocio = '" . $id . "'");

                        $mensaje = "Socio creado en el club destino." . $id;
                    }
                }

                if (!empty($mensaje_crear_canje)) {
                    $guardado = $mensaje_crear_canje;
                } else {
                    $guardado = "Guardado ";
                }

                $respuesta["message"] = $guardado . $mensaje;
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Error el socio no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "11. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_configuracion_canjes($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLConfiguracion = "SELECT * FROM ConfiguracionCanjes WHERE IDClub = $IDClub AND Activo = 1";
            $QRYCOnfiguracion = $dbo->query($SQLConfiguracion);

            if ($dbo->rows($QRYCOnfiguracion) > 0) :
                $message = $dbo->rows($QRYCOnfiguracion) . "Encontrados";
                while ($Datos = $dbo->fetchArray($QRYCOnfiguracion)) :

                    $InfoConfiguracion[PermitePais] = $Datos[PermitePais];
                    $InfoConfiguracion[PermiteCiudad] = $Datos[PermiteCiudad];
                    $InfoConfiguracion[LabelBotonPais] = $Datos[LabelBotonPais];
                    $InfoConfiguracion[LabelBotonCiudad] = $Datos[LabelBotonCiudad];

                    $InfoConfiguracion[PaisesConvenios] = $Datos[PaisesConvenios];
                    $InfoConfiguracion[CiudadesConvenios] = $Datos[CiudadesConvenios];
                    $InfoConfiguracion[PermiteComentarios] = $Datos[PermiteComentarios];

                    array_push($response, $InfoConfiguracion);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_ciudades_canjes($IDClub, $IDPais)
    {
        $dbo = SIMDB::get();
        $response = array();

        $Config = SIMWebServiceCanjes::get_configuracion_canjes($IDClub);
        $CiudadesConvenio = $Config[response][0][CiudadesConvenios];

        $ArrayCiudades = explode("|||", $CiudadesConvenio);
        foreach ($ArrayCiudades as $id => $Ciudad) :
            $ArrayCiudad = explode("-", $Ciudad);
            $IDCiudad = $ArrayCiudad[0];

            if (!empty($IDCiudad))
                $CiudadesBuscar[] = $IDCiudad;
        endforeach;

        if (count($CiudadesBuscar) > 0) :
            $Ciudades = implode(",", $CiudadesBuscar);
            $CondicionCiudades = " AND IDCiudad IN ($Ciudades)";
        endif;

        if (!empty($IDPais)) :
            $CondicionPais = " AND IDPais = $IDPais ";
        else :
            $CondicionPais = " AND IDPais = 1 ";
        endif;

        if (!empty($IDClub)) :

            $SQLCiudades = "SELECT IDCiudad,Nombre FROM Ciudad WHERE Publicar = 'S' $CondicionPais $CondicionCiudades ORDER BY Nombre ASC";
            $QRYCiudades = $dbo->query($SQLCiudades);

            if ($dbo->rows($QRYCiudades) > 0) :

                $message = $dbo->rows($QRYCiudades) . "Encontrados";
                while ($Datos = $dbo->fetchArray($QRYCiudades)) :

                    $InfoCiudad[IDCiudad] = $Datos[IDCiudad];
                    $InfoCiudad[NombreCiudad] = $Datos[Nombre];

                    array_push($response, $InfoCiudad);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_paises_canjes($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        $Config = SIMWebServiceCanjes::get_configuracion_canjes($IDClub);
        $PaisesConvenio = $Config[response][0][PaisesConvenios];

        $ArrayPaises = explode("|||", $PaisesConvenio);
        foreach ($ArrayPaises as $id => $Pais) :
            $ArrayPaises = explode("-", $Pais);
            $IDPais = $ArrayPaises[0];

            if (!empty($IDPais))
                $PaisesBuscar[] = $IDPais;
        endforeach;


        if (count($PaisesBuscar) > 0) :
            $Paises = implode(",", $PaisesBuscar);
            $CondicionPaises = " AND IDPais IN ($Paises)";
        endif;

        if (!empty($IDClub)) :

            $SQLPaises = "SELECT IDPais,Nombre FROM Pais WHERE Publicar = 'S' $CondicionPaises ORDER BY Nombre ASC";
            $QRYPaises = $dbo->query($SQLPaises);

            if ($dbo->rows($QRYPaises) > 0) :

                $message = $dbo->rows($QRYPaises) . "Encontrados";
                while ($Datos = $dbo->fetchArray($QRYPaises)) :

                    $InfoPais[IDPais] = $Datos[IDPais];
                    $InfoPais[NombrePais] = $Datos[Nombre];

                    array_push($response, $InfoPais);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_lista_clubes($IDClub, $IDPais, $IDCiudad)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDPais)) :
            $CondicionPais = " AND LC.IDPais = $IDPais ";
        endif;

        if (!empty($IDCiudad)) :
            $CondicionCiudad = " AND LC.IDCiudad = $IDCiudad ";
        endif;

        $response = array();
        $sql = "SELECT DC.*, LC.Nombre, P.Nombre as NombrePais FROM DetalleClubCanje DC,ListaClubes LC, Pais P  WHERE DC.IDListaClubes=LC.IDListaClubes and LC.IDPais=P.IDPais and DC.IDClub = '$IDClub' $CondicionPais $CondicionCiudad Order By NombrePais, LC.Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $lista_club["IDListaClubes"] = $r["IDListaClubes"];
                $id_pais = utf8_encode($dbo->getFields("ListaClubes", "IDPais", "IDListaClubes = '" . $r["IDListaClubes"] . "'"));
                if ($IDClub != 48) //para comercio pereira no quieren que aparezca el pais
                {
                    $pais = $dbo->getFields("Pais", "Nombre", "IDPais = '" . $id_pais . "'") . " - ";
                } else {
                    $pais = "";
                }

                if (!empty($r["Descripcion"])) {
                    $Descripcion = "-" . $r["Descripcion"];
                } else {
                    $Descripcion = "";
                }

                $lista_club["Nombre"] = $pais . $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $r["IDListaClubes"] . "'") . $Descripcion;
                $lista_club["DiaMinimo"] = (int)utf8_encode($dbo->getFields("ClubCanje", "DiaMinimo", "IDClubCanje = '" . $r["IDClubCanje"] . "'"));
                $lista_club["DiaMaximo"] = (int)utf8_encode($dbo->getFields("ClubCanje", "DiaMaximo", "IDClubCanje = '" . $r["IDClubCanje"] . "'"));
                $SecuenciaDia = (int) $dbo->getFields("ClubCanje", "SecuenciaDia", "IDClubCanje = '" . $r["IDClubCanje"] . "'");
                if ($SecuenciaDia == 0) {
                    $SecuenciaDia = 1;
                }
                //$SecuenciaDia = 1;
                //$lista_club["DiaMinimo"]=1;
                //$lista_club["DiaMaximo"]=9;

                $lista_club["SecuenciaDia"] = $SecuenciaDia;

                //Campos Formulario
                $response_campo_formulario = array();
                $sql_campo_form = "SELECT * FROM CampoFormularioCanje WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                $qry_campo_form = $dbo->query($sql_campo_form);
                if ($dbo->rows($qry_campo_form) > 0) {
                    while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                        $campoformulario["IDCampoFormularioCanje"] = $r_campo["IDCampoFormularioCanje"];
                        $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                        $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                        $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                        $campoformulario["Valores"] = $r_campo["Valores"];
                        array_push($response_campo_formulario, $campoformulario);
                    } //end while
                    $lista_club["CampoFormulario"] = $response_campo_formulario;
                } else {
                    $lista_club["CampoFormulario"] = $response_campo_formulario;
                }

                array_push($response, $lista_club);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registro";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function
}
