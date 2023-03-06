<?
session_start();
require( "admin/config.inc.php" );
error_reporting(0);

/********** INCLUDE DE LIBRERIAS ***************/
include 'farallonespago/credibanco/beans/vpos_plugin.php';
/***********************************************/
	$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $_GET["IDClub"] . "' ", "array" );

	$sql_datos="INSERT INTO PagoCredibanco (XMLREQ) VALUES('".$_GET["IDClub"]."') ";
	$dbo->query($sql_datos);

	  $VI = $datos_club["VI"];
		$llavePublicaFirma = $datos_club["LlaveCriptoCredibanco"];
		$llavePrivadaCifrado = $datos_club["LlaveFirmaPrivadaCredibanco"];

		//print_r($llavePublicaFirma);
		//print_r($llavePrivadaCifrado);
		//print_r($_POST);
		//exit;


		if(VPOSResponse($_POST,$arrayOut,$llavePublicaFirma,$llavePrivadaCifrado,$VI)){
				//La salida esta en $arrayOut con todos los parámetros decifrados devueltos por el VPOS
				$resultadoAutorizacion = $arrayOut['authorizationResult'];
				$codigoAutorizacion = $arrayOut['authorizationCode'];
				//Guardo los datos

				$datos_log=json_encode($arrayOut);
				$sql_datos="INSERT INTO PagoCredibanco (XMLREQ) VALUES('".$datos_log."') ";
				$dbo->query($sql_datos);

				$sql_actualiza="UPDATE PagoCredibanco
												SET acquirerId='".$arrayOut["acquirerId"]."',commerceId='".$arrayOut["commerceId"]."',purchaseOperationNumber='".$arrayOut["purchaseOperationNumber"]."',purchaseCurrencyCode='".$arrayOut["purchaseCurrencyCode"]."',
												purchaseTerminalCode='".$arrayOut["purchaseTerminalCode"]."',purchasePlanId='".$arrayOut["purchasePlanId"]."',purchaseQuotaId='".$arrayOut["purchaseQuotaId"]."',purchaseLanguage='".$arrayOut["purchaseLanguage"]."',
												purchaseIpAddress='".$arrayOut["purchaseIpAddress"]."',transactionTrace='".$arrayOut["transactionTrace"]."',fingerPrint='".$arrayOut["fingerPrint"]."',
												additionalObservations='".$arrayOut["additionalObservations"]."',authorizationCode='".$arrayOut["authorizationCode"]."',errorCode='".$arrayOut["errorCode"]."',errorMessage='".$arrayOut["errorMessage"]."',
												authorizationResult='".$arrayOut["authorizationResult"]."', authorizationCodeAR='".$arrayOut["authorizationCodeAR"]."',errorCodeAR='".$arrayOut["errorCodeAR"]."',errorMessageAR='".$arrayOut["errorMessageAR"]."',
												authorizationResultAR='".$arrayOut["authorizationResultAR"]."',xmlResponse='".xmlResponse."',cardNumber='".$arrayOut["cardNumber"]."',reserved12='".$arrayOut["reserved12"]."',reserved13='".$arrayOut["reserved13"]."',reserved14='".$arrayOut["reserved14"]."'
												WHERE NumeroTransaccion = '".$arrayOut["purchaseOperationNumber"]."'";
				$dbo->query($sql_actualiza);
				$datos_transaccion = $dbo->fetchAll( "PagoCredibanco", " NumeroTransaccion = '" . $arrayOut["purchaseOperationNumber"] . "' ", "array" );


				$datos_transa=json_encode($arrayOut);
				$ModuloPago=$arrayOut["reserved12"];

				if(empty($ModuloPago)){
					$ModuloPago=$datos_transaccion["Modulo"];
				}

				switch($ModuloPago){
					case "Domicilio":
								if($arrayOut["errorMessage"]=="Aprobada"){
									$sql_pedido="UPDATE Domicilio".$Version."
															SET Pagado = 'S',PagoPayu='S',
															CodigoPago='".$arrayOut["purchaseOperationNumber"]."',
															EstadoTransaccion='".$arrayOut["errorMessage"]."',
															FechaTransaccion='".substr($datos_transaccion["FechaTransaccion"],0,10)."',
															CodigoRespuesta='".$arrayOut["authorizationCode"]."',
															MedioPago='".$arrayOut["authorizationCode"]."',
															TipoMedioPago='".$arrayOut["authorizationResultAR"]."'
															WHERE IDDomicilio = '".$arrayOut["reserved2"]."' and IDClub = '".$arrayOut["reserved13"]."'";
									$dbo->query($sql_pedido);
								}
								else{
									$sql_pedido="UPDATE Domicilio".$Version."
															SET Pagado = 'N',PagoPayu='N',
															CodigoPago='".$arrayOut["purchaseOperationNumber"]."',
															EstadoTransaccion='".$arrayOut["errorMessage"]."',
															FechaTransaccion='".substr($datos_transaccion["FechaTransaccion"],0,10)."',
															CodigoRespuesta='".$arrayOut["authorizationCode"]."',
															MedioPago='".$arrayOut["authorizationCode"]."',
															TipoMedioPago='".$arrayOut["authorizationResultAR"]."',
															IDEstadoDomicilio=3
															WHERE IDDomicilio = '".$arrayOut["reserved2"]."' and IDClub = '".$arrayOut["reserved13"]."' ";
									$dbo->query($sql_pedido);
								}
					break;

					case "Inscripcion";
						if($arrayOut["errorMessage"]=="Aprobada"){
							$dia_actual=date("d");
							$mes_actual=date("m");
							$datos_producto = $dbo->fetchAll( "ProductoLiga", " Valor = '" . $datos_transaccion["ValorPago"] . "' and IDClub = '".$arrayOut["reserved13"]."' ", "array" );
							$NumeroMeses=$datos_producto["Meses"];
							$FechaInicial=date("Y-m")."-05";

							/*
							if((int)$dia_actual<=5){
								$MesesSumar=$NumeroMeses-1;
								$FechaInicial=date("Y-m")."-05";
							}
							else{
								$MesesSumar=$NumeroMeses-1;
								$fecha = date('Y-m')."-05";
								$nuevafecha = strtotime ( '+1 month' , strtotime ( $fecha ) ) ;
								$FechaInicial = date ( 'Y-m-d' , $nuevafecha );
							}
							*/

							$nuevafechapago = strtotime ( '+'.$NumeroMeses.' month' , strtotime ( $FechaInicial ) ) ;
							$PagadoHasta =date ( 'Y-m-d' , $nuevafechapago );

							$sql_pedido="UPDATE Socio
													SET IDEstadoSocio = '1',PagadoHasta='".$PagadoHasta."',FechaPago='".date("Y-m-d")."',
															IDProductoLiga='".$datos_producto["IDProductoLiga"]."'
													WHERE IDSocio = '".$arrayOut["reserved14"]."' and IDClub = '".$arrayOut["reserved13"]."' ";
							$dbo->query($sql_pedido);

						}
					break;
					case "Extracto":
								if($arrayOut["errorMessage"]=="Aprobada"){
									//Envio mensaje de pago exitoso
									$IDClubConsulta=$arrayOut["reserved13"];
									$IDSocioConsulta=$arrayOut["reserved14"];
									$Valor=$datos_transaccion["ValorPago"];
									$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocioConsulta."','apruebapagocuotaisraeli','".json_encode($arrayOut)."','".json_encode($response)."')");
									if($IDClubConsulta==98){
										if(empty($arrayOut["reserved14"])){
											$arrayOut["reserved14"]=$datos_transaccion["IDSocio"];
										}
										$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $arrayOut["reserved14"] . "' ", "array" );
										SIMWebServiceIsraeli::pago_cuota($datos_socio["NumeroDocumento"],$Valor,$arrayOut["reserved14"]);
									}
									SIMUtil::notifica_pago_extracto($IDClubConsulta,$IDSocioConsulta,$Valor);

								}

					break;
					case "Donacion":
								if($arrayOut["errorMessage"]=="Aprobada"){
									$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $arrayOut["reserved14"] . "' ", "array" );
									//Envio mensaje de pago exitoso
									$IDClubConsulta=$arrayOut["reserved13"];
									$IDSocioConsulta=$arrayOut["reserved14"];
									$Valor=$datos_transaccion["ValorPago"];
									$frm["UsuarioTrCr"]="Donacion";
									$frm["FechaTrCr"]=date("Y-m-d H:i:S");
									$frm["Valor"]=$datos_transaccion["ValorPago"];
									$frm["IDClub"]=$arrayOut["reserved13"];
									$frm["IDSocio"]=$arrayOut["reserved14"];
									$frm["Nombre"]=$datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
									$frm["Accion"]=$datos_socio["Accion"];
									$id = $dbo->insert( $frm , "Donacion" , "IDDonacion" );
									//SIMUtil::notifica_pago_extracto($IDClubConsulta,$IDSocioConsulta,$Valor);
								}

					break;
					case "Reservas":
								if($arrayOut["errorMessage"]=="Aprobada"){

									$query="UPDATE ReservaGeneral
											SET EstadoTransaccion='" . $arrayOut["errorMessage"]."',
												FechaTransaccion='" . substr($datos_transaccion["FechaTransaccion"],0,10)."',
												CodigoRespuesta='" . $arrayOut["authorizationCode"]."',
												MedioPago='" . $arrayOut["authorizationCode"]."',
												TipoMedioPago='" . $arrayOut["authorizationResultAR"]."',
												Pagado ='S',
												PagoPayu = 'S'
											WHERE IDReservaGeneral='" . $arrayOut["reserved2"]."'";
									$sql_actualizar=$dbo->query($query);
								}

					break;
				}

			}else{
				echo "<br>Ocurrio un problema de comunicacion, por favor comuníquese con el club";
				exit;
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
							<span>RECIBO ELECTRONICO DE TRANSACCION</span>
						</div>

						<div class="titulo">
								<span>CREDIBANCO</span>
							</div>

							<div style="background:#FFF;text-align:center !important;" >
									<p style="padding:10px !important;">
											VENTA NO PRESENCIAL<br>
											PAGAR&Eacute; INCONDICIONALMENTE Y A LA ORDEN DEL ACREEDOR, EL VALOR TOTAL DE ESTE PAGAR&Eacute; JUNTO CON LOS INTERES
											A LAS TASAS M&Aacute;XIMAS PERMITIDAS POR LA LEY
									</p>
									<p>
											<br><b><?php echo $datos_club["Nombre"]; ?></b><br><br>
									</p>
								</div>




		  <div class="formulario" >
									<p>
											<label>C&oacute;digo &Uacute;nico</label>
											<input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $datos_club["CodigoUnico"]; ?>" readonly />
									</p>
                    <p>
                        <label>Terminal:</label>
                        <input id="Fecha" name="Fecha"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["purchaseTerminalCode"]; ?>" readonly />
                    </p>
                    <br style="clear:both" />
                    <p>
                        <label>Numero de Transaccion:</label>
                        <input id="NumeroTransaccion" name="NumeroTransaccion"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["purchaseOperationNumber"]; ?>  " readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Numero de Pedido:</label>
                        <input id="NumeroTransaccion" name="NumeroTransaccion"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $datos_transaccion["NumeroFactura"]; ?>  " readonly />

                    </p>
                    <br style="clear:both" />
                    <p>
                        <label>Fecha de la transaccion:</label>
                        <input id="ValorTotal" name="ValorTotal"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo substr($datos_transaccion["FechaTransaccion"],0,10); ?> " readonly />
                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Franquicia de la tarjeta:</label>
                        <input id="ValorTotal" name="ValorTotal"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["cardType"]; ?> " readonly />
                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Numero de cuotas:</label>
                        <input id="ValorTotal" name="ValorTotal"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["quotaCode"]; ?> " readonly />
                    </p>
                    <br style="clear:both" />
										<p>
												<label>Tarjeta:</label>
												<input id="ValorTotal" name="ValorTotal"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo substr($arrayOut["cardNumber"],-4); ?> " readonly />
										</p>
										<br style="clear:both" />
                    <p>
                        <label>Hora Transaccion:</label>
                        <input id="RespuestaEntidad" name="RespuestaEntidad"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo substr($datos_transaccion["FechaTransaccion"],11); ?>  " readonly />
                    </p>
                    <br style="clear:both" />
                    <p>
                        <label>Moneda:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="COP" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Valor Total:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" align="right" value="<?php echo "$".number_format($datos_transaccion["ValorPago"],2,',','.'); ?>" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>IVA:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="0" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>*Base Devolución del iva:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="0" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Valor Neto:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" align="right" value="<?php echo "$".number_format($datos_transaccion["ValorPago"],2,',','.'); ?>" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Descripcion:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["additionalObservations"]; ?>" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Numero de autorizacion:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["authorizationCode"]; ?>" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Respuesta:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["errorMessage"]; ?>" readonly />

                    </p>
                    <br style="clear:both" />
										<p>
                        <label>Descripcion de la respuesta:</label>
                        <input id="Estado" name="Estado"  size="30" maxlength="255" class="campo_text" type="text" value="<?php echo $arrayOut["errorCode"]; ?>" readonly />

                    </p>

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
