<?php

$host = '190.66.22.93';
$user = 'miclubapp';
$pass = 'Miclub2017';


$AccionSocio="0001";

/// set up basic connection
$conn_id = ftp_connect($host,"64007");

//Comprobar que la conexión ha tenido éxito
if (!$conn_id) {
echo '<br>Error al tratar de conectar con ' . $host . "<br><br>";
exit();
}


// login with username and password
$login_result = ftp_login($conn_id, $user, $pass);

// enabling passive mode
ftp_pasv( $conn_id, true );

// Obtener los archivos contenidos en el directorio actual
      $files = ftp_nlist($conn_id, "");

      foreach ($files as $file):
	  	if (ftp_size($conn_id, $file) == -1): //Es directorio
			$array_directorio[]=$file;
			//Ingreso a ese directorio y consulto los archivos
			 $directorio = ftp_nlist( $conn_id, $file );
			 foreach ($directorio as $archivo):
			 	$ruta_original=$archivo;
			 	$archivo = str_replace($file."/","",$archivo);
				//Capturo Accion Socio
				$array_nombre_archivo=explode("_",$archivo);				
				$accion_socio_pdf= str_replace(".pdf","",$array_nombre_archivo[1]);				
				//Comparo si el archivo pertenece al socio a consultar
				if($AccionSocio==$accion_socio_pdf):
				 	$array_pdf[$file]=$archivo;
					
					echo  "<br>Copiar: " . $ruta_original ."," ."extracto.pdf" . "<br>";
					
					if (ftp_get($conn_id, "EXT_0001.pdf", "201701/EXT_0001.pdf", FTP_BINARY)) {
						echo "Successfully written to $local_file\n";
					} else {
						echo "There was a problem A   \n";
					}
					
					// intenta descargar $server_file y guardarlo en $local_file
					if (ftp_get($conn_id, $ruta_original, "extracto.pdf", FTP_BINARY)) {
						echo "Se ha guardado satisfactoriamente en $local_file <br>";
					} else {
						echo "Ha habido un problemaaa<br>";
					}
					
					exit;
					
					
				endif;	
			 endforeach;
		endif;
      endforeach;
	  
	  echo "<br>Directorios";
	  print_r($array_directorio);
	  
	   echo "<br>ARchivos";
	  print_r($array_pdf);
	  
	  
	  foreach($array_pdf as $fecha => $archivo_extracto):
	  	echo "Extracto del mes de " . substr($fecha,4,2) . " de " .  substr($fecha,0,4) . " ";
		echo $archivo_extracto;
		 echo '<a href="http://'.$host."/".$fecha."/".$archivo_extracto.'">Descargar</a>';  
	  endforeach;
	  
	  
	  exit;
	 
	  
	  
	  
		   echo "<br>".$file;
         echo '<br><a href="'.$file.'">Descargar</a>';  
		 echo "Fin";      
	 
 
	  
	  
	  
	

// cerrar la conexión ftp
ftp_close($conn_id);



echo "<br><br>Fin";
?>