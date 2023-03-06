<!DOCTYPE html>
<?php

$transaccion="1";
?>
<html>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Ingreso de datos</title>
        <meta name="description" content="">
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
        <script src="https://secure.placetopay.ec/redirection/lightbox.min.js"></script>
        <script>
            P.on('response', function(data) {
                var respuestaJson = JSON.stringify(data);
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {

                    if (this.readyState == 4 && this.status == 200) {
                        //var resp = JSON.parse(this.responseText);
                        alert(this.responseText);
                    }
                };

                xmlhttp.open("POST", "actualizarOrden.php", true);
                xmlhttp.setRequestHeader('Content-type', 'application/json; charset=utf-8');
                xmlhttp.send(respuestaJson);
                //console.log(respuestaJson);
                $("#respuesta").html(JSON.stringify(data, null, 2));
            });
        </script>
    </head>
    <body>
        <form action="#" method="POST">
            <h2>Ingreso de datos</h2>
            <lengend>Datos de pago</lengend>
            <p>
                Subtotal
                <input type="number" min="0" value="12.00" step="0.01" required name="subtotal" id="subtotal" onchange="calculoIce(this.value)" />
                <input type="radio" value="si" name="iva" id="iva" onclick="calculoIva(this.value)" />Con I.V.A
                <input type="radio" value="no" name="iva" checked onclick="calculoIva(this.value)" />Sin I.V.A<br/>
            </p>
            <p>
                I.C.E. &nbsp; &nbsp;
                <input type="number" value="0.00" name="valorIce" id="valorIce" readonly />
            </p>
            <p>
                I.V.A. &nbsp; &nbsp;
                <input type="number" value="0.00" name="valorIva" id="valorIva" readonly />
            </p>
            <p>
                Total: &nbsp; &nbsp;
                <input type="number" value="0.00" name="valorTotal" id="valorTotal" readonly />
            </p>
            <p>
                Descripción del producto: &nbsp; &nbsp;
                <input type="text"  name="descripcion" id="descripcion" />
            </p>
            <lengend>Datos de cliente</lengend>
            <p>
                Nombre: <input type="text" placeholder="Nombre" name="nombre" id="nombre" required/>
                Apellido: <input type="text" placeholder="Apellido" name="apellido" id="apellido" required/>
            </p>
            <p>
                Identificación: <input type="text" placeholder="Identificación" name="identificacion" id="identificacion" required/>
                Tipo de ID.:
                <select name="tipoId" id="tipoId" required>
                    <option value="" selected disabled>Seleccione un tipo de Identificación</option>
                    <option value="CI">Cédula de identidad</option>
                    <option value="RUC">R.U.C.</option>
                    <option value="PPN">Pasaporte</option>
                </select>
            </p>
            <p>
                E-mail: <input type="email" placeholder="E-mail" name="email" id="email" required/>
                Teléfono: <input type="tel" placeholder="Teléfono" name="telefono" id="telefono" required/>
            </p>
            <p>
                <input type="checkbox" name="pagador" /> Yo realizaré el pago
            </p>
            <p style="align:right">
                <input type="submit" value="Enviar datos">
            </p>
        </form>

        <p>
            respuesta
        </p>
        <div id="respuesta" name="respuesta"></div>
    </body>
    <script>
        var iva = document.getElementById('valorIva');
        var opcionIva = document.getElementById('iva');
        var base = document.getElementById('subtotal');
        var ice = document.getElementById('valorIce');
        var valorTotal = document.getElementById('valorTotal');
        ice.value = roundToTwo(12 * 0.05);
        valorTotal.value = Number(ice.value) + Number(base.value) + Number(iva.value);
        function calculoIce(num) {
            ice.value = roundToTwo( base.value * 0.05 );
            calculoIva (opcionIva.value);
        }

        function calculoIva(valor) {
            if (valor == "si" ){
                iva.value =  roundToTwo( ( Number(base.value)) * 0.12);
            } else {
                iva.value = 0.00;
            }
            valorTotal.value = roundToTwo( Number(ice.value) + Number(base.value) + Number(iva.value) );
        }

        function roundToTwo(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        }

    </script>
    <?php
    date_default_timezone_set("America/Bogota");
    //obtención de nonce
    if (function_exists('random_bytes')) {
    $nonce = bin2hex(random_bytes(16));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $nonce = bin2hex(openssl_random_pseudo_bytes(16));
    } else {
        $nonce = mt_rand();
    }

    $nonceBase64 = base64_encode($nonce);

    $login = "a1a059dde7a37c4f236f5f8f3253d4f1";
    $secretKey = "clIU8A0Cqj8Zhx6v";
    $seed = date('c');
    $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

    $expiracion = strtotime ( '+20 minute' , strtotime ( $seed ) ) ; //sumar al menos 15 minutos
    $expiracion = date('c', $expiracion);

    if ($_POST){



        $auth = array (
            "auth" => array(
                "login" => $login,
                "seed" => $seed,
                "nonce" => $nonceBase64,
                "tranKey" => $tranKey)
        );

        $ice = array (
            "kind" => "ice",
            "amount" => $_POST['valorIce'],
            "base" => $_POST['subtotal']
        );

        $taxes = array( $ice );

        if ( $_POST ['iva'] == "si" ){
            $iva = array (
                "kind" => "valueAddedTax",
                "amount" => $_POST['valorIva'],
                "base" => $_POST['subtotal'] //+ $_POST['valorIce']
            );
            $taxes = array( $ice, $iva );
        }

        if ( isset ($taxes) ){
            $amount = array (
                "taxes" => $taxes,
                "currency" => "USD",
                "total" => $_POST['valorTotal']
            );
        }   else {
            $amount = array (
                "currency" => "USD",
                "total" => $_POST['valorTotal']
            );
        }

        $data = array (
            "locale" => "es_EC",
            "buyer" => array(
                "name" => $_POST['nombre'],
                "surname" => $_POST['apellido'],
                "email" => $_POST['email'],
                "document" => $_POST['identificacion'],
                "documentType" => $_POST['tipoId'],
                "mobile" => $_POST['telefono']
            ),
            "payment" => array(
                "reference" => $transaccion, //editar
                "description" => $_POST['descripcion'],
                "amount" => $amount
            ),
            "expiration" => $expiracion,
            "ipAddress" => getRealIpAddr(),
            "returnUrl" => "https://dnetix.co/p2p/client",
            "userAgent" => $_SERVER['HTTP_USER_AGENT'],
            "paymentMethod" => ""
        );


        if (isset( $_POST ['pagador'] )){
            $pagador = array( "payer" => array(
                    "name" => $_POST['nombre'],
                    "surname" => $_POST['apellido'],
                    "email" => $_POST['email'],
                    "document" => $_POST['identificacion'],
                    "documentType" => $_POST['tipoId'],
                    "mobile" => $_POST['telefono']
                ));
            $data = array_merge($data, $pagador);
        }
        $data = array_merge($data, $auth);

        print_r($data);

        $ch = curl_init('https://test.placetopay.ec/redirection/api/session');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = ['Content-Type:application/json; charset=utf-8'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
        $response = curl_exec($ch);
        curl_close($ch);
        // do anything you want with your response
        $respuesta = json_decode($response, true);
        print "<pre>";print_r($respuesta);
        if ( $respuesta['status']['status'] == "FAILED" ){
            print "<script> alert('". $respuesta['status']['message'] ."');</script>";
        } else {
            if (isset($respuesta['processUrl'])){
                $sql = "INSERT INTO `peticiones`(`referencia`, estado_transaccion,  `request_id`, `fecha_peticion`, `url_proceso`) VALUES ($transaccion,
                '".$respuesta['status']['status']."',
                '".$respuesta['requestId']."',
                '".$respuesta['status']['date']."',
                '".$respuesta['processUrl']."')";
            }
            $link->query($sql);
        }

        print  ''."<br><script>P.init('".$respuesta['processUrl']."')</script>";
        print "<pre>"; print_r($data); print "</pre>";
    }

    function getRealIpAddr()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
    $auth = array (
            "auth" => array(
                "login" => $login,
                "seed" => $seed,
                "nonce" => $nonceBase64,
                "tranKey" => $tranKey)
        );

    $ch = curl_init('https://test.placetopay.ec/redirection//api/session/50078');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = ['Content-Type:application/json; charset=utf-8'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $auth ) );
        $response = curl_exec($ch);
        curl_close($ch);
        // do anything you want with your response
        $respuesta = json_decode($response, true);
        print "<pre>";print_r($respuesta);
?>
</html>
