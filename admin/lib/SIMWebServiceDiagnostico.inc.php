<?php
class SIMWebServiceDiagnostico {

	function validar_autodiagnostico_usuario($IDClub,$IDSocio,$IDUsuario){

		$dbo =& SIMDB::get();

		$datos_club["SolicitarRegistroAutodiagnostico"] = "N";
		if( !empty( $IDClub ) ){

			$datos_club = $dbo->fetchAll("Club", " IDClub  = '" . $IDClub . "' ", "array");
			$otra_config_club = $dbo->fetchAll("ConfiguracionClub", " IDClub  = '" . $id_club . "' ", "array");

				if(!empty($IDSocio)){
					$Campo="IDSocio";
					$Valor=$IDSocio;
					$HoraIngreso="";
					$Estado="P";
				}
				else{
					$Campo="IDUsuario";
					$Valor=$IDUsuario;
					$HoraIngreso=date("Y-m-d H:i:s");
					$Estado="R";
				}






				//Autodisagnostico al abrir app
				$diagnostico_activa = 0;
				$response_diagnostico = array();
				$datos_diagnostico_obl=array();
				$sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'S' or DirigidoA = 'T')  ORDER BY Orden ";
				$qry_diagnostico = $dbo->query($sql_diagnostico);
				if ($dbo->rows($qry_diagnostico) > 0) {
					while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
						$mostrar_disgnostico = 0;
						//Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
						if ($r_diagnostico["UnaporSocio"] == "S") {
							$sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
							$r_resp = $dbo->query($sql_resp);
							if ($dbo->rows($r_resp) <= 0) {
								$mostrar_disgnostico = 1;
							}
						} else {
							$fecha_hoy = date("Y-m-d") . " 00:00:00";
							$sql_unica = "SELECT IDDiagnosticoRespuesta FROM  DiagnosticoRespuesta WHERE IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' and FechaTrCr >= '" . $fecha_hoy . "' and IDSocio = '".$IDSocio."' ";
							$r_resp = $dbo->query($sql_unica);
							if ($dbo->rows($r_resp) <= 0) {
								$mostrar_disgnostico = 1;
							}
						}
						//Verifico si la encuesta es solo para algunos socios para mostrar o no
						$permiso_diagnostico = SIMWebServiceApp::verifica_ver_diagnostico($r_diagnostico, $IDSocio, $IDUsuario);
						//$permiso_encuesta=1;						
						if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
							$diagnostico["IDClub"] = $r_diagnostico["IDClub"];
							$diagnostico["IDDiagnostico"] = $r_diagnostico["IDDiagnostico"];
							$diagnostico["Nombre"] = $r_diagnostico["Nombre"];
							$diagnostico["Descripcion"] = $r_diagnostico["Descripcion"];
							$r_diagnostico["DiagnosticoObligatorio"];
							if($r_diagnostico["DiagnosticoObligatorio"]=="S"){
							  $datos_diagnostico_obl["SolicitarRegistroAutodiagnostico"] = "S";
							  $datos_diagnostico_obl["SolicitarRegistroAutodiagnosticoLabel"] = $otra_config_club["SolicitarRegistroAutodiagnosticoLabel"];
							}													
						}
					} //ednw while
				}

				$respuesta["message"] = "Registrado con exito";
				$respuesta["success"] = true;
				$respuesta["response"] = $datos_diagnostico_obl;
		}
		else{
			$respuesta["message"] = "DC1. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;

	}// fin function

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

                $mostrar_encuesta = SIMWebServiceApp::verifica_ver_diagnostico($r, $IDSocio, $IDUsuario);

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
                    if (!empty($datos_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else :
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
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)) :
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
        require LIBDIR . "SIMWebServiceUsuarios.inc.php";
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
                $result_validacion = SIMWebServiceUsuarios::get_verifica_documento($IDClub, $NumeroDocumento);
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
                if (count($datos_respuesta) > 0) :

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

                    foreach ($datos_respuesta as $detalle_respuesta) :
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

		

} //end class
?>
