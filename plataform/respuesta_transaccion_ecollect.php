<?
session_start();
require( "admin/config.inc.php" );
require( "admin/lib/Ecollect.inc.php" );


//$_GET["ValorID"]="MzAxMA==";
//$_GET["Modulo"]="Consumos";

$ValorID=base64_decode($_GET["ValorID"]);
$Modulo=$_GET["Modulo"];
$datos_pago = $dbo->fetchAll( "PagoEcollect", " ValorID = '" . $ValorID . "' and Modulo = '".$Modulo."' Order by IDPagoEcollect DESC Limit 1 ", "array" );
$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $datos_pago["IDClub"] . "' ", "array" );

switch ($_GET["Modulo"]) {
  case 'Consumos':
      $descripcion_pago="Pago consumo club";
  break;
  default:
    $descripcion_pago="Pago consumo club";
  break;
}

if(!empty($datos_pago["TranState"])){
  $Token=Ecollect::obtener_token($datos_pago["IDClub"]);
  if($Token=="error"){
    echo "Lo sentimos servicio de pasarela no disponible, intente mas tarde";
    exit;
  }
  else{
   
    $datos_respuesta=Ecollect::consultar_transaccion($datos_pago["TicketId"],$Token);
    Ecollect::actualiza_transaccion($datos_respuesta);
    $datos_pago = $dbo->fetchAll( "PagoEcollect", " ValorID = '" . $ValorID . "' and Modulo = '".$Modulo."' Order by IDPagoEcollect DESC Limit 1 ", "array" );
    	
    switch($datos_pago[TranState])
      {
        case "OK":
        case "APPROVED":
          $estado = 'A';
        break;

        case "REJECTED":
        case "EXPIRED":
        case "NOT_AUTHORIZED":
          $estado = 'R';				
        break;

        case "PENDING":
          $estado = 'A';				
        break;

        case "APPROVED_PARTIAL":
          $estado = 'A';
        break;

        case "PARTIAL_EXPIRED":
          $estado = 'R';
        break;

        case "PENDING_VALIDATION":
          $estado = 'A';
        break;

        case "REFUNDED":
          $estado = 'R';
        break;

        default:
          $estado = "OTRO";
      }

    if($datos_pago[Modulo] ==  "Domicilio"):

      $Factura = $datos_pago[Factura];
      $datos_factura = explode("-",$Factura);
      $IDDomicilio = $datos_factura[0];
      $version = $datos_factura[1];
      
      $query="UPDATE Domicilio ".$version."
			SET EstadoTransaccion = '" . $estado."',
				FechaTransaccion = NOW(),
				CodigoRespuesta = '" . $datos_pago["TicketId"]."',
				MedioPago = 'PagoEcollect',
				TipoMedioPago = '1'
			WHERE IDDomicilio = '" . $IDDomicilio."'";

			$sql_actualizar=$dbo->query($query);

    endif;
		// ENVIAMOS NOTIFICACIÓN AL WEB SERVICE DEL PAGO
    if($datos_pago[Modulo] == "CarteraPereira"):
      require LIBDIR . "SIMWebServiceCampestrePereira.inc.php"; 

      $Id = $datos_pago[NumeroDocumento];
      $Cuota = 0;
      $FormaPago = 9;
      $NumeroSoporte = $datos_pago[TicketId];

      $comas = strpos($datos_pago[Factura],"/");

      if($comas === false):

        // echo "ENTRA EN IF<br><br>";

        $Valor = $datos_pago[ValorPagado];
        $Factura = $datos_pago[Factura];  
        $respuesta = SIMWebServiceCampestrePereira::Abono($Id, $Factura, $Cuota, $Valor, $FormaPago, $NumeroSoporte);

      else:
        // echo "ENTRA EN ELSE <br><br>";
        foreach($ArregloNumeroFactura as $id => $Factura):        
          $DatosFactura = explode("|",$Factura);
          $Valor = $DatosFactura[1];
          $Factura = $DatosFactura[0];  
          $respuesta = SIMWebServiceCampestrePereira::Abono($Id, $Factura, $Cuota, $Valor, $FormaPago, $NumeroSoporte);  
        endforeach;
      endif;

      $correo="sistemas@campestrepereira.com";
  
      $Asunto="Pago APP Consumos";

      $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" .$datos_pago["IDSocio"] . "' ", "array");

      $Mensaje="Pago APP Consumos: <br>Socio:  ". $datos_socio["Nombre"] . " " .$datos_pago["Apellido"];
      $Mensaje.="<br>Accion:  ". $datos_socio["Accion"];
      $Mensaje.="<br>Valor:  ". $datos_pago["TransValue"];
      $Mensaje.="<br>Fecha:  ". $datos_pago["BankProcessDate"];
      $Mensaje.="<br>Nombre Banco:  ". $datos_pago["BankProcessDate"];
      $Mensaje.="<br>Concepto:  ". $datos_pago["Modulo"] . " " . $datos_pago["Factura"];
    
      SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);
    endif;

    if($datos_pago[Modulo] == "Evento"):      

      $Factura = $datos_pago[Factura];
      $datos_factura = explode("-",$Factura);
      $IDEventoRegistro = $datos_factura[0];
      $version = $datos_factura[1];    

      $query = "UPDATE EventoRegistro$version
                            SET EstadoTransaccion='" . $estado . "',
                                FechaTransaccion='NOW()',
                                CodigoRespuesta='$datos_pago[TicketId]',
                                MedioPago='PagoEcollect',
                                TipoMedioPago='1'
                            WHERE IDEventoRegistro='$IDEventoRegistro'";
            $sql_actualizar = $dbo->query($query);
    endif;
    
    $datos=json_decode($datos_respuesta);   
    if($datos->TranState=="OK"){
      //Guardo la factura
      require( "/home/http/miclubapp/httpdocs/admin/lib/FacturaPereira.inc.php" );
      FacturaPereira::guardar_factura($datos);
    }
  }

}

