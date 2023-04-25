<?php

    class SIMWebServiceBanner
    {
        public function get_banner_app($IDClub)
        {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM BannerApp WHERE (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY RAND()";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " ".SIMUtil::get_traduccion('', '', 'Encontrados',LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $banner["IDBannerApp"] = $r["IDBannerApp"];
                    $banner["Nombre"] = $r["Nombre"];
                    if (!empty($r["Foto1"])):
                        $foto = BANNERAPP_ROOT . $r["Foto1"];
                    else:
                        $foto = "";
                    endif;
                    $banner["Foto1"] = $foto;
                    array_push($response, $banner);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradosplash',LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else

                return $respuesta;

        } // fin function

        public function get_banner_app_empleado($IDClub)
        {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM BannerApp WHERE (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY RAND()";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " ".SIMUtil::get_traduccion('', '', 'Encontrados',LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $banner["IDBannerApp"] = $r["IDBannerApp"];
                    $banner["Nombre"] = $r["Nombre"];
                    if (!empty($r["Foto1"])):
                        $foto = BANNERAPP_ROOT . $r["Foto1"];
                    else:
                        $foto = "";
                    endif;
                    $banner["Foto1"] = $foto;
                    array_push($response, $banner);

                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradosplash',LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else

                return $respuesta;

        } // fin function
    }
