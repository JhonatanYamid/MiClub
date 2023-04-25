<?php
class SIMEncuesta {

	function set_respuesta_encuesta($IDClub,$IDSocio,$IDEncuesta,$Respuestas,$IDUsuario="",$Archivo, $File = "")
	{
		$dbo =& SIMDB::get();


		//subir las imagenes
		if ( isset( $File ) ) {
			//$nombrefoto.=json_encode($File);
			foreach($File as $nombre_archivo => $archivo){
				 $ArrayPreguntaEncuesta = explode ("|",$nombre_archivo);
				 $IDPreguntaActualiza=$ArrayPreguntaEncuesta[1];
				 //$nombrefoto.=$archivo["name"];
				 //$nombrefoto.=json_encode($archivo);
				 $tamano_archivo = $archivo["size"];
		 		if ( $tamano_archivo >= 6000000 ){
		 			$respuesta[ "message" ] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
		 			$respuesta[ "success" ] = false;
		 			$respuesta[ "response" ] = NULL;
		 			return $respuesta;
		 		}
				else{
					$files = SIMFile::upload( $File[ $nombre_archivo ], PQR_DIR, "IMAGE" );
					if ( empty( $files ) && !empty( $File[ $nombre_archivo ][ "name" ] ) ):
						$respuesta[ "message" ] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
						$respuesta[ "success" ] = false;
						$respuesta[ "response" ] = NULL;
						return $respuesta;
					endif;

					$Archivo = $files[ 0 ][ "innername" ];
					$actualiza_pregunta="UPDATE EncuestaRespuesta SET Valor = '".$Archivo."' WHERE IDPregunta ='".$IDPreguntaActualiza."' and IDEncuesta  = '".$IDEncuesta."' and IDSocio = '".$IDSocio."' LIMIT 1";
					$dbo->query($actualiza_pregunta);
					$nombrefoto.=	$actualiza_pregunta;		
				}
			}
		}
		$respuesta[ "message" ] = $nombrefoto;
		$respuesta[ "success" ] = false;
		$respuesta[ "response" ] = NULL;
		return $respuesta;

		//Valido el pseo del archivo
		$tamano_archivo = $File["Foto"]["size"];
		if ( $tamano_archivo >= 6000000 ){
			$respuesta[ "message" ] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
			$respuesta[ "success" ] = false;
			$respuesta[ "response" ] = NULL;
			return $respuesta;
		}



		if( !empty( $IDClub )  && (!empty( $IDSocio ) || !empty( $IDUsuario ) ) && !empty( $IDEncuesta ) ){
				$guardar_encuesta=0;
				$contesta_una=utf8_decode($dbo->getFields( "Encuesta" , "UnaporSocio" , "IDEncuesta = '".$IDEncuesta."'" ));
				if($contesta_una=="S"){
					$sql_resp="Select IDEncuesta From EncuestaRespuesta Where IDSocio = '".$IDSocio."' and IDEncuesta = '".$IDEncuesta."' Limit 1"	;
					$r_resp=$dbo->query($sql_resp);
					if ( $dbo->rows( $r_resp ) <= 0 ) {
							$guardar_encuesta=1;
					}
				}
				else{
					$guardar_encuesta=1;
				}

				if(!empty( $IDUsuario )){
					$IDSocio=$IDUsuario;
					$TipoUsuario="Funcionario";
				}
				else{
					$TipoUsuario="Socio";
				}

				$Respuestas= trim(preg_replace('/\s+/', ' ', $Respuestas));
				if($guardar_encuesta==1){
						$datos_respuesta= json_decode($Respuestas, true);
						if (count($datos_respuesta)>0):
							foreach($datos_respuesta as $detalle_respuesta):
								$sql_datos_form = $dbo->query("Insert Into EncuestaRespuesta (IDEncuesta, IDSocio, IDPregunta,  TipoUsuario, Valor, FechaTrCr) Values ('".$IDEncuesta."','". $IDSocio ."','". $detalle_respuesta["IDPregunta"]."','".$TipoUsuario."','".$detalle_respuesta["Valor"]."',NOW())");
							endforeach;
						endif;
						$respuesta["message"] = "guardado";
						$respuesta["success"] = true;
						$respuesta["response"] = $datos_reserva;
				}
				else{
					$respuesta["message"] = "Esta encuesta ya había sido contestada por ud, solo se permite 1 vez";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}

		}
		else{
			$respuesta["message"] = "E1. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;

	}




} //end class
?>
