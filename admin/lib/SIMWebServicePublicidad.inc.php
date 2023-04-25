<?php

class SIMWebServicePublicidad
{
    public function get_publicidad($IDClub, $IDModulo, $IDCategoria, $TipoApp, $IDServicio, $IDSocio = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        //guardamos las imagenes y datos desde el servicio de country medellin


        $response = array();
        $datos_publicidad["Publicidad"] = $dbo->getFields("Club", "Publicidad", "IDClub = '" . $IDClub . "'");
        $datos_publicidad["PublicidadTiempo"] = $dbo->getFields("Club", "TiempoPublicidad", "IDClub = '" . $IDClub . "'");

        $datos_publicidad["TipoHeaderApp"] = $dbo->getFields("Club", "TipoHeaderApp", "IDClub = '" . $IDClub . "'");
        $datos_publicidad["TiempoPublicidadHeader"] = $dbo->getFields("Club", "TiempoPublicidad", "IDClub = '" . $IDClub . "'");

        $datos_publicidad["TipoImagenPublicidadNoticias"] = $dbo->getFields("ConfiguracionClub", "TipoImagenPublicidadNoticias", "IDClub = '" . $IDClub . "'");

        if ($datos_publicidad["Publicidad"] == "S") :
            $array_publicidad = SIMWebServicePublicidad::get_banner_publicidad($IDClub, $IDModulo, $IDSocio, "", $TipoApp, $IDServicio);
            $datos_publicidad["Publicidades"] = $array_publicidad["response"];

        else :
            $datos_publicidad["Publicidad"] = "N";
        endif;

        if ($datos_publicidad["TipoHeaderApp"] == "Publicidad" || $datos_publicidad["TipoHeaderApp"] == "PublicidadFoto" || $datos_publicidad["TipoHeaderApp"] == "Noticias" || $datos_publicidad["TipoHeaderApp"] == "Mixta") :
            $array_publicidad_header = SIMWebServicePublicidad::get_banner_publicidad_header($IDClub, $IDModulo, "", $TipoApp, $IDServicio);
            $datos_publicidad["PublicidadesHeader"] = $array_publicidad_header["response"];

            if ($datos_publicidad["TipoHeaderApp"] == "Noticias") :
                $array_publicidad_header = SIMWebServicePublicidad::get_banner_publicidad_noticias($IDClub, $IDSocio, $IDUsuario);
                $datos_publicidad["PublicidadesNoticias"] = $array_publicidad_header["response"];
            endif;

            if ($datos_publicidad["TipoHeaderApp"] == "Mixta") :
                $array_publicidad_header = SIMWebServicePublicidad::get_banner_publicidad_header($IDClub, $IDModulo, "", $TipoApp, $IDServicio);
                $datos_publicidad["PublicidadesHeader"] = $array_publicidad_header["response"];
                $array_publicidad_header = SIMWebServicePublicidad::get_banner_publicidad_noticias($IDClub, $IDSocio, $IDUsuario);
                $datos_publicidad["PublicidadesNoticias"] = $array_publicidad_header["response"];

            endif;

        else :
            $datos_publicidad["Publicidad"] = "N";
        endif;



        array_push($response, $datos_publicidad);
        $respuesta["message"] = "1 " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        return $respuesta;
    }

