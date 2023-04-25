<?php
class SIMWebServiceObjetosPrestados
{
    public function get_configuracion_objetos_prestados($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        if ($IDSocio > 0) {
            $condicionUsuario = " AND AplicaPara='S'";
        } elseif ($IDUsuario > 0) {

            $condicionUsuario = " AND AplicaPara='U'";
        }



        $sql = "SELECT IDClub,LabelTitulo,LabelSubtitulo,LabelPendientesPorEntregar,LabelEntregados FROM ConfiguracionObjetosPrestados  WHERE IDClub = '" . $IDClub . "' $condicionUsuario";
        //echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["LabelTitulo"] = $r["LabelTitulo"];
                $configuracion["LabelSubtitulo"] = $r["LabelSubtitulo"];
                $configuracion["LabelPendientesPorEntregar"] = $r["LabelPendientesPorEntregar"];
                $configuracion["LabelEntregados"] = $r["LabelEntregados"];
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', '	Configuracionnoestáactivo', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  get_categorias_objetos_prestados($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        /*  if ($IDSocio > 0) {
            $condicionDirigidoA = " AND (DirigidoA='S' OR  DirigidoA='T')";
        } elseif ($IDUsuario > 0) {

            $condicionDirigidoA = " AND (DirigidoA='E' OR  DirigidoA='T')";
        }
 */

        $response = array();
        $sql = "SELECT IDCategoriaObjetosPrestados,NombreCategoriaObjeto,Icono FROM CategoriaObjetosPrestados  WHERE IDClub = '" . $IDClub . "' AND Publicar='S' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoria["IDCategoriaObjeto"] = $r["IDCategoriaObjetosPrestados"];
                $categoria["NombreCategoriaObjeto"] = $r["NombreCategoriaObjeto"];
                if (!empty($r["Icono"])) :
                    $foto = OBJETOSPRESTADOS_ROOT . $r["Icono"];
                else :
                    $foto = "";
                endif;
                $categoria["Icono"] = $foto;


                array_push($response, $categoria);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  get_objetos_prestados_pendientes($IDCategoriaObjeto, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio > 0) {
            $condicionUsuario = " AND IDSocioPrestamo='" . $IDSocio . "'";
        } elseif ($IDUsuario > 0) {

            $condicionUsuario = " AND IDSocioPrestamo='" . $IDUsuario . "'";
        }



        $response = array();
        $sql = "SELECT Nombre,CantidadPrestada,IDLugarObjetosPrestados,Estado,IDObjetosPrestados FROM ObjetosPrestados  WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND Estado='1'" . $condicionUsuario;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $ObjetosPrestados["IDObjetoPrestamo"] = $r["IDObjetosPrestados"];
                $ObjetosPrestados["Nombre"] = $r["Nombre"];
                $ObjetosPrestados["CantidadPrestada"] = $r["CantidadPrestada"];

                $ObjetosPrestados["LugarDeEntrega"] =   $dbo->getFields("LugarObjetosPrestados", "Nombre", "IDLugarObjetosPrestados = '" .  $r["IDLugarObjetosPrestados"] . "'");
                $ObjetosPrestados["Estado"] = SIMResources::$estado_objeto_prestado[$r["Estado"]];



                array_push($response, $ObjetosPrestados);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  get_objetos_prestados_entregados($IDCategoriaObjeto, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio > 0) {
            $condicionUsuario = " AND IDSocioPrestamo='" . $IDSocio . "'";
        } elseif ($IDUsuario > 0) {

            $condicionUsuario = " AND IDSocioPrestamo='" . $IDUsuario . "'";
        }



        $response = array();
        $sql = "SELECT Nombre,CantidadPrestada,IDLugarObjetosPrestados,Estado,IDObjetosPrestados,CantidadEntregada FROM ObjetosPrestados  WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND Estado='2'" . $condicionUsuario;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $ObjetosPrestados["IDObjetoPrestamo"] = $r["IDObjetosPrestados"];
                $ObjetosPrestados["Nombre"] = $r["Nombre"];
                $ObjetosPrestados["CantidadEntregada"] = $r["CantidadEntregada"];
                $ObjetosPrestados["LugarDeEntrega"] = $dbo->getFields("LugarObjetosPrestados", "Nombre", "IDLugarObjetosPrestados = '" .  $r["IDLugarObjetosPrestados"] . "'");
                $ObjetosPrestados["Estado"] = SIMResources::$estado_objeto_prestado[$r["Estado"]];



