<?php
class SIMServicioReserva
{

    public function get_servicios_adicionales($IDClub, $IDSocio, $IDServicio, $IDElemento, $Fecha, $IDClubAsociado, $IDTipoReserva)
    {

        $dbo = &SIMDB::get();

        $AdicionarServicio = $dbo->getFields("Servicio", "PermiteAdicionarServicios", "IDServicio = $IDServicio");

        if ($AdicionarServicio == "N") :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosepermitenlosadicionalesenesteservicio', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = "";
            return $respuesta;
        endif;

        if ($IDClub == 112) :
            require LIBDIR . "SIMWebServiceBellavista.inc.php";
            if ($IDServicio == 19939 || $IDServicio == 19940) :
                $respuesta = SIMWebServiceBellavista::get_servicios_adicionales_bellavista($IDClub, $IDSocio, $IDServicio, $Fecha);
                return $respuesta;
            elseif ($IDServicio == 2729) :
                $respuesta = SIMWebServiceBellavista::get_servicios_adicionales_bellavista_albercas($IDClub, $IDSocio, $IDServicio, $Fecha);
                return $respuesta;
            elseif ($IDServicio == 233) :
                $respuesta = SIMWebServiceBellavista::get_servicios_adicionales_bellavista_tennis($IDClub, $IDSocio, $IDServicio, $Fecha);
                return $respuesta;
            endif;
        endif;

        $response = array();
        $response_lista_producto = array();
        $message = "Adicionales";

        if (!empty($IDTipoReserva)) :
            $Busqueda = " AND ( SP.IDServicioTipoReserva = $IDTipoReserva or SP.IDServicioTipoReserva = 0 ) ";
        endif;

        //Caracteristicas
        $sql_producto_carac = "SELECT SP.IDServicioPropiedad,SP.Nombre as Categoria, SP.Tipo, SP.Obligatorio, SP.MaximoPermitido,SA.Nombre as NombreValor, SA.Valor as Precio, SA.Stock, SA.IDServicioAdicional
                                FROM ServicioAdicional SA, ServicioPropiedad SP
                                WHERE SA.IDServicioPropiedad=SP.IDServicioPropiedad AND SA.Publicar = 'S' AND SP.Publicar = 'S' AND SP.IDServicio = '" . $IDServicio . "' $Busqueda
                                ORDER BY Categoria";


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

                $categoria_carac["IDCaracteristica"] = $row_prod_carac["IDServicioPropiedad"];
                $categoria_carac["TipoCampo"] = $row_prod_carac["Tipo"];
                $categoria_carac["EtiquetaCampo"] = $row_prod_carac["Categoria"];
                $categoria_carac["Obligatorio"] = $row_prod_carac["Obligatorio"];
                $categoria_carac["CantidadMaximaSeleccion"] = $row_prod_carac["MaximoPermitido"];
            }

            $valores["IDCaracteristicaValor"] = $row_prod_carac["IDServicioAdicional"];
            $valores["Opcion"] = $row_prod_carac["NombreValor"];
            $valores["Precio"] = $row_prod_carac["Precio"];
            $valores["Stock"] = $row_prod_carac["Stock"];
            $valores["Agotado"] = "N";

            $disponible = self::validar_disponibilidad_elemento_adicional($row_prod_carac["IDServicioAdicional"], $Fecha);
            if ($disponible['response'] == 'S')
                array_push($response_valores_carac, $valores);

