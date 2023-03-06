<?php
	switch( $_POST["action"] )
	{

		case "insert":

		require_once "recaptchalib.php";
		// tu clave secreta
		$secret = "6LdtvCYTAAAAAJKmrTc2Pak5PR2D_Uf-2c82yFsW";

		// respuesta vacía
		$response = null;

		// comprueba la clave secreta
		$reCaptcha = new ReCaptcha($secret);

		// si se detecta la respuesta como enviada
		if ($_POST["g-recaptcha-response"]) {
		$response = $reCaptcha->verifyResponse(
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
		}

		 if ($response != null && $response->success) {
			 $_SESSION["validcaptcha"]="okcaptcha".date("Y-m-d H:i:s");
			 //echo $_SESSION["validcaptcha"]="okcaptcha".date("Y-m-d H:i:s");
			//echo "Hi " .  ", thanks for submitting the form!";
			$robot_verificacion="S";
		  }
		  else{
			//SIMHTML::jsAlert( "Debes verificar que no eres un robot" );
			//SIMHTML::jsRedirect( "inscripcionevento.php?IDEvento=".$_GET["IDEvento"] );
			//exit;
		}


			$_POST["FechaTrCr"] = date( "Y-m-d H:i:s" );
			$_POST["FechaRegistro"] = date( "Y-m-d H:i:s" );


					//seguridad para cada campo del formulario
					$contador++;
					foreach($_POST as $clave=>$valor)
					{
						$_POST[$clave] = SIMUtil::antiinjection($valor);
					}//end for

					foreach($_POST as $clave=>$valor)
					{
						if(is_numeric($clave)){
							$array_datos[$contador]["IDCampoFormularioEvento"]=$clave;
							$array_datos[$contador]["Valor"]=$valor;
							$contador++;
						}
					}//end for


					$AccionBuscar=$_POST["179"];
					if(strlen($AccionBuscar)==3)
						$AccionBuscar="0".$AccionBuscar;

					$id_socio = $dbo->getFields( "Socio", "IDSocio", "Accion = '" . $AccionBuscar . "' and IDClub = '" . $_POST["IDClub"] . "'" );
					$datos_socio = $dbo->fetchAll( "Socio", " Accion = '" . $AccionBuscar . "' ", "array" );

					$correo_socio = $_POST["188"].",".$_POST["191"];
					$ValoresFormulario=json_encode($array_datos);


					if(!empty($id_socio)){
							$Mensaje="Inscripcion a evento realizada con exito";
							$respuesta = SIMWebServiceApp::set_formulario_evento($_POST["IDClub"],$_POST["IDEvento"],$id_socio,$IDSocioBeneficiario,$ValoresFormulario,$OtrosDatosFormulario);
							//SIMHTML::jsAlert( $respuesta[message] );
							$mensaje_proceso=$respuesta[message];
							if($_POST["100000"]=="Pagoonline"){ //Pagos online
								SIMHTML::jsRedirect( "https://www.psepagos.co/PSEHostingUI/ShowTicketOffice.aspx?ID=2350" );

							}	elseif($_POST["100000"]=="AbonoCuenta"){ //Abono cuentas
								//SIMHTML::jsAlert( "Se realizara abono a su cuenta" );
								SIMUtil::notifica_actualiza_datos("7",$correo_socio,$msg);
								SIMUtil::enviar_notificacion_push_general(7,$id_socio,$Mensaje);
								$mensaje_proceso.=" .Se realizara abono a su cuenta";
								SIMHTML::jsRedirect( "inscripcionevento.php?IDEvento=".$_GET["IDEvento"]."&mensaje_proceso=".$mensaje_proceso."&procesook=S" );
							}else{
								SIMUtil::notifica_actualiza_datos("7",$correo_socio,$msg);
								SIMUtil::enviar_notificacion_push_general(7,$id_socio,$Mensaje);
								$mensaje_proceso.=" .Pago Efectivo";
								SIMHTML::jsRedirect( "inscripcionevento.php?IDEvento=".$_GET["IDEvento"]."&mensaje_proceso=".$mensaje_proceso."&procesook=S" );
							}

							exit;
					}else{
						?>
						<?php
						$mensaje_proceso="El numero de accion no existe, por favor verifique";
						//SIMHTML::jsAlert( "El numero de accion no existe, por favor verifique" );
						SIMHTML::jsRedirect( "inscripcionevento.php?IDEvento=".$_GET["IDEvento"]."&mensaje_proceso=".$mensaje_proceso );

						exit;
					}


		break;



	}//end switch
?>
