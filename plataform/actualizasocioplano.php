<?php

include("/home/http/miclubapp/httpdocs/admin/config.inc.php");

$FIELD_TEMINATED = "TAB";
$IDClub = 9;
$ruta = "/home/http/miclubapp/httpdocs/file/socioplanos/398081_2017-01-19_PruebaCarga.txt";
		
if($fp = fopen($ruta,"r")){
		$cont = 0;
		
		ini_set('auto_detect_line_endings', true); 
		
		if($IGNORE_FIRTS_ROW)
			$row = fgets($fp,4096);
			
		while(!feof($fp)){
			
				$row = fgets($fp,4096);			
			
				if(!empty($FIELD_TEMINATED)){
					if($FIELD_TEMINATED == "TAB")
						$row_data = explode("\t",$row);
					else
						$row_data = explode($FIELD_TEMINATED,$row);							
				}
				
				//Relacion de Campos
				//Relacion de Campos
				$Accion = $row_data[0];
				$AccionPadre = $row_data[1];				
				$NumeroDocumento = $row_data[2];				
				$Nombre = utf8_encode($row_data[3]);
				$Apellido = utf8_encode($row_data[4]);
				$Email = $row_data[5];
				$Telefono = $row_data[6];
				$Direccion = $row_data[7];
				$Parentesco = $row_data[8];
				$FechaNacimiento = $row_data[9];
				$Lote = $row_data[10];
				$Invitaciones = $row_data[11];
				$Accesos = $row_data[12];
				$PermiteReservar = $row_data[13];
				$Estado = trim(strtoupper($row_data[14]));
				
				$array_cedula[] = $NumeroDocumento;
				
				/*
				if(is_numeric($NumeroDocumento)){
					//Consulto Socio
					$sql_socio = "Select * 
								  From Socio 
								  Where IDClub = '".$IDClub."' and Accion = '".$Accion."'";
					$result_socio = $dbo->query($sql_socio);
								  
					if($dbo->rows($result_socio)>0):
						echo "<br>Editar";
					else:
						echo "<br>Crear " . $NumeroDocumento;
					endif
				}
				*/
				
				
		} // END While
		fclose($fp);	
		
					$sql_socio = "Select * 
								  From Socio 
								  Where IDClub = '".$IDClub."'";
					$result_socio = $dbo->query($sql_socio);
					while($row_socio = $dbo->fetchArray($result_socio)):
						if(!in_array($row_socio["NumeroDocumento"],$array_cedula)):
							// SI el token es vacio lo desactivo							
							echo "<br>" . $row_socio["NumeroDocumento"] . " TOKEN: " . $row_socio["Token"];
							echo "<br>" . $sql_inactivar="Update Socio Set IDEstadoSocio = '2' Where IDSocio = '".$row_socio["IDSocio"]."' and Token = ''";
							$dbo->query($sql_inactivar);
							$array_ced_no_existe[] = $row_socio["NumeroDocumento"];
							$contador_no_existe++;
						endif;	
					endwhile;
					echo "<br>NO EXISTE: " . $contador_no_existe;
								  
					
		

}
else{
	echo "Archivo no encontrado";
		
}

echo "Fin";

?>