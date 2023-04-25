<?php

    class SIMWebServiceRuta
    {
        public function get_rutas($IDClub, $IDSocio, $IDUsuario, $Tag)
        {
            $dbo = &SIMDB::get();

            // Tag
            if (!empty($Tag)) :
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
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
    }