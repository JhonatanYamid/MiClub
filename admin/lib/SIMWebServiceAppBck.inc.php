<?php

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class SIMWebServiceApp
{

    public function verifica_permiso_modulo($IDModulo, $IDPerfil)
    {
        $dbo = &SIMDB::get();
        $agregar_modulo = "S";

        //Dependiendo el perfil muestro o no los modulos
        switch ($IDModulo) {
            case "1": //Invitados, Solo porteria y admin puede tener acceso a este modulo
                if ($IDPerfil == "4" || $IDPerfil == "25" || $IDPerfil == "16" || $IDPerfil == "63") {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
            case "29": //Agenda, Solo porteria y admin puede tener acceso a este modulo
                if ($IDPerfil == "2" || $IDPerfil == "3" || $IDPerfil == "10" || $IDPerfil == "31" || $IDPerfil == "63") {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
            case "2": //Reservas, Solo porteria y admin puede tener acceso a este modulo
                if ($IDPerfil == "2" || $IDPerfil == "3" || $IDPerfil == "10") {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
        }

        //Perfil admin puede ver todo
        if ($IDPerfil == "1"):
            $agregar_modulo = "S";
        endif;

        return $agregar_modulo;

    }

    public function valida_usuario_web($email, $clave, $id_club, $id_club_consulta = "", $AppVersion)
    {
        $dbo = &SIMDB::get();
        $foto_cod_barras = "";

        if (empty($id_club_consulta)) {
            $id_club_consulta = $id_club;
        }

        if (!empty($email) && !empty($clave)) {

            //$sql_verifica = "SELECT * FROM Usuario WHERE User = '".$email ."' and Password = '".sha1($clave )."' and IDClub = '".$id_club."' and Activo <> 'N'";
            $sql_verifica = "SELECT * FROM Usuario WHERE User = '" . $email . "' and Password = '" . sha1($clave) . "' and IDClub in (" . $id_club_consulta . ") and Activo <> 'N'";

            $qry_verifica = $dbo->query($sql_verifica);
            if ($dbo->rows($qry_verifica) == 0) {
                $respuesta["message"] = "No encontrado";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {
                    $datos_usuario = $dbo->fetchArray($qry_verifica);

                    $CerrarSesion = $datos_usuario["SolicitarCierreSesion"];
                    if ($CerrarSesion == "S") {
                        $condicion_modulo = " and IDModulo = 14 "; //cerrar sesion
                    }

                    //Modulos Sistema Menu Central
                    $response_modulo = array();
                    $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Central%' " . $condicion_modulo . " ORDER BY Orden";
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                            $agregar_modulo = self::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                            if ($agregar_modulo == "S"):
                                // Verificar si el modulo tiene contenido para mostrar
                                $flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                //$flag_mostrar=0;
                                if ($flag_mostrar == 0):
                                    $modulo["IDClub"] = $datos_usuario["IDClub"];
                                    $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                    if (!empty($r_modulo["Titulo"])) {
                                        $modulo["NombreModulo"] = trim($r_modulo["Titulo"]);
                                    } else {
                                        $modulo["NombreModulo"] = trim($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                                    }

                                    $modulo["Orden"] = $r_modulo["Orden"];
                                    $icono_modulo = $r_modulo["Icono"];
                                    if (!empty($r_modulo["Icono"])):
                                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                                    else:
                                        $foto = "";
                                    endif;
                                    $modulo["Icono"] = $foto;
                                    $modulo["MostrarBadgeNotificaciones"] = $r_modulo["MostrarBadgeNotificaciones"];
                                    array_push($response_modulo, $modulo);
                                endif;
                            endif;

                        } //ednw while
                    }

                    //Modulos Sistema Menu Lateral
                    unset($modulo);
                    $response_modulo_lateral = array();
                    $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Lateral%'  " . $condicion_modulo . " ORDER BY Orden";
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {

                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                            $agregar_modulo = self::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                            if ($agregar_modulo == "S"):
                                // Verificar si el modulo tiene contenido para mostrar
                                $flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                //$flag_mostrar=0;
                                if ($flag_mostrar == 0):
                                    $modulo["IDClub"] = $datos_usuario["IDClub"];
                                    $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                    //$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
                                    if (!empty($r_modulo["Titulo"])) {
                                        $modulo["NombreModulo"] = $r_modulo["Titulo"];
                                    } else {
                                        $modulo["NombreModulo"] = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'");
                                    }

                                    $modulo["Orden"] = $r_modulo["Orden"];
                                    $icono_modulo = $r_modulo["Icono"];
                                    if (!empty($r_modulo["Icono"])):
                                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                                    else:
                                        $foto = "";
                                    endif;
                                    $modulo["Icono"] = $foto;
                                    $modulo["MostrarBadgeNotificaciones"] = $r_modulo["MostrarBadgeNotificaciones"];
                                    array_push($response_modulo_lateral, $modulo);
                                endif;
                            endif;

                        } //ednw while
                    }

                    //traer servicios del usuario
                    $response_servicio = array();
                    $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $datos_usuario["IDUsuario"] . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
                    $qry_servicios = $dbo->query($sql_servicios);
                    while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                        $servicio["IDClub"] = $datos_usuario["IDClub"];
                        $servicio["IDServicio"] = $r_servicio["IDServicio"];
                        $servicio["NombreServicio"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' ");
                        if (!empty($r_servicio["Icono"])):
                            $foto = SERVICIO_ROOT . $r_servicio["Icono"];
                        else:
                            $foto = "";
                        endif;

                        $servicio["Icono"] = $foto;
                        //$servicio["ServicioInicial"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$r_servicio["IDServicioInicial"]."'" );
                        array_push($response_servicio, $servicio);

                    } //end while

                    $tipo_codigo_carne = $dbo->getFields("AppEmpleado", "TipoCodigoCarne", "IDClub = '" . $id_club . "'");
                    switch ($tipo_codigo_carne) {
                        case "Barras":
                            if (!empty($datos_usuario["CodigoBarras"])) {
                                $foto_cod_barras = USUARIO_ROOT . $datos_usuario["CodigoBarras"];
                            }
                            break;
                        case "QR":
                            if (!empty($datos_usuario["CodigoQR"])) {
                                $foto_cod_barras = USUARIO_ROOT . "qr/" . $datos_usuario["CodigoQR"];
                            }
                            break;
                        default:
                            $foto_cod_barras = "";
                    }

                    if (!empty($datos_usuario["Foto"])) {
                        $foto_empleado = USUARIO_ROOT . $datos_usuario["Foto"];
                    }

                    $response["IDClub"] = $datos_usuario["IDClub"];
                    $response["IDUsuario"] = $datos_usuario["IDUsuario"];
                    $response["IDPerfil"] = $datos_usuario["IDPerfil"];
                    $response["Nombre"] = $datos_usuario["Nombre"];
                    $response["Autorizado"] = $datos_usuario["Autorizado"];
                    $response["Nivel"] = $datos_usuario["Nivel"];
                    $response["Permiso"] = $datos_usuario["Permiso"];
                    $response["ServiciosReserva"] = $response_servicio;
                    $response["Modulos"] = $response_modulo;
                    $response["ModulosLateral"] = $response_modulo_lateral;
                    $response["CodigoBarras"] = $foto_cod_barras;
                    $response["Dispositivo"] = $datos_usuario["Dispositivo"];
                    $response["Token"] = $datos_usuario["Token"];
                    $response["ColorFondoCarne"] = $dbo->getFields("AppEmpleado", "ColorFondoCarne", "IDClub = '" . $id_club . "'");
                    //$response["NumeroDerecho"] = $datos_usuario["CodigoUsuario"];
                    $response["NumeroDerecho"] = "";
                    //Consulto si el app esta configurado para permitir se puede cambiar p[ara que sea por usuario
                    $response["PermiteInvitacionPortero"] = $dbo->getFields("AppEmpleado", "PermiteInvitacionPortero", "IDClub = '" . $id_club . "'");
                    //Consulto las areas
                    $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $datos_usuario["IDUsuario"] . "'";
                    $result_area_usuario = $dbo->query($sql_area_usuario);
                    while ($row_area = $dbo->fetchArray($result_area_usuario)):
                        $nombre_area = utf8_encode($dbo->getFields("Area", "Nombre", "IDArea = '" . $row_area["IDArea"] . "'"));
                        $array_areas[] = $nombre_area;
                    endwhile;
                    if (count($array_areas) > 0):
                        $nombre_areas = implode(",", $array_areas);
                    endif;

                    $nombre_areas = "";
                    $response["Area"] = $nombre_areas;
                    $response["Cargo"] = utf8_encode($datos_usuario["Cargo"]);
                    $response["Codigo"] = $datos_usuario["CodigoUsuario"];
                    $response["PermiteReservar"] = $datos_usuario["PermiteReservar"];
                    $response["Activo"] = $datos_usuario["Activo"];
                    $response["Foto"] = $foto_empleado;
                    $response["TipoUsuario"] = "Empleado";

                    //Encuestas al abrir app
                    $encuesta_activa = 0;
                    $response_encuesta = array();
                    $sql_encuesta = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                    $qry_encuesta = $dbo->query($sql_encuesta);
                    if ($dbo->rows($qry_encuesta) > 0) {
                        while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) {
                            $mostrar_encuesta = 0;
                            //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                            if ($r_encuesta["UnaporSocio"] == "S") {
                                $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $datos_usuario["IDUsuario"] . "' and IDEncuesta = '" . $r_encuesta["IDEncuesta"] . "' Limit 1";
                                $r_resp = $dbo->query($sql_resp);
                                if ($dbo->rows($r_resp) <= 0) {
                                    $mostrar_encuesta = 1;
                                }
                            } else {
                                $mostrar_encuesta = 1;
                            }
                            //Verifico si la encuesta es solo para algunos socios para mostrar o no
                            //$permiso_encuesta=SIMWebServiceApp::verifica_ver_encuesta($r_encuesta,$IDSocio);
                            $permiso_encuesta = 1;

                            if ($mostrar_encuesta == 1 && $permiso_encuesta == 1) {
                                $encuesta["IDClub"] = $r_encuesta["IDClub"];
                                $encuesta["IDEncuesta"] = $r_encuesta["IDEncuesta"];
                                $encuesta["Nombre"] = $r_encuesta["Nombre"];
                                $encuesta["Descripcion"] = $r_encuesta["Descripcion"];
                                if (!empty($r_encuesta["Imagen"])):
                                    $foto = BANNERAPP_ROOT . $r_encuesta["Imagen"];
                                else:
                                    $foto = "";
                                endif;
                                $encuesta["ImagenEncuesta"] = $foto;
                                $encuesta_activa = 1;

                                array_push($response_encuesta, $encuesta);
                            }

                        } //ednw while
                    }
                    //FIN Encuestas al abrir app
                    $response["Encuesta"] = $response_encuesta;
                    $response["LabelEncuesta"] = "Encuesta";

                    //Autodisagnostico al abrir app
                    $diagnostico_activa = 0;
                    $response_diagnostico = array();
                    $sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                    $qry_diagnostico = $dbo->query($sql_diagnostico);
                    if ($dbo->rows($qry_diagnostico) > 0) {
                        while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                            $mostrar_disgnostico = 0;
                            //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                            if ($r_diagnostico["UnaporSocio"] == "S") {
                                $sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDUsuario = '" . $IDUsuario . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
                                $r_resp = $dbo->query($sql_resp);
                                if ($dbo->rows($r_resp) <= 0) {
                                    $mostrar_disgnostico = 1;
                                }
                            } else {
                                $mostrar_disgnostico = 1;
                            }
                            //Verifico si la encuesta es solo para algunos socios para mostrar o no
                            $permiso_diagnostico = SIMWebServiceApp::verifica_ver_diagnostico($r_diagnostico, $IDSocio, $IDUsuario);
                            //$permiso_encuesta=1;

                            if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
                                $diagnostico["IDClub"] = $r_diagnostico["IDClub"];
                                $diagnostico["IDDiagnostico"] = $r_diagnostico["IDDiagnostico"];
                                $diagnostico["Nombre"] = $r_diagnostico["Nombre"];
                                $diagnostico["Descripcion"] = $r_diagnostico["Descripcion"];
                                if (!empty($r_diagnostico["Imagen"])):
                                    $foto = BANNERAPP_ROOT . $r_diagnostico["Imagen"];
                                else:
                                    $foto = "";
                                endif;
                                $diagnostico["ImagenDiagnostico"] = $foto;
                                $diagnostico_activa = 1;

                                array_push($response_diagnostico, $diagnostico);
                            }

                        } //ednw while
                    }
                    //FIN Encuestas al abrir app
                    $response["Diagnostico"] = $response_diagnostico;
                    $response["LabelDiagnostico"] = "Diligenciar auto";

                    $response_campo_editar = array();
                    $sql_campo_editar = "SELECT CEU.* FROM CampoEditarUsuario CEU
																		WHERE CEU.IDClub = '" . $datos_usuario["IDClub"] . "' ORDER BY CEU.Orden";

                    $qry_campo_editar = $dbo->query($sql_campo_editar);
                    if ($dbo->rows($qry_campo_editar) > 0) {
                        while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                            $campo_editar["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarUsuario"];
                            $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                            $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                            $campo_editar["Valores"] = $r_campo_editar["Valores"];
                            $campo_editar["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                            //Consulto el valor de la actualización
                            $ValorDatoCampo = $dbo->getFields("UsuarioCampoEditarUsuario", "Valor", "IDCampoEditarUsuario = '" . $r_campo_editar["IDCampoEditarUsuario"] . "' and IDUsuario = '" . $datos_usuario["IDUsuario"] . "'");
                            if ($ValorDatoCampo != "" && $ValorDatoCampo != "false") {
                                $ValorDato = $ValorDatoCampo;
                            } else {
                                $ValorDato = $datos_socio[$r_campo_editar["Nombre"]];

                            }

                            //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                            $campo_editar["ValorActual"] = $ValorDato;

                            $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                            array_push($response_campo_editar, $campo_editar);

                        } //ednw while
                    }
                    $response["CampoEditar"] = $response_campo_editar;

                    if ($AppVersion >= 31) {
                        $respuesta = json_encode($response);
                        $param['key'] = KEY_API;
                        $param['nonce'] = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
                        $param['msg'] = $respuesta;
                        $result = SIMUtil::cryptSodium($param);

                        //$response_encrip=array();
                        //$response_encrip[ "data" ] = $param['nonce'].sodium_bin2hex($result["cryptedText"]);
                        $respuesta = array();
                        $respuesta["message"] = "ok";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $param['nonce'] . sodium_bin2hex($result["cryptedText"]);

                    } else {
                        $respuesta["message"] = "ok";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    }

                    //$respuesta["message"] = "ok";
                    //$respuesta["success"] = true;
                    //$respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        } //end function

        public function get_diagnostico_persona($IDClub, $NumeroDocumento, $Fecha)
    {

            $dbo = &SIMDB::get();
            $IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' ");
            if (!empty($Fecha)) {
                $fecha_hoy = $Fecha;
            } else {
                $fecha_hoy = date("Y-m-d");
            }
            if (!empty($IDSocio)) {
                $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $IDSocio . "' GROUP BY IDSocio ";
                $r_unica = $dbo->query($sql_unica);
                $total_unica = $dbo->rows($r_unica);
                $row_resp_diag = $dbo->fetchArray($r_unica);
                $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                if ($total_unica <= 0) {
                    $alerta_diagnostico = "Atención la persona no ha llenado el diagnostico";
                    $respuesta_diagnostico = 2;
                } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                $alerta_diagnostico = "Atencion Diagnostico sospechoso";
                $respuesta_diagnostico = 3;
            } else {
                $alerta_diagnostico = "Diagnostico correcto";
                $respuesta_diagnostico = 1;
            }
            $respuesta["message"] = $respuesta_diagnostico;
            $respuesta["success"] = true;
            $respuesta["response"] = $alerta_diagnostico;
        } else {
            $respuesta["message"] = "Documento no encontrado";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_agenda($IDClub, $IDUsuario, $Fecha)
    {
        $dbo = &SIMDB::get();
        if (empty($Fecha)):
            $Fecha = date("Y-m-d");
        endif;

        if (!empty($IDUsuario)) {
            //Consulto el servicio que tiene permiso y el elemnto para consultar la agenda
            $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
            $qry_servicios = $dbo->query($sql_servicios);
            $response_agenda = array();
            $response = array();
            $agenda_dia = false;
            while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                //Consulto solo los elementos al los que tiene permiso de ver
                //$sql_elementos = "Select * From UsuarioServicioElemento Where IDUsuario = '".$IDUsuario."'";
                $sql_elementos = "Select *
									  From UsuarioServicioElemento USES, ServicioElemento SE
									  Where SE.IDServicioElemento = USES.IDServicioElemento
									  and IDServicio = '" . $r_servicio["IDServicio"] . "'
									  and IDUsuario = '" . $IDUsuario . "'";

                $qry_elementos = $dbo->query($sql_elementos);
                while ($row_elemento = $dbo->fetchArray($qry_elementos)):
                    //Si el elemnto pertenece al servicio lo consulto
                    $horas = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $r_servicio["IDServicio"], $Fecha, $row_elemento["IDServicioElemento"], "Agenda", "", "", "", "S", "", $IDUsuario);

                    if ($horas["response"][0]):
                        if (count($horas["response"][0]["Disponibilidad"][0]) > 0):
                            $agenda_dia = true;
                            array_push($response, $horas["response"][0]);
                        endif;
                    endif;
                endwhile;

            } //end while

            //Para los auxiliares monitores muestro los elemtos donde esten reservados
            $sql_aux = "SELECT A.IDAuxiliar, IDServicio FROM UsuarioAuxiliar UA, Auxiliar A WHERE UA.IDAuxiliar=A.IDAuxiliar and UA.IDUsuario='" . $IDUsuario . "' ";
            $result_aux = $dbo->query($sql_aux);
            while ($row_aux = $dbo->fetchArray($result_aux)) {
                // Consulto las reserva en esta fecha de este usuario
                $sql_reserva = "SELECT IDServicioElemento From ReservaGeneral Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "' and IDAuxiliar like '" . $row_aux["IDAuxiliar"] . ",%' ";
                $r_reserva = $dbo->query($sql_reserva);
                while ($row_reserva = $dbo->fetchArray($r_reserva)) {
                    $array_elemento[$row_reserva["IDServicioElemento"]] = $row_reserva["IDServicioElemento"];
                }
                if (count($array_elemento > 0)) {
                    foreach ($array_elemento as $id_elemento_aux) {
                        unset($array_disponibilidad);
                        $horas = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $row_aux["IDServicio"], $Fecha, $id_elemento_aux, "Agenda", "", "", "", "S");
                        if ($horas["response"][0]):
                            if (count($horas["response"][0]["Disponibilidad"][0]) > 0):
                                $agenda_dia = true;
                                // Solo muestro donde este reservado el auxiliar
                                foreach ($horas["response"][0]["Disponibilidad"][0] as $datos_disponibilidad) {
                                    $array_id_aux = explode(",", $datos_disponibilidad["IDAuxiliar"]);
                                    if (in_array($row_aux["IDAuxiliar"], $array_id_aux)) {
                                        $array_disponibilidad[] = $datos_disponibilidad;
                                        //print_r($datos_disponibilidad["IDAuxiliar"]);
                                        //echo "<br>";
                                    }
                                }
                                if (count($array_disponibilidad) <= 0) {
                                    $array_disponibilidad = array();
                                }
                                $horas["response"][0]["Disponibilidad"][0] = $array_disponibilidad;
                                array_push($response, $horas["response"][0]);
                            endif;
                        endif;
                    }
                }
            }

            if ($agenda_dia):
                //$response["Agenda"] = $response_agenda;
                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else:
                //$response["Agenda"] = $response_agenda;
                $respuesta["message"] = "No tiene reservas para hoy.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        } else {
            $respuesta["message"] = "28. Atencion faltan parametros en agenda";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_parametros_empleados($id_club)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT * FROM ParametroAcceso WHERE IDClub = '" . $id_club . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["IconoFamiliar"])) {
                    $foto_familiar = CLUB_ROOT . $r["IconoFamiliar"];
                }
                if (!empty($r["IconoIndividual"])) {
                    $foto_individual = CLUB_ROOT . $r["IconoIndividual"];
                }

                //Tipo Invitado
                $response_tipo_invitado = array();
                $array_tipo_invitado = explode("|", $r["TipoInvitado"]);
                if (count($array_tipo_invitado) > 0):
                    foreach ($array_tipo_invitado as $nombre_tipo):
                        $dato_tipo_invitado[] = $nombre_tipo;
                        array_push($response_tipo_invitado, $nombre_tipo);
                    endforeach;
                endif;

                //Tipo Autorizacion
                $response_tipo_autorizacion = array();
                $array_tipo_autorizacion = explode("|", $r["TipoAutorizacion"]);
                if (count($array_tipo_autorizacion) > 0):
                    foreach ($array_tipo_autorizacion as $nombre_tipo):
                        $dato_tipo_autorizacion[] = $nombre_tipo;
                        array_push($response_tipo_autorizacion, $nombre_tipo);
                    endforeach;
                endif;

                //Tipo Documentos
                $response_tipodoc = array();
                $sql_tipodoc = "SELECT * FROM TipoDocumento WHERE Publicar = 'S' ORDER BY Nombre";
                $qry_tipodoc = $dbo->query($sql_tipodoc);
                if ($dbo->rows($qry_tipodoc) > 0) {
                    while ($r_tipodoc = $dbo->fetchArray($qry_tipodoc)) {
                        $tipodoc["IDTipoDocumento"] = (int) $r_tipodoc["IDTipoDocumento"];
                        $tipodoc["Nombre"] = $r_tipodoc["Nombre"];
                        array_push($response_tipodoc, $tipodoc);

                    } //ednw hile
                }

                //Consulto el icono de contratistas
                //Modulos Sistema Menu Central
                $response_modulo = array();
                $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Central%' and IDModulo = 26 ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {
                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                        // Verificar si el modulo tiene contenido para mostrar
                        $flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                        //$flag_mostrar=0;
                        if ($flag_mostrar == 0):
                            if (!empty($r_modulo["Titulo"])) {
                                $modulo["NombreModulo"] = trim($r_modulo["Titulo"]);
                            } else {
                                $modulo["NombreModulo"] = trim($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                            }

                            $modulo["Orden"] = $r_modulo["Orden"];
                            $icono_modulo = $r_modulo["Icono"];
                            if (!empty($r_modulo["Icono"])):
                                $foto_modulo = MODULO_ROOT . $r_modulo["Icono"];
                            else:
                                $foto_modulo = "";
                            endif;
                            $icono_contratista = $foto_modulo;
                        endif;

                    } //ednw while
                }

                $datos_acceso["IDClub"] = $r["IDClub"];
                $datos_acceso["GrupoFamiliar"] = $r["GrupoFamiliar"];
                $datos_acceso["IconoFamiliar"] = $foto_familiar;
                $datos_acceso["NombreFamiliar"] = $r["NombreFamiliar"];
                $datos_acceso["Invitado"] = $r["Invitado"];
                $datos_acceso["IconoIndividual"] = $foto_individual;
                $datos_acceso["NombreIndividual"] = $r["NombreIndividual"];
                $datos_acceso["TipoInvitado"] = $response_tipo_invitado;
                $datos_acceso["IconoContratista"] = $icono_contratista;
                $datos_acceso["NombreContratista"] = $modulo["NombreModulo"];
                $datos_acceso["TipoAutorizacion"] = $response_tipo_autorizacion;
                $datos_acceso["TipoDocumento"] = $response_tipodoc;
                $datos_acceso["TextoMenorEdad"] = $r["TextoMenorEdad"];

                array_push($response, $datos_acceso);

            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se ha encontrado club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_invitado_documento($IDClub, $Documento)
    {

            $dbo = &SIMDB::get();
            if (!empty($Documento)) {

                //BUSQUEDA INVITADOS ACCESOS
                $qryString = str_replace(".", "", $Documento);
                $qryString = str_replace(",", "", $qryString);
                $qryString = str_replace("-", "", $qryString);

                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "' Order By IDSocioInvitadoEspecial";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "' Order By IDSocioInvitadoEspecial";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

                if ($datos_invitacion["Ingreso"] == "N") {
                    $accion_acceso = "ingreso";
                    $label_accion_acceso = "Ingres&oacute;";
                } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S"):
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;
            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '" . (int) $qryString . "' and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";

                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                }
            endif;
            //FIN BUSQUEDA CONTRATISTA

            if ($total_resultados <= 0) {
                $respuesta["message"] = "No encontrado";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                    $response["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                    $response["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                    $response["FechaInicio"] = $datos_invitacion["FechaInicio"];
                    $response["FechaFin"] = $datos_invitacion["FechaFin"];
                    $response["Accion"] = $datos_socio["Accion"];
                    $response["Socio"] = "Invitado por: " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Inv " . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . "  " . $datos_socio["Predio"];
                    $response["TipoSocio"] = $datos_socio["TipoSocio"];
                    $response["Observaciones"] = $datos_socio["Predio"];
                    $response["Ingreso"] = $datos_invitacion["Ingreso"];
                    $response["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                    $response["Salida"] = $datos_invitacion["Salida"];
                    $response["FechaSalida"] = $datos_invitacion["FechaFin"];

                    if (!empty($datos_invitado[FotoFile])) {
                        $foto = SOCIO_ROOT . $datos_invitado["FotoFile"];
                    } else {
                        $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $response["Foto"] = $foto;
                    $response["NombreInvitado"] = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"];
                    $response["TipoDocumentoInvitado"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    $response["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];

                    //SI ES CABEZA CONUSLTO EL GRUPO FAMILIAR
                    $response_invitado_familia = array();
                    if ($datos_invitacion["CabezaInvitacion"] == "S"):
                        while ($datos_grupo_familiar = $dbo->fetchArray($result_grupo)):
                            $datos_invitado_familiar = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array");
                            if (!empty($datos_invitado_familiar[FotoFile])) {
                                $foto = INVITADO_ROOT . $datos_invitado_familiar[FotoFile];
                            } else {
                                $foto = URLROOT . "/images/sinfoto.png";
                            }

                            $dato_invitado_asociado["IDClub"] = $IDClub;
                            $dato_invitado_asociado["IDInvitacion"] = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                            $dato_invitado_asociado["Nombre"] = utf8_encode($datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"]);
                            $dato_invitado_asociado["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
                            //Consulto el historial de ingresos y salidas del dia
                            $response_historial_acceso_grupo = array();
                            $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                            $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                            $sql_historial_grupo = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_grupo_familiar["IDSocioInvitadoEspecial"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                            while ($row_historial_grupo = $dbo->fetchArray($sql_historial_grupo)):
                                $dato_historial_grupo["Tipo"] = $row_historial_grupo["Tipo"];
                                $dato_historial_grupo["Salida"] = $row_historial_grupo["Salida"];
                                $dato_historial_grupo["FechaSalida"] = $row_historial_grupo["FechaSalida"];
                                $dato_historial_grupo["Entrada"] = $row_historial_grupo["Entrada"];
                                $dato_historial_grupo["FechaIngreso"] = $row_historial_grupo["FechaIngreso"];
                                array_push($response_historial_acceso_grupo, $dato_historial_grupo);
                            endwhile;
                            $dato_invitado_asociado["Historial"] = $response_historial_acceso_grupo;
                            //Fin Historial de acceso
                            array_push($response_invitado_familia, $dato_invitado_asociado);
                        endwhile;
                    endif;

                    $response["GrupoInvitado"] = $response_invitado_familia;

                    //Consulto el historial de ingresos y salidas del dia
                    $response_historial_acceso = array();
                    $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                    $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                    $sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_invitacion["IDInvitacion"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                    while ($row_historial = $dbo->fetchArray($sql_historial)):
                        $dato_historial["Tipo"] = $row_historial["Tipo"];
                        $dato_historial["Salida"] = $row_historial["Salida"];
                        $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                        $dato_historial["Entrada"] = $row_historial["Entrada"];
                        $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
                        array_push($response_historial_acceso, $dato_historial);
                    endwhile;
                    $response["Historial"] = $response_historial_acceso;

                    $respuesta["message"] = "ok";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "Ver1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        } //end function

        public function get_invitado_documento_v2($IDClub, $Documento)
    {
            $dbo = &SIMDB::get();
            if (!empty($Documento)) {
                $autorizacion_recogida = 0;
                $autorizacion_invitacion = 0;

                //BUSQUEDA INVITADOS ACCESOS
                $qryString = str_replace(".", "", $Documento);
                $qryString = str_replace(",", "", $qryString);
                $qryString = str_replace("-", "", $qryString);
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SIE.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $tipo_socio = $datos_socio["TipoSocio"];
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

                if ($datos_invitacion["Ingreso"] == "N") {
                    $accion_acceso = "ingreso";
                    $label_accion_acceso = "Ingres&oacute;";
                } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S"):
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;
            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '" . (int) $qryString . "' and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";

                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    if ($total_resultados > 0) {
                        $autorizacion_invitacion = 1;
                    }

                    $datos_invitado_otro = $dbo->fetchAll("Invitado", " NumeroDocumento = '" . $qryString . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                    $datos_invitado["NumeroDocumento"] = $qryString;
                    $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
                }
            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA USUARIO FUNCIONARIO
            if ($total_resultados <= 0):
                $sql_invitacion = "Select * From Usuario Where NumeroDocumento = '" . (int) $qryString . "' and IDClub = '" . $IDClub . "'";
                $modo_busqueda = "DOCUMENTO";

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $id_registro = $datos_invitacion["IDUsuario"];

                $fecha_hoy = date("Y-m-d") . " 00:00:00";
                $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDUsuario='" . $id_registro . "' Group by IDusuario ";
                $r_unica = $dbo->query($sql_unica);
                $total_unica = $dbo->rows($r_unica);
                $row_resp_diag = $dbo->fetchArray($r_unica);
                $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                if ($total_unica <= 0) {
                    $alerta_diagnostico = "Atención la persona no ha llenado el diagnostico";
                } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                $alerta_diagnostico = "Atención la persona se debe comunicar con salud ocupacional";
            } else {
                $alerta_diagnostico = "Diagnostico correcto";
            }

            $datos_invitacion["Ingreso"];
            $datos_invitacion["Salida"];
            $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDUsuario"];
            $datos_invitacion["TipoInvitacion"] = "Usuario";
            $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
            $datos_invitado["NumeroDocumento"] = $qryString;
            $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
            $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
            $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
            $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
            $Observaciones = $alerta_diagnostico;

            endif;
            //FIN BUSQUEDA USUARIO FUNCIONARIO

            //BUSQUEDA SOCIO o Empleado si esta como Socio
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" . (int) $qryString . "' or Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "' or AccionPadre = '" . $qryString . "') and IDEstadoSocio = 1 and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa    o una accion
                    //Consulto las placas de vehiculos de socios
                    $sql_invitacion = "Select * From Socio Where (Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "') and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . $IDClub . "'  and AccionPadre = ''";

                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $FotoSocio = $datos_invitacion["Foto"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
                $datos_invitacion["TipoInvitacion"] = "SocioClub";
                $datos_invitacion["PersonaAutoriza"] = "b";
                $datos_invitacion["FechaInicio"] = 'indefinida';
                $datos_invitacion["FechaFin"] = 'indedefinida';
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $datos_socio;
                $modulo = "Socio";
                $id_registro = $datos_invitacion["IDSocio"];

                $fecha_hoy = date("Y-m-d") . " 00:00:00";
                $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $id_registro . "' Group by IDusuario ";
                $r_unica = $dbo->query($sql_unica);
                $total_unica = $dbo->rows($r_unica);
                $row_resp_diag = $dbo->fetchArray($r_unica);
                $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                if ($total_unica <= 0) {
                    $alerta_diagnostico = "Atención la persona no ha llenado el diagnostico";
                } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                $alerta_diagnostico = "Atención! la persona se debe comunicar con salud ocupacional.";
            } else {
                $alerta_diagnostico = "Diagnostico correcto";
            }
            $Observaciones = $alerta_diagnostico;

            $resp = SIMWebService::get_reservas_socio($IDClub, $id_registro, $Limite, $IDReserva, $IDUsuario);
            $resp_como_invitado = SIMWebServiceApp::get_reservas_socio_invitado($IDClub, $id_registro, 0, "", $IDUsuario);
            foreach ($resp["response"] as $key => $value) {
                $FechaReserva = $value["Fecha"];
                $HoraReserva = $value["Hora"];
                $Servicio = $value["NombreServicio"];
                if ($FechaReserva == date("Y-m-d")) {
                    $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva;
                    $ObservacionsReservas .= "Hora reserva: " . $HoraReserva;
                    $ObservacionsReservas .= "Servicio: " . $Servicio;
                }
            }
            foreach ($resp_como_invitado["response"] as $key => $value) {
                $FechaReserva = $value["Fecha"];
                $HoraReserva = $value["Hora"];
                $Servicio = $value["NombreServicio"];
                if ($FechaReserva == date("Y-m-d")) {
                    $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva;
                    $ObservacionsReservas .= "Hora reserva: " . $HoraReserva;
                    $ObservacionsReservas .= "Servicio: " . $Servicio;
                }
            }
            $Observaciones .= $ObservacionsReservas;

            $Observaciones .= " ESTADO: " . $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = '" . $datos_invitacion["IDEstadoSocio"] . "'");

            //Consulto grupo Familiar
            if (empty($datos_socio["AccionPadre"]) || !empty($datos_socio["Accion"])): // Es Cabeza
                $nucleo_socio = 1;
                $condicion_nucleo = " and AccionPadre = '" . $datos_socio["Accion"] . "'";
                $datos_invitacion["CabezaInvitacion"] = "S";
                $response_nucleo = array();
                $sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' " . $condicion_nucleo;
                $result_grupo = $dbo->query($sql_grupo);
                while ($row_nucleo = $dbo->fetchArray($result_grupo)):
                    if (!empty($row_nucleo[Foto])) {
                        $foto_nucleo = SOCIO_ROOT . $row_nucleo[Foto];
                    } else {
                        $foto_nucleo = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $dato_nucleo["IDClub"] = $IDClub;
                    $dato_nucleo["IDInvitacion"] = $row_nucleo["IDSocio"];
                    $dato_nucleo["Nombre"] = $row_nucleo["Nombre"] . " " . $row_nucleo["Apellido"];
                    $dato_nucleo["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                    $dato_nucleo["Documento"] = $row_nucleo["NumeroDocumento"];
                    $dato_nucleo["Foto"] = $foto_nucleo;
                    $dato_nucleo["TipoInvitacion"] = "SocioClub";

                    //Tipos de ingresos
                    $response_tipo_n = array();
                    $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                    $array_tipo_n = explode(",", $TipoIngreso);
                    if (count($array_tipo_n) > 0) {
                        $encontrados = 1;
                        $message = count($array_tipo_n) . " Encontrados";
                        foreach ($array_tipo_n as $value) {
                            $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                            array_push($response_tipo_n, $tipo_ingreso_n);
                        }
                    }

                    if ($IDSocio > 0) {
                        $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                        $r_vehiculo = $dbo->query($sql_vehiculo);
                        if ($dbo->rows($r_vehiculo) > 0) {
                            $encontrados = 1;
                            while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                array_push($response_tipo_n, $tipo_ingreso_n);
                            }
                        }
                    }
                    $dato_nucleo["TipoIngreso"] = $response_tipo_n;
                    //Fin Tipo Ingreso

                    array_push($response_nucleo, $dato_nucleo);
                endwhile;
            endif;

            endif;
            //FIN BUSQUEDA SOCIO

            //Busco en autorizaciones de recogida d alumnos
            $array_autorizacion_recogida = self::buscar_autorizacion_recogida($IDClub, $Documento);

            if ($total_resultados <= 0 && count($array_autorizacion_recogida) <= 0) {
                $respuesta["message"] = "No encontrado!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                    //Para fontanar en observaciones agrego el predio del socio
                    if ($IDClub == 18 || $IDClub == 35) {
                        $Observaciones = "Se dirije a " . $datos_socio["Predio"];
                    }

                    $Observaciones .= " " . $datos_invitacion["ObservacionSocio"];
                    if ($autorizacion_invitacion == 1):
                        $datos_invitacion_individual = array();
                        $datos_invitacion_individual["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                        $datos_invitacion_individual["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                        $datos_invitacion_individual["FechaInicio"] = $datos_invitacion["FechaInicio"];
                        $datos_invitacion_individual["FechaFin"] = $datos_invitacion["FechaFin"];
                        $datos_invitacion_individual["Accion"] = $datos_socio["Accion"];
                        $datos_invitacion_individual["Socio"] = $datos_invitacion["PersonaAutoriza"];
                        $datos_invitacion_individual["TipoSocio"] = $datos_socio["TipoSocio"];
                        $datos_invitacion_individual["Observaciones"] = $Observaciones;
                        $datos_invitacion_individual["Ingreso"] = $datos_invitacion["Ingreso"];
                        $datos_invitacion_individual["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                        $datos_invitacion_individual["Salida"] = $datos_invitacion["Salida"];
                        $datos_invitacion_individual["FechaSalida"] = $datos_invitacion["FechaFin"];

                        if (!empty($datos_invitado[FotoFile])) {
                            $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                        } elseif (!empty($FotoSocio)) {
                        $foto = SOCIO_ROOT . $FotoSocio;
                    } else {
                        $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $datos_invitacion_individual["Foto"] = $foto;
                    $datos_invitacion_individual["NombreInvitado"] = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"];
                    $tipodoc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    if (empty($tipodoc)) {
                        $TipoDocumento = "Doc";
                    } else {
                        $TipoDocumento = $tipodoc;
                    }

                    $datos_invitacion_individual["TipoDocumentoInvitado"] = $TipoDocumento;
                    $datos_invitacion_individual["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];

                    //SI ES CABEZA CONUSLTO EL GRUPO FAMILIAR
                    $response_invitado_familia = array();
                    if ($datos_invitacion["CabezaInvitacion"] == "S"):
                        while ($datos_grupo_familiar = $dbo->fetchArray($result_grupo)):
                            $datos_invitado_familiar = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array");
                            if (!empty($datos_invitado_familiar[FotoFile])) {
                                $foto = INVITADO_ROOT . $datos_invitado_familiar[FotoFile];
                            } else {
                                $foto = URLROOT . "/images/sinfoto.png";
                            }

                            $dato_invitado_asociado["IDClub"] = $IDClub;
                            $dato_invitado_asociado["IDInvitacion"] = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                            $dato_invitado_asociado["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                            $dato_invitado_asociado["Nombre"] = $datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"];
                            $dato_invitado_asociado["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];

                            //Tipos de ingresos
                            $response_tipo_n = array();
                            $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                            $array_tipo_n = explode(",", $TipoIngreso);
                            if (count($array_tipo_n) > 0) {
                                $encontrados = 1;
                                $message = count($array_tipo_n) . " Encontrados";
                                foreach ($array_tipo_n as $value) {
                                    $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                                    array_push($response_tipo_n, $tipo_ingreso_n);
                                }
                            }

                            if ($IDSocio > 0) {
                                $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                                $r_vehiculo = $dbo->query($sql_vehiculo);
                                if ($dbo->rows($r_vehiculo) > 0) {
                                    $encontrados = 1;
                                    while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                        $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                        array_push($response_tipo_n, $tipo_ingreso_n);
                                    }
                                }
                            }
                            $dato_invitado_asociado["TipoIngreso"] = $response_tipo_n;
                            //Fin Tipo Ingreso

                            array_push($response_invitado_familia, $dato_invitado_asociado);
                        endwhile;
                    endif;

                    $datos_invitacion_individual["GrupoInvitado"] = $response_invitado_familia;

                    //Consulto el historial de ingresos y salidas del dia
                    $response_historial_acceso = array();
                    $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                    $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                    $sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_invitacion["IDInvitacion"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                    while ($row_historial = $dbo->fetchArray($sql_historial)):
                        $dato_historial["Tipo"] = $row_historial["Tipo"];
                        $dato_historial["Salida"] = $row_historial["Salida"];
                        $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                        $dato_historial["Entrada"] = $row_historial["Entrada"];
                        $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];

                        $datos_campos = "";
                        //Consulto los otros datos registrados
                        $array_otros_datos = explode("|", $row_historial["CamposAcceso"]);
                        foreach ($array_otros_datos as $valor) {
                            if (!empty($valor)) {
                                if (!in_array($valor, $array_respuesta)) {
                                    $datos_campos .= $valor . "<br>";
                                }
                                $array_respuesta[] = $valor;
                            }
                        }
                        $dato_historial["OtrosDatos"] = $datos_campos;
                        $dato_historial["LimiteSuperado"] = $row_historial["LimiteSuperado"];

                        array_push($response_historial_acceso, $dato_historial);
                    endwhile;
                    $datos_invitacion_individual["Historial"] = $response_historial_acceso;
                else:
                    $datos_invitacion_individual = null;
                endif;

                if ($nucleo_socio == 1):
                    $datos_invitacion_individual["GrupoInvitado"] = $response_nucleo;
                endif;

                //Tipos de ingresos
                $response_tipo = array();
                $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                $array_tipo = explode(",", $TipoIngreso);
                if (count($array_tipo) > 0) {
                    $encontrados = 1;
                    $message = count($array_tipo) . " Encontrados";
                    foreach ($array_tipo as $value) {
                        $tipo_ingreso["Nombre"] = utf8_encode($value);
                        array_push($response_tipo, $tipo_ingreso);
                    }
                }

                if ($IDSocio > 0) {
                    $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                    $r_vehiculo = $dbo->query($sql_vehiculo);
                    if ($dbo->rows($r_vehiculo) > 0) {
                        $encontrados = 1;
                        while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                            $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                            array_push($response_tipo, $tipo_ingreso);
                        }
                    }
                }
                $datos_invitacion_individual["TipoIngreso"] = $response_tipo;
                //Fin Tipo Ingreso

                $response["Invitacion"] = $datos_invitacion_individual;

                $response["AutorizacionRecogida"] = $array_autorizacion_recogida;

                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = "Ver2.1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    } //end function

    public function buscar_autorizacion_recogida($IDClub, $Documento)
    {

        $dbo = &SIMDB::get();

        //BUSQUEDA INVITADOS ACCESOS
        $qryString = str_replace(".", "", $Documento);
        $qryString = str_replace(",", "", $qryString);
        $qryString = str_replace("-", "", $qryString);

        if (ctype_digit($qryString)) {
            // si es solo numeros en un numero de documento
            $sql_invitacion = "Select SA.* From SocioAutorizacionSalida SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
            $modo_busqueda = "DOCUMENTO";
        } else {
            //seguramente es una placa
            //Consulto en invitaciones accesos
            $sql_invitacion = "Select SA.* From SocioAutorizacionSalida SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
            $modo_busqueda = "PLACA";
        }

        $result_invitacion = $dbo->query($sql_invitacion);
        $total_resultados = $dbo->rows($result_invitacion);

        if ($total_resultados > 0):

            $datos_invitacion = $dbo->fetchArray($result_invitacion);
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

            if (!empty($datos_invitado[FotoFile])) {
                $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            } else {
                $foto = URLROOT . "plataform/assets/images/sinfoto.png";
            }

            $datos_autorizado["IDSocioAutorizacionSalida"] = $datos_invitacion["IDSocioAutorizacionSalida"];
            $datos_autorizado["NombreAutorizado"] = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $datos_autorizado["TipoDocumentoAutorizado"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
            $datos_autorizado["DocumentoAutorizado"] = $datos_invitado["NumeroDocumento"];
            $datos_autorizado["FotoAutorizado"] = $foto;
            $datos_autorizado["FechaInicio"] = $datos_invitacion["FechaInicio"];
            $datos_autorizado["FechaFin"] = $datos_invitacion["FechaFin"];
            $datos_autorizado["AutorizadoPor"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $datos_socio["TipoSocio"] . ")");
            $datos_autorizado["TipoSocio"] = $datos_socio["TipoSocio"];
            $datos_autorizado["Observaciones"] = "";

            $array_doc_alumno = array();
            //Consulto los alumos que tiene autorizados para recogida
            $result_invitacion = $dbo->query($sql_invitacion);
            $contador_alumno = 0;
            while ($datos_autorizacion_estudiante = $dbo->fetchArray($result_invitacion)):
                //valido que la cedula no tenga autorizaciones sobre el mismo alumno para no repetirlo
                if (!in_array($datos_autorizacion_estudiante["IDSocioSalida"], $array_doc_alumno)):
                    //valido que el dia de hoy tenga permiso de recoger
                    $dia_hoy = date("w");
                    $array_dias_autorizados = explode("|", $datos_autorizacion_estudiante["Dias"]);
                    if (in_array($dia_hoy, $array_dias_autorizados)):
                        $datos_alumno = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_autorizacion_estudiante["IDSocioSalida"] . "' ", "array");

                        if (!empty($datos_alumno[Foto])) {
                            $foto_alumno = SOCIO_ROOT . $datos_alumno["Foto"];
                        } else {
                            $foto_alumno = URLROOT . "plataform/assets/images/sinfoto.png";
                        }

                        $array_alumno_recogida[$contador_alumno]["IDSocioAutorizacionSalida"] = $datos_autorizacion_estudiante["IDSocioAutorizacionSalida"];
                        $array_alumno_recogida[$contador_alumno]["TipoInvitacion"] = "AutorizacionSalida";
                        $array_alumno_recogida[$contador_alumno]["NombreAlumno"] = $datos_alumno["Nombre"] . " " . $datos_alumno["Apellido"];
                        $array_alumno_recogida[$contador_alumno]["CursoAlumno"] = $datos_alumno["Curso"];
                        $array_alumno_recogida[$contador_alumno]["ObservacionesAlumno"] = "Solo los dias creados";
                        $array_alumno_recogida[$contador_alumno]["TipoDocumentoAlumno"] = "Codigo";
                        $array_alumno_recogida[$contador_alumno]["DocumentoAlumno"] = $datos_alumno["NumeroDocumento"];
                        $array_alumno_recogida[$contador_alumno]["FotoAlumno"] = $foto_alumno . " " . $datos_alumno[FotoFile];
                        $array_alumno_recogida[$contador_alumno]["FechaInicio"] = $datos_autorizacion_estudiante["FechaInicio"];
                        $array_alumno_recogida[$contador_alumno]["FechaFin"] = $datos_autorizacion_estudiante["FechaFin"];

                        //Consulto el historal de ingresos y salidas del alumno
                        $response_historial_acceso = array();
                        $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                        $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                        $sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_autorizacion_estudiante["IDSocioAutorizacionSalida"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                        while ($row_historial = $dbo->fetchArray($sql_historial)):
                            $dato_historial["Tipo"] = $row_historial["Tipo"];
                            $dato_historial["Salida"] = $row_historial["Salida"];
                            $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                            $dato_historial["Entrada"] = $row_historial["Entrada"];
                            $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
                            array_push($response_historial_acceso, $dato_historial);
                        endwhile;
                        $array_alumno_recogida[$contador_alumno]["Historial"] = $response_historial_acceso;

                    endif;
                endif;
                $array_doc_alumno[] = $datos_autorizacion_estudiante["IDSocioSalida"];
                $contador_alumno++;
            endwhile;

            $datos_autorizado["AlumnosAutorizados"] = $array_alumno_recogida;

        else:
            $datos_autorizado = null;
        endif;

        return $datos_autorizado;
    }

    public function set_entrada_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo = "", $IDUsuario = "", $OtrosCampos = "")
    {
        $dbo = &SIMDB::get();

        //Guardo el Log de la busqueda
        $parametros = "INVITACION:" . $IDInvitacion . " TIPO: " . $TipoInvitacion . " Mecanismo: " . $Mecanismo;
        $sql_log_peticion = $dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('" . $IDClub . "','" . $IDUsuario . "','" . $parametros . "','Entrada',NOW())");

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $sql_ingreso = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW(), IDUsuarioIngreso = '" . $IDUsuario . "' Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "'");
                    //$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );
                    //if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
                    //$sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
                    //endif;
                    //Envio Notificacion Push
                    if ($IDClub != "9"): // Solo para mesa yeguas temporalmente no se registra
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);

                        //Verifico si alguien mas innvito a esta persona para enviarle también la notificación
                        $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                        $sql_otra_inv = "Select * From SocioInvitadoEspecial Where IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' and FechaInicio<=CURDATE() and FechaFin >= CURDATE() and IDSocioInvitadoEspecial <> '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'";
                        $result_otra_inv = $dbo->query($sql_otra_inv);
                        while ($row_otra_inv = $dbo->fetchArray($result_otra_inv)):
                            SIMUtil::push_socio_entrada($IDClub, $row_otra_inv["IDSocioInvitadoEspecial"], $TipoInvitacion, $IDUsuario);
                        endwhile;

                    endif;

                    break;
                case "Contratista":
                    $sql_ingreso = $dbo->query("Update SocioAutorizacion Set Ingreso = 'S', FechaIngreso = NOW(),IDUsuarioIngreso = '" . $IDUsuario . "' Where IDSocioAutorizacion = '" . $IDInvitacion . "'");
                    if ($IDClub == "18"): // Para Fontanar en algunas horas no se debe permitir el acceso
                        $flag_restriccion = 1;
                        $fecha_hora_actual = date("Y-m-d H:i:s");
                        if (date("N", strtotime($Fecha)) == "7"): //Domingo
                            $flag_restriccion = 1;
                        endif;
                        if (date("N", strtotime($Fecha)) == "6"): //Sabado
                            $hora_inicio_permitida = date("Y-m-d 08:00:00");
                            $hora_fin_permitida = date("Y-m-d 12:00:00");
                            if (strtotime($fecha_hora_actual) >= strtotime($hora_inicio_permitida) && strtotime($fecha_hora_actual) <= strtotime($hora_fin_permitida)):
                                $flag_restriccion = 0;
                            else:
                                $flag_restriccion = 1;
                            endif;

                        else: //Lunes - Viernes
                            $hora_inicio_permitida = date("Y-m-d 08:00:00");
                            //$hora_fin_permitida = date("Y-m-d 17:00:00");
                            $hora_fin_permitida = date("Y-m-d 23:00:00");
                            if (strtotime($fecha_hora_actual) >= strtotime($hora_inicio_permitida) && strtotime($fecha_hora_actual) <= strtotime($hora_fin_permitida)):
                                $flag_restriccion = 0;
                            else:
                                $flag_restriccion = 1;
                            endif;
                        endif;

                        if ($flag_restriccion == 1):
                            $respuesta["message"] = "Lo sentimos , no esta en el horario establecido para acceso de contratistas";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;

                    if ($IDClub != "9" && $IDClub != "37") // Solo para mesa yeguas y polo temporalmente no se registra
                    {
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
                case "Socio":
                    ///Se deja la PreSalida en blanco
                    $sql_ingreso = $dbo->query("Update Socio Set IDSocioPresalida = '', Presalida = 'N',FehaPresalida = '' Where IDSocio = '" . $IDInvitacion . "'");

                    //para el club del country cambiamos la cantidad de ausencias de ser así
                    if ($IDClub == 44) {
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDInvitacion . "' ", "array");

                        if ($datos_socio["SocioAusente"] == "S") {
                            $cantidadAusencias = $datos_socio["CantidadAusencias"];
                            $selectNucleo = "SELECT IDSocio FROM Socio WHERE AccionPadre = '" . $datos_socio['AccionPadre'] . "' AND IDClub = 44";
                            $queryNucleo = $dbo->query($selectNucleo);

                            while ($nucleo = $dbo->fetchArray($queryNucleo)) {
                                $cantidadAusencias += 1;
                                $sqlAusencias = "UPDATE Socio SET CantidadAusencias = '" . $cantidadAusencias . "' WHERE IDSocio = '" . $nucleo['IDSocio'] . "' AND IDClub = 44";
                                $qryActualiza = $dbo->query($sqlAusencias);

                                //Consulto el historial de entradas y salidas del dia
                                $sql_log_acceso = "Select * From LogAccesoDiario Where IDInvitacion = '" . $nucleo['IDSocio'] . "' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '" . date("Y-m-d") . "' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '" . date("Y-m-d") . "') Order by IDLogAcceso Desc";
                                $result_log_acceso = $dbo->query($sql_log_acceso);
                                while ($row_log_acceso = $dbo->fetchArray($result_log_acceso)) {
                                    if ($row_log_acceso["Entrada"] == 'S'):
                                        $cantidadAusencias -= 1;
                                        $sqlAusencias = "UPDATE Socio SET CantidadAusencias = '" . $cantidadAusencias . "' WHERE IDSocio = '" . $nucleo['IDSocio'] . "' AND IDClub = 44";
                                        $qryActualiza = $dbo->query($sqlAusencias);
                                        break;
                                    endif;
                                }
                            }

                        }

                    }
                    break;

                case "SocioInvitado":
                case "Invitado":
                    //No tiene proceso especifico solo se registra el log de acceso
                    $sql_ingreso = $dbo->query("Update SocioInvitado Set Estado = 'I', FechaIngresoClub = NOW() Where IDSocioInvitado = '" . $IDInvitacion . "'");
                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;

            }

            //Para Hacienda Fontanar muestro alerta si e=la persona ya se le había registrado acceso
            if ($IDClub == "18"):
                $sql_ultimo_registro = "Select * From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' and Tipo = '" . $TipoInvitacion . "' and FechaTrCr >= '" . date("Y-m-d 00:00:00") . "' and Entrada = 'S' Order by IDLogAcceso Desc Limit 1";
                $result_ultimo_registro = $dbo->query($sql_ultimo_registro);
                $row_ultimo_registro = $dbo->fetchArray($result_ultimo_registro);
                if ($row_ultimo_registro["Entrada"] == "S"):
                    $mensaje_alerta_ingreso = " ATENCION!! Esta persona ya tiene un ingreso el día de hoy: " . $row_ultimo_registro["FechaTrCr"];
                endif;
            endif;

            $OtrosCampos = str_replace("\\", "", $OtrosCampos);
            $datos_respuesta = json_decode($OtrosCampos, true);
            if (count($datos_respuesta) > 0):
                $LimiteSuperado = "N";

                foreach ($datos_respuesta as $detalle_respuesta):
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                        $Predio = $detalle_respuesta["Valor"];
                    }
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                        $Habitacion = $detalle_respuesta["Valor"];
                    }

                    $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                    if ($datos_pregunta["TipoCampo"] == "number" && $datos_pregunta["Limite"] > 0 && $detalle_respuesta["Valor"] > $datos_pregunta["Limite"]) {
                        //se envia notificacion de alerta al responsable
                        $datos_mensaje = $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"];
                        SIMUtil::notifica_alerta_acceso($IDClub, $IDInvitacion, $TipoInvitacion, $datos_mensaje);
                        $LimiteSuperado = "S";
                    }
                    $OtrosCamposAcceso .= "|" . $datos_pregunta["EtiquetaCampo"] . ":" . $detalle_respuesta["Valor"] . "|";
                    //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";
                endforeach;
            endif;

            //Registro el historial de accesos
            $sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Entrada, Mecanismo, FechaIngreso,FechaTrCr, IDUsuario, CamposAcceso,LimiteSuperado,Predio,Habitacion) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "',NOW(),NOW(),'" . $IDUsuario . "','" . $OtrosCamposAcceso . "','" . $LimiteSuperado . "','" . $Predio . "','" . $Habitacion . "')");
            $IDLogA = $dbo->lastID();
            foreach ($datos_respuesta as $detalle_respuesta) {

                if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                    $Predio = $detalle_respuesta["Valor"];
                }
                if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                    $Habitacion = $detalle_respuesta["Valor"];
                }

                $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                $sql_otros = "INSERT INTO AccesosOtrosDatos(IDClub,IDLogAcceso,IDPreguntaAcceso,IDInvitacion,Tipo,Movimiento,Valor,FechaTrCr)
												VALUES('" . $IDClub . "','" . $IDLogA . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Entrada','" . $detalle_respuesta["Valor"] . "',NOW())";
                $dbo->query($sql_otros);
            }

            //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";

            $sql_inserta_historial = $dbo->query("Insert Into LogAccesoDiario (IDInvitacion, IDClub, Tipo, Entrada, Mecanismo, FechaIngreso,FechaTrCr, IDUsuario) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "',NOW(),NOW(),'" . $IDUsuario . "')");

            $respuesta["message"] = "Ingreso registrado con exito." . $mensaje_alerta_ingreso;
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        } else {
            $respuesta["message"] = "7. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_salida_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo = "", $IDUsuario = "", $OtrosCampos = "")
    {
        $dbo = &SIMDB::get();

        //Guardo el Log de la busqueda
        $parametros = "INVITACION:" . $IDInvitacion . " TIPO: " . $TipoInvitacion . " Mecanismo: " . $Mecanismo;
        $sql_log_peticion = $dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('" . $IDClub . "','" . $IDUsuario . "','" . $parametros . "','Salida',NOW())");

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $sql_salida = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW(),IDUsuarioSalida = '" . $IDUsuario . "' Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "'");
                    $IDInvitadoSal = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                    /*
                    $sql_registramov="SELECT IDSocioInvitadoEspecial FROM SocioInvitadoEspecial WHERE IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    $r_resgitramov=$dbo->query($sql_registramov);
                    while($row_registramov=$dbo->fetchArray($r_resgitramov)){
                    $sql_inserta_historial_otro = "Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioInvitadoEspecial"]."','".$IDClub."', '".$TipoInvitacion."','S','".$Mecanismo."', NOW(),NOW(),'".$IDUsuario."')";
                    $dbo->query($sql_inserta_historial_otro);
                    }
                     */

                    //$sql_otras="Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW(), IDUsuarioSalida = '".$IDUsuario."' Where IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    //$dbo->query($sql_otras);
                    /*
                    $datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );
                    if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
                    $sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
                    endif;
                     */
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
                case "Contratista":
                    $sql_salida = $dbo->query("Update SocioAutorizacion Set Salida = 'S', FechaSalida = NOW(), IDUsuarioSalida = '" . $IDUsuario . "' Where IDSocioAutorizacion = '" . $IDInvitacion . "'");
                    $IDInvitadoSal = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                    /*
                    $sql_registramov="SELECT IDSocioAutorizacion FROM SocioAutorizacion WHERE IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    $r_resgitramov=$dbo->query($sql_registramov);
                    while($row_registramov=$dbo->fetchArray($r_resgitramov)){
                    $sql_inserta_historial_otro = "Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioAutorizacion"]."','".$IDClub."', '".$TipoInvitacion."','S','".$Mecanismo."', NOW(),NOW(),'".$IDUsuario."')";
                    $dbo->query($sql_inserta_historial_otro);
                    }
                     */
                    //$sql_otras="Update SocioAutorizacion Set Salida = 'S', FechaSalida = NOW(), IDUsuarioSalida = '".$IDUsuario."' Where IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    //$dbo->query($sql_otras);
                    //marcar las demas como salida

                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
                case "SocioInvitado":
                case "Invitado":
                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
            }

            $OtrosCampos = str_replace("\\", "", $OtrosCampos);
            $datos_respuesta = json_decode($OtrosCampos, true);
            if (count($datos_respuesta) > 0):
                $LimiteSuperado = "N";
                foreach ($datos_respuesta as $detalle_respuesta):
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                        $Predio = $detalle_respuesta["Valor"];
                    }
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                        $Habitacion = $detalle_respuesta["Valor"];
                    }
                    $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                    if ($datos_pregunta["TipoCampo"] == "number" && $datos_pregunta["Limite"] > 0 && $detalle_respuesta["Valor"] > $datos_pregunta["Limite"]) {
                        //se envia notificacion de alerta al responsable
                        $datos_mensaje = $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"];
                        SIMUtil::notifica_alerta_acceso($IDClub, $IDInvitacion, $TipoInvitacion, $datos_mensaje);
                        $LimiteSuperado = "S";
                    }
                    $OtrosCamposAcceso .= "|" . $datos_pregunta["EtiquetaCampo"] . ":" . $detalle_respuesta["Valor"] . "|";
                    //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";
                endforeach;
            endif;

            //Registro el historial de accesos
            $sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario,CamposAcceso,LimiteSuperado,Predio,Habitacion) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "', NOW(),NOW(),'" . $IDUsuario . "','" . $OtrosCamposAcceso . "','" . $LimiteSuperado . "','" . $Predio . "','" . $Habitacion . "')");
            $IDLogA = $dbo->lastID();
            foreach ($datos_respuesta as $detalle_respuesta) {
                $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                $sql_otros = "INSERT INTO AccesosOtrosDatos(IDClub,IDLogAcceso,IDPreguntaAcceso,IDInvitacion,Tipo,Movimiento,Valor,FechaTrCr)
												VALUES('" . $IDClub . "','" . $IDLogA . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Salida','" . $detalle_respuesta["Valor"] . "',NOW())";
                $dbo->query($sql_otros);
            }

            $sql_inserta_historial = $dbo->query("Insert Into LogAccesoDiario (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "', NOW(),NOW(),'" . $IDUsuario . "')");

            $respuesta["message"] = "Salida registrada con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "7. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function verifica_sancion_socio($IDClub, $IDSocio, $IDServicio, $FechaReserva)
    {
        $dbo = &SIMDB::get();
        $sancion_vigente = false;
        $reserva_no_cumplida = 0;
        $reserva_anterior = "";
        $contador_reserva_no_cumplida_seguida = 0;
        $contador_reserva_parcial = 0;
        $numero_dia_sancion = 0;
        $contador_reserva_socio = 0;
        $contador_reserva_parcial_seguida = 0;
        $FechaUltimaNoCumplida = "";
        $FechaUltimaNoCumplidaP = "";
        if (!empty($IDSocio) && !empty($IDClub) && !empty($IDServicio)) {

            $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
            //Consulto si hay sanciones publicadas ene l club
            $sql_sancion = "Select * From Sancion Where IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $IDServicioMaestro . "' and Publicar = 'S'";
            $result_sancion = $dbo->query($sql_sancion);
            $total_sanciones = $dbo->rows($result_sancion);
            if ($total_sanciones > 0):

                $array_socio[] = $IDSocio;

                // PARA EL CAMPESTRE PEREIRA SE DEBE VALIDAR TODO EL NUCLEO FAMILIAR
                if($IDClub == 15):

                    $sqlSocio =  "SELECT Accion, IDSocio FROM Socio WHERE IDSocio = '".$IDSocio."' AND IDClub = '".$IDClub."'";
                    $qrySocio = $dbo->query($sqlSocio);
                    $datos_socio = $dbo->fetchArray($qrySocio);

                    $accion_socio = $datos_socio["Accion"];

                    $sql_nucleo = "Select IDSocio From Socio Where Accion = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                    $result_nucleo = $dbo->query( $sql_nucleo );
                    while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
                        $array_socio[] = $row_nucleo[ "IDSocio" ];
                    endwhile;

                endif;

                if ( count( $array_socio ) > 0 ):
                    $id_socio_nucleo = implode( ",", $array_socio );
                endif;

                $sql_reserva_socio = "Select IDReservaGeneral, Cumplida, Fecha
                                        From ReservaGeneral
                                        Where IDSocio in ('" . $id_socio_nucleo . "') and
                                        IDServicio = '" . $IDServicio . "' and
                                        IDEstadoReserva = 1 and CumplidaCabeza = 'N'
                                        Order By Fecha Desc Limit 10";

                $result_reserva_socio = $dbo->query($sql_reserva_socio);

                while ($row_reserva_socio = $dbo->fetchArray($result_reserva_socio)):

                    if ($reserva_anterior == "" && $row_reserva_socio["Cumplida"] == "N") {
                        $reserva_anterior = "N";
                    }

                    if ($row_reserva_socio["Cumplida"] == "N"):
                        $contador_reserva_no_cumplida++;
                        if (empty($FechaUltimaNoCumplida)) {
                            $FechaUltimaNoCumplida = $row_reserva_socio["Fecha"];
                        }

                    endif;

                    if ($row_reserva_socio["Cumplida"] == "P"):
                        $contador_reserva_parcial++;
                        if (empty($FechaUltimaNoCumplida)) {
                            $FechaUltimaNoCumplidaP = $row_reserva_socio["Fecha"];
                        }

                    endif;

                    if (($reserva_anterior == "N" || $reserva_anterior == "P") && $row_reserva_socio["Cumplida"] == "N"):
                        $contador_reserva_no_cumplida_seguida++;
                    endif;

                    if (($reserva_anterior == "N" || $reserva_anterior == "P") && $row_reserva_socio["Cumplida"] == "P"):
                        $contador_reserva_parcial_seguida++;
                    endif;

                    $reserva_anterior = $row_reserva_socio["Cumplida"];

                    if ($contador_reserva_socio == 0):
                        $fecha_ultima_reserva = $row_reserva_socio["Fecha"];
                    endif;
                    $contador_reserva_socio++;
                endwhile;

                if (($contador_reserva_no_cumplida_seguida) > 0) {
                    $contador_reserva_no_cumplida_seguida++;
                }

                if (($contador_reserva_parcial_seguida) > 0) {
                    $contador_reserva_parcial_seguida++;
                }

                /*
                echo "<br>RESE " . $contador_reserva_socio;
                echo "<br>SEG " . $contador_reserva_no_cumplida_seguida;
                echo "<br>NO CUM " . $contador_reserva_no_cumplida;
                echo "<br>Parcial " . $contador_reserva_parcial;
                echo "<br>Parcial Seguida " . $contador_reserva_parcial_seguida;
                 */

                while ($row_sancion = $dbo->fetchArray($result_sancion)):
                    //Consulto solo si se ha encontrado una sancion
                    if ($numero_dia_sancion == 0):
                        if ($row_sancion["Cumplida"] == "N"):
                            if ($row_sancion["Seguida"] == "S"):
                                if ($contador_reserva_no_cumplida_seguida >= $row_sancion["NumeroIncumplida"]):
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            elseif ($row_sancion["Seguida"] == "N"):
                                if ($contador_reserva_no_cumplida >= $row_sancion["NumeroIncumplida"]):
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            endif;
                        elseif ($row_sancion["Cumplida"] == "P"):
                            if ($row_sancion["Seguida"] == "S"):
                                if ($contador_reserva_parcial_seguida >= $row_sancion["NumeroIncumplida"]):
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            elseif ($row_sancion["Seguida"] == "N"):
                                if ($contador_reserva_parcial >= $row_sancion["NumeroIncumplida"]):
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            endif;

                        endif;
                    endif;
                endwhile;

                //Si se encontro numero de dias en sanciones consulto si ya la cumplio
                if ($numero_dia_sancion > 0):
                    //sumo los dias de sancion a la fecha de ultima reserva registrada
                    $fecha_hoy = date("Y-m-j");
                    $fecha_hoy = $FechaReserva;
                    $fecha_actual = $fecha_ultima_reserva;
                    if (empty($FechaUltimaNoCumplida)) {
                        $FechaUltimaNoCumplida = $FechaUltimaNoCumplidaP;
                    }

                    $fecha_final_sancion = strtotime('+' . (int) $numero_dia_sancion . ' day', strtotime($FechaUltimaNoCumplida));
                    $fecha_final_sancion = date('Y-m-j', $fecha_final_sancion);
                    if (strtotime($fecha_hoy) <= strtotime($fecha_final_sancion)):
                        $sancion_vigente = true;
                        //echo "<br>ID SANC  " . $numero_dia_sancion;
                    endif;
                endif;
            endif;
        }

        return $sancion_vigente;

    }

    public function validar_disponibilidad_auxiliar($IDAuxiliar, $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub)
    {
        $dbo = &SIMDB::get();
        $disponible = "";
        $fecha_hora_solicitud = strtotime($Fecha . " " . $Hora);
        //Selecciono los auxiliares que tengan el mismo numero de documento, ya que un auiliar puede estar en mas servicios por ejemplo masajista hombre y mujer
        $documento_auxiliar = $dbo->getFields("Auxiliar", "NumeroDocumento", "IDAuxiliar = '" . $IDAuxiliar . "'");
        $sql_auxiliar = "Select * From Auxiliar Where NumeroDocumento = '" . $documento_auxiliar . "'";
        $result_auxiliar = $dbo->query($sql_auxiliar);
        while ($row_auxiliar = $dbo->fetchArray($result_auxiliar)):
            $array_id_auxiliar[] = $row_auxiliar["IDAuxiliar"];
        endwhile;
        if (count($array_id_auxiliar) > 0):
            $id_auxiliar_doc = implode(",", $array_id_auxiliar);
        endif;
        //Consulto que el auxiliar no este reservado en otro servicio en la misma fecha y hora con un lapso de 2 horas
        $sql_reserva_aux = "Select * From ReservaGeneral Where IDAuxiliar in (" . $id_auxiliar_doc . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva  = 1)";
        $result_reserva_aux = $dbo->query($sql_reserva_aux);
        while ($row_reserva_aux = $dbo->fetchArray($result_reserva_aux)):
            //consulto el intervalo de la disponibilidad
            $intervalo = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_reserva_aux["IDDisponibilidad"] . "'");
            $fecha_hora_inicial = strtotime($row_reserva_aux["Fecha"] . " " . $row_reserva_aux["Hora"]);
            $fecha_hora_final = strtotime('+' . $intervalo . ' minute', $fecha_hora_inicial);

            //echo "<br>Solicitud: " . date("Y-m-d H:i:s",$fecha_hora_solicitud);
            //echo "<br>Inicial: " . date("Y-m-d H:i:s",$fecha_hora_inicial);
            //echo "<br>Final: " . date("Y-m-d H:i:s",$fecha_hora_final);
            if ($fecha_hora_solicitud >= $fecha_hora_inicial && $fecha_hora_solicitud <= $fecha_hora_final):
                $disponible = "N";
                break;
            endif;
        endwhile;
        //echo "Dispo " . $disponible;
        //exit;
        return $disponible;
    }

    public function get_reservas_socio_invitado($IDClub, $IDSocio, $Limite = 0, $IDReserva = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        $array_id_consulta[] = $IDSocio;

        if (!empty($IDReserva)) {
            $condicion_reserva = " and RG.IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        //Selecciono las reservas donde el socio esta como invitado
        $sql = "SELECT * FROM ReservaGeneral RG,ReservaGeneralInvitado RGI WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and RGI.IDSocio = '" . $IDSocio . "' and RG.IDClub = '" . $IDClub . "' and RG.IDEstadoReserva = 1 and RG.Fecha >= CURDATE() " . $where_beneficiario . " " . $condicion_reserva . "ORDER BY RG.Fecha Desc  " . $condicion_limite;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {

            $message = $dbo->rows($qry) . " Encontrados";

            while ($row_reserva = $dbo->fetchArray($qry)):

                $mostra_reserva = 1;
                $fecha_hoy = date("Y-m-d");
                if ($row_reserva["Fecha"] == $fecha_hoy && $row_reserva["Hora"] <= date("H:i:s") && empty($IDUsuario)) {
                    $mostra_reserva = 0;
                    if ($dbo->rows($qry) == 1) {
                        $reserva["IDClub"] = "";
                        $reserva["IDSocio"] = "";
                        $reserva["IDReserva"] = "";
                        $reserva["IDServicio"] = "";
                        $id_servicio_maestro = "";
                        $reserva["NombreServicio"] = "";
                        $reserva["IDElemento"] = "";
                        $reserva["NombreElemento"] = "";
                        $reserva["Fecha"] = "";
                        $reserva["Tee"] = "";
                        array_push($response, $reserva);
                        $respuesta["message"] = "No tienes reservas programadas.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                        return $respuesta;
                    }

                }

                //$mostra_reserva=1;

                if ($mostra_reserva == 1) {

                    // Verifico si es una reserva asociada para no mostrarla en el resultado
                    $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                    $qry_auto = $dbo->query($sql_auto);
                    if ($dbo->rows($qry_auto) <= 0) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");

                        $reserva["IDClub"] = $IDClub;
                        $reserva["IDSocio"] = $IDSocio;
                        $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                        $reserva["IDServicio"] = $row_reserva["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");

                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        }

                        $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'");
                        $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                        $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                        $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                        $reserva["Fecha"] = $row_reserva["Fecha"];
                        $reserva["Tee"] = $row_reserva["Tee"];
                        $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                        $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                        $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                        $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                        $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                        $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                        $reserva["AdicionalesObligatorio"] = $datos_servicio["AdicionalesObligatorio"];
                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                        if (!empty($row_reserva["IDAuxiliar"])):
                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["Auxiliar"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                        else:
                            unset($reserva['IDAuxiliar']);
                            unset($reserva['Auxiliar']);
                            unset($reserva['TipoAuxiliar']);
                        endif;

                        if (!empty($row_reserva["IDTipoModalidadEsqui"])):
                            $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                            $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                        else:
                            unset($reserva['IDTipoModalidad']);
                            unset($reserva['Modalidad']);
                        endif;

                        if (strlen($row_reserva["Hora"]) != 8):
                            $row_reserva["Hora"] .= ":00";
                        endif;

                        $reserva["Hora"] = $row_reserva["Hora"];

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        if ($row_reserva["IDDisponibilidad"] <= 0):
                            $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "'");
                        else:
                            $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                        endif;

                        $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)):
                            $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else:
                            $reserva["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)):
                            $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else:
                            $reserva["NumeroInvitadoExterno"] = "";
                        endif;

                        if ($row_reserva["IDInvitadoBeneficiario"] > 0):
                            $reserva["Beneficiario"] = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                        endif;
                        if ($row_reserva["IDSocioBeneficiario"] > 0):
                            $reserva["Beneficiario"] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                        endif;

                        //Invitados Reserva
                        $response_invitados_reserva = array();
                        $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $total_invitado = $dbo->rows($sql_invitados_reserva);
                        while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)):
                            $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];

                            if ($r_invitados_reserva["IDSocio"] == $IDSocio) {
                                $IDInvitacionEliminar = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            }

                            $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                            $invitado_reserva[NombreExterno] = strtoupper($r_invitados_reserva["Nombre"]);
                            if ($r_invitados_reserva["IDSocio"] == 0):
                                $tipo_invitado = "Externo";
                            else:
                                $tipo_invitado = "Socio";
                            endif;

                            $invitado_reserva[TipoInvitado] = $tipo_invitado;

                            array_push($response_invitados_reserva, $invitado_reserva);
                        endwhile;

                        /*
                        //Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
                        if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
                        //Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
                        if ($id_disponibilidad>0):
                        $minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        endif;

                        if($total_invitado<$minimo_invitado_reserva):
                        $invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
                        $invitado_reserva[IDSocio]=$IDSocio;
                        $invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));
                        $tipo_invitado = "Socio";
                        $invitado_reserva[TipoInvitado]=$tipo_invitado;
                        array_push($response_invitados_reserva, $invitado_reserva);
                        endif;
                        endif;
                         */

                        $reserva["Invitados"] = $response_invitados_reserva;

                        //Reservas asociadas
                        $response_reserva_asociada = array();
                        $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                        foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva):
                            array_push($response_reserva_asociada, $datos_reserva);
                        endforeach;
                        $reserva["ReservaAsociada"] = $response_reserva_asociada;
                        $reserva["IDReservaGeneralInvitado"] = $IDInvitacionEliminar;
                        //Reservada por
                        $id_socio_reserva = $dbo->getFields("ReservaGeneral", "IDSocio", "IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $reserva["InvitadoPor"] = utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $id_socio_reserva . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $id_socio_reserva . "'")));

                        //Adicionales
                        $response_adicionales = array();
                        $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
																FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
																WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
																			 IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $IDInvitacionEliminar . "'
																ORDER BY SP.Nombre";
                        $r_carac = $dbo->query($sql_carac);
                        while ($row_carac = $dbo->FetchArray($r_carac)) {

                            $adicionales["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                            $adicionales["EtiquetaCampo"] = $row_carac["Caracteristica"];
                            $adicionales["TipoCampo"] = $row_carac["TipoCampo"];
                            $adicionales["Valores"] = $row_carac["Valores"];
                            $adicionales["ValoresID"] = $row_carac["Valor"];
                            $adicionales["Total"] = $row_carac["Total"];
                            array_push($response_adicionales, $adicionales);
                        }

                        $reserva["Adicionales"] = $response_adicionales;
                        //Fin Adicionales

                        array_push($response, $reserva);
                    }
                } // fin verificar si fue un areserva automatica
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No tienes reservas programadas.";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function get_producto($IDClub, $IDProducto = "", $Tag = "", $Version = "")
    {

            $dbo = &SIMDB::get();

            // Seccion Especifica
            if (!empty($IDProducto)):
                $array_condiciones[] = " IDProducto  = '" . $IDProducto . "'";
            endif;

            // Tag
            if (!empty($Tag)):
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_producto = " and " . $condiciones;
            endif;

            $response = array();
            $response_lista_producto = array();
            $sql = "SELECT * FROM Producto" . $Version . " WHERE Publicar = 'S' and Existencias > 0  and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Nombre ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";

                $mostrar_fecha = $dbo->getFields("Club", "SolicitaFechaDomicilio", "IDClub = '" . $IDClub . "'");
                if ($mostrar_fecha == "S"):
                    $producto["setearfecha"] = true;
                else:
                    $producto["setearfecha"] = false;
                endif;

                $mostrar_hora = $dbo->getFields("Club", "SolicitaHoraDomicilio", "IDClub = '" . $IDClub . "'");
                if ($mostrar_hora == "S"):
                    $producto["setearhora"] = true;
                else:
                    $producto["setearhora"] = false;
                endif;

                $mostrar_direccion = $dbo->getFields("Club", "SolicitaDireccionDomicilio", "IDClub = '" . $IDClub . "'");
                if ($mostrar_direccion == "S"):
                    $producto["seteardireccion"] = true;
                else:
                    $producto["seteardireccion"] = false;
                endif;

                while ($r = $dbo->fetchArray($qry)) {
                    $lista_producto["IDClub"] = $r["IDClub"];
                    $lista_producto["IDProducto"] = $r["IDProducto"];
                    $lista_producto["Nombre"] = utf8_encode($r["Nombre"]);
                    $lista_producto["Descripcion"] = utf8_encode($r["Descripcion"]);
                    $lista_producto["Precio"] = $r["Precio"];
                    $lista_producto["PermiteComentarios"] = $r["PermiteComentarios"];

                    if (!empty($r["Foto1"])):
                        if (strstr(strtolower($r["Foto1"]), "http://")) {
                            $foto = $r["FotoDestacada"];
                        } else {
                            $foto = IMGPRODUCTO_ROOT . $r["Foto1"];
                        }

                        //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                    else :
                        $foto = "";
                    endif;

                    $lista_producto["Foto"] = $foto;
                    array_push($response_lista_producto, $lista_producto);

                } //ednw hile

                $producto["Productos"] = $response_lista_producto;
                array_push($response, $producto);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_producto_categoria($IDClub, $IDCategoria = "", $Tag = "", $Version = "", $IDRestaurante = "")
    {

            $dbo = &SIMDB::get();

            $respuesta_dispo = self::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha = "", $Version, $IDRestaurante);
            if (!$respuesta_dispo["success"]) {
                $respuesta["message"] = $respuesta_dispo["message"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            // Seccion Especifica
            if (!empty($IDCategoria)):
                $array_condiciones[] = " IDCategoriaProducto  = '" . $IDCategoria . "'";
            endif;

            // Tag
            if (!empty($Tag)):
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (!empty($IDRestaurante)):
                $array_condiciones[] = " (IDRestauranteDomicilio  = '" . $IDRestaurante . "' or IDRestauranteDomicilio = '')";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_producto = " and " . $condiciones;
            endif;

            $response = array();
            $response_lista_producto = array();
            $sql = "SELECT * FROM CategoriaProducto" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Orden ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";

                //$mostrar_fecha = $dbo->getFields( "Club" , "SolicitaFechaDomicilio" , "IDClub = '".$IDClub."'" );
                $mostrar_fecha = $respuesta_dispo["response"][0]["SolicitaFechaDomicilio"];
                if ($mostrar_fecha == "S"):
                    $categoria_producto["setearfecha"] = true;
                else:
                    $categoria_producto["setearfecha"] = false;
                endif;

                //$mostrar_hora = $dbo->getFields( "Club" , "SolicitaHoraDomicilio" , "IDClub = '".$IDClub."'" );
                $mostrar_hora = $respuesta_dispo["response"][0]["SolicitaHoraDomicilio"];
                if ($mostrar_hora == "S"):
                    $categoria_producto["setearhora"] = true;
                else:
                    $categoria_producto["setearhora"] = false;
                endif;

                while ($r = $dbo->fetchArray($qry)) {
                    $categoria_producto["IDClub"] = $r["IDClub"];
                    $categoria_producto["IDCategoriaProducto"] = $r["IDCategoriaProducto"];

                    /*
                    if(!empty(trim($r["ComentarioCategoria"]))):
                    $descripcion_cat = " (".$r["ComentarioCategoria"].")";
                    else:
                    $descripcion_cat="";
                    endif;
                     */

                    $categoria_producto["NombreCategoria"] = $r["Nombre"] . $descripcion_cat;
                    $categoria_producto["DescripcionCategoria"] = $r["Descripcion"];
                    $categoria_producto["ComentarioCategoria"] = $r["ComentarioCategoria"];
                    //Busco los productos de la categoria
                    $response_detalle_producto = array();
                    $sql_productos = "Select PC.* From ProductoCategoria" . $Version . " PC, Producto" . $Version . " P Where P.IDProducto=PC.IDProducto and  IDCategoriaProducto = '" . $r["IDCategoriaProducto"] . "' Order by P.Orden";
                    $result_productos = $dbo->query($sql_productos);
                    while ($row_producto = $dbo->fetchArray($result_productos)):

                        $datos_producto = $dbo->fetchAll("Producto" . $Version, " IDProducto = '" . $row_producto["IDProducto"] . "' ", "array");
                        if ($datos_producto["Publicar"] == "S" && $datos_producto["Existencias"] > 0):
                            $producto["IDProducto"] = $datos_producto["IDProducto"];
                            $producto["Nombre"] = $datos_producto["Nombre"];
                            $producto["Descripcion"] = $datos_producto["Descripcion"];
                            $producto["Precio"] = $datos_producto["Precio"];
                            $producto["Nombre"] = $datos_producto["Nombre"];
                            $producto["PermiteComentarios"] = $datos_producto["PermiteComentarios"];
                            if (!empty($datos_producto["Foto1"])):
                                if (strstr(strtolower($datos_producto["Foto1"]), "http://")) {
                                    $foto = $datos_producto["FotoDestacada"];
                                } else {
                                    $foto = IMGPRODUCTO_ROOT . $datos_producto["Foto1"];
                                }

                                //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                            else :
                                $foto = "";
                            endif;
                            $producto["Foto"] = $foto;

                            //Caracteristicas
                            $sql_producto_carac = "SELECT PP.IDPropiedadProducto,PP.Nombre as Categoria, PP.Tipo, PP.Obligatorio, PP.MaximoPermitido,CP.Nombre as NombreValor, CP.Valor as Precio, CP.IDCaracteristicaProducto
																						 FROM ProductoCaracteristica PC, CaracteristicaProducto CP, PropiedadProducto PP
																						 WHERE PC.IDCaracteristicaProducto=CP.IDCaracteristicaProducto And
																									 CP.IDPropiedadProducto = PP.IDPropiedadProducto And
																									 PC.IDProducto = '" . $datos_producto["IDProducto"] . "'";
                            $result_prod_carac = $dbo->query($sql_producto_carac);
                            $Nombre_cat = "";
                            $contador_cat = 0;
                            $response_carac_producto = array();
                            $response_valores_carac = array();

                            while ($row_prod_carac = $dbo->fetchArray($result_prod_carac)) {
                                if ($Nombre_cat != $row_prod_carac["Categoria"]) {
                                    $Nombre_cat = $row_prod_carac["Categoria"];
                                    if ($contador_cat > 0) {
                                        array_push($response_carac_producto, $categoria_carac);
                                        $response_valores_carac = array();
                                    }

                                    $categoria_carac["IDCaracteristica"] = $row_prod_carac["IDPropiedadProducto"];
                                    $categoria_carac["TipoCampo"] = $row_prod_carac["Tipo"];
                                    $categoria_carac["EtiquetaCampo"] = $row_prod_carac["Categoria"];
                                    $categoria_carac["Obligatorio"] = $row_prod_carac["Obligatorio"];
                                    $categoria_carac["CantidadMaximaSeleccion"] = $row_prod_carac["MaximoPermitido"];

                                }

                                $valores["IDCaracteristicaValor"] = $row_prod_carac["IDCaracteristicaProducto"];
                                $valores["Opcion"] = $row_prod_carac["NombreValor"];
                                $valores["Precio"] = $row_prod_carac["Precio"];
                                array_push($response_valores_carac, $valores);
                                $categoria_carac["Valores"] = $response_valores_carac;
                                $contador_cat++;

                            }
                            if (count($response_valores_carac) > 0) {
                                array_push($response_carac_producto, $categoria_carac);
                            }
                            $producto["Caracteristicas"] = $response_carac_producto;

                            //FIN caracteristicas

                            array_push($response_detalle_producto, $producto);
                        endif;
                    endwhile;

                    $categoria_producto["Productos"] = $response_detalle_producto;

                    array_push($response, $categoria_producto);

                } //ednw hile

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_domicilio_socio($IDClub, $IDSocio, $Version = "", $IDRestaurante)
    {

            $dbo = &SIMDB::get();

            if (!empty($IDRestaurante)) {
                $condicion = " and IDRestauranteDomicilio='" . $IDRestaurante . "'";
            }

            if($IDClub == 70)
            {
                $fecha = date("Y-m-d");

                $year = date('Y', strtotime($fecha));
                $mes = date('m', strtotime($fecha));

                $condicion .= " AND MONTH(HoraEntrega) = '".$mes."' AND YEAR(HoraEntrega) = '".$year."'";
            }

            $response = array();
            $response_detalle_domicilio = array();
            $sql = "SELECT * FROM Domicilio" . $Version . " WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'" . " and IDEstadoDomicilio <> 3 " . $condicion . " ORDER BY FechaTrCr Desc Limit 3";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $domicilio["IDClub"] = $r["IDClub"];
                    $domicilio["IDDomicilio"] = $r["IDDomicilio"];
                    $domicilio["Estado"] = utf8_encode($dbo->getFields("EstadoDomicilio" . $Version, "Nombre", "IDEstadoDomicilio = '" . $r["IDEstadoDomicilio"] . "'"));
                    $domicilio["Numero"] = $r["Numero"];
                    $domicilio["Total"] = (float) $r["Total"] + (int) $r["ValorDomicilio"];
                    $domicilio["HoraEntrega"] = $r["HoraEntrega"];
                    $domicilio["ComentariosSocio"] = utf8_encode($r["ComentariosSocio"]);
                    $domicilio["ComentariosClub"] = utf8_encode($r["ComentariosClub"]);
                    $domicilio["Fecha"] = $r["FechaTrCr"];
                    //Consulto los productos pedidos
                    $detalle_pedido = self::get_domicilio_detalle($IDClub, $r["IDDomicilio"], $r["IDSocio"], (int) $r["ValorDomicilio"], $Version);
                    $domicilio["Productos"] = $detalle_pedido["response"];

                    if ($IDClub != 25 && $r["IDEstadoDomicilio"] != 8) {
                        array_push($response, $domicilio);
                    }

                    if ($IDClub == 25 && $r["IDEstadoDomicilio"] == 8) {
                        $respuesta["message"] = "No se encontraron registros";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio, $ValorDomicilio = "", $Version = "")
    {

            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM DomicilioDetalle" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $id_domiclio = $r["IDDomicilio"];
                    $domicilio_detalle["IDDomicilio"] = $r["IDDomicilio"];
                    $domicilio_detalle["IDProducto"] = $r["IDProducto"];
                    $domicilio_detalle["Producto"] = utf8_encode($dbo->getFields("Producto" . $Version, "Nombre", "IDProducto = '" . $r["IDProducto"] . "'"));
                    $domicilio_detalle["Comentario"] = utf8_encode($r["Comentario"]);

                    $foto_prod = $dbo->getFields("Producto" . $Version, "Foto1", "IDProducto = '" . $r["IDProducto"] . "'");
                    if (!empty($foto_prod)):
                        if (strstr(strtolower($foto), "http://")) {
                            $foto = $foto_prod;
                        } else {
                            $foto = IMGPRODUCTO_ROOT . $foto_prod;
                        }

                        //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                    else :
                        $foto = "";
                    endif;

                    $domicilio_detalle["FotoProducto"] = $foto;
                    $domicilio_detalle["Cantidad"] = $r["Cantidad"];
                    $domicilio_detalle["ValorUnitario"] = $r["ValorUnitario"];
                    $domicilio_detalle["Total"] = $r["Total"];

                    $sql_carac = "SELECT DC.*,PP.Nombre as Categoria, CP.Nombre as Caracteristica
											FROM DomicilioCaracteristica DC, CaracteristicaProducto CP, PropiedadProducto PP
											WHERE  DC.IDCaracteristicaProducto = CP.IDCaracteristicaProducto and PP.IDPropiedadProducto = DC.IDPropiedadProducto and
											IDDomicilio = '" . $IDDomicilio . "' and IDProducto = '" . $r["IDProducto"] . "'
											ORDER BY PP.Nombre";
                    $r_carac = $dbo->query($sql_carac);
                    while ($row_carac = $dbo->FetchArray($r_carac)) {
                        $caracteristicas .= $row_carac["Categoria"] . " : " . $row_carac["Caracteristica"];
                    }

                    $domicilio_detalle["Categorias"] = $caracteristicas;

                    array_push($response, $domicilio_detalle);

                } //ednw hile

                if ((int) $ValorDomicilio > 0):
                    $domicilio_detalle["IDDomicilio"] = $id_domiclio;
                    $domicilio_detalle["IDProducto"] = "0";
                    $domicilio_detalle["Producto"] = "Domicilio";
                    $domicilio_detalle["FotoProducto"] = "";
                    $domicilio_detalle["Cantidad"] = "1";
                    $domicilio_detalle["ValorUnitario"] = $ValorDomicilio;
                    $domicilio_detalle["Total"] = $ValorDomicilio;
                    array_push($response, $domicilio_detalle);

                endif;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion = "", $ValorDomicilio = "", $FormaPago = "", $Version = "", $IDRestaurante, $NumeroMesa = "", $CamposFormulario = "", $Propina = "")
    {

            $dbo = &SIMDB::get();

            $id_socio_club = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            if ((int) $id_socio_club <= 0) {
                $IDSocio = "";
            }

            $resp_config = self::get_configuracion_domicilio($IDClub, $IDSocio, "", $Version);
            $PedidoMinimo = (int) $resp_config["response"][0]["PedidoMinimo"];
            $porcentaje_propina = (float) $resp_config["response"][0]["PorcentajePropina"];

            $DetallePedido = trim(preg_replace('/\s+/', ' ', $DetallePedido));
            $datos_pedido = json_decode($DetallePedido, true);

            if (!empty($IDSocio) && !empty($HoraEntrega) && count($datos_pedido) > 0) {

                $hoy = date("Y-m-d");

                $GranTotal = 0;
                foreach ($datos_pedido as $detalle_datos):

                    $GranTotal += (int) $detalle_datos["Cantidad"] * $detalle_datos["ValorUnitario"];
                    //verifico que hayan en existencias
                    $datos_prod = $dbo->fetchAll("Producto" . $Version, " IDProducto = '" . $detalle_datos["IDProducto"] . "' ", "array");

                    if ($IDClub == 8 && $detalle_datos["Cantidad"] > 2) {
                        $respuesta["message"] = "Solo se permiten maximo 2 unidades del producto: " . $datos_prod["Nombre"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    if ($datos_prod["Existencias"] < $detalle_datos["Cantidad"]) {
                        $respuesta["message"] = "Lo sentimos, no hay existencia del producto: " . $datos_prod["Nombre"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }
                endforeach;

                if ($GranTotal <= 0) {
                    $respuesta["message"] = "Lo sentimos, no selecciono ninguna cantidad, por favor verifique, el pedido no fue enviado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }

                if ($GranTotal < $PedidoMinimo) {
                    $respuesta["message"] = "El pedido mínimo debe ser de $ " . $PedidoMinimo;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                $PermiteReservar = $datos_socio["PermiteDomicilios"];
                if ($PermiteReservar == "N" && $IDClub != 7) {
                    $respuesta["message"] = "Su pedido no puede ser realizado por favor contactar con el area de cartera";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }

                //San andres depende la zona se calcula la fecha del pedido
                if ($IDClub == 70) {
                    $pos1 = strpos($FormaPago, "junio 30.");
                    $pos2 = strpos($FormaPago, "junio 30.");

                    if ($pos1 !== false) {
                        $HoraEntrega = "2021-06-30";
                    } elseif ($pos2 !== false) {
                    $HoraEntrega = "2021-06-30";
                }

                $fechamaxima = date("Y-m-d", strtotime($HoraEntrega . "- 3 days"));
                if (strtotime($hoy) > strtotime($fechamaxima)) {
                    $respuesta["message"] = "No es posible realizar el pedido debe ser 3 dias antes de la fecha de entrega por zona:" . $fechamaxima;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }
            }

            //Consulto el siguiente consecutivo del pedido
            $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From Domicilio" . $Version . " Where IDClub = '" . $IDClub . "'";
            $result_numero = $dbo->query($sql_max_numero);
            $row_numero = $dbo->fetchArray($result_numero);
            $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

            //Actualizo el celular y direccion del Socio
            $update_celular = "Update Socio Set Celular = '" . $Celular . "', Direccion= '" . $Direccion . "' Where IDSocio='" . $IDSocio . "'";
            $dbo->query($update_celular);

            $sql_domicilio = $dbo->query("Insert Into Domicilio" . $Version . " (IDClub, IDSocio, IDEstadoDomicilio, IDRestauranteDomicilio, Numero, Total, HoraEntrega, ComentariosSocio, Celular, Direccion, ValorDomicilio, FormaPago, NumeroMesa, Propina, UsuarioTrCr, FechaTrCr)
									Values ('" . $IDClub . "','" . $IDSocio . "','1','" . $IDRestaurante . "','" . $siguiente_consecutivo . "','" . $GranTotal . "', '" . $HoraEntrega . "','" . $ComentariosSocio . "','" . $Celular . "','" . $Direccion . "','" . $ValorDomicilio . "','" . $FormaPago . "','" . $NumeroMesa . "','" . $Propina . "','App',NOW())");

            $id_domicilio = $dbo->lastID();

            $CamposFormulario = trim(preg_replace('/\s+/', ' ', $CamposFormulario));
            $array_Campos = json_decode($CamposFormulario, true);

            if (count($array_Campos) > 0):
                foreach ($array_Campos as $id_valor_campo => $valor_campo):
                    // Guardo los campos personalizados
                    $sql_inserta_campo = $dbo->query("INSERT INTO DomicilioCampo (IDDomicilio, IDDomicilioPregunta, Valor)
																						Values ('" . $id_domicilio . "','" . $valor_campo["IDCampo"] . "', '" . $valor_campo["Valor"] . "')");
                endforeach;
            endif;

            if ($IDClub == 20) { // Para Medellin guardo en tabla de ellos
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                $server = '190.0.53.38';
                try {
                    $hostname = $server;
                    $port = "";
                    $dbname = DBNAME_MEDELLIN;
                    $username = DBUSER_MEDELLIN;
                    $pw = DBPASS_MEDELLIN;
                    $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
                } catch (PDOException $e) {
                    //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                    echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
                    exit;
                }
            }

            foreach ($datos_pedido as $detalle_datos):
                $IDProducto = $detalle_datos["IDProducto"];
                $Cantidad = $detalle_datos["Cantidad"];
                $Comentario = $detalle_datos["Comentario"];
                $ValorUnitario = $detalle_datos["ValorUnitario"];
                $Total = (int) $detalle_datos["Cantidad"] * (float) $detalle_datos["ValorUnitario"];
                if ($Cantidad > 0) {
                    $inserta_detalle = "INSERT INTO DomicilioDetalle" . $Version . " (IDDomicilio, IDProducto, Comentario, Cantidad, ValorUnitario, Total)
													 Values('" . $id_domicilio . "', '" . $IDProducto . "','" . $Comentario . "','" . $Cantidad . "','" . $ValorUnitario . "','" . $Total . "')";
                    $dbo->query($inserta_detalle);

                    $sql_existencias = "UPDATE Producto" . $Version . " SET Existencias =  Existencias - " . $Cantidad . " WHERE IDProducto = '" . $IDProducto . "'";
                    $dbo->query($sql_existencias);

                    //$Caracteristicas= trim(preg_replace('/\s+/', ' ', $detalle_datos["Caracteristicas"]));
                    //$datos_respuesta= json_decode($Caracteristicas, true);
                    //$datos_respuesta= $Caracteristicas;
                    $datos_respuesta = $detalle_datos["Caracteristicas"];
                    $SumaEspeciales = 0;
                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_carac):
                            $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                            $ValoresCarac = $detalle_carac["Valores"];
                            $ValoresID = $detalle_carac["ValoresID"];
                            $Total = $detalle_carac["Total"];
                            $SumaEspeciales += $Total;

                            if (!empty($IDPropiedadProducto)) {
                                $array_id_carac = explode(",", $ValoresID);
                                if (count($array_id_carac) > 0) {
                                    foreach ($array_id_carac as $id_carac) {
                                        $sql_datos_form = $dbo->query("INSERT INTO DomicilioCaracteristica (IDDomicilio, IDProducto, IDPropiedadProducto, IDCaracteristicaProducto, Valor, Valores, Total) Values ('" . $id_domicilio . "','" . $IDProducto . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                    }
                                }
                            }
                        endforeach;
                        $sql_dom = "UPDATE Domicilio SET Total = Total + " . (float) $SumaEspeciales . " WHERE IDDomicilio = '" . $id_domicilio . "'";
                        $dbo->query($sql_dom);
                    endif;

                    if ($IDClub == 20) { // Para Medellin guardo en tabla de ellos
                        $datos_prod = $dbo->fetchAll("Producto" . $Version, " IDProducto = '" . $detalle_datos["IDProducto"] . "' ", "array");
                        if ($link) {

                            $doc = $datos_socio["NumeroDocumento"];
                            $idprod = $datos_prod["IDProductoExterno"];
                            $cant = $detalle_datos["Cantidad"];
                            $fech = date("Y-m-d H:i:s");
                            $sql = $dbh->query("INSERT INTO vapp_det_pedido (ident_cliente,codigo_producto_pos,cantidad)
																					VALUES('" . $doc . "',$idprod,$cant) ");
                        }
                    }
                }
            endforeach;

            if ($IDClub == 20) { // Para Medellin guardo en tabla encabezado
                if ($link) {
                    $doc = $datos_socio["NumeroDocumento"];
                    $fech = date("Y-m-d H:i:s");

                    $sql = $dbh->query("INSERT INTO vapp_enc_pedido (ident_cliente,fecha_envio,comentario,id_pedido)
																VALUES('" . $doc . "','" . $fech . "','" . $ComentariosSocio . "',$id_domicilio) ");

                    //Verifico que el pedido se genere correctamente en el sistema
                    $sql_confirma = "SELECT TOP 1 CAST(id_pedido  AS INTEGER) AS id_pedido
													 FROM vapp_pedidos
													 WHERE id_pedido = '" . $id_domicilio . "'";
                    $r_confirma = $dbh->query($sql_confirma);
                    $contador_sql = 0;
                    while ($row = $r_confirma->fetch()) {
                        $contador_sql++;
                    }
                    if ($contador_sql <= 0) {
                        //Borro el pedio
                        $sql_borra = "DELETE FROM Domicilio" . $Version . " WHERE IDDomicilio = '" . $id_domicilio . "'";
                        $dbo->query($sql_borra);
                        $respuesta["message"] = "Atencion ocurrio un problema de comunicacion por favor intente mas tarde";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                }
            }

            //if($IDClub==7): // Para Lagartos manda impresion
            self::imprime_recibo_domicilio($id_domicilio, $Version);

            //endif;
            SIMUtil::notifica_recibo_domicilio($id_domicilio, $Version);

            $datos_domicilio = $dbo->fetchAll("Domicilio" . $Version, " IDDomicilio = '" . $id_domicilio . "' ", "array");
            $datos_config_domicilio = $dbo->fetchAll("ConfiguracionDomicilios" . $Version, " IDClub = '" . $datos_domicilio["IDClub"] . "'AND Activo = 'S' Limit 1", "array");

            if ($IDClub == 44) {
                $mensaje_guardar = $datos_config_domicilio['MensajeConfirmacion'];
                if ($mensaje_guardar == "") {
                    //$mensaje_guardar="Su pedido se ha enviado al área encargada, se le enviará un correo para confirmar fecha y hora de entrega. le recordamos que los pedidos se tramitan  con un día de anticipación";
                    //$mensaje_guardar="Su pedido estará listo para recoger en la restaurante de la piscina a la hora seleccionada";
                    $mensaje_guardar = "Su pedido estará listo para recoger a la hora acordada en el Club Juvenil.";
                }

            } else {
                $mensaje_guardar = "Pedido realizado " . $otro_mensaje;
            }

            if ($PermiteReservar == "N" && $IDClub == 7) {
                $mensaje_guardar = "Para poder hacer su pedido por favor comuniqese con Alimentos y Bebidas (Ext 248) o con el Departamento de Cartera (Ext 1241)";
                $otros_mensajes = "";
            }

            //Datos reserva
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $response_reserva = array();
            $datos_reserva["IDDomicilio"] = (int) $id_domicilio;
            //Calculo el valor de la reserva
            $valor_inicial_reserva = (int) $GranTotal;
            $datos_reserva["ValorReserva"] = $GranTotal + $ValorDomicilio + $SumaEspeciales;

            if ($Propina == "S" && !empty($porcentaje_propina)) {
                $ValorPropina = $datos_reserva["ValorReserva"] * $porcentaje_propina / 100;
                $datos_reserva["ValorReserva"] += $ValorPropina;
            }

            $ValorReserva = $GranTotal + $ValorDomicilio + $ValorPropina;
            $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
            $ApiLogin = $datos_club["ApiLogin"]; //Api Login

            if ($datos_club["MerchantId"] != "placetopay") {
                $usuarioId = $datos_club["MerchantId"];
            }
            //c0digo inicio del cliente
            else {
                    $usuarioId = $datos_club["ApiLogin"];
                }
                //c0digo inicio del cliente

                $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                $iva = 0; //impuestos calculados de la transacciÛn
                $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                $moneda = "COP"; //la moneda con la que se realiza la compra
                $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
                $descripcion = "Pago Domicilio Mi Club"; //descripciÛn de la transacciÛn
                $url_respuesta = URLROOT . "respuesta_transaccion_domicilio.php?Version=" . $Version; //Esta es la p·gina a la que se direccionar· al final del pago
                $url_confirmacion = URLROOT . "confirmacion_pagos_domicilio.php?Version=" . $Version;
                $emailSocio = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                    $emailComprador = $emailSocio;
                } else {
                    $emailComprador = "";
                }

                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                $extra1 = $id_domicilio;

                $datos_reserva["Action"] = $datos_club["URL_PAYU"];

                $response_parametros = array();
                $datos_post["llave"] = "moneda";
                $datos_post["valor"] = (string) $moneda;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "IDSocio";
                $datos_post["valor"] = (string) $IDSocio;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "ref";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "llave";
                $datos_post["valor"] = $llave_encripcion;
                array_push($response_parametros, $datos_post);

                //$datos_post["llave"] = "userid";
                //$datos_post["valor"] = $usuarioId;
                //array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "usuarioId";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "accountId";
                $datos_post["valor"] = (string) $datos_club["AccountId"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "descripcion";
                $datos_post["valor"] = $descripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra1";
                $datos_post["valor"] = (string) $extra1;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = $ValorReserva;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "iva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "baseDevolucionIva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "firma";
                $datos_post["valor"] = $firma;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "emailComprador";
                $datos_post["valor"] = $emailComprador;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "prueba";
                $datos_post["valor"] = (string) $datos_club["IsTest"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "version";
                $datos_post["valor"] = (string) $Version;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "Modulo";
                $datos_post["valor"] = "Domicilio";
                array_push($response_parametros, $datos_post);

                $datos_reserva["ParametrosPost"] = $response_parametros;

                //PAGO
                $datos_post_pago = array();
                $datos_post_pago["iva"] = 0;
                $datos_post_pago["purchaseCode"] = $refVenta;
                $datos_post_pago["totalAmount"] = $ValorReserva * 100;
                $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                //FIN PAGO

                $respuesta["message"] = $mensaje_guardar . $otros_mensajes;
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_reserva;
            } else {
                $respuesta["message"] = "Dom. Atencion faltan parametros o socio no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function elimina_pedido($IDClub, $IDSocio, $IDDomicilio, $Admin = "", $Razon = "", $Version = "")
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDDomicilio)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    //Verifico que este en el tiempo limite para eliminar

                    $tiempo_cancelacion = (int) $dbo->getFields("ConfiguracionDomicilios" . $Version, "TiempoMinimoCancelacion", "IDClub = '" . $IDClub . "'");
                    if ((int) $tiempo_cancelacion > 0):
                        $minutos_anticipacion = $tiempo_cancelacion;
                    else:
                        $minutos_anticipacion = 30;
                    endif;

                    $hora_fecha_entrega = $dbo->getFields("Domicilio" . $Version, "HoraEntrega", "IDDomicilio = '" . $IDDomicilio . "'");

                    $fecha_domicilio = substr($hora_fecha_entrega, 0, 10);
                    if (empty($fecha_domicilio)) {
                        $fecha_domicilio = date("Y-m-d");
                    }

                    $hora_entrega = substr($hora_fecha_entrega, 11);

                    $hora_inicio_domicilio = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($fecha_domicilio . " " . $hora_entrega));
                    $fechahora_actual = strtotime(date("Y-m-d H:i:s"));

                    if ($fechahora_actual > $hora_inicio_domicilio && empty($Admin)):
                        $respuesta["message"] = "El pedido no puede ser cancelado. supera el tiempo mínimo par cancelación. Para mayor información comuníquese con el club";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    else:

                        //if($IDClub==7): // Para Lagartos manda impresion
                        SIMUtil::notifica_elimina_domicilio($IDDomicilio);
                        //endif;

                        //borro domicilio
                        //$sql_borra_domicilio = $dbo->query("Delete From Domicilio".$Version." Where IDDomicilio  = '".$IDDomicilio."'");
                        $sql_borra_domicilio = $dbo->query("UPDATE Domicilio" . $Version . " SET IDEstadoDomicilio = '3' Where IDDomicilio  = '" . $IDDomicilio . "'");

                        if ($IDClub == 20) { // Para Medellin guardo en tabla de ellos los productos borrados
                            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                            $sql_detalle = "SELECT * FROM DomicilioDetalle Where IDDomicilio  = '" . $IDDomicilio . "' ";
                            $r_detalle = $dbo->query($sql_detalle);
                            $server = '190.0.53.38';
                            // Connect to Sql server CASMPRESTRE MEDELLIN
                            try {
                                $hostname = $server;
                                $port = "";
                                $dbname = DBNAME_MEDELLIN;
                                $username = DBUSER_MEDELLIN;
                                $pw = DBPASS_MEDELLIN;
                                $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
                            } catch (PDOException $e) {
                                //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                                echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
                                exit;
                            }

                            while ($row_detalle = $dbo->fetchArray($r_detalle)) {
                                $datos_prod = $dbo->fetchAll("Producto", " IDProducto = '" . $detalle_datos["IDProducto"] . "' ", "array");

                                $doc = $datos_socio["NumeroDocumento"];
                                $idprod = $datos_prod["IDProductoExterno"];
                                $cant = "-" . $detalle_datos["Cantidad"];
                                $fech = date("Y-m-d H:i:s");

                                $sql = $dbh->query("INSERT INTO vapp_pedidos (ident_cliente,codigo_producto_pos,cantidad,fecha_envio,enviado)
																				VALUES($doc,$idprod,$cant,'" . $fech . "',1) ");

                            }

                        }

                        //borro productos domicilio
                        //$sql_borra_producto = $dbo->query("Delete From DomicilioDetalle".$Version." Where IDDomicilio  = '".$IDDomicilio."'");

                        $respuesta["message"] = "Pedido eliminado correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    endif;

                } else {
                    $respuesta["message"] = "Error el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "9. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_configuracion_correspondencia($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM ConfiguracionCorrespondencia  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["IconoPorEntregar"] = CLUB_ROOT . $r["IconoPorEntregar"];
                    $configuracion["IconoEntregado"] = CLUB_ROOT . $r["IconoEntregado"];
                    $configuracion["IconoEntregar"] = CLUB_ROOT . $r["IconoEntregar"];
                    $configuracion["IconoRecibir"] = CLUB_ROOT . $r["IconoRecibir"];
                    $configuracion["HabilitaRegistraTodos"] = $r["HabilitaRegistraTodos"];
                    $configuracion["LabelBuscador"] = $r["LabelBuscador"];
                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Correspondencia no está activo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_configuracion_evento($IDClub, $IDSocio, $Version)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

            $configuracion["PermiteMisEventos"] = $datos_club["PermiteMisEventos" . $Version];
            $configuracion["PermiteMisEventos"] = $datos_club["PermiteMisEventos" . $Version];
            $configuracion["TipoFiltroEvento"] = $datos_club["TipoFiltroEvento" . $Version];
            $configuracion["EventoLista"] = $datos_club["EventoLista" . $Version];
            $configuracion["ColorFondoEvento"] = $datos_club["ColorFondoEvento" . $Version];
            $configuracion["BuscadorFechaEvento"] = $datos_club["BuscadorFechaEvento" . $Version];
            $configuracion["TipoCeldaEvento"] = $datos_club["TipoCeldaEvento" . $Version];
            array_push($response, $configuracion);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;

            return $respuesta;

        } // fin function

        public function get_configuracion_votacion($IDClub, $IDSocio, $IDUsuario)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $configuracion["IDClub"] = $IDClub;
            $configuracion["LabelBotonResultados"] = "Ver Resultados";
            $configuracion["LabelBotonRefrescar"] = "Pulse para refrescar";
            $configuracion["BotonResultados"] = "S";
            //Busco el evento activo de la votacion
            $IDEvento = $dbo->getFields("VotacionEvento", "IDVotacionEvento", "Activo = 'S' and IDClub = '" . $IDClub . "'");
            $configuracion["URLResultados"] = URLROOT . "plataform/screen/pantallavotacion.php?IDVotacionEvento=" . $IDEvento . "&IDClub=" . $IDClub;
            array_push($response, $configuracion);
            $respuesta["message"] = "1 resultado encontrado";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        } // fin function

        public function get_configuracion_clasificado($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["PermiteCrearClasificado"] = $r["CrearClasificado"];
                    $configuracion["TipoCrearClasificado"] = $r["TipoCrearClasificado"];
                    $configuracion["URLCLasificado"] = $r["URLCLasificado"];
                    if ($IDClub == 49 || $IDClub == 8) {
                        $configuracion["LabelIDCategoria"] = "Categoria Clasificado";
                        $configuracion["LabelNombre"] = "Titulo a nombre de la publicación";
                        $configuracion["LabelDescripcion"] = "¿Qué tiene tu producto?";
                        $configuracion["LabelTelefono"] = "Teléfono de contacto";
                        $configuracion["LabelEmail"] = "Email de contacto";
                        $configuracion["LabelValor"] = "Meta de recaudo en números";
                        $configuracion["LabelFechaInicio"] = "Fecha Inicio";
                        $configuracion["LabelFechaFin"] = "FechaFin";
                        $configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

                        $configuracion["LabelDetalleNombre"] = "";
                        $configuracion["LabelDetalleValor"] = "Meta";
                        $configuracion["LabelDetalleDescripcion"] = "Tiene:";

                    } elseif ($IDClub == 72) {
                    $configuracion["LabelDescripcion"] = "Descripción";
                    $configuracion["LabelNombre"] = "Nombre del producto o servicio";
                    $configuracion["LabelDetalleNombre"] = "";
                    $configuracion["LabelDetalleValor"] = " Precio ";
                    $configuracion["LabelDetalleDescripcion"] = "Tiene:";
                } elseif ($IDClub == 34) {
                    $configuracion["LabelIDCategoria"] = "Categoría de tu producto";
                    $configuracion["LabelNombre"] = "Nombre de tu producto";
                    $configuracion["LabelDescripcion"] = "Detalle de servicios";
                    $configuracion["LabelTelefono"] = "Teléfono de contacto";
                    $configuracion["LabelEmail"] = "Mail de contacto";
                    $configuracion["LabelValor"] = "Precio Referencial";
                    $configuracion["LabelFechaInicio"] = "Fecha inicial";
                    $configuracion["LabelFechaFin"] = "Fecha Final";
                    $configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

                    $configuracion["LabelDetalleNombre"] = "";
                    $configuracion["LabelDetalleValor"] = "Precio";
                    $configuracion["LabelDetalleDescripcion"] = "Detalle de servicios:";
                } else {
                    $configuracion["LabelIDCategoria"] = "";
                    $configuracion["LabelNombre"] = "";
                    $configuracion["LabelDescripcion"] = "";
                    $configuracion["LabelTelefono"] = "";
                    $configuracion["LabelEmail"] = "";
                    $configuracion["LabelValor"] = "";
                    $configuracion["LabelFechaInicio"] = "";
                    $configuracion["LabelFechaFin"] = "";
                    $configuracion["LabelIDEstadoClasificado"] = "";

                    $configuracion["LabelDetalleNombre"] = "";
                    $configuracion["LabelDetalleValor"] = "";
                    $configuracion["LabelDetalleDescripcion"] = "";

                }

                array_push($response, $configuracion);

            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "Correspondencia no está activo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_configuracion_clasificado2($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["PermiteCrearClasificado"] = $r["CrearClasificado"];
                    $configuracion["TipoCrearClasificado"] = $r["TipoCrearClasificado"];
                    $configuracion["URLCLasificado"] = $r["URLCLasificado"];
                    if ($IDClub == 25 || $IDClub == 8) {
                        $configuracion["LabelIDCategoria"] = "Categoria";
                        $configuracion["LabelNombre"] = "Nombre de la empresa / emprendimiento";
                        $configuracion["LabelDescripcion"] = "Descripcion del producto";
                        $configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";
                        //Dinamicos por club
                        $campo = array();
                        $response_campo = array();
                        $sql_campo = "SELECT * FROM ClasificadoCampo WHERE IDClub = '" . $IDClub . "' AND Publicar = 'S' Order By 	Orden";
                        $r_campo = $dbo->query($sql_campo);
                        while ($row_campo = $dbo->FetchArray($r_campo)):
                            $campo["IDClasificadoCampo"] = $row_campo["IDClasificadoCampo"];
                            $campo["TipoCampo"] = $row_campo["TipoCampo"];
                            $campo["EtiquetaCampo"] = $row_campo["EtiquetaCampo"];
                            $campo["Obligatorio"] = $row_campo["Obligatorio"];
                            $campo["Valores"] = $row_campo["Valores"];
                            $campo["Orden"] = $row_campo["Orden"];
                            array_push($response_campo, $campo);
                        endwhile;
                        $configuracion["Campos"] = $response_campo;
                    } else {
                        $configuracion["LabelIDCategoria"] = "";
                        $configuracion["LabelNombre"] = "";
                        $configuracion["LabelDescripcion"] = "";
                        $configuracion["LabelTelefono"] = "";
                        $configuracion["LabelEmail"] = "";
                        $configuracion["LabelValor"] = "";
                        $configuracion["LabelFechaInicio"] = "";
                        $configuracion["LabelFechaFin"] = "";
                        $configuracion["LabelIDEstadoClasificado"] = "";

                        $configuracion["LabelDetalleNombre"] = "";
                        $configuracion["LabelDetalleValor"] = "";
                        $configuracion["LabelDetalleDescripcion"] = "";

                    }

                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Correspondencia no está activo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_configuracion_ofertas($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $array_tipo_contrato = SIMResources::$tipo_contrato;

            $sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];

                    //Tipo Contrato
                    $response_tipo_contrato = array();
                    if (count($array_tipo_contrato) > 0):
                        foreach ($array_tipo_contrato as $nombre_tipo):
                            $dato_tipo_contrato["Valor"] = $nombre_tipo;

                            array_push($response_tipo_contrato, $dato_tipo_contrato);
                        endforeach;
                    endif;

                    $configuracion["TipoContrato"] = $response_tipo_contrato;

                    $response_industria = array();
                    $sql_otros = "SELECT * From Industria Where Publicar = 'S'";
                    $result_otros = $dbo->query($sql_otros);
                    while ($row_otros = $dbo->fetchArray($result_otros)):
                        $array_otros["IDIndustria"] = $row_otros["IDIndustria"];
                        $array_otros["Valor"] = $row_otros["Nombre"];
                        array_push($response_industria, $array_otros);
                    endwhile;

                    $configuracion["Industrias"] = $response_industria;
                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Ofertas no está activo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_oferta($IDClub, $IDSocio, $IDUsuario, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario) ) && !empty($IDIndustria) && !empty($NombreEmpresa) && !empty($PublicarEmpresa) && !empty($Cargo)) {

                //verifico que el socio exista y pertenezca al club
                if(!empty($IDSocio)){
                  $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                }
                elseif(!empty($IDUsuario)){
                  $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
                }

                if (!empty($id_socio) || !empty($id_usuario) ) {


                    $sql_oferta = $dbo->query("INSERT INTO Oferta (IDSocio	, IDUsuario, IDIndustria, IDClub, IDEstadoOferta, TipoContrato, NombreEmpresa, PublicarEmpresa, Cargo, Ciudad, NombreEncargado, CorreoContacto, DescripcionCargo, ComentarioAdicional, FechaInicio, FechaFin, UsuarioTrCr, FechaTrCr	)
															   					Values ('" . $IDSocio . "','".$IDUsuario."','" . $IDIndustria . "','" . $IDClub . "','5','" . $TipoContrato . "','" . $NombreEmpresa . "','" . $PublicarEmpresa . "','" . $Cargo . "','" . $Ciudad . "','" . $NombreEncargado . "','" . $CorreoContacto . "','" . $DescripcionCargo . "','" . $ComentarioAdicional . "'
																										,'" . $FechaInicio . "','" . $FechaFin . "','App',NOW())");

                    $id_oferta = $dbo->lastID();

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_edita_oferta($IDClub, $IDOferta, $IDEstadoOferta, $IDSocio, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDOferta) && !empty($IDIndustria) && !empty($NombreEmpresa) && !empty($PublicarEmpresa) && !empty($Cargo)) {

                //verifico que el socio exista y pertenezca al club
                if(!empty($IDSocio)){
                  $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                }
                elseif(!empty($IDUsuario)){
                  $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
                }

                if (!empty($id_socio) || !empty($id_usuario) ) {

                    $sql_oferta = "UPDATE Oferta
																SET  IDIndustria='" . $IDIndustria . "', IDEstadoOferta='" . $IDEstadoOferta . "', TipoContrato='" . $TipoContrato . "', NombreEmpresa='" . $NombreEmpresa . "',
																		  PublicarEmpresa='" . $PublicarEmpresa . "', Cargo='" . $Cargo . "', Ciudad='" . $Ciudad . "', NombreEncargado='" . $NombreEncargado . "', CorreoContacto='" . $CorreoContacto . "',
																			DescripcionCargo='" . $DescripcionCargo . "', ComentarioAdicional='" . $ComentarioAdicional . "', FechaInicio='" . $FechaInicio . "', FechaFin='" . $FechaFin . "',
																			UsuarioTrCr='" . $IDSocio . "', FechaTrCr=NOW()
																WHERE IDOferta = '" . $IDOferta . "'";

                    $dbo->query($sql_oferta);

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_aplicar_oferta($IDClub, $IDSocio, $IDOferta, $NombreRecomendado, $Telefono, $CorreoElectronico, $File = "", $IDUsuario)
    {
            $dbo = &SIMDB::get();
            $datos_oferta = $dbo->fetchAll("Oferta", " IDOferta = '" . $IDOferta . "' ", "array");
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDOferta) && !empty($Telefono) && !empty($CorreoElectronico)) {

                //verifico que el socio exista y pertenezca al club
                if(!empty($IDSocio)){
                  $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                }
                elseif(!empty($IDUsuario)){
                  $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
                }

                if (!empty($id_socio) || !empty($id_usuario) ) {
                    if (isset($File)) {

                        $campo_foto = "Archivo";
                        $files = SIMFile::upload($File["Archivo"], OFERTA_DIR, "IMAGE");
                        if (empty($files) && !empty($File["Archivo"]["name"])):
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $Archivo = $files[0]["innername"];

                    } //end if

                    $sql_candidato = $dbo->query("INSERT INTO OfertaCandidato (IDOferta,IDSocio	, IDUsuario, NombreRecomendado, Telefono, CorreoElectronico, Archivo, UsuarioTrCr, FechaTrCr)
																   Values ('" . $IDOferta . "', '" . $IDSocio . "','".$IDUsuario."','" . $NombreRecomendado . "','" . $Telefono . "','" . $CorreoElectronico . "','" . $Archivo . "','App',NOW())");

                    $id_candidato = $dbo->lastID();

                    //SIMUtil::notificar_nueva_aplicacion_oferta($id_candidato);

                    $Mensaje = "Se ha creado un nueva aplicacion a una oferta laboral : " . $datos_oferta["Cargo"];
                    //Consulto que socio se les envia la notificacion
                    $id_socio_oferta = $dbo->getFields("Oferta", "IDSocio", "IDOferta = '" . $IDOferta . "' and IDClub = '" . $IDClub . "'");
                    if ((int) $id_socio_oferta > 0):
                        SIMUtil::enviar_notificacion_push_general($IDClub, $id_socio_oferta, $Mensaje);
                        SIMUtil::enviar_oferta($IDOferta, $id_candidato);
                    endif;

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "O3.Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_clasificado($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $File = "", $Dispositivo)
    {
            $dbo = &SIMDB::get();

            if ($FechaInicio == "" || $FechaFin == "") {
                $respuesta["message"] = "Las fechas son obligatorias";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($Telefono)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    if (isset($File)) {

                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                            $$campo_foto = $files[0]["innername"];
                        endfor;

                    } //end if

                    //valido que la fecha sea maximo de 1 mes
                    $fecha_actual = date('Y-m-j');
                    $fecha_maxima = strtotime('+1 month', strtotime($fecha_actual));
                    $fecha_fin_clasif = strtotime($FechaFin);

                    if ($fecha_fin_clasif > $fecha_maxima):
                        $FechaFin = date('Y-m-d', $fecha_maxima);
                    endif;

                    if ($IDClub == 16) {
                        $IDEstadoDefecto = 1;
                    } else {
                        $IDEstadoDefecto = 5;
                    }

                    if ($Dispositivo == "iOS") {
                        $Nombre = utf8_decode($Nombre);
                        $Descripcion = utf8_decode($Descripcion);
                    }

                    $sql_clasificado = $dbo->query("Insert Into Clasificado (IDSocio	, IDSeccionClasificados, IDClub, IDEstadoClasificado, Nombre, Descripcion, Telefono, Email, Valor, FechaInicio, FechaFin, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr	)
																   Values ('" . $IDSocio . "','" . $IDCategoria . "','" . $IDClub . "','" . $IDEstadoDefecto . "','" . $Nombre . "','" . $Descripcion . "','" . $Telefono . "','" . $Email . "','" . $Valor . "','" . $FechaInicio . "','" . $FechaFin . "','" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','App',NOW())");

                    $id_clasificado = $dbo->lastID();

                    SIMUtil::notificar_nuevo_clasificado($id_clasificado);

                    /*
                    $Mensaje = "Se ha creado un nuevo clasificado: " . $Nombre;
                    //Consulto que socio se les envia la notificacion
                    $sql_socio_clasif = "Select S.IDSocio
                    From Socio S, SocioSeccionClasificados SS
                    Where S.IDSocio=SS.IDSocio And SS.IDSeccionClasificados = '".$IDCategoria."'";
                    $result_socio_clasif =    $dbo->query($sql_socio_clasif);
                    while($row_socio_clasif = $dbo->fetchArray($result_socio_clasif)):
                    $array_id_socio[]=$row_socio_clasif["IDSocio"];
                    endwhile;
                    if(count($array_id_socio)>0):
                    $IDSocio = implode(",",$array_id_socio);
                    SIMUtil::enviar_notificacion_push_clasificado($IDClub,$IDSocio,$Mensaje,$id_clasificado);
                    endif;
                     */

                    $respuesta["message"] = "Gracias por usar el app su clasificado está en revisión y será publicado en las próximas horas.";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_clasificado2($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $File = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    if (isset($File)) {

                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                            $$campo_foto = $files[0]["innername"];
                        endfor;

                    } //end if

                    //valido que la fecha sea maximo de 1 mes
                    $fecha_actual = date('Y-m-j');
                    $fecha_maxima = strtotime('+1 month', strtotime($fecha_actual));
                    $FechaFin = $fecha_maxima;
                    $fecha_fin_clasif = strtotime($FechaFin);

                    if ($fecha_fin_clasif > $fecha_maxima):
                        $FechaFin = date('Y-m-d', $fecha_maxima);
                    endif;

                    $sql_clasificado = $dbo->query("Insert Into Clasificado2 (IDSocio	, IDSeccionClasificados, IDClub, IDEstadoClasificado, Nombre, Descripcion, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr	)
																	   Values ('" . $IDSocio . "','" . $IDCategoria . "','" . $IDClub . "','5','" . $Nombre . "','" . $Descripcion . "','" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','App',NOW())");

                    $id_clasificado = $dbo->lastID();

                    //Inserto el valor de los campos dinamicos
                    $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                    $datos_respuesta = json_decode($Respuestas, true);
                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_respuesta):
                            $sql_datos_form = $dbo->query("INSERT INTO ClasificadoRespuesta (IDClasificado, IDSocio, IDClasificadoCampo, Valor, FechaTrCr) Values ('" . $id_clasificado . "','" . $IDSocio . "','" . $detalle_respuesta["IDClasificadoCampo"] . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                        endforeach;
                    endif;

                    //SIMUtil::notificar_nuevo_clasificado2($id_clasificado);

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_ofertas($IDClub, $IDSocio, $IDEstadoOferta)
    {

            $dbo = &SIMDB::get();

            /*
            //Socio
            if (!empty($IDSocio)):
            $array_condiciones[] = " IDSocio  = '".$IDSocio."' ";
            else:
            $array_condiciones[] = " IDSocio  <> '".$IDSocio."' ";
            endif;

            if (!empty($IDEstadoOferta)):
            $array_condiciones[] = " IDEstadoOferta  = '".$IDEstadoClasificado."' ";
            else:
            $array_condiciones[] = " IDEstadoOferta  > 0 ";
            endif;
             */

            /*
            if (!empty($Tipo)):
            $sql_aplicacion="SELECT IDOferta FROM OfertaCandidato WHERE IDSocio = '".$IDSocio."'";
            $r_aplicacion=$dbo->query($sql_aplicacion);
            while($row_aplicacion=$dbo->fetchArray($r_aplicacion)){
            $array_id_oferta[]=$row_aplicacion["IDOferta"];
            }
            if(count($array_id_oferta)>0){
            $id_oferta_aplicacion=implode(",",$array_id_oferta);
            $array_condiciones[] = " IDOferta  in (".$id_oferta_aplicacion.")  ";
            }
            else{
            $array_condiciones[] = " IDOferta  = 0 ";
            }

            $array_condiciones[] = " IDEstadoOferta  = '".$IDEstadoClasificado."' ";
            else:
            $array_condiciones[] = " IDEstadoOferta  > 0 ";
            endif;
             */

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_oferta = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Oferta WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "'" . $condiciones_oferta . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $IDTipoOferta = 1;
                    if ($r["IDSocio"] == $IDSocio) {
                        $IDTipoOferta = 2; // Es la oferta creada por el socio
                    } else {
                        $IDTipoOferta = 1; // Es una oferta de otro socio
                    }

                    $sql_aplicacion = "SELECT IDOferta FROM OfertaCandidato WHERE IDSocio = '" . $IDSocio . "' and IDOferta = '" . $r["IDOferta"] . "'";
                    $r_aplicacion = $dbo->query($sql_aplicacion);
                    while ($row_aplicacion = $dbo->fetchArray($r_aplicacion)) {
                        $IDTipoOferta = 3; // oferta en al que aplico el socio
                    }

                    $oferta["IDOferta"] = $r["IDOferta"];
                    $oferta["IDSocio"] = $r["IDSocio"];
                    $oferta["Industria"] = utf8_encode($dbo->getFields("Industria", "Nombre", "IDIndustria = '" . $r["IDIndustria"] . "'"));
                    $oferta["IDEstadoOferta"] = $r["IDEstadoOferta"];
                    $oferta["EstadoOferta"] = utf8_encode($dbo->getFields("EstadoOferta", "Nombre", "IDEstadoOferta = '" . $r["IDEstadoOferta"] . "'"));
                    $oferta["TipoContrato"] = $r["TipoContrato"];
                    $oferta["NombreEmpresa"] = $r["NombreEmpresa"];
                    $oferta["PublicarEmpresa"] = $r["PublicarEmpresa"];
                    $oferta["Cargo"] = $r["Cargo"];
                    $oferta["Ciudad"] = $r["Ciudad"];
                    $oferta["NombreEncargado"] = $r["NombreEncargado"];
                    $oferta["CorreoContacto"] = $r["CorreoContacto"];
                    $oferta["DescripcionCargo"] = $r["DescripcionCargo"];
                    $oferta["ComentarioAdicional"] = $r["ComentarioAdicional"];
                    $oferta["FechaInicio"] = $r["FechaInicio"];
                    $oferta["FechaFin"] = $r["FechaFin"];
                    $oferta["IDTipoOferta"] = $IDTipoOferta;

                    $response_candidatos = array();
                    $sql_candidatos = "Select * From OfertaCandidato Where IDOferta = '" . $r["IDOferta"] . "' Order by FechaTrCr Desc";
                    $result_candidatos = $dbo->query($sql_candidatos);
                    while ($row_candidatos = $dbo->fetchArray($result_candidatos)):
                        $array_candidatos["IDOfertaCandidato"] = $row_candidatos["IDOfertaCandidato"];
                        $array_candidatos["IDOferta"] = $row_candidatos["IDOferta"];
                        $array_candidatos["IDSocio"] = $row_candidatos["IDSocio"];
                        $array_candidatos["Socio"] = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_candidatos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_candidatos["IDSocio"] . "'"));
                        $array_candidatos["NombreRecomendado"] = $row_candidatos["NombreRecomendado"];
                        $array_candidatos["Telefono"] = $row_candidatos["Telefono"];
                        $array_candidatos["CorreoElectronico"] = $row_candidatos["CorreoElectronico"];

                        if (!empty($row_candidatos["Archivo"])) {
                            $archivo = OFERTA_ROOT . $row_candidatos["Archivo"];
                        } else {
                            $archivo = "";
                        }

                        $array_candidatos["Archivo"] = $archivo;
                        $array_candidatos["FechaCreacion"] = $row_candidatos["FechaTrCr"];
                        array_push($response_candidatos, $array_candidatos);
                    endwhile;

                    $oferta["Aplicaciones"] = $response_candidatos;

                    array_push($response, $oferta);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_configuracion_objetos_perdidos($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["LabelBotonSolicitarEntrega"] = "Solicitar entrega";
                    $configuracion["LabelConfirmacionEntrega"] = "Estas seguro que este objeto perdido es tuyo?";
                    $configuracion["LabelBotonMisSolicitudes"] = "Mis Solicitudes";

                    $tipodoc = array();
                    $response_tipodoc = array();
                    $sql_tipodoc = "SELECT * FROM TipoDocumento WHERE 1 ";
                    $r_tipodoc = $dbo->query($sql_tipodoc);
                    while ($row_tipodoc = $dbo->fetchArray($r_tipodoc)) {
                        $tipodoc["IDTipoDocumento"] = $row_tipodoc["IDTipoDocumento"];
                        $tipodoc["Nombre"] = $row_tipodoc["Nombre"];
                        array_push($response_tipodoc, $tipodoc);

                    }

                    $configuracion["TipoDocumento"] = $response_tipodoc;

                    $estados_obj = array();
                    $response_estado_objeto = array();
                    $sql_estados_obj = "SELECT * FROM EstadoObjetosPerdidos WHERE Publicar = 'S' ORDER BY Nombre";
                    $r_estados_obj = $dbo->query($sql_estados_obj
                    );
                    while ($row_estados_obj = $dbo->fetchArray($r_estados_obj)) {
                        $estados_obj["IDEstadoObjetosPerdidos"] = $row_estados_obj["IDEstadoObjetosPerdidos"];
                        $estados_obj["Nombre"] = $row_estados_obj["Nombre"];
                        array_push($response_estado_objeto, $estados_obj);

                    }

                    $configuracion["EstadoObjetosPerdidos"] = $response_estado_objeto;

                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Correspondencia no está activo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_configuracion_noticias($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $sql_modulo = "Select IDModulo,Titulo From ClubModulo Where IDClub = '" . $IDClub . "' and IDModulo in (3,4,5,66,81,76)";
            $r_modulo = $dbo->query($sql_modulo);
            while ($row_modulo = $dbo->fetchArray($r_modulo)) {
                $array_modulo[$row_modulo["IDModulo"]] = $row_modulo["Titulo"];
            }

            $configuracion["IDClub"] = $IDClub;

            $configuracion["IDClub"] = $IDClub;
            if (empty($array_modulo["3"])) {
                $configuracion["MisNoticias"] = "Mis Noticias";
            } else {
                $configuracion["MisNoticias"] = $array_modulo["3"];
            }

            if (empty($array_modulo["66"])) {
                $configuracion["MisNoticias2"] = "Mis Noticias";
            } else {
                $configuracion["MisNoticias2"] = $array_modulo["66"];
            }

            if (empty($array_modulo["81"])) {
                $configuracion["MisNoticias3"] = "Mis Noticias";
            } else {
                $configuracion["MisNoticias3"] = $array_modulo["81"];
            }

            if (empty($array_modulo["4"])) {
                $configuracion["MisEventos"] = "Mis Eventos";
            } else {
                $configuracion["MisEventos"] = $array_modulo["4"];
            }

            if (empty($array_modulo["76"])) {
                $configuracion["MisEventos2"] = "Mis Eventos";
            } else {
                $configuracion["MisEventos2"] = $array_modulo["76"];
            }

            if (empty($array_modulo["5"])) {
                $configuracion["MisGalerias"] = "Mis Galerias";
            } else {
                $configuracion["MisGalerias"] = $array_modulo["5"];
            }

            $configuracion["PermiteLikesNoticias"] = $datos_club["PermiteLikeNoticia1"];
            $configuracion["PermiteLikesNoticias2"] = $datos_club["PermiteLikeNoticia2"];
            $configuracion["PermiteLikesNoticias3"] = $datos_club["PermiteLikeNoticia3"];
            $configuracion["PermiteComentariosNoticias"] = $datos_club["PermiteComentarioNoticia1"];
            $configuracion["PermiteComentariosNoticias2"] = $datos_club["PermiteComentarioNoticia2"];
            $configuracion["PermiteComentariosNoticias3"] = $datos_club["PermiteComentarioNoticia3"];

            if (!empty($datos_club["IconoLikeNoticias"])) {
                $configuracion["ImagenLike"] = CLUB_ROOT . $datos_club["IconoLikeNoticias"];
            } else {
                $configuracion["ImagenLike"] = "";
            }

            if (!empty($datos_club["IconoUnLikeNoticias"])) {
                $configuracion["ImagenUnlike"] = CLUB_ROOT . $datos_club["IconoUnLikeNoticias"];
            } else {
                $configuracion["ImagenUnlike"] = "";
            }

            $TipoImagenNoticias = $dbo->getFields("Club", "TipoImagenNoticias", "IDClub = '" . $IDClub . "'");
            $configuracion["TipoImagenNoticias"] = $TipoImagenNoticias;
            $configuracion["TipoImagenNoticias2"] = $TipoImagenNoticias;
            $configuracion["TipoImagenNoticias3"] = $TipoImagenNoticias;

            array_push($response, $configuracion);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        } // fin function

        public function get_configuracion_domicilio($IDClub, $IDSocio, $Fecha = "", $Version = "", $IDRestaurante = "")
    {
            if (empty($Fecha)):
                $Fecha = date("Y-m-d");
                $dia_fecha = date("w");
            else:
                $dia_fecha = date("w", $Fecha);
            endif;

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM  ClubFechaCierre WHERE Fecha = '" . $Fecha . "' and IDClub = '" . $IDClub . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $r_cierre = $dbo->fetchArray($qry);
                $mensaje_respuesta = "Lo sentimos club cerrado el dia: " . $Fecha . " Motivo: " . $r_cierre["Motivo"];
                $respuesta["message"] = $mensaje_respuesta;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } //end else

            if (!empty($IDRestaurante)) {
                $condicion = " and (IDRestauranteDomicilio= '" . $IDRestaurante . "' or IDRestauranteDomicilio = '' ) ";
            }

            $sql = "SELECT * FROM ConfiguracionDomicilios" . $Version . "  WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and Dias like '%" . $dia_fecha . "|%' " . $condicion;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

                    $configuracion["IDConfiguracionDomicilios"] = $r["IDConfiguracionDomicilios"];
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["HoraInicioEntrega"] = $r["HoraInicioEntrega"];
                    $configuracion["HoraFinEntrega"] = $r["HoraFinEntrega"];
                    $configuracion["TiempoMinimoPedido"] = $r["TiempoMinimoPedido"];
                    $configuracion["IntervaloEntrega"] = $r["IntervaloEntrega"];
                    $configuracion["TiempoMinimoCancelacion"] = $r["TiempoMinimoCancelacion"];
                    $configuracion["TiempoConfirmacion"] = $r["TiempoConfirmacion"];
                    $configuracion["Celular"] = $datos_socio["Celular"];
                    $configuracion["LabelDomicilios"] = $r["LabelDomicilios"];
                    $configuracion["TextoDomicilio"] = $r["TextoDomicilio"];

                    if ($r["DireccionSocio"] == 'N') {
                        $configuracion["Direccion"] = "";
                    } else {
                        $configuracion["Direccion"] = $datos_socio["Direccion"];
                    }

                    //$configuracion["Direccion"] = "";
                    $configuracion["SolicitarCelular"] = $r["SolicitarCelular"];
                    $configuracion["ObligatorioCelular"] = $r["ObligatorioCelular"];
                    $configuracion["SolicitarDireccion"] = $r["SolicitarDireccion"];
                    $configuracion["ObligatorioDireccion"] = $r["ObligatorioDireccion"];
                    $configuracion["SolicitarMesa"] = $r["SolicitarMesa"];
                    $configuracion["ObligatorioMesa"] = $r["ObligatorioMesa"];

                    $configuracion["SolicitarComentario"] = $r["SolicitarComentario"];
                    $configuracion["ObligatorioComentario"] = $r["ObligatorioComentario"];
                    $configuracion["SolicitarPropina"] = $r["SolicitarPropina"];
                    $configuracion["ObligatorioPropina"] = $r["ObligatorioPropina"];
                    $configuracion["PorcentajePropina"] = $r["PorcentajePropina"];
                    $configuracion["LabelPropina"] = $r["LabelPropina"];

                    $configuracion["CobroDomicilio"] = $r["CobroDomicilio"];
                    $configuracion["ValorDomicilio"] = $r["ValorDomicilio"];
                    $configuracion["CobroDomicilioMenorA"] = $r["CobroDomicilioMenorA"];
                    $configuracion["SolicitaFechaDomicilio"] = $r["SolicitaFechaDomicilio"];
                    $configuracion["SolicitaHoraDomicilio"] = $r["SolicitaHoraDomicilio"];
                    $configuracion["SolicitaFormaPagoDomicilio"] = $r["SolicitaFormaPagoDomicilio"];
                    $configuracion["PedidoMinimo"] = $r["PedidoMinimo"];
                    $configuracion["MostrarBuscadorProductos"] = $r["MostrarBuscadorProductos"];



                    $array_forma_pago = explode(",", $r["FormaPago"]);
                    $response_forma_pago = array();
                    foreach ($array_forma_pago as $valor_forma) {
                        $forma_pago["FormaPago"] = $valor_forma;
                        array_push($response_forma_pago, $forma_pago);

                    }
                    $configuracion["FormaPago"] = $response_forma_pago;
                    $configuracion["MostrarDecimal"] = $r["MostrarDecimal"];

                    //Pagos online
                    //Tipos de pagos recibidos
                    $response_tipo_pago = array();
                    $sql_tipo_pago = "SELECT * FROM DomicilioTipoPago" . $Version . " DTP, TipoPago TP  WHERE DTP.IDTipoPago = TP.IDTipoPago and IDConfiguracionDomicilio = '" . $r["IDConfiguracionDomicilios"] . "' ";
                    $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                    if ($dbo->rows($qry_tipo_pago) > 0) {
                        $evento["PagoReserva"] = "S";
                        while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                            $tipopago["IDClub"] = $IDClub;
                            $tipopago["IDConfiguracionDomicilio"] = $r_tipo_pago["IDConfiguracionDomicilio"];
                            $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                            $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                            $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);
                            $tipopago["Nombre"] = $r_tipo_pago["Nombre"];
                            $tipopago["ConRespuesta"] = $r_tipo_pago["ConRespuesta"];
                            $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                            $imagen = "";
                            //Para el condado y es pagos online muestro la imagen de placetopay
                            if ($id_club == 51) {
                                switch ($r_tipo_pago["IDTipoPago"]) {
                                    case "1":
                                        $imagen = "https://www.miclubapp.com/file/noticia/641923_placetopay.png";
                                        break;
                                    case "2":
                                        $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                        break;
                                    case "3":
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;
                                }
                            }

                            $tipopago["Imagen"] = $imagen;
                            array_push($response_tipo_pago, $tipopago);

                        } //end while
                        $configuracion["TipoPago"] = $response_tipo_pago;
                        $configuracion["PagoReserva"] = "S";
                    } else {
                        $configuracion["PagoReserva"] = "N";
                    }

                    //Campos Personalizados
                    $response_campos = array();
                    if (empty($Version)) {
                        $VersionPregunta = 1;
                    } else {
                        $VersionPregunta = $Version;
                    }

                    $sql_campos = "SELECT * FROM DomicilioPregunta WHERE Publicar = 'S' and Version = '" . $VersionPregunta . "' and IDConfiguracionDomicilio= '" . $r["IDConfiguracionDomicilios"] . "' ORDER BY Nombre";
                    $qry_campos = $dbo->query($sql_campos);
                    if ($dbo->rows($qry_campos) > 0) {
                        while ($r_campos = $dbo->fetchArray($qry_campos)) {
                            $campos["IDCampo"] = $r_campos["IDDomicilioPregunta"];
                            $campos["Nombre"] = $r_campos["Nombre"];
                            $campos["Descripcion"] = utf8_encode($r_campos["Descripcion"]);
                            $campos["Tipo"] = $r_campos["Tipo"];
                            $campos["Valores"] = $r_campos["Valor"];
                            $campos["Obligatorio"] = $r_campos["Obligatorio"];
                            array_push($response_campos, $campos);

                        } //end while
                    }
                    $configuracion["CampoFormulario"] = $response_campos;

                    $configuracion["MostrarFotoProducto"] = $r["MostrarFotoProducto"];
                    $configuracion["MostrarPantallaTexto"] = $r["MostrarPantallaTexto"];
                    $configuracion["TextoPantalla"] = $r["TextoPantalla"];

                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                if ($IDClub == 32) { //Bijao
                    $respuesta["message"] = "El servicio de domicilio está disponible fines de semana y temporadas";
                } else {
                    $respuesta["message"] = "El servicio de Domicilio no está activo el dia de hoy";
                }
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function


        public function get_producto_buscador($IDClub,$IDSocio,$IDUsuario,$Tag,$IDRestaurante,$Version="") {

            $dbo = &SIMDB::get();
            $response = array();

            //$sql = "SELECT * FROM  Producto".$Version." WHERE (Nombre like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%' ) and  IDClub = '" . $IDClub . "'";
            $sql = "SELECT P.*
                    From Producto".$Version." P, CategoriaProducto".$Version." CP, ProductoCategoria".$Version." PC
                    WHERE CP.IDCategoriaProducto = PC.IDCategoriaProducto
                    And P.IDProducto=PC.IDProducto and P.IDClub = '".$IDClub."' and CP.IDRestauranteDomicilio = '".$IDRestaurante."'
                    And (P.Nombre like '%" . $Tag . "%' or P.Descripcion like '%" . $Tag . "%' )";


            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                  $lista_producto["IDClub"] = $r["IDClub"];
                  $lista_producto["IDProducto"] = $r["IDProducto"];
                  $lista_producto["Nombre"] = utf8_encode($r["Nombre"]);
                  $lista_producto["Descripcion"] = utf8_encode($r["Descripcion"]);
                  $lista_producto["Precio"] = $r["Precio"];
                  $lista_producto["PermiteComentarios"] = $r["PermiteComentarios"];

                  if (!empty($r["Foto1"])):
                      if (strstr(strtolower($r["Foto1"]), "http://")) {
                          $foto = $r["FotoDestacada"];
                      } else {
                          $foto = IMGPRODUCTO_ROOT . $r["Foto1"];
                      }

                      //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                  else :
                      $foto = "";
                  endif;

                  $lista_producto["Foto"] = $foto;
                  array_push($response, $lista_producto);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = $dbo->rows($qry) . "Encontrados";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_fechas_domicilio($IDClub, $Version = "", $IDRestaurante = "")
    {

            $dbo = &SIMDB::get();
            $response = array();

            $fecha_actual = date('Y-m-j');
            $fecha_final = strtotime('+120 day', strtotime($fecha_actual));
            $fecha_final = date('Y-m-j', $fecha_final);
            $fechaInicio = strtotime($fecha_actual);
            $fechaFin = strtotime($fecha_final);
            $FechahoraActual = date("Y-m-d H:i:s");
            $FechahoraPermitida = date("Y-m-d 19:00:00");

            //Especial rancho san francisco
            if ($IDClub == 34) {
                if (strtotime($FechahoraActual) <= strtotime($FechahoraPermitida)) {
                    $suma_dia = 1;
                } else {
                    $suma_dia = 2;
                }

                $fechaInicio = strtotime($fecha_actual . "+ " . $suma_dia . " days");
            }

            if ($IDClub == 23 && $Version == 2) {
                $fechaInicio = strtotime($fecha_actual);
                $fechaFin = strtotime($fecha_actual);
            }

            //Especial country en domicilios 3 dos dias minimo para entrega
            if ($IDClub == 44 && $Version == 3) {
                $suma_dia = 2;
                $fechaInicio = strtotime($fecha_actual . "+ " . $suma_dia . " days");
            }

            $contador = 1;
            $primera_fecha = 1;
            $flag_disponible_hoy = 0;

            if (!empty($IDRestaurante)):
                $condicion = " and (IDRestauranteDomicilio  = '" . $IDRestaurante . "' or IDRestauranteDomicilio = '')";
            endif;

            for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
                $fecha_validar = date("Y-m-d", $i);
                $fecha_fin_validar = date("Y-m-d", $fechaFin);
                //Consulto la disponibilidad en este dia
                $dia_semana = date('w', strtotime($fecha_validar));
                $sql_dispo_elemento_gral = "Select * From ConfiguracionDomicilios" . $Version . " Where Dias like '%" . $dia_semana . "|%' and Activo = 'S' and IDClub = '" . $IDClub . "' " . $condicion . " Order By IDConfiguracionDomicilios Desc Limit 1";
                $qry_disponibilidad = $dbo->query($sql_dispo_elemento_gral);
                $r_disponibilidad = $dbo->fetchArray($qry_disponibilidad);
                $fecha_cierre = 0;
                $sql = "SELECT * FROM  ClubFechaCierre WHERE Fecha = '" . date("Y-m-d", $i) . "' and IDClub = '" . $IDClub . "'";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $fecha_cierre = 1;
                } //end else

                // si no permite la fecha de hoy no la muestro
                if ($r_disponibilidad["PedidoMismoDia"] == "N" && strtotime($fecha_validar) == strtotime($fecha_actual)) {
                    $fecha_cierre = 1;
                }

                if ($IDClub == 34 && $dia_semana == 1) {
                    $fecha_cierre = 1;
                }

                //Especial arrayanes solo fecha de entrega martes - jueves y sabado
                if ($IDClub == 11 && ($dia_semana == 1 || $dia_semana == 0 || strtotime($fecha_validar) == strtotime($fecha_actual))) {
                    $fecha_cierre = 1;
                }
                //Fin arrayanes

                //Especial israel no sabado ni domingos
                if ($IDClub == 98 && ($dia_semana == 0 || $dia_semana == 6)) {
                    $fecha_cierre = 1;
                }
                //Fin arrayanes

                //Especial country solo domingos
                //Especial israel no sabado ni domingos
                if ($IDClub == 44 && ($dia_semana == 1000)) {
                    $fecha_cierre = 1;
                }
                //Fin arrayanes

                $fecha_domicilio = date('Y-m-d', $i);
                $configuracion["IDClub"] = $IDClub;
                $configuracion["Fecha"] = $fecha_domicilio;
                if ((int) $r_disponibilidad["IDConfiguracionDomicilios"] > 0 && $fecha_cierre == 0):
                    array_push($response, $configuracion);
                endif;
            }

            if (count($configuracion) > 0):
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else:
                $respuesta["message"] = "No se encontraron configuracion para pedidos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;

        } // fin function

        public function get_horas_entrega($IDClub, $Fecha, $Version = "", $IDRestaurante = "")
    {

            $dbo = &SIMDB::get();

            if (empty($Fecha)):
                $dia_fecha = date("w");
                $Fecha = date("Y-m-d");
            else:
                $dia_fecha = date("w", strtotime($Fecha));
            endif;

            if (!empty($IDRestaurante)):
                $condicion = " and (IDRestauranteDomicilio  = '" . $IDRestaurante . "' or IDRestauranteDomicilio = '')";
            endif;

            $response = array();
            $sql = "SELECT * FROM ConfiguracionDomicilios" . $Version . "  WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and Dias like '%" . $dia_fecha . "|%' " . $condicion;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $hora_inicio = $r["HoraInicioEntrega"];
                    $hora_fin = $r["HoraFinEntrega"];
                    $intervalo = $r["IntervaloEntrega"];
                    $hora_final = strtotime($Fecha . " " . $hora_fin);
                    $hora_actual = strtotime($Fecha . " " . $hora_inicio);

                    //Si es hoy le sumo el tiempo minimo para pedir
                    if ($Fecha == date("Y-m-d")) {
                        $TiempoMinimoPedido = $r["TiempoMinimoPedido"];
                    } else {
                        $TiempoMinimoPedido = 0;
                    }

                    $hora_fecha_actual = strtotime($Fecha . " " . date('H:i:s'));
                    $hora_fecha_actual = strtotime('+' . $TiempoMinimoPedido . ' minute', $hora_fecha_actual);

                    while ($hora_actual <= $hora_final):

                        // Si es hoy solo devuelvo desde la hora mayor de lo contrario devuelvo todas las horas
                        if ($Fecha == date("Y-m-d") && $hora_actual >= $hora_fecha_actual):
                            $flag_hora_valida = 1;
                        elseif ($Fecha == date("Y-m-d")):
                            $flag_hora_valida = 0;
                        elseif ($Fecha != date("Y-m-d")):
                            $flag_hora_valida = 1;
                        endif;

                        $configuracion["IDClub"] = $r["IDClub"];
                        $configuracion["Fecha"] = $Fecha;
                        $configuracion["Hora"] = date("H:i", $hora_actual);
                        $hora_actual = strtotime('+' . $intervalo . ' minute', $hora_actual);
                        if ($flag_hora_valida == 1):
                            array_push($response, $configuracion);
                        endif;
                    endwhile;

                } //ednw hile

                if (count($configuracion) > 0):
                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                else:
                    $respuesta["message"] = "La fecha de hoy no esta disponible para Pedidos";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                endif;
            } //End if
        else {
                $respuesta["message"] = "La fecha de hoy no esta disponible para Pedidos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function imprime_recibo_domicilio($IDDomicilio, $Version = "")
    {

            /* Change to the correct path if you copy this example! */
            require LIBDIR . '/../impresionremota/autoload.php';

            $dbo = &SIMDB::get();
            $datos_domicilio = $dbo->fetchAll("Domicilio" . $Version, " IDDomicilio = '" . $IDDomicilio . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_domicilio["IDSocio"] . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_domicilio["IDClub"] . "' ", "array");

            //Averiguar si tiene config de impresora
            if ($datos_domicilio["IDRestauranteDomicilio"] > 0) {

                $datos_restaurante = $dbo->fetchAll("RestauranteDomicilio" . $Version, " IDRestauranteDomicilio = '" . $datos_domicilio["IDRestauranteDomicilio"] . "' ", "array");
                $RestauranteDom = $datos_restaurante["Nombre"];
                if (!empty($datos_restaurante["Ipimpresora"]) && !empty($datos_restaurante["PuertoImpresora"])) {
                    $IPImpresora = $datos_restaurante["Ipimpresora"];
                    $PuertoImpresora = $datos_restaurante["PuertoImpresora"];
                }
            }

            //Verifico si la config tiene la impresora
            $datos_config = $dbo->fetchAll("ConfiguracionDomicilios" . $Version, " IDClub = '" . $datos_domicilio["IDClub"] . "' Limit 1", "array");
            if (empty($IPImpresora)) {
                if (!empty($datos_config["Ipimpresora"]) && !empty($datos_config["PuertoImpresora"])) {
                    $IPImpresora = $datos_config["Ipimpresora"];
                    $PuertoImpresora = $datos_config["PuertoImpresora"];
                } else {
                    //Tomo la impresora  de los datos del club
                    $IPImpresora = $datos_club["IPImpresora"];
                    $PuertoImpresora = $datos_club["PuertoImpresora"];
                }
            }
            //Fin Impresora

            if (!empty($datos_domicilio["IDDomicilio"]) && !empty($IPImpresora) && !empty($PuertoImpresora)):
                try {
                    $nombre_socio = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                    $accion_socio = $datos_socio["Accion"];
                    $celular_socio = $datos_socio["Celular"];
                    $hora_entrega = $datos_domicilio["HoraEntrega"];
                    $numero_pedido = $datos_domicilio["Numero"];
                    $NumeroMesa = $datos_domicilio["NumeroMesa"];
                    $hora_solicitud = $datos_domicilio["FechaTrCr"];
                    $Direccion = $datos_domicilio["Direccion"];
                    $FormaPago = $datos_domicilio["FormaPago"];
                    $Propina = $datos_domicilio["Propina"];
                    $pedido = "";
                    $sql = "SELECT * FROM DomicilioDetalle" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
                    $qry = $dbo->query($sql);
                    while ($r = $dbo->fetchArray($qry)) {
                        $pedido .= $r["Producto"] = utf8_encode($dbo->getFields("Producto" . $Version, "Nombre", "IDProducto = '" . $r["IDProducto"] . "'") . " " . $dbo->getFields("Producto" . $Version, "Descripcion", "IDProducto = '" . $r["IDProducto"] . "'"));
                        $pedido .= ":" . $r["Cantidad"] . "\n" . "Comentario: " . utf8_encode($r["Comentario"]) . "\n";

                        $sql_carac = "SELECT DC.*,PP.Nombre as Categoria, CP.Nombre as Caracteristica
												FROM DomicilioCaracteristica DC, CaracteristicaProducto CP, PropiedadProducto PP
												WHERE  DC.IDCaracteristicaProducto = CP.IDCaracteristicaProducto and PP.IDPropiedadProducto = DC.IDPropiedadProducto and
												IDDomicilio = '" . $IDDomicilio . "' and IDProducto = '" . $r["IDProducto"] . "'
												ORDER BY PP.Nombre";
                        $r_carac = $dbo->query($sql_carac);
                        while ($row_carac = $dbo->FetchArray($r_carac)) {
                            $pedido .= $row_carac["Categoria"] . " : " . $row_carac["Caracteristica"] . "\n";
                        }

                    } //ednw hile

                    $comentarios = $datos_domicilio["ComentariosSocio"];

                    if (empty($Version)) {
                        $Version = 1;
                    }

                    switch ($Version) {
                        case '1':
                            $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '33' and IDClub = '" . $datos_club["IDClub"] . "' ");
                            break;
                        case '2':
                            $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '98'and IDClub = '" . $datos_club["IDClub"] . "'  ");
                            break;
                        case '3':
                            $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '112' and IDClub = '" . $datos_club["IDClub"] . "' ");
                            break;
                        case '4':
                            $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '113' and IDClub = '" . $datos_club["IDClub"] . "' ");
                            break;
                    }

                    if (empty($NombreModulo)) {
                        $NombreModulo = "Pedidos";
                    }

                    $array_ip = explode(",", $IPImpresora);
                    $array_puerto = explode(",", $PuertoImpresora);
                    $contador_pos = 0;

                    foreach ($array_ip as $IPImpresora) {

                        //echo $IPImpresora ." " . $array_puerto[$contador_pos];

                        $connector = new NetworkPrintConnector($IPImpresora, $array_puerto[$contador_pos]);
                        $printer = new Printer($connector);
                        $printer->initialize();
                        $printer->text("\n");
                        $printer->text("\n");
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->setTextSize(2, 2);
                        $printer->text($datos_club["Nombre"] . " \n");
                        $printer->setTextSize(2, 2);
                        $printer->text($NombreModulo . "\n");
                        if (!empty($RestauranteDom)) {
                            $printer->setTextSize(2, 2);
                            $printer->text($RestauranteDom . "\n");
                        }
                        //$printer -> text("App para todos\n");
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->setTextSize(1, 1);
                        $printer->text("Numero: " . $numero_pedido . "\n");
                        $printer->text("Nombre Socio: " . $nombre_socio . "\n");
                        $printer->setTextSize(1, 2);
                        $printer->text("Numero Accion: " . $accion_socio . "\n");
                        $printer->setTextSize(1, 2);
                        $printer->text("Celular: " . $celular_socio . "\n");
                        $printer->setTextSize(1, 1);
                        $printer->text("Hora Solicitud:" . $hora_solicitud . " \n");
                        if ($datos_config["SolicitaHoraDomicilio"] == "S") {
                            $printer->text("Hora Entrega:" . $hora_entrega . " \n");
                        }
                        $printer->text("Lugar:" . $Direccion . " \n");
                        $printer->text("Medio Pago:" . $FormaPago . " \n");
                        $printer->setTextSize(1, 2);
                        $printer->text("Numero Mesa:" . $NumeroMesa . " \n");
                        $printer->setJustification(Printer::JUSTIFY_LEFT);
                        $printer->text("Propina (" . $datos_config["LabelPropina"] . "): " . $Propina . " \n");
                        $printer->setJustification(Printer::JUSTIFY_LEFT);
                        $printer->setTextSize(1, 2);
                        $printer->text("Descripcion Pedido: \n\n");
                        $printer->setTextSize(1, 1);
                        $printer->text($pedido . "\n");
                        $printer->setTextSize(2, 1);
                        $printer->text("Comentarios: \n");
                        $printer->setTextSize(2, 1);
                        $printer->text($comentarios . " \n");
                        if ($datos_socio["PermiteDomicilios"] == "N") {
                            $printer->setTextSize(1, 2);
                            $printer->text("Pendiente verificacion pago" . " \n");
                        }
                        $printer->cut();
                        $printer->close();

                        $contador_pos++;
                    }

                    //$connector = new NetworkPrintConnector("181.48.188.75", 6000);
                    //$connector = new NetworkPrintConnector($IPImpresora, $PuertoImpresora);
                    /* Print a "Hello world" receipt" */
                    //return true;
                } catch (Exception $e) {
                    //echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";

                }
            endif;

            return true;

        }

        public function get_certificado($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
            $dbo = &SIMDB::get();

            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            $RutaExtractos = EXTRACTOSRANCHO_DIR . "/certificados/";

            //$AccionSocio="001-0241-7-1";

            self::borrar_extracto_tmp();

            if (is_dir($RutaExtractos)) {
                if ($dir = opendir($RutaExtractos)) {
                    while (($archivo = readdir($dir)) !== false) {
                        if (is_dir($RutaExtractos . $archivo)) {
                            if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                                while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                    if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                        //Capturo Accion Socio
                                        $array_nombre_archivo = explode(".", $archivo_carpeta);
                                        $accion_socio_pdf = $array_nombre_archivo[0];
                                        if ($AccionSocio == $accion_socio_pdf):
                                            //$array_pdf[$archivo]=$archivo_carpeta;
                                            //lo copio con nombre encriptado para tenerlo temporalmente
                                            $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                            $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                            $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                            copy($origen_copy, $destino_copy);
                                            $array_pdf[$archivo] = $nombre_encriptado;
                                        endif;
                                    }
                                }
                            }
                            closedir($carpeta_mes);
                        }
                    }
                    closedir($dir);
                }
            }

            krsort($array_pdf);

            foreach ($array_pdf as $fecha => $archivo_extracto):
                $nombre_categoria = "Extractos";
                $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $archivo_extracto;
                $factura["NumeroFactura"] = $fecha;
                $factura["Fecha"] = $fecha_extracto;
                $factura["ValorFactura"] = "Extracto";
                $factura["Almacen"] = "";
                $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
                $array_categoria_factura[$nombre_categoria][] = $factura;

            endforeach;

            $response = array();
            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas):

                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            $datos_facturas["BuscadorFechas"] = "N";
            $datos_facturas["Categorias"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            // cerrar la conexión ftp
            ftp_close($conn_id);

            return $respuesta;
        }

        public function borrar_extracto_tmp()
    {

            $RutaExtractosTMP = EXTRACTOSTMP_DIR;

            if (is_dir($RutaExtractosTMP)) {
                if ($dir = opendir($RutaExtractosTMP)) {
                    while (($archivo = readdir($dir)) !== false) {
                        if (is_dir($RutaExtractosTMP . $archivo)) {
                            if ($carpeta_mes = opendir($RutaExtractosTMP . $archivo)) {
                                while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                    if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {

                                        $pos = strpos($archivo_carpeta, ".pdf");
                                        if ($pos === false) {
                                        } else {
                                            $array_nombre_archivo = explode("-", $archivo_carpeta);
                                            if (count($array_nombre_archivo) > 1):
                                                $fecha_hora_archivo = strtotime($array_nombre_archivo[0]);
                                                //10 minutos antes
                                                $fecha = date('Y-m-j H:i:s');
                                                $nuevafecha = strtotime('-10 minutes', strtotime($fecha));
                                                if ($fecha_hora_archivo <= $nuevafecha && !empty($archivo_carpeta)):
                                                    $ruta_borrar = EXTRACTOSTMP_DIR . $archivo_carpeta;
                                                    unlink($ruta_borrar);
                                                endif;
                                            endif;
                                        }
                                    }
                                }
                            }
                            closedir($carpeta_mes);
                        }
                    }
                    closedir($dir);
                }
            }
            return $respuesta;
        }

        public function set_calificacion_pqr($IDClub, $IDSocio, $IDPqr, $Comentario, $Calificacion)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr) && !empty($IDSocio) && !empty($Calificacion)) {

                $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");
                if ($datos_pqr["Calificacion"] == 0) {
                    $sql_pqr = $dbo->query("Update Pqr Set Calificacion = '" . $Calificacion . "', ComentarioCalificacion = '" . $Comentario . "', FechaCalificacion = NOW() Where IDPqr = '" . $IDPqr . "'");
                    SIMUtil::noticar_calificacion_pqr($IDPqr, $Calificacion);
                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "Ya se había registrado una calificación";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "120. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_calificacion_directorio($IDClub, $IDSocio, $IDDirectorio, $Comentario, $Calificacion)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDDirectorio) && !empty($IDSocio) && !empty($Calificacion)) {
                $sql_directorio = $dbo->query("Insert into DirectorioCalificacion (IDSocio, IDDirectorio, Calificacion, ComentarioCalificacion, Publicar, Fecha) Values ('" . $IDSocio . "','" . $IDDirectorio . "','" . $Calificacion . "','" . $Comentario . "','S',NOW())");
                //SIMUtil::noticar_calificacion($IDDirectorio,$Comentario);
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "130. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_calificacion_directorio_socios($IDClub, $IDSocio, $IDDirectorio, $Comentario, $Calificacion)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDDirectorio) && !empty($IDSocio) && !empty($Calificacion)) {
                $sql_directorio = $dbo->query("Insert into DirectorioSocioCalificacion (IDSocio, IDDirectorioSocio, Calificacion, ComentarioCalificacion, Publicar, Fecha) Values ('" . $IDSocio . "','" . $IDDirectorio . "','" . $Calificacion . "','" . $Comentario . "','S',NOW())");
                //SIMUtil::noticar_calificacion_socio($IDDirectorio,$Comentario);
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "131. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_calificacion_directorio($IDClub, $IDDirectorio)
    {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM DirectorioCalificacion WHERE IDDirectorio = '" . $IDDirectorio . "' and Publicar = 'S' ORDER BY IDDirectorioCalificacion Desc";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "'", "array");
                    $calificacion["IDDirectorioCalificacion"] = $r["IDDirectorioCalificacion"];
                    $calificacion["IDSocio"] = $r["IDSocio"];
                    $calificacion["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $calificacion["Calificacion"] = $r["Calificacion"];
                    $calificacion["ComentarioCalificacion"] = utf8_encode($r["ComentarioCalificacion"]);
                    $calificacion["Fecha"] = $r["Fecha"];
                    array_push($response, $calificacion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        }

        public function get_calificacion_directorio_socios($IDClub, $IDDirectorio)
    {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM DirectorioSocioCalificacion WHERE IDDirectorioSocio = '" . $IDDirectorio . "' and Publicar = 'S' ORDER BY IDDirectorioSocioCalificacion Desc";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "'", "array");
                    $calificacion["IDDirectorioCalificacion"] = $r["IDDirectorioCalificacion"];
                    $calificacion["IDSocio"] = $r["IDSocio"];
                    $calificacion["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $calificacion["Calificacion"] = $r["Calificacion"];
                    $calificacion["ComentarioCalificacion"] = $r["ComentarioCalificacion"];
                    $calificacion["Fecha"] = $r["Fecha"];
                    array_push($response, $calificacion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        }

        public function set_reserva_cumplida($IDClub, $IDSocio, $IDUsuario, $IDReservaGeneral, $ReservaCumplida, $ReservaCumplidaSocio, $Observacion, $Invitados)
    {
            $dbo = &SIMDB::get();

            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDUsuario) && !empty($IDReservaGeneral) && !empty($ReservaCumplida) && !empty($ReservaCumplidaSocio)) {

                $datos_invitado = json_decode($Invitados, true);
                //Recorro los datos de los invitados
                if (count($datos_invitado) > 0):
                    foreach ($datos_invitado as $detalle_datos):
                        $IDReservaGeneralInvitado = $detalle_datos["IDReservaGeneralInvitado"];
                        $ReservaCumplidaInvitado = $detalle_datos["ReservaCumplidaInvitado"];
                        if ($ReservaCumplidaInvitado == "S") {
                            $invitado_asiste++;
                        }

                        $sql_actualiza_invitado = "Update ReservaGeneralInvitado Set Cumplida = '" . $ReservaCumplidaInvitado . "' Where IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "' and IDReservaGeneral = '" . $IDReservaGeneral . "'";
                        $dbo->query($sql_actualiza_invitado);
                    endforeach;
                endif;

                if (count($datos_invitado) > 0 && $invitado_asiste != count($datos_invitado) || $ReservaCumplidaSocio == "N") {
                    $estado_reserva_cumplida = "P";
                } else {
                    $estado_reserva_cumplida = $ReservaCumplida;
                }

                //Actualizo Estado de reserva
                $sql_reserva_estado = "Update ReservaGeneral Set Cumplida = '" . $estado_reserva_cumplida . "', CumplidaCabeza = '" . $ReservaCumplidaSocio . "', FechaCumplida = NOW(), IDUsuarioCumplida = '" . $IDUsuario . "', ObservacionCumplida = '" . $Observacion . "' Where IDReservaGeneral = '" . $IDReservaGeneral . "'";
                $dbo->query($sql_reserva_estado);

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "121. Atencion faltan parametros ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_reconfirma_reserva($IDClub, $IDSocio, $IDReserva)
    {
            $dbo = &SIMDB::get();
            $MinimoInvitados = 2;
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {
                //Actualizo Estado de reserva
                $sql_invitados_reserva = $dbo->query("SELECT count(*) as TotalConfirmado FROM ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "' and Confirmado = 'S' ");
                $r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva);
                if ((int) $r_invitados_reserva["TotalConfirmado"] >= $MinimoInvitados) {
                    $sql_reserva_estado = "UPDATE ReservaGeneral Set ConfirmadaSocio = 'S', FechaConfirmadaSocio = NOW() Where IDReservaGeneral = '" . $IDReserva . "'";
                    $dbo->query($sql_reserva_estado);
                    $respuesta["message"] = "Su reserva fue confirmada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "No es posible confirmar la reserva sin al menos " . $MinimoInvitados . " invitados confirmados";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "121. Atencion faltan parametros ";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        }

        public function set_invitado_grupo_servicio($IDClub, $IDSocio, $IDReserva, $Invitados)
    {
            $dbo = &SIMDB::get();
            //if( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDReserva ) ){
            if (!empty($IDClub) && !empty($IDReserva)) {

                //solo se puede hacer confirmaciones antes de las 4pm del jueves
                if ($IDClub == 112 || $IDClub == 8) {
                    $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral  = '" . $IDReserva . "' ", "array");
                    $Hoy = date("Y-m-d H:i:s");
                    $Fecha = $datos_reserva["Fecha"];
                    $FechaMaxima = strtotime('-2 day', strtotime($Fecha));
                    $FechaMaxima = date("Y-m-d 16:00:00", $FechaMaxima);
                    if ($Hoy <= $FechaMaxima) {
                        //Bien
                    } else {
                        $respuesta["message"] = "El tiempo limite para confirmar reserva fue superado (Jueves 16:00:00)";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                $datos_invitado = json_decode($Invitados, true);
                $TotalConfirmados = 0;
                //Recorro los datos de los invitados
                if (count($datos_invitado) > 0):
                    foreach ($datos_invitado as $detalle_datos):
                        $IDReservaGeneralInvitado = $detalle_datos["IDReservaGeneralInvitado"];
                        $SeleccionadoGrupo = $detalle_datos["SeleccionadoGrupo"];
                        if ($SeleccionadoGrupo == "S") {
                            $TotalConfirmados++;
                        }

                        if ($TotalConfirmados <= 4) {
                            $sql_actualiza_invitado = "UPDATE ReservaGeneralInvitado Set Confirmado = '" . $SeleccionadoGrupo . "' Where IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "' and IDReservaGeneral = '" . $IDReserva . "'";
                            $dbo->query($sql_actualiza_invitado);
                        }
                    endforeach;
                endif;

                if ($TotalConfirmados <= 4) {
                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "Atencion solo es posible confirmar maximo a 4 invitados";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "CF1. Atencion faltan parametros " . $IDClub . "-" . $IDSocio . "-" . $IDReserva;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_edita_auxiliar_reserva($IDClub, $IDSocio, $IDReservaGeneral, $ListaAuxiliar)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReservaGeneral) && !empty($ListaAuxiliar)) {

                //Verifico de nuevo que la lista de auxiliares seleccionados esten disponibles
                if (!empty($ListaAuxiliar)):
                    $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
                    $datos_auxiliares_revisar = json_decode($ListaAuxiliar, true);
                    $response_dispo_aux = SIMWebService::get_auxiliares($IDClub, $datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["Hora"]);
                    foreach ($response_dispo_aux["response"] as $datos_conf_aux):
                        foreach ($datos_conf_aux["Auxiliares"] as $datos_aux):
                            $array_aux_disponibles[] = $datos_aux["IDAuxiliar"];
                        endforeach;
                    endforeach;

                    if (count($datos_auxiliares_revisar) > 0):
                        foreach ($datos_auxiliares_revisar as $key_aux => $auxiliar_seleccionado):
                            if (!in_array($auxiliar_seleccionado["IDAuxiliar"], $array_aux_disponibles)):
                                $respuesta["message"] = "El auxiliar " . $auxiliar_seleccionado["Nombre"] . " no esta disponible en ese horario";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                        endforeach;
                    endif;
                endif;

                $datos_auxiliares = json_decode($ListaAuxiliar, true);
                if (count($datos_auxiliares) > 0):
                    $cantidad_auxiliar = count($datos_auxiliares);
                    //$ArrayAuxiliar = implode(",",$datos_auxiliares);
                    foreach ($datos_auxiliares as $key_aux => $auxiliar_seleccionado):
                        $array_id_auxiliar[] = $auxiliar_seleccionado["IDAuxiliar"];

                    endforeach;
                    if (count($array_id_auxiliar) > 0):
                        $IDAuxiliar = implode(",", $array_id_auxiliar) . ",";
                    endif;
                endif;

                //Actualizo Estado de reserva
                $sql_reserva_estado = "Update ReservaGeneral Set IDAuxiliar = '" . $IDAuxiliar . "' Where IDReservaGeneral = '" . $IDReservaGeneral . "' Limit 1";
                $dbo->query($sql_reserva_estado);

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "A121. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function valida_pago_reserva($IDClub, $IDSocio, $IDReservaGeneral)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM ReservaGeneral WHERE IDReservaGeneral = '" . $IDReservaGeneral . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4): // payU
                        if ($r["EstadoTransaccion"] == ""):
                            $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;

                        elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4"):
                            $respuesta["message"] = "Reserva pagada correctamente";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        else:
                            $respuesta["message"] = "El pago no fue realizado";
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        endif;
                    else:
                        $respuesta["message"] = "La reserva no fue pagada por pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    endif;
                }

            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function valida_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $Version)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM Domicilio" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4 || $r["IDTipoPago"] == 5): // payU
                        if ($r["EstadoTransaccion"] == ""):
                            $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;

                        elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4" || $r["EstadoTransaccion"] == "AUTHORISED" || $r["EstadoTransaccion"] == "Aprobada"):
                            $respuesta["message"] = "Domicilio pagado correctamente";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        else:
                            $respuesta["message"] = "El pago no fue realizado";
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        endif;
                    else:
                        $respuesta["message"] = "El domicilio no fue pagado por pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    endif;
                }

            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function verifica_place_to_pay($IDClub, $IDSocio, $IDEventoRegistro)
    {

            $dbo = &SIMDB::get();
            $transaccion_pendiente = "";
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $datos_transaccion = $dbo->fetchAll("PeticionesPlacetoPay", " tipo='EventoRegistro' and IDClub = '" . $IDClub . "' and IDMaestro = '" . $IDEventoRegistro . "' and estado_transaccion = 'OK' ", "array");
            $login = $datos_club["ApiLogin"];
            $secretKey = $datos_club["ApiKey"];

            if ((int) $datos_transaccion["IDPeticionesPlacetoPay"] > 0) {
                //obtención de nonce
                if (function_exists('random_bytes')) {
                    $nonce = bin2hex(random_bytes(16));
                } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $nonce = bin2hex(openssl_random_pseudo_bytes(16));
            } else {
                $nonce = mt_rand();
            }
            $nonceBase64 = base64_encode($nonce);

            $seed = date('c');
            $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

            $auth = array(
                "auth" => array(
                    "login" => $login,
                    "seed" => $seed,
                    "nonce" => $nonceBase64,
                    "tranKey" => $tranKey),
            );

            if ($datos_club["IsTest"] == 1) {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
            } else {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
            }
            $url_place_to_pay . 'redirection/api/session/' . $datos_transaccion["request_id"];
            $ch = curl_init($url_place_to_pay . 'redirection/api/session/' . $datos_transaccion["request_id"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = ['Content-Type:application/json; charset=utf-8'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth));
            $response = curl_exec($ch);
            curl_close($ch);
            // do anything you want with your response
            $respuesta = json_decode($response, true);

            if ($respuesta["status"]["status"] == "PENDING") {
                $transaccion_pendiente = 1;
            }
        }

        return $transaccion_pendiente;
    }

    public function valida_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $Version = "")
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM EventoRegistro" . $Version . " WHERE IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                //para place to pay verifico que no se encuentre en estado pendiente
                $verifica_estado_ptp = self::verifica_place_to_pay($IDClub, $IDSocio, $IDEventoRegistro);
                if ($verifica_estado_ptp == 1) {
                    $respuesta["message"] = "La transaccion se encuentra pendiente de aprobacion de la entidad financiera";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4): // payU
                    if ($r["EstadoTransaccion"] == ""):
                        $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4"):
                        $respuesta["message"] = "Reserva pagada correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else:
                        $respuesta["message"] = "El pago no fue realizado";
                        $respuesta["success"] = false;
                        $respuesta["response"] = $response;
                    endif;
                else:
                    $respuesta["message"] = "La reserva no fue pagada por pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            }

        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function get_factura_ftp2($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
            $dbo = &SIMDB::get();

            $host = '190.66.22.93';
            $user = 'miclubapp';
            $pass = 'Miclub2017';

            //$AccionSocio="0001";

            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");

            /// set up basic connection
            $conn_id = ftp_connect($host, "64007");

            //Comprobar que la conexión ha tenido éxito
            if (!$conn_id) {
                $respuesta["message"] = "Lo sentimos no hay conexion a la base de extractos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            // login with username and password
            $login_result = ftp_login($conn_id, $user, $pass);

            // enabling passive mode
            ftp_pasv($conn_id, true);

            // Obtener los archivos contenidos en el directorio actual
            $files = ftp_nlist($conn_id, "");

            foreach ($files as $file):
                if (ftp_size($conn_id, $file) == -1): //Es directorio
                    $array_directorio[] = $file;
                    //Ingreso a ese directorio y consulto los archivos
                    $directorio = ftp_nlist($conn_id, $file);
                    foreach ($directorio as $archivo):
                        $ruta_original = $archivo;
                        $archivo = str_replace($file . "/", "", $archivo);
                        //Capturo Accion Socio
                        $array_nombre_archivo = explode("_", $archivo);
                        $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[1]);
                        //Comparo si el archivo pertenece al socio a consultar
                        if ($AccionSocio == $accion_socio_pdf):

                            //$guardar_local = EXTRACTOS_DIR."/".$IDSocio.$file."extractor.pdf";
                            //$ruta_local = $IDSocio.$file."extractor.pdf";
                            $ruta_local = $ruta_original;
                            // descargar $server_file y guardarlo en $local_file
                            //ftp_get($conn_id, $guardar_local, $ruta_original, FTP_BINARY);

                            /*
                            if (ftp_get($conn_id, $guardar_local, $ruta_original, FTP_BINARY)) {
                            echo "Se ha guardado satisfactoriamente en $local_file <br>";
                            } else {
                            echo "Ha habido un problemaaa<br>";
                            }
                             */

                            $array_pdf[$file] = $ruta_local;

                        endif;
                    endforeach;
                endif;
            endforeach;

            foreach ($array_pdf as $fecha => $archivo_extracto):

                $nombre_categoria = "Extractos";

                $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $archivo_extracto;
                $factura["NumeroFactura"] = $fecha;
                $factura["Fecha"] = $fecha_extracto;
                $factura["ValorFactura"] = "Extracto";
                $factura["Almacen"] = "";

                $array_categoria_factura[$nombre_categoria][] = $factura;

            endforeach;

            $response = array();
            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas):

                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            $datos_facturas["BuscadorFechas"] = "N";
            $datos_facturas["Categorias"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            // cerrar la conexión ftp
            ftp_close($conn_id);

            return $respuesta;
        }

        public function get_detalle_factura_zeus($IDClub, $IDFactura, $NumeroFactura)
    {

            //Valido si es el detalle del extracto o el de los movimientos
            $pos = strpos($IDFactura, "Extracto");
            if ($pos === false) { //Consulta movimientos
                $array_movimiento = explode("Movimiento", $IDFactura);
                $Mes = $array_movimiento[1];
                $IDSocio = $array_movimiento[2];
                if (strlen($Mes) <= 1) {
                    $Mes = "0" . $Mes;
                }

                if ($IDSocio == "84887" || $IDSocio == "5533"):
                    $IDSocio = "85176";
                endif;

                $respuesta = SIMWebServiceZeus::consulta_movimiento($IDClub, $IDSocio, $Mes);
            } else { // Consulta extractos
                $array_extracto = explode("Extracto", $IDFactura);
                $IDSocio = array_pop($array_extracto);

                if ($IDSocio == "84887" || $IDSocio == "5533"):
                    $IDSocio = "85176";
                endif;

                $respuesta = SIMWebServiceZeus::consulta_extracto($IDClub, $IDSocio);
            }
            return $respuesta;

        }

        public function get_detalle_factura_app2($IDClub, $IDFactura, $NumeroFactura)
    {
            $dbo = &SIMDB::get();

            $host = '190.66.22.93';
            $user = 'miclubapp';
            $pass = 'Miclub2017';

            //$AccionSocio="0001";

            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");

            /// set up basic connection
            $conn_id = ftp_connect($host, "64007");

            //Comprobar que la conexión ha tenido éxito
            if (!$conn_id) {
                $respuesta["message"] = "Lo sentimos no hay conexion a la base de extractos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            $carpeta_archivo_ftp = substr($IDFactura, 0, 6);
            $archivo_ftp = substr($IDFactura, 6, 3) . "_" . substr($IDFactura, 9);
            $ruta_archivo_ftp = $carpeta_archivo_ftp . "/" . $archivo_ftp;

            $guardar_local = EXTRACTOS_DIR . "/" . $IDClub . "_" . $carpeta_archivo_ftp . "_" . $archivo_ftp;
            $ruta_local = EXTRACTOS_ROOT . "/" . $IDClub . "_" . $carpeta_archivo_ftp . "_" . $archivo_ftp;

            // login with username and password
            $login_result = ftp_login($conn_id, $user, $pass);

            // enabling passive mode
            ftp_pasv($conn_id, true);

            ftp_get($conn_id, $guardar_local, $ruta_archivo_ftp, FTP_BINARY);

            // cerrar la conexión ftp
            ftp_close($conn_id);

            $response = array();

            $cuerpo_factura = '<!doctype html>
							<html>
							<head>
							<meta charset="UTF-8">
							<title>Detalle Extracto</title>

							</head>
							<body>
								<a href="' . $ruta_local . '">
								Pulse aca para ver el Extracto
								</a>
							</body>
						</html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;

            array_push($response, $factura);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        }

        public function get_factura_mi_conjunto($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
            $dbo = &SIMDB::get();
            $IDClub = 71;

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $datos_socio["Predio"];
            $predio = str_replace("-", "", $datos_socio["Predio"]);

            $nombre_categoria = "Estado Cuenta";
            $fecha_extracto = date("Y");

            $sql_valor = "SELECT SUM(Debito) as ValorPagar,month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
            $r_valor = $dbo->query($sql_valor);
            while ($row_valor = $dbo->fetchArray($r_valor)) {
                $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
                $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-30";
                //Consulto si tiene saldo en Cartera
                $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
                $r_cartera = $dbo->query($sql_cartera);
                $row_cartera = $dbo->fetchArray($r_cartera);
                if ((int) $row_cartera["Total"] > 0) {
                    $row_valor["ValorPagar"] += $row_cartera["Total"];
                }

                $factura["IDClub"] = $IDClub;
                $valor_mes = (int) $row_valor["Mes"] - 1;
                $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
                $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
                $factura["Fecha"] = date("Y");
                $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
                $factura["Almacen"] = $row_valor["Detalle"];

                $array_categoria_factura[$nombre_categoria][] = $factura;
            }

            $response = array();
            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas):
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            $datos_facturas["BuscadorFechas"] = "N";
            $datos_facturas["Categorias"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;
        }

        public function get_detalle_factura_mi_conjunto($IDClub, $IDFactura, $NumeroFactura)
    {
            $IDClub = 71;
            $dbo = &SIMDB::get();
            $array_dato_factura = explode("-", $IDFactura);
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $predio = str_replace("-", "", $datos_socio["Predio"]);

            $year_consulta = $array_dato_factura[0];
            $mes_consulta = $array_dato_factura[1];

            $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
            $r_valor = $dbo->query($sql_valor);
            $contador = 1;
            while ($row_valor = $dbo->fetchArray($r_valor)) {

                if ((int) $row_valor["Debito"] > 0) {
                    $descripcion_concepto = $row_valor["Nombre"];
                    $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                    $valor_mostrar = $row_valor["Debito"];
                }

                if ((int) $row_valor["Credito"] > 0) {
                    $descripcion_concepto = $row_valor["Detalle"];
                    $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                    $valor_mostrar = "-" . $row_valor["Credito"];
                }

                if ($array_valor[$row_valor["Nombre"]] > 0) {

                    $valor_total += $row_valor["Debito"];
                    if ($contador % 2) {
                        $fondo = "#f7f7f7";
                    } else {
                        $fondo = "#FFF";
                    }

                    $detalle_cuenta .= '
					<tr bgcolor=' . $fondo . '>
						<td>' . $descripcion_concepto . '</td>
					<td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
					</tr>';
                    $contador++;
                    $ref_pago = $row_valor["Codigo"];
                    $valor_pagar += $row_valor["Debito"];
                }
                $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
            }

            //Consulto si tiene saldo en Cartera
            $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
            $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-30";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $valor_total += $row_cartera["Total"];
                $detalle_cuenta .= '
				<tr bgcolor=' . $fondo . '>
					<td>Saldos anteriores</td>
				<td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
				</tr>';
            }

            $detalle_cuenta .= '
			<tr bgcolor= #c47e7e >
				<td>Valor Total: </td>
			<td>$' . number_format($valor_total, 0, '', '.') . '</td>
			</tr>';

            $response = array();
            $cuerpo_factura = '<!doctype html>
								<html>
								<head>
								<meta charset="UTF-8">
								<title>Detalle ESTADO CUENTA</title>
								</head>
								<body>
									<table align="left" width="100%">
										<tr>
											<td>
											<img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
											</td>
										</tr>
										<tr>
											<td>
											<table align="center">
												<tr bgcolor="#24508e">
													<td style="color:#FFF">Detalle</td>
													<td style="color:#FFF">Valor</td>
												</tr>
												' . $detalle_cuenta . '
											</table>
											</td>
										</tr>
									</table>
								</body>
							</html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
            $factura["BotonPago"] = "S";
            $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

            $factura["Referencia"] = $r["Referencia"];
            $factura["Observacion"] = $r["Observacion"];
            $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
            $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
            $factura["Almacen"] = "";
            $factura["TipoPago"] = "WebView";
            $factura["ObligatorioCodigoPago"] = "N";
            $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

            $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

            array_push($response, $factura);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        }

        public function set_codigo_pago($IDClub, $IDSocio, $IDFactura, $Codigo)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDFactura)) {

                $sql_pqr = $dbo->query("Update SocioFactura Set CodigoPago = '" . $Codigo . "', FechaCodigoPago = NOW() Where IDFacturaSocio = '" . $IDFactura . "'");

                //SIMUtil::noticar_pago($IDFactura);

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "122. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_detalle_factura_uraki($IDClub, $IDFactura, $NumeroFactura)
    {
            $dbo = &SIMDB::get();
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $response = array();
            $cuerpo_factura = '<!doctype html>
								<html>
								<head>
								<meta charset="UTF-8">
								<title>Detalle Extracto</title>

								</head>
								<body>
									<table align="center">
										<tr>
											<td>
											<img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="150">
											</td>
										</tr>
										<tr>
											<td valign="middle">
											<iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
											</a><br><br>
											</td>
										</tr>
									</table>

								</body>
							</html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
            $factura["BotonPago"] = "N";
            $factura["WebViewInterno"] = "S";
            $extracto["Action"] = "";

            array_push($response, $factura);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        }

        public function get_publicidad($IDClub, $IDModulo, $IDCategoria, $TipoApp)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $datos_publicidad["Publicidad"] = $dbo->getFields("Club", "Publicidad", "IDClub = '" . $IDClub . "'");
            $datos_publicidad["PublicidadTiempo"] = $dbo->getFields("Club", "TiempoPublicidad", "IDClub = '" . $IDClub . "'");

            $datos_publicidad["TipoHeaderApp"] = $dbo->getFields("Club", "TipoHeaderApp", "IDClub = '" . $IDClub . "'");
            $datos_publicidad["TiempoPublicidadHeader"] = $dbo->getFields("Club", "TiempoPublicidad", "IDClub = '" . $IDClub . "'");

            if ($datos_publicidad["Publicidad"] == "S"):
                $array_publicidad = self::get_banner_publicidad($IDClub, "", "", $TipoApp);
                $datos_publicidad["Publicidades"] = $array_publicidad["response"];
            else:
                $datos_publicidad["Publicidad"] = "N";
            endif;

            if ($datos_publicidad["TipoHeaderApp"] == "Publicidad" || $datos_publicidad["TipoHeaderApp"] == "PublicidadFoto"):
                $array_publicidad_header = self::get_banner_publicidad_header($IDClub, "", "", $TipoApp);
                $datos_publicidad["PublicidadesHeader"] = $array_publicidad_header["response"];
            else:
                $datos_publicidad["Publicidad"] = "N";
            endif;

            array_push($response, $datos_publicidad);
            $respuesta["message"] = "1 Encontrados";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
            return $respuesta;
        }

        public function get_banner_publicidad($IDClub, $IDModulo, $IDCategoria, $TipoApp)
    {

            $dbo = &SIMDB::get();

            // Modulo Especifico
            if (!empty($IDModulo)):
                $array_condiciones[] = " IDModulo  = '" . $id_seccion . "'";
            endif;

            // Categoria Especifica
            if (!empty($IDCategoria)):
                $array_condiciones[] = " IDCategoria  = '" . $IDCategoria . "'";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_publicidad = " and " . $condiciones;
            endif;

            if ($TipoApp == "Socio") {
                $condiciones_publicidad .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
            } else {
                $condiciones_publicidad .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
            }

            $response = array();
            $sql = "SELECT * FROM Publicidad WHERE Publicar = 'S' and Footer = 'S'  and (FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' " . $condiciones_publicidad . " ORDER BY Orden";
            $qry = $dbo->query($sql);

            $sql_dia_anglo = "Select * From DiaAnglo Where Fecha = CURDATE() and IDClub = '" . $IDClub . "' Limit 1";
            $result_dia_anglo = $dbo->query($sql_dia_anglo);

            if ($dbo->rows($qry) > 0 || $dbo->rows($result_dia_anglo) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $publicidad["IDPublicidad"] = $r["IDPublicidad"];
                    $publicidad["Nombre"] = $r["Nombre"];
                    //$publicidad["Descripcion"] = $r["Descripcion"];
                    //$publicidad["FechaInicio"] = $r["FechaInicio"];
                    //$publicidad["FechaFin"] = $r["FechaFin"];
                    //$publicidad["Orden"] = $r["Orden"];

                    //$publicidad["Accion"] = $r["AccionClick"];

                    $response_accion = array();
                    $publicidad["Url"] = $r["Url"];
                    $publicidad["VentanaExterna"] = $r["VentanaExterna"];
                    /*
                    switch($r["AccionClick"]){
                    case "Url":
                    $datos_accion["Url"] = $r["Url"];
                    array_push($response_accion, $datos_accion);
                    break;
                    case "WebView":
                    $datos_accion["WebView"] = $r["Cuerpo"];
                    array_push($response_accion, $datos_accion);
                    break;
                    case "SinAccion":
                    break;
                    }
                    $publicidad["DataAccion"] = $response_accion;
                     */

                    if (!empty($r["Foto1"])):
                        $foto = PUBLICIDAD_ROOT . $r["Foto1"];
                    else:
                        $foto = "";
                    endif;
                    $publicidad["Foto1"] = $foto;

                    //Consulto Modulos
                    $response_modulo = array();
                    $sql_modulo = "SELECT * FROM PublicidadModulo Where IDPublicidad = '" . $r["IDPublicidad"] . "' and Activo = 'S'";
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                            if (!empty($r_modulo["Titulo"])) {
                                $modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
                            } else {
                                $modulo["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                            }

                            $modulo["Icono"] = $foto;
                            array_push($response_modulo, $modulo);

                        } //ednw while
                    }
                    $publicidad["Modulos"] = $response_modulo;

                    array_push($response, $publicidad);

                } //ednw hile

                //Especial para Anglo publicar el banner del dia
                if ($IDClub == 8):
                    $sql_dia_anglo = "Select * From DiaAnglo Where Fecha = CURDATE() and IDClub = '" . $IDClub . "' Limit 1";
                    $result_dia_anglo = $dbo->query($sql_dia_anglo);
                    while ($r_dia_anglo = $dbo->fetchArray($result_dia_anglo)):
                        switch ($r_dia_anglo["Dia"]):
                    case "1":
                        $IDBanner = 74;
                        break;
                    case "2":
                        $IDBanner = 75;
                        break;
                    case "3":
                        $IDBanner = 76;
                        break;
                    case "4":
                        $IDBanner = 77;
                        break;
                    case "5":
                        $IDBanner = 78;
                        break;
                    case "6":
                        $IDBanner = 79;
                        break;
                        endswitch;
                        $sql = "SELECT * FROM Publicidad WHERE IDPublicidad = '" . $IDBanner . "'";
                        $qry = $dbo->query($sql);
                        if ($dbo->rows($qry) > 0) {
                                $message = $dbo->rows($qry) . " Encontrados";
                                while ($r = $dbo->fetchArray($qry)) {

                                    $publicidad["IDPublicidad"] = $r["IDPublicidad"];
                                    $publicidad["Nombre"] = $r["Nombre"];
                                    $response_accion = array();
                                    $publicidad["Url"] = $r["Url"];
                                    if (!empty($r["Foto1"])):
                                        $foto = PUBLICIDAD_ROOT . $r["Foto1"];
                                    else:
                                        $foto = "";
                                    endif;
                                    $publicidad["Foto1"] = $foto;
                                    //Consulto Modulos
                                    $response_modulo = array();
                                    $sql_modulo = "SELECT * FROM PublicidadModulo Where IDPublicidad = '" . $r["IDPublicidad"] . "' and Activo = 'S'";
                                    $qry_modulo = $dbo->query($sql_modulo);
                                    if ($dbo->rows($qry_modulo) > 0) {
                                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                            if (!empty($r_modulo["Titulo"])) {
                                                $modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
                                            } else {
                                                $modulo["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                                            }

                                            $modulo["Icono"] = $foto;
                                            array_push($response_modulo, $modulo);

                                        } //ednw while
                                    }
                                    $publicidad["Modulos"] = $response_modulo;
                                    array_push($response, $publicidad);
                                }
                        }

                    endwhile;
                endif;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se ha encontrado publicidad";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_banner_publicidad_header($IDClub, $IDModulo, $IDCategoria, $TipoApp)
    {

            $dbo = &SIMDB::get();

            // Modulo Especifico
            if (!empty($IDModulo)):
                $array_condiciones[] = " IDModulo  = '" . $id_seccion . "'";
            endif;

            // Categoria Especifica
            if (!empty($IDCategoria)):
                $array_condiciones[] = " IDCategoria  = '" . $IDCategoria . "'";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_publicidad = " and " . $condiciones;
            endif;

            if ($TipoApp == "Socio") {
                $condiciones_publicidad .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
            } else {
                $condiciones_publicidad .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
            }

            $response = array();
            $sql = "SELECT * FROM Publicidad WHERE Publicar = 'S' and Header = 'S'  and ( FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' " . $condiciones_publicidad . " ORDER BY Orden";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $publicidad["IDPublicidad"] = $r["IDPublicidad"];
                    $publicidad["Nombre"] = $r["Nombre"];
                    $response_accion = array();
                    $publicidad["Url"] = $r["Url"];
                    $publicidad["VentanaExterna"] = $r["VentanaExterna"];
                    if (!empty($r["Foto1"])):
                        $foto = PUBLICIDAD_ROOT . $r["Foto1"];
                    else:
                        $foto = "";
                    endif;
                    $publicidad["Foto1"] = $foto;
                    array_push($response, $publicidad);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se ha encontrado publicidad";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function solicitar_cerrar_sesion($IDClub)
    {
            $dbo = &SIMDB::get();
            $nowserver = date("Y-m-d H:i:s");
            $respuesta["message"] = "Debe cerrar sesion e ingresar nuevamente";
            $respuesta["success"] = true;
            $respuesta["response"] = "Debe cerrar sesion e ingresar nuevamente";
            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
            exit;
        }

        public function get_lista_clubes($IDClub)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT DC.*, LC.Nombre, P.Nombre as NombrePais FROM DetalleClubCanje DC,ListaClubes LC, Pais P  WHERE DC.IDListaClubes=LC.IDListaClubes and LC.IDPais=P.IDPais and DC.IDClub = '" . $IDClub . "' Order By NombrePais, LC.Nombre ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $lista_club["IDListaClubes"] = $r["IDListaClubes"];
                    $id_pais = utf8_encode($dbo->getFields("ListaClubes", "IDPais", "IDListaClubes = '" . $r["IDListaClubes"] . "'"));
                    if ($IDClub != 48) //para comercio pereira no quieren que aparezca el pais
                {
                        $pais = utf8_encode($dbo->getFields("Pais", "Nombre", "IDPais = '" . $id_pais . "'")) . " - ";
                    } else {
                        $pais = "";
                    }

                    $lista_club["Nombre"] = $pais . utf8_encode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $r["IDListaClubes"] . "'"));
                    $lista_club["DiaMinimo"] = utf8_encode($dbo->getFields("ClubCanje", "DiaMinimo", "IDClubCanje = '" . $r["IDClubCanje"] . "'"));
                    $lista_club["DiaMaximo"] = utf8_encode($dbo->getFields("ClubCanje", "DiaMaximo", "IDClubCanje = '" . $r["IDClubCanje"] . "'"));
                    $SecuenciaDia = (int) $dbo->getFields("ClubCanje", "SecuenciaDia", "IDClubCanje = '" . $r["IDClubCanje"] . "'");
                    if ($SecuenciaDia == 0) {
                        $SecuenciaDia = 1;
                    }

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

        public function get_solicitud_canje($IDClub, $IDSocio, $IDCanjeSolicitud)
    {
            $dbo = &SIMDB::get();

            $response = array();

            $array_id_consulta[] = $IDSocio;

            if (!empty($IDCanjeSolicitud)) {
                $condicion = " and IDCanjeSolicitud = '" . $IDCanjeSolicitud . "'";
            }

            $sql = "SELECT * FROM CanjeSolicitud WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' $condicion ORDER BY IDCanjeSolicitud Desc ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($row_solicitud = $dbo->fetchArray($qry)):
                    $solicitud["IDClub"] = $IDClub;
                    $solicitud["IDSocio"] = $IDSocio;
                    $solicitud["IDCanjeSolicitud"] = $row_solicitud["IDCanjeSolicitud"];
                    $solicitud["IDListaClubes"] = $row_solicitud["IDListaClubes"];
                    $solicitud["Nombre"] = utf8_encode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $row_solicitud["IDListaClubes"] . "'"));
                    $solicitud["IDEstadoCanjeSolicitud"] = $row_solicitud["IDEstadoCanjeSolicitud"];
                    $solicitud["Estado"] = utf8_encode($dbo->getFields("EstadoCanjeSolicitud", "Nombre", "IDEstadoCanjeSolicitud = '" . $row_solicitud["IDEstadoCanjeSolicitud"] . "'"));
                    $solicitud["Numero"] = $row_solicitud["Numero"];
                    $solicitud["FechaInicio"] = $row_solicitud["FechaInicio"];
                    $fecha = date('Y-m-j');
                    $cantidad_dias_canje = (int) $row_solicitud["CantidadDias"] - 1;
                    $fechafinal = strtotime('+' . $cantidad_dias_canje . ' day', strtotime($solicitud["FechaInicio"]));
                    $nuevafechafinal = date('Y-m-d', $fechafinal);
                    $solicitud["FechaFin"] = $nuevafechafinal;
                    $solicitud["CantidadDias"] = $row_solicitud["CantidadDias"];

                    //Beneficiarios
                    $response_benef = array();
                    $array_beneficarios = explode("|", $row_solicitud["IDSocioBeneficiario"]);
                    if (count($array_beneficarios) > 0):
                        foreach ($array_beneficarios as $id_socio):
                            if (!empty($id_socio)):
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

        public function get_campos_invitados($IDClub)
    {
            $dbo = &SIMDB::get();
            $response = array();

            //Campos Formulario
            $response_campo_formulario = array();
            $sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
            $qry_campo_form = $dbo->query($sql_campo_form);
            if ($dbo->rows($qry_campo_form) > 0) {
                while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                    $campoformulario["IDCampoFormularioInvitado"] = $r_campo["IDCampoFormularioInvitado"];
                    $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                    $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                    $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                    $campoformulario["Valores"] = $r_campo["Valores"];
                    array_push($response, $campoformulario);
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se han encontrado campos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function get_campos_contratista($IDClub)
    {
            $dbo = &SIMDB::get();
            $response = array();

//Campos Formulario
            $response_campo_formulario = array();
            $sql_campo_form = "SELECT * FROM CampoFormularioContratista WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
            $qry_campo_form = $dbo->query($sql_campo_form);
            if ($dbo->rows($qry_campo_form) > 0) {
                while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                    $campoformulario["IDCampoFormularioContratista"] = $r_campo["IDCampoFormularioContratista"];
                    $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                    $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                    $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                    $campoformulario["Valores"] = $r_campo["Valores"];
                    array_push($response, $campoformulario);
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se han encontrado campos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio = "", $Categoria = "", $Masivo = "", $CodigoCarne, $ClaveApp = "", $IDSocioSistemaExterno = "", $array_socios = "", $PermiteReservar = "")
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($NumeroDocumento) && !empty($Nombre)) {

                $Documento = trim($row["NumeroDocumento"]);
                $Nombre = str_replace("'", "", $Nombre);
                $Apellido = str_replace("'", "", $Apellido);
                //Consulto si el socio ya existe
                if (count($array_socios) > 0 && is_array($array_socios)) {
                    $con_array = "S";
                    if ((int) $array_socios[trim($NumeroDocumento)] > 0) {
                        $row_socio["IDSocio"] = $array_socios[trim($NumeroDocumento)];

                    }
                } else {
                    $con_array = "N";
                    $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                    $result_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($result_socio);
                }

                //Estado Socio
                if ($EstadoSocio == "A"):
                    $estado_socio = 1;
                else:
                    $estado_socio = 2;
                endif;

                if ($IDClub == 70) {
                    $estado_socio = $EstadoSocio;

                    $con_array = "N";
                    $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                    $result_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($result_socio);
                }

                if (empty($UsuarioApp)):
                    $UsuarioApp = $NumeroDocumento;
                endif;

                if (empty($ClaveApp)) {
                    $clave_socio = sha1(trim($NumeroDocumento));
                } else {
                    $clave_socio = $ClaveApp;
                }

                if (empty($PermiteReservar)) {
                    $Permite_Reservar = 'S';
                } else {
                    $Permite_Reservar = $PermiteReservar;
                }

                //$id_perentesco=$dbo->getFields( "Parentesco" , "IDParentesco" , "Nombre = '" . $Parentesco . "'" );

                if (!empty($Categoria)) {
                    $id_categoria = $dbo->getFields("Categoria", "IDCategoria", "IDSistemaExterno = '" . $Categoria . "' and IDClub = '" . $IDClub . "'");
                    if (!empty($id_categoria)) {
                        $id_categoria = $Categoria;
                    }
                }

                if ($IDClub == 135) {
                    $id_categoria = $Categoria;
                }

                if ($IDClub == 130) {
                    $CambiarClave = 'N';
                } else {
                    $CambiarClave = 'S';
                }

                if ((int) $row_socio["IDSocio"] <= 0):
                    //Crear Socio
                    $inserta_socio = "INSERT INTO Socio (IDClub, IDSocioSistemaExterno, IdentificadorExterno, IDEstadoSocio, Accion, AccionPadre, IDParentesco, Parentesco, TipoSocio, IDCategoria, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Telefono, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar, CambioClave,FotoActualizadaSocio,Predio,CodigoCarne)
											  Values ('" . $IDClub . "','" . $IDSocioSistemaExterno . "','" . $IDSocioSistemaExterno . "','" . $estado_socio . "','" . $Accion . "','" . $AccionPadre . "','" . $Parentesco . "','" . $Parentesco . "','" . $TipoSocio . "','" . $id_categoria . "','" . $Genero . "','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $UsuarioApp . "','" . $clave_socio . "','" . $CorreoElectronico . "',
												'" . $Telefono . "','" . $Celular . "','" . $FechaNacimiento . "','Cron',NOW(),'100','100','" . $Permite_Reservar . "','" . $CambiarClave . "','S','" . $Predio . "','" . $CodigoCarne . "')";

                    //echo "<br>".$inserta_socio;
                    $dbo->query($inserta_socio);
                    $id = $dbo->lastID();
                    $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','" . $inserta_socio . "','" . json_encode($parameters) . "')");

                    $parametros_codigo_barras = $NumeroDocumento;
                    //$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$id);
                    //actualizo codigo barras
                    //$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");

                    if ($frm[IDClub] == 34):
                        $parametros_codigo_qr = $frm[NumeroDocumento];
                    else:
                        $parametros_codigo_qr = $frm[NumeroDocumento] . "\r\n";
                    endif;

                    if ($Masivo != "S"):
                        $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                        $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $id . "'");
                    endif;

                else:
                    //Actualiza Socio

                    $actualiza_socio = "Update Socio
												set IDEstadoSocio = '" . $estado_socio . "',
												IDSocioSistemaExterno='" . $IDSocioSistemaExterno . "',
												IdentificadorExterno='" . $IDSocioSistemaExterno . "',
												Accion = '" . $Accion . "',
												AccionPadre='" . $AccionPadre . "',
												Parentesco = '" . $Parentesco . "',
												TipoSocio = '" . $TipoSocio . "',
												Telefono = '" . $Telefono . "',
												IDCategoria= '" . $id_categoria . "',
												Celular = '" . $Celular . "',
												Direccion = '" . $Direccion . "',
												Nombre = '" . trim($Nombre) . "',
												Apellido = '" . trim($Apellido) . "',
												NumeroDocumento = '" . $NumeroDocumento . "',
												CorreoElectronico = '" . $CorreoElectronico . "',
												FechaNacimiento = '" . $FechaNacimiento . "',
												NumeroInvitados= '100',
												NumeroAccesos= '100',
												Email='" . $UsuarioApp . "',
												Predio = '" . $Predio . "',
												CodigoCarne = '" . $CodigoCarne . "',
												UsuarioTrEd = 'Cron',
												FechaTrEd = NOW(),
												PermiteReservar = '" . $Permite_Reservar . "'
												Where IDSocio = '" . $row_socio["IDSocio"] . "'";
                    $dbo->query($actualiza_socio);
                    //echo "SQ:".$actualiza_socio;
                    //exit;
                endif;

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {

                $respuesta["message"] = "11. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_propietario($IDClub, $Nombre, $Apellido, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Portal, $Casa, $Llave, $AccionRegistro)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($NumeroDocumento) && !empty($Nombre)) {

                $Documento = trim($row["NumeroDocumento"]);
                //Consulto si el socio ya existe
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                $result_socio = $dbo->query($sql_socio);
                //Estado Socio
                switch ($row["AccionRegistro"]):
            case "insert":
                $estado_socio = 1;
                break;
            case "delete":
                $estado_socio = 2;
                break;
            case "update":
                $estado_socio = 1;
                break;
            default:
                $estado_socio = 1;
                endswitch;

                $clave_socio = $Llave;
                $Predio = $Portal . " " . $Casa;

                if ($dbo->rows($result_socio) <= 0):
                    //Crear Socio
                    $inserta_socio = "Insert into Socio (IDClub, IDEstadoSocio, Accion, AccionPadre, Parentesco, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar, CambioClave, TipoSocio, Predio)
											  Values ('" . $IDClub . "','" . $estado_socio . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "','','','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "','" . $clave_socio . "','" . $CorreoElectronico . "','" . $Telefono . "','" . $FechaNacimiento . "','Cron',NOW(),'100','100','S','N','Propietario','" . $Predio . "')";

                    $dbo->query($inserta_socio);
                else:
                    //Actualiza Socio
                    $actualiza_socio = "Update Socio
												set IDEstadoSocio = '" . $estado_socio . "',
												NumeroDocumento = '" . $NumeroDocumento . "',
												NumeroDocumento='" . $NumeroDocumento . "',
												TipoSocio = 'Propietario',
												Celular = '" . $Celular . "',
												Nombre = '" . trim($Nombre) . "',
												Apellido = '" . trim($Apellido) . "',
												NumeroDocumento = '" . $NumeroDocumento . "',
												CorreoElectronico = '" . $CorreoElectronico . "',
												FechaNacimiento = '" . $FechaNacimiento . "',
												NumeroInvitados= '100',
												NumeroAccesos= '100',
												Clave = '" . $Llave . "',
												Predio = '" . $Predio . "',
												UsuarioTrEd = 'Cron',
												FechaTrEd = NOW()
												Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($actualiza_socio);
                endif;

                $respuesta["message"] = "registro guardado Llave: " . $Llave;
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                    $respuesta["message"] = "11. Atencion faltan parametros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_crear_cuenta($IDClub, $Accion, $Identificacion, $CorreoElectronico)
    {
            $dbo = &SIMDB::get();

            if (!empty($IDClub) && !empty($Accion) && !empty($Identificacion) && !empty($CorreoElectronico)) {

                //Verifico que el socio no exista
                $id_socio = $dbo->getFields("Socio", "IDSocio", "Accion = '" . $Accion . "' and IDClub = '" . $IDClub . "'");
                if (empty($id_socio)) {
                    //Verifico si la membresia existe
                    $endpoint = ENDPOINT_CONDADO;
                    $wsdlFile = ENDPOINT_CONDADO;
                    //Creación del cliente SOAP
                    $clienteSOAP = new SoapClient($wsdlFile, array(
                        ‘location’ => $endpoint,
                        ‘trace’ => true,
                        ‘exceptions’ => false));
                    //Incluye los parámetros que necesites en tu función
                    $parameters = array(
                        membresia => $Accion,
                    );
                    //Invocamos a una función del cliente, devolverá el resultado en formato array.
                    $membresia_encontrada = 0;
                    $valor = $clienteSOAP->Socio($parameters);

                    if (is_array($valor->SocioResult->Usuario)) {
                        $array_membresia = $valor->SocioResult->Usuario;
                    } elseif (!empty($valor->SocioResult->Usuario)) {
                    $array_membresia[] = $valor->SocioResult->Usuario;
                }

                foreach ($array_membresia as $datos_membresia) {

                    $membresia_encontrada = 1;

                    $Accion = $datos_membresia->Membresia;
                    $AccionPadre = $datos_membresia->Membresia;
                    $Parentesco = $datos_membresia->Aux3;
                    $Genero = "";
                    $Nombre = $datos_membresia->Socio;
                    $Apellido = "";
                    $FechaNacimiento = substr($datos_membresia->FechaNac, 0, 10);
                    $NumeroDocumento = $datos_membresia->CI;
                    $CorreoElectronico = $datos_membresia->email;
                    $Telefono = $datos_membresia->TelDom;
                    $Celular = $datos_membresia->Celular;
                    $Direccion = $datos_membresia->DirDom;
                    $TipoSocio = $datos_membresia->Relacion;
                    if ($datos_membresia->Estatus == "Presente") {
                        $EstadoSocio = "A";
                    } else {
                        $EstadoSocio = "I";
                    }

                    $InvitacionesPermitidasMes = 100;
                    $UsuarioApp = $Accion;
                    $Predio = $datos_membresia->CI;
                    $Categoria = "";

                    $respuesta = self::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria);
                    $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcrearcuenta','" . $NumeroDocumento . "|" . $IDClub . "|" . $Accion . "','" . json_encode($parameters) . "')");
                }
                if ($membresia_encontrada == 1) {
                    $respuesta["message"] = "Registro exitoso, Su usuario es el código de socio o membresía y clave es su numero de identificación";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "La membresia no existe, por favor verifique o comuniquese con el club";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "No es posible crear la cuenta la membresia ya existe, por favor verifique o comuniquese con el club";
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

    public function set_actualiza_datos($IDClub, $IDSocio, $Direccion, $Telefono, $DireccionOficina, $TelefonoOficina, $Celular, $CorreoElectronico)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($CorreoElectronico)) {

            $sql_socio = "UPDATE Socio SET  Direccion='" . $Direccion . "',Telefono='" . $Telefono . "',DireccionOficina='" . $DireccionOficina . "',TelefonoOficina='" . $TelefonoOficina . "',Celular='" . $Celular . "',CorreoElectronico='" . $CorreoElectronico . "' WHERE IDSocio = '" . $IDSocio . "'";
            $dbo->query($sql_socio);
            $sql_socio_inserta = "INSERT INTO SocioActualizacion (IDClub, IDSocio,Direccion,Telefono,DireccionOficina,TelefonoOficina,Celular,CorreoElectronico,FechaTrCr) VALUES ('" . $IDClub . "', '" . $IDSocio . "','" . $Direccion . "','" . $Telefono . "','" . $DireccionOficina . "','" . $TelefonoOficina . "','" . $Celular . "','" . $CorreoElectronico . "',NOW())";
            $dbo->query($sql_socio_inserta);

            $respuesta["message"] = "Datos actualizados correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        } else {
            $respuesta["message"] = "11. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_actualiza_datos_socio($IDClub, $IDSocio, $Campos, $IDUsuario, $TipoApp)
    {
        $dbo = &SIMDB::get();

        if ($TipoApp == "Empleado" && $IDUsuario == "") {
            $IDUsuario = $IDSocio;
            $IDSocio = "";
        }
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

            $select_campos_editar = "SELECT * FROM CampoEditarSocio";
            $r_select_campos_editar = $dbo->query($select_campos_editar);
            while ($row_campo_editar = $dbo->fetchArray($r_select_campos_editar)) {
                $datos_campos_editar[$row_campo_editar["Nombre"]] = $row_campo_editar["IDCampoEditarSocio"];
            }

            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
            $datos_campos = json_decode($Campos, true);
            if (count($datos_campos) > 0):
                foreach ($datos_campos as $detalle_campo):
                    $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                    $valor = $detalle_campo["Valor"];

                    if (!empty($IDSocio)) {
                        $sql_verifica = "SELECT IDSocioCampoEditarSocio FROM SocioCampoEditarSocio WHERE IDSocio = '" . $IDSocio . "' and IDCampoEditarSocio = '" . $IDCampo . "'";
                        $r_verifica = $dbo->query($sql_verifica);
                        if ($dbo->rows($r_verifica) > 0) {
                            $row_verifica = $dbo->fetchArray($r_verifica);
                            $sql_socio_datos = "UPDATE SocioCampoEditarSocio
																					SET  Valor='" . $valor . "',FechaTrEd = NOW()
																					WHERE IDSocioCampoEditarSocio = '" . $row_verifica["IDSocioCampoEditarSocio"] . "'";
                        } else {
                            $sql_socio_datos = "INSERT INTO SocioCampoEditarSocio (IDCampoEditarSocio, IDSocio,Valor,FechaTrCr)
																						VALUES ('" . $IDCampo . "','" . $IDSocio . "','" . $valor . "',NOW())";
                        }

                    } else {
                        $sql_verifica = "SELECT IDUsuarioCampoEditarUsuario FROM UsuarioCampoEditarUsuario WHERE IDUsuario = '" . $IDUsuario . "' and IDCampoEditarUsuario = '" . $IDCampo . "'";
                        $r_verifica = $dbo->query($sql_verifica);
                        if ($dbo->rows($r_verifica) > 0) {
                            $row_verifica = $dbo->fetchArray($r_verifica);
                            $sql_socio_datos = "UPDATE UsuarioCampoEditarUsuario
																					SET  Valor='" . $valor . "',FechaTrEd = NOW()
																					WHERE IDUsuarioCampoEditarUsuario = '" . $row_verifica["IDUsuarioCampoEditarUsuario"] . "'";
                        } else {
                            $sql_socio_datos = "INSERT INTO UsuarioCampoEditarUsuario (IDCampoEditarUsuario, IDUsuario,Valor,FechaTrCr)
																						VALUES ('" . $IDCampo . "','" . $IDUsuario . "','" . $valor . "',NOW())";
                        }

                    }

                    $dbo->query($sql_socio_datos);

                endforeach;

                //Para uruguay envio datos al ws
                if (!empty($IDSocio) && $IDClub == 125):
                    require LIBDIR . "SIMUruguay.inc.php";
                    $resp = SIMUruguay::actualiza_socio_uruguay($IDClub, $IDSocio, $datos_campos);
                    if (!empty($resp)) {
                        $respuesta["message"] = $resp;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                endif;

            else:
                $respuesta["message"] = "Datos vacios por favor verifique";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

            if (count($datos_campos) > 0) {

                $set_update = implode(",", $array_actualiza);
                $set_campos = implode(",", $array_campos);
                $set_datos = implode(",", $array_datos);
                //$sql_socio="UPDATE Socio SET  ".$set_update.", SolicitaEditarPerfil='N' WHERE IDSocio = '".$IDSocio."' Limit 1";
                if ($TipoApp == "Empleado") {
                    $sql_socio = "UPDATE Usuario SET SolicitaEditarPerfil='N' WHERE IDUsuario = '" . $IDUsuario . "' Limit 1";
                } else {
                    $sql_socio = "UPDATE Socio SET SolicitaEditarPerfil='N' WHERE IDSocio = '" . $IDSocio . "' Limit 1";
                }

                $dbo->query($sql_socio);

                $sql_socio_inserta = "INSERT INTO SocioActualizacion (IDClub, IDSocio," . $set_campos . ",FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "',$set_datos,NOW())";
                $dbo->query($sql_socio_inserta);

            }

            $respuesta["message"] = "Datos actualizados correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        } else {
            $respuesta["message"] = "11. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function cambio_clave_condado($IDClub, $IDSocio, $Clave)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($Clave)) {
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            $endpoint = ENDPOINT_CONDADO;
            $wsdlFile = ENDPOINT_CONDADO;

            try {
                $client = new SoapClient($wsdlFile, array('exceptions' => 0));
                $parameters = array(
                    Membresia => $datos_socio["Accion"],
                    Cedula => $datos_socio["NumeroDocumento"],
                    Correo_electronico => $datos_socio["CorreoElectronico"],
                    Clave_desencriptada => $Clave,
                    Clave_encriptada => sha1($Clave),
                );

                $result = $client->ActualizacionPassword_Socio($parameters);

            } catch (SoapFault $fault) {
                $error_ws = 1;
                //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
            }
            $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('cambioclavecondado','" . $result->ActualizacionPassword_SocioResult . "','" . json_encode($parameters) . "')");
        }

        return true;
    }

    public function get_deuda_socio($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio)) {
            $response = array();
            //Verifico que el socio no exista
            $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            if (!empty($accion_socio)) {
                //Verifico si la membresia existe
                $endpoint = ENDPOINT_CONDADO;
                $wsdlFile = ENDPOINT_CONDADO;
                //Creación del cliente SOAP
                $clienteSOAP = new SoapClient($wsdlFile, array(
                    ‘location’ => $endpoint,
                    ‘trace’ => true,
                    ‘exceptions’ => false));
                //Incluye los parámetros que necesites en tu función
                $parameters = array(
                    membresia => $accion_socio,
                );
                //Invocamos a una función del cliente, devolverá el resultado en formato array.
                $membresia_encontrada = 0;
                $valor = $clienteSOAP->DeudaSocio($parameters);

                if (is_array($valor->DeudaSocioResult->Deuda)) {
                    $array_membresia_deuda = $valor->DeudaSocioResult->Deuda;
                } else {
                    $array_membresia_deuda[] = $valor->DeudaSocioResult->Deuda;
                }

                foreach ($array_membresia_deuda as $datos_deuda) {
                    $mostrar = "S";
                    //Verifico que el documento no se encuentre en estado de pago ya que pudo haber sido pago,  pero hasta el siguiente dia se envia al ws
                    $sql_place = "SELECT IDPeticionesPlacetoPay FROM PeticionesPlacetoPay WHERE Documento like '%" . $datos_deuda->DOCUMENTO . "%' and estado_transaccion = 'APPROVED' ";
                    $r_place = $dbo->query($sql_place);
                    if ($dbo->rows($r_place) > 0) {
                        $mostrar = "N";
                    }

                    if ($mostrar == "S") {
                        $encontrados++;
                        $deudasocio["IDClub"] = $IDClub;
                        $deudasocio["IDSocio"] = $IDSocio;
                        $deudasocio["Cedula"] = $datos_deuda->CEDULA;
                        $deudasocio["Contacto"] = $datos_deuda->CONTACTO;
                        $deudasocio["Descripcion"] = $datos_deuda->DESCRIPCION;
                        $deudasocio["Descuento"] = $datos_deuda->DESCUENTO;
                        $deudasocio["Documento"] = $datos_deuda->DOCUMENTO;
                        $deudasocio["FechaVencimiento"] = $datos_deuda->FECHA_VENCIMIENTO;
                        $deudasocio["ICE"] = $datos_deuda->ICE;
                        $deudasocio["Impuestos"] = $datos_deuda->IMPUESTOS;
                        $deudasocio["IVA"] = $datos_deuda->IVA;
                        $deudasocio["NombreSocio"] = $datos_deuda->NOMBRE_SOCIO;
                        $deudasocio["NombreSocioPrincipal"] = $datos_deuda->NOMBRE_SOCIO_PRINCIPAL;
                        $deudasocio["OtrosCargos"] = $datos_deuda->OTROS_CARGOS;
                        $deudasocio["SaldoPorCobrar"] = $datos_deuda->SALDO_POR_COBRAR;
                        $deudasocio["Servicio"] = $datos_deuda->SERVICIO;
                        $deudasocio["Subtotal"] = $datos_deuda->SUBTOTAL;
                        $deudasocio["TipoDocumento"] = $datos_deuda->TIPO_DOCUMENTO;
                        $deudasocio["TotalDocumento"] = $datos_deuda->SALDO_POR_COBRAR;

                        unset($array_detalle_deuda);
                        //Detalle de la deuda
                        if (is_array($datos_deuda->L_Detalle_Deuda)) {
                            $array_detalle_deuda = $datos_deuda->L_Detalle_Deuda;
                        } else {
                            $array_detalle_deuda[] = $datos_deuda->L_Detalle_Deuda;
                        }

                        $response_detalle = array();
                        unset($detalledeuda);
                        foreach ($array_detalle_deuda as $datos_detalle) {
                            $detalledeuda["Ambiente"] = $datos_detalle->Deuda_Detallle->AMBIENTE;
                            $detalledeuda["Cantidad"] = $datos_detalle->Deuda_Detallle->CANTIDAD_FACTURADA;
                            $detalledeuda["Descuento"] = $datos_detalle->Deuda_Detallle->DESCUENTO;
                            $detalledeuda["Documento"] = $datos_detalle->Deuda_Detallle->DOCUMENTO;
                            $detalledeuda["ItemFacturadoCodigo"] = $datos_detalle->Deuda_Detallle->ITEM_FACTURADO_CODIGO;
                            $detalledeuda["ItemFacturadoDescripcion"] = $datos_detalle->Deuda_Detallle->ITEM_FACTURADO_DESCRIPCION;
                            $detalledeuda["PrecioUnitario"] = $datos_detalle->Deuda_Detallle->PRECIO_UNITARIO;
                            $detalledeuda["SubTotal"] = $datos_detalle->Deuda_Detallle->SUB_TOTAL;
                            $detalledeuda["TipoDocumento"] = $datos_detalle->Deuda_Detallle->TIPO_DOCUMENTO;
                            array_push($response_detalle, $detalledeuda);
                        }
                        $deudasocio["DetalleDeuda"] = $response_detalle;
                        array_push($response, $deudasocio);
                    }

                }

                $respuesta["message"] = "Encontrados: " . $encontrados;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            } else {
                $respuesta["message"] = "No se encontro accion";
                $respuesta["success"] = false;
                $respuesta["response"] = $response;
            }
        } else {

            $respuesta["message"] = "Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = $response;

        }

        return $respuesta;

    }

    public function set_deuda_socio($IDClub, $IDSocio, $Documento, $ValorPagar)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Documento) && !empty($IDSocio)) {

            self::get_deuda_socio($IDClub, $IDSocio);
            $array_documentos_pagar = json_decode($Documento, true);
            foreach ($array_documentos_pagar as $datos_doc) {
                $array_seleccion_pago[] = $datos_doc["NumeroDocumento"];
            }

            $deuda_socio = SIMWebServiceApp::get_deuda_socio($IDClub, $IDSocio);
            $contador = 1;
            foreach ($deuda_socio["response"] as $id_deuda => $datos_deuda) {
                $array_pago_asc[$contador] = $datos_deuda["Documento"];
                $contador++;
            }

            //$array_seleccion_pago=explode(",",$documentos_pagar);
            if (count($array_seleccion_pago) > 0) {
                foreach ($array_seleccion_pago as $id_doc => $num_doc) {
                    $clave = array_search($num_doc, $array_pago_asc);
                    if ($clave > 0) {
                        $array_clave_encontradas[] = $clave;
                    }
                }
            }
            //verifico si se selecciono en orden
            $en_orden = "S";
            for ($i = 1; $i <= count($array_seleccion_pago); $i++) {
                if (!in_array($i, $array_clave_encontradas)) {
                    $en_orden = "N";
                }
            }

            if ($en_orden == "N") {
                $respuesta["message"] = "Debe primero pagar los mas antiguos, por favor verifique!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            }

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            if (!empty($id_socio)) {

                //Datos reserva
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                $response_reserva = array();
                $datos_reserva["IDRegistro"] = time();
                //Calculo el valor de la reserva
                $valor_inicial_reserva = (float) $ValorPagar;
                $datos_reserva["ValorReserva"] = $ValorPagar;
                $ValorReserva = $datos_reserva["ValorReserva"];
                $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
                $usuarioId = $datos_club["ApiLogin"]; //c0digo inicio del cliente
                $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                $iva = 0; //impuestos calculados de la transacciÛn
                $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                $moneda = "COP"; //la moneda con la que se realiza la compra
                $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
                $descripcion = "Pago Cartera Condado."; //descripciÛn de la transacciÛn
                $url_respuesta = URLROOT . "respuesta_transaccion_evento.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                $url_confirmacion = URLROOT . "confirmacion_pagos_evento.php";
                $emailSocio = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                    $emailComprador = $emailSocio;
                } else {
                    $emailComprador = "";
                }

                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                $extra1 = $IDSocio;

                $datos_reserva["Action"] = "https://miclubapp.com/placetopaydeuda.php";

                $response_parametros = array();
                $datos_post["llave"] = "moneda";
                $datos_post["valor"] = (string) $moneda;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "ref";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "llave";
                $datos_post["valor"] = $llave_encripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "userid";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "usuarioId";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "accountId";
                $datos_post["valor"] = (string) $datos_club["AccountId"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "descripcion";
                $datos_post["valor"] = $descripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra1";
                $datos_post["valor"] = (string) $extra1;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = $ValorReserva;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "iva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "baseDevolucionIva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "firma";
                $datos_post["valor"] = $firma;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "emailComprador";
                $datos_post["valor"] = $emailComprador;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "prueba";
                $datos_post["valor"] = (string) $datos_club["IsTest"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "Documento";
                $datos_post["valor"] = (string) $Documento;
                array_push($response_parametros, $datos_post);

                $datos_reserva["ParametrosPost"] = $response_parametros;

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_reserva;

            } else {
                $respuesta["message"] = "No identificado, por favor cierre sesion y vuelva a ingresar!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "D1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function get_notificaciones($IDClub, $IDSocio, $TipoApp)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if ($TipoApp == "Empleado") {
            $sql = "SELECT * FROM LogNotificacion WHERE App='Empleado' and IDUsuario = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ORDER BY FechaReserva DESC, IDLogNotificacion Desc Limit 30";
        } else {
            $sql = "SELECT * FROM LogNotificacion WHERE App='Socio' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' and DATE(Fecha) > CURDATE() - INTERVAL 7 DAY ORDER BY IDLogNotificacion Desc Limit 10";
        }

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $notificacion["IDLogNotificacion"] = $r["IDLogNotificacion"];
                $notificacion["Tipo"] = $r["Tipo"];
                $notificacion["Titulo"] = $r["Titulo"];
                $notificacion["Mensaje"] = $r["Mensaje"];
                $notificacion["Modulo"] = $r["Modulo"];
                $notificacion["IDSeccion"] = $r["IDSeccion"];
                $notificacion["Submodulo"] = $r["SubModulo"];
                $notificacion["IDDetalle"] = $r["IDDetalle"];
                if ($r["Leido"] == "") {
                    $Leido = "N";
                } else {
                    $Leido = $r["Leido"];
                }

                $notificacion["Leido"] = $Leido;
                $notificacion["Link"] = $r["Link"];
                array_push($response, $notificacion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registro";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
            } //end else

            return $respuesta;

        } // fin function

        public function set_notificacion_leida($IDClub, $IDSocio, $IDLogNotificacion)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDLogNotificacion)) {

                if (!empty($IDLogNotificacion)):
                    $array_not = explode(",", $IDLogNotificacion);
                    if (count($array_not) > 0):
                        foreach ($array_not as $id_not):
                            //$sql_leida = $dbo->query("Update LogNotificacion Set Leido = 'S' Where IDLogNotificacion = '".$id_not."' and IDSocio = '".$IDSocio."'");
                            $sql_leida = $dbo->query("Update LogNotificacion Set Leido = 'S' Where IDLogNotificacion = '" . $id_not . "'");
                        endforeach;
                    endif;
                endif;

                $respuesta["message"] = "Notificacion marcada como leida con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_solicitud_canje($IDClub, $IDSocio, $IDListaClubes, $FechaInicio, $CantidadDias, $Beneficiarios = "", $ValoresFormulario = "")
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDListaClubes) && !empty($IDSocio) && !empty($FechaInicio) && !empty($CantidadDias)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {
                    //Consulto el siguiente consecutivo
                    $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From CanjeSolicitud Where IDClub = '" . $IDClub . "'";
                    $result_numero = $dbo->query($sql_max_numero);
                    $row_numero = $dbo->fetchArray($result_numero);
                    $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

                    //Beneficiarios
                    $datos_benef = json_decode($Beneficiarios, true);
                    foreach ($datos_benef as $detalle_datos):
                        $array_benef[] = $detalle_datos["IDSocio"];
                    endforeach;

                    if (count($array_benef) > 0):
                        $IDSocioBeneficiario = implode("|", $array_benef) . "|";
                    else:
                        $IDSocioBeneficiario = "";
                    endif;

                    //Para club colombia quedan aprobados al enviarse
                    if ($IDClub == 38 || $IDClub == 8) {
                        $IDEstadoCanje = 8;
                    } else {
                        $IDEstadoCanje = 1;
                    }

                    $sql_solicitud = $dbo->query("Insert Into CanjeSolicitud (IDClub, IDSocio, IDListaClubes,  IDEstadoCanjeSolicitud, Numero, FechaInicio, CantidadDias, IDSocioBeneficiario, UsuarioTrCr, FechaTrCr)
											Values ('" . $IDClub . "','" . $IDSocio . "','" . $IDListaClubes . "','" . $IDEstadoCanje . "','" . $siguiente_consecutivo . "','" . $FechaInicio . "','" . $CantidadDias . "','" . $IDSocioBeneficiario . "','WebService',NOW())");
                    $id_solicitud = $dbo->lastID();

                    //Guardo los datos de los campos
                    $datos_formulario = json_decode($ValoresFormulario, true);
                    if (count($datos_formulario) > 0):
                        foreach ($datos_formulario as $detalle_datos):
                            $IDSocioInvitado = $detalle_datos["IDSocio"];
                            $sql_datos_form = $dbo->query("Insert Into CanjeOtrosDatos (IDCanje, IDCampoFormularioCanje, Valor) Values ('" . $id_solicitud . "','" . $detalle_datos["IDCampoFormularioCanje"] . "','" . $detalle_datos["Valor"] . "')");
                            $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];
                        endforeach;
                    endif;

                    SIMUtil::notificar_solicitud_canje($id_solicitud);

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

                    $respuesta["message"] = "Guardado." . $mensaje;
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

        public function get_categoria_clasificado($id_club)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM SeccionClasificados  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Orden";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    // verifico que la seccion tenga por lo menos una noticia publicada
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDCategoria"] = $r["IDSeccionClasificados"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];

                    if (!empty($r["Foto"])):
                        $foto = CLASIFICADOS_ROOT . $r["Foto"];
                    else:
                        $foto = "";
                    endif;

                    $seccion["Icono"] = $foto;

                    array_push($response, $seccion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_categoria_clasificado2($id_club)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM SeccionClasificados2  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Orden";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    // verifico que la seccion tenga por lo menos una noticia publicada
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDCategoria"] = $r["IDSeccionClasificados"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];

                    if (!empty($r["Foto"])):
                        $foto = CLASIFICADOS_ROOT . $r["Foto"];
                    else:
                        $foto = "";
                    endif;

                    $seccion["Icono"] = $foto;

                    array_push($response, $seccion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_categoria_objetos_perdidos($id_club)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM SeccionObjetosPerdidos  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Orden";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    // verifico que la seccion tenga por lo menos una noticia publicada
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];

                    if (!empty($r["Foto"])):
                        $foto = OBJETOSPERDIDOS_ROOT . $r["Foto"];
                    else:
                        $foto = "";
                    endif;

                    $seccion["Icono"] = $foto;

                    array_push($response, $seccion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_clasificado($id_club, $id_categoria = "", $id_clasificado = "", $tag = "", $IDSocio, $IDEstadoClasificado)
    {

            $dbo = &SIMDB::get();

            //Socio
            // Seccion Especifica
            if (!empty($IDSocio)):
                $array_condiciones[] = " IDSocio  = '" . $IDSocio . "' ";
            endif;

            // Seccion Especifica
            if (!empty($id_categoria)):
                $array_condiciones[] = " IDSeccionClasificados  = '" . $id_categoria . "' and IDEstadoClasificado  = 1 ";
            endif;

            // Seccion Especifica
            if (!empty($id_clasificado)):
                $array_condiciones[] = " IDClasificado  = '" . $id_clasificado . "' and IDEstadoClasificado  = 1 ";
            endif;

            // Tag
            if (!empty($tag)):
                $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoClasificado  = 1";
            endif;

            // Tag
            if (!empty($IDEstadoClasificado)):
                $array_condiciones[] = " IDEstadoClasificado  = '" . $IDEstadoClasificado . "' ";
            else:
                $array_condiciones[] = " IDEstadoClasificado  > 0 ";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Clasificado WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $clasificado["IDClasificado"] = $r["IDClasificado"];
                    $clasificado["IDSocio"] = $r["IDSocio"];
                    $clasificado["IDCategoria"] = $r["IDSeccionClasificados"];
                    $clasificado["IDClub"] = $r["IDClub"];
                    if ($r["IDEstadoClasificado"] == 5) {
                        $clasificado["IDEstadoClasificado"] = 1;
                    } else {
                        $clasificado["IDEstadoClasificado"] = $r["IDEstadoClasificado"];
                    }

                    $clasificado["Nombre"] = $r["Nombre"];
                    $clasificado["Descripcion"] = $r["Descripcion"];
                    $clasificado["Telefono"] = $r["Telefono"];
                    $clasificado["Email"] = $r["Email"];
                    if ($r["Valor"] == 0) {
                        $clasificado["Valor"] = "";
                    } else {
                        $clasificado["Valor"] = $r["Valor"];
                    }

                    $clasificado["FechaInicio"] = $r["FechaInicio"];
                    $clasificado["FechaFin"] = $r["FechaFin"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $clasificado["Fotos"] = $response_fotos;

                    $response_preguntas = array();
                    $sql_preguntas = "Select * From ClasificadoPregunta Where IDClasificado = '" . $r["IDClasificado"] . "' Order by FechaTrCr Desc";
                    $result_preguntas = $dbo->query($sql_preguntas);
                    while ($row_preguntas = $dbo->fetchArray($result_preguntas)):
                        $array_preguntas["IDPregunta"] = $row_preguntas["IDClasificadoPregunta"];
                        $array_preguntas["Pregunta"] = $row_preguntas["Pregunta"];
                        $array_preguntas["FechaPregunta"] = $row_preguntas["FechaPregunta"];
                        $array_preguntas["Respuesta"] = $row_preguntas["Respuesta"];
                        $array_preguntas["FechaRespuesta"] = $row_preguntas["FechaRespuesta"];
                        array_push($response_preguntas, $array_preguntas);
                    endwhile;

                    $clasificado["Preguntas"] = $response_preguntas;

                    array_push($response, $clasificado);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_clasificado2($id_club, $id_categoria = "", $id_clasificado = "", $tag = "", $IDSocio, $IDEstadoClasificado)
    {

            $dbo = &SIMDB::get();

            //Socio
            // Seccion Especifica
            if (!empty($IDSocio)):
                $array_condiciones[] = " IDSocio  = '" . $IDSocio . "' ";
            endif;

            // Seccion Especifica
            if (!empty($id_categoria)):
                $array_condiciones[] = " IDSeccionClasificados  = '" . $id_categoria . "' and IDEstadoClasificado  = 1 ";
            endif;

            // Seccion Especifica
            if (!empty($id_clasificado)):
                $array_condiciones[] = " IDClasificado  = '" . $id_clasificado . "' and IDEstadoClasificado  = 1 ";
            endif;

            // Tag
            if (!empty($tag)):
                $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoClasificado  = 1";
            endif;

            // Tag
            if (!empty($IDEstadoClasificado)):
                $array_condiciones[] = " IDEstadoClasificado  = '" . $IDEstadoClasificado . "' ";
            else:
                $array_condiciones[] = " IDEstadoClasificado  > 0 ";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Clasificado2 WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $clasificado["IDClasificado"] = $r["IDClasificado"];
                    $clasificado["IDSocio"] = $r["IDSocio"];
                    $clasificado["IDCategoria"] = $r["IDSeccionClasificados"];
                    $clasificado["IDClub"] = $r["IDClub"];
                    $clasificado["IDEstadoClasificado"] = $r["IDEstadoClasificado"];
                    $clasificado["Nombre"] = $r["Nombre"];
                    $clasificado["Descripcion"] = $r["Descripcion"];
                    $clasificado["Telefono"] = $r["Telefono"];
                    $clasificado["Email"] = $r["Email"];
                    $clasificado["Valor"] = $r["Valor"];
                    $clasificado["FechaInicio"] = $r["FechaInicio"];
                    $clasificado["FechaFin"] = $r["FechaFin"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $clasificado["Fotos"] = $response_fotos;

                    $response_preguntas = array();
                    $sql_preguntas = "Select * From ClasificadoPregunta2 Where IDClasificado = '" . $r["IDClasificado"] . "' Order by FechaTrCr Desc";
                    $result_preguntas = $dbo->query($sql_preguntas);
                    while ($row_preguntas = $dbo->fetchArray($result_preguntas)):
                        $array_preguntas["IDPregunta"] = $row_preguntas["IDClasificadoPregunta"];
                        $array_preguntas["Pregunta"] = $row_preguntas["Pregunta"];
                        $array_preguntas["FechaPregunta"] = $row_preguntas["FechaPregunta"];
                        $array_preguntas["Respuesta"] = $row_preguntas["Respuesta"];
                        $array_preguntas["FechaRespuesta"] = $row_preguntas["FechaRespuesta"];
                        array_push($response_preguntas, $array_preguntas);
                    endwhile;

                    $clasificado["Preguntas"] = $response_preguntas;

                    array_push($response, $clasificado);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_objetos_perdidos($id_club, $id_categoria = "", $id_objeto_perdido = "", $tag = "", $IDSocio, $IDEstadoObjetosPerdidos)
    {

            $dbo = &SIMDB::get();

            //Socio
            if (!empty($IDSocio)):
                $sql_solicitudes = "SELECT IDObjetoPerdido FROM ObjetoPerdidoSolicitud WHERE IDSocio = '" . $IDSocio . "' ";
                $r_solicitudes = $dbo->query($sql_solicitudes);
                while ($row_solicitudes = $dbo->fetchArray($r_solicitudes)) {
                    $array_id_objeto[] = $row_solicitudes["IDObjetoPerdido"];
                }
                if (count($array_id_objeto)) {
                    $id_objetos = implode(",", $array_id_objeto);
                } else {
                    $id_objetos = 0;
                }
                //$array_condiciones[] = " IDObjetoPerdido  in (".$id_objetos.") ";
            endif;

            // Seccion Especifica
            if (!empty($id_categoria)):
                $array_condiciones[] = " IDSeccionObjetosPerdidos  = '" . $id_categoria . "' and IDEstadoObjetosPerdidos  in (1,2) ";
            endif;

            // Seccion Especifica
            if (!empty($id_objeto_perdido)):
                $array_condiciones[] = " IDObjetoPerdido  = '" . $id_objeto_perdido . "' and IDEstadoObjetosPerdidos  in (1,2) ";
            endif;

            // Tag
            if (!empty($tag)):
                $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoObjetosPerdidos in (1,2)";
            endif;

            // Tag
            if (!empty($IDEstadoObjetosPerdidos)):
                $array_condiciones[] = " IDEstadoObjetosPerdidos  = '" . $IDEstadoObjetosPerdidos . "' ";
            else:
                $array_condiciones[] = " IDEstadoObjetosPerdidos  > 0 ";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM ObjetoPerdido WHERE  IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaTrCr DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $objeto["IDObjetoPerdido"] = $r["IDObjetoPerdido"];
                    $objeto["IDSocio"] = $r["IDSocio"];
                    $objeto["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                    $objeto["IDClub"] = $r["IDClub"];
                    $objeto["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                    $objeto["EstadoObjetosPerdidos"] = $dbo->getFields("EstadoObjetosPerdidos", "Nombre", "IDEstadoObjetosPerdidos = '" . $r["IDEstadoObjetosPerdidos"] . "'");
                    $objeto["Nombre"] = $r["Nombre"];
                    $objeto["FechaInicio"] = $r["FechaInicio"];
                    $objeto["FechaFin"] = $r["FechaFin"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = OBJETOSPERDIDOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $objeto["Fotos"] = $response_fotos;

                    //Socios que han enviado la solicitud de entrega
                    $response_socios = array();
                    unset($array_socio);
                    $sql_solicitud = "SELECT * FROM  ObjetoPerdidoSolicitud WHERE IDObjetoPerdido = '" . $r["IDObjetoPerdido"] . "'";
                    $r_solicitud = $dbo->query($sql_solicitud);
                    while ($row_solicitud = $dbo->fetchArray($r_solicitud)) {
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_solicitud["IDSocio"] . "' ", "array");
                        $array_socio["IDSocio"] = $row_solicitud["IDSocio"];
                        $array_socio["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                        $array_socio["Accion"] = $datos_socio["Accion"];
                        array_push($response_socios, $array_socio);
                    }

                    $objeto["RequeridoPor"] = $response_socios;

                    array_push($response, $objeto);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_mis_solictudes_objetos_perdidos($id_club, $IDSocio)
    {

            $dbo = &SIMDB::get();

            //Socio
            if (!empty($IDSocio)):
                $sql_solicitudes = "SELECT IDObjetoPerdido FROM ObjetoPerdidoSolicitud WHERE IDSocio = '" . $IDSocio . "' ";
                $r_solicitudes = $dbo->query($sql_solicitudes);
                while ($row_solicitudes = $dbo->fetchArray($r_solicitudes)) {
                    $array_id_objeto[] = $row_solicitudes["IDObjetoPerdido"];
                }
                if (count($array_id_objeto)) {
                    $id_objetos = implode(",", $array_id_objeto);
                } else {
                    $id_objetos = 0;
                }
                $array_condiciones[] = " IDObjetoPerdido  in (" . $id_objetos . ") ";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM ObjetoPerdido WHERE IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaTrCr DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $objeto["IDObjetoPerdido"] = $r["IDObjetoPerdido"];
                    $objeto["IDSocio"] = $r["IDSocio"];
                    $objeto["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                    $objeto["IDClub"] = $r["IDClub"];
                    $objeto["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                    $objeto["Nombre"] = $r["Nombre"];
                    $objeto["FechaInicio"] = $r["FechaInicio"];
                    $objeto["FechaFin"] = $r["FechaFin"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = OBJETOSPERDIDOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $objeto["Fotos"] = $response_fotos;

                    array_push($response, $objeto);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_pregunta_clasificado($IDClub, $IDClasificado, $Pregunta, $IDSocio)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($Pregunta)) {

                //verifico que el clasificado exista y pertenezca al club
                $datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");

                if (!empty($datos_clasificado["IDClasificado"])) {

                    $sql_pregunta = $dbo->query("Insert Into ClasificadoPregunta (IDClasificado, IDSocioPregunta, Pregunta, FechaPregunta, Publicar, UsuarioTrCr, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "', '" . $Pregunta . "','" . date("Y-m-d") . "','S','App',NOW())");

                    //Envio push con notificacion de pregunta
                    $Mensaje = "Tienes una pregunta del clasificado : " . $datos_clasificado["Nombre"];
                    SIMUtil::enviar_notificacion_push_general($IDClub, $datos_clasificado["IDSocio"], $Mensaje);

                    $respuesta["message"] = "Pregunta enviada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el clasificado no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_configuracion_registro_contacto($IDClub, $IDSocio, $IDUsuario)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM ConfiguracionRegistroContacto  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["LabelRegistro"] = $r["LabelRegistro"];
                    $configuracion["LabelBuscador"] = $r["LabelBuscador"];

                    //Campos Formulario
                    $response_campo_formulario = array();
                    $sql_campo_form = "SELECT * FROM CampoRegistroContacto WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                    $qry_campo_form = $dbo->query($sql_campo_form);
                    if ($dbo->rows($qry_campo_form) > 0) {
                        while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                            $campoformulario["IDCampoFormulario"] = $r_campo["IDCampoRegistroContacto"];
                            $campoformulario["Tipo"] = $r_campo["Tipo"];
                            $campoformulario["Nombre"] = $r_campo["Nombre"];
                            $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                            $campoformulario["Valores"] = $r_campo["Valores"];
                            array_push($response_campo_formulario, $campoformulario);
                        } //end while
                    }
                    $configuracion["CamposFormulario"] = $response_campo_formulario;

                    //Campos Contacto Externo
                    $response_campo_externo = array();
                    $sql_campo_form = "SELECT * FROM CampoContactoExterno WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                    $qry_campo_form = $dbo->query($sql_campo_form);
                    if ($dbo->rows($qry_campo_form) > 0) {
                        while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                            $campoexterno["IDCampoContacto"] = $r_campo["IDCampoContactoExterno"];
                            $campoexterno["Tipo"] = $r_campo["Tipo"];
                            $campoexterno["Nombre"] = $r_campo["Nombre"];
                            $campoexterno["Obligatorio"] = $r_campo["Obligatorio"];
                            $campoexterno["Valores"] = $r_campo["Valores"];
                            array_push($response_campo_externo, $campoexterno);
                        } //end while
                    }

                    $configuracion["CamposContactoExterno"] = $response_campo_externo;

                    array_push($response, $configuracion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["LabelRegistro"] = "A continuación agregue a las personas con las que ha tenido un contacto (contacto se considera estar con una persona mas de 15 min)";
                $configuracion["LabelBuscador"] = "A continuación agregue a las personas con las que ha tenido un contacto (contacto se considera estar con una persona mas de 15 min)";
                array_push($response, $configuracion);

                $respuesta["message"] = "No hay configuracion en modulo registro contacto";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //end else

            return $respuesta;

        } // fin function
        public function set_registro_contacto($IDClub, $IDSocio, $IDUsuario, $FechaHora, $Lugar, $Latitud, $Longitud, $Contactos, $CamposFormulario = "")
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($FechaHora) && !empty($Lugar) && !empty($Latitud) && !empty($Longitud)) {

                $sql_registro = $dbo->query("INSERT INTO RegistroContacto (IDClub, IDSocio, IDUsuario, Fecha, Lugar, Latitud, Longitud, UsuarioTrCr, FechaTrCr)
																				 VALUES ('" . $IDClub . "','" . $IDSocio . "', '" . $IDUsuario . "','" . $FechaHora . "','" . $Lugar . "','" . $Latitud . "','" . $Longitud . "','App',NOW())");
                $id_registro = $dbo->lastID();

                //Inserto los contactos
                $datos_invitado_turno = json_decode($Contactos, true);
                $total_invitados_turno = count($datos_invitado_turno);

                $contador_invitado_agregado = 1;

                foreach ($datos_invitado_turno as $detalle_datos_turno):
                    $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
                    $NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];

                    // Guardo los invitados socios o externos
                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)):

                        $sql_inserta_invitado_turno = $dbo->query("INSERT INTO RegistroContactoPersona (IDRegistroContacto, IDSocio, IDUsuario, NombreExterno, UsuarioTrCr, FechaTrCr)
																																	  VALUES ('" . $id_registro . "','" . $IDSocioInvitadoTurno . "','" . $IDUsuario . "', '" . $NombreSocioInvitadoTurno . "','App',NOW())");
                        $id_registro_persona = $dbo->lastID();

                        //Inserto los otros datos
                        $datos_dinamico_ext = json_decode($detalle_datos_turno["CamposContactoExterno"], true);
                        $total_dinamico_ext = count($datos_dinamico_ext);
                        $contador_dinamico_ext = 1;
                        $datos_dinamico_ext = $detalle_datos_turno["CamposContactoExterno"];

                        foreach ($datos_dinamico_ext as $detalle_dinamico_ext):
                            $IDCampoFormularioExt = $detalle_dinamico_ext["IDCampoContacto"];
                            $ValorExt = $detalle_dinamico_ext["Valor"];
                            $sql_inserta_otro_ext = $dbo->query("INSERT INTO RegistroContactoExternoOtrosDatos (IDRegistroContacto, IDCampoContactoExterno, IDRegistroContactoPersona, Valor)
																															VALUES ('" . $id_registro . "','" . $IDCampoFormularioExt . "','" . $id_registro_persona . "','" . $ValorExt . "')");
                        endforeach;

                        $array_invitado_agregado[] = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;

                    else:
                        $contador_invitado_agregado = 0;
                    endif;
                    $contador_invitado_agregado++;
                endforeach;

                //Inserto los otros datos
                $datos_dinamico = json_decode($CamposFormulario, true);
                $total_dinamico = count($datos_dinamico);
                $contador_dinamico = 1;
                foreach ($datos_dinamico as $detalle_dinamico):
                    $IDCampoFormulario = $detalle_dinamico["IDCampoFormulario"];
                    $Valor = $detalle_dinamico["Valor"];
                    $sql_inserta_otro = $dbo->query("INSERT INTO RegistroContactoOtrosDatos (IDRegistroContacto, IDCampoRegistroContacto, Valor)
																										VALUES ('" . $id_registro . "','" . $IDCampoFormulario . "','" . $Valor . "')");
                endforeach;

                $respuesta["message"] = "¡Tu reporte ha sido generado exitosamente!";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_mis_registros_contactos($IDClub, $IDSocio, $IDUsuario)
    {

            $dbo = &SIMDB::get();

            //Socio
            if (!empty($IDSocio) || !empty($IDUsuario)) {

                if (!empty($IDSocio)) {
                    $condicion = " IDSocio = '" . $IDSocio . "' ";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                    $info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                }

                if (!empty($IDUsuario)) {
                    $condicion = " IDUsuario = '" . $IDUsuario . "' ";
                    $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
                    $info = $datos_usuario["Nombre"];
                }

                $response = array();
                $sql = "SELECT IDRegistroContacto, FechaTrCr FROM RegistroContacto WHERE  " . $condicion . " GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " Encontrados";
                    while ($r = $dbo->fetchArray($qry)) {
                        $DetalleResp = "";
                        $objeto["IDContacto"] = $r["IDRegistroContacto"];
                        $objeto["Fecha"] = substr($r["FechaTrCr"], 0, 10);
                        $objeto["Hora"] = substr($r["FechaTrCr"], 10);
                        $objeto["TextoContactos"] = $info;
                        $objeto["Texto"] = $info;

                        //Consulta otros datos
                        $sql_detalle = "SELECT CRC.Nombre,CRC.IDCampoRegistroContacto,Valor
														FROM RegistroContactoOtrosDatos RCOD, CampoRegistroContacto CRC
														WHERE RCOD.IDCampoRegistroContacto=CRC.IDCampoRegistroContacto and RCOD.IDRegistroContacto = '" . $r["IDRegistroContacto"] . "' ";
                        $qry_detalle = $dbo->query($sql_detalle);
                        while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                            $DetalleResp .= "<b>" . $r_detalle["Nombre"] . "</b>=" . $r_detalle["Valor"] . "<br>";
                        }

                        //Consulta los contactos
                        $sql_detalle = "SELECT IDSocio,NombreExterno
														FROM RegistroContactoPersona RCP
														WHERE IDRegistroContacto = '" . $r["IDRegistroContacto"] . "' ";
                        $qry_detalle = $dbo->query($sql_detalle);
                        while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                            $DetalleResp .= $r_detalle["NombreExterno"] . "<br>";
                        }

                        $objeto["Descripcion"] = $DetalleResp;
                        array_push($response, $objeto);
                    } //ednw hile
                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } //End if
            else {
                    $respuesta["message"] = "No se encontraron registros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else
            } else {
                $respuesta["message"] = "DR. Faltan Parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;

        } // fin function

        public function get_verifica_accion($IDClub, $IDSocio, $IDUsuario, $IDNotificacion)
    {
            $dbo = &SIMDB::get();
            $FechaHoy = date("Y-m-d");
            //Modulos soportados
            $array_tabla_soportado = array("99" => "Diagnostico", "58" => "Encuesta", "101" => "Encuesta2", "70" => "Votacion", "102" => "Dotacion");
            $array_tabla_resp_soportado = array("99" => "DiagnosticoRespuesta", "58" => "EncuestaRespuesta", "101" => "Encuesta2Respuesta", "70" => "VotacionRespuesta", "102" => "DotacionRespuesta");
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && count($IDNotificacion) > 0) {

                $datos_notif = json_decode($IDNotificacion, true);

                $response_notif = array();
                foreach ($datos_notif as $datos_notif) {
                    $id_notif = $datos_notif["IDNotificacion"];
                    $datos_notif = $dbo->fetchAll("NotificacionLocal", " IDNotificacionLocal = '" . $id_notif . "' and IDClub = '" . $IDClub . "' ", "array");
                    if (!empty($datos_notif["IDModulo"]) && !empty($datos_notif["IDDetalle"])) {
                        $Tabla = $array_tabla_soportado[$datos_notif["IDModulo"]];
                        $IDTabla = "ID" . $Tabla;
                        $TablaResp = $array_tabla_resp_soportado[$datos_notif["IDModulo"]];
                        $IDTablaResp = "ID" . $TablaResp;
                        $parametro_busqueda = $IDTabla . "= '" . $datos_notif["IDDetalle"] . "' and IDClub  = '" . $IDClub . "'";
                        $datos_modulo = $dbo->fetchAll($Tabla, $parametro_busqueda, "array");
                        if ($datos_modulo["UnaporSocio"] == "N") {
                            $condicion = " and FechaTrCr >= '" . $FechaHoy . " 00:00:00'";
                        } else {
                            $condicion = "";
                        }

                        if (!empty($IDUsuario)) {
                            $IDSocio = $IDUsuario;
                        }

                        $sql_responde = "SELECT  " . $IDTablaResp . " FROM " . $TablaResp . " WHERE IDSocio = '" . $IDSocio . "' and  " . $IDTabla . " = '" . $datos_notif["IDDetalle"] . "' " . $condicion . " Limit 1";
                        $r_responde = $dbo->query($sql_responde);
                        if ($dbo->rows($r_responde) > 0) {
                            $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                            $array_modulo_resp["Respondido"] = "S";
                        } else {
                            $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                            $array_modulo_resp["Respondido"] = "N";
                        }
                    } else {
                        $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                        $array_modulo_resp["Respondido"] = "S";
                    }
                    array_push($response_notif, $array_modulo_resp);
                }

                $response["Notificacion"] = $response_notif;

                $respuesta["message"] = "Respuesta";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            } else {
                $respuesta["message"] = "NL. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_verifica_documento($IDClub, $NumeroDocumento)
    {
            $dbo = &SIMDB::get();
            $FechaHoy = date("Y-m-d");
            $IDInvitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'");
            if ((int) $IDInvitado > 0) {
                //Consulto si tuene una invitacion activa
                $IDInvitacion = $dbo->getFields("SocioInvitado", "IDSocioInvitado", "IDClub = '" . $IDClub . "' and FechaIngreso = '" . $FechaHoy . "' and NumeroDocumento = '" . $NumeroDocumento . "'");
                $IDInvitacionEspecial = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDInvitado = '" . $IDInvitado . "' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ");
                $IDInvitacionAutorizacion = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDInvitado = '" . $IDInvitado . "' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ");

                if (!empty($IDInvitacion) || !empty($IDInvitacionEspecial) || !empty($IDInvitacionAutorizacion)) {
                    $respuesta["message"] = "ok";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = "El documento no tiene invitacion para el dia de hoy, recurde diligenciar este formulario el dia de su ingreso";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "El documento no tiene invitacion o autorizacion de ingreso";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_pregunta_clasificado2($IDClub, $IDClasificado, $Pregunta, $IDSocio)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($Pregunta)) {

                //verifico que el clasificado exista y pertenezca al club
                $datos_clasificado = $dbo->fetchAll("Clasificado2", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");

                if (!empty($datos_clasificado["IDClasificado"])) {

                    $sql_pregunta = $dbo->query("Insert Into ClasificadoPregunta2 (IDClasificado, IDSocioPregunta, Pregunta, FechaPregunta, Publicar, UsuarioTrCr, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "', '" . $Pregunta . "','" . date("Y-m-d") . "','S','App',NOW())");

                    //Envio push con notificacion de pregunta
                    $Mensaje = "Tienes una pregunta del clasificado : " . $datos_clasificado["Nombre"];
                    SIMUtil::enviar_notificacion_push_general($IDClub, $id_socio_clasificado, $Mensaje);

                    $respuesta["message"] = "Pregunta enviada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el clasificado no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_pertenencia($IDClub, $IDObjetoPerdido, $IDSocio)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDSocio)) {

                $datos_objeto = $dbo->fetchAll("ObjetoPerdido", " IDObjetoPerdido = '" . $IDObjetoPerdido . "' and IDClub = '" . $IDClub . "' ", "array");

                if (!empty($datos_objeto["IDObjetoPerdido"])) {

                    $sql_solicitud = $dbo->query("INSERT IGNORE INTO ObjetoPerdidoSolicitud (IDObjetoPerdido, IDSocio, UsuarioTrCr, FechaTrCr) Values ('" . $IDObjetoPerdido . "','" . $IDSocio . "','App',NOW())");
                    $sql_estado_solicitud = $dbo->query("UPDATE  ObjetoPerdido SET IDEstadoObjetosPerdidos =  2  WHERE IDObjetoPerdido = '" . $IDObjetoPerdido . "' ");
                    //Envio correo
                    SIMUtil::notificar_solicitud_objeto_perdido($IDObjetoPerdido, $IDSocio);

                    $respuesta["message"] = "Solicitud enviada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el objeto no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_respuesta_clasificado($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDPregunta) && !empty($Respuesta)) {

                //verifico que la pregunta exista y pertenezca al club
                $datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");
                $datos_pregunta = $dbo->fetchAll("ClasificadoPregunta", " IDClasificadoPregunta = '" . $IDPregunta . "'", "array");

                if (!empty($datos_pregunta["IDClasificadoPregunta"])) {

                    $sql_pregunta = $dbo->query("Update ClasificadoPregunta Set Respuesta = '" . $Respuesta . "', FechaRespuesta = '" . date("Y-m-d") . "' Where  IDClasificadoPregunta = '" . $IDPregunta . "'");

                    //Envio push con notificacion de respuesta
                    $Mensaje = "Recibio una respuesta de su pregunta al clasificado : " . $datos_clasificado["Nombre"];
                    SIMUtil::enviar_notificacion_push_general($IDClub, $datos_pregunta["IDSocioPregunta"], $Mensaje);

                    $respuesta["message"] = "Respuesta enviada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion la pregunta no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_respuesta_clasificado2($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDPregunta) && !empty($Respuesta)) {

                //verifico que la pregunta exista y pertenezca al club
                $datos_clasificado = $dbo->fetchAll("Clasificado2", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");
                $datos_pregunta = $dbo->fetchAll("ClasificadoPregunta2", " IDClasificadoPregunta = '" . $IDPregunta . "'", "array");

                if (!empty($datos_pregunta["IDClasificadoPregunta"])) {

                    $sql_pregunta = $dbo->query("Update ClasificadoPregunta2 Set Respuesta = '" . $Respuesta . "', FechaRespuesta = '" . date("Y-m-d") . "' Where  IDClasificadoPregunta = '" . $IDPregunta . "'");

                    //Envio push con notificacion de respuesta
                    $Mensaje = "Recibio una respuesta de su pregunta al clasificado : " . $datos_clasificado["Nombre"];
                    SIMUtil::enviar_notificacion_push_general($IDClub, $datos_pregunta["IDSocioPregunta"], $Mensaje);

                    $respuesta["message"] = "Respuesta enviada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion la pregunta no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_objeto_perdido($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $File = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($FechaInicio)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    if (isset($File)) {

                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], OBJETOSPERDIDOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                            $$campo_foto = $files[0]["innername"];
                        endfor;

                    } //end if

                    $sql_objeto = $dbo->query("INSERT INTO ObjetoPerdido (IDUsuario	, IDSeccionObjetosPerdidos, IDClub, IDEstadoObjetosPerdidos, Nombre, Descripcion, FechaInicio, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr	)
												   Values ('" . $IDUsuario . "','" . $IDCategoria . "','" . $IDClub . "','1','" . $Nombre . "','" . $Descripcion . "','" . $FechaInicio . "'
														,'" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','" . $IDUsuario . "',NOW())");

                    $id_objeto = $dbo->lastID();

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el usuario no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_entrega_objeto_perdido($IDClub, $IDSocio, $IDObjetoPerdido, $TipoReclamante, $NombreParticular, $DocumentoParticular, $IDTipoDocumentoParticular, $Observaciones, $IDUsuario, $File = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDUsuario)) {

                if (isset($File)) {

                    for ($i_foto = 1; $i_foto <= 2; $i_foto++):
                        $campo_foto = "FotoEntrega" . $i_foto;
                        $files = SIMFile::upload($File[$campo_foto], OBJETOSPERDIDOS_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$campo_foto]["name"])):
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $$campo_foto = $files[0]["innername"];
                    endfor;

                } //end if

                $sql_objeto = $dbo->query("UPDATE ObjetoPerdido SET TipoReclamante='" . $TipoReclamante . "', NombreParticular='" . $NombreParticular . "',
																			DocumentoParticular='" . $DocumentoParticular . "', IDTipoDocumento='" . $IDTipoDocumentoParticular . "',
																			Observaciones = '" . $Observaciones . "', IDUsuarioEntrega = '" . $IDUsuario . "', IDEstadoObjetosPerdidos = '2', FechaEntrega =  NOW()  WHERE IDObjetoPerdido = '" . $IDObjetoPerdido . "'");

                $respuesta["message"] = "Guardado con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_edita_clasificado($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($Telefono)) {

                //verifico que el socio exista y pertenezca al club
                $id_clasificado = $dbo->getFields("Clasificado", "IDClasificado", "IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_clasificado)) {

                    //actualizao la fotos en blanco para que queden solo las enviadas
                    $sql_clasificado = "Update Clasificado set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
								   Where IDClasificado = '" . $IDClasificado . "'";
                    $dbo->query($sql_clasificado);

                    if (isset($File)) {
                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            else:
                                if (!empty($files[0]["innername"])):
                                    $actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
                                endif;
                            endif;

                        endfor;

                    } //end if

                    if (!empty($UrlFoto1)) {
                        $actualiza_foto .= " , Foto1 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto1) . "'";
                    }

                    if (!empty($UrlFoto2)) {
                        $actualiza_foto .= " , Foto2 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto2) . "'";
                    }

                    if (!empty($UrlFoto3)) {
                        $actualiza_foto .= " , Foto3 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto3) . "'";
                    }

                    if (!empty($UrlFoto4)) {
                        $actualiza_foto .= " , Foto4 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto4) . "'";
                    }

                    if (!empty($UrlFoto5)) {
                        $actualiza_foto .= " , Foto5 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto5) . "'";
                    }

                    $sql_clasificado = "UPDATE Clasificado
											  set IDSeccionClasificados = '" . $IDCategoria . "', IDEstadoClasificado = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
											  Telefono = '" . $Telefono . "', Email = '" . $Email . "', Valor = '" . $Valor . "', FechaInicio = '" . $FechaInicio . "', FechaFin = '" . $FechaFin . "',
											  UsuarioTrEd = '" . $IDSocio . "', FechaTrEd = NOW()  " . $actualiza_foto . "
											  Where IDClasificado = '" . $IDClasificado . "'";

                    $dbo->query($sql_clasificado);

                    $respuesta["message"] = "Guardado con exito ";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el clasificado no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_edita_clasificado2($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

                //verifico que el socio exista y pertenezca al club
                $id_clasificado = $dbo->getFields("Clasificado2", "IDClasificado", "IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_clasificado)) {

                    //actualizao la fotos en blanco para que queden solo las enviadas
                    $sql_clasificado = "Update Clasificado2 set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
									   Where IDClasificado = '" . $IDClasificado . "'";
                    $dbo->query($sql_clasificado);

                    if (isset($File)) {
                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            else:
                                if (!empty($files[0]["innername"])):
                                    $actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
                                endif;
                            endif;

                        endfor;

                    } //end if

                    if (!empty($UrlFoto1)) {
                        $actualiza_foto .= " , Foto1 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto1) . "'";
                    }

                    if (!empty($UrlFoto2)) {
                        $actualiza_foto .= " , Foto2 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto2) . "'";
                    }

                    if (!empty($UrlFoto3)) {
                        $actualiza_foto .= " , Foto3 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto3) . "'";
                    }

                    if (!empty($UrlFoto4)) {
                        $actualiza_foto .= " , Foto4 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto4) . "'";
                    }

                    if (!empty($UrlFoto5)) {
                        $actualiza_foto .= " , Foto5 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto5) . "'";
                    }

                    $sql_clasificado = "UPDATE Clasificado2
												  set IDSeccionClasificados = '" . $IDCategoria . "', IDEstadoClasificado = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
												  Telefono = '" . $Telefono . "', Email = '" . $Email . "', Valor = '" . $Valor . "', FechaInicio = '" . $FechaInicio . "', FechaFin = '" . $FechaFin . "',
												  UsuarioTrEd = '" . $IDSocio . "', FechaTrEd = NOW()  " . $actualiza_foto . "
												  Where IDClasificado = '" . $IDClasificado . "'";

                    $dbo->query($sql_clasificado);

                    //Inserto el valor de los campos dinamicos
                    $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                    $datos_respuesta = json_decode($Respuestas, true);
                    if (count($datos_respuesta) > 0):
                        //borar los datos anteriores
                        $sql_borra = "DELETE FROM ClasificadoRespuesta Where IDClasificado = '" . $IDClasificado . "'";
                        $dbo->query($sql_borra);
                        foreach ($datos_respuesta as $detalle_respuesta):
                            $sql_datos_form = $dbo->query("INSERT INTO ClasificadoRespuesta (IDClasificado, IDSocio, IDClasificadoCampo, Valor, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "','" . $detalle_respuesta["IDClasificadoCampo"] . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                        endforeach;
                    endif;

                    $respuesta["message"] = "Guardado con exito ";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el clasificado no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "22. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_edita_objeto_perdido($IDClub, $IDObjetoPerdido, $IDEstadoObjetosPerdidos, $IDCategoria, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDUsuario) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

                //verifico que el socio exista y pertenezca al club
                $id_objeto = $dbo->getFields("ObjetoPerdido", "IDObjetoPerdido", "IDObjetoPerdido = '" . $IDObjetoPerdido . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_objeto)) {

                    //actualizao la fotos en blanco para que queden solo las enviadas
                    $sql_objeto = "Update ObjetoPerdido set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
									   Where IDObjetoPerdido = '" . $IDObjetoPerdido . "'";
                    $dbo->query($sql_clasificado);

                    if (isset($File)) {
                        for ($i_foto = 1; $i_foto <= 6; $i_foto++):
                            $campo_foto = "Foto" . $i_foto;
                            $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$campo_foto]["name"])):
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            else:
                                if (!empty($files[0]["innername"])):
                                    $actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
                                endif;
                            endif;

                        endfor;

                    } //end if

                    if (!empty($UrlFoto1)) {
                        $actualiza_foto .= " , Foto1 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto1) . "'";
                    }

                    if (!empty($UrlFoto2)) {
                        $actualiza_foto .= " , Foto2 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto2) . "'";
                    }

                    if (!empty($UrlFoto3)) {
                        $actualiza_foto .= " , Foto3 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto3) . "'";
                    }

                    if (!empty($UrlFoto4)) {
                        $actualiza_foto .= " , Foto4 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto4) . "'";
                    }

                    if (!empty($UrlFoto5)) {
                        $actualiza_foto .= " , Foto5 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto5) . "'";
                    }

                    $sql_objeto = "UPDATE ObjetoPerdido
												  set IDSeccionObjetosPerdidos = '" . $IDCategoria . "', IDEstadoObjetosPerdidos = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
												  FechaInicio = '" . $FechaInicio . "',
												  UsuarioTrEd = '" . $IDUsuario . "', FechaTrEd = NOW()  " . $actualiza_foto . "
												  Where IDObjetoPerdido = '" . $IDObjetoPerdido . "'";

                    $dbo->query($sql_objeto);

                    $respuesta["message"] = "Guardado con exito ";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el objeto no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O2. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_categoria_beneficio($id_club)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM SeccionBeneficio  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    // verifico que la seccion tenga por lo menos una noticia publicada
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDCategoria"] = $r["IDSeccionBeneficio"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];

                    if (!empty($r["Foto"])):
                        $foto = CLASIFICADOS_ROOT . $r["Foto"];
                    else:
                        $foto = "";
                    endif;

                    $seccion["Icono"] = $foto;

                    array_push($response, $seccion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_restaurante_domicilio($id_club, $Version)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM RestauranteDomicilio" . $Version . "  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' ORDER BY Orden";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    // verifico que la seccion tenga por lo menos una noticia publicada
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDRestaurante"] = $r["IDRestauranteDomicilio"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];

                    if (!empty($r["RestauranteFile"])):
                        $foto = IMGEVENTO_ROOT . $r["RestauranteFile"];
                    else:
                        $foto = "";
                    endif;

                    $seccion["Imagen"] = $foto;

                    array_push($response, $seccion);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_beneficio($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio)
    {

            $dbo = &SIMDB::get();

            // Seccion Especifica
            if (!empty($IDCategoria)):
                $array_condiciones[] = " IDSeccionBeneficio  = '" . $IDCategoria . "' ";
            endif;

            // Seccion Especifica
            if (!empty($IDBeneficio)):
                $array_condiciones[] = " IDBeneficio  = '" . $id_clasificado . "' ";
            endif;

            // Tag
            if (!empty($Tag)):
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Beneficio WHERE Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $beneficio["IDBeneficio"] = $r["IDBeneficio"];
                    $beneficio["IDCategoria"] = $r["IDSeccionBeneficio"];
                    $beneficio["IDClub"] = $r["IDClub"];
                    $beneficio["Nombre"] = $r["Nombre"];
                    $beneficio["Introduccion"] = $r["Introduccion"];
                    $beneficio["Descripcion"] = $r["Descripcion"];

                    $cuerpo_beneficio = str_replace("file/clasificados/editor/", CLASIFICADOS_ROOT . 'editor/', $r["DescripcionHtml"]);
                    //Documentos adjuntos
                    if (!empty($r["Adjunto1File"])):
                        $cuerpo_beneficio .= "<br><a href='" . CLASIFICADOS_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                    endif;

                    $beneficio["DescripcionHtml"] = $cuerpo_beneficio;

                    $beneficio["Telefono"] = $r["Telefono"];
                    $beneficio["PaginaWeb"] = $r["PaginaWeb"];
                    $beneficio["Latitud"] = $r["Latitud"];
                    $beneficio["Longitud"] = $r["Longitud"];
                    $beneficio["FechaInicio"] = $r["FechaInicio"];
                    $beneficio["FechaFin"] = $r["FechaFin"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 1; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $beneficio["Fotos"] = $response_fotos;
                    array_push($response, $beneficio);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_seccion_socio_beneficio($id_club, $id_socio = "")
    {
            $dbo = &SIMDB::get();
            $response = array();
            $contador_resultado = 0;

            unset($seccion);
            $nombre_modulo = "";
            //Secciones Galeria
            $sql = "SELECT * FROM SeccionBeneficio WHERE Publicar = 'S' and IDClub = '" . $id_club . "' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $contador_resultado++;
                $seccion["IDClub"] = $r["IDClub"];
                $nombre_modulo = "Beneficio";
                $seccion["IDSeccion"] = $r["IDSeccionBeneficio"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];

                // verifico si es de preferencia del socio
                if (!empty($id_socio)):
                    $sql_preferencia = "Select * From SocioSeccionBeneficio Where IDSocio = '" . $id_socio . "' and IDSeccionBeneficio = '" . $seccion["IDSeccion"] . "'";
                    $result_preferencia = $dbo->query($sql_preferencia);
                    if ($dbo->rows($result_preferencia) > 0):
                        $seccion["PreferenciaSocio"] = "S";
                    else:
                        $seccion["PreferenciaSocio"] = "N";
                    endif;
                else:
                    $seccion["PreferenciaSocio"] = "N";
                endif;

                array_push($response, $seccion);
            } //end while

            $message = $contador_resultado . " Encontrados";
            if ($contador_resultado > 0) {
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_preferencias_beneficio($IDClub, $IDSocio, $SeccionesBeneficio)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {
                    //borro las secciones asociadas al socio
                    $sql_borra_seccion_gal = $dbo->query("Delete From SocioSeccionBeneficio Where IDSocio  = '" . $IDSocio . "'");

                    if (!empty($SeccionesBeneficio)):
                        $array_secciones_benef = explode(",", $SeccionesBeneficio);
                        if (count($array_secciones_benef) > 0):
                            foreach ($array_secciones_benef as $id_seccion):
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionBeneficio", "IDSeccionBeneficio", "IDClub = '" . $IDClub . "' and IDSeccionBeneficio = '" . $id_seccion . "'");
                                if (!empty($id_seccion)):
                                    $sql_seccion_cla = $dbo->query("Insert Into SocioSeccionBeneficio (IDSocio, IDSeccionBeneficio) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Error el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "7. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_lista_espera($IDClub, $IDSocio, $IDServicio, $IDServicioElemento, $IDAuxiliar, $FechaInicio, $FechaInicioFin, $HoraInicio, $HoraFin, $AceptoTerminos, $Celular, $Tipo)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($FechaInicio) && !empty($Tipo)) {

                //verifico que el socio exista y pertenezca al club
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ", "array");

                if (!empty($datos_socio["IDSocio"])) {

                    $sql_lista_espera = $dbo->query("Insert Into ListaEspera (IDClub, IDSocio, IDServicio, IDAuxiliar, IDServicioElemento, FechaInicio, FechaFin, HoraInicio, HoraFin, AceptoTerminos, Celular, Tipo, UsuarioTrCr, FechaTrCr)
											 Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDServicio . "','" . $IDAuxiliar . "','" . $IDServicioElemento . "','" . $FechaInicio . "','" . $FechaInicioFin . "', '" . $HoraInicio . "','" . $HoraFin . "','S','" . $Celular . "','" . $Tipo . "','App',NOW())");

                    $respuesta["message"] = "Guardado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "51. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_tipo_pago_reserva($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva) && !empty($IDTipoPago)) {

                //verifico que la reserva exista y pertenezca al club
                $id_reserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'");

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if (!empty($id_reserva)) {

                    //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                    if (!empty($CodigoPago)):

                        $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                        $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                        $valorCodigo = $dbo->getFields("ClubCodigoPago", "Valor", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");

                        $datoReserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = '" . $IDReserva . "'");
                        if ($IDClub == 28):
                            $valorReserva = SIMUtil::calcular_tarifa($IDClub, $IDSocio, $datoReserva['IDServicio'], $datoReserva['Fecha'], $datoReserva['Hora'], $datoReserva['IDServicioElemento'], $IDReserva, $datoReserva['IDServicioTipoReserva']);
                        endif;

                        if (empty($id_codigo)) {
                            $respuesta["message"] = "Codigo invalido, por favor verifique";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        } elseif ($codigo_disponible != "S") {
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($valorCodigo != $valorReserva && ($IDClub == 8 || $IDClub == 28)) {
                    $respuesta["message"] = "El codigo que intenta redimir esta registrado por otro valor";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {

                    $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($sql_actualiza_codigo);
                }

                endif;

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update ReservaGeneral Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function set_tipo_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("ReservaHotel", "IDReserva", "IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)):

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    if (empty($id_codigo)) {
                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($codigo_disponible != "S") {
                    $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {

                    $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($sql_actualiza_codigo);
                }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update ReservaHotel Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function get_codigo_pago($IDClub, $CodigoPago)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($CodigoPago)) {

            //verifico que el codigo exista y no haya sido utilizado
            $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "' and Disponible = 'S'");

            if (!empty($id_codigo)) {

                $respuesta["message"] = "Codigo correcto";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "El codigo no es valido";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "52. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function get_documento_dinamico($IDClub, $IDSubmodulo, $Version)
    {
        $dbo = &SIMDB::get();

        $response = array();
        //$sql = "SELECT TA.*,CTA.Icono,CTA.NombreTipoArchivo FROM TipoArchivo TA,ClubTipoArchivo CTA  WHERE TA.IDTipoArchivo=CTA.IDTipoArchivo and  CTA.IDClub = '".$IDClub."' and Activo = 'S' ORDER BY Nombre";
        $sql = "SELECT * FROM TipoArchivo" . $Version . " WHERE IDClub = '" . $IDClub . "' and Publicar = 'S'  and DirigidoA <> 'E' ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                $foto = "";
                $foto_icono = $r["Icono"] . "'";

                if (!empty($r["Icono"]) && empty($foto)) {
                    $foto = CLIENTE_ROOT . $r["Icono"];
                }

                $tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];
                $tipo_archivo["Nombre"] = $r["Nombre"];
                $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                if (!empty($nombre_tipoarchivo)):
                    $tipo_archivo["Label"] = $nombre_tipoarchivo;
                else:
                    $tipo_archivo["Label"] = $r["Nombre"];
                endif;
                $tipo_archivo["Tipo"] = $r["Tipo"];
                $tipo_archivo["Icono"] = $foto;
                $tipo_archivo["SoloIcono"] = $r["SoloIcono"];

                //Consulto los archivos que tiene este tipo de archivo
                $response_archivo = array();
                $sql_archivo = "SELECT * FROM Documento" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' ORDER BY Fecha DESC";
                $qry_archivo = $dbo->query($sql_archivo);
                while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                    $documento["IDClub"] = $r_archivo["IDClub"];
                    $documento["IDTipoArchivo"] = $r_archivo["IDTipoArchivo"];
                    $foto_servicio = "";
                    if (!empty($r["Icono"])):
                        $foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                    else:
                        $foto_servicio = "";
                    endif;

                    if (empty($r_archivo["IDServicio"])):
                        $servicio = "";
                        $id_servicio = "";
                    else:
                        $id_servicio = $r_archivo["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                        $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                        if (empty($foto_servicio)):
                            if (!empty($icono_servicio)):
                                $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                            else:
                                $foto_servicio = "";
                            endif;
                        endif;
                    endif;

                    $documento["IDServicio"] = $id_servicio;
                    //$documento["Servicio"] = $servicio;
                    $documento["Servicio"] = $r_archivo["Nombre"];
                    $documento["IconoServicio"] = $foto_servicio;
                    $documento["IDDocumento"] = $r_archivo["IDDocumento"];
                    $documento["Titular"] = $r_archivo["Nombre"];
                    $documento["Subtitular"] = $r_archivo["Subtitular"];
                    $documento["Fecha"] = $r_archivo["Fecha"];
                    $documento["Descripcion"] = $r_archivo["Descripcion"];

                    //ruta temporal =
                    $ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                    $ruta_temporal = DOCUMENTO_ROOT;
                    if (!empty($r_archivo["Archivo1"])):
                        $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                    else:
                        $archivo = "";
                    endif;
                    $documento["Documento"] = $archivo;

                    array_push($response_archivo, $documento);
                }
                //Fin consulto archivos

                $tipo_archivo["Documentos"] = $response_archivo;

                array_push($response, $tipo_archivo);

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

        public function get_documento_dinamico_funcionario($IDClub, $Version)
    {
            $dbo = &SIMDB::get();

            $response = array();
            //$sql = "SELECT TA.*,CTA.Icono,CTA.NombreTipoArchivo FROM TipoArchivo TA,ClubTipoArchivo CTA  WHERE TA.IDTipoArchivo=CTA.IDTipoArchivo and  CTA.IDClub = '".$IDClub."' and Activo = 'S' ORDER BY Nombre";
            $sql = "SELECT * FROM TipoArchivo" . $Version . " WHERE IDClub = '" . $IDClub . "' and Publicar = 'S' and (DirigidoA = 'E' or DirigidoA = 'T') ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $foto = "";
                    $foto_icono = $r["Icono"] . "'";

                    if (!empty($r["Icono"]) && empty($foto)) {
                        $foto = CLIENTE_ROOT . $r["Icono"];
                    }

                    $tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];
                    $tipo_archivo["Nombre"] = $r["Nombre"];
                    $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                    if (!empty($nombre_tipoarchivo)):
                        $tipo_archivo["Label"] = $nombre_tipoarchivo;
                    else:
                        $tipo_archivo["Label"] = $r["Nombre"];
                    endif;
                    $tipo_archivo["Tipo"] = $r["Tipo"];
                    $tipo_archivo["Icono"] = $foto;

                    //Consulto los archivos que tiene este tipo de archivo
                    $response_archivo = array();
                    $sql_archivo = "SELECT * FROM Documento" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' ORDER BY Nombre";
                    $qry_archivo = $dbo->query($sql_archivo);
                    while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                        $documento["IDClub"] = $r_archivo["IDClub"];
                        $documento["IDTipoArchivo"] = $r_archivo["IDTipoArchivo"];
                        $foto_servicio = "";
                        if (!empty($r["Icono"])):
                            $foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                        else:
                            $foto_servicio = "";
                        endif;

                        if (empty($r_archivo["IDServicio"])):
                            $servicio = "";
                            $id_servicio = "";
                        else:
                            $id_servicio = $r_archivo["IDServicio"];
                            $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                            $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                            $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                            if (empty($foto_servicio)):
                                if (!empty($icono_servicio)):
                                    $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                                else:
                                    $foto_servicio = "";
                                endif;
                            endif;
                        endif;

                        $documento["IDServicio"] = $id_servicio;
                        //$documento["Servicio"] = $servicio;
                        $documento["Servicio"] = $r_archivo["Nombre"];
                        $documento["IconoServicio"] = $foto_servicio;
                        $documento["IDDocumento"] = $r_archivo["IDDocumento"];
                        $documento["Titular"] = $r_archivo["Nombre"];
                        $documento["Subtitular"] = $r_archivo["Subtitular"];
                        $documento["Fecha"] = $r_archivo["Fecha"];
                        $documento["Descripcion"] = $r_archivo["Descripcion"];
                        //ruta temporal =
                        $ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                        $ruta_temporal = DOCUMENTO_ROOT;
                        if (!empty($r_archivo["Archivo1"])):
                            $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                        else:
                            $archivo = "";
                        endif;
                        $documento["Documento"] = $archivo;

                        array_push($response_archivo, $documento);
                    }
                    //Fin consulto archivos

                    $tipo_archivo["Documentos"] = $response_archivo;

                    array_push($response, $tipo_archivo);

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

        public function set_formulario_evento($IDClub, $IDEvento, $IDSocio, $IDSocioBeneficiario, $ValoresFormulario = "", $OtrosDatosFormulario = "", $Version = "", $UsuarioCreacion = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDEvento) && !empty($IDSocio)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_socio)) {

                    //verifico que ya no este inscrito en este eventos
                    $id_registro = (int) $dbo->getFields("EventoRegistro" . $Version, "IDEventoRegistro" . $Version, "IDSocio = '" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and IDClub = '" . $IDClub . "' and IDEvento" . $Version . " = '" . $IDEvento . "' ");
                    $permite_repetir = $dbo->getFields("Evento" . $Version, "PermiteRepetir", "IDEvento" . $Version . " = '" . $IDEvento . "' and IDClub = '" . $IDClub . "' ");
                    if ($id_registro > 0 && $permite_repetir == "N") {
                        $respuesta["message"] = "No es posible registrarse en este evento ya que tiene una inscripcion activa.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    $sql_datos_evento = $dbo->query("Insert Into EventoRegistro" . $Version . " (IDEvento" . $Version . ", IDClub, IDSocio, IDSocioBeneficiario, UsuarioTrCr, FechaTrCr) Values ('" . $IDEvento . "','" . $IDClub . "','" . $IDSocio . "', '" . $IDSocioBeneficiario . "','WebService $UsuarioCreacion',NOW())");
                    $id_evento_registro = $dbo->lastID();
                    //Guardo los datos de los campos
                    $ValoresFormulario = trim(preg_replace('/\s+/', ' ', $ValoresFormulario));
                    $datos_formulario = json_decode($ValoresFormulario, true);
                    if (count($datos_formulario) > 0):
                        foreach ($datos_formulario as $detalle_datos):
                            $IDSocioInvitado = $detalle_datos["IDSocio"];
                            $sql_datos_form = $dbo->query("Insert Into EventoRegistroDatos" . $Version . " (IDEventoRegistro" . $Version . ", IDCampoFormularioEvento" . $Version . ", Valor) Values ('" . $id_evento_registro . "','" . $detalle_datos["IDCampoFormularioEvento"] . "','" . $detalle_datos["Valor"] . "')");
                            $campo_form = $dbo->getFields("CampoFormularioEvento" . $Version, "EtiquetaCampo", "IDCampoFormularioEvento" . $Version . " = '" . $detalle_datos["IDCampoFormularioEvento" . $Version] . "'  ");
                            $OtrosDatosFormulario .= "<br>" . $campo_form . " : " . $detalle_datos["Valor"];
                        endforeach;
                    endif;

                    SIMUtil::notificar_nueva_inscripcion_evento($IDEvento, $IDSocio, $OtrosDatosFormulario, $Version);

                    $parametros_codigo_qr = $IDEvento . "|" . $IDSocio;
                    $ruta_qr_evento = SIMUtil::generar_qr_evento($IDSocio, $parametros_codigo_qr);
                    $actualiza_qr = "UPDATE EventoRegistro SET Qr = '" . $ruta_qr_evento . "' WHERE IDEventoRegistro = '" . $id_evento_registro . "'";
                    $dbo->query($actualiza_qr);

                    //Datos reserva
                    $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                    $response_reserva = array();
                    $datos_reserva["IDEventoRegistro"] = (int) $id_evento_registro;
                    //Calculo el valor de la reserva
                    $valor_inicial_reserva = (int) $dbo->getFields("Evento" . $Version, "ValorInscripcion", "IDEvento" . $Version . " = '" . $IDEvento . "'");
                    $datos_reserva["ValorReserva"] = $valor_inicial_reserva;
                    $ValorReserva = $datos_reserva["ValorReserva"];
                    $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
                    $ApiLogin = $datos_club["ApiLogin"]; //Api Login

                    if ($datos_club["MerchantId"] != "placetopay") {
                        $usuarioId = $datos_club["MerchantId"];
                    }
                    //c0digo inicio del cliente
                else {
                        $usuarioId = $datos_club["ApiLogin"];
                    }
                    //c0digo inicio del cliente

                    $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                    $iva = 0; //impuestos calculados de la transacciÛn
                    $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                    $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                    $moneda = "COP"; //la moneda con la que se realiza la compra
                    $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
                    if ($IDClub == 28) {
                        $CampoCategoria = "SELECT IDCampoFormularioEvento FROM CampoFormularioEvento WHERE EtiquetaCampo = 'Categoría que participa el jugador' AND IDEvento = " . $IDEvento;
                        $qryCampo = $dbo->query($CampoCategoria);
                        $dato = $dbo->fetchArray($qryCampo);

                        $valorCampo = "SELECT Valor FROM EventoRegistroDatos WHERE IDCampoFormularioEvento = " . $dato["IDCampoFormularioEvento"] . " AND IDEventoRegistro = " . $id_evento_registro;
                        $qryValor = $dbo->query($valorCampo);
                        $Valor = $dbo->fetchArray($qryValor);

                        $socio = "SELECT Nombre, Apellido FROM Socio WHERE IDSocio = " . $IDSocio;
                        $qry = $dbo->query($socio);
                        $datoS = $dbo->fetchArray($qry);

                        $descripcion = "Pago Evento " . $datoS["Nombre"] . " " . $datoS["Apellido"] . " Categoria: " . $Valor["Valor"];
                    } else {
                        $descripcion = "Pago Evento Mi Club."; //descripciÛn de la transacciÛn
                    }

                    $url_respuesta = URLROOT . "respuesta_transaccion_evento.php?Version=" . $Version; //Esta es la p·gina a la que se direccionar· al final del pago
                    $url_confirmacion = URLROOT . "confirmacion_pagos_evento.php?Version=" . $Version;
                    $emailSocio = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                    if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                        $emailComprador = $emailSocio;
                    } else {
                        $emailComprador = "";
                    }

                    $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                    $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                    $extra1 = $id_evento_registro;

                    $datos_reserva["Action"] = $datos_club["URL_PAYU"];

                    $response_parametros = array();
                    $datos_post["llave"] = "moneda";
                    $datos_post["valor"] = (string) $moneda;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "ref";
                    $datos_post["valor"] = $refVenta;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "llave";
                    $datos_post["valor"] = $llave_encripcion;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "userid";
                    $datos_post["valor"] = $usuarioId;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "usuarioId";
                    $datos_post["valor"] = $usuarioId;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "accountId";
                    $datos_post["valor"] = (string) $datos_club["AccountId"];
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "descripcion";
                    $datos_post["valor"] = $descripcion;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "extra1";
                    $datos_post["valor"] = (string) $extra1;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "extra2";
                    $datos_post["valor"] = $IDClub;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "refVenta";
                    $datos_post["valor"] = $refVenta;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "valor";
                    $datos_post["valor"] = $ValorReserva;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "iva";
                    $datos_post["valor"] = "0";
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "baseDevolucionIva";
                    $datos_post["valor"] = "0";
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "firma";
                    $datos_post["valor"] = $firma;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "emailComprador";
                    $datos_post["valor"] = $emailComprador;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "prueba";
                    $datos_post["valor"] = (string) $datos_club["IsTest"];
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "url_respuesta";
                    $datos_post["valor"] = (string) $url_respuesta;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "url_confirmacion";
                    $datos_post["valor"] = (string) $url_confirmacion;
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "IDSocio";
                    $datos_post["valor"] = $IDSocio;
                    array_push($response_parametros, $datos_post);

                    $datos_reserva["ParametrosPost"] = $response_parametros;

                    //PAGO
                    $datos_post_pago = array();
                    $datos_post_pago["iva"] = 0;
                    $datos_post_pago["purchaseCode"] = $refVenta;
                    $datos_post_pago["totalAmount"] = $ValorReserva * 100;
                    $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                    $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                    //FIN PAGO

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $datos_reserva;

                } else {
                    $respuesta["message"] = "No identificado, por favor cierre sesion y vuelva a ingresar!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_evento_socio($IDClub, $IDSocio, $Limite = 0, $IDEvento = "", $Version = "")
    {
            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDEvento)):
                $condicion_evento = " and IDEvento" . $Version . " = '" . $IDEvento . "'";
            endif;

            $sql = "SELECT *
				   FROM EventoRegistro" . $Version . " ER, Evento" . $Version . " E
				   WHERE ER.IDEvento" . $Version . " = E.IDEvento" . $Version . "
				   and IDSocio = '" . $IDSocio . "'
				   and FechaInicio <= '" . date("Y-m-d") . "'
				   " . $condicion_evento . " ORDER BY ER.IDEvento" . $Version . " Desc  ";
            $qry = $dbo->query($sql);

            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";

                while ($row_reserva = $dbo->fetchArray($qry)):

                    $reserva["IDClub"] = $IDClub;
                    $reserva["IDSocio"] = $IDSocio;
                    $reserva["IDEvento"] = $row_reserva["IDEventoRegistro" . $Version];
                    $reserva["IDEventoRegistro"] = $row_reserva["IDEventoRegistro" . $Version];
                    $reserva["Evento"] = $dbo->getFields("Evento" . $Version, "Titular", "IDEvento" . $Version . " = '" . $row_reserva["IDEvento" . $Version] . "'");

                    if (empty($row_reserva["Qr"])) {
                        $reserva["QR"] = "";
                    } else {
                        $reserva["QR"] = SOCIO_ROOT . "qr/" . $row_reserva["Qr"];
                    }

                    $rutafoto = $dbo->getFields("Evento" . $Version, "EventoFile", "IDEvento" . $Version . " = '" . $row_reserva["IDEvento" . $Version] . "'");

                    if (!empty($rutafoto)):
                        $foto1 = IMGEVENTO_ROOT . $rutafoto;
                    else:
                        $foto1 = "";
                    endif;
                    $reserva["Foto"] = $foto1;

                    $reserva["Fecha"] = $row_reserva["FechaEvento"];
                    $reserva["FechaFinEvento"] = $row_reserva["FechaFinEvento"];

                    $reserva["PagadaOnline"] = $row_reserva["Pagado"];
                    if (!empty($row_reserva["FechaTransaccion"])) {
                        $reserva["FechaTransaccion"] = $row_reserva["FechaTransaccion"];
                    } else {
                        $reserva["FechaTransaccion"] = "No aplica";
                    }

                    $Mensaje_transaccion = "";
                    if (!empty($row_reserva["MensajeTransaccion"])) {
                        $reserva["MensajeTransaccion"] = "Mensaje transaccion: " . $Mensaje_transaccion;
                    }

                    //Datos Reserva
                    $response_datos = array();
                    $sql_datos_reserva = $dbo->query("Select * From EventoRegistroDatos" . $Version . " Where IDEventoRegistro" . $Version . " = '" . $row_reserva["IDEventoRegistro" . $Version] . "'");
                    while ($r_datos_reserva = $dbo->fetchArray($sql_datos_reserva)):
                        $dato_reserva["Campo"] = utf8_encode($dbo->getFields("CampoFormularioEvento" . $Version, "EtiquetaCampo", "IDCampoFormularioEvento" . $Version . " = '" . $r_datos_reserva["IDCampoFormularioEvento" . $Version] . "'"));
                        $dato_reserva["Valor"] = $r_datos_reserva["Valor"];
                        array_push($response_datos, $dato_reserva);
                    endwhile;

                    $reserva["Datos"] = $response_datos;

                    array_push($response, $reserva);

                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                /*
                $reserva["IDClub"] = "";
                $reserva["IDSocio"] = "";
                $reserva["IDEvento"] = "";
                array_push($response, $reserva);

                $respuesta["message"] = "No tienes eventos programados.";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
                 */

                $respuesta["message"] = "No tienes eventos programados.";
                $respuesta["success"] = false;
                $respuesta["response"] = $response;
            } //end else

            return $respuesta;

        }

        public function elimina_evento_socio($IDClub, $IDSocio, $IDEventoRegistro, $Version)
    {
            $dbo = &SIMDB::get();

            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDEventoRegistro)) {

                //verifico que exista
                //$id_evento_socio = $dbo->getFields( "EventoRegistro".$Version , "IDEventoRegistro".$Version , "IDClub = '" . $IDClub . "' and IDEventoRegistro".$Version." = '".$IDEventoRegistro."' and IDSocio = '".$IDSocio."'" );
                $datos_registro = $dbo->fetchAll("EventoRegistro" . $Version, " IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' ", "array");
                $id_evento_socio = $datos_registro["IDEventoRegistro" . $Version];

                if (!empty($id_evento_socio)) {

                    if ($datos_registro["Pagado"] != "S") {
                        //Hacer copia del registrado por temas de confirmacion de pago
                        $sql_bck = "INSERT IGNORE INTO EventoRegistroEliminado" . $Version . " (IDEventoRegistro" . $Version . ", IDEvento" . $Version . ", IDClub, IDSocio, IDSocioBeneficiario, IDTipoPago, Valor, CodigoPago, EstadoTransaccion, FechaTransaccion, CodigoRespuesta, MedioPago, TipoMedioPago, Pagado, PagoPayu, UsuarioTrCr, FechaTrCr,UsuarioTrEd, FechaTrEd)
					SELECT IDEventoRegistro" . $Version . ", IDEvento" . $Version . ", IDClub, IDSocio, IDSocioBeneficiario, IDTipoPago, Valor, CodigoPago, EstadoTransaccion, FechaTransaccion, CodigoRespuesta, MedioPago, TipoMedioPago, Pagado, PagoPayu, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd FROM EventoRegistro" . $Version . "
					WHERE IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDSocio = '" . $IDSocio . "'";
                        $dbo->query($sql_bck);
                        // Borrar evento socio
                        $sql_elimina_evento_socio = $dbo->query("DELETE FROM EventoRegistro" . $Version . " Where IDClub = '" . $IDClub . "' and IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDSocio = '" . $IDSocio . "'");

                        SIMUtil::notificar_elimina_inscripcion_evento($IDEventoRegistro, $IDSocio, $Version);

                        $respuesta["message"] = "eliminado correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = "eliminado";

                    } else {
                        $respuesta["message"] = "No se puede eliminar el registro ya fue pagado";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }

                } else {
                    $respuesta["message"] = "Atencion la reserva del evento no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "51. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_tipo_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $IDTipoPago, $CodigoPago = "", $Version = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDEventoRegistro) && !empty($IDTipoPago)) {

                //verifico que la reserva exista y pertenezca al club
                $id_reserva = $dbo->getFields("EventoRegistro" . $Version, "IDEventoRegistro" . $Version, "IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_reserva)) {

                    //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                    if (!empty($CodigoPago)):

                        $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                        $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                        if (empty($id_codigo)) {
                            $respuesta["message"] = "Codigo invalido, por favor verifique";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        } elseif ($codigo_disponible != "S") {
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_actualiza_codigo);
                    }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update EventoRegistro Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDEventoRegistro = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago = "", $Version = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDDomicilio) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("Domicilio" . $Version, "IDDomicilio", "IDDomicilio = '" . $IDDomicilio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)):

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    if (empty($id_codigo)) {
                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($codigo_disponible != "S") {
                    $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {

                    $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($sql_actualiza_codigo);
                }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update Domicilio" . $Version . " Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDDomicilio = '" . $IDDomicilio . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito!";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function get_codigo_pago_evento($IDClub, $CodigoPago)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($CodigoPago)) {

            //verifico que el codigo exista y no haya sido utilizado
            $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "' and Disponible = 'S'");

            if (!empty($id_codigo)) {

                $respuesta["message"] = "Codigo correcto";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "El codigo no es valido";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "52. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function valida_cierre_sesion($IDSocio)
    {
        $dbo = &SIMDB::get();
        $IDSocioCierre = $dbo->getFields("CierreSesionSocio", "IDSocio", "IDSocio = '" . $IDSocio . "'");
        if ((int) $IDSocioCierre > 0): //El socio necesita cerrar sesion por alguna razon
            $respuesta = 1;
        else:
            $respuesta = 0;
        endif;
        return $respuesta;
    }

    public function set_cerrar_sesion($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDSocio)):
            $sql_actualiza_cierre = "Update Socio Set SolicitarCierreSesion = 'N', FechaCierreSesion = NOW() Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
            $dbo->query($sql_actualiza_cierre);
            $respuesta["message"] = "Sesion cerrada correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        else:
            $respuesta["message"] = "El socio no existe";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

//PQR Funcionarios
    public function get_pqr_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDUsuario;

        if (!empty($IDPqr)) {
            $condicion = " and IDPqr = '" . $IDPqr . "'";
        }

        $sql = "SELECT * FROM PqrFuncionario WHERE IDClub = '" . $IDClub . "' and IDUsuarioCreacion = '" . $IDUsuario . "' $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($row_pqr = $dbo->fetchArray($qry)):
                $pqr["IDClub"] = $IDClub;
                $pqr["IDUsuario"] = $IDUsuario;
                $pqr["IDPqr"] = $row_pqr["IDPqr"];
                $pqr["IDArea"] = $row_pqr["IDArea"];
                $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]);
                $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                $pqr["Fecha"] = $row_pqr["Fecha"];
                $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                //Bitacora Pqr
                $response_bitacora = array();
                $sql_bitacora = $dbo->query("SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $row_pqr["IDPqr"] . "' Order By IDDetallePqr Desc");
                while ($r_bitacora = $dbo->fetchArray($sql_bitacora)):
                    $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                    if ($r_bitacora[IDUsuario] > 0) {
                        $usuario_responde = "CLUB: " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                        $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                        //quito caracteres especiales
                        $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                        //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                        if ($pos !== false) {
                            //$respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                        }

                    } elseif ($r_bitacora[IDUsuarioCreacion] > 0) {
                    $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuarioCreacion] . "'"));
                    $usuario_responde = "Funcionario: " . $nombre_cliente;
                    $respuesta_pqr = utf8_encode($r_bitacora["Respuesta"]);
                }
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);
            endwhile;

            //Agrego el primer comentario como parte del seguimiento
            $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
            $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuarioCreacion . "'"));
            $usuario_responde = "Funcionario: " . $nombre_cliente;
            $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
            $bitacora[UsuarioResponde] = $usuario_responde;
            $bitacora[RespuestaPqr] = $respuesta_pqr;
            $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
            $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
            array_push($response_bitacora, $bitacora);

            $pqr["Seguimiento"] = $response_bitacora;
            array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se han encontraron resultados";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_pqr_socio_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
            $dbo = &SIMDB::get();

            $response = array();

            $array_id_consulta[] = $IDUsuario;

            if (!empty($IDPqr)) {
                $condicion = " and IDPqr = '" . $IDPqr . "'";
            }

            //Selecciona las areas del usuario para saber cuales pqr puede gestionar
            $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $IDUsuario . "'";
            $result_area = $dbo->query($sql_area_usuario);
            while ($row_area = $dbo->fetchArray($result_area)):
                $array_id_area[] = $row_area["IDArea"];
            endwhile;

            if (count($array_id_area) > 0) {
                $id_area = implode(",", $array_id_area);
                $sql = "SELECT * FROM Pqr WHERE IDClub = '" . $IDClub . "' and IDArea in (" . $id_area . ") $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
                $qry = $dbo->query($sql);
                $message = $dbo->rows($qry) . " Encontrados";
                while ($row_pqr = $dbo->fetchArray($qry)):
                    $pqr["IDClub"] = $IDClub;
                    $pqr["IDUsuario"] = $IDUsuario;
                    $pqr["IDPqr"] = $row_pqr["IDPqr"];
                    $pqr["IDSocio"] = $row_pqr["IDSocio"];
                    $IDSocio = $row_pqr["IDSocio"];
                    $pqr["IDArea"] = $row_pqr["IDArea"];
                    $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                    $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                    $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                    $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                    $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]);
                    $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                    $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                    $pqr["Fecha"] = $row_pqr["Fecha"];
                    $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                    //Bitacora Pqr
                    $response_bitacora = array();
                    $sql_bitacora = $dbo->query("SELECT * FROM Detalle_Pqr WHERE IDPQR = '" . $row_pqr["IDPqr"] . "' Order By 	IDDetallePqr Desc");
                    while ($r_bitacora = $dbo->fetchArray($sql_bitacora)):
                        $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                        if ($r_bitacora[IDUsuario] > 0) {
                            $usuario_responde = "CLUB: " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                            $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                            //quito caracteres especiales
                            $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                            //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                            if ($pos !== false) {
                                $respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                            }

                        } elseif ($r_bitacora[IDSocio] > 0) {
                        $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_bitacora[IDSocio] . "'"));
                        $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_bitacora[IDSocio] . "'"));
                        $usuario_responde = "Socio: " . $nombre_cliente . " " . $apellido_cliente;
                        $respuesta_pqr = utf8_encode($r_bitacora["Respuesta"]);
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                    array_push($response_bitacora, $bitacora);
                endwhile;

                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'"));
                $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'"));
                $usuario_responde = "Socio: " . $nombre_cliente . " " . $apellido_cliente;
                $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se han encontraron resultados";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_pqr_asignada_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
            $dbo = &SIMDB::get();

            $response = array();

            $array_id_consulta[] = $IDUsuario;

            if (!empty($IDPqr)) {
                $condicion = " and IDPqr = '" . $IDPqr . "'";
            }

            //Selecciona las areas del usuario para saber cuales pqr puede gestionar de funcionarios
            $sql_area_usuario = "Select * From UsuarioAreaFuncionario Where IDUsuario = '" . $IDUsuario . "'";
            $result_area = $dbo->query($sql_area_usuario);
            while ($row_area = $dbo->fetchArray($result_area)):
                $array_id_area[] = $row_area["IDArea"];
            endwhile;

            if (count($array_id_area) > 0) {
                $id_area = implode(",", $array_id_area);
                $sql = "SELECT * FROM PqrFuncionario WHERE IDClub = '" . $IDClub . "' and IDArea in (" . $id_area . ") $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
                $qry = $dbo->query($sql);
                $message = $dbo->rows($qry) . " Encontrados";
                while ($row_pqr = $dbo->fetchArray($qry)):
                    $pqr["IDClub"] = $IDClub;
                    $pqr["IDUsuario"] = $IDUsuario;
                    $pqr["IDPqr"] = $row_pqr["IDPqr"];
                    $pqr["IDUsuarioCreacion"] = $row_pqr["IDUsuarioCreacion"];
                    $IDUsuario = $row_pqr["IDUsuarioCreacion"];
                    $pqr["IDArea"] = $row_pqr["IDArea"];
                    $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                    $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                    $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                    $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                    $UsuarioCreacion = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row_pqr["IDUsuarioCreacion"] . "'"));
                    $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]) . " creado por: " . $UsuarioCreacion;
                    $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                    $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                    $pqr["Fecha"] = $row_pqr["Fecha"];
                    $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                    //Bitacora Pqr
                    $response_bitacora = array();
                    $sql_bitacora = $dbo->query("SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $row_pqr["IDPqr"] . "' Order By 	IDDetallePqr Desc");
                    while ($r_bitacora = $dbo->fetchArray($sql_bitacora)):
                        $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                        if ($r_bitacora[IDUsuario] > 0) {
                            $usuario_responde = "CLUB: " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                            $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                            //quito caracteres especiales
                            $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                            //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                            if ($pos !== false) {
                                $respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                            }

                        } elseif ($r_bitacora[IDUsuarioCreacion] > 0) {
                        $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuarioCreacion] . "'"));
                        $usuario_responde = "Socio: " . $nombre_cliente . " " . $apellido_cliente;
                        $respuesta_pqr = utf8_decode($r_bitacora["Respuesta"]);
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                    array_push($response_bitacora, $bitacora);
                endwhile;

                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'"));
                $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'"));
                $usuario_responde = "Usuario: " . $nombre_cliente . " " . $apellido_cliente;
                $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se han encontraron resultados";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_tipo_pqr_funcionario($IDClub)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM TipoPqrFuncionario WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $tipo_pqr["IDTipoPqr"] = $r["IDTipoPqr"];
                    $tipo_pqr["Nombre"] = $r["Nombre"];
                    array_push($response, $tipo_pqr);

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

        public function set_pqr_funcionario($IDClub, $IDArea, $IDUsuario, $TipoPqr, $Asunto, $Comentario, $Archivo, $File = "", $IDTipoPqr = "")
    {
            $dbo = &SIMDB::get();

            //Valido el pseo del archivo
            $tamano_archivo = $File["Archivo"]["size"];
            if ($tamano_archivo >= 6000000) {
                $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            if (!empty($IDClub) && !empty($IDArea) && !empty($IDUsuario) && !empty($Comentario)) {

                //verifico que el usuario exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    //UPLOAD de imagenes

                    if (isset($File)) {

                        $files = SIMFile::upload($File["Archivo"], PQR_DIR, "IMAGE");
                        if (empty($files) && !empty($File["Archivo"]["name"])):
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $Archivo = $files[0]["innername"];

                    } //end if

                    //Consulto el siguiente consecutivo del pqr
                    $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From PqrFuncionario Where IDClub = '" . $IDClub . "'";
                    $result_numero = $dbo->query($sql_max_numero);
                    $row_numero = $dbo->fetchArray($result_numero);
                    $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

                    //Valido que el pqr no exista por que en algunos casos quedó repetido
                    $sql_pqr_existe = "Select *
								   From PqrFuncionario
								   Where IDTipoPqr = '" . $IDTipoPqr . "' and IDArea = '" . $IDArea . "' and IDUsuarioCreacion = '" . $IDUsuario . "' and Tipo = '" . $TipoPqr . "' and Asunto = '" . $Asunto . "' and Descripcion='" . $Comentario . "'";
                    $result_pqr_existe = $dbo->query($sql_pqr_existe);
                    if ($dbo->rows($result_pqr_existe) <= 0):
                        $sql_pqr = $dbo->query("Insert Into PqrFuncionario (IDClub, Numero, IDTipoPqr, IDArea, IDUsuarioCreacion, IDPqrEstado, Tipo, Asunto, Descripcion, Archivo1, Fecha,  UsuarioTrCr, FechaTrCr)
												Values ('" . $IDClub . "','" . $siguiente_consecutivo . "','" . $IDTipoPqr . "','" . $IDArea . "','" . $IDUsuario . "', '1','" . $TipoPqr . "','" . $Asunto . "','" . $Comentario . "','" . $Archivo . "',NOW(),'WebService',NOW())");
                        $id_pqr = $dbo->lastID();
                        SIMUtil::noticar_nuevo_pqr_funcionario($id_pqr);
                    endif;

                    $respuesta["message"] = "guardado";
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

        public function set_pqr_respuesta_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    $sql_pqr = $dbo->query("Insert Into Detalle_PqrFuncionario (IDPqr, IDUsuarioCreacion, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                    SIMUtil::noticar_respuesta_pqr_funcionario($IDPqr, $Comentario);

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Error el usuaro no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "12. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_calificacion_pqr_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $Calificacion)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Calificacion)) {

                $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $IDPqr . "' ", "array");

                if ($datos_pqr["Calificacion"] == 0) {

                    $sql_pqr = $dbo->query("Update PqrFuncionario Set Calificacion = '" . $Calificacion . "', ComentarioCalificacion = '" . $Comentario . "', FechaCalificacion = NOW() Where IDPqr = '" . $IDPqr . "'");

                    SIMUtil::noticar_respuesta_pqr_funcionario($IDPqr, $Comentario);

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "Ya se había registrado una calificación";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                }

            } else {
                $respuesta["message"] = "120. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_area_club_funcionario($IDClub)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * From AreaFuncionario WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and MostrarApp <> 'N' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $area["IDClub"] = $r["IDClub"];
                    $area["IDArea"] = $r["IDArea"];
                    $area["Nombre"] = utf8_encode($r["Nombre"]);
                    $area["CorreoResponsable"] = $r["CorreoResponsable"];
                    array_push($response, $area);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_pqr_respuesta_para_socio($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    //Actualizo el estado del pqr
                    if (!empty($IDPqrEstado)) {
                        $sql_estado = $dbo->query("Update Pqr Set IDPqrEstado = '" . $IDPqrEstado . "' Where IDPqr = '" . $IDPqr . "' ");
                    }

                    $sql_pqr = $dbo->query("Insert Into Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                    //Averiguo el nombre del modulo del pqr
                    $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDModulo = '15' and IDClub = '" . $IDClub . "'"));
                    if (empty($nombre_modulo)) {
                        $nombre_modulo = "Pqr";
                    }

                    $frm = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");

                    $Mensaje = "Cordial Saludo, se ha dado respuesta a su " . $nombre_modulo . ", por favor ingrese al app para conocer mas detalles. (" . $frm["Numero"] . ")";

                    SIMUtil::envia_respuesta_cliente($frm, $IDPqr, $Comentario, $IDClub);
                    SIMUtil::enviar_notificacion_push_general($IDClub, $frm["IDSocio"], $Mensaje);

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Error el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "12. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_pqr_respuesta_para_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    //Actualizo el estado del pqr
                    if (!empty($IDPqrEstado)) {
                        $sql_estado = $dbo->query("Update PqrFuncionario Set IDPqrEstado = '" . $IDPqrEstado . "' Where IDPqr = '" . $IDPqr . "' ");
                    }

                    $sql_pqr = $dbo->query("Insert Into Detalle_PqrFuncionario (IDPqr, IDUsuario, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                    //Averiguo el nombre del modulo del pqr
                    $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDModulo = '15' and IDClub = '" . $IDClub . "'"));
                    if (empty($nombre_modulo)) {
                        $nombre_modulo = "Pqr";
                    }

                    $frm = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");

                    $Mensaje = "Cordial saludo, se ha dado respuesta a su " . $nombre_modulo . ", por favor ingrese al app para conocer mas detalles. (" . $frm["Numero"] . ")";

                    SIMUtil::envia_respuesta_funcionario($frm, $IDPqr, $Comentario, $IDClub);
                    SIMUtil::enviar_notificacion_push_general_funcionario($IDClub, $IDUsuario, $Mensaje);

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;

                } else {
                    $respuesta["message"] = "Error el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "12. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_estado_pqr($IDClub)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM PqrEstado WHERE Publicar = 'S' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $estado_pqr["IDPqrEstado"] = $r["IDPqrEstado"];
                    $estado_pqr["Nombre"] = $r["Nombre"];
                    array_push($response, $estado_pqr);

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
        //FIN PQR Funcionarios

        public function get_estado_objetos_perdidos($IDClub)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM EstadoObjetosPerdidos WHERE Publicar = 'S' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $estado_pqr["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                    $estado_pqr["Nombre"] = $r["Nombre"];
                    array_push($response, $estado_pqr);

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

        public function get_submodulo($IDClub, $IDModulo)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "Select * From SubModulo Where IDModulo = '" . $IDModulo . "' and IDClub = '" . $IDClub . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $modulo["IDClub"] = $IDClub;
                    $modulo["IDModulo"] = $IDModulo;
                    $modulo["IDSubModulo"] = $r["IDSubModulo"];
                    $modulo["MostrarMisReservas"] = $r["MostrarMisReservas"];

                    //Eventos
                    $response_eve = array();
                    if (!empty($r["IDSeccionEvento"])):
                        $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '4' Limit 1";
                        $qry_modulo = $dbo->query($sql_modulo);
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo_eve["IDModulo"] = $r_modulo["IDModulo"];
                        if (!empty($r_modulo["Titulo"])) {
                            $modulo_eve["NombreModulo"] = $r_modulo["Titulo"];
                        } else {
                            $modulo_eve["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                        }

                        $modulo_eve["Orden"] = $r_modulo["Orden"];
                        $icono_modulo = $r_modulo["Icono"];
                        if (!empty($r_modulo["Icono"])):
                            $foto = MODULO_ROOT . $r_modulo["Icono"];
                        else:
                            $foto = "";
                        endif;
                        $modulo_eve["Icono"] = $foto;

                        if (($IDClub == "7") && $IDModulo == "50") {
                            $modulo_eve["NombreModulo"] = "Inscripcion Torneos";
                            $modulo_eve["Icono"] = MODULO_ROOT . "icon_rankf.png";
                        }

                        if (($IDClub == "9") && $IDModulo == "50") {
                            $modulo_eve["NombreModulo"] = "Inscripcion Ranking";
                            $modulo_eve["Icono"] = MODULO_ROOT . "icon_rankf.png";
                        }

                        $array_evento = explode("|", $r["IDSeccionEvento"]);
                        $response_id_eve = array();
                        foreach ($array_evento as $id_evento):
                            $array_id_eve["IDSeccionEvento"] = $id_evento;
                            array_push($response_id_eve, $array_id_eve);
                        endforeach;

                        $modulo_eve["SeccionEvento"] = $response_id_eve;
                        array_push($response_eve, $modulo_eve);
                        $modulo["Eventos"] = $response_eve;
                    endif;

                    //Reservas
                    $response_reserva = array();
                    if (!empty($r["IDServicio"])):
                        $array_reserva = explode("|", $r["IDServicio"]);
                        $response_id_reserva = array();
                        $id_servicio_sub = implode(",", $array_reserva);
                        $sql_sub = "SELECT S.IDServicio,SM.IDServicioMaestro FROM Servicio S,ServicioMaestro SM  WHERE S.IDServicioMaestro=SM.IDServicioMaestro and S.IDServicio in (" . $id_servicio_sub . ")";
                        $r_sub = $dbo->query($sql_sub);
                        unset($array_reserva);
                        while ($row_sub = $dbo->fetchArray($r_sub)) {
                            $OrdenServicio = $dbo->getFields("ServicioClub", "Orden", "IDServicioMaestro = '" . $row_sub["IDServicioMaestro"] . "' and IDClub = '" . $IDClub . "'");
                            $array_reserva[$OrdenServicio] = $row_sub["IDServicio"];
                        }

                        ksort($array_reserva);
                        foreach ($array_reserva as $id_servicio):
                            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $id_servicio . "' ", "array");
                            $NombrePersonalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' and IDClub = '" . $IDClub . "'");

                            $array_id_serv["IDServicio"] = $id_servicio;
                            //Si tiene un nombre personalizado se lo pongo de lo contrario le pongo el general
                            if (!empty($NombrePersonalizado)):
                                $array_id_serv["Nombre"] = $NombrePersonalizado;
                            else:
                                $array_id_serv["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                            endif;

                            $foto = "";
                            if (!empty($datos_servicio["Icono"])) {
                                $foto = SERVICIO_ROOT . $datos_servicio["Icono"];
                            } else {
                                $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                                if (!empty($icono_maestro)) {
                                    $foto = SERVICIO_ROOT . $icono_maestro;
                                }
                            }
                            $array_id_serv["Icono"] = $foto;
                            array_push($response_id_reserva, $array_id_serv);
                        endforeach;
                        //Ordeno el array pr el or

                        $modulo["Reservas"] = $response_id_reserva;
                    endif;

                    //Noticias
                    $response_not = array();
                    if (!empty($r["IDSeccionNoticia"])):
                        $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '3' Limit 1";
                        $qry_modulo = $dbo->query($sql_modulo);
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo_not["IDModulo"] = $r_modulo["IDModulo"];
                        if (!empty($r_modulo["Titulo"])) {
                            $modulo_not["NombreModulo"] = $r_modulo["Titulo"];
                        } else {
                            $modulo_not["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                        }

                        $modulo_not["Orden"] = $r_modulo["Orden"];
                        $icono_modulo = $r_modulo["Icono"];
                        if (!empty($r_modulo["Icono"])):
                            $foto = MODULO_ROOT . $r_modulo["Icono"];
                        else:
                            $foto = "";
                        endif;
                        $modulo_not["Icono"] = $foto;

                        if (($IDClub == "44") && ($IDModulo == "88" || $IDModulo == "89")) {
                            $modulo_not["NombreModulo"] = "Horarios";
                        }

                        $array_noticia = explode("|", $r["IDSeccionNoticia"]);
                        $response_id_not = array();
                        foreach ($array_noticia as $id_noticia):
                            $array_id_not["IDSeccionNoticia"] = $id_noticia;
                            array_push($response_id_not, $array_id_not);
                        endforeach;

                        $modulo_not["SeccionNoticia"] = $response_id_not;
                        array_push($response_not, $modulo_not);
                        $modulo["Noticias"] = $response_not;
                    endif;

                    //Galerias
                    $response_gal = array();
                    if (!empty($r["IDSeccionGaleria"])):
                        $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '5' Limit 1";
                        $qry_modulo = $dbo->query($sql_modulo);
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo_gal["IDModulo"] = $r_modulo["IDModulo"];
                        if (!empty($r_modulo["Titulo"])) {
                            $modulo_gal["NombreModulo"] = $r_modulo["Titulo"];
                        } else {
                            $modulo_gal["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                        }

                        $modulo_gal["Orden"] = $r_modulo["Orden"];
                        $icono_modulo = $r_modulo["Icono"];
                        if (!empty($r_modulo["Icono"])):
                            $foto = MODULO_ROOT . $r_modulo["Icono"];
                        else:
                            $foto = "";
                        endif;
                        $modulo_gal["Icono"] = $foto;
                        $array_galeria = explode("|", $r["IDSeccionGaleria"]);
                        $response_id_gal = array();
                        foreach ($array_galeria as $id_galeria):
                            $array_id_gal["IDSeccionGaleria"] = $id_galeria;
                            array_push($response_id_gal, $array_id_gal);
                        endforeach;

                        $modulo_gal["SeccionGaleria"] = $response_id_gal;
                        array_push($response_gal, $modulo_gal);
                        $modulo["Galerias"] = $response_gal;
                    endif;
                    //Archivos
                    $response_arch = array();
                    if (!empty($r["IDTipoArchivo"])):
                        $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '6' Limit 1";
                        $qry_modulo = $dbo->query($sql_modulo);
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo_arch["IDModulo"] = $r_modulo["IDModulo"];
                        if (!empty($r_modulo["Titulo"])) {
                            $modulo_arch["NombreModulo"] = $r_modulo["Titulo"];
                        } else {
                            $modulo_arch["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                        }

                        $modulo_arch["Orden"] = $r_modulo["Orden"];
                        $icono_modulo = $r_modulo["Icono"];
                        if (!empty($r_modulo["Icono"])):
                            $foto = MODULO_ROOT . $r_modulo["Icono"];
                        else:
                            $foto = "";
                        endif;
                        $modulo_arch["Icono"] = $foto;
                        $array_archivo = explode("|", $r["IDTipoArchivo"]);
                        $response_id_arch = array();
                        foreach ($array_archivo as $id_tipoarchivo):
                            $array_id_arch["IDTipoArchivo"] = $id_tipoarchivo;
                            array_push($response_id_arch, $array_id_arch);
                        endforeach;

                        $modulo_arch["TipoArchivo"] = $response_id_arch;
                        array_push($response_arch, $modulo_arch);
                        $modulo["Archivos"] = $response_arch;
                    endif;
                    //Modulos hijos
                    $response_hijo = array();
                    if (!empty($r["IDModuloHijo"])):
                        $array_modhijo = explode("|", $r["IDModuloHijo"]);
                        if (count($array_modhijo) > 0) {
                            foreach ($array_modhijo as $id_modhijo):
                                $array_id_modhijo[] = $id_modhijo;
                            endforeach;
                            $id_mod_hijo = implode(",", $array_id_modhijo);
                            $sql_modulo_hijo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo in ($id_mod_hijo)";
                            $r_modulo_hijo = $dbo->query($sql_modulo_hijo);
                            while ($row_modulo_hijo = $dbo->fetchArray($r_modulo_hijo)) {
                                $datos_hijo["IDClub"] = $IDClub;
                                $datos_hijo["IDModulo"] = $row_modulo_hijo["IDModulo"];
                                if (!empty($row_modulo_hijo["Titulo"])) {
                                    $NombreModulo = $row_modulo_hijo["Titulo"];
                                } elseif (!empty($row_modulo_hijo["TituloLateral"])) {
                                $NombreModulo = $row_modulo_hijo["TituloLateral"];
                            } else {
                                $NombreModulo = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $row_modulo_hijo["IDModulo"] . "'");
                            }

                            $datos_hijo["NombreModulo"] = $NombreModulo;
                            $datos_hijo["Orden"] = $row_modulo_hijo["Orden"];

                            $icono_modulo = $row_modulo_hijo["Icono"];
                            if (!empty($row_modulo_hijo["Icono"])):
                                $foto = MODULO_ROOT . $row_modulo_hijo["Icono"];
                            else:
                                $foto = "";
                            endif;
                            $datos_hijo["Icono"] = $foto;
                            array_push($response_hijo, $datos_hijo);
                        }

                    }

                    $modulo["Modulos"] = $response_hijo;

                endif;

                array_push($response, $modulo);

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
        }

//WEBSERVICE CLUB COLOMBIA
        public function get_datos_usuario_club_colombia($Token, $email = "", $clave = "")
    {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/user_information.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err):
                $resultado_usuario = json_decode($response, true);
                $datos_usuario = json_decode($resultado_usuario["usuario"], true);
                self::actualiza_datos_colombia($datos_usuario, $email, $clave);
                return "ok";
            else:
                return "no";
            endif;
        }

        public function actualiza_datos_colombia($datos_usuario, $email = "", $clave = "")
    {
            $dbo = &SIMDB::get();
            $IDClub = 38;
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocioSistemaExterno = '" . $datos_usuario["id"] . "' and IDClub = '" . $IDClub . "'");
            if (empty($datos_usuario["email"])):
                $cambiar_clave = "S";
            else:
                $cambiar_clave = "N";
            endif;

            if (empty($email)) {
                $email = $datos_usuario["documento"];
            }

            if (empty($clave)) {
                $clave = $datos_usuario["documento"];
            }

            if (empty($id_socio)):
                $parametros_codigo_barras = $datos_usuario["documento"] . ";";
                $CodigoBarras = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno,FotoActualizadaSocio)
														Values ('38','" . $datos_usuario["id"] . "',1,'" . $datos_usuario["accion"] . "','" . $datos_usuario["sexo"] . "','" . $datos_usuario["nombre"] . "','','" . $datos_usuario["fecha_nacimiento"] . "','" . $datos_usuario["documento"] . "',
														'" . $email . "',sha1('" . $clave . "'), '" . $datos_usuario["email"] . "',NOW(),'WebServiceClubColombia','Socio','S','" . $cambiar_clave . "','" . $datos_usuario["telefono_residencia"] . "','" . $datos_usuario["direccion_residencia"] . "','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "','S')";
                $dbo->query($sql_crea_socio);
                //echo "<br>" . $sql_crea_socio;
            else:
                $sql_actualiza_socio = "Update Socio Set
							  						IDSocioSistemaExterno = '" . $datos_usuario["id"] . "',
													IDEstadoSocio = 1,
													Accion = '" . $datos_usuario["accion"] . "',
													Genero = '" . $datos_usuario["sexo"] . "',
													Nombre = '" . $datos_usuario["nombre"] . "',
													Apellido = '',
													FechaNacimiento = '" . $datos_usuario["fecha_nacimiento"] . "',
													NumeroDocumento = '" . $datos_usuario["documento"] . "',
													Email = '" . $email . "',
													Clave = sha1('" . $clave . "'),
													CorreoElectronico = '" . $datos_usuario["email"] . "',
													FechaTrEd = 'NOW()',
													UsuarioTrEd = 'Webservice Colombia',
													TipoSocio = 'Socio',
													PermiteReservar = 'S',
													CambioClave = '" . $cambiar_clave . "',
													Telefono = '" . $datos_usuario["telefono_residencia"] . "',
													NumeroInvitados = '100',
													NumeroAccesos = '100',
													ClaveSistemaExterno = '" . base64_encode($clave) . "'
													Where IDSocio = '" . $id_socio . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_actualiza_socio);
                //echo "<br>" . $sql_actualiza_socio;
            endif;
        }

        public function set_cambio_clave_colombia($Token, $NuevaClave)
    {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/update_password.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&password=" . $NuevaClave,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err):
                //print_r($response);
                return "ok";
            else:
                return "no";
            endif;

        }

        public function set_recordar_clave_colombia($Token, $Email, $NuevaClave)
    {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/reset_password.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&email=" . $Email . "&password=" . $NuevaClave,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err):
                //print_r($response);
                return "ok";
            else:
                return "no";
            endif;

        }

        public function set_email_colombia($Token, $Correo)
    {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/update_email.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&email=" . $Correo,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err):
                //print_r($response);
                return "ok";
            else:
                return "no";
            endif;
        }

        public function get_socios_colombia($Token)
    {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/users_information.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err):
                //print_r($response);
                return $response;
            else:
                return "no";
            endif;
        }

        public function obtener_token_colombia($Usuario, $Clave)
    {
            $post_data = array(
                'grant_type' => 'password',
                'client_id' => 'clubcolo_app',
                'client_secret' => '20ClUbC0l0mB1@18P@5SCl13nT',
                'username' => $Usuario,
                'password' => $Clave,
            );

            $crl = curl_init();
            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_URL, 'https://www.clubcolombia.org/api/login.php');
            curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);

            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $rest = curl_exec($crl);
            if ($rest === false) {
                return curl_error($crl);
            }
            curl_close($crl);
            //print_r($rest);
            $resultado_token = json_decode($rest, true);
            $token = $resultado_token["access_token"];
            return $token;

        }

//FIN WEBSERVICE CLUB COLOMBIA

//WebService Country CLUB
        public function autentica_country_club($usuario, $clave)
    {

            $estado_autentica = 0;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_PORT => "3000",
                CURLOPT_URL => "https://countryclubdebogota.com:3000/sessions/api_create",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\"session\":{\"username\":\"$usuario\",\"password\":\"$clave\"}}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer 4beafec232f5610a2985bcc37f4693f2",
                    "Content-Type: application/json",
                    "Postman-Token: 8b8a043a-bd8d-4f92-ae7c-36740164bf5f",
                    "cache-control: no-cache",
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                //echo "cURL Error #:" . $err;
                $estado_autentica = 0;
            } else {
                $resultado_autentica = json_decode($response, true);
                if (!empty($resultado_autentica["message"])) {
                    $estado_autentica = 0;
                } else {
                    $CorreoElectronico = $resultado_autentica["data"]["attributes"]["email"];
                    $Documento = $resultado_autentica["data"]["attributes"]["document_number"];
                    $IDSocioExterno = $resultado_autentica["data"]["attributes"]["id_socio"];
                    $UsuarioApp = $resultado_autentica["data"]["attributes"]["username"];
                    $Nombre = $resultado_autentica["data"]["attributes"]["name"];
                    $Apellido = $resultado_autentica["data"]["attributes"]["last_name"];
                    $Accion = $Documento;
                    $cambiar_clave = "N";

                    $estado_autentica = self::crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave);

                    $AccionPadre = $resultado_autentica["data"]["partner_data"]["id_derecho"];
                    $Consanguinidad = $resultado_autentica["data"]["partner_data"]["id_derecho"];
                    $Consecutivo = $resultado_autentica["data"]["partner_data"]["consecutivo"];
                    $Digito = $resultado_autentica["data"]["partner_data"]["digito"];
                    $Accion = $resultado_autentica["data"]["partner_data"]["numero_socio"];
                    $IDSocioPadre = $resultado_autentica["data"]["partner_data"]["id_socio_padre"];
                    $IDSocioTitular = $resultado_autentica["data"]["partner_data"]["id_socio_titular"];

                    $GrupoFamiliar = $resultado_autentica["data"]["partner_group"];
                    foreach ($GrupoFamiliar as $datos_grupo) {
                        $CorreoElectronico = "";
                        $Documento = $datos_grupo["numero_socio"];
                        $IDSocioExterno = $datos_grupo["id_socio"];
                        $UsuarioApp = $datos_grupo["numero_socio"];
                        $Consanguinidad = $datos_grupo["consanguinidad"];
                        $AccionPadre = $datos_grupo["id_derecho"];
                        $Accion = $datos_grupo["numero_socio"];
                        $Nombre = $datos_grupo["persona"]["primer_nombre"];
                        $Apellido = $datos_grupo["persona"]["primer_apellido"] . " " . $datos_grupo["data"]["persona"]["segundo_apellido"];

                        $Accion = $datos_grupo["numero_socio"];
                        $usuario = "sinusuario";
                        $clave = "sinclave";
                        $crear_beneficiario = self::crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave, $Consanguinidad, $AccionPadre);
                    }
                }
            }
            return $estado_autentica;
        }
// WebService Country CLUB

        public function crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave, $Consanguinidad, $AccionPadre)
    {
            $dbo = &SIMDB::get();
            if ((int) $IDSocioExterno <= 0) {
                $IDSocioExterno = $resultado_autentica["data"]["id"];
            }

            //Si el Socio existe lo creo de los contrario lo actualizo
            $IDClub = 44;
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocioSistemaExterno = '" . $IDSocioExterno . "' and IDClub = '" . $IDClub . "'");

            if ($Consanguinidad == "TITULAR") {
                $Accion = $AccionPadre;
                $AccionPadre = "";
            }

            if (empty($id_socio)):
                $parametros_codigo_barras = $Documento;
                $CodigoBarras = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                if ($usuario == "sinusuario" && $clave == "sinclave") {
                    $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, AccionPadre, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno)
					Values ('" . $IDClub . "','" . $IDSocioExterno . "',1,'" . $Accion . "','" . $AccionPadre . "','','" . $Nombre . "','" . $Apellido . "','','" . $Documento . "',
					'" . $CorreoElectronico . "',NOW(),'WebServiceCountry','Socio','S','" . $cambiar_clave . "','','','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "')";
                } else {
                    $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, AccionPadre, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno)
					Values ('" . $IDClub . "','" . $IDSocioExterno . "',1,'" . $Accion . "','" . $AccionPadre . "','','" . $Nombre . "','" . $Apellido . "','','" . $Documento . "',
					'" . $usuario . "',sha1('" . $clave . "'), '" . $CorreoElectronico . "',NOW(),'WebServiceCountry','Socio','S','" . $cambiar_clave . "','','','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "')";

                }
                $dbo->query($sql_crea_socio);
                //echo "<br>" . $sql_crea_socio;
                $estado_autentica = 1;
            else:

                //Validacion especial con este usuario que no quiere que aparezca el nombre del esposo sino el de ella
                if ($usuario == "homebrunner@gmail.com") {
                    $Nombre = "Ana Maria";
                    $Apellido = "de Brunner";
                }

                if ($usuario == "sinusuario" && $clave == "sinclave") {
                    $sql_actualiza_socio = "Update Socio Set
											IDSocioSistemaExterno = '" . $IDSocioExterno . "',
											IDEstadoSocio = 1,
											Accion = '" . $Accion . "',
											AccionPadre = '" . $AccionPadre . "',
											Genero = '',
											Nombre = '" . $Nombre . "',
											Apellido = '" . $Apellido . "',
											FechaNacimiento = '',
											NumeroDocumento = '" . $Documento . "',
											CorreoElectronico = '" . $CorreoElectronico . "',
											FechaTrEd = 'NOW()',
											UsuarioTrEd = 'Webservice Country',
											TipoSocio = 'Socio',
											PermiteReservar = 'S',
											CambioClave = '" . $cambiar_clave . "',
											Telefono = '',
											Direccion = '',
											NumeroInvitados = '100',
											NumeroAccesos = '100',
											ClaveSistemaExterno = '" . base64_encode($clave) . "'
											Where IDSocio = '" . $id_socio . "'";
                } else {
                    $sql_actualiza_socio = "Update Socio Set
												IDSocioSistemaExterno = '" . $IDSocioExterno . "',
												IDEstadoSocio = 1,
												Accion = '" . $Accion . "',
												AccionPadre = '" . $AccionPadre . "',
												Genero = '',
												Nombre = '" . $Nombre . "',
												Apellido = '" . $Apellido . "',
												FechaNacimiento = '',
												NumeroDocumento = '" . $Documento . "',
												Email = '" . $usuario . "',
												Clave = sha1('" . $clave . "'),
												CorreoElectronico = '" . $CorreoElectronico . "',
												FechaTrEd = 'NOW()',
												UsuarioTrEd = 'Webservice Country',
												TipoSocio = 'Socio',
												PermiteReservar = 'S',
												CambioClave = '" . $cambiar_clave . "',
												Telefono = '',
												Direccion = '',
												NumeroInvitados = '100',
												NumeroAccesos = '100',
												ClaveSistemaExterno = '" . base64_encode($clave) . "'
												Where IDSocio = '" . $id_socio . "'";

                }
                $dbo->query($sql_actualiza_socio);
                //echo "<br>" . $sql_actualiza_socio;
            endif;
            return $estado_autentica = 1;
        }

        public function get_datos_socio($IDClub, $Identificacion, $Todos)
    {

            $dbo = &SIMDB::get();
            if (!empty($Identificacion)) {
                $foto = "";
                $foto_cod_barras = "";
                $sql_verifica = "SELECT * FROM Socio WHERE NumeroDocumento = '" . $Identificacion . "'  and IDClub = '" . $IDClub . "'";
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {
                    $respuesta["message"] = "El socio no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end if
            else {
                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    if (!empty($datos_socio["Foto"])) {
                        $foto = SOCIO_ROOT . $datos_socio["Foto"];
                    }

                    $tipo_codigo_carne = $dbo->getFields("Club", "TipoCodigoCarne", "IDClub = '" . $IDClub . "'");

                    switch ($tipo_codigo_carne) {
                        case "Barras":
                            if (!empty($datos_socio["CodigoBarras"])) {
                                $foto_cod_barras = SOCIO_ROOT . $datos_socio["CodigoBarras"];
                            }
                            break;
                        case "QR":
                            if (!empty($datos_socio["CodigoQR"])) {
                                $foto_cod_barras = SOCIO_ROOT . "qr/" . $datos_socio["CodigoQR"];
                            }
                            break;
                        default:
                            $foto_cod_barras = "";
                    }

                    $tipo_socio = $datos_socio["TipoSocio"];
                    $response["IDClub"] = $datos_socio["IDClub"];
                    $response["IDSocio"] = $datos_socio["IDSocio"];
                    $response["Foto"] = $foto;
                    $response["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $response["NumeroDerecho"] = $datos_socio["Accion"];
                    $response["CodigoBarras"] = $foto_cod_barras;
                    $response["Dispositivo"] = $datos_socio["Dispositivo"];
                    $response["TipoSocio"] = $tipo_socio;
                    $respuesta["message"] = "ok";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "WS1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        } //end function

        public function get_moduloweb_view($IDClub, $IDSocio, $IDModulo)
    {

            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

                if ($IDModulo == 55):
                    $Mensaje = $datos_club["MensajeWebView1"];
                    $UrlWebView = $datos_club["UrlWebView1"];
                    $ControlNavegacion = "S";
                    $MostrarHeader = "S";
                    $LinkExterno = "N";
                elseif ($IDModulo == 56):
                    $Mensaje = $datos_club["MensajeWebView2"];
                    $UrlWebView = $datos_club["UrlWebView2"];
                    $ControlNavegacion = "S";
                    $MostrarHeader = "S";
                    $LinkExterno = "N";
                elseif ($IDModulo == 95):
                    $Mensaje = $datos_club["MensajeWebView3"];
                    $UrlWebView = $datos_club["UrlWebView3"];
                    $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                    $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                    $MostrarHeader = $datos_modulo["MostrarHeader"];
                    $LinkExterno = $datos_modulo["LinkExterno"];
                elseif ($IDModulo == 96):
                    $Mensaje = $datos_club["MensajeWebView4"];
                    $UrlWebView = $datos_club["UrlWebView4"];
                    $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                    $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                    $MostrarHeader = $datos_modulo["MostrarHeader"];
                    $LinkExterno = $datos_modulo["LinkExterno"];
                elseif ($IDModulo == 111):
                    $Mensaje = $datos_club["MensajeWebView5"];
                    $UrlWebView = $datos_club["UrlWebView5"];
                    $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                    $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                    $MostrarHeader = $datos_modulo["MostrarHeader"];
                    $LinkExterno = $datos_modulo["LinkExterno"];
                else:
                    $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                    $Mensaje = $datos_modulo["MensajeWebView"];
                    $UrlWebView = $datos_modulo["Url"];
                    $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                    $MostrarHeader = $datos_modulo["MostrarHeader"];
                    $LinkExterno = $datos_modulo["LinkExterno"];

                endif;

                $webview["IDClub"] = $IDClub;
                $webview["Encabezado"] = $Mensaje;
                $webview["Url"] = $UrlWebView . "&IDSocio=" . $IDSocio . "&IDClub=" . $IDClub;
                $webview["MostrarControlNavegacion"] = $ControlNavegacion;
                $webview["MostrarHeader"] = $MostrarHeader;
                $webview["LinkExterno"] = $LinkExterno;
                array_push($response, $webview);
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "2. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        } // fin function

        public function set_invitado_grupo_reserva($IDClub, $IDSocio, $IDReserva)
    {
            $dbo = &SIMDB::get();

            if (!empty($IDSocio) && !empty($IDReserva)) {

                //Verifico que todavia queden cupos
                $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
                $IDDisponibilidad = $datos_reserva["IDDisponibilidad"];
                if ((int) $IDDisponibilidad == 0) {
                    $IDDisponibilidad = SIMWebService::obtener_disponibilidad_utilizada($datos_reserva["IDServicio"], $datos_reserva["Fecha"], $datos_reserva["IDServicioElemento"], $datos_reserva["Hora"]);
                }

                $MaximoInvitados = $dbo->getFields("Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $IDDisponibilidad . "'");
                $MaximoReservaSocioServicio = $dbo->getFields("Disponibilidad", "MaximoReservaDia", "IDDisponibilidad = '" . $IDDisponibilidad . "'");

                //validacion especial arrayanes ec solo permite agregarse a un grupo despues de x horas antes de la reserva
                if ($IDClub == 23) {
                    $HoraEmpieza = "09:30:00";
                    $FechaHoraReserva = $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                    //$HoraPermitida = strtotime( '-' . $HorasAntes . ' hour', strtotime( date( $FechaHoraReserva ) ) );
                    $DiaPermitido = strtotime('-2 day', strtotime(date($FechaHoraReserva)));
                    $HoraPermitida = strtotime(date("Y-m-d", $DiaPermitido) . " " . $HoraEmpieza);
                    $HoraActual = strtotime(date("Y-m-d H:i:s"));
                    if ($HoraActual <= $HoraPermitida) {
                        $respuesta["message"] = "Lo sentimos solo puede agregarse a un grupo libre despues de las: " . $HoraEmpieza;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }
                }

                //validacion especial bellavista solo permite agregarse a un grupo despues de x horas antes de la reserva
                $dia_semana = date("w", strtotime($datos_reserva["Fecha"]));
                if (($IDClub == 112 || $IDClub == 8) && (int) $dia_semana == 6) {
                    $HoraEmpieza = "08:00:00";
                    $FechaHoraReserva = $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                    //$HoraPermitida = strtotime( '-' . $HorasAntes . ' hour', strtotime( date( $FechaHoraReserva ) ) );
                    $DiaPermitido = strtotime('-2 day', strtotime(date($FechaHoraReserva)));
                    $HoraPermitida = strtotime(date("Y-m-d", $DiaPermitido) . " " . $HoraEmpieza);
                    $HoraActual = strtotime(date("Y-m-d H:i:s"));
                    if ($HoraActual <= $HoraPermitida) {
                        $respuesta["message"] = "Lo sentimos solo puede agregarse a un grupo libre desde el jueves anterior despues de las: " . $HoraEmpieza;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }
                }

                //Verifico que el socio no tenga mas reserva
                $sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $datos_reserva["Fecha"] . "' and RG.IDServicio = '" . $datos_reserva["IDServicio"] . "' ORDER BY IDReservaGeneralInvitado Desc ";
                $qry_socio_grupo = $dbo->query($sql_socio_grupo);
                if ($dbo->rows($qry_socio_grupo) > 0 && $MaximoReservaSocioServicio <= 1):
                    $respuesta["message"] = "Lo sentimos ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                endif;
                $IDReservaOtra = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDSocio = '" . $IDSocio . "' and Fecha = '" . $datos_reserva["Fecha"] . "' and IDServicio = '" . $datos_reserva["IDServicio"] . "' ");
                if (!empty($IDReservaOtra)) {
                    $respuesta["message"] = "Lo sentimos ya tiene una reserva activa para el mismo dia";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }
                //Fin

                $datos_socio_agregado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                if ($datos_socio_agregado["PermiteReservar"] == "N") {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para reservar";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_invitados = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '" . $IDReserva . "'";
                $result_invitado = $dbo->query($sql_invitados);
                $total_invitados = $dbo->rows($result_invitado);

                if ((int) $MaximoInvitados > (int) $total_invitados):

                    //inserto al invitado
                    $insert_invitado = "INSERT INTO ReservaGeneralInvitado (IDReservaGeneral, IDSocio,AgregadoBotonPublico,FechaTrCr ) Values ('" . $IDReserva . "','" . $IDSocio . "','S',NOW())";
                    $dbo->query($insert_invitado);

                    //Notifico al dueño de la reserva que alguien se agregó al grupo
                    $datos_socio_dueno_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
                    $Mensaje = "Se agrego el socio " . $datos_socio_agregado["Nombre"] . " " . $datos_socio_agregado["Apellido"] . " a la reserva del día: " . $datos_reserva["Fecha"] . " Hora: " . $datos_reserva["Hora"];
                    //SIMUtil::enviar_notificacion_push_general($IDClub,$datos_socio_dueno_reserva["IDSocio"],$Mensaje);

                    $respuesta["message"] = "Agregado correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                else:
                    $respuesta["message"] = "Lo sentimos la reserva ya tiene el cupo completo de invitados";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                endif;

            } else {
                $respuesta["message"] = "14. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function actualiza_payande($IDSocio = "")
    {
            $dbo = &SIMDB::get();

            $IDClub = 27;

            if (!empty($IDSocio)):
                $condicion = " and IDSocio = '" . $IDSocio . "'";
            endif;

            //La base de Mi Club es la maestra entre mi club y el sistema de la página
            $sql_socio = "Select * From Socio Where IDClub = '" . $IDClub . "' " . $condicion . " Order by IDSocio ASC";
            $result_socio = $dbo->query($sql_socio);
            while ($row_socio_bd = $dbo->fetchArray($result_socio)):
                $IDSocio = $row_socio_bd["IDSocio"];
                $DocumentoSocioPadre = "";
                $NumeroDocumento = $row_socio_bd["NumeroDocumento"];
                if ($row_socio_bd["TipoSocio"] == "Socio"):
                    $TipoSocio = "TITULAR";
                else:
                    $TipoSocio = "BENEFICIARIO";
                    //Averiguo el socio padre
                    if (!empty($row_socio_bd["AccionPadre"])) {
                        $DocumentoSocioPadre = $dbo->getFields("Socio", "NumeroDocumento", "Accion = '" . $row_socio_bd["AccionPadre"] . "'");
                    }

                endif;

                $Nombre = $row_socio_bd["Nombre"];
                $Apellido = $row_socio_bd["Apellido"];
                $Telefono = $row_socio_bd["Telefono"];
                $Celular = $row_socio_bd["Celular"];
                $Email = $row_socio_bd["CorreoElectronico"];
                $FechaNacimiento = $row_socio_bd["FechaNacimiento"];
                $Usuario = $row_socio_bd["Email"];
                $Clave = $row_socio_bd["Clave"];
                $Accion = $row_socio_bd["Accion"];
                if ($row_socio_bd["IDEstadoSocio"] == "1") {
                    $Autorizado = "S";
                } else {
                    $Autorizado = "N";
                }

                $IDSocioPadre = $row_socio_bd["IDSocioSistemaExterno"];
                $key = "P4y4nd3Reser";
                $action = "actualizasocio";
                $url = "http://www.clubpayande.com/reservas/services/club.php";
                $post = [
                    'key' => $key,
                    'action' => $action,
                    'IDSocio' => $IDSocio,
                    'NumeroDocumento' => $NumeroDocumento,
                    'TipoSocio' => $TipoSocio,
                    'Nombre' => $Nombre,
                    'Apellido' => $Apellido,
                    'Telefono' => $Telefono,
                    'Celular' => $Celular,
                    'Email' => $Email,
                    'Usuario' => $Usuario,
                    'Clave' => $Clave,
                    'Accion' => $Accion,
                    'Autorizado' => $Autorizado,
                    'IDSocioSistemaExterno' => $IDSocioSistemaExterno,
                    'DocumentoSocioPadre' => $DocumentoSocioPadre,

                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                // execute!
                $response = curl_exec($ch);
                // close the connection, release resources used
                curl_close($ch);
                //print_r($response);
                //inserta _log
                $contador++;
            endwhile;

            return "<br>Proceso terminado con exito. Registros: " . $contador;

        }

        public function verifica_ver_encuesta($r, $IDSocio)
    {
            $dbo = &SIMDB::get();
            $mostrar_encuesta = 1;
            $IDEncuesta = $r["IDEncuesta"];

            //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
            if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T' || $r["DirigidoA"] == 'E') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "EE")) {
                if ($r["DirigidoAGeneral"] == "SE") {
                    $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                    if (count($array_invitados) > 0) {
                        foreach ($array_invitados as $id_invitado => $datos_invitado) {
                            if (!empty($datos_invitado)) {
                                $array_datos_invitados = explode("-", $datos_invitado);
                                $array_socios_encuesta[] = $array_datos_invitados[1];
                            }
                        }
                    }
                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }

                } elseif ($r["DirigidoAGeneral"] == "EE") {
                $array_invitados = explode("|||", $r["UsuarioSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $//si va dirigido a varios grupos

                $arregloGrupos = explode("|||", $r["InvitadoSeleccionGrupo"]);

                if (count($arregloGrupos) > 0) {
                    foreach ($arregloGrupos as $id_grupo => $datos_grupo) {
                        if (!empty($datos_grupo)) {
                            $array_datos_grupo = explode("-", $datos_grupo);
                            $mostrar_encuesta = SIMWebserviceApp::verificar_socio_grupo($IDSocio, $array_datos_grupo[1]);
                        }
                        if ($mostrar_encuesta == 1) {
                            break;
                        }
                    }
                }
            }
        }

        if ($r["UnaporSocio"] == 'S') {
            $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta = '" . $IDEncuesta . "' Limit 1";
            $r_resp = $dbo->query($sql_resp);
            if ($dbo->rows($r_resp) > 0) {
                $mostrar_encuesta = 0;
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_dotacion($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }

            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $r["IDGrupoSocio"] . "'");
                $array_invitados = explode("|||", $SociosGrupo);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        $array_socios_encuesta[] = $datos_invitado;
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_encuesta2($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }

            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $r["IDGrupoSocio"] . "'");
                $array_invitados = explode("|||", $SociosGrupo);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        $array_socios_encuesta[] = $datos_invitado;
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_diagnostico($r, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }

            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $r["IDGrupoSocio"] . "'");
                $array_invitados = explode("|||", $SociosGrupo);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        $array_socios_encuesta[] = $datos_invitado;
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "E") {
                $mostrar_encuesta = 1;
                //$mostrar_encuesta=0;

            }
        }

        return $mostrar_encuesta;
    }

    public function verifica_ver_votacion($r, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "L")) {

            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }

            } elseif ($r["DirigidoAGeneral"] == "GS") {

                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $r["IDGrupoSocio"] . "'");
                $array_invitados = explode("|||", $SociosGrupo);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        $array_socios_encuesta[] = $datos_invitado;
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "L") {
                $IDVotacionEvento = $dbo->getFields("VotacionEventoVotacion", "IDVotacionEvento", "IDVotacion = '" . $r["IDVotacion"] . "'  and Activa = 'S'");
                if (empty($IDVotacionEvento)) {
                    $mostrar_encuesta = 0;
                } else {

                    $sql_votante = "SELECT IDSocio
														FROM VotacionVotante
														WHERE Presente = 'S' and Moroso= 'N' and IDVotacionEvento = '" . $IDVotacionEvento . "'";

                    $r_votante = $dbo->query($sql_votante);
                    while ($row_votante = $dbo->fetchArray($r_votante)) {
                        $array_socios_encuesta[] = $row_votante["IDSocio"];
                    }

                    if (in_array($IDSocio, $array_socios_encuesta)) {
                        $mostrar_encuesta = 1;
                    } else {
                        $mostrar_encuesta = 0;
                    }

                    if ($mostrar_encuesta == 1) {
                        $ActivaVitacion = $dbo->getFields("VotacionEventoVotacion", "Activa", "IDVotacion = '" . $r["IDVotacion"] . "' and IDVotacionEvento = '" . $IDVotacionEvento . "'");
                        if ($ActivaVitacion == "S") {
                            $mostrar_encuesta = 1;
                        } else {
                            $mostrar_encuesta = 0;
                        }

                    }
                    //verifico si la votacion esta activa

                }

            }
        }

        if ($mostrar_encuesta == 1) {
            $datos_votacion = $dbo->fetchAll("Votacion", " IDVotacion = '" . $r["IDVotacion"] . "' ", "array");
            if ($datos_votacion["UnaporSocio"] == "S") {
                //verifico si la persona ya voto

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                }

                $sql_votacion = "SELECT * FROM VotacionRespuesta WHERE IDVotacion = '" . $r["IDVotacion"] . "' AND IDSocio = '" . $IDSocio . "'";
                $result_votacion = $dbo->query($sql_votacion);
                if ($dbo->rows($result_votacion) <= 0) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }

            } else {
                $mostrar_encuesta = 1;
            }
        }

        return $mostrar_encuesta;
    }

    public function verificar_notificacion_local($IDSocio, $IDUsuario, $IDClub)
    {
        $dbo = &SIMDB::get();

        //Diagnostico
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $r_sql = $dbo->query($sql);
        while ($r = $dbo->fetchArray($r_sql)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_diagnostico($r, $IDSocio, $IDUsuario);
            if ($mostrar_encuesta == 1) {
                $array_modulos["99"][] = $r["IDDiagnostico"];
            }
        }
        //Fin Diagnostico

        //Diagnostico
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Dotacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        $message = $dbo->rows($qry) . " Encontrados";
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = self::verifica_ver_dotacion($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["102"][] = $r["IDDotacion"];
            }
        }
        //Fin Diagnostico

        //Encuesta Calificada
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta2 WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta2($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["101"][] = $r["IDEncuesta2"];
            }
        }
        //Fin encuesta

        //Encuesta Normal
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDSocio);
            if ($mostrar_encuesta == 1) {
                $array_modulos["58"][] = $r["IDEncuesta"];
            }
        }
        //Fin encuesta

        //Votacion
        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Votacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        $message = $dbo->rows($qry) . " Encontrados";
        while ($r = $dbo->fetchArray($qry)) {
            $mostrar_encuesta = SIMWebServiceApp::verifica_ver_votacion($r, $IDSocio, $IDUsuario);
            if ($mostrar_encuesta == 1) {
                $array_modulos["70"][] = $r["IDVotacion"];
            }
        }
        //Fin Votacion

        foreach ($array_modulos as $id_modulo => $detalle_modulo) {
            foreach ($detalle_modulo as $id_detalle => $valor_detalle) {
                $array_condicion_modulo[] = " (IDModulo = '" . $id_modulo . "' and IDDetalle = '" . $valor_detalle . "') ";
            }
        }

        if (count($array_condicion_modulo) > 0) {
            $condicion_notif = implode(" or ", $array_condicion_modulo);
        } else {
            $condicion_notif = " and IDModulo = 0 and IDSeccion = 0 ";
        }

        $condicion = " and  (" . $condicion_notif . ")";
        return $condicion;

    }

    public function get_campo_acceso($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        //Pregunta
        $pregunta = array();
        $response_pregunta = array();
        $sql_respuesta = "Select * From PreguntaAcceso Where IDClub = '" . $IDClub . "' and Publicar = 'S' Order by Orden";
        $r_encuesta = $dbo->query($sql_respuesta);
        if ($dbo->rows($r_encuesta) > 0) {
            $message = $dbo->rows($r_encuesta) . " Encontrados";
            while ($row_pregunta = $dbo->FetchArray($r_encuesta)):
                $pregunta["IDPreguntaAcceso"] = $row_pregunta["IDPreguntaAcceso"];
                $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                $pregunta["Valores"] = $row_pregunta["Valores"];
                $pregunta["Orden"] = $row_pregunta["Orden"];
                array_push($response, $pregunta);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_encuesta($IDClub, $IDSocio = "", $IDUsuario = "")
    {

            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
            } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($IDSocio)) {
                    $mostrar_encuesta = self::verifica_ver_encuesta($r, $IDSocio);
                } elseif (!empty($IDUsuario)) {
                    $mostrar_encuesta = self::verifica_ver_encuesta($r, $IDUsuario);
                }

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDEncuesta"] = $r["IDEncuesta"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    if (!empty($r["ImagenEncuesta"])):
                        $foto = BANNERAPP_ROOT . $r["ImagenEncuesta"];
                    else:
                        $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '58' and IDClub='" . $IDClub . "' ", "array");
                        $icono_modulo = $datos_modulo["Icono"];
                        if (!empty($datos_modulo["Icono"])):
                            $foto = MODULO_ROOT . $datos_modulo["Icono"];
                        else:
                            $foto = "";
                        endif;
                    endif;

                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM EncuestaRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDEncuesta"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "Select * From Pregunta Where IDEncuesta = '" . $encuesta["IDEncuesta"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)):
                        $pregunta["IDPregunta"] = $row_pregunta["IDPregunta"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        if ($row_pregunta["TipoCampo"] == "imagen") {
                            $pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPregunta"];
                        }

                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_dotacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
            } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Dotacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = self::verifica_ver_dotacion($r, $IDSocio);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDDotacion"] = $r["IDDotacion"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '102' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])):
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else:
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM DotacionRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDDotacion"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "Select * From PreguntaDotacion Where IDDotacion = '" . $encuesta["IDDotacion"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)):
                        $pregunta["IDPreguntaDotacion"] = $row_pregunta["IDPreguntaDotacion"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_diagnostico($IDClub, $IDSocio = "", $IDUsuario = "")
    {

            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
            } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = self::verifica_ver_diagnostico($r, $IDSocio, $IDUsuario);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDDiagnostico"] = $r["IDDiagnostico"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];
                    $encuesta["PermiteBeneficiarios"] = $r["PermiteBeneficiarios"];
                    $encuesta["MostrarTodas"] = $r["MostrarTodas"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '99' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])):
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else:
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM DiagnosticoRespuesta WHERE IDSocio='" . $IDSocio . "' and IDDiagnostico = '" . $r["IDDiagnostico"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * FROM PreguntaDiagnostico Where IDDiagnostico = '" . $encuesta["IDDiagnostico"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)):
                        $pregunta["IDPreguntaDiagnostico"] = $row_pregunta["IDPreguntaDiagnostico"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM DiagnosticoOpcionesRespuesta WHERE IDDiagnosticoPregunta = '" . $row_pregunta["IDPreguntaDiagnostico"] . "' order by Orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDDiagnosticoPregunta"] = $row_opciones["IDDiagnosticoPregunta"];
                            $opciones_respuesta["IDDiagnosticoOpcionesRespuesta"] = $row_opciones["IDDiagnosticoOpcionesRespuesta"];
                            //$opciones_respuesta[ "IDDiagnosticoPreguntaSiguiente" ] = $row_opciones[ "IDDiagnosticoPreguntaSiguiente" ];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            $opciones_respuesta["Terminar"] = $row_opciones["Terminar"];
                            $opciones_respuesta["Peso"] = $row_opciones["Peso"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        $pregunta["Valores"] = $response_valores;
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_mis_diagnosticos($IDClub, $IDSocio, $IDUsuario)
    {

            $dbo = &SIMDB::get();

            //Socio
            if (!empty($IDSocio) || !empty($IDUsuario)) {

                if (!empty($IDSocio)) {
                    $condicion = " IDSocio = '" . $IDSocio . "' ";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                    $info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                }

                if (!empty($IDUsuario)) {
                    $condicion = " IDUsuario = '" . $IDUsuario . "' ";
                    $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
                    $info = $datos_usuario["Nombre"];
                }

                $response = array();
                $sql = "SELECT IDDiagnostico, IDDiagnosticoRespuesta,FechaTrCr FROM DiagnosticoRespuesta WHERE  " . $condicion . " GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " Encontrados";
                    while ($r = $dbo->fetchArray($qry)) {
                        $DetalleResp = "";
                        $objeto["IDDiagnostico"] = $r["IDDiagnostico"];
                        $objeto["Fecha"] = substr($r["FechaTrCr"], 0, 10);
                        $objeto["Hora"] = substr($r["FechaTrCr"], 10);
                        $objeto["Texto"] = $info;

                        //Consulta las respuestas del diagnostico
                        $sql_detalle = "SELECT PD.EtiquetaCampo, DR.IDDiagnostico, DR.IDDiagnosticoRespuesta,DR.FechaTrCr, DR.Valor
														FROM DiagnosticoRespuesta DR, PreguntaDiagnostico PD
														WHERE " . $condicion . " and DR.IDPreguntaDiagnostico=PD.IDPreguntaDiagnostico
														AND DR.IDDiagnostico = '" . $r["IDDiagnostico"] . "' and DR.FechaTrCr between '" . substr($r["FechaTrCr"], 0, 10) . " 00:00:00' and '" . substr($r["FechaTrCr"], 0, 10) . " 23:59:59' ";
                        $qry_detalle = $dbo->query($sql_detalle);
                        while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                            $DetalleResp .= "<b>" . $r_detalle["EtiquetaCampo"] . "</b>=" . $r_detalle["Valor"] . "<br>";
                        }

                        $objeto["Descripcion"] = $DetalleResp;
                        array_push($response, $objeto);
                    } //ednw hile
                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } //End if
            else {
                    $respuesta["message"] = "No se encontraron registros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else
            } else {
                $respuesta["message"] = "DR. Faltan Parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;

        } // fin function

        public function set_respuesta_diagnostico($IDClub, $IDSocio, $IDDiagnostico, $Respuestas, $IDUsuario = "", $NumeroDocumento = "", $Nombre = "", $IDBeneficiario = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario) || !empty($NumeroDocumento)) && !empty($IDDiagnostico)) {

                if (!empty($IDBeneficiario)) {
                    $IDSocio = $IDBeneficiario;
                }

                $guardar_encuesta = 0;
                $datos_diagnostico = $dbo->fetchAll("Diagnostico", " IDDiagnostico = '" . $IDDiagnostico . "' and IDClub='" . $IDClub . "' ", "array");
                $contesta_una = $datos_diagnostico["UnaporSocio"];
                if ($contesta_una == "S") {
                    $sql_resp = "SELECT IDDiagnostico From DiagnosticoRespuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $IDDiagnostico . "' Limit 1";
                    $r_resp = $dbo->query($sql_resp);
                    if ($dbo->rows($r_resp) <= 0) {
                        $guardar_encuesta = 1;
                    }
                } else {
                    $guardar_encuesta = 1;
                }

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                    $TipoUsuario = "Funcionario";
                    $condicion_unica = " and IDUsuario='" . $IDUsuario . "'";
                } elseif (!empty($IDSocio)) {
                $TipoUsuario = "Socio";
                $condicion_unica = " and IDSocio='" . $IDSocio . "'";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

                //caso especial lagartos no permite a padres o madre mas de 4 veces al mes
                if ($IDClub == 7 && ($datos_socio["IDParentesco"] == "13" || $datos_socio["IDParentesco"] == "14" || $datos_socio["IDParentesco"] == "16" || $datos_socio["IDParentesco"] == "17")) {
                    /*
                $IngresoPermitidoLag=4;
                $TotalIngresos=SIMWebService::valida_cantidad_ingresos($IDClub,$datos_socio["IDSocio"]);
                if((int)$TotalIngresos>=(int)$IngresoPermitidoLag){
                $respuesta["message"] = "Atencion supera los 4 ingresos permitidos al mes";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                return $respuesta;
                }
                 */
                }
                //Fin Caso especial

            } else {
                $TipoUsuario = "Externo";
                $condicion_unica = " and NumeroDocumento='" . $NumeroDocumento . "'";
                //Verifico que ese externo tenga invitacion
                $result_validacion = self::get_verifica_documento($IDClub, $NumeroDocumento);
                if ($result_validacion["success"] == false) {
                    $respuesta["message"] = "Ya había registrado los datos el día de hoy";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $result_validacion;
                }
            }

            $fecha_hoy = date("Y-m-d") . " 00:00:00";
            $sql_unica = "SELECT IDDiagnosticoRespuesta FROM  DiagnosticoRespuesta WHERE IDDiagnostico = '" . $IDDiagnostico . "' and FechaTrCr >= '" . $fecha_hoy . "' " . $condicion_unica;
            $r_unica = $dbo->query($sql_unica);
            $total_unica = $dbo->rows($r_unica);
            if ($total_unica > 0 && $IDClub != 72) {
                $respuesta["message"] = "Ya había registrado los datos el día de hoy";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            if ($guardar_encuesta == 1) {

                $sql_pregunta = "SELECT IDPreguntaDiagnostico,Obligatorio FROM PreguntaDiagnostico WHERE IDDiagnostico = '" . $IDDiagnostico . "' ";
                $r_pregunta = $dbo->query($sql_pregunta);
                while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
                    $array_pregunta[$row_pregunta["IDPreguntaDiagnostico"]] = $row_pregunta["Obligatorio"];
                }

                $datos_correctos = "S";
                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0):

                    foreach ($datos_respuesta as $detalle_respuesta) {
                        if ($detalle_respuesta["Valor"] == "null" && $array_pregunta[$detalle_respuesta["IDPreguntaDiagnostico"]] == "S") {
                            $datos_correctos = "N";
                            $pre = $detalle_respuesta["IDPreguntaDiagnostico"];

                        } else {
                            $datos_correctos = "S";
                            break;
                        }
                    }
                    if ($datos_correctos == "N") {
                        $respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique" . $pre;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    foreach ($datos_respuesta as $detalle_respuesta):
                        if ($detalle_respuesta["Valor"] != "null") {
                            $sql_datos_form = $dbo->query("INSERT INTO DiagnosticoRespuesta (IDDiagnostico, IDSocio, IDUsuario, IDPreguntaDiagnostico, IDDiagnosticoOpcionesRespuesta, NumeroDocumento, Nombre, TipoUsuario, Valor, Peso, FechaTrCr) Values ('" . $IDDiagnostico . "','" . $IDSocio . "','" . $IDUsuario . "','" . $detalle_respuesta["IDPreguntaDiagnostico"] . "','" . $detalle_respuesta["ValorID"] . "','" . $NumeroDocumento . "','" . $Nombre . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $detalle_respuesta["Peso"] . "',NOW())");
                            $suma_peso += $detalle_respuesta["Peso"];
                            $datos_pregunta = $dbo->fetchAll("PreguntaDiagnostico", " IDPreguntaDiagnostico = '" . $detalle_respuesta["IDPreguntaDiagnostico"] . "' ", "array");
                            $respuestas_diagnostico .= $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"] . "<br>";
                            if ($detalle_respuesta["Terminar"] == "S" && $detalle_respuesta["Peso"] > 0) {
                                $suma_peso = $datos_diagnostico["PesoMaximo"] + 1;
                            }
                        }
                    endforeach;
                endif;

                $RespuestaDiagnostico = $datos_diagnostico["MensajeBien"];
                if ($suma_peso >= $datos_diagnostico["PesoMaximo"] && !empty($datos_diagnostico["EmailAlerta"])) {
                    SIMUtil::notifica_alerta_diagnostico($IDClub, $IDSocio, $IDDiagnostico, $respuestas_diagnostico, $IDUsuario, $datos_diagnostico["EmailAlerta"], $suma_peso, $TipoUsuario);
                    $RespuestaDiagnostico = $datos_diagnostico["MensajeMal"];
                    $estado_salud = "UPDATE Socio Set IDEstadoSalud = 2 Where IDSocio='" . $IDSocio . "'";
                    $dbo->query($estado_salud);
                    //regisyro el seguimiento
                    $sql_insert = "INSERT INTO SocioSeguimiento (IDSocioSeguimiento,IDSocio,IDUsuario,IDEstadoSalud,Observacion,Fecha,FechaTrCr)
																VALUES ('','" . $IDSocio . "','" . $IDUsuario . "','2','Autodiagnostico',CURDATE(),now()) ";
                    $dbo->query($sql_insert);
                }

                if ($datos_socio["IDEstadoSocio"] == 4) {
                    $RespuestaDiagnostico .= " Para poder ingresar al Club, por favor comuníquese con el Departamento de Cartera ";
                }

                $respuesta["message"] = $RespuestaDiagnostico;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "Este diagnostico ya había sido contestada por ud, solo se permite 1 vez por día";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    }

    public function get_encuesta_calificacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta2 WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = self::verifica_ver_encuesta2($r, $IDSocio);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDEncuesta"] = $r["IDEncuesta2"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '101' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])):
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else:
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM  Encuesta2Respuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta2 = '" . $r["IDEncuesta2"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * FROM PreguntaEncuesta2 Where IDEncuesta2 = '" . $encuesta["IDEncuesta"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)):
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "'", "array");
                        $TextoPregunta = $row_pregunta["EtiquetaCampo"];
                        $TextoPregunta = str_replace("[NombreSocio]", $datos_socio["Nombre"] . " " . $datos_socio["Apellido"], $TextoPregunta);
                        $TextoPregunta = str_replace("[AccionSocio]", $datos_socio["Accion"], $TextoPregunta);
                        $TextoPregunta = str_replace("[CasaSocio]", $datos_socio["Predio"], $TextoPregunta);
                        $TextoPregunta = str_replace("[DocumentoSocio]", $datos_socio["NumeroDocumento"], $TextoPregunta);

                        $pregunta["IDPreguntaEncuesta"] = $row_pregunta["IDPreguntaEncuesta2"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $TextoPregunta;
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM Encuesta2OpcionesRespuesta WHERE IDEncuesta2Pregunta = '" . $row_pregunta["IDPreguntaEncuesta2"] . "' Order by orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDEncuestaPregunta"] = $row_opciones["IDEncuesta2Pregunta"];
                            $opciones_respuesta["IDEncuesta2OpcionesRespuesta"] = $row_opciones["IDEncuesta2OpcionesRespuesta"];
                            //$opciones_respuesta[ "IDDiagnosticoPreguntaSiguiente" ] = $row_opciones[ "IDDiagnosticoPreguntaSiguiente" ];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            $opciones_respuesta["RespuestaCorrecta"] = $row_opciones["RespuestaCorrecta"];
                            $opciones_respuesta["Puntos"] = $row_opciones["Puntos"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        $pregunta["Valores"] = $response_valores;
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_respuesta_encuesta_calificacion($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDEncuesta)) {
                $guardar_encuesta = 0;
                $datos_diagnostico = $dbo->fetchAll("Encuesta2", " IDEncuesta2 = '" . $IDEncuesta . "' and IDClub='" . $IDClub . "' ", "array");
                $contesta_una = $datos_diagnostico["UnaporSocio"];
                if ($contesta_una == "S") {
                    $sql_resp = "SELECT IDEncuesta2 From Encuesta2Respuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $IDDiagnostico . "' Limit 1";
                    $r_resp = $dbo->query($sql_resp);
                    if ($dbo->rows($r_resp) <= 0) {
                        $guardar_encuesta = 1;
                    }
                } else {
                    $guardar_encuesta = 1;
                }

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                    $TipoUsuario = "Funcionario";
                } else {
                    $TipoUsuario = "Socio";
                }

                if ($guardar_encuesta == 1) {
                    $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                    $datos_respuesta = json_decode($Respuestas, true);

                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_respuesta):
                            $sql_datos_form = $dbo->query("INSERT INTO Encuesta2Respuesta (IDEncuesta2, IDSocio, IDPreguntaEncuesta2, IDEncuesta2OpcionesRespuesta, TipoUsuario, Valor, Puntos, RespuestaCorrecta, FechaTrCr)
																													Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDEncuestaPregunta"] . "','" . $detalle_respuesta["ValorID"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $detalle_respuesta["Puntos"] . "','" . $detalle_respuesta["RespuestaCorrecta"] . "',NOW())");
                            $suma_puntos += $detalle_respuesta["Puntos"];
                        endforeach;
                    endif;

                    $respuesta["message"] = "Datos enviado correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = "Este encuesta ya había sido contestada por ud, solo se permite 1 vez por día";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_respuesta_encuesta($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario = "", $Archivo, $File = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDEncuesta)) {
                $guardar_encuesta = 0;
                $contesta_una = utf8_decode($dbo->getFields("Encuesta", "UnaporSocio", "IDEncuesta = '" . $IDEncuesta . "'"));
                if ($contesta_una == "S") {
                    $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta = '" . $IDEncuesta . "' Limit 1";
                    $r_resp = $dbo->query($sql_resp);
                    if ($dbo->rows($r_resp) <= 0) {
                        $guardar_encuesta = 1;
                    }
                } else {
                    $guardar_encuesta = 1;
                }

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                    $TipoUsuario = "Funcionario";
                } else {
                    $TipoUsuario = "Socio";
                }

                if ($guardar_encuesta == 1) {
                    $sql_pregunta = "SELECT IDPregunta, Obligatorio,TipoCampo FROM Pregunta WHERE IDEncuesta = '" . $IDEncuesta . "' ";
                    $r_pregunta = $dbo->query($sql_pregunta);
                    while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
                        $array_pregunta[$row_pregunta["IDPregunta"]] = $row_pregunta["Obligatorio"];
                        $array_preguntaImage[$row_pregunta["IDPregunta"]] = $row_pregunta["TipoCampo"];
                    }

                    $datos_correctos = "S";
                    $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                    $datos_respuesta = json_decode($Respuestas, true);
                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_respuesta) {
                            if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPregunta"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPregunta"]] != "imagen") {
                                $datos_correctos = "N";
                                $PreguntaValida = $detalle_respuesta["IDPregunta"];
                                break;
                            } else {
                                $datos_correctos = "S";
                            }
                        }
                        if ($datos_correctos == "N") {
                            $respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique." . $PreguntaValida;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }

                        foreach ($datos_respuesta as $detalle_respuesta):
                            $sql_datos_form = $dbo->query("Insert Into EncuestaRespuesta (IDEncuesta, IDSocio, IDPregunta,  TipoUsuario, Valor, FechaTrCr) Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                        endforeach;
                    endif;

                    //subir las imagenes
                    if (isset($File)) {
                        //$nombrefoto.=json_encode($File);
                        foreach ($File as $nombre_archivo => $archivo) {
                            $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                            $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
                            //$nombrefoto.=$archivo["name"];
                            //$nombrefoto.=json_encode($archivo);
                            $tamano_archivo = $archivo["size"];
                            if ($tamano_archivo >= 6000000) {
                                $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                $files = SIMFile::upload($File[$nombre_archivo], PQR_DIR, "IMAGE");
                                if (empty($files) && !empty($File[$nombre_archivo]["name"])):
                                    $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                $Archivo = $files[0]["innername"];
                                $actualiza_pregunta = "UPDATE EncuestaRespuesta SET Valor = '" . $Archivo . "' WHERE IDPregunta ='" . $IDPreguntaActualiza . "' and IDEncuesta  = '" . $IDEncuesta . "' and IDSocio = '" . $IDSocio . "' ORDER BY FechaTrCr DESC LIMIT 1";
                                $dbo->query($actualiza_pregunta);
                                //$nombrefoto.=    $actualiza_pregunta;
                            }
                        }
                    }

                    $respuesta_personalizada = $dbo->getFields("Encuesta", "RespuestaGuardar", "IDEncuesta = '" . $IDEncuesta . "'");
                    if (!empty($respuesta_personalizada)) {
                        $respuesta_enc = $respuesta_personalizada;
                    } else {
                        $respuesta_enc = "Guardado";
                    }

                    $respuesta["message"] = $respuesta_enc;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $datos_reserva;
                } else {
                    $respuesta["message"] = "Esta encuesta ya había sido contestada por ud, solo se permite 1 vez";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_respuesta_dotacion($IDClub, $IDSocio, $IDDotacion, $Respuestas, $IDUsuario = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDDotacion)) {
                $guardar_encuesta = 0;
                $contesta_una = utf8_decode($dbo->getFields("Dotacion", "UnaporSocio", "IDDotacion = '" . $IDDotacion . "'"));
                if ($contesta_una == "S") {
                    $sql_resp = "Select IDDotacion From DotacionRespuesta Where IDSocio = '" . $IDSocio . "' and IDDotacion = '" . $IDDotacion . "' Limit 1";
                    $r_resp = $dbo->query($sql_resp);
                    if ($dbo->rows($r_resp) <= 0) {
                        $guardar_encuesta = 1;
                    }
                } else {
                    $guardar_encuesta = 1;
                }

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                    $TipoUsuario = "Funcionario";
                } else {
                    $TipoUsuario = "Socio";
                }

                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                if ($guardar_encuesta == 1) {
                    $respuestas_dotacion = "";
                    $datos_respuesta = json_decode($Respuestas, true);
                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_respuesta):

                            $pregunta = $dbo->getFields("PreguntaDotacion", "EtiquetaCampo", "IDPreguntaDotacion = '" . $detalle_respuesta["IDPreguntaDotacion"] . "'");

                            $respuestas_dotacion .= $pregunta . " = " . $detalle_respuesta["Valor"] . "<br>";
                            $sql_datos_form = $dbo->query("Insert Into DotacionRespuesta (IDDotacion, IDSocio, IDPreguntaDotacion, TipoUsuario, Valor, FechaTrCr) Values ('" . $IDDotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPreguntaDotacion"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                        endforeach;
                    endif;

                    $correos = $dbo->getFields("Dotacion", "EmailAlerta", "IDDotacion = '" . $IDDotacion . "'");
                    if (!empty($correos)) {
                        SIMUtil::enviar_notificacion_dotacion($IDClub, $TipoUsuario, $IDSocio, $IDDotacion, $respuestas_dotacion, $correos);
                    }

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $datos_reserva;
                } else {
                    $respuesta["message"] = "Esta encuesta ya había sido contestada por ud, solo se permite 1 vez";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_votacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

            $dbo = &SIMDB::get();
            $response = array();

            if (!empty($IDSocio)) {
                $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T' or DirigidoA = 'L') ";
            } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T' or DirigidoA = 'L') ";
        }

        $sql = "SELECT * FROM Votacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = self::verifica_ver_votacion($r, $IDSocio, $IDUsuario);
                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDVotacion"] = $r["IDVotacion"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];
                    $encuesta["Georeferenciacion"] = $r["Georeferenciacion"];
                    $encuesta["Latitud"] = $r["Latitud"];
                    $encuesta["Longitud"] = $r["Longitud"];
                    $encuesta["Rango"] = $r["Rango"];
                    $encuesta["MensajeFueraRango"] = $r["MensajeFueraRango"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '70' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])):
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else:
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM VotacionRespuesta WHERE IDSocio='" . $IDSocio . "' and IDVotacion = '" . $r["IDVotacion"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * From PreguntaVotacion Where IDVotacion = '" . $encuesta["IDVotacion"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)):
                        $pregunta["IDPregunta"] = $row_pregunta["IDPregunta"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];

                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM VotacionOpcionesRespuesta WHERE IDPreguntaVotacion = '" . $row_pregunta["IDPregunta"] . "' order by Orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDPregunta"] = $row_opciones["IDPreguntaVotacion"];
                            $opciones_respuesta["IDPreguntaRespuesta"] = $row_opciones["IDVotacionOpcionesRespuesta"];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        //$pregunta["ValoresLista"] = $response_valores;

                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;
                    $encuesta["Preguntas"] = $response_pregunta;

                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_respuesta_votacion($IDClub, $IDSocio, $IDVotacion, $Respuestas, $IDUsuario = "", $Dispositivo = "")
    {
            $dbo = &SIMDB::get();
            $IP = SIMUtil::get_IP();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDVotacion)) {
                $guardar_encuesta = 0;
                $contesta_una = utf8_decode($dbo->getFields("Votacion", "UnaporSocio", "IDVotacion = '" . $IDVotacion . "'"));
                $nombre_votacion = utf8_decode($dbo->getFields("Votacion", "Nombre", "IDVotacion = '" . $IDVotacion . "'"));
                if ($contesta_una == "S") {
                    $sql_resp = "Select IDVotacion From VotacionRespuesta Where IDSocio = '" . $IDSocio . "' and IDVotacion = '" . $IDVotacion . "' Limit 1";
                    $r_resp = $dbo->query($sql_resp);
                    if ($dbo->rows($r_resp) <= 0) {
                        $guardar_encuesta = 1;
                    }
                } else {
                    $guardar_encuesta = 1;
                }

                if (!empty($IDUsuario)) {
                    $IDSocio = $IDUsuario;
                    $TipoUsuario = "Funcionario";
                } else {
                    $TipoUsuario = "Socio";
                }

                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                if ($guardar_encuesta == 1) {
                    $datos_respuesta = json_decode($Respuestas, true);
                    if (count($datos_respuesta) > 0):
                        foreach ($datos_respuesta as $detalle_respuesta):

                            //reviso si existeun evento activo
                            $sql_evento = "SELECT IDVotacionEvento FROM VotacionEvento WHERE IDClub = '" . $IDClub . "' and Activo = 'S' Order by IDVotacionEvento DESC Limit 1";
                            $r_evento = $dbo->query($sql_evento);
                            $row_evento = $dbo->fetchArray($r_evento);
                            if ($row_evento["IDVotacionEvento"] > 0) {
                                $IDEvento = $row_evento["IDVotacionEvento"];
                            } else {
                                $IDEvento = "1";
                            }
                            //Verifico el coeficiente del votante
                            $coeficiente = SIMUtil::verifica_coeficiente($IDSocio, $IDEvento);
                            if ((float) ($coeficiente) <= 0) {
                                $PesoVoto = 1;
                            } else {
                                $PesoVoto = $coeficiente;
                            }

                            $valores_lista = $detalle_respuesta["ValoresLista"];
                            if (count($valores_lista) > 0) {
                                foreach ($valores_lista as $detalle_lista) {
                                    $sql_datos_form = "INSERT INTO VotacionRespuesta (IDVotacion, IDSocio, IDPregunta,  IDVotacionOpcionesRespuesta , TipoUsuario, Valor, ValorID, PesoVoto, IP, Dispositivo, FechaTrCr)
																												 		 Values ('" . $IDVotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $detalle_lista["IDPreguntaRespuesta"] . "','" . $TipoUsuario . "','" . $detalle_lista["Valor"] . "','" . $detalle_lista["ValorID"] . "','" . $PesoVoto . "','" . $IP . "','" . $Dispositivo . "',NOW())";
                                    $dbo->query($sql_datos_form);
                                }
                            } else {
                                $sql_datos_form = $dbo->query("INSERT INTO VotacionRespuesta (IDVotacion, IDSocio, IDPregunta, TipoUsuario, Valor, PesoVoto, IP, Dispositivo,FechaTrCr) Values ('" . $IDVotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $PesoVoto . "','" . $IP . "','" . $Dispositivo . "',NOW())");
                            }

                        endforeach;
                        SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, "Tu votacion a: " . $nombre_votacion . " fue contabilizada");
                    endif;
                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $datos_reserva;
                } else {
                    $respuesta["message"] = "Ya había votado, solo se permite 1 vez";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_correspondencia($IDClub, $IDSocio, $Tag)
    {
            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $condicion = " and IDSocio = '" . $IDSocio . "'";
            }

            if (!empty($Tag)) {
                $condicion = " and ( Nombre = '" . $Tag . "' or Apellido = '" . $Tag . "' or Accion = '" . $Tag . "' or Predio = '" . $Tag . "' )";
            }

            $sql = "SELECT * FROM Socio 	WHERE IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Nombre ASC ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($row = $dbo->fetchArray($qry)) {
                    $socio["IDClub"] = $IDClub;
                    $socio["IDSocio"] = $row["IDSocio"];
                    $socio["Predio"] = $row["Predio"];

                    $response_cat_correspondencia = array();
                    $sql_cat_correspondencia = $dbo->query("SELECT * FROM CategoriaCorrespondencia WHERE IDClub = '" . $IDClub . "' ORDER BY Nombre ASC");
                    while ($r_cat_correspondencia = $dbo->fetchArray($sql_cat_correspondencia)) {
                        $dato_cat_correspondencia["IDCategoriaCorrespondencia"] = $r_cat_correspondencia["IDCategoriaCorrespondencia"];
                        $dato_cat_correspondencia["Nombre"] = $r_cat_correspondencia["Nombre"];
                        $dato_cat_correspondencia["ServicioPublico"] = $r_cat_correspondencia["ServicioPublico"];

                        $response_correspondencia = array();

                        $sql_correspondencia = $dbo->query("SELECT C.*, TC.Nombre as NombreCorrespondencia
											 																	  FROM Correspondencia C, TipoCorrespondencia TC, CategoriaCorrespondencia CC
																													WHERE C.IDTipoCorrespondencia=TC.IDTipoCorrespondencia
																													AND CC.IDCategoriaCorrespondencia = TC.IDCategoriaCorrespondencia
																													AND TC.IDCategoriaCorrespondencia= '" . $dato_cat_correspondencia["IDCategoriaCorrespondencia"] . "'
																													AND C.IDSocio = '" . $row["IDSocio"] . "'
																													ORDER BY FIELD (IDCorrespondenciaEstado,'1','2'),IDCorrespondenciaEstado ASC");

                        while ($r_correspondencia = $dbo->fetchArray($sql_correspondencia)) {
                            $dato_correspondencia["IDCorrespondencia"] = $r_correspondencia["IDCorrespondencia"];
                            $dato_correspondencia["IDCorrespondenciaEstado"] = $r_correspondencia["IDCorrespondenciaEstado"];
                            $dato_correspondencia["Estado"] = utf8_decode($dbo->getFields("CorrespondenciaEstado", "Nombre", "IDCorrespondenciaEstado = '" . $r_correspondencia["IDCorrespondenciaEstado"] . "'"));
                            $dato_correspondencia["NombreCorrespondencia"] = $r_correspondencia["NombreCorrespondencia"];
                            $dato_correspondencia["Destinatario"] = $r_correspondencia["Destinatario"];
                            $dato_correspondencia["FechaRecepcion"] = $r_correspondencia["FechaRecepcion"];
                            $dato_correspondencia["FechaEntrega"] = $r_correspondencia["FechaEntrega"];
                            $dato_correspondencia["EntregadoA"] = $r_correspondencia["EntregadoA"];
                            if (!empty($r_correspondencia["Archivo"])) {
                                $foto = CORRESPONDENCIA_ROOT . $r_correspondencia["Archivo"];
                            } else {
                                $foto = "";
                            }

                            $dato_correspondencia["Archivo"] = $foto;
                            $dato_correspondencia["EntregadoPor"] = utf8_decode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_correspondencia["IDUsuarioEntrega"] . "'"));
                            array_push($response_correspondencia, $dato_correspondencia);
                        }

                        $dato_cat_correspondencia["Nombre"] = $r_cat_correspondencia["Nombre"];
                        $dato_cat_correspondencia["DatosCorrespondencia"] = $response_correspondencia;
                        array_push($response_cat_correspondencia, $dato_cat_correspondencia);

                    }
                    $socio["Correspondencia"] = $response_cat_correspondencia;
                    array_push($response, $socio);
                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                }

            } //End if
        else {
                $respuesta["message"] = "No se han encontraron resultados";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function get_tipo_correspondencia($IDClub)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT TC.*,CC.Nombre as Categoria,CC.ServicioPublico FROM TipoCorrespondencia TC, CategoriaCorrespondencia CC WHERE TC.IDCategoriaCorrespondencia = CC.IDCategoriaCorrespondencia and TC.Publicar = 'S' and TC.IDClub = '" . $IDClub . "' ORDER BY TC.Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $tipo_corresp["IDTipoCorrespondencia"] = $r["IDTipoCorrespondencia"];
                    $tipo_corresp["Nombre"] = $r["Nombre"] . " - " . $r["Categoria"];
                    $tipo_corresp["EsServicioPublico"] = $r["ServicioPublico"];
                    array_push($response, $tipo_corresp);

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

        public function set_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDTipoCorrrespondencia, $Vivienda, $Destinatario, $FechaRecepcion, $HoraRecepcion, $EntregarATodos, $Archivo, $File = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($EntregarATodos) && !empty($IDTipoCorrrespondencia)) {

                if ($EntregarATodos == "S") {
                    $condicion_socio = " ";
                } else {
                    $condicion_socio = " and IDSocio = '" . $IDSocio . "'";
                }

                if (isset($File)) {

                    $files = SIMFile::upload($File["Archivo"], CORRESPONDENCIA_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo"]["name"])):
                        $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];

                } //end if

                $sql_socio = "Select * From Socio Where IDClub = '" . $IDClub . "' " . $condicion_socio;
                $r_socio = $dbo->query($sql_socio);
                while ($row_socio = $dbo->FetchArray($r_socio)) {
                    $inserta_corresp = "INSERT INTO Correspondencia (IDClub, IDSocio, IDTipoCorrespondencia, IDUsuarioCrea, IDCorrespondenciaEstado, Vivienda, Destinatario, FechaRecepcion, Archivo, UsuarioTrCr, FechaTrCr) Values
																																		('" . $IDClub . "','" . $IDSocio . "','" . $IDTipoCorrrespondencia . "','" . $IDUsuario . "','1','" . $Vivienda . "','" . $Destinatario . "','" . $FechaRecepcion . " " . $HoraRecepcion . "','" . $Archivo . "','" . $IDUsuario . "',NOW())";
                    $dbo->query($inserta_corresp);
                    //Envio la notificacion
                    if ($EntregarATodos != "S") {
                        $Mensaje = "Nueva correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $IDTipoCorrrespondencia . "'"));
                        SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, $Mensaje);
                    } else {
                        //Inserto Cola de notificaciones
                        $sql_notif = "INSERT INTO ColaNotificacionPush (IDClub, IDSocio, Mensaje, UsuarioTrCr, FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $Mensaje . "','Aut',NOW())";
                        $dbo->query($sql_notif);
                    }
                }

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = "";

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_entrega_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDCorrespondencia, $FechaEntrega, $HoraEntrega, $EntregadoA)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDUsuario) && !empty($IDCorrespondencia) && !empty($EntregadoA)) {
                $datos_corresp = $dbo->fetchAll("Correspondencia", " IDCorrespondencia = '" . $IDCorrespondencia . "' ", "array");
                $update_corresp = "UPDATE Correspondencia Set IDUsuarioEntrega = '" . $IDUsuario . "', IDCorrespondenciaEstado=2, FechaEntrega='" . $FechaEntrega . " " . $HoraEntrega . "', EntregadoA='" . $EntregadoA . "', UsuarioTrEd='" . $IDUsuario . "',FechaTrEd =NOW() Where IDCorrespondencia = '" . $IDCorrespondencia . "'";
                $dbo->query($update_corresp);
                $Mensaje = "Correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $datos_corresp["IDTipoCorrrespondencia"] . "'")) . " entregado a " . $EntregadoA;
                SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, $Mensaje);

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = "";

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_codigo_qr($IDClub, $IDSocio, $CodigoQR)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($CodigoQR)) {

                //Por ahora guardo el qr en registro temporal para verificar que guarda
                $actualiza = "Update Socio Set ObservacionEspecial='" . $CodigoQR . "' Where IDSocio = '5533'";
                $dbo->query($actualiza);

                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = "";

            } else {
                $respuesta["message"] = "E1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_header_club($IDClub)
    {
            $dbo = &SIMDB::get();
            $response = array();

            //Obtener clima
            $apiKey = "4fb76e57d4c8d4ea7aa61f8ce5cb4eaa";
            $cityId = "3652462";
            $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response_temp = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response_temp);
            $currentTime = time();

            $temperatura = (int) $data->main->temp_max;

            //Header Reserva, clima, bienvenida
            $header_club["IDClub"] = $IDClub;
            $header_club["Tipo"] = "Reserva";
            $header_club["TextoBienvenida"] = "Bienvenido al club";
            $header_club["Latitud"] = "0.097035";
            $header_club["Longitud"] = "78.493674";
            $header_club["Icono"] = "https://miclubapp.com/img/iconos/iconogolfheader.png";
            $header_club["TextoIcono"] = "QTGC Golf.";
            $header_club["LinkIcono"] = "https://www.feg.org.ec/";
            $header_club["Clima"] = $temperatura . "º";
            $header_club["IconoClima"] = "https://miclubapp.com/img/iconos/iconoclima.png";
            $header_club["FotoFondo"] = "https://miclubapp.com/img/iconos/fotoarbol.png";

            array_push($response, $header_club);

            //Header Noticia
            unset($header_club);
            $sql = "SELECT * FROM Noticia WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "'" . " ORDER BY FechaInicio DESC,Orden Limit 2";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $header_club["IDClub"] = $IDClub;
                $header_club["Tipo"] = "Noticia";
                $header_club["IDNoticia"] = $r["IDNoticia"];
                $header_club["IDSeccion"] = $r["IDSeccion"];
                $header_club["Titular"] = $r["Titular"];
                $header_club["Introduccion"] = $r["Introduccion"];
                if (!empty($r["NoticiaFile"])):
                    if (strstr(strtolower($r["NoticiaFile"]), "http://")) {
                        $foto1 = $r["NoticiaFile"];
                    } else {
                        $foto1 = IMGNOTICIA_ROOT . $r["NoticiaFile"];
                    }

                    //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                else :
                    $foto1 = "";
                endif;
                $header_club["Imagen"] = $foto1;
                array_push($response, $header_club);
            }

            //Header Evento
            unset($header_club);
            $orden = " FechaEvento ASC";
            $sql = "SELECT * FROM Evento WHERE (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and FechaInicio <= NOW() and FechaFin >= NOW() " . $CondicionFechaEvento . " and IDClub = '" . $IDClub . "' " . $condiciones_noticia . " ORDER BY " . $orden . " Limit 2";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $header_club["IDClub"] = $r["IDClub"];
                $header_club["Tipo"] = "Evento";
                $header_club["IDEvento"] = $r["IDEvento"];
                $header_club["IDSeccion"] = $r["IDSeccionEvento"];
                $header_club["Titular"] = $r["FechaEvento"] . ": " . $r["Titular"];
                $header_club["Introduccion"] = $r["Introduccion"];

                if (!empty($r["EventoFile"])):
                    if (strstr(strtolower($r["EventoFile"]), "http://")) {
                        $foto1 = $r["EventoFile"];
                    } else {
                        $foto1 = IMGEVENTO_ROOT . $r["EventoFile"];
                    }

                    //$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
                else :
                    $foto1 = "";
                endif;
                $header_club["Imagen"] = $foto1;
            }

            if ($header_club["IDEvento"] > 0) {
                array_push($response, $header_club);
            }

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        } // fin function

        public function get_datos_socio_ws($IDClub, $IDSocio)
    {
            $dbo = &SIMDB::get();
            $response = array();

            $Accion = 01;

            require_once LIBDIR . 'nusoap/lib/nusoap.php';
            $client = new nusoap_client(ENDPOINT_CONDADO, 'wsdl');
            $err = $client->getError();
            if ($err) {
                echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
                echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
                exit();
            }

            $params = "
					<ns1:Socio xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
					  <ns1:Request>
						<ns1:TypeSQL></ns1:TypeSQL>
						<ns1:DynamicProperty></ns1:DynamicProperty>
						<ns1:SessionID></ns1:SessionID>
						<ns1:Action></ns1:Action>
						<ns1:Body><![CDATA[<generatortoken><security user='ClubesWS' password='Zeus1234%' />    </generatortoken>]]>
					</ns1:Body>
					  </ns1:Request>
					</ns1:Socio>";

            $result = $client->call('Socio', $params, '', '');

            echo "1a";
            print_r($client);
            exit;

            if ($client->fault) {
                echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
                print_r($result);
                echo '</pre>';
            } else {
                $err = $client->getError();
                if ($err) {
                    echo '<h2>Error</h2><pre>' . $err . '</pre>';
                }
            }

            //print_r( $result);
            //echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
            //echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];

            return $result;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;

        } // fin function

        public function get_presalida($IDClub, $IDSocio, $Documento)
    {
            $dbo = &SIMDB::get();
            if (!empty($Documento)) {
                $autorizacion_recogida = 0;
                $autorizacion_invitacion = 0;

                //BUSQUEDA INVITADOS ACCESOS
                $qryString = str_replace(".", "", $Documento);
                $qryString = str_replace(",", "", $qryString);
                $qryString = str_replace("-", "", $qryString);
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SIE.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $tipo_socio = $datos_socio["TipoSocio"];
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

                if ($datos_invitacion["Ingreso"] == "N") {
                    $accion_acceso = "ingreso";
                    $label_accion_acceso = "Ingres&oacute;";
                } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S"):
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;
            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '" . (int) $qryString . "' and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";

                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    if ($total_resultados > 0) {
                        $autorizacion_invitacion = 1;
                    }

                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                }
            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA SOCIO o Empleado si esta como Socio
            if ($total_resultados <= 0):
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" . (int) $qryString . "' or Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "') and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa    o una accion
                    //Consulto las placas de vehiculos de socios
                    $sql_invitacion = "Select * From Socio Where (Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "') and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . $IDClub . "'  and AccionPadre = ''";

                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
                $datos_invitacion["TipoInvitacion"] = "SocioClub";
                $datos_invitacion["PersonaAutoriza"] = "b";
                $datos_invitacion["FechaInicio"] = 'indefinida';
                $datos_invitacion["FechaFin"] = 'indedefinida';
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $datos_socio;
                $modulo = "Socio";
                $id_registro = $datos_invitacion["IDSocio"];

            endif;
            //FIN BUSQUEDA SOCIO

            //Busco en autorizaciones de recogida d alumnos
            $array_autorizacion_recogida = self::buscar_autorizacion_recogida($IDClub, $Documento);

            if ($total_resultados <= 0 && count($array_autorizacion_recogida) <= 0) {
                $respuesta["message"] = "No encontrado!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                    //Para fontanar en observaciones agrego el predio del socio
                    if ($IDClub == 18 || $IDClub == 35) {
                        $Observaciones = "Se dirije a " . $datos_socio["Predio"];
                    }

                    $Observaciones .= " " . $datos_invitacion["ObservacionSocio"];
                    if ($autorizacion_invitacion == 1):
                        $datos_invitacion_individual = array();
                        $datos_invitacion_individual["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                        $datos_invitacion_individual["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                        $datos_invitacion_individual["FechaInicio"] = $datos_invitacion["FechaInicio"];
                        $datos_invitacion_individual["FechaFin"] = $datos_invitacion["FechaFin"];
                        $datos_invitacion_individual["Accion"] = $datos_socio["Accion"];
                        $datos_invitacion_individual["Socio"] = $datos_invitacion["PersonaAutoriza"];
                        $datos_invitacion_individual["TipoSocio"] = $datos_socio["TipoSocio"];
                        $datos_invitacion_individual["Observaciones"] = $Observaciones;
                        $datos_invitacion_individual["Ingreso"] = $datos_invitacion["Ingreso"];
                        $datos_invitacion_individual["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                        $datos_invitacion_individual["Salida"] = $datos_invitacion["Salida"];
                        $datos_invitacion_individual["FechaSalida"] = $datos_invitacion["FechaFin"];

                        if (!empty($datos_invitado[FotoFile])) {
                            $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                        } else {
                            $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                        }

                        $datos_invitacion_individual["Foto"] = $foto;
                        $datos_invitacion_individual["NombreInvitado"] = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                        $tipodoc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                        if (empty($tipodoc)) {
                            $TipoDocumento = "Doc";
                        } else {
                            $TipoDocumento = $tipodoc;
                        }

                        $datos_invitacion_individual["TipoDocumentoInvitado"] = $TipoDocumento;
                        $datos_invitacion_individual["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];

                        //Consulto el historial de ingresos y salidas del dia
                        $response_historial_acceso = array();
                        $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                        $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                        $sql_historial = $dbo->query("Select * From LogAccesoDiario Where IDInvitacion = '" . $datos_invitacion["IDInvitacion"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                        while ($row_historial = $dbo->fetchArray($sql_historial)):
                            $dato_historial["Tipo"] = $row_historial["Tipo"];
                            $dato_historial["Salida"] = $row_historial["Salida"];
                            $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                            $dato_historial["Entrada"] = $row_historial["Entrada"];
                            $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
                            $UltimoMovimiento = $row_historial["Entrada"];
                            array_push($response_historial_acceso, $dato_historial);
                        endwhile;
                        $datos_invitacion_individual["Historial"] = $response_historial_acceso;
                    else:
                        $datos_invitacion_individual = null;
                    endif;

                    $datos_invitacion_individual["Presalida"] = $datos_invitado["Presalida"];
                    $datos_invitacion_individual["MostrarPresalida"] = ($UltimoMovimiento == "S" && $datos_invitado["Presalida"] != "S") ? "S" : "N";

                    $response["Invitacion"] = $datos_invitacion_individual;

                    $respuesta["message"] = "ok";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_estado_cuenta_ws($IDClub, $IDRegistro, $NumeroDocumento, $Accion, $Secuencia, $Concepto, $Valor, $Fecha, $Observaciones)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDRegistro) && !empty($NumeroDocumento) && !empty($Accion) && !empty($Secuencia) && !empty($Valor) && !empty($Fecha)) {
                $datos_estado = $dbo->fetchAll("SocioEstadoCuentaWS", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $NumeroDocumento . "' ", "array");
                if ($datos_estado["IDSocioEstadoCuentaWS"] > 0) {
                    $sql_inserta = "INSERT INTO SocioEstadoCuentaWS (IDClub,IDRegistro,NumeroDocumento,Accion,Secuencia,Concepto,Valor,Fecha,Observaciones,UsuarioTrCr,FechaTrCr)
														VALUES('" . $IDClub . "','" . $IDRegistro . "','" . $NumeroDocumento . "','" . $Accion . "','" . $Secuencia . "','" . $Concepto . "','" . $Valor . "','" . $Fecha . "','" . $Observaciones . "','WS',NOW())";
                    $dbo->query($sql_inserta);
                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "EC2. El registro ya existe, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "EC1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_borrar_estado_cuenta_ws($IDClub, $NumeroDocumento)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($NumeroDocumento)) {
                $datos_estado = $dbo->fetchAll("SocioEstadoCuentaWS", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $NumeroDocumento . "' ", "array");
                if ($datos_estado["IDSocioEstadoCuentaWS"] > 0) {
                    $sql_borra = "DELETE FROM  SocioEstadoCuentaWS WHERE IDClub = '" . $IDClub . "' and  NumeroDocumento = '" . $NumeroDocumento . "'";
                    $dbo->query($sql_borra);
                    $respuesta["message"] = "Registro borrardo correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "EC3. El registro no existe, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "EC1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_presalida_cualquiera($IDClub, $IDSocio, $IDInvitacion, $TipoInvitacion, $TipoSocio)
    {
            $dbo = &SIMDB::get();

            if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

                switch ($TipoInvitacion) {
                    case "InvitadoAcceso":
                        $sql_presalida = "Update  SocioInvitadoEspecial Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1";
                        $dbo->query($sql_presalida);
                        break;
                    case "Contratista":
                        $sql_presalida = "Update  SocioAutorizacion Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1";
                        $dbo->query($sql_presalida);
                        break;
                    case "Socio":
                        $sql_presalida = $dbo->query("Update  Socio Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocio = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1");
                        break;
                    case "SocioInvitado":
                    case "Invitado":
                        break;
                }
                $respuesta["message"] = "Presalida registrada con exito." . $mensaje_alerta_ingreso;
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "7. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        }

        public function get_place_to_pay_transacciones($IDClub, $IDSocio)
    {
            $dbo = &SIMDB::get();
            $response = array();

            switch ($IDClub) {
                case "23": //Arrayanes es factura
                    $TipoBusqueda = 'Factura';
                    break;
                default:
                    $TipoBusqueda = 'Servicio_Pago_Factura';

            }

            $sql = "SELECT *
				   FROM PeticionesPlacetoPay
				   WHERE IDSocio = '" . $IDSocio . "' and tipo = '" . $TipoBusqueda . "'
					 ORDER BY IDPeticionesPlacetoPay DESC
					 LIMIT 10";

            $qry = $dbo->query($sql);

            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";

                while ($row_reg = $dbo->fetchArray($qry)):

                    switch ($row_reg["estado_transaccion"]) {
                        case "OK":
                        case "APPROVED":
                            $estadoTx = "APROBADA";
                            $color = "verde";
                            break;
                        case "REJECTED":
                            $estadoTx = "RECHAZADA";
                            $color = "rojo";
                            break;
                        case "PENDING":
                            $estadoTx = "PENDIENTE";
                            $color = "amarillo";
                            break;
                        case "APPROVED_PARTIAL":
                            $estadoTx = "APROBADO PARCIAL";
                            $color = "verde";
                            break;
                        case "PARTIAL_EXPIRED":
                            $estadoTx = "PARCIALMENTE EXPIRADO";
                            $color = "rojo";
                            break;
                        case "PENDING_VALIDATION":
                            $estadoTx = "PENDIENTE DE VALIDACION";
                            $color = "rojo";
                            break;
                        case "REFUNDED":
                            $estadoTx = "REINTEGRADO";
                            $color = "azul";
                            break;
                        default:
                            $estadoTx = $estado_transaccion;
                            $color = "rojo";
                    }

                    if ($row_reg["tipo"] == "EventoRegistro") {
                        $Descripcion = utf8_decode($dbo->getFields("Evento", "Titular", "IDEvento = '" . $row_reg["IDEvento"] . "'"));
                    } elseif ($row_reg["tipo"] == "Deuda" || $row_reg["tipo"] == "Servicio_Pago_Factura") {
                    $Descripcion = "Pagos pendientes";
                } else {
                    $Descripcion = "Pagos";
                }

                $documentos_transacc = "";
                /*
                $array_documentos = json_decode( $row_reg["Documento"], true );
                if ( count( $array_documentos ) > 0 ){
                foreach ( $array_documentos as $detalle_documento ){
                $documentos_transacc.=$detalle_documento["NumeroDocumento"];
                }
                $mensajes_documentos=" Documentos: " . $documentos_transacc;
                }
                 */
                if (!empty($row_reg["Documento"])) {
                    $mensajes_documentos = " Documento: " . $row_reg["Documento"];
                }

                $transaccion["IDClub"] = $IDClub;
                $transaccion["IDSocio"] = $row_reg["IDSocio"];
                $transaccion["Referencia"] = $row_reg["referencia"] . $mensajes_documentos;
                $transaccion["Estado"] = $estadoTx;
                $transaccion["Descripcion"] = $Descripcion;
                $transaccion["Color"] = $color;
                $fecha_hora = substr($row_reg["fecha_peticion"], 0, 19);
                $fecha_hora = str_replace("T", " ", $fecha_hora);
                $transaccion["Fecha"] = $fecha_hora;
                $transaccion["Valor"] = $row_reg["valor"];
                array_push($response, $transaccion);
            endwhile;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
                $respuesta["message"] = "No tienes historial de transacciones.";
                $respuesta["success"] = false;
                $respuesta["response"] = $response;
            } //end else
            return $respuesta;
        }

        public function get_tipo_ingreso($IDClub, $IDSocio)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
            $array_tipo = explode(",", $TipoIngreso);
            if (count($array_tipo) > 0) {
                $encontrados = 1;
                $message = count($array_tipo) . " Encontrados";
                foreach ($array_tipo as $value) {
                    $tipo_ingreso["Nombre"] = utf8_encode($value);
                    array_push($response, $tipo_ingreso);
                }
            }

            $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
            $r_vehiculo = $dbo->query($sql_vehiculo);
            if ($dbo->rows($r_vehiculo) > 0) {
                $encontrados = 1;
                while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                    $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                    array_push($response, $tipo_ingreso);
                }
            }

            if ($encontrados == 1) {
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        } // fin function

        public function set_factura_consumo($IDClub, $Accion, $Cedula, $TipoSocio, $NumeroDocumentoFactura, $Total, $Iva, $Servicio, $Estado, $IDFactura, $Detalle, $TextoRecibo, $SubTotal0, $SubTotal12, $RucAsociado = "")
    {
            $dbo = &SIMDB::get();
            $datos_socio = $dbo->fetchAll("Socio", " NumeroDocumento = '" . $Cedula . "' and IDClub = '" . $IDClub . "' ", "array");
            if (!empty($datos_socio["IDSocio"])) {
                if (!empty($IDClub) && !empty($Accion) && !empty($Cedula) && !empty($TipoSocio) && !empty($Total) && !empty($Estado)) {

                    //verifico si la factura ya existe para crear o editar
                    $datos_factura = $dbo->fetchAll("FacturaConsumo", " NumeroDocumentoFactura = '" . $NumeroDocumentoFactura . "' and IDClub = '" . $IDClub . "' ", "array");

                    if (empty($datos_factura["IDFacturaConsumo"])) {
                        $sql_factura = $dbo->query("INSERT INTO FacturaConsumo (IDClub, IDSocio, IDFactura, Detalle, TextoRecibo, Accion, Cedula, NumeroDocumentoFactura, TipoSocio, Total, Iva, SubTotal0, SubTotal12, RucAsociado, Servicio, Estado, UsuarioTrCr, FechaTrCr )
																						Values ('" . $IDClub . "','" . $datos_socio["IDSocio"] . "','" . $IDFactura . "','" . $Detalle . "','" . $TextoRecibo . "', '" . $Accion . "', '" . $Cedula . "','" . $NumeroDocumentoFactura . "'
																							,'" . $TipoSocio . "','" . $Total . "','" . $Iva . "','" . $SubTotal0 . "','" . $SubTotal12 . "','" . $RucAsociado . "','" . $Servicio . "','" . $Estado . "','WS',NOW())");

                        //Envio push con notificacion de factura
                        $Mensaje = "Se genero una nueva factura " . $NumeroDocumentoFactura . "Lo invitamos a diligenciar nuestra encuesta";
                        SIMUtil::enviar_notificacion_push_general($IDClub, $datos_socio["IDSocio"], $Mensaje, 58, "151");
                        $respuesta["message"] = "Factura registrada con exito";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {

                        if ($Estado == "Pagada" || $Estado == "Anulada") {
                            $sql_factura = $dbo->query("UPDATE  FacturaConsumo SET Estado = '" . $Estado . "', UsuarioTrEd = 'WS', FechaTrEd= NOW()
																							WHERE IDFacturaConsumo = '" . $datos_factura["IDFacturaConsumo"] . "' and Estado <> 'Pagado' ");

                        } else {
                            $sql_factura = $dbo->query("UPDATE  FacturaConsumo SET  Total = '" . $Total . "', $SubTotal12='" . $SubTotal12 . "', $SubTotal0 ='" . $SubTotal0 . "', Iva = '" . $Iva . "',
																							Servicio = '" . $Servicio . "', Estado = '" . $Estado . "', UsuarioTrEd = 'WS',IDfactura='" . $IDFactura . "', Detalle = '" . $Detalle . "', FechaTrEd= NOW()
																							WHERE IDFacturaConsumo = '" . $datos_factura["IDFacturaConsumo"] . "' and Estado <> 'Pagado' ");

                        }

                        $respuesta["message"] = "Factura modificada con exito.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "FC1. Atencion faltan parametros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "FC2. Atencion socio no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_estado_factura_consumo($IDClub, $Accion, $Cedula, $NumeroDocumentoFactura)
    {
            $dbo = &SIMDB::get();

            $response = array();
            $sql = "SELECT * FROM FacturaConsumo WHERE Accion = '" . $Accion . "' and  Cedula = '" . $Cedula . "'  and NumeroDocumentoFactura = '" . $NumeroDocumentoFactura . "' LIMIT 1";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $facturaconsumo["NumeroDocumentoFactura"] = $r["NumeroDocumentoFactura"];
                    $facturaconsumo["Total"] = $r["Total"];
                    $facturaconsumo["Estado"] = $r["Estado"];
                    $facturaconsumo["NumeroAprobacion"] = $r["NumeroAprobacion"];
                    $facturaconsumo["Tarjeta"] = $r["Tarjeta"];
                    array_push($response, $facturaconsumo);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron facturas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function set_solicitar_vehiculo($IDClub, $IDSocio, $Placa, $Tercero)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa) && !empty($Tercero)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                //Verifico que la placa exista
                $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_socio)) {
                    if (!empty($id_valet)) {
                        //inserto movimiento
                        $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,FechaSolicitado,UsuarioTrCr, FechaTrCr)
											 VALUES ('" . $IDClub . "','" . $IDSocio . "','Solicitado','" . $Placa . "',NOW(),'SOCIO APP',NOW())";
                        $dbo->query($sql_log);
                        $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Solicitado', FechaSolicitado = NOW() WHERE Placa = '" . $Placa . "'");
                        $id_valet = $dbo->lastID();
                        $Mensaje = "Solicitud entrega vehiculo: " . $Placa;
                        SIMUtil::enviar_notificacion_push_entrega_vehiculo($IDClub, $Mensaje);

                        $respuesta["message"] = "Solicitado con exito";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "La placa no esta registrada!";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_recibir_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Cedula, $Nombre, $NumeroParqueadero)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario) && !empty($Placa) && !empty($NumeroParqueadero)) {

                //Verifico que la placa exista
                $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");

                //inserto movimiento
                $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,IDUsuarioRecibe,FechaRecibe,CedulaTercero,NombreTercero,NumeroParqueadero,UsuarioTrCr, FechaTrCr)
								 VALUES ('" . $IDClub . "','" . $IDSocio . "','Recibido','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $Cedula . "','" . $Nombre . "','" . $NumeroParqueadero . "','SOCIO APP',NOW())";
                $dbo->query($sql_log);

                if (!empty($id_valet)) {
                    $sql_valet = $dbo->query("UPDATE ValetParking
																				SET Estado='Recibido',IDUsuarioRecibe='" . $IDUsuario . "',FechaRecibe=NOW(),CedulaTercero='" . $Cedula . "',NombreTercero='" . $Nombre . "',NumeroParqueadero='" . $NumeroParqueadero . "',UsuarioTrCr='" . $IDUsuario . "', FechaTrCr=NOW()
																				WHERE Placa = '" . $Placa . "'");
                } else {
                    $sql_valet = "INSERT INTO ValetParking(IDClub,IDSocio,Estado,Placa,IDUsuarioRecibe,FechaRecibe,CedulaTercero,NombreTercero,NumeroParqueadero,UsuarioTrCr, FechaTrCr)
									 VALUES ('" . $IDClub . "','" . $IDSocio . "','Recibido','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $Cedula . "','" . $Nombre . "','" . $NumeroParqueadero . "','SOCIO APP',NOW())";
                    $dbo->query($sql_valet);
                }
                $respuesta["message"] = "Vehiculo recibido con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                //Verifico que la placa exista
                $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_socio)) {
                    if (!empty($id_valet)) {
                        //inserto movimiento
                        $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,IDUsuarioEntrega,FechaEntrega,UsuarioTrCr, FechaTrCr)
											 VALUES ('" . $IDClub . "','" . $IDSocio . "','Entregado','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $IDUsuario . "',NOW())";
                        $dbo->query($sql_log);
                        $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Entregado',IDUsuarioEntrega='" . $IDUsuario . "',FechaEntrega=NOW() WHERE Placa = '" . $Placa . "'");

                        $respuesta["message"] = "Entregado con exito";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "La placa no esta registrada!";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function set_cancelar_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Tercero)
    {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa) && !empty($Tercero)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                //Verifico que la placa exista
                $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_socio)) {
                    if (!empty($id_valet)) {
                        //inserto movimiento
                        $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,FechaSolicitado,UsuarioTrCr, FechaTrCr)
											 VALUES ('" . $IDClub . "','" . $IDSocio . "','Cancelado','" . $Placa . "',NOW(),'SOCIO APP',NOW())";
                        $dbo->query($sql_log);
                        $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Recibido' WHERE Placa = '" . $Placa . "'");
                        $id_valet = $dbo->lastID();
                        $Mensaje = "Cancelacion de entrega vehiculo: " . $Placa;
                        SIMUtil::enviar_notificacion_push_entrega_vehiculo($IDClub, $Mensaje);

                        $respuesta["message"] = "Cancelado con exito";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "La placa no esta registrada!";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }

            } else {
                $respuesta["message"] = "O1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_configuracion_valet($IDClub)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM ConfiguracionValet  WHERE IDClub = '" . $IDClub . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $configuracion["MensajeCancelacion"] = $r["MensajeCancelacion"];
                    $configuracion["MensajeSolicitar"] = $r["MensajeSolicitar"];
                    $configuracion["MensajeSolicitarTercero"] = $r["MensajeSolicitarTercero"];
                    $configuracion["BotonSolicitar"] = $r["BotonSolicitar"];
                    $configuracion["BotonSolicitarTercero"] = $r["BotonSolicitarTercero"];
                    $configuracion["BotonCancelacion"] = $r["BotonCancelacion"];
                    $configuracion["LabelRecibirSocio"] = $r["LabelRecibirSocio"];
                    $configuracion["BotonBuscarSocio"] = $r["BotonBuscarSocio"];
                    $configuracion["BotonRecibirSocio"] = $r["BotonRecibirSocio"];
                    $configuracion["LabelRecibirTercero"] = $r["LabelRecibirTercero"];
                    $configuracion["BotonRecibirTercero"] = $r["BotonRecibirTercero"];
                    array_push($response, $configuracion);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Sin configuracion";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function get_rutas($IDClub, $IDSocio, $IDUsuario, $Tag)
    {
            $dbo = &SIMDB::get();

            // Tag
            if (!empty($Tag)):
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_busqueda = " and " . $condiciones;
            endif;

            $response = array();

            $sql = "SELECT * FROM Ruta  WHERE IDClub = '" . $IDClub . "' and Publicar= 'S'" . $condiciones_busqueda;
            $qry = $dbo->query($sql);

            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $ruta["IDRuta"] = $r["IDRuta"];
                    $ruta["IDClub"] = $r["IDClub"];
                    $ruta["Nombre"] = $r["Nombre"];
                    $ruta["Descripcion"] = $r["Descripcion"];
                    array_push($response, $ruta);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron rutas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function get_personas_ruta($IDClub, $IDSocio, $IDUsuario, $IDRuta)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM Ruta  WHERE IDRuta = '" . $IDRuta . "' and Publicar= 'S'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $ruta["IDRuta"] = $r["IDRuta"];
                    $ruta["IDClub"] = $r["IDClub"];
                    $ruta["Nombre"] = $r["Nombre"];
                    $ruta["Descripcion"] = $r["Descripcion"];

                    //Obtengo las personas de la ruta
                    $response_personas = array();
                    $array_personas = explode("|||", $r["IDSocio"]);
                    if (count($array_personas) > 0) {
                        foreach ($array_personas as $id_persona => $datos_persona) {
                            if (!empty($datos_persona)) {
                                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_persona . "' ", "array");
                                $array_datos_persona["IDPersona"] = $datos_socio["IDSocio"];
                                $array_datos_persona["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                                $array_datos_persona["Tipo"] = "Socio";
                                array_push($response_personas, $array_datos_persona);
                            }
                        }
                    }
                    $ruta["Personas"] = $response_personas;
                    array_push($response, $ruta);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron rutas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function set_ingreso_ruta($IDClub, $IDRuta, $IDSocio, $IDUsuario, $IDPersona, $Tipo)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDRuta) && !empty($IDPersona) && !empty($Tipo)) {
                $sql_ingreso = "INSERT INTO RutaIngreso (IDRuta,IDSocio,IDUsuario,IDPersona,Tipo,UsuarioTrCr,FechaTrCr)
															VALUES('" . $IDRuta . "','" . $IDSocio . "','" . $IDUsuario . "','" . $IDPersona . "','" . $Tipo . "','App',NOW())";
                $dbo->query($sql_ingreso);
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Rt. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_configuracion_encuesta_calificacion($IDClub, $IDSocio, $IDUsuario)
    {

            $dbo = &SIMDB::get();
            $response = array();

            $sql = "SELECT * FROM ConfiguracionEncuesta2  WHERE IDClub = '" . $IDClub . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $configuracion["LabelResultado"] = $r["LabelResultado"];
                    array_push($response, $configuracion);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "Sin configuracion";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function get_vehiculo_registrado_socio($IDClub, $IDSocio)
    {

            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM ValetParking  WHERE IDSocio = '" . $IDSocio . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $valet["IDSocio"] = $r["IDSocio"];
                    $valet["IDValetParking"] = $r["IDValetParking"];
                    $valet["Placa"] = $r["Placa"];
                    $valet["Estado"] = $r["Estado"];
                    array_push($response, $valet);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function get_solicitud_entrega($IDClub, $Placa = "")
    {
            $dbo = &SIMDB::get();
            // Seccion Especifica
            if (!empty($Placa)):
                $array_condiciones[] = " Placa  = '" . $Placa . "'";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM ValetParking WHERE Estado = 'Solicitado' and IDClub = '" . $IDClub . "'" . $condiciones . " ORDER BY FechaSolicitado ASC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";

                while ($r = $dbo->fetchArray($qry)) {
                    $valet["IDClub"] = $IDClub;
                    $valet["IDValetParking"] = $r["IDValetParking"];
                    $valet["Placa"] = $r["Placa"];
                    $valet["IDSocio"] = $r["IDSocio"];
                    $valet["Socio"] = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r["IDSocio"] . "'"));
                    $valet["CedulaTercero"] = utf8_encode($r["CedulaTercero"]);
                    $valet["NombreTercero"] = utf8_encode($r["NombreTercero"]);
                    array_push($response, $valet);
                } //end while

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        } // fin function

        public function curso_buscar($IDClub, $IDSocio, $IDSede, $IDCursoTipo, $IDCursoEntrenador)
    {
            $dbo = &SIMDB::get();
            $response = array();
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if ($datos_socio["IDCursoNivel"] == 0) {
                $respuesta["message"] = "Lo sentimos no tiene un nivel asignado, debe comunicarse con la sede";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            if (!empty($IDCursoTipo)) {
                $array_condicion_curso[] = "CH.IDCursoTipo = '" . $IDCursoTipo . "'";
            }

            if (!empty($IDCursoEntrenador)) {
                $array_condicion_curso[] = "CH.IDCursoEntrenador = '" . $IDCursoEntrenador . "'";
            }

            if (count($array_condicion_curso) > 0) {
                $condicion_curso = " and " . implode(" and ", $array_condicion_curso);
            }

            $sql_entrenador = "SELECT * FROM CursoEntrenador WHERE IDClub = '" . $IDClub . "'";
            $r_entrenador = $dbo->query($sql_entrenador);
            while ($row_entrenador = $dbo->fetchArray($r_entrenador)) {
                $array_entrenador[$row_entrenador["IDCursoEntrenador"]] = $row_entrenador["Nombre"];
            }

            $sql_nivel = "SELECT * FROM CursoNivel WHERE IDClub = '" . $IDClub . "'";
            $r_nivel = $dbo->query($sql_nivel);
            while ($row_nivel = $dbo->fetchArray($r_nivel)) {
                $array_nivel[$row_nivel["IDCursoNivel"]] = $row_nivel["Nombre"];
            }

            $sql_edad = "SELECT * FROM CursoEdad WHERE IDClub = '" . $IDClub . "'";
            $r_edad = $dbo->query($sql_edad);
            while ($row_edad = $dbo->fetchArray($r_edad)) {
                $array_edad[$row_edad["IDCursoEdad"]] = $row_edad["Nombre"];
            }

            $sql_sede = "SELECT * FROM CursoSede WHERE IDClub = '" . $IDClub . "'";
            $r_sede = $dbo->query($sql_sede);
            while ($row_sede = $dbo->fetchArray($r_sede)) {
                $array_sede[$row_sede["IDCursoSede"]] = $row_sede["Nombre"];
            }

            $sql_tipo = "SELECT * FROM CursoTipo WHERE IDClub = '" . $IDClub . "'";
            $r_tipo = $dbo->query($sql_tipo);
            while ($row_tipo = $dbo->fetchArray($r_tipo)) {
                $array_tipo[$row_tipo["IDCursoTipo"]] = $row_tipo["Nombre"];
            }

            if (date("m") == 1) {
                $mes_actual = 1;
                $year_actual = date("Y");

            } else {
                $mes_actual = date("m") - 1;
                $year_actual = date("Y");
            }

            $fecha_consulta = $year_actual . "-" . $mes_actual . "-01";

            $sql = "SELECT *
						FROM CursoHorario CH, CursoCalendario CC
						WHERE CH.IDCursoTipo=CC.IDCursoTipo
						AND CH.Publicar='S'
						AND CH.IDClub = '" . $IDClub . "'
						AND CH.IDCursoSede = '" . $IDSede . "'
						AND CH.IDCursoNivel = '" . $datos_socio["IDCursoNivel"] . "' " . $condicion_curso . "
						AND CC.FechaInicio >= '" . $fecha_consulta . "'
						ORDER BY FechaInicio, HoraDesde";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $curso["IDClub"] = $r["IDClub"];
                    $curso["IDCursoHorario"] = $r["IDCursoHorario"];
                    $curso["IDCursoEntrenador"] = $r["IDCursoEntrenador"];
                    $curso["IDCursoCalendario"] = $r["IDCursoCalendario"];
                    $curso["Entrenador"] = $array_entrenador[$r["IDCursoEntrenador"]];
                    $curso["IDCursoNivel"] = $r["IDCursoNivel"];
                    $curso["Nivel"] = $array_nivel[$r["IDCursoNivel"]];
                    $curso["IDCursoEdad"] = $r["IDCursoEdad"];
                    $curso["Edad"] = $array_edad[$r["IDCursoEdad"]];
                    $curso["IDCursoSede"] = $r["IDCursoSede"];
                    $curso["Sede"] = $array_sede[$r["IDCursoSede"]];
                    $curso["IDCursoTipo"] = $r["IDCursoTipo"];
                    $curso["Dia"] = $array_tipo[$r["IDCursoTipo"]];
                    $curso["Nombre"] = $r["Nombre"];
                    $curso["Descripcion"] = $r["Descripcion"];
                    $curso["Cupo"] = $r["Cupo"];
                    $curso["ValorMes"] = $r["ValorMes"];
                    $curso["ValorTrimestre"] = $r["ValorTrimestre"];
                    $curso["HoraDesde"] = $r["HoraDesde"];
                    $curso["HoraHasta"] = $r["HoraHasta"];
                    $curso["FechaInicio"] = $r["FechaInicio"];
                    $curso["FechaFin"] = $r["FechaFin"];
                    array_push($response, $curso);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
        else {
                $respuesta["message"] = "No se encontraron cursos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;

        }

        public function set_curso_inscribir($IDClub, $IDSocio, $IDCursoHorario, $IDCursoCalendario, $IDUsuarioInscribe, $HoraDesde, $Cupos, $Valor, $referencia = "")
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDCursoHorario) && !empty($IDCursoCalendario) && !empty($HoraDesde) && !empty($Valor)) {
                //Verifico que queden Cupos
                $inscritos = self::get_curso_inscritos($IDClub, $IDCursoHorario, $IDCursoCalendario, $HoraDesde);
                if ($Cupos > 0 && $Cupos > $inscritos) {

                    if (!empty($IDUsuarioInscribe)) {
                        $Estado = "Confirmado";
                        $CreadoPor = "Starter-" . $IDUsuarioInscribe;
                    } else {
                        $Estado = "EsperaPago";
                        $CreadoPor = "Socio";
                    }

                    $sql_inscribe = "INSERT INTO CursoInscripcion (IDClub,IDSocio, IDCursoHorario, IDCursoCalendario, Referencia, HoraDesde, Valor, EstadoInscripcion, IDUsuarioInscribe,UsuarioTrCr,FechaTrCr )
															 VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDCursoHorario . "','" . $IDCursoCalendario . "','" . $referencia . "','" . $HoraDesde . "','" . $Valor . "','" . $Estado . "','" . $IDUsuarioInscribe . "','" . $CreadoPor . "',NOW())";
                    $dbo->query($sql_inscribe);
                    $id_inscripcion = $dbo->lastID();
                    $respuesta["message"] = "Inscripcion realizada-. NUMERO: " . $id_inscripcion;
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "No hay cupos disponibles!";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "C1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function get_curso_inscritos($IDClub, $IDCursoHorario, $IDCursoCalendario, $HoraDesde)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDCursoHorario) && !empty($IDCursoCalendario) && !empty($HoraDesde)) {
                $sql_inscrito = "SELECT COUNT(IDCursoInscripcion) as Total
															 FROM CursoInscripcion
															 WHERE  IDClub='" . $IDClub . "' and IDCursoHorario='" . $IDCursoHorario . "' and  IDCursoCalendario = '" . $IDCursoCalendario . "' and HoraDesde = '" . $HoraDesde . "' and EstadoInscripcion = 'Confirmado'";
                $r_inscrito = $dbo->query($sql_inscrito);
                $row_inscrito = $dbo->fetchArray($r_inscrito);
                $total_inscrito = $row_inscrito["Total"];
            } else {
                $total_inscrito = 0;
            }
            return $total_inscrito;
        }

        public function set_push_general($IDClub, $Accion, $Mensaje)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($Accion) && !empty($Mensaje)) {
                $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Accion . "' and IDClub = '" . $IDClub . "' ", "array");
                SIMUtil::enviar_notificacion_push_general($IDClub, $datos_socio["IDSocio"], $Mensaje);
                $respuesta["message"] = "Enviado con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "PUSH1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;

        }

        public function crear_pqr_ws($IDClub, $IDPqr)
    {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDPqr)) {
                $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_pqr["IDSocio"] . "' ", "array");
                $datos_tipo_pqr = $dbo->fetchAll("TipoPqr", " IDTipoPqr = '" . $datos_pqr["IDTipoPqr"] . "' ", "array");

                $URLHUB = URLHUB;
                $IDHUB = IDHUB;
                $ACCESSTOKENHUB = ACCESSTOKENHUB;
                $PASSWORDTOKENHUB = PASSWORDTOKENHUB;
                $curl = curl_init();
                $Prioridad = 1;

                if ($datos_tipo_pqr["TipoExterno"] == "REQ_FLR") {
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $URLHUB,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_FLR\",
											\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",\n \"id_equipment\": \"" . $IDEquipo . "\",
											\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"title\": \"" . $datos_pqr["Asunto"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\",\n \"priority\": \"1\"\n}\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                        ),
                    ));

                } elseif ($datos_tipo_pqr["TipoExterno"] == "REQ_HSK") {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $URLHUB,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_HSK\",
									\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",\n \"priority\": \"" . $Prioridad . "\",
									\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\"\n}\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
            } elseif ($datos_tipo_pqr["TipoExterno"] == "REQ_QTY") {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $URLHUB,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n \"id\": \"" . $IDHUB . "\",\n \"accessToken\": \"" . $ACCESSTOKENHUB . "\",\n \"passwordToken\": \"" . $PASSWORDTOKENHUB . "\",\n \"action\": \"REQ_QTY\",
									\n \"data\": {\n \"address\": \"" . $datos_socio["Predio"] . "\",\n \"owner\": \"" . $datos_socio["Predio"] . "\",\n \"code\": \"" . $IDPqr . "\",
									\n \"id_type\": \"" . $datos_tipo_pqr["IDTipoExterno"] . "\",\n \"description\": \"" . $datos_pqr["Descripcion"] . "\"\n}\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
            }

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
    }

    public function get_labels($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $datos_modulo = array();
        $datos_modulo = array();
        $sql_modulos = "SELECT IDClubLabelModulo, Nombre
										FROM ClubLabelModulo
										WHERE 1";
        $qry_modulo = $dbo->query($sql_modulos);
        if ($dbo->rows($qry_modulo) > 0) {
            while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                //$datos_modulo[ "Nombre" ] = $r_modulo[ "Nombre" ];
                //etiquetas
                $response_label = array();
                $array_label = array();

                $sql_label = "SELECT Etiqueta,Valor FROM ClubLabel WHERE IDClubLabelModulo = '" . $r_modulo["IDClubLabelModulo"] . "' and IDClub = '" . $IDClub . "'";
                $r_label = $dbo->query($sql_label);
                while ($row_label = $dbo->fetchArray($r_label)) {
                    $array_label[$row_label["Etiqueta"]] = $row_label["Valor"];
                }
                array_push($response_label, $array_label);
                $datos_modulo[$r_modulo["Nombre"]] = $response_label;
            }
        }
        array_push($response, $datos_modulo);
        $respuesta["message"] = "Encontrado";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;

    }

    public function actualizar_pqr_ws($IDClub, $IDPqr, $Hub_Code, $Status)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr)) {
            $actualizacion_hub = "CODE:" . $IDPqr . " Hub_Code " . $Hub_Code . " Status " . $Status;
            $sql_pqr = "UPDATE Pqr SET NombreColaborador = '" . $actualizacion_hub . "' WHERE IDPqr='" . $IDPqr . "'";
            $dbo->query($sql_pqr);
            $respuesta["message"] = "Actualizacion exitosa";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "PQW. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

} //end class
