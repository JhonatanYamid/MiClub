<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);

$IDHitorialCuotasSociales = $frm['id'];
$Saldo = $frm['saldo'];
$valores = $frm['data'];

$suma = 0;
foreach ($valores as $indice => $valor) {
    $suma += $valor['monto'];
}

if (!is_numeric($suma) || $suma <= 0 || $suma == '') {
    $result = array('saldo' => $Saldo, 'mensaje' => 'El monto recibido no es valido');
} else {
    if ($Saldo > 0) {
        if ($suma <= $Saldo) {
            // Validación comentada para permitir abonos a la deuda.
            // if ($suma == $Saldo) {
            $SaldoRestante = $Saldo - $suma;
            foreach ($valores as $indice => $valor) {
                if ($valor['monto'] > 0) {
                    $insert = "INSERT INTO DetalleHistorialCuotasSociales (IDHistorialCuotasSociales,MetodoPago,MontoPago,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDHitorialCuotasSociales . "','" . SIMResources::$MetodoPago[$valor['metodo']] . "','" . $valor['monto'] . "','" . SIMUser::get('Nombre') . '' . SIMUser::get('Apellido') . "', NOW())";
                    $q_insert = $dbo->query($insert);
                }
            }
            if ($SaldoRestante > 0) {
                $result = array('saldo' => $SaldoRestante, 'mensaje' => 'Saldo pendiente: ' . $SaldoRestante);
                $Estado = 'Pendiente';
            } else {
                $result = array('saldo' => $SaldoRestante, 'mensaje' => 'Cuota pagada con exito');
                $Estado = 'Pagado';
            }
            if ($q_insert === true) {
                $update = "UPDATE HistorialCuotasSociales SET Saldo = '" . $SaldoRestante . "', Estado='$Estado', UsuarioTrEd='" . SIMUser::get('Nombre') . ' ' . SIMUser::get('Apellido') . "', FechaTrEd = NOW() WHERE IDHistorialCuotasSociales = " . $IDHitorialCuotasSociales;
                $dbo->query($update);
            }
            // } else {
            //     $result = array('saldo' => $Saldo, 'mensaje' => 'El monto recibido es insuficiente');
            // }
        } else {
            $result = array('saldo' => $Saldo, 'mensaje' => 'El monto recibido es mayor al saldo pendiente');
        }
    } else {
        $result = array('saldo' => $Saldo, 'mensaje' => 'Sin saldo pendiente');
    }
}
echo $response = json_encode($result);
