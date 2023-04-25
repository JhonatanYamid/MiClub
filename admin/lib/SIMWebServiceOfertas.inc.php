<?php
class SIMWebServiceOfertas
{

    public function get_configuracion_ofertas($IDClub, $IDSocio, $Version)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $array_tipo_contrato = SIMResources::$tipo_contrato;


        $sql = "SELECT * FROM ConfiguracionOfertaLaboral  WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and Version = '" . $Version . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {

            //Tipo Contrato
            $response_tipo_contrato = array();
            if (count($array_tipo_contrato) > 0) :
                foreach ($array_tipo_contrato as $nombre_tipo) :
                    $dato_tipo_contrato["Valor"] = $nombre_tipo;
                    array_push($response_tipo_contrato, $dato_tipo_contrato);
                endforeach;
            endif;

            $configuracion["TipoContrato"] = $response_tipo_contrato;
            $response_industria = array();
            $sql_otros = "SELECT * From Industria Where Publicar = 'S'";
            $result_otros = $dbo->query($sql_otros);
            while ($row_otros = $dbo->fetchArray($result_otros)) :
                $array_otros["IDIndustria"] = $row_otros["IDIndustria"];
                $array_otros["Valor"] = $row_otros["Nombre"];
                array_push($response_industria, $array_otros);
            endwhile;

            $configuracion["Industrias"] = $response_industria;


            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["PermitePublicarOferta"] = $r["PermitePublicarOferta"];
                $configuracion["MostrarTabMisOfertasLaborales"] = $r["MostrarTabMisOfertasLaborales"];
                $configuracion["LabelTabMisOfertasLaborales"] = $r["LabelTabMisOfertasLaborales"];
                $configuracion["LabelTabOfertasLaborales"] = $r["LabelTabOfertasLaborales"];
                $configuracion["LabelTabMisAplicaciones"] = $r["LabelTabMisAplicaciones"];
                $configuracion["LabelAplicarParaMi"] = $r["LabelAplicarParaMi"];
                $configuracion["LabelAplicarParaTercero"] = $r["LabelAplicarParaTercero"];
                $configuracion["PermiteAplicarParaMi"] = $r["PermiteAplicarParaMi"];
                $configuracion["PermiteAplicarParaTercero"] = $r["PermiteAplicarParaTercero"];
                $configuracion["MostrarTelefonoAplicarParaMi"] = $r["MostrarTelefonoAplicarParaMi"];
                $configuracion["MostrarEmailAplicarParaMi"] = $r["MostrarEmailAplicarParaMi"];
                $configuracion["MostrarHojaVidaAplicarParaMi"] = $r["MostrarHojaVidaAplicarParaMi"];
                $configuracion["MostrarNombreAplicarTercero"] = $r["MostrarNombreAplicarTercero"];
                $configuracion["MostrarTelefonoAplicarTercero"] = $r["MostrarTelefonoAplicarTercero"];
                $configuracion["MostrarEmailAplicarTercero"] = $r["MostrarEmailAplicarTercero"];
                $configuracion["MostrarHojaVidaAplicarTercero"] = $r["MostrarHojaVidaAplicarTercero"];
                $configuracion["HeaderAplicarParaMiTexto"] = $r["HeaderAplicarParaMiTexto"];
                $configuracion["FooterAplicarParaMiTexto"] = $r["FooterAplicarParaMiTexto"];
                $configuracion["HeaderAplicarParaTerceroTexto"] = $r["HeaderAplicarParaTerceroTexto"];
                $configuracion["FooterAplicarParaTerceroTexto"] = $r["FooterAplicarParaTerceroTexto"];
                //nuevo documento adjunto
                $configuracion["LabelDocumentoNuevo"] = $r["LabelDocumentoNuevo"];
                $configuracion["PermiteDocumentoNuevo"] = $r["PermiteDocumentoNuevo"];
                $configuracion["ObligatorioDocumentoNuevoParaMi"] = $r["ObligatorioDocumentoNuevoParaMi"];
                $configuracion["ObligatorioDocumentoNuevoParaTercero"] = $r["ObligatorioDocumentoNuevoParaTercero"];
                $configuracion["LabelVerDocumentoAdjunto"] = $r["LabelVerDocumentoAdjunto"];

                $configuracion["MostrarCargoActualParaMi"] = $r["MostrarCargoActualParaMi"];
				$configuracion["MostrarCargoActualTercero"] = $r["MostrarCargoActualTercero"];
				$configuracion["MostrarRazonPostulacionParaMi"] = $r["MostrarRazonPostulacionParaMi"];
				$configuracion["MostrarRazonPostulacionTercero"] = $r["MostrarRazonPostulacionTercero"];
				$configuracion["ObilgatorioTelefonoParaMi"] = $r["ObilgatorioTelefonoParaMi"];
				$configuracion["ObligatorioEmailParaMi"] = $r["ObligatorioEmailParaMi"];
				$configuracion["ObligatorioHojaVidaParaMi"] = $r["ObligatorioHojaVidaParaMi"];
				$configuracion["ObligatorioCargoActualParaMi"] = $r["ObligatorioCargoActualParaMi"];
				$configuracion["ObligatorioRazonPostulacionParaMi"] = $r["ObligatorioRazonPostulacionParaMi"];
				$configuracion["ObligatorioNombreTercero"] = $r["ObligatorioNombreTercero"];
				$configuracion["ObligatorioTelefonoTercero"] = $r["ObligatorioTelefonoTercero"];
				$configuracion["ObligatorioEmailTercero"] = $r["ObligatorioEmailTercero"];
				$configuracion["ObligatorioHojaVidaTercero"] = $r["ObligatorioHojaVidaTercero"];
				$configuracion["ObligatorioCargoActualTercero"] = $r["ObligatorioCargoActualTercero"];
				$configuracion["ObligatorioRazonPostulacionTercero"] = $r["ObligatorioRazonPostulacionTercero"];

                array_push($response, $configuracion);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Ofertasnoestáactivo.', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_oferta($IDClub, $IDSocio, $IDUsuario, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin, $Version)
    {
        $dbo = &SIMDB::get();
        //if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario) ) && !empty($IDIndustria) && !empty($NombreEmpresa) && !empty($PublicarEmpresa) && !empty($Cargo)) {
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

            //verifico que el socio exista y pertenezca al club
            if (!empty($IDSocio)) {
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            } elseif (!empty($IDUsuario)) {
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
            }

            if (!empty($id_socio) || !empty($id_usuario)) {


                $sql_oferta = $dbo->query("INSERT INTO Oferta (IDSocio	, IDUsuario, IDIndustria, IDClub, IDEstadoOferta, TipoContrato, NombreEmpresa, PublicarEmpresa, Cargo, Ciudad, NombreEncargado, CorreoContacto, DescripcionCargo, ComentarioAdicional, FechaInicio, FechaFin, Version, UsuarioTrCr, FechaTrCr	)
                                    Values ('" . $IDSocio . "','" . $IDUsuario . "','" . $IDIndustria . "','" . $IDClub . "','5','" . $TipoContrato . "','" . $NombreEmpresa . "','" . $PublicarEmpresa . "','" . $Cargo . "','" . $Ciudad . "','" . $NombreEncargado . "','" . $CorreoContacto . "','" . $DescripcionCargo . "','" . $ComentarioAdicional . "'
                                              ,'" . $FechaInicio . "','" . $FechaFin . "', $Version ,'App',NOW())");

                $id_oferta = $dbo->lastID();

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_edita_oferta($IDClub, $IDOferta, $IDEstadoOferta, $IDSocio, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin, $Version)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDOferta) && !empty($IDIndustria) && !empty($NombreEmpresa) && !empty($PublicarEmpresa) && !empty($Cargo)) {

            //verifico que el socio exista y pertenezca al club
            if (!empty($IDSocio)) {
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            } elseif (!empty($IDUsuario)) {
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
            }

            if (!empty($id_socio) || !empty($id_usuario)) {

                $sql_oferta = "UPDATE Oferta
                          SET  IDIndustria='" . $IDIndustria . "', IDEstadoOferta='" . $IDEstadoOferta . "', TipoContrato='" . $TipoContrato . "', NombreEmpresa='" . $NombreEmpresa . "',
                                PublicarEmpresa='" . $PublicarEmpresa . "', Cargo='" . $Cargo . "', Ciudad='" . $Ciudad . "', NombreEncargado='" . $NombreEncargado . "', CorreoContacto='" . $CorreoContacto . "',
                                DescripcionCargo='" . $DescripcionCargo . "', ComentarioAdicional='" . $ComentarioAdicional . "', FechaInicio='" . $FechaInicio . "', FechaFin='" . $FechaFin . "',
                                UsuarioTrCr='" . $IDSocio . "', FechaTrCr=NOW()
                          WHERE IDOferta = '" . $IDOferta . "'";

                $dbo->query($sql_oferta);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_aplicar_oferta($IDClub, $IDSocio, $IDOferta, $NombreRecomendado, $Telefono, $CorreoElectronico, $File = "", $IDUsuario, $Version, $CargoActual, $RazonPostulacion)
    {
        $dbo = &SIMDB::get();
        $datos_oferta = $dbo->fetchAll("Oferta", " IDOferta = '" . $IDOferta . "' ", "array");
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDOferta)) {

            //verifico que el socio exista y pertenezca al club
            if (!empty($IDSocio)) {
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            } elseif (!empty($IDUsuario)) {
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' ");
            }

            if (!empty($id_socio) || !empty($id_usuario)) {
             if (isset($File)) {

                    $campo_foto = "Archivo";
                    $files = SIMFile::upload($File["Archivo"], OFERTA_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = "error de archivo";
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];
                    
                    
                    $campo_foto1 = "Archivo2";
                    $files = SIMFile::upload($File["Archivo2"], OFERTA_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo2"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = "error de archivo";
                        return $respuesta;
                    endif;
                    $Archivo2= $files[0]["innername"];
                    
                    
                    
                } //end if */
                                    

                $sql_candidato = $dbo->query("INSERT INTO OfertaCandidato (IDOferta,IDSocio	, IDUsuario, NombreRecomendado, Telefono, CorreoElectronico, Archivo,Archivo2,CargoActual,RazonPostulacion, UsuarioTrCr, FechaTrCr)
                             Values ('$IDOferta', '$IDSocio','$IDUsuario','$NombreRecomendado','$Telefono','$CorreoElectronico','$Archivo','$Archivo2','$CargoActual','$RazonPostulacion','App',NOW())");

                $id_candidato = $dbo->lastID();

                //SIMUtil::notificar_nueva_aplicacion_oferta($id_candidato);

                $Mensaje = SIMUtil::get_traduccion('', '', 'Sehacreadounanuevaaplicaciónaunaofertalaboral', LANG) . ": " . $datos_oferta["Cargo"];
                //Consulto que socio se les envia la notificacion
                $id_socio_oferta = $dbo->getFields("Oferta", "IDSocio", "IDOferta = '" . $IDOferta . "' and IDClub = '" . $IDClub . "'");
                if ((int) $id_socio_oferta > 0) :
                    SIMUtil::enviar_notificacion_push_general($IDClub, $id_socio_oferta, $Mensaje);
                    SIMUtil::enviar_oferta($IDOferta, $id_candidato);
                endif;

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "O3." . SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_ofertas($IDClub, $IDSocio, $IDEstadoOferta, $Version)
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

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_oferta = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Oferta WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "' and Version = '" . $Version . "' " . $condiciones_oferta . " ORDER BY FechaInicio DESC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
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
                while ($row_candidatos = $dbo->fetchArray($result_candidatos)) :
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
                    
                    
                     if (!empty($row_candidatos["Archivo2"])) {
                        $archivo2 = OFERTA_ROOT . $row_candidatos["Archivo2"];
                    } else {
                        $archivo2 = "";
                    }

                    $array_candidatos["Archivo2"] = $archivo2;
                    
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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

}
