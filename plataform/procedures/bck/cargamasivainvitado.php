 <?php 
 	
	 $titulo = "Invitado";
 

	SIMReg::setFromStructure( array(
						"title" => "Invitados Especiales",
						"table" => "SocioInvitadoEspecial",
						"key" => "IDSocioInvitadoEspecial",
						"mod" => "SocioInvitadoEspecial"
	) );
	
	
	$script = "invitadosespeciales";
	
	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );
	
	
	
	function copiar_archivo(&$frm,$file) {	
		$filedir=SOCIOPLANO_DIR;
		$nuevo_nombre = rand(0,1000000). "_".date("Y-m-d")."_".$file['file']['name'];
		if (copy($file['file']['tmp_name'], "$filedir/".$nuevo_nombre) ) {
			echo "File : ".$file['file']['name']."... ";
			echo "Size :".$file['file']['size']." Bytes ... ";
			echo "Status : Transfer Ok ...<br>";
			return $nuevo_nombre;
	
		}
		else{
			echo "error";
		}	
}

function get_data($nombrearchivo,$file,$IGNORE_FIRTS_ROW,$FIELD_TEMINATED='',$field='',$IDClub){
	
	$dbo =& SIMDB::get();
	
	$numregok = 0;

	if(!empty($field))
				$strfields = "(".implode(",",$field).")";					
	
	if($fp = fopen($file,"r")){
		$cont = 0;		
		ini_set('auto_detect_line_endings', true); 		
		if($IGNORE_FIRTS_ROW)
			$row = fgets($fp,4096);
			echo "<br><br>";		
		while(!feof($fp)){
			
				$row = fgets($fp,4096);			
				if(!empty($FIELD_TEMINATED))
					if($FIELD_TEMINATED == "TAB")
						$row_data = explode("\t",$row);
					else
						$row_data = explode($FIELD_TEMINATED,$row);	
				
				//Relacion de Campos
				$CedulaAutoriza = $row_data[0];
				$FechaIngreso = $row_data[1];				
				$FechaSalida = $row_data[2];				
				$DocumentoInvitado = utf8_encode($row_data[3]);
				$Nombre = utf8_encode($row_data[4]);
				$Apellido = $row_data[5];
				$Email = $row_data[6];
				$Telefono = $row_data[7];
				$TipoSangre = $row_data[8];
				$Placa = $row_data[9];
				
				if(is_numeric($CedulaAutoriza) && is_numeric($DocumentoInvitado) && !empty($CedulaAutoriza) && !empty($FechaIngreso) && !empty($FechaSalida) && !empty($Nombre) && !empty($Apellido) ){
					
						if(strlen($FechaIngreso)==10 && strlen($FechaSalida)==10){
							
							//Consulto Socio
							$sql_socio = "Select * 
										  From Socio 
										  Where IDClub = '".$IDClub."' and NumeroDocumento = '".$CedulaAutoriza."'";
							$result_socio = $dbo->query($sql_socio);
								  
							if($dbo->rows($result_socio)>0):
								$row_datos_socio = $dbo->fetchArray($result_socio);
								//Crear invitacion
								//Servicio de invitados
								$array_datos = array();
								
								$array_datos_invitado["IDTipoDocumento"]="2";
								$array_datos_invitado["NumeroDocumento"]=$DocumentoInvitado;
								$array_datos_invitado["Nombre"]=$Nombre;
								$array_datos_invitado["Apellido"]=$Apellido;
								$array_datos_invitado["Email"]=$Email;
								$array_datos_invitado["TipoInvitado"]="5";
								$array_datos_invitado["Placa"]=$Placa;
								$array_datos_invitado["CabezaInvitacion"]="N";
								array_push($array_datos, $array_datos_invitado);									
								$DatosInvitado = json_encode($array_datos);
								
								$respuesta = SIMWebService::set_autorizacion_invitado($IDClub,$row_datos_socio["IDSocio"],$FechaIngreso,$FechaSalida,$DatosInvitado);
								print_r($respuesta["message"]);
								echo "<br><br>";						
								$numregok++;													
							else:
								echo "<br>" . "La cedula de quien invita no existe en la base: " . $CedulaAutoriza;	
								
							endif;
						}
						else{
							echo "<br>" . "Las fechas tienen un formato invalido: " . $DocumentoInvitado . "Estado: " . $Estado;	
						}
							
				}
				else{
					echo "<br>" . "El numero de documento debe ser numerico: " . $DocumentoInvitado ;	
				}



				
				$cont++;			
		} // END While
		fclose($fp);	
			return array("Numregs"=>$cont,"RegsOK"=>$numregok);
	}
	else
		echo "error open $file";
	
	return false;
}
	
	
	

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );
	
	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );
	
	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	

	
	switch ( SIMNet::req( "action" ) ) {

		
		case "cargarplano" :	
					$time_start = SIMUtil::getmicrotime();			
					$nombre_archivo = copiar_archivo($_POST,$_FILES);
					if($nombre_archivo=="error"):
						echo "Error Transfiriendo Archivo";		
						exit;
					endif;
							
					$result = get_data($nombre_archivo,SOCIOPLANO_DIR.$nombre_archivo,$_POST['IGNORELINE'],$_POST['FIELD_TEMINATED'],$_POST['field'],$_POST['IDClub']);
					if($result["Numregs"] > 0){
						echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
																
				} // if($result["Numregs"] > 0){

				$time_end = getmicrotime();	
				$time = $time_end - $time_start;
				$time = number_format($time,3);
				display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
				exit;
		break;	
		
		
		
	
	} // End switch



?>