    public function get_banner_publicidad($IDClub, $IDModulo, $IDSocio, $IDCategoria, $TipoApp, $IDServicio)
    {

        $dbo = &SIMDB::get();



        if (empty($IDModulo) && empty($IDServicio)) {
            $IDModulo = "11";
        }

        // Modulo Especifico
        if (!empty($IDModulo)) :
            $TablaJoin = " ,PublicidadModulo PM";
            $WhereJoin = " Publicidad.IDPublicidad = PM.IDPublicidad and Activo = 'S' and IDModulo= '" . $IDModulo . "' and ";
        endif;

        if (!empty($IDServicio)) :
            $TablaJoin = " ,PublicidadServicio PS";
            $WhereJoin = " Publicidad.IDPublicidad = PS.IDPublicidad and IDServicio = '" . $IDServicio . "' and ";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_publicidad = " and " . $condiciones;
        endif;

        if ($TipoApp == "Socio") {
            $condiciones_publicidad .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } else {
            $condiciones_publicidad .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }



        $response = array();


        $sql = "SELECT * FROM Publicidad" . $TablaJoin . " WHERE " . $WhereJoin . " Publicar = 'S' and Footer = 'S'  and (FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' " . $condiciones_publicidad .  " ORDER BY Orden";
        // echo $sql;
        $qry = $dbo->query($sql);

        $sql_dia_anglo = "Select * From DiaAnglo Where Fecha = CURDATE() and IDClub = '" . $IDClub . "' Limit 1";
        $result_dia_anglo = $dbo->query($sql_dia_anglo);

        if ($dbo->rows($qry) > 0 || $dbo->rows($result_dia_anglo) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";


            while ($r = $dbo->fetchArray($qry)) {
                $publicidad["IDPublicidad"] = $r["IDPublicidad"];
                $publicidad["Nombre"] = $r["Nombre"];
                $response_accion = array();
                $publicidad["Url"] = $r["Url"];
                $publicidad["VentanaExterna"] = $r["VentanaExterna"];

                if (!empty($r["Foto1"])) :
                    $foto = PUBLICIDAD_ROOT . $r["Foto1"];
                else :
                    $foto = "";
                endif;
                $publicidad["Foto1"] = $foto;

                array_push($response, $publicidad);
            } //ednw hile

            //ednw hile

            //Especial para Anglo publicar el banner del dia
            if ($IDClub == 8) :
                $sql_dia_anglo = "Select * From DiaAnglo Where Fecha = CURDATE() and IDClub = '" . $IDClub . "' Limit 1";
                $result_dia_anglo = $dbo->query($sql_dia_anglo);
                while ($r_dia_anglo = $dbo->fetchArray($result_dia_anglo)) :
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

    public function get_banner_publicidad_header($IDClub, $IDModulo, $IDCategoria, $TipoApp, $IDServicio, $Publica = 0)
    {

        $dbo = &SIMDB::get();

        //MODULO DE INICIO
        if (empty($IDModulo) && empty($IDServicio)) {
            $IDModulo = "11";
        }


        // Modulo Especifico
        if (!empty($IDModulo)) :
            $TablaJoin = " ,PublicidadModulo PM";
            $WhereJoin = " Publicidad.IDPublicidad = PM.IDPublicidad and Activo = 'S' and IDModulo = '" . $IDModulo . "' and ";
        endif;



        if (!empty($IDServicio)) :
            $TablaJoin = " ,PublicidadServicio PS";
            $WhereJoin = " Publicidad.IDPublicidad = PS.IDPublicidad and IDServicio = '" . $IDServicio . "' and ";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_publicidad = " and " . $condiciones;
        endif;

        if ($TipoApp == "Socio") {
            $condiciones_publicidad .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } else {
            $condiciones_publicidad .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        if (empty($IDModulo) && empty($IDServicio)) {
            $condicion_home = " and MostrarHome = 'S' ";
        }

        if ($Publica == 1) :
            $condiciones_publicidad .= " and ParaSeccionPublica = 1 ";
        endif;

        $response = array();
        $sql = "SELECT * FROM Publicidad" . $TablaJoin . " WHERE " . $WhereJoin . " Publicar = 'S' and Header = 'S'  and ( FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' " . $condiciones_publicidad . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $publicidad["IDPublicidad"] = $r["IDPublicidad"];
                $publicidad["Nombre"] = $r["Nombre"];
                $response_accion = array();
                $publicidad["Url"] = $r["Url"];
                $publicidad["VentanaExterna"] = $r["VentanaExterna"];
                if (!empty($r["Foto1"])) :
                    $foto = PUBLICIDAD_ROOT . $r["Foto1"];
                else :
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

    public function get_banner_publicidad_noticias($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = SIMDB::get();
        $response = array();

        require_once LIBDIR . "SIMWebServiceNoticias.inc.php";
        require_once LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php";

        $SQLValidaNoticias1 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 3 ";
        $QRYValidaNoticias1 = $dbo->query($SQLValidaNoticias1);
        $DatoSValidaNoticias1 = $dbo->fetchArray($QRYValidaNoticias1);
        if ($DatoSValidaNoticias1[Activo] == 'S') :
            $Noticias1 = SIMWebServiceNoticias::get_noticias($IDClub, "", $IDSocio, "", "", $IDUsuario);
            foreach ($Noticias1[response] as $ID => $Noticia) :
                if ($Noticia[ParaPublicidad] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '3';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;

        $SQLValidaNoticias2 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 66 ";
        $QRYValidaNoticias2 = $dbo->query($SQLValidaNoticias2);
        $DatoSValidaNoticias2 = $dbo->fetchArray($QRYValidaNoticias2);

        if ($DatoSValidaNoticias2[Activo] == 'S') :
            $Noticias2 = SIMWebServiceNoticias::get_noticias($IDClub, "", $IDSocio, "", "2", $IDUsuario);
            foreach ($Noticias2[response] as $ID => $Noticia) :
                if ($Noticia[ParaPublicidad] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion2", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '66';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;

        $SQLValidaNoticias3 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 81 ";
        $QRYValidaNoticias3 = $dbo->query($SQLValidaNoticias1);
        $DatoSValidaNoticias3 = $dbo->fetchArray($QRYValidaNoticias3);
        if ($DatoSValidaNoticias3[Activo] == 'S') :
            $Noticias3 = SIMWebServiceNoticias::get_noticias($IDClub, "", $IDSocio, "", "3", $IDUsuario);
            foreach ($Noticias3[response] as $ID => $Noticia) :
                if ($Noticia[ParaPublicidad] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion3", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '81';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;
        // NOTICIAS INFINITAS

        $SQLNoticiasInfinitas = "SELECT * FROM NoticiaInfinita WHERE IDClub = $IDClub AND ParaPublicidad = 1";
        $QRYNoticiasInfinitas = $dbo->query($SQLNoticiasInfinitas);

        while ($Noticia = $dbo->fetchArray($QRYNoticiasInfinitas)) :

            $SQLValidaNoticiasInfi = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = $Noticia[IDModulo] ";
            $QRYValidaNoticiasInfi = $dbo->query($SQLValidaNoticiasInfi);
            $DatoSValidaNoticiasInfi = $dbo->fetchArray($QRYValidaNoticiasInfi);
            if ($DatoSValidaNoticiasInfi[Activo] == 'S') :
                $InfoResponse[IDClub] = $IDClub;
                $InfoResponse[IDSeccion] = $Noticia[IDSeccionNoticiaInfinita];
                $InfoResponse[NombreSeccion] = $dbo->getFields("SeccionNoticiaInfinita", "Nombre", "IDSeccionNoticiaInfinita = $Noticia[IDSeccionNoticiaInfinita]");
                $InfoResponse[IDModulo] = $Noticia[IDModulo];
                $InfoResponse[IDNoticia] = $Noticia[IDNoticiaInfinita];
                $InfoResponse[Titular] = $Noticia[Titular];
                $InfoResponse[Introduccion] = $Noticia[Introduccion];
                $InfoResponse[Fecha] = $Noticia[FechaInicio];

                if (!empty($Noticia["FotoPortada"])) :
                    if (strstr(strtolower($Noticia["FotoPortada"]), "http://")) {
                        $FotoPortada = $Noticia["FotoPortada"];
                    }
                    if (strstr(strtolower($Noticia["FotoPortada"]), "https://")) {
                        $FotoPortada = $Noticia["FotoPortada"];
                    } else {
                        $FotoPortada = IMGNOTICIA_ROOT . $Noticia["FotoPortada"];
                    }

                //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                else :
                    $FotoPortada = "";
                endif;

                $InfoResponse[FotoPortada] = $FotoPortada;

                if (!empty($Noticia["NoticiaFile"])) :
                    if (strstr(strtolower($Noticia["NoticiaFile"]), "http://")) {
                        $foto1 = $Noticia["NoticiaFile"];
                    }
                    if (strstr(strtolower($Noticia["NoticiaFile"]), "https://")) {
                        $foto1 = $Noticia["NoticiaFile"];
                    } else {
                        $foto1 = IMGNOTICIA_ROOT . $Noticia["NoticiaFile"];
                    }

                //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                else :
                    $foto1 = "";
                endif;

                $InfoResponse[Foto] = $foto1;

                array_push($response, $InfoResponse);
            endif;
        endwhile;

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
}
