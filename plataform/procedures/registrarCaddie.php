<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "RegistroCaddie",
    "table" => "RegistroCaddie",
    "key" => "IDRegistroCaddie",
    "mod" => "RegistroCaddie"
));



//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$script = "registrarCaddie";

switch (SIMNet::req("action")) {

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $sql = "select IDRegistroCaddie "
                . "from RegistroCaddie "
                . "where IDCaddie = '" . $_POST["ID"] . "' "
                . "AND DATE_FORMAT(fechaRegistro, '%Y-%m-%d') = '" . date('Y-m-d') . "' ";
            //. "AND IDClub = '".SIMUser::get("club")."' ";

            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);

            if ($row == "") {
                $frm["fechaRegistro"] = date("Y-m-d H:i:s");
                $frm["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
                $frm["IDCaddie"] = $_POST["ID"];
                //aca
                $id = $dbo->insert($frm, $table, $key);
                /* VALIDACION CUANDO YA HAY UN SORTEO LO AGREGA DE ULTIMO */

                $categoria = $dbo->getFields("Caddie", "IDCategoriaCaddie", "IDCaddie = " . $_POST["ID"]);

                $sql_sorteo = "   SELECT * 
                                    FROM SorteoCaddie 
                                    WHERE fechaInicio <= CURDATE() 
                                    AND fechaFin >= CURDATE() 
                                    AND IDClub = '" . $_POST["IDClub"] . "' 
                                    Order by IDSorteoCaddie DESC Limit 1";
                $result_sorteo = $dbo->query($sql_sorteo);


                if ($dbo->rows($result_sorteo) > 0) {
                    $row_sorteo = $dbo->fetchArray($result_sorteo);

                    $sql = "select MAX(orden) AS orden  "
                        . "from SorteoCaddieDetalle "
                        . "where IDSorteoCaddie = '" . $row_sorteo['IDSorteoCaddie'] . "' "
                        . "AND IDCategoriaCaddie = " . $categoria . " ";

                    $result = $dbo->query($sql);

                    $aDataMax = $dbo->fetchArray($result);

                    if ($aDataMax != null)
                        $siguienteOrden = $aDataMax["orden"] + 1;
                    else
                        $siguienteOrden = 0;

                    $aSorteoDetalle = array(
                        "IDSorteoCaddie" => $row_sorteo['IDSorteoCaddie'],
                        "IDCaddie" => $frm["IDCaddie"],
                        "IDCategoriaCaddie" => $categoria,
                        "orden" => $siguienteOrden,
                        "estado" => 1,
                        "fechaRegistro" => date("Y-m-d H:i:s"),
                        "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $dbo->insert($aSorteoDetalle, "SorteoCaddieDetalle", "IDSorteoCaddieDetalle");
                }

                /* FIN VALIDACION */


                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else {

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Elcaddieyaseencuentraregistrado', LANGSESSION)); //necesito

                SIMHTML::jsRedirect($script . ".php");
            }
        } else
            exit;


        break;

    case "search":


        $sql = "select c.*, cc.nombre AS categoria "
            . "from Caddie c "
            . "INNER JOIN CategoriaCaddie cc ON(c.IDCategoriaCaddie = cc.IDCategoriaCaddie) "
            . "where c.numeroDocumento = '" . $_GET["qryString"] . "'  AND c.IDClub = '" . SIMUser::get("club") . "' ";
        $result = $dbo->query($sql);
        $row = $dbo->fetchArray($result);
        $frm = $row;

        if ($frm == "") {
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Nohayuncaddieregistradoconestenumerodedocumento', LANGSESSION));

            SIMHTML::jsRedirect($script . ".php");
        }
        $newmode = "insert";
        //$titulo_accion = "Actualizar";
        break;
} // End switch

if (empty($view))
    $view = "views/registrarCaddie/form.php";
