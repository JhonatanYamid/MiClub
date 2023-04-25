<?php

class SIMWebServiceGalerias
{
    public function get_configuracion_galeria($IDClub, $Version)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT * FROM ConfiguracionGaleria WHERE IDClub = $IDClub AND Version = $Version AND Activa = 1";

            $QRYDatos = $dbo->query($SQLDatos);

            if ($dbo->rows($QRYDatos) > 0) :
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[TipoInicio] = $Datos[TipoInicio];
                    $InfoDatos[MisGalerias] = $Datos[MisGalerias];
                    $InfoDatos[PermiteMeGusta] = $Datos[PermiteMeGusta];
                    $InfoDatos[ImagenMeGustaLleno] = GALERIA_ROOT . $Datos[ImagenMeGustaLleno];
                    $InfoDatos[ImagenMeGustaVacio] = GALERIA_ROOT . $Datos[ImagenMeGustaVacio];

                    array_push($response, $InfoDatos);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_configuracion_galeria', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_configuracion_galeria_empleados($IDClub, $Version)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT * FROM ConfiguracionGaleriaEmpleados WHERE IDClub = $IDClub AND Version = $Version AND Activa = 1";
            $QRYDatos = $dbo->query($SQLDatos);

            if ($dbo->rows($QRYDatos) > 0) :
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[TipoInicio] = $Datos[TipoInicio];
                    $InfoDatos[MisGalerias] = $Datos[MisGalerias];
                    $InfoDatos[PermiteMeGusta] = $Datos[PermiteMeGusta];
                    $InfoDatos[ImagenMeGustaLleno] = GALERIA_ROOT . $Datos[ImagenMeGustaLleno];
                    $InfoDatos[ImagenMeGustaVacio] = GALERIA_ROOT . $Datos[ImagenMeGustaVacio];

                    array_push($response, $InfoDatos);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_configuracion_galeria', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_seccion_galeria($IDClub, $IDSocio = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) :
        //$condicion = " and SSG.IDSeccionGaleria = S.IDSeccionGaleria and IDSocio = '" . $IDSocio . "' ";
        //$tabla_join = ", SocioSeccionGaleria SSG ";
        endif;

        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
        if ($IDClub == 36 && $TipoSocio == "Estudiante") {
            $condicion_galeria .= " and TipoSocio = 'Estudiante'";
        } else {
            $condicion_galeria .= " and TipoSocio <> 'Estudiante'";
        }

        $response = array();
        $sql = "SELECT S.* FROM SeccionGaleria$Version S  WHERE (S.DirigidoA = 'S' OR S.DirigidoA = 'T') AND S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una galeria publicada
                $id_galeria = $dbo->getFields("Galeria" . $Version, "IDGaleria" . $Version, "IDSeccionGaleria" . $Version . " = '" . $r["IDSeccionGaleria" . $Version] . "' and Publicar = 'S' " . $condicion_galeria);
                if (!empty($id_galeria)) :
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDSeccion"] = $r["IDSeccionGaleria" . $Version];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];

                    $seccion["Icono"] = GALERIA_ROOT . $r["Icono"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];
                    array_push($response, $seccion);
                endif;
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

