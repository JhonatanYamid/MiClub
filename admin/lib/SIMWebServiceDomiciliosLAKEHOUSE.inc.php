<?php

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
//require LIBDIR  . "SIMWebServiceZeus.inc.php";
class SIMWebServiceDomicilios
{
    public function get_restaurante($IDClub, $IDModulo = "")
    {

        if (!empty($IDModulo)) {
            $condicion = " AND IDModulo='" . $IDModulo . "'";
        } else {
            $condicion = " AND IDModulo='0'";
        }
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM Restaurante WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" .  $condicion . " ORDER BY Orden ASC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $restaurante["IDClub"] = $r["IDClub"];
                $restaurante["IDModulo"] = $r["IDModulo"];
                $restaurante["IDRestaurante"] = $r["IDRestaurante"];
                $restaurante["Nombre"] = $r["Nombre"];
                $restaurante["Lugar"] = $r["Lugar"];
                $restaurante["Menu"] = nl2br($r["Menu"]);
                $restaurante["Horario"] = $r["Horario"];
                $restaurante["Localizacion"] = $r["Localizacion"];

                if (!empty($r["RestauranteFile"])) :
                    $foto1 = IMGEVENTO_ROOT . $r["RestauranteFile"];
                else :
                    $foto1 = "";
                endif;

                $restaurante["Foto"] = $foto1;

                if (!empty($r["RestauranteIcono"])) :
                    $fotoicono = IMGEVENTO_ROOT . $r["RestauranteIcono"];
                else :
                    $fotoicono = "";
                endif;

                $restaurante["Icono"] = $fotoicono;

                //Para la carta

                if (!empty($r["CartaFile"])) :
                    $foto1 = IMGEVENTO_ROOT . $r["CartaFile"];
                else :
                    $foto1 = "";
                endif;
                $restaurante["FotoCarta"] = "";

                $fotos_carta = array();
                $fotos_carta[] = $foto1;

                //Para la carta
                if (!empty($r["CartaFile2"])) :
                    $foto2 = IMGEVENTO_ROOT . $r["CartaFile2"];
                    $fotos_carta[] = $foto2;
                else :
                    $foto2 = "";
                endif;
                //$restaurante["FotoCarta2"] = $foto2;

                //Para la carta
                if (!empty($r["CartaFile3"])) :
                    $foto3 = IMGEVENTO_ROOT . $r["CartaFile3"];
                    $fotos_carta[] = $foto3;
                else :
                    $foto3 = "";
                endif;
                //$restaurante["FotoCarta3"] = $foto3;

                //Para la carta
                if (!empty($r["CartaFile4"])) :
                    $foto4 = IMGEVENTO_ROOT . $r["CartaFile4"];
                    $fotos_carta[] = $foto4;
                else :
                    $foto4 = "";
                endif;
                //$restaurante["FotoCarta4"] = $foto4;

                //Para la carta
                if (!empty($r["CartaFile5"])) :
                    $foto5 = IMGEVENTO_ROOT . $r["CartaFile5"];
                    $fotos_carta[] = $foto5;
                else :
                    $foto5 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile6"])) :
                    $foto6 = IMGEVENTO_ROOT . $r["CartaFile6"];
                    $fotos_carta[] = $foto6;
                else :
                    $foto6 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile7"])) :
                    $foto7 = IMGEVENTO_ROOT . $r["CartaFile7"];
                    $fotos_carta[] = $foto7;
                else :
                    $foto7 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile8"])) :
                    $foto8 = IMGEVENTO_ROOT . $r["CartaFile8"];
                    $fotos_carta[] = $foto8;
                else :
                    $foto8 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile9"])) :
                    $foto9 = IMGEVENTO_ROOT . $r["CartaFile9"];
                    $fotos_carta[] = $foto9;
                else :
                    $foto9 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile10"])) :
                    $foto10 = IMGEVENTO_ROOT . $r["CartaFile10"];
                    $fotos_carta[] = $foto10;
                else :
                    $foto10 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile11"])) :
                    $foto11 = IMGEVENTO_ROOT . $r["CartaFile11"];
                    $fotos_carta[] = $foto11;
                else :
                    $fot11 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile12"])) :
                    $foto12 = IMGEVENTO_ROOT . $r["CartaFile12"];
                    $fotos_carta[] = $foto12;
                else :
                    $foto12 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile13"])) :
                    $foto13 = IMGEVENTO_ROOT . $r["CartaFile13"];
                    $fotos_carta[] = $foto13;
                else :
                    $foto13 = "";
                endif;

                //Para la carta
                if (!empty($r["CartaFile14"])) :
                    $foto14 = IMGEVENTO_ROOT . $r["CartaFile14"];
                    $fotos_carta[] = $foto14;
                else :
                    $foto14 = "";
                endif;

                $restaurante["fotoscarta"] = $fotos_carta;

                array_push($response, $restaurante);
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

    //NO ESTA FUNCIONANDO
    public function get_producto($IDClub, $IDProducto = "", $Tag = "", $Version = "")
    {

        $dbo = &SIMDB::get();

        // Seccion Especifica
        if (!empty($IDProducto)) :
            $array_condiciones[] = " IDProducto  = '" . $IDProducto . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
        endif;

        $Fecha = date("Y-m-d");
        $dia_fecha = date("w");

        $array_condiciones[] = " Dias like '%" . $dia_fecha . "|%' ";

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_producto = " and " . $condiciones;
        endif;

        $response = array();
        $response_lista_producto = array();
        $sql = "SELECT * FROM Producto" . $Version . " WHERE Publicar = 'S' and Existencias > 0  and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

            $mostrar_fecha = $dbo->getFields("Club", "SolicitaFechaDomicilio", "IDClub = '" . $IDClub . "'");
            if ($mostrar_fecha == "S") :
                $producto["setearfecha"] = true;
            else :
                $producto["setearfecha"] = false;
            endif;

            $mostrar_hora = $dbo->getFields("Club", "SolicitaHoraDomicilio", "IDClub = '" . $IDClub . "'");
            if ($mostrar_hora == "S") :
                $producto["setearhora"] = true;
            else :
                $producto["setearhora"] = false;
            endif;

            $mostrar_direccion = $dbo->getFields("Club", "SolicitaDireccionDomicilio", "IDClub = '" . $IDClub . "'");
            if ($mostrar_direccion == "S") :
                $producto["seteardireccion"] = true;
            else :
                $producto["seteardireccion"] = false;
            endif;

            while ($r = $dbo->fetchArray($qry)) {

                $lista_producto["IDClub"] = $r["IDClub"];
                $lista_producto["IDProducto"] = $r["IDProducto"];
                $lista_producto["Nombre"] = utf8_encode($r["Nombre"]);
                $lista_producto["Descripcion"] = utf8_encode($r["Descripcion"]);
                $lista_producto["Precio"] = $r["Precio"];
                $lista_producto["PermiteComentarios"] = $r["PermiteComentarios"];

                if (!empty($r["Foto1"])) :
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

                $horaActual = strtotime(date("H:m:s"));
                $horaInicio = strtotime($r[HoraInicioDisponible]);
                $horaFin = strtotime($r[HoraFinDisponible]);

                if ($horaActual > $horaInicio && $horaActual < $horaFin)
                    array_push($response_lista_producto, $lista_producto);
            } //ednw hile

            $producto["Productos"] = $response_lista_producto;
            array_push($response, $producto);

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

    public function get_producto_categoria($IDClub, $IDCategoria = "", $Tag = "", $Version = "", $IDRestaurante = "", $TipoApp, $IDSocio = "", $IDUSuario = "")
    {

        $dbo = &SIMDB::get();


        /* if ($TipoApp == "Empleado") :
            $IDUsuario = 111111111;
        else :
            $IDSocio = 11111111;
        endif; */

        $respuesta_dispo = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha = "", $Version, $IDRestaurante, $IDUsuario);
        if (!$respuesta_dispo["success"]) {
            $respuesta["message"] = $respuesta_dispo["message"];
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        // Seccion Especifica
        if (!empty($IDCategoria)) :
            $array_condiciones[] = " IDCategoriaProducto  = '" . $IDCategoria . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
        endif;

        if (!empty($IDRestaurante)) :
            $array_condiciones[] = " (IDRestauranteDomicilio  = '" . $IDRestaurante . "' or IDRestauranteDomicilio = '')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_producto = " and " . $condiciones;
        endif;

        $response = array();
        $response_lista_producto = array();
        $response_detalle_producto = array();


        //CLUB LAKE HOUSE
        if ($IDClub == 233 ||  $IDClub == 8) {
            //BUSCO EL CODIGO DE AMBIENTE
            $datos_ambientes = $dbo->fetchAll("RestauranteDomicilio", " IDRestauranteDomicilio = '" . $IDRestaurante . "' ", "array");
            $datos_ambiente = $datos_ambientes["CodigoExternoAmbienteZeus"];

            //Base Produccion
            $urlendpoint = "http://200.1.126.78:1080/wsZeusGenerico/ServiceWS.asmx?WSDL";
            $usuariuozeus = "userpos2"; // WSPos1
            $clavezeus = "userpos2022";

            $Token = SIMWebServiceZeus::obtener_token_club_curl($urlendpoint, $usuariuozeus, $clavezeus);

            $datos_productos_agrupacion = SIMWebServiceZeus::POS_ConsultarProductosAgrupacion($urlendpoint, $Token, $datos_ambiente);

            $categoria_producto["setearfecha"] = true;
            $categoria_producto["setearhora"] = true;

            //CATEGORIAS O AGRUPACIONES DE LOS PRODUCTOS
            foreach ($datos_productos_agrupacion as $info_productos) :
                foreach ($info_productos->Agrupacion as $info_producto) :

                    $categoria_producto["IDClub"] = 233;
                    $categoria_producto["IDCategoriaProducto"] = "$info_producto->Codigo";
                    $categoria_producto["NombreCategoria"] = "$info_producto->Descripcion";
                    $categoria_producto["DescripcionCategoria"] = "$info_producto->Descripcion";
                    $categoria_producto["ComentarioCategoria"] = "";

                    $codigo_agrupacion = "$info_producto->Codigo";
                    $nombre_agrupacion = "$info_producto->Descripcion";

                    //Busco los productos
                    $response_detalle_producto = array();
                    $datos_productos = SIMWebServiceZeus::POS_ConsultarProductos($urlendpoint, $Token, $datos_ambiente, $codigo_agrupacion);

                    foreach ($datos_productos as $productos_todos) {

                        foreach ($productos_todos->Producto as $info_producto) {

                            //elijo solo los que tienen precio, por que aun no sirve el servicio de disponibilidad
                            if ($info_producto->Valor != 0) :

                                $producto["IDProducto"] = "$info_producto->Codigo" . "|" . "$info_producto->Descripcion" . "|" . "$codigo_agrupacion" . "|" . "$nombre_agrupacion" . "|" . "$datos_ambiente";
                                $producto["Nombre"] = "$info_producto->Descripcion";
                                $producto["Descripcion"] = "$info_producto->Descripcion";
                                $producto["Precio"] = (string)$info_producto->Valor;


                                //PRECIO PARA SOCIOS TIPO NO AFILIADOS LAKE HOUSE (CONFIGURAR ESTA PARTE)
                                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");


                                $producto["OcultarMostrarCantidad"] = "";
                                $producto["PermiteComentarios"] = "";

                                $foto = "";
                                $producto["Foto"] = (string)$foto;

                                //VARIAS FOTOS
                                $fotos = array();
                                $producto["Fotos"] = $fotos;

                                $response_valores_carac = array();
                                $response_carac_producto = array();
                                $categoria_carac["IDCaracteristica"] = "";
                                $categoria_carac["TipoCampo"] =  "";
                                $categoria_carac["EtiquetaCampo"] =  "";
                                $categoria_carac["Obligatorio"] =  "";
                                $categoria_carac["CantidadMaximaSeleccion"] =  "";


                                $valores["OcultarPrecioEnCero"] =  "";
                                $valores["IDCaracteristicaValor"] =  "";
                                $valores["Opcion"] =  "";
                                $valores["Precio"] =  "";
                                array_push($response_valores_carac, $valores);
                                $categoria_carac["Valores"] = "";

                                if (count($response_valores_carac) > 0) {
                                    array_push($response_carac_producto, $categoria_carac);
                                }
                                $caracteristicas = array();
                                $producto["Caracteristicas"] = $caracteristicas;

                                //FIN caracteristicas


                                array_push($response_detalle_producto, $producto);




                            endif;
                        }
                    }



                    $codigo_agrupacion = "";
                    $nombre_agrupacion = "";


                    $categoria_producto["Productos"] = $response_detalle_producto;
                    array_push($response, $categoria_producto);

                endforeach;
            endforeach;




            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {


            //SI ES PARA DEMAS CLUBES 

            $sql = "SELECT * FROM CategoriaProducto" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_producto . " ORDER BY Orden ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);;

                //$mostrar_fecha = $dbo->getFields( "Club" , "SolicitaFechaDomicilio" , "IDClub = '".$IDClub."'" );
                $mostrar_fecha = $respuesta_dispo["response"][0]["SolicitaFechaDomicilio"];
                if ($mostrar_fecha == "S") :
                    $categoria_producto["setearfecha"] = true;
                else :
                    $categoria_producto["setearfecha"] = false;
                endif;

                //$mostrar_hora = $dbo->getFields( "Club" , "SolicitaHoraDomicilio" , "IDClub = '".$IDClub."'" );
                $mostrar_hora = $respuesta_dispo["response"][0]["SolicitaHoraDomicilio"];
                if ($mostrar_hora == "S") :
                    $categoria_producto["setearhora"] = true;
                else :
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
                    while ($row_producto = $dbo->fetchArray($result_productos)) :

                        $datos_producto = $dbo->fetchAll("Producto" . $Version, " IDProducto = '" . $row_producto["IDProducto"] . "' ", "array");
                        if ($datos_producto["Publicar"] == "S" && $datos_producto["Existencias"] > 0) :
                            $producto["IDProducto"] = $datos_producto["IDProducto"];
                            $producto["Nombre"] = $datos_producto["Nombre"];
                            $producto["Descripcion"] = $datos_producto["Descripcion"];
                            $producto["Precio"] = $datos_producto["Precio"] + $datos_producto["PrecioEmpaque"];
                            /*   echo "IDSocio:" . $IDSocio; */

                            //PRECIO PARA SOCIOS TIPO NO AFILIADOS LAKE HOUSE
                            $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");

                            if ($TipoSocio == "No Afiliado") {
                                $producto["Precio"] = $datos_producto["PrecioNoAfiliado"];
                            }

                            //PRECIO PARA LOS USUARIOS
                            if ($TipoApp == "Empleado" && $datos_producto["PrecioUsuario"] > 0) {
                                $producto["Precio"] = $datos_producto["PrecioUsuario"];
                            }
                            $producto["Nombre"] = $datos_producto["Nombre"];
                            $producto["OcultarMostrarCantidad"] = $datos_producto["OcultarMostrarCantidad"];
                            $producto["PermiteComentarios"] = $datos_producto["PermiteComentarios"];

                            if (!empty($datos_producto["Foto1"])) :
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



                            //VARIAS FOTOS
                            $response_fotos = array();
                            for ($i_foto = 1; $i_foto <= 6; $i_foto++) :

                                $campo_foto = "Foto" . $i_foto;
                                if (!empty($datos_producto[$campo_foto])) :
                                    $array_dato_foto["Foto"] = IMGPRODUCTO_ROOT . $datos_producto[$campo_foto];
                                    array_push($response_fotos, $array_dato_foto);
                                endif;
                            endfor;
                            $producto["Fotos"] = $response_fotos;


                            //Caracteristicas
                            $sql_producto_carac = "SELECT PP.IDPropiedadProducto,PP.Nombre as Categoria, PP.Tipo, PP.Obligatorio, PP.MaximoPermitido,CP.Nombre as NombreValor, CP.Valor as Precio, CP.IDCaracteristicaProducto, CP.OcultarPrecioEnCero as OcultarPrecio
                                                                                            FROM ProductoCaracteristica PC, CaracteristicaProducto CP, PropiedadProducto PP
                                                                                            WHERE PC.IDCaracteristicaProducto=CP.IDCaracteristicaProducto And
                                                                                                        CP.IDPropiedadProducto = PP.IDPropiedadProducto And
                                                                                                        CP.IDClub = '" . $IDClub . "' and PC.IDProducto = '" . $datos_producto["IDProducto"] . "' 
                                                                                            ORDER BY IDPropiedadProducto ";

                            if ($datos_producto["IDProducto"] == 379) {
                                //print_r($sql_producto_carac);
                            }

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

                                $valores["OcultarPrecioEnCero"] = $row_prod_carac["OcultarPrecio"];
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
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }


        return $respuesta;
    } // fin function

    public function get_domicilio_socio($IDClub, $IDSocio, $Version = "", $IDRestaurante, $IDUsuario)
    {

        $dbo = &SIMDB::get();

        if (!empty($IDRestaurante)) {
            $condicion = " and IDRestauranteDomicilio='" . $IDRestaurante . "'";
        }

        if ($IDClub == 70) {
            $fecha = date("Y-m-d");

            $year = date('Y', strtotime($fecha));
            $mes = date('m', strtotime($fecha));

            $condicion .= " AND MONTH(HoraEntrega) = '" . $mes . "' AND YEAR(HoraEntrega) = '" . $year . "'";
        }

        $response = array();
        $response_detalle_domicilio = array();
        $sql = "SELECT * FROM Domicilio" . $Version . " WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'" . " and IDEstadoDomicilio <> 3  and HoraEntrega >= '" . date("Y-m-d 00:00:00") . "'" . $condicion . " ORDER BY FechaTrCr Desc Limit 3";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
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
                $detalle_pedido = SIMWebServiceDomicilios::get_domicilio_detalle($IDClub, $r["IDDomicilio"], $r["IDSocio"], (int) $r["ValorDomicilio"], $Version);
                $domicilio["Productos"] = $detalle_pedido["response"];

                //if ($IDClub != 25 && $r["IDEstadoDomicilio"] != 8) {
                array_push($response, $domicilio);
                //}

                if ($IDClub == 25 && $r["IDEstadoDomicilio"] == 8) {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
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

    public function get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio, $ValorDomicilio = "", $Version = "")
    {

        $dbo = &SIMDB::get();

        $response = array();
        //Detalle domicilio para lake house desde zeus
        if ($IDClub == 233 ||  $IDClub == 8) {



            $sql = "SELECT * FROM DomicilioDetalle" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {



                    $id_domiclio = $r["IDDomicilio"];
                    $domicilio_detalle["IDDomicilio"] = $r["IDDomicilio"];
                    $domicilio_detalle["IDProducto"] = $r["IDProducto"];
                    $domicilio_detalle["Producto"] = $r["NombreProducto"];
                    $domicilio_detalle["Comentario"] = utf8_encode($r["Comentario"]);

                    $foto_prod = $dbo->getFields("Producto" . $Version, "Foto1", "IDProducto = '" . $r["IDProducto"] . "'");
                    if (!empty($foto_prod)) :
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

                    // echo $sql_carac;
                    $caracteristicas = "";
                    $r_carac = $dbo->query($sql_carac);
                    while ($row_carac = $dbo->FetchArray($r_carac)) {
                        $caracteristicas .= $row_carac["Categoria"] . " : " . $row_carac["Caracteristica"];
                    }

                    $domicilio_detalle["Categorias"] = $caracteristicas;

                    array_push($response, $domicilio_detalle);
                } //ednw hile


                if ((int) $ValorDomicilio > 0) :
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
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else


        } else { //PARA LOS DEMAS CLUBES
            $sql = "SELECT * FROM DomicilioDetalle" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $id_domiclio = $r["IDDomicilio"];
                    $domicilio_detalle["IDDomicilio"] = $r["IDDomicilio"];
                    $domicilio_detalle["IDProducto"] = $r["IDProducto"];
                    $domicilio_detalle["Producto"] = $dbo->getFields("Producto" . $Version, "Nombre", "IDProducto = '" . $r["IDProducto"] . "'");
                    $domicilio_detalle["Comentario"] = utf8_encode($r["Comentario"]);

                    $foto_prod = $dbo->getFields("Producto" . $Version, "Foto1", "IDProducto = '" . $r["IDProducto"] . "'");
                    if (!empty($foto_prod)) :
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

                    // echo $sql_carac;
                    $caracteristicas = "";
                    $r_carac = $dbo->query($sql_carac);
                    while ($row_carac = $dbo->FetchArray($r_carac)) {
                        $caracteristicas .= $row_carac["Categoria"] . " : " . $row_carac["Caracteristica"];
                    }

                    $domicilio_detalle["Categorias"] = $caracteristicas;

                    array_push($response, $domicilio_detalle);
                } //ednw hile

                if ((int) $ValorDomicilio > 0) :
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
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

        } //End else


        return $respuesta;
    } // fin function

    public function set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion = "", $ValorDomicilio = "", $FormaPago = "", $Version = "", $IDRestaurante, $NumeroMesa = "", $CamposFormulario = "", $Propina = "", $IDUsuario = "", $Qr = "")
    {
        $dbo = &SIMDB::get();



        //VARIABLE QUE GUARDA EL NOMBRE DEL PRODUCTO DESDE EL SERVICIO DE ZEUS
        $NombreProducto = "";
        if ($IDClub == 125) :
            date_default_timezone_set('America/Montevideo');
        endif;

        if ($IDUsuario > 0) :
            $IDSocio = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");
        else :
            //verifico que el socio exista y pertenezca al club
            $IDSocio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
        endif;

        $Fecha = date("Y-m-d");
        $Hora = date("H:i:s");


        $resp_config = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha, $Version, $IDRestaurante, $IDUsuario);
        $PedidoMinimo = (int) $resp_config["response"][0]["PedidoMinimo"];
        $porcentaje_propina = (float) $resp_config["response"][0]["PorcentajePropina"];
        $datos_config_domicilio['MensajeConfirmacion'] = $resp_config["response"][0]["MensajeConfirmacion"];

        $NumeroPedidosEnTiempo = $resp_config["response"][0]["NumeroPedidosEnTiempo"];
        $CantidadMaximaProductoPorTiempo = $resp_config["response"][0]["CantidadMaximaProductoPorTiempo"];
        $TiempoValidoParaCantidadPedidos = $resp_config["response"][0]["TiempoValidoParaCantidadPedidos"];
        $SolicitaFechaDomicilio = $resp_config["response"][0]["SolicitaFechaDomicilio"];


        // VALIDAR  SI EN LA CONFIGURACION ESTA EL DIA EN EL CUAL SEVA HACER EL PEDIDO
        if ($SolicitaFechaDomicilio == "N" || empty($SolicitaFechaDomicilio)) {
            $condicion = " and (IDRestauranteDomicilio  = '" . $IDRestaurante . "' or IDRestauranteDomicilio = '')";
            $dia_semana = date('w', strtotime($Fecha));
            $sql_dispo_elemento_gral = "Select IDConfiguracionDomicilios From ConfiguracionDomicilios" . $Version . " Where Dias like '%" . $dia_semana . "|%' and Activo = 'S' and IDClub = '" . $IDClub . "' " . $condicion . " Order By IDConfiguracionDomicilios Desc Limit 1";
            $qry_disponibilidad = $dbo->query($sql_dispo_elemento_gral);
            $r_disponibilidad = $dbo->fetchArray($qry_disponibilidad);

            if (empty($r_disponibilidad)) {
                $respuesta["message"] = "No esta permitido hacer domicilios el dia de hoy.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //PARA METROPOLITAN CLUB SOLO SE HACEN PEDIDOS HASTA EL 21 DE DICIEMBRE
        if ($IDClub == 79) {

            if ($Fecha > '2022-12-21') {
                $respuesta["message"] = "No esta permitido hacer domicilios el dia de hoy.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }


        //PARA LUKER VALIDAMOS UN PEDIDO POR PERSONA AL MES
        if ($IDClub == 95 || $IDClub == 96 || $IDClub == 97) {

            $Mes = date('m');
            $anno = date('Y');

            $Sql_Validar_Pedido = "SELECT IDSocio FROM Domicilio" . $Version . " WHERE MONTH(FechaTrCr)=" . $Mes . " AND YEAR(FechaTrCr)=" . $anno . " AND IDSocio='" . $IDSocio . "' AND IDClub='" . $IDClub . "'";
            $Qry_Validar_Pedido = $dbo->query($Sql_Validar_Pedido);
            $r_pedido = $dbo->fetchArray($Qry_Validar_Pedido);

            if (!empty($r_pedido["IDSocio"])) {
                $respuesta["message"] = "Solo se puede hacer un pedido cada mes.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $DetallePedido = trim(preg_replace('/\s+/', ' ', $DetallePedido));
        $datos_pedido = json_decode($DetallePedido, true);

        if (empty($HoraEntrega)) :
            $HoraEntrega = date("Y-m-d H:i:s");
        else :
            $Guion = strpos($HoraEntrega, "-");
            $HoraValidar = strpos($HoraEntrega, ":");
            if ($Guion == true && $HoraValidar == true) :
                $HoraEntrega = date("Y-m-d H:i:s", strtotime($HoraEntrega));
            elseif ($Guion == true) :
                $HoraEntrega = date("Y-m-d", strtotime($HoraEntrega));
                $HoraEntrega = $HoraEntrega . " " . $Hora;
            elseif ($HoraValidar == true) :
                $HoraEntrega = date("H:i:s", strtotime($HoraEntrega));
                $HoraEntrega = $Fecha . " " . $HoraEntrega;
            endif;
        endif;



        if (!empty($IDSocio) && !empty($HoraEntrega) && count($datos_pedido) > 0) {

            $hoy = date("Y-m-d");
            $GranTotal = 0;
            $array_cant_x_prod = array();
            $array_cant_x_cat = array();

            $TotalCantidadPedido = 0;

            //LAKE HOUSE
            if ($IDClub == 233 ||  $IDClub == 8) :

                //Base Produccion zeus
                $urlendpoint = "http://200.1.126.78:1080/wsZeusGenerico/ServiceWS.asmx?WSDL";
                $usuariuozeus = "userpos2"; // WSPos1
                $clavezeus = "userpos2022";


                $socio_datos = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'", "array");

                $cedula = $socio_datos["NumeroDocumento"];
                $nombre_completo =  $socio_datos["Nombre"] . "" . $socio_datos["Apellido"];
                $Direccion = $socio_datos["Direccion"];
                $Celular = $socio_datos["Celular"];

                $Token = SIMWebServiceZeus::obtener_token_club_curl($urlendpoint, $usuariuozeus, $clavezeus);


                $datos_ambientes = $dbo->fetchAll("RestauranteDomicilio", " IDRestauranteDomicilio = '" . $IDRestaurante . "' ", "array");
                $datos_ambiente = $datos_ambientes["CodigoExternoAmbienteZeus"];

                $datos_productos_agrupacion = SIMWebServiceZeus::POS_ConsultarProductosAgrupacion($urlendpoint, $Token, $datos_ambiente);

                //CATEGORIAS O AGRUPACIONES DE LOS PRODUCTOS
                foreach ($datos_productos_agrupacion as $info_productos) :
                    foreach ($info_productos->Agrupacion as $info_producto) :

                        $codigo_agrupacion = "$info_producto->Codigo";
                    endforeach;
                endforeach;


                $datos_productos = SIMWebServiceZeus::POS_ConsultarProductos($urlendpoint, $Token, $datos_ambiente, $codigo_agrupacion);



                // $Celular="121233"; $Direccion="calle 15"; $cedula="3383822"; $nombre_completo="jorge luis ospino";  

                $CantidadPlatosFuertes = 0;
                //SACAMOS LOS DATOS DE PRECIO TOTAL
                foreach ($datos_pedido as $detalle_datos) :
                    $GranTotal1 += (int) $detalle_datos["Cantidad"] * $detalle_datos["ValorUnitario"];
                    $GranTotal += (int) $detalle_datos["Cantidad"] * $detalle_datos["ValorUnitario"];

                    $porciones = explode("|", $detalle_datos["IDProducto"]);
                    $IDProducto = $porciones[0]; // cod
                    $NombreProducto = $porciones[1]; // nombre prod

                    $codigo_categoria_producto = $porciones[2]; // codigo de la categoria del producto
                    $nombre_categoria = $porciones[3]; // categoria del producto
                    $ambiente_usado = $porciones[4]; // ambiente en el que se hizo el pedido del producto

                    if ($nombre_categoria ==  "FUERTES PRINCIPALES") :
                        $CantidadPlatosFuertes += $detalle_datos["Cantidad"];
                    endif;
                endforeach;


                //PARA LAKEHOUSE SE VALIDA 15 PLATOS EN 30 MINUTOS PERO SOLO PARA LA CATEGORIA PLATOS FUERTES
                if ($nombre_categoria ==  "FUERTES PRINCIPALES") :
                    $SQLCantidadProductos = "SELECT  SUM(Cantidad) as CantidadProductos FROM Domicilio$Version D,DomicilioDetalle$Version DD WHERE D.IDDomicilio=DD.IDDomicilio AND D.IDClub = $IDClub AND D.HoraEntrega ='$HoraEntrega' AND D.IDRestauranteDomicilio='$IDRestaurante'   AND DD.CategoriaZeus='FUERTES PRINCIPALES'";
                    $qry_CantidadProductos = $dbo->query($SQLCantidadProductos);
                    $TotalCantidadYaPedidos = $dbo->fetchArray($qry_CantidadProductos);

                    $TotalCantidadYaPedidos = !empty($TotalCantidadYaPedidos["CantidadProductos"]) ? $TotalCantidadYaPedidos["CantidadProductos"] : 0;
                    $totalACalcular = $TotalCantidadYaPedidos + $CantidadPlatosFuertes;
                    $CantidadMaxima = 15;
                    //if ($totalACalcular > $CantidadMaximaProductoPorTiempo || $TotalCantidadPedido > $CantidadMaximaProductoPorTiempo) :
                    if ($totalACalcular > $CantidadMaxima) :

                        $productosRestantes = (int)$CantidadMaxima - (int)$TotalCantidadYaPedidos;
                        $respuesta["message"] = "Se superó el máximo de productos pedidos en la hora seleccionada,máximo de productos que se pueden escoger en la categoria fuertes principales:" . $productosRestantes;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                endif;



                $CamposFormulario = trim(preg_replace('/\s+/', ' ', $CamposFormulario));
                $array_Campos = json_decode($CamposFormulario, true);

                if (count($array_Campos) > 0) :
                    foreach ($array_Campos as $id_valor_campo => $valor_campo) :

                        if ($valor_campo["IDCampo"] == 18) :

                            if ($valor_campo["Valor"] == "Efectivo") :
                                $FormaPago1 = "01";
                            elseif ($valor_campo["Valor"] == "Pago PSE") :
                                $FormaPago1 = "22";
                            else :
                                $FormaPago1 = "";
                            endif;

                        elseif ($valor_campo["IDCampo"] == 16) :
                            $mesa =  $valor_campo["Valor"];

                        else :

                            $mesero = $valor_campo["Valor"];
                        endif;

                    endforeach;
                endif;

                //VALIDAMOS QUE TENGA O NO LA PROPINA
                if ($Propina == "S") :
                    $Propina = "S";
                else :
                    $Propina = "N";
                endif;

                $POS_CrearPedido = SIMWebServiceZeus::POS_CrearPedido($urlendpoint, $Token, $datos_pedido, $Celular, $Direccion, $cedula, $nombre_completo, $mesa, $mesero, $FormaPago1, $datos_ambiente, $Propina);


                $fact = "";
                $mensaje = "";
                foreach ($POS_CrearPedido->status as $info) {
                    $mensaje = $info->message;
                    $fact = $info->status;
                }
                //si no hubo respuesta entonces no se creo el pedido
                if ($fact != "SUCESS") :
                    $respuesta["message"] = "Lo sentimos, no se pudo crear el pedido " . $mensaje;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

            else :

                foreach ($datos_pedido as $detalle_datos) :

                    $array_cant_x_prod[$detalle_datos["IDProducto"]] += (int)$detalle_datos["Cantidad"];
                    $CantidadMaximaProducto = $resp_config["response"][0]["CantidadMaximaProducto"];

                    $GranTotal += (int) $detalle_datos["Cantidad"] * $detalle_datos["ValorUnitario"];
                    //verifico que hayan en existencias
                    $datos_prod = $dbo->fetchAll("Producto" . $Version, " IDProducto = '" . $detalle_datos["IDProducto"] . "' ", "array");

                    if ($CantidadMaximaProducto > 0) {
                        $sql_cat_pro = "SELECT IDCategoriaProducto FROM ProductoCategoria  WHERE IDProducto = '" . $detalle_datos["IDProducto"] . "' LIMIT 1 ";
                        $r_prod_cat = $dbo->query($sql_cat_pro);
                        $row_prod_cat = $dbo->fetchArray($r_prod_cat);
                        $IDCategoriaProducto = $row_prod_cat["IDCategoriaProducto"];
                        if ((int)$IDCategoriaProducto > 0) {
                            $array_cant_x_cat[$IDCategoriaProducto] += (int)$detalle_datos["Cantidad"];
                        }
                    }

                    if ((int)$CantidadMaximaProducto > 0 && ($detalle_datos["Cantidad"] > (int)$CantidadMaximaProducto || $array_cant_x_prod[$detalle_datos["IDProducto"]] > (int)$CantidadMaximaProducto)) {
                        $respuesta["message"] = "Solo se permite " . $CantidadMaximaProducto . " unidades de: " . $datos_prod["Nombre"] . "(" . $array_cant_x_prod[$detalle_datos["IDProducto"]] . ")";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    if ((int)$CantidadMaximaProducto > 0 && ($array_cant_x_cat[$IDCategoriaProducto] > (int)$CantidadMaximaProducto)) {
                        $respuesta["message"] = "Solo se permite " . $CantidadMaximaProducto . " de unidades por categoria ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }


                    if ($datos_prod["Existencias"] < $array_cant_x_prod[$detalle_datos["IDProducto"]]) {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,nohayexistenciadelproducto', LANG) . ":" . $datos_prod["Nombre"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                        exit;
                    }






                    $TotalExis = $detalle_datos["IDProducto"] + $detalle_datos["Cantidad"];
                    $TotalCantidadPedido += $detalle_datos["Cantidad"];
                endforeach;
            endif;

            // VALIDAMOS CANTIDAD DE PEDIDOS EN DOMICILIOS EN UN TIEMPO
            $SQLDomicilios = "SELECT  IDDomicilio FROM Domicilio$Version WHERE IDClub = $IDClub AND HoraEntrega >= DATE_SUB(NOW(),INTERVAL $TiempoValidoParaCantidadPedidos MINUTE)";
            $QRYDomicilios = $dbo->query($SQLDomicilios);

            if ($NumeroPedidosEnTiempo > 0 && $dbo->rows($QRYDomicilios) >= $NumeroPedidosEnTiempo) :
                $respuesta["message"] = "Se superó el máximo de pedidos en la hora seleccionada, por favor seleccione otra hora de entrega";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

            // VALIDAMOS CANTIDAD DE PRODUCTOS PEDIDOS EN  DOMICILIOS EN UN TIEMPO
            if ($CantidadMaximaProductoPorTiempo > 0) :

                $SQLCantidadProductos = "SELECT  SUM(Cantidad) as CantidadProductos FROM Domicilio$Version D,DomicilioDetalle$Version DD WHERE D.IDDomicilio=DD.IDDomicilio AND D.IDClub = $IDClub AND D.HoraEntrega ='$HoraEntrega'";
                $qry_CantidadProductos = $dbo->query($SQLCantidadProductos);
                $TotalCantidadYaPedidos = $dbo->fetchArray($qry_CantidadProductos);

                $TotalCantidadYaPedidos = !empty($TotalCantidadYaPedidos["CantidadProductos"]) ? $TotalCantidadYaPedidos["CantidadProductos"] : 0;
                $totalACalcular = $TotalCantidadYaPedidos + $TotalCantidadPedido;

                //if ($totalACalcular > $CantidadMaximaProductoPorTiempo || $TotalCantidadPedido > $CantidadMaximaProductoPorTiempo) :
                if ($totalACalcular > $CantidadMaximaProductoPorTiempo) :

                    $productosRestantes = (int)$CantidadMaximaProductoPorTiempo - (int)$TotalCantidadYaPedidos;
                    $respuesta["message"] = "Se superó el máximo de productos pedidos en la hora seleccionada,máximo de productos que se pueden escoger:" . $productosRestantes;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endif;



            // DESOCNTAMOS DEL CUPO DEL EMPLEADO PARA LA PRADERA
            if ($IDUsuario > 0 && $IDClub == 16 && $Version == 2) :
                // CONSULTAMOS EL CUPO QUE TIENE PARA VALIDAR SI LE ALCANZA
                $CupoEmpleado = $dbo->getFields("Usuario", "CupoDomicilio", "IDUsuario = '$IDUsuario'");

                if ($GranTotal > $CupoEmpleado && $CupoEmpleado > 0) :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Notienecuposuficienteparahacereldomicilio', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                else :
                    $NuevoCupo = $CupoEmpleado - $GranTotal;
                    $SQLActualizaCupo = "UPDATE Usuario SET CupoDomicilio = '$NuevoCupo' WHERE IDUsuario = '$IDUsuario'";
                    $dbo->query($SQLActualizaCupo);

                    $otros_mensajes = SIMUtil::get_traduccion('', '', 'Sehadescontadoelcupolequeda', LANG) . ": $NuevoCupo";
                endif;

            endif;

            //PARA LA EMPRESA 3A COMPOSITES LOS PRODUCTOS TIENEN PRECIO EN CERO
            if ($GranTotal <= 0 && $IDClub != 198) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,noselecciononingunacantidad,porfavorverifique,elpedidonofueenviado', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            }

            //PARA LA EMPRESA 3A COMPOSITES LOS PRODUCTOS TIENEN PRECIO EN CERO
            if ($GranTotal < $PedidoMinimo && $IDClub != 198) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elpedidomínimodebeserde', LANG) . " $ " . $PedidoMinimo;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            }

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $PermiteReservar = $datos_socio["PermiteDomicilios"];
            if ($PermiteReservar == "N" && $IDClub != 7 && $IDUsuario <= 0) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Supedidonopuedeserrealizadoporfavorcontactarconelareadecartera', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
                exit;
            }

            if ($datos_socio["IDEstadoSocio"] == 5 && ($FormaPago == 'Cargo a cuenta' /* || $FormaPago == 'Tarjeta' */)) :
                $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            endif;

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
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noesposiblerealizarelpedidodebeser3diasantesdelafechadeentregaporzona', LANG) . ":" . $fechamaxima;
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

            //lo que viene por el qr lo inserto en la mesa
            if (!empty($Qr)) {
                $parametrosQr = explode("-", $Qr);
                $NumeroMesa = $parametrosQr[1];
            }

            //actualizo los datos por los del servicio de zeus para lake house
            if ($IDClub == 233 ||  $IDClub == 8) :
                $GranTotal = $GranTotal1;
                $FormaPago = $FormaPago1;
            endif;
            if ($IDUsuario > 0) :
                $sql_domicilio = $dbo->query("Insert Into Domicilio" . $Version . " (IDClub, IDUsuario, IDEstadoDomicilio, IDRestauranteDomicilio, Numero, Total, HoraEntrega, ComentariosSocio, Celular, Direccion, ValorDomicilio, FormaPago, NumeroMesa, Propina, UsuarioTrCr, FechaTrCr)
                    Values ('" . $IDClub . "','" . $IDSocio . "','1','" . $IDRestaurante . "','" . $siguiente_consecutivo . "','" . $GranTotal . "', '" . $HoraEntrega . "','" . $ComentariosSocio . "','" . $Celular . "','" . $Direccion . "','" . $ValorDomicilio . "','" . $FormaPago . "','" . $NumeroMesa . "','" . $Propina . "','App',NOW())");
            else :
                $sql_domicilio = $dbo->query("Insert Into Domicilio" . $Version . " (IDClub, IDSocio, IDEstadoDomicilio, IDRestauranteDomicilio, Numero, Total, HoraEntrega, ComentariosSocio, Celular, Direccion, ValorDomicilio, FormaPago, NumeroMesa, Propina, UsuarioTrCr, FechaTrCr)
                                        Values ('" . $IDClub . "','" . $IDSocio . "','1','" . $IDRestaurante . "','" . $siguiente_consecutivo . "','" . $GranTotal . "', '" . $HoraEntrega . "','" . $ComentariosSocio . "','" . $Celular . "','" . $Direccion . "','" . $ValorDomicilio . "','" . $FormaPago . "','" . $NumeroMesa . "','" . $Propina . "','App',NOW())");
            endif;

            $id_domicilio = $dbo->lastID();

            $CamposFormulario = trim(preg_replace('/\s+/', ' ', $CamposFormulario));
            $array_Campos = json_decode($CamposFormulario, true);

            if (count($array_Campos) > 0) :
                foreach ($array_Campos as $id_valor_campo => $valor_campo) :
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
                    echo $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimosnohayconexionalabase', LANG);
                    exit;
                }
            }

            //Declaramos variables para que no haya errores en caso que la accion la haga otro club
            $codigo_categoria_producto = ""; // codigo de la categoria del producto
            $nombre_categoria = ""; // categoria del producto
            $ambiente_usado = ""; // ambiente en el que se hizo el pedido del producto

            foreach ($datos_pedido as $detalle_datos) :
                $IDProducto = $detalle_datos["IDProducto"];
                $Cantidad = $detalle_datos["Cantidad"];
                $Comentario = $detalle_datos["Comentario"];

                //separamos los datos y guardamos el nombre de producto que viene desde zeus para mostrarlo en detalle de pedido (LAKE HOUSE)               
                if ($IDClub == 233 ||  $IDClub == 8) :
                    $porciones = explode("|", $detalle_datos["IDProducto"]);
                    $IDProducto = $porciones[0]; // cod
                    $NombreProducto = $porciones[1]; // nombre prod

                    $codigo_categoria_producto = $porciones[2]; // codigo de la categoria del producto
                    $nombre_categoria = $porciones[3]; // categoria del producto
                    $ambiente_usado = $porciones[4]; // ambiente en el que se hizo el pedido del producto

                endif;

                $ValorUnitario = $detalle_datos["ValorUnitario"];
                $Total = (int) $detalle_datos["Cantidad"] * (float) $detalle_datos["ValorUnitario"];
                if ($Cantidad > 0) {
                    $inserta_detalle = "INSERT INTO DomicilioDetalle" . $Version . " (IDDomicilio, IDProducto, NombreProducto, Comentario, Cantidad, ValorUnitario, Total, IDCategoriaZeus, CategoriaZeus, AmbienteZeus)
                                                        Values('" . $id_domicilio . "', '" . $IDProducto . "', '" . $NombreProducto . "','" . $Comentario . "','" . $Cantidad . "','" . $ValorUnitario . "','" . $Total . "','" . $codigo_categoria_producto . "','" . $nombre_categoria . "','" . $ambiente_usado . "')";
                    $dbo->query($inserta_detalle);
                    $IDDomicilioDetalle = $dbo->lastID();
                    //REVISAR POR QUE ESTA DOBLE LA CONSULTA, PARA LAKE HOUSE NO SE APLICA POR QUE EL ID VIENE DE ZEUS Y PUEDE AFECTAR A LOS DEMAS PRODUCTOS EN LA DB
                    if ($IDClub != 233 || $IDClub != 8) :
                        $sql_existencias = "UPDATE Producto" . $Version . " SET Existencias =  Existencias - " . $Cantidad . " WHERE IDProducto = '" . $IDProducto . "'";
                        $dbo->query($sql_existencias);
                        $IDDomicilioDetalle = $dbo->lastID();

                        $sql_existencias = "UPDATE Producto" . $Version . " SET Existencias =  Existencias - " . $Cantidad . " WHERE IDProducto = '" . $IDProducto . "'";
                        $dbo->query($sql_existencias);

                    endif;

                    //$Caracteristicas= trim(preg_replace('/\s+/', ' ', $detalle_datos["Caracteristicas"]));
                    //$datos_respuesta= json_decode($Caracteristicas, true);
                    //$datos_respuesta= $Caracteristicas;
                    $datos_respuesta = $detalle_datos["Caracteristicas"];
                    $SumaEspeciales = 0;
                    if (count($datos_respuesta) > 0) :
                        foreach ($datos_respuesta as $detalle_carac) :
                            $IDPropiedadProducto = $detalle_carac["IDCaracteristica"];
                            $ValoresCarac = $detalle_carac["Valores"];
                            $ValoresID = $detalle_carac["ValoresID"];
                            $Total = $detalle_carac["Total"];
                            $SumaEspeciales += $Total;

                            if (!empty($IDPropiedadProducto)) {
                                $array_id_carac = explode(",", $ValoresID);
                                if (count($array_id_carac) > 0) {
                                    foreach ($array_id_carac as $id_carac) {
                                        $sql_datos_form = $dbo->query("INSERT INTO DomicilioCaracteristica (IDDomicilio, IDProducto, IDPropiedadProducto, IDCaracteristicaProducto, IDDomicilioDetalle,Valor, Valores, Total)
                                            Values ('" . $id_domicilio . "','" . $IDProducto . "','" . $IDPropiedadProducto . "','" . $id_carac . "', '$IDDomicilioDetalle', '" . $ValoresID . "','" . $ValoresCarac . "','" . $Total . "')");
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
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionocurriounproblemadecomunicacionporfavorintentemastarde', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
            }

            //if($IDClub==7): // Para Lagartos manda impresion
            if ($IDClub != 72) { // Para Bogota tenis Club no mando impresion hasta que se pague por la pasarela
                SIMWebServiceDomicilios::imprime_recibo_domicilio($id_domicilio, $Version, "");
            }

            /*  if ($IDClub == 8) : // Para Country manda impresion

                require(LIBDIR . "SIMWebServiceDomiciliosBTC.inc.php");
                SIMWebServiceDomiciliosBTC::enviar_domicilio($id_domicilio, $Version, "");
            endif; */

            SIMUtil::notifica_recibo_domicilio($id_domicilio, $Version);

            $datos_domicilio = $dbo->fetchAll("Domicilio" . $Version, " IDDomicilio = '" . $id_domicilio . "' ", "array");
            $mensajeAlRealizarPedido = $datos_config_domicilio['MensajeConfirmacion'];
            if ($IDClub == 44) {
                $mensaje_guardar = $datos_config_domicilio['MensajeConfirmacion'];
                if ($mensaje_guardar == "") {
                    //$mensaje_guardar="Su pedido se ha enviado al área encargada, se le enviará un correo para confirmar fecha y hora de entrega. le recordamos que los pedidos se tramitan  con un día de anticipación";
                    //$mensaje_guardar="Su pedido estará listo para recoger en la restaurante de la piscina a la hora seleccionada";
                    $mensaje_guardar = SIMUtil::get_traduccion('', '', 'SupedidoestarálistopararecogeralahoraacordadaenelClubJuvenil', LANG);
                }
            } else if ($mensajeAlRealizarPedido  <> "") {
                $mensaje_guardar = $datos_config_domicilio['MensajeConfirmacion'];
            } else {
                $mensaje_guardar = SIMUtil::get_traduccion('', '', 'Pedidorealizado', LANG) . $otro_mensaje;
            }

            if ($PermiteReservar == "N" && $IDClub == 7) {
                $mensaje_guardar = SIMUtil::get_traduccion('', '', 'ParapoderhacersupedidoporfavorcomuniqueseconAlimentosyBebidas(Ext248)oconelDepartamentodeCartera(Ext1241)', LANG);
                $otros_mensajes = "";
            }

            //Datos reserva
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");
            $response_reserva = array();
            $datos_reserva["IDDomicilio"] = (int) $id_domicilio;
            //Calculo el valor de la reserva
            $valor_inicial_reserva = (int) $GranTotal;
            // $datos_reserva["ValorReserva"] = $GranTotal + $ValorDomicilio + $SumaEspeciales;
            $Valor = $GranTotal + $SumaEspeciales;

            if ($Propina == "S" && !empty($porcentaje_propina)) {
                // $ValorPropina = $datos_reserva["ValorReserva"] * $porcentaje_propina / 100;
                $ValorPropina = $Valor * $porcentaje_propina / 100;
                // $datos_reserva["ValorReserva"] += $ValorPropina;
            }

            // $ValorReserva = $GranTotal + $ValorDomicilio + $ValorPropina;
            $ValorReserva = $Valor + $ValorDomicilio + $ValorPropina;
            $datos_reserva["ValorReserva"] = $ValorReserva;

            $datos_reserva["ValorPagoTexto"] = $datos_club_otros["SignoPago"] . " " . $ValorReserva . " " . $datos_club_otros["TextoPago"];

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
            $descripcion = SIMUtil::get_traduccion('', '', 'PagoDomicilioMiClub', LANG); //descripciÛn de la transacciÛn
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
            $datos_post["llave"] = 'moneda';
            $datos_post["valor"] = (string) $moneda;
            array_push($response_parametros, $datos_post);

            $datos_post["llave"] = "IDSocio";
            $datos_post["valor"] = (string) $IDSocio;
            array_push($response_parametros, $datos_post);

            $datos_post["llave"] = "ref";
            $datos_post["valor"] = $refVenta;
            array_push($response_parametros, $datos_post);

            $datos_post["llave"] = 'llave';
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

            $datos_post["llave"] = 'descripcion';
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

            $datos_post["llave"] = 'valor';
            $datos_post["valor"] = $ValorReserva;
            array_push($response_parametros, $datos_post);

            $datos_post["llave"] = 'iva';
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

            $datos_post["llave"] = 'prueba';
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

            // $respuesta["message"] = $mensaje_guardar . $otros_mensajes;
            $respuesta["message"] = "Guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = $datos_reserva;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Dom.Atencionfaltanparametrososocionoexiste', LANG);
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


                $hora_fecha_entrega = $dbo->getFields("Domicilio" . $Version, "HoraEntrega", "IDDomicilio = '" . $IDDomicilio . "'");
                $Restaurante = $dbo->getFields("Domicilio" . $Version, "IDRestauranteDomicilio", "IDDomicilio = '" . $IDDomicilio . "'");

                $fecha_domicilio = substr($hora_fecha_entrega, 0, 10);
                if (empty($fecha_domicilio)) {
                    $fecha_domicilio = date("Y-m-d");
                }

                $hora_entrega = substr($hora_fecha_entrega, 11);

                // $hora_entrega = date("H:i:s", strtotime("+ 12 hour",strtotime($hora_entrega)));

                //Verifico que este en el tiempo limite para eliminar
                $resp_config = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $fecha_domicilio, $Version, $Restaurante, $IDUsuario);
                $tiempo_cancelacion = (int) $resp_config["response"][0]["TiempoMinimoCancelacion"];
                $despuesOantes = $resp_config["response"][0]["TiempoMinimoCancelacionDespues"];

                /*  if (empty($tiempo_cancelacion)) :
                        $tiempo_cancelacion = (int) $dbo->getFields("ConfiguracionDomicilios" . $Version, "TiempoMinimoCancelacion", "IDClub = '$IDClub' AND Activo = 'S'");
                        $tiempo_cancelacion = (int) $dbo->getFields("ConfiguracionDomicilios" . $Version, "TiempoMinimoCancelacionDespues", "IDClub = '$IDClub' AND Activo = 'S'");
                    endif; */


                if ((int) $tiempo_cancelacion > 0) :
                    $minutos_anticipacion = $tiempo_cancelacion;
                else :
                    $minutos_anticipacion = 30;
                endif;

                $ValidarEliminarConTiempo = true;
                if ((int) $tiempo_cancelacion == 0) :
                    $ValidarEliminarConTiempo = false;
                endif;


                $fechahora_actual = date("Y-m-d H:i:s");

                if ($despuesOantes == 1) :
                    $hora_inicio_domicilio =  date("Y-m-d H:i:s", strtotime('+' . $minutos_anticipacion . ' minute', strtotime($fecha_domicilio . " " . $hora_entrega)));
                else :
                    $hora_inicio_domicilio =  date("Y-m-d H:i:s", strtotime('-' . $minutos_anticipacion . ' minute', strtotime($fecha_domicilio . " " . $hora_entrega)));
                endif;

                if ($fechahora_actual > $hora_inicio_domicilio && empty($Admin) && $ValidarEliminarConTiempo) :

                    //para luker mostramos otro mensaje
                    if ($IDClub == 95 || $IDClub == 96 || $IDClub == 97) {
                        $mensajeCancelacion = "El pedido no puede ser cancelado. supera el tiempo mínimo para cancelación. Para mayor información comuníquese con el área encargada.";
                    } else {
                        $mensajeCancelacion = SIMUtil::get_traduccion('', '', 'Elpedidonopuedesercancelado.superaeltiempomínimoparacancelación.Paramayorinformacióncomuníqueseconelclub', LANG);
                    }

                    $respuesta["message"] = $mensajeCancelacion;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                else :



                    //if($IDClub==7): // Para Lagartos manda impresion
                    SIMUtil::notifica_elimina_domicilio($IDDomicilio, $Version);
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
                            echo $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimosnohayconexionalabase', LANG);
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



                    SIMWebServiceDomicilios::imprime_recibo_domicilio($IDDomicilio, $Version, 'Eliminado');


                    //borro productos domicilio
                    //$sql_borra_producto = $dbo->query("Delete From DomicilioDetalle".$Version." Where IDDomicilio  = '".$IDDomicilio."'");

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Pedidoeliminadocorrectamente', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                endif;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_configuracion_domicilio($IDClub, $IDSocio = "",  $Fecha = "", $Version = "", $IDRestaurante = "", $IDUsuario = "")
    {
        if (empty($Fecha)) :
            $Fecha = date("Y-m-d");
            $dia_fecha = date("w");
        else :
            $dia_fecha = date("w", strtotime($Fecha));
        endif;

        $dbo = &SIMDB::get();
        $response = array();



        $sql = "SELECT * FROM  ClubFechaCierre WHERE Fecha = '" . $Fecha . "' and IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $r_cierre = $dbo->fetchArray($qry);
            $mensaje_respuesta = SIMUtil::get_traduccion('', '', 'Losentimosclubcerradoeldia', LANG) . $Fecha . SIMUtil::get_traduccion('', '', 'Motivo', LANG) . $r_cierre["Motivo"];
            $respuesta["message"] = $mensaje_respuesta;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        } //end else

        if (!empty($IDRestaurante)) {
            $condicion = " and (IDRestauranteDomicilio= '" . $IDRestaurante . "' or IDRestauranteDomicilio = '' ) ";
        }

        //HAGO UNA CONSULTA PARA SABER SI HAY QUE MOSTRAR EL RESTAURANTE TODOS LOS DIAS
        $MostrarRestauranteTodosLosDias = $dbo->getFields("RestauranteDomicilio" . $Version, "MostrarRestauranteTodosLosDias", "IDRestauranteDomicilio = '" . $IDRestaurante . "'");

        if ($MostrarRestauranteTodosLosDias == "S") {
            $CondicionDias = "";
        } else {
            $CondicionDias = " and Dias like '%" . $dia_fecha . "|%' ";
        }

        if (!empty($IDSocio)) {
            $condicion .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        //  $sql = "SELECT * FROM ConfiguracionDomicilios" . $Version . "  WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and Dias like '%" . $dia_fecha . "|%' " . $condicion;
        $sql = "SELECT * FROM ConfiguracionDomicilios" . $Version . "  WHERE Activo = 'S' and IDClub = '" . $IDClub . "'" . $CondicionDias . $condicion;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                // VALIDO LA HORA DEL DOMICILIO PARA SABER SI ESTA DISPONIBLE EL SERVICIO

                $sqlHoras = "SELECT HoraInicioDomicilios, HoraFinDomilios FROM ConfiguracionDomicilios$Version  WHERE IDConfiguracionDomicilios = $r[IDConfiguracionDomicilios]";
                $qryHoras = $dbo->query($sqlHoras);
                $datos = $dbo->fetchArray($qryHoras);

                $horaActual = strtotime(date("H:i:s"));
                $horaInicio = strtotime($datos[HoraInicioDomicilios]);
                $horaFin = strtotime($datos[HoraFinDomilios]);

                if ($horaActual > $horaFin) {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'LahoralimitedelDomicilioyapaso,porfavorcontactarconelclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($horaActual < $horaInicio) {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'LosDomiciliosaunnoestandisponibles', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

                $configuracion["IDConfiguracionDomicilios"] = $r["IDConfiguracionDomicilios"];
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["HoraInicioEntrega"] = $r["HoraInicioEntrega"];
                $configuracion["HoraFinEntrega"] = $r["HoraFinEntrega"];
                $configuracion["TiempoMinimoPedido"] = $r["TiempoMinimoPedido"];
                $configuracion["IntervaloEntrega"] = $r["IntervaloEntrega"];
                $configuracion["TiempoMinimoCancelacion"] = $r["TiempoMinimoCancelacion"];
                $configuracion["TiempoMinimoCancelacionDespues"] = $r["TiempoMinimoCancelacionDespues"];
                $configuracion["TiempoConfirmacion"] = $r["TiempoConfirmacion"];
                $configuracion["Celular"] = $datos_socio["Celular"];
                $configuracion["LabelDomicilios"] = $r["LabelDomicilios"];
                $configuracion["TextoDomicilio"] = $r["TextoDomicilio"];
                $configuracion["CantidadMaximaProducto"] = $r["CantidadMaximaProducto"];

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
                $configuracion["MensajeConfirmacion"] = $r["MensajeConfirmacion"];
                $configuracion["RequerirQRParaVerMenu"] = $r["RequerirQRParaVerMenu"];
                $configuracion["TextoRequerirQR"] = $r["TextoRequerirQR"];
                $configuracion["BotonEscanearQR"] = $r["BotonEscanearQR"];

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

                        if ($IDClub == 15 && $r_tipo_pago["IDTipoPago"] == 15) :
                            $r_tipo_pago["Nombre"] = "Pago en efectivo";
                        endif;
                        $tipopago["Nombre"] = $r_tipo_pago["Nombre"];
                        $tipopago["ConRespuesta"] = $r_tipo_pago["ConRespuesta"];
                        $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                        $imagen = "";
                        //Para el condado y es pagos online muestro la imagen de placetopay

                        switch ($r_tipo_pago["IDTipoPago"]) {
                            case "1":
                                $imagen = "https://www.miclubapp.com/file/noticia/641923_placetopay.png";
                                break;
                            case "2":
                                $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                break;
                            case "3":
                                $imagen = "https://www.miclubapp.com/file/noticia/icsi.png";
                                break;
                            case "9":
                                $imagen = "https://www.miclubapp.com/file/noticia/ictarjeta.png";
                                break;
                            case "12":
                                $imagen = "https://www.miclubapp.com/file/noticia/iccredibancopago.png";
                                break;
                            case "7":
                                $imagen = "https://www.miclubapp.com/file/tipopago/tarjeta.png";
                                break;
                            case "17":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Tarjeta.png";
                                break;
                            case "15":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Efectivo.png";
                                break;
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

                $sql_campos = "SELECT * FROM DomicilioPregunta WHERE Publicar = 'S' and Version = '" . $VersionPregunta . "' and IDConfiguracionDomicilio= '" . $r["IDConfiguracionDomicilios"] . "' ORDER BY Orden";

                $qry_campos = $dbo->query($sql_campos);
                if ($dbo->rows($qry_campos) > 0) {
                    while ($r_campos = $dbo->fetchArray($qry_campos)) {
                        $campos["IDCampo"] = $r_campos["IDDomicilioPregunta"];
                        $campos["Nombre"] = $r_campos["Nombre"];
                        $campos["Descripcion"] = utf8_encode($r_campos["Descripcion"]);

                        //MESAS PARA CADA AMBIENTE LAKE HOUSE
                        if ($IDClub == 233 ||  $IDClub == 8) :
                            $campos["Tipo"] = $r_campos["Tipo"];



                            //SACAMOS EL AMBIENTE DEL RESTAURANTE PARA FILTRAR MESAS         
                            $datos_ambientes = $dbo->fetchAll("RestauranteDomicilio", " IDRestauranteDomicilio = '" . $IDRestaurante . "' ", "array");
                            $datos_ambiente = $datos_ambientes["CodigoExternoAmbienteZeus"];

                            //ACA ESTAN LAS MESAS POR TIPO DE AMBIENTE
                            if ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "MR") :
                                $campos["Valores"] = "001,002,003,004,005,006,007,008,009,010,011,012,013,014,015,016,017,018,019,020";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "AT") :
                                $campos["Valores"] = "021";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "DP") :
                                $campos["Valores"] = "022";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "EV") :
                                $campos["Valores"] = "023";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "MC") :
                                $campos["Valores"] = "034";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "MV") :
                                $campos["Valores"] = "035";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "PT") :
                                $campos["Valores"] = "120";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "RS") :
                                $campos["Valores"] = "121";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "HY") :
                                $campos["Valores"] = "HY001,HY002,HY003,HY004,HY005,HY006,HY007,HY008,HY009,HY010";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "LG") :
                                $campos["Valores"] = "LG001,LG002,LG003,LG004,LG005,LG006,LG007,LG008,LG009,LG010,LG011,LG012,LG013,LG014";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "OL") :
                                $campos["Valores"] = "OL001,OL002,OL003,OL004,OL005,OL006,OL007,OL008";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "RT") :
                                $campos["Valores"] = "RT001,RT002,RT003,RT004,RT005,RT006,RT007,RT008,RT009,RT010,RT011,RT012,RT013,RT014,RT015,RT016,RT017,RT018,RT019,RT020,RT021,RT022,RT023,RT024,RT025,RT026,RT027";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "SB") :
                                $campos["Valores"] = "SB01";
                            elseif ($r_campos["IDDomicilioPregunta"] == "16" and $datos_ambiente == "SG") :
                                $campos["Valores"] = "SG01";
                            /* ACA ESTSAN LOS TIPOS DE PAGOS
                        elseif($r_campos["IDDomicilioPregunta"] == "18" and 1==1):
                        $campos["Valores"] = "01- EFECTIVO";
                        elseif($r_campos["IDDomicilioPregunta"] == "18" and 1==1):
                        $campos["Valores"] = "02- PSE O TARJETA CREDITO"; */
                            else :
                                //ESTE SE DEJA ASI POR QUE NO TENEMOS LISTA DE MESEROS AUN
                                $campos["Valores"] = $r_campos["Valor"];
                            endif;
                        else :
                            $campos["Tipo"] = $r_campos["Tipo"];
                            $campos["Valores"] = $r_campos["Valor"];
                        endif;
                        $campos["Obligatorio"] = $r_campos["Obligatorio"];
                        array_push($response_campos, $campos);
                    } //end while
                }
                $configuracion["CampoFormulario"] = $response_campos;

                $configuracion["MostrarFotoProducto"] = $r["MostrarFotoProducto"];
                $configuracion["MostrarPantallaTexto"] = $r["MostrarPantallaTexto"];
                $configuracion["TextoPantalla"] = $r["TextoPantalla"];

                $configuracion["NumeroPedidosEnTiempo"] = $r["NumeroPedidosEnTiempo"];
                $configuracion["CantidadMaximaProductoPorTiempo"] = $r["CantidadMaximaProductoPorTiempo"];
                $configuracion["TiempoValidoParaCantidadPedidos"] = $r["TiempoValidoParaCantidadPedidos"];

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            if ($IDClub == 32) { //Bijao
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elserviciodedomicilioestádisponiblefinesdesemanaytemporadas', LANG);
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ElserviciodeDomicilionoestáactivoeldiadehoy', LANG);
            }
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        } //end else

        return $respuesta;
    } // fin function

    public function get_producto_buscador($IDClub, $IDSocio, $IDUsuario, $Tag, $IDRestaurante, $Version = "")
    {

        $dbo = &SIMDB::get();
        $response = array();

        //$sql = "SELECT * FROM  Producto".$Version." WHERE (Nombre like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%' ) and  IDClub = '" . $IDClub . "'";
        $sql = "SELECT P.*,CP.Publicar
                        From Producto" . $Version . " P, CategoriaProducto" . $Version . " CP, ProductoCategoria" . $Version . " PC
                        WHERE CP.IDCategoriaProducto = PC.IDCategoriaProducto
                        And P.IDProducto=PC.IDProducto and P.IDClub = '" . $IDClub . "' and ( CP.IDRestauranteDomicilio = '" . $IDRestaurante . "' or CP.IDRestauranteDomicilio = 0)
                        And (P.Nombre like '%" . $Tag . "%' or P.Descripcion like '%" . $Tag . "%' ) and P.Publicar = 'S'  and CP.Publicar='S' ";


        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $lista_producto["IDClub"] = $r["IDClub"];
                $lista_producto["IDProducto"] = $r["IDProducto"];
                $lista_producto["Nombre"] = utf8_encode($r["Nombre"]);
                $lista_producto["Descripcion"] = utf8_encode($r["Descripcion"]);
                $lista_producto["Precio"] = $r["Precio"] + $r["PrecioEmpaque"];

                //PRECIO PARA SOCIOS TIPO NO AFILIADOS
                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
                if ($TipoSocio == "No Afiliado") {
                    $lista_producto["Precio"] = $r["PrecioNoAfiliado"];
                }

                //PRECIO PARA LOS USUARIOS
                if ($TipoApp == "Empleado" && $datos_producto["PrecioUsuario"] > 0) {
                    $producto["Precio"] = $datos_producto["PrecioUsuario"];
                }

                $lista_producto["PermiteComentarios"] = $r["PermiteComentarios"];
                $lista_producto["OcultarMostrarCantidad"] = $r["OcultarMostrarCantidad"];
                if (!empty($r["Foto1"])) :
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


                //VARIAS FOTOS
                $response_fotos = array();
                for ($i_foto = 1; $i_foto <= 6; $i_foto++) :

                    $campo_foto = "Foto" . $i_foto;
                    if (!empty($r[$campo_foto])) :
                        $array_dato_foto["Foto"] = IMGPRODUCTO_ROOT . $r[$campo_foto];
                        array_push($response_fotos, $array_dato_foto);
                    endif;
                endfor;
                $lista_producto["Fotos"] = $response_fotos;




                //Caracteristicas
                $sql_producto_carac = "SELECT PP.IDPropiedadProducto,PP.Nombre as Categoria, PP.Tipo, PP.Obligatorio, PP.MaximoPermitido,CP.Nombre as NombreValor, CP.Valor as Precio, CP.IDCaracteristicaProducto,CP.OcultarPrecioEnCero as OcultarPrecio
                                    FROM ProductoCaracteristica PC, CaracteristicaProducto CP, PropiedadProducto PP
                                    WHERE PC.IDCaracteristicaProducto=CP.IDCaracteristicaProducto And
                                          CP.IDPropiedadProducto = PP.IDPropiedadProducto And
                                          CP.IDClub =  '" . $IDClub . "'  And
                                          PC.IDProducto = '" . $r["IDProducto"] . "' 
                                          ORDER BY IDPropiedadProducto ";
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

                    $valores["OcultarPrecioEnCero"] = $row_prod_carac["OcultarPrecio"];
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
                $lista_producto["Caracteristicas"] = $response_carac_producto;

                //FIN caracteristicas





                array_push($response, $lista_producto);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
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

        //metropolitan club coloco la fecha inicio apartir del tercer dia
        if ($IDClub == 79) {
            $fechaInicio = strtotime('+3 day', strtotime($fecha_actual));
        }
        $contador = 1;
        $primera_fecha = 1;
        $flag_disponible_hoy = 0;

        if (!empty($IDRestaurante)) :
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

            //metropolitan club si es festivo no se muestra la fecha o si es 31 de diciembbre del 2022
            if ($IDClub == 79) {
                $festivo = SIMUtil::validaDiaFestivo($IDClub, $fecha_validar);
                if ($festivo == true || $fecha_validar == '2022-12-31') {
                    $fecha_cierre = 1;
                }
            }
            $fecha_domicilio = date('Y-m-d', $i);
            $configuracion["IDClub"] = $IDClub;
            $configuracion["Fecha"] = $fecha_domicilio;
            if ((int) $r_disponibilidad["IDConfiguracionDomicilios"] > 0 && $fecha_cierre == 0) :
                array_push($response, $configuracion);
            endif;
        }

        if (count($configuracion) > 0) :
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronconfiguracionparapedidos', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function get_horas_entrega($IDClub, $Fecha, $Version = "", $IDRestaurante = "")
    {

        $dbo = &SIMDB::get();

        if (empty($Fecha)) :
            $dia_fecha = date("w");
            $Fecha = date("Y-m-d");
        else :
            $dia_fecha = date("w", strtotime($Fecha));
        endif;

        if (!empty($IDRestaurante)) :
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

                //especial para metropolitan club el 23 de diciembre 2022 los pedidos son hasta las 2 pm
                if (($IDClub == 79) && $Fecha == '2022-12-23') {
                    $hora_fin = '14:00';
                }
                //especial para metropolitan club el 30 de diciembre 2022 los pedidos son hasta las 12 pm
                if (($IDClub == 79) && $Fecha == '2022-12-30') {
                    $hora_fin = '12:00';
                }
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

                while ($hora_actual <= $hora_final) :

                    // Si es hoy solo devuelvo desde la hora mayor de lo contrario devuelvo todas las horas
                    if ($Fecha == date("Y-m-d") && $hora_actual >= $hora_fecha_actual) :
                        $flag_hora_valida = 1;
                    elseif ($Fecha == date("Y-m-d")) :
                        $flag_hora_valida = 0;
                    elseif ($Fecha != date("Y-m-d")) :
                        $flag_hora_valida = 1;
                    endif;

                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["Fecha"] = $Fecha;
                    $configuracion["Hora"] = date("H:i", $hora_actual);
                    $hora_actual = strtotime('+' . $intervalo . ' minute', $hora_actual);
                    if ($flag_hora_valida == 1) :
                        array_push($response, $configuracion);
                    endif;
                endwhile;
            } //ednw hile

            if (count($configuracion) > 0) :
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'LafechadehoynoestadisponibleparaPedidos', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;

            endif;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'LafechadehoynoestadisponibleparaPedidos', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function imprime_recibo_domicilio($IDDomicilio, $Version = "", $Tipo = "")
    {

        /* Change to the correct path if you copy this example! */
        require LIBDIR . '/../impresionremota/autoload.php';

        if ($IDClub == 125) :
            date_default_timezone_set('America/Montevideo');
        endif;


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

        if (!empty($datos_domicilio["IDDomicilio"]) && !empty($IPImpresora) && !empty($PuertoImpresora)) :
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
                                                    IDDomicilio = '" . $IDDomicilio . "' and IDProducto = '" . $r["IDProducto"] . "' AND DC.IDDomicilioDetalle = $r[IDDomicilioDetalle]
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
                    if ($Tipo == 'Eliminado') {
                        $printer->setTextSize(2, 2);
                        $printer->text(SIMUtil::get_traduccion('', '', 'PEDIDOELIMINADOPORSOCIO', "Es"));
                    }
                    if (!empty($RestauranteDom)) {
                        $printer->setTextSize(2, 2);
                        $printer->text($RestauranteDom . "\n");
                    }
                    //$printer -> text("App para todos\n");
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(1, 1);
                    $printer->text(SIMUtil::get_traduccion('', '', 'Numero', "Es") . ":" . $numero_pedido . "\n");
                    $printer->text(SIMUtil::get_traduccion('', '', 'NombreSocio', "Es") . ":" . $nombre_socio . "\n");
                    $printer->setTextSize(1, 2);
                    $printer->text(SIMUtil::get_traduccion('', '', 'NumeroAccion', "Es") . ":" . $accion_socio . "\n", True);
                    $printer->setTextSize(1, 2);
                    $printer->text(SIMUtil::get_traduccion('', '', 'Celular', "Es") . ":" . $celular_socio . "\n");
                    $printer->setTextSize(1, 1);
                    $printer->text(SIMUtil::get_traduccion('', '', 'HoraSolicitud', "Es") . ":" . $hora_solicitud . " \n");
                    if ($datos_config["SolicitaHoraDomicilio"] == "S") {
                        $printer->text(SIMUtil::get_traduccion('', '', 'HoraEntrega', "Es") . ":" . $hora_entrega . " \n");
                    }
                    $printer->text(SIMUtil::get_traduccion('', '', 'Lugar', "Es") . ":" . $Direccion . " \n");
                    $printer->text(SIMUtil::get_traduccion('', '', 'MedioPago', "Es") . ":" . $FormaPago . " \n");
                    $printer->setTextSize(1, 2);


                    $sql_otro_dato = "SELECT * From DomicilioCampo Where IDDomicilio = '" . $IDDomicilio . "'";
                    $result_otro_dato = $dbo->query($sql_otro_dato);
                    while ($row_otro_dato = $dbo->fetchArray($result_otro_dato)) :
                        $Pregunta = $dbo->getFields("DomicilioPregunta", "Nombre", "IDDomicilioPregunta = '" . $row_otro_dato["IDDomicilioPregunta"] . "' AND Version='3'");
                        $ValorPregunta =  $row_otro_dato["Valor"];
                        $NumeroMesa = $ValorPregunta;
                        if ($datos_domicilio["IDClub"] != 7) { // Para lagartos no se imprime los dinamicos
                            $printer->text($Pregunta . ":" . $ValorPregunta . " \n");
                            $printer->setTextSize(1, 1);
                        }

                    endwhile;


                    $printer->text(SIMUtil::get_traduccion('', '', 'NumeroMesa', "Es") . ":" . $NumeroMesa . " \n", True);
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->text("Propina (" . $datos_config["LabelPropina"] . "): " . $Propina . " \n");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->setTextSize(1, 2);
                    $printer->text(SIMUtil::get_traduccion('', '', 'DescripcionPedido', "Es") . ":" . "\n\n");
                    $printer->setTextSize(1, 1);
                    $printer->text($pedido . "\n", True);
                    $printer->setTextSize(2, 1);


                    $printer->text(SIMUtil::get_traduccion('', '', 'Comentarios', "Es") . "\n");
                    $printer->setTextSize(2, 1);
                    $printer->text($comentarios . " \n");
                    if ($datos_socio["PermiteDomicilios"] == "N") {
                        $printer->setTextSize(1, 2);
                        $printer->text(SIMUtil::get_traduccion('', '', 'Pendienteverificacionpago', "Es") . " \n");
                    }
                    $printer->cut();
                    $printer->close();

                    $contador_pos++;

                    //Marco el domicilio como impreso
                    $sql_impreso = "UPDATE Domicilio" . $Version . " SET Impreso = 'S' WHERE IDDomicilio = '" . $IDDomicilio . "' ";
                    $dbo->query($sql_impreso);
                }

                //$connector = new NetworkPrintConnector("181.48.188.75", 6000);
                //$connector = new NetworkPrintConnector($IPImpresora, $PuertoImpresora);
                /* Print a "Hello world" receipt" */
                //return true;
            } catch (Exception $e) {
                //echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
                $sql_impreso = "UPDATE Domicilio" . $Version . " SET Impreso = 'E' WHERE IDDomicilio = '" . $IDDomicilio . "' ";
                $dbo->query($sql_impreso);
            }
        endif;

