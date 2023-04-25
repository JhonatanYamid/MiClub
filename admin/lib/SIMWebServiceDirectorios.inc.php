<?php

class SIMWebServiceDirectorios
{
    public function get_configuracion_directorio($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            // SACAMOS LA CONFIGURACION DE DIRECTORIO NORMAL NO DE SOCIOS
            $SQLDatos = "SELECT * FROM ConfiguracionDirectorio WHERE IDClub = $IDClub AND Activa = 1";
            $QRYDatos = $dbo->query($SQLDatos);
            if ($dbo->rows($QRYDatos) > 0) :
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[TipoInicio] = $Datos[TipoInicio];

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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_configuracion_directorio', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_configuracion_directorio_socio($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            // SACAMOS LA CONFIGURACION DE DIRECTORIO NORMAL DE SOCIOS
            $SQLDatos = "SELECT * FROM ConfiguracionDirectorioSocio WHERE IDClub = $IDClub AND Activa = 1";
            $QRYDatos = $dbo->query($SQLDatos);
            if ($dbo->rows($QRYDatos) > 0) :
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[TipoInicio] = $Datos[TipoInicio];

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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_configuracion_directorio', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_seccion_directorio($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT * FROM CategoriaDirectorio WHERE IDClub = $IDClub AND Publicar = 'S' ORDER BY Nombre ASC";
            $QRYDatos = $dbo->query($SQLDatos);

            if ($dbo->rows($QRYDatos) > 0) :

                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[IDSeccion] = $Datos[IDCategoriaDirectorio];
                    $InfoDatos[Nombre] = $Datos[Nombre];
                    $InfoDatos[Descripcion] = $Datos[Descripcion];
                    $InfoDatos[Icono] = DIRECTORIO_ROOT . $Datos[Icono];
                    $InfoDatos[SoloIcono] = $Datos[SoloIcono];

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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_seccion_directorio', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_seccion_directorio_socio($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT * FROM CategoriaDirectorioSocio WHERE IDClub = $IDClub AND Publicar = 'S'";
            $QRYDatos = $dbo->query($SQLDatos);

            if ($dbo->rows($QRYDatos) > 0) :

                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                // ARMAMOS LA RESPUESTA
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $Datos[IDClub];
                    $InfoDatos[IDSeccion] = $Datos[IDCategoriaDirectorioSocio];
                    $InfoDatos[Nombre] = $Datos[Nombre];
                    $InfoDatos[Descripcion] = $Datos[Descripcion];
                    $InfoDatos[Icono] = DIRECTORIO_ROOT . $Datos[Icono];
                    $InfoDatos[SoloIcono] = $Datos[SoloIcono];

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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosservicioget_seccion_directorio', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_directorio($IDClub, $Tag, $IDSeccion = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($Tag)) :
            $Tag = utf8_decode($Tag);
            $array_buscar = explode(" ", $Tag);
            foreach ($array_buscar as $key => $value) {
                $array_condiciones_nombre[] = " (	Nombre  like '%" . $value . "%' or Descripcion like '%" . $value . "%'  )";
            }
            if (count($array_condiciones_nombre) > 0) {
                $condicion_nombre = implode(" and ", $array_condiciones_nombre);
            }
            $array_condiciones[] = $condicion_nombre;
        endif;

        if (!empty($IDSeccion)) :

            $condicion_categoria = " IDCategoriaDirectorio = $IDSeccion";

            $array_condiciones[] = $condicion_categoria;

        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_directorio = " and " . $condiciones;
        endif;

        if ($IDClub == "36") {
            $condicion_orden = " Orden ";
        } else {
            $condicion_orden = " Nombre ";
        }

        $response = array();
        $sql = "SELECT * FROM Directorio WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones_directorio . " ORDER BY " . $condicion_orden;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $response_otros = array();
                $directorio["IDClub"] = $r["IDClub"];
                $directorio["IDDirectorio"] = $r["IDDirectorio"];
                $directorio["Nombre"] = $r["Nombre"];
                $directorio["Telefono"] = $r["Telefono"];
                $directorio["PermiteCalificacion"] = $r["PermiteCalificar"];
                $directorio["MostrarIcono"] = $r["MostrarIcono"];
                $directorio["MostrarIconoEmail"] = $r["MostrarIconoEmail"];
                $directorio["LabelEmail"] = $r["LabelEmail"];
                $directorio["LabelTelefono"] = $r["LabelTelefono"];
                $directorio["LabelDescripcion"] = $r["LabelDescripcion"];
                $directorio["Whatsapp"] = $r["Whatsapp"];
                $directorio["LabelWhatsapp"] = $r["LabelWhatsapp"];

                if (!empty($r["Foto1"])) :
                    $foto = DIRECTORIO_ROOT . $r["Foto1"];
                else :
                    $foto = "";
                endif;
                //icono whatsapp
                $directorio["Foto"] = $foto;
                if (!empty($r["IconoWhatsapp"])) :
                    $IconoWhatsapp = DIRECTORIO_ROOT . $r["IconoWhatsapp"];
                else :
                    $IconoWhatsapp = "";
                endif;

                $directorio["IconoWhatsapp"] = $IconoWhatsapp;

                $icono_telefono = $dbo->getFields("Club", "IconoTelefono", "IDClub = '" . $IDClub . "'");
                if (!empty($icono_telefono)) :
                    $fototelefono = CLUB_ROOT . $icono_telefono;
                else :
                    $fototelefono = "";
                endif;
                $directorio["IconoTelefono"] = $fototelefono;

                $icono_email = $dbo->getFields("Club", "IconoEmail", "IDClub = '" . $IDClub . "'");
                if (!empty($icono_email)) :
                    $fotomail = CLUB_ROOT . $icono_email;
                else :
                    $fotomail = "";
                endif;
                $directorio["IconoEmail"] = $fotomail;

                //Otros datos
                if (!empty(trim($r["Descripcion"]))) :

                    if ($IDClub == 151)
                        $labeldesc = "Details";


                    if (!empty($r["LabelDescripcion"]))
                        $LabelDescrip = $r["LabelDescripcion"];
                    else
                        $LabelDescrip = "Descripción";

                    $array_otros["Campo"] = $LabelDescrip;
                    $array_otros["Valor"] = $r["Descripcion"];
                    $array_otros["Tipo"] = "texto";
                    array_push($response_otros, $array_otros);
                endif;

                if (trim($r["Email"]) != "") :
                    if (!empty($r["LabelEmail"]))
                        $LabelEmail = $r["LabelEmail"];
                    else
                        $LabelEmail = "Email";

                    $array_otros["Campo"] = $LabelEmail;
                    $array_otros["Valor"] = $r["Email"];
                    $array_otros["Tipo"] = "email";
                    array_push($response_otros, $array_otros);
                endif;

                $sql_otros = "Select * From CampoDirectorioClubValor Where IDDirectorio = '" . $r["IDDirectorio"] . "'";
                $result_otros = $dbo->query($sql_otros);
                while ($row_otros = $dbo->fetchArray($result_otros)) :
                    $array_otros["Campo"] = $dbo->getFields("CampoDirectorioClub", "Nombre", "IDCampoDirectorioClub = '" . $row_otros["IDCampoDirectorioClub"] . "'");
                    $array_otros["Valor"] = $row_otros["Valor"];
                    $array_otros["Tipo"] = $dbo->getFields("CampoDirectorioClub", "TipoCampo", "IDCampoDirectorioClub = '" . $row_otros["IDCampoDirectorioClub"] . "'");
                    array_push($response_otros, $array_otros);
                endwhile;

                $directorio["OtrosDatos"] = $response_otros;

                array_push($response, $directorio);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_directorio_socio($IDClub, $Tag, $IDSeccion = "")
    {
        $dbo = &SIMDB::get();

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%' )";
        endif;

        if (!empty($IDSeccion)) :
            $condicion_categoria = " IDCategoriaDirectorioSocio = $IDSeccion";
            $array_condiciones[] = $condicion_categoria;
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_directorio = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM DirectorioSocio WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones_directorio . " ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $response_otros = array();
                $directorio["IDClub"] = $r["IDClub"];
                $directorio["IDDirectorioSocio"] = $r["IDDirectorioSocio"];
                $directorio["Nombre"] = $r["Nombre"];
                $directorio["Telefono"] = $r["Telefono"];
                $directorio["PermiteCalificacion"] = $r["PermiteCalificar"];
                $directorio["MostrarIcono"] = $r["MostrarIcono"];
                $directorio["MostrarIconoEmail"] = $r["MostrarIconoEmail"];
                $directorio["LabelEmail"] = $r["LabelEmail"];
                $directorio["LabelTelefono"] = $r["LabelTelefono"];
                $directorio["LabelDescripcion"] = $r["LabelDescripcion"];
                $directorio["Whatsapp"] = $r["Whatsapp"];
                $directorio["LabelWhatsapp"] = $r["LabelWhatsapp"];

                if (!empty($r["Foto1"])) :
                    $foto = DIRECTORIO_ROOT . $r["Foto1"];
                else :
                    $foto = "";
                endif;
                $directorio["Foto"] = $foto;

                if (!empty($r["IconoWhatsapp"])) :
                    $IconoWhatsapp = DIRECTORIO_ROOT . $r["IconoWhatsapp"];
                else :
                    $IconoWhatsapp = "";
                endif;

                $directorio["IconoWhatsapp"] = $IconoWhatsapp;

                $icono_telefono = $dbo->getFields("Club", "IconoTelefono", "IDClub = '" . $IDClub . "'");
                if (!empty($icono_telefono)) :
                    $fototelefono = CLUB_ROOT . $icono_telefono;
                else :
                    $fototelefono = "";
                endif;
                $directorio["IconoTelefono"] = $fototelefono;

                $icono_mail = $dbo->getFields("Club", "IconoEmail", "IDClub = '" . $IDClub . "'");
                if (!empty($icono_mail)) :
                    $fotomail = CLUB_ROOT . $icono_mail;
                else :
                    $fotomail = "";
                endif;
                $directorio["IconoEmail"] = $fotomail;

                //Otros datos
                if (!empty(trim($r["Descripcion"]))) :
                    if (!empty($r["LabelDescripcion"]))
                        $LabelDescrip = $r["LabelDescripcion"];
                    else
                        $LabelDescrip = "Descripción";

                    $array_otros["Campo"] = $LabelDescrip;
                    $array_otros["Valor"] = $r["Descripcion"];
                    $array_otros["Tipo"] = "texto";
                    array_push($response_otros, $array_otros);
                endif;

                if (trim($r["Email"]) != "") :
                    if (!empty($r["LabelEmail"]))
                        $LabelEmail = $r["LabelEmail"];
                    else
                        $LabelEmail = "Email";

                    $array_otros["Campo"] = $LabelEmail;
                    $array_otros["Valor"] = $r["Email"];
                    array_push($response_otros, $array_otros);
                endif;

                $sql_otros = "Select * From CampoDirectorioSocioValor Where IDDirectorio = '" . $r["IDDirectorioSocio"] . "'";
                $result_otros = $dbo->query($sql_otros);
                while ($row_otros = $dbo->fetchArray($result_otros)) :
                    $array_otros["Campo"] = $dbo->getFields("CampoDirectorioSocio", "Nombre", "IDCampoDirectorioSocio = '" . $row_otros["IDCampoDirectorioSocio"] . "'");
                    $array_otros["Valor"] = $row_otros["Valor"];
                    $array_otros["Tipo"] = $dbo->getFields("CampoDirectorioSocio", "TipoCampo", "IDCampoDirectorioSocio = '" . $row_otros["IDCampoDirectorioSocio"] . "'");
                    array_push($response_otros, $array_otros);
                endwhile;

                $directorio["OtrosDatos"] = $response_otros;

                array_push($response, $directorio);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_categoria_directorio($IDClub, $IDCategoria = "", $Tag = "")
    {

        $dbo = &SIMDB::get();

        // Seccion Especifica
        if (!empty($IDCategoria)) :
            $array_condiciones[] = " IDCategoriaDirectorio  = '" . $IDCategoria . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_producto = " and " . $condiciones;
        endif;

        $response = array();
        $response_lista_producto = array();
        $sql = "SELECT * FROM CategoriaDirectorio WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Nombre ASC ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoria_directorio["IDClub"] = $r["IDClub"];
                $categoria_directorio["IDCategoriaDirectorio"] = $r["IDCategoriaDirectorio"];
                $categoria_directorio["NombreCategoria"] = $r["Nombre"];
                $categoria_directorio["DescripcionCategoria"] = $r["Descripcion"];
                //Busco los registros de la categoria
                $response_detalle_directorio = array();

                $sql_directorio = "Select * From Directorio Where IDCategoriaDirectorio = '" . $r["IDCategoriaDirectorio"] . "' and Publicar = 'S' Order by Nombre ";
                $result_directorio = $dbo->query($sql_directorio);
                while ($row_directorio = $dbo->fetchArray($result_directorio)) :
                    $response_otros = array();
                    $array_otros = array();
                    $directorio["IDClub"] = $row_directorio["IDClub"];
                    $directorio["IDDirectorio"] = $row_directorio["IDDirectorio"];
                    $directorio["Nombre"] = $row_directorio["Nombre"];
                    $directorio["Telefono"] = $row_directorio["Telefono"];

                    $directorio["PermiteCalificacion"] = $row_directorio["PermiteCalificar"];
                    if (!empty($row_directorio["Foto1"])) :
                        $foto = DIRECTORIO_ROOT . $row_directorio["Foto1"];
                    else :
                        $foto = "";
                    endif;
                    $directorio["Foto"] = $foto;

                    //Otros datos
                    if (!empty(trim($row_directorio["Descripcion"]))) :
                        $array_otros["Campo"] = "Descripción";
                        $array_otros["Valor"] = $row_directorio["Descripcion"];
                        $array_otros["Tipo"] = "texto";
                        array_push($response_otros, $array_otros);
                    endif;

                    if (trim($row_directorio["Email"]) != "") :
                        $array_otros["Campo"] = "Email";
                        $array_otros["Valor"] = $row_directorio["Email"];
                        array_push($response_otros, $array_otros);
                    endif;

                    $sql_otros = "Select * From CampoDirectorioClubValor Where IDDirectorio = '" . $row_directorio["IDDirectorio"] . "'";
                    $result_otros = $dbo->query($sql_otros);
                    while ($row_otros = $dbo->fetchArray($result_otros)) :
                        $array_otros["Campo"] = $dbo->getFields("CampoDirectorioClub", "Nombre", "IDCampoDirectorioClub = '" . $row_otros["IDCampoDirectorioClub"] . "'");
                        $array_otros["Valor"] = $row_otros["Valor"];
                        $array_otros["Tipo"] = $dbo->getFields("CampoDirectorioClub", "TipoCmpo", "IDCampoDirectorioClub = '" . $row_otros["IDCampoDirectorioClub"] . "'");
                        array_push($response_otros, $array_otros);
                    endwhile;

                    $directorio["OtrosDatos"] = $response_otros;
                    array_push($response_detalle_directorio, $directorio);
                endwhile;

                $categoria_directorio["Registros"] = $response_detalle_directorio;
                array_push($response, $categoria_directorio);
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

    public function get_categoria_directorio_socio($IDClub, $IDCategoria = "", $Tag = "")
    {

        $dbo = &SIMDB::get();

        // Seccion Especifica
        if (!empty($IDCategoria)) :
            $array_condiciones[] = " IDCategoriaDirectorioSocio  = '" . $IDCategoria . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_producto = " and " . $condiciones;
        endif;

        $response = array();
        $response_lista_producto = array();
        $sql = "SELECT * FROM CategoriaDirectorioSocio WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoria_directorio["IDClub"] = $r["IDClub"];
                $categoria_directorio["IDCategoriaDirectorioSocio"] = $r["IDCategoriaDirectorioSocio"];
                $categoria_directorio["NombreCategoria"] = $r["Nombre"];
                $categoria_directorio["DescripcionCategoria"] = $r["Descripcion"];
                //Busco los registros de la categoria
                $response_detalle_directorio = array();

                $sql_directorio = "Select * From DirectorioSocio Where IDCategoriaDirectorioSocio = '" . $r["IDCategoriaDirectorioSocio"] . "' and Publicar = 'S'";
                $result_directorio = $dbo->query($sql_directorio);
                while ($row_directorio = $dbo->fetchArray($result_directorio)) :
                    $array_otros = array();
                    $response_otros = array();

                    $directorio["IDClub"] = $row_directorio["IDClub"];
                    $directorio["IDDirectorioSocio"] = $row_directorio["IDDirectorioSocio"];
                    $directorio["Nombre"] = $row_directorio["Nombre"];
                    $directorio["Telefono"] = $row_directorio["Telefono"];
                    $directorio["PermiteCalificacion"] = $row_directorio["PermiteCalificar"];
                    if (!empty($row_directorio["Foto1"])) :
                        $foto = DIRECTORIO_ROOT . $row_directorio["Foto1"];
                    else :
                        $foto = "";
                    endif;
                    $directorio["Foto"] = $foto;

                    //Otros datos
                    if (!empty(trim($row_directorio["Descripcion"]))) :
                        $array_otros["Campo"] = "Descripción";
                        $array_otros["Valor"] = $row_directorio["Descripcion"];
                        $array_otros["Tipo"] = "texto";
                        array_push($response_otros, $array_otros);
                    endif;

                    if (trim($row_directorio["Email"]) != "") :
                        $array_otros["Campo"] = "Email";
                        $array_otros["Valor"] = $row_directorio["Email"];
                        array_push($response_otros, $array_otros);
                    endif;

                    $sql_otros = "Select * From CampoDirectorioSocioValor Where IDDirectorioSocio = '" . $row_directorio["IDDirectorioSocio"] . "'";
                    $result_otros = $dbo->query($sql_otros);
                    while ($row_otros = $dbo->fetchArray($result_otros)) :
                        $array_otros["Campo"] = $dbo->getFields("CampoDirectorioSocio", "Nombre", "IDCampoDirectorioSocio = '" . $row_otros["IDCampoDirectorioSocio"] . "'");
                        $array_otros["Valor"] = $row_otros["Valor"];
                        $array_otros["Tipo"] = $dbo->getFields("CampoDirectorioSocio", "TipoCampo", "IDCampoDirectorioSocio = '" . $row_otros["IDCampoDirectorioSocio"] . "'");
                        array_push($response_otros, $array_otros);
                    endwhile;

                    $directorio["OtrosDatos"] = $response_otros;

                    array_push($response_detalle_directorio, $directorio);
                endwhile;

                $categoria_directorio["Registros"] = $response_detalle_directorio;
                array_push($response, $categoria_directorio);
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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "131. " .  SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
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
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
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
}
