<?php
require("../admin/config.inc.php");
header("Content-type: application/json; charset=utf-8");




$nowserver = date("Y-m-d H:i:s");

$datos_post = json_decode(file_get_contents('php://input'), true);

$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','REQ_U_FLR','".json_encode($_POST)."','".json_encode($datos_post)."')");


if($datos_post["accessToken"]==ACCESSTOKENHUB && $datos_post["passwordToken"]==PASSWORDTOKENHUB){



	if($datos_post["action"]=="REQ_U_FLR" || $datos_post["action"]=="REQ_U_HSK"){
		$dbo =& SIMDB::get();
		$IDClub=8;

		$Hub_Code = $datos_post["data"]["hub_code"];
		$Status = $datos_post["data"]["status"];
		$IDPqr=$datos_post["data"]["code"];

		if( !empty( $IDClub ) && !empty( $Hub_Code )   ) {
					$actualizacion_hub="CODE:".$IDPqr . " Hub_Code " . $Hub_Code . " Status " .$Status . " action: " . $datos_post["action"];
					$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','REQ_U_FLR','".$actualizacion_hub."','".json_encode($datos_post)."')");
					$IDPqr = $dbo->getFields( "Pqr" , "IDPqr" , "IDPqr='".$IDPqr."'" );
					$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','REQ_U_FLR','".$IDPqr."','".json_encode($datos_post)."')");
					if((int)$IDPqr>0){
						$sql_pqr="UPDATE Pqr SET NombreColaborador = '".$actualizacion_hub."', ApellidoColaborador='hub' WHERE IDPqr='".$IDPqr."' LIMIT 1";
						$dbo->query($sql_pqr);
						SIMUtil::noticar_respuesta_pqr( $IDPqr, $actualizacion_hub );
						$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','REQ_U_FLR','consulta','".json_encode($datos_post)."')");
						$respuesta["code"] = "0";
						$respuesta["description"] = "Correcto";
					}
					else{
						$respuesta["code"] = "6";
						$respuesta["description"] = "Identificador de acción desconocido No";
					}

		}
		else{
			$respuesta["code"] = "7";
			$respuesta["description"] = "Faltan datos obligatorios o no tienen el formato esperado";
		}
			$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','REQ_U_FLR','".json_encode($_POST)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'code' => $respuesta[code], 'description'=>$respuesta[description] ) ) );
	}

	else{
		die( json_encode( array(  'code' => '3', 'description'=>"Error de formato JSON Accion invalida" ) ) );
	}

}
else{
	die( json_encode( array(  'code' => '2', 'description'=>"Error de autenticación" ) ) );

}



?>
