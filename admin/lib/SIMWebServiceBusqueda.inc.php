<?php

class SIMWebServiceBusqueda
{

    public function buscar_contenido_en_modulos($IDClub, $IDUsuario, $IDSocio, $Tag = '')
    {
        $dbo = SIMDB::get();
        $response = array();
        $modulo = array();
        $conditionSeccion = "";
        $conditionNoticia = "";
        $conditionEvento = "";
        $conditionGaleria = "";
        if ($Tag != "") {
            $conditionSeccion = " AND (CM.Titulo LIKE UCASE('%" . $Tag . "%') OR M.Nombre LIKE UCASE('%" . $Tag . "%'))";
            $conditionNoticia = " AND (Titular LIKE UCASE('%" . $Tag . "%') OR Cuerpo LIKE UCASE('%" . $Tag . "%'))";
            $conditionEvento = " AND (Titular LIKE UCASE('%" . $Tag . "%') OR Cuerpo LIKE UCASE('%" . $Tag . "%'))";
            $conditionGaleria = " AND (Nombre LIKE UCASE('%" . $Tag . "%') OR Descripcion LIKE UCASE('%" . $Tag . "%'))";
        }

        if (isset($IDSocio) && $IDSocio) {
            $conditionNoticia .= " AND (DirigidoA = 'S' or DirigidoA = 'T') ";
            $conditionEvento .= " AND (DirigidoA = 'S' or DirigidoA = 'T') ";
            $conditionGaleria .= " AND (DirigidoA = 'S' or DirigidoA = 'T') ";
            $sql_modulo = "SELECT CM.IDModulo, CM.Icono, CM.Titulo, M.Nombre FROM ClubModulo AS CM JOIN Modulo AS M ON CM.IDModulo = M.IDModulo WHERE CM.IDClub = '" . $IDClub . "' and CM.Activo = 'S' $conditionSeccion ORDER BY CM.Orden";
            $qry_modulo = $dbo->query($sql_modulo);
            if ($dbo->rows($qry_modulo) > 0) {
                while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                    $modulo["IDModulo"] = strval($r_modulo["IDModulo"]);
                    $modulo["IDSeccion"] = "";
                    $modulo["IDSubModulo"] = "";
                    $modulo["IDDetalle"] = "";
                    $modulo["Icono"] = "";
                    if ($r_modulo["Icono"] != '') {
                        $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                    }



                    $modulo["NombreBusqueda"] = $r_modulo["Titulo"];
                    if ($r_modulo["Titulo"] == '') {
                        $modulo["NombreBusqueda"] = $r_modulo["Nombre"];
                    }


                    array_push($response, $modulo);
                }
            }
        } else {
            $conditionNoticia .= " AND (DirigidoA = 'E' or DirigidoA = 'T') ";
            $conditionEvento .= " AND (DirigidoA = 'E' or DirigidoA = 'T') ";
            $conditionGaleria .= " AND (DirigidoA = 'E' or DirigidoA = 'T') ";
            $sql_modulo = "SELECT CM.IDModulo, CM.Icono, CM.Titulo, M.Nombre FROM AppEmpleadoModulo AS CM JOIN Modulo AS M ON CM.IDModulo = M.IDModulo WHERE CM.IDClub = '" . $IDClub . "' and CM.Activo = 'S' $conditionSeccion ORDER BY CM.Orden";
            $qry_modulo = $dbo->query($sql_modulo);
            if ($dbo->rows($qry_modulo) > 0) {
                while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                    $modulo["IDModulo"] = strval($r_modulo["IDModulo"]);
                    $modulo["IDSeccion"] = "";
                    $modulo["IDSubModulo"] = "";
                    $modulo["IDDetalle"] = "";
                    $modulo["Icono"] = "";
                    if ($r_modulo["Icono"] != '') {
                        $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                    }
                    $modulo["NombreBusqueda"] = $r_modulo["Titulo"];
                    if ($r_modulo["Titulo"] == '') {
                        $modulo["NombreBusqueda"] = $r_modulo["Nombre"];
                    }

                    array_push($response, $modulo);
                }
            }
        }
        if ($Tag != "") {
            $sql = "SELECT `IDEvento` as 'ID' ,`IDSeccionEvento` AS 'IDSeccion',`Titular`,`SubTitular`,`Cuerpo`, '4' as 'IDModulo' FROM `Evento` WHERE Publicar = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() AND `IDClub` = $IDClub $conditionEvento UNION SELECT `IDEvento2` as 'ID' ,`IDSeccionEvento2` AS 'IDSeccion',`Titular`,`SubTitular`,`Cuerpo`, '76' as 'IDModulo' FROM `Evento2` WHERE Publicar = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() AND `IDClub` = $IDClub $conditionEvento;";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                while ($r = $dbo->fetchArray($qry)) {

                    $sql_modulop = "SELECT IDSubModulo,IDModulo FROM SubModulo WHERE IDClub = '" . $IDClub . "' and IDSeccionEvento = '" . $r["IDSeccion"] . "' Limit 1";
                    $qry_modulop = $dbo->query($sql_modulop);
                    $r_modulop = $dbo->fetchArray($qry_modulop);
                    if ($dbo->rows($qry_modulop) > 0 && $r_modulop['IDModulo']) {
                        $modulo["IDModulo"] = strval($r_modulop["IDModulo"]);
                        $modulo["IDSubModulo"] = strval($r["IDModulo"]);
                    } else {
                        $modulo["IDModulo"] = strval($r["IDModulo"]);
                        $modulo["IDSubModulo"] = "";
                    }

                    if (isset($IDSocio) && $IDSocio) {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' Limit 1";
                    } else {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' $conditionSeccion ORDER BY Orden";
                    }
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo["IDSeccion"] = $r["IDSeccion"];
                        $modulo["IDDetalle"] = $r["ID"];
                        $modulo["Icono"] = "";
                        if ($r_modulo["Icono"] != '') {
                            $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                        }
                        $modulo["NombreBusqueda"] = $r["Titular"];
                        array_push($response, $modulo);
                    }
                }
            }


            $sql = "SELECT `IDNoticia`,`IDSeccion`, `Titular`,`Cuerpo` FROM `Noticia` WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and Publicar = 'S' AND `IDClub` = '$IDClub' $conditionNoticia;";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                while ($r = $dbo->fetchArray($qry)) {


                    $sql_modulop = "SELECT IDSubModulo,IDModulo FROM SubModulo WHERE IDClub = '" . $IDClub . "' and IDSeccionNoticia = '" . $r["IDSeccion"] . "' Limit 1";
                    $qry_modulop = $dbo->query($sql_modulop);
                    $r_modulop = $dbo->fetchArray($qry_modulop);
                    if ($dbo->rows($qry_modulop) > 0 && $r_modulop['IDModulo']) {
                        $modulo["IDModulo"] = strval($r_modulop["IDModulo"]);
                        $modulo["IDSubModulo"] = strval(3);
                    } else {
                        $modulo["IDModulo"] = strval(3);
                        $modulo["IDSubModulo"] = "";
                    }

                    if (isset($IDSocio) && $IDSocio) {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' Limit 1";
                    } else {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' $conditionSeccion ORDER BY Orden";
                    }
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo["IDSeccion"] = $r["IDSeccion"];
                        $modulo["IDDetalle"] = $r["IDNoticia"];
                        $modulo["Icono"] = "";
                        if ($r_modulo["Icono"] != '') {
                            $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                        }
                        $modulo["NombreBusqueda"] = $r["Titular"];
                        array_push($response, $modulo);
                    }
                }
            }
            $sql = "SELECT `IDNoticia`,`IDSeccion`, `Titular`,`Cuerpo` FROM `Noticia2` WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and Publicar = 'S' AND `IDClub` = $IDClub $conditionNoticia;";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                while ($r = $dbo->fetchArray($qry)) {

                    $sql_modulop = "SELECT IDSubModulo,IDModulo FROM SubModulo WHERE IDClub = '" . $IDClub . "' and IDSeccionNoticia2 = '" . $r["IDSeccion"] . "' Limit 1";
                    $qry_modulop = $dbo->query($sql_modulop);
                    $r_modulop = $dbo->fetchArray($qry_modulop);
                    if ($dbo->rows($qry_modulop) > 0 && $r_modulop['IDModulo']) {
                        $modulo["IDModulo"] = strval($r_modulop["IDModulo"]);
                        $modulo["IDSubModulo"] = strval(66);
                    } else {
                        $modulo["IDModulo"] = strval(66);
                        $modulo["IDSubModulo"] = "";
                    }

                    if (isset($IDSocio) && $IDSocio) {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' Limit 1";
                    } else {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' $conditionSeccion ORDER BY Orden";
                    }
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo["IDSeccion"] = $r["IDSeccion"];
                        $modulo["IDDetalle"] = $r["IDNoticia"];
                        $modulo["Icono"] = "";
                        if ($r_modulo["Icono"] != '') {
                            $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                        }
                        $modulo["NombreBusqueda"] = $r["Titular"];
                        array_push($response, $modulo);
                    }
                }
            }
            $sql = "SELECT `IDNoticia`,`IDSeccion`, `Titular`,`Cuerpo` FROM `Noticia3` WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and Publicar = 'S' AND `IDClub` = $IDClub $conditionNoticia;";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                while ($r = $dbo->fetchArray($qry)) {

                    $sql_modulop = "SELECT IDSubModulo,IDModulo FROM SubModulo WHERE IDClub = '" . $IDClub . "' and IDSeccionNoticia3 = '" . $r["IDSeccion"] . "' Limit 1";
                    $qry_modulop = $dbo->query($sql_modulop);
                    $r_modulop = $dbo->fetchArray($qry_modulop);
                    if ($dbo->rows($qry_modulop) > 0 && $r_modulop['IDModulo']) {
                        $modulo["IDModulo"] = strval($r_modulop["IDModulo"]);
                        $modulo["IDSubModulo"] = strval(81);
                    } else {
                        $modulo["IDModulo"] = strval(81);
                        $modulo["IDSubModulo"] = "";
                    }

                    if (isset($IDSocio) && $IDSocio) {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' Limit 1";
                    } else {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' $conditionSeccion ORDER BY Orden";
                    }
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo["IDSeccion"] = $r["IDSeccion"];
                        $modulo["IDDetalle"] = $r["IDNoticia"];
                        $modulo["Icono"] = "";
                        if ($r_modulo["Icono"] != '') {
                            $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                        }
                        $modulo["NombreBusqueda"] = $r["Titular"];
                        array_push($response, $modulo);
                    }
                }
            }

            $sql = "SELECT `IDGaleria` as 'IDGaleria',`IDSeccionGaleria` as 'IDSeccion', `Nombre`,`Descripcion`, '5' as 'IDModulo' FROM `Galeria` WHERE Publicar = 'S' AND `IDClub` = $IDClub $conditionGaleria UNION SELECT `IDGaleria2` as 'IDGaleria',`IDSeccionGaleria2` as 'IDSeccion', `Nombre`,`Descripcion`, '150' as 'IDModulo'  FROM `Galeria2` WHERE Publicar = 'S' AND `IDClub` = $IDClub $conditionGaleria;";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                while ($r = $dbo->fetchArray($qry)) {
                    $sql_modulop = "SELECT IDSubModulo,IDModulo FROM SubModulo WHERE IDClub = '" . $IDClub . "' and IDSeccionGaleria = '" . $r["IDSeccion"] . "' Limit 1";
                    $qry_modulop = $dbo->query($sql_modulop);
                    $r_modulop = $dbo->fetchArray($qry_modulop);

                    if ($dbo->rows($qry_modulop) > 0 && $r_modulop['IDModulo']) {
                        $modulo["IDModulo"] = strval($r_modulop["IDModulo"]);
                        $modulo["IDSubModulo"] = strval($r["IDModulo"]);
                    } else {
                        $modulo["IDModulo"] = strval($r["IDModulo"]);
                        $modulo["IDSubModulo"] = "";
                    }

                    if (isset($IDSocio) && $IDSocio) {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' Limit 1";
                    } else {
                        $sql_modulo = "SELECT IDModulo, Icono, Titulo FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '" . $modulo["IDModulo"] . "' and Activo = 'S' $conditionSeccion ORDER BY Orden";
                    }
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        $r_modulo = $dbo->fetchArray($qry_modulo);
                        $modulo["IDSeccion"] = $r["IDSeccion"];
                        $modulo["IDDetalle"] = $r["IDGaleria"];
                        $modulo["Icono"] = "";
                        if ($r_modulo["Icono"] != '') {
                            $modulo["Icono"] = MODULO_ROOT . $r_modulo["Icono"];
                        }
                        $modulo["NombreBusqueda"] = $r["Nombre"];
                        array_push($response, $modulo);
                    }
                }
            }
        }

        $respuesta["message"] = 'Respuesta Correcta';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }
}
