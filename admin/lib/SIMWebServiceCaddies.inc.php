<?php

class SIMWebServiceCaddies
{
    public function get_categorias_caddies($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDElemento,$IDClubAsociado = "")
    {
        $dbo = SIMDB::get();
        $response = array();

        if(!empty($IDClubAsociado)):
            $IDClub = $IDClubAsociado;
        endif;

        $SQLCategorias = "SELECT * FROM CategoriaCaddie2 WHERE IDClub = '$IDClub' AND IDServicio = $IDServicio";
        $QRYCategorias = $dbo->query($SQLCategorias);

        if ($dbo->rows($QRYCategorias) > 0) :
            $message = $dbo->rows($QRYCategorias) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($Datos = $dbo->fetchArray($QRYCategorias)) :

                $Categoria[IDCategoria] = $Datos[IDCategoriaCaddie];
                $Categoria[Categoria] = $Datos[Categoria];

                array_push($response, $Categoria);
            endwhile;

            $respuesta[success] = true;
            $respuesta[message] = $message;
            $respuesta[response] = $response;

        else :
            $respuesta[success] = false;
            $respuesta[message] = SIMUtil::get_traduccion('', '', 'Nohaycategoriasdecaddies', LANG);
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function get_caddies_disponibles($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Hora, $IDElemento, $IDCategoria = "", $Tag = "",$IDClubAsociado = "")
    {
        $dbo = SIMDB::get();
        $Condicion = "";
        $response = array();
        $Dia = date("w", strtotime($Fecha));

        if(!empty($IDClubAsociado)):
            $IDClub = $IDClubAsociado;
        endif;

        // CREAMOS LAS CONCICIONES
        if (!empty($IDCategoria)) :
            $Condicion .= " AND IDCategoriaCaddie = $IDCategoria";
        endif;

        if (!empty($Tag)) :
            $Condicion .= " AND Nombre LIKE '%$Tag%'";
        endif;

        if (!empty($IDElemento)) :
            $Condicion .= " AND (IDElemento LIKE '%$IDElemento%' OR IDElemento = '')";
        endif;

        // CONSULTAMOS LOS CADDIES
        $SQLCaddies = "SELECT * FROM Caddie2 WHERE IDClub = $IDClub AND IDServicio = $IDServicio AND Activo = 'S' $Condicion";
        $QRYCaddies = $dbo->query($SQLCaddies);

        if ($dbo->rows($QRYCaddies)) :
            $message = $dbo->rows($QRYCategorias) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($Datos = $dbo->fetchArray($QRYCaddies)) :

                // VALIDAMOS QUE EL CADDIE ESTE DISPONIBLE EN ESAS FECHAS PARA ESE SERVICIO
                $SQLDisponibilidad = "SELECT CDD.* FROM CaddieDisponibilidadDetalle CDD, CaddieDisponibilidad CD WHERE 
                                                CDD.IDCaddieDisponibilidad = CD.IDCaddieDisponibilidad AND  
                                                CDD.IDServicio = '$IDServicio' AND 
                                                IDDia LIKE '%$Dia|%' AND IDCaddie LIKE '%$Datos[IDCaddie]|%'
                                                AND '$Hora' >= HoraDesde and '$Hora' <= HoraHasta 
                                                AND CD.Activo='S' ";

                $QRYDisponibilidad = $dbo->query($SQLDisponibilidad);

                if ($dbo->rows($QRYDisponibilidad) > 0) :
                    $Disponible = 'S';
                    // VALIDAMOS DISPONIBILIDAD POR RESERVA
                    $SQLReserva = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE Fecha = '$Fecha' AND Hora = '$Hora' AND IDCaddie = $Datos[IDCaddie]";
                    $QRYReserva = $dbo->query($SQLReserva);
                    $Data = $dbo->fetchArray($QRYReserva);

                    if (!empty($Data[IDReservaGeneral])) :
                        $Disponible = 'N';
                    endif;
                else :
                    $Disponible = 'N';
                endif;

                $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $Datos[IDCategoriaCaddie]");

                $Caddie[IDCaddie] = $Datos[IDCaddie];
                $Caddie[Nombre] = $Datos[Nombre];
                $Caddie[Categoria] = $datos_categoria[Categoria];
                $Caddie[IDCategoria] = $Datos[IDCategoriaCaddie];
                $Caddie[Precio] = $Datos[Precio];
                $Caddie[Disponible] = $Disponible;
                $Caddie[Texto] = $Datos[Descripcion];

                array_push($response, $Caddie);

            endwhile;

            $respuesta[success] = true;
            $respuesta[message] = $message;
            $respuesta[response] = $response;

        else :
            $respuesta[success] = false;
            $respuesta[message] = SIMUtil::get_traduccion('', '', 'Nohaycategoriasdecaddies', LANG);
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }
}
