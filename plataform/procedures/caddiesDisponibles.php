<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "Talega",
    "table" => "Talega",
    "key" => "IDTalega",
    "mod" => "administrarTalega"
));



//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$script = "caddiesDisponibles";

switch (SIMNet::req("action")) {

    case "search" :

        $view = "views/" . $script . "/list.php";

        $condicion = "";
        if ($_GET["qryString"] != "") {
            $condicion = " AND (c.nombre LIKE '%" . $_GET["qryString"] . "%' "
                    . " OR c.apellido LIKE '%" . $_GET["qryString"] . "%' "
                    . " OR c.Codigo LIKE '%" . $_GET["qryString"] . "%' "
                    . " OR c.numeroDocumento LIKE '%" . $_GET["qryString"] . "%' ) ";
        }

        $sql = "SELECT scd.IDCategoriaCaddie, cc.nombre AS categoria, c.nombre, c.apellido, c.Codigo, "
                . "c.numeroDocumento, scd.estado, c.IDCaddie, s.IDSorteoCaddie, scd.IDSorteoCaddieDetalle, "
                . "t.codigo AS codigoTalega "
                . "from SorteoCaddie s "
                . "INNER JOIN ( SELECT MAX(IDSorteoCaddie) AS maximoId "
                . "             FROM SorteoCaddie "
                . "             WHERE IDClub = " . SIMUser::get("club") . " "
                . "             AND DATE_FORMAT(NOW(),'%Y-%m-%d') BETWEEN fechaInicio AND fechaFin "
                . "             ) usor ON(s.IDSorteoCaddie = usor.maximoId) "
                . "INNER JOIN SorteoCaddieDetalle scd ON(s.IDSorteoCaddie = scd.IDSorteoCaddie) "
                . "INNER JOIN Caddie c ON(scd.IDCaddie = c.IDCaddie) "
                . "INNER JOIN CategoriaCaddie cc ON(scd.IDCategoriaCaddie = cc.IDCategoriaCaddie) "
                . "LEFT JOIN Talega t ON(scd.IDTalega = t.IDTalega) "
                . "WHERE s.IDClub = " . SIMUser::get("club") . " $condicion "
                . "ORDER BY cc.orden, scd.orden ASC ";

        $result = $dbo->query($sql);
        while ($row = $dbo->fetchArray($result)) {
            $frm["sorteo"][$row["IDCategoriaCaddie"]][] = $row;
            $idSorteo = $row["IDSorteoCaddie"];
        }

        $sql = "select IDPropiedadesTalega, nombre "
                . "from PropiedadesTalega "
                . "where IDClub = '" . SIMUser::get("club") . "' ";

        $result = $dbo->query($sql);
        $aPropiedades = [];
        while ($row = $dbo->fetchArray($result)) {
            $frm["propiedadesTalega"][] = $row;
        }

        $sql = "select c.IDCaddie, CONCAT_WS(' ',c.nombre, c.apellido) AS caddie, c.IDCategoriaCaddie,c.Codigo "
                . "from Caddie c "
                . "INNER JOIN RegistroCaddie rc ON(c.IDCaddie = rc.IDCaddie) "
                . "LEFT JOIN SorteoCaddieDetalle scd ON(c.IDCaddie = scd.IDCaddie AND scd.IDSorteoCaddie = $idSorteo) "
                . "LEFT JOIN SorteoCaddie sc ON(scd.IDSorteoCaddie = sc.IDSorteoCaddie) "
                . "WHERE c.IDClub = '" . SIMUser::get("club") . "' AND scd.IDCaddie IS NULL "
                . "AND DATE_FORMAT(rc.fechaRegistro,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') ";

        $result = $dbo->query($sql);
        while ($row = $dbo->fetchArray($result)) {
            $frm["caddies"][] = $row;
        }


        $newmode = "asociarSocio";


        break;



    case "asociarSocio" :

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $idSocio = $frm["IDSocio"];


            $sql = "select estado, IDCategoriaCaddie "
                    . "from SorteoCaddieDetalle "
                    . "where IDSorteoCaddieDetalle = '" . $frm["idSorteoCaddieDetalle"] . "' ";

            $result = $dbo->query($sql);
            $aDataTalega = $dbo->fetchArray($result);

            $estadoCaddie = 2;
            if ($aDataTalega["estado"] == 2 && $frm["regresa"] == 1) {


//                if ($frm["liberarCaddie"] != "")
                $estadoCaddie = 1;
//                else
//                    $estadoCaddie = 3;

                $aDataDetallaSorteo["IDTalega"] = "NULL";
                $idSocio = "NULL";
                if($frm["idTalega"] > 0)
                {
                $sql = "SELECT IDSocio "
                     . "FROM Talega "
                     . "WHERE IDTalega = ".$frm["idTalega"];

                $result = $dbo->query($sql);
                $aDataTalega = $dbo->fetchArray($result);
                $idSocio = $aDataTalega["IDSocio"];

                }
            }

            if ($estado == 2)
                $proceso = "la entrega al caddie";
            else
                $proceso = "el ingreso";

            $mensaje = "se ha realizado $proceso de su talega con la siguiente información: ";
            $aTextoPropiedades = [];

            //if ($estadoCaddie == 2) {
            //if ($aDataTalega["estado"] == 2) {

                $sql = "select MAX(orden) AS orden  "
                        . "from SorteoCaddieDetalle "
                        . "where IDSorteoCaddie = '" . $frm["idSorteoCaddie"] . "' "
                        . "AND IDCategoriaCaddie = " . $aDataTalega["IDCategoriaCaddie"];

                $result = $dbo->query($sql);
                $aDataMax = $dbo->fetchArray($result);
                $siguienteOrden = $aDataMax["orden"] + 1;
                $aDataDetallaSorteo["orden"] = $siguienteOrden;
                $aDataDetallaSorteo["IDTalega"] = $frm["idTalega"] > 0 ? $frm["idTalega"] : "NULL";
            //}

            $aDataDetallaSorteo["estado"] = $estadoCaddie;

            $dbo->update($aDataDetallaSorteo, "SorteoCaddieDetalle", "IDSorteoCaddieDetalle", $frm["idSorteoCaddieDetalle"]);


            $estadoTalega = 2;
            $idTalegaAdministracion = "NULL";

            if ($frm["idTalega"] > 0) {

                $sql = "select estado, IDSocio "
                        . "from Talega "
                        . "where IDClub = '" . SIMUser::get("club") . "' AND idTalega = " . $frm["idTalega"] . " ";
                $result = $dbo->query($sql);
                $aDataTalega = $dbo->fetchArray($result);
                if ($frm["regresa"] == "")
                    $idSocio = $aDataTalega["IDSocio"];
                if ($aDataTalega["estado"] == 2)
                    $estadoTalega = 1;

                $aTalega["estado"] = $estadoTalega;
                $idTalega = $dbo->update($aTalega, "Talega", "IDTalega", $frm["idTalega"]);

                $talegaAdministracion = array(
                    "IDTalega" => $frm["idTalega"],
                    "IDCaddie" => $frm["idCaddie"],
                    "numeroDocumentoTercero" => "",
                    "nombreTercero" => "",
                    "estado" => $estadoTalega,
                    "observaciones" => $frm["observaciones"],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

                $sql_delete = "DELETE FROM TalegaDetalle "
                        . "WHERE IDTalega = '" . $frm["idTalega"] . "' ";
                $qry_delete = $dbo->query($sql_delete);

                $aPropiedad = [];
                foreach ($frm["idPropiedad"] AS $index => $idPropiedad) {
                    $aPropiedadHistorico = array(
                        "IDTalegaAdministracion" => $idTalegaAdministracion,
                        "IDPropiedadesTalega" => $idPropiedad,
                        "valor" => $frm["propiedad"][$index],
                        "fechaRegistro" => date("Y-m-d H:i:s"),
                        "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $aTextoPropiedades[] = $frm["nombrePropiedad"][$index] . ": " . $frm["propiedad"][$index];

                    $aPropiedadDetalle = array(
                        "IDTalegaAdministracion" => $idTalegaAdministracion,
                        "IDTalega" => $frm["idTalega"],
                        "IDPropiedadesTalega" => $idPropiedad,
                        "valor" => $frm["propiedad"][$index],
                        "fechaRegistro" => date("Y-m-d H:i:s"),
                        "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );
                    $dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
                    $dbo->insert($aPropiedadDetalle, "TalegaDetalle", "IDTalegaDetalle");
                }
            }

            $aCaddieHistoricoAsignacion = array(
                "IDCaddie" => $frm["idCaddie"],
                "IDSocio" => $idSocio,
                //"IDSocio" => "NULL",
                "estado" => $estadoCaddie,
                "IDTalegaAdministracion" => $idTalegaAdministracion,
                "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                "fechaRegistro" => date("Y-m-d H:i:s"),
            );

            $idHistoricoAsignacion = $dbo->insert($aCaddieHistoricoAsignacion, "CaddieHistoricoAsignacion", "IDCaddieHistoricoAsignacion");


            if (count($aTextoPropiedades) > 0 && $frm["idTalega"] > 0 && $idSocio > 0) {
                $textoPropiedades = join(", ", $aTextoPropiedades);
                $mensaje = $mensaje . " " . $textoPropiedades;
                SIMUtil::enviar_notificacion_push_general(SIMUser::get("club"), $idSocio, $mensaje);
            }

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Asociacionrealizadacorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;

    case 'agregarCaddie' :


        $sql = "select MAX(ordenInicial) AS orden  "
                . "from SorteoCaddieDetalle "
                . "where IDSorteoCaddie = '" . $_POST['idSorteoCaddieAgregar'] . "' "
                . "AND IDCategoriaCaddie = " . $_POST['idCategoriaCaddieAgregar']." ";

        $result = $dbo->query($sql);
        $aDataMax = $dbo->fetchArray($result);
        if ($aDataMax != null)
            $siguienteOrden = $aDataMax["orden"] + 1;
        else
            $siguienteOrden = 0;

        $aSorteoDetalle = array(
            "IDSorteoCaddie" => $_POST['idSorteoCaddieAgregar'],
            "IDCaddie" => $_POST['caddieAsignar'],
            "IDCategoriaCaddie" => $_POST['idCategoriaCaddieAgregar'],
            "orden" => $siguienteOrden,
            "estado" => 1,
            "fechaRegistro" => SIMUser::get("IDUsuario"),
            "idUsuarioRegistra" => date("Y-m-d H:i:s"),
        );

        $dbo->insert($aSorteoDetalle, "SorteoCaddieDetalle", "IDSorteoCaddieDetalle");

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'caddieagregadoconéxito', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php");

        break;

    default;
        $view = "views/" . $script . "/list.php";

        $sql = "SELECT scd.IDCategoriaCaddie, cc.nombre AS categoria, c.nombre, c.apellido, c.Codigo,"
                . "c.numeroDocumento, scd.estado, c.IDCaddie, s.IDSorteoCaddie, scd.IDSorteoCaddieDetalle, "
                . "t.codigo AS codigoTalega "
                . "from SorteoCaddie s "
                . "INNER JOIN ( SELECT MAX(IDSorteoCaddie) AS maximoId "
                . "             FROM SorteoCaddie "
                . "             WHERE IDClub = " . SIMUser::get("club") . " "
                . "             AND DATE_FORMAT(NOW(),'%Y-%m-%d') BETWEEN fechaInicio AND fechaFin "
                . "             ) usor ON(s.IDSorteoCaddie = usor.maximoId) "
                . "INNER JOIN SorteoCaddieDetalle scd ON(s.IDSorteoCaddie = scd.IDSorteoCaddie) "
                . "INNER JOIN Caddie c ON(scd.IDCaddie = c.IDCaddie) "
                . "INNER JOIN CategoriaCaddie cc ON(scd.IDCategoriaCaddie = cc.IDCategoriaCaddie) "
                . "LEFT JOIN Talega t ON(scd.IDTalega = t.IDTalega) "
                . "ORDER BY cc.orden, scd.orden ASC ";

        $result = $dbo->query($sql);
        while ($row = $dbo->fetchArray($result)) {
            $frm["sorteo"][$row["IDCategoriaCaddie"]][] = $row;
            $idSorteo = $row["IDSorteoCaddie"];
        }

        $sql = "select IDPropiedadesTalega, nombre "
                . "from PropiedadesTalega "
                . "where IDClub = '" . SIMUser::get("club") . "' ";

        $result = $dbo->query($sql);
        $aPropiedades = [];
        while ($row = $dbo->fetchArray($result)) {
            $frm["propiedadesTalega"][] = $row;
        }

        $sql = "select c.IDCaddie, CONCAT_WS(' ',c.nombre, c.apellido) AS caddie, c.IDCategoriaCaddie,C.Codigo "
                . "from Caddie c "
                . "INNER JOIN RegistroCaddie rc ON(c.IDCaddie = rc.IDCaddie) "
                . "LEFT JOIN SorteoCaddieDetalle scd ON(c.IDCaddie = scd.IDCaddie AND scd.IDSorteoCaddie = $idSorteo) "
                . "LEFT JOIN SorteoCaddie sc ON(scd.IDSorteoCaddie = sc.IDSorteoCaddie) "
                . "WHERE c.IDClub = '" . SIMUser::get("club") . "' AND scd.IDCaddie IS NULL "
                . "AND DATE_FORMAT(rc.fechaRegistro,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') ";

        $result = $dbo->query($sql);
        while ($row = $dbo->fetchArray($result)) {
            $frm["caddies"][] = $row;
        }


        $newmode = "asociarSocio";

        break;
} // End switch

if (empty($view))
    $view = "views/" . $script . "/list.php";