switch ($datos_pago["TranState"]) {
  case 'OK':
    $estado_transaccion="Aprobada";
    $fecha_transaccion=$datos_pago["BankProcessDate"];
  break;
  case 'CREATED':
    $estado_transaccion="Transaccion creada, en espera de pago";
    $fecha_transaccion=$datos_pago["BankProcessDate"];
  break;
  case 'PENDING':
    $estado_transaccion="Pendiente";
    $mensaje_adicional="Por favor verificar si el débito fue realizado en el Banco. ";
    $fecha_transaccion=$datos_pago["BankProcessDate"];
  break;
  break;
  case 'NOT_AUTHORIZED':
    $estado_transaccion="Rechazada";
    $fecha_transaccion=$datos_pago["BankProcessDate"];
  break;
  case 'FAILED':
    $estado_transaccion="Fallida";
    $fecha_transaccion=$datos_pago["BankProcessDate"];
  break;
  default:
    $fecha_transaccion=$datos_pago["FechaTrCr"];
    break;
}

?>

<html>
 <head>
  <title><?php echo $datos_club["Nombre"]; ?></title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <script src="farallonespago/js/modernizr.custom.js"></script>
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
  <link href="farallonespago/css/main.css" media="all" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="farallonespago/js/fancybox/dist/jquery.fancybox.min.css">

  <link href="farallonespago/js/lity-2.3.1/dist/lity.css" rel="stylesheet"/>
  <script src="farallonespago/js/lity-2.3.1/vendor/jquery.js"></script>
  <script src="farallonespago/js/lity-2.3.1/dist/lity.js"></script>
 </head>

 <body>

  <div id="wrap">

   <!--header-->

   <!--breadcrumb-->
   <div id="main">
    <div id="content">

     <!--sidebar-->
		 <div id="content-in">
						<div class="titulo">
								<span><?php echo $datos_club["Nombre"] . "<br>" . $datos_club["Direccion"] ?></span>
							</div>





		  <div class="formulario" >
									<p>
											<label>CUS</label>
											<input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $datos_pago["TrazabilityCode"]; ?>" readonly />
									</p>
                  <br style="clear:both" />
                  <p>
											<label>Referencia de pago</label>
											<input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $datos_pago["TicketId"]; ?>" readonly />
									</p>
                  <br style="clear:both" />
                  <p>
											<label>Descripcion del pago</label>
											<input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $descripcion_pago; ?>" readonly />
									</p>
                  <br style="clear:both" />
                    <p>
                        <label>Estado de la transacción:</label>
                        <input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $estado_transaccion; ?>" readonly />
                        <?php echo $mensaje_adicional; ?>
                    </p>
                    <br style="clear:both" />
                    <p>
                        <label>Valor:</label>
                        <input id="TransValue" name="TransValue"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo "$".number_format($datos_pago["TransValue"],0,'','.'); ?>  " readonly />

                    </p>
                    <p>
                        <label>Fecha Transaccion:</label>
                        <input id="FiName" name="FiName"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $fecha_transaccion; ?>  " readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Banco:</label>
                        <input id="FiName" name="FiName"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $datos_pago["FiName"]; ?>  " readonly />

                    </p>
                    <?php if($datos_pago["IDClub"]==15): ?>
										<p>
                        <label>Respuesta Club Campestre Pereira:</label>
                        <input id="FiName" name="FiName"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $respuesta[mensaje]; ?>  " readonly />

                    </p>
                    <?php endif;?>
                    <br style="clear:both" />
               </div>

		    <!--info-bottom-->
        <!--formulario-->
      </div>

</div>

    <!--content--></div>
   <!--main--></div>
  <!--wrap-->
   <div id="footer">
   <div id="footer-in">
    <p>
     Todos los derechos reservados &copy;.</p>
   </div>
   <!--footer-in--></div>
  <!--footer-->
  <script src="farallonespago/js/jquery-1.9.1.min.js"></script>
  <script src="farallonespago/js/fancybox/dist/jquery.fancybox.min.js"></script>
  <script  src="farallonespago/js/formulario_form.js?<?php echo rand(1,1000)?>"></script>


  </body>
</html>
