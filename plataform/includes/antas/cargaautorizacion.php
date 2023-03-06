
<?php

include( "config.inc.php" ); 

//Datos de acceso a la BD
/*
define( "DBHOST" , "localhost" );
define( "DBNAME" , "miclubapp" );
define( "DBUSER" , "miclubapp" );
define( "DBPASS" , "*wS.Ncna;89=BG3L" );
*/

 	$file = fopen("LogAutoMY.txt", "w+");
  	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	if(!empty($_GET["IDLog"])):
		$sql_log_revisa = "SELECT * FROM `LogServicio` WHERE `IDLogServicio` >= '".$_GET["IDLog"]."' and Servicio = 'setautorizacioninvitado'";
		$result_log_revisa = $dbo->query($sql_log_revisa);
		while($row_los_revisa = $dbo->fetchArray($result_log_revisa)):
			$linea= $row_los_revisa["IDLogServicio"].$sep.$row_los_revisa["Servicio"].$sep.$row_los_revisa["Parametros"].$sep.$row_los_revisa["Respuesta"].$sep.$row_los_revisa["FechaPeticion"].$sep;
			fwrite($file, $linea . PHP_EOL);
		endwhile;
	endif;	
	fclose($file);
	
  ini_set('max_execution_time', 0);
  
  
  $FIELD_TEMINATED = "TAB";
  $file = "LogAutoMY.txt";
	if($fp = fopen($file,"r")):		
		ini_set('auto_detect_line_endings', true); 
		while(!feof($fp)):			
		
		$row = fgets($fp,4096);			
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);	
				
			
			$Consecutivo=trim($row_data[0]);
			$Servicio=trim($row_data[1]);
			$Datos=trim($row_data[2]);
			$Respuesta=trim($row_data[3]);
			$Fecha=trim($row_data[4]);
			
				
			if(!empty($Consecutivo) && !empty($Servicio)):
				
				$cadena=str_replace('""','"',$Datos);
				$cadena=str_replace('"{','{',$cadena);
				$cadena=str_replace('}"','}',$cadena);		
				$cadena=str_replace('"[{"','{"',$cadena);							
				$cadena=str_replace('"}]"','"}',$cadena);	
				$cadena=str_replace(':",',':"",',$cadena);							
				
				$array_datos=json_decode($cadena,true); 
				
				//print_r($cadena);
				
				
				/*
				$pos_inicio = (int)strpos($cadena, "[{")+1;
				$pos_final = (int)strpos($cadena, '}]')+1;
				$caracteres_seleccionar = $pos_final - $pos_inicio;				
				$cadena = substr($cadena,$pos_inicio,$caracteres_seleccionar);
				$array_datos=json_decode($cadena); 
				print_r($array_datos);				
				*/
				
				$AppVersion=$array_datos["AppVersion"];
				$Dispositivo=$array_datos["Dispositivo"];
				$FechaIngreso=$array_datos["FechaIngreso"] . " 23:00:00";				
				$FechaSalida=$array_datos["FechaSalida"]. " 23:00:00";
				$IDClub=$array_datos["IDClub"];
				$IDSocio=$array_datos["IDSocio"];
				$IDTipoDocumento=$array_datos["DatosInvitado"]["IDTipoDocumento"];
				$NumeroDocumento=$array_datos["DatosInvitado"]["NumeroDocumento"];
				$Nombre=$array_datos["DatosInvitado"]["Nombre"];
				$Apellido=$array_datos["DatosInvitado"]["Apellido"];
				$Email=$array_datos["DatosInvitado"]["Email"];
				$TipoInvitado=$array_datos["DatosInvitado"]["TipoInvitado"];
				$Placa=$array_datos["DatosInvitado"]["Placa"];
				$CabezaInvitacion=$array_datos["DatosInvitado"]["CabezaInvitacion"];
				$MenorEdad=$array_datos["DatosInvitado"]["MenorEdad"];
				
				
				
				
				
				
							
							$fecha_bien=0;
							//Verifico si la fecha es igual o mayo a hoy
							$fecha_hoy=date("Y-m-d 00:00:01");
							
							if(strlen($FechaIngreso)<=18):
								//echo "Fecha mal " . $FechaIngreso;
								$fecha_bien=0;
							else:
								//echo "Fecha bien " . $FechaIngreso;
								$fecha_bien=1;
							endif;
							
							if($Consecutivo=="2418270"):
										//echo "JORGE ". $Datos . "FECA BIEN " . $fecha_bien . " ING " . $FechaIngreso;
										//exit;
									endif;
							
							
							
							if( ( strtotime($FechaIngreso)>=strtotime($fecha_hoy) || strtotime($FechaFin)>=strtotime($fecha_hoy)  )  && $fecha_bien==1):															
									
									
									
									
												
									if(!empty($NumeroDocumento)):
									//Verifico si existe la invitacion	
																											
											$IDInvitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '" . $NumeroDocumento . "'" );
											if(!empty($IDInvitado)):
												$sql_invitaciones="Select * From SocioInvitadoEspecial Where IDInvitado = '".$IDInvitado."' and IDClub = '".$IDClub."' and FechaInicio >= CURDATE()";
												$result_invitaciones=$dbo->query($sql_invitaciones);
												$total_invitaciones = $dbo->rows($result_invitaciones);
												if((int)$total_invitaciones<=0):
													//Se debe crear la invitacion
													echo "<br>MAL Documento:".$NumeroDocumento;													
													
													
													
															//Inserto invitacion
															$sql_inserta_inv = $dbo->query("Insert Into SocioInvitadoEspecial (IDClub, IDSocio, IDInvitado, IDPadre, IDPadreInvitacion, IDVehiculo, CodigoAutorizacion, CabezaInvitacion,  TipoInvitacion, FechaInicio, FechaFin, UsuarioTrCr, FechaTrCr) 
																							Values ('".$IDClub."','".$IDSocio."', '".$IDInvitado."', '".$IDPadre."','".$IDInvitacionGenerada."', '".$id_vehiculo."', '".$CodigoAutorizacion."','".$CabezaInvitacion."', 'Invitacion', '".$FechaIngreso."', '".$FechaSalida."', 'WebService',NOW())");
															//$id_invitado_inserta = $dbo->lastID();																							
													
												else:
													//Quedo bien creada $NumeroDocumento
													echo "<br><br>BIEN Documento:".$NumeroDocumento;	
													
												endif;
											endif;			
									
									else:										
										if((int)$NumeroDocumento>0):
											echo "<br>Esta invitado pero no esta creado en invitados";							
										endif;
									endif;
							endif;
							
				
			
			endif;
			
			
		endwhile;		
	endif;		
  echo "Terminado.";
?>