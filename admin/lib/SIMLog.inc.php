<?php
class SIMLog
{

public static function insert( $id_usuario , $table , $mod , $transaccion , $operacion )
	{


		$IP = SIMUtil::get_IP();
		//$sql = urlencode( $operacion );
		$no_permitidas= array ("'","\"");
		$permitidas= array (" "," ",);
		$sql = str_replace($no_permitidas, $permitidas ,$operacion);
		$dbo =& SIMDB::get();

		$dbo->query( "INSERT INTO Log ( IDUsuario , Fecha , Modulo , Transaccion , Operacion , DireccionIP,FechaTrCr )
						VALUES( '" . $id_usuario . "' , NOW() , '" . $table . "','" . $transaccion . "','" . $sql . "','" . $IP . "',NOW())" );

		return true;
	}

	function insert_app( $ServicioApp, $IDClub, $DatosRecibe, $DatosRetorna ){
		$dboMongo =& SIMDBMongo::get();
		$array_no_guardar = array("Token","Token2");



		$FechaActual=(
		  ($FechaInsertar=new MongoDB\BSON\UTCDateTime())->toDateTime()->format('U.u')
		);

		$now = \DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new \DateTimeZone('America/Bogota'));
		$FechaPeticionTexto=$now->setTimeZone(new DateTimeZone('America/Bogota'))->format("Y-m-d H:i:s.u");

		$array_guardar["Servicio"]=$ServicioApp;
		$array_guardar["FechaPeticion"]=$FechaInsertar;
		$array_guardar["FechaPeticionTexto"]=$FechaPeticionTexto;

		if(count($DatosRecibe)>0){

			foreach($DatosRecibe as $index_recibe => $valor_recibe){
				$guardar="S";
				switch($index_recibe){
					case "IDClub":
					case "IDServicio":
					case "IDSocio":
					case "IDServicioElemento":
					case "IDElemento":
						$valor_recibe=(int)$valor_recibe;
						$guardar="N";
					break;
					case "TokenID":
					case "key":
					case "action":
						$index_recibe="";
						$guardar="N";
					break;
					case "Fecha":
						if(!empty($valor_recibe)){
							$valor_recibe=substr($valor_recibe,0,10);
							$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $valor_recibe . " 00:00:00");
							$valor_recibe = new \MongoDB\BSON\UTCDateTime( $date->format('U') * 1000 );
							$guardar="N";
						}
					break;

				}

				if(!empty($index_recibe)){
					$array_guardar[$index_recibe]=$valor_recibe;
				}

				if($guardar=="S"){
					$DatosRecibeGuardar[$index_recibe]=$valor_recibe;
				}

			}
		}



		$array_guardar["DatosApp"]=$DatosRecibeGuardar;



		if(count($DatosRetorna)>0){
			foreach($DatosRetorna as $index_retorna => $valor_retorna){
					$guardar="S";
					switch($index_retorna){
						case "message":
							$valor_respuesta=$valor_retorna;
							$array_guardar["RespuestaServicio"]=$valor_respuesta;
							unset($DatosRetorna[$index_retorna]);
						break;
						case "success":
							$valor_respuesta=$valor_retorna;
							$array_guardar["EstadoRespuesta"]=$valor_respuesta;
							unset($DatosRetorna[$index_retorna]);
						break;
						case "response":
							foreach($valor_retorna[0] as $index_response => $valor_response){
									switch($index_response){
										case "IDClub":
										case "IDServicio":
										case "Disponibilidad":
											unset($DatosRetorna["response"][0][$index_response]);
										break;
									}
							}
						break;
					}
			}

			$array_guardar["RespuestaApp"]=$DatosRetorna;

		}


		$dboMongo->insert($array_guardar,'Operacion');
	}


}
?>
