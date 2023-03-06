<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "SorteoCaddie",
    "table" => "SorteoCaddie",
    "key" => "IDSorteoCaddie",
    "mod" => "SorteoCaddie"
));

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);
$script = "sorteoCaddie";


switch (SIMNet::req("action")) {

    case "realizarSorteo":

        $mail = new phpmailer();
        $frm = $_POST;

        $sql = "SELECT c.IDCaddie, c.numeroDocumento, c.nombre, c.apellido, c.IDCategoriaCaddie, cc.nombre AS categoria "
            . "FROM RegistroCaddie r "
            . "INNER JOIN Caddie c ON(r.IDCaddie = c.IDCaddie)"
            . "INNER JOIN CategoriaCaddie cc ON(c.IDCategoriaCaddie = cc.IDCategoriaCaddie)"
            . "WHERE DATE_FORMAT(r.fechaRegistro,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') "
            . "AND c.IDClub = " . SIMUser::get("club") . " "
            . "ORDER BY cc.orden ASC ";
        $result = $dbo->query($sql);



        if ($dbo->rows($result) > 0) {


            $aDataSorteo["fechaInicio"] = $frm["fechaInicio"];
            $aDataSorteo["fechaFin"] = $frm["fechaFin"];
            $aDataSorteo["fechaRegistro"] = date("Y-m-d H:i:s");
            $aDataSorteo["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
            $aDataSorteo["IDClub"] = SIMUser::get("club");

            $idSorteo = $dbo->insert($aDataSorteo, $table, $key);

            $html = 'Hola Buen dia<br><br> '
                . 'El usuario <b>' . SIMUser::get("Nombre") . '</b> realizo un sorteo con fecha y hora <b>' . date("Y-m-d H:i:s") . '</b> con fecha de inicio <b>' . $frm["fechaInicio"] . "</b> "
                . "y fecha de fin <b>" . $frm["fechaFin"] . "</b> el cual arrojo como resultado:<br><br> ";
            $aCorreo = '';

            while ($row = $dbo->fetchArray($result)) {
                $frmNew[$row["IDCategoriaCaddie"]][] = $row;
            }

            $newForm = [];
            foreach ($frmNew as $index => $categoria) {
                $numeroCaddies = count($categoria);
                $llaves = [];
                for ($i = 0; $i < $numeroCaddies; $i++) {
                    $llaves[] = $i;
                }

                $numeroLlaves = count($llaves);
                while ($numeroLlaves > 0) {
                    $rand = rand(0, ($numeroLlaves - 1));
                    $newForm[$index][] = $categoria[$llaves[$rand]];
                    unset($llaves[$rand]);
                    $llaves = array_values($llaves);
                    $numeroLlaves = count($llaves);
                }
            }


            foreach ($newForm as $idCategoria => $dataCaddiesCategoria) {

                foreach ($dataCaddiesCategoria as $index => $dataCaddies) {

                    $aDataDetalle = [];
                    $aDataDetalle["IDSorteoCaddie"] = $idSorteo;
                    $aDataDetalle["IDCaddie"] = $dataCaddies["IDCaddie"];
                    $aDataDetalle["IDCategoriaCaddie"] = $idCategoria;
                    $aDataDetalle["orden"] = $index;
                    $aDataDetalle["ordenInicial"] = $index;
                    $aDataDetalle["estado"] = 1;
                    $aDataDetalle["IDTalega"] = "NULL";
                    $aDataDetalle["fechaRegistro"] = date("Y-m-d H:i:s");
                    $aDataDetalle["idUsuarioRegistra"] = SIMUser::get("IDUsuario");


                    $id = $dbo->insert($aDataDetalle, "SorteoCaddieDetalle", "IDSorteoCaddieDetalle");
                    $aCorreo = array();
                    $aCorreo[$idCategoria]["categoria"] = $dataCaddies["categoria"];
                    $aCorreo[$idCategoria]["data"][] = array(
                        "numeroDocumentoCaddie" => $dataCaddies["numeroDocumento"],
                        "nombreCaddie" => $dataCaddies["nombre"],
                        "apellidoCaddie" => $dataCaddies["apellido"],
                        "categoriaCaddie" => $dataCaddies["categoria"],
                        "idCategoriaCaddie" => $dataCaddies["IDCategoriaCaddie"],
                    );
                }
            }



            foreach ($aCorreo as $index => $aData) {
                $html .= '<h4>' . $aData["categoria"] . '</h4>'
                    . '<table style="width: 100%;border: #000 1px solid;text-align: center;">'
                    . '<thead>'
                    . '<tr style="background: #3fb0ac;height: 30px;color: #FFF;text-align: center;">'
                    . '<th style="width: 20%;text-align: center;">Numero documento</th>'
                    . '<th style="text-align: center;">Nombre</th>'
                    . '<th style="text-align: center;">Apellido</th>'
                    . '</tr>'
                    . '</thead>'
                    . '<tbody>';

                foreach ($aData["data"] as $index2 => $data) {

                    $html .= '<tr>'
                        . '<td style="text-align: center;">' . $data["numeroDocumentoCaddie"] . '</td>'
                        . '<td style="text-align: center;">' . $data["nombreCaddie"] . '</td>'
                        . '<td style="text-align: center;">' . $data["apellidoCaddie"] . '</td>'
                        . '</tr>';
                }
                $html .= '</tbody>'
                    . '</table>'
                    . '<br><br>';
            }

            $sql = "SELECT correoNotificaciones "
                . "FROM Club  "
                . "WHERE IDClub = " . SIMUser::get("club");

            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            if ($row["correoNotificaciones"] != "") {
                $mail->AddAddress($row["correoNotificaciones"]);
                $mail->Subject = "Sorteo de caddie realizado ";
                $mail->Body = $html;
                $mail->IsHTML(true);
                $mail->Sender = "info@miclubapp.com";
                $mail->Timeout = 120;
                $mail->Host = "localhost";
                $mail->Mailer = 'smtp';
                $mail->Password = 's0luci0nes#A';
                $mail->Username = 'postmater@correosim.com';
                $mail->SMTPAuth = false;
                $mail->From = "info@miclubapp.com";
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:info@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Sorteorealizadoconéxito', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=ver&id=" . $idSorteo);
        } else {

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Nohaycaddiesregistradospararealizarelsorteo', LANGSESSION));
        }

        break;


    case 'ver':

        //$view = "views/" . $script . "/list.php";

        $sql = "SELECT scd.IDCategoriaCaddie, cc.nombre AS categoria, c.nombre, c.apellido, "
            . "c.numeroDocumento, scd.estado, c.IDCaddie, s.IDSorteoCaddie, scd.IDSorteoCaddieDetalle, "
            . "t.codigo AS codigoTalega "
            . "from SorteoCaddie s "
            . "INNER JOIN ( SELECT MAX(IDSorteoCaddie) AS maximoId "
            . "             FROM SorteoCaddie "
            . "             WHERE IDSorteoCaddie = " . $_GET["id"] . " "
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
        }

        $view = "views/sorteoCaddie/form.php";

        break;
} // End switch



if (empty($view))
    $view = "views/sorteoCaddie/list.php";