        return true;
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

                if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4 || $r["IDTipoPago"] == 5) :                     // payU
                    if ($r["EstadoTransaccion"] == "") :
                        $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4" || $r["EstadoTransaccion"] == "AUTHORISED" || $r["EstadoTransaccion"] == "Aprobada") :
                        $respuesta["message"] = "Domicilio pagado correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = "El pago no fue realizado";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    endif;
                elseif ($r["IDTipoPago"] == 12) :
                    if ($r["EstadoTransaccion"] == "A") :
                        $respuesta["message"] = "C1. Reserva pagada correctamente!";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        //Compruebo de nuevo la transaccion para confirmar que no este pagada
                        $orden = $dbo->getFields("PagoCredibanco", "NumeroFactura", "reserved12 = '$r[IDDomicilio]'");
                        if (empty($orden)) {
                            $respuesta["message"] = "C2.Domicilio en espera de confirmacion de pago";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        } else {
                            $repuesta = SIMPasarelaPagos::CredibancoRespuestaV2($orden);
                            if ($repuesta["success"]) {
                                $estado = $repuesta["response"]["orderStatus"];
                                switch ($estado) {
                                    case "0":
                                        // $estadoTx = "NO PAGADO";
                                        $respuesta["message"] = "C10. No pagado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    case "1":
                                    case "7":
                                        // $estadoTx = "PENDIENTE";
                                        $respuesta["message"] = "C11. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;

                                    case "2":
                                        // $estadoTx = "APROBADO";
                                        $respuesta["message"] = "C12.Reserva pagada correctamente.";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                    case "3":
                                    case "6":
                                        // $estadoTx = "RECHAZADO";
                                        $respuesta["message"] = "C13.Transaccion rechazada";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    default:
                                        // $estadoTx = "OTRO";
                                        $respuesta["message"] = "C14. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                }
                            } else {
                                $respuesta["message"] = "C4. El pago no fue realizado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = $response;
                            }
                        }
                    endif;
                elseif ($r["IDTipoPago"] == 15) :
                    $respuesta["message"] = "Registro de pago Exitoso!";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                else :
                    $respuesta["message"] = "El domicilio no fue pagado por pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;

                if ($r["IDTipoPago"] == 3) :
                    $respuesta["message"] = "Pago Exitoso!";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                endif;
            }

            if ($IDClub == 15) :
                // BUSCAMOS EL REGISTRO EN LA TABLA DE PAGOS
                $SQL = "SELECT * FROM PagoEcollect WHERE IDClub = $IDClub AND Factura = '$IDDomicilio-$Version'";
                $qryPereira = $dbo->query($SQL);

                if ($dbo->rows($qryPereira) > 0) :
                    $respuesta["message"] = "Compra exitosa!";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                endif;
            endif;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_restaurante_domicilio($id_club, $Version, $IDSocio = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $response = array();
        $sql = "SELECT * FROM RestauranteDomicilio" . $Version . "  WHERE Publicar = 'S' and IDClub = '" . $id_club . "'  $condicion ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', "Es");
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDRestaurante"] = $r["IDRestauranteDomicilio"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];

                if (!empty($r["RestauranteFile"])) :
                    $foto = IMGEVENTO_ROOT . $r["RestauranteFile"];
                else :
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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', "Es");
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago = "", $Version = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDDomicilio) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("Domicilio" . $Version, "IDDomicilio", "IDDomicilio = '" . $IDDomicilio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

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

    public function validar_qr_ver_menu_domicilio($IDClub, $IDSocio, $IDUsuario, $QR, $IDRestaurante, $Version)
    {
        $dbo = &SIMDB::get();

        //id del restaurante y mesa
        //EL QR TRAE EL ID DEL RESTAURANTE Y LA MESA
        $parametrosQr = explode("-", $QR);


        //IDRestaurante
        $IDRestauranteQr = $parametrosQr[0];

        //Mesa que trae el qr
        $Mesa = $parametrosQr[1];
        //verifico que el restaurante exista y pertenezca al club
        $id_restaurante = $dbo->getFields("RestauranteDomicilio" . $Version, "IDRestauranteDomicilio", "IDRestauranteDomicilio = '" . $IDRestauranteQr . "' and IDClub = '" . $IDClub . "'");

        if (!empty($id_restaurante)) {

            //VALIDO QUE EL QR QUE SE ESCANEO SEA IGUAL AL RESTAURANTE QUE SELECCIONO EN EL APP
            if ($IDRestaurante == $IDRestauranteQr) {
                $respuesta["message"] = "El usuario se encuentra en restaurante";
                $respuesta["success"] = true;
                $respuesta["response"] = $Mesa;
            } else {
                $respuesta["message"] = "no se encuentra en el restaurante correcto.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Atencion el restaurante no existe o no pertenece al club";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
}