                array_push($response, $ObjetosPrestados);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }


    public function get_configuracion_objetos_prestados_administrador($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        if ($IDSocio > 0) {
            $condicionUsuario = " AND AplicaPara='S'";
        } elseif ($IDUsuario > 0) {

            $condicionUsuario = " AND AplicaPara='U'";
        }



        $sql = "SELECT IDClub,LabelTitulo,LabelBotonIngresarPrestamo,LabelBotonRegistrarDevolucion,LabelTituloBuscador,LabelBuscador,LabelTituloCantidad,LabelTituloLugarEntrega,LabelBuscadorObjetos,LabelProductosPrestados,LabelProductosEntregados FROM ConfiguracionObjetosPrestadosAdministrador  WHERE IDClub = '" . $IDClub . "' $condicionUsuario";
        //echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracionAdministrador["IDClub"] = $r["IDClub"];
                $configuracionAdministrador["LabelTitulo"] = $r["LabelTitulo"];
                $configuracionAdministrador["LabelBotonIngresarPrestamo"] = $r["LabelBotonIngresarPrestamo"];
                $configuracionAdministrador["LabelBotonRegistrarDevolucion"] = $r["LabelBotonRegistrarDevolucion"];
                $configuracionAdministrador["LabelTituloBuscador"] = $r["LabelTituloBuscador"];
                $configuracionAdministrador["LabelBuscador"] = $r["LabelBuscador"];
                $configuracionAdministrador["LabelTituloCantidad"] = $r["LabelTituloCantidad"];
                $configuracionAdministrador["LabelTituloLugarEntrega"] = $r["LabelTituloLugarEntrega"];
                $configuracionAdministrador["LabelBuscadorObjetos"] = $r["LabelBuscadorObjetos"];
                $configuracionAdministrador["LabelProductosPrestados"] = $r["LabelProductosPrestados"];
                $configuracionAdministrador["LabelProductosEntregados"] = $r["LabelProductosEntregados"];
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracionAdministrador;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', '	Configuracionnoestáactivo', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  get_detalle_categoria_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto)
    {
        $dbo = &SIMDB::get();
        /*    if ($IDSocio > 0) {
            $condicionDirigidoA = " AND (DirigidoA='S' OR  DirigidoA='T')";
        } elseif ($IDUsuario > 0) {

            $condicionDirigidoA = " AND (DirigidoA='E' OR  DirigidoA='T')";
        }
 */


        $sql = "SELECT IDCategoriaObjetosPrestados,NombreCategoriaObjeto,IDClub,DisponiblesEnStock,Entregadas FROM CategoriaObjetosPrestados  WHERE IDClub = '" . $IDClub . "' AND IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND Publicar='S' ";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoriaDetalle["IDClub"] = $r["IDClub"];
                $categoriaDetalle["IDCategoriaObjeto"] = $r["IDCategoriaObjetosPrestados"];
                $categoriaDetalle["NombreCategoriaObjeto"] = $r["NombreCategoriaObjeto"];
                $categoriaDetalle["DisponiblesEnStock"] = $r["DisponiblesEnStock"];
                $categoriaDetalle["Entregadas"] = $r["Entregadas"];
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $categoriaDetalle;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function  get_lugares_entrega_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto)
    {
        $dbo = &SIMDB::get();




        $response = array();
        $sql = "SELECT Nombre,IDLugarObjetosPrestados FROM LugarObjetosPrestados  WHERE IDClub='" . $IDClub . "' AND (IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' OR IDCategoriaObjetosPrestados='0') AND Publicar='S'";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $LugarObjetosPrestados["IDLugarEntrega"] = $r["IDLugarObjetosPrestados"];
                $LugarObjetosPrestados["Nombre"] = $r["Nombre"];



                array_push($response, $LugarObjetosPrestados);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No hay ningun lugar.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_lista_socios_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag)
    {
        $dbo = &SIMDB::get();


        /*    if ($IDSocio > 0) {
            $campo = "IDSocio";
            $Tabla = "Socio";
        } elseif ($IDUsuario > 0) {
            $campo = "IDUsuario";
            $Tabla = "Usuario";
        } */

        if (!empty($Tag)) {

            $condicion = " AND (Nombre LIKE '%" . $Tag . "%' OR NumeroDocumento LIKE '%" . $Tag . "%' OR Accion LIKE '%" . $Tag . "%')";
        }



        $response = array();
        $sql = "SELECT IDSocio,NumeroDocumento,Nombre,Apellido FROM  Socio  WHERE IDClub='" . $IDClub . "' $condicion";
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $ListaSocio["IDSocioPrestamo"] = $r["IDSocio"];
                $ListaSocio["Nombre"] = $r["Nombre"] . " " . $r["Apellido"];
                $ListaSocio["Documento"] = $r["NumeroDocumento"];



                array_push($response, $ListaSocio);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontro ningun socio";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }


    public function set_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDSocioPrestamo, $IDLugarEntrega, $IDCategoriaObjeto)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocioPrestamo) && !empty($Cantidad) && !empty($IDLugarEntrega) && !empty($IDCategoriaObjeto)) {

            $Fecha = date("Y-m-d");
            //LAGUNITA
            if ($IDClub == 141) {
                //Venezuela
                date_default_timezone_set('America/Caracas');
                $FechaActual = date("Y-m-d H:i:s");
            } else {
                $FechaActual = date("Y-m-d H:i:s");
            }

            //PARA LAGUNITA VALIDO QUE HAYAN ENTREGADO TODO LO PRESTADO EL DIA ANTERIOR
            if ($IDClub == 141 || $IDClub == 8) {

                $fechaAnterior = strtotime('-1 day', strtotime($Fecha));
                $fechaAnterior = date('Y-m-j', $fechaAnterior);
                $sql_cantidad_pendiente = "SELECT CantidadPrestada,CantidadPendiente FROM ObjetosPrestados WHERE IDSocioPrestamo='$IDSocioPrestamo' AND IDClub='$IDClub' AND IDCategoriaObjetosPrestados='$IDCategoriaObjeto' AND Estado='1' AND DATE(FechaTrCr)<='$fechaAnterior'";
                $qry_cantidad_pendiente = $dbo->query($sql_cantidad_pendiente);
                if ($dbo->rows($qry_cantidad_pendiente) > 0) {
                    $respuesta["message"] = "No puede hacer prestamos el dia de hoy debido a que tiene cantidades pendientes que no se entregaron en dias anteriores.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }



            //VERIFICAR LA CANTIDAD MAXIMA A PRESTAR
            $CantidadMaxima = $dbo->getFields("CategoriaObjetosPrestados", "CantidadMaxima", "IDCategoriaObjetosPrestados = '" . $IDCategoriaObjeto . "' and IDClub = '" . $IDClub . "'");

            if ($CantidadMaxima > 0) {

                //CONSULTO CANTIDAD QUE EL SOCIO TIENE PRESTADO
                $sql_cantidad_prestada = "SELECT CantidadPrestada,CantidadPendiente FROM ObjetosPrestados WHERE IDSocioPrestamo='$IDSocioPrestamo' AND IDClub='$IDClub' AND IDCategoriaObjetosPrestados='$IDCategoriaObjeto' AND Estado='1'";

                $result_cantidad_prestada = $dbo->query($sql_cantidad_prestada);
                $TotalCantidadPrestada = 0;
                while ($DatosPrestados = $dbo->fetchArray($result_cantidad_prestada)) {
                    $TotalCantidadPrestada += $DatosPrestados["CantidadPrestada"] - $DatosPrestados["CantidadPendiente"];
                }

                //SUMO LA CANTIDAD A PRESTAR CON LA QUE EL SOCIO YA PRESTO
                $Total = $TotalCantidadPrestada + $Cantidad;
                if ($Total > $CantidadMaxima) {
                    $respuesta["message"] = "Supero la cantidad maxima prestada, cantidad actual prestada:" . $TotalCantidadPrestada . " y solo se pueden prestar:" . $CantidadMaxima;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }





            //consulto la disponibilidad que no sea menor a la solicitada
            $sql_consultar_disponibilidad = "SELECT DisponiblesEnStock,Entregadas FROM CategoriaObjetosPrestados WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "'";
            $result_disponibilidad = $dbo->query($sql_consultar_disponibilidad);
            $row_disponibilidad = $dbo->fetchArray($result_disponibilidad);

            if ($Cantidad <= $row_disponibilidad["DisponiblesEnStock"]) {
                //saco nombre del producto entregado
                $NombreProductoEntregado = $dbo->getFields("CategoriaObjetosPrestados", "NombreCategoriaObjeto", "IDCategoriaObjetosPrestados = '" . $IDCategoriaObjeto . "' and IDClub = '" . $IDClub . "'");


                //inserto el objeto prestado
                $sql_objeto_prestado = $dbo->query("INSERT INTO ObjetosPrestados (IDUsuario, IDSocio, IDClub, IDSocioPrestamo, IDCategoriaObjetosPrestados, IDLugarObjetosPrestados, Nombre, CantidadPrestada, Estado,FechaTrCr	)
                                     Values ('" . $IDUsuario . "','" . $IDSocio . "','" . $IDClub . "','"  . $IDSocioPrestamo . "','" . $IDCategoriaObjeto . "','" . $IDLugarEntrega . "','" . $NombreProductoEntregado . "'
                                             ,'" . $Cantidad . "','1','$FechaActual')");


                //actualizo los entregados y los pendientes por entregar del stock
                $DisponiblesEnStock = (int) $row_disponibilidad["DisponiblesEnStock"] - $Cantidad;
                $Entregadas = (int) $row_disponibilidad["Entregadas"] + $Cantidad;

                $update_disponibilidad = $dbo->query("UPDATE CategoriaObjetosPrestados SET DisponiblesEnStock='" . $DisponiblesEnStock . "',Entregadas='" . $Entregadas . "' WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "'");

                $respuesta["message"] = "Objeto prestado";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {

                $respuesta["message"] = "No hay disponibilidad en el stock, cantidad que hay:" . $row_disponibilidad["DisponiblesEnStock"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_objetos_prestados_pendientes_administrador($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag)
    {
        $dbo = &SIMDB::get();


        if (!empty($Tag)) {

            $condicion = " AND (S.Nombre LIKE '%" . $Tag . "%' OR S.NumeroDocumento LIKE '%" . $Tag . "%' OR S.Accion LIKE '%" . $Tag . "%')";
        }


        $response = array();
        $sql = "SELECT OP.Nombre,OP.CantidadPrestada,OP.IDLugarObjetosPrestados,OP.Estado,OP.IDObjetosPrestados,OP.IDSocioPrestamo,OP.CantidadEntregada FROM ObjetosPrestados OP,Socio S WHERE OP.IDSocioPrestamo=S.IDSocio AND OP.IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND OP.Estado='1' AND OP.IDClub='" . $IDClub . "' $condicion";
        //echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                //datos socio
                $datos_socio = $dbo->fetchAll('Socio', 'IDSocio ="' . $r["IDSocioPrestamo"] . '" AND IDClub="' . $IDClub . '"', 'array');

                $ObjetosPrestadosAdministrador["IDObjetoPrestamo"] = $r["IDObjetosPrestados"];
                $ObjetosPrestadosAdministrador["NombreObjeto"] = $r["Nombre"];
                $ObjetosPrestadosAdministrador["NombreSocio"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $ObjetosPrestadosAdministrador["DocumentoSocio"] = $datos_socio["NumeroDocumento"];
                //CANTIDAD PRESTADA
                $CantidadPrestada = $r["CantidadPrestada"] - $r["CantidadEntregada"];
                $ObjetosPrestadosAdministrador["CantidadPrestada"] = $CantidadPrestada;
                $ObjetosPrestadosAdministrador["Estado"] = SIMResources::$estado_objeto_prestado[$r["Estado"]];



                array_push($response, $ObjetosPrestadosAdministrador);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_objetos_prestados_entregados_administrador($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag)
    {
        $dbo = &SIMDB::get();


        if (!empty($Tag)) {

            $condicion = " AND (S.Nombre LIKE '%" . $Tag . "%' OR S.NumeroDocumento LIKE '%" . $Tag . "%' OR S.Accion LIKE '%" . $Tag . "%')";
        }


        $response = array();
        $sql = "SELECT OP.Nombre,OP.CantidadPrestada,OP.IDLugarObjetosPrestados,OP.Estado,OP.IDObjetosPrestados,OP.IDSocioPrestamo,OP.CantidadEntregada,OP.CantidadPendiente FROM ObjetosPrestados OP,Socio S WHERE OP.IDSocioPrestamo=S.IDSocio AND OP.IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND OP.Estado='2' AND OP.IDClub='" . $IDClub . "' $condicion";
        //echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                //datos socio
                $datos_socio = $dbo->fetchAll('Socio', 'IDSocio ="' . $r["IDSocioPrestamo"] . '" AND IDClub="' . $IDClub . '"', 'array');

                $ObjetosPrestadosAdministrador["IDObjetoPrestamo"] = $r["IDObjetosPrestados"];
                $ObjetosPrestadosAdministrador["NombreObjeto"] = $r["Nombre"];
                $ObjetosPrestadosAdministrador["NombreSocio"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $ObjetosPrestadosAdministrador["DocumentoSocio"] = $datos_socio["NumeroDocumento"];
                $ObjetosPrestadosAdministrador["CantidadEntregada"] = $r["CantidadEntregada"];
                $ObjetosPrestadosAdministrador["CantidadPendiente"] = $r["CantidadPendiente"];
                $ObjetosPrestadosAdministrador["Estado"] = SIMResources::$estado_objeto_prestado[$r["Estado"]];



                array_push($response, $ObjetosPrestadosAdministrador);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_devolucion_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDCategoriaObjeto, $IDObjetoPrestamo)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub)  && !empty($Cantidad)  && !empty($IDCategoriaObjeto)) {

            //LAGUNITA
            if ($IDClub == 141) {
                //Venezuela
                date_default_timezone_set('America/Caracas');
                $FechaActual = date("Y-m-d H:i:s");
            } else {
                $FechaActual = date("Y-m-d H:i:s");
            }


            //consulto la disponibilidad que no sea menor a la solicitada
            $sql_consultar_prestamo = "SELECT * FROM ObjetosPrestados WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "' AND IDObjetosPrestados='" . $IDObjetoPrestamo . "'";
            $result_prestamo = $dbo->query($sql_consultar_prestamo);
            $row_prestamo = $dbo->fetchArray($result_prestamo);

            if ($Cantidad <= $row_prestamo["CantidadPrestada"]) {

                //si la cantidad prestada es igual a la cantidad que el socio va a entregar actualizo para que no quede pendiente si no sigue en pendiente
                if ($Cantidad == $row_prestamo["CantidadPrestada"]) {
                    $Estado = "2";
                } elseif ($Cantidad == $row_prestamo["CantidadPendiente"]) {
                    $Estado = "2";
                } else {
                    $Estado = "1";
                }

                //actualizo los objetos prestados
                $cantidadEntregada = (int)$row_prestamo["CantidadEntregada"] + $Cantidad;
                $cantidadPendiente = (int)$row_prestamo["CantidadPrestada"] - $cantidadEntregada;
                $update_prestamo = $dbo->query("UPDATE ObjetosPrestados SET CantidadPendiente='" . $cantidadPendiente . "',cantidadEntregada='" . $cantidadEntregada . "',IDUsuarioRecibe='" . $IDUsuario . "',Estado='$Estado' WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "' AND IDObjetosPrestados='" . $IDObjetoPrestamo . "'");



                //actualizo los entregados y los pendientes por entregar del stock tabla categoriasobjetosprestados
                $sql_consultar_disponibilidad = "SELECT DisponiblesEnStock,Entregadas FROM CategoriaObjetosPrestados WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "'";
                $result_disponibilidad = $dbo->query($sql_consultar_disponibilidad);
                $row_disponibilidad = $dbo->fetchArray($result_disponibilidad);

                $DisponiblesEnStock = (int) $row_disponibilidad["DisponiblesEnStock"] + $Cantidad;
                $Entregadas = (int) $row_disponibilidad["Entregadas"] - $Cantidad;

                $update_disponibilidad = $dbo->query("UPDATE CategoriaObjetosPrestados SET DisponiblesEnStock='" . $DisponiblesEnStock . "',Entregadas='" . $Entregadas . "' WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "'");

                //ACTUALIZO FECHA  DE DEVOLUCION
                $update_fecha_devolucion = $dbo->query("UPDATE ObjetosPrestados SET FechaDevolucion='$FechaActual' WHERE IDCategoriaObjetosPrestados='" . $IDCategoriaObjeto . "' AND IDClub='" . $IDClub . "' AND IDObjetosPrestados='" . $IDObjetoPrestamo . "'");

                $respuesta["message"] = "Objeto guardado con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {

                $respuesta["message"] = "Cantidad a entregar es mayor a la cantidad que se presto.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
}
