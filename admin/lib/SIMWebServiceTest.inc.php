<?php
class SIMWebServiceTest
{

	function valida_siguiente_turno_sin_reserva_test($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos ){
			global $array_horarios;
			$dbo =& SIMDB::get();	
			$hora_turno_siguiente = "";
			$flag_turno_disponible = 0;
			$contador_turnos=1;	
			// Quito 1 turno por que necesito validar los siguientes
			$cantidad_turnos--;
			if(count($array_horarios[$IDElemento])<=0):
				$array_horarios[$IDElemento] = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");
			endif;	
			
			//$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");
			
			foreach ($array_horarios[$IDElemento]["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):				
				if($flag_turno_siguiente==1):														
						//$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,"",$Fecha,$datos_horario["Hora"],"",$cantidad_turnos);			
						// verifico si esta disponible la reserva
						echo "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$datos_horario["Hora"]."'";
						$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$datos_horario["Hora"]."'" );						
						if (empty($id_reserva_disponible)):													
							$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,"",$Fecha,$datos_horario["Hora"],"","");									
						else:							
							$respuesta = false;	
						endif;
						
					if ($respuesta==true):												
						echo "si se pudo";
						$hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
						$contador_turnos++;
						if($contador_turnos < $cantidad_turnos):
							$Hora = $datos_horario["Hora"];
						endif;
					else:
						unset($hora_turno_siguiente); // No se pudo separar
					endif;					
				endif;
				
				if ($datos_horario["Hora"]==$Hora):
					$flag_turno_siguiente = 1;
				else:
					$flag_turno_siguiente = 0;				
				endif;
			endforeach;	
			
			//Valido que se hayan podido separado los mismos turnos que se solicitaron
			if (count($hora_turno_siguiente)!=$cantidad_turnos):
				unset($hora_turno_siguiente);
				//echo "no se pudieron tomar todos";
			endif;
			
			
		return $hora_turno_siguiente;
			
	}	
	function validar_turnos_seguidos_test($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario = "", $TipoBeneficiario = "" ){
			$dbo =& SIMDB::get();	
			$flag_turno_seguido = 0;
			$array_confirmado = array();
			// Consulto los turnos reservados y confirmados del socio para no tomar los separados
			if(!empty($IDBeneficiario)):
				$condicion_beneficiario = " and  (IDSocioBeneficiario = '".$IDBeneficiario."' or IDInvitadoBeneficiario = '".$IDBeneficiario."')";
			else:		
				$condicion_beneficiario = " and  IDSocioBeneficiario = '0' and IDInvitadoBeneficiario = '0'";
			endif;
			
			// Valido tambien para que los de la misma acción no puedan tomar turnos seguidos			
			$accion_padre = $dbo->getFields( "Socio" , "AccionPadre" , "IDSocio = '".$IDSocio."'" );
			$accion_socio = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'" );
			if(empty($accion_padre)): // Es titular
				$array_socio[] = $IDSocio;
				$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_socio."' and IDClub = '".$IDClub."' ";
				$result_nucleo = $dbo->query($sql_nucleo);
				while($row_nucleo = $dbo->fetchArray($result_nucleo)):
					$array_socio[] = $row_nucleo["IDSocio"];
				endwhile;
			else:
				$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_padre."' or Accion = '".$accion_padre."' and IDClub = '".$IDClub."' ";
				$result_nucleo = $dbo->query($sql_nucleo);
				while($row_nucleo = $dbo->fetchArray($result_nucleo)):
					$array_socio[] = $row_nucleo["IDSocio"];
				endwhile;				
			endif;
			if(count($array_socio)>0):
				$id_socio_nucleo = implode(",",$array_socio);
			endif;
			
			
			//$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
			echo $sql_confirmado="Select * From  ReservaGeneral Where IDSocio in (".$id_socio_nucleo.")  and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
			$qry_confirmado = $dbo->query($sql_confirmado);
			while($r_confirmado = $dbo->fetchArray($qry_confirmado)):
				$array_confirmado [] = $r_confirmado["Hora"];
			endwhile;
			
			$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,"","");
			foreach ($array_horarios["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):				
				if(in_array($IDSocio,$array_socio) && in_array($datos_horario["Hora"],$array_confirmado)):					
					$id_socio_turno = $IDSocio;
				elseif(empty($array_turnos_dia[$datos_horario["Hora"]])):
					$id_socio_turno = "";	
				endif;
				if(empty($array_turnos_dia[$datos_horario["Hora"]])):				
					$array_turnos_dia[$datos_horario["Hora"]] = $id_socio_turno;	
				endif;	
			endforeach;	
			
			
			for($i=1;$i<=count($array_turnos_dia);$i++):
				current($array_turnos_dia);
				//Primer Posicion
				if($i==1 && key($array_turnos_dia)==$Hora && current($array_turnos_dia)==$IDSocio): // Es el primer horario y lo valido
					$flag_turno_seguido = 1;	
				endif;
				if(key($array_turnos_dia)==$Hora):			
					// me devuelvo al turno anterior
					prev($array_turnos_dia);
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 2;	
					endif;
					//Adelanto dos turnos, si es el final solo uno
					next($array_turnos_dia);
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 3;	
					endif;
					if ($i!=count($array_turnos_dia)):				
						next($array_turnos_dia);
					endif;
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 4;	
					endif;
				endif;
				next($array_turnos_dia);
			endfor;
			
		return $flag_turno_seguido;	
			
	}
	
	

	
	
}//end class
?>
