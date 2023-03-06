<?php
  ini_set('max_execution_time', 0);
  include( "config.inc.php" ); 

//echo $clave_especial =  md5("79793972"."_".md5("63380fcfe2bf3c3d2cb3ec089c3c521b"."-miclave01"));
echo "<br>Ejemplo: ".md5("79793972"."_".md5("63380fcfe2bf3c3d2cb3ec089c3c521b-miclave01"));
echo "<br><br>Base: ". "1afdaa15036e5d75b9c007d219bc33e2";
echo "<br>Generada María Consuelo Giraldo: ". md5("65733327"."_".md5("63380fcfe2bf3c3d2cb3ec089c3c521b-e49b3"));
echo "<br><br>Base: ". "5e9f16ab5d958b8c94e04b404b40db29";
echo "<br>Generada Andrés Rojas Rodríguez: ". md5("79147541"."_".md5("63380fcfe2bf3c3d2cb3ec089c3c521b-82a3b"));
echo "<br><br>Base: ". "bb98c41dff864340c2c6815b67ce75a2";
echo "<br>Generada Diego Fernández: ". md5("472107"."_".md5("63380fcfe2bf3c3d2cb3ec089c3c521b-c34b7"));

//(MD5(Usuario+MD5(Contraseña)))
echo "<br><br>Nueva<br><br>";
echo "<br>" .  md5("65733327" . md5("63380fcfe2bf3c3d2cb3ec089c3c521b" . "-" . "e49b3"));

exit;

  
  $FIELD_TEMINATED = "TAB";
  $file = "basesocio/ResidentesDic1_2017W.txt";
	if($fp = fopen($file,"r")):		
		ini_set('auto_detect_line_endings', true); 
		while(!feof($fp)):			
		
		$row = fgets($fp,4096);			
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);	
				
			
			$Accion=trim($row_data[3]);
			$Nombre=trim(utf8_encode($row_data[2]));
			$Documento = trim($row_data[3]);
			$Predio = trim($row_data[0] . " " . $row_data[1] );
			$Clave = trim($row_data[4]);
			
			//quito ñ
			
			$Nombre = str_replace("Ò","n",$Nombre);
			$Nombre = str_replace("¡","A",$Nombre);
			$Nombre = str_replace("·","a",$Nombre);
			$Nombre = str_replace("È","e",$Nombre);
			$Nombre = str_replace("Ì","i",$Nombre);
			$Nombre = str_replace("Û","o",$Nombre);
			
			
			
			
				
			if(!empty($Documento)):				
				if($contador>=1): //Para no tomar el encabezado
						//Consulto si existe
						$sql_socio = "Select * 
									  From Socio 
									  Where IDClub = 18
									  and (NumeroDocumento = '".$Documento."')";
						$result_socio = $dbo->query($sql_socio);
						$total_socio = $dbo->rows($result_socio);
						if((int)$total_socio>0):							
							$datos_socio = $dbo->fetchArray($result_socio);							
							echo "<br><br>". $sql_update = "Update Socio 
										  Set Accion = '".$Accion."',
										  Nombre = '".$Nombre."', 
										  NumeroDocumento = '".$Documento."', Email = '".$Documento."',  
										  TipoSocio = 'Propietario', Predio = '".$Predio."',
										  CambioClave = 'N', IDEstadoSocio = 1,
										  Clave = '".$Clave."'
										  Where IDSocio = '".$datos_socio["IDSocio"]."' and IDClub =18";
							$dbo->query($sql_update);			  
						else:							
							echo "<br><br>". $sql_inserta = "Insert into Socio (IDClub,Accion,Nombre,NumeroDocumento,Email,Clave,TipoSocio,CambioClave,IDEstadoSocio,Predio)
											Values (18,'".$Accion."','".$Nombre."','".$Documento."','".$Documento."','".$Clave.",	'Propietario','N','1','".$Predio."')";
							$dbo->query($sql_inserta);
						endif;
				endif;		
			else:
				echo "<br>Documento mal " . 	$Documento;
			endif;
			$contador++;			
		endwhile;		
	endif;		
  echo "Terminado";
?>