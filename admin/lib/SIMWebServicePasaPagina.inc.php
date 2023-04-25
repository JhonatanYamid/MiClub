<?php
class SIMWebServicePasaPagina {

	function get_configuracion_pasalapagina($IDClub,$IDSocio){

			$dbo =& SIMDB::get();
			$response = array();


			$sql = "SELECT UrlPasaPagina,RefererPasaPagina,SourcePasaPagina FROM Club  WHERE IDClub = '".$IDClub."' ";
			$qry = $dbo->query( $sql );
			if( $dbo->rows( $qry ) > 0 )
			{
				$message = $dbo->rows( $qry ) . " Encontrados";
				while( $r = $dbo->fetchArray( $qry ) )
				{
						$configuracion["IDClub"] = $IDClub;
						$configuracion["URL"] = $r["UrlPasaPagina"];
						$configuracion["referer"] = $r["RefererPasaPagina"];
						$configuracion["source"] = $r["SourcePasaPagina"];
						array_push($response, $configuracion);

				}//ednw hile
					$respuesta["message"] = $message;
					$respuesta["success"] = true;
					$respuesta["response"] = $response;
			}//End if
			else
			{
					$respuesta["message"] = "Pasa la pagina no está activo";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
			}//end else

			return $respuesta;

		}// fin function


} //end class
?>
