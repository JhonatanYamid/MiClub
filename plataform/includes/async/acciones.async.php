<?php

header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache("text/json");
$dbo = & SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
if ($frm == "")
    $frm = SIMUtil::makeSafe($_GET);


switch ($frm["action"]) {

    case 'comprobarFechas':

        $fechaInicio = $_POST['fechaInicio'];
        $fechaFin = $_POST['fechaFin'];
        $sql = "SELECT IDSorteoCaddie "
                . "FROM SorteoCaddie "
                . "WHERE IDClub = " . SIMUser::get("club") . " "
                . "AND ( "
                . "fechaInicio BETWEEN '$fechaInicio' AND '$fechaFin' OR "
                . "fechaFin BETWEEN '$fechaInicio' AND '$fechaFin' OR "
                . "'$fechaInicio' BETWEEN fechaInicio AND fechaFin OR "
                . "'$fechaFin' BETWEEN fechaInicio AND fechaFin "
                . ")";

        $result = $dbo->query($sql);
        $row = $dbo->fetchArray($result);
        echo $row["IDSorteoCaddie"] == "" ? "0" : "1";

        break;

    case 'getTalegasSocio':


        $condicion = "";
        if($_POST['idSocio'] > 0)$condicion .= " AND IDSocio = ".$_POST['idSocio'];
        if($_POST['idInvitado'] > 0)
            {
                if($_POST['tipo'] == 1)$condicion .= " AND IDSocioInvitado = ".$_POST['idInvitado'];
                else if($_POST['tipo'] == 2)$condicion .= " AND IDInvitado = ".$_POST['idInvitado'];
            }

        $sql = "SELECT IDTalega, nombre, codigo "
                . "FROM Talega "
                . "WHERE IDClub = " . SIMUser::get("club") . " "
                . "$condicion AND estado = 1 ";

        $result = $dbo->query($sql);
        $aData = [];
        while ($row = $dbo->fetchArray($result)) {
            $aData[] = $row;
        }

        echo json_encode($aData);

        break;


    case 'getTalegaPropiedades':

        $codigoTalega = $_POST['codigoTalega'];
        $estadoTalega = $_POST['estadoTalega'];

        $sql = "SELECT t.IDTalega, td.idPropiedadesTalega, td.valor "
                . "FROM Talega t "
                . "INNER JOIN TalegaDetalle td ON(t.IDTalega = td.IDTalega) "
                . "WHERE t.IDClub = " . SIMUser::get("club") . " "
                . "AND t.codigo = '$codigoTalega' AND t.estado = $estadoTalega";


        $result = $dbo->query($sql);
        $aData = [];
        while ($row = $dbo->fetchArray($result)) {
            $aData[] = $row;
        }

        echo json_encode($aData);

        break;

    case 'CaddieHistoricoAsignacion':

        $idCaddie = $_POST['idCaddie'];

        $sql = "SELECT IF(t.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS socio, t.nombre AS talega, "
                . "ta.fechaRegistro, ta.observaciones "
                . "FROM CaddieHistoricoAsignacion ch "
                . "LEFT JOIN TalegaAdministracion ta ON(ch.IDTalegaAdministracion=ta.IDTalegaAdministracion) "
                . "LEFT JOIN Talega t ON(ta.IDTalega = t.IDTalega) "
                . "LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) "
                . "LEFT JOIN Invitado i ON(t.IDInvitado = i.IDInvitado) "
                . "LEFT JOIN SocioInvitado si ON(t.IDSocioInvitado = si.IDSocioInvitado) "
                . "WHERE ch.IDCaddie = " . $idCaddie . " "
                . "ORDER BY ch.IDCaddieHistoricoAsignacion DESC LIMIT 0, 1";

        $sql = "SELECT *,CONCAT_WS(' ',s.Nombre, s.Apellido) as socio "
                        . "FROM CaddieHistoricoAsignacion ch, Socio s "
                        . "WHERE ch.IDSocio=s.IDSocio and ch.IDCaddie = " . $idCaddie . " "
                        . "ORDER BY ch.IDCaddieHistoricoAsignacion DESC LIMIT 0, 1";


        $result = $dbo->query($sql);
        $aData = [];
        while ($row = $dbo->fetchArray($result)) {
            $row["Nombre"]=utf8_encode($row["Nombre"]);
            $row["Apellido"]=utf8_encode($row["Apellido"]);
            $row["socio"]=utf8_encode($row["socio"]);
            $aData = $row;
        }
        echo json_encode($aData);

        break;

    case 'getHistoricoTalega':

        $idTalega = $_POST['idTalega'];

        $sql = "SELECT IF(ta.estado = 1,'Ingresa', IF(ta.estado = 2, 'En campo', IF(ta.estado = 3,'Entregada', 'Editado'))) AS nombreEstado, "
                . "CONCAT_WS(' ',s.Nombre,s.Apellido) AS socio, ta.nombreTercero AS tercero, "
                . "CONCAT_WS(' ',c.nombre, c.apellido) AS caddie, ta.fechaRegistro, ta.estado "
                . "FROM TalegaAdministracion ta "
                . "INNER JOIN Talega t ON(ta.IDTalega = t.IDTalega) "
                . "LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) "
                . "LEFT JOIN Caddie c ON(ta.IDCaddie = c.IDCaddie) "
                . "WHERE ta.IDTalega = " . $idTalega . " "
                . "ORDER BY ta.IDTalegaAdministracion DESC ";

        $result = $dbo->query($sql);
        $aData = [];
        while ($row = $dbo->fetchArray($result)) {
            $aData[] = $row;
        }

        echo json_encode($aData);

        break;
}
?>
