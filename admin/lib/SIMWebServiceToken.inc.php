<?php
require_once(LIBDIR.'/php-jwt-master/src/BeforeValidException.php');
require_once(LIBDIR.'/php-jwt-master/src/ExpiredException.php');
require_once(LIBDIR.'/php-jwt-master/src/JWT.php');
require_once(LIBDIR.'/php-jwt-master/src/SignatureInvalidException.php');
use Firebase\JWT\JWT;

class SIMWebServiceToken
{

		function get_token($Usuario,$Clave){

				$dbo = & SIMDB::get();
				if ( !empty( $Usuario ) && !empty( $Clave ) ) {
					 $issuedAt   = time();
					 $notBefore  = $issuedAt + 0;             //Adding 10 seconds
					 $segundos_expira=60000;
					 $expire     = $notBefore + $segundos_expira;            // Adding 60 seconds


					$sql_verifica = "SELECT * FROM UsuarioWS WHERE Usuario = '" . $Usuario . "' and  Clave = '" . sha1( $Clave ) . "' and Activo = 'S' Limit 1";
					$qry_verifica = $dbo->query( $sql_verifica );
					if ( $dbo->rows( $qry_verifica ) == 0 ) {
						$respuesta[ "message" ] = "Datos incorrectos";
						$respuesta[ "success" ] = false;
						$respuesta[ "response" ] = NULL;
					}
					else{
							$response=array();
							$datos_usuario = $dbo->fetchArray( $qry_verifica );
							//Genero el token
					    $token = array(
					       "iss" => "https://www.miclubapp.com",
					       "aud" => "https://www.miclubapp.com",
					       "iat" => $issuedAt,
					       "nbf" => $notBefore,
								 'exp'  => $expire,
								 "data" => array(
														       "IDUsuarioWS" => $datos_usuario["IDUsuarioWS"],
														       "Nombre" => $datos_usuario["Nombre"],
														       "Empresa" => $datos_usuario["Empresa"]
														   )
					    );

					    $jwt = JWT::encode($token, KEY_TOKEN);

							$datos_token["Token"] = $jwt;
							$datos_token["Expira"] = $segundos_expira;

							array_push($response, $datos_token);

							$respuesta[ "message" ] = "Token Generado con exito ";
							$respuesta[ "success" ] = true;
							$respuesta[ "response" ] = $response;
					}

				} else {
					$respuesta[ "message" ] = "T1. Atencion faltan parametros";
					$respuesta[ "success" ] = false;
					$respuesta[ "response" ] = NULL;
			}
			return $respuesta;
		}

		function valida_token($Token){
				if ( !empty( $Token )  ) {

								// if decode succeed, show user details
								try {
										// decode jwt
										$decoded = JWT::decode($Token, KEY_TOKEN, array('HS256'));
										$respuesta[ "message" ] = "Token valido";
										$respuesta[ "success" ] = true;
										$respuesta[ "response" ] = $decoded;

								}
								catch (Exception $e){
									$respuesta[ "message" ] = "Token invalido";
									$respuesta[ "success" ] = false;
									$respuesta[ "response" ] = "";
								}

				} else {
					$respuesta[ "message" ] = "T2. Token vacio";
					$respuesta[ "success" ] = false;
					$respuesta[ "response" ] = NULL;
			}
			return $respuesta;
		}

}//end class
?>
