<?php


 ini_set('max_execution_time', 0);
  include( "config.inc.php" ); 
  
  $FIELD_TEMINATED = "TAB";
  $file = "basesocio/salasnov10.txt";
	if($fp = fopen($file,"r")):		
		ini_set('auto_detect_line_endings', true); 
		while(!feof($fp)):			
		
		$row = fgets($fp,4096);			
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);	
				
			
			$Socio=trim($row_data[0]);
			$Sala=trim($row_data[1]);
			$Fecha=trim($row_data[2] );
			$HoraInicio=trim($row_data[4]);
			$HoraFin = trim($row_data[5]);
			$NumeroPersona = $row_data[6];
			$Comentarios=trim($row_data[7]);			
			
			$IDServicio = "1392";
			
			$nombre_socio = $Comentarios;
			$nombre_socio = str_replace("(EM)","",utf8_encode($nombre_socio));
			$nombre_socio = str_replace("(RO)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(RR)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(YC)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(HZ)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(C.C.C)","",utf8_encode($nombre_socio));
			$nombre_socio = str_replace("Ñ","N",utf8_encode($nombre_socio));			
			
				
			if(!empty($Sala) && !empty($Comentarios)):				
				if($contador>=1): //Para no tomar el encabezado
						//Consulto si existe
						unset($array_condicion);
						$array_nombre_socio = explode(" ",$nombre_socio);
						foreach ($array_nombre_socio as $parametro):
							if(!empty(trim($parametro)))
								$array_condicion[] = " Nombre like ('%".$parametro."%')";
						endforeach;
						
						$condicion = " and (" . implode(" and ", $array_condicion) .")";
						
						
						$sql_socio = "Select * 
									  From Socio 
									  Where IDClub = 25
									  ".$condicion;
									  
						$result_socio = $dbo->query($sql_socio);
						$total_socio = $dbo->rows($result_socio);
						if((int)$total_socio>0):	
							$row_socio = $dbo->fetchArray($result_socio);							
							$Socio = $row_socio["IDSocio"];
						else:							
							//echo "<br>No existe " . $sql_socio;
							$Socio = "88939"; // Socio General gun
						endif;
						
						
							$hora_inicial = strtotime(date("Y-m-d ".$HoraInicio));
							$hora_final = strtotime(date("Y-m-d ".$HoraFin));
							while($hora_inicial<=$hora_final):
							
								//echo "<br>Si existe " . $nombre_socio;
								$Hora_insertar = date("H:i:s",$hora_inicial);
								
								//Verifico que no este reservado
								$sql_reservado ="Select * From ReservaGeneral Where IDClub = 25 and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$Sala."' and Fecha = '".$Fecha."' and Hora = '".$Hora_insertar."'";
								$result_reservado = $dbo->query($sql_reservado);
								if($dbo->rows($result_reservado)<=0):															
									$insert_reserva = "Insert into ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, CantidadInvitadoSalon, Fecha, Hora, Observaciones, UsuarioTrCr, FechaTrCr)
									Values (25, '".$Socio."','".$Socio."','".$IDServicio."','".$Sala."',1,745,'".$NumeroPersona."','".$Fecha."','".$Hora_insertar."','".$Comentarios."','Socio',NOW())";
									//echo "<br>" . $insert_reserva;
									$dbo->query($insert_reserva);	
								else:
									echo "<br>Ya estaba reservado";
								endif;	
									
								$hora_actual = date("Y-m-d H:i:s",$hora_inicial);
								$hora_inicial = strtotime ( '+30 minutes' , strtotime ( $hora_actual ) ) ;	
								//echo " Siguiente ". date("Y-m-d H:i:s",$hora_inicial);
								//echo "<br>";
							endwhile;
							
						
												
							
						
				endif;		
			else:
				echo "<br>Datos mal " . 	$Sala . " " . $Comentarios;
			endif;
			$contador++;			
		endwhile;		
	endif;		
  echo "Terminado";
  
  
  
  exit;



  ini_set('max_execution_time', 0);
  include( "config.inc.php" ); 
  
  $FIELD_TEMINATED = "TAB";
  $file = "basesocio/reservassalas.txt";
	if($fp = fopen($file,"r")):		
		ini_set('auto_detect_line_endings', true); 
		while(!feof($fp)):			
		
		$row = fgets($fp,4096);			
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);	
				
			
			$Socio=trim($row_data[0]);
			$Sala=trim($row_data[1]);
			$Fecha=trim($row_data[2] );
			$HoraInicio=trim($row_data[4]);
			$HoraFin = trim($row_data[5]);
			$NumeroPersona = $row_data[6];
			$Comentarios=trim($row_data[7]);			
			
			$nombre_socio = $Comentarios;
			$nombre_socio = str_replace("(EM)","",utf8_encode($nombre_socio));
			$nombre_socio = str_replace("(RO)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(RR)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(YC)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("(HZ)","",utf8_encode($nombre_socio));			
			$nombre_socio = str_replace("RR","",utf8_encode($nombre_socio));
			$nombre_socio = str_replace("Ñ","N",utf8_encode($nombre_socio));			
			
				
			if(!empty($Sala) && !empty($Comentarios)):				
				if($contador>=1): //Para no tomar el encabezado
						//Consulto si existe
						$sql_socio = "Select * 
									  From Socio 
									  Where IDClub = 25
									  and (IDSocio = '".$Socio."')";
						$result_socio = $dbo->query($sql_socio);
						$total_socio = $dbo->rows($result_socio);
						if((int)$total_socio>0):	
							$hora_inicial = strtotime(date("Y-m-d ".$HoraInicio));
							$hora_final = strtotime(date("Y-m-d ".$HoraFin));
							while($hora_inicial<=$hora_final):
							
								//echo "<br>Si existe " . $nombre_socio;
								$Hora_insertar = date("H:i:s",$hora_inicial);
								echo "<br>" . $insert_reserva = "Insert into ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, CantidadInvitadoSalon, Fecha, Hora, Observaciones, UsuarioTrCr, FechaTrCr)
								Values (25, '".$Socio."','".$Socio."',1392,'".$Sala."',1,745,'".$NumeroPersona."','".$Fecha."','".$Hora_insertar."','".$Comentarios."','Socio',NOW())";
								//$dbo->query($insert_reserva);							
								
								$hora_actual = date("Y-m-d H:i:s",$hora_inicial);
								$hora_inicial = strtotime ( '+30 minutes' , strtotime ( $hora_actual ) ) ;	
								//echo " Siguiente ". date("Y-m-d H:i:s",$hora_inicial);
								//echo "<br>";
							endwhile;
							
						
												
							
						else:							
							echo "<br>No existe " . $nombre_socio;
						endif;
				endif;		
			else:
				echo "<br>Datos mal " . 	$Sala . " " . $Comentarios;
			endif;
			$contador++;			
		endwhile;		
	endif;		
  echo "Terminado";
?>