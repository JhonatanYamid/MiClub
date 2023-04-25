<?php
class SIMWebServicePtoAzul
{

	function autentica($Username, $Clave, $IDClub)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => URL_PTOAZUL . 'login',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'username=' . $Username . '&password=' . $Clave,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		//echo $response;
		$datos_login = json_decode($response);
		//print_r($datos_login);
		if (!empty($datos_login->access_token)) {
			//Consulto los datos del socio
			$curl_datos = curl_init();
			curl_setopt_array($curl_datos, array(
				CURLOPT_URL => URL_PTOAZUL . 'datosocio',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer ' . $datos_login->access_token
				),
			));

			$response_datos = curl_exec($curl_datos);

			curl_close($curl_datos);
			$datos_socio = json_decode($response_datos);
			if (!empty($datos_socio->ACCION)) {

				if ($datos_socio->MOROSIDAD == 0) {
					$IDEstadoSocio = "A";
				} else {
					$IDEstadoSocio = "I";
				}

				$Accion = $datos_socio->ACCION;
				$AccionPadre = $datos_socio->ACCION;
				$Parentesco = $datos_socio->PARENTESCO;
				$Genero = "";
				$Nombre = $datos_socio->NOMBRES;
				$Apellido = $datos_socio->APELLIDOS;
				$FechaNacimiento = "";
				$NumeroDocumento = $datos_socio->CEDULA;
				$CorreoElectronico = $datos_socio->CORREO;
				$Telefono = "";
				$Celular = "";
				$Direccion = "";
				$TipoSocio = $datos_socio->PARENTESCO;
				$EstadoSocio = $IDEstadoSocio;
				$InvitacionesPermitidasMes = 100;
				$UsuarioApp = $Username;
				$Predio = "";
				$Categoria = "";
				$CodigoCarne = $datos_socio->CEDULA;
				$ClaveApp = sha1($Clave);
				//Verifico si ya eciste el socio
				//$array_socio = $dbo->fetchAll( "Socio", " NumeroDocumento = '" . $datos_socio["CEDULA"] . "' and IDCLub = '" . $IDClub . "' LIMIT 1", "array" );
				//Actualizo
				$resp = SIMWebServiceApp::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria, "S", $CodigoCarne, $ClaveApp);

				//Consulto el vinculo familiar
				$curl_fam = curl_init();
				curl_setopt_array($curl_fam, array(
					CURLOPT_URL => URL_PTOAZUL . 'datosfamiliares',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer ' . $datos_login->access_token
					),
				));

				$response_fam = curl_exec($curl_fam);
				curl_close($curl_fam);
				//echo $response_fam;
				$datos_familia = json_decode($response_fam);
				if (count($datos_familia->FAMILIARES) > 0) {
					foreach ($datos_familia->FAMILIARES as $datos_familia) {
						if ($datos_familia->MOROSIDAD == 0) {
							$IDEstadoSocio = "A";
						} else {
							$IDEstadoSocio = "I";
						}

						$Accion = $datos_familia->ACCION;
						$AccionPadre = $datos_familia->ACCION;
						$Parentesco = $datos_familia->PARENTESCO;
						$Genero = "";
						$Nombre = $datos_familia->NOMBRES;
						$Apellido = $datos_familia->APELLIDOS;
						$FechaNacimiento = "";
						$NumeroDocumento = $datos_familia->CEDULA;
						$CorreoElectronico = $datos_familia->CORREO;
						$Telefono = "";
						$Celular = "";
						$Direccion = "";
						$TipoSocio = $datos_familia->PARENTESCO;
						$EstadoSocio = $IDEstadoSocio;
						$InvitacionesPermitidasMes = 100;
						$UsuarioApp = $datos_familia->CEDULA;
						$Predio = "";
						$Categoria = "";
						$CodigoCarne = $datos_familia->CEDULA;
						$ClaveApp = sha1($NumeroDocumento);
						//Verifico si ya eciste el socio
						//$array_socio = $dbo->fetchAll( "Socio", " NumeroDocumento = '" . $datos_socio["CEDULA"] . "' and IDCLub = '" . $IDClub . "' LIMIT 1", "array" );
						//Actualizo
						$resp = SIMWebServiceApp::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria, "S", $CodigoCarne, $ClaveApp);
					}
				}

				$respuesta["message"] = "ok";
				$respuesta["success"] = true;
				$respuesta["response"] = "ok";
			} else {
				$respuesta["message"] = $datos_socio->message;
				$respuesta["success"] = false;
				$respuesta["response"] = $datos_socio->status;
			}
		} else {
			$respuesta["message"] = $datos_login->descripcion;
			$respuesta["success"] = false;
			$respuesta["response"] = $datos_login->status;
		}
		return $respuesta;
	} // fin function


} //end class
