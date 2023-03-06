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


					$AccionBuscar=$_POST["Accion"];
					if(strlen($AccionBuscar)==3)
						$AccionBuscar="0".$AccionBuscar;

					$id_socio = $dbo->getFields( "Socio", "IDSocio", "Accion = '" . $AccionBuscar . "' and IDClub = '" . $_POST["IDClub"] . "'" );
					$ValoresFormulario=json_encode($array_datos);

					if(!empty($id_socio)){

							$respuesta = SIMWEBService::set_pqr( $_POST["IDClub"], $_POST["IDArea"], $id_socio, $_POST["IDTipoPqr"], $_POST["Asunto"], $_POST["ComentarioPqr"], $Archivo, $File, $_POST["IDTipoPqr"], $NombreColaborador,$ApellidoColaborador );
							//SIMHTML::jsAlert( $respuesta[message] );
							$mensaje_proceso=$respuesta[message];
							SIMHTML::jsRedirect( "pqrfarallones.php?mensaje_proceso=".$mensaje_proceso."&estado=ok" );
							exit;
					}else{
						?>
						<?php
						$mensaje_proceso="El numero de accion no existe, por favor verifique";
						//SIMHTML::jsAlert( "El numero de accion no existe, por favor verifique" );
						SIMHTML::jsRedirect( "pqrfarallones.php?mensaje_proceso=".$mensaje_proceso."&estado=err" );
						exit;
					}
		break;
	}//end switch
?>
