<?php
  ini_set('max_execution_time', 0);
  include( "config.inc.php" ); 
  
  $FIELD_TEMINATED = "TAB";
  $file = "PredioSocioW.txt";
	if($fp = fopen($file,"r")):		
		ini_set('auto_detect_line_endings', true); 
		while(!feof($fp)):			
		
		$row = fgets($fp,4096);			
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);	
				
			
			$Accion=trim($row_data[0]);
			$Predio=trim($row_data[1]);
			
				
			if(!empty($Accion) && !empty($Predio)):
				//Consulto a quien corresponde la accion
				$sql_socio="Select * From Socio Where (Accion = '".$Accion."' or AccionPadre = '".$Accion."' ) and IDClub = 9 ";
				$result_socio=$dbo->query($sql_socio);
				while($row_socio = $dbo->fetchArray($result_socio)):
					//si tiene mas predios en la misma celda los guardo por separado
					$array_predio = explode("/",$Predio);
					if(count($array_predio)>0):
						foreach($array_predio as $nombre_predio):
							$sql_inserta= "Insert Into Predio (IDSocio,Predio) Values ('".$row_socio["IDSocio"]."','".$nombre_predio."')";							
							$dbo->query($sql_inserta);								
						endforeach;
					endif;
					
				endwhile;
			
			endif;
			
			
		endwhile;		
	endif;		
  echo "Terminado";
?>