<?php
include("general.php");
$hoy = date('Y-m-d');
$sql_Socio = "SELECT IDSocio,IDClub,TipoSocio,IDCategoria,IDParentesco,EstadoCivil,FechaNacimiento FROM Socio WHERE IDClub = '" . SIMUser::get('club') . "' AND FechaNacimiento = '" . $hoy . "'";
$q_Socio = $dbo->query($sql_Socio);

while ($r_Socio = $dbo->assoc($q_Socio)) {
    $Edad = SIMUtil::Calcular_Edad($r_Socio['FechaNacimiento']);
    $TipoSocio = $r_Socio['TipoSocio'];
    $IDCategoria = $r_Socio['IDCategoria'];
    $IDParentesco = $r_Socio['IDParentesco'];
    $EstadoCivil = $r_Socio['EstadoCivil'];

    $where = "WHERE IDClub = '" . SIMUser::get('club') . "'";
    if ($Edad > 0) {
        $where .= " AND Edad like '%|" . $Edad . "|%'";
    }
    if ($TipoSocio != '') {
        $where .= " AND TipoSocio like '%|" . $TipoSocio . "|%'";
    }
    if ($IDParentesco > 0) {
        $where .= " AND IDParentesco like '%|" . $IDParentesco . "|%'";
    }
    if ($EstadoCivil != '') {
        $where .= " AND EstadoCivil like '%|" . $EstadoCivil . "|%'";
    }
    $sqlCategoria = "SELECT IDCategoria FROM Categoria " . $where . " LIMIT 1";
    $qCategoria = $dbo->query($sqlCategoria);
    $IDCategoria = $dbo->assoc($qCategoria);
    $rows_Categoria = $dbo->rows($qCategoria);
    if ($rows_Categoria > 0) {
        if ($IDCategoria != $r_Socio['IDCategoria']) {
            $update_Socio = "UPDATE Socio SET IDCategoria = '" . $IDCategoria['IDCategoria'] . "', UsuarioTrEd = 'RutinaCambioCategoria', FechaTrEd = NOW() WHERE IDSocio = '" . $r_Socio['IDSocio'] . "'";
            $dbo->query($update_Socio);
            SIMUtil::Notificar_Categoria_Socio_Actualizada($r_Socio['IDClub'], $r_Socio['IDSocio']);
            exit;
        }
    } else {
        SIMUtil::Notificar_Categoria_Socio_No_Encontrada($r_Socio['IDClub'], $r_Socio['IDSocio']);
        exit;
    }
}