    public function get_seccion_galeria_empleados($IDClub, $IDSocio = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT S.* FROM SeccionGaleria$Version S  WHERE (S.DirigidoA = 'E' OR S.DirigidoA = 'T') AND S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una galeria publicada
                $id_galeria = $dbo->getFields("Galeria" . $Version, "IDGaleria" . $Version, "IDSeccionGaleria" . $Version . " = '" . $r["IDSeccionGaleria" . $Version] . "' and Publicar = 'S' " . $condicion_galeria);
                if (!empty($id_galeria)) :
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDSeccion"] = $r["IDSeccionGaleria" . $Version];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];

                    $seccion["Icono"] = GALERIA_ROOT . $r["Icono"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];
                    array_push($response, $seccion);
                endif;
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

    public function get_galeria($IDClub, $IDSeccion = "", $IDSocio = "", $tag = "", $Version = "", $IDFoto = "")

    {
        $dbo = &SIMDB::get();

        // Secciones Socio
        if (!empty($IDSocio)) :
            /*
                $sql_seccion_socio = $dbo->query("Select * From SocioSeccionGaleria Where IDSocio = '".$IDSocio."'");
                while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
                $array_secciones_socio[] = $row_seccion["IDSeccionGaleria"];
                endwhile;

                if (count($array_secciones_socio)>0):
                $IDSecciones = implode(",",$array_secciones_socio);
                $array_condiciones[] = " IDSeccionGaleria in(".$IDSecciones.") ";
                endif;
                 */
            $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
            if ($IDClub == 36 && $TipoSocio == "Estudiante") {
                $array_condiciones[] = " TipoSocio = 'Estudiante'";
            } else {
                $array_condiciones[] = " TipoSocio = ''";
            }
        endif;

        // Seccion Especifica
        if (!empty($IDSeccion)) :
            $array_condiciones[] = " IDSeccionGaleria$Version  = '" . $IDSeccion . "'";
        else :
            $array_condiciones[] = " Home  = 'S'";
        endif;

        // Tag
        if (!empty($tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_galeria = " and " . $condiciones;
        endif;

        //para el gun club se ordena por fecha de creacion
        if ($IDClub == 25) {
            $orden = " Order by Fecha DESC";
        } else {
            $orden = " Order by Orden DESC";
        }

        $response = array();
        $sql = "SELECT * FROM Galeria" . $Version . " WHERE (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones_galeria .  $orden;
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["Foto"])) {
                    if (strstr(strtolower($r["Foto"]), "http://")) {
                        $foto_principal = $r["Foto"];
                    } else {
                        $foto_principal = GALERIA_ROOT . $r["Foto"];
                    }

                    //$foto_principal = GALERIA_ROOT.$r["Foto"];
                }
                
                   if (!empty($r["FotoPagina"])) {
                    if (strstr(strtolower($r["FotoPagina"]), "http://")) {
                        $foto_principal_pagina = $r["FotoPagina"];
                    } else {
                        $foto_principal_pagina = GALERIA_ROOT . $r["FotoPagina"];
                    }

                    //$foto_principal = GALERIA_ROOT.$r["Foto"];
                }else{
               $foto_principal_pagina ="";
                }
                

                //Fotos
                $response_foto = array();
                $response_foto1 = array();
                $sql_foto = "SELECT * FROM FotoGaleria" . $Version . " WHERE IDGaleria" . $Version . " = '" . $r["IDGaleria" . $Version] . "' ORDER BY Orden ASC";

                //condiciones para el like
                if (!empty($IDFoto)) {
                    $sql_foto = "SELECT * FROM FotoGaleria" . $Version . "  WHERE IDGaleria = '" . $r["IDGaleria"] . "' AND IDFoto='" . $IDFoto . "'";
                }

                //  echo $sql_foto;
                $qry_foto = $dbo->query($sql_foto);
                if ($dbo->rows($qry_foto) > 0) {
                    while ($r_foto = $dbo->fetchArray($qry_foto)) {
                        $foto_galeria["IDClub"] = $IDClub;
                        $foto_galeria["IDFoto"] = $r_foto["IDFoto" . $Version];
                        $foto_galeria["IDGaleria"] = $r_foto["IDGaleria" . $Version];
                        $foto_galeria["Nombre"] = $r_foto["Nombre"];
                        $foto_galeria["Orden"] = $r_foto["Orden"];
                        $foto_galeria["Descripcion"] = $r_foto["Descripcion"];
                        $foto_galeria["MeGusta"] = $dbo->getFields("GaleriaLike", "MeGusta", "IDSocio = '" . $IDSocio . "' AND IDFoto='" . $r_foto[IDFoto] . "'");

                        //cantidad de me gusta
                        if (empty($Version)) {
                            $VersionGaleria = 1;
                        } else {
                            $VersionGaleria = 2;
                        }
                        $sql_like_total = "SELECT count(IDGaleriaLike) as Total FROM GaleriaLike WHERE IDFoto = '" . $r_foto["IDFoto"] . "' and Version = '" . $VersionGaleria . "' AND MeGusta='S'";
                        $r_like_total = $dbo->query($sql_like_total);
                        $row_like_total = $dbo->fetchArray($r_like_total);
                        $foto_galeria["CantidadMeGusta"] = $row_like_total["Total"];

                        if (!empty($r_foto["Foto"])) :
                            if (strstr(strtolower($r_foto["Foto"]), "http://")) {
                                $foto = $r_foto["Foto"];
                            } else {
                                $foto = GALERIA_ROOT . $r_foto["Foto"];
                            }
                        else :
                            $foto = "";
                        endif; 
                        //array fotos paginas 
                         if (!empty($r_foto["FotoPagina"])) :
                            if (strstr(strtolower($r_foto["FotoPagina"]), "http://")) {
                                $foto_pagina = $r_foto["FotoPagina"];
                            } else {
                                $foto_pagina = GALERIA_ROOT . $r_foto["FotoPagina"];
                            }
                        else :
                            $foto_pagina = "";
                        endif;
                        
                        
                        $foto_galeria1["FotoPagina"] = $foto_pagina;
                        $foto_galeria["Foto"] = $foto;
                        if(!empty($foto_pagina)):
                        $foto_galeria1["IDClub"] = $IDClub;
                        $foto_galeria1["IDFoto"] = $r_foto["IDFoto" . $Version];
                        $foto_galeria1["IDGaleria"] = $r_foto["IDGaleria" . $Version];
                        $foto_galeria1["Nombre"] = $r_foto["Nombre"];
                        $foto_galeria1["Orden"] = $r_foto["Orden"];
                        $foto_galeria1["Descripcion"] = $r_foto["Descripcion"];
                        $foto_galeria1["MeGusta"] = $dbo->getFields("GaleriaLike", "MeGusta", "IDSocio = '" . $IDSocio . "' AND IDFoto='" . $r_foto[IDFoto] . "'");
                        array_push($response_foto1, $foto_galeria1);
                        endif;
                        if(!empty($foto)):
                        array_push($response_foto, $foto_galeria);
                        endif;
                    } //ednw hile
                }

                //VERIFICO SI SE PUEDE MOSTRAR LA DESCRIPCION
               $PermiteDescripcion= $dbo->getFields("ConfiguracionGaleria", "PermiteDescripcion", "IDClub = '" . $IDClub . "'");
               if($PermiteDescripcion == "S"){

                $Descripcion= "  " . $r["Descripcion"];
               }else{
                $Descripcion="";
               }

                $datos_galeria["IDClub"] = $r["IDClub"];
                $datos_galeria["IDSeccionGaleria"] = $r["IDSeccionGaleria" . $Version];
                $datos_galeria["IDGaleria"] = $r["IDGaleria" . $Version];
                $datos_galeria["Nombre"] = $r["Nombre"] . $Descripcion;
                $datos_galeria["Descripcion"] = $r["Descripcion"];


                if ($r["Fecha"] > 0) {
                    $Fecha = $r["Fecha"];
                } else {
                    $Fecha = "";
                }

                $datos_galeria["Fecha"] = $Fecha;
                $datos_galeria["Foto"] = $foto_principal;
                $datos_galeria["FotoPagina"] = $foto_principal_pagina;
                
                $datos_galeria["FotoGaleria"] = $response_foto;
                $datos_galeria["FotoGaleriaPagina"] = $response_foto1;
                array_push($response, $datos_galeria);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            if ($IDClub == 151) {
                $respuesta_serv = "There are no galleries";
            } else {
                $respuesta_serv = SIMUtil::get_traduccion('', '', 'Nohaygaleriasactivas.', LANG);
            }

            $respuesta["message"] = $respuesta_serv;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_galeria_empleados($IDClub, $IDSeccion = "", $IDUsuario = "", $tag = "", $Version = "", $IDFoto = "")
    {
        $dbo = &SIMDB::get();

        // Secciones Socio
        if (!empty($IDUsuario)) :
            $sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccionGaleria Where IDSocio = '" . $IDUsuario . "'");
            while ($row_seccion = $dbo->fetchArray($sql_seccion_empleado)) :
                $array_secciones_empleado[] = $row_seccion["IDSeccionGaleria"];
            endwhile;

            if (count($array_secciones_empleado) > 0) :
                $IDSecciones = implode(",", $array_secciones_empleado);
                $array_condiciones[] = " IDSeccionGaleria in(" . $IDSecciones . ") ";
            endif;
        endif;

        // Seccion Especifica
        if (!empty($IDSeccion)) :
            $array_condiciones[] = " IDSeccionGaleria$Version    = '" . $IDSeccion . "'";
        endif;

        // Tag
        if (!empty($tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_galeria = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Galeria" . $Version . " WHERE (DirigidoA = 'E' or DirigidoA = 'T' ) and Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones_galeria . "ORDER BY IDGaleria DESC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["Foto"])) {
                    if (strstr(strtolower($r["Foto"]), "http://")) {
                        $foto_principal = $r["Foto"];
                    } else {
                        $foto_principal = GALERIA_ROOT . $r["Foto"];
                    }

                    //$foto_principal = GALERIA_ROOT.$r["Foto"];
                }
                
                 if (!empty($r["FotoPagina"])) {
                    if (strstr(strtolower($r["FotoPagina"]), "http://")) {
                        $foto_principal_pagina = $r["FotoPagina"];
                    } else {
                        $foto_principal_pagina = GALERIA_ROOT . $r["FotoPagina"];
                    }

                    //$foto_principal_pagina = GALERIA_ROOT.$r["Foto"];
                }else{
                               $foto_principal_pagina ="";
                               }


 
                //Fotos
                $response_foto = array();
                $response_foto1 = array();
                $sql_foto = "SELECT * FROM FotoGaleria" . $Version . " WHERE IDGaleria = '" . $r["IDGaleria"] . "' ORDER BY RAND()";

                //condiciones para el like
                if (!empty($IDFoto)) {
                    $sql_foto = "SELECT * FROM FotoGaleria" . $Version . "  WHERE IDGaleria = '" . $r["IDGaleria"] . "' AND IDFoto='" . $IDFoto . "'";
                }


                $qry_foto = $dbo->query($sql_foto);
                if ($dbo->rows($qry_foto) > 0) {
                    while ($r_foto = $dbo->fetchArray($qry_foto)) {
                        $foto_galeria["IDClub"] = $IDClub;
                        $foto_galeria["IDFoto"] = $r_foto["IDFoto"];
                        $foto_galeria["IDGaleria"] = $r_foto["IDGaleria"];
                        $foto_galeria["Nombre"] = $r_foto["Nombre"];
                        $foto_galeria["Orden"] = $r_foto["Orden"];
                        $foto_galeria["Descripcion"] = $r_foto["Descripcion"];
                        $foto_galeria["MeGusta"] = $dbo->getFields("GaleriaLike", "MeGusta", "IDUsuario = '" . $IDUsuario . "' AND IDFoto='" . $r_foto[IDFoto] . "'");

                        //cantidad de me gusta
                        if (empty($Version)) {
                            $VersionGaleria = 1;
                        } else {
                            $VersionGaleria = 2;
                        }
                        $sql_like_total = "SELECT count(IDGaleriaLike) as Total FROM GaleriaLike WHERE IDFoto = '" . $r_foto["IDFoto"] . "' and Version = '" . $VersionGaleria . "' AND MeGusta='S'";
                        $r_like_total = $dbo->query($sql_like_total);
                        $row_like_total = $dbo->fetchArray($r_like_total);
                        $foto_galeria["CantidadMeGusta"] = $row_like_total["Total"];

                        if (!empty($r_foto["Foto"])) :
                            if (strstr(strtolower($r_foto["Foto"]), "http://")) {
                                $foto = $r_foto["Foto"];
                            } else {
                                $foto = GALERIA_ROOT . $r_foto["Foto"];
                            }
                        else :
                            $foto = "";
                        endif;
                          //array fotos paginas 
                         if (!empty($r_foto["FotoPagina"])) :
                            if (strstr(strtolower($r_foto["FotoPagina"]), "http://")) {
                                $foto_pagina = $r_foto["FotoPagina"];
                            } else {
                                $foto_pagina = GALERIA_ROOT . $r_foto["FotoPagina"];
                            }
                        else :
                            $foto_pagina = "";
                        endif;
                        $foto_galeria1["FotoPagina"] = $foto_pagina;
                        $foto_galeria["Foto"] = $foto;
                        if($foto_pagina <> ""):
                        array_push($response_foto1, $foto_galeria1);
                        endif;
                        if($foto <> ""):
                        array_push($response_foto, $foto_galeria);
                        endif;
                    } //ednw hile
                }

                $datos_galeria["IDClub"] = $r["IDClub"];
                $datos_galeria["IDSeccionGaleria"] = $r["IDSeccionGaleria"];
                $datos_galeria["IDGaleria"] = $r["IDGaleria"];
                $datos_galeria["Nombre"] = $r["Nombre"];
                $datos_galeria["Descripcion"] = $r["Descripcion"];
                $datos_galeria["Fecha"] = $r["Fecha"];
                $datos_galeria["Foto"] = $foto_principal;
                $datos_galeria["FotoPagina"] = $foto_principal_pagina;
                $datos_galeria["FotoGaleria"] = $response_foto;
                array_push($response, $datos_galeria);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradoclub', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function actualizar_me_gusta($IDClub, $IDSocio, $Version, $IDFoto, $MeGusta)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDFoto)) {

            //verifico si ya no hay un like del socio en la foto
            /*        $sqlLike = "SELECT * FROM GaleriaLike WHERE IDSocio='" . $IDSocio . "' AND IDFoto='" . $IDFoto . "' AND Version='" . $Version . "'";
            $qryLike = $dbo->query($sqlLike);
            if ($dbo->rows($qryLike) > 0) {
                $sql_update_like = "UPDATE GaleriaLike SET  MeGusta='" . $MeGusta . "'  WHERE IDSocio='" . $IDSocio . "' AND IDFoto='" . $IDFoto . "' AND Version='" . $Version . "'";
                $qry_update_Like = $dbo->query($sql_update_like);
            } else {
                $sql_insertar_like = "INSERT INTO GaleriaLike (IDSocio,IDFoto,Version,MeGusta,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDFoto . "','" . $Version . "','" . $MeGusta . "','WS',NOW())";
                $qry_insertar_like = $dbo->query($sql_insertar_like);
            } */

            if ($MeGusta == "S") {
                $sql_insertar_like = "INSERT INTO GaleriaLike (IDSocio,IDFoto,Version,MeGusta,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDFoto . "','" . $Version . "','" . $MeGusta . "','WS',NOW())";
                $qry_insertar_like = $dbo->query($sql_insertar_like);
            } else {

                $sql_like = "DELETE FROM GaleriaLike WHERE  IDSocio = '" . $IDSocio . "' and Version='" . $Version . "' and IDFoto = '" . $IDFoto . "' LIMIT 1";
                $dbo->query($sql_like);
            }


            $respuesta["message"] = "Actualización de likes correcta";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function actualizar_me_gusta_empleados($IDClub, $IDUsuario, $Version, $IDFoto, $MeGusta)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDUsuario) && !empty($IDFoto)) {

            //verifico si ya no hay un like del socio en la foto
            /*      $sqlLike = "SELECT * FROM GaleriaLike WHERE IDUsuario='" . $IDUsuario . "' AND IDFoto='" . $IDFoto . "' AND Version='" . $Version . "'";
            $qryLike = $dbo->query($sqlLike);
            if ($dbo->rows($qryLike) > 0) {
                $sql_update_like = "UPDATE GaleriaLike SET  MeGusta='" . $MeGusta . "'  WHERE IDUsuario='" . $IDUsuario . "' AND IDFoto='" . $IDFoto . "' AND Version='" . $Version . "'";
                $qry_update_Like = $dbo->query($sql_update_like);
            } else {
                $sql_insertar_like = "INSERT INTO GaleriaLike (IDUsuario,IDFoto,Version,MeGusta,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDFoto . "','" . $Version . "','" . $MeGusta . "','WS',NOW())";
                $qry_insertar_like = $dbo->query($sql_insertar_like);
            }
 */

            if ($MeGusta == "S") {
                $sql_insertar_like = "INSERT INTO GaleriaLike (IDUsuario,IDFoto,Version,MeGusta,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDFoto . "','" . $Version . "','" . $MeGusta . "','WS',NOW())";
                $qry_insertar_like = $dbo->query($sql_insertar_like);
            } else {

                $sql_like = "DELETE FROM GaleriaLike WHERE  IDUsuario = '" . $IDUsuario . "' and Version='" . $Version . "' and IDFoto = '" . $IDFoto . "' LIMIT 1";
                $dbo->query($sql_like);
            }


            $respuesta["message"] = "Actualización de likes correcta";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_detalle_foto_galeria($IDClub, $IDSocio, $Version, $IDFoto)
    {
        //Fotos
        // echo "IDClub=" . $IDClub . " IDSocio=" . $IDSocio . " VErsion=" . $Version;
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDFoto)) {
            if ($Version == 1) {
                $Version = "";
            }
            //traigo la foto que tiene me gusta o no
            $respuesta = SIMWebServiceGalerias::get_galeria($IDClub, "", $IDSocio, "", $Version, $IDFoto);
            $respuestaDatos = $respuesta["response"];
            $Datos = $respuestaDatos[0][FotoGaleria];
            // print_r($Datos);

            foreach ($Datos as $key => $value) {
                $likes["IDClub"] = $value["IDClub"];
                $likes["IDFoto"] = $value["IDFoto"];
                $likes["IDGaleria"] = $value["IDGaleria"];
                $likes["Nombre"] = $value["Nombre"];
                $likes["Orden"] = $value["Orden"];
                $likes["Descripcion"] = $value["Descripcion"];
                $likes["MeGusta"] = (string)$value["MeGusta"];
                $likes["CantidadMeGusta"] = $value["CantidadMeGusta"];
                $likes["Foto"] = $value["Foto"];
            }

            $respuesta["message"] = "Actualización de likes correcta";
            $respuesta["success"] = true;
            $respuesta["response"] = $likes;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_detalle_foto_galeria_empleados($IDClub, $IDUsuario, $Version, $IDFoto)
    {
        //Fotos
        // echo "IDClub=" . $IDClub . " IDSocio=" . $IDSocio . " VErsion=" . $Version;
        if (!empty($IDClub) && !empty($IDUsuario) && !empty($IDFoto)) {
            if ($Version == 1) {
                $Version = "";
            }
            //traigo la foto que tiene me gusta o no
            $respuesta = SIMWebServiceGalerias::get_galeria_empleados($IDClub, "", $IDUsuario, "", $Version, $IDFoto);

            $respuestaDatos = $respuesta["response"];
            $Datos = $respuestaDatos[0][FotoGaleria];
            // print_r($Datos);

            foreach ($Datos as $key => $value) {
                $likes["IDClub"] = $value["IDClub"];
                $likes["IDFoto"] = $value["IDFoto"];
                $likes["IDGaleria"] = $value["IDGaleria"];
                $likes["Nombre"] = $value["Nombre"];
                $likes["Orden"] = $value["Orden"];
                $likes["Descripcion"] = $value["Descripcion"];
                $likes["MeGusta"] = (string)$value["MeGusta"];
                $likes["CantidadMeGusta"] = $value["CantidadMeGusta"];
                $likes["Foto"] = $value["Foto"];
            }

            $respuesta["message"] = "Actualización de likes correcta";
            $respuesta["success"] = true;
            $respuesta["response"] = $likes;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
}