            $categoria_carac["Valores"] = $response_valores_carac;
            $contador_cat++;
        }
        if (count($response_valores_carac) > 0) {
            array_push($response_carac_producto, $categoria_carac);
        }
        $producto["Caracteristicas"] = $response_carac_producto;
        //FIN caracteristicas

        if (count($response_carac_producto) > 0) {
            //array_push($response, $response_carac_producto);
            $response = $response_carac_producto;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } // fin function

    public function verificar_apertura_reserva_tipo_reserva($IDTipoReserva, $IDDisponibilidad, $Fecha)
    {
        $dbo = &SIMDB::get();
        $fechahora_actual = strtotime(date("Y-m-d H:i:s"));
        $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");
        $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $IDDisponibilidad . "' ", "array");
        $datos_disponibilidad_detalle = $dbo->fetchAll("ServicioDisponibilidad", " IDDisponibilidad = '" . $IDDisponibilidad . "' Order by HoraDesde Limit 1 ", "array");

        $permite_reservar = "";
        if ((int) $datos_tipo_reserva["TiempoDespues"] > 0 && !empty($datos_tipo_reserva["MedicionTiempoDespues"])) {
            $anticipacion = $datos_disponibilidad["MedicionTiempoAnticipacion"];
            $valor_anticipacion = $datos_disponibilidad["Anticipacion"];
            switch ($anticipacion):
                case "Dias":
                    $minutos_anticipacion = (60 * 24) * $valor_anticipacion;
                    break;
                case "Horas":
                    $minutos_anticipacion = 60 * $valor_anticipacion;
                    break;
                case "Minutos":
                    $minutos_anticipacion = $valor_anticipacion;
                    break;
            endswitch;

            $hora_inicio_reserva = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($Fecha . " " . $datos_disponibilidad_detalle["HoraDesde"]));

            $medicion_tiempo_despues = $datos_tipo_reserva["MedicionTiempoDespues"];
            $valor_tiempo_despues = $datos_tipo_reserva["TiempoDespues"];
            switch ($medicion_tiempo_despues):
                case "Dias":
                    $minutos_despues = (60 * 24) * $valor_tiempo_despues;
                    break;
                case "Horas":
                    $minutos_despues = 60 * $valor_tiempo_despues;
                    break;
                case "Minutos":
                    $minutos_despues = $valor_tiempo_despues;
                    break;
            endswitch;

            $hora_inicio_despues_de = strtotime('+' . $minutos_despues . ' minute', $hora_inicio_reserva);
            $fecha_hora_actual = strtotime(date('Y-m-d H:i:s'));

            if ($fecha_hora_actual <= $hora_inicio_despues_de) {
                $permite_reservar = SIMUtil::get_traduccion('', '', 'Estetipodereservasolosepuedereservardesde', LANG) . ":" . date("Y-m-d H:i:s", $hora_inicio_despues_de);
            }
        }
        return $permite_reservar;
    }
    public function verificar_apertura_reserva_tipo_socio($datos_servicio, $Fecha, $TipoSocio, $IDDisponibilidad)
    {
        $dbo = &SIMDB::get();
        $fecha_hora_actual = strtotime(date('Y-m-d H:i:s'));
        $HoraActual = date("H:i:s");
        $FechaActual = date("Y-m-d");

        $permite_reservar = "";

        $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $IDDisponibilidad . "' ", "array");
        $datos_disponibilidad_detalle = $dbo->fetchAll("ServicioDisponibilidad", " IDDisponibilidad = '" . $IDDisponibilidad . "' Order by HoraDesde Limit 1 ", "array");

        $anticipacion = $datos_disponibilidad["MedicionTiempoAnticipacion"];
        $valor_anticipacion = $datos_disponibilidad["Anticipacion"];

        switch ($anticipacion):
            case "Dias":
                $minutos_anticipacion = (60 * 24) * $valor_anticipacion;
                break;
            case "Horas":
                $minutos_anticipacion = 60 * $valor_anticipacion;
                break;
            case "Minutos":
                $minutos_anticipacion = $valor_anticipacion;
                break;
        endswitch;

        $hora_inicio_reserva = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($Fecha . " " . $datos_disponibilidad_detalle["HoraDesde"]));

        $anticipacionReserva = $datos_servicio["MedicionTiempoTipoSocio"];
        $valor_anticipacionReserva = $datos_servicio["TiempoTipoSocio"];

        $validarAntes = $datos_servicio["ValidarAntes"];
        switch ($anticipacionReserva):
            case "Dias":
                $minutos = (60 * 24) * $valor_anticipacionReserva;
                break;
            case "Horas":
                $minutos = 60 * $valor_anticipacionReserva;
                break;
            case "Minutos":
                $minutos = $valor_anticipacionReserva;
                break;
        endswitch;

        $FechaIncio = date("Y-m-d", $hora_inicio_reserva);

        $arrayTipo = explode("|||", $datos_servicio[TipoSocioValidar]);
        foreach ($arrayTipo as $id_invitado => $datos_tipo) :
            $Tipo = explode("-", $datos_tipo);
            if ($Tipo[1] == $TipoSocio) :

                if ($validarAntes == 1) :

                    $hora_inicio = date("H:i:s", strtotime('-' . $minutos . ' minute', $hora_inicio_reserva));

                    if ($HoraActual > $hora_inicio && $FechaActual == $FechaIncio) :
                        $permite_reservar = SIMUtil::get_traduccion('', '', 'Estetipodereservasolosepodiareservarantesde', LANG) . ":" .  date("Y-m-d H:i:s", strtotime($hora_inicio));
                    endif;

                else :

                    $hora_inicio = date("H:i:s", strtotime('+' . $minutos . ' minute', $hora_inicio_reserva));
                    if ($HoraActual < $hora_inicio && $FechaActual == $FechaIncio) :
                        $permite_reservar = SIMUtil::get_traduccion('', '', 'Estetipodereservasolosepuedereservardesde', LANG) . ":" . date("Y-m-d H:i:s", strtotime($hora_inicio));
                    endif;

                endif;
                break;
            endif;
        endforeach;



        return $permite_reservar;
    }

    public function set_editar_servicios_reserva($IDClub, $IDSocio, $IDReserva, $AdicionalesSocio, $Adicionales, $IDReservaGeneralInvitado = "", $Invitados = "", $IDCaddieSocio = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDReserva)) {

            if (empty($IDReservaGeneralInvitado)) {
                //Adicionales Socio
                $borra_adicional = "DELETE FROM ReservaGeneralAdicional WHERE IDReservaGeneral = '" . $IDReserva . "'";
                $dbo->query($borra_adicional);
                $array_Adicional = $AdicionalesSocio;
                $array_Adicional = json_decode($AdicionalesSocio, true);
                if (count($array_Adicional) > 0) {
                    foreach ($array_Adicional as $detalle_carac) {
                        $SumaEspeciales = 0;
                        $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                        $ValoresCarac = $detalle_carac["Valores"];
                        $ValoresID = $detalle_carac["ValoresID"];
                        $Total = $detalle_carac["Total"];
                        $SumaEspeciales += $Total;

                        if (!empty($IDPropiedadProducto)) {
                            $array_id_carac = explode(",", $ValoresID);
                            if (count($array_id_carac) > 0) {
                                foreach ($array_id_carac as $id_carac) {
                                    $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicional (IDReservaGeneral, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $IDReserva . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                    //echo "INSERT INTO ReservaGeneralAdicional (IDReservaGeneral, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('".$IDReserva."','". $IDPropiedadProducto ."','".$id_carac."','". $ValoresID."','".$ValoresCarac."','".$Total."')";
                                }
                            }
                        }
                    }
                }
                // FIN Adicionales Socio
            }

            //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
            $datos_inv = json_decode($Invitados, true);
            $SumaEspeciales = 0;

            if (count($datos_inv) > 0) :
                foreach ($datos_inv as $detalle_inv) :

                    $IDReservaGeneralInvitado = $detalle_inv["IDReservaGeneralInvitado"];
                    $IDCaddielInvitado = $detalle_inv["IDCaddieInvitado"];

                    // INSERTAMOS EL CADDIE SI VIENE
                    if (!empty($IDCaddieInvitado)) :
                        $UpdateCaddieInivtado = "UPDATE ReservaGeneralInvitado SET IDCaddie = $IDCaddieSocio WHERE IDReservaGeneralInvitado = $IDReservaGeneralInvitado";
                        $dbo->query($UpdateCaddieInivtado);
                    endif;

                    $borra_ante = "DELETE FROM ReservaGeneralAdicionalInvitado WHERE IDReservaGeneral = '" . $IDReserva . "' and IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "'";
                    $dbo->query($borra_ante);
                    $datos_adi = $detalle_inv["Adicionales"];
                    if (count($datos_adi) > 0) :

                        foreach ($datos_adi as $detalle_carac) :
                            $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                            $ValoresCarac = $detalle_carac["Valores"];
                            $ValoresID = $detalle_carac["ValoresID"];
                            $Total = $detalle_carac["Total"];
                            $SumaEspeciales += $Total;

                            if (!empty($IDPropiedadProducto)) {
                                $array_id_carac = explode(",", $ValoresID);
                                if (count($array_id_carac) > 0) {
                                    foreach ($array_id_carac as $id_carac) {
                                        $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('" . $IDReserva . "','" . $IDReservaGeneralInvitado . "','" . $IDPropiedadProducto . "','" . $id_carac . "','" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
                                        //echo "INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('".$IDReserva."','".$IDReservaGeneralInvitado."','". $IDPropiedadProducto ."','".$id_carac."','". $ValoresID."','".$ValoresCarac."','".$Total."')";
                                    }
                                }
                            }

                        endforeach;
                    endif;
                endforeach;
            endif;
            //INSERTO LOS ADICIONALES POR INVITADO SI APLICA

            /*
                //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                $datos_respuesta=$detalle_datos["Adicionales"];
                $SumaEspeciales=0;
                if (count($datos_respuesta)>0):
                foreach($datos_respuesta as $detalle_carac):
                $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                $IDReservaGeneralInvitado = $detalle_carac["IDReservaGeneralInvitado"];
                $ValoresCarac = $detalle_carac["Valores"];
                $ValoresID = $detalle_carac["ValoresID"];
                $Total = $detalle_carac["Total"];
                $SumaEspeciales+=$Total;

                if(!empty($IDPropiedadProducto)){
                $borra_ante="DELETE FROM ReservaGeneralAdicionalInvitado WHERE IDReservaGeneral = '".$IDReserva."' and IDReservaGeneralInvitado = '".$IDReservaGeneralInvitado."'";
                $dbo->query($borra_ante);
                $array_id_carac=explode(",",$ValoresID);
                if(count($array_id_carac)>0){
                foreach($array_id_carac as $id_carac){
                $sql_datos_form = $dbo->query("INSERT INTO ReservaGeneralAdicionalInvitado (IDReservaGeneral, IDReservaGeneralInvitado, IDServicioPropiedad, IDServicioAdicional, Valor, Valores, Total) Values ('".$IDReserva."','".$IDReservaGeneralInvitado."','". $IDPropiedadProducto ."','".$id_carac."','". $ValoresID."','".$ValoresCarac."','".$Total."')");
                }
                }
                }
                endforeach;
                endif;
                //INSERTO LOS ADICIONALES POR INVITADO SI APLICA
                 */

            //  INSERTO EL CADDIE EN LA RESERAVA
            if (!empty($IDCaddieSocio)) :
                $UpdateCaddie = "UPDATE ReservaGeneral SET IDCaddie = $IDCaddieSocio WHERE IDReservaGeneral = $IDReserva";
                $dbo->query($UpdateCaddie);
            endif;

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Datosmodificadosconexito', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "RG." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', $LANG) . $IDReserva;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_ocupacion_elemento_adicional($idServicioAdicional, $fecha)
    {
        $fecha = date('Y-m-d', strtotime($fecha));

        $countAdicionalSociosQuery = "SELECT COUNT(RGA.IDReservaGeneralAdicional) AS countSevicioAdcionalSocios
		FROM ServicioAdicional SA
		INNER JOIN ReservaGeneralAdicional RGA ON SA.IDServicioAdicional=RGA.IDServicioAdicional
		INNER JOIN ReservaGeneral RG ON RGA.IDReservaGeneral=RG.IDReservaGeneral
		AND SA.IDServicioAdicional=$idServicioAdicional
		AND RG.Fecha='$fecha'
		AND (RGA.Retornado <> 'S' OR RGA.Retornado IS NULL)";

        $dbo = &SIMDB::get();
        $countAdicionalSocios = $dbo->query($countAdicionalSociosQuery);
        $countAdicionalSocios = $dbo->fetch($countAdicionalSocios)["countSevicioAdcionalSocios"];

        $countAdicionalInvitadosQuery = "SELECT COUNT(RGAI.IDReservaGeneralAdicionalInvitado) AS countSevicioAdcionalInvitados
		FROM ServicioAdicional SA
		INNER JOIN ReservaGeneralAdicionalInvitado RGAI ON SA.IDServicioAdicional=RGAI.IDServicioAdicional
		INNER JOIN ReservaGeneral RG ON RGAI.IDReservaGeneral=RG.IDReservaGeneral
		AND SA.IDServicioAdicional=$idServicioAdicional
		AND RG.Fecha='$fecha'
		AND (RGAI.Retornado <> 'S' OR RGAI.Retornado IS NULL)";

        $countAdicionalInvitados = $dbo->query($countAdicionalInvitadosQuery);
        $countAdicionalInvitados = $dbo->fetch($countAdicionalInvitados)["countSevicioAdcionalInvitados"];

        $counAdicional = $countAdicionalSocios + $countAdicionalInvitados;

        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Ocupacióndeservicioadicionalcalculado', LANG);
        $respuesta["success"] = true;
        $respuesta["response"] = $counAdicional;
        $respuesta["count-socios"] = $countAdicionalSocios;
        $respuesta["count-invitados"] = $countAdicionalInvitados;
        return $respuesta;
    }

    public function validar_disponibilidad_elemento_adicional($idServicioAdicional, $fecha = null)
    {
        if ($fecha == null) {
            $fecha = date("Y-m-d");
        }

        $dbo = &SIMDB::get();

        //Consulto el elemento adicional
        $servicioAdicionalSql = "SELECT Nombre, Stock FROM ServicioAdicional Where IDServicioAdicional = $idServicioAdicional";
        $servicioAdicionalkQuery = $dbo->query($servicioAdicionalSql);
        $servicioAdicional = $dbo->fetchArray($servicioAdicionalkQuery);

        $nombreElemento = $servicioAdicional["Nombre"];
        $stock = $servicioAdicional["Stock"];

        $disponible = "S";
        $mensaje = SIMUtil::get_traduccion('', '', 'Elelemento', LANG) . $nombreElemento . SIMUtil::get_traduccion('', '', 'tieneunidadesdisponibles', LANG);

        $ocupacion = self::get_ocupacion_elemento_adicional($idServicioAdicional, $fecha)["response"];

        $sqlEnReserva = "SELECT COUNT(IDServicioAdicionalEnReserva) AS countServicioAdicionalEnReserva
                        FROM ServicioAdicionalEnReserva
                        WHERE IDServicioAdicional=$idServicioAdicional
                        AND FechaReserva='$fecha'";

        $enreserva = $dbo->fetch($sqlEnReserva)["countServicioAdicionalEnReserva"];
        $enreserva = 0;
        $disponibilidad = (int) $stock - (int) $ocupacion - (int) $enreserva;

        if ($disponibilidad < 1) {
            $disponible = "N";
            $mensaje = SIMUtil::get_traduccion('', '', 'Elelemento', LANG) . $nombreElemento . SIMUtil::get_traduccion('', '', 'notieneunidadesdisponibles', LANG);
        }

        $respuesta["success"] = true;
        $respuesta["response"] = $disponible;
        $respuesta["cantidad"] = $disponibilidad;
        $respuesta["nombre"] = $nombreElemento;
        $respuesta["message"] = $mensaje;
        return $respuesta;
    }

    public function ElementoAdicionalEnReserva($IDElemento, $IDSocio, $Consecutivo, $Tipo, $Fecha)
    {
        $dbo = &SIMDB::get();

        $frm["IDServicioAdicional"] = $IDElemento;
        $frm["IDSocio"] = $IDSocio;
        $frm["Consecutivo"] = $Consecutivo;
        $frm["Tipo"] = $Tipo;
        $frm["FechaReserva"] = $Fecha;

        $frm = SIMUtil::varsLOG($frm);
        $id = $dbo->insert($frm, "ServicioAdicionalEnReserva", "IDServicioAdicionalEnReserva");
    }

    public function LiberarOcupacionElementoAdicional($Consecutivo, $Tipo)
    {
        $dbo = &SIMDB::get();

        $sqlDelete = "DELETE FROM ServicioAdicionalEnReserva WHERE Consecutivo='$Consecutivo' AND Tipo='$Tipo'";
        $queryDelete = $dbo->query($sqlDelete);
    }

    public function validarReservaSocioInvitadoMesAno($ingresosMes, $ingresosAno, $maxMes, $maxAno)
    {
        $valido = "S";
        if ($ingresosAno >= $maxAno && $maxAno > 0) {
            $valido = "N";
        } else if ($ingresosMes >= $maxMes && $maxMes > 0) {
            $valido = "N";
        }
        return $valido;
    }

    public function validarReservaAreaDeportiva($IDClub, $IDServicio, $NombreInvitado, $Cedula = null)
    {

        $dbo = &SIMDB::get();

        $respuesta["success"] = true;
        $respuesta["response"] = "S";
        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElInvitado', LANG) . $NombreInvitado . SIMUtil::get_traduccion('', '', 'nohasuperadoelmáximodeentradaspermitidasaláreadeportiva', LANG);

        $servicioSql = "SELECT AreaDeportiva, MaxInvitadoAno, MaxInvitadoMes
			FROM Servicio
			WHERE IDServicio=$IDServicio";

        $servicioQuery = $dbo->query($servicioSql);
        $servicio = $dbo->fetch($servicioQuery);

        if ($servicio["AreaDeportiva"] == "S") {
            $ano = date('Y');
            $mes = date('m');
            $diaFinal = date("d", mktime(0, 0, 0, $mes + 1, 0, $ano));

            $fechaIncioAno = date('Y-m-d', strtotime("$ano-01-01"));
            $fechaFinAno = date('Y-m-d', strtotime("$ano-12-31"));

            $fechaInicioMes = date('Y-m-d', strtotime("$ano-$mes-01"));
            $fechaFinMes = date('Y-m-d', strtotime("$ano-$mes-$diaFinal"));

            if ($Cedula != null && $Cedula != "") {
                $where = "AND RGI.Cedula = '$Cedula'";
            } else {
                $where = "AND RGI.Nombre = '$NombreInvitado'";
            }

            $invitadoAnoSql = "SELECT COUNT(IDReservaGeneralInvitado) AS invitadoAno
				FROM ReservaGeneralInvitado RGI
				INNER JOIN ReservaGeneral RG ON RGI.IDReservaGeneral=RG.IDReservaGeneral
				WHERE RG.Fecha BETWEEN '$fechaIncioAno' AND '$fechaFinAno'
				AND RGI.IDSocio=0
				$where";

            $invitadoAnoQuery = $dbo->query($invitadoAnoSql);
            $invitadoAno = $dbo->fetch($invitadoAnoQuery);

            $invitadoMesSql = "SELECT COUNT(IDReservaGeneralInvitado) AS invitadosMes
				FROM ReservaGeneralInvitado RGI
				INNER JOIN ReservaGeneral RG ON RGI.IDReservaGeneral=RG.IDReservaGeneral
				WHERE RG.Fecha BETWEEN '$fechaInicioMes' AND '$fechaFinMes'
				AND RGI.IDSocio=0
				$where";

            $invitadoMesQuery = $dbo->query($invitadoMesSql);
            $invitadoMes = $dbo->fetch($invitadoMesQuery);

            $ingresosMes = (int) $invitadoMes['invitadosMes'];
            $ingresosAno = (int) $invitadoAno['invitadoAno'];
            $maxMes = (int) $servicio["MaxInvitadoMes"];
            $maxAno = (int) $servicio["MaxInvitadoAno"];

            $validacion = self::validarReservaSocioInvitadoMesAno($ingresosMes, $ingresosAno, $maxMes, $maxAno);

            if ($validacion == "N") {
                $respuesta["success"] = false;
                $respuesta["response"] = "N";
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElInvitado', LANG) . " " . $NombreInvitado . SIMUtil::get_traduccion('', '', 'hasuperadoelmáximodeentradaspermitidasaláreadeportiva', LANG);
            }
        }

        //$respuesta['servicioSql'] = $servicioSql;
        //$respuesta['invitadoAnoSql'] = $invitadoAnoSql;
        //$respuesta['invitadoMesSql'] = $invitadoMesSql;

        return $respuesta;
    }

    public function get_busqueda_elementos($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $condicion_elemento = "";
        $resultado = "N";
        if (!empty($IDUsuario)) :
            $sql_elemento_usuario = "Select * From UsuarioServicioElemento Where IDUsuario = '" . $IDUsuario . "'";
            $result_elemento_usuario = $dbo->query($sql_elemento_usuario);
            while ($row_elemento_usuario = $dbo->fetchArray($result_elemento_usuario)) :
                $array_id_elemento[] = $row_elemento_usuario["IDServicioElemento"];
            endwhile;
            if (count($array_id_elemento) > 0) :
                $condicion_elemento = " and IDServicioElemento in (" . implode(",", $array_id_elemento) . ") ";
            endif;
        endif;

        if (!empty($Tag)) {
            $condicion_tag_ele = " and  SE.Nombre like '%" . $Tag . "%' ";
            $condicion_tag_aux = " and  Nombre like '%" . $Tag . "%' ";
        }

        $response = array();
        $sql = "SELECT SE.* FROM ServicioElemento SE, Servicio S WHERE SE.IDServicio = S.IDServicio and SE.Publicar = 'S' and S.IDClub = '" . $IDClub . "' and SE.IDServicio = '" . $IDServicio . "' " . $condicion_elemento . $condicion_tag_ele . " ORDER BY SE.Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $resultado = "S";
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $elemento["IDElemento"] = $r["IDServicioElemento"];
                $elemento["IDFiltro"] = $r["IDServicioElemento"];
                $elemento["IDClub"] = $IDClub;
                $elemento["IDServicio"] = $r["IDServicio"];
                $elemento["IDPadre"] = $r["IDPadre"];
                $elemento["Nombre"] = $r["Nombre"];
                $elemento["Descripcion"] = "Servicio";
                $elemento["Tipo"] = "Elemento";
                if (!empty($r["Foto"])) {
                    $FotoElemento = ELEMENTOS_ROOT . $r["Foto"];
                } else {
                    $FotoElemento = "";
                }

                $elemento["Foto"] = $FotoElemento;

                array_push($response, $elemento);
            } //ednw hile
        } //End if

        // Consulto los auxiliares boleadores
        $sql = "SELECT IDAuxiliar,Nombre FROM Auxiliar WHERE IDServicio = '" . $IDServicio . "' and Activo = 'S' " . $condicion_tag_aux . "   ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $resultado = "S";
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $elemento["IDFiltro"] = $r["IDAuxiliar"];
                $elemento["IDClub"] = $IDClub;
                $elemento["IDServicio"] = $IDServicio;
                $elemento["Nombre"] = $r["Nombre"];
                $elemento["Tipo"] = "Auxiliar";
                $elemento["Descripcion"] = "Servicio";
                array_push($response, $elemento);
            } //ednw hile
        } //End if

        if ($resultado == "S") {
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_filtro_elemento_por_texto($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado = "")
    {
        return $respuesta = self::get_busqueda_elementos($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado = "");
        //return $respuesta = SIMWebService::get_elementos($IDClub,$IDSocio,$IDServicio);

    }

    public function get_filtro_elemento_por_boton($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado = "")
    {
        return $respuesta = self::get_busqueda_elementos($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado = "");
        //return $respuesta = SIMWebService::get_elementos($IDClub,$IDSocio,$IDServicio);
    }

    public function CrearInvitacionExterno($IDClub, $IDSocio, $Documento, $Nombre, $email, $FechaInicio, $FechaFin, $IDServicio)
    {

        $dbo = &SIMDB::get();
        require_once(LIBDIR . "SIMWebServiceAccesos.inc.php");

        $Servicio = $dbo->fetchAll("Servicio", "IDServicio = '$IDServicio'", "array");

        if (empty($Servicio[Nombre])) :
            $Servicio = $dbo->fetchAll("ServicioMaestro", "IDServicioMaestro = $Servicio[IDServicioMaestro]");
        endif;

        $array_datos_invitado = [];
        $array_datos_invitado[0]["IDTipoDocumento"] = "";
        $array_datos_invitado[0]["NumeroDocumento"] = $Documento;
        $array_datos_invitado[0]["Nombre"] = $Nombre;
        $array_datos_invitado[0]["Apellido"] = "";
        $array_datos_invitado[0]["Email"] = $email;
        $array_datos_invitado[0]["TipoInvitado"] = "";
        $array_datos_invitado[0]["Placa"] = "";
        $array_datos_invitado[0]["CabezaInvitacion"] = "";
        $array_datos_invitado[0]["NumeroCarnet"] = "";
        $array_datos_invitado[0]["Predio"] = $Servicio[Nombre];

        $datosInvitados = json_encode($array_datos_invitado);



        $respuesta = SIMWebServiceAccesos::set_autorizacion_invitado($IDClub, $IDSocio, $FechaInicio, $FechaFin, $datosInvitados, "");

        return $respuesta;
    }

    public function valida_reservas_activas($IDClub, $IDServicio, $Fecha, $Hora, $IDSocio, $IDBeneficiario, $ValidaSeman = "", $ValidaFin = "", $NumeroSeman = "", $NumeroFin = "", $Invitados, $SoloInvitados = "", $ValidaGeneral = "", $NumeroGeneral = "", $minutos, $IDReserva = "")
    {

        $dbo = &SIMDB::get();

        if ($IDClub == 201) :
            date_default_timezone_set('America/Caracas');
        endif;

        $fecha_hoy_semana = date("Y-m-d");
        $hora_hoy_semana = date("H:i:s");
        $year = date('Y', strtotime($Fecha));
        $week = date('W', strtotime($Fecha));
        $dia_reserva = date("w", strtotime($Fecha));
        $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
        $fecha_lunes = $fechaInicioSemana; //Lunes
        $fecha_domingo = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
        $condicion_reserva_verif = "AND Tipo <> 'Automatica'";


        if (!empty($IDReserva))
            $cond_reserva = " and IDReservaGeneral <> '" . $IDReserva . "' ";


        if ((int) $dia_reserva >= 1 && (int) $dia_reserva <= 5) {

            if ($ValidaSeman == 'S') {
                $ReservasPermitida = $NumeroSeman;
                $fecha_inicio_valida = $fechaInicioSemana; //Lunes
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 4 day')); //Viernes
                $mensaje_reserva = SIMUtil::get_traduccion('', '', 'entresemana', LANG);
            }
        } else {

            if ($ValidaFin == "S") {

                $ReservasPermitida = $NumeroFin;
                $proximo_sabado = strtotime('next Saturday');
                $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
                $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
                $mensaje_reserva = SIMUtil::get_traduccion('', '', 'losfinesdesemana', LANG);
            }
        }

        if ($ValidaGeneral == 1) :
            $ReservasPermitida = $NumeroGeneral;
            $fecha_inicio_valida = $fechaInicioSemana; //Lunes
            $fecha_fin_valida = date('Y-m-d', strtotime($fechaInicioSemana . ' 6 day')); //Domingo
        endif;

        if ($IDClub == 79) :
            $condicionadicional = " AND (RG.Cumplida <> 'S' OR Fecha > '$fecha_hoy_semana')";
            $condicionadicional_invi = " AND (RG.Cumplida <> 'S' OR RG.Fecha > '$fecha_hoy_semana')";
        endif;

        if ($IDClub == 201 || $IDClub == 183) :
            $condicionadicional = " AND ( ((Fecha = '$fecha_hoy_semana' AND Hora > DATE_SUB('$fecha_hoy_semana $hora_hoy_semana',INTERVAL $minutos MINUTE)) OR (Fecha > '$fecha_hoy_semana')))";
            $condicionadicional_invi = " AND (((RG.Fecha = '$fecha_hoy_semana' AND RG.Hora > DATE_SUB('$fecha_hoy_semana $hora_hoy_semana',INTERVAL $minutos MINUTE)) OR (RG.Fecha > '$fecha_hoy_semana')))";
        endif;

        if (!empty($IDBeneficiario)) {
            $sql = "SELECT * From ReservaGeneral RG Where ( IDSocioBeneficiario = '" . $IDBeneficiario . "') and  Fecha >= '$fecha_hoy_semana'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' $condicionadicional " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql);
        } else {
            $sql = "SELECT * From ReservaGeneral RG Where ( IDSocio in (" . $IDSocio . ") AND IDSocioBeneficiario = 0 ) and  Fecha >= '$fecha_hoy_semana'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' $condicionadicional " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql);
        }

        $total_reservas_semana += $dbo->rows($sql_reservas_sem);

        if (!empty($IDBeneficiario)) {
            $sql = "SELECT RG.IDReservaGeneral From ReservaGeneral RG, ReservaGeneralInvitado RGI Where RG.IDReservaGeneral = RGI.IDReservaGeneral AND ( RGI.IDSocio = $IDBeneficiario) and  RG.Fecha >= '" . date("Y-m-d") . "'  and (RG.Fecha >= '" . $fecha_inicio_valida . "' and RG.Fecha <= '" . $fecha_fin_valida . "') and RG.IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' $condicionadicional_invi " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql);
        } else {
            $sql = "SELECT RG.IDReservaGeneral From ReservaGeneral RG, ReservaGeneralInvitado RGI Where RG.IDReservaGeneral = RGI.IDReservaGeneral AND ( RGI.IDSocio = $IDSocio) and  RG.Fecha >= '" . date("Y-m-d") . "'  and (RG.Fecha >= '" . $fecha_inicio_valida . "' and RG.Fecha <= '" . $fecha_fin_valida . "') and RG.IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' $condicionadicional_invi " . $condicion_reserva_verif;
            $sql_reservas_sem = $dbo->query($sql);
        }

        $total_reservas_semana += $dbo->rows($sql_reservas_sem);

        if ((int) $total_reservas_semana >= (int) $ReservasPermitida && $SoloInvitados != 1) :

            $respuesta["message"] =  "Lo sentimos, solo se permite $ReservasPermitida reserva activa..";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

        else :

            $datos_invitado = json_decode($Invitados, true);
            if (count($datos_invitado) > 0) :
                foreach ($datos_invitado as $detalle_datos) :

                    $IDSocioInvitado = $detalle_datos["IDSocio"];
                    $NombreInvitado = $detalle_datos["Nombre"];
                    $CedulaInvitado = $detalle_datos["Cedula"];


                    if ($IDSocioInvitado > 0) {
                        /* $sql = "Select IDReservaGeneral From ReservaGeneral Where ( IDSocio in ($IDSocioInvitado) OR IDSocioBeneficiario = $IDSocioInvitado ) and (Fecha= '" . date("Y-m-d") . "' and Hora >= '" . date("H:i:s") . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' $condicionadicional " . $condicion_reserva_verif;
                        $sql_reservas_sem = $dbo->query($sql);
                        $total_reservas_semana_invi = $dbo->rows($sql_reservas_sem);  */
                        $sql = "SELECT IDReservaGeneral From ReservaGeneral RG Where ( IDSocio in ($IDSocioInvitado) OR IDSocioBeneficiario = $IDSocioInvitado ) and  Fecha >= '" . date("Y-m-d") . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' $condicionadicional  " . $condicion_reserva_verif . $cond_reserva;
                        $sql_reservas_sem = $dbo->query($sql);
                        $total_reservas_semana_invi = $dbo->rows($sql_reservas_sem);

                        /* $sql = "Select RG.IDReservaGeneral From ReservaGeneral RG , ReservaGeneralInvitado RGI Where RG.IDReservaGeneral = RGI.IDReservaGeneral AND ( RGI.IDSocio in ($IDSocioInvitado) ) and (RG.Fecha= '" . date("Y-m-d") . "' and RG.Hora >= '" . date("H:i:s") . "' ) and (RG.Fecha >= '" . $fecha_inicio_valida . "' and RG.Fecha <= '" . $fecha_fin_valida . "') and RG.IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' $condicionadicional_invi " . $condicion_reserva_verif;
                        $sql_reservas_sem = $dbo->query($sql);
                        $total_reservas_semana_invi = $dbo->rows($sql_reservas_sem);      */
                        $sql2 = "SELECT RG.IDReservaGeneral From ReservaGeneral RG, ReservaGeneralInvitado RGI Where RG.IDReservaGeneral = RGI.IDReservaGeneral AND ( RGI.IDSocio in ($IDSocioInvitado) ) and  RG.Fecha >= '" . date("Y-m-d") . "'  and (RG.Fecha >= '" . $fecha_inicio_valida . "' and RG.Fecha <= '" . $fecha_fin_valida . "') and RG.IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1'   $condicionadicional_invi " . $condicion_reserva_verif . $cond_reserva;
                        $sql_reservas_sem = $dbo->query($sql2);
                        $total_reservas_semana_invi += (int) $dbo->rows($sql_reservas_sem);


                        if ((int) $total_reservas_semana_invi >= (int) $ReservasPermitida) :

                            $respuesta["message"] = "Solo se permiten: $ReservasPermitida reservas activas, el invitado $NombreInvitado tiene $total_reservas_semana_invi";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;

                            return $respuesta;

                        endif;
                    } else {
                        //valido los externos
                        if (!empty($CedulaInvitado)) {
                            $sql2 = "SELECT RG.IDReservaGeneral From ReservaGeneral RG, ReservaGeneralInvitado RGI Where RG.IDReservaGeneral = RGI.IDReservaGeneral AND ( RGI.Cedula ='" . $CedulaInvitado . "' ) and  RG.Fecha >= '" . date("Y-m-d") . "'  and (RG.Fecha >= '" . $fecha_inicio_valida . "' and RG.Fecha <= '" . $fecha_fin_valida . "') and RG.IDServicio = '" . $IDServicio . "' and RG.IDEstadoReserva = '1' $condicionadicional_invi " . $condicion_reserva_verif;
                            $sql_reservas_sem = $dbo->query($sql2);
                            $total_reservas_semana_invi += (int) $dbo->rows($sql_reservas_sem);
                            if ((int) $total_reservas_semana_invi >= (int) $ReservasPermitida) :
                                $respuesta["message"] = "Solo se permiten $ReservasPermitida reservas activas, el invitado $NombreInvitado tiene: $total_reservas_semana_invi";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                        }
                    }

                endforeach;
            endif;

            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }



    public function validar_invitados($IDClub, $IDSocio, $DocumentoInvitado, $FechaReserva)
    {
        $dbo = &SIMDB::get();

        $resultado = 0;

        $SocioInvitado = "SELECT IDSocioInvitado FROM SocioInvitado WHERE NumeroDocumento = $DocumentoInvitado AND IDSocio = $IDSocio AND IDClub = $IDClub AND FechaIngreso = '" . $FechaReserva . "' ";
        $qrySI = $dbo->query($SocioInvitado);
        $datosSI = $dbo->fetchArray($qrySI);

        $resultado = $datosSI[IDSocioInvitado];

        if ($resultado == 0) :
            $SocioInvitadoEspecial = "SELECT IDSocioInvitadoEspecial FROM SocioInvitadoEspecial SIE, Invitado I WHERE SIE.IDInvitado = I.IDInvitado AND I.NumeroDocumento = $DocumentoInvitado AND SIE.IDSocio = $IDSocio AND SIE.IDClub = $IDClub AND SIE.FechaInicio =  '" . $FechaReserva . "' ";
            $qrySIE = $dbo->query($SocioInvitadoEspecial);
            $datosSIE = $dbo->fetchArray($qrySIE);

            $resultado = $datosSIE[IDSocioInvitadoEspecial];
        endif;

        if ($resultado == 0) :
            $SocioAutorizacion = "SELECT IDSocioAutorizacion FROM SocioAutorizacion SA, Invitado I WHERE SA.IDInvitado = I.IDInvitado AND I.NumeroDocumento = $DocumentoInvitado AND SA.IDSocio = $IDSocio AND SA.IDClub = $IDClub AND SA.FechaInicio = '" . $FechaReserva . "' ";
            $qrySA = $dbo->query($SocioAutorizacion);
            $datosSA = $dbo->fetchArray($qrySA);

            $resultado = $datosSA[IDSocioAutorizacion];
        endif;

        if ($resultado > 0) :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'SocioInvitado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $resultado;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nofueposibleagregaralinvitado,debeserañadidoenelmodulodeinvitados', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function validar_edad_adicionales($IDServicioAdicional, $IDSocio, $FechaNacimientoSocio, $IDSocioBeneficiario = "", $FechaNacimientoBeneficiario = "")
    {
        $dbo = &SIMDB::get();

        $SQLAdicional = "SELECT ValidarEdad, EdadMinima, EdadMaxima, Nombre FROM ServicioAdicional WHERE IDServicioAdicional = $IDServicioAdicional";
        $QRYAdicional = $dbo->query($SQLAdicional);
        $datosAdicional = $dbo->fetchArray($QRYAdicional);

        if ($datosAdicional[ValidarEdad] == 1) {

            if (!empty($IDSocioBeneficiario)) {
                $dia_actual = date("Y-m-d");
                $edad_diff = date_diff(date_create($FechaNacimientoBeneficiario), date_create($dia_actual));
                $EdadSocio = $edad_diff->format('%y');
            } else {
                $dia_actual = date("Y-m-d");
                $edad_diff = date_diff(date_create($FechaNacimientoSocio), date_create($dia_actual));
                $EdadSocio = $edad_diff->format('%y');
            }

            if ($datosAdicional[EdadMinima] > $EdadSocio) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElSocionotienelaedadparatomareladicional', LANG) . ":" . $datosAdicional[Nombre];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } elseif ($datosAdicional[EdadMaxima] < $EdadSocio) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElSociosobrepasalaedadparatomareladicional', LANG) . ":" . $datosAdicional[Nombre];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_valida_reserva($IDClub, $Documento, $IDServicio)
    {
        $dbo = &SIMDB::get();
        require LIBDIR . "SIMWebServiceQR.inc.php";
        $sql_soc = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' and (NumeroDocumento = '" . $Documento . "' or Accion = '" . $Documento . "') LIMIT 1";
        $r_soc = $dbo->query($sql_soc);
        $row_soc = $dbo->fetchArray($r_soc);
        //para medellin debe validar los demas servicios relacionados
        if ($IDServicio == 532) {
            $IDServicio = "532,14689,7693";
        }

        $Codigo = "Servicio|" . $IDServicio . "|" . $IDClub;
        if ((int) $row_soc["IDSocio"] > 0) {
            $respuesta = SIMWebServiceQR::set_qr($IDClub, $row_soc["IDSocio"], $IDUsuario, $Codigo);
        } else {
            //valido si es un funcionario
            $sql_usu = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $Documento . "' LIMIT 1";
            $r_usu = $dbo->query($sql_usu);
            $row_usu = $dbo->fetchArray($r_usu);
            if ((int) $row_usu["IDUsuario"] > 0) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Usuariocorrecto', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {

                if ($IDClub == 20) :
                    // PARA EL CAMPESTRE MEDELLIN BUSCAMOS ESA CEDULA TIENE UNA RESERVA PARA ESE DIA.
                    $Fecha = date("Y-m-d"); //FECHA DEL DIA, RESERVAS DEL DIA.                

                    $FechaHoraActual = date("Y-m-d H:i:s");
                    $NuevaFechaHora = strtotime('-60 minute', strtotime($FechaHoraActual));
                    $Hora = date('H:i:s', $NuevaFechaHora);

                    $sqlReserva = "SELECT IDReservaGeneralInvitado FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RGI.Cedula = '$Documento' AND RGI.IDReservaGeneral=RG.IDReservaGeneral  AND RG.Fecha = '$Fecha' AND Hora >= '$Hora' AND RG.IDClub = '$IDClub' AND RG.IDServicio IN ($IDServicio)";
                    $qryReserva = $dbo->query($sqlReserva);
                    $datos = $dbo->rows($qryReserva);

                    if ($datos > 0) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Inivtadodereservacorrecto', LANG);
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Socionoexiste', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    endif;
                else :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Socionoexiste', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            }
        }

        return $respuesta;
    }

    public function valida_lista_espera($IDSocio, $Fecha, $Hora, $IDServicio)
    {
        $dbo = &SIMDB::get();
        //Consulto si este turno fue eliminado recientemente
        $sql_reserva = "SELECT IDReservaGeneral FROM ReservaGeneralEliminada WHERE IDServicio = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and Hora='" . $Hora . "' LIMIT 1";
        $r_reserva = $dbo->query($sql_reserva);
        $Total = $dbo->rows($r_reserva);
        if ($Total >= 1) {
            $sql_lista_espera = "SELECT IDListaEspera
                                 From ListaEspera
                                 Where FechaInicio >= '" . $Fecha . "' and FechaFin <= '" . $Fecha . "' and
                                       HoraInicio >= '" . $Hora . "' and HoraFin <= '" . $Hora . "' and
                                       IDServicio = '" . $IDServicio . "' and Tipo = 'Reserva' and IDSocio = '" . $IDSocio . "' and
                                       FechaTrCr >= DATE_SUB(NOW(),INTERVAL 60 MINUTE)
                                 LIMIT 1";
            $r_lista_espera = $dbo->query($sql_lista_espera);
            $TotalLista = $dbo->rows($r_lista_espera);
            if ($TotalLista <= 0) {
                return "nolista";
            } else {
                return $sql_lista_espera;
            }
        }
    }

    /* FUNCIÓN PARA VALIDAR LOS INVITADOS NO SOCIOS DE URUGUAY SOLO ES PERMITIDO UNA VEZ EN EL MES EN EL MISMO SERVICIO */
    public function valida_invitados_uruguay($IDSocio, $Cedula, $Nombre, $Fecha)
    {
        $dbo = &SIMDB::get();

        $MesReserva = date('m', strtotime($Fecha));

        $servicios = array('23059', '22972', '23009', '23013');

        // BUSCA LAS INVITACIONES DE ESA MISMA CEDULA
        $Busqueda = "SELECT IDReservaGeneral FROM ReservaGeneralInvitado WHERE Cedula = '$Cedula'";

        $qry = $dbo->query($Busqueda);
        while ($row = $dbo->fetchArray($qry)) :
            //BUSCAMOS PARA CADA RESERVA EN LA QUE FUE INVITADO
            $reservas = "SELECT IDServicio, Fecha FROM ReservaGeneral WHERE  IDReservaGeneral = '$row[IDReservaGeneral]'";
            $qry2 = $dbo->query($reservas);
            $Datos = $dbo->fetchArray($qry2);

            $FechaReservaYaTomada = $Datos[Fecha];
            $ServicioReservaYaTomada = $Datos[IDServicio];

            $MesYaTomada = date('m', strtotime($FechaReservaYaTomada));
            // VALIDO SI EL MES DE LA FECHA ES LE MISMO QUE EL QUE SE ESTA TOMANDO Y SI EL SERVICIO ES IGUAL PARA NO PERMITIR LA INVITACIÓN
            if ($MesYaTomada == $MesReserva && in_array($ServicioReservaYaTomada, $servicios)) :

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElInvitado', LANG) . $Nombre . SIMUtil::get_traduccion('', '', 'yafueinvitadoestemesporunsocioenesteservicioynopuedeserinvitadodenuevo', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;

                return $respuesta;

            endif;
        endwhile;

        $respuesta["message"] = "";
        $respuesta["success"] = true;
        $respuesta["response"] = null;

        return $respuesta;
    }

    /* FUNCION PARA VALIDAR SI UN SOCIO HA INVITADO A UN NO SOCIO EN EL MISMO MES PARA ESE SERVICIO */
    public function valida_invitaciones_socio_uruguay($IDSocio, $Fecha)
    {
        $dbo = &SIMDB::get();
        $MesReserva = date('m', strtotime($Fecha)); //MES DE LA RESERVA
        $servicios = '23059,22972,23009,23013';

        // BUSCA LAS RESERVAS DEL SOCIO EN ESE MISMO SERVICIO EN EL MES
        $Busqueda = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDSocio = '$IDSocio' AND IDServicio IN ($servicios) AND MONTH(Fecha) = '$MesReserva'";

        /*  if($IDSocio == 393819):
        echo $Busqueda;
        exit;
        endif; */

        $qry = $dbo->query($Busqueda);
        while ($row = $dbo->fetchArray($qry)) :
            //BUSCAMOS PARA CADA RESERVA SI HAY INVITADOS NO SOCIO
            $reservas = "SELECT IDReservaGeneralInvitado, Cedula FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '$row[IDReservaGeneral]'";
            $qry2 = $dbo->query($reservas);
            while ($Datos = $dbo->fetchArray($qry2)) :
                // VALIDO SI EL MES DE LA FECHA ES LE MISMO QUE EL QUE SE ESTA TOMANDO Y SI EL SERVICIO ES IGUAL PARA NO PERMITIR LA INVITACIÓN
                if ($Datos[Cedula] > 0) :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'NopuedeinvitaraunexternodelclubelmismomesalosserviciosdeTenis,GolfyPiscinaExterior', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endwhile;
        endwhile;

        $respuesta["message"] = "";
        $respuesta["success"] = true;
        $respuesta["response"] = null;

        return $respuesta;
    }

    /* FUNCION PARA EL CANTEGIL URUGUAY QUE SOLO SE PUEDEN 2 VECES PARA UN MIMSO INVITADO EXTERNO */
    public function valida_invitados_cantegril_uruguay($IDSocio, $Cedula, $Nombre, $Fecha)
    {
        $dbo = &SIMDB::get();

        $MesReserva = date('m', strtotime($Fecha));
        $CantidadPermitidaMes = 2;

        $servicios = array('36930');

        // BUSCA LAS INVITACIONES DE ESA MISMA CEDULA
        $Busqueda = "SELECT IDReservaGeneral FROM ReservaGeneralInvitado WHERE Cedula = '$Cedula'";

        $qry = $dbo->query($Busqueda);
        $Cantidad = $dbo->rows($qry);
        while ($row = $dbo->fetchArray($qry)) :
            //BUSCAMOS PARA CADA RESERVA EN LA QUE FUE INVITADO
            $reservas = "SELECT IDServicio, Fecha FROM ReservaGeneral WHERE  IDReservaGeneral = '$row[IDReservaGeneral]'";
            $qry2 = $dbo->query($reservas);
            $Datos = $dbo->fetchArray($qry2);

            $FechaReservaYaTomada = $Datos[Fecha];
            $ServicioReservaYaTomada = $Datos[IDServicio];


            $MesYaTomada = date('m', strtotime($FechaReservaYaTomada));
            // VALIDO SI EL MES DE LA FECHA ES LE MISMO QUE EL QUE SE ESTA TOMANDO Y SI EL SERVICIO ES IGUAL PARA NO PERMITIR LA INVITACIÓN
            if ($MesYaTomada == $MesReserva && in_array($ServicioReservaYaTomada, $servicios) && $Cantidad > $CantidadPermitidaMes) :

                $respuesta["message"] = "EL invitado ya fue invitado mas de dos veces al mismo servicio y no puede ser invitado de nuevo";
                $respuesta["success"] = false;
                $respuesta["response"] = null;

                return $respuesta;

            endif;
        endwhile;

        $respuesta["message"] = "";
        $respuesta["success"] = true;
        $respuesta["response"] = null;

        return $respuesta;
    }

    public function get_reservas_fecha($IDClub, $FechaInicio, $FechaFin, $IDServicio)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($FechaInicio) && !empty($FechaFin)) {
            $sql = "SELECT * FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva = 1  and Fecha >= '" . $FechaInicio . "' and Fecha <= '" . $FechaFin . "' and IDServicio in ($IDServicio)";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($row_reserva = $dbo->fetchArray($qry)) {

                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_reserva["IDSocio"] . "' ", "array");
                    $NombreElemento = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                    if (!empty($NombreElemento)) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");
                        // Verifico si es una reserva asociada para no mostrarla en el resultado
                        $reserva["IDClub"] = $IDClub;
                        $reserva["IDSocio"] = $row_reserva["IDSocio"];
                        $reserva["Socio"] = utf8_encode($datos_socio["Nombre"] . "'" . " " . $datos_socio["Apellido"]);
                        $reserva["NumeroDocumento"] = utf8_encode($datos_socio["NumeroDocumento"]);
                        $reserva["Accion"] = utf8_encode($datos_socio["Accion"]);
                        $reserva["CodFamilia"] = utf8_encode($datos_socio["AccionPadre"]);
                        $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                        $reserva["IDServicio"] = $row_reserva["IDServicio"];
                        $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        }

                        if ((int) $row_reserva["IDServicioTipoReserva"] > 0 && $IDClub != "9") :
                            $nombre_servicio_personalizado .= " (" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row_reserva["IDServicioTipoReserva"] . "'") . ")";
                        endif;

                        $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                        $reserva["NombreServicioPersonalizado"] = $nombre_servicio_personalizado;

                        if ((int) $row_reserva["IDSocioBeneficiario"] > 0) {
                            $socio_benef = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                            $otros_datos_reserva = "(" . $socio_benef . ")";
                        } else {
                            $otros_datos_reserva = "(" . $reserva["Socio"] . ")";
                        }

                        $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                        $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                        $reserva["NombreElemento"] = $NombreElemento;
                        $reserva["Fecha"] = $row_reserva["Fecha"];
                        $reserva["FechaCreacion"] = $row_reserva["FechaTrCr"];
                        $reserva["Tee"] = $row_reserva["Tee"];
                        $reserva["CantidadInvitadoSalon"] = $row_reserva["CantidadInvitadoSalon"];
                        $reserva["PagadaOnline"] = $row_reserva["Pagado"];
                        $reserva["FechaTransaccion"] = $row_reserva["FechaTransaccion"];
                        $reserva["MensajeTransaccion"] = "Mensaje transacción: " . $row_reserva["MensajeTransaccion"];

                        $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                        $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                        $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                        $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                        $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                        $response_auxiliar_reserva = array();
                        if (!empty($row_reserva["IDAuxiliar"])) :
                            $Array_Auxiliar = explode(",", $row_reserva["IDAuxiliar"]);
                            if (count($Array_Auxiliar) > 0) :
                                foreach ($Array_Auxiliar as $id_auxiliar) :
                                    if (!empty($id_auxiliar)) :
                                        $array_datos_auxiliar["IDAuxiliar"] = $id_auxiliar;
                                        $array_datos_auxiliar["Nombre"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $id_auxiliar . "'");
                                        $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $id_auxiliar . "'");
                                        $array_datos_auxiliar["Tipo"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                                        array_push($response_auxiliar_reserva, $array_datos_auxiliar);
                                    endif;
                                endforeach;
                            endif;

                            $reserva["ListaAuxiliar"] = $response_auxiliar_reserva;

                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["Auxiliar"] = utf8_encode($dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'"));
                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                        else :
                            unset($reserva['IDAuxiliar']);
                            unset($reserva['Auxiliar']);
                            unset($reserva['TipoAuxiliar']);
                            $reserva["ListaAuxiliar"] = array();
                        endif;

                        if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                            $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                            $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                        else :
                            unset($reserva['IDTipoModalidad']);
                            unset($reserva['Modalidad']);
                        endif;

                        if (strlen($row_reserva["Hora"]) != 8) :
                            $row_reserva["Hora"] .= ":00";
                        endif;

                        $reserva["Hora"] = $row_reserva["Hora"];

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        if ($row_reserva["IDDisponibilidad"] <= 0) :
                            $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                        else :
                            $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                        endif;

                        $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)) :
                            $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)) :
                            $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoExterno"] = "";
                        endif;

                        if ($row_reserva["IDInvitadoBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = utf8_encode($dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'"));
                        else :
                            $reserva["Beneficiario"] = "";
                        endif;
                        if ($row_reserva["IDSocioBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = strtoupper(utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'")));
                        else :
                            $reserva["Beneficiario"] = "";
                        endif;

                        array_push($response_invitados_reserva, $invitado_reserva);

                        $reserva["Invitados"] = $response_invitados_reserva;

                        //Reservas asociadas
                        $response_reserva_asociada = array();
                        $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                        foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                            array_push($response_reserva_asociada, $datos_reserva);
                        endforeach;
                        $reserva["ReservaAsociada"] = $response_reserva_asociada;

                        array_push($response, $reserva);
                    }
                }

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', $LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        }

        return $respuesta;
    }
    // PARA VALIDAR SI LA CEDULA DE UN INVITADO EXTERNO TIENE CARACTERES ESPECIALES
    public function valida_cedula_invitado_externo($Cedula, $NombreInvitado)
    {
        $ValidaDocumento = str_replace(SIMResources::$abecedario, 1,  $Cedula);

        if (!ctype_space($ValidaDocumento)) :

            if (is_numeric($ValidaDocumento)) :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'CedulaValida', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'CeduladelInvitado', LANG) . $NombreInvitado . SIMUtil::get_traduccion('', '', 'noesvalida', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'CeduladelInvitado', LANG) . $NombreInvitado . SIMUtil::get_traduccion('', '', 'noesvalida', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function calcular_precio_elemento($Fecha, $Hora, $IDServicio, $IDElemento)
    {
        $dbo = SIMDB::get();
        $DiaReserva = date("w", strtotime($Fecha));

        $SQLValor = "SELECT Valor FROM ValorReservaElemento WHERE IDServicio = $IDServicio AND IDServicioElemento LIKE '%$IDElemento%' AND Dias LIKE '%$DiaReserva%' AND HoraInicio <= '$Hora' AND HoraFin >= '$Hora' AND Activo = 1";
        $QRYValor = $dbo->query($SQLValor);
        $Datos = $dbo->fetchArray($QRYValor);

        return $Datos[Valor];
    }

    public function valor_reserva_chicureo_golf($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio)
    {
        $dbo = SIMDB::get();
        $Valor = 0;
        $dia_actual = date("Y-m-d");
        $DiaReserva  = date("w", $Fecha);

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        if ($datos_socio[TipoSocio] == 'Externo') :
            $Valor += 28500;
        else :

            $FechaNacimientoSocio = $datos_socio[FechaNacimiento];
            $edad_Socio = date_diff(date_create($FechaNacimientoSocio), date_create($dia_actual));

            if ($edad_Socio >= 18 && $edad_socio <= 23) :
                $Valor += 500;
            elseif ($edad_socio > 23) :
                $Valor += 3500;
            endif;

            $DatosInvitados = json_decode($Invitados, true);
            if (count($DatosInvitados) > 0) :
                foreach ($DatosInvitados as $id => $Invitado) :

                    /* EDAD PARA CUANDO SE HAGA EL PROCESO CON TICKET DE DESCUENTO */
                    $FechaNacimientoInvitado = $Invitado[FechaNacimiento];
                    $Ticket = $Invitado[IDTicket];
                    $edad_Invitado = date_diff(date_create($FechaNacimientoInvitado), date_create($dia_actual));


                    if ($DiaReserva == 1 || $DiaReserva == 2 || $DiaReserva == 3 || $DiaReserva == 4) :
                        if ($edad_Invitado >= 18 && $edad_Invitado <= 24 && $Ticket > 0) :
                            $Valor += 3500;
                        elseif ($edad_Invitado > 24 && $Ticket > 0) :
                            $Valor += 18500;
                        else :
                            $Valor += 28500;
                        endif;
                    else :
                        if ($DiaReserva == 5) :
                            if ($Hora >= '07:30:00' && $Hora <= '13:00:00') :
                                if ($edad_Invitado >= 18 && $edad_Invitado <= 24 && $Ticket > 0) :
                                    $Valor += 3500;
                                elseif ($edad_Invitado > 24 && $Ticket > 0) :
                                    $Valor += 18500;
                                else :
                                    $Valor += 28500;
                                endif;
                            else :
                                if ($edad_Invitado >= 18 && $edad_Invitado <= 24 && $Ticket > 0) :
                                    $Valor += 3500;
                                elseif ($edad_Invitado > 24 && $Ticket > 0) :
                                    $Valor += 23500;
                                else :
                                    $Valor += 33500;
                                endif;
                            endif;
                        else :
                            if ($Hora >= '07:30:00' && $Hora <= '13:00:00') :
                                if ($edad_Invitado >= 18 && $edad_Invitado <= 24 && $Ticket > 0) :
                                    $Valor += 3500;
                                elseif ($edad_Invitado > 24 && $Ticket > 0) :
                                    $Valor += 28500;
                                else :
                                    $Valor += 43500;
                                endif;
                            else :
                                if ($edad_Invitado >= 18 && $edad_Invitado <= 24 && $Ticket > 0) :
                                    $Valor += 3500;
                                elseif ($edad_Invitado > 24 && $Ticket > 0) :
                                    $Valor += 18500;
                                else :
                                    $Valor += 28500;
                                endif;
                            endif;
                        endif;
                    endif;

                endforeach;
            endif;
        endif;



        return $Valor;
    }

    public function valor_reserva_san_luis($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva, $IDElemento, $TurnosSeparar)
    {
        $dbo = SIMDB::get();
        $Valor = 0;
        $dia_actual = date("Y-m-d");
        $DiaReserva  = date("w", $Fecha);

        /*
        //tenis pista 1        
        if($IDServicio==45976){
            if(  ($DiaReserva == 1 || $DiaReserva == 2 || $DiaReserva == 3 || $DiaReserva == 4 || $DiaReserva == 5) && ($Hora == '07:00:00' || $Hora <= '10:00:00' || ($Hora >= '16:00:00' || $Hora <= '21:00:00') ) ):
                $Valor = 15;
            else:    
                $Valor = 20;
            endif;   
        }

        //tenis otras canchas
        if($IDServicio==45974){
            if(  ($DiaReserva == 1 || $DiaReserva == 2 || $DiaReserva == 3 || $DiaReserva == 4 || $DiaReserva == 5) && ($Hora >= '14:00:00' || $Hora <= '19:00:00' ) ):
                $Valor = 15;
            else:   
                $Valor = 20;
            endif;   
        }
        */

        if (($IDServicio == 45975 && $IDElemento == 18896) || ($IDServicio == 45974 && $IDElemento == 17731)) {
            $Valor = 25 * $TurnosSeparar;
        }

        return $Valor;
    }

    public function valor_serena($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio)
    {
        $Valor = 0;
        $DiaReserva  = date("w", $Fecha);
        //tenis
        if ($IDServicio == 13698) {
            if (($DiaReserva == 1 || $DiaReserva == 2 || $DiaReserva == 3 || $DiaReserva == 4 || $DiaReserva == 5) && $Hora >= '06:00:00' && $Hora <= '16:00:00') {
                $Valor = 40000;
            } elseif (($DiaReserva == 1 || $DiaReserva == 2 || $DiaReserva == 3 || $DiaReserva == 4 || $DiaReserva == 5) && $Hora >= '08:00:00' && $Hora <= '21:00:00') {
                $Valor = 45000;
            } elseif (($DiaReserva == 6) && $Hora >= '06:00:00' && $Hora <= '07:00:00') {
                $Valor = 40000;
            } elseif (($DiaReserva == 6) && $Hora >= '12:00:00' && $Hora <= '20:00:00') {
                $Valor = 45000;
            } elseif (($DiaReserva == 0) && $Hora >= '06:00:00' && $Hora <= '18:00:00') {
                $Valor = 40000;
            }
        }

        //canchas multiple
        if ($IDServicio == 13697) {
            if (($DiaReserva == 1 || $DiaReserva == 3 || $DiaReserva == 5) && $Hora >= '06:00:00' && $Hora <= '16:00:00') {
                $Valor = 40000;
            } elseif (($DiaReserva == 2 || $DiaReserva == 4) && $Hora >= '06:00:00' && $Hora <= '18:00:00') {
                $Valor = 40000;
            } elseif (($DiaReserva == 1 || $DiaReserva == 3 || $DiaReserva == 5) && $Hora >= '20:00:00' && $Hora <= '21:00:00') {
                $Valor = 45000;
            } elseif (($DiaReserva == 2 || $DiaReserva == 4) && $Hora >= '19:00:00' && $Hora <= '21:00:00') {
                $Valor = 45000;
            } elseif (($DiaReserva == 6) && $Hora >= '06:00:00' && $Hora <= '19:00:00') {
                $Valor = 40000;
            } elseif (($DiaReserva == 6 || $DiaReserva == 0) && $Hora >= '19:00:00' && $Hora <= '21:00:00') {
                $Valor = 45000;
            }
        }

        return $Valor;
    }

    public function valor_reserva_chicureo_tenis($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva)
    {
        $dbo = SIMDB::get();
        $Valor = 0;
        $dia_actual = date("Y-m-d");
        $MesReserva  = date("m", $Fecha);

        $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$Fecha' AND IDPais = '9'");

        if ($MesReserva == '09' || $MesReserva == '10' || $MesReserva == '11' || $MesReserva == '12' || $MesReserva == '01' || $MesReserva == '02' || $MesReserva == '03' || $MesReserva == '04' || $MesReserva == '05') :
            $Temporada = 'Verano';
        else :
            $Temporada = 'Invierno';
        endif;

        if ($IDTipoReserva == 5464) :

            if ($Hora >= '20:00:00' && $Hora <= '22:00:00' && $Temporada == 'Verano') :
                $Valor += 3000;
            endif;

            if ($Hora >= '18:00:00' && $Hora <= '22:00:00' && $Temporada == 'Invierno') :
                $Valor += 3000;
            endif;


            $DatosInvitados = json_decode($Invitados, true);
            if (count($DatosInvitados) > 0) :
                foreach ($DatosInvitados as $id => $Invitado) :
                    $Ticket = $Invitado[IDTicket];
                    if ($DiaReserva >= 1 && $DiaReserva <= 5 && empty($IDFestivo)) :
                        if ($Ticket > 0) :
                            $Valor += 2000;
                        else :
                            $Valor += 4000;
                        endif;
                    else :
                        if ($Ticket > 0) :
                            $Valor += 3000;
                        else :
                            $Valor += 6000;
                        endif;
                    endif;
                endforeach;
            endif;


        elseif ($IDTipoReserva == 5465) :
            if ($Hora >= '20:00:00' && $Hora <= '22:00:00' && $Temporada == 'Verano') :
                $Valor += 4500;
            endif;

            if ($Hora >= '18:00:00' && $Hora <= '22:00:00' && $Temporada == 'Invierno') :
                $Valor += 4500;
            endif;


            $DatosInvitados = json_decode($Invitados, true);
            if (count($DatosInvitados) > 0) :
                foreach ($DatosInvitados as $id => $Invitado) :
                    $Ticket = $Invitado[IDTicket];
                    if ($DiaReserva >= 1 && $DiaReserva <= 5 && empty($IDFestivo)) :
                        if ($Ticket > 0) :
                            $Valor += 3000;
                        else :
                            $Valor += 6000;
                        endif;
                    else :
                        if ($Ticket > 0) :
                            $Valor += 4500;
                        else :
                            $Valor += 9000;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;

        return $Valor;
    }

    public function valor_reserva_chicureo_padel($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio, $IDTipoReserva)
    {
        $dbo = SIMDB::get();
        $Valor = 0;
        $dia_actual = date("Y-m-d");
        $MesReserva  = date("m", $Fecha);

        $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$Fecha' AND IDPais = '9'");

        if ($MesReserva == '09' || $MesReserva == '10' || $MesReserva == '11' || $MesReserva == '12' || $MesReserva == '01' || $MesReserva == '02' || $MesReserva == '03' || $MesReserva == '04' || $MesReserva == '05') :
            $Temporada = 'Verano';
        else :
            $Temporada = 'Invierno';
        endif;

        if ($IDTipoReserva == 5434) :

            if ($Hora >= '20:00:00' && $Hora <= '22:00:00' && $Temporada == 'Verano') :
                $Valor += 3000;
            endif;

            if ($Hora >= '18:00:00' && $Hora <= '22:00:00' && $Temporada == 'Invierno') :
                $Valor += 3000;
            endif;


            $DatosInvitados = json_decode($Invitados, true);
            if (count($DatosInvitados) > 0) :
                foreach ($DatosInvitados as $id => $Invitado) :
                    $Ticket = $Invitado[IDTicket];
                    if ($DiaReserva >= 1 && $DiaReserva <= 5 && empty($IDFestivo)) :
                        if ($Ticket > 0) :
                            $Valor += 2000;
                        else :
                            $Valor += 4000;
                        endif;
                    else :
                        if ($Ticket > 0) :
                            $Valor += 3000;
                        else :
                            $Valor += 6000;
                        endif;
                    endif;
                endforeach;
            endif;


        elseif ($IDTipoReserva == 5435) :
            if ($Hora >= '20:00:00' && $Hora <= '22:00:00' && $Temporada == 'Verano') :
                $Valor += 4500;
            endif;

            if ($Hora >= '18:00:00' && $Hora <= '22:00:00' && $Temporada == 'Invierno') :
                $Valor += 4500;
            endif;


            $DatosInvitados = json_decode($Invitados, true);
            if (count($DatosInvitados) > 0) :
                foreach ($DatosInvitados as $id => $Invitado) :
                    $Ticket = $Invitado[IDTicket];
                    if ($DiaReserva >= 1 && $DiaReserva <= 5 && empty($IDFestivo)) :
                        if ($Ticket > 0) :
                            $Valor += 3000;
                        else :
                            $Valor += 6000;
                        endif;
                    else :
                        if ($Ticket > 0) :
                            $Valor += 4500;
                        else :
                            $Valor += 9000;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;

        return $Valor;
    }


    public function valor_reserva_chicureo_arriendo_carros($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio)
    {
        $dbo = SIMDB::get();
        $Valor = 0;


        // ADICIONALES SOCIO
        $ValorArriendoSocio = 20000;
        $ValorArriendoInvitado = 25000;
        $ValorArriendoExterno = 25000;

        $Adicionales = json_decode($AdicionalesSocio, true);
        /* if($TipoBeneficiario == "Invitado"):
            if(count($Adicionales) > 0):
                foreach($Adicionales as $id => $Adicional):      
                    if($Adicional[Valores] == "Externo"):
                        $Valor += $Adicional[Total];
                    else:
                        $Valor += $ValorArriendoExterno;
                    endif;                                      
                endforeach;
            endif;
        else: */
        if (count($Adicionales) > 0) :
            foreach ($Adicionales as $id => $Adicional) :
                /* if($Adicional[Valores] == "Socio"): */
                $Valor += $Adicional[Total];
            /* else:
                        $Valor += $ValorArriendoSocio;
                    endif; */
            endforeach;
        endif;
        /* endif;  */

        /* $DatosInvitados = json_decode($Invitados, true);
        if(count($DatosInvitados) > 0):
            foreach($DatosInvitados as $id => $Invitado):
                $AdicionalesInvitado = json_decode($Invitado[Adicionales], true);
                if(count($AdicionalesInvitado) > 0):
                    foreach($AdicionalesInvitado as $id => $Adicional):  
                        if($Adicional[Valores] == "Invitado"):
                            $Valor += $Adicional[Total];
                        else:
                            $Valor += $ValorArriendoInvitado;
                        endif;    
                    endforeach;
                endif;
                
            endforeach;                
        endif; */

        return $Valor;
    }

    public function valor_reserva_chicureo_canosto_pelotas($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio)
    {
        $dbo = SIMDB::get();
        $Valor = 0;

        // PRIMERO EL PRECIO DE LOS ADICIONALES
        $Adicionales = json_decode($AdicionalesSocio, true);

        if (count($Adicionales) > 0) :
            foreach ($Adicionales as $id => $Adicional) :
                $Valor += $Adicional[Total];
            endforeach;
        endif;

        $DatosInvitados = json_decode($Invitados, true);
        if (count($DatosInvitados) > 0) :
            foreach ($DatosInvitados as $id => $Invitado) :
                $AdicionalesInvitado = json_decode($Invitado[Adicionales], true);
                if (count($AdicionalesInvitado) > 0) :
                    foreach ($AdicionalesInvitado as $id => $Adicional) :
                        $Valor += $Adicional[Total];
                    endforeach;
                endif;

            endforeach;
        endif;

        return $Valor;
    }

    public function valor_reserva_chicureo_squash($IDSocio, $IDBeneficiario, $TipoBeneficiario, $Invitados, $AdicionalesSocio, $Fecha, $Hora, $IDServicio)
    {
        $dbo = SIMDB::get();
        $Valor = 0;
        $dia_actual = date("Y-m-d");
        $dia = date("w");
        $MesReserva  = date("m", $Fecha);

        $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$Fecha' AND IDPais = '9'");

        $DatosInvitados = json_decode($Invitados, true);
        if (count($DatosInvitados) > 0) :
            foreach ($DatosInvitados as $id => $Invitado) :
                $Ticket = $Invitado[IDTicket];
                if ($DiaReserva >= 1 && $DiaReserva <= 5 && empty($IDFestivo)) :
                    if ($Ticket > 0) :
                        $Valor += 2000;
                    else :
                        $Valor += 4000;
                    endif;
                else :
                    if ($Ticket > 0) :
                        $Valor += 3000;
                    else :
                        $Valor += 6000;
                    endif;
                endif;
            endforeach;
        endif;

        return $Valor;
    }

    public function valor_reserva_santa_monica($IDSocio, $Fecha, $Hora, $IDServicio, $datos_socio, $IDReserva)
    {
        $dbo = SIMDB::get();
        $Cobrar = 'S';
        $NumeroReservasGratis = 5;
        if ($datos_socio["TipoSocio"] == "Profesores" && $IDServicio == 44481) {

            if ($IDSocio == 706758 && $Hora < "11:00:00") {
                $NumeroReservasGratis = 6;
                $Cobrar = 'N';
            } elseif ($IDSocio == 706759 && $Hora < "11:45:00") {
                $NumeroReservasGratis = 6;
                $Cobrar = 'N';
            } elseif ($Hora < "10:45:00") {
                $Cobrar = 'N';
            }

            /*
            //Verifico cuantas reservas tienen en el dia
            $sql_reserva="SELECT count(IDReservaGeneral) as TotalReservas FROM ReservaGeneral Where IDSocio = '".$IDSocio."' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."' ";
            $r_reserva=$dbo->query($sql_reserva);
            $row_reserva=$dbo->fetchArray($r_reserva);
            if((int)$row_reserva["TotalReservas"]<=$NumeroReservasGratis){
                $Cobrar='N';
                //Actualizo la reserva como pagada para que no se elimine
                $actulizaValor = "UPDATE ReservaGeneral SET IDTipoPago = '3', UsuarioTrEd='Profesor sin pago', FechaTrEd=NOW(),EstadoTransaccion='Sin cobro por politicas sta monica' WHERE IDReservaGeneral ='" . $IDReserva . "'";
                $dbo->query($actulizaValor);
            }
            */
        }
        return $Cobrar;
    }
} //end class
