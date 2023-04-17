<?php
require("admin/config.inc.php");
include("plataform/procedures/home_site.php");
SIMUtil::cache();
session_start();

$domainGet = str_replace(":", "://", $_GET['domain']);
$_GET['domain'] = $domainGet;

$get = $_GET;
$response = json_encode($get);
$dbo->query("update PagosYappy set InfoExtra = '{$response}', FechaTrEd = NOW() where ReferenciaTransaccion='{$get['orderId']}'");


function validateHash()
{
    try {
        // include 'env.php'; // IMPORTAR ARCHIVO DE ENV PARA UTILIZAR LA VARIABLE 'CLAVE_SECRETA'

        // $get = SIMUtil::makeSafe($_GET);
        $get = $_GET;

        $dbo = &SIMDB::get();

        $orderId = $get['orderId'];
        $status = $get['status'];
        $hash = $get['hash'];
        $domain = $_GET['domain'];

        $IDClub = $dbo->getFields("PagosYappy", "IDClub", "ReferenciaTransaccion=$orderId");
        $ClaveSecreta = $dbo->fetchAll("ConfiguracionClub", "CLAVE_SECRETA_YAPPY", " IDClub = '" . $IDClub . "' ");

        $values = base64_decode($ClaveSecreta);
        $secrete = explode('.', $values);
        $signature =  hash_hmac('sha256', $orderId . $status . $domain, $secrete[0]);
        // echo '<pre>';
        // print_r($signature);
        // die();
        $success = strcmp($hash, $signature) === 0;
    } catch (\Throwable $th) {
        $success = false;
    }
    return $success;
}

$dbo = &SIMDB::get();



if (isset($_GET['orderId']) && isset($_GET['status']) && isset($_GET['domain']) && isset($_GET['hash'])) {
    header('Content-Type: application/json');
    $success = validateHash();
    if ($success) {
        $ReferenciaTransaccion = $get['orderId'];
        $ReferenciaYappy = $get['hash'];
        $FechaTransaccion = date('Y-m-d H:i:s');
        $MetodoPago = "Pasarela Yappy";
        $UrlRespuesta = '';
        $Response = json_encode($get);
        $datos_transaccion = $dbo->fetchAll("PagosYappy", "ReferenciaTransaccion=$ReferenciaTransaccion", "array");
        if ($datos_transaccion['reserved13'] > 0) {
            $Version = $datos_transaccion['reserved13'];
        } else {
            $Version = "";
        }
        // Estados de transacción
        switch ($get['status']) {
            case 'E':
                $Estado = "APROBADO";
                break;
            case 'R':
                $Estado = "RECHAZADO";
                break;
            case 'C':
                $Estado = "CANCELADO";
                break;
            default:
                $Estado = "PENDIENTE";
                break;
        }

        $MensajeEstado = $Estado;
        $UpdatePagos = "UPDATE PagosYappy 
                        SET Estado = '$Estado', 
                            MensajeEstado = '$MensajeEstado', 
                            ReferenciaYappy = '$ReferenciaYappy', 
                            FechaTransaccion = '$FechaTransaccion', 
                            MetodoPago = '$MetodoPago', 
                            UrlRespuesta = '$UrlRespuesta',
                            Response = '$Response',
                            FechaTrEd = NOW(),
                            UsuarioTrEd = 'PASARELA YAPPY'
                            WHERE ReferenciaTransaccion = $ReferenciaTransaccion";
        $dbo->query($UpdatePagos);
        $Modulo = $datos_transaccion['Tipo'];
        /*
        Estados Yappy
        E "Ejecutado"
        C "Cancelado"
        R "Rechazado"
        */
        switch ($get['status']):
            case "E":
                $estadoFinal = 1;
                $pagado = "S";
                $estadoTransaccion = "A";
                $success = true;
                break;
            case "C":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "C";
                $success = true;
                break;
            case "R":
                $estadoFinal = 2;
                $pagado = "N";
                $estadoTransaccion = "R";
                $success = true;
                break;


            default:
                $estadoFinal = 2;
                $Estado = "PENDIENTE";
                $pagado = "N";
                $estadoTransaccion = "P";
                $success = true;
                break;

        endswitch;

        // DEPENDIENDO DEL MODULO ACTUALIZAMOS LA TABLA CORERSPONDIENTE PARA QUE SE ACEPTE EL PAGO O TRANSACCION.
        switch ($Modulo):

            case "Reservas":

                $reserva = $datos_transaccion['IDReserva'];

                if ($pagado == "S") {
                    $EstadoT = " IDEstadoReserva = '1' , ";
                } else {
                    $condicion_no_aprobado = " and EstadoTransaccion <> 'A' ";
                }


                $actualizaReserva = "UPDATE ReservaGeneral SET " . $EstadoT . " Pagado = '$pagado', MedioPago = 'Yappy-$Estado',
                                    FechaTransaccion = '$FechaTransaccion', CodigoRespuesta = '$Estado',
                                    EstadoTransaccion = '$estadoTransaccion', FechaTrEd=NOW(),UsuarioTrEd='PASARELA YAPPY'
                                    WHERE IDReservaGeneral ='$reserva' " . $condicion_no_aprobado;

                $dbo->query($actualizaReserva);
                break;




            case "Taloneras":
                if ($pagado == "S") {
                    $estado_talonera = ", Activo='1' ";
                } else {
                    $estado_talonera = ", Activo='0' ";
                }

                $update = "UPDATE SocioTalonera SET EstadoTransaccion = '$estadoTransaccion', Pagado = '$pagado', MedioPago = 'Yappy-$Estado' " . $estado_talonera . " WHERE IDSocioTalonera = $datos_transaccion[IDReserva]";
                $dbo->query($update);

                break;
            case "Domicilio":
                $sql_pedido = "UPDATE Domicilio" . $Version . "
											SET Pagado = '" . $pagado . "',
												CodigoPago='" . $ReferenciaTransaccion . "',
												EstadoTransaccion='" . $estadoTransaccion . "',
												FechaTransaccion= NOW(),
												CodigoRespuesta='" . $status . "',
												MedioPago='PASARELA YAPPY',
												TipoMedioPago='PASARELA YAPPY'
											WHERE IDDomicilio = '" . $datos_transaccion["reserved12"] . "' and IDClub = '" . $datos_transaccion["IDClub"] . "'";
                $dbo->query($sql_pedido);
                break;

        endswitch;
    }
    echo json_encode(['succes' => $success]);
}
