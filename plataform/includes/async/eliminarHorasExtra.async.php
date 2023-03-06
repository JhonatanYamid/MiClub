<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();


$frm = SIMUtil::makeSafe($_POST);


if ($frm["table"] == "Socio") {
    $campo = "IDSocio";
} else {
    $campo = "IDUsuario";
}

if (!empty($frm["fechainicio"]) && !empty($frm["fechafinal"])) {

    //creo un backup en la tabla CheckinLaboralBck
    $sql_id = "SELECT IDCheckinLaboral FROM CheckinLaboral WHERE date(FechaMovimientoEntrada) >='" . $frm["fechainicio"] . "' AND  date(FechaMovimientoEntrada)<='" . $frm["fechafinal"] . "' AND IDClub ='" . $frm['club'] . "' AND $campo='" . $frm['id'] . "'";
    $query_id = $dbo->query($sql_id);
    while ($row = $dbo->fetchArray($query_id)) {
        $usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
        $sql_copia_checkin = "INSERT  INTO CheckinLaboralBck (IDCheckinLaboral ,IDClub,IDSocio,IDUsuario,LatitudEntrada,LongitudEntrada,LatitudSalida,LongitudSalida,Entrada,Salida,Estado,
                FechaCambioEstado,ComentarioRevision,FechaMovimientoEntrada,FechaMovimientoSalida,UltimoMovimiento,Observaciones,HoraEntradaEstablecida,HoraSalidaEstablecida,ObservacionEntrada,
                ObservacionSalida,FechaMovimientoEntradaDespuesDelTurno,FechaMovimientoSalidaDespuesDelTurno,UsuarioTrCr,FechaTrCr,FechaTrEd,UsuarioTrEd)
                SELECT IDCheckinLaboral ,IDClub,IDSocio,IDUsuario,LatitudEntrada,LongitudEntrada,LatitudSalida,LongitudSalida,Entrada,Salida,Estado,
                FechaCambioEstado,ComentarioRevision,FechaMovimientoEntrada,FechaMovimientoSalida,UltimoMovimiento,Observaciones,HoraEntradaEstablecida,HoraSalidaEstablecida,ObservacionEntrada,
                ObservacionSalida,FechaMovimientoEntradaDespuesDelTurno,FechaMovimientoSalidaDespuesDelTurno,UsuarioTrCr,FechaTrCr,NOW(),'" . $usuario_elimina . "' FROM CheckinLaboral WHERE IDCheckinLaboral = '" . $row["IDCheckinLaboral"] . "'";

        $dbo->query($sql_copia_checkin);
    }
    //fin bck


    //borro los datos de la tabla checkin
    $sql = "DELETE FROM CheckinLaboral  WHERE  CheckinLaboral.IDClub = " . $frm['club'] . " AND $campo=" . $frm['id'] . " AND  date(FechaMovimientoEntrada) >='" . $frm["fechainicio"] . "' AND  date(FechaMovimientoEntrada)<='" . $frm["fechafinal"] . "'";
    echo ($query = $dbo->query($sql)) ? '1' : '2';
    $nom_usu = SIMUser::get("IDUsuario") . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");
    SIMLog::insert($nom_usu, "CheckinLaboral", "CheckinLaboral", "delete",  $sql);
} else {
    echo '0';
}